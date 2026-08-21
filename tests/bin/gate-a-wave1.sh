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

grep_count() { # <ERE> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s absent>' "$_f"; return; }
  printf '%s' "$(grep -cE "$1" "$_f" 2>/dev/null || true)"
}

grep_count_fixed() { # <fixed string> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s absent>' "$_f"; return; }
  printf '%s' "$(grep -cF "$1" "$_f" 2>/dev/null || true)"
}

exists_file() { [ -f "$1" ] && printf 'present' || printf 'absent'; }
exists_dir()  { [ -d "$1" ] && printf 'present' || printf 'absent'; }

# Files in scope for filesystem scans: the packaged tree, minus VCS/build dirs.
scan_files() {
  find . \
    -path ./.git -prune -o \
    -path ./vendor -prune -o \
    -path ./node_modules -prune -o \
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
# DEBT WITH AN OWNER AND AN EXIT GATE - THIS IS NOT A RELAXED CHECK.
#
# Expected = PRESENT, on purpose. Rider of the `blank` theme, signed by [andres]
# on 2026-08-21 (specs/000-proyecto/DECISIONES.md, "Riders on wave 1"):
#
#   "`blank` and the `extra.drupal-site-template` block are kept until unit 002.
#    T-103 deletes the three `_comment` arrays and `GET-STARTED.md`, but NOT the
#    `extra` block. [...] the affected check is adjusted to the specification in
#    force for unit 001 (`blank` and the `extra` block are expected to be
#    PRESENT, with a reference to this rider)."
#
# Why this is the specification in force and not an excuse: `recipe.yml` today
# installs the `blank` theme (`install: - blank`) and sets it as the default
# theme (`system.theme.default: 'blank'`). That theme is NOT versioned: it is
# MANUFACTURED by drupal/site_template_helper reading exactly this `extra`
# block. Deleting it today would leave `recipe.yml` pointing at a non-existent
# theme and the template would NOT install. In unit 001 the block is a hard
# requirement; expecting 'absent' was describing the end state of unit 002, not
# the contract in force.
#
# WHO REVERTS IT: the UNIT 002 task that performs the atomic
# `drupal/agora_theme` change (D-014, option B) in ONE SINGLE COMMIT:
#     delete .extra["drupal-site-template"] from composer.json
#   + add "drupal/agora_theme" to .require
#   + change `- blank` to `- agora_theme` in `install:` of recipe.yml
#   + change system.theme.default to 'agora_theme'
# That same task MUST set 'absent' back here. This line is the tripwire: the
# moment someone deletes the `extra` block without touching the gate, this
# check goes RED and the change does not pass. It is deliberate (I-020: "known
# debt is a task with an owner and an exit gate, never a tolerated red").
#
# FORBIDDEN: changing this expected value to 'absent' -- or deleting the check --
# without executing the four steps above in the same commit.
# ---------------------------------------------------------------------------
check '.extra["drupal-site-template"]' "$(jq_raw '.extra["drupal-site-template"] // "absent" | if . == "absent" then . else "present" end' composer.json)" 'present'
note 'expected PRESENT by the `blank` rider [andres] 2026-08-21; reverted by the unit 002 theme task (atomic change)'
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
INFOYML=$(scan_files | grep -c '\.info\.yml$' || true)
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
# RequirementsTest.php READS it with getenv() and cannot be touched - T-406).
# What is searched for is its DEFINITION.
CIALLOW=$(scan_files | xargs grep -lIE 'CI_ALLOW_DEV[[:space:]]*[:=]' 2>/dev/null | wc -l | tr -d ' ')
check 'files DEFINING CI_ALLOW_DEV' "$CIALLOW" '0'
[ "$CIALLOW" != "0" ] && scan_files | xargs grep -lIE 'CI_ALLOW_DEV[[:space:]]*[:=]' 2>/dev/null | sed 's/^/      | /'

# ------------------------------------- G6 kit files present ------------------
# `ValidationTest.php` is here because of the specification-correction rider
# [andres] 2026-08-21: "ValidationTest.php is added to the set of kit files
# watched by the gate." These are the three tests the kit ships: T-406 forbids
# modifying them, so the gate watches that they still exist.
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
D011=$(grep_count_fixed 'D-011' specs/000-proyecto/DECISIONES.md)
check 'D-011 in DECISIONES.md (>=1)' "$([ "${D011:-0}" -ge 1 ] 2>/dev/null && echo 'yes' || echo 'no')" 'yes'
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
  '.eslintrc.json'
do
  # Prefix anchored at the start: 'tests/' matches 'tests/...' and the directory
  # entry 'tests/'; 'CLAUDE.md' matches the exact file entry.
  HITS=$(printf '%s\n' "$ARCHIVE_LIST" | grep -c "^$(printf '%s' "$excluded" | sed 's/[.[\*^$\/]/\\&/g')" 2>/dev/null || true)
  check "NOT packaged: $excluded" "${HITS:-0}" '0'
  if [ "${HITS:-0}" != "0" ]; then
    printf '%s\n' "$ARCHIVE_LIST" | grep "^$(printf '%s' "$excluded" | sed 's/[.[\*^$\/]/\\&/g')" | sed 's/^/      | LEAKS INTO THE RELEASE: /'
  fi
done

# --- must travel: the product the end user installs ---------------------------
# AGENTS.md is here on purpose (D-015.1): it is product, not process.
for included in \
  AGENTS.md \
  recipe.yml \
  composer.json \
  recommended.yml \
  screenshot.webp \
  LICENSE.txt
do
  FOUND=$(printf '%s\n' "$ARCHIVE_LIST" | grep -cx -F "$included" 2>/dev/null || true)
  check "packaged: $included" "$([ "${FOUND:-0}" -ge 1 ] && echo 'present' || echo 'absent')" 'present'
done

# ----------------------------------------------------------------- summary ---
printf '\n=========================================================================================================\n'
if [ "$M" -eq 0 ]; then
  printf '%s%d checks - %d failures%s\n' "$C_OK" "$N" "$M" "$C_OFF"
else
  printf '%s%d checks - %d failures%s\n' "$C_BAD" "$N" "$M" "$C_OFF"
fi
printf '=========================================================================================================\n'

[ "$M" -eq 0 ] && exit 0
exit 1
