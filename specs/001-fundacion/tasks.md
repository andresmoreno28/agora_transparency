# Ágora · Unit 001 — Foundation · Tasks

> **Append-only.** A task signed `[✓ date]` is not renumbered or rewritten.
> No task starts without gate B of the previous wave. Produced [ejecutor] 2026-08-20.
>
> ✅ **WAVE 1 CLOSED 2026-08-21**: gate A **61 checks · 0 failures**, gate B signed by [andres].
> 12 tasks signed, 2 deferred with an owner (T-106 → unit 002, T-114 → unit 003).
> **Waves 2 and 3 are now open** and are parallelizable (disjoint files).
> D-009 remains open → T-206. See the "Active blockers" table at the end for the current state.
>
> ✅ **WAVE 3 CLOSED 2026-08-21**: gate A **`gate-a-wave1.sh` 61 checks · 0 failures** +
> **`gate-a-wave3.sh` 28 checks · 0 failures** + a 12-injection dirty-case matrix, all reverted.
> **16 tasks signed** (T-115, T-209, T-301…T-313, T-315 — the verdict text said 14; the list it
> certified holds 16, counted on disk). Independent verdict by `orquestador`, which re-ran every count and closed the
> last open matrix row itself. Gate B: none required (wave 3 enters the unit closure verdict).
> Debt carried forward with owners: **T-314** (packaged `recipe.yml` boilerplate),
> **T-316** (grep rc-blindness across all scanners), **T-317** (toolchain floor, blocked by D-019).
> ⚠️ This gate is certified **on the development host** (grep 3.0 · jq 1.8.2 · python 3.12.6 ·
> PHP 8.4.24 ZTS · Composer 2.10.2). It is **not** a claim about the drupalcode runners: no CI
> executes `tests/bin/` today. See the blockers table.

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
- [✓ 2026-08-21] **T-115** · Amend D-008 in `DECISIONES.md` with the real `Plugin.php` code, and record D-009
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
> **Note appended 2026-08-22 — the `python3 -c "import yaml…"` line above is broken on this host**
> (T-320c). This note **does not edit the signed block**; same treatment as the superseded wave 3
> gate block. Verified 2026-08-22: that one-liner raises
> `UnicodeDecodeError: 'charmap' codec can't decode byte 0x81 in position 88` on any host whose
> Python defaults to a non-UTF-8 encoding — position 88 is the `Á` of `name: Ágora`. It needs
> `encoding='utf-8'` (or `PYTHONUTF8=1`). Nobody noticed because **`tests/bin/gate-a-wave1.sh`
> supersedes this block** and deliberately does **not** parse YAML: `ValidationTest::testApply()`
> applies the recipe through Drupal itself in wave 4 (T-402/T-406), and adding PyYAML to gate A
> would put a pip dependency inside the gate — which `sbom-check`'s own header already refuses
> in writing for `defusedxml`, and which would need a signed D-NNN.

**Gate B wave 1** 👤 · Andrés confirms package name, visible description and identity.
Sign here: `[✓ 2026-08-21 andres]` — package `drupal/agora_transparency`, visible identity "Ágora",
description as recorded in `composer.json`. Gate A closed with **61 checks · 0 failures**.

> **Note appended 2026-08-22 — what the wave 1 gate could not see (T-316).**
> This note **does not amend the signature above**: gate A of wave 1 is signed
> `[✓ 2026-08-21 andres]` at **61 checks · 0 failures**, and that number stands.
>
> What was later discovered: of those 61 checks, **two could have passed on a `grep` that
> never ran.** Both compared a counter against the expected value `0`, and both had a
> degenerate value of `0` — so a `grep` that exited ≥ 2 and printed nothing was
> indistinguishable from a `grep` that ran and found nothing (I-028). One of the two was
> `files DEFINING CI_ALLOW_DEV`, which `CLAUDE.md` designates an automatic 🔴.
> Confirmed reproducible at `c3dc9f5`: with a probe file whose name contains a **space**,
> `xargs` word-split it into two non-existent paths, `grep` exited 2 silently, and the gate
> printed `0 | 0 | OK` and `61 checks · 0 failures`, exit 0 — while `tests/bin/no-ci-allow-dev`,
> on the identical tree, reported `definitions: 1`, exit 1.
>
> **Why the signature stands.** The defect was in the check's *power*, not in its *result*.
> On the tree that was signed, the invariant independently confirms the same verdict
> (`definitions: 0`, `findings: 0`), and — decisively — **the repaired runner reproduces the
> same count on the same tree: 61 checks · 0 failures.** An instrument that can now say "no"
> says "yes" to the reading that was signed.
>
> Repaired by **T-316** (2026-08-22): 28 `rc >= 2` guards across 27 call sites in 9 files,
> plus the removal of two fallback-zero sites. Clean-path output verified **byte-identical**
> to `c3dc9f5`. Lessons recorded as **I-028** and **I-029**.

---

## Wave 2 · Environment and CI

- [ ] **T-201** · Reproducible DDEV configuration (≥ 1.25.0), documented in the README.
      *Success:* `ddev start` from scratch on a clean machine, with no manual steps.
- [ ] **T-202** · Review `.gitlab-ci.yml`: keep the `gitlab_templates` include, set only
      the necessary variables. *Success:* no job is defined by hand.
      > **Rider [orquestador] 2026-08-22, adopted by [ejecutor] under [andres]'s delegation —
      > amendment to the T-202 success criterion.** The criterion *"no job is defined by hand"* was
      > written against a failure mode the DA documents: a project that copies `gitlab_templates`'
      > jobs into its own file and then drifts from upstream. It was never meant to forbid Ágora
      > from testing something upstream does not test at all — and as written it forbids the only
      > available fix for the open blocker *"`tests/bin/` runs in no CI"*. **A criterion that
      > forbids its own remedy has stopped describing the goal** (I-020).
      > **Amended criterion:** no job may redefine, copy or override a job that `gitlab_templates`
      > provides; the `include:` block stays byte-identical to upstream and upstream jobs are
      > configured **only** through documented `variables:`. Ágora **MAY** add jobs that upstream
      > does not provide at all, and each such job must (a) carry a comment naming which upstream
      > job would otherwise cover it and why none does, (b) print real counts, and (c) appear in
      > T-203's inventory.
      > *Success, checkable:* `.gitlab-ci.yml` contains exactly one `include:` block, unchanged from
      > upstream's three files; the set of locally defined job keys is **disjoint** from the set
      > `gitlab_templates` defines; every locally defined job prints a count.
- [ ] **T-203** · Read `include.drupalci.variables.yml` and document in the README which jobs remain
      active (phpcs, phpstan, cspell, eslint, stylelint, phpunit). *Success:* a real list, not an assumed one.
- [ ] **T-204** · Create `.cspell-project-words.txt` with the project vocabulary.
      *Success:* the cspell job passes without disabling it.
- [ ] **T-205** · First green pipeline in the working repo. *Success:* number of jobs executed > 0 and
      the phpunit job's log shows `--fail-on-empty-test-suite` in the executed command line (T-214c), and
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
- [✓ 2026-08-21] **T-209** · Invariant: `CI_ALLOW_DEV` is not defined in any versioned file.
      *Success:* 0 matches, printing the number of files scanned (> 0).
      > **Note (rider [andres] 2026-08-21):** *"specified as 'not DEFINED', never 'not mentioned' —
      > see I-018."*
      > **Rider [ejecutor] 2026-08-21:** signed **out of order** — T-209 is a wave 2 task closed
      > inside wave 3. The tick is NOT wave 2 progress: wave 2 remains blocked by the
      > T-201/T-207/T-208 conflict. Dirty case run 2026-08-21 (T-312): mentions 7→8,
      > definitions 0→1, `RequirementsTest.php` correctly remaining a mention.

- [✓ 2026-08-22] **T-210** · 🔒 **Retroactive record.** `tests/bin/doctor` (D-019 rider b) was
      delivered in commit `1954a44` with no task number. It detects the platform and **exercises**
      every required tool rather than locating it (I-026), reports what is missing and how to
      install it, and is the first step of `/retomar`. Recorded here rather than left unnumbered:
      work that no task owns is work no gate can be held to.
      *Success (as delivered):* `tests/bin/doctor` exists and is executable; it reports
      `CLI present, daemon unreachable` for a down Docker daemon rather than passing silently.
- **T-211** · **NOT ISSUED.** Recorded so that a future reader does not hunt for a missing task.
      The number was skipped when T-212 was referenced by number in dispatch before it was written
      to disk. Append-only forbids renumbering; a recorded gap is cheaper than a rename that makes
      the transcript lie.
- [✓ 2026-08-22] **T-212** · 🔴 **Audit of the GitHub Actions run history — the finding that
      reopened wave 4.** Executed with `gh` against every run of `.github/workflows/phpunit.yml`.
      **Nine of the ten green runs executed ZERO tests.** `1b4a48f` (2026-08-21 10:05) reported
      `Tests: 3, Assertions: 36`; every run from `577b23e` (10:20) onward, including `e54caa3`
      (2026-08-22 13:07) on the then-current HEAD, reported `No tests executed!` **and exited 0**.
      Bisected to the single relevant commit between them: `975b263`, which added
      `/tests export-ignore` at `.gitattributes:18`.
      **Mechanism, verified at source 2026-08-22, not inferred:** the workflow installs the package
      as a path repository with `COMPOSER_MIRROR_PATH_REPOS=1`; Composer's `PathDownloader`
      (`:157-160`) mirrors through `ArchivableFilesFinder`, which chains `GitExcludeFilter` and
      `ComposerExcludeFilter` (`:26,60-61`) and therefore honours `export-ignore`. `tests/` never
      reached `recipes/agora_transparency`; PHPUnit found nothing and returned success.
      **Consequences recorded, not softened:** `RequirementsTest` — the assertion that decides
      whether this package is publishable at all — had not executed for two days; T-406 had no
      evidence; the `Tests: 3` of `1b4a48f` were the **kit's own** tests under the kit's name,
      predating the rename in `e6197ed`.
      *Success:* the mechanism reproduced end to end from source, the responsible commit named, and
      the class recorded as **I-032** rather than patched as an instance.
- [✓ 2026-08-22] **T-213** · 🔴 Restore test execution in `.github/workflows/phpunit.yml` by copying the tests
      into the installed package, mirroring `gitlab_templates`' `.recipe-replace-symlinks`
      (`include.drupalci.main.yml:1605-1611`). **`/tests export-ignore` is NOT removed** (D-020(d)).
      The copy step is the **last** step before phpunit — any `composer` command after it re-mirrors
      and silently deletes the tests again; that trap is written into the file as a comment.
      `--testdox` is added, so the log names each test instead of counting them.
      *Success:* the workflow step prints `3` for
      `find "$RECIPE_DIR/tests" -name '*Test.php' | wc -l` and FATALs if
      `tests/src/Kernel/RequirementsTest.php` is absent; and the run for the resulting commit
      reports `Tests: N, Assertions: M` with **N ≥ 3** and **no** `No tests executed!`, with
      `RequirementsTest`, `InstallTest` and `ValidationTest` all named in the log. Counts quoted
      from `gh run view <RUN_ID> --log`, never from the badge.
      **Owns the automatic 🔴 raised by T-212. If it cannot produce a count, the workflow is
      deleted under D-020 rider (a), not kept.**
      *Evidence, read from the log and not the badge — run `32582950414`, `1a09e96`, conclusion
      `success`:* copy step printed **`3`** for `find "$RECIPE_DIR/tests" -name '*Test.php' | wc -l`;
      **`Tests: 3, Assertions: 38, Deprecations: 125.`**; verdict line `OK, but there were issues!`
      with **0** occurrences of `FAILURES!` or `ERRORS!` — the `⚠` on `Apply` is PHPUnit's issue
      marker for deprecations, not a failure; **0** occurrences of `No tests executed`; `--testdox`
      named all three — `✔ Install`, `✔ Site template requirements`, `⚠ Apply`.
      **The paths are the part that matters:**
      `/var/www/html/recipes/agora_transparency/tests/src/Functional/{InstallTest,ValidationTest}.php`
      — resolved from the **installed package under Ágora's own name**, unlike the `Tests: 3` of
      `1b4a48f`, which predated the rename in `e6197ed` and was the kit testing itself.
      The lane predicted `3` and `N ≥ 3` before the run and matched exactly, which is what makes
      this a verification rather than a hope (I-030).
      Verified independently by `orquestador` 2026-08-22, re-reading the log with `gh run view
      32582950414 --log` and re-deriving every count above.
      > **Note — the TRAP comment is documentation, not the guard.** The guard against a `composer`
      > command being added after the copy step is `--fail-on-empty-test-suite` (T-214a): the
      > recurrence would turn the build **red**, exactly as run `32583207616` demonstrates. The
      > comment buys diagnosis speed, not detection. A mechanical guard (a YAML step-ordering rule
      > in `no-blind-phpunit`) was considered and **rejected**: it would mean building a
      > YAML-ordering parser for a file that D-020 rider (a) schedules for deletion or reduction at
      > unit 007 — engineering around a disposable shim, which is precisely what I-035 warns
      > against. Recorded as a decision, so nobody re-opens it as an oversight.
      > **Note — the deprecation that touches Ágora's own `recipe.yml`, settled at source.** Of the
      > 125 deprecations, **0 originate in Ágora**. One warranted checking rather than assuming:
      > *"Using the `simpleConfigUpdate` config action on config entities is deprecated in
      > drupal:11.2.0 and **throws an exception in drupal:12.0.0**"*, attributed in the log to
      > `InstallTest::testInstall (2 times)` — and Ágora's `recipe.yml` uses `simpleConfigUpdate`
      > exactly **twice** (`:120`, `:125`). **That numeric coincidence is not attribution** (I-037).
      > Settled by reading the trigger, `SimpleConfigUpdate.php` @ `11.4.x`:
      > `if ($this->configManager->getEntityTypeIdByName($configName)) { @trigger_error(…); }`
      > — it fires **only** when the config name maps to a config **entity type**. Ágora's two uses
      > target `system.site` and `system.theme`, which are **simple config objects with no entity
      > type**, so they **provably cannot** trigger it. Not our defect; it belongs to a Drupal CMS
      > recipe Ágora composes, and it is carried in the blockers table, not as a task here.
- [✓ 2026-08-22] **T-214** · The invariant that was missing: **a PHPUnit run that executed zero tests must fail
      the build.** Two layers, neither sufficient alone.
      **(a)** `--fail-on-empty-test-suite` on the workflow's phpunit invocation. Verified at source
          in PHPUnit 11.5: `ShellExitCodeCalculator` →
          `if ($failOnEmptyTestSuite && !$result->hasTests()) { $returnCode = FAILURE_EXIT; }`.
      **(b)** `tests/bin/no-blind-phpunit`: scans the working tree (never `git grep` — I-018) over
          `.gitlab-ci.yml` and `.github/workflows/*.yml`; finds phpunit invocations and
          `_PHPUNIT_EXTRA` assignments; reports as findings any unguarded invocation, any
          `--do-not-fail-on-empty-test-suite`, and `_PHPUNIT_CONCURRENT` set to `1` (which routes
          `_PHPUNIT_EXTRA` to `run-tests.sh` and voids the guard silently). FATALs on
          `files scanned == 0` and on `phpunit invocations == 0`, both with defaulted inputs
          (I-031) and no summary line printed. `wc -l`, never `grep -c`, for any value feeding a
          comparison — T-321's house rule adopted at birth rather than retrofitted.
      **(c)** `.gitlab-ci.yml` gains `variables: _PHPUNIT_EXTRA: '--fail-on-empty-test-suite'` and
          does **not** set `_PHPUNIT_CONCURRENT` (default `'0'`). Depends on the T-202 rider above.
      *Success:* clean tree → exit 0,
      `files scanned: 2 · phpunit invocations: 2 · guarded: 2 · unguarded: 0`; four dirty cases in
      the **same commit** (D-019 rider e) — flag removed → exit 1 with `file:line`; negation flag →
      exit 1; `_PHPUNIT_CONCURRENT: '1'` → exit 1; scope collapse in a throwaway copy → exit 1 with
      `FATAL: phpunit invocations: 0 …` **and no summary line** — each reverted with
      `git status --porcelain` empty. Added to `gate-a-wave3.sh` as two checks:
      **29 → 31 checks · 0 failures**, stated as a number so the change cannot be silent.
      `gate-a-wave1.sh` unchanged at **61 · 0**.
      *Evidence, reproduced independently by `orquestador` 2026-08-22 in a throwaway copy, with
      real exit codes captured directly (never through a pipe — a `tail` in the pipeline returns
      its own status and would have reported `exit=0` for every dirty case, which is I-028 one more
      time and is how this matrix was nearly mis-certified):*
      clean → exit 0, `files scanned: 2 · phpunit invocations: 2 · guarded: 2 · unguarded: 0 ·
      findings: 0`; **D1** flag removed → exit 1, 1 finding, `unguarded: 1`; **D2** negation flag →
      exit 1, 3 findings; **D3** `_PHPUNIT_CONCURRENT: '1'` → exit 1, 1 finding; **D4** scope
      collapse → exit 1, `FATAL: files scanned: 0 …`, **and no summary line printed**.
      `git status --porcelain` empty after the matrix. `gate-a-wave3.sh` **31 checks · 0 failures**;
      `gate-a-wave1.sh` unchanged at **61 checks · 0 failures**.
      > **Scope of this signature, stated so it is not read wider than it is.** Layers **(a)** and
      > **(b)** are proven, in CI and locally respectively. Layer **(c)** — that
      > `_PHPUNIT_EXTRA: '--fail-on-empty-test-suite'` actually reaches the phpunit binary on a
      > GitLab runner — is verified **statically only**: `gitlab_templates`
      > `include.drupalci.main.yml:1638` appends `$_PHPUNIT_EXTRA` to the phpunit invocation, and
      > `_PHPUNIT_CONCURRENT` defaults to `'0'`, which is the branch where phpunit options are
      > legal. **No run has observed it**, and none can until the drupalcode project exists.
      > Carried by **T-205**, whose criterion is extended accordingly. Debt with an owner and an
      > exit gate (I-020), not an unmet criterion: this task's criterion never claimed a GitLab
      > run, because none was available to claim.
- [✓ 2026-08-22] **T-215** · Prove T-214 in the place it has to work: a CI run that executes nothing must go
      **red**. Executed by an actor other than whoever wrote T-213/T-214 (I-030).
      *Success:* on a throwaway branch with only the T-213 copy step removed, `gh run watch
      --exit-status` returns **non-zero** and the log names the cause (`No tests executed!` or
      `FATAL: tests did not reach …`); the branch is deleted locally and on the remote afterwards.
      **Until this run exists, T-214 is decoration, not an invariant** (I-027).
- [ ] **T-216** · The record for this turn, in one commit: sign **D-020** (a/b/c/d); append the
      T-202 criterion rider; append **I-032…I-035**; the risk-status update under `plan.md` §7; the
      gate A job list in `CLAUDE.md`.
      *Success:* `grep -c 'D-020' specs/000-proyecto/DECISIONES.md` ≥ 1;
      `grep -cE '^- I-03[2-5]' specs/000-proyecto/IDIOMS.md` = 4; no line deleted from either file.

      *Evidence — run `32583207616`, `3af759e`, branch `ci/prove-empty-suite-fails`, conclusion
      `failure`, `gh run watch --exit-status` → **1**:* `No tests executed!` ×1;
      `Failed to execute command … --fail-on-empty-test-suite …: exit status 1`;
      `##[error]Process completed with exit code 1.` The change was `+0/−26` on one file: only the
      copy step removed, the flag kept. **The same `No tests executed!` line that accompanied nine
      green runs, opposite outcome.** Branch deleted locally and on the remote; tree clean.
      > **Correction to this task's own evidence, recorded rather than quietly dropped
      > (`orquestador`, 2026-08-22).** The hand-off also cited *"`FATAL: tests did not reach …`
      > appears **0 times**, so it failed for the correct cause."* **That check was vacuous:** the
      > copy step had been deleted, so the string could not appear at all — not even as CI's echo
      > of the step's own script, which is where it appears **once** in the clean run. Its
      > degenerate value equalled its expected value. **The verdict is unaffected**, because it
      > never depended on that check: the positive evidence — `No tests executed!` present, and
      > phpunit's own exit status naming the flag — is what carries it. Recorded because this is
      > **I-028's sixth appearance and its first outside `tests/bin/`**: the class has escaped from
      > our scanners into how we read logs. See I-038.

**Gate A wave 2**
```bash
ddev start && ddev drush status          # expected: Drupal bootstrap = Successful
# On GitLab: the pipeline of the latest commit, green, with the number of jobs in view
```
**Gate B wave 2** 👤 · Andrés confirms the split of tests between drupalcode and GitHub.
Sign here: `[ ]`

---

## Wave 3 · Invariants (parallelizable with wave 2 — disjoint files)

- [✓ 2026-08-21] **T-301** · `tests/bin/no-unstable-deps` according to the spec in `plan.md` §6.
      *Success:* it detects a deliberately injected `-beta` and does **not** flag the starter kit.
- [✓ 2026-08-21] **T-302** · `tests/bin/no-patches`. *Success:* it detects an injected `patches` section.
- [✓ 2026-08-21] **T-303** · `tests/bin/no-secrets` over the whole repo except `.git/`.
      *Success:* it detects a fake key injected in `config/` and another in `content/`.
- [✓ 2026-08-21] **T-304** · `tests/bin/sbom-check` against `updates.drupal.org` (method in research §10.4).
      *Success:* it requires stable + `<security covered="1">` + a line in `DECISIONES.md`; it fails if one is missing.
- [✓ 2026-08-21] **T-305** · All four print scope, number of files scanned and number of findings.
      *Success:* none reports "0 files scanned".
- [✓ 2026-08-21] **T-306** · **Amendment to the T-304 method** (`sbom-check`): the endpoint returns **HTTP 200
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
- [✓ 2026-08-21] **T-307** · `tests/bin/no-code-in-template`: mirrors the `RequirementsTest` assert locally.
      *Success:* it prints `N files scanned · 0 *.info.yml files`, N > 0; it detects an injected
      `themes/x/x.info.yml`; clean tree after reverting.
- [✓ 2026-08-21] **T-308** · Translate `tests/bin/gate-a-wave1.sh` to English. It is code, and D-017 covers
      code; T-113 enumerated `CLAUDE.md`, `.claude/*` and `specs/` and left it out.
      **This blocks T-204:** the cspell job is on by default in `gitlab_templates`, and the
      shortcut to green would be dumping Spanish words into `.cspell-project-words.txt` — which is
      adding a file to an ignore list to turn a gate green, an automatic 🔴.
      *Success:* 0 matches for the Spanish token list, **and the gate still reports
      `61 checks · 0 failures`** — same N, same M: it is a translation, not a change of checks.
- [✓ 2026-08-21] **T-309** · `tests/bin/no-boilerplate` — a deny-list invariant over **every published
      artefact** (what `git archive` ships), for strings inherited from the starter kit:
      `electrician`, `WCAG AAA`, `GET-STARTED`, `MY_SITE_TEMPLATE_NAME`,
      `Site Template Starter Kit`, `my amazing site template`.
      > **Rider of [andres] 2026-08-21:** *"it runs after **a full initial sweep** over config, demo
      > content and descriptions — not just the README."*
      *Success:* it prints scope and the number of files scanned (> 0) and 0 findings; it detects an
      injected string; clean tree after reverting.
- [✓ 2026-08-21] **T-310** · 🔒 **T-403 is brought forward to wave 3** (rider of [andres] 2026-08-21):
      rewrite `README.md` in English describing Ágora, and resolve `recommended.yml`.
      > **Rider of [andres] 2026-08-21:** *"the README declares **WCAG 2.2 AA, verified — never
      > AAA** (I-023)."*
      *Success:* 0 findings from `tests/bin/no-boilerplate`; the "Development process" section from
      T-112 survives; no broken links.
      > **Note [ejecutor] 2026-08-21:** signed on **post-T-315 evidence**. The original 0 findings
      > were produced by `no-boilerplate` while it was a no-op (see T-315 / I-027). Re-verified
      > after the repair: 18 scanned, 7 deny terms, 0 findings. The four relative links
      > (`LICENSE.txt`, `screenshot.webp`, `specs/`, `specs/000-proyecto/DECISIONES.md`) were
      > resolved on disk; 2 external links (`ddev.com`) were not fetched.

- [✓ 2026-08-21] **T-311** · `tests/bin/sbom-check`: normalise CRLF at the three tool-output
      boundaries (`sys.stdout.reconfigure(newline='\n')` in the embedded parser; `| tr -d '\r'` on
      the `PROJECTS` and `LOCK_NAMES` jq pipelines). Portability only — no check, threshold,
      exclusion or finding condition altered.
      *Success:* exit 0 and `9 projects queried - 9 with coverage - 0 findings`, with
      `with stable release: 9`, `with a D-NNN line: 9`,
      `core_compatibility declared: 1 (8 n/a)`, `exclusions: none`. Verified deterministic over
      3 consecutive runs. See I-025.
- [✓ 2026-08-21] **T-312** · Dirty-case matrix for every invariant: 12 injections, each reverted,
      `git status --porcelain` empty after each.
      *Success:* every invariant fails with garbage inside and returns to exit 0 after the revert.
      Recorded: sbom-check clean → exit 0 9/9/9; non-existent project → exit 1 rejected on the
      `<error>` body (I-013 proven caught); `drupal/token` with no D-NNN → exit 1 while showing the
      package stable **and** covered, the sole finding being the missing justification; severed
      network → exit 1 `NETWORK UNAVAILABLE`. `no-secrets` hit **both** `config/` and `content/`,
      values never echoed. `no-ci-allow-dev`: mentions 7→8, definitions 0→1, with
      `RequirementsTest.php` correctly remaining a mention (I-018).
      > **Note:** this matrix **found T-315**. It is the task that earned its keep.
- [✓ 2026-08-21] **T-313** · `tests/bin/gate-a-wave3.sh`: single runner over all eight invariants,
      two checks each (exit 0 + non-zero parsed scope), `gate-a-wave1.sh` conventions. Preflight
      **exercises** 14 tools (`jq -n 1`, `python3 -c 'pass'`, …) rather than locating them (I-026).
      Parses each invariant's summary individually, as their wordings differ;
      `no-code-in-template` never prints "scanned", so its metric is `packaged: N entries`.
      *Success:* **28 checks - 0 failures**, exit 0 — **and proven falsifiable**: pointed at a
      non-existent invariant path in a throwaway copy it reported 28 checks - 2 failures, exit 1.
      > **Note:** supersedes the four-script loop in this file's "Gate A wave 3" block, which
      > enumerated only `no-unstable-deps no-patches no-secrets sbom-check` and left three
      > invariants in no gate at all. That block is left unedited as the historical record of what
      > the gate was when it was written; `gate-a-wave3.sh` is what it is now.
- [✓ 2026-08-22] **T-314** · Strip inherited starter-kit comments from the packaged `recipe.yml`
      and close the deny-list gap that let them through. Surveyed with `git blame` against the kit
      import (`e8d1fd3`) to separate untouched kit text from what Ágora had already rewritten,
      rather than by eye.
      **Removed:** the "this file MUST be named `recipe.yml`" packaging mechanic; the `recipes:`
      authoring rule about not building on another site template; the entire commented
      `drupal_cms_installer` block with its `example.com` placeholder URLs.
      **Kept, deliberately:** the schema documentation on `description`/`type`/`strict`/`install`
      (including the `type: Site` guard), every per-recipe description, and the Canvas mechanism
      notes — accurate, and true of Ágora. *Inherited* is not the test; *inaccurate or
      author-facing* is. No functional key touched.
      *Success:* `my-site-template` added to the `no-boilerplate` deny list (7 → 8) **and proven
      to fire on the uncleaned file before the cleanup** — 4 findings at `recipe.yml:189,192` —
      then quiet after: `scanned: 18 · deny-list terms: 8 · findings: 0`, exit 0. `recipe.yml`
      still parses, still `type: Site`, 10 recipes / 3 install unchanged.
      `gate-a-wave3.sh` 29 checks · 0 failures. Verified independently by `orquestador`
      2026-08-22, including re-injecting the exact removed string and confirming the term fires.
      > **Note (orquestador, 2026-08-22):** the removal of the *"you shouldn't build a site
      > template on top of another site template"* rule is accepted, but the rule is now recorded
      > in **no file under `specs/`** and it becomes load-bearing at D-011 rider (b), when
      > functional areas are extracted into independent contrib recipes. It is upstream guidance
      > from the kit, **unverified by us**. T-320(b) owns recording it — verified, or explicitly
      > marked unverified. The test applied ("is it a checkable constraint recorded in `specs/`?")
      > is the wrong test for a comment in a shipped file; the right one is "is it true, and
      > useful to whoever reads this file?"
- [✓ 2026-08-21] **T-315** · 🔴 `tests/bin/no-boilerplate` was a **total no-op**: line 119 used
      `grep -Iin -F`, and GNU grep 3.0 on this host SIGABRTs on `-F` combined with `-i`. Repair:
      drop `-F`, keep `-Iin`. See I-027.
      *Success:* clean tree → exit 0, `scanned: 18 · deny-list terms: 7 · findings: 0`;
      `electrician` appended → exit 1, 1 finding at `README.md:161`; **all seven terms appended →
      exit 1, 7 findings, lines 161-167**; reverted → exit 0.
      > **Note:** this invalidates the *original* evidence for **T-310**, whose criterion is
      > "0 findings from `tests/bin/no-boilerplate`" — that zero was produced by the no-op.
      > T-310 is signed on **post-T-315 evidence**, re-verified 2026-08-21.
- [✓ 2026-08-22] **T-316** · The class behind T-315: 28 `rc >= 2` guards across 27 call sites in
      9 files; helpers running inside command substitutions write a marker honoured by
      `assert_grep_ok` **before any summary is printed** (an `exit` there would end only the
      subshell); `no-boilerplate` gains a deny-list preflight that compiles every term in the
      parent shell, plus per-file exit status, each proven to fire with the other disabled; the
      two gate runners use **sentinels, not aborts** (I-029); the incorrect T-315 comment is
      corrected in place, stating which of its claims was false.
      Two residual holes found by the `tester` and closed in the same wave:
      **R2** `FINDINGS=$(grep -cve '^$' "$HITS_FILE") || FINDINGS=0` printed `findings: 0`,
      exit 0, with a real finding present, and `gate-a-wave3.sh` reported 28 checks · 0 failures
      — invisible at every level; replaced by `wc -l`, which **deletes** the failure mode rather
      than guarding it. **R1** `no-boilerplate` was the only invariant with no `scanned > 0`
      self-guard despite its header claiming that contract.
      *Success:* every scanning grep treats rc ≥ 2 as FATAL; the `[unclosed` deny-term injection
      fails loudly (`FATAL: deny-list term is not a pattern grep can compile (exit 2)`, exit 1);
      `gate-a-wave3.sh` still 28 checks · 0 failures on a clean tree; the two silent passes at
      `c3dc9f5` now FAIL (`61 checks · 1 failures`, exit 1); **clean-path output byte-identical
      to `c3dc9f5`** across all invariants. Verified independently by `orquestador` 2026-08-22.
- [ ] **T-317** · Determine whether the `-Fin` abort, and the toolchain assumptions generally, hold
      on **every platform this project is developed or gated on** — Docker was unavailable on
      2026-08-21, so this is **unverified in every direction** and must not be assumed either way.
      Three platforms are in play, not two:
      1. **Windows dev host** — measured 2026-08-21: GNU grep 3.0 aborts on `-Fin` (rc 134);
         jq 1.8.2 and python 3.12.6 emit CRLF on stdout. Both defects are repaired at the
         call site (T-311, T-315), not worked around at the tool level.
      2. **CI / DDEV image** — unmeasured.
      3. **macOS dev host** — unmeasured, and it is a *different* question, not the same one:
         macOS ships **BSD grep**, not GNU grep, so its `-Fin` behaviour, its exit-status
         semantics and its handling of the BRE patterns T-315 introduced are all open. A Homebrew
         `ggrep` earlier on `PATH` changes the answer again. **Measure it; do not reason about it**
         — the whole point of I-027 is that this class of defect fails green.
      *Success:* `grep --version` and the `-IFin` return code recorded for all three; minimum
      versions for `grep`, `jq`, `python3` recorded in the README as a toolchain floor; the
      dirty-case matrix (T-312) re-run in full on each platform declared first-class by D-019 —
      a no-op on one platform is a no-op nobody sees.
      **Blocked by D-019.**

- [✓ 2026-08-22] **T-318** · Close the two survivors of T-316, both of the I-028 shape.
      **(a)** `gate-a-wave3.sh` now asserts `no-boilerplate`'s deny-term count positive, parsed
      through the existing `extract_count` helper from the invariant's own summary — **not pinned
      to a number**, so a legitimate change to the deny-list size cannot break it — and added to
      **both** the if and the else branch so the total is consistent regardless of failure mode.
      **(b)** `sbom-check`: of 3 `grep_failed` call sites, the two in `decision_line()` duplicated
      the pattern literal and now pass `"$_pat"`; the third, in `line_of()`, already passed the
      same variable used in its own grep and was left alone and reported as sound.
      *Success:* deny-term count asserted positive; no `grep_failed` site repeats a pattern
      literal; runner still 0 failures on a clean tree — **29 checks · 0 failures**. Falsifiability
      verified independently by `orquestador` 2026-08-22: with the deny list emptied,
      `no-boilerplate (deny terms) 0 | >0 | FAIL` → **29 checks · 1 failures, exit 1**.
- [✓ 2026-08-22] **T-319** · Pin shell scripts to LF on checkout: `tests/bin/** text eol=lf` and
      `*.sh text eol=lf` in `.gitattributes`, in **its own section, explicitly labelled a checkout
      attribute and not filed under the `export-ignore` heading** (I-021: the two do entirely
      different things and conflating them in one list is how they get confused). `core.autocrlf=true`
      is set at **system** scope by the Git for Windows installer, so a fresh clone checked these
      scripts out as CRLF on any default Windows machine. No `git add --renormalize` (the index was
      already LF); never a blanket `* text=auto`.
      *Success:* `git check-attr text eol -- tests/bin/no-boilerplate` → `text: set / eol: lf`,
      while `recipe.yml`, `composer.json` and `.gitattributes` remain `unspecified`.
      Scope confirmed narrow by `orquestador` 2026-08-22.
- [✓ 2026-08-22] **T-320** · The last three loose ends of wave 3.
      **(a)** `no-boilerplate` now **self-guards its deny-term count**, immediately after the
      existing `SCANNED` guard, same idiom and message shape: this invariant's scope is
      *files × terms*, so zero terms is a degenerate scope exactly as zero files is. The
      `gate-a-wave3.sh` assertion from T-318(a) **stays** — defence in depth, not a replacement.
      **(b)** The *"a site template is not built on another site template"* rule, dropped by
      T-314, is recorded in `recipe.yml`'s seam-convention block — **not** in `ROADMAP.md`, which
      on inspection has no D-011(b) extraction note at all (only a wave-001 blocker line and a
      risk row); the location named in this task did not exist, and the seam block is where a
      reader editing `recipes:` during the extraction is already looking. It cites `T-101` rather
      than the upstream machine name so as not to trip its own deny list inside a packaged file.
      It came back **verified, not hedged**: confirmed 2026-08-22 at two independent sources — the
      upstream recipe this repository was copied from, and Drupal.org issue **3534752**,
      *"RFC: The architecture and philosophy of site templates"*. A **third source was silent**:
      the Recipes Initiative "Recipe Author Guide" does not mention the rule. That 2-of-3 result
      is why the shipped wording says *SHOULD-NOT design choice … rather than a rule enforced by
      the installer's own mechanics* instead of flattening it to "confirmed".
      **(c)** A note is appended beside the **Gate A wave 1** block — the signed block itself
      unedited, same treatment as the superseded wave 3 gate block — recording that its
      `python3 -c "import yaml…"` one-liner fails on any host whose Python defaults to a
      non-UTF-8 encoding, and why gate A deliberately does not parse YAML.
      *Success:* emptied deny list → `FATAL: deny-list terms: 0 - the scope collapsed to
      nothing.`, exit 1, **and no summary line printed**; `recipe.yml` still parses
      (`type: Site`, 10 recipes / 3 install / 22 config actions, unchanged);
      `gate-a-wave1.sh` **61 · 0**; `gate-a-wave3.sh` **29 · 0**; `no-boilerplate`
      `scanned: 18 · deny-list terms: 8 · findings: 0`.
      Verified independently by `orquestador` 2026-08-22, including re-running the (a) probe and
      re-deriving source 1 of (b) verbatim from the upstream URL.
      > **Note (orquestador, 2026-08-22) — the limit of the (a) guard, recorded so nobody reads
      > "self-guarded" as absolute.** It catches `TERM_COUNT == 0`, which is the reachable case.
      > It does **not** catch a *blank* `TERM_COUNT`: verified, `[ "" -eq 0 ]` exits 2 with
      > `integer expression expected` and the branch is simply **not taken**, so execution
      > continues past the guard — and `grep -c` provably emits blank on rc >= 2. No reproduction
      > exists for that path here (hardcoded pattern, in-memory input), so it is **not** a finding.
      > It is the fifth appearance of the I-028 class and is escalated as a pattern, not patched
      > as an instance: **T-321**.
- [ ] **T-321** · 🔒 **Pattern escalation, unit 002 — deliberately NOT wave 3 work.** Five times
      now the same class has surfaced in a different place (`-Fin` no-op → fallback zeros →
      expect-zero gate checks → the deny-term counter → blank-vs-zero). Each individual patch was
      correct; the recurrence is the finding. Stop patching sites and close the class by
      construction, using the move this codebase has already proven at T-316/R2 — **delete the
      failure mode rather than guard it**:
      (a) inventory every counter in `tests/bin/` and replace `grep -c` with `wc -l` wherever the
          value feeds a comparison, so a broken scanner cannot yield a non-numeric value;
      (b) default every numeric guard input (`[ "${X:-0}" -eq 0 ]`), so a blank trips the guard
          instead of skipping it — see I-031 for why that defaulting is safe *here* and unsafe in
          the cases I-028 warns about;
      (c) state both as a house rule in the invariant header convention, so the next invariant
          inherits it instead of rediscovering it.
      *Success:* no `grep -c` in `tests/bin/` feeds a comparison; every numeric guard defaults its
      input; a blank injected into any counter produces a loud failure, never a skipped guard;
      `gate-a-wave1.sh` still 61 · 0 and `gate-a-wave3.sh` still 29 · 0 on a clean tree, with
      clean-path output byte-identical to the commit before the sweep.
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
- [⏸ deferred 2026-08-22 → unit 002 (content model)] **T-402** · 🔒 **Extending
      `InstallTest`/`ValidationTest` with Ágora's key routes presumes Ágora has routes. It has
      none.** There is no content model until unit 002/003: every route the site serves today comes
      from the Drupal CMS base recipes, so assertions written now would pass with Ágora absent —
      a test that cannot fail for the reason it claims to test, which is this unit's signature
      defect in test form (I-032). Deferred with an owner rather than written weak.
      Deferred by **[ejecutor] under [andres]'s explicit delegation** of 2026-08-22; it changes
      unit 001's signed scope, and the record says who decided it.
      *What survives in unit 001:* the three kit tests execute unmodified against the installed
      package and report real counts — **T-406**, signed below.
      *Success (unit 002):* `ValidationTest::testApply` asserts on at least one route, block or
      config entity that **Ágora's own `recipe.yml` creates**, and the assertion is proven to fail
      on a site where the Ágora recipe has not been applied.
      *Original text:* Extend `InstallTest`/`ValidationTest` with Ágora's key routes.
      *Success:* number of tests and assertions reported, > 0.
- [ ] **T-403** · Project README **in English** (public docs in English, D-005): what it is, how it is
      installed, what it ships.
      > **Note 2026-08-21:** superseded in wave 3 by **T-310**; T-403 keeps whatever is left over
      > for wave 4.
- [ ] **T-404** · `orquestador` audit (read-only): standards, SBOM, licences, marketplace
      requirements. *Success:* verdict with no open 🔴.
- [ ] **T-405** · Promote the unit's lessons to `IDIOMS.md`.
- [✓ 2026-08-22] **T-406** · Verify that `InstallTest`, `ValidationTest` and `RequirementsTest` pass **without
      being modified**. *Success:* 0 lines deleted in those 3 files; phpunit output with number of
      tests **and** assertions.
      *Evidence — run `32582950414`:* `Tests: 3, Assertions: 38`, both halves of the criterion met
      for the first time; `git diff --stat 1b4a48f..HEAD -- tests/src/` is **empty** — zero lines
      touched in `InstallTest.php`, `ValidationTest.php` and `RequirementsTest.php` since the kit
      import. `RequirementsTest` (`✔ Site template requirements`) executed against the **installed
      package** — the first execution of Ágora's publishability oracle, and the first since
      2026-08-21.
      > **Rider [orquestador] 2026-08-22 — signed OUT OF ORDER.** T-406 is a **wave 4** task closed
      > while wave 4 is **not open**, on the precedent of T-209 (a wave 2 task closed inside wave
      > 3). **This tick is NOT wave 4 progress.** Wave 4 remains closed: T-401 needs a Docker
      > daemon that is unreachable on the dev host, T-402 is deferred to unit 002, and
      > T-403/T-404/T-405 are untouched. It is signed here rather than held because the evidence
      > exists now and holding it would mean the record understating what has been proven —
      > which is the failure this whole episode was about.

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
> Last updated: 2026-08-22, after T-213/T-214/T-215/T-406 were signed.

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
| **Wave 2 deadlock** | ✅ **RESOLVED 2026-08-22 by D-019** · the incompatibility was two needs conflated: running the invariants (→ container) and running the install smoke (→ a Drupal site set up separately, this package added as a path repository — the T-207 flow, already executed by `.github/workflows/phpunit.yml`). **T-201 superseded by T-207**; **T-208 redefined** to version the gate's container definition, not a `.ddev/config.yaml` for a site this repository does not contain. Task-level rewiring owned by the `orquestador` | — | — |
| T-205 first green pipeline | 🔴 **OPEN** · **there is no project on drupalcode** (`git.drupalcode.org/api/v4/projects/project%2Fagora_transparency` → **404**, verified 2026-08-21). Without a project there is no pipeline, no canary MR (D-009 rider b) and nothing to apply D-009 to | T-205, and the application of T-206 | 👤 Andrés (creates the project) |
| **`phpunit.yml` executed ZERO tests** | ✅ **CLOSED 2026-08-22 by T-213** · run `32582950414` reports `Tests: 3, Assertions: 38` with 0 occurrences of `No tests executed`, paths resolving under `recipes/agora_transparency/`; the recurrence is guarded by `--fail-on-empty-test-suite` and proven red by T-215 (run `32583207616`, exit 1) | — | — |
| **`_PHPUNIT_EXTRA` guard unobserved** | 🟡 **OPEN** · T-214(c) is verified statically (`include.drupalci.main.yml:1638` appends it; `_PHPUNIT_CONCURRENT` defaults `'0'`) but **no run has seen it**. Folded into T-205's criterion: the first pipeline's phpunit job log must show `--fail-on-empty-test-suite` in the executed command line | T-205 | 👤 Andrés (creates the project) |
| **`simpleConfigUpdate` → exception in Drupal 12** | 🟡 **OPEN** · an upstream Drupal CMS recipe that Ágora composes calls `simpleConfigUpdate` on a config **entity**; deprecated in 11.2, **throws** in 12.0 (verified at source). **Not Ágora's** — its two uses target `system.site` and `system.theme`, simple config with no entity type, which cannot trigger it (I-037). Re-checked at unit 007's dependency review | unit 007 | — |
| T-106 theme approach | ⏸️ DEFERRED to unit 002 (see T-110) | — | — |
| definitive `screenshot.webp` | ⏸️ DEFERRED to unit 003 (see T-114) · provisional placeholder in its place, deliberately does not imitate a real site | — | — |
| **`tests/bin/` runs in no CI** | 🔴 **OPEN** · the eight invariants execute only when a human types them; `no-boilerplate` was a no-op for two commits and only a hand-dispatched injection caught it. The criterion clash is **RESOLVED 2026-08-22** by the T-202 rider (Ágora may add jobs upstream does not provide). The blocker itself still needs a drupalcode project (T-205); automatic 🔴 at unit 001 closure (T-404) if it reaches wave 4 unowned | T-404 | — |
| T-316 grep rc-blindness | ✅ **CLOSED 2026-08-22** · signed — 28 `rc >= 2` guards across 27 call sites in 9 files; residuals R1/R2 closed in the same wave; clean-path output byte-identical to `c3dc9f5` | — | — |
| T-317 toolchain floor | 🟡 **OPEN** · unblocked by D-019, which requires the floor to be pinned and documented · `-Fin` and the CRLF assumptions are measured **only on the Windows dev host**. The CI/DDEV image and the **macOS dev host** are both unmeasured, and macOS is a different question (BSD grep, not GNU). Do not assume either covers for the other | — | blocked by D-019 |
| **second dev host (macOS)** | 🟡 **OPEN** · a second agent works this repo from a Mac. The wave 3 gate is certified on the Windows host **only** (see the closure note): its toolchain floor, its `grep` flavour and its line endings are all unverified there. First action on that host is T-317's measurement, before trusting any invariant's green | T-317 | — |
| T-314 packaged `recipe.yml` boilerplate | ✅ **CLOSED 2026-08-22** · signed; deny list 7 → 8, proven to fire on the uncleaned file before the cleanup | — | — |
| `.gitattributes` `text eol=lf` | ✅ **CLOSED 2026-08-22 by T-319** · `tests/bin/** text eol=lf` and `*.sh text eol=lf`, in their own labelled section, scope confirmed narrow | — | — |
| PHP 8.4 **ZTS** on the dev host | 🟡 **OPEN** · `gitlab_templates` and the Drupal CLI assume NTS. Inert today (no invariant executes PHP; `composer validate --strict` exit 0); surfaces at **T-406** | T-406 | — |
