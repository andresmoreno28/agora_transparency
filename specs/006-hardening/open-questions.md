# Unit 006 · Open decisions

Proposed 2026-09-21. ~~**None is signed.**~~ Held here rather than in `specs/000-project/DECISIONS.md`
because that file is append-only and signed, and a proposal written into it reads like a ruling.

⚠️ ~~**Next free number is D-060.**~~ D-059 is signed in `DECISIONS.md`; **D-054 to D-058 are reserved
and unsigned in unit 005's `open-questions.md` and must not be reused.** Next free idiom: **I-117**.

⚠️ **AMENDED 2026-09-21. THREE OF THE SIX ARE SIGNED, AND THIS FILE WAS NEVER TOLD.** Nothing above
is edited (rule 8) — both struck clauses were true when they were written and stopped being true the
same day. This file has **exactly one commit**, the scaffold `d06b9b6`, which is the whole defect:
a record of proposals that is never reopened goes on describing the moment it was written.

**`D-062`, `D-063` and `D-065` were signed by [ejecutor] under standing delegation in commit
`0966558`** — *"task: D-062, D-063 and D-065 signed under standing delegation"* — and their signed
text lives in `specs/000-project/DECISIONS.md`, never here. **All three were ruled as recommended**,
so no ★ below was overturned: D-062 → **A**, D-063 → **B**, D-065 → **A**. Each of the three
headings below now carries that fact at the point a reader meets it; the proposal text under it is
left exactly as written.

**`D-060`, `D-061` and `D-064` remain UNSIGNED and are [andres]'s.** Their substance is untouched.

⚠️ **And the numbering line went stale by the same commit, which the amendment above would have
hidden.** **The next free number is `D-066`.** D-060 to D-065 are all allocated — three signed in
`DECISIONS.md`, three reserved by the unsigned proposals in this file — and this file's own rule,
stated one line up about D-054 to D-058, is that a reserved number is never reused. Next free idiom
is still **I-117**, re-derived from `IDIOMS.md` on the day rather than carried: the highest there
is **I-116**.

---

## D-060 · Unit 004 was never scaffolded. What does 006 audit? — 👤 [andres]

*Context in one line:* the ROADMAP says 006 needs 004 closed; 004 has a README and nothing else,
and `recipe.yml` says `(empty in v1; filled by unit 004)` in **six** places.

| | option | cost |
|---|---|---|
| **A** | **006 waits for 004** | Faithful to the ROADMAP. Publication slips a whole unit and the seams stay empty meanwhile |
| **B ★** | **004 deferred past v1; 006 audits what ships and narrows the README here** | v1 becomes a transparency **publication** portal with no editorial workflow and no FOI cycle — **which is what `recipe.yml` already is.** Cost: `plan.md` §2's area table and the ROADMAP's 004 section describe features v1 does not have, and **the packaged text must say so before a reviewer reads it** |
| **C** | A minimal 004 inside 006 — moderation on the six bundles, no FOI | A feature unit wearing an audit unit's clothes. `content_moderation` is already installed so it *looks* cheap; moderation changes every view, every role and every test |

★ **B.** The product that exists is coherent without 004 — six registers, 60 records, a library —
and what needs fixing is a sentence, not a subsystem.

**Cost of being wrong about B:** a reviewer expects a workflow because `plan.md` promises one; the
remedy is prose and it is in scope here. **Cost of being wrong about A:** unit 007 does not happen
this year.

---

## D-061 · The administrative surface and the AA claim — 👤 [andres]

*Context in one line:* two gates measure fixtures and anonymous installed pages; **nothing
measures the interface an editor uses daily**, and the sentence excluding it is false.

**The question, answered plainly before the options:** *can a product claiming WCAG 2.2 AA ship
with its administrative interface unmeasured?* **Yes — but only if it never claims AA for that
interface, and today it accidentally does, in the one sentence written to scope it out.** The
statement says the admin interface *"is Drupal core"*. An Ágora install's admin interface is
**Gin**, core **`navigation`**, contrib **`coffee`**, and — since D-059 — **the Config Guardian
dashboard**: 12 Twig templates and 3 stylesheets this package chose, installed and configured.

⚠️ **So the answer is not "measure everything". It is: measure what you chose, and say truthfully
what you did not.** What is not legitimate is excluding the one admin surface this product
**sells** — *"the portal audits its own configuration"* is a headline claim and its UI sits
outside both gates.

| | option | cost |
|---|---|---|
| **A** | Prose only: fix the scope sentence, measure nothing | ~2 rows. **Wrong if** the dashboard has violations — the correction then reads as having been written to cover them |
| **B ★** | Add the **Config Guardian dashboard** to `AccessibilityTest` as a separately declared **logged-in** page with its own expectation set; leave the anonymous nine untouched | ~4 rows. The test already knows how and already records why the logged-in scan was dropped. The criterion is *"zero violations in markup this package owns"*, with the three foreign ones enumerated **by selector** and asserted to be exactly those three, **so a fourth fails** |
| **C** | Full admin walkthrough of every route `drupal_cms_admin_ui` installs | 12+ rows, and it makes this project the accessibility auditor of Gin, `navigation` and `coffee` — none of which it can fix |

★ **B.** The only option under which *"the portal audits its own configuration"* and *"we measure
what we ship"* are both true.

⚠️ **The scope sentence is corrected under EVERY option, including A. That half is not a
decision** — it is false as written, and falseness is not a budget question.

⚠️ **And one nearly-free thing with real teeth:** the three criteria axe cannot decide have
**never been walked by a person**. A keyboard transcript over the nine anonymous pages converts
*"a design decision is not a measurement"* into a measurement, and it is the evidence base the
attestation needs. **Recorded as a measurement, never counted as coverage.**

---

## D-062 · Wave numbering — [ejecutor] may sign

✅ **SIGNED by [ejecutor], 2026-09-21, in commit `0966558`, as option A.** The ruling is
`## D-062 · Wave numbers are unit-scoped` in `specs/000-project/DECISIONS.md`; the proposal below
is left as written (rule 8) and is no longer the live document.

| | option | cost |
|---|---|---|
| **A ★** | Unit-scoped wave numbers, as task ids already are | Collisions impossible **by construction**. A heading stops telling you what else ran that week — information this project has used **once** |
| **B** | Keep global numbering and give the counter a home a machine reads | The information survives and collisions become detectable. Cost: a new file or check to maintain, for a counter |

★ **A**, on D-058's own reasoning: **the cheapest fix for a counter nobody owns is not to share
it.** The counter collided **three times in one night** on 2026-09-20. Nothing structural rides on
it, which is why it is delegable. This unit's task file already uses A's shape; if B is ruled, the
headings change and **no id moves**.

---

## D-063 · Does upstream drift get a mechanism, or stay a dated review? — [ejecutor] may sign, **after T-0604**

✅ **SIGNED by [ejecutor], 2026-09-21, in commit `0966558`, as option B** — and the condition was
met first: T-0604 is `✓` in this unit's `tasks.md` and is what produced the +132/−4 and +54/−3 the
option A row quotes. The ruling is `## D-063 · Upstream drift gets a mechanism — option B` in
`specs/000-project/DECISIONS.md`; the proposal below is left as written (rule 8).

| | option | cost |
|---|---|---|
| **A** | Stay a dated review, as D-028 wrote it | ⚠️ **28 days and +132 / +54 lines happened while the detector printed CLEAN**, and the last catch came from a human noticing a 300-line divergence |
| **B ★** | `shared-invariants` gains a source-reading mode printing per-record line deltas | Needs the sibling repository present, so it is a **preflight, not a gate** — and **a preflight nobody runs is the defect one level up** |
| **C** | Make both copies `verbatim` and delete the `adapted` category | The honest end state, and not free: both were adapted for reasons the manifest records |

★ **B, with C as the target.** **Cost of being wrong:** one more script whose denominator has to
be watched — this project's cheapest known failure mode.

---

## D-064 · What does the security-response commitment promise? — 👤 [andres]

| | option | cost |
|---|---|---|
| **A** | Best-effort, no time named | Satisfies the letter of the criterion and tells a reporter nothing |
| **B ★** | A named acknowledgement window and a named route, both chosen so **one maintainer can keep them** | It binds a person. **That is the point** |
| **C** | Point at the Drupal Security Team process | 🔴 **Ágora is not covered by it** until it opts in and has a stable release. **Quoting somebody else's SLA for a project they do not cover is exactly the class of claim this unit exists to remove** |

★ **B.** **Cost of being wrong:** a promise missed in public is worse than a promise not made —
the same asymmetry that decides D-055.

---

## D-065 · Where the WCAG attestation lives — [ejecutor] may sign

✅ **SIGNED by [ejecutor], 2026-09-21, in commit `0966558`, as option A.** The ruling is
`## D-065 · The WCAG attestation is a packaged file at the package root` in
`specs/000-project/DECISIONS.md`; the proposal below is left as written (rule 8).

| | option | cost |
|---|---|---|
| **A ★** | A packaged file at the package root, referenced from `README.md` | It is what a reviewer can open **inside the tarball**, and the one-copy rule can police it |
| **B** | A section of `README.md` | Fewer files; the attestation then shares a file with 39 KB of other prose and **its figures drift with everything else's** |
| **C** | A page on drupal.org only | Invisible to anyone reading the package, **which is where the review happens** |

★ **A.** **Cost of being wrong:** one more root file in a package with 374 entries.

---

## What needs [andres], separated from what does not

| | what | why it cannot be delegated |
|---|---|---|
| 1 | **D-060** — unit 004's fate | It changes what v1 **is**, and it changes packaged text a reviewer reads |
| 2 | **D-061** — the administrative surface and the AA claim | A public accessibility claim on a public body's website |
| 3 | **D-064** — what the response commitment promises | It binds a named person to a named time |
| 4 | **D-045** — carried from T-1205, still `Ruling: B.` with no signature | A product trade-off against D-009 C(2); no pipeline can judge it |
| 5 | **T-0616** — the keyboard walkthrough | A machine cannot press a key and see a focus ring |
| 6 | **Gate B** | Unchanged |

~~**[ejecutor] may sign under standing delegation:** **D-062** now; **D-063** after T-0604 has
measured it; **D-065**; and **the whole of wave 1**, because it is measurement only and its entire
purpose is to put numbers in front of items 1-3.~~

⚠️ **AMENDED 2026-09-21. THE DELEGATION ABOVE WAS EXERCISED, and the struck sentence is the only
place in this file that still read as though it had not been.** It is struck rather than edited
(rule 8): it is a correct record of what was **delegated**, not of what was **done**. All three
decisions were signed in commit `0966558`, and wave 1 closed the same day — five of its seven rows
by [ejecutor], with **T-0603** and **T-0606** left `⏸` on an environment, never on a signature.

⚠️ **The table above never listed D-062, D-063 or D-065, and needed no correction** — this was
checked rather than assumed, because the obvious guess is that a stale file is stale everywhere.
The table's subject is what **cannot** be delegated; those three could be, so they were never in
it. **Its six items stand unchanged, re-derived today:** D-045 still carries `Ruling: B.` with no
signature at `specs/000-project/DECISIONS.md:1810`, and T-0616 is still `⏸ 👤` in this unit's
`tasks.md`. **The staleness was in one sentence, not in the table.**

⚠️ **D-060 is a genuine blocker on this plan's MEANING, not on writing it down.** Without it,
`plan.md` §3's OUT list and T-0620 are conditional. **D-061 changes wave 3's shape** — 2 rows
versus 4 versus 12 — **but not its existence**, because the scope-sentence fix is in every branch.
