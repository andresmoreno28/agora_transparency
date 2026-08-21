---
name: accesibilidad-wcag-aa
description: Use when writing or reviewing Twig templates, CSS tokens, components, forms or demo pages for the site template, when choosing colours or focus styles, when an axe run reports violations, or when preparing the accessibility statement required for publication.
---

# WCAG 2.2 AA accessibility as a gate

## Core principle

**Accessibility is written, not patched in.** In this project it is a gate: axe with no violations
and keyboard navigation on the key flows. An inaccessible component is not "debt", it is an
unfinished task.

## What gets checked before taking anything as done

| Layer | Check | Tool |
|---|---|---|
| Semantics | Landmarks, heading hierarchy without skips, real lists | Review + axe |
| Keyboard | Everything reachable in a logical order; **visible** focus always; no traps | Manual, Tab/Shift+Tab |
| Contrast | Text ≥ 4.5:1; large text and UI ≥ 3:1 | Theme tokens |
| Forms | Associated `<label>`; errors linked to the field; not colour alone | axe + manual |
| Images | Purposeful `alt`; decorative ones with `alt=""` | Review |
| Language | Correct `lang`, and per fragment in bilingual ES/EN content | Review |
| Motion | Respects `prefers-reduced-motion` | CSS |

## The 2.2 criteria most often forgotten

WCAG **2.2** added criteria that were not in 2.1 and that tend to fail:

- **Focus Not Obscured** — the focused element cannot end up covered by sticky headers or cookie
  banners. It is the most common failure in institutional portals.
- **Target Size (Minimum)** — touch targets ≥ 24×24 px, barring exceptions.
- **Dragging Movements** — anything that is dragged needs a single-pointer alternative.
- **Consistent Help** — if there is help/contact, in the same place on every page.
- **Redundant Entry** — do not ask again for data already entered in the same process.
- **Accessible Authentication** — no mandatory cognitive tests (remembering, transcribing).

## Accessible data tables

This template displays remuneration, contracts and budgets. Tables are core content:

- A real `<th scope="col|row">`, never a bold `<td>`
- A `<caption>` that describes the table
- No tables for layout
- **Every visualisation needs its equivalent table** as an accessible fallback — and it is the
  table, not the chart, that is the source of truth

## Accessibility statement

It is a deliverable, not an extra. It must exist and contain: the declared conformance level, the
date of the evaluation, the method, known limitations and an **operational complaints channel**.

## Common mistakes

- **Removing the focus `outline`** for aesthetics → if you remove it, replace it with something at ≥ 3:1
- **Contrast measured against the wrong colour** → measure against the real rendered background
- **`aria-*` to paper over badly written HTML** → the correct native element first
- **Colour as the only carrier of meaning** (states, errors, categories) → add text or shape
- **Testing only the home page** → the flows (search, facets, request form) are what fail
- **axe green = accessible** → axe detects a fraction; the keyboard has to be tested by hand

## Red flags — STOP

- "We'll fix it in the hardening unit" → a11y is not deferred, it is a gate per wave
- "axe passes, that's it" → have you walked through it with the keyboard?
- "It's an edge case" → screen readers are not an edge case
- You are about to silence/exclude an axe rule to turn the gate green → that gets escalated, not silenced
