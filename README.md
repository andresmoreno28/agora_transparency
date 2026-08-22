# Ágora Transparency

Ágora Transparency — **Ágora** for short — is a site template for Drupal CMS aimed at **transparency
and open government portals**: small local councils, public bodies, foundations and any organisation
that has to publish what it decides, what it spends and who works for it.

![Placeholder image, not a screenshot: Ágora has no demo site yet, so there is nothing real to capture. It will be replaced once the demo content exists.](screenshot.webp)

## Status — in development

Ágora is being built in the open and is **not usable as a transparency portal yet**. So far only the
foundation exists: the packaging skeleton, the recipe that composes Drupal CMS, and the test suite
that installs Drupal with the template applied.

**What the template does today**

* Installs a working Drupal CMS site: administrative back end, media, basic SEO, basic privacy and
  consent, anti-spam, authentication tweaks and HTML email.
* Generates a blank front-end theme at install time and sets a blank Canvas landing page as the
  home page.
* Nothing else. There is no transparency-specific functionality in it yet.

**What does not exist yet**, and is therefore not offered by this template — this is the plan, not a
feature list:

| Planned | Where it is going |
|---|---|
| Content model: documents, officials, contracts, budget lines, public calls | unit 002 |
| Ágora's own theme, as the separate project `drupal/agora_theme` | unit 002 |
| Bilingual (ES/EN) demo content and the real screenshot | unit 003 |
| Editorial workflow and freedom-of-information requests | unit 004 |
| AI assistant with citations, and configuration auditing | unit 005 |

The full intended scope is written down in `specs/000-proyecto/plan.md` in this repository.

## Accessibility

**A goal, not a verified result — yet.** Ágora targets WCAG 2.2 AA, and the plan is for automated
accessibility checks over the demo pages to gate every release, with the outcome reported in this
section. No such check has run, because there is nothing to run it against: the template currently
ships no theme, no components and no demo pages. **No conformance with any WCAG level is claimed at
this point.**

## Requirements

* A **Drupal CMS 2.x** site — the Drupal CMS recipes Ágora builds on are constrained to `^2`, and
  that line runs on Drupal core 11.
* **PHP:** whatever your Drupal CMS version requires. Ágora adds no constraint of its own; there is
  no `php` entry in its `composer.json`.
* Composer. [DDEV](https://ddev.com) is recommended for a local environment; see
  [DDEV's installation instructions](https://docs.ddev.com/en/stable/users/install).

## Installation

> **Ágora is not released yet.** The project exists on Drupal.org —
> [drupal.org/project/agora_transparency](https://www.drupal.org/project/agora_transparency) — but it
> has no released package, so `composer require drupal/agora_transparency` does not resolve today.
> The first release is a later unit of work. Until then, install it from a local checkout, as shown
> below.

Create a Drupal CMS project, but do not install the site yet:

```shell
mkdir agora-site
cd agora-site
ddev config --project-type=drupal11 --docroot=web
ddev composer create-project drupal/cms
```

Clone this repository into the project directory, as `source/`, and add it as a path repository:

```shell
git clone <this-repository> source
ddev composer repository add source path source
ddev composer config allow-plugins.drupal/site_template_helper true
ddev composer require --update-with-all-dependencies drupal/agora_transparency:@dev
```

Composer places the template in `recipes/agora_transparency`. The `allow-plugins` line is needed
because Ágora depends on `drupal/site_template_helper`, the Composer plugin that generates the blank
theme; without it, Composer will stop and ask. This is the same sequence the project's own CI uses to
install the template before running its tests.

Then install Drupal with the template applied — either through the web installer, choosing **Ágora**
at the site template step:

```shell
ddev launch
```

…or from the command line:

```shell
ddev drush site:install --yes recipes/agora_transparency
```

Once Ágora is published on Drupal.org, the first two commands of the second block are replaced by a
plain `ddev composer require drupal/agora_transparency`, and this section will say so.

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

It also installs `drupal_cms_helper`, the `stark` theme and the generated `blank` theme, points the
front page at a blank Canvas landing page shipped in `content/`, makes `blank` the default theme, and
hides from the Canvas page builder a set of administrative components that are not useful for
building pages.

## Known limitations

* **The site has no design.** The default theme is `blank` — a deliberately empty theme. Ágora's own
  theme is a separate project that does not exist yet.
* **`screenshot.webp` is a placeholder**, and says so on its face. It is not a picture of an
  installed site.
* **No demo content**, beyond the empty home page.

## Support

Bugs and questions go to the project's issue queue on Drupal.org:
[Issues for Ágora Transparency](https://www.drupal.org/project/issues/agora_transparency). It is open
and currently empty. There is no release yet, so there is no supported version to report against —
anything filed today is about work in progress.

## License

GPL-2.0-or-later. See [LICENSE.txt](LICENSE.txt).

## Development process

Ágora is built with a human-in-the-loop process, and the artefacts of that process are kept in the
open rather than tidied away before release.

* **Decisions are signed before they are implemented.** Every load-bearing choice — the package
  name, the recipe architecture, where the theme lives, the dependency policy, the publication
  route — is recorded as a numbered, append-only entry in
  [`specs/000-proyecto/DECISIONES.md`](specs/000-proyecto/DECISIONES.md) and approved by a human
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
