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
