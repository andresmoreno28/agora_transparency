# Ágora · Idioms and lessons (append-only)

> **Translation note — 2026-08-21.** This file was mechanically translated from Spanish into English
> under D-017(b), which authorizes it explicitly. **The semantic content is unchanged:** no idiom was
> added, removed, renumbered or altered in meaning. The Spanish original is preserved in the git
> history as the signed record. New entries are written directly in English.

> Project gotchas and lessons. They are promoted when each unit closes. Not to be confused with ADRs/decisions.

- I-001 · This project builds on young technology (Drupal CMS, Canvas, site templates): all research
  expires. Any claim about the state of the art carries a date and a source, and is re-verified
  before building on top of it (prior-is-not-disk).
- I-002 · The marketplace's "no unstable releases and no patches" requirement turns every dependency
  into a decision, not a casual `composer require`.
- I-003 · The installation CI runs WITHOUT AI keys: any AI feature that breaks the installation
  in the absence of an API key is a design bug, not a detail.
- I-004 · A site template's repository **IS the recipe**: `recipe.yml` at the root with `type: Site`
  (case-sensitive). There is no `recipes/` with local sub-recipes, and the theme is **generated** via
  `site_template_helper`, it is not versioned. Verified 2026-08-20 against `drupal_cms_site_template_base`.
- I-005 · The starter kit **has no stable releases, only branches** — and that does **not** violate the
  dependency policy: it is copied as scaffolding, never declared in `require`. It does not enter the SBOM.
  The `no-unstable-deps` invariant must exclude it explicitly so as not to produce a false positive.
- I-006 · `www.drupal.org` may be blocked, but **`updates.drupal.org` and `git.drupalcode.org`
  usually respond**. The official release history (`/release-history/<project>/current`) gives the stable
  version, `<core_compatibility>` and `<security covered="1">` — it is the correct source for `sbom-check`,
  and it avoids writing off as "not verifiable" something that is.
- I-007 · An `exit 0` without counts proves nothing: a suite with no tests, a grep with no files and an axe
  run that never loaded the page all return 0. Always demand the number of items analyzed.
- I-008 · In this harness, the **subagents in `.claude/agents/` freeze at session start**:
  not only their registration, but also **the content of their definition**. Editing an `agents/*.md`
  mid-session has NO effect — the subagent keeps running with the old system prompt. Verified
  2026-08-20: after rewriting `orquestador.md`, the agent still did not know the new facts and
  answered according to the previous version. **Skills and commands DO hot-reload.**
  → After creating or editing agents: **restart the session** before relying on them.
- I-009 · A subagent with a clean context does not see the conversation: its definition file must be
  **self-sufficient**. If a verified fact (repo structure, SBOM versions, known false
  positives) is not written in its `.md`, for it that fact does not exist — and it will fill the gap with
  the generic rule, which is exactly how already-discarded false positives come back.
- I-010 · A permission allowlist with a **trailing glob** on a command that accepts output flags
  amounts to arbitrary file writing: `Bash(curl -s https://host/path/*)` authorizes
  `curl -s https://host/path/x -o ~/.zshrc`, and `Bash(git diff:*)` authorizes `git diff --output=<file>`.
  Rule: exact commands, never `*` as the final token, and **never allowlist a directory whose
  contents do not yet exist** (`tests/bin/*` with the folder empty authorizes scripts that have not yet
  been written or reviewed). Detected by automated security review, 2026-08-20.
- I-011 · Amendment to I-006: in this environment **Bash DOES have network access** (verified 2026-08-21: `curl` to
  `updates.drupal.org`, `git.drupalcode.org` and `www.drupal.org` → exit 0). But **it varies within the
  same session**: the same `curl` failed with exit 6 (DNS) twenty minutes before it worked. It is
  checked with a command at the start, never assumed — neither that there is network access, nor that there is not.
  Declaring "there is no network" without testing it produces plans as bad as taking it for granted.
- I-012 · `www.drupal.org/project/<X>` returns a **302 to new.drupal.org for any string**,
  including a non-existent one: a 302 does NOT prove that a machine name is free. The valid oracle is
  `git.drupalcode.org/api/v4/projects/project%2F<X>` (200 = taken, 404 = free).
- I-013 · `updates.drupal.org/release-history/<X>/current` returns **HTTP 200 with an `<error>` body**
  for non-existent projects. `curl -f` or any check by status code gives a false green:
  the XML has to be parsed and `<title>` + at least one `<release>` demanded.
- I-014 · A site template **cannot contain code of its own**: the starter kit's `RequirementsTest`
  requires **0 `*.info.yml` files** in the package, which is moreover installed in `./recipes/<name>` —
  outside the docroot, where `RecursiveExtensionFilterCallback` does not even recurse (only the root's
  `profiles/`, `modules/`, `themes/`). Themes and modules are **declared in `require`**. It corrects I-004 in its
  "the theme is generated" part: it is generated on the **working site**, not in the repo, it is development
  scaffolding, and the `extra.drupal-site-template` block is deleted before publishing.
- I-015 · `RequirementsTest` honors the `CI_ALLOW_DEV` variable: if it is defined in CI, the listed
  dependencies **skip** the pinned/dev version check. It is a gate weakening
  by design. In Ágora it is **never defined**, and there is an invariant that verifies this (T-209).
- I-016 · An unverified alarming premise poisons planning just as much as a falsely
  reassuring one: "the marketplace is DCP-only and costs $395" circulated for two units as a hard
  constraint and turned out to be **false** (free is open to any individual; the fee said "none for pilot
  and MVP"). Verify at the source before letting an external constraint shape the scope.
- I-017 · License and privacy are **structural** constraints, not finishing touches: the self-hosted
  OFL typography is files that configuration does not carry, and a font CDN is a GDPR
  liability in the EU public sector. That, and not aesthetics, is what forced the theme to be a
  separate project (D-014).
- I-018 · String-match invariants are specified as **"not DEFINED in a versioned file"**, never
  **"not mentioned"**: the tests that police a string legitimately contain the very string being
  searched for. `git grep 'CI_ALLOW_DEV'` returns a permanent, unfixable failure because
  `RequirementsTest.php` reads that variable by design. Match the assignment
  (`CI_ALLOW_DEV[[:space:]]*[:=]`), not the token. Also: `git grep` only sees **tracked** files, so a
  freshly written offender that has not been `git add`ed passes invisibly — scan the working tree.
  Found by the `tester`, wave 1, 2026-08-21.
- I-019 · Shell globs do **not** match dotfiles: `ls *.example | wc -l` returned 0 with
  `.gitattributes.example` sitting right there, and in zsh the glob aborts before `ls` ever runs. Any
  check meant to find dotfiles uses `find`. This is the same failure shape as a `grep -c` that
  returned 0 both before and after the change it claimed to verify. Found by the `tester`, wave 1,
  2026-08-21.
- I-020 · Known debt is a **task with an owner and an exit gate**, never a tolerated red light in a
  gate. If a check cannot pass under the specification in force, the check is adjusted to that
  specification (or skipped explicitly and documented), and the future task that resolves the debt is
  the one that reverts the adjustment. A red that everybody "knows about" stops being read.
- I-021 · `export-ignore` in `.gitattributes` removes files from the **packaged release only**;
  anyone cloning the repository still gets them. Hiding process artifacts from a Drupal.org project
  would require not committing them at all, not `export-ignore`. Verified 2026-08-21.
- I-022 · The XML from `updates.drupal.org` puts **every `<release>` element on a single line**
  (14 lines total, but line 13 is 23,303 characters for `drupal_cms_helper`), so a greedy
  `sed`/`grep` returns the **last** release on that line — which is the `-dev` branch, not the
  stable one: `sed -n 's/.*<version>\(.*\)<\/version>.*/\1/p` returned `1.2.x-dev` where the correct
  answer was `2.1.3`. And `python3 urllib` fails on this machine with
  `[SSL: CERTIFICATE_VERIFY_FAILED] unable to get local issuer certificate` (Python 3.13.7 from
  python.org, installed without a CA bundle). Rule: **download with `curl`, parse with `xml.etree`
  reading from stdin.** Complements I-013 (the endpoint returns HTTP 200 with an `<error>` body):
  between the two, neither the status code nor a line-oriented parse can be trusted. Verified
  2026-08-21.
- I-023 · **No public claim without a gate that backs it.** What cannot be verified is **deleted, not
  softened.** Origin: the `README.md` inherited from the starter kit promised *"a gorgeous theme
  that meets WCAG AAA standards"* in a project whose thesis is **verified** AA — and it was
  published. Rider of [andres], 2026-08-21.
- I-024 · **Inherited boilerplate outlives the task that was supposed to clean it up** whenever no
  task owns the *whole* file: T-112 owned a *section* of the README, not the README, so
  "electricians" and "WCAG AAA" sailed through the gate without the gate lying. Rule: when importing
  third-party scaffolding, do **a full sweep of the original's strings** across every published
  artefact, not file-by-file according to whoever happens to touch it.
- I-025 · Windows builds of `jq` and `python` translate `
` to `
` on **stdout**. A shell
  invariant that reads tool output into a `while read` loop and compares it against clean literals
  then fails on the *good* case — and, far worse, stops discriminating: `sbom-check` returned exit 1
  for a clean SBOM, for an injected non-existent project and for a severed network alike, so all
  three of its dirty-case criteria would have "passed" for the wrong reason. Bash's `$( )` strips
  only the trailing `
`, so the corruption hits **every line but the last** — which is exactly why
  8 of 9 projects failed and the 9th, last in the list, did not. Rule: normalise at the boundary
  (`| tr -d ''`, or `sys.stdout.reconfigure(newline='
')`), and **never read a dirty-case result
  off an already-red gate** — a red gate answers "fail" to every question, including the ones you
  did not ask. Verified 2026-08-21.
- I-026 · A preflight that checks tool **presence** is not checking tool **usability**.
  `for tool in jq curl python3; do command -v "$tool"; done` passed on the Microsoft Store `python3`
  stub, which prints *"no se encontró Python"* and exits non-zero at first real use; the failure
  surfaced later, at parse time, disguised as a data problem. Same shape as I-013 one level up: the
  cheap oracle answers "yes" for the broken case. Preflight must **exercise** the tool
  (`jq -n 1`, `python3 -c 'pass'`), never merely locate it. Corollary: I-022's stated cause
  (Python 3.13.7 from python.org, no CA bundle) no longer reproduces — the interpreter on PATH is
  now 3.12.6 behind a shim. A machine-specific idiom expires with the machine: record the **rule**,
  not the **machine**. Verified 2026-08-21.
- I-027 · `grep` has **three** exit states — 0 match, 1 no match, **≥2 error** — and a scanner that
  collapses "≥2" into "no match" converts every one of its own crashes into a clean bill of health.
  Origin: GNU grep 3.0 (shipped by Git for Windows/MSYS2) **SIGABRTs whenever `-F` and `-i` are
  combined**; bisected as `-Fin`/`-IFin` → rc 134, while `-n`, `-in`, `-Fn`, `-In`, `-Iin`, `-IFn`
  all behave; `LC_ALL=C` does not avoid it. `no-boilerplate` therefore shipped as a **total no-op**:
  all seven deny terms appended verbatim to `README.md` still gave `findings: 0`, exit 0. Note what
  did *not* hide it: grep emits **nothing on stderr** when it aborts, so `2>/dev/null` was innocent
  and removing it would not have helped — what hid it was **discarding the exit code**. The trigger
  is one grep flag; the class is every scanner in `tests/bin/`. Two consequences that outlive the
  bug: (a) a **false green survives commit, review and gate** where a false red cannot, so the
  dirty-case injection is not optional polish, it is the only thing that distinguishes an invariant
  from a decoration; (b) **any criterion of the form "invariant X reports 0 findings" is worth
  exactly as much as X's most recent dirty-case run** — cite that date when signing, not the clean
  run. Found by the `tester`, wave 3, 2026-08-21.
- I-028 · **A check whose degenerate value equals its expected value cannot fail.** Every
  broken counter in a shell returns the same thing: `0`. A `grep` that exited ≥ 2 and printed
  nothing, a `wc -l` over an empty stream, an `xargs` that word-split a filename into
  non-existent paths, a `${VAR:-0}` fallback, a `|| COUNT=0` — all of them are `0`, which is
  exactly what a check written as *"expected: 0"* is hoping to see. At `c3dc9f5` the wave 1
  gate ran 61 checks; **exactly two were silent, and they were precisely the two whose
  degenerate value equalled their expected value.** That is analysis, not luck: the other
  `grep -c` sites were probed and failed loudly, because they expected something that a
  failure could not counterfeit. Rules: (a) an *expect-zero* check must, in the same breath,
  assert that the scan actually happened — `check 'files scanned > 0' … 'yes'` is safe because
  its degenerate value is `no`, never `yes`; (b) prefer expected values a failure cannot
  produce (`yes`, `present`) over `0`; (c) when a tool's status is the only witness, capture it
  and substitute a **sentinel string** that cannot collide with any legitimate value.
  Trigger worth remembering on its own: **a space in a filename** was enough — `xargs`
  word-splits it, `grep` exits 2, the count is 0, the gate is green.
  Corollary on provenance: commit `a462235`, *"count findings without concatenating a fallback
  zero"*, removed the string concatenation and **kept the rc-blindness** — so the defect
  survived under a commit message that reads like its cure. A fix aimed at a symptom leaves the
  defect *and* removes the evidence that it is still there. Found by the `tester`, wave 3,
  2026-08-22.
- I-029 · **An invariant's product is a verdict; a gate runner's product is a count** — so they
  fail differently, on purpose. An invariant that cannot scan **FATALs and stops**: a verdict it
  cannot support is worth nothing. A gate runner **never aborts**; it degrades the individual
  check to `FAIL` via a sentinel and preserves the `N checks - M failures` line. Reason: an
  abort mid-run does not produce a red gate, it produces a **smaller gate that still looks
  green** — the `6 checks - 2 failures` preflight abort that made the wave 1 record
  non-reproducible is the same shape, and it was reproduced accidentally again on 2026-08-22 by
  a truncated `PATH`. Sentinels must be unable to collide with any legitimate expected value
  (they begin with `<` and contain spaces), comparison must happen on the raw value with
  truncation applied **only** at display time, and a sentinel `FAIL` must stay visually
  distinguishable from an ordinary `FAIL`, so a reader can tell *"this check failed"* from
  *"this check could not run."* Recorded 2026-08-22 so that a future contributor enforcing
  uniformity does not "fix" the runners into aborting.
- I-030 · **An invariant that scans `HEAD` cannot be certified green by a lane forbidden to
  commit — and that is the design working, not friction to remove.** `no-boilerplate` runs two
  passes, `git show HEAD:<path>` and the working-tree copy, because the packaged artefact is
  literally `git archive HEAD`: that is what ships. So when a lane cleans a packaged file under a
  "do not commit" mandate, the HEAD pass necessarily still sees the old content and the invariant
  correctly reports findings; the lane must hand off at exit 1 **stating the predicted
  post-commit result**, and a different actor commits and re-runs. The tempting fix — make the
  invariant worktree-only — is a silent weakening: a boilerplate string could then sit **committed**
  while the invariant went green the moment someone edited their local copy. What looks like a
  collision with *"the desarrollador does not close its own task"* is that rule enforced by the
  tooling instead of by discipline. Rule: for packaged-file work, "done" is a **two-step verdict**,
  and the hand-off carries a falsifiable prediction rather than a promise. Recorded 2026-08-22.
- I-031 · **`${VAR:-0}` is safe when zero means FAIL and dangerous when zero means PASS — the same
  three characters, opposite consequences.** I-028 warns about fallback zeros, and it is right:
  `${HITS:-0}` and `|| FINDINGS=0` turned broken scanners into clean bills of health, because
  there `0` was the *expected* value. But in a guard written `[ "$COUNT" -eq 0 ] && FATAL`, a
  blank `COUNT` does not fail the comparison — it makes `[` exit **2** with
  `integer expression expected`, the branch is **not taken**, and execution walks straight past
  the guard. There, defaulting to `0` is the safe direction: it trips the guard. Rule: before
  adding or removing a `:-0`, ask what `0` *does* at that line, never what it looks like. Better
  than either: make the value incapable of being blank in the first place — `wc -l`, not
  `grep -c` — which is the T-316/R2 move of deleting a failure mode rather than guarding it.
  Recorded 2026-08-22 so that a reader applying I-028 mechanically does not "fix" a guard into
  being bypassable.
- I-032 · **The third species of false green: the instrument works, the method works, and the
  specimen never arrived.** I-027 and I-028 cover a broken *instrument* (the host's `grep` aborts)
  and a broken *method* (a scanner whose degenerate value equals its expected value). This is
  neither: `/tests export-ignore` was a correct packaging decision, `COMPOSER_MIRROR_PATH_REPOS=1`
  was a correct isolation decision, PHPUnit behaved exactly as documented, and the workflow was
  written by the Drupal Association. **Every component worked as designed, and the suite still
  reported nothing for nine consecutive green runs** — because the defect lived in the **seam**
  between two correct decisions, and no component owns a seam. That is the diagnostic: species 3 is
  the only false green in which nothing is broken, which is why no amount of hardening `tests/bin/`
  could ever have found it. Rules: (a) every gate asserts **what it measured**, by count and by
  identity, not merely that the measurement ran — `find … -name '*Test.php' | wc -l` before
  phpunit, not after; (b) whenever two correct decisions meet at a packaging, checkout or install
  boundary, **the seam gets its own check**, owned by name; (c) prefer the tool's own guard where
  one exists — `--fail-on-empty-test-suite` is PHPUnit's, verified in `ShellExitCodeCalculator`,
  and it survives every runner we might migrate to. Found by the `ejecutor`, T-212, 2026-08-22.
- I-033 · **`export-ignore` has more consumers than the release tarball, and one of them is
  Composer.** I-021 is true but incomplete: *"only affects the packaged tarball, never a clone"*
  describes drupal.org's packaging, and it made `export-ignore` look inert outside release time. It
  is not. Composer's `PathDownloader`, when `COMPOSER_MIRROR_PATH_REPOS=1`, mirrors through
  `ArchivableFilesFinder`, which chains `GitExcludeFilter` and `ComposerExcludeFilter` — the same
  filters `composer archive` uses — so **any** consumer of Composer's archive machinery honours
  `.gitattributes`. Two consequences: (a) moving an exclusion from `.gitattributes` to
  `composer.json`'s `archive.exclude` changes **nothing**, because both filters sit in the same
  chain; (b) a path-repository mirror is a faithful simulation of the packaged artefact — which is
  a **feature** worth keeping, and the reason the fix is to copy tests back in rather than to stop
  mirroring. Verified at source 2026-08-22: `PathDownloader.php:157-160`,
  `ArchivableFilesFinder.php:26,60-61`.
- I-034 · **A green CI badge is an exit code, so I-007 applies to CI itself.** Ten runs were read as
  green; nine had executed nothing. The trigger that should have forced a log read was visible in
  the diff: the commit that broke it, `975b263`, changed **packaging** — not code, not tests. Rule:
  whenever a change touches how the artefact is built, checked out, packaged or installed —
  `.gitattributes`, `.gitignore`, installer paths, path repositories, `composer.json` `extra` —
  **read one full CI log end to end before trusting the next green**, and quote the count. A badge
  answers "did it exit 0", never "what did it do". Corollary on why nobody noticed: the three
  published site templates ship **no tests at all**, so a site-template pipeline that runs zero
  tests is the ecosystem's normal appearance. **When the broken state is indistinguishable from the
  ecosystem norm, only a count can tell them apart.**
- I-035 · **Before engineering around a defect in inherited scaffolding, check whether the canonical
  pipeline already solved it.** The GitHub workflow's blindness looked like a problem to design
  against; verification at source showed `gitlab_templates` has carried `.recipe-replace-symlinks`
  for exactly this — it never composer-mirrors a recipe, and it force-copies `tests/` from the clone
  regardless. That reframes the fix from *architecture* to **a disposable shim with an expiry date**,
  and it argues for prioritising the move to drupalcode over hardening a surface that is scheduled
  for deletion. Rule: locate the defect's blast radius before sizing the fix — *"does the place we
  are going have this problem?"* is a cheaper question than any solution to it, and it is answerable
  with `curl`.
- I-036 · **Text is not disk until it is persisted, and a dispatch that cites unpersisted IDs is a
  dispatch against phantoms.** The coordinator dispatched T-213 citing T-213, T-214(a), D-020(d)
  and I-032 **before writing any of them to disk**; the `desarrollador` stopped, reported the
  divergence and refused to implement. Cost: one round trip. Value: the reconciliation rule
  (*"disk wins over any prompt"*) **caught its own coordinator**, which is the strongest evidence
  yet that it is load-bearing rather than ceremonial — a rule only its author can bypass is not a
  rule, it is a habit. Order, always: receive text → **persist and commit** → dispatch against the
  IDs that now exist. Corollary for subagents: an ID that cannot be found on disk is a **stop
  condition**, never something to infer from context. Recorded 2026-08-22.
- I-037 · **A matching count is not an attribution.** The log attributed a deprecation —
  *"`simpleConfigUpdate` on config entities … throws an exception in drupal:12.0.0"* — to
  `InstallTest::testInstall (2 times)`, and Ágora's `recipe.yml` uses `simpleConfigUpdate` exactly
  **twice**. Two and two: a coincidence one step away from being written down as a finding against
  our own file. It was settled by reading the **trigger condition** at source instead —
  `SimpleConfigUpdate::apply()` fires only when `getEntityTypeIdByName($configName)` is truthy, and
  Ágora's two targets (`system.site`, `system.theme`) are simple config objects with no entity
  type, so they **cannot** trigger it. The recipe applies ten upstream recipes; any of them can
  emit twice. Rule: attribute by **mechanism**, never by arithmetic — ask *what makes this fire*
  and check whether our input satisfies it. A number that matches is a prompt to verify, not a
  verification. Recorded 2026-08-22.
- I-038 · **I-028 has escaped `tests/bin/`: a log grep can be vacuous exactly as a counter can.**
  T-215's hand-off cited *"`FATAL: tests did not reach …` appears 0 times, so it failed for the
  correct cause."* But the dirty case had **deleted the step that prints that string**, so it could
  not appear — not even as CI's echo of the step's own script, which is where it appears once in
  the clean run. The check's degenerate value equalled its expected value, in a `gh run view --log`
  pipeline rather than in a shell counter: the sixth appearance of the class and the first outside
  our own scanners. Rules: (a) a claim of the form *"string X is absent from the log"* must be
  paired, in the same breath, with a positive claim a broken run could not counterfeit — here,
  `No tests executed!` **present** and phpunit's exit status naming the flag; (b) before citing an
  absence, ask whether the code that would have produced the string was even reachable in that run.
  A CI log is an instrument like any other, and I-007 applies to reading it. Recorded 2026-08-22.
- I-039 · **On an empty GitLab repository the default branch is *resolved*, not *stored* — and the
  resolution is a fallback chain a later branch can hijack.** `project/agora_transparency` reported
  `default_branch: main` while holding zero refs. Gitaly's `GetDefaultBranch` tries, in order:
  HEAD's target → `refs/heads/main` → `refs/heads/master` → **the first ref of
  `git for-each-ref --count=1 refs/heads/`**, which git sorts by refname bytewise when `--sort` is
  absent. Three consequences we would have walked into: (a) push only a working branch and it
  silently becomes the public default; (b) push `1.x` and `001-fundacion/scaffolding` together and
  **`001-…` wins**, because `'0'` (0x30) precedes `'1'` (0x31) — *push order is irrelevant, byte
  order decides*; (c) create a `main` branch at any later date and it outranks the release branch
  two steps earlier in the chain. The only stable state is a **pinned** HEAD, set in GitLab →
  Settings → Repository → Branch defaults. And the API cannot tell you which state you are in:
  `default_branch` reports the *resolution*; `git ls-remote --symref <remote> HEAD` reports the
  *symref*. When a value can be either stored or derived, find the instrument that reads the store.
  Verified locally with `sort` before acting. Recorded 2026-08-22.
- I-040 · **A branch name is a CI trigger, so a badly-named branch produces a seventh species of
  false green: no red, because no run.** `gitlab_templates` gates branch pipelines on
  ``($CI_COMMIT_BRANCH == $CI_DEFAULT_BRANCH || $CI_COMMIT_BRANCH =~
  /^[78]\.x-\d+\.x$|^[\d+.]+\.x$/) && $CI_PROJECT_ROOT_NAMESPACE == "project"``. Push to
  `001-fundacion/scaffolding` and **nothing happens at all** — no job, no pipeline, no badge, no
  notification. On a project page that is indistinguishable from "nothing is broken". The same guard
  means pushes to an **issue fork** (namespace `issue`) never run either: only the merge-request
  rules fire, which is why an MR must be opened *early*, not when the work is finished. Two rules:
  (a) before believing a branch is tested, confirm the branch **name** satisfies the workflow rules,
  reading `include.drupalci.workflows.yml` at source — the public documentation page shows an older,
  simpler rule set **without** the release-branch regex or the namespace guard; (b) I-007 again:
  "the pipeline is not red" is worthless until you have counted the pipelines that ran.
  Recorded 2026-08-22.
- I-041 · **A drupal.org doc URL that 302s to `new.drupal.org` and then 404s is a stale redirect,
  not a deleted page — the page usually lives at a different slug on `www`.** A previous turn
  reported the branch-naming convention as unsettleable and declined to name one, on the strength of
  a 404. The page exists, updated 2026-01-11, at `…/git-for-drupal-project-maintainers/
  release-naming-conventions`. The error was guessing the slug from the page's *title* and then
  treating the 404 as evidence about the *content*. Method that works, and is cheap: fetch the
  **parent** guide page, which does resolve, and enumerate its child links — the real slug is in
  that list. Corollary: `new.drupal.org` and `www.drupal.org` redirect to each other page by page in
  both directions, so a 302 in either direction says nothing about where the content is. **Never
  conclude "undocumented" from one 404 on a URL you constructed.** Recorded 2026-08-22.
- I-042 · **A capability read from documentation is not a capability present in the installation
  that will run.** Two instances in one turn, same root. `@cspell/dict-es-es` is documented, real,
  and **not among the 59 dictionaries `@cspell/cspell-bundled-dicts` ships** on the Drupal runner —
  a plan was built on `"language": "en,es"` resolving to something, and it resolves to nothing.
  Then the word list's own header claimed cspell's `stripCaseAndAccents` folds accents onto one
  entry; `fundacion` was listed and <!-- cspell:disable -->`fundación`<!-- cspell:enable --> was **still flagged** in pipeline 933311 (scoped out, not added to the word list: an entry there would make the accented form correct everywhere, forever). Method:
  before planning against a capability, **enumerate the installed set** — the bundled-dicts
  manifest, or the job's own `--version`/`-i`/`-e` echo lines, which every `gitlab_templates` job
  prints for exactly this reason. Corollary, and the harder half: when documented behaviour turns
  out false, correct the claim to what was **observed** and mark the untested neighbouring claims
  as untested rather than quietly leaving them. Recorded 2026-08-23.
- I-043 · **`allow_failure: true` is the eighth species of false green: the job ran, the job
  failed, and the pipeline was green.** Pipeline `933270`: seven jobs, `cspell` **failed**, pipeline
  `success` in 309s, because four of seven carried `allow_failure: true` by upstream default.
  Distinguish it from its siblings: I-040 is *no run at all*; I-032 is *ran and found nothing*;
  this one is *ran, found it, and the finding was discarded on the way up*. What makes it the
  nastiest of the three is that **both readers are reading correctly** — open the job and it is red,
  look at the pipeline and it is green, and neither is lying. Rule: never read a pipeline's
  `status`; read the job list with per-job `status` **and** `allow_failure` (D-023(5)). Second half,
  which cost the most here: **upstream permissiveness is invisible in your own config** — cspell was
  non-blocking by an inheritance stated in no file we own. Recorded 2026-08-23.
- I-044 · **A decision that names a task number must not reach disk before that task does.** D-023(6)
  created the cspell exception with an owner and an exit gate — *"Owner **T-226**; deleting the line
  is the exit gate"* — and **T-226 was never written to `tasks.md`**. It survived only because the
  same turn kept going. Had the turn ended there, `grep T-226 DECISIONS.md` would return a confident
  citation and `grep T-226 tasks.md` would return nothing: an accountability record whose
  accountability is a dangling pointer. This is **I-036 (*text is not disk*) applied to forward
  references**, which is the direction nobody was watching. Guarded by `tests/bin/cited-tasks-exist`
  (T-223), which found exactly one dangling citation on its first run. Recorded 2026-08-23.
- I-045 · **A green linter is a statement about the set it opened, and the set is usually not
  printed.** `cspell` says `Files checked: 36`; the repository tracks **63**. `phpcs`, `phpstan` and
  `eslint` say nothing at all about how many files they touched — phpcs emitted **zero bytes**
  between its invocation and its verdict. Every one of them is now **blocking** (D-023), which
  raises the stakes: **a blocking check over an unknown denominator is not a stronger gate than a
  permissive one, it is a more confident one.** Rule: for every gate job, record the denominator
  beside the result; where the tool will not print one, make it print one through a documented
  variable (`-p` for phpcs, `_CSPELL_SHOW_PROGRESS` for cspell) — never by inferring it.
  *"0 issues in N files"* is a gate; *"0 issues"* is a badge. This is **I-028's escape into upstream
  jobs we do not own**, and it gets its own number for the same reason I-038 did: the class keeps
  finding new surfaces, and folding each one back into the parent hides the spread.
  Recorded 2026-08-23.
- I-046 · **On Windows, DDEV's own recommended setup is docker-ce **inside WSL2**, not Docker
  Desktop — and the two conflict.** T-401 sat blocked for a day on *"the Docker daemon is
  unreachable"*, and the daemon was running the whole time: it was `docker-ce` inside a WSL2 Ubuntu,
  with DDEV **already installed there too**. What was unreachable was Docker Desktop's named pipe,
  which is a different product; its `docker-desktop` WSL distro sat stopped beside the working one.
  `doctor` reported *"CLI present, daemon unreachable"* and that was accurate about the Windows
  CLI — and useless, because it never asked whether a daemon existed **somewhere else on the same
  machine**. Rules: (a) before recording a capability as absent, enumerate where it might live —
  `wsl -l -v`, then `wsl -e bash -lc 'docker info'`, two commands; (b) run the project **inside** the
  WSL filesystem (`~/project`), never from `/mnt/c`, which DDEV documents as slow and
  permission-prone. Related to I-042: a capability absent from the installation you looked at is not
  a capability absent from the machine. Recorded 2026-08-23.
- I-047 · **An "observed" fact is a dated measurement, and ours decayed within hours.** The README's
  CI section and `CLAUDE.md`'s Gate A block were both written to be unfalsifiable-by-badge — *"read
  from the API, not from the UI, not from the badge"* — and both were false by the end of the same
  day, falsified **not by a regression but by our own next commit**, which added the eighth job the
  README said did not exist. I-024 covers *inherited* boilerplate outliving its cleanup; this is the
  mirror image, and harder, because the text is ours, was true, and was written with more rigour
  than the text around it. Rule: any block quoting a pipeline ID, a job count or a file denominator
  is **research under I-001** — it carries a date, and the commit that changes what it describes is
  the commit that updates it. Corollary on blast radius: `README.md` **ships** and `CLAUDE.md`
  **governs**, so a stale observation is either a false statement to a user or a false instruction to
  the next session. There is no harmless copy. Recorded 2026-08-23.
- I-048 · **Evidence abundant on the wrong axis reads as proof — the ninth species, and the only one
  where the evidence is genuinely strong.** T-401's criterion had three clauses: `sql:drop`,
  reinstallation, and *"the template appears in the selector"* — the same sentence `plan.md` §1 names
  as the unit's definition of done. What was produced was a real Drupal 11.4.5, a clone of the
  **canonical** remote, 78 recipe steps, HTTP 200, 58 modules and `Tests: 3, Assertions: 38`. Every
  number real, every number verified, and **not one of them about the selector**, which could not
  have appeared at all because the flow started from `drupal/recommended-project` rather than the
  Drupal CMS installer. Distinguish it from its siblings: I-027 is a broken instrument, I-028 a
  degenerate value, I-032 an absent specimen, I-043 a discarded finding — here instrument, method
  and specimen were all sound, and the measurement was simply **of something adjacent**. That is what
  makes it hard: quantity of evidence is the heuristic everyone uses for whether to look closer, and
  this species maximises it. Rule: check a criterion **clause by clause**, marking each proven or
  not, *before* reading the evidence as a whole. A criterion with three clauses and one impressive
  proof is a criterion with **two unmet clauses**. Recorded 2026-08-23.
- I-049 · **Agentic speed compresses the work and leaves the round-trips untouched, so latency
  becomes the schedule.** Unit 001 ran ~58 commits across three days, and throughput inside a turn
  was never the constraint — waves 1 and 3, roughly 28 tasks, landed in about a day. What set the
  wall clock was the **count of round-trips**: a pipeline (minutes), a human signature (hours), an
  environment discovery (**a day** — I-046). The single largest identifiable loss in the unit was a
  `doctor` probe that answered a question nobody had asked it, and no amount of parallel lanes or
  shorter records would have touched it. Rule: optimise the **number** of round-trips, not the speed
  of the work between them — batch every pipeline-visible change into one push; **pre-delegate
  mechanical signatures at wave start** rather than discovering the need mid-wave (this unit invented
  `[ejecutor] under [andres]'s delegation` three separate times before it became a habit); and run
  every environment probe **before** the wave that depends on it. Corollary this unit paid for twice:
  an *unknown* environment is a round-trip of unbounded length, so probing it is never the thing to
  defer. Two things worth naming beside the round-trips: the unit had quality gates and **no scope
  gate** — planned at ~25 tasks, closed at 50-plus, every addition individually justified and none
  ever asked *"does this belong in 001 or in 006?"*; and the dead weight in the record is not its
  length but the **evidence blocks inside signed tasks** that restate what a linked pipeline log
  already says. Recorded 2026-08-23.
- I-050 · **A job that exists in the template is not a job that exists in your pipeline, and a job
  in your pipeline is not a job that ran.** D-009 chose to mount accessibility inside "the Nightwatch
  job that `gitlab_templates` already ships and already supports" — true of the template, and the
  canary (T-228) found the job absent from a real pipeline: `.nightwatch-tests-exist-rule` gates it
  on `exists: tests/src/Nightwatch/**/*.js`, and we have no such file, so it never materialises.
  Meanwhile the opt-in we did set produced `Drupal CMS`, which arrived **`when: manual` and
  `allow_failure: true`** — present in the job list and not run. Three states that all read as "the
  job is there" and are not the same thing: **defined upstream · materialised in this pipeline ·
  actually executed**. Rule: when a decision rests on an upstream job, verify it at the third level
  before building on it, and read the job's **gating rule**, not its existence — the rule is where
  the answer lives. Corollary that made this cheap: a canary is worth running the moment it is
  possible, not when the feature needs it. This one cost one merge request and moved D-009 from an
  assumption to a measurement with a named next step. Recorded 2026-08-23.
  **Follow-up, same day, and it is the reason the canary was worth running:** the manual job was
  triggered and succeeded, and the useful evidence was **not** its result but its **container
  list** — `database`, `selenium`, `chrome`. The shared contrib runners provision a real browser.
  A job's services tell you what the runner *can* do; its assertions tell you only what this test
  chose to do. Read both.
- I-051 · **A local check that is documented as an approximation stops being read, and then the
  gate it approximates fails unwatched.** The README offered a bare `cspell` command and said, in
  writing, that it *"can disagree with the pipeline in both directions"*. It did — with no
  `.cspell.json` in the repository it loaded neither the project dictionary nor Drupal core's two,
  so it printed **905 findings where the job finds none**. Nobody reads a 905-line report, so
  nobody read it, and `cspell` was **red on three consecutive pipelines** (`934242`, `934297`,
  `934329`) while three commits were reported as landing clean. The honest warning did not help:
  **a check with a known false-positive rate is not a weak signal, it is an absent one**, and it is
  worse than no check because it occupies the slot where a real one would go. Distinguish it from
  I-034 (*the badge is an exit code*): there the signal was read and meant less than it looked;
  here the signal was never readable at all. Fix: `tests/bin/spellcheck` fetches the job's real
  inputs and was verified against the very pipeline that had been failing. Rule: **a local
  pre-flight either reproduces its gate or it is deleted** — shipping one with a disclaimer is
  choosing the disclaimer over the gate. Recorded 2026-08-24.
- I-052 · **An in-file `cspell:ignore` directive ends at the newline, not at the end of the HTML
  comment.** 192 words wrapped across 22 lines inside one `<!-- cspell:ignore … -->` block silenced
  **only the first line**; the other 21 lines were parsed as prose, so the same words were flagged
  as the directive that was supposed to allow them. It fails quietly in the direction that looks
  like success — the block is visibly *there*, and the count merely goes down. One directive per
  line. Recorded 2026-08-24.
- I-053 · **The shape a config file holds is not always the shape the API that writes it takes.**
  `config/` stores a `list_string` field's `allowed_values` as a **structured** list of
  `{value, label}` maps; `ListItemBase::structureAllowedValues()` expects the **simple**
  `value => label` map and structures it. Handed the structured form it structures it twice and
  throws `The configuration property settings.allowed_values.0.label.0 doesn't exist` — a message
  that names a key nobody wrote and points at nothing a reader can act on. Cost one failed run in
  T-614. The lasting hazard is the **reverse** trip: someone reading `config/` later, seeing the
  structured form, and "fixing" the model script to match it — which breaks it again, in the
  direction that looks like consistency. Rule: **when a value round-trips through a tool, verify
  the shape on BOTH sides before assuming they are the same shape.** Recorded 2026-08-25.
- I-054 · **A pre-flight that is QUIETER than its gate is worse than one that is noisier, and the
  two failures look identical from the inside.** Three local checks disagreed with their gates in
  one day. `phpcs`: the 80-character limit applies to **comment lines only** and counts
  **characters, not bytes**, so a naïve `awk` reported 27 where the gate reported 2. `stylelint`:
  the CI job **symlinks core's `.prettierrc.json` before running** and a local run does not, so the
  local pass and the CI failure were both correct. `cspell`: the replica fetched core's
  dictionaries from the **11.x development branch** while CI installs **stable** core —
  `vincentlanglet` is in one and not the other, so the local run said *"30 files checked, 0
  issues"* and the blocking job then failed on exactly that word.
  **The first two were noisy; the third was quiet, and only the quiet one is dangerous.** A noisy
  pre-flight wastes attention. A quiet one **certifies a red push** — it does not merely fail to
  help, it actively tells you the thing it cannot see is fine.
  Rule, and it is stronger than *"keep the replica in sync"*: **a replica must CHECK its own
  inputs against the gate's, and stop when they diverge.** `tests/bin/spellcheck` now reads
  `CORE_STABLE` from `gitlab_templates` at source, derives the branch its dictionaries live on,
  prints the parity line on a match and **exits 2 on a mismatch**. Offline is a **third state**,
  reported as `UNVERIFIED`, never as a pass. The test of such a guard is not that it passes today:
  it is that when Drupal ships 11.5.0 the script **says so and stops**, instead of silently
  reading a larger dictionary. ⚠️ And note the shape of the trap it closes — pointing the branch
  back at `11.x` **would not look like a mistake, it would look like an upgrade**.
  Recorded 2026-08-25.
- I-055 · **The fourth rung: defined upstream · materialised in this pipeline · COLLECTED BY THE
  HARNESS · actually executed.** I-050 named the first three. T-509 found the fourth, and it is the
  one that fails most quietly, because the job is *present and green*.
  **Two globs have to match and only one of them is the CI rule.** CI materialises `nightwatch` on
  `exists: tests/src/Nightwatch/**/*.js` (`include.drupalci.main.yml`, the `.nightwatch-tests-exist`
  rule) — **any** path under that tree. But Drupal's own `nightwatch.conf.js` builds `src_folders`
  by walking `$DRUPAL_PROJECT_FOLDER` and keeping **only** paths containing
  `Nightwatch/Tests`, `Nightwatch/Commands`, `Nightwatch/Assertions` or `Nightwatch/Pages`.
  So a test at `Nightwatch/Accessibility/axe.js` — **the path this project's own signed task row
  specified** — passes the first and fails the second: **the job appears, collects nothing, runs
  zero tests and reports success.** Nothing in the job list, the badge or the status field
  distinguishes that from a real pass.
  ⚠️ **The rule: read where the HARNESS looks, not only where CI puts you.** A job's `exists:`
  condition tells you the job will run; it says nothing about whether the runner inside it will
  find your file. Those are two different programs reading two different globs, and only one of
  them is in the file you are editing.
  Corollary, and it is why this is a species rather than an incident: **the same shape exists
  wherever a CI rule and a test-runner's discovery disagree** — PHPUnit's `testsuite` directories
  against `exists: '**/tests/**/*Test.php'` is the identical pair, and `--fail-on-empty-test-suite`
  exists precisely because someone met this rung before. Recorded 2026-08-25 (T-512).
- I-056 · **A DDEV rig cannot see a base-path bug, because a DDEV rig is the environment in which
  that bug is invisible.** drupalci serves the docroot **in a subdirectory** — its own access log
  opens with `GET /web → 301`, then `GET /web/ → 200` — so Drupal's base path there is `/web` and
  every internal href carries it. DDEV serves the docroot at the server root, base path empty. A
  functional test asserting `a[href="/publications"]` is therefore **green locally by
  construction** and is only ever tested by CI. It cost this project two red pipelines and an
  afternoon.
  ⚠️ **The premise of the investigation was wrong, and that is the transferable part.** The
  dispatch asked *"why is the menu link absent on a clean install"*. **It was not absent.** The
  runner's rendered HTML had all eight links, correctly nested, with the right titles — under
  `/web`. Hypothesising about derivative discovery, router ordering and cache state would have
  been reasoning about a thing that never happened.
  ✅ **What answered it in one step: a failed drupalci job uploads its `browser_output` HTML as an
  artifact.** `curl -sL https://git.drupalcode.org/project/<p>/-/jobs/<id>/artifacts/download`.
  **Look at what the browser actually saw before theorising about why it did not see it.**
  *The corroboration was arithmetic, not a hunch:* the failure reported **1654** assertions where
  local reported 1662, and Mink's `WebAssert` methods **throw instead of counting**, so the only
  counted assertions after the abort were the eight in the loop it aborted. 1662 − 8 = 1654 —
  the gap named the exact loop.
  **Mechanical rule: a functional test never writes a literal internal path into a selector; it
  writes `base_path() . $path`.** And the general form, now on its **third occurrence in this
  unit** (I-051, I-054, this): **when a local pass and a CI failure are both correct, the
  difference IS the finding** — do not look for which one is lying. Recorded 2026-08-25.
- I-057 · **`awk`'s `length` counts bytes or characters depending on the LOCALE, not on the
  implementation — and the same binary in the same shell gives both answers.** Measured 2026-08-25
  by T-803's probe: gawk 5.0.0 returned **4** for a 3-character accented string with `LANG` unset
  and **3** under `LANG=en_US.UTF-8`. The container's **mawk 1.3.4 returns 4 either way**, and a
  forced UTF-8 locale does not move it.
  ⚠️ **Why this is not a footnote.** The 80-character rule that turned this project's gate red
  twice is a **character** rule (`Drupal.Files.LineLength`), and the obvious local check for it is
  `awk 'length>80'`. That check is therefore **correct or incorrect depending on an environment
  variable nobody sets deliberately** — and on a host where `LANG` is unset it is wrong while
  looking right. The pre-flight in CLAUDE.md counts characters in Python for exactly this reason.
  **Rule: never infer one from the other. Measure both, print both.** The probe reports the
  ambient locale's answer and a forced-UTF-8 answer side by side, because either alone is a
  half-measurement that reads like a whole one. This is I-027's family — a shell tool behaving
  differently than the reader assumes — with the twist that here **the tool is not even the
  variable**. Recorded 2026-08-25.
- I-058 · **Pushing is not finishing, and a resolution that has already failed twice is not a
  fix.** On 2026-08-25 the coordinator pushed T-603 without reading its pipeline, then signed
  T-512 and **tagged the theme's `1.0.0` on top of a red gate**. It wrote down the lesson —
  *"pushing is not finishing; finishing is having read the pipeline's job list"* — and then, within
  hours, **pushed T-801, T-802 and T-803 without reading any of them. All three were red.**
  ⚠️ **The failure is not forgetfulness, it is that the check had no cost of omission.** Every
  other rule in this project is enforced by something that fails: the gate runners, the drift
  detector, the parity check, the deny lists. *"Remember to look"* was the only one enforced by
  intention, and it is the only one that broke twice in a day.
  **The fix is `tests/bin/watch-gate`**: one command, short enough to run every time, that reads
  the **job list** from the API — never the status field, which has reported `success` over a
  failed job in this very project (I-043) — applies D-023(5) and **exits non-zero when the rule
  is not met**. It decides nothing; it makes *"I pushed"* into *"I know"*.
  Rule: **when a discipline fails twice, stop restating it and give it an exit code.** A habit
  that only works when someone is paying attention is a habit that fails exactly when they are
  not — which is at the end of a long session, immediately after something went well.
  Recorded 2026-08-25.

- I-059 · **A justification is checked for EXISTING, never for being TRUE — so a reason can rot
  into fiction with the gate green throughout.** `sbom-check` requires, for every contrib
  dependency, a line in `DECISIONS.md` carrying both a `D-NNN` token and the package name. D-018
  justified `drupal/site_template_helper` as the plugin that *"generates the `blank` theme declared
  in `extra.drupal-site-template`"*. Wave 7's swap **deleted that key**. The line still existed, so
  the invariant stayed green for days while describing a block that was no longer in
  `composer.json`. Rule: **a mechanical check can enforce that a reason was written and cannot
  enforce that it still holds** — which is the class of defect an audit exists for, and the T-806
  audit is what found it. Do not answer this by making the checker smarter; answer it by keeping
  the periodic read. Recorded 2026-08-26.

- I-060 · **A closure chain can be unsatisfiable on its own terms, and every row in it will look
  fine individually.** Unit 002 ended with T-805 blocked by T-801…T-804, T-806 blocked by T-805 and
  T-807 by T-806. T-804 **could not run at all** — it wanted a GitHub visual-regression workflow and
  no GitHub repository exists for the theme. So T-805 could never start, and nothing after it ever
  could. Each row read as reasonable; the *graph* was impossible, and nothing in the process reads
  the graph. Rule: **when a task cannot run, the blocker lists naming it are already broken** —
  resolve it explicitly by deferring with a named prerequisite, never by quietly proceeding as
  though the blocker were met. This is the third impossible criterion this unit produced.
  Recorded 2026-08-26.

- I-061 · **Two pipelines at the same job count are not running the same jobs, and the count is the
  part that invites the mistake.** On 2026-08-26 both repositories were observed at **nine blocking
  jobs**. Three of the nine differ: the theme runs `nightwatch` and `stylelint` and **no `phpunit`**;
  the site template runs `phpunit` and `Drupal CMS` and no `stylelint`. A shared floor of `jobs >= 9`
  is therefore satisfied by two different sets, and *"both are at nine"* is not the statement *"both
  run what they need to run"* — the theme has no PHP tests at all, which the number conceals and the
  **names** reveal. Rule: **a floor catches a pipeline that materialised almost nothing; only an
  observed job LIST catches a single missing job.** Keep both, and never let the count stand in for
  the list. Recorded 2026-08-26.

- I-062 · **Views renders no `<table>` at all for an empty result set, so a table test with no rows
  passes against a page that has no table on it.** Measured while building coverage for the theme's
  `views-view-table.html.twig` override: with every node unpublished the page still returns **HTTP
  200**, the Views wrapper is still in the DOM, and `<table>`, `<caption>` and the scroll wrapper
  are **all zero**. An axe run over that page reports *no violations* — truthfully, and about
  nothing. Rule: **assert the denominator before asserting the property** — `rows >= 3` first, then
  the markers. This is I-028's degenerate case wearing a Views costume. Recorded 2026-08-26.

- I-063 · **A fix that is reviewed and committed is not a fix that ships.** The theme's Views
  table override was committed at `dbbe934` and treated as resolved. The site template resolves
  `drupal/agora_theme` at `^1.0`, and the `Drupal CMS` job's log says what that means in practice:
  `Locking drupal/agora_theme (1.0.0)` — **the release in which the file does not exist at all**. So
  every install, including the one 1717 assertions ran against, got the pre-fix theme, and the fix
  was in the repository and in no release. Rule: **for a dependency resolved from a package server,
  the unit of shipping is the RELEASE, not the commit** — and the resolved version is printed in the
  install log, so this is checkable rather than inferable. Recorded 2026-08-26.

- I-064 · **The tool written to end false greens shipped with a false green in it, and the shape
  of the hole is the lesson: a verdict that does not name its subject is not a verdict.**
  `tests/bin/watch-gate --wait` resolved HEAD **once** and watched that sha to the end. Push again
  while it waits — the normal rhythm of a working session — and one of two things happens. The
  visible one is a **false red**: GitLab auto-cancels the superseded pipeline, so the script
  reported `NOT GREEN` over a commit nothing was wrong with, twice; a red that has to be explained
  away teaches whoever reads the next one to explain it away too. The dangerous one is the same
  defect with the timing reversed — if the watched pipeline finishes *before* the newer push
  registers, the script prints **GREEN for a commit that is no longer HEAD**, while the code
  actually on the branch has been tested by nothing. Rule: **any check that reports on "the current
  state" must re-read what current means on every iteration, and announce it when it moves.**
  Recorded 2026-08-26.

- I-065 · **Enumerating states from memory produces a list that is right about the states you have
  seen and silent about the one you have not.** The fix for I-064 handled `canceled` and missed
  `canceling` — GitLab cancels in **two** steps. A run that reached the API mid-transition returned
  six jobs `canceled`, two `canceling` and one `success`, fell through the terminal-state case and
  delivered a verdict about a pipeline that had not finished being cancelled. Both halves of this
  fix came from **an observed run**, neither from reasoning; the enumeration written from memory was
  wrong within an hour of being written. Rule: **when a check switches on an external system's state
  names, get the list from the system, and treat every unlisted state as non-terminal rather than as
  failure.** Recorded 2026-08-26.

- I-066 · **The step an implementer flags as "reasoned, not run" is the step that breaks.** The
  theme's Views-table coverage was delivered with one honest caveat — `drupalInstallModule(module,
  force)` had been reasoned about rather than executed, because the host has no chromedriver. That
  is precisely and only what failed in CI: the `/admin/modules` confirm form appeared, the module
  never installed, and **9 of 11 assertions failed downstream of that one step**, every one of them
  reporting a missing theme marker rather than the real cause. Rule: **a named unverified step is a
  prediction of where the red will be — read the caveat as a work item, not as a disclaimer.** The
  caveat is what made the failure take minutes to diagnose instead of an afternoon, which is the
  argument for writing them down. Recorded 2026-08-26.

- I-067 · **A `before()` hook aborts at its first unguarded failure, so every later assertion
  reports the wrong thing.** The theme's `nightwatch` job failed with **9 of 11 assertions** naming
  a missing `.agora-page__main`. The theme marker was not the problem and neither was the fixture
  module, which the failure screenshot shows **rendering its rows correctly** — unstyled, in Stark.
  What happened is that core's post-install readiness probe (`drupalInstallModule.js:39`, waiting
  for the enable checkbox to turn `:disabled`) timed out on a site where the module was **already
  installed**; that wait carries no `abortOnFailure: false`, so the hook aborted there and
  `/admin/theme/install_default/agora_theme` **never ran** — verified by the absence of its
  *"Loading url"* line, not inferred. Rule: **in a suite whose setup is a hook, count the failures
  that share a cause before believing any of their messages**, and check the log for the step that
  is *missing* rather than the step that is complaining. Recorded 2026-08-26.

- I-068 · **The artifact settles what two layers of reasoning got wrong.** Both hypotheses handed to
  the implementer for I-067 were false — *"the module never installed"* and *"Nightwatch 3 parses
  `waitForElementPresent(sel, 10000, false, cb)` into the wrong slots so `Continue` is never
  clicked"*. The second was falsified **at source**, by extracting `nightwatch@3.12.3` and reading
  `setCallback` and `_waitFor.js`, and by finding that core exercises its own `force` path in
  `navigation`'s own test. The first was falsified by **one screenshot in the job's artifacts**.
  Rule: **when a job produces artifacts, open them before theorising** — this project already
  learned it once at I-056, from a `browser_output` artifact, and paid for it again here. ⚠️ Note
  what did **not** happen: the fix was **not** built on the still-unknown reason the probe missed
  the state (the runner flush-batches timestamps, so the 2.4-second window cannot be resolved). It
  removes the probe from the path instead, via `drupalInstall({ setupFile })`. **A fix resting on
  an unexplained intermittent is a fix you cannot defend.** Recorded 2026-08-26.

- I-069 · **Config shipped by a module survives that module's uninstall, and CI can never see it.**
  `views.view.agora_theme_test_register` remained in active configuration after
  `drush pm:uninstall agora_theme_test`, because its **computed** dependencies name `node`, `user`
  and `views` and never the module that ships it. Reinstalling then fails outright: *"Configuration
  objects (…) provided by agora_theme_test already exist in active configuration."* The remedy is
  `dependencies.enforced.module`. Rule: **any module shipping `config/install` needs `enforced`, and
  the gate will never tell you** — CI installs a fresh site every run, so this class of defect is
  invisible to it by construction and shows up only on a human's development site. It is how a rig
  got stuck during this very work. Recorded 2026-08-26.

- I-070 · **I-066 held, and its explanation was wrong twice — which is the useful part.** The
  implementer had flagged exactly one step as *"reasoned, not run"*: `drupalInstallModule(module,
  force)`. That is precisely where the red landed. But **both** explanations offered for *why* — mine
  and the hypothesis I passed on — were falsified. Rule: **a named unverified step tells you WHERE
  reliably and WHY not at all.** Use the caveat to aim the investigation; do not let it supply the
  conclusion, and do not let a plausible mechanism substitute for opening the evidence.
  Recorded 2026-08-26.

- I-071 · **A predicted expiry that nobody scheduled is a defect with a date on it.** On 2026-08-24
  `specs/002-base-and-theme/plan.md:137` wrote down, correctly: *"known expiry: from global wave 10
  the numbers go four-digit."* The prediction was recorded and **nothing was adjusted**, so when
  unit 003's scaffolding introduced `T-1001`, `tests/bin/cited-tasks-exist` — whose pattern was
  `T-[0-9]{3}` — matched it as `T-100` and reported two dangling citations naming ids **nobody has
  ever written down**. Rule: **writing down that something will break is not a mitigation.** A
  prediction with no owner and no task is a finding scheduled for later, and the later is always a
  turn where somebody is busy with something else. Recorded 2026-08-26.

- I-072 · **A status glyph in an id cell can silently unmake a task's definition.** `cited-tasks-exist`
  recognises `| T-502 ✓ |` as a definition and, as its own comment records, the `✓` alternative was
  added because **signing a task used to delete its definition**. On 2026-08-26 the same defect
  arrived through a new glyph: amending T-804 to `| T-804 ⏸ |` — deferred, a third state beside
  pending and signed — deleted the definition again, and two citations of it went dangling. Rule:
  **when a check keys on a marker set, adding a marker is a change to the check**, and the two edits
  belong in the same commit. The set is now `✓ ⏸ 👤`, still literal rather than a wildcard, because
  `[^|]*` would swallow carried-debt rows and turn citations of debt into definitions.
  Recorded 2026-08-26.

- I-073 · **A route that fails loudly is worth more than a route that works, and the strict one is
  the one to pick.** The theme's fixture module installed cleanly under `drush en` — `[success]`,
  quoted — and failed under `scripts/test-site.php` with one line: *"'is_grouping' is not a supported
  key"*. `drush` does not run strict config schema checking; `test-site.php` does. So moving the
  fixture install from the browser path to the setup-file path **raised** the standard, and the
  first thing the stricter route did was name a real defect in the view — the key is `is_grouped`,
  and the `expose` block was three keys where core's own `views.view.frontpage.yml` carries twelve.
  Rule: **prefer the mechanism that refuses early and says why.** The two failures cost minutes and
  an afternoon respectively, and the difference was entirely in how each one reported.
  Recorded 2026-08-26.

- I-074 · **A recipe's module install skips config ENTITIES, so a recipe cannot ship a language in
  its own `config/` directory.** Measured at T-902 on Drupal 11.4.5:
  `ConfigInstaller::createConfiguration()` line 409 reads `if ($this->isSyncing()) { continue; }`,
  and `RecipeRunner::installModules()` sets the installer syncing — so installing `language` leaves
  the site with `language.mappings`, `language.negotiation` and `language.types` and **zero
  `language.entity.*`**, not even `und`. `ConfigurableLanguage::postSave()` then calls
  `updateLockedLanguageWeights()`, which loads `und`, gets NULL, and dies. Simple config installs;
  config entities do not. **The shape that works** is `config: import:` listing the locked languages
  **first and explicitly** (`'*'` fails) plus the new language created by a **config action**,
  because actions run after `installRecipeConfig` while the recipe's own `config/` is written before
  the imports. **Zero recipes anywhere on the rig install `language`** — core's or contrib's — so
  there was no precedent to copy and none to warn us. Recorded 2026-08-26.

- I-075 · **`drush recipe` rolls back on any throwable, so the post-mortem state actively
  contradicts the stack trace.** `RecipeCommand.php:132` takes a checkpoint and restores it when the
  apply throws. After the language fatal above, `core.extension` showed `language` **not installed**
  — while the trace clearly named the *config* step, which only runs after the module is in. Several
  passes were spent reconciling two true observations of different moments. Rule: **when a recipe
  apply fails, read the trace and ignore the config state afterwards** — the rollback has already
  removed the evidence, and the tidiness is the problem. Recorded 2026-08-26.

- I-076 · **A fresh database is not a fresh site: `site:install` wipes the database and not
  `public://`.** T-903's clean case asserts that a binary shipped in `content/file/` lands on disk
  after apply. Rerun on a dropped database, the *previous* run's binary is still sitting in
  `sites/default/files`, so the assertion passes on residue and proves nothing. The probe deletes
  the file and **prints its absence** before each apply. Rule: **name every store the test depends
  on and reset each one** — for Drupal that is at least the database, the public files directory and
  the cache. Recorded 2026-08-26.

- I-077 · **Two silent failures in the same importer, and only one of them has a log line to grep
  for.** Measured side by side at T-902/T-903. A `file` entity whose binary is missing produces
  exactly one **severity-4** watchdog row — one in sixty-four — and a green install. A translation
  for a language the site has not configured produces **nothing at all**: fifteen rows, every one severity 6
  *"module installed"*, zero mentioning language, translation or langcode, zero at severity ≤ 4.
  The importer has a warning path and chose not to use it for translations. Rule: **before relying
  on "we would see it in the log", grep the log for the failure you are worried about, on a run
  where you caused it.** For the translation case there is no line to find, which is why the guard
  has to be an assertion about the installed site — count the entities that have the translation —
  rather than an inspection of the shipped files. Recorded 2026-08-26.

- I-078 · **Git’s executable-bit setting, disabled on Windows, hides a missing executable bit until the Linux runner
  finds it, and the local gate cannot tell.** Four new `tests/bin/` scripts would have been committed
  `100644`. `gate-a-wave3.sh` gates every invariant on `[ -x "$INV" ]`, which under MSYS is **true
  regardless of mode** — so every local run passed, and on the runner two whole groups would have
  taken the `else` branch and reported **4 FAILs** for a reason nothing local could reproduce. Caught
  before the push and staged with `git add --chmod=+x`; the index now shows `100755` for all four,
  matching the other 20 scripts. Rule: **on this host, check the INDEX mode of every new executable
  (`git ls-files -s`), because the filesystem's answer is not the one the runner will get.** ⚠️ A
  `git reset` before committing drops the mode again. Recorded 2026-08-26.

- I-079 · **The subject of a scan is not its denominator, and confusing the two produces a guard that
  cannot be armed honestly.** `media-licence` must assert `N > 0` or a scan of zero files exits 0
  exactly like a clean tree (I-028) — but `content/` holds no binaries until wave 10, so the
  invariant as specified could not pass on the day it landed. The coordinator proposed arming it on
  *"a binary exists or the manifest exists"*, and the implementer refused: while unarmed it would
  still have **no asserted denominator at all**, printing the same words whether the scan worked or
  `find` silently returned nothing. The resolution is that **"N binaries" is what the script is
  looking FOR; the denominator is whether it looked**. `content entries` is asserted, FATAL at zero,
  from the first run — so the enumerator and the classifier are both proven to have run before the
  script says anything about media. Rule: **when a check cannot yet find what it hunts, assert that
  it searched.** Recorded 2026-08-26.

- I-080 · **A shared verbatim invariant was changed in one repository, and nothing in either can see
  it.** `no-secrets` is one of D-028's five shared scripts; `agora_theme`'s manifest pins it at
  `local == source == 134f6f1a…`. T-906 extended the template's copy with a binary sweep, so that
  `source_sha256` now describes a file that no longer exists at the template's HEAD. **The theme's
  gate stays green** — `shared-invariants` says in its own header that it *"cannot see UPSTREAM
  drift"* and assigns it to a dated unit-006 review — so the mechanism is behaving exactly as
  documented, and the consequence is still that the theme ships **no binary sweep** while believing
  its copy is current. Rule: **a drift detector that can only see one direction must have the other
  direction on somebody's calendar with a name on it**, or the honest limitation quietly becomes a
  real gap. Owner [ejecutor], target unit 006, named on the row rather than left in prose.
  Recorded 2026-08-26.

- I-081 · **Two NUL bytes in a Markdown file made every `LC_ALL=C grep` over it return nothing, and
  the file looked perfectly normal.** Writing up T-906 I quoted the EXIF marker as a literal escape;
  the escaping collapsed and **two real NUL bytes** landed in `specs/003-demo-content/tasks.md`. GNU
  grep then classified the whole file as **binary** — it printed `Binary file … matches` and **one**
  match instead of thirty. `cited-tasks-exist` promptly reported **10 dangling citations**, naming
  ids that are defined on the very lines grep had stopped reading. Nothing looked wrong: the file
  rendered correctly, `wc` and `sort` were unaffected, and a plain `grep -c` in the ambient locale
  still returned **30** — only the invariant's own `LC_ALL=C grep -o` saw binary. Rule: **treat
  `Binary file X matches` as a hard failure, never as a match**, and never paste a byte-level escape
  into prose without checking what actually landed (count the NUL bytes with a language that will not re-escape them for you, which the very next paragraph of this idiom failed to do). ⚠️ The wider
  point is that the failure was **loud and specific because an invariant was watching** — the same
  invariant widened hours earlier for an unrelated reason. Recorded 2026-08-26.

- I-082 · **Extraction working is not delivery working, and the artefacts of a broken pipeline look
  exactly like the artefacts of a working one.** `haven`, a published Drupal CMS site template, has
  roughly **180 `.po` files per release** on `ftp.drupal.org`; one of them carries **168 real
  Spanish strings** — <!-- cspell:disable -->`Home → Inicio`, `Tags → Etiquetas`<!-- cspell:enable --> — extracted from its `config/` by `potx`
  and translated by volunteers. **No installed `haven` site can fetch a single one.**
  `LocaleProjectRepository::getProjectList()` builds its project list from the **module and theme**
  extension lists, and a site template contains **zero `*.info.yml`** by rule, so it is invisible to
  the update system: no project entry, no server pattern, no fetch, ever. Rule: **a full pipeline of
  intermediate outputs proves the early stages ran, and says nothing about whether anything arrives.**
  Follow the last hop before believing the chain. Recorded 2026-08-26.

- I-083 · **A decision that is right for the wrong reason will be reopened by the next person who
  checks it.** D-033 concluded correctly that shipped config translations cannot reach an installed
  site, and named as its sharpest evidence that a site template's config carries no
  `_core.default_config_hash`. **Measured: 103 of 104 recipe-installed config objects DO carry it.**
  `RequirementsTest` guards the **packaged YAML**; `LocaleConfigManager` reads the **active store in
  the database**. Different objects, and the test was never the lock. The holding survives on five
  other locks, one of them absolute — but a task row had already been written to depend on the false
  clause, which is how a wrong reason propagates before anyone notices the conclusion was fine.
  Rule: **amend the reasoning as loudly as you would amend the conclusion.** Recorded 2026-08-26.

- I-084 · **A probe that stops you building something has paid for itself as fully as one that
  enables it — and it is the cheaper of the two.** T-902 proved shipped ES translations work, and
  found on the way that a recipe **cannot** create a language in its own `config/`, that `'*'` fails
  where an explicitly ordered list works, and that a dropped translation logs **nothing at all**.
  Hours later D-035 was signed English-only and every one of those mechanisms became unnecessary. The
  row is **not withdrawn** (rule 8): its findings stay live for whoever adds `translations:` later.
  Rule: **do not measure the value of a probe by whether the thing it probed got built** — the
  alternative was discovering the same six failure modes across fifty authored nodes. Recorded
  2026-08-26.

- I-085 · **Answering a question can reveal a defect that neither answer would have fixed, and the
  row that would have caught it can be the one you were about to delete.** D-035 asked whether to
  ship bilingual content. Its research found **three Spanish legal-citation fragments already
  shipped inside English config descriptions** — `convenio`, `subvención`,
  `importe de adjudicación` — rendering on English admin forms **today**. That is **SC 3.1.2**,
  live, and independent of the answer. It was owned by exactly one task row, **T-1011, which the
  chosen option would otherwise have removed**. Rule: **before deleting a row a decision makes
  unnecessary, ask what else it was the only owner of.** Recorded 2026-08-26.

- I-086 · **I-074 is not about languages. A recipe skips config ENTITIES of every kind, and the
  second victim was the theme's own block placements.** `1.0.1` shipped four
  `config/optional/block.block.agora_theme_*` files. On a site installed **the way Ágora actually
  installs** — by recipe — **none of the four is created**: the files sit in the installed theme and
  the `block_content` table has no row for them, so every page renders with **no page title, no
  `<h1>`, no menu and no status messages**, which is precisely the defect T-1009 was written to fix.
  Root cause is I-074 in a new costume: `RecipeRunner` sets the config installer **syncing**, and
  `ConfigInstaller::createConfiguration()` skips config entities while syncing. Proven by remedy
  rather than by reading: calling `installOptionalConfig()` by hand took the site from **21 blocks to
  25**, naming all four, and the rendered pages went from `h1=0` and 1.6 KB to `h1=1` and 3.9 KB.
  Rule: **whenever a recipe is supposed to bring in configuration, ask whether that configuration is
  simple config or an ENTITY — and if it is an entity, assume it was skipped until you have seen it
  in the database.** Recorded 2026-08-26.

- I-087 · **A verification that exercises a code path the product does not use is not a
  verification, and it can be greener than the real one.** T-1009's implementer proved the block
  placements install by running `drush theme:enable` on a block-less site, watched them appear, and
  proved the negative too by moving the file to `config/install` and catching
  `UnmetDependenciesException` by name. **All of that was true and none of it was about Ágora**,
  which installs the theme through a recipe — a different code path with the opposite behaviour. The
  work was careful; it was careful about the wrong door. Rule: **name the path the PRODUCT takes
  before choosing the path the test takes**, and when they differ, that difference is the finding.
  ⚠️ This is the sharper twin of I-070: there, a step flagged *"reasoned, not run"* told us where the
  red would be. Here **nothing was flagged**, because the implementer did not know there were two
  doors — and the only thing that found it was **rendering a real page and counting the `<h1>`
  elements**, which nothing in either repository's gate does. Recorded 2026-08-26.

- I-088 · **`grep -I` calls a file binary only when it finds a NUL byte, so the metadata sweep was
  blind to the file type it was most likely to meet.** T-906 scoped level 3 of `no-secrets` to *"the
  files `grep -I` skips"*. When 39 binaries landed in `content/`, it opened **2 of them**. The other
  37 were 34 PDFs and 3 CSVs — and a PDF has no NUL near its head, so `grep` classifies it as
  **text**. Meanwhile levels 1 and 2 hunt credential shapes, not XMP packets. **A PDF carrying an XMP
  packet is the ordinary case, not the exotic one**: every tool that writes a PDF writes one, and it
  routinely names the author and the software. So the invariant's own selector guaranteed it would
  miss the commonest instance of the thing it was built to catch. Widened to *(grep says binary) OR
  (the extension names a container)*, with the extension list **printed on every run**; the sweep
  went from **2 to 80** files opened across both scopes, and an XMP packet injected into a corpus PDF
  now produces **2 findings naming the marker and its byte offset** where the old code found nothing.
  Rule: **when a check defines its scope as "whatever some tool excludes", the tool's definition
  becomes your specification** — and `grep -I`'s definition of binary is narrower than any reasonable
  reading of the word. Recorded 2026-08-26.

- I-089 · **Two gates rejected a working design, and rebuilding to satisfy them produced a better
  one.** T-1101 and T-1102 were first built on two new views, `agora_base_indicators` and
  `agora_base_areas`. Both pages rendered correctly. Then `config-inventory` reported **2 findings**
  — *"no page display path; a view with no route cannot be a key route"* — and
  `ValidationTest::testKeyRoutes()` failed, because it compares the shipped `agora_base_*` view set
  against its named list **in both directions** and its own comment says *"a NINTH view landing with
  no assertions fails too"*. **Neither gate knows about a view that exists only to be placed on a
  page**, and both are right about routes. The implementer **withdrew the design rather than
  weakening either check**, and rebuilt the same two pages from **block displays on views that
  already exist** — adding no view id at all. `config-inventory` went back to `102 config objects · 0
  findings` and the eight-view set is unchanged. Rule: **a red from a guard you cannot fault is a
  design review you did not ask for.** Take it. And if a page-less view is genuinely wanted one day,
  that is a decision with a number, not a task row that quietly adds a ninth id. Recorded 2026-08-26.

- I-090 · **A generated identifier scheme that is not written down is not recoverable from its own
  output.** Wave 10 exported 199 content files with **v5 UUIDs**, deliberately, so that a re-export
  is a zero-line diff rather than 199 rewritten files. Wave 11 needed to add two entities to the same
  corpus and could not reproduce the derivation: **36 namespaces × ~11 name forms brute-forced
  against two known corpus UUIDs gave 0 matches**, and nothing in `specs/`, the commit messages or
  `tests/` records the scheme. A new one was defined and documented instead, so **the corpus now
  carries two different derivations** — harmless today, and exactly the kind of thing that is
  discovered at the worst moment. Rule: **when you choose determinism, the recipe for it is part of
  the deliverable.** A reproducible output whose reproduction procedure lives only in a deleted
  scratch script is reproducible by nobody. Recorded 2026-08-26.

- I-091 · **"I proved it is not needed" and "the test requires it" were both true, about different
  things.** T-1101/T-1102 placed three views blocks on two Canvas pages, and the export produced
  three `canvas.component.block.views_block.*` config entities. The implementer dropped them,
  having **measured on a clean install that the pages render without them** — which is correct, and
  it is why the rig showed nothing wrong. `ValidationTest::testApply` then failed:
  *"the site template should include this component in its configuration"* — an assertion inherited
  from the starter kit, walking every Canvas entity's component tree and requiring each component's
  config dependency to be **shipped by the package**. Rule: **"it renders" and "it is
  self-contained" are different claims, and a site template is judged on the second.** A page
  referencing a component the package does not ship works on the machine that authored it and is
  broken wherever that component happens not to exist — which is every site but one. ⚠️ Note what
  caught it: not the rig, not any local invariant, but a test the **starter kit** shipped and this
  project inherited. Recorded 2026-08-26.

- I-092 · **`drush`'s export is a starting point, not an artefact — and it took three consecutive
  red pipelines on the same three files to finish learning it.** Three
  `canvas.component.block.views_block.*` objects were exported from a rig and committed. Each push
  failed on a different rule, and **not one of them was a rule this project wrote**:
  1. `ValidationTest::testApply` — the components were **missing**, because they had been dropped on
     the correct measurement that the pages render without them. *"It renders"* and *"it is
     self-contained"* are different claims, and a site template is judged on the second.
  2. `RequirementsTest` — they carried a **`uuid` key**. A package that ships UUIDs imposes
     identities on every site that installs it; **0 of the other 102** shipped config objects had
     one. Stripped, and Drupal generates its own on install — verified, three fresh UUIDs.
  3. `phpcs` — `Drupal.Files.EndFileNewline`: `drush config:get --format=yaml` writes a trailing
     **blank line**, so every object exported that way arrives one newline too long.
  A package ships **less** than a site has: no `_core`, no `uuid`, no `default_config_hash`, and no
  stray newline. Rule: **after exporting config, subtract before committing**, and treat each of
  those three as a separate subtraction rather than as one vague "clean it up". ⚠️ The third one is
  now mechanical — `tests/bin/config-inventory` checks for exactly one trailing newline, in **both**
  directions, falsified both ways — because `phpcs` cannot run on this host and had turned the gate
  red three times in one day over something no local tool could see. Recorded 2026-08-26.

- I-093 · **A public file's `Content-Type` is the host web server's answer, not Drupal's — and
  `Content-Disposition` is nobody's.** T-1104's signed criterion asked each open-data distribution's
  response to carry a correct `Content-Type` **and** `Content-Disposition`. Measured on the wire: the
  five CSVs return `application/octet-stream` with **no** `Content-Disposition`, while a PDF from the
  same field, the same directory and the same install returns `application/pdf`. **Nothing about the
  template differs between those two responses** — public files never reach PHP, so the header is
  read out of the web server's own mime table. In one container: nginx's `/etc/nginx/mime.types`
  contains **0** occurrences of `csv` and `/etc/nginx/nginx.conf:40` sets
  `default_type application/octet-stream`; Apache, wired `TypesConfig /etc/mime.types`
  (`/etc/apache2/mods-available/mime.conf:7`), reads a table whose line 2072 is `text/csv csv`. Same
  site, two hosts, two headers, and the template is not a party to either. The Drupal-served route is
  no better on the second half: `File::getDownloadHeaders()` returns exactly `Content-Type`,
  `Content-Length` and `Cache-Control`, and `FileDownloadController` never calls
  `setContentDisposition()` — so **no core path, public or private, on any host, emits
  `Content-Disposition`.** Every escape route breaks a rule this project already signed:
  `private://` needs `$settings['file_private_path']` written into `settings.php` by hand, a
  `hook_file_download()` or a response subscriber needs code and `RequirementsTest` requires **0
  `*.info.yml`**, and an nginx line or an `.htaccess` mime type configures a stranger's server.
  Rule: **before a criterion names a response header, name the layer that owns it.** A criterion the
  template cannot influence is either a finding or a green that holds on exactly one web server.
  What the template does own was measured instead, and it is not nothing: the `file` entity's
  `filemime`, the `type` attribute core puts on the rendered anchor, the link text, and the fact
  that the bytes served are byte-for-byte the bytes shipped. Recorded 2026-08-26.

- I-094 · **Before asking which repository owns a fix, ask whether the code you are about to change
  is even on the page.** T-1011 was dispatched to `agora_theme` to mark three Spanish fragments
  `lang="es"`, with a careful warning about not hard-coding another project's sentences. The
  implementer stopped without writing code and produced a better answer: **those fragments render
  only on admin routes, and admin routes are rendered by Gin.** Measured — three admin pages carry
  all three fragments with `gin` assets present and `agora_theme` assets at **0**, while five
  front-end pages carry **0 fragments** between them. A `hook_preprocess` fires only when its theme
  is active. Rule: **a boundary argument can be sound and still be the second reason** — check first
  that the component you are modifying participates in rendering the thing you are fixing. The fix
  belonged where the sentence is authored, and `<span lang="es">` around the words is a mechanism
  about language rather than about phrases: it travels with a reword instead of being orphaned by
  one. Recorded 2026-08-26.

- I-095 · **A rule that reports `inapplicable` reads exactly like a rule that passed, and naming it
  in a gate is how the false green gets welded in.** T-1011's row asked for `valid-lang` to be
  **named in the axe rules-run list** reporting 0 violations. It cannot be: `valid-lang`'s scope is
  elements carrying `lang` **other than `<html>`**, and the theme emits **none** — on `/contracts`
  the 29 apparent hits are `hreflang` on taxonomy links, and the real count of `lang=` attributes is
  **1**, on `<html>`. So the rule would land in `inapplicable`, and the gate asserts
  `bucket === 'passes'`. **Adding it would have forced that assertion to accept `inapplicable`** —
  permanently, for every rule — which is I-045's failure mode built into the mechanism that exists
  to prevent it. The implementer refused the criterion and said why. Rule: **a criterion that cannot
  be honoured honestly is escalated, not approximated**, and *"the gate went green after we relaxed
  what green means"* is the sentence this project keeps being one step away from. Recorded 2026-08-26.

- I-096 · **The coordinator wrote into a checkout while an implementer was measuring it — twice in
  one session, and both times the implementer is who noticed.** The first time, content changed under
  a `tester` and its whole report of rig and gate numbers described a tree that no longer existed; it
  surfaced only because `no-boilerplate` went red for an unrelated-looking reason (eight
  `DELETED`-class findings from staged-but-uncommitted deletions). The second time, three config
  files were rewritten **three minutes after** an implementer's own edit, and a row it was working
  under was signed beneath it. Neither collision lost work, and that is luck rather than design.
  Rule: **while an agent is measuring a tree, that tree is its instrument** — serialise writes
  against it, or hand the agent a commit rather than a live working copy. ⚠️ The tell to watch for is
  a **denominator that moves between two runs of the same command**, and the honest response is to
  find out what changed rather than to re-run until the numbers agree. Recorded 2026-08-26 by the
  coordinator, about the coordinator.

- I-097 · **A file copy and the cache rebuild that reveals it must be two commands, and the first
  citation of this idiom predated its existence.** Falsifying the `file-link.html.twig` override,
  the template was moved aside and the cache rebuilt **in the same command**; the rebuild had not
  taken effect when the page was fetched, the duplicate did not reappear, and a correct fix was
  deleted as dead code on that evidence. Re-measured with the `mv` and the `drush cr` separated,
  the duplicate was plainly there — **5 → 10 → 5** occurrences across remove and restore. The same
  trap then recurred in reverse hours later: blocks placed and page fetched before the rebuild
  landed produced "component exists, does not render". Rule: **mutate, then rebuild, then measure —
  three steps, three commands**, and treat any dirty case that seems to disprove a fix as a
  measurement to check before it is a verdict. ⚠️ This number was cited in two dispatch briefs
  before this entry existed; a citation is not a record, and `cited-tasks-exist` would have caught
  exactly this had idioms been in its scope. Recorded 2026-08-27.

- I-098 · **A preview shown mid-round exhibits a state no one considers finished, and the viewer
  cannot tell.** [andres] was given the live preview URL while a two-repository design round was in
  flight: the template had already landed list markup for register cards whose CSS the theme was
  still writing, and a rig rebuild had silently dropped the theme's optional blocks (I-086's bite,
  again). He saw a home page with no masthead, no footer, and a raw field list — and reasonably
  asked what had broken. Nothing had; he was looking between two agents' half-finished halves.
  Rule: **when work spans repositories, the preview is part of the round** — either freeze it at the
  last coherent state or say plainly that it is mid-surgery. A stakeholder debugging an
  intermediate state costs more trust than the intermediate state saved time. Recorded 2026-08-27.
