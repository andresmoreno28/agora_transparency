# The live demo's deployment package

This directory is how the live demonstration of Ágora at
<https://agora-demo.andresmoreno.dev> is built and run. It is versioned here so the demo can be
rebuilt and audited, and it is **not part of the released template**: `.gitattributes` marks
`demo/` as `export-ignore`, so `git archive` and every release package leave it out.

**Read [`PLAN.md`](PLAN.md) first.** It is the whole procedure: what each file does, the steps on
the server in order, how each condition is verified, and what the rehearsal measured.

## What the demo is

A Drupal CMS site with this template and its fictional municipality, Fuentelclaro, installed from
the released packages by one self-contained Docker Compose project: Cloudflare Tunnel, nginx,
PHP-FPM and MariaDB, with no port published on the host. It is read-only to the public:
administration, sign-in and every entity operation answer 404, and Drupal is administered only
with `drush` inside the container. Search engines are asked not to index it, and every night at
04:15 (Europe/Madrid) a systemd timer restores the baseline taken right after install.

## It never reports usage

The demo is not counted as an installation of anything. `drupal/settings.php` sends update status
requests to a closed loopback port instead of `updates.drupal.org` and switches the security
advisories feed off; Drupal CMS telemetry is off (`DRUPAL_CMS_TELEMETRY=0`); and the PHP container
has no route out of the host at all. `setup.sh` stops if any of these does not hold, and
`rehearsal/rehearse.sh` counts every outbound attempt against a trap, with a positive control.

## On a Drupal security advisory, run `./update.sh` on the server

Because it never asks drupal.org for update status, the demo never learns about a security release
by itself. When a Drupal security advisory affects anything it runs, run this on the server:

```bash
cd /opt/agora-demo && ./update.sh
```

It rebuilds both images from the newest releases, refuses to build if `composer audit` reports an
advisory, switches over, checks the site, and rolls back to the previous images and baseline if
anything fails. A new release of the template itself also needs `./setup.sh --reinstall`
afterwards, because a recipe is applied only at install (`PLAN.md`, section 6).
