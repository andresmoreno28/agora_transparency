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
   `specs/000-project/DECISIONS.md` (what it brings, security coverage status). When in doubt,
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
   ~~Demo content stays bilingual ES/EN.~~ Supersedes D-005 on this point.
   **⚠️ The struck sentence is AMENDED by D-035, signed by [andres] 2026-08-26: demo content is
   ENGLISH-ONLY.** It is amended on evidence, not preference. A site template's shipped config
   **cannot ever receive an interface translation** — the locale system builds its project list
   from the module and theme extension lists alone, and `RequirementsTest` requires the package to
   contain **zero `*.info.yml`**, so a site template is structurally invisible to it. Measured, and
   the measurement is the memorable part: **`haven` has ~180 `.po` files per release on
   ftp.drupal.org, one of them carrying 168 real Spanish strings — and no installed `haven` site
   can fetch a single one.** Extraction works; delivery does not exist. This is a property of
   **recipes**, not of Ágora: Drupal CMS's own recipe-installed content model measures the same way.
   Both published site templates ship **0 translations across 213 content files**, and the
   marketplace criteria name no language requirement at all. Spanish is documented as a
   **post-install** path (`language` + `config_translation`, translated through the UI, which never
   consults the mechanism that is blocked), and `drupal/agora_theme` — which **does** have an
   `.info.yml` — is a real translation project whenever that is wanted. **This changes nothing
   about D-017: the repository, this file and all orchestration prose stay exactly as they were.**
7. **Commits: the Drupal convention, verified at source** (`/docs/develop/git/
   git-for-drupal-project-maintainers/the-format-of-the-git-commit-message`, updated 2026-04-24):
   *"As of November 2025, the Drupal Core project adopted Git commit messages formatted to comply
   with the Conventional Commits specification."* Format `{type}: #{issue ID} One line summary`,
   with `By:` trailers for co-contributors. Allowed types, exactly:
   **fix · feat · ci · docs · perf · refactor · test · task · revert** — note **`task`, not
   `chore`**. Three consequences for us: `chore:` becomes `task:` from 2026-08-23 (the 8 existing
   `chore:` commits stay — rule 8); `#{issue ID}` is mandatory once an issue exists for the work;
   and **the no-AI-trailer rule stands and is reinforced** — `By:` names humans. Role labels in
   docs: `[ejecutor]`, `[andres]` — never AI tool names.
8. **Append-only:** signed tasks in `tasks.md` are not renumbered. Signed ADRs/decisions are not
   edited: they are amended or a new one is created.
9. **Nothing broken moves forward:** a wave does not close without a complete gate A in green
   (exit 0 + real counts). ~~A red CI pipeline blocks everything else.~~
   **(Second sentence superseded by D-023(5), 2026-08-23.)** A pipeline's *status field* is not the
   gate and never was: pipeline `933270` reported `success` with a **failed** `cspell` job inside
   it. The gate is a statement about the **job list** — see the Gate A block below.
10. **Git hands:** you may commit and push to working branches if the dispatch delegates it.
    ~~Merges to the canonical branch, tags, releases and creation of the project on Drupal.org: human.~~
    **⚠️ AMENDED by [andres] 2026-08-27, and the amendment is narrower than the sentence it
    replaces.** The rule as written was read literally and produced a wrong refusal: a tag was
    prepared as a copy-paste command for the human instead of being cut. The correction, in
    [andres]'s own framing: **you push and you create the tags; the human creates the RELEASE on
    drupal.org, because that is the only step you cannot perform** - and he creates it from the
    data you hand him (description, short description, and whether it is a new feature, a bug fix
    or both). So: **commits, pushes and tags are yours. Releases on drupal.org, and creating the
    project there, are his.** Merges to the canonical branch are unchanged and stay with the human.
    ⚠️ The practical consequence is that **a release now needs release NOTES from you**, not a
    command for him to run - if you cut a tag and hand over nothing to paste into the form, you
    have moved the work rather than done it.

## Repository structure

**Process layer (exists and is stable):**
```
CLAUDE.md                  # this file
specs/
  000-project/            # meta unit: identity, decisions, architecture
    plan.md                # master plan (ALWAYS read when resuming)
    ROADMAP.md             # units 001-007 fleshed out (direction, not signed scope)
    DECISIONS.md          # append-only D-NNN record (verify the free no. ON DISK)
    IDIOMS.md              # project lessons/gotchas, append-only
  001-foundation/           # active unit
    DISPATCH-00.md · plan.md · tasks.md
    research/              # dated research (prior-is-not-disk)
  002-base-and-theme/ … 007-publication/  # 002 scaffolded 2026-08-24; 003-007 see ROADMAP.md
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
(see `specs/001-foundation/`).
Canonical layout: skill `drupal-site-template` + the research in `specs/001-foundation/research/`.

## Where the work happens — read this before touching anything

**There is exactly ONE working copy: the Windows checkout you are already in.**
There is no other. All editing, committing and pushing happens here.
Remotes: **`drupalcode`** (canonical, `git.drupalcode.org/project/agora_transparency`) and
**`github`** (read-only mirror, D-016). **There is deliberately no remote named `origin`**, so a
bare `git push` fails loudly instead of reaching the mirror by accident.

**`~/agora-smoke` inside WSL2 is a throwaway TEST RIG, never a second working copy.** It holds a
full Drupal plus a `source/` directory that is a **complete clone of this repository** — so it
*looks* like a working copy, which makes it more dangerous, not less. **Never edit it, never commit
from it, never open a session against it.** Refresh it with `git pull`, or delete and rebuild it —
rebuilding is the only thing that keeps a clean-install smoke actually clean.

**Docker and DDEV live INSIDE WSL2 Ubuntu on this machine**, not in Docker Desktop, whose distro is
stopped on purpose — the two conflict, and the WSL setup is the one DDEV itself recommends for
Windows. Reach it with `wsl.exe -e bash -lc '...'`. **Never report Docker as unavailable from the
Windows side alone**: that mistake cost a day (I-046). Run `tests/bin/doctor` — it probes WSL and
distinguishes the four states, and **its output beats any memory and beats this file**.

**Amended 2026-08-24 (unit 002, wave 5). “Exactly ONE working copy” was true while this project was
one repository. D-014=B made it two.** The paragraph above is not edited (rule 8); it is superseded
here, and its warning gets *sharper*, not softer, because there are now two real checkouts that
`~/agora-smoke` could be mistaken for.

| Directory | Repository | Remotes |
|---|---|---|
| the checkout you are already in | **`agora_transparency`** — the site template | `drupalcode`, `github`; **no `origin`** |
| its **sibling** `agora-theme/` | **`agora_theme`** — the theme (D-014) | `drupalcode` only |

Written as *sibling* rather than as an absolute path on purpose: this file is public on drupalcode
and on the mirror, and a home directory names a person. `tests/bin/doctor` prints both real paths
when you need them, and its output beats this file.

**The two are never both a session's working directory.** Every dispatch names **one absolute path
on its first line**; an order without one is refused, not guessed. The guard that actually works is
mechanical, not visual — the two names differ by a hyphen and a word, which is thin:
`tests/bin/identity-strings` runs inside `agora-invariants` in **both** repositories and fires when
a tree is not the repository it claims to be.

⚠️ **AMENDED 2026-08-26. Every count in the block below is now wrong, and one of its claims was
found FALSE by measurement — which is the whole reason `tests/bin/doctor` now answers this
question instead of a file.** Nothing below is edited (rule 8); it is superseded here.

**What was found, and it was a near-miss rather than a tidiness problem.** The block says both WSL
clones *"are now renamed to `drupalcode` with their push URL disabled"*. That was true of one of
them. **`~/agora-smoke/source` still carried a remote NAMED `origin` with a live push URL**, so a
bare `git push` there would have **succeeded** against the canonical repository — the exact
accident the deliberate absence of an `origin` remote in the real working copy exists to prevent.
Renamed, push disabled, and falsified: `git push` there now exits **128** while `git fetch` still
works.

**And the count moved in the safe direction, which is not the same as being right.** T-901 rebuilt
`~/agora-cms` from zero and the rebuild carries no `source/` directory, so the machine holds
**two** clones of the template — this working copy and `~/agora-smoke/source` — not three. There
are, however, **four** WSL rigs, and two of them are named nowhere in this file: `~/agora-export`,
which reaches the template through a **symlinked path repository into this working copy** and is
where unit 002's modelling actually happened, and `~/agora-theme-rig`.

⚠️ **THIS PARAGRAPH WILL GO STALE TOO. Do not read it; run the tool.**
**`bash tests/bin/doctor` group 6** walks the disk, prints both working copies and every
`~/agora-*` rig, says which rigs hold a full clone, and **warns when a clone's push URL is live** —
measured at the moment you read it, which a file cannot be. It was added on 2026-08-26 precisely
because CLAUDE.md:161-162 claimed doctor *"prints both real paths"* when it printed one, and two
separate readers found that same divergence the same day. The instruction *"its output beats this
file"* is only sound while doctor answers the question; now it does. The live-push warning was
falsified in both directions before it was trusted.

🔴 **THERE ARE THREE CLONES OF THE TEMPLATE ON THIS MACHINE, NOT ONE. Added 2026-08-24;
the third was unnamed until today.** Both WSL rigs hold a `source/` directory that is a **full
clone**: `~/agora-smoke/source` **and `~/agora-cms/source`**. The second is the more dangerous of
the two, because it is the directory an implementer sits beside for the whole of wave 6 — and
until today **both carried a remote named `origin`**, the exact name deliberately removed from the
real working copy so a bare `git push` fails loudly instead of reaching a remote by accident. In
those rigs it would have **succeeded**. Both are now renamed to `drupalcode` with their **push URL
disabled**; `git pull` still works, which is all a rig needs.
⚠️ **`tests/bin/identity-strings` does not protect against this**, and assuming it does is the
trap: it fires when a tree is **not** the repository it claims to be, and those trees **are** this
repository. Neither rig is ever edited or committed from — that rule has no mechanical backstop,
which is exactly why it is written here.

**The template must never contain the theme.** `RequirementsTest` requires **0 `*.info.yml` files**
in the package. A theme checkout nested anywhere under the template's tree is one `git add -A` away
from making that permanently false, and the failure mode is a marketplace reviewer finding it, not
a test. The sibling layout is the guard — that is why it is a sibling and not a subdirectory.

⚠️ **Chosen by [ejecutor] 2026-08-24 under standing delegation, and it is reversible.** A
`workspace/` parent holding both is the right shape at five repositories; at two it would mean
moving the working copy a session is running in, on the day wave 5 starts.
## Gate A (the drupalcode pipeline IS the gate — **job lists observed**, 2026-08-26, T-1204)

- `composer validate` + clean install.
- **Observed inventory — the site template.** Pipeline `937268`, ref `1.x`, commit `d7cf665`,
  read from `/api/v4/projects/project%2Fagora_transparency/pipelines/937268/jobs` on 2026-08-26 —
  not from the UI, not from the badge. **Re-read at T-1204; the list is unchanged since `936386`,
  and it is re-read rather than carried because a table nobody re-opened is a claim, not a
  measurement:**

  | job | stage | status | `allow_failure` |
  |---|---|---|---|
  | `Drupal CMS` | build | success | false |
  | `agora-invariants` | validate | success | false |
  | `composer` | build | success | false |
  | `composer-lint` | validate | success | false |
  | `cspell` | validate | success | false |
  | `eslint` | validate | success | false |
  | `phpcs` | validate | success | false |
  | `phpstan` | validate | success | false |
  | `phpunit` | test | success | false |

  **Nine jobs · all blocking · zero named exceptions.** This single table replaces the pair that
  stood here until 2026-08-26 — eight rows read from `934387` plus a ninth appended from `934533`,
  which was two observations of one list, split by the commit that produced each and increasingly
  hard to read as one thing. Nothing about the list changed in the merge; it is the same nine jobs,
  re-read whole. `stylelint` is absent because this package contains no CSS, and since the theme is
  a **separate project** (D-014) it may never run here at all — see the theme's own table below,
  where it does.

  **Derived lists are forbidden here: this table is replaced only by another observation, and it is
  a dated measurement, not a promise — the commit that changes the CI job list, the packaged file
  set or a gate's denominator is the commit that updates it.**

- **Observed inventory — the theme.** Pipeline `937289`, ref `1.x`, commit `813b32a`, read from
  `/api/v4/projects/project%2Fagora_theme/pipelines/937289/jobs` on 2026-08-26 (T-1204; the list
  is unchanged since `936390`). It is recorded in
  **this** file because `agora_theme` has no `CLAUDE.md` of its own: it is a theme, and its
  repository holds code, not the process layer.

  | job | stage | status | `allow_failure` |
  |---|---|---|---|
  | `agora-invariants` | validate | success | false |
  | `composer` | build | success | false |
  | `composer-lint` | validate | success | false |
  | `cspell` | validate | success | false |
  | `eslint` | validate | success | false |
  | `nightwatch` | test | success | false |
  | `phpcs` | validate | success | false |
  | `phpstan` | validate | success | false |
  | `stylelint` | validate | success | false |

  ⚠️ **Nine jobs here too — and it is NOT the same nine. Reading the count and skipping the names
  is the mistake this pair of tables is shaped to prevent.** Three jobs differ. The theme runs
  **`nightwatch`** (the axe gate) and **`stylelint`** (it has CSS), and it runs **no `phpunit`**
  and no `Drupal CMS`. So a per-repository floor of nine would be satisfied by two different sets,
  and *"both are at nine"* is not the same statement as *"both run what they need to run"*. The
  **denominators** are the part that carries meaning: on pipeline `937289`, commit `813b32a`,
  `nightwatch` printed `agora_theme axe gate: 6 pages scanned, 89-89 axe rules run per page,
  0 violations, heading-order reported on 6 of 6 pages` and `297 total assertions` — **re-read at
  T-1204 and identical to `936572`'s, which is itself the finding: the theme gained a template and
  a config directory in between and the accessibility surface did not move**, and that job
  has been **seen to fail** on a real missing `alt` (pipeline `935776`) — so its green is a
  measurement, not an absence.

  ⚠️ **The `heading-order reported on 6 of 6 pages` clause is the newest and the most useful.**
  A rule that did not run cannot have passed, and axe files a rule it never applied in a bucket
  that reads exactly like a pass (I-045). The suite now asserts **which bucket** the rule landed in
  per page and compares that count against the page count, so a rule that quietly stops applying
  fails the gate instead of disappearing into the green.

  ⚠️ **Those numbers moved THREE TIMES on 2026-08-26 and the movement is the point.**
  `4 · 128` → `5 · 167` → **`6 · 297`**, each in the commit that moved it. The sixth page is a node
  rendered through the entity path, and the jump from 167 to 297 is what the theme's **first
  `config/` directory** bought: until that commit the theme shipped **no block placements at all**,
  so no real page had a page title, an `<h1>`, a menu or status messages, and `menu`, `node` and
  `field.html.twig` were exercised by nothing. ⚠️ **And the fixtures had been carrying TWO `<h1>`
  elements each** — one hand-written, one from `SimplePageVariant`, because the fixture site had no
  `block` module — which nothing counted and axe does not flag. The assertion is now `h1 === 1`,
  not `>= 1`: zero and two are both defects and `>= 1` hides one of them. The earlier note read:
  the fifth page is the
  fixture that renders a real Views table, and the 39 new assertions are what now notices if
  `templates/views-view-table.html.twig` disappears. **Five of them were watched going red with
  the template moved aside** — the scroll wrapper and its `tabindex`, the themed table class, the
  themed caption class, and the two counts behind them — while core's own `<caption>`, `scope`
  and `headers` markers stayed green, because an override that disturbed those would be a
  regression the fix caused. ⚠️ The suite also asserts `rows >= 3` **before** any markup
  assertion: with an empty result set Views renders **no `<table>` at all** and axe reports no
  violations, truthfully and about nothing (I-062).

  ⚠️ **The theme has no `phpunit` job because it has no PHP tests, and that is a gap with an
  owner, not a property of themes.** It is named here rather than left to be inferred from a
  missing row.

- **The clean-install smoke runs on drupalcode and is observed, not merely declared.** The
  `Drupal CMS` job builds a fresh `drupal/cms` at `2.1.3`, installs **this package** into it
  through a Composer path repository, and runs Drupal CMS's own compatibility test against it —
  `OK (1 test, 1 assertion)` in job `11771967`. It is the clean-install smoke, and it runs **where
  a Drupal.org reviewer can re-run it**. The GitHub workflow keeps running and stays informative
  (D-020); what ended is its monopoly.

  ⚠️ **And the same log is where discharge condition 2 is visible.** It resolves from
  `packages.drupal.org`, and it prints `Locking drupal/agora_theme (1.0.0)` · `Downloading` ·
  `Installing`. **`1.0.0` is the release in which `templates/views-view-table.html.twig` does not
  exist at all** — so every install today, including the one those 1717 assertions ran against,
  gets the pre-fix theme. Publishing `1.0.1` is [andres]'s action and it is a **blocker on closing
  unit 002**, not a nicety. The log naming the resolved version is what makes this a fact rather
  than a suspicion.

- **The gate is the job list, never the pipeline's status field** (D-023(5), superseding
  non-negotiable rule 9's second sentence and D-006 on this point):
  > *Green when, and only when: the pipeline's **job list** is read from the API; `jobs >= 7`;
  > every job's `status == "success"`; and every job's `allow_failure == false` except those named
  > in a dated, owned exception in `.gitlab-ci.yml`. **`jobs: 0` is a failure, not "nothing to
  > report."** The pipeline's own status field is never the evidence.*

  The exception list in `.gitlab-ci.yml` is **empty** as of 2026-08-23 (T-226). A `success` pipeline
  containing a failed permissive job is a **failed** gate (I-043).

  **The floor is now `jobs >= 9`** (Amendment to D-020, 2026-08-24, T-511). D-023(5) is quoted above
  verbatim and its other three conditions are untouched; only the minimum count moves, and it moves
  because the job list moves. ⚠️ The quoted `jobs >= 7` is D-023(5) as first written, when seven
  jobs were observed; the amendment states the floor as rising *"from `jobs >= 8` to `jobs >= 9`"*,
  which matches the eight-job table above rather than the quote. **Read `9`.**

- ⚠️ **A green linter is a statement about the set it opened, and most do not print it.** Of the
  repository's **386** tracked files, `bash tests/bin/spellcheck` opens **347** and finds 0 issues
  (re-measured 2026-08-26 at T-806's audit; it read 183/178 for the few hours between the unit-003
  scaffolding commit, which added three files, and this one — the previous pair, 87 and 82, was 96
  files behind the tree — the
  config export landed in between, and a denominator that stale is the reason this line is
  re-measured rather than carried forward);
  the other five (`.eslintrc.json`, `.gitignore`, `LICENSE.txt`, `composer.json`, `screenshot.webp`)
  are skipped by the upstream `.cspell.json` defaults, not by omission. The CI job's own count runs
  two higher — it also opens two files the runner generates and this repository does not track. The
  36-versus-63 gap **T-222** opened is closed and has stayed closed across a change of denominator.
  `phpcs`, `phpstan` and `eslint` still print **no denominator at all**; quote result and scope
  together or not at all (I-045).

- ⚠️ **`phpcs` cannot be run on this host, and its one recurring failure has a one-line local
  check — use it before pushing PHP.** `Drupal.Files.LineLength` enforces the 80-character limit
  **on comment lines only**, and it counts **characters, not bytes**. A naive `awk 'length>80'`
  reports **27** lines where phpcs reports **2**: 25 are code, which the standard permits, and the
  other two are comments long in bytes but not characters (an `á`, a typographic quote). It has
  turned the gate red **twice**. The check that matches the rule:

  ```bash
  python3 -c "import io,sys
  for f in sys.argv[1:]:
      for i,l in enumerate(io.open(f,encoding='utf-8').read().splitlines(),1):
          if l.strip().startswith('//') and len(l)>80: print(f,i,len(l))" tests/src/**/*.php
  ```

- PHPUnit runs with **`--fail-on-empty-test-suite`** on every runner — **observed on drupalcode**,
  not inferred: the flag in the executed command line, `_PHPUNIT_CONCURRENT=0`, and
  `OK (16 tests, 1951 assertions)` from job
  `11787158`'s successor on pipeline `937268` (re-measured 2026-08-26 at T-1204; it read
  `16 tests, 1717 assertions` at T-805 and `3 tests, 38 assertions` before that). ⚠️ **The test
  count did not move and the assertion count rose by 234**, which is the shape to expect from a
  unit that populated a corpus: the same sixteen methods now walk 56 nodes, 8 rendered surfaces and
  two Canvas pages instead of a handful of fixtures. A suite that executed 0 tests is a **failed**
  gate (I-007, I-032). `tests/bin/no-blind-phpunit` enforces the flag in every versioned CI file.

- **`tests/bin/` runs on every push.** `agora-invariants` executes both gate runners — `gate-a-wave1.sh`
  (61 checks · 0 failures) and `gate-a-wave3.sh` (**43** checks · 0 failures), **13** invariants in total —
  not only when a human types them. Closed by **T-221** → **T-219** → **T-202**, all signed.
  ⚠️ **They moved again on 2026-08-26 — from 37 · 11 to 43 · 13** — and the arithmetic is stated
  rather than left to be inferred: T-906 adds one check to G3 (`no-secrets (binaries opened)`),
  T-904 adds G12 with two, T-905 adds G13 with three (exit, scanned, **deny terms** — a deny-list
  whose length is not printed is a deny-list somebody can shorten). The two new invariants are
  `media-licence` and `no-real-people`; the third change extends `no-secrets` rather than adding a
  script, because its `is_text()` guard skips every binary **by design** and EXIF is therefore
  invisible to it.
  The wave 3 numbers moved from 35 · 10 on 2026-08-24: `config-inventory` (T-601) is the eleventh,
  and it exists because a **kernel test cannot print a denominator** — PHPUnit turns any output a
  test emits, STDERR included, into an error. Measured locally on Windows, not on the runner.

- **Install smoke:** apply the template on a CLEAN Drupal CMS and verify key routes/render.
  ✅ **Observed running on drupalcode as the blocking `Drupal CMS` job** — see its own bullet
  above, with the pipeline, the job id and the line it printed. This paragraph said *"not yet
  observed running there"* until 2026-08-26, pointing at a placeholder row; the row is filled and
  the placeholder is gone. D-020's holding is unchanged and the GitHub workflow keeps running as an
  **informative** second opinion — it may fail without blocking, but it may never lie, and no wave
  closes on its green. What the amendment ended is GitHub's **monopoly** on running this smoke.
- Playwright: functional + visual regression of the demo pages.
  ⏸ **NOT RUNNING ANYWHERE, and not counted as coverage until it is.** T-804 is **deferred to unit
  003 with the mirror as its prerequisite**: D-009(d) puts visual regression on the GitHub mirror,
  and no GitHub repository exists for `agora_theme` yet (`gh repo view` → *"Could not resolve to a
  Repository"*). With no demo content until unit 003, the only pages a screenshot could capture are
  four synthetic fixtures. **Prerequisite: [andres] creates the mirror.**
- axe (a11y) with no violations on the demo pages. **Running in the theme, not here**, as the
  blocking `nightwatch` job — **5** pages, 89 rules per page, 0 violations; see the theme's table.
  ⚠️ This line said **4** until 2026-08-26: the same gate block carried the figure twice and only
  one copy was refreshed when the Views-table coverage landed. A number written down twice is a
  number that goes stale in one place first.
  ⚠️ *"on the demo pages"* is still aspirational: there are no demo pages until unit 003, so what
  it scans today is fixtures.
- `tests/bin/`: sbom-check (stable + coverage), no-unstable-deps, no-secrets, no-patches.
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
