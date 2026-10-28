# Ágora Transparency 1.0.0 live demo on your VPS: plan

> English translation of the plan deployed with the package on 2026-10-27, made with no semantic
> change because this repository is written in English (D-017). The deployed original is in
> Spanish; its SHA-256 is `e74037fcc43c803738e38488e2e3890d7b4ac63f543585f3c30c75bb7eb8334a`.
> Every command is the original's; only comments, one prompt and one pair of output strings inside
> them are translated. Every other file here except `README.md` is byte-identical to the deployed
> package.

Rehearsed end to end on 2026-10-27 on Docker 28.1.1 with Compose 2.35.1 (WSL2), except for the
Cloudflare tunnel. The rehearsal's result is at the end.

## 1. What it is

It is a self-contained Docker Compose project. It lives in `/opt/agora-demo` and serves, at
`https://agora-demo.andresmoreno.dev`, a Drupal CMS site with the Ágora 1.0.0 template and the
Fuentelclaro content. It is made of four containers that are always running and two one-shot jobs:

```
Cloudflare ──tunnel──> cloudflared ──edge network──> web (nginx) ──app network──> php (PHP-FPM) ──db network──> db (MariaDB)
```

| Service | Image | What for |
|---|---|---|
| `db` | `mariadb:11.8.9` | The demo's own database |
| `php` | `agora-demo-php:live` (built here) | PHP-FPM 8.4.27 with Drupal; also runs drush |
| `web` | `agora-demo-web:live` (built here) | nginx with only the static files; blocks administration |
| `cloudflared` | `cloudflare/cloudflared:2026.10.4` | The demo's OWN tunnel, separate from the andres-cms one |
| `snapshot` | the `php` image | One-shot job: saves the baseline (database + files) |
| `restore` | the `php` image | One-shot job: restores it every night. Mounts the copy read-only |

Networks, all of them the project's own:

| Network | Who is on it | Route to the Internet |
|---|---|---|
| `egress` | cloudflared only | yes, the only one. It is the way out to Cloudflare |
| `edge` | cloudflared and web | no (`internal: true`) |
| `app` | web and php | no |
| `db` | php and db | no |

PHP has no route out at all. Even if something tried to report to drupal.org or to Amplitude, it
could not get out. On top of that, usage reporting is switched off in the configuration (section 7).

## 2. Files

| File | What it does |
|---|---|
| `compose.yaml` | The services, their limits and their hardening, the networks and the volumes. It contains no secret |
| `Dockerfile` | One Composer stage and two runtime images (`php`, `web`) built from that same stage |
| `nginx.conf` | nginx without root and read-only. Blocks administration and every PHP file other than `index.php` |
| `php/zz-agora.ini` | `expose_php=Off`, `session.upload_progress.enabled=Off`, OPcache on |
| `php/fpm-pool.conf` | The FPM pool: `disable_functions` for the web only, and the minimal environment the PHP processes receive |
| `drupal/settings.php` | Reads the secrets from the environment. Sets the trusted host and the trusted proxy, and switches usage reporting off |
| `bin/agora-snapshot`, `bin/agora-restore` | The scripts that go inside the image and that the `snapshot` and `restore` jobs use |
| `.env.example` | The variable names. `setup.sh` copies it to `.env` (mode 600) |
| `setup.sh` | First build, install, baseline and self-check. `--check` only checks |
| `restore.sh` | The nightly restore (`--previous` restores the previous baseline) |
| `update.sh` | The security-day rebuild, with automatic rollback. `--audit-only` only audits |
| `uninstall.sh` | Removes everything this project created, and nothing else |
| `lib.sh` | Functions shared by the scripts |
| `systemd/agora-demo-restore.{service,timer}` | The nightly timer |
| `rehearsal/` | For rehearsing only: `rehearse.sh`, the outbound trap and the FPM probe. Never part of the real demo |

## 3. Requirements on the VPS

- Docker CE with the Compose v2 plugin (`docker compose version`). I rehearsed it with 2.35.1.
- `flock`, `ip` and `curl`, which Debian 13 already ships.
- About 3 GB free: the `php` image takes 840 MB and the `web` one 113 MB, plus the database, the
  previous images that are kept and the build cache.
- A route out to Cloudflare from Docker, which already works because the andres-cms tunnel uses it.
- No Compose project called `agora-demo` may exist yet. Check it with
  `docker compose ls -a | grep agora-demo`, which must print nothing.
- The range `10.213.47.0/28` must be free. `setup.sh` checks it against every Docker network and
  the host's routes, and stops without touching anything if it overlaps. If it collides, change
  `EDGE_SUBNET` in `.env`.

## 4. Steps, in order

All of them are done as root on the VPS.

### Step 0 (recommended): a rehearsal on the VPS, without the tunnel and beside everything else

The rehearsal uses another project name (`agora-demo-rehearsal`), other images and other subnets.
It touches neither the demo nor andres-cms, and removes itself at the end.

```bash
mkdir -p /root/agora-demo-rehearsal
tar -xzf agora-demo-compose.tar.gz -C /root/agora-demo-rehearsal
cd /root/agora-demo-rehearsal
./rehearsal/rehearse.sh            # expected result: "checks passed: 26   failed: 0", exit 0
./rehearsal/rehearse.sh --teardown # removes it all: containers, networks, volumes, images and .env
cd / && rm -rf /root/agora-demo-rehearsal
```

### Step 1: copy the files

Copy the two files in `dist/` to the VPS: `agora-demo-compose.tar.gz` and
`agora-demo-compose.tar.gz.sha256`. The `.tar.gz` holds the contents of this folder, except
`dist/`, with the scripts already executable.

```bash
sha256sum -c agora-demo-compose.tar.gz.sha256     # expected: agora-demo-compose.tar.gz: OK
install -d -m 750 /opt/agora-demo
tar -xzf agora-demo-compose.tar.gz -C /opt/agora-demo
cd /opt/agora-demo
```

### Step 2: create the tunnel in Cloudflare and paste the token

This step is done in the Cloudflare dashboard. A **new** tunnel must be created: do not reuse the
andres-cms one.

1. Go to `https://one.dash.cloudflare.com`, open the `andresmoreno.dev` account and go to
   **Networks → Tunnels**. In the new dashboard it is under **Networks → Connectors → Cloudflare Tunnels**.
2. Click **Create a tunnel**, choose **Cloudflared** and name it `agora-demo`. Save.
3. On the **Install and run a connector** screen, choose **Docker**. The command it shows ends in
   `--token eyJ…`. **Do not run it.** Copy only the token, the long string that comes after
   `--token`.
4. On the VPS, create `.env` with mode 600 and paste the token without it staying in the shell
   history. The database passwords and Drupal's hash salt are generated by `setup.sh` in step 3:

   ```bash
   cd /opt/agora-demo
   [ -f .env ] || install -m 600 .env.example .env
   read -rs -p "agora-demo tunnel token: " T && sed -i "s|^TUNNEL_TOKEN=.*|TUNNEL_TOKEN=$T|" .env && unset T; echo
   stat -c '%a %U %n' .env            # expected result: 600 root .env
   grep -c '^TUNNEL_TOKEN=.\+' .env   # expected result: 1
   ```
5. Back in the dashboard, click **Next**. Under **Public Hostnames**, called
   **Published application routes** in the new dashboard, fill in:
   - Subdomain: `agora-demo`
   - Domain: `andresmoreno.dev`
   - Path: empty
   - Service, Type: `HTTP`
   - Service, URL: `web:8080`

   Do not touch **Additional application settings**: cloudflared has to keep the original Host
   header. Save. Cloudflare creates the CNAME `agora-demo` → `<id>.cfargotunnel.com` by itself,
   proxied.
6. **Cloudflare Access.** If andres-cms has an Access application covering `*.andresmoreno.dev`,
   the demo would ask visitors to sign in. Check **Access → Applications**. If there is a wildcard,
   create an application for `agora-demo.andresmoreno.dev` with a **Bypass** policy and
   **Include: Everyone**.
7. This is optional and adds a second barrier at Cloudflare's edge. In **Security → WAF →
   Custom rules**, create a rule for the host `agora-demo.andresmoreno.dev` that blocks the paths
   starting with `/user` or `/admin`. nginx already blocks them inside the demo.

### Step 3: build, install and take the baseline

```bash
cd /opt/agora-demo
./setup.sh
```

In the rehearsal it took between 131 and 150 s: 58-69 s of build, 35-38 s of install, the baseline
and the self-check. It ends with `self-check passed` and exit 0. If there is no token, it stops
before building anything.

### Step 4: check from outside, through Cloudflare

```bash
docker compose ps --format '{{.Service}} {{.Status}}'
# expected: db, php, web and cloudflared with "Up … (healthy)"
docker compose logs cloudflared | grep -c 'Registered tunnel connection'
# expected: 4 (or more)
for p in / /contracts /datasets /user/login /admin /node/add /core/install.php /update.php; do
  curl -s -o /dev/null -w "%{http_code} $p\n" "https://agora-demo.andresmoreno.dev$p"; done
# expected: 200 for the first three and 404 for all the others
```

### Step 5: the nightly timer

I recommend systemd: it restores every day at 04:15 Madrid time, with up to 5 minutes of random
delay. If the VPS was off at that time, it catches up on the missed run.

```bash
cd /opt/agora-demo
install -m 644 systemd/agora-demo-restore.service systemd/agora-demo-restore.timer /etc/systemd/system/
systemctl daemon-reload
systemctl enable --now agora-demo-restore.timer
systemctl list-timers agora-demo-restore.timer      # expected: NEXT = tomorrow 04:15 CET/CEST
systemctl start agora-demo-restore.service          # try it now
journalctl -u agora-demo-restore.service -n 8 --no-pager
# expected: "[restore] done: 61 nodes, as recorded in the snapshot" and "restore finished in ~15 s"
```

This is the text of the two units, which are already in `systemd/`:

```ini
# /etc/systemd/system/agora-demo-restore.service
[Unit]
Description=Agora demo: restore the baseline snapshot taken right after install
Documentation=file:///opt/agora-demo/PLAN.md
Requires=docker.service
After=docker.service network-online.target
Wants=network-online.target

[Service]
Type=oneshot
WorkingDirectory=/opt/agora-demo
ExecStart=/opt/agora-demo/restore.sh
TimeoutStartSec=20min
Nice=10
IOSchedulingClass=best-effort
IOSchedulingPriority=7

# /etc/systemd/system/agora-demo-restore.timer
[Unit]
Description=Agora demo: nightly restore of the baseline snapshot

[Timer]
OnCalendar=*-*-* 04:15:00 Europe/Madrid
RandomizedDelaySec=5min
Persistent=true
Unit=agora-demo-restore.service

[Install]
WantedBy=timers.target
```

If you prefer cron, bear in mind that Debian's cron does not accept a time zone per line and uses
the server's. The file would be `/etc/cron.d/agora-demo`:

```
15 4 * * * root /opt/agora-demo/restore.sh >> /var/log/agora-demo-restore.log 2>&1
```

## 5. How each condition is verified

Every command is run from `/opt/agora-demo`. The expected outputs are the rehearsal's.

**1. Its own project, sharing nothing with andres-cms**

```bash
docker network ls --filter label=com.docker.compose.project=agora-demo --format '{{.Name}}'
# expected: agora-demo_app, agora-demo_db, agora-demo_edge, agora-demo_egress
docker volume ls --filter label=com.docker.compose.project=agora-demo --format '{{.Name}}'
# expected: agora-demo_db-data, agora-demo_files, agora-demo_private, agora-demo_snapshot
for c in $(docker compose ps -q); do docker inspect -f '{{.Name}}: {{range $k,$v := .NetworkSettings.Networks}}{{$k}} {{end}}' $c; done
# expected: only agora-demo_* networks; no andres-cms container appears on them
```

The `agora-demo-php` and `agora-demo-web` images are built only from the files in this folder. The
public base images (`mariadb`, `nginx`, `php`, `cloudflared`) are pulled from the registry. If
andres-cms uses exactly the same tag, Docker keeps a single copy of their read-only layers, but no
state is shared.

**2. CPU, memory and process limits on every service**

```bash
for c in $(docker compose ps -q); do docker inspect -f '{{.Name}} cpus={{.HostConfig.NanoCpus}} mem={{.HostConfig.Memory}} pids={{.HostConfig.PidsLimit}}' $c; done
# expected: db 1 CPU / 1 GiB / 256 · php 2 CPU / 2 GiB / 256 · web 1 CPU / 256 MiB / 64 · cloudflared 0.5 CPU / 256 MiB / 64
```

The `snapshot` and `restore` jobs have 1 CPU, 1 GiB and 128 processes. The swap limit equals the
memory limit, so there is no extra swap.

**3. No published port; only the demo's own tunnel; the token outside `compose.yaml`**

```bash
for c in $(docker compose ps -q); do docker inspect -f '{{.Name}} {{json .HostConfig.PortBindings}} {{.HostConfig.PublishAllPorts}}' $c; done
# expected: "{} false" on every container
for c in $(docker compose ps -q); do docker port $c; done        # expected: nothing
grep -n TUNNEL_TOKEN compose.yaml          # expected: only  TUNNEL_TOKEN: ${TUNNEL_TOKEN:-}
stat -c '%a %U' .env                       # expected: 600 root
```

`docker compose ps` shows `80/tcp` on `web` because the nginx image declares that port. Declaring a
port is not publishing it, and the `PortBindings` line confirms it.

**4. Nightly restore**

```bash
U=https://agora-demo.andresmoreno.dev/datasets/budget-execution-financial-year-2024
curl -s -o /dev/null -w '%{http_code}\n' $U                  # expected: 200
docker compose exec -T php drush php:eval 'echo \Drupal::service("path_alias.manager")->getPathByAlias("/datasets/budget-execution-financial-year-2024"), PHP_EOL;'
# expected: /node/6  (it was 6 in the rehearsal; use the number printed here)
docker compose exec -T php drush entity:delete node 6
curl -s -o /dev/null -w '%{http_code}\n' $U                  # expected: 404
./restore.sh                                                  # expected: "done: 61 nodes …", about 15 s
curl -s -o /dev/null -w '%{http_code}\n' $U                  # expected: 200
```

**5. No administration reachable from outside**

```bash
./setup.sh --check
# expected: 4 public pages with 200 and 31 blocked paths (404, or 403 for /fpm-ping), exit 0
```

The list covers `/user/login`, `/user/password`, `/user/register`, `/user/1/edit`, `/admin*`,
`/node/add*`, `/node/*/edit`, `/core/install.php` and `/update.php`. It also covers the variants
Drupal accepts and nginx might not see: `/ADMIN`, `/%61dmin`, `//user/login`, `/index.php/admin`
and `/en/admin`.

```bash
docker compose exec -T php drush config:get user.settings register
# expected: 'user.settings:register': admin_only
docker compose exec -T php drush sql:query "SELECT COUNT(*) FROM users_field_data WHERE uid > 0"
# expected: 1  (only the administrator, with a random password that is stored nowhere)
```

Administration is possible only with `docker compose exec php drush …`. Even the `drush uli` link
returns 404, because `/user/reset/…` is blocked too.

**6. PHP 8.4.27 or later with the hardened pool**

```bash
docker compose exec -T php php -v | head -1                  # expected: PHP 8.4.27 (cli) …
docker compose exec -T php php < rehearsal/fpm-probe.php     # asks FPM itself, not the CLI
# expected:
#   sapi=fpm-fcgi
#   php_version=8.4.27
#   disable_functions=exec,shell_exec,system,passthru,proc_open,popen,pcntl_exec
#   still_callable=none
#   expose_php=off
#   session.upload_progress.enabled=off
#   opcache_enabled=yes
#   memory_limit=256M
docker compose exec -T php php -r 'echo function_exists("proc_open") ? "cli keeps proc_open\n" : "broken\n";'
# expected: cli keeps proc_open  (drush keeps working)
```

**7. Drupal up to date, no usage reports to drupal.org and no telemetry**

```bash
docker compose exec -T php cat /opt/drupal/BUILD-INFO.txt     # the versions of this build
docker compose exec -T php cat /opt/drupal/BUILD-AUDIT.txt    # expected: No security vulnerability advisories found.
./update.sh --audit-only                                      # audits again against today's advisories; same expected result
docker compose exec -T php drush config:get update.settings fetch.url --include-overridden
# expected: 'update.settings:fetch.url': 'http://127.0.0.1:9/agora-demo-never-reports-usage'
docker compose exec -T php drush php:eval 'var_export(\Drupal::state()->get("drupal_cms_telemetry")); echo PHP_EOL;'
# expected: false
docker compose exec -T php php -r '$s=@fsockopen("1.1.1.1",443,$e,$m,3); echo $s ? "GETS OUT\n" : "no route out: $m\n";'
# expected: no route out: Network is unreachable
```

Drupal CMS's telemetry queue holds one event, "installed from a template". Drupal CMS's own code
always records it, but sends it only if telemetry is on. Here it is off, and the container has no
route out anyway.

**Hardening**

```bash
for c in $(docker compose ps -q); do docker inspect -f '{{.Name}} user={{.Config.User}} ro={{.HostConfig.ReadonlyRootfs}} cap_drop={{json .HostConfig.CapDrop}} {{json .HostConfig.SecurityOpt}} restart={{.HostConfig.RestartPolicy.Name}} log={{.HostConfig.LogConfig.Type}}/{{index .HostConfig.LogConfig.Config "max-size"}}x{{index .HostConfig.LogConfig.Config "max-file"}}' $c; done
# expected: no empty or root user (mysql, www-data, 101:101; the cloudflared image uses 65532),
#           ro=true, ["ALL"], ["no-new-privileges:true"], unless-stopped, json-file/10mx3
docker network inspect -f '{{.Name}} internal={{.Internal}}' agora-demo_db agora-demo_app agora-demo_edge agora-demo_egress
# expected: true, true, true, false (egress is the only network with a route out, and only cloudflared is on it)
for c in $(docker compose ps -q); do docker inspect -f '{{range .Mounts}}{{.Source}} {{end}}' $c; done | grep -c docker.sock
# expected: 0
```

The only trusted host is `agora-demo.andresmoreno.dev`. Any other name gets a 400.
Drupal trusts `X-Forwarded-Proto` only when it comes from the `edge` network, which is cloudflared's.
**No visitor IP address is kept** (decision of 2026-10-27): nginx writes "-" instead of the address
in its log and empties `X-Forwarded-For` before passing the request to PHP, so Drupal only sees
cloudflared's internal address. The rehearsal checks that Drupal takes `https` but not the
visitor's address, and that from inside nginx it ignores a forged header.

## 6. Security days

The demo never asks drupal.org whether there are updates, so that it does not count as an
installation. The consequence is that it **will never find out by itself** about a security
release, and Drupal's status report will say there is no update data. You have to find out
yourself, from drupal.org's security advisories (`https://www.drupal.org/security`, the mailing
list or the RSS feed), and then run:

```bash
cd /opt/agora-demo && ./update.sh
```

This is what it does:

1. Builds new images from scratch, with the latest published releases of Drupal CMS, of the
   template's 1.x and of every dependency, and with freshly pulled base images. If `composer audit`
   finds anything, it stops and the demo stays as it was.
2. Switches `php` and `web` to the new images and keeps the previous ones as `:prev`.
3. Restores the baseline, runs `drush updatedb` and `drush cache:rebuild`, and checks the site.
4. Takes a new baseline and keeps the previous one as `previous`.

If anything in steps 2 or 3 fails, it goes back by itself to the previous images and baseline. I
rehearsed it by breaking nginx on purpose: it took 100 s, the demo kept answering 200 and the
running images were exactly the ones from before. A normal update took between 99 and 120 s.

If a new PHP 8.4 release comes out, `update.sh` warns with the new tag. Put it in `.env`, for
example `PHP_IMAGE=php:8.4.28-fpm-trixie`, and run `./update.sh` again. For MariaDB and
cloudflared, put the new tag in `.env` (`MARIADB_IMAGE=…` or `CLOUDFLARED_IMAGE=…`) and run
`docker compose up -d db cloudflared`.

For a new release of the template itself, `update.sh` is not enough. A recipe is applied only at
install, so run `./update.sh` and then `./setup.sh --reinstall`. In the rehearsal, the reinstall
took 59 s.

## 7. How to undo it

```bash
cd /opt/agora-demo
./uninstall.sh      # shows you the list and asks you to type "agora-demo" to confirm
```

It removes the containers, networks and volumes labelled with the `agora-demo` project, the
database, the files and the snapshots included. It also removes the `agora-demo-php:*` and
`agora-demo-web:*` images and the project's earlier builds, the systemd timer, `.env` and the lock
file. When it finishes, it checks that no object with the label is left.

It removes neither the pulled base images nor Docker's build cache, because they are shared. In
the rehearsal I compared the machine's 426 Docker objects before and after: exactly the project's
14 were gone.

Afterwards, by hand:

1. In Cloudflare, go to **Networks → Tunnels → agora-demo**. Delete the public route
   `agora-demo.andresmoreno.dev` and then the tunnel.
2. In the domain's **DNS → Records**, check that the `agora-demo` CNAME is gone, and delete it if
   it is still there.
3. If you created the Access Bypass application or the WAF rule, delete them too.
4. Run `rm -rf /opt/agora-demo`.

## 8. What could not be rehearsed

- **The tunnel.** I ran no real tunnel. What I did check: the image and the command start
  read-only and without privileges, a fake token is rejected (exit 255, "Provided Tunnel token is
  not valid") and the healthcheck returns 1 when the tunnel is not ready. In the rehearsal, a curl
  container on the `edge` network stood in for cloudflared, with the same headers.
- **The real systemd timer.** I checked the syntax with `systemd-analyze verify` and the time with
  `systemd-analyze calendar` (next run: 04:15 CET). The script it starts is rehearsed.

## 9. Rehearsal result (2026-10-27)

- **Versions resolved:** Drupal CMS 2.2.7, drupal/core 11.4.11, Ágora 1.0.0, agora_theme 1.2.3,
  agora_core 1.0.0, Config Guardian 1.1.0, drush 13.8.0 and PHP 8.4.27. 166 packages in all.
  `composer audit` found no advisory, neither at build time nor when auditing again.
- **From scratch**, `setup.sh` ended with exit 0 in 131 s on the final pass: 58 s of build and 35 s of install. Earlier passes took up to 150 s.
  The `php` image takes 840 MB, the `web` one 113 MB and the baseline 1.1 MB.
- **HTTP**, from the `edge` network:
  - `/`, `/contracts`, `/datasets` and `/datasets/budget-execution-financial-year-2024` answered
    200 with "Fuentelclaro".
  - The 31 blocked paths answered 404, and `/fpm-ping` answered 403.
  - The front page's 6 assets: 200 for 6 of 6.
  - Another Host answered 400.
- **Containers:** none publishes a port. All have limits, a read-only filesystem, `cap_drop ALL` and
  `no-new-privileges`; none runs as root and none mounts the Docker socket.
- **Outbound to the Internet:**
  - The rehearsal had a trap: the drupal.org and Amplitude host names pointed to a recorder.
  - After building, installing, running cron twice, making an explicit update status request and
    taking the baseline, the trap recorded 0 attempts.
  - As a positive control, I sent two requests on purpose and the trap recorded exactly 2.
  - The update status request went to `http://127.0.0.1:9/…` and the connection was refused.
- **Restore:** after a node was deleted, its page answered 404. `restore.sh` brought it back in
  12-16 s and the page answered 200 again.
- **Total:** 26 checks passed and 0 failed. In addition, `update.sh` worked, so did the rollback,
  and `--reinstall` and `--check` exited 0.

## 10. Open questions for you

1. **Cloudflare Access.** Is there an Access application covering `*.andresmoreno.dev`? If there
   is, the Bypass of step 2.6 is needed.
2. **Indexing.** nginx now sends `X-Robots-Tag: noindex, nofollow`, because this is a fictional
   municipality with invented salaries and contracts. Would you rather have search engines index
   it? It is one line in `nginx.conf`.
3. **Time and name.** The restore runs at 04:15 Madrid time and the site is called "Fuentelclaro
   Town Council" (`SITE_NAME` in `.env`). Are they right for you?
4. **Limits.** The overall ceiling is 4.5 CPU and 4.5 GiB. Do you want them lower, given that it
   shares the machine with production?
5. **Logs.** Settled on 2026-10-27: no visitor IP address is kept, neither by nginx nor by Drupal
   (section 5).
6. **Alpha package.** Drupal CMS 2.2.7 includes `drupal/canvas_translate: @alpha` in its own
   `composer.json`. Drupal CMS brings it, not Ágora, but the demo carries it. I mention it so it
   does not surprise you if anyone looks.

<!-- cspell:ignore dmin -->
