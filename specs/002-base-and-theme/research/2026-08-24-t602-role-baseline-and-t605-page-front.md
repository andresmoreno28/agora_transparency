# T-602 role baseline · T-605 `page.front` — measured on one clean install

**Measured 2026-08-24.** Two orders, one install, no remediation. Every number below was read
from a site installed from scratch in this session; nothing is inferred from a previous run.

> **Two verdicts, and the second one is not the verdict the task row expected.**
>
> **T-602 is RED ON ARRIVAL, confirmed.** `content_editor` — a role Ágora does not create and
> does not name — holds **3** permissions matching `^administer `. The criterion as written
> cannot pass on a clean install of today's `recipe.yml`.
>
> **T-605's premise is REFUTED.** The `/home` alias **does** exist on a clean install, and
> `page.front` is `/page/1` because an upstream event subscriber **deliberately rewrote it on
> apply** — not because the alias was missing. `/home` and `/page/1` are the same page. The
> gap is cosmetic, not functional.

---

## 0. The install these numbers come from

| | |
|---|---|
| Rig | `~/agora-cms` (WSL2 · DDEV) — throwaway rig, not a working copy |
| Recipe applied | `/var/www/html/recipes/agora_transparency` |
| Recipe content | **byte-identical to this repository's `recipe.yml`** modulo line endings — 187 lines both sides, `diff --strip-trailing-cr` clean, 187-byte size gap is CRLF vs LF |
| Repository HEAD | `6e5ca44` |
| Drupal | 11.4.5 · PHP 8.3.19 · Drush 13.7.6.0 |
| AI API key | **none configured** — the install completed anyway |

```bash
ddev drush sql:drop --yes
ddev drush site:install /var/www/html/recipes/agora_transparency \
  --account-name=admin --account-pass=admin --site-name="Agora Clean Smoke" --yes
```

`INSTALL_EXIT=0`. Install tasks ran to `install_finished`, `[success] Installation complete.`

**Post-install extension count: 98 enabled** = 92 modules (41 core, 51 non-core) + 6 themes
(`blank`, `canvas_stark`, `claro`, `easy_email_theme`, `gin`, `stark`).

---

## 1. T-602 — roles × `administer *` on a clean install

**Roles inspected: 4. Granted permissions inspected: 61.**
Denominator for context: the site defines **234** permissions, **67** of which match
`^administer `.

| Role | `is_admin` | Permissions granted | Matching `^administer ` |
|---|---|---|---|
| `anonymous` | FALSE | 3 | **0** |
| `authenticated` | FALSE | 12 | **0** |
| `content_editor` | FALSE | 46 | **3** |
| `administrator` | **TRUE** | 0 | **0** |

### The three offending permissions, and exactly who grants them

| Permission | Granted by | Reaches Ágora via |
|---|---|---|
| `administer menu` | `drupal_cms_content_type_base/recipe.yml:109` | `drupal_cms_privacy_basic` |
| `administer url aliases` | `drupal_cms_content_type_base/recipe.yml:110` | `drupal_cms_privacy_basic` |
| `administer redirects` | `drupal_cms_seo_basic/recipe.yml:46` | listed directly in `recipe.yml` |

Core's own `core/recipes/content_editor_role/config/user.role.content_editor.yml` grants
**zero** `administer *` permissions. All three are added downstream by Drupal CMS recipes:

```yaml
# drupal_cms_content_type_base/recipe.yml:105-111
    user.role.content_editor:
      grantPermissions:
        - 'access content overview'
        - 'access media overview'
        - 'access trash'
        - 'administer menu'
        - 'administer url aliases'
```

```yaml
# drupal_cms_seo_basic/recipe.yml:44-46
    user.role.content_editor:
      # Allow access to the redirect views.
      grantPermission: 'administer redirects'
```

Both `drupal_cms_privacy_basic` and `drupal_cms_seo_basic` are named in Ágora's own
`recipes:` list. **Ágora cannot avoid these three without dropping a recipe it wants.**

### The `administrator` trap a naive test would fall into

`administrator` shows **0 granted permissions** — not because it is clean, but because it
carries `is_admin: TRUE` and therefore holds every permission implicitly, with none stored.
A test that reads `Role::getPermissions()` and asserts "no role except `administrator` holds
`^administer `" would find `administrator` empty **by accident, not by exemption**. If the
`administrator` role were ever changed to grant permissions explicitly instead, that same test
would flip red for the one role it was written to excuse. The test must key on `isAdmin()`,
not on the role ID.

### Cross-check on the second rig

`~/agora-smoke` (4 roles, 70 granted permissions, a `standard`-profile install) reports the
**same three permissions on `content_editor`**. The finding is not an artefact of one install.

### What this means for T-602 — recorded, not fixed

The row already anticipated this and named the only two dishonest exits: strip permissions
from an upstream role (changes the product) or narrow the regex (**automatic 🔴**). Neither was
taken. The measurement supports the row's own proposed resolution — scope the assertion to
**roles this recipe creates**, and record these three as a dated, named exception with the
`file:line` provenance above. That is a ruling for the human, not for this note.

⚠️ **Zero roles inspected would have been a failure.** Four were inspected, and the run
distinguishes itself from a degenerate one by printing the denominators (234 / 67) alongside
the numerators.

---

## 2. T-605 — the `page.front` gap

### Measured values

| Question | Measured |
|---|---|
| `recipe.yml` declares | `page.front: '/home'` (`recipe.yml:125`) |
| `drush config:get system.site page.front` | **`/page/1`** |
| Does the `/home` alias exist on a clean install? | **YES** |
| `getPathByAlias('/home')` | `/page/1` |
| `getAliasByPath('/page/1')` | `/home` |
| `path_alias` entities on a clean install | **2** — `/home` → `/page/1`, `/privacy-policy` → `/node/1` |

### The mechanism — the two references confirmed, and a third one that changes the answer

**Reference 1 — confirmed exactly at the stated lines**, with one material addition:

```php
// drupal_cms_helper/src/GenericConfigurationListener.php:44-47
      // @todo Remove when https://www.drupal.org/i/1503146 is released.
      if ($name === 'system.site' && $this->convertFrontPagePathToAlias) {
        $data['page']['front'] = '/' . ltrim($this->aliasManager->getAliasByPath($data['page']['front'], $data['langcode'] ?? NULL), '/');
      }
```

The addition: the rewrite is **gated on a flag that defaults to FALSE** (line 28,
`public bool $convertFrontPagePathToAlias = FALSE;`) and is switched on in exactly one place,
`drupal_cms_helper/src/SiteExporter.php:97`. So this listener acts **only during
`site:export`**, never during a normal config export.

**Reference 2 — confirmed, line exact.** `path_alias` is item one on the reject list, and the
quoted comment sits at lines 27-28 with the entry itself at line 29:

```php
// drupal_cms_helper/src/ContentLoader.php:26-29
  private static array $reject = [
    // Path aliases are created when the content is, and therefore should not be
    // exported.
    'path_alias',
```

**Reference 3 — not in the task row, and it is the one that actually produces `/page/1`:**

```php
// drupal_cms_helper/src/EventSubscriber/RecipeSubscriber.php:36-44
  public function onApply(RecipeAppliedEvent $event): void {
    // @todo Remove when https://www.drupal.org/i/1503146 is released.
    $config = $this->configFactory->getEditable('system.site');

    $front_saved_path = $config->get('page.front');
    $front_system_path = $this->aliasManager->getPathByAlias($front_saved_path);
    if ($front_system_path !== $front_saved_path) {
      $config->set('page.front', $front_system_path)->save();
    }
```

### What is actually happening

The two listeners are a **deliberate round-trip pair**, both carrying the same
`@todo Remove when https://www.drupal.org/i/1503146 is released.`:

| Direction | Class | Effect |
|---|---|---|
| **apply** | `RecipeSubscriber::onApply()` | declared alias → system path · `/home` → `/page/1` |
| **export** | `GenericConfigurationListener` | system path → alias · `/page/1` → `/home` |

`recipe.yml` declares the portable alias; the installed site stores the resolvable system
path; `drush site:export` converts it back. **The recipe and the site disagree by design, and
the disagreement is self-cancelling across an export.**

### Why the row's stated reasoning is wrong

T-605 reasons: *"the content is exported without its alias, and on a fresh install the alias
does not exist."* Both halves fail against measurement.

The `ContentLoader` reject list excludes standalone **`path_alias` entities**. It does not
strip the `path` field carried **inside** a content entity — and that is where this alias
lives. This repository's only content file declares it outright:

```yaml
# content/canvas_page/ff94a20d-4eee-42f8-9ec7-48ccf940d5ac.yml:21-24
  path:
    -
      alias: /home
      langcode: en
```

So the alias ships with the content, is recreated on every clean install, and `page.front`
lands on `/page/1` because it was **converted**, not because anything was missing.

### Route behaviour

| Route | Status | Follows to |
|---|---|---|
| `/` | **200** | — |
| `/home` | 301 | `/` → **200** |
| `/page/1` | 301 | `/` → **200** |
| `/user/login` | **200** | — |
| `/privacy-policy` | 404 | — (node 1 ships **unpublished** upstream; not a defect) |
| `/definitely-not-a-route` | 404 | — (control: the 404s above are real) |

`/home` is reachable and renders the front page. The 301 to `/` is Drupal's canonical
front-page redirect, not a misconfiguration.

⚠️ **T-605's criterion says `/home` returns 200. Literally it returns 301.** An assertion
written as `assertSession()->statusCodeEquals(200)` on `/home` **will fail** unless it follows
redirects. This is a criterion-wording problem, not a product problem, and it must be settled
before the assertion is written rather than after it goes red.

### Two named options for closing the gap

**Option A — amend the declaration to the system path.**
Change `recipe.yml` to `page.front: '/page/1'`.
*For:* declared value and installed value match exactly; the T-605 assertion becomes trivially
true. *Against:* `/page/1` is an **entity-ID-dependent** path. It is correct only while the
home page happens to be `canvas_page` 1. It is also what `site:export` would immediately
rewrite back to `/home`, so the file would fight the tool on every export. **Not recommended.**

**Option B — keep `/home` and amend the criterion.** *(recommended)*
Leave `recipe.yml` declaring `/home`, and restate T-605's success criterion as what is
actually true and actually worth protecting:
1. the `/home` alias exists after a clean install, and resolves to the same path
   `system.site:page.front` holds — i.e. `getPathByAlias(<declared>) == <installed>`;
2. requesting `/home` reaches a 200 **following redirects**;
3. `/` returns 200 and renders the home page.

This asserts the round trip rather than string equality, survives the entity ID changing, and
does not fight `site:export`. It also keeps the declaration portable, which is the reason the
upstream pair exists at all.

**Neither option is taken here.** The row's words are *"Silence is not an outcome"* — this
note ends the silence; the choice between A and B is the human's.

---

## 3. Divergences found while measuring

1. **The task row and the dispatch both say Ágora applies "eleven upstream recipes".**
   `recipe.yml` lists **ten**: `administrator_role`, `core_recommended_maintenance`,
   `core_recommended_performance`, `drupal_cms_admin_ui`, `drupal_cms_anti_spam`,
   `drupal_cms_authentication`, `drupal_cms_media`, `drupal_cms_privacy_basic`,
   `drupal_cms_seo_basic`, `easy_email_express`. Eleven is right only if Ágora itself is
   counted. Immaterial to both findings, recorded because the number is quoted as fact.
2. **`system.site` on the installed site carries a `_core.default_config_hash`.** Harmless
   here — `GenericConfigurationListener` line 40 strips `_core` on export — but it confirms
   the export path is the only thing standing between the live config and a dirty `config/`.
3. **`drupal_cms_content_type_base` is not in Ágora's `recipes:` list** yet supplies two of
   the three offending grants, reaching the site through `drupal_cms_privacy_basic`. Any
   exception list for T-602 must name the transitive recipe, not just the direct one.
