# Media licences — Ágora Transparency

Every file this package ships under `content/` that is not a `*.yml` or `*.json` entity export —
a photograph, a PDF, a CSV distribution — has one row in the table below. The rule is checked by
`tests/bin/media-licence`, which runs in the blocking `agora-invariants` job, and it is checked in
both directions: a file with no row is a finding, and a row naming no file is a finding.

Neither published Drupal CMS site template documents any media licence at all. `haven` ships 22
Unsplash-named JPEGs and one GPL `LICENSE.txt` covering the code. A template whose subject is
accountability cannot be the third, so this file exists before the media does.

## Columns

| column | what goes in it |
|---|---|
| `path` | the path as it appears in the package, starting `content/` |
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

There are no media files yet; wave 10 (`T-1007`) authors them. The header below is the shape those
rows take, and the invariant prints `0 manifest rows … NOT YET LOAD-BEARING` until the first one
lands, rather than printing `clean` over an empty scan.

| path | title | author | source URL | licence | date retrieved |
|---|---|---|---|---|---|
