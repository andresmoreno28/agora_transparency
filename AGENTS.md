# Agent guidance for this Drupal site

This codebase is a Composer-managed Drupal site. Local development uses `ddev`.

> **Audience.** This file is for AI assistants working on a Drupal site **built with the Ágora
> Transparency site template** (`drupal/agora_transparency`) — not on the template itself. If you
> are developing or maintaining that template, `CLAUDE.md` in its repository governs and this file
> does not apply to you. Do not read the notes below as instructions for changing the template.

## Local environment (DDEV)

Run commands from the project root:

- Start or restart the local environment with `ddev start`, `ddev restart`, and `ddev stop`.
- Install PHP dependencies with `ddev composer install`.
- Open the site with `ddev launch`.
- Run Drush commands with `ddev drush <command>` such as `status`, `user:login`,  `cache:rebuild`, and `update:db`.

DDEV project config lives in `.ddev/config.yaml`. Use `.ddev/config.local.yaml` for machine-specific overrides.

## Common Drupal workflows

- Add a module with `ddev composer require drupal/<project>`, then  `ddev drush pm:enable --yes <module_machine_name>`, then `ddev drush cache:rebuild`.
- Apply database updates after code changes with `ddev drush update:db --yes`.
- Import repository configuration into the site with `ddev drush config:import --yes`.
- Export site configuration back to the repo with `ddev drush config:export --yes`.

## Guardrails

- Do not commit secrets or machine-local overrides such as `.env`, `settings.local.php`, or `.ddev/config.local.yaml`.
- Do not commit `vendor/` or uploaded files under `web/sites/*/files`.
- Do not edit Drupal core or contributed projects in place.
- Put custom code in `web/modules/custom` and `web/themes/custom`.

## Template-specific notes

These notes are specific to Ágora Transparency, a transparency and open government site template. They describe
how to work on a site that was created from it.

### Content model

- The content model on this site was installed by applying the Ágora site template **once**, at
  install time. Inspect what actually exists before assuming anything: `drush pm:list --status=enabled`,
  `drush config:status`, and `/admin/structure/types`.
- Identifiers that Ágora owns carry a functional-area prefix: `agora_base_*`, `agora_publishing_*`,
  `agora_foi_*`, `agora_ai_*`, `agora_governance_*`. Keep the prefix when you extend an area; it is
  what keeps the areas separable.
- A site template is applied once and is then disposable. It has **no upgrade path**. Never re-apply
  it to an existing site expecting an update, and never treat a newer release of the template as a
  migration path for this site.

### Editorial workflow and roles

- Roles and permissions come from the applied template. Read them from the running site
  (`drush role:list`, `/admin/people/permissions`), not from documentation that may have drifted.
- Change permissions through the UI or Drush and then export. Do not widen access by editing
  exported permission YAML by hand.

### Theme notes

- **The theme is not part of the site template.** A site template cannot contain code of its own —
  the package must contain zero `*.info.yml` files — so themes and modules are declared in the
  `require` section of `composer.json` and installed from `recipe.yml`.
- The theme is therefore an ordinary contributed project. Do not copy it into `web/themes/custom`
  and do not patch it in place. To change the site's appearance, create a subtheme under
  `web/themes/custom`, or override through theme settings and CSS.
- Accessibility is a hard requirement of this template, not a preference: WCAG 2.2 AA. Keep semantic
  markup, keep a visible focus indicator with at least 3:1 contrast against its background, use
  `<th scope="...">` on data tables — salary and contract tables are core content here — and never
  let colour be the only thing that carries meaning.

### Deployment notes

- **Never hand-edit exported configuration.** Everything under `config/` is generated. Change the
  site through the UI or Drush, run `ddev drush config:export --yes`, then review the diff before
  committing. Hand-edited exports drift from the site and fail on import.
- **AI features degrade on purpose.** Ágora depends on no AI provider and requires no API key.
  Without a configured provider, AI features are simply unavailable and the rest of the site works
  normally. Never commit an API key, token or endpoint — configure providers through environment
  variables or the admin UI after installation. CI runs with no keys at all and must keep working
  that way.
- Keep dependencies on **stable** releases only: no `dev`, `alpha`, `beta` or `rc` versions, no
  pinned exact versions, and no patching of contributed code.

## References

- https://docs.ddev.com/en/stable/
- https://www.drupal.org/docs/administering-a-drupal-site/configuration-management/workflow-using-drush
