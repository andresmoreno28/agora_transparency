# T-503 · Does a front-end theme carry any Canvas-side obligation beyond declaring regions?

**Measured 2026-08-24.** One question, two documents, read in full at one tag.

> **Verdict: no obligation found.** Neither document places a single requirement on a theme
> *as a theme*. Both are addressed to the author of a **component**, and a theme is bound by
> them only in the case where it chooses to ship its own Single-Directory Components and wants
> Canvas to accept them. Ágora's theme ships none in unit 002, so nothing here is binding on it.
>
> **R-1 of `2026-08-24-canvas-theme-and-cross-repo-gates.md` is confirmed, not contradicted.**
> The `NOT MEASURED` paragraph that file closes with (its lines 57-59) is hereby closed.
> **T-502's file list does not change.**

---

## 1. What was read, exactly

| URL | HTTP | Blob | Bytes | Lines |
|---|---|---|---|---|
| `https://git.drupalcode.org/project/canvas/-/raw/1.10.1/docs/components.md` | **200** | `efb827e8e7cb561b7ba54d91923739e1a0daae3b` | 14117 | 210 |
| `https://git.drupalcode.org/project/canvas/-/raw/1.10.1/docs/shape-matching.md` | **200** | `5e2febbb29517cf62552717f2dc82254bf30bc0c` | 35230 | 545 |

Tag `1.10.1` resolves to commit `50a7f2f0e62f3ecd52ba6e010d936a2a51187084`, dated
<!-- The API path below is quoted URL-encoded so it can be replayed as written. The token cspell
     sees is a percent-encoded path separator glued to a project name, not a word: D-024(3),
     bucket 4 - scoped in place, not declared globally. -->
<!-- cspell:disable-next-line -->
2026-08-12 (`/api/v4/projects/project%2Fcanvas/repository/tags/1.10.1`).
Both files were read whole; every line range below was counted in the downloaded file, not
inferred from a heading.

**Incidental measurement, recorded so it is not rediscovered:** `components.md` links four
times to a filename that does not exist at this tag — `shape-matching-into-field-types.md`,
at `components.md:58-59`, `:89-90`, `:101-102` and `:174-175`.
`https://git.drupalcode.org/project/canvas/-/raw/1.10.1/docs/shape-matching-into-field-types.md`
returns **404**. The document was renamed and the in-document links were not updated. The tree
listing at that tag (`/repository/tree?path=docs&ref=1.10.1`) contains `docs/shape-matching.md`
and no file by the older name, so the two paths this task was given are the right ones.

---

## 2. Why the answer is "no obligation"

### 2.1 Both documents are addressed to component authors, not to themes

`components.md` is organised entirely by **component source plugin**: Single-Directory
Components at `:79-113`, block plugins at `:116-158`, JavaScript components at `:162-182`,
the fallback plugin at `:184-200`, categorisation at `:206-210`. Its product requirements
(`:32-39`) are requirements **on Canvas**, phrased as what Canvas MUST support — not
requirements on anything a site installs alongside it.

`shape-matching.md:5` states its own scope: *"This builds on top of the `Canvas Components`
doc."* Its implementation section opens with a warning that narrows it further, verbatim at
`shape-matching.md:156-157`:

> ⚠️ This only applies to `component`s originating from a `Component Source Plugin` that DO NOT
> have an input UX (such as `SDC`), for others the UX and storage are both simply the existing
> one, and NOTHING in this document applies! ⚠️

Everything after that point — prop shapes (`:159-167`), conjured fields (`:201-241`), entity
field matching (`:242-277`), other prop sources (`:279-307`), prop expressions (`:309-395`),
and the JSON Schema extensions (`:398-545`) — describes how Canvas builds an editing UX for
component inputs. None of it is a step a theme performs.

### 2.2 The word "theme" occurs five times across 755 lines, and none is a requirement

Counted, case-insensitive, over both files: **4** occurrences on 4 lines in `components.md`,
**1** occurrence on 1 line in `shape-matching.md`. All five, in full:

- `components.md:73-74` — two PHP class references, `\Drupal\Core\Theme\Component\ComponentMetadata`
  and `ComponentValidator::validateProps()`. Namespace, not obligation.
- `components.md:98` — *"MUST always have schema, even for theme `SDC`s"*. This is item one of
  the eligibility list at `:97-107`, and it is conditional — see 2.3.
- `components.md:120` — *"they're hard-coupled to a theme (region): they're a 'placed block' in
  the Drupal UI!"*, one of the two reasons given at `:118-123` for why Canvas surfaces block
  **plugins** rather than block **config entities**.
- `shape-matching.md:136` — *"a `prop shape` that is _named_: one that is defined in a module's
  or theme's `/schema.json` file"*. An available mechanism, offered to whoever defines a
  well-known prop shape. Nothing requires a theme to define one; `components.md:112` records the
  same `/schema.json` mechanism as SDC functionality Canvas implements ahead of core.

`components.md:120` deserves a sentence of care, because it is the only place a region is named
and it could be misread as touching R-1. It does not. It explains that Canvas does **not** offer
placed blocks as components *precisely because* those are region-coupled; Canvas components are
therefore region-independent. It says nothing for or against a theme declaring regions. The
region requirement recorded in R-1 comes from `canvas.info.yml`'s own comment about needing main
content, title and messages block plugins — a different file, measured on 2026-08-24 and
unaffected by anything read today.

### 2.3 The one conditional obligation, stated so it is not mistaken for none

If — and only if — a theme ships its own Single-Directory Components and wants them usable in
Canvas, `components.md:97-107` binds **each such component**, verbatim:

> For an `SDC` to be compatible/eligible for use in Canvas, it:
> - MUST always have schema, even for theme `SDC`s
> - MUST have `title` for each prop and each slot
> - MUST have `example` for each required prop, the first example is used as the default value
> - MUST have only props for whose `prop shape`s a `static prop source` can be found
> - MUST not have `status` value `obsolete`
> - MUST not have `noUi` value `true`
> - SHOULD have a `category`; if not specified, the fallback value "Other" will be used

Enforced by `\Drupal\canvas\ComponentMetadataRequirementsChecker` (`components.md:107`). The
list carries its own expiry, at `components.md:109`: *"this list of criteria is not final, it
will keep evolving _at least_ until a `1.0` release of Canvas."* Canvas is at 1.10.1, so that
caveat has expired on its own terms, but the sentence is still in the shipped document.

The penalty for ignoring it is bounded and local: a non-conforming component is not offered in
the Canvas UI. It does not break the theme, the site or the install.

Two optional extras belong in the same conditional bucket, both offered rather than required:
`contentMediaType: text/html` plus `x-formatting-context` for rich-text props
(`shape-matching.md:402-455`), and `contentMediaType` plus `x-allowed-schemes` for URI props
(`shape-matching.md:457-481`). One extra is currently a trap and is recorded as such —
`shape-matching.md:495-497`, verbatim:

> Today only code components actually support `content-entity-reference` props end-to-end. The
> YAML examples below show the shape, but **an SDC that authors them directly will currently be
> flagged ineligible at discovery time**.

Block-plugin eligibility (`components.md:143-145`: fully validatable settings schema, no
required context) constrains the **block plugin**, which a theme does not provide.

---

## 3. What this settles for the plan

1. **T-502's file list is unchanged.** No `*.component.yml`, no `/schema.json`, no
   `libraries.yml` entry and no additional key in `agora_theme.info.yml` is required by either
   document.
2. **"Canvas-compatible" remains a non-goal**, exactly as R-1 concluded. The theme's obligations
   are the ones this project imposes on itself: AA colour tokens, self-hosted OFL typography,
   accessible tables and forms, visible focus.
3. **If a later unit adds a theme-side component**, this note is the checklist to satisfy —
   `components.md:97-107` per component, re-read at the Canvas version in force at that time,
   because `components.md:109` says the list moves.

---

## 4. NOT MEASURED

Stated so nothing is inferred from silence:

- The top level of `docs/` at this tag holds **15 files and 6 subdirectories**; the 13 files other
  than the two read here were not opened, and neither were the subdirectories.
  `config-management.md`, `data-model.md` and `twig-to-react-in-canvas-stark.md` are the three
  whose names suggest they are most likely to touch a theme.
- `\Drupal\canvas\ComponentMetadataRequirementsChecker` was not read. The eligibility list above
  is quoted from the document, not verified against the code that enforces it.
- Whether Canvas's page template functionality imposes anything at render time beyond the block
  plugins named in `canvas.info.yml` was not re-measured today; R-1's measurement stands
  unchallenged, and nothing read today touches it.
