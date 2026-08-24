# Unit 002 · State of the art, measured 2026-08-24

> Expires per I-001. Every claim below carries its URL and the date it was read.
> Where a thing was not measured, it says NOT MEASURED and no inference is drawn from it.
> Method: `curl` against `git.drupalcode.org` (raw + API v4), `updates.drupal.org`,
> `packages.drupal.org`, `registry.npmjs.org`. `www.drupal.org` answers only via
> `api-d7`, which now returns redirect stubs to `git.drupalcode.org/-/work_items/<id>`.

---

## R-1 · What a Canvas-compatible theme requires today

**Answer: an `.info.yml`. Nothing else is a requirement.**

Verified three ways, all on 2026-08-24:

1. **The official generator produces an info file and nothing else.**
   `drupal/site_template_helper` 1.0.3, `src/Plugin.php` lines 82-137
   (https://git.drupalcode.org/project/site_template_helper/-/raw/1.0.3/src/Plugin.php).
   With `from: false` — which is exactly what Ágora's `composer.json` sets today — the
   plugin runs `ensureDirectoryExists()` and writes a single YAML file. Verbatim, the
   defaults it merges in:

       $info += [
         'type' => 'theme',
         'core_version_requirement' => "^$drupal_version",
         'base theme' => FALSE,
         'version' => '1.0.0',
       ];

   Upstream commit `0e4f0327`, 2026-03-26, is titled *"Generate the blank theme with an
   info file only."* The `blank` theme that Ágora ships today is an info file with three
   regions and one `libraries-override`. It is enough for Canvas.

2. **Canvas's own bundled theme is not a front-end model.**
   `drupal/canvas` 1.10.1 ships exactly one theme, `themes/canvas_stark`, and its
   `.info.yml` says `hidden: true` and *"A theme for rendering forms in the Drupal Canvas
   UI."* It is infrastructure for the editing UI, **not** a reference front-end theme.
   Do not copy it. (https://git.drupalcode.org/project/canvas/-/raw/1.10.1/themes/canvas_stark/canvas_stark.info.yml)

3. **Canvas's only stated theme-side dependency is block plugins.**
   `canvas.info.yml` 1.10.1, verbatim comment:
   *"Canvas's 'page template' functionality is an integral part of Canvas. To allow that
   to function, this (currently) needs >=1 main content, title and messages block
   plugins."* Those come from `drupal:block`, which Canvas requires itself — not from
   the theme. The theme's obligation is therefore to **declare regions those blocks can
   be placed into**. `blank` declares `header`, `content`, `footer`.

**Canvas release status:** 1.10.1, published 2026-08-12, `security covered="1"`,
`core_compatibility ^11.3`. Supported branches 1.8, 1.9, 1.10. 35 releases.
(https://updates.drupal.org/release-history/canvas/current)

**Consequence for the plan:** "Canvas-compatible" is not a certification to chase. The
theme's real work is the part nobody else does — AA tokens, self-hosted OFL typography,
accessible tables and forms, visible focus. Which is precisely D-014 rider (f).

**NOT MEASURED:** whether Canvas ships Twig-side hooks a theme *should* implement to make
its components render better (`docs/components.md`, `docs/shape-matching.md` exist and
were not read). Resolve inside T-503, not by assumption.

---

## R-2 · Can the site template depend on a theme with no stable release?

Three separate questions were conflated in the dispatch. Separated:

### (a) Does anything upstream *forbid* it?

**No.** Read at source:

- `RequirementsTest.php` (the enforcer, on disk at
  `tests/src/Kernel/RequirementsTest.php`) checks the constraint with one heuristic:
  `assertDoesNotMatchRegularExpression('/^v?[0-9]+\./i', $constraint)`.
  `"^1.0@dev"` starts with `^`. `"dev-1.x"` starts with `d`. **Both pass.** Only a literal
  pin like `"1.0.x-dev"` fails.
- The RFC (*Site Templates*, drupal_cms wiki, read 2026-08-24 via
  `/api/v4/projects/project%2Fdrupal_cms/wikis/Architecture-Decision-Records%2FSite-Templates`)
  says, verbatim: *"They MAY depend on a theme (or multiple themes) as design systems"*
  and *"A site template MUST NOT pin dependencies"*. It says **nothing** about release
  stability.
- `GET-STARTED.md` on the starter kit's `1.x`
  (https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/GET-STARTED.md)
  says only: *"use version constraint operators like `^1` (recommended) or `~2.1.4`."*

**So the stable-only prohibition is OURS** (non-negotiable rule 1) plus the marketplace
requirement recorded on 2026-08-20 — which is itself **still unverified** (D-012 took the
Community route, so it does not block). It is a policy, not a mechanism. Worth knowing:
we cannot blame upstream for the constraint, and we cannot be rescued by it either.

### (b) `CI_ALLOW_DEV` is a red herring here

Its docblock is almost embarrassingly on-the-nose: *"this is useful for testing the site
template against the latest commit of a bespoke theme to which it is strongly coupled."*
That is literally our situation. **But it only skips the pin regex**, and a `^1.0@dev`
constraint never trips that regex. So `CI_ALLOW_DEV` buys nothing we need, and CLAUDE.md
already makes defining it an automatic 🔴. **Settled: we never touch it.**

### (c) The thing that actually blocks it — measured today

    curl -s -o - "https://packages.drupal.org/files/packages/8/p2/drupal/canvas~dev.json"
      → HTTP 200, versions: ["dev-1.x"]
    curl -s -o - "https://packages.drupal.org/files/packages/8/p2/drupal/agora_transparency.json"
      → HTTP 404
    curl -s -o - "https://packages.drupal.org/files/packages/8/p2/drupal/agora_transparency~dev.json"
      → HTTP 404
    curl -s "https://updates.drupal.org/release-history/agora_transparency/current"
      → <error>No release history was found for the requested project</error>

`drupal/agora_transparency` has had a `1.x` branch pushed to drupalcode since 2026-08-23
(T-217) and **is still not in Composer's index at all**, not even as `dev-1.x`.
`drupal/agora_theme`, created 2026-08-24 with an empty repository, likewise.

**Therefore: `composer require drupal/agora_theme` cannot resolve, at any stability,
until a release exists on the project's drupal.org page.** A pushed branch is not enough.
Two candidate causes, neither confirmed: the project has no release node yet, or
drupal.org packaging has not run for a project this young. **NOT MEASURED — which of
the two.** Re-measure with the exact command above; the day `agora_transparency~dev.json`
returns 200, we know a branch alone suffices and the delay was packaging latency.

### (d) Can the template's CI test against an unreleased theme?

Technically yes, three ways, and all three are the false-green species this project keeps
catching:

| Route | Why it is wrong |
|---|---|
| `CI_ALLOW_DEV` | Automatic 🔴 per CLAUDE.md; and buys nothing (see (b)) |
| `repositories: [{type: vcs, …}]` in `composer.json` | Ships to the end user. An exotic pin by another name. 🔴 |
| `_COMPOSER_EXTRA` injecting a path repo at build time only | Clean of the package, but the gate would then prove an installation **the end user cannot perform**. That is exactly I-048. |

**The honest answer is: no, and the fix is sequencing, not tooling.** The theme reaches a
stable `1.0.0` release *before* the template ever names it. See D-025.

---

## R-3 · Where the axe-in-Nightwatch pattern actually lives — and the finding that reshapes the unit

### (a) Drupal core has no axe integration. Nightwatch does.

Measured 2026-08-24 against `drupal/drupal` `11.x`:

- `core/tests/Drupal/Nightwatch/Commands/` → 12 files, all `drupal*.js`. **No `axeInject`,
  no `axeRun`.**
- `core/tests/Drupal/Nightwatch/Assertions/` → `deprecationErrorExists.js`,
  `elementCount.js`, `noDeprecationErrors.js`. **No `isAccessible`.**
- `core/package.json` contains **zero** occurrences of `axe`.

So there is no "core pattern" to copy. What exists is one level down:

    registry.npmjs.org/nightwatch/3.12.3 → dependencies include
      "nightwatch-axe-verbose": "^2.3.0"

`core/package.json` declares `"nightwatch": "^3.12.3"`. Nightwatch 3 bundles
`nightwatch-axe-verbose`, which provides `browser.axeInject()` and `browser.axeRun()` and
carries `axe-core` with it. **The capability arrives free with core's `yarn` install and
needs no new dependency, no new SBOM line and no pnpm/yarn conflict.**

### (b) The gating ladder for the Nightwatch job — read at source, 2026-08-24

`gitlab_templates` `main`, `includes/include.drupalci.main.yml:1491-1496`:

    .nightwatch-base:
      extends: .testing-job-base
      rules:
        - *opt-in-current-rule          # OPT_IN_TEST_CURRENT != "1" → never. Default IS "1". Passes.
        - *skip-nightwatch-rule         # SKIP_NIGHTWATCH == "1" → never. Default "0". Passes.
        - *nightwatch-tests-exist-rule  # exists: tests/src/Nightwatch/**/*.js

`.testing-job-base` provides services `database`, `chrome`, `chrome-legacy`.
`.nightwatch-base` sets **no `allow_failure`** → with `_ALL_VALIDATE_ALLOW_FAILURE` not
applying to the `test` stage, the job is **blocking by default**. Good news.

### (c) 🔴 The finding: a *recipe* project's Nightwatch tests can never be collected

Two facts that only bite when put together.

**Fact 1** — `include.drupalci.main.yml:118-127`, verbatim:

    [[ $DRUPAL_RECIPES_PATH == "" ]] && export DRUPAL_RECIPES_PATH="recipes"
    if [[ $PROJECT_TYPE == "recipe" ]]; then
      export DRUPAL_PROJECT_FOLDER=$CI_PROJECT_DIR/$DRUPAL_RECIPES_PATH/$PROJECT_NAME
    else
      export DRUPAL_PROJECT_FOLDER=$CI_PROJECT_DIR/$_WEB_ROOT/$DRUPAL_PROJECTS_PATH/$PROJECT_NAME
    fi

A recipe lands at `$CI_PROJECT_DIR/recipes/<name>` — a **sibling** of the docroot.
A theme lands at `$CI_PROJECT_DIR/web/themes/custom/<name>` — **inside** it.
(`PROJECT_TYPE` is read from the top-level `*.info.yml`'s `type:` key, line 91; for a
recipe it is hard-set at line 62.)

**Fact 2** — `core/tests/Drupal/Nightwatch/nightwatch.conf.js` lines 16-21, verbatim:

    globSync('**/tests/**/Nightwatch/**/*.js', {
      cwd: path.resolve(process.cwd(), `../${searchDirectory}`),
      …
    })

The job `cd`s into `$_WEB_ROOT/core`, so `cwd` resolves to **the docroot**.
`DRUPAL_NIGHTWATCH_SEARCH_DIRECTORY` is set to `''` by `.nightwatch-base`.

**Put together:** in `agora_transparency`, `tests/src/Nightwatch/foo.js` would make the
job **materialise** (the exists-rule is evaluated against `$CI_PROJECT_DIR`) — and then
Nightwatch's own glob, rooted at the docroot, would **find zero test files**, because the
recipe lives outside the docroot. A Nightwatch run with an empty `src_folders` is the
`--fail-on-empty-test-suite` disaster with no `--fail-on-empty-test-suite` available.

**This extends I-050 by a fourth rung.** The ladder was *defined upstream · materialised
in this pipeline · actually executed*. There is a rung between the last two:
**collected** — the harness has to be able to *find* the test, and where the CI puts your
project is not where the harness looks.

**In `agora_theme` the same file is at `web/themes/custom/agora_theme/tests/src/Nightwatch/`,
which the glob matches.** The theme repository is the only one of the two where a
Nightwatch accessibility test can run at all.

**One more rung after that:** the script is
`yarn test:nightwatch --tag=$PROJECT_NAME` (line 1539). Tests without
`'@tags': ['agora_theme']` are filtered out and the job goes green having run nothing.

**NOT MEASURED:** whether pointing `DRUPAL_RECIPES_PATH` inside the docroot would make the
template repo's Nightwatch tests collectable. It would also move where Composer installs
the recipe, so it is not a free variable. Do not attempt it in unit 002.

---

## R-4 · The install smoke can become blocking on drupalcode — measured

The `Drupal CMS` job that T-228's canary ran manually is not condemned to be manual.
`include.drupalci.main.yml:858-864`:

    Drupal CMS:
      rules:
        - *opt-in-drupal-cms          # OPT_IN_TEST_DRUPAL_CMS != "1" → never.  Default '0'.
        - *autorun-drupal-cms-rule    # if _AUTORUN_DRUPAL_CMS matches → when: always
        - *make-job-manual            # when: manual, allow_failure: true

`.autorun-drupal-cms-rule` (lines 487-492) ends `when: always` and **declares no
`allow_failure`** → `false`. `_AUTORUN_DRUPAL_CMS` defaults to `'none'` with options
`none | push | push-and-mr | push-and-schedule | all`.

**Therefore:** setting `OPT_IN_TEST_DRUPAL_CMS: '1'` **and** `_AUTORUN_DRUPAL_CMS: 'all'`
promotes the canary into an automatic, **blocking, ninth job**. That closes the gap D-020
records — the clean-install smoke running only on the informative GitHub surface.

The job's script (lines 897-916) does `composer create-project drupal/cms $_CMS_ROOT
$_DRUPAL_CMS_TAG`, then `composer config minimum-stability dev`, then adds the checkout as
a **path repository** and `composer require`s it. Note the consequence: **that job will
try to resolve `drupal/agora_theme` from packages.drupal.org.** It is the job that will
fail loudest if the theme is not released. Which is a feature.

`drupal/cms` current stable is **2.1.3** (updates.drupal.org, 2026-08-24) — unchanged
since the 2026-08-20 research.

---

## R-5 · The OFL typography question

Three candidates, licence files read at source on 2026-08-24. All three are **SIL Open
Font License 1.1**, all three are self-hostable, none requires a CDN.

| Font | Licence file read | Notes |
|---|---|---|
| **Public Sans** | `raw.githubusercontent.com/uswds/public-sans/develop/LICENSE.md` | A fork of Libre Franklin by the US GSA. Verbatim: *"users of this Modified Version (Public Sans) should use Public Sans according to the terms of the SIL Open Font License, Version 1.1."* GSA's own modifications are CC0. **No Reserved Font Name.** Designed for government interfaces. |
| **Atkinson Hyperlegible** | `raw.githubusercontent.com/googlefonts/atkinson-hyperlegible/main/OFL.txt` | *"Copyright 2020 Braille Institute of America, Inc."* OFL 1.1. Designed for low-vision legibility — the strongest a11y story available. |
| **Source Sans 3** | `raw.githubusercontent.com/adobe-fonts/source-sans/release/LICENSE.md` | OFL 1.1, **Reserved Font Name `'Source'`** — we may redistribute unmodified, but may not ship a modified build under that name. An extra rule to remember forever. |

**What is NOT MEASURED, deliberately:** glyph coverage for Spanish diacritics
(`á é í ó ú ü ñ ¿ ¡`) and the tabular-figures feature that accessible budget tables need.
Both are cheap to verify and belong in the task that picks the font, not here.

**Nothing about the licence obliges us; the obligations are ours to discharge:** ship the
`OFL.txt` alongside the `woff2` files, and name the font in the licence manifest. Both are
task criteria in wave 6.

---

## R-6 · Does `type: Site` constrain themes in any way?

**No.** The RFC's entire theme paragraph, verbatim:

> #### Site templates care about looks
> * They MAY integrate heavily with Canvas, which allows a look and feel to be packaged in configuration.
> * They MAY depend on a theme (or multiple themes) as design systems — libraries of components, and their associated styles, for building out the look in Canvas.
> * They MAY ship custom components (probably code components, which are Canvas configuration entities) to augment the design system's components.
> * They MAY ship content templates (a Canvas configuration entity) that use the design system's components to define standard looks for the site template's content models.
>
> In short, site templates MAY use any tool, framework, design system, theme, or module they wish in order to define their look.

Every clause is MAY. The only theme-adjacent hard rules come from `RequirementsTest`:
**0 `*.info.yml` files in the package**, no pins, no patches, no install-profile
dependency, `type: drupal-recipe`, a declared licence, and no `_core`/`uuid` keys in
exported config. D-014=B is confirmed, not merely still-valid.

---

## R-7 · Incidental observations, recorded so they are not rediscovered

- **The starter kit's own project status is `unsupported`.**
  `updates.drupal.org/release-history/drupal_cms_site_template_base/current` →
  `<project_status>unsupported</project_status>`, one release, `1.x-dev`. Consistent with
  its own commit `baf67f1e`, *"this project will never have a stable release."*
  **Still not a finding** — we copied it, we do not require it.
- The branch `3568170-theme-dev` in the starter kit is **byte-identical to `1.x`**
  (API compare returns zero diffs). Issue 3568170 is *"The site template should set you up
  for theme development by default"* — about disabling Twig caching and aggregation, not
  about theme architecture. Not relevant to us.
- **`stylelint` and `twig-cs-fixer` gate on file existence**, not on project type:
  `.ccs-files-exist → exists: ['**/*.css']`, `.twig-files-exist → exists: ['**/*.twig']`
  (lines 522-532). Both will materialise in the theme repository the moment it has a
  stylesheet and a template, and with `_ALL_VALIDATE_ALLOW_FAILURE: '0'` both are
  blocking. The ROADMAP's unit-002 gate naming `stylelint` was right — about the wrong
  repository.
