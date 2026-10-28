#!/usr/bin/env bash
#
# restore.sh - brings the demo back to the baseline taken right after install.
#
# Run nightly by the systemd timer agora-demo-restore.timer (see systemd/), or
# by hand:
#
#   ./restore.sh              restore the current baseline
#   ./restore.sh --previous   restore the baseline the last snapshot replaced
#
# The work happens in the one-shot "restore" container (bin/agora-restore in
# the image), which mounts the snapshot volume read-only. It drops every table,
# loads the dump, replaces the public and private files, rebuilds caches, runs
# cron once, and checks the node count against the snapshot's manifest.

set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
# shellcheck source=lib.sh
. ./lib.sh

case "${1:-}" in
  ""|--previous) ;;
  -h|--help) sed -n '2,15p' "$0"; exit 0 ;;
  *) die "unknown option: $1 (see --help)" ;;
esac

require_tools
take_lock
[ -f .env ] || die ".env is missing; run setup.sh first"

t0=$(date +%s)
IMAGE_TAG=live docker compose run --rm -T restore "$@"
log "restore finished in $(( $(date +%s) - t0 )) s"
