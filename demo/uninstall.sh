#!/usr/bin/env bash
#
# uninstall.sh - removes everything this project created, and nothing else.
#
#   ./uninstall.sh          show what would be removed, then ask
#   ./uninstall.sh --yes    remove without asking
#
# Removed:
#   - the containers, networks and volumes labelled with this Compose project
#     (database, files, private files and snapshots included: the demo's data
#     is gone for good);
#   - the images this project built: <repo>-php:* and <repo>-web:*;
#   - the systemd timer and service agora-demo-restore, if installed;
#   - .env (the generated secrets and the tunnel token) and the lock file.
#
# NOT removed, because other projects on the host may use them:
#   - the pulled base images (mariadb, nginx, php, composer, cloudflared);
#   - Docker's shared build cache.
# Removed by hand afterwards (the script prints the steps): the tunnel in the
# Cloudflare dashboard and this directory.

set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
# shellcheck source=lib.sh
. ./lib.sh

YES=0
case "${1:-}" in
  "") ;;
  --yes) YES=1 ;;
  -h|--help) sed -n '2,22p' "$0"; exit 0 ;;
  *) die "unknown option: $1 (see --help)" ;;
esac

require_tools
project=$(project_name)
repo=$(image_repo)
label="com.docker.compose.project=$project"

containers=$(docker ps -a --filter "label=$label" --format '{{.Names}}' | sort)
networks=$(docker network ls --filter "label=$label" --format '{{.Name}}' | sort)
volumes=$(docker volume ls --filter "label=$label" --format '{{.Name}}' | sort)
images=$(docker image ls --format '{{.Repository}}:{{.Tag}}' | grep -E "^($repo-php|$repo-web):" | sort || true)
# Older builds this project made that no tag points to any more (Compose
# labels every image it builds with the project's name).
old_builds=$(docker image ls -aq --filter "label=$label" --filter dangling=true | sort -u)
units=()
for u in agora-demo-restore.timer agora-demo-restore.service; do
  [ -f "/etc/systemd/system/$u" ] && units+=("$u")
done

echo "Compose project '$project' - this will be removed:"
echo "  containers: ${containers//$'\n'/ }"
echo "  networks:   ${networks//$'\n'/ }"
echo "  volumes:    ${volumes//$'\n'/ }   <- the demo's database, files and snapshots"
echo "  images:     ${images//$'\n'/ } + $(printf '%s' "$old_builds" | grep -c . || true) untagged older build(s)"
echo "  systemd:    ${units[*]:-(none installed)}"
echo "  files:      .env (secrets), .agora-demo.lock"
if [ "$YES" = 0 ]; then
  read -r -p "Type the project name ($project) to confirm: " answer
  [ "$answer" = "$project" ] || die "not confirmed; nothing was removed"
fi

if [ "${#units[@]}" -gt 0 ]; then
  systemctl disable --now agora-demo-restore.timer 2>/dev/null || true
  for u in "${units[@]}"; do rm -f "/etc/systemd/system/$u"; done
  systemctl daemon-reload
  log "removed systemd units: ${units[*]}"
fi

if [ -f .env ]; then
  docker compose --profile jobs down --volumes --remove-orphans --timeout 30
else
  # Without .env compose cannot read this file (its secrets are required), so
  # remove the labelled objects directly.
  [ -z "$containers" ] || docker rm -f $containers >/dev/null
  [ -z "$networks" ] || docker network rm $networks >/dev/null
  [ -z "$volumes" ] || docker volume rm $volumes >/dev/null
fi
[ -z "$images" ] || docker image rm $images >/dev/null
[ -z "$old_builds" ] || docker image rm $old_builds >/dev/null

left=$( { docker ps -a --filter "label=$label" -q; docker network ls --filter "label=$label" -q;
          docker volume ls --filter "label=$label" -q; docker image ls -aq --filter "label=$label"; } | wc -l)
[ "$left" -eq 0 ] || die "$left Docker object(s) labelled $label are still present"
rm -f .env .agora-demo.lock
log "removed. Docker objects left with label $label: 0"
log "by hand: delete the tunnel in the Cloudflare dashboard (PLAN.md), then: rm -rf $AGORA_DIR"
