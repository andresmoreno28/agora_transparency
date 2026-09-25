# Unit 005 · AI and governance — tasks

Scaffolded 2026-09-19. **No row here is signed.** Read `plan.md` and
`research/2026-09-19-ai-and-governance-state-of-the-art.md` first; two of that file's findings
close whole architectures.

**Budget: 16 rows if there is no module, 28 if there is** — the module ruling moves it by twelve,
which is why both are stated. Known rows 13 / 24, reserve 3 / 4. Per **D-044** the count is an
instrument and not a gate: necessary work goes in, and the accounting is written **in the same
commit**. Crossing the applicable ceiling costs a **D-031** rider naming what it displaces.

⚠️ **One number here can genuinely bind, and it is not the total: at most 4 rows in this unit may
rest on neither D-044 necessity nor a signature from [andres].** A total can be legitimately spent
by necessary work; this cannot, because exceeding it always means the same thing.

**Count with the command, never from prose:**

```
grep -cE '^\| T-05[0-9]{2} ' specs/005-ai-and-governance/tasks.md
```

⚠️ **Task ids are unit-scoped `T-05NN` and are NOT derived from the wave number.** Waves continue
the project's global numbering at 22. The decoupling is deliberate: unit 003's plan §4 records its
counting regex breaking twice precisely because ids and waves were coupled, and the regex above is
bounded at **both** ends.

Glyphs: `✓` done · `○` open · `⏸` blocked · `👤` needs [andres].
Repo (legend added 2026-09-25): `T` the site template (`agora_transparency`) · `H` the theme
(`agora_theme`) · `G` `drupal/config_guardian` (sibling `config-guardian`) · `A` `drupal/agora_core`
(sibling `agora-core`).

---

## Wave 22 · The measurements that decide the architecture

**No code. No file outside `specs/005-ai-and-governance/research/` is touched.** Its whole purpose
is to put numbers in front of the three rulings.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0501 ○ | T | Build the unit-005 rig: a clean Drupal CMS with Ágora applied, `search_api` + `search_api_db` + `ai` 1.4.9 present, **no provider and no key**. **Rebuild, never pull** (I-048) | `drush status` reports installed. `composer show drupal/ai` prints **1.4.9** and the version is quoted in the row. `drush php:eval` prints **0** `key` entities. **One `repositories` entry only** — `packages.drupal.org/8`, no path repository of any kind | — |
| T-0502 ○ | T | **R9 · Does `RagAction` work over `search_api_db`?** Index the six bundles, configure an assistant with `rag_action`, `output_mode: rendered`, ask one question with a stubbed provider | **Both outcomes quoted.** Either the answer renders and `getExtraData('drupal_entity_id')` is shown non-NULL with its value printed, **or** the exception is quoted verbatim with file and line. A run that prints neither is a failed criterion. Then run `output_mode: chunks` and show it failing **differently** — an identical failure means the probe measured nothing | T-0501 |
| T-0503 ○ | T | **R10 · The keyless citizen path.** Assistant configured, provider selected, key empty. Visit the surface as **anonymous** | HTTP status printed. The rendered text quoted. `dblog` rows for that request counted **and their severity printed**. ⚠️ A 200 carrying *"The website encountered an unexpected error"* is a **failed** criterion, not a pass — status alone proves nothing | T-0502 |
| T-0504 ○ | T | **R5/R11 · Where the key lands.** On a **throwaway** rig, apply with a fake key, then `drush config:export` | `key.key.*` listed, or its absence proven by a printed `find` denominator. If present, `key_provider` and `key_provider_settings` printed **with the value redacted**. Whether `ensureEasyEncryptionSetup` changed the provider is stated either way. ⚠️ **The rig is destroyed in the same row and its destruction is evidenced** | T-0501 |
| T-0505 ○ | T | **R3/R13 · The lifecycle audit.** Read the `lifecycle:` key of every `.info.yml` in the dependency closure; read the security-advisory policy at source | A table of module · `lifecycle` · verdict, with **N modules examined** printed and N > 0. The policy clause about experimental modules **quoted verbatim with its URL**, or its absence stated as an absence. ⚠️ *"I could not find it"* is an acceptable finding; an inferred answer is not | T-0501 |
| T-0506 ○ | T | **R12/R16 · Two network-and-time questions.** Does `ensureAmazeeAiAccess` call out during apply? Does Config Guardian's daily snapshot need cron? | The amazee call proven present or absent **by reading the source AND by an apply with egress blocked** — both, because either alone is inference. Config Guardian: snapshot count with cron disabled, then with cron run. **Two numbers or it is not measured** | T-0501 |
| T-0507 ○ | H | **R15/R17 · The two accessibility unknowns.** axe + keyboard over the deep-chat component if it is a candidate, and over the Config Guardian dashboard | Per surface: pages scanned, **rules run per page**, violations, and whether a streamed response is announced (live region present, its `aria-live` value printed). ⚠️ A rule that did not run cannot have passed (I-045) — **the rule count is part of the criterion, not commentary** | T-0501 |
| T-0508 ○ | T | **R14/R18 · Licence and precedent.** Read `deepchat/LICENSE` and its `package.json`; check whether `haven` and `byte` ship any AI configuration | The deep-chat licence named with its SPDX identifier. Per published template: **config objects examined** and AI-related ones found, both printed. **0 of N is a result**; *"they do not seem to"* is not | — |

---

## Wave 23 · The half that needs no key — two disjoint lanes

**Lane A files:** `composer.json`, `recipe.yml` (governance block only), `config/`, `DECISIONS.md`
**Lane B files:** `tests/bin/*`, `tests/bin/gate-a-wave3.sh`, `CLAUDE.md` (gate-claim numbers only)

The lanes do not share a file. That is the condition for running them at once, and it is stated
rather than assumed — this project lost work on 2026-09-19 to two writers in one checkout.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0509 ✓ | T | **Lane A** · `drupal/config_guardian ^1.0` into `require`, with its `DECISIONS.md` line **in the same commit** (rule 2) | **`tests/bin/sbom-check` exits 0: `11 projects queried - 11 with coverage - 0 findings`, the row reading `config_guardian 1.0.3 sec=1 core ^10.5 \|\| ^11 \|\| ^12 D-059`.** Release status **re-read at source on the day of the commit** (rule 1 is about today, and yesterday's figure was a day old): `updates.drupal.org/release-history/config_guardian/current` → 4 releases, newest `1.0.3`, newest STABLE `1.0.3`, `<security covered="1">`; the project contains no `-alpha`, `-beta`, `-rc` or `-dev` release at all. `composer validate --strict` exits **0**; wave 1's G5 reports `pinned versions 0`. Dependency closure read inside the published tarball rather than inferred: `dependencies: [drupal:config, drupal:file]` — **two core modules, zero contrib** — and `composer.json` `require` is `{"php": ">=8.1"}`, so the SBOM grows by **one** package and **zero** transitive packages. ⚠️ **The rule-2 check is WATCHED FAILING, and it needed two lines removed rather than one**: the D-059 heading AND its table row each carry a `D-NNN` token beside `drupal/config_guardian`, so deleting only the table row would have passed. With both gone: `with a D-NNN line: 10` (from 11), `findings: 1`, exit **1**, the finding naming `composer.json:8` and quoting CLAUDE.md rule 2. Restored → exit **0**, and the restored diff is byte-for-byte the 81 lines appended | T-0505 |
| T-0510 ✓ | T | **Lane A** · The `governance` area block in `recipe.yml` plus `config/config_guardian.settings.yml`, retention and exclusions each carrying a comment saying why | **`tests/bin/config-inventory` exits 0 at `133 config object(s), all top-level, all non-empty`, findings 0. The rise is STATED: 131 → 133, +2** — `config_guardian.settings.yml` and `user.role.agora_governance_auditor.yml`. **The `recipe.yml` diff is exactly three hunks, `git diff -U0` quoting them: `@@ -84 +84,5 @@`, `@@ -155 +159,32 @@`, `@@ -671 +706,15 @@`** — each replaces the single `# (empty in v1…)` line that sat directly under an `# -- area: governance` label, in `recipes:`, `install:` and `config.actions` respectively. No line outside a governance seam is touched. **Retention and exclusions each carry their reason**, and both moved off the module's defaults: `retention_days` 90 → **400** (a financial year plus five weeks; 90 cannot answer *"was the register public on the date the law required"*), `auto_snapshot_interval` `daily` → **weekly** and `max_snapshots` 50 → **60**, because `cleanupOldSnapshots()` applies an age cap and then a count cap and **the tighter wins** — at weekly, 400 days is ~57 snapshots, so the age cap is the operative one and the file says which. `exclude_patterns` keeps the module's two and adds **`key.key.*`**, which the same list also removes from what a **rollback** may write back (`RollbackEngineService::filterExcludedConfigs()`). ⚠️ **The object is shipped COMPLETE, and a partial one would have silently disabled the feature**: `RecipeRunner::processInstall()` wraps the recipe's config in a `RecipeOverrideConfigStorage` that REPLACES the module's `config/install` copy rather than merging with it, and `SettingsService::isAutoSnapshotEnabled()` has no `??` fallback, so an omitted key arrives NULL and reads as FALSE. Read in core 11.x at source, not inferred. ⚠️ **AND THE FIRST DRAFT OF THIS FILE TURNED THE GATE RED, which is the closed world in `tests/bin/identity-strings` doing its job on a file nobody had thought of**: two comment lines named the product, and every PACKAGED file that names the product must be declared as an identity file or as prose-only (I-024). `identity-strings` reported `packaged files naming the product: 10 · findings: 1` and exit 1. Fixed by **removing the name, not by widening the allow-list** — a config file shipped onto somebody else's site reads better as *"this package"* anyway, and declaring a new prose-only entry would have made every future commented config object cost a declaration. Now `9 · findings: 0`, exit 0. 🔴 **AND A SECOND RED FOLLOWED, IN CI, WITH BOTH LOCAL RUNNERS GREEN — which is the more instructive of the two.** Pipeline `969535` on `82335d1`: 8 jobs `success`, **`phpunit` and `phpunit-pgsql` both FAILED**, `Tests: 22, Assertions: 2580, Failures: 1`, on `ContentModelTest::testFinancialRegimeBundles` — *"Only the accented legal citations D-033 permits and the currency units D-047 configures may put a non-ASCII byte into `config/`"*. Both new config objects carried non-ASCII in their **COMMENTS**: 14 em dashes, 7 warning signs, 1 section sign. Fixed by making both files pure ASCII, **not** by adding them to the permitted list — the list is a signed D-033 bound, and the residue check behind it requires every non-ASCII byte to belong to a string the test can name, which a comment never will. ⚠️ **NEITHER LOCAL RUNNER COULD HAVE CAUGHT IT, and the number was on the page the whole time**: `config-inventory` PRINTS `objects carrying a byte above 0x7F: N` and does not assert it, while the assertion lives in a PHPUnit kernel test that **neither gate runner executes** — the runners are shell invariants only. It printed **10** against a declared 8 and nothing compared them. A printed denominator that nothing compares is a number nobody checks. Back to **8**, and CI re-run green | T-0509 |
| T-0511 ✓ | T | **Lane A** · `agora_governance_auditor` role: `view config snapshots` + `analyze config impact`, and **none** of the seven `restrict access: true` permissions | **`config/user.role.agora_governance_auditor.yml` ships, and `tests/bin/config-inventory` prints `agora_governance_auditor 2 permission(s), is_admin=false, 0 matching ^administer` with `roles inspected: 3  permissions granted across them: 41`** — 2 → 3 roles, 39 → 41 permissions, and both figures are ASSERTED in `RolesAndPermissionsTest` (a kernel test cannot print: PHPUnit turns any output into an error, pipeline 934619). The permission set is asserted by **equality in both directions** by that class's existing `ROLES` mechanism, so a permission that accretes fails as loudly as one that goes missing. A second method, `testGovernanceAuditorHoldsNoRestrictedPermission()`, asserts the role holds **0 of the seven restricted permissions, each named individually** — counted at source in `config_guardian.permissions.yml` inside the published 1.0.3: **11 permissions, 7 carrying `restrict access: true`** — and the list's own length is asserted (`assertCount(7, …)`, plus a distinctness assertion) BEFORE it is used, because a deny-list somebody shortens reports "0 held" exactly as a correct one does (I-028). ⚠️ **It also asserts the TWO UNRESTRICTED permissions declined on purpose** — `create config snapshots` (its holder can push the automatic history past both caps and destroy the evidence they exist to read) and `export config snapshots` (it returns the site's entire active configuration) — because least privilege is only real when what was left out is checked for. ⚠️ **ONE FILE OUTSIDE BOTH DECLARED LANE LISTS WAS TOUCHED AND IT IS THE MORE IMPORTANT HALF**: `ValidationTest::testRolesOnAnInstalledSite()` now names the auditor in its existence loop. A kernel test reads YAML on disk, which proves the file is right and **not** that Drupal ever created the entity; a `user.role.*` is a config ENTITY and a recipe that fails to bring one in fails in silence (I-086). That one line turns the silence into a red job. Folded into this row rather than given a row of its own | T-0510 |
| T-0512 ✓ | T | **Lane B** · `tests/bin/no-key-material` to plan §5.1 | **Exits 0 printing `scanned: 316 file(s)` · `patterns: 5 rule(s)` · `key entities found: 0` · `key_value assignments: 0` · `provider key configs: 0` · `findings: 0`.** Scope `config/**/*.yml` (133) + `content/**/*.yml` (182) + `recipe.yml` (1). ⚠️ **THE THREE DIRTY CASES WERE WATCHED FAILING, AND A FOURTH AND FIFTH WERE ADDED**: (1) a real-shaped `sk-proj-…` key → **exit 1, 4 findings**; (2) an **empty `key_value: ''`** → **exit 1, 3 findings**; (3) a `key.key.*` with **no `key_value` at all** → **exit 1, 1 finding** (the filename rule, which is why file names and file contents are two separate rules: a config object does not carry its own name); (4) a bare `key.key.<name>` reference added to `recipe.yml` → **exit 1, 1 finding** naming `recipe.yml:728`; (5) the comment exclusion falsified in BOTH directions — the same text as `# key_value: …` gives `1 line(s) skipped`, `findings: 0`, exit 0, and uncommented gives `findings: 1`, exit 1. ⚠️ **THE MEASUREMENT THAT JUSTIFIES A SEPARATE INVARIANT, rather than the argument for one**: over dirty case (2), the identical planted file, **`no-key-material` exits 1 with 3 findings and `tests/bin/no-secrets` exits 0**. `no-secrets` matches a value of real length (I-018) and is structurally incapable of seeing an empty one — which is not a secret and **is** a defect, because it means a key entity was exported from a rig and the next export on a keyed site fills it in. Registered as **G16** in `gate-a-wave3.sh` (3 checks: exit, files scanned, rule-list length), whose total moves **51 → 60** across the wave, with `# GATE-CLAIM: checks=60 invariants=18` in the same commit | T-0501 |
| T-0513 ✓ | T | **Lane B** · `tests/bin/no-experimental-modules` to plan §5.2 | **Exits 0 printing `modules examined: 8` · `lifecycle keys read: 10 (tree: 0, recorded audit: 10)` · `ledger rows: 10 (experimental 2, deprecated 7, obsolete 1)` · `findings: 0`, and the eight names in `install:` listed one per line.** ⚠️ **ALL FOUR lifecycle values are handled** — the scaffold said two, wave 22 found the third and fourth — and `absent == stable` is core's own rule, quoted from `InfoParserDynamic.php:110`, not an assumption. **Falsified in four directions**: (a) `ai_search` added to `install:` → **exit 1, 1 finding naming `lifecycle: experimental`** at `recipe.yml:191` with its source; (b) a fixture module experimental **only in a tree** and absent from the ledger → exit 1, the finding naming the `.info.yml`; (c) a tree whose `ai_search.info.yml` says `stable` while the audit says `experimental` → **exit 1 on the cross-check**, which is what stops the recorded audit rotting into prose; (d) a tree that AGREES, with a second fixture stable-by-absence → `lifecycle keys read: 12 (tree: 2, recorded audit: 10)`, findings 0, exit 0. ⚠️ **TODAY'S 0 IS A MEASUREMENT AND THE PROOF IS IN THE LEDGER**: `automatic_updates_extensions` (obsolete) and `eca_node_access` (experimental) are ledger rows by name, so the list is tested against names that really occur in Ágora's tree rather than against names invented to be absent. ⚠️ **DIVERGENCE, reported rather than papered over**: plan §5.2 says the invariant "resolves every module … to its `.info.yml`", and **this repository has none and must have none** — zero `*.info.yml` is a `RequirementsTest` requirement and `agora-invariants` declares `needs: []`. So `L` is the sum of a tree half (0 here, and the script says so **in words**, not by leaving a reader to infer it from a digit) and a recorded-audit half that always runs and is cross-checked against any tree it can reach | T-0505 |
| T-0514 ✓ | T | **Lane B** · `tests/bin/no-skip-on-missing-key` to plan §5.3 | **Exits 0 printing `scanned: 7 file(s)` · `excluded: 38 file(s)` · `skip constructs searched for: 7` · `credential terms: 13` · `skip constructs found: 0` · `findings: 0`.** ⚠️ **THE ZERO IS PRINTED, and on its own line followed by `(none - no test in this package disables itself for any reason)`** — a repository with no skips and a parser that found nothing produce byte-identical silence, so the number is on the page either way, and the GATE checks the **deny-list length** rather than `skip constructs found`, because that figure is legitimately 0 today and pinning it positive would invert the invariant. **Dirty case watched failing**: a `markTestSkipped` guarded by `getenv('AGORA_AI_API_KEY')` → **exit 1**, `skip constructs found: 1`, the finding naming `tests/src/Kernel/DirtyCaseTest.php:11`, the matched term `api[_ -]?key`, and the full condition window. ⚠️ **And watched STAYING GREEN on discrimination**, which is the half a blanket ban would have failed: the same `markTestSkipped` guarded by `PHP_INT_SIZE < 8` is PRINTED with its condition and gives `findings: 0`, exit 0. Plan §5.3 asked for a credential-conditioned rule, not a ban on skipping. **Exclusion stated and counted, not silent**: `tests/bin/` (invariants and gate runners, not tests — and this file necessarily contains every construct it searches for), `__pycache__/`, `*.pyc`. Registered as **G18** | T-0501 |
| T-0515 ✓ | T | **Lane B** · `CLAUDE.md`'s gate block updated: both `# GATE-CLAIM:` lines, the checks-and-invariants sentence, and the `jobs >= N` floor if the job list moved | **`bash tests/bin/claims-match-sources` exits 0: `comparisons: 10 (offline)`, `mismatches: 0`, `NOT CHECKED - 6 quantities` named one by one. `--online` exits 0: `read 15 of 15 items, NOT READ 0`, `mismatches: 0`.** The figures moved together and the arithmetic was **re-derived, not carried**: `51 + 9 = 60` checks, `15 + 3 = 18` invariants in the wave-3 runner, `2 + 18 = 20` across both. Edited in one commit: `CLAUDE.md`'s sentence (**68 · 60 · 20**, from 68 · 51 · 17), `gate-a-wave3.sh`'s `# GATE-CLAIM: checks=60 invariants=18`, and that runner's `group 'GN'` declarations, which `claims-match-sources` counts structurally. ⚠️ **THE BINDING IS WATCHED WORKING, not assumed**: reverting the `GATE-CLAIM` line alone to `checks=51 invariants=15` gives **3 MISMATCHES and exit 1** — `wave3_checks`, `invariants`, and `invariants_structural`, the last of which compares the declaration against the runner's own `group` lines rather than against more prose. Restored → exit 0. ⚠️ **THE JOB LIST DID NOT MOVE, and that is a MEASURED NON-CHANGE rather than an omission.** `--online` re-read both inventories from the API: the site template's pipeline `969327` (`434a0e2`) still runs **10 jobs**, every one `success`, every `allow_failure` false, and the theme's `969322` (`4f82307`) likewise — so `jobs >= 10` is untouched, and `template_job_rows` / `theme_job_rows` both compare **10 rows against 10 real jobs** and agree. **Wave 1 is untouched at 68 for the same reason**: nothing in this wave added a check to that runner, and it printed `68 checks - 0 failures` against its own unchanged `checks=68 invariants=2`. ⚠️ **RECONCILIATION, reported rather than silently absorbed**: the dispatch said the sentence *"currently reads 68 · 51 · 17"* and it did; but the two pipeline tables in `CLAUDE.md` on disk had already moved past the copy this session opened with — disk won, and no figure was carried from the prompt | T-0512, T-0513, T-0514 |

⚠️ **Reserve accounting, FIRST entry for unit 005, 2026-09-20 — ZERO rows.** Wave 23 closed
**T-0509 through T-0515** and added **no row**. Counted with this file's own command, never from
prose:

```
grep -cE '^\| T-05[0-9]{2} ' specs/005-ai-and-governance/tasks.md   ->  32
```

**32 rows, before wave 23 and after it.** The one number that can genuinely bind — *"at most 4
rows in this unit may rest on neither D-044 necessity nor a signature from [andres]"* — is
**untouched at 0 spent**, because nothing was added to spend it on.

🟡 **AND THE COUNT WAS ALREADY OVER BOTH CEILINGS BEFORE THIS WAVE STARTED. Nobody had said so,
and saying it is the whole point of writing the accounting while it is cheap.** The head of this
file states *"16 rows if there is no module, 28 if there is"* and *"known rows 13 / 24, reserve
3 / 4"*. The scaffolding turn that wrote those sentences wrote **32 rows in the same file**, so
unit 005 opened at **four over its generous ceiling and sixteen over its strict one** — before a
single ruling was taken and before one line of code was written.

⚠️ **This is the exact failure plan §9 was written to prevent, arriving one level up from where it
was expected.** §9 diagnoses unit 003's overrun as *"003's ceiling was set before its open
decisions were answered"* and prescribes two ceilings so that *"the decision's cost is visible
before it is taken"*. Both ceilings were then set beside a table that already exceeded them. **The
prescription was correct and it was not applied to its own document** — which is the same shape as
a hand-written figure sitting beside the machine-printed figure that contradicts it (the `28` /
`30` correction in `DECISIONS.md`), and it survived for the same reason: **a number that gates
nothing is a number nothing checks** (D-044 rules the budget counts and does not gate, so this
overrun has never blocked anything and never would have).

**No D-031 rider is opened here, and the reason is stated rather than assumed.** A D-031 rider
names *what an overrun displaces*, and an overrun that existed before any work was scheduled
displaces nothing that this wave chose. The honest instrument is the one §9 asked for: **the
ceiling is wrong, not the work.** Re-setting it belongs with D-054, because that ruling is what
§9 says moves the figure by twelve, and re-setting it now would be setting a ceiling before the
decision again. **Named here so the next accounting entry starts from 32 and from this paragraph,
rather than from a sentence at the head of the file that has been false since the day it was
written.**

**What wave 23 did NOT touch**, so the green is not read as wider than it is: `content/`,
`recommended.yml`, `screenshot.webp`, `README.md`, `AGENTS.md`, `.gitlab-ci.yml`, the `ai`
seam of `recipe.yml`, `tests/bin/no-secrets` (plan §5.4's level-2 extension is **not** in any
wave-23 row and remains open), and every file in `agora_theme`.

---

## Wave 25 · The retrieval surface and its gates

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0516 ⏸ | T | The retrieval substrate: either a `search_api` index on the DB backend over the six bundles, or the existing Views exposed filters | If Search API: `drupal/search_api` in `require` with its `DECISIONS.md` line, `sbom-check` green. Either way a kernel test prints **items indexed / rows reachable** and asserts it equals the count of **published** nodes — **and creates an unpublished node and asserts its absence**. A filter that can never exclude anything is an untested filter | T-0502, the substrate ruling |
| T-0517 ⏸ | T | The answer surface: one route taking a question and returning **sources**, working with no key | Anonymous `GET` returns **200**. A question matching corpus content returns **at least 3** source links, each resolving to a published node, **asserted by following them and reading the status**. A question matching nothing returns **0** sources and the fixed no-sources statement. ⚠️ **Both halves, or the row is not done**: a surface that always returns something is untested | T-0516 |
| T-0518 ⏸ | T | The `allow_access_bypass` guard | Prints **config objects examined** (> 0, FATAL at 0) and **bypass flags found: 0**. Dirty case: set it true in a fixture → exit 1 naming file and key | T-0517 |
| T-0519 ⏸ | T | Add the answer surface to `AccessibilityTest`'s declared page list | The declared page count rises by one and the assertion floor moves with it. The `phpunit` job's new assertion total is **quoted in the row**. ⚠️ `heading-order` must be reported on **N of N** pages — a rule that stops applying fails the gate rather than disappearing (I-045) | T-0517 |
| T-0520 ⏸ | T | The keyless functional test: anonymous, no key, no env var | 200 asserted; the absence of *"The website encountered an unexpected error"* asserted; the page states the generative feature needs configuration, asserted by string. **No `markTestSkipped` anywhere in the file**, enforced by T-0514 | T-0517 |
| T-0521 ⏸ | T | `AGENTS.md:77-78` and `README.md:33` reconciled with what now ships | Each claim quoted, its current truth value stated, and the file edited **or explicitly left alone with a reason**. ⚠️ **A claim that is still aspirational must say so in the shipped text**, not in a spec file the user never reads | T-0520 |

---

## Wave 26 · The generative half — every row conditional on the module ruling

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0522 ⏸ 👤 | T | **If the ruling is yes:** create the third package — project page, CI pipeline, security-coverage opt-in, SBOM line | The project resolves on the GitLab API → 200 (I-012: the API is the only valid oracle). Its pipeline's **job list read from the API**, every job `success`, every `allow_failure` false, count printed<br>**WIDENED 2026-09-25, 0 new rows:** the project page he creates; first push; default branch `1.x`; its job list; the security-coverage opt-in on day 10 or later; the tag after his yes; his release. | the module ruling |
| T-0523 ⏸ | T | **If yes:** `drupal/ai ^1.4` into `require` with its `DECISIONS.md` line; **no provider** | `sbom-check` green naming `ai` at **1.4.9**, `covered="1"`. `no-experimental-modules` green: **no experimental submodule is in `install:`**, asserted by name | T-0522, T-0513 |
| T-0524 ⏸ | T | **If yes:** the citation guarantee of plan §4 — strip pipeline plus server-side citation rendering | `CitationRenderTest`: stubbed provider returns 3 fabricated URLs and a fake title; rendered answer contains **0** anchors; citation block contains **exactly** the stub's entity URLs by equality; empty retrieval set → **0** provider calls. **Falsified in both directions**: remove stripping → red; remove the short-circuit → call count 1 | T-0523 |
| T-0525 ⏸ | T | **If yes:** `tests/bin/no-model-authored-links` to plan §4 | Prints templates, render paths, sanctioned uses, findings; templates > 0; findings 0. Dirty case: one raw render of provider output → exit 1 naming file and line | T-0524 |
| T-0526 ⏸ | T | **If yes:** the `gin_login` route alter of D-052, which becomes free once a module exists | Proven by **rendering `/user/login` and asserting the theme**, never by quoting the hook's documentation — D-052 §3 names three behaviours the hook does not switch off, and each is checked and its state recorded<br>**WIDENED 2026-09-25, 0 new rows:** the local package, its tests and the rig proof (`/user/login` served by the front theme, 1 `<h1>`, 1 reset link); Repo becomes A. | T-0522 |
| T-0527 ⏸ 👤 | T | **If the ruling is no:** `recommended.yml` filled by criterion rather than brand, and the README and AGENTS claims **narrowed** | Every entry verified stable and `covered="1"` with its version printed. ⚠️ Consuming the list needs `project_browser`, which is **beta** — so either it is not added (and the file's header says so) or a rule-1 exception is signed. **A `recommended.yml` nothing can read is documentation and must be labelled as documentation** | the module ruling |
| T-0528 ⏸ | T | The AI disclosure surface: what is generated, by which provider, what is sent where | Present on every page carrying a generated answer, asserted by string. Passes axe as part of T-0519's page. Names the provider **from configuration, never hard-coded** — asserted by changing the provider in config and re-reading the page | T-0524 or T-0527 |

---

## Wave 27 · Gates, audit, closure

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0529 ⏸ 👤 | T | The manual key protocol: five questions, expected source sets, run once with a real key, transcript committed | Transcript in the repository with provider and model named and the **key redacted**, the redaction proven by `no-key-material` exiting 0 over it. ⚠️ **Recorded as a measurement, never as coverage**: it appears in no gate total and closes no wave | T-0524 |
| T-0530 ⏸ | T | Full gate A with real counts | Both runners' totals printed and matching their `# GATE-CLAIM:` lines. Job list from the API with pipeline id, ref and commit quoted; every job `success`; every `allow_failure` false; `jobs >= 10`. **The pipeline's status field is never the evidence** (D-023(5)) | all |
| T-0531 ⏸ | T | `orquestador` READ-ONLY audit: standards, SBOM, licences, publishability, accessibility, structure | Verdict delivered with no open 🔴. Every finding carries file:line, why, remedy, target unit | T-0530 |
| T-0532 ⏸ 👤 | T | Closure: unit report, budget accounting in the same commit, HOLD | The count printed by the command at the head of this file. D-044's necessity test applied **row by row**, necessary and useful separated, the basis of each stated | T-0531 |

---

## ⚠️ The wave counter collided, and it is the same coupling this file already fixed once

**Waves 22 and 23 were each executed TWICE on 2026-09-20 — once in unit 003 and once here.** Unit
003 ran its waves 22, 23 and 24 the same night this unit ran its 22 and 23. Three collisions, and
the third was caught only because an implementer noticed a heading it did not expect.

**The unexecuted waves are renumbered 24 → 25, 25 → 26, 26 → 27**, highest first so no renumber
collided mid-flight. ⚠️ **The two that already ran are NOT renumbered**: they happened, their
commits and their CI pipelines name them, and rewriting executed history to tidy a counter would
trade a visible collision for an invisible one.

⚠️ **The defect is the coupling, not the numbers — and this file already fixed the same coupling
once.** `plan.md` §7 says waves are *"numbered globally"*, but **nothing owns that counter**: it
lives in prose in two task files that are edited in parallel by different sessions, so two units
picking "the next one" pick the same one. That is exactly why D-058 decoupled **task ids** from
wave numbers, and the argument transfers wholesale: **a shared counter with no mechanism is a
number that goes wrong in one place first.**

**Two ways out, neither taken here because it is a process decision and not a task:**

- **A** · Unit-scoped wave numbers, the way task ids already are — unit 005 runs waves 1, 2, 3.
  Collisions become impossible by construction, and a wave heading stops carrying information
  about what else was happening in the project that week.
- **B** · Keep global numbering and give the counter a home a machine can read — a single file, or
  a check in `tests/bin/` that fails when two units claim one number. The information survives and
  the collision becomes detectable.

★ **A**, on the same reasoning D-058 already won: the cheapest fix for a counter nobody owns is
not to share it. Left for whoever opens the next unit, with the cost of being wrong stated — under
A, a reader can no longer tell from a heading whether two waves ran in the same week, which is
information this project has used exactly once, in the paragraph above.

---

## ⚠️ `plan.md` §5.4 is answered, and the answer is "do not build it" — [ejecutor] 2026-09-21

**No row in this file is edited** (rule 8). This is appended because §5.4 is carried by no wave row
in either unit, and whoever executes wave 25 would otherwise implement it.

§5.4 asks for an extension of `tests/bin/no-secrets` level 2, *"not a new script"*, to learn the
`key_provider_settings` shape *"whose value can legitimately be short and which today's length rule
would miss"*. It was measured under **T-1917** in `specs/003-demo-content/tasks.md`, wave 28.
Three findings, in the order they matter:

1. **The subject is already covered, and covered more strongly than §5.4 asked for.**
   `tests/bin/no-key-material` — built for §5.1 — matches `key_value` and
   `key_provider(_settings)?` in a YAML **key position, whatever the value**. A planted
   `config/key.key.probe.yml` carrying `key_provider: config` and `key_value: sk-ab12` gives it
   **4 findings and exit 1**; `no-secrets`, over the same tree, prints `findings: 0` and `clean`.
   A length rule can never see `key_value: ''`, and the empty one is the warning that precedes the
   incident — which is §5.1's own argument, now measured for the **provider** half as well.

2. **§5.4's stated reason is wrong in a way that misdirects the fix.** The length rule is not why
   `no-secrets` misses this shape: `key_value` and `key_provider` **are not in its level 2 keyword
   list at all**, so it misses them at every length, a full-length real key included. Lowering the
   threshold would have changed nothing.

3. 🔴 **The extension cannot be made in the file §5.4 names.** Simulated exactly — level 2's
   pattern with the two keywords added, level 2's placeholder suppression applied to the isolated
   value, over the whole working tree — it produces **two findings, both on this unit's own
   research prose**: `research/2026-09-19-ai-and-governance-state-of-the-art.md:62` and
   `research/2026-09-20-wave-22-measurements.md:661`. Neither is a credential and neither is
   suppressed. That is I-018 verbatim, and `no-secrets`' own header spends four paragraphs
   refusing it. **So §5.4 is not merely redundant; it is unimplementable where it is written,
   without weakening the invariant it names.**

### The one residual, left open on purpose and owned by this unit

Outside `config/`, `content/` and `recipe.yml` — the three trees `no-key-material` reads — a
`key_value:` carrying a real key that matches no level-1 issued shape (`sk-`, `ghp_`, `AIza`, a
JWT, a connection string) is caught by **nothing**. The realistic home for such a value is
`.gitlab-ci.yml` or `.github/workflows/`, which are YAML and which this package does not ship.

**It is a SCOPE decision on `no-key-material`, not a pattern change in `no-secrets`**, and it is
left here rather than taken in unit 003, because the sentence that bounds that script's scope is
its own: *"key material is configuration, and configuration in this package is YAML."* Widening it
to the repository's CI YAML fits that sentence and costs one line plus a denominator; widening it
further does not. Measured on 2026-09-21: the two keywords occur in **three tracked files**
(`tests/bin/no-key-material`, and the two research documents above) and in **no** CI file, so the
widening would be green on the day it lands and non-vacuous in scope.

---

⚠️ **Reserve accounting, 2026-09-25 — D-054 is signed as B, so the module branch's ceiling applies:
28 rows. The unit holds 32. Over by 4, and recorded as that.** Counted with this file's own command,
never from prose:

```
grep -cE '^\| T-05[0-9]{2} ' specs/005-ai-and-governance/tasks.md   ->  32
```

**The ruling adds no row and removes none**: the rows the module branch needs were already here,
written conditional on the ruling (*"If the ruling is yes"*). The first reserve-accounting entry
above left the ceiling to be re-set with this ruling; **D-054 records the overrun instead of
re-setting the ceiling upward to fit**, because a ceiling moved to meet the count after the work is
chosen measures nothing. The one number that can genuinely bind — at most 4 rows resting on neither
D-044 necessity nor a signature from [andres] — is unchanged at **0** spent.

---

## Rows added 2026-09-25 · Config Guardian 1.0.5 and the companion module

Each row rests on what its task cell's closing clause names. *The mandates* is the entry
"[andres]'s mandates of 2026-09-25 (late morning), verbatim" in `specs/000-project/DECISIONS.md`;
*the record* is that file's "[andres]'s answers of 2026-09-25, verbatim", whose item 1(6) is his
sixth answer under item 1: Impact Analysis takes more than 10 seconds to open.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0533 ○ | G | Config Guardian's CI pins `_PHPUNIT_CONCURRENT: '0'` and `--fail-on-empty-test-suite`, and makes its lint jobs blocking; its kernel test classes declare `#[RunTestsInSeparateProcesses]`. *Rests on: necessity (I-116).* | Pushed job list: 8 jobs, all `success`, none permissive; the `phpunit` trace shows `_PHPUNIT_CONCURRENT=0` and the flag in the executed command, with totals printed | — |
| T-0534 ○ | G | The impact analysis reads each configuration object once: a single-pass dependency index; graph nodes unique. *Rests on: his words, record item 1(6).* | Read-count tests: active reads == N and sync reads == S for any number of changes; on the 669-object rig, 2,781,103 → 669 reads and identical findings (MEDIUM 42/100; 669 rows) | — |
| T-0535 ○ | G | Empty or invalid sync: the page says why and analyses nothing. *Rests on: D-075, signed by [ejecutor] (the mandates' (c)).* | Functional test: the sentence appears once, 0 change rows, no graph frame; the export link only for a reader who may export<br>**WIDENED 2026-09-25, 0 rows:** a functional test submits Sync → Export and follows its batch to the end — N files in the sync directory, 0 php watchdog rows — watched failing on PHP 8.3 with readonly restored (Config Guardian `0c5dc96`). | — |
| T-0536 ○ | G | The Impact Analysis page renders before the analysis ends (BigPipe placeholder with a `role="status"` preview; finished state announced; no focus moved; works without JS). *Rests on: record item 1(6).* | Functional tests in the three modes plus a FunctionalJavascript announce test; the page's first flush carries the preview | — |
| T-0537 ○ | G | The dependency-graph frame reuses its page's graph, and its two fields are labelled. *Rests on: record item 1(6); labels under the mandates' (c).* | Frame test: `?source=parent` carries no node payload; `label[for]` ×2 | — |
| T-0538 ○ | G | The dashboard shows the warnings and totals it computes; pages offer only the actions their reader may take. *Rests on: necessity (D-059's role reaches 7 routes that answer 403; the dashboard withholds its own warning).* | A closed-world functional test as the auditor: every Config Guardian link GETs without 403, count > 0 printed; the empty-sync warning appears exactly once | — |
| T-0539 ○ | G | Dashboard text meets WCAG 2.2 AA contrast (the 7 nodes this template's gate declares). *Rests on: the mandates' (c).* | axe on the dashboard: 0 `color-contrast` in Config Guardian's markup | — |
| T-0540 ○ | G | CHANGELOG and README for 1.0.5; the template's suite on a rig against Config Guardian's tip, measuring the new declared violation set; release notes drafted, never committed. *Rests on: necessity.* | Rig: `OK (22 tests, <m>)`, with the new set recorded; notes handed over | T-0533 to T-0539 |
| T-0541 ○ 👤 | G | Tag 1.0.5 after his yes; he creates the release. *Rests on: his signature.* | The tag's pipeline is 10/10-shaped (all its jobs green, none permissive); the release feed lists 1.0.5 | T-0540, his yes |
| T-0542 ○ | T | The template requires `drupal/agora_core: ^1.0` and `config_guardian: ^1.0.5`; `recipe.yml` installs `agora_core`; SBOM line; `/user/login` in `AccessibilityTest`; `ValidationTest` asserts the theme that serves `/user/login`; README and statement updated. *Rests on: D-054 plus the mandates.* | Job list 10/10; `Locking drupal/agora_core (1.0.0)` in the `Drupal CMS` trace; phpunit totals predicted and matched | T-0522, T-0526, T-0541 |
| T-0543 ○ | T | `tests/bin/doctor` and the project instructions know the four working copies (template, theme, `agora-core`, `config-guardian`). *Rests on: necessity.* | Doctor's group 6 prints each, with its branch and push remotes, and warns on `origin` | — |

**Accounting, 2026-09-25, [andres]'s mandates of the same day (I-105): 43 known rows · stated ceiling 28 (module branch, D-054) — over by 15, recorded rather than re-set** (D-044; D-054 refused to re-set). +11 rows (T-0533–T-0543); T-0522 and T-0526 widened with 0. New contrib dependencies: **+1**, `drupal/agora_core` (ours). Config Guardian's floor moves `^1.0` → `^1.0.5` when T-0542 lands. New invariants: **0**. What the overrun displaces: nothing in this unit's own queue — the rows run in two other repositories or after releases — but calendar before the launch: T-0542 cannot land before `agora_core`'s coverage day.

**Progress, 2026-09-25 (afternoon).** Config Guardian's `1.0.x` now carries the work of T-0533 to T-0539, plus the export fix (`9189c56`; its test `0c5dc96`, then `6e9784a`), the Impact Analysis link gating (`0a76059`) and T-0540's 1.0.5 CHANGELOG and README (`b0a4b62`). The latest pushed pipeline is 976665 on `b0a4b62`: 8 jobs, all `success`, none permissive; `Tests: 183, Assertions: 1282`. The rows close in a later commit, after the local commits drupalcode does not have yet are pushed and green — one when this was written, `d6208e0`, read against `git ls-remote`.
