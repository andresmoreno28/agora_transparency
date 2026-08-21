---
name: exportar-config-limpia
description: Use when exporting a Drupal site into a recipe with drush site:export, or when reviewing exported YAML under config/ — checking for site UUIDs, _core hashes, unstable ordering, leaked secrets or environment-specific values before committing the export.
---

# Exporting the site into a recipe, cleanly

## Core principle

`drush site:export` turns the working site into a recipe. **What it exports is what is there** — if
the environment was dirty, the recipe comes out dirty. The cleanup is done **before** exporting and
is **verified** afterwards, in the diff.

## Flow

```bash
# 1. Leave the site exactly as you want the user to receive it
# 2. Export
ddev drush site:export
# 3. Test on a CLEAN install (not on the working environment)
ddev drush sql:drop --yes
#    reinstall and check that the template appears in the selector
```

`site:export` does two things on its own that are worth knowing about:
- **It adds to the `require`** of `composer.json` the modules/themes/recipes the site uses.
- **It removes the `extra.drupal-site-template` block** (the `generate-theme` one).

## What to ALWAYS review in the diff

| Look for | Why | Action |
|---|---|---|
| `uuid:` at the root level of a config | It is the UUID **of the origin site** | Remove |
| `_core:` / `default_config_hash` | Tied to the origin installation | Remove unless it belongs |
| Absolute paths (`/var/www`, `/home/...`) | Environment-specific | Remove |
| API keys, tokens, passwords, DSNs | **Never in the repo** | Remove and move to the environment |
| `mail`, real domains, analytics IDs | Development environment data | Neutralise |
| Development modules (`devel`, `webprofiler`) | They do not go to the end user | Uninstall and re-export |
| Unstable ordering between exports | Generates noise diffs | Re-export and compare |

## Quick check before committing

```bash
grep -rn "_core\|default_config_hash" config/ | head
grep -rniE "api[_-]?key|secret|token|passwd|password" config/ content/ | head
grep -rn "/var/www\|/home/" config/ | head
```

Any hit → **stop and clean**, do not commit "and I'll fix it later".

## Demo content

It comes out to `content/<entity_type>/<uuid>.yml`. Rules:
- Only content over which **legal rights** are held (images, fonts, texts).
- No real personal data: names, emails and phone numbers made up and obviously so.
- The referenced binary files must travel with the template.

## Common mistakes

- **Exporting from the dirty environment** with dev modules installed → they travel to the user.
- **Testing the recipe on the same site it was exported from** → it always passes; it proves nothing.
- **Committing the export without reading the diff** → this is how UUIDs and secrets get in.
- **Leaving `extra.drupal-site-template`** if the composer.json was edited after exporting.

## Red flags — STOP

- "The secret is from a test environment, it's fine" → **no**, it leaves the repo.
- "The diff is huge, I'll skim it" → that is exactly when leaks get in.
- "I already tested it on my site and it works" → test on a clean install or you have not tested.
