# Ágora · Master plan (unit 000)

> ALWAYS read this when resuming. This document encodes what was decided in the concept phase [andres, concept phase].
> Nothing here is contradicted without a new decision in DECISIONS.md.

## 1 · Positioning

"Accountability by default: a WCAG-AA transparency portal, AI-assisted, that keeps an audit record of
itself." Audience: small local councils, public bodies, foundations and accountable entities. Dual
strategic purpose: (a) entry into the Drupal.org Site Template Marketplace as the flagship free
template; (b) a showcase for the author's professional thesis (security + accessibility + AI
governance in the public sector) and for Config Guardian.

## 2 · Architecture: a single recipe, with seams for extraction

> **Amended by D-011 and D-014, [andres] 2026-08-21.** The original version described `agora_base`,
> `agora_publishing`, `agora_foi`, `agora_ai`, `agora_governance` and `agora_theme` as sub-recipes in
> subdirectories. **The repository IS the recipe**: a single `recipe.yml` at the root with `type: Site`.
> The first five names stop being installable artifacts and become **functional areas**: the unit of
> internal organization of `recipe.yml` and of `config/`. `agora_theme` leaves this repository and
> becomes **its own project on Drupal.org** (D-014).

Ágora v1 is **a single `recipe.yml` at the root** that composes Drupal CMS recipes and contrib modules
declared in `require`. The functional areas are the seam along which, once the paid template exists,
**independent contrib recipes** will be extracted (pattern B of D-011).
**In v1 the seam is left in place; the extraction is not implemented.**

Seam rules (mandatory from day 1, zero cost):
- Each area occupies a contiguous block labeled with a comment in `recipe.yml`.
- Its own identifiers carry an area prefix: `agora_base_*`, `agora_publishing_*`,
  `agora_foi_*`, `agora_ai_*`, `agora_governance_*`.
- If an area references an identifier from another, the dependency is noted in the block.

| Area | What it provides |
|---|---|
| **base** | Content model and taxonomies: Document (facets: type, year, area), Position/Person, Contract, Budget line, Public call. Roles and permissions. |
| **publishing** | Editorial workflows with ECA (draft → review → published, with traceability). |
| **foi** | Citizen freedom-of-information requests: Webform + ECA lifecycle (acknowledgment, deadlines, states, reminders). |
| **ai** | Assistant with citations over the document corpus (RAG), on top of the Drupal CMS AI recipe, provider-agnostic, **optional and degrading gracefully** without an API key. Answers ONLY from published documents; says "I don't know" outside its sources. Hard dependency: `ai ^1.4` and no provider (D-013). |
| **governance** | Config Guardian preconfigured: scheduled snapshots, admin panel. |

**Outside this repository — `drupal/agora_theme` (D-014):** sober institutional aesthetics, AA contrast
tokens, free-licensed typography (OFL) **self-hosted**, own/CC0 images, everything in the license
manifest. It is a **separate project on Drupal.org**, declared in Ágora's `require`: a site template
**cannot contain code of its own** (`RequirementsTest` requires 0 `*.info.yml` files).
Scope: minimal with teeth, Canvas-compatible, no generic CSS frameworks.

## 3 · Demo content pages (bilingual ES/EN)

Home page (a "what do you want to know?" search box + key indicators) · Institution (org chart,
offices, remuneration in accessible tables) · Document library with facets · Budgets and contracts
(lightweight visualization + accessible table as fallback; avoid heavy chart modules) ·
Participation (freedom-of-information request with ECA cycle) · Downloadable open data · AI assistant
(with disclaimer and citations) · **Accessibility statement** pre-built, with a complaints channel.

## 4 · Marketplace requirements that act as hard constraints

CI installability review · SBOM with the security coverage status of every component ·
license manifest (GPL for anything derived from Drupal; proprietary/CC0 possible for content/images) ·
WCAG attestation · security response commitment (a defined SLA; the author is part of the Security
Team process through Config Guardian) · current Drupal CMS + Canvas only · **no unstable releases
and no patches**.

## 5 · Out-of-scope for v1 (explicit)

- The vertical paid template (it uses these same recipes; a separate future unit).
- Real integrations with e-government portals / public procurement platforms (seams, not features).
- Multilingual beyond ES/EN. — Midgard (alpha; narrative in docs only). — Marketplace commerce
  (the DA is building it). — Any module without security coverage.

## 6 · Planned units (001 is planned in its scaffolding turn; the rest is direction, not scope)

000 project (this doc) → 001 foundation (research + starter kit skeleton + green CI while empty) →
002 base+theme (content model + Canvas theme) → 003 demo content → 004 publishing+foi (ECA) →
005 ai+governance → 006 hardening (full a11y audit, binding smoke, SBOM/licenses) →
007 publication (Drupal.org project, release, marketplace application) [the human's hands].
