---
name: drupal-recipe-authoring
description: Use when writing or editing a Drupal recipe.yml — adding config actions, choosing between the recipes/install/config keys, setting strict mode, making config optional, disabling Canvas components, or debugging a recipe that fails to apply or conflicts with existing configuration.
---

# Writing `recipe.yml`

## Core principle

A recipe is **declarative and idempotent-by-design**: it describes the desired state, not steps.
If it fails to apply, it is almost always because of **config that already exists** or **config that
does not exist yet**.

## Anatomy

```yaml
name: Visible name
description: 'Text the user sees in the installer.'
type: Site          # 'Site' only for site templates; other recipes omit it

recipes:            # recipes applied BEFORE this one
  - core/recipes/administrator_role
  - drupal_cms_media

install:            # modules and themes to install
  - pathauto
  - my_theme

config:
  strict: false     # see below — almost always false in site templates
  actions:
    some.config.name:
      whicheverAction: value

extra:
  recipe_installer_kit:
    finish_url: '/admin/dashboard/welcome'
```

## `recipes:` vs `install:` — the most frequent confusion

| Key | What it accepts | When |
|---|---|---|
| `recipes:` | Other **recipes** (by core path or by name) | You want their config and their complete model |
| `install:` | Standalone **modules and themes** | You only need the module to be enabled |

The recipes listed in `recipes:` **must not be site templates**. Composing on top of small recipes
is correct; on top of another site template, it is not.

## `strict: false` — what it really means

- `strict: false` → if the site **already has** a config that the recipe provides, **the existing one
  wins** and the recipe does not fail.
- `strict: true` → conflict = error when applying.

In site templates **`false`** is used. Literal comment from the starter kit: *"most site templates
provide configuration that will break at install time if you change this"*.

## The `?` prefix — optional config

```yaml
config:
  actions:
    ?canvas.component.block.navigation_user:   # with ? → if it does not exist, it is ignored
      disable: []
    canvas.component.block.system_messages_block:   # without ? → if it does not exist, ERROR
      disable: []
```

**Rule:** if the config is provided by a module that may not be installed, put a `?` on it.
One `?` too many is harmless; one too few breaks the clean installation.

## Common actions

| Action | Use |
|---|---|
| `simpleConfigUpdate` | Change keys of simple config (`system.site`, `system.theme`) |
| `disable: []` | Hide a Canvas component from the UI (does not delete it) |
| `setComponentList` / `grantPermissions` | Depending on the entity; depends on the config type |

```yaml
system.site:
  simpleConfigUpdate:
    page.front: '/home'
system.theme:
  simpleConfigUpdate:
    default: 'my_theme'
```

## Common mistakes

- **Forgetting `?`** on config from an optional module → breaks the clean install smoke.
- **Putting a module in `recipes:`** (or a recipe in `install:`) → it does not resolve.
- **Changing `strict` to `true`** to "be more rigorous" → breaks the installation.
- **`type: site`** in lowercase → the template does not appear in the installer (it is case-sensitive).
- **Assuming order**: `recipes:` is applied first; if your action touches config provided by another
  recipe, that recipe must be listed first.

## Verification

Apply on a **clean** Drupal, never on the dirty development environment:

```bash
ddev drush sql:drop --yes
# reinstall and check that the template appears in the selector
```

A missing `?` only shows up on a clean install. Testing on the working environment gives false greens.
