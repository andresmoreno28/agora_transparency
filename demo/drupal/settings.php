<?php

/**
 * @file
 * Drupal settings for the Agora Transparency live demo.
 *
 * This file is baked into the image and is read-only at runtime. Everything
 * secret or host-specific is read from the container's environment, which
 * compose.yaml fills from /opt/agora-demo/.env (mode 600): no password, salt
 * or token is ever written into the image.
 */

$databases['default']['default'] = [
  'driver' => 'mysql',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
  'host' => getenv('DB_HOST') ?: 'db',
  'port' => '3306',
  'database' => getenv('DB_NAME') ?: 'drupal',
  'username' => getenv('DB_USER') ?: 'drupal',
  'password' => (string) getenv('DB_PASSWORD'),
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
  'isolation_level' => 'READ COMMITTED',
];

$settings['hash_salt'] = (string) getenv('DRUPAL_HASH_SALT');

// Paths. The code is read-only; only these are writable (volumes and /tmp).
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '/opt/drupal/private/files';
$settings['file_temp_path'] = '/tmp';
$settings['config_sync_directory'] = '/opt/drupal/private/sync';

// The demo answers to one host name only.
$demo_hostname = getenv('DEMO_HOSTNAME') ?: 'agora-demo.andresmoreno.dev';
$settings['trusted_host_patterns'] = ['^' . preg_quote($demo_hostname) . '$'];

// Requests arrive through Cloudflare Tunnel: Cloudflare -> cloudflared -> nginx
// -> PHP. PHP sees cloudflared's address on the "edge" network, so that
// network is the only one trusted to say who the visitor is and that the
// request came over HTTPS (X-Forwarded-For, X-Forwarded-Proto).
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = [getenv('EDGE_SUBNET') ?: '10.213.47.0/28'];

// The demo never reports usage to drupal.org. Update status is sent to a
// closed loopback port instead of updates.drupal.org, so no request carrying
// the site key and the module list ever leaves the container; the security
// advisories feed is not fetched either. Security releases reach the demo by
// rebuilding the image (update.sh), not through the site itself.
$config['update.settings']['fetch']['url'] = 'http://127.0.0.1:9/agora-demo-never-reports-usage';
$config['system.advisories']['enabled'] = FALSE;

// Cron does not fetch the two dashboard feeds from www.drupal.org (Drupal
// announcements, Drupical events). They appear only on administration pages,
// which the demo never serves, and the containers have no route out anyway:
// without this, every cron run logs two failed requests.
$config['announcements_feed.settings']['cron_interval'] = PHP_INT_MAX;
$config['drupical.settings']['cron_interval'] = PHP_INT_MAX;

// The demo never sends email: core's test collector keeps messages in the
// database instead of handing them to a mail server. Drupal CMS routes mail
// through the Mail System module, so its defaults are overridden as well.
$config['system.mail']['interface']['default'] = 'test_mail_collector';
$config['mailsystem.settings']['defaults']['sender'] = 'test_mail_collector';
$config['mailsystem.settings']['defaults']['formatter'] = 'test_mail_collector';

// Cron never runs inside a page request. The nightly restore runs it once.
$config['automated_cron.settings']['interval'] = 0;

// Errors are logged, never shown.
$config['system.logging']['error_level'] = 'hide';

// Nothing on the web may run update.php, authorize.php or rebuild.php.
$settings['update_free_access'] = FALSE;
$settings['allow_authorize_operations'] = FALSE;
$settings['rebuild_access'] = FALSE;
// The site directory is read-only, so Drupal must not try to chmod it.
$settings['skip_permissions_hardening'] = TRUE;

$settings['file_scan_ignore_directories'] = ['node_modules', 'bower_components'];
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['state_cache'] = TRUE;
