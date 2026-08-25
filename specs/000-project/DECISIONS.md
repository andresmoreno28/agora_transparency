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
