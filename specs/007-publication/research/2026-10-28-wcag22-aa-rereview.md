# Ágora Transparency 1.0.1 candidate — WCAG 2.2 AA re-review, run 1 (2026-10-28)

Rig `~/agora-t0738`: template `ae5fd5a` (rendering-identical to the T-0737 candidate), agora_core 1.0.1, agora_theme 1.2.4, drupal/cms 2.2.7, core 11.4.11, config_guardian 1.1.0, gin_login 2.1.5. Harness: `a11y-review-2/tools` (adapted from the 2026-10-27 review). Raw data: `a11y-review-2/raw`.

Sample: 33 pages (the 32 of 2026-10-27 plus the one-time login page), same 94-page population; 34 PDFs.

- **Criteria (55):** 39 pass, 1 fail (2.5.8), 15 not applicable. The three Gin failures of 2026-10-27 (1.4.11, 2.4.6, 2.5.8 on /user/password and /user/register) are gone. 2.2.1 now applies and passes (one-time link valid 24 h; 20-hour exception).
- **axe A/AA:** `/user/password` 1 `target-size` node (Chrome, 1280 and 320); 32 other pages 0.
- **Keyboard:** 1,645 stops per width; 0 traps, 0 missing indicators.
- **PDFs:** 34/34 tagged (StructTreeRoot, MarkInfo, /Lang en, DisplayDocTitle, H1/P).
- **Usage guard:** telemetry off, update fetch pinned to 127.0.0.1:9, cron never ran, 0 packets from the site.

## Failure

| Page | Selector, size | Owner | Fix |
|---|---|---|---|
| `/user/password` (as shipped) | `#user-pass .more-links a#edit-login-link` "Log in", 45.1×19 | Gin Login `gin_login_form_alter()` adds it under any theme; agora_theme leaves `.more-links` unstyled | theme: `.more-links a` ≥ 24×24 |
| `/user/login`, registration open | `#edit-register-button` "Create new account", 147.6×19 | same | same |
| `/user/register`, registration open | `#edit-login-link` "Already have an account? Log in", 239.5×19 | same | same |

Clean: closed `/user/register` (403 sign-in page; agora_core strips the links) and the one-time login page (0 violations).

Fix dispatched as T-0740 (agora_theme 1.2.5); the template floor moves to `^1.2.5` and run 2 follows.

---

# Ágora Transparency 1.0.1 candidate — WCAG 2.2 AA re-review, run 2 (2026-10-28): PASS

Harness 11:53:07–12:00:45 UTC, open-registration checks and restore until 12:03:28 UTC, on rig `~/agora-t0738`.

**Versions:** template `ae5fd5a` (rendering-identical to the 1.0.1 candidate: T-0737 changes only tests, dependency floors and documentation), agora_theme 1.2.5, agora_core 1.0.1, config_guardian 1.1.0, drupal/cms 2.2.7, core 11.4.11, gin_login 2.1.5.

**Method:** WCAG-EM 1.0, the 2026-10-27 harness adapted (`a11y-review-2/tools`): 33 sampled pages from a 94-page population (the 32 of 2026-10-27 plus the one-time login page), the open-registration variants of `/user/login` and `/user/register`, and all 34 demonstration PDFs. Chromium and Firefox via Playwright; axe-core 4.14.0 with every rule; keyboard walks; reflow at 320 px; 200% zoom; WCAG text spacing; orientation; hover; target-size geometry; contrast sampling; forms and error states.

| | Result |
|---|---|
| Criteria (55; 4.1.1 obsolete) | **40 pass, 0 fail, 15 not applicable** |
| axe A/AA | **0 violations** in 78 scans (33 pages × 2 widths, 4 open-registration scans, 8 extra closed-account checks); only AAA `color-contrast-enhanced` remains |
| `.more-links a` on account forms | 45×44 (`/user/password`), 148×44 (`/user/login`, open registration), 240×44 (`/user/register`, open registration) |
| PDFs | 34/34 tagged (StructTreeRoot, MarkInfo, /Lang en, DisplayDocTitle, H1/P) |
| Keyboard | 1,645 stops per width (Chromium), all visible, 0 traps; Firefox 10 pages, 0 missing indicators, 0 obscured; open-registration pages 130 stops, 0 traps |

Only 2.5.8 changed against run 1 (fixed by agora_theme 1.2.5); every other output identical to run 1.

**Usage guard:** telemetry 0, `update.settings` fetch url pinned to 127.0.0.1:9, cron never ran; capture `guard-ae5fd5a-theme125-20261028-125220.pcap`: 0 packets (0 DNS, 0 to 80/443, 0 to port 9). **Incident, stated:** a quoting error in a Composer step emptied the web container's `/etc/hosts` for about a minute around 11:51 UTC, with no capture running; the site made no request in that window (its last request was at 10:46 UTC) and the file was restored before any page request.

**Registration** restored to administrators only; `/user/register` answers 403 again.

**Screen reader:** [andres]'s informal pass with NVDA and Firefox on the fixed heads (2026-10-28): NVDA read all content wherever he placed the cursor; the scripted guide was not completed. His skip-link report was traced to NVDA's virtual cursor, not to the site (first Tab reaches the skip link 20/20 in Firefox and Chrome without a screen reader).

Run 1 (same day, agora_theme 1.2.4): 1 failure, 2.5.8 on Gin Login's `.more-links` links; raw output in `a11y-review-2/raw-run1`.

<!-- The two reports above are copied verbatim from the evaluator (T-0738): run 1 first, then run 2, separated by a horizontal rule. Paths under a11y-review-2/ name the evaluator's working directory, which is not part of this repository. -->
