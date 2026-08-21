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

> After the research `specs/001-fundacion/research/2026-08-20-estado-del-arte.md`.
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
  (b) For `DECISIONES.md` and `IDIOMS.md` the translation **does not violate append-only**: the whole
      file is translated with a header note (date, *"semantic content unchanged"*), and the Spanish
      original is preserved in git history as the signed record.
  (c) **New entries are written directly in English** from this decision onward.
  ⚠️ Operational consequence (I-008): the three subagent definitions under `.claude/agents/` freeze
  at session start. After translating them, the session **must be restarted** before relying on them.

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

- **On the specification corrections found by the `tester` in wave 1, 2026-08-21.** All three adopted:
  1. **T-209** is specified as *"`CI_ALLOW_DEV` is not **defined** in any versioned file"*, never
     *"not mentioned"* — the string legitimately lives in `tests/src/Kernel/RequirementsTest.php:55`,
     which T-406 forbids modifying.
  2. **T-103** must account for the **three** `_comment` occurrences in `composer.json`.
  3. **`ValidationTest.php`** is added to the set of kit files watched by the gate.
