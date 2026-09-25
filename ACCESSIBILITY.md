# Accessibility — WCAG 2.2 attestation

Ágora Transparency targets WCAG 2.2 level AA; no conformance with any WCAG level is claimed. It is
checked with axe on every push, and its keyboard behaviour was walked through — by machine on every
public page the automated check reads, and by a person on 2026-09-25 where only a person can
judge.

This file is the author's statement about the package, for whoever reviews it. It is not the
accessibility statement a site publishes: that one ships with the demonstration content, speaks to a
member of the public, and has sections that the organisation running the site must complete. This
file says how the package was checked and where each result can be read; the figures stay where
they are measured.

## What every push checks

Every push to the project's repository on git.drupalcode.org runs this package's accessibility
test, and the pipeline fails when the test does. The test applies the package to a fresh Drupal
site and runs axe-core in Chrome: as an anonymous visitor over pages of that site, and signed in
over the Config Guardian dashboard. The figures below are those reported by the most recent run at
the time this text was written. The shipped accessibility statement, at `/accessibility-statement`
([`content/node/542d60d9-f7b6-596c-9f15-7ee81964d139.yml`](content/node/542d60d9-f7b6-596c-9f15-7ee81964d139.yml)),
points here for this account instead of repeating it.

* **Automated accessibility testing of the pages this site serves.** axe-core runs over nine pages
  of the site as it is installed: the two composed landing pages, the front page as a visitor meets
  it, four of the eight listing routes (two of the six registers, the cross-type listing and the
  document library), one published record rendered through its own page, and the page-not-found
  screen. They are scanned **as an anonymous visitor**, because that is what a member of the public
  is served: the same pages scanned while signed in report findings in the administrative toolbar,
  which no visitor ever sees and which this site does not own. Result: **0 violations**. The number
  of rules actually applied is recorded for each page rather than assumed, because a rule that never
  ran cannot have passed; and each of them is checked to be a different page, because a site serving
  one page under several paths would otherwise pass.
* **Target size.** WCAG 2.2 success criterion 2.5.8 asks that a link or control be large enough, or
  far enough from its neighbours, to be hit reliably. axe-core measures this with a rule that it
  ships switched off. The scan of the pages above switches that rule on by name, so their links and
  controls are measured against the minimum size and spacing the criterion sets, apart from links
  inside a line of text, which the criterion exempts; a target that falls short fails the check.
  Where axe-core cannot decide whether a target meets the criterion, it reports that target for
  review instead of passing it, and those targets are left to a person. The measurement is taken at
  a single window size, so a target that shrinks only on a narrower screen is not measured.
* **Automated accessibility testing of the theme on its own.** The theme this site uses is a
  separate package with a gate of its own, and axe-core runs there over ten pages: a composed front
  page, a hand-built data table with rows, a second one in its empty state, a view rendering a real
  register table, a register with an exposed filter, a prose page with links and a pager, a content
  record page, a page of nested layout components, a dataset record whose distributions are drawn as
  tables, and the core sign-in form. Result: **90 rules applied per page, 0 violations**. **Those
  pages are test fixtures, not pages of this site.** They exist so that a template can be made to
  fail on its own, and a clean run over them says nothing about what a visitor here reads.
* **Colour contrast.** Every foreground and background pair declared in the colour tokens of the
  theme is measured against the threshold that pair declares. Result: **68 pairs checked, 0 below
  threshold**.
* **Data tables.** Every table the template renders carries a caption naming what the table holds,
  and header cells marked as headers, so that a screen reader can announce the row and the column a
  figure belongs to. A table too wide for the screen sits inside a scrolling region that can be
  reached and moved with the keyboard alone.
* **Page structure.** Each page carries exactly one first-level heading, asserted as exactly one: a
  page with none and a page with two are both defects, and a check that accepts *at least one* hides
  the second.
* **Bypassing the navigation.** Every key route - the listing routes and the front page - is checked
  to render the target a skip link needs: an anchor at the start of the main content, able to
  receive focus. **That the link itself is the first thing a keyboard reaches, and that following it
  moves focus, are not checked automatically**; they are listed below with the rest of what a person
  has to confirm.

Every job's whole log is public and opens without an account:

* this package: <https://git.drupalcode.org/project/agora_transparency/-/pipelines>
* the theme: <https://git.drupalcode.org/project/agora_theme/-/pipelines>

## The keyboard walkthrough

A keyboard walkthrough answers what axe cannot: whether every control can be reached and left with
the keyboard alone, in an order that makes sense, with focus visible wherever it lands. This one had
two parts, on two different builds.

**By machine, on 2026-09-23.** A script pressed real keys in Chrome — Tab through each page to its
end, and Shift+Tab back — at two window sizes: 1280 × 800 CSS pixels, and 320 × 256, which is a
1280-pixel-wide screen zoomed to 400 %. It walked every page the automated check reads as an
anonymous visitor and, signed in as an administrator, every Config Guardian page that takes no
argument. At each stop it recorded what received focus and the name the browser gave it,
photographed the element with focus and without it to see whether an indicator appeared, and
measured how much of it was out of sight. On the pages the automated check reads, it found every
stop reachable, no keyboard trap, a visible indicator at every stop, the skip link working in use,
no target failing axe's check for 2.5.8, and no page scrolling sideways at 320 pixels. What it found
on the administrative pages is under *The administrative interface* below. The build was this
package at commit `acd2a51`, applied to a fresh Drupal site, with `drupal/agora_theme` 1.2.0.

**By a person, on 2026-09-25**, on what a machine cannot settle: whether an indicator that is there
is noticed, and whether the order makes sense. The person confirmed that:

* "Skip to main content" is plainly visible on the first Tab, on any page;
* the order focus moves in makes sense: on the public pages tried it goes left to right and top to
  bottom, and on the Config Guardian dashboard it seems logical;
* focus is visible at the two table stops the machine had flagged: a "Minor contract" link on the
  contracts register at desktop width, in a column the table had not scrolled to, and the "Annual
  remuneration" column heading on the people register at phone width;
* signed in as a Governance auditor, focus is visible on the dashboard's "Analyze Impact" and
  "Create Snapshot" actions, whose focus indicators the machine measured at a contrast below 3:1.

A third faint indicator on the dashboard, the Gin administration theme's "Tabs display toggle" at
phone width, was not put to the person.

The person worked on a preview site built from this package at commit `cdccf2b`, with
`drupal/agora_theme` from its development branch at commit `ec2c990` — not on the build the machine
walked. That theme build carries a change first released in `agora_theme` 1.2.1, which scrolls a
table to follow keyboard focus, and two later changes that keep all four sides of a focus ring on
screen, inside tables and outside them; no release carried those two on 2026-09-25. On
`agora_theme` 1.2.0 the machine had found both table links the person confirmed only partly in view
when they received focus. Neither build is `agora_theme` 1.2.1, the newest release of the theme on
that date.

## The administrative interface

No level of conformance is claimed for it.

Every push measures one page of it: the Config Guardian dashboard, which this package installs, read
by a user holding only the Governance auditor role, which this package ships. The accessibility
statement reports what axe finds there and whose markup each finding sits in — Config Guardian's,
the Gin administration theme's and `coffee`'s — and the test fails if that set of findings changes.
No other administrative page is checked on any push.

The keyboard walk of 2026-09-23 found failures on the same surface, every one in markup this
package does not write: focus carried out of sight by `coffee`'s search box and by Config Guardian's
dependency graph, hidden buttons that Gin leaves in the Tab order, and, at phone width, targets too
small and forms too wide in Gin's interface. On the dashboard itself, the last Tab puts focus on
`coffee`'s search box above the window, where it cannot be seen. The walkthrough record, named under
*Method and date*, attributes each failure to the element that carries it.

## Not measured

Named, so that silence is not read as a pass.

* **Screen readers.** None was used. The machine recorded the role and name the browser computed for
  every stop — what a screen reader is given — and not what a screen reader says.
* **Browsers other than Chrome.** The automated check and the machine's walk both ran in Chrome.
* **Widths other than 1280 and 320 CSS pixels.** The keyboard walk used those two and nothing
  between them, such as a tablet's.
* **Administrative pages other than the dashboard.** No push checks any of them, and no conformance
  is claimed for any of them. Config Guardian's other pages that take no argument were walked once,
  by machine, on 2026-09-23.
* **The five Config Guardian pages that take an argument**: a snapshot's own page, the comparison,
  rollback, delete and export. They need a snapshot to exist, and a measurement must not create
  one.
* **The CSV distributions as tables.** The theme can draw a dataset's CSV file as a table on the
  dataset's page; no check reads the tables drawn from the files this package ships.
* **2.4.13 Focus Appearance.** It is a level AAA criterion, outside the target. The machine computed
  a figure for it, with a reading of its own where the criterion's text leaves a case open, and
  nothing here relies on that figure.
* **Consent-banner states.** No consent banner appeared to an anonymous visitor on the site the
  machine walked. A site that turns on a service needing consent will show one, and whether it
  covers the focused element was not measured.
* **Content added after installation**: pages, files, embedded maps, video players and widgets a
  site adds itself. Nothing here reaches them.
* **The sign-in page.** On a Drupal CMS site it is served by the administration theme, not by
  `drupal/agora_theme`, and no check measures it. A companion module is planned to hand it to the
  site's own theme.

## Method and date

* **Automated.** axe-core, run in Chrome by this package's own functional test on every push, as
  described under *What every push checks*.
* **Keyboard, by machine.** A script sent real key presses to Chrome. Before pressing any key it
  predicted from each page's markup where focus should go, and compared that with where focus went,
  forwards and backwards. It judged whether a stop showed an indicator by comparing the element's
  pixels with focus and without it. Each of its checks was first seen to fail on a planted defect,
  so a clean result is a measurement and not an absence of one.
* **Keyboard, by a person.** A short list of questions covering only what the machine cannot
  settle, answered on the preview site described above.
* **Record.** The machine's transcript — every stop, at both widths, in both directions — is
  `specs/006-hardening/research/2026-09-23-keyboard-measurability.md`; the person's answers are in
  `specs/006-hardening/tasks.md`, under *Keyboard walkthroughs, the person's half*. Both are in the
  project's [public repository](https://git.drupalcode.org/project/agora_transparency/-/tree/1.x/specs),
  and neither is part of this package.
* **Date.** Written on 2026-09-25, about the package as it stood that day.
