# Ágora · Unit 001 — Foundation · Plan

> Produced in the scaffolding turn of `DISPATCH-00.md` [ejecutor] 2026-08-20.
> Factual basis: `research/2026-08-20-estado-del-arte.md`. **Do not implement anything from here without gate B.**

## 1 · Unit objective

That there exists on disk a repository that **is already a valid Drupal CMS site template**: installable
on a clean Drupal, with the drupalcode pipeline green and the `tests/bin/` invariants
operational — **still without visual identity, without content model and without demo content**.

Criterion for "done": a third party clones the repo, brings up DDEV, installs Drupal CMS and **sees the
Ágora template in the installer's template selector**.

## 2 · What changed with respect to what the master plan assumed

The research invalidated two structural assumptions in `CLAUDE.md` §Repository structure:

1. **There is no `recipes/`.** The repository **is** a single recipe: `recipe.yml` at the root with
   `type: Site`. Features are composed by referencing **external composer packages**.
2. **There is no `themes/agora_theme/`** in the default flow: the theme is **generated** by
   `drupal/site_template_helper` from `extra.drupal-site-template.generate-theme` in `composer.json`.

Both things trigger stopping rule no. 2 of the dispatch → **D-011 and D-008 must be signed before
this unit starts**. The rest of the master plan survives intact.

## 3 · Scope

**YES** · Copy and strip the starter kit (`1.x`) · package identity · own `recipe.yml` ·
reproducible DDEV environment · green CI while empty · the 4 invariant scripts · real install smoke.

**NO** · Content model (002) · theme with aesthetics (002) · demo content (003) · ECA (004) ·
AI and Config Guardian operational (005) · creation of the project on Drupal.org (007) · tag/release.

## 4 · Waves

### Wave 1 — Skeleton and identity
Copy `1.x`, rename the package, clean up the kit's scaffolding, definitive `.gitignore`/`.gitattributes`,
own `recipe.yml` with the inherited base recipes.
**Depends on:** D-007 (machine name), D-011 (architecture).

### Wave 2 — Environment and CI
DDEV ≥ 1.25.0 reproducible and documented; `.gitlab-ci.yml` with its variables; first green pipeline;
decide what runs on GitHub Actions.
**Depends on:** wave 1. Opens D-009.

### Wave 3 — Invariants
`tests/bin/no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check` (spec in §6).
Parallelizable with wave 2: disjoint files.

### Wave 4 — Install smoke and closure
Clean installation verified, own provisional `screenshot.webp`, README in English,
`orquestador` verdict.
**Depends on:** waves 1–3.

## 5 · Gates

| Wave | Gate A (automatable, with counts) | Gate B (Andrés) |
|---|---|---|
| 1 | `composer validate --strict` exit 0 · `recipe.yml` with `type: Site` · no residual `_comment` or `extra.drupal-site-template` | Confirms machine name and identity |
| 2 | drupalcode pipeline **green**, with number of jobs executed · clean `ddev start` from scratch | Confirms where each type of test runs |
| 3 | The 4 scripts exit 0, each reporting **number of files scanned** and **number of findings** | — |
| 4 | `InstallTest`+`ValidationTest`+`RequirementsTest` green with number of tests and assertions · template visible in the selector after `sql:drop` + reinstallation | Signs the closure of the unit |

Rule that applies to all four: **real counts, never bare exit codes** (skill `gate-a-verde`).

## 6 · Specification of the invariants (`tests/bin/`) — NOT implemented yet

All four: exit 0 = clean, exit 1 = findings. All print **scanned scope + number of files +
number of findings**, and each finding with `file:line`.

| Script | What it looks for | Scope | Notes |
|---|---|---|---|
| `no-unstable-deps` | `-dev`, `-alpha`, `-beta`, `-rc`, `dev-` in constraints; `minimum-stability` other than `stable` | `composer.json`, `composer.lock` | **Exclude** the starter kit: it is copied, not declared (research §3.1) |
| `no-patches` | `patches`, `composer-patches`, `patches-file` key | `composer.json` | Literal prohibition from the kit |
| `no-secrets` | `api[_-]?key`, `secret`, `token`, `passwd`, `password`, `Bearer `, DSNs, private keys | the whole repo except `.git/` | Must also run over `config/` and `content/` |
| `sbom-check` | For each `drupal/*` in `require`: queries `updates.drupal.org` and requires a **stable** release + `<security covered="1">` + a line in `DECISIONES.md` | `composer.json` + `DECISIONES.md` | Method verified in research §10.4 |

## 7 · Risks

| Risk | Sev | Mitigation |
|---|---|---|
| Copying the `require` from the `2.x` branch drags in `project_browser ^2.1-beta3` (**beta**) | 🔴 | Start from `1.x`; if Project Browser is needed, wait for its stable release or leave it out |
| Unverified marketplace requirements (DCP-only, fees) | 🔴 | Verify before 006; does not block 001 |
| Machine name taken on Drupal.org | 🟡 | Verify before wave 1; alternatives already listed |
| drupalcode runners without support for Playwright/axe | 🟡 | D-009; plan B = the mirror's GitHub Actions |

> **Risk status update — [orquestador] 2026-08-22.** Three of these four rows are stale, and a risk
> table that is mostly stale stops being read (same mechanism as I-020). Statuses are recorded here
> rather than by deleting rows.
> · **Row 1 — `project_browser ^2.1-beta3` (beta): CLOSED.** Verified 2026-08-22 against
>   `https://updates.drupal.org/release-history/project_browser/current` (method I-022):
>   **2.1.4**, stable, `<security covered="1">`, `core_compatibility ^11.2 || ^12`. Ágora never
>   carried it — `require` holds nine packages, none of them `project_browser` (D-018).
> · **Row 2 — unverified marketplace requirements: CLOSED by D-012.** The "DCP-only, $395" premise
>   was false (I-016).
> · **Row 3 — machine name taken: CLOSED by D-007** (`agora_transparency`, 404 on the GitLab API
>   oracle, I-012; re-verified free 2026-08-22).
> · **Row 4 — drupalcode runners without Playwright/axe: SUPERSEDED by D-009 = C.** The row's
>   *verification* remains open. **Updated 2026-08-22: the project now exists**
>   (`project/agora_transparency`, created 18:17:19Z, public, repository empty), so the canary MR of
>   D-009 rider (b) is no longer blocked by its absence. It is blocked by the first push (T-217)
>   and, substantively, by unit 002 — until a theme exists there is nothing for axe to audit.
> **New risk, added 2026-08-22:** a gate surface whose result nobody reads as a count.
> Sev 🔴 · **CLOSED for the GitHub surface** (T-213 run `32582950414` reports `Tests: 3,
> Assertions: 38`; T-215 run `32583207616` proves the empty case red), **OPEN for drupalcode**
> until T-205 observes `--fail-on-empty-test-suite` in an executed phpunit command line.
> **New risk 2026-08-22 — `simpleConfigUpdate` becomes an exception in Drupal 12** inside an
> upstream recipe Ágora composes. Sev 🟡 · Not Ágora's own usage (verified at source, I-037) ·
> Mitigation: unit 007 dependency review; see the blockers table.

## 8 · Open questions → decisions to sign

Written in plain language, with recommendation ★, in `DECISIONES.md` §Pending.
**D-007** machine name · **D-008** theme approach · **D-009** where the visual tests run ·
**D-011** recipe architecture (blocking) · **D-012** publication route · **D-013** AI provider.
