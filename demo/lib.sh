# shellcheck shell=bash
# lib.sh - shared by setup.sh, restore.sh, update.sh and uninstall.sh.
# Sourced, never run. Every script changes to its own directory first, so
# "docker compose" always means THIS project's compose.yaml and .env.

AGORA_DIR=$(pwd)

log()  { printf '[agora-demo] %s\n' "$*"; }
warn() { printf '[agora-demo] WARNING: %s\n' "$*" >&2; }
die()  { printf '[agora-demo] ERROR: %s\n' "$*" >&2; exit 1; }

# The value of KEY in .env, without surrounding double quotes.
env_get() {
  [ -f "$AGORA_DIR/.env" ] || return 0
  sed -n "s/^$1=//p" "$AGORA_DIR/.env" | tail -n 1 | sed 's/^"\(.*\)"$/\1/'
}

# Sets KEY=VALUE in .env, replacing an existing line or appending one.
env_set() {
  if grep -q "^$1=" "$AGORA_DIR/.env"; then
    sed -i "s|^$1=.*|$1=$2|" "$AGORA_DIR/.env"
  else
    printf '%s=%s\n' "$1" "$2" >> "$AGORA_DIR/.env"
  fi
}

# 64 hexadecimal characters from the kernel's random source.
random_hex() { od -vAn -N32 -tx1 /dev/urandom | tr -d ' \n'; }

# Names this project gives to its Docker objects.
project_name() { local p; p=$(env_get COMPOSE_PROJECT_NAME); printf '%s' "${p:-agora-demo}"; }
image_repo()   { local r; r=$(env_get IMAGE_REPO); printf '%s' "${r:-agora-demo}"; }
demo_host()    { local h; h=$(env_get DEMO_HOSTNAME); printf '%s' "${h:-agora-demo.andresmoreno.dev}"; }

require_tools() {
  command -v docker >/dev/null || die "docker is not installed"
  docker compose version >/dev/null 2>&1 || die "the docker compose plugin (Compose v2) is not installed"
  command -v flock >/dev/null || die "flock is missing (package util-linux)"
  docker info >/dev/null 2>&1 || die "cannot talk to the Docker daemon; run as root or as a member of the docker group"
}

# Only one of setup/update/restore runs at a time.
take_lock() {
  exec 9> "$AGORA_DIR/.agora-demo.lock"
  flock -n 9 || die "another setup.sh, update.sh or restore.sh is running in $AGORA_DIR"
}

# drush inside the running php container.
drush() { docker compose exec -T php drush "$@"; }

# Asks the demo for each path the way cloudflared does: from a throwaway
# container on the "edge" network, with the public host name, over plain HTTP
# to nginx, saying the visitor came over HTTPS. Prints one line per path:
#   <http code> <bytes> <has "Fuentelclaro"> <path>
# Usage: probe <image tag> <path>...
probe() {
  local tag=$1; shift
  docker run --rm --network "$(project_name)_edge" \
    --read-only --tmpfs /tmp:rw,noexec,nosuid,size=16m \
    --cap-drop ALL --security-opt no-new-privileges:true --user 33:33 \
    --memory 128m --pids-limit 32 \
    --entrypoint /bin/bash "$(image_repo)-php:$tag" -c '
      host=$1; shift
      for path in "$@"; do
        code=$(curl -sS -o /tmp/body -w "%{http_code}" --max-time 60 \
          -H "Host: $host" -H "X-Forwarded-Proto: https" \
          -H "X-Forwarded-For: 192.0.2.10" -H "CF-Connecting-IP: 192.0.2.10" \
          "http://web:8080$path") || code=000
        bytes=$(wc -c < /tmp/body 2>/dev/null || echo 0)
        if grep -q Fuentelclaro /tmp/body 2>/dev/null; then named=yes; else named=no; fi
        printf "%s %s %s %s\n" "$code" "$bytes" "$named" "$path"
      done' probe "$(demo_host)" "$@"
}

# Paths that must never be reachable from outside, and what they must answer.
BLOCKED_PATHS=(
  /user/login /user/password /user/register /user/1/edit /user /user/reset/1/1/x/login
  /admin /admin/config /admin/content /node/add /node/add/agora_base_dataset /node/1/edit
  /core/install.php /update.php /core/authorize.php /core/rebuild.php
  /index.php /index.php/admin /index.php/user/login /ADMIN /User/Login /%61dmin //user/login
  /en/admin /batch /cron/x /canvas /media/add /media/1/edit /taxonomy/term/1/edit
)

# Checks the public pages and the blocked paths. Returns non-zero on any
# unexpected answer.
self_check() {
  local tag=$1 dataset failures=0 code bytes named path
  dataset=$(drush php:eval '
    $ids = \Drupal::entityQuery("node")->accessCheck(TRUE)
      ->condition("type", "agora_base_dataset")->condition("status", 1)
      ->sort("nid")->range(0, 1)->execute();
    if ($ids) { echo \Drupal\node\Entity\Node::load(reset($ids))->toUrl()->toString(); }' 2>/dev/null | tr -d '\r') || dataset=""
  [ -n "$dataset" ] || { warn "no published dataset found in the site"; failures=$((failures + 1)); }

  log "public pages (expect 200 and the word Fuentelclaro):"
  while read -r code bytes named path; do
    if [ "$code" = 200 ] && [ "$named" = yes ]; then
      printf '  ok    %s  %8s bytes  %s\n' "$code" "$bytes" "$path"
    else
      printf '  FAIL  %s  %8s bytes  Fuentelclaro=%s  %s\n' "$code" "$bytes" "$named" "$path"
      failures=$((failures + 1))
    fi
  done < <(probe "$tag" / /contracts /datasets ${dataset:+"$dataset"})

  log "blocked paths (expect 404; /fpm-ping expects 403):"
  while read -r code bytes named path; do
    local want=404
    [ "$path" = /fpm-ping ] && want=403
    if [ "$code" = "$want" ]; then
      printf '  ok    %s  %s\n' "$code" "$path"
    else
      printf '  FAIL  %s  %s (expected %s)\n' "$code" "$path" "$want"
      failures=$((failures + 1))
    fi
  done < <(probe "$tag" "${BLOCKED_PATHS[@]}" /fpm-ping)

  [ "$failures" -eq 0 ]
}
# cspell:ignore dmin
