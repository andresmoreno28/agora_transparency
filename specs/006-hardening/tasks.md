# Unit 006 · Hardening — tasks

Scaffolded 2026-09-21. **No row here is signed.** Read `plan.md` first — its §1 supersedes three
things the ROADMAP still says, and its §5 explains why this unit opens over its own ceiling.

**Count with the command, never from prose:**

```
grep -cE '^\| T-06[0-9]{2} ' specs/006-hardening/tasks.md
```

⚠️ **Waves here are UNIT-SCOPED (1..5), not global.** The global counter collided three times in
one night on 2026-09-20 because nothing owns it; D-062 proposes making that permanent and is
unsigned. If D-062 is ruled B, these headings change and **no task id moves** — which is the whole
point of D-058's decoupling.

⚠️ **Budget accounting, first entry, 2026-09-21 — wave 2, and it spends no rows at all.**
T-0608 … T-0611 were scaffolded rows; closing them moved the count from **27 to 27**. Run the
command above: it prints **27**. The arithmetic in `plan.md` §5 is therefore unchanged — 27 known
rows against a stated ceiling of 32, with the unit still opening over its real obligation because
of the pull-forward I-105 records.

**What wave 2 DID spend is the instrument `plan.md` §5 names third, and it is worth writing down
because it is the one that cannot be recovered.** §5 budgets **two** new invariants, named in
advance: `packaged-claims` and the drift mode. **Wave 2 spends the first.** `tests/bin/packaged-claims`
costs, permanently, a `GATE-CLAIM` field, a group in the wave-1 runner (G11) and six checks —
**71 → 77 checks, 2 → 3 invariants** — plus three figures in `CLAUDE.md` that
`tests/bin/claims-match-sources` now compares on every push. **One remains**, T-0622's, and a third
is a conversation rather than a decision this unit can take on its own.

**D-044's necessity test, applied to the row that spent it:** T-0608 passes. Something already
signed was **false** — `README.md` told a Drupal.org visitor the package ships 131 config objects
and quoted both gate runners' check totals four and seventeen behind, in a package whose stated
non-negotiable property is that every claim has a source a stranger can read. `plan.md` §2 states
that as the definition of hardening. The other two constraints are untouched: **zero** new contrib
dependencies, and **zero** rows resting on neither necessity nor a signature.

Glyphs: `✓` done · `○` open · `⏸` blocked · `👤` needs [andres].

---

## Wave 1 · Measurement before planning — no file outside `specs/006-hardening/research/` is touched

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0601 ○ | T | **Re-read the marketplace review criteria at source today** | The five criteria quoted verbatim with URL and fetch date. Any difference from D-012's 2026-08-21 finding stated as a divergence, **never merged**. ⚠️ *"Unchanged"* is a result and is printed **with the URL that produced it** | — |
| T-0602 ○ | T | What `haven` and `byte` ship as a **security-response** statement and as a **licence manifest** | Per template: **files examined** printed and `> 0`; each artefact's presence or absence stated **with its path**. **0 of N is a result**; *"they do not appear to"* is not | — |
| T-0603 ○ | H | axe + keyboard over the **Config Guardian dashboard** and the authenticated admin surface | Per surface: **pages scanned · rules run per page · violations**, each violation attributed to the module whose markup produced it, **by selector**. The three known foreign violations (`navigation` ×2, `coffee` ×1) named and their count asserted; **a fourth is a finding**. ⚠️ A rule that did not run cannot have passed (I-045) — the rule count is part of the criterion | a rig |
| T-0604 ○ | T | The **28-day drift** in the two `adapted` shared invariants | `git diff --numstat 935c133f HEAD` quoted for both (**132/4** and **54/3**, measured 2026-09-21), **every added hunk classified** theme-relevant or template-only with each count printed. Both scripts then **run in the theme**, exit status and denominators recorded. ⚠️ **No re-sync in this row — measure first** | — |
| T-0605 ○ | T | Where a page count can be emitted from a PHPUnit test **on a green run** | Three candidate routes (assertion message · JUnit log · printer) each stated with what the runner does with it, read from the upstream template. ⚠️ **Quote `RequirementsTest`'s own header**, which records the pipeline that failed because a test wrote to STDERR, rather than rediscovering it | — |
| T-0606 ○ | T | Page weight and query count of the **eight listing routes** | Two numbers per route, tool named, all eight present. **No threshold is set in this row** — a number with no threshold is honest; a threshold invented at the end is not | a rig |
| T-0607 ○ | T | Which of the ROADMAP's ten 006 points are already continuously green | One row per point — green / stale-wording / absent — each with the `file:line` or job id that decides it. **Ten rows or the row is not done** | — |

---

## Wave 2 · The claims layer

**Closed 2026-09-21 by [ejecutor].** Four rows, no new rows: the count this file's own command
prints is **27** before and after. The measurements are in the criterion cells, appended rather
than substituted, so the criterion a row was written against stays readable beside the result.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0608 ✓ | T | `tests/bin/packaged-claims` — an extractor over **packaged, user-facing** prose | Prints **files opened · claims extracted · findings**. ⚠️ **`claims extracted == 0` is FATAL, not a pass** (I-028) — an extractor that matches nothing passes by construction. Every claim matched against the trace-figures table or a **non-empty** NOT CHECKED list whose length is printed. **Dirty case:** alter one figure in `README.md` → exit 1 naming file, line, claimed value and measured value.<br>**DONE.** `bash tests/bin/packaged-claims` prints `files opened: 6 · claims extracted: 21 · comparisons: 31 · one-copy checks: 8 · not checked: 9 · findings: 0`, exit 0. Five denominators, not one, and each is a gate check in wave 1's new G11: an extractor whose patterns stopped matching, a run that opened no file, a run that compared nothing, an emptied one-copy register and a deleted NOT CHECKED entry all print `findings: 0`. Subjects verified present in `git archive` (374 entries) before being read, so `export-ignore`ing one is a FATAL rather than a silent narrowing. `--online` reads **5** figures the shipped statement quotes about the theme, from `agora_theme` pipeline **969928** (commit `d8079ad0`, newest terminal success on `1.x`): `nightwatch` job `12332875` → 10 pages · 89 rules · 0 violations, `agora-invariants` job `12332874` → 68 contrast pairs · 0 below threshold. All five OK, `NOT READ 0`.<br>**DIRTY, in a scratch copy:** the **five** figures the pre-extractor audit found wrong were re-planted at their old values and **all five fired**, each naming file, line, claimed and measured — `README.md:12 claims 131; the measurement says 133` · `README.md:246 claims 13 … says 11` · `README.md:393 claims 67 … says 77` · `README.md:393 claims 49 … says 66` · `content/MEDIA-LICENCES.md:72 claims 40 … says 42`. `findings: 5`, exit **1**. The sixth wrong figure, the spell-check denominators (`451 · 410`, really `467 · 426`), is **named in NOT CHECKED** rather than bound: it comes from `tests/bin/spellcheck`'s own filter and no offline read produces it. Its **subtraction** is bound, in the same paragraph, as `37 + 4 = 41` | T-0607 |
| T-0609 ✓ | T | The **one-copy rule**, enforced | The extractor FATALs when one figure appears in two packaged files, in the wording `claims-match-sources.py`'s `one()` helper already uses. ⚠️ **Watched failing on a REAL case**: the recorded before/after of `README.md`'s theme axe figure, which stated *"nine fixture pages … 9 of 9"* from 2026-09-20 17:56 until it was removed — **written in the commit whose own subject was that the package contradicted itself about accessibility, and false within hours.** Re-plant it in a scratch copy, never in the tracked tree.<br>**DONE.** **8 registered figures**, each declaring a home; the detector runs over all six packaged files and the matched set must equal the home exactly. A home is one of three things — one packaged file, `EXTERNAL` (no packaged copy at all), or a **deliberately bound** pair.<br>**WATCHED FAILING ON THE REAL CASE.** The 2026-09-20 sentence was re-planted in a scratch copy, never in the tracked tree, and `theme_axe_gate` fired:<br>`TWO COPIES: theme_axe_gate is stated in 2 packaged files:` `README.md` → `nine fixture pages` · `content/node/542d…yml` → `axe-core runs there over ten pages`.<br>Exit **2**, no summary line, in the `one()` helper's own wording — *"the same number is written in more than one place, which is the drift this guard exists to refuse"* — and the two quoted copies show the drift itself: README would say nine while the statement says ten | T-0608 |
| T-0610 ✓ | T | Every remaining packaged figure stated **once** | Each surviving duplicate either becomes a reference or is deleted; the surviving copy is named. ⚠️ **Refreshing both copies faithfully is the wrong fix** and this file's own history is the evidence: every previous refresh moved one copy and left the other.<br>**DONE. Four cross-file duplicates found; four resolved, survivor named in each case.** (1) *the nine scanned pages* — survivor **the shipped accessibility statement**, `README.md` now carries no count and points at `/accessibility-statement`, which is the document a visitor is actually served and has to be readable alone. (2) *four of the eight listing routes* — survivor **the statement**, same reason and the same sentence. (3) *34 PDFs* and (4) *5 CSV distributions* — survivor **`content/MEDIA-LICENCES.md`**, the document whose subject is the media; README's bullet is now a cross-reference with no number in it.<br>**Plus four in-file restatements deleted from the statement** — `the same nine scanned while signed in`, `Result: nine pages, 0 violations`, `each of the nine`, `Those ten are test fixtures` — each rewritten to name the pages rather than re-count them, and **one from README** (`thirteen`/`ELEVEN` top-level entries, which contradicted itself four paragraphs apart; the live sentence now leads with eleven).<br>**THE THIRD OPTION WAS USED TWICE, where deletion would cost a reader something.** *six registers* stands in **both** README and the statement and is declared a **bound** duplicate: both copies are compared against the distinct `agora_base_*` node bundles in the same run, so neither can drift alone. The package **description** stands in both `recipe.yml` (read by the installer) and `composer.json` (read by Packagist), cannot be deleted from either, and is now compared against itself — `description_match`, 215 characters | T-0609 |
| T-0611 ✓ | T | The gate floor **exists once, derived** | `jobs >= N` computed from `len()` of the declared job list; the five other mentions become references. **Dirty case:** shorten the declared list by one → the floor moves with it and the job-list comparison fails. ⚠️ **Falsified the other way too:** there is no longer a set of six numbers that could all be lowered together, which is how the current heuristic passes.<br>**DONE.** `CLAUDE.md` now carries one marked canonical sentence — *"THE FLOOR, canonical and stated once: `jobs >= 10`"* — and `claims-match-sources.py` reads it with the `one()` helper (exactly one match, or FATAL) and compares it against **`len()` of the shorter of the two job lists `tests/bin/watch-gate` declares**. The other five mentions (`7`, `7`, `8`, `9`, `9`) are **named as references in the file** and asserted as a frozen multiset, because rule 8 makes them immutable. Offline comparisons **10 → 11**; `floor_history` is the eleventh.<br>**DIRTY 1 — shorten the declared list.** `phpunit-pgsql` removed from `EXPECTED_agora_transparency`: `floor MISMATCH claimed 10 / source 9` **and** `template_jobs MISMATCH` in the same run. `mismatches: 2`, exit 1.<br>**DIRTY 2 — the hole the old heuristic left, measured rather than argued.** The canonical floor was lowered to `jobs >= 2` while one frozen mention was raised `9 → 10`. **The pre-change reader, run from `git show HEAD:` on that same tree, printed `CMP floor OK 10 10` and exited 0** — it passes a gate whose stated floor is 2, because `max()` over a set containing frozen records is satisfied by any one of them. The new reader prints `floor MISMATCH claimed 2 / source 10` **and** `floor_history MISMATCH claimed 7, 7, 8, 9, 10 / source 7, 7, 8, 9, 9`, exit 1. ⚠️ **The old reader named this gap in its own NOT CHECKED list** — *"whether a LOWERED gate floor is still consistent"* — which is the honest thing to do with a hole and is not the same as closing it. That entry is **replaced**, not deleted, by a narrower one that is true: nothing reads the markdown *around* the five frozen mentions, so a record moved out of its blockquote keeps its value and loses its framing | T-0608 |

---

## Wave 3 · Accessibility

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0612 ○ | T | `AccessibilityTest`'s page floor becomes an **equality** | The declared count is a named constant; the assertion is `assertSame`, not `assertGreaterThanOrEqual`. **Watched failing both ways:** declare 10 against 9 pages → red; declare 8 against 9 → red. ⚠️ Today a Canvas page could stop shipping, the gate would scan eight and stay green, and the statement would still say nine | T-0605 |
| T-0613 ○ | T | The page summary appears in a **GREEN** run's log | The line naming pages scanned, rules run and violations is present in the `phpunit` job's trace on a **passing** pipeline, quoted with job and pipeline id. ⚠️ **Measured 2026-09-21: it appears 0 times**, because the summary is an assertion message. The theme's equivalent is public. **A reviewer can check one claim and not the other** | T-0605, T-0612 |
| T-0614 ○ | T | The accessibility statement's **scoping sentence is made true** | The excluding sentence names **what actually renders the admin interface** — Gin via `drupal_cms_admin_ui`, core `navigation`, contrib `coffee`, and **the Config Guardian dashboard this package installs** — not *"Drupal core"*. Each component verified present by reading the install chain, with its `file:line`. ⚠️ **This is in every branch of D-061**: falseness is not a budget question | T-0603 |
| T-0615 ⏸ 👤 | T | **The admin-surface ruling applied** | Under D-061 ★B: the Config Guardian dashboard is a declared **logged-in** page in `AccessibilityTest` with its own expectation set. Violations in markup **this package owns** = 0. Foreign violations enumerated **by selector** and asserted to be **exactly** the known set, so a new one fails the gate | T-0603, **D-061** |
| T-0616 ⏸ 👤 | T | The **keyboard walkthrough** of the nine anonymous pages, by a person | A transcript naming, per page: what was pressed, what received focus, whether the indicator was visible, and the outcome for **2.4.7 · 2.5.8 · 1.4.10 · 2.4.1 in use**. ⚠️ **Recorded as a measurement, never as coverage** — it enters no gate total and closes no wave. **A machine cannot press a key and see a focus ring**, and it is the attestation's evidence base | T-0612 |

---

## Wave 4 · The absent artefacts

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0617 ⏸ | T | The **WCAG attestation** — the author's statement about the template | A packaged document **distinct from** the demo accessibility statement — two audiences: the statement addresses a citizen and is completed by the body operating the site; the attestation addresses a reviewer and is about this package. States target, what is measured **with denominators**, what is **not** measured by name, the date and the method. Every figure is a **reference, not a second copy** — asserted by T-0609 | T-0616, T-0601 |
| T-0618 ⏸ 👤 | T | The **security-response commitment** | A packaged document naming where a report goes, the acknowledgement window, and what happens when a dependency carries an advisory. ⚠️ **It binds a person**: it names a route that exists today and a time a single maintainer can keep | T-0601, T-0602, **D-064** |
| T-0619 ○ | T | One **package-level licence manifest** | One document covering code (GPL-2.0-or-later), fonts (the theme's OFL Public Sans), media and generated assets, each with its **count** and the check that keeps it honest. `tests/bin/media-licence`'s denominator is **quoted, not restated** | T-0602 |
| T-0620 ⏸ | T | The README's claims about units 004 and 005 **narrowed to what ships** | Each promissory claim quoted with its line, its truth value stated, and the file edited **or explicitly left with a reason**. ⚠️ A claim still aspirational must say so **in the shipped text**, not in a spec file the user never reads | **D-060**, the 005 ruling |

---

## Wave 5 · Drift, sweep, verdict

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0621 ○ | H | Re-sync the two `adapted` invariants and regenerate the manifest | Both records carry a `source_commit` no older than this unit; both hashes **re-derived by three commands**, as the manifest's own precedent requires; `shared-invariants` exits 0 printing files swept, records and local-only. ⚠️ **D-028 forbids WEAKENING a copy, not editing one** — the 2026-09-19 correction is the authority, and the wider constraint it replaced was invented and cost six days | T-0604 |
| T-0622 ⏸ | T | Upstream drift becomes **detectable** rather than a dated task | `shared-invariants` gains a mode reading the source repository at the recorded commit and printing the **per-record line delta**, **or** the decision to keep it a dated review is recorded with its reason. Either way the output names the delta. ⚠️ **Today that delta is +132/−4 and +54/−3 against a 28-day-old commit and the script prints CLEAN** | T-0621, **D-063** |
| T-0623 ○ | T | The mirror's conclusion is **pushed, not pulled** | A streak `> 0` becomes visible without anybody typing the command. ⚠️ **D-020 unchanged**: the mirror stays informative and the exit status is untouched. It was red for three weeks and only an inbox noticed | T-0611 |
| T-0624 ○ | T | Final **SBOM sweep, re-read at source on the day** | `sbom-check` exits 0 printing projects queried / with coverage / findings; every release status re-read **that day**, because **rule 1 is about today**. Any project whose newest stable moved is named | all |
| T-0625 ○ | T | Full gate A with real counts, **both repositories** | Both runners' totals printed and matching their `# GATE-CLAIM:` lines **and their self-check lines**. Job list from the API with pipeline id, ref and commit; every job `success`; every `allow_failure` false; `jobs >= 10`. **The pipeline's status field is never the evidence** (D-023(5)) | all |
| T-0626 ○ | T | `orquestador` READ-ONLY audit | Verdict with no open 🔴. Every finding carries `file:line`, why, remedy, target unit. ⚠️ **The audit re-reads packaged prose with the extractor**, so its own claims are subject to the mechanism this unit built | T-0625 |
| T-0627 ⏸ 👤 | T | Closure: unit report, budget accounting in the same commit, HOLD | The count printed by this file's own command. D-044's necessity test applied **row by row**. ⚠️ **The rows pulled forward into unit 003 are counted here** (I-105), **by id, derived by command and not carried from the scaffold** — they are scattered across five or six prose notes in another unit's file, which is the budget's own version of the defect this unit exists to fix | T-0626 |
