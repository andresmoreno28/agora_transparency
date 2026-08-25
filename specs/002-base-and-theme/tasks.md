# Unit 002 · tasks (append-only)

Budget: **38** tasks. Raised from 34 by **D-026** (2026-08-24, signed under delegation): the content
model is six node types, not one task's worth of work, and it was split into five. **+4 buys exactly
the content model and nothing else** — 34 for the known work, 4 to keep the reserve that wave 7's
atomic swap and wave 8's carried debts are most likely to need. Crossing **38** costs a signed rider.

---

> **Legend for the `Repo` column** (added 2026-08-24 — it was used in all five tables and defined
> nowhere, which is unreadable to anyone executing this file without the conversation):
> **`T`** = `agora_transparency`, the site template — the checkout you are already in ·
> **`H`** = `agora_theme`, the theme — its own checkout (see CLAUDE.md, “Where the work happens”) ·
> **`·`** = the process layer, which lives inside the template's repository.

## Wave 5 · The theme repository exists and its gate is provably real

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-501 ✓ | · | ⚠️ **Mostly already done, 2026-08-24 audit:** the four renames landed in commit `a0e01b6` (the unit-002 scaffolding turn), which honoured D-029's *"before any unit-002 file is created under the old name"* clause and consumed the first half of this row. `ls specs/` shows English names only and the heading inside each README is correct. **What is left is three comment lines** in `.cspell-project-words.txt` (135-137) that still say *"not yet started, not renamed"*. ⚠️ **The three Spanish words themselves must NOT be deleted** — `DECISIONS.md` (D-029) and this very row quote the old paths permanently, both files are spell-checked, and `cspell` is blocking; removing them turns the gate red. D-024's table says the remedy is *"the token leaves the corpus"*, and for these three that is now impossible — amended, never edited (rule 8). Original text follows. Rename the four Spanish unit directories: `002-base-tema` → `002-base-and-theme`, `003-contenido-demo` → `003-demo-content`, `005-ia-governance` → `005-ai-and-governance`, `007-publicacion` → `007-publication`; update the heading inside each `README.md` and every cross-reference | The grep over tracked files returns **0 lines outside two named exclusions**: `specs/000-project/DECISIONS.md`, which is **append-only** and whose D-029 must keep quoting the old names to stay a record of what was renamed, and **this row itself**, which names them for the same reason. Stated as run: `git ls-files \| grep -v '^specs/000-project/DECISIONS.md$' \| grep -v '^specs/002-base-and-theme/tasks.md$' \| xargs grep -n "base-tema\|contenido-demo\|ia-governance\|007-publicacion"` → **0 lines**. ⚠️ Criterion corrected 2026-08-24: as first written it was **unsatisfiable** — it demanded 0 lines from a set that provably contains two, so the task could only be closed by lying or by editing an append-only file (I-044’s neighbour: a criterion whose own evidence file falsifies it). `git log --follow` resolves on each moved README | D-029 |
| T-502 ✓ | H | Scaffold `agora_theme`: `agora_theme.info.yml` (`type: theme`, `core_version_requirement: ^11`, `base theme: false`, regions `header`/`content`/`footer` at minimum), `composer.json` (`drupal/agora_theme`, `type: drupal-theme`, `license: GPL-2.0-or-later`), `LICENSE.txt` (GPL-2.0-or-later, byte-identical to the template's), `README.md`, `.gitignore`, `.gitattributes` | `composer validate --strict` exit 0; the info file parses; **exactly one** `*.info.yml` at the repository root. ⚠️ **Two corrections, 2026-08-24:** add **`.cspell-project-words.txt`** to the file list — `cspell` is blocking from the theme's first pipeline and has no exists-gate, and the README will contain *Ágora*, *drupalcode* and British spellings; and the `composer.json` needs a `description`, because `--strict` promotes that warning to an error and it reads as a mystery failure | ~~D-025~~ **D-014(b),(c)** — *blocker corrected 2026-08-24 on evidence: D-025 decides when the **template** may name the theme in `require`, and its own stated cost is "wave 7 cannot start until 1.0.0 is tagged". It is already carried by T-701 and T-702. Across all three of D-025's options the files this row creates are byte-identical, so it never blocked this row. What does constrain it is D-014 riders (b) and (c). Wave 5's theme lane is blocked on nothing but a path to clone into.* |
| T-503 ✓ | · | ⚠️ **Repo cell corrected H → · and blocker removed, 2026-08-24.** The theme repository ships to end users and must not grow a `research/`; the note lands in `specs/002-base-and-theme/research/`. And its blocker `T-502` was spurious for the same reason as T-502's own: reading documentation requires no theme to exist. **It now runs FIRST in the wave**, because it is the one task whose answer can change T-502's file list. Read `canvas/docs/components.md` and `docs/shape-matching.md` at 1.10.1 and record, in `research/`, whether a front-end theme has any Canvas-side obligation beyond regions | A dated note stating either "no obligation found, here is what was read" or a named list; the note cites file paths and line ranges, not summaries | — |
| T-504 ✓ | H | `.gitlab-ci.yml` for the theme: upstream `include:` byte-identical to the template's, plus `_ALL_VALIDATE_ALLOW_FAILURE: '0'`, `_PHPUNIT_EXTRA: '--fail-on-empty-test-suite'`, `_CSPELL_EXTRA`, `_CSPELL_SHOW_PROGRESS: '1'`, `_PHPCS_EXTRA: '-p'` | `tests/bin/no-blind-phpunit` (theme copy) exits 0 over the file; ~~the `include:` block diffs clean against `gitlab_templates` guidance~~ — ⚠️ **unfalsifiable as written, corrected 2026-08-24: "guidance" is not an artefact anyone can diff.** Restated against a concrete object: `diff <(sed -n '/^include:/,/workflows\.yml.$/p' <THEME>/.gitlab-ci.yml) <(sed -n '/^include:/,/workflows\.yml.$/p' <TEMPLATE>/.gitlab-ci.yml)` produces **0 lines of output**; **no** `allow_failure` and **no** `CI_ALLOW_DEV` anywhere in the file | T-502 |
| T-505 ✓ | · | **Measure** whether a pushed branch is enough for drupal.org packaging: run the exact command below against **both** projects and record the HTTP codes and the date | Recorded output of `curl -s -o /dev/null -w "%{http_code}" "https://packages.drupal.org/files/packages/8/p2/drupal/agora_transparency~dev.json"` and the same for `agora_theme~dev`. **Any** result is a pass; silence is a fail. ✅ **MEASURED 2026-08-24T11:56:01Z, and the answer is decisive: a pushed branch is NOT enough.** All four URLs return **404** — `agora_transparency`, `agora_transparency~dev`, `agora_theme`, `agora_theme~dev` — and `updates.drupal.org/release-history/<p>/current` answers *"No release history was found"* for both. `agora_transparency`'s `1.x` has been pushed since 2026-08-22 and **two days of packaging produced no Composer entry**. So the release node, not the branch, is what puts a project in Composer's index; T-506's fallback is not a fallback, it is the path. ⚠️ This applies to **the template too**, which nobody had checked | — |
| T-506 ✓ | · | 🔴 **BLOCKED 2026-08-24 — `403 You are not allowed to push code to this project`.** The commit is ready (`973870b`) and the push is refused **per project**: the same credential, same host, same machine pushed to `agora_transparency` minutes before. So this is authorisation on drupal.org's side, and it is **[andres]**'s to clear — the two things worth checking first are whether the theme project lists him under **Maintainers** with *Write to VCS*, and whether drupal.org has propagated permissions for a project created the same day. ⚠️ **The coordinator was to do the push half** (it is a branch push, not a tag, release or merge); that split still stands and only the unblocking is his. **[andres]** Push `agora_theme` `1.x` to drupalcode and, if T-505 shows a branch is not enough, create the `1.x-dev` release on the project page | `git ls-remote --symref` shows HEAD pinned to `refs/heads/1.x`; the API reports `1` branch; the T-505 command returns **200**. ⚠️ **Two amendments, 2026-08-24.** (a) The API reports `default_branch: main` on a repository with **zero** branches; whether GitLab repoints HEAD to the first branch pushed is **NOT MEASURED**, so a `main` answer means *Settings → Repository → Default branch*, not *the push failed*. (b) The `200` has an unbounded wait inside it and **T-505 has now measured that the wait is real** — do not soften it, bound it: if the release node exists and the URL still returns 404, record both timestamps and re-measure at +2h and +24h; **a 404 with a release node present after 24h is a 🔴 finding, not a pass** | T-502, T-504, T-505, **T-508** |
| T-507 ✓ | H | First observed pipeline: read the **job list** from the API, not the UI or the badge, and write the observed table into the theme's `README.md` in the same commit that makes it true | `jobs >= 5` — ⚠️ **satisfiable only because T-508 was moved ahead of T-506, 2026-08-24.** Measured against `gitlab_templates@main`: a tree holding only T-502 + T-504 output materialises **four** jobs (`composer`, `composer-lint`, `cspell`, `eslint` — the last on `**/*.yml`, which the `.info.yml` alone satisfies). `phpcs`, `phpstan` and `phpunit` are gated on PHP files and test files that a theme at this stage does not have; that absence is correct and must be stated in the theme's README gate table, or a later session will "fix" a job that was never meant to be there. The fifth job is `agora-invariants`, which has no exists-gate — so **T-508 lands in the same push T-506 makes**. **The threshold was NOT lowered:** a gate lowered to go green is an automatic 🔴; every `status == "success"`; every `allow_failure == false`; the table names each job, its stage and its `allow_failure`, with the pipeline id and commit sha | T-506 |
| T-508 ✓ | H | Theme `tests/bin/`: the named subset plus `shared-invariants.manifest` (sha256 per shared script + the `agora_transparency` sha it was taken from), and `agora-invariants` as a local blocking job | The runner prints `N checks — 0 failures` with `N` stated; a deliberately edited byte in one shared copy makes it print `N checks — 1 failures` and exit non-zero | D-028, T-504 |
| T-509 | H | **The unit's centrepiece.** `tests/src/Nightwatch/Accessibility/axe.js`: `'@tags': ['agora_theme']`, `drupalInstall`, navigate to at least one theme-rendered page, `browser.axeInject().axeRun('html', {})` | The `nightwatch` job is **in the observed job list** with `allow_failure: false`; its log shows **`N` tests executed, `N >= 1`**; the axe result names the **number of rules run** and the number of violations. `0 tests` is a **failure**. ⚠️ **Added 2026-08-24 — this row could pass having scanned the wrong theme.** `drupalInstall` gives you a site, not a site using `agora_theme`, so a green here is compatible with axe having audited **Olivero**. That is the exact species of false green this project keeps catching, on the unit's centrepiece. Also required: **the log names the active theme as `agora_theme`, and the scanned page's markup contains a marker emitted only by this theme** | T-507, D-027 |
| T-510 | H | **Dirty case for T-509** (D-019 rider e): prove on a throwaway branch that a deliberately inaccessible fragment — an `<img>` with no `alt` — turns the `nightwatch` job **red** | A pipeline id and job id where `nightwatch` is `failed` and the log names the axe rule that fired. The branch is then deleted; the evidence is the pipeline record, not the branch | T-509 |
| T-511 ✓ | T | Promote the install smoke: add `OPT_IN_TEST_DRUPAL_CMS: '1'` and `_AUTORUN_DRUPAL_CMS: 'all'` to the template's `.gitlab-ci.yml`, with a comment citing `include.drupalci.main.yml:487-496` | The next pipeline's job list contains `Drupal CMS` with `status: success` and `allow_failure: false`; **`jobs >= 9`**; the CLAUDE.md gate-A table is updated **in the same commit** (the derived-list prohibition) | Amendment to D-020 |
| T-512 | · | Record **I-053** — ⚠️ **renumbered 2026-08-24, from I-051, before the task ran**: I-051 and I-052 were taken the same day by the cspell episode, so this row's citation would have pointed at a different lesson (I-044's shape, caught by writing the number down rather than by a script). Use the next free number on disk, not the one this row was drafted with — the fourth rung — *defined upstream · materialised in this pipeline · collected by the harness · actually executed* — with the `nightwatch.conf.js` glob and the `DRUPAL_PROJECT_FOLDER` branch as the two halves of the evidence | The idiom is in `IDIOMS.md` under the **next free number verified on disk**, cites both file:line references, and states the rule: **read where the harness looks, not only where CI puts you** | T-509 |

---

✅ **UNBLOCKED — `drupal/agora_theme` is live.** The 403 held through four attempts from this
session and through **[andres]**'s own attempt from the same machine, on a project where he is
**Maintainer in GitLab and listed on the drupal.org project page**. Diagnosed as far as evidence
allowed: it was **project-level, not branch-level** — pushing an unrelated throwaway ref was
refused identically — and it was **not the credential**, since the same stored credential pushed
to `agora_transparency` on the same host minutes either side. **He cleared it in the end**; the
cause is not recorded here because nobody measured it, and writing down a guess would be worse
than the gap. ⚠️ If a third project is ever created, expect this and budget for it.
**✓ = done and evidenced 2026-08-24 [ejecutor].** Three rows closed in the wave's first tranche,
none of which needed the theme repository to exist:

- **T-501** — the rename half landed in `a0e01b6`; `ls specs/` is English-only and each moved
  `README.md` keeps its heading. The residue was three stale comments in
  `.cspell-project-words.txt`, now corrected. The grep with its two named exclusions returns
  **0 lines**. ⚠️ The three Spanish tokens are **kept deliberately** — see the comment block above
  them in that file.
- **T-503** — **no obligation found.** `docs/components.md` and `docs/shape-matching.md` read at tag
  `1.10.1` (commit `50a7f2f`), both HTTP **200**. The word *theme* appears **five times in 755
  lines** and not one is a requirement on a theme: two are PHP namespace fragments
  (`components.md:73-74`), one is the conditional SDC criterion (`:98`), one explains block plugins
  versus regions (`:120`), one offers `/schema.json` as an available mechanism
  (`shape-matching.md:136`). `shape-matching.md:156-157` scopes itself out loud. The only
  conditional obligation — `components.md:97-107`, the seven-item SDC eligibility list — binds a
  theme **only if it ships its own Single-Directory Components**, which Ágora's does not in unit
  002, and its penalty is bounded: the component is not offered in the Canvas UI. **R-1 is
  confirmed and T-502's file list does not change.** Incidental: `components.md` links four times
  to `docs/shape-matching-into-field-types.md`, which **404s** at that tag — renamed, links not
  updated (I-041's shape, upstream this time). Recorded in
  `research/2026-08-24-canvas-theme-obligations.md` with byte sizes, blob ids and a
  `NOT MEASURED` list of what was **not** read.
- **T-502 / T-504** — the theme is scaffolded in its sibling checkout and **committed locally**
  (`973870b`), not pushed. `composer validate --strict` exit **0**; the info file **parses** (not
  eyeballed — loaded and its keys printed); **exactly one** `*.info.yml` at the root and in the
  whole tree. `LICENSE.txt` sha256 `ab04becd…`, `cmp` silent against the template's, 18341 bytes.
  The info file's `name:` is **byte-identical** to the drupal.org title (`c381676f7261…`). T-504's
  restated criterion holds: the two `include:` blocks diff to **0 lines**, and
  `grep -c 'allow_failure\|CI_ALLOW_DEV'` returns **0** — the implementer had to **reword its own
  comments**, because an explanation of why those strings are forbidden contains the strings and
  would have fooled the check. Word list: **2** entries, both measured by running the checker with
  an empty list first; four of the five words the dispatch predicted needed **no** entry, and
  declaring them would have been the unjustified-entry pattern D-024(4) forbids.
- **T-508** — D-028=B implemented, **and both halves of the criterion were run, not asserted**:
  clean **23 checks — 0 failures**, exit 0 · one byte flipped in a copied invariant → **23 checks
  — 1 failures**, exit **1** · restored → **23 checks — 0 failures**. ⚠️ **The flipped byte left
  `no-patches` itself still exiting 0** — only the drift detector caught it, which is exactly the
  case D-028 bought it for: an edited invariant that still passes.
  Manifest: 5 records, each with local sha256, source sha256 and the source commit (`935c133`),
  and a `verbatim`/`adapted` column the manifest **itself asserts** — verbatim requires the hashes
  equal, adapted requires them to differ, so a half-updated manifest is a finding.
  `identity-strings` is recorded `adapted` and why: it gained the **positive** form of the
  forbidden `no-code-in-template` (exactly one root `*.info.yml`, named `agora_theme`), and a
  pre-first-commit projection — `git archive HEAD` has nothing to archive until T-506 pushes, so
  without it the invariant could not run until after the push it guards. Both code paths exercised
  and in agreement: 14 files in scope, 12 naming the product.
  `tests/bin/spellcheck` copied too, though D-028 does not name it — and it earned it immediately:
  **19 issues in 6 files** on its first run. Now **13 files checked, 0 issues**, 13 justified
  entries. ⚠️ 11 of the 19 sat inside hash-pinned copies, where a `cspell:ignore` directive would
  have **tripped the drift detector** — a dictionary entry was the only correct remedy of
  D-024(3)'s four.
  Measured in passing, closing an open question in the word list's own header: cspell's
  `stripCaseAndAccents` **does** fold case (`FILT` covered by `filt`) and still **does not** fold
  accents.
- **T-506 / T-507** — **the theme is published and its gate is real.** `git ls-remote --symref`
  reports HEAD pinned to **`refs/heads/1.x`**, one branch, at `e037b0f`.
  **First observed pipeline: `934789`, read from the API** — not the UI, not the badge:
  `agora-invariants` · `composer` · `composer-lint` · `cspell` · `eslint`. **5 jobs, every one
  `success`, every one `allow_failure: false`, zero named exceptions.**
  ✅ **`jobs >= 5` was met without lowering anything**, which was the whole point of the reordering:
  a tree holding only T-502 + T-504 materialises **four**, and the fifth is `agora-invariants`,
  which is why T-508 was moved ahead of the push. The threshold stayed; the order changed.
  ✅ **The prediction was exact.** The `orquestador` derived those five job names from
  `gitlab_templates@main`'s rules **before** the repository existed, and named the four absences
  with the rule that causes each — `phpcs`/`phpstan` on `.php-files-exist`, `phpunit` on
  `**/tests/**/*Test.php`, `nightwatch` on `tests/src/Nightwatch/**/*.js`, `stylelint` on
  `**/*.css`. All four are absent, and **each absence is correct rather than a missing check**.
  The theme's own runner printed **`25 checks — 0 failures`** inside that job, with its parts
  stated: `no-blind-phpunit` 1 file / 1 invocation / 1 guarded / 0 unguarded · `identity-strings`
  3 identity files / 13 packaged files naming the product / 1 root info file · `no-unstable-deps`
  **0 require entries** (vacuous **and saying so**, per I-028) · `shared-invariants` 6 records over
  9 swept files · 0 findings anywhere.
- **T-606 / T-607 / T-608** — **the theme has an identity, and its contrast is a gate rather than a
  claim.** 18 colour tokens in one file, Public Sans 2.001 self-hosted with its OFL, a rem type
  scale, visible focus and reduced-motion handling. **38 contrast pairs, 0 below threshold**, at
  4.5:1 for text and 3:1 for focus rings and table rules.
  ✅ **The palette was computed BEFORE the checker existed, which is the right order:** T-608's job
  is to *verify* a palette, not to discover that nobody had calculated one. **One colour failed on
  first pass and was fixed rather than excused** — the table rule started at **2.52:1** and was
  darkened until it cleared 3:1 on the darkest surface it actually sits on. These are the lines
  separating one salary from another in a table; calling them decoration was the easy way out.
  ⚠️ **Dark mode was REFUSED, with a reason worth keeping:** a second palette redeclares every
  token name inside a media query, so a flat name→value parse **keeps whichever it read last and
  reports clean having checked one palette of two**. T-608 now detects that at-rule — matching the
  **at-rule and never the string**, because `tokens.css` says those words in prose explaining the
  absence, and a string match would file a finding against the comment documenting the rule.
  **T-607 verified the typeface from the archive that was downloaded, not from the release page:**
  the OFL's first line, `head.fontRevision` **read from inside the woff2 binaries**, and all three
  files byte-identical to upstream. D-030's two mandatory pre-adoption checks measured on those
  binaries: **565 codepoints per face, every Spanish diacritic present, none missing**, and `tnum`
  in GSUB — with the catch that `onum` is *also* present, so the table CSS must pin lining tabular
  figures rather than trust the default. **No subsetting**: it would save 30 KB and put the Spanish
  alphabet one careless unicode range from becoming tofu in a marketplace screenshot.
  **The no-external-font grep returns 0 — and returned 1 on its first run**, matching a comment
  that quoted the forbidden patterns while documenting the criterion. A check tripped by its own
  documentation teaches people to add exceptions.
  **T-608 makes the ratios assertion 7 of 9**, because a checker that only computes ratios goes
  green while the file it reads rots. It also fails on a duplicate token, a pair naming an
  undefined token, **a token no pair mentions**, a malformed line, **a declared minimum that is
  neither 4.5 nor 3** — without which anyone could dim a colour and edit its own threshold down —
  and **`0 pairs checked` exits 1**, which is I-028 and the likeliest way this would have rotted
  into decoration. **Eleven negative paths executed, all exit 1.**
  **The arithmetic was validated against four independently known values first:** 21.00 for black
  on white, **4.54 for the canonical AA boundary gray**, 4.48 for one shade lighter — correctly
  **below** — and 4.00 for red on white, which also proves the three-digit expansion, so
  `stylelint`'s blocking `color-hex-length: short` is honoured by the parser rather than fought.
  Ratios are compared **unrounded** and printed rounded: 4.4996 is below 4.5 even when it prints
  as 4.50.
  ⚠️ **The WCAG erratum was read and deliberately NOT guarded**, with the arithmetic in the header:
  the wiki threshold is `0.03928` and IEC's correct value is `0.04045`, but `10/255 = 0.039216`
  falls below the first and `11/255 = 0.043137` above the second, so **no 8-bit value lands between
  them**. A check that cannot fire is decoration.
  ⚠️ **The job list went 5 → 6 and `stylelint` FAILED on its first run** — one wrapped font stack.
  It had been linted locally with core's own config and come back clean: the divergence was the
  **formatter, not the linter**. The CI job **symlinks `.prettierrc.json` to core's before running**
  and the local run had no such symlink, so prettier used its own defaults. **A local check that
  models most of a gate is not the gate, and the half it does not model is where something gets
  through** — the same shape as the `phpcs` lesson, two commits earlier.
  🟡 **The manifest cannot hold a record for a local-only script**, verified against the checker
  rather than assumed: records need six fields, `role` is closed to two values, exactly five
  `role=invariant` are enforced, and a `source_sha256` would mean **inventing a 40-hex commit for a
  file the template has never held** — an invention chosen to satisfy a check while describing
  nothing. Declared through `LOCAL_ONLY` instead, with a prose block saying why.
- **T-602 / T-604 / T-605** — **`config/` is now 100 objects; PHPUnit `13 tests, 1423 assertions,
  0 failures`** (was 9 / 1220).
  **T-602 ships TWO roles, not three, and the missing one is the interesting part.**
  `agora_base_editor` (31 permissions) and `agora_base_reviewer` (8). **No `publisher`** — measured,
  not aesthetic: a publisher distinguishable from an editor is one that can change `status`, and
  `NodeAccessControlHandler.php:256` gates that field on `administer node published status` **or**
  `administer nodes`. **Both match `^administer `**, the first is `restrict access: true` and
  site-wide across *all* content types so it cannot even be scoped to Ágora's six bundles.
  **Creating the role meant granting exactly what the criterion forbids.** Publication is content
  moderation's job, and moderation is unit 004's row.
  **Distinguishable in BOTH directions, falsified against real entities rather than asserted from
  strings:** the reviewer sees unpublished records and cannot create, edit or delete; the editor
  creates and edits and **cannot see another author's unpublished record**; neither can touch
  `status`. Two deliberate omissions: **no delete permission at all**, and the `procedure type` and
  `status` terms are **not editable** — they are statutory closed lists.
  **The three inherited offenders are recorded AND asserted**, with provenance, because an
  exception nobody checks rots. The `is_admin` trap is handled: exemption keys on the **flag**, and
  an exempted role must enumerate **zero** permissions.
  **T-604: 16 `?` keys inspected, 16 exist, 0 absent — all 16 prefixes dropped**, verified on a
  **second, independent install path** where the recipe would hard-fail if a name stopped
  resolving. 21 component actions, each with a one-line reason. **The front-end review found a real
  gap:** the inherited list disables `canvas.component.sdc.navigation.title` and leaves its sibling
  `…navigation.message` **enabled** — same directory in core, same admin chrome, and
  `status: experimental` besides. Disabled.
  ⚠️ **Asking how the test FAILS found dead code in the test itself:** a `?`-prefixed key does not
  start with `canvas.component.`, so it hit `continue` and **the `?` assertion could never fire**.
  Fixed by stripping the prefix before filtering; the dirty run then failed on the intended
  assertion rather than merely on the count. A passing test whose assertion is unreachable is the
  purest form of the false green this project keeps finding.
  **T-605: `/home` kept, and the round trip is asserted from the alias's own target rather than a
  hard-coded `/page/1`** — so it survives the entity id changing.
  ✅ **Step 4b did not fire, and the reason is now known rather than observed:** `isAction()` routes
  only the **default** config of core, System and User — `anonymous` and `authenticated` are theirs,
  new roles are not. Four `system.action.user_{add,remove}_role_action.*` files came along
  unrequested, auto-created by `UserHooks.php:249`'s `user_role_insert` hook, and were kept because
  the baseline already ships the pair for `administrator` and `content_editor`.
  ⚠️ **One judgement call, ratified [ejecutor] 2026-08-25:** `content_moderation` added to
  `install:`. The reviewer role's config **declares a dependency on it** — `view any unpublished
  content` is its permission — so without it **the role fails to import**. Core module, no SBOM
  line, and **no bundle is put under moderation**: this is the dependency, not the feature. Same
  defensive pattern already recorded for `views` and `datetime_range`.
- **T-615** — **six table views, `config/` is now 94 objects: 47 columns and 11 exposed filters.**
  Mechanism as [andres] ruled it: **core Views exposed filters, no Search API, no Facets, no new
  dependency** — `sbom-check` still 9 queried, 9 covered, 0 findings.
  ✅ **PHPUnit: `9 tests, 1220 assertions, 0 failures`** (T-614 was 7 / 795).
  ✅ **All four layers built, and every one of them was SEEN TO FAIL before being believed** — six
  injected faults, each failing at a named assertion: a deleted column (set equality, assertion
  14) · **Contract's `bidder_count` injected as a Grants column — the union type D-026 refuses —
  caught at assertion 228** · a deleted facet (the derived-spine assertion, 253) · `empty_table`
  flipped (*"an element matching css table appears on this page, but it should not"*) · a column
  deleted but its field kept · and a column marked `exclude: true`.
  ⚠️ **That last fault found a REAL HOLE and it was closed rather than noted:** a column marked
  `exclude` **passes the config-level set equality and never renders**. The kernel test now asserts
  `exclude` is falsy per column (+47 assertions), and re-injecting the fault makes it fail at its
  own named message. A layer that could be defeated by a flag nobody checked is not a layer.
  🔴 **The spine does NOT apply the way the dispatch assumed, and the model won the argument.**
  I said the three financial regimes carry all three facets; **disk says they carry `área` and
  `estado` but NOT `financial_year`** — their temporal field is `field_agora_base_period`, a
  **daterange** (the contract's term), which is not a taxonomy facet. **Not forced.** Each view
  exposes what its own bundle has, and the test derives that expectation **from the model** rather
  than from a typed list, cross-checked against a transcribed measurement so **two independent
  sources must agree**. ★ **Ruled: leave it.** Attaching `financial_year` to the three regimes
  would add a field their regime does not name — exactly what D-026 forbids, and
  `testFinancialRegimeBundles()` would fail on it, **correctly**.
  ⚠️ **One named base-field exception, declared rather than smuggled:** read literally, layer (a)
  forbids a `title` column — leaving a table of contracts with no contract name and no link to the
  record. `VIEW_BASE_FIELD_COLUMNS = ['title']`, and **its size is itself asserted**, so widening
  it is a visible edit to a test file rather than a hole in it. `created`, `nid` and a column from
  another regime all still fail.
  **The empty state, which only this unit could ever test:** each route returns 200, renders the
  empty text, and emits **no `<table>`** — and on the populated pass, 1 `<caption>`, N
  `<th scope="col">`, N `<td>`. The selector was **checked, not recalled**: this Drupal emits no
  `view-id-*` class, so the container is `div[class*="js-view-dom-id-"]`, which core's own
  `views-view.html.twig` writes in every theme.
  **The fixture is built from field *definitions*** — a field type with no fixture rule calls
  `fail()` rather than skipping — lives in the test class and the test database, and **never
  touches the export rig**. `content/` is still **1 file**.
  ⚠️ **Why the byte audit still says 2:** core's full pager defaults contain four **non-ASCII**
  arrow characters, so the six views declare only `type`, `items_per_page` and `offset` and let
  Views merge the rest at runtime. Written out in full, six shipped objects would each have carried
  bytes above `0x7F` **for strings this project neither wrote nor wants to own**.
  🟡 **Nothing links to the six routes** — no menu entry, no navigation. T-603 owns the library and
  the cross-type listing; **no signed row owns menu placement**. Same shape as the CSV-rendering
  gap, and carried the same way.
- **T-614** — **the sixth and last bundle. 14 new files, `config/` is now 88 objects, `node
  types: 6`** — asserted, not counted by hand. Fields per bundle: Contract 10 · Agreement 7 ·
  **Dataset 7** · Grant 6 · Person 6 · Document 5.
  ✅ **PHPUnit on a clean install: `7 tests, 795 assertions, 0 failures`** (T-613 was 6 / 644).
  ✅ **And the new assertions were FALSIFIED rather than trusted:** deleting one shipped field
  file makes `testDatasetBundle` fail at assertion 10; restoring it returns `OK (1 test, 137
  assertions)`. A test nobody has seen fail is not evidence.
  **The budget-execution table needed no seventh bundle and got none** — it is a Dataset with a
  machine-readable distribution and a `financial year`, and the bundle description says so.
  **Reuse is asserted, not claimed:** each of the three reused storages must be **absent from what
  T-614 created AND already attached to another bundle**, so a quietly duplicated storage fails
  both halves.
  ✅ **THE ANNEX IS VERIFIED, and the row's third branch was not needed.** *Reglamento de
<!-- cspell:disable -- D-024(3): the Annex's headings and the two corrected forms are quoted verbatim from the official Spanish text of Reglamento (UE) 2023/138. Scoped, not declared - these are a statute's words, not this project's vocabulary, and rewording them would destroy the evidence the row exists to record. -->
  Ejecución (UE) 2023/138*, Anexo, read at `https://www.boe.es/buscar/doc.php?id=DOUE-L-2023-80077`
  on **2026-08-25**, extracted from the fetched HTML rather than from a summary, with the DOUE PDF
  as corroboration (sha256 `9122dd60…`). **Six categories, and a grep for a seventh heading returns
  nothing.** ⚠️ **Two corrections to the research file's snippet**, which is exactly why the row
  demanded source verification: it wrote *"observación de la **T**ierra"* (official: lower-case)
  and *"estadística"* singular (official: **Estadísticas**).
<!-- cspell:enable -->
  ⚠️ **The English original is `NOT MEASURED`** — EUR-Lex answers **HTTP 202 with a bot challenge**
  from this machine, with and without a browser user agent. The verified list is the **official
  Spanish text**, not the English. And **the six categories are named in no config and in no doc**:
  the verification exists so the record is honest, not so a list could ship.
  ⚠️ **Step 4b fired nothing for the third round running** — `recipe.yml` and `composer.json` are
  **byte-identical** across the export. `list_string` needs core's `options`, already enabled by the
  declared chain; `file` was already carried by Person's declaración. No contrib, no SBOM line.
  **Two limits on scope recorded as choices rather than defaults:** `format` and `licence` are **closed
  lists, not free text** — a catalogue filters on a value, not on a filename suffix — and the
  distribution's accepted extensions are asserted **equal in both directions** to `format`'s
  allowed values, so a dataset cannot declare a format no file on it could carry. And **cardinality
  1**: DCAT allows several distributions per dataset, but a facet and a sortable column both break
  on an unbounded list, so a second format is a second node.
  ⚠️ **`update_frequency` did not fit** — 16 characters against the 15 left by the
  `field_agora_base_` prefix. The field is `frequency` and the **label** carries the full name.
    ✅ **The gap below is RULED, 2026-08-25 [ejecutor], and it costs no task row.** It goes to
  **unit 003**, not to T-615 and not to a new row here — and the reason is the project's own
  standard rather than convenience: **unit 002 ships no demo content**, so there is no CSV for a
  renderer to render, and a renderer built here could only be proven by a test that asserts nothing
  about a real distribution. Building what cannot be proven is the failure this unit has spent all
  day catching. Recorded in the carried-debt table with an owner, so it is carried rather than
  forgotten — which is the whole distinction D-031's headroom is protecting.
🟡 **A gap that falls between two rows, found now:** D-026 says the Dataset's *rendered* table is
  the accessible source of truth the charts read. This row ships the file and the fields; **nothing
  yet renders a CSV distribution as a `<table>` with a `<caption>` and `<th scope>`**, and no signed
  row owns it — T-615 owns view columns, unit 003 owns demo content, and the rendering falls
  between them.
  🟡 **`~/agora-smoke` holds uncommitted state again** (rsync'd to run PHPUnit, because the export
  rig has no phpunit and adding one would falsify D-032's recorded manifest). **Rebuild it before
  it counts as a clean-install smoke.** All three rigs left **stopped**; T-615's baseline is
  `~/agora-export/export-scratch/t614b`.
- **T-613** — **the three legal regimes exist. 37 new config files, `config/` is now 74 objects.**
  Field counts per bundle, printed by `config-inventory`: **Contract 10 · Agreement 7 · Grant 6**
  (Document 5, Person 6). **Grant adds nothing to the shared pattern, and that is the correct
  outcome rather than an oversight** — the shared six is all art. 8.1.c) names.
  ✅ **PHPUnit on a clean install: `6 tests, 644 assertions, 0 failures`** — 301 from T-612 plus 306
  from the new method plus 37 from `RequirementsTest`, whose per-object loop grows with `config/`.
  The functional `InstallTest` applies the template with all 74 objects present, so the model
  **imports**, not merely parses.
  **Reuse, measured:** six storages **attached, not recreated**; five new, the longest tying the
  model's existing tightest at **31 of Drupal's 32 characters**.
  ⚠️ **The non-ASCII audit is now bounded by a test, not by discipline.** Exactly **two** shipped
  objects carry a byte above `0x7F` — `importe de adjudicación` on Contract's amount field and
  `subvención` on Grant's type — and the third permitted citation, `convenio`, is pure ASCII so it
  correctly does not appear in a byte audit. The test asserts the set **equals** those two, so a
  **fourth Spanish term fails rather than slips in**. `LCSP` deliberately still absent.
  ⚠️ **Step 4b fired nothing again**, and that is a measurement: `recipe.yml` and `composer.json`
  are **byte-identical** across the export, because `datetime_range` (T-601) already covers
  `daterange` and every other type is core. No contrib, no SBOM line.
  ⚠️ **Art. 8.1.b) also names the *modificaciones* of a convenio**, but D-026 and this row
  fix Agreement's additions at exactly **one**. The signed set was implemented and the discrepancy
  **reported instead of resolved** — adding it is a model change, not an implementer's call.
  ⚠️ **D-026(a) says *"`Agreement` ships with six fields and `Grant` with three"*, and shipped is
  7 and 6.** They reconcile: (a) quotes **the law's named-field counts** from the research file,
  not config fields. Recorded so nobody later quotes 6/3 as a config expectation.
  ⚠️ **A bug the gate caught, worth keeping:** the first cross-check used `assertNull` on
  `FileStorage::read()` of an absent object. It returns **`FALSE`**, not `NULL` — so **a correct
  model failed**. Rewritten to compare against the shipped set, which has no such convention to
  get wrong.
- **T-612** — **`Document` and `Person` exist. 26 new config files, `config/` is now 37 objects**,
  every one produced by the rig and copied in by baseline diff, **none hand-written**.
  **The two money fields are `decimal`, scale 2** — `retribución anual` and `indemnización`, art.
  8.1.f) — which is the same load-bearing point as `importe`: a table has to sort them.
  ⚠️ **They could not both reuse `field_agora_base_amount`** — one storage cannot attach twice to
  one bundle — and putting the three financial regimes' `importe` on a `Person` would have been
  **the union typing D-026 exists to refuse**. The reasoning is in the code's docblock, not only
  in this row.
  **Reuse before creation, measured:** 3 vocabularies attached and **0 duplicated**;
  `field_agora_base_area` reused on both bundles; `field_agora_base_summary` created for Document
  and reused for Person's *perfil y trayectoria*. **Budget stayed a Document**, there is no seventh
  bundle and no `Person` subtype — the declaración de bienes is an unconditional optional file,
  exactly as the row ruled.
  ✅ **The strongest evidence available was obtained: PHPUnit ran the whole package** —
  **`5 tests, 301 assertions, 0 failures`** — and that includes the **functional `InstallTest`
  applying the template to a clean Drupal with all 26 new files present**. The config does not
  merely parse; it **imports**.
  ⚠️ **Step 4b fired nothing this round, and that is a measurement rather than an absence:**
  `diff after2/recipe.yml t612/recipe.yml` is **empty**, because every module these field types
  need (`file`, `media`, `text`, `taxonomy`, `options`) is already enabled by the declared recipe
  chain. `media.type.document` is **not ours** — `drupal_cms_media` applies core's
  `document_media_type`, verified on disk. **No contrib, no SBOM line owed.**
  ⚠️ **`testSharedSpine()` had to be widened and the way it was widened is the point:** its
  set-equality assertion said `config/` ships *exactly* T-601's six storages, and T-612
  legitimately adds eight. The **declared list** grew to the union; the assertion stayed
  **byte-identical and still fails in both directions**. It was **not** relaxed to a `>=` and no
  total was hard-coded — which is the distinction between a gate that grows and a gate that rots.
  ⚠️ **`field_agora_base_financial_year` is 31 characters against Drupal's 32-character ceiling.**
  T-601 predicted this exact field would be the tightest; there is now **no room** for a longer
  name under this area's convention.
  🟡 **For T-615:** the three-facet spine **will not apply uniformly** — Document carries `área`
  and `financial year` but no `estado`; Person carries `área` only. Discovered now rather than
  when the views are built.
  🟡 **`~/agora-smoke` now holds uncommitted state** — it was refreshed with this working copy to
  run PHPUnit, because the export rig has no phpunit binary and adding one would have falsified
  D-032's recorded manifest. **Rebuild it before using it as a clean-install smoke**; a smoke rig
  that was not rebuilt is not a clean install. The T-613 baseline is
  `~/agora-export/export-scratch/t612`; `after2` is spent.
- **T-601** — **the spine exists and `config/` is no longer empty: 11 files**, five vocabularies
  and six field storages, **every one copied out of `diff -r baseline after`, none hand-edited**
  and byte-identical to the export. `importe` is `decimal(14,2)` — **numeric, not text**, which is
  D-026's load-bearing point — and `periodo` is a real `daterange`. Zero node types:
  `find config -name 'node.type.*'` → **0**, the scope ruling enforced mechanically rather than
  promised.
  **The tautology is closed:** `RequirementsTest` now captures `listAll()`, asserts it non-empty
  and **prints the count** before looping — 11 objects enumerated, 0 with `_core`, 0 with `uuid`.
  **Naming convention, decided and stated so later rows inherit it:** machine names **English**
  (rule 6 / D-017), Spanish carried by labels; every identifier takes its functional-area prefix
  per D-011 — `agora_base_*`. `field_agora_base_` eats 17 of Drupal's 32-character ceiling,
  leaving **15**; every field T-612–T-614 needs fits, `financial_year` at 14 being the tightest.
  ⚠️ **D-032's step 4b fired on its first real use, exactly as predicted.** `periodo` needed core's
  `datetime_range`, which the recipe did not install — and the export routed that into its
  regenerated `recipe.yml` as a config action, **not** into `config/`. One hand-transplant into the
  `# -- area: base` block. Had the session copied only `config/`, the field would have shipped
  broken **with no error anywhere**. `recipes:` still lists **10**; `composer.json` diff is empty,
  because a core module owes no SBOM line.
  *Also settled in passing, closing an open claim in the word list's own header:* cspell's case
  folding **works** — `área`/`estado`/`procedimiento` were listed lower-case and their
  sentence-initial forms passed unflagged in the same run that flagged 16 new words. The accent
  claim stays false. ⚠️ And the spell fix sprang **T-501's trap a second time**: the justification
  comment declining to declare three ordinary Spanish connectives **spelled them**, producing four
  findings inside the word list itself. Reworded, not declared — three Spanish descriptions were
  **reworded in the rig and re-exported** rather than hand-edited in the YAML, which would have
  broken D-032's procedure.
- **T-511** — **all three clauses met, and the two that could only be read after a push were read
  from the API.** Pipeline `934533`, commit `09fb47b`: **9 jobs**, `Drupal CMS` **`success`** and
  **`allow_failure: false`**. The CLAUDE.md and README tables were published **empty** in the
  declaring commit and filled in the observing one — the derived-list prohibition satisfied in
  both directions, since neither table ever held an unobserved green.
  The citation the row demands was **verified at source** rather than copied:
  `include.drupalci.main.yml:487-496` on `main` today spans both rules the variable chooses
  between — `.autorun-drupal-cms-rule` (487-492, four `if` alternatives ending `when: always`) and
  `.make-job-manual` (494-496, the fallback that tolerates failure). The five citations already in
  the file were re-checked against today's `main`; **all still land**.
  ⚠️ `_ALL_VALIDATE_ALLOW_FAILURE: '0'` does **not** cover this job — it is in the **`build`**
  stage. It arrived blocking on its own, so **no exception was needed and the exception list stays
  empty**; had it arrived permissive, D-023(5) would have required a dated, owned one.
- **T-505** — measured `2026-08-24T11:56:01Z`; see the row. A pushed branch is not enough, for
  either project.

## Wave 6 · Content model ‖ visual identity (two lanes, disjoint by repository)

### Lane A — template repository only

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-601 ✓ | T | **The shared spine only** (narrowed by D-026, which split this row into five): the five taxonomy vocabularies (`area`, `document type`, `financial year`, `status`, `procedure type`) and the **one shared field pattern** the three financial regimes reuse — `objeto`, `importe`, `periodo`, `contraparte`, `área`, `estado` — created once as reusable field storages, plus the `Document` and `Dataset` bundles' shared base. Built in the WSL rig, exported with `drush site:export` into `config/` | A kernel test asserts **every** vocabulary and **every** field storage by machine name and exits with a stated assertion count; `RequirementsTest` still green (no `_core`, no stray `uuid`) — ⚠️ **and the number of config objects it enumerated asserted non-empty, and printed by the `agora-invariants` job — amended 2026-08-24, twice in one day.** *First correction:* as written the clause was a **TAUTOLOGY**. *Second, after pipeline `934619` proved the first correction unsatisfiable:* **a kernel test cannot print anything.** PHPUnit converts **any** output a test emits — STDOUT **and STDERR alike** — into a `PHPUnit\Framework\Exception`, so both tests failed on their print statements while **every assertion in them passed**. The denominator therefore lives where printing is free and already blocking: `tests/bin/config-inventory`, run by `agora-invariants`. The test **asserts**, the invariant **prints**. ⚠️ And the assertion is deliberately **not** a census: hard-coding today's 11 would force a relaxation every time T-612–T-615 legitimately grow the model, and a gate that must be relaxed as the product grows is a gate that teaches people to relax gates. `RequirementsTest` builds a `FileStorage` over `config/`, `config/` **does not exist**, `listAll()` returns `[]`, and the assertion block passes today with nothing built — so it could not distinguish a good export from **no export at all**, and every later row inherits the check. ⚠️ **Scope ruling, because the row was circular:** T-601 creates **field storages and vocabularies only, ZERO node types**; the six bundles belong to T-612/T-613/T-614, which assert them. The row said it built the *"`Document` and `Dataset` bundles' shared base"* while those rows assert *"both bundles"* — each row now owns what it asserts. ⚠️ **The export instruction is superseded by D-032=B:** `site:export` is authoritative for `config/` **only**, through a **baseline diff**; `recipe.yml` and `composer.json` are never written by the tool | **D-026** |
| T-612 ✓ | T | ⚠️ **D-033 binds this row and T-613–T-615: every label and description is ENGLISH.** Machine names already were. The three terms where the English is a correct translation and still the wrong word — `convenio`, `subvención`, `importe de adjudicación` — keep an English label and name the Spanish term **once in the description, as a legal citation**. That is a citation, not bilingual UI, and it is the only Spanish that may enter shipped config. **`Document` and `Person`** bundles. `Document`: title, `document type` term, file/media, `área`, `financial year`, summary — the art. 6-7 catch-all, including the approved budget, execution reports and cuentas anuales (**budget is not a node type**). `Person`: art. 6.1 organigrama with *perfil y trayectoria*, plus art. 8.1.f) **retribución anual** and **indemnización** as numeric fields, and art. 8.1.h) *declaración de bienes* as an attached file on local representatives | A kernel test asserts both bundles and **every** field by machine name, with the two `Person` money fields asserted to be numeric (not text), and prints the assertion count. ⚠️ **Ruling added 2026-08-24, because the row left it open:** art. 8.1.h) scopes the *declaración de bienes* to **local representatives specifically**, and the row did not say whether that is a conditional field, a `Person` subtype, or an unconditional optional field. **It is an unconditional optional field, no subtype** — a subtype would be a **seventh bundle by the back door**, and D-026 fixed the count at six | T-601 |
| T-613 ✓ | T | **`Contract`, `Agreement` (convenio) and `Grant` (subvención)** — the three legal regimes, each attaching the T-601 pattern plus what its own statute names. `Contract` (art. 8.1.a): + `procedure type`, `importe de licitación`, `nº de licitadores`, `modificaciones`. `Agreement` (art. 8.1.b, Ley 40/2015 arts. 47-53): + `obligaciones económicas convenidas`. `Grant` (art. 8.1.c, Ley 38/2003): the shared pattern unchanged | A kernel test asserts the three bundles, that each carries **all six** shared fields, and that `Contract` carries its four extras — assertion count stated. A second assertion proves **no bundle carries a field its own regime does not name** (the union-type failure D-026 rejects) — ⚠️ **restated 2026-08-24 so it can actually be run: a test cannot assert against a statute.** The oracle exists only as prose in D-026 and the research file. What the test does: it carries a **literal expected field set per bundle, transcribed from D-026's table**, and asserts **set equality in both directions**, printing the six set sizes. Worth keeping — it locks the model against later accretion — but the row must say what it does, because as first phrased it read like a legal check and a reviewer would have read it as one | T-601 |
| T-614 ✓ | T | **`Dataset`** — Ley 37/2007 as amended by Directive (UE) 2019/1024 and **Reglamento (UE) 2023/138**, which binds Spanish local entities directly. Fields: title, description, distribution file + format, licence, `área`, `financial year`, update frequency. Includes the **machine-readable budget-execution table**, which is the accessible source of truth the charts read — not a chart with a table bolted on | A kernel test asserts the bundle and its fields by machine name. ⚠️ **The six high-value categories are snippet-level only in the research file:** before any config or doc names them, the Reglamento's Annex is read at source and the list either confirmed or corrected **in this task**, with the URL and date written into the commit — ⚠️ **or recorded `NOT MEASURED`, added 2026-08-24: the row had no third branch.** If the source is unreachable the row could be closed neither way; `www.drupal.org` is already unreachable from this machine, so this is not hypothetical. **If it lands `NOT MEASURED`, the six categories are named in no config and no doc** — an unverified list does not get to appear anywhere just because it was hard to check | T-601 |
| T-615 ✓ | T | **One table view per bundle** plus the shared facet spine (`área · año · estado`). Each view's columns are that bundle's fields **and no others** — the point of six types rather than one | A functional test asserts each of the six routes returns **200**, and — the criterion that makes D-026 falsifiable rather than an opinion — ~~that **no rendered `<td>` in any of the six tables is structurally empty across the fixture set**~~ — 🔴 **UNSATISFIABLE AS WRITTEN, replaced 2026-08-24, and unsatisfiable in three independent ways.** (1) **Vacuous with zero content:** unit 002 ships none, so the six views render zero rows and zero cells; *"no cell is empty"* is then true by construction and the row prints `0 rows, 0 cells` and **passes** — I-028 verbatim. (2) **With content it measures the wrong object:** *"no empty cell"* holds only if every fixture node has every field populated, so it tests **the fixtures**, not the model — a correct model with a legitimately optional field **fails**, and a union-type model with fully populated fixtures **passes**. It is the opposite of what D-026 wants proved. (3) The model-level claim **already has a home**, at config level in T-613, where it is cheap and exact. **Replacement, in four layers, none of which smuggles in demo content:** (a) for each of the six views, the set of field columns declared in `config/views.view.<name>.yml` **equals** the set of fields attached to that bundle in `config/field.field.node.<bundle>.*`, asserted **both directions**, six set sizes printed; (b) a functional test creates **one node per bundle with every field populated**, asserts the six routes return **200** and that `<td>`-per-row equals the view's column count, printing `6 routes · 6 nodes · C cells` — the fixture lives in the test class, uses the test database and never goes near `site:export`, so the NO-list's narrow exception is satisfied **by construction rather than by discipline**; (c) `find content -type f | wc -l` still returns **1**, which is what makes *"never exported"* true rather than intended; (d) **the empty state, which is a real shipping state and only unit 002 can test it** — each route returns 200 with zero rows, renders the view's empty text, and does **not** emit a bare `<table>` with headers and no rows, which is itself an accessibility defect. Unit 003 cannot make that assertion, because by then there is content. ⚠️ This row also **now owns the facet spine**, taken from T-603 | T-612, T-613, T-614 |
| T-602 ✓ | T | Roles and permissions: editor, reviewer, publisher, least privilege | A kernel test asserts that **no role except `administrator`** holds any permission matching `^administer `, and prints the number of roles and permissions inspected. ⚠️ **Corrected 2026-08-24 — this criterion is probably RED ON ARRIVAL, through no fault of the task.** Ágora applies eleven upstream recipes, several of which create roles; Drupal CMS's editorial roles routinely hold `administer url aliases`, `administer menu`, `administer nodes`. If an **inherited** role does, the only ways to green are (a) strip permissions from an upstream role, changing the product, or (b) narrow the regex — **an automatic 🔴**. So the order is **measure first** (a read-only baseline, dispatched before any implementation), then scope the assertion honestly to **roles this recipe creates**, with every inherited offender recorded as a dated, named exception rather than silently excluded. **Zero roles inspected is a failure, not a pass.**
      ✅ **MEASURED 2026-08-24 on a clean install, and the prediction was right: T-602 IS red on
      arrival.** 4 roles and 61 granted permissions inspected, against a site denominator of **312** (**234 when first measured, before the six bundles and five vocabularies added 78** — re-measured 2026-08-25 rather than left to rot)
      permissions of which 67 match `^administer `. **`content_editor` holds three of them**, and
      the provenance is exact rather than suspected: `administer menu` and `administer url aliases`
      from `drupal_cms_content_type_base/recipe.yml:109-110`, reaching Ágora **transitively via
      `drupal_cms_privacy_basic`**; `administer redirects` from `drupal_cms_seo_basic/recipe.yml:46`.
      **Core's own `content_editor_role` grants zero of them** — so this is not Drupal's default
      being lax, it is two recipes Ágora lists deliberately. `~/agora-smoke` reports the same three
      independently. The three are therefore a **dated, named exception**, not a defect to fix, and
      the assertion is scoped to roles this recipe creates.
      ⚠️ **A trap that would have excused the wrong role.** `administrator` shows **0** granted
      permissions — not because it is clean, but because `is_admin = TRUE` stores none. A test
      keyed on the role **ID** would let it pass by accident and would also let any future
      `is_admin` role pass. **Key on `isAdmin()`, never on the string `administrator`.** | ~~T-601~~ **—** — *blocker corrected: its criterion concerns roles and permissions, not fields; nothing in it needs the content model* |
| T-603 | T | Base views: document library with facets, cross-type listing, search box — the surfaces **above** the per-bundle tables T-615 builds | A functional test asserts each view's route returns **200** and that the rendered listing contains a `<table>` with `<th scope>` on every header cell, or an equivalent semantic list — count of cells asserted. ⚠️ **Two corrections 2026-08-24.** (a) *"Equivalent semantic list"* has **no definition a test can evaluate** — name the chosen structure per surface in this row before implementing, and assert that one. (b) **The facet spine was owned twice**: this row said *"with facets"* and T-615 says *"the shared facet spine (`área · año · estado`)"*. Whichever ran second would either redo it or skip it, and **both look like progress**. Assigned once, to **T-615**, where the six per-bundle tables live; this row keeps the library, the cross-type listing and the search box | T-615 |
| T-604 ✓ | T | Canvas component enable/disable review for the **front-end** (the inherited list is admin-only) | `recipe.yml`'s `config.actions` block names each component with a one-line reason; ~~a test asserts the recipe applies with **0** unresolved `?`-optional keys that were expected to exist~~ — ⚠️ **not evaluable, corrected 2026-08-24: the `?` prefix means *apply if present, SILENTLY skip if not*, and there is no API that reports what was skipped. Silence is the feature.** Restated: after apply, for **each** `?`-prefixed config name, either the config **exists** (→ the `?` is unnecessary and is **dropped**) or it does not (→ the `?` **stays** and this row records why). The count of `?`-prefixed names inspected is printed | ~~T-601~~ **—** — *blocker corrected: it writes `recipe.yml`, not `config/`, and needs Canvas installed rather than six bundles. ⚠️ It should run **before** the export loop, not after: this row and T-605 decide what `recipe.yml` must say, and `recipe.yml` is exactly what the tool overwrites — settling them afterwards means settling them twice* |
| T-605 ✓ | T | The `page.front` gap: `recipe.yml` declares `/home`, the installed site reports `/page/1` | A functional assertion that `/home` returns 200 **and** that `system.site` `page.front` on the installed site equals the declared value — or, if it legitimately cannot, a dated note explaining why and an amended declaration. Silence is not an outcome. ✅ **The mechanism is no longer a mystery, read at source 2026-08-24:** `drupal_cms_helper/src/GenericConfigurationListener.php:45-46` rewrites `system.site` `page.front` to **the alias of the current front page** on export, and `src/ContentLoader.php:29` puts `path_alias` on the exporter's hard reject list with the comment *"Path aliases are created when the content is, and therefore should not be exported."* So the alias `/home` is a property of the **content**, the content is exported **without** its alias, and on a fresh install the alias does not exist — leaving `page.front` at the raw system path. The `/home` in today's hand-written `recipe.yml` is **starter-kit inheritance, never something this project measured**.
      ✅ **MEASURED 2026-08-24 — and the premise above is REFUTED. The `/home` alias DOES exist on a
      clean install** (`/home` → `/page/1`). The real mechanism is a **third file the row never
      mentioned**: `drupal_cms_helper/src/EventSubscriber/RecipeSubscriber.php:40-44` deliberately
      converts the **declared alias to its system path** on `RecipeAppliedEvent`. The export-side
      rewrite is its mirror image, and is gated on a flag defaulting to `FALSE` that only
      `SiteExporter.php:97` switches on — so it runs **during `site:export` only**. The two are a
      **round-trip pair** and both carry the same upstream `@todo` (issue 1503146). The alias
      survives an export because it rides inside the content entity's own `path` field
      (`content/canvas_page/ff94a20d-….yml:21-24`), not as a standalone `path_alias` entity — which
      is all `ContentLoader:29` rejects. **So the gap is cosmetic and self-cancelling**, not a bug.
      ★ **Ruling: keep `/home` and assert the round trip** (option B) rather than declaring
      `/page/1` (option A) — `/page/1` is **entity-ID dependent**, and `site:export` would rewrite
      it back to the alias on every export, so option A would fight the tool forever.
      ⚠️ **CORRECTED AGAIN 2026-08-25, and this time the correction was the wrong one.** The text below said `/home` returns 301 and I wrote it as a fact. **Under `BrowserTestBase` the same recipe returns a plain 200.** The 301 is produced by **contrib `redirect`'s route normalizer**, gated by `RedirectChecker::canRedirect()` on *environment* conditions — script name, request method, maintenance mode — so **the redirect status is a property of the harness, not of the template**. I measured it in DDEV and published it as a property of the product: a measurement quoted without its scope, which is the exact failure I-045 names, committed in the sentence that was correcting someone else's criterion. The test asserts **the final status after following**, with the un-followed status still inspected and any 301 required to come with its cause. Superseded text follows. ~~Criterion wording corrected: `/home` does NOT return 200,
      it returns 301** → `/` → 200. `statusCodeEquals(200)` on `/home` **fails** unless redirects
      are followed. Assert the redirect chain, or assert the final status after following it — and
      say which, in the test | ~~T-601~~ **—** — *blocker corrected: it needs an installed site, not six bundles* |

### Lane B — theme repository only

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-606 ✓ | H | Colour tokens as CSS custom properties: text, background, link, focus, border, status | Every token defined in exactly one file; the file is machine-readable enough for T-608 to parse without a bespoke grammar | T-507 |
| T-607 ✓ | H | Typography: one OFL face, self-hosted `woff2`, `OFL.txt` shipped beside it, licence-manifest line, type scale, `prefers-reduced-motion`, visible focus | The `OFL.txt` is present and its first line names the copyright holder; the manifest line names font, version, licence and source URL; **no** external font URL anywhere — `grep -rn "fonts.googleapis\|fonts.gstatic\|@import url(http" .` returns **0 lines** | **D-030** |
| T-608 ✓ | H | `tests/bin/contrast-check` + its dirty case: parse the token file, compute WCAG contrast for every declared foreground/background pair | Prints `N pairs checked — 0 below threshold` with `N` stated (4.5:1 body, 3:1 large text and non-text). A deliberately dimmed token makes it print `N pairs checked — 1 below threshold` and exit non-zero | T-606, T-508 |
| T-609 | H | Twig templates: page, node, field, **table**, form element, pager, menu | `twig-cs-fixer` **materialises** in the observed job list with `allow_failure: false` and passes; the table template emits `<th scope="col">`/`<th scope="row">` and a `<caption>`, asserted by the axe test's DOM checks | T-606 |
| T-610 | H | Base stylesheet built from the tokens; no generic CSS framework (D-014 rider f) | `stylelint` **materialises** in the job list with `allow_failure: false` and passes; the CSS contains **0** hard-coded colour literals outside the token file — asserted by an invariant with its dirty case | T-606, T-609 |
| T-611 | H | Extend T-509: axe over **every** page the theme renders in its fixtures, with the page count stated | The `nightwatch` log states **`P` pages scanned, `P >= 3`**, `R` axe rules run, **0** violations. `P` is written into the theme README's gate table in the same commit | T-609, T-610 |

---

## Wave 7 · The atomic swap

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-701 | · | **[andres]** Cut `agora_theme` **1.0.0 stable** on drupal.org | `curl -s "https://updates.drupal.org/release-history/agora_theme/current"` lists a `<version>1.0.0</version>` with `<status>published</status>`; `packages.drupal.org/files/packages/8/p2/drupal/agora_theme.json` returns 200 containing `1.0.0` | Wave 6 gate green, D-025 |
| T-702 | T | **The atomic commit.** Delete `extra.drupal-site-template` from `composer.json`; add `"drupal/agora_theme": "^1.0"` to `require`; replace `blank` with `agora_theme` in `recipe.yml` `install:`; set `system.theme` `default: 'agora_theme'`. One commit, nothing else in it | `grep -c "drupal-site-template" composer.json` → **0**; `grep -c "blank" recipe.yml` → **0**; `RequirementsTest` green (still **0** `*.info.yml`, constraint `^1.0` does not match the pin regex); `composer validate --strict` exit 0 | T-701 |
| T-703 | T | Discharge the wave-1 rider **by name**: revert the adjusted `no-boilerplate` check, add `extra.drupal-site-template` to the deny list, prove the deny list fires on the pre-swap file | The deny list grows from 8 to 9 entries; running it against the `HEAD~1` `composer.json` exits non-zero and names the key; running it against `HEAD` exits 0. The rider text in `DECISIONS.md` gains a closure note citing T-703 | T-702 |
| T-704 | T | Discharge **T-106** (deferred from unit 001 against D-014=B) and record its closure in the blockers table | The blockers table row for T-106 reads ✅ CLOSED with the T-702 commit sha; `tests/bin/cited-tasks-exist` exits 0 | T-702 |
| T-705 | T | Prove the swap on a **clean** install: rebuild `~/agora-smoke` from scratch (rebuild, not `git pull` — CLAUDE.md), apply the template, confirm `agora_theme` is the default theme and the front page renders | `drush config:get system.theme default` → `agora_theme`; front page HTTP **200**; the theme's stylesheet is in the served HTML; **and** the `Drupal CMS` job on drupalcode shows the same, `allow_failure: false` | T-702, T-511 |
| T-706 | T | `sbom-check` against a first-party dependency: `drupal/agora_theme` has no third-party security-coverage answer until 2026-09-03 | The invariant either reports the theme as covered (if the date has passed and coverage was granted) or **names it explicitly as a first-party exception with its eligibility date**, and its D-NNN line exists in `DECISIONS.md`. A silent pass is a **failure** | T-702 |

---

## Wave 8 · Closure, carried debt and the verdict

| # | Repo | Task | Success criterion | Blocked by |
|---|---|---|---|---|
| T-801 | T | **T-402** (carried): route assertions in the functional tests | Each key route asserted for status **and** for a rendered marker; the test prints the number of routes asserted; the number is quoted in the closure report | Wave 7 |
| T-802 | · | **T-208** (carried, redefined): the digest-pinned local gate container | The container definition names an image by **sha256 digest**, not a tag; `tests/bin/doctor` reports the digest; running the wave runners inside it reproduces the host's counts **exactly**, both numbers quoted | D-019 |
| T-803 | · | **T-317** (carried): toolchain floor measured on the macOS host | The floor (grep flavour, `-F` behaviour, line endings, `sha256sum` availability) recorded per host; **either** macOS is certified with its counts **or** it is recorded as NOT CERTIFIED with the named blocking difference. An unmeasured host is a **failure** | — |
| T-804 | H | **T-206(b)** (carried): Playwright visual regression on GitHub Actions, non-blocking per D-009 | Baselines committed; the workflow runs and reports **`S` screenshots compared**, `S` stated. Non-blocking, but **it may never lie** (D-020) | Wave 6 |
| T-805 | · | Both repositories' gate tables refreshed **from observation** in the same commit that makes them true, and CLAUDE.md's gate-A block updated | Each table names its pipeline id, ref, commit sha and the API path it was read from. No derived lists | T-801…T-804 |
| T-806 | · | `orquestador` audit and gate verdict for unit 002 | No open 🔴; every green quoted with its denominator (I-045); the **task count reported against the 38 budget** (raised from 34 by D-026); every 🟡 carries an owner and a target unit | T-805 |
| T-807 | · | Closure report and HOLD for [andres]'s gate B signature | The report states, for each of the two repositories: job list, test counts, assertion counts, pages scanned by axe, files scanned by each invariant. Any missing number is stated as missing, not omitted | T-806 |

---

## Carried debt, by existing number

| Existing task | Carried as | State |
|---|---|---|
| T-106 (theme approach, deferred unit 001) | T-704 | owned, wave 7 |
| T-402 (route assertions, deferred unit 001) | T-801 | owned, wave 8 |
| T-208 (gate container, redefined by D-019) | T-802 | owned, wave 8 |
| T-206(b) (axe + visual regression) | T-509/T-611 (axe) + T-804 (visual) | **split** — axe is wave 5/6 and blocking; visual is wave 8 and informative |
| T-317 (toolchain floor; macOS NOT CERTIFIED) | T-803 | owned, wave 8 |
| Wave-1 rider (`blank` + `extra` adjusted check) | T-703 | owned, wave 7 |
| `page.front` declared `/home` vs landed `/page/1` | T-605 | owned, wave 6 |
| Rendering a Dataset's CSV distribution as an accessible `<table>` (D-026 calls that table the source of truth the charts read) | **unit 003** | owned, ruled 2026-08-25 |
| Menu placement: nothing links to the six per-bundle routes | **T-603** | owned, ruled 2026-08-25 |

~~**Count: 34 tasks. Budget 38. Headroom 4.**~~

🟡 **CORRECTED 2026-08-24, and the correction is itself the finding.** Counted on disk rather
than by hand — `grep -c '^| T-[5-8][0-9][0-9] ' specs/002-base-and-theme/tasks.md`:
**wave 5 = 12 · wave 6 = 15 · wave 7 = 6 · wave 8 = 7 → 40 rows against a budget of 38,
headroom −2.**

The 30 was already six short **before** D-026 raised the ceiling, so the +4 rider was reasoned from
a number nobody had run. The scope gate's entire content is a number; a number computed by hand is
precisely the failure it was built to prevent, and it failed on its first use. Recorded here rather
than quietly re-based.

✅ **RESOLVED — [andres] chose **A** on 2026-08-24, signed as D-031: accept 40 against 38, record why, move nothing, raise nothing. The unit runs at **headroom −2**; any further task needs a signed rider naming what it displaces, and T-806 reports against **38** with the −2 stated rather than re-basing to 40.** Options as put to him, kept for the record — the `orquestador` deliberately gave no recommendation — it said so
itself: a recommendation here would be the failure mode the gate exists to catch. Options:
**(A)** correct the count and accept −2 with a rider naming why · **(B)** correct the count and move
two rows to a later unit · **(C)** correct the count and raise the ceiling again. (was 30/34/4; D-026 split the content model into five tasks and raised the budget by the same +4, so the reserve is unchanged — the increase was not spent on itself.)
```

---
