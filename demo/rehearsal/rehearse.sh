#!/usr/bin/env bash
#
# rehearse.sh - a full rehearsal of the demo WITHOUT Cloudflare.
#
# Run it in a COPY of the project directory, never in /opt/agora-demo itself:
#
#   cp -a /opt/agora-demo /root/agora-demo-rehearsal   (before .env exists)
#   /root/agora-demo-rehearsal/rehearsal/rehearse.sh             set up and check
#   /root/agora-demo-rehearsal/rehearsal/rehearse.sh --teardown  remove it all
#
# It uses its own Compose project (agora-demo-rehearsal), its own image names
# (agora-demo-rehearsal-php/-web) and its own subnets, so it can run beside the
# real demo. cloudflared is replaced by throwaway curl containers on the edge
# network. compose.rehearsal.yaml adds a trap: inside the PHP containers the
# drupal.org and Amplitude host names resolve to a recorder that logs every
# connection attempt, so "no request was made" is a count, not an assumption.
#
# Exit status: 0 when every check passed, 1 otherwise.

set -uo pipefail
cd "$(dirname "$(readlink -f "$0")")/.." || exit 1
# shellcheck source=../lib.sh
. ./lib.sh

if [ "${1:-}" = --teardown ]; then
  [ "$(project_name)" = agora-demo-rehearsal ] || die "this directory's .env is not a rehearsal; refusing to tear it down"
  exec ./uninstall.sh --yes
fi

if [ -f .env ]; then
  [ "$(project_name)" = agora-demo-rehearsal ] \
    || die "this directory holds a real .env (project $(project_name)); rehearse in a copy"
else
  install -m 600 /dev/null .env
  cat > .env <<'EOF'
COMPOSE_PROJECT_NAME=agora-demo-rehearsal
COMPOSE_FILE=compose.yaml:rehearsal/compose.rehearsal.yaml
IMAGE_REPO=agora-demo-rehearsal
TUNNEL_TOKEN=
DB_PASSWORD=
DB_ROOT_PASSWORD=
DRUPAL_HASH_SALT=
DEMO_HOSTNAME=agora-demo.andresmoreno.dev
SITE_NAME="Fuentelclaro Town Council"
EDGE_SUBNET=10.213.57.0/28
TRAP_SUBNET=10.213.58.0/28
TRAP_IP=10.213.58.2
EOF
fi

PASS=0
FAIL=0
pass() { PASS=$((PASS + 1)); printf '  PASS  %s\n' "$*"; }
fail() { FAIL=$((FAIL + 1)); printf '  FAIL  %s\n' "$*"; }
section() { printf '\n=== %s ===\n' "$*"; }
trap_count() { docker compose logs --no-log-prefix trap 2>/dev/null | grep -c '^TRAP ' || true; }

section "1. setup.sh --no-tunnel"
t=$(date +%s)
if ./setup.sh --no-tunnel; then pass "setup.sh exited 0 in $(( $(date +%s) - t )) s"; else fail "setup.sh failed"; fi

section "2. Versions and images"
docker compose exec -T php cat /opt/drupal/BUILD-INFO.txt | sed 's/^/  /'
php_v=$(docker compose exec -T php php -r 'echo PHP_VERSION;')
if [ "$(printf '%s\n8.4.27\n' "$php_v" | sort -V | head -n 1)" = 8.4.27 ]; then
  pass "php -v in the php container: $php_v (>= 8.4.27)"
else
  fail "php -v in the php container: $php_v (< 8.4.27)"
fi
docker image ls --format '{{.Repository}}:{{.Tag}} {{.Size}}' | grep -E "^$(image_repo)-(php|web):live " | sed 's/^/  image size: /'
docker compose exec -T php cat /opt/drupal/BUILD-AUDIT.txt | sed 's/^/  build-time composer audit: /'

section "3. PHP-FPM pool settings, asked of FPM itself"
fpm=$(docker compose exec -T php php < rehearsal/fpm-probe.php)
printf '%s\n' "$fpm" | sed 's/^/  /'
grep -q '^sapi=fpm-fcgi$' <<< "$fpm" && pass "answered by the FPM pool (sapi fpm-fcgi)" || fail "not answered by FPM"
grep -q '^still_callable=none$' <<< "$fpm" && pass "exec, shell_exec, system, passthru, proc_open, popen, pcntl_exec: none callable in FPM" || fail "a disabled function is still callable in FPM"
grep -q '^expose_php=off$' <<< "$fpm" && pass "expose_php off" || fail "expose_php on"
grep -q '^session.upload_progress.enabled=off$' <<< "$fpm" && pass "session.upload_progress.enabled off" || fail "upload progress on"
grep -q '^opcache_enabled=yes$' <<< "$fpm" && pass "OPcache enabled in FPM" || fail "OPcache not enabled"
cli=$(docker compose exec -T php php -r 'echo function_exists("proc_open") ? "yes" : "no";')
[ "$cli" = yes ] && pass "the command-line PHP (drush) keeps proc_open" || fail "the CLI lost proc_open"

section "4. HTTP from the edge network (as cloudflared)"
if self_check live; then pass "public pages 200 with Fuentelclaro; every blocked path 404, /fpm-ping 403"; else fail "self-check"; fi

assets=$(docker run --rm --network "$(project_name)_edge" --read-only --tmpfs /tmp:size=16m --cap-drop ALL \
  --security-opt no-new-privileges:true --user 33:33 --entrypoint /bin/bash "$(image_repo)-php:live" -c '
  h=$1
  curl -sS -H "Host: $h" -H "X-Forwarded-Proto: https" http://web:8080/ -o /tmp/front
  grep -oE "(src|href)=\"/[^\"]+\.(css|js|png|jpe?g|svg|webp|ico|woff2?)(\?[^\"]*)?\"" /tmp/front \
    | sed -E "s/^(src|href)=\"//; s/\"$//; s/&amp;/\&/g" | sort -u > /tmp/assets
  while read -r a; do
    printf "%s %s\n" "$(curl -sS -o /dev/null -w "%{http_code}" -H "Host: $h" -H "X-Forwarded-Proto: https" "http://web:8080$a")" "$a"
  done < /tmp/assets' assets "$(demo_host)")
total=$(printf '%s\n' "$assets" | grep -c . || true)
ok=$(printf '%s\n' "$assets" | grep -c '^200 ' || true)
printf '%s\n' "$assets" | grep -v '^200 ' | sed 's/^/  not 200: /'
[ "$total" -gt 0 ] && [ "$ok" = "$total" ] && pass "front page assets: $ok of $total answer 200" || fail "front page assets: $ok of $total answer 200"

code=$(docker run --rm --network "$(project_name)_edge" --cap-drop ALL --user 33:33 --entrypoint curl "$(image_repo)-php:live" \
  -s -o /dev/null -w '%{http_code}' -H 'Host: some-other-site.example' http://web:8080/)
[ "$code" = 400 ] && pass "a request for another host name: $code" || fail "a request for another host name: $code (expected 400)"

# Drupal believes X-Forwarded-For only from the edge network. A page Drupal
# itself refuses (403) is logged with the visitor address Drupal settled on.
xff_path=/contextual/render
probe live "$xff_path" > /dev/null
docker compose exec -T web wget -q -O /dev/null --header "Host: $(demo_host)" \
  --header "X-Forwarded-For: 198.51.100.7" "http://127.0.0.1:8080$xff_path" 2>/dev/null
seen=$(drush php:eval '
  $q = "SELECT hostname, location FROM {watchdog} WHERE type = :t ORDER BY wid DESC LIMIT 2";
  foreach (\Drupal::database()->query($q, [":t" => "access denied"]) as $r) {
    echo $r->hostname, " ", $r->location, PHP_EOL;
  }' | tr -d '\r')
printf '%s\n' "$seen" | sed 's/^/  logged: /'
# nginx empties X-Forwarded-For, so Drupal never keeps the visitor's address (192.0.2.10 in the
# probe): it logs the edge client's internal address, and still takes HTTPS from X-Forwarded-Proto.
{ ! grep -q "^192.0.2.10 " <<< "$seen" && grep -qE "^[0-9.]+ https://$(demo_host)$xff_path" <<< "$seen"; } \
  && pass "from the edge network, Drupal takes HTTPS from cloudflared's headers and never the visitor address" \
  || fail "Drupal kept the visitor address, or ignored X-Forwarded-Proto from the edge network"
grep -q "^127.0.0.1 http://$(demo_host)$xff_path" <<< "$seen" \
  && pass "from anywhere else, a forged X-Forwarded-For (198.51.100.7) is ignored" \
  || fail "a forged X-Forwarded-For from outside the edge network was believed"

section "5. Containers"
project=$(project_name)
running=0
for c in $(docker compose ps -q); do
  running=$((running + 1))
  line=$(docker inspect -f '{{.Name}} ports={{json .HostConfig.PortBindings}} publishAll={{.HostConfig.PublishAllPorts}} cpus={{.HostConfig.NanoCpus}} mem={{.HostConfig.Memory}} pids={{.HostConfig.PidsLimit}} ro={{.HostConfig.ReadonlyRootfs}} capdrop={{json .HostConfig.CapDrop}} secopt={{json .HostConfig.SecurityOpt}} user={{.Config.User}} restart={{.HostConfig.RestartPolicy.Name}} log={{.HostConfig.LogConfig.Type}}:{{index .HostConfig.LogConfig.Config "max-size"}}x{{index .HostConfig.LogConfig.Config "max-file"}} mounts={{range .Mounts}}{{.Source}}:{{.Destination}},{{end}} health={{with index .State "Health"}}{{.Status}}{{else}}none{{end}}' "$c")
  echo "  $line"
  name=$(cut -d' ' -f1 <<< "$line")
  { grep -qE ' ports=(\{\}|null) ' <<< "$line" && grep -q ' publishAll=false ' <<< "$line"; } || fail "$name publishes a port"
  [ -z "$(docker port "$c")" ] || fail "$name: docker port is not empty"
  grep -qE ' cpus=0 | mem=0 | pids=(0|<nil>|-1) ' <<< "$line" && fail "$name lacks a CPU, memory or pids limit"
  grep -q ' ro=true ' <<< "$line" || fail "$name root filesystem is writable"
  grep -q '"ALL"' <<< "$line" || fail "$name keeps capabilities"
  grep -q 'no-new-privileges:true' <<< "$line" || fail "$name lacks no-new-privileges"
  grep -qE ' user=(0|root)? ' <<< "$line" && fail "$name runs as root"
  grep -q 'docker.sock' <<< "$line" && fail "$name mounts the Docker socket"
done
pass "$running containers inspected: no published port, limits set, read-only, ALL capabilities dropped, no-new-privileges, non-root, no Docker socket (any exception is a FAIL above)"
docker network ls --filter "label=com.docker.compose.project=$project" -q \
  | xargs -r docker network inspect -f '  network {{.Name}} internal={{.Internal}} subnet={{range .IPAM.Config}}{{.Subnet}}{{end}} containers={{len .Containers}}'
int=$(docker network inspect -f '{{.Internal}}' "${project}_db" "${project}_app" "${project}_edge" | sort -u)
[ "$int" = true ] && pass "networks db, app and edge are internal (no route out)" || fail "db/app/edge are not all internal"
edge_members=$(docker network inspect -f '{{range .Containers}}{{.Name}} {{end}}' "${project}_edge")
[ "$edge_members" = "${project}-web-1 " ] && pass "edge network holds only web (cloudflared not started in a rehearsal)" || fail "edge network holds: $edge_members"

section "6. Outbound calls: usage reporting and telemetry"
before=$(trap_count)
[ "$before" = 0 ] && pass "trap after build, install, first cron and snapshot: $before connection attempts" || fail "trap after install: $before attempts: $(docker compose logs --no-log-prefix trap | grep '^TRAP ')"
out=$(docker compose exec -T php php -r '$s = @fsockopen("1.1.1.1", 443, $e, $m, 3); echo $s ? "CONNECTED" : "no route: $m", "|", gethostbyname("example.com");')
echo "  php container -> 1.1.1.1:443 and DNS for example.com: $out"
grep -q '^no route' <<< "$out" && grep -q '|example.com$' <<< "$out" && pass "the php container has no route out and no outside DNS" || fail "the php container reached outside: $out"
pin=$(drush php:eval '
  $f = \Drupal::service("update.fetcher");
  $p = ["name" => "drupal", "info" => [], "project_type" => "core", "includes" => []];
  echo "url=", $f->buildFetchUrl($p, "rehearsal-site-key"), "\n";
  echo "bytes=", strlen($f->fetchProjectData($p, "rehearsal-site-key")), "\n";
  $t = \Drupal::state()->get("drupal_cms_telemetry");
  echo "telemetry=", $t === NULL ? "unset" : var_export((bool) $t, TRUE), "\n";
  echo "queued=", \Drupal::queue("drupal_cms_telemetry")->numberOfItems(), "\n";' | tr -d '\r')
printf '%s\n' "$pin" | sed 's/^/  /'
grep -q '^url=http://127.0.0.1:9/agora-demo-never-reports-usage/drupal/current?site_key=' <<< "$pin" \
  && pass "update status would be fetched from the closed loopback port" || fail "update status URL is not the pinned one"
grep -q '^bytes=0$' <<< "$pin" && pass "fetching update status returned nothing" || fail "update status fetch returned data"
grep -q '^telemetry=false$' <<< "$pin" && pass "Drupal CMS telemetry state: false" || fail "telemetry is not off"
drush core:cron --quiet
after_cron=$(trap_count)
[ "$after_cron" = 0 ] && pass "trap after an explicit update fetch and another cron: $after_cron attempts" || fail "trap after cron: $after_cron"
# Positive control: Drupal's own HTTP client, sent on purpose to the trapped
# names, must show up in the trap. Otherwise "0" above would prove nothing.
drush php:eval '
  foreach (["https://updates.drupal.org/release-history/drupal/current", "https://api.eu.amplitude.com/2/httpapi"] as $u) {
    try { \Drupal::httpClient()->get($u, ["timeout" => 5]); echo "  control reached $u\n"; }
    catch (\Throwable $e) { echo "  control ", $u, ": ", get_class($e), "\n"; }
  }' | tr -d '\r'
sleep 1
control=$(trap_count)
docker compose logs --no-log-prefix trap | grep '^TRAP ' | sed 's/^/  /'
[ "$control" = 2 ] && pass "positive control: the trap recorded exactly the 2 deliberate requests (updates.drupal.org, api.eu.amplitude.com)" || fail "positive control: trap shows $control attempts, expected 2"

section "7. Nightly restore brings back a deleted node"
read -r nid path < <(drush php:eval '
  $ids = \Drupal::entityQuery("node")->accessCheck(TRUE)->condition("type", "agora_base_dataset")
    ->condition("status", 1)->sort("nid")->range(0, 1)->execute();
  $n = \Drupal\node\Entity\Node::load(reset($ids));
  echo $n->id(), " ", $n->toUrl()->toString(), "\n";' | tr -d '\r')
echo "  dataset node $nid at $path"
before_code=$(probe live "$path" | cut -d' ' -f1)
drush entity:delete node "$nid" --quiet
gone_code=$(probe live "$path" | cut -d' ' -f1)
t=$(date +%s)
./restore.sh
rt=$(( $(date +%s) - t ))
back_code=$(probe live "$path" | cut -d' ' -f1)
echo "  $path: $before_code before, $gone_code after deleting node $nid, $back_code after restore.sh (${rt} s)"
[ "$before_code" = 200 ] && [ "$gone_code" = 404 ] && [ "$back_code" = 200 ] \
  && pass "restore.sh brought node $nid back in $rt s" || fail "restore: $before_code -> $gone_code -> $back_code"
[ "$(trap_count)" = 2 ] && pass "trap unchanged by the restore and its cron run (still 2, both the controls)" || fail "trap moved during restore: $(trap_count)"

section "8. composer audit of the running image, against today's advisories"
if ./update.sh --audit-only; then pass "composer audit: no advisories"; else fail "composer audit reported advisories"; fi

section "Result"
echo "  checks passed: $PASS   failed: $FAIL"
echo "  Remove everything with: rehearsal/rehearse.sh --teardown"
[ "$FAIL" -eq 0 ]
# cspell:ignore capdrop secopt
