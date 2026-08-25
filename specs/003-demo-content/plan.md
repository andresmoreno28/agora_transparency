# Unit 003 · Demo content — the portal starts telling a true story

> Scaffolded [ejecutor] 2026-08-26, against
> `research/2026-08-26-demo-content-mechanics.md`.
> ⚠️ **Unit 002 is NOT closed.** 37 of 40 rows signed; T-804 deferred here, T-806 (the audit) and
> T-807 (closure) open. §7 states exactly which rows of this unit depend on 002 closing and which
> do not. This plan diverges from the ROADMAP where the research says it must; every divergence is
> named in §2.

## 1 · Objective

At the end of unit 003, someone who installs Ágora on a clean Drupal CMS lands on a portal that
**already publishes something**, and every claim the portal makes about itself is measured:

- Six populated bundles, sized so that **every table has rows before any test asserts anything
  about a table** — because Views renders no `<table>` at all for an empty result set (I-062), and
  the entire content model shipped in unit 002 is, on an empty site, invisible to axe, to Playwright
  and to a human.
- An **org chart with names and salaries that are provably fictional**, checked by a script rather
  than promised in a sentence.
- **Media whose licence is nameable**, checked the same way.
- A page that is **worth showing to a person** — which is the first time in this project that has
  been true, and it is the deliverable [andres] has actually asked for.

And one thing that does not exist anywhere in the project today: **an accessibility result about the
product.** The theme's blocking `nightwatch` job reports `4 pages scanned, 89-89 rules per page,
0 violations` — over four synthetic fixtures that supply their own `<h1>` and render `menu`,
`node` and `field.html.twig` **not at all**. That green is true and it is about scaffolding.
Unit 003's real product is the same sentence said about real pages.

## 2 · What changed against the ROADMAP

The ROADMAP's unit 003 was written 2026-08-20, before the content model existed, before D-033 put
config strings in English, and before core's content importer had been read at source.

| ROADMAP said | Now | Why |
|---|---|---|
| "ES/EN translations of all of the above" (point 7) | **Content is bilingual; the chrome is English and cannot be otherwise.** Opened as **D-035**, not decided here | D-033 puts shipped config strings in English and records that four locks stop the Spanish translation reaching an installed site. The result is a half-Spanish page, and that is a **WCAG 3.1.2** obligation, not a taste question (research §5) |
| "correct `lang` per fragment" — one line | **Theme work in `agora_theme`, on every config-sourced label**, and it is not free | Same. No template in `agora-theme/templates/` emits a `lang` attribute today |
| "Budgets and contracts: lightweight visualization" | **The accessible table is the deliverable; a chart is an open question (D-037) and needs an SBOM line if it needs a module** | D-026 already ruled the table is the source of truth. Rule 2 forbids adding a dependency without a `DECISIONS.md` line |
| "Accessibility statement pre-built" (point 6) | **Contradicted by `specs/002-base-and-theme/plan.md:94`, which assigns it to unit 006.** Planned here, flagged for a one-word ruling | The 002 plan is signed scope and later-dated; the ROADMAP declares itself direction. The statement's *content* is this unit's axe numbers, which is the argument for keeping it here |
| "Demo media with documented licenses" (point 8) | Unchanged, and **the precedent is worse than expected**: neither published site template documents any media licence at all | `haven` ships 22 Unsplash-named JPEGs and one GPL `LICENSE.txt`. Measured (research §7) |
| Gate A: "Playwright functional + visual" | **Not a gate.** D-009(d): *"Visual regression is INFORMATIVE by definition: no task may reference it as a gate"* | The ROADMAP predates D-009's signature. T-804's prerequisite ([andres] creates the mirror) is named on its row, never assumed |
| Gate A: "axe with no violations on all the demo pages" | **Has no surface today.** D-027 proved Nightwatch cannot be collected in the template repository. Opened as **D-036** | A recipe project installs outside the docroot; Nightwatch's glob is rooted at the docroot |
| Nothing about the content producer | `drush content:export --with-dependencies --dir=…` is the producer, and it is **not** `site:export` | Read at source. D-032's hazards do not apply to it; D-032 step 7's `content == 1` check does, and this unit retires it by name |

## 3 · Scope

### YES — in this unit

**Template repository (`drupal/agora_transparency`)**
- **A populated corpus across all six bundles**, sized by falsifiable criteria rather than taste:
  every view reaches `rows >= 3`; the document library exceeds **25 rows so its pager actually
  paginates** (`config/views.view.agora_base_library.yml`, `pager: full`, `items_per_page: 25`);
  contracts span at least three distinct `procedure_type` values with at least one counterparty
  holding two, so the **art. 8.1.a) derived statistic is non-degenerate**.
- **The art. 8.1.a) derived statistic**, carried explicitly from unit 002
  (`specs/002-base-and-theme/plan.md:52-57`): a **legal requirement**, named there as unit 003's.
- **Taxonomy terms** for the five vocabularies, English labels per D-033, with ES translations.
- **Media** — images and document files — each with a manifest line and a nameable licence.
- **Multilingual configuration**: `language` + `content_translation` in `install:`, the `es`
  language, negotiation, per-bundle content-translation settings, `translatable` on prose fields.
  All **through the D-032 export rig**, never by hand.
- **The home page**, the institution page, the document library, the budgets-and-contracts page and
  the open-data page — as Canvas landing pages in `content/canvas_page/` plus the views that exist.
- **Open-data distributions** (CSV/JSON) that actually download, with a record page each.
- **`screenshot.webp`** — the real thing, replacing the 6,686-byte placeholder
  (`specs/002-base-and-theme/plan.md:97` assigns this here by name).
- **Three new invariants**: `media-licence`, `no-real-people`, and a binary-metadata sweep.
- **The visible preview** on `https://agora-smoke.ddev.site`, from a rebuilt rig.

**Theme repository (`drupal/agora_theme`)** — the three debts unit 002 recorded as owned by 003
(`specs/002-base-and-theme/tasks.md:816-819`):
- **Default block placements.** The theme ships **no `block.block.*` config at all** — verified:
  `agora-theme/` has no `config/` directory. On a real page there is no page title, no `<h1>`, no
  menu. Every axe fixture supplies its own `<h1>`, which papers over a genuine defect.
- **Pager heading level.** Core defaults `#pagination_heading_level` to `h4`; the template correctly
  prints what the caller sets and **nothing makes the caller choose**; `heading-order` fired during
  development.
- **Rendering a Dataset's CSV distribution as an accessible `<table>`** — D-026 calls that table the
  source of truth the charts read.

### NO — explicitly not this unit

This list is normative. If something below appears mid-wave, it does **not** get done here.

1. **Editorial workflow, moderation, ECA, Webform, FOI.** → unit 004. Content is authored published;
   no transitions.
2. **Anything AI, and Config Guardian.** → unit 005. The AI assistant needs this corpus and gets it
   from here; it is not built here.
3. **The WCAG attestation, the full keyboard walkthrough, the final SBOM sweep, the
   security-response SLA, performance work.** → unit 006.
4. **Multilingual beyond ES/EN.** Master plan §5, explicit.
5. **New content types, new fields, new vocabularies.** D-026 is signed. If demo content wants a
   field that does not exist, that is a **finding about D-026**, escalated — never a quiet addition.
6. **Pinning, patching, or any unstable dependency.** Rules 1 and 2 stand; D-037 is the only place a
   new package may even be proposed, and it is a decision, not a task.
7. **The marketplace question.** → unit 007-bis, per D-012=C.
8. **`recommended.yml`** is not touched.
9. **Touching `~/agora-smoke` or `~/agora-cms` as a working copy.** They are rigs. They are rebuilt
   or pulled; they are never edited and never committed from.

### The scope gate

**(a) The admission test.** Inherited verbatim from unit 002 §3. A candidate task enters only if all
four are YES: does gate A of *this unit* go red without it · does it touch a file this unit already
owns · is it required for unit 004 or 005 to **start** · can it finish inside the current wave
without opening a new D-NNN. There is no "it's small" exemption.

**(b) The budget, and it is a number produced by a command.** D-031's method fix is binding:
*"every count in `tasks.md` and `plan.md` is now a **quoted command with its output**, never a
number in prose."*

```
grep -cE '^\| T-(9[0-9]{2}|1[0-2][0-9]{2}) ' specs/003-demo-content/tasks.md
```

**Known rows: 30. Budget: 34. Headroom: +4.**

The reserve is sized to named risks, not chosen round: **two unsigned decisions that can each add
rows** (D-035's answer determines whether the theme grows `lang`-attribute work across seven
templates; D-010's answer determines the corpus size), **one prerequisite outside our control**
(the GitHub mirror, T-1202), and **one divergence awaiting a ruling** (the accessibility statement).
Four risks, four reserve rows. Crossing **34** costs a rider signed by [andres] naming what it
displaces — unit 002 ran at headroom **−2** and D-031 records what that cost.

**(c) The audit hook.** Every wave verdict reports the count against 34 in the same breath as the
test counts. A unit that has quietly grown 40% is a finding at 🟡.

## 4 · Task numbering — a signed convention reaches its predicted expiry

`specs/002-base-and-theme/plan.md:137` predicted it: *"Known expiry: from global wave 10 the numbers
go four-digit."* Unit 003 is waves **9-12**, so its ids are `T-901…` then `T-1001…`, `T-1101…`,
`T-1201…`.

⚠️ **The hazard is the counting regex, not the ids.** `T-[0-9][0-9][0-9]` matches the first three
digits of `T-1001`. Every count in this unit uses the anchored, bounded form above, which is verified
not to match any unit-001 or unit-002 id. This is stated here because D-031 exists precisely because
a scope gate whose content is a number failed on a number nobody ran.

## 5 · Waves

Four waves. Wave 9 buys the right to write content; waves 10 and 11 write it; wave 12 proves it.

### Wave 9 · The mechanisms are proven before a single node is authored
**Sequential, single lane, template repository plus the rig.** Nothing lands in `content/`.

The wave exists because **three of this unit's load-bearing mechanisms fail silently**, and each
one would produce a green gate over an empty result:

1. A translation for a language the site has not configured is dropped with no error (research §4.1).
2. A `file` entity whose binary is missing logs a warning and continues (research §2).
3. An empty Views page renders no `<table>`, and axe reports 0 violations about nothing (I-062).

Each gets a probe **with its dirty case observed**, not reasoned about — I-066, recorded today:
*"the step an implementer flags as 'reasoned, not run' is the step that breaks."*

**Gate A(9)**
- Every probe quotes **both** outcomes: the clean run and the deliberately-broken run.
- The two new invariants each print `N … 0 findings` with `N > 0` asserted, and each has a dirty
  case that was **watched failing** before the invariant was trusted (the T-508 standard).
- `tests/bin/gate-a-wave1.sh` and `gate-a-wave3.sh` still at **61 · 0** and **37 · 0** or higher,
  with the numbers re-quoted rather than carried.

### Wave 10 · The corpus — two lanes, disjoint by repository
**Lane A** touches only `agora_transparency`: `config/`, `content/`, `recipe.yml`.
**Lane B** touches only `agora_theme`: `templates/`, `css/`, a new `config/optional/`,
`tests/src/Nightwatch/`. Zero shared files. Neither lane blocks the other.

⚠️ **Lane A is internally SEQUENTIAL even though its files are disjoint**, and this is the
non-obvious lane call of the unit: every row in it goes through **one export rig**, which is a
single mutable resource. Two implementers modelling in the same rig produce a baseline diff that
belongs to neither of them. Disjoint files are not disjoint work when the tool is shared.

The `tester` starts **in parallel with both lanes**. Its initial failures are expected and are not a
problem: an assertion that a view has 26 rows is supposed to fail until the 26th node exists. Say so
here so that nobody reads a red wave-10 morning as a regression.

**Gate A(10)**
- Every view reports its row count **before** any markup assertion (I-062, non-negotiable).
- `config-inventory` reports its new file count; the rise from **102** is stated, not implied.
- `media-licence` reports `N binaries · N manifest rows · 0 unlicensed`, `N > 0`.
- `no-real-people` reports `N files · R roster rows · D deny-list terms · 0 findings`.
- `du -sh content` is quoted against the stated ceiling.
- Theme: `nightwatch` axe over fixtures that **no longer supply their own `<h1>`**, page count and
  rules-run count stated, 0 violations; `stylelint` and `twig-cs-fixer` still blocking and green.

### Wave 11 · The pages, and the first thing worth looking at
**Sequential.** Depends on wave 10 green in both repositories.

This is where the visible preview lands, and it lands **last in the wave, not first**, for a reason
that is mechanical rather than cautious: before the home page and the theme's block placements
exist, a preview shows a site with no `<h1>`, no navigation and — on any view whose bundle is still
empty — no table at all. Showing that is showing a defect, not a product.

**Gate A(11)**
- Every demo page returns its expected status **and** a rendered marker (the T-801 standard).
- Each accessible table asserts, in order: `rows >= 3` → `<caption>` present → `<th scope>` on every
  header cell → the theme's scroll wrapper present.
- The derived statistic is asserted against a count computed independently in the test, **never
  against itself**.
- The preview row prints the **resolved** `drupal/agora_theme` version and states whether it came
  from `packages.drupal.org` or from a path repository. A path repository proves an installation the
  end user cannot perform (I-048) and makes the preview inadmissible as evidence.

### Wave 12 · Gates, closure, verdict
**Sequential.**

**Gate A(12)**
- Both repositories' job lists read from the API and printed side by side; `jobs >= 9` on each; the
  floor restated if it moved. **Derived lists are forbidden** — the table is replaced only by
  another observation, in the commit that makes it true.
- axe over the demo pages: `P` pages with `P >= 6`, the rules-run count stated **per page**, and the
  counts compared across pages — a page reporting fewer rules run than its neighbours is a page
  where something did not load, and it reads exactly like a pass.
- Every invariant quotes its denominator (I-045). `no-code-in-template` re-measures the packaged
  entry count, which was **116** at T-703 and is now stale by every content file and binary.
- `orquestador` audit with no open 🔴, and the task count reported against **34** by the §4 command.

## 6 · The two new invariants — contracts, in house style

Both follow the shape every `tests/bin/` script already uses: a printed scope, a denominator that
must be `> 0` or the script fails, findings as `path:line reason`, exit 0 iff findings == 0, and a
**FORBIDDEN** clause naming the remedy that is not allowed.

### 6.1 · `tests/bin/media-licence`

```
scope:      every file under content/ that is NOT *.yml and NOT *.json, in BOTH scopes —
            the working tree (find, because git grep never sees an uncommitted offender, I-019)
            and the packaged tree (git archive --format=tar HEAD | tar -tf -, because what a
            marketplace reviewer downloads is the tarball, not the checkout).
            The two-scope pattern is no-code-in-template's, deliberately.
manifest:   content/MEDIA-LICENCES.md — one row per binary, six columns:
            path | title | author | source URL | SPDX id or "own work" | date retrieved
allow-list: CC0-1.0 · CC-BY-4.0 · CC-BY-SA-4.0 · OFL-1.1 · own work
            The list lives in the script and is PRINTED on every run. A licence not on it is a
            finding, never a silent pass — an unrecognised string must not read as absence.
scanned:    N binaries; N must be > 0 or this script FAILS. A scan of zero files exits 0 exactly
            like a clean tree (I-028), and this repository has been caught by that twice.
findings:   a binary with no manifest row · a manifest row naming no existing binary ·
            a licence field not on the allow-list · an empty author, source or date field ·
            a binary present in one scope and absent from the other
prints:     "N binaries (working) · M binaries (packaged) · K manifest rows · L allow-listed
             licences · F findings"
exit:       0 iff F == 0
dirty case: (a) drop an unlisted 1-byte .jpg into content/file/  -> "1 findings", exit 1
            (b) delete the manifest row of a present binary       -> "1 findings", exit 1
            (c) set a row's licence to "All rights reserved"      -> "1 findings", exit 1
            All three must be WATCHED FAILING before the invariant is trusted (the T-508 standard:
            "a copied invariant is never edited until it passes").
FORBIDDEN:  adding a path to an ignore list. A false positive is fixed by fixing the MANIFEST or
            by removing the file — never by shrinking the scope. This clause exists because the
            only route from a red deny-list back to green is deleting a term, and that is the move
            no-boilerplate's own FORBIDDEN clause was written to stop (T-703).
```

### 6.2 · `tests/bin/no-real-people`

**Its limit is stated first, because a check that claims more than it does is worse than none.**
This invariant **cannot prove a person is fictional.** Nothing can. It proves three narrower
things, each falsifiable, and the honesty about that is the point:

```
(1) ROSTER COMPLETENESS. Every human name appearing anywhere in content/ is declared in
    content/PEOPLE.md, a roster whose every row carries the word "fictional" and a one-line
    provenance ("surnames drawn from the INE frequency table", "invented", …). A name in the
    content that is not in the roster is a finding. This is what catches a real name pasted in
    during authoring — which is the actual failure mode, not a random collision.

(2) DENY-LIST. No name in content/ matches a term in a deny-list built from the real office-holders
    of the municipalities the demo is modelled on. Named explicitly, because the realistic accident
    is somebody copying a real town hall's org chart "as a template" and forgetting.

(3) SHAPES THAT ARE PERSONAL DATA REGARDLESS OF THE NAME, matched by pattern in every YAML/JSON
    file under content/:
      - Spanish DNI / NIE                - IBAN
      - a phone number in E.164 or Spanish national form
      - an email address at a real municipal domain (.gob.es, or an .es host that resolves)
      - a postal address carrying a street number
    A fictional person with a real IBAN is a data leak with a made-up name on it.

scope:      every *.yml and *.json under content/, BOTH scopes, as media-licence
scanned:    N files; N must be > 0 or FAIL
prints:     "N files · R roster rows · D deny-list terms · S shape patterns · F findings"
exit:       0 iff F == 0
dirty case: (a) a node field holding a DNI-shaped string        -> exit 1
            (b) a person node whose name is absent from PEOPLE.md -> exit 1
            (c) a mailto: at a .gob.es host                       -> exit 1
NOTE on remuneration: the org chart ships salary and severance figures. Those are fictional numbers
    about fictional people and are NOT personal data. They become personal data only when attached
    to a real person, which is exactly what (1) and (2) exist to prevent. Written down so nobody
    later "hardens" this invariant by deleting the numbers, which would delete the product.
FORBIDDEN:  as media-licence. The roster is corrected; the scope is not.
```

### 6.3 · The binary-metadata sweep — an extension of `no-secrets`, not a new script

`tests/bin/no-secrets` excludes every image in `content/` **by design**: its `is_text()` guard
(lines 118-130) uses `grep -I`, so binaries are skipped. That was correct when `content/` held one
YAML file. It stops being correct the moment the package ships photographs, because **EXIF and XMP
metadata carry GPS coordinates, camera serial numbers and author names** — personal data in a file
the secrets invariant is structurally blind to.

```
scope:      the same binaries media-licence scans
method:     byte-level, tool-free — assert the absence of the APP1/EXIF marker (Exif\x00\x00) and
            of the XMP packet header (http://ns.adobe.com/xap/1.0/) in each file.
            NO NEW TOOL. doctor certifies grep/python3 on this host; adding an external EXIF tool would move the
            toolchain floor (T-803) for one check, and the floor is a certified surface.
prints:     "N binaries opened · M carrying EXIF · K carrying XMP · F findings"
exit:       0 iff F == 0
dirty case: an unstripped camera JPEG -> exit 1, naming the marker and the offset
```

## 7 · What genuinely depends on unit 002 closing — and what does not

Asked directly in the dispatch, answered row by row so nobody has to infer it.

| Depends on 002 closing (T-806 audit + T-807 + `agora_theme` 1.0.1) | Why |
|---|---|
| **T-1107** the visible preview | It resolves `drupal/agora_theme` from `packages.drupal.org`, which today gives `1.0.0` — the release in which `templates/views-view-table.html.twig` **does not exist** (observed in the `Drupal CMS` job log, `CLAUDE.md:260-266`). A preview on `1.0.0` shows [andres] the defect the fix already repairs |
| **T-1103** budgets/contracts page | Same: its centrepiece is a Views table |
| **T-1201** axe over demo pages | Scanning a table whose theme override is absent measures the wrong theme |
| **T-1204** gate-table refresh | It must record the resolved theme version, which changes when 1.0.1 ships |

| Does **not** depend on it | Why |
|---|---|
| **All of wave 9** (T-901…T-906) | Probes and invariants. They test core's importer and our own scripts; the theme is irrelevant to every one |
| **T-1001…T-1008**, all of lane A | Authoring content and exporting config. The Views *template* affects rendering, not the data |
| **T-1009…T-1011**, all of lane B | They are work **in** `agora_theme`, on its `1.x` branch, which is already two commits ahead of `1.0.0`. They are part of what `1.0.1` — or `1.1.0` — will contain |
| **T-1101, T-1102, T-1104, T-1105, T-1106** | Pages, downloads, statement, screenshot. None is a Views table |

**So: unit 003 can be planned in full, and 22 of its 30 rows are executable today.** The dependency is real but
it is concentrated in wave 11's tail and wave 12, which is where an ordering constraint costs least
— the same shape D-025 chose deliberately for the theme swap.

⚠️ **One dependency that is NOT about the release.** T-806 is unit 002's audit and it has not run.
Unit 003 must not **close** on top of an unaudited unit 002; starting on top of one is fine. Stated
as a hard ordering constraint on **T-1205**, not as a vague concern.

## 8 · Accessibility — what changes when the fixtures become pages

Asked directly. Five things change, and the fifth is the one that will be missed.

1. **Rules that never fire on a fixture start firing.** A fixture page has no landmarks, one
   hand-written `<h1>` and no navigation, so `region`, `landmark-one-main`, `bypass`,
   `page-has-heading-one`, `link-in-text-block`, `td-headers-attr` and `scope-attr-valid` are
   either skipped or trivially satisfied. On a real page every one of them is live. The theme's
   current `89-89 rules run per page` is a number about fixtures; expect it to move, and expect the
   move itself to be informative.

2. **The empty-page trap becomes the dominant failure mode.** I-062, recorded today: with every node
   unpublished, a Views page still returns 200, the wrapper is still in the DOM, and `<table>`,
   `<caption>` and the scroll wrapper are **all zero** — and axe reports no violations, *truthfully,
   and about nothing.* **Every axe assertion in this unit is preceded by a denominator assertion**:
   `rows >= 3`, `tables >= 1`, `h1 == 1`. This is not a style rule; it is the difference between a
   gate and a badge.

3. **The rules-run count must be quoted per page and compared across pages** (I-045). A green over
   an unknown denominator is not a stronger result than a permissive one, it is a more confident one.

4. **The surface is undecided.** D-027 proved Nightwatch **cannot be collected** in the template
   repository, and the demo pages live in the template. So *"axe over the demo pages"* has no home
   today. That is **D-036**, framed for [andres]; it is not a task and must not be written as one.

5. **New WCAG 2.2 AA criteria enter scope that no fixture could have exercised, and axe checks only
   some of them.** Naming them here is what gives unit 006 something to walk:

   | Criterion | Enters because | axe? |
   |---|---|---|
   | **3.1.2 Language of Parts** | English labels on a Spanish page (research §5) | partially — `valid-lang` only |
   | **2.4.1 Bypass Blocks** | a real menu exists for the first time | yes — `bypass` |
   | **1.3.1 Info and Relationships** | real tables with real headers | yes |
   | **3.3.2 Labels or Instructions** | the exposed facet form | yes — `label` |
   | **2.4.7 Focus Visible** | tabbing through a real pager | **no** — manual |
   | **2.5.8 Target Size (Minimum)** | pager and facet links | **no** — manual, WCAG 2.2 |
   | **1.4.10 Reflow** | a wide financial table on a narrow viewport | **no** — manual |

   **Three of the seven are not machine-checkable.** A unit that reports "axe clean" and stops has
   measured four of them. Said here so that gate B's reviewer knows what the green does not cover.

## 9 · Risks

| Risk | Sev | Mitigation |
|---|---|---|
| A shipped translation is dropped silently because `es` is not configured at import time | 🔴 | T-902's dirty case measures it **before** any content carries a translation; the idiom is recorded at I-067 |
| A gate goes green over an empty view (I-062) | 🔴 | Every assertion in this unit orders the denominator first. It is a gate-A(10) clause, not a convention |
| `agora_theme 1.0.0` is what installs, so every table renders through the pre-fix path | 🔴 | §7's dependency table; [andres] publishes 1.0.1; the preview row prints the resolved version and is inadmissible without it |
| D-035 unanswered when wave 10 starts | 🟡 | Lane B's `lang`-attribute row is gated on the decision and named as gated. Wave 9 does not touch it |
| Package weight — `haven` ships 75 MB of content | 🟡 | A stated ceiling is a task criterion in wave 10, quoted with `du -sh content` |
| Real personal data reaching `content/` | 🟡 | `no-real-people` + the EXIF sweep, both with watched dirty cases. §6.2 states what they cannot prove |
| The GitHub mirror never appears, so T-1202 defers a second time | 🟡 | The prerequisite is on the row. A second deferral is written down with the prerequisite restated — never silent |
| Unit 003 grows the way unit 001 grew | 🟡 | §3's admission test and the 34-row budget, reported by command in every verdict |
| Demo content contradicts D-026 (a field is missing) | 🟡 | That is a **finding about D-026**, escalated. Adding a field quietly is forbidden by §3's NO-list item 5 |
