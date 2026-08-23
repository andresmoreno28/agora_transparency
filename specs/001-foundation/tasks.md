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
      `grep -c 'recipes/agora_base' specs/000-project/plan.md` = 0;
      `grep -c 'D-014' specs/000-project/DECISIONS.md` ≥ 1.
- [✓ 2026-08-21] **T-108** · Append I-011…I-017 to `IDIOMS.md`.
      *Success:* `grep -cE '^- I-01[1-7]' specs/000-project/IDIOMS.md` = 7; no previous line deleted.
- [✓ 2026-08-21] **T-109** · Dated research `specs/001-foundation/research/2026-08-21-flujo-tema-y-marketplace.md`.
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
- [✓ 2026-08-21] **T-115** · Amend D-008 in `DECISIONS.md` with the real `Plugin.php` code, and record D-009
      and D-018. *Success:* `grep -c 'D-018' specs/000-project/DECISIONS.md` >= 1;
      `grep -c 'onPackageInstall' specs/000-project/DECISIONS.md` >= 1.
      > **Note:** added after wave 1's gate A closed (61 checks / 0 failures). It is a
      > process-layer task: it touches no artefact the wave 1 gate measures, so it does not
      > reopen that gate.

- [✓ 2026-08-23] **T-116** · Apply **D-021** to the packaged identity strings, and correct the two README
      statements that the project's creation made false the same day.
      **Identity (full name `Ágora Transparency`):** `recipe.yml` `name:` · `README.md` H1 and first
      prose mention · `AGENTS.md` audience header and first body mention · `recommended.yml` header.
      **Both `description` fields drop the name** (`recipe.yml` and `composer.json`), kept
      byte-identical to each other. **Prose keeps `Ágora`** — do not sweep it.
      **Truth fixes (I-024 shape — text in a shipped artefact outliving the state it described):**
      `README.md:55` says *"There is no project on Drupal.org"* — false since 18:17 today;
      `README.md:130` says *"no public issue queue and no support channel"* — false, verified:
      `drupal.org/project/issues/agora_transparency` → 200, `<h1>Issues for Ágora Transparency</h1>`.
      *Not in this task:* `README.md:71`'s `git clone <this-repository>` — it waits for the push, or
      the README would document a clone that 404s. Owned by T-217.
      *Success:* the identity strings exact; `gate-a-wave1.sh` **61 · 0** unchanged;
      `no-boilerplate` **scanned 18 · terms 8 · findings 0** unchanged; `composer validate --strict`
      exit 0; `recipe.yml` still parses with `type: Site`, 10 recipes / 3 install unchanged.

      *Evidence — commit `3476164`:* `recipe.yml` `name: Ágora Transparency`, byte-identical to the
      Drupal.org project title; both `description` fields identical to each other and name-free;
      README H1 and first mention, `AGENTS.md` audience header, `recommended.yml` header all
      carrying the full name. Prose keeps `Ágora` deliberately. The two false README statements
      corrected. `gate-a-wave1.sh` 61 · 0 and `no-boilerplate` unchanged, as required.
- [✓ 2026-08-23] **T-117** · `.mailmap` at the repository root, canonicalising the two author names that share
      one mailbox. `git shortlog -sne` reports `Andrés Moreno <andresmrubio28@gmail.com>` **×22** and
      `andresmoreno28 <andresmrubio28@gmail.com>` **×19** — one person, two identities, visible to
      anyone who opens the project cold. History is **not** rewritten (D-016); `.mailmap` is git's
      own non-rewriting mechanism for exactly this. Also set the forward identity so the split does
      not resume: `git config user.name "Andrés Moreno"` in this repository.
      *Success:* `git shortlog -sne --all` prints **exactly one** author line with count **41**;
      `git log --format='%aN' | sort -u | wc -l` prints **1**; `git rev-parse HEAD` **unchanged**
      — note **`%aN`, not `%an`**: lowercase is the *raw* recorded name and never consults
      `.mailmap`; only uppercase is mapped. This criterion was written with `%an` and was
      unsatisfiable by any correct `.mailmap`; the `desarrollador` caught it rather than
      declaring the task done against a check that could not pass.
      from before the task — that is the proof nothing was rewritten.
      *Do NOT* `export-ignore` it: it is 200 bytes and answers a question the tarball's recipient
      may also ask. **Land before the first push**, so the public repository never shows the split.
      *Evidence:* `git shortlog -sne --all` → one line. `git rev-parse HEAD` unchanged across the
      change, which is the proof nothing was rewritten.
      > **The criterion itself was wrong and the lane caught it.** It specified
      > `git log --format='%an'`; lowercase `%an` is the **raw** recorded name and never consults
      > `.mailmap`. Only `%aN` is mapped. Corrected in place rather than declared met against a
      > check that could not pass.
- [✓ 2026-08-23] **T-118** · Amend **`CLAUDE.md` rule 7** to the Drupal convention verified at source
      (`…/git-for-drupal-project-maintainers/the-format-of-the-git-commit-message`, updated
      2026-04-24): *"As of November 2025, the Drupal Core project adopted Git commit messages
      formatted to comply with the Conventional Commits specification"*, format
      `{type}: #{issue ID} One line summary`, with `By:` trailers for co-contributors. Allowed types
      are exactly **fix · feat · ci · docs · perf · refactor · test · task · revert** — note
      **`task`, not `chore`**. Three changes for us: (a) `chore:` → `task:` from now on (the 8
      existing `chore:` commits stay — D-016); (b) `#{issue ID}` becomes mandatory once an issue
      exists for the work; (c) the no-AI-trailer rule **stands and is reinforced** — `By:` names
      humans.
      *Success:* rule 7 quotes the doc and its URL; `git log --format=%s | grep -c '^chore'` is
      **8 and does not grow**; the next 3 commits after T-118 carry a type from the list.
      *Note:* deliberately **not** enforced by a `tests/bin/` invariant yet — one over commit
      subjects would have to scan history, which a lane forbidden to rewrite history cannot certify
      green (I-030). If wanted, it belongs in unit 002 as a **forward-only** check over
      `drupalcode/1.x..HEAD`, with its own task.
      > **Good news worth recording:** our 41 commits are already in the right family. The gap is
      > `chore` vs `task` and the missing issue IDs — both forward-only. A reviewer opening the log
      > sees the format Drupal core itself uses.

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

      *Evidence:* rule 7 now quotes the doc and its URL, lists the nine allowed types, and names
      `task` over `chore`. `git log --format=%s | grep -c '^chore'` → **9** as of this commit, and that number does
      not grow: the nine existing ones stay under rule 8, and the change is forward-only.
      > **The good news is the larger half.** The 45 commits were already in the right family —
      > Drupal core adopted Conventional Commits in November 2025, so a reviewer opening this log
      > sees the format their own core project uses. The gap was `chore` vs `task` and the missing
      > issue IDs, both of which only apply going forward.
- [⏸ superseded 2026-08-23 → T-207 (D-019)] **T-201** · Reproducible DDEV configuration (≥ 1.25.0), documented in the README.
      *Success:* `ddev start` from scratch on a clean machine, with no manual steps.
      > **Superseded, not done.** D-019 states it: this repository is **not a site** and is never
      > `ddev start`ed on its own. T-201 assumed it could be. The need it was written for — a
      > reproducible environment — is met by **T-207**'s path-repository flow, which is what CI
      > actually executes. Recorded rather than silently dropped.
- [✓ 2026-08-23] **T-202** · Review `.gitlab-ci.yml`: keep the `gitlab_templates` include, set only
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
      *Evidence — the criterion is no longer satisfied vacuously.* Until today `.gitlab-ci.yml`
      defined **zero** jobs, so "locally defined job keys are disjoint from upstream's" quantified
      over an empty set and "every local job prints a count" over nothing — the I-028 family, a
      check whose degenerate value equals its expected value. It now defines exactly one job,
      `agora-invariants`, verified disjoint against the **42** keys parsed from upstream (which
      carries both `secret detection` and `secret_detection`, and a `Pipeline set-up failed ⚠️`),
      carrying the comment its criterion (a) requires and printing two real counts.
      `diff` against upstream's `.gitlab-ci.yml`: **0 deletions**, additions only; one `include:`
      block, byte-identical.
- [✓ 2026-08-23] **T-203** · Read `include.drupalci.variables.yml` and document in the README which jobs remain
      active (phpcs, phpstan, cspell, eslint, stylelint, phpunit). *Success:* a real list, not an assumed one.
      *Evidence:* the README now carries the **observed** seven-job table with its pipeline ID,
      branch and commit, the two absences explained (`stylelint` — no CSS, and the theme is a
      separate project; `secret detection` — not among the three included files), the gate rule from
      D-023(5), and the denominator caveat.
      > **Its own parenthetical was the assumption it existed to replace.** The task text listed
      > *"phpcs, phpstan, cspell, eslint, stylelint, phpunit"*: `stylelint` does not run, and
      > `composer` and `composer-lint` do. "A real list, not an assumed one" — working as intended.
- [✓ 2026-08-23] **T-204** · Create `.cspell-project-words.txt` with the project vocabulary.
      *Success:* the cspell job passes without disabling it.
      *Evidence — pipeline `933342`, job `cspell`:* `Issues found: 0 in 0 files`,
      `allow_failure=False`. The criterion is *"the cspell job passes without disabling it"*:
      `.gitlab-ci.yml` carries **no** `_CSPELL_IGNORE_PATHS` and **no** `SKIP_CSPELL`, and the word
      list holds only justified entries — the job's own `_cspell_updated_project_words.txt` ("your
      dictionary plus everything that just failed") was never copied.
      ⚠️ **Caveat that rides with the signature, not a reason to withhold it:** cspell reported
      `Files checked: 36` while the repository tracks 63. It found 0 issues **in what it opened**;
      the 27-file gap is unexplained and owned by **T-222**.
- [✓ 2026-08-23] **T-205** · First green pipeline in the working repo. *Success:* number of jobs executed > 0 and
      the phpunit job's log shows `--fail-on-empty-test-suite` in the executed command line (T-214c), and
      all green. **A pipeline with no jobs is NOT green.**
      *Evidence — pipeline `933342`, ref `1.x`, commit `fd8d3b2`, read from the API:* **7 jobs,
      7 `success`, 7 `allow_failure=false`, 0 named exceptions.** Not merely "> 0 and all green" —
      the stronger form is what makes it mean anything: every job can now fail the build.
      The phpunit job's log carries `--fail-on-empty-test-suite` in the **executed command line**
      with `_PHPUNIT_CONCURRENT=0`, and `OK (3 tests, 38 assertions)` from the same log — the I-038
      pairing, positive evidence beside the flag rather than the flag alone.
      > **Note:** the criterion was written as *"a pipeline with no jobs is NOT green"*. It was
      > satisfiable only from today: pipeline `933270`, the first ever run, had 7 jobs and reported
      > `success` **with a failed `cspell` inside it** (I-043). This signature is against a job
      > list, never against the pipeline's status field (D-023(5)).
- [ ] **T-206** · Decide and apply D-009: what runs on drupalcode and what on GitHub Actions.
      **Blocked by D-009.**
      > **Rider 2026-08-23 — SPLIT. The blocker line above is stale: D-009 was signed 2026-08-21,
      > option C.** What remained was *apply*, and it is two unrelated jobs travelling as one.
      > **(a) The canary is unblocked today, for the first time, and does NOT need a theme** —
      > D-009 rider (b) says so in writing: *"as soon as the drupalcode project exists, run a canary
      > MR that verifies the browser job really executes on the shared contrib runners — before unit
      > 006."* Split out as **T-228**. It is the only item in this unit that gets more expensive the
      > longer it waits, because the accessibility thesis rests on a browser job nobody has seen run.
      > **(b) The axe test and the visual-regression workflow are deferred to unit 002**: there is
      > provably nothing to audit — no theme, no Twig, no CSS, no components, no pages — and a test
      > written now would pass with Ágora absent, which is the same reasoning already accepted for
      > T-402.
- [✓ 2026-08-23] **T-207** · Replace the assumption "`ddev start` in the repo" with the verified flow: set up
      Drupal separately and add the template as a *path repository*, following the kit's `.github/workflows/phpunit.yml`
      (`ddev config --project-type=drupal11 --docroot=web` → `ddev composer create-project
      --no-install drupal/recommended-project` → `ddev composer repository add source path source` →
      `ddev composer require "<package>:@dev"`, with `COMPOSER_MIRROR_PATH_REPOS=1`).
      *Success:* one command reproduces the environment from scratch; `ddev exec drush status` →
      `Drupal bootstrap : Successful`; `./recipes/agora_transparency` exists.
      *Evidence:* the README carries the verified path-repository flow with its two assertions
      (`./recipes/agora_transparency` exists after the require; `Drupal bootstrap : Successful`
      after the site install), attributed to `.github/workflows/phpunit.yml`, which executes it on
      every push and is the authority.
      **And the trap that cost two days is written down:** the tests do not travel with the package
      — `/tests` is `export-ignore`d and Composer's path mirroring honours it — so anyone running
      PHPUnit against the installed package must copy `tests/` in first, or it finds nothing,
      prints `No tests executed!` and **exits 0**.
- [ ] **T-208** · Pin DDEV ≥ 1.25.0 and **version `.ddev/config.yaml`** (today `.gitignore` ignores
      `/.ddev/`, which makes the T-201 criterion unreachable).
      *Success:* `git ls-files .ddev/config.yaml | wc -l` = 1; requirement documented in the README.
      > **Rider 2026-08-23 — the original text above is SUPERSEDED, not done** (the T-201
      > treatment). As written it is unsatisfiable and directionally wrong: this repository is not a
      > site, so versioning a `.ddev/config.yaml` would assert something false about the package.
      > D-019 already redefined it and assigned the rewiring here.
      > **Not moot** now that the invariants run in the DA's image — saying so would be I-042's
      > exact shape, a capability observed in one installation asserted of another. The pipeline
      > proves the toolchain in **CI**; it says nothing about the local half, and **all five of
      > D-019's false greens came from the host**. The macOS blocker is still open and unmeasured.
      > **Redefined scope:** a versioned, **digest-pinned** container reproducing the gate toolchain
      > for local runs on any host (D-020 rider (c): the DA gate image; fallback
      > `debian:bookworm-slim`; **never `alpine`**, whose BusyBox grep reintroduces I-027's class).
      > Not a `.ddev/config.yaml`, not a second CI surface. **Owner: unit 002**, tied to the macOS
      > host, which is the only thing that makes it urgent.
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
      > **Evidence rider [ejecutor] 2026-08-23 — layer (c) is now OBSERVED, and this is not a new
      > tick (rule 8).** Pipeline `933342`, phpunit job: the runner echoed
      > `_PHPUNIT_CONCURRENT=0, _PHPUNIT_TESTGROUPS=--all, _PHPUNIT_EXTRA=--fail-on-empty-test-suite`,
      > `_PHPUNIT_CONCURRENT=0` confirming the branch where phpunit options are legal, the flag
      > present in the **executed command line**, and the suite non-empty
      > (`OK (3 tests, 38 assertions)`) — the I-038 pairing the rider demanded. The scope note's
      > *"no run has observed it"* is discharged.
- [✓ 2026-08-22] **T-215** · Prove T-214 in the place it has to work: a CI run that executes nothing must go
      **red**. Executed by an actor other than whoever wrote T-213/T-214 (I-030).
      *Success:* on a throwaway branch with only the T-213 copy step removed, `gh run watch
      --exit-status` returns **non-zero** and the log names the cause (`No tests executed!` or
      `FATAL: tests did not reach …`); the branch is deleted locally and on the remote afterwards.
      **Until this run exists, T-214 is decoration, not an invariant** (I-027).
- [✓ 2026-08-23] **T-216** · The record for this turn, in one commit: sign **D-020** (a/b/c/d); append the
      T-202 criterion rider; append **I-032…I-035**; the risk-status update under `plan.md` §7; the
      gate A job list in `CLAUDE.md`.
      *Success:* `grep -c 'D-020' specs/000-project/DECISIONS.md` ≥ 1;
      `grep -cE '^- I-03[2-5]' specs/000-project/IDIOMS.md` = 4; no line deleted from either file.

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

      *Evidence:* D-020 signed with riders (a)–(d); the T-202 criterion rider appended; I-032…I-035
      appended; `plan.md` §7 status lines; the `CLAUDE.md` install-smoke qualifier. All present on
      disk and pushed.
- [✓ 2026-08-23] **T-217** · 🔒 First push to the canonical remote. **Blocked by D-022** (👤 [andres]).
      Rename `origin` → `github`, add `drupalcode`, push `001-fundacion/scaffolding` to both. After
      the rename there is **no remote named `origin`**, so a bare `git push` from a fresh clone fails
      loudly instead of guessing. Then update `README.md:71` to the real clone URL.
      *Success:* `git ls-remote drupalcode refs/heads/001-fundacion/scaffolding` shows **the same SHA
      as local HEAD**; `git rev-list --count HEAD` = **40**; **`default_branch` re-queried from the
      API and reported verbatim, whatever it says**; no branch created other than the one pushed;
      no `--force`, ever, to this remote.
      > **Rider [orquestador] 2026-08-22 — REDEFINED by the D-022 replacement.** The branch pushed
      > is **`1.x`**, created as a pointer with `git branch 1.x 001-fundacion/scaffolding` (same 41
      > SHAs), pushed **alone and first**; `001-fundacion/scaffolding` is **never** pushed to
      > drupalcode. Success is four counts, not an exit code: `git ls-remote --heads drupalcode`
      > prints **1** line · that SHA equals local `git rev-parse 1.x` ·
      > `git rev-list --count drupalcode/1.x` prints **41** · `git log --oneline drupalcode/1.x |
      > tail -1` prints `553c580`. Two further readings are recorded **verbatim whatever they say**:
      > the API's `default_branch`, and `git ls-remote --symref drupalcode HEAD` — the second tells
      > a *pinned* HEAD from a *resolved* one, and only pinned is stable (I-039). **T-217 does not
      > close until [andres] has pinned Branch defaults to `1.x` and both readings agree.**
      *Evidence, all four counts plus both readings:* `git ls-remote --heads drupalcode` → **1**
      line · its SHA identical to local `1.x` · root commit `553c580` ·
      `git ls-remote --symref drupalcode HEAD` → `ref: refs/heads/1.x` — **pinned**, not merely
      resolved · API `default_branch: "1.x"`, agreeing with the symref. GitLab retargeted HEAD on
      the first push to an empty repository, so the Settings step D-022(b) reserved was not needed.
      > **On the count clause.** The criterion says `git rev-list --count` prints **41**. It printed
      > 41 at the push and prints **45** today, because four commits landed after. Those clauses
      > were assertions about the push moment and held then; the durable clause is *"remote tip ==
      > local tip"*, which holds at 45 as it held at 41. Recorded rather than restated as 41.
      > **Not done:** the rider's `origin` → `github` rename. `origin` still points at the mirror,
      > so a bare `git push` targets the derived remote rather than the canonical one — backwards,
      > and owned by **T-224**.
- [✓ 2026-08-23] **T-218** · Observe the first pipeline end to end — this is T-203's and T-205's evidence, and
      it runs against an **unmodified** `.gitlab-ci.yml` deliberately: an inventory taken from a
      modified include is an inventory of us, not of upstream, and a red pipeline would then have two
      candidate causes.
      *Success:* **N jobs executed, N > 0, each named**; the phpunit job log shows
      `--fail-on-empty-test-suite` **in the executed command line** (closes T-214c); `Tests: N,
      Assertions: M` with **N ≥ 3** and `RequirementsTest` named; every outcome quoted from the log,
      **never from the badge** (I-034).
      > **Rider [orquestador] 2026-08-22.** The first pipeline is now expected to be triggered by
      > the **release-branch regex** `^[\d+.]+\.x$`, not by the default-branch rule — so it must
      > appear **even if `default_branch` still reads `main`**. **An empty `/pipelines` response is a
      > FAILURE, not "nothing to report"**: it means the instance's `$_GITLAB_TEMPLATES_REF` differs
      > from `main`, or CI is disabled on the project — check **Settings → General → Visibility →
      > CI/CD** before concluding anything. The job inventory (name + status for all N jobs)
      > supersedes the **derived** list in `CLAUDE.md`'s Gate A block and closes **T-203**. The
      > `--fail-on-empty-test-suite` half must be evidenced per I-038: the flag in the **executed
      > command line**, paired with the positive `Tests: N, Assertions: M` from the same log. Green
      > with `Tests: 0` is a failed gate.
      > **Note:** this run is a **measurement, not a gate.** Nothing closes on it. Two failure modes
      > to expect, neither ours: the `include:` uses `$_GITLAB_TEMPLATES_REPO`/`_REF`, which are
      > group-level variables on drupalcode — a config-load error is those not inheriting, not our
      > file; and cspell scans the **clone**, so it sees `specs/`, `CLAUDE.md` and `.claude/`
      > (`export-ignore` does not affect a clone — I-021, I-033). Expect a wall of findings; that is
      > T-204's workload.
      *Evidence — pipeline `933342`, every value read from the raw job logs, never from the badge:*
      **7 jobs**, each named with its status and `allow_failure`, all blocking, all `success`;
      `--fail-on-empty-test-suite` in the executed command line; `RequirementsTest` named twice in
      the phpunit log; `No tests executed` appears **0** times.
      > **One notational correction, recorded rather than fudged.** The criterion names
      > `Tests: N, Assertions: M`; the drupalcode runner printed **`OK (3 tests, 38 assertions)`**.
      > Different PHPUnit summary form — the GitHub run hit deprecations and got the
      > `OK, but there were issues!` variant; this runner did not. Same fact, N = 3 ≥ 3. Said out
      > loud because silently accepting a near-match is how a criterion stops meaning anything.
      > **Both predicted failure modes did NOT occur:** the group-level `$_GITLAB_TEMPLATES_REPO`
      > and `_REF` inherited cleanly, and the predicted "wall of cspell findings" was 146 and is
      > now 0. Recorded so the rider is not re-read later as an open worry.
      > **Correction to T-203's parenthetical**, which this inventory supersedes: it named
      > `stylelint`, which does **not** run, and omitted `composer` and `composer-lint`, which do.
- [✓ 2026-08-23] **T-219** · The second pipeline — the run that actually closes the `tests/bin/`-in-no-CI 🔴,
      after T-202 adds the job its own amended rider permits.
      *Success:* the `tests/bin/` job's log shows `gate-a-wave1.sh` **61 · 0** and `gate-a-wave3.sh`
      **33 · 0** (31 after T-214, +2 after T-322).
      *Evidence — pipeline `933415`, job `agora-invariants`, `allow_failure=false`:* the job's log
      carries both summary lines, `61 checks - 0 failures` and `33 checks - 0 failures`.
      **The criterion's `33 · 0` is met, but for a different reason than it predicted:** it assumed
      31 + 2 from T-322, which is still unimplemented; the +2 came from T-223's `cited-tasks-exist`.
      Recorded, because a criterion met by coincidence is worth exactly as much as one that is not.
      > **First run went red, and correctly.** Four checks failed: `no-blind-phpunit` and
      > `cited-tasks-exist` reported `present | FAIL` because both were committed **mode 644**, and
      > the runner tests the file is executable. Every local invocation went through
      > `bash tests/bin/x`, so the missing bit was invisible for the whole of wave 3 — **a habit in
      > how we invoke a script masked a property of the script itself.** Four files `chmod +x`.
- [ ] **T-220** · Confirm the 41 commits are **attributable** on Drupal.org once pushed. Commit
      authorship on drupalcode attaches to a user account only when the author email is a **verified
      email on that account**; otherwise the commit list shows a bare string and the maintainer gets
      no contribution record. Our author email is a personal address, not the drupal.org
      `NNNNNN-noreply@drupal.org` form.
      *Success (👤 [andres], after the push):* on
      `git.drupalcode.org/project/agora_transparency/-/commits/1.x`, the author of the tip and of
      the root commit `553c580` both render as a **linked GitLab user with an avatar**, not a plain
      email string; the commit count shown is **41**.
      *If it fails:* add that email to the drupal.org account — retroactive, no history change.
      **Never** rewrite the author fields.

- [✓ 2026-08-23] **T-226** · 🔒 **Retroactive record, written at the number D-023(6) reserved.**
      D-023(6) created the cspell exception naming *"Owner **T-226**; deleting the line is the exit
      gate"* — and **T-226 was never written to this file.** The exception was owned by a number
      that did not exist: an accountability record whose accountability was a dangling pointer. It
      survived only because the same turn kept going. Recorded as **I-044**, and guarded from
      recurrence by **T-223**.
      *What was done:* `_CSPELL_ALLOW_FAILURE` deleted from `.gitlab-ci.yml` once the corpus was
      clean, making all seven jobs blocking.
      *Success:* `grep -c '_CSPELL_ALLOW_FAILURE' .gitlab-ci.yml` → **0**, **and** the cspell job of
      the resulting pipeline reports `allow_failure=false` **and** `status=success` — both, because
      either alone is the exception in disguise. Evidence: `933317` (green, still permissive) →
      **`933342`** (green, blocking). The exception lived **zero days beyond its purpose**, which is
      the outcome D-023(6) was designed for.
- [✓ 2026-08-23] **T-221** · 🔴 Add the Ágora-local invariants job — the vehicle that closes the last open
      🔴 of this unit, *`tests/bin/` runs in no CI*. Exactly one job key, disjoint from every key
      `gitlab_templates` defines (**42** verified by parsing upstream, not by assuming a list — it
      carries `secret detection` **and** `secret_detection`, plus `Pipeline set-up failed ⚠️`);
      a comment naming which upstream job would otherwise cover this and why none does (T-202's
      amended criterion (a)); runs both gate runners with both summary lines reaching the log; the
      `include:` block untouched; no `allow_failure`, no `|| true`.
      *Success:* the next pipeline shows **8** jobs; the job's log contains exactly **two**
      `N checks - M failures` lines, both ending `- 0 failures`. **Zero such lines is a FAILURE,
      not "nothing to report".**
      > **Predicted first red, so it is diagnosed and not panicked over:** `gate-a-wave3.sh`'s
      > preflight needs `python3` and `gate-a-wave1.sh`'s needs `jq`. **Neither is guaranteed in the
      > runner image** — `jq` appears **zero** times in `include.drupalci.main.yml`, and upstream's
      > only Python use pulls a *separate* `image: python:3.12` for the `pages` job, which implies
      > the PHP image is not assumed to have it. Deliberately **not** pre-installed: doing so would
      > convert an honest preflight failure into a hidden dependency of the job.
      *Evidence — pipeline `933415`:* **8 jobs**, `agora-invariants` `success` and blocking, its
      log carrying exactly two `N checks - M failures` lines, both `- 0 failures`.
      **The predicted first red did not happen for the predicted reason.** `jq`, `composer` and
      `python3` are all present in the runner image — the preflight passed, so both predictions
      about missing tooling were wrong, and recording that is worth as much as recording a hit.
      What did go red was the execute bit (see T-219).
- [✓ 2026-08-23] **T-222** · Make the denominators visible, then reconcile them. Four **blocking** checks
      report a result with no idea how many files they opened: `cspell` printed `Files checked: 36`
      against **63** tracked; `phpcs` printed **nothing at all** between invocation and exit code;
      `phpstan` and `eslint` print no count. **A blocking check over an unknown denominator is not a
      stronger gate than a permissive one — it is a more confident one** (I-045).
      Variables added (both verified upstream: `variables.yml:183` and `:143`):
      `_CSPELL_SHOW_PROGRESS: '1'`, `_PHPCS_EXTRA: '-p'`.
      *Success:* the next cspell log enumerates its file list; `phpcs` prints `N / N (100%)`; and
      the 36-vs-63 gap is reconciled by **naming every absent file** and stating for each whether it
      should be in scope. Where the answer is "yes and it is not", that is its own finding —
      anything covering `.claude/` touches D-024(4) and needs its own line, so **do not fix it
      inside this task**.
      *Evidence — the gap is explained, not estimated.* `_CSPELL_SHOW_PROGRESS: '1'` made cspell
      name every file it opens, and the 37-of-64 gap decomposes exactly: **25 dot-paths** —
      `.claude/` (14 files), `.github/`, `.tugboat/` and the root dotfiles — plus `LICENSE.txt` and
      `composer.json`, which upstream's `_CSPELL_IGNORE_COMMON` skips by default, and one binary.
      **D-024(4) names `.claude/` as something that must NOT be excluded**, and a Spanish comment in
      `.claude/settings.json` had survived there unseen until a human found it — so the exclusion
      was arriving by **upstream default rather than by our choice**, which is worse than choosing
      it. `--dot` put them back: **37 → 61 files checked**, at a cost of five words — three
      third-party identifiers listed with justification, two coinages of mine reworded rather than
      listed.
      `_PHPCS_EXTRA: '-p'` is set; phpcs's own denominator is verified on the next run that has
      PHP to lint.
- [✓ 2026-08-23] **T-223** · `tests/bin/cited-tasks-exist` — I-044's guard. Extracts every `T-NNN` cited in
      `DECISIONS.md` and asserts each is **defined** in some `specs/*/tasks.md`. Scans the working
      tree, never `git grep` (I-018). FATAL on `citations == 0` with defaulted input (I-031) and no
      summary line. `wc -l` never `grep -c`; `grep` rc ≥ 2 fatal; no `-F` with `-i`.
      *Success:* clean tree → exit 0 printing citations and definitions; the T-999 dirty case →
      exit 1 with `file:line`; reverted with `git status --porcelain` empty. `gate-a-wave3.sh`
      **31 → 33 checks** (14 preflight + 17 invariant + 2 new), stated as a number so the change
      cannot be silent.
      > **It found its own reason for existing on the first run:** 31 citations, 21 distinct, and
      > **one dangling** — `DECISIONS.md:728` citing T-226. Also worth recording: the author's first
      > draft pattern capped a list-item prefix at 24 characters and **falsely** reported T-402
      > dangling, because its status marker is long (`[⏸ deferred 2026-08-22 → unit 002 …]`).
      > Caught before shipping, and documented in the script.
      *Evidence:* clean tree → exit 0, `citations: 31 · distinct cited: 21 · definitions: 72 ·
      findings: 0`. Dirty case: a `T-999` citation injected into `DECISIONS.md` → exit 1 naming
      `file:line`; reverted, `git status --porcelain` empty. `gate-a-wave3.sh` **31 → 33**.
      **It found its reason for existing on its first run:** one dangling citation, `DECISIONS.md`
      naming T-226 as the owner of an exception that no task list defined.
- [✓ 2026-08-23] **T-224** · Complete T-217's rider: rename `origin` → `github`. Today `origin` points at the
      **read-only mirror**, so a bare `git push` from a fresh clone targets the derived remote
      rather than the canonical one — exactly backwards under D-016. After the rename there is no
      remote named `origin`, so the bare command fails loudly instead of guessing.
      *Success:* `git remote` lists `drupalcode` and `github`, and **no** `origin`.
      *Evidence:* `git remote` → `drupalcode`, `github`. **No remote named `origin`.** Until now a
      bare `git push` targeted the read-only mirror rather than the canonical remote — backwards
      under D-016, and now impossible: the bare command fails loudly instead of guessing.
- [✓ 2026-08-23] **T-225** · Document a local cspell pre-flight. Load-bearing now: **cspell is blocking and it
      reads `README.md` and `specs/`, so every prose commit is a gate.** `pnpm dlx cspell@9.8.0
      --locale en,en-GB` — pnpm exclusively (rule 5), version pinned to the runner's, locale
      matching `_CSPELL_EXTRA`.
      *Success:* the README section exists and **labels itself an approximation**: CI fetches
      upstream's `.cspell.json` and expands it with `prepare-cspell.php`; the local run does
      neither, so it can disagree in both directions. An approximation that says so is a tool; one
      that does not is I-042 again.

- [✓ 2026-08-23] **T-227** · `tests/bin/doctor` probes WSL before declaring Docker unavailable.
      It reported `CLI present, daemon unreachable` — accurate about the Windows CLI, and useless,
      because a working daemon was on the same machine the whole time. **T-401 lost a day to that
      report** (I-046).
      Four states now reported distinctly, and the distinction is the whole value: usable on
      Windows · usable **inside WSL** (naming the distro, server version and whether `ddev` is
      there too) · **a stopped `docker-desktop` distro beside a working one** — the exact shape
      that produced the wrong conclusion · no daemon anywhere, which is the only case that now
      says Docker is unavailable.
      It also states that the WSL setup **is DDEV's own recommended one, not a degraded one**, so
      nobody "fixes" it by installing Docker Desktop, and that a project must live in the WSL
      filesystem, never `/mnt/c`.
      *Success:* on this host `doctor` reports `available in WSL distro 'Ubuntu' (server 28.1.1)`
      and exit 0; forced to fail the WSL probe it degrades to the honest "no working daemon found"
      rather than crashing or claiming success (falsified, then reverted byte-identical).
      `gate-a-wave1.sh` 61 · 0 and `gate-a-wave3.sh` 33 · 0, unchanged. Windows-only probe: no
      added latency on Linux or macOS.
      > **Boundary handling worth keeping:** `wsl.exe -l -v` emits UTF-16 with embedded NULs, and
      > stripping them *after* the command substitution still triggers bash's own "ignored null
      > byte" warning. They are stripped **inside** the pipeline instead. Same family as I-025:
      > normalise at the boundary, not after it.

- [ ] **T-228** · The D-009 rider (b) canary: verify the browser job **really executes** on the
      shared contrib runners. `OPT_IN_TEST_DRUPAL_CMS: '1'`, one push, one API read of the job list.
      **Run it as a merge request, not on `1.x`** — per I-040 a plain branch triggers **no pipeline
      at all**, so a throwaway branch would return a silent nothing and read as success; MR rules do
      fire, and an MR leaves `1.x`'s observed gate untouched.
      *Success:* the job list shows a browser job that **ran** — a skipped job is not a job that
      ran — and its `allow_failure` is **recorded**. ⚠️ `nightwatch` sits in the **`test`** stage,
      which `_ALL_VALIDATE_ALLOW_FAILURE: '0'` does **not** cover; if it arrives permissive, D-023(5)
      requires a dated, owned exception named in `.gitlab-ci.yml`, or the gate rule is violated the
      moment the job exists. **Do not merge the canary without settling that.**

**Gate A wave 2**
```bash
ddev start && ddev drush status          # expected: Drupal bootstrap = Successful
# On GitLab: the pipeline of the latest commit, green, with the number of jobs in view
```
**Gate B wave 2** 👤 · Andrés confirms the split of tests between drupalcode and GitHub.
Sign here: `[ ]`

---

## Wave 3 · Invariants (parallelizable with wave 2 — disjoint files)

      *Evidence:* the README documents `pnpm dlx cspell@9.8.0 --locale en,en-GB`, pinned to the
      runner's version and matching `_CSPELL_EXTRA`, and **labels itself an approximation**.
      That label earned itself the same day: run locally over `.claude/**` it reported 82 issues
      where CI reported 5, because the local run lacks upstream's `.cspell.json` and is therefore
      **stricter** than the gate. An approximation that says so is a tool (I-042).
- [✓ 2026-08-21] **T-301** · `tests/bin/no-unstable-deps` according to the spec in `plan.md` §6.
      *Success:* it detects a deliberately injected `-beta` and does **not** flag the starter kit.
- [✓ 2026-08-21] **T-302** · `tests/bin/no-patches`. *Success:* it detects an injected `patches` section.
- [✓ 2026-08-21] **T-303** · `tests/bin/no-secrets` over the whole repo except `.git/`.
      *Success:* it detects a fake key injected in `config/` and another in `content/`.
- [✓ 2026-08-21] **T-304** · `tests/bin/sbom-check` against `updates.drupal.org` (method in research §10.4).
      *Success:* it requires stable + `<security covered="1">` + a line in `DECISIONS.md`; it fails if one is missing.
- [✓ 2026-08-21] **T-305** · All four print scope, number of files scanned and number of findings.
      *Success:* none reports "0 files scanned".
- [✓ 2026-08-21] **T-306** · **Amendment to the T-304 method** (`sbom-check`): the endpoint returns **HTTP 200
      with an `<error>` body** for non-existent projects → a `curl -f` gives a false green. It must:
      (a) check the network at startup and **fail loudly if there is none** — "skip" forbidden;
      (b) parse the XML; (c) require `<title>` and the absence of `<error>`; (d) take as stable the
      first release without `dev|alpha|beta|rc`; (e) require `<security covered="1">` in that release;
      (f) check `<core_compatibility>`; (g) require a `D-NNN` line in `DECISIONS.md` for each
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
      > (`LICENSE.txt`, `screenshot.webp`, `specs/`, `specs/000-project/DECISIONS.md`) were
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
- [✓ 2026-08-23] **T-317** · Determine whether the `-Fin` abort, and the toolchain assumptions generally, hold
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

      *Evidence:* the README's toolchain floor table records **four** platforms from measurement,
      not reasoning: the Windows dev host (GNU grep **3.0**, `-IFin` → rc **134**; jq 1.8.2, Python
      3.12.6 defaulting to **cp1252**, PHP 8.4.24 ZTS, Composer 2.10.2), **WSL2 Ubuntu 24.04**
      (docker-ce 28.1.1, DDEV 1.24.4 — where the smoke runs), the **drupalcode runner** (jq, python3,
      curl, git, composer all present — verified by the invariants job passing preflight after we
      predicted they might be missing and were **wrong**), and **macOS: NOT MEASURED**, stated as
      such because it ships BSD grep and every question about it is open.
      Drupal-side floor recorded: core 11.4.5, PHP 8.3–8.5, Composer 2.3.6.
      One line says `tests/bin/doctor` beats the table — a table decays, a probe does not.
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
- [✓ 2026-08-23] **T-321** · 🔒 **Pattern escalation, unit 002 — deliberately NOT wave 3 work.** Five times
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
      *Evidence:* 10 `grep -c` sites inventoried, **7 converted** to `wc -l` (every one feeding a
      comparison), **3 left and annotated in place** so they are not read as oversights — one is
      display-only, and two are probes where `grep -c` **is the subject being tested** and the
      comparison is against a string, where a blank fails loudly rather than skipping a branch.
      Numeric guards defaulted **only where zero means FAIL** — I-031's safe direction. Guards where
      zero means PASS were deliberately left as they were, and **two pre-existing `${HITS:-0}` in
      `gate-a-wave1.sh`'s expect-zero packaging check were removed**: that is precisely where `:-0`
      turns a blank into a green.
      Two blank injections, on different scripts, each shown before and after: `no-boilerplate`'s
      term count and `no-code-in-template`'s packaged count both went from **exit 0 with a summary
      printed** to **exit 1 with none**.
      House rules written once into the shared contract block that eight scripts already point at.
      Clean-path output byte-identical apart from file counts that moved because T-322 added a file.
- [✓ 2026-08-23] **T-322** · `tests/bin/identity-strings` — the guard D-021 chose **instead of** a deny term,
      because "the identity strings are correct" is naturally **expect-present**, whose degenerate
      value is `no` and never `yes` (I-028).
      Single source of truth at the top: `FULL_NAME='Ágora Transparency'`,
      `PACKAGE='drupal/agora_transparency'`. Asserts: (1) `recipe.yml` has exactly one `^name:` line
      and its value is exactly `$FULL_NAME`; (2) `composer.json` `.name` == `$PACKAGE`; (3) the two
      `description` fields are **byte-identical**; (4) for each identity file, the **first line
      containing `Ágora` contains `Ágora Transparency`` — D-021's rule as one check instead of five
      line numbers that rot; (5) **closed world**: the set of packaged files naming the product must
      be a subset of {identity files} ∪ {declared prose-only}. **An undeclared new packaged file
      naming the product is a finding** — that is I-024 made impossible rather than remembered.
      Born with the house rules: `wc -l` never `grep -c` for a compared value, defaulted numeric
      guards, `grep` rc ≥ 2 fatal, no `-F` with `-i`.
      *Success:* clean → exit 0, `identity files checked: 5 · prose-only declared: 2 · packaged files
      naming the product: 7 · findings: 0`; five dirty cases **in the same commit** (D-019 rider e),
      each reverted — name shortened to `Ágora`, the two descriptions made to diverge, README's first
      mention made bare, a new undeclared packaged file, an identity file deleted (FATAL, **no
      summary line**). `gate-a-wave3.sh` **31 → 33 checks · 0 failures**.
      **Ordering: lands AFTER T-116.** Run against today's tree it correctly reports findings, and a
      lane that writes it first will be tempted to "fix" them.
      *Predicted unchanged, so a move is itself a finding:* `gate-a-wave1.sh` **61 · 0** (its identity
      assertions are on the package name and the `^name:` line count, not the value) and
      `no-boilerplate` **18 · 8 · 0**.

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

      *Evidence:* `identity files checked: 5 · prose-only declared: 2 · packaged files naming the
      product: 7 · findings: 0`, exit 0. Five dirty cases, each reverted: bare `Ágora` in
      `recipe.yml` → 2 findings (assertions 1 and 4 both fire); descriptions diverged → 1 with both
      values printed; README's first mention made bare → 1; an **undeclared new packaged file** →
      scope 11→12, naming 7→8, 1 finding; an identity file deleted → FATAL with **0 summary lines**.
      `gate-a-wave3.sh` **33 → 35**.
      > **One place the spec was interpreted rather than transcribed, flagged not hidden.**
      > Assertion 4 as written is line-oriented, and `AGENTS.md`'s first mention is a blockquote
      > where the full name **wraps across a newline** — so a literal implementation reports a
      > finding against correct prose, and the tempting repair (a hand-placed line break) is exactly
      > the rot D-021 exists to prevent. Implemented on a normalised stream instead: leading `>`/`#`
      > dropped, whitespace collapsed, then assert the text after the first `Ágora` begins with
      > ` Transparency`.
      > **And `git check-attr export-ignore` turned out unusable** for deriving the packaged set: it
      > reports nothing for a file inside a directory whose *directory* entry carries the attribute,
      > which is how all nine roots are written — 60 files against the archive's 11. The
      > authoritative half stays `git archive HEAD`.
- [✓ 2026-08-23] **T-401** · Clean install smoke: `sql:drop` + reinstallation, verifying that the template
      appears in the selector. *Success:* a capture or output that demonstrates it.
      > **Note (rider [andres] 2026-08-21):** *"the clean-install assertion runs in
      > **non-interactive mode** (no composer prompts answered by hand), and must verify how
      > `site_template_helper` ends up authorised in the `allow-plugins` of the `composer.json` the
      > end user receives. If generation requires manual interaction → **escalate as an install-UX
      > finding, do not patch silently.**"* See the amendment to D-008.
      *Evidence — a real, clean Drupal 11.4.5, 2026-08-23. Environment: WSL2 Ubuntu 24.04 with
      docker-ce 28.1.1 and DDEV 1.24.4 **inside WSL**, which is DDEV's own recommended Windows
      setup — not Docker Desktop, whose distro was the conflict that had blocked this task.*
      **The package under test was cloned from the canonical remote**, not from the working copy:
      `git clone https://git.drupalcode.org/project/agora_transparency.git` at `6aba924`. That
      matters — it tests what a user receives, not what a developer has.
      *What the installed package contained:* `AGENTS.md`, `LICENSE.txt`, `README.md`,
      `composer.json`, `content/`, `recipe.yml`, `recommended.yml`, `screenshot.webp` — and
      **0 test files**, because `/tests` is `export-ignore`d and Composer's path mirroring honours
      it. Exactly the packaged artefact `git archive` predicts (I-033).
      *Applying the recipe:* `drush recipe ../recipes/agora_transparency` → **78 steps**,
      `[OK] Ágora Transparency applied successfully`, **exit 0**.
      *The resulting site:* Drupal bootstrap **Successful** · front page **HTTP 200** ·
      `system.theme:default` = `blank` · `system.site:page.front` = `/page/1` (the Canvas page the
      recipe creates) · **58** non-core modules enabled.
      *And the documented workaround verified end to end:* copying `source/tests` into the
      installed package, exactly as the README's new section instructs, then
      `phpunit --fail-on-empty-test-suite ./recipes/agora_transparency` → **`Tests: 3,
      Assertions: 38`**, the same counts as CI. Without the copy it would have printed
      `No tests executed!` and exited 0.
      > **This closes the D-008 amendment's open consequence, which was recorded as UNVERIFIED and
      > inherited by this task.** The amendment established from `Plugin.php` that
      > `site_template_helper` has **no root-package check** and would therefore run on the end
      > user's machine. It does: `web/themes/blank` was generated on this clean install, by
      > `composer require` alone. The inference was correct, and it is now measured rather than
      > deduced.
      > **The `allow-plugins` half is also answered.** The install needed
      > `composer config allow-plugins.drupal/site_template_helper true` — without it Composer
      > refuses the plugin and `blank` is never generated, while `recipe.yml` both lists it in
      > `install:` and pins it in `system.theme`. That is an **install-UX finding**, exactly as the
      > task's rider required it be treated: reported, not patched silently. It is resolved for
      > real when the theme becomes `drupal/agora_theme` (D-014) and the generator is dropped.
      > **PROOF OF THE SELECTOR CLAUSE, added 2026-08-23 after the T-404 audit found it missing.**
      > The first evidence proved the recipe applies to a clean Drupal — abundant, real, and about
      > the wrong thing: `plan.md` §1 defines this unit's done-criterion as *"sees the Ágora
      > template in the installer's template selector"*, and the flow used started from
      > `drupal/recommended-project`, which has no installer selector at all. Two clauses were
      > unproven and the quantity of evidence hid it (**I-048**).
      > Now proven, on a second rig built from `drupal/cms` with the package installed from a clone
      > of the canonical remote: five recipes declare `type: Site` — `agora_transparency`, `byte`,
      > `drupal_cms_site_template_base`, `drupal_cms_starter`, `haven` — and walking the web
      > installer reaches `<title>Choose a site template | Drupal CMS`, where the page renders
      > `value="agora_transparency"` with `alt="Ágora Transparency"` and our own
      > `screenshot.webp` inlined. **Ágora appears in the selector beside Byte, Haven, Blank and
      > Starter**, the real published templates.
      > The non-interactive half also passes: `drush site:install --yes recipes/agora_transparency`
      > → `[success] Installation complete.`
      > **Finding D resolved, and it is a real discrepancy, not a display artefact:**
      > `recipe.yml:125` declares `page.front: '/home'`, and the installed site reports
      > `system.site:page.front` = **`/page/1`**. They resolve to the same node — the alias of
      > `/page/1` is `/home`, verified — and the front page returns 200, so nothing is broken. But
      > what the recipe declares is not what lands. Carried to unit 002 with the theme swap, which
      > touches the same block. Attributed by mechanism, not inferred (I-037).
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
- [✓ 2026-08-23] **T-403** · Project README **in English** (public docs in English, D-005): what it is, how it is
      installed, what it ships.
      > **Note 2026-08-21:** superseded in wave 3 by **T-310**; T-403 keeps whatever is left over
      > for wave 4.
      *Evidence:* the README now carries **what it ships** (the eight packaged entries, and that
      `/tests` is `export-ignore`d and never travels), **what a clean install actually does**
      (78 steps, `[OK] … applied successfully`, front page 200, blank theme, Canvas front page, 58
      non-core modules, `Tests: 3, Assertions: 38`), and the **`allow-plugins` install-UX finding**
      stated honestly rather than smoothed over.
      The Requirements section is corrected: it claimed the flow starts from a Drupal CMS 2.x site,
      when the verified flow starts from `drupal/recommended-project` and Ágora's own
      `composer.json` is what pulls the Drupal CMS `^2` recipes in. A previous lane had flagged this
      and left it.
- [✓ 2026-08-23] **T-404** · `orquestador` audit (read-only): standards, SBOM, licences, marketplace
      requirements. *Success:* verdict with no open 🔴.
      *Verdict: initially **⛔ CONDITIONAL** — the audit refused to close the unit, and it was
      right on both counts.* It found that `README.md` and `CLAUDE.md` published a **seven**-job CI
      inventory falsified by our own next commit (**I-047**), and that **T-401 was signed on
      evidence that did not meet its own criterion** (**I-048**). Both were remedied before this
      signature; neither was argued away.
      *On the criterion itself:* T-404 requires *"no open 🔴"*, and D-010 sat red in the blockers
      table. The audit ruled it **does not block**, and not by softening anything — three places on
      disk agree it belongs to unit 003 (`plan.md` §3 puts demo content in the explicit NO column,
      D-010's own text says *"postponed to unit 003"*, and the row's own Blocks cell says unit 003).
      **The finding underneath was that the blockers table used 🔴 to mean "unsigned" rather than
      "blocking"**, which is how an unrelated decision came to sit in a closure criterion. Re-labelled.
      *Dimensions audited, clean in one line each:* **SBOM** — 9 packages, all stable, all
      `covered="1"`, all with a D-NNN line, verified live in this turn · **structure** — no
      `recipes/`, `themes/` or `modules/`, 0 `*.info.yml`, `type: Site` exact, no pins, no patches ·
      **standards** — 8/8 blocking, 8/8 success, zero exceptions · **licences** — code half clean
      and mutually consistent; the asset half is 🟡 E below · **accessibility** — **nothing to
      audit, and that is the correct state**: no theme, no Twig, no components, no demo pages, and
      the README makes no conformance claim, which is I-023 applied to ourselves.
      *Carried forward with owners, not as debt:* 🟡 E, no licence manifest for non-code assets —
      the placeholder screenshot's typeface provenance lives only in an `export-ignore`d file and
      never reaches the recipient. One row today; twenty in unit 003. Owner **unit 006**.
- [✓ 2026-08-23] **T-405** · Promote the unit's lessons to `IDIOMS.md`.
      *Evidence:* `IDIOMS.md` holds **I-001…I-049**. The audit checked each major episode of this
      unit against the file and found the record **already complete** for the unit's body of work —
      and said so rather than inventing entries to fill a task, which is the honest answer and the
      one worth recording.
      Three were genuinely missing, and all three came from the closure audit rather than from the
      work: **I-047** (an "observed" fact is a dated measurement, and ours decayed within hours —
      falsified not by a regression but by our own next commit), **I-048** (the ninth species of
      false green: evidence abundant on the wrong axis, the only one where the evidence is genuinely
      strong), and **I-049** (agentic speed compresses the work and leaves the round-trips untouched,
      so latency becomes the schedule).
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
Sign here: `[ ]` — unit 001 CLOSED.

Gate A, all counts **read rather than assumed**: drupalcode pipeline `933556`, ref `1.x`,
**8 jobs · 8 success · 8 `allow_failure=false` · 0 named exceptions** (D-023(5));
`gate-a-wave1.sh` **61 · 0**; `gate-a-wave3.sh` **35 · 0**; 10 invariants exit 0, each with its
dirty case run.

Clean install on a real Drupal 11.4.5 in WSL2, from a **clone of the canonical remote**: recipe
applied in 78 steps, front page HTTP 200, `Tests: 3, Assertions: 38` — and **the template observed
in the Drupal CMS installer's template selector**, rendering as `value="agora_transparency"` /
`alt="Ágora Transparency"` beside Byte, Haven, Blank and Starter. That sentence is `plan.md` §1's
criterion for done, and it was missing from T-401's first signature until the T-404 audit caught
it (I-048).

Independent verdict by `orquestador` (T-404): **no 🔴 open in unit 001**. It first returned
⛔ CONDITIONAL and was right twice — a stale CI inventory in two files, and T-401 signed on
evidence adjacent to its own criterion. Both remedied, neither argued away. **D-010 is confirmed
NOT blocking**: it belongs to unit 003 and its red was the blockers table using 🔴 to mean
*unsigned* rather than *blocking*.

Carried forward with owners, **not as debt**: T-206 SPLIT (canary → **T-228**, run now via MR;
axe + visual regression → unit 002) · T-208 REDEFINED, owner unit 002 · T-402 → unit 002 ·
T-114 → unit 003 · licence manifest for non-code assets → unit 006 · macOS host NOT CERTIFIED
(T-317) · `recipe.yml` declares `page.front: '/home'` and the installed site reports `/page/1`
— same node, alias verified, front page 200, but declared ≠ landed → unit 002 with the theme swap.

⚠️ **Release rider.** No tag, no release and no marketplace submission before unit 002's atomic
theme swap lands. A package released today ships `extra.drupal-site-template`, generates a theme
on the end user's machine, and needs a manual `allow-plugins` step or the install fails for a
non-obvious reason (D-008 amendment, **proven** by T-401, not inferred).

---

## Active blockers

> A table of **state**, not of signed tasks: it is rewritten on each update.
> Last updated: 2026-08-23, after wave 2 closed.

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
| D-010 v1 demo content scope | 🟢 **DEFERRED, opens with unit 003** · re-labelled 2026-08-23 by the T-404 audit: it blocks no task, gate or non-negotiable in unit 001, and `plan.md` §3 puts demo content in this unit's explicit NO column. The 🔴 was the table using red to mean *unsigned* rather than *blocking* — which is how an unrelated decision came to sit in a closure criterion | unit 003 | 👤 Andrés |
| **Wave 2 deadlock** | ✅ **RESOLVED 2026-08-22 by D-019** · the incompatibility was two needs conflated: running the invariants (→ container) and running the install smoke (→ a Drupal site set up separately, this package added as a path repository — the T-207 flow, already executed by `.github/workflows/phpunit.yml`). **T-201 superseded by T-207**; **T-208 redefined** to version the gate's container definition, not a `.ddev/config.yaml` for a site this repository does not contain. Task-level rewiring owned by the `orquestador` | — | — |
| T-205 first green pipeline | 🟡 **OPEN, unblocked 2026-08-22** · the drupalcode project **exists** (API 200, created `2026-08-22T18:17:19Z`, public, `default_branch: main`) — and the repository is **empty: 0 branches, 0 commits**, so `main` is a pointer to a ref that does not exist. What is missing is no longer a project but a **push** (T-217) and an **observed run** (T-218) | T-205, T-206, the D-009(b) canary | 👤 Andrés signs the first push (D-022) |
| **`phpunit.yml` executed ZERO tests** | ✅ **CLOSED 2026-08-22 by T-213** · run `32582950414` reports `Tests: 3, Assertions: 38` with 0 occurrences of `No tests executed`, paths resolving under `recipes/agora_transparency/`; the recurrence is guarded by `--fail-on-empty-test-suite` and proven red by T-215 (run `32583207616`, exit 1) | — | — |
| **`_PHPUNIT_EXTRA` guard unobserved** | 🟡 **OPEN** · verified statically but **no run has seen it**. No longer blocked by the absence of a project: folded into **T-218**, whose criterion is that the first pipeline's phpunit job log shows `--fail-on-empty-test-suite` in the **executed command line** | T-218 | — |
| **`simpleConfigUpdate` → exception in Drupal 12** | 🟡 **OPEN** · an upstream Drupal CMS recipe that Ágora composes calls `simpleConfigUpdate` on a config **entity**; deprecated in 11.2, **throws** in 12.0 (verified at source). **Not Ágora's** — its two uses target `system.site` and `system.theme`, simple config with no entity type, which cannot trigger it (I-037). Re-checked at unit 007's dependency review | unit 007 | — |
| T-106 theme approach | ⏸️ DEFERRED to unit 002 (see T-110) | — | — |
| definitive `screenshot.webp` | ⏸️ DEFERRED to unit 003 (see T-114) · provisional placeholder in its place, deliberately does not imitate a real site | — | — |
| **`tests/bin/` runs in no CI** | ✅ **CLOSED 2026-08-23 by T-221** · pipeline `933415` runs `agora-invariants` as an eighth job, blocking, printing `61 checks - 0 failures` and `33 checks - 0 failures`. Nine invariants that ran only when a human typed them now run on every push | — | — |
| **first push to drupalcode** | ✅ **CLOSED 2026-08-23 by T-217** · `1.x` pushed, remote tip identical to local, root `553c580`, HEAD **pinned** (`ls-remote --symref` → `ref: refs/heads/1.x`) and the API agreeing. `origin` renamed to `github` by T-224, so no bare push can reach the mirror by accident | — | — |
| T-316 grep rc-blindness | ✅ **CLOSED 2026-08-22** · signed — 28 `rc >= 2` guards across 27 call sites in 9 files; residuals R1/R2 closed in the same wave; clean-path output byte-identical to `c3dc9f5` | — | — |
| T-317 toolchain floor | 🟡 **OPEN** · unblocked by D-019, which requires the floor to be pinned and documented · `-Fin` and the CRLF assumptions are measured **only on the Windows dev host**. The CI/DDEV image and the **macOS dev host** are both unmeasured, and macOS is a different question (BSD grep, not GNU). Do not assume either covers for the other | — | blocked by D-019 |
| **second dev host (macOS)** | 🟡 **OPEN** · a second agent works this repo from a Mac. The wave 3 gate is certified on the Windows host **only** (see the closure note): its toolchain floor, its `grep` flavour and its line endings are all unverified there. First action on that host is T-317's measurement, before trusting any invariant's green | T-317 | — |
| T-314 packaged `recipe.yml` boilerplate | ✅ **CLOSED 2026-08-22** · signed; deny list 7 → 8, proven to fire on the uncleaned file before the cleanup | — | — |
| `.gitattributes` `text eol=lf` | ✅ **CLOSED 2026-08-22 by T-319** · `tests/bin/** text eol=lf` and `*.sh text eol=lf`, in their own labelled section, scope confirmed narrow | — | — |
| **Docker for the smoke tests** | ✅ **RESOLVED 2026-08-23** · not absent, just elsewhere: `docker-ce` 28.1.1 and DDEV 1.24.4 live **inside WSL2 Ubuntu**, which is DDEV's own recommended Windows setup. The unreachable daemon was Docker Desktop's, a different product, whose stopped distro sat beside the working one. T-401 ran there (I-046) | — | — |
| PHP 8.4 **ZTS** on the dev host | 🟡 **OPEN** · `gitlab_templates` and the Drupal CLI assume NTS. Inert today (no invariant executes PHP; `composer validate --strict` exit 0); surfaces at **T-406** | T-406 | — |
