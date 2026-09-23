# Ágora Transparency

Ágora Transparency — **Ágora** for short — is a site template for Drupal CMS aimed at **transparency
and open government portals**: small local councils, public bodies, foundations and any organisation
that has to publish what it decides, what it spends and who works for it.

![The Ágora front page as the template installs it: the masthead, the search block, the key figures for the fictional municipality of Fuentelclaro, and the register of published records beneath them.](screenshot.webp)

## Status — in development

Ágora is being built in the open and is **not finished**, but it is no longer only a skeleton: the
recipe composes Drupal CMS, installs the theme, and ships a demonstration corpus of **60 records
across six registers** — 224 files in `content/`, 133 config objects — which a clean install
renders as a working portal.

⚠️ **This paragraph described a skeleton until 2026-09-20, months after that stopped being true,
and the section below still listed the content model and the demo content as "not yet".** A status
section is the first thing a reader believes and the last thing anyone re-reads.

**What the template does today**

* Installs a working Drupal CMS site: administrative back end, media, basic SEO, basic privacy and
  consent, anti-spam, authentication tweaks and HTML email.
* Installs Ágora's own front-end theme — `drupal/agora_theme`, a separate Drupal.org project that
  this template requires at `^1.1` — and makes it the site's default theme.
* Sets a Canvas landing page as the home page, carrying **five** blocks from the template's own
  views. (It read *four* until 2026-09-20 and was already wrong: the page carried six, and
  T-1310 moved the service-area cards off it to `/publications`, beside the register they
  filter. A hand-written count in prose is a number nothing checks.)
* Ships the demonstration corpus described in the next section: six registers of invented records
  for a fictional Spanish municipality, with generated PDFs and CSV distributions. Their counts,
  their provenance and their licences are stated once, in
  [`content/MEDIA-LICENCES.md`](content/MEDIA-LICENCES.md), which ships inside the package.

**What does not exist yet**, and is therefore not offered by this template — this is the plan, not a
feature list:

| Planned | Where it is going |
|---|---|
| ~~Content model: documents, officials, contracts, budget lines, public calls~~ **DONE, unit 002** | six bundles ship in `config/` |
| ~~Demo content — English only, see below — and the real screenshot~~ **DONE, unit 003** | 60 records, and `screenshot.webp` is a real 500×400 capture |
| Editorial workflow and freedom-of-information requests — **deferred past the first release** (D-060) | unit 004, after the first release |
| ~~Configuration auditing~~ **DONE, unit 005** | `drupal/config_guardian` in `require`, installed by `recipe.yml`, with its settings and a governance-auditor role in `config/` |
| AI assistant with citations | unit 005 |

The first release is a transparency **publication** portal: it publishes registers of records and
a document library, for anyone to browse, filter and download. That is a decision, recorded as
D-060 in `specs/000-project/DECISIONS.md`; the full intended scope, including what that decision
defers past the first release, is written down in `specs/000-project/plan.md`. Both are in this
repository.

## The demo content is a fictional Spanish municipality, published in English

This is stated here, and in one sentence on the home page, because the demonstration corpus is
coherent and nothing on the site said so: a reviewer who reads an English-language portal publishing
Spanish legal instruments would reasonably read it as carelessness rather than as a choice. It is a
choice. The corpus belongs to **Fuentelclaro**, a **fictional Spanish municipality** whose name was
checked against Spain's official register of municipalities and found not to exist; every person,
figure, contract and document in it is invented. Its six registers — documents, people, contracts,
agreements, grants and datasets — are derived from **Spanish transparency law**, which is why the
demo publishes nine budget chapters, a by-law on the tax on construction and works, an equality plan
and declarations of assets for elected members: those are the categories a Spanish municipality is
required to publish, not a selection made for effect. The corpus is nonetheless in **English**, and
that is deliberate too — a site template is structurally invisible to Drupal's interface-translation
system, so its shipped strings are English and Spanish is a **post-install** path through the
`language` and `config_translation` modules. The reasoning behind each half is recorded as D-026,
D-035 and D-041 in `specs/000-project/DECISIONS.md` in this repository.

## Accessibility

**Ágora targets WCAG 2.2 AA, and two automated gates now measure it — but neither of them is a
conformance claim, and the difference is the point of this section.**

⚠️ **This paragraph said until 2026-09-20 that "no such check has run here, because there is
nothing in this repository to run it against".** That was false, and it contradicted the
accessibility statement this same package ships, which describes the check in detail. Understating
is the safe direction for liability and the wrong one for a reader deciding whether to trust the
product.

What actually runs, and what it covers:

* **In this repository**, `tests/src/FunctionalJavascript/AccessibilityTest.php` runs axe-core over
  the pages of the site as the template installs it — the composed landing pages, the front page as
  a visitor meets it, several listing routes, a published record and the page-not-found screen —
  **anonymously**, as a member of the public meets them. It is **blocking** in both PHPUnit jobs.
  **The count, the breakdown and the result are stated once**, in the accessibility statement this
  package ships at `/accessibility-statement`: that is the document a visitor is actually served,
  and it has to be readable on its own.
* **In `drupal/agora_theme`**, a `nightwatch` job runs axe over its fixture pages. **That figure is
  not repeated anywhere in this package.** It moves whenever the theme adds a surface, it is
  printed by the job itself, and the job's log is **public and readable without an account** — so
  the number belongs where a reader can check it rather than in a second copy that goes stale. The
  one place this package describes that gate is the shipped accessibility statement, in the
  paragraph that also says those pages are fixtures and not pages of this site.
  ⚠️ **A copy of that figure stood in this file for a day in September 2026 and was false within
  hours of being written**, because the theme's gate moved the same evening and nothing here could
  see it. `tests/bin/packaged-claims` now refuses a figure stated in two packaged files at once,
  and its header records the case.

**What neither gate covers, stated because a gate's silence is not a pass:** everything behind a
login except the Config Guardian dashboard, which is the only page the gate in this repository
scans signed in and whose findings the shipped statement reports; the listing routes that are not
scanned, the prose pages — including this statement's own page — and every record page but the one;
and the three AA criteria axe cannot decide — **2.4.7 Focus Visible, 2.5.8 Target Size (Minimum) and
1.4.10 Reflow** — plus 2.4.1 Bypass Blocks *in use*.
**No conformance with any WCAG level is claimed.** The shipped accessibility statement carries the
full account, with the denominators.

## Requirements

* **A plain Drupal site, not a Drupal CMS one.** The installation flow below starts from
  `drupal/recommended-project` — a bare Drupal core 11 codebase — and it is Ágora's own
  `composer.json` that pulls in the Drupal CMS recipes it composes, all constrained to `^2`. You do
  not need a Drupal CMS site to begin; you end up with one because Ágora requires its pieces.
* **PHP:** whatever your Drupal CMS version requires. Ágora adds no constraint of its own; there is
  no `php` entry in its `composer.json`. Drupal core `11.4.5` itself needs PHP `8.3`–`8.5` and
  Composer `2.3.6` or later — see [Toolchain floor](#toolchain-floor) below for where those figures
  come from and what else was measured alongside them.
* Composer. [DDEV](https://ddev.com) is recommended for a local environment; see
  [DDEV's installation instructions](https://docs.ddev.com/en/stable/users/install).

## Installation

> **Ágora is not released yet.** The project exists on Drupal.org —
> [drupal.org/project/agora_transparency](https://www.drupal.org/project/agora_transparency) — but it
> has no released package, so `composer require drupal/agora_transparency` does not resolve today.
> The first release is a later unit of work. Until then, install it from a local checkout, as shown
> below.

**This repository is not a site, and it cannot be brought up on its own.** It is a recipe package:
a `recipe.yml`, its configuration and its metadata. There is no Drupal in it, so there is nothing
here to start. What you do instead is set up a Drupal site somewhere else and add this package to
it as a Composer *path repository*. The sequence below is the one in the project's own
[`.github/workflows/phpunit.yml`](.github/workflows/phpunit.yml), which runs it on every push —
that file, not this section, is the authority, because it is the copy that gets exercised.

Create the project directory and a Drupal codebase inside it, without installing the site yet:

```shell
mkdir agora-site
cd agora-site
ddev config --project-type=drupal11 --docroot=web

# Copy the path repository into the project rather than symlinking it, so the
# installed package is the one an end user would get.
ddev config --web-environment-add="COMPOSER_MIRROR_PATH_REPOS=1"

ddev start
ddev composer create-project --no-install drupal/recommended-project
```

Clone this repository into the project directory, as `source/`, and add it as a path repository:

```shell
git clone <this-repository> source
ddev composer repository add source path source
ddev composer config allow-plugins.drupal/site_template_helper true
ddev composer require --update-with-all-dependencies "drupal/agora_transparency:@dev"
ddev composer require --dev "drush/drush:^13"
```

Composer places the template in `recipes/agora_transparency`, outside the docroot. The
`allow-plugins` line is needed because Ágora depends on `drupal/site_template_helper`, which is a
Composer plugin, and Composer will not execute a plugin's code without being told to; without the
line it stops and asks. The step is mandatory, not optional. Check that the package arrived:

```shell
test -d ./recipes/agora_transparency && echo present
```

⚠️ **The `drush` line is not optional and it used to be missing.** `drupal/recommended-project`
does not ship drush, and the command-line install below is a drush command — without it the
sequence stops at `drush is not available`. This was found on 2026-08-25 by T-705, the first time
anyone built this rig **from nothing**: every earlier rig had drush added by hand long before,
so a refreshed rig could never have surfaced it. A published sequence that does not run is the
defect I-024 is named for.

Then install Drupal with the template applied — either through the web installer, choosing **Ágora**
at the site template step:

```shell
ddev launch
```

…or from the command line:

```shell
ddev drush site:install --yes recipes/agora_transparency
```

Once the site is installed, `ddev exec drush status` reports `Drupal bootstrap : Successful`. Those
two checks — the directory and the bootstrap line — are the whole of what "it worked" means here.

### What a clean install actually does

This is the strongest thing this project can currently say about itself, so it is measured, not
described.

⚠️ **The measurement below predates the theme swap of 2026-08-25**, which replaced the generated
blank theme with `drupal/agora_theme`. It is left standing because a dated measurement is only
replaced by another measurement, never by an estimate: adding a dependency moves the step count and
the module count, and neither figure is re-stated here until a clean install has been run again.

Against a real, clean Drupal `11.4.5` on 2026-08-23, from a clone of the canonical
repository at `git.drupalcode.org` — not the working copy, so this is what an end user receives, not
what a developer has — applying the recipe produced **78 steps** and
`[OK] Ágora Transparency applied successfully`, exit 0. The resulting site: Drupal
bootstrap **Successful**, front page **HTTP 200**, that build's blank theme set as the site
default, the front page pointed at the Canvas page the recipe creates, and **58** non-core modules
enabled. With the test suite copied in as described in the next section,
`phpunit --fail-on-empty-test-suite` reported **`Tests: 3, Assertions: 38`** — the same counts the
CI pipeline reports.

Once Ágora is published on Drupal.org, the clone and the `repository add` line are replaced by a
plain `ddev composer require drupal/agora_transparency`, and this section will say so.

### Running the tests against an installed package

**The tests do not travel with the package.** `/tests` is `export-ignore`d in
[`.gitattributes`](.gitattributes) — an end user of a site template has no use for its test suite —
and Composer's path-repository mirroring honours `export-ignore` through the same machinery that
builds a release. So the copy under `./recipes/agora_transparency` has no `tests/` directory in it,
no matter how many times you look.

That is a trap with a green face on it: point PHPUnit at the installed package as it stands and it
finds nothing, prints `No tests executed!` and **exits 0**.

Running the tests also needs two things the plain install does not: PHPUnit itself, and the two
environment variables Drupal's functional tests read. Both belong to the setup step above, before
the `composer require` that installs the template:

```shell
ddev config --web-environment-add='SIMPLETEST_BASE_URL=$DDEV_PRIMARY_URL'
ddev config --web-environment-add='SIMPLETEST_DB=$DDEV_DATABASE_FAMILY://db:db@db/db'
ddev composer require --no-update --dev drupal/core-dev
```

Then copy the tests in, from the clone, where `export-ignore` does not apply:

```shell
rm -rf ./recipes/agora_transparency/tests
cp -R ./source/tests ./recipes/agora_transparency/
ddev exec phpunit --configuration=./web/core --fail-on-empty-test-suite ./recipes/agora_transparency
```

`--fail-on-empty-test-suite` is what turns the silent version of that failure into a loud one; the
CI workflow passes it for the same reason. Any `ddev composer` command run after the copy
re-mirrors the path repository and deletes the tests again, so keep the copy as the last step
before you run them.

## What it ships

The packaged release holds **twelve** top-level entries, and the whole tarball is **375 entries**.
They are not transcribed by hand here, because that is how two earlier versions of this paragraph
went wrong — they are derived, and the derivation is one command anybody reading this can re-run:

```shell
git archive HEAD | tar -t | sed 's#/.*#/#' | sort -u
```

Measured 2026-09-21, it prints, in that order: `.gitattributes`, `AGENTS.md`,
`LICENCE-MANIFEST.md`, `LICENSE.txt`, `README.md`, `composer.json`, `config/`, `content/`,
`logo.png`, `recipe.yml`, `recommended.yml`, `screenshot.webp`. Both figures above are now read out of `git archive` by
`tests/bin/packaged-claims` on every push, so the commit that changes what the tarball holds is the
commit that fails until this paragraph is changed with it.

⚠️ **Two entries left the package on 2026-09-21, and this paragraph went on leading with the old
count for the rest of that day.** `.gitignore` and `.mailmap` are `export-ignore`d as of that date:
both are git's files rather than the product's, and neither does anything once the package is
extracted into `recipes/agora_transparency/` — a tarball has no history for a `.mailmap` to
canonicalise, and a `.gitignore` there names `/web/`, `/vendor/` and `/.ddev/` relative to a
directory in which none of them exists. Two checks in `tests/bin/gate-a-wave1.sh` G8 hold the
exclusion, so undoing it turns the gate red rather than quietly restoring the noise.

`git archive` applies the `export-ignore` rules in [`.gitattributes`](.gitattributes), which is what
Drupal.org's packaging and a Composer release apply too — so the command answers the question
rather than approximating it. ⚠️ **A path-repository checkout is a different and larger thing**:
Composer mirrors the working tree, `export-ignore` does not apply to it, and `tests/` arrives with
it. The two were conflated in one sentence here until today.

⚠️ **This paragraph said "eight entries" until 2026-09-12, and said in the same breath "that is
not a partial list". It was one, and it was one on the day it was written.** Run the command above
at `6471756`, the commit that wrote the sentence on 2026-08-23, and it prints **eleven**: the three
dotfiles the release ships — `.gitattributes`, `.gitignore`, `.mailmap` — were left out from the
start. Two more entries then arrived and were never added to it: **`config/`** the very next day,
2026-08-24, and `logo.png` on 2026-09-02.

⚠️ **The omission that matters is `config/`**, and it is worth naming rather than folding into a
count. It is the content model — the largest single thing this package ships, and the reason the
package exists at all — missing from the one paragraph a Drupal.org visitor opens to find out what
they are installing. A hand-kept list of a directory's contents goes stale in the commit that
changes the directory, which is every commit of a content-model unit; a command does not.

**Tests do not ship, on purpose.** `/tests` is `export-ignore`d in
[`.gitattributes`](.gitattributes) — an end user of a site template has no use for its test suite —
so it is absent from every installed copy, not only from a tagged release. See
["Running the tests against an installed package"](#running-the-tests-against-an-installed-package)
above for what that means in practice and how to run them anyway, deliberately, when you need to.

## What the template applies

Recipes, in the order `recipe.yml` applies them:

| Recipe | What it contributes |
|---|---|
| `core/recipes/administrator_role` | A generic administrator role with all permissions |
| `core/recipes/core_recommended_maintenance` | Core modules that help with site maintenance |
| `core/recipes/core_recommended_performance` | Core modules that improve performance |
| `drupal_cms_admin_ui` | The administrative back end, with its theme and site management modules |
| `drupal_cms_anti_spam` | Basic anti-spam protection |
| `drupal_cms_authentication` | Tweaks to user authentication |
| `drupal_cms_media` | Basic media types and configuration |
| `drupal_cms_privacy_basic` | Basic privacy and consent management |
| `drupal_cms_seo_basic` | Basic SEO tools and configuration |
| `easy_email_express` | HTML email |

It also installs `drupal_cms_helper`, the `stark` theme and `agora_theme` — Ágora's own theme, a
separate Drupal.org project rather than code bundled here — and makes `agora_theme` the site's
default. It points the front page at an empty Canvas landing page shipped in `content/`, and hides
from the Canvas page builder a set of administrative components that are not useful for building
pages.

## Known limitations

* ~~**`screenshot.webp` is a placeholder**, and says so on its face. It is not a picture of an
  installed site.~~ **Replaced 2026-09-19: it is a 500×400 capture of the front page of an
  installed site carrying the demonstration corpus.**
* **The demo corpus is a fictional Spanish municipality published in English**, which is a
  deliberate frame rather than a gap — see
  ["The demo content is a fictional Spanish municipality, published in English"](#the-demo-content-is-a-fictional-spanish-municipality-published-in-english)
  above. It is listed here because it is the thing a reviewer is most likely to misread.

## Toolchain floor

What each platform this project is developed or gated on actually provides — measured, not
reasoned about. A tool that silently changes behaviour between platforms is the kind of defect that
fails *green*: the failure looks like a pass until someone runs it somewhere else.

### How a host gets measured

```bash
bash tests/bin/toolchain-floor                                    # this host
bash tests/bin/gate-in-container --exec 'bash tests/bin/toolchain-floor'   # the pinned image
```

`tests/bin/toolchain-floor` writes nothing, installs nothing and judges nothing — it exits 0
whatever it finds, because what it finds is evidence, not a verdict. It prints a table meant to be
pasted back whole. Every row below came out of it.

### The floor, per host

| Axis | Windows dev host (MSYS2 / Git for Windows) | Gate container (digest-pinned) | macOS |
|---|---|---|---|
| Measured | ✅ 2026-08-22, re-measured 2026-08-25 | ✅ 2026-08-25 | ❌ **never** |
| `grep` | GNU **3.0** | GNU **3.11** | not measured |
| `grep -Fi` and `grep -IFin` | **rc 134 (SIGABRT)** on both — combining `-F` with `-i` aborts it | **rc 0** on both — no abort | not measured |
| `awk` | gawk **5.0.0** | **mawk** 1.3.4 | not measured |
| `awk length` counts | **bytes** in the ambient locale (`LANG` unset), **characters** under a forced UTF-8 locale — the same binary, two answers | **bytes**, and a forced UTF-8 locale does not change it | not measured |
| `sha256sum` | present (`shasum` and `openssl` also present; all three agree) | present (all three present, all three agree) | **absent — the one difference known in advance**, see below |
| `sed` | GNU 4.9; `sed -i` with no backup suffix exits 0 | GNU 4.9; same | not measured |
| `jq` / `python3` stdout | **CRLF** on both (I-025) | clean LF on both | not measured |
| Python default encoding | **cp1252** — any script opening a repository file needs `encoding='utf-8'` explicitly; the product is named *Ágora* and the `Á` breaks the default | UTF-8 | not measured |
| Locale | `LANG`, `LC_ALL`, `LC_CTYPE` all unset | `LANG=LC_ALL=C.UTF-8` | not measured |
| `cd ""` | exits **0** without moving | exits **0** without moving | not measured |
| Other versions | `jq` 1.8.2, Python 3.12.6, PHP 8.4.24 **ZTS**, Composer 2.10.2 | `jq` 1.7, Python 3.12.3, git 2.43.0, curl 8.5.0 — recorded in `tests/container/compose.yaml` | not measured |
| Dirty-case matrix (T-312) | ✅ run 2026-08-22 — **CERTIFIED** | ❌ not run — measured, not certified | ❌ not run |

Two more hosts are recorded but are not development hosts, so they get a line rather than a column:

- **WSL2 Ubuntu 24.04** — `docker-ce` **28.1.1**, DDEV **1.24.4**. Where the clean-install smoke
  actually runs, and DDEV's own recommended Windows setup.
- **drupalcode CI runner** — `jq`, `python3`, `curl`, `git` and `composer` all present, verified by
  the invariants job passing its preflight after this project predicted some might be missing and
  was wrong.

### macOS: NOT CERTIFIED

**Named blocking reason: nobody has run the probe there.** Not one measurement exists. That is the
whole reason, stated plainly rather than dressed up as a technical obstacle — macOS ships a BSD
userland, so its `grep`, `sed` and `awk` are different programs wearing the same names, and
guessing what they do is precisely what this section exists to forbid.

One difference is known in advance without a Mac in the room, because it is a fact about that
system's packaging rather than a prediction: **macOS ships no `sha256sum`.** It ships `shasum`.
That matters for `drupal/agora_theme`, whose `tests/bin/shared-invariants` is built entirely out of
sha256 comparisons. It is already handled: that script selects the first of `sha256sum`,
`shasum -a 256` and `openssl dgst -sha256` that exists, and refuses to run at all — loudly — if
none does. Falsified on 2026-08-25 under a `PATH` shim that hid each in turn: all three produce
`f7070d57bbe5496e29249421e91572f46ac4c2b62953b7ea046fa3707b9e6b2a` for the five bytes `agora`, all
three give the same **6 records · 0 findings**, and with all three hidden the script exits **1**
with a FATAL rather than reporting a false clean. `tests/bin/doctor` now names the implementation
it selected, on every host, for the same reason: a drift detector that quietly changes which
program computes its hashes is worse than one that is noisy.

**What would flip macOS to CERTIFIED**, in order:

1. Run `bash tests/bin/toolchain-floor` on the Mac and paste the whole output back. That fills the
   empty column above and is the only step that needs a Mac in front of a human.
2. Run `bash tests/bin/doctor` there; it must reach `READY`.
3. Run both wave runners and reproduce the counts this repository quotes — **95 checks · 0
   failures** and **67 checks · 0 failures**. ⚠️ **Re-run them rather than trusting this line.**
   It read `61` and `37` until 2026-09-12 and `67` and `49` until 2026-09-21, which was six
   invariants and twenty-seven checks behind by the end: the runners grew and the sentence did not,
   twice. It then read `88` for part of one day, and this time the guard below moved it — which is
   what the last sentence of this item was written to make true. Both figures are now compared against each runner's own `GATE-CLAIM` header line by
   `tests/bin/packaged-claims`, which runs in the blocking `agora-invariants` job — so this is the
   last time that drift can happen silently. Each runner also prints its own total on its last
   line, which is the number to compare against.
4. Re-run the dirty-case matrix (T-312): 12 injections, each reverted, each seen to fail. A
   platform where no invariant has been watched *failing* has not been shown to have working
   invariants at all — that is what certification means here, and it is the step that separates
   this column from the container's.

Until step 4, macOS stays **NOT CERTIFIED**, and so does the container. Host mode remains
explicitly allowed on both (D-019 rider c); a green result from either is simply not the same
statement as a green result from the Windows host.

### Reading this table

Any platform not in it, or listed with a gap in it, is **NOT MEASURED** — never a plausible guess.
That is the entire point of keeping it. And it is a dated measurement, not a promise: the commit
that changes what a host provides is the commit that updates this table.

Drupal itself has a floor independent of all of the above: core `11.4.5` needs PHP `8.3`–`8.5` and
Composer `2.3.6` or later. The Windows dev host runs PHP 8.4.24 **ZTS** and Composer 2.10.2.

`tests/bin/doctor` is how a machine gets checked against all of this. Run it before trusting
anything else on a new host, and trust its output over this table: a table decays, a probe does
not.

## Continuous integration

Ágora's pipeline is the shared `gitlab_templates` pipeline the Drupal Association maintains, run on
[git.drupalcode.org](https://git.drupalcode.org), plus one job of our own defined on top of it,
`agora-invariants`. This is the list of jobs that actually ran, taken from pipeline `952632` on
branch `1.x`, commit `6559813`, read on 2026-09-12 from
`/api/v4/projects/project%2Fagora_transparency/pipelines/952632/jobs` — not from the badge, and not
from the set of jobs the template could in principle run:

| Job | Stage | Status | `allow_failure` |
|---|---|---|---|
| `Drupal CMS` | build | success | false |
| `agora-invariants` | validate | success | false |
| `composer` | build | success | false |
| `composer-lint` | validate | success | false |
| `cspell` | validate | success | false |
| `eslint` | validate | success | false |
| `phpcs` | validate | success | false |
| `phpstan` | validate | success | false |
| `phpunit` | test | success | false |
| `phpunit-pgsql` | test | success | false |

**Ten jobs · all blocking · zero named exceptions.**

⚠️ **This table said EIGHT until 2026-09-12, and it had been wrong in two separate ways for weeks.**
It stood at pipeline `934387` from 2026-08-24, a ninth job (`Drupal CMS`) was appended below it in
its own dated table rather than merged in, and a **tenth** — `phpunit-pgsql`, added on 2026-08-27
under D-040(2) — appeared in neither and was named nowhere in this file. The two old tables are
folded into the single one above for the reason `CLAUDE.md` merged its own pair: two observations of
one list, split by the commit that produced each, get harder to read as one thing with every
addition, and the second one is the copy nobody refreshes. **The names are what carry meaning, not
the total** — see "The tenth job" below for why a count alone would have hidden the thing worth
knowing.

Read the last column as the API reports it: `allow_failure: false` is a blocking job. This project
quotes the field rather than a "yes" of its own, because the gate is a statement about that field
and a translation is one more thing to keep true.

This table is a dated measurement, not a promise: whichever commit changes the CI job list, the
packaged file set or a gate's denominator is the commit that updates it. `stylelint` is absent from
the list because this package contains no CSS, and since the theme is a separate Drupal.org project
(D-014) it may never run here at all; it does run there.

### The ninth job: the clean-install smoke, now on the canonical gate

`.gitlab-ci.yml` sets `OPT_IN_TEST_DRUPAL_CMS: '1'` and `_AUTORUN_DRUPAL_CMS: 'all'` — two
variables the shared `gitlab_templates` pipeline already understands, with no job defined or
overridden here. Together they turn on a job named `Drupal CMS` that builds a fresh Drupal CMS
site, installs this package into it from a Composer path repository, and runs Drupal CMS's own
compatibility test against it.

First observed in pipeline `934533` on branch `1.x`, commit `09fb47b`, read from the API on
2026-08-24; it is the first row of the current table above, still `success` and still blocking at
pipeline `952632` on 2026-09-12. The row was published empty in the commit that declared the job and
filled in the commit that observed it, because this project publishes job lists it has watched run
or none at all.

Two outcomes would have been failures rather than passes, and both were written down before the
pipeline ran: a job absent from the list would mean the minimum of nine is unmet and the work is
not done, and a job present but non-blocking would need a dated, owned exception. Neither
happened — the job appeared and arrived blocking on its own. Worth knowing why that was not
automatic: the variable that makes the validate stage blocking does not reach this job, which is
declared in the build stage.

Until this landed, the clean-install smoke ran only on the GitHub mirror, which is an informative
surface — a reviewer on Drupal.org can neither see nor re-run it. The mirror keeps running as a
second opinion; what ended is its monopoly.

### The tenth job: the suite runs on a second database

`phpunit-pgsql` was added on 2026-08-27 under **D-040(2)**. It runs the same test suite as
`phpunit` against PostgreSQL 16 instead of MySQL, and it is job `12052124` of pipeline `952632`,
`success` and blocking, in the table above. **This README named it nowhere until 2026-09-12**,
which is the reason it gets a heading of its own rather than a row.

⚠️ **Why a tenth job exists is the most useful sentence in this section.** The nine-job list was
green while this package shipped a view that summed a **text** column — PostgreSQL refusing the
query outright, MariaDB answering `0` with a warning, SQLite answering `0.0` in silence — and every
assertion passed because nothing read the result. **A job list is only as good as the environments
it runs in**, and until D-040(2) this one ran MySQL and SQLite because those are the shared
pipeline's defaults, not because anyone chose them.

The job also demonstrates a trap worth carrying elsewhere: it prints the database under test twice,
once as the **unexpanded literal** every job in the phpunit family echoes and once as the real
value, and it exits non-zero if the real value is not `pgsql`. A criterion that grepped the log for
the expected string would have matched the literal and passed a PostgreSQL job that never touched
PostgreSQL. Those two lines are quoted in full in D-040(2), read there with the maintainer's token;
⚠️ **they cannot be re-read from here**, because `/api/v4/.../jobs/<id>/trace` answers **401** to an
anonymous request — verified 2026-09-12. The **job list** is public and is what this section quotes.

**The gate is the list of jobs, never the pipeline's status field.** This is not a preference. An
earlier pipeline reported `success` while the spell check inside it had failed: four of the seven
jobs then defined were non-blocking by upstream default, so their failures were recorded and then
rolled up into a green result that hid them. The failure repeated in the opposite direction on
2026-08-24 — `cspell` red and **blocking** on `934242`, `934297` and `934329`, so the pipelines
were correctly red and nobody read them, because the local pre-flight this README documented was
unreadable. Both halves are the same lesson: a signal has to be both correct and read.
All **ten** jobs are blocking now, with no exceptions —
`_ALL_VALIDATE_ALLOW_FAILURE: '0'` in [`.gitlab-ci.yml`](.gitlab-ci.yml) is what makes the validate
stage stop the pipeline, and the two jobs outside that stage arrived blocking on their own. Read
the job list; the status field has already been wrong here once. ⚠️ This sentence read *"all eight
jobs"* until 2026-09-12, two jobs after it stopped being true — which is the same defect as the
table it summarises, one sentence further from the measurement.

**What the green does not tell you.** The 36-versus-63 gap reported earlier is closed, and has
stayed closed across five changes of denominator. Measured 2026-09-21 by
`bash tests/bin/spellcheck`: **472 tracked or stage-able files offered to cspell, 431 checked,
`Issues found: 0`** — plus two the CI runner generates and this repository does not track
(`.editorconfig`, `gitlab_templates_version.txt`), which is why the job's own count reads two
higher. The script prints both numbers every time it runs, so this paragraph is checkable rather
than quotable. ⚠️ It read **70 tracked · 65 opened** here until 2026-09-12; the repository has since
grown a content model and a media corpus, and the figures were carried rather than re-run.

**The 41 files not opened, all 41 of them.** The accounting is given in full because an enumeration
that does not add up to its own denominator reads as an explanation — that exact mistake stood in
`CLAUDE.md` across three re-measurements, where five named files were offered against a gap of
thirty-nine, and nobody did the subtraction.

* **37 are binaries `cspell` does not open** — every file under `content/file/` that is neither a
  `*.yml` entity export nor one of the CSV distributions it reads happily, plus `screenshot.webp`
  and `logo.png` at the root. The per-format counts are stated once, in
  [`content/MEDIA-LICENCES.md`](content/MEDIA-LICENCES.md), and this bullet does not restate them.
* **4 are matched by the upstream `.cspell.json` defaults, not by omission** — `.eslintrc.json` and
  `.gitignore` match its dotfile and `*ignore` patterns; `LICENSE.txt` and `composer.json` match its
  case-insensitive filename list regardless of extension.

37 + 4 = 41, and the list was derived rather than recalled: run `cspell` without `--no-progress`
over the same `--file-list` the script builds, and the files it opens are printed one per line; the
41 above are the set difference. ⚠️ **`cspell` reads `.gitattributes` and `.mailmap`**, which is
easy to guess wrong in either direction — the dotfile pattern is narrower than "every dotfile".

`phpcs`, `phpstan` and `eslint` still print no file count at all unless asked. A passing check over
an unknown number of files is a weaker statement than it looks, and it is written down here as one
rather than counted as coverage.

### Checking spelling before you push

`cspell` is blocking, and it reads `README.md` and everything under `specs/`. That makes every prose
commit a gate, so run it before you push:

```shell
bash tests/bin/spellcheck
```

It prints the file count and either `Issues found: 0` or the words, and it exits non-zero when it
finds something — so it is usable in a hook or a loop, not only by eye.

**What changed, and why the old advice is gone.** This section used to offer a bare
`pnpm dlx cspell@9.8.0 --locale en,en-GB README.md "specs/**/*.md"` and warn that it was *"an
approximation … not a second opinion on the gate"*. It was worse than an approximation. This
repository deliberately has **no `.cspell.json`** (D-024(2)), so that command loaded no project
dictionary and neither of Drupal core's: it reported hundreds of words the job accepts, and the
real failures were indistinguishable inside the noise. **Three consecutive pipelines went red on
`cspell`** — `934242`, `934297`, `934329` — while the local command kept printing the same
unreadable output it always printed.

`tests/bin/spellcheck` fetches the job's actual inputs instead of guessing them: the
`assets/.cspell.json` that `gitlab_templates` copies in, Drupal core's two
`core/misc/cspell/*.txt` dictionaries, and this project's `.cspell-project-words.txt` — then applies
in shell the same transformations `scripts/prepare-cspell.php` applies in the job. It reads
**tracked and stage-able files both**, because a file about to be committed is a file the job will
read. Verified equivalent against pipeline `934329` on 2026-08-24: same verdict before the fix, and
a clean run after it. ⚠️ **The denominator is deliberately not repeated here** — it stood at
`65 files checked` in this sentence long after it had moved, because the same figure was written
down in two places and only one of them was ever refreshed. It is stated once, in
"What the green does not tell you" above, and the script prints it on every run. It is a replica,
not the job — it pins nothing about the runner's Node version, and upstream can change
`prepare-cspell.php` without this file noticing.
The first run needs network for those three inputs and caches them in `.cspell-cache/`, which is
git-ignored; later runs are offline, and a stale cache says so rather than pretending.

Words that are genuinely words go in
[`.cspell-project-words.txt`](.cspell-project-words.txt), one at a time, each with the reason it
belongs there written beside it. A **verbatim quotation** is not vocabulary and does not go there:
it is scoped where it sits, with `cspell:disable`/`cspell:enable` around a phrase or `cspell:ignore`
for a long quoted passage — which is why the research file quoting four Spanish statutes carries
~190 words in its own header and none of them in the project dictionary. The job offers an artefact
that is "this dictionary plus everything that just failed"; importing that wholesale is one command
away from a green pipeline and is forbidden here, because it declares the next real misspelling
correct before anyone has seen it.
## Support

Bugs and questions go to the project's issue queue on Drupal.org:
[Issues for Ágora Transparency](https://www.drupal.org/project/issues/agora_transparency). It is open
and currently empty. There is no release yet, so there is no supported version to report against —
anything filed today is about work in progress.

## License

GPL-2.0-or-later. See [LICENSE.txt](LICENSE.txt) for the licence text itself.

**What every file in the package is licensed under is stated in
[`LICENCE-MANIFEST.md`](LICENCE-MANIFEST.md)** — the package's own exports and prose, the projects
it requires, the fonts (which ship in the theme and not here), the media and the generated
assets, each with the invariant that keeps its figure honest. That file is where those numbers
live; this section deliberately carries none of them.

Neither published Drupal CMS site template ships such a manifest. The measurement behind that
sentence — which files were examined and how many matched — is stated once, in the manifest
itself, and is not repeated here.

## Development process

Ágora is built with a human-in-the-loop process, and the artefacts of that process are kept in the
open rather than tidied away before release.

* **Decisions are signed before they are implemented.** Every load-bearing choice — the package
  name, the recipe architecture, where the theme lives, the dependency policy, the publication
  route — is recorded as a numbered, append-only entry in
  [`specs/000-project/DECISIONS.md`](specs/000-project/DECISIONS.md) and approved by a human
  before any code depends on it. Signed entries are never rewritten: they are amended or superseded.
* **Work is planned in units and waves.** Each unit under [`specs/`](specs/) carries a plan, an
  explicit task list and a verification gate that has to pass with real counts — an exit code of
  zero on its own does not close anything.
* **AI assistance is used, and disclosed.** Parts of this repository were drafted with AI coding
  assistants working under human direction and review. The instructions those assistants operate
  under are in `CLAUDE.md` and `.claude/`, in this repository, for anyone to read. A human reviews
  and signs every decision and every gate and is accountable for what is released. Commits are
  attributed to their human author, with no AI co-authorship trailers.
* **The process layer does not ship.** `CLAUDE.md`, `.claude/` and `specs/` are marked
  `export-ignore` in `.gitattributes`: they stay visible to anyone who clones the repository, but
  they are not part of the packaged release an end user installs. `AGENTS.md` is the deliberate
  exception — it is product, and documents how AI assistants should work on a site built *with*
  this template.
