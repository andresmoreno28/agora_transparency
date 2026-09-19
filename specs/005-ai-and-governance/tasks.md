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
| T-0509 ○ | T | **Lane A** · `drupal/config_guardian ^1.0` into `require`, with its `DECISIONS.md` line **in the same commit** (rule 2) | `tests/bin/sbom-check` exits 0 with its denominator printed, naming `config_guardian` verified stable and `covered="1"`. `composer validate` exits 0. ⚠️ **Removing the `DECISIONS.md` line is watched turning `sbom-check` red** — an untested rule-2 check is a claim | T-0505 |
| T-0510 ○ | T | **Lane A** · The `governance` area block in `recipe.yml` plus `config/config_guardian.settings.yml`, retention and exclusions each carrying a comment saying why | `tests/bin/config-inventory` prints the new count and **the rise from its current value is stated, not implied**. The `recipe.yml` diff touches only lines inside the governance seam, asserted by a diff whose line numbers are quoted | T-0509 |
| T-0511 ○ | T | **Lane A** · `agora_governance_auditor` role: `view config snapshots` + `analyze config impact`, and **none** of the seven `restrict access: true` permissions | A kernel test asserts the permission set by **equality, not containment**, and asserts the role holds **0** of the seven restricted ones, **listing all seven by name** so a new restricted permission upstream cannot slip in unnoticed | T-0510 |
| T-0512 ○ | T | **Lane B** · `tests/bin/no-key-material` to plan §5.1 | Prints files, key entities, `key_value` assignments, provider key configs, findings; files > 0; findings 0. **Three dirty cases watched failing**: a real-shaped key; an **empty** `key_value: ''` (the case `no-secrets` structurally cannot see); a `key.key.*` with no `key_value` at all. Registered in `gate-a-wave3.sh`, whose total moves from **51** to a stated number, with `# GATE-CLAIM:` updated in the same commit | T-0501 |
| T-0513 ○ | T | **Lane B** · `tests/bin/no-experimental-modules` to plan §5.2 | Prints **modules examined** and **lifecycle keys read** — two numbers, because `N > 0` with `L == 0` is a parser that never ran, not a clean tree (I-028). **Falsified both ways**: over a tree containing `ai_search`, **1 finding naming `lifecycle: experimental`**, exit 1; over the real `install:` list, 0 | T-0505 |
| T-0514 ○ | T | **Lane B** · `tests/bin/no-skip-on-missing-key` to plan §5.3 | Prints files and **skip constructs found, each with its condition**; files > 0. Dirty case: a `markTestSkipped` guarded on an API-key env var → exit 1 naming file, line and condition. ⚠️ **`0 skips` must be printed, not silently absent** — no skips and a parser that found nothing look identical | T-0501 |
| T-0515 ○ | T | **Lane B** · `CLAUDE.md`'s gate block updated: both `# GATE-CLAIM:` lines, the checks-and-invariants sentence, and the `jobs >= N` floor if the job list moved | `tests/bin/claims-match-sources` exits 0 with its comparisons printed and its `NOT CHECKED` list non-empty. ⚠️ **If the job list did not move, that is stated as a measured non-change, not omitted** | T-0512, T-0513, T-0514 |

---

## Wave 24 · The retrieval surface and its gates

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0516 ⏸ | T | The retrieval substrate: either a `search_api` index on the DB backend over the six bundles, or the existing Views exposed filters | If Search API: `drupal/search_api` in `require` with its `DECISIONS.md` line, `sbom-check` green. Either way a kernel test prints **items indexed / rows reachable** and asserts it equals the count of **published** nodes — **and creates an unpublished node and asserts its absence**. A filter that can never exclude anything is an untested filter | T-0502, the substrate ruling |
| T-0517 ⏸ | T | The answer surface: one route taking a question and returning **sources**, working with no key | Anonymous `GET` returns **200**. A question matching corpus content returns **at least 3** source links, each resolving to a published node, **asserted by following them and reading the status**. A question matching nothing returns **0** sources and the fixed no-sources statement. ⚠️ **Both halves, or the row is not done**: a surface that always returns something is untested | T-0516 |
| T-0518 ⏸ | T | The `allow_access_bypass` guard | Prints **config objects examined** (> 0, FATAL at 0) and **bypass flags found: 0**. Dirty case: set it true in a fixture → exit 1 naming file and key | T-0517 |
| T-0519 ⏸ | T | Add the answer surface to `AccessibilityTest`'s declared page list | The declared page count rises by one and the assertion floor moves with it. The `phpunit` job's new assertion total is **quoted in the row**. ⚠️ `heading-order` must be reported on **N of N** pages — a rule that stops applying fails the gate rather than disappearing (I-045) | T-0517 |
| T-0520 ⏸ | T | The keyless functional test: anonymous, no key, no env var | 200 asserted; the absence of *"The website encountered an unexpected error"* asserted; the page states the generative feature needs configuration, asserted by string. **No `markTestSkipped` anywhere in the file**, enforced by T-0514 | T-0517 |
| T-0521 ⏸ | T | `AGENTS.md:77-78` and `README.md:33` reconciled with what now ships | Each claim quoted, its current truth value stated, and the file edited **or explicitly left alone with a reason**. ⚠️ **A claim that is still aspirational must say so in the shipped text**, not in a spec file the user never reads | T-0520 |

---

## Wave 25 · The generative half — every row conditional on the module ruling

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0522 ⏸ 👤 | T | **If the ruling is yes:** create the third package — project page, CI pipeline, security-coverage opt-in, SBOM line | The project resolves on the GitLab API → 200 (I-012: the API is the only valid oracle). Its pipeline's **job list read from the API**, every job `success`, every `allow_failure` false, count printed | the module ruling |
| T-0523 ⏸ | T | **If yes:** `drupal/ai ^1.4` into `require` with its `DECISIONS.md` line; **no provider** | `sbom-check` green naming `ai` at **1.4.9**, `covered="1"`. `no-experimental-modules` green: **no experimental submodule is in `install:`**, asserted by name | T-0522, T-0513 |
| T-0524 ⏸ | T | **If yes:** the citation guarantee of plan §4 — strip pipeline plus server-side citation rendering | `CitationRenderTest`: stubbed provider returns 3 fabricated URLs and a fake title; rendered answer contains **0** anchors; citation block contains **exactly** the stub's entity URLs by equality; empty retrieval set → **0** provider calls. **Falsified in both directions**: remove stripping → red; remove the short-circuit → call count 1 | T-0523 |
| T-0525 ⏸ | T | **If yes:** `tests/bin/no-model-authored-links` to plan §4 | Prints templates, render paths, sanctioned uses, findings; templates > 0; findings 0. Dirty case: one raw render of provider output → exit 1 naming file and line | T-0524 |
| T-0526 ⏸ | T | **If yes:** the `gin_login` route alter of D-052, which becomes free once a module exists | Proven by **rendering `/user/login` and asserting the theme**, never by quoting the hook's documentation — D-052 §3 names three behaviours the hook does not switch off, and each is checked and its state recorded | T-0522 |
| T-0527 ⏸ 👤 | T | **If the ruling is no:** `recommended.yml` filled by criterion rather than brand, and the README and AGENTS claims **narrowed** | Every entry verified stable and `covered="1"` with its version printed. ⚠️ Consuming the list needs `project_browser`, which is **beta** — so either it is not added (and the file's header says so) or a rule-1 exception is signed. **A `recommended.yml` nothing can read is documentation and must be labelled as documentation** | the module ruling |
| T-0528 ⏸ | T | The AI disclosure surface: what is generated, by which provider, what is sent where | Present on every page carrying a generated answer, asserted by string. Passes axe as part of T-0519's page. Names the provider **from configuration, never hard-coded** — asserted by changing the provider in config and re-reading the page | T-0524 or T-0527 |

---

## Wave 26 · Gates, audit, closure

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0529 ⏸ 👤 | T | The manual key protocol: five questions, expected source sets, run once with a real key, transcript committed | Transcript in the repository with provider and model named and the **key redacted**, the redaction proven by `no-key-material` exiting 0 over it. ⚠️ **Recorded as a measurement, never as coverage**: it appears in no gate total and closes no wave | T-0524 |
| T-0530 ⏸ | T | Full gate A with real counts | Both runners' totals printed and matching their `# GATE-CLAIM:` lines. Job list from the API with pipeline id, ref and commit quoted; every job `success`; every `allow_failure` false; `jobs >= 10`. **The pipeline's status field is never the evidence** (D-023(5)) | all |
| T-0531 ⏸ | T | `orquestador` READ-ONLY audit: standards, SBOM, licences, publishability, accessibility, structure | Verdict delivered with no open 🔴. Every finding carries file:line, why, remedy, target unit | T-0530 |
| T-0532 ⏸ 👤 | T | Closure: unit report, budget accounting in the same commit, HOLD | The count printed by the command at the head of this file. D-044's necessity test applied **row by row**, necessary and useful separated, the basis of each stated | T-0531 |
