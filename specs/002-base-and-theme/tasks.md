# Unit 002 · tasks (append-only)

Budget: 34 tasks (plan.md §3c). Crossing it costs a signed rider.

---

## Wave 5 · The theme repository exists and its gate is provably real

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-501 | · | Rename the four Spanish unit directories: `002-base-tema` → `002-base-and-theme`, `003-contenido-demo` → `003-demo-content`, `005-ia-governance` → `005-ai-and-governance`, `007-publicacion` → `007-publication`; update the heading inside each `README.md` and every cross-reference | `grep -rn "base-tema\|contenido-demo\|ia-governance\|007-publicacion" .` over tracked files returns **0 lines**; `git log --follow` resolves on each moved README | D-029 |
| T-502 | H | Scaffold `agora_theme`: `agora_theme.info.yml` (`type: theme`, `core_version_requirement: ^11`, `base theme: false`, regions `header`/`content`/`footer` at minimum), `composer.json` (`drupal/agora_theme`, `type: drupal-theme`, `license: GPL-2.0-or-later`), `LICENSE.txt` (GPL-2.0-or-later, byte-identical to the template's), `README.md`, `.gitignore`, `.gitattributes` | `composer validate --strict` exit 0; the info file parses; **exactly one** `*.info.yml` at the repository root | D-025 |
| T-503 | H | Read `canvas/docs/components.md` and `docs/shape-matching.md` at 1.10.1 and record, in `research/`, whether a front-end theme has any Canvas-side obligation beyond regions | A dated note stating either "no obligation found, here is what was read" or a named list; the note cites file paths and line ranges, not summaries | T-502 |
| T-504 | H | `.gitlab-ci.yml` for the theme: upstream `include:` byte-identical to the template's, plus `_ALL_VALIDATE_ALLOW_FAILURE: '0'`, `_PHPUNIT_EXTRA: '--fail-on-empty-test-suite'`, `_CSPELL_EXTRA`, `_CSPELL_SHOW_PROGRESS: '1'`, `_PHPCS_EXTRA: '-p'` | `tests/bin/no-blind-phpunit` (theme copy) exits 0 over the file; the `include:` block diffs clean against `gitlab_templates` guidance; **no** `allow_failure` and **no** `CI_ALLOW_DEV` anywhere in the file | T-502 |
| T-505 | · | **Measure** whether a pushed branch is enough for drupal.org packaging: run the exact command below against **both** projects and record the HTTP codes and the date | Recorded output of `curl -s -o /dev/null -w "%{http_code}" "https://packages.drupal.org/files/packages/8/p2/drupal/agora_transparency~dev.json"` and the same for `agora_theme~dev`. **Any** result is a pass; silence is a fail | — |
| T-506 | · | **[andres]** Push `agora_theme` `1.x` to drupalcode and, if T-505 shows a branch is not enough, create the `1.x-dev` release on the project page | `git ls-remote --symref` shows HEAD pinned to `refs/heads/1.x`; the API reports `1` branch; the T-505 command returns **200** | T-502, T-504, T-505 |
| T-507 | H | First observed pipeline: read the **job list** from the API, not the UI or the badge, and write the observed table into the theme's `README.md` in the same commit that makes it true | `jobs >= 5`; every `status == "success"`; every `allow_failure == false`; the table names each job, its stage and its `allow_failure`, with the pipeline id and commit sha | T-506 |
| T-508 | H | Theme `tests/bin/`: the named subset plus `shared-invariants.manifest` (sha256 per shared script + the `agora_transparency` sha it was taken from), and `agora-invariants` as a local blocking job | The runner prints `N checks — 0 failures` with `N` stated; a deliberately edited byte in one shared copy makes it print `N checks — 1 failures` and exit non-zero | D-028, T-504 |
| T-509 | H | **The unit's centrepiece.** `tests/src/Nightwatch/Accessibility/axe.js`: `'@tags': ['agora_theme']`, `drupalInstall`, navigate to at least one theme-rendered page, `browser.axeInject().axeRun('html', {})` | The `nightwatch` job is **in the observed job list** with `allow_failure: false`; its log shows **`N` tests executed, `N >= 1`**; the axe result names the **number of rules run** and the number of violations. `0 tests` is a **failure** | T-507, D-027 |
| T-510 | H | **Dirty case for T-509** (D-019 rider e): prove on a throwaway branch that a deliberately inaccessible fragment — an `<img>` with no `alt` — turns the `nightwatch` job **red** | A pipeline id and job id where `nightwatch` is `failed` and the log names the axe rule that fired. The branch is then deleted; the evidence is the pipeline record, not the branch | T-509 |
| T-511 | T | Promote the install smoke: add `OPT_IN_TEST_DRUPAL_CMS: '1'` and `_AUTORUN_DRUPAL_CMS: 'all'` to the template's `.gitlab-ci.yml`, with a comment citing `include.drupalci.main.yml:487-496` | The next pipeline's job list contains `Drupal CMS` with `status: success` and `allow_failure: false`; **`jobs >= 9`**; the CLAUDE.md gate-A table is updated **in the same commit** (the derived-list prohibition) | Amendment to D-020 |
| T-512 | · | Record **I-051**: the fourth rung — *defined upstream · materialised in this pipeline · collected by the harness · actually executed* — with the `nightwatch.conf.js` glob and the `DRUPAL_PROJECT_FOLDER` branch as the two halves of the evidence | The idiom is in `IDIOMS.md`, cites both file:line references, and states the rule: **read where the harness looks, not only where CI puts you** | T-509 |

---

## Wave 6 · Content model ‖ visual identity (two lanes, disjoint by repository)

### Lane A — template repository only

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-601 | T | Content model: node types, fields and taxonomy vocabularies per the signed D-026, built in the WSL rig and exported with `drush site:export` into `config/` | A kernel test asserts **every** type, field and vocabulary by machine name and exits with a stated assertion count; `RequirementsTest` still green (no `_core`, no stray `uuid`) | **D-026** |
| T-602 | T | Roles and permissions: editor, reviewer, publisher, least privilege | A kernel test asserts that **no role except `administrator`** holds any permission matching `^administer `, and prints the number of roles and permissions inspected | T-601 |
| T-603 | T | Base views: document library with facets, listing by type, search box | A functional test asserts each view's route returns **200** and that the rendered listing contains a `<table>` with `<th scope>` on every header cell, or an equivalent semantic list — count of cells asserted | T-601 |
| T-604 | T | Canvas component enable/disable review for the **front-end** (the inherited list is admin-only) | `recipe.yml`'s `config.actions` block names each component with a one-line reason; a test asserts the recipe applies with **0** unresolved `?`-optional keys that were expected to exist | T-601 |
| T-605 | T | The `page.front` gap: `recipe.yml` declares `/home`, the installed site reports `/page/1` | A functional assertion that `/home` returns 200 **and** that `system.site` `page.front` on the installed site equals the declared value — or, if it legitimately cannot, a dated note explaining why and an amended declaration. Silence is not an outcome | T-601 |

### Lane B — theme repository only

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-606 | H | Colour tokens as CSS custom properties: text, background, link, focus, border, status | Every token defined in exactly one file; the file is machine-readable enough for T-608 to parse without a bespoke grammar | T-507 |
| T-607 | H | Typography: one OFL face, self-hosted `woff2`, `OFL.txt` shipped beside it, licence-manifest line, type scale, `prefers-reduced-motion`, visible focus | The `OFL.txt` is present and its first line names the copyright holder; the manifest line names font, version, licence and source URL; **no** external font URL anywhere — `grep -rn "fonts.googleapis\|fonts.gstatic\|@import url(http" .` returns **0 lines** | **D-030** |
| T-608 | H | `tests/bin/contrast-check` + its dirty case: parse the token file, compute WCAG contrast for every declared foreground/background pair | Prints `N pairs checked — 0 below threshold` with `N` stated (4.5:1 body, 3:1 large text and non-text). A deliberately dimmed token makes it print `N pairs checked — 1 below threshold` and exit non-zero | T-606, T-508 |
| T-609 | H | Twig templates: page, node, field, **table**, form element, pager, menu | `twig-cs-fixer` **materialises** in the observed job list with `allow_failure: false` and passes; the table template emits `<th scope="col">`/`<th scope="row">` and a `<caption>`, asserted by the axe test's DOM checks | T-606 |
| T-610 | H | Base stylesheet built from the tokens; no generic CSS framework (D-014 rider f) | `stylelint` **materialises** in the job list with `allow_failure: false` and passes; the CSS contains **0** hard-coded colour literals outside the token file — asserted by an invariant with its dirty case | T-606, T-609 |
| T-611 | H | Extend T-509: axe over **every** page the theme renders in its fixtures, with the page count stated | The `nightwatch` log states **`P` pages scanned, `P >= 3`**, `R` axe rules run, **0** violations. `P` is written into the theme README's gate table in the same commit | T-609, T-610 |

---

## Wave 7 · The atomic swap

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-701 | · | **[andres]** Cut `agora_theme` **1.0.0 stable** on drupal.org | `curl -s "https://updates.drupal.org/release-history/agora_theme/current"` lists a `<version>1.0.0</version>` with `<status>published</status>`; `packages.drupal.org/files/packages/8/p2/drupal/agora_theme.json` returns 200 containing `1.0.0` | Wave 6 gate green, D-025 |
| T-702 | T | **The atomic commit.** Delete `extra.drupal-site-template` from `composer.json`; add `"drupal/agora_theme": "^1.0"` to `require`; replace `blank` with `agora_theme` in `recipe.yml` `install:`; set `system.theme` `default: 'agora_theme'`. One commit, nothing else in it | `grep -c "drupal-site-template" composer.json` → **0**; `grep -c "blank" recipe.yml` → **0**; `RequirementsTest` green (still **0** `*.info.yml`, constraint `^1.0` does not match the pin regex); `composer validate --strict` exit 0 | T-701 |
| T-703 | T | Discharge the wave-1 rider **by name**: revert the adjusted `no-boilerplate` check, add `extra.drupal-site-template` to the deny list, prove the deny list fires on the pre-swap file | The deny list grows from 8 to 9 entries; running it against the `HEAD~1` `composer.json` exits non-zero and names the key; running it against `HEAD` exits 0. The rider text in `DECISIONS.md` gains a closure note citing T-703 | T-702 |
| T-704 | T | Discharge **T-106** (deferred from unit 001 against D-014=B) and record its closure in the blockers table | The blockers table row for T-106 reads ✅ CLOSED with the T-702 commit sha; `tests/bin/cited-tasks-exist` exits 0 | T-702 |
| T-705 | T | Prove the swap on a **clean** install: rebuild `~/agora-smoke` from scratch (rebuild, not `git pull` — CLAUDE.md), apply the template, confirm `agora_theme` is the default theme and the front page renders | `drush config:get system.theme default` → `agora_theme`; front page HTTP **200**; the theme's stylesheet is in the served HTML; **and** the `Drupal CMS` job on drupalcode shows the same, `allow_failure: false` | T-702, T-511 |
| T-706 | T | `sbom-check` against a first-party dependency: `drupal/agora_theme` has no third-party security-coverage answer until 2026-09-03 | The invariant either reports the theme as covered (if the date has passed and coverage was granted) or **names it explicitly as a first-party exception with its eligibility date**, and its D-NNN line exists in `DECISIONS.md`. A silent pass is a **failure** | T-702 |

---

## Wave 8 · Closure, carried debt and the verdict

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-801 | T | **T-402** (carried): route assertions in the functional tests | Each key route asserted for status **and** for a rendered marker; the test prints the number of routes asserted; the number is quoted in the closure report | Wave 7 |
| T-802 | · | **T-208** (carried, redefined): the digest-pinned local gate container | The container definition names an image by **sha256 digest**, not a tag; `tests/bin/doctor` reports the digest; running the wave runners inside it reproduces the host's counts **exactly**, both numbers quoted | D-019 |
| T-803 | · | **T-317** (carried): toolchain floor measured on the macOS host | The floor (grep flavour, `-F` behaviour, line endings, `sha256sum` availability) recorded per host; **either** macOS is certified with its counts **or** it is recorded as NOT CERTIFIED with the named blocking difference. An unmeasured host is a **failure** | — |
| T-804 | H | **T-206(b)** (carried): Playwright visual regression on GitHub Actions, non-blocking per D-009 | Baselines committed; the workflow runs and reports **`S` screenshots compared**, `S` stated. Non-blocking, but **it may never lie** (D-020) | Wave 6 |
| T-805 | · | Both repositories' gate tables refreshed **from observation** in the same commit that makes them true, and CLAUDE.md's gate-A block updated | Each table names its pipeline id, ref, commit sha and the API path it was read from. No derived lists | T-801…T-804 |
| T-806 | · | `orquestador` audit and gate verdict for unit 002 | No open 🔴; every green quoted with its denominator (I-045); the **task count reported against the 34 budget**; every 🟡 carries an owner and a target unit | T-805 |
| T-807 | · | Closure report and HOLD for [andres]'s gate B signature | The report states, for each of the two repositories: job list, test counts, assertion counts, pages scanned by axe, files scanned by each invariant. Any missing number is stated as missing, not omitted | T-806 |

---

## Carried debt, by existing number

| Existing task | Carried as | State |
|---|---|---|
| T-106 (theme approach, deferred unit 001) | T-704 | owned, wave 7 |
| T-402 (route assertions, deferred unit 001) | T-801 | owned, wave 8 |
| T-208 (gate container, redefined by D-019) | T-802 | owned, wave 8 |
| T-206(b) (axe + visual regression) | T-509/T-611 (axe) + T-804 (visual) | **split** — axe is wave 5/6 and blocking; visual is wave 8 and informative |
| T-317 (toolchain floor; macOS NOT CERTIFIED) | T-803 | owned, wave 8 |
| Wave-1 rider (`blank` + `extra` adjusted check) | T-703 | owned, wave 7 |
| `page.front` declared `/home` vs landed `/page/1` | T-605 | owned, wave 6 |

**Count: 30 tasks. Budget 34. Headroom 4.**
```

---
