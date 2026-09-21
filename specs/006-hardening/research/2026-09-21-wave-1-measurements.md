# Unit 006 · Wave 1 — measurement before planning

Measured by `tester` on **2026-09-21**, against working tree `d06b9b6` on branch `1.x`.
**Nothing here is a plan and nothing here edits a gate.** Its entire purpose is to put numbers in
front of D-060, D-061 and D-064.

**Every figure carries its source.** Each row below is labelled exactly one of:

- **MEASURED** — a command was run, or a public artefact was read, and the output is quoted.
- **READ AT SOURCE** — a file or page was read; what it still does not prove is stated.
- **NOT MEASURED — needs a rig** — Docker was off-limits for this turn. The exact command
  sequence is written out so the row becomes a short job the day a rig is free.

⚠️ **A reading is never promoted to a measurement in this file.** Unit 005's research file keeps
that separation and it is its most valuable property; it is kept here for the same reason.

---

## 0 · Reconciliation — what the dispatch and the scaffold assumed, against disk

Seven divergences. None of them is fatal to the wave; two change what a later row must do.

| # | Assumed | On disk | Consequence |
|---|---|---|---|
| 1 | `AccessibilityTest` lives in `tests/src/Functional/` | `tests/src/FunctionalJavascript/AccessibilityTest.php` | Cosmetic. Recorded so the path is right in later rows |
| 2 | `RequirementsTest`'s **header** records the STDERR pipeline | It is recorded at **`tests/src/Kernel/RequirementsTest.php:99-106`**, inside the method, not in the class header. The header is upstream's, unmodified | Cosmetic. The record exists and is quoted in T-0605 |
| 3 | `specs/006-hardening/research/` exists | It did not. This file creates it | None |
| 4 | The two `adapted` invariants carry **28 days of drift** the theme has not got | True of `identity-strings` (**125 of 125** substantive added lines absent). **FALSE of `spellcheck`: 0 of 54 added lines are absent** — the theme already has all of it | 🔴 **Changes D-063.** See T-0604 |
| 5 | D-061: the Config Guardian dashboard is *"12 Twig templates and 3 stylesheets"* | **13 Twig templates and 3 stylesheets** at 1.0.3, the version a clean install actually resolves | Minor, but it is a figure in an unsigned decision; corrected before it is signed |
| 6 | CLAUDE.md rule 1: *"literal marketplace requirement" does not survive checking* | The marketplace **application page states it literally**, verbatim, today | 🔴 **A divergence from a signed amendment.** See T-0601. **It does not weaken the rule** |
| 7 | The scaffold's ceiling arithmetic and budget | Not re-derived in this wave — out of scope for measurement-only rows | Left to T-0627, which derives it by command |

---

## T-0601 · The marketplace review criteria, re-read at source today

**MEASURED.** Source: `https://new.drupal.org/site-template/apply`, fetched **2026-09-21 06:51
UTC**, HTTP **200**, **60,970 bytes**. `https://www.drupal.org/site-template/apply` redirects to
the same page (`curl -sSL -o /dev/null -w '%{url_effective}'`). Tag-stripped to **194 text lines**;
line numbers below are that extraction's, reproducible with the script in §Appendix A.

### The five criteria, verbatim

Under the heading **"What we review"** (line 148), introduced by *"Before a template is published
to the marketplace, you'll complete a short review covering:"* (line 149):

| # | Criterion (verbatim) | Its gloss (verbatim) | line |
|---|---|---|---|
| 1 | **Installability** | *"automated CI confirms the template installs and its components render."* | 150-151 |
| 2 | **A Software Bill of Materials (SBOM)** | *"the recipes and contributed projects you include, with their security-coverage status."* | 152-153 |
| 3 | **A license manifest** | *"Drupal-derived components remain GPL; any non-GPL components (such as default content and images) are listed."* | 154-155 |
| 4 | **A WCAG accessibility attestation** | *"accessible colour contrast, keyboard navigation, and ARIA patterns."* | 156-157 |
| 5 | **A security-update commitment** | *"a defined timeline for responding to security issues, with no pinned or patched dependencies."* | 158-159 |

**Result against D-012 (signed 2026-08-21): the five names are UNCHANGED**, and this is printed
with the URL that produced it, as the row requires. D-012's note reads *"CI installability · SBOM
with security coverage · license manifest · WCAG attestation · security response, with no pins and
no patches"* (`specs/000-project/DECISIONS.md:182-184`). All five match.

### Three divergences from D-012, stated as divergences and not merged

**🔴 D-1 · The release-stability rule IS written down on the marketplace page, verbatim.**
Line 165, quoted whole:

> *"You understand that templates must work within the current versions of Drupal CMS and Drupal
> Canvas, and cannot include non-stable releases (dev, alpha, beta, rc) or patches."*

and line 159 again, inside criterion 5: *"with no pinned or patched dependencies."*

⚠️ **This contradicts CLAUDE.md's 2026-09-05 amendment to non-negotiable rule 1**, which holds
that *"Literal marketplace requirement"* **does not survive checking**. That amendment checked
three sources — the starter kit's `GET-STARTED.md`, the RFC, and `haven`'s `composer.json` — and
**it did not check the marketplace application page**, which is the one page that states the
requirement.

**What this does and does not do:**

- It does **not** weaken the rule. The rule is unchanged and now has two independent grounds
  instead of one.
- It does **not** falsify the amendment's *evidence*. `haven` 1.0.3 still ships
  `"drupal/webform": "^6.3.0-beta8"` — **re-verified at source today**, see T-0602 — so a published
  template does violate a written requirement.
- It **does** falsify the amendment's *inference*. *"Nobody wrote it"* and *"a published package
  violates it"* are different claims; the amendment proves the second and asserts the first, and
  the first is false.
- ⚠️ **The practical consequence runs the opposite way from the amendment's.** The amendment
  worried that *"the marketplace requires it"* would lose an argument the first time anyone opened
  `haven`'s `composer.json`. The real exposure is the reverse: a maintainer who has internalised
  *"nobody requires this"* could accept a beta on that basis, and the application page would then
  contradict them.

**This is reported, not applied.** CLAUDE.md is [ejecutor]'s and [andres]'s to amend, and rule 8
says a signed amendment is amended, never edited.

**🟡 D-2 · The fee has a shape now, and it is still zero for Ágora.** D-012 found the
$395 + $250/year figure came from a proposal saying *"(none for pilot and MVP)"*. The page today
names a different instrument (line 167): *"up to 30% of net retail revenue is assessed as a fee by
the Drupal Association"* — a **revenue share on paid templates**. `grep -niE "fee|395|250"` over
the extraction returns that line and nothing else. **For a free template the fee is zero**, and
D-012's conclusion is unaffected.

**🟢 D-3 · D-012's eligibility quote is unchanged**, re-read today at lines 132-133 and 141-142:
*"Free templates: Any organizations who want to submit free templates, are welcome to."* and
*"Free templates: Any individual who wants to submit free templates, is welcome to."*

### 🔴 The finding that most changes what unit 006 is for

**The five criteria bind the MARKETPLACE route. Ágora's signed route is COMMUNITY (D-012, option
C), and the community route imposes none of them.**

`https://new.drupal.org/site-template/share`, fetched 2026-09-21, HTTP 200, 180 text lines.
Line 126, verbatim:

> *"This is the simplest way to share your work: publish your site template as a free, open project
> on Drupal.org. Anyone can then find it in the Site Templates listing, download it, and install it
> on their own site. **There's no application to fill out, no fee, and no review queue to wait in**
> — publishing works the same way it does for any Drupal module or theme."*

Its five publishing steps (lines 132-145) are: build and export · create a General project ·
add code and a release · **"Write a short README. Explain what the template is for, what it
includes, and how to install it."** · publish. Then line 145: *"That's it. Your template is now
listed and installable."*

<!-- cspell:disable -->`grep -niE "WCAG|accessib|SBOM|bill of materials|licen[sc]e manifest|security|alpha|beta|patch"`<!-- cspell:enable --><!-- the regex is quoted verbatim because it is the evidence; its deliberately truncated stems are not prose and must not be "corrected" -->

over that page returns **4 hits, all of them site chrome** (the footer's "Security Advisories" and
"Web Accessibility" links, at lines 64, 65, 167). **Zero review criteria.**

⚠️ **So every artefact unit 006 is about to build — the attestation, the response commitment, the
licence manifest — is required by a route the project has not chosen, and optional on the route it
has.** That is not an argument for dropping them; CLAUDE.md already says *"Building to the
marketplace standard keeps both doors open"* and D-012's rider makes the marketplace application
**007-bis, non-blocking**. It is an argument for **saying so in the unit's own plan**, so that a
later reader does not mistake optional excellence for a blocking requirement — and for **not
letting any of the three become a blocker on publication**.

**What this still does not prove.** Line 160 says *"The Site Template Creator Guide explains each
of these in detail."* **That guide could not be found.** The link behind
*"Read the Site Template Creator Guide"* is
`/docs/develop/managing-a-drupalorg-theme-module-or-distribution-project/creating-a-new-project/how-to-create-a-new-project`
— the **generic how-to-create-a-project page**. Fetched 2026-09-21, HTTP 200, 191 text lines; the
same keyword grep returns **3 hits, all site chrome**. **The document that would define the five
criteria in detail does not exist at the link given**, so the one-line glosses above are the most
detailed statement of the criteria that could be found anywhere.

---

## T-0602 · What `haven` and `byte` actually ship

**MEASURED.** Method: the release tag's archive, not the git tip, because
`.gitattributes export-ignore` removes files from the packaged tarball and the tarball is what a
reviewer downloads. Verified that it applies: both archives arrive with `patches.json`,
`.gitlab-ci.yml`, `tests/` and `.tugboat/` **absent**.

```
curl -sSL -o <p>.tar.gz "https://git.drupalcode.org/api/v4/projects/project%2F<p>/repository/archive.tar.gz?sha=1.0.3"
```

| | `haven` 1.0.3 | `byte` 1.0.3 | `agora_transparency` @ d06b9b6 |
|---|---|---|---|
| tag date | 2026-08-11 | 2026-07-31 | (unreleased) |
| **files examined (shipped)** | **748** | **651** | **374** |
| blobs in git at the tag | 756 | 660 | 465 tracked |
| root-level entries shipped | 7 | 7 | 11 |
| `config/` files | 613 | 559 | — |
| `content/` files | 128 | 85 | — |
| media/binary assets | 25 | 15 | 37 |

**Shipped root of both published templates, identical:** `LICENSE.txt`, `composer.json`, `config/`,
`content/`, `recipe.yml`, `recommended.yml`, `screenshot.webp`.

### Artefact 1 · a security-response statement

| template | present? | path | evidence |
|---|---|---|---|
| `haven` 1.0.3 | **absent** | — | **0 of 748** files match <!-- cspell:disable -->`vulnerab\|responsible disclos\|report a security\|security\.txt\|security advisory\|security team`<!-- cspell:enable --> |
| `byte` 1.0.3 | **absent** | — | **0 of 651** |

No `SECURITY.md` at any path in either tree; no `support` key in either `composer.json` (both are
quoted in full in §Appendix B); **neither ships a `README.md` at all**, so there is no prose file
in which such a statement could hide.

**"0 of N" is the result**, as the row requires — not *"they do not appear to"*.

### Artefact 2 · a licence manifest

| template | present? | path | what it actually is |
|---|---|---|---|
| `haven` 1.0.3 | **absent** | — | `LICENSE.txt` (339 lines) is the **verbatim GPL-2.0 text**, not a manifest |
| `byte` 1.0.3 | **absent** | — | same file, same 339 lines |

Licence wording in **text** files outside `LICENSE.txt`:

- `haven`: **1 file** — `composer.json`, carrying `"license": "GPL-2.0-or-later"`. Nothing else.
- `byte`: **1 file** — `composer.json`, carrying `"license": ["GPL-2.0-or-later"]`.

⚠️ **A false positive was caught and is recorded because it is the kind that reads as a finding.**
A first pass reported `byte`'s `config/canvas.page_region.byte_theme.footer.yml` as carrying a
licence statement. It does not: the regex `CC0` matched inside the UUID
`cc01f585-46ec-48a4-90b6-570277d1f370` at line 29. Re-read at the line, and withdrawn.

### 🔴 The precedent finding that decides T-0619's shape

**`haven` ships 48 files in `content/file/`, of which 21 are named `*-unsplash.*`** — third-party
photography — **with no attribution, no per-asset licence, and no manifest anywhere in the
package.** Criterion 3 says in as many words that *"any non-GPL components (such as default content
and images) are listed"*. **A published marketplace template does not list them.**

**What this decides, and what it deliberately does not.** It decides the **shape**: one
package-level document, because there is no precedent for per-asset files and the criterion asks
for a list. It does **not** license a relaxation — Ágora already ships `content/MEDIA-LICENCES.md`
with `tests/bin/media-licence` behind it, which is **more** than either published template, and
this is precedent, not permission.

### Two further observations, both re-verified at source today

1. **`haven` 1.0.3 still ships `"drupal/webform": "^6.3.0-beta8"`** in `require`. CLAUDE.md cites
   exactly this; it is **true today**, at the current release. `byte` 1.0.3 has moved to
   `"drupal/webform": "^6.3"`.
2. **Both ship `cweagans/composer-patches` in `require-dev`** and both carry a root `patches.json`.
   ⚠️ **Neither file ships**: `patches.json` is `export-ignore`d in both. Its contents in both,
   byte-identical: `{"patches": {}, "_readme": "These patches are used for internal development
   purposes, and will be applied in CI because the composer-patches plugin is a dev dependency."}`
   — **an empty patch set**. So Ágora's `no-patches`, which scans `composer.json`, would pass on
   both; the mechanism is present and unused, and it is present in the **development** repository
   rather than in the product.

### Accessibility, checked while the trees were open

`grep -rilE 'WCAG|accessibility statement|conformance'`: **haven 1 hit, byte 0 hits.** The single
haven hit is <!-- cspell:disable -->`content/file/naja-bertolt-jensen-ovgWaprcWQM-unsplash.jpg`<!-- cspell:enable --> — **EXIF noise in a binary**,
not prose. **Neither published site template ships any accessibility statement or attestation.**

---

## T-0603 · axe + keyboard over the Config Guardian dashboard

**NOT MEASURED — needs a rig.** Docker is off-limits for this turn by dispatch. **No reading below
is offered as a substitute for the scan**, and none of it can be: axe needs a rendered page and a
browser, and a keyboard walkthrough needs a person.

### What IS established by reading, and what it is worth

**The surface exists, is this package's choice, and is larger than D-061 states.**

- Declared in `composer.json:8` as `"drupal/config_guardian": "^1.0"`.
- Installed by `recipe.yml:190` (`- config_guardian`), configured at `recipe.yml:712`.
- **The version a clean install resolves is 1.0.3, not 1.0.4**, and this is worth knowing before a
  rig run is planned: `updates.drupal.org/release-history/config_guardian/current` lists **4
  releases, newest 1.0.3** (security covered, `core_compatibility ^10.5 || ^11 || ^12`), while the
  git repository carries a **1.0.4 tag dated 2026-06-25**. **A git tag is not a release**, and
  `packages.drupal.org` is built from releases.

Read from `git.drupalcode.org/project/config_guardian/-/raw/1.0.3/config_guardian.routing.yml`:

| measure | 1.0.3 (what installs) | 1.0.4 (tagged, unreleased) |
|---|---|---|
| routes declared | **19** | 19 |
| of those, JSON (`_format: json`) | 3 | 3 |
| **HTML admin routes** | **16** | 16 |
| Twig templates | **13** | 11 |
| stylesheets | **3** | 3 |

⚠️ **D-061 states "12 Twig templates and 3 stylesheets". The number at the installed version is
13.** Corrected here before the decision is signed, rather than after.

The dashboard proper is `config_guardian.dashboard`, path
**`/admin/config/development/config-guardian`**, permission `view config snapshots`. The 19 routes
are gated by **11 distinct permissions**, 7 of them by `view config snapshots`.

**What this does not prove — stated plainly.** It proves the surface is chosen, installed and
sized. It proves **nothing whatsoever** about whether it is accessible. A route list cannot tell
you about contrast, focus order, ARIA, or whether a rule ran.

### The exact command sequence that answers this row

Run from the site template working copy with a DDEV rig available. Step 4 is the row's deliverable.

```bash
# 1. A clean rig with the template applied, and NO AI key (I-003).
wsl.exe -e bash -lc 'cd ~/agora-cms && ddev drush sql:drop --yes && ddev drush site:install \
  --existing-config --yes && ddev drush recipe ../recipes/agora_transparency'

# 2. Confirm the module and its dashboard route actually exist in THIS install,
#    rather than trusting the recipe. A route that 404s scans nothing (I-062).
wsl.exe -e bash -lc 'cd ~/agora-cms && ddev drush pm:list --status=enabled --filter=config_guardian'
wsl.exe -e bash -lc 'cd ~/agora-cms && ddev drush php:eval \
  "echo \Drupal::service(\"router.route_provider\")->getRouteByName(\"config_guardian.dashboard\")->getPath();"'

# 3. A logged-in session. The dashboard is permission-gated: an anonymous scan
#    gets the 403 page and reports 0 violations, truthfully and about nothing.
wsl.exe -e bash -lc 'cd ~/agora-cms && ddev drush user:login /admin/config/development/config-guardian'

# 4. axe over the 16 HTML routes, logged in, recording per page:
#    pages scanned · rules run per page · violations · each violation's selector.
#    Use the theme's axe harness as the pattern - it already asserts the rule
#    count and the heading-order bucket per page:
#      agora-theme/tests/src/Nightwatch/Tests/axe.js
#
#    The criterion, per D-061 option B:
#      - violations in markup THIS package owns .............. must be 0
#      - foreign violations, enumerated BY SELECTOR .......... exactly the known set
#      - the three known foreign ones (navigation x2, coffee x1) named and asserted
#      - a fourth is a FINDING, not a tolerance
#      - the rule count is part of the criterion: a rule that did not run
#        cannot have passed (I-045)

# 5. The keyboard walkthrough is T-0616 and is a PERSON's job, not this one's.
#    A machine cannot press a key and see a focus ring.
```

⚠️ **Two traps this sequence is shaped to avoid**, both of which would produce a green that means
nothing: scanning the dashboard **anonymously** (you scan the 403 page), and counting violations
without counting **rules run** (axe files a rule it could not apply in a bucket that reads exactly
like a pass).

---

## T-0604 · The 28-day drift in the two `adapted` shared invariants

**MEASURED**, and the headline is a **falsification of the scaffold's framing for one of the two
files**.

### Step 1 — the numstat, re-derived rather than carried

```
$ git diff --numstat 935c133f HEAD -- tests/bin/identity-strings tests/bin/spellcheck
132     4       tests/bin/identity-strings
54      3       tests/bin/spellcheck
```

**132/4 and 54/3 — identical to the scaffold's figures.** The recorded `source_commit` for both
`adapted` records in `agora-theme/tests/bin/shared-invariants.manifest` (lines 627 and 810) is
`935c133fbc2bfe6d10d8adff9d51b2c6a8fec35b`, dated **2026-08-24 14:16:06 +0200**; HEAD is `d06b9b6`,
**2026-09-21 05:57:23 +0200**. **28 days**, exactly as stated.

### Step 2 — 🔴 the drift in the git history is NOT the drift in the artefact

The manifest exists to protect the **copies**, so the question that matters is how many of those
added lines are actually missing from the theme. Measured line by line (`grep -qxF` of each added
line against the theme's copy):

| file | added lines since `935c133f` | **absent from the theme's copy** | present |
|---|---|---|---|
| `identity-strings` | 125 substantive (+ 7 blank = 132) | **125** | 0 |
| `spellcheck` | 54 | **0** | **54** |

⚠️ **All 14 apparent "present" matches for `identity-strings` were coincidental** — bare `#`,
`else`, `fi`, `  fi`. Inspected individually and discounted, so the honest figure is **125 of 125
absent**, not 111.

**`spellcheck` has no drift at all.** The theme's copy already carries `CORE_BRANCH="11.4.x"`
(`agora-theme/tests/bin/spellcheck:87`) and the whole parity check (lines 135-155). What is stale
is the **manifest's `source_commit` field**, not the file. Cross-checked three ways:

| comparison | `identity-strings` | `spellcheck` |
|---|---|---|
| theme copy vs template **HEAD** | +321 / −185, 11 hunks | +38 / −13, 3 hunks |
| theme copy vs template **at `935c133f`** | +323 / −66, 11 hunks | +92 / −16, 5 hunks |

For `spellcheck` the theme's copy is **closer to HEAD than to its recorded commit** — which is only
possible if it was updated past that commit and the record was never refreshed.

⚠️ **The mechanism failed in the direction that looks like vigilance.** `shared-invariants` prints
CLEAN because the **hashes** match the copies; the `source_commit` is a field nothing compares, so
it went stale silently and made the debt look **larger** than it is. A number that is wrong in the
safe direction is still a number nobody checked.

### Step 3 — every added hunk classified, with counts

**`identity-strings` — 6 hunks, 132 added, 4 deleted.** `1+45+10+3+71+2 = 132`; the accounting is
exact.

| hunk | + / − | subject | verdict |
|---|---|---|---|
| `@@ -19 +19` | +1 / −1 | header: `FIVE assertions` → `SIX assertions` | **template-only** |
| `@@ -37,0 +38,45` | +45 | header prose for assertion 6 | **mixed**: 37 template-only, **8 theme-relevant** (the "WHY IT IS GUARDED ON THE FILE EXISTING" paragraph, which is explicitly about `agora_theme`) |
| `@@ -117,0 +163,10` | +10 | prose for `content/PEOPLE.md`, `content/MEDIA-LICENCES.md` | **template-only** — neither file exists in the theme |
| `@@ -122 +177,3` | +3 / −1 | `IDENTITY_FILES` gains those two paths | 🔴 **template-only AND theme-hostile** — see below |
| `@@ -416,0 +474,71` | +71 | assertion 6 body (recipe.yml structure) | **mixed**: 61 template-only, **10 theme-relevant** (the `[ ! -r recipe.yml ]` guard at `:488-494`, plus the two `n/a` defaults at `:485-486`) |
| `@@ -419,2 +547,2` | +2 / −2 | summary `printf` gains two fields | **theme-relevant in effect** — see below |

**Totals: 114 lines template-only, 18 lines theme-relevant, across 6 hunks.**

🔴 **Hunk 4 cannot be copied.** `identity-strings:242` calls
`fatal "declared identity file(s) not readable in the working tree: … - the assertions about them
cannot be evaluated, and absent is not correct."` The theme has neither `content/PEOPLE.md` nor
`content/MEDIA-LICENCES.md`, so a verbatim re-sync **turns the theme's gate red immediately**, and
the red would be the invariant working correctly.

✅ **Hunk 6 is safe, and this was checked rather than assumed.** The theme's runner parses that
summary at `agora-theme/tests/bin/gate-a-theme.sh:366` with
`extract_count "$INV_OUT" 'naming the product:[[:space:]]*[0-9]+'` — **anchored on the field name,
not on position** — so appending fields does not break it.

🔴 **And the two copies each already have an assertion 6, and they are different assertions.**
The template's is *"recipe.yml keeps its structure"*; the theme's is *"exactly one root `*.info.yml`,
and it is named for the machine name"* — the positive form of `no-code-in-template`, which the
manifest's own header says was **written from scratch** precisely because `no-code-in-template`
cannot be copied. **A re-sync that takes the template's file whole would delete it.**

**`spellcheck` — 2 hunks, 54 added, 3 deleted. Both hunks: theme-relevant, 54 of 54 lines.**

| hunk | + / − | subject | verdict |
|---|---|---|---|
| `@@ -48,2 +48,17` | +17 / −2 | `CORE_BRANCH="11.4.x"` replacing the `11.x` development branch | **theme-relevant** |
| `@@ -84 +99,37` | +37 / −1 | the `CORE_STABLE` parity check against upstream | **theme-relevant** |

⚠️ **And the fix's own comment says the defect it repairs happened IN THE THEME**
(`tests/bin/spellcheck:77-78`): *"The theme's README cited that package name, the local run reported
'30 files checked, 0 issues', and the blocking CI job then failed on it."* **The theme already has
the fix.** The record says otherwise, and the record is what a future reader would have trusted.

### Step 4 — both scripts run in the theme

⚠️ **`spellcheck` was run from a scratchpad clone, not in place**, because it creates
`$REPO/.cspell-cache`; the dispatch forbids writing to the theme. `identity-strings` writes only to
a `mktemp -d`, so it was run in place. The theme's working tree was confirmed **clean**
(`git status --porcelain` empty) at `4f82307`.

| script | exit | denominators printed |
|---|---|---|
| `identity-strings` | **0** | identity files checked **3** · prose-only declared **45** · packaged files naming the product **48** · root info files **1** · **findings 0** · scope `git archive HEAD` = 98 files |
| `spellcheck` | **0** | **100** files offered · **91** checked by cspell · **0 issues** · parity `CORE_STABLE=11.4.7 → 11.4.x` |

⚠️ **The 9-file gap is accounted for exactly**: 4 binaries (2 `.woff2`, `logo.png`,
`screenshot.png`) + **2 SVG** (`*.svg` is an upstream `ignorePath`) + 3 named
(`.gitignore` via `.*ignore`, `LICENSE.txt`, `composer.json`) = 9. `100 − 9 = 91`.
**My prediction was 93 and it was wrong by exactly the two SVGs** — recorded because a prediction
that is quietly corrected afterwards is not a prediction.

⚠️ **`CORE_STABLE` upstream is now `11.4.7`**, not the `11.4.5` the comment names. The parity check
passes because the **branch** is unchanged (`11.4.x`), which is what it compares. The comment is
one patch release stale; the guard is not.

### What T-0604 does NOT prove

It does not prove the theme's copies are *correct* — only that they are green and that one of them
is not behind. It does not measure the other three `verbatim` shared records, whose
`source_commit`s are different again (`1b4c1a44`, `c8ce99f6`, `bc10c006`) and were **not** examined
in this row.

---

## T-0605 · Where a page count can be emitted on a GREEN run

**MEASURED for the current behaviour; READ AT SOURCE for the three routes.**

### The premise, verified rather than accepted

`tests/src/FunctionalJavascript/AccessibilityTest.php:253-266` carries the summary as the **third
argument of `assertSame()`** — an assertion message. PHPUnit emits an assertion message **only when
the assertion fails**.

**Measured on a green run.** Pipeline **`969787`**, ref `1.x`, commit **`d06b9b6`** (the current
HEAD), job **`12330735`** (`phpunit`), trace read anonymously via
`curl -sSL "https://git.drupalcode.org/project/agora_transparency/-/jobs/12330735/raw"`,
**34,487 bytes**:

```
grep -c "agora_transparency axe gate"  ->  0
grep -c "pages scanned"                ->  0
```

**Zero occurrences, confirmed by running it**, exactly as the plan's §2 states. The same trace ends
`OK (22 tests, 2596 assertions)` at line 259, with `_PHPUNIT_CONCURRENT=0` and
`_PHPUNIT_EXTRA=--fail-on-empty-test-suite` on the executed command line (line 134-135) — so the
T-1701 pin is holding on HEAD.

### The record the row asks to be quoted rather than rediscovered

`tests/src/Kernel/RequirementsTest.php:99-106` — in the method, not the class header:

> *"The denominator is therefore ASSERTED, and deliberately not printed: a test cannot print.
> PHPUnit turns any output a test emits into a `PHPUnit\Framework\Exception`, and writing to STDERR
> to dodge `beStrictAboutOutputDuringTests` does not work - it is what failed pipeline 934619 here,
> with every assertion in this method passing. The count itself is printed by
> tests/bin/config-inventory, in the agora-invariants job, where output is free and this project
> already states its denominators (I-045)."*

### 🔴 A comment in `AccessibilityTest` is itself false, and measurement is how it was caught

`AccessibilityTest.php:244-246` claims the assertion message *"reaches junit.xml and the job's own
summary instead of being lost."* **It does not, on a green run.**

The `junit.xml` artefact of that same green job is **publicly downloadable anonymously**:

```
curl -sSL "https://git.drupalcode.org/project/agora_transparency/-/jobs/12330735/artifacts/raw/junit.xml"
-> HTTP 200, 7,359 bytes
```

In it:

```xml
<testsuite name="AccessibilityTest" ... tests="1" assertions="187" errors="0" failures="0" skipped="0" time="69.955643">
  <testcase name="testAccessibilityOfTheInstalledPages" ... assertions="187" time="69.955643"/>
</testsuite>
```

`grep -c "axe gate"` → **0**. `grep -c "<failure\|<system-out\|<error"` → **0**. The `<testcase>`
is **self-closing**: a passing test carries no message anywhere. **The comment is right about the
failure path and wrong about the pass path**, which is the only path a green gate ever takes.

### Three candidate routes, with what the runner does with each

Read from `gitlab_templates` `includes/include.drupalci.main.yml` on `main`, fetched 2026-09-21,
101,953 bytes.

| # | route | what the runner does with it | verdict |
|---|---|---|---|
| **1** | **assertion message** (status quo) | Nothing on green. Reaches the trace only via a failure report | ❌ **Measured at 0 occurrences.** This is the defect |
| **2** | **`--log-junit junit.xml`** | Line **1696-1697** puts `--log-junit $CI_PROJECT_DIR/junit.xml` on the command line whenever `_PHPUNIT_CONCURRENT == 0` — which this project pins (`.gitlab-ci.yml:92`). Lines **1729-1732** publish it as a GitLab **junit report**; line **1735** also keeps it as a plain artefact path. Confirmed live in the trace's tail: `junit.xml: found 1 matching artifact files and directories` | ⚠️ **Partial.** It carries `assertions="187"` per test on green, publicly and machine-readably — a **real denominator**. It does **not** carry the page count, and no PHPUnit logger will put a passing assertion's message there |
| **3** | **a PHPUnit extension / printer** | Line **1686**: the project's own `phpunit.xml(.dist)` is used *only if it exists*; otherwise core's `phpunit.xml.dist` is copied and rewritten by `prepare-phpunit-xml.php`. **This repository ships neither** (`ls phpunit.xml phpunit.xml.dist` → both absent) | ⚠️ **Possible but costly.** It requires shipping a `phpunit.xml`, which flips that branch and changes what CI does for both phpunit jobs. Not a small change |

**A fourth route, which the row did not name and which is the one the project has already used
once.** `RequirementsTest`'s own comment points at it: **print the count from `tests/bin/`, in the
`agora-invariants` job, where output is free** — exactly what `config-inventory` does for the
config denominator.

⚠️ **And the reason it is not a drop-in, stated so nobody plans around a false hope:** an invariant
can read the **declared** page list out of `AccessibilityTest.php` statically, but the **scanned**
count exists only at runtime, and `declarePages()` builds it dynamically —
`$canvas_storage->loadMultiple()` at line 343, plus the four `VIEW_PAGES`, the front page, one
node and the not-found page. **Declared and scanned are different numbers**, and the claim in the
README is about the scanned one. Any route that prints only the declared count must **say which it
is printing**, or it replaces an invisible figure with a misleading one.

### Context this row uncovered, which T-0612 and T-0613 will want

- The floor today is **`assertGreaterThanOrEqual(6, count($pages))`** at line **207** — a floor of
  **six**, against a README claim of **nine**.
- The nine derives as **2 canvas pages** (`content/canvas_page/`, measured: 2) **+ 1** front
  **+ 4** register routes **+ 1** node **+ 1** not-found.
- `VIEW_PAGES` (lines 71-76) names **4 of the 8** registers: `agora_base_contracts`,
  `agora_base_publications`, `agora_base_people`, `agora_base_library`. **Not scanned:**
  `agora_base_agreements`, `agora_base_datasets`, `agora_base_documents`, `agora_base_grants`.
- 🔴 **`tests/` is `export-ignore`d** (`.gitattributes`), measured: `git archive
  --worktree-attributes HEAD | tar -t | grep -c "tests/"` → **0**. So a reviewer who downloads the
  tarball **cannot read the test either**. The nine-page claim is checkable from the README, from
  the CI log (where it appears 0 times) and from the package (which omits the test) — **that is,
  from nowhere.**

---

## T-0606 · Page weight and query count of the eight listing routes

**NOT MEASURED — needs a rig.** Two numbers per route require a rendered response and a query log.
**No threshold is proposed here**, per the row: a number with no threshold is honest.

### The eight routes, read at source

From `config/views.view.agora_base_*.yml`, each declaring exactly one `page_1` path:

| # | view | route | scanned by `AccessibilityTest`? |
|---|---|---|---|
| 1 | `agora_base_agreements` | `/agreements` | no |
| 2 | `agora_base_contracts` | `/contracts` | **yes** |
| 3 | `agora_base_datasets` | `/datasets` | no |
| 4 | `agora_base_documents` | `/documents` | no |
| 5 | `agora_base_grants` | `/grants` | no |
| 6 | `agora_base_library` | `/library` | **yes** |
| 7 | `agora_base_people` | `/people` | **yes** |
| 8 | `agora_base_publications` | `/publications` | **yes** |

### The exact command sequence that answers this row

```bash
# 0. Same clean rig as T-0603 steps 1-2. Measure ANONYMOUSLY: an authenticated
#    response carries admin chrome no visitor ever downloads.

# 1. Query count per route, from Drupal's own database logger. devel/webprofiler
#    is NOT installed and MUST NOT be added - plan.md §3 puts "any new contrib
#    dependency" explicitly OUT of scope for 006.
#    Use core's built-in database logging instead:
for r in agreements contracts datasets documents grants library people publications; do
  wsl.exe -e bash -lc "cd ~/agora-cms && ddev drush php:eval '
    \Drupal::database()->enableLogging();
    \$r = \Drupal::service(\"http_kernel\")->handle(
      \Symfony\Component\HttpFoundation\Request::create(\"/$r\")
    );
    printf(\"%-14s status=%d  bytes=%d  queries=%d\n\",
      \"$r\", \$r->getStatusCode(), strlen(\$r->getContent()),
      count(\Drupal::database()->getLogger()->get(\"default\")));
  '"
done

# 2. Page weight AS A BROWSER SEES IT - the number above is the HTML document
#    only, and excludes CSS, JS, fonts and images. Both numbers are wanted and
#    they are not the same number; report them separately or the figure is a
#    claim about the wrong thing.
#    Anonymous, cold cache, total transferred over the wire:
for r in agreements contracts datasets documents grants library people publications; do
  wsl.exe -e bash -lc "cd ~/agora-cms && curl -sS -o /dev/null \
    -w '$r  html_bytes=%{size_download}  time=%{time_total}\n' \
    https://agora-cms.ddev.site/$r"
done

# 3. Full page weight including sub-resources, via the Windows screenshot loop
#    already used in this project (pnpm dlx playwright, installed Chrome):
#      page.on('response', r => total += (await r.body()).length)
#    Report: document bytes | total transferred | request count | queries.
```

⚠️ **Three traps.** (a) **Cold cache matters**: Drupal's dynamic page cache makes a second request
report a fraction of the first, and which one you measured is the whole meaning of the number.
(b) **`minRows >= 3`**: four of these routes are asserted to have rows; the other four are not, and
an empty view renders no table at all, so a suspiciously light page may be an empty one (I-062).
(c) **Do not let a test install phone drupal.org** — usage reporting inflates the project's install
count.

---

## T-0607 · Which of the ROADMAP's ten 006 points are already continuously green

**MEASURED / READ AT SOURCE per row.** Points are `specs/000-project/ROADMAP.md:215-227`.
Job ids are pipeline **`969787`**, ref `1.x`, commit **`d06b9b6`**, read from
`/api/v4/projects/project%2Fagora_transparency/pipelines/969787/jobs` on 2026-09-21: **10 jobs,
every one `success`, every one `allow_failure=false`.**

**Ten rows, as the criterion requires.**

| # | ROADMAP point | `ROADMAP.md` | verdict | what decides it |
|---|---|---|---|---|
| 1 | Full a11y audit: axe + keyboard walkthrough of every flow + WCAG 2.2 criteria | `:215-216` | 🟡 **part green, part absent** | axe is green and blocking: job **`12330735`**, `AccessibilityTest` **187 assertions**, 0 failures, in `junit.xml`. **Keyboard walkthrough: absent** — no transcript exists; T-0616. **"every flow": false** — 4 of 8 registers, 0 admin routes, `AccessibilityTest.php:71-76` |
| 2 | WCAG attestation written and signed | `:217` | 🔴 **absent** | No such file. `ACCESSIBILITY.md`, `ATTESTATION.md` both absent; `git ls-files \| grep -iE 'attest\|wcag'` → 0 hits |
| 3 | Final SBOM: stable version, security coverage, line in `DECISIONS.md` | `:218-219` | ✅ **green** | `tests/bin/sbom-check`, run by `agora-invariants` job **`12330734`** (`success`, blocking). ⚠️ Re-read on the day is T-0624's job, not this one's |
| 4 | Complete licence manifest: GPL code, OFL fonts, CC0/own media | `:220` | 🟡 **part green, part absent** | **Media covered**: `content/MEDIA-LICENCES.md` + `tests/bin/media-licence` (G13, wave 3 runner). **Code**: `LICENSE.txt` + `composer.json`. **Fonts**: OFL Public Sans lives in `agora_theme`, not here. **No package-level manifest ties the three together** — T-0619 |
| 5 | Binding smoke: clean install, no keys, verifying routes and rendering | `:221` | ✅ **green** | `Drupal CMS` job **`12330728`**, `success`, `allow_failure=false`. Builds a fresh `drupal/cms`, installs this package through a path repository |
| 6 | Visual regression stabilised across all demo pages | `:222` | 🔴 **absent** | Runs nowhere. T-804 deferred; D-045 unsigned; the `agora_theme` GitHub mirror does not exist. `plan.md:66` puts it explicitly OUT of unit 006 |
| 7 | Performance: page weight and queries; no unnecessary heavy modules | `:223` | 🔴 **absent** | No measurement exists. T-0606, above — **NOT MEASURED** |
| 8 | Public documentation in English: README, installation, post-install config | `:224-225` | 🟡 **green but stale in wording** | `README.md` ships (374-entry tarball, root entry). **Neither `haven` nor `byte` ships a README at all**, so this is already beyond precedent. **Stale**: `README.md:76` claims nine axe pages against a test floor of six and a log that prints it 0 times; `:475` states the job floor as *"the minimum of nine"* in a paragraph that is historical but reads as current against today's **ten**. T-0620, T-0611 |
| 9 | Security response commitment: documented SLA | `:226` | 🔴 **absent** | `SECURITY.md` absent; no `support` key in `composer.json`. **And no precedent**: 0 of 748 in `haven`, 0 of 651 in `byte`. T-0618, D-064 |
| 10 | Final sweep of invariants: `no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check` | `:227` | ✅ **green** | All four run in `agora-invariants` job **`12330734`**, `success`, blocking. It executes both gate runners — 17 invariants between them |

**Tally: 3 green · 3 part-green (1, 4, 8) · 4 absent (2, 6, 7, 9).**

⚠️ **This is not the scaffold's split and the difference is worth one line.** `plan.md:23-26` says
*"four-tenths already continuously green … three stale in wording, and three genuinely absent"*.
Measured, it is **three** fully green, not four; points 1, 4 and 8 are each **partly** green and
partly absent, which is a different and less comfortable shape than "stale in wording". Point 1 in
particular is not a wording problem: *"every flow"* is false by a factor of two on the registers
alone, and entirely false on the admin surface.

---

## What this wave does NOT cover

Said out loud, because a gate's silence is not a pass:

1. **The admin surface is unmeasured.** T-0603 is read, not scanned. Nothing here says the Config
   Guardian dashboard is accessible, and nothing here says it is not.
2. **Performance is unmeasured.** Not one byte and not one query.
3. **The keyboard walkthrough does not exist**, for any surface, by anyone. The three AA criteria
   axe cannot decide have never been walked.
4. **The three `verbatim` shared-invariant records were not examined** — only the two `adapted`
   ones. Their `source_commit`s differ and are older in two cases.
5. **`GET-STARTED.md` and the RFC were not re-read today.** T-0601 re-read the *marketplace pages*;
   the other two legs of CLAUDE.md's rule-1 amendment were taken as previously recorded.
6. **No claim was made about `haven`'s or `byte`'s drupal.org PROJECT PAGES.** The measurement is
   of their **packages**. A project page is a different surface and it was not opened, so *"neither
   ships a README"* is a statement about the tarball and the git tree, nothing wider.
7. **The scaffold's budget arithmetic was not re-derived.** That is T-0627's, by command.

---

## What this implies for the three decisions

### D-060 · Unit 004 was never scaffolded. What does 006 audit?

**This moves it, and it moves it toward ★B — but by removing the urgency rather than by
confirming the reasoning.** The scaffold argues B because the product is coherent without 004 and
what needs fixing is a sentence. T-0601 adds something the scaffold could not know: **the route
this project has actually signed imposes no review at all.** `/site-template/share`, line 126:
*"There's no application to fill out, no fee, and no review queue to wait in."* There is no
reviewer waiting to find that `recipe.yml` says `(empty in v1; filled by unit 004)` in six places,
because on the community route **nobody reviews it**. That does not make the promissory text
acceptable — a user reading `README.md` is a better reason to fix it than a reviewer is, and T-0620
stands on its own merits — but it removes the *"a reviewer expects a workflow"* cost from option B
and makes the cost of option A (a whole unit's slip, for a gate nobody is holding) look worse.
**T-0602 adds a second push in the same direction:** both published templates ship far more config
and content than Ágora (748 and 651 files against 374) and **neither ships a README, a security
statement, or a licence manifest.** The bar that is actually being cleared out there is lower than
this project's floor. ⚠️ **One honest caveat: none of this is evidence about whether v1 is a good
product without an editorial workflow.** It is evidence about what will be *checked*, and those are
different questions. The first still needs [andres].

### D-061 · The administrative surface and the AA claim

**This does not move the ruling, and it sharpens two of its inputs — one of which was wrong.**
The half that is not a decision is confirmed: the scope sentence is false, and T-0603 now names
what actually renders that interface with sources — `drupal_cms_admin_ui` at `recipe.yml:64`,
`config_guardian` at `recipe.yml:190` and `composer.json:8`, resolving to **1.0.3** (not the
tagged-but-unreleased 1.0.4). **The surface is bigger than D-061 states: 13 Twig templates, not 12,
plus 3 stylesheets, plus 16 HTML routes gated by 11 permissions.** The cost line for ★B should be
read against 16 routes, not against one dashboard. ⚠️ **What this wave emphatically does NOT
supply is the fact that would decide between A and B**: whether the dashboard has violations.
Option A is described as *"wrong if the dashboard has violations — the correction then reads as
having been written to cover them"*, and **that risk is exactly as open now as it was before this
wave**, because axe needs a rig. T-0603's command sequence is written so that the scan is a short
job rather than a new project; **the decision would be better made after it than before it.**
T-0605 adds an argument for ★B that D-061 does not make: the nine-page anonymous claim is currently
checkable from **nowhere** a stranger can reach — not the log, not the package — so adding a tenth
declared page without also fixing T-0613 would extend an unverifiable claim rather than a verified
one.

### D-064 · What does the security-response commitment promise?

**This moves it toward ★B and makes option C's 🔴 harder, not softer.** T-0601 pins the criterion's
exact words — *"a defined timeline for responding to security issues"* (line 158-159) — so
**option A, "best-effort, no time named", does not satisfy the text as written**: the criterion
asks for a *defined timeline*, and "best effort" defines none. T-0602 supplies the precedent, and
the precedent is **nothing**: 0 of 748 files in `haven` and 0 of 651 in `byte` carry any
security-response wording, no `SECURITY.md` at any path, no `support` key in either `composer.json`.
⚠️ **Read that carefully, because it cuts both ways.** It means the commitment is genuinely not
enforced on the route Ágora has signed, so **nothing is blocked on D-064**; and it means a document
that says something real would be the **only** one in the published set, which is precisely the
differentiation this product is for. Option C stays 🔴 on the ground D-064 already gives — Ágora is
not covered by the Drupal Security Team — and T-0602 adds that neither published template points at
it either, so there is not even a convention to borrow. **The decision is still entirely
[andres]'s, because it binds a named person to a named time, and no measurement can choose that
number.**

---

## Appendix A · Reproducing the page extractions

```bash
curl -sSL -o apply.html "https://new.drupal.org/site-template/apply"
python3 - apply.html <<'PY'
import sys,re,html
raw=open(sys.argv[1],encoding='utf-8',errors='replace').read()
raw=re.sub(r'(?is)<(script|style|svg)\b.*?</\1>',' ',raw)
txt=html.unescape(re.sub(r'(?s)<[^>]+>','\n',raw))
ls=[re.sub(r'\s+',' ',l).strip() for l in txt.split('\n')]
ls=[l for l in ls if l]
o=[]
for l in ls:
    if not o or o[-1]!=l: o.append(l)
open(sys.argv[1]+'.txt','w',encoding='utf-8').write('\n'.join(o))
print("text lines:",len(o))
PY
```

⚠️ **The de-duplication step matters**: without it the extraction runs to roughly three times the
line count and the line numbers quoted above do not reproduce.

## Appendix B · Anonymous access to drupalcode, as actually observed

Recorded because CLAUDE.md documents two of these three and the third is new.

| endpoint | anonymous result |
|---|---|
| `/api/v4/projects/.../jobs/<id>/trace` | **401** — documented, re-confirmed |
| `/project/<p>/-/jobs/<id>/raw` | **200** — the web route works. `-L` is mandatory |
| **`/project/<p>/-/jobs/<id>/artifacts/raw/<path>`** | **200** — **new.** `junit.xml` was read this way, 7,359 bytes. Also served by `/api/v4/projects/.../jobs/<id>/artifacts/<path>` |

⚠️ **The third row may close part of `claims-match-sources`' NOT CHECKED list.** Assertion totals
are currently excluded on the grounds that they *"live only in a CI log"*. Per-suite and per-test
`assertions=` are in `junit.xml`, which is anonymously downloadable. **The page count is not**, so
the exclusion does not disappear — it gets shorter. Not acted on here; named for whoever owns
T-0613.
