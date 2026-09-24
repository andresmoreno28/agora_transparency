# Media licences — Ágora Transparency

Every file this package ships under `content/` that is not a `*.yml` or `*.json` entity export —
a photograph, a PDF, a CSV distribution — has one row in the table below. The rule is checked by
`tests/bin/media-licence`, which runs in the blocking `agora-invariants` job, and it is checked in
both directions: a file with no row is a finding, and a row naming no file is a finding.

⚠️ **AMENDED 2026-09-21 (T-1916): "under `content/`" was the whole of this file's scope for
twenty-six days, and the package is larger than `content/`.** `logo.png` sat at the root of every
tarball — 2,251 bytes, referenced by no recipe, no config and no content, and carrying no
provenance row anywhere — and this file did not cover it, because the invariant behind it stopped
at `content/`. So the scope now reads: **every file this package ships that is not a `*.yml` or
`*.json` entity export**, whether it is under `content/` or at the top level. The two root files
are the last two rows of the table and have a section of their own at the end.

⚠️ **The top level is a CLOSED world too, and that is the part that keeps this from going stale
again.** `tests/bin/media-licence` rule 4 requires every top-level file, in the working tree and in
the tarball alike, to be declared in the invariant either as root media — which obliges a row here
— or as root non-media. A new binary dropped at the root is a finding on the day it lands, rather
than nineteen days later.

Neither published Drupal CMS site template documents any media licence at all. `haven` ships
Unsplash-named JPEGs and one GPL `LICENSE.txt` covering the code; how many, and how heavy, is
measured **once** further down this page, in the section that explains why this package ships its
own work instead. A template whose subject is accountability cannot be the third, so this file
exists before the media does.

## Columns

| column | what goes in it |
|---|---|
| `path` | the path as it appears in the package — under `content/`, or at the top level |
| `title` | what the work is called by whoever made it, not what we use it for |
| `author` | the person or body that holds the copyright |
| `source URL` | where it was obtained, so the claim can be re-checked by someone else |
| `licence` | an SPDX identifier from the allow-list, or the words `own work` |
| `date retrieved` | ISO `YYYY-MM-DD`, the day the file and its licence statement were read |

## Allow-list

`CC0-1.0` · `CC-BY-4.0` · `CC-BY-SA-4.0` · `OFL-1.1` · `own work`

The list lives in `tests/bin/media-licence` and is printed on every run. A licence outside it is a
finding, never a silent pass. **The remedy for a red row is to relicense or remove the file — never
to add a term to the list and never to add a path to an ignore.**

## The table

⚠️ **The paragraph below said "thirty-nine files, and every one of them is own
work" until 2026-09-05. Forty now, and the fortieth is the first row in this
file that is neither `own work` nor produced by the script the other
thirty-nine cite.** It is corrected here rather than quietly rewritten, because
the sentence it replaces is the exact claim a reviewer would have checked. The
superseded wording follows, and everything it says about the other thirty-nine
is still true of them.

**Thirty-nine files, and every one of them is `own work`:** this project laid
them out itself, so there is no third party whose terms could be misread. The
PDFs are generated from scratch — a page of Helvetica, one of the fourteen
standard PDF fonts a reader supplies rather than a font anyone ships — and the
CSVs hold invented figures for the fictional municipality of `Fuentelclaro`.
Nothing here was downloaded, so the `source URL` column names the script that
produced the file rather than a website, and the `date retrieved` column is the
day it was generated.

That is a deliberate choice and not a shortcut. `haven`, the published site
template measured in this unit's research, ships **21** Unsplash-named JPEGs —
**77,426,040 bytes** of them — and documents the licence of none. A template
whose subject is accountability can either document somebody else's work or
ship its own; the second is cheaper to defend and an order of magnitude
lighter.

⚠️ **AMENDED 2026-09-21 (T-0629): this page said 22, in two places, and 22 is
not reproducible at any release `haven` has ever cut.** Re-measured at source
on 2026-09-21 against the **release archive**, which is what a reviewer
downloads: **21** at `1.0.3`, **21** at `1.0.2`, **21** at the `1.x` tip, by
three spellings of the question — a path ending `-unsplash.jpg`, a path
containing `-unsplash.`, and a case-insensitive `unsplash` anywhere in the
path. All three return the same set, and every one of the 21 is under
`content/file/`. The superseded 22 was taken once, on 2026-08-26, and then
copied rather than re-derived; the two copies that were in this file are now
one, and the two in `specs/` are dated records and stay where they are.

⚠️ **A figure about somebody else's package earns its place here only if a
reader can check it**, so this is the command that produces both numbers. It
needs no account and takes about a second:

```bash
curl -sSL "https://git.drupalcode.org/api/v4/projects/project%2Fhaven/repository/archive.tar.gz?sha=1.0.3" \
  | tar -tzv | awk '/unsplash/ { n++; b += $3 } END { print n " files, " b " bytes" }'
```

It prints `21 files, 77426040 bytes`. The byte total replaces a rounded
*"75 MB"* that named no unit convention and no version, and could therefore
have meant the photographs or the whole package, which are different
numbers.

⚠️ **And nothing in this repository keeps either figure true.** They are facts
about another project at a version that will move, and no check here can
notice when it does. They are therefore named in `tests/bin/packaged-claims`'
NOT CHECKED list, with that reason, rather than left looking guarded — and
stated **once on this page**, for the reason every other figure here is.

### The script the `source URL` column names

**Thirty-nine of the forty-two rows** point at **`tests/bin/generate-demo-media.py`**,
and that is a real file in this project's repository. Run it and it re-derives
those thirty-nine from the entity exports under `content/` and compares them,
byte for byte, against what is on disk — and it checks one more, the masthead
photograph, against a recorded SHA-256 without claiming to have made it. That
file has a section of its own further down this page, and so do the two rows
the script does not reach at all, which are the two files at the package root:

```
python3 tests/bin/generate-demo-media.py          # compares; writes nothing
python3 tests/bin/generate-demo-media.py --write  # regenerates
```

The thirty-four PDFs are **derived**: every string in them — the title, the
council, the document type, the responsible area, the financial year, the
summary, and for a declaration the post and the figures — is read out of
`content/node/*.yml` and the media, file and taxonomy entities those nodes
reference. Nothing is typed into the script. The five CSVs are **not** derivable
— their figures exist nowhere else in the package — so the script holds their
exact bytes as literal text. ⚠️ **And since 2026-09-05 there is a third case:
one file the script neither derives nor holds, and only verifies.** All three
are named here rather than left to be inferred, because "generated" means three
different things across this table and the difference is the whole point of the
column.

⚠️ **`tests/` is `export-ignore`d, so the script is in the repository and not in
this tarball.** That is deliberate and it is what makes the claim free: it costs
the download nothing and it is still re-checkable by anyone, which is the whole
point of the column. The repository is
`https://git.drupalcode.org/project/agora_transparency`. The invariant that
checks this manifest, `tests/bin/media-licence`, lives under the same rule.

**This section exists because until 2026-08-27 every row cited "the template's
own build script" and no such script was in the repository** — a provenance
claim pointing at something nobody could inspect, in the one file written to
prevent exactly that. Recorded as D-042.

### The one file that script does NOT produce, and why it may not claim to

`content/file/hero-wide.webp` is the photograph behind the front-page masthead
of the demonstration site. It is **not** generated by
`tests/bin/generate-demo-media.py` and its row does not cite it. It was
**generated with OpenAI `gpt-image` version 2.0 on 2026-08-31**, cover-cropped
to 2200×530 and re-encoded to WebP from the generator's PNG output. **No human
authorship is claimed**, and it is dedicated to the public domain as
`CC0-1.0` — the row shape **D-039** signs for generated imagery, which is also
why `own work` is the one licence it may not carry: that would be false under
the US Copyright Office's position on prompt-only output and against OpenAI's
own terms in the other direction.

**Why the script does not simply emit its bytes**, which was the obvious
alternative and was tried on paper first. The `source URL` column exists so the
claim can be re-checked by someone else. Had the script carried the image as a
literal — the way it carries the five CSVs — this row would name the script,
and the script would be a **courier presenting itself as the origin**. D-042
was signed because a provenance claim pointed at something nobody could
inspect; a provenance claim naming the wrong producer is the same defect in
better clothes. ⚠️ **The technical objection anyone would reach for first is
not the reason, and it is false: measured on 2026-09-05, a 200-line
Base64 block inside that script produces `0` cspell issues.** Recorded so the
next reader does not re-derive a wrong reason for a right decision.

**It is still covered, and by the same invariant.** The script knows the file by
name, holds its **SHA-256**, and reports a finding if the bytes on disk differ
by one — so `--check` is an equality test for all forty files the script knows.
⚠️ **Those forty are not the forty-two rows of the table**: the two files at the
package root are outside the script's reach and are covered by
`tests/bin/media-licence` instead, which is the invariant that counts rows.
What it cannot do, and
says so on every run, is **re-create** this one under `--write`: a digest
restores nothing. That limitation is the whole of what is different, and it is
printed rather than left to be discovered.

```
7eaa0b36f5ae193cc9464714597d80cd585717d3a09a0829c3c18d8d5e1ffad7  hero-wide.webp
```

⚠️ **The file carries no metadata of any kind, and that is measured rather than
assumed.** The PNG OpenAI produced carries a **C2PA Content Credentials**
manifest — signed, ISO-standard, declaring `softwareAgent: gpt-image 2.0` and
`digitalSourceType: trainedAlgorithmicMedia` — and **re-encoding to WebP drops
it**. A walk of the RIFF chunk list finds exactly one chunk, `VP8 `: no `EXIF`,
no `XMP `, no colour profile, no C2PA container. `tests/bin/no-secrets` level 3
sweeps the same seven markers across every binary in the package and is
blocking. The consequence worth stating plainly: **a shipped file cannot prove
its own origin**; this section does, from a measurement taken before
conversion, and the signed source PNG is retained outside every repository.

⚠️ **D-039's one open falsification is discharged here.** It asked, before any
image was packaged, whether current generated output still carries the visible
"CR" symbol 2024 press reporting described — because if it did, *"option A dies
on aesthetics"*. The C2PA manifest records `c2pa.watermarked.unbound`, where
*unbound* means imperceptible rather than a visible mark, and **the shipped file
was opened and looked at, at 2200 pixels across, on 2026-09-05**: nothing is
stamped on it. The same inspection is what backs the subject description in the
table — a wide symmetrical building under an even overcast, colonnade across the
full width, empty forecourt — with **no flag, no coat of arms, no emblem, no
signage, no legible text and no person in the frame**. `tests/bin/no-real-people`
scans text and cannot see any of that, which is exactly why it is written down
by somebody who looked.

**Nothing else in this package is a photograph.** `screenshot.webp` at the root
is a screenshot of a rendered page and is not media under `content/`; D-039's
first riding constraint is that it is **never** generated.

⚠️ **AMENDED 2026-09-21 (T-1916). The sentence above is still true and was
doing a job it could not do.** It is the only place the package's root binaries
were mentioned, it named one of the two, and nothing could check it — so
`logo.png` was invisible to this document and to the invariant behind it for
nineteen days. Both root files now have rows in the table and a section of
their own below. The claim itself is unchanged: neither is a photograph, and
the only photograph this package ships is `content/file/hero-wide.webp`.

| path | title | author | source URL | licence | date retrieved |
|---|---|---|---|---|---|
| content/file/accounts-2022.pdf | Annual accounts 2022 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/accounts-2023.pdf | Annual accounts 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/budget-2022.pdf | Municipal budget 2022 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/budget-2023.pdf | Municipal budget 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/budget-2024.pdf | Municipal budget 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/budget-execution-2023.csv | Budget execution by chapter, financial year 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/budget-execution-2024.csv | Budget execution by chapter, financial year 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/bylaw-public-space.pdf | By-law on public space and civic conduct | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/bylaw-sport.pdf | By-law on the use of municipal sports facilities | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/bylaw-waste.pdf | By-law on the municipal waste collection charge | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/bylaw-works-tax.pdf | By-law on the tax on construction and works | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/contracts-awarded-2024.csv | Register of public contracts awarded in 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-culture.pdf | Declaration of assets and economic interests - Nuria Vallejera Sanz | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-deputy.pdf | Declaration of assets and economic interests - Ignacio Cardeñosa Vela | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-environment.pdf | Declaration of assets and economic interests - Álvaro Menchón Serna | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-mayor.pdf | Declaration of assets and economic interests - Marta Belloso Iriarte | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-planning.pdf | Declaration of assets and economic interests - Elena Rebollar Quintana | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/declaration-social.pdf | Declaration of assets and economic interests - Tomás Aguaviva Pinilla | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/exec-2024-q1.pdf | Budget execution report, first quarter 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/exec-2024-q2.pdf | Budget execution report, second quarter 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/exec-2024-q3.pdf | Budget execution report, third quarter 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/exec-2024-q4.pdf | Budget execution report, fourth quarter 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/grants-awarded-2024.csv | Register of grants awarded in 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/hero-wide.webp | Front-page masthead: a wide civic building under an even overcast | generated with OpenAI gpt-image 2.0; no human authorship claimed | OpenAI gpt-image 2.0, generated 2026-08-31; provenance, SHA-256 and metadata sweep in the section above | CC0-1.0 | 2026-08-31 |
| content/file/minutes-2023-02.pdf | Minutes of the ordinary council meeting of 23 February 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/minutes-2024-02.pdf | Minutes of the ordinary council meeting of 28 February 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/minutes-2024-04.pdf | Minutes of the ordinary council meeting of 25 April 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/minutes-2024-06.pdf | Minutes of the ordinary council meeting of 27 June 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/minutes-2024-09.pdf | Minutes of the extraordinary council meeting of 12 September 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/minutes-2024-11.pdf | Minutes of the ordinary council meeting of 28 November 2024 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/notice-interests.pdf | Public notice of the register of interests of elected members | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/plan-climate.pdf | Climate and energy action plan 2023-2030 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/plan-equality.pdf | Municipal plan for equality between women and men 2024-2027 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/plan-loneliness.pdf | Municipal plan against loneliness in older age | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/plan-urban.pdf | Municipal urban development plan, consolidated text | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/regulation-participation.pdf | Regulation of the citizen participation council | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/report-home-help.pdf | Annual report on the home help service 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/report-library.pdf | Annual report of the municipal library 2023 | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/report-roads.pdf | Report on the state of municipal roads and pavements | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| content/file/street-tree-inventory.csv | Inventory of street trees in the municipal register | Fuentelclaro Town Council (fictional) | generated by tests/bin/generate-demo-media.py | own work | 2026-08-26 |
| logo.png | The Ágora mark, rasterised at 512x512 for the Drupal.org project icon | Ágora Transparency (this project) | authored as geometry, not obtained; see "The two files at the root of the package" below | own work | 2026-09-02 |
| screenshot.webp | The front page of the installed site template, 500x400 | Ágora Transparency (this project) | captured from a page this package renders; see "The two files at the root of the package" below | own work | 2026-09-19 |

## The two files at the root of the package

Neither is under `content/` and neither is a photograph, which is why they were outside this
document until 2026-09-21. Both are own work, both are structurally inert to Drupal, and both were
measured rather than described.

### `logo.png` — 512x512, 2,251 bytes, sha256 `ffa404fe99d3dd4bf3690669a7baf8855bab79ee1df22d54911063ee8b47f841`

**It has a job, and the job is the reason it is at the root rather than anywhere else.** Drupal.org
renders a project's icon from the GitLab project avatar, and on drupalcode that avatar cannot be
set from either direction: a `PUT` to `/api/v4/projects` redirects to `www.drupal.org/git-error`,
and the project's General settings page redirects to `/-/settings/repository` for every project, so
there is no avatar field to fill in. The supported route is a file — a 512x512 PNG named
`logo.png` at the root of the default branch, 10 KB or less. It is also the card image Project
Browser shows, which for a **site template** is the picture a site builder looks at while choosing
what to install. Verified on `agora_theme` first (commit `fd14b6d`), where the icon then appeared
beside the project title, before being added here (commit `a754611`).

**It is the Ágora mark**, byte-identical to the copy in the `agora_theme` repository: an architrave
over three closed columns and one open door, on three widening steps. The structure is the brand
navy `#0d2b4d` and the open door the brand ochre `#c8781f`. The vector original is that theme's
`logo.svg`, authored as axis-aligned rectangles on a 120-unit grid — geometry, never traced from a
bitmap — so there is no third party whose terms could be misread.

**It is inert to Drupal**, which was falsified rather than assumed when it was added: a theme with
no `logo:` key in its `.info.yml` takes Drupal's default mark, and if that default had been
`logo.png` this file would have silently replaced the masthead of every installed site. Measured on
a running site with the file in place: `theme_get_setting` still returns `logo.svg` and the
rendered masthead still points at it.

### `screenshot.webp` — 500x400, 20,826 bytes, sha256 `5258a6daa55a46efa6fbd23c36de27d6b2398363ec3a5249fa776f8ac7d93585`

The screenshot a site template is required to ship, and the only property any code enforces is the
extension. It is a capture of this package's own front page — anonymous, in a fresh browser context
with no toolbar, at a 1280x1024 viewport with a device scale factor of 2, reduced from the
resulting 2560x2048 frame with a Lanczos filter and encoded as WebP at quality 86. Nothing in it
was drawn by hand or by a model, and D-039's first riding constraint is that it is **never**
generated. Full measurements, including the assertions made against the photographed DOM, are in
`specs/003-demo-content/tasks.md` under T-1106.

**Metadata proven absent rather than declared absent:** the file is a single `VP8 ` chunk with the
whole RIFF chunk list walked, no ICC profile, and none of the seven EXIF, XMP and C2PA markers
present. `tests/bin/no-secrets` level 3 agrees from outside on every run.
