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

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0608 ○ | T | `tests/bin/packaged-claims` — an extractor over **packaged, user-facing** prose | Prints **files opened · claims extracted · findings**. ⚠️ **`claims extracted == 0` is FATAL, not a pass** (I-028) — an extractor that matches nothing passes by construction. Every claim matched against the trace-figures table or a **non-empty** NOT CHECKED list whose length is printed. **Dirty case:** alter one figure in `README.md` → exit 1 naming file, line, claimed value and measured value | T-0607 |
| T-0609 ○ | T | The **one-copy rule**, enforced | The extractor FATALs when one figure appears in two packaged files, in the wording `claims-match-sources.py`'s `one()` helper already uses. ⚠️ **Watched failing on a REAL case**: the recorded before/after of `README.md`'s theme axe figure, which stated *"nine fixture pages … 9 of 9"* from 2026-09-20 17:56 until it was removed — **written in the commit whose own subject was that the package contradicted itself about accessibility, and false within hours.** Re-plant it in a scratch copy, never in the tracked tree | T-0608 |
| T-0610 ○ | T | Every remaining packaged figure stated **once** | Each surviving duplicate either becomes a reference or is deleted; the surviving copy is named. ⚠️ **Refreshing both copies faithfully is the wrong fix** and this file's own history is the evidence: every previous refresh moved one copy and left the other | T-0609 |
| T-0611 ○ | T | The gate floor **exists once, derived** | `jobs >= N` computed from `len()` of the declared job list; the five other mentions become references. **Dirty case:** shorten the declared list by one → the floor moves with it and the job-list comparison fails. ⚠️ **Falsified the other way too:** there is no longer a set of six numbers that could all be lowered together, which is how the current heuristic passes | T-0608 |

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
