#!/usr/bin/env bash
#
# update.sh - the security-day rebuild of the Agora demo.
#
# The demo never asks drupal.org for update status (it never reports usage),
# so it never learns about security releases by itself. On a security day -
# a Drupal core or contrib security advisory, a PHP release, a Debian security
# update in the base image - run this:
#
#   ./update.sh               rebuild with the newest releases and switch over
#   ./update.sh --audit-only  only check the running image's composer.lock
#                             against today's advisories; change nothing
#
# What it does:
#   1. builds new images (tag "next") from scratch: the newest drupal/cms, the
#      newest drupal/agora_transparency 1.x and every dependency at its newest
#      allowed release, on freshly pulled base images. The build stops if
#      composer audit reports an advisory, and the live demo is untouched.
#   2. switches php and web to the new images (the old ones are kept as "prev");
#   3. restores the baseline snapshot into the new code, runs drush updatedb
#      and rebuilds caches;
#   4. checks the site from the edge network, as cloudflared would;
#   5. takes a new baseline snapshot (the old one is kept as "previous").
# If anything in 2-4 fails, it switches back to the "prev" images and restores
# the old baseline, so the demo ends as it was.
#
# A NEW RELEASE OF THE TEMPLATE ITSELF is different: a recipe is applied once,
# at install, so new demo content or configuration reaches the demo only by
# installing again: ./update.sh, then ./setup.sh --reinstall.

set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
# shellcheck source=lib.sh
. ./lib.sh

AUDIT_ONLY=0
case "${1:-}" in
  "") ;;
  --audit-only) AUDIT_ONLY=1 ;;
  -h|--help) sed -n '2,30p' "$0"; exit 0 ;;
  *) die "unknown option: $1 (see --help)" ;;
esac

require_tools
[ -f .env ] || die ".env is missing; run setup.sh first"
repo=$(image_repo)
docker image inspect "$repo-php:live" "$repo-web:live" >/dev/null 2>&1 \
  || die "no live images ($repo-php:live, $repo-web:live); run setup.sh first"

# ---------------------------------------------------------------------------
# --audit-only: composer audit of the lock file inside the running image.
# ---------------------------------------------------------------------------
if [ "$AUDIT_ONLY" = 1 ]; then
  tmp=$(mktemp -d)
  trap 'rm -rf "$tmp"' EXIT
  cid=$(docker create "$repo-php:live")
  docker cp -q "$cid:/opt/drupal/composer.json" "$tmp/composer.json"
  docker cp -q "$cid:/opt/drupal/composer.lock" "$tmp/composer.lock"
  docker rm "$cid" >/dev/null
  chmod -R a+rX "$tmp"
  composer_image=$(env_get COMPOSER_IMAGE); composer_image=${composer_image:-composer:2.10.3}
  log "composer audit of $repo-php:live ($(docker run --rm --entrypoint cat "$repo-php:live" /opt/drupal/BUILD-INFO.txt | sed -n 's/^built_at=//p'))"
  docker run --rm -v "$tmp:/app:ro" -w /app -e COMPOSER_HOME=/tmp/composer \
    "$composer_image" audit --locked --no-interaction --format=plain
  exit $?
fi

take_lock
t0=$(date +%s)

# Informational: is there a newer PHP 8.4 image than the one pinned?
php_image=$(env_get PHP_IMAGE); php_image=${php_image:-php:8.4.27-fpm-trixie}
newest=$(curl -fsS --max-time 20 \
  'https://hub.docker.com/v2/repositories/library/php/tags?page_size=100&name=-fpm-trixie&ordering=last_updated' 2>/dev/null \
  | grep -o '"name":"8\.4\.[0-9]*-fpm-trixie"' | cut -d'"' -f4 | sort -V | tail -n 1) || newest=""
if [ -n "$newest" ] && [ "php:$newest" != "$php_image" ]; then
  warn "Docker Hub has php:$newest; this demo builds on $php_image. To move to it, set PHP_IMAGE=php:$newest in .env and run ./update.sh again."
else
  log "PHP base image: $php_image${newest:+ (the newest 8.4 FPM trixie tag)}"
fi

# ---------------------------------------------------------------------------
# 1. Build the "next" images. The live demo is not touched.
# ---------------------------------------------------------------------------
export CACHE_BUST
CACHE_BUST=$(date -u +%Y%m%d%H%M%S)
log "building $repo-php:next and $repo-web:next from the newest releases"
IMAGE_TAG=next docker compose build --pull php
IMAGE_TAG=next docker compose build web
next_php=$(docker run --rm --entrypoint cat "$repo-php:next" /opt/drupal/BUILD-ID)
next_web=$(docker run --rm --entrypoint cat "$repo-web:next" /opt/drupal/BUILD-ID)
[ "$next_php" = "$next_web" ] || die "the new php and web images come from different Composer builds; nothing was switched"
changes=$(diff <(docker run --rm --entrypoint cat "$repo-php:live" /opt/drupal/BUILD-INFO.txt | grep -v '^built_at=') \
               <(docker run --rm --entrypoint cat "$repo-php:next" /opt/drupal/BUILD-INFO.txt | grep -v '^built_at=') \
  | sed -n 's/^</    was /p; s/^>/    now /p' || true)
if [ -n "$changes" ]; then
  log "versions, live -> next:"
  printf '%s\n' "$changes"
else
  log "no listed version changed (Drupal CMS, core, the template, its theme, PHP); the base images and every other package were rebuilt anyway"
fi

# ---------------------------------------------------------------------------
# 2-4. Switch, restore the baseline into the new code, update, check.
# ---------------------------------------------------------------------------
switch_to() {  # switch_to <tag>: makes <tag> the live images and restarts php and web
  docker image tag "$repo-php:$1" "$repo-php:live" || return 1
  docker image tag "$repo-web:$1" "$repo-web:live" || return 1
  IMAGE_TAG=live docker compose up -d --wait --wait-timeout 300 --force-recreate --no-deps php web || return 1
}
switch_and_verify() {
  switch_to next || return 1
  IMAGE_TAG=live docker compose run --rm -T restore || return 1
  drush updatedb --yes || return 1
  drush cache:rebuild --quiet || return 1
  self_check live || return 1
}

docker image tag "$repo-php:live" "$repo-php:prev"
docker image tag "$repo-web:live" "$repo-web:prev"
if ! switch_and_verify; then
  warn "the update failed; switching back to the previous images and restoring the old baseline"
  switch_to prev || die "ROLLBACK FAILED: the demo may be down. Check: docker compose ps"
  IMAGE_TAG=live docker compose run --rm -T restore || die "ROLLBACK FAILED at the restore step"
  docker image rm "$repo-php:next" "$repo-web:next" >/dev/null 2>&1 || true
  die "the demo is back on its previous images and baseline. Nothing was updated."
fi

# ---------------------------------------------------------------------------
# 5. New baseline.
# ---------------------------------------------------------------------------
IMAGE_TAG=live docker compose run --rm -T snapshot
docker image rm "$repo-php:next" "$repo-web:next" >/dev/null 2>&1 || true
# Only this project's own superseded builds: Compose labels what it builds.
docker image prune -f --filter "label=com.docker.compose.project=$(project_name)" >/dev/null
log "updated in $(( $(date +%s) - t0 )) s. The previous images stay as $repo-php:prev and $repo-web:prev."
log "Docker's build cache is shared with other projects and is left alone; 'docker builder du' shows its size."
