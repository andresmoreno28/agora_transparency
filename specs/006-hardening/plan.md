# Unit 006 · Hardening — plan

Scaffolded by `orquestador` 2026-09-21, written here by [ejecutor]. **Nothing is signed scope.**
Open decisions are in `open-questions.md`; three need [andres] before this plan fully means
anything.

## §1 · What is stale before you read anything else

⚠️ **`specs/000-project/ROADMAP.md` is superseded in three places by this section** (rule 8 — it
is not edited there):

1. **`ROADMAP.md:14-20` says 006 requires units 003, 004 and 005 closed. That is unsatisfiable
   today.** `specs/004-publishing-foi/` holds **one file, a README** — never scaffolded — and
   `recipe.yml` carries `# (empty in v1; filled by unit 004)` in **six places**. Unit 005 is open
   with 17 blocked rows and three unsigned rulings. So scaffolding 006 means auditing a product
   with no editorial workflow, no FOI cycle, and an AI half that may never exist. **That is a
   legitimate thing to audit and it is not what the ROADMAP describes.** See D-060.
2. **`ROADMAP.md:249-252` still reads as a 🔴 blocker that was discharged a month ago.**
   **D-012, signed by [andres] 2026-08-21**, checked the marketplace-fee premise at source and
   found it **false**: free templates are open to any individual, and the fee comes from a
   *proposal* that says *"(none for pilot and MVP)"*. A reader arriving here finds a blocker that
   does not exist.
3. **The ten development points are four-tenths already continuously green** — the SBOM sweep, the
   binding install smoke, the invariant sweep and the public docs — **three stale in wording**, and
   **three genuinely absent**. The a11y point names four WCAG criteria; the product's real gaps are
   a different set, overlapping in **one** item.

**What D-012 confirmed a reviewer actually asks for, and what this unit works backwards from:**
CI installability · SBOM with security coverage · licence manifest · **WCAG attestation** ·
**security response**. The last two do not exist.

## §2 · What hardening means here

The name says security. On a transparency portal for public bodies the binding property is
narrower and harder:

> **Hardening is the unit in which the package stops making claims nothing can check.**

Four properties, each with a live counter-example on disk the day this was written:

1. **Every user-facing claim has a source a stranger can read.** The theme's axe figure is in a
   public log; the template's nine-page figure is in **nothing** — its summary is an assertion
   message, emitted only on failure, and `pages scanned` appears **0 times** in a green trace.
2. **Every gate states its denominator on a GREEN run.** Same counter-example.
3. **Every surface a person uses is measured, or the claim is scoped in writing to the surfaces
   that are.** The admin surface is neither.
4. **Every carried debt has an owner and an expiry.** The two `adapted` shared invariants have
   neither: **28 days and +132 / +54 lines of upstream drift while the detector prints CLEAN**.

⚠️ **Security proper folds under 1 and 3 and is already continuous** — `sbom-check`,
`no-secrets`, `no-key-material`, `no-patches`, `no-unstable-deps`. **The security work actually
missing is a claim about US, not about the code: the response commitment.**

## §3 · Scope

**IN** — the claims layer (one extractor over packaged prose, plus the one-copy rule); the
accessibility completion (the admin ruling applied, the scope sentence made true, the keyboard
transcript, `AccessibilityTest`'s summary made visible and its floor made exact); the two absent
marketplace artefacts; one package-level licence manifest; the shared-invariant drift review D-028
assigns here **plus a mechanism**; the three reachable `NOT CHECKED` items; the final sweep and
audit.

**OUT, explicitly** — unit 004's work (006 audits what ships; narrowing the README is a row, not a
feature) · unit 005's unsigned half · **visual regression**, blocked behind D-045's missing
signature, so 006 carries the row that **reports it absent** · performance beyond measuring
(a number with no threshold is honest; a threshold invented at the end of a project is not) ·
full admin auditing of Gin, `navigation` and `coffee`, none of which this project can fix ·
**any new contrib dependency** — 006 shrinks surface · tags and releases.

## §4 · Seams — which lanes may run at once

| lane | files | repository |
|---|---|---|
| **A · packaged prose** | `README.md`, the accessibility-statement node, `content/MEDIA-LICENCES.md`, `recommended.yml`, `composer.json` description | template |
| **B · tests/bin** | the new extractor, `claims-match-sources*`, both runners' `GATE-CLAIM` lines, `shared-invariants` | template |
| **C · PHP tests** | `AccessibilityTest`, `tests/src/Kernel/*` | template |
| **D · the theme** | `tests/bin/*`, `shared-invariants.manifest` | **`agora_theme`** |
| **E · process** | `specs/006-hardening/*`, `CLAUDE.md`, `DECISIONS.md` | template |

⚠️ **A and E both touch prose and never the same file** — A is *packaged*, E is *process*.
⚠️ **B READS A's files, so B lands after A** or its denominator moves underneath it. That is a
**sequencing** constraint rather than a file conflict, and it is stated because **a shared reader
is the case this project's lane rule does not cover.**
⚠️ **D is a different repository**: one dispatch, one absolute path on its first line.

**The `tester` starts in parallel with B and C, and its first runs will FAIL** — the extractor's
first pass is expected to find claims with no source. **That is the deliverable, not a problem.**

## §5 · Budget, and what a ceiling could mean here

Three units of evidence: 002 ran **40 against 38**; 003 ran **82 against 34**, across seventeen
accounting entries; 005 opened at **32 against 16/28 before a line was written**. And **D-044
already ruled the ceiling does not gate.**

⚠️ **So a total-row ceiling in this project reliably measures one thing: how much was unknown when
the ceiling was set.** Worth recording, useless as a bound, and this plan will not pretend
otherwise by proposing a tighter number.

**What can bind, because exceeding each always means the same thing:**

1. **At most 4 rows resting on neither D-044 necessity nor a signature.** Unit 005 invented this
   instrument and it is the right one — a total can be legitimately spent by necessary work; this
   cannot.
2. **New contrib dependencies: zero.** 006 shrinks surface.
3. **New invariants: 2, named in advance** — `packaged-claims` and the drift mode. A third is a
   conversation, because each permanently costs a `GATE-CLAIM` field, a group and three figures.
4. **A FLOOR, not a ceiling, on what must not be dropped**: the attestation, the response
   commitment, the licence manifest and the README reconciliation. A unit that runs long drops the
   boring rows; naming them as a floor points the constraint the useful way.

**27 known rows · reserve 5 · stated ceiling 32**, as an instrument per D-044.

⚠️ **And the figure that makes this section worth reading: unit 006 opens OVER its ceiling.**
T-1205's audit assigned work here that was then **built in unit 003 as a deliberate pull-forward**
with 006 still carrying the accounting (I-105) — **at least 9 rows**. So 006 opens at roughly
**36 against 32, before anything is planned.**

⚠️ **A second finding falls out of trying to count them: those nine are recorded across five or
six separate prose notes in another unit's task file. There is no single place to read what unit
006 owes.** That is *"a number written twice goes stale in one place first"* applied to the budget
itself, so the closure row derives it **by command** and does not carry the scaffold's figure —
treat 9 as a measured lower bound, not the answer.
