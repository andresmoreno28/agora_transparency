# Unit 007 · Research — the marketplace, the community route and the launch

**Dated 2026-09-25.** Every read below was taken between **23:00 and 23:40 UTC on 2026-09-24** —
01:00 to 01:40 on 2026-09-25 in Madrid — anonymously, from the sources listed in §6. Nothing here is
a plan and nothing here edits a gate: `plan.md`, `tasks.md` and `open-questions.md` are built on it.

Every claim carries one label, and a reading is never promoted to a measurement:

- **MEASURED** — a command was run or a public artefact was read, and the output is quoted.
- **READ AT SOURCE** — a file or page was read; what it does not prove is stated beside it.
- **NOT MEASURED** — named, with the reason, so nobody plans around it as though it were known.

Page text is extracted with the script in Appendix A of
`specs/006-hardening/research/2026-09-21-wave-1-measurements.md`, unchanged, so every page line
number below reproduces with it.

---

## 0 · The ROADMAP's unit 007, against disk

`specs/000-project/ROADMAP.md:242-259` lists eight points and a blocker. Measured on 2026-09-25:

| # | the ROADMAP says | state | evidence |
|---|---|---|---|
| 1 | create the project on Drupal.org (`:247`) | **done** | Drupal.org node 3618645, `agora_transparency`, a general project, created 2026-08-22T18:17Z; the theme is node 3618791, created 2026-08-24T10:25Z (D7 API) |
| 2 | push to drupalcode; a GitHub mirror if applicable (`:248`) | **done** | drupalcode project 241097 has one branch, `1.x`, at `f697759` — this working copy's `HEAD`; `git ls-remote github 1.x` prints the same commit |
| 3 | verify the pipeline on the real project (`:249`) | **done, and continuous** | pipeline 975696 on `f697759`: 10 jobs, every one `success`, every one `allow_failure: false` |
| 4 | publish the screenshot, description and documentation (`:250`) | **partly** | the page body exists — 2,659 bytes, headed *"Status: in development"* — and the node carries **0** images, **0** screenshots, **0** documentation links and **0** demo links; `screenshot.webp` is in the package |
| 5 | configure `recommended.yml` with its GitLab API permalink (`:251`) | **not done, and premature** | the file recommends nothing; its permalink answers today — §4 |
| 6 | a Tugboat demo, linked from `recipe.yml` through `drupal_cms_installer.links` (`:252`) | **not done; the ground moved** | the key exists but reaches only templates already on disk; the installer's own demo links moved to SimplyTest.me on 2026-09-24 — §3 |
| 7 | the first stable release (`:253`) | **not done** | drupalcode lists **0** tags; the release history answers *"No release history was found"*; `packages.drupal.org` answers **404** |
| 8 | decide the route and, if applicable, apply (`:254`) | **route decided; application not filed** | D-012, signed 2026-08-21: community first, marketplace afterwards, the application *"007-bis, non-blocking"* |
| ⚠️ | a $395 listing fee plus $250 a year, *"must be verified before 006"* (`:256-259`, and the risk row at `:267`) | **false, and stale for five weeks** | D-012 found it false at source on 2026-08-21; re-read in §1.1, the only fee on the application page is a share of revenue on **paid** templates |

⚠️ The section's heading (`:242-244`) gives the whole unit to the human. Non-negotiable rule 10, as
amended by [andres] on 2026-08-27, splits it: commits, pushes, tags and the release notes are
[ejecutor]'s; creating releases and projects on drupal.org is [andres]'s. `plan.md` §1 records the
supersession; the ROADMAP is not edited (rule 8).

---

## 1 · The marketplace application, today

### 1.1 · The application page — re-read, and unchanged where it matters

**MEASURED.** `https://new.drupal.org/site-template/apply` (the `www.drupal.org` spelling redirects
there), 23:05:18 UTC, HTTP **200**, **60,549 bytes**, **194** text lines. Every line unit 006 quoted on
2026-09-21 (T-0601) lands on the same text at the same number today; the byte count moved by 421 and
the text did not, so the difference is outside the extracted text.

Under *"What we review"* (line 148), introduced by *"Before a template is published to the
marketplace, you'll complete a short review covering:"* (line 149):

| # | criterion (verbatim) | gloss (verbatim) | lines |
|---|---|---|---|
| 1 | Installability | — automated CI confirms the template installs and its components render. | 150-151 |
| 2 | A Software Bill of Materials (SBOM) | — the recipes and contributed projects you include, with their security-coverage status. | 152-153 |
| 3 | A license manifest | — Drupal-derived components remain GPL; any non-GPL components (such as default content or images) are listed. | 154-155 |
| 4 | A WCAG accessibility attestation | — accessible colour contrast, keyboard navigation, and ARIA patterns. | 156-157 |
| 5 | A security-update commitment | — a defined timeline for responding to security issues, with no pinned or patched dependencies. | 158-159 |

Four of the terms an applicant agrees to bind this project, verbatim:

- line 162 — *"All Drupal-derived components remain subject to the GPL, but components like default
  content and images may carry a proprietary licence."*
- line 164 — *"You will work with the Drupal CMS leadership team with the same responsiveness and
  attention to detail you would give a client."*
- line 165 — *"You understand that templates must work within the current versions of Drupal CMS and
  Drupal Canvas, and cannot include non-stable releases (dev, alpha, beta, rc) or patches."*
- line 168 — *"You understand that your publication date is not guaranteed and depends, among other
  factors, on the backlog of template reviews in the queue."*

**Who can apply** (lines 130-147): *"Any organizations who want to submit free templates, are welcome
to."* (133) and *"Any individual who wants to submit free templates, is welcome to."* (142). Partner
status or membership is asked only of **paid** templates (134-139, 143-147).

**Cost** (line 167): *"up to 30% of net retail revenue is assessed as a fee by the Drupal
Association"* — a share of revenue, so **zero for a free template**. D-012's finding holds.

**Timeline:** none is published. Line 168 is the whole of it.

### 1.2 · "Submit an application" opens a form written for sellers

**MEASURED.** Line 170's link, <!-- cspell:disable -->`https://forms.gle/wFNkQW6eUdUMzsR77`<!-- cspell:enable -->,
resolves to a Google Form (23:05:49 UTC, HTTP **200**, 123,354 bytes) titled *"Site Template Makers -
Application"*. Its description, verbatim:

> *"In accordance with the Site Template Guide, Section 1, this form is for Site Template Makers to
> apply to have templates available for sale in the Drupal.org Marketplace. Organizations should only
> submit one proposal, outlining the details of their commercial expectations and any information
> about the template(s) they plan to create. This is the initial step toward becoming a template
> seller. Each template is reviewed individually for inclusion."*

**12 questions, all 12 required.** Two ask for what a free template does not have: *"Provide a
summary of your plan for creating paid site templates, including what verticals you intend to build
for, how many templates you plan to create and on what timeline."* and *"Provide a brief summary (2–4
sentences) of your proposed pricing model."* The partner-status question offers four answers — *Yes*
· *No* · *No, but we'd be willing to become one* · *No, I am an individual or small co-op and we are
all Ripple Makers* — and none of them is an individual offering a free template. **0 of the 12
questions mention a free template.**

Two of its five acknowledgements describe the review better than the application page does:

- *"the quality gates for final acceptance will be strict and varied - ranging from technical
  considerations, accessibility, performance, responsive design, default content and image quality,
  etc"* — three axes the five criteria do not name, with no published threshold for any of them;
- *"you will submit your complete template, including access to all the repositories needed for
  review, all marketing materials, and draft content and images for your listing"*.

⚠️ **The page welcomes free templates from anyone, and the only channel it links is a form for
sellers.** Free templates do reach the marketplace — 15 of the 16 entries in the installer's curated
list carry no price (§3.2) — but how a free one applies is written nowhere found today. That is
D-073's subject.

### 1.3 · The Creator Guide exists — a pilot document, linked from the other two pages

Unit 006 (T-0601) reported that the guide could not be found: the application page's own link (line
172) goes to the generic how-to-create-a-project page. **The other two pages link a document.**
`/site-template/share` (*"Read the creator guide"*) and `/site-template/become-a-creator` (*"Read the
Site Template Creator Guide"*) both point at a Google Doc,
<!-- cspell:disable -->`https://docs.google.com/document/d/1XbrzsEV4T4hxZddW66KCe9X7ggmksE7pKIcq33Ev9jY`<!-- cspell:enable -->.

**READ AT SOURCE.** Its plain-text export (23:06:16 UTC, HTTP **200**, 17,683 bytes, 354 lines) is
titled *"Site Template Creator Guide - Pilot Program"*. What it adds, by export line:

- **Government is an "advanced vertical"** (72-74): *"A full-scale government portal requires a scope
  of features and functionality that is likely beyond the capabilities of an off-the-shelf template
  for example. However, there may be more narrow use-cases within these verticals that are still good
  candidates, and we are open to these proposals."* — then *"[Government] - local gov, single agency
  use case"*.
- **Approval before building** (87-89): *"Once the use case and high-level feature list is defined,
  request approval from the DA/CMS team to continue."* No such request is recorded in this repository.
- **The self-assessment** (203-218) is the five criteria again: an SBOM with coverage status, a
  licence manifest, *"WCAG Compliance Attestation: Declaring accessible color contrast, keyboard
  navigation, aria patterns, etc."*, and *"Security Update Commitment: Defined timeline for responding
  to security issues. No pinning of dependencies"*.
- **Submission** (223-237) adds a support link, warranty or terms, and optional services; **the
  listing** (241-250) carries *"Manual attestation toggles"* and links to support and terms, and
  *"Once approved, the DA publishes the listing."*
- **One content type should showcase Canvas** (Q&A, 306-307): *"we would like at least one content
  type (such as a marketing/landing page) that is part of the template to be Canvas compatible to
  showcase what it can do."*
- **It names no language requirement**: **0** matches for `language`, `multilingual`, <!-- cspell:disable -->`translat`<!-- cspell:enable -->,
  `english`, `locale` or `i18n` in its 354 lines. D-035 rested on *"not on a written rule"* partly
  because this guide was unreachable; it is reachable, and the finding stands.
- **Its timeline is history** (254-257): *"Earliest: DrupalCon Chicago (at risk)"*, *"Latest: July
  2026"*. The document was not rewritten after the pilot: its dates are past, and its checklists are
  still the most detailed statement of the criteria found anywhere.

### 1.4 · Two routes, in the Drupal Association's words — and two pages that disagree

**MEASURED.** `https://new.drupal.org/site-template/become-a-creator` — reached from *"Become a
creator"* on the listing (23:08:04 UTC, HTTP **200**, 61,307 bytes, 169 lines):

- 125 — *"Creators can start by sharing a free template with the community—anyone can create a site
  template this way, no application required. If you'd like your template to be available for sale in
  the marketplace, you can then submit an application for review by the Drupal CMS team."*
- 130-132 — *"No application, no fee, and no partner status required."* · *"If you have a Drupal.org
  account, you can publish today."* · *"Your template may be chosen to appear in the Site Templates
  listings."*
- 136 — the marketplace is *"For creators who want to sell their template via the Marketplace."*
- 143-144 — *"Apply to the marketplace if you want your template in the Marketplace and the Drupal CMS
  installer, you want the option to charge for it, and you're ready to meet the added quality,
  security, and support requirements that come with being promoted to every Drupal CMS user."*

**MEASURED.** `https://new.drupal.org/site-template/share` (23:05:18 UTC, HTTP **200**, 59,336 bytes,
180 lines — the same length as on 2026-09-21): line 126, *"There's no application to fill out, no fee,
and no review queue to wait in"*; line 141, *"create a release so people can download it"*; line 145,
*"That's it. Your template is now listed and installable."*; lines 153-154, *"Make sure your template
installs cleanly on a current version of Drupal CMS."* and *"We recommend depending only on stable
releases of modules and themes"*.

⚠️ **Share line 145 says a published template "is now listed"; become-a-creator line 132 says it "may
be chosen to appear".** Measured which is true: `https://www.drupal.org/browse/site-templates`
redirects to `new.drupal.org/browse/site-templates` (23:06:49 UTC, HTTP **200**); its two pages show
**13 distinct templates**, **every one of them an entry in the installer's curated list** (§3.2), and
the word `agora` **0** times. **On the community route a template becomes installable, and findable
from its own project page. It does not become listed, and it does not reach the installer, by being
published.**

### 1.5 · What a marketplace listing carries — measured on a free government template

**MEASURED.** `https://new.drupal.org/site-template/local` (23:06:49 UTC, HTTP **200**) — a free
council template under the *Government* label — shows: a price of `$0.00`; a *Download* link to a
generic page that sends the user to the Drupal CMS desktop app and its installer; labels; a
description; **Accessibility** *"WCAG 2.2 Level AA"*; a **Privacy attestation**; a **Security
attestation** (*"Stable releases of the site template project and theme project are covered by the
security advisory policy."*); dependencies; a **Support link** and a **Support attestation**; the
creating organisation and users.

The listing index filters on **Accessibility** with exactly four values: *Untested* · *WCAG 2.2 Level
A* · *WCAG 2.2 Level AA* · *WCAG 2.2 Level AAA*. ⚠️ **None of them says what Ágora ships today** — a
statement that targets AA and claims no conformance — so the value is a public accessibility claim,
chosen at application time, and it can be no stronger than the attestation behind it (T-0617).

### 1.6 · Each requirement against the package, by measurement

| requirement (source) | Ágora on 2026-09-25 | evidence | state |
|---|---|---|---|
| installability (apply 150-151) | a clean install of the package inside Drupal CMS runs on every push | pipeline **975696** (`f697759`): the `Drupal CMS` job, **12429265**, prints `OK (1 test, 1 assertion)`; `phpunit`, **12429272**, prints `OK (22 tests, 2643 assertions)` | **met**, on Drupal CMS **2.1.4** — next row |
| the current Drupal CMS and Canvas (apply 165) | Canvas **1.11.0** locked, its newest release (2026-09-08); Drupal CMS **2.1.4** built | job 12429265 prints `Create Drupal CMS project using _DRUPAL_CMS_TAG 2.1.4`, taken from `CMS_STABLE` when the project sets none; `updates.drupal.org/release-history/cms/current` lists **2.1.6**, released **2026-09-23** | **Canvas met · Drupal CMS NOT MEASURED on its current release** |
| an SBOM with coverage (apply 152-153) | 11 direct dependencies, each with a decision line | `bash tests/bin/sbom-check`, 23:15:35 UTC: **11 queried · 11 with coverage · 11 with a D-NNN line · findings 0**, exit 0 | **met** |
| a licence manifest (apply 154-155) | `LICENCE-MANIFEST.md`, 12,780 bytes, at the packaged root | `git archive --worktree-attributes HEAD \| tar -t`: **375** entries, **12** at the root, this file among them | **met** |
| a WCAG attestation (apply 156-157) | **absent** | not among the 12 packaged root entries; T-0617 is `⏸`, blocked by T-0616 — a person's keyboard walkthrough | **not met — unit 006** |
| a security-update commitment (apply 158-159) | **absent** | no such file among the 12; T-0618 is `⏸ 👤`, its shape ruled by D-064's amendment of 2026-09-25 | **not met — unit 006** |
| no pinned or patched dependencies (apply 159, 165) | none | `bash tests/bin/no-patches`: **7** patch vectors checked, **0** findings; `RequirementsTest` asserts no pin, no patch and no patching plugin | **met** |
| no non-stable releases (apply 165) | none | `bash tests/bin/no-unstable-deps`: **11** require entries, **0** findings | **met** |
| one content type showcasing Canvas (guide 306-307) | the home page is a Canvas page | `content/canvas_page/`; `README.md:26` | **met — READ AT SOURCE** |
| the installer's thumbnail — *"a 500x400 thumbnail (screenshot.webp)"*, in the Drupal CMS team's own issue adding a template to the installer; WebP, named `screenshot.webp`, in the starter kit's `GET-STARTED.md:41` | `screenshot.webp` | read from its RIFF header: **500 × 400**, lossy VP8, **20,826** bytes | **met** |
| `recommended.yml` lists stable projects only (the starter kit's own header) | it lists nothing | §4: two of its lines are half false | **met, trivially** |
| listing material: description, labels, the accessibility value, privacy, security and support attestations, a support link (form acknowledgement 5; §1.5) | not prepared | — | **007-bis** |
| approval of the use case before building (guide 87-89) | never requested | — | **D-073** |
| the form's seller questions: a paid plan, a pricing model (§1.2) | there is no free-template path | — | **D-073** |

⚠️ **Dated note, 2026-09-25 — the licence-manifest, WCAG-attestation and security-commitment rows
above were read at `f697759`.** At `6dc1d06` (00:09 UTC 2026-09-25): 376 entries, 13 at the root;
`SECURITY.md` present — T-0618 ✓, pipeline 975799 10/10.

---

## 2 · What a stable release of a site template requires

### 2.1 · Naming and mechanics

**READ AT SOURCE.** *Release naming conventions* (updated 11 January 2026): release branches are
`{major}.x` or `{major}.{minor}.x`; *"tagged releases must have all 3 components"*; *"A tag for a
stable release will consists solely of numbers and full stops, i.e. 1.1.0. Only stable releases will
be prominently displayed on the module page."*; *"the convention is to start from 1, for example
1.0.0"*; a release candidate is *suggested* to stay out *"at least a month with status set to 'needs
review'"*; packaging happens *"Usually within 10 minutes, up to an hour delay can happen if the
packaging queue is busy."*

*Creating a project release* (updated 3 April 2025): an official release is two steps — a git tag,
then the *Add new release* form, where the maintainer chooses the release's terms (*Features*, *Bug
fixes*) and writes its notes — and a separate *Supported* checkbox under *Administer releases*. And
the two sentences the launch is shaped around: *"Once you make a release, the only way to fix a
problem is to make a new release that includes the fix."* and *"Once you release something, it's out
in public and you can not take it back."*

**Ágora fits the naming today:** one branch, `1.x`; no `version` key in `composer.json`; `type:
drupal-recipe`, which `RequirementsTest` asserts.

### 2.2 · Why the first stable release is what makes the template installable at all

- **MEASURED.** `drupal/cms` **2.1.6**'s own `composer.json` sets `"minimum-stability": "stable"` and
  `"prefer-stable": true`. On a stock Drupal CMS project, `composer require drupal/agora_transparency`
  can resolve **only** a stable release unless the user types a stability flag.
- **MEASURED.** `packages.drupal.org/files/packages/8/p2/drupal/agora_transparency.json` answers
  **404**, and so does `…~dev.json`; the release history answers *"No release history was found for
  the requested project (agora_transparency)."* Unit 002's T-505 finding holds a month later: **a
  pushed branch is not a package; the release node is.**
- **READ AT SOURCE.** Both automated paths into an install or a demo run `composer require` with no
  version and no flag: the installer, for a curated template not yet on disk
  (`drupal_cms_installer.profile` at 2.1.6: `require <package> --minimal-changes
  --update-with-all-dependencies`), and SimplyTest.me's launcher (§3.3).

**So a pre-release is not a soft launch here.** It is a release nobody can install by default and
that no advisory covers (§2.4) — which is the measured reason the launch is the first **stable**
release, rather than a matter of taste.

### 2.3 · The theme is stable and covered — and six commits ahead of its newest release

**MEASURED.** `updates.drupal.org/release-history/agora_theme/current`: **10** published releases,
`1.0.0` to `1.2.1`, **every one** `<security covered="1">`, the newest **1.2.1** on 2026-09-24,
`<supported_branches>1.2.</supported_branches>`. `packages.drupal.org`'s `agora_theme.json` lists the
same ten. drupalcode carries **13** tags: `1.0.4`, `1.0.8` and **`1.2.2`** are tags with no release.

**MEASURED** in the theme's working copy: `1.x` is at `ec2c990`, **6 commits past `1.2.1`** and **2
past `1.2.2`**; its pipeline **975455** on that commit lists 10 jobs, every one `success`, none
permissive.

⚠️ **Dated note, 2026-09-25:** `7aeb482` landed at 00:24 UTC, tests only: 7 commits past 1.2.1 and 3
past 1.2.2; pipeline 975809 10/10.

So the template **can** be stable today: D-014(d)'s gate — the theme's stable release must exist
first — was discharged by `1.0.0` on 2026-08-25, and the dependency is covered. Two things remain
for the launch to answer. **The template's pipeline has never run against those six commits**: it
locks the newest published release, `Locking drupal/agora_theme (1.2.1)` in job 12429265. And its
constraint `^1.1` admits `1.1.0`, whose fitness was last measured on 2026-09-23, against a template
that has moved since.

### 2.4 · Security coverage — what the first stable release switches on

**MEASURED.** The template's node reads `field_security_advisory_coverage = covered`, last changed
**2026-09-24T15:14:07Z** — the opt-in D-064's amendment of 2026-09-25 records as deliberate. The
project page (23:09:12 UTC, HTTP **200**) says *"Stable releases for this project are covered by the
security advisory policy."* and *"There are currently no supported stable releases."*; its *"Report a
security vulnerability"* link opens a confidential work item on drupalcode.

**READ AT SOURCE.** The security advisory policy (updated 1 April 2025; `/security-advisory-policy`
redirects to *Security advisory process and permissions policy*): *"Security advisories are only made
for issues affecting stable releases (Y.x-Z.0 or higher / X.Y.0 or higher) in the supported major
version branches. That means no security advisories for development releases (-dev), alphas, betas,
or release candidates."* — and *"Support means both as supported by Drupal core … and by the project
maintainer via their project page."* The same page speaks of *"a Monday before a Wednesday security
release"*.

**READ AT SOURCE.** The starter kit, `GET-STARTED.md:48`: *"Site templates are starting points for new
sites and don't support upgrade paths, so any changes you make will not affect any sites in the wild
that were created from your site template."*

What follows, stated rather than left to be inferred:

1. **Coverage starts with the first stable release and not before**, and only on a branch marked
   supported. A first release on a new branch is supported by default (I-113).
2. **A fix to the template cannot reach a site already built from it.** An advisory about Ágora would
   mean a new release for new sites and written remediation for existing ones; the theme and Config
   Guardian, which are extensions, update normally.
3. **The acknowledgement window D-064 set — 14 calendar days — binds against real users from that
   day.**

### 2.5 · The dependency gates, run today

**MEASURED**, 23:15:35 UTC, in this working copy; each script writes only to a temporary directory.

| invariant | printed | exit |
|---|---|---|
| `sbom-check` | 11 projects queried · 11 with coverage · 11 with a D-NNN line · 3 declare `core_compatibility` · findings 0 | 0 |
| `no-unstable-deps` | 1 file scanned · 11 require entries · 0 require-dev entries · findings 0 | 0 |
| `no-patches` | 1 file scanned · 7 patch vectors checked · findings 0 | 0 |

### 2.6 · What CI runs today

**MEASURED** from the API, and from each job's `/-/jobs/<id>/raw` route, anonymously. Pipeline
**975696**, ref `1.x`, commit `f697759`: **10 jobs**, every one `success`, every one `allow_failure:
false`. The `Drupal CMS` job (12429265) builds `drupal/cms` **2.1.4** and locks `drupal/agora_theme`
**1.2.1**, `drupal/canvas` **1.11.0**, `drupal/core` **11.4.7**, `drupal/drupal_cms_admin_ui` **2.1.6**
and `drupal/config_guardian` **1.0.3**; it prints `OK (1 test, 1 assertion)`. `phpunit` (12429272)
prints `OK (22 tests, 2643 assertions)`.

⚠️ **Dated note, 2026-09-25 — pipeline 975799, ref `1.x`, commit `6dc1d06`**, read the same way: 10
jobs, every one `success`, every one `allow_failure: false`. `phpunit` (12430735) and `phpunit-pgsql`
(12430736) print `OK (22 tests, 2643 assertions)`; `cspell` (12430733) prints
`Files checked: 437, Issues found: 0`; `Drupal CMS` (12430728) prints `_DRUPAL_CMS_TAG 2.1.4`,
`Locking drupal/agora_theme (1.2.1)` and `OK (1 test, 1 assertion)`.

---

## 3 · A live demo

### 3.1 · Drupal.org's Tugboat integration builds merge-request previews, not demos

**READ AT SOURCE.** *Using live previews on Drupal Core and contrib* (updated 12 March 2026; the older
URL redirects to it): *"When a merge request is opened on a core issue, a live deployment preview is
automatically generated…"*; *"For contributed modules, a Tugboat configuration file named
.tugboat/config.yml must be added at the repository's root."*; *"Live previews expire 30 days after
their last update."*; *"Tugboat previews will work with 'general' projects that are not a module or
theme on Drupal.org as well."* The default credentials of any preview are `admin` / `admin`.

**MEASURED.** This repository's `.tugboat/config.yml` is **byte-identical** to the starter kit's `1.x`
copy (`diff` prints nothing) and has not changed since the import commit `e8d1fd3`. It builds Drupal
CMS with `composer create-project "drupal/cms:^2" --stability=dev` and `composer config
minimum-stability dev`, installs with `drush site:install`, and carries **no usage guard**. The project
has had **1** merge request, opened 2026-08-24 and closed.

**NOT MEASURED.** A search-engine summary says a preview also needs Tugboat *"enabled for the project
in its Drupal.org settings"*; the page read does not say so, and it is not repeated here as fact. How
two listed templates' branch previews (`1-x-<id>.tugboatqa.com`) were set up is not on that page
either.

### 3.2 · The installer's own demo links moved on 2026-09-24

**MEASURED.** Drupal CMS keeps its curated list of site templates in `project/drupal_cms_installer`
(drupalcode project 204857), as `site-templates.yml`. Its header calls it *"part of Drupal CMS's API"*
and names its canonical URL,
`https://git.drupalcode.org/api/v4/projects/204857/repository/files/site-templates.yml/raw?ref=HEAD`
(23:02:57 UTC, HTTP **200**, 10,402 bytes). **16 entries**, 1 paid and 15 free. Their *Demo* links:
**13** point at <!-- cspell:disable -->`https://simplytest.me/template/<name>`<!-- cspell:enable -->,
**2** at the creator's own site, **1** has none.

Its history on `2.x`, from the API: on **2026-09-23**, *"Remove preview links"* — the Tugboat preview
URLs of two templates; on **2026-09-24**, *"Provide demo links to all site templates via SimplyTest"*,
and the same day *"Adjust installer links to retain backwards compatibility with Drupal CMS 2.1.x."*,
which put the list back into the `- text: … / url: …` form after a keyed `demo:` / `info:` form.

One more data point, recorded because it matches the pattern unit 006 recorded for a beta dependency
in a published template: <!-- cspell:disable -->`forma`<!-- cspell:enable -->, a curated entry,
publishes its newest release with **no** security coverage in its update feed. The list does not
enforce coverage.

### 3.3 · SimplyTest.me, read at source

**READ AT SOURCE**, in the site's public repository
(<!-- cspell:disable -->`https://github.com/simplytestme/website`<!-- cspell:enable -->, `HEAD`):

- Its site-template importer reads **only** Drupal CMS's curated list, once a day, and skips paid
  entries and entries served from another Composer repository.
- A launch runs `composer require <package>` in a prepared Drupal CMS, then `drush si
  ../recipes/<name>`. Nothing in those commands, nor in its preview-config generator, pins the
  update-status fetch URL: a search for `update`, `fetch`, `usage`, `cron`, `pm:uninstall` and
  `config:set` matched only `composer update`, `git fetch` and comment lines.
- Its generic launcher maps a project's drupal.org type to one of **core, module, theme or
  distribution**; any other type maps to nothing. A site template is a **general project**.

So a SimplyTest.me demo of Ágora exists only once Ágora is in the curated list **and** has a stable
release that `composer require` can resolve. ⚠️ **The theme's project page links
<!-- cspell:disable -->`https://simplytest.me/project/agora_transparency/1.0.0`<!-- cspell:enable -->
today** — a version that does not exist (0 tags), on the generic launcher, which does not handle the
template's project type. **NOT MEASURED by launching it, deliberately**: a launch is an install, and
§3.5 says what an install reports.

### 3.4 · `drupal_cms_installer.links` — a real key, read only for templates already on disk

**READ AT SOURCE.** The installer's `SiteTemplate` class is **byte-identical at the stable tags 2.1.2
and 2.1.6**. `createFromRecipe()` reads `extra.drupal_cms_installer.links` and
`extra.drupal_cms_installer.creator` from a template's `recipe.yml`; each link is a URL or a `text` +
`url` pair, and must be external (asserted). The installer form merges these **locally present**
templates with the curated list: *"If any of them are already in the code base, the ones that are
physically present will 'win'."* The head of the `2.x` branch, which neither tag carries, also accepts
keyed `demo` / `info` links and an `extra.drupal_cms_installer.languages` key. The class is marked
`@internal`: *"Everything in the Drupal CMS installer is internal and may be changed or removed at any
time without warning."*

**READ AT SOURCE.** The starter kit's `recipe.yml` (`1.x`) ships the block commented out, suggesting a
*Demo* link (*"The provided Tugboat configuration can be used to set this up…"*) and a *Learn more*
link. **MEASURED:** Ágora's `recipe.yml` has one `extra:` key, `recipe_installer_kit.finish_url`, and
no `drupal_cms_installer` block.

So the key the ROADMAP names exists, and it reaches exactly one audience: somebody who ran `composer
require drupal/agora_transparency` before opening the installer — which **is** the community route's
install path. Whether the 2.1.x card renders a local template's links is **NOT MEASURED** in a
browser.

### 3.5 · Would a demo report usage to drupal.org? Yes — to the theme's figure, never the template's

- **READ AT SOURCE.** Every Ágora install applies `drupal_cms_admin_ui`, whose **2.1.6** recipe
  installs core `update` (and `project_browser` and `automatic_updates`), and core's
  `core_recommended_maintenance`, whose recipe at **11.4.7** installs `automated_cron`. A site that
  serves requests therefore runs cron and asks `updates.drupal.org` about every enabled extension,
  carrying its site key. T-0635 measured the mechanism on this package's own test sites: **18 requests
  from 9 test sites per run**.
- **MEASURED** (23:16:50 UTC): `agora_transparency`'s usage page reads *"There is no usage information
  available."* — as do the pages of three published, listed site templates, `haven`, `byte` and
  `local`. A recipe is not an extension, and nothing reports it. `agora_theme`'s page reads **273 ·
  188 · 155 · 613** sites for the weeks starting 13 September, 6 September, 30 August and 23 August.
- **So** a demo built by anybody else — SimplyTest.me's launcher, or a merge-request preview built
  from the kit's `.tugboat/` file — counts as an **`agora_theme`** site each time it runs cron, unless
  its build pins the fetch URL. This package's test sites are already pinned (T-0635), with the value
  in `tests/src/Traits/NoUsageReportingTrait.php`. A demo Ágora builds itself can be pinned the same
  way; a demo SimplyTest.me builds cannot be pinned from here.

---

## 4 · `recommended.yml` and its permalink

**READ AT SOURCE.** The starter kit's `recommended.yml` (`1.x`) is where the ROADMAP's phrase comes
from: *"In order for this list to be remotely updatable by you, this file must have a permanent URL
that never changes."* and *"If you are hosting your site template's repository on drupal.org, this
file will have a permalink via GitLab's API"*. Consuming it takes `project_browser` installed and a
config action on `project_browser.admin_settings` that sets `enabled_sources.recommended.uri` to the
permalink and `default_source: recommended`.

**READ AT SOURCE.** Project Browser **2.1.5**'s `Recommended` source fetches the URI, decodes the YAML,
caches it for three days, and — the line that matters — marks every entry covered and maintained:
*"Since this plugin is showing projects that are explicitly recommended by an external curator, we
can assume it is covered by security advisories and actively maintained."* A file with no entries
yields an empty source.

**MEASURED.** The permalink works today:
`https://git.drupalcode.org/api/v4/projects/241097/repository/files/recommended.yml/raw?ref=1.x`
answers HTTP **200** with 1,231 bytes, **byte-identical** to the working copy (`cmp`); `?ref=HEAD`
answers the same.

**Is the current file correct?**

- It is valid YAML made only of comments, so a consumer reads no entries. Lines 1-7 are true: it
  recommends nothing, and says why.
- ⚠️ **Lines 28-30 are half false, in shipped text**: *"Consuming this list also requires installing
  `project_browser` and pointing `project_browser.admin_settings` at a permanent URL for this file, in
  `recipe.yml`. Neither is done yet."* The first is done. `project_browser` is installed on every
  Ágora site by `drupal_cms_admin_ui` — its **2.1.6** recipe lists it under `install:` and its
  `composer.json` requires `^2.1.5`, a stable release with coverage published on 2026-09-23 — and
  `recipe.yml` already acts on its Canvas components (`canvas.component.block.project_browser_block.*`).
  The second is not done, and should not be: wiring an empty list as the default source would greet
  every site owner with an empty tab.
- ⚠️ **The day the file gets its first entry, it needs a guard**: Project Browser will present that
  entry as covered and maintained, whatever its real status is.

---

## 5 · What this research does not establish

- That a clean install works on Drupal CMS **2.1.6** — CI builds 2.1.4.
- That this template works with the theme's `1.x` tip, or still with `agora_theme` **1.1.0**.
- How a **free** template applies to the marketplace: no source found says.
- Whether drupal.org requires a project-level Tugboat switch, and how the listed templates' branch
  previews were built.
- Whether a SimplyTest.me sandbox runs cron within its lifetime: read in source, never launched.
- What the review's *"strict and varied"* gates on performance, responsive design and content quality
  measure: no threshold is published.
- That the installer's 2.1.x card renders a local template's `links` and `creator`.

---

## 6 · Sources

All read anonymously; times are UTC on 2026-09-24.

| # | source | read | result |
|---|---|---|---|
| S1 | `https://new.drupal.org/site-template/apply` | 23:05:18 | 200 · 60,549 B · 194 lines |
| S2 | the application form (§1.2) | 23:05:49 | 200 · 123,354 B · 12 questions |
| S3 | the Creator Guide, plain-text export (§1.3) | 23:06:16 | 200 · 17,683 B · 354 lines |
| S4 | `https://new.drupal.org/site-template/share` | 23:05:18 | 200 · 59,336 B · 180 lines |
| S5 | `https://new.drupal.org/site-template/become-a-creator` | 23:08:04 | 200 · 61,307 B · 169 lines |
| S6 | `https://www.drupal.org/browse/site-templates`, both pages | 23:06:49 | 200 · 13 distinct templates |
| S7 | `https://new.drupal.org/site-template/local`, `/haven` and `/install` | 23:06 – 23:14 | 200 each |
| S8 | `https://www.drupal.org/project/agora_transparency` | 23:09:12 | 200 · 33,395 B |
| S9 | the D7 API, `node.json?field_project_machine_name=`, for both projects and five listed templates | 23:00 – 23:10 | 200 each |
| S10 | `https://www.drupal.org/project/usage/<project>` for both projects, `haven`, `byte` and `local` | 23:16:50 | 200 each |
| S11 | `https://updates.drupal.org/release-history/<project>/current` for `agora_transparency`, `agora_theme`, `cms`, `drupal_cms_installer`, `drupal_cms_admin_ui`, `project_browser`, `canvas` and four listed templates | 23:00 – 23:20 | 200 each; the first with an `<error>` body |
| S12 | `https://packages.drupal.org/files/packages/8/p2/drupal/<name>.json` for both projects, with and without `~dev` | 23:00 | 404 · 404 · 200 · 404 |
| S13 | the drupalcode API: projects 241097, 241203 and 204857, their tags, branches and merge requests, pipelines 975696 and 975455 with their job lists, and the commits touching `site-templates.yml` | 23:00 – 23:14 | 200 each |
| S14 | job traces through `/-/jobs/<id>/raw`: 12429264, 12429265 and 12429272 (template), 12425450 (theme) | 23:13 – 23:36 | 200 · 88,888 / 227,445 / 37,339 / 155,496 B |
| S15 | `site-templates.yml` at its canonical URL (§3.2) | 23:02:57 | 200 · 10,402 B |
| S16 | `drupal_cms_installer`: `src/SiteTemplate.php` at `2.x`, `2.1.2` and `2.1.6`; `src/Form/SiteTemplateForm.php` and `drupal_cms_installer.profile` at `2.1.6` | 23:03 – 23:40 | 200 each |
| S17 | `project_browser`, `src/Plugin/ProjectBrowserSource/Recommended.php` at `2.1.5` | 23:12 | 200 · 4,937 B |
| S18 | `drupal_cms_admin_ui` `recipe.yml` and `composer.json` at `2.1.6`; core's `core_recommended_maintenance/recipe.yml` at `11.4.7`; `cms`'s `composer.json` at `2.1.6` | 23:11 – 23:38 | 200 each |
| S19 | the starter kit, `drupal_cms_site_template_base` at `1.x`: `GET-STARTED.md`, `recommended.yml`, `recipe.yml`, `.tugboat/config.yml` | 23:11:18 | 200 · `GET-STARTED.md` 8,070 B |
| S20 | Drupal.org documentation: release naming conventions (updated 11 January 2026) · creating a project release (3 April 2025) · the security advisory policy (1 April 2025) · live previews (12 March 2026) | 23:09 – 23:13 | 200 each |
| S21 | SimplyTest.me's public source (§3.3) | 23:00 – 23:06 | 200 each |
| S22 | the permalink of `recommended.yml`, `?ref=1.x` and `?ref=HEAD` | 23:12 | 200 · 1,231 B each |
| S23 | in this working copy: `sbom-check`, `no-unstable-deps`, `no-patches`; the RIFF header of `screenshot.webp`; `git archive`; `git ls-remote` | 23:15 | exit 0 each |
