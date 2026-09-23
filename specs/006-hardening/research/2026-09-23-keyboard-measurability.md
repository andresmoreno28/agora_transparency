<!--
  cspell:ignore tbachert networkidle

  Two identifiers quoted VERBATIM, scoped here rather than declared in .cspell-project-words.txt,
  because this dispatch limited writing to this one file. Each survives D-024(3)'s question:

    tbachert    - the Composer vendor of `tbachert/spi`, written out in the rig's `allow-plugins`
                  list in Appendix B (copied from the `~/agora-t06` rig's composer.json). A plugin
                  missing from that list is blocked, so the name has to appear verbatim.
    networkidle - Playwright's own name for a load state, `waitForLoadState('networkidle')`, in
                  the harness in Appendix B. It is an API string; spelling it any other way
                  would break the call.
-->
# Unit 006 · The keyboard walkthrough — what a machine measured, and what is left for a person

Measured by `tester` on **2026-09-23**, on a rig built for the purpose from the packaged tree at
**`acd2a51`** (`~/agora-kbd`, never `~/agora-t06`). Research for **T-0616** (the nine anonymous
pages) and **T-0633** (the admin surface). **Neither row is closed by this file** — both name a
person, and this file's job is to say exactly how much of "a person" is still needed, with the
evidence ready.

**Every figure carries its source.** Each is exactly one of:

- **MEASURED** — a real Chrome was driven with real key presses; the figure comes from the
  recorded run, and every table below was generated from the run's JSON rather than typed.
- **READ AT SOURCE** — a file or page was read; what that does not prove is stated.
- **LEFT TO A PERSON** — what no instrument used here settles, written as a question with the
  evidence prepared (§6).

⚠️ **A reading is never promoted to a measurement here, and a measurement is never promoted to a
judgement.** The harness that produced every number is in Appendix B, byte-identical to the files
that were executed, and every one of its checks was watched failing before its green was used
(§4).

---

## 0 · Reconciliation — the premise, against what a browser can be made to do

| # | Assumed | Measured or read | Consequence |
|---|---|---|---|
| 1 | *"A machine cannot press a key and see a focus ring"* — T-0616's cell, T-0603's step 5, and the dispatch that asked for this file | **Half false. MEASURED.** Playwright sends real key events to a real, installed Chrome — Tab, Shift+Tab, Enter, Space, arrows, Escape — and a focus ring is *seen* by photographing each focused element and then the same pixels a frame after focus is removed: identical pixels mean there is no indicator. **1176** stops on the nine pages were photographed that way at two widths; **0** had no indicator. The half that is true: a machine cannot say whether a *present* indicator is *noticed*, nor whether an order *means* something | The human part of both rows shrinks to the checklist in §6 |
| 2 | T-0616's criterion names **2.4.7 · 2.5.8 · 1.4.10 · 2.4.1 in use** | **All four have a machine measure here, and all four were run** — §1 says how, and where each stops | What stays human is perception and meaning, not measurement |
| 3 | The accessibility gate already covers 2.5.8 | **It does not, in either repository. READ AT SOURCE.** `target-size`, axe-core's only 2.5.8 rule, ships `"enabled": false` in axe-core **4.10.3** — what Drupal core 11.4.7's `core/yarn.lock` resolves, so what the CI job loads — and in **4.13.0** (`lib/rules/target-size.json` read at both tags). `AccessibilityTest.php:798` at `acd2a51` calls `axe.run(document)` with no options; `agora_theme`'s `tests/src/Nightwatch/Tests/axe.js:1023` calls `.axeRun('html', {})`. **Asked by name here: 0 violations over 1138 passing nodes** | F3. Reported, not changed: `AccessibilityTest.php` is another writer's file today |
| 4 | The rig is `~/agora-t06` | Not touched. `~/agora-kbd` was built from `git archive --worktree-attributes acd2a51` with the theme resolved from `packages.drupal.org` under the template's own `^1.1`: **1.2.0** | §A.1 |
| 5 | A cookie banner could obscure focus | **No banner renders for an anonymous visitor on this install. READ AT SOURCE** on the rig: `klaro.settings` `dialog_mode: silent`, and 2 of 29 Klaro services enabled (`cms`, `klaro`). The obscuring check met no banner, so it was falsified with injected ones (§4) | A site that enables a consent-requiring service changes this; the harness should be re-run then |
| 6 | The usage-reporting guard, I-117 | In place before the site was installed; `update_available_releases` empty after the recipe and after the last run (§A.2) | — |
| 7 | HEAD is `acd2a51` | **HEAD moved four commits while this was measured** (`a3695ff`, `328c78c`, `da373cc`, `d47fd5a`). **MEASURED:** the packaged file list is identical at both, and the packaged bytes differ only in `README.md` and in `recipe.yml` — whose changed lines are all comments, and whose parsed YAML is identical. `AccessibilityTest`'s nine-page constants are identical at both. **So every measurement here applies to HEAD's package unchanged**; the rig itself was built from `acd2a51` | — |
| 8 | — | **Nine things the harness learned by being wrong first — each fixed, and every recorded run made with the fixed file.** (a) Shift+Tab from the first element wraps straight to the last with no document state between, so a clean reverse walk ends by *wrapping*; the first "trap" ever reported was this. (b) Keyboard scrolling is **animated** — in `probe-scroll.mjs`'s recorded run (Appendix B) `scrollLeft` read 0 at once and still 0 at 55 ms, 41 at 102 ms and 120 at 198 ms — so an immediate read called a scrollable table unscrollable; the harness now waits 700 ms. (c) A point clipped away by a scroll container hits an *ancestor* in hit-testing, and was counted as visible. (d) A sampling grid steps over slivers, and called a link with 4.3 % of itself on screen "entirely hidden"; geometry is now exact. (e) **A revisit is not a trap**: Gin sends focus from a hidden form button back to its sticky copy once, and the next Tab leaves the page; a trap is now an element reached a third time, or focus that stops. (f) Chrome 130 and later make a scroll container reachable by Tab without any `tabindex`, so "focusable" is decided by the walk, not the attribute. (g) At 320 px Gin collapses its sidebar **with script, after `load`**, so a prediction taken at `load` counted the sidebar's links, which Tab never reaches; the page is now settled before anything is predicted. (h) Config Guardian's `/analyze` takes 18 s to render, and an 8 s wait called Enter on a link to it "not followed" when it had been; a navigation is now awaited to its commit, for up to 60 s. (i) The content of a *closed* `<details>` keeps its layout, so 43 hidden lists on one admin page measured as overflowing scroll regions, and `<summary>` was missing from what counts as focusable content; invisible regions are now skipped. **One more thing learned the same way is a product finding**: Chrome does not scroll a table to a focused cell that is already *partly* visible (F1) | Recorded so the next harness does not re-learn them |

---

## 1 · The boundary — each criterion the walkthroughs cover, and who can settle it

**Machine** = the harness measures it and can fail on its own; each such check was watched
failing (§4). **Partly** = the machine settles the measurable half and hands a person a prepared
question for the rest. **Person** = no instrument used here settles it.

| criterion | level | what the walkthrough asks | what the machine measured | class | why, and what is left |
|---|---|---|---|---|---|
| the transcript itself | — | *"what was pressed, what received focus"* (T-0616, T-0633) | every stop: the key pressed, the element, the **role and accessible name Chrome computed for it** (the accessibility tree a screen reader is given), its landmark region, its box | **Machine** | More complete than a person's notes: every stop, every page, two widths, both directions |
| 2.1.1 Keyboard — reach | A | can every control be reached? | the DOM's focus candidates, predicted before a key is pressed, against the stops the walk actually reached | **Machine** | Only for things that are focusable elements at all. Functionality that exists solely for a pointer — a drag, a hover-only menu — is not a candidate and its absence is invisible to this comparison. None is known on these pages; a person looking for "something I can click but not reach" closes it |
| 2.1.1 Keyboard — operation | A | do Enter and Space do what a click does? | every non-link control, on a fresh load each time: click, then Enter, then Space, compared by the page state they leave; Enter on the first link of every landmark region; the register filter used by keyboard alone | **Machine** for the controls present | A custom widget with its own key model (menu, tabs, slider) would need its expected keys spelled out. There is none on the anonymous pages. **Not run on the admin set**: an admin button can create a snapshot or start an import, and a measurement must not change the rig it measures |
| 2.1.2 No Keyboard Trap | A | can focus always leave? | each walk must end by leaving the page or wrapping to its own first stop after covering everything predicted; focus that stops moving (3 presses) or cycles through a subset is a trap, and Escape is tried | **Machine** | A *legitimate* trap (a modal with an advised exit) would need a person to judge the "advised" part. None here |
| 2.4.3 Focus Order | A | is the order logical? | Tab order against DOM order; positive `tabindex`; the reverse walk against the forward one; every upward visual jump, classified; the landmark trail | **Partly** | The machine proves the order **is** the DOM order, identical in both directions, with no positive `tabindex`. Whether that DOM order *preserves meaning* is a reading of the trail (§2), which takes a minute per page |
| 2.4.7 Focus Visible | AA | is the indicator visible? | each stop photographed focused, then blurred, same pixels: 0 changed pixels = **no indicator**; the pixels that change by ≥ 3:1 are counted | **Machine** for presence; **partly** for perception | This is where the premise was wrong. What a machine cannot do is decide whether a *present* indicator is noticed in context — a faint change, or a ring mostly outside the visible box. The faint band was empty on the anonymous pages (0 stops) and holds 12 on the admin surface (A5); the "mostly outside" case is real (F1) and is §6's first question |
| 1.4.11 Non-text Contrast, of the indicator | AA | does the indicator stand out? | the contrast between each changed pixel's focused and blurred colour | **Machine** (approximation) | 1.4.11 compares against adjacent colours; the harness compares against what was there before, which for a ring drawn over a background is the same pixels. An indicator drawn inside a gradient would need a person |
| 2.4.11 Focus Not Obscured (Minimum) | AA | is the focused element ever entirely hidden? | the element's boxes intersected **exactly** with the viewport and every clipping ancestor; opaque content stacked above sampled inside what is left; forward and reverse walks, two widths | **Machine** | Covers sticky headers, fixed banners and clipping scroll containers alike. A transparent overlay with a pointer target is not an occluder and is not counted |
| 2.4.12 Focus Not Obscured (Enhanced) | AAA | is any part hidden? | the same measure, as a percentage | **Machine** | Reported apart; not a target of this project |
| 2.4.13 Focus Appearance | AAA | is the indicator big and strong enough? | pixels changing by ≥ 3:1 against the area of a 2 CSS px perimeter of the component, counting only edges an indicator could be drawn along on screen | **Machine** | The perimeter of a link that wraps is not defined by the criterion's text; the harness uses the outline of the union of its line boxes |
| 2.4.1 Bypass Blocks, in use | A | does the skip link work? | the first Tab lands on it; Enter changes the address to its target and moves focus there; the next Tab lands inside `<main>` and is not hidden | **Machine** | See §3 for the one page shape where "next Tab inside `<main>`" is the wrong question |
| 3.2.1 On Focus | A | does focus change the context? | the address never changes during a walk | **Machine** for navigation | A window or dialog opened on focus would need a person to notice. None |
| 2.5.8 Target Size (Minimum) | AA | are targets large enough or spaced? | axe-core's `target-size` rule, **asked by name** because it is off by default | **Machine**, with axe's own reading of the inline and equivalent-target exceptions | Where axe says `incomplete`, a person decides |
| 1.4.10 Reflow | AA | usable at 320 CSS px without scrolling in two directions? | at 320 × 256 (a 1280 × 1024 screen at 400 %): the page's scroll width against the viewport; any element overflowing outside a scroll container; text cut by hidden overflow; tables held in keyboard-scrollable wrappers (the criterion's two-dimensional exception); **and the whole keyboard walk repeated at that width** | **Partly** | "Without loss of information or functionality" beyond overflow and clipping — overlaps, stacking that reads wrongly — is a visual judgement. A person glancing at the narrow pages for that is optional (§6) |
| screen-reader speech (4.1.2 in use; 4.1.3 after filtering) | A / AA | does a screen reader say something sensible? | the computed role and name of every stop, which is what a screen reader is handed | **Person** | Whether NVDA or VoiceOver actually says it sensibly, and whether the register's result count is announced after a filter, needs a person with one. **Not in either row's criterion**; named so it is not assumed covered |
| other browsers | — | — | Chrome 153 only | **Person, or a port of the harness** | Firefox draws its own default ring and Safari on macOS does not Tab to links by default (known platform behaviour, **not measured here**). Playwright can drive Firefox and WebKit with its own builds, but **this harness cannot as written**: it reads accessible names through the Chrome DevTools protocol, and that step would have to be replaced first |

---

## 2 · The nine anonymous pages — T-0616's scope — MEASURED

**The pages are the ones `AccessibilityTest` declares, derived rather than typed** (§A.3): two
Canvas pages, the front page, the four registers, one published record and the not-found page.
Each was walked at **1280 × 800** and at **320 × 256** — a 1280 × 1024 screen at 400 % zoom, the
width WCAG uses for reflow.

**Denominators, all eighteen page-widths together.** 18 walked,
**0 harness failures**.

| measure | result |
|---|---|
| Tab stops reached / predicted from the DOM before any key was pressed | **1176 / 1176** — 0 unreached, 0 reached but not predicted |
| walks that ended cleanly (leaving the page forward, wrapping in reverse) | **36 of 36** · traps **0** · revisits 0 |
| reverse walk is the exact mirror of the forward walk | **18 of 18** |
| address changed while focus moved (3.2.1) | **0** |
| focused stops with a visible indicator · with none · faint (no pixel changing by ≥ 3:1) | **1176** · 0 · 0 (and 0 off-screen) |
| stops meeting the 2.4.13 appearance measure (AAA, not a target) | 1107 of 1176 |
| entirely hidden at landing, forward · reverse (2.4.11) | **0 · 0** |
| partly hidden at landing, forward (2.4.12, AAA) · of which under half visible | 107 · 46 — broken down by cause in §2.4 |
| skip link works in use (2.4.1) | **18 of 18** |
| non-link controls where click, Enter and Space all act | **32 tested · 0 failing** |
| links followed on Enter (first link of every landmark region) | **200 of 200** |
| register filter flows completed by keyboard alone | **14** (every page-width that carries an exposed filter) |
| scroll regions operable by keyboard · of which arrows scroll them | **11 of 11** · 11 · relying on the browser 0 |
| `target-size`, asked by name (2.5.8): violations · passing nodes · incomplete | **0** · 1138 · 0 |
| axe default run, both widths: violation nodes | **0** |
| pages scrolling sideways at 320 CSS px (1.4.10) | **0 of 9** |
| findings the harness can call on its own | **0** — exit 0 |

⚠️ **Zero findings is a measured result here, not an absence of one**: every check behind it was
seen to fire on a planted defect first (§4), and a walk that reached nothing would have exited 2.

### 2.1 · Reach, order, traps, skip link and activation — 1280 × 800

| page | path | reached / predicted | forward · reverse | DOM-order inversions · positive `tabindex` | reverse mirrors forward | skip link (first stop → Enter → next Tab in main) | controls: click = Enter = Space | links followed on Enter | address changes while tabbing |
|---|---|---|---|---|---|---|---|---|---|
| Canvas page 1 | `/institution` | **57 / 57** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| Canvas page 2 | `/home` | **63 / 63** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| front page | `/` | **63 / 63** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| contracts register | `/contracts` | **72 / 72** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| publications register | `/publications` | **101 / 101** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 12 of 12 | 0 |
| people register | `/people` | **62 / 62** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| library register | `/library` | **112 / 112** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 12 of 12 | 0 |
| record page | `/contracts/resurfacing-camino-viejo-access-road` | **31 / 31** | leaves page · wraps | 0 · 0 | yes | ✓ | 1 of 1 | 11 of 11 | 0 |
| not-found page | `/agora-transparency-no-such-route` | **27 / 27** | leaves page · wraps | 0 · 0 | yes | ✓ | 1 of 1 | 10 of 10 | 0 |

**The order, region by region** — what §6 asks a person to read. An "upward jump" is a stop
above the previous one; "up-and-left" is one that is also not further right, which is the shape of
an order that goes back on itself rather than on to the next column.

| page | the order focus moves through the page's regions (count of stops in each) | upward jumps · of which up-and-left |
|---|---|---|
| Canvas page 1 | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → navigation "Breadcrumb" x1 → form x2 → main x28 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |
| Canvas page 2 | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → main x2 → navigation "Quick access" x6 → main x5 → form x4 → main x20 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |
| front page | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → main x2 → navigation "Quick access" x6 → main x5 → form x4 → main x20 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |
| contracts register | (no landmark) x1 → banner x1 → navigation "Main navigation" x9 → search "Search published records" x2 → navigation "Breadcrumb" x1 → form x3 → main x36 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 4 · 0 |
| publications register | (no landmark) x1 → banner x1 → navigation "Main navigation" x9 → search "Search published records" x2 → navigation "Breadcrumb" x1 → main x6 → form x4 → main x53 → navigation "Pagination" x5 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 4 · 0 |
| people register | (no landmark) x1 → banner x1 → navigation "Main navigation" x9 → search "Search published records" x2 → navigation "Breadcrumb" x1 → form x2 → main x27 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 4 · 0 |
| library register | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → navigation "Breadcrumb" x1 → form x4 → main x77 → navigation "Pagination" x4 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |
| record page | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → navigation "Breadcrumb" x2 → main x3 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |
| not-found page | (no landmark) x1 → banner x1 → navigation "Main navigation" x3 → search "Search published records" x2 → navigation "Breadcrumb" x1 → contentinfo x1 → navigation "Find a record" x2 → navigation "Public money" x3 → navigation "Documents and data" x2 → navigation "The institution" x2 → navigation "Legal and accessibility" x4 → contentinfo x1 → navigation "Follow us" x4 | 3 · 0 |

### 2.2 · What can be seen — 1280 × 800

| page | stops | indicator visible · none or off-screen · faint | 2.4.13 (AAA) met | entirely hidden (fwd · rev) | partly hidden (fwd) · of which under half | `target-size` pass · violation · incomplete | axe rules · violation nodes | page overflow at this width |
|---|---|---|---|---|---|---|---|---|
| Canvas page 1 | 57 | **57** · 0 · 0 | 57 of 57 | 0 · 0 | 2 · 0 | 55 · 0 · 0 | 89 · 0 | 0 px |
| Canvas page 2 | 63 | **63** · 0 · 0 | 63 of 63 | 0 · 0 | 1 · 0 | 60 · 0 · 0 | 90 · 0 | 0 px |
| front page | 63 | **63** · 0 · 0 | 63 of 63 | 0 · 0 | 1 · 0 | 60 · 0 · 0 | 90 · 0 | 0 px |
| contracts register | 72 | **72** · 0 · 0 | 63 of 72 | 0 · 0 | 9 · 7 | 69 · 0 · 0 | 89 · 0 | 0 px |
| publications register | 101 | **101** · 0 · 0 | 100 of 101 | 0 · 0 | 2 · 0 | 99 · 0 · 0 | 89 · 0 | 0 px |
| people register | 62 | **62** · 0 · 0 | 61 of 62 | 0 · 0 | 2 · 0 | 60 · 0 · 0 | 89 · 0 | 0 px |
| library register | 112 | **112** · 0 · 0 | 112 of 112 | 0 · 0 | 8 · 0 | 110 · 0 · 0 | 89 · 0 | 0 px |
| record page | 31 | **31** · 0 · 0 | 31 of 31 | 0 · 0 | 1 · 0 | 30 · 0 · 0 | 89 · 0 | 0 px |
| not-found page | 27 | **27** · 0 · 0 | 27 of 27 | 0 · 0 | 1 · 0 | 26 · 0 · 0 | 89 · 0 | 0 px |

### 2.3 · The same at 320 × 256

| page | path | reached / predicted | forward · reverse | DOM-order inversions · positive `tabindex` | reverse mirrors forward | skip link (first stop → Enter → next Tab in main) | controls: click = Enter = Space | links followed on Enter | address changes while tabbing |
|---|---|---|---|---|---|---|---|---|---|
| Canvas page 1 | `/institution` | **57 / 57** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| Canvas page 2 | `/home` | **63 / 63** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| front page | `/` | **63 / 63** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| contracts register | `/contracts` | **72 / 72** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| publications register | `/publications` | **101 / 101** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 12 of 12 | 0 |
| people register | `/people` | **62 / 62** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 11 of 11 | 0 |
| library register | `/library` | **112 / 112** | leaves page · wraps | 0 · 0 | yes | ✓ | 2 of 2 | 12 of 12 | 0 |
| record page | `/contracts/resurfacing-camino-viejo-access-road` | **31 / 31** | leaves page · wraps | 0 · 0 | yes | ✓ | 1 of 1 | 11 of 11 | 0 |
| not-found page | `/agora-transparency-no-such-route` | **27 / 27** | leaves page · wraps | 0 · 0 | yes | ✓ | 1 of 1 | 10 of 10 | 0 |

| page | stops | indicator visible · none or off-screen · faint | 2.4.13 (AAA) met | entirely hidden (fwd · rev) | partly hidden (fwd) · of which under half | `target-size` pass · violation · incomplete | axe rules · violation nodes | page overflow at this width |
|---|---|---|---|---|---|---|---|---|
| Canvas page 1 | 57 | **57** · 0 · 0 | 56 of 57 | 0 · 0 | 14 · 7 | 55 · 0 · 0 | 89 · 0 | 0 px |
| Canvas page 2 | 63 | **63** · 0 · 0 | 58 of 63 | 0 · 0 | 3 · 2 | 60 · 0 · 0 | 90 · 0 | 0 px |
| front page | 63 | **63** · 0 · 0 | 58 of 63 | 0 · 0 | 3 · 2 | 60 · 0 · 0 | 90 · 0 | 0 px |
| contracts register | 72 | **72** · 0 · 0 | 64 of 72 | 0 · 0 | 12 · 7 | 69 · 0 · 0 | 89 · 0 | 0 px |
| publications register | 101 | **101** · 0 · 0 | 81 of 101 | 0 · 0 | 2 · 1 | 99 · 0 · 0 | 89 · 0 | 0 px |
| people register | 62 | **62** · 0 · 0 | 61 of 62 | 0 · 0 | 14 · 7 | 60 · 0 · 0 | 89 · 0 | 0 px |
| library register | 112 | **112** · 0 · 0 | 94 of 112 | 0 · 0 | 28 · 13 | 110 · 0 · 0 | 89 · 0 | 0 px |
| record page | 31 | **31** · 0 · 0 | 31 of 31 | 0 · 0 | 1 · 0 | 30 · 0 · 0 | 89 · 0 | 0 px |
| not-found page | 27 | **27** · 0 · 0 | 27 of 27 | 0 · 0 | 3 · 0 | 26 · 0 · 0 | 89 · 0 | 0 px |

### 2.4 · Where focus lands partly out of sight

**No stop on any of the nine pages is ever entirely hidden**, forward or reverse, at either width.
107 forward stops land with some part out of sight, and the cause is
the whole story:

| width | cause | stops | visible at landing | pages |
|---|---|---|---|---|
| 1280x800 | clipped by a scroll container Chrome did not scroll to the focused element (F1) | **7** | 12.9–20.8% | contracts register |
| 1280x800 | cut by the window edge only, by a row of pixels (not a defect) | **6** | 98.5% | library register |
| 1280x800 | larger than the window: a table wrapper taller or wider than the viewport can never be wholly on screen (not a defect) | **5** | 52–75.5% | Canvas page 1, contracts register, library register, people register, publications register |
| 1280x800 | the skip link: its box starts 8 px above the window and the header's background covers its bottom padding; the text is fully on screen | **9** | 53% | Canvas page 1, Canvas page 2, contracts register, front page, library register, not-found page, people register, publications register, record page |
| 320x256 | clipped by a scroll container Chrome did not scroll to the focused element (F1) | **58** | 4.3–95.1% | Canvas page 1, contracts register, library register, people register |
| 320x256 | cut by the window edge only, by a row of pixels (not a defect) | **3** | 98.9% | library register, not-found page |
| 320x256 | larger than the window: a table wrapper taller or wider than the viewport can never be wholly on screen (not a defect) | **10** | 8.8–46.3% | Canvas page 1, Canvas page 2, contracts register, front page, library register, people register, publications register |
| 320x256 | the skip link: its box starts 8 px above the window and the header's background covers its bottom padding; the text is fully on screen | **9** | 53% | Canvas page 1, Canvas page 2, contracts register, front page, library register, not-found page, people register, publications register, record page |
| | **total** | **107** | | |

**Two of these categories are defects, and neither fails AA.** The skip link's is F2. The other is
F1: Chrome scrolls a register's table to a focused cell
that is *entirely* out of its visible area — measured with `probe-table.mjs` (Appendix B): "Completed" moved the table 609 px — but
leaves a cell that is *partly* visible exactly where it is. So a keyboard user lands on a link that
is only partly inside the table — as little as 4.3 % of it:

| width | page | stop | focused link | visible at landing | ring pixels on screen at landing (≥ 3:1) |
|---|---|---|---|---|---|
| 1280x800 | contracts register | 27 | "Minor contract" | **14.3%** | 189 |
| 1280x800 | contracts register | 31 | "Negotiated procedure without publication" | **20.8%** | 405 |
| 1280x800 | contracts register | 35 | "Simplified open procedure" | **16.1%** | 261 |
| 1280x800 | contracts register | 39 | "Simplified open procedure" | **16.1%** | 261 |
| 1280x800 | contracts register | 43 | "Open procedure" | **12.9%** | 189 |
| 1280x800 | contracts register | 47 | "Open procedure" | **12.9%** | 189 |
| 1280x800 | contracts register | 51 | "Minor contract" | **14.3%** | 189 |
| 320x256 | Canvas page 1 | 14 | "Annual remuneration" | **4.3%** | 204 |
| 320x256 | Canvas page 1 | 15 | "Severance payment" | **31.6%** | 414 |
| 320x256 | Canvas page 1 | 17 | "Environment and Public Space" | **86.1%** | 741 |
| 320x256 | Canvas page 1 | 18 | "declaration-environment.pdf" | **36.2%** | 399 |
| 320x256 | Canvas page 1 | 20 | "Urban Planning and Works" | **90.2%** | 813 |
| 320x256 | Canvas page 1 | 21 | "declaration-planning.pdf" | **36.2%** | 369 |
| 320x256 | Canvas page 1 | 24 | "declaration-deputy.pdf" | **23.8%** | 210 |
| 320x256 | Canvas page 1 | 28 | "Mayor's Office" | **89.5%** | 669 |
| 320x256 | Canvas page 1 | 31 | "Culture, Education and Sport" | **95.1%** | 957 |
| 320x256 | Canvas page 1 | 34 | "Mayor's Office" | **89.5%** | 669 |
| 320x256 | Canvas page 1 | 36 | "Social Services" | **83.8%** | 669 |
| 320x256 | Canvas page 1 | 37 | "declaration-social.pdf" | **39.8%** | 363 |
| 320x256 | contracts register | 20 | "Award amount" | **68.8%** | 546 |
| 320x256 | contracts register | 22 | "Awardee" | **64.1%** | 354 |
| 320x256 | contracts register | 28 | "Completed" | **37.6%** | 279 |
| 320x256 | contracts register | 32 | "Completed" | **21.6%** | 201 |
| 320x256 | contracts register | 36 | "Completed" | **42.5%** | 303 |
| 320x256 | contracts register | 40 | "Awarded" | **52.4%** | 303 |
| 320x256 | contracts register | 44 | "In force" | **62.5%** | 309 |
| 320x256 | contracts register | 48 | "Completed" | **43.7%** | 309 |
| 320x256 | contracts register | 52 | "Completed" | **37.6%** | 279 |
| 320x256 | people register | 20 | "Annual remuneration" | **4.3%** | 204 |
| 320x256 | people register | 21 | "Severance payment" | **31.6%** | 414 |
| 320x256 | people register | 23 | "Environment and Public Space" | **86.1%** | 741 |
| 320x256 | people register | 24 | "declaration-environment.pdf" | **36.2%** | 399 |
| 320x256 | people register | 26 | "Urban Planning and Works" | **90.2%** | 813 |
| 320x256 | people register | 27 | "declaration-planning.pdf" | **36.2%** | 369 |
| 320x256 | people register | 30 | "declaration-deputy.pdf" | **23.6%** | 210 |
| 320x256 | people register | 34 | "Mayor's Office" | **89.5%** | 669 |
| 320x256 | people register | 37 | "Culture, Education and Sport" | **95.1%** | 957 |
| 320x256 | people register | 40 | "Mayor's Office" | **89.5%** | 669 |
| 320x256 | people register | 42 | "Social Services" | **83.8%** | 669 |
| 320x256 | people register | 43 | "declaration-social.pdf" | **39.8%** | 363 |
| 320x256 | library register | 17 | "2022" | **45.7%** | 219 |
| 320x256 | library register | 20 | "2023" | **45.7%** | 219 |
| 320x256 | library register | 23 | "2023" | **63.6%** | 267 |
| 320x256 | library register | 26 | "2023" | **72.5%** | 291 |
| 320x256 | library register | 29 | "2024" | **45.7%** | 219 |
| 320x256 | library register | 32 | "2024" | **45.7%** | 219 |
| 320x256 | library register | 35 | "2024" | **45.7%** | 219 |
| 320x256 | library register | 38 | "2024" | **45.7%** | 219 |
| 320x256 | library register | 41 | "2023" | **45.7%** | 219 |
| 320x256 | library register | 44 | "2024" | **45.7%** | 219 |
| 320x256 | library register | 47 | "2022" | **85.9%** | 327 |
| 320x256 | library register | 50 | "2023" | **85.9%** | 327 |
| 320x256 | library register | 53 | "2023" | **45.7%** | 219 |
| 320x256 | library register | 56 | "2022" | **63.6%** | 267 |
| 320x256 | library register | 59 | "2023" | **85.9%** | 327 |
| 320x256 | library register | 62 | "2024" | **85.9%** | 327 |
| 320x256 | library register | 65 | "2024" | **65.8%** | 273 |
| 320x256 | library register | 68 | "2023" | **65.8%** | 273 |
| 320x256 | library register | 71 | "2024" | **65.8%** | 273 |
| 320x256 | library register | 74 | "2024" | **65.8%** | 273 |
| 320x256 | library register | 77 | "2024" | **65.8%** | 273 |
| 320x256 | library register | 80 | "2024" | **65.8%** | 273 |
| 320x256 | library register | 83 | "2022" | **45.7%** | 219 |
| 320x256 | library register | 86 | "2023" | **45.7%** | 219 |
| 320x256 | library register | 89 | "2024" | **45.7%** | 219 |

⚠️ **On the letter of the criteria this passes 2.4.11 and fails 2.4.12**: the element is not
*entirely* hidden. Whether a 4.3 % sliver of a focus ring is *visible* to a person is the first
question in §6, and the answer does not change the recommendation (F1).

### 2.5 · The transcripts — every stop, 1280 × 800, forward

What T-0616's criterion asks a person to write down — *what was pressed, what received focus,
whether the indicator was visible* — for every stop on every page. "Received focus" is the role
and accessible name Chrome computed; "indicator" is the pixel comparison, with the number of pixels
that changed, the number that changed by at least 3:1, and the style properties that differ between
the focused and blurred states; "on screen" is the share of the element visible at landing.

<details><summary>Canvas page 1 <code>/institution</code> — 57 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 9 | Tab | combobox "Area" | form | ✓ 6266 · 6198 · outline | meets | 100% |
| 10 | Tab | button "Filter" | form | ✓ 914 · 846 · outline | meets | 100% |
| 11 | Tab | generic "One row per person in the organisation chart." | main | ✓ 4800 · 4800 · outline | meets | 69.2% |
| 12 | Tab | link "Full name Sort descending" | main | ✓ 756 · 756 · outline | meets | 100% |
| 13 | Tab | link "Position" | main | ✓ 582 · 582 · outline | meets | 100% |
| 14 | Tab | link "Annual remuneration" | main | ✓ 1122 · 1122 · outline | meets | 100% |
| 15 | Tab | link "Severance payment" | main | ✓ 1122 · 1122 · outline | meets | 100% |
| 16 | Tab | link "Álvaro Menchón Serna" | main | ✓ 1062 · 1062 · outline | meets | 100% |
| 17 | Tab | link "Environment and Public Space" | main | ✓ 1092 · 1092 · outline | meets | 100% |
| 18 | Tab | link "declaration-environment.pdf" | main | ✓ 1080 · 1080 · outline | meets | 100% |
| 19 | Tab | link "Elena Rebollar Quintana" | main | ✓ 990 · 990 · outline | meets | 100% |
| 20 | Tab | link "Urban Planning and Works" | main | ✓ 1032 · 1032 · outline | meets | 100% |
| 21 | Tab | link "declaration-planning.pdf" | main | ✓ 912 · 912 · outline | meets | 100% |
| 22 | Tab | link "Ignacio Cardeñosa Vela" | main | ✓ 1038 · 1038 · outline | meets | 100% |
| 23 | Tab | link "Finance and Budget" | main | ✓ 882 · 882 · outline | meets | 100% |
| 24 | Tab | link "declaration-deputy.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 25 | Tab | link "Javier Otazua Lumbreras" | main | ✓ 930 · 930 · outline | meets | 100% |
| 26 | Tab | link "Finance and Budget" | main | ✓ 882 · 882 · outline | meets | 100% |
| 27 | Tab | link "Marta Belloso Iriarte" | main | ✓ 960 · 960 · outline | meets | 100% |
| 28 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 29 | Tab | link "declaration-mayor.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 30 | Tab | link "Nuria Vallejera Sanz" | main | ✓ 996 · 996 · outline | meets | 100% |
| 31 | Tab | link "Culture, Education and Sport" | main | ✓ 1122 · 1122 · outline | meets | 100% |
| 32 | Tab | link "declaration-culture.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 33 | Tab | link "Rosa Camarena Olalla" | main | ✓ 1038 · 1038 · outline | meets | 100% |
| 34 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 35 | Tab | link "Tomás Aguaviva Pinilla" | main | ✓ 1056 · 1056 · outline | meets | 100% |
| 36 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 37 | Tab | link "declaration-social.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 38 | Tab | link "Open the full organisation chart" | main | ✓ 1608 · 1608 · outline | meets | 100% |
| 39 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 40 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 41 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 42 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 43 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 44 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 45 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 46 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 47 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 48 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 49 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 50 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 51 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 52 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 53 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 54 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 55 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 56 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 57 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>Canvas page 2 <code>/home</code> — 63 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "All publications" | main | ✓ 1382 · 1322 · outline | meets | 100% |
| 9 | Tab | link "Document library" | main | ✓ 1466 · 1406 · outline | meets | 100% |
| 10 | Tab | link "Who runs the council" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 11 | Tab | link "Contracts awarded" | navigation "Quick access" | ✓ 1952 · 1884 · outline | meets | 100% |
| 12 | Tab | link "Grants awarded" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 13 | Tab | link "Agreements signed" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 14 | Tab | link "Budgets, accounts and plans" | navigation "Quick access" | ✓ 1952 · 1884 · outline | meets | 100% |
| 15 | Tab | link "Open data" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 16 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 2226 · 2226 · outline | meets | 100% |
| 17 | Tab | link "Register of public contracts awarded, 2024" | main | ✓ 2172 · 2172 · outline | meets | 100% |
| 18 | Tab | link "Grant for the restoration of the fountain in the old …" | main | ✓ 2406 · 2406 · outline | meets | 100% |
| 19 | Tab | link "See all published records" | main | ✓ 1224 · 1224 · outline | meets | 100% |
| 20 | Tab | generic "Contracts and grants by service area, ordered by the …" | main | ✓ 9606 · 9606 · outline | meets | 100% |
| 21 | Tab | combobox "Type" | form | ✓ 2216 · 2148 · outline | meets | 100% |
| 22 | Tab | textbox "Search" | form | ✓ 2222 · 2154 · outline | meets | 100% |
| 23 | Tab | combobox "Area" | form | ✓ 2216 · 2148 · outline | meets | 100% |
| 24 | Tab | button "Search" | form | ✓ 1094 · 1026 · outline | meets | 100% |
| 25 | Tab | generic "One row per published record, of any type." | main | ✓ 9330 · 9330 · outline | meets | 100% |
| 26 | Tab | link "Title" | main | ✓ 426 · 426 · outline | meets | 100% |
| 27 | Tab | link "Updated Sort ascending" | main | ✓ 696 · 696 · outline | meets | 100% |
| 28 | Tab | link "Register of public contracts awarded, 2024" | main | ✓ 2154 · 2154 · outline | meets | 100% |
| 29 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 30 | Tab | link "Municipal plan for equality between women and men …" | main | ✓ 3018 · 3018 · outline | meets | 100% |
| 31 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 32 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 2178 · 2178 · outline | meets | 100% |
| 33 | Tab | link "Urban Planning and Works" | main | ✓ 1374 · 1374 · outline | meets | 100% |
| 34 | Tab | link "Supply of electricity to municipal buildings 2024" | main | ✓ 2388 · 2388 · outline | meets | 100% |
| 35 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 36 | Tab | link "Grant for the annual programme of the local music school" | main | ✓ 2760 · 2760 · outline | meets | 100% |
| 37 | Tab | link "Culture, Education and Sport" | main | ✓ 1356 · 1356 · outline | meets | 100% |
| 38 | Tab | link "Browse everything this council publishes" | main | ✓ 2016 · 2016 · outline | meets | 100% |
| 39 | Tab | link "Document" | main | ✓ 2189 · 1150 · outline+border+color+textDecoration | meets | 100% |
| 40 | Tab | link "Person" | main | ✓ 1531 · 854 · outline+border+color+textDecoration | meets | 100% |
| 41 | Tab | link "Contract" | main | ✓ 1889 · 1022 · outline+border+color+textDecoration | meets | 100% |
| 42 | Tab | link "Dataset" | main | ✓ 1787 · 934 · outline+border+color+textDecoration | meets | 100% |
| 43 | Tab | link "Grant" | main | ✓ 1343 · 750 · outline+border+color+textDecoration | meets | 100% |
| 44 | Tab | link "Agreement" | main | ✓ 2381 · 1216 · outline+border+color+textDecoration | meets | 100% |
| 45 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 46 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 99.6% |
| 47 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 48 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 49 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 50 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 51 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 52 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 53 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 54 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 55 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 56 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 57 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 58 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 59 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 60 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 61 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 62 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 63 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>front page <code>/</code> — 63 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "All publications" | main | ✓ 1382 · 1322 · outline | meets | 100% |
| 9 | Tab | link "Document library" | main | ✓ 1466 · 1406 · outline | meets | 100% |
| 10 | Tab | link "Who runs the council" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 11 | Tab | link "Contracts awarded" | navigation "Quick access" | ✓ 1952 · 1884 · outline | meets | 100% |
| 12 | Tab | link "Grants awarded" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 13 | Tab | link "Agreements signed" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 14 | Tab | link "Budgets, accounts and plans" | navigation "Quick access" | ✓ 1952 · 1884 · outline | meets | 100% |
| 15 | Tab | link "Open data" | navigation "Quick access" | ✓ 1958 · 1890 · outline | meets | 100% |
| 16 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 2226 · 2226 · outline | meets | 100% |
| 17 | Tab | link "Register of public contracts awarded, 2024" | main | ✓ 2172 · 2172 · outline | meets | 100% |
| 18 | Tab | link "Grant for the restoration of the fountain in the old …" | main | ✓ 2406 · 2406 · outline | meets | 100% |
| 19 | Tab | link "See all published records" | main | ✓ 1224 · 1224 · outline | meets | 100% |
| 20 | Tab | generic "Contracts and grants by service area, ordered by the …" | main | ✓ 9606 · 9606 · outline | meets | 100% |
| 21 | Tab | combobox "Type" | form | ✓ 2216 · 2148 · outline | meets | 100% |
| 22 | Tab | textbox "Search" | form | ✓ 2222 · 2154 · outline | meets | 100% |
| 23 | Tab | combobox "Area" | form | ✓ 2216 · 2148 · outline | meets | 100% |
| 24 | Tab | button "Search" | form | ✓ 1094 · 1026 · outline | meets | 100% |
| 25 | Tab | generic "One row per published record, of any type." | main | ✓ 9330 · 9330 · outline | meets | 100% |
| 26 | Tab | link "Title" | main | ✓ 426 · 426 · outline | meets | 100% |
| 27 | Tab | link "Updated Sort ascending" | main | ✓ 696 · 696 · outline | meets | 100% |
| 28 | Tab | link "Register of public contracts awarded, 2024" | main | ✓ 2154 · 2154 · outline | meets | 100% |
| 29 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 30 | Tab | link "Municipal plan for equality between women and men …" | main | ✓ 3018 · 3018 · outline | meets | 100% |
| 31 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 32 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 2178 · 2178 · outline | meets | 100% |
| 33 | Tab | link "Urban Planning and Works" | main | ✓ 1374 · 1374 · outline | meets | 100% |
| 34 | Tab | link "Supply of electricity to municipal buildings 2024" | main | ✓ 2388 · 2388 · outline | meets | 100% |
| 35 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 36 | Tab | link "Grant for the annual programme of the local music school" | main | ✓ 2760 · 2760 · outline | meets | 100% |
| 37 | Tab | link "Culture, Education and Sport" | main | ✓ 1356 · 1356 · outline | meets | 100% |
| 38 | Tab | link "Browse everything this council publishes" | main | ✓ 2016 · 2016 · outline | meets | 100% |
| 39 | Tab | link "Document" | main | ✓ 2189 · 1150 · outline+border+color+textDecoration | meets | 100% |
| 40 | Tab | link "Person" | main | ✓ 1531 · 854 · outline+border+color+textDecoration | meets | 100% |
| 41 | Tab | link "Contract" | main | ✓ 1889 · 1022 · outline+border+color+textDecoration | meets | 100% |
| 42 | Tab | link "Dataset" | main | ✓ 1787 · 934 · outline+border+color+textDecoration | meets | 100% |
| 43 | Tab | link "Grant" | main | ✓ 1343 · 750 · outline+border+color+textDecoration | meets | 100% |
| 44 | Tab | link "Agreement" | main | ✓ 2381 · 1216 · outline+border+color+textDecoration | meets | 100% |
| 45 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 46 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 99.6% |
| 47 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 48 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 49 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 50 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 51 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 52 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 53 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 54 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 55 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 56 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 57 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 58 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 59 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 60 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 61 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 62 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 63 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>contracts register <code>/contracts</code> — 72 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Documents" | navigation "Main navigation" | ✓ 342 · 342 · outline | below | 100% |
| 5 | Tab | link "People" | navigation "Main navigation" | ✓ 363 · 363 · outline | meets | 100% |
| 6 | Tab | link "Contracts" | navigation "Main navigation" | ✓ 423 · 423 · outline | meets | 100% |
| 7 | Tab | link "Agreements" | navigation "Main navigation" | ✓ 474 · 474 · outline | meets | 100% |
| 8 | Tab | link "Grants" | navigation "Main navigation" | ✓ 357 · 357 · outline | meets | 100% |
| 9 | Tab | link "Datasets" | navigation "Main navigation" | ✓ 402 · 402 · outline | meets | 100% |
| 10 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 11 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 12 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 13 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 14 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 15 | Tab | combobox "Area" | form | ✓ 3236 · 3168 · outline | meets | 100% |
| 16 | Tab | combobox "Status" | form | ✓ 3242 · 3174 · outline | meets | 100% |
| 17 | Tab | button "Filter" | form | ✓ 914 · 846 · outline | meets | 100% |
| 18 | Tab | generic "One row per published contract." | main | ✓ 4800 · 4800 · outline | meets | 75.5% |
| 19 | Tab | link "Title Sort descending" | main | ✓ 522 · 522 · outline | meets | 100% |
| 20 | Tab | link "Award amount" | main | ✓ 858 · 858 · outline | meets | 100% |
| 21 | Tab | link "Number of bidders" | main | ✓ 1116 · 1116 · outline | meets | 100% |
| 22 | Tab | link "Awardee" | main | ✓ 606 · 606 · outline | meets | 100% |
| 23 | Tab | link "Subject" | main | ✓ 564 · 564 · outline | meets | 100% |
| 24 | Tab | link "Tender amount" | main | ✓ 882 · 882 · outline | meets | 100% |
| 25 | Tab | link "Annual audit of the municipal accounts 2024" | main | ✓ 1254 · 1254 · outline | below | 100% |
| 26 | Tab | link "Finance and Budget" | main | ✓ 876 · 876 · outline | meets | 100% |
| 27 | Tab | link "Minor contract" | main | ✓ 846 · 846 · outline | meets | 14.3% |
| 28 | Tab | link "Completed" | main | ✓ 684 · 684 · outline | meets | 100% |
| 29 | Tab | link "Emergency repair of the water main at the sports ground" | main | ✓ 1380 · 1380 · outline | below | 100% |
| 30 | Tab | link "Environment and Public Space" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 31 | Tab | link "Negotiated procedure without publication" | main | ✓ 1278 · 1278 · outline | below | 20.8% |
| 32 | Tab | link "Completed" | main | ✓ 684 · 684 · outline | meets | 100% |
| 33 | Tab | link "Refurbishment of the municipal library roof" | main | ✓ 1200 · 1200 · outline | below | 100% |
| 34 | Tab | link "Urban Planning and Works" | main | ✓ 1026 · 1026 · outline | meets | 100% |
| 35 | Tab | link "Simplified open procedure" | main | ✓ 1038 · 1038 · outline | meets | 16.1% |
| 36 | Tab | link "Completed" | main | ✓ 684 · 684 · outline | meets | 100% |
| 37 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 1236 · 1236 · outline | below | 100% |
| 38 | Tab | link "Urban Planning and Works" | main | ✓ 1026 · 1026 · outline | meets | 100% |
| 39 | Tab | link "Simplified open procedure" | main | ✓ 1038 · 1038 · outline | meets | 16.1% |
| 40 | Tab | link "Awarded" | main | ✓ 588 · 588 · outline | meets | 100% |
| 41 | Tab | link "Street lighting maintenance service 2024-2026" | main | ✓ 1350 · 1350 · outline | below | 100% |
| 42 | Tab | link "Environment and Public Space" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 43 | Tab | link "Open procedure" | main | ✓ 912 · 912 · outline | meets | 12.9% |
| 44 | Tab | link "In force" | main | ✓ 534 · 534 · outline | meets | 100% |
| 45 | Tab | link "Supply of electricity to municipal buildings 2024" | main | ✓ 1464 · 1464 · outline | below | 99.5% |
| 46 | Tab | link "Finance and Budget" | main | ✓ 876 · 876 · outline | meets | 100% |
| 47 | Tab | link "Open procedure" | main | ✓ 912 · 912 · outline | meets | 12.9% |
| 48 | Tab | link "Completed" | main | ✓ 684 · 684 · outline | meets | 100% |
| 49 | Tab | link "Supply of uniforms and protective equipment for the …" | main | ✓ 1680 · 1680 · outline | below | 100% |
| 50 | Tab | link "Municipal Police" | main | ✓ 924 · 924 · outline | meets | 100% |
| 51 | Tab | link "Minor contract" | main | ✓ 846 · 846 · outline | meets | 14.3% |
| 52 | Tab | link "Completed" | main | ✓ 684 · 684 · outline | meets | 100% |
| 53 | Tab | generic "How many published contracts were awarded under each …" | main | ✓ 8910 · 8910 · outline | meets | 100% |
| 54 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 55 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 56 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 57 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 58 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 59 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 60 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 61 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 62 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 63 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 64 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 65 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 66 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 67 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 68 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 69 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 70 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 71 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 72 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>publications register <code>/publications</code> — 101 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Documents" | navigation "Main navigation" | ✓ 342 · 342 · outline | below | 100% |
| 5 | Tab | link "People" | navigation "Main navigation" | ✓ 363 · 363 · outline | meets | 100% |
| 6 | Tab | link "Contracts" | navigation "Main navigation" | ✓ 423 · 423 · outline | meets | 100% |
| 7 | Tab | link "Agreements" | navigation "Main navigation" | ✓ 474 · 474 · outline | meets | 100% |
| 8 | Tab | link "Grants" | navigation "Main navigation" | ✓ 357 · 357 · outline | meets | 100% |
| 9 | Tab | link "Datasets" | navigation "Main navigation" | ✓ 402 · 402 · outline | meets | 100% |
| 10 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 11 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 12 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 13 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 14 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 15 | Tab | link "Finance and Budget" | main | ✓ 2391 · 1248 · outline+border+color+textDecoration | meets | 100% |
| 16 | Tab | link "Mayor's Office" | main | ✓ 1825 · 962 · outline+border+color+textDecoration | meets | 100% |
| 17 | Tab | link "Social Services" | main | ✓ 1907 · 1017 · outline+border+color+textDecoration | meets | 100% |
| 18 | Tab | link "Environment and Public Space" | main | ✓ 3372 · 1644 · outline+border+color+textDecoration | meets | 100% |
| 19 | Tab | link "Culture, Education and Sport" | main | ✓ 3226 · 1595 · outline+border+color+textDecoration | meets | 100% |
| 20 | Tab | link "Urban Planning and Works" | main | ✓ 2972 · 1422 · outline+border+color+textDecoration | meets | 100% |
| 21 | Tab | combobox "Type" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 22 | Tab | textbox "Search" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 23 | Tab | combobox "Area" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 24 | Tab | button "Search" | form | ✓ 992 · 924 · outline | meets | 100% |
| 25 | Tab | generic "One row per published record, of any type." | main | ✓ 4800 · 4800 · outline | meets | 52% |
| 26 | Tab | link "Title" | main | ✓ 426 · 426 · outline | meets | 100% |
| 27 | Tab | link "Updated Sort ascending" | main | ✓ 696 · 696 · outline | meets | 100% |
| 28 | Tab | link "Register of public contracts awarded, 2024" | main | ✓ 2154 · 2154 · outline | meets | 100% |
| 29 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 30 | Tab | link "Municipal plan for equality between women and men …" | main | ✓ 3018 · 3018 · outline | meets | 100% |
| 31 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 32 | Tab | link "Resurfacing of the Camino Viejo access road" | main | ✓ 2178 · 2178 · outline | meets | 100% |
| 33 | Tab | link "Urban Planning and Works" | main | ✓ 1380 · 1380 · outline | meets | 100% |
| 34 | Tab | link "Supply of electricity to municipal buildings 2024" | main | ✓ 2388 · 2388 · outline | meets | 100% |
| 35 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 36 | Tab | link "Grant for the annual programme of the local music school" | main | ✓ 2760 · 2760 · outline | meets | 100% |
| 37 | Tab | link "Culture, Education and Sport" | main | ✓ 1494 · 1494 · outline | meets | 100% |
| 38 | Tab | link "Municipal budget 2023" | main | ✓ 1266 · 1266 · outline | meets | 100% |
| 39 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 40 | Tab | link "Agreement with the regional university on student work …" | main | ✓ 2814 · 2814 · outline | meets | 100% |
| 41 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 42 | Tab | link "Tomás Aguaviva Pinilla" | main | ✓ 1212 · 1212 · outline | meets | 100% |
| 43 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 44 | Tab | link "Annual audit of the municipal accounts 2024" | main | ✓ 2226 · 2226 · outline | meets | 100% |
| 45 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 46 | Tab | link "Agreement with the residents' association on the …" | main | ✓ 3030 · 3030 · outline | meets | 100% |
| 47 | Tab | link "Culture, Education and Sport" | main | ✓ 1494 · 1494 · outline | meets | 100% |
| 48 | Tab | link "Minutes of the ordinary council meeting of 28 November …" | main | ✓ 3012 · 3012 · outline | meets | 100% |
| 49 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 50 | Tab | link "Budget execution report, third quarter 2024" | main | ✓ 2190 · 2190 · outline | meets | 100% |
| 51 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 52 | Tab | link "Public notice of the register of interests of elected …" | main | ✓ 2868 · 2868 · outline | meets | 100% |
| 53 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 54 | Tab | link "Grant for the restoration of the fountain in the old …" | main | ✓ 2742 · 2742 · outline | meets | 100% |
| 55 | Tab | link "Urban Planning and Works" | main | ✓ 1380 · 1380 · outline | meets | 100% |
| 56 | Tab | link "Report on the state of municipal roads and pavements" | main | ✓ 2592 · 2592 · outline | meets | 100% |
| 57 | Tab | link "Urban Planning and Works" | main | ✓ 1380 · 1380 · outline | meets | 100% |
| 58 | Tab | link "By-law on public space and civic conduct" | main | ✓ 2016 · 2016 · outline | meets | 100% |
| 59 | Tab | link "Environment and Public Space" | main | ✓ 1398 · 1398 · outline | meets | 100% |
| 60 | Tab | link "Budget execution report, second quarter 2024" | main | ✓ 2298 · 2298 · outline | meets | 100% |
| 61 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 62 | Tab | link "Register of grants awarded, 2024" | main | ✓ 1728 · 1728 · outline | meets | 100% |
| 63 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 64 | Tab | link "Budget execution, financial year 2024" | main | ✓ 1926 · 1926 · outline | meets | 100% |
| 65 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 66 | Tab | link "Nuria Vallejera Sanz" | main | ✓ 1092 · 1092 · outline | meets | 100% |
| 67 | Tab | link "Culture, Education and Sport" | main | ✓ 1494 · 1494 · outline | meets | 100% |
| 68 | Tab | link "Budget execution report, fourth quarter 2024" | main | ✓ 2256 · 2256 · outline | meets | 100% |
| 69 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 70 | Tab | link "Budget execution report, first quarter 2024" | main | ✓ 2160 · 2160 · outline | meets | 100% |
| 71 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 72 | Tab | link "Municipal budget 2022" | main | ✓ 1266 · 1266 · outline | meets | 100% |
| 73 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 74 | Tab | link "Inventory of street trees" | main | ✓ 1272 · 1272 · outline | meets | 100% |
| 75 | Tab | link "Environment and Public Space" | main | ✓ 1398 · 1398 · outline | meets | 100% |
| 76 | Tab | link "Municipal plan against loneliness in older age" | main | ✓ 2220 · 2220 · outline | meets | 100% |
| 77 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 78 | Tab | link "Page 1" | navigation "Pagination" | ✓ 612 · 612 · outline | meets | 100% |
| 79 | Tab | link "Page 2" | navigation "Pagination" | ✓ 612 · 610 · outline | meets | 100% |
| 80 | Tab | link "Page 3" | navigation "Pagination" | ✓ 612 · 612 · outline | meets | 100% |
| 81 | Tab | link "Next page" | navigation "Pagination" | ✓ 708 · 708 · outline | meets | 100% |
| 82 | Tab | link "Last page" | navigation "Pagination" | ✓ 702 · 702 · outline | meets | 100% |
| 83 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 84 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 85 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 86 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 87 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 88 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 89 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 90 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 91 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 92 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 93 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 94 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 95 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 96 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 97 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 98 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 99 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 100 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 101 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>people register <code>/people</code> — 62 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Documents" | navigation "Main navigation" | ✓ 342 · 342 · outline | below | 100% |
| 5 | Tab | link "People" | navigation "Main navigation" | ✓ 363 · 363 · outline | meets | 100% |
| 6 | Tab | link "Contracts" | navigation "Main navigation" | ✓ 423 · 423 · outline | meets | 100% |
| 7 | Tab | link "Agreements" | navigation "Main navigation" | ✓ 474 · 474 · outline | meets | 100% |
| 8 | Tab | link "Grants" | navigation "Main navigation" | ✓ 357 · 357 · outline | meets | 100% |
| 9 | Tab | link "Datasets" | navigation "Main navigation" | ✓ 402 · 402 · outline | meets | 100% |
| 10 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 11 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 12 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 13 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 14 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 15 | Tab | combobox "Area" | form | ✓ 6266 · 6198 · outline | meets | 100% |
| 16 | Tab | button "Filter" | form | ✓ 914 · 846 · outline | meets | 100% |
| 17 | Tab | generic "One row per person in the organisation chart." | main | ✓ 4800 · 4800 · outline | meets | 69.2% |
| 18 | Tab | link "Full name Sort descending" | main | ✓ 756 · 756 · outline | meets | 100% |
| 19 | Tab | link "Position" | main | ✓ 582 · 582 · outline | meets | 100% |
| 20 | Tab | link "Annual remuneration" | main | ✓ 1122 · 1122 · outline | meets | 100% |
| 21 | Tab | link "Severance payment" | main | ✓ 1122 · 1122 · outline | meets | 100% |
| 22 | Tab | link "Álvaro Menchón Serna" | main | ✓ 1062 · 1062 · outline | meets | 100% |
| 23 | Tab | link "Environment and Public Space" | main | ✓ 1092 · 1092 · outline | meets | 100% |
| 24 | Tab | link "declaration-environment.pdf" | main | ✓ 1080 · 1080 · outline | meets | 100% |
| 25 | Tab | link "Elena Rebollar Quintana" | main | ✓ 990 · 990 · outline | meets | 100% |
| 26 | Tab | link "Urban Planning and Works" | main | ✓ 1032 · 1032 · outline | meets | 100% |
| 27 | Tab | link "declaration-planning.pdf" | main | ✓ 912 · 912 · outline | meets | 100% |
| 28 | Tab | link "Ignacio Cardeñosa Vela" | main | ✓ 1038 · 1038 · outline | meets | 100% |
| 29 | Tab | link "Finance and Budget" | main | ✓ 882 · 882 · outline | meets | 100% |
| 30 | Tab | link "declaration-deputy.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 31 | Tab | link "Javier Otazua Lumbreras" | main | ✓ 930 · 930 · outline | meets | 100% |
| 32 | Tab | link "Finance and Budget" | main | ✓ 882 · 882 · outline | meets | 100% |
| 33 | Tab | link "Marta Belloso Iriarte" | main | ✓ 960 · 960 · outline | meets | 100% |
| 34 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 35 | Tab | link "declaration-mayor.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 36 | Tab | link "Nuria Vallejera Sanz" | main | ✓ 996 · 996 · outline | meets | 99.6% |
| 37 | Tab | link "Culture, Education and Sport" | main | ✓ 1122 · 1122 · outline | meets | 99.7% |
| 38 | Tab | link "declaration-culture.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 39 | Tab | link "Rosa Camarena Olalla" | main | ✓ 1038 · 1038 · outline | meets | 100% |
| 40 | Tab | link "Mayor's Office" | main | ✓ 840 · 840 · outline | meets | 100% |
| 41 | Tab | link "Tomás Aguaviva Pinilla" | main | ✓ 1056 · 1056 · outline | meets | 100% |
| 42 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 43 | Tab | link "declaration-social.pdf" | main | ✓ 882 · 882 · outline | meets | 100% |
| 44 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 45 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 46 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 47 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 48 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 49 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 50 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 51 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 52 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 53 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 54 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 55 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 56 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 57 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 58 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 59 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 60 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 61 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 62 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>library register <code>/library</code> — 112 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 9 | Tab | textbox "Search" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 10 | Tab | combobox "Area" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 11 | Tab | combobox "Financial year" | form | ✓ 2204 · 2136 · outline | meets | 100% |
| 12 | Tab | button "Search" | form | ✓ 992 · 924 · outline | meets | 100% |
| 13 | Tab | generic "One row per published document or dataset." | main | ✓ 4800 · 4800 · outline | meets | 60.5% |
| 14 | Tab | link "Title Sort descending" | main | ✓ 522 · 522 · outline | meets | 100% |
| 15 | Tab | link "Annual accounts 2022" | main | ✓ 1236 · 1236 · outline | meets | 100% |
| 16 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 17 | Tab | link "2022" | main | ✓ 462 · 462 · outline | meets | 100% |
| 18 | Tab | link "Annual accounts 2023" | main | ✓ 1236 · 1236 · outline | meets | 100% |
| 19 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 20 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 21 | Tab | link "Annual report of the municipal library 2023" | main | ✓ 2160 · 2160 · outline | meets | 100% |
| 22 | Tab | link "Culture, Education and Sport" | main | ✓ 1488 · 1488 · outline | meets | 100% |
| 23 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 24 | Tab | link "Annual report on the home help service 2023" | main | ✓ 2238 · 2238 · outline | meets | 100% |
| 25 | Tab | link "Social Services" | main | ✓ 882 · 882 · outline | meets | 100% |
| 26 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 27 | Tab | link "Budget execution report, first quarter 2024" | main | ✓ 2160 · 2160 · outline | meets | 100% |
| 28 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 29 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 30 | Tab | link "Budget execution report, fourth quarter 2024" | main | ✓ 2256 · 2256 · outline | meets | 100% |
| 31 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 32 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 33 | Tab | link "Budget execution report, second quarter 2024" | main | ✓ 2298 · 2298 · outline | meets | 100% |
| 34 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 35 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 36 | Tab | link "Budget execution report, third quarter 2024" | main | ✓ 2190 · 2190 · outline | meets | 100% |
| 37 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 38 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 39 | Tab | link "Budget execution, financial year 2023" | main | ✓ 1926 · 1926 · outline | meets | 100% |
| 40 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 41 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 42 | Tab | link "Budget execution, financial year 2024" | main | ✓ 1926 · 1926 · outline | meets | 100% |
| 43 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 44 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 45 | Tab | link "By-law on public space and civic conduct" | main | ✓ 2016 · 2016 · outline | meets | 100% |
| 46 | Tab | link "Environment and Public Space" | main | ✓ 1548 · 1548 · outline | meets | 100% |
| 47 | Tab | link "2022" | main | ✓ 462 · 462 · outline | meets | 100% |
| 48 | Tab | link "By-law on the municipal waste collection charge" | main | ✓ 2334 · 2334 · outline | meets | 100% |
| 49 | Tab | link "Environment and Public Space" | main | ✓ 1548 · 1548 · outline | meets | 100% |
| 50 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 51 | Tab | link "By-law on the tax on construction and works" | main | ✓ 2166 · 2166 · outline | meets | 100% |
| 52 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 53 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 54 | Tab | link "By-law on the use of municipal sports facilities" | main | ✓ 2268 · 2268 · outline | meets | 100% |
| 55 | Tab | link "Culture, Education and Sport" | main | ✓ 1488 · 1488 · outline | meets | 100% |
| 56 | Tab | link "2022" | main | ✓ 462 · 462 · outline | meets | 100% |
| 57 | Tab | link "Climate and energy action plan 2023-2030" | main | ✓ 2172 · 2172 · outline | meets | 98.5% |
| 58 | Tab | link "Environment and Public Space" | main | ✓ 1548 · 1548 · outline | meets | 98.5% |
| 59 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 98.5% |
| 60 | Tab | link "Inventory of street trees" | main | ✓ 1272 · 1272 · outline | meets | 100% |
| 61 | Tab | link "Environment and Public Space" | main | ✓ 1548 · 1548 · outline | meets | 100% |
| 62 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 63 | Tab | link "Minutes of the extraordinary council meeting of 12 …" | main | ✓ 3276 · 3276 · outline | meets | 100% |
| 64 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 65 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 66 | Tab | link "Minutes of the ordinary council meeting of 23 February …" | main | ✓ 2958 · 2958 · outline | meets | 100% |
| 67 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 68 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 100% |
| 69 | Tab | link "Minutes of the ordinary council meeting of 25 April 2024" | main | ✓ 2772 · 2772 · outline | meets | 100% |
| 70 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 71 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 72 | Tab | link "Minutes of the ordinary council meeting of 27 June 2024" | main | ✓ 2754 · 2754 · outline | meets | 100% |
| 73 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 74 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 75 | Tab | link "Minutes of the ordinary council meeting of 28 February …" | main | ✓ 2958 · 2958 · outline | meets | 100% |
| 76 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 77 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 78 | Tab | link "Minutes of the ordinary council meeting of 28 November …" | main | ✓ 3012 · 3012 · outline | meets | 100% |
| 79 | Tab | link "Mayor's Office" | main | ✓ 834 · 834 · outline | meets | 100% |
| 80 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 81 | Tab | link "Municipal budget 2022" | main | ✓ 1266 · 1266 · outline | meets | 100% |
| 82 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 83 | Tab | link "2022" | main | ✓ 462 · 462 · outline | meets | 100% |
| 84 | Tab | link "Municipal budget 2023" | main | ✓ 1266 · 1266 · outline | meets | 98.5% |
| 85 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 98.5% |
| 86 | Tab | link "2023" | main | ✓ 462 · 462 · outline | meets | 98.5% |
| 87 | Tab | link "Municipal budget 2024" | main | ✓ 1266 · 1266 · outline | meets | 100% |
| 88 | Tab | link "Finance and Budget" | main | ✓ 1086 · 1086 · outline | meets | 100% |
| 89 | Tab | link "2024" | main | ✓ 462 · 462 · outline | meets | 100% |
| 90 | Tab | link "Page 1" | navigation "Pagination" | ✓ 612 · 612 · outline | meets | 100% |
| 91 | Tab | link "Page 2" | navigation "Pagination" | ✓ 612 · 610 · outline | meets | 100% |
| 92 | Tab | link "Next page" | navigation "Pagination" | ✓ 708 · 708 · outline | meets | 100% |
| 93 | Tab | link "Last page" | navigation "Pagination" | ✓ 702 · 702 · outline | meets | 100% |
| 94 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 95 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 96 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 97 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 98 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 99 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 100 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 101 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 102 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 103 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 104 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 105 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 106 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 107 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 108 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 109 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 110 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 111 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 112 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>record page <code>/contracts/resurfacing-camino-viejo-access-road</code> — 31 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 9 | Tab | link "Contracts" | navigation "Breadcrumb" | ✓ 630 · 630 · outline | meets | 100% |
| 10 | Tab | link "Simplified open procedure" | main | ✓ 1380 · 1380 · outline | meets | 100% |
| 11 | Tab | link "Urban Planning and Works" | main | ✓ 1374 · 1374 · outline | meets | 100% |
| 12 | Tab | link "Awarded" | main | ✓ 594 · 594 · outline | meets | 100% |
| 13 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 14 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 15 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 16 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 17 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 18 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 19 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 20 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 21 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 22 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 23 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 24 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 25 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 26 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 27 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 28 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 29 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 30 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 31 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>

<details><summary>not-found page <code>/agora-transparency-no-such-route</code> — 27 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 2893 · 1907 · outline+border+background+color+textDecoration | meets | 53% |
| 2 | Tab | link "Agora keyboard rig" | banner | ✓ 1938 · 1938 · outline | meets | 100% |
| 3 | Tab | link "All publications" | navigation "Main navigation" | ✓ 1062 · 1062 · outline | meets | 100% |
| 4 | Tab | link "Document library" | navigation "Main navigation" | ✓ 1146 · 1146 · outline | meets | 100% |
| 5 | Tab | link "The institution" | navigation "Main navigation" | ✓ 1014 · 1014 · outline | meets | 100% |
| 6 | Tab | searchbox "Search published records" | search "Search published records" | ✓ 1772 · 1704 · outline | meets | 100% |
| 7 | Tab | button "Search" | search "Search published records" | ✓ 620 · 552 · outline | meets | 100% |
| 8 | Tab | link "Home" | navigation "Breadcrumb" | ✓ 468 · 468 · outline | meets | 100% |
| 9 | Tab | link "Agora keyboard rig" | contentinfo | ✓ 1494 · 1494 · outline | meets | 100% |
| 10 | Tab | link "All publications" | navigation "Find a record" | ✓ 1044 · 1044 · outline | meets | 100% |
| 11 | Tab | link "Document library" | navigation "Find a record" | ✓ 1122 · 1122 · outline | meets | 100% |
| 12 | Tab | link "Contracts awarded" | navigation "Public money" | ✓ 1194 · 1194 · outline | meets | 100% |
| 13 | Tab | link "Grants awarded" | navigation "Public money" | ✓ 1056 · 1056 · outline | meets | 100% |
| 14 | Tab | link "Agreements signed" | navigation "Public money" | ✓ 1218 · 1218 · outline | meets | 100% |
| 15 | Tab | link "Budgets, accounts and plans" | navigation "Documents and data" | ✓ 1494 · 1494 · outline | meets | 100% |
| 16 | Tab | link "Open data" | navigation "Documents and data" | ✓ 804 · 804 · outline | meets | 100% |
| 17 | Tab | link "Who runs the council" | navigation "The institution" | ✓ 1284 · 1284 · outline | meets | 100% |
| 18 | Tab | link "People and their pay" | navigation "The institution" | ✓ 1260 · 1260 · outline | meets | 100% |
| 19 | Tab | link "Accessibility statement" | navigation "Legal and accessibility" | ✓ 1392 · 1392 · outline | meets | 100% |
| 20 | Tab | link "Legal notice" | navigation "Legal and accessibility" | ✓ 894 · 894 · outline | meets | 100% |
| 21 | Tab | link "Privacy notice" | navigation "Legal and accessibility" | ✓ 972 · 972 · outline | meets | 100% |
| 22 | Tab | link "Cookies" | navigation "Legal and accessibility" | ✓ 702 · 702 · outline | meets | 100% |
| 23 | Tab | link "Drupal" | contentinfo | ✓ 462 · 457 · outline | meets | 100% |
| 24 | Tab | link "Facebook" | navigation "Follow us" | ✓ 2261 · 560 · outline+border+background | meets | 100% |
| 25 | Tab | link "X" | navigation "Follow us" | ✓ 2475 · 560 · outline+border+background | meets | 100% |
| 26 | Tab | link "Instagram" | navigation "Follow us" | ✓ 2381 · 560 · outline+border+background | meets | 100% |
| 27 | Tab | link "YouTube" | navigation "Follow us" | ✓ 2218 · 560 · outline+border+background | meets | 100% |

</details>


---

## 3 · The admin surface — T-0633's scope — MEASURED, reported not fixed

**What was walked.** Logged in as user 1 through a one-time link. Every `config_guardian` route
that takes no argument, derived from the router (§A.3): 14, of which 3 answer JSON and are skipped
**by content type**, leaving **11 HTML routes** — the dashboard, activity, analyze, settings,
snapshots, snapshot add and import, sync, sync export and import, and the dependency-graph
document that `/analyze` embeds as an iframe. Each at both widths. **Gin** renders all of it,
framed by core's `navigation` sidebar and top bar and by `coffee`; `agora_theme` renders none of
it. **Buttons were not pressed** (§7 item 6).

| measure | result |
|---|---|
| page-widths walked · harness failures | 22 · **0** (plus 6 JSON responses skipped) |
| Tab stops reached / predicted | **486 / 498** — 16 unreached, 4 reached but not predicted |
| walks ending cleanly · traps · revisits | **44 of 44** · **0** · 14 |
| reverse walk mirrors forward | 12 of 22 |
| indicator visible · none · faint · off-screen | **442** · **19** · 12 · **13** |
| 2.4.13 (AAA) met | 28 of 486 |
| entirely hidden at landing, forward · reverse | **26 · 15** |
| skip link: moves focus to its target · passes the full in-use check · not applicable | 20 of 20 · 16 of 20 · 2 (no `<main>`) |
| links followed on Enter (action-free links only) | 55 of 55 |
| scroll regions operable · relying on the browser | 13 of 13 · **2** |
| `target-size` violations · passing nodes | **4** · 488 |
| axe default run: violation nodes (T-0603 attributed these; not re-analysed here) | 103 |
| pages scrolling sideways at 320 CSS px | 3 |
| findings the harness calls on its own | **139** |

### 3.1 · Reach, order and the skip link — 1280 × 800

| page | path | reached / predicted | forward · reverse | DOM-order inversions · positive `tabindex` | reverse mirrors forward | skip link (first stop → Enter → next Tab in main) | controls: click = Enter = Space | links followed on Enter | address changes while tabbing |
|---|---|---|---|---|---|---|---|---|---|
| activity | `/admin/config/development/config-guardian/activity` | **30 / 30** | leaves page · wraps | 0 · 0 | yes | **✗** Enter → `#main-content`, focus on `a#main-content`; next Tab: input "" in form — entirely hidden | not run (admin) | 3 of 3 | 0 |
| analyze | `/admin/config/development/config-guardian/analyze` | **32 / 30** | leaves page · wraps | 0 · 0 | yes | **✗** Enter → `#main-content`, focus on `a#main-content`; next Tab: iframe "Interactive Dependency Graph" in main — entirely hidden | not run (admin) | 3 of 3 | 0 |
| dashboard | `/admin/config/development/config-guardian` | **36 / 36** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 5 of 5 | 0 |
| dependency graph iframe | `/admin/config/development/config-guardian/dependency-graph-frame` | **6 / 6** | leaves page · wraps | 0 · 0 | yes | n/a — no `<main>` | not run (admin) | 0 of 0 | 0 |
| settings | `/admin/config/development/config-guardian/settings` | **41 / 43** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 3 of 3 | 0 |
| snapshot add | `/admin/config/development/config-guardian/snapshot/add` | **29 / 31** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 4 of 4 | 0 |
| snapshot import | `/admin/config/development/config-guardian/snapshot/import` | **28 / 30** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 4 of 4 | 0 |
| snapshots | `/admin/config/development/config-guardian/snapshots` | **37 / 37** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 4 of 4 | 0 |
| sync | `/admin/config/development/config-guardian/sync` | **32 / 32** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 3 of 3 | 0 |
| sync export | `/admin/config/development/config-guardian/sync/export` | **32 / 34** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 2 of 2 | 0 |
| sync import | `/admin/config/development/config-guardian/sync/import` | **32 / 32** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 2 of 2 | 0 |

**Where the reverse walk does not mirror the forward one, it is A2**: on the forms with Gin's
sticky actions, script sends focus from the hidden originals to their sticky copies, so the two
directions meet those buttons in a different order. The one up-and-left jump on most pages below
is the jump to A1's or A2's hidden element.

| page | the order focus moves through the page's regions (count of stops in each) | upward jumps · of which up-and-left |
|---|---|---|
| activity | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → navigation "Primary tabs" x6 → form x1 | 2 · 1 |
| analyze | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → navigation "Primary tabs" x6 → main x2 → form x1 | 2 · 0 |
| dashboard | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x4 → (no landmark) x1 → navigation "Primary tabs" x6 → main x6 → form x1 | 2 · 1 |
| dependency graph iframe | (no landmark) x6 | 0 · 0 |
| settings | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → (no landmark) x1 → navigation "Primary tabs" x6 → form x11 | 2 · 1 |
| snapshot add | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → (no landmark) x2 → form x4 | 2 · 1 |
| snapshot import | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → (no landmark) x2 → form x3 | 2 · 1 |
| snapshots | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → navigation "Primary tabs" x6 → main x7 → form x1 | 2 · 1 |
| sync | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x5 → navigation "Primary tabs" x6 → main x2 → form x1 | 3 · 1 |
| sync export | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x6 → (no landmark) x2 → form x6 | 3 · 1 |
| sync import | (no landmark) x1 → navigation "Administrative sidebar" x17 → navigation "Breadcrumb" x6 → (no landmark) x1 → form x7 | 4 · 1 |

### 3.2 · What can be seen — 1280 × 800

| page | stops | indicator visible · none or off-screen · faint | 2.4.13 (AAA) met | entirely hidden (fwd · rev) | partly hidden (fwd) · of which under half | `target-size` pass · violation · incomplete | axe rules · violation nodes | page overflow at this width |
|---|---|---|---|---|---|---|---|---|
| activity | 30 | **29** · 1 · 0 | 1 of 30 | 1 · 1 | 0 · 0 | 30 · 0 · 0 | 93 · 4 | 0 px |
| analyze | 32 | **30** · 2 · 0 | 1 of 32 | 2 · 1 | 0 · 0 | 30 · 0 · 0 | 93 · 13 | 0 px |
| dashboard | 36 | **33** · 1 · 2 | 1 of 36 | 1 · 1 | 0 · 0 | 36 · 0 · 0 | 93 · 6 | 0 px |
| dependency graph iframe | 6 | **6** · 0 · 0 | 0 of 6 | 0 · 0 | 0 · 0 | 6 · 0 · 0 | 91 · 6 | 0 px |
| settings | 41 | **40** · 1 · 0 | 1 of 41 | 1 · 0 | 0 · 0 | 41 · 0 · 0 | 92 · 3 | 0 px |
| snapshot add | 29 | **27** · 2 · 0 | 2 of 29 | 2 · 0 | 0 · 0 | 31 · 0 · 0 | 92 · 3 | 0 px |
| snapshot import | 28 | **26** · 2 · 0 | 2 of 28 | 2 · 0 | 0 · 0 | 31 · 0 · 0 | 92 · 3 | 0 px |
| snapshots | 37 | **36** · 1 · 0 | 2 of 37 | 1 · 1 | 0 · 0 | 37 · 0 · 0 | 92 · 3 | 0 px |
| sync | 32 | **31** · 1 · 0 | 1 of 32 | 1 · 1 | 0 · 0 | 32 · 0 · 0 | 93 · 8 | 0 px |
| sync export | 32 | **29** · 2 · 1 | 2 of 32 | 2 · 0 | 1 · 0 | 33 · 0 · 0 | 92 · 3 | 0 px |
| sync import | 32 | **28** · 3 · 1 | 1 of 32 | 2 · 2 | 0 · 0 | 31 · 0 · 0 | 93 · 4 | 0 px |

### 3.3 · The same at 320 × 256

| page | path | reached / predicted | forward · reverse | DOM-order inversions · positive `tabindex` | reverse mirrors forward | skip link (first stop → Enter → next Tab in main) | controls: click = Enter = Space | links followed on Enter | address changes while tabbing |
|---|---|---|---|---|---|---|---|---|---|
| activity | `/admin/config/development/config-guardian/activity` | **10 / 10** | leaves page · wraps | 0 · 0 | yes | **✗** Enter → `#main-content`, focus on `a#main-content`; next Tab: input "" in form — entirely hidden | not run (admin) | 2 of 2 | 0 |
| analyze | `/admin/config/development/config-guardian/analyze` | **12 / 10** | leaves page · wraps | 0 · 0 | yes | **✗** Enter → `#main-content`, focus on `a#main-content`; next Tab: iframe "Interactive Dependency Graph" in main — entirely hidden | not run (admin) | 2 of 2 | 0 |
| dashboard | `/admin/config/development/config-guardian` | **16 / 16** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 4 of 4 | 0 |
| dependency graph iframe | `/admin/config/development/config-guardian/dependency-graph-frame` | **6 / 6** | leaves page · wraps | 0 · 0 | yes | n/a — no `<main>` | not run (admin) | 0 of 0 | 0 |
| settings | `/admin/config/development/config-guardian/settings` | **21 / 23** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 2 of 2 | 0 |
| snapshot add | `/admin/config/development/config-guardian/snapshot/add` | **13 / 15** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 3 of 3 | 0 |
| snapshot import | `/admin/config/development/config-guardian/snapshot/import` | **12 / 14** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 3 of 3 | 0 |
| snapshots | `/admin/config/development/config-guardian/snapshots` | **17 / 17** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 3 of 3 | 0 |
| sync | `/admin/config/development/config-guardian/sync` | **12 / 12** | leaves page · wraps | 0 · 0 | yes | ✓ | not run (admin) | 1 of 1 | 0 |
| sync export | `/admin/config/development/config-guardian/sync/export` | **16 / 18** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 1 of 1 | 0 |
| sync import | `/admin/config/development/config-guardian/sync/import` | **16 / 16** | leaves page · leaves page | 0 · 0 | **False** | ✓ | not run (admin) | 1 of 1 | 0 |

| page | stops | indicator visible · none or off-screen · faint | 2.4.13 (AAA) met | entirely hidden (fwd · rev) | partly hidden (fwd) · of which under half | `target-size` pass · violation · incomplete | axe rules · violation nodes | page overflow at this width |
|---|---|---|---|---|---|---|---|---|
| activity | 10 | **8** · 1 · 1 | 1 of 10 | 1 · 1 | 0 · 0 | 10 · 0 · 0 | 91 · 3 | 0 px |
| analyze | 12 | **9** · 2 · 1 | 1 of 12 | 2 · 2 | 1 · 0 | 10 · 0 · 0 | 92 · 11 | 0 px |
| dashboard | 16 | **14** · 1 · 1 | 1 of 16 | 1 · 1 | 0 · 0 | 16 · 0 · 0 | 92 · 5 | 0 px |
| dependency graph iframe | 6 | **6** · 0 · 0 | 0 of 6 | 0 · 0 | 0 · 0 | 6 · 0 · 0 | 91 · 8 | 0 px |
| settings | 21 | **19** · 1 · 1 | 1 of 21 | 1 · 0 | 1 · 0 | 21 · 0 · 0 | 91 · 2 | 5 px |
| snapshot add | 13 | **11** · 2 · 0 | 2 of 13 | 0 · 0 | 3 · 3 | 14 · 1 · 0 | 91 · 2 | 0 px |
| snapshot import | 12 | **10** · 2 · 0 | 2 of 12 | 0 · 0 | 3 · 3 | 14 · 1 · 0 | 91 · 2 | 0 px |
| snapshots | 17 | **15** · 1 · 1 | 2 of 17 | 1 · 1 | 2 · 0 | 17 · 0 · 0 | 91 · 2 | 0 px |
| sync | 12 | **10** · 1 · 1 | 1 of 12 | 1 · 1 | 0 · 0 | 12 · 0 · 0 | 92 · 7 | 0 px |
| sync export | 16 | **13** · 2 · 1 | 2 of 16 | 2 · 0 | 1 · 0 | 15 · 2 · 0 | 91 · 2 | 12 px |
| sync import | 16 | **12** · 3 · 1 | 1 of 16 | 2 · 2 | 1 · 0 | 15 · 0 · 0 | 92 · 3 | 12 px |

### 3.4 · Where focus lands partly or wholly out of sight

| width | cause | stops | visible at landing | pages |
|---|---|---|---|---|
| 1280x800 | ENTIRELY hidden at landing: a "+ Cancel" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **1** | 0% | snapshot add |
| 1280x800 | ENTIRELY hidden at landing: a "Cancel" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **3** | 0% | snapshot import, sync export, sync import |
| 1280x800 | ENTIRELY hidden at landing: input "Create Snapshot" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **1** | 0% | snapshot add |
| 1280x800 | ENTIRELY hidden at landing: input "Export Configuration" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **1** | 0% | sync export |
| 1280x800 | ENTIRELY hidden at landing: input "Import Snapshot" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **1** | 0% | snapshot import |
| 1280x800 | ENTIRELY hidden at landing: input "Save configuration" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under div.top-bar.gin--navigation-top-bar | **1** | 0% | settings |
| 1280x800 | ENTIRELY off-screen at landing: iframe "Interactive Dependency Graph" | **1** | 0% | analyze |
| 1280x800 | ENTIRELY off-screen at landing: input "Query" | **6** | 0% | activity, analyze, dashboard, snapshots, sync, sync import |
| 1280x800 | cut by the window edge only (not a defect) | **1** | 89.3% | sync export |
| 320x256 | ENTIRELY hidden at landing: a "Cancel" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under details#edit-changes.cg-changes-details.js-form-wrapper | **1** | 0% | sync import |
| 320x256 | ENTIRELY hidden at landing: a "Cancel" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under summary.claro-details__summary | **1** | 0% | sync export |
| 320x256 | ENTIRELY hidden at landing: input "Export Configuration" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under summary.claro-details__summary | **1** | 0% | sync export |
| 320x256 | ENTIRELY hidden at landing: input "Save configuration" — clipped by div#edit-actions.gin-sticky-form-actions.form-actions; under details#edit-storage.js-form-wrapper.form-wrapper | **1** | 0% | settings |
| 320x256 | ENTIRELY off-screen at landing: iframe "Interactive Dependency Graph" | **1** | 0% | analyze |
| 320x256 | ENTIRELY off-screen at landing: input "Query" | **6** | 0% | activity, analyze, dashboard, snapshots, sync, sync import |
| 320x256 | clipped by div#edit-actions.gin-sticky-form-actions.form-actions | **4** | 0.1–0.6% | snapshot add, snapshot import |
| 320x256 | clipped by div.gin-table-scroll-wrapper.gin-horizontal-scroll-shadow | **2** | 77.4–91.4% | snapshots |
| 320x256 | cut by the window edge only (not a defect) | **3** | 85.6–96.4% | settings, sync export, sync import |
| 320x256 | larger than the window (not a defect) | **1** | 71.1% | analyze |
| 320x256 | under input#gin-sticky-edit-submit.button.button--primary | **2** | 28.6% | snapshot add, snapshot import |
| | **total** | **39** | | |

### 3.5 · What the admin numbers mean, element by element

Every item below is attributed by the element that carries it, read from the recorded run. None
of it is in markup this package owns, and none of it is fixed here (D-061 option B).

**A1 · `coffee` — a search box that takes focus off-screen.** On **12 of
22 page-widths** the walk ends on `input#coffee-q`, inside
`div.coffee-form-wrapper.hide-form`, outside `<main>`, parked above the window (y = −786 at 1280 px,
−242 at 320 px). The harness reports it OFF-SCREEN — no pixel of it is on screen — so a keyboard
user who tabs to the end of an admin page watches focus disappear. **2.4.7 fails.** On the other
8 page-widths it is never reached at all, because focus is sent elsewhere first
(A2), and the dependency-graph document has none. `coffee` arrives through `drupal_cms_admin_ui`.
**It is also why the skip-link check fails on `/activity`**, at both widths: that page's `<main>`
holds nothing focusable, so the first Tab after "Skip to main content" — which does move focus to
`#main-content`, inside `<main>` — leaves `<main>` and lands here.

**A2 · `gin` — form buttons stay in the Tab order after Gin hides them.** On the forms that carry
actions, Gin copies the actions into its sticky header and hides the originals:
`div#edit-actions.gin-sticky-form-actions` clips them away and `div.top-bar.gin--navigation-top-bar`
sits over where they are. They remain focusable. **16 stops on 10 page-widths
put focus on one of those hidden originals — at most 0.6 % of it on screen — and no
pixel changes.** Script then sends focus back to the sticky copies (the 14 revisits)
and the next Tab leaves the page, **so there is no trap, but there are presses with nothing to
see: 2.4.7 and 2.4.11 fail on those forms.** What follows the hidden block in the page — Gin's own
"Moves focus to sticky header actions" link and `coffee`'s box — is never reached
(16 stops in all); no function is lost, because the sticky copies are reached
earlier in the same walk. And where that Gin link *is* reached (sync import, 2
page-widths), it stays visually hidden while focused: **no pixel changes**.

**A3 · `gin` at 320 px — a sticky header over the focused element, a breadcrumb too small, and
three forms wider than the window.** 2 stops land partly under
`input#gin-sticky-edit-submit`, the sticky header's own button — the classic sticky-header case,
at 400 % zoom (2.4.12; not *entirely* hidden, so not 2.4.11). The breadcrumb's links shrink to 81–83 × 16 px
(axe's own measurement) with too little space around them: **the only `target-size` violations measured
anywhere in this file, 4 nodes on 3
page-widths — 2.5.8 fails.** And the page scrolls sideways on 3
page-widths (settings 5 px, sync export 12 px, sync import 12 px), the overflow being `div.top-bar__actions`, the sticky header's action
area — **1.4.10 fails.**

**A4 · `config_guardian` — the dependency-graph iframe on `/analyze` takes focus out of sight,
with no indicator.** When Tab moves focus into the iframe the page does not scroll: at 1280 px the
frame stays 3,658 px below the top of the window, and even centred, focusing it changes
no pixel. **2.4.7 fails.** The next stop, the changes table's scroll container, has no `tabindex`
and is reachable only because Chrome 130 and later make a scroll container focusable on its own —
2 such regions in the run, this one at each width. It is what axe's
`scrollable-region-focusable` reports (T-0603), and in a browser without that behaviour it would
fail 2.1.1. **This is also why the skip-link check fails on `/analyze`**: the next Tab after the
skip link lands on the iframe, inside `<main>` but off-screen.

**A5 · indicators that change, but by less than 3:1.** 12 stops change
pixels on focus but no pixel by 3:1 or more — below the contrast 1.4.11 expects of a focus
indicator: a "Analyze Impact" (dashboard 1280; strongest change 2.95–2.95:1); a "Create Snapshot" (dashboard 1280; strongest change 2.92–2.92:1); summary "675 new configurations" (sync export 1280, sync export 320; strongest change 2.98–2.98:1); summary "675 configurations to delete" (sync import 1280, sync import 320; strongest change 2.99–2.99:1); button "Tabs display toggle" (activity 320, analyze 320, dashboard 320, settings 320, snapshots 320, sync 320; strongest change 2.27–2.27:1). Whether each is *noticed* is the one perception question this surface
leaves for a person (§6).

**A6 · Gin's ring is visible but small.** 2.4.13 is met by 28 of
486 admin stops, against 1107 of 1176 on the
anonymous pages. AAA and not a target; recorded for the contrast.

### 3.6 · The transcripts — every stop, 1280 × 800, forward

The same shape as §2.5, for T-0633. A stop whose indicator reads **OFF-SCREEN** or **NONE**, or
whose "on screen" reads 0 %, is one of A1, A2 or A4.

<details><summary>activity <code>/admin/config/development/config-guardian/activity</code> — 30 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 4344 · 192 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 25 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 4224 · 186 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 26 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 2504 · 100 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 27 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 5864 · 268 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 28 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 1228 · 206 · outline+boxShadow | below | 100% |
| 29 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 3544 · 152 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>

<details><summary>analyze <code>/admin/config/development/config-guardian/analyze</code> — 32 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 4344 · 192 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 25 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 4224 · 186 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 26 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 2504 · 100 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 27 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 1476 · 268 · outline+boxShadow | below | 100% |
| 28 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 4624 · 206 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 29 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 3544 · 152 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | Iframe "Interactive Dependency Graph" | main | **NONE** 0 · 0 · — | below | 0% |
| 31 | Tab | generic "TYPE CONFIGURATION CATEGORY DEPENDENTS IMPACT RISK DEL …" | main | ✓ 10032 · 2496 · boxShadow | below | 100% |
| 32 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>

<details><summary>dashboard <code>/admin/config/development/config-guardian</code> — 36 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 2280 · 524 · boxShadow | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 750 · 172 · outline+boxShadow | below | 100% |
| 23 | Tab | link "+Create Snapshot" | (no landmark) | ✓ 6610 · 164 · outline+boxShadow+border+background | below | 100% |
| 24 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 1172 · 192 · outline+boxShadow | below | 100% |
| 25 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 4224 · 186 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 26 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 2504 · 100 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 27 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 5864 · 268 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 28 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 4624 · 206 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 29 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 3544 · 152 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | link "Analyze Impact" | main | **SUBTLE** 1744 · 0 · outline+boxShadow | below | 100% |
| 31 | Tab | link "Create Snapshot" | main | **SUBTLE** 1806 · 0 · outline+boxShadow | below | 100% |
| 32 | Tab | link "Go to Import" | main | ✓ 1400 · 312 · outline+boxShadow | below | 100% |
| 33 | Tab | link "Go to Export" | main | ✓ 1400 · 312 · outline+boxShadow | below | 100% |
| 34 | Tab | link "View All →" | main | ✓ 792 · 186 · outline+boxShadow | below | 100% |
| 35 | Tab | link "View All →" | main | ✓ 792 · 186 · outline+boxShadow | below | 100% |
| 36 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>

<details><summary>dependency graph iframe <code>/admin/config/development/config-guardian/dependency-graph-frame</code> — 6 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | button "+" | (no landmark) | ✓ 252 · 220 · outline | below | 100% |
| 2 | Tab | button "−" | (no landmark) | ✓ 248 · 216 · outline | below | 100% |
| 3 | Tab | button "Reset" | (no landmark) | ✓ 356 · 324 · outline | below | 100% |
| 4 | Tab | button "Fit" | (no landmark) | ✓ 280 · 248 · outline | below | 100% |
| 5 | Tab | combobox "All Types System Fields Content Types Views Blocks User …" | (no landmark) | ✓ 628 · 596 · outline | below | 100% |
| 6 | Tab | textbox "Search configurations..." | (no landmark) | ✓ 836 · 804 · outline | below | 100% |

</details>

<details><summary>settings <code>/admin/config/development/config-guardian/settings</code> — 41 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | button "Save configuration" | (no landmark) | ✓ 6795 · 168 · boxShadow+border+background | below | 100% |
| 25 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 4344 · 192 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 26 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 4224 · 186 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 27 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 2504 · 100 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 28 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 5864 · 268 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 29 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 4624 · 206 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 1012 · 152 · outline+boxShadow | below | 100% |
| 31 | Tab | button "Snapshot Settings" | form | ✓ 8426 · 1780 · outline+border+color+textDecoration+before | below | 100% |
| 32 | Tab | checkbox "Enable automatic snapshots" | form | ✓ 568 · 72 · boxShadow | below | 100% |
| 33 | Tab | checkbox "Create snapshot before configuration import" | form | ✓ 568 · 72 · boxShadow | below | 100% |
| 34 | Tab | combobox "Automatic snapshot interval" | form | ✓ 1294 · 264 · boxShadow+background | below | 100% |
| 35 | Tab | button "Retention Settings" | form | ✓ 8453 · 1782 · outline+border+color+textDecoration+before | below | 100% |
| 36 | Tab | spinbutton "Maximum snapshots to keep" | form | ✓ 2123 · 328 · boxShadow | below | 100% |
| 37 | Tab | spinbutton "Retention days" | form | ✓ 2365 · 328 · boxShadow | below | 100% |
| 38 | Tab | button "Storage Settings" | form | ✓ 8367 · 1780 · outline+border+color+textDecoration+before | below | 100% |
| 39 | Tab | combobox "Compression method" | form | ✓ 2134 · 474 · boxShadow+background | below | 100% |
| 40 | Tab | button "Snapshot Exclusions" | form | ✓ 7296 · 1776 · — | below | 100% |
| 41 | Tab | button "Save configuration" | form | **NONE** 0 · 0 · boxShadow+border+background | below | 0% |

</details>

<details><summary>snapshot add <code>/admin/config/development/config-guardian/snapshot/add</code> — 29 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | button "Create Snapshot" | (no landmark) | ✓ 6203 · 155 · boxShadow+border+background | below | 100% |
| 25 | Tab | link "+Cancel" | (no landmark) | ✓ 4708 · 3143 · outline+boxShadow+border+background+color+textDecoration | meets | 100% |
| 26 | Tab | textbox "Snapshot name *" | form | ✓ 12500 · 1268 · boxShadow | below | 100% |
| 27 | Tab | textbox "Description" | form | ✓ 7736 · 1884 · boxShadow | below | 100% |
| 28 | Tab | button "Create Snapshot" | form | **NONE** 0 · 0 · boxShadow+border+background | below | 0% |
| 29 | Tab | link "+ Cancel" | form | **NONE** 0 · 0 · outline+boxShadow+border | below | 0% |

</details>

<details><summary>snapshot import <code>/admin/config/development/config-guardian/snapshot/import</code> — 28 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | button "Import Snapshot" | (no landmark) | ✓ 6219 · 155 · boxShadow+border+background | below | 100% |
| 25 | Tab | link "+Cancel" | (no landmark) | ✓ 4708 · 3143 · outline+boxShadow+border+background+color+textDecoration | meets | 100% |
| 26 | Tab | radio "From existing snapshot" | form | ✓ 443 · 49 · outline+boxShadow+border | below | 100% |
| 27 | Tab | button "Import Snapshot" | form | **NONE** 0 · 0 · boxShadow+border+background | below | 0% |
| 28 | Tab | link "Cancel" | form | **NONE** 0 · 0 · outline+boxShadow+border | below | 0% |

</details>

<details><summary>snapshots <code>/admin/config/development/config-guardian/snapshots</code> — 37 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 4344 · 192 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 25 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 1148 · 186 · outline+boxShadow | below | 100% |
| 26 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 2504 · 100 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 27 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 5864 · 268 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 28 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 4624 · 206 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 29 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 3544 · 152 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | link "+Create Snapshot" | main | ✓ 8697 · 183 · outline+boxShadow+border+background | below | 100% |
| 31 | Tab | link "+Import Snapshot" | main | ✓ 7876 · 5382 · outline+boxShadow+border+background+color+textDecoration | meets | 100% |
| 32 | Tab | link "ID Sort descending" | main | ✓ 936 · 222 · outline+boxShadow | below | 100% |
| 33 | Tab | link "Name" | main | ✓ 1313 · 314 · outline+boxShadow | below | 100% |
| 34 | Tab | link "Type" | main | ✓ 1241 · 296 · outline+boxShadow | below | 100% |
| 35 | Tab | link "Configs" | main | ✓ 1473 · 354 · outline+boxShadow | below | 100% |
| 36 | Tab | link "Created" | main | ✓ 1489 · 358 · outline+boxShadow | below | 100% |
| 37 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>

<details><summary>sync <code>/admin/config/development/config-guardian/sync</code> — 32 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Dashboard" | navigation "Primary tabs" | ✓ 4344 · 192 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 25 | Tab | link "Snapshots" | navigation "Primary tabs" | ✓ 4224 · 186 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 26 | Tab | link "Sync" | navigation "Primary tabs" | ✓ 804 · 100 · outline+boxShadow | below | 100% |
| 27 | Tab | link "Impact Analysis" | navigation "Primary tabs" | ✓ 5864 · 268 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 28 | Tab | link "Activity Log" | navigation "Primary tabs" | ✓ 4624 · 206 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 29 | Tab | link "Settings" | navigation "Primary tabs" | ✓ 3544 · 152 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 30 | Tab | link "Export" | main | ✓ 1438 · 208 · outline+boxShadow | below | 100% |
| 31 | Tab | link "Import" | main | ✓ 1446 · 210 · outline+boxShadow | below | 100% |
| 32 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>

<details><summary>sync export <code>/admin/config/development/config-guardian/sync/export</code> — 32 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Configuration Sync" | navigation "Breadcrumb" | ✓ 1128 · 258 · outline+boxShadow | below | 100% |
| 25 | Tab | button "Export Configuration" | (no landmark) | ✓ 7339 · 181 · boxShadow+border+background | below | 100% |
| 26 | Tab | link "Cancel" | (no landmark) | ✓ 4228 · 2784 · outline+boxShadow+border+background+color+textDecoration | meets | 100% |
| 27 | Tab | button "Changes Preview" | form | ✓ 8317 · 1780 · outline+border+color+textDecoration+before | below | 100% |
| 28 | Tab | button "675 new configurations" | form | **SUBTLE** 4988 · 0 · — | below | 100% |
| 29 | Tab | button "All configurations by module (675)" | form | ✓ 7306 · 1780 · — | below | 100% |
| 30 | Tab | checkbox "Create backup snapshot before export" | form | ✓ 568 · 72 · boxShadow | below | 89.3% |
| 31 | Tab | button "Export Configuration" | form | **NONE** 0 · 0 · boxShadow+border+background | below | 0% |
| 32 | Tab | link "Cancel" | form | **NONE** 0 · 0 · outline+boxShadow+border+background+color+textDecoration | below | 0% |

</details>

<details><summary>sync import <code>/admin/config/development/config-guardian/sync/import</code> — 32 stops, forward walk, 1280x800</summary>

| # | key | received focus (role, accessible name) | region | indicator: px changed · ≥ 3:1 · carried by | 2.4.13 | on screen |
|---|---|---|---|---|---|---|
| 1 | Tab | link "Skip to main content" | (no landmark) | ✓ 4985 · 3830 · outline+textDecoration | meets | 100% |
| 2 | Tab | link "Home page" | navigation "Administrative sidebar" | ✓ 2040 · 124 · outline+boxShadow+textDecoration | below | 100% |
| 3 | Tab | link "Dashboard" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 4 | Tab | button "Extend Create" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 5 | Tab | link "Pages" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 6 | Tab | link "CMS" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 7 | Tab | link "Media" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 8 | Tab | link "Trash" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 9 | Tab | button "Extend Tools" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 10 | Tab | button "Extend Structure" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 11 | Tab | link "Appearance" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 12 | Tab | link "Extend" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 13 | Tab | button "Extend Configuration" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 14 | Tab | link "People" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 15 | Tab | button "Extend Reports" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 16 | Tab | link "Help" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 17 | Tab | button "Extend admin" | navigation "Administrative sidebar" | ✓ 11316 · 524 · outline+boxShadow+border+background+color+textDecoration | below | 100% |
| 18 | Tab | button "Collapse sidebar" | navigation "Administrative sidebar" | ✓ 616 · 64 · boxShadow | below | 100% |
| 19 | Tab | link "Back to site" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 20 | Tab | link "Administration" | navigation "Breadcrumb" | ✓ 904 · 202 · outline+boxShadow | below | 100% |
| 21 | Tab | link "Configuration" | navigation "Breadcrumb" | ✓ 856 · 190 · outline+boxShadow | below | 100% |
| 22 | Tab | link "Development" | navigation "Breadcrumb" | ✓ 840 · 186 · outline+boxShadow | below | 100% |
| 23 | Tab | link "Config Guardian" | navigation "Breadcrumb" | ✓ 952 · 214 · outline+boxShadow | below | 100% |
| 24 | Tab | link "Configuration Sync" | navigation "Breadcrumb" | ✓ 1128 · 258 · outline+boxShadow | below | 100% |
| 25 | Tab | link "Cancel" | (no landmark) | ✓ 1412 · 244 · boxShadow | below | 100% |
| 26 | Tab | button "61 risk factors" | form | ✓ 7296 · 1776 · — | below | 100% |
| 27 | Tab | button "Detailed Changes" | form | ✓ 8412 · 1780 · outline+border+color+textDecoration+before | below | 100% |
| 28 | Tab | button "675 configurations to delete" | form | **SUBTLE** 4988 · 0 · — | below | 100% |
| 29 | Tab | checkbox "Create backup snapshot before import" | form | ✓ 568 · 72 · boxShadow | below | 100% |
| 30 | Tab | link "Cancel" | form | **NONE** 0 · 0 · outline+boxShadow | below | 0% |
| 31 | Tab | button "Moves focus to sticky header actions" | form | **NONE** 0 · 0 · outline+boxShadow+textDecoration | below | 100% |
| 32 | Tab | textbox "Query" | form | **OFFSCREEN** 0 · 0 · — | — | 0% |

</details>


---

## 4 · Every check, watched failing before its green was used — MEASURED

**A check first seen passing is a claim.** Each of the harness's own verdicts was made to fire by
planting exactly the defect it looks for, in the page, after load — then the same pages without the
plant are the baseline in §2, where each came back clean. Nothing below touches the package; the
plants live only in the browser tab.

| injected defect | the check it must fire | pages · width | findings of that kind · all findings | the first of that kind, verbatim | exit |
|---|---|---|---|---|---|
| `outline` | 2.4.7 focus visible | 9 · 1280x800 | **525** · 525 | canvas:1 @1280x800 2.4.7 NO visible indicator at stop 2: a "Agora keyboard rig" (banner) | 1 |
| `trap` | 2.1.2 trap (focus stops moving) | 2 · 1280x800 | **4** · 8 | front @1280x800 2.1.2 TRAP-no-movement at "injected trap" (Escape frees it: false) | 1 |
| `trap-loop` | 2.1.2 trap (focus cycles) | 2 · 1280x800 | **4** · 8 | front @1280x800 2.1.2 TRAP-cycle at "loop A" (Escape frees it: false) | 1 |
| `skip` | 2.4.1 skip link in use | 2 · 1280x800 | **2** · 4 | front @1280x800 2.4.1 skip link: {"firstStop":"a \"Skip to main … | 1 |
| `obscure` | 2.4.11 obscured, forward walk | 2 · 1280x800 | **68** · 159 | front @1280x800 2.4.11 entirely hidden (forward) at stop 10: a "Who runs the council" by div#injected-banner:21 | 1 |
| `sticky` | 2.4.11 obscured, reverse walk | 2 · 1280x800 | **67** · 146 | front @1280x800 2.4.11 entirely hidden (reverse) at stop 20: undefined "Agreement" by div#injected-header:21 | 1 |
| `target` | 2.5.8 target size | 2 · 1280x800 | **2** · 2 | front @1280x800 2.5.8 target-size: 3 node(s): a[href$="#injected-target-0"] :: Target has insufficient size (12px by 12px, should be at least 24px by … | 1 |
| `click-only` | 2.1.1 activation | 2 · 1280x800 | **2** · 2 | front @1280x800 2.1.1 activation: div "click-only control" click=changed Enter=no-effect Space=no-effect | 1 |
| `reflow` | 1.4.10 reflow | 2 · 320x256 | **2** · 2 | front @320x256 1.4.10 page scrolls horizontally by 880px: div#injected-wide in body.canvas-page | 1 |

**The outline plant was a prediction before it was a run.** The baseline recorded, for every stop,
which style properties differ between its focused and blurred states. At 1280 × 800,
**525** stops change their `outline` and nothing else, so removing the
outline (and `box-shadow`) on `:focus` had to leave them with **no** indicator, while the rest —
which also change border, background, colour or underline — had to keep one. **Measured: the
indicator vanished on 525 of 588 stops — the same set, stop for stop
as predicted.** Of the rest, 27 kept a strong indicator (18 by border+color+textDecoration; 9 by border+background+color+textDecoration), and
**36 became faint**: the footer's "Facebook", "Instagram", "X", "YouTube" links on every page, whose
other focus styles (`border+background`) do change, but by less than 3:1 once the outline is gone. So the
focus-visible check does not merely fire; it fires on exactly the stops that depend on the property
removed, and stays quiet on the ones that do not. **It is also a fact about the theme worth
knowing**: on most stops the indicator *is* the outline and nothing else changes, so any later
change that restyles `outline` changes the indicator on those stops — and this harness would now
say so.

⚠️ **Two plants where a defect has two shapes.** An element that swallows Tab (`trap`) and two
elements that hand focus to each other (`trap-loop`) fail differently, and the harness names them
differently (`TRAP-no-movement`, `TRAP-cycle`). A fixed band at the bottom (`obscure`) catches the
forward walk, where Chrome scrolls a newly focused element to the bottom edge; a fixed band at the
top (`sticky`) catches the reverse walk, where it scrolls to the top edge — the sticky-header case,
which is exactly what Gin's top bar does on the admin surface at 320 px (§3).

⚠️ **What the plants do not prove.** They prove each check *can* fail. They do not prove the
checks see every *form* of the defect: an occluder painted with `pointer-events: none`, or a trap
that releases after N presses, would need plants of their own.

---

## 5 · Findings — reported, not fixed

Classified as the project classifies escalations. **None of them is fixed here**: F1 and F2 belong
to `agora_theme` (D-014), F3 to the owner of the two gate files, F4 to modules this project does
not maintain (D-061 option B).

**F1 · 🟡 A register table does not follow keyboard focus into a column it has only partly shown.**
`agora_theme`. Measured in §2.4: 65 stops across the two widths land with part of the
focused link outside the table's visible area, as little as **4.3 %** ("Annual
remuneration", a column heading, at 320 px on the people register and the first Canvas page) and
**12.9–20.8 %** for the 7 links of the "Procedure type" column of the contracts
register at 1280 px. Chrome scrolls the table only when
the focused cell is *entirely* outside it (supporting probe, Appendix B: "Completed" moved the
table 609 px). **On the letter**: 2.4.11 (AA) passes — nothing is *entirely* hidden — and 2.4.12
(AAA) fails. **In use**: a keyboard user lands on a sliver. The remedy is small and belongs in the
theme: on `focusin` inside `.agora-table__scroll`, scroll the focused element into view with
`inline: 'nearest'`. It does not need a person's judgement to be worth doing; §6 asks the person
only whether the current state is *usable*, which decides its priority, not its merit. *A note on
the same element, not a finding:* the wrapper is `<div class="agora-table__scroll" tabindex="0">`
with no role and no label, so the name in the transcripts is one Chrome derives from its
contents. What a screen reader announces when focus lands on it is a screen-reader question,
outside both rows' criteria (§6).

**F2 · 🟢 The focused skip link starts 8 px above the window.** `agora_theme`. Every page, both
widths: the link becomes a static inline box whose vertical padding overflows its line box at the
very top of the page, so the top edge of its outline is off-screen, and the header — painted later,
with an opaque background — covers its bottom padding. The text is wholly on screen and the ring
shows on three sides. AAA only (2.4.12); an `inline-block` focused state would remove it.

**F3 · 🟡 WCAG 2.5.8 has never been asked by either repository's accessibility gate.** READ AT
SOURCE (§0 row 3): axe-core's `target-size` ships disabled in the version CI loads and in the
current one, and neither `AccessibilityTest` nor `agora_theme`'s Nightwatch suite enables it. The
gate's "89 rules per page" therefore contains no 2.5.8 check, although T-0616's criterion names
2.5.8. **Asked by name here**, the nine anonymous pages are clean —
0 violations over 1138 passing nodes —
so for the pages this package renders it is a gap in coverage, not a defect. **The admin surface
is not clean**: 4 violation nodes, all in Gin's breadcrumb at
320 px (A3), which no gate in either repository would ever have reported. Closing the gap is the
cheapest change in this file — one option on each `axe.run()` call — and is not made here:
`AccessibilityTest.php` is another writer's file today.

**F4 · 🔴 The admin surface has keyboard failures a person would hit, all in markup this package
does not own.** MEASURED, §3; attributed by element, not by guess:

| # | owner | what a keyboard user meets | criterion | measured |
|---|---|---|---|---|
| A1 | `coffee` | the last Tab of the page puts focus on a search box parked above the window | 2.4.7 fails | 12 of 22 page-widths |
| A2 | `gin` | after a form's last field, focus lands on hidden original buttons, then jumps back to their sticky copies; a visually-hidden helper link takes focus and stays invisible | 2.4.7, 2.4.11 fail | 16 stops on 10 page-widths, plus the link on 2 |
| A3 | `gin` | at 320 px: the sticky header over the focused element, breadcrumb targets too small, three forms wider than the window | 2.5.8 and 1.4.10 fail; 2.4.12 | 4 `target-size` nodes; settings 5 px, sync export 12 px, sync import 12 px; 2 covered stops |
| A4 | `config_guardian` | Tab into the dependency graph moves focus 3,658 px below the window, with no indicator; the next region is reachable only through Chrome's own behaviour | 2.4.7 fails; 2.1.1 browser-dependent | `/analyze`, both widths |
| A5 | `config_guardian`, `gin` | 12 indicators change, but by less than 3:1 anywhere | 1.4.11 as measured; 2.4.7 is a person's call (§6) | listed in §3.5 |

⚠️ **None of these can be fixed in this repository**, and D-061 option B does not ask for it: it
asks that what was chosen be measured and said truthfully. **What this repository *can* do is
keep declining to claim AA for the admin surface**, as D-061 already requires — and, optionally,
report A1, A2 and A4 upstream, where each is a small change in its own project.

**F5 · The premise.** *"A machine cannot press a key and see a focus ring"* was repeated in two
task rows, a research procedure and a dispatch, and it was never tested. Half of it is false (§0,
§1). The reusable lesson is the project's own, one level up: **a limitation stated without a
measurement reads exactly like a measurement**, and this one had already routed two rows to a
person.

---

## 6 · What is left for a person — the honest size of "these rows are yours"

**Before this file**, T-0616 and T-0633 read as two full keyboard walkthroughs by hand: every
page, every stop, a written transcript, four criteria judged. **After it**, the transcript exists
(§2.5), every stop has been reached and photographed, and each of the four criteria T-0616 names
has a measured outcome. What remains is **judgement about perception and meaning**, and it fits in
the checklists below — about **ten minutes for T-0616 and five for T-0633**, in an ordinary desktop
browser, **no screen reader required by either row's criterion**. Each item says what the machine
already established, so nothing is re-measured by hand.

### T-0616 · the nine anonymous pages

1. **Can you see where focus went, when it lands on a column the table has not scrolled to?**
   *(2.4.7 in use · about 3 minutes)* — Open `/contracts` at desktop width and press Tab until focus
   reaches the first row's "Procedure type" link, "Minor contract" (**27 presses** on this install;
   the 1280 px stop numbers are in §2.5). Then, in a phone-width window or at 400 % zoom, do the same on
   `/people` to the "Annual remuneration" column heading (**20 presses**). *Machine: 14.3 % and
   4.3 % of those links are on screen when focus lands; how many ring pixels survive is in §2.4's F1 table.* **Answer
   yes or no for each.** A "no" makes F1 urgent; a "yes" makes it tidy-up. Either way F1 is worth
   fixing.
2. **Is the skip link obviously there when it appears?** *(2.4.1 / 2.4.7 · 30 seconds)* — Press
   Tab once on any page. *Machine: the text is wholly on screen, the ring shows on three sides and
   its top edge is 8 px above the window (F2).* Yes or no.
3. **Does the order make sense to a citizen?** *(2.4.3 · about 3 minutes)* — Read the region
   trail printed in §2.1 for three pages (the contracts register, a Canvas page, the record page):
   skip link, banner, main navigation, search, breadcrumb, filters, table, footer menus, social
   links. *Machine: it **is** the DOM order, identical forwards and backwards, with no positive
   `tabindex`, and 0 of the 30 upward jumps at 1280 px go
   back up and to the left — every other one is a move to the top of the next column.*
   The only question left is whether this is the order you would expect. Yes or no, and the page
   if no.
4. **Optional — the narrow layout, beyond what overflow can show.** *(1.4.10 · 2 minutes)* — At
   phone width, scroll the front page and one register. Anything overlapping or unreadable?
   *Machine: 0 of 9 pages scroll sideways at 320 px, 0 texts are clipped by hidden
   overflow, and 10 of the 10 regions that overflow at that width are tables in keyboard-scrollable wrappers.*

**Not needed from a person on these pages**, because the machine settled it and was watched
failing first (§4): whether every control is reachable (1176 of
1176); whether there is a trap (0 in 36 walks); whether each focused element shows
an indicator (1176 of 1176); whether anything is ever
entirely hidden (0 forward, 0 reverse); whether the skip link works (18 of 18); target size
(0 violations); sideways scrolling at 320 px (0 of 9); whether
Enter and Space act (32 controls) and Enter follows links
(200 of 200); whether the register filters work by keyboard
(14 flows).

### T-0633 · the admin surface

T-0633's criterion asks for the same four outcomes as T-0616, and **here the machine's answer is
already a verdict, not a draft**: 2.4.7 **fails** (A1, A2, A4); 2.5.8 **fails at 320 px** (A3);
1.4.10 **fails at 320 px** (A3); 2.4.1 **works in use** — the skip link moves focus to its target
on 20 of 20 page-widths where it applies, and the
4 page-widths where the *next* Tab then vanishes are A1 and A4, not the link.
**Every one of those failures is in markup this package does not own.** A person does not need to
re-derive any of it; what is left is small:

1. **The indicators that change by less than 3:1.** *(A5 · about 2 minutes)* — Logged in, Tab to
   each element listed in A5 and say whether you notice where focus is. *Machine: pixels change,
   but none by 3:1.*
2. **Does the admin order make sense?** *(2.4.3 · 1 minute)* — The trail in §3.1: skip link, the
   administrative sidebar, breadcrumb, primary tabs, the page's own content, then `coffee`'s box.
   At 320 px the sidebar collapses behind "Expand sidebar". Yes or no.
3. **Optional — see A1, A2 and A4 for yourself** *(3 minutes)*, only if they are to be reported
   upstream: Tab to the end of `/activity` (A1), to the end of the settings form (A2), and into
   the graph on `/analyze` (A4). Nothing about the verdict depends on it.

**About five minutes**, and the verdict does not wait on any of it.

### What would enlarge either checklist — named so it is not assumed away

- **A screen reader.** Neither row asks for one, but an accessibility claim eventually will. The
  machine recorded the role and name Chrome computed for every stop — what a screen reader is
  given — and not what NVDA or VoiceOver make of it.
- **Another browser.** Everything here is Chrome 153. A second browser is a port of the harness's
  accessible-name step, not a second manual walkthrough.
- **A consent banner.** None renders on this install (§0 row 5). A site that adds a service needing
  consent gets one, and the obscuring checks should be re-run then.

---

## 7 · What this does NOT cover

1. **Neither row is closed.** T-0616 and T-0633 name a person, and §6 is what that person has left
   to do. **No task row, no gate, no statement and no attestation was edited by this file**, and
   none of its figures enters a gate total (both rows say so of themselves).
2. **Chrome 153 only**, headless, at device scale factor 1. No Firefox, no Safari, no mobile
   browser, no Windows High Contrast mode, no forced colours, no user stylesheet.
3. **No screen reader.** The accessible name and role of every stop are recorded; what an actual
   screen reader announces, and whether filtering a register announces its result count, is not.
4. **Two widths.** 1280 × 800 and 320 × 256. Nothing between them (a tablet width) was walked.
5. **Only what is focusable is judged reachable.** Functionality offered solely to a pointer — a
   drag, a hover-only menu — would never enter the predicted set, and its absence is invisible to
   the comparison in §2. None is known on these pages; none was searched for by other means.
6. **Activation on the admin set was not run**, deliberately: a button there can create a snapshot
   or start an import. The admin surface was walked, photographed and scanned; its buttons were not
   pressed. The admin links sampled were only those naming no action (§A.4's filter).
7. **The admin routes that take an argument were not walked** — a snapshot's page, a comparison,
   rollback, delete, export — because they need a snapshot to exist first, which a measurement run
   must not create. T-0603 names the same five.
8. **The 2.4.13 perimeter of a wrapped link is this harness's reading** (the outline of the union
   of its line boxes), because the criterion's text does not define it. 2.4.13 is AAA and no target
   of this project; the figure is reported, not relied on.
9. **The occlusion check sees what is opaque.** A translucent overlay, or one painted with
   `pointer-events: none`, is invisible to hit-testing and would be missed. §4 names the planted
   forms that *were* caught.
10. **The Config Guardian dashboard was walked before the site's first cron run**, so without its
    recent-snapshots table: this rig pins `automated_cron` off and never runs cron (the dispatch
    forbids it, and I-117 is why). `d47fd5a`'s logged-in scan in `AccessibilityTest` deliberately
    waits for that first run so the table exists; the table's rows were never Tab stops here.
11. **The clean-install smoke is not this rig.** `~/agora-kbd` is `minimal` plus the recipe on
    `drupal/core-recommended`, which is `AccessibilityTest`'s shape; the Drupal CMS installer path
    runs on drupalcode as the `Drupal CMS` job.

---

## Appendix A · Reproducing it — every command here was executed on 2026-09-23

**Where things ran.** Docker and DDEV inside WSL2 Ubuntu (DDEV 1.24.4, Docker 28.1.1). The
browser side on Windows: Node 22.20.0, pnpm 11.20.0, the installed Google Chrome 153.0.8010.52
driven headless through `playwright-core` 1.63.0. **No browser was downloaded**, and no package
manager other than pnpm was used. The libraries were added to a scratch package with `pnpm add`
rather than run through `pnpm dlx`, because a script that *imports* a library resolves it from
its own directory, which `pnpm dlx` does not populate.

### A.1 · The rig — built fresh, from the packaged tree

```bash
# In the site template working copy (Git Bash). The package is exactly what Drupal.org ships.
git archive --worktree-attributes --format=tar acd2a511a8de9f2dd9f471586c4971e863771226 > "$SCRATCH/pkg-acd2a51.tar"
#   -> 367 files, sha256 0b2e180fc17473347847e01649e842ae75d2cb0b5e21f2ea03072088c071dd01

# build-rig.sh is Appendix B; it refuses to run over an existing directory.
wsl.exe -e bash -lc 'bash "$SCRATCH_WSL/build-rig.sh" "$SCRATCH_WSL/pkg-acd2a51.tar" "$HOME/agora-kbd" agora-kbd'
wsl.exe -e bash -lc 'cd ~/agora-kbd && ddev drush site:install minimal --yes --site-name="Agora keyboard rig"'
wsl.exe -e bash -lc 'cd ~/agora-kbd && ddev drush recipe ../recipes/agora_transparency'
#   -> 82/82 ... [OK] Ágora Transparency applied successfully   (44 s)
```

**What resolved**, read with `ddev composer show`: `drupal/agora_theme` **1.2.0** (dist
`ftp.drupal.org/files/projects/agora_theme-1.2.0.zip`, under the template's own `^1.1`),
`drupal/config_guardian` **1.0.3**, `drupal/core` **11.4.7**, `drupal/canvas` 1.11.0,
`drupal/gin` 5.0.15, `drupal/klaro` 3.1.1, `drupal/coffee` 2.0.1.

⚠️ **The installed package differs from the archive in exactly one place**, and it is not the
package's doing: `diff -r src-template recipes/agora_transparency` reports only that
`composer.json` gained `"version": "dev-main"`, which is what Composer writes into a package it
installs from a path repository.

⚠️ **`minimal`, not the Drupal CMS installer**, for the reason §T-0603 of the wave-1 research
file gives: the rig is built on `drupal/core-recommended`, which ships no Drupal CMS profile.
**This is not the clean-install smoke** — that runs on drupalcode as the `Drupal CMS` job. It is
the same profile-plus-recipe shape `AccessibilityTest` uses, which is the relevant comparison
here.

### A.2 · The usage-reporting guard, and the proof that nothing phoned home

`build-rig.sh` appends three lines to `settings.php` **before** the site is installed (they are
in Appendix B). Read after the recipe with an inline `ddev drush php:eval`, and after the last run
in this file with `guard-check.php` (Appendix B), which reads the same values plus the rows that
name the pinned address.

| reading | after the recipe | after the last run |
|---|---|---|
| effective `update.settings` `fetch.url` | `'http://127.0.0.1:1/release-history'` | `'http://127.0.0.1:1/release-history'` |
| `automated_cron.settings` `interval` (effective) | `0` | `0` |
| **`update_available_releases` entries** | **0** | **0** |
| `system.cron_last` | `NULL` — cron has never run | `NULL` — cron never ran |
| `update.last_check` | `NULL` | `NULL` |
| `watchdog` rows naming `release-history` or `updates.drupal.org` | 0 | 0 (of 128 rows); rows naming the pinned address: 0 |
| `key` module · `ai` module | not installed · not installed | — |

⚠️ **Per I-117, `update.last_check` alone would prove nothing** in either direction; the
decisive pair is the empty `update_available_releases` and, where an attempt was recorded, the
address it was made to.

### A.3 · The pages, derived rather than typed

```powershell
# $REPO is the site template working copy; the rig name is the directory under ~ in WSL.
node derive-pages.mjs "$REPO" acd2a511a8de9f2dd9f471586c4971e863771226 agora-kbd pages.json
```

It reads `VIEW_PAGES`, `NODE_BUNDLE`, `MISSING_PATH`, `MISSING_HEADING`, `MAIN` and
`DECLARED_PAGES` out of `tests/src/FunctionalJavascript/AccessibilityTest.php` **at the commit
named, with `git show`** — so an uncommitted edit by another writer in the same checkout cannot
leak in — and runs `derive-pages.php` inside the rig, which mirrors `declarePages()`. A count
different from `DECLARED_PAGES` is a failure. Printed on this run: **9 of 9 declared**, and 14
`config_guardian.*` routes that take no argument (3 of them answer JSON and are skipped by the
harness as not HTML, by content type, not by name).

### A.4 · The runs

```powershell
# In a directory holding Appendix B's package.json, derive-pages.* and keyboard-harness.mjs:
pnpm install                                   # playwright-core 1.63.0, axe-core 4.13.0, pngjs 7.0.0

node keyboard-harness.mjs --pages=pages.json --set=anon --out=final-anon-none.json --shots=final-shots --shots-all

# Falsification: every check, watched failing (§4).
node keyboard-harness.mjs --pages=pages.json --set=anon --inject=outline --viewports=1280x800 --out=final-anon-outline.json
foreach ($m in 'trap','trap-loop','skip','obscure','sticky','target','click-only') {
  node keyboard-harness.mjs --pages=pages.json --set=anon --inject=$m --only=front,agora_base_contracts --viewports=1280x800 --out=final-anon-$m.json
}
node keyboard-harness.mjs --pages=pages.json --set=anon --inject=reflow --only=front,agora_base_contracts --viewports=320x256 --out=final-anon-reflow.json

# The admin surface, logged in through a one-time link (never written down anywhere).
$login = (wsl.exe -e bash -lc 'cd ~/agora-kbd && ddev drush user:login --uri=https://agora-kbd.ddev.site --no-browser /admin/config/development/config-guardian' | Select-Object -Last 1).Trim()
node keyboard-harness.mjs --pages=pages.json --set=admin --login=$login --out=final-admin.json --shots=final-shots-admin --shots-all
```

**Exit codes, as recorded**: `derive` 0 · `anon-none` 0 · `anon-outline` 1 · `anon-trap` 1 · `anon-trap-loop` 1 · `anon-skip` 1 · `anon-obscure` 1 · `anon-sticky` 1 · `anon-target` 1 · `anon-click-only` 1 · `anon-reflow` 1 · `admin` 1. Exit 1 is the harness's "findings" verdict, which
is the expected result of every falsification run; exit 2 would be a harness failure and did
not occur.

### A.5 · Getting the harness out of this file

The files in Appendix B are the executed files, byte for byte. The assembler that wrote this
file extracted each one back out and compared its sha256 before anything was committed, and the
command below — in Node, the harness's own runtime — was then run against this file's final
bytes (§A.6).

```bash
node -e '
const fs = require("fs");
const [doc, out] = process.argv.slice(1);
const text = fs.readFileSync(doc, "utf8");
for (const m of text.matchAll(/<!-- file: (\S+) -->\n````\w*\n([\s\S]*?)````\n/g)) {
  fs.writeFileSync(out + "/" + m[1], m[2]);
  console.log(m[1]);
}' specs/006-hardening/research/2026-09-23-keyboard-measurability.md "$OUT"
sha256sum "$OUT"/*    # compare with the sha256 line above each file in Appendix B
```

### A.6 · That extraction, executed

**Executed on this file as it stood one step before commit — every byte the same except this
paragraph, which cannot change a code block** — with §A.5's command verbatim, into an empty
directory: **9 files extracted, 9 of 9 byte-identical**
to the executed ones.

| file | sha256 of the extracted copy | against the executed file |
|---|---|---|
| `build-rig.sh` | `494eaed2b7648459…` | identical |
| `package.json` | `12fd4fb990ca0215…` | identical |
| `derive-pages.php` | `d3318d1e255fe832…` | identical |
| `derive-pages.mjs` | `6d24192341285c4a…` | identical |
| `keyboard-harness.mjs` | `63a4d4b22f5c3dd1…` | identical |
| `probe-table.mjs` | `0f7c37b85a625f9e…` | identical |
| `probe-scroll.mjs` | `d5144abf7eae9934…` | identical |
| `probe-analyze.mjs` | `27378b623b30f72f…` | identical |
| `guard-check.php` | `ab065bec38308b64…` | identical |

Then, in that directory and nowhere else: `pnpm install` exited **0**;
`node derive-pages.mjs` exited **0**; and the extracted harness, run on the front page at
1280 × 800, exited **0** with **63 of 63** stops reached,
**63** visible indicators, **0** traps and **0** findings —
the front page's row in §2.1 and §2.2, reproduced by the copy a reader would extract.

The assembler then extracted the nine files once more from the final bytes, with this paragraph in
place, and compared every sha256 again before the commit: identical.

---

## Appendix B · The harness, verbatim

**These are the executed files, byte for byte**: the assembler that wrote this file embedded each
one, extracted it back out and compared the sha256 above it before anything was committed, and
§A.5's command reproduces the extraction. Three supporting probes are published because a figure
in this file rests on each: `probe-table.mjs` (F1: "Completed" moved the table 609 px),
`probe-scroll.mjs` (§0 row 8(b): keyboard scrolling is animated) and `probe-analyze.mjs` (§0 row
8(h): `/analyze` takes 18 s, and Enter on a link to it does navigate). Other probes used while building the harness were
diagnostic only; **every keyboard figure in §2–§4 comes from `keyboard-harness.mjs`'s recorded
runs**, and the figures labelled READ AT SOURCE come from the files and pages they name.

**What each file is.** `build-rig.sh` builds the rig and appends the usage-reporting guard;
`package.json` pins the three libraries; `derive-pages.php` and `derive-pages.mjs` derive the
pages from the committed test and the router; `keyboard-harness.mjs` is the measurement;
`guard-check.php` reads the usage-reporting guard's state after the runs (§A.2).

### `build-rig.sh`

90 lines · sha256 `494eaed2b7648459eecc7999360d8acb5c7af2d11b1b12b13ff99045d7cc1d05`

<!-- file: build-rig.sh -->
````bash
#!/usr/bin/env bash
# Builds ~/agora-kbd: a throwaway clean-install rig for the keyboard measurement.
# NEVER a working copy. The package comes from `git archive --worktree-attributes`
# of a named commit; the theme and every other dependency resolve from
# packages.drupal.org under the template's own constraints.
#
# Usage: build-rig.sh <path-to-package-tar> <rig-dir> <project-name>
set -euo pipefail

TAR="$1"
RIG="$2"
NAME="$3"

if [ -e "$RIG" ]; then
  echo "refusing: $RIG already exists - this script only builds a fresh rig" >&2
  exit 2
fi

mkdir -p "$RIG/src-template"
tar -x -f "$TAR" -C "$RIG/src-template"
echo "package files extracted: $(find "$RIG/src-template" -type f | wc -l)"

cat > "$RIG/composer.json" <<'JSON'
{
    "name": "agora/rig-kbd",
    "description": "Throwaway clean-install rig for the keyboard measurement. Never a working copy.",
    "type": "project",
    "license": "GPL-2.0-or-later",
    "repositories": [
        { "type": "path", "url": "src-template", "options": { "symlink": false } },
        { "type": "composer", "url": "https://packages.drupal.org/8" }
    ],
    "require": {
        "composer/installers": "^2.3",
        "drupal/agora_transparency": "*",
        "drupal/core-composer-scaffold": "^11.4",
        "drupal/core-recommended": "^11.4",
        "drush/drush": "^13"
    },
    "minimum-stability": "dev",
    "prefer-stable": true,
    "config": {
        "allow-plugins": {
            "composer/installers": true,
            "drupal/core-composer-scaffold": true,
            "drupal/site_template_helper": true,
            "php-http/discovery": true,
            "symfony/runtime": true,
            "tbachert/spi": true
        },
        "sort-packages": true
    },
    "extra": {
        "drupal-scaffold": { "locations": { "web-root": "web/" } },
        "installer-paths": {
            "web/core": ["type:drupal-core"],
            "web/libraries/{$name}": ["type:drupal-library"],
            "web/modules/contrib/{$name}": ["type:drupal-module"],
            "web/profiles/contrib/{$name}": ["type:drupal-profile"],
            "web/themes/contrib/{$name}": ["type:drupal-theme"],
            "./recipes/{$name}": ["type:drupal-recipe"],
            "./drush/Commands/contrib/{$name}": ["type:drupal-drush"]
        }
    }
}
JSON

cd "$RIG"
ddev config --project-type=drupal11 --docroot=web --php-version=8.3 \
  --database=mariadb:10.11 --project-name="$NAME" --webserver-type=nginx-fpm
ddev start -y
ddev composer install --no-interaction 2>&1 | tail -n 25
# settings.php is generated by DDEV only once the docroot exists.
ddev restart -y >/dev/null 2>&1 || ddev start -y

SETTINGS="$RIG/web/sites/default/settings.php"
test -f "$SETTINGS"
chmod u+w "$RIG/web/sites/default" "$SETTINGS"
cat >> "$SETTINGS" <<'PHP'

// ---- agora-kbd rig guard (NOT part of the package) --------------------------
// No usage reporting, ever: release fetching (which carries the site_key that
// drupal.org counts as an install) is pinned to an address that refuses the
// connection. See IDIOMS I-117 for the /admin/config 500 this causes.
$config['update.settings']['fetch']['url'] = 'http://127.0.0.1:1/release-history';
$config['update.settings']['notification']['emails'] = [];
// Never run cron: automated_cron would otherwise run it on a page request.
$config['automated_cron.settings']['interval'] = 0;
PHP
echo "guard lines appended: $(grep -c 'agora-kbd rig guard\|127.0.0.1:1\|automated_cron.settings' "$SETTINGS")"
````

### `package.json`

1 lines · sha256 `12fd4fb990ca021595c8060b009b9809e2e6587d132cd76f84fab1e21e1a4411`

<!-- file: package.json -->
````json
{"name":"agora-kbd-harness","private":true,"type":"module","dependencies":{"axe-core":"4.13.0","playwright-core":"1.63.0","pngjs":"7.0.0"}}
````

### `derive-pages.php`

90 lines · sha256 `d3318d1e255fe832c7e015f4ef193529ad030b4b92c8200e38b6fac1f5385e69`

<!-- file: derive-pages.php -->
````php
<?php

/**
 * @file
 * Mirrors AccessibilityTest::declarePages() on a live rig, as JSON.
 *
 * The constants (VIEW_PAGES, NODE_BUNDLE, MISSING_PATH, MISSING_HEADING) are
 * NOT typed here: derive-pages.mjs reads them out of the committed test file
 * and hands them over base64-encoded as the first extra argument.
 *
 * Run: ddev drush php:script /var/www/html/derive-pages.php -- <base64-json>
 */

use Drupal\Core\Url;
use Drupal\views\Entity\View;

$in = json_decode(base64_decode($extra[0] ?? ''), TRUE);
if (!is_array($in)) {
  fwrite(STDERR, "derive-pages.php: no constants were handed in\n");
  exit(2);
}

$pages = [];
$labels = [];
foreach (\Drupal::entityTypeManager()->getStorage('canvas_page')->loadMultiple() as $entity) {
  $labels[] = (string) $entity->label();
  $pages[] = [
    'name' => 'canvas:' . $entity->id(),
    'path' => $entity->toUrl()->toString(),
    'heading' => (string) $entity->label(),
    'why' => 'a Canvas page, composed of component blocks',
  ];
}
$pages[] = [
  'name' => 'front',
  'path' => Url::fromRoute('<front>')->toString(),
  'heading' => $labels,
  'why' => 'the page every visitor lands on, without a breadcrumb',
];
foreach ($in['VIEW_PAGES'] as $view_id) {
  $view = View::load($view_id);
  $pages[] = [
    'name' => $view_id,
    'path' => '/' . ($view->getDisplay('page_1')['display_options']['path'] ?? ''),
    'heading' => (string) ($view->getDisplay('default')['display_options']['title'] ?? ''),
    'why' => 'a register, rendered as a table with rows in it',
  ];
}
$storage = \Drupal::entityTypeManager()->getStorage('node');
$ids = $storage->getQuery()
  ->accessCheck(FALSE)
  ->condition('status', 1)
  ->condition('type', $in['NODE_BUNDLE'])
  ->sort('nid')
  ->range(0, 1)
  ->execute();
$node = $storage->load(reset($ids));
$pages[] = [
  'name' => 'node:' . $node->id(),
  'path' => $node->toUrl()->toString(),
  'heading' => (string) $node->label(),
  'why' => 'a published record, rendered through the entity path',
];
$pages[] = [
  'name' => 'not-found',
  'path' => $in['MISSING_PATH'],
  'heading' => $in['MISSING_HEADING'],
  'why' => 'the one page served with no content behind it',
];

// The admin surface for T-0633: every config_guardian HTML route that takes
// no argument, derived from the router rather than typed.
$admin = [];
foreach (\Drupal::service('router.route_provider')->getAllRoutes() as $route_name => $route) {
  if (!str_starts_with($route_name, 'config_guardian.')) {
    continue;
  }
  $path = $route->getPath();
  $format = $route->getRequirement('_format');
  if (str_contains($path, '{') || $format === 'json') {
    continue;
  }
  $methods = $route->getMethods();
  if ($methods && !in_array('GET', $methods, TRUE)) {
    continue;
  }
  $admin[] = ['name' => $route_name, 'path' => $path];
}

print json_encode(['pages' => $pages, 'admin' => $admin], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
````

### `derive-pages.mjs`

61 lines · sha256 `6d24192341285c4adc18ef268a7f9e393f0cd2d22465936ce251c30b8ffd4575`

<!-- file: derive-pages.mjs -->
````js
// Derives the pages to walk from the COMMITTED AccessibilityTest.php and a live rig.
//
// Usage: node derive-pages.mjs <repo-dir> <commit> <rig-name> <out.json>
//
// Nothing about the nine pages is typed here. The constants are read out of the
// test file at <commit> (git show, so an uncommitted edit by another writer cannot
// leak in), handed to derive-pages.php inside the rig, and the result is compared
// against the test's own DECLARED_PAGES. A mismatch is a FAILURE, not a warning.
import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const [repo, commit, rig, out] = process.argv.slice(2);
if (!repo || !commit || !rig || !out) {
  console.error('usage: node derive-pages.mjs <repo-dir> <commit> <rig-name> <out.json>');
  process.exit(2);
}
const TEST = 'tests/src/FunctionalJavascript/AccessibilityTest.php';
const src = execFileSync('git', ['-C', repo, 'show', `${commit}:${TEST}`], { encoding: 'utf8' });

function constString(name) {
  const m = src.match(new RegExp(`private const ${name} = '([^']*)';`));
  if (!m) throw new Error(`${TEST}@${commit}: constant ${name} not found`);
  return m[1];
}
function constInt(name) {
  const m = src.match(new RegExp(`private const ${name} = (\\d+);`));
  if (!m) throw new Error(`${TEST}@${commit}: constant ${name} not found`);
  return Number(m[1]);
}
const views = src.match(/private const VIEW_PAGES = \[([\s\S]*?)\];/);
if (!views) throw new Error(`${TEST}@${commit}: VIEW_PAGES not found`);
const constants = {
  VIEW_PAGES: [...views[1].matchAll(/'([^']+)'/g)].map((m) => m[1]),
  NODE_BUNDLE: constString('NODE_BUNDLE'),
  MISSING_PATH: constString('MISSING_PATH'),
  MISSING_HEADING: constString('MISSING_HEADING'),
  MAIN: constString('MAIN'),
  DECLARED_PAGES: constInt('DECLARED_PAGES'),
};
console.log(`read from ${TEST} at ${commit}:`, JSON.stringify(constants));

// Hand the PHP mirror to the rig and run it there. C:\x\y is /mnt/c/x/y in WSL.
const here = path.dirname(fileURLToPath(import.meta.url));
const phpWindows = path.join(here, 'derive-pages.php');
const phpLinux = phpWindows.replace(/^([A-Za-z]):/, (m, d) => `/mnt/${d.toLowerCase()}`).replace(/\\/g, '/');
const b64 = Buffer.from(JSON.stringify(constants)).toString('base64');
const json = execFileSync('wsl.exe', ['-e', 'bash', '-lc',
  `cp '${phpLinux}' ~/${rig}/derive-pages.php && cd ~/${rig} && ddev drush php:script /var/www/html/derive-pages.php -- ${b64}`],
{ encoding: 'utf8' });
const derived = JSON.parse(json.slice(json.indexOf('{')));

if (derived.pages.length !== constants.DECLARED_PAGES) {
  console.error(`FAIL: derived ${derived.pages.length} pages, the test declares ${constants.DECLARED_PAGES}`);
  process.exit(1);
}
fs.writeFileSync(out, JSON.stringify({ commit, constants, ...derived }, null, 2));
console.log(`pages derived: ${derived.pages.length} of ${constants.DECLARED_PAGES} declared; admin routes derived: ${derived.admin.length}`);
for (const p of derived.pages) console.log(`  ${p.name.padEnd(28)} ${p.path}`);
for (const a of derived.admin) console.log(`  [admin] ${a.name.padEnd(40)} ${a.path}`);
````

### `keyboard-harness.mjs`

1067 lines · sha256 `63a4d4b22f5c3dd1422ff5db14bae54d31425c5651407fd238873b44227e2587`

<!-- file: keyboard-harness.mjs -->
````js
// keyboard-harness.mjs - what a machine can measure of a keyboard walkthrough.
//
// Drives a real, installed Chrome through Playwright, presses real keys, and
// records per page and per viewport:
//   2.1.1  reachability - every predicted focus candidate reached by Tab;
//          activation by Enter/Space compared against a click; Enter on a
//          sample of links; the register search flow, by keyboard alone
//   2.1.2  traps - focus that stops moving, or cycles without leaving the page
//   2.4.3  order - Tab order against DOM order, the reverse walk against the
//          forward one, and every upward visual jump listed for a person
//   2.4.7  focus visible - each stop captured focused and then blurred; a stop
//          whose two crops are pixel-identical has NO visible indicator
//   2.4.13 (AAA, reported apart) - area of pixels changing by >= 3:1 against
//          a 2 CSS px perimeter of the component
//   2.4.11 focus not obscured (and 2.4.12, AAA) - a grid of points across the
//          focused element, each tested against the viewport, every clipping
//          ancestor and every opaque element stacked above it; forward AND
//          reverse walks
//   3.2.1  on focus - the address must not change while focus moves
//   2.4.1  the skip link in use - first stop, Enter, where the next Tab lands
//   2.5.8  target size - axe-core's own target-size rule, asked by name
//   1.4.10 reflow - page-level horizontal overflow at 320 CSS px
//
// Exit 0: no findings. Exit 1: findings. Exit 2: the HARNESS failed (a page did
// not render, or a walk reached zero elements) - never a clean result.
//
// Usage:
//   node keyboard-harness.mjs --pages=pages.json [--set=anon|admin]
//        [--login=<one-time login URL>] [--viewports=1280x800,320x256]
//        [--inject=<mode>] [--only=a,b] [--base=https://agora-kbd.ddev.site]
//        [--out=results.json] [--shots=dir] [--shots-all]
// Injection modes (falsification - each must FIRE): outline trap trap-loop
//   skip obscure sticky target reflow click-only
import { chromium } from 'playwright-core';
import { PNG } from 'pngjs';
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const AXE_SOURCE = fs.readFileSync(require.resolve('axe-core/axe.min.js'), 'utf8');
const AXE_VERSION = require('axe-core/package.json').version;
const PLAYWRIGHT_VERSION = require('playwright-core/package.json').version;

const args = Object.fromEntries(process.argv.slice(2).map((a) => {
  const m = a.match(/^--([^=]+)(?:=(.*))?$/);
  return m ? [m[1], m[2] ?? 'true'] : [a, 'true'];
}));
const BASE = (args.base || 'https://agora-kbd.ddev.site').replace(/\/$/, '');
const DERIVED = JSON.parse(fs.readFileSync(args.pages || 'pages.json', 'utf8'));
const SET = args.set || 'anon';
const INJECT = args.inject || 'none';
const VIEWPORTS = (args.viewports || '1280x800,320x256').split(',').map((s) => {
  const [width, height] = s.split('x').map(Number);
  return { width, height, label: s };
});
const ONLY = args.only ? args.only.split(',') : null;
const OUT = args.out || `results-${SET}-${INJECT}.json`;
const SHOTS = args.shots || null;
const SHOTS_ALL = args['shots-all'] === 'true';
// The main landmark: read from AccessibilityTest for the anonymous pages; the
// admin theme's own element for the admin set.
const MAIN = SET === 'admin' ? 'main' : DERIVED.constants.MAIN;
const PAD = 8;
// How long a link or a submitted form may take before "it did not navigate"
// is concluded. Config Guardian's /analyze takes 18 s to render, and an 8 s wait
// reported Enter on a link to it as not followed when it had been. Waiting for
// the navigation to COMMIT, for up to 60 s, can only turn a "no" into a "yes".
const NAVIGATION_WAIT = { waitUntil: 'commit', timeout: 60000 };
// A stop counts as fully visible at 99% of its own area and above: fractional
// layout puts a few tenths of a pixel outside a clip box on perfectly
// ordinary pages, and that is not what 2.4.11 or 2.4.12 is about.
const FULL = 99;

let specs = SET === 'admin'
  ? DERIVED.admin.map((a) => ({ name: a.name, path: a.path, why: 'Config Guardian admin route', expectStatus: 200 }))
  : DERIVED.pages.map((p) => ({ ...p, expectStatus: p.name === 'not-found' ? 404 : 200 }));
if (ONLY) specs = specs.filter((s) => ONLY.includes(s.name));

// --------------------------------------------------------------------------
// In-page helpers, installed on every navigation before any page script runs.
// --------------------------------------------------------------------------
const HELPERS = `
window.__keyboard = (() => {
  const MAIN = ${JSON.stringify(MAIN)};
  function cssPath(el) {
    const parts = [];
    let n = el;
    while (n && n.nodeType === 1 && n !== n.ownerDocument.documentElement) {
      let sel = n.localName;
      if (n.id && n.ownerDocument.querySelectorAll('#' + CSS.escape(n.id)).length === 1) {
        parts.unshift(sel + '#' + CSS.escape(n.id));
        return parts.join(' > ');
      }
      const p = n.parentElement;
      if (p) {
        const same = Array.from(p.children).filter((c) => c.localName === n.localName);
        if (same.length > 1) sel += ':nth-of-type(' + (same.indexOf(n) + 1) + ')';
      }
      parts.unshift(sel);
      n = p;
    }
    return 'html > ' + parts.join(' > ');
  }
  function deepActive() {
    let a = document.activeElement;
    let offX = 0, offY = 0;
    const frames = [];
    for (;;) {
      if (!a) break;
      if (a.shadowRoot && a.shadowRoot.activeElement) { a = a.shadowRoot.activeElement; continue; }
      if (a.tagName === 'IFRAME' || a.tagName === 'FRAME') {
        let inner = null;
        try { inner = a.contentDocument && a.contentDocument.activeElement; } catch (e) { inner = null; }
        if (inner && inner !== a.contentDocument.body && inner !== a.contentDocument.documentElement) {
          const r = a.getBoundingClientRect();
          offX += r.left + a.clientLeft; offY += r.top + a.clientTop;
          frames.push(a); a = inner; continue;
        }
      }
      break;
    }
    return { el: a, offX, offY, frames };
  }
  function keyOf(el, frames) { return frames.map(cssPath).concat([cssPath(el)]).join(' >>> '); }
  // A name a person can recognise: tag, id and the first two classes.
  function nameOf(e) {
    const c = typeof e.className === 'string' ? e.className.trim().split(/\\s+/).filter(Boolean).slice(0, 2) : [];
    return e.localName + (e.id ? '#' + e.id : '') + (c.length ? '.' + c.join('.') : '');
  }
  const LANDMARK = { header: 'banner', nav: 'navigation', main: 'main', footer: 'contentinfo', aside: 'complementary', form: 'form', search: 'search' };
  function region(el) {
    const r = el.closest('[role=banner],[role=navigation],[role=main],[role=contentinfo],[role=search],[role=complementary],[role=dialog],[role=region],header,nav,main,footer,aside,form,search');
    if (!r) return '(no landmark)';
    const role = r.getAttribute('role') || LANDMARK[r.localName] || r.localName;
    let label = r.getAttribute('aria-label') || '';
    const by = r.getAttribute('aria-labelledby');
    if (!label && by) label = by.split(/\\s+/).map((id) => (r.ownerDocument.getElementById(id) || {}).textContent || '').join(' ').trim();
    return role + (label ? ' "' + label.replace(/\\s+/g, ' ').slice(0, 40) + '"' : '');
  }
  function text(el) {
    const t = (el.getAttribute('aria-label') || el.innerText || el.value || el.getAttribute('title') || el.getAttribute('alt') || '').trim();
    return t.replace(/\\s+/g, ' ').slice(0, 80);
  }
  function describe() {
    const { el, offX, offY, frames } = deepActive();
    if (!el) return { none: true, url: location.href };
    const doc = el.ownerDocument;
    if (el === doc.body || el === doc.documentElement) return { none: true, depth: frames.length, url: location.href };
    window.__keyboardCurrent = el; window.__keyboardOffset = { offX, offY }; window.__keyboardFrames = frames;
    const box = el.getBoundingClientRect();
    const boxes = Array.from(el.getClientRects()).filter((r) => r.width > 0 && r.height > 0)
      .map((r) => ({ x: r.left + offX, y: r.top + offY, w: r.width, h: r.height }));
    const cs = getComputedStyle(el);
    return {
      key: keyOf(el, frames), tag: el.localName, type: el.getAttribute('type') || '', role: el.getAttribute('role') || '',
      text: text(el), hrefAttr: el.getAttribute('href') || '', href: el.href || '',
      tabindexAttr: el.getAttribute('tabindex'), tabIndex: el.tabIndex,
      rect: { x: box.left + offX, y: box.top + offY, w: box.width, h: box.height }, boxes,
      docY: Math.round(box.top + offY + scrollY), docX: Math.round(box.left + offX + scrollX), scrollY: Math.round(scrollY),
      region: region(el), inMain: !!el.closest(MAIN), focusVisible: el.matches(':focus-visible'),
      display: cs.display, depth: frames.length, url: location.href,
      visible: el.checkVisibility ? el.checkVisibility({ opacityProperty: true, visibilityProperty: true }) : true,
    };
  }
  function isActive(el) { const d = deepActive(); return d.el === el; }
  function styleOf(el) {
    const cs = getComputedStyle(el);
    return {
      outline: [cs.outlineStyle, cs.outlineWidth, cs.outlineColor, 'offset ' + cs.outlineOffset].join(' '),
      boxShadow: cs.boxShadow,
      border: [cs.borderTopColor, cs.borderRightColor, cs.borderBottomColor, cs.borderLeftColor, cs.borderTopWidth, cs.borderBottomWidth].join(' '),
      background: cs.backgroundColor + ' ' + cs.backgroundImage.slice(0, 60),
      color: cs.color,
      textDecoration: [cs.textDecorationLine, cs.textDecorationThickness, cs.textDecorationColor, cs.textUnderlineOffset].join(' '),
      after: (() => { const a = getComputedStyle(el, '::after'); return [a.content, a.outlineStyle, a.boxShadow, a.backgroundColor, a.borderBottomWidth].join(' '); })(),
      before: (() => { const b = getComputedStyle(el, '::before'); return [b.content, b.outlineStyle, b.boxShadow, b.backgroundColor, b.borderBottomWidth].join(' '); })(),
    };
  }
  // Does this element paint something opaque over what lies beneath it? A
  // transparent wrapper that merely sits higher in the stack hides nothing,
  // and counting it as a cover would report a false obstruction.
  function occludes(e) {
    const cs = getComputedStyle(e);
    if (cs.visibility === 'hidden' || Number(cs.opacity) < 0.1) return false;
    if (['img', 'svg', 'video', 'canvas', 'iframe', 'input', 'select', 'textarea', 'button', 'object', 'embed'].includes(e.localName)) return true;
    if (cs.backgroundImage && cs.backgroundImage !== 'none') return true;
    const m = cs.backgroundColor.match(/rgba?\\(([^)]+)\\)/);
    if (m) { const parts = m[1].split(/[ ,\\/]+/).filter(Boolean); const alpha = parts.length > 3 ? Number(parts[3]) : 1; if (alpha >= 0.5) return true; }
    return false;
  }
  // The boxes that clip this element: every ancestor whose overflow is not
  // visible clips its descendants to its padding box.
  function clipBoxes(el) {
    const out = [];
    for (let p = el.parentElement; p && p !== p.ownerDocument.body && p !== p.ownerDocument.documentElement; p = p.parentElement) {
      const cs = getComputedStyle(p);
      if (cs.overflowX === 'visible' && cs.overflowY === 'visible') continue;
      const r = p.getBoundingClientRect();
      const left = r.left + p.clientLeft, top = r.top + p.clientTop;
      out.push({ left, top, right: left + p.clientWidth, bottom: top + p.clientHeight, by: nameOf(p) });
      if (cs.position === 'fixed') break;
    }
    return out;
  }
  // How much of the focused element a user can see at this moment. Geometry
  // is EXACT - each of its boxes intersected with the viewport and with every
  // clipping ancestor - because a sampling grid steps over a sliver and calls
  // a 6 px strip "entirely hidden" (measured on a 128 px link). Occlusion by
  // opaque content stacked above is then sampled INSIDE the visible part only.
  function obscured() {
    const el = window.__keyboardCurrent; if (!el) return null;
    const { offX, offY } = window.__keyboardOffset; const frames = window.__keyboardFrames || [];
    const doc = el.ownerDocument;
    const own = Array.from(el.getClientRects()).filter((r) => r.width > 0 && r.height > 0);
    const clips = clipBoxes(el);
    // The viewport and any iframe, expressed in the element's own document.
    const limits = [{ left: -offX, top: -offY, right: innerWidth - offX, bottom: innerHeight - offY, by: '(viewport)' }];
    if (frames.length) { const fr = frames[0].getBoundingClientRect(); limits.push({ left: fr.left - offX, top: fr.top - offY, right: fr.right - offX, bottom: fr.bottom - offY, by: 'iframe' }); }
    let area = 0, geometric = 0, seen = 0, samples = 0, coveredSamples = 0;
    const coveredBy = {}; const clippedBy = {};
    for (const r of own) {
      area += r.width * r.height;
      let v = { left: r.left, top: r.top, right: r.right, bottom: r.bottom };
      for (const c of limits.concat(clips)) {
        const before = Math.max(0, v.right - v.left) * Math.max(0, v.bottom - v.top);
        v = { left: Math.max(v.left, c.left), top: Math.max(v.top, c.top), right: Math.min(v.right, c.right), bottom: Math.min(v.bottom, c.bottom) };
        const after = Math.max(0, v.right - v.left) * Math.max(0, v.bottom - v.top);
        if (after < before) clippedBy[c.by] = (clippedBy[c.by] || 0) + Math.round(before - after);
      }
      const w = v.right - v.left, h = v.bottom - v.top;
      if (w <= 0 || h <= 0) continue;
      geometric += w * h;
      const nx = Math.max(1, Math.min(7, Math.ceil(w / 6))), ny = Math.max(1, Math.min(3, Math.ceil(h / 6)));
      let hits = 0, n = 0;
      for (let i = 0; i < nx; i++) for (let j = 0; j < ny; j++) {
        const x = v.left + (w * (i + 0.5)) / nx, y = v.top + (h * (j + 0.5)) / ny;
        n++;
        let cover = null, missing = false;
        if (frames.length) {
          const f = frames[0]; const above = document.elementsFromPoint(x + offX, y + offY);
          const at = above.findIndex((e) => e === f || f.contains(e) || e.contains(f));
          cover = above.slice(0, Math.max(0, at)).find(occludes) || null; missing = at < 0;
        }
        if (!cover && !missing) {
          const stack = doc.elementsFromPoint(x, y);
          const at = stack.findIndex((e) => e === el || el.contains(e) || e.contains(el));
          cover = stack.slice(0, Math.max(0, at)).find(occludes) || null; missing = at < 0;
        }
        if (cover || missing) { const c = cover ? nameOf(cover) : '(not in hit stack)'; coveredBy[c] = (coveredBy[c] || 0) + 1; }
        else hits++;
      }
      samples += n; coveredSamples += n - hits;
      seen += (w * h * hits) / n;
    }
    // pct: the share of the element's own area that is on screen, inside every
    // clipping box, and not under opaque content. 0 is "entirely hidden".
    const pct = area ? Math.round((1000 * seen) / area) / 10 : 0;
    return { area: Math.round(area), geometricPct: area ? Math.round((1000 * geometric) / area) / 10 : 0, samples, coveredSamples, pct, coveredBy, clippedBy };
  }
  // What sequential focus navigation SHOULD reach, predicted from the DOM
  // alone, before a single key is pressed. The walk is compared against it.
  function focusCandidates() {
    const out = [];
    const SEL = 'a[href], area[href], button, input, select, textarea, iframe, summary, [tabindex], [contenteditable="true"], [contenteditable=""], audio[controls], video[controls]';
    const groupsSeen = new Set();
    function scan(doc, frames) {
      for (const el of doc.querySelectorAll(SEL)) {
        if (el.disabled || el.tabIndex < 0) continue;
        if (el.localName === 'input' && el.type === 'hidden') continue;
        if (el.closest('[inert]')) continue;
        if (el.localName === 'summary' && !(el.parentElement && el.parentElement.localName === 'details' && el.parentElement.querySelector(':scope > summary') === el)) continue;
        const closed = el.closest('details:not([open])');
        if (closed && !(el.localName === 'summary' && el.parentElement === closed)) continue;
        if (el.checkVisibility && !el.checkVisibility({ visibilityProperty: true })) continue;
        if (el.localName === 'input' && el.type === 'radio' && el.name) {
          const g = (el.form || doc).querySelectorAll('input[type=radio][name="' + CSS.escape(el.name) + '"]');
          const chosen = Array.from(g).find((x) => x.checked) || g[0];
          if (chosen !== el || groupsSeen.has(el.name)) continue;
          groupsSeen.add(el.name);
        }
        if (el.localName === 'iframe') {
          let d = null; try { d = el.contentDocument; } catch (e) { d = null; }
          if (d && d.body) { scan(d, frames.concat([el])); continue; }
        }
        out.push({ key: keyOf(el, frames), tag: el.localName, text: text(el), positive: el.tabIndex > 0 });
      }
    }
    scan(document, []);
    return out;
  }
  function fingerprint() {
    const st = Array.from(document.querySelectorAll('[aria-expanded],[aria-pressed],[aria-selected],[aria-checked],details,dialog,[hidden]'))
      .map((e) => [e.getAttribute('aria-expanded'), e.getAttribute('aria-pressed'), e.getAttribute('aria-selected'), e.getAttribute('aria-checked'), e.open, e.hidden].join(',')).join('|');
    return location.href + '#' + document.title + '#' + st + '#' + document.body.innerText.length;
  }
  return { describe, isActive, styleOf, obscured, focusCandidates, fingerprint, cssPath, nameOf };
})();
`;

// --------------------------------------------------------------------------
// Falsification injections. Each one plants exactly the defect its check is
// for, so a check is only trusted after it has been seen to fire.
// --------------------------------------------------------------------------
const HOST = `(document.querySelector(${JSON.stringify(MAIN)}) || document.body)`;
const INJECTIONS = {
  none: null,
  outline: `(() => { const s = document.createElement('style'); s.id = 'injected-outline'; s.textContent = '*:focus{outline:none!important;box-shadow:none!important}'; document.head.append(s); })()`,
  trap: `(() => { const d = document.createElement('div'); d.tabIndex = 0; d.id = 'injected-trap'; d.textContent = 'injected trap';
    d.addEventListener('keydown', (e) => { if (e.key === 'Tab') e.preventDefault(); });
    ${HOST}.prepend(d); })()`,
  'trap-loop': `(() => { const host = ${HOST};
    const a = document.createElement('button'); a.id = 'injected-loop-a'; a.textContent = 'loop A';
    const b = document.createElement('button'); b.id = 'injected-loop-b'; b.textContent = 'loop B';
    a.addEventListener('keydown', (e) => { if (e.key === 'Tab') { e.preventDefault(); b.focus(); } });
    b.addEventListener('keydown', (e) => { if (e.key === 'Tab') { e.preventDefault(); a.focus(); } });
    host.prepend(b); host.prepend(a); })()`,
  skip: `(() => { document.querySelectorAll('#main-content').forEach((n) => n.remove()); })()`,
  obscure: `(() => { const d = document.createElement('div'); d.id = 'injected-banner';
    d.style.cssText = 'position:fixed;left:0;right:0;bottom:0;height:45vh;background:#fff;z-index:2147483647;border-top:1px solid #000';
    d.textContent = 'injected fixed banner'; document.body.append(d); })()`,
  sticky: `(() => { const d = document.createElement('div'); d.id = 'injected-header';
    d.style.cssText = 'position:fixed;left:0;right:0;top:0;height:45vh;background:#fff;z-index:2147483647;border-bottom:1px solid #000';
    d.textContent = 'injected fixed header'; document.body.append(d); })()`,
  target: `(() => { const h = document.querySelector('header') || document.body; const w = document.createElement('div'); w.id = 'injected-targets';
    for (let i = 0; i < 3; i++) { const a = document.createElement('a'); a.href = '#injected-target-' + i; a.textContent = String(i);
      a.style.cssText = 'display:inline-block;width:12px;height:12px;font-size:8px;line-height:12px;margin:0;padding:0;overflow:hidden'; w.append(a); }
    h.prepend(w); })()`,
  reflow: `(() => { const d = document.createElement('div'); d.id = 'injected-wide'; d.style.cssText = 'width:1200px;height:12px;background:#ccc';
    document.body.append(d); })()`,
  'click-only': `(() => { const d = document.createElement('div'); d.setAttribute('role', 'button'); d.tabIndex = 0; d.id = 'injected-click-only';
    d.setAttribute('aria-pressed', 'false'); d.textContent = 'click-only control';
    d.addEventListener('click', () => { d.setAttribute('aria-pressed', d.getAttribute('aria-pressed') === 'true' ? 'false' : 'true'); });
    ${HOST}.prepend(d); })()`,
};
if (!(INJECT in INJECTIONS)) { console.error(`unknown --inject=${INJECT}`); process.exit(2); }

// --------------------------------------------------------------------------
// Pixel arithmetic for 2.4.7 / 2.4.13.
// --------------------------------------------------------------------------
function luminance(r, g, b) {
  const f = (c) => { c /= 255; return c <= 0.04045 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4; };
  return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
}
function contrast(l1, l2) { return (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05); }
function comparePng(a, b) {
  const A = PNG.sync.read(a); const B = PNG.sync.read(b);
  if (A.width !== B.width || A.height !== B.height) return { changed: -1, qualifying: 0, maxRatio: 0 };
  let changed = 0, qualifying = 0, maxRatio = 1;
  for (let i = 0; i < A.data.length; i += 4) {
    const r1 = A.data[i], g1 = A.data[i + 1], b1 = A.data[i + 2];
    const r2 = B.data[i], g2 = B.data[i + 1], b2 = B.data[i + 2];
    if (r1 === r2 && g1 === g2 && b1 === b2) continue;
    changed++;
    const ratio = contrast(luminance(r1, g1, b1), luminance(r2, g2, b2));
    if (ratio >= 3) qualifying++;
    if (ratio > maxRatio) maxRatio = ratio;
  }
  return { changed, qualifying, maxRatio: Math.round(maxRatio * 100) / 100, pixels: A.width * A.height };
}
// The area of a 2 CSS px perimeter of the component (WCAG 2.4.13). An edge
// counts only when an indicator drawn along it could be on screen: a
// component flush with the viewport edge cannot show an outline outside
// itself there, and that must not count against the indicator.
function perimeterAreaInView(boxes, viewport) {
  const E = 3;
  let s = 0;
  const sorted = [...boxes].sort((a, b) => a.y - b.y);
  for (const r of sorted) {
    const left = Math.max(0, r.x), top = Math.max(0, r.y);
    const right = Math.min(viewport.width, r.x + r.w), bottom = Math.min(viewport.height, r.y + r.h);
    const w = Math.round(right - left), h = Math.round(bottom - top);
    if (w <= 0 || h <= 0) continue;
    if (w <= 4 || h <= 4) { s += w * h; continue; }
    if (r.y >= E) s += 2 * w;
    if (r.y + r.h <= viewport.height - E) s += 2 * w;
    if (r.x >= E) s += 2 * h;
    if (r.x + r.w <= viewport.width - E) s += 2 * h;
    // Corners are counted twice above (<= 16 px), which errs towards a
    // stricter requirement rather than a kinder one.
  }
  // A link that wraps is ONE component: its line boxes touch, and the edges
  // they share are inside it, not on its perimeter. Subtract them twice
  // (once from each neighbour) so the requirement is the union's outline.
  for (let i = 1; i < sorted.length; i++) {
    const a = sorted[i - 1], b = sorted[i];
    if (b.y > a.y + a.h + 1) continue;
    const shared = Math.max(0, Math.min(a.x + a.w, b.x + b.w) - Math.max(a.x, b.x));
    s -= 2 * 2 * Math.round(shared);
  }
  return Math.max(0, s);
}
const nextFrame = (page) => page.evaluate(() => new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r))));

// The accessible name and role the browser computed for the focused element:
// the half of "what received focus" that a screen reader would announce.
async function accessibleNameOf(cdp) {
  try {
    const { result } = await cdp.send('Runtime.evaluate', { expression: 'window.__keyboardCurrent', objectGroup: 'keyboard' });
    if (!result.objectId) return {};
    const { node } = await cdp.send('DOM.describeNode', { objectId: result.objectId });
    const tree = await cdp.send('Accessibility.getPartialAXTree', { backendNodeId: node.backendNodeId, fetchRelatives: false });
    const n = tree.nodes.find((x) => x.backendDOMNodeId === node.backendNodeId) || tree.nodes[0];
    await cdp.send('Runtime.releaseObjectGroup', { objectGroup: 'keyboard' });
    return { role: n?.role?.value || '', name: (n?.name?.value || '').replace(/\s+/g, ' ').slice(0, 80) };
  } catch (e) {
    return { role: '?', name: '?' };
  }
}

// Focused against blurred, pixel for pixel. The obscured check has already
// been made at the scroll position Chrome chose; here, a component cut by the
// viewport edge would under-report its own ring, so it is centred first when
// it fits, and the landing-position comparison is kept beside it.
async function indicator(page, d0, shotBase) {
  const viewport = page.viewportSize();
  let d = d0;
  const fits = d0.rect.w + 2 * PAD <= viewport.width && d0.rect.h + 2 * PAD <= viewport.height;
  const inside = (q) => q.x - PAD >= 0 && q.y - PAD >= 0 && q.x + q.w + PAD <= viewport.width && q.y + q.h + PAD <= viewport.height;
  let centred = false;
  const scroll0 = await page.evaluate(() => ({ x: scrollX, y: scrollY }));
  let landing = null;
  if (fits && !inside(d0.rect)) {
    landing = await landingDiff(page, d0, viewport);
    await page.evaluate(() => window.__keyboardCurrent.scrollIntoView({ block: 'center', inline: 'center' }));
    await nextFrame(page);
    d = { ...d0, ...(await page.evaluate(() => window.__keyboard.describe())) };
    centred = true;
  }
  const r = d.rect;
  const x0 = Math.max(0, Math.floor(r.x - PAD)); const y0 = Math.max(0, Math.floor(r.y - PAD));
  const x1 = Math.min(viewport.width, Math.ceil(r.x + r.w + PAD)); const y1 = Math.min(viewport.height, Math.ceil(r.y + r.h + PAD));
  if (x1 - x0 < 2 || y1 - y0 < 2) return { verdict: 'OFFSCREEN' };
  const clip = { x: x0, y: y0, width: x1 - x0, height: y1 - y0 };
  const styleFocused = await page.evaluate(() => window.__keyboard.styleOf(window.__keyboardCurrent));
  const focused = await page.screenshot({ clip, animations: 'disabled', caret: 'hide' });
  await page.evaluate(() => { window.__keyboardCurrent.blur(); });
  await nextFrame(page);
  const styleBlurred = await page.evaluate(() => window.__keyboard.styleOf(window.__keyboardCurrent));
  const blurred = await page.screenshot({ clip, animations: 'disabled', caret: 'hide' });
  const back = await page.evaluate((s) => {
    const el = window.__keyboardCurrent; el.focus({ preventScroll: true });
    // Put the page back where Chrome's own focus scrolling left it, so the
    // NEXT stop's obscured check is made from a user's scroll position and
    // not from one this measurement chose.
    window.scrollTo(s.x, s.y);
    return { active: window.__keyboard.isActive(el), focusVisible: el.matches(':focus-visible') };
  }, scroll0);
  await nextFrame(page);
  const comparison = comparePng(focused, blurred);
  const carriedBy = Object.keys(styleFocused).filter((k) => styleFocused[k] !== styleBlurred[k]);
  const need = perimeterAreaInView(d.boxes.length ? d.boxes : [d.rect], viewport);
  let verdict;
  if (comparison.changed === 0) verdict = 'NONE';
  else if (comparison.qualifying === 0) verdict = 'SUBTLE';
  else verdict = 'VISIBLE';
  const aaa = comparison.qualifying >= need ? 'meets' : 'below';
  if (shotBase && (SHOTS_ALL || verdict !== 'VISIBLE' || aaa === 'below')) {
    fs.writeFileSync(`${shotBase}-focused.png`, focused);
    fs.writeFileSync(`${shotBase}-blurred.png`, blurred);
  }
  return { verdict, aaa, changed: comparison.changed, qualifying: comparison.qualifying, need, maxRatio: comparison.maxRatio, carriedBy, refocused: back, centred, fits, landing };
}

// The focused-against-blurred comparison at the landing position, clipped to
// the viewport, with no scrolling of any kind. Leaves focus where it found it.
async function landingDiff(page, d, viewport) {
  const r = d.rect;
  const x0 = Math.max(0, Math.floor(r.x - PAD)); const y0 = Math.max(0, Math.floor(r.y - PAD));
  const x1 = Math.min(viewport.width, Math.ceil(r.x + r.w + PAD)); const y1 = Math.min(viewport.height, Math.ceil(r.y + r.h + PAD));
  if (x1 - x0 < 2 || y1 - y0 < 2) return { changed: 0, qualifying: 0, note: 'no part of the crop is on screen' };
  const clip = { x: x0, y: y0, width: x1 - x0, height: y1 - y0 };
  const scroll = await page.evaluate(() => {
    const out = []; for (let p = window.__keyboardCurrent.parentElement; p; p = p.parentElement) out.push([p.scrollLeft, p.scrollTop]);
    return { x: scrollX, y: scrollY, ancestors: out };
  });
  const f = await page.screenshot({ clip, animations: 'disabled', caret: 'hide' });
  await page.evaluate(() => window.__keyboardCurrent.blur());
  await nextFrame(page);
  const u = await page.screenshot({ clip, animations: 'disabled', caret: 'hide' });
  await page.evaluate((s) => {
    const el = window.__keyboardCurrent; el.focus({ preventScroll: true });
    let i = 0; for (let p = el.parentElement; p; p = p.parentElement, i++) { if (s.ancestors[i]) { p.scrollLeft = s.ancestors[i][0]; p.scrollTop = s.ancestors[i][1]; } }
    window.scrollTo(s.x, s.y);
  }, scroll);
  await nextFrame(page);
  const c = comparePng(f, u);
  return { changed: c.changed, qualifying: c.qualifying, maxRatio: c.maxRatio };
}

// --------------------------------------------------------------------------
// One page, one viewport.
// --------------------------------------------------------------------------
async function openPage(browser, viewport, state) {
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: viewport.width, height: viewport.height }, deviceScaleFactor: 1, storageState: state || undefined });
  await ctx.addInitScript({ content: HELPERS });
  const page = await ctx.newPage();
  const cdp = await ctx.newCDPSession(page);
  await cdp.send('DOM.enable');
  await cdp.send('Accessibility.enable');
  return { ctx, page, cdp };
}
async function load(page, spec) {
  const response = await page.goto(BASE + spec.path, { waitUntil: 'load', timeout: 60000 });
  const status = response ? response.status() : 0;
  const contentType = response ? (response.headers()['content-type'] || '') : '';
  let mainFound = true;
  try { await page.waitForSelector(MAIN, { timeout: 15000, state: 'attached' }); } catch (e) { mainFound = false; }
  await page.evaluate(() => document.fonts && document.fonts.ready);
  // LET THE PAGE'S OWN SCRIPT FINISH BEFORE ANYTHING IS PREDICTED. At 320 px
  // the admin theme collapses its sidebar with script AFTER `load` - its links
  // end up `visibility: hidden` - and a prediction taken at `load` counted 17
  // links Chrome was never going to Tab to. So: network idle, then read the
  // focus candidates until two readings 250 ms apart agree (at most 3 s).
  await page.waitForLoadState('networkidle', { timeout: 8000 }).catch(() => {});
  let previous = null;
  for (let i = 0; i < 12; i++) {
    const now = await page.evaluate(() => window.__keyboard.focusCandidates().map((c) => c.key).join('\n'));
    if (now === previous) break;
    previous = now;
    await page.waitForTimeout(250);
  }
  if (INJECTIONS[INJECT]) await page.evaluate(INJECTIONS[INJECT]);
  await nextFrame(page);
  return { status, contentType, mainFound };
}

async function walk(page, cdp, direction, cap, opts) {
  const key = direction === 'forward' ? 'Tab' : 'Shift+Tab';
  const stops = []; const seen = new Map(); let exit = null; let prevKey = null; let stuck = 0;
  const startUrl = page.url(); const urlChanges = []; const revisits = []; const visits = new Map();
  for (let i = 1; i <= cap; i++) {
    await page.keyboard.press(key);
    const d = await page.evaluate(() => window.__keyboard.describe());
    if (d.url !== startUrl) urlChanges.push({ press: i, url: d.url });
    if (d.none) { exit = { how: 'left-document', presses: i }; break; }
    if (d.key === prevKey) {
      stuck++;
      if (stuck >= 3) { exit = { how: 'TRAP-no-movement', presses: i, key: d.key, text: d.text }; break; }
      continue;
    }
    stuck = 0;
    if (seen.has(d.key)) {
      const from = seen.get(d.key);
      if (from === 0 && stops.length >= opts.expected) {
        // Back at the walk's own first stop with everything predicted covered.
        // Headless Chrome wraps Shift+Tab from the first element straight to
        // the last one, with no document state in between, so this is how a
        // clean reverse walk ends.
        exit = { how: 'wrapped', presses: i, covered: stops.length, expected: opts.expected };
        break;
      }
      // A REVISIT IS NOT A TRAP. Script can send focus back to an earlier
      // element once (Gin does, from a hidden form button to its sticky copy)
      // and the next key still leaves the page. A trap is focus that keeps
      // circulating: an element reached a THIRD time, or focus that stops.
      const count = (visits.get(d.key) || 1) + 1; visits.set(d.key, count);
      revisits.push({ press: i, key: d.key, text: d.text, firstSeenAt: from + 1, visit: count });
      if (count >= 3) {
        exit = { how: 'TRAP-cycle', presses: i, key: d.key, text: d.text, cycle: stops.slice(from).map((s) => s.text || s.key) };
        break;
      }
      prevKey = d.key;
      continue;
    }
    const hidden = await page.evaluate(() => window.__keyboard.obscured());
    const name = await accessibleNameOf(cdp);
    const stop = { n: stops.length + 1, pressed: key, ...d, accessible: name, obscured: hidden };
    if (opts.measure) {
      const shotBase = opts.shotDir ? path.join(opts.shotDir, String(stop.n).padStart(3, '0')) : null;
      stop.indicator = await indicator(page, d, shotBase);
    }
    seen.set(d.key, stops.length); stops.push(stop); prevKey = d.key;
  }
  if (!exit) exit = { how: 'CAP-reached', presses: cap };
  if (exit.how.startsWith('TRAP') || exit.how === 'WRAPPED-INCOMPLETE') {
    // Is the trap escapable with Escape (2.1.2 allows an advised exit)?
    await page.keyboard.press('Escape');
    await page.keyboard.press(key);
    const after = await page.evaluate(() => window.__keyboard.describe());
    exit.escapeFrees = !!(after.none || (after.key !== exit.key && !stops.some((s) => s.key === after.key && s.n >= (seen.get(exit.key) ?? 0))));
  }
  return { stops, exit, urlChanges, revisits };
}

async function skipLinkTest(browser, viewport, spec, state) {
  const { ctx, page } = await openPage(browser, viewport, state);
  try {
    await load(page, spec);
    await page.keyboard.press('Tab');
    const first = await page.evaluate(() => window.__keyboard.describe());
    const isSkip = !first.none && first.tag === 'a' && first.hrefAttr.startsWith('#') && /skip/i.test(first.text);
    const res = { firstStop: first.none ? '(nothing)' : `${first.tag} "${first.text}"`, isSkip, target: isSkip ? first.hrefAttr : null };
    if (!isSkip) return res;
    await page.keyboard.press('Enter');
    await nextFrame(page);
    const after = await page.evaluate(() => ({ hash: location.hash, d: window.__keyboard.describe() }));
    res.hashAfterEnter = after.hash;
    res.focusAfterEnter = after.d.none ? '(document)' : `${after.d.tag}${after.d.key.includes('#') ? '#' + after.d.key.split('#').pop() : ''}`;
    await page.keyboard.press('Tab');
    const next = await page.evaluate(() => window.__keyboard.describe());
    res.nextTab = next.none ? '(nothing)' : `${next.tag} "${next.text}" in ${next.region}`;
    res.nextInMain = !next.none && next.inMain;
    const hidden = next.none ? null : await page.evaluate(() => window.__keyboard.obscured());
    res.nextEntirelyHidden = hidden ? hidden.pct === 0 : null;
    res.pass = res.isSkip && res.hashAfterEnter === res.target && res.nextInMain && !res.nextEntirelyHidden;
    return res;
  } finally { await ctx.close(); }
}

// Enter / Space against a click, for every non-link control reached, each on a
// fresh load so one activation cannot leak into the next. NOT run on the
// admin set: an admin button can create a snapshot or start a configuration
// import, and a measurement must not change the rig it is measuring.
async function activationTests(browser, viewport, spec, state, stops) {
  const controls = stops.filter((s) => s.depth === 0 && (s.tag === 'button' || s.tag === 'summary' || ['button', 'tab', 'switch', 'menuitem'].includes(s.role) ||
    (s.tag === 'input' && ['submit', 'button', 'reset', 'checkbox', 'radio'].includes(s.type))));
  const out = [];
  for (const c of controls) {
    const r = { control: `${c.tag}${c.type ? '[' + c.type + ']' : ''} "${c.text}"`, key: c.key };
    for (const how of ['click', 'Enter', 'Space']) {
      const { ctx, page } = await openPage(browser, viewport, state);
      try {
        await load(page, spec);
        const before = await page.evaluate(() => window.__keyboard.fingerprint());
        await page.waitForTimeout(150);
        const noise = (await page.evaluate(() => window.__keyboard.fingerprint())) !== before;
        const url0 = page.url();
        const found = await page.evaluate((k) => { const el = document.querySelector(k); if (!el) return false; el.focus(); return document.activeElement === el; }, c.key);
        if (!found) { r[how] = 'not-found'; continue; }
        const nav = page.waitForNavigation({ timeout: 4000 }).then(() => true).catch(() => false);
        if (how === 'click') await page.click(c.key, { timeout: 4000 }).catch(() => {});
        else await page.keyboard.press(how);
        const navigated = await nav;
        await nextFrame(page).catch(() => {});
        let after = '';
        try { after = await page.evaluate(() => window.__keyboard && window.__keyboard.fingerprint()); } catch (e) { after = 'navigated'; }
        r[how] = navigated || page.url() !== url0 ? 'navigated' : (after !== before ? 'changed' : (noise ? 'noisy' : 'no-effect'));
      } finally { await ctx.close(); }
    }
    // A checkbox or radio is operated by Space only (Enter submits the form);
    // every other control here must answer both Enter and Space. A control a
    // click does nothing to cannot fail this comparison.
    const spaceOnly = c.tag === 'input' && ['checkbox', 'radio'].includes(c.type);
    const works = (v) => v === 'navigated' || v === 'changed' || v === 'noisy';
    r.pass = !works(r.click) || ((spaceOnly || works(r.Enter)) && works(r.Space));
    out.push(r);
  }
  return out;
}

// Links: Enter must follow the link. Sampled - the first link of every
// landmark region the walk passed through - each on a fresh load. On the
// admin set, links whose address or text names an action are left alone.
const UNSAFE = /delete|rollback|restore|revert|import|export|sync|logout|uninstall|cron|run/i;
async function linkTests(browser, viewport, spec, state, stops) {
  const regionsSeen = new Set(); const sample = [];
  for (const s of stops) {
    if (s.depth !== 0 || s.tag !== 'a' || !s.hrefAttr || s.hrefAttr.startsWith('#') || s.hrefAttr.startsWith('javascript')) continue;
    if (SET === 'admin' && (UNSAFE.test(s.hrefAttr) || UNSAFE.test(s.text))) continue;
    if (regionsSeen.has(s.region)) continue;
    regionsSeen.add(s.region); sample.push(s);
  }
  const out = [];
  for (const s of sample) {
    const { ctx, page } = await openPage(browser, viewport, state);
    try {
      await load(page, spec);
      const ok = await page.evaluate((k) => { const el = document.querySelector(k); if (!el) return false; el.focus(); return document.activeElement === el; }, s.key);
      if (!ok) { out.push({ link: s.text, region: s.region, result: 'not-found', pass: false }); continue; }
      const nav = page.waitForNavigation(NAVIGATION_WAIT).then(() => true).catch(() => false);
      await page.keyboard.press('Enter');
      const navigated = await nav;
      const landed = page.url();
      const expected = s.href.split('#')[0];
      out.push({ link: s.text, region: s.region, expected, landed, pass: navigated && landed.split('#')[0] === expected });
    } finally { await ctx.close(); }
  }
  return out;
}

// The register flow a citizen actually uses: reach the filter form by Tab,
// type or choose, submit with Enter, then clear the filter by keyboard.
const EXPOSED = 'form.views-exposed-form, form[id^="views-exposed-form"]';
async function registerFlow(browser, viewport, spec, state) {
  const { ctx, page } = await openPage(browser, viewport, state);
  try {
    await load(page, spec);
    const form = await page.evaluate((sel) => {
      const f = document.querySelector(sel);
      if (!f) return null;
      const input = f.querySelector('input[type=text], input[type=search]');
      // The term comes from THIS view's own table (the title link of its
      // first row), so a search that is supposed to match does.
      let v = f.parentElement; while (v && !v.querySelector('table tbody tr')) v = v.parentElement;
      const rows = v ? v.querySelectorAll('table tbody tr').length : 0;
      const firstTitle = v ? v.querySelector('table tbody tr td a') : null;
      return { id: f.id, input: input ? input.name : null, rows, term: firstTitle ? (firstTitle.innerText.trim().split(/\s+/).find((w) => w.replace(/[^\p{L}]/gu, '').length >= 5) || '').replace(/[^\p{L}\p{N}]/gu, '') : '' };
    }, EXPOSED);
    if (!form) return { present: false };
    const res = { present: true, rowsBefore: form.rows, field: form.input };
    if (!form.input) res.note = 'no text field in this exposed form';
    const where = () => page.evaluate((sel) => { const x = window.__keyboard.describe(); const f = document.activeElement && document.activeElement.closest(sel); return { ...x, inForm: !!f }; }, EXPOSED);
    // Reach the first control of the exposed form by Tab alone.
    let presses = 0; let d;
    for (;;) {
      await page.keyboard.press('Tab'); presses++;
      d = await where();
      if (d.none || d.inForm || presses > 200) break;
    }
    res.tabsToForm = d.inForm ? presses : null;
    if (!d.inForm) return res;
    if (form.input) {
      let guard = 0;
      while (!(d.tag === 'input' && ['text', 'search', ''].includes(d.type) && d.inForm) && guard++ < 15) {
        await page.keyboard.press('Tab'); presses++;
        d = await where();
      }
      res.tabsToField = presses;
      res.term = form.term;
      await page.keyboard.type(form.term);
      const nav = page.waitForNavigation(NAVIGATION_WAIT).then(() => true).catch(() => false);
      await page.keyboard.press('Enter');
      res.enterSubmitted = await nav;
      await page.waitForLoadState('load', { timeout: 60000 }).catch(() => {});
      await page.waitForSelector(MAIN, { timeout: 15000 }).catch(() => {});
      const after = await page.evaluate((n) => ({ path: location.pathname, query: location.search, rows: document.querySelectorAll('table tbody tr').length, hit: document.body.innerText.includes(n) }), form.term);
      res.pathAfter = after.path; res.queryAfter = after.query; res.rowsAfter = after.rows; res.termOnPage = after.hit;
    } else {
      // Only selects: move the first one by keyboard, then Enter on the submit control.
      await page.keyboard.press('ArrowDown');
      let guard = 0;
      while (!(d.tag === 'input' && d.type === 'submit') && guard++ < 15) {
        await page.keyboard.press('Tab'); presses++;
        d = await where();
      }
      const nav = page.waitForNavigation(NAVIGATION_WAIT).then(() => true).catch(() => false);
      await page.keyboard.press('Enter');
      res.enterSubmitted = await nav;
      await page.waitForLoadState('load', { timeout: 60000 }).catch(() => {});
      await page.waitForSelector(MAIN, { timeout: 15000 }).catch(() => {});
      const after = await page.evaluate(() => ({ path: location.pathname, query: location.search, rows: document.querySelectorAll('table tbody tr').length }));
      res.pathAfter = after.path; res.queryAfter = after.query; res.rowsAfter = after.rows;
    }
    // After a submission the page is new: where does the first Tab land?
    // Clear the filter by keyboard: Tab to a Reset control and press Enter.
    res.resetPresent = await page.evaluate((sel) => Array.from(document.querySelectorAll(sel.split(',').map((s) => s.trim() + ' input[type=submit], ' + s.trim() + ' button').join(', ')))
      .some((b) => /reset/i.test(b.value || b.innerText || '')), EXPOSED);
    let guard = 0; let r;
    for (;;) {
      await page.keyboard.press('Tab');
      r = await page.evaluate(() => window.__keyboard.describe());
      if (guard === 0) res.firstTabAfterSubmit = r.none ? '(nothing)' : `${r.tag} "${r.text}"`;
      if (r.none || /reset/i.test(r.text) || guard++ > 250) break;
    }
    res.resetReached = !r.none && /reset/i.test(r.text);
    if (res.resetReached) {
      const nav = page.waitForNavigation(NAVIGATION_WAIT).then(() => true).catch(() => false);
      await page.keyboard.press('Enter');
      res.resetNavigated = await nav;
      await page.waitForLoadState('load', { timeout: 60000 }).catch(() => {});
      await page.waitForSelector(MAIN, { timeout: 15000 }).catch(() => {});
      const back = await page.evaluate(() => ({ path: location.pathname, query: location.search, rows: document.querySelectorAll('table tbody tr').length }));
      res.pathAfterReset = back.path; res.queryAfterReset = back.query; res.rowsAfterReset = back.rows;
    }
    return res;
  } finally { await ctx.close(); }
}

// Every overflowing scroll container, in either direction. The question is
// what the KEYBOARD can do with it, so "focusable" is decided by the walk, not
// by a tabindex attribute: Chrome 130 and later make a scroll container with
// nothing focusable inside it reachable by Tab on its own (the walk reached
// one on /analyze that carries no tabindex). A region that passes only for
// that reason is marked, because a browser without the behaviour fails it.
async function scrollRegions(page, reachedKeys) {
  const list = await page.evaluate(() => Array.from(document.querySelectorAll('*')).map((el) => {
    // A region nobody can see is not one anybody needs to scroll. The content
    // of a CLOSED <details> keeps its layout and so measures as overflowing:
    // 43 such lists on one admin page were reported unscrollable before this.
    if (el.checkVisibility && !el.checkVisibility({ visibilityProperty: true })) return null;
    const cs = getComputedStyle(el);
    const x = ['auto', 'scroll'].includes(cs.overflowX) && el.scrollWidth > el.clientWidth + 1;
    const y = ['auto', 'scroll'].includes(cs.overflowY) && el.scrollHeight > el.clientHeight + 1 && el !== document.scrollingElement && el !== document.body;
    if (!x && !y) return null;
    return { key: window.__keyboard.cssPath(el), axis: x ? 'x' : 'y', tabindex: el.getAttribute('tabindex'), table: !!el.querySelector('table'),
      // <summary> is focusable too; leaving it out called a list of <details> unscrollable.
      focusableInside: !!el.querySelector('a[href], button, input:not([type=hidden]), select, textarea, summary, iframe, audio[controls], video[controls], [contenteditable=""], [contenteditable="true"], [tabindex]:not([tabindex="-1"])') };
  }).filter(Boolean));
  for (const s of list) {
    s.reachedByTab = reachedKeys.has(s.key);
    s.focusable = await page.evaluate((k) => { const el = document.querySelector(k); if (!el) return false; el.scrollLeft = 0; el.scrollTop = 0; el.focus(); return document.activeElement === el; }, s.key);
    if (!s.focusable) { s.keyboardScrolls = false; continue; }
    const key = s.axis === 'x' ? 'ArrowRight' : 'ArrowDown';
    for (let i = 0; i < 6; i++) await page.keyboard.press(key);
    // Keyboard scrolling is ANIMATED in Chrome: read at once, the offset is
    // still 0 (measured: 0 at once, 29 at 50 ms, 120 by 300 ms).
    await page.waitForTimeout(700);
    s.scrolledBy = await page.evaluate((a) => { const el = document.querySelector(a.k); return a.x ? el.scrollLeft : el.scrollTop; }, { k: s.key, x: s.axis === 'x' });
    s.keyboardScrolls = s.scrolledBy > 0;
  }
  for (const s of list) {
    // Operable by keyboard when its content can be reached by Tab (focus
    // scrolling then reveals it) or when Tab reaches the region and the arrow
    // keys scroll it.
    s.operable = s.focusableInside || (s.reachedByTab && s.keyboardScrolls);
    s.reliesOnBrowser = !s.focusableInside && s.tabindex === null && s.reachedByTab;
  }
  return list;
}

async function reflow(page) {
  return page.evaluate(() => {
    const de = document.documentElement; const vw = de.clientWidth;
    const inScroller = (el) => { for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) { const cs = getComputedStyle(p); if (['auto', 'scroll', 'hidden', 'clip'].includes(cs.overflowX)) return true; } return false; };
    const offenders = [];
    for (const el of document.body.querySelectorAll('*')) {
      const r = el.getBoundingClientRect();
      if (r.width <= 1 || r.height <= 1 || r.right <= vw + 1) continue;
      const cs = getComputedStyle(el);
      if (cs.position === 'fixed' || inScroller(el)) continue;
      if (offenders.some((o) => o.el.contains(el))) continue;
      offenders.push({ el, key: window.__keyboard.nameOf(el) + ' in ' + (el.parentElement ? window.__keyboard.nameOf(el.parentElement) : '-'), right: Math.round(r.right) });
    }
    const clipped = [];
    for (const el of document.body.querySelectorAll('*')) {
      const cs = getComputedStyle(el);
      if (!['hidden', 'clip'].includes(cs.overflowX) || el.scrollWidth <= el.clientWidth + 1) continue;
      const r = el.getBoundingClientRect(); if (r.width <= 2 || r.height <= 2) continue;
      if (!el.innerText || !el.innerText.trim()) continue;
      clipped.push({ key: window.__keyboard.nameOf(el) + ' in ' + (el.parentElement ? window.__keyboard.nameOf(el.parentElement) : '-'), scrollWidth: el.scrollWidth, clientWidth: el.clientWidth });
    }
    return { viewport: vw, scrollWidth: de.scrollWidth, overflowPx: de.scrollWidth - vw, offenders: offenders.map(({ el, ...o }) => o), clipped };
  });
}

async function axeRun(page) {
  await page.addScriptTag({ content: AXE_SOURCE });
  return page.evaluate(async () => {
    const r = await window.axe.run(document);
    // target-size is DISABLED by default in axe-core 4.13, so a default run
    // (which is what the CI gate makes) never asks it. It is asked here by
    // name, and its own four buckets are what the 2.5.8 row reports.
    const t = await window.axe.run(document, { runOnly: { type: 'rule', values: ['target-size'] } });
    const bucketNodes = (b) => (t[b].find((x) => x.id === 'target-size') || { nodes: [] }).nodes;
    const rule = window.axe.getRules().find((x) => x.ruleId === 'target-size');
    const audit = (window.axe._audit && window.axe._audit.rules || []).find((x) => x.id === 'target-size');
    const describeNodes = (nodes) => nodes.slice(0, 6).map((n) => n.target.join(' ') + ' :: ' + (n.any.concat(n.all, n.none).map((c) => c.message).join('; ')).slice(0, 160));
    return {
      rules: r.passes.length + r.violations.length + r.incomplete.length + r.inapplicable.length,
      violations: r.violations.map((v) => ({ id: v.id, impact: v.impact, nodes: v.nodes.length, where: v.nodes.slice(0, 4).map((n) => n.target.join(' ')) })),
      incomplete: r.incomplete.map((v) => ({ id: v.id, nodes: v.nodes.length })),
      targetSize: {
        inDefaultRun: ['passes', 'violations', 'incomplete', 'inapplicable'].filter((b) => r[b].some((x) => x.id === 'target-size')).join('+') || 'absent',
        bucket: ['passes', 'violations', 'incomplete', 'inapplicable'].filter((b) => t[b].some((x) => x.id === 'target-size')).join('+') || 'absent',
        passNodes: bucketNodes('passes').length,
        violationNodes: bucketNodes('violations').length,
        violationWhere: describeNodes(bucketNodes('violations')),
        incompleteNodes: bucketNodes('incomplete').length,
        incompleteWhere: describeNodes(bucketNodes('incomplete')),
        tags: rule ? rule.tags : null, enabledByDefault: audit ? audit.enabled !== false : null,
      },
    };
  });
}

function orderAnalysis(stops, predicted) {
  // DOM order of the stops that are in the predicted list, against Tab order.
  const index = new Map(predicted.map((p, i) => [p.key, i]));
  const seq = stops.map((s) => index.get(s.key)).filter((x) => x !== undefined);
  let domInversions = 0;
  for (let i = 1; i < seq.length; i++) if (seq[i] < seq[i - 1]) domInversions++;
  // Upward visual jumps, classified: moving to a new column is normal.
  const jumps = [];
  for (let i = 1; i < stops.length; i++) {
    const a = stops[i - 1], b = stops[i];
    if (b.docY < a.docY - 4 && b.docY + b.rect.h < a.docY + a.rect.h) {
      const newColumn = b.docX > a.docX + 8;
      jumps.push({ from: a.n, to: b.n, fromText: a.text, toText: b.text, up: Math.round(a.docY - b.docY), kind: newColumn ? 'to-the-right (new column)' : 'UP-AND-LEFT' });
    }
  }
  return { positiveTabindex: predicted.filter((p) => p.positive).length, domInversions, upwardJumps: jumps };
}

function regionTrail(stops) {
  const trail = [];
  for (const s of stops) {
    const last = trail[trail.length - 1];
    if (last && last.region === s.region) last.n++; else trail.push({ region: s.region, n: 1 });
  }
  return trail.map((t) => `${t.region} x${t.n}`).join(' -> ');
}

// --------------------------------------------------------------------------
async function main() {
  const browser = await chromium.launch({ channel: 'chrome', headless: true });
  const version = browser.version();
  let state = null;
  if (SET === 'admin') {
    if (!args.login) { console.error('--set=admin needs --login=<one-time login URL>'); process.exit(2); }
    const ctx = await browser.newContext({ ignoreHTTPSErrors: true });
    const p = await ctx.newPage();
    await p.goto(args.login, { waitUntil: 'load' });
    const loggedIn = await p.evaluate(() => document.body.classList.contains('user-logged-in') || !!document.querySelector('a[href*="/user/logout"]'));
    state = await ctx.storageState();
    await ctx.close();
    if (!loggedIn) { console.error('HARNESS FAILURE: the one-time login did not produce a logged-in session; walking now would walk a 403.'); process.exit(2); }
  }
  const results = { harness: { playwright: PLAYWRIGHT_VERSION, chrome: version, axe: AXE_VERSION, base: BASE, set: SET, inject: INJECT, commit: DERIVED.commit, main: MAIN, date: new Date().toISOString() }, pages: [] };
  let harnessFailures = 0; const findings = [];
  const shotRoot = SHOTS ? path.resolve(SHOTS) : null;

  for (const viewport of VIEWPORTS) {
    for (const spec of specs) {
      const res = { name: spec.name, path: spec.path, viewport: viewport.label };
      const { ctx, page, cdp } = await openPage(browser, viewport, state);
      try {
        const loaded = await load(page, spec);
        Object.assign(res, loaded);
        if (!loaded.contentType.includes('text/html')) { res.skipped = `not HTML (${loaded.contentType})`; results.pages.push(res); console.log(`[skip] ${spec.name} @${viewport.label}: ${res.skipped}`); continue; }
        // On the anonymous pages a missing <main> means the theme did not
        // render, which is a harness failure. On the admin set it is a fact
        // about the route (the dependency-graph iframe document has none):
        // it is walked, recorded, and the skip-link check is not applicable.
        if (loaded.status !== spec.expectStatus || (!loaded.mainFound && SET !== 'admin')) {
          res.harnessFailure = `status ${loaded.status} (expected ${spec.expectStatus}), main ${loaded.mainFound ? 'found' : 'MISSING'}`;
          harnessFailures++; results.pages.push(res); console.log(`[HARNESS FAILURE] ${spec.name} @${viewport.label}: ${res.harnessFailure}`); continue;
        }
        res.initialActive = await page.evaluate(() => (document.activeElement === document.body ? 'body' : window.__keyboard.cssPath(document.activeElement)));
        const predicted = await page.evaluate(() => window.__keyboard.focusCandidates());
        res.predicted = predicted.length;
        const shotDir = shotRoot ? path.join(shotRoot, INJECT, viewport.label, spec.name.replace(/[^\w.-]+/g, '_')) : null;
        if (shotDir) fs.mkdirSync(shotDir, { recursive: true });
        const cap = Math.min(800, predicted.length * 2 + 20);
        const forward = await walk(page, cdp, 'forward', cap, { measure: true, shotDir, expected: predicted.length });
        const reverse = await walk(page, cdp, 'reverse', cap, { measure: false, expected: predicted.length });
        res.forward = forward;
        res.reverse = { exit: reverse.exit, urlChanges: reverse.urlChanges, revisits: reverse.revisits, stops: reverse.stops.map((s) => ({ n: s.n, key: s.key, text: s.text, obscured: s.obscured, rect: s.rect })) };
        const reached = new Set(forward.stops.map((s) => s.key));
        res.reached = forward.stops.length;
        // Read the candidates again after both walks. "Unreached" is only what
        // was a candidate before AND after; anything that stopped being one
        // while the walks ran is reported on its own line, never dropped.
        const afterKeys = new Set((await page.evaluate(() => window.__keyboard.focusCandidates())).map((p) => p.key));
        res.droppedDuringWalk = predicted.filter((p) => !afterKeys.has(p.key)).map((p) => `${p.tag} "${p.text}"`);
        res.unreached = predicted.filter((p) => !reached.has(p.key) && afterKeys.has(p.key)).map((p) => `${p.tag} "${p.text}"`);
        res.notPredicted = forward.stops.filter((s) => !predicted.some((p) => p.key === s.key)).map((s) => `${s.tag} "${s.text}"`);
        const clean = (h) => h === 'left-document' || h === 'wrapped';
        const fk = forward.stops.map((s) => s.key); const rk = reverse.stops.map((s) => s.key).reverse();
        res.reverseMirrors = clean(forward.exit.how) && clean(reverse.exit.how) ? JSON.stringify(fk) === JSON.stringify(rk) : null;
        res.order = orderAnalysis(forward.stops, predicted);
        res.trail = regionTrail(forward.stops);
        res.scrollRegions = await scrollRegions(page, reached);
        res.reflow = await reflow(page);
        res.axe = await axeRun(page);
      } catch (e) {
        res.harnessFailure = `exception: ${e.message.split('\n')[0]}`; harnessFailures++;
        console.log(`[HARNESS FAILURE] ${spec.name} @${viewport.label}: ${res.harnessFailure}`);
      } finally { await ctx.close(); }
      if (!res.harnessFailure && !res.skipped) {
        res.skip = res.mainFound ? await skipLinkTest(browser, viewport, spec, state) : { applicable: false, pass: true, firstStop: '(not tested)', reason: 'this route renders no <main> landmark, so there is nothing for a skip link to reach' };
        res.activation = SET === 'admin' ? [] : await activationTests(browser, viewport, spec, state, res.forward.stops);
        res.links = await linkTests(browser, viewport, spec, state, res.forward.stops);
        res.register = await registerFlow(browser, viewport, spec, state);
        results.pages.push(res);
        summarise(res, findings);
      } else if (!results.pages.includes(res)) {
        results.pages.push(res);
      }
    }
  }
  await browser.close();

  // ---- totals ---------------------------------------------------------------
  const ok = results.pages.filter((p) => !p.harnessFailure && !p.skipped);
  for (const z of ok.filter((p) => p.reached === 0)) { harnessFailures++; console.log(`HARNESS FAILURE: ${z.name} @${z.viewport} reached ZERO elements - that is not a clean page.`); }
  const sum = (f) => ok.reduce((a, p) => a + f(p), 0);
  const indicatorCount = (v) => sum((p) => p.forward.stops.filter((s) => s.indicator && s.indicator.verdict === v).length);
  const walks = (p) => [p.forward, p.reverse];
  const totals = {
    pageViewports: ok.length, skippedNotHtml: results.pages.filter((p) => p.skipped).length, harnessFailures,
    stopsReached: sum((p) => p.reached), predicted: sum((p) => p.predicted), unreached: sum((p) => p.unreached.length), notPredicted: sum((p) => p.notPredicted.length),
    droppedDuringWalk: sum((p) => p.droppedDuringWalk.length),
    indicatorVisible: indicatorCount('VISIBLE'), indicatorSubtle: indicatorCount('SUBTLE'), indicatorNone: indicatorCount('NONE'), indicatorOffscreen: indicatorCount('OFFSCREEN'),
    aaaMeets: sum((p) => p.forward.stops.filter((s) => s.indicator && s.indicator.aaa === 'meets').length),
    obscuredEntirelyForward: sum((p) => p.forward.stops.filter((s) => s.obscured && s.obscured.pct === 0).length),
    obscuredPartlyForward: sum((p) => p.forward.stops.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < FULL).length),
    obscuredUnderHalfForward: sum((p) => p.forward.stops.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < 50).length),
    obscuredEntirelyReverse: sum((p) => p.reverse.stops.filter((s) => s.obscured && s.obscured.pct === 0).length),
    obscuredPartlyReverse: sum((p) => p.reverse.stops.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < FULL).length),
    traps: sum((p) => walks(p).filter((w) => w.exit.how.startsWith('TRAP') || w.exit.how === 'WRAPPED-INCOMPLETE' || w.exit.how === 'CAP-reached').length),
    walksClean: sum((p) => walks(p).filter((w) => w.exit.how === 'left-document' || w.exit.how === 'wrapped').length),
    reverseMirrorsForward: ok.filter((p) => p.reverseMirrors === true).length,
    urlChangesDuringWalks: sum((p) => p.forward.urlChanges.length + p.reverse.urlChanges.length),
    revisits: sum((p) => p.forward.revisits.length + p.reverse.revisits.length),
    routesWithoutMain: ok.filter((p) => !p.mainFound).length,
    skipPass: ok.filter((p) => p.skip && p.skip.pass).length,
    controlsTested: sum((p) => p.activation.length), controlsFailing: sum((p) => p.activation.filter((a) => !a.pass).length),
    linksTested: sum((p) => p.links.length), linksFollowed: sum((p) => p.links.filter((l) => l.pass).length),
    registerFlows: ok.filter((p) => p.register && p.register.present).length,
    scrollRegions: sum((p) => p.scrollRegions.length), scrollRegionsOperable: sum((p) => p.scrollRegions.filter((s) => s.operable).length),
    scrollRegionsKeyboardScrolls: sum((p) => p.scrollRegions.filter((s) => s.keyboardScrolls).length), scrollRegionsRelyOnBrowser: sum((p) => p.scrollRegions.filter((s) => s.reliesOnBrowser).length),
    axeViolationNodes: sum((p) => p.axe.violations.reduce((a, v) => a + v.nodes, 0)),
    targetSizeViolationNodes: sum((p) => p.axe.targetSize.violationNodes), targetSizePassNodes: sum((p) => p.axe.targetSize.passNodes), targetSizeIncompleteNodes: sum((p) => p.axe.targetSize.incompleteNodes),
    reflowOverflowPages: ok.filter((p) => p.viewport.startsWith('320x') && p.reflow.overflowPx > 0).length,
  };
  results.totals = totals; results.findings = findings;
  fs.writeFileSync(OUT, JSON.stringify(results, null, 1));
  console.log('\n==== TOTALS ' + JSON.stringify(results.harness));
  for (const [k, v] of Object.entries(totals)) console.log(`  ${k}: ${v}`);
  console.log(`  findings: ${findings.length}`);
  for (const f of findings) console.log(`   - ${f}`);
  console.log(`results written: ${OUT}`);
  process.exit(harnessFailures ? 2 : (findings.length ? 1 : 0));
}

function summarise(p, findings) {
  const tag = `${p.name} @${p.viewport}`;
  const st = p.forward.stops;
  const c = (v) => st.filter((s) => s.indicator && s.indicator.verdict === v).length;
  const aaa = st.filter((s) => s.indicator && s.indicator.aaa === 'meets').length;
  const hiddenF = st.filter((s) => s.obscured && s.obscured.pct === 0);
  const partF = st.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < FULL);
  const underHalfF = st.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < 50);
  const hiddenR = p.reverse.stops.filter((s) => s.obscured && s.obscured.pct === 0);
  const partR = p.reverse.stops.filter((s) => s.obscured && s.obscured.pct > 0 && s.obscured.pct < FULL);
  const why = (list) => { const m = {}; for (const s of list) for (const [k, v] of Object.entries({ ...s.obscured.coveredBy, ...s.obscured.clippedBy })) m[k] = (m[k] || 0) + v; return Object.entries(m).map(([k, v]) => `${k}:${v}`).join(', ') || '-'; };
  const failingControls = p.activation.filter((a) => !a.pass);
  console.log(`\n[${tag}] ${p.path}  status ${p.status}`);
  console.log(`  2.1.1 reach : ${p.reached} reached of ${p.predicted} predicted (unreached ${p.unreached.length}, not predicted ${p.notPredicted.length}, stopped being candidates during the walk ${p.droppedDuringWalk.length}); walk exit: ${p.forward.exit.how} after ${p.forward.exit.presses} presses`);
  console.log(`  2.1.2 traps : forward ${p.forward.exit.how}, reverse ${p.reverse.exit.how}`);
  console.log(`  2.4.3 order : positive tabindex ${p.order.positiveTabindex}; DOM-order inversions ${p.order.domInversions}; reverse walk mirrors forward: ${p.reverseMirrors}; upward jumps ${p.order.upwardJumps.length} (${p.order.upwardJumps.filter((j) => j.kind === 'UP-AND-LEFT').length} up-and-left)`);
  console.log(`        trail : ${p.trail}`);
  console.log(`  3.2.1       : address changes during the walks ${p.forward.urlChanges.length + p.reverse.urlChanges.length}`);
  console.log(`  2.4.7 focus : visible ${c('VISIBLE')} · subtle ${c('SUBTLE')} · NONE ${c('NONE')} · offscreen ${c('OFFSCREEN')}   (2.4.13 AAA area+3:1 met by ${aaa} of ${st.length})`);
  console.log(`  2.4.11 hide : forward entirely hidden ${hiddenF.length}, partly ${partF.length} (under 50% visible ${underHalfF.length}); reverse entirely hidden ${hiddenR.length}, partly ${partR.length}; causes: ${why(partF.concat(partR, hiddenF, hiddenR))}`);
  if (p.skip.applicable === false) console.log(`  2.4.1 skip  : NOT APPLICABLE - ${p.skip.reason}`);
  else console.log(`  2.4.1 skip  : first stop ${p.skip.firstStop}; ${p.skip.isSkip ? `Enter -> hash ${p.skip.hashAfterEnter}, focus ${p.skip.focusAfterEnter}; next Tab ${p.skip.nextTab}; pass ${p.skip.pass}` : 'NOT a skip link'}`);
  if (!p.mainFound) console.log('  landmark    : this route renders NO <main> element');
  const rv = p.forward.revisits.concat(p.reverse.revisits);
  if (rv.length) console.log(`  revisits    : ${rv.map((r) => `"${r.text}" again at press ${r.press} (first reached as stop ${r.firstSeenAt})`).join('; ')}`);
  console.log(`  activation  : ${SET === 'admin' ? 'NOT RUN on the admin set (it would change the rig)' : `${p.activation.length} non-link controls, click vs Enter vs Space; failing ${failingControls.length}`}; links sampled ${p.links.length}, followed on Enter ${p.links.filter((l) => l.pass).length}`);
  if (p.register.present) console.log(`  register    : ${JSON.stringify(p.register)}`);
  console.log(`  scrollers   : ${p.scrollRegions.map((s) => `${s.table ? 'table' : 'other'} (${s.axis}) ${s.tabindex !== null ? 'tabindex=' + s.tabindex : 'no tabindex'}, reached by Tab ${s.reachedByTab}, arrows scroll ${s.keyboardScrolls}${s.focusableInside ? ', focusable content inside' : ''}${s.reliesOnBrowser ? ', RELIES ON THE BROWSER' : ''}`).join('; ') || 'none overflowing'}`);
  console.log(`  1.4.10      : scrollWidth ${p.reflow.scrollWidth} vs viewport ${p.reflow.viewport} (overflow ${p.reflow.overflowPx}px); offenders ${p.reflow.offenders.length}; text clipped ${p.reflow.clipped.length}`);
  console.log(`  axe         : ${p.axe.rules} rules, ${p.axe.violations.reduce((a, v) => a + v.nodes, 0)} violation nodes [${p.axe.violations.map((v) => v.id + ' x' + v.nodes).join(', ')}]; target-size [default run: ${p.axe.targetSize.inDefaultRun}; asked by name: ${p.axe.targetSize.bucket}] (pass nodes ${p.axe.targetSize.passNodes}, violations ${p.axe.targetSize.violationNodes}, incomplete ${p.axe.targetSize.incompleteNodes})`);
  // Findings: what a machine can call a failure on its own.
  if (p.unreached.length) findings.push(`${tag} 2.1.1 unreached by Tab: ${p.unreached.join(' | ')}`);
  for (const s of st.filter((s) => s.indicator && s.indicator.verdict === 'NONE')) findings.push(`${tag} 2.4.7 NO visible indicator at stop ${s.n}: ${s.tag} "${s.text}" (${s.region})`);
  for (const s of st.filter((s) => s.indicator && s.indicator.verdict === 'OFFSCREEN')) findings.push(`${tag} 2.4.7 focused element is OFF-SCREEN at stop ${s.n}: ${s.tag} "${s.text}"`);
  for (const w of [p.forward, p.reverse]) if (w.exit.how.startsWith('TRAP') || w.exit.how === 'CAP-reached' || w.exit.how === 'WRAPPED-INCOMPLETE') findings.push(`${tag} 2.1.2 ${w.exit.how} at "${w.exit.text || w.exit.key || ''}" (Escape frees it: ${w.exit.escapeFrees})`);
  for (const w of [p.forward, p.reverse]) for (const u of w.urlChanges) findings.push(`${tag} 3.2.1 the address changed while focus moved (press ${u.press}): ${u.url}`);
  for (const s of hiddenF) findings.push(`${tag} 2.4.11 entirely hidden (forward) at stop ${s.n}: ${s.tag} "${s.text}" by ${why([s])}`);
  for (const s of hiddenR) findings.push(`${tag} 2.4.11 entirely hidden (reverse) at stop ${s.n}: ${s.tag} "${s.text}" by ${why([s])}`);
  if (p.skip.applicable !== false && !p.skip.pass) findings.push(`${tag} 2.4.1 skip link: ${JSON.stringify(p.skip)}`);
  for (const a of failingControls) findings.push(`${tag} 2.1.1 activation: ${a.control} click=${a.click} Enter=${a.Enter} Space=${a.Space}`);
  for (const l of p.links.filter((l) => !l.pass)) findings.push(`${tag} 2.1.1 link not followed on Enter: "${l.link}" (${l.region}) expected ${l.expected} landed ${l.landed}`);
  if (p.register.present && p.register.field && !(p.register.enterSubmitted && p.register.rowsAfter > 0 && p.register.termOnPage)) findings.push(`${tag} 2.1.1 register search by keyboard: ${JSON.stringify(p.register)}`);
  if (p.register.present && !p.register.field && !p.register.enterSubmitted) findings.push(`${tag} 2.1.1 register filter by keyboard: ${JSON.stringify(p.register)}`);
  if (p.register.present && p.register.resetPresent && !p.register.resetReached) findings.push(`${tag} 2.1.1 register: a Reset control exists after filtering and Tab never reached it`);
  for (const s of p.scrollRegions.filter((s) => !s.operable)) findings.push(`${tag} 2.1.1 scroll region not operable by keyboard: ${s.key}`);
  if (p.viewport.startsWith('320x') && p.reflow.overflowPx > 0) findings.push(`${tag} 1.4.10 page scrolls horizontally by ${p.reflow.overflowPx}px: ${p.reflow.offenders.map((o) => o.key).join(' | ')}`);
  if (p.axe.targetSize.violationNodes) findings.push(`${tag} 2.5.8 target-size: ${p.axe.targetSize.violationNodes} node(s): ${p.axe.targetSize.violationWhere.join(' | ')}`);
  for (const v of p.axe.violations) findings.push(`${tag} axe ${v.id} x${v.nodes} @ ${v.where.join(' | ')}`);
}

main().catch((e) => { console.error('HARNESS FAILURE (exception):', e); process.exit(2); });
````

### `probe-table.mjs`

28 lines · sha256 `0f7c37b85a625f9edef5593a9f681b4ffee4d0e783ffb8ffef1ae3ac2b3bef37`

<!-- file: probe-table.mjs -->
````js
// Probe: for every Tab stop inside the register table, how much of the focused
// element lies inside the wrapper's visible box at the moment focus lands?
import { chromium } from 'playwright-core';
const url = process.argv[2] || 'https://agora-kbd.ddev.site/contracts';
const w = Number(process.argv[3] || 1280), h = Number(process.argv[4] || 800);
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: w, height: h } });
const page = await ctx.newPage();
await page.goto(url, { waitUntil: 'load' });
let last = '';
for (let i = 1; i < 120; i++) {
  await page.keyboard.press('Tab');
  await page.waitForTimeout(120);
  const d = await page.evaluate(() => {
    const a = document.activeElement; if (!a || a === document.body) return null;
    const sc = a.closest('.agora-table__scroll'); if (!sc || a === sc) return { out: true, t: (a.innerText || '').trim().slice(0, 20) };
    const r = a.getBoundingClientRect(); const s = sc.getBoundingClientRect();
    const left = Math.max(r.left, s.left + sc.clientLeft), right = Math.min(r.right, s.left + sc.clientLeft + sc.clientWidth);
    const vis = Math.max(0, right - left);
    const th = a.closest('td, th'); const col = th ? th.cellIndex : -1;
    return { t: (a.innerText || '').trim().replace(/\s+/g, ' ').slice(0, 26), col, x: Math.round(r.left), w: Math.round(r.width), sl: sc.scrollLeft, visiblePx: Math.round(vis), visiblePct: Math.round((100 * vis) / r.width) };
  });
  if (!d) break;
  if (d.out) { if (last === 'in') break; continue; }
  last = 'in';
  console.log(i, JSON.stringify(d));
}
await browser.close();
````

### `probe-scroll.mjs`

26 lines · sha256 `d5144abf7eae9934b91ef95e1864b74812d0f3fef9bd8903489cf51c6a3aee6d`

<!-- file: probe-scroll.mjs -->
````js
// Supporting probe: keyboard scrolling of the register table's scroll wrapper
// is ANIMATED in Chrome, so an offset read the moment the key is pressed is 0.
// Prints the offset against real elapsed time after three ArrowRight presses.
//
// Usage: node probe-scroll.mjs [url] [width] [height]
import { chromium } from 'playwright-core';
const url = process.argv[2] || 'https://agora-kbd.ddev.site/contracts';
const w = Number(process.argv[3] || 1280), h = Number(process.argv[4] || 800);
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: w, height: h } });
const page = await ctx.newPage();
await page.goto(url, { waitUntil: 'load' });
const info = await page.evaluate(() => {
  const sc = document.querySelector('.agora-table__scroll');
  return { tabindex: sc.getAttribute('tabindex'), scrollWidth: sc.scrollWidth, clientWidth: sc.clientWidth, behaviour: getComputedStyle(sc).scrollBehavior };
});
console.log('wrapper:', JSON.stringify(info));
await page.evaluate(() => document.querySelector('.agora-table__scroll').focus());
for (let i = 0; i < 3; i++) await page.keyboard.press('ArrowRight');
const start = Date.now();
for (let i = 0; i < 8; i++) {
  const left = await page.evaluate(() => document.querySelector('.agora-table__scroll').scrollLeft);
  console.log(`+${String(Date.now() - start).padStart(4)} ms  scrollLeft ${left}`);
  await page.waitForTimeout(40);
}
await browser.close();
````

### `probe-analyze.mjs`

21 lines · sha256 `27378b623b30f72fa41516ca2284491f9f2d9e2f606b5ba738dc020d85fe6113`

<!-- file: probe-analyze.mjs -->
````js
// Probe: does Enter on the dashboard's "Analyze Impact" link navigate, given
// more than the harness's 8 s? And how long does /analyze take to load?
import { chromium } from 'playwright-core';
const login = process.argv[2];
const base = 'https://agora-kbd.ddev.site/admin/config/development/config-guardian';
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1280, height: 800 } });
const page = await ctx.newPage();
await page.goto(login, { waitUntil: 'load' });
let t = Date.now();
await page.goto(base + '/analyze', { waitUntil: 'load', timeout: 120000 });
console.log(`direct load of /analyze: ${Date.now() - t} ms`);
await page.goto(base, { waitUntil: 'load' });
const link = await page.evaluate(() => { const a = Array.from(document.querySelectorAll('a')).find((x) => /Analyze Impact/.test(x.innerText)); if (!a) return null; a.focus(); return { href: a.getAttribute('href'), target: a.getAttribute('target'), onclick: !!a.onclick, focused: document.activeElement === a, cls: a.className }; });
console.log('link:', JSON.stringify(link));
t = Date.now();
const nav = page.waitForNavigation({ timeout: 90000 }).then(() => true).catch(() => false);
await page.keyboard.press('Enter');
const ok = await nav;
console.log(`Enter -> navigated: ${ok} after ${Date.now() - t} ms; now at ${page.url()}`);
await browser.close();
````

### `guard-check.php`

25 lines · sha256 `ab065bec38308b643d487633019e66298617b309d014b041b2057d7ac4934062`

<!-- file: guard-check.php -->
````php
<?php

/**
 * @file
 * Reads the usage-reporting guard's state on the rig, as JSON (I-117).
 *
 * Run: ddev drush php:script /var/www/html/guard-check.php
 */

$db = \Drupal::database();
$attempts = $db->query("SELECT COUNT(*) FROM {watchdog} WHERE message LIKE :a OR variables LIKE :a OR message LIKE :b OR variables LIKE :b", [
  ':a' => '%release-history%',
  ':b' => '%updates.drupal.org%',
])->fetchField();
$pinned = $db->query("SELECT COUNT(*) FROM {watchdog} WHERE message LIKE :p OR variables LIKE :p", [':p' => '%127.0.0.1:1%'])->fetchField();
print json_encode([
  'fetch_url' => \Drupal::config('update.settings')->get('fetch.url'),
  'automated_cron_interval' => \Drupal::config('automated_cron.settings')->get('interval'),
  'update_available_releases' => count(\Drupal::keyValueExpirable('update_available_releases')->getAll()),
  'system_cron_last' => \Drupal::state()->get('system.cron_last'),
  'update_last_check' => \Drupal::state()->get('update.last_check'),
  'watchdog_rows_naming_release_history' => (int) $attempts,
  'watchdog_rows_naming_the_pinned_address' => (int) $pinned,
  'watchdog_rows_total' => (int) $db->query('SELECT COUNT(*) FROM {watchdog}')->fetchField(),
], JSON_UNESCAPED_SLASHES) . "\n";
````

