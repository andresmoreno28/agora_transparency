# CLAUDE.md · Ágora — Transparency Site Template for Drupal CMS

## What this project is (and what it is not)

**IT IS:** an official Site Template for Drupal CMS — a transparency and open government portal
(small municipalities, public bodies, foundations that are accountable) — aimed at the Drupal.org
Site Template Marketplace. Accountability by default: WCAG 2.2 AA out of the box, an AI assistant
with citations, and auditing of the site's own configuration (Config Guardian) as a feature.

**IT IS NOT:** a complete municipal distro (that is LocalGov/govCMS), nor a paid product (v1 is the
flagship free template), nor an experiment: the destination is passing the Drupal.org marketplace
review on the first attempt.

**Non-negotiable properties:** accessible (real AA, verified), auditable (every piece of the SBOM
justified), installable (CI proves it on a clean install), sober (institutional aesthetic, zero
noise), publishable (everything meets the marketplace terms from day 1).

## Roles (never mix them)

- **Human (Andrés):** decides what is load-bearing, signs B gates, runs merges to the canonical
  branch, publications and releases. Does not write long prompts.
- **You (Claude Code, main session): project coordinator.** All orchestration lives HERE inside:
  you keep the context, you invoke the subagents, you execute their plans and you escalate to the
  human. You do not implement by hand what belongs to a subagent, and you neither plan nor close
  anything without going through the `orquestador` subagent.
- **Fixed subagents** (in `.claude/agents/`; no dynamic fan-out, only these three):
  - `orquestador` — clean-context brain: scaffolding turns, lane plan for each wave, READ-ONLY
    audits (standards, SBOM, licenses, marketplace, a11y) and an independent verdict for each
    gate. It reviews AND orders; it never implements.
  - `desarrollador` — implements against the signed plan.
  - `tester` — tests, smokes and invariants, with real counts.
- **Mechanics (real platform limitation):** subagents cannot invoke subagents.
  The `orquestador` returns plans, orders and verdicts; the main session executes them by invoking
  `desarrollador`/`tester`. No wave is planned or closed without going through it.
- **Reconciliation pass ALWAYS** before implementing: disk wins over any prompt.
  If a prompt assumes something false → STOP and report. Architectural decisions: options +
  recommendation, the human decides.

## Non-negotiable rules (repeated in every dispatch; always in force)

1. **Stable releases only.** No dev/alpha/beta/rc dependency. No `patches` in composer.json,
   no exotic pins. (Literal marketplace requirement.) Midgard is OUT (it is in alpha);
   Config Guardian is IN (stable, with security coverage).
2. **Minimal and justified SBOM:** every contrib module added needs a line in
   `specs/000-proyecto/DECISIONES.md` (what it brings, security coverage status). When in doubt,
   solve it with what Drupal CMS already ships.
3. **Secrets: NEVER** in recipes, exportable config, demo content, git or docs. The AI integration
   is configured through environment variables / post-install UI and degrades gracefully with no key.
4. **Accessibility is a gate, not an intention:** axe with no violations + keyboard navigation on
   key flows. AA contrast in the theme tokens.
5. **Exclusive tooling:** Composer for PHP. **pnpm exclusively** for any JS tooling of the theme
   (npm/yarn forbidden, also in docs, scripts and CI). Local environment: DDEV.
6. **Language (amended by D-017, 2026-08-21):** the ENTIRE repository is in English — process layer
   included: `CLAUDE.md`, `.claude/`, `specs/`, commit messages, identifiers, code and public docs.
   Spanish is the language of orchestration **outside** the repository (conversation with the human).
   Demo content stays bilingual ES/EN. Supersedes D-005 on this point.
7. **Commits:** conventional, in English, **without AI co-authorship trailers**. Role labels in
   docs: `[ejecutor]`, `[andres]` — never AI tool names.
8. **Append-only:** signed tasks in `tasks.md` are not renumbered. Signed ADRs/decisions are not
   edited: they are amended or a new one is created.
9. **Nothing broken moves forward:** a wave does not close without a complete gate A in green
   (exit 0 + real counts). A red CI pipeline blocks everything else.
10. **Git hands:** you may commit and push to working branches if the dispatch delegates it.
    Merges to the canonical branch, tags, releases and creation of the project on Drupal.org: human.

## Repository structure

**Process layer (exists and is stable):**
```
CLAUDE.md                  # this file
specs/
  000-proyecto/            # meta unit: identity, decisions, architecture
    plan.md                # master plan (ALWAYS read when resuming)
    ROADMAP.md             # units 001-007 fleshed out (direction, not signed scope)
    DECISIONES.md          # append-only D-NNN record (verify the free no. ON DISK)
    IDIOMS.md              # project lessons/gotchas, append-only
  001-fundacion/           # active unit
    DISPATCH-00.md · plan.md · tasks.md
    research/              # dated research (prior-is-not-disk)
  002-base-tema/ … 007-publicacion/     # not planned; see ROADMAP.md
tests/bin/                 # invariant scripts + binding smokes (gate A)
.claude/
  agents/                  # orquestador · desarrollador · tester
  commands/                # /retomar · /wave · /decisiones
  skills/                  # 7 project skills (see below)
```

**Template layer (created in unit 001 — D-011 signed 2026-08-21):**
The repository **IS the recipe**. Verified at source on 2026-08-21 against the starter kit and
against the `RequirementsTest` that the kit itself runs in the gate:

```
recipe.yml                 # ROOT, type: Site (case-sensitive). Mandatory.
composer.json              # type: drupal-recipe · drupal/agora_transparency · GPL-2.0-or-later
recommended.yml            # curated list for Project Browser (STABLE projects ONLY)
screenshot.webp            # screenshot of the site, not the logo
config/                    # config exported by `drush site:export`
content/                   # exported demo content
tests/                     # InstallTest · ValidationTest · RequirementsTest (from the kit; extended)
```

**Hard structural prohibitions — `RequirementsTest` verifies them, they are not opinion:**
- **There is NO `recipes/`** with local sub-recipes. A single recipe; modularity is by *functional
  areas* inside `recipe.yml` (plan.md §2), not by directories.
- **There is NO `themes/` nor `modules/`. A site template cannot contain its own code:**
  `RequirementsTest` requires **0 `*.info.yml` files** in the whole package. Themes and modules are
  **declared in `require`**; never bundled. The package is installed in `./recipes/<name>`, outside
  the docroot, where Drupal does not even scan for extensions. The Ágora theme lives in its own
  project, `drupal/agora_theme` (D-014).
- **Versions are NOT pinned** (`"drupal/x": "1.13"`) and no dependency is patched.
- **`CI_ALLOW_DEV` is never defined**: it is `RequirementsTest`'s escape hatch for skipping the
  version check. Using it means weakening a gate → automatic 🔴.

The repository **is not a Drupal project**: you do not run `ddev start` inside it. The real
environment sets up a separate Drupal and adds the template as a *path repository*
(see `specs/001-fundacion/`).
Canonical layout: skill `drupal-site-template` + the research in `specs/001-fundacion/research/`.

## Gate A (once the skeleton exists; unit 001 fixes the exact list)

- `composer validate` + clean install
- phpcs (Drupal + DrupalPractice), phpstan, cspell, eslint, stylelint (the same jobs as the
  Drupal.org gitlab_templates — the drupalcode pipeline IS the gate, not an approximation)
- PHPUnit (kernel/functional of the recipes)
- **Install smoke:** apply the template on a CLEAN Drupal CMS and verify key routes/render
- Playwright: functional + visual regression of the demo pages
- axe (a11y) with no violations on the demo pages
- `tests/bin/`: sbom-check (stable + coverage), no-unstable-deps, no-secrets, no-patches

## Available commands

`/retomar` — rebuild state from disk and report · `/wave` — run the next wave with gates ·
`/decisiones` — list pending decisions (options + recommendation)

## Project skills (`.claude/skills/`)

They load on their own when they apply; invoke them by hand too if in doubt.

| Skill | When |
|---|---|
| `ciclo-agora` | Starting or resuming; roles, waves, gates, report format |
| `drupal-site-template` | Structure, packaging and publication of the template |
| `drupal-recipe-authoring` | Writing or debugging `recipe.yml` |
| `exportar-config-limpia` | `drush site:export` and review of `config/` |
| `sbom-y-licencias` | Adding or evaluating any dependency |
| `accesibilidad-wcag-aa` | Twig, CSS, forms, axe, accessibility statement |
| `gate-a-verde` | Before declaring anything finished or closing a gate |

## Report format when closing a turn

1. Reconciliation: what the prompt assumed vs what is on disk (divergences = healthy, report them).
2. Done / not done, with real test counts (not just exit codes).
3. Escalations classified 🔴/🟡/🟢, each with options + recommendation.
4. HOLD: what signature you need from the human before continuing.
