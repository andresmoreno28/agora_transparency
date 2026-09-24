#!/usr/bin/env bash
#
# gate-a-wave1.sh - Agora - Unit 001, wave 1 gate A verification.
#
# Verifies the skeleton and identity of the site template package (T-101..T-105,
# T-107, T-111, T-112) against the hard structural rules recorded in CLAUDE.md and
# IDIOMS.md (I-004, I-007, I-014, I-015, I-020).
#
# Contract:
#   - every check prints:  obtained | expected | OK/FAIL
#   - closing line:        "N checks - M failures"
#   - exit 0 ONLY if M = 0
#   - a check that CANNOT be evaluated (missing file, missing tool, invalid
#     JSON) is a FAIL, never a skip. I-007: an exit 0 without counts proves
#     nothing.
#
# House rules for tests/bin/ (T-321(c)): `wc -l` never `grep -c` for a compared
# value; numeric guards defaulted ONLY where zero means FAIL; grep rc >= 2 is an
# error, never "no match"; never `-F` with `-i`. The CANONICAL copy of the block,
# with the reasoning and the I-031 asymmetry spelled out, lives in the header of
# tests/bin/no-ci-allow-dev. Read it before adding a counter here.
#
# CHECK COUNT, and the line below is MACHINE-READ. tests/bin/claims-match-sources
# compares it against the figure CLAUDE.md's Gate A block states for this runner,
# and fails the gate when the two disagree. It is declared here, in the file it
# describes, rather than only in CLAUDE.md, because the commit that moves a check
# count has to walk past this line and does not have to open that file.
#
# It went 61 -> 64 on 2026-09-06: G9 adds three checks. It is the first group here
# whose subject is the PROSE rather than the package - CLAUDE.md's Gate A block had
# drifted on six separate figures at once, in a block whose own stated rule is that
# every number in it is a dated measurement.
#
# ⚠️ WHAT THIS LINE DOES NOT DO, so nobody reads more into it than it carries: it
# is a declaration, not a measurement. Nothing yet asserts that it matches the
# total this runner actually PRINTS. Closing that needs the summary below to
# compare N against it - one line - and it is deliberately not done in the commit
# that introduces the reader, so that the reader is falsified against a total a
# human read off the terminal first.
#
# `invariants=2` is G9 and G10, and nothing else. G0-G8 are checks written inline
# in this file; G9 was the first group here that executes a script from
# tests/bin/, which is what the word counts in CLAUDE.md's "N invariants in
# total" - a total across both runners, and until 2026-09-06 it was 0 here plus
# 15 in wave 3.
#
# G10 is executable-bit, added 2026-09-06, +3 checks and +1 invariant: 64 -> 67
# here, and 16 -> 17 across both runners. It is the second group whose subject is
# this repository's own tooling rather than the packaged product.
#
# 2026-09-20: G9 grows a FOURTH check, `trace figures claimed > 0`, so 67 -> 68
# here and `invariants` does NOT move - no group was added. It guards the online
# half of claims-match-sources from its own back door: the gate never passes
# --online, so what it can assert offline is that there are still figures for
# --online to read. Emptying the two trace-figures tables in CLAUDE.md would
# otherwise leave that half comparing nothing and printing the silence as
# agreement, which is I-028 arriving inside the tool built to end it.
#
# UNIT 003 WAVE 28 (2026-09-21) takes it from 68 to 71, and `invariants` does
# NOT move, because no group was added - the three checks land in the EXISTING
# G8. Written out term by term so the total is stated rather than inferred:
#   T-1916  two more entries on G8's "must NOT travel" list, `.gitignore` and
#           `.mailmap`, now `export-ignore`d. They are git's files and not the
#           product's: a tarball has no history for a .mailmap to canonicalise,
#           and a .gitignore inside `recipes/agora_transparency/` names paths
#           that never exist there. The checks are what stop the exclusion
#           being quietly undone.                                     68 -> 70
#   T-1916  one more entry on G8's "must travel" list, `logo.png`. The audit
#           asked whether a binary referenced by nothing belongs in the package
#           at all; the answer is that it has a job Drupal.org performs and no
#           file in this repository can perform - so it stays, it now carries a
#           provenance row, and this check is what stops it being deleted by
#           somebody who finds it referenced by nothing.               70 -> 71
#
# UNIT 006 WAVE 2 (2026-09-21) takes it from 71 to 77 and `invariants` from 2 to
# 3, because G11 IS a new group running a new script: tests/bin/packaged-claims
# (T-0608). Six checks - exit plus five denominators - and the denominators are
# the point rather than padding. G9 guards CLAUDE.md, which is process and is
# `export-ignore`d; nothing guarded the prose that actually SHIPS, and an audit
# of it the same morning found six wrong figures in three packaged files,
# including the two check totals README quotes about these very runners.
#
# UNIT 006 WAVE 3 (2026-09-21) takes it from 77 to 88 and `invariants` from 3 to
# 5, because G12 and G13 are two new groups running two new scripts:
# tests/bin/ported-copies (6 checks) and tests/bin/ported-drift (5). They close
# the direction NOTHING here had a record of - the files this repository took FROM
# `agora_theme`. The sibling has carried a manifest, a local detector and, since
# D-063, an API-reading drift check for the files it took from here; this
# direction had no manifest, no recorded provenance and no detection of any kind,
# and its whole provenance was one sentence in a commit message.
#
# ⚠️ G13 IS THE FIRST GROUP IN THIS RUNNER THAT TOUCHES THE NETWORK, and that is
# a deliberate change of character rather than an oversight. The check it performs
# cannot be done offline - it asks what the other repository says - and a
# preflight nobody is obliged to run has the same detection properties as a
# comment. NOT READ is a third state that exits 2 and is tolerated here for the
# same reason a raw delta is: no commit in this repository can fix an unreachable
# drupalcode.
#
# UNIT 006 (2026-09-24, T-0635) takes it from 95 to 100 and `invariants` from 6
# to 7, because G15 is a new group running a new script:
# tests/bin/no-usage-reporting, five checks. It exists because this package's own
# test suite reported usage to Drupal.org - every functional test site ran cron
# with `update` installed and asked updates.drupal.org about every enabled
# project, 18 requests from 9 sites per run, twice per push. The guard is a trait
# in tests/src; this group is what stops the next test class leaving it out.
#
# GATE-CLAIM: checks=100 invariants=7
#
# Usage: tests/bin/gate-a-wave1.sh   (run from anywhere; it cd's to the repo root)

set -u
# No `set -e`: a failing check must be recorded, not abort the run.

# ---------------------------------------------------------------- repo root --
SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
REPO_ROOT=$(CDPATH= cd -- "$SCRIPT_DIR/../.." && pwd)
cd "$REPO_ROOT" || { echo "FATAL: cannot cd to repo root $REPO_ROOT"; exit 1; }

N=0
M=0
GRP=""

C_OK=""; C_BAD=""; C_OFF=""; C_DIM=""
if [ -t 1 ]; then
  C_OK=$'\033[32m'; C_BAD=$'\033[31m'; C_OFF=$'\033[0m'; C_DIM=$'\033[2m'
fi

# Truncate a value so the table stays aligned; never hide the fact it was cut.
trunc() {
  _v=$1; _max=$2
  if [ "${#_v}" -gt "$_max" ]; then
    printf '%s...' "$(printf '%s' "$_v" | cut -c1-$((_max - 3)))"
  else
    printf '%s' "$_v"
  fi
}

# check <label> <obtained> <expected>
check() {
  _label=$1; _got=$2; _exp=$3
  N=$((N + 1))
  if [ "$_got" = "$_exp" ]; then
    _res="${C_OK}OK${C_OFF}"
  else
    _res="${C_BAD}FAIL${C_OFF}"
    M=$((M + 1))
  fi
  printf '  %-38s %-28s | %-28s | %s\n' \
    "$_label" "$(trunc "$_got" 28)" "$(trunc "$_exp" 28)" "$_res"
}

group() {
  GRP=$1
  printf '\n%s\n' "$GRP"
  printf '  %-38s %-28s | %-28s | %s\n' "check" "obtained" "expected" "verdict"
  printf '  %s\n' "----------------------------------------------------------------------------------------------------------"
}

note() { printf '  %s%s%s\n' "$C_DIM" "$1" "$C_OFF"; }

# ------------------------------------------------------------ safe readers ----
# Every reader returns a sentinel string on failure so the check reports FAIL
# with a legible reason instead of crashing or silently passing.

jq_raw() { # <filter> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s absent>' "$_f"; return; }
  _out=$(jq -r "$1" "$_f" 2>/dev/null) || { printf '<%s invalid json>' "$_f"; return; }
  printf '%s' "$_out"
}

# T-316 / I-027: `grep` has THREE exit states, not two - 0 = matched, 1 = no
# match, >= 2 = grep itself FAILED (invalid pattern, unreadable file, crash).
# `grep -c` prints `0` and exits 1 when nothing matches, which is a real answer;
# on rc >= 2 it prints NOTHING, and `$(... || true)` turned that into an empty
# string that the caller could not tell from a count. Both readers now return a
# sentinel naming the exit status, exactly like jq_raw() above: the check FAILS
# with a legible reason instead of comparing an invisible value.
#
# T-321(a), house rule 1: the count itself is now produced by `wc -l` over the
# matched lines, never by `grep -c`. The sentinel above covers the rc >= 2 path,
# but only where a caller remembers to read it; `wc -l` removes the failure mode
# instead of guarding it, because it cannot emit a non-number at all. Values are
# unchanged - grep prints exactly one line per matching line, so counting its
# output lines and counting its matches are the same number.
grep_count() { # <ERE> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s absent>' "$_f"; return; }
  _matched=$(grep -E "$1" "$_f" 2>/dev/null)
  _rc=$?
  [ "$_rc" -ge 2 ] && { printf '<grep exit %s on %s>' "$_rc" "$_f"; return; }
  if [ "$_rc" -eq 0 ]; then
    _n=$(printf '%s\n' "$_matched" | wc -l | tr -d ' ')
  else
    _n=0
  fi
  printf '%s' "$_n"
}

grep_count_fixed() { # <fixed string> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s absent>' "$_f"; return; }
  _matched=$(grep -F "$1" "$_f" 2>/dev/null)
  _rc=$?
  [ "$_rc" -ge 2 ] && { printf '<grep exit %s on %s>' "$_rc" "$_f"; return; }
  if [ "$_rc" -eq 0 ]; then
    _n=$(printf '%s\n' "$_matched" | wc -l | tr -d ' ')
  else
    _n=0
  fi
  printf '%s' "$_n"
}

# Number of in-scope files whose CONTENT matches <ERE>, one path per line.
# Replaces `scan_files | xargs grep -lIE ... | wc -l`, which reported "0 files"
# both when nothing matched and when grep FAILED - `wc` counts an empty stdout
# identically either way, so a broken grep passed the check (T-316 / I-027).
# On a grep error this prints a single sentinel line instead of paths, which the
# caller turns into a FAIL. It also handles paths with spaces, which xargs did
# not.
grep_definers() { # <ERE>
  while IFS= read -r _file; do
    [ -n "$_file" ] || continue
    LC_ALL=C grep -qIE "$1" "$_file" 2>/dev/null
    _rc=$?
    if [ "$_rc" -eq 0 ]; then
      printf '%s\n' "$_file"
    elif [ "$_rc" -ge 2 ]; then
      printf '<grep exit %s on %s>\n' "$_rc" "$_file"
      return
    fi
  done <<< "$(scan_files)"
}

exists_file() { [ -f "$1" ] && printf 'present' || printf 'absent'; }
exists_dir()  { [ -d "$1" ] && printf 'present' || printf 'absent'; }

# Files in scope for filesystem scans: the packaged tree, minus VCS/build dirs.
# `./web` IS PRUNED, AND IT WAS THE ONE EXCLUSION MISSING HERE. It is where a
# local Drupal build goes, it is in .gitignore, and `git archive HEAD` therefore
# never contains it - so an *.info.yml under it is not in the package and cannot
# be. tests/bin/no-code-in-template, the AUTHORITATIVE check for that rule,
# already documents its working-tree scope as "find, minus .git/ vendor/
# node_modules/ web/" and has excluded it all along. This scanner did not, so
# the two disagreed about the same question.
#
# FOUND BY IT FIRING, 2026-09-02: a theme checkout appeared under
# web/themes/custom/agora_theme/ and this check reported `*.info.yml files: 2`
# while no-code-in-template reported nothing - the package was clean in both
# readings. Nothing is weakened by aligning them: no-code-in-template still
# reads the PACKAGE for the same rule, the scope RequirementsTest applies.
scan_files() {
  find . \
    -path ./.git -prune -o \
    -path ./vendor -prune -o \
    -path ./node_modules -prune -o \
    -path ./web -prune -o \
    -type f -print 2>/dev/null
}

printf '=========================================================================================================\n'
printf 'Gate A - Agora unit 001 wave 1 - skeleton and identity\n'
printf 'repo: %s\n' "$REPO_ROOT"
printf 'branch: %s\n' "$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '<no git>')"
printf 'date: %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')"
printf '=========================================================================================================\n'

# ------------------------------------------------------------- G0 preflight --
# Missing tooling is a FAILURE of the gate, not a reason to skip it.
group 'G0 - Preflight (required tooling)'
MISSING=0
# `tar` is required by G8 (packaging): it reads the tarball produced by
# `git archive`. If it is missing, G8 cannot be evaluated -> that is a FAIL.
for tool in jq composer git find grep tar; do
  if command -v "$tool" >/dev/null 2>&1; then
    got='available'
  else
    got="NOT FOUND: $tool"
    MISSING=$((MISSING + 1))
  fi
  check "tool '$tool'" "$got" 'available'
done

if [ "$MISSING" -gt 0 ]; then
  printf '\n%sPREFLIGHT FAILED:%s %d tool(s) missing.\n' "$C_BAD" "$C_OFF" "$MISSING"
  printf 'The gate CANNOT be evaluated. Install what is missing (brew install jq composer) and re-run.\n'
  printf 'A gate that silently skips checks is worse than not having one.\n\n'
  printf '%d checks - %d failures\n' "$N" "$M"
  exit 1
fi

# --------------------------------------------------- G1 package identity -----
group 'G1 - Package identity (composer.json)'
check 'composer.json present'      "$(exists_file composer.json)" 'present'
check '.name'                      "$(jq_raw '.name'    composer.json)" 'drupal/agora_transparency'
check '.type'                      "$(jq_raw '.type'    composer.json)" 'drupal-recipe'
check '.license'                   "$(jq_raw '.license' composer.json)" 'GPL-2.0-or-later'

if [ -f composer.json ]; then
  CV_OUT=$(composer validate --strict --no-interaction 2>&1)
  CV_RC=$?
  check 'composer validate --strict (exit)' "$CV_RC" '0'
  if [ "$CV_RC" -ne 0 ]; then
    printf '%s' "$CV_OUT" | sed 's/^/      | /'
  fi
else
  check 'composer validate --strict (exit)' '<composer.json absent>' '0'
fi

# ---------------------------------------------------------- G2 recipe.yml ----
group 'G2 - recipe.yml (root, type: Site case-sensitive)'
check 'recipe.yml present'         "$(exists_file recipe.yml)" 'present'
check 'lines "^type: Site" (exact)'    "$(grep_count '^type:[[:space:]]*Site$' recipe.yml)" '1'
check 'lines "^type:" (no duplicates)' "$(grep_count '^type:' recipe.yml)"                  '1'
check 'lines "^name:"'                 "$(grep_count '^name:' recipe.yml)"                  '1'
check 'lines "^description:"'          "$(grep_count '^description:' recipe.yml)"           '1'

# ------------------------------------- G3 kit scaffolding removed ------------
group 'G3 - Starter kit scaffolding removed (T-103, T-104)'
check 'occurrences of _comment'    "$(grep_count_fixed '_comment' composer.json)" '0'
# ---------------------------------------------------------------------------
# DEBT DISCHARGED - the exit gate fired, and this is the note that closes it.
#
# This check expected PRESENT from 2026-08-21 to 2026-08-25, under the `blank`
# rider signed by [andres] (specs/000-project/DECISIONS.md, "Riders on wave 1"):
# the theme the recipe installed was not versioned at all, it was MANUFACTURED
# at install time by drupal/site_template_helper reading the
# `extra.drupal-site-template` block, so deleting that block would have left
# `recipe.yml` pointing at a theme that did not exist. The rider named its own
# exit: the unit 002 task performing the atomic `drupal/agora_theme` change
# (D-014, option B) in ONE SINGLE COMMIT, and that task was required to set
# 'absent' back here in the same commit. It has:
#     - .extra["drupal-site-template"] deleted from composer.json
#     + "drupal/agora_theme": "^1.0" added to .require
#     + `- blank` changed to `- agora_theme` in `install:` of recipe.yml
#     + system.theme.default changed to 'agora_theme'
# all four in the commit that also flipped the expected value below.
#
# The tripwire has not been removed, it has been TURNED AROUND. Expecting
# 'absent' is now what fails if the generated-theme block ever comes back -
# and it can come back by accident: `drush site:export` `unset`s the key on its
# way past (D-032), and a hand-restored block would silently reintroduce a theme
# that this package must never manufacture. I-020 still governs: known debt is a
# task with an owner and an exit gate, never a tolerated red.
# ---------------------------------------------------------------------------
check '.extra["drupal-site-template"]' "$(jq_raw '.extra["drupal-site-template"] // "absent" | if . == "absent" then . else "present" end' composer.json)" 'absent'
note 'the `blank` rider [andres] 2026-08-21 is DISCHARGED: the unit 002 atomic theme change removed the block and flipped this expectation in the same commit'
check 'GET-STARTED.md'             "$(exists_file GET-STARTED.md)" 'absent'
# NOTE: find is used, not the dispatch's `*.example` glob: the shell glob does
# NOT match dotfiles and the kit ships `.gitignore.example` / `.gitattributes.example`.
EXAMPLES=$(find . -maxdepth 1 -name '*.example' -type f 2>/dev/null | wc -l | tr -d ' ')
check '*.example files at root'     "$EXAMPLES" '0'
if [ "$EXAMPLES" != "0" ]; then
  find . -maxdepth 1 -name '*.example' -type f | sed 's/^/      | /'
fi

# ------------------------------- G4 structural invariants (I-014) -----------
group 'G4 - Structural invariants (mirrors the kit RequirementsTest)'
SCANNED=$(scan_files | wc -l | tr -d ' ')
note "scope: package tree without .git/ vendor/ node_modules/ - $SCANNED files scanned"
check 'files scanned > 0'          "$([ "$SCANNED" -gt 0 ] && echo 'yes' || echo 'no')" 'yes'
# T-321(a), house rule 1: `wc -l` over the matched lines, never `grep -c`.
# rc 1 means "none" and yields 0; rc >= 2 printed nothing and yields the
# sentinel, which cannot be mistaken for a count.
INFO_MATCHED=$(scan_files | grep '\.info\.yml$' 2>/dev/null)
INFO_RC=$?
if [ "$INFO_RC" -ge 2 ]; then
  INFOYML="<grep exit $INFO_RC on the file list>"
elif [ "$INFO_RC" -eq 0 ]; then
  INFOYML=$(printf '%s\n' "$INFO_MATCHED" | wc -l | tr -d ' ')
else
  INFOYML=0
fi
check '*.info.yml files'           "$INFOYML" '0'
if [ "$INFOYML" != "0" ]; then
  scan_files | grep '\.info\.yml$' | sed 's/^/      | /'
fi
check 'recipes/ directory'         "$(exists_dir recipes)" 'absent'
check 'themes/ directory'          "$(exists_dir themes)"  'absent'
check 'modules/ directory'         "$(exists_dir modules)" 'absent'

# ------------------------- G5 no pins, no patches, no escape hatches (I-015) -
group 'G5 - No pins, no patches, no escape hatches'
REQ_COUNT=$(jq_raw '.require | to_entries | length' composer.json)
check '.require entries > 0'       "$([ "$REQ_COUNT" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
note "require: $REQ_COUNT entries"
PINNED=$(jq_raw '[.require | to_entries[] | select(.value|test("^v?[0-9]+\\.")) | .key] | length' composer.json)
check 'pinned versions'            "$PINNED" '0'
[ "$PINNED" != "0" ] && jq -r '.require | to_entries[] | select(.value|test("^v?[0-9]+\\.")) | "      | \(.key): \(.value)"' composer.json 2>/dev/null
UNSTABLE=$(jq_raw '[.require | to_entries[] | select(.value|test("dev|alpha|beta|rc";"i")) | .key] | length' composer.json)
check 'dev/alpha/beta/rc constraints' "$UNSTABLE" '0'
[ "$UNSTABLE" != "0" ] && jq -r '.require | to_entries[] | select(.value|test("dev|alpha|beta|rc";"i")) | "      | \(.key): \(.value)"' composer.json 2>/dev/null
check '.extra.patches'             "$(jq_raw 'if (.extra.patches // null) == null then "absent" else "present" end' composer.json)" 'absent'
# NOTE: the bare mention of CI_ALLOW_DEV is not searched for (the kit's
# RequirementsTest.php READS it with getenv(), by design). What is searched for
# is its DEFINITION.
DEFINERS=$(grep_definers 'CI_ALLOW_DEV[[:space:]]*[:=]')
case "$DEFINERS" in
  '')            CIALLOW=0 ;;
  '<grep exit'*) CIALLOW=$DEFINERS ;;
  *)             CIALLOW=$(printf '%s\n' "$DEFINERS" | wc -l | tr -d ' ') ;;
esac
check 'files DEFINING CI_ALLOW_DEV' "$CIALLOW" '0'
[ "$CIALLOW" != "0" ] && printf '%s\n' "$DEFINERS" | sed 's/^/      | /'

# ------------------------------------- G6 kit files present ------------------
# `ValidationTest.php` is here because of the specification-correction rider
# [andres] 2026-08-21: "ValidationTest.php is added to the set of kit files
# watched by the gate." These are the three tests the kit ships, and this group
# asserts that they are PRESENT - nothing more. T-406's criterion was "0 lines
# deleted" in them; it never forbade adding to them. Until 2026-09-24 this
# comment said T-406 "forbids modifying them", which was wider than the task
# that made the rule: RequirementsTest.php had been extended twice by then, and
# InstallTest.php gained its first four lines that day (T-0635), the guard that
# stops its test site reporting usage to Drupal.org, which can only live inside
# the class.
group 'G6 - Starter kit files present (T-101) - 13/13'
for f in \
  recipe.yml \
  composer.json \
  recommended.yml \
  screenshot.webp \
  LICENSE.txt \
  README.md \
  .gitlab-ci.yml \
  .github/workflows/phpunit.yml \
  .tugboat/config.yml \
  .gitattributes \
  tests/src/Functional/InstallTest.php \
  tests/src/Functional/ValidationTest.php \
  tests/src/Kernel/RequirementsTest.php
do
  check "$f" "$(exists_file "$f")" 'present'
done

# ------------------------------------- G7 consistency with what is signed ----
group 'G7 - Process layer consistency with what is signed'
D011=$(grep_count_fixed 'D-011' specs/000-project/DECISIONS.md)
check 'D-011 in DECISIONS.md (>=1)' "$([ "${D011:-0}" -ge 1 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
NAME_REF=$(grep_count_fixed 'agora_transparency' composer.json)
check 'agora_transparency in composer.json (>=1)' "$([ "${NAME_REF:-0}" -ge 1 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'

# --------------------------------------------------- G8 packaging (D-015.2) --
# The only wall stopping the process layer (specs/, .claude/, CLAUDE.md) from
# travelling inside the release published on Drupal.org is the `export-ignore`
# in `.gitattributes` (D-015.2). If someone takes out a line, nobody finds out
# until the marketplace review.
#
# That is why this group does NOT grep `.gitattributes`: it RUNS `git archive`
# and looks at the resulting tarball, which is literally what Drupal.org
# packages. A grep would verify the text of the rule; this verifies its effect.
#
# SCOPE: the tree at HEAD. `git archive <commit>` reads the attributes of the
# commit itself, not of the working tree -- just like the Drupal.org packager,
# which archives a tag. Known consequence: an uncommitted edit to
# `.gitattributes` is not seen by this group; it is caught on the next commit
# (and in CI).
group 'G8 - Packaging: real contents of `git archive` (D-015.2)'

ARCHIVE_LIST=""
ARCHIVE_RC=1
if git rev-parse --verify HEAD >/dev/null 2>&1; then
  ARCHIVE_LIST=$(git archive --format=tar HEAD 2>/dev/null | tar -tf - 2>/dev/null)
  ARCHIVE_RC=$?
fi
if [ "$ARCHIVE_RC" -eq 0 ] && [ -n "$ARCHIVE_LIST" ]; then
  ENTRIES=$(printf '%s\n' "$ARCHIVE_LIST" | wc -l | tr -d ' ')
else
  # A failure to archive = a FAIL of the gate, never a skip. I-007: an empty
  # archive would pass every "does not contain" check through the back door.
  ENTRIES=0
fi
note "HEAD: $(git rev-parse --short HEAD 2>/dev/null || echo '<no git>') - entries in the tarball: $ENTRIES"
check 'git archive runnable (exit)'    "$ARCHIVE_RC" '0'
check 'entries in the tarball > 0'     "$([ "$ENTRIES" -gt 0 ] && echo 'yes' || echo 'no')" 'yes'

# --- must NOT travel: process layer, CI, tests and development tooling --------
for excluded in \
  'specs/' \
  '.claude/' \
  'CLAUDE.md' \
  'tests/' \
  '.github/' \
  '.gitlab-ci.yml' \
  '.tugboat/' \
  '.eslintrc.json' \
  '.gitignore' \
  '.mailmap'
do
  # Prefix anchored at the start: 'tests/' matches 'tests/...' and the directory
  # entry 'tests/'; 'CLAUDE.md' matches the exact file entry.
  EXC_RE=$(printf '%s' "$excluded" | sed 's/[.[\*^$\/]/\\&/g')
  # rc 1 = no such entry, which is the clean answer and counts as 0.
  # rc >= 2 printed nothing, and the `${HITS:-0}` these lines used to carry would
  # have turned that empty string into the number 0: a grep that never ran
  # PASSING this check, which is the exact defect T-316 repairs. The sentinel
  # makes it FAIL and names it.
  #
  # T-321(a)/(b): the count now comes from `wc -l`, so HITS can never be blank,
  # and the `:-0` defaults are GONE from both lines below on purpose. This is an
  # EXPECT-ZERO site - zero means PASS - which is precisely where I-031 says a
  # `:-0` is dangerous rather than safe: it would convert a blank into a green.
  # With `wc -l` producing the number and the sentinel producing the error, a
  # blank cannot arise; and if one ever did, an empty string is not "0", so the
  # check FAILS loudly instead of passing.
  HITS_MATCHED=$(printf '%s\n' "$ARCHIVE_LIST" | grep "^$EXC_RE" 2>/dev/null)
  HITS_RC=$?
  if [ "$HITS_RC" -ge 2 ]; then
    HITS="<grep exit $HITS_RC on the archive list>"
  elif [ "$HITS_RC" -eq 0 ]; then
    HITS=$(printf '%s\n' "$HITS_MATCHED" | wc -l | tr -d ' ')
  else
    HITS=0
  fi
  check "NOT packaged: $excluded" "$HITS" '0'
  if [ "$HITS" != "0" ]; then
    printf '%s\n' "$ARCHIVE_LIST" | grep "^$EXC_RE" | sed 's/^/      | LEAKS INTO THE RELEASE: /'
  fi
done

# --- must travel: the product the end user installs ---------------------------
# AGENTS.md is here on purpose (D-015.1): it is product, not process.
#
# T-1916: logo.png is here on purpose too, and the reason is the opposite of a
# tidy-up. It was packaged and referenced by no recipe, no config and no
# content, which is exactly what a binary looks like on its way out of a
# package - so the question "does it have a job?" was answered before the check
# was written, from the commit that added it (a754611): Drupal.org renders a
# 512x512 PNG named logo.png at the root of the default branch as the project
# icon, because the GitLab project avatar is unreachable on drupalcode from
# both the API and the UI, and Project Browser uses the same file as the card
# image a site builder sees while CHOOSING what to install. It now has a
# provenance row in content/MEDIA-LICENCES.md like every other binary this
# package ships, and this line is what stops it being dropped by somebody who
# finds it referenced by nothing.
for included in \
  AGENTS.md \
  recipe.yml \
  composer.json \
  recommended.yml \
  screenshot.webp \
  logo.png \
  LICENSE.txt \
  LICENCE-MANIFEST.md
do
  # T-321(a), house rule 1. Expect-PRESENT site: the `${FOUND:-0}` further down
  # STAYS, because here zero means FAIL (absent) - the safe direction of I-031.
  FOUND_MATCHED=$(printf '%s\n' "$ARCHIVE_LIST" | grep -x -F "$included" 2>/dev/null)
  FOUND_RC=$?
  if [ "$FOUND_RC" -eq 0 ]; then
    FOUND=$(printf '%s\n' "$FOUND_MATCHED" | wc -l | tr -d ' ')
  else
    FOUND=0
  fi
  if [ "$FOUND_RC" -ge 2 ]; then
    check "packaged: $included" "<grep exit $FOUND_RC on the archive list>" 'present'
  else
    check "packaged: $included" "$([ "${FOUND:-0}" -ge 1 ] && echo 'present' || echo 'absent')" 'present'
  fi
done

# -------------------------------------- G9 - claims-match-sources (2026-09-06) --
# The only group here whose subject is CLAUDE.md rather than the package. It runs
# in THIS runner and not in gate-a-wave3.sh because it needs no network, no
# container and no database: it reads four files and exits in well under a second,
# and wave 3 takes about 35 minutes. A guard against stale prose that is only
# affordable half an hour at a time is a guard that gets skipped.
group 'G9 - claims-match-sources (CLAUDE.md against this repository)'
INV=tests/bin/claims-match-sources
if [ -x "$INV" ]; then
  INV_OUT=$("$INV" 2>&1); INV_RC=$?
  CMP_N=$(printf '%s\n' "$INV_OUT" | grep -E '^comparisons: [0-9]+ ' | tail -1 | grep -oE '[0-9]+' | head -1)
  UNC_N=$(printf '%s\n' "$INV_OUT" | grep -E '^NOT CHECKED - [0-9]+ ' | tail -1 | grep -oE '[0-9]+' | head -1)
  FIG_N=$(printf '%s\n' "$INV_OUT" | grep -E '^trace figures claimed: +[0-9]+$' | tail -1 | grep -oE '[0-9]+' | head -1)
  note "$(printf '%s' "$INV_OUT" | grep -E '^(claims extracted|trace figures|comparisons|mismatches)' | tr '\n' ' ')"
  check 'claims-match-sources (exit)'      "$INV_RC" '0'
  # check_positive does not exist in this runner; these three reproduce it. All
  # three denominators are asserted for the reason I-028 gives: a reader that
  # compared nothing, a reader whose NOT CHECKED list had been deleted, and a
  # reader whose trace-figures tables had been emptied would each print
  # "mismatches: 0" and pass by construction.
  check 'claims-match-sources (comparisons > 0)' \
    "$([ "${CMP_N:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'claims-match-sources (unchecked named > 0)' \
    "$([ "${UNC_N:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  # The gate NEVER passes --online, so the figures themselves are not read here.
  # What is asserted is that there are still figures to read: emptying the two
  # trace-figures tables in CLAUDE.md would leave `--online` comparing nothing
  # and reporting the silence as agreement, which is the defect the online half
  # was built to end, arriving through its own back door.
  check 'claims-match-sources (trace figures claimed > 0)' \
    "$([ "${FIG_N:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
else
  check 'claims-match-sources present'     "$(trunc "$INV" 28)" 'present'
  check 'claims-match-sources (comparisons > 0)'     'not run' 'yes'
  check 'claims-match-sources (unchecked named > 0)' 'not run' 'yes'
  check 'claims-match-sources (trace figures claimed > 0)' 'not run' 'yes'
fi

# ------------------------------------------ G10 - executable-bit (2026-09-06) --
# The mode a shebang file is COMMITTED at, read from the git index. It is in this
# runner rather than wave 3 because it is pure git: no network, no container, no
# database, well under a second. See tests/bin/executable-bit for why the index
# and never the filesystem, and for the two 100644 files it found on its first
# run here.
#
# ⚠️ THIS GROUP IS THE ONE THAT MUST NOT USE `[ -x "$INV" ]`, and that is the
# whole point of it. Every other group in both runners guards its invariant with
# that test, which asks the FILESYSTEM: on this Windows checkout it answers TRUE
# for a file committed 100644, and on the runner it answers FALSE. Guarding the
# executable-bit check itself that way would mean that the one defect it exists
# to catch - a script committed without its bit - could disable it, silently,
# in exactly the environment where the defect is real. `[ -r ]` plus `bash` runs
# it identically on both sides, so if this script ever loses its own bit it
# still executes and reports ITSELF by name and mode, which is a red naming the
# file rather than a red naming a missing check.
group 'G10 - executable-bit (shebang files are committed 100755)'
INV=tests/bin/executable-bit
if [ -r "$INV" ]; then
  INV_OUT=$(bash "$INV" 2>&1); INV_RC=$?
  EB_EXAMINED=$(printf '%s\n' "$INV_OUT" | grep -oE '^examined: [0-9]+' | tail -1 | grep -oE '[0-9]+')
  EB_SCRIPTS=$(printf '%s\n' "$INV_OUT" | grep -oE '^scripts:  [0-9]+' | tail -1 | grep -oE '[0-9]+')
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(examined|scripts|findings):' | tr '\n' ' ')"
  check 'executable-bit (exit)'                "$INV_RC" '0'
  # Two denominators, not one question asked twice: `examined` comes from
  # `git ls-files` and stays positive even if the shebang selector broke;
  # `scripts` comes from the selector and can only be positive if first lines
  # were actually read. A selector that matched nothing would otherwise print
  # "0 findings" and pass by construction (I-028).
  check 'executable-bit (files examined > 0)' \
    "$([ "${EB_EXAMINED:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'executable-bit (shebang scripts > 0)' \
    "$([ "${EB_SCRIPTS:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  if [ "$INV_RC" -ne 0 ]; then
    printf '%s\n' "$INV_OUT" | grep -E '^  [^ ]+:[0-9<]' | sed 's/^/  /'
  fi
else
  check 'executable-bit present'               "$(trunc "$INV" 28)" 'present'
  check 'executable-bit (files examined > 0)'  'not run' 'yes'
  check 'executable-bit (shebang scripts > 0)' 'not run' 'yes'
fi

# ------------------------------------- G11 - packaged-claims (T-0608, 2026-09-21) --
# The figures the PACKAGED prose states, against the things that produce them. G9
# above is the same technique aimed at CLAUDE.md, which is process and does not
# ship; this one is aimed at README.md, the licence manifest, the shipped
# accessibility statement, recipe.yml, composer.json and recommended.yml, which do.
# The two subjects are deliberately disjoint, so neither guard's green says anything
# about the other's file set.
#
# It is in this runner and not wave 3 because it is pure git plus grep: no network,
# no container, no database, well under a second.
#
# FIVE DENOMINATORS AND NOT ONE, for the reason I-028 gives and which this family of
# checks keeps rediscovering. Each of these would print "findings: 0" and pass by
# construction: an extractor whose patterns stopped matching, a run that opened no
# file, a run that compared nothing, a one-copy register somebody emptied, and a
# NOT CHECKED list somebody deleted an entry into rather than answering. The exit
# status alone distinguishes none of them from a clean package.
group 'G11 - packaged-claims (the packaged prose against what it describes)'
INV=tests/bin/packaged-claims
if [ -r "$INV" ]; then
  INV_OUT=$(bash "$INV" 2>&1); INV_RC=$?
  PC_FILES=$(printf '%s\n' "$INV_OUT" | grep -oE '^files opened: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PC_CLAIMS=$(printf '%s\n' "$INV_OUT" | grep -oE '^claims extracted: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PC_CMP=$(printf '%s\n' "$INV_OUT" | grep -oE '^comparisons: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PC_ONE=$(printf '%s\n' "$INV_OUT" | grep -oE '^one-copy checks: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PC_NC=$(printf '%s\n' "$INV_OUT" | grep -oE '^not checked: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(files opened|claims extracted|comparisons|one-copy checks|findings):' | tr -s ' ' | tr '\n' ' ')"
  check 'packaged-claims (exit)'                      "$INV_RC" '0'
  check 'packaged-claims (files opened > 0)' \
    "$([ "${PC_FILES:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'packaged-claims (claims extracted > 0)' \
    "$([ "${PC_CLAIMS:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'packaged-claims (comparisons > 0)' \
    "$([ "${PC_CMP:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'packaged-claims (one-copy checks > 0)' \
    "$([ "${PC_ONE:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'packaged-claims (unchecked named > 0)' \
    "$([ "${PC_NC:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  if [ "$INV_RC" -ne 0 ]; then
    printf '%s\n' "$INV_OUT" | grep -E 'MISMATCH|EMPTY SIDE|WRONG HOME|MISSING HOME|TWO COPIES|FATAL' | sed 's/^/  /'
  fi
else
  check 'packaged-claims present'                 "$(trunc "$INV" 28)" 'present'
  check 'packaged-claims (files opened > 0)'      'not run' 'yes'
  check 'packaged-claims (claims extracted > 0)'  'not run' 'yes'
  check 'packaged-claims (comparisons > 0)'       'not run' 'yes'
  check 'packaged-claims (one-copy checks > 0)'   'not run' 'yes'
  check 'packaged-claims (unchecked named > 0)'   'not run' 'yes'
fi

# ------------------------------- G12 - ported-copies (T-0622, 2026-09-21) --
# THE DIRECTION THIS REPOSITORY HAD NO RECORD OF AT ALL.
#
# `agora_theme` has carried tests/bin/shared-invariants.manifest since 2026-08-24
# for the files it copied FROM here, with a local detector and, since D-063, one
# that reads this repository over the API. Files travel the other way too:
# tests/bin/executable-bit and tests/bin/preflight were authored in the theme and
# ported here on 2026-09-06, and tests/bin/packaged-claims took 63 lines from the
# theme's tests/bin/claims-match-readme on 2026-09-21. Until this group existed,
# nothing in this repository knew any of that. The only provenance was a sentence
# in a commit message, which is read once, by the person who wrote it.
#
# ⚠️ THE HANDED LIST WAS TWO FILES AND THE ANSWER IS THREE. The third was found
# by comparing every script in both tests/bin/ directories against every script in
# the other and reading the BAND of shared long lines - 15-24 is house style,
# 37-65 is shared substance - then settling DIRECTION by `git log --reverse` on
# both paths, never by reading the code. The method and its control are written
# into tests/bin/ported-from-theme.manifest.
#
# IT IS IN THIS RUNNER because it is a hash and a grep: no network, no container,
# no database, well under a second.
#
# SIX DENOMINATORS AND NOT ONE, for the reason I-028 gives. Each of these prints
# "findings: 0" and passes by construction: a manifest parsed to nothing, a walk
# that hashed nothing, a status column nobody compared, a role column checked
# against runners that could not be read, an emptied DECLARED_RECORDS list, and an
# emptied DECLARED_COMMITS list. The exit status alone separates none of them from
# a clean tree.
group 'G12 - ported-copies (the copies taken FROM agora_theme, against their record)'
INV=tests/bin/ported-copies
if [ -x "$INV" ]; then
  INV_OUT=$("$INV" 2>&1); INV_RC=$?
  PF_CMP=$(printf '%s\n' "$INV_OUT" | grep -oE '^compared: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PF_VER=$(printf '%s\n' "$INV_OUT" | grep -oE '^verified: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PF_STA=$(printf '%s\n' "$INV_OUT" | grep -oE '^status: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PF_ROL=$(printf '%s\n' "$INV_OUT" | grep -oE '^roles: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  PF_COM=$(printf '%s\n' "$INV_OUT" | grep -oE '^commits: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(compared|verified|status|roles|commits|findings):' | tr -s ' ' | tr '\n' ' ')"
  check 'ported-copies (exit)'                    "$INV_RC" '0'
  check 'ported-copies (records compared > 0)' \
    "$([ "${PF_CMP:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  # EVERY record verified, not merely "some". `verified` counts local hashes
  # re-derived AND matching, so a copy edited here lowers it while `compared`
  # stays put: the two numbers are equal exactly when nothing has been edited,
  # and comparing them is what a bare "> 0" would miss.
  check 'ported-copies (every local copy verified)' "$PF_VER" "$PF_CMP"
  check 'ported-copies (status labels checked > 0)' \
    "$([ "${PF_STA:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'ported-copies (roles checked > 0)' \
    "$([ "${PF_ROL:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'ported-copies (source commits > 0)' \
    "$([ "${PF_COM:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  if [ "$INV_RC" -ne 0 ]; then
    printf '%s\n' "$INV_OUT" | grep -E '^  [^ ]+:[a-z_-]+ ' | sed 's/^/  /'
  fi
else
  check 'ported-copies present'                     "$(trunc "$INV" 28)" 'present'
  check 'ported-copies (records compared > 0)'      'not run' 'yes'
  check 'ported-copies (every local copy verified)' 'not run' '<not run, and that is a failure>'
  check 'ported-copies (status labels checked > 0)' 'not run' 'yes'
  check 'ported-copies (roles checked > 0)'         'not run' 'yes'
  check 'ported-copies (source commits > 0)'        'not run' 'yes'
fi

# -------------------------------- G13 - ported-drift (T-0622, 2026-09-21) --
# THE ONLY GROUP IN THIS RUNNER THAT TOUCHES THE NETWORK.
#
# WHAT IT CLOSES. G12 answers "has a copy been edited HERE". It cannot see whether
# `agora_theme` has changed a source file since the copy was taken, and the cost of
# that blindness is measured rather than feared: the theme's own equivalent printed
# CLEAN for four weeks while 125 genuinely-absent lines accumulated upstream in a
# file it was watching. ported-drift reads the other repository over the drupalcode
# API, anonymously, so this group runs on every push with nothing checked out but
# this tree. No token: /trace answers 401 anonymously, which is why the API is read
# and not a job log.
#
# BLOCKING ON PROVENANCE, REPORTING ON DELTA, and the exit check below is where
# that shape lives. Exit 1 - broken provenance, or a delta left standing past the
# 14-day cap - FAILS this group, because every one of those is fixable by a commit
# in this repository. Exit 2 - NOT READ - does not, for the same reason a raw delta
# does not: a runner that cannot reach drupalcode cannot be fixed by a commit here
# either, and a gate that fails for a reason its author cannot address is the gate
# somebody makes permissive inside a week (D-023(5)).
#
# ⚠️ AND THE DELTA MUST STAY DATA FOR A REASON SPECIAL TO THIS DIRECTION: all three
# records are `adapted`, by up to +836/-437 lines, because they were rewritten for a
# different subject on arrival. A non-zero delta is the NORMAL state here, so
# failing on one would mean failing permanently.
#
# WHAT STOPS EXIT 2 BECOMING A SILENT PERMANENT SKIP is the third check. The script
# prints three denominators - compared, read, unread - and this group asserts
# read + unread == compared, so a walk that quietly examined fewer records than the
# manifest offered FAILS rather than reporting a tidy zero (I-028).
#
# THE CAP IS BOUND IN THE FIFTH CHECK. --max-age-days exists so each branch of the
# script can be falsified; this runner never passes it, and asserting the printed
# cap reads `14 days (default)` means the number cannot be loosened from the call
# site without moving a check a human has to look at. CLAUDE.md's own PINNED_VARS
# lesson is the precedent: a gate-critical value guarded by nothing is a value that
# changes.
group 'G13 - ported-drift (NETWORK - the copies against agora_theme, over the API)'
INV=tests/bin/ported-drift
if [ -x "$INV" ]; then
  INV_OUT=$("$INV" 2>&1); INV_RC=$?
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(cap|compared|read|verified|behind|findings|network):' | tr '\n' ' ')"

  # I-027: grep has THREE exit states. rc >= 2 is grep failing and must never read
  # as "the line is absent" - the two need different remedies.
  PD_READLINE=$(printf '%s\n' "$INV_OUT" | grep -E '^read:[[:space:]]' 2>/dev/null)
  PD_RC=$?
  if [ "$PD_RC" -ge 2 ]; then
    PD_READ="<grep exit $PD_RC>"; PD_UNREAD="<grep exit $PD_RC>"
  else
    PD_READ=$(printf '%s' "$PD_READLINE" | sed -nE 's/^read:[[:space:]]+([0-9]+) read,.*/\1/p')
    PD_UNREAD=$(printf '%s' "$PD_READLINE" | sed -nE 's/^read:.*, ([0-9]+) NOT READ.*/\1/p')
  fi
  PD_CMP=$(printf '%s\n' "$INV_OUT" | grep -oE '^compared: +[0-9]+' | tail -1 | grep -oE '[0-9]+')

  PD_NET_LINE=$(printf '%s\n' "$INV_OUT" | grep -E '^network:[[:space:]]+(NOT )?READ' 2>/dev/null)
  PD_NET_RC=$?
  if [ "$PD_NET_RC" -ge 2 ]; then PD_NET="<grep exit $PD_NET_RC>"
  elif [ -n "$PD_NET_LINE" ]; then PD_NET='named'
  else PD_NET='absent'; fi

  PD_CAP_LINE=$(printf '%s\n' "$INV_OUT" | grep -E '^cap:[[:space:]]' 2>/dev/null)
  PD_CAP_RC=$?
  if [ "$PD_CAP_RC" -ge 2 ]; then PD_CAP="<grep exit $PD_CAP_RC>"
  else PD_CAP=$(printf '%s' "$PD_CAP_LINE" | sed -nE 's/^cap:[[:space:]]+(.*)$/\1/p'); fi

  # Exit 2 is the third state and is tolerated; exit 1 is a finding and is not.
  case "$INV_RC" in
    0|2) PD_EXIT='no finding' ;;
    *)   PD_EXIT="exit $INV_RC" ;;
  esac

  # The SUM, not the two numbers separately: a walk that skipped records reports a
  # smaller total than the manifest offered, and that is the shape this check
  # exists to catch.
  if [ -n "$PD_READ" ] && [ -n "$PD_UNREAD" ]; then
    PD_SUM=$((PD_READ + PD_UNREAD)) 2>/dev/null || PD_SUM='<unparsed>'
  else
    PD_SUM='<unparsed>'
  fi

  check 'ported-drift (exit 0 clean | 2 NOT READ)' "$PD_EXIT" 'no finding'
  check 'ported-drift (records compared > 0)' \
    "$([ "${PD_CMP:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'ported-drift (read + unread = compared)'  "$PD_SUM" "$PD_CMP"
  check 'ported-drift (network state named)'       "$PD_NET" 'named'
  check 'ported-drift (age cap, never from here)'  "$PD_CAP" '14 days (default)'
  [ "$INV_RC" -eq 1 ] && printf '%s\n' "$INV_OUT" | grep -E ':(source_commit|source_sha256|source_path|delta)[[:space:]]' | sed 's/^/  /'
  [ "$INV_RC" -eq 2 ] && printf '  %sNOT READ is a third state, not a pass - see the denominators above.%s\n' "$C_BAD" "$C_OFF"
else
  check 'ported-drift present'                     "$(trunc "$INV" 28)" 'present'
  check 'ported-drift (records compared > 0)'      'not run' 'yes'
  check 'ported-drift (read + unread = compared)'  '<not run>' '<not run, and that is a failure>'
  check 'ported-drift (network state named)'       'absent'  'named'
  check 'ported-drift (age cap, never from here)'  'absent'  '14 days (default)'
fi

# ------------------------------- G14 - mirror-streak (T-0623, 2026-09-21) --
# THE MIRROR'S CONCLUSION, PUSHED RATHER THAN PULLED.
#
# tests/bin/watch-gate has read the GitHub mirror since 2026-09-19 and reads it
# well. It runs when somebody types the command, which is the same detection
# property as the email that had already been missed nine times in a row over
# three weeks. This group is the half that runs unasked.
#
# ⚠️ THE STATED REASON THIS COULD NOT BE AN INVARIANT WAS HALF FALSE, and the
# false half was the load-bearing one. watch-gate's comment says such a check
# "would need the network and a GitHub token inside `agora-invariants`". The
# mirror is a PUBLIC repository and GitHub's Actions API answers a public
# repository ANONYMOUSLY - measured before the script was written, three
# endpoints, HTTP 200 on all three, no credentials of any kind. The network
# half stands and is answered the way G13 answers it: exit 2 is the third
# state, it is tolerated here, and it is never a pass.
#
# D-020 IS UNCHANGED. A red mirror does not fail this gate. What fails it is
# provenance - an undeclared workflow, a slug that answers 404 - and a streak
# that has stood past the 14-day cap with nothing in this repository saying
# anything about it. Every one of those is fixable by a commit HERE, which is
# the property D-023(5) exists to protect.
#
# SIX DENOMINATORS, and the first two are the ones that matter under I-028: an
# emptied workflow declaration and an unread API both print "findings: 0". The
# declared count is OFFLINE, so it survives a runner with no network and is the
# figure that separates "nothing to check" from "nothing was checked".
group 'G14 - mirror-streak (NETWORK - the GitHub mirror, read anonymously)'
INV=tests/bin/mirror-streak
if [ -x "$INV" ]; then
  INV_OUT=$("$INV" 2>&1); INV_RC=$?
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(scope|cap|examined|streak|findings|network):' | tr '\n' ' ')"

  # I-027: grep has THREE exit states. rc >= 2 is grep failing and must never
  # read as "the line is absent" - the two need different remedies.
  MS_DECL_LINE=$(printf '%s\n' "$INV_OUT" | grep -E '^scope:[[:space:]]' 2>/dev/null)
  MS_DECL_RC=$?
  if [ "$MS_DECL_RC" -ge 2 ]; then
    MS_DECL="<grep exit $MS_DECL_RC>"
  else
    MS_DECL=$(printf '%s' "$MS_DECL_LINE" | sed -nE 's/^scope:.*, ([0-9]+) workflow\(s\) declared.*/\1/p')
  fi

  MS_EXAM=$(printf '%s\n' "$INV_OUT" | grep -oE '^examined: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  MS_STREAK=$(printf '%s\n' "$INV_OUT" | grep -oE '^streak: +[0-9]+' | tail -1 | grep -oE '[0-9]+')

  MS_NET_LINE=$(printf '%s\n' "$INV_OUT" | grep -E '^network:[[:space:]]+(NOT )?READ' 2>/dev/null)
  MS_NET_RC=$?
  if [ "$MS_NET_RC" -ge 2 ]; then MS_NET="<grep exit $MS_NET_RC>"
  elif [ -n "$MS_NET_LINE" ]; then MS_NET='named'
  else MS_NET='absent'; fi

  MS_CAP_LINE=$(printf '%s\n' "$INV_OUT" | grep -E '^cap:[[:space:]]' 2>/dev/null)
  MS_CAP_RC=$?
  if [ "$MS_CAP_RC" -ge 2 ]; then MS_CAP="<grep exit $MS_CAP_RC>"
  else MS_CAP=$(printf '%s' "$MS_CAP_LINE" | sed -nE 's/^cap:[[:space:]]+(.*)$/\1/p'); fi

  # Exit 2 is the third state and is tolerated; exit 1 is a finding and is not.
  case "$INV_RC" in
    0|2) MS_EXIT='no finding' ;;
    *)   MS_EXIT="exit $INV_RC" ;;
  esac

  check 'mirror-streak (exit 0 clean | 2 NOT READ)' "$MS_EXIT" 'no finding'
  check 'mirror-streak (workflows declared > 0)' \
    "$([ "${MS_DECL:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  # NAMED rather than "> 0": a runner with no network legitimately examines 0
  # runs, and that state is reported by the NOT READ banner and the network
  # line, not by this figure. What must never happen is the figure going blank,
  # because a blank reads exactly like a zero and a zero reads like a clean
  # mirror.
  check 'mirror-streak (runs examined, named)' \
    "$([ -n "$MS_EXAM" ] && echo 'named' || echo 'absent')" 'named'
  check 'mirror-streak (streak stated)' \
    "$([ -n "$MS_STREAK" ] && echo 'stated' || echo 'absent')" 'stated'
  check 'mirror-streak (network state named)'      "$MS_NET" 'named'
  check 'mirror-streak (age cap, never from here)' "$MS_CAP" '14 days (default)'
  [ "$INV_RC" -eq 1 ] && printf '%s\n' "$INV_OUT" | grep -E '^  (workflows|slug|streak):' | sed 's/^/  /'
  [ "$INV_RC" -eq 2 ] && printf '  %sNOT READ is a third state, not a pass - see the denominators above.%s\n' "$C_BAD" "$C_OFF"
  if [ "$INV_RC" -eq 0 ] && [ "${MS_STREAK:-0}" -gt 0 ] 2>/dev/null; then
    printf '  %sTHE MIRROR IS RED (%s consecutive non-success run(s)) and this does NOT fail the\n' "$C_BAD" "$MS_STREAK"
    printf '  gate - D-020 keeps it informative. It is printed here so that it is read.%s\n' "$C_OFF"
  fi
else
  check 'mirror-streak (exit 0 clean | 2 NOT READ)' "$(trunc "$INV" 28)" 'no finding'
  check 'mirror-streak (workflows declared > 0)'    'not run' 'yes'
  check 'mirror-streak (runs examined, named)'      'absent'  'named'
  check 'mirror-streak (streak stated)'             'absent'  'stated'
  check 'mirror-streak (network state named)'       'absent'  'named'
  check 'mirror-streak (age cap, never from here)'  'absent'  '14 days (default)'
fi

# ------------------------------- G15 - no-usage-reporting (T-0635, 2026-09-24) --
# NO SITE THIS SUITE INSTALLS MAY REPORT USAGE TO DRUPAL.ORG.
#
# The recipe installs `update` and `automated_cron`, so every functional test
# site ran cron on its first web request and sent updates.drupal.org a site_key
# and its module list - measured against a recording stub: 18 requests from 9
# sites per run, and CI runs the suite twice per push. The fix is a trait in
# tests/src that pins update.settings:fetch.url to a closed loopback port before
# Drupal is installed. A guard that the next test class can silently omit is not
# a guard, so this group fails the gate when one does.
#
# It is in this runner because it is find, git and awk: no network, no
# container, no database, well under a second.
#
# FOUR DENOMINATORS BESIDE THE EXIT, for the reason I-028 gives: a scan that
# opened no file, a parser that stopped recognising BrowserTestBase, a count of
# guarded classes read from nowhere, and a pin read from nowhere would each
# print "findings: 0" exactly as a clean tree does.
group 'G15 - no-usage-reporting (no test site reports usage to Drupal.org)'
INV=tests/bin/no-usage-reporting
if [ -r "$INV" ]; then
  INV_OUT=$(bash "$INV" 2>&1); INV_RC=$?
  NU_FILES=$(printf '%s\n' "$INV_OUT" | grep -oE '^scanned: +[0-9]+' | tail -1 | grep -oE '[0-9]+')
  NU_FUNC=$(printf '%s\n' "$INV_OUT" | sed -nE 's/^guarded: +[0-9]+ of ([0-9]+) concrete.*/\1/p' | tail -1)
  NU_GUARD=$(printf '%s\n' "$INV_OUT" | sed -nE 's/^guarded: +([0-9]+) of [0-9]+ concrete.*/\1/p' | tail -1)
  NU_LOOP=$(printf '%s\n' "$INV_OUT" | sed -nE 's/^trait pin: .*\(loopback: (yes|no)\).*/\1/p' | tail -1)
  note "$(printf '%s\n' "$INV_OUT" | grep -E '^(scanned|guarded|trait pin|findings):' | tr -s ' ' | tr '\n' ' ')"
  check 'no-usage-reporting (exit)'            "$INV_RC" '0'
  check 'no-usage-reporting (files > 0)' \
    "$([ "${NU_FILES:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'no-usage-reporting (functional > 0)' \
    "$([ "${NU_FUNC:-0}" -gt 0 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
  check 'no-usage-reporting (all guarded)' \
    "$([ -n "$NU_FUNC" ] && [ "$NU_GUARD" = "$NU_FUNC" ] && echo 'yes' || echo 'no')" 'yes'
  check 'no-usage-reporting (pin is loopback)' "${NU_LOOP:-absent}" 'yes'
  if [ "$INV_RC" -ne 0 ]; then
    printf '%s\n' "$INV_OUT" | grep -E '^  [^ ]+:[0-9]+  |^FAILURE|^FATAL' | sed 's/^/  /'
  fi
else
  check 'no-usage-reporting (exit)'            "$(trunc "$INV" 28)" '0'
  check 'no-usage-reporting (files > 0)'       'not run' 'yes'
  check 'no-usage-reporting (functional > 0)'  'not run' 'yes'
  check 'no-usage-reporting (all guarded)'     'not run' 'yes'
  check 'no-usage-reporting (pin is loopback)' 'not run' 'yes'
fi

# ----------------------------------------------------------------- summary ---
printf '\n=========================================================================================================\n'
if [ "$M" -eq 0 ]; then
  printf '%s%d checks - %d failures%s\n' "$C_OK" "$N" "$M" "$C_OFF"
else
  printf '%s%d checks - %d failures%s\n' "$C_BAD" "$N" "$M" "$C_OFF"
fi
# -- the runner grades itself against its own header (T-1911) -----------------
# CLAUDE.md carried this as an open gap with NO OWNER for weeks, in its own
# words: "nothing yet asserts that a GATE-CLAIM line matches the total its own
# runner PRINTS. Five of the ten offline comparisons are therefore prose against
# prose." tests/bin/claims-match-sources reads the header line of this file and
# the sentence in CLAUDE.md and compares those two - neither of which is the
# arithmetic that just ran. The comparison below is the missing side of it: the
# declaration against the count.
#
# WHY IT IS WORTH A GUARD RATHER THAN A HABIT. The declaration lives about three
# lines from the arithmetic that derives it, which is why it has usually been
# right; but "usually right" is the state every stale figure in this project was
# in the day before it went stale, and this whole family of checks exists
# because a number written in two places goes wrong in one place first.
#
# IT MUST NOT BE ABLE TO PASS BY NOT COUNTING. An unset or zero N agreeing with
# an unset or zero declaration is exactly the I-028 shape - a comparison whose
# degenerate value is "agreement" - so both sides must be positive integers
# BEFORE they are compared, and every unreadable state below is a FAILURE and
# never a skip (I-007).
#
# IT IS DELIBERATELY NOT A NUMBERED CHECK. A check counted by the very total it
# verifies reads as circular, and keeping it out of N means the commit closing
# this gap moves no GATE-CLAIM line and no figure in CLAUDE.md - so it can be
# pushed, and watched failing, on its own.
#
# The pattern below is `^#[[:space:]]*GATE-CLAIM:` rather than `^# GATE-CLAIM:`
# on purpose: it is the shape claims-match-sources.py already matches, and two
# guards reading one line by two different patterns is a disagreement waiting to
# happen.
SELF="$SCRIPT_DIR/$(basename -- "$0")"
SELF_FAIL=0
SELF_WHY=""
DECLARED=""
CLAIM_LINES=$(grep -E '^#[[:space:]]*GATE-CLAIM:' "$SELF" 2>/dev/null)
CLAIM_RC=$?
if [ "$CLAIM_RC" -ge 2 ]; then
  # I-027: grep has THREE exit states. rc >= 2 is grep itself failing - an
  # unreadable file, a broken pattern - and must never read as "no match".
  SELF_FAIL=1
  SELF_WHY="grep exited $CLAIM_RC reading $SELF; the declaration was not read"
elif [ "$CLAIM_RC" -ne 0 ]; then
  SELF_FAIL=1
  SELF_WHY="$SELF carries no GATE-CLAIM line at all"
else
  # `wc -l` over the matched lines and never `grep -c`: house rule T-321(a),
  # and this value IS compared.
  CLAIM_COUNT=$(printf '%s\n' "$CLAIM_LINES" | wc -l | tr -d '[:space:]')
  DECLARED=$(printf '%s\n' "$CLAIM_LINES" \
    | sed -n 's/.*checks=\([0-9][0-9]*\).*/\1/p' | tail -1)
  if [ "${CLAIM_COUNT:-0}" -ne 1 ]; then
    SELF_FAIL=1
    SELF_WHY="$SELF carries $CLAIM_COUNT GATE-CLAIM lines, expected exactly 1"
  elif [ -z "$DECLARED" ]; then
    SELF_FAIL=1
    SELF_WHY="the GATE-CLAIM line in $SELF has no checks=<number> field"
  elif [ "$DECLARED" -eq 0 ] 2>/dev/null; then
    SELF_FAIL=1
    SELF_WHY="the GATE-CLAIM line declares checks=0, which no real run satisfies"
  elif [ "${N:-0}" -eq 0 ] 2>/dev/null; then
    SELF_FAIL=1
    SELF_WHY="this run counted 0 checks; a run that counted nothing found nothing"
  elif [ "$N" -ne "$DECLARED" ]; then
    SELF_FAIL=1
    SELF_WHY="printed $N check(s) and the header declares checks=$DECLARED"
  fi
fi

if [ "$SELF_FAIL" -eq 0 ]; then
  printf '%sGATE-CLAIM self-check: printed %d = declared checks=%s%s\n' \
    "$C_OK" "$N" "$DECLARED" "$C_OFF"
else
  printf '%sGATE-CLAIM self-check: FAIL - %s%s\n' "$C_BAD" "$SELF_WHY" "$C_OFF"
fi
printf '=========================================================================================================\n'
if [ "$SELF_FAIL" -ne 0 ]; then
  printf '\nFAILURE: this runner disagrees with its own GATE-CLAIM line.\n'
  printf '  %s\n' "$SELF_WHY"
  printf 'That line is a claim about the arithmetic in THIS file. The commit that\n'
  printf 'moves a check count moves it in the same commit; then\n'
  printf 'tests/bin/claims-match-sources binds CLAUDE.md to the line, and the\n'
  printf 'check above binds the line to the count that actually ran.\n'
fi

[ "$M" -eq 0 ] && [ "$SELF_FAIL" -eq 0 ] && exit 0
exit 1
