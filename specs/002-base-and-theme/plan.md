# Unit 002 · Base and theme — content model, Canvas, and a real accessibility gate

> Scaffolded [ejecutor] 2026-08-24, against
> `research/2026-08-24-canvas-theme-and-cross-repo-gates.md`.
> Unit 001 is CLOSED and signed. This plan diverges from the ROADMAP where the research
> says it must; every divergence is named in §2.

## 1 · Objective

At the end of unit 002 there are **two repositories that hold each other up**:

- `drupal/agora_theme` — a sober, accessible, Canvas-usable theme with AA tokens, one
  self-hosted OFL typeface, accessible table and form templates, and a **blocking
  accessibility gate that actually runs a browser**.
- `drupal/agora_transparency` — carrying the transparency content model, its taxonomies,
  roles, permissions and base views, with the **atomic swap done**: no
  `extra.drupal-site-template`, no `blank`, the theme in `require` and in `system.theme`.

And one thing that does not exist yet anywhere in the project: **an accessibility check
that can fail.** Today rule 4 says "accessibility is a gate, not an intention" and there is
no job on either surface that could turn red for an a11y reason. Unit 002's real product
is that job. Everything else is what the job needs in order to have something to look at.

## 2 · What changed against the ROADMAP

The ROADMAP's unit 002 was written 2026-08-20, before `drupal/agora_theme` existed and
before the Nightwatch mechanics were read at source. Six changes, each with its reason.

| ROADMAP said | Now | Why |
|---|---|---|
| One unit, implicitly one repository | **Two repositories, and the seam between them is scoped work, not overhead** | D-014=B, and the theme project was created 2026-08-24 |
| Gate A names `stylelint` | `stylelint` and `twig-cs-fixer` belong to the **theme** gate; the template gate has neither and may never | Both gate on `exists: **/*.css` / `**/*.twig`; the template package contains no CSS and no Twig (research R-7) |
| "axe with no violations on the templates", surface unstated | The axe gate **must** live in the theme repository | A recipe project is installed outside the docroot; Nightwatch's glob is rooted at the docroot, so the template repo's Nightwatch tests would be found by CI and **not collected by the harness** (research R-3c). Amends D-009 → D-027 |
| "Content types: Document, Position/Person, Contract, Budget line, Public call" — five | **Three** proposed: Document, Person, Dataset, with a `document type` taxonomy | Five node types is five editorial UIs, five accessible-table templates and five sets of demo content, paid twice in units 003 and 006. Opened as **D-026**, not decided here |
| "Decide and apply the theme approach (D-008)" | Already decided (D-014=B). What is left is the **atomic swap**, which carries T-106's deferred debt | D-008 subsumed 2026-08-21 |
| Install smoke was informative only (D-020) | Proposed to become a **blocking ninth job** on drupalcode | `_AUTORUN_DRUPAL_CMS: 'all'` makes the `Drupal CMS` job automatic and `allow_failure: false` (research R-4). Amendment to D-020 |

## 3 · Scope

### YES — in this unit

**Template repository (`drupal/agora_transparency`)**
- **Content model — six node types**, fixed by **D-026** (2026-08-24) against Ley 19/2013 arts. 6-8,
  not chosen by us: `Document` · `Person` · `Contract` · `Agreement` (convenio) · `Grant`
  (subvención) · `Dataset`. Fields and taxonomy vocabularies exported to `config/`.
  **`Budget` is not a node type:** the approved budget, its execution reports and the cuentas
  anuales are `Document`s; the machine-readable execution table is a `Dataset`, and that rendered
  table — not a chart — is the accessible source of truth. The ROADMAP's “Budget line” was the
  one genuinely wrong shape: the unit of publication is the budget of year N, not the *partida*.
  Six bundles are **not** six units of work — the three financial regimes share one field pattern
  built once in T-601 and attached three times.
  ⚠️ **The art. 8.1.a) derived statistic** (percentage of contracts by procedure, and the volume
  awarded per adjudicatario) is a **legal requirement and it is unit 003's**, not this unit's: it
  needs contracts to aggregate, and this unit ships **no demo content** (NO-list item 1). It is
  named here so it is carried rather than discovered later — the model must make it computable,
  which is precisely why `importe` and `procedure type` are real fields on `Contract` and not
  text on a PDF.
- Roles and permissions: editor, reviewer, publisher — least privilege, asserted by test.
- Base views: **one table view per node type** (columns = that bundle's fields and no others), one shared facet spine (`área · año · estado`), and above them the document library and search box.
- Canvas component enable/disable review for the front-end (not just the admin list
  inherited from the kit).
- The **atomic swap**, in one commit: drop `extra.drupal-site-template`, drop `blank` from
  `install:`, add `drupal/agora_theme` to `require`, `install:` it, point `system.theme`
  at it. Owns and discharges T-106 and the wave-1 rider's adjusted check.
- `_AUTORUN_DRUPAL_CMS` promotion of the install smoke to blocking.
- Carried debt: T-402, T-208, T-317, and the `page.front` declared-vs-landed gap.

**Theme repository (`drupal/agora_theme`)**
- `agora_theme.info.yml`, `composer.json` (`type: drupal-theme`, GPL-2.0-or-later),
  `LICENSE.txt`, `README.md`, `.gitlab-ci.yml`, `.gitattributes`, `.cspell` corpus.
- AA colour tokens, type scale, spacing, visible focus, `prefers-reduced-motion`.
- One self-hosted OFL typeface with its `OFL.txt` and its licence-manifest line.
- Twig templates: page, node, field, **table**, form element, pager, menu.
- Nightwatch + axe test suite, tagged `agora_theme`, blocking.
- Its own `tests/bin/` invariants, including a **token-contrast** invariant with a dirty
  case.
- A stable `1.0.0` release, cut by [andres], before the swap.

### NO — explicitly not this unit

This list is normative. If something below appears mid-wave, it does **not** get done here.

1. **Demo content of any kind.** No nodes, no media, no `content/` additions. One
   exception, narrowly drawn: the *minimum* fixture a Nightwatch or kernel test needs to
   have a page to look at, created **by the test** and never exported. → unit 003.
2. **Bilingual ES/EN anything.** No translations, no `lang` attributes beyond the theme's
   structural `{{ language.getId }}`. → unit 003.
3. **Charts, visualisation, CSV/JSON download endpoints, and the art. 8.1.a) derived statistic.**
   The `Dataset` node type exists as a model; nothing renders it beyond a table — and the table is
   the deliverable, not a fallback. → unit 003.
4. **Editorial workflow, content moderation, ECA, Webform, FOI.** Roles and permissions
   are created; **no transitions and no automation**. → unit 004.
5. **Anything AI, and Config Guardian.** → unit 005.
6. **The accessibility statement page**, the WCAG attestation, the full keyboard
   walkthrough, visual-regression baselines across "all demo pages", performance work, the
   final SBOM sweep, the security-response SLA. → unit 006.
7. **`screenshot.webp`** stays the provisional placeholder. → unit 003.
8. **The marketplace question.** → unit 007-bis, per D-012=C.
9. **`recommended.yml`** is not touched.
10. **macOS certification (T-317).** The *measurement* is carried and owned; making macOS
    a certified surface is not this unit's job unless the measurement comes back clean for
    free.

### The scope gate — how to decide mid-wave

Unit 001 had quality gates and no scope gate, and grew from ~25 tasks to 50-plus with
every single addition individually justified. That is the failure mode: **local
justification with no global denominator.** Two mechanisms, both hard.

**(a) The admission test.** A candidate task enters unit 002 only if **all four** answers
are YES. Any NO sends it to a named later unit with a one-line reason. There is no
"it's small" exemption — smallness was never the problem.

1. Does **gate A of this unit** go red without it? (Not "would the project be better" —
   red or not red.)
2. Does it touch a file **this unit already owns**? A new file in a new area is a new
   surface, and new surfaces belong to the unit that owns the area.
3. Is it required for **unit 003 or 004 to start**, as opposed to merely useful to them?
4. Can it be finished **inside the current wave** without opening a new D-NNN?

⚠️ **AMENDED 2026-08-26 (T-806's audit). The budget is 38, not 34, and this paragraph and the
four other places below still say 34.** D-026 raised it to **38**; D-031 then signed the unit
running at **40 against 38, headroom −2**. The plan was amended once, for the `T-834` correction
below, and never for the budget — so the unit's own **exit criterion** at §"Gate A(8)" reads
*"reported against the 34 budget"*, a number superseded twice. **Read 38 everywhere in this
subsection, and read the count as a command:**

```
grep -c '^| T-[5-8][0-9][0-9] ' specs/002-base-and-theme/tasks.md
40
```

**40 rows against 38. Headroom −2**, reported against 38 rather than re-based to 40, per D-031.
The original text is not edited (rule 8); it is superseded here. ⚠️ And the shape of this defect is
**D-031's own failure mode, alive in the file D-031's method fix names**: *"every count in
`tasks.md` and `plan.md` is now a quoted command with its output, never a number in prose."* Five
numbers in prose, none of them re-derived, and the audit is what found them.

**(b) The budget, and it is a number.** Unit 002 closes at **34 tasks, T-501 … T-834**.
  ⚠️ **Corrected 2026-08-24: there is no T-834.** The unit's last row is **T-806**.
Crossing 34 is not forbidden — it is *escalated*: it costs a rider signed by [andres]
that names the tasks added and the reason, exactly as widening any other denominator
would. The point is not that 34 is correct. The point is that growth becomes **visible
and signed** instead of arriving one well-argued task at a time.

**(c) The audit hook.** The `orquestador`'s wave verdicts report the task count against
the budget, in the same breath as test counts. A unit that has quietly grown 40% is a
finding, at 🟡.

## 4 · Waves

Four waves. The hundreds digit is the **global** wave counter, continuing from unit 001
(waves 1-4), so unit 002 is waves **5-8** and its tasks are T-5xx … T-8xx. This keeps the
signed `T-<wave><nn>` convention and keeps every number unique across the project
(rule 8). Known expiry: from global wave 10 the numbers go four-digit.

### Wave 5 · The theme repository exists and its gate is provably real
**Repositories:** ~~`agora_theme` only. The template repository is not touched.~~ ⚠️ **Falsified by its own table, 2026-08-24:** four wave-5 rows touch the template repository — T-501 (`specs/`, the word list), T-503 (`specs/…/research/`), T-511 (`.gitlab-ci.yml` **and** `CLAUDE.md`) and T-512 (`IDIOMS.md`). Wave 5 is a **two-repository wave**. Not a scope problem, a lane problem: the lanes are `AT` (template) and `TH` (theme), and their file sets are disjoint.
The wave's product is **not a theme** — it is a pipeline whose job list has been read from
the API, containing a Nightwatch job that ran a real browser and printed a count.
No visual design happens here.

**Gate A(5) — theme repository**
- Pipeline job list read from
  `/api/v4/projects/project%2Fagora_theme/pipelines/<id>/jobs`; `jobs >= 5`; every
  `status == "success"`; every `allow_failure == false`; the exception list in
  `.gitlab-ci.yml` empty. (D-023(5), applied to the second repository.)
- `nightwatch` present in that list, `allow_failure: false`, and its log shows
  **`N` tests executed with `N >= 1`**, plus the axe rule count for the page it scanned.
  A Nightwatch job that collected 0 test files is a **failed** gate.
- The theme's own `tests/bin/` runner prints `N checks — 0 failures` with `N` stated.
- `composer validate --strict` exit 0.

### Wave 6 · Content model ‖ visual identity — fully parallel, disjoint by repository
**Lane A** touches only `agora_transparency`. **Lane B** touches only `agora_theme`.
Zero shared files. Neither lane blocks the other. Both must be green before wave 7.

**Gate A(6)**
- Template: `phpunit` green with counts; a kernel test asserts every node type, field,
  vocabulary and role exists with the intended machine name; a test asserts **no role
  outside `administrator` holds `administer *` permissions**.
- Template: `composer validate`, all 8 existing jobs green, `agora-invariants` counts
  unchanged or higher.
- Theme: `stylelint` and `twig-cs-fixer` now **materialise** (CSS and Twig exist) and are
  in the job list, blocking. Their arrival updates the theme's job table.
- Theme: the token-contrast invariant reports **`N` pairs checked, 0 below 4.5:1**
  (3:1 for large text and non-text), with `N` stated and a dirty case proving it fires.
- Theme: `nightwatch` axe run over the theme's own rendered fixture pages, **page count
  stated**, 0 violations.

### Wave 7 · The atomic swap
**Sequential. Single lane.** Depends on: wave 6 green in both repositories, and a **stable
`agora_theme` 1.0.0 release** existing (a [andres] action, ordered in wave 5, performed
between waves 6 and 7).

**Gate A(7)**
- `composer.json` has **no** `extra.drupal-site-template` key and **does** have
  `drupal/agora_theme` with a caret constraint. `no-boilerplate`'s deny list grows by the
  `extra.drupal-site-template` entry, with its dirty case.
- `recipe.yml` `install:` contains `agora_theme` and **not** `blank`; `system.theme`
  `default: 'agora_theme'`.
- The wave-1 rider's **adjusted check is reverted** and the adjustment's absence is
  asserted. The rider's debt is discharged **by name** in the same commit.
- `RequirementsTest` green: still **0 `*.info.yml`** in the package, no pin, no patch.
- The `Drupal CMS` job is in the job list, `allow_failure: false`, and its log shows the
  site installed with `agora_theme` as the default theme.

### Wave 8 · Closure, carried debt, and the verdict
**Sequential.** Discharges T-402, T-208, T-317, and the `page.front` gap.

**Gate A(8)**
- Both repositories' job lists read from the API, printed side by side in the report.
- Every runner's denominator quoted with its result (I-045). Nothing is reported as green
  without the size of the set it opened.
- `orquestador` audit with no open 🔴, and the task count reported against the 34 budget.

## 5 · Which gate belongs to which repository

The one-line answer to the dispatch's question, because it will be asked again:

| Check | `agora_transparency` | `agora_theme` |
|---|---|---|
| `composer`, `composer-lint`, `cspell`, `phpcs`, `phpstan`, `phpunit` | ✅ blocking today | ✅ from wave 5 |
| `eslint` | ✅ blocking today | ✅ (Nightwatch tests are `.js`) |
| `agora-invariants` | ✅ blocking today | ✅ own subset, from wave 5 |
| `stylelint` | ❌ no CSS in the package — **may never run here** | ✅ from wave 6, blocking |
| `twig-cs-fixer` | ❌ no Twig in the package | ✅ from wave 6, blocking |
| `nightwatch` + axe | ❌ **cannot be collected** (research R-3c) | ✅ from wave 5, blocking |
| `Drupal CMS` clean-install smoke | ✅ from wave 7, blocking | ❌ not applicable |
| Playwright visual regression (GitHub, informative) | — | ✅ T-206(b), non-blocking per D-009 |

## 6 · How the two repositories are kept in step

Three mechanisms, no ceremony.

1. **One direction only.** The template depends on the theme; the theme knows nothing about
   the template. There is no reverse edge and none is permitted. A theme that needed to
   know about Ágora's content types would be a coupling that unit 006 could not audit.
2. **The dependency is a released, caret-constrained version.** Never a branch, never a
   path repo, never `@dev`. Consequence: the theme's `1.0.0` release is a **hard
   prerequisite of wave 7**, and it is an [andres] action (rule 10). See D-025.
3. **Shared invariants carry a manifest, not a promise.** Whatever `tests/bin/` scripts
   both repositories run exist as copies with a checksum manifest; an invariant in the
   theme repo fails when a copy has been edited locally. Upstream drift is caught by a
   dated review task in unit 006, not by magic. See D-028.

## 7 · Risks

| Risk | Sev | Mitigation |
|---|---|---|
| `drupal/agora_theme` never appears in `packages.drupal.org`, so the swap cannot land | 🔴 | T-505 measures it early with an exact command and escalates the same day; the swap is in wave 7 precisely to leave room |
| A Nightwatch job goes green having collected 0 tests | 🔴 | T-509's success criterion is a **stated test count ≥ 1** plus a deliberately-failing axe assertion proving the job can go red |
| The security-coverage window (eligible 2026-09-03) closes with the theme half-written | 🟡 | Waves 5 and 6 are the window. Quality standards apply to the **first** theme commit, not to its gate |
| Contrast tokens pass a script and fail a human eye | 🟡 | The invariant is necessary, not sufficient; gate B(6) is Andrés looking at it |
| macOS host still uncertified while a second agent works there | 🟡 | T-317 carried into wave 8; until then no invariant green from that host is quotable |
| Unit 002 grows the way unit 001 grew | 🟡 | §3's admission test and the 34-task budget, reported in every wave verdict |
