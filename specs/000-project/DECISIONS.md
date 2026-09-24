# Ágora · Decision record (D-NNN, append-only)

> **Translation note — 2026-08-21.** This file was mechanically translated from Spanish into English
> under D-017(b), which authorizes it explicitly. **The semantic content is unchanged:** no decision
> was added, removed, renumbered or altered in meaning. The Spanish original is preserved in the git
> history as the signed record. New entries are written directly in English.

> Verify ON DISK the next free number before adding one. Signed decisions are not edited: they are
> amended (in the same commit as the change that motivates it, only if it is a direct consequence) or
> a new one is opened.

- **D-001** · Name and concept: "Ágora", a transparency portal as a Drupal CMS Site Template, the
  flagship free template for the marketplace. Machine name PENDING an availability check
  (candidates: agora_transparency, agora_gov) — it is closed after DISPATCH-00. Signed (concept) by [andres] 2026-08-20.
- **D-002** · Repository and base: development on git.drupalcode.org as a Drupal.org general project,
  starting from the official Drupal CMS Site Template Starter Kit (it brings GitLab CI, GitHub Actions,
  Tugboat and base recipes). GitHub mirror optional, only for portfolio and, if needed, visual tests.
  Signed by [andres] 2026-08-20.
- **D-003** · Stack and tooling: current stable Drupal CMS + Recipes + a Drupal Canvas-compatible theme.
  Composer (PHP), pnpm exclusively (JS), DDEV (local). Signed by [andres] 2026-08-20.
- **D-004** · SBOM policy: stable releases with security team coverage only; no patches and no
  pins. Config Guardian included and preconfigured (recipe agora_governance). Midgard EXCLUDED
  while it is in alpha (narrative in docs only). Signed by [andres] 2026-08-20.
- **D-005** · Languages: process docs ES; code, identifiers, commits and public docs EN;
  demo content bilingual ES/EN. No AI co-authorship trailers in commits. Signed by [andres] 2026-08-20.
- **D-006** · Quality as a gate: drupalcode pipeline (gitlab_templates) + install smoke + PHPUnit +
  Playwright (functional/visual) + axe + invariant scripts in tests/bin/. Nothing advances with the
  pipeline red. Signed by [andres] 2026-08-20.

## Pending (raised after DISPATCH-00, with options + recommendation)
- D-007 · Final machine name of the project.
- D-008 · Canvas theme approach (start from the starter kit theme vs a bespoke theme from scratch).
- D-009 · Where the visual tests run (drupalcode CI vs the mirror's GitHub Actions), depending on what
  the runners support today.
- D-010 · Exact scope of the v1 demo content.

---

## Framing of pending decisions — prepared [ejecutor] 2026-08-20

> After the research `specs/001-foundation/research/2026-08-20-estado-del-arte.md`.
> None is closed without [andres]'s signature. ★ = recommendation from [ejecutor].
> Next free D-NNN verified on disk: **D-011**.

### D-007 · Final machine name
Context: the package name will be `drupal/<machine_name>` and it can no longer be changed after
publishing. Availability could not be checked (drupal.org blocked during the session).
- **A** · `agora` — clean, but it is a common word: probably taken.
- **B ★** · `agora_transparency` — descriptive, in English, almost certainly free.
- **C** · `agora_gov` — shorter, but "gov" suggests central government and the audience is broader.
★ **B**: it describes what it does, survives a Project Browser search and does not depend on `agora` being free.
*Prerequisite:* check availability on drupal.org before fixing it.

### D-008 · Theme approach
Context: the starter kit **generates** the theme with `site_template_helper` (`generate-theme`, `from: false`).
`CLAUDE.md` assumed a versioned `themes/agora_theme/` folder, which is not the default flow.
- **A ★** · Theme **generated** by the plugin and customized afterwards via config and the theme's own CSS.
  It follows the official path; less friction during review.
- **B** · A **bespoke theme versioned** in the repo. More control and better for the professional thesis, but
  it departs from the standard flow and has to be justified to the marketplace.
★ **A** for v1: the stated goal is to pass review on the first attempt. The sober aesthetic is achieved
just as well with tokens and CSS; B is a deviation that has to be defended for no reason.

### D-009 · Where the visual tests run
Context: the kit brings GitLab CI (DA jobs) **and** `.github/workflows/phpunit.yml`. It could not be
verified whether the drupalcode runners support Playwright + axe.
- **A ★** · Linters, static analysis and PHPUnit on **drupalcode**; Playwright + axe on the mirror's
  **GitHub Actions**. The kit already uses GitHub for PHPUnit, so the mirror is not only portfolio.
- **B** · Everything on drupalcode, if the runners allow it.
★ **A**, but **verify first**: if drupalcode supports Playwright, B is cleaner (a single gate).
Decision reviewable in wave 2 of unit 001.

### D-010 · Scope of the v1 demo content
Postponed to unit 003, once the content model exists. Keep it open.

### D-011 · Recipe architecture 🔴 **BLOCKING for unit 001**
Context: `plan.md` §2 and `CLAUDE.md` describe `recipes/agora_base`, `agora_publishing`, `agora_foi`,
`agora_ai`, `agora_governance` as subdirectories. **The starter kit does not work that way**: the repository
IS a single recipe (`recipe.yml` at the root, `type: Site`) and it composes **external composer packages**.
There is no evidence that the installer resolves local sub-recipes.
- **A ★** · **A single recipe** at the root. Ágora = one `recipe.yml` that composes Drupal CMS recipes
  and contrib modules. It is the kit's verified path and the lowest-risk one in review.
  Cost: the internal modularity that `plan.md` §2 wanted for the future paid template is lost.
- **B** · **Several projects on Drupal.org**: `agora_base`, `agora_foi`… as independent contrib recipes,
  and Ágora as a site template that lists them in `recipes:`. Maximum reuse; it is the pattern Drupal CMS
  itself uses. Cost: maintaining N projects, N releases, N reviews.
- **C** · Monorepo with local sub-recipes. It keeps the original plan exactly as it is, but **it is not
  verified to work** and it is the greatest risk of rejection.
★ **A for v1, with B as the evolution**: first publish a template that passes review, and extract
reusable recipes once the paid template exists. C is discarded unless it is verified.
*If A or B is signed, `plan.md` §2 and the "Repository structure" section of `CLAUDE.md` must be amended.*

### D-012 · Publication route 🔴
Context (⚠️ **unverified**, drupal.org blocked): signals that the marketplace started as a
**pilot limited to Drupal Certified Partners**, with **$395 per listing + $250 annually**.
`CLAUDE.md` states as its goal "passing the marketplace review on the first attempt" and `plan.md`
describes v1 as the "flagship free template" — both could be incompatible with the above.
- **A ★** · **The Community route** (a general project on Drupal.org, publishable without review and at no cost).
  It is what D-002 already chose. It meets all the quality standards just the same; the marketplace remains
  a later goal if it opens to non-DCPs.
- **B** · Marketplace, assuming the fee and the DCP requirement.
★ **A**, but **the decision must not be closed until the real requirements are verified** on drupal.org.
Building to the marketplace standard keeps both doors open.

### D-013 · AI provider
Context: stable `ai` is **1.4.7** (the 1.5 branch only has alpha/rc). `ai_provider_openai` 1.2.5 is
stable and covered. `plan.md` §2 requires **provider-agnostic** and graceful degradation without a key.
- **A ★** · Depend only on `ai` (^1.4) and **no specific provider**. The user chooses and installs their
  provider after installation. Maximum neutrality; CI runs without keys naturally.
- **B** · Include `ai_provider_openai` as recommended in `recommended.yml`, without it being a hard dependency.
- **C** · Depend on a specific provider. It contradicts provider-agnosticism. Discard.
★ **A**, with **B** as a complement: recommend without imposing.

### Note on D-004 (Config Guardian) — CONFIRMED, no changes
Verified on 2026-08-20 on `updates.drupal.org`: **Config Guardian 1.0.3**, stable, **with security
coverage**, `core_compatibility: ^10.5 || ^11 || ^12` → compatible with the core 11.4 that the starter kit
requires. It passes the four gates of the SBOM policy. **No amendment required.**
Equally verified and suitable: ECA 3.1.6, AI 1.4.7, AI Agents 1.3.4, Search API 8.x-1.41,
Facets 3.0.4, Webform 6.3.0, Charts 5.2.3 — all stable and covered (research §10).

---

## Signatures — 2026-08-21 [andres]

> Append-only. This section does not edit anything above. When a signature changes the framing of a
> decision already written above, it states explicitly which one is superseded and by which.
> Next free D-NNN after this batch: **D-015**.

- **D-007** · Final machine name: **`agora_transparency`**. The composer package will be
  `drupal/agora_transparency`; the project's visible title remains "Ágora".
  *Evidence (2026-08-21):* `git.drupalcode.org/api/v4/projects/project%2Fagora` → **200** (taken
  by an unrelated project); `…%2Fagora_transparency` and `…%2Fagora_gov` → **404** (free).
  ⚠️ The GitLab API is the only valid oracle: `www.drupal.org/project/<X>` returns **302 to
  new.drupal.org for any string**, including a non-existent one, and therefore **does not prove
  availability** (see I-012). Signed by [andres] 2026-08-21.

- **D-011** · Recipe architecture: **option A — a single recipe at the root**. Ágora is a single
  `recipe.yml` (`type: Site`) that composes Drupal CMS recipes and contrib modules declared in
  `require`. **There is no `recipes/` directory** with local sub-recipes. Signed by [andres] 2026-08-21.
  *Riders of [andres]:*
  (a) `plan.md` §2 and `CLAUDE.md` §"Repository structure" are amended **in this same commit**.
  (b) Reuse for the future paid template will be done by **extracting pieces into independent contrib
      recipes (pattern B)** when that unit exists. In v1 what is left is **seam, not
      implementation**: each functional area occupies a contiguous, labeled block of `recipe.yml`, and
      its own identifiers carry an area prefix (`agora_base_*`, `agora_foi_*`…), so that
      the future extraction is moving files and not rewriting.
  (c) The datum "a $395 fee per listing" remains pending verification with source and date in
      unit 007. → **Pending item CLOSED the same day by D-012**, see there.

- **D-008** · Signed by [andres] 2026-08-21 as **option A** (the theme is **not** hand-written
  inside this repository). With two caveats recorded the same day:
  1. **The attached rider — "the generated theme stays committed in the repo and the customization goes
     on top, versioned" — is SUSPENDED as technically impossible**, verified at the source:
     · the kit's `tests/src/Kernel/RequirementsTest.php` requires **0 `*.info.yml` files** in the whole
       package: *"Recipes cannot include any code (modules or themes) of their own; they must list
       them as dependencies in `composer.json`."*
     · The package is installed in `./recipes/<name>` (`drupal/cms` 2.x, `extra.installer-paths`),
       **outside the docroot**, where `RecursiveExtensionFilterCallback` does not even recurse (only
       the root's `profiles/`, `modules/`, `themes/`).
     · The official site templates ADR says that a template **MAY depend on a theme**, not include it.
     The stop condition that [andres] attached to the rider ("if the kit regenerates the theme on every
     end-user installation, STOP") **does not trigger**: `site_template_helper` generates once only,
     on the author's working site, is idempotent, and the `extra.drupal-site-template` block
     is removed before publishing.
  2. **D-008 is SUBSUMED by D-014**: the question "where the theme lives" is answered there. What
     survives of D-008 is the negative rule: *this repository contains no theme of its own*.

- **D-012** · Publication route: **option C — community first, marketplace afterwards**. They are not
  mutually exclusive: the marketplace **requires** the template to be a general project.
  Signed by [andres] 2026-08-21.
  *Finding that closes pending item (c) of D-011, verified 2026-08-21:*
  · `new.drupal.org/site-template/apply`, §Individuals: *"Free templates: Any individual who wants
    to submit free templates, is welcome to."* Being a Drupal Certified Partner or Ripplemaker is a
    requirement **only for paid templates**. The premise "DCP-only pilot" was **false**.
  · The fee of **$395 + $250/year** comes from the July 2025 *proposal*
    (`drupal.org/project/innovation_ideas/issues/3532934`), which says literally
    **"(none for pilot and MVP)"**. There is no confirmed fee for the MVP.
  · `drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates`:
    *"All free site templates, including marketplace templates, are general projects for packaging
    and distribution purposes"*, and it explicitly recommends *"sharing a community template first"*.
  *Riders of [andres]:* unit **007 becomes "community publication"**; the application to the
  marketplace is treated as **007-bis, non-blocking**.
  *Note:* the five marketplace review criteria (CI installability · SBOM with security
  coverage · license manifest · WCAG attestation · security response, with no pins and no
  patches) match `plan.md` §4 literally: that section is **confirmed at the source**.

- **D-013** · AI provider: **option A + B**. Hard dependency on `ai` **^1.4** only
  (never `^1.5`: that branch only has alpha/rc, it would violate non-negotiable rule 1). No provider as a
  hard dependency. `ai_provider_openai` (1.2.5, stable and covered) is **recommended** in
  `recommended.yml`, without being imposed. It preserves the provider-agnosticism of `plan.md` §2 and CI
  runs without keys (I-003). Signed by [andres] 2026-08-21.
  *Riders of [andres]:*
  (a) The recommendation is **by criterion, not by brand**: any provider that meets the same bar
      (stable release + security coverage) is also listed in `recommended.yml`.
  (b) **A fresh verification of the state of `ai` ^1.x on reaching unit 005**, before implementing.
      If `^1.5` has reached stable, an **amendment is proposed**; it is not assumed (I-001).

- **D-014** · Where Ágora's aesthetics live: **option B — theme as a separate project on
  Drupal.org** (`drupal/agora_theme`), versioned as normal (Twig, tokens, OFL typography),
  declared in Ágora's `require` and installed from `recipe.yml`. It is the route the official ADR
  contemplates and the one the kit's own `RequirementsTest` foresees ("a bespoke theme to which it is
  strongly coupled"). Signed by [andres] 2026-08-21.
  *Riders of [andres]:*
  (a) **Additional reason that makes B the only option compatible with the non-negotiables:** the
      self-hosted OFL typography is **files**, and configuration does not carry them; using a
      font CDN is a **GDPR liability** in the EU public sector. The "everything in Canvas config"
      option could satisfy neither the license nor privacy.
  (b) `agora_theme` is **scaffolded with the official generator** (`site_template_helper`) and is
      **promoted to its own project** from there.
  (c) **Theme machine name pending verification** (proposal: `agora_theme`). It is closed in
      unit 002, with the oracle from I-012.
  (d) **The stable release of `agora_theme` is a gate for Ágora's release** in unit 007: it must
      exist first.
  (e) When creating the theme project, **opt in to security team coverage**.
  (f) **Theme scope: minimal with teeth** — AA tokens, typography, accessible tables and forms,
      Canvas-compatible. **No generic CSS frameworks.**

- **D-015** · **Policy for AI artifacts in the public repository.** Signed by [andres] 2026-08-21.
  1. **`AGENTS.md` is product.** It stays in the repository, ships to the end user, and is written
     in English. Its "Template-specific notes" section — empty in the starter kit — is filled with
     what is specific to Ágora (do not hand-edit exported config, where the theme lives, AI features
     degrade without an API key in CI). It carries an **audience header**: *a guide for AI assistants
     working on a site built WITH this template*. During development of the template itself,
     `CLAUDE.md` governs — no agent of ours may mistake `AGENTS.md` for process instructions.
  2. **`CLAUDE.md`, `.claude/` and `specs/` stay VISIBLE** in the public repository, as disclosure of
     methodology. They are `export-ignore`d so they do not travel inside the packaged release.
     ⚠️ Verified 2026-08-21: `export-ignore` affects **only the packaged tarball**, never the git
     repository — *"All files will still be available for users that clone your project via Git."*
     Visibility in the repo is therefore a deliberate choice, not a side effect. Source:
     `drupal.org/docs/develop/git/git-for-drupal-project-maintainers/creating-a-project-release`
  3. **Amends D-005 on language** → superseded in full by **D-017**, see below.
  4. The `README.md` gains a **"Development process"** section: human-in-the-loop methodology,
     decisions signed under `specs/`, and disclosure of AI use in line with the current governance
     debate. It frames the artifacts before anyone discovers them.
  5. **Rule 7 reaffirmed:** no AI co-authorship trailers in commit messages, ever.

- **D-016** · **Repository workflow: D-002 is CONFIRMED, not amended.** Development stays canonical on
  `git.drupalcode.org`; the GitHub mirror is **read-only and carries the same history**, a trivial
  sync, and is set up in unit 007 (today it is only recorded). Signed by [andres] 2026-08-21.
  *Rationale, recorded because it is load-bearing:*
  · A **synthetic history** on drupalcode would reproduce the appearance of the "code dump" pattern
    that governance guidelines penalise. The **real history** — waves, signed gates, granular
    commits — **is the anti-slop evidence**.
  · Commits on drupalcode are the author's **contribution currency**; a filtered republish would
    throw them away.
  · The collaboration surface must match the truth of the code.
  · The aesthetic discomfort of exposing process artifacts is handled by `README` §"Development
    process" (D-015.4), which frames them before anyone stumbles on them.
  *Considered and rejected:* developing on a public GitHub as canonical and publishing only the
  template layer to drupalcode through a filtering script. Rejected for the reasons above.

- **D-017** · **Language: the ENTIRE repository is in English, process layer included.** Amends **D-005**
  and **rule 6 of `CLAUDE.md`**. Spanish remains the language of orchestration **outside** the
  repository (conversation with the human). Signed by [andres] 2026-08-21.
  *Riders of [andres]:*
  (a) The mechanical translation is executed **now**, in its own commit
      (`docs: translate process layer to English`), **with no semantic changes**. Any ambiguity that
      could alter meaning is **escalated, never resolved silently**.
  (b) For `DECISIONS.md` and `IDIOMS.md` the translation **does not violate append-only**: the whole
      file is translated with a header note (date, *"semantic content unchanged"*), and the Spanish
      original is preserved in git history as the signed record.
  (c) **New entries are written directly in English** from this decision onward.
  ⚠️ Operational consequence (I-008): the three subagent definitions under `.claude/agents/` freeze
  at session start. After translating them, the session **must be restarted** before relying on them.

- **D-018** · **Baseline SBOM.** The nine `drupal/*` packages in `composer.json`'s `require` as of
  today are approved as a single, **closed** baseline. All nine were verified on **2026-08-21**
  against `https://updates.drupal.org/release-history/<project>/current` (method: I-022 — download
  with `curl`, parse with `xml.etree` reading from stdin). All nine have a stable release and
  `<security covered="1">`. Signed by [andres] 2026-08-21.

| Decision | Package | Constraint | Verified stable | Coverage | Maintenance on drupal.org | What it contributes |
|---|---|---|---|---|---|---|
| `D-018` | `drupal/drupal_cms_admin_ui` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | Administrative backend: admin theme plus site-management modules |
| `D-018` | `drupal/drupal_cms_anti_spam` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | Basic anti-spam protection |
| `D-018` | `drupal/drupal_cms_authentication` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | Tweaks to user authentication |
| `D-018` | `drupal/drupal_cms_helper` | `^2` | 2.1.3 | `covered="1"` | Actively maintained · core `^11.3` | Tools for site template creators; polyfills functionality not yet in core |
| `D-018` | `drupal/drupal_cms_media` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | A set of basic media types and configuration |
| `D-018` | `drupal/drupal_cms_privacy_basic` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | Basic privacy and consent management tools |
| `D-018` | `drupal/drupal_cms_seo_basic` | `^2` | 2.1.2 | `covered="1"` | Minimally maintained · No further development | Basic SEO tools and configuration |
| `D-018` | `drupal/easy_email_express` | `^1` | 1.0.4 | `covered="1"` | Actively maintained | Configures Drupal to send well-formed HTML email |
| `D-018` | `drupal/site_template_helper` | `^1.0.3` | 1.0.4 | `covered="1"` | Actively maintained | Composer plugin that generates the `blank` theme declared in `extra.drupal-site-template` |

  *Source of the "what it contributes" column:* the descriptive comments the repository itself carries
  next to each entry — `recipe.yml` under `recipes:` (the seven Drupal CMS recipes and
  `easy_email_express`) and under `install:` (`drupal_cms_helper`) — and, for
  `site_template_helper`, `composer.json` (`config.allow-plugins` + the
  `extra.drupal-site-template.generate-theme` block) together with its `src/Plugin.php`.
  The token `D-018` sits in the **first cell of every row** as a technical requirement: `sbom-check`
  (T-304/T-306) matches "short package name + a `D-[0-9]{3}` token on the same line".

  *Why ONE decision and not nine:* rule 2 of `CLAUDE.md` asks for *"a line in `DECISIONS.md`"* per
  contrib module, not a decision per module. Seven of the nine are the base recipes that the starter
  kit itself composes — they are not choices Ágora made.

  *Riders of [andres]:*
  (a) **The baseline is CLOSED.** Every later addition, removal or major-version jump gets **its own
      D-NNN**. The baseline does not grow silently. The dependencies that *are* a choice — ECA, AI,
      Config Guardian, Webform, Charts — each get their own D-NNN when they arrive.
  (b) **`sbom-check` must fail loudly** for any package in `composer.lock` with no associated
      `D-NNN` token.

  *Note recorded explicitly rather than hidden:* six of the nine are marked
  *"No further development / Minimally maintained"* on drupal.org
  (`drupal_cms_admin_ui`, `drupal_cms_anti_spam`, `drupal_cms_authentication`, `drupal_cms_media`,
  `drupal_cms_privacy_basic`, `drupal_cms_seo_basic`). **This does not violate the policy** — D-004
  requires a stable release plus security-team coverage, and all nine meet both. It is written down
  here, ahead of the question, because these are **recipes**: they are applied, unpacked, and then
  live with the version of Drupal CMS that applied them. A recipe that is no longer developed is not
  an unmaintained runtime dependency.

- **D-009** · **Where the visual tests run: option C.** Signed by [andres] 2026-08-21.
  ⚠️ **C is a third option, not one of the A/B framed on 2026-08-20 above** (which read "everything
  split by tool" vs "everything on drupalcode"). It supersedes that framing.
  The decision is **split in two**, because accessibility and visual regression are not the same
  question and treating them as one is what made it hard:
  1. **Accessibility (axe) → drupalcode, canonical and MANDATORY.** It is mounted **inside the
     Nightwatch job that `gitlab_templates` already ships and already supports**, with
     `OPT_IN_TEST_DRUPAL_CMS: '1'`. No new job, no new image, no new runner: one npm dependency and
     one test file.
  2. **Visual regression → GitHub Actions, NON-blocking.**

  *Facts verified 2026-08-21 that support it:*
  · `gitlab_templates` ships **no Playwright job, no axe job and no visual-regression job**;
    Nightwatch is its only browser tool.
  · `OPT_IN_TEST_DRUPAL_CMS` exists (default `0`), documented as *"Set to 1 to opt in testing
    against the current stable Drupal CMS version"*.
  · Drupal core does **not** ship an axe integration for Nightwatch: it needs an npm dependency and
    a test of our own.

  *Why option A (axe on GitHub Actions) was discarded:* **D-016 declares GitHub a read-only mirror**,
  and a mirror whose CI is mandatory is a contradiction — the accessibility gate, which is the
  product's thesis, would live where a Drupal.org contributor can neither see it nor re-run it, and
  the reviewer who opens the canonical project would find no accessibility evidence at all.

  *Riders of [andres]:*
  (a) The Nightwatch+axe pattern is **the one Drupal core itself uses**: cited here as precedent.
  (b) Turn on the opt-in variable and, **as soon as the drupalcode project exists, run a canary MR**
      that verifies the browser job really executes on the shared contrib runners — **before unit
      006**. Finding this out late was exactly the cost this was meant to avoid.
  (c) **The axe report is exported as a CI artefact**: it becomes the citable evidence for the WCAG
      attestation the marketplace requires.
  (d) **Visual regression is INFORMATIVE by definition**: no task may reference it as a gate. Its
      workflow **lives in the canonical repository** and runs only on the mirror.

  *Warnings that ship with the signature, not after it:* the axe↔Nightwatch integration **is not
  proven**; it is verified in unit 002, when a theme exists and there is something to audit. And
  none of the options can be *applied* while the drupalcode project does not exist (404 verified
  today): signing D-009 and creating the project are the same practical decision.

- **Amendment to D-008 — correction of a fact.** Signed by [andres] 2026-08-21.
  D-008 records above that *"`site_template_helper` generates once only, on the author's working
  site […] The stop condition […] does not trigger."* **That is false.** Verified at the source
  (`git.drupalcode.org/project/site_template_helper/-/raw/1.x/src/Plugin.php`):

  ```php
  public function onPackageInstall(PackageEvent $event): void {
    $package = $operation->getPackage();
    $this->generateTheme($package);   // no root-package check
  ```

  `generateTheme()` filters on two conditions only: that the package is a recipe, and that it
  carries `extra.drupal-site-template.generate-theme`. **It does not check whether it is the root
  package.** Therefore, when an end user runs `composer require drupal/agora_transparency`, the
  plugin runs on their machine and writes `<drupal_root>/themes/blank/blank.info.yml`. It is
  idempotent (`if (file_exists($info_file_path)) return;`), but it **does** generate on the end
  user's installation: the stop condition [andres] attached to the D-008 rider **did** trigger, and
  was assessed wrongly.

  **The conclusion does not change**: D-014=B (theme as a separate project) remains correct, for the
  reasons [andres] added — self-hosted OFL typography and GDPR (I-017). **What is amended is the
  record of the fact**, so that whoever reads D-008 six months from now does not believe something
  false.

  *Practical consequence, UNVERIFIED, inherited by T-401:* **`config.allow-plugins` in a dependency
  package is ignored by Composer** — only the root package's counts. The kit's own workflow gives
  this away by running `ddev composer config allow-plugins.drupal/site_template_helper true`
  explicitly. If the end user's root `composer.json` does not authorise the plugin, `blank` is not
  generated, and `recipe.yml` both lists it in `install:` and pins it in `system.theme.default` →
  **the clean install fails.**

- **D-019** · **Where the development environment lives: option C — the gate runs in a container,
  not on host tools.** Signed by [andres] 2026-08-22.
  ⚠️ **C is a third option, not one of the A/B framed by the `orquestador` on 2026-08-21**
  ("Linux/DDEV canonical, Windows a convenience" vs "the Windows host is a first-class gate
  environment"). It supersedes that framing, and it was surfaced by [andres]'s question
  *"can it not be environment-agnostic — I need it to work wherever I am"*.

  **Windows and macOS are BOTH first-class development hosts.** Neither host's tooling is the
  gate's tooling: the toolchain that produces a verdict lives in a container, so `grep`, `jq`,
  `python3` and `composer` are the same bytes on every machine and in CI.

  *Evidence that decided it — all five false greens of unit 001 came from the HOST, not from the
  project's own code:*
  · `sbom-check` non-discriminating — Windows `jq`/`python` emit CRLF on stdout (I-025).
  · `no-boilerplate` a total no-op — GNU grep 3.0 from Git for Windows aborts on `-F` with `-i` (I-027).
  · `python3` resolving to the Microsoft Store stub, which passes `command -v` (I-026).
  · The wave 1 gate not runnable at all — no `jq`, no `composer` on the host.
  · `UnicodeDecodeError` in a signed gate command — host Python defaulting to cp1252, not UTF-8.
  A container does not *guard* that class of defect; it **deletes** it, which is the same move as
  T-316/R2 and the reason this is C and not B.

  *Riders of [andres]:*
  (a) **A toolchain floor is pinned and documented** — the container image and the minimum
      versions of `grep`, `jq`, `python3`, `curl` and `composer`. T-317 owns recording it.
  (b) **`tests/bin/doctor` runs before any work in a session.** It detects the platform and
      **exercises** every required tool rather than locating it (I-026: the Store `python3` stub
      satisfies `command -v` and then fails at first real use). It reports what is missing and
      how to install it. It is the first step of `/retomar`.
  (c) **Host mode stays available as a fallback** — a laptop away from home should not need
      Docker running to do useful work — **but `doctor` labels any platform whose dirty-case
      matrix has not been run as NOT CERTIFIED**, and a gate result from an uncertified platform
      is reported as such. A green on a platform with no matrix is not a green, it is an
      assumption: that is exactly what `no-boilerplate` was for two commits.
  (d) **The dirty-case matrix (T-312) runs on every platform declared first-class.** A no-op on
      one platform is a no-op nobody sees.
  (e) **Every new invariant ships with its dirty case in the same commit**, from here to unit 007.
      No invariant counts as existing until it has been made to fail. (Rider absorbing the
      `orquestador`'s E4 meta-risk: verification code is the only code whose bugs are invisible
      by construction, because its correct output and its broken output look the same.)

  *Consequence for the wave 2 deadlock, which this decision exists to break.* Two needs were being
  conflated in T-201/T-207/T-208, which is why they were mutually incompatible:
  1. **Running the `tests/bin/` invariants** needs only the pinned toolchain → the container.
  2. **The install smoke and PHPUnit** need a real Drupal site → set up **separately**, with this
     package added as a *path repository*, exactly the flow T-207 recorded and the flow
     `.github/workflows/phpunit.yml` already executes.
  A recipe package is not a site and is never `ddev start`ed on its own. **T-201 is therefore
  superseded by T-207**, and **T-208 is redefined**: what gets versioned is the gate's container
  definition, not a `.ddev/config.yaml` for a site this repository does not contain. The
  `orquestador` owns the task-level rewiring; this decision fixes only the principle.

- **D-020** · **Interim CI: GitHub is an execution surface, never an authority — and tests reach the
  package under test by being copied in, not by being shipped.**
  Signed 2026-08-22 by **[ejecutor] under the explicit delegation of [andres]**
  <!-- cspell:disable -->(*"aquí dejo a tu elección según lo que hemos hablado qué firmar y qué arreglar"*)<!-- cspell:enable -->. Recorded this
  way deliberately: [andres] delegated the choice, he did not write this text, and the record must
  not imply otherwise.
  *Spell-check rider (D-024):* the quotation above is a verbatim record of a delegation by [andres].
  It is scoped out of the spell check rather than declared as vocabulary, and **must not be
  reworded, translated or corrected**.

  *Context:* Ágora's canonical remote does not exist yet (`git.drupalcode.org/api/v4/projects/
  project%2Fagora_transparency` → 404). Throughout unit 001 the **only** remote, and the only place
  any CI has ever executed, has been a personal GitHub repository running the workflow inherited
  from the starter kit — which, from `975b263` (2026-08-21) to `e54caa3` (2026-08-22), executed
  **zero tests across nine consecutive green runs**.

  - **A** · Delete `.github/workflows/phpunit.yml` and run nothing until drupalcode exists.
    Cost: unit 001 closes with no execution evidence whatsoever; wave 4's gate A becomes
    unreachable and T-402/T-406 slide behind a 👤 action.
  - **B** · Promote GitHub to canonical CI for the interim. Cost: contradicts D-016 and D-009 —
    the quality evidence would live where a Drupal.org reviewer can neither see nor re-run it,
    and it would have to be un-promoted later.
  - **C ★** · Keep GitHub as an **interim, informative execution surface**: the workflow runs, its
    result never gates a wave, it is repaired so that what it reports is **true**, and it carries an
    explicit expiry at unit 007.
  ★ **C**: the cheapest way to obtain real execution evidence before the canonical remote exists,
  without granting authority to a mirror.

  *Riders:*
  (a) **`phpunit.yml` is reclassified as informative — and "informative" is a lower standard of
      AUTHORITY, never a lower standard of TRUTH.** The earlier framing kept this workflow because
      it was *"the only executing install smoke Ágora has"*. **That justification was false**: it
      executed nothing. It is kept on a narrower ground — it is the only place where a real Drupal,
      this package and these tests are assembled at all, and it is repairable today without a
      project on drupalcode. The keep is **conditional and expiring**: if T-213 does not produce a
      run reporting `Tests: N` with N ≥ 3 and `RequirementsTest` among them, the workflow is
      **deleted**, not tolerated. A job that runs no tests is worse than no job, because it
      manufactures the appearance of coverage — which is the entire finding of this turn. At unit
      007 it is deleted, or reduced to what D-009(2) assigns to the mirror (visual regression,
      non-blocking).
  (b) **Amendment to D-016, one line, fact only.** D-016 records that the GitHub mirror *"is set up
      in unit 007 (today it is only recorded)"*. In fact, for the whole of unit 001 GitHub was the
      project's **only** remote and the only place any CI executed. D-016's direction is unchanged —
      drupalcode is canonical the day it exists, and the history moves whole — but the record is
      corrected so that nobody reads D-016 as a description of what was true in August 2026.
  (c) **Container base for the gate toolchain (closes D-019 rider a's open half).**
      ★ the Drupal Association's own gate image,
      `registry.gitlab.com/drupal-infrastructure/drupalci/drupalci-environments/php-8.3-ubuntu-apache`,
      **pinned by digest**, never by the moving `:production` tag. Verified 2026-08-22 in
      `gitlab_templates` — this is literally the userland the canonical gate runs, so a green there
      means what a green there means. It also ships PHP 8.3 **NTS** and Composer, retiring the
      "PHP 8.4 ZTS on the dev host" blocker. Named fallback if that image lacks `jq` or `python3`:
      `debian:bookworm-slim` with explicit apt pins — **never `alpine`**, whose BusyBox `grep` is a
      different implementation and would reintroduce I-027's exact class.
      ⚠️ **Unverified today**: the Docker daemon is down on the dev host, so the image's contents
      have not been inspected. This rider fixes the **preference and the fallback**, not a
      measurement. T-317 owns the measurement; until it exists, `doctor` reports the container path
      as **NOT CERTIFIED** (D-019 rider c).
  (d) **`/tests export-ignore` STAYS, and tests reach the package under test by being copied in.**
      T-104 was correct: the end user receives no tests. Verified 2026-08-22:
      · the starter kit does the same — `drupal_cms_site_template_base@2.x`, `.gitattributes:1`;
      · the three published site templates (`caresphere@1.2.x`, `convene@1.2.x`, `provus_edu@1.x`)
        carry **no `tests/` and no `.gitattributes` at all**, so the ecosystem norm is "no tests
        anywhere" — which is exactly why a suite reporting zero looked normal to everyone;
      · the canonical pipeline already solves this by copying: `gitlab_templates`
        `include.drupalci.main.yml:1605-1611`, `.recipe-replace-symlinks`, `cp -Rvp
        $CI_PROJECT_DIR/tests $DRUPAL_PROJECT_FOLDER` — from the **clone**, where `export-ignore`
        does not apply (I-021).
      The local workflow adopts the same move (T-213). **Rejected alternatives, with their reasons
      recorded so they are not re-proposed:** dropping the `export-ignore` (inverts a correct
      packaging decision to work around one runner); dropping `COMPOSER_MIRROR_PATH_REPOS=1`
      (upstream wrote a shim *because* symlinks break recipe test resolution); pointing PHPUnit at
      `source/tests` (**impossible**: all three tests resolve the recipe with `dirname(__FILE__, 4)`
      — `InstallTest.php:28`, `ValidationTest.php:36`, `RequirementsTest.php:27` — so they would
      silently change subject from the package to the repository); moving the exclusion to
      `composer.json`'s `archive.exclude` (**no effect** — Composer applies `GitExcludeFilter` and
      `ComposerExcludeFilter` in the same chain, `ArchivableFilesFinder.php:60-61`).
      *Consequence kept deliberately:* everything **except** `tests/` in the mirrored copy remains
      the packaged artefact, so `RequirementsTest` scans, and `ValidationTest` applies, what the end
      user actually receives. That is stronger evidence than drupalcode's own symlinked variant.

- **Amendment to D-007 — the visible title, corrected by the event.** Signed by [andres] 2026-08-22.
  D-007 records above that *"The composer package will be `drupal/agora_transparency`; the project's
  visible title remains 'Ágora'."* **The second half did not survive contact with the form.**

  *What happened, 2026-08-22:* on submitting the project-creation form, Drupal.org rejected the
  **Name** field with **"This project name is already in use."** The **Short name**
  `agora_transparency` was accepted without complaint. **The machine-name half of D-007 is untouched
  and confirmed**; only the title half is amended.

  *The title actually accepted, read from the project page rather than assumed:* **`Ágora
  Transparency`** — `<title>Ágora Transparency | Drupal.org</title>` and
  `<h1 id="page-title">Ágora Transparency</h1>`, identical on `www.drupal.org` and `new.drupal.org`.
  It was recommended before the fact and **verified after it**: a recommendation is not a record
  (I-037).

  *The colliding project, verified at source 2026-08-22:*
  `git.drupalcode.org/api/v4/projects/project%2Fagora` → **200**, created **2018-12-21**,
  `last_activity_at` **2019-01-08T17:15:39Z** — dormant for seven and a half years —
  `visibility: public`, `archived: null`, `default_branch: master`. It predates site templates
  entirely and therefore cannot appear in the Drupal CMS installer's template selector.

  ⚠️ **Its title is `Agora`, without the accent**, and Drupal.org still rejected `Ágora` as already
  in use: **the name check is accent-insensitive.** Recorded because it is the fact that decides
  D-021 — to Drupal.org's own matching logic `Ágora` and `Agora` are one name, so a bare `Ágora` on
  any of Ágora's own surfaces is a string that resolves, on the site we are publishing to, to a
  stranger's 2019 project.

  *What this amendment does NOT do:* it changes no string in the repository. "What the project is
  called on Drupal.org" and "what the installer shows the user" are two questions, and conflating
  them is what produced this divergence in the first place. **D-021 rules on the repository**,
  separately and on its own evidence.

  *Consequence for the record, stated so nobody edits it:* the **Gate B wave 1** signature in
  `specs/001-foundation/tasks.md` — `[✓ 2026-08-21 andres]`, *"visible identity 'Ágora'"* — **is not
  edited.** It recorded what was true and decided on 2026-08-21. Its package-name half stands; its
  identity half is superseded by this amendment and by D-021.

- **D-021** · **Naming coherence: identity strings carry the full name, prose keeps the short form.**
  Signed 2026-08-22 by **[ejecutor] under [andres]'s explicit delegation** <!-- cspell:disable -->(*"cambia lo que tengas
  que cambiar para que todo esté en sintonía y no haya malentendidos"*)<!-- cspell:enable -->. [andres] raised the risk
  and delegated the fix; he did not write this text.
  *Spell-check rider (D-024):* the quotation above is a verbatim record of a delegation by [andres].
  It is scoped out of the spell check rather than declared as vocabulary, and **must not be
  reworded, translated or corrected**.

  *The rule:* **a string whose job is to name the product to someone who has no other context
  carries the full name `Ágora Transparency`. A string that refers back to a product already named
  on the same surface may use `Ágora`.** Identity vs. prose.

  *Why the "Drupal CMS"/"Drupal" analogy does not license a bare title:* that pattern works because
  the full name is established first, where the reader cannot miss it, and the short form is then a
  pronoun. `recipe.yml`'s `name:` has **no line above it** — in the installer's template selector
  that string *is* the entire user interface for "what is this and where do I find out more". A user
  who reads `Ágora` and searches drupal.org lands on a project last touched in January 2019, under
  a name-matching rule Drupal.org has already demonstrated treats the two as identical.

  *Ecosystem evidence, verified 2026-08-22 — `recipe.yml` `name:` against the Drupal.org title:*
  `caresphere` → `CareSphere` / `CareSphere`, exact · `convene` → `Convene` / `Convene`, exact ·
  `provus_edu` → **`Drush Site-Install`** / `Provus®EDU`. Two of three are byte-identical; the third
  ships a `drush site:install` artefact in the installer's selector that nobody ever read. **The norm
  is that the two match, and where it diverges it is a defect that proves nobody checked** — which
  is exactly the kind of self-audit this template sells.

  *Identity strings (full name):* `recipe.yml` `name:` · `README.md` H1 and its first prose mention ·
  `AGENTS.md` audience header and first body mention · `recommended.yml` header. `AGENTS.md` and
  `recommended.yml` are load-bearing here for a structural reason: `AGENTS.md` is scaffolded into the
  **end user's** site root by `composer.json`'s `drupal-scaffold.file-mapping`, and `recommended.yml`
  is consumed by Project Browser **by permalink** — both are read with no README adjacent.

  *The two `description` fields drop the name rather than expanding it* — `recipe.yml` and
  `composer.json`, kept byte-identical to each other. A description's job is to describe, not to
  name; the name is rendered immediately adjacent in every surface that shows the field. It also
  avoids reading "transparency" twice in six words, and it is what both well-formed published
  templates already do.

  *Prose keeps `Ágora`* everywhere below a surface that has already named it in full, and the whole
  process layer (`CLAUDE.md`, `.claude/**`, `specs/**`) is untouched: it is `export-ignore`d, it
  names the product to nobody evaluating it, and append-only forbids rewriting signed text.

  *A deny term in `no-boilerplate` was considered and **rejected**, with reasons recorded so it is
  not re-proposed:* (1) `Ágora` is a legitimate substring of the correct value and of ~33 prose
  occurrences, so the term would report ~35 findings on a clean tree and the only way to green would
  be deleting the product's name from its own documentation; (2) that list's header defines its
  terms as verbatim starter-kit strings, and a term filed under a header that does not describe it
  is a term the next maintainer is entitled to delete as miscategorised; (3) decisively, a deny term
  is an **expect-zero** assertion, and I-028 says to prefer an expected value a failure cannot
  counterfeit. "The identity strings are correct" is naturally **expect-present**. The guard is
  therefore **T-322**, `tests/bin/identity-strings`, which also closes the I-024 class by requiring
  that every packaged file naming the product be declared as identity or prose — a new undeclared
  one is a finding, not a silent pass.

- **D-022** · **Canonical git topology and the first push.** Replaces the framing of earlier the
  same day, whose option A is **withdrawn**: it rested on the belief that pushing any branch would
  produce a pipeline. That belief is **false** (see (6)), and [andres] ruled 2026-08-22:
  <!-- cspell:disable -->*"la metodología de git que utiliza toda la comunidad de Drupal … haz primer push en la rama
  principal y luego crea las ramas que consideres."*<!-- cspell:enable --> Clauses (1)-(6) are signed **[ejecutor] under
  his delegation**; the three reserved acts are marked 👤.
  *Spell-check rider (D-024):* the quotation above is a verbatim ruling by [andres]. It is scoped
  out of the spell check rather than declared as vocabulary, and **must not be reworded,
  translated or corrected**.

  *Context:* `project/agora_transparency` exists, repository **empty — 0 branches, 0 commits**, and
  `default_branch: main` points at a ref that does not exist.

  **(1) The branch name is a rule, not a preference.** Release branches are `{major}.x` or
  `{major}.{minor}.x` — `/docs/develop/git/git-for-drupal-project-maintainers/release-naming-conventions`,
  updated 2026-01-11 — and a branch outside that shape **cannot host a release**: *"If you fail to
  do so, you will not be able to add a new release from your project page."* `main`/`master` are
  ruled out by the same page: *"Git's default `master` branch should be avoided and no downloadable
  releases can be tied to that branch … Use a release branch like `7.x-1.x`, `8.x-1.x`, or `1.x` as
  your main development branch."*
  ⚠️ This doc was reported *unfindable* in the previous turn. It was not deleted — the slug guessed
  from its title 404s while the real page serves 200. See **I-041**.

  **(2) `1.x`, not `1.0.x`.** ★ The elective choice of this decision, made on **mechanism, not
  frequency**. Behaviour sample of 31 projects: 11 use `{major}.x`, 14 `{major}.{minor}.x`, 6 remain
  on legacy `8.x-1.x`. A `{major}.x` branch publishes any three-component tag — the docs' own
  example is *"3.x and 3.4.2"* — so `1.x` carries the whole 1.* series with **no branch migration
  at any minor**, and the docs describe adding `1.0.x` **later** *"for those commits, if needed"*.
  The converse is not true: starting at `1.0.x` and later wanting `1.x` strands a `1.0.x-dev`
  release node and costs a default-branch move. **`1.x` is the option that keeps the other option
  open.**
  ⚠️ Counter-evidence, recorded against ourselves: the two published marketplace site templates,
  `caresphere` and `convene`, both use `{major}.{minor}.x`. Choosing `1.x` is a mechanical argument,
  not a conformist one.

  **(3) `main` is never created.** Beyond (1), it is mechanically dangerous here. Gitaly resolves a
  default branch as: HEAD's target → `refs/heads/main` → `refs/heads/master` → the first ref of
  `git for-each-ref --count=1 refs/heads/` (read at source 2026-08-22,
  `internal/git/localrepo/refs.go`). While HEAD is unpinned, a `main` branch would **silently
  outrank `1.x`** and drag `$CI_DEFAULT_BRANCH` with it.

  **(4) `001-fundacion/scaffolding` is never pushed to drupalcode.** Not because the name breaks a
  rule — day-to-day names are explicitly free (*"You are free to name your branches whatever you
  like"*) — but because it cannot host a release, it triggers no pipeline, and
  `refs/heads/001-…` sorts **before** `refs/heads/1.x` bytewise, so it would capture the fallback
  default branch. Verified locally 2026-08-22 with `sort`. `1.x` is created with
  `git branch 1.x 001-fundacion/scaffolding` — a pointer to the same commit object. **All 41
  commits keep their SHAs, authors, dates and messages; nothing is rebased, amended, filtered or
  forced.** D-016 is satisfied by construction, not by care.

  **(5) The public topology is one branch.** After the push, `project/agora_transparency` holds
  **`1.x` and nothing else**. The GitHub mirror is retargeted to `1.x` so the two remotes agree, and
  the old working branch is deleted there once `1.x` is verified on both — with `git branch -d`,
  **never `-D`**, so git itself refuses if any commit would become unreachable.

  **(6) Why `1.x` first, and why option A could not have worked.** `gitlab_templates`' workflow
  rules, read at source 2026-08-22 (`includes/include.drupalci.workflows.yml`), run a branch
  pipeline only when ``($CI_COMMIT_BRANCH == $CI_DEFAULT_BRANCH || $CI_COMMIT_BRANCH =~
  /^[78]\.x-\d+\.x$|^[\d+.]+\.x$/) && $CI_PROJECT_ROOT_NAMESPACE == "project"``. `1.x` matches the
  regex, so pushing it triggers a pipeline **regardless of what the default branch says** — the only
  first move whose CI does not depend on Gitaly's fallback. `001-fundacion/scaffolding` matches
  nothing. Option A's claimed gain, *"the pipeline runs today"*, was false. See **I-040**.
  ⚠️ The include is pinned by `$_GITLAB_TEMPLATES_REF`, an instance variable we cannot read. These
  rules are read from `main`, **not observed in our pipeline**. T-203/T-218 are the authority.

  **👤 Reserved to [andres] — three acts, none delegable:**
  (a) **The push credentials.** The maintainer URL and a PAT or registered SSH key, from the
      project's Version control tab. Rule 10 restated: *the first push to an empty canonical
      repository needs a signature; every subsequent push to `1.x` does not.*
  (b) **Pinning the default branch to `1.x`** — GitLab → Settings → Repository → Branch defaults.
      Requires `Administer maintainers` + `Write to VCS` (the GitLab Maintainer role). **Mandatory,
      not cosmetic:** until HEAD is pinned the default branch is merely *resolved* and flips the
      moment a second branch exists. `git ls-remote --symref drupalcode HEAD` tells pinned from
      resolved; the API's `default_branch` cannot.
  (c) **Whether to create a `1.x-dev` release node.** Optional, no deadline.

  **Never `--force` to either remote.** If `git ls-remote` ever disagrees with local `1.x`, stop and
  report; do not reconcile with a force push.
- **D-023** · **A green pipeline is not a green gate: the validate stage becomes blocking, and gate A
  is redefined as a statement about the job list.** Signed by [andres] 2026-08-23.

  *Context, observed not assumed — pipeline `933270`, ref `1.x`:* seven jobs ran, **`cspell` FAILED**,
  and the pipeline reported `success` in 309s. Four of the seven carry `allow_failure: true` by
  upstream default. Non-negotiable rule 9 (*"a red CI pipeline blocks everything else"*) and D-006
  (*"nothing advances with the pipeline red"*) therefore describe, for `phpcs`, `phpstan`, `eslint`
  and `cspell`, a state that **cannot occur**.

  **(1) The mechanism is a documented variable, not an override.** `_ALL_VALIDATE_ALLOW_FAILURE`
  (`include.drupalci.variables.yml:127-129`): *"set to 0 for not allowing any failure."* Each validate
  job carries a three-rule ladder (cspell at `include.drupalci.main.yml:1290-1294`) whose second rule
  matches `== "0"` and sets `allow_failure: false`. **No job is redefined, copied or overridden; the
  `include:` block is unchanged. T-202's amended criterion is untouched.**

  **(2) Per-job `'1'` beats the global `'0'`** — the ladder is ordered and rule 1 matches on the
  per-job variable alone. That asymmetry is what makes a scoped, named, dated exception expressible.

  **(3) Precedent, both halves.** Fifteen actively-maintained contrib projects read 2026-08-23:
  **zero** set this variable; the contrib norm is to *disable* checks (`devel`: `SKIP_CSPELL: 1`;
  `ai`: `SKIP_ESLINT: 1`, `SKIP_STYLELINT: 1`, a nine-entry `_CSPELL_IGNORE_PATHS`). **Drupal core
  does the opposite:** in `core/.gitlab-ci.yml` @ `11.x`, Spell-checking, PHPCS and PHPStan carry
  **no `allow_failure` key at all** — GitLab's default `false`. Ágora follows core, not contrib: a
  transparency template whose linters cannot fail is not defensible at review.

  **(4) Accepted cost, stated before the benefit.** Blocking `phpcs`/`phpstan` means **upstream can
  turn our branch red with no commit from us**: `$_GITLAB_TEMPLATES_REF` is an instance variable we
  cannot read or pin (D-022(6)). A new sniff or a bumped `_PHPSTAN_LEVEL` default arrives
  unannounced. This is precisely why the fifteen contrib projects decline. **We pay it.** It is the
  price of rule 9 meaning something.

  **(5) The variable alone is insufficient — gate A is redefined.** Even with all seven blocking, a
  job that **never ran** still yields a green pipeline (I-040): `skip-*-rule` and the
  `php-files-exist` guards can silently reduce N, and `stylelint` and `twig-cs-fixer` are absent from
  the observed seven for exactly that reason. **Amended rule, superseding rule 9's second sentence
  and D-006 on this point:**
  > *Gate A on drupalcode is green when, and only when: the pipeline's **job list** is read from the
  > API; `jobs >= 7`; every job's `status == "success"`; and every job's `allow_failure == false`
  > except those named in a dated, owned exception in `.gitlab-ci.yml`. **`jobs: 0` is a failure, not
  > "nothing to report."** The pipeline's own status field is never the evidence.*

  **(6) `cspell` is exempted temporarily, with an owner and an exit gate.** `_CSPELL_ALLOW_FAILURE:
  '1'` **changes no behaviour** — cspell is already non-blocking by an inheritance stated nowhere.
  The line converts an *invisible* exception into a *stated* one, and because the global is now `'0'`
  it is the **only** thing keeping cspell permissive, so it cannot be forgotten silently. Owner
  **T-226**; deleting the line is the exit gate. A fully-blocking cspell today was considered and
  rejected by the wave-1 rider: it would leave the public canonical branch deliberately red for the
  duration of the triage, and *"a tolerated red degrades the gate"*.

- **D-024** · **The cspell corpus: rename the Spanish, declare the vocabulary, scope the quotations —
  never dump the artifact.** Signed by [andres] 2026-08-23.

  *Context:* the cspell job reported **495 occurrences** over the **clone** (`export-ignore` is
  irrelevant — I-021, I-033). The number that governs the work is the one the job itself computed and
  saved as an artifact: **146 distinct unrecognised words**, read from
  `_cspell_unrecognized_words.txt` of pipeline `933270` rather than estimated (I-037).

  **(1) Declaring the Spanish dictionary is IMPOSSIBLE on this runner. The earlier ★ was wrong.**
  Verified 2026-08-23: the job runs Drupal core's cspell install; `@cspell/cspell-bundled-dicts`
  declares **59 dictionaries and no `@cspell/dict-es-es`** — the only natural-language dicts are
  `dict-en_us`, `dict-en-gb-mit` and `dict-en-common-misspellings`. `"language": "en,es"` resolves to
  no dictionary. Vendoring a Spanish word list is mechanically possible and **rejected**: shipping a
  Spanish dictionary inside a site template to pass a spell check is not defensible at review. The
  error corrected is asserting a capability without checking the installation — **I-042**.

  **(2) No project `.cspell.json`.** The job honours one (`main.yml:1303-1309`), but the documented
  variables cover everything we need, and a custom file both freezes us against upstream improvements
  to `assets/.cspell.json` and triggers a `[WARNING]` banner if it omits any default flagged word
  (`prepare-cspell.php:168-173`). We use `.cspell-project-words.txt` (the default value of
  `_CSPELL_DICTIONARY`, registered only if the file exists) plus `_CSPELL_EXTRA`.

  **(3) The 146 decompose into four buckets, and only one of them is a word list.**

| Bucket | ~ | Treatment | Why it is honest |
|---|---|---|---|
| British-English spellings — `behaviour`, `licence`, `colour`, `organisation`, `artefacts`, `normalise` | 30 | **`_CSPELL_EXTRA: '--locale en,en-GB'`**; `dict-en-gb-mit` **is** bundled | We do write British English. Declaring the actual language of the text is not silencing a check |
| Project vocabulary and proper nouns — `Ágora`, `Andrés`, `drupalcode`, `gitaly`, `caresphere`, plus our own shell/Python identifiers | 75 | `.cspell-project-words.txt`, **one justified line each** | The file's documented purpose |
| Spanish identifiers — `proyecto`, `fundacion`, `decisiones`, `contenido`, `publicacion`, `tema` | 40 | **Rename the paths to English** | A standing violation of **D-017** (*"the ENTIRE repository is in English"*) that cspell has now made public. The token leaves the corpus; nothing is declared |
| Real misspellings | ≥ 2 | **Fix them** | <!-- cspell:disable -->`encontro` coexists with `encontró`, `fundacion` with `fundación`<!-- cspell:enable --><!-- this cell quotes real misspellings verbatim as its own subject matter; rule 8 forbids rewording the row, so the misspelled forms are scoped out here rather than "corrected" -->. This is the bucket that justifies the exercise |

  **(4) Explicitly forbidden.** `_CSPELL_IGNORE_PATHS` covering `specs/`, `CLAUDE.md`, `AGENTS.md` or
  `.claude/` — those are our own English prose, D-017 puts them in scope, and excluding them would
  delete the fourth bucket without looking at it. And copying `_cspell_updated_project_words.txt`
  (the job's own "your dictionary plus everything that just failed") over the word list: **one
  command to green, and an automatic 🔴**. Every line entering that file must survive the question
  *"why is this a word this project legitimately uses?"*

  **(5) Scope of the rename, bounded deliberately.** Only `specs/000-proyecto/` → `specs/000-project/`,
  `specs/001-fundacion/` → `specs/001-foundation/`, `DECISIONES.md` → `DECISIONS.md`, with their
  references swept — **including four shell scripts** that hard-code the paths
  (`gate-a-wave1.sh`, `gate-a-wave3.sh`, `no-boilerplate`, `sbom-check`, whose T-306(g) clause names
  `DECISIONES.md`). **Not renamed:** `.claude/agents/*` (their filenames are `subagent_type` names —
  a functional change), `.claude/commands/decisiones.md` (renaming it changes the `/decisiones`
  command), the four Spanish skill directories, and the not-yet-started unit directories
  `002-base-tema` etc. Those go in the word list as signed identifiers, and `[ejecutor]` is pinned by
  non-negotiable rule 7. The remaining inconsistency is **recorded rather than half-fixed**.

- **Note on D-024 — the local pre-flight now reproduces the gate, because the version that did not
  cost three red pipelines.** Recorded [ejecutor] 2026-08-24. D-024 settled *what* to do with each
  bucket of unknown words and that part held: the buckets are still the four it named, and this
  turn's ~250 new words were sorted into them without inventing a fifth. What D-024 did **not**
  settle is how anyone would see the job's verdict before pushing, and the README's answer was a
  bare `cspell` invocation that loads **no** dictionary this repository or Drupal core provides.
  It printed 905 findings against a job that finds none, so it was unreadable, so it went unread,
  and `cspell` failed on `934242`, `934297` and `934329` — three commits reported as clean.
  `tests/bin/spellcheck` replaces it: it fetches `gitlab_templates`' `assets/.cspell.json`, Drupal
  core's two dictionaries and this project's word list, applies `prepare-cspell.php`'s
  transformations, reads tracked **and** stage-able files, and prints its denominator (I-045).
  Verified against `934329` itself — same verdict before the fix, `65 files · 0 issues` after — with
  a dirty case proving exit 1. **No decision is reversed here.** D-024(2) still holds (no project
  `.cspell.json` is committed; the replica config is built in a git-ignored cache), D-024(4)'s
  prohibitions still hold, and the new lines in the word list carry one reason each. The addition
  is procedural: **a pre-flight either reproduces its gate or it is deleted** (I-051).
  ⚠️ Scoped, not silenced: the research file that quotes four Spanish statutes carries ~190 words
  in its own `cspell:ignore` header — D-024(3)'s *"scope the quotations"* applied literally — while
  only the 53 terms Ágora itself uses as node types and fields entered the project dictionary.
  `_CSPELL_IGNORE_PATHS` was **not** used on it: English spelling is still checked in that file.

- **Note on D-014(e) — security advisory coverage has a waiting period, and it is a quality clock.**
  Recorded 2026-08-24 from [andres], who read it on the application page.
  D-014 rider (e) says *"when creating the theme project, opt in to security team coverage"*, and an
  earlier reading of mine assumed the gate was **a stable release**. That is wrong, or at least not
  the binding constraint: **a new project cannot apply until it is at least ten days old**, and the
  Drupal Association uses that window explicitly to ask for quality code and good practice, so that
  the review does not turn up problems.
  *Consequences, none of which change the plan:*
  · `drupal/agora_theme` was created **2026-08-24**, so it becomes eligible on or after
    **2026-09-03**. `drupal/agora_transparency` was created **2026-08-22** → eligible from
    **2026-09-01**.
  · The ten days are **not** dead time. They are exactly the window in which unit 002 writes the
    theme, and what lands in it is what the coverage review will read. That reframes the waiting
    period from an obstacle into the reason the unit's standards matter on the first commit rather
    than at its gate.
  · Neither project's page can claim coverage before then; both currently show *"not covered by the
    security advisory policy"*, which is accurate and must not be papered over in the README (I-023).
  *Correction of method, not just of fact:* I asserted the stable-release prerequisite from memory
  instead of reading the page. That is **I-042** — a capability asserted without checking the
  installation that grants it — and it is the third time this pattern has been caught by someone
  else rather than by me.

- **Amendment to D-020 — the install smoke moves onto the canonical gate and becomes blocking
  there. GitHub keeps running and stops being the only place it runs.** **SIGNED [ejecutor]
  2026-08-24 under [andres]'s standing delegation**, on the same footing as D-020 itself, which he
  also delegated: this is **gate methodology with no product trade-off**, and the `orquestador`
  recommended the same route. Unblocks **T-511**.

  *What changes:* the template's `.gitlab-ci.yml` declares `OPT_IN_TEST_DRUPAL_CMS: '1'` and
  `_AUTORUN_DRUPAL_CMS: 'all'`, so the `Drupal CMS` job **materialises in the observed job list**
  with `allow_failure: false`. The gate's floor rises from `jobs >= 8` to **`jobs >= 9`**, and per
  the derived-list prohibition the CLAUDE.md and README tables are rewritten **in the same commit
  that makes it true** — an observation and the commit that changes it never travel separately.

  *What does NOT change, and this is the half worth stating:* **D-020's holding stands entirely.**
  GitHub remains an *informative* surface — it may fail without blocking, it may never lie, and no
  wave closes on its green. It is not deleted here; D-020 rider (a) already sets its expiry at unit
  007. What ends is its **monopoly**: from D-020's signing until today, the only place a real
  Drupal, this package and these tests were ever assembled was a mirror a Drupal.org reviewer
  cannot re-run. That was tolerable while the canonical remote had **no project**. It has one, and
  eight blocking jobs on it, so the tolerance has expired on its own terms.

  *Why now rather than in wave 7, which is where the swap lives.* The `Drupal CMS` job resolves the
  template's dependencies from `packages.drupal.org`, and wave 7 is precisely when the template
  starts requiring `drupal/agora_theme`. Landing the job **today**, on a tree that requires no
  theme, buys a **known-good baseline**: when it goes red after the swap, the red is attributable
  to the swap and to nothing else (**I-037** — a matching count is not an attribution). Promoting
  it in wave 7 would introduce a new job and a new dependency in the same window and leave nobody
  able to say which one broke.

  ⚠️ **Two failure modes are named in advance, so neither can be argued away when it appears.**
  (a) If the job materialises **permissive**, D-023(5) requires a dated, owned exception written
  into `.gitlab-ci.yml` — the exception list is empty today and a silent permissive job would
  break the gate rule the moment it exists. `_ALL_VALIDATE_ALLOW_FAILURE: '0'` covers the
  **validate** stage only, and this job is not in it.
  (b) If the job **does not materialise at all**, that is a **failure to report, not a pass**:
  `jobs >= 9` is unmet and T-511 is not done. A job that was asked for and did not appear is I-050
  exactly — *defined ≠ materialised ≠ collected ≠ executed* — and the temptation will be to call
  eight jobs "still green".


- **D-032 gains a step 4b, and its central claim is now MEASURED rather than argued.** Recorded
  [ejecutor] 2026-08-24 from the export rig's own numbers. The rig exists, it installs clean, and
  the procedure was exercised before anything was built on it.

  **The claim that decided D-032 is confirmed by counting:** `_core.default_config_hash` appears on
  **398 live config objects** and on **0 of 454 exported files**. The exporter really does delete
  the only marker distinguishing *"this came from an extension"* from *"this is ours"*.

  **And the replacement discriminator works.** A throwaway slogan change, exported and diffed
  against the baseline, named **2 files** — and the second was measured away rather than excused:
  `SiteExporter::writeComposerJson()` names the package after the **destination directory**, so
  exporting into a same-basename destination diffs at **1 file, 1 line**. The strongest form: revert
  the change, export again → **0 differing files out of 541**. The export is **byte-stable run to
  run**, which is what makes a diff-based procedure trustworthy at all.

  ⚠️ **STEP 4b, and it changes how T-601 is executed.** The slogan **never appeared in `config/`**
  — both exports' `config/` are byte-identical at 454 files. `SiteExporter::isAction()` routes
  anything shipped as **default config by core, System or User** into `recipe.yml` **as a config
  action** instead. So D-032 step 4 (*"copy changed files into `config/`"*) is correct for node
  types, fields and views — and **silently captures nothing** for core/System/User config, whose
  only artefact lands in the one file step 5 forbids copying. Those changes are **read from the
  export's `recipe.yml` and hand-transplanted into the correct `# -- area:` block**. A modelling
  session that changes a System setting and copies only `config/` will lose it **with no error**.

  *Three hazards the rig surfaced, none of them anticipated:*
  · **The read-only mount earned itself on the first `composer update`.**
    `drupal/site_template_helper`'s Composer plugin writes a `version` key into the installed
    recipe's `composer.json` on every install — and under a symlink **that file is this
    repository's hand-maintained manifest**. The mount refused the write. Verified after:
    **0** `version` keys in `composer.json`, `git status` empty. Without the read-only mount, the
    template's manifest would have been edited by a tool nobody invoked.
  · **Why the older rigs got copies is a setting, not a mystery:** `COMPOSER_MIRROR_PATH_REPOS=1`
    in both `.ddev/config.yaml`, inherited from this repository's own GitHub workflow, where it is
    deliberate and correct. Composer's default is to symlink.
  · **Rule-1 exposure observed verbatim in the export log** — *"Falling back to an allow-all (`*`)
    constraint"* for `drupal/blank`. D-032 rejected option A on reasoning; this is the sentence
    that would have proved it.

  *Numbers worth quoting with their scope (I-045):* the baseline is **541 files**, of which **454
  are `config/`** and **72 were mirrored in from the checkout by `--base`**. The honest denominator
  for "what the site contains" is **454**, not 541. The export wrote **13** content files while the
  working copy's `content/` still holds **1** — which is exactly why D-032 step 6 discards the
  export's `content/` wholesale.

  *The rig, stated so it can be rebuilt rather than remembered:* `~/agora-export`, **not a git
  working copy and containing no clone** — the machine still has **three** clones, not four, and
  the path repository points straight at the Windows checkout through `/mnt/c`, measured fast
  enough (593 files enumerated in 0.5 s, baseline export 12 s). `require` is **5 entries, 0
  unstable**; the resolved lock is **153 packages, 0 non-stable**; **11 out-of-scope packages
  checked, 0 present**. It installs to `install_finished` with **no AI key in the environment** and
  no AI module in the closure — I-003 holds. `drupal/core-recipe-unpack` and
  `drupal/core-project-message` were **removed deliberately**: unpack would flatten the template's
  requirements into the rig's own `require` and can dissolve the path-repo entry the rig exists for.


- **Corrections to D-032, measured the same day it was signed.** Three of its supporting numbers
  were wrong and one of its arguments was aimed at the wrong target. **The holding stands** —
  export authoritative for `config/` only, through a baseline diff — but a decision whose evidence
  is not re-measured is a decision that decays (I-047), so:
  · **Ten upstream recipes, not eleven.** Eleven only if Ágora counts itself. Corrected in place
    above; the wrong number had already reached two files.
  · **`drupal_cms_search` is not in the rig's `require` at all**, and `search_api` is not
    installed. Four of the five packages I named are real; that one was invented by repetition.
  · **The pollution argument was aimed at the wrong thing.** None of `byte`, `haven`,
    `drupal_cms_ai`, `webform` or `search_api` is **enabled** on either rig, so an export would not
    have captured their config. **The real thing that rules them out is different and worse:**
    `recipes/agora_transparency` is a **copy, not a symlink**, in *both* rigs — so anything an
    export loop writes lands in the rig's copy and **never reaches the working copy**, and the next
    `composer install` deletes it. The purpose-built rig is still required; the reason is
    mechanical, not hygienic.
  · Worth recording rather than discarding: the rig's `require` carries **two unstable
    constraints** (`drupal/webform` at beta, `drupal/drupal_cms_site_template_base ^1@dev`).
    `tests/bin/no-unstable-deps` correctly does not see them — its scope is **this** repository's
    `composer.json` — but D-032=A would have copied that `require` into the template, which is
    exactly the rule-1 exposure the option was rejected for. The option was rejected on reasoning;
    this is the measurement that would have proved it.


- **Correction to D-018 — `drupal/site_template_helper`'s justification described a block that no
  longer exists, and the invariant could not tell.** Recorded [ejecutor] 2026-08-26 after T-806's
  audit. ⚠️ **This corrects a FALSE STATEMENT; it does not decide whether the dependency stays.**
  That second question is an SBOM change and belongs to [andres].

  D-018 justified the package as *"Composer plugin that generates the `blank` theme declared in
  `extra.drupal-site-template`"*. **The swap deleted that key**, so as of `9dc5722` the sentence
  describes a block that is not in `composer.json`.

  *What the package actually does, read at the version that actually resolves.* An earlier reading
  of mine said `generateTheme()` was now a no-op and left it there — which was true of **1.0.3**
  and wrong about the installed one. Observed in job `11771135`:
  `Locking drupal/site_template_helper (1.0.4)`. At **1.0.4**, `onPackageInstall()` **stamps a
  `version` key into an installed recipe's `composer.json`**, and the package's own comment says
  why: *"so we can derive the URL where we can download translations"*. So the dependency has a
  real, current function — and it is **coherent with D-033**, which put this project's
  translations on localize.drupal.org rather than in the repository. `generateTheme()` is indeed
  inert now, because it requires the deleted key; the package is not.

  ⚠️ **The finding underneath is about the invariant, not the package.** `sbom-check`'s
  `decision_line()` requires a line carrying both a `D-NNN` token and the package name. It checks
  that **a line exists**, never that it is **true** — so a justification can rot into fiction with
  the gate green throughout. That is a real limit of the mechanism and it is recorded rather than
  papered over: a machine can enforce that a reason was written, and cannot enforce that it still
  holds. **The audit is what catches this class, and it did.**

  ⚠️ **Still open and NOT decided here:** whether the template should keep depending on a plugin
  whose only remaining function serves a translation-download URL this project does not use today.
  Dropping it is an SBOM change under rule 2 and needs [andres]. **Owner: [andres]. Target unit:
  006**, with the SBOM sweep — named now, because a finding with an owner and no unit is how a
  finding becomes permanent.


- **D-034** · **`sbom-check` gets its first named exception: `drupal/agora_theme`, dated, printed,
  and expiring by FAILING.** **DECIDED BY [andres] 2026-08-25**, and this line contains the
  `D-034` token beside `drupal/agora_theme` on purpose — `sbom-check`'s `decision_line()` requires
  exactly that pairing, so this entry is both the decision and the record the invariant reads.

  *The problem in one line:* the template must declare the theme in `require`, and `sbom-check`
  demands security-advisory coverage for every `drupal/*` entry. Measured on the real release:
  `<security>` is present with **no `covered` attribute** and the text *"Project has not opted
  into security advisory coverage!"*, so the parser's `security.get('covered', '0')` returns
  **`'0'`** and files a finding. The project is **not eligible to apply until 2026-09-03**, ten
  days after creation.

  ⚠️ **A cost this project overstated, corrected by [andres] and worth recording because the
  overstatement was the argument for option B:** both the audit and I wrote that waiting would
  cost *"nine days plus an unbounded Drupal Association review"*. **That is false.** He already
  holds the role to opt any project into coverage once the ten days elapse; **no third-party
  review is involved.** The Association's review is the **marketplace** submission, which is a
  different gate at a different time. So option A's real cost was **nine days, bounded** — and B
  was chosen on its merits rather than because the alternative was frightening.

  | | Option | Real cost |
  |---|---|---|
  | A | Wait: land the swap only once `covered="1"` | **Nine days, bounded** (corrected above). Waves 5 and 6 sit green with nothing able to close them |
  | **B ★** | **Land the swap with a named, dated exception that expires by failing** | One exemption inside a gate-A invariant — the first this project has ever granted |
  | C | Land it and tolerate a red `agora-invariants` until coverage arrives | **Rejected.** Rule 9, and the wave-1 rider's own words: *"A gate A with a permanent red is NOT accepted"* |

  ★ **B**, chosen by [andres]: <!-- cspell:disable -->*"vamos haciendo igualmente, y el día 3 ya activo la cobertura"*<!-- cspell:enable -->.

  *The argument that makes this a reading of the rule rather than a hole in it:* **D-004 and
  non-negotiable rule 2 are written against THIRD-PARTY risk** — the danger of depending on code
  nobody maintains. `drupal/agora_theme` is **first-party**: same maintainer, same project, same
  session, **nine blocking jobs green**, and an accessibility gate that has been **seen to fail**.
  Applying a rule written against third-party risk to our own theme would satisfy its letter
  against its purpose.

  **Three properties are mandatory, and without any one of them B collapses into a silent pass:**
  (1) the exception is **named** — it exempts `drupal/agora_theme` and nothing else, never a
  wildcard, never a category; (2) it is **printed in the summary on every run**, so a reader sees
  an exemption was applied rather than a clean sweep — the script's line reads
  `exclusions: none` today and must stop saying that; (3) it **expires by FAILING on 2026-09-03**,
  not by warning and not by silently continuing. An exemption that lapses quietly is worse than
  no exemption, because it leaves a gate that looks intact.

  ⚠️ **The expiry is actionable rather than hopeful**, and that is what distinguishes it from a
  deferral: it does not wait on a third party. **[andres] holds the role and activates coverage on
  2026-09-03**; on that date the invariant fails until he has, and the failure is the reminder.

  *Scope, stated so it cannot creep:* this exempts **one package** from **one check** — the
  security-coverage clause. `drupal/agora_theme` must still satisfy every other clause:
  a stable release, no dev/alpha/beta/rc constraint, and this very D-NNN line. It is queried like
  any other entry and appears in the counts.

- **Amendment to D-034 — the exemption is DISCHARGED and DELETED, 2026-09-05.** Nothing above is
  edited (rule 8); this records how it ended, because how an exemption ends is the only part of it
  that was ever in doubt. ⚠️ **This paragraph also carries the `D-034` token beside
  `drupal/agora_theme` deliberately**, exactly as the decision above does and for the same reason:
  `sbom-check`'s `decision_line()` requires that pairing, and after the discharge the package's
  SBOM justification should rest on a live statement rather than only on the text of an exemption
  that no longer exists.

  **The three mandatory properties were all exercised, and the third one fired.** ⚠️ **All of it was
  reproduced today rather than recalled from August**, against a **copy** of the invariant serving a
  **local fixture** — a real release-history document with `covered="1"` removed and nothing else
  changed — so each state below is something that was watched happening, not something the code was
  read and believed about.
  · It was seen **APPLIED**: with the clock before the expiry date and a release reporting
    `covered="0"`, the invariant printed `agora_theme … 0 exempt`, `APPLIED: its stable release
    reports no coverage, and that one finding was withheld`, `findings: 0`, exit **0** — so the
    exemption really did excuse something and was not decorative.
  · It **EXPIRED BY FAILING on 2026-09-03**, exactly as designed and with no intervention: the
    same input on 2026-09-05 printed `0 LAPSED`, `EXPIRED on 2026-09-03: the missing coverage is a
    FINDING again, not an exemption`, and exit **1**.
  · **It was never silently extended, and that is a measurement rather than a recollection.**
    `git log -S 'EXEMPT_UNTIL="' -- tests/bin/sbom-check` returns **exactly one commit**, `9dc5722`
    (*"the atomic swap — the template ships Ágora's own theme"*), and the only value that line ever
    held is `2026-09-03`. No commit moved it; the one repair that would have made the red go away
    was the one repair the code named as unavailable.
  · The clock hook could not have hidden it either, and that was **re-falsified rather than
    recalled**: `SBOM_CHECK_TODAY=2026-09-02` on 2026-09-05 aborts with *"the clock hook moves
    forward only, so it can never hide an expired exemption"*. Forward-only, as written.

  **Coverage arrived on 2026-09-05**, when [andres] opted `agora_theme` into the security advisory
  policy on drupal.org. `sbom-check` then printed, of its own accord and without anyone asking it
  to, `NOT NEEDED: drupal/agora_theme reports coverage of its own, so the exemption excused
  nothing and can be deleted` — the sentence the script was written to be able to say. It now
  reports **10 projects queried · 10 with coverage · 0 findings**.

  **What was deleted, and what deleting it costs.** The whole block: `EXEMPT_PROJECT`,
  `EXEMPT_CLAUSE`, `EXEMPT_UNTIL`, `EXEMPT_DECISION`, the injectable clock and its
  `SBOM_CHECK_TODAY` test hook, the four bookkeeping counters, the two `elif` branches inside the
  coverage clause and the six summary lines. It was hard-wired to one project name, so **removing
  it removes the ability to grant a cheap exemption, and that is intended rather than overlooked**:
  an empty exemption list is a ready-made hole, and pricing the next exemption at one line is the
  opposite of what a gate is for. A future exemption costs a signed decision plus re-implementing
  the three properties — and the working implementation sits in this file's git history at the
  commit before the deletion, so the real price is reverting a known-good block, not designing one.

  ⚠️ **Falsified before it was believed, in both directions and in one run.** A hermetic copy of
  the invariant was pointed at two local fixtures that are the same release-history document
  differing **only** in the `<security>` element: the covered one passed, the uncovered one
  produced `has no <security covered="1"> (covered="0"); D-004 requires security-team coverage`,
  and the run exited **1**. Then the uncovered fixture was renamed to `agora_theme` and served
  again: **still a finding**, which is the measurement that separates *"the exemption is gone"*
  from *"the exemption was not reached"*. The `exclusions:` line did not disappear with the
  exemption — it now prints `none — every drupal/* entry in require is held to every clause above`
  on every run, for the same reason property (2) required the presence to be printed.


- **D-033** · **The language of shipped config strings is ENGLISH, and the Spanish is a
  translation that does not live in this repository.** **DECIDED BY [andres] 2026-08-24**, in his
  own words — <!-- cspell:disable -->*"El idioma de los proyectos en Drupal.org SIEMPRE es inglés,
  la traducción de los proyectos NO se sube al mismo repo, revísalo bien porque creo que las
  traducciones a los diferentes idiomas van por otro lado."*<!-- cspell:enable -->
  He was **right**, and he was correcting **me**: I had ruled the opposite (*"labels carry the
  Spanish"*) in the dispatch that produced T-601, and that ruling shipped. Verified at source
  before acting, because a correction accepted on authority is still an unverified claim.

  **What the verification found, with citations rather than recollection:**
  · **English is not a preference, it is the precondition for being translatable at all.** Core's
    `LocaleConfigManager::isSupported()` reads
    `getDefaultConfigLangcode($name) == 'en'`, and `translateString()`'s docblock says it plainly:
    *"we only know how to translate strings from English so the source string should be in
    English."* Config whose default langcode is not `en` is **outside the mechanism by
    construction**.
  · **Translations left drupal.org's repositories in 2011 and the announcement is unambiguous:**
    *"the drupal.org projects themselves should not have `.po` files committed and maintained…
    Project maintainers should not accept `.po` files anymore"* (localize.drupal.org/node/3044).
    Measured rather than trusted: **23 recipes on the rig, 2,273 files, 0 `.po`, 0 `.pot`, 0
    `translations/`.**
  · **Drupal CMS's own recipes agree, and the number is the argument:** 706 config files across 13
    `drupal_cms_*` recipes, **660 `langcode:` lines and every one `en`**, 784 label/name/description
    lines of which **8 carry a non-ASCII byte and all eight are typography** (an em dash, a `×`).
    **Zero non-English strings.** Both published site templates match.
  · ★ **The precedent that settles the hard case: `dsfr`, the French State Design System.** Its
    `.info.yml` and config are **English**; it ships `translations/dsfr-fr.po` and 456 source
    strings on the localisation server. **The French government writes English in its own Drupal
    theme and delivers French as a translation.** That is exactly the objection — *a Spanish clerk
    does not call it an Agreement* — answered by the closest possible analogue.

  ⚠️ **What was actually on disk was worse than "Spanish labels".** All five vocabularies declare
  **`langcode: en` while carrying Spanish text** — a false statement in the **one field the
  machinery consults**. That is red under any option: A fixes it by changing the text, B by
  changing the langcode. It could not stay.

  | | Option | Real cost |
  |---|---|---|
  | **A ★** | **English labels and descriptions; Spanish arrives as a translation** | On install day a Spanish clerk sees English labels. Real, and the pitch's soft spot |
  | B | Keep Spanish, correct `langcode: es` | Honest, and it **kills the future**: `isSupported()` requires `en`, so the strings become permanently untranslatable by the standard mechanism, and Ágora stands alone against every measured precedent |
  | C | English labels **plus** a Spanish translation shipped inside the template | **Rejected on mechanics, not taste.** `RecipeConfigInstaller` hard-codes `DEFAULT_COLLECTION`; `ConfigConfigurator` opens `config/` with a non-recursive `FileStorage`; a `translations/*.po` needs an `.info.yml` key and a site template has **zero** `.info.yml`. **There is no seam.** ⚠️ The same trick **is** available to `drupal/agora_theme`, which has one — an experiment for unit 005/006, recorded as an experiment and never as a promise |
  | D | English label + the Spanish statutory term in the description | Rejected as a **general** rule — it puts a second language back into shipped config. **Kept in narrow form**, below |

  ★ **A, with D's narrow form for exactly three terms.** `convenio`, `subvención` and `importe de
  adjudicación` are cases where the English is a correct translation and still the wrong word — so
  their labels are English and the Spanish term is named **once, in the description, as a legal
  citation**, which is what a description is for. That is a citation, not bilingual UI. It binds
  **T-612–T-615**, which are not yet written, so it costs nothing to impose now.

  ⚠️ **This does NOT reopen D-026.** No bundle, no field and no statute article moves; machine
  names were **already English** (`agora_base_area`, `field_agora_base_amount`) and stay. Only the
  language of label strings, and where a translation lives.

  *Two consequences with owners:* (a) the five shipped vocabularies are corrected **through the
  export rig, never by hand** — D-032=B is signed and a label change is a modelling change — and
  the correction lands **before T-612**, so six bundles and ~30 fields are not exported twice;
  (b) the 13 `.cspell-project-words.txt` entries justified as *"the Spanish that T-601 ships inside
  `config/`"* are **removed in the same commit**, because a justification that outlives its reason
  makes the word list lie about why its entries exist. The earlier ~40 entries justifying Spanish
  in `specs/` prose and in the three legal citations **stay**.

  *Cost in task rows: zero.* This changes the **content** of T-601's output and writes a constraint
  into rows that have not run. **D-031's headroom −2 is untouched.**

  ⚠️ *Two things NOT established, recorded so nobody quotes them as settled:* **no site-template or
  marketplace document names a language requirement** — the review list is five items and language
  is not among them, and the Creator Guide it points at is unreachable, so this decision rests on
  core's machinery and on precedent, **not on a written rule**. And **extraction is not delivery**:
  a recipe's config strings *can* now be extracted to the localisation server (potx gained recipe
  support 2026-04-09, and `haven` shows 620 source strings dated the day potx 2.0.0-alpha1 was
  tagged) — but **four independent locks stop those translations reaching an installed site**, the
  sharpest being that a site template's config carries no `_core.default_config_hash`, which
  `isSupported()` requires, **and our own `RequirementsTest` forbids it**. Re-measure at unit 007
  rather than quoting this date (I-047).

  ⚠️ **CORRECTION 2026-08-26 (D-035's research): the sentence above names the WRONG lock, and it is
  false as written. D-033's HOLDING IS UNCHANGED and is now better supported — but a decision that
  is right for a wrong reason is one the next person to check will reopen.** Not edited (rule 8).

  **What was measured**, on `~/agora-smoke` with the Ágora recipe applied: **103 of 104**
  Ágora-named *active* config objects **DO carry `_core.default_config_hash`**.
  `RecipeConfigInstaller::installRecipeConfig()` calls `createConfiguration()` in the default
  collection, and `ConfigInstaller` adds the hash there unconditionally. So `RequirementsTest`'s
  `assertArrayNotHasKey('_core', …)` is a statement about **the YAML in the package**, while
  `LocaleConfigManager` reads the hash from the **active store in the database**. They are different
  objects, and the test is not the lock this sentence claims.

  **The locks that actually hold are five, and the first is absolute:**
  1. **A site template can never be a translation project at all.**
     `LocaleProjectRepository::getProjectList()` builds its list from the **module and theme**
     extension lists only; `RequirementsTest` requires **0 `*.info.yml`** in the package. No project
     entry, no server pattern, no fetch, ever.
  2. **Recipe config is invisible to the storage `isSupported()` reads** —
     `LocaleDefaultConfigStorage` is two `ExtensionInstallStorage`s over modules, themes and
     profiles, and a recipe's `config/` is none of the three. Measured: **0 of 102** supported,
     against **208 of 520** for everything else in the same database. **This is what "sharpest"
     should have said**, and it holds regardless of the hash.
  3. `RecipeConfigInstaller` writes only `DEFAULT_COLLECTION`, so the language override collection
     is unreachable. 4. `ConfigConfigurator` opens `config/` **flat**. 5. There is no feature to
     reach for: `Drupal\Core\Recipe` never mentions translation in any of its 21 files.

  ⚠️ **And it is a property of RECIPES, not of us.** In the same database, `node.type.page` and
  `taxonomy.vocabulary.tags` — shipped by **Drupal CMS's own recipes** — report `isSupported`
  **FALSE**, while module-shipped config reports TRUE. **Drupal CMS ships an untranslatable content
  model too.**

  ⚠️ **One consequence outside this record:** T-1001's success criterion said *"no `_core` key on any
  new file, **which is also what keeps D-033's four locks accurately described**"*. The first half is
  a real packaging requirement and stands; the clause after it rested on this false premise and is
  corrected on that row.


- **D-032** · **What is the authoritative producer of `config/`, `recipe.yml` and
  `composer.json`?** **SIGNED [ejecutor] 2026-08-24 under standing delegation** — this is
  mechanics, not a product trade-off: it decides which tool writes which file, and every option
  ships the same template. Blocks everything from T-601 onward. **Costs no task row.**

  *Context in one line:* T-601's row says the model is *"exported with `drush site:export` into
  `config/`"*, and **executed literally that command does not update `config/` at all** — it writes
  a whole new recipe elsewhere, regenerates `recipe.yml` from a four-key array, and **replaces**
  `composer.json`'s `require`.

  *Read at source in the rig, not recalled* (`drupal_cms_helper/src/SiteExporter.php`,
  `Drush/Commands/SiteExportCommand.php`):
  · `--destination` defaults to `recipes/site_export`; the command **refuses to run** if the
    destination exists without `--overwrite`. The skill's flow therefore writes a recipe next door
    that nobody reads, and `config/` is untouched.
  · `recipe.yml` is **regenerated** from `name`/`type`/`install`/`config` via `Yaml::encode()`. So:
    **there is no `recipes:` key in the output** — Ágora's ten upstream recipes vanish and their
    config is inlined; **every comment is destroyed**, and most of that 9,491-byte file is D-011's
    seam convention, the area labels and D-021's rationale for the exact `name:` string; `install:`
    becomes ~100 entries instead of three.
  · `$data['require'] = $this->getExtensionRequirements($extensions)` — an **assignment**, one
    `^<installed-version>` per installed extension. Where a version cannot be determined it emits a
    raw dev constraint (`'*'` in the `catch`): **direct rule-1 exposure**.
  · It `unset`s `extra.drupal-site-template`, which means an export in wave 6 would **silently
    perform half of wave 7's T-702** and falsify T-702's criterion that the change be *its own*.
  · `--base` defaults to the kit's base recipe and mirrors it in, putting `GET-STARTED.md` and the
    kit's README back — **the boilerplate T-103 deleted**.
  · It exports **all content** into `content/`.

  | | Option | Real cost |
  |---|---|---|
  | A | The export is authoritative for all three files; the repository is regenerated from the site | Loses the ten upstream recipes, the seam convention, D-021's rationale and deliberate SBOM control; performs half of T-702 by accident. **This is what T-601's row literally says today.** |
  | **B ★** | **The export is authoritative for `config/` only, and only through a baseline diff. `recipe.yml` and `composer.json` stay hand-maintained and are never written by the tool** | One extra export (the baseline) per modelling session, plus a documented transplant step for `install:` entries and `require` additions |
  | C | Hand-write `config/` YAML directly; never run `site:export` | 60-100 files hand-authored including entity view and form displays, with schema keys wrong in ways only an install surfaces |

  ★ **B.** The only option where the export's output is **reviewable** — because the baseline gives
  it a denominator — and the only one where the seam convention and the SBOM survive contact with
  the tool.

  ⚠️ **The reason the baseline is not optional, and it is the finding that decided this.**
  `_core.default_config_hash` is the marker that says *"this config came from an extension's
  default config, not from you"* — **and the exporter strips it**. After a `site:export`, Ágora's
  own config is **indistinguishable by inspection** from the config the ten upstream recipes
  supplied. The only discriminator left is a **diff against a baseline export of the same site
  without Ágora's changes**. Neither the skill nor any task row mentioned such a step.

  *The procedure B mandates:* (1) a purpose-built rig whose `require` is exactly Ágora's dependency
  closure — **not `~/agora-cms`**, whose `require` carries Byte, Haven, `drupal_cms_ai`,
  `drupal_cms_forms` and more, **none of which is in Ágora's SBOM** ·
  (2) **baseline export first**, before touching anything · (3) model, then export again ·
  (4) **the artefact is `diff -r baseline after`, never `after`** — only files that appear or change
  are copied into `config/` · (5) `recipe.yml` and `composer.json` are **never taken from the
  export**; its versions are read as *information* and the relevant lines transplanted by hand into
  their area block, each new package earning its `DECISIONS.md` line per rule 2 · (6) the export's
  `content/` is **discarded wholesale** in unit 002 · (7) pre-commit review adds four checks the
  skill lacks: `config/` file count printed and **> 0**, `git status --porcelain content/` empty,
  ⚠️ **RETIRED 2026-08-26, in the commit that first made it false — by name, never deleted.**
  `content/` now holds **3** files: the blank Canvas landing page it was written about, plus
  `MEDIA-LICENCES.md` and `PEOPLE.md`, the two manifests T-904 and T-905 read. The check said `1`
  because at the time anything else in `content/` was an accident. That stopped being true the
  moment this unit shipped manifests, and it will stop being true again at every wave of unit 003.
  **Its replacement is mechanical rather than a number in prose**: `tests/bin/media-licence`
  asserts `content entries (working) >= 1` **and** `content entries (packaged) >= 1`, both FATAL at
  zero, on every gate run — which is what the `1` was actually guarding (that the enumeration
  happened at all), stated in a form that survives the directory filling up. Neither manifest is
  content: Drupal's `Finder` globs only `*.yml` and `*.json`, so a `.md` file under `content/` is
  inert to the importer while still shipping in the tarball, which is exactly what a manifest a
  reviewer must be able to read needs to do. The original text is not edited (rule 8):

  `find content -type f | wc -l` still **1**, and `git diff recipe.yml` showing only hand-made,
  area-blocked changes.

  *Three failure modes are caught by **nothing** today* — `recipe.yml` silently replaced, content
  silently exported into `content/`, and **`config/` silently empty**. The last is live right now:
  `RequirementsTest` builds a `FileStorage` over `config/`, **which does not exist**, so
  `listAll()` returns `[]` and the assertion block passes with nothing built. Procedure B prevents
  all three; only a script would **detect** them. Whether that script is written is a **separate
  question for [andres]**, because at headroom −2 it costs a signed rider naming a displacement.


- **Correction to the Amendment to D-020, same day, before it was acted on.** The amendment said
  the floor rises *"from `jobs >= 8` to `jobs >= 9`"*. **`8` was never the floor.** D-023(5) sets
  the floor at **`jobs >= 7`**; `8` is the *observed count* of pipeline `934387`. The amendment
  conflated the rule with the measurement — which is exactly the confusion D-023(5) exists to
  prevent, committed inside the sentence that cites it. Read it as: **the floor rises from
  `jobs >= 7` to `jobs >= 9`**, and the observed count is expected to go from 8 to 9.
  Caught by the implementer reading the quoted decision instead of trusting the amendment's
  summary of it. Recorded as a correction rather than an edit (rule 8): a decision that had to be
  re-read to be understood is evidence about how it was written.


- **Amendment to D-028 — `no-unstable-deps` is the fifth shared invariant. Its enumeration was
  incomplete, and the gap was in the one place a gap is invisible.** Recorded [ejecutor]
  2026-08-24, under standing delegation. **This completes D-028's own list; it does not add work
  to the unit** — no new task row, so D-031's headroom −2 is untouched. Said explicitly because
  "it's small" is the exemption this project does not grant, and the distinction being relied on
  here is *completion of a signed enumeration* rather than *new scope*.

  *How it surfaced:* the `desarrollador` implementing T-508 noticed that the template's
  `tests/bin/` holds **14** files while D-028 names **9** — four shared, five excluded. Of the
  remaining five, three are template-local tooling and one is `spellcheck` (a pre-flight, not an
  invariant). **`no-unstable-deps` was in neither list**, so it was neither adopted nor rejected:
  it had no verdict at all. It was never copied, which was the correct reading of "a named subset",
  and it was reported instead of being quietly resolved in either direction. That is the behaviour
  the reconciliation rule exists to produce.

  *Why it belongs on the theme, and why now:* **non-negotiable rule 1 — no dev/alpha/beta/rc
  dependency, ever — is a literal marketplace requirement, and it binds every package this
  project publishes, not only the template.** The theme's `composer.json` has **no `require`
  section at all** today, so the invariant passes **vacuously**. That is precisely the moment to
  install it: it becomes load-bearing the first time anyone adds a dependency, and whoever adds
  that dependency is the last person who will think to bring a guard along with it.
  ⚠️ A vacuous pass is its own hazard (**I-028** — the degenerate case and the expected value
  printing the same word), so the invariant must report **`0 entries`**, not merely `clean`.

  *What is NOT amended:* the five deliberately excluded invariants stay excluded, and D-028's
  central holding is untouched — **a copied invariant is never edited until it passes**; that is
  the failure the manifest and its drift detector were bought to prevent. The new record enters
  the manifest as `status=verbatim`, which the manifest itself asserts by requiring the local and
  source hashes to be **equal** — so this amendment is enforced mechanically, not by memory.


- **Note closing D-014(c) — the theme's identity is measured, and now it is on disk.** Recorded
  [ejecutor] 2026-08-24. D-014 rider (c) read *"theme machine name pending verification (proposal:
  `agora_theme`) — closed in unit 002"*. It is closed. Read from
  `www.drupal.org/api-d7/node/3618791.json`, not from a prompt:
  **node `3618791`** · title **`Ágora Transparency Theme`** · `field_project_machine_name`
  **`agora_theme`** · `type` **`project_theme`** · `field_project_type` **`full`** (not a sandbox).
  Composer package **`drupal/agora_theme`**; git repository `project/agora_theme`, id `241203`,
  created `2026-08-24T10:25:56Z`.
  ⚠️ **The title carries a `Theme` suffix and that is not cosmetic:** `Ágora Transparency` is
  already taken by the template project, so drupal.org refused it. Nobody may "tidy" the suffix
  away, and the theme's `name:` key is **byte-identical** to the title above — verified as bytes
  (`c381676f7261…`), not by eye, because D-021 exists after this class of thing was got wrong once.
  *Why this note exists at all:* the identity was true in a dispatch prompt and **nowhere on disk**
  — `grep -rn '3618791' specs/` returned nothing while T-506 and T-507 were already written to cite
  it. A fact that lives only in a prompt is I-036's exact failure, caught this time by the
  implementer rather than by an audit.

- **D-031** · **The unit-002 scope gate is 2 over, and the overrun is accepted rather than
  re-based.** **SIGNED by [andres] 2026-08-24** — he was given A/B/C and answered **A**.

  *What happened, stated as the failure it is.* The unit's task count was carried in prose as "30"
  and never computed. `grep -c '^| T-[5-8][0-9][0-9] '` returns **12 + 15 + 6 + 7 = 40**. So the
  count was **six short before D-026 raised the ceiling this morning**, which means that morning's
  +4 rider was reasoned from a number nobody had run — the scope gate's entire content is a number,
  and it failed on its first use. This entry exists so that fact is on the record beside the
  decision it distorted, rather than being quietly absorbed by a corrected total.

  | | Option | Real cost |
  |---|---|---|
  | **A ★** | **Accept 40 against 38; record why; do not move rows and do not raise the ceiling** | Headroom −2. The reserve that existed for wave 7's atomic swap and wave 8's carried debts is gone, so the **next** unplanned task is a rider, not a shrug — which is the gate doing its job, late but honestly |
  | B | Move two rows to a later unit | Would have to be two real rows. Every wave-5 and wave-6 row is either a gate, a measurement or a piece of the signed content model; the two cheapest to move are the two whose absence would be discovered in unit 006 as debt |
  | C | Raise the ceiling to 40 | Makes the number follow the work, which is the definition of a scope gate that does not gate |

  ★ **A, chosen by [andres].** The overrun is **2 tasks, and the +4 that preceded it bought exactly
  the six-node content model** — the work is not padding, it is scope that was always there and was
  miscounted. What A buys is the thing B and C both destroy: **the number keeps meaning something.**

  *Binding consequence, not decoration:* the unit now runs with **negative headroom**. Any task
  added from here needs a signed rider naming what it displaces — including tasks discovered by an
  audit. **T-806 reports the count against 38 and states the −2**; it does not re-base to 40.

  *Method fix, so this cannot recur silently:* every count in `tasks.md` and `plan.md` is now a
  **quoted command with its output**, never a number in prose. Recorded as the rule; the invariant
  that enforces it is unit 006's, because writing one today would be the 41st task.


## Decisions opened by unit 002

> **Framed [ejecutor] 2026-08-24 in the unit 002 scaffolding turn, against
> `specs/002-base-and-theme/research/2026-08-24-canvas-theme-and-cross-repo-gates.md`.**
> **D-027, D-028, D-029 and D-030 are SIGNED by [ejecutor] under [andres]'s standing delegation** —
> each is methodology or a licence-constrained choice, not a product trade-off.
> **D-025 and D-026 await [andres]:** D-025 imposes an ordering constraint that costs him a release
> action, and D-026 decides the shape of the product itself. Neither is mine.
> ⚠️ **D-027 is a material amendment to D-009**, which is signed: D-009 put the accessibility gate
> in the template repository, and the mechanics make that impossible. Recorded as an amendment,
> never as an edit (rule 8).

- **D-025** · How `agora_transparency` depends on `agora_theme` before the theme has a release

**Context in one line.** The template must name the theme in `require`, and today Composer cannot resolve `drupal/agora_theme` at any version — measured, not assumed: `packages.drupal.org/files/packages/8/p2/drupal/agora_theme.json` returns 404, and so does the same URL for `agora_transparency`, which has had a pushed branch since yesterday.

| | Option | Real cost |
|---|---|---|
| A | Depend on `^1.0@dev` once a dev release exists | Violates non-negotiable rule 1. Upstream would not stop us (`RequirementsTest`'s pin regex does not match `^1.0@dev`), which makes it *more* dangerous, not less — nothing would catch it but us. Also forces `minimum-stability` on the end user. |
| B ★ | **Sequence: the theme cuts a stable `1.0.0` before the template ever names it.** The template's `require` only ever holds `"drupal/agora_theme": "^1.0"` | Costs one ordering constraint: wave 7 cannot start until [andres] has tagged 1.0.0 (T-701). Costs nothing else. The theme's later `1.0.1`/`1.1.0` flow to new installs through the caret, which is exactly how it should work — site templates are apply-once and provide no update paths (RFC). |
| C | Test the template against the unreleased theme via a build-time-only path repository (`_COMPOSER_EXTRA`) | Technically clean of the package. But the gate would then prove an installation *the end user cannot perform* — the I-048 pattern this project has been caught by before. |

**★ B.** It is the only option where the gate proves the same thing the end user gets, and the only cost is an ordering constraint we control.

**Riders needed from [andres]:** (i) confirm that `1.0.0` may be tagged before security-advisory coverage is granted — coverage is not a release prerequisite, only a 10-day-age one; (ii) confirm that a theme reaching 1.0.0 mid-unit is acceptable rather than waiting for unit 006.

---

- **D-026** · **The shape of the content model: six node types, and budget is not one of them.**
  **SIGNED [ejecutor] 2026-08-24 under [andres]'s delegation** — <!-- cspell:disable -->*"aquí como tú estimes oportuno, o
  el orquestador"*<!-- cspell:enable -->, after he ruled that **cost is not the criterion**: <!-- cspell:disable -->*"debe tener sentido para un
  producto final pulido, no quiero cosas de relleno pero tampoco quiero quitar cosas que tendrían
  sentido añadir."*<!-- cspell:enable -->
  ⚠️ **This REPLACES the framing of earlier the same day**, which recommended three types on a
  surface-cost argument. That argument was answered and the recommendation was **refuted on its own
  terms** — not because it was cheap, but because it was wrong. Research:
  `specs/002-base-and-theme/research/2026-08-24-content-model-against-spanish-transparency-law.md`.

  *Context in one line:* **Ley 19/2013 arts. 6-8 enumerate what a Spanish public body must publish
  and, for each category, names the fields** — nine of them for contracts, including three amounts
  and a legally required derived statistic. The model's shape is specified by law, not chosen by us.

  **The rule that decides every row:** *a category earns its own node type when the law names
  **three or more fields that are neither prose nor a file**, at least one being a **number or a
  counterparty a table must sort or filter on**. Otherwise it is a Document with a type, or a
  Dataset.*

  - **A** · Three: Document (+ vocabulary), Person, Dataset. **Refuted, and not on cost.** Putting
    `importe de adjudicación`, `procedimiento`, `nº de licitadores` and `beneficiario` on `Document`
    makes it a **union type**: its listing's columns become the union of every regime, so a third of
    the cells are structurally empty — and an empty `<td>` that is empty *by design* is
    indistinguishable from missing data to a screen-reader user. **It makes accessible tables
    worse**, which is the one constraint that is not negotiable. It also puts `nº de licitadores` on
    the form of the clerk adding a subvención.
  - **B ★** · **Six, aligned to the law's regimes:** `Document` · `Person` · `Contract` ·
    `Agreement (convenio)` · `Grant (subvención)` · `Dataset`. **Budget is not a node type:** it is
    a Document (the approved budget, execution reports, cuentas anuales) plus a Dataset (the
    machine-readable execution table, which *is* the accessible table and feeds any chart).
    Six bundles, **not six units of work**: the three financial regimes share one field pattern
    (`objeto`, `importe`, `periodo`, `contraparte`, `área`, `estado`), created once and attached
    three times; Contract adds four fields, Convenio one, Grant none. One facet spine
    (`área · año · estado`) serves all six.
  - **C** · Five, as the ROADMAP says. **Wrong in both directions.** It omits **convenios
    (art. 8.1.b)** and **subvenciones (art. 8.1.c)** — two of the eight enumerated categories, and
    subvenciones is the most politically scrutinised item a small municipality publishes. And it
    adds **Budget line**, the one genuinely wrong shape.
  - **D** · Four, with one `Financial record` type and a `regime` discriminator. **Fails on Spanish
    administrative law, not on taste:** a convenio is legally defined by its **exclusion from the
    LCSP** (Ley 40/2015 arts. 47-53); a subvención is its own regime under Ley 38/2003 with its own
    national register (BDNS). Collapsing three distinct legal regimes into one bundle with a
    dropdown is an error any Spanish reviewer sees immediately.

  ★ **B.** The only option where every node type maps to a category the law names, every field on it
  is a field the law names, and nothing is modelled that the law does not ask for. **Six is what the
  specification yields — not a compromise between three and five.**

  *Two things this deliberately does not do.*
  (a) **It does not model everything at full depth in v1.** `Agreement` ships with six fields and
      `Grant` with three — which is *all the law names for them*. They are small **because the law
      is brief about them**, not because they were trimmed.
  (b) **It does not chase the autonomic layer.** Every observed autonomic addition (Andalucía's
      *actas de plenos*, Cataluña's extension to privately financed entities, plenary recordings) is
      a document or a media file. The model does not change; the `document type` vocabulary gains
      `acta de pleno`. A template that tries to be all seventeen autonomic regimes is one no
      municipality recognises.

  *The four questions, answered:*
  · **Contract** — own type, the least arguable of the six: art. 8.1.a) names nine fields and then
    requires a **statistic derived from them**. You cannot aggregate a taxonomy term on a PDF.
  · **Budget line** — **not** a node type. The unit of publication is *the budget of year N*, not
    the *partida*. This is the ROADMAP's one real error.
  · **Grant and Agreement** — own types, and **the ROADMAP omitting them is a real gap**, not a
    scoping choice. The plan simply had not read the law.
  · **Dataset** — a real legal requirement, from a statute nobody in this project had cited:
    **Ley 37/2007** as amended by Directive (UE) 2019/1024, plus **Reglamento (UE) 2023/138**, which
    binds Spanish local entities **directly, without transposition**. It was in the plan for the
    wrong reason and survives for a much better one. ⚠️ The six high-value categories are
    **snippet-level only** — verified inside T-614 before any task cites them.
  · **Person** — own type. Art. 6.1 requires the organigrama with *perfil y trayectoria*; art. 8.1.f)
    requires **retribución anual** and **indemnización**, two numbers that must sit in a sortable
    table; art. 8.1.h) attaches the *declaración de bienes* of **local representatives specifically**
    — the most municipality-specific obligation in the statute.

  *A finding for the marketplace pitch, measured not assumed:* **no Drupal content model for Spanish
  transparency obligations exists.** `transparencia` and `open_data` are not projects on drupal.org;
  `opendata`, `open_data_schema_map` and `datastore` have no release for current core; LocalGov
  Drupal models services and directories and nothing financial. The incumbent is **Gobierto**
  (Populate — Barcelona, Madrid, Terrassa), a commercial non-Drupal platform. Re-measure at unit 007
  rather than quoting this date.

  *Budget consequence, stated as a number rather than absorbed silently:* the model lands in wave 6
  Lane A, which carried it in **one** task. Split into five (T-601 restated, plus T-612…T-615), the
  unit goes 30 → **34 tasks against a 34 budget, headroom 0**. The headroom existed because wave 7's
  atomic swap and wave 8's carried debts are the parts most likely to surprise. **The budget is
  raised to 38** — 34 for the known work, 4 to preserve the reserve. The increase is +4 and it buys
  exactly the content model, nothing else. Signed under the same delegation; recorded here because
  a budget that absorbs work silently is the failure the scope gate was built to prevent.
- **D-027** · Where the accessibility gate physically lives — **a material amendment to D-009**

**Context in one line.** D-009 (signed 2026-08-21, option C) put axe "inside the existing `gitlab_templates` Nightwatch job on drupalcode", assuming the template repository; the mechanics say that cannot work there.

The evidence, both halves read at source today:

- `include.drupalci.main.yml:118-127` — a `recipe` project is installed at `$CI_PROJECT_DIR/recipes/<name>`, a **sibling** of the docroot; every other project type goes **inside** it.
- `core/tests/Drupal/Nightwatch/nightwatch.conf.js:16-21` — Nightwatch globs `**/tests/**/Nightwatch/**/*.js` with `cwd` resolved to **the docroot**.

So in `agora_transparency` the CI job would materialise (its exists-rule reads `$CI_PROJECT_DIR`) and the harness would collect **zero** test files. A green Nightwatch job that ran nothing.

| | Option | Real cost |
|---|---|---|
| A | Keep it in the template repo and set `DRUPAL_RECIPES_PATH` inside the docroot | Also moves where Composer installs the recipe. **NOT MEASURED**, and unpicking it later would touch the packaging. Fixing a test-collection problem by moving the product is backwards. |
| B ★ | **Amend D-009: the blocking axe gate lives in `agora_theme`, where a theme is installed inside the docroot and the glob finds it.** The template repo's a11y surface is the `Drupal CMS` install smoke plus, from unit 003, axe over demo pages on the informative GitHub surface | The theme repo's axe test scans the theme rendering **core's** content, not Ágora's demo pages. Real gap — but the demo pages do not exist until unit 003, so the gap is not being introduced here, only made visible. |
| C | Drive axe from PHPUnit `FunctionalJavascript` in the template repo, browser proven by T-228 | Needs axe-core reachable from PHP. Vendoring it into a package whose defining rule is "contains no code" is a fight with the reviewer we do not need. |

**★ B**, with an explicit note that the demo-page axe coverage is unit 003's and is named there, so nobody reads today's arrangement as complete.

---

- **D-028** · Does `agora_theme` get its own `tests/bin/`, or inherit by copy?

**Context in one line.** Four invariants apply to both repositories (`no-secrets`, `no-patches`, `no-blind-phpunit`, `identity-strings`); five do not (`sbom-check`, `no-code-in-template`, `no-boilerplate`, `no-ci-allow-dev`, `cited-tasks-exist`).

| | Option | Real cost |
|---|---|---|
| A | Copy `tests/bin/` wholesale | Five scripts that assert things false about a theme. They would be adjusted to pass — which is the exact shape of weakening a gate, arrived at innocently. |
| B ★ | **A named subset, copied, plus `shared-invariants.manifest` (sha256 per script + the source sha it came from) and an invariant that fails when a local copy has been edited** | Copy-with-a-detector. Local drift is caught mechanically; upstream drift is caught by a dated review task in unit 006, not by magic. Cost: one manifest to regenerate whenever a shared script legitimately changes. |
| C | Extract the shared scripts into a third repository both consume | Correct at ten repositories. At two it is a third thing to release, version and gate, for four shell scripts. |

**★ B.** It matches the house pattern — a detector with a dirty case — and it is honest that this is a copy rather than pretending it is sharing.

---

- **D-029** · Directory naming under D-017

**Context in one line.** D-017 put the whole repository in English, unit 001's Spanish paths were renamed, and **four Spanish unit directories are still on disk**: `002-base-tema`, `003-contenido-demo`, `005-ia-governance`, `007-publicacion`.

| | Option | Real cost |
|---|---|---|
| A | Rename only `002-*` now, leave the others | Guarantees the same discussion three more times, and leaves a repository that is visibly half-translated at exactly the moment two new reviewers (security coverage, marketplace) start reading it. |
| B ★ | **Rename all four in T-501: `002-base-and-theme`, `003-demo-content`, `005-ai-and-governance`, `007-publication`** | Four `git mv`s and four one-line README edits. Each file is a 4-line placeholder; there is no history worth preserving and `--follow` handles what there is. |
| C | Leave them; the paths are internal | D-017 says "the ENTIRE repository", process layer included, and D-015 keeps `specs/` **visible** in the public repository. These paths are published. |

**★ B**, in T-501, before any unit-002 file is created under the old name.

---

- **D-030** · The typeface

**Context in one line.** D-014 rider (a) requires self-hosted OFL typography — CDN fonts are a GDPR liability for EU public bodies — and this is an SBOM and licence-manifest entry, not a taste question.

All three verified as SIL OFL 1.1 at source on 2026-08-24 (licence file URLs in the research).

| | Option | Real cost |
|---|---|---|
| A | **Source Sans 3** (Adobe) | Excellent quality and coverage. Carries **Reserved Font Name `'Source'`** — a permanent rule that any modified build may not use the name. One more thing to remember forever. |
| B ★ | **Public Sans** (US GSA, a fork of Libre Franklin) | Purpose-built for government interfaces. **No Reserved Font Name**; GSA's modifications are CC0 on top of OFL 1.1, and the licence explicitly says to treat the combined work as OFL 1.1. Institutional-sober by design, which is the brief. |
| C | **Atkinson Hyperlegible** (Braille Institute) | The strongest accessibility narrative available — designed for low vision. Its display personality is more distinctive than "sober institutional", and it is a weaker workhorse for dense tables. |

**★ B — Public Sans**, with the note that C is the right choice if [andres] wants the a11y story to be legible in the typeface itself rather than only in the audit. **Either way, T-607 must verify before adoption:** full Spanish diacritic coverage and tabular figures. That verification is a task criterion, not a decision.

---

### Amendment proposal to D-020 (not a new decision — rule 8)

**D-020 classifies the install smoke's surface as informative.** Measured today: setting `OPT_IN_TEST_DRUPAL_CMS: '1'` and `_AUTORUN_DRUPAL_CMS: 'all'` makes the `Drupal CMS` job match `.autorun-drupal-cms-rule`, which ends `when: always` and declares no `allow_failure` — i.e. **automatic and blocking** on drupalcode. Proposal: **amend D-020** so the clean-install smoke becomes the ninth blocking job (T-511), and the GitHub workflow keeps its informative status as a second opinion. This changes gate A's job list, so per D-023(5) the CLAUDE.md table is updated in the same commit that makes it true.

---

---

## Riders on wave 1, signed by [andres] 2026-08-21

- **On the `blank` theme (T-103 / T-105).** `blank` and the `extra.drupal-site-template` block are
  **kept until unit 002**. T-103 deletes the three `_comment` arrays and `GET-STARTED.md`, but **not**
  the `extra` block. **A gate A with a permanent red is NOT accepted** — a tolerated red degrades the
  gate and violates non-negotiable rule 9. Therefore the affected check is **adjusted to the
  specification in force for unit 001** (`blank` and the `extra` block are expected to be PRESENT,
  with a reference to this rider); if adjusting it required touching a protected file, it is recorded
  as an **explicit, documented skip** in the gate runner. In both cases the unit-002 task that
  performs the coordinated change (delete `extra` + `require` the theme + `install:` + `system.theme`,
  in **one atomic commit**) **owns that debt** and is the one that reverts the adjustment or skip.
  **Debt = a task with an owner and an exit gate, never a known red light.**

  ✅ **CLOSED 2026-08-25 by T-703, and the closure is in two halves that must not be confused.**
  Appended, not edited: the rider above stands as signed.
  **(1) The adjusted check — DISCHARGED, and it was already discharged before T-703 ran.** The
  "affected check" this rider names is `.extra["drupal-site-template"]` in
  `tests/bin/gate-a-wave1.sh`, and the atomic swap `9dc5722` flipped it from `'present'` to
  `'absent'` in the same commit as the four coordinated changes, which is what that check's own
  header both demanded and forbade doing anywhere else. T-703 verified the flip on disk rather
  than re-performing it; the tripwire now points the other way and fails if the generated-theme
  block ever returns.
  **(2) The deny-list term — NEW COVERAGE, which inherits no part of (1)'s discharge.**
  `tests/bin/no-boilerplate` was **never** adjusted for this rider: its list held **8** verbatim
  starter-kit strings and neither `blank` nor `extra.drupal-site-template` was among them, so the
  string that describes the wrong product to the user had exactly one watcher and it lived in a
  different file. T-703 adds a **ninth** term, `drupal-site-template` (8 → 9), and it was **watched
  failing** before being trusted: run against a clone checked out at `9dc5722~1`, the invariant
  reports **6 findings, exit 1**, naming `HEAD:composer.json:28` and `WORKTREE:composer.json:28`
  — the `extra` block itself — plus two `recipe.yml` comment lines in each pass. At `HEAD`:
  **116 packaged entries · 224 files scanned · 9 deny-list terms · 0 findings · exit 0.**
  **Two rulings, both measured rather than argued.** The term is `drupal-site-template` and **not**
  `extra\.drupal-site-template`, because the JSON key is nested and the dotted path never appears
  as a literal in `composer.json`: the escaped form produces 2 hits at `9dc5722~1`, both in
  `recipe.yml` comments and **none** in the file that actually carried the block. And **`blank` is
  deliberately NOT a term**, against the pre-audit's recommendation, because it over-matches on
  content that is currently correct: over `git archive HEAD` it yields **4 hits and 0 true
  positives** — `.gitattributes:45` ("blanket"), `README.md:128` and `:136` (the prose that
  correctly describes this very swap) and the Canvas landing page's own comment; narrowing it to
  `blank theme` still leaves 2 hits of legitimate README prose. Adding it would have turned the
  invariant red on the truth, and the only route back to green would have been deleting a term —
  the move this list's FORBIDDEN clause exists to stop. The README's `blank` claims are retired
  instead by **T-705** re-measuring them on a rebuilt rig.

- **On the specification corrections found by the `tester` in wave 1, 2026-08-21.** All three adopted:
  1. **T-209** is specified as *"`CI_ALLOW_DEV` is not **defined** in any versioned file"*, never
     *"not mentioned"* — the string legitimately lives in `tests/src/Kernel/RequirementsTest.php:55`,
     which T-406 forbids modifying.
  2. **T-103** must account for the **three** `_comment` occurrences in `composer.json`.
  3. **`ValidationTest.php`** is added to the set of kit files watched by the gate.

---

## Decisions opened by unit 003

> Scaffolded [ejecutor] 2026-08-26. **None of these is closed here.** Three are new (D-035, D-036,
> D-037), one has been open since 2026-08-21 (D-010), and two are riders. Verified on disk before
> numbering: the highest existing number was **D-034**.

### D-010 · Scope of the v1 demo content — **open since 2026-08-21, and it is this unit's**

Not new. This file already reads *"Postponed to unit 003, once the content model exists. Keep it
open."* The model exists. It must be signed **before wave 10 authors a node**, because it is the
single largest determinant of this unit's task count.

*Context in one line:* how many rows per bundle, and what municipality is the demo pretending to be?

| | Option | Real cost |
|---|---|---|
| A | Minimal — 3-5 rows per bundle | Every table is smaller than one screen; the **pager never renders**, so `heading-order`, focus visibility and target size are untested on the one component that exercises them. Cheapest to author, and it leaves three WCAG criteria unmeasured |
| **B ★** | **Sized by what must be testable**, not by taste: ≥26 Documents (the library's `items_per_page` is **25**, so 26 is the smallest number that paginates); ≥3 rows per remaining bundle; ≥3 distinct `procedure_type` values; ≥1 counterparty holding two contracts | Roughly 50-60 nodes. More authoring, and every number in it is defensible from a config file rather than from an opinion. It is also the smallest corpus in which the art. 8.1.a) statistic is non-degenerate |
| C | Rich — a full fictional year of a real-sized municipality | The most convincing demo and the largest `no-real-people` surface. `haven` ships 75 MB; a corpus this size invites the same |

✅ **SIGNED B by [andres], 2026-08-26.** Open since 2026-08-21; closed the day the corpus was
about to be authored, which is the deadline the decision itself named. **The binding numbers are
therefore:** ≥26 Documents · ≥3 rows in every other bundle · ≥3 distinct `procedure_type` values ·
≥1 counterparty holding two contracts. Roughly 50-60 nodes. These are **task criteria now, not
guidance**: T-1004's pagination assertion and T-1005's non-degeneracy assertion both fail if the
corpus is smaller, so the size is enforced by the gate rather than remembered.

⚠️ **The rider was answered differently from how it was asked.** The fictional municipality's name
was offered as *"you choose"* or *"I propose and you veto"*; [andres] answered **"propose it and
try to verify it yourself too"** — so the naming AND the proof that it is not a real Spanish
municipality are both [ejecutor]'s, and the verification is part of the deliverable rather than an
assurance. It is recorded as a separate row's criterion, not as prose here.

★ **B.** It is the only option whose size is derived from a measurement — `items_per_page: 25` in
`config/views.view.agora_base_library.yml` — rather than chosen, and it is the smallest corpus in
which the accessibility gate can test what it claims to test.

✅ **RIDER CLOSED 2026-08-26: the municipality is `Fuentelclaro`, and the proof is a measurement.**
[andres] answered *"propose it and try to verify it yourself too"*, so the verification is part of
the deliverable rather than an assurance. What was actually run:

- **The INE's own register.** `https://www.ine.es/daco/daco42/codmun/diccionario25.xlsx`, downloaded
  and parsed — **8,656 entries checked**, accent-normalised. `Fuentelclaro` is **not among them**.
  The nearest entry sharing its stem is <!-- cspell:disable -->**`Fuentelcésped`**<!-- cspell:enable --> (Burgos), which is a different word.
- **The naming pattern is real even though the name is not**, which is the property that makes it
  usable: <!-- cspell:disable -->`Fuentelviejo` (Guadalajara), `Fuentelisendo` (Burgos),
  `Fuentelcésped` (Burgos), `Fuentelarreina` (Madrid)<!-- cspell:enable --> are all real. It **reads** as a Spanish municipality without
  **impersonating** one — the standard non-negotiable rule 3 and `no-real-people` are both
  protecting.
- **No organisation to impersonate.** <!-- cspell:disable -->`fuentelclaro.es`, `fuentelclaro.com` and
  `ayuntamientofuentelclaro.es`<!-- cspell:enable --> all return **NXDOMAIN**, so the demo's email domains and URLs
  collide with nothing that exists today.
- **Rejected candidates and why**, because a shortlist nobody sees is a claim:
  <!-- cspell:disable -->`Sotomonte de la Vega` is absent from the register but sits one word from
  the real **`Soto de la Vega`** (León) — close enough to be misread as it; `Ribalta de Segura` is
  clean as a toponym but `Ribalta` is a Spanish painter's surname; `Torrealba de Duero` and
  `Montebranco`<!-- cspell:enable --> are clean and are held as fallbacks.

⚠️ **What this does NOT prove, stated rather than implied.** The INE dictionary lists
**municipalities**, not <!-- cspell:disable -->*pedanías*, *entidades locales menores*<!-- cspell:enable --> or hamlets, so a locality of this
name could exist below the level the register covers; a web search found none, which is weaker
evidence than the register and is labelled as such. And absence from DNS proves nobody has
registered those domains **today**, not that no organisation of the name exists anywhere.

⚠️ **Consequence for `no-real-people`.** T-905's row says its 12-term deny list is *"a floor"* and
that T-1001 must add *"the real office-holders of whatever real municipality the demo models"*.
Because `Fuentelclaro` is fictional it models none, so **there are no office-holders to add and the
floor stands as the whole list** — recorded here so that the row's instruction is answered rather
than quietly skipped.

**Rider as originally written:** the fictional municipality's **name**. Everything downstream — the org
chart, the domain in email addresses, the deny-list `no-real-people` is built from — depends on it,
and it must be a name that is *provably* not a real Spanish municipality.

### D-035 · The demo content is bilingual and the interface cannot be — what ships?

*Context in one line:* **rule 6** says demo content is bilingual ES/EN; **D-033** puts every shipped
config string in English and records that four locks stop the Spanish translation reaching an
installed site — so a Spanish demo page shows Spanish content inside English chrome, and unmarked
that is a **WCAG 2.2 SC 3.1.2 (AA) failure**, not a cosmetic complaint.

Measured, so the options are not argued from taste
(`specs/003-demo-content/research/2026-08-26-demo-content-mechanics.md` §5, §4):

- `Importer.php:257` — a translation for a language the site has not configured is **dropped silently**.
- `haven` and `byte`, the two published site templates: **0 of 213 content files carry a
  `translations:` key.** Ágora would be the first, with no worked example to copy.
- No template in `agora-theme/templates/` emits a `lang` attribute today.

| | Option | Real cost |
|---|---|---|
| **A ★** | **Ship bilingual content; mark every English fragment with `lang="en"` in the theme** | Honest, standards-correct, and it turns the awkwardness into a demonstrated WCAG competence — a transparency template that gets 3.1.2 right is making its own argument. Costs **T-1011**: seven theme templates gain a `lang` mechanism, and it must be maintained as templates are added |
| B | Ship bilingual content; **do not** mark the fragments | Cheaper today. It ships a **known AA failure** in the product whose thesis is "real AA, verified", and the reviewer most likely to find it is the Spanish one. Not recommended, listed so the trade-off is visible |
| C | **Ship English-only demo content**; document ES as a post-install step | Zero WCAG risk, matches both published precedents exactly, and removes the whole multilingual config layer (~13 config files) and T-902's probe. **It contradicts non-negotiable rule 6**, so it costs an amendment to rule 6, not a task |
| D | Ship bilingual content and ALSO the Spanish interface translation inside the template | **Rejected on mechanics, not preference** — D-033 already established there is no seam: `RecipeConfigInstaller` hard-codes `DEFAULT_COLLECTION`, `ConfigConfigurator` opens `config/` non-recursively, and a `.po` file needs an `.info.yml` key the package cannot have |

✅ **SIGNED C by [andres], 2026-08-26 — "C with the floor". The recommendation below was A and the
evidence overturned it; recording that is the point of writing recommendations down.**

**What is signed:** demo content is **English-only**; **non-negotiable rule 6 is amended** in
`CLAUDE.md`; and exactly one piece of option A survives, sized to what exists rather than to a page
that will not — the **three Spanish legal-citation fragments already shipped in `config/`** get
`lang="es"`, because they are a live **SC 3.1.2** exposure **today, independent of this decision**.

**The measurement that overturned A**, and it is one sentence: **`haven` has roughly 180 `.po` files
per release on `ftp.drupal.org`, one carrying 168 real Spanish strings — and no installed `haven`
site can fetch a single one.** Extraction works; delivery does not exist.

*Five locks, measured on the rig, replacing the four this record asserted:*
1. **A site template can never be a translation project.** `LocaleProjectRepository::getProjectList()`
   builds its list from the **module and theme** extension lists only, and `RequirementsTest`
   requires **0 `*.info.yml`** in the package. Absolute, and D-033 does not name it.
2. **Recipe config is invisible to the storage `isSupported()` reads** —
   `LocaleDefaultConfigStorage` is two `ExtensionInstallStorage`s over modules, themes and profiles.
   Measured on an installed site: **0 of 102** Ágora config objects supported, against **208 of 520**
   for everything else. This is what D-033 should have called sharpest.
3. `RecipeConfigInstaller` writes only `DEFAULT_COLLECTION`, so the `language.<code>` override
   collection is unreachable.
4. `ConfigConfigurator` opens `config/` **flat**; `config/language/es/*.yml` is never seen.
5. **There is no feature to reach for**: `Drupal\Core\Recipe` contains no occurrence of the word stem behind *translation*
   **zero** times across all 21 files.

⚠️ **It is a property of RECIPES, not of Ágora, and that is the reassuring half.** Measured in the
same database: `node.type.page`, `field.field.node.page.field_content` and
`taxonomy.vocabulary.tags` — all shipped by **Drupal CMS's own recipes** — report `isSupported`
**FALSE**, while `views.view.content` and `image.style.large` from modules report TRUE. Drupal CMS
ships an untranslatable content model too. No reviewer expectation is being missed.

*Precedent, re-measured from the published archives rather than the rig:* `haven` 128 content files,
`byte` 85, **0 with a `translations:` key** in either. The Marketplace Initiative criteria name
**no language requirement whatsoever**.

⚠️ **What C does NOT do, stated because the framing hid it.** On a **Spanish-language install** C is
arguably worse than A: with no translation, a Spanish site would serve English prose under
`<html lang="es">`, engaging **SC 3.1.1 (level A)** — lower level, more serious — and no theme
`lang` handling can fix it, because the wrong langcode is on the entity. ⚠️ **Reachability is NOT
MEASURED**: the install-task order suggests the branch that would cause it does not fire, read at
source and **not observed**. Named rather than glossed.

*What it costs, and it is smaller than the original C:* T-1001 loses its multilingual half
(`config/` stays at **102** instead of rising to ~115); T-1002 loses its `translations.es` blocks;
**T-1011 does not disappear — it shrinks and re-points** at the three shipped fragments; **T-902 is
already signed and is neither deleted nor weakened** (rule 8) — its finding that the importer drops
a translation **silently, with no log line at all** stays live in `IDIOMS.md` for whoever adds
`translations:` later. A probe that stops you building a thing is as legitimate an outcome as one
that enables it. One new paragraph of README documents the post-install Spanish path.

*And Spanish has a verified home whenever it is wanted:* `drupal/agora_theme` **does** have an
`.info.yml`, so it can declare `interface translation project` and be a real translation project —
proven on the same database, where theme-shipped config reports `isSupported` **TRUE**.

★ **The original recommendation, kept because a superseded recommendation is evidence about how the
decision was made:** A. It is the only option where the product's accessibility claim and its
bilingual claim are
both true at once, and the cost is one mechanism in one repository rather than a permanent
exception. **But C is a legitimate answer and it is cheaper by roughly four task rows** — if
[andres] would rather rule 6 be amended than pay for `lang` handling, that is a defensible call and
it needs to be made **now**, because T-1001 and T-1011 are both blocked on it.

### D-036 · Where does axe run over the **demo pages**, and where does Playwright run?

*Context in one line:* D-027 proved Nightwatch **cannot be collected** in the template repository
(recipes install outside the docroot; Nightwatch's glob is rooted at the docroot), and the demo
pages exist only in the template — so *"axe with no violations on the demo pages"* has **no surface
today**, and D-027 said so explicitly: *"the demo pages do not exist until unit 003, so the gap is
not being introduced here, only made visible."* It is now visible.

| | Option | Real cost |
|---|---|---|
| A | Theme repo's Nightwatch builds a site **with the template applied**, then scans | Keeps the blocking axe gate in one place and keeps it on drupalcode where a reviewer can re-run it. **Creates a reverse edge**: the theme would know about the template, which `specs/002-base-and-theme/plan.md:218-221` forbids — *"There is no reverse edge and none is permitted"* |
| B | Axe over demo pages on the **template's GitHub mirror**, informative | The mirror **already exists** so there is no prerequisite. But D-009 discarded exactly this for the axe gate: *"the accessibility gate, which is the product's thesis, would live where a Drupal.org contributor can neither see it nor re-run it"* |
| **C ★** | **Split by what each surface can actually prove.** Blocking `nightwatch` **stays in the theme** over theme fixtures (unchanged). Demo-page axe runs on the template's **`Drupal CMS` job**, which already builds a real site from this package and is already blocking — driven from PHPUnit `FunctionalJavascript` or a Nightwatch run inside that job's docroot | Needs axe-core reachable inside that job. D-027 rejected a related idea partly on *"vendoring it into a package whose defining rule is 'contains no code'"* — but a **CI-time npm fetch inside a job** is not vendoring, and it is **NOT MEASURED**. Wave 12's row is written with the surface unnamed precisely so this is measured before it is chosen |
| D | Accept the gap for unit 003; demo-page axe is unit 006's | Honest, and it makes the unit's headline claim — *"axe stops scanning fixtures"* — false for another two units |

★ **C, conditional on one measurement**: whether axe-core can be installed and run inside the
existing `Drupal CMS` job. That measurement belongs in wave 9 or early wave 12 and it is **one
task-row's worth of reserve**, which the budget carries. If it fails, **B** is the fallback and the
gap is stated rather than hidden.

**And the same decision settles Playwright's home.** T-804 was written `H` (theme), but demo pages
live in the template, whose mirror exists — while the theme has **no GitHub repository at all**
(`gh repo view` → *"Could not resolve to a Repository"*). So the prerequisite [andres] has been
asked for may be for the **wrong repository**. Recommendation: **demo-page visual regression goes to
the template's existing mirror**, and the theme mirror becomes a separate, smaller, later question.
**This would remove a prerequisite from unit 003's critical path entirely.**

#### D-036 · Amended 2026-09-12 — BOTH halves are superseded, and the record said so nowhere

**Nothing above is edited** (rule 8). This block is appended because D-036 asked two questions,
each has since been answered by a later record, and until today **neither supersession was visible
from D-036 itself** — a reader arriving here read a live `★ C` and a live recommendation.

| D-036's half | Superseded by | What replaced it |
|---|---|---|
| **where axe runs over the demo pages** | **D-053**, signed by [ejecutor] 2026-09-07 under standing delegation | A PHPUnit `FunctionalJavascript` test at `tests/src/FunctionalJavascript/AccessibilityTest.php`, collected by the **existing** `phpunit` and `phpunit-pgsql` jobs. No new CI job; the job list stays at ten. D-053 records the measurement `★ C` was made *"conditional on"* and that nobody had taken |
| **where Playwright runs** | **D-045**, ruled **B** | Playwright functional and visual regression run on drupalcode. D-045: *"The mirror is not abolished — D-016 keeps it as a read-only mirror and D-020 keeps the GitHub install smoke as an informative second opinion. **What is abolished is the mirror being a prerequisite for anything.**"* |

⚠️ **The consequence is the reason this block exists, and it is a blocker that was assigned to a
human for weeks after it had stopped existing.** D-036's closing paragraph above ends on
*"demo-page visual regression goes to the template's existing mirror"*, and downstream rows read
that as *"[andres] must create a mirror first"*. D-045's own ⚠️ names this exact failure — *"The
cost of not checking was not a wrong CI file. It was a blocker assigned to the human."* — and then
the same stale precondition kept being quoted **from D-036**, one record away from where D-045
struck it. That is D-045's lesson recurring inside D-045's own neighbourhood: **a supersession that
is not visible from the superseded record has not been delivered.**

🔴 **And one thing this amendment deliberately does NOT resolve. D-045 carries `Ruling: B.` with no
`SIGNED by [andres]` line**, where the neighbouring records have one — verified on disk 2026-09-12.
**No signature is written here and none is implied**: this block relies on D-045 only for the
statement that the mirror-as-prerequisite is abolished, which is what its ruling says, and it
records the gap rather than closing it. `specs/003-demo-content/tasks.md` already carries the same
finding as its own entry; **[andres] confirms or rules, and until he does, "abolished by D-045,
**signed**" is a sentence nobody should write.**

⚠️ **Corrected within the hour, and both corrections are the failure this whole amendment is
about.** The paragraph above was first written citing the ruling at `DECISIONS.md:2455` and saying
*"three places nonetheless cite it as signed"*. Neither survived being measured.

* **The line citation was made stale by this very block.** Appending these paragraphs pushed D-045
  down by 29 lines, so `:2455` became `:2484` in the same commit that wrote `:2455`. It is now
  cited by its quoted `**Ruling: B.**` string instead, which moves with it. ⚠️ **The same insertion
  also invalidated `tasks.md:545`, which cites `DECISIONS.md:2455` and was correct when written.**
  That row is dated and append-only; it is **not** edited, and the displacement is recorded here
  because this is the commit that caused it. **A line number in a cross-file citation is a claim
  with a very short life**, and nothing in this repository checks one.
* **"Three places" was carried from a prompt rather than counted.** Read on disk 2026-09-12, before
  this amendment was written: a `grep` for `D-045` near the word `signed` returned **three lines**,
  and exactly **one** of them asserts the record is signed — `tasks.md:79`, *"ABOLISHED by D-045,
  signed"*. The other two, `tasks.md:542` and `:545`, **quote that claim in order to refute it**,
  which is the opposite of citing it. **Three matches, one claim**; reading the matches instead of
  counting them is the difference between a live finding and an already-corrected one.
  ⚠️ **No total is quoted, and the grep is described rather than given as a runnable line, because
  re-running it now returns this block too.** A figure that a paragraph changes by existing cannot
  be stated inside that paragraph — the first two attempts at this bullet each quoted a number that
  their own text had already falsified.

### D-037 · Does a chart module enter the SBOM for the budgets page?

⚠️ **SUPERSEDED IN PLACE 2026-09-05, and the new recommendation is B rather than A.** D-037 was
never signed, so its second text sits directly below the first instead of in a record of its own —
and **nothing here is deleted**. The argument for A is the cleanest short statement of this
project's default (rule 2: solve it with what Drupal CMS already ships), and a record that
silently improves reads as though it was right the first time. Read the original, then read
*D-037, second text* below it. **Both are PREPARED and UNSIGNED**; [andres] signs.

*Context in one line:* the ROADMAP asks for *"lightweight visualization + accessible table as the
source of truth (avoid heavy chart modules)"*; D-026 already ruled the table **is** the source of
truth; rule 2 forbids any contrib module without its `DECISIONS.md` line.

| | Option | Real cost |
|---|---|---|
| **A ★** | **No chart. The table is the deliverable.** | Zero SBOM growth, zero new attack surface, zero new accessibility surface. The screenshot is less striking. ⚠️ `drupal/charts 5.2.3` is in the 2026-08-20 SBOM research as *stable with security coverage* — so this is a genuine choice, not a constraint |
| B | Inline SVG generated by a Twig template in the theme, no module | No dependency. Costs real theme work and a genuine a11y surface — an SVG chart needs `role`, an accessible name and `aria-hidden` on decoration |
| C | `drupal/charts ^5.2` | A real dependency on the flagship **free** template, for one page. It pulls a JS charting library whose licence must enter the manifest, and it adds a component to unit 006's final SBOM sweep |

★ **A for v1**, with B as a unit-006 improvement if the screenshot needs it. The reasoning is the
project's own: *"when in doubt, solve it with what Drupal CMS already ships"* (rule 2), and the
table is not a fallback — D-026 already made it the primary artefact.

#### D-037, second text · The chart ships, drawn by the theme

**SIGNED = B by [andres], 2026-09-05.** He signed it by pointing at the approved mockup and saying
the chart was missing — <!-- cspell:disable -->*"La gráfica no está por ningún lado… esa no la
veo"*<!-- cspell:enable --> ("the chart is nowhere to be found… that one I do not see" —
translated, per rule 6) — which is the same instruction he had already given in different words on
2026-09-04, quoted in full below: <!-- cspell:disable -->*"a mí esa gráfica es de lo que más me
gusta… yo no la deshecharía"*<!-- cspell:enable -->. Two askings, four days apart, and the second
one arrived as a defect report about a page that did not have it yet. **A ruled *no* the signer
keeps asking for is not a saving**, which is what the second text said and is now settled.

⚠️ **AMENDED IN THE SAME BREATH AS IT IS SIGNED: the shipped shape is B-ii, not the B-i this record
recommends.** The sub-question below reads *"B-i ★ — the chart is its own Canvas component"*; what
shipped, at theme commit `bf46da9`, is a **trend line inside the first key-figure tile**, drawn
under the total it is a picture of. The recommendation is not deleted (rule 8) and it was not found
wrong — it was outranked by cost, and the cost is worth stating because it is the whole difference:

- **B-i costs the template a change; B-ii costs it nothing.** B-i needs one new Views display and
  one new component entry on the front page — a `canvas_page` edit in `agora_transparency` — so it
  is a **two-repository** change, and by this record's own legend that makes its evidence a
  rendered page on a rig rather than two job lists. B-ii ships **entirely inside `agora_theme`**,
  because it hangs off a block the front page already places. One repository, one pipeline, one
  commit.
- **What B-ii does not buy** is named rather than quietly dropped: the band-parity correction stays
  unmade and the two-column rhythm is still one pair short. That is design debt this signature
  leaves standing, not a defect it hides.
- **Nothing else about B moves.** It is still an inline SVG the theme draws, still
  `aria-hidden="true"`, still with the month-by-month figures beside it in the DOM, still no
  dependency and no JavaScript, and **D-026 still makes the table the primary artefact**.

**What shipped, measured — every figure below read off `bf46da9` in the theme checkout, not
carried from the design round.**

- **Eleven awards across a 24-month window, non-zero in 8 of them**, and the eight months are
  asserted individually against the table printed further down this record —
  `ThemeHelpersTest.php:655-664` pins `2023-05 … 2025-04` with both the awarded and the cumulative
  figure for each.
- **The rendered series ends on `€592,470.00` — the identical string the tile prints above it.**
  Identical rather than merely equal: the series' cumulative values go through
  `_agora_theme_format_amount()` with the same configured prefix and suffix the tile uses
  (`agora_theme.theme:1602-1606`), so the two cannot drift apart into a page disagreeing with
  itself. The unit in front of it is D-047's, signed the same day.
- **Window 24 months, row ceiling 750 rows — both PRINTED by the suite, not left in a comment.**
  `testBothCeilingsArePrinted()` writes `agora_theme award trend: window 24 months, row ceiling 750
  rows` to STDERR and then pins both values with assertions that carry their reason, plus a third
  asserting the ceiling stays above **731** — the most distinct award dates two consecutive years
  can hold. A constant nobody prints is a constant nobody can check (I-045).
- **The theme's PHPUnit job went from 50 tests / 145 assertions to 78 tests / 418 assertions.**

🔴 **The falsification is worth more than the rest of this entry, and it is the reason the suite is
shaped the way it is.** *A fabricated series built from constants still passes the "ends on the
correct total" test.* It has to: the function carries in everything outside the window as the
line's starting height, so the last point equals the grand total **by construction**, for any
corpus whatever — which is exactly what `bf46da9`'s own commit subject says out loud, *"ending on
that figure by construction"*. So the endpoint test proves the chart does not contradict the figure
above it, and proves **nothing at all** about whether the shape between the ends is this
municipality's real award history. **Authenticity is carried by the month-by-month assertion, and
by that alone.** Delete those eight rows and every remaining test stays green over a straight line
drawn from nothing.

⚠️ **Provenance limit on the two counts above, stated because this project treats an unstated scope
as a defect.** 78 / 418 was measured locally at `bf46da9`; the theme's newest **pushed** commit was
`0bf84e3` (pipeline `948043`) when this was written, so the `phpunit` job has not yet seen it, and
the theme's HEAD has already moved past `bf46da9`. It is a dated measurement of one commit, which
is all it claims to be.

**The original preparation note follows, unedited.**

**PREPARED by [ejecutor] 2026-09-05 — UNSIGNED. [andres] signs; nothing below is a ruling until
he does.** Nothing has been implemented against it: **T-1305** is written and blocked on this
record. Note what it would do to the task list — T-1305 **discharges T-1103**, whose success
criterion is already this chart's acceptance criterion verbatim, so this text closes signed scope
rather than adding any.

*Why the recommendation moved, and it was not a new measurement:* [andres] asked for the chart in
plain words on 2026-09-04 — <!-- cspell:disable -->*"pues a mí esa gráfica es de lo que más me
gusta, aunque sea solo visualmente. Obviamente no saldrá esa curva ascendente, probablemente sea
más una linea con ciertas 'ondulaciones' pero se ve bastante bonita, yo no la deshecharía."*<!-- cspell:enable -->
("that chart is one of the things I like most, even if only visually. Obviously that rising curve
will not come out; probably more of a line with certain undulations, but it looks quite good — I
would not throw it away." — translated, per rule 6.) A ruled *no* that the person who signs it
does not want is not a saving.

**What was measured before writing the options.** Every figure below was computed from
`content/node/*.yml` in this working copy on 2026-09-05, not taken from the design round.

- **The series is eleven awards, and they are all of them.** 7 `agora_base_contract` + 4
  `agora_base_grant`, summing to exactly **592,470.00** — the same total the front page already
  prints. Earliest period start `2023-05-15`, latest `2025-04-01`.
- **Non-zero in 8 months out of 24**, and the eight are not evenly spread:

  | month | awarded | cumulative |
  |---|---:|---:|
  | 2023-05 | 88,320.00 | 88,320.00 |
  | 2024-01 | 172,400.00 | 260,720.00 |
  | 2024-03 | 187,450.00 | 448,170.00 |
  | 2024-08 | 34,800.00 | 482,970.00 |
  | 2024-10 | 11,900.00 | 494,870.00 |
  | 2025-01 | 32,500.00 | 527,370.00 |
  | 2025-03 | 7,200.00 | 534,570.00 |
  | 2025-04 | 57,900.00 | 592,470.00 |

  ⚠️ **He predicted undulations; the data gives a staircase — so the honest chart and the shape he
  had already rejected by eye agree with each other.** Sixteen of the twenty-four months are flat,
  and two of them, 2024-01 and 2024-03, carry **359,850.00 between them, which is 60.7% of the
  whole series**. Cumulatively that is a step function with two large risers, not the smooth
  twelve-point ascent a mockup draws. This is the strongest argument available for shipping it:
  the chart will look like a small municipality's real award history, because it is one.
- 🔴 **THERE IS NO AWARD-DATE FIELD, and the row has to be written around that.**
  `field.storage.node.field_agora_base_period` is the **only** `daterange` or `datetime` field
  storage in the entire package — measured across every `config/field.storage.node.*.yml` — and it
  holds the **performance period**, not the date of award. The month must therefore be derived
  from the period start or from `created`, and **which basis is used must be stated on the page,
  never chosen silently**: a chart of money over time whose time axis is undefined is a chart
  making a claim nobody can check.
  ⚠️ **The two candidate bases disagree on exactly one award of eleven.** The earliest award's
  period starts `2023-05-15` while its node's `created` falls in **2023-06**; the other ten agree
  to the month. So the choice moves one award across a month boundary and a quarter boundary and
  changes nothing else. **Recommended: the period start**, because it is a value a reader can see
  on the record page, whereas `created` is a fact about the CMS — on a real installation it is the
  day somebody typed the record in, which is not a fact about public money at all.
- **A Views display that sums the amount is not on the table, and refusing it is not taste.**
  Summing `field_agora_base_amount` under a `group_by` display is the **exact SQL D-040 removed**,
  and `tests/bin/no-varchar-aggregate` exists to refuse it — it reports **8 views, 23 displays, 4
  aggregating, 9 field entries, 0 findings** as of today. That shape was wrong on **every**
  database Drupal supports: PostgreSQL errored, MariaDB answered `0` with a truncation warning,
  and SQLite answered `0.0` in silence. The option is closed by a signed decision and by a gate.
- **The mechanism that replaces it is already shipped and already gated.**
  `_agora_theme_award_totals()` at `agora_theme.theme:622` runs a `getAggregateQuery()` with
  `accessCheck(TRUE)`, aggregates `field_agora_base_amount` with `SUM`, returns the rows, the
  column alias and the list cache tags, and **catches the exception** — raising a warning and
  rendering the block without its money rather than taking the page down. The chart needs one more call of
  that same shape, not a new mechanism, and it inherits failure behaviour somebody already thought
  about.
- **The entity query cannot group by month, and the honest consequence carries a cost.**
  `groupBy()` groups by a column's raw value, not by a truncated month, so the query returns **one
  row per distinct award date** and the theme buckets them in PHP. Eleven rows here; at a real
  municipality with twenty years of contracts it is unbounded — **the same objection that closed
  D-040's option C**. So the query carries a **window** (trailing 24 months from the newest award)
  **and a row ceiling**, both written as named constants in the code and **printed by a test**, on
  D-038 option A's standard.
  ⚠️ **The demo corpus exercises neither of them, and a test written only against the demo would
  hide that.** The oldest award, `2023-05`, is *exactly* the twenty-fourth month back from the
  newest, `2025-04`: all eleven fall inside the window and none is ever discarded. A test run
  against `content/` alone therefore proves the window **exists** and proves nothing about what it
  **does** — the shape of I-062, a green over a result nothing filled. The window and the ceiling
  each need a fixture that crosses them, or the two printed numbers are decoration.

**Options.**

- **B (recommended) — an inline SVG drawn by the theme, `aria-hidden="true"`, with an adjacent
  accessible table as its equivalent.** Zero SBOM growth. The drawing carries nothing a reader can
  get only from it: the table beside it holds the same eight months and the same cumulative
  column, so the chart is decoration over data already published in a form a screen reader walks.
  The cost is real theme work and a real accessibility surface — which is what D-037's first text
  said about B, and is still true.
- **A — no chart; the table is the deliverable.** The first text's recommendation, kept above in
  full and not weakened here. Nothing in its reasoning was found wrong; it was outranked by the
  person who signs it.
- **C — `drupal/charts ^5.2`.** Rejected on **cost, not on taste**: a JS charting dependency on
  the flagship **free** template, for one component on one page, whose library licence enters the
  manifest and whose components enter unit 006's SBOM sweep. The 2026-08-20 research records it as
  stable with security coverage, so this is a real choice being declined rather than a constraint
  — and declining it is the judgement rule 2 already makes.

**Recommendation: B**, with the period start as the month basis and both ceilings printed.

**Sub-question, and it needs its own answer because the two are not the same amount of work.**

- **B-i ★ — the chart is its own Canvas component**, and becomes the partner of the spend table in
  the second two-column pair. It costs the template one views display and one component entry on
  the front page, it corrects the band parity the page currently gets wrong, and it completes the
  two-column rhythm.
- **B-ii — the chart hangs off the key-figures block.** It costs the template nothing at all, and
  it leaves the band-parity correction unmade and the two-column look one pair short — which is
  the thing the approved design round was about.

**What B does NOT do, named so nobody infers it:** it adds no dependency and no JavaScript; it
makes no claim the adjacent table does not already make; and it does not make the chart the source
of truth — **D-026 made the table the primary artefact and B leaves that untouched**. The chart is
`aria-hidden` because it is a second rendering of already-published data, not because the
accessibility question was hard.


### D-038 · How does a Dataset's CSV distribution become an accessible `<table>`, and in which repository?

*Context in one line:* **D-026 made the machine-readable table the source of truth** — budget is not a
node type, the Dataset **is** the budget execution table — and unit 002 recorded *"rendering a
Dataset's CSV distribution as an accessible `<table>`"* as owed debt. T-1006 authored the datasets
and their CSVs, all five of which download and parse; **the rendering half was not attempted**,
because **no core formatter parses a CSV into a table** and choosing what does is an architectural
call, not a task-row detail.

⚠️ **The disk contradicts itself about who owns it**, which is why this is a decision rather than a
ruling: `specs/003-demo-content/plan.md` §3 lists the debt under **"Theme repository"**, while
`tasks.md`'s T-1006 and the carried-debt table put it in **wave 10 lane A**, the template. Neither
was written knowing there was a mechanism question underneath.

*Measured, so the options are not argued from taste:* the display uses the `file_default` formatter,
which renders a download link. The five shipped CSVs are **small** — 5 to 12 data rows, 5 to 8
columns, 302 to 859 bytes — but nothing in the model bounds that, and a Dataset is exactly the field
a municipality will one day point at a 40 MB municipal register export.

| | Option | Real cost |
|---|---|---|
| **A ★** | **A preprocess hook in `agora_theme` reads the file and hands Twig a rows/header array; a template renders it with the theme's existing accessible-table markup.** | Rendering is the theme's job, the accessible-table markup already exists and is already gated, and it needs **no new dependency** — rule 2's "solve it with what Drupal CMS already ships" applied literally. ⚠️ Costs a **file-size ceiling and a column ceiling in code**, because an unbounded parse at render time is a memory fault waiting for its first real dataset, and both numbers must be printed rather than assumed. Also puts PHP in a theme, which this project has so far avoided. |
| B | A contrib module that provides a CSV formatter | Someone else maintains the parsing, the ceilings and the escaping. ⚠️ It is a **dependency on the flagship free template for one field**, it needs its own `DECISIONS.md` line under rule 2, its security coverage must be checked, and it joins unit 006's SBOM sweep. The 2026-08-20 SBOM research names no such module as stable-with-coverage, so this option starts with a search, not a package. |
| C | Ship the table **as content** — a second field on the Dataset holding the rendered table — and keep the CSV purely as a download | No parsing at render time at all, so no ceiling and no new code. ⚠️ **It contradicts D-026 head-on**: the CSV stops being the source of truth and becomes a copy that can silently disagree with the table beside it. That is the exact failure D-026 chose the machine-readable table to avoid. |
| D | Defer the rendering to **unit 006**, ship the download now | Honest and cheap today. ⚠️ It leaves the unit's own headline — *the accessible table is the deliverable* — false for two more units, and unit 006 is already carrying the SBOM sweep, the WCAG attestation and the keyboard walkthrough. |

★ **A**, with the two ceilings stated in the code and printed by a test rather than trusted: it is the
only option that keeps D-026's holding intact, adds no dependency, and reuses markup that is already
under an accessibility gate. **But B is a legitimate answer** if [andres] would rather not have PHP
in the theme at all — that is a maintenance preference, and it is his to hold.

⚠️ **Whichever is signed, the ownership contradiction is settled by the same signature**: A and C are
theme and template respectively; B is a template dependency; D moves the whole question. `plan.md`
§3 and the carried-debt table are then amended, not edited, to agree with it.

#### D-038 · SIGNED = A by [ejecutor] 2026-09-12 under [andres]'s standing delegation

**SIGNED = A.** T-1006's own row asked for exactly this and named both routes —
*"**Needs one word from [andres], or a ruling under standing delegation.**"* The word is delegated,
2026-09-12: <!-- cspell:disable -->*"Firma tú lo que haya siempre que esté todo correcto y
continua"*<!-- cspell:enable --> ("sign whatever there is yourself, so long as everything is
correct, and continue" — translated, per rule 6), the same delegation D-027 through D-030 and D-053
were signed on, with the same boundary: **methodology and licence-constrained choices are
[ejecutor]'s; product trade-offs are not.** ⚠️ **A is inside that boundary and B would have been
outside it.** D-038 says so itself — *"**B is a legitimate answer** if [andres] would rather not
have PHP in the theme at all — that is a maintenance preference, and **it is his to hold**"* — and
B adds a dependency, which is rule 2 territory. A adds nothing to the SBOM, so the choice is
between *"solve it with what Drupal CMS already ships"* and a dependency nobody has found; that is
methodology.

**RE-VERIFIED ON DISK BEFORE SIGNING, because a recommendation prepared 2026-08-26 is not a
recommendation confirmed 2026-09-12.** Every measured premise holds, and A's **only** cultural cost
has gone stale in A's favour:

| D-038's premise | measured 2026-09-12 | verdict |
|---|---|---|
| the display renders a download link via `file_default` | `config/core.entity_view_display.node.agora_base_dataset.default.yml` → `field_agora_base_distribution:` `type: file_default` | **holds** |
| five small CSVs, *"302 to 859 bytes"* | `content/file/`: 302 · 692 · 692 · 692 · 859 bytes | **holds** |
| *"no core formatter parses a CSV into a table"* — nothing renders one today | no parser and no table-from-file template in **either** repository. The only `csv` strings on disk are the field's `file_extensions: 'csv json xml ods rdf'`, the `format` vocabulary's `CSV` label, and a comment in the theme's `file-link.html.twig` | **holds** |
| the accessible-table markup already exists and is already gated | `agora-theme/templates/table.html.twig` and `views-view-table.html.twig`, both exercised by the blocking `nightwatch` axe gate | **holds** |
| ⚠️ *"Also puts PHP in a theme, which this project has so far avoided"* | **STALE — and this was A's only non-technical cost.** `agora_theme.theme` is **2,528 lines** carrying **28 functions**, and since 2026-09-02 a `phpunit` job that did not exist when D-038 was written covers them: `OK (102 tests, 571 assertions)` (CLAUDE.md's observed theme table, pipeline `950212`, job `12015107`) | **cost discharged**, not merely outweighed |

**So A is now strictly better than it was when it was recommended**: the thing that made it
uncomfortable — PHP in a theme — is already true, already tested, and already has a CI job to put
the parser's ceilings under. **The two ceilings D-038 requires are unchanged and binding:** a
**file-size ceiling** and a **column ceiling**, stated in the code and **printed by a test rather
than trusted**, because *"an unbounded parse at render time is a memory fault waiting for its first
real dataset"*.

**The ownership contradiction is settled by this signature, as D-038 said it would be: the owner is
`agora_theme`.** `specs/003-demo-content/plan.md` §3 was right — it lists the debt under the
heading *"Theme repository (`drupal/agora_theme`)"*, cited by that string rather than by a line
number, since appending to a file moves every line number in it and nothing here checks one — and
T-1006's row and the carried-debt table, which
put it in wave 10 lane A (the template), are **superseded, not edited** (rule 8). ⚠️ The template's
share is **zero new code and at most one config change**: whether the dataset's view display keeps
`file_default`, gains a second formatter, or renders both the table and the download link is a
display decision that follows the theme's implementation and does not need deciding here.

#### What T-1006's second half becomes: **SPLIT**, and it leaves this unit

The dispatch asked for one of three — done here, deferred to a named unit, or split. **It is split**,
and the three reasons are on disk rather than argued:

1. **The first half stays done and stays here.** `datasets` **5** rows; **5 of 5** distributions
   resolve at HTTP 200; every CSV parses with rows and columns printed (**9×7 · 9×7 · 4×8 · 2×5 ·
   12×6**). That is template work, it is evidenced, and nothing about it moves.
2. **The second half is not template work at all under A**, so it cannot be finished as a row in
   this unit's template lane. It is a theme preprocess plus a template plus unit tests plus the two
   ceilings — and, because the template resolves `drupal/agora_theme` from
   `packages.drupal.org` rather than pinning it, it also needs a **published theme release** before
   a clean install would receive it. That last step is [andres]'s (rule 10), which makes it a
   cross-repository piece of work and not a task-row detail.
3. 🔴 **D-044's necessity test, applied honestly: it FAILS, and that is why it is deferred rather
   than appended.** *Work without which something already signed is broken, false, or impossible to
   ship* — measured, none of that is the case:
   - **The registers already render as accessible tables** and are under the axe gate: D-053's
     nine-page scan covers the four register routes at **0 violations**.
   - **Nothing shipped reads the CSV.** The art. 8.1.a) statistic is computed from the imported
     entities (T-1005), and T-1305's cumulative-award chart draws from a bounded
     `getAggregateQuery()` — **not** from a distribution file. So no rendered figure depends on a
     table that does not exist.
   - **The download affordance is already good, not a placeholder.** The theme's
     `file-link.html.twig` renders the anchor as *"Budget execution 2024, CSV, 692 bytes"* —
     format and weight in the accessible name, which is what an open-data portal is supposed to
     offer.
   ⚠️ **What IS incomplete is a sentence in D-026**, and it is named rather than smoothed over:
   *"the machine-readable execution table … **is** the accessible table and feeds any chart."* Today
   it is a download, and no chart is fed by it. That makes D-026's parenthetical **not yet true** —
   a description of intent that the product has not reached — rather than a statement the site
   contradicts. **Incomplete, not false**, and D-044's word is *false*.
4. **Unit 003 is 31 rows over its ceiling** (65 against 34 — see this file's budget rider, signed
   today). Appending a cross-repository wave to it on a basis that is neither necessity nor
   [andres]'s signature is the precise move D-044's ⚠️ forbids: *"This is not a licence to grow
   scope."*

**Named unit: 006 (Hardening).** That is where D-038's own option D put it, and the alternatives are
worse fits by their own scope statements — unit 004 is editorial workflow and FOI, unit 005 is AI
and Config Guardian. ⚠️ **D-038's cost bullet for option D is accepted rather than waved away**: it
warned that 006 *"is already carrying the SBOM sweep, the WCAG attestation and the keyboard
walkthrough"*, and that is now one item heavier. **Unit 006's budget must carry this row when it is
written** — which is the accounting arriving before the unit rather than at its closure (I-105).
⚠️ **And one thing that is deliberately NOT constrained:** the theme releases on its own cadence
(D-050), so if a theme release happens for another reason the parser may ship earlier. Unit 006 is
the **accounting home**, not an embargo.

**T-1006's glyph does not move, and that is the correct answer rather than a convenience.** The
legend defines `⏸` as *"deferred with an owner and a named prerequisite"*, which is exactly its
state — only the owner and the prerequisite change. Before: blocked on a ruling, owner ambiguous
between two documents. **After: owner `agora_theme`, scheduled unit 006, no prerequisite on
[andres] except the eventual theme release.** The row is not edited (rule 8); the change is recorded
in this block and in `tasks.md`'s appended state section.

**The success criterion travels with it, verbatim, because it is the part that makes the work
falsifiable:** *"the rendered table's row count equals the CSV's row count, asserted, so a table
that silently truncates is a finding."* ⚠️ To it, this signature adds the two ceilings: **the
file-size and column limits must be printed by a test**, so that a parse refusing an oversized file
is a stated, measured refusal and not a blank `<div>` (I-062 in a third shape — an empty table
reports no violations, truthfully and about nothing).

### Rider requested · The accessibility statement's unit

Not a decision, a **contradiction between two on-disk documents** that needs one word.
`ROADMAP.md:101` → unit 003. `specs/002-base-and-theme/plan.md:94` → unit 006.
✅ **RULED 003 by [andres], 2026-08-26.** The statement stays in this unit as **T-1105** and the
budget stays at **34**. The reason it was worth one word rather than a shrug: the statement's
substance **is** this unit's axe denominators — pages scanned, rules run per page, and by name the
three WCAG 2.2 criteria axe cannot check — and one written in unit 006 against numbers taken in
unit 003 rots in the gap. ⚠️ `specs/002-base-and-theme/plan.md:94` still sends it to unit 006 and is
**not edited** (rule 8); this ruling supersedes it, and a reader arriving at unit 006 should find
this line rather than rediscover the contradiction.

**Recommendation as written before the ruling:** **003**, as **T-1105**, because the statement's
substance is this unit's axe denominators and a statement written in 006 against numbers taken in
003 rots in between. If [andres] prefers 006, T-1105 leaves this unit and the budget drops to 33.

### Rider requested · Four-digit task ids from wave 10

`specs/002-base-and-theme/plan.md:137` predicted this expiry. It touches the signed
`T-<wave><nn>` convention, so it is noted rather than assumed: unit 003 runs waves **9-12**, ids
`T-901…T-1206`, and every count in the unit uses the anchored, bounded regex recorded in
`specs/003-demo-content/plan.md` §4 — **verified collision-free by command**: it returns **30**
against unit 003's `tasks.md` and **0** against both unit 001's and unit 002's.
**The convention is unchanged; only its rendering widens.**

### ⚠️ Two divergences found on disk during this scaffolding, recorded rather than resolved quietly

**(a) The accessibility statement is assigned to two different units by two on-disk documents** —
the rider above. **(b) Visual regression is assigned to two different units**: the 002 plan's NO-list
sends it to unit 006, while T-804, amended two days later with its evidence, defers it to unit 003.
The tasks.md amendment is later and names its evidence, so it governs — but the 002 plan's NO-list
was never amended, so a reader arriving at unit 006 will find visual regression waiting for them
there too. Neither is edited here (rule 8); both are recorded so the next reader is not the one who
discovers them.

---

### D-039 · Does the template ship generated imagery, and under which licence row?

**SIGNED = A by [andres], 2026-08-27.**

*Context in one line:* three consecutive reviews of the front page ended in the same sentence —
*"it still does not land as a site's HOME... I still find it dull"* (translated from [andres]'s
own words, per rule 6) — and the question that followed
it is the one this project exists to answer: **would anyone pick this off the marketplace?** The
honest answer was no, and the diagnosis was structural rather than aesthetic: the front page is
navigation, not content, and **the package contains no image at all**. `ls config | grep -c image`
returns **0**; T-1007 signed that absence deliberately, which is why adding a field is a decision
and not a task.

*Measured at source on 2026-08-27, so the options are not argued from assumption:*

- **Drupal.org has an adopted AI policy** (last updated 2026-06-10) covering **code and text**, with
  a mandatory disclosure format. It says **nothing about images or media**. "No policy found" is a
  finding; it is not a permission, and it is not a prohibition either.
- **The marketplace criteria ask for a manifest, not GPL purity**: *"components like default content
  and images may carry a proprietary licence"*, provided they are listed. This is more permissive
  than this project had been assuming.
- **OpenAI assigns output ownership "if any"** — the hedge is theirs — and forbids representing
  output as human-generated when it is not.
- **The USCO holds prompt-only output uncopyrightable** (2025-01-29). So a GPL grant over it is a
  **no-op — and a no-op breaks nothing**: redistributing a work in which nobody holds rights is
  more clearly permissible than redistributing a CC-BY photograph. The licence question resolves
  in our favour; the risk that survives is **provenance honesty**, not GPL compatibility.
- **The competition ships images and declares none.** `haven` ships **24**, `byte` ships **14**,
  mostly Unsplash-named, and **neither declares the provenance of a single one**. Five of `byte`'s
  were downloaded and their bytes scanned for C2PA, XMP and generator strings: no AI marker found,
  so no claim is made that either ships generated imagery.

| | Option | Real cost |
|---|---|---|
| **A ★ SIGNED** | **Add an image field, ship generated imagery, declare every file as `CC0-1.0` with an explicit dedication** — author column reading *"generated with OpenAI &lt;model&gt;; no human authorship claimed"*, source column carrying tool, model and date. | Truthful in every column, and it **does not touch the `media-licence` allow-list**, whose FORBIDDEN clause exists precisely to stop a red row being fixed by widening the list. ⚠️ Costs: a reopened content-model decision, new axe surface on every page an image reaches, and **extending `no-secrets` to C2PA/JUMBF as a prerequisite rather than a follow-up** — ChatGPT images carry Content Credentials, and level 3 sweeps EXIF and XMP only, so the first shipped image would arrive carrying metadata nothing counted (I-045's shape). |
| B | Ship nothing; close with zero images | Costs nothing and reopens nothing. ⚠️ **Rejected, and against the auditor's own recommendation.** Its argument was that the product does not need imagery and that 203 KiB against `haven`'s 75 MB is a selling point. Nobody chooses a site template by its download size, and shipping zero images against competitors carrying 24 is a differentiator pointing the wrong way. The auditor optimised for auditability; the marketplace decision optimises for being chosen. **The advantage was never having no images — it is being the only one that declares where they came from.** |
| C | Own-work geometric SVG only | Zero licence question, zero C2PA, nothing reopened. ⚠️ Kept as the fallback if the falsification below fails, and used **alongside** A regardless: the portico grammar is the theme's, and illustration is not a substitute for the thing a citizen recognises as a page. |

⚠️ **Two constraints ride with the signature and neither is negotiable.**

1. **`screenshot.webp` (T-1106) is never generated.** Its criterion requires a rendered demo page
   carrying real content. A generated screenshot would misrepresent the product to a reviewer,
   which is a worse failure than any licence question in this record.
2. **`own work` is the one row generated imagery may not carry.** It is false under the USCO
   position and arguably breaches OpenAI's own terms. `CC0-1.0` plus dedication is the route that
   is truthful in both directions: where rights exist the dedication grants them, where none exist
   the effect is identical.

⚠️ **Open falsification, owed before the first image is packaged:** press reporting from 2024
describes generated output carrying a **visible "CR" symbol**. If current output still does, option
A dies on aesthetics before any of this matters, and C becomes the answer. One throwaway image
settles it; it has not been done at the time of signing.

---

### D-040 · Two front-page blocks hard-error on PostgreSQL. What ships?

**SIGNED = A by [andres], 2026-08-27.** *(Config-safe now; the euro total reopened as its own
investigation rather than abandoned.)*

*Context in one line:* the key-figures and spend-by-area blocks aggregate `SUM()` over a Field API
field, and **Views drags that field table's `langcode` and `bundle` columns into the same
aggregate**. Both are `varchar`.

*Confirmed by reading core in the rig and by executing the generated SQL against three databases —
not cited, measured:*

| database | `SUM(varchar)` |
|---|---|
| PostgreSQL 16.15 | **`ERROR: function sum(character varying) does not exist`** |
| MariaDB | `0`, with `Warning 1292 Truncated incorrect DOUBLE value: 'en'` per row |
| SQLite | `0.0`, silently |

⚠️ **Read the MariaDB and SQLite rows again: the SQL this package ships is wrong on EVERY database,
not only on the one that complains.** PostgreSQL is the only one honest enough to refuse. That is
the more disturbing half of this record and it is why D-040(2) exists.

The cause is core, open since 2018 and unfixed in 11.4.5: [#2975149] names our exact line
(*"`FieldPluginBase::addAdditionalFields` applies that grouping to each additional field"*) and
[#3018025] is our exact symptom, **Major**, Active. Patching is closed by non-negotiable rule 1.

*Four config workarounds were probed; three died measured.* The survivor is the useful finding:
**keeping the SUM as a SORT emits a correct, PostgreSQL-safe `SUM()`** — `GroupByNumeric::query()`
never calls `addAdditionalFields()`. The database computes the true total. What Views cannot do is
**print** it, because its text tokens come from field handlers, not from raw result columns.

| | Option | Real cost |
|---|---|---|
| **A ★ SIGNED** | **Config-only fix now: the aggregated SUM *fields* go, the SUM *sort* stays — so rows remain ordered by real spend and the bars stay truthful — and the euro total is reopened as its own investigation rather than written off.** | Nothing broken ships, today, with no dependency and no cross-repository coupling. ⚠️ It temporarily costs the single most meaningful line a transparency portal can print. **That is why the second half of the signature is not decoration**: the auditor recommended stopping at the config fix, and stopping there would replace *"€592,470 awarded in total"* with *"largest single award"* and turn the chart from money into a count of files — gutting the feature whose absence caused three consecutive reviews to end in *"it still does not land as a HOME"*. |
| B | Theme prints the sort's SUM from `hook_views_pre_render()` | Verified reachable (`ViewExecutable.php:1586`, *"Let the themes play too"*), restores the real total at O(1). ⚠️ It depends on a **sort's side-effect** and an undocumented column alias - the exact cleverness a future maintainer breaks - and it puts template config in dependency on theme PHP, which D-014 split apart on purpose. **Not rejected: deferred into the investigation, where `entityQueryAggregate` is to be probed FIRST as the documented-API route.** |
| C | Theme sums a non-aggregated `$view->result` in PHP | Honest, no internals. ⚠️ Requires loading **every** row: fine at 56 demo nodes, unbounded at a real municipality's 50,000 contracts. A site template must not ship a front page that degrades with the site's success. |
| D | Add `views_aggregator` to the SBOM | Measured stable, security-covered, and named by core's own issue. ⚠️ **Not measured that it avoids the SQL**, and it is a *table style plugin*: its markup is not core's, so `views-view-table.html.twig` - the override a whole wave and 39 assertions were spent on - would not apply to it. A dependency, new axe surface and a lost accessibility asset, for one figure. |
| E | Bake the figures in as static content | ⚠️ A transparency portal whose "total awarded" does not move when a record is added is worse than no number at all. |
| F | Revert both blocks | Genuinely zero risk, and genuinely on the table. ⚠️ Reopens the unanswered marketplace question. |
| G | Patch core | **Closed** by rule 1. Named to close it. |

---

### D-040(2) · Gate A was green on SQL that is wrong on every database

**SIGNED = "PostgreSQL in CI, now" by [andres], 2026-08-27.**

Nine jobs, all `success`, all blocking, 1951 assertions — while the shipped query summed a text
column on every database Drupal supports. **The assertions passed because nothing read that
column.** This is I-007 and I-045's shape at its purest: a green is a statement about the set it
opened, and no set anyone opened contained PostgreSQL.

⚠️ **`jobs >= 9` all-success was satisfied while the product was broken on a supported database.**
The gate definition in D-023(5) is not weakened by this and does not need amending — it was never
a claim that the job list is *sufficient*, only that the status field is *insufficient*. What this
record adds is the corollary: **a job list is only as good as the environments it runs in**, and
this one ran MySQL and SQLite because those are the defaults, not because anyone chose them.

The fix is a PostgreSQL `phpunit` variant. Upstream already supports it — `POSTGRESQL: 'pgsql'`
in `include.drupalci.hidden-variables.yml`, consumed at `include.drupalci.main.yml:289` — so it is
reachable **without redefining an upstream job**. ⚠️ If it turns out it cannot be, that is a new
decision and not a task: stop and escalate rather than weaken a gate to fit.

Deferring to unit 006 was the alternative and was declined: it would have left the false green
standing for two more units, and an intention without an owner and a date is not a plan.

---

### D-040(A) · AMENDMENT, 2026-08-27 — "the bars stay truthful" was false

D-040(A) as first written says the SUM sort's survival means *"the row order, and therefore the
bars, remain truthful."* **The second half is wrong, and it was wrong when I wrote it.**

The `<span class="agora-bar">` the theme paints into is emitted by the **rewrite on
`field_agora_base_amount_1`** — one of the three entries the fix removes. Measured on the rig after
the fix, against the real released theme `1.0.2`: `--agora-bar` is set on all **6** rows and
`class="agora-bar"` appears **0** times. The order is truthful; there is nothing left to draw.

⚠️ **And the theme's own guard could not see it.** It fires when the amount is missing from
`$view->result` — and the sort keeps it there, so the theme computed six correct percentages for a
span that no longer exists. **The chart stopped being a chart silently**, which is the failure the
theme's own header says it refuses to rely on a CSS fallback for. One diagnostic was captured
during a `block_4` render and it was an unrelated Symfony deprecation.

The record is amended rather than edited (rule 8) because the error is instructive: **a claim about
a second repository was made from inside the first, without measuring there.** D-014 split these
two on purpose, and a sentence that reaches across the split needs a measurement on the far side.

*What ships in the meantime, and it is honest rather than good:* the spend table renders **Service
area · Awards**, ordered by real total spend, with the ordering stated in the caption and the
heading renamed to *Contracts and grants by service area* — because *"Awarded spend by service
area"* over a table containing no spend is what a marketplace reviewer flags. The euro total's slot
in the key-figures block is **left empty rather than filled**: three figures, not four, and no
substitute figure that could become permanent by inertia.

**MEASURED ON POSTGRESQL 16.15, 2026-08-27, closing D-040's open investigation.** Three shapes
were executed against a throwaway `postgres:16-alpine` — not read, executed, because reading SQL
and calling it safe is the mistake D-040(2) exists to record:

| shape | result |
|---|---|
| the `entityQueryAggregate` route (documented API) | **592470.00** |
| the Views aggregation D-040 removed | `ERROR: function sum(character varying) does not exist` |
| the `GroupByNumeric` sort D-040 kept | runs, orders correctly |

**All three hypotheses confirmed.** The euro total is recoverable on PostgreSQL by a documented
API, the removal was necessary, and the sort that was kept is safe. What remains open is not
whether it *can* come back but **where the code lives**: the package may ship none, so it would be
theme PHP, and the theme has no `phpunit` job. That is the decision, and it is narrower than it
was this morning.

---

### D-041 · Does the demo municipality stay Spanish, or become British?

**SIGNED = A by [andres], 2026-08-27** — *"whatever is recommended here"* (translated from
[andres]'s own words, per rule 6), given after reading the evidence below. ⚠️ **Recorded because the signature is easy to misread: option A means the rename
does NOT happen.** He had earlier chosen a fictional anglophone council and expected a rewrite;
what he authorised is keeping the Spanish municipality and stating the frame on the site. Two task
rows, no corpus rewrite, no reopened content model.

⚠️ **This recommendation contradicts a choice [andres] has already voiced.** He chose *"a fictional
anglophone council"*, I proposed **Wealdhurst District Council**, verified it fictional (zero exact
and zero near matches across 28,421 UK place names) and told him the rewrite was authorised. **The
evidence below arrived after that exchange.** It is put in front of him for the same reason D-035
was: a measurement overturned a preference, and the measurement wins or it does not, but it gets
read first.

*Context in one line:* the corpus is **coherently Spanish in substance and English in language**.
The question is whether to change the substance to match the language, or to state the frame so
that the English-language reading stops being the wrong one.

**Measured, at source:**

- **The Local Government Transparency Code 2015 does not map onto D-026.** Applying **D-026's own
  rule** — a category earns a bundle when the law names three or more non-prose fields — to the
  Code's categories: **2 of 6 map cleanly** (`Grant`, `Document`), **2 map partially with the wrong
  fields** (`Contract` has 5 fields wrong for the UK and lacks 5 the Code mandates; `Person`
  publishes exact remuneration and severance for named elected members, which **no UK council
  does** — the Code requires GBP 5,000 brackets and names individuals only above GBP 150,000), and
  **1 has no counterpart at all** (`Agreement`: England folds agreements into the contract
  register).
- ⚠️ **The single most recognisable UK artefact is absent from our model.** *Expenditure over GBP
  500* **is** what "local government transparency" means to a British reader. Ágora has no bundle
  for it.
- ⚠️ **Removing `Agreement` inverts D-026's central refutation.** D-026 rejected a single financial
  record type *"on Spanish administrative law, not on taste"* — a statutory exclusion and a national
  subsidy register. **Neither exists in England**, so under the Code, D-026's own rule yields the
  option D-026 refuted. That is the model's spine, not a trim.
- ⚠️ **"District Council" names a body type English law is abolishing.** The English Devolution and
  Community Empowerment Act 2026 received Royal Assent on 2026-04-29; vesting day for the
  replacement unitary authorities is **2028-04-01**. The fictionality proof is sound and irrelevant
  to this.
- ⚠️ **The product's own scope statement only holds in Spain.** `CLAUDE.md` says Ágora is for
  *"small municipalities"*. In England the corresponding body is a **town or parish council**, which
  publishes under a much lighter regime and would have **no contract register, no senior salaries,
  no grants register**. The body that publishes Ágora's six registers is a principal authority,
  which is not small. **There is no English body that is both small and publishes what Ágora
  models.** In Spain there is: every municipality, however small, is bound by the same statute.

| | Option | Real cost |
|---|---|---|
| **A ★** | **Keep the Spanish municipality. State the frame on the served site** — one sentence on the home page and a paragraph in `README.md`: a fictional **Spanish** municipality, published in English per D-035, six registers derived from the Spanish transparency statute. | **About 2 task rows**, fits the unit's remaining reserve. D-010's rider, D-026, D-033 and D-035 all untouched. **Zero gate movement.** The precedent is already on disk and is the strongest available: D-033 records that **the French State Design System ships English config and delivers French as a translation.** ⚠️ It does not make the content *feel* British — it is not meant to. **The real defect is that the demo is coherent and nobody is told, and the fix for a legibility defect is legibility, not relocation.** |
| B | Rename to a UK council, keep the Spanish-derived model | ⚠️ **The only option that makes the incoherence WORSE, and that is why it is named rather than quietly dropped.** Today the name and the substance agree and only the language differs; after B they disagree — a British-named council publishing nine Spanish budget chapters, a municipal police force and a social services department a district council cannot lawfully have — **with a British label now vouching for it.** About 10 rows across two repositories. |
| C | Rename **and** re-derive the model against the Code | Honest and defensible, and the only version that produces a credible UK portal. ⚠️ **Reopens D-026**, which all of unit 002 and the whole of unit 003's corpus were built on. It is a **unit, not a wave**, and it needs a dated research file first. Also needs a body type that survives 2028. |
| D | A jurisdiction-free fiction ("Wealdhurst Council", no country) | Cheap in rows and **worse than A** on the only axis that matters: it leaves the Spanish-derived categories with no jurisdiction to attribute them to, so they become **unexplainable** rather than merely unexplained. |

★ **A.** It is the only option in which the content model's legal derivation, the research file that
supports it, the product's own scope statement and the shipped corpus are **all true at the same
time** — and the only one whose cost fits the unit's remaining budget.

**The argument against A, at full strength, because it is real:** a reviewer who does not open
`DECISIONS.md` sees an English-language site publishing Spanish legal instruments and reads it as
sloppy rather than deliberate. That is exactly the defect option A exists to fix, and it is why A
costs two rows rather than zero.

⚠️ **Two riders if B or C is signed instead**, both blocking, neither optional:

1. **`no-real-people` becomes structurally dead code.** Five of its seven detection shapes are
   Spain-specific; under a UK corpus they match nothing while the script keeps printing *"7 shapes"*
   and exiting 0. **That is a silenced invariant by omission — the automatic-🔴 species — and it
   would pass the gate.** It must be rewritten in the **same commit** as the corpus, never after.
   ⚠️ And verification gets *weaker* as risk rises: an invented Spanish full name carries two
   surnames and is very unlikely to collide with a real person, which is what made D-010's rider
   provable against a national name dictionary. A plausible British name has thousands of real
   bearers, several of them serving councillors, **and there is no single machine-readable national
   register of UK councillors to check eight invented names against.**
2. **The unit is at 30 of 34 rows with all four reserve rows already allocated by name.** About 10
   rows does not fit; a budget rider naming what it displaces is required.

**D-035 needs no amendment under any option** — it has no jurisdiction and does not acquire one.
**D-033 is untouched under A**, and **amended, never superseded**, under B or C. ⚠️ Recorded in
fairness to B and C: under a UK frame D-033's exception list would go to **zero**, and the Spanish
language spans would leave `config/` entirely. That is the one genuine technical gain of renaming.

---

### D-042 · `MEDIA-LICENCES.md` cites a build script that does not exist

**SIGNED = A by [andres], 2026-08-27** — same instruction, and with the budget consequence
accepted: the generator is written, which takes unit 003 to **34 of 34**. ⚠️ **The two named risks
still open — the GitHub-mirror prerequisite and the accessibility-statement ruling — therefore
each need a D-031 rider if they need a row.** That is now a certainty rather than a risk, and it is
stated here so the rider is written deliberately and not discovered at closure (I-105).

*Context in one line:* all **39** manifest rows give their `source URL` as *"generated by the
template's own build script"*, and **that script is nowhere in this repository** — verified by
searching `tests/bin/` and the whole tree, not assumed from the earlier audit that first raised it.

⚠️ **The file indicts itself.** Its own header says the manifest exists so that *"the claim can be
re-checked by someone else"*, and cites the fact that **neither published site template documents
any media licence at all** as the reason Ágora must. A provenance claim pointing at an unversioned
artefact is the one failure this file was written to prevent, committed by the file itself.

And it is the exact question Drupal.org's third-party asset policy asks — *"Who authored it?"* and
*"Where or who did you get it from?"* — so it is marketplace-relevant, not merely tidy.

*Measured, 2026-08-27:*

- **34 of 34** shipped PDFs carry `Fuentelclaro Town Council` in their page text.
- The PDFs are **real and well-formed**, not stubs: `%PDF-1.4`, two pages, fourteen text operations
  carrying title, council, document type, responsible area, financial year and summary. Their
  ~1.8 KB size is correct for a one-page document and is **disclosed in the link text**.
- The manifest carries **no hashes**, so regenerating the binaries would not move its 39 rows.
- ⚠️ **`/tests` is `export-ignore`d**, so a generator committed to `tests/bin/` lands in the
  repository — where a reviewer can re-check the claim — and **not** in the packaged tarball.
  **The packaging cost of option A is therefore zero**, which is the fact that decides this.

| | Option | Real cost |
|---|---|---|
| **A ★** | **Commit a generator to `tests/bin/` that produces the 34 PDFs from a data source, and point the manifest's `source URL` at it.** | Makes the claim true, and makes every future content change tractable: regenerating after a rename becomes a script run instead of 34 hand edits — which materially lowers **D-041**'s option B and C costs. Zero packaging cost. ⚠️ It need not reproduce the current bytes; it needs to become the source of truth going forward, and the binaries it emits must carry **no metadata** (`no-secrets` level 3 sweeps EXIF, XMP and now C2PA). |
| B | Amend the manifest to say the artefacts are hand-authored and unreproducible | Honest and costs almost nothing. ⚠️ But it **weakens the exact claim the file exists to make**, on the one axis where this template is currently ahead of both published competitors. Trading a differentiator for an afternoon is the wrong side of that bargain. |
| C | Leave it | ⚠️ Named only to close it. The manifest would keep asserting a source that cannot be inspected, in a package whose subject is accountability. |

★ **A** — but ⚠️ **the recommendation is technical and the decision is budgetary, which is why this
is [andres]'s and not mine.** Unit 003 stands at **33 of 34** with **one reserve row left and two
named risks still open** (the GitHub-mirror prerequisite and the accessibility-statement ruling).
Spending the last row here takes the unit to its ceiling and guarantees a D-031 rider if either
risk needs a row. **That is precisely the silent-reserve-spend I-105 was recorded to prevent, so it
is stated instead of done.**

Deferring to unit 006 is a legitimate fourth answer and costs nothing today, provided it gets an
owner and a date rather than an intention.

---

### D-043 · When does `agora_theme` opt into security advisory coverage?

**SIGNED = "when the template is stable, not before" by [andres], 2026-08-27.**

*Context in one line:* the project page carries *"This project is not covered by the security
advisory policy. Use at your own risk!"*, and non-negotiable rule 2 requires security coverage of
every SBOM dependency — `agora_theme` **is** a dependency of the template, so the project currently
fails its own rule with its own theme.

⚠️ **[andres]'s objection is recorded because it corrects a framing of mine.** I presented opting
in as an overdue chore. He answered that marking something covered *"until this is finished is a bit
odd"* (translated, per rule 6). He is right about the optics and I was wrong to push on timing:
**opting in is a commitment to handle security issues through the security team's process, not a
claim that the code is finished** — but publishing that commitment over a template still being
rebuilt weekly invites a report against a version nobody intends to keep.

*Measured, at source (2026-08-27):* coverage requires a **vetted** maintainer role — **[andres]
has it** — a **full project**, **no open issues tagged `security`**, a **stable release**, and
**ten days since project creation**. The project was created **2026-08-24**, so the window opens
**2026-09-03** regardless of intent.

**The ruling: opt in when unit 003 closes and the template is stable, and not before.** The date
is not a deadline to hit; it is the earliest possible day. ⚠️ It is the same date D-034's
`sbom-check` exemption for `agora_theme` expires **by failing** — so the invariant will go red on
2026-09-03 and that red is the reminder. It is not to be silenced; it is to be answered by opting
in or by a dated, signed extension.

---

### D-044 · The task budget counts; it does not gate

**SIGNED by [andres], 2026-08-27:** *"do not let the task maximum stop you if something has to go
in out of necessity"* (translated, per rule 6).

*Context in one line:* D-031 set a per-unit task ceiling and made crossing it cost a signed rider
naming what it displaces. Three times in two days the ceiling produced a **pause for a signature on
work that was plainly necessary** — the PostgreSQL fix, the invariant that catches it, the media
generator — and each pause cost more than the accounting was worth.

**The ruling: the count is an instrument, not a gate.** Necessary work goes in and the row is
appended; the ceiling is not a reason to stop, to ask, or to defer. ⚠️ **What does NOT change is
the accounting**, and that is the whole point of writing this down rather than deleting the
budget: I-105 stands. When the reserve is drawn on for something outside its named list, or when
the ceiling is crossed, **the budget line says so in the same commit**, with what remains and what
is still owed against it.

So the discipline moves from *"ask before exceeding"* to *"exceed when necessary and state it"*.
The failure this prevents is unchanged — discovering an overrun at closure, when the only options
left are a rider signed under pressure or a quiet trim.

⚠️ **"Necessary" is not "useful".** This is not a licence to grow scope: it covers work without
which something already signed is broken, false, or impossible to ship. Work that is merely good remains
a proposal with options and a recommendation, the same as before. **D-031's rider is not abolished
— it stops being a precondition and becomes a record.**

This amends **D-031**'s mechanism (rule 8: amended, not edited) and leaves its reason intact.


---

### D-045 · The visual-regression gate runs on drupalcode. The GitHub mirror is not a prerequisite.

**Amends D-009, which chose option A on a premise it told us to verify and nobody did.**

D-009 was written 2026-08-20 and its context sentence is the whole of this decision:

> *"It could not be verified whether the drupalcode runners support Playwright + axe."*

It then chose **A** — linters and PHPUnit on drupalcode, Playwright and axe on the mirror's GitHub
Actions — and said, in its own recommendation: *"★ A, but **verify first**: if drupalcode supports
Playwright, B is cleaner (a single gate). Decision reviewable in wave 2 of unit 001."*

**It was never verified, and it is false.** Measured 2026-09-01 by reading the trace of the theme's
`nightwatch` job on pipeline `943665` with the maintainer's token: the job runs with
`CHROMEDRIVER_AUTOSTART=true` and chromedriver. **The drupalcode runner already drives a real
headless Chrome** — that is how axe analyses rendered pages, and it has been doing so on every push
since the axe gate existed. The capability D-009 could not confirm has been demonstrated by a
blocking job in our own pipeline for weeks.

⚠️ **The cost of not checking was not a wrong CI file. It was a blocker assigned to the human.**
T-804 was deferred out of unit 002 and T-1202 out of unit 003, both with *"prerequisite: [andres]
creates the GitHub mirror"* — a task he did not need to do, holding up the only automated check
that looks at what the theme actually renders. In the meantime every visual defect was found by
him, by eye. Three were fixed on 2026-09-01 alone.

**Ruling: B.** Playwright functional and visual regression run on drupalcode, alongside the nine
jobs already there. One gate, one place, and a reviewer on Drupal.org can re-run it.

**The mirror is not abolished** — D-016 keeps it as a read-only mirror and D-020 keeps the GitHub
install smoke as an informative second opinion. What is abolished is the mirror being a
**prerequisite** for anything.

⚠️ **The lesson is the generalisable part and it is not about CI.** A decision that says *"verify
first"* and is then acted on without verifying is worse than one that never mentioned verification:
it reads, to everyone downstream, as though the check was done. Six task rows cited D-009(d) as
settled fact. **A recommendation carrying an unmet precondition must not be quoted without the
precondition** (see I-045, the same defect in the shape of a denominator).

#### D-045 · Amended 2026-09-12 — NOT SIGNED, and the reason is that its account of D-009 does not survive being read on disk

**Nothing above is edited** (rule 8). This block is appended because D-045 was put in front of me
to sign under [andres]'s standing delegation of 2026-09-12 —
<!-- cspell:disable -->*"Firma tú lo que haya siempre que esté todo correcto y continua"*<!-- cspell:enable -->
("sign whatever there is yourself, so long as everything is correct, and continue" — translated,
per rule 6) — and **his delegation is conditional on its own clause,
<!-- cspell:disable -->*"siempre que esté todo correcto"*<!-- cspell:enable -->
("so long as everything is correct"). It is not correct.** So it stays unsigned, and the two things
that actually needed closing are closed below on a different and stronger basis.

🔴 **THE FINDING: D-045 says D-009 chose option A. D-009 was signed as option C.** Read on disk
2026-09-12, and both halves are verbatim:

- D-045's own second line above: *"**Amends D-009, which chose option A on a premise it told us to
  verify and nobody did.**"*
- `DECISIONS.md`, the signed entry: *"**D-009** · **Where the visual tests run: option C.** Signed
  by [andres] 2026-08-21."* Its next sentence is the one that settles it: *"⚠️ **C is a third
  option, not one of the A/B framed on 2026-08-20 above** … It supersedes that framing."*

**What D-045 quoted was the recommendation, not the ruling.** The 2026-08-20 framing does say
*"★ **A**, but **verify first**: if drupalcode supports Playwright, B is cleaner (a single gate)"*
— that quotation is exact, and D-045's lesson about an unmet precondition being quoted as settled
fact is **correct and worth keeping**. But **A was never signed.** ⚠️ **And the defect is D-045's
own lesson happening to D-045**: a recommendation was read as a ruling, one record away from where
the ruling is written.

**What D-009 = C actually says, because it changes what D-045 is amending:**

1. *"**Accessibility (axe) → drupalcode, canonical and MANDATORY.**"* — so axe was **never** on the
   mirror. D-045's mirror half can only ever have concerned visual regression.
2. *"**Visual regression → GitHub Actions, NON-blocking.**"*

⚠️ **So D-045 amends C(2), a clause [andres] wrote deliberately after being handed the A/B
framing — not an unverified default. That is the whole reason this is not mine to sign.** C's own
*"Facts verified 2026-08-21"* include *"`gitlab_templates` ships **no** Playwright job, no axe job
and no visual-regression job; Nightwatch is its only browser tool."* **That fact is still true**,
and D-045 does not refute it: D-045 measures that the runner drives a real headless Chrome — which
it does, and the theme's blocking `nightwatch` job is the standing proof — but a Playwright job on
drupalcode would still have to be **hand-written**, exactly as C recorded. Capability and provision
are two claims, and only one of them was measured. Moving visual regression off GitHub is therefore
a **product and process trade-off against a signed human choice**, and the delegation's boundary
(methodology and licence-constrained choices, not product trade-offs) puts it on his side of the
line. **It stays open. [andres] rules.**

#### The two things that actually needed closing, closed without this signature

**1 · The prerequisite is struck, and it needs no signature at all — because no signed decision
ever imposed it.** This is the stronger close and it was available all along:

- The words on the rows are *"prerequisite: **[andres] creates the GitHub mirror**"*, and they were
  attached to **T-804**, which `specs/003-demo-content/tasks.md` records as written `H` — the
  **theme**. D-036's own text says where that came from: *"T-804 was written `H` (theme), but demo
  pages live in the template, whose mirror exists — while the theme has **no GitHub repository at
  all**."* D-036's closing recommendation is **unsigned**, and a recommendation is not a ruling —
  which is this record's own lesson.
- **Measured 2026-09-12, by command rather than by memory.** `git remote -v` in this working copy:
  `drupalcode` **and** `github` → `https://github.com/andresmoreno28/agora_transparency.git`. In the
  theme's sibling checkout: `drupalcode` **only**. **So the mirror the work needs already exists,
  and the one that does not exist is for the repository the work does not live in.**
- **Therefore the prerequisite was never a requirement of anything signed.** It was a task-level
  assignment error — the right work pointed at the wrong repository — and striking it costs no
  signature, no ruling and none of [andres]'s time. The carried-debt table and T-1202's row already
  strike it as of 2026-09-12; **this paragraph is the basis they should be read against**, in place
  of D-045.

**2 · `tasks.md`'s claim that D-045 is signed is still false, and it is now false in a second way.**
`specs/003-demo-content/tasks.md` line 79 reads *"The GitHub-mirror risk is **ABOLISHED by D-045,
signed**"*. D-045 carries `**Ruling: B.**` and **no `SIGNED by` line** — verified again today, and
this amendment does not add one. ⚠️ **And the sentence would have been misleading even if I had
signed**: a reader meeting *"D-045, signed"* in a risk register reasonably infers [andres] signed
it, since that is what every neighbouring signature means. Under the delegation it would have been
**[ejecutor]**, and a human's blocker released by a machine's signature is precisely the sentence
this project should never let pass unlabelled. That row is dated and append-only; it is **not**
edited, and the correction is recorded here and in that file's own appended state section.

⚠️ **What this amendment does NOT do, named so nobody infers it.** It does **not** re-impose the
prerequisite — item 1 removes it on a basis that does not need D-045. It does **not** dispute
D-045's measurement: the drupalcode runner does drive a real headless Chrome, and that is worth
keeping. It does **not** move T-1202's glyph, which stays `○`: **no `playwright.config.*` exists in
either repository** (`git ls-files` finds none in `agora_transparency` and none in `agora_theme`),
and removing a prerequisite has never made a row done. And it does **not** touch D-009, D-016 or
D-020, all of which stand — including C(2), which remains the signed answer to *where visual
regression runs* until [andres] rules otherwise.

**🔴 HOLD for [andres], one question, options attached:** visual regression currently has a signed
home — D-009(2), GitHub Actions, non-blocking — and an unsigned proposal to move it to drupalcode
and make it a tenth-or-later blocking gate. **(A)** Leave C(2) as signed and build T-1202 on the
template's existing mirror, which needs nothing from him. **(B)** Rule D-045 = B and move it to
drupalcode as a hand-written job, one gate in one place a reviewer can re-run, at the cost of
writing and maintaining a job upstream does not ship. **★ A is available today and costs him
nothing; B is better if a single gate matters more than the job being ours to maintain.** Either
way the prerequisite is gone.


---

### D-046 · The footer's social row: brand marks in the theme, root URLs in the template

**PREPARED by [ejecutor] 2026-09-04 - UNSIGNED. [andres] signs; nothing below is a ruling until
he does.** Amended the same day, still unsigned, after the orquestador's read-only audit found two
of the "what was measured" bullets false on disk; each corrected bullet says what it said before
and what is true instead, because a record that silently improves reads as though it was right.
 The template lane (T-1216, T-1217) and the theme lane were implemented against option A
under the dispatch's standing delegation, and both are reversible if he rules otherwise: A ships
six files here and one template plus icon paths there, and nothing else depends on either.

*Context in one line:* [andres] asked for a footer row that shows the official marks of the social
networks. Two questions are entangled in that request and are answered separately: **where the
marks come from** (a licence question, because the marks are trademarks) and **what the demo
links to** (a truthfulness question, because the demo municipality is fictional).

**What was measured before writing the options.**
- ⚠️ **Corrected 2026-09-04 by the orquestador's read-only audit; the first wording was false on
  disk.** It said the theme *already* rendered every footer menu through `menu--footer.html.twig`
  and that T-1215's four columns rendered through it. **They did not.** Twig reached that template
  by a suggestion derived from the menu's machine name, so only core's own `footer` menu landed
  there and the four grouped menus fell through to `menu.html.twig` - measured on the rig as
  **5 level-0 lists and 0 footer lists**, four nested navigations with level classes beside the
  one primary navigation those classes are counted to prove. It became true only because lane A
  shipped the rule **in this wave**, theme commit **`fddbe37`** (*"a menu block placed in the
  footer region renders through the footer template"*): the block preprocess now decides by the
  block's **region**, read from its own placement, never from a prefix or a machine name. A fifth
  menu therefore costs the template two config objects and four links, and costs the theme that
  region rule plus one recognition rule: link host to mark.
- **Simple Icons** publishes the marks as SVG path data under **CC0-1.0**; the marks themselves
  remain the networks' **trademarks**, which CC0 cannot and does not license (§4(a), quoted
  below). What a footer does with them is **nominative use** - naming the network a link goes to
  - which is the use every network's own brand guidelines describe as the permitted one. So the
  rendition is free and the use is the one the owners ask for; what would NOT be permitted is
  altering the marks or implying endorsement, and a footer link does neither.
- ⚠️ **Corrected 2026-09-04, same audit: `linkedin.com` is NOT a recognised host.** The shipped
  mapper, `_agora_theme_social_brand()` in `agora_theme.theme`, returns a brand for `facebook.com`,
  `x.com`, `twitter.com` (as `x`), `instagram.com`, `youtube.com` and `bsky.app`, and **NULL for
  everything else, LinkedIn included**. In the theme's own words (`README.md`, "Third-party
  assets"): *"There is no LinkedIn mark, and the absence is a licence fact rather than an
  omission. Simple Icons removed LinkedIn in release 14.0.0 (issue 11380), so no pinned release
  this repository could cite carries it under terms this repository could check. A footer link to
  `linkedin.com` renders as a text link in the same row."* The template header says the same:
  *"NO LINKEDIN, AND IT IS NOT MISSING BY ACCIDENT."* The demo ships **four** of the five marks
  the theme carries; Bluesky is recognised so a site owner who adds it gets a mark, and not
  shipped because four is a row.
- **The renditions, pinned and checked.** Simple Icons **16.29.0**, each `d` attribute the path
  from that release's `icons/<slug>.svg` byte for byte, fetched from
  `https://cdn.jsdelivr.net/npm/simple-icons@16.29.0/icons/` for `facebook`, `x`, `instagram`,
  `youtube` and `bluesky`. The release's `data/simple-icons.json` was read **at that version** and
  the per-icon `license` field is **absent for all five**, which under the project's `LICENSE.md`
  means each rendition is CC0-1.0 and none carries a licence of its own that would override it.
  The `<title>` and `role` the upstream files carry are dropped because the link's own text, kept
  visually hidden inside the mark, is the accessible name.
- **What CC0 does and does not do, in its own words.** CC0-1.0 §4(a): *"No trademark or patent
  rights held by Affirmer are waived, abandoned, surrendered, licensed or otherwise affected by
  this document."* So the rendition is free and the marks stay trademarks of their owners; the
  footer uses them solely to identify the destination a link leads to, and a site that removes its
  social links removes the marks with them.
- **A fictional municipality owns no account.** An invented handle such as `/fuentelclaro` is a
  link to whoever registers that name, on every network, forever. The only href that is true
  today and stays true is the network's **root URL**, which a site owner replaces in the menu UI.

**Options.**

- **A (recommended) - theme renders host to mark from Simple Icons paths; template ships the
  `Follow us` menu with root-URL links.** The marks are inlined SVG in the theme (CC0-1.0
  renditions, trademarks used nominatively, attribution in the theme's licence notes), each link
  keeps its visible name as the accessible name, and the template's demo shows the row working
  with links that lie to nobody. With a theme release that does not recognise a host, the links
  render as text.
- **B - theme marks only, no menu in the template.** The mechanism ships and the demo never shows
  it. A reviewer installing the template sees no social row, and a site owner has to discover
  that a menu named in no documentation would grow marks.
- **C - text links, no marks.** No trademark question at all, and a footer row that reads as four
  words where every public-sector footer a reader has seen shows four marks. Set aside on the
  human's own request, which was the marks.

**Recommendation: A.** The licence question is answered by the pairing CC0 rendition plus
nominative use, and the truthfulness question is answered by root URLs. Both halves are already on
disk: the template half is T-1216 and T-1217 here, the theme half is the theme lane's.

**What A does NOT do, named so nobody infers it:** no brand colour is applied as the only carrier
of meaning - the name is always present for assistive technology; no account is invented; nothing
is fetched from any network at render time, since the marks are static paths in the theme, so the
footer makes no third-party request and the template's privacy posture is unchanged.

#### D-046 · SIGNED = A by [ejecutor] 2026-09-12 under [andres]'s standing delegation

**SIGNED = A.** The header above reads `PREPARED … UNSIGNED. [andres] signs`; that is superseded
here and not edited (rule 8). The authority is his standing delegation, given 2026-09-12:
<!-- cspell:disable -->*"Firma tú lo que haya siempre que esté todo correcto y continua"*<!-- cspell:enable -->
("sign whatever there is yourself, so long as everything is correct, and continue" — translated,
per rule 6) — the delegation D-027 through D-030 and D-053 were signed on, with the same boundary:
**methodology and licence-constrained choices are [ejecutor]'s; product trade-offs are not.**
D-046 is a licence-constrained choice in both of its halves — CC0-1.0 renditions of marks that
remain their owners' trademarks, used nominatively, and root URLs because a fictional council owns
no account — and the aesthetic half of the request was [andres]'s own: he asked for the marks.

⚠️ **RE-CHECKED BEFORE SIGNING, AND THE REASON IS THE RECORD'S OWN HISTORY: this record was
corrected once, on 2026-09-04, after an audit found two of its measured bullets FALSE on disk. A
record corrected once is not a record verified.** Both corrected bullets, and the three that were
never challenged, were re-measured on 2026-09-12:

| bullet | re-measured 2026-09-12 | verdict |
|---|---|---|
| **corrected #1** — the footer template is reached by **region**, not by a menu's machine name | `agora_theme.theme` calls `_agora_theme_block_region($variables) === 'footer'` at **two** sites, and its own comment reads *"a menu reaches the footer template because of the REGION its block is placed in"*. Theme commit **`fddbe37`** is present in the theme's history, with the message D-046 quotes | **holds** |
| **corrected #2** — `linkedin.com` is **NOT** a recognised host | `_agora_theme_social_brand()` maps exactly six: `facebook.com`→`facebook`, `x.com`→`x`, `twitter.com`→`x`, `instagram.com`→`instagram`, `youtube.com`→`youtube`, `bsky.app`→`bluesky`, then `return $brands[$registrable] ?? NULL`. **No LinkedIn**, and the fall-through is NULL | **holds** |
| Simple Icons **16.29.0**, CC0-1.0, five slugs | the version string appears in `agora_theme.theme`, `README.md` (four places) and `templates/menu--footer.html.twig`, each naming the same pinned `cdn.jsdelivr.net/npm/simple-icons@16.29.0/icons/` source and the same five slugs. The template's own header carries *"NO LINKEDIN, AND IT IS NOT MISSING BY ACCIDENT"* | **holds** |
| the template ships **four** of the five marks, on **root URLs** | `content/menu_link_content/*.yml` — exactly four social `uri` values, and every one is a root: `https://www.facebook.com/`, `https://www.instagram.com/`, `https://www.youtube.com/`, `https://x.com/`. **No invented handle anywhere** | **holds** |
| the template half is **two config objects** | `config/system.menu.agora-base-footer-social.yml` and `config/block.block.agora_base_footer_social.yml` — two, exactly as A priced | **holds** |

**Nothing has gone false since 2026-09-04, and the two corrected bullets are the two that were
measured hardest.** The one figure that has moved is not in this record: the theme's comment now
speaks of *"All six footer menus"* where D-046 reasoned about a fifth, because wave 13's legal
bottom bar (T-1311, T-1312) added another. **That does not touch this ruling** — the region rule
D-046 signs is precisely what made a sixth menu cost nothing, which is the property it was chosen
for.

**So the signature ratifies what is already on disk**, rather than authorising work: the template
lane (T-1216, T-1217) and the theme lane shipped against A under the dispatch's standing delegation
and are both `✓`. ⚠️ **No glyph moves and no row changes**: a decision catching up with its
implementation does not make anything newly done.

⚠️ **What signing A does NOT do, restated because these are the sentences that protect the licence
position.** It does not assert any right in the marks themselves — CC0-1.0 §4(a), quoted in full
above, waives no trademark and cannot. It does not permit altering a mark or implying endorsement.
It does not adopt LinkedIn by any other route: the absence is a **licence fact**, Simple Icons
having removed the mark in release 14.0.0, and a `linkedin.com` link renders as a text link in the
same row. And it fetches nothing at render time — the paths are static in the theme, so the footer
makes no third-party request and the template's privacy posture is unchanged.


---

### D-047 · The currency unit: shown, euro in the demo, configurable per installation

**SIGNED = E by [andres], 2026-09-05.** The ruling is his own direction, in his own words:
<!-- cspell:disable -->*"habría que hacer que se pueda adaptar a cualquier entorno, que se pueda
configurar la moneda. Para la demo se puede poner euros como ejemplo, pero debe ser
configurable."*<!-- cspell:enable --> ("it should be able to adapt to any environment — the
currency should be configurable. For the demo, euros can go in as an example, but it must be
configurable." — translated, per rule 6.) **Both halves shipped**: the register half at
`agora_transparency` commit `da42f60`, the theme half at `agora_theme` commit `de0fc18`.

✅ **THE ACCEPTANCE CRITERION WAS MET, AND IT IS RECORDED HERE AS EVIDENCE RATHER THAN AS A
CLAIM.** This record demanded a *rendered comparison on the rig* — the front page and a register
table printing **the same string for the same number** — precisely because a commit cannot span two
repositories and two green pipelines would each have seen only their own side. The comparison:

> **`€11,900.00`, printed by both paths for the same money** — on the front page by the theme, out
> of a `SUM()` over an entity aggregate query, and on `/contracts` by Views reading the field
> instance.

That is the whole of what E promised. One number, two entirely different code paths, one string.
Move one half without the other and those two surfaces disagree about the same public money on the
same site, which is the failure this criterion was written to be able to see.

**Three further properties of the shipped mechanism, recorded because each answers a question the
options table could not.**

- **The unit is the one EVERY summed bundle agrees on.** The front-page figures are a sum *across
  bundles*, so there is no single field instance to read. A bundle without the field is skipped —
  it contributes nothing to the sum, so it has no unit to disagree with. **Instances that disagree
  yield no unit at all, plus a logged warning** naming the view, the display and each instance's
  setting. A total wearing one of two labels would be a wrong statement where a missing one was
  available.
- **Seven field instances carry `prefix: €`, and they are the seven money ones.** Verified on disk:
  `agreement.field_agora_base_amount`, `agreement.field_agora_base_obligations`,
  `contract.field_agora_base_amount`, `contract.field_agora_base_tender_amount`,
  `grant.field_agora_base_amount`, `person.field_agora_base_remuneration`,
  `person.field_agora_base_severance` — each `field_type: decimal`. A site owner in another
  jurisdiction edits those seven in the field UI and every register follows, with no code at all.
- ⚠️ **Places carrying the same keys that deliberately did NOT receive a unit, named because
  *"every field with a prefix key"* is exactly the bulk edit this change must not be.** On disk
  there are **two**: `contract.field_agora_base_bidder_count`, an `integer` labelled *"Number of
  bidders"* and the only field instance left with `prefix: ''`; and the `nid` field under
  `group_type: count` at `views.view.agora_base_publications:349`, labelled *"Records published"*,
  the only `prefix_suffix: true` in that view. **Neither is money.** `da42f60`'s commit message
  says *"three places"* and enumerates these two; the third is not identifiable on disk, and the
  discrepancy is recorded rather than reconciled away.

**Two consequences that are settled by this signature and should not be reopened by inference:**
the guard test at `tests/src/Unit/ThemeHelpersTest.php` is **rewritten and stronger, not relaxed**
— over the same nine amounts it now asserts the formatter emits no unit **of its own** and that a
configured one comes through exactly, so it fails both on a hard-coded symbol and on configuration
ignored; and **the six shipped declaration PDFs keep saying `EUR`**, because regenerating six
byte-reproduced binaries to harmonise punctuation would move G15's manifest for no gain. That
question stays open rather than being settled in passing, exactly as this record said it would.

**The original preparation note follows, unedited.**

**PREPARED by [ejecutor] 2026-09-05 — UNSIGNED. [andres] signs; nothing below is a ruling until
he does.** ⚠️ **The recommendation is [andres]'s own direction of 2026-09-04 and it is none of the
four options this record was drafted with.** The four are kept below unedited, because the fifth
is only legible beside them and because a record whose rejected options vanish reads as though the
answer was obvious.

*Context in one line:* the demo council is Spanish (**D-041 = A**, signed), the shipped interface
strings are English (**D-035 = C**, signed), and the same kind of number renders `592,470.00` on
the front page and `14,500.00` in the registers.

**[andres]'s direction, 2026-09-04:** <!-- cspell:disable -->*"quizá para resolver el tema de la
moneda de alguna forma habría que hacer que se pueda adaptar a cualquier entorno, que se pueda
configurar la moneda. Para la demo se puede poner euros como ejemplo, pero debe ser configurable.
De hecho me gustaría que fuese lo más configurable posible."*<!-- cspell:enable --> ("perhaps, to
settle the currency question, it should be able to adapt to any environment — the currency should
be configurable. For the demo, euros can go in as an example, but it must be configurable. In fact
I would like it to be as configurable as possible." — translated, per rule 6.)

**What was measured, and the first bullet falsifies the premise this record was drafted on.**

- 🔴 **"There is no unit anywhere on the site" is FALSE of the package.** It is true of the HTML:
  `grep -rc "€" config/ content/ recipe.yml` finds **zero**, and the theme's CSS, templates and
  `.theme` file carry none either. But **six of the 34 shipped PDFs already print a currency
  unit**, twice each — the asset declarations at `content/file/declaration-*.pdf`, reading
  `Annual remuneration for the post: 21300.00 EUR` and `Severance entitlement on leaving office:
  5325.00 EUR`. It is not incidental and it is not stale: `tests/bin/generate-demo-media.py:657`
  and `:659` write those two strings, and G15 reproduces all 39 media files byte for byte, so the
  choice is already made, already shipped and already under a gate.
- ⚠️ **So the package is not silent about currency; it is inconsistent about it.** For the same
  office-holder the same figure appears three ways in one install: the register table renders
  **`21,300.00`** (`views.view.agora_base_people`, `number_decimal`, `thousand_separator: ','`,
  `decimal_separator: .`, `scale: 2`), the shipped declaration PDF beside it reads
  **`21300.00 EUR`**, and the front page prints its own total with no unit at all. The three
  disagree on the thousands separator **and** on the presence of a unit **and** on the unit's
  style. That is a stronger reason to act than ambiguity, and it is the reason this is a decision
  rather than a preference.
- **The registers are already per-installation configurable and need no code at all.** Views'
  `number_decimal` formatter with `prefix_suffix: true` reads `prefix` and `suffix` from the
  **field-instance** configuration, which a site owner edits in the field UI.
  ⚠️ **There are SEVEN such money field instances, not four** — measured, not assumed:
  `field.field.node.agora_base_agreement.field_agora_base_amount`,
  `…agreement.field_agora_base_obligations`, `…contract.field_agora_base_amount`,
  `…contract.field_agora_base_tender_amount`, `…grant.field_agora_base_amount`,
  `…person.field_agora_base_remuneration` and `…person.field_agora_base_severance`. Each is
  `field_type: decimal` and each carries `prefix: ''` and `suffix: ''` today. They are honoured at
  **14 rendering sites**: 7 in four register views (`agreements` ×2, `contracts` ×2, `grants` ×1,
  `people` ×2) and 7 in four node record-sheet displays
  (`core.entity_view_display.node.agora_base_{agreement,contract,grant,person}.default`).
- ⚠️ **An eighth field instance and a fifteenth rendering site carry the same keys and must NOT
  receive a unit.** `…contract.field_agora_base_bidder_count` is `field_type: integer` — its label
  is *"Number of bidders"* — and `views.view.agora_base_publications:331` sets
  `prefix_suffix: true` on `nid` under `group_type: count`. Neither is money. The second is safe by
  construction, because `nid` is a base field with no prefix setting to read; the first is exactly
  the row that a bulk edit over *"every field instance with a prefix key"* would put a euro sign
  on. Naming it here is cheaper than finding it in a screenshot.
- **The two front-page figures have no field handler to read**, and the theme says so in its own
  words at `agora_theme.theme:463`: the blocks it serves *"have no such formatter to read, because
  since the SUM fields were removed they have no amount field at all; the number arrives from a
  query, not from a field handler. So the theme matches the configured shape by hand."*
- **The prohibition this decision has to satisfy is in the same comment**, in capitals:
  *"THERE IS NO CURRENCY SYMBOL AND THERE MUST NOT BE ONE … a symbol added here would answer it by
  accident and permanently."* It is enforced by `tests/src/Unit/ThemeHelpersTest.php:107`
  (`testNoCurrencySymbolEverAppears`), whose pattern is deliberately total — digits, commas, one
  point, two decimals, an optional leading minus and nothing else — over a stated denominator of
  **nine amounts**.

**Options.**

| | Option | Real cost |
|---|---|---|
| A | `€` prefix — `€592,470.00` | Reads naturally in English. Two formatters plus the field configs. **Fails the guard test by design**, so that test is rewritten rather than relaxed |
| B | ISO code — `EUR 592,470.00` | Unambiguous and jurisdiction-neutral; slightly bureaucratic. ⚠️ **It is also what the six shipped PDFs already do**, which the drafted options did not know |
| C | Name the unit once in a column heading or tile label; figures stay bare | Zero repetition, and the codebase already does this — the axe fixture's caption at `agora-theme/tests/src/Nightwatch/Tests/axe.js:531` reads *"Contract awards over 15,000 euro"*. The only option that leaves `_agora_theme_format_amount()` and its guard test untouched |
| D | Spanish convention — `592.470,00 €` | Contradicts D-035's English rendering and the separators used everywhere else in the package |
| **E ★** | **The unit is shown; it is euro in the shipped demo; it is configurable per installation, and no symbol is ever written into theme code** | [andres]'s direction. It **subsumes A** — the demo looks like A — while answering the objection that made A hard. Costs the seven field instances one non-empty `prefix` each, and costs the theme a read of that configuration instead of a literal |

**Recommendation: E**, because it is his direction and because it is the only option that shows a
unit without deciding, for every installation of this template, which unit that is.

**The mechanism, stated concretely, because "configurable" with no named mechanism is how a
decision becomes unimplementable.**

1. **The registers need no code.** Set `prefix: '€'` (with its trailing space handled the way the
   formatter handles it) on the **seven money field instances** listed above. Every one of the 14
   rendering sites already has `prefix_suffix: true` and starts honouring it in the same change.
   A site owner in another jurisdiction edits those seven fields in the UI and the whole register
   set follows. **`bidder_count` is not touched.**
2. **The theme reads the same configuration rather than carrying a symbol of its own.**
   `_agora_theme_format_amount()` gains the unit from the field configuration the registers
   already use — one source of truth, edited in one place, by whoever installs the template. It
   does not gain a constant, a Twig literal or a hard-coded `€`.
3. **One source of truth, and it is checkable.** If the front page and a register table can ever
   print different units for the same currency, the mechanism is wrong, and the acceptance
   criterion below is written to catch exactly that.

**Why this is the right answer and not a compromise, in one sentence that is worth keeping:**
`agora_theme.theme:463` objects to a symbol *written into the formatter*, because that "would
answer it by accident and permanently" — and **the objection was never to showing a currency, it
was to fixing it in code for every installation**. Reading it from configuration answers the
product question without answering it permanently, which is precisely what that comment asked for.

**The guard test is rewritten, not relaxed, and what it asserts afterwards is stronger.** Today it
asserts that the formatter's output contains no unit. Afterwards it asserts that the formatter
emits **no currency symbol of its own** — that whatever unit appears came from configuration and
that with no configured unit the output is still bare digits, separators and two decimals. That is
a property about the *source* of the symbol rather than about its absence, and it is the property
D-047 actually depends on. ⚠️ **Its nine-amount denominator is kept**, so the rewritten test
cannot pass by checking fewer things than the old one.

⚠️ **The two-repository constraint, stated because it changes what "done" means.** The registers'
half lives in `agora_transparency` (seven field-instance config objects) and the theme's half
lives in `agora_theme` (`_agora_theme_format_amount()` and its test). **A commit cannot span two
repositories**, and two green pipelines would not prove the two halves agree — each pipeline only
ever sees its own side. So the acceptance criterion is **a rendered comparison on the rig**: the
front page and a register table printing the **same string for the same number**, quoted. Move one
half without the other and the front page and the register tables disagree about the same money.

**His general steer, recorded as direction and not as a ruling:**
<!-- cspell:disable -->*"me gustaría que fuese lo más configurable posible"*<!-- cspell:enable -->
("I would like it to be as configurable as possible"). It is not a decision and nothing is gated on
it, but it settles close calls: **where this wave can honour it cheaply it should — a value a site
owner would plausibly want to change belongs in configuration, not in a Twig template or a PHP
constant.** Written here rather than as a decision of its own because it is a preference about how
to choose, not a choice.

**What E does NOT do, named so nobody infers it:** it does not add a currency module or any
dependency; it does not introduce currency *conversion*, multi-currency display, or locale-aware
number formatting — the separators stay exactly as D-035 leaves them; and it does not change the
six shipped declaration PDFs, whose `EUR` wording is a separate question this record deliberately
leaves open rather than settling in passing.


---

### D-048 · The hero mark as a watermark, and the invariant it would silence

**SIGNED = A by [andres], 2026-09-05.** He was asked plainly — watermark with the colour measured,
or leave the mark as it is — and answered <!-- cspell:disable -->*"Sí, con el color
medido"*<!-- cspell:enable --> ("yes, with the measured colour" — translated, per rule 6). So the
watermark **is** wanted, and it arrives by option A rather than by `opacity`.

**What A obliges, in one paragraph, because the obligation IS the decision.** The colour the mark
resolves to once it is drawn at the intended strength over the band is **computed and declared as
its own token, with its own `@pair` at the non-text threshold**, and the mark is set to that flat
value. **`opacity` appears in no rule.** The measurement is taken over the band's photograph, per
pixel, the same way the two existing values were — the band is not a flat colour, and a ratio taken
against the flat surface would be the wrong number for exactly the reason it was the wrong number
last time (`css/tokens.css:194-203`: the toned photograph pushed the measured bars to **1.40:1**
against a token that clears the threshold by four hundredths on paper).

🔴 **The reason for A over `opacity` is the whole point of the option, and it is recorded here so it
survives whoever next thinks `opacity: 0.15` is the obvious one-line answer.** `tests/bin/
contrast-check` computes WCAG relative luminance from the **hex token literals** it parses out of
`css/tokens.css`. It has no renderer and no notion of a composite. Apply CSS `opacity` to the mark
and the script stays **green over two declarations that have stopped describing what is painted** —
`--agora-color-mark` and `--agora-color-text-inverse-muted`, both of which were chosen by per-pixel
measurement over that photograph. That is not a red turning green; it is a **check quietly ceasing
to be about anything**, arriving through a mechanism nobody edited. A red is a fact and can be
acted on. This would be a green nobody could act on.

⚠️ **And the honest note from the record below stands, unweakened by the signature:** the hero mark
is `aria-hidden="true"` with no `<title>`, so WCAG requires none of this. What `opacity` would
break is **this project's own declaration** that every colour combination it renders has been
checked — which, in a template whose pitch is auditability, is the worse of the two failures.

**The original preparation note follows, unedited.**

**PREPARED by [ejecutor] 2026-09-05 — UNSIGNED. [andres] signs; nothing below is a ruling until
he does.** He floated it as a possibility rather than a request — <!-- cspell:disable -->*"lo del
logo grande no sé... quizá se podría mirar de meterlo con poca opacidad en plan marca de agua? Es
una posibilidad solamente."*<!-- cspell:enable --> ("about the big logo, I don't know… maybe it
could be looked at, putting it in with low opacity like a watermark? It is only a possibility." —
translated, per rule 6.) **T-1304** is written and blocked on this record.

**Lead with the hazard, because it is the whole value of this record: CSS `opacity` is invisible
to `tests/bin/contrast-check`.** That script computes WCAG relative luminance from the **hex token
literals** it parses out of `css/tokens.css`; it has no renderer and no notion of a composite. Two
of those literals were chosen by **per-pixel measurement over the band's photograph**, and the
file says so at length:

- **`--agora-color-mark` on `--agora-color-surface-inverse` at 3:1**, declared at
  `agora-theme/css/tokens.css:185` — *"the portico on the hero band"*.
- **The record bars are drawn in `--agora-color-text-inverse-muted`**, whose pair is declared at
  `css/tokens.css:155` against the same inverse surface at 4.5. ⚠️ **Its predecessor,
  `--agora-color-mark-muted`, is the pair at `:208`, and that pair is deliberately kept although
  nothing renders it.** The file's own account, at `:194-203`: it *"clears the threshold by four
  hundredths"* at 3.19, the toned photograph behind the mark pushed the measured bars to
  **1.40:1**, and *"a scan of every position and three sizes of the mark inside the band found
  nowhere they clear it."* The replacement tolerates 0.1792 against a measured worst ground of
  0.1275.

**So the failure mode is precise, and nobody would have edited anything to cause it.** Apply
`opacity` to the mark and `contrast-check` stays **green over declarations that have stopped
describing what is painted** — the two literals it checks would no longer be the colours on the
screen. That is a silenced invariant arriving through a mechanism nobody touched, which is worse
than a red: a red is a fact, and this would be a green that is no longer about anything.

**Options.**

- **A ★ — compute the resulting flat colour and declare it as its own token, with its own
  `@pair`.** Work out what the mark's colour becomes once it is drawn at the intended opacity over
  the band, declare that value as a new token, give it a `@pair` at the non-text threshold, and set
  the mark to the flat value with **no `opacity` in the rule**. `contrast-check` then checks what
  is actually painted, which is the property it was written to have. Cost: one token, one pair, and
  the same per-pixel measurement over the photograph that produced the two existing values —
  because the band is not a flat colour and a ratio taken against the flat surface would be the
  wrong number for the same reason it was the wrong number last time.
- **B — apply `opacity` and delete the two pairs.** It fails **assertion 5** of `contrast-check`
  (*"every token is named by at least one `@pair` — an unpaired colour is a colour whose contrast
  nobody checked"*), and it is the wrong direction even if it could be made to pass: it removes
  the check rather than the risk.
- **C — leave the mark exactly as it is.** The stated goal — that the mark must not compete with
  the headline or the body text — is reachable by **position and size alone**, and the current
  values are already measured against the photograph. Zero cost, zero risk, and it is a real
  option rather than a placeholder.

**Recommendation: A if the watermark is wanted; C if it is not.** The choice between them is his,
because it is about how the band should look and nothing measurable separates them on correctness.

⚠️ **The honest note, and it cuts against this record's own seriousness:** the hero mark is
`aria-hidden="true"` with no `<title>` (`agora-theme/templates/agora-hero.html.twig:111`), so it is
decoration and **WCAG requires none of this**. What option B would break is not a conformance
obligation — it is **the project's own declaration** that every colour combination it renders is
checked. That is a different failure from a WCAG failure, and in a template whose pitch is
auditability it is arguably the worse of the two: an accessibility claim that quietly stops being
true is exactly what a marketplace reviewer is entitled to disbelieve everything else on the
strength of.


---

### D-049 · Canvas component sources: the eight Views blocks stay, and the gap is a presentation layer

**SIGNED by [andres], 2026-09-05**, on the ruling below and on its schedule:
<!-- cspell:disable -->*"Sí, al cerrar esta ronda"*<!-- cspell:enable --> ("yes, on closing this
round" — translated, per rule 6), answering whether to build the minimal layout kit and when.

*Context in one line:* every component instance Ágora places on a Canvas page is a **block**, both
published site templates place almost nothing but **SDC**, and the question that had never been
asked on evidence is whether that difference is a defect, a style, or two different things being
confused for one.

**It is two different things, and separating them is the whole value of this record.** The blocks
are **correct** and stay. What is missing is a **presentation layer**, and that is a real gap with
a real cost, measured below.

**What was measured. Every figure was re-derived on 2026-09-05 from the packages themselves, not
quoted from a summary.**

- **Canvas 1.10.1 supports exactly three component sources: `sdc`, `block`, `js`** — its own
  `docs/components.md`, sections 3.1, 3.2 and 3.3, read from the module installed on the rig.
  🔴 **And on the question that decides this record, the documentation is unambiguous and says the
  opposite of the folklore:**

  | source | implicit inputs | `docs/components.md` |
  |---|---|---|
  | `SDC` | **NO** | *"`SDC` `component`s DO NOT accept implicit inputs."* (§3.1.1) |
  | `Block` | **YES** | *"`Block` `component`s DO accept implicit inputs, in two ways even: 1. Logic in the block plugin can fetch data — through database queries, HTTP requests, anything."* (§3.2.1) |
  | `JS` | **NO** | *"`JS` `component`s DO NOT accept implicit inputs."* (§3.3.1) |

  **The block plugin is the only source that can run a query.** That is the load-bearing fact of
  this decision, and it is the reverse of *"block is the legacy path"*.

- **The published site templates agree, and their block instances are where their queries are.**
  Counted by walking every `content/canvas_page/*.yml` in each package at tag `1.0.3`:

  | | pages | instances | `sdc` | `block` |
  |---|---:|---:|---:|---:|
  | `haven` 1.0.3 | 7 | 145 | 141 | **4** |
  | `byte` 1.0.3 | 8 | 149 | 145 | **4** |
  | **Ágora** | 2 | 8 | **0** | **8** |

  ⚠️ **What those 4 + 4 block instances are, corrected against the claim this record was drafted
  with.** The draft said *"every one of them is a Views block"*. That is true of `haven` — all four
  are `views_block` (`blog-latest`, `blog-all`, `projects-all`, `projects-featured`). It is **false
  of `byte`**, whose four are `views_block.blog-latest`, `views_block.blog-all`,
  **`system_menu_block.social`** and **`webform_block`**. So **six of the eight** are Views blocks.
  ⚠️ **The argument survives the correction and is arguably strengthened by it**: a menu block
  builds its links from the menu tree and a webform block renders a form — both are *"logic in the
  block plugin fetching data"*, which is §3.2.1's sentence exactly. **All eight block instances
  across both published templates are query- or service-backed; not one is static markup.** The
  claim to carry forward is that shape, not the word *"Views"*.

- **The criteria are silent, and the silence is deliberate rather than accidental.** The RFC *"The
  architecture and philosophy of site templates"* declares that it uses RFC 2119 keywords, and it
  is full of MUSTs — **14 of them** across the document. Its section on this exact question,
  *"Site templates care about looks"*, contains **5 MAY, 0 MUST and 0 SHOULD**: they *MAY*
  integrate heavily with Canvas, they *MAY* depend on a theme as a design system, they *MAY* ship
  custom components, they *MAY* ship content templates, and *"In short, site templates MAY use any
  tool, framework, design system, theme, or module they wish in order to define their look."*
  ⚠️ **The count is 5, not 4** — four bullets plus the summary sentence; the bullet count and the
  keyword count are different numbers, and this record uses the keyword one.
  And the starter kit's `GET-STARTED.md` lists **seven** ironclad rules — `type: Site`, composer
  `type: drupal-recipe`, no patching, no dependence on an install profile, no `drupal_cms_` name
  prefix, no pinned versions, legal right to all content — of which **none** mentions Canvas,
  components or page composition.

**Ruling, part one: the eight Views blocks stay, permanently and on the record.** They are the only
source that can carry a query, and **every number on that front page is a real number with a real
query behind it**. Rewriting them as SDC would mean either inventing static values for figures this
portal exists to publish truthfully, or reaching for `js` — which §3.3.1 rules out for the same
reason. This is not a concession to be revisited when somebody notices `0 sdc` next to `141 sdc`;
it is the correct use of the mechanism.

**Ruling, part two: option B, a minimal layout kit, scheduled as wave 14.** The gap the block/SDC
count is really pointing at is not the blocks — it is that a site owner can **rearrange** eight
things and **cannot add a heading or a paragraph**.

- `haven_theme` **1.0.1** exposes **25** SDC components; `byte_theme` **1.0.3** exposes **24**
  (counted as directories under `components/` at each released tag). ⚠️ **24, not 25** — the pair
  is not symmetric, and the draft rounded them together.
- **Ágora exposes 0**, and `recipe.yml` **disables the only two SDC components in reach** —
  `canvas.component.sdc.navigation.title` and `canvas.component.sdc.navigation.message`, both
  administrative toolbar chrome from core's `navigation` module, and both disabled for good
  reasons this record does not disturb.
- **The costing number, and it is the reason B is affordable at all: four generic components are
  the majority of everything both published templates place.** `section`, `group`, `heading` and
  `text` account for **83 of haven's 145 instances (57.2%)** and **83 of byte's 149 (55.7%)** —
  the same 83 twice, by coincidence. **Four components, not twenty-five**, buy more than half of
  what a published site template's page composition actually consists of.

**Option D is refused on the record rather than left unconsidered, because it is the obvious
shortcut and it needs a reason, not a silence.** Regenerating the theme from `mercury` — the
starter kit both published themes were generated from, named in their own `.info.yml`
(`generator: "mercury:1.0.0-rc1"` for haven, `"mercury:1.0.0-beta1"` for byte) — would supply all
25 SDC components free and give exact parity with the marketplace's existing pair. It is refused
because of what it costs, and the costs are specific: `mercury` and both derived themes build their
CSS with **Tailwind v4** (`"tailwindcss": "^4.1.18"`), a build system and an authoring model this
theme does not use; and regenerating would discard the **per-pixel measured contrast tokens**,
`tests/bin/contrast-check` that checks them, the **six-page axe gate**, and the visual identity
those were built to serve.

⚠️ **One line of the draft reasoning was found FALSE on disk and is corrected rather than repeated:
Tailwind is NOT forbidden by D-003 "in as many words".** The string `tailwind` appears **nowhere**
in `specs/` or in `CLAUDE.md`; D-003 names Composer, pnpm and DDEV and stops there. **The refusal
therefore rests on the four named costs and on nothing else** — and that is a sound basis, whereas
citing a rule that does not exist would have been the exact defect D-045 recorded: a precondition
quoted as though it had been checked.

⚠️ **`mercury` itself is NOT an SBOM objection today**, and saying so keeps the refusal honest: its
current release is **1.0.5**, stable. The two published themes were generated from `1.0.0-rc1` and
`1.0.0-beta1`, which were pre-stable **at that time**; that is a fact about their history, not a
live argument against the project.

🔴 **The trap that comes with this territory, recorded here because a reader arrives at it from
this decision and from nowhere else: enabling Canvas's page regions silently destroys the theme's
footer design.** Canvas registers a hook on the theme settings form offering a `use_canvas`
checkbox plus per-region *"editable"* checkboxes. Run on the rig, it generates **2 regions** from
the theme's existing block placements — header with 5 components, footer with 9 — and the front
page still returns **200**, with its 8 `<nav>` landmarks and its four legal links intact. **But the
theme's own footer classes fall from 54 occurrences to 0**, and the page shrinks from **55,431 to
51,346 bytes**: the statutory bar, the column layout and the social row all lose their design.

**The cause is exact, and it is the part worth keeping.** Theme commit `fddbe37` — *"a menu block
placed in the footer region renders through the footer template"* — made the footer work by
deciding on **the region a block is placed in**, read from its own placement
(`_agora_theme_block_region($variables) === 'footer'`, `agora_theme.theme:366` and `:391`). **A
Canvas page region is precisely what replaces block placements.** So the mechanism that makes the
footer correct is the mechanism Canvas's page regions remove. **A site owner can tick that checkbox
and lose the design, with no error of any kind.** The rig was restored and the count went back to
54. Recorded as **I-112** as well, because the next person to meet it will be reading the theme,
not this file.

**What this decision does NOT do, named so nobody infers it:** it does not add a dependency; it
does not make Ágora's front page an SDC page — the eight blocks are staying; it does not commit to
25 components, or to 5, or to any number beyond wave 14's four; and it does not reopen D-014,
D-003, or the disabling of the two `navigation` SDC components in `recipe.yml`.


---

### D-050 · Versioning, and the branch flag a minor tag sets by itself

**SIGNED by [andres], 2026-09-06**, in one line — <!-- cspell:disable -->*"firma D-050 y continua"*<!-- cspell:enable -->
("sign D-050 and continue" — translated, per rule 6) — after a read-only audit and a conversation
in which he reached the load-bearing conclusion of part 3 himself.

*Context in one line:* publishing `agora_theme` **1.1.0** put **two download rows** on the project
page, offering a choice between two branches that declare the **same** `core_compatibility: ^11` —
and nobody chose the second one.

**The mechanism, which is the part worth keeping.** There was no misconfigured setting to find, and
that is the whole finding. **Verified at source on 2026-09-06** in `project/project` at its default
branch `7.x-2.x`, read from git.drupalcode.org rather than inferred from the page:

- **A branch is computed from the version string and from nothing else.**
  `project_release_get_branch($version)` is one line — `preg_replace('#\.[^.]*$#', '.', $version)` —
  so `1.1.0` becomes `1.1.` and `1.0.7` becomes `1.0.`. It receives no project, no node and no
  repository: **the git branch name is never consulted, and cannot be.** Both our tags sit on `1.x`,
  and that fact is invisible to this function.
- **The `supported` column's schema default is `1`.** `release/project_release.install` declares
  `project_release_supported_versions.supported` as `'type' => 'int'`, `'size' => 'tiny'`,
  `'unsigned' => TRUE`, `'not null' => TRUE`, **`'default' => 1`**.
- **The save hook writes that column downwards only, and for one branch only.**
  `project_release_check_supported_versions()` builds `$fields` from three release node ids —
  `recommended_release`, `latest_release`, `latest_security_release` — adds `'supported' => 0` in
  exactly two exceptional cases (a branch failing the site's supportable-branch pattern, or a
  core-compatibility term that is not recommended), and then calls
  `db_merge('project_release_supported_versions')->key(['nid' => $pid, 'branch' => $branch])`.

⚠️ **Three consequences follow, and the third is the one the ruling actually needs.**

1. **The first release on a new branch INSERTS the row, so `supported` takes the schema default
   `1`.** No code path chose it. The second download row appeared by itself — which is why hunting
   for the setting that caused it was time spent on a thing that does not exist.
2. The merge key is `(nid, branch)`, so **no other branch's row is read or written**. `1.0.` kept
   whatever it already had while `1.1.` arrived beside it.
3. **`supported` is absent from `$fields` on the ordinary path, so an UPDATE never restores it.**
   The code can lower the flag to `0` and has no path that raises it to `1`. **Unchecking a branch
   is therefore durable:** a later release on it refreshes that row's three release ids and leaves
   the flag alone. That is not academic here — two tags on `1.0.` carry no published release
   (measured below), and this is what says publishing one of them would not undo today's work.

⚠️ **And the second row's own install command did not do what the row said.** Under the 1.0.7
heading the page printed `composer require 'drupal/agora_theme:^1.0'`. Executed during the audit,
that constraint resolves to `Locking drupal/agora_theme (1.1.0)` — and it is not an accident of the
moment: `^1.0` means `>=1.0.0 <2.0.0`, so it can only ever prefer the newest 1.x, which lives on the
other branch. Somebody deliberately choosing the older line got the newer release. **The choice was
not merely unnecessary; it was not a choice.**

**What was measured, and it is the heart of the record. Re-derived on 2026-09-06 from
`updates.drupal.org/release-history/<project>/all`, not carried from the draft.**

| | pre-releases before stable 1.0.0 | released tags | span, first tag → last | cadence |
|---|---|---|---|---|
| `byte` | **11** — 6 alpha, 3 beta, 2 rc — over **102 days** | 15 | 287 days | one per **19.1** days |
| `haven` | **3 betas** over **10 days** | 7 | 161 days | one per **23.0** days |
| `agora_theme` | **0 — stable on day one** | 8 | **11 days** | **one per 1.4 days** |

⚠️ **The draft's figures for the other two were not like-for-like, and the reason will recur.** It
read `byte` at 16 releases over 318 days and `haven` at 8 over 176. Both counts include the
project's **`1.x-dev` nightly snapshot** as though it were a release, and both spans **end on that
snapshot's date** — rebuilt continuously, and read **2026-08-31 for both projects**, which is the
tell that it is a clock rather than an event. `agora_theme` publishes **no** `1.x-dev` at all, so
the comparison was counting a row for two projects and not for the third. Recomputed on released
tags only, the cadence gap is **13.5x** against `byte` and **16.3x** against `haven`.

**Both published site templates spent a pre-release phase in public before promising anything. This
project skipped it entirely and then released between thirteen and sixteen times faster than
either.**

⚠️ **`agora_theme` has TEN git tags and EIGHT published releases.** `1.0.4` (tagged 2026-08-27) and
`1.0.8` (tagged 2026-09-04) carry no release on drupal.org. So *"the seven 1.0.x releases"* below is
right even though the numbering runs to `1.0.7`, and a reader counting `1.0.0` through `1.0.7` would
say eight and be wrong. The tagging rate is faster still than the table's: ten tags in the same
11 days.

**Why it happened, and this is the sentence the record exists for. Not by oversight. By a rule of
ours.** **D-025** (2026-08-24) chose option B — *"the theme cuts a stable `1.0.0` before the
template ever names it"* — and dismissed option A in four words: *"Violates non-negotiable rule 1."*
Rule 1 forbids a **dev/alpha/beta/rc dependency**, so the pre-release phase `byte` and `haven` both
used was vetoed by the same clause. **D-025's option table never lists a beta.** It was not weighed
and rejected; it was outside the space of options.

⚠️ **And rule 1's stated provenance was found false on 2026-09-05** and amended in `CLAUDE.md` in
place: it claimed to be a *"literal marketplace requirement"*, and `haven` 1.0.3 publishes
`"drupal/webform": "^6.3.0-beta8"` in its own `require`. **So the rule that forced a premature
stable release is our own stricter choice, not the marketplace's** — a cost nobody priced when it
was written. **The rule is not being weakened here** (being stricter than the ecosystem stays
deliberate, and CLAUDE.md's amendment says why), but its consequences now sit on the record beside
it.

#### The ruling — three parts

**1. Semantic versioning is the rule for both packages**, and the divergence from `haven` and `byte`
is deliberate rather than accidental. It is recorded with the measurement that makes it a choice:

- **`byte` shipped 630 changed files and a deleted test class under a patch number.** Its
  `1.0.2 → 1.0.3` compare, read from the drupalcode API with `compare_timeout: false` so it is not
  truncated, is 6 commits over **630** files, one of them deleting
  `tests/src/Functional/SiteTemplateTest.php`. ⚠️ **630, not the 634 the draft carried.**
- **`haven` ships a `feat:` commit under a patch number every single time — three patch releases,
  three `feat:` commits**, one each in `1.0.0 → 1.0.1`, `1.0.1 → 1.0.2` and `1.0.2 → 1.0.3`. It is
  the pattern, not an exception inside it.

That is the norm this project is judged beside; we diverge knowingly. **The minor digit also earns
its keep:** `composer.json`'s `"drupal/agora_theme": "^1.1"` is how the template says *"the release
with the settings object"*, and `recipe.yml` spells out why it must — its `agora_theme.settings`
config action is a hard `drush recipe` failure on any site that resolved 1.0.7, because
`SimpleConfigUpdate::apply()` throws on an absent config object and `config.actions` has no `?`
optionality. Under patch-only versioning that constraint would have to be a three-component one,
which rule 1 makes awkward.

**2. Unchecking the superseded branch is part of the release procedure, not a tidy-up.** A new
minor's first tag creates a second supported branch **by itself**, so the previous one is unchecked
in the same sitting.

- The convention is Drupal.org's own, quoted verbatim from *"Managing unsupported branches /
  releases"*: *"Releases should ideally be moved from supported to unsupported on Wednesdays to give
  site admins time to react during the work week."* ⚠️ It is **not** in the release-creation
  document, which is where somebody working through a release checklist would look for it. That is
  half the reason it is written down here.
- ⚠️ **Done for this case — and the first application already broke the convention it adopts:
  2026-09-06 was a Sunday.** [andres] unchecked `1.0.` that day rather than waiting until the 9th,
  because the row had already been live for a day offering an install command that resolved
  somewhere else, and leaving it was the worse of the two. **The Wednesday rule binds from the next
  minor onwards, when it will be foreseeable instead of discovered.**
- **Verified in the update feed rather than on the page:** `agora_theme`'s release history now
  carries `<supported_branches>1.1.</supported_branches>` — one branch — and the seven 1.0.x
  releases all remain `<status>published</status>` and downloadable at their own `ftp.drupal.org`
  URLs.

**3. `agora_transparency` ships PRE-RELEASES until it is ready for the marketplace.** Its release
history answers *"No release history was found"* today, so this choice is entirely open and costs
nothing to make. It is [andres]'s own conclusion from the measurement above:
<!-- cspell:disable -->*"seguramente empezaron como alpha o prealpha dev o inferior a la 1 para
salir con la versión 1 estable cuando estuviese preparado para el marketplace."*<!-- cspell:enable -->
("they most likely started as alpha, or pre-alpha dev, or below 1, so as to come out with a stable
version 1 when it was ready for the marketplace" — translated, per rule 6).

⚠️ **The unlock is one distinction, and missing it is what cost the theme its pre-release phase:
rule 1 governs what this project DEPENDS ON, not what it PUBLISHES.** Publishing `1.0.0-alpha1` of
our own package never violated it, and no invariant in `tests/bin/` looks at our own version string.
It was never forbidden — it was never considered.

**Also ruled: the theme's cadence slows.** A release happens when a site owner would gain something,
not when our own development needs an artefact published. Today's 1.1.0 had a real reason — the
recipe's config action needs `agora_theme.settings` to exist in a **published** release, which is
the coupling `recipe.yml` documents at length — and that class of coupling is the exception that
must be argued for each time, not the pattern.

#### Deleting 1.0.7 — considered and rejected, with the reason

[andres] asked directly whether the old release could be deleted so that only 1.1.0 remained. **It
would not have worked, and it would have broken people.**

- **The download row is per branch, not per release.** Deleting 1.0.7 promotes 1.0.6 into the same
  row, because `recommended_release` is recomputed from whatever releases the branch still has. All
  seven would have to go.
- **Deletion breaks reproducible builds.** Anyone who installed one has its `ftp.drupal.org` URL in
  `composer.lock`; removing it breaks their `composer install`, their CI and their ability to
  rebuild an old site.
- **Drupal.org's own documents prescribe the checkbox and never deletion.** *"Managing unsupported
  branches / releases"* does not mention deleting a release at all. ⚠️ The release-creation document
  **does** carry a section headed *"Deleting a tag/branch"*, and that near-miss is worth recording:
  it is about **git refs**, and it disqualifies itself in its own first sentence — *"assuming you
  haven't created a release with the tag"*. The one place the word appears excludes exactly this
  case.

**The checkbox achieves the whole of the intent at none of the cost.**

#### One consequence still owed to users

**1.1.0 removed the photographs the theme used to ship** — `images/hero-wide.webp` and
`images/commitment-chamber-1000x750.webp`, both deleted in theme commit `5f8397b`, which turned the
masthead picture into the `hero_image_path` setting. **The site template is unaffected:** it ships
the demonstration image as `content/file/hero-wide.webp` and `recipe.yml` points the setting at it.
**A standalone site updating from 1.0.x loses the picture silently, with no in-package
replacement.** Under strict semantic versioning that argues for a major rather than a minor; it is
recorded here rather than re-versioned, because the release is published and rule 8 is not suspended
for tidiness.

**A sentence belongs in the 1.1.0 release notes on drupal.org, which is [andres]'s form (rule 10):**

> The theme no longer ships a masthead photograph. Sites updating from 1.0.x should upload their own
> at Appearance → Ágora Transparency Theme.

**What this decision does NOT do, named so nobody infers it:** it does not weaken non-negotiable
rule 1, and it does not reopen D-004 or D-025 — both stand, and part 3 is available **because**
rule 1 was always about dependencies rather than about us; it does not commit `agora_theme` to a
version number, a date or a next release; it does not delete, unpublish or renumber anything already
released; and it does not make the Wednesday convention retroactive — it binds the next minor, not
this one.

#### D-050 part 3 · Amended 2026-09-12 — when the template's first STABLE version comes, in [andres]'s own words

**Nothing in part 3 is edited** (rule 8). It is amended because [andres] added the half it left
open, on 2026-09-12, unprompted:

> <!-- cspell:disable -->*"La plantilla dijimos que la primera versión estable saldría al estar
> terminada al 100%, así que de momento sigamos."*<!-- cspell:enable -->

("we said the template's first stable version would come out once it was finished 100%, so for now
let's carry on" — translated, per rule 6.)

**What this adds to part 3, exactly:** part 3 ruled *how* `agora_transparency` versions itself —
pre-releases until marketplace-ready — and named no condition for leaving that phase. His sentence
names it: **the first stable version is cut when the template is finished, not when a milestone
feels close.** And the second clause is the operative one for today:
<!-- cspell:disable -->*"de momento sigamos"*<!-- cspell:enable --> — **nothing is released now.**

⚠️ **What this is NOT, named because it is the easy misreading and it would be a real loss.** It is
**not** a prohibition on pre-releases. Part 3 *permitted* them and required none; he has declined
to cut one **today**, which is a decision about today. A reader arriving here later must not find
*"D-050 part 3 was narrowed to forbid pre-releases"* — the phase stays available the moment a
pre-release would buy something, and part 3's own measurement is the reason it exists:
**`haven` and `byte` both spent a pre-release phase in public before promising anything**, and the
theme lost its phase because *"it was never forbidden — it was never considered"*.

⚠️ **And it does not set a date, a version number or a definition of "finished."**
<!-- cspell:disable -->*"Terminada al 100%"*<!-- cspell:enable --> is his standard, applied by him. This project's own instrument for it is the unit roadmap:
unit 007 is *"Publication — the human's hands"*, and units 004, 005 and 006 stand between here and
it. So the honest statement of today's position is that the template is **three units from the
condition he named**, and that is a measurement rather than an estimate of when.

**Consequence for the release procedure, so nobody looks for work that does not exist:** part 2's
Wednesday convention and the unchecking step bind `agora_theme`, which has published branches. They
bind `agora_transparency` from its **first** tag, which has not been cut. **Measured here rather
than quoted from part 3's reading of the project page, because a tag is a local fact:** `git tag`
in this working copy returns **0 tags**, against **10** in the theme's (`1.0.0`-`1.0.8`, `1.1.0`).
⚠️ Part 3's table counted **8 released tags** for the theme on 2026-09-06 and there are **10 tags
on disk** today — **two different quantities**, since a tag is cut here and a release is created on
drupal.org by [andres] (rule 10), so the pair is reported as a difference to be read rather than as
a contradiction. Neither figure is this record's business beyond noting that one of them moved. Nothing
is owed to [andres]'s form (rule 10) today: **no tag, no release notes, no drupal.org action.**


---

### D-051 · `agora_setup` — a third package: what would live in it, why it is justified, and why it is NOT being created today

⚠️ **READ THE SPLIT BEFORE READING ANYTHING ELSE, because the two halves of this record have
different standing and mistaking one for the other is the only way to misuse it.**

| | Status |
|---|---|
| **The DEFERRAL** — do not create the module today; build the sign-in page and the read-only panel instead; record the module so the argument survives | ✅ **SIGNED by [andres], 2026-09-06** |
| **The MODULE ITSELF** — whether `agora_setup` is ever created, under what name, with which of the five tenants below | 🔵 **OPEN RULING.** Taken when unit 005 opens. Nothing here is a signature for it |

**Everything below the fold is the argument for a module that does not exist and has not been
approved.** [andres] approved the *approach* — <!-- cspell:disable -->*"enfócalo como dices"*<!-- cspell:enable -->
("frame it the way you say" — translated, per rule 6) — and the approach is: **build the sign-in
page now, record the module, do not create it yet.** A reader who arrives here in unit 005 and finds
five tenants, a justification and a cost table may reasonably conclude the design is settled. **It is
not. The decision to create it has not been taken.**

---

#### 1 · How the question arose, which matters because it is the wrong reason

He could not find where the currency is configured.

**It is seven field instances across seven admin pages, for one decision** — *"this portal speaks
euros"*. Verified on disk, and the enumeration is D-047's own, re-read rather than carried:
`agreement.field_agora_base_amount`, `agreement.field_agora_base_obligations`,
`contract.field_agora_base_amount`, `contract.field_agora_base_tender_amount`,
`grant.field_agora_base_amount`, `person.field_agora_base_remuneration`,
`person.field_agora_base_severance` — every one `field_type: decimal`, every one carrying
`prefix: €`, spread over **four** bundles, and each with its **own** field-edit form. A council that
changes six of the seven leaves the site contradicting itself in public, **which is the exact defect
fixed on 2026-09-05** (D-047).

⚠️ **The counts do not line up the way a reader expects, and the mismatch is the reason this is
seven pages rather than six or eight.** There are **six** field storages behind those seven
instances — `field_agora_base_amount` is shared by `agreement`, `contract` and `grant` — and
**eight** instances carrying `prefix`/`suffix` keys at all, the eighth being
`contract.field_agora_base_bidder_count`, an `integer` labelled *"Number of bidders"* whose prefix is
correctly empty. **The currency lives on the INSTANCE, not on the storage**, so the number of places
to visit is seven: not six (storages), and not eight (every field that could carry a unit).

**His proposal:** a checkbox plus a currency selector, with the field prefixes as the fallback source
of truth when the checkbox is off. In his words, on the alternative of documenting the prefixes and
leaving it there: <!-- cspell:disable -->*"poner solo el prefijo lo veo algo cutre"*<!-- cspell:enable -->
("just putting the prefix in strikes me as a bit shabby" — translated, per rule 6).

**And he then asked the question that decided this record**, which is a better question than the one
it answers: <!-- cspell:disable -->*"Si nos pusieramos a hacer el módulo de agora_setup nos tiene
que rentar... Que no sea solo por la moneda, si se hace es para que tenga sentido."*<!-- cspell:enable -->
("if we were to go and build the `agora_setup` module it has to pay for itself… it should not be just
for the currency; if it is done, it is done so that it makes sense" — translated, per rule 6.)

**That is the test this record applies, and the currency fails it on its own.**

#### 2 · Why a theme-level override was REFUSED — and this belongs where somebody meets it before "improving" it

🔴 **A theme setting that SHADOWS the field instances cannot work, and it fails silently, in public,
about money.**

- The **register tables** are rendered by **Views**, whose `number_decimal` formatter reads the
  **field instance settings** and nothing else. Views never consults theme settings; there is no
  mechanism by which it could.
- The **two front-page figures** are formatted by the **theme**, and since `agora_theme` commit
  `de0fc18` they read **the same field instance settings** — `_agora_theme_amount_unit()`, which
  resolves the unit from the instances of the field the aggregate query actually summed.

**So a theme-level currency setting would change the second surface and cannot change the first.**
The front page would say one currency and its own register tables another, on the same site, with no
error anywhere. **That is the contradiction D-047 fixed on 2026-09-05, re-entering by a different
door and wearing the label of a feature.** Recorded as **I-114**, generalised past this case, because
the next person to meet it will be reading a settings form rather than this file.

**The shape that DOES work is the same checkbox with the opposite mechanism: it WRITES the seven
field configs.** There is then still exactly one source of truth, and what the setting buys is the
*visiting of seven admin pages* rather than the reading of a second value.

⚠️ **And a writing global carries an obligation the shadowing one appeared not to: it must SHOW what
the fields currently hold and FLAG divergence.** Someone editing a field afterwards makes the global
stop describing reality, and a settings form that keeps displaying the last value it wrote is then
lying about the site it configures.

⚠️ **The divergence detector already exists and is deliberately silent, which is the part that would
be missed.** `_agora_theme_agreed_unit()` already returns `NULL` when the summed instances disagree;
the theme then prints **no unit at all** and logs a warning naming the view, the display and each
instance's setting. That is correct and conservative — D-047 signed it in as many words: *"a total
wearing one of two labels would be a wrong statement where a missing one was available"* — and it is
**invisible to the person who caused it**, who is looking at an admin form, not at `dblog`. Surfacing
that state is a screen, not a formatter change.

#### 3 · The structural constraint that forces a THIRD package, rather than a second

**The site template may contain ZERO code.** `RequirementsTest` requires **0 `*.info.yml` files** in
the whole package, it is a hard marketplace requirement, and `tests/bin/no-code-in-template` enforces
it here. **A settings form cannot live in `agora_transparency`**, and no amount of wanting it to
changes that.

**The theme can hold code, and does** — `theme-settings.php`, `agora_theme.theme`. **But a theme
writing content-model configuration is the wrong layer**, and the failure is concrete rather than
aesthetic: field instance settings belong to the content model, and **a site that swaps the theme
loses the screen** while keeping every field it configured. The same is true of an AI retrieval layer
and of a compliance dashboard: none of them stops being needed because somebody changed the
appearance.

**So: template = no code · theme = wrong layer · therefore a third package.** That is the whole of
the structural argument, and it is why this cannot be solved by putting the form somewhere cheaper.

#### 4 · What would justify it — and it is NOT the currency

**Unit 005**, quoted from `specs/000-project/ROADMAP.md` verbatim:

> 2. **RAG over the document corpus**: it indexes **only published documents**.
> 3. **Mandatory citations**: every answer links to its sources; outside its sources it answers "I don't know".

**That is product logic, not configuration.** Drupal CMS's AI recipe supplies the **provider
plumbing** — which model, which key, which endpoint — and **none** of that discipline. No config
object can decide what enters an index, and no config object can refuse to answer. Point **8** adds
*"Key configuration via environment variable / post-installation UI"*, and the UI half of that is a
second screen.

🔴 **And here is a gap in our own plan, found today rather than in unit 005: point 1 calls that piece
a "Recipe `agora_ai`", and a recipe cannot contain code.** Points 2 and 3 do not fit the vehicle
point 1 names. **A dated note has been appended to the ROADMAP's unit 005 section** recording this;
the section itself is not rewritten (rule 8), because it is direction and its real scope is fixed in
that unit's own scaffolding turn.

#### 5 · The tenants — all five, so the module is judged on what it would actually hold

1. **The AI assistant's retrieval-and-citation layer** — unit 005, points 2 and 3.
2. **The API key screen** — unit 005, point 8.
3. **The post-install setup page** — council name, mark, currency, masthead image, contact.
4. **The currency editor** that writes the seven field instances (§2 above).
5. **A compliance dashboard** — which obligations are covered, which registers are empty, what has
   not been updated in twelve months. ⚠️ **Proposed by [ejecutor], not requested by [andres]**, and
   labelled so nobody reads it as his. It is the **auditing** half of the product's own positioning:
   `CLAUDE.md` names *"auditing of the site's own configuration (Config Guardian) as a feature"*, and
   the 2026-08-24 research found there is **no published Drupal content model for Spanish
   transparency obligations** at all — *"nothing to be consistent with"*, its own words, over a
   measured table of nine projects plus LocalGov Drupal, which models services and directories and
   **not** contracts, convenios, subvenciones, budgets or senior-official remuneration.

   ⚠️ **One claim this record was briefed with is NOT supported by that research and is corrected
   rather than repeated: the brief said the incumbent competitor "is not Drupal".** The research
   names **Gobierto** as the incumbent — a mid-size municipality's portal resolved to a
   `*.gobierto.es` certificate, which is how it was identified — and then says in as many words:
   *"Open-source status and stack are **not stated on the page** — do not assert either."* **So the
   supportable claim is about the Drupal ecosystem modelling nothing, not about what Gobierto is
   built on.** The argument survives the correction intact; the correction is recorded because
   asserting a competitor's stack from a record that forbids it is exactly the defect D-045
   named — a precondition quoted as though it had been checked.

⚠️ **[andres]'s binding condition on ALL FIVE**, and it disqualifies the obvious shape:
<!-- cspell:disable -->*"imagina que quieren cambiar alguna de esas opciones en el futuro, que puedan
hacerlo también."*<!-- cspell:enable --> ("imagine they want to change one of those options in the
future — they should be able to do that too" — translated, per rule 6.) **So it is a settings page
that happens to be useful at install time. It is NEVER an install-time wizard.** A wizard that runs
once and cannot be reopened fails this condition on the day the council changes its name.

**Unit 004 does not need it, and saying so keeps the justification honest.** Editorial workflow, ECA,
the FOI webform and the tracking panel are all configuration over existing contrib. **Two units want
this module, not three.**

#### 6 · The cost, stated plainly, because it is the whole argument for the deferral

A third public project on Drupal.org means: **its own project page · its own releases · its own
security-advisory coverage · its own line in the SBOM · its own CI pipeline.**

**None of that is theoretical here — the SECOND package already produced measured costs, twice, in
the last two days:**

- **D-050**, signed 2026-09-06: publishing `agora_theme` 1.1.0 put **two download rows** on the
  project page offering a choice that was not a choice, and nobody set the flag that caused it. That
  is a per-project failure mode, and a third project gets its own copy of it.
- **D-028 chose option B over option C for this exact reason, and its wording reads as though it were
  written for today**: extracting shared scripts into a third repository is *"correct at ten
  repositories. At two it is a third thing to release, version and gate, for four shell scripts."*
  ⚠️ And the strain is already visible: **T-1501 ported `executable-bit` from the theme into the
  template**, which is the **opposite** of the direction D-028's manifest models, and it opened a
  D-028 question that is still open. **Two repositories already do not share cleanly.**

**Creating a third package today to hold one checkbox, for a module two units want and neither has
started, is paying before using. That is the deferral, and it is the whole of what was signed.**

#### 7 · What ships instead, today

- **The sign-in page** — theme work, in progress in `agora_theme` at the time of writing.
- **A READ-ONLY currency panel** in the theme's settings, which shows the money fields it discovers —
  label, bundle, current prefix and suffix — each with a direct link to its own edit form.

**Two properties make the panel safe to ship from the theme, and both are the reason it is read-only
rather than a first instalment of tenant 4:**

- **It WRITES NOTHING**, so it creates no second source of truth and §2's failure mode cannot occur.
  It answers *"where is the currency set?"* and changes nothing.
- **It discovers fields by TYPE rather than from a hard-coded list**, so an eighth field appears in
  it by itself. ⚠️ **The discovered set is the eight instances carrying `prefix`/`suffix`, not the
  seven money ones** — `bidder_count` is listed with its prefix shown as *not set*, because filtering
  it out would mean the panel deciding what counts as money, which is the judgement it exists to hand
  back to the reader.

**A read-only panel is not a smaller version of the currency editor. It is the half that can be built
without a module**, and building it does not commit the project to building the other half.

#### 8 · What this decision does NOT do, named so nobody infers it

It does **not** approve the creation of `agora_setup`, fix its name, or fix its tenant list — §5 is
an argument, not a scope. It does **not** schedule anything: no wave, no task row, no date. It does
**not** reopen D-047, whose ruling stands and whose mechanism §2 depends on. It does **not** reopen
D-014 (the theme as a separate project) or D-028. It does **not** change `recipe.yml`, `config/` or
any field instance — the seven still carry `prefix: €` and the euro is still the demo's example, per
D-047. It does **not** add a dependency, and nothing here touches the SBOM. And it does **not** make
the read-only panel a promise of the writing one: shipping §7 leaves §5's tenant 4 exactly as
undecided as it was before.

---

### D-052 · The sign-in page a stock install never sees, and the supported hook that will fix it

**SIGNED by [andres], 2026-09-06** — after being shown a corrected analysis that **reversed a
recommendation he had already approved**. His instruction on being shown the correction:
<!-- cspell:disable -->*"lo que mejor sea, lo que me recomiendes que esté bien pensado e
investigado"*<!-- cspell:enable --> ("whichever is best — whatever you recommend, so long as it is
well thought through and researched" — translated, per rule 6). §6 records the withdrawn approval,
and it is the part of this file with the longest shelf life.

⚠️ **READ THE SPLIT FIRST.** The ruling has two halves, they have different standing, and only one
of them can be acted on today.

| | Status |
|---|---|
| **NOW — option B.** Change nothing. The sign-in page stays as it is: unreached on a stock Ágora install, reached on a standalone install of `drupal/agora_theme` | ✅ **SIGNED.** It is already the state of the tree — this decision ships **zero** lines of code |
| **LATER — option C.** A module implements the documented `hook_gin_login_route_definitions_alter()` and removes `user.login` from the list | ✅ **SIGNED as the direction**, 🔵 **and it cannot be scheduled yet.** It needs a module, and that module is D-051's **open ruling**. Nothing here approves creating one |

---

#### 1 · The defect, and why it is nobody's bug

`agora_theme` shipped a sign-in page — commits `c320586` (*"a sign-in page of the theme's own, and a
panel that says where the currency lives"*) and `20056e1`, both on disk in the sibling checkout, and
covered by T-1601. **On a stock Ágora install nobody ever sees it.** `gin_login`'s theme negotiator
takes `user.login` and four neighbouring routes and hands them to the site's admin theme.

**The page is not broken; it is not reached.** `_agora_theme_signin()` returns `NULL` off the
`user.login` route, and the function that would build the panel — `agora_theme_preprocess_page()` —
is a **theme** preprocess, so it runs only while `agora_theme` is the active theme. On that one
route it is not, so the panel is never built and no markup is wrong anywhere.

⚠️ **And `gin_login` is not being rude. Recording that is not politeness; it is what makes the fix
a supported hook rather than a fight.** The negotiator does not impose Gin. It imposes
`system.theme:admin`, **whatever that happens to be**. Its opinion is not *"this screen should look
like Gin"* but ***"signing in is an administrative task and should look like the administration"***
— a defensible position, and one Ágora's own configuration agrees with by omission: `recipe.yml`
sets `system.theme` `default: agora_theme` and **never sets `admin`**, while `drupal_cms_admin_ui`
sets `admin: gin`. The module is doing exactly what the configuration it reads tells it to.

#### 2 · What was measured, at source

Read on 2026-09-06 from `gin_login` **2.1.4** in the `~/agora-cms` rig, and from Drupal core in the
same rig. ⚠️ **Two figures this record was briefed with are corrected below rather than repeated**,
which is why every item names the file it came from.

**a · The negotiator's whole decision is one config read.** `src/Theme/ThemeNegotiator.php:99`:

```php
return $this->configFactory->get('system.theme')->get('admin');
```

reached when the current route is a key of the route list, and `FALSE` otherwise. That is the entire
mechanism.

**b · Priority 1000 — and it does not win narrowly.** `gin_login.services.yml:9` tags the negotiator
`{ name: theme_negotiator, priority: 1000 }`. In core's `core.services.yml`,
`theme.negotiator.default` — the negotiator that would return `agora_theme` — sits at **-100**
(line 691), and **1000 ties the highest priority core uses anywhere** (`theme.negotiator.ajax_base_page`,
line 696). There is no priority left to outbid it with.

**c · The route list is hard-coded, and it is FIVE routes, not one.**
`src/Services/GinLoginRouteService.php:50-76`: `user.login`, `user.pass`, `user.register`,
`user.reset.form`, `user.logout.confirm`. The service's constructor takes **one** argument — a module
handler — and reads **no config at all**. No setting anywhere shortens this list.

**d · ⚠️ `gin_login.settings` is NOT "the logo and nothing else". The brief said that; it is wrong.**
`config/schema/gin_login.schema.yml` declares **two** mappings — `logo` **and** `brand_image`, the
sign-in wallpaper — each with `use_default` and `path`. The claim that survives the correction is the
one the argument actually needs: **the route list is not in that object, nor in any other.** And the
corrected detail strengthens the case rather than weakening it, because `drupal_cms_admin_ui` already
writes to that object — a `simpleConfigUpdate` on `gin_login.settings` setting
`brand_image.use_default: false` and `path: public://login-wallpaper.png`. **Even the configurable
half is already spoken for by the recipe chain.**

**e · It arrives inside another recipe with 22 other modules — the brief said "roughly twenty", and
the real number is worth having.** Counted from `recipes/drupal_cms_admin_ui/recipe.yml` on disk: the
`install:` list holds **23** modules, one of which is `gin_login`. The recipe declares **no
`recipes:` key**, so that list is the whole of what applying it brings. In full, because a count with
no list is a number nobody can check:

<!-- cspell:disable -->
```
announcements_feed · automatic_updates · coffee · contextual · dashboard · dblog ·
drupal_cms_helper · drupical · file · gin · gin_login · gin_toolbar · menu_link_content ·
menu_ui · navigation · navigation_extra_tools · project_browser · sam · tagify ·
tagify_user_list · update · views_ui · view_password
```
<!-- cspell:enable -->

⚠️ **The `cspell:disable` pair around that block is deliberate and follows the project word list's
own stated policy**, not convenience: these are third-party machine names quoted verbatim from
another project's file, and declaring them in `.cspell-project-words.txt` would make them
permanently correct **everywhere in this repository, forever**. That is the same reasoning the word
list gives for scoping [andres]'s Spanish quotations in place instead of declaring them.

**f · There IS a supported extension point, documented by the module itself.**
`GinLoginRouteService.php:78` ends the getter with

```php
$this->moduleHandler->alter('gin_login_route_definitions', $route_definitions);
```

and `gin_login.api.php` documents `hook_gin_login_route_definitions_alter(&$route_definitions)` with
a worked example. **An implementation can `unset($route_definitions['user.login'])` and leave the
other four routes exactly as they are** — which is the right granularity, because Ágora has a
sign-in page and no opinion at all about the password-reset or logout-confirm screens.

**g · ⚠️ A THEME cannot implement that hook, and this was verified in core rather than assumed —
it is the whole reason option C needs a module.** `ModuleHandler::alter()` builds its callback list
through `getCombinedListeners()`, which groups listeners **by module** (`iterateByModule()`) and
orders them by `moduleList` weight. Theme implementations live in a **different registry** — the
`theme_hook_list` key-value collection that only `ThemeManager::alterForTheme()` reads. Nothing
dispatched through the module handler ever consults it. So a `hook_gin_login_route_definitions_alter()`
written into `agora_theme.theme` would be **silently ignored**: no error, no warning, no log line,
and a sign-in page that still does not appear.

#### 3 · ⚠️ What the hook does NOT switch off — found by reading the module, not its documentation

**This section exists because option C is a one-line change whose blast radius is not one line, and
three of its edges are only visible in the source.**

- **`gin_login_theme_suggestions_page_alter()` does not read the altered list.** It has its **own
  hard-coded `switch`** on the route name (lines 254-271), so it keeps adding the
  `page__user__login` suggestion after the alter hook has removed that route. It happens to be
  harmless — `gin_login_theme()` builds its theme registry entries **from** the altered list, so the
  suggestion resolves to no registered template and the active theme's `page.html.twig` renders
  anyway. ⚠️ **But that is a coincidence of two functions disagreeing, not a designed off-switch.
  Option C must therefore be verified by rendering the page, never by quoting `gin_login.api.php`.**
- **`gin_login_form_alter()` matches on form ID, not on route**, so the *"Forgot your password?"* and
  *"Create new account"* links it injects into `user_login_form` **survive** the removal. Not a
  defect — they are useful links — but the sign-in page after option C is core's form plus
  gin_login's two links plus Ágora's panel, and a criterion written as *"no trace of gin_login"*
  would fail against a correct implementation.
- **`_gin_login_gin_is_active()` gates the suggestion alter on Gin being in the admin theme's base
  chain.** So a site that sets its **admin** theme to `agora_theme` already gets the Ágora sign-in
  page today, through `gin_login` rather than around it — the negotiator returns `agora_theme`, the
  suggestion is never added, and the theme's own `page.html.twig` runs. ⚠️ **This is recorded as
  evidence that the module's logic is coherent, NOT as a fourth option:** it would render the whole
  administration in a public-facing theme, discarding the Gin experience that `drupal_cms_admin_ui`
  exists to provide, which is a far larger change than the one being made.

#### 4 · The options, with costs that were measured rather than estimated

| | Option | Cost, measured |
|---|---|---|
| A | Stop applying `drupal_cms_admin_ui` | 🔴 **Rejected.** §2(e): it forfeits **22 other modules** — the whole Drupal CMS administration experience, `gin`, `navigation`, `project_browser`, `dashboard`, `automatic_updates` and the rest — to change **one** route. And diverging from a Drupal CMS default is precisely what a marketplace reviewer inspects. ⚠️ **A recipe cannot uninstall a module either**, so the only way to be rid of `gin_login` is to not apply the recipe that installs it |
| **B ★ now** | Accept it. The sign-in page serves standalone installs of `drupal/agora_theme` | ✅ **Zero.** It is today's actual state. The theme is a **published, separately installable project** (D-014, D-050), so the page is not dead code — it renders on any site running `agora_theme` without `drupal_cms_admin_ui`. The axe gate measures it on the theme's fixture site, which has no `gin_login`: `/user/login` is the **7th** and last entry in `PAGES` at `tests/src/Nightwatch/Tests/axe.js` |
| **C ★ later** | The unit-005 module implements the documented alter hook and removes `user.login` | **One hook implementation, plus the verification §3 demands.** **Free when the module exists**, and it exists for the AI assistant (D-051 §4-5), not for this |

#### 5 · Ruling

**B now, C when the module lands.** The shape is D-051's exactly, and the cross-reference is the
point of writing it down: **the module is not justified by this, and this rides along free once it
exists.** A sign-in page is not a reason to create a public Drupal.org project — §6 of D-051 prices
that at its own project page, its own releases, its own security-advisory coverage, its own SBOM
line and its own CI pipeline — but it is a genuine, already-built benefit that arrives at no
additional cost the day the module does, and a decision taken about the module without this on the
table would be taken on an incomplete list.

**Recorded as a sixth tenant against D-051 §5**, and recorded **here** rather than by editing that
list, because D-051's deferral half is signed and rule 8 forbids editing it. **Tenant 6 · removing
`user.login` from `gin_login`'s route list**, so the theme's sign-in page is reached on the installs
the template actually produces.

#### 6 · ⚠️ The approval that was given on a cost estimate nobody had measured

**[andres] first approved option A, and he approved it on an incomplete framing of mine.** He was
told `gin_login` could be dropped from *"the Ágora recipe chain"* — **without** its having been
measured that it arrives inside **another recipe's** install list alongside 22 other modules, and
**without** the alter hook having been found. He agreed, reasonably, to something described as cheap
that is not cheap.

**The approval was given and then withdrawn on measurement, by the person who gave the wrong
framing.** That asymmetry is the reason this section is here: the correction cost one conversation,
and it would have cost a marketplace review had it been discovered later.

🔴 **This is the third time in one day that a claim about a decision was made without opening the
thing it was about**, and the pattern is the finding rather than any of the three instances:

1. **D-003 and Tailwind** — corrected in `044e6e4`.
2. **The Gobierto stack claim** — refused by an implementer, and already recorded inside D-051 §5,
   where the research being cited says in as many words *"do not assert either"*.
3. **This record's own brief** — three separate figures wrong in one page: *"roughly twenty"*
   modules (22), *"the logo and nothing else"* (two mappings), and the whole of option A's cost.

Recorded as **I-115**: *a recommendation is only as good as the cost estimate under it, and a cost
nobody measured is a guess wearing a recommendation's clothes.*

#### 7 · Where the alter-hook work is recorded, and why it is NOT a task row

**It must not be findable only inside a decision record**, and it is not. It is written as a **dated
note in `specs/000-project/ROADMAP.md`'s unit 005 section**, beside the 2026-09-06 note D-051 §4 put
there.

**Why that home and not a task row, stated because the alternative was the instruction's first
suggestion.** Three things were checked on disk before choosing:

- **`specs/005-ai-and-governance/` holds a `README.md` and nothing else.** There is no `tasks.md` to
  append to, and creating one is a **scaffolding turn** — the `orquestador`'s work, with dated
  research and a plan in front of it — not a row appended by an implementer.
- **Unit 003's `tasks.md` is the only open task list, and this is not unit 003's work.** Its own
  wave 16 already refuses the neighbouring case in as many words: *"a row that starts to write field
  configuration from the theme is out of scope until that ruling exists."* The same logic binds
  here, and harder — this work cannot even be attempted without a module.
- **A task row is a schedule, and there is nothing to schedule.** D-051 §8 says of itself that it
  *"does not schedule anything: no wave, no task row, no date"*, and inventing a `T-` number for
  work blocked on an open ruling would put a dangling commitment in a file that
  `tests/bin/cited-tasks-exist` reads as an accountability record.

**The ROADMAP note is direction, which is exactly what this is**, and it lands where unit 005's
scaffolding turn will read it — the same instrument, in the same section, that D-051 used for the
same reason one day earlier.

#### 8 · What this decision does NOT do, named so nobody infers it

It does **not** approve creating `agora_setup`, or any module — D-051's open ruling is left exactly
as open as it was. It does **not** schedule the alter hook: §7 records direction, not a wave or a
date. It does **not** change `recipe.yml`, `composer.json`, `config/` or the SBOM; `drupal_cms_admin_ui`
stays in the chain and `gin_login` keeps arriving with it, on purpose. It does **not** touch
`agora_theme`: the sign-in page and its tests ship unchanged, and T-1601's criteria are unaffected.
It does **not** reopen D-014 or D-050. And it does **not** claim the sign-in page is unused — it is
reached on every standalone install of the theme, and measured on every axe run.


---

### D-053 · Where axe runs over the demo pages: a PHPUnit test in the `phpunit` job, and no new job

**SIGNED by [ejecutor], 2026-09-07, under [andres]'s standing delegation** — the same delegation
D-027 through D-030 were signed on, and for the same reason: **this is methodology, not a product
trade-off.** It decides *where a test runs*, not what the product does. Nothing in it changes
`recipe.yml`, `composer.json`, `config/`, `content/`, the SBOM or a single line the installer
touches. His standing instruction, given 2026-09-06 and quoted because it is the standard this
record is measured against: <!-- cspell:disable -->*"lo que mejor sea, lo que me recomiendes que
esté bien pensado e investigado"*<!-- cspell:enable --> ("whichever is best — whatever you
recommend, so long as it is well thought through and researched" — translated, per rule 6).

⚠️ **This supersedes the axe half of D-036.** D-036's Playwright half was already superseded by
D-045. **Nothing in D-036 is edited** (rule 8); it is superseded here, and its own `★ C` was
explicitly *"conditional on one measurement"* that nobody took until now.

**The ruling, in one line:** the accessibility gate over the pages this template installs is a
**PHPUnit `FunctionalJavascript` test** at `tests/src/FunctionalJavascript/AccessibilityTest.php`,
collected by the **existing** `phpunit` and `phpunit-pgsql` jobs. **`.gitlab-ci.yml` is not
edited and the job list stays at ten.**

**This unblocks T-1201**, which has carried `⏸` and the words *"the surface is undecided"* since
unit 003 was scaffolded. It does not close it: the last clause of that row's own criterion is a job
list read from the API, and no pipeline has run yet. Its glyph moves to `○`, not `✓`.

---

#### 1 · The measurement D-036 left open, taken — and it discharges the condition

D-036 made its recommendation conditional on *"whether axe-core can be installed and run inside the
existing `Drupal CMS` job"*. Read at source on 2026-09-07 in `gitlab_templates` on `main`, the
answer is **yes, but not in that job** — and the five lines below are why. Each is a file and a
line number rather than a summary, because the whole cost of D-036's condition going untaken for a
month was that nobody had opened these files.

| what | where it was read | what it says |
|---|---|---|
| `_COMPOSER_YARN_INSTALL` defaults to `'1'` | `include.drupalci.variables.yml:75-77` | its own description: *"install Yarn in the `Composer` job and make `node_modules` part of the artifact for later jobs to use"* |
| the `composer` job runs `cd $CI_PROJECT_DIR/$_WEB_ROOT/core && corepack enable && yarn install` | `include.drupalci.main.yml:707-715` | and publishes `artifacts: paths: [.]` — the whole build directory |
| `.phpunit-base` declares `needs: [composer]` | `include.drupalci.main.yml:1709` | so the `phpunit` job already **receives** that `node_modules` |
| `.testing-job-base` declares the chrome service | `include.drupalci.main.yml:25-27`, `1468-1474` | `selenium/standalone-chrome:127.0`, alias `selenium` |
| `.test-variables` sets the driver arguments | `include.drupalci.main.yml:52` | `MINK_DRIVER_ARGS_WEBDRIVER_DEFAULT` pointing at `http://selenium:4444` |

**So axe-core is already in this pipeline, and so is a real headless Chrome, and the job that has
both is `phpunit` — the job that already runs this package's tests.** The measurement did not
merely pass; it made the question smaller than D-036 imagined it.

And upstream says so in one sentence, which is the strongest citation in this record because it is
a statement of intent rather than an inference from a variable —
`project.pages.drupalcode.org/gitlab_templates/jobs/phpunit/`, read 2026-09-07:

> *"The `phpunit` jobs run the PHPUnit tests defined by your module, including Functional,
> **FunctionalJavascript**, Kernel and Unit tests."*

The same page documents the Selenium service and the W3C driver arguments as the supported way to
run them. **This is not a clever use of a job; it is the job's documented purpose.**

⚠️ **The one thing NOT verified from this checkout, named rather than glossed:**
`SIMPLETEST_BASE_URL` is `http://localhost/$_WEB_ROOT` (`include.drupalci.main.yml:41`) and it is
never overridden, so whether the browser inside the `selenium` container resolves it to the job
container is GitLab Runner's networking, not something readable here. The local falsification runs
used a container hostname instead. **If that assumption is wrong the failure is a Mink connection
error, loud and immediate — not a false green**, which is the property that matters; and §6's
fallback is the remedy.

⚠️ **AND D-036 NAMED THE WRONG HOST, WHICH IS THE MORE USEFUL HALF OF THIS SECTION.** Its option C
proposed running demo-page axe on the **`Drupal CMS`** job. That job cannot do it, for three
reasons that are all in its own definition at `include.drupalci.main.yml:864-874`:

1. **It declares no `needs:` at all**, so it receives no artifact from `composer` and therefore has
   **no `node_modules` and no axe-core**.
2. **Its docroot is somewhere else.** It sets `_CMS_ROOT: 'cms'` alongside `_WEB_ROOT: 'web'`, so
   the site it builds lives at `$CI_PROJECT_DIR/cms/web`, not at the `$CI_PROJECT_DIR/web` every
   other job means by "the docroot". A path written for one is wrong in the other.
3. **It is a smoke, and it is priced like one.** CLAUDE.md's Gate A block records what it prints:
   `OK (1 test, 1 assertion)`. The read-only audit that took this measurement timed that single
   assertion at **219.6 s** — a figure taken from the job trace by that audit and not re-measured
   here, and flagged as such rather than absorbed. Hanging a nine-page browser suite off it would
   have been the most expensive of the available options as well as the only impossible one.

**A recommendation naming a host nobody had opened is D-045's lesson in a second shape**, and it is
why this record names the file and line for every claim above.

---

#### 2 · Option C's other half — a Nightwatch run in this repository — is refuted three ways

D-036's option C offered two ways to drive the scan: *"PHPUnit `FunctionalJavascript` **or a
Nightwatch run inside that job's docroot**"*. §1 settled the host; this settles the driver, and
the Nightwatch half is not merely worse — it is the one that fails while looking like it worked.

D-027 already established that Nightwatch **cannot be collected** in the template repository. That
finding is not merely reaffirmed here; it is now shown to fail in a way that **produces a green-
looking eleventh job**, which is worse than failing loudly.

1. **The rule that creates the job and the glob that fills it are rooted in different places.**
   `.nightwatch-tests-exist-rule` is `exists: tests/src/Nightwatch/**/*.js`
   (`include.drupalci.main.yml:514-519`), evaluated at the **repository root**. Core's
   `nightwatch.conf.js` globs `**/tests/**/Nightwatch/**/*.js` with
   `cwd: path.resolve(process.cwd(), '../' + searchDirectory)`, and the job runs it from
   `cd core` — so its `cwd` is the **docroot**.
2. **A recipe package is not under the docroot.** `include.drupalci.main.yml:119-125`:
   `DRUPAL_PROJECT_FOLDER=$CI_PROJECT_DIR/$DRUPAL_RECIPES_PATH/$PROJECT_NAME`, which for this
   package is `$CI_PROJECT_DIR/recipes/agora_transparency` — a **sibling** of
   `$CI_PROJECT_DIR/web`, invisible to that glob.
3. **A third filter would catch it even if the first two did not.** The job runs
   `yarn test:nightwatch --tag=$PROJECT_NAME` (`include.drupalci.main.yml:1544-1545`).

**Committing the parked `axe.js` would therefore add an eleventh blocking job that collects zero
tests.** That is I-050 exactly: a job that exists is not a job that ran.

⚠️ **AND THE PARKED README RECORDS THAT MATERIALISATION AS THE WIN.**
`Documents/projects/agora-parked/2026-09-02-axe-on-real-pages/README.md` closes with *"Committing
`Nightwatch/Tests/axe.js` MATERIALISES a `nightwatch` job in the template's pipeline … so the job
list moves from 10 to 11"*. The mechanism is real — it is how `agora_theme` went from nine jobs to
ten — but in **that** repository the tests are inside the docroot and in **this** one they are not.
Anyone resuming from that README walks into I-050 while believing they are avoiding it.
**That file needs annotating; it is outside this repository and is deliberately not edited from
here.** Naming it is this section's job. Fixing it is a separate, one-paragraph change in the
directory that owns it.

---

#### 3 · What was built, and what it measures

One file, `tests/src/FunctionalJavascript/AccessibilityTest.php`. It applies this recipe to a
freshly installed Drupal, walks **nine pages of the installed product** and runs axe over each.

Every page is **derived from the package**, never typed: the two Canvas pages come from
`canvas_page` entities at their own aliases, the front page from `<front>`, the four register
routes from each View's `page_1` path with its heading read from the `default` display, the node
from the first published `agora_base_contract` at its alias, and the not-found page from a path
that must not resolve. Renaming a register in config changes what is scanned instead of leaving the
test asserting a string nothing produces.

**Measured, on a local rig with the same Selenium image CI uses:**

```
OK (1 test, 187 assertions)      —  9 pages · 89-90 axe rules per page · 0 violations · 54 s
OK (19 tests, 2438 assertions)   —  the whole package suite, same rig, 9 m 19 s
```

⚠️ **The second line is four assertions higher than the arithmetic predicts, and the four are not
this file's.** CLAUDE.md:524 records the existing suite at `OK (18 tests, 2247 assertions)` (jobs
`12014958`/`12014959`, pipeline `950203`); the same eighteen read **2251** in this rig. The gap
predates this change. **The number to expect from the pipeline is therefore `19 tests, 2434
assertions`**, and if the trace reads 2438 the difference belongs to the pre-existing suite. Stated
this way rather than as one tidy number, because a prediction that hides its own uncertainty is
worth less than no prediction at all.

⚠️ **THE PAGES ARE SCANNED ANONYMOUSLY, AND THAT IS A MEASURED CHOICE THAT REVERSED THE FIRST
DRAFT.** The suite originally logged in as a reader holding only `access content`, on the reasoning
that it removed a dependency on how the anonymous role is configured. **The first real run refuted
it:** that surface came back with **three `region` violations**, and none of the three was this
template's markup — `.toolbar-title__label` and `.toolbar-badge` from the `navigation` module's top
bar, and `.coffee-form-wrapper` from `coffee`. All three are Drupal CMS admin chrome, none is
fixable here, and **no member of the public ever sees any of them.** A logged-in session is the
wrong surface for this question. Scanned anonymously — the way a citizen reads these pages, and the
way `agora_theme`'s own axe suite scans — the same nine pages report **zero** violations.

**This is the first accessibility result this project has ever had about the product rather than
about scaffolding**, and it is `specs/003-demo-content/plan.md:25-29`'s declared deliverable.

---

#### 4 · Falsified in four directions before it was trusted

A gate first seen green is a claim, not a measurement. Each row below was **watched failing**, then
watched passing again after the change was reverted.

| what was broken | what the gate did |
|---|---|
| an `<img>` with no `alt` injected into the theme's `page.html.twig` | **FAIL** — `image-alt x1 @ img[src$="druplicon.png"] :: <img src="/core/misc/druplicon.png" …>` — rule, count, selector and the offending markup, all in the message |
| `axe.min.js` moved aside | **FAIL in 3 seconds**, before the site is even installed, with the sentence naming `_COMPOSER_YARN_INSTALL` |
| the loop made to skip one declared page | **FAIL** — `8 of 9 declared pages scanned, 89-90 axe rules run per page, 0 violations` — the message that would otherwise have read as a pass |
| the bucket check pointed at a rule id axe never reports | **FAIL** — *"it landed in no bucket at all, which is not the same as passing"* |

⚠️ **The third and fourth rows are the ones worth re-reading.** Both produce **zero violations**.
A suite that only counted violations would have called both of them green, and that is I-045 in its
purest form — which is why the denominator and the bucket are assertions here and not comments.

Two further failures were **not** staged: the uniqueness check fired on a real duplicate
(`<front>` and the Canvas alias behind it are the same page, correctly), and the violation check
fired on the three real `region` findings above. **An assertion first seen failing on something
nobody planted is the strongest evidence in this record.**

---

#### 5 · axe-core's provenance, and the licence rider for [andres] — UNSIGNED

`axe-core` **is not named in Drupal core's `package.json`.** It arrives three hops down, read in
`core/yarn.lock` on 2026-09-07:

```
core/package.json  devDependency  nightwatch ^3.12.3
  └─ yarn.lock:8404   nightwatch-axe-verbose ^2.3.0   (resolved 2.3.1)
       └─ yarn.lock:3626   axe-core ^4.9.1            (resolved 4.10.3)
```

Confirmed independently on this machine, in a rig built from `packages.drupal.org` rather than from
the CI artifact: `web/core/node_modules/axe-core/package.json` — **6,819 bytes, `"version":
"4.10.3"`, `"license": "MPL-2.0"`** — and `axe.min.js` at **553,446 bytes**. Both figures match the
CI artifact byte for byte, from two machines that share no state.

**Every link in that chain is somebody else's decision**, which is exactly why the test's first
assertion proves the file is readable and fails with a sentence naming `_COMPOSER_YARN_INSTALL` if
it is not. An absent axe must be a red with an explanation, never a scan of nothing (I-007, I-032).

🔵 **THE RIDER, AND IT IS [andres]'s CALL — recorded UNSIGNED.** axe-core is **MPL-2.0** and it is
**not shipped**: it enters no `require` in `composer.json`, no `recommended.yml` row, and no file in
the package a user downloads. It is a test-time tool that exists only inside a CI job, on the same
footing as PHPUnit, phpcs and Selenium. **My reading is that it needs no SBOM line and no
licence-manifest entry**, and rule 2's *"every contrib module added needs a line"* is about things
the SBOM ships. That reading is a recommendation, not a ruling: **[andres] decides.** If he
disagrees, the fix is one line in `DECISIONS.md` and one in `MEDIA-LICENCES.md`'s neighbour, and
nothing about the test changes.

---

#### 6 · The fallback, named now rather than discovered later

The `phpunit` job gains one test and roughly a minute. Measured: **54 s** for the whole nine-page
scan, against a job that already performs ten full site installs. There is a large margin, and this
is not expected to be close.

**If it nevertheless lengthens `phpunit` past its timeout, option B is a hand-written `phpunit-axe`
job extending `.phpunit-base`** — the same shape as `phpunit-pgsql`, which is already the precedent
for adding one blocking job by hand without touching upstream's `include:` block. Taking it moves
the job list **from ten to eleven**, and **CLAUDE.md's Gate A table must be re-observed from the API
in that same commit**, not derived. Nothing about the test file would change.

---

#### 7 · What this decision does NOT do

It does **not** edit `.gitlab-ci.yml`, add a job, or move the job list off ten. It does **not**
touch `recipe.yml`, `composer.json`, `config/`, `content/` or `recommended.yml`. It does **not**
retire `agora_theme`'s `nightwatch` job: that gate keeps scanning the theme's own fixtures, which
is the correct scope for a theme installable on its own, and the two suites now ask different
questions of different surfaces. It does **not** edit D-036 or D-027. It does **not** add anything
to the SBOM — §5's rider is open, not decided. And it does **not** touch the parked directory
outside this repository; §2 names what needs annotating there and leaves the annotation to whoever
owns that tree.

---

#### 8 · The §5 rider, SIGNED — axe-core needs no SBOM line and no licence-manifest entry

**SIGNED by [ejecutor], 2026-09-12, under [andres]'s standing delegation**, given the same day in
two sentences, the first of which is the authority this signature rests on:
<!-- cspell:disable -->*"Firma tú lo que haya siempre que esté todo correcto y continua"*<!-- cspell:enable -->
("sign whatever there is yourself, so long as everything is correct, and continue" — translated,
per rule 6). It is the delegation D-027, D-028, D-029, D-030 and D-053 itself were signed on, and
its boundary is unchanged: **methodology and licence-constrained choices are [ejecutor]'s; product
trade-offs are not.** A licence question about a tool the package does not ship is squarely the
first kind, and §5's own text says the alternative costs *"one line in `DECISIONS.md` and one in
`MEDIA-LICENCES.md`'s neighbour, and nothing about the test changes"* — so nothing the installer
receives turns on it either way.

⚠️ **The signature is conditional on his own condition —
<!-- cspell:disable -->*"siempre que esté todo correcto"*<!-- cspell:enable --> — so
the absence was MEASURED on 2026-09-12 rather than carried from §5.** Three surfaces, three
commands, and the third is the one that matters because it is what a user downloads:

| surface | command | result |
|---|---|---|
| `require` | `grep -in axe composer.json` | **exit 1** — no match |
| Project Browser list | `grep -in axe recommended.yml` | **exit 1** — no match |
| the packaged tarball | `git archive HEAD \| tar -x` into a scratch directory, then a recursive case-insensitive grep | **373 files extracted · 5 files match `axe` · 0 of them ship axe-core** |

**The five matches are the useful part of this measurement, because "0 matches" would have been the
wrong answer to report.** Three are the word **`taxes`** (*"municipal taxes and charges"*, twice,
and *"municipal taxes and procurement"*). The other two are **prose in one content file** —
`content/node/542d60d9-f7b6-596c-9f15-7ee81964d139.yml`, the accessibility statement T-1105
shipped — which **names** axe-core in a sentence about how the site is tested. **Naming a tool is
not distributing it.** MPL-2.0's obligations attach to distributing the covered Source Code Form;
a GPL-2.0-or-later content file that mentions a tool's name triggers none of them, and the
marketplace criteria ask for a manifest of *"components like default content and images"* — not of
the test harness. `axe-core` therefore stays on the same footing as PHPUnit, phpcs, phpstan,
Selenium and chromedriver: read from the CI runner's own `core/node_modules` at test time, present
in no `require`, no `recommended.yml` row and no packaged byte.

**So: no SBOM line, and no entry in `content/MEDIA-LICENCES.md` or any neighbour of it.** Rule 2's
*"every contrib module added needs a line"* is a statement about what the SBOM ships, and this
ships nothing. ⚠️ **What this does NOT do:** it does not exempt anything, and it creates no
precedent that a test-time tool can become a shipped one quietly — the moment axe-core appears in
`require`, in `recommended.yml` or in `git archive`, it is a dependency and rule 2 applies in full.
The test's own first assertion is the guard on the other side of that line: it proves `axe.min.js`
is readable and fails with a sentence naming `_COMPOSER_YARN_INSTALL` if it is not, so an absent
axe is a red with an explanation rather than a scan of nothing (I-007, I-032).

🟡 **And one finding this verification produced that is NOT part of the rider, recorded because it
was found by opening the file the rider sent me to.** The shipped accessibility statement says
<!-- cspell:disable -->*"axe-core runs over six representative page types"*<!-- cspell:enable -->
and enumerates the **theme's fixtures** — *"two hand-built data tables, a view rendering a real
table, a prose page, a content record page and the core sign-in form"* — reporting **89 rules
applied per page, 0 violations**. Two things have moved underneath it since T-1105 was written:
the theme's axe gate now scans **7** pages (CLAUDE.md's observed table, pipeline `950212`, job
`12015106`), and **D-053 added a nine-page scan of the installed product** — which is the first
accessibility result this project has ever had about the product rather than about scaffolding, and
the statement does not mention it at all. The text hedges honestly (*"the figures are those
reported by the most recent run at the time this text was written"*), so it is **stale rather than
false** — but a public accessibility statement that understates its own gate is worth one edit, and
it is content rather than process, so it is named here and **not** touched from this turn. Owner:
whoever next opens T-1105's node. It needs no new task row.

---

### Rider · Unit 003's task budget: 65 rows against a ceiling of 34, and what the overrun displaces

**SIGNED by [ejecutor], 2026-09-12, under [andres]'s standing delegation** —
<!-- cspell:disable -->*"Firma tú lo que haya siempre que esté todo correcto y continua"*<!-- cspell:enable -->
("sign whatever there is yourself, so long as everything is correct, and continue" — translated,
per rule 6). **D-031 requires a rider naming what an overrun displaces; D-044 amended the mechanism
so the rider is a record rather than a precondition.** This is that record, and it is written
before closure rather than at it, which is the whole of what I-105 asks for.

⚠️ **A naming divergence, stated first because the dispatch that ordered this rider called it
`R-003`.** That label is recorded here so it is findable — but **this repository has never used an
`R-NNN` series.** Riders are named sections: `## Riders on wave 1, signed by [andres] 2026-08-21`,
`### Rider requested · The accessibility statement's unit`, `### Rider requested · Four-digit task
ids from wave 10`. **There is no `R-001` and no `R-002` on disk**, so a rider numbered 3 would
imply two predecessors that do not exist. Written in the house style instead, with the dispatch's
label quoted rather than adopted.

#### The arithmetic, re-derived by command and not carried

Every figure below was re-run on 2026-09-12 on a clean working tree. **The count is a command with
its output, never a number in prose** (D-031's own method fix):

```
grep -cE '^\| T-(9[0-9]{2}|1[0-6][0-9]{2}) ' specs/003-demo-content/tasks.md
```

| quantity | value | how |
|---|---|---|
| ceiling | **34** | `specs/003-demo-content/tasks.md:3` — *"Budget: **34** tasks"* |
| rows on disk | **65** | the command above |
| **overrun** | **31** | 65 − 34 |
| original signed scope | **30** | waves 9 (6) + 10 (11) + 11 (7) + 12's first six, `T-1201…T-1206` |
| rows appended beyond it | **35** | 65 − 30 = wave 12's `T-1207…T-1217` (11) + wave 13 (14) + wave 14 (6) + wave 15 (2) + wave 16 (2) |

⚠️ **`T-804` is in this file and is NOT in that 65**, which is correct rather than an oversight:
it is unit 002's carried row, three digits, and the anchored regex requires four. Counted by eye it
would make 66 and the budget would be wrong by one — the exact class of error the anchoring exists
to prevent.

#### D-044's necessity test, applied row by row: **9 necessary · 26 useful**

D-044's wording is the standard — *work without which something already signed is broken, false,
or impossible to ship* — and its ⚠️ is the reason this is enumerated rather than totalled:
*"'Necessary' is not 'useful'. This is not a licence to grow scope."*

**The nine that pass.** Seven are classified by `tasks.md`'s own accounting entries or by D-044's
own text; **two are mine, and they are marked as mine** because an unattributed classification is
how a total gets inflated.

| row | why it passes | whose classification |
|---|---|---|
| **T-1207** | the front page shipped SQL that summed a text column, so its headline money figure was **false on every database** | D-040 / D-044's *"the PostgreSQL fix"* |
| **T-1208** | the invariant that catches it; a signed gate that cannot see the defect it was green over | D-044 names it |
| **T-1209** | the PostgreSQL job; nine green jobs over a query wrong on every database, because the ones that ran were the **defaults** | D-040(2) |
| **T-1210** | ⚠️ **mine.** Without the frame, a shipped site presents a **fictional** municipality as a real one. That is the one judgement gate B is reserved for, and leaving it unframed made the English-language reading the wrong one — D-041's own words | **[ejecutor]**, borderline |
| **T-1211** | all **39** rows of `content/MEDIA-LICENCES.md` cited a build script that **did not exist**; a shipped manifest was false | D-044 names it |
| **T-1305** | discharges **T-1103**, signed scope then undone, whose criterion was already this chart's criterion verbatim | `tasks.md`, sixth entry |
| **T-1306** | six shipped PDFs print `21300.00 EUR` beside a table rendering `21,300.00`; not silent, **inconsistent** | `tasks.md`, sixth entry |
| **T-1308** | the same inconsistency on the seven register field instances | `tasks.md`, sixth entry |
| **T-1501** | both gate runners guard every invariant with `[ -x ]`, which answers TRUE on this checkout and FALSE on the runner — and **2 of 28** shebang files were committed `100644`, so it was already latently false here | `tasks.md`, eighth entry |

⚠️ **One row was considered for a tenth place and REJECTED, and saying so is worth more than the
row would have been.** **T-1314** deleted the discharged D-034 SBOM exemption, which removed a live
bypass from `sbom-check` — real hygiene. But by then `sbom-check` itself printed that the exemption
*"excused nothing"*, so **nothing signed was broken or false while it sat there**. It is counted as
**useful**. The nine above are nine because this one was argued and lost, not because nine was the
number I arrived with.

**The twenty-six that do not**, with the basis each actually rests on, because
`tasks.md`'s ninth entry is right that *"an unlabelled basis is how 'approved' gets read onto work
nobody approved"*:

| rows | count | basis |
|---|---|---|
| `T-1212`-`T-1214`, `T-1215`, `T-1216`, `T-1217` | 6 | [andres]'s front-page and footer direction; D-046 for the last two |
| `T-1301`-`T-1304`, `T-1307`, `T-1309`-`T-1312` | 9 | **[andres] approved the design round, 2026-09-04** |
| `T-1313`, `T-1314` | 2 | the masthead boundary [andres] set in his own words; SBOM hygiene |
| `T-1401`-`T-1406` | 6 | **D-049, signed 2026-09-05** — <!-- cspell:disable -->*"Sí, al cerrar esta ronda"*<!-- cspell:enable --> |
| `T-1502` | 1 | **measured failures** — pipelines `949480`, `949481`, `950124` red behind green local checks |
| `T-1601`, `T-1602` | 2 | **a defect found by opening the page**; no gate had an opinion about it |

**9 + 26 = 35.** ⚠️ The read-only audit that drafted this rider reached **9 and 26** as well. The
totals agreeing is not confirmation — **the sets may differ**, since the audit's nine is not
enumerated anywhere I can compare it against, and I reached mine by rejecting `T-1314` and
admitting `T-1210`. Stated rather than presented as agreement.

#### What the overrun displaces — four rows, and the draft's table was stale in both directions

⚠️ **Re-derived, and it is SMALLER than the seven the audit drafted.** Two corrections, and the
second is the one that matters:

1. **`T-1103` is done** — `✓` on disk, discharged by `T-1305`. It cannot be displaced.
2. 🔴 **`T-1201` is NOT closed, and the dispatch that ordered this rider says it is.** Disk wins:
   its glyph is **`○`**. The *work* landed — `tests/src/FunctionalJavascript/AccessibilityTest.php`,
   **29,500 bytes**, dated 2026-09-07 — and D-053 says in as many words that this does **not** close
   it: *"the last clause of that row's own criterion is a job list read from the API, and no
   pipeline has run yet. Its glyph moves to `○`, not `✓`."* So it is neither done nor displaced; it
   is **built and awaiting one API read**.
3. ⚠️ **`T-1307`, `T-1310` and `T-1406` cannot be counted as displaced, and this is the correction
   that makes the table honest rather than merely shorter.** All three are **among the 35 appended
   rows**. A row that is itself part of the overrun is not something the overrun pushed out — it is
   the overrun. Counting them would double-count in the project's own favour.

**So the displaced set is the rows of the ORIGINAL 30 that are undone and are not the closure
machinery itself:**

| row | state | what is actually missing |
|---|---|---|
| **T-1006** (second half) | `⏸` | the CSV-distribution table. **Leaves this unit today** by D-038 = A — see that record's signature block |
| **T-1106** | `○` | `screenshot.webp` is still the placeholder: **6,686 bytes**, sha256 `98363dd5a77e…`, unchanged since 2026-08-27 |
| **T-1107** | `○` 👤 | the visible preview at `agora-smoke.ddev.site`; needs a rebuilt rig and [andres]'s eyes |
| **T-1202** | `○` | Playwright: **no `playwright.config.*` in either repository** — `git ls-files` finds none in `agora_transparency` and none in `agora_theme` |

**Four rows.** `T-1203`, `T-1205` and `T-1206` are excluded deliberately: they are the invariant
sweep, the audit and the closure report — the closure mechanism runs at closure by construction,
and calling it displaced would make every unit's rider name its own last three rows.

#### What the overrun cost, stated as a cost rather than as an accounting note

**The reserve was four rows, one per named risk, and it was spent on none of them.** All four risks
were answered anyway — D-035 and D-010 without a row, the accessibility statement inside `T-1105`,
and the mirror by ruling — so the reserve's failure was not that the risks consumed it. It is that
**31 rows of unplanned work went in ahead of four rows of signed scope**, and three of those four
are the ones a marketplace reviewer meets first: the **screenshot** on the project page, the
**preview** [andres] looks at, and the **visual-regression gate** that would have caught by machine
the presentation defects I-106 records being found by eye.

**Nothing is re-based and no row is moved.** D-044 rules that the budget counts and does not gate,
and 26 of the 35 rest on [andres]'s own signatures and approvals — he is the one who signs scope.
What this rider refuses is the shape I-105 names: an overrun discovered at closure, when the only
options left are a rider signed under pressure or a quiet trim.

🟡 **The one recommendation this rider makes, and it is a recommendation rather than a ruling:**
unit 004's budget should be set **after** these four rows are placed, not before. Three of them are
publication-facing and two need [andres]'s hands; a ceiling written without them is a ceiling that
is already wrong on the day it is signed, which is how this one started.

---

### Rider · AMENDMENT, 2026-09-12 — the `2 of 28` at line 4327 is a row disagreeing with itself, not drift

**Rule 8 governs this: the table above is signed, so it is amended here and NOT edited in place.**
Line 4327, in the D-044 necessity table, justifies **T-1501** with *"**2 of 28** shebang files were
committed `100644`"*. **28 is wrong, the correct figure is 30, and the interesting part is that it
was never a measurement of anything.**

#### What was measured, and by what

`bash tests/bin/executable-bit`, re-run 2026-09-12 on a clean tree:

```
examined: 451 tracked file(s)
scripts:  30 with a shebang on line 1
findings: 0
```

**`30` is the guard's denominator** — the count of tracked files whose first line is a shebang, read
from the git index by the invariant itself. The two files fixed on 2026-09-06
(`tests/bin/content-timestamps.py`, `tests/bin/generate-demo-media.py`) were **2 of 30**.

#### Why this is not ordinary drift, and why that distinction is worth a rider

Drift is a number that was true and stopped being true. **This number was never true.** T-1501's own
row in `specs/003-demo-content/tasks.md` carries **both figures, three lines apart, written in the
same commit**: its evidence column prints the machine's `scripts: 30 with a shebang on line 1`, and
its prose says *"2 of the 28 shebang files"*. A hand-written figure sat beside the machine-printed
figure that contradicted it, in the same paragraph, and **nothing failed** — not the gate, not
`claims-match-sources`, not review. The wrong figure then propagated into `CLAUDE.md` and into this
decision record, so the same fabricated 28 came to stand in **three** files.

⚠️ **Nothing mechanical could have caught it, and saying which mechanism was missing is the point.**
`tests/bin/claims-match-sources` compares `CLAUDE.md` against the `# GATE-CLAIM:` lines of the two
gate runners — it does not read this file, it does not read `tasks.md`, and `30` appears in no
`GATE-CLAIM` line, because it is a denominator the invariant prints at run time rather than a check
count declared in a header. So the figure lived in prose in three places and in a machine's stdout
in one, with no comparison between them.

#### What does NOT change

**T-1501 still passes D-044's necessity test, and its reason is untouched.** The argument at line
4327 is that both gate runners guard every invariant with `[ -x "$INV" ]`, which answers TRUE on
this Windows checkout and FALSE on the Linux runner, and that the defect was **already latently
present here** — two shebang files committed `100644`. **That remains true at 30 exactly as it was
claimed at 28**: the numerator, the two file names, the latency and the conclusion are all
unchanged. Only the denominator moves, and it moves from a figure nobody measured to the one the
guard prints.

No other row, count, classification or total in the rider above is altered by this amendment. The
budget arithmetic (65 rows, ceiling 34, 9 necessary · 26 useful) does not reference the figure.

**Recorded by [ejecutor], 2026-09-12. No signature is sought: this corrects a measurement inside a
signed record without changing what the record decided.**


---

## Amendment · the unit 003 budget rider's row count, two entries behind — [ejecutor] 2026-09-19

**The signed rider's own heading reads *"65 rows against a ceiling of 34"*. On disk the unit holds
67.** The rider was signed 2026-09-12 at 65; `specs/003-demo-content/tasks.md` then recorded a
tenth reserve-accounting entry taking it to **66** (T-1701, 2026-09-13) and an eleventh taking it
to **67** (T-1702, 2026-09-19). Both entries were written in `tasks.md` and neither reached this
file, so the rider has been one row behind since the 13th and two rows behind since today.

**The corrected arithmetic: 67 rows against a ceiling of 34 — thirty-three over, not thirty-one.**
The classification split the rider records (9 necessary · 26 useful) is **unchanged**: T-1701 and
T-1702 are both *necessary* under D-044's test, so the split becomes **11 necessary · 26 useful**,
and 11 + 26 = 37 ≠ 67 because the rider's split covers only the rows it classified, which is the
rider's own stated scope and is not altered here.

⚠️ **What this amendment changes is a count, not a decision.** D-044 rules that the budget
**counts and does not gate**, so a rider that is two rows behind never blocked anything and never
would have — which is precisely why nobody noticed for six days, and why it is worth writing down:
**a number that gates nothing is a number nothing checks.** The two guards that could have caught
it do not look here: `tests/bin/claims-match-sources` reads `CLAUDE.md`, not `DECISIONS.md`, and
`tests/bin/cited-tasks-exist` compares task *ids*, not task *counts*. Extending either is a real
change with no row yet, and it is named here rather than left implied.

**Nothing else in the rider is altered.** Every row, classification, displacement and total it
records stands; only the headline count moves, and it moves to what `tasks.md` has said since the
entries that produced it.

**Recorded by [ejecutor], 2026-09-19. No signature is sought: this corrects a count inside a
signed record without changing what the record decided** — the same footing as the T-1501
denominator correction above.


---

## Amendment · the same count went stale again the same day, and the fix is to stop writing it here — [ejecutor] 2026-09-19

**The amendment immediately above was written this morning to correct the rider's row count from
65 to 67. By the afternoon the disk held 69.** Wave 19 added two rows (T-1903, T-1904), the
twelfth reserve-accounting entry recorded it in `tasks.md`, and this file was one wave behind
within hours of being corrected.

⚠️ **That is not carelessness, it is the shape of the record.** The correction above named a
number. A number in a file nothing executes has an expiry date and no mechanism watches it — the
very sentence the amendment above wrote down as its own lesson, reproduced by the amendment
writing it. Three corrections of one figure (65 → 66 → 67 → 69) in seven days is the evidence.

**So this amendment names the COMMAND instead of the count**, and every future reader should run
it rather than read a digit here:

```
grep -cE '^\| T-(9[0-9]{2}|1[0-9]{3}) ' specs/003-demo-content/tasks.md
```

**It printed 69 on 2026-09-19** against a ceiling of 34 — thirty-five over. That figure is
recorded as a dated measurement, exactly like a pipeline observation, and **it is not to be
refreshed here when it moves**: `specs/003-demo-content/tasks.md` carries the live arithmetic in
its reserve-accounting entries, which is one place rather than two, and a figure that exists in
one place cannot go stale in the other.

⚠️ **The counting command itself has broken four times by that file's own record**, most recently
on 2026-09-19 when `1[0-7][0-9]{2}` stopped matching the wave-19 ids it was supposed to count.
It is widened to `1[0-9]{3}` above. **A command is only better than a number while somebody
checks that it still counts the right things** — which is why the widened form and its printed
result are quoted together here rather than the command alone.

**Nothing in the rider or in this morning's amendment is otherwise altered.** D-044 is unchanged:
the budget counts and does not gate, so neither the stale 67 nor the live 69 ever blocked
anything.

**Recorded by [ejecutor], 2026-09-19. No signature is sought: this corrects a count inside a
signed record without changing what the record decided.**


---

## D-059 · `drupal/config_guardian` enters the SBOM — the governance half of unit 005 — [ejecutor] 2026-09-20

**Context in one line.** D-018 rider (a) closed the baseline SBOM and named the dependencies that
*are* a choice — *"ECA, AI, **Config Guardian**, Webform, Charts — each get their own D-NNN when
they arrive."* This is Config Guardian arriving, under unit 005 plan §3 and task T-0509.

| Decision | Package | Constraint | Verified stable | Coverage | Maintenance on drupal.org | What it contributes |
|---|---|---|---|---|---|---|
| `D-059` | `drupal/config_guardian` | `^1.0` | 1.0.3 | `covered="1"` | Actively maintained · core `^10.5 \|\| ^11 \|\| ^12` | Configuration snapshots, rollback and impact analysis: the mechanism behind *"the portal audits its own configuration"* |

**Method, so it can be re-derived rather than trusted.** `updates.drupal.org/release-history/
config_guardian/current`, fetched anonymously and **re-read on 2026-09-20, the day of the commit**,
because a release status is a claim with an expiry date and rule 1 is about today. **4 releases;
newest `1.0.3`; newest STABLE `1.0.3`; `<security covered="1">`.** There is no `-alpha`, `-beta`,
`-rc` or `-dev` release in the project at all, so rule 1 is satisfied without a preference being
exercised. Licence `GPL-2.0-or-later`, read from the package's own `composer.json`.

### Why this is the cheapest thing in unit 005, stated as a measurement

Read at source inside the published `1.0.3` tarball, not inferred from the project page:

- **`dependencies: [drupal:config, drupal:file]`** in `config_guardian.info.yml`. **Zero contrib.**
  Both are core modules already present on every Drupal CMS install, so the SBOM grows by exactly
  **one** package and by **zero** transitive packages. That is the whole argument for shipping the
  governance half first: it costs one line and needs no ruling.
- `composer.json` `require` is **`{"php": ">=8.1"}`** — no PHP library, so nothing enters `vendor/`
  either.
- **No web-accessible snapshot directory exists to secure.** Snapshots are a gzip-compressed blob
  in a database table (`SnapshotManagerService::compressData()`), not files under `public://`.
  The question was asked before the dependency was added because a transparency portal that
  published its own configuration dump would be a disclosure incident wearing a feature's clothes.

### What is configured, and the two settings that carry a reason rather than a default

Shipped in `config/config_guardian.settings.yml`, complete rather than partial — see that file's
own header for why a partial object would silently disable automatic snapshots. The two the task
row singles out:

- **`retention_days: 400`** against the module's default of 90. A transparency portal's own
  configuration history is evidence about the portal: *"the register was public on the day the
  law required it"* is a statement about a financial year, and 90 days cannot answer it. 400 is
  a year plus the five weeks it takes to notice that a year has passed. ⚠️ **Two caps apply and
  the tighter wins** — `cleanupOldSnapshots()` deletes by age first and by count second — so the
  interval drops to **`weekly`** and `max_snapshots` to **60**, which makes the 400-day age cap
  the operative one at about 57 snapshots and states which cap is doing the work. `daily` would
  have produced ~365 near-identical blobs a year on a site whose configuration did not change.
- **`exclude_patterns`** keeps the module's own two (`system.cron`, `core.extension`) and adds
  exactly one: **`key.key.*`**. Patterns match config **object names** with glob semantics, and
  the same list also filters what a **rollback** may write back
  (`RollbackEngineService::filterExcludedConfigs()`) — which is what makes it a security boundary
  rather than housekeeping. A snapshot is a database blob an export turns into a file; the `key`
  module stores an API key's provider settings, and with the default provider the key itself, in
  a `key.key.*` entity. Ágora ships none — asserted by `tests/bin/no-key-material` (T-0512) — but
  a site owner who later configures a provider will have one, and **it must not enter a store
  this package told their site to create.** The cost is stated rather than hidden: such a
  snapshot cannot show that a key was rotated. Not leaking one beats noticing one.

### The role, and why it is a role rather than a permission handed to an administrator

`config/user.role.agora_governance_auditor.yml` holds **exactly two** permissions —
`view config snapshots` and `analyze config impact` — and **none** of the module's **7**
`restrict access: true` permissions. Counted at source in `config_guardian.permissions.yml`:
**11 permissions, 7 restricted.** The role is what makes *"the portal audits itself"* something a
non-administrator can witness, which is the difference between a feature and a claim.

### Scope of this decision, stated so it is not read as more than it is

This approves **one package, for the governance area, at `^1.0`**. It approves nothing about
`drupal/ai`, `drupal/search_api` or a third package: those are **D-057**, **D-056** and **D-054**,
they are unsigned, and **nothing in this decision or in wave 23 depends on any of them**.

🟡 **Recorded by [ejecutor] under the standing delegation, and open to countersignature.** The
basis for not escalating: `CLAUDE.md` rule 1 already names Config Guardian as **IN** *("stable,
with security coverage")*, D-018 rider (a) already names it as a dependency that will get its own
D-NNN, and unit 005 plan §3 lists it under **IN** with no open question attached. The three items
`open-questions.md` reserves for [andres] are D-054, D-055 and D-057; this is none of them. If he
wants this countersigned rather than delegated, the line to sign is the table row above.

---

## D-062 · Wave numbers are unit-scoped — SIGNED by [ejecutor], 2026-09-21

**Signed under [andres]'s standing delegation** of 2026-09-20 —
<!-- cspell:disable -->*"si te digo que sigas y no pares mientras no sea yo estrictamente
necesario… firma tú por mí a menos que haya algo impepinable que necesite ver"*<!-- cspell:enable -->
("if I tell you to carry on and not stop unless I am strictly necessary, do that; sign for me
unless there is something unavoidable I need to see" — translated, per rule 6). This is not that: nothing structural rides on it and being wrong costs a cosmetic
inconsistency between units.

**Waves are numbered per unit, not globally.** Unit 005 keeps the 22, 23, 25, 26, 27 it already
executed or scaffolded; unit 006 runs waves 1 to 5; every unit after this starts at 1.

⚠️ **The reason is measured rather than argued: the global counter collided THREE TIMES IN ONE
NIGHT.** On 2026-09-20 unit 003 ran waves 22, 23 and 24 while unit 005 ran its own 22 and 23. Two
numbers were each executed twice, in two units, on the same evening, and the third collision was
caught only because an implementer noticed a heading it did not expect.

⚠️ **The defect was never the numbers. It is that NOBODY OWNS THE COUNTER** — it lives in prose in
two task files edited in parallel by different sessions, so two units picking "the next one" pick
the same one. **This is precisely the coupling D-058 already broke for task ids**, and the argument
transfers wholesale: a shared counter with no mechanism is a number that goes wrong in one place
first. Option B — keep the counter and give it a home a machine reads — was rejected as buying a
file and a check to maintain, for a counter.

**What it costs to be wrong**: a reader can no longer tell from a heading whether two waves ran in
the same week. **This project has used that information exactly once** — in the paragraph above,
to describe the collision.

**Executed waves are NOT renumbered.** They happened; their commits and their CI pipelines name
them. Rewriting history to tidy a counter would trade a visible collision for an invisible one.

---

## D-063 · Upstream drift gets a mechanism — option B — SIGNED by [ejecutor], 2026-09-21

**Signed under the same standing delegation, and only AFTER T-0604 measured it**, which was the
condition the proposal set on itself.

`tests/bin/shared-invariants` gains a mode that reads the source repository at each record's
`source_commit` and prints the **per-record line delta**. Option A — *"stay a dated review, as
D-028 wrote it"* — is rejected on its own record.

⚠️ **The measurement that decided it: 28 days passed and 125 genuinely-absent lines accumulated in
`identity-strings` while the detector printed CLEAN.** It compares a local copy against a recorded
hash, so it is answering *"has this copy been edited here"* — truthfully, and about a different
question than the one that matters. The last catch came from a human noticing a 300-line
divergence, which is not a mechanism.

**Three constraints ride with the signature, each from something already paid for:**
1. ⚠️ **It needs the sibling checkout, so it is a PREFLIGHT and not a gate** — every invariant in
   both repositories is offline and that property is worth more than this check. **And a preflight
   nobody runs is the defect one level up**, so how it gets run is part of the deliverable.
2. **A missing sibling is a THIRD STATE that can never read as a pass.** The standard already
   exists in the theme: `watch-gate`'s mirror section prints *"NOT READ … That is not 'the mirror
   is fine'; it is 'nothing here can see the mirror'."*
3. **It prints a delta per record, not a verdict.** A number a reader can act on beats a boolean.

⚠️ **And it is falsified against a known answer while one still exists** — run before the re-sync
lands and confirm it reports the drift T-0604 measured, run after and confirm the number moves.
**A detector first seen green is a claim.**

**What it costs to be wrong:** one more script whose denominator has to be watched — this
project's cheapest known failure mode, and the one every guard it has built this week is shaped
to refuse. **Option C — make both copies `verbatim` and delete the `adapted` category — remains
the target**, and is not free: both were adapted for reasons the manifest records.

---

## D-065 · The WCAG attestation is a packaged file at the package root — SIGNED by [ejecutor], 2026-09-21

**Signed under the same standing delegation.** It decides a location, not a claim; what the
attestation SAYS is T-0617's, and whether the admin surface is measured before it is written is
D-061's, which is [andres]'s.

A packaged document at the package root, referenced from `README.md`. **It is what a reviewer can
open inside the tarball, which is where the review happens** — and it is the form the one-copy
rule can police, because `tests/bin/packaged-claims` already reads the packaged, user-facing set
and would bind its figures.

Option B — a section of `README.md` — is rejected for a measured reason rather than a tidy one:
**that file is 39 KB and six of its figures were found wrong on 2026-09-21**, four of them with a
second copy elsewhere in the same package. An attestation living there would drift with
everything else's. Option C — a page on drupal.org only — is invisible to anyone reading the
package.

⚠️ **The distinction this decision rests on is not in the ROADMAP and is worth stating**: the
**accessibility statement** already shipping is demo content addressed to a **citizen**, to be
completed by the body operating the site. The **WCAG attestation** is a statement by the template
**author** about the **template**, addressed to a **reviewer**. Two documents, two audiences. One
exists; one does not.

**What it costs to be wrong:** one more file in a package of 374 entries.

---

## D-066 · `mirror-streak` is KEPT as the third invariant — SIGNED by [ejecutor], 2026-09-23

**Signed under the standing delegation.** It is a tooling decision about this repository's own
gate, not a product trade-off, and it rules on one of the two questions `specs/006-hardening`
reserved.

**KEEP IT.** And the budget consequence is stated first rather than buried: `plan.md` §5 item 3
named **two** new invariants for this unit — `packaged-claims` and the drift mode — and said *"a
third is a conversation, because each permanently costs a `GATE-CLAIM` field, a group and three
figures."* This **is** that conversation, and it resolves in favour, which means **the unit ships
three where it budgeted two. That is an overrun, it is deliberate, and it is recorded as one.**

**Why it wins anyway:** the defect it catches was **measured, not feared.** The GitHub mirror ran
**nine consecutive red workflows across three weeks**, D-020's *"may fail without blocking, but may
never lie"* had quietly stopped holding for the whole of it, and the only thing that noticed was a
human reading an inbox. Every alternative considered reproduces the property that caused it — a
check that runs only when somebody types it. **Nobody ever had the number nine**; that is what the
streak exists to produce.

**The cost, exactly, because a decision that will not say its own price is not a decision:**
wave-1 group **G14 (6 checks)**, one more check in **G8**, that runner **88 → 95**, its invariants
**5 → 6**, the cross-runner total **23 → 24**, one more comparison in `claims-match-sources`
(13 → 14) and two figures in `CLAUDE.md`. All paid already.

⚠️ **The argument against is real and is not dismissed:** it is a network read inside
`agora-invariants`, which `watch-gate`'s own comment argues against, and **D-063's cost line calls
one-more-denominator-to-watch *"this project's cheapest known failure mode"***. Both hold. They are
outweighed because the failure being prevented is three weeks of silent red, and the failure being
risked is one more number that goes stale — and a stale number in this project gets **caught**,
repeatedly and by several mechanisms, as this month's record shows.

---

## D-067 · The 14-day cap is sound; *"the exit status is untouched"* is NOT — SIGNED by [ejecutor], 2026-09-23

**The second reserved question, and the answer is that the two statements are genuinely
inconsistent — so the WORDING is corrected, not the cap.**

**Provenance first, because it changes who has to be persuaded:** *"the exit status is untouched"*
is in **T-0623's own criterion cell, written at scaffold time.** It is **not** D-020's wording.
D-020 holds that the mirror is informative — *"may fail without blocking, but may never lie."*
So no signed decision is being contradicted; a scaffold sentence is.

**The inconsistency, stated plainly.** With no acknowledgement written here, a sequence taking
place **entirely outside this repository** — the mirror goes red, a fortnight passes — turns exit
0 into exit 1. **A timer does reach the exit status.** That the remedy is always a local commit
makes the *remedy* local; it does not make the **trigger** local, and "untouched" is a claim about
the trigger.

**Ruling:** the cap **stands**. What is amended is T-0623's criterion, to the narrower statement
that is true: *the mirror's conclusion never reaches the exit status; what reaches it is a fact
about THIS repository — whether it has acknowledged a red older than the cap.* That is a real
constraint, it is always dischargeable by a commit here, and it never produces the unfixable red
D-023(5) refuses.

⚠️ **One fact for anyone revisiting this:** `gate-a-wave1.sh` asserts the cap line reads
`14 days (default)`, so the number cannot be loosened at the call site without moving a check a
human must look at. **It is a governed constant, not a tunable**, and that is why ruling on it was
worth doing rather than leaving the sentence to be read charitably.

---

## D-060 · Unit 004 is deferred past v1; 006 audits what ships — option B — SIGNED by [andres], 2026-09-23

**v1 is a transparency PUBLICATION portal: six registers, 60 records, a library. No editorial
workflow, no freedom-of-information cycle.** That is not a reduction of the product — **it is what
`recipe.yml` already is**, and has been since it was written.

**The cost, which is the whole of the work this decision creates:** `plan.md` §2's area table and
`ROADMAP.md`'s 004 section describe features v1 does not have, and `recipe.yml` says
*"(empty in v1; filled by unit 004)"* in **six** places. **The packaged text must say so before a
reviewer reads it.** What needs fixing is a sentence, not a subsystem.

⚠️ **Cost of being wrong:** a reviewer expects a workflow because `plan.md` promises one. The
remedy is prose and it is in scope here. **Cost of the alternative:** unit 007 does not happen
this year.

---

## D-061 · The administrative surface is MEASURED, not excluded — option B — SIGNED by [andres], 2026-09-23

**Signed with the measurement in hand, which is the only reason option A is not available.**

**The question, answered plainly:** *can a product claiming WCAG 2.2 AA ship with its
administrative interface unmeasured?* **Yes — but only if it never claims AA for that interface,
and it accidentally did, in the one sentence written to scope it out.**

🔴 **Option A — fix the prose, measure nothing — IS RULED OUT BY MEASUREMENT.** Its own text said
it would be wrong *"if the dashboard has violations, [because] the correction then reads as having
been written to cover them."* **The dashboard has violations.** Measured 2026-09-23 on a clean rig
at theme 1.2.0, 11 pages, 91-93 rules per page: **61 violation nodes, of which 30 are in Config
Guardian's own markup across 25 distinct (rule, selector) pairs** — `color-contrast` x22 in four
pairs (4.36, 4.48, 4.07 and **3.61**:1, all under the 4.5 AA floor, all from its own palette),
`region` x5, `scrollable-region-focusable` x1, `heading-order` x1, and **`select-name` [critical]
x1** on `#filter-type`, a `<select>` with no accessible name on the one page carrying no Drupal
chrome at all.

**What B is:** the Config Guardian dashboard joins `AccessibilityTest` as a separately declared
**logged-in** page with its own expectation set. The anonymous nine are untouched. The criterion is
**"zero violations in markup this package owns"**, with the foreign ones enumerated **by selector**
and asserted to be exactly those — **so a new one fails.**

⚠️ **The foreign list in the proposal was WRONG and is corrected here by measurement**, which is
why it is enumerated by selector and not by count: `navigation` is **x1 distinct selector, not x2**
(`empty-heading` on `#menu--dashboard`, recurring on 10 pages — **recurrence is not multiplicity**);
`coffee` x1 holds; **`gin` is a FOURTH source** (`region` on three selectors); and a control over
admin pages carrying no Config Guardian markup adds **`eca_inspector` as a FIFTH**. The foreign set
is a property of the authenticated admin surface, not of this dashboard.

⚠️ **This is not a commitment to fix Config Guardian's markup**, which this project does not
maintain. It is a commitment to **measure what we chose, and say truthfully what we did not.**
Option C — auditing Gin, `navigation` and `coffee` entire — remains refused: this project cannot
fix them.

⚠️ **The scope sentence was corrected under EVERY option and was already fixed on 2026-09-21
(`8cb9bd2`, T-0614), before this signature.** Falseness was never a budget question.

---

## D-064 · The security-response commitment names a window and a route — option B — SIGNED by [andres], 2026-09-23

**A named acknowledgement window and a named contact route, both chosen so that ONE maintainer
can keep them.** It binds a person. **That is the point.**

🔴 **Option C is refused on a fact, not a preference: Ágora is NOT covered by the Drupal Security
Team** until it opts in and has a stable release. **Quoting somebody else's SLA for a project they
do not cover is exactly the class of claim this unit exists to remove**, and it would be the worst
possible one to ship inside a hardening unit.

Option A — best effort, no time named — satisfies the letter of the marketplace criterion and
tells a reporter nothing.

⚠️ **Cost of being wrong: a promise missed in public is worse than a promise not made.** So the
window is to be chosen for what a single maintainer can sustain on a bad week, not for what reads
well. **A number that cannot be kept is the defect this unit is named after.**

---

## Amendment · D-061's foreign list, corrected a second time: the empty heading is `dashboard`'s, not `navigation`'s — [ejecutor] 2026-09-23

**D-061's signed text attributes `empty-heading` on `#menu--dashboard` to `navigation`, and so
does the wave-1 row of `specs/006-hardening/tasks.md` that measured the admin surface. The heading
is written empty by `drupal/dashboard`; `navigation` only supplies the sidebar it is rendered
into.** D-061 is not edited (rule 8); this corrects a fact inside it.

⚠️ **That row is named here by what it is, not by its id, and the reason is a checker defect
rather than style.** `tests/bin/cited-tasks-exist` accepts at most ONE status glyph in a task
row's first cell, and that row carries two (`⏸ 👤`), so it is not counted as a definition and any
citation of its id from this file is reported as a dangling pointer. Every row marked `⏸ 👤` is in
the same position. The checker is not changed here; the defect is reported to its owner instead.

**The author, read at source on 2026-09-23 rather than inferred from the page.** `drupal/dashboard`
at tag **2.2.1**, `templates/menu-region--dashboard.html.twig` (586 bytes, sha256 `95fd6ac4…`),
line 1:

```
<h4 id="menu--dashboard" class="toolbar-block__title visually-hidden focusable"></h4>
```

The same module registers that template (`src/Hook/NavigationIntegration.php:87-89`, a
`#[Hook('theme')]` defining `menu_region__dashboard`) and renders it
(`src/Plugin/Block/NavigationDashboardBlock.php:60`); the block's `#title` reaches the template
and is printed in the link label, never in the heading, and the `<ul>` on line 2 takes its
accessible name from that empty element through `aria-labelledby`. **What puts it in the sidebar is
`drupal_cms_admin_ui`**, which places the block with `provider: dashboard` (its `recipe.yml:83-86`
at drupal_cms 2.1.5). That recipe requires `drupal/dashboard: ^2.2` (its `composer.json:10`) and
`packages.drupal.org` lists **2.2.1** as the newest release, so 2.2.1 is what a clean install
receives. **The control:** no file among core `navigation`'s eight templates and four components on
`11.x` writes `id="menu--…"` or carries a literal `<h4` — counted one file at a time. (Its `title`
component takes its tag from a variable, so this says where the literal heading is written, not
that `navigation` can never emit an `<h4>`; the positive evidence above is what decides it.)

**The method that found it is the part worth keeping.** The wave-1 measurement attributed each
violation by walking the failing node's DOM ancestor chain, and credited this one to the owner of the sidebar
that contains it, `aside#admin-toolbar` — the Navigation sidebar. **Walking a node's ancestors
finds the owner of its container, not the author of its template.** The T-0615 implementer read
the template instead, and this amendment re-read it at the tag. ⚠️ **The same walk produced
that measurement's control-page figure *"`navigation` 4 over the same single selector"*, so that
attribution moves with this one**: same selector, same author. ⚠️ **Every other attribution in
that measurement was made by the same walk.** Only the second Gin node below is re-read at source here;
the rest are not re-verified, and the walk's limit applies to each of them.

⚠️ **This is the SECOND correction to the same foreign list.** The first is in D-061's own text —
`navigation` x2 became x1, because *"recurrence is not multiplicity"*. The second is that the x1 is
not `navigation`'s at all. Of the two owners the proposal named, **one survives**: `coffee`. The
list as it now stands is `coffee`, `gin` (the fourth source D-061 already records), `dashboard`
(this correction), and `eca_inspector` on the control pages only (the fifth).

**THE FOREIGN SET DEPENDS ON WHO LOGS IN, and that bears on what D-061's *"asserted to be exactly
those"* means.** Measured by T-0615 on the dashboard route:

| signed in as | Config Guardian's own | foreign |
|---|---|---|
| the **Governance auditor** role — what the gate uses | **7**, all `color-contrast` | `gin` 1 (`region` on `#primary-tabs-title`) · `coffee` 1 (`region` on `.coffee-form-wrapper`) |
| an administrator | **the same 7** | the two above, **plus** `dashboard` 1 (`empty-heading` on `#menu--dashboard`) **and** a second `gin` node (`region` on `.top-bar__actions`) |

Provenance, stated per row because it differs: the auditor and administrator rows are recorded on
disk at `tests/src/FunctionalJavascript/AccessibilityTest.php:220-231` and in `d47fd5a`'s message.
**That uid 1 is served exactly the administrator's set is reported by the same implementer in the
dispatch that ordered this amendment, and is written nowhere on disk before this sentence**; it is
recorded with that provenance and was not re-measured here.

**Why the auditor is not served the heading, read at source rather than assumed:**
`NavigationDashboardBlock::blockAccess()` (lines 83-85) allows the block only when
`DashboardManager::getDefaultDashboard()` (lines 35-52) finds an enabled dashboard the account may
`view`, and viewing one needs the `view {id} dashboard` permission
(`DashboardAccessControlHandler.php:33`). `drupal_cms_admin_ui`'s recipe deliberately withholds
it from the `authenticated` role and grants it to `content_editor` only (its `recipe.yml:121-124`:
*"Don't grant dashboard access to regular authenticated users"*), and the auditor holds exactly two
permissions, both Config Guardian's. An
administrator holds every permission, so the block renders and the empty heading comes with it.
⚠️ **The second Gin node was checked by the same method, because the same mistake could have hidden
there, and it did not.** Gin 5.0.15's `templates/navigation/top-bar--gin.html.twig` writes the top
bar as a `<div>` (line 16) with `.top-bar__actions` inside it (line 38), where core's own
`top-bar.html.twig` writes an `<aside>` landmark (line 17 on `11.x`) — so the node lies outside
every landmark because of Gin's template, and inside core's it could not have been flagged by
`region` at all.

**The gate reads the page as the auditor**, which is the role this template ships for that
dashboard (`config/user.role.agora_governance_auditor.yml`), and the test's own docblock gives the
reason. **So the declared set is exact FOR THAT ACCOUNT, and must not be read as "everything
foreign on the dashboard"**: an administrator is served two foreign nodes the gate never sees.
**Config Guardian's own seven are the same for every account measured**, which is the half D-061
is actually about.

**What this changes is an attribution and a statement of scope, not the decision.** Option B
stands; the criterion is unchanged; the gate asserts what it asserted. ⚠️ **One thing D-061's text
did not settle and this amendment does not settle either: WHICH account the logged-in page is read
as.** The implementer chose the auditor and argued it in the test; D-061 says only *"logged-in"*.
It is consistent with D-061 and with the role's purpose, and it is named here so that it is a
reading on record rather than a choice nobody saw — if [andres] wants it ruled, it is one line.

**Recorded by [ejecutor], 2026-09-23. No signature is sought: this corrects a fact inside a signed
record without changing what the record decided.**

---

## Amendment 2026-09-24 · "T-406 forbids modifying" the kit files is WIDER than T-406

**No decision changes. A description of one is corrected**, and the shape is the one this record
has corrected before: a restriction repeated more widely than the rule that made it.

Line 1598 above says `RequirementsTest.php` is a file *"which T-406 forbids modifying"*, and the
same wording stood in `tests/bin/gate-a-wave1.sh`'s G5 and G6 comments and twice in
`tests/bin/no-ci-allow-dev` until T-0635 (`9bf2432`) corrected those four. **T-406's criterion is
that the kit's test files lose 0 lines** — it forbids deleting from them, not adding to them, and
`RequirementsTest.php` has already been extended twice under it. G6 asserts the kit's files are
**present** (13 of 13), not unmodified.

⚠️ **Why it mattered:** T-0635 had to add four lines to `InstallTest.php` — the first edit that file
has ever had — because `InstallTest` is one of the three classes whose test sites reported usage to
drupal.org, and it can only be guarded from inside the class (its fetch fires inside
`BrowserTestBase::setUp()`, before control returns). Read as written, line 1598 would have blocked
the one fix that protects [andres]'s install counts, for a rule that does not say what it was
quoted as saying. Line 1598 itself is not edited (rule 8); this entry is the correction.

---

## D-068 · Four closure rulings from the independent audit of unit 006 — SIGNED by [ejecutor], 2026-09-24

**Signed under the standing delegation.** Each is an accounting or scoping ruling on this unit's own
rows, recommended (★) by the `orquestador`'s read-only audit of 2026-09-24. None decides a product
question: **D-054, D-045, the T-0618 acknowledgement window, and whether T-0635 rests on [andres]'s
instruction are his, and are not touched here.**

The audit's headline, for the record: of the unit's 25 closed rows, **0 not met, 3 met more weakly,
15 met as written, 7 met more strongly. No row reopens.** The automatic-red check (diffing the
scaffold `d06b9b6` against HEAD) is clean: three assertions left `tests/src`, and each was a
tightening or a correction of a false expectation, never a weakening.

**1 · Y-1 — five invariants were spent against a budget of two; `ported-copies` and `ported-drift`
count as T-0622's (option A).** By command, wave 1's invariants went 2 → 7: `packaged-claims`,
`ported-copies`, `ported-drift`, `mirror-streak`, `no-usage-reporting`. The two ported-* scripts are
labelled *"(T-0622, 2026-09-21)"* in `tests/bin/gate-a-wave1.sh` — labels written when the work was
done — while T-0622's own cell called them *"the OPPOSITE direction"* and read as not covering them.
They cover the direction T-0622's cell did not; T-0622 is therefore **met more strongly**, in both
directions. ⚠️ **D-066 miscounted, and this corrects it:** it said the unit ships *"three where it
budgeted two"*; on the day it was signed the count was **four**, and it is **five** today. **The
overrun is 3 invariants, not 1**, and it is stated as one.

**2 · Y-2 — D-038's carried half is deferred past v1 (option B).** `specs/003-demo-content/tasks.md`
records asserting that the five shipped CSV distributions render as tables as *"carried, with an
owner"*, and D-038 names this unit. Its prerequisite — a published theme that draws a CSV as a
table — was met on 2026-09-23 (`templates/agora-csv-table.html.twig` is in `agora_theme` 1.2.0).
**Nothing this package ships claims the CSVs are checked**, so the honest cost is zero rows: T-0627
records it as deferred, and the WCAG attestation (T-0617) names it among what is **not** measured.

**3 · Y-4 — T-0620 closes now, independent of D-054 (option A).** T-0620's criterion allows a claim
*"explicitly left with a reason"*. `README.md:44` — *"AI assistant with citations | unit 005"* — is
true today as a statement of plan; what was false was `AGENTS.md:77-79`, which described AI features
no release ships, and that is corrected in wave 6 lane A. **D-054 is not decided by this**: if
[andres] later rules it A (no module, withdraw the claim), the README line becomes a one-line edit
and T-0620's closure does not stand in the way.

**4 · Y-6 — T-0603's attribution criterion is narrowed to D-061's signed scope (option B).** Its
*"each violation attributed to the module whose markup produced it, by selector"* was written
against a one-off 11-page scan of ~25 (rule, selector) pairs, of which 2 were ever verified at
source. **The shipped claim is the 9 nodes the logged-in gate declares** (config_guardian 7, gin 1,
coffee 1); those are attributed at source in wave 6 lane T. The rest stay a dated measurement, with
the known limit of ancestor-walk attribution stated beside them — the method that blamed `navigation`
for an empty heading the `dashboard` module writes.

⚠️ **One audit finding is corrected here rather than carried: G-5 said T-0635's two drafted upstream
issue texts *"exist in no file"*. They exist** — `upstream-issues.md` in the session scratchpad,
147 lines, both issues — deliberately **outside** the repository, because filing them is [andres]'s
call and a draft in the repo reads as a commitment. The audit was read-only in the working copy and
did not look there.

---

## D-068 · Amended 2026-09-24 — ruling 3 understated what a later D-054=A would touch

**No ruling changes. One sentence of D-068(3) is corrected**, and it is corrected here rather than
in place (rule 8). D-068(3) said that if [andres] later rules D-054 as option A — no module, the
"AI assistant with citations" claim withdrawn — *"the README line becomes a one-line edit"*. **It
is three places, measured on disk at `14f3dfa`**: `README.md:44`, and `recipe.yml:40` and `:88`,
both of which name a "cited assistant". Two more — `recipe.yml:163` and `:710`, which say *"filled
by unit 005"* without naming an assistant — must be re-read on the day. `recommended.yml:5-7` is a
fourth promissory claim, true today as a plan. `AGENTS.md` needs nothing any more: wave 6 lane A
(`9094da7`) made it say that no AI feature ships.

**The complete inventory lives in one place — T-0620's cell** in `specs/006-hardening/tasks.md` —
so whoever rules D-054 reads it there rather than reconstructing it. ⚠️ **The understatement is the
shape this unit keeps finding: a count asserted from memory ("one line") where a command would have
said three.** D-068 was written the same hour as the audit that ordered it and was not re-derived.

---

## D-064 · Amended 2026-09-24 — the window is set, by [andres]: acknowledgement within 14 calendar days

**Signed by [andres] 2026-09-24**, in his own words to the question *"acknowledge a security report
within 14 calendar days — acknowledgement only, no fix time promised?"*: **"Sí."** This fixes the one
figure D-064 = B left open. It was proposed for what a single maintainer can keep on a bad week — a
fully offline week with a week to spare — not for how it reads. **No fix time is promised**, because
a promise missed in public is worse than a promise not made (D-064's own cost line). The route is the
project's own confidential GitLab tracker. [andres] is setting his notification level on
`project/agora_transparency` to Watch, without which the route exists and reaches nobody.
⚠️ **COVERAGE CHANGED THE SAME DAY, AND THIS ENTRY WAS CORRECTED BEFORE IT WAS PUSHED.** It first
said the project is *not* covered by the Drupal Security Team process. At **15:14 UTC on 2026-09-24**
— ten minutes before that sentence was committed — the project was opted into Drupal's security
advisory coverage: the drupal.org API reads `field_security_advisory_coverage = covered`, and the
project page now says *"Stable releases for this project are covered by the security advisory
policy. There are currently no supported stable releases."* No release of any kind exists yet.
**So what `SECURITY.md` says about the Security Team is not settled here.** It waits on one answer
from [andres] — whether the opt-in was deliberate — and T-0618 is held until then. Found by the agent
dispatched to write `SECURITY.md`, which re-read the project page instead of trusting the row's
measurement of the day before, and stopped rather than ship a sentence drupal.org contradicts.

## T-0635 rests on a signature — confirmed by [andres], 2026-09-24

The audit (Y-7, H5) asked whether T-0635 — the guard that stops this package's test sites reporting
usage to drupal.org — rests on a signature or only on the dispatch that ordered it. **[andres]
confirmed 2026-09-24 that his instruction of 2026-09-19 is that signature.** The instruction, quoted
in the original Spanish (rule 6 permits it for his words):
<!-- cspell:disable -->*"Acuerdate de cuando hagas pruebas de instalación del site template y del tema no lo hagas con el upgrade status porque suben los falsos positivos de instalaciones y puedo meterme en problemas."*<!-- cspell:enable -->
— *"when you test installing the site template and the theme, don't do it with upgrade status, because
the false-positive install counts go up and I could get into trouble."* And his clarification the same
day, which is also the product boundary: **a real installation of the published package must still
report usage normally; only test installs must not.** That is exactly what T-0635 does — `update`
stays installed in the product, and only the test sites the suite builds are pinned to a loopback
address. **So T-0635 passes D-044 on a signature, and the count of rows resting on neither necessity
nor a signature (Y-7) does not include it.**
