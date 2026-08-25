# Unit 003 · How demo content actually ships, and what it costs

> Dated research, [ejecutor] 2026-08-26, for the unit 003 scaffolding turn.
> **Prior is not disk (I-001): this expires.** Every claim below was read at source today,
> in Drupal core **11.4.5** on the `~/agora-cms` rig and in the two published site templates
> installed there. Re-measure before quoting after 2026-09-26.
>
> Sources are file paths with line numbers or commands with their output. Where something was
> **not** measured it is listed in §8 rather than left to be assumed.

## 1 · The mechanism, read at source

`content/` is **not** wired up by anything in `recipe.yml`. It is discovered by directory.

- `web/core/lib/Drupal/Core/Recipe/Recipe.php:96` —
  `$content = new Finder($path . '/content');`
  The directory is passed to `\Drupal\Core\DefaultContent\Finder` unconditionally, at
  `Recipe::createFromDirectory()`. A missing directory is caught
  (`Finder.php:41`, `DirectoryNotFoundException` → `$this->data = []`) and is not an error.

- ⚠️ **`recipe.yml` accepts a `content:` key that does nothing.** `Recipe.php:307` declares
  `'content' => new Optional([new Type('array')])` in the validation constraints, and
  `Recipe.php:330` defaults it to `[]` — but **no code path reads `$recipe_data['content']`.**
  The `Finder` is built from the path, never from the key. Verified: neither `haven` nor `byte`
  declares the key (`grep -n "^content" recipe.yml` → no match in either).
  **Consequence: do not add a `content:` key to `recipe.yml`. It would validate, look load-bearing,
  and mean nothing** — the exact shape of a false green this project keeps catching.

- `RecipeRunner.php:38-43` fixes the order, and the order is what makes multilingual possible:
  ```php
  static::processRecipes($recipe->recipes);
  static::processInstall($recipe->install, $recipe->config->getConfigStorage());
  static::processConfiguration($recipe);
  static::processContent($recipe->content);
  ```
  Sub-recipes → modules and themes → config actions → **content, last**. So a language declared in
  `config/` and a module declared in `install:` are both in place before the first entity is created.

- `RecipeRunner.php:150-154` — content is imported with `Existing::Skip`:
  ```php
  $importer->importContent($content, Existing::Skip);
  ```
  An entity whose UUID already exists is **skipped silently**. Not a problem on a clean install;
  it is a problem for any re-apply-based test, which will pass having imported nothing.

## 2 · The file format, and the layout the published templates use

`Finder.php:33-40` globs `*.yml` and `*.json` **recursively** under `content/`. Every file must
carry `_meta.uuid` (`Finder.php:56`, `ImportException` otherwise). Ordering is a topological sort
over `_meta.depends: {<uuid>: <entity_type>}` (`Finder.php:69-88`), so authoring order is irrelevant
and dependency order is declared, not implied by filename.

Measured layout of the two published site templates on the rig:

| | `haven` | `byte` |
|---|---|---|
| content files | **128** | **85** |
| `.yml` | 104 | — |
| binaries | 22 `.jpg` · 1 `.png` · 1 `.svg` | — |
| directories | `canvas_page` `crop` `easy_email` `file` `media` `menu_link_content` `node` `taxonomy_term` | same minus `easy_email` |
| files carrying `translations:` | **0** | **0** |
| `du -sh content` | **75M** | **8.8M** |

The layout is `content/<entity_type>/<uuid>.yml`, and **binaries sit inside `content/file/`
alongside the `file` entity YAML that names them**. Confirmed both directions:

- import — `Importer.php:158-160`:
  `$source = $path . '/' . basename($destination);` where `$path = dirname($yaml_path)`.
  If the binary is absent the importer logs a **warning and continues**
  (`Importer.php:161-165`): *"File entity %name was imported, but the associated file (@path) was
  not found."* **A missing binary is not an error.** It is a warning nobody reads, and the install
  goes green.
- export — `Exporter.php:143`:
  `$this->fileSystem->copy($from, $destination . '/' . $to, FileExists::Replace);`

## 3 · The authoritative producer of `content/` — and it is NOT `site:export`

D-032 named `drush site:export` and ruled (step 6) that *"the export's `content/` is **discarded
wholesale** in unit 002."* That ruling self-scopes to unit 002 and does not bind this one, but it
also leaves unit 003 with **no recorded producer for `content/`**. Read at source today:

`web/core/lib/Drupal/Core/DefaultContent/Command/ContentExportCommand.php:30-31, 49-59`

```
drush content:export <entity_type_id> [<entity_id>]
  -W, --with-dependencies   Recursively export all referenced entities into a directory structure
  -b, --bundle=BUNDLE       Only export entities of the specified bundle(s) (multiple allowed)
  -d, --dir=DIR             The path where content should be exported
```

- `--dir` is **mandatory** whenever more than one entity, or one entity with dependencies, is
  exported (`ContentExportCommand.php:96-98`, `RuntimeException`).
- `--with-dependencies` is what produces the `content/<entity_type>/<uuid>.yml` layout §2 measured,
  via `Exporter::exportWithDependencies()` (`Exporter.php:160-173`), and it is what copies binaries.
- The command **returns a count** of entities exported and errors when a named entity produced zero.

**This is a different command from `site:export` and it carries none of D-032's hazards:** it does
not touch `recipe.yml`, does not rewrite `composer.json`, does not `unset` `extra.*`, does not
mirror in a base recipe. D-032's baseline-diff apparatus exists because `site:export` strips
`_core.default_config_hash` and makes our config indistinguishable from upstream's. **No such
ambiguity exists for content**: every entity in `content/` is one we created, and the
denominator is `find content -type f | wc -l`, which is currently **1**.

⚠️ **D-032 step 7's fourth pre-commit check is `find content -type f | wc -l` still `1`.**
Unit 003 is the unit that legitimately makes that false. It must be **replaced by name** in the
commit that first breaks it — not deleted, and not left to fail.

## 4 · Multilingual: what it takes, and the silent failure hiding in it

### 4.1 · The mechanism

`Importer.php:257-264`:
```php
foreach ($data['translations'] ?? [] as $langcode => $translation_data) {
  if ($this->languageManager->getLanguage($langcode)) {
    $translation = $entity->addTranslation($langcode, $entity->toArray());
    foreach ($translation_data as $field_name => $values) {
      $this->setFieldValues($translation, $field_name, $values);
    }
  }
}
```

🔴 **Read the `if`.** A translation whose langcode is not a configured language on the site is
**dropped silently** — no exception, no warning, no log line, no counter. `content/` could ship
two hundred Spanish translations and, if `es` is not configured at the moment content is imported,
all two hundred vanish and the install smoke goes green. This is `I-028`'s degenerate case with a
new costume: the expected state (*"no errors"*) and the total-failure state produce identical
output. **It gets its own idiom number in wave 9 and its own dirty case.**

### 4.2 · The installer-language branch, which is subtler

`Importer.php:381-408`, `verifyNormalizedLanguage()`:
```php
if (!$this->languageManager->getLanguage($default_langcode)
    || (InstallerKernel::installationAttempted() && $default_language->getId() !== $default_langcode)) {
```
A site template is applied **during installation**, so `installationAttempted()` is TRUE. If a user
installs Drupal CMS **in Spanish** while our entities declare `default_langcode: en`, the second
clause fires: core walks our `translations`, finds `es` configured, **promotes the ES values into
`default`, and unsets the ES translation** (`:395-402`). The node then exists **in Spanish only** —
the English is gone. That is core behaving as documented, not a defect, but it means
**"the demo is bilingual" is false for a Spanish-language install**, and a test asserting two
translations would fail there for a reason nobody would guess.

### 4.3 · The configuration cost

To have `es` exist at content-import time the template must ship:

| Artefact | Where | Note |
|---|---|---|
| `language`, `content_translation` in `install:` | `recipe.yml` | **Core modules** — like `datetime_range`, `views`, `content_moderation` already there, they add nothing to `composer.json` and need **no SBOM line** (rule 2 precedent, `recipe.yml` comments) |
| `language.entity.es` | `config/` | The `es` language itself |
| `language.types` / `language.negotiation` | `config/` | How a visitor reaches the Spanish version — URL prefix is the only method that gives a URL a person can link to, a crawler can index and a test can request |
| `language.content_settings.node.agora_base_*` × 6 | `config/` | Per-bundle translatability |
| `translatable: true` on the prose fields | `config/field.field.node.*.yml` | **Touches files unit 002 exported.** Must go through the D-032 export rig; hand-editing them is a modelling change made by hand |

`config/` goes from **102** files to roughly **115**. The number is measured in wave 10, not
predicted here.

### 4.4 · Precedent, measured

**Neither published site template ships a single translation.** `haven`: 0 files containing
`translations:` out of 104 YAML. `byte`: 0. Ágora would be the first, which is a differentiator and
a risk in the same sentence: there is no worked example to copy and no reviewer expectation to meet.

## 5 · The D-033 tension, stated precisely

D-033 (signed by [andres] 2026-08-24) rules that **shipped config strings are English**, that the
Spanish arrives as a translation from localize.drupal.org, and — in its own ⚠️ — that
**"four independent locks stop those translations reaching an installed site,"** the sharpest being
that a site template's config carries no `_core.default_config_hash`, which
`LocaleConfigManager::isSupported()` requires **and our own `RequirementsTest` forbids**
(`tests/src/Kernel/RequirementsTest.php:138`, `assertArrayNotHasKey('_core', $data, …)`).

Non-negotiable rule 6 says **"Demo content stays bilingual ES/EN."**

Both are true, and together they produce **a page that is half Spanish**:

| On a Spanish demo page | Language | Why |
|---|---|---|
| Node title, summary, body | **ES** | Content entity translation — works |
| Taxonomy term names | **ES** | Content entity translation — works |
| Menu link titles | **ES** | `menu_link_content` is a content entity — works |
| Field labels — *Award amount*, *Counterparty*, *Remuneration* | **EN** | `field.field.*` config, English by D-033, translation unreachable |
| View titles — *Document library* | **EN** | `views.view.*` config, same |
| Exposed filter labels, facet headings | **EN** | same |
| Pager — *Next page*, *Last page* | **EN** | Core interface strings; reachable only if `locale` downloads from localize.drupal.org at install time |
| Node type names on the add-content form | **EN** | same |

**This is not a stylistic problem, it is a WCAG one.** WCAG 2.2 **SC 3.1.2 Language of Parts (AA)**
requires that the language of every passage differing from the page's language be programmatically
determinable. A page whose `<html lang="es">` contains a dozen unmarked English labels **fails 3.1.2**.
The ROADMAP's development point 7 — *"correct `lang` per fragment"* — is exactly this obligation,
and D-033 has made it an order of magnitude larger than it looked when that line was written on
2026-08-20, because it is now every config-sourced string on every Spanish page, not a stray quote.

**No theme template does this today.** `agora-theme/templates/` holds `field.html.twig`,
`table.html.twig`, `menu.html.twig`, `node.html.twig`, `pager.html.twig`, `page.html.twig`,
`form-element.html.twig`, `views-view-table.html.twig` — and none emits a `lang` attribute on a
label. This is theme work, in the theme repository, and it is not free.

**This is D-035 and it is [andres]'s.** Framed with options in the open-questions list; not decided
here, because it trades a signed decision (D-033) against a signed non-negotiable (rule 6).

## 6 · `RequirementsTest` and content — measured, and the answer is clean

`tests/src/Kernel/RequirementsTest.php:31`:
```php
$this->assertCount(0, $finder, "Recipes cannot include any code (modules or themes) of their own…");
```
The finder globs `*.info.yml`. Content files are named `<uuid>.yml` by both the exporter
(`Exporter::exportToFile`) and the two published templates, so **no content file can collide with
`*.info.yml` unless someone names one by hand**, and `tests/bin/no-code-in-template` already
catches that in **both** scopes it scans — the packaged tree
(`git archive --format=tar HEAD | tar -tf -`) and the working tree (`find`).

✅ **Nothing about content export violates the zero-`*.info.yml` rule, and the guard already
covers it.** No new invariant is needed for this. What *does* change is the invariant's
**denominator**: the packaged entry count was **116** at T-703 and will rise by every content file
and every binary. That number is re-measured, not carried (I-045).

⚠️ One thing content export **can** break that nothing watches: `.gitattributes` marks
`/tests export-ignore` but **not** `/content`, which is correct — content must ship. The risk is
the inverse: a binary added to `content/` is packaged into the tarball a marketplace reviewer
downloads. `haven`'s content is **75 MB**. Package weight is a "sober" property, and it needs a
stated ceiling, which is a task criterion in wave 10.

## 7 · Media licensing — what the precedent actually is

Measured, not assumed: **neither published site template documents its media licences at all.**

- `haven` ships 22 `.jpg` files whose filenames literally end `-unsplash.jpg`
  (`matt-palmer-K5KmnZHv1Pg-unsplash.jpg`, 5,737,069 bytes, and 21 more). The only licence file in
  the package is `LICENSE.txt` (GPL, for the code). There is no credits file, no manifest, no
  per-image attribution.
  `grep -ril "licence\|license\|unsplash\|CC0\|public domain" --include="*.md" --include="*.txt" .`
  → `./LICENSE.txt` only.
- `byte` — identical situation.

Two conclusions, both worth having:
1. **The marketplace review evidently did not require a media manifest.** So this is not a
   publication blocker; it is a standard Ágora holds itself to. Worth [andres] knowing before he
   pays for it.
2. **Ágora's whole thesis is "auditable — every piece of the SBOM justified".** A transparency
   portal that ships 20 images of unstated provenance is arguing against itself in its own
   screenshot. The mechanical check is specified in the plan (§6) as `tests/bin/media-licence`.

## 8 · NOT MEASURED — recorded so nobody quotes it as settled

- Whether `locale` can download Spanish interface translations during a **site-template install**
  with no network, and what happens when it cannot. Untested. It is the difference between
  "English chrome" and "English chrome plus an install-time error", and it is wave 9's probe.
- Whether the Drupal CMS installer offers a language choice **before** the site template is applied,
  and therefore whether §4.2's promote-and-unset branch is reachable in practice for our users.
- Whether `drush content:export --with-dependencies` exports **`crop` entities** (both published
  templates ship a `content/crop/` directory with one entity per image; nothing read today explains
  what creates them or whether they are required).
- Whether Nightwatch in the **theme** repository can be pointed at a site built from the **template**
  package, which is the mechanical question underneath D-036.
- Any claim about `~/agora-smoke`'s current state. It is a throwaway rig and CLAUDE.md requires it
  to be **rebuilt**, not pulled, before any clean-install claim.
