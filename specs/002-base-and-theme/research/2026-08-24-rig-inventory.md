# Rig inventory · `~/agora-cms` and `~/agora-smoke`

**Measured 2026-08-24.** Read-only with respect to both rigs' files. Nothing was edited,
nothing was committed, no remote was touched.

> **Three verdicts.**
>
> **1. The `origin` claim published in CLAUDE.md is TRUE.** Neither rig has a remote named
> `origin`. Both were renamed to `drupalcode` with the push URL disabled. Verified, not assumed.
>
> **2. `recipes/agora_transparency` is a COPY in both rigs, not a symlink.** The dispatch left
> this open; the answer is copy, in both. That has a consequence for the export loop (§4).
>
> **3. D-032's claim about `~/agora-cms` is CONFIRMED at the `composer.json` level and
> REFUTED at the installed-site level** — with one named package that is not there at all.

---

## 1. The `origin` remote — the question CLAUDE.md already answered in public

| Rig | `source/` remote name | Fetch URL | Push URL | `origin` present? |
|---|---|---|---|---|
| `~/agora-cms` | `drupalcode` | `https://git.drupalcode.org/project/agora_transparency.git` | `DISABLED-this-is-a-throwaway-rig-not-a-working-copy` | **NO** |
| `~/agora-smoke` | `drupalcode` | `https://git.drupalcode.org/project/agora_transparency.git` | `DISABLED-this-is-a-throwaway-rig-not-a-working-copy` | **NO** |

**Explicit yes/no, as asked: `~/agora-cms` — no. `~/agora-smoke` — no.**

The published claim holds. A bare `git push` from either rig fails on an unresolvable push URL
rather than reaching a real remote, which is the intended failure mode.

Both `source/` directories are on branch `1.x` and both are **behind this working copy**:

| Rig | `source/` HEAD | |
|---|---|---|
| `~/agora-cms` | `5556bb3` | 4 commits behind |
| `~/agora-smoke` | `6aba924` | further behind |
| working copy | `6e5ca44` | — |

⚠️ Staleness of `source/` did **not** affect this session's measurements: the applied recipe
was checked byte-for-byte against today's file (§4) and is identical.

---

## 2. Symlink or copy

| Rig | `recipes/agora_transparency` | `readlink -f` | Files inside |
|---|---|---|---|
<!-- The two rows below are verbatim `readlink -f` output. The token cspell sees is this
     machine's OS account name inside an absolute path, not a word: D-024(3), bucket 4 -
     scoped in place, not declared globally. The path is load-bearing evidence: `readlink -f`
     returning the argument unchanged is what proves the entry is not a symlink. -->
<!-- cspell:disable -->
| `~/agora-cms` | **real directory (copy)** | `/home/andresmrubio/agora-cms/recipes/agora_transparency` | **11** |
| `~/agora-smoke` | **real directory (copy)** | `/home/andresmrubio/agora-smoke/recipes/agora_transparency` | **27** |
<!-- cspell:enable -->

`test -L` is false in both; `stat -c %F` reports `directory` in both. Composer installed these
as copies, not as symlinked path repositories.

The file-count gap is real, not noise: `~/agora-smoke`'s copy carries a `tests/` directory
that `~/agora-cms`'s does not, and its `README.md` is a different size (14470 vs 18889 bytes) —
the two copies were taken at different commits.

---

## 3. `require` blocks

### `~/agora-cms` — 19 entries

```
composer/composer                        ^2.9.8
composer/installers                      ^2.3
drupal/agora_transparency                @dev
drupal/byte                              ^1
drupal/core-composer-scaffold            ^11.3.10
drupal/core-project-message              ^11.3.10
drupal/core-recipe-unpack                ^11.3.10
drupal/core-recommended                  ^11.3.10
drupal/core-vendor-hardening             ^11.3.10
drupal/drupal_cms_accessibility_tools    ^2
drupal/drupal_cms_ai                     ^2
drupal/drupal_cms_forms                  ^2
drupal/drupal_cms_google_analytics       ^2
drupal/drupal_cms_installer              ^2
drupal/drupal_cms_seo_tools              ^2
drupal/drupal_cms_site_template_base     ^1@dev
drupal/drupal_cms_starter                ^2
drupal/haven                             ^1
drupal/webform                           @beta
```

`require-dev`: `drush/drush ^13.7`. `minimum-stability: stable`, `prefer-stable: true`.
`repositories`: a `path` repository named `source` plus `packages.drupal.org/8`.

### `~/agora-smoke` — 48 entries

The unpacked Drupal CMS dependency set (`core-recipe-unpack` has flattened the
`drupal_cms_*` recipes into individual module requirements): `drupal/canvas ^1.2`,
`drupal/eca ^3.0.8`, `drupal/gin ^5`, `drupal/pathauto ^1.13`, `drupal/site_template_helper
^1.0.3`, `drupal/webform` **absent**, and so on for 48 entries.
`require-dev`: `drupal/core-dev ^11.4`. `minimum-stability: stable`, `prefer-stable: true`.

⚠️ **`~/agora-smoke` does NOT require `drupal/agora_transparency`** — yet
`recipes/agora_transparency` exists there as a 27-file copy. That directory was not placed by
Composer resolving a dependency. Whatever put it there, it is unmanaged, and `composer install`
in that rig would not restore or update it.

---

## 4. D-032 — confirmed, refuted, and corrected

The dispatch reports `~/agora-cms`'s `require` as carrying `drupal/byte`, `drupal/haven`,
`drupal_cms_ai`, `drupal_cms_forms`, `drupal_cms_search` "and others", none of which is in
Ágora's SBOM. Read from the file:

| Package named in the dispatch | In `~/agora-cms` `require`? |
|---|---|
| `drupal/byte` | **YES** — `^1` |
| `drupal/haven` | **YES** — `^1` |
| `drupal/drupal_cms_ai` | **YES** — `^2` |
| `drupal/drupal_cms_forms` | **YES** — `^2` |
| `drupal/drupal_cms_search` | **NO — not present** |

Four of five confirmed. **`drupal_cms_search` is not in the file** and `search_api` is not
installed either; the claim should be corrected wherever it is repeated. The "and others" is
understated rather than overstated — `drupal_cms_accessibility_tools`,
`drupal_cms_google_analytics`, `drupal_cms_seo_tools`, `drupal_cms_starter`,
`drupal_cms_site_template_base` and `webform` are also present and also outside Ágora's SBOM.

### The sharper finding: two unstable constraints

```
drupal/webform                        @beta
drupal/drupal_cms_site_template_base  ^1@dev
```

`minimum-stability` is `stable`, but per-package stability flags override it. **This rig is
built on two non-stable requirements.** Non-negotiable rule 1 forbids exactly this shape in the
template. `tests/bin/no-unstable-deps` does not fire on it — correctly, since its scope is the
repository's own `composer.json`, and a rig is not the package. But it means any tooling that
derives the template's `require` from this rig would import an `@beta` and an `@dev` constraint
straight through the gate's blind spot.

### But the pollution does not reach the installed site

| Suspect module | Enabled on `agora-cms`? | Enabled on `agora-smoke`? |
|---|---|---|
| `byte` | NO | NO |
| `haven` | NO | NO |
| `drupal_cms_ai` / `ai` | NO | NO |
| `drupal_cms_forms` / `webform` | NO | NO |
| `drupal_cms_search` / `search_api` | NO | NO |
| `google_analytics` | NO | NO |

**Installed extension counts**

| Rig | Enabled total | Modules (core / non-core) | Themes |
|---|---|---|---|
| `~/agora-cms` | **98** | 92 (41 / 51) | **6** — `blank`, `canvas_stark`, `claro`, `easy_email_theme`, `gin`, `stark` |
| `~/agora-smoke` | **102** | 95 (44 / 51) | **7** — the same six plus `olivero` |

The two installed sets differ by exactly **three modules** — `block_content`, `config`, `help` —
plus `olivero`, all of which come from `~/agora-smoke` having been installed from the
`standard` profile rather than from Ágora's recipe. **Non-core module sets are identical: 51
and 51, same names.**

### So: can either rig be the export rig?

**Not as they stand — but the reason is not the one D-032 gives.** The installed config
surface of `~/agora-cms` is clean; a `drush site:export` of *config* from it would not pull in
`byte`, `haven` or the AI modules, because they are not installed. The disqualifying facts are
different, and there are three:

1. **`composer.json` `require` carries `@beta` and `@dev` constraints.** D-032=B rules that
   `site:export` writes a whole new recipe, `composer.json` included. Exporting from this rig
   puts an unstable constraint into the template's own manifest.
2. **`recipes/agora_transparency` is a copy, not a symlink** (§2). Anything the export loop
   writes lands in the rig's *copy* and never reaches the working copy. A symlinked path
   repository would have made the export loop a one-step operation; a copy makes it a
   two-step one with a manual, forgettable, silent-on-failure sync in the middle.
3. **`~/agora-smoke` does not even require the package** (§3), so its copy is unmanaged.

Fact 2 is the one that matters most operationally, and it applies to **both** rigs.

---

## 5. Divergences between the dispatch and disk

1. **`drupal_cms_search` is not in `~/agora-cms`'s `require`.** Four of the five named packages
   are; that one is not. Disk wins.
2. **Both rigs' `recipes/agora_transparency` is a copy, not a symlink** — the dispatch asked
   which, and the answer has a consequence the export loop must absorb (§4.2).
3. **`~/agora-smoke` does not require `drupal/agora_transparency`** while holding a copy of it.
4. **The rigs' `recipe.yml` is byte-identical to today's**, despite `source/` being several
   commits behind: 187 lines each side, `diff --strip-trailing-cr` clean, the 187-byte size gap
   is CRLF (Windows working copy) vs LF (rig). The rigs are stale in `source/` but **current in
   the thing that was actually measured**. This was checked rather than assumed, because a
   stale recipe would have invalidated the T-602 and T-605 baselines.
5. **The session-start git snapshot was stale** — it reported HEAD `667a280` with
   `specs/002-base-and-theme/tasks.md` modified. On disk, HEAD is `6e5ca44`, the working tree
   is clean, and `667a280` is an ancestor. Nothing was lost; the snapshot was simply older than
   the repository.
