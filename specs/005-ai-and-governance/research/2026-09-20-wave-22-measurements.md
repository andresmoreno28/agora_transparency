<!--
  cspell:ignore dropai litellm yethee tiktoken Ovidijus Parsiunas

  Seven identifiers quoted VERBATIM from third-party source in this file, scoped here rather
  than declared in .cspell-project-words.txt, because this wave's dispatch limited writing to
  specs/005-ai-and-governance/research/. Each survives D-024(3)'s question, and `cspell:ignore`
  is used in preference to a cspell:disable/enable block on purpose: it exempts these seven
  words by name and leaves every other word in the file checked, where a disable block would
  stop checking the prose around the quotation too.

    dropai     - the `dropai_provider` submodule of drupal/ai 1.4.9. A row of the lifecycle
                 table, which is only a denominator if it is complete.
    litellm    - `$trialAccessKey->litellm_token`, quoted from ai_provider_amazeeio 1.4.3
                 src/TrialAccess/TrialAccountProvisioner.php:90.
    yethee     - the composer vendor of `yethee/tiktoken`, quoted from drupal/ai 1.4.9's
    tiktoken     composer.json require block.
    Ovidijus   - the copyright holder named in modules/ai_chatbot/deepchat/LICENSE. Quoting a
    Parsiunas    copyright line and altering the name would defeat the purpose of quoting it.

  RECOMMENDED FOLLOW-UP: these belong in .cspell-project-words.txt as justified lines, next to
  the `milvus`/`deepchat`/`amazee` entries unit 005 scaffolding already added. They are scoped
  here only because this file's author could not write that file.
-->

# Unit 005 · Wave 22 measurements, 2026-09-20

**This file amends `2026-09-19-ai-and-governance-state-of-the-art.md`. It does not replace it.**
That file's structure — *answered at source* kept apart from *must be measured on a rig* — is its
most valuable property, and it is preserved here rather than blurred.

## How to read this file, and what it is honest about

Every row below carries one of exactly three labels, and the difference between them is the point:

| label | means |
|---|---|
| **MEASURED** | a command was run and its output is quoted, with the command |
| **READ AT SOURCE** | a file was read at a named tag, quoted with its line number. **It proves what the code says, never what the code does.** |
| **NOT MEASURED — needs a rig** | nothing was run. The exact command sequence is given so the row becomes a short job the day a rig is free. |

⚠️ **No Drupal rig was available for this wave.** Docker was off-limits for the whole of it, by
standing instruction. **No row below claims a runtime observation, and none of the three decisions
this wave feeds is treated as settled by a reading.** Where a reading is decisive *about the
source* and still silent *about the behaviour*, it says so in the row.

**Method, so the numbers can be re-derived rather than trusted.** Release status came from
`updates.drupal.org/release-history/<project>/current`, parsed rather than eyeballed. Source came
from `git.drupalcode.org/project/<p>/-/archive/<tag>/<p>-<tag>.tar.gz`, extracted and read
locally, so every path below is a real path inside a real published tag. Both endpoints answer
anonymously; the API's `/trace` endpoint does not, which is a separate limitation and is not
relied on here.

---

## Release status, re-read 2026-09-20

Re-read rather than carried from yesterday, because a release status is a claim with an expiry
date. **One line in yesterday's set has changed meaning and it matters — see `project_browser`.**

| project | releases | newest | newest STABLE | `security covered` | core |
|---|---|---|---|---|---|
| `ai` | 104 | `1.5.0-rc4` | **`1.4.9`** | `1` | `^10.5 \|\| ^11.2` |
| `search_api` | 69 | `8.x-1.41` | **`8.x-1.41`** | `1` | `^10.3 \|\| ^11` |
| `config_guardian` | 4 | `1.0.3` | **`1.0.3`** | `1` | `^10.5 \|\| ^11 \|\| ^12` |
| `key` | 32 | `2.0.0-alpha1` | **`8.x-1.22`** | `1` | `^9.1 \|\| ^10 \|\| ^11` |
| `project_browser` | 33 | `2.1.4` | **`2.1.4`** | `1` | `^11.2 \|\| ^12` |
| `easy_encryption` | 16 | `1.0.4` | **`1.0.4`** | `1` | `^11.2` |
| `ai_provider_amazeeio` | 64 | `1.4.3` | **`1.4.3`** | `1` | `^10.4 \|\| ^11` |
| `ai_provider_openai` | 23 | `1.3.0-beta3` | **`1.2.5`** | `1` | `^10.3 \|\| ^11` |
| `ai_provider_anthropic` | 17 | `1.3.0-beta2` | **`1.2.2`** | `1` | `^10.3 \|\| ^11` |
| `ai_agents` | 38 | `1.4.0-beta1` | **`1.3.5`** | `1` | `^10.3 \|\| ^11` |
| `drupal_cms_ai` | 22 | `2.1.2` | **`2.1.2`** | `1` | — |

🔴 **`project_browser` IS STABLE AND COVERED, AND THE OPEN QUESTIONS FILE SAYS IT IS BETA.**
`open-questions.md` prices D-057 option C on *"`recommended.yml` needs `project_browser`, which is
**beta***", and lists *"`project_browser` at beta"* among the five things that need [andres]'s
signature as a rule-1 exception. **Measured: `2.1.0` went stable at epoch `1760620953`, and the
newest stable is `2.1.4`, `covered="1"`.** Five stable releases precede today. **No rule-1
exception is required and no signature is needed for it.** The claim was already stale the day it
was written, which is the ordinary failure mode of a release status copied forward.

---

## T-0505 / R3 / R13 — the lifecycle audit · **MEASURED (reading) + an absence proven**

This is the row the dispatch called the one that matters most, and the measurement is **four times
larger than yesterday's note recorded.**

### The denominators, printed

```
PROJECTS EXAMINED:               4       (ai 1.4.9, search_api 8.x-1.41,
                                          config_guardian 1.0.3, key 8.x-1.22)
INFO.YML FILES EXAMINED:         47      (shipping extensions: 21, test fixtures: 26)
FILES UNREADABLE:                0
FILES CARRYING A lifecycle KEY:  9
FILES WITH NO lifecycle KEY:     38
```

N = 47 and N > 0. The split between shipping extensions and test fixtures is kept because a
fixture's lifecycle is not a fact about the package a site installs.

### The table · module · `lifecycle` · verdict

Test fixtures excluded. **Verdict column uses core's own semantics, not ours — see the next block.**

| project | machine name | `lifecycle` | verdict |
|---|---|---|---|
| `ai` 1.4.9 | `ai_search` | **experimental** | 🔴 experimental |
| `ai` 1.4.9 | `ai_content_suggestions` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai_eca` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai_external_moderation` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai_logging` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai_translate` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai_validations` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `field_widget_actions` | **deprecated** | 🔴 deprecated |
| `ai` 1.4.9 | `ai` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_api_explorer` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_assistant_api` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_automators` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_chatbot` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_ckeditor` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `ai_observability` | (absent) | ✅ stable by default |
| `ai` 1.4.9 | `dropai_provider` | (absent) | ✅ stable by default |
| `search_api` 8.x-1.41 | `search_api` | **stable** (explicit) | ✅ stable |
| `search_api` 8.x-1.41 | `search_api_db` | (absent) | ✅ stable by default |
| `search_api` 8.x-1.41 | `search_api_db_defaults` | (absent) | ✅ stable by default |
| `config_guardian` 1.0.3 | `config_guardian` | (absent) | ✅ stable by default |
| `key` 8.x-1.22 | `key` | (absent) | ✅ stable by default |

**Per project:**

| project | shipping extensions | non-stable | share |
|---|---|---|---|
| **`ai` 1.4.9** | **16** | **8** | **exactly half** |
| `search_api` 8.x-1.41 | 3 | 0 | — |
| `config_guardian` 1.0.3 | 1 | 0 | — |
| `key` 8.x-1.22 | 1 | 0 | — |

⚠️ **Yesterday's file recorded two of these eight** (`ai_search`, `ai_logging`). The other six were
not wrong, they were **unlooked-for** — the note was written from two targeted reads rather than
from a walk of the package, and a targeted read cannot produce a denominator. **This is the
difference the row's "N examined, N > 0" criterion exists to force.**

### "Absent means stable" is core's rule, not an assumption

Verified in core rather than assumed, because the whole table's `(absent)` column rests on it.
`core/lib/Drupal/Core/Extension/ExtensionLifecycle.php` on `11.x`:

> ```php
>   /**
>    * Extension is stable. This is the default value of any extension.
>    */
>   const STABLE = 'stable';
> ```

And mechanically, in `core/lib/Drupal/Core/Extension/InfoParserDynamic.php:110`:

```php
$parsed_info += [ExtensionLifecycle::LIFECYCLE_IDENTIFIER => ExtensionLifecycle::STABLE];
```

The same class documents what the two values we found actually mean, which is worth quoting because
they are not the same severity:

> `EXPERIMENTAL` — *"Extension is experimental. Warnings will be shown if installed."*
> `DEPRECATED` — *"Extension is deprecated. Warnings will be shown if still installed."*
> `OBSOLETE` — *"Extension is obsolete and installation will be prevented."*

**Neither experimental nor deprecated blocks installation. Both produce a visible warning on the
site's own status report** — which, on a transparency portal whose thesis is auditability, is a
product problem before it is a technical one.

### R13 · The security-advisory clause · **AN ABSENCE, PROVEN**

⚠️ **There is no clause. That is the finding, and it is stated as an absence rather than filled in
by inference, exactly as the row demands.**

**Source 1 — the authoritative contrib policy.**
`https://www.drupal.org/drupal-security-team/security-advisory-process-and-permissions-policy`
("Security advisory process and permissions policy", last updated **1 April 2025**). Extracted to
text and counted: **the word "experimental" occurs 0 times in the whole page.** The page has a
section headed *"Which Releases Get Security Advisories?"* and its contributed-projects clause reads,
verbatim:

> *"Security advisories are only made for issues affecting stable releases (Y.x-Z.0 or higher /
> X.Y.0 or higher) in the supported major version branches. That means no security advisories for
> development releases (-dev), alphas, betas, or release candidates."*

and its *"Which Projects are Covered?"* clause reads, verbatim:

<!-- cspell:disable -- verbatim quotation of drupal.org policy text; "grey" is the source's own
     spelling and correcting it inside quotation marks would falsify the quotation. -->
> *"Covered contributed projects have a grey shield icon and "Stable releases for this project are
> covered by the security advisory policy." note on their project page."*
<!-- cspell:enable -->

**The unit of coverage the policy speaks in is the RELEASE, and its exclusion list is exhaustive
and short**: non-stable releases; external libraries not hosted on Drupal.org; exploits requiring
advanced permissions; issues that cannot be exploited. **A submodule's `lifecycle` is not among
them, and is not mentioned anywhere.**

**Source 2 — the core experimental policy, which does state the principle, for core.**
`https://www.drupal.org/core/experimental` ("Experimental modules and themes in Drupal core", last
updated **9 September 2026**), under *"Stability levels" → "Beta"*, verbatim:

> *"Once the module or theme is in a tagged release of core, security issues with it should be
> reported privately using the normal security reporting process. They have security coverage
> because they have been included in a tagged stable release of core, which has security
> coverage."*

**What this does and does not settle.** It states plainly that coverage is inherited from the
**tagged stable release**, not held by the module. Read across to contrib, `ai_search` inside
`ai` 1.4.9 would be covered. **But reading it across is an inference and is labelled as one**, for
two concrete reasons: the clause is about core, and it sits under a **Beta** heading — a
core-release-cycle stability level with **no counterpart in the contrib `lifecycle:` key**, whose
only four values are `experimental`, `stable`, `deprecated`, `obsolete`.

**Conclusion, as an absence:** *no clause was found stating whether an experimental submodule of a
covered stable contrib release is itself covered.* The literal text of the contrib policy neither
excludes it nor names it. **This is the acceptable finding the row anticipated, and it is not
upgraded into an answer.**

### The finding about our own gate, re-verified

```
$ grep -rn "lifecycle" tests/bin/
(no output)
```

**Still nothing. Not one invariant in this repository reads that key**, while the candidate
dependency for this unit carries eight non-stable declarations. `tests/bin/sbom-check` asks the
project-level question and answers it correctly; the gap is one level down and remains invisible.
T-0513 is the row that closes it, and this audit gives it a falsification target that is real
rather than synthetic: **over `ai` 1.4.9 it must find 8, not 1.**

---

## T-0508 / R14 / R18 — licence and precedent · **MEASURED (reading), both halves**

### R14 · The deep-chat licence

**`modules/ai_chatbot/deepchat/LICENSE` at tag 1.4.9, read in full:**

- **Licence: MIT.** **SPDX identifier: `MIT`.**
- Copyright line, verbatim: `Copyright (c) 2024 Ovidijus Parsiunas`
- **GPL-2.0-or-later compatibility: MIT is compatible.** It is permissive and one-way compatible
  into GPL, so bundling it inside a GPL-licensed Drupal project raises no conflict.

⚠️ **The row asks for "its `package.json`", and there is no `package.json`.** The directory contains
**exactly two files**:

```
LICENSE            1,075 bytes
deepchat.bundle.js 361,371 bytes
```

**So the bundled version cannot be read from a manifest, because the manifest is not shipped.**
That is a real gap for the licence manifest — a minified 361 KB third-party bundle whose upstream
version is not recorded anywhere in the package. It is named here rather than guessed at. The
upstream project is `deep-chat` by the copyright holder above; establishing *which release* this
bundle is would need a hash comparison against upstream releases, which is a separate small job
and is **not** done here.

### R18 · Do the two published site templates ship AI configuration?

Both read at their newest stable tag, `1.0.3`. **Deny list of 19 machine-name terms, printed in the
run** (`ai_`, `_ai`, `ai.settings`, `deepchat`, `openai`, `anthropic`, `amazee`, `llm`, `ai_agents`,
`ai_assistant`, `ai_chatbot`, `ai_search`, `ai_provider`, `ai_logging`, `ai_dashboard`,
`ai_image_alt_text`, `canvas_ai`, `easy_encryption`, `key.key.`), matched against both filenames and
file bodies.

| template | **config objects examined** | AI-related by filename | AI-related by body | AI in `recipe.yml` | AI in `composer.json` | AI in `recommended.yml` |
|---|---|---|---|---|---|---|
| `haven` 1.0.3 | **613** | **2** | **2** | **none** | **none** | **yes** |
| `byte` 1.0.3 | **559** | **2** | **2** | **none** | **none** | **yes** |

⚠️ **"2 of 613" is not "they ship AI configuration", and the distinction is the whole value of the
row.** Both twos are the identical pair, and neither is AI configuration:

```
config/klaro.klaro_app.ai_alt_text_generation.yml   id: ai_alt_text_generation
config/klaro.klaro_app.deepchat.yml                 id: deepchat
```

These are **consent-manager app declarations** — `klaro` entries describing AI features *so they can
be disclosed and switched off* if something else installs them. They are privacy metadata, not an
assistant, a provider, an agent, a key or an index.

**So the precise answer is a three-way split:**

- **AI functional configuration: 0 of 613 and 0 of 559.** Neither published template ships an
  assistant, a provider, a key entity, an agent or a vector index.
- **AI consent declarations: 2 of 613 and 2 of 559.** Both disclose AI features they do not install.
- **AI in `recommended.yml`: both.** Each recommends `drupal/drupal_cms_ai` as an optional add-on,
  with a summary naming the providers.

**The precedent is therefore not silence — it is a pattern, and the pattern is D-057 option C:
recommend it, do not require it, and disclose it in the consent manager.** Two of two published
site templates do exactly this.

⚠️ *"The marketplace expects an AI feature"* is an invention, and now measurably so: **neither
published site template ships one.** The row asked for this to be known before it was written
anywhere. It is now known.

### Two dependency facts found in the same read, both load-bearing elsewhere

**1. `recommended.yml` is consumed by `project_browser`, and both published templates require it —
at a stable constraint.**

```
haven-1.0.3/composer.json   "drupal/project_browser": "^2.1.3"
byte-1.0.3/composer.json    "drupal/project_browser": "^2.1.3"
```

`drupal/cms` 2.1.4 does **not** require `project_browser` (18 require entries, none of them it), so
it comes from the templates themselves. **This is the second, independent falsification of the
"project_browser is beta" claim**: not only is it stable, both published templates already depend
on it, at `^2.1.3`, which cannot resolve to a pre-release.

**2. Both published templates already require `search_api` — and ship an index on the DB backend.**
See T-0502's precedent block below. This bears directly on D-056.

---

## T-0502 / R9 — does `RagAction` work over `search_api_db`? · **READ AT SOURCE, decisively**

⚠️ **The source answers this, and the answer is NO — for both output modes, in two different ways.
This is a measurement of the source. It is not a measurement of the behaviour, and the row wants
both.** What it removes is the uncertainty, not the need for the run.

### The chain, with line numbers

**1. What `rendered` mode reads.**
`ai-1.4.9/modules/ai_search/src/Plugin/AiAssistantAction/RagAction.php:366-375`:

```php
protected function fullEntityCheck(array $result_items, string $query_string, array $rag_database): string {
  $rendered_entities = [];
  foreach ($result_items as $result) {
    $entity_string = $result->getExtraData('drupal_entity_id');
    // Load the entity from search api key.
    // @todo probably exists a function for this.
    [, $entity_parts, $lang] = explode(':', $entity_string);
    [$entity_type, $entity_id] = explode('/', $entity_parts);
    $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
```

**2. What `chunks` mode reads.** Same file, `renderRagResponseAsString()`, lines 293-297:

```php
      // Chunked mode is easy.
      if ($rag_database['output_mode'] == 'chunks') {
        $response .= $result->getExtraData('content') . "\n\n";
        $response .= '----------------------------------------' . "\n\n";
      }
```

**3. Who sets those two extra-data keys.** Measured over the whole of `search_api` 8.x-1.41:

```
occurrences of the string "drupal_entity_id" in search_api 8.x-1.41 ........... 0
item-level setExtraData('content', ...) in search_api 8.x-1.41 ................ 0
setExtraData calls in search_api_db's Database.php ............................ 1
   -> Database.php:1855   $results->setExtraData('search_api_facets', $facets);
      (on the ResultSet, not on an Item)
```

The only item-level extra-data keys `search_api` itself ever sets are `highlighted_fields` and
`highlighted_keys`, both from the `Highlight` processor.

**The producer of both keys is `ai_search`'s own backend**, and only it —
`ai-1.4.9/modules/ai_search/src/Plugin/search_api/backend/SearchApiAiSearchBackend.php:657`:

```php
      $item->setExtraData($key, $value);
```

fed from a vector-database match whose output fields are declared at line 470 as
`['id', 'drupal_entity_id', 'drupal_long_id', 'content']`.

### What that means for each mode

| mode | over `search_api_db` | how it fails |
|---|---|---|
| **`rendered`** | `getExtraData('drupal_entity_id')` returns **NULL** → the two list assignments at lines 372-373 have nothing to unpack → `getStorage()` is called with a null/empty entity type | **Loudly.** An exception or error is raised. |
| **`chunks`** | `getExtraData('content')` returns **NULL** → line 295 appends nothing but the separator | 🔴 **SILENTLY. No exception.** The action returns a well-formed response made of N separator rules and **zero retrieved text**, and hands that to the model as its context. |

⚠️ **The silent mode is the dangerous one, and it is dangerous in precisely the way plan §4 exists
to prevent**: a model asked to answer a citizen's question from an empty corpus, with nothing
anywhere reporting that the corpus was empty. **On a public body's transparency portal that is the
fabrication path.**

⚠️ **The row requires that `chunks` fail *differently* from `rendered`, because an identical failure
would mean the probe measured nothing. The source predicts they fail differently — loud versus
silent — which makes this a real discriminator rather than one probe run twice.**

`RagAction.php` carries **no `declare(strict_types=1)`** (checked: lines 1-5 are `<?php`, blank,
`namespace`, blank, `use`), so PHP coerces rather than raising a `TypeError` at the `explode()`
boundary; the failure surfaces one or two lines later. **Which error is raised first, and its exact
text, is not knowable by reading** — that is the part the rig run must quote.

### The consequence that reaches rule 1

**The only Search API backend that sets `drupal_entity_id` is `ai_search`'s, and that backend
requires a vector-database provider** — `SearchApiAiSearchBackend.php:113` injects
`ai.vdb_provider`, and lines 180-184 refuse to build a settings form when
`getSearchApiProviders(TRUE)` is empty.

**And every vector-database provider on drupal.org has no stable release at all** (re-verified
yesterday: `ai_vdb_provider_postgres`, `ai_vdb_provider_milvus`, `ai_vdb_provider_pinecone` — newest
stable **NONE**, no security tag on any of them).

⚠️ **So `RagAction` is not merely "backend-specific". It is specific to the one backend rule 1
forbids us from having.** The open question in the scaffold — *"that extra data looks
backend-specific"* — resolves harder than it was posed: it is not a portability wrinkle, it is a
closed door.

### Precedent, measured in the same pass — and it points the other way on the substrate

Both published site templates ship a Search API setup, and it is the **database** backend:

```
haven-1.0.3/config/   search_api.server.database.yml   backend: search_api_db   status: true
                      search_api.index.content.yml     server: database         status: true
byte-1.0.3/config/    search_api.server.database.yml   backend: search_api_db   status: true
                      search_api.index.content.yml     server: database         status: true
```

4 `search_api*` config objects each, identical shape, both enabled, `read_only: false`, datasources
`entity:node` and `entity:canvas_page`. Both require `drupal/search_api: ^1.40.0` directly.

⚠️ **haven's index enables the `entity_status` processor** (`search_api.index.content.yml:69`),
whose own description in `search_api` 8.x-1.41 is *"Exclude inactive users and unpublished entities
(which have a "Published"" state) from being indexed."* **That is the stock mechanism for T-0516's
security boundary**, and it is already the published-template norm rather than something Ágora
would invent. The index also enables `rendered_item`, which is what would make D-055 option B — a
quoted passage — reachable from the retrieval layer rather than from the model.

---

## T-0506 / R12 — does `ensureAmazeeAiAccess` call out during recipe apply? · **READ AT SOURCE**

**It is not in `drupal/ai`.** Measured: `grep -rn "ensureAmazeeAiAccess"` over `ai` 1.4.9 returns
**0 hits**; `grep -rn "ensureEasyEncryptionSetup"` likewise **0**. Both are config-action plugin ids
invoked from `drupal_cms_ai`'s `recipe.yml` and defined in other projects:

```
drupal_cms_ai 2.1.2 · recipe.yml
  ai.settings:                        ensureEasyEncryptionSetup: ~
  ai_provider_amazeeio.settings:      ensureAmazeeAiAccess: ~
                                      setupAiProvider: { provider: amazeeio, no_key_needed: true }
```

### The answer: **yes, it makes a network call during apply — and it can never fail the install**

`ai_provider_amazeeio-1.4.3/src/Plugin/ConfigAction/EnsureAmazeeAiAccess.php:62-78` — `apply()`
calls `->provision()` inside a `try`, and catches **`\Throwable`**, logging a warning and
continuing. Its own docblock, lines 25-31, says so in as many words:

> *"Provisioning the trial account is a FALLBACK, not a requirement: it must never abort the
> recipe/install that depends on this config action. If amazee.ai is at trial capacity (HTTP 429),
> rate-limiting, returning a malformed payload, unreachable (network/DNS/timeout), or fails for any
> other reason … this action logs a warning and lets the install continue"*

The call itself, `src/TrialAccess/TrialAccountProvisioner.php:138-160`:

```php
  private function fetchTrialAccountData(): object {
    $trialAccessUrl = AmazeeClient::AMAZEE_API_HOST . '/auth/generate-trial-access';
    ...
      $response = $this->httpClient
        ->requestAsync('POST', $trialAccessUrl, $options)
        ->wait();
```

with `AmazeeClient::AMAZEE_API_HOST = 'https://api.amazee.ai'`
(`src/AmazeeIoApi/AmazeeClient.php:30`), a **30-second timeout**, and a header `Referer:
drupal-install`.

**So both halves of the worry in the scaffold resolve, and they resolve in opposite directions:**

| worry | answer at source |
|---|---|
| *"it breaks the offline clean-install smoke"* | **No.** It swallows `\Throwable` and the install continues. An egress-blocked apply should cost ~30 s and log a warning. |
| *"it silently contacts a third-party processor from a public body's site"* | **Yes, and worse than the question assumed** — see below. |

### 🔴 The sharper finding: it provisions an account, and writes two live credentials into config

`TrialAccountProvisioner.php:88-108`, on success, creates or updates **two `key` entities**:

```php
    $key
      ->set('key_provider', 'config')
      ->set('key_provider_settings', ['key_value' => $trialAccessKey->litellm_token])
      ...
    $databaseKey
      ->set('key_provider', 'config')
      ->set('key_provider_settings', ['key_value' => $trialAccessKey->database_password])
```

and writes a remote host, port, database name and username into `ai_provider_amazeeio.settings`
(lines 70-76).

⚠️ **Yesterday's 🔴 said an API key lands in plaintext config when a user supplies one. This is
stronger: credentials the site operator never supplied — an API token and a database password —
are fetched from a third party and written into exportable configuration, during install, with no
key entered anywhere.** The trigger is `ensureAmazeeAiAccess`, and the guard at lines 49-55 only
skips when a non-empty key already exists.

**That `key` entities are exportable configuration is verified at source, not assumed.**
`key-8.x-1.22/src/Entity/Key.php:42-53` declares `@ConfigEntityType` with:

```php
 *   config_export = {
 *     "id", "label", "description", "key_type", "key_type_settings",
 *     "key_provider", "key_provider_settings", "key_input", "key_input_settings"
 *   }
```

`key_provider_settings` is in the export list, and that is where `key_value` lives. **`drush
config:export` writes it to YAML.** This confirms the mechanism behind plan §5.1's
`no-key-material` invariant, and confirms why `no-secrets` structurally cannot catch the empty-value
case.

**For Ágora this is a reason not to take `ai_provider_amazeeio`, and none of this reaches us unless
we do.** Ágora requires neither it nor `drupal_cms_ai` today.

### R11 · Does `ensureEasyEncryptionSetup` move the key provider off `config`? · **NO**

`easy_encryption-1.0.4/src/Plugin/ConfigAction/EnsureEasyEncryptionSetup.php:57-81`, read in full.
`apply()` returns early if an active key pair exists, otherwise calls `$this->keyGenerator->generate()`
and `$this->keyActivator->activate($keypair_id)`. **It touches no `key` entity and reads no
`key_provider`.** It generates and activates an encryption key pair for `easy_encryption` itself.

⚠️ The module **does** ship `src/Plugin/KeyProvider/EasyEncryptedKeyProvider.php` — so the mitigation
exists as a plugin. **Nothing in `drupal_cms_ai`'s recipe switches any key to it.** The 🔴 is
therefore **not** mitigated upstream: the plugin is available and unused, and `setupAiProvider`
hard-codes `'key_provider' => 'config'` at `ai-1.4.9/src/Plugin/ConfigAction/SetupAiProvider.php:127`.

### Yesterday's `SetupAiProvider` claims re-verified at source — all three hold

| claim | line | verdict |
|---|---|---|
| returns silently on an empty key, creating no key entity | `SetupAiProvider.php:61-62` — `if (empty($value['key_value']) && empty($value['no_key_needed'])) { return; }` | ✅ |
| has a first-class environment-variable path | `:56-57` — falls back to `getenv($value['env_var'])` | ✅ |
| builds a key with `key_provider: config` and a plaintext `key_value` | `:127-129` | ✅ |

**So "a keyless recipe apply cannot fail there" stands**, and the keyless risk stays where the
scaffold put it: the runtime path, which is R10.

---

## The rows that need a rig · **NOT MEASURED**

Nothing below was run. Each carries the command sequence that answers it.

### T-0501 — build the rig · NOT MEASURED

```bash
mkdir -p ~/agora-005 && cd ~/agora-005
composer create-project drupal/cms:2.1.4 . --no-interaction
# I-048: rebuild, never pull. And keep exactly ONE repositories entry.
composer config --unset repositories 2>/dev/null || true
composer require drupal/agora_transparency:@dev drupal/search_api drupal/ai:1.4.9 --no-interaction
ddev start && ddev drush site:install --account-pass=admin -y
ddev drush recipe recipes/agora_transparency
# Criteria, each printed:
ddev drush status
composer show drupal/ai | head -3                      # must print 1.4.9
ddev drush php:eval 'print count(\Drupal::entityTypeManager()->getStorage("key")->loadMultiple());'   # must print 0
composer config repositories --list                    # must show packages.drupal.org/8 only
```
⚠️ **No usage reporting:** set `drupal.org` telemetry off before install — a test rig must never
inflate the project's install count.

### T-0502 — the `RagAction` run · NOT MEASURED (source answered; behaviour did not)

```bash
ddev drush en search_api search_api_db ai ai_search ai_assistant_api -y
# Index the six bundles on the DB backend, then:
ddev drush php:eval '
$i = \Drupal::entityTypeManager()->getStorage("search_api_index")->load("agora");
$q = $i->query(["limit" => 5]); $q->keys("contract"); $r = $q->execute();
foreach ($r->getResultItems() as $it) {
  var_dump($it->getExtraData("drupal_entity_id"));   // expect NULL
  var_dump($it->getExtraData("content"));            // expect NULL
}'
# Then run the assistant twice and QUOTE BOTH:
#   output_mode: rendered  -> expect a thrown error; quote it verbatim with file and line
#   output_mode: chunks    -> expect NO error and an EMPTY context; quote the rendered string
```
**Predicted from source, so the run can falsify a prediction rather than merely produce output:**
`rendered` raises; `chunks` returns separators with no text. **If `chunks` raises too, the source
reading above is wrong and must be corrected here.**

### T-0503 — R10, the keyless citizen path · NOT MEASURED

```bash
ddev drush php:eval 'print \Drupal::entityTypeManager()->getStorage("key")->load("x") ? "key" : "none";'
ddev drush watchdog:delete all -y
curl -sS -o /tmp/anon.html -w "HTTP=%{http_code}\n" https://<rig>/<assistant-surface>
grep -c "The website encountered an unexpected error" /tmp/anon.html   # MUST be 0
ddev drush watchdog:show --count=50 --format=table   # count rows AND print severity
```
⚠️ A 200 carrying the error string is a **failed** criterion, not a pass.

### T-0504 — R5/R11, where the key lands · NOT MEASURED. **Throwaway rig; destroy and evidence it**

```bash
# SEPARATE rig. Never the one above.
ddev drush recipe recipes/... --input=openai_api_key=FAKE-KEY-DO-NOT-USE
ddev drush config:export -y
find config/ -name 'key.key.*' | tee /dev/stderr | wc -l     # the denominator, printed either way
grep -rn "key_provider:" config/key.key.* 2>/dev/null
grep -rn "key_value" config/key.key.* 2>/dev/null | sed 's/key_value:.*/key_value: [REDACTED]/'
# Destruction, evidenced:
ddev delete -Oy && rm -rf ~/agora-throwaway && ls -d ~/agora-throwaway 2>&1   # must say: No such file
```
**Source predicts:** one or more `key.key.*` objects with `key_provider: config` and a plaintext
`key_value`, and `ensureEasyEncryptionSetup` will **not** have changed the provider.

### T-0506 (second half) — apply with egress blocked · NOT MEASURED

Only needed if `ai_provider_amazeeio` is ever a candidate, which on this reading it should not be.

```bash
ddev exec 'iptables -A OUTPUT -d api.amazee.ai -j DROP' || \
  echo '127.0.0.1 api.amazee.ai' | ddev exec sudo tee -a /etc/hosts
time ddev drush recipe recipes/drupal_cms_ai        # expect success, ~30s slower
ddev drush watchdog:show --type=ai_provider_amazeeio --count=20
# Expect the warning string: "Could not provision an amazee.ai trial account"
```

### T-0506 (Config Guardian half) — R16, does the daily snapshot need cron? · NOT MEASURED

```bash
ddev drush en config_guardian -y
ddev drush php:eval 'print \Drupal::database()->select("config_guardian_snapshot")->countQuery()->execute()->fetchField();'  # number 1
ddev drush cron
ddev drush php:eval 'print \Drupal::database()->select("config_guardian_snapshot")->countQuery()->execute()->fetchField();'  # number 2
```
**Two numbers or it is not measured.** Also record whether `automated_cron` is installed, because
"daily" on a low-traffic municipal site means "whenever someone visits", and the README must not
claim "scheduled" if nothing fires.

### T-0507 — R15/R17, the two accessibility unknowns · NOT MEASURED. **Human row**

Needs a rig **and** a browser. Per surface: pages scanned, **rules run per page**, violations, and
for the chat component the `aria-live` value printed. ⚠️ A rule that did not run cannot have
passed (I-045) — the rule count is part of the criterion.

⚠️ **R17's scope caveat is worth stating now, because it changes what the gate may claim:** the
Config Guardian dashboard is **admin-only**, so Ágora's anonymous axe gate does not and will not
cover it. Whatever is measured there is recorded as a **measurement**, never as gate coverage —
plan §6 already says this, and this row must not quietly widen it.

---

## Reconciliation against the 2026-09-19 file

Divergences are healthy and are reported rather than silently corrected. **Nothing in that file is
edited; this section supersedes the specific points named.**

| # | what it said | what is on disk today | severity |
|---|---|---|---|
| 1 | two non-stable submodules in `ai` 1.4.9 (`ai_search`, `ai_logging`) | **eight** of sixteen shipping extensions | 🟡 the note was right and incomplete; the denominator is what was missing |
| 2 | `project_browser` is **beta**, so option C needs a rule-1 exception | **`2.1.4` stable, `covered="1"`**, five stable releases deep; `haven` and `byte` both require `^2.1.3` | 🔴 **a signature is being requested that is not needed** |
| 3 | R9 *"that extra data looks backend-specific"* | true, and narrower: it is specific to `ai_search`'s **VDB-backed** backend, which rule 1 forbids | 🟢 resolves harder than posed |
| 4 | R12 framed as *"if yes it breaks the offline smoke"* | it **cannot** break the install (catches `\Throwable`), but it **does** call `https://api.amazee.ai` and write two live credentials into exportable config | 🟡 the risk is real and is privacy, not install robustness |
| 5 | `SetupAiProvider` behaviour (three claims) | all three verified at source, unchanged | 🟢 |
| 6 | D-013 rider (b): `ai` 1.5 has not reached stable | still `1.5.0-rc4` today | 🟢 unchanged |
| 7 | the task row assumes `deepchat/package.json` | **no `package.json` exists**; the directory holds two files | 🟡 the row's criterion cannot be met as written |

**One more, outside the file but inside the closure.** `drupal/ai` 1.4.9's `composer.json` requires
exactly four packages — `drupal/key ^1.18`, `league/html-to-markdown ^5.1`, `yethee/tiktoken
^0.5.1`, `openai-php/client >=v0.10.1`. **D-057 option A's "four transitive packages" is exactly
right**, verified. Two observations come with it, and both bear on rule 1's spirit:

- **`openai-php/client: ">=v0.10.1"` has no upper bound.** Any future major version satisfies it.
- **`yethee/tiktoken: "^0.5.1"` is a 0.x package**, which under semver carries no stability promise.
- ⚠️ **Taking `drupal/ai` into `require` puts an OpenAI SDK into the vendor tree of every Ágora
  install**, including public bodies that will never configure a provider. That is an SBOM fact a
  transparency portal should decide deliberately rather than inherit.

---

## What this implies for the three decisions

### D-054 · Does unit 005 create the third package?

**The findings move it, and they move it toward B — but the reason has changed.** B was recommended
because it makes the citation guarantee a property rather than a request. T-0502 adds a harder
argument: **there is no stock retrieval-and-citation path to fall back on.** `RagAction` cannot run
over the only substrate rule 1 permits, and its `chunks` mode fails *silently* over that substrate
by appending empty context. So option A ("no module; generation is `recommended.yml` plus
documentation") is not merely the option that loses a differentiator — it is the option under which
**nothing in the box prevents an ungrounded answer**, because the two gates of plan §4 have nowhere
to live and the upstream action they would have replaced is unusable anyway.

Against that, T-0508 is the strongest evidence option A has ever had: **both published site
templates do exactly option A** — 0 of 613 and 0 of 559 AI config objects, `recommended.yml` only.
So A is the ecosystem-normal answer and carries no marketplace risk whatsoever. **The decision is
genuinely a product choice between "normal" and "differentiated", and it is [andres]'s.** What the
measurements remove is the middle path: *"use the stock RAG action and skip the module"* is not
available.

### D-056 · The retrieval substrate

**This moves substantially, and in a direction that makes the decision easier rather than harder.**
The recommendation was *"B, conditional on T-0502 — if `RagAction` breaks over `search_api_db`, B's
main argument evaporates and A becomes the honest choice."* **At source, `RagAction` does break over
`search_api_db`.** So B's stated main argument — *"it is the substrate the stock action expects"* —
is gone.

**But B does not fall with it, because a second argument arrived that is stronger than the one that
died.** Both published site templates ship `search_api` + `search_api_db` with an enabled index on
the database backend, and haven's index already enables `entity_status`, which is the stock
mechanism for T-0516's published-only boundary, and `rendered_item`, which is what would make a
quoted passage (D-055 option B) reachable from retrieval rather than from the model. **So B is now
justified as the published-template norm and as the substrate whose security boundary is
stock**, rather than as the thing `RagAction` wants.

⚠️ **The honest consequence: choosing B now also means accepting that the retrieval code is ours.**
That was the cost line of option A, and T-0502 has moved it onto B as well. **B and A now cost the
same in retrieval code; B buys ranking, a stock published-only filter and ecosystem precedent for
one SBOM line.** That is a clean argument for B — but note that it **couples D-056 to D-054**: if
there is no module, there is nowhere to put the retrieval code either, and B degrades to "an index
that feeds Views". **[ejecutor] may sign D-056 after T-0502, and T-0502 is only half-answered: the
source is read, the behaviour is not. I recommend not signing it until the rig run confirms the
predicted failure**, because the whole argument above rests on a prediction the run can falsify.

### D-057 · The shape of the AI dependency

**This moves, and one of its options needs repricing before it can be decided.**

- **Option B's star gets brighter and its mechanism changes.** B was *"`drupal/ai` in `require`,
  `ai_search` NOT in `install:`"*, pending R13. R13 came back as **an absence**: no clause says an
  experimental submodule of a covered stable release is or is not covered. **So B can no longer be
  justified as "the only option that keeps `sbom-check`'s promise literally true"** — nothing
  measured makes that promise false either. B's real justification is now the lifecycle audit:
  **8 of 16 shipping extensions in `ai` 1.4.9 are non-stable**, so declining submodules one by one
  is not a gesture, it is the majority of the package. `no-experimental-modules` (T-0513) becomes
  the load-bearing guard, with a real falsification target of 8.
- **Option A costs more than it looked.** The four transitive packages are confirmed, and one of
  them is an OpenAI SDK pulled into every install, behind an unbounded `>=` constraint.
- 🔴 **Option C is mispriced and must be repriced before it is decided.** Its only stated cost is
  that `recommended.yml` needs `project_browser`, *"which is beta"*. **`project_browser` is
  `2.1.4`, stable, `covered="1"`, and both published site templates require it at `^2.1.3`.** With
  that cost removed, **option C is simply what the two published site templates do**, at zero SBOM
  growth and zero rule-1 tension. It deserves to be re-put to [andres] with its real price, which
  is close to nothing, rather than with a false one.
- **The rule-1 exception list shortens.** `open-questions.md` item 5 names two candidates:
  `ai_search` while experimental, and `project_browser` at beta. **The second is not needed.**
  The first still is, and the audit makes it sharper: `ai_search` is experimental *and* it is the
  gateway to a backend that needs an alpha/beta VDB provider, so an exception for it would be an
  exception for the whole unstable chain, not for one module.

---

## What is NOT covered, said out loud

- **No runtime behaviour was observed at all.** Every "it fails" above is a statement about source.
- **R10, R15, R16, R17 are untouched** beyond the commands written for them.
- **The lifecycle audit covers the four unit-005 candidate projects, not the modules Ágora installs
  today.** Ágora's `install:` list and the `drupal_cms_*` recipes it applies were **not** walked for
  `lifecycle:` keys. **So this audit does not tell you whether the package already ships a
  deprecated module**, and that question is open. It is a bounded job: resolve the recipes' install
  lists and re-run the same walk.
- **The deep-chat bundle's upstream version is unknown** and cannot be recovered from the package.
- **`no-secrets` was not re-run against these findings**; nothing in this wave wrote code.
