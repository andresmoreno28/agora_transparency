# The export rig · `~/agora-export`

**Built and measured 2026-08-24.** Infrastructure only: no content model, no node types, no fields,
nothing written into `config/`. T-601 is a separate dispatch.

This is the third rig on this machine and the only one D-032=B can use. `~/agora-cms` and
`~/agora-smoke` stay exactly as they are — throwaway, never edited, never committed from.

> **Six things this note establishes, each with a number rather than an assertion.**
>
> 1. The rig's `require` is **4 entries + 1 dev entry**, **0** of them unstable, and **0** of the
>    eleven out-of-scope packages appear anywhere in its lock.
> 2. The installed template is a **symlink** resolving **outside** the rig, and edits propagate
>    working copy → rig → container.
> 3. **The two older rigs got copies for a named reason, not a mysterious one:**
>    `COMPOSER_MIRROR_PATH_REPOS=1`, an explicit opt-out inherited from upstream.
> 4. The rig holds **no git clone at all**. The machine still has **three** clones, not four.
> 5. The install reaches **`install_finished`** with **0** AI-key environment variables.
> 6. **D-032's central finding is confirmed by measurement:** `_core.default_config_hash` is
>    present on **398** live config objects and on **0 of 454** exported files. And the baseline
>    diff that replaces it is **clean** — a re-export with nothing changed differs in **0 of 541
>    files**.

---

## 1. Where it is and how to rebuild it from scratch

Location: `~/agora-export` inside **WSL2 Ubuntu**, reached from Windows with
`wsl.exe -e bash -lc '...'`. It is **not** a git working copy and must never become one.

DDEV project `agora-export`, type `drupal11`, docroot `web`, Drupal **11.4.5**,
DDEV v1.24.4, `performance_mode: none` (plain bind mounts — no Mutagen, so symlinks behave).

### 1.1 The whole procedure

<!-- The commands below embed this machine's OS account name inside absolute paths. That token is
     a path component, not a word: D-024(3) bucket 4 — scoped in place, not declared globally.
     The paths are load-bearing: `readlink -f` resolving to one of them is the proof in §3. -->
<!-- cspell:disable -->
```bash
# 0. One variable used throughout: the ONE working copy.
S=/mnt/c/Users/andresmrubio/Documents/projects/agora

mkdir -p ~/agora-export && cd ~/agora-export
ddev config --project-name=agora-export --project-type=drupal11 --docroot=web
ddev config --web-environment-add="COMPOSER_NO_AUDIT=1"
# DELIBERATELY ABSENT: COMPOSER_MIRROR_PATH_REPOS. See §4.

# 1. Mount the working copy at the SAME absolute path it has on the host, READ-ONLY.
mkdir -p .ddev
cat > .ddev/docker-compose.agora-source.yaml <<YAML
services:
  web:
    volumes:
      - type: bind
        source: $S
        target: $S
        read_only: true
YAML

# 2. Turn assertions off BEFORE the first composer run. This is not optional — see §5.
mkdir -p .ddev/php
printf '[PHP]\nzend.assertions = -1\n' > .ddev/php/agora-export.ini

ddev start

# 3. Drupal skeleton.
ddev composer create-project --no-install drupal/recommended-project
ddev composer config name "agora/export-rig"
# core-recipe-unpack would flatten the template's requirements into the rig's own
# `require` and dissolve the path-repo entry this rig exists for. core-project-message
# is noise. Both go.
ddev composer remove --no-update drupal/core-recipe-unpack drupal/core-project-message
ddev composer config allow-plugins.drupal/site_template_helper true

# 4. The template as a SYMLINKED path repository, pinned to a stable version string so the
#    rig's require reads ^1.0 and not @dev (see §2, divergence 1).
ddev composer config repositories.source \
  "{\"type\":\"path\",\"url\":\"$S\",\"options\":{\"symlink\":true,\"versions\":{\"drupal/agora_transparency\":\"1.0.0\"}}}"

ddev composer require --no-update "drupal/agora_transparency:^1.0"
ddev composer require --no-update --dev "drush/drush:^13"
ddev composer update

# 5. Install with no AI key configured, from today's recipe.yml.
ddev drush -y site:install recipes/agora_transparency \
  --account-pass=admin --site-name="Agora Export Rig"

# 6. The baseline. Taken BEFORE anything is modelled. This is the denominator.
mkdir -p export-scratch
ddev drush site:export \
  --destination=/var/www/html/export-scratch/baseline \
  --base="$S" --overwrite
```
<!-- cspell:enable -->

`rig-report.php` in the rig root reproduces every manifest number in §2 in one run
(`ddev exec -- php rig-report.php`).

---

## 2. `require` — exactly Ágora's dependency closure

Printed from the rig's `composer.json`:

```
require
  composer/installers                    ^2.3
  drupal/agora_transparency              ^1.0
  drupal/core-composer-scaffold          ^11.4
  drupal/core-recommended                ^11.4
require-dev
  drush/drush                            ^13
```

Everything else Ágora needs — the seven upstream recipes, `drupal_cms_helper`,
`site_template_helper` and their modules — arrives **transitively through the template's own
`require`**, which is the point: the rig does not restate Ágora's SBOM, it consumes it.

**Unstable-constraint scan** over `require` + `require-dev`:

| measurement | value |
|---|---|
| entries checked | **5** |
| entries matching `@dev`/`@alpha`/`@beta`/`@RC`/`dev-`/`-dev` | **0** |
| `minimum-stability` | `stable` |
| `prefer-stable` | `true` |

**Resolved lock**, which is the stricter question — a stable constraint can still resolve to a
non-stable version:

| measurement | value |
|---|---|
| `packages` | 132 |
| `packages-dev` | 21 |
| locked packages checked | **153** |
| non-stable resolved versions | **0** |

**Packages the dispatch named as out of scope**, each looked up in the lock:

```
drupal/byte                            no      drupal/drupal_cms_forms                no
drupal/haven                           no      drupal/drupal_cms_starter              no
drupal/drupal_cms_ai                   no      drupal/drupal_cms_site_template_base   no
drupal/webform                         no      drupal/drupal_cms_accessibility_tools  no
drupal/search_api                      no      drupal/drupal_cms_google_analytics     no
                                               drupal/drupal_cms_seo_tools            no
```

**11 checked, 0 present.** Contrast `~/agora-cms`, whose `require` carries eight of them plus
`drupal/webform` at `@beta` and `drupal/drupal_cms_site_template_base` at `^1@dev`.

### Divergence 1 — how `@dev` was avoided, stated rather than hidden

A path repository normally hands the package a `dev-<branch>` version, which forces
`"drupal/agora_transparency": "@dev"` into the rig's `require` — precisely the shape the dispatch
forbids. Writing it anyway and calling it "only a rig" was the easy road; instead the repository
entry carries `options.versions` pinning the package to `1.0.0`, so the requirement reads `^1.0`.

That is not a cosmetic dodge. `^1.0` is **the exact constraint the end user will get** once the
theme and template are released (D-025=B), so the rig now resolves the same shape the marketplace
will. What it does **not** prove is that a real `1.0.0` exists on `packages.drupal.org` — it does
not, and this rig is not the gate that would show that.

---

## 3. Symlink, not copy — the property both older rigs lack

<!-- The block below is verbatim tool output. Its first line carries the symbolic-link mode
     string that `ls -l` prints, which is a permission bitmap and not a word: D-024(3)
     bucket 4 - scoped in place, not declared globally. It is load-bearing: the leading `l`
     is what distinguishes a symlink from the directory both older rigs got. -->
<!-- cspell:disable -->
```
recipes/agora_transparency -> /mnt/c/Users/.../Documents/projects/agora/   (lrwxrwxrwx)
test -L                     -> true
readlink -f                 -> the working copy, OUTSIDE the rig
lock transport-options      -> array ( 'symlink' => true, 'relative' => false )
```
<!-- cspell:enable -->

**Propagation test, run in both directions of the path:**

1. Created `SYMLINK_PROBE.txt` in the Windows working copy with a unique payload.
2. Read the same payload through `~/agora-export/recipes/agora_transparency/SYMLINK_PROBE.txt`
   (WSL host) **and** through `/var/www/html/recipes/agora_transparency/SYMLINK_PROBE.txt`
   (inside the container). Identical in all three places.
3. Removed the probe from the working copy; it disappeared from the rig.
4. `git status --porcelain` in the working copy: **empty**, before and after.

The install itself is the stronger proof: the recipe Drupal applied in §6 was read through this
symlink — `readlink -f` on the applied path resolves to the working copy.

---

## 4. Why the older rigs got copies — a setting, not a mystery

The dispatch asked to find out before assuming. Found:

```
~/agora-cms/.ddev/config.yaml:15    - COMPOSER_MIRROR_PATH_REPOS=1
~/agora-smoke/.ddev/config.yaml:15  - COMPOSER_MIRROR_PATH_REPOS=1
```

Composer's default for a path repository **is** to symlink. Both rigs opted out explicitly, and
they did not invent it: `.github/workflows/phpunit.yml` in this repository — inherited from the
upstream site-template starter kit — sets it with an intentional comment,
*"Fully copy the site template into the project, rather than symlinking it."*

**That upstream choice is correct for CI and wrong for an export rig**, and the reason is worth
recording because it cuts the other way too. Mirroring routes the package through the same
`export-ignore` machinery that builds a release tarball, so the installed copy has **no `tests/`
directory** — which is why that same workflow has to copy `tests/` back in afterwards, with a
loud comment explaining that any later Composer command silently deletes it again. A symlinked
rig never has that problem: the installed path *is* the repository.

The new rig therefore does not set the variable at all, and states `"symlink": true` explicitly
on the repository entry so the intent survives someone reading only the manifest.

---

## 5. What protects the working copy — and the write it already caught

A symlinked path repository is bidirectional: the rig can write into the working copy. D-032
forbids exactly that for `recipe.yml` and `composer.json`.

**What protects it: the bind mount is `read_only: true`.** Proof:

```
$ ddev exec -- touch $S/WRITE_PROBE.txt
touch: cannot touch '.../WRITE_PROBE.txt': Read-only file system
$ ls $S/WRITE_PROBE.txt
No such file or directory
```

This is not a precaution that has never been exercised. **It fired on the first `composer update`
and aborted it**, which is how the following was found:

> `drupal/site_template_helper`'s Composer plugin (`src/Plugin.php`, lines 64-77) reacts to
> `POST_PACKAGE_INSTALL` for every package of type `drupal-recipe` by writing a **`version` key
> into the installed package's own `composer.json`**:
> ```php
> $path .= DIRECTORY_SEPARATOR . 'composer.json';
> assert(file_exists($path) && is_file($path) && is_writable($path));
> $manipulator->addMainKey('version', $package->getPrettyVersion());
> file_put_contents($path, $manipulator->getContents());
> ```
> In a **mirrored** rig that write lands in a disposable copy and nobody notices. In a
> **symlinked** rig the installed package *is* the working copy, so **Composer itself would edit
> the template's hand-maintained `composer.json` on every install** — a tool writing a file
> D-032=B reserves to hand-maintenance, before any export has even been run.

The read-only mount refused the write; the plugin's `is_writable()` assertion then failed and took
the whole `composer update` down with an `AssertionError`. The rig therefore sets
`zend.assertions = -1` in `.ddev/php/agora-export.ini`, after which the write fails as a warning
and Composer carries on. The only thing lost is a `version` key the plugin uses to build a
translation-download URL, which this rig never uses.

Verified afterwards: `grep -c '"version"' <working copy>/composer.json` → **0**, and
`git status --porcelain` → empty. The template's manifest was never touched.

⚠️ **Two limits on this protection, said plainly.** It stops writes **from the container only** —
a human editing the Windows checkout is unaffected, as intended. And `zend.assertions = -1` is
rig-wide, so Drupal core's own `assert()` calls are off too. That is production-like and correct
*here*; it must not be copied into a rig whose job is to catch assertion failures.

---

## 6. No git clone — the machine still has three, not four

`find ~/agora-export -maxdepth 4 -name .git` → **nothing**. `git -C ~/agora-export remote -v` →
*"not a git repository"*. The path repository points straight at the Windows checkout, so no
clone was needed and none was made.

| clone | remotes |
|---|---|
| the Windows working copy | `drupalcode` (push enabled) · `github` (mirror) |
| `~/agora-cms/source` | `drupalcode`, push URL disabled |
| `~/agora-smoke/source` | `drupalcode`, push URL disabled |
| `~/agora-export` | **not a git repository** |

**CLAUDE.md needs no change**: its published count of three clones is still true.

### Was `/mnt/c` fast enough? Measured, not impressed-upon

| operation | on `/mnt/c` (the working copy) | on ext4 (`~/agora-cms/source`) |
|---|---|---|
| `find -type f \| wc -l` | 593 files in **0.513 s** | 95 files in **0.004 s** |
| read every byte of every file | **0.706 s** | — |

Per-operation `/mnt/c` is roughly two orders of magnitude slower, and the absolute numbers are
still negligible: the recipe apply and the export each touch a few hundred files once. The
baseline export took **12.0 s** end to end reading its base recipe across that mount. No clone is
warranted.

---

## 7. Clean install with no AI key (I-003)

```
ddev drush -y site:install recipes/agora_transparency --account-pass=admin \
  --site-name="Agora Export Rig"
...
[notice] Performed install task: install_recipe_batch
[notice] Performed install task: install_configure_form
[notice] Performed install task: install_finished
[success] Installation complete.
```

| check | result |
|---|---|
| final install task | **`install_finished`** |
| `drush state:get install_task` | **`done`** |
| Drupal version | 11.4.5 |
| Drupal bootstrap | Successful |
| default theme | `blank` (the provisional generated theme, as `recipe.yml` says) |
| enabled extensions | **98** |
| `GET /` | **200** |
| `GET /user/login` | **200** |
| `GET /home` | 301 (front-page alias redirect; matches the T-605 finding) |
| environment variables matching `ANTHROPIC\|OPENAI\|AI_API\|_API_KEY` | **0** |

No AI module is in the closure at all, so this is a stronger statement than "degrades gracefully":
there is nothing present that could have looked for a key.

---

## 8. The baseline — the denominator

```
drush site:export --destination=/var/www/html/export-scratch/baseline \
                  --base=<the working copy> --overwrite
```

Kept at `~/agora-export/export-scratch/baseline`. **Nothing from it is committed.**

| what | files |
|---|---|
| **total in the baseline recipe** | **541** |
| of those, `config/` | **454** |
| of those, `content/` | **13** |
| of those, mirrored in from the checkout by `--base` | **72** |

### Divergence 2 — `--base` copies the entire repository, `specs/` and `.claude/` included

`SiteExporter::copyBaseRecipe()` mirrors everything in the base except `config/`, `content/` and
`recipe.yml`. Pointed at the template checkout — which is what D-032 step 2 prescribes — that
means `CLAUDE.md`, `specs/`, `.claude/`, `tests/`, `.gitlab-ci.yml` and the cspell cache are all
copied into the export destination. Harmless (it is scratch, and the same 72 files appear
identically on both sides of every diff), but it makes **541 a misleading headline number**.

**The honest denominator for "what is not ours" is `config/`: 454 files.**

### Two D-032 claims confirmed in passing

- **`content/` must be discarded wholesale** (step 6). The export wrote **13** content files; the
  working copy's `content/` still holds **1**. The exporter exports the site's live content
  unconditionally.
- **The rule-1 exposure is real, and it printed.** The export's own log:
  *"Cannot determine a version constraint for theme blank ... Falling back to an allow-all (`*`)
  constraint"* followed by *"Package drupal/blank has a **dev** version constraint, which may
  prevent the recipe from being installed into projects that require stable dependencies."*
  That is D-032's `catch`-branch argument, observed rather than read.

---

## 9. The discriminator — does the diff actually work?

D-032 rests on one load-bearing claim: `_core.default_config_hash` is stripped by the exporter, so
a baseline diff is the only remaining way to tell *"this came from an upstream recipe"* from
*"this is ours"*. Both halves were measured.

### 9.1 The marker really is gone

| where | config objects carrying `_core.default_config_hash` |
|---|---|
| the live active config storage | **398** |
| the exported `baseline/config` (454 files) | **0** |

Also **0** files containing a top-level `_core:` key at all. **D-032 is confirmed, by measurement
rather than by reading the source.**

### 9.2 The replacement discriminator works, and it is clean

One throwaway change, exactly as prescribed — the site slogan:

```
drush config:set system.site slogan "AGORA-DISCRIMINATOR-PROBE"
drush site:export --destination=.../after --base=<the working copy> --overwrite
diff -r baseline after
```

**Result: 2 differing files out of 541.**

```
diff -r baseline/composer.json after/composer.json
2c2
<     "name": "drupal/baseline"
---
>     "name": "drupal/after"

diff -r baseline/recipe.yml after/recipe.yml
304c304
<         slogan: ''
---
>         slogan: AGORA-DISCRIMINATOR-PROBE
```

One of those two is the change. **The other is not noise from the site — it is an artefact of the
destination directory's name**: `SiteExporter::writeComposerJson()` sets
`$data['name'] = 'drupal/' . basename(dirname($destination))`. Rather than assert that, it was
measured: a third export of the *same* site into a destination whose basename is also `baseline`
diffs at

> **1 differing file, 1 differing line** — the slogan, and nothing else.

### 9.3 And the noise floor is zero

The strongest available test: revert the slogan, export again into a same-basename destination,
diff against the original baseline.

> **0 differing files out of 541.** `diff -rq` exit 0, empty output.

The export is **byte-stable run to run**. There is no timestamp drift, no UUID churn, no
ordering instability across 541 files. The diff D-032 depends on is readable, and its signal-to-
noise ratio is exactly 1:0.

**The rebuttable finding the dispatch asked for did not materialise, and that is reported as a
measurement, not as a relief:** three exports, two of them of an unchanged site, produced zero
unexplained differences. The one apparent exception was chased down to its line of source and then
eliminated by construction.

### 9.4 The finding that *does* change how T-601 must work

**The slogan never appeared in `config/`.** Both exports' `config/` directories hold **454 files
and are byte-identical**. The change surfaced only in the regenerated `recipe.yml`, as a config
action.

The mechanism is `SiteExporter::isAction()`: any config object shipped as default config by
**core itself, or by the System or User modules**, is exported as a **config action inside
`recipe.yml`**, never as a file under `config/`. `system.site` is one of those.

This matters because D-032 step 4 says *"only files that appear or change are copied into
`config/`"* while step 5 says `recipe.yml` is **never** taken from the export. For the node types,
fields and displays T-601 will create, step 4 works exactly as written — those are new files in
`config/`. But **for any change to core/System/User config, the diff's only artefact lives in a
file the procedure forbids copying.** Those changes must be read out of the `recipe.yml` diff as
*information* and hand-transplanted into the correct `# -- area:` block of the real `recipe.yml`,
which is what step 5 already mandates — but nothing in D-032 says *where the diff will put them*,
and the natural reading of step 4 is that a changed setting shows up under `config/`. It does not.

---

## 10. Divergences from the dispatch, collected

1. **`@dev` was unavoidable as posed; resolved with `options.versions`** rather than by writing an
   unstable constraint. The rig's `require` reads `^1.0`. §2.
2. **`--base` at the template checkout mirrors the whole repository** (72 files including `specs/`
   and `.claude/`) into the export destination. The dispatch's "baseline file count" is therefore
   ambiguous; both numbers are reported, and `config/` = 454 is the meaningful one. §8.
3. **The dispatch treats the older rigs' copies as needing investigation; the cause is a single
   explicit setting** inherited from upstream, and upstream's reason for it is sound for CI. §4.
4. **The discriminator demo returned 2 files, not 1**, on the first run. The second was an
   artefact of the destination name and was eliminated by measurement, not by argument. §9.2.
5. **Two packages were removed from the `drupal/recommended-project` skeleton** —
   `drupal/core-recipe-unpack` (would flatten the template's requirements into the rig's own
   `require` and can dissolve the path-repo entry the rig exists for) and
   `drupal/core-project-message` (noise). Not mentioned by the dispatch; both are deliberate.
6. **A rig-wide `zend.assertions = -1` was required**, for a reason the dispatch could not have
   anticipated: the read-only mount turned a silent upstream write into a hard failure. §5.

## 11. What was deliberately not done

No content model, no node types, no fields, no taxonomy, no roles. Nothing was written into
`config/` — which **still does not exist** in the working copy, exactly as D-032 records. Neither
older rig was started, edited, or read from beyond its `composer.json` and `.ddev/config.yaml`.
Nothing was committed.
