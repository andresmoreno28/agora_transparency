# Ágora · Unit 001 — Foundation · Tasks

> **Append-only.** A task signed `[✓ date]` is not renumbered or rewritten.
> No task starts without gate B of the previous wave. Produced [ejecutor] 2026-08-20.
>
> ✅ **WAVE 1 CLOSED 2026-08-21**: gate A **61 checks · 0 failures**, gate B signed by [andres].
> 12 tasks signed, 2 deferred with an owner (T-106 → unit 002, T-114 → unit 003).
> **Waves 2 and 3 are now open** and are parallelizable (disjoint files).
> D-009 remains open → T-206. See the "Active blockers" table at the end for the current state.

Legend: `[ ]` pending · `[~]` in progress · `[✓ YYYY-MM-DD]` signed ·
`[⏸ …]` deferred to a later unit, with owner · 👤 requires the human

---

## Wave 1 · Skeleton and identity

- [✓ 2026-08-21] **T-101** · Copy the `1.x` branch of `drupal_cms_site_template_base` into the repo, without its git history.
      *Success:* `recipe.yml`, `composer.json`, `.gitlab-ci.yml`, `.tugboat/`,
      `.github/`, `tests/` exist at the root. **Blocked by D-011.**
- [✓ 2026-08-21] **T-102** · Rename the package to `drupal/<machine_name>` in `composer.json` and adjust
      `description`. *Success:* `composer validate --strict` exit 0. **Blocked by D-007.**
- [✓ 2026-08-21] **T-103** · Strip the scaffolding: delete the `_comment` array from `composer.json`, delete
      `GET-STARTED.md`, replace `screenshot.webp` with a provisional one of our own.
      *Success:* `grep -c '_comment' composer.json` = 0; `GET-STARTED.md` does not exist.
      > **Note (rider [andres] 2026-08-21):** *"three `_comment` occurrences, not one; the
      > `extra.drupal-site-template` block is NOT deleted in unit 001 — see the wave 1 rider on the
      > `blank` theme."*
- [✓ 2026-08-21] **T-104** · Definitive `.gitignore` and `.gitattributes` from the `.example` files; delete the `.example` files.
      *Success:* `.gitattributes` contains the `export-ignore` entries for `/tests`, `/.github`,
      `/.gitlab-ci.yml`, `/.tugboat`.
      > **Note (rider [andres] 2026-08-21):** *"`.gitattributes` must `export-ignore` `/CLAUDE.md`,
      > `/.claude`, `/specs` (D-015.2) and must NOT export-ignore `AGENTS.md` (D-015.1)."*
- [✓ 2026-08-21] **T-105** · Own `recipe.yml`: `name`, `description`, `type: Site`, inherited base recipes.
      *Success:* `type: Site` exact; the file parses as valid YAML.
- [⏸ deferred 2026-08-21 → unit 002 (D-014=B)] **T-106** · Resolve the theme approach according to D-008 (generated via `generate-theme` vs
      versioned). *Success:* the decision is applied and `recipe.yml` installs the correct theme.
      **Blocked by D-008.**
- [✓ 2026-08-21] **T-107** · Signatures for D-007, D-008, D-011, D-012, D-013, D-014 + amendments to `plan.md` §2 and
      `CLAUDE.md` §Structure, **in a single commit** (rider D-011a + rider D-014b).
      *Success:* `git show --stat HEAD` lists exactly 3 files;
      `grep -c 'recipes/agora_base' specs/000-proyecto/plan.md` = 0;
      `grep -c 'D-014' specs/000-proyecto/DECISIONES.md` ≥ 1.
- [✓ 2026-08-21] **T-108** · Append I-011…I-017 to `IDIOMS.md`.
      *Success:* `grep -cE '^- I-01[1-7]' specs/000-proyecto/IDIOMS.md` = 7; no previous line deleted.
- [✓ 2026-08-21] **T-109** · Dated research `specs/001-fundacion/research/2026-08-21-flujo-tema-y-marketplace.md`.
      *Success:* ≥ 6 source URLs cited and the 4 conclusions recorded.
- [✓ 2026-08-21] **T-110** · 🔒 **T-106 is declared DEFERRED** to unit 002: it is redefined there against
      **D-014=B** (integrate the `drupal/agora_theme` theme as a dependency, not generate it in this
      repo). *Success:* the blockers table reflects the deferral and the pending redefinition.
- [✓ 2026-08-21] **T-111** · Fill in the "Template-specific notes" section of `AGENTS.md` in English, with the
      audience header required by D-015.1. *Success:* the section is no longer empty; it states that
      `CLAUDE.md` governs template development and `AGENTS.md` targets sites built with the template.
- [✓ 2026-08-21] **T-112** · `README.md` gains the "Development process" section required by D-015.4.
      *Success:* the section exists, in English, and links to `specs/`.
- [✓ 2026-08-21] **T-113** · Mechanical translation of the process layer to English (D-017): `CLAUDE.md`,
      `.claude/agents/*` (3), `.claude/commands/*` (3), `.claude/skills/*` (7), `specs/`. Own commit,
      no semantic changes; ambiguities escalated. *Success:* zero semantic diffs reported; a restart
      of the session after translating `.claude/agents/` (I-008).
- [⏸ deferred 2026-08-21 → unit 003 (demo content)] **T-114** · 🔒 The **definitive `screenshot.webp` is DEFERRED to unit 003**, where the demo
      content it must depict exists. T-103 shipped a **provisional placeholder** instead: a 632×363
      WebP (the starter kit's own dimensions, therefore known-compatible with the installer), neutral,
      hatched, and labelled *"PLACEHOLDER — NOT A SCREENSHOT OF A REAL SITE"*, rendered with DejaVu
      Sans (Bitstream Vera / DejaVu licence, redistributable). It deliberately does **not** imitate a
      site: fabricating a screenshot of a site that does not exist would misrepresent the template to
      anyone browsing the installer. This task owns the replacement.
      *Success:* `screenshot.webp` is a real capture of the installed demo site, WebP, at the
      repository root; `shasum -a 256 screenshot.webp` differs from
      `98363dd5a77e8374d33666d2bbf905f15229a7c1aca9e82fc7c37542b3e02f1c` (the placeholder);
      the blockers table records this debt as closed. Signed off visually by 👤 [andres].
- [ ] **T-115** · Amend D-008 in `DECISIONES.md` with the real `Plugin.php` code, and record D-009
      and D-018. *Success:* `grep -c 'D-018' specs/000-proyecto/DECISIONES.md` >= 1;
      `grep -c 'onPackageInstall' specs/000-proyecto/DECISIONES.md` >= 1.
      > **Note:** added after wave 1's gate A closed (61 checks / 0 failures). It is a
      > process-layer task: it touches no artefact the wave 1 gate measures, so it does not
      > reopen that gate.

**Gate A wave 1**
```bash
composer validate --strict
python3 -c "import yaml,sys; d=yaml.safe_load(open('recipe.yml')); assert d['type']=='Site', d.get('type'); print('type OK')"
grep -c '_comment' composer.json          # expected: 0
test ! -f GET-STARTED.md && echo "kit docs clean"
```
**Gate B wave 1** 👤 · Andrés confirms package name, visible description and identity.
Sign here: `[✓ 2026-08-21 andres]` — package `drupal/agora_transparency`, visible identity "Ágora",
description as recorded in `composer.json`. Gate A closed with **61 checks · 0 failures**.

---

## Wave 2 · Environment and CI

- [ ] **T-201** · Reproducible DDEV configuration (≥ 1.25.0), documented in the README.
      *Success:* `ddev start` from scratch on a clean machine, with no manual steps.
- [ ] **T-202** · Review `.gitlab-ci.yml`: keep the `gitlab_templates` include, set only
      the necessary variables. *Success:* no job is defined by hand.
- [ ] **T-203** · Read `include.drupalci.variables.yml` and document in the README which jobs remain
      active (phpcs, phpstan, cspell, eslint, stylelint, phpunit). *Success:* a real list, not an assumed one.
- [ ] **T-204** · Create `.cspell-project-words.txt` with the project vocabulary.
      *Success:* the cspell job passes without disabling it.
- [ ] **T-205** · First green pipeline in the working repo. *Success:* number of jobs executed > 0 and
      all green. **A pipeline with no jobs is NOT green.**
- [ ] **T-206** · Decide and apply D-009: what runs on drupalcode and what on GitHub Actions.
      **Blocked by D-009.**
- [ ] **T-207** · Replace the assumption "`ddev start` in the repo" with the verified flow: set up
      Drupal separately and add the template as a *path repository*, following the kit's `.github/workflows/phpunit.yml`
      (`ddev config --project-type=drupal11 --docroot=web` → `ddev composer create-project
      --no-install drupal/recommended-project` → `ddev composer repository add source path source` →
      `ddev composer require "<package>:@dev"`, with `COMPOSER_MIRROR_PATH_REPOS=1`).
      *Success:* one command reproduces the environment from scratch; `ddev exec drush status` →
      `Drupal bootstrap : Successful`; `./recipes/agora_transparency` exists.
- [ ] **T-208** · Pin DDEV ≥ 1.25.0 and **version `.ddev/config.yaml`** (today `.gitignore` ignores
      `/.ddev/`, which makes the T-201 criterion unreachable).
      *Success:* `git ls-files .ddev/config.yaml | wc -l` = 1; requirement documented in the README.
- [ ] **T-209** · Invariant: `CI_ALLOW_DEV` is not defined in any versioned file.
      *Success:* 0 matches, printing the number of files scanned (> 0).
      > **Note (rider [andres] 2026-08-21):** *"specified as 'not DEFINED', never 'not mentioned' —
      > see I-018."*

**Gate A wave 2**
```bash
ddev start && ddev drush status          # expected: Drupal bootstrap = Successful
# On GitLab: the pipeline of the latest commit, green, with the number of jobs in view
```
**Gate B wave 2** 👤 · Andrés confirms the split of tests between drupalcode and GitHub.
Sign here: `[ ]`

---

## Wave 3 · Invariants (parallelizable with wave 2 — disjoint files)

- [ ] **T-301** · `tests/bin/no-unstable-deps` according to the spec in `plan.md` §6.
      *Success:* it detects a deliberately injected `-beta` and does **not** flag the starter kit.
- [ ] **T-302** · `tests/bin/no-patches`. *Success:* it detects an injected `patches` section.
- [ ] **T-303** · `tests/bin/no-secrets` over the whole repo except `.git/`.
      *Success:* it detects a fake key injected in `config/` and another in `content/`.
- [ ] **T-304** · `tests/bin/sbom-check` against `updates.drupal.org` (method in research §10.4).
      *Success:* it requires stable + `<security covered="1">` + a line in `DECISIONES.md`; it fails if one is missing.
- [ ] **T-305** · All four print scope, number of files scanned and number of findings.
      *Success:* none reports "0 files scanned".
- [ ] **T-306** · **Amendment to the T-304 method** (`sbom-check`): the endpoint returns **HTTP 200
      with an `<error>` body** for non-existent projects → a `curl -f` gives a false green. It must:
      (a) check the network at startup and **fail loudly if there is none** — "skip" forbidden;
      (b) parse the XML; (c) require `<title>` and the absence of `<error>`; (d) take as stable the
      first release without `dev|alpha|beta|rc`; (e) require `<security covered="1">` in that release;
      (f) check `<core_compatibility>`; (g) require a `D-NNN` line in `DECISIONES.md` for each
      `drupal/*` in `require`.
      *Success:* with the real `require` → exit 0 and it prints
      `N projects queried · N with coverage · 0 findings`; with a non-existent project
      injected → exit 1; with the network cut off (`https_proxy=http://127.0.0.1:1`) → **exit 1**,
      never exit 0.
- [ ] **T-307** · `tests/bin/no-code-in-template`: mirrors the `RequirementsTest` assert locally.
      *Success:* it prints `N files scanned · 0 *.info.yml files`, N > 0; it detects an injected
      `themes/x/x.info.yml`; clean tree after reverting.
- [ ] **T-308** · Translate `tests/bin/gate-a-wave1.sh` to English. It is code, and D-017 covers
      code; T-113 enumerated `CLAUDE.md`, `.claude/*` and `specs/` and left it out.
      **This blocks T-204:** the cspell job is on by default in `gitlab_templates`, and the
      shortcut to green would be dumping Spanish words into `.cspell-project-words.txt` — which is
      adding a file to an ignore list to turn a gate green, an automatic 🔴.
      *Success:* 0 matches for the Spanish token list, **and the gate still reports
      `61 checks · 0 failures`** — same N, same M: it is a translation, not a change of checks.
- [ ] **T-309** · `tests/bin/no-boilerplate` — a deny-list invariant over **every published
      artefact** (what `git archive` ships), for strings inherited from the starter kit:
      `electrician`, `WCAG AAA`, `GET-STARTED`, `MY_SITE_TEMPLATE_NAME`,
      `Site Template Starter Kit`, `my amazing site template`.
      > **Rider of [andres] 2026-08-21:** *"it runs after **a full initial sweep** over config, demo
      > content and descriptions — not just the README."*
      *Success:* it prints scope and the number of files scanned (> 0) and 0 findings; it detects an
      injected string; clean tree after reverting.
- [ ] **T-310** · 🔒 **T-403 is brought forward to wave 3** (rider of [andres] 2026-08-21):
      rewrite `README.md` in English describing Ágora, and resolve `recommended.yml`.
      > **Rider of [andres] 2026-08-21:** *"the README declares **WCAG 2.2 AA, verified — never
      > AAA** (I-023)."*
      *Success:* 0 findings from `tests/bin/no-boilerplate`; the "Development process" section from
      T-112 survives; no broken links.

**Gate A wave 3**
```bash
for s in no-unstable-deps no-patches no-secrets sbom-check; do
  echo "── $s"; tests/bin/$s; echo "exit=$?"
done
# Each one: exit 0 + number of files scanned > 0 + number of findings printed
```
Each script must additionally be tested **with an injected dirty case** (and reverted): if it does not
fail with garbage inside, it is useless. Silencing an invariant to pass = automatic 🔴.
**Gate B wave 3** 👤 · No signature required; it enters the unit closure verdict.

---

## Wave 4 · Install smoke and closure

- [ ] **T-401** · Clean install smoke: `sql:drop` + reinstallation, verifying that the template
      appears in the selector. *Success:* a capture or output that demonstrates it.
      > **Note (rider [andres] 2026-08-21):** *"the clean-install assertion runs in
      > **non-interactive mode** (no composer prompts answered by hand), and must verify how
      > `site_template_helper` ends up authorised in the `allow-plugins` of the `composer.json` the
      > end user receives. If generation requires manual interaction → **escalate as an install-UX
      > finding, do not patch silently.**"* See the amendment to D-008.
- [ ] **T-402** · Extend `InstallTest`/`ValidationTest` with Ágora's key routes.
      *Success:* number of tests and assertions reported, > 0.
- [ ] **T-403** · Project README **in English** (public docs in English, D-005): what it is, how it is
      installed, what it ships.
      > **Note 2026-08-21:** superseded in wave 3 by **T-310**; T-403 keeps whatever is left over
      > for wave 4.
- [ ] **T-404** · `orquestador` audit (read-only): standards, SBOM, licences, marketplace
      requirements. *Success:* verdict with no open 🔴.
- [ ] **T-405** · Promote the unit's lessons to `IDIOMS.md`.
- [ ] **T-406** · Verify that `InstallTest`, `ValidationTest` and `RequirementsTest` pass **without
      being modified**. *Success:* 0 lines deleted in those 3 files; phpunit output with number of
      tests **and** assertions.

**Gate A wave 4**
```bash
ddev drush sql:drop --yes && ddev drush site:install --yes   # and check the selector
ddev exec vendor/bin/phpunit --testdox tests/                 # number of tests and assertions
```
**Gate B wave 4** 👤 · Andrés signs the closure of unit 001.
Sign here: `[ ]`

---

## Active blockers

> A table of **state**, not of signed tasks: it is rewritten on each update.
> Last updated: 2026-08-21, after the D-009 / D-018 signature batch.

| Blocker | State | Blocks | Who resolves |
|---|---|---|---|
| D-011 recipe architecture | ✅ SIGNED 2026-08-21 · option A (a single recipe at the root) | — unblocks T-101…T-105 | — |
| D-007 machine name | ✅ SIGNED 2026-08-21 · `agora_transparency` | — unblocks T-102 | — |
| D-008 theme approach | ✅ SIGNED 2026-08-21 · option A; rider suspended and **subsumed by D-014**. **AMENDED 2026-08-21**: the claim "it does not generate on the end user's installation" was false (`Plugin.php` has no root-package check) — conclusion unchanged, fact corrected | — the `allow-plugins` consequence is inherited by **T-401** | — |
| D-012 publication route | ✅ SIGNED 2026-08-21 · option C (community first; marketplace = 007-bis, non-blocking) | — | — |
| D-013 AI provider | ✅ SIGNED 2026-08-21 · `ai` ^1.4 hard + `ai_provider_openai` recommended | — | — |
| D-014 where the theme lives | ✅ SIGNED 2026-08-21 · option B (separate project `drupal/agora_theme`) | **T-106 DEFERRED to unit 002**, where it is redefined against D-014=B (see T-110) | — |
| D-015 AI artefacts in the public repo | ✅ SIGNED 2026-08-21 · `AGENTS.md` is product; `CLAUDE.md`/`.claude/`/`specs/` visible and `export-ignore`d | — unblocks T-111, T-112 and the T-104 note | — |
| D-016 repository flow | ✅ SIGNED 2026-08-21 · D-002 CONFIRMED (drupalcode canonical; GitHub mirror read-only, same history) | — the mirror is set up in unit 007 | — |
| D-017 language | ✅ SIGNED 2026-08-21 · entire repo in English, process layer included; amends D-005 and rule 6 | — unblocks T-113; **T-308** picks up `tests/bin/gate-a-wave1.sh`, which T-113 left out | — |
| D-009 test split | ✅ **SIGNED 2026-08-21 · option C** — axe inside the existing `gitlab_templates` Nightwatch job on drupalcode (canonical, mandatory); visual regression on GitHub Actions, non-blocking | — unblocks **T-206**, but it cannot be *applied* until the drupalcode project exists; the axe↔Nightwatch integration is verified in unit 002 | — |
| D-018 baseline SBOM | ✅ **SIGNED 2026-08-21** — the 9 packages in `require`, all stable and `covered="1"`, verified against `updates.drupal.org`. Baseline **CLOSED**: any later change needs its own D-NNN | — gives `sbom-check` (T-304/T-306) its `D-NNN` oracle | — |
| D-010 v1 demo content scope | 🔴 **OPEN** | unit 003 | 👤 Andrés |
| **Wave 2 substantially BLOCKED** | 🔴 **OPEN** · **T-201, T-207 and T-208 are mutually incompatible**: T-201 assumes `ddev start` inside this repo, T-207 replaces that with the path-repository flow (Drupal set up separately), and T-208 versions `.ddev/config.yaml` in a repo that today `.gitignore`s `/.ddev/`. A recipe package is not a site: it cannot be `ddev start`ed on its own. **Pending a decision on where the development environment lives** — no D-NNN assigned yet | T-201, T-207, T-208 | 👤 Andrés |
| T-205 first green pipeline | 🔴 **OPEN** · **there is no project on drupalcode** (`git.drupalcode.org/api/v4/projects/project%2Fagora_transparency` → **404**, verified 2026-08-21). Without a project there is no pipeline, no canary MR (D-009 rider b) and nothing to apply D-009 to | T-205, and the application of T-206 | 👤 Andrés (creates the project) |
| T-106 theme approach | ⏸️ DEFERRED to unit 002 (see T-110) | — | — |
| definitive `screenshot.webp` | ⏸️ DEFERRED to unit 003 (see T-114) · provisional placeholder in its place, deliberately does not imitate a real site | — | — |
