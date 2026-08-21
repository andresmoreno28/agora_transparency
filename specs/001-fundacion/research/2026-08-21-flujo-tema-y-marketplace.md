# Research · Theme flow and publication route

- **Research:** 2026-08-21 [ejecutor]
- **Re-verified at source:** 2026-08-21 [ejecutor], point by point, before writing this
  document. All the literal quotes below were reproduced in this second pass.
- **Triggers:** D-008 (rider suspended), D-012, D-014 — signed by [andres] 2026-08-21.
- **Expires:** by I-001, every statement here is re-verified before building on top.

---

## 1 · Purpose

Close three questions that were blocking unit 001 and that had been answered from memory:

1. Does the starter kit **regenerate** the theme on each end-user installation? (stopping condition
   that [andres] attached to the D-008 rider).
2. Can a site template **contain** its own versioned theme?
3. Is the marketplace **DCP-only and paid**, as had been assumed?

## 2 · Method

Direct consultation of the code and the source pages, never intermediate documentation or memory.

- **Network check first** (I-011: the network in this environment varies within the same session;
  it is assumed neither that there is one nor that there is not):
  `curl -s -o /dev/null -w "%{http_code}" https://git.drupalcode.org/api/v4/projects/project%2Fagora`
  → `200`. Network available; proceed.
- Source code via `raw` from git.drupalcode.org; name availability via the **GitLab API**;
  release history via `updates.drupal.org`; marketplace pages via `curl -L` + text extraction.
- Criterion: a statement only enters here if the **literal quote** or the **exact value** was reproduced.

## 3 · Findings

### H-1 · The theme is generated ONCE, in the author's working site — not on every installation
Source: `https://git.drupalcode.org/project/site_template_helper/-/raw/1.x/src/Plugin.php`

The `generateTheme()` method builds the `.info.yml` path under the **Drupal root of the working
site** (`$drupal_root/themes/<name>/<name>.info.yml`, lines 117-123) and returns without doing anything
if it already exists (lines 124-126):

```php
// If the theme was already generated, leave it alone.
if (file_exists($info_file_path)) {
  return;
}
```

That is: **idempotent**, and it writes **in the working Drupal site, not inside the recipe
package**. It is the author's development scaffolding.
→ **The stopping condition of the D-008 rider is not triggered.**

### H-2 · The `extra.drupal-site-template` block is deleted before publishing — said by the kit itself
Source: `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/composer.json`

The block carries its own self-explanatory `_comment`, quoted literally:

> "This section contains configuration used by drupal/site_template_helper to assist developers in
> creating site templates. It will be automatically removed by `drush site:export`, but you should be
> sure to delete this section before publishing your site template."

In that same file, the theme it generates is called `blank`, with `"from": false` (empty theme, with no
source starterkit). It agrees with `GET-STARTED.md` line 18: *"An empty theme, called `blank`,
which you can customize however you want (or uninstall completely…)"*.

### H-3 · A site template CANNOT contain its own code — a kit test verifies it
Source: `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/tests/src/Kernel/RequirementsTest.php`

Lines 30-31, literal:

```php
$finder = Finder::create()->in($path)->files()->name('*.info.yml');
$this->assertCount(0, $finder, "Recipes cannot include any code (modules or themes) of their own; they must list them as dependencies in `composer.json`.");
```

**Zero `*.info.yml` files in the whole package.** It is not a convention: it is an assert that runs in the gate.

### H-4 · The package is installed OUTSIDE the docroot
Source: `https://git.drupalcode.org/project/drupal_cms/-/raw/2.x/project_template/composer.json`
(the `drupal_cms` 2.x repo is a monorepo: **there is no `composer.json` at the root**; the one for the
installable project — package **`drupal/cms`**, verified in the `name` field — lives in `project_template/`.)

`extra.installer-paths`, exact and relevant values:

```
'web/themes/contrib/{$name}'  <- ['type:drupal-theme']
'./recipes/{$name}'           <- ['type:drupal-recipe']
```

A recipe (`type: drupal-recipe`, which is what Ágora is) lands in `./recipes/<name>`, **a sibling
of `web/`, not inside it**. Drupal does not scan extensions there: `RecursiveExtensionFilterCallback` only
recurses into the root's `profiles/`, `modules/` and `themes/`. A theme placed inside the package would be
invisible even if the H-3 test did not exist.

### H-5 · The official ADR contemplates depending on a theme, not including it
Source: `https://git.drupalcode.org/project/drupal_cms/-/wikis/Architecture-Decision-Records/Site-Templates.md`

Literal:

> "They MAY depend on a theme (or multiple themes) as design systems – libraries of components, and
> their associated styles, for building out the look in Canvas."

The same ADR establishes that MUST/SHOULD/MAY are read according to RFC 2119.

### H-6 · `CI_ALLOW_DEV` is an escape hatch — and it explains why the "coupled theme" pattern exists
Source: the same `RequirementsTest.php`, lines 54-55:

```php
// latest commit of a bespoke theme to which it is strongly coupled.
$allow_dev = getenv('CI_ALLOW_DEV');
```

The comment describes the use case: testing the template against the latest commit of *"a bespoke
theme to which it is strongly coupled"*. It confirms that **a coupled theme as a separate project is the
intended pattern**, and at the same time that the variable **weakens the version gate** (→ I-015, T-209).

### H-7 · Machine name: the only valid oracle is the GitLab API
Source: `https://git.drupalcode.org/api/v4/projects/project%2F<X>`

| Candidate | HTTP | Reading |
|---|---|---|
| `agora` | **200** | taken by an unrelated project |
| `agora_transparency` | **404** | free |
| `agora_gov` | **404** | free |
| `agora_theme` | **404** | free (new fact, relevant to rider (c) of D-014) |

⚠️ `www.drupal.org/project/<X>` returns a **302 towards new.drupal.org for any string**, including
non-existent ones: a 302 **does not prove availability** (→ I-012).

Concordant note from `GET-STARTED.md` line 64: *"don't prefix its name with `drupal_cms_` or
`drupal-cms-` … it is not part of Drupal CMS"*. `agora_transparency` complies.

### H-8 · The marketplace is open to individuals for free templates
Source: `https://new.drupal.org/site-template/apply`, section "Who can apply". Literal:

> "Individuals — Free templates: Any individual who wants to submit free templates, is welcome to."
> "For paid templates: Drupal Certified Partners, with a history of contribution."

Being a DCP/Ripplemaker is a requirement **only for paid templates**. The "DCP-only pilot" premise
was **false**. Furthermore: **no fee appears** (`395`/`250`) on the page. The figure comes from the
July 2025 *proposal* (`https://www.drupal.org/project/innovation_ideas/issues/3532934`), which says
literally **"(none for pilot and MVP)"**.

Complement:
`https://www.drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates`
→ *"All free site templates, including marketplace templates, are general projects for packaging and
distribution purposes"*, with an explicit recommendation of *"sharing a community template first"*.

### H-9 · `release-history` gives a false green by status code
Source: `https://updates.drupal.org/release-history/<X>/current`

Reproduced with a non-existent project:

```
$ curl -s -w "%{http_code}" .../release-history/agora_no_existe_xyz/current
HTTP 200
<?xml version="1.0" encoding="utf-8"?>
<error>No release history was found for the requested project (agora_no_existe_xyz).</error>
```

**HTTP 200 with an `<error>` body.** A `curl -f` or any status-based check gives green on a
project that does not exist. The positive control (`config_guardian`) does return `<title>Config Guardian</title>`
and `<supported_branches>`. → I-013, and amendment of the `sbom-check` method (T-306).

## 4 · Conclusions

1. **The theme is not regenerated on the end user's installation.** It is generated only once, in the
   author's working site, idempotently, and its configuration block is deleted before publishing
   (H-1, H-2).
2. **A site template cannot contain its own code.** Zero `*.info.yml`, verified by a test from the
   kit itself, and moreover the package lives outside the docroot where Drupal does not even scan (H-3, H-4).
   Themes and modules are **declared in `require`** (H-5).
3. **The marketplace is open to individuals for free templates and there is no confirmed fee for
   the MVP**; free templates are, in any case, general projects, and it is recommended to publish
   first as community (H-8).
4. **The valid oracle for the machine name is the GitLab API**, not `www.drupal.org/project/<X>` (H-7);
   and `updates.drupal.org` requires parsing the XML, not looking at the status (H-9).

## 5 · Which decisions it triggers

| Decision | Effect |
|---|---|
| **D-008** | Rider "theme committed in the repo" **SUSPENDED due to technical impossibility** (H-3, H-4). The negative rule survives: this repository contains no theme of its own. Subsumed by D-014. |
| **D-012** | Community first, marketplace afterwards. Closes pending item (c) of D-011 about the fee (H-8). |
| **D-014** | The theme is a **separate project** (`drupal/agora_theme`), declared in `require` (H-3, H-4, H-5, H-6). H-7 additionally contributes that `agora_theme` is free. |
| **D-007** | `agora_transparency` (H-7). |

Derived idioms: I-011 (network), I-012 (name oracle), I-013 (release-history false green),
I-014 (no code of its own), I-015 (`CI_ALLOW_DEV`), I-016 (unverified alarming premise),
I-017 (licence and privacy as structural constraints).

## 6 · Expiry

By **I-001**, this document describes the state of the art as of **2026-08-21**. Drupal CMS, Canvas and
the site template programme are young technology: before building on any finding from here
—very especially H-8, which depends on an evolving policy— it is re-verified at source.
