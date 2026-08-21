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
- [ ] **T-402** · Extend `InstallTest`/`ValidationTest` with Ágora's key routes.
      *Success:* number of tests and assertions reported, > 0.
- [ ] **T-403** · Project README **in English** (public docs in English, D-005): what it is, how it is
      installed, what it ships.
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
> Last updated: 2026-08-21, after wave 1 closed (gate A 61/0, gate B signed).

| Blocker | State | Blocks | Who resolves |
|---|---|---|---|
| D-011 recipe architecture | ✅ SIGNED 2026-08-21 · option A (a single recipe at the root) | — unblocks T-101…T-105 | — |
| D-007 machine name | ✅ SIGNED 2026-08-21 · `agora_transparency` | — unblocks T-102 | — |
| D-008 theme approach | ✅ SIGNED 2026-08-21 · option A; rider suspended and **subsumed by D-014** | — | — |
| D-012 publication route | ✅ SIGNED 2026-08-21 · option C (community first; marketplace = 007-bis, non-blocking) | — | — |
| D-013 AI provider | ✅ SIGNED 2026-08-21 · `ai` ^1.4 hard + `ai_provider_openai` recommended | — | — |
| D-014 where the theme lives | ✅ SIGNED 2026-08-21 · option B (separate project `drupal/agora_theme`) | **T-106 DEFERRED to unit 002**, where it is redefined against D-014=B (see T-110) | — |
| D-015 AI artefacts in the public repo | ✅ SIGNED 2026-08-21 · `AGENTS.md` is product; `CLAUDE.md`/`.claude/`/`specs/` visible and `export-ignore`d | — unblocks T-111, T-112 and the T-104 note | — |
| D-016 repository flow | ✅ SIGNED 2026-08-21 · D-002 CONFIRMED (drupalcode canonical; GitHub mirror read-only, same history) | — the mirror is set up in unit 007 | — |
| D-017 language | ✅ SIGNED 2026-08-21 · entire repo in English, process layer included; amends D-005 and rule 6 | — unblocks T-113 | — |
| D-009 test split | 🔴 OPEN | T-206 | 👤 Andrés |
| D-010 v1 demo content scope | 🔴 OPEN | unit 003 | 👤 Andrés |
| T-106 theme approach | ⏸️ DEFERRED to unit 002 (see T-110) | — | — |
| definitive `screenshot.webp` | ⏸️ DEFERRED to unit 003 (see T-114) · provisional placeholder in its place, deliberately does not imitate a real site | — | — |
