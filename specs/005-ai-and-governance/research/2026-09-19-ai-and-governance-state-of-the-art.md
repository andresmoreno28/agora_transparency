# Unit 005 · State of the art, measured 2026-09-19

⚠️ **Prior is not disk, and a release status is not a fact about tomorrow.** Every figure below
was read from `updates.drupal.org` or from `git.drupalcode.org` on **2026-09-19**. Re-read before
acting on any of it: a dependency that is alpha today can be stable next month, and the point of
this file is that nobody has to take these on trust.

## The headline: embedding-based RAG is closed to this project under rule 1

Read from `updates.drupal.org/release-history/<project>/current`, parsed rather than eyeballed —
the newest release and the newest **stable** release are different questions, and the second is
the one rule 1 asks:

| project | releases | newest | newest STABLE | `security covered` |
|---|---|---|---|---|
| `ai_vdb_provider_postgres` | 5 | `1.0.0-alpha4` | **NONE** | no tag |
| `ai_vdb_provider_milvus` | 10 | `1.1.0-beta4` | **NONE** | no tag |
| `ai_vdb_provider_pinecone` | 12 | `1.1.0-beta5` | **NONE** | no tag |
| `ai` | 104 | `1.5.0-rc4` | **`1.4.9`** | `1` |
| `search_api` | 69 | `8.x-1.41` | **`8.x-1.41`** | `1` |
| `config_guardian` | 4 | `1.0.3` | **`1.0.3`** | `1` |

**Every vector-database provider on drupal.org has NO stable release at all** — not one, and none
carries security coverage. Rule 1 forbids a dev/alpha/beta/rc dependency, so embeddings are not
available to Ágora today at any price.

⚠️ **`specs/000-project/ROADMAP.md` uses the word "RAG" for unit 005**, and that word smuggles in
a vector store. The retrieval this unit can actually build is Search API over the database. **The
word should stop being used as though the thing were available.** This is not a compromise being
made; it is the only shape rule 1 permits today.

## D-013 rider (b) is DISCHARGED, with no amendment

The rider required *"a fresh verification of the state of `ai` ^1.x on reaching unit 005 … if
`^1.5` has reached stable, an amendment is proposed."* **`ai` 1.5 is `1.5.0-rc4`. It has not
reached stable.** Newest stable is `1.4.9`, `covered="1"`, core `^10.5 || ^11.2`. **`^1.4` stands
exactly as signed and no amendment is proposed.** Recorded here because a rider quietly left
undischarged is the failure I-105 names.

## 🔴 The finding about OUR OWN gate: nothing reads a `lifecycle:` key

Read at tag `1.4.9` from `git.drupalcode.org/project/ai/-/raw/1.4.9/modules/…`:

```
ai_search   name: AI Search    package: AI (Experimental)   lifecycle: experimental
ai_logging  name: AI Logging   package: AI                  lifecycle: deprecated
```

**`tests/bin/sbom-check` asks whether the PROJECT has a stable, covered release. `drupal/ai`
1.4.9 passes it.** The risk is one level down, inside that same release: a submodule we might put
in `install:` declares itself experimental, and another declares itself deprecated.

⚠️ **Measured: `grep -rn "lifecycle" tests/bin/` returns nothing. Not one invariant in this
repository reads that key.** Whether an experimental submodule of a covered stable release is
itself covered by the security advisory policy is **R13, and it is unanswered** — the policy must
be read and quoted, never inferred. Either way the gap is invisible today, and invisible is the
part that gets fixed first.

## 🔴 An API key lands in config, in plaintext, by default

`ai/src/Plugin/ConfigAction/SetupAiProvider.php@1.4.9`, `createKeyFromApiKey()` builds a `key`
entity with `key_provider: config` and `key_provider_settings: {key_value: <the key>}`.
**A `key` entity is configuration**, so `drush config:export` writes the live key into YAML.

⚠️ **The accident is realistic rather than theoretical**: D-032's flow is model-on-the-rig then
export, and one export from a rig that has a key puts that key in `config/`. Rule 3 forbids
exactly this.

See plan §5.1 for the invariant, and note why it is not `no-secrets`: that script matches an
assignment carrying a value of real length (I-018), and **an empty `key_value: ''` is not a secret
and IS a defect** — it means a `key.key.*` entity got exported, and the next export on a keyed
site fills it in.

## What upstream already does right, so we do not rebuild it

- `SetupAiProvider::apply()` returns **silently** on an empty key and creates no key entity, and
  it also has a first-class environment-variable path. **A keyless recipe apply cannot fail
  there** — which narrows the keyless risk to the runtime path, where R10 looks.
- `ai_chatbot` carries an upstream regression test (`DeepChatBlockNoAssistantTest`) asserting a
  200 and that the block hides itself when no assistant is configured. ⚠️ That tests **no
  assistant**. *Assistant + provider + empty key* is a different path and is **not** tested
  upstream. That is R10, and it is ours to measure.

## The Drupal CMS AI recipe is not the foundation the ROADMAP assumes

`drupal_cms_ai` 2.1.2 places `ai_deepchat_block` **in the admin theme**
(`placeBlockInAdminTheme`, regions `gin`/`claro`) wired to the `drupal_cms_assistant` agent. It is
a **site-building assistant for editors**, not a citizen-facing assistant over a document corpus.
ROADMAP 005 point 1 names it as this unit's base; building on it inherits eight dependencies to
reach something we would then switch off.

## Config Guardian is the cheapest dependency this project has ever added

`config_guardian` 1.0.3, stable, `covered="1"`, core `^10.5 || ^11 || ^12`, and
`dependencies: [drupal:config, drupal:file]` — **zero contrib**. Snapshots are a gzipped blob in
`config_guardian_snapshot.config_data` (`type: blob`), **not files**, so there is no
web-accessible snapshot directory to leak — the first thing worth checking about a tool that
stores copies of a site's configuration. Ships 11 permissions, 7 of them `restrict access: true`.
Defaults already sane: `auto_snapshot_enabled: true`, `daily`, `max_snapshots: 50`,
`retention_days: 90`, `exclude_patterns: [system.cron, core.extension]`.

**It needs no key, no provider and no network — so the governance half of this unit can ship
before this unit's hardest decision is taken.**

## Two claims already in the shipped box that this unit must redeem or withdraw

- `AGENTS.md:77-78`: *"Ágora depends on no AI provider and requires no API key. Without a
  configured provider, AI features are simply unavailable and the rest of the site works."*
  True today **only because there are no AI features at all.** The sentence presupposes features
  that exist and switch off.
- `README.md:33`: *"AI assistant with citations, and configuration auditing — unit 005."*

Both are promissory notes in text a user reads. **If D-054 comes back "no module", they must be
narrowed in the same commit**, not left standing.

## Still to measure on a rig — these are not answerable by reading

| # | Question | Why reading cannot answer it |
|---|---|---|
| R9 | Does `RagAction` with `output_mode: rendered` work over a **`search_api_db`** index? | `getRagResults()` does a plain `$index->query()`, which suggests yes — but `fullEntityCheck()` reads `$result->getExtraData('drupal_entity_id')` and splits on it, and that extra data looks backend-specific. **This is the unit's most load-bearing unknown**: it decides whether retrieval is stock-plus-config or needs code. |
| R10 | Assistant + provider + **empty key**: what does an anonymous citizen see? | Upstream tests the no-assistant path, not this one. "Degrades gracefully" is written down and undefined; this turns it into a status code and a rendered string. |
| R11 | Does `ensureEasyEncryptionSetup` move the key provider off `config`? | Decides whether the 🔴 above is already mitigated upstream. |
| R12 | Does `ensureAmazeeAiAccess` make a network call **during recipe apply**? | If yes it breaks the offline clean-install smoke and silently contacts a third-party processor from a public body's site. |
| R13 | Are experimental submodules of a covered stable release covered by the security policy? | The policy must be quoted from source. **"I could not find it" is an acceptable finding; an inferred answer is not.** |
| R14 | What licence is `ai_chatbot/deepchat/deepchat.bundle.js`? | Minified third-party code in the dependency closure; feeds the licence manifest. |
| R15 | Is the deep-chat component keyboard-operable and axe-clean? Does it announce streamed responses? | A chat widget is a live region, a focus trap and a form at once. **Assume nothing**; this is where an AA claim is lost. |
| R16 | Does Config Guardian's `daily` snapshot need cron, and what happens where cron runs on page visits? | A "scheduled" feature that never fires is a false claim on a transparency portal. |
| R17 | Is the Config Guardian dashboard accessible? | 12 Twig templates and 3 stylesheets outside Gin and outside our theme. Admin-only, so **our anonymous axe gate does not cover it** — say so rather than let the gate imply cover it does not have. |
| R18 | Do `haven` and `byte` ship any AI configuration? | Precedent, not permission. But if neither does, *"the marketplace expects it"* is an invention, and we should know that before writing it anywhere. |
