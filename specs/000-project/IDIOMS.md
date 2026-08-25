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
