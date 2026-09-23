# Unit 005 · Open decisions

Proposed 2026-09-19. **None is signed.** They are held here rather than in
`specs/000-project/DECISIONS.md` because that file is append-only and signed: a proposal written
into it reads like a ruling. They move there, with their number, when they are signed.

Next free decision number verified on disk: **D-054** (highest present is D-053; what follows are
riders and amendments, no new numbers). Next free idiom: **I-117**.

⚠️ **Take D-054, D-056 and D-057 AFTER wave 22, not before.** Wave 22 is measurement only, and
its numbers move all three. Taking them first is the mistake unit 003 made: its ceiling was set
before its decisions were answered, and nearly every answer added rows.

---

## D-054 · Does unit 005 create the third package? — 👤 [andres]

*Context in one line:* D-051 §5 lists five tenants for a module, signed only the **deferral**, and
says the ruling is taken *"when unit 005 opens."* It has opened.

| | option | cost |
|---|---|---|
| **A** | **No module.** Governance and retrieval ship as configuration; generation is `recommended.yml` plus documentation | **The citation guarantee of plan §4 cannot exist** — there is nowhere to put the strip pipeline or the server-side renderer. So *"an AI assistant with citations"* must be **withdrawn** from ~~`README.md:33`~~ `README.md:44` *(line moved 2026-09-21 by `7ab5546`'s split of the Planned table; cited by line, so it went stale by being right)*, from `AGENTS.md` and from the project description. The product keeps one differentiator, not two. ~16 rows |
| **B ★** | **Yes, one module, scoped to this unit's two tenants** — the retrieval-and-citation layer and the key screen — leaving D-051's other four undecided | A third public project: page, releases, security coverage, SBOM line, CI pipeline. **D-051 §6 prices this from measured experience, not estimate:** publishing the *second* package produced two real failures in two days. ~28 rows |
| **C** | **Yes, the full D-051 §5 module**, all five tenants plus D-052's route alter | B's cost plus scope nobody has researched, against a unit already carrying eighteen research questions |

★ **B.** It is the only option under which the differentiator is a **property** rather than a
**request**, and it buys D-052's fix for free.

**What it costs to be wrong:** wrong about B → a third project exists carrying perhaps 400 lines,
and D-051 §6's costs are real. Wrong about A → the claim is withdrawn publicly, and re-adding it
later means creating the project anyway, **after the README has already said we do not do this.**

---

## D-055 · What a citation points at — 👤 [andres]

*Context in one line:* "citations" is undefined, and a marketplace reviewer can test it with one
question.

| | option | note |
|---|---|---|
| **A ★** | **A published entity's canonical URL and nothing else** — field-level fragments only where the field renders a stable `id`; PDF page numbers and Views rows forbidden | plan §4 carries the table and the reasoning |
| **B** | Entity URL **plus the passage quoted** | Strictly better and self-verifying on the page. **Not free:** the passage must come from the retrieval layer and never from the model, and R9 decides whether it can |
| **C** | Whatever the model emits, validated afterwards against the corpus | **Discard.** Validation-after-the-fact is a filter, and a filter has a false-negative rate; plan §4's construction has none |

★ **A now, B if R9 shows the retrieval layer returns usable text.**

**What it costs to be wrong:** wrong about A → citations are coarser than they could be and a
reader opens a document to find the line. Wrong about C → **the portal of a public body prints a
link to a document that does not say what the page claims it says.** Those two errors are not the
same size, and that asymmetry is the whole reason A is the recommendation.

---

## D-056 · The retrieval substrate — [ejecutor] may sign, but only after T-0502

*Context in one line:* the stock RAG action needs a `search_api_index`; the corpus is already
browsable through Views exposed filters that ship today.

| | option | cost |
|---|---|---|
| **A** | **Core Views only** | Zero new dependencies; the keyless answer surface is a filtered list. **`RagAction` cannot be used at all** — it loads `search_api_index` entities and nothing else — so the generative half needs its own retrieval code |
| **B ★** | **`search_api` + `search_api_db`** (`8.x-1.41`, stable, covered, one project) | Relevance ranking, and it is the substrate the stock action expects. One SBOM line, an index to keep in sync, **and the indexing filter becomes a security boundary** |
| **C** | `search_api` + a vector backend | 🔴 **Discard on rule 1.** Measured 2026-09-19: every VDB provider on drupal.org has **no stable release at all** |

★ **B, conditional on T-0502.** If `RagAction` breaks over `search_api_db`, B's main argument
evaporates and A becomes the honest choice.

**What it costs to be wrong:** wrong about B → one dependency carried for ranking Views could have
done. Wrong about **the filter inside B** → a disclosure incident on a transparency portal, which
is why T-0516's criterion asserts an unpublished node is *absent* rather than merely that
published ones are present.

**Why [ejecutor] may sign it:** it is a technical consequence of a measurement, not a product
choice. If R9 forces option A, the decision has effectively been taken by the code.

---

## D-057 · The shape of the AI dependency — 👤 [andres]

*Context in one line:* D-013 settled **which provider** (none, plus recommendations). It did not
settle whether `drupal/ai` itself is a hard dependency.

| | option | cost |
|---|---|---|
| **A** | **`drupal/ai ^1.4` in `require`** | The assistant is in the box and a keyless install gets it switched off. It pulls four transitive packages into **every** Ágora install, including ones that will never use AI |
| **B ★** | **`drupal/ai` in `require`, `ai_search` NOT in `install:`, no provider anywhere** | The difference from A is one submodule line: take the project, decline the experimental submodule, and let `no-experimental-modules` make that permanent rather than a habit |
| **C** | **Nothing in `require`**; everything through `recommended.yml` | Smallest SBOM. But `recommended.yml` needs `project_browser`, which is **beta** — so the recommendation mechanism is itself unstable, and **a `recommended.yml` nothing can read is documentation wearing a config file's clothes** |

★ **B, pending R13.** If experimental submodules turn out to sit outside the security policy, B is
also the only option that keeps `sbom-check`'s promise literally true.

**Rule 2 makes this [andres]'s**: every contrib module needs a line in `DECISIONS.md`, and this
one brings four transitive packages.

---

## D-058 · Unit 005 task ids: `T-05NN`, decoupled from wave numbers — [ejecutor] may sign

*Context in one line:* the documented convention is `T-<wave><nn>` with global waves; unit 003 ran
to wave 21 and its counting regex broke on exactly that coupling.

- **A ★** · Unit-scoped `T-05NN`, waves numbered globally from 22, the two **explicitly not
  derived** from each other. **Verified compatible on disk today**: `cited-tasks-exist` uses a
  greedy `T-[0-9]{3,4}`, so `T-0501` matches whole rather than as `T-050`, and its definition
  pattern accepts the row shape. The count regex is bounded at **both** ends, which is the defect
  unit 003 documents in its own amendment.
- **B** · Keep `T-22NN`. Consistent with units 001-003. **Cost:** if 005 outgrows its planned
  waves the way 003 did, the ids scatter and the counting regex needs the same surgery again.

★ **A.** Cheap, mechanically verified, and it removes a failure this project has already paid for
twice. **Nothing structural rides on it** — being wrong costs a cosmetic inconsistency between
units — which is why it can be signed under standing delegation.

---

## What needs [andres], separated from what does not

| | what | why it cannot be delegated |
|---|---|---|
| 1 | **D-054** — the third package | D-051 §5 says in as many words that nothing in it is a signature for the module, and §6 prices a third project. It changes the SBOM, the release surface and the unit's size by twelve rows |
| 2 | **D-055** — what a citation points at | It is the product's public claim on a public body's website |
| 3 | **D-057** — whether `drupal/ai` enters `require` | Rule 2, and four transitive packages |
| 4 | **Gate B** — that a keyless install behaves well, and that with a key the citations are correct | **He is the only one who can run T-0529 with a real key**, because a real key must never reach any rig this project builds |
| 5 | **Any rule-1 exception** — `ai_search` while experimental, or `project_browser` at beta | Rule 1 is non-negotiable; only its author bends it, and CLAUDE.md's own amendment note makes clear the rule stands on our merits rather than somebody else's requirement |

**[ejecutor] may sign under standing delegation:** D-058 now; D-056 once T-0502 has measured it;
and **wave 22 in full**, because it is measurement only and its entire purpose is to put numbers
in front of decisions 1-3.

---

## Amendment · what wave 22 measured, and what it removes from [andres]'s list — 2026-09-20

Measurements: `research/2026-09-20-wave-22-measurements.md`. Read it before these options; three
of them are repriced and one **signature item disappears entirely**.

### 🔴 `project_browser` is NOT beta, and D-057 option C was mispriced on that error

**Measured at source 2026-09-20**: `project_browser` has **33 releases, newest `2.1.4`, newest
STABLE `2.1.4`, `security covered="1"`**, six stable releases deep — and **both published site
templates require it at `^2.1.3`**. The claim that it is `^2.1-beta3` came from the starter kit
and was never re-read.

**Consequences, and the second is the one that matters to [andres]:**
1. **Option C's only stated cost is gone.** It was *"the recommendation mechanism is itself
   unstable"*. It is not. With that removed, C is **what the ecosystem actually does, at zero SBOM
   growth**.
2. ⚠️ **Item 5 of the signature list above names two rule-1 exception candidates. `project_browser`
   is NOT one of them — no signature is needed for it.** `ai_search` still is, and the audit
   sharpens what that exception would mean: it is the gateway to a backend requiring an alpha or
   beta vector-database provider, so **an exception for `ai_search` is an exception for the whole
   unstable chain**, not for one module.

### D-054 — the middle path is gone, and that is the real change

The recommendation stays **B**, but its argument is now harder and its alternative is now better
evidenced, which is worth stating in the same breath.

**Harder, for B:** `RagAction` **cannot work over `search_api_db`** — read at source, and it fails
in two different ways. `search_api` 8.x-1.41 contains `drupal_entity_id` **zero times** and never
sets an item key named `content`; the sole producer of both is `ai_search`'s own backend, which
requires a VDB provider, and **every VDB provider has no stable release at all**. ⚠️ **The two
modes fail differently and the silent one is the dangerous one**: `rendered` raises, while
**`chunks` fails silently** — it appends separators and zero retrieved text and hands that to the
model as context. **That is the fabrication path plan §4 exists to prevent, arriving by default.**
So option A is no longer *"lose a differentiator"*; it is **the option under which nothing in the
box prevents an ungrounded answer.**

**Better, for A:** both published site templates ship **no AI configuration at all** — 0 of 613
and 0 of 559 config objects. That is the strongest evidence A has ever had, and it is precedent
rather than permission.

⚠️ **What the measurements remove is the middle path.** *"Use the stock RAG action and skip the
module"* is not available. The choice is between **normal** and **differentiated**, and it stays
[andres]'s.

### D-056 — moves substantially, and is now coupled to D-054

The recommendation was *"B, conditional on T-0502 — if `RagAction` breaks over `search_api_db`,
B's main argument evaporates."* **At source it breaks.** B does not fall, because a better
argument arrived: both published templates ship `search_api` + `search_api_db` with an enabled
index, and `haven`'s enables **`entity_status`** — the stock mechanism for T-0516's published-only
boundary — and **`rendered_item`**, which is what makes D-055's option B reachable **from
retrieval rather than from the model**. So A and B now cost the same in retrieval code, and B buys
ranking, a stock filter and precedent for one SBOM line.

⚠️ **But D-056 is now coupled to D-054: with no module there is nowhere to put retrieval code
either.** And ⚠️ **do not sign D-056 until a rig confirms the predicted failure** — the whole
argument rests on a prediction a run can falsify, and this project has been wrong twice about
exactly this kind of reasoning-from-source.

### The lifecycle audit is four times worse than yesterday's note, and it was a denominator problem

**8 of `drupal/ai` 1.4.9's 16 shipping extensions are non-stable — exactly half** (7 deprecated,
1 experimental), over **47 `.info.yml` examined, 0 unreadable, 9 carrying a `lifecycle` key**.
Yesterday's note said two. ⚠️ **The other six were not wrong, they were UNLOOKED-FOR: a targeted
read cannot produce a denominator**, which is this project's own I-045 arriving in a new place.

**R13 is an ABSENCE and it was not upgraded into an answer.** The contrib security-advisory policy
contains the word *"experimental"* **zero times** — its unit of coverage is the **release**. Core's
experimental policy does state an inheritance principle, but it is about core and sits under a
heading with no counterpart in the four-valued contrib `lifecycle` key. Both quoted with URLs in
the measurements file. **"I could not find a clause" is the finding.**

⚠️ **And T-0513's scope was wrong in the scaffold: `lifecycle` has a FOURTH value, `obsolete`.**
Ágora installs 32 contrib projects today; **2 non-stable exist in the tree** —
`automatic_updates_extensions` (**obsolete**) and `eca_node_access` (experimental) — and
**neither is in any `install:` list**, so the invariant returns 0 today as a measurement rather
than by construction.
