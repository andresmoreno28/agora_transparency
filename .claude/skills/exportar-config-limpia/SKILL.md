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

⚠️ **THE FLOW ABOVE IS WRONG AND THE CLAIM BELOW WAS FACTUALLY FALSE. Corrected 2026-08-24
under D-032, read at source in `drupal_cms_helper/src/SiteExporter.php` and
`Drush/Commands/SiteExportCommand.php`.**

**`ddev drush site:export` on its own does not update `config/` at all.** `--destination` defaults
to `recipes/site_export`, and the command **refuses to run** if the destination exists without
`--overwrite`. Run as written above it produces a recipe next door that nobody reads.

**The correction that matters most:** this file used to say *"it **adds to** the `require`"*. It
**replaces** it — `$data['require'] = $this->getExtensionRequirements($extensions)`, an assignment,
one `^<installed-version>` per installed extension. That error sat in a signed skill at the exact
moment somebody was about to run the command for the first time, and it is the one most likely to
cause **silent SBOM loss**.

**What else it does, none of it mentioned before, all of it silent:**
- **`recipe.yml` is REGENERATED** from four keys via `Yaml::encode()`. There is **no `recipes:` key
  in the output** — the upstream recipes vanish and their config is inlined — **every comment is
  destroyed**, and `install:` becomes every installed extension rather than the curated few.
- It `unset`s `extra.drupal-site-template`, so an export performs part of a later unit's work by
  accident.
- Where a package version cannot be determined it emits a **raw dev constraint** (`'*'`): direct
  exposure to non-negotiable rule 1.
- `--base` defaults to the starter kit's base recipe and **mirrors its files back in**, returning
  the boilerplate this project deleted.
- It exports **all content** into `content/`.

**The procedure that is actually safe (D-032 = B):**
1. Use a **purpose-built rig** whose `require` is exactly this project's dependency closure. Not a
   general Drupal CMS sandbox — one of ours carries Byte, Haven and half of Drupal CMS's optional
   recipes, and exporting from it produces a template that requires them.
2. **Baseline export FIRST**, before touching anything:
   `drush site:export --destination=<scratch>/baseline --base=<the template checkout> --overwrite`.
   Commit nothing. Everything in it is **not yours**.
3. Model the change, then export again to `<scratch>/after` with the same `--base`.
4. **The artefact is `diff -r baseline after`, never `after`.** Only files that appear or change are
   copied into `config/`.
5. **`recipe.yml` and `composer.json` are never taken from the export.** Read the export's versions
   as *information* and transplant the relevant lines by hand into their area block; every new
   package earns its `DECISIONS.md` line.
6. Discard the export's `content/` wholesale unless demo content is this unit's job.

⚠️ **Why the baseline is not optional.** `_core.default_config_hash` is the marker that says
*"this config came from an extension, not from you"* — **and the exporter strips it**. After an
export, your config is **indistinguishable by inspection** from the upstream recipes' config. The
baseline diff is the replacement for the marker the tool deleted. The table below leads with
`_core` and `uuid` for historical reasons; **those two are the safe ones** — the exporter already
removes them and `RequirementsTest` already asserts they are gone. The dangerous ones are the
three nothing catches: `recipe.yml` silently replaced, content silently exported, and `config/`
silently **empty** — a `FileStorage` over a missing directory lists nothing and every check passes.
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

# Added 2026-08-24 (D-032). The three greps above find what the exporter already removed;
# these four find what nothing else does.
find config -type f | wc -l          # must be printed, and must be > 0
find content -type f | wc -l         # must not have grown
git status --porcelain content/      # must be empty
grep -n '^recipes:' recipe.yml       # the upstream recipe list must still be there
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
