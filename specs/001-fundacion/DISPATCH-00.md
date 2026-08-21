DISPATCH · Ágora · 001-fundación / Turn 00 — Scaffolding: research + plan, NO CODE
Pure scaffolding turn. Baseline: repo freshly initialised with this kit. NO code is written, the
project is NOT created on Drupal.org, NOTHING is installed yet beyond what is needed to verify. NO tag.

═══════════════════════════════════════════
CONTEXT
═══════════════════════════════════════════
Ágora is a Site Template for Drupal CMS (transparency portal) destined for the Drupal.org Site
Template Marketplace. Identity, recipe architecture and marketplace constraints are encoded in
`specs/000-proyecto/plan.md` and `CLAUDE.md` — read them first; they override this prompt.
Decisions already signed by the human (do not reopen): start from the official **Drupal CMS Site
Template Starter Kit**; development on **git.drupalcode.org** as a general project (GitHub mirror
optional afterwards); only stable dependencies with security coverage; Config Guardian in, Midgard out;
pnpm/composer/DDEV. This turn exists because we are building on young technology (Drupal CMS, Canvas,
site templates) and the state of the art MUST be verified fresh before fixing plan and tasks.

═══════════════════════════════════════════
RECONCILIATION PASS — MANDATORY, UP FRONT, NO CODE
═══════════════════════════════════════════
1. Read `CLAUDE.md`, `specs/000-proyecto/plan.md`, `specs/000-proyecto/DECISIONES.md`. Tie everything
   you produce to this framework. Verify ON DISK the next free D-NNN number before proposing
   new decisions.
2. FRESH ground truth on the web (all dated, with URL, saved in `specs/001-fundacion/research/`):
   a. Current stable version of **Drupal CMS** and status of **Drupal Canvas** (what does a
      "Canvas-compatible" theme require today? is there a reference/starter theme?).
   b. The **Drupal CMS Site Template Starter Kit** (project drupal_cms_site_template_base on
      Drupal.org): real structure, what CI it ships (GitLab CI and GitHub Actions), which base recipes
      it includes, how Tugboat and Project Browser are integrated.
   c. The real **share template** flow (new.drupal.org/site-template/share) and the marketplace
      requirements in force (new.drupal.org/site-template/apply): confirm they still are as
      plan.md §4 encodes them; if they diverge → report it (disk/plan is reconciled, not forced).
   d. `drush site:export` from the **Drupal CMS Helper**: status, known limitations.
   e. **Drupal.org gitlab_templates**: exact jobs available today (phpcs, phpstan, cspell,
      eslint, stylelint, phpunit…), how they are referenced, and whether a job/pattern exists to test
      recipe installability. Feasibility of Playwright+axe in drupalcode CI (do the runners
      support it? or do the visual tests go to the mirror's GitHub Actions?).
   f. Availability of the machine name on Drupal.org: "agora" probably taken; check
      alternatives (e.g. agora_transparency, agora_gov) WITHOUT creating anything.
   g. SBOM candidate modules (facets/search, webform, ECA, AI): for each one, current stable
      version, compatibility with the core Drupal CMS uses today and security coverage
      status. Whatever does not comply → out, with an alternative inside Drupal CMS if one exists.
3. Deliverables of the pass: `research/2026-XX-XX-estado-del-arte.md` (dated, with sources) +
   report of divergences with respect to plan.md/this prompt.

STOPPING RULES:
  ▸ The marketplace requirements have changed with respect to plan.md §4 → ESCALATE with the exact diff.
  ▸ The Starter Kit imposes a structure incompatible with the recipe architecture of plan.md §2
    → ESCALATE with options (adapt architecture vs do not use starter kit), do NOT decide alone.
  ▸ Canvas requires unstable releases for something essential → ESCALATE (clashes with non-negotiable no. 1).
  ▸ An SBOM candidate has no security coverage and there is no reasonable alternative → ESCALATE.
  ▸ Anything this prompt assumes and the state of the art contradicts → the real state wins;
    report it, do not force it.

═══════════════════════════════════════════
SCOPE (YES / NO)
═══════════════════════════════════════════
YES: dated research · `specs/001-fundacion/plan.md` (unit objective: skeleton from starter
kit + green CI while empty + recipe structure created but minimal) · `specs/001-fundacion/tasks.md`
(numbered tasks, waves, A/B gates per wave) · open questions with options + recommendation ·
proposal for the first invariant scripts (`tests/bin/`: no-unstable-deps, no-patches,
no-secrets, sbom-check) AS A SPECIFICATION, not implemented.
NO: code · site installation · creation of the project on Drupal.org (unit 007, human) ·
visual design of the theme (unit 002) · load-bearing decisions closed on your own · tag/release.

═══════════════════════════════════════════
NON-NEGOTIABLE RULES
═══════════════════════════════════════════
The 10 in CLAUDE.md apply in full. Reminder of those this turn may brush against: stable only,
no patches; secrets never in the repo; process docs in Spanish, identifiers in English; pnpm
exclusively if any JS tooling shows up in research; commits without AI co-authorship; append-only.

═══════════════════════════════════════════
GATE A · GATE B
═══════════════════════════════════════════
Gate A for this turn (docs-only): the three deliverables exist on disk, the research has a date and
sources, tasks.md has A/B gates defined per wave with concrete commands.
Gate B (human): Andrés reads plan.md + open questions and signs the new D-NNN decisions in
DECISIONES.md. You prepare the EXACT list of decisions: one per point, options, recommendation
marked, in plain language.

═══════════════════════════════════════════
EXECUTION ORDER
═══════════════════════════════════════════
1) Reconciliation (disk reads + dated web research). 2) Delegate to the `orquestador` subagent
the drafting of the unit's research.md, plan.md and tasks.md and the open questions + D-NNN proposals
(this turn does NOT invoke `desarrollador` or `tester`: it is scaffolding, no code). 3) The main
session integrates and persists the deliverables on disk. 4) HOLD: report to the human with the
CLAUDE.md format (reconciliation → deliverables → escalations 🔴🟡🟢 → pending decisions). Do NOT
advance to implementation until signature.
