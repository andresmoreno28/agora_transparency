# 💧 Site Template Starter Kit

If you're here to create a site template for Drupal CMS, you've come to the right place; see [GET-STARTED.md](GET-STARTED.md).

**You should customize this file** and fill it with information about the fantastic site template you build with this starter kit. 🚀

A screenshot is a great way to start:

![A screenshot of my amazing site template.](screenshot.webp)

## Key Features
Describe who this site template is for, and what it does particularly well. For example:

* Designed for professional electricians
* Stellar content model with job management functionality
* A gorgeous theme that meets WCAG AAA standards
* E-commerce capabilities
* AI features that will amaze you
* Comes in any color you want

## Installation
These are generic instructions for how to install the finished site template; customize these however you want.

We recommend using DDEV 1.25.0 or later to set up your local Drupal development environment; see [DDEV's installation instructions](https://docs.ddev.com/en/stable/users/install). Once you have DDEV, you can set up this site template as follows:
```shell
mkdir my-project
cd my-project
ddev config --project-type=drupal11 --docroot=web
ddev composer create-project drupal/cms
ddev composer require drupal/MY_SITE_TEMPLATE_NAME
ddev launch
```
Replace `MY_SITE_TEMPLATE_NAME` with the actual name of your site template, from the `name` field of `composer.json`.

## Known Issues
Are there any bugs or gotchas you want end users to know about? List them here, along with any workarounds.

## Support
Provide a few links here where your end users can get help.

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
