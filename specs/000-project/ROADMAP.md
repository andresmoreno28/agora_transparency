# Ágora · Full development roadmap (units 001 → 007)

> **Status: DIRECTION, not signed scope.** This document develops §6 of `plan.md` so that an
> end-to-end vision exists. The real scope of each unit is fixed in **its own** scaffolding turn
> (dated research → `plan.md` → `tasks.md`), and there it may diverge from this one: disk wins, as
> does the state of the art at that moment (I-001).
>
> Written [ejecutor] 2026-08-20, after the research `specs/001-foundation/research/2026-08-20-estado-del-arte.md`.

## Dependency map

```
001 foundation ──► 002 base + theme ──┬─► 003 demo content ─────┐
                                      │                         ├─► 006 hardening ──► 007 publication
                                      ├─► 004 publishing + foi ─┤
                                      └─► 005 ai + governance ──┘
```

`003`, `004` and `005` depend on `002` but **not on each other**: they can overlap if the files are
disjoint. `006` requires the three of them closed. `007` belongs to the human.

## Roadmap conventions

- **Gate A** = automatable green, with real counts (see the `gate-a-verde` skill).
- **Gate B** = Andrés's signature.
- Every unit starts with a reconciliation pass and closes with a report + HOLD.
- Every contrib module that appears needs its line in `DECISIONS.md` **in the same change**.

---

## 001 · Foundation — skeleton and green CI while empty

**Goal:** that a repository exists which IS a valid site template, installable from clean and with the
drupalcode pipeline green, still without its own identity or content.

**Blocked by:** D-007 (machine name), D-008 (theme approach), **D-011 (recipe architecture)**
and the re-verification of the marketplace requirements. See `specs/001-foundation/plan.md`.

**Development points**
1. Copy the starter kit's `1.x` branch and rename the package to `drupal/<machine_name>`.
2. Clean up the scaffolding: `_comment` in `composer.json`, `GET-STARTED.md`, the kit's `screenshot.webp`.
3. Final `.gitignore` / `.gitattributes` from the `.example` files.
4. Own `recipe.yml`: `name`, `description`, `type: Site`, inherited base recipes.
5. Reproducible and documented DDEV environment (≥1.25.0).
6. `.gitlab-ci.yml` with the correct variables; first green pipeline while empty.
7. Invariant scripts in `tests/bin/`: `no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check`.
8. Real install smoke: apply it on a clean Drupal CMS and check that it appears in the selector.

**Gate A** · `composer validate` · drupalcode pipeline green · `InstallTest`/`ValidationTest`/
`RequirementsTest` green with counts · the 4 invariants exit 0 with the number of files scanned.

**Gate B** · Andrés confirms that the skeleton installs from clean and that the machine name is the final one.

---

## 002 · Base + theme — content model and Canvas

**Goal:** the transparency data model and a sober, accessible Canvas-compatible theme.

**Development points**
1. **Content types**: Document, Position/Person, Contract, Budget line, Public call.
2. **Taxonomies and facets**: document type, year, area/department, status.
3. **Roles and permissions**: editor, reviewer, publisher, administrator. Minimum permissions per role.
4. **Base views**: document library with facets, listings by type, search box.
5. **Canvas theme**: color tokens with verified AA contrast, OFL typography, type scale,
   spacing, visible focus, `prefers-reduced-motion`.
6. **Canvas components**: which components are enabled and which are hidden (`disable: []`).
7. **Twig templates** for the content types, with correct semantics and accessible tables.
8. Decide and apply the theme approach (D-008: generated vs versioned).

**Gate A** · phpcs/phpstan/eslint/stylelint green · axe with no violations on the templates ·
contrast of every token verified · PHPUnit kernel tests of the content model.

**Gate B** · Andrés validates the institutional aesthetics and the content model.

**Main risk** 🟡 · Canvas is young technology: re-verify what a "Canvas-compatible" theme requires
today before writing the theme.

---

## 003 · Demo content — bilingual ES/EN

**Goal:** a portal that, once installed, already tells a coherent and complete story.

**Development points**
1. **Home page**: a "what do you want to know?" search box + key indicators.
2. **Institution**: org chart, offices, remuneration in accessible tables.
3. **Document library** with populated facets.
4. **Budgets and contracts**: lightweight visualization **+ accessible table as the source of truth**
   (avoid heavy chart modules).
5. **Open data** downloadable (CSV/JSON) with its own record page.
6. **Accessibility statement** pre-built, with a complaints channel.
7. ES/EN translations of all of the above; correct `lang` per fragment.
8. Demo media with documented licenses (CC0/own) in the manifest.

**Gate A** · axe with no violations on **all** the demo pages · Playwright functional + visual ·
the `no-secrets` invariant over `content/` · verification that there is no real personal data.

**Gate B** · Andrés reviews the institutional tone and the plausible truthfulness of the fictional content.

**Risk** 🟡 · Image and font rights: nothing enters without a nameable license.

---

## 004 · Publishing + FOI — workflows with ECA

**Goal:** the portal stops being static: it has an editorial workflow and a citizen request cycle.

**Development points**
1. **Editorial workflow** (draft → review → published) with content moderation and traceability.
2. **Publishing ECA**: notifications, transitions, a record of who and when.
3. **Citizen freedom-of-information request Webform**, accessible and with clear validation.
4. **FOI cycle with ECA**: automatic acknowledgment of receipt, deadline computation, states, reminders,
   response and closure.
5. **Tracking panel** for requests, for the manager.
6. Transactional emails (on top of `easy_email_express`) accessible and without sensitive data.
7. Permissions and visibility: what the citizen sees, what the manager sees.

**Gate A** · functional PHPUnit of the ECA cycles (states and deadlines) · Playwright of the complete
request flow · axe over the form and its errors · `no-secrets`.

**Gate B** · Andrés walks the FOI cycle end to end.

**Risk** 🟡 · Legal deadlines vary by jurisdiction: configurable, never hardcoded.

---

## 005 · AI + governance — assistant with citations and auditing

**Goal:** the two differentiating features. Both must degrade gracefully.

**Development points**
1. **Recipe `agora_ai`** on top of the Drupal CMS AI recipe, **provider-agnostic**.
2. **RAG over the document corpus**: it indexes **only published documents**.
3. **Mandatory citations**: every answer links to its sources; outside its sources it answers "I don't know".
4. **Graceful degradation without an API key** — the installation CI runs without keys (I-003). If the
   key is missing: the feature is hidden or reports it, and **the installation does not break**.
5. **Visible disclaimer** that the answer is generated by an AI.
6. **Config Guardian preconfigured**: scheduled snapshots + admin panel.
7. The "the portal audits itself" narrative documented for the end user.
8. Key configuration **via environment variable / post-installation UI**, never in config.

**Gate A** · install smoke **without an API key** green (blocking) · PHPUnit of the AI recipe ·
reinforced `no-secrets` invariant · axe over the assistant's UI.

**Gate B** · Andrés validates that without a key the site installs and behaves well, and that with a key the
citations are correct.

**Risk** 🔴 · It is the unit with the largest secret-leak surface and the one that most easily breaks
installability. Treat the keyless smoke as the main test, not as an extra.

---

## 006 · Hardening — full audit before publishing

**Goal:** pass the marketplace review **on the first attempt**.

**Development points**
1. **Full a11y audit**: axe + keyboard walkthrough of every flow + WCAG 2.2 criteria
   (focus not obscured, target size, consistent help, redundant entry).
2. **WCAG attestation** written and signed.
3. **Final SBOM**: every component with a stable version, security coverage status and its line
   in `DECISIONS.md`.
4. **Complete license manifest**: GPL code, OFL fonts, CC0/own media.
5. **Binding smoke**: end-to-end clean installation, without keys, verifying routes and rendering.
6. **Visual regression** stabilized across all the demo pages.
7. **Performance**: review of page weight and queries; no unnecessary heavy modules.
8. **Public documentation in English**: project README, installation, post-install configuration,
   what each recipe does.
9. **Security response commitment**: documented SLA.
10. Final sweep of invariants: `no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check`.

**Gate A** · All of the above green with counts + the `orquestador`'s verdict with no open 🔴.

**Gate B** · Andrés signs that the template is ready to be submitted for review.

---

## 007 · Publication — the human's hands

**Goal:** that Ágora exists publicly. **This unit is not executed by the AI.**

**Development points**
1. Create the project on Drupal.org with the signed machine name (D-007).
2. Push the repository to `git.drupalcode.org`; set up the GitHub mirror if applicable.
3. Verify the pipeline on the real project.
4. Publish `screenshot.webp`, the project description and documentation.
5. Configure `recommended.yml` with its GitLab API permalink.
6. Tugboat for the live demo; link it from `recipe.yml` (`drupal_cms_installer.links`).
7. First **stable release**.
8. Decide the publication route (see ⚠️ below) and, if applicable, the marketplace application.

⚠️ **Unresolved blocker:** the 2026-08-20 research picked up signals (unverified) that the
marketplace started as a **pilot limited to Drupal Certified Partners**, with a **fee of $395 per
listing + $250 annually**. If confirmed, the marketplace route may not be open and the real path
would be **Community** (a general project, publishable without review). It must be verified before 006.

---

## Cross-cutting risks

| Risk | Sev | Mitigation |
|---|---|---|
| Marketplace eligibility and cost | 🔴 | Verify on drupal.org before 006; Community route as plan B |
| Recipe architecture undecided (D-011) | 🔴 | Blocks 001; Andrés's decision |
| Canvas / site templates are young technology | 🟡 | Dated research at the start of each unit (I-001) |
| AI breaking the installation without a key | 🟡 | Keyless smoke as the main test of 005 |
| Media and font rights | 🟡 | Nothing enters without a nameable license |
| Modules without security coverage | 🟡 | Entry gate of the `sbom-y-licencias` skill |
