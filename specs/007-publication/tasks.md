# Unit 007 · Publication — tasks

Scaffolded 2026-09-25. **No row here is signed.** Read `plan.md` first: its §1 names where the
ROADMAP is superseded for this unit, and its §5 is the launch these rows build.

**Count with the command, never from prose:**

```
grep -cE '^\| T-07[0-9]{2} ' specs/007-publication/tasks.md
```

Waves are **unit-scoped** (D-062): 1 to 4. Glyphs: `✓` done · `○` open · `⏸` blocked · `👤` needs
[andres]. Repo: `T` the site template (`agora_transparency`) · `H` the theme (`agora_theme`) · `—`
neither repository: a drupal.org action, or a draft that is never committed.

**Budget (D-044 — an instrument, not a gate): 15 known rows · reserve 4 · stated ceiling 19.**
Binding: at most **2** rows resting on neither necessity nor a signature (today **0**); **0** new
contrib dependencies; **0** new invariants; and a floor — T-0704(d), T-0707 and T-0713 are never
dropped. Every change to this count is accounted in the commit that makes it (I-105).

**Every rig below is new and throwaway, and is guarded three ways before anything installs:**
`update.settings` → `fetch` → `url` pinned in `settings.php` to the value in
`tests/src/Traits/NoUsageReportingTrait.php`; the same pin in `sites/default/settings.testing.php`,
which core copies into every functional-test site; and `updates.drupal.org` and `www.drupal.org`
resolved to `127.0.0.1` in the web container. `DrupalCmsCompatibilityTest` is never run on a rig:
guarded, it fails by construction; unguarded, it reports usage (T-0635's named residual). No rig is
`~/agora-smoke`.

⚠️ **Inside a table cell a pipe is written `\|`, so a command copied from a cell below must lose the
backslash before it is run.** Measured at `6dc1d06`: T-0706's grep over the packaged text finds **0**
hits copied raw and **9** with a plain `|`.

---

## Wave 1 · True today — nothing here waits on unit 006

Four disjoint lanes: R (T-0701), P (T-0702), X (T-0703), V (T-0704). T-0702 lands between unit 006's
remaining packaged edits, never alongside them (`plan.md` §6).

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0701 ○ | T | **Put [andres]'s two release rulings on disk**, as amendments to D-050 with no new number: no more large version steps (2026-09-20), and no tag or release of either project until the official launch unless one is strictly necessary (2026-09-24). Each quoted in his words inside `cspell:disable` markers, with a translation (rule 6). His 2026-09-20 words name no end point, so the reading applied since — patch-only until the launch — is recorded beside them as a reading, never as his words | The amendment heading(s) exist in `specs/000-project/DECISIONS.md` (`grep -c` printed); the numbering command in `open-questions.md` prints the **same** number before and after the commit — the amendment consumes none; `bash tests/bin/cited-tasks-exist` exit 0 with its counts; the pushed commit's job list 10/10, `cspell` among them | — |
| T-0702 ○ | T | **Two shipped comments describe a site that no longer exists.** *Widened 2026-09-25 by ★A, signed by [ejecutor] under standing delegation: 0 new rows.* (i) **`recommended.yml:28-30` says "Neither is done yet" of two things, and one of them is done**: `project_browser` is installed on every Ágora site by `drupal_cms_admin_ui`. Correct the shipped sentence; the permalink wiring stays undone, and the file says why in one line. (ii) **`recipe.yml:583-587` says the newest theme release is 1.1.0 and the components are *"in no tag"***: 1.2.0 and 1.2.1 ship all 4 (`git ls-tree -r <tag> -- components/`). Correct it; the four `?` prefixes stay while `^1.1` admits 1.1.0. `recipe.yml:599` says the object count *"stays at 130"*, and `config-inventory` reads 133 today: the row removes the figure rather than refreshing it | (1) **measured, not read from a recipe**: on a rig, `drush pm:list --status=enabled --field=name` lists `project_browser` exactly once, and the SDC count — the `canvas.component.sdc.agora_theme.*` config objects the four `?` actions name — is **4** with the released theme and **0** with `1.1.0`; the rig, the Drupal CMS version, the `drupal_cms_admin_ui` version and both theme versions printed; (2) every edited sentence is verifiable on disk; (3) `bash tests/bin/packaged-claims` exit 0, its counts unchanged; (4) `git archive --worktree-attributes HEAD \| tar -t \| grep -cx recommended.yml` prints `1`; (5) job list 10/10 | a rig |
| T-0703 ○ 👤 | — | **The theme's project page is false in at least ten statements against the release it describes (1.2.1)**: the axe figures (*6 pages / 89 / 362*, *6 of 6*), read against the newest nightwatch trace on the day; *"55 contrast pairs"* (the trace prints 68); *"25 colour tokens"* (27 parsed); *"16 template overrides"* (19 at 1.2.1); *"5 stylesheets"* (9 CSS files since 1.2.0); *"not one line of JavaScript"* and *"No JavaScript. Zero files."* (1.2.1 ships `js/table-scroll.js`); *"Nine CI jobs"* (ten); *"not yet covered… Opt-in is in progress"* (it is covered, since 2026-09-05); and the SimplyTest.me link to `agora_transparency/1.0.0` — a version that does not exist, on a launcher that does not handle a general project. [ejecutor] drafts the replacement body — **removing figures rather than refreshing them**, keeping only those a trace prints the same day — and [andres] pastes it | After the paste, from the D7 API (`plan.md` §7): each of the **14** strings its command lists — one per statement above, the axe figures as four and the coverage note as two — is found **0** times; every numeral left in the body is listed in the handed draft with its source | — |
| T-0704 ○ | T | **Measure what the launch will rest on, before it is scheduled.** *Rewritten 2026-09-25 by ★A, signed by [ejecutor] under standing delegation, because replicating the `Drupal CMS` job on a rig either reports usage or fails by construction.* (a) the Drupal CMS release current on the day, against the version the `Drupal CMS` job builds (2.1.4 today; 2.1.6 released 2026-09-23); (b) this template at `HEAD` with the theme's `1.x` tip read on the day (`7aeb482` today), fetched from drupalcode at that named commit; (c) this template with the lowest theme release `^1.1` admits, `1.1.0`; (d) **the post-release checks of T-0713, run now, where they must fail** | (a) both versions printed — from the job trace and from `updates.drupal.org/release-history/cms/current`; on a rig at the current release, the 12 key packages' locked versions printed beside the CI job's `Locking` lines, and every difference named; `drush site:install` of the template exit 0; the template's own suite printing `OK (22 tests, <m> assertions)`, broken down per class; `DrupalCmsCompatibilityTest` **not run**, and the evidence says so — never a silent skip; (b) and (c) on a rig: the `Locking drupal/agora_theme (<v>)` line, `drush site:install` exit 0, and the suite's totals **equal to CI's at the same template commit** (22 tests / 2643 assertions at `6dc1d06`), or each difference attributed to a class — **MariaDB only, and the evidence says so**; (d) every check T-0713 makes, each recorded with its baseline value: the release feed's root `error`, `packages.drupal.org` **404** ×2, `field_project_composer_types` `[]`, `composer require drupal/agora_transparency` failing under `minimum-stability: stable`, and the install not reached. ⚠️ **(d) failing is the deliverable** | a rig |

## Wave 2 · Launch readiness — after unit 006 closes and the rulings exist

T-0705 needs only D-072 and may land earlier; it touches no packaged file. T-0706 is lane P and
waits for unit 006's last packaged edit.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0705 ○ | T | **`.tugboat/` per D-072**: deleted, or made safe | **Deleted (D-072 A):** `git ls-files .tugboat \| wc -l` prints `0`, and the packaged text names nothing that no longer exists (count printed); job list 10/10. **Kept (B or C):** `.tugboat/tugboat-settings.txt` pins the fetch URL to the trait's loopback value (count `1`); `grep -cE -- '--stability=dev\|minimum-stability dev' .tugboat/config.yml` prints `0`; `tests/bin/no-usage-reporting` reads `.tugboat/` and was **watched failing** with the pin removed before it passed; job list 10/10 | D-072 |
| T-0706 ○ | T | **The launch text**, in the packaged set: every sentence that stops being true on launch day, rewritten in the commit that will be tagged — the status heading and paragraph (`README.md:9-14`), the *"not released yet"* installation note (`README.md:129-133`), the support note that says there is no release (`README.md:621-624`), and both `description` strings that say *"In development"* (`composer.json:3`, and `recipe.yml:9`, which the installer shows). The community route's install path written down: `composer require drupal/agora_transparency` into a Drupal CMS project, then the installer or `drush site:install`. `extra.drupal_cms_installer` per D-074. **Not touched:** the process-disclosure section (D-069's) and the lines naming the cited assistant (D-054's) | The inventory **re-derived by command on the day**, not carried from this cell: every text file in `git archive` of `HEAD` searched with `grep -n -i -E 'in development\|not released\|no release\|not finished\|does not resolve'`, each hit edited or left with its reason in this row; every link in a rewritten section resolves **inside the tarball** or is absolute (today `README.md` sends a tarball reader to `.github/workflows/phpunit.yml`, which the tarball omits); `git diff -U0` of the commit contains **0** lines matching `cited assistant\|with citations`; `bash tests/bin/packaged-claims` exit 0 with its counts; job list 10/10 | unit 006 closed (T-0627) · D-069 · D-074 · T-0704 |
| T-0707 ○ | — | **The drafts, handed and never committed**: release notes for the theme's launch release and for the template's first release — description, short description, and whether each is new features, bug fixes or both (rule 10) — the replacement body of both project pages, and the template page's image | Handed in the launch report from the session's scratch area; `git status --short` in both working copies shows none of them; every numeral carries the job id whose `/-/jobs/<id>/raw` prints it, read the same day — **0** figures without a source; the process-disclosure wording is whatever D-069 rules, and nothing else | T-0706 · D-069 · D-070 |

## Wave 3 · The launch — one event, in this order

Strictly sequential (`plan.md` §5). Theme first, so that every intermediate state is a coherent public
state.

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0708 ○ | H | **Cut the theme's launch tag** — the version D-070 names — at a `1.x` commit whose job list is green, and read the **tag** pipeline | In `agora_theme`: `git ls-remote drupalcode refs/tags/<v>` resolves to that commit; its branch pipeline lists 10 jobs, all `success`, all `allow_failure: false`; the tag's line in the tags section of `bash tests/bin/watch-gate`, run from the theme's working copy, reads 10 jobs, all `success`, none permissive — **before** T-0709. A transient red (`error: 429`) is retried, never released beside | T-0707 · D-070 · unit 006 closed |
| T-0709 ○ 👤 | — | **[andres] creates the theme release** from T-0707's notes; if D-070 chose a new minor, he unchecks `1.2.` in the same sitting (D-050 part 2; a Wednesday) | Read by [ejecutor] afterwards: `release-history/agora_theme/current` lists `<v>` `published` with `covered="1"`; `<supported_branches>` holds exactly **one** branch; `packages.drupal.org`'s `agora_theme.json` lists `<v>`, and the time it appeared is recorded | T-0708 |
| T-0710 ○ | T | **The template's launch commit**: T-0706's text, and the theme constraint's floor raised **only if** T-0704(c) or the launched theme requires it; push and read the job list | Job list 10/10 — all `success`, all `allow_failure: false`; the `Drupal CMS` job's trace prints `Locking drupal/agora_theme (<v from T-0709>)` and `OK (1 test, 1 assertion)`, with the job id quoted; `phpunit` and `phpunit-pgsql` totals printed | T-0706 · T-0709 |
| T-0711 ○ | T | **Cut the template's first stable tag** — the version D-070 names — at T-0710's commit, and read its tag pipeline | `git ls-remote drupalcode 'refs/tags/*' \| wc -l` prints `1`, resolving to T-0710's commit; the tag's line in `bash tests/bin/watch-gate`'s tags section reads 10 jobs, all `success`, none permissive — **before** T-0712 | T-0710 |
| T-0712 ○ 👤 | — | **[andres] creates the template's first release** from T-0707's notes, and replaces both project pages and the template's image from T-0707's drafts | The release feed stops answering *"No release history was found"*; both pages re-read through the D7 API: the template's body contains **0** of `in development` and **0** of `no stable release`; the template node's `field_project_images` has **≥ 1** entry | T-0711 |
| T-0713 ○ | T | **Post-release verification** — T-0704(d)'s checks again, now expected to pass | `release-history/agora_transparency/current`: the tag's version, `published`, `covered="1"`, `<supported_branches>` = the tag's branch; `packages.drupal.org/…/agora_transparency.json` **200**, listing it; the D7 API's `field_project_composer_types` = `["drupal-recipe"]`; on a new rig, `composer create-project drupal/cms` at the current release with its `minimum-stability: stable` untouched, then `composer require drupal/agora_transparency` **with no stability flag** printing `Locking drupal/agora_transparency (<version>)`, then `drush site:install ../recipes/agora_transparency` exit 0 — each read with its time | T-0712 |
| T-0714 ○ 👤 | T | **Closure**: the unit report, the budget accounting in the same commit, and a HOLD for [andres]'s Gate B | This file's own count printed; D-044's necessity test applied **row by row**; wave 4 recorded as **carried**, with an owner and a trigger — D-012 makes it non-blocking | T-0713 |

## Wave 4 · 007-bis — the marketplace application, after the launch and never blocking it

| # | Repo | Task | Success criterion (falsifiable) | Blocked by |
|---|---|---|---|---|
| T-0715 ○ 👤 | — | **The marketplace application, by the channel D-073 names.** [ejecutor] prepares the answers and the listing material — description, labels, the five self-assessment items, the privacy, security and support attestations, a support link; [andres] asks, or submits | Every sentence in the draft points at a packaged file (`path:line`) or a job trace (job id), read the same day; the listing's accessibility value is **the attestation's own claim, never a stronger one** (T-0617); each of the five review criteria maps to exactly one packaged file; the date asked or submitted is recorded, and the reply when it comes. **If Ágora enters the installer's curated list:** its entry read back from `site-templates.yml` — package `drupal/agora_transparency`, the screenshot URL answering **200** with a 500 × 400 WebP, every link external — and D-072's usage question asked again about the SimplyTest.me demo that arrives with it | T-0713 · D-073 |
