# Ágora · State of the art — 2026-08-20

> Research from the DISPATCH-00 reconciliation pass (unit 001-fundación).
> I-001 applies: **every statement here expires**. Re-verify before building on top.
> Author: [ejecutor]. Capture date: **2026-08-20**.

## 0 · Confidence level of each block (read before anything else)

During this session **`www.drupal.org` was not reachable** (DNS resolution failure from the environment:
`getaddrinfo ENOTFOUND www.drupal.org`). `git.drupalcode.org` (GitLab API + raw) and
`packagist.org` were reachable. Consequently:

| Block | Source | Confidence |
|---|---|---|
| Real structure of the Starter Kit | `git.drupalcode.org` API/raw, read directly | **HIGH — verified at source** |
| Versions and branches | GitLab tags/branches API + Packagist | **HIGH — verified at source** |
| Marketplace requirements | Search-engine *snippets* only; pages not opened | **LOW — UNVERIFIED** |
| Machine name `agora` available | Not checkable without drupal.org | **UNVERIFIED** |
| SBOM (security coverage per module) | `updates.drupal.org` (official API), read directly | **HIGH — verified at source** (see §10) |

What is marked LOW/UNVERIFIED **cannot be used to close decisions**. It requires a second
pass with access to drupal.org.

---

## 1 · Drupal CMS — current stable version

Tags of the `project/drupal_cms` repo (GitLab API, 2026-08-20):

| Version | Date |
|---|---|
| **2.1.3** | **2026-06-01** ← latest stable |
| 2.1.2 | 2026-05-21 |
| 2.1.1 | 2026-04-09 |
| 2.1.0 | 2026-03-20 |
| 2.0.0 | 2026-01-27 |

→ **Current stable line: Drupal CMS 2.1.3.** The starter kit's `2.x` branch requires `drupal/core ^11.4`.

Source: `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms/repository/tags`

## 2 · Drupal Canvas

Confirmed indirectly but solidly: **Canvas is the page builder assumed by the starter kit**.

- The starter kit's `2.x` branch requires `drupal/canvas ^1.2` → **Canvas has a stable 1.x line**.
  This is relevant to non-negotiable no. 1: **it does not force unstable releases**.
- `recipe.yml` (1.x) contains an extensive block of `canvas.component.block.*` / `canvas.component.sdc.*`
  actions with `disable: []`, that is: the template controls which Canvas components appear in the UI.
- The kit's demo content lives as `content/canvas_page/<uuid>.yml` → **the demo pages are Canvas entities**.
- The default front page is set with `system.site: page.front: '/home'` pointing at a blank Canvas landing page.

⚠️ Terminology note: the marketplace marketing material speaks of *"XB-compatible themes"*
(Experience Builder). XB is the former name of Canvas. It is worth confirming when drupal.org is opened.

## 3 · The Starter Kit (`drupal_cms_site_template_base`) — REAL STRUCTURE

Project: `https://git.drupalcode.org/project/drupal_cms_site_template_base`
Packagist: package type **`drupal-recipe`**.

### 3.1 · Major finding: it has NO stable releases

`.../repository/tags` returns an **empty list**. Only branches exist:

| Branch | Latest commit |
|---|---|
| `1.x` | 2026-05-22 |
| `2.x` | 2026-08-18 (2 days ago) |
| `3568170-theme-dev` | 2026-04-28 |

**This does NOT violate non-negotiable no. 1.** The starter kit is **scaffolding that is COPIED**, not a
dependency that is declared: `GET-STARTED.md` describes the flow as *"copying the starter project"* and
`composer.json` literally instructs *"Change the 'name' field to your own package name"*.
It never appears in Ágora's `require` → it does not enter the SBOM. Record as an idiom so as not to
raise a false alarm in the `no-unstable-deps` invariant.

### 3.2 · The two branches are DIFFERENT things

**`1.x` = the real starter kit for developers** (it is the one that ships the documentation):

```
.eslintrc.json
.gitattributes.example        → rename to .gitattributes
.gitignore.example            → rename to .gitignore
.gitlab-ci.yml                → include of gitlab_templates
.github/workflows/phpunit.yml
.tugboat/config.yml
.tugboat/tugboat-settings.txt
AGENTS.md
GET-STARTED.md                → export-ignore; delete before publishing
LICENSE.txt
README.md
composer.json                 → type: drupal-recipe
recipe.yml                    → type: Site  (MANDATORY at the ROOT)
recommended.yml               → curated list for Project Browser
screenshot.webp
content/canvas_page/<uuid>.yml
tests/src/Functional/InstallTest.php
tests/src/Functional/ValidationTest.php
tests/src/Kernel/RequirementsTest.php
```

**`2.x` = an already-exported template**, without development scaffolding: it has `config/` (≈100 YAML
files of exported config) and `content/{file,menu_link_content,node}`, but it does **not** have
`.gitlab-ci.yml`, nor `.github/`, nor `.tugboat/`, nor `GET-STARTED.md` (HTTP 404 confirmed).

→ **For Ágora, the branch to copy is `1.x`** (it is the one that ships CI, Tugboat and documentation).
`2.x` serves as a **reference for how a template looks after `drush site:export`**.

### 3.3 · Hard constraints imposed by the kit itself

From `GET-STARTED.md` and the comments in `composer.json` / `recipe.yml`:

1. `recipe.yml` **must be named that and be at the root**.
2. `type: Site` **exact and case-sensitive**. Without that it does not appear in the installer.
3. The composer `type` **must be `drupal-recipe`**.
4. Package name: **must start with `drupal/`** and contain only letters, numbers and underscores.
5. **Forbidden to patch dependencies** with composer-patches plugins.
6. **Forbidden to pin versions**: use operators (`^1`), not exact versions.
7. Licence: `GPL-2.0-or-later` unless there is a specific reason — *"which license you choose may affect your
   ability to publish the site template"*.
8. You must have **legal rights over all included content**.

→ Points 5, 6, 7 and 8 **literally confirm** non-negotiables 1 and 2 of CLAUDE.md and §4 of
`plan.md`. That part of the plan does **NOT** diverge.

### 3.4 · Recipe composition rule (CRITICAL for plan.md §2)

Verbatim comment from `recipe.yml`:

> *"Recipes to apply before this one. None of these should be site templates themselves; a site
> template can be built on any number of smaller recipes, but you shouldn't build a site template on
> top of another site template (or combination of site templates)."*

Two readings, both important:
- ✅ **It is allowed** to compose a site template from N smaller recipes → the modular idea in
  `plan.md` §2 (`agora_base`, `agora_foi`…) **is conceptually legitimate**.
- ❌ **But** the starter kit **does not have a `recipes/` directory**. The recipes it composes
  (`drupal_cms_admin_ui`, `drupal_cms_media`, `easy_email_express`…) are **external composer packages**,
  declared in `require` and referred to by name in the `recipes:` key. Core ones go by path
  (`core/recipes/administrator_role`).

**There is no evidence in the kit of local sub-recipes inside the same repository.** It is the point that
triggers stopping rule no. 2 of the DISPATCH (see §7).

### 3.5 · The theme is GENERATED, not written by hand (to begin with)

`composer.json` → `extra.drupal-site-template.generate-theme`:

```json
"generate-theme": {
  "info": { "name": "Blank", "regions": {"header","content","footer"},
            "libraries-override": {"core/normalize": false} },
  "from": false,
  "name": "blank"
}
```

`drupal/site_template_helper` (composer plugin, `allow-plugins: true`) generates the `blank` theme in the
working site. `from: false` = **it does not inherit from any base theme**. The `extra.drupal-site-template`
block **is automatically removed by `drush site:export`** and must be deleted before publishing.

→ Impact on `plan.md`: `themes/agora_theme/` **is not a folder of the template repo** in the default
flow. It feeds decision D-008.

### 3.6 · Project Browser: `recommended.yml`

It allows publishing a curated list of recommended add-ons, served by a **GitLab API permalink**.
Literal warning from the file:

> *"It is STRONGLY recommended that this file ONLY list projects that have stable, supported releases."*

→ Aligned with the SBOM policy (D-004). Examples cited by the kit itself: `project/byte` and
`project/drupal_cms` (`recipes/drupal_cms_starter/recommended.yml`).

## 4 · CI: what it actually ships

### 4.1 · GitLab CI (`drupalcode`)

The kit's `.gitlab-ci.yml` **does not define its own jobs**; it is an include of the DA's `gitlab_templates`:

```yaml
include:
  - project: $_GITLAB_TEMPLATES_REPO
    ref: $_GITLAB_TEMPLATES_REF
    file:
      - '/includes/include.drupalci.main.yml'
      - '/includes/include.drupalci.variables.yml'
      - '/includes/include.drupalci.workflows.yml'
```

The jobs are controlled by **variables** (`SKIP_ESLINT: '1'`, `OPT_IN_TEST_NEXT_MAJOR: '1'`,
`_CURL_TEMPLATES_REF`…), documented at
`https://git.drupalcode.org/project/gitlab_templates/-/blob/main/includes/include.drupalci.variables.yml`.
General doc: `https://project.pages.drupalcode.org/gitlab_templates/`

→ **Confirms D-006**: the drupalcode pipeline IS the gate. The exact list of jobs (phpcs, phpstan,
cspell, eslint, stylelint, phpunit) **remains pending on reading the variables file**: it is not
invented here.

### 4.2 · GitHub Actions

A single workflow: `.github/workflows/phpunit.yml`. The GitHub mirror **is not just portfolio**:
the kit already uses it for PHPUnit. It feeds D-009 (where the visual tests run).

### 4.3 · Tests the kit already ships

- `tests/src/Functional/InstallTest.php` — clean installability
- `tests/src/Functional/ValidationTest.php` — recipe validation
- `tests/src/Kernel/RequirementsTest.php` — requirements

→ **The CLAUDE.md "install smoke" already exists out of the box.** It does not need inventing: it is extended.

### 4.4 · `.gitattributes` — the `export-ignore` pattern

The kit excludes from the downloadable package: `/.cspell-project-words.txt`, `/GET-STARTED.md`, `/.github`,
`/.gitlab-ci.yml`, `/tests`, `/.tugboat`.

→ Collateral fact: the existence of `/.cspell-project-words.txt` **confirms there is a cspell job** in
the DA's pipeline.

## 5 · `drush site:export` (Drupal CMS Helper)

Confirmed by `GET-STARTED.md`:
- Flow: build the site in the UI → `ddev drush site:export` → the site becomes a recipe.
- It automatically removes the `extra.drupal-site-template` block from `composer.json`.
- It automatically adds to `require` the modules/themes/recipes the template depends on.
- Recommended test: export → `ddev drush sql:drop --yes` → reinstall and check that the template
  appears in the installer's selection step.
- Recommended environment: **DDEV ≥ 1.25.0** (confirms D-003).

Known limitations: **not verified** (they require the issue queue on drupal.org).

## 6 · Marketplace — ⚠️ UNVERIFIED, search-engine snippets only

**Nothing in this block should be treated as firm.** No drupal.org page was opened.

Signals collected that, if confirmed, **diverge from `plan.md` §4**:

1. **Pilot limited to Drupal Certified Partners (DCP).** The marketplace would have started as a
   restricted pilot for DCPs, with later expansion. `plan.md` §4 **does not contemplate** any eligibility
   restriction. → If confirmed, it is **blocking for the goal declared in CLAUDE.md**.
2. **Fees: $395 per new listing + $250 annual review.** `plan.md` §4 does not mention cost.
   It also clashes with *"v1 is the flagship free template"*.
3. **Two separate routes**: *Marketplace* (reviewed, paid, DCP-only in the pilot) vs *Community*
   (general project on Drupal.org, **publishable directly without review**). D-002 already chose
   "general project on drupalcode" = the **Community route**, which is compatible and non-blocking.
4. Standards cited for the marketplace: security, **WCAG 2.2 AA accessibility**, performance,
   code quality, structured documentation, maintenance and support commitment.
   → Consistent with `plan.md` §4 and with non-negotiable no. 4.
5. Technical requirement cited: *"built for Drupal CMS, using the Recipes schema, demo content, and
   XB-compatible themes"*. → Consistent with the chosen architecture.

Sources (snippets, not opened):
`https://www.drupal.org/about/starshot/marketplace-initiative` ·
`https://www.drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates` ·
`https://events.drupal.org/chicago2026/session/launching-drupal-site-template-marketplace` ·
`https://www.thedroptimes.com/67494/drupal-clarifies-marketplace-and-community-pathways-site-templates`

## 7 · Divergences against `plan.md` / `CLAUDE.md`

| # | What disk assumes | What the real state says | Sev |
|---|---|---|---|
| A | `recipes/agora_base`, `agora_publishing`, `agora_foi`, `agora_ai`, `agora_governance` as subdirectories | The repo **IS a single recipe**: `recipe.yml` at the root. The kit has no `recipes/`; it composes external composer packages | 🔴 |
| B | `themes/agora_theme/` as a folder of the repo | The theme is **generated** via `site_template_helper` (`extra.generate-theme`), it is not versioned in the template | 🟡 |
| C | Open marketplace; flagship free v1 | **DCP-only** pilot + **$395/$250** (unverified) | 🔴 |
| D | "Stable only" applicable to everything | The starter kit **has no stable releases** — but it is copied, not declared → does not apply | 🟢 |
| E | The install smoke has to be built | It already exists: `InstallTest` / `ValidationTest` / `RequirementsTest` | 🟢 |
| F | GitHub mirror "optional, portfolio only" | The kit already ships `.github/workflows/phpunit.yml` out of the box | 🟢 |
| G | Machine name `agora` to be verified | **Not verifiable** without drupal.org | 🟡 |

## 8 · Pending for the second pass (requires access to drupal.org)

1. Open `new.drupal.org/site-template/apply` and `/share` → confirm or refute §6 (DCP-only, fees).
2. Availability of the machine name: `agora`, `agora_transparency`, `agora_gov`.
3. Read `include.drupalci.variables.yml` → exact list of jobs and their variables.
4. Feasibility of Playwright + axe on drupalcode runners (→ D-009).
5. SBOM: stable version and **security coverage** of each candidate (facets/search, webform,
   ECA, AI). Note: `eca ^3.1.2` already comes in the `require` of the kit's `2.x` branch.
6. Verify that `project_browser ^2.1-beta3` (a **beta** constraint present in the kit's `2.x`) does not
   drag unstable releases into Ágora's SBOM.
7. Confirm XB vs Canvas terminology in the official requirements.

## 9 · Sources consulted (2026-08-20)

- `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms_site_template_base/repository/{tree,tags,branches}`
- `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/{GET-STARTED.md,composer.json,recipe.yml,recommended.yml,.gitlab-ci.yml,.gitignore.example,.gitattributes.example}`
- `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/2.x/composer.json`
- `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms/repository/tags`
- `https://packagist.org/packages/drupal/drupal_cms_site_template_base`
- Search-engine snippets (§6), pages NOT opened due to the DNS block on `www.drupal.org`.

---

## 10 · Verified SBOM — 2026-08-20 (extension)

`www.drupal.org` remains blocked, but **`updates.drupal.org` does resolve**. It is the official
release history API and exposes, per release, the `<security covered="1">` element and `<core_compatibility>`.
This **closes the gap** of the SBOM block (§0) for the candidates consulted.

Endpoint: `https://updates.drupal.org/release-history/<project>/current`

| Project | Latest **stable** | Core compat | Security coverage |
|---|---|---|---|
| **Config Guardian** | **1.0.3** | `^10.5 \|\| ^11 \|\| ^12` | ✅ **COVERED** |
| AI (Artificial Intelligence) | **1.4.7** | `^10.5 \|\| ^11.2` | ✅ COVERED |
| AI Agents | 1.3.4 | `^10.3 \|\| ^11` | ✅ COVERED |
| OpenAI Provider | 1.2.5 | `^10.3 \|\| ^11` | ✅ COVERED |
| ECA | **3.1.6** | `^11.3 \|\| ^12.0` | ✅ COVERED |
| Search API | 8.x-1.41 | `^10.3 \|\| ^11` | ✅ COVERED |
| Search API Autocomplete | 8.x-1.12 | `^10.2 \|\| ^11` | ✅ COVERED |
| Facets | 3.0.4 | `^10.1 \|\| ^11` | ✅ COVERED |
| Webform | 6.3.0 | `^10.3 \|\| ^11.0` | ✅ COVERED |
| Charts | 5.2.3 | `^10.3 \|\| ^11 \|\| ^12` | ✅ COVERED |

**Conclusion: no planned SBOM candidate is excluded by policy.** All ten have a stable
release and security team coverage. Stopping rule no. 4 of the DISPATCH **is not triggered**.

### 10.1 · Config Guardian fits without friction

- Stable **1.0.3**, with security coverage, and `core_compatibility: ^10.5 || ^11 || ^12`.
- The starter kit's `2.x` branch requires `drupal/core ^11.4` → **11.4 satisfies `^11`**. Compatible.
- It passes the four gates of the `sbom-y-licencias` skill. It confirms D-004 with no need for an amendment.

### 10.2 · Warning about the AI module: use the 1.4 line, not 1.5

The **1.5 branch only has `alpha`/`rc`** (`1.5.0-rc1`, `1.5.0-alpha2`…). The latest **stable** is
**1.4.7**. Declare `^1.4` and **never** `^1.5` while 1.5 has not published a stable release.
Stable `ai` requires core `^11.2`, satisfied by 11.4.

⚠️ Pending verification: which AI **provider** to use. `ai_provider_openai` is covered, but
`plan.md` §2 requires **provider-agnostic**; the concrete provider should not be a hard dependency of the
template but a post-installation choice. Decision to prepare.

### 10.3 · Compatibility notes to watch

- **ECA 3.1.6** declares `^11.3 || ^12.0` → **it is not compatible with Drupal 10**. Not a problem
  (Drupal CMS 2.1.3 runs on core 11), but it sets a higher core floor than the rest.
- **Charts 5.2.3** is available and covered, but `plan.md` §3 asks to *avoid heavy charts
  modules*. If visualisation is wanted, it is a decision to open, not an automatism.
- `project_browser ^2.1-beta3` appears in the `require` of the starter kit's `2.x` branch. **Beta.**
  If Ágora copies that `require`, it drags in an unstable release → it must be resolved in unit 001.

### 10.4 · Method (reproducible)

```bash
curl -s "https://updates.drupal.org/release-history/<project>/current"
# leer, por release: <version>, <security covered="1">, <core_compatibility>
# the first release without dev/alpha/beta/rc is the latest stable one
```

This is the method the `tests/bin/sbom-check` invariant must implement.
