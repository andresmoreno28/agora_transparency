# Unit 007 · Publication — plan

Scaffolded 2026-09-25. **Nothing here is signed scope.** It rests on
`research/2026-09-25-marketplace-and-launch.md`; the rulings it needs are proposed in
`open-questions.md` as D-070 to D-074, and all five are [andres]'s. **D-069 is reserved in this
unit's `open-questions.md`, for a ruling on the process-disclosure text that nothing here proposes
or decides.**

## §1 · What is stale before you read anything else

The ROADMAP's unit 007 (`specs/000-project/ROADMAP.md:242-259`) is this unit's direction and is
superseded here in five places. It is not edited there (rule 8).

1. **Three of its eight points are done**, measured on 2026-09-25: both projects exist on Drupal.org,
   both are canonical on drupalcode — the template with a current GitHub mirror — and both pipelines
   run there on every push (research §0).
2. **Its blocker is false**: the $395 listing fee at `:256-259`, and the risk row at `:267`. D-012
   found it false on 2026-08-21, and the application page still says so: the only fee is a share of
   revenue on **paid** templates, and free templates are open to any individual. ⚠️ Unit 006's
   `plan.md` §1 cites the same blocker at `:249-252`; the lines have moved since.
3. **Its point 8 is already decided.** D-012 chose *community first, marketplace afterwards* and made
   the application *"007-bis, non-blocking"*. What is open is the application's **channel and
   timing** — D-073 — not the route. (Whether "the launch" names the community publication or the
   marketplace listing is D-071's first question, because [andres]'s words of 2026-09-24 do not say
   which.)
4. **Its point 6 names a real key whose reach is narrow, and the ground moved under it.**
   `extra.drupal_cms_installer.links` is read only for a template already in a site's codebase; the
   installer's own demo links moved to SimplyTest.me on 2026-09-24, and SimplyTest.me launches only
   curated templates (research §3).
5. **Its heading gives the whole unit to the human** (`:242-244`). Non-negotiable rule 10, as amended
   by [andres] on 2026-08-27, splits it: commits, pushes, tags and **the release notes** are
   [ejecutor]'s; creating releases and projects on drupal.org is [andres]'s.

## §2 · What publication means here

> **Publication is the day Ágora becomes installable by default — and it happens once.** A stable
> release exists, on a project covered by the security advisory policy; `composer require
> drupal/agora_transparency` resolves it on a stock Drupal CMS without a stability flag; and every
> sentence the package and both project pages say about themselves is true that day.

"Installable by default" is the bar because of three measurements, not a preference (research §2.2):
Drupal CMS **2.1.6** ships `minimum-stability: stable`; `packages.drupal.org` answers **404** for the
template today, because a branch is not a package; and both automated paths into an install or a
demo — the installer for a curated template, and SimplyTest.me — run `composer require` with no
version and no flag. **A pre-release would be a release nobody can install by default, and no
advisory covers.**

**What publication is NOT**, so that nobody waits for it:

- **Not a place in the Drupal CMS installer or in the Site Templates listing.** Both are curated: the
  listing shows 13 templates, and every one is in the installer's list. A published community
  template is installable, findable from its own project page, and *"may be chosen"* for the listing
  (research §1.4).
- **Not the marketplace.** That is 007-bis, after the launch and never blocking it (D-012).
- **Not a demo.** D-072.

**Two standing rulings of [andres] shape the event, and neither is on disk yet** — T-0701 records them,
in his words, as amendments to D-050. On **2026-09-20** his words were
<!-- cspell:disable -->*"ya no crees más tags grandes, en todo caso 1.2.1, 1.2.2… el tema ni siquiera
está siendo usado por la gente como para subirlo tanto de versión."*<!-- cspell:enable -->
Paraphrased: no more large version steps — at most 1.2.1, 1.2.2 — because nobody uses the theme yet.
His words name no end point, and name neither "public" nor the marketplace; "until the launch" is
the reading applied since, and T-0701 records the words and the reading separately. On
**2026-09-24**: **no tag or release of either project until the official launch**, unless one is
strictly necessary to continue. D-050 part 3's amendment of 2026-09-12 already holds the template's
half of it: its first stable version comes when it is finished.

## §3 · What is already done — measured, not remembered

| ROADMAP point | state | evidence, 2026-09-25 |
|---|---|---|
| 1 · the project on Drupal.org | done | node 3618645, a general project, created 2026-08-22; the theme's node 3618791, 2026-08-24 |
| 2 · drupalcode, and the mirror | done | `1.x` at `f697759` on drupalcode **and** on the GitHub mirror; no tags |
| 3 · the pipeline on the real project | done, continuously | pipeline 975696: 10 jobs, every one `success`, none permissive |
| 4 · screenshot, description, docs on the page | partly | the page describes a template in development; **0** images, **0** documentation or demo links; `screenshot.webp` is a real 500 × 400 capture in the package |
| 5 · the `recommended.yml` permalink | not done — **out of scope** (§4) | there is nothing to recommend; research §4 |
| 6 · a demo linked from `recipe.yml` | not done | D-072, D-074 |
| 7 · the first stable release | not done | the launch, §5 |
| 8 · the route | decided (D-012); application not filed | D-073 |

**Also already true, and easy to forget:** the template has been **opted into security advisory
coverage** since 2026-09-24 — deliberately, per D-064's amendment of 2026-09-25 — and the theme since
2026-09-05. Nothing about coverage is left to do at the launch; what changes is that it starts to
apply.

## §4 · Scope

**IN**

- **The launch**, as one event across both projects (§5): the theme's launch release, the template's
  first stable release, the text that must be true that day, both project pages, and the verification
  that all of it happened.
- **Claims that are false today**, whatever the launch date: two shipped comments,
  `recommended.yml:28-30` and `recipe.yml:583-587` (T-0702), and at least ten statements on the
  theme's project page (T-0703).
- **The two release rulings, on disk** (T-0701), so this plan rests on something a reader can open.
- **What the launch depends on, measured first** (T-0704): the current Drupal CMS, the theme's
  unreleased commits, the constraint's floor, and the baseline of every post-release check.
- **`.tugboat/`** and **the installer metadata in `recipe.yml`**, per D-072 and D-074.
- **007-bis**: the marketplace application, after the launch, by the channel D-073 names.

**OUT, explicitly**

- **Wiring `recommended.yml`.** It recommends nothing, and wiring an empty list as Project Browser's
  default source shows every site owner an empty tab. ⚠️ **The day it gets an entry, a guard comes
  with it**: Project Browser marks every entry covered and maintained, whatever the truth is (research
  §4).
- **A development release (`1.x-dev`).** Composer does not need one to resolve the stable release, and
  it would publish an uncovered snapshot beside it. Listed templates carry one: precedent, not
  requirement.
- **Any change to the process-disclosure text** already in the repository and on the project page.
  It is D-069's; this unit neither edits it nor plans around a particular ruling.
- **Unit 004's areas and unit 005's unsigned half** (D-060, D-054). Neither is a launch precondition:
  the README's line about the cited assistant is true as a plan, in a section headed as a plan.
- **New contrib dependencies**, marketplace commerce, and creating projects — already done.

## §5 · The launch, as one event

### Preconditions — all of them, not most

| # | precondition | where it is decided or measured |
|---|---|---|
| 1 | **Unit 006 closed**, its Gate B signed | T-0627, which waits on T-0616 and T-0633 (keyboard walkthroughs by a person), T-0617 (the attestation), T-0603, T-0624, T-0625 and T-0626 — **8 of its 35 rows are open at `6dc1d06`, T-0627 included** |
| 2 | **D-069 ruled** | a tag freezes the packaged text — *"Once you release something, it's out in public and you can not take it back"* (research §2.1) — and the packaged set carries text D-069 governs |
| 3 | **D-070 ruled**: both version numbers | his, by his own rulings |
| 4 | **D-072 and D-074 ruled** | both change files before the tag |
| 5 | **T-0704 measured on the day** | the current Drupal CMS, the theme's `1.x` tip, the constraint's floor |
| 6 | **[andres] available for 14 days from the release** | D-064's acknowledgement window starts binding against real users; D-071 |
| 7 | **`agora_core` 1.0.0 released and covered** — added 2026-09-25, pending [andres]'s yes | unit 005's T-0522: D-054 leaves the project, its name and its coverage opt-in to him, and a new project waits 10 days before it may opt in |
| 8 | **Config Guardian 1.0.5 released** — added 2026-09-25, pending [andres]'s yes | unit 005's T-0541: the tag after his yes, and his release; T-0542 then raises the template's floor to `^1.0.5` |

Rows 7 and 8 come from [andres]'s mandates of 2026-09-25 (`specs/000-project/DECISIONS.md`) and
both are pending his yes: row 7 on whether `1.0.0` waits for `agora_core` 1.0.0 (D-071's open
half), row 8 on Config Guardian's 1.0.5 tag. The launch order puts both releases on days before
the theme's and the template's.

### The order of acts — one sitting

**Theme before template**, because every intermediate state must be a coherent public state: a theme
release that nothing requires beyond `^1.1` harms nobody if the chain stops there, while a template
that needs an unreleased theme would.

1. [ejecutor] cuts the theme's launch tag at a green `1.x` commit, and its **tag** pipeline is read and
   is green (T-0708). A tag pipeline sits beside a published release, where strangers see it; a red
   one is retried before anything is released.
2. 👤 [andres] creates the theme release from the notes (T-0709). If D-070 chose a new minor, he
   unchecks `1.2.` in the same sitting — a Wednesday, by D-050 part 2's convention.
3. [ejecutor] waits until `packages.drupal.org` lists it — up to an hour — then pushes the template's
   launch commit and reads its job list. The `Drupal CMS` trace must print the **launched** theme
   version (T-0710).
4. [ejecutor] cuts the template's first stable tag at that commit; its tag pipeline is read and is
   green (T-0711).
5. 👤 [andres] creates the template release, and replaces both project pages from the drafts (T-0712).
6. [ejecutor] runs the post-release checks — the same checks T-0704 ran as a failing baseline — and
   they now pass (T-0713).

**The event is over when T-0713 passes.** If it stops after step 2, the theme release stands on its
own and the template waits: nothing public is broken. If it stops after step 4, a tag with no release
is harmless — the theme already carries three.

### What the first stable release promises, stated before it is made

In plain words (research §2.4): reports about a stable release enter the Drupal Security Team's
process; advisories exist only for stable releases on supported branches; **a fix to the template
cannot reach a site already built from it** — site templates have no upgrade path — so an advisory
would mean a new release plus written remediation, while the theme and Config Guardian update
normally; and the 14-day acknowledgement window binds from that day. `SECURITY.md` (T-0618,
`6dc1d06`) says the same: stable releases are covered, with the Security Team's process linked and
not quoted (lines 26-30), and there is no upgrade path, so release notes say what to change by hand
(lines 46-51). Checking that is unit 006's audit (T-0626), not an edit this unit makes.

## §6 · Lanes

| lane | owner | touches | repository | rows |
|---|---|---|---|---|
| **R · record** | [ejecutor] | `specs/000-project/DECISIONS.md`, `specs/007-publication/*` | template | T-0701, T-0714 |
| **P · packaged text** | [ejecutor] | `recommended.yml`, then `README.md`, `composer.json`, `recipe.yml` | template | T-0702, T-0706, T-0710 |
| **D · preview tooling** | [ejecutor] | `.tugboat/` — and `tests/bin/no-usage-reporting` only if D-072 keeps a preview | template | T-0705 |
| **V · verification** | [ejecutor] | **no file in either repository**: throwaway rigs, public APIs, job traces | both, read-only | T-0704, T-0713, and the reads inside T-0708 to T-0712 |
| **N · notes** | [ejecutor] | the session's scratch area — **never committed** | neither | T-0707 |
| **G · tags** | [ejecutor] | git refs only | theme, then template | T-0708, T-0711 |
| **X · drupal.org** | 👤 [andres] | project pages, release forms, branch checkboxes | neither | T-0703, T-0709, T-0712, T-0715 |

⚠️ **Lane P is sequential within itself, and runs after or between unit 006's remaining prose — never
alongside it.** `tests/bin/packaged-claims` reads every packaged file, so two packaged edits landing
together move its denominator under both. Unit 006's T-0618 added a root file (`6dc1d06`), and
T-0626's remedies may edit prose.
⚠️ **Two lanes committing in one working copy commit by path** — `git commit -- <paths>` — never by
staging and then committing, which sweeps up whatever the other lane staged.
⚠️ **Every rig in this unit is new and throwaway, and is guarded three ways before anything
installs**: `update.settings` → `fetch` → `url` pinned in `settings.php` to the value in
`tests/src/Traits/NoUsageReportingTrait.php`; the same pin in `sites/default/settings.testing.php`,
which core copies into every functional-test site; and `updates.drupal.org` and `www.drupal.org`
resolved to `127.0.0.1` in the web container. `DrupalCmsCompatibilityTest` is never run on a rig:
guarded, it fails by construction; unguarded, it reports usage (T-0635's named residual). **No rig
is `~/agora-smoke`**, which is [andres]'s live preview. I-117 applies: with the pin, `/admin/config`
answers 500, by design.
⚠️ **The verification lane starts first, and it fails.** T-0704(d) runs the post-release checks before
any release exists: `packages.drupal.org` 404, *"No release history was found"*, `composer require`
unresolvable. **That failure is the baseline and is recorded, not fixed** — the same checks passing in
T-0713 is what makes the launch a measurement rather than an announcement.

## §7 · Gates, per wave

The gate is the job list read from the API, never a pipeline's status field (D-023(5)): **10 jobs,
every one `success`, every one `allow_failure: false`**, and `jobs: 0` is a failure. No wave closes on
this plan's say-so: each closes on an independent read-only verdict over its counts.

**Waves 1 and 2**, from the template's working copy:

```bash
bash tests/bin/spellcheck            # files offered, files checked, Issues found: 0; the gap stays 41
bash tests/bin/executable-bit        # examined, scripts, findings: 0
bash tests/bin/packaged-claims       # exit 0; files opened, claims extracted, comparisons, findings: 0
bash tests/bin/claims-match-sources  # exit 0; comparisons printed
bash tests/bin/cited-tasks-exist     # exit 0; citations and definitions printed
bash tests/bin/gate-a-wave1.sh       # N checks - 0 failures, and its self-check: printed = declared
git push drupalcode 1.x
bash tests/bin/watch-gate --wait     # 10 jobs, every one success, every one blocking
```

Every new tracked text file moves `spellcheck`'s two figures and `executable-bit`'s examined count
by one — this unit's own scaffold moved them by four — and the commit that moves them updates their
live copies in `CLAUDE.md` and `README.md`, in the same commit.
⚠️ **Inside a table cell a pipe is written `\|`, so a command copied from a cell must lose the
backslash before it is run.** Measured at `6dc1d06`: T-0706's grep over the packaged text finds
**0** hits copied raw and **9** with a plain `|`.

**T-0703's page**, from anywhere — expected after the paste: `0` on each of the 14 lines. It reads
the body as markup **and** as text, because one string is split by markup (`No JavaScript.` is bold
and `Zero files.` is not) and one lives only in a link. A string counts only between non-word
characters, so a new `16` cannot match an old `6`. Run on 2026-09-25, before any paste, every line
printed at least `1`.

<!-- cspell:ignore findall -->
```bash
curl -s "https://www.drupal.org/api-d7/node.json?field_project_machine_name=agora_theme" | python3 -c '
import html, json, re, sys
b = json.load(sys.stdin)["list"][0]["body"]["value"]
t = b + "\n" + html.unescape(re.sub(r"<[^>]+>", "", b))
for s in ["6 pages scanned", "89 axe rules", "362 assertions", "6 of 6 pages",
          "55 contrast pairs", "25 colour tokens", "16 template overrides", "5 stylesheets",
          "not one line of JavaScript", "No JavaScript. Zero files.", "Nine CI jobs",
          "not yet covered", "Opt-in is in progress", "agora_transparency/1.0.0"]:
    print(len(re.findall(r"(?<!\w)" + re.escape(s) + r"(?!\w)", t)), s)
'
```

**Wave 3**, at each step. Release feeds are parsed, never grepped (I-022). Before the release, the
template's feed prints `root: error` and `packages.drupal.org` answers `404` — the baseline. After it:
the version, `published`, `covered: 1`, and `200`.

```bash
# steps 1 and 4 — from that repository's working copy. The tags section is informative:
# it never changes the exit status, so read the tag's line, not the exit code.
bash tests/bin/watch-gate
# steps 2, 5 and 6 — the release feed and the package index (swap in agora_theme for step 2)
curl -s https://updates.drupal.org/release-history/agora_transparency/current | python3 -c '
import sys, xml.etree.ElementTree as E
r = E.fromstring(sys.stdin.buffer.read())
print("root:", r.tag, "| supported:", r.findtext("supported_branches"))
for x in r.iter("release"):
    s = x.find("security")
    print(x.findtext("version"), x.findtext("status"), "covered:", None if s is None else s.get("covered"))
'
curl -s -o /dev/null -w '%{http_code}\n' https://packages.drupal.org/files/packages/8/p2/drupal/agora_transparency.json
```

## §8 · Budget

**15 known rows · reserve 4 · stated ceiling 19 — an instrument, not a gate (D-044).** Any change is
accounted in the commit that makes it (I-105).

What binds, because exceeding it always means the same thing:

1. **Rows resting on neither necessity nor a signature: at most 2.** Today, 0.
2. **New contrib dependencies: 0.**
3. **New invariants: 0.** If D-072 keeps a preview, T-0705 extends `no-usage-reporting` rather than
   adding a script; a new invariant is a conversation, as it was in unit 006.
4. **A floor that is never dropped when the unit runs long:** the baseline, T-0704(d); the notes,
   T-0707; the post-release verification, T-0713.

## §9 · Risks

| risk | sev | mitigation |
|---|---|---|
| The ground moves weekly: the curated list changed on 2026-09-23 and twice on 2026-09-24, Drupal CMS 2.1.6 shipped on 2026-09-23, and the installer's `2.x` changes its link format and its language handling | 🟡 | T-0704 is re-run on the launch day; nothing in the launch rests on a figure older than that day |
| CI builds Drupal CMS **2.1.4** while **2.1.6** is current, and the marketplace asks for current versions. Measured 2026-09-25: `drupal/cms` 2.1.4 → 2.1.6 is 5 commits touching 1 file, `composer.json`, and its only changes are the version string and the removal of `"drupal/webform": "@beta"`; `DrupalCmsCompatibilityTest.php` is byte-identical at both tags (2,452 bytes); and the 2.1.4 CI job already locks `drupal_cms_installer` 2.1.6 and `drupal_cms_admin_ui` 2.1.6 (`Drupal CMS` job 12430728). So the lag is real but narrow | 🟡 | T-0704(a) |
| The launch ships six theme commits the template's CI has never run against | 🟡 | T-0704(b); T-0710 reads the `Drupal CMS` trace for the launched version |
| A demo inflates the theme's public usage figure | 🟡 | D-072; this package's own test sites are pinned already (T-0635) |
| The marketplace's only application channel is a form for sellers, and the first attempt is spent on the mismatch | 🟡 | D-073: ask first |
| Two government templates are listed already — a council template and a government starter | 🟢 | positioning, not a blocker: Ágora is the narrow case the Creator Guide says it is open to, a transparency **publication** portal (D-060) |
| The launch stops half-way | 🟢 | the order in §5 keeps every intermediate state coherent |
