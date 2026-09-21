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
   no exotic pins. ~~(Literal marketplace requirement.)~~ Midgard is OUT (it is in alpha);
   Config Guardian is IN (stable, with security coverage).
   ⚠️ **The struck parenthetical is AMENDED 2026-09-05. THE RULE IS NOT WEAKENED — only its
   provenance is corrected, and the correction makes the rule *ours* instead of somebody else's.**
   *"Literal marketplace requirement"* does not survive checking, and it was checked three ways:
   the starter kit's `GET-STARTED.md` lists **seven** ironclad rules and **none is about release
   stability** — it forbids **patching** and **pinning** by name, which is where the rest of this
   line genuinely comes from, and stops there; the RFC *"The architecture and philosophy of site
   templates"* contains **14 MUSTs** and the words `alpha`, `beta` and `stable` **zero times each**;
   and, decisively, **`haven` 1.0.3 — a published site template on the marketplace — ships
   `"drupal/webform": "^6.3.0-beta8"` in its `require`.** A requirement that a published package
   violates is not a requirement. **So the rule stands on its own merits**: being stricter than the
   ecosystem is a deliberate property of this product (D-004, and the SBOM discipline in rule 2
   below), and it is easier to defend as a choice we made than as a rule nobody wrote. ⚠️ **The
   practical consequence is the reason to bother:** *"the marketplace requires it"* would have lost
   an argument the first time anyone opened haven's `composer.json`, and every rule sharing the
   sentence would have lost credibility with it.
   🔴 **THE AMENDMENT ABOVE IS ITSELF CORRECTED, 2026-09-21, AND THE RULE ENDS UP STRONGER THAN
   EITHER VERSION.** It checked three sources — the starter kit's `GET-STARTED.md`, the site-template
   RFC, and `haven`'s shipped `composer.json` — and concluded the provenance did not survive.
   **It never checked the marketplace's own application page.** That page says it, verbatim, and it
   was re-read at source today (`new.drupal.org/site-template/apply`, HTTP 200, 60,970 bytes,
   fetched 2026-09-21): *"templates must work within the current versions of Drupal CMS and Drupal
   Canvas, and **cannot include non-stable releases (dev, alpha, beta, rc) or patches**."*
   ⚠️ **So the rule now stands on TWO grounds, not one, and neither replaces the other**: it is our
   own deliberate property (D-004, and rule 2's SBOM discipline), **and** it is written where a
   template is submitted. ⚠️ **The amendment's evidence survives and its inference does not.**
   `haven` 1.0.3 still ships `"drupal/webform": "^6.3.0-beta8"` — re-verified today — so a
   *published* package still violates the *written* requirement; what that falsifies is the claim
   that the requirement is enforced, not the claim that it exists.
   ⚠️ **Why this is 🔴 rather than a footnote:** the struck text could let a reader accept a beta
   dependency believing nobody requires otherwise. They do, on the page where the submission
   happens. ⚠️ **And the reusable lesson is about the shape of the original mistake, not its
   content:** *"it does not survive checking"* was concluded from **three sources that did not
   include the one place the claim would most obviously be written.** An enumeration that stops
   before the obvious source reads exactly like diligence.
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
- **Observed inventory — the site template.** Pipeline `969557`, ref `1.x`, commit `bc4baa4`,
  read from `/api/v4/projects/project%2Fagora_transparency/pipelines/969557/jobs` on 2026-09-21 —
  not from the UI, not from the badge. **Ten jobs, every one `success`, every one
  `allow_failure: false`**, on `drupal/cms (2.1.4)`.
  ⚠️ **Refreshed BECAUSE THE RULE AT THE FOOT OF THIS BLOCK DEMANDED IT, which is the first time
  that has happened rather than a human noticing.** Unit 005 wave 23 moved a gate's denominators —
  `phpunit` 21 → 22 tests and 2555 → 2596 assertions, `cspell` 417 → 423 files — so *"the commit
  that changes … a gate's denominator is the commit that updates it"* applied, and the trace-figures
  table below moves with it. **The ten job NAMES did not change**, which is exactly the staleness
  the warning below says nothing can see; what made this refresh happen was the denominator rule,
  not the list comparison. (The previous row stood at `969327` / `434a0e2` / 2026-09-20.)
  ⚠️ **This one IS the pipeline of the commit at the tip, and it is complete**: `watch-gate --wait`
  ran it to a verdict — *"GREEN for `bc4baa4` — every declared job ran, every job success, every job
  blocking"* — so it is a finished job list rather than one read mid-flight.
  ⚠️ **The figures those traces printed left this sentence on 2026-09-20 and moved into the
  second table below**, which is the one `tests/bin/claims-match-sources --online` re-reads from
  the jobs themselves. They were prose, and prose is where a stale figure hides: the whole of
  this bullet's warning below is about a staleness that nothing could see.
  ⚠️ **This row stood at `958595` / `49b4f2f` / 2026-09-12 until today — nine days and eight
  pipelines stale, in the same file whose theme row was refreshed three times in that period.**
  Nothing caught it, and the reason is worth more than the refresh:
  `tests/bin/claims-match-sources` compares this table's **job NAMES** against `watch-gate`'s
  declared list, and the names had not changed. **A stale observation of an unchanged list is
  invisible to a check that compares lists.** The block's own rule — *"the commit that changes the
  CI job list, the packaged file set or a gate's denominator is the commit that updates it"* —
  does not reach it either, because none of those three moved. ⚠️ **`Locking drupal/agora_theme
  (1.1.0)` is the figure to read twice**: the theme's `1.x` is 21 commits past that release and
  its `components/` directory exists in no published release, so what a clean install receives is
  not what this repository is developed against. **Re-read whole; the ten names are unchanged since
  `936386`, and it is re-read rather than carried because a table nobody re-opened is a claim, not
  a measurement.** ⚠️ **This is NOT the pipeline of the commit at the tip, and saying which one it
  is matters more than being one commit newer.** `958678`, on `31217a5`, was read first: eight
  jobs `success` and `phpunit` and `phpunit-pgsql` still `running`. **A job list read mid-pipeline
  is not the gate**, so the newest COMPLETE pipeline is what this table records and the tip's is
  named rather than quietly used to fill the eight rows that had finished. (The previous
  observation stood at pipeline `950203`, commit `7e09615`, 2026-09-06.)

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
  | `phpunit-pgsql` | test | success | false |

  **Trace figures — the site template.** Every row is a line the named pipeline's job actually
  printed, and `tests/bin/claims-match-sources --online` re-reads each one from that job's own
  `/-/jobs/<id>/raw`, anonymously, because that route needs no credential. **A figure that goes
  stale here now goes RED**, which is precisely what the nine-day staleness described above had
  nothing to make it do. Keep the rows to ONE canonical statement of each figure: the point is
  not to record them twice more carefully, it is to stop recording them twice.

  | job | the line its trace printed |
  |---|---|
  | `Drupal CMS` | `Locking drupal/agora_theme (1.1.0)` |
  | `Drupal CMS` | `OK (1 test, 1 assertion)` |
  | `cspell` | `Files checked: 423, Issues found: 0` |
  | `phpunit` | `OK (22 tests, 2596 assertions)` |
  | `phpunit-pgsql` | `OK (22 tests, 2596 assertions)` |
  | `phpunit-pgsql` | `_TARGET_DB_TYPE=pgsql - _TARGET_DB_VERSION=16` |

  ~~**Nine jobs · all blocking · zero named exceptions.**~~ **TEN as of 2026-08-27 — and the tenth
  row is the only one in this file that is a PREDICTION rather than an observation, which is why it
  says so in its own status cell.** `phpunit-pgsql` was added under D-040(2), and it is now
  **observed**: pipeline **`937841`**, ref `1.x`, commit **`47b2a99`**, job `11799334`, read from
  the API with the maintainer's token because **the `/trace` endpoint returns `401` to anonymous
  requests**
  ⚠️ **AMENDED 2026-09-20, and the amendment matters in both directions.** The sentence is true of
  the **API** endpoint and was verified again today: `/api/v4/projects/<id>/jobs/<id>/trace`
  answers **`401`, 30 bytes**, anonymously. **It is false as a statement about traces.** The web
  route `https://git.drupalcode.org/project/<name>/-/jobs/<id>/raw` serves the **whole log with
  no credential at all** — measured the same minute, **HTTP 200, 123,480 bytes**, carrying the
  axe summary line. So no verification here ever needed the maintainer's token, and **anyone,
  a marketplace reviewer included, can read every trace this project produces.** The same
  correction applies to the two other places this file repeats the `401` claim. — a limitation worth knowing before someone plans a verification around reading a log.

  ⚠️ **What the trace prints is the whole argument of D-040(2), on two consecutive lines:**

  ```
  database under test - _TARGET_DB_TYPE=$_TARGET_DB_TYPE - _TARGET_DB_VERSION=$_TARGET_DB_VERSION
  database under test - _TARGET_DB_TYPE=pgsql - _TARGET_DB_VERSION=16
  ```

  The first is the **unexpanded literal** that every phpunit-family job echoes — the string a
  log-grep criterion would have matched in the MySQL job, passing a PostgreSQL job that never
  touched PostgreSQL (I-102). The second is the real value, and the job **exits 1** if it is not
  `pgsql`. Service image `pgsql-16:production`; result `OK (16 tests, 1955 assertions)` — **the
  same 16 and the same 1955 as the MySQL job**, which is the point: the suite is now known to hold
  on both, rather than assumed to.

  ⚠️ **Why a tenth job exists at all, in one sentence, because it is the most useful thing on this
  page:** the nine-job list was green while the package shipped SQL that summed a text column on
  **every** database Drupal supports — PostgreSQL refusing it, MariaDB answering `0` with a warning,
  SQLite answering `0.0` in silence — and the 1951 assertions passed because nothing read that
  column. **A job list is only as good as the environments it runs in**, and this one ran MySQL and
  SQLite because those are the defaults, not because anyone chose them. See D-040(2).

  This single table replaces the pair that
  stood here until 2026-08-26 — eight rows read from `934387` plus a ninth appended from `934533`,
  which was two observations of one list, split by the commit that produced each and increasingly
  hard to read as one thing. Nothing about the list changed in the merge; it is the same nine jobs,
  re-read whole. `stylelint` is absent because this package contains no CSS, and since the theme is
  a **separate project** (D-014) it may never run here at all — see the theme's own table below,
  where it does.

  **Derived lists are forbidden here: this table is replaced only by another observation, and it is
  a dated measurement, not a promise — the commit that changes the CI job list, the packaged file
  set or a gate's denominator is the commit that updates it.**

- **Observed inventory — the theme.** Pipeline `970165`, ref `1.x`, commit `bf433c9`, read from
  `/api/v4/projects/project%2Fagora_theme/pipelines/970165/jobs` on 2026-09-21. **Ten jobs, every
  one `success`, every one `allow_failure: false`.** Figures read from the traces the same day:
  `nightwatch` job `12336498` — **10 pages scanned, 89-89 axe rules per page, 0 violations,
  heading-order reported on 10 of 10 pages, 774 total assertions**; `phpunit` job `12336499` —
  **`OK (203 tests, 959 assertions)`**.
  ✅ **THE FOURTH REFRESH IN TWENTY-FOUR HOURS, AND THE FIRST ONE THAT LEAVES A MECHANISM BEHIND.**
  It stood at `970030`/`a3037ae`, and at `969322`, `969068` and `968026` before that. ⚠️ **Both
  trace figures are UNCHANGED across that whole run of refreshes** — 10 pages, 89-89 rules, 0
  violations, 10 of 10, 774 assertions, and `OK (203 tests, 959 assertions)` — which is exactly
  what makes this row's staleness so hard to see by eye: **everything a reader would check is
  still true, and only the address is wrong.**
  ⚠️ **What was added is NOT a staleness check, and the distinction is the whole of T-0622's
  second half.** `tests/bin/claims-match-sources` now binds the pipeline id in the prose above to
  the id inside the API URL beside it — offline, on every push, for **both** observation tables.
  The id was written twice and compared against nothing; now a row has to be refreshed in two
  places or the gate fails. ⚠️ **A check asserting this is the NEWEST pipeline was considered and
  REFUSED**: it would go red every time somebody pushes to the other repository — a red no commit
  here can fix, which is the unfixable-red `D-023(5)` exists to refuse — and this file's own rule
  is that a table records a **named, complete** observation, so "newest" is not even defined while
  a pipeline is running. Staleness stays in `claims-match-sources`' NOT CHECKED list, named, with
  a reason that is true.
  🔴 **THIS IS THE THIRD REFRESH OF THIS ROW IN TWENTY-FOUR HOURS, AND THE STALENESS IS THE
  RECORD.** It stood at `969322`/`4f82307` when a drift detector in the theme found it; before
  that at `969068`, and before that at `968026`. **Nothing catches it and the reason is structural:
  `tests/bin/claims-match-sources` compares this table's job NAMES against `watch-gate`'s declared
  list, and the names do not change when the pipeline does.** ⚠️ **`tests/bin/packaged-claims`
  does not reach it either** — its subject is the *packaged, user-facing* set, and `CLAUDE.md` is
  `export-ignore`d and therefore not in it. **A stale observation of an unchanged list is invisible
  to every guard this project owns**, which is the one gap left after a week of closing them. ~~The
  fix is to bind the pipeline id itself, the way the theme's `claims-match-readme` binds one to a
  re-runnable URL; it has **no owner and no task row**.~~
  ✅ **CLOSED 2026-09-21 by T-0622, and the struck sentence named the fix correctly** — the binding
  it describes is the one that landed, modelled on `claims-match-readme` exactly as written. ⚠️
  **The rest of the paragraph is kept whole because it is still TRUE and is the best description
  in this file of what a binding does and does not buy:** `theme_jobs` still compares only job
  names, `packaged-claims` still cannot reach this file, and **a row that is stale in both the
  prose and the URL still passes.** What changed is that it now has to be stale in *both*, which
  is a narrower claim than "staleness is caught" and is deliberately the only one being made.
  🔴 **THIS ROW WAS FOUR PIPELINES STALE WHEN THE ONLINE CHECKER WAS BUILT, AND BUILDING THE
  CHECKER IS HOW THAT WAS FOUND.** It named `969068`/`8909f76` while **T-1205's own audit, signed
  in this repository earlier the same day, had already read `969322`/`4f82307`** — so the file
  and its own audit disagreed about the theme, in writing, for hours. The theme took **five
  pipelines on 2026-09-20**, one of them red (`969297`). ⚠️ **This is the same class as the site
  template's nine-day staleness above, found the same way and not by a gate**, which is the
  argument for reading a trace rather than trusting a table: the offline half compared job names,
  and the ten names have not changed through any of it.
  ⚠️ **The axe gate has moved twice more since T-1307's ninth page: NINE became TEN.** Every
  current figure is in this bullet's **trace-figures table**, once, where a machine re-reads it
  from the jobs themselves; it used to be restated here as well, and the restatement is gone
  rather than refreshed. (For scale: the axe totals were `677` over 9 pages, `576` over 8 and
  `489` over 7, and `phpunit` was `156 / 797` at `969068`.)
  ⚠️ **The rules-run range held at 89-89 across BOTH moves**, and that is the figure that makes
  the rise meaningful: a new page scanning fewer rules raises the assertion total while covering
  less. ⚠️ **The ninth page had to BE the site's front page, not merely look like one.** The hero
  band exists only where Drupal's front-page flag is set, and about thirty rules in the theme's
  stylesheet key on the class that flag produces — so a look-alike would have been scanned with
  the hero on the wrong ground and no section tints at all. **Contrast is a fact about the
  ground.** A side-effect worth knowing: core ships `/user/login` as the front page, so until
  T-1307 the **sign-in fixture was the front page** and carried a hero no real Ágora puts there;
  nothing asserted it, so nothing was red. Measured before and after: 89 rules, 0 violations, one
  `<h1>`, identical.
  ⚠️ **The axe gate moved for the first time since 2026-09-06: SEVEN pages became EIGHT**, and
  the figures below are the new ones. `nightwatch` job `12308877` prints `agora_theme axe gate:
  8 pages scanned, 89-89 axe rules run per page, 0 violations, heading-order reported on 8 of 8
  pages` and **`576 total assertions`** (was `489` over 7). ⚠️ **The rule count HELD at 89, and
  that is the figure to read rather than the page count**: a new page that scanned fewer rules
  would raise the total while covering less, which is the exact shape of a green that means less
  than it did. `phpunit` job `12308878` prints **`OK (156 tests, 797 assertions)`**, unchanged
  from `968062`, correct for a commit that adds no PHP test. (It stood at
  `968026`/`1db46dd` and `967950`/`7d7e791` earlier the same day and at `950770`/`71de28e` on
  2026-09-12; the ten names are unchanged, every job `success`, every `allow_failure` false.
  **Three observations in one day is not churn — the theme took three commits that day**, and a
  row carried forward would have described none of them.)
  ⚠️ **The two denominators moved in OPPOSITE ways across those three commits, and that is the
  useful part.** `phpunit` went `127 / 720` → `127 / 720` → **`OK (156 tests, 797 assertions)`**:
  unchanged across the two tooling commits, then +29 tests and +77 assertions on the one that
  added behaviour, which is exactly the shape to want and was predicted before it was read.
  `nightwatch` printed **7 pages … `489 total assertions`** on all three — correctly, because
  none of them added a scanned page. (**7 → 8 → 9** since; see the bullet above.)
  ✅ ~~⚠️ 🔴 **AND THAT LAST SENTENCE IS THE GAP, not a reassurance.**~~ **CLOSED the same day it
  was opened, by `e79c265`.** What it said, and it was right: the change that moved `phpunit`
  added a views empty-region message and an exposed-form button row to every register page, and
  **none of the seven axe-scanned fixtures contained an exposed form** — so neither surface was
  scanned by the accessibility gate anywhere. The theme's own stylesheet had recorded that
  absence since it was written; until 2026-09-19 it cost nothing.
  **An eighth fixture now carries a view with an exposed filter and is scanned in BOTH states**,
  which is the half that matters: unfiltered it reads 3 body rows, 1 exposed form, 2 labelled
  controls and **one** action; filtered it reads 0 tables, 1 empty region, a way out, **two**
  actions in the order `Search|Reset`, and the phrase *"been published yet"* **absent**. The
  accessibility gate now also watches for the return of the falsehood that opened this.
  ⚠️ **The existing register fixture was NOT given an exposed filter, and refusing the cheaper
  option is the point**: exposing one on it costs no new file, and it would have changed what
  seven already-green pages are a measurement OF. ⚠️ **The list
  MOVED on 2026-09-02: nine jobs became TEN**, and the tenth is `phpunit`. It is recorded in
  **this** file because `agora_theme` has no `CLAUDE.md` of its own: it is a theme, and its
  repository holds code, not the process layer. (Before `950770` the row stood at pipeline
  `950212`, commit `82201a6`, 2026-09-06; the ten names are unchanged. ⚠️ **This is the second
  "previous observation" clause in one bullet and they are three deep now** — the chain is
  `950212` → `950770` → `967950`, and it is left visible only because the reds between the first
  two are the point of the paragraph below. A fourth link should replace this clause, not join
  it.)
  ⚠️ **~~The theme's `1.x` has not moved since 2026-09-06~~ — IT MOVED ON 2026-09-19 (`7d7e791`),
  and the thirteen days it did not move are the whole reason this block was wrong about something
  else; see the correction in the `phpunit` paragraph below.** The superseded sentence, whose
  argument survives, said this row was six days newer than the one it replaced and **the SUBJECT
  was identical** — which is worth one line, because a refreshed
  date over an unchanged commit is the only case where re-reading looks like busywork and is not:
  three of the four pipelines between `950212` and `950770` **failed** (`950485`, `950521`,
  `950575`), and a reader who carried the earlier row forward would have carried a green that was
  true while three reds it never mentions went past.

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
  | `phpunit` | test | success | false |
  | `stylelint` | validate | success | false |

  **Trace figures — the theme.** Same mechanism as the site template's table above, same
  credential-free `/-/jobs/<id>/raw` route, read from pipeline `970165`'s own jobs.
  ⚠️ **`agora-invariants` read `35` until 2026-09-21 and the trace says `44`, which is nine
  checks of drift in a figure whose whole subject is a gate runner's own arithmetic.** It was found
  by REFRESHING the observation above and re-running `--online`, not by anything watching: the
  theme's runner grew when it gained its own drift checks, and this table had no way to know. That
  is the same shape as the pipeline id four paragraphs up — a number written here about a
  repository that moves on its own schedule — except that this one IS machine-checked, and the
  check found it the moment the row it hangs off was made current. **A trace figure is only as
  fresh as the pipeline its table names.** ⚠️ **The
  `gate-a-theme.sh` row is the first figure from the SIBLING repository that anything here can
  check at all** — it was listed as unreachable on the grounds that it "lives in the `agora_theme`
  repository", which is true of the script and false of the number its CI prints.

  | job | the line its trace printed |
  |---|---|
  | `agora-invariants` | `44 checks — 0 failures` |
  | `nightwatch` | `10 pages scanned, 89-89 axe rules run per page, 0 violations` |
  | `nightwatch` | `heading-order reported on 10 of 10 pages` |
  | `nightwatch` | `774 total assertions` |
  | `phpunit` | `OK (203 tests, 959 assertions)` |

  ⚠️ **A TABLE CAN NEVER NAME ITS OWN COMMIT'S PIPELINE, and that is structural rather than an
  oversight.** The pipeline is produced *by* the commit that carries the table, so every
  observation here names an EARLIER one and is at best one commit behind by construction. What
  `--online` proves is that the observation is TRUE OF THE PIPELINE IT NAMES — not that it is the
  newest. "Is it the newest?" is `tests/bin/watch-gate`'s question, and it is named in the NOT
  CHECKED list for exactly that reason.

  ⚠️ **`phpunit` IS NEW, 2026-09-02, AND NOBODY ADDED A JOB TO GET IT.** The upstream template
  materialises that job only when the package contains PHP test classes, and this theme had none -
  so for the whole of its life the pipeline was nine jobs green over 12 functions nothing executed,
  among them the ones computing the money figure and the bar widths. Writing
  `tests/src/Unit/ThemeHelpersTest.php` made the job appear: ~~`OK (18 tests, 63 assertions)`~~
  ~~`OK (102 tests, 571 assertions)`~~ **`OK (127 tests, 720 assertions)`, job `12303708` on
  pipeline `967950`, read 2026-09-19** (it was job `12024568` on `950770`, 2026-09-12, at the same
  127 and 720 — **the first time this figure has been re-read and found UNCHANGED**, on a commit
  that touched only `.gitlab-ci.yml` and `README.md`) — the two struck figures are that first day's and
  2026-09-06's. **The rate of change is the reason this row gets re-read rather than carried: a
  suite that goes 18 → 102 → 127 tests in ten days is one where a stale number is not slightly
  wrong, it is about a different suite.** ⚠️ **This job still prints `OK (…)` and the site
  template's two no longer do** — see the `--fail-on-empty-test-suite` bullet below, and the
  reason a criterion must never be written around the string `OK (`.
  ~~which is the same upstream template behaving differently in two repositories on the same
  day~~ **CORRECTED 2026-09-19, and the correction is worth more than the sentence it replaces.**
  The two repositories were never observed on the same day. The theme's `OK (…)` was printed on
  **2026-09-06** and the site template's two `Pass` traces on **2026-09-12** — one measurement
  from before the upstream flip and two from after it, *read* on the same day and silently
  compared as though they were simultaneous. The theme's `1.x` had not moved since the 6th, so it
  had **not executed one pipeline under the new default**. It was never immune; it was untested.
  ⚠️ **A green that predates a change is not evidence about the change**, and "behaving
  differently" was an explanation invented for a difference that was only a date. Falsified at
  the source rather than by argument: `include.drupalci.variables.yml` on `main` reads
  `_PHPUNIT_CONCURRENT` / `value: '1'` today, and neither repository pins a templates version, so
  both inherit it. Pinned in the theme on 2026-09-19 (`7d7e791`).
  **The absence of a job is not the absence of a need for one**, and nothing in a job list says
  which jobs are missing - that is the gap this row closes and the reason it is written down here
  rather than left to be noticed.

  ⚠️ ~~**Nine jobs here too — and it is NOT the same nine.**~~ **TEN as of 2026-09-02**, and the
  paragraph below is kept because its ARGUMENT survives the count changing: the two lists are still
  different, and the difference is still the point. The theme runs `nightwatch` and `stylelint`
  where the template runs `Drupal CMS` and `phpunit-pgsql`. Read the names, not the total.
  The superseded wording follows.

  **Nine jobs here too — and it is NOT the same nine. Reading the count and skipping the names
  is the mistake this pair of tables is shaped to prevent.** Three jobs differ. The theme runs
  **`nightwatch`** (the axe gate) and **`stylelint`** (it has CSS), and it runs **no `phpunit`**
  and no `Drupal CMS`. So a per-repository floor of nine would be satisfied by two different sets,
  and *"both are at nine"* is not the same statement as *"both run what they need to run"*. The
  **denominators** are the part that carries meaning, and they are in the theme's trace-figures
  table above — once, read by machine.
  🔴 ~~on pipeline `967950`, commit `7d7e791`, job `12319513`, read 2026-09-20, `nightwatch`
  printed …~~ **THAT ATTRIBUTION WAS WRONG AND IT IS THE BEST ARGUMENT ON THIS PAGE FOR NOT
  WRITING A FIGURE TWICE.** Job `12319513` belongs to pipeline **`969068`**, commit **`8909f76`**;
  `967950`/`7d7e791` is the 2026-09-19 observation. The paragraph had been HALF refreshed — the
  job id and the figures were moved forward, the pipeline and the commit were left behind — so it
  read as a dated measurement of a pipeline that never produced it. Nothing caught it, because
  nothing was comparing this copy to anything. The figures now live in the table and this
  paragraph keeps only the argument.
  ~~**the rule count held at 89 and the page count held at 7 while the
  assertions went 476 → 489**~~ **the rule count has now held at 89 across 7 → 8 → 9 → 10 pages
  and 489 → 576 → 677 → 774 assertions**, which is the shape to want on a commit that added no
  surface: the same pages asserted about more, not a page counted twice. ⚠️ **`6` and `362` stood
  in this line until 2026-09-06 and `476` until today**, read from pipeline `943602`, commit
  `c5b0f68`, then from `950212`; the line before those read `297` and was four commits stale.
  **297 → 362 → 476 → 489: four refreshes of one figure in seventeen days, and that is the
  evidence that a hand-maintained number in this block does not stay true — see `tests/bin/claims-match-sources`, which now fails the gate
  for the half of these figures that a machine in this repository can check.** It was refreshed by reading
  the job's trace, ~~which needs the maintainer's token because `/trace` answers `401` to anonymous
  requests~~ **which needs no credential at all: that is the second of the three places this file
  repeated the `401` claim, and it is corrected here rather than left for a reader to trip over.
  `/-/jobs/<id>/raw` with `-L` serves the whole log to anybody.** And that job has been **seen to fail** on a real missing `alt` (pipeline `935776`) — so
  its green is a measurement, not an absence.

  ⚠️ **The `heading-order reported on N of N pages` clause is the newest and the most useful.**
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

  ⚠️ ~~**The theme has no `phpunit` job because it has no PHP tests, and that is a gap with an
  owner, not a property of themes.** It is named here rather than left to be inferred from a
  missing row.~~ **CLOSED 2026-09-02 and the struck sentence had been false for four days when it
  was read again on 2026-09-06.** The gap it named is the one the paragraph above describes being
  filled; the theme runs `phpunit` and it is the tenth row of its table. Left visible rather than
  deleted, because the interesting part is that **two paragraphs of the same bullet disagreed with
  each other** — the newer one was appended and the older one was not struck, which is the same
  drift as a number written down twice.

- **The clean-install smoke runs on drupalcode and is observed, not merely declared.** The
  `Drupal CMS` job builds a fresh `drupal/cms` at ~~`2.1.3`~~ **`2.1.4`**, installs **this package**
  into it through a Composer path repository, and runs Drupal CMS's own compatibility test against
  it — `OK (1 test, 1 assertion)` in job `12155536` on pipeline `958595`, read 2026-09-12 (it was
  job `11771967` at `2.1.3`). It is the clean-install smoke, and it runs **where a Drupal.org
  reviewer can re-run it**. The GitHub workflow keeps running and stays informative (D-020); what
  ended is its monopoly.
  ⚠️ **This job DOES still print `OK (…)`**, and that is not a contradiction of the bullet below:
  it runs the phpunit binary directly on Drupal CMS's own test, so the `_PHPUNIT_CONCURRENT` flip
  does not reach it. **The same pipeline, on the same day, reports in two formats** — which is why
  the format is recorded per job rather than per repository.

  ⚠️ ~~**And the same log is where discharge condition 2 is visible.** It resolves from
  `packages.drupal.org`, and it prints `Locking drupal/agora_theme (1.0.0)` · `Downloading` ·
  `Installing`. **`1.0.0` is the release in which `templates/views-view-table.html.twig` does not
  exist at all** — so every install today, including the one those 1717 assertions ran against,
  gets the pre-fix theme. Publishing `1.0.1` is [andres]'s action and it is a **blocker on closing
  unit 002**, not a nicety.~~ **DISCHARGED at T-807 by `1.0.1`, and the resolved version has moved
  again since: job `12014951` on pipeline `950203`, 2026-09-06, prints `Locking
  drupal/agora_theme (1.1.0)` · `Installing drupal/agora_theme (1.1.0): Extracting archive` ·
  `OK (1 test, 1 assertion)`.** The struck text is kept because its last sentence is the reusable
  part and it survives the discharge: **the log naming the resolved version is what makes this a
  fact rather than a suspicion** — the site template pins nothing, so what a clean install actually
  receives is decided by `packages.drupal.org` at install time and is knowable only by reading it.

- **The gate is the job list, never the pipeline's status field** (D-023(5), superseding
  non-negotiable rule 9's second sentence and D-006 on this point):
  > *Green when, and only when: the pipeline's **job list** is read from the API; `jobs >= 7`;
  > every job's `status == "success"`; and every job's `allow_failure == false` except those named
  > in a dated, owned exception in `.gitlab-ci.yml`. **`jobs: 0` is a failure, not "nothing to
  > report."** The pipeline's own status field is never the evidence.*

  The exception list in `.gitlab-ci.yml` is **empty** as of 2026-08-23 (T-226). A `success` pipeline
  containing a failed permissive job is a **failed** gate (I-043).

  ~~**The floor is now `jobs >= 9`**~~ **THE FLOOR, canonical and stated once: `jobs >= 10`** — as
  of 2026-08-27 (D-040(2)). (Amendment to D-020, 2026-08-24, T-511.) D-023(5) is quoted above
  verbatim and its other three conditions are untouched; only the minimum count moves, and it moves
  because the job list moves. ⚠️ The quoted `jobs >= 7` is D-023(5) as first written, when seven
  jobs were observed; the amendment states the floor as rising *"from `jobs >= 8` to `jobs >= 9`"*,
  which matches the eight-job table above rather than the quote. ~~**Read `9`.**~~ **Read `10`.**

  ⚠️ **THAT SENTENCE IS THE ONLY LIVE FLOOR IN THIS FILE, AND `10` IS NOT WRITTEN DOWN AS A
  JUDGEMENT ANY MORE — IT IS DERIVED (T-0611, 2026-09-21).** `tests/bin/claims-match-sources`
  reads the number out of the marked sentence above and compares it against **`len()` of the
  shorter of the two job lists `tests/bin/watch-gate` declares**. Shorten a declared list by one
  and the floor moves under the claim, which fails; the job-list comparison fails in the same run.
  **The five other `jobs >= N` in this file are references, not floors**, and they are now named
  rather than inferred: the **`7`** inside the D-023(5) blockquote above, the struck **`9`**
  immediately before the marker, and the **`7`**, **`8`** and **`9`** quoted in the sentence after
  it. All five are frozen records under rule 8, so the guard asserts that the multiset of
  non-canonical mentions is still exactly **{7, 7, 8, 9, 9}** — an edit to any one of them is a
  rule-8 violation and fails the gate.
  ⚠️ **What this replaces, and why it was weak enough to be worth a task row:** until today the
  floor was read as the **maximum of all six mentions**. That is right for every direction the
  gate has ever moved except one — a set of six numbers lowered **together** passes, because the
  maximum of the lowered set is exactly what the prose now says. The old code named that gap in
  its own `NOT CHECKED` list, which is the honest thing to do with a hole and is not the same as
  closing it. It is closed.

- ⚠️ **A green linter is a statement about the set it opened, and most do not print it.**
  `bash tests/bin/spellcheck` offers **472** tracked files to cspell, which **checks 431** and
  finds 0 issues (re-measured 2026-09-21, and see the correction immediately below; it read
  **470/429** earlier the same day, when **this commit adds three files — `tests/bin/ported-copies`, `tests/bin/ported-drift` and `tests/bin/ported-from-theme.manifest`, T-0622 — and moved the denominator
  by exactly three** was written, which is the shape to want — and **467/426** before that, **456/415** on 2026-09-20, **455/414** on 2026-09-19, **451/410** on 2026-09-12, **448/407**
  on 2026-09-06, **426/387** on 2026-08-26 at T-806's audit, 183/178 before that and 87/82 before
  that).
  ⚠️ **The gap held at 41 across that move, and that is the half worth checking.** Three text
  files were added and cspell opened all three: 472 − 431 = 41, the same 37 binaries and 4 globs
  accounted for below. **Both figures were PREDICTED before the run and the run returned exactly
  470 and 429** on the day that sentence was written, which is the only way a denominator claim is
  worth anything.
  🔴 **AND IT WENT STALE BY TWO WITHIN HOURS, IN THE ONE BULLET OF THIS FILE WHOSE SUBJECT IS A
  DENOMINATOR GOING STALE. Corrected 2026-09-21 (T-0628's report), after being found by RE-RUNNING
  the tool rather than by any guard.** `74a2b60` added `LICENCE-MANIFEST.md` and
  `tests/bin/mirror-streak` and did not touch this bullet — against this block's own rule that
  *the commit that changes a gate's denominator is the commit that updates it.* Re-measured:
  **472 offered · 431 checked · 0 issues**, and `executable-bit` moved with it to **472 examined ·
  37 scripts**, +1 script being `mirror-streak` itself. ⚠️ **The two new files explain both moves
  exactly, which is the check worth doing rather than just refreshing the number**: one script and
  one markdown file, both text, both opened, so the gap could not move and did not. **A gap that
  held while both sides rose is evidence; a refreshed pair of numbers on its own is not.**
  ⚠️ **This figure is written FIVE times in this file** — here, in the arithmetic below, in the
  frozen prediction above, in the `Files checked:` experiment further down and in the
  `executable-bit` paragraph — **and that is the finding, not the two stale digits.** The
  paragraph two below says in as many words that the remedy is *"stop making the second copy"*,
  and this bullet has four of them. Nothing machine-checks any of it: `claims-match-sources`
  names spellcheck's LOCAL denominators in its NOT CHECKED list, with a reason that is true. **A denominator that rises while the gap also rises would mean a new file
  went unopened**, which reads exactly like a clean measurement and is not one. The CI job's own count runs two higher — it also opens two files the runner
  generates and this repository does not track. The 36-versus-63 gap **T-222** opened is closed and
  has stayed closed across four changes of denominator.

  ✅ **The 41 files not opened are now accounted for EXACTLY, by set difference rather than by
  enumeration, and the shortfall this paragraph used to admit is closed.** **37** are binaries
  cspell does not read — **34 PDF, 2 WebP and 1 PNG** — and **4** are matched by the upstream
  ignore globs (`.eslintrc.json` by `**/.*.json`, `.gitignore` by `.*ignore`, `LICENSE.txt` and
  `composer.json` by name). **37 + 4 = 41**, and 472 − 41 = **431**, the number cspell prints.
  ⚠️ **This line read `456 − 41 = 415` until 2026-09-21 — stale by SIXTEEN, three denominators
  behind the bullet above it**, which is the same defect its own next paragraph describes and is
  why that paragraph is no longer the worst instance in this file.
  ⚠️ **This arithmetic said `451 − 41 = 410` until 2026-09-20 while the bullet four paragraphs
  above already said 455/414 — the same figure written twice and refreshed in one copy only**,
  which is the defect that bullet is itself about, committed inside it. Both copies moved in the
  commit that moved the denominator this time.
  ⚠️ **The missing file was `logo.png`, which landed on 2026-09-02 — so the shortfall was not an
  unknown, it was an ARRIVAL nobody subtracted.** The accounting was written when the binaries
  were 36, the file was added a month later, and the sentence saying *"the remaining one is not
  identified"* survived two re-measurements because it read like honesty rather than like an
  unfinished subtraction.
  ⚠️ **How it was closed is the reusable part: not by reasoning about globs, but by running cspell
  over the two sets and reading its own counts.** The 41 predicted-skipped files, handed to cspell
  as a file list, produce **no `Files checked` line at all**; their complement produces
  `Files checked: 415` (it printed `410` when the experiment was first run — the skipped set is
  what held at 41, not the complement). **Falsified in both directions, per file**: `logo.png`, `screenshot.webp`,
  `.eslintrc.json`, `.gitignore`, `LICENSE.txt` and `composer.json` are each opened **0** times
  alone, while `.claude/settings.json`, `.gitattributes` and `README.md` are each opened **1** —
  the first of those being the one a glob-reading argument gets wrong, because `**/.*.json` matches
  a dot-prefixed FILENAME and not a file inside a dot-prefixed directory.
  ~~the other five ... are skipped by the upstream `.cspell.json` defaults, not by omission~~ —
  the superseded sentence named **five** files against a gap that was **39** at the time, which is
  the failure mode worth remembering: an enumeration that does not add up to its own denominator
  read as an explanation across three re-measurements because nobody did the subtraction.
  `phpcs`, `phpstan` and `eslint` still print **no denominator at all** unless asked — `-p` for
  phpcs, `--debug` for phpstan, counting result objects for eslint; quote result and scope together
  or not at all (I-045).

- 🔴 **`phpcs` RUNS ON THIS HOST. The struck sentence below was false, it was written down as
  fact, it was repeated into a dispatch, and it cost a working day and real money.** Measured
  2026-09-06: the tracked tree, copied with its line endings normalised into a rig outside the
  docroot and linted with the pipeline's own `gitlab_templates assets/phpcs.xml.dist`, gives
  **321 files offered to phpcs, 0 errors, 0 warnings** — the same verdict as job `12014953` on
  pipeline `950203`, which is what makes it a replica rather than a second opinion.

  ⚠️ **Two details are the whole difference between a useful local run and a misleading one, and
  both were found by getting them wrong first.** (1) **phpcs reads its ruleset from the working
  directory, not from the path it is scanning.** Run from the rig root instead of from inside the
  copy, the same tree reports **2993 errors** — `PEAR.WhiteSpace.ScopeIndent` and friends, because
  the `Drupal` standard was never loaded. A local check that is NOISIER than the gate is more
  dangerous than one that is quieter: noise gets dismissed as pre-existing, and that is exactly how
  three real `stylelint` findings crossed a green local check in the theme. (2) The copy must have
  **CR stripped** — phpcs counts the carriage return as a character — and must land **outside the
  docroot**, because Drupal's extension discovery walks the docroot and a second copy wins the race
  and gets served.

  **`stylelint` and `eslint` run here too**, and the same day's measurements are why this whole
  bullet is rewritten rather than edited: `tests/bin/preflight` **in the theme repository** records
  `phpcs` — *"EIGHT real errors sat undetected on this branch; a ninth turned pipeline 949480
  red"*; `stylelint` — *"THREE real findings turned pipeline 949481 red"*; `eslint` — *"never
  attempted at all. It runs, and it caught two formatting errors in freshly written code before
  they reached CI."* **A tool nobody tried is indistinguishable from a tool that cannot run, and
  both look exactly like a tool with nothing to report.** That script runs **7 of the theme's 10
  jobs** with the pipeline's own fetched configuration and prints the 3 it does not cover
  (`composer`, `nightwatch`, `phpunit`) by name. ~~**There is no equivalent in this repository yet**
  — the run above was assembled by hand — and that is a gap with no owner.~~
  ⚠️ **CLOSED THE SAME DAY THIS SENTENCE WAS WRITTEN, and it stood false for six days.**
  `tests/bin/preflight` exists here — 48,569 bytes, committed `100755` in `434e289` on 2026-09-06,
  under T-1502 — and its own header opens with the measurement that this bullet's first paragraph
  reports. **The gap was given no owner and closed itself within hours; the sentence naming it was
  never re-read.** That is the same defect as a stale number, one level up: *"there is no X yet"*
  is a claim with an expiry date and no mechanism watches it. It runs this pipeline's lint jobs
  against a rig with the pipeline's own fetched configuration and names the ones it does not cover;
  it is not wired into either gate runner, because it needs Docker.

  ⚠️ **The struck claim below is kept whole, and its second half is still correct and still worth
  using.** The 80-character check is a good pre-flight when a rig is not running, which is the
  situation the false half of the sentence was invented to cover:

  ~~⚠️ **`phpcs` cannot be run on this host, and its one recurring failure has a one-line local
  check — use it before pushing PHP.**~~ `Drupal.Files.LineLength` enforces the 80-character limit
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

- ✅ ~~🔴~~ ~~PHPUnit runs with **`--fail-on-empty-test-suite`** on every runner~~ **IT DID NOT,
  BETWEEN 2026-09-08 AND 2026-09-13, AND NOBODY HERE CHANGED ANYTHING. FIXED AND VERIFIED; THE
  BULLET IS KEPT WHOLE AS THE RECORD OF HOW IT HAPPENED, WHICH IS THE PART THAT GENERALISES.**
  It runs with the flag again in both repositories — pinned in the site template on 2026-09-13
  (`fc83f63`, T-1701) and in `agora_theme` on 2026-09-19 (`7d7e791`). The present tense below is
  the tense of the finding, not of today. The flag is
  still in `.gitlab-ci.yml`, `tests/bin/no-blind-phpunit` still passes, and the guard is **inert**.
  Read from the API on 2026-09-12, jobs `12155543` (`phpunit`) and `12155544` (`phpunit-pgsql`) on
  pipeline `958595`, commit `49b4f2f` — the upstream template now says so **in its own words**, in
  the trace, in yellow:

<!-- cspell:disable -->
  ```
  WARNING: _PHPUNIT_EXTRA ('--fail-on-empty-test-suite') is set, but _PHPUNIT_CONCURRENT is '1'
  (run-tests.sh) and _RUNTESTS_EXTRA is blank. _PHPUNIT_EXTRA is intended for the 'phpunit'
  binary only (_PHPUNIT_CONCURRENT: 0).
  ```
<!-- cspell:enable -->

  ⚠️ **This repository's `.gitlab-ci.yml` predicted this failure in a comment and could not
  prevent it**, which is the part worth reading twice. Its own words at **lines 34-37**:
  *"`_PHPUNIT_CONCURRENT` is deliberately left unset/default ('0'): at '1' drupalci routes
  `_PHPUNIT_EXTRA` to run-tests.sh instead, where `--fail-on-empty-test-suite` is not a valid
  option and the guard silently stops existing"* — **the sentence names the exact failure that has
  now happened**, and it is right about the mechanism and wrong about the risk, because
  **T-214(c) protected the variable it sets and relied on an upstream DEFAULT for the one it does
  not.** A default is not a setting. The last observation with `_PHPUNIT_CONCURRENT=0` is jobs
  `12052123`/`12052124` on pipeline `952632`, commit `6559813`, 2026-09-08, where the flag is in
  the executed command line verbatim; every pipeline read on 2026-09-12 has it at `1`.

  ⚠️ **AND THE OUTPUT FORMAT CHANGED WITH IT, WHICH IS THE MORE USEFUL HALF.** `run-tests.sh`
  prints **no `OK (…)` line at all** and **no assertion total** — it prints one line per test:

  ```
  Pass         64.252s testAccessibilityOfTheInstalledPages       (phpunit, MySQL)
  Pass         50.885s testAccessibilityOfTheInstalledPages       (phpunit-pgsql, PostgreSQL)
  ```

  **Record the per-test line, not the total.** A `Pass` line names the method, so it says *which*
  test ran; a criterion written around the string `OK (` would not have survived this change, and
  would have gone from matching a true statement to matching nothing — reading, to anyone who did
  not open the trace, exactly like a suite that stopped reporting. **20 tests on each database,
  identical method for identical method**, across `AccessibilityTest` (1), `ValidationTest` (10),
  `InstallTest` (1), `ContentModelTest` (6), `RequirementsTest` (1) and `RolesAndPermissionsTest`
  (1) — and `_TARGET_DB_TYPE=pgsql - _TARGET_DB_VERSION=16` still printed expanded on the second,
  so the tenth job is still doing its job.

  ~~The last assertion total this gate will have for a while~~ **— it was for five days; the
  totals came back with the pin on 2026-09-13 —** is
  ~~`OK (18 tests, 2247 assertions)`~~ ~~`OK (20 tests, 2549 assertions)`~~
  **`OK (21 tests, 2555 assertions)`** on both jobs of pipeline `969201`, commit `bc10c00`,
  2026-09-20 (it read `20 / 2549` on pipeline `952632`, 2026-09-08)
  ⚠️ **This figure was stale for two days and NOTHING FAILED, which is the finding rather than the
  number.** It was one of the nine quantities `tests/bin/claims-match-sources` printed as NOT
  CHECKED, and its stated reason — *"only in a CI job trace; `/trace` answers 401 anonymously"* —
  **is false**: `https://git.drupalcode.org/project/<name>/-/jobs/<id>/raw` serves the whole log
  with no credential, **provided the redirect is followed** (`-L`; without it the 302 looks exactly
  like a failure). **Four of those nine were readable the whole time.**
  ✅ **CLOSED 2026-09-20 by T-1601**: the figure lives once, in the site template's trace-figures
  table, and `claims-match-sources --online` re-reads it from job `12324052`'s own log. The audit
  assigned this to unit 006 and it was pulled forward to unit 003 deliberately — **unit 006 still
  carries the accounting** (I-105). (it read `18 / 2247` on 2026-09-06, `16 / 2024` on 2026-09-01,
  `16 / 1951` at T-1204, `1717` at T-805 and `3 tests, 38 assertions` before that).
  A suite that executed 0 tests is a **failed** gate (I-007, I-032).
  🔴 **`tests/bin/no-blind-phpunit` enforces the flag in every versioned CI file and is GREEN
  while the flag does nothing** — it reads the repository, and the defect is in the environment
  the repository is read into. **That sentence is still true of that script and is the reusable
  lesson: an invariant that reads only its own repository cannot see a gate weakened from
  outside it.**
  ~~**This is an open gate weakening with no owner and no task row**; the fix is one variable
  (`_PHPUNIT_CONCURRENT: '0'`, or the extra-args variable the warning above names for
  `run-tests.sh`), and it is named here rather than applied, because `.gitlab-ci.yml` and
  `tests/bin/` were outside the scope of the change that found it.~~
  ✅ **CLOSED 2026-09-13 by T-1701 (`fc83f63`), and VERIFIED by reading the trace rather than the
  badge.** Pipeline `959227`, commit `fc83f63`: **10 jobs, every one `success`, every one
  `allow_failure: false`**; jobs `12163739` and `12163740` each print **`OK (20 tests, 2549
  assertions)`** — the figure predicted in T-1701's own row before the push, unchanged from
  `952632` because the commit added no test — with `_PHPUNIT_CONCURRENT=0`,
  `--fail-on-empty-test-suite` on the command line, upstream's warning **absent**, and **zero**
  `Pass` per-test lines. `agora_theme` carried the identical defect and was fixed on 2026-09-19
  (`7d7e791`).
  ✅ ~~⚠️ **What is NOT closed, named so it is not read as finished:**~~ **CLOSED HERE
  2026-09-19 by T-1903, and the struck text is kept because its last sentence is still true of
  the THEME.** What it said: the assertion that a
  gate-critical variable is pinned *by us* lives in `tests/bin/no-ci-allow-dev`, whose subject it
  is not. Its proper home is `no-blind-phpunit` — one of the five invariants **D-028** shares
  with `agora_theme`. Until that move lands the theme's pin is guarded by nothing but this
  paragraph.
  ⚠️ **What the move is, stated so “closed” is not read as more than it is.** The
  `PINNED_VARS` section now lives in `tests/bin/no-blind-phpunit` **here**, and its two
  denominators — `pinned required` and `correctly pinned` — are asserted by the wave-3 runner's
  **existing** `no-blind-phpunit` group, which is why that runner's check total moved and its
  invariant count did not. **`agora_theme` is NOT fixed by this.** A shared invariant reaches the
  theme by copy plus a manifest row (D-028 option B), which is a second dispatch; **until it
  lands, the theme's pin is guarded by nothing**, exactly as the struck sentence says. The two
  copies differed by **26 comment lines** before this change — one `diff -u` hunk, the
  `D-040(2)` sharp-edge block, executable code byte-identical — and that block is left in place
  so the copies end identical when the theme takes this file whole.
  🔴 **AND THE REASON IT HAD NOT LANDED WAS A CONSTRAINT NOBODY WROTE. Corrected 2026-09-19,
  same day, by reading D-028 instead of citing it.** This paragraph said until today that D-028
  *"forbids editing a shared file from one repository alone"*, and that sentence is **wider than
  the decision**. D-028's own text prices the opposite: option B's cost is *"one manifest to
  regenerate whenever a shared script legitimately changes"* — it **contemplates** legitimate
  changes. Its 2026-08-24 amendment holds that *"a copied invariant is never edited **until it
  passes**"*, which forbids adjusting a copy to make a red go away. **That is a ban on weakening,
  not a ban on editing.** ⚠️ **The invented constraint was load-bearing and it cost real work**:
  it is the stated reason T-1702 shipped a pin with no guard, and the stated reason this move had
  no task row for six days. It appears in five places, of which this is one; the copy in T-1701's
  signed row and the one in a pushed commit message stand uncorrected by rule 8 and are corrected
  by their successors instead. **A constraint that is repeated accurately for six days is
  indistinguishable from one that was ever checked.**

- **`tests/bin/` runs on every push.** `agora-invariants` executes both gate runners — `gate-a-wave1.sh`
  (95 checks · 0 failures) and `gate-a-wave3.sh` (**67** checks · 0 failures), **24** invariants in total —
  not only when a human types them. Closed by **T-221** → **T-219** → **T-202**, all signed.
  ⚠️ **`95 · 66 · 24` became `95 · 67 · 24` on 2026-09-21 (unit 006 wave 5, T-0632), and the one
  new check is in an EXISTING group — so `invariants` does not move, and wave 1's 95 is a measured
  non-change rather than an omission.** G7's `no-boilerplate (accounted)`.
  ⚠️ **The defect it closes is the one this whole block is about, committed inside an invariant
  instead of inside this file: `tests/bin/no-boilerplate` was FATAL AT ZERO AND SILENT AT PARTIAL.**
  It already refused `scanned: 0` (I-028). It did not refuse `scanned: half` — and when a scanning
  pass ended early, the files it never opened were **silently reclassified as binary blobs** and the
  run printed `findings: 0` at **exit 0**, with nothing in its summary saying a pass had stopped.
  Measured twice by two people: a complete run reads **728 scanned / 6 skipped as binary**, a
  truncated one **364 / 370**, and **370 − 6 = 364** is exactly the set that was never searched.
  ⚠️ **This is NOT an observed CI defect and must not be read as one**: the truncation was
  *induced*, by wrapping the script in `timeout 120`, and nothing wraps it in `timeout` in CI. What
  is general is the question it exposed — a child dying mid-scan for any reason, OOM or an evicted
  runner or a killed pipe, reported green.
  ⚠️ **AND ADDING THE NUMBERS UP WOULD NOT HAVE CAUGHT IT, which is the reusable half.** Under the
  same induced truncation the new reconciliation prints `accounted 734 of 734` and `skips named 350
  of 350` — **every identity balances**, because a path moved from `scanned` into a skip bucket is
  still accounted for. It is the **classification** that is false, not the sum. What fires is a
  per-pass **receipt** written where the work happens, and a bucket that says NOT EXTRACTED instead
  of `binary`. **This project's rule is that every check prints its denominator; this one did, and
  printing two numbers is not comparing them.**
  ⚠️ **`88 · 23` became `95 · 24` on 2026-09-21 (unit 006 wave 4), and one of the seven checks is NOT
  in the new group.** Wave 1's **G14** runs `tests/bin/mirror-streak` (6 checks), which reads the GitHub
  mirror's runs API **anonymously** and prints the red streak on every push — the quantity that was missing
  when nine consecutive reds stood for three weeks and only an inbox noticed. The seventh check is in
  **G8**: the packaged set must now contain `LICENCE-MANIFEST.md`.
  ⚠️ **And the stated reason a mirror check could not be an invariant was half FALSE, which is why it
  is one now.** `tests/bin/watch-gate`'s own comment holds that such a check *"would need the network
  and a GitHub token inside `agora-invariants`"*. Measured 2026-09-21 before the script was written:
  the mirror is a **public** repository and GitHub answers `/repos/<slug>`, `/actions/workflows` and
  `/actions/runs` with **HTTP 200 to an anonymous caller**, 60 requests an hour, and this one makes
  three. The network half stands and is answered as G13 answers it — exit 2 is a third state, tolerated,
  never a pass. **D-020 is untouched: a red mirror does not fail the gate.** What fails it is provenance
  and a streak past a 14-day cap with nothing in this repository saying anything about it, both of
  which a commit here can always fix.
  ⚠️ **`77 · 21` became `88 · 23` on 2026-09-21 (unit 006 wave 3), and the eleven checks arrive in
  TWO new groups rather than one.** Wave 1's **G12** runs `tests/bin/ported-copies` (6 checks) and
  **G13** runs `tests/bin/ported-drift` (5). They close the direction this repository had no record
  of at all: `agora_theme` has carried a manifest, a local detector and — since **D-063** — an
  API-reading drift check for the files it copied FROM here, while the files that travelled the
  OTHER way had **no manifest, no recorded provenance and no detection of any kind.**
  ⚠️ **The handed list of those files was two and the answer is three.** `tests/bin/executable-bit`
  and `tests/bin/preflight` were authored in the theme and ported here on 2026-09-06;
  `tests/bin/packaged-claims` took 63 lines from the theme's `tests/bin/claims-match-readme` on
  2026-09-21, **sixty-five minutes after that file was committed there.** It was found by comparing
  every script in both `tests/bin/` directories against every script in the other and reading the
  **band** of shared long lines — 15–24 is house style, 37–65 is shared substance — then settling
  direction with `git log --reverse` on both paths and never by reading the code. The control is
  that the same method puts *this* repository first for all eight files the theme's own manifest
  records. **A citation is not a provenance record**: `packaged-claims` names the theme's guard
  nine times in its own header and not one of those mentions records a commit, so none of them
  could ever have been checked.
  🔴 **G13 IS THE FIRST GROUP IN EITHER RUNNER THAT TOUCHES THE NETWORK**, and that is a change of
  character worth stating rather than discovering. It reads drupalcode's API **anonymously** — no
  token, because `/trace` answers `401` to anonymous requests and anything built on a CI log would
  have needed the maintainer's credential wired into CI. **NOT READ is a third state**: it exits
  **2**, prints `read: 0` beside `compared: 3`, and is tolerated by the gate for the same reason a
  raw delta is — no commit here can fix an unreachable drupalcode.
  ⚠️ **A DEFECT WAS FOUND IN THE SIBLING'S IMPLEMENTATION WHILE FALSIFYING THIS ONE, and it is
  still live over there.** The drift clock asks the API for commits `since` the recorded one, and
  `committed_date` comes back as `2026-09-21T12:44:08.000+02:00` — where **a raw `+` in a URL query
  string means a SPACE.** GitLab matches nothing and answers `[]`. Falsified at the API rather than
  argued: the same request answers **0** commits with the offset raw and **2** with it
  percent-encoded. **It fails in the SAFE direction, which is exactly why it survived** — an
  undated drift is reported as a finding, so the bug looks like strictness. Its real cost is that
  every genuinely recent drift would have been printed as a **finding** instead of as a number to
  read, which is the unfixable-red the whole design refuses, arriving through the back door. Fixed
  here; ~~`agora_theme`'s `tests/bin/upstream-drift` carries the same line and **nothing in this
  repository can fix it** — it needs a commit there.~~
  ✅ **THAT COMMIT LANDED THE SAME DAY: `a427375` in `agora_theme`, 2026-09-21.** The struck
  sentence was true for hours, and it is kept because it is the only place in this file where a
  defect is correctly named as **unfixable from here** — which is a real category, and the fix for
  it is a dispatch against the other repository rather than a task row in this one.
  ⚠️ **The theme's fix records the mechanism more precisely than the paragraph above does**, in a
  comment carrying four measured requests rather than two:

  ```
  since=2026-09-21T09:04:46.000+02:00    -> 3 commits
  since=2026-09-21T09:04:46.000%2B02:00  -> 8 commits
  since=2026-09-21T09:04:46.000Z         -> 3 commits  <- what a raw + MEANS
  since=2026-09-21T07:04:46.000Z         -> 8 commits  <- the instant meant
  ```

  **The third row is the one worth reading.** A discarded offset does not make the query
  meaningless — the server reads the same clock reading as **UTC**, two hours later than the
  instant intended, so the window is too SHORT and a drift age comes out too SMALL. *"Answers
  nothing"* was the right observation and the wrong mechanism: it answers a different question,
  confidently. ⚠️ **And the safe-direction reasoning above survives only because of which way the
  error points here.** An offset EAST of UTC shortens the window; a repository committing from a
  zone WEST of it would have the window silently LENGTHEN, and a real drift would then be reported
  as older than it is rather than as a finding. The bug looked like strictness by accident of
  geography.
  ⚠️ **`71 · 20` became `77 · 21` on 2026-09-21 (unit 006 wave 2), and this is the case where
  `invariants` DOES move**, which is worth one line beside the three notes below saying it did
  not: **T-0608** adds a new GROUP, wave 1's G11, running a new script — `tests/bin/packaged-claims`,
  six checks. The three notes below are about checks landing in groups that already existed; the
  rule they illustrate is the same one, seen from the other side. **The reason it exists is the
  uncomfortable half:** `claims-match-sources` has guarded this file for a fortnight, and this
  file is `export-ignore`d. Nothing guarded the prose that actually ships, and an audit of it that
  morning found **six wrong figures in three packaged files** — including the two check totals
  `README.md` quotes about these very runners, four and seventeen behind.
  ⚠️ **`68 · 61` became `71 · 66` on 2026-09-21 (wave 28) and `20` did NOT MOVE for the third
  time running**, which is the arithmetic behaving rather than an omission: all eight new checks
  land in EXISTING groups — three in wave 1's G8, three across wave 3's G3/G5/G6 and two in its
  G12 — and only a new GROUP moves `invariants`. **T-1915** partitions the three invariants that
  walk the filesystem into `git-tracked` and `untracked`, because their totals were a property of
  whose machine ran them: the same commit gave **472** files on a maintainer's desk and **462** in
  CI, and a denominator that changes with the desk is not a denominator. **T-1916** puts the
  package's own root inside `media-licence`'s closed world, which it never was.
  ⚠️ **`60` became `61` on 2026-09-21 (T-1912) and `20` did NOT MOVE, which is the arithmetic
  behaving rather than an omission**: the third check lands in the EXISTING G11 group, and only a
  new group moves `invariants`. ⚠️ **The defect it closes is the most expensive kind this file
  records, because it cost a red on a branch whose author had run the full local gate green.**
  `config/` objects are bounded in how many may carry a byte above `0x7F` (D-033 plus D-047), the
  assertion lives in a **PHPUnit kernel test**, and **neither runner executes PHPUnit** — both are
  shell invariants. `config-inventory` PRINTED `objects carrying a byte above 0x7F: 10` against a
  declared 8 and nothing compared them. The count is now compared, **by name and not by total**
  (two objects swapping places keep the total at 8 and mean something else), against the bound read
  out of the two `private const` lists in `tests/src/Kernel/ContentModelTest.php` — **read, never
  retyped**, because a bound written in two places is the defect one level up from the one being
  fixed. An unreadable or empty declared list is a FATAL, not a finding: a bound nobody can read is
  not a bound that happens to be met (I-028).
  ⚠️ **`51` and `17` stood here until 2026-09-20 and both moved in the same commit** (unit 005
  wave 23, T-0512/T-0513/T-0514/T-0515). Three new groups in the wave-3 runner, three checks each:
  **G16 `no-key-material`**, **G17 `no-experimental-modules`**, **G18 `no-skip-on-missing-key`**.
  A new group moves `checks` **and** `invariants`, and `claims-match-sources` binds the structural
  group count too, so all four figures — this sentence's two, the runner's `# GATE-CLAIM:` line and
  its `group 'GN'` declarations — are edited together or the gate fails. **That is the mechanism
  working: the arithmetic was not carried, it was re-read off `51 + 9 = 60` and `15 + 3 = 18`,
  and wave 1's 68 is a MEASURED NON-CHANGE rather than an omission — nothing in this wave touched
  that runner.** ⚠️ **G16 is the one whose existence is a measurement rather than an argument**:
  on a planted `config/key.key.openai.yml` carrying `key_value: ''`, `no-key-material` exits **1**
  with 3 findings and `no-secrets` exits **0**. An empty key value is not a secret and **is** a
  defect, and a length rule (I-018) is structurally incapable of seeing it — which is why it is a
  separate invariant rather than a third level of `no-secrets`.
  ⚠️ **THESE THREE NUMBERS ARE NOW MACHINE-CHECKED, AND THEY ARE THE REASON THE CHECKER EXISTS.**
  On 2026-09-06 this line read **61 · 48 · 15** while the runners printed **61 · 49** and carried
  **16** invariants between them: wave 13 had added a third check to G15 without adding an
  invariant, and this file was never told. `tests/bin/claims-match-sources` now reads the two
  figures out of this sentence and fails the gate when they disagree with the `# GATE-CLAIM:` line
  each runner carries in its own header — along with the two job tables above, which it compares
  against `watch-gate`'s declared job lists, and the `jobs >= N` floor, which it compares against
  the length of those lists. ~~**Seven comparisons**~~ ~~**EIGHT as of 2026-09-19**~~ ~~**TEN as of
  2026-09-20**~~ **ELEVEN as of 2026-09-21** — all offline, well under a second, which is why it is
  wired into wave 1 and not into the runner that takes 35 minutes. The eleventh is `floor_history`
  and it exists because the tenth was made stricter: T-0611 derives the floor from `len()` of a
  declared job list instead of reading the largest of six prose mentions, and the eleventh asserts
  that the other five mentions are still the frozen values rule 8 makes them. The eighth reads `.github/workflows/` against the workflow
  list `tests/bin/watch-gate` declares it reads, so a workflow appearing on the GitHub mirror that
  nothing has been told to watch fails the gate on the day it lands rather than on the day somebody
  notices. The ninth and tenth check that neither trace-figures table attributes a figure to a job
  its own inventory does not list.
  ⚠️ **AND IT NOW HAS AN ONLINE HALF, `--online`, which the gate never passes and which reads the
  CI job traces themselves — FIFTEEN more comparisons.** Per repository: the pipeline the table
  **names** is fetched and its ref and commit checked against the bullet's; its whole job list is
  compared against all FOUR columns of the table, stage and status included; and every row of the
  trace-figures table is looked for in that job's own log, by shape, so a mismatch prints the
  claimed value beside the measured one. ⚠️ **It reads the pipeline the table NAMES and never "the
  newest"** — reading the tip would compare today's prose against something nobody observed, and
  this file says in as many words that a job list read mid-pipeline is not the gate.
  ⚠️ **The absence of `--online` is a THIRD STATE and prints as one.** No flag, no network, no
  `curl`, a non-200, a pipeline still running, a job id that no longer exists: each prints its own
  sentence, and none of them may read as agreement. Asking for `--online` and reading **zero** of
  its items is a FAILURE, for the same reason `jobs: 0` is.
  ⚠️ **It covered about half of this block, and the half it did not cover was named for weeks with
  reasons that were FALSE.** *"Only in a CI job trace; `/trace` answers 401 anonymously"* was true
  of the API endpoint and false of traces, and *"needs Chrome and chromedriver to reproduce"*
  confused reproducing an axe run with reading what one printed. **Four of those nine entries were
  readable the whole time**, and the cost was measured rather than argued: on 2026-09-20 this file
  stated `OK (20 tests, 2549 assertions)` against a gate printing 21/2555, and the site template's
  inventory stood nine days and eight pipelines stale — both found by a human reading, neither by a
  gate. The `NOT CHECKED` list is down to **six** entries and every reason in it now says what
  specifically is out of reach. **A guard that silently covers half its subject reads exactly like
  one that covers all of it**, and the deleted-exclusion-list case is checked for the same reason
  the deny-list lengths in G7, G13 and G14 are: an empty list passes by construction (I-028). So is
  the trace-figures table itself: **0 rows would leave `--online` comparing nothing**, so 0 rows is
  a failure and not "no figures to check".
  ✅ **CLOSED 2026-09-21 by T-1911, after standing in this file as an open gap with NO OWNER
  for weeks.** Each runner now reads its own header at the END of its own run and compares the
  declaration against the total it has just PRINTED, so the five prose-against-prose comparisons
  have arithmetic at one end. ⚠️ **It cannot pass by not counting**: an unset or zero total
  agreeing with an unset or zero declaration is the I-028 shape this family of guards exists to
  refuse, so both sides must be positive integers BEFORE they are compared, and a missing,
  duplicated or unparseable declaration is a FAILURE and never a skip. ⚠️ **It is deliberately
  NOT a numbered check** — a check counted by the very total it verifies reads as circular — so it
  moved no `# GATE-CLAIM:` line and no figure in this file on the day it landed, and could be
  pushed on its own. **Watched failing five ways and passing once**, the block lifted verbatim out
  of the runner by `sed` so the harness cannot drift from the code it exercises: a declaration of
  999, no declaration at all, `checks=0`, two declarations, and a run whose own total was 0.
  ⚠️ **The 999 case failed TWICE and that is the cross-check**: `claims-match-sources` reported
  the same mutation independently, from the other side, which is what makes the two guards
  complementary rather than one of them redundant.
  ⚠️ **SUPERSEDED, and kept whole rather than deleted because its last sentence was right about
  the fix. The superseded wording follows.**
  ⚠️ **What it still does not prove**, stated so it is not mistaken for full cover: nothing yet
  asserts that a `# GATE-CLAIM:` line matches the total its own runner PRINTS. Five of the
  ~~seven~~ ~~**eight**~~ **ten** offline comparisons are therefore prose against prose — better than prose against nothing, because the
  declaration lives three lines from the arithmetic that derives it, but not a measurement. Closing
  it is one line in each runner's summary and it has no owner yet.
  ⚠️ **The seventeenth invariant is `executable-bit`, and it is the first number this file has
  moved BECAUSE the checker above demanded it** — which is the mechanism working on its first
  real customer rather than a claim about it. G10 in wave 1, three checks — exit, files examined,
  shebang scripts found. **64 → 67.** It reads the mode a shebang file is **committed** at out of
  the git index, never from the filesystem, because on this Windows checkout git does not track
  the bit and `[ -x ]` therefore answers TRUE for a file committed `100644` — while the Linux
  runner answers FALSE, takes the guard's else branch, and reports the invariant as missing. Both
  runners here carry that guard, **1 use in wave 1 and 14 in wave 3**, and nothing checked it.
  ⚠️ **It was not vacuous on its first run: 2 of the ~~28~~ 30 shebang files were committed
  `100644`** (`tests/bin/content-timestamps.py`, `tests/bin/generate-demo-media.py`), latent
  rather than breaking, because G15 reaches its script through `[ -r ]` and `python3 <path>`.
  Fixed in the same commit. The failure it prevents is measured in the sibling repository, not
  predicted — `agora_theme` pipeline `950124` was red for a full push cycle behind a green local
  gate. ⚠️ **`28` WAS NEVER A MEASUREMENT OF ANYTHING, and that is a different defect from drift.**
  The invariant printed `scripts: 30 with a shebang on line 1` on the day it was written and
  prints `examined: 472 tracked file(s)` · `scripts: 37` · `findings: 0` today, re-run 2026-09-21
  (it read `470` and `36` earlier the same day, `467` and `34` before that, `456` and `30` on
  2026-09-20, `455` before T-1310 added one config file, and `451` on 2026-09-12). **The +1 script
  is `tests/bin/mirror-streak`**, which is also one of the two files that moved spellcheck's
  denominator in the bullet above — the same arrival, counted by two invariants, and reconciling
  them is what made both corrections trustworthy rather than just current.
  ⚠️ **`456 · 30` stood in this line while the invariant printed `467 · 34`, and it was found by
  re-running the tool rather than by any guard** — eleven files and four scripts of drift, in the
  one paragraph of this file whose subject is a figure going stale. It is the third time this pair
  has been refreshed in eight days. **The scripts figure is the one that moved for a reason worth
  naming**: 34 → 36 is `tests/bin/ported-copies` and `tests/bin/ported-drift`, added by the commit
  that also wrote this line, so it is a prediction confirmed by a run and not a number carried.
  **A hand-written 28 stood beside a machine-printed 30 in the same paragraph, in the same commit,
  in three files, and nothing failed** — the denominator is 30, and the two files fixed that day
  were 2 of 30. `specs/003-demo-content/tasks.md` records the same correction against T-1501's own
  row, and `specs/000-project/DECISIONS.md` carries it inside a signed table, amended at the end
  of that file rather than edited in place (rule 8).
  ⚠️ **G10 is the one group that deliberately does NOT use the `[ -x ]` guard**: guarding the
  executable-bit check with the test it exists to police would let the defect disable its own
  detector, silently, in exactly the environment where the defect is real.

  ⚠️ **The sixteenth invariant is the first one whose subject is this file** rather than the
  package: G9 in wave 1, ~~three checks — exit, comparisons made, and exclusions named~~ **FOUR as
  of 2026-09-20 — exit, comparisons made, exclusions named, and trace figures claimed. 67 → 68.**
  The fourth is the I-028 guard on the new online half: emptying the two trace-figures tables
  would leave `--online` comparing nothing and saying so as if it were agreement. **61 → 64.**
  ⚠️ **They moved twice on 2026-08-27 — 43 · 13 to 46 · 14, then to 48 · 15** — and the second
  move is the interesting one: G15 runs `generate-demo-media.py`, the script D-042 wrote so the
  media manifest's 39 provenance rows could cite something that exists. **A generator nobody runs
  is a claim nobody can check**, which is the same defect one layer up from the one D-042 fixed.
  Its two checks are exit status and **files reproduced**, and the second is load-bearing: a broken
  enumerator prints "0 differing" exactly as a clean tree does (I-028). Falsified both ways — one
  byte appended to a shipped PDF gives exit 1, restored gives exit 0.

  ⚠️ **They moved earlier the same day — from 43 · 13 to 46 · 14** — and this line is edited in the
  same commit as the runner, because a number written in two places goes stale in one first.
  The fourteenth invariant is `no-varchar-aggregate` (D-040) and it adds G14's three checks: exit,
  **views scanned**, and **displays scanned**. The last two are not one question asked twice. The
  view count comes from `find`, so it reads 8 even if the parser inside the invariant read nothing;
  the display count comes from the parser, so it can only be positive if the files were opened and
  walked. *"8 views scanned, 0 findings"* from a parser that never ran is the exact shape of green
  this invariant exists to refuse, so the gate refuses it too.
  ⚠️ **It was seen RED before it was seen green, on 3 entries** — the two SUM fields on `block_4`
  and the one on `block_3` — and the fix took it to 0 findings over the same 8 views, 21 displays
  and 3 aggregating displays, with the field entries inspected falling 10 → 7. An invariant first
  seen green is a claim, not a measurement.
  ⚠️ **They moved on 2026-08-26 — from 37 · 11 to 43 · 13** — and the arithmetic is stated
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
  the placeholder is gone. D-020's holding is unchanged and ~~the GitHub workflow keeps running as an
  **informative** second opinion — it may fail without blocking, but it may never lie, and no wave
  closes on its green.~~ What the amendment ended is GitHub's **monopoly** on running this smoke.

  ⚠️ **AMENDED 2026-09-19. The struck clause was FALSE for three weeks, and nothing in this
  project was watching it — which is the worse half of the finding.** The mirror's
  `.github/workflows/phpunit.yml` failed on **nine consecutive runs** between 2026-08-27 and
  2026-09-19; the last green was `505c18a0`, run `33074134414`, 2026-08-27 12:54. Every push in
  between emailed the maintainer a red. **A check that has been red for three weeks is not
  "informative", it is noise that trains its reader to ignore it** — so the half of D-020 saying
  the mirror *may never lie* had quietly stopped holding, while the half saying it *may fail
  without blocking* went on being quoted as though it covered this.

  ⚠️ **The failure was UNREADABLE, and that is the part that generalises.** Run `35451608869`
  printed `Tests: 19, Assertions: 2362, Failures: 1, Deprecations: 138` above 138 deprecation
  reports and **not one word about the failure**: no failure marker in the `--testdox` list, no
  "There was 1 failure:" section, no assertion message anywhere in 408 KB of log. The cause is
  structural rather than a formatting accident: **a test that fails inside `setUp()` never emits
  PHPUnit's `Test\Prepared` event, so the TestDox collector never registers it** — the failing test
  is absent from the very list it failed in, which reads exactly like a test that never ran. The
  `Tests: 19` was wrong too; the JUnit log of the next run records **20**.

  ✅ **Closed by `--log-junit` plus a reporting step (T-1905), so the printer is no longer the only
  witness.** What it named on its first run: `AccessibilityTest::testAccessibilityOfTheInstalledPages`,
  `Behat\Mink\Exception\DriverException: Could not open connection: Failed to connect to localhost
  port 4444` — every frame inside `BrowserTestBase::setUp()` and `WebDriverTestBase::initMink()`,
  **not one inside the test file**. The test is right and this rig is wrong: it has no Selenium
  service and no `core/node_modules`, while the canonical pipeline has both and is green on the
  same test. It is now excluded **by name** through `EXCLUDED_TEST_CLASSES`, and the job
  reconciles the classes that ran against the `*Test.php` the package ships and goes red naming
  the class if that difference is not exactly the declared one (T-1906).

  ~~🔴 **What is STILL open, named so that "closed" is not read as more than it is: nothing in
  this repository watches the mirror's conclusion.** `tests/bin/watch-gate` reads drupalcode and
  only drupalcode — correctly, because that is the gate — and `tests/bin/claims-match-sources`
  prints eight exclusions by name, none of which is GitHub. **The mechanism that let nine reds
  pass unread is unchanged**: the mirror is watched by a human noticing an email, and this
  paragraph is the only thing in the repository that says so. The fix above makes the next red
  **legible**; it does not make it **noticed**.~~

  ✅ **CLOSED THE SAME DAY IT WAS WRITTEN, 2026-09-19 (T-1907, T-1908), and the struck paragraph
  is kept whole because its last sentence is the test the fix had to pass.** `tests/bin/watch-gate`
  now prints the mirror's state beside the drupalcode job list on **every** terminating path —
  including the *"no pipeline yet"* exit, which is what a push is normally followed by. It is
  reported from an `EXIT` trap for exactly that reason: a section printed only beside a finished
  verdict would be absent from the commonest invocation there is, which is how it would come to be
  unread again.

  ⚠️ **What it prints is the STREAK, and the absence of that number is the whole story.** A single
  conclusion for a single commit would not have caught this: each of the nine reds was a fresh
  failure on a fresh commit, and each was individually unremarkable. **Nobody ever had the number
  nine.** So it counts consecutive non-success completed runs on the branch, names the conclusions
  in the streak, names the last success and its age, and states how many runs it examined.
  Falsified against the **real API payload windowed to the moment the defect was found**, not
  against invented JSON: `RED STREAK: 9 consecutive non-success completed runs` ·
  `conclusions in the streak: failure x9` · `oldest in the streak: 2026-08-27T13:30:25Z (23 days
  ago)` · `last success: 505c18a run 33074134414 (23 days ago)`. When the page it read holds no
  success at all it reports a FLOOR — `>= N` — never a count it cannot justify.

  ⚠️ **It does NOT gate, and that is deliberate rather than timid.** D-020 makes drupalcode the
  gate and the mirror informative; promoting a red mirror to a failed gate here would be a decision
  taken by a tool instead of by a person. The exit status is computed from the drupalcode job list
  and from nothing else — **falsified in both directions**: a mirror the tool could not read at all
  still exits `0` over a green pipeline, and a green mirror still exits `1` over a pipeline that
  measured nothing.

  ⚠️ **Ten ways of not knowing, each with its own sentence, because the defect being closed is an
  absence that read as good news for three weeks.** No `gh`; `gh` unauthenticated; no `github`
  remote; a remote that does not parse; `--repo` naming a project this checkout is not; a 404; any
  other API failure; a non-JSON answer; an empty page; and no run for this commit. Every one prints
  `NOT READ` or `NO RUN` and says which, and **all ten were run**: unauthenticated prints *"gh is
  installed and NOT AUTHENTICATED … A third state, not a pass"*, and `gh` absent prints the URL to
  go and read by hand.

  ⚠️ **The offline half is in `tests/bin/claims-match-sources`, and it is the half that catches the
  NEXT one.** The conclusion itself needs the network, so it is named in the NOT CHECKED list
  pointing at the tool that reads it — that list had named eight quantities, **none of them
  GitHub**, so the mirror was missing from the covered half and from the named-uncovered half at
  the same time. What IS checkable offline is that every workflow on the mirror is one `watch-gate`
  has been told to read, and that is the eighth comparison: `.github/workflows/` against
  `MIRROR_WORKFLOWS`. It earns its place because the mirror is **scheduled to grow** — D-009(d) and
  T-804 put visual regression there — and a second workflow nobody reads would be this same defect
  one file over.
- Playwright: functional + visual regression of the demo pages.
  ⏸ **NOT RUNNING ANYWHERE, and not counted as coverage until it is.** T-804 is **deferred to unit
  003 with the mirror as its prerequisite**: D-009(d) puts visual regression on the GitHub mirror,
  and no GitHub repository exists for `agora_theme` yet (`gh repo view` → *"Could not resolve to a
  Repository"*). With no demo content until unit 003, the only pages a screenshot could capture are
  four synthetic fixtures. **Prerequisite: [andres] creates the mirror.**
- axe (a11y) with no violations on the demo pages. **Running in the theme, not here**, as the
  blocking `nightwatch` job — the page count, the rules-per-page range and the violation count are
  in the theme's **trace-figures table**, and this line no longer repeats them.
  ~~**9** pages, 89 rules per page, 0 violations; see the theme's table.~~
  ⚠️ This line said **4** until 2026-08-26 and **5** until 2026-09-06, while the table above said
  6 and then 7: the same gate block carries the figure twice and only one copy is ever refreshed.
  **A number written down twice is a number that goes stale in one place first — and this is the
  clearest instance of it in the file, having now drifted twice in the same direction.** It was
  fixed by hand each time; the durable fix was said to be impossible, because neither copy could be
  machine-checked — the page count *"exists only in a CI job trace"*, one of the exclusions
  `tests/bin/claims-match-sources` printed by name.
  ✅ **BOTH HALVES OF THAT ARE FIXED, 2026-09-20.** `claims-match-sources --online` reads the
  trace, so the figure IS machine-checkable; and the second copy is struck above, so there is
  nothing left to drift. **The lesson stated further down this bullet — "stop making the second
  copy" — is finally the thing that was done, rather than the thing that was recommended while
  both copies were refreshed by hand.**
  ⚠️ **Both copies were re-read against job `12024567` on 2026-09-12 and BOTH said 7**, and
  again against job `12303707` on 2026-09-19 — still 7, still 89, still `489 total assertions`,
  which is what a commit that adds no surface should do to them.
  ⚠️ **BOTH COPIES MOVED TO 8 on 2026-09-19 (job `12308877`), and to 9 on 2026-09-20 (job
  `12319513`), each time in the same commit** — twice in a row now, where every earlier refresh
  moved one copy and left the other stale.
  The 2026-09-12 note follows: which is
  the first time this pair has been checked and found to agree. **The assertion total is
  deliberately NOT repeated here** — it lives once, in the table above. The lesson of the drift is
  not "refresh both copies faithfully", it is **"stop making the second copy"**, and a figure that
  exists in one place cannot go stale in the other.
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
