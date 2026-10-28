#!/usr/bin/env bash
#
# setup.sh - first build, install and baseline snapshot of the Agora demo.
#
# Run as root (or a docker-group member) from the project directory:
#
#   ./setup.sh               build, start everything (tunnel included), install,
#                            take the baseline snapshot, check the site
#   ./setup.sh --no-tunnel   the same without cloudflared: for a rehearsal, or
#                            before the tunnel token exists
#   ./setup.sh --reinstall   wipe the site's database and files, install again,
#                            and replace the baseline
#   ./setup.sh --rebuild     build the images again even if they exist
#   ./setup.sh --check       change nothing: only ask the running demo for its
#                            public pages and blocked paths, as cloudflared would
#
# Safe to run twice: an installed site is left as it is unless --reinstall is
# given, and existing images are reused unless --rebuild is given.
#
# Exit status: 0 when the site is installed, snapshotted and passes the
# self-check; 1 otherwise.

set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
# shellcheck source=lib.sh
. ./lib.sh

NO_TUNNEL=0
REINSTALL=0
REBUILD=0
CHECK=0
for arg in "$@"; do
  case "$arg" in
    --no-tunnel) NO_TUNNEL=1 ;;
    --reinstall) REINSTALL=1 ;;
    --rebuild) REBUILD=1 ;;
    --check) CHECK=1 ;;
    -h|--help) sed -n '2,22p' "$0"; exit 0 ;;
    *) die "unknown option: $arg (see --help)" ;;
  esac
done

require_tools
if [ "$CHECK" = 1 ]; then
  [ -f .env ] || die ".env is missing; run ./setup.sh first"
  if self_check live; then log "self-check passed"; exit 0; fi
  die "self-check failed; see the lines marked FAIL above"
fi
take_lock
t0=$(date +%s)

# ---------------------------------------------------------------------------
# 1. .env: created from .env.example, mode 600, secrets generated here.
# ---------------------------------------------------------------------------
if [ ! -f .env ]; then
  [ -f .env.example ] || die ".env.example is missing"
  install -m 600 .env.example .env
  log "created .env from .env.example (mode 600)"
fi
chmod 600 .env
for key in DB_PASSWORD DB_ROOT_PASSWORD DRUPAL_HASH_SALT; do
  if [ -z "$(env_get "$key")" ]; then
    env_set "$key" "$(random_hex)"
    log "generated $key in .env"
  fi
done
if [ "$NO_TUNNEL" = 0 ] && [ -z "$(env_get TUNNEL_TOKEN)" ]; then
  die "TUNNEL_TOKEN is empty in $AGORA_DIR/.env. Paste the token of the demo's own tunnel (PLAN.md, section Cloudflare) and run ./setup.sh again, or run ./setup.sh --no-tunnel."
fi
SITE_NAME=$(env_get SITE_NAME)
SITE_NAME=${SITE_NAME:-Fuentelclaro Town Council}
log "project: $(project_name)   host name: $(demo_host)   site name: $SITE_NAME"

# ---------------------------------------------------------------------------
# 2. The edge subnet must not collide with a network already on the host.
# ---------------------------------------------------------------------------
ip2int() { local IFS=.; read -r a b c d <<< "$1"; echo $(( (a << 24) | (b << 16) | (c << 8) | d )); }
cidr_range() {  # prints "first last" for a.b.c.d/n
  local ip=${1%/*} bits=${1#*/} n mask
  n=$(ip2int "$ip"); mask=$(( bits == 0 ? 0 : (0xFFFFFFFF << (32 - bits)) & 0xFFFFFFFF ))
  echo $(( n & mask )) $(( (n & mask) | (~mask & 0xFFFFFFFF) ))
}
EDGE_SUBNET=$(env_get EDGE_SUBNET); EDGE_SUBNET=${EDGE_SUBNET:-10.213.47.0/28}
[[ "$EDGE_SUBNET" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/[0-9]+$ ]] || die "EDGE_SUBNET '$EDGE_SUBNET' is not an IPv4 CIDR"
read -r e_lo e_hi <<< "$(cidr_range "$EDGE_SUBNET")"
# This project's own edge network (on a second run) is not a collision, and
# neither is the host route Docker adds for it.
own_subnet=$(docker network inspect -f '{{range .IPAM.Config}}{{.Subnet}}{{end}}' "$(project_name)_edge" 2>/dev/null || true)
while read -r name subnet; do
  [ -n "$subnet" ] || continue
  [[ "$subnet" =~ ^[0-9.]+/[0-9]+$ ]] || continue
  [ -n "$own_subnet" ] && [ "$subnet" = "$own_subnet" ] && continue
  read -r lo hi <<< "$(cidr_range "$subnet")"
  if [ "$lo" -le "$e_hi" ] && [ "$e_lo" -le "$hi" ]; then
    die "EDGE_SUBNET $EDGE_SUBNET overlaps $subnet ($name). Choose another range in .env."
  fi
done < <(
  docker network ls -q | xargs -r docker network inspect \
    -f '{{.Name}}{{range .IPAM.Config}} {{.Subnet}}{{end}}' \
    | awk '{ for (i = 2; i <= NF; i++) print $1, $i }'
  ip -4 route show 2>/dev/null | awk '$1 ~ /\// { print "route", $1 }'
)
log "edge subnet $EDGE_SUBNET overlaps no Docker network or host route"

# ---------------------------------------------------------------------------
# 3. Images: php and web come from the same Composer stage.
# ---------------------------------------------------------------------------
repo=$(image_repo)
if [ "$REBUILD" = 1 ] || ! docker image inspect "$repo-php:live" "$repo-web:live" >/dev/null 2>&1; then
  export CACHE_BUST
  CACHE_BUST=$(date -u +%Y%m%d%H%M%S)
  tb=$(date +%s)
  log "building $repo-php:live (composer create-project drupal/cms, require drupal/agora_transparency, audit)"
  IMAGE_TAG=live docker compose build --pull php
  log "building $repo-web:live from the same Composer stage"
  IMAGE_TAG=live docker compose build web
  log "images built in $(( $(date +%s) - tb )) s"
  # Only this project's own superseded builds: Compose labels what it builds.
  docker image prune -f --filter "label=com.docker.compose.project=$(project_name)" >/dev/null
else
  log "images $repo-php:live and $repo-web:live exist; reusing them (--rebuild to build again)"
fi
php_id=$(docker run --rm --entrypoint cat "$repo-php:live" /opt/drupal/BUILD-ID)
web_id=$(docker run --rm --entrypoint cat "$repo-web:live" /opt/drupal/BUILD-ID)
[ "$php_id" = "$web_id" ] || die "the php and web images come from different Composer builds ($php_id vs $web_id); run ./setup.sh --rebuild"
log "both images carry composer.lock ${php_id:0:12}:"
docker run --rm --entrypoint cat "$repo-php:live" /opt/drupal/BUILD-INFO.txt | sed 's/^/    /'

# ---------------------------------------------------------------------------
# 4. Start the services. No port is published; nothing is reachable yet
#    except through the tunnel.
# ---------------------------------------------------------------------------
services=(db php web)
[ "$NO_TUNNEL" = 0 ] && services+=(cloudflared)
log "starting: ${services[*]}"
IMAGE_TAG=live docker compose up -d --wait --wait-timeout 300 "${services[@]}"

# ---------------------------------------------------------------------------
# 5. Install (the README's command), unless the site is already installed.
# ---------------------------------------------------------------------------
installed=0
if [ "$(drush status --field=bootstrap 2>/dev/null | tr -d '\r')" = Successful ]; then
  installed=1
fi
if [ "$installed" = 1 ] && [ "$REINSTALL" = 1 ]; then
  log "--reinstall: wiping the database and the files"
  docker compose run --rm -T restore --wipe
  installed=0
fi
if [ "$installed" = 0 ]; then
  ti=$(date +%s)
  log "installing: drush site:install --yes recipes/agora_transparency --site-name=\"$SITE_NAME\" (DRUPAL_CMS_TELEMETRY=0)"
  # The admin password is generated inside the container and never shown or
  # stored: nobody can sign in through the web anyway, and drush can always
  # set a new one.
  docker compose exec -T -e SITE_NAME="$SITE_NAME" php sh -c '
    exec drush site:install --yes /opt/drupal/recipes/agora_transparency \
      --site-name="$SITE_NAME" --account-name=admin \
      --account-pass="$(od -vAn -N24 -tx1 /dev/urandom | tr -d " \n")"'
  log "installed in $(( $(date +%s) - ti )) s"
else
  log "the site is already installed; leaving it as it is (--reinstall to start over)"
fi

# ---------------------------------------------------------------------------
# 6. Lock the site down, and prove the usage and telemetry opt-outs hold.
# ---------------------------------------------------------------------------
drush config:set --yes user.settings register admin_only >/dev/null
[ "$(drush config:get user.settings register --format=string | tr -d '\r\n')" = admin_only ] \
  || die "visitor registration could not be restricted to administrators"
log "visitor registration: administrators only"

report=$(drush php:eval '
  $out = [];
  $out[] = "fetch_url=" . \Drupal::config("update.settings")->get("fetch.url");
  $out[] = "advisories=" . var_export(\Drupal::config("system.advisories")->get("enabled"), TRUE);
  $out[] = "feed_cron_intervals=" . \Drupal::config("announcements_feed.settings")->get("cron_interval") . "," . \Drupal::config("drupical.settings")->get("cron_interval");
  $out[] = "mail_sender=" . (\Drupal::config("mailsystem.settings")->get("defaults.sender") ?? \Drupal::config("system.mail")->get("interface.default"));
  $t = \Drupal::state()->get("drupal_cms_telemetry");
  $out[] = "telemetry=" . ($t === NULL ? "unset" : var_export((bool) $t, TRUE));
  $out[] = "accounts=" . \Drupal::entityQuery("user")->accessCheck(FALSE)->condition("uid", 0, ">")->count()->execute();
  $logins = 0;
  foreach (\Drupal::entityTypeManager()->getStorage("block")->loadMultiple() as $b) {
    if ($b->getPluginId() === "user_login_block" && $b->status()) { $b->disable()->save(); $logins++; }
  }
  $out[] = "login_blocks_disabled=" . $logins;
  $out[] = "aliases_to_blocked=" . \Drupal::database()->query("SELECT COUNT(*) FROM {path_alias} WHERE path LIKE :a OR path LIKE :u OR path LIKE :n OR path LIKE :e",
    [":a" => "/admin%", ":u" => "/user%", ":n" => "/node/add%", ":e" => "/node/%/edit%"])->fetchField();
  echo implode("\n", $out), "\n";' | tr -d '\r')
printf '%s\n' "$report" | sed 's/^/    /'
grep -qx 'fetch_url=http://127.0.0.1:9/agora-demo-never-reports-usage' <<< "$report" \
  || die "update status is NOT pinned to the closed loopback port; the site could report usage"
grep -qx 'advisories=false' <<< "$report" || die "the security advisories feed is not switched off"
grep -qx 'telemetry=false' <<< "$report" || die "Drupal CMS telemetry is not off (DRUPAL_CMS_TELEMETRY=0 was not honoured)"
grep -qx 'accounts=1' <<< "$report" || die "the site has accounts other than the administrator"
grep -qx 'aliases_to_blocked=0' <<< "$report" || die "a path alias leads to an administrative path"
log "usage reporting and telemetry: off; one account (the administrator); no alias leads to a blocked path"

drush core:cron --quiet
drush cache:rebuild --quiet

# ---------------------------------------------------------------------------
# 7. Baseline snapshot: what the nightly restore brings back.
# ---------------------------------------------------------------------------
if [ "$installed" = 0 ] || ! docker compose run --rm -T --entrypoint test snapshot -f /var/lib/agora-snapshot/baseline/manifest; then
  docker compose run --rm -T snapshot
else
  log "a baseline snapshot exists; keeping it"
fi

# ---------------------------------------------------------------------------
# 8. Self-check, from the edge network, as cloudflared would ask.
# ---------------------------------------------------------------------------
if self_check live; then
  log "self-check passed"
else
  die "self-check failed; see the lines marked FAIL above"
fi

log "done in $(( $(date +%s) - t0 )) s"
if [ "$NO_TUNNEL" = 1 ]; then
  log "cloudflared was NOT started (--no-tunnel). Start it with: docker compose up -d cloudflared"
fi
log "next: install the nightly restore timer (PLAN.md, step 6)"
