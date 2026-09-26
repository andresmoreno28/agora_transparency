# Licence manifest — Ágora Transparency

**What every file in this package is licensed under, in one place, with the check that keeps each
line honest.**

This is **not** the licence itself. [`LICENSE.txt`](LICENSE.txt) is the verbatim GPL-2.0 text and
[`composer.json`](composer.json) carries `"license": "GPL-2.0-or-later"`. This file is the
*manifest*: the thing a reviewer, a council's legal officer or a curious installer reads to find out
what is inside the tarball and on what terms, without opening 300 files.

**Why it exists at all, stated with the measurement behind it.** Drupal.org's site-template review
asks for *"A license manifest — Drupal-derived components remain GPL; any non-GPL components (such
as default content or images) are listed"* — read at source on 2026-09-24 at
`https://new.drupal.org/site-template/apply`. Measured on 2026-09-21 against the two published site
templates: **`haven` 1.0.3 ships 0 of 748 files matching any licence manifest, and `byte` 1.0.3 ships
0 of 651.** Both ship the GPL text and nothing else, and `haven` ships third-party photography
whose terms appear nowhere in its package — the count of those files is stated once, in
[`content/MEDIA-LICENCES.md`](content/MEDIA-LICENCES.md), and is not repeated here. So there is no
shape to copy, and being first is the situation.

⚠️ **And it is optional on the route this project has signed.** Ágora publishes through the
community route, which states in its own words that there is *"no application to fill out, no fee,
and no review queue to wait in"*. Nobody is holding a gate on this page. It ships because a document
that says what you are installing is worth writing whether or not somebody checks it — and because
building to the review standard keeps the other door open.

---

## The five categories, and where each number comes from

| what | terms | its count | the check that keeps it honest |
|---|---|---|---|
| This package's own files | `GPL-2.0-or-later` | *stated once, in its section below* | `tests/bin/packaged-claims` |
| The projects it requires | `GPL-2.0-or-later` (Drupal.org's own condition) | *stated once, in its section below* | `tests/bin/packaged-claims` · `tests/bin/sbom-check` |
| Fonts | `OFL-1.1`, and **not in this package** | *stated once, in its section below* | `tests/bin/packaged-claims` |
| Media | per file, in the media manifest | *quoted, not restated* — see below | `tests/bin/media-licence` |
| Generated assets | `own work` | *stated once, in the media manifest* | `tests/bin/generate-demo-media.py` |

The file, project and font counts are stated once each, in their sections below, as is the number
of licence terms in use under Media. `tests/bin/packaged-claims` checks them against the package on
every push in the blocking `agora-invariants` job, so any that stops agreeing turns the pipeline red.

⚠️ **The checks themselves are NOT in the tarball you are holding, and that is said here
rather than left to be discovered.** Everything under `tests/` is `export-ignore`d, so a packaged
release contains this page and not the scripts it names. They are public, in the repository, at
`https://git.drupalcode.org/project/agora_transparency` — clone it and every command below runs.
A manifest that pointed at evidence the reader cannot reach would be asking to be taken on
trust, which is the opposite of what it is for.

---

## 1 · This package's own files — GPL-2.0-or-later

**327 files in the packaged release are covered by `GPL-2.0-or-later`.** That is every configuration
object, every content export, every line of prose and every piece of packaging metadata: everything
the tarball holds that is not a media file.

⚠️ **`LICENSE.txt` is inside that count, and it is the one file the sentence above is loose
about.** It is the GPL's own text rather than a component of this work; the GPL permits copying
that text verbatim, and distributing it beside a GPL work is what it is for. It is counted rather
than carved out because carving it out would produce a figure no command reproduces, and this
page's rule is that every number on it is re-derivable.

The configuration and content are **Drupal-derived** — they are exports produced by Drupal's own
config and content systems, so they inherit Drupal's licence, which is the condition the review
criterion states first. The prose and the packaging metadata are this project's own work, released
under the same terms deliberately, so that nothing in the package carries two answers.

⚠️ **The count above is a subtraction, and neither of the two numbers it is derived from is written
on this page.** The size of the packaged file set is stated once, in [`README.md`](README.md); the
size of the media set is stated once, in [`content/MEDIA-LICENCES.md`](content/MEDIA-LICENCES.md).
Copying either of them here would create a second copy that goes stale on a day nobody is looking —
which is the failure this whole document is shaped around, and it has already happened in this
repository more than once. `tests/bin/packaged-claims` performs the same subtraction from
`git archive` and from the media rule, and compares the result against the number above.

---

## 2 · The projects this template requires — GPL-2.0-or-later

**6 projects** are named in `composer.json`'s `require`, and **every one of them is a project
hosted on Drupal.org**, where the licensing policy makes `GPL-2.0-or-later` a condition of hosting.
None is vendored: Composer resolves them all at install time and none of their files is inside this
tarball.

⚠️ **This count moved 11 → 6 on 2026-09-26.** Drupal CMS 2.2.0 consolidated six of the projects this
template required — `drupal_cms_admin_ui`, `drupal_cms_anti_spam`, `drupal_cms_authentication`,
`drupal_cms_media`, `drupal_cms_privacy_basic` and `drupal_cms_seo_basic` — into one,
`drupal/drupal_cms_site_template_base`, also GPL-2.0-or-later and hosted on Drupal.org. See the
amendment to D-018 in `specs/000-project/DECISIONS.md`.

What is checked here, and what is not, because the difference matters:

* **Checked, offline, on every push.** The count above against the `require` block itself, by
  `tests/bin/packaged-claims`.
* **Checked, over the network, on every push.** Each project's newest release and its
  security-coverage status, by `tests/bin/sbom-check` against `updates.drupal.org`. Alongside it,
  `tests/bin/no-unstable-deps` refuses a dev, alpha, beta or rc dependency and `tests/bin/no-patches`
  refuses a patch, which is the other half of what the review asks for.
* ⚠️ **NOT checked.** Nothing in this repository re-derives each project's declared licence from
  `packages.drupal.org` and compares it against the sentence above. The claim rests on Drupal.org's
  hosting condition, not on a measurement taken here. It is written down as an unchecked claim
  rather than left to look like a checked one; closing it takes an HTTP request per project, and it
  has no owner yet.

The dependency list itself, with a justification for every entry, is in
[`specs/000-project/DECISIONS.md`](specs/000-project/DECISIONS.md) — which stays in the repository
and is not part of the release.

---

## 3 · Fonts — OFL-1.1, and deliberately not here

**This package ships 0 font files.** Asserted rather than implied: `tests/bin/packaged-claims`
counts `.woff2`, `.woff`, `.ttf`, `.otf` and `.eot` in the packaged set and fails when that count
and the figure in bold disagree, so a font cannot be vendored here without this page saying so.

The typeface the installed site uses is **Public Sans**, under the **SIL Open Font License 1.1**. It
ships in [`drupal/agora_theme`](https://www.drupal.org/project/agora_theme), a separate Drupal.org
project this template requires, beside its own `fonts/OFL.txt`; that project's `README.md` records
the version, the upstream release, the SHA-256 of every file and whether they were modified, and its
own blocking CI job checks it.

⚠️ **No figure about the theme is copied onto this page, and that is a rule rather than an
omission.** A number about another project, restated here, goes stale on that project's release
schedule and nothing in this package could ever notice. This repository has watched that happen: a
sentence in `README.md` quoted the theme's accessibility figure for less than a day before the theme
moved it, and nothing in either repository could see the contradiction.

---

## 4 · Media — one row per file, in the media manifest

Every photograph, PDF and CSV distribution this package ships has its own row in
[`content/MEDIA-LICENCES.md`](content/MEDIA-LICENCES.md), giving its path, title, author, source URL,
SPDX licence identifier and the date its licence statement was read.

**Two licence terms are in use across those rows**: `own work` and `CC0-1.0`, and no other.
The allow-list a row may draw on is wider than that, and it lives in
`tests/bin/media-licence` rather than in prose, so it is printed on every run and cannot be widened
quietly. **The remedy for a row that will not pass is to relicense or remove the file — never to add
a term to the list.**

**The count of those rows is quoted, not restated.** It is the `manifest rows` field of the summary
line `tests/bin/media-licence` prints, and it is read there rather than written here:

```shell
bash tests/bin/media-licence | tail -3
```

That invariant checks the rule in both directions — a media file with no row is a finding, and a row
naming no file is a finding — over the working tree and over the packaged tarball, which are
different sets and are compared against each other. It runs in the blocking `agora-invariants` job.

---

## 5 · Generated assets — own work

The PDF documents and CSV distributions in `content/file/` were not downloaded from anywhere. They
are generated from the package's own entity exports by `tests/bin/generate-demo-media.py`, which is
in the repository — not in this tarball, see the note above — and can be run by anyone: every string in a generated PDF is read out of
`content/node/*.yml` and the entities those nodes reference, and the figures in the CSVs are
invented for a fictional municipality. Their per-format counts are stated once, in the media
manifest, and are not repeated here.

That script does more than document them. Run without arguments it **re-derives each file and
compares it byte for byte** against what is on disk; `tests/bin/gate-a-wave3.sh` runs it on every
push and asserts both its exit status and how many files it reproduced, because a generator that
silently enumerated nothing prints exactly what a clean tree prints.

The one file that is neither own work nor produced by that script is the front-page masthead image,
which is machine-generated, claims no human authorship and is released under `CC0-1.0`. Its
provenance, its SHA-256 and the metadata sweep applied to it are recorded in its own section of the
media manifest.

---

## What this manifest does not cover

Said plainly, because a manifest's silence is not a statement of freedom:

* **Drupal core, and everything Composer resolves beneath the required projects.** None of it is
  in this tarball; all of it arrives at install time under its own terms, and Drupal core is
  `GPL-2.0-or-later`.
* **The development and process layer** — `tests/`, `specs/`, `.claude/`, `CLAUDE.md`,
  `.gitlab-ci.yml`. All of it is `export-ignore`d in `.gitattributes`, so it stays readable to
  anyone who clones the repository and is not part of the release an end user installs. It is the
  same licence; it is simply not in the package this page is about.
* **Trade marks.** The GPL licenses copyright, not names. "Ágora" and the Ágora mark in `logo.png`
  are this project's own work and are listed in the media manifest as such; nothing here grants a
  right to use the name for a modified distribution.

---

## Re-deriving each line of the table

Each command below answers one line of the table at the top, and the same answers are computed on
every push by the invariants named beside them. **They run in a clone of the repository, not in an
extracted tarball**, for the reason given at the top of this page:

```shell
# 1 - files under GPL-2.0-or-later: the packaged set minus the media set
bash tests/bin/packaged-claims          # prints the comparison and its two sources

# 2 - required projects
python3 -c "import json;print(len(json.load(open('composer.json'))['require']))"
bash tests/bin/sbom-check               # release status and security coverage, over the network

# 3 - font files in the packaged set
git archive --worktree-attributes HEAD | tar -t | grep -icE '\.(woff2?|ttf|otf|eot)$'

# 4 - the media rows, read from the invariant rather than from prose
bash tests/bin/media-licence | tail -3

# 5 - the generated assets, reproduced and compared byte for byte
python3 tests/bin/generate-demo-media.py
```

**Measured 2026-09-21.** A dated measurement on this page is replaced by another measurement, never
by an edit that looks tidier. The figures about `haven` and `byte` near the top are such a
measurement — of those projects' own releases, not of this package — and no command above
re-derives them.
