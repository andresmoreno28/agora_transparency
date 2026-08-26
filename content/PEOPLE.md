# People in the demo content — Ágora Transparency

Spanish transparency law (art. 6.1 and art. 8.1.f) obliges a public body to publish who holds each
senior post, their career, their remuneration and their severance entitlement. A transparency
template that ships an empty org chart demonstrates nothing, so this one ships a populated org
chart — and every person in it is invented.

This file is the roster that says so, one row per person, and `tests/bin/no-real-people` checks it
in the blocking `agora-invariants` job: a person shipped in `content/` whose name is not on this
roster is a finding.

## What the check can and cannot prove

**It cannot prove that a person is fictional. Nothing can.** A script cannot tell an invented
Spanish name from a real one. What it proves is narrower and worth stating exactly:

- **Roster completeness** — every person entity in `content/` has a row here. This is what catches a
  real name pasted in during authoring, which is the failure that actually happens.
- **A deny-list** — no real, named public office-holder appears anywhere in `content/`, including in
  this file. The roster is in its own scope on purpose: otherwise a real name pasted here would
  vouch for itself.
- **Personal-data shapes** — no DNI, NIE, IBAN, telephone number, routable email address or street
  address with a number, whatever name it sits beside. A fictional person with a real bank account
  is a data leak with a made-up name on it.

## The remuneration and severance figures are not personal data

The org chart carries salary and severance figures because the law requires them in a sortable
table. They are fictional numbers about fictional people. They become personal data only when
attached to a real person, which is what the roster and the deny-list exist to prevent. **They are
not findings and must never be made into one** — deleting them would delete the product, not the
risk.

## Columns

- **name** — exactly as it appears in the person entity's title.
- **position** — the post held in the fictional organisation.
- **status** — always the single word `fictional`. A row that does not say it is a finding.
- **provenance** — one line saying where the name came from, so a reader can check the claim
  instead of trusting it.

## The roster

Eight people, the whole of the org chart the demo publishes. Every name is
invented: a given name and two surnames that are ordinary in Spain were combined
by hand into a person who does not exist, for a municipality — `Fuentelclaro` —
that does not exist either, and whose absence from the INE register of the
8,656 Spanish municipalities is recorded in `specs/000-project/DECISIONS.md`
under D-010.

Because the municipality is fictional it models no real town hall, so there is
no real org chart this one could have been copied from. That is the reason the
deny-list in `tests/bin/no-real-people.py` stays at the twelve nationally
prominent names it already carried: D-010's rider records the same conclusion,
so that the instruction on T-905 to add local office-holders is answered rather
than quietly skipped.

| name | position | status | provenance |
|---|---|---|---|
| Marta Belloso Iriarte | Mayor | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Ignacio Cardeñosa Vela | Deputy Mayor and Councillor for Finance and Budget | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Elena Rebollar Quintana | Councillor for Urban Planning and Works | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Tomás Aguaviva Pinilla | Councillor for Social Services | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Nuria Vallejera Sanz | Councillor for Culture, Education and Sport | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Álvaro Menchón Serna | Councillor for Environment and Public Space | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Rosa Camarena Olalla | Municipal Secretary | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
| Javier Otazua Lumbreras | Municipal Treasurer | fictional | invented for this demo; given name and two surnames combined by hand, no real office-holder of any municipality |
