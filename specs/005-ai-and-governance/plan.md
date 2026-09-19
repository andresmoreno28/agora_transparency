# Unit 005 · AI and governance — plan

Scaffolded by `orquestador`, 2026-09-19, and written here by [ejecutor]. **Nothing in this file
is signed scope.** The decisions it rests on are in `open-questions.md`; three of them need
[andres].

Measurements behind every claim: `research/2026-09-19-ai-and-governance-state-of-the-art.md`.
⚠️ **Read that file before this one.** Two of its findings close whole architectures.

## §1 · Objective

At the end of unit 005, an installed Ágora **audits its own configuration from the first day with
no key, no provider and no account**, and answers citizens' questions about its published
documents **with citations that are structurally incapable of pointing at something that does not
exist**.

The second clause is the whole unit. Everything else is plumbing.

## §2 · What changed against the ROADMAP, and why

| ROADMAP 005 said | Now | Why |
|---|---|---|
| 1 · *"Recipe `agora_ai` on top of the Drupal CMS AI recipe"* | **Dropped as a vehicle.** | `drupal_cms_ai` places its block in the **admin** theme wired to `drupal_cms_assistant`: a site-building assistant for editors. Building on it inherits eight dependencies to reach something we then switch off. |
| 2 · *"RAG over the document corpus"* | **Retrieval, yes. The word "RAG", no.** | It smuggles in embeddings, and **every vector-database provider on drupal.org has no stable release at all**. Rule 1 closes it. Ágora retrieves with Search API over the database. |
| 3 · *"Mandatory citations"* | **Reframed from a prompt instruction into a rendering guarantee.** | Stock `ai_search` returns a **string**. A prompt asking for citations is a request, not a property. See §4. |
| 4 · *"Graceful degradation without an API key"* | **Promoted from a property to the primary product.** | The keyless site is not a degraded Ágora; it is the Ágora most installs will run. Config Guardian needs no key, retrieval needs no key, only generation does. |
| 6 · *"Config Guardian preconfigured"* | Unchanged, and **the cheapest thing in this unit**. | Zero contrib dependencies, core only, stable and covered. |
| Gate A · *"install smoke without an API key green (blocking)"* | Unchanged and **sharpened**: **no test in this unit may `markTestSkipped` on a missing key.** | This project shipped an inert blocking gate for five days in September and a mirror red for three weeks. A criterion that cannot fail is the thing to avoid here. |
| Gate A · *"axe over the assistant's UI"* | Unchanged, landing in `AccessibilityTest`'s declared page list, **not** a new CI job. | D-053 settled where axe runs. |

## §3 · Scope

### IN

**Governance — needs no key, no network, no ruling. Ships first.**
- `drupal/config_guardian ^1.0` in `require`, with its `DECISIONS.md` line in the same commit.
- Installed and preconfigured in the `governance` area seam of `recipe.yml`.
- An `agora_governance_auditor` role holding `view config snapshots` + `analyze config impact`
  and **none** of the seven `restrict access: true` permissions — so *"the portal audits itself"*
  is something a non-administrator can witness.
- Retention and exclusion settings stated in config **with a comment saying why**, not left at
  module defaults by accident.

**Retrieval — needs no key. Ships second.**
- A Search API index over the six published bundles, on the **database** backend, filtered to
  `status = 1`.
- **The answer surface**: one route that takes a question and returns *sources*. With no key that
  is the whole feature, and it is honest.

**Generation — needs a key, and needs the module ruling.**
- `drupal/ai ^1.4` in `require`. **No provider as a dependency** (D-013 = A, unchanged).
- The citation guarantee of §4.
- The AI disclosure surface: a visible statement that answers are machine-generated, which
  provider is in use, and what is sent where. For an EU public body this is not decoration.

**Gates — every one of them offline.** Three new invariants (§5), one extension of `no-secrets`,
one new page in `AccessibilityTest`, and one unit test that is the differentiator's gate.

### OUT, explicitly

- **Any vector database.** Rule 1, measured. A municipality on shared hosting has none either.
- **`ai_search`** while it declares `lifecycle: experimental` — unless R13 shows experimental
  submodules of a covered release are themselves covered, **and** [andres] signs it.
- **`ai_logging`** — it declares `lifecycle: deprecated`.
- **`ai_agents`, `ai_automators`, `ai_ckeditor`, `ai_content_suggestions`, `ai_translate`** —
  editor-productivity tools. A different product.
- **Any bundled provider**, `ai_provider_amazeeio` included. A free trial with no key is a
  third-party data processor reached from a public body's site: the site owner's choice to make,
  never ours to make silently.
- **A second assistant for editors.** `drupal_cms_ai` exists; a site owner can apply it.
- **FOI and editorial workflow** — unit 004. **Translation of answers** — D-035 = C, English only.

## §4 · What a citation points at, and how fabrication is made impossible

This is the section most likely to be hand-waved, so it is written as mechanics.

### The target: a published entity's canonical URL, and nothing else

| candidate | verdict | why |
|---|---|---|
| A **node** of the six bundles | ✅ **The only sanctioned target** | It has a canonical URL, an access check, a publication status and a title we control. A reviewer dereferences it in one click. |
| A **field** within a node | 🟡 Permitted **only** as a fragment on the node URL, where the field renders a stable `id` | Otherwise the anchor rots on the next display change and the citation silently points at the top of the page. |
| A **page of a PDF** | 🔴 **Forbidden** | We do not extract PDF text, so we cannot verify a page number. **A citation whose precision we cannot check is worse than a coarser one we can.** The PDF is cited as the file attached to a node. |
| A **Views row** | 🔴 **Forbidden** | Not addressable. A row is a rendering of entities that are individually citable. |
| A **taxonomy term / facet** | 🟢 Permitted as a *navigation* offer, rendered as a distinct affordance and **never inside the citation list** | It is a place to look, not a source for a claim. |

### When there is no source, the model is never called

If retrieval returns zero results above threshold, the answer path **short-circuits before any
provider call** and returns a fixed statement — *"I found nothing about that in the published
documents"* — plus the search page. No key is spent, no sentence is generated, nothing can be
fabricated. **This is cheaper, faster and safer than the alternative, which is the only reason it
will survive contact with a deadline.**

### The guarantee: the model is made incapable of emitting a citation

A fabricated citation cannot be detected by inspecting text, so the architecture prevents it:

1. **The model never produces a link.** Provider output passes through a strip pipeline that
   removes every URL, every markdown link and every raw anchor before rendering.
2. **The citation block is rendered server-side from the retrieval result**, by entity ID, through
   `toUrl()`. A citation is a thing the retrieval layer returned this turn, or it does not render.

Two gates, both offline, both with printed denominators:

- **`tests/bin/no-model-authored-links`** — scans every template and render path in the assistant
  surface for a construct that renders provider output as markup. Prints
  `templates examined · render paths examined · sanctioned pipeline uses · findings`, **FATAL at
  zero examined** (I-028: an empty scan reads exactly like a clean one). Watched failing before it
  is trusted.
- **`CitationRenderTest`** — **the differentiator's gate**, no key, no network. A stubbed provider
  returns prose containing three fabricated URLs and one plausible fake node title. Assertions:
  the rendered answer contains **0** anchors; the citation block contains **exactly** the entity
  URLs the stubbed retrieval returned, compared by equality; and with an **empty** retrieval set
  the stub records **0** provider calls. Falsified in both directions: remove the stripping and it
  goes red; remove the short-circuit and the call count goes to 1.

⚠️ **If the module ruling comes back "no", neither gate can exist**, because there is nowhere to
put the code. That is not a detail — **it is the price of the "no" answer, and it must be visible
when the answer is given.** Without them, "citations" means "we ask the model nicely", which is
precisely the claim this project's discipline exists to refuse.

## §5 · The new invariants

Each prints a denominator, each is FATAL at zero, each is watched failing before it is trusted.

**5.1 · `tests/bin/no-key-material`** — scope `config/**`, `recipe.yml`, `content/**`. Prints
files scanned, key entities found, `key_value` assignments, provider key configs (each named),
findings. ⚠️ **Why it is not `no-secrets`:** that script matches a value of real length (I-018),
and **an empty `key_value: ''` is not a secret and IS a defect**. Dirty cases: a real-shaped key;
an empty one; a `key.key.*` file with no `key_value` at all.

**5.2 · `tests/bin/no-experimental-modules`** — resolves every module named in `recipe.yml`'s
`install:` to its `.info.yml` and reads `lifecycle:`. Prints modules examined and lifecycle keys
read — **two numbers, because `N > 0` with `L == 0` is a parser that never ran, not a clean tree**
(the I-028 shape G14 already guards). Closes the gap that `sbom-check` structurally cannot see.

**5.3 · `tests/bin/no-skip-on-missing-key`** — scans tests for skip constructs whose condition
mentions a key, token, provider or env credential. ⚠️ **`S == 0` must be printed, not silently
absent**: a repository with no skips and a parser that found nothing look identical.
**Either a test needs no key, or it is not a test — it is a signed manual protocol.**

**5.4 · Extension of `no-secrets`**, not a new script: teach level 2 the `key_provider_settings`
shape, whose value can legitimately be short and which today's length rule would miss.

## §6 · What can be gated, what cannot, and what is prose

| claim | can a gate hold it offline, with no key? | where |
|---|---|---|
| Clean install with no key and no env vars | ✅ blocking | `Drupal CMS` job + `InstallTest` |
| Zero `key.key.*` and zero `key_value:` in the package | ✅ blocking | `no-key-material` |
| No module in `install:` is experimental or deprecated | ✅ blocking | `no-experimental-modules` |
| No test disables itself on a missing key | ✅ blocking | `no-skip-on-missing-key` |
| **The answer surface renders no link retrieval did not supply** | ✅ blocking, stubbed provider | `CitationRenderTest` |
| **Zero sources → zero provider calls** | ✅ blocking, stubbed provider | same test |
| The index contains only published entities | ✅ blocking | kernel test that also creates an unpublished node and asserts its absence |
| `allow_access_bypass` is never true | ✅ blocking | config invariant with a printed denominator |
| Anonymous, no key → 200 and no exception text | ✅ blocking | functional test |
| The assistant surface passes axe | ✅ blocking | a new page in `AccessibilityTest` |
| The Config Guardian dashboard passes axe | ⚠️ **admin-only, NOT in the anonymous gate** — measured once and recorded as a measurement, never claimed as gate cover | manual, recorded |
| Answers are grounded in the cited passages | 🔴 **no gate can hold it** — it needs a key, a provider and judgement | **signed manual protocol**, Gate B |
| Answer quality, latency, cost | 🔴 **no gate can hold it** | same protocol |
| *"The portal audits itself"* | 📝 prose, **bounded by R16**: if cron does not fire, "scheduled" is false | `README.md` |

⚠️ **The manual protocol is a deliverable, not a shrug.** A written script — five questions,
expected source sets, what a failure looks like — run once with a key, transcript committed with
provider and model named and the key redacted. It closes the honesty gap a CI gate cannot reach.
**It is never counted as automated coverage and no wave closes on it.**

## §7 · Waves

Waves are numbered **globally, continuing at 22**. Task ids are **unit-scoped `T-05NN` and are
not derived from the wave number** — that decoupling is the fix for the counting failure unit 003
documents in its own plan §4.

| wave | name | shape |
|---|---|---|
| **22** | The measurements that decide the architecture | Single lane, `tester`. **No code.** The module and substrate rulings are taken **after** this wave, on its numbers. |
| **23** | The half that needs no key | Two disjoint lanes: governance config, and the three invariants. Files do not overlap. |
| **24** | The retrieval surface and its gates | Sequential after 23. |
| **25** | The generative half | **Conditional on the module ruling.** |
| **26** | Gates, audit, closure | Full gate A with counts, orquestador verdict, Gate B. |

**The `tester` starts in parallel with wave 23 and its first runs will be red** — the invariants
do not exist yet and the recipe has no `governance` block. That is expected, and it is written
here so nobody reads it as a problem.

## §8 · Risks

| risk | sev | mitigation |
|---|---|---|
| A key reaching `config/` via an export from a keyed rig | 🔴 | `no-key-material`, **plus a rig rule: the export rig never receives a real key.** The mechanism is measured, not feared. |
| `ai_search` is `lifecycle: experimental` | 🔴 | R13 first. If it is not covered it is out, and retrieval is Views-based. |
| A gate that skips on a missing key | 🔴 | `no-skip-on-missing-key`. This project has shipped inert gates twice this month. |
| The deep-chat component fails AA | 🟡 | R15 before any commitment. If it fails we do not ship it; the answer surface is a form and a list, which we can make accessible. |
| Cron never fires, so "scheduled snapshots" is false | 🟡 | R16; if true, the README says *on cron* and the dashboard shows when the last snapshot ran. |
| A third-party provider contacted at install time | 🟡 | R12; no provider is a dependency. |
| The module ruling arrives late | 🟡 | Taken at the **start**, after wave 22, on measurements. Wave 25 is the only conditional wave; 22-24 are roughly three quarters of the rows. |

## §9 · Budget, and an honest answer about ceilings

**Unit 003 stands at 73 rows against a ceiling of 34 — plus 39, or 215% of ceiling.**

⚠️ **Is a ceiling overrun by more than 100% a ceiling? No.** It stopped being one the day D-044
removed the gate, and it should stop being described as one. What unit 003 produced is a forecast
wrong by a factor of two **plus an excellent audit trail of why** — a dozen accounting entries,
each honest, each naming what its rows rested on. **The accounting worked. The number did not.**

Why it was wrong generalises: **003's ceiling was set before its open decisions were answered**,
and nearly every answered decision added rows. So for unit 005, change what is counted:

1. **Two ceilings, one per branch of the module ruling** — **16 rows** if no module, **28** if
   there is one. A single number would be wrong under one branch by construction, and stating both
   makes the decision's cost visible **before** it is taken, which is what D-031 wanted.
2. **A per-wave accounting entry**, not a per-overrun one — written while it is cheap.
3. **One number that can genuinely bind: at most 4 rows in this unit may rest on neither D-044
   necessity nor a signature from [andres].** That is the category 003 grew through, and unlike a
   total it cannot be legitimately spent by necessary work. **It is a ceiling because exceeding it
   always means the same thing.**

⚠️ **The predictor to watch is open decisions at scaffolding time.** This unit opens with five,
and one of them moves the row count by twelve. Named here rather than discovered in wave 25.

Count with, never from prose:

```
grep -cE '^\| T-05[0-9]{2} ' specs/005-ai-and-governance/tasks.md
```
