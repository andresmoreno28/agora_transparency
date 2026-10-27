# Ágora Transparency 1.0.0 — WCAG 2.2 Level AA review of the public pages

- **Evaluation date:** 2026-10-27.
- **Method:** WCAG-EM 1.0, steps 1 to 5.
- **Target:** WCAG 2.2 Level AA, 55 success criteria. 4.1.1 Parsing is obsolete and was removed in WCAG 2.2, so it is not counted.
- **Tools:** Playwright 1.64.0, Chrome 156.0.8078.49, Firefox 157.0, axe-core 4.14.0 with all 105 rules enabled on every run.
- **Site:** `agora_transparency` 1.0.0 (installed from the tag via `git archive`), `drupal/cms` 2.2.5, core 11.4.11, `agora_theme` 1.2.3, on the WSL rig `~/agora-launch-shot`.

## 1. Verdict

"WCAG 2.2 Level AA" **cannot be claimed today for all public pages**.

The 30 sampled pages rendered by `agora_theme` show **0 failures**: every content page, register and record type, the document library and its filter states, search results, the area page, the legal pages, the 404 and 403 pages, and the sign-in page.

**All 3 failures are on two account pages rendered by Gin through Gin Login:** `/user/password` (linked from the sign-in page as "Forgotten your password?") and `/user/register` (a 403 page).

| | Count |
|---|---|
| Criteria | 55 (31 at level A, 24 at AA) |
| Pass | 36 |
| Fail | 3 — 1.4.11, 2.4.6, 2.5.8 |
| Not applicable | 16 |
| Advisories | 12 |

A claim scoped explicitly to exclude those account pages and the demo PDFs is supportable today, pending the NVDA pass.

## 2. Usage guard

Before and after the review (`raw/00-guard-check.txt`, `raw/10-usage-guard-capture.txt`): `fetch.url` pinned to `http://127.0.0.1:9/agora-tests-never-report-usage`; `DRUPAL_CMS_TELEMETRY=0`; `update.last_check` unchanged at 1792751044 (2026-10-23); cron never ran; packet capture over the whole review: 0 packets to ports 80/443, 0 DNS, 0 SYN to the pinned port; `/admin/config` never opened.

Changes to the rig: stopped containers started; a stale MySQL socket lock replaced (data volume untouched); loopback host entries re-added; a PHP server and a packet capture started (capture now stopped); one empty and three wrong sign-ins submitted (6 watchdog rows, flood entries). No content or configuration changed.

## 3. Scope and sample

Scope: everything an anonymous visitor can reach. Excluded: the admin interface, pages needing an account, `/user/reset/*` (needs a one-time token).

Population: 94 public HTML URLs found by crawling (`raw/01-crawl.txt`): 1 front page, 7 registers, 56 records, 24 area pages, 6 prose pages, the document library. Random picks: `shuf -n 2 --random-source=<(yes 20261027)` → S31, S32.

| ID | Type | URL |
|---|---|---|
| S01 | Front page | `/` |
| S02-S07 | Registers | `/documents` `/people` `/contracts` `/agreements` `/grants` `/datasets` |
| S08 | Document record (with PDF) | `/documents/annual-accounts-2023` |
| S09 | Person record | `/people/elena-rebollar-quintana` |
| S10 | Contract record | `/contracts/resurfacing-camino-viejo-access-road` |
| S11 | Agreement record | `/agreements/agreement-regional-health-service-municipal-health-centre` |
| S12 | Grant record | `/grants/grant-restoration-fountain-old-quarter` |
| S13 | Dataset record (CSV, data table) | `/datasets/budget-execution-financial-year-2024` |
| S14 | Library, no filter | `/library` |
| S15 | Library, filtered | `/library?search=&area=7&year=10` |
| S16 | Library, no results | `/library?search=zzqxv&area=All&year=All` |
| S17 | Search results | `/publications?search=budget` |
| S18 | All publications | `/publications` |
| S19 | Area page | `/taxonomy/term/7` |
| S20 | The institution | `/institution` |
| S21-S24 | Accessibility statement, privacy notice, legal notice, cookies | `/accessibility-statement` `/privacy-notice` `/legal-notice` `/cookies` |
| S25 | 404 | `/this-page-does-not-exist` |
| S26 | Sign-in and its error states | `/user/login` |
| S27 | Password reset (Gin) | `/user/password` |
| S28 | 403 at `/user/register` (Gin) | `/user/register` |
| S29, S30 | Pagination | `/documents?page=1` `/publications?page=2` |
| S31, S32 | Random | `/documents/report-state-municipal-roads-and-pavements` `/documents/regulation-citizen-participation-council` |

Extra states (`raw/04-flows.json`): sign-in submitted empty (browser and server validation), wrong password; password reset submitted empty; header search empty and with a query; filter changed without submitting, then submitted; the no-results state.

## 4. Method

- **axe:** per page at 1280 and 320 px in Chrome, 1280 px in Firefox, plus the sign-in error state: 97 runs.
- **Keyboard:** real Tab presses from load until focus left the page; per stop: position, `:focus-visible`, outline/box-shadow and ring contrast, coverage by other elements (9 sample points), hidden while focused. Shift+Tab reverse check, skip link, a walk at 320 px, Firefox walk on 9 key pages.
- **Accessibility tree:** Playwright ARIA snapshot plus Chrome's full accessibility tree per page.
- **1.4.10:** 320×256, page overflow, elements past the edge, text lost against 1280 px.
- **1.4.4:** 640×512 CSS px at DPR 2, plus Firefox with a 32 px default font.
- **1.4.12:** the WCAG text-spacing CSS at 1280 and 320 px.
- **1.3.4:** 390×844 against 844×390.
- **Hover:** 472 link and button styles hovered.
- **2.5.8:** geometry of every interactive element with the size, inline and spacing exceptions, cross-checked with axe.
- **Contrast:** pixel sampling of the hero text over the photo at 5 widths; placeholder, visited, hover, focus, icon and chart colours.
- **Forms:** labels, `aria-describedby`, autocomplete, error states, paste, CAPTCHA, select-submits-on-change.
- **Event listeners:** read through Chrome DevTools.
- **PDFs:** 28 parsed for tagging (`raw/05-pdf-check.json`).

## 5. Counts

- **axe WCAG A/AA:** 1 rule, 1 node, 1 page (S28, `target-size`), identical in all 3 configurations; 31 of 32 pages: 0.
- **axe, not A/AA:** `color-contrast-enhanced` (AAA) 102 nodes on 18 pages; `focus-order-semantics` (experimental) 17 nodes on 15 pages; `heading-order` 1 node (S19); sign-in error state: 2 contentinfo-landmark best-practice findings.
- **Keyboard:** 1,551 tab stops at 1280 px and 1,551 at 320 px in Chrome; 463 in Firefox, identical per page. 0 traps, 0 stops without an indicator, 0 hidden while focused, 0 fully obscured. On all 30 Ágora pages, interactive elements plus the skip link equal the tab stops exactly.
- **Skip link:** first stop, visible on focus, moves focus into `<main>` on 30 of 30 pages.
- **Targets:** 1,537 per width. At 1280 px: 1,139 pass by size, 357 by spacing, 40 inline, 1 fails. At 320 px: 1,302 by size, 197 by spacing, 36 inline, 1 fails, 1 false positive (Gin's visually hidden skip link).
- **Reflow:** 0 px overflow on 32 of 32; 17 data tables on 15 pages scroll inside their own region, scrollable with arrow keys.
- **200% zoom and Firefox text-only 200%:** 0 overflow, 0 clipping on 32 of 32.
- **Text spacing at 1280 px:** 0 findings on 32 of 32.
- **Hover:** 0 content appearing, 0 states below AA contrast, of 472.
- **Hero text over the photo:** minimum 8.88:1 (lead text), 12.33:1 (H1).
- **PDFs:** 0 of 28 tagged, 0 with a language, 0 with `DisplayDocTitle`.

## 6. Every criterion

P = pass, **F** = fail, NA = not applicable.

- **1.1.1** A — P. Logos `alt=""` inside links named after the site; icons hidden SVGs beside text or a hidden name; hero photo is a CSS background; the chart is `aria-hidden` with a 24-row captioned table as its alternative; bars repeat values shown as text.
- **1.2.1, 1.2.2, 1.2.3** A, **1.2.4, 1.2.5** AA — NA. No audio, video, iframe or object.
- **1.3.1** A — P. All 17 table types have `<caption>`, `<th scope>` and `headers`; real lists; named landmarks; labelled controls. Advisories A1, A3, A4, A12.
- **1.3.2** A — P. No CSS reordering; logical focus order.
- **1.3.3** A — P. "Above"/"below" refer to reading order.
- **1.3.4** AA — P.
- **1.3.5** AA — P. `autocomplete` `username`/`current-password` on sign-in, `username` on reset.
- **1.4.1** A — P. All 19 links in running text underlined; active menu item has a bottom rule and `aria-current`.
- **1.4.2** A — NA.
- **1.4.3** AA — P. axe `color-contrast` 0 in 97 runs; hero ≥ 8.88:1; placeholder 6.4:1; visited ≥ 7.4:1; hover/focus text at AA or above.
- **1.4.4** AA — P.
- **1.4.5** AA — P. Only brand marks are images of text.
- **1.4.10** AA — P. Advisory A7.
- **1.4.11** AA — **F.** Ágora theme passes (focus ring 3 px `#b34700` 4.48-5.5:1; `#ffb877` on navy 8.42:1; input borders 6.3-7.03:1; sort icon 3.6:1; trend line 3.05:1; eye icon 8.4:1; spend bars 2.7:1 but values printed as text). **Gin halo 2.27:1 (F2).**
- **1.4.12** AA — P. Advisory A6.
- **1.4.13** AA — NA. Menu `title` tooltips are the browser's.
- **2.1.1** A — P.
- **2.1.2** A — P.
- **2.1.4** A — NA. No key listeners.
- **2.2.1, 2.2.2** A — NA. Only Gin's 0.3 s fade.
- **2.3.1** A — P.
- **2.4.1** A — P.
- **2.4.2** A — P. Advisory A8.
- **2.4.3** A — P. Advisory A5.
- **2.4.4** A — P. File links read e.g. "Annual accounts 2023, PDF, 1,789 bytes". Advisory A9.
- **2.4.5** AA — P. Menu, footer, search, breadcrumb, area pages, library.
- **2.4.6** AA — **F.** Ágora pages correct; **S28's H1 wrong (F3).**
- **2.4.7** AA — P. All 3,565 focus stops have a visible indicator.
- **2.4.11** AA — P. No sticky/fixed elements; 0 fully obscured; 7 partly covered (4 inline links touched by neighbours' rings, 3 password fields under their own show-password button).
- **2.5.1** A — NA.
- **2.5.2** A — P.
- **2.5.3** A — P.
- **2.5.4** A — NA.
- **2.5.7** AA — NA.
- **2.5.8** AA — **F.** Ágora pages 0 failures; **Gin show-password 24×16 (F1).**
- **3.1.1** A — P. `lang="en"` on 32 of 32.
- **3.1.2** AA — P. Spanish appears only in proper names.
- **3.2.1** A — P. 0 context changes in 3,565 focus moves.
- **3.2.2** A — P.
- **3.2.3** AA — P. Header 13 links, same order on 30 pages; footer 19 identical links.
- **3.2.4** AA — P.
- **3.2.6** A — NA. No help mechanism shipped; the contact route is to be completed by the operator.
- **3.3.1** A — P. Browser message per field; server `role="alert"` naming the fields; `aria-invalid="true"`.
- **3.3.2** A — P. Advisory A1.
- **3.3.3** AA — P.
- **3.3.4** AA — NA.
- **3.3.7** A — P.
- **3.3.8** AA — P. No CAPTCHA, paste allowed, autocomplete, show-password toggle.
- **4.1.2** A — P. Advisory A2.
- **4.1.3** AA — NA. No AJAX; errors also in `role="alert"`.

## 7. Failures

All three come from Gin. `agora_core` (D-054) removes only `user.login` from the routes Gin Login takes over, so these pages fall back to Gin.

**F1 — 2.5.8, medium.** S28 `/user/register`. `#user-login-form > div.form-type--password.form-item:nth-of-type(2) > button.shwpd.eye-close`: 24×16 px inside the password field, so its 24 px spacing circle hits the field; axe agrees. The same button on the Ágora sign-in page is 44×44. Screenshot `screenshots/failures/F1-target-size-show-password-user-register.png`. Fix: `agora_core` routes `user.register` to the site theme, or upstream in Gin.

**F2 — 1.4.11 focus indicator, medium.** S27, S28: `#edit-submit`, `#edit-login-link`, `#edit-forgot-password-link`, `button.shwpd`, inputs, the "Home" link. Gin draws `0 0 0 4px rgba(0,125,250,.6)` (renders `#66b1fc`), 2.27:1 against white; the 1 px inner line 1.6:1; the button's own focus colour change 1.46:1. Visible, so 2.4.7 passes. Screenshots `screenshots/failures/F2-focus-ring-reset-button-user-password.png`, `F2-focus-ring-login-link-user-password.png`. Fix: `agora_core` routes `user.pass` and `user.register` to the site theme, or upstream in Gin.

**F3 — 2.4.6, low.** S28 `h1.page-title.user-form-page__page-title` reads "Create new account" above a sign-in form; registration is admin-only so the page is a 403 showing the sign-in form (`page.403` = `/user/login`); the page title says "Log in" and nothing says registration is unavailable. Other 403s (`/admin`, `/node/add`, `/user/1`) correctly show the Ágora sign-in page with "Log in" as H1. Screenshot `screenshots/failures/F3-heading-user-register-403.png`. Fix: the same `agora_core` change, or upstream in Gin Login.

## 8. Advisories

| # | What | Where | Fix belongs to |
|---|---|---|---|
| A1 | Search hint `#edit-search--description` not referenced by `aria-describedby` | S01, S14-S18, S30 | Theme or core Views |
| A2 | `div.agora-table__scroll[tabindex=0]` focusable with no role or name; add `role="region"` + `aria-labelledby` to the caption | 15 pages | Theme |
| A3 | Error messages wrapped in `role="contentinfo"` (a second footer landmark) | Sign-in error state | Theme |
| A4 | Pager `<h4>Pagination</h4>` after an h2 | Area pages | Theme |
| A5 | Sign-in autofocuses the username, so forward Tab skips the skip link, header, H1 and "Forgotten your password?" | S26-S28 | Core (theme or `agora_core` can override) |
| A6 | At 320 px with text spacing, hero "Transparency" overflows by 46 px | S01 | Theme |
| A7 | At 320 px, table captions cut until scrolled sideways | Registers | Theme |
| A8 | Filtered and paged states share their register's page title | S14-S18, S29, S30 | Theme / config |
| A9 | "Finance and Budget" links to both `/publications?area=7` and `/taxonomy/term/7` | `/publications` | Template config |
| A10 | 28 of 28 demo PDFs untagged (no StructTreeRoot, MarkInfo, `/Lang`, DisplayDocTitle) | Documents | Template content (`generate-demo-media.py`) |
| A11 | The Gin pages look like a different site | S27, S28 | Same fix as F1-F3 |
| A12 | Record label/value pairs are `div`s, not `dl`; "File" followed by a redundant "Document" label | Records | Theme |

## 9. Fixes needed for an unqualified claim

1. `agora_core`: serve `user.pass` and `user.register` with `agora_theme` (and check `user.reset`, `user.reset.form`); re-run the harness. Fixes F1, F2, F3 (and A11).
2. Make the generator produce tagged PDFs (StructTreeRoot with H1 and P, `MarkInfo Marked true`, `/Lang (en)`, `DisplayDocTitle true`), or exclude the demo PDFs in the claim.
3. Complete the NVDA pass.

Recommended: A1-A4. The shipped accessibility statement says "No claim of full conformance is made here"; changing that is [andres]'s decision.

## 10. Not covered

No screen reader yet; `/user/reset/*`; real mobile devices and their screen readers; forced colours; voice control (inferred only); 62 of 94 URLs (they share templates with sampled pages); PDF reading order and contrast inside PDFs; AAA; the admin interface.

## 11. Files

`raw/`: usage guard (`00`, `10`), crawl (`01`), population (`02`), Chrome run log (`03`), flows (`04`), PDF check (`05`), Firefox (`06`, `06b`), summary (`07`), hero contrast (`08`), Gin and sign-in checks (`09`); per-page `pages/`, `axe/`, `aria/`, `keyboard/`; `files/` (the 33 demo files); `tools/` (every script).
`screenshots/`: `failures`, `advisories`, `pages-1280`, `reflow-320`, `zoom-200`, `spacing-1280`, `spacing-320`, `firefox-text-200`, `focus` (70 crops), `flows`.

<!-- cspell:ignore shuf zzqxv shwpd autofocuses -->
<!-- The report above is copied verbatim from the evaluator (T-0730), with one exception: in F1 the selector's two classes are written in the other order, which is the same CSS selector. As written, `password` followed by a colon and eight characters is what tests/bin/no-secrets level 2 reads as a credential assignment. The four words above are a shell command, a deliberate no-results query, a class name in Gin and a verb as written; they are ignored here, in this file only, rather than declared project vocabulary. -->
