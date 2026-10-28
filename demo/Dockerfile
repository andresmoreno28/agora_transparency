# syntax=docker/dockerfile:1
#
# Agora Transparency live demo: two images built from ONE Composer stage, so
# they always carry the same code.
#
#   target "php"  PHP-FPM serving Drupal; also runs drush and the snapshot and
#                 restore jobs. Runs as www-data on a read-only filesystem.
#   target "web"  nginx serving the static assets (no PHP source at all) and
#                 passing every page request to "php". Runs as nginx (uid 101).
#
# Everything is built from released packages: drupal/cms from Packagist, its
# Drupal projects from packages.drupal.org, and drupal/agora_transparency from
# Packagist, where site templates are published. Nothing is patched.

ARG PHP_IMAGE=php:8.4.27-fpm-trixie
ARG NGINX_IMAGE=nginx:1.30.6-alpine
ARG COMPOSER_IMAGE=composer:2.10.3

FROM ${COMPOSER_IMAGE} AS composer

# ---------------------------------------------------------------------------
# PHP plus the two extensions Drupal CMS needs that the official image lacks
# (gd, pdo_mysql). OPcache is already built in. The compilers and -dev
# packages are removed again in the same layer; only the shared libraries the
# extensions link against stay installed.
# ---------------------------------------------------------------------------
FROM ${PHP_IMAGE} AS php-base
SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN set -eux; \
    savedAptMark="$(apt-mark showmanual)"; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      libfreetype-dev libjpeg62-turbo-dev libpng-dev libwebp-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql; \
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    extdir="$(php -r 'echo ini_get("extension_dir");')"; \
    ldd "$extdir"/*.so \
      | awk '/=>/ { so = $(NF-1); if (index(so, "/usr/local/") == 1) { next }; gsub("^/(usr/)?", "", so); printf "*%s\n", so }' \
      | sort -u | xargs -r dpkg-query --search | cut -d: -f1 | sort -u | xargs -rt apt-mark manual; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    apt-get install -y --no-install-recommends mariadb-client; \
    rm -rf /var/lib/apt/lists/*; \
    php -m | grep -qx gd; \
    php -m | grep -qx pdo_mysql; \
    php -m | grep -qx 'Zend OPcache'; \
    command -v mariadb mariadb-dump

# ---------------------------------------------------------------------------
# The Composer build, exactly as the template's README documents it:
#   composer create-project --no-install drupal/cms
#   composer install
#   composer require drupal/agora_transparency
#   composer audit
# The build FAILS if composer audit reports an advisory, so a vulnerable image
# is never produced; the running demo keeps its previous image instead.
# ---------------------------------------------------------------------------
FROM php-base AS build
SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends git unzip; \
    rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_HOME=/tmp/composer-home \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1
ARG DRUPAL_CMS_CONSTRAINT=^2.2
ARG TEMPLATE_CONSTRAINT=^1.0
# A new value re-runs every step below on the next build: setup.sh and
# update.sh pass the current time, so each build resolves the newest releases.
ARG CACHE_BUST=0
RUN set -eux; \
    echo "cache bust: ${CACHE_BUST}"; \
    # Downloads made by this build are not reported to Packagist or drupal.org.
    composer config --global notify-on-install false; \
    composer create-project --no-install --no-progress \
      "drupal/cms:${DRUPAL_CMS_CONSTRAINT}" /opt/drupal 2>&1 | tee /tmp/create-project.log; \
    cd /opt/drupal; \
    composer install --no-progress; \
    composer config allow-plugins.drupal/site_template_helper true; \
    composer require --no-progress --update-with-all-dependencies \
      "drupal/agora_transparency:${TEMPLATE_CONSTRAINT}" 2>&1 | tee /tmp/require.log; \
    test -f /opt/drupal/recipes/agora_transparency/recipe.yml; \
    composer audit --no-interaction --format=plain 2>&1 | tee /opt/drupal/BUILD-AUDIT.txt; \
    cms_version="$(sed -n 's/.*Installing drupal\/cms (\([^)]*\)).*/\1/p' /tmp/create-project.log | head -n 1)"; \
    # Drupal's recipe unpacker drops the template from composer.lock once its
    # requirements are copied into the project, so its version is read from
    # the require output instead.
    template_version="$(sed -n 's/.*Installing drupal\/agora_transparency (\([^)]*\)).*/\1/p' /tmp/require.log | head -n 1)"; \
    test -n "$template_version"; \
    { \
      echo "built_at=$(date -u +%Y-%m-%dT%H:%M:%SZ)"; \
      echo "drupal/cms=${cms_version:-unknown}"; \
      echo "drupal/agora_transparency=${template_version} (unpacked into the project)"; \
      php -r '$l = json_decode(file_get_contents("composer.lock"), TRUE); \
        $want = ["drupal/core", "drupal/agora_theme", "drupal/agora_core", \
                 "drupal/drupal_cms_helper", "drupal/config_guardian", "drush/drush", "twig/twig"]; \
        foreach (array_merge($l["packages"], $l["packages-dev"]) as $p) { \
          if (in_array($p["name"], $want, TRUE)) { echo $p["name"], "=", $p["version"], "\n"; } \
        } \
        echo "packages_locked=", count($l["packages"]) + count($l["packages-dev"]), "\n";'; \
      echo "php=$(php -r 'echo PHP_VERSION;')"; \
    } > /opt/drupal/BUILD-INFO.txt; \
    sha256sum /opt/drupal/composer.lock | cut -d' ' -f1 > /opt/drupal/BUILD-ID; \
    cat /opt/drupal/BUILD-INFO.txt; \
    rm -rf /tmp/composer-home /tmp/create-project.log /tmp/require.log; \
    # The static files nginx serves: assets only, never PHP or source files.
    mkdir -p /opt/static; \
    find web -type f -not -path 'web/sites/*' \( \
         -iname '*.css' -o -iname '*.js' -o -iname '*.mjs' \
      -o -iname '*.png' -o -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.gif' \
      -o -iname '*.webp' -o -iname '*.avif' -o -iname '*.svg' -o -iname '*.ico' \
      -o -iname '*.woff' -o -iname '*.woff2' -o -iname '*.ttf' -o -iname '*.otf' -o -iname '*.eot' \
      -o -path 'web/robots.txt' \) -print0 \
      | tar --null -cf - -T - | tar -xf - -C /opt/static; \
    cp /opt/drupal/BUILD-ID /opt/static/BUILD-ID

# ---------------------------------------------------------------------------
# Runtime: PHP-FPM. The code is owned by root and the root filesystem is
# mounted read-only by compose.yaml; www-data can write only to the volumes
# (public files, private files) and to /tmp.
# ---------------------------------------------------------------------------
FROM php-base AS php
COPY --from=build /opt/drupal /opt/drupal
RUN set -eux; \
    rm -f /usr/local/etc/php-fpm.d/*; \
    install -d -o www-data -g www-data -m 0755 /opt/drupal/web/sites/default/files; \
    install -d -o www-data -g www-data -m 0750 \
      /opt/drupal/private /opt/drupal/private/files /opt/drupal/private/sync \
      /var/lib/agora-snapshot
COPY php/zz-agora.ini /usr/local/etc/php/conf.d/zz-agora.ini
COPY php/fpm-pool.conf /usr/local/etc/php-fpm.d/agora.conf
COPY --chmod=0444 drupal/settings.php /opt/drupal/web/sites/default/settings.php
COPY --chmod=0755 bin/agora-snapshot bin/agora-restore /usr/local/bin/
ENV PATH=/opt/drupal/vendor/bin:$PATH \
    HOME=/tmp
WORKDIR /opt/drupal
USER www-data
STOPSIGNAL SIGQUIT
CMD ["php-fpm", "--nodaemonize"]

# ---------------------------------------------------------------------------
# Runtime: nginx with the static assets only.
# ---------------------------------------------------------------------------
FROM ${NGINX_IMAGE} AS web
RUN set -eux; \
    rm -f /etc/nginx/conf.d/default.conf; \
    mkdir -p /opt/drupal/web/sites/default/files; \
    chown 33:33 /opt/drupal/web/sites/default/files
COPY nginx.conf /etc/nginx/nginx.conf
COPY --from=build /opt/static/web/ /opt/drupal/web/
COPY --from=build /opt/static/BUILD-ID /opt/drupal/BUILD-ID
USER 101:101
# cspell:ignore extdir
