#!/usr/bin/env bash
#
# gate-a-wave3.sh - Agora - Unit 001, wave 3 gate A verification.
#
# Wave 3 counterpart of tests/bin/gate-a-wave1.sh (T-308). Runs the THIRTEEN
# invariants that exist on disk today - the tasks.md "Gate A wave 3" block
# still loops over only four (no-unstable-deps no-patches no-secrets
# sbom-check); no-code-in-template, no-ci-allow-dev, no-boilerplate,
# no-blind-phpunit, cited-tasks-exist and identity-strings landed later (T-307,
# T-308 area, T-309, T-214, T-223, T-322) and are exercised by no gate runner at
# all. Closing
# wave 3 on the stale four-item loop would close it on a stale gate. This
# script is that missing runner (T-313).
#
# Check count, stated so a silent change is impossible: 14 preflight + 17
# invariant checks = 31 before T-223; G9 (cited-tasks-exist, T-223) adds 2, for
# 33; G10 (identity-strings, T-322) adds 2, for 35; G11 (config-inventory,
# T-601) adds 2, for 37.
#
# UNIT 003 WAVE 9 (2026-08-26) takes it from 37 to 43, and from ELEVEN
# invariants to THIRTEEN. Written out term by term so the total is stated rather
# than inferred:
#   T-906  G3 gains a THIRD check - the binary-metadata sweep's own denominator
#          ("binaries opened"). The sweep lives inside no-secrets rather than in
#          a script of its own (plan.md 6.3: it closes a hole in that invariant's
#          is_text() guard), so it gets a check rather than a group.   37 -> 38
#   T-904  G12 media-licence, 2 checks.                                38 -> 40
#   T-905  G13 no-real-people, 3 checks - exit, scanned, and the
#          deny-list term count, for the reason G7 already gives about
#          no-boilerplate: a degenerate list of zero terms prints
#          "0 findings" and passes by construction (I-028).            40 -> 43
#
# G11 amended the sentence above from TEN invariants to ELEVEN on 2026-08-24.
# It is not a dependency or process invariant like the other ten: it exists
# because T-601 requires the number of config objects to be STATED, and the
# kernel test that was stating it could not - PHPUnit turns any output a test
# emits, STDERR included, into an error (pipeline 934619). The printing moved
# to a job that prints for a living; the asserting stayed in PHP. Where the
# other ten answer "is anything forbidden present?", this one answers "how big
# was the set the tests just passed over?" - which is the question I-045 says a
# green check must always be able to answer.
#
# Contract (mirrors gate-a-wave1.sh on purpose - one house style):
#   - every check prints:  obtained | expected | OK/FAIL
#   - closing line:        "N checks - M failures"
#   - exit 0 ONLY if M = 0
#   - a check that CANNOT be evaluated (missing/broken tool, invariant script
#     absent) is a FAIL, never a skip. I-007: an exit 0 without counts proves
#     nothing.
#   - per invariant, TWO checks: (a) exit code == 0, (b) the invariant's own
#     scanned/scope count is > 0. An exit 0 with "0 files scanned" is a
#     failure, not a success (I-007) - a silently empty scope would let every
#     "0 findings" pass by construction.
#
# I-026 (appended this turn): wave 1's preflight only LOCATES tools with
# `command -v`, which is not enough. On this host `python3` resolves to a
# Microsoft Store stub that satisfies `command -v python3` and then prints
# "no se encontró Python" and exits non-zero at first real use - a preflight
# that only checks presence would wave that stub through and every invariant
# that needs python3 (sbom-check) would fail later with a confusing error
# instead of a clear, loud preflight failure. This script EXERCISES every
# tool the eleven invariants actually call (a real, minimal invocation whose
# output or exit code is checked), not merely locates it.
#
# sbom-check needs the network (queries updates.drupal.org). If the network is
# unavailable, sbom-check's own preflight fails loudly and this gate reports
# it as a FAILING invariant - it never skips it (see the sbom-check group
# below; there is no special-casing here).
#
#
# House rules for tests/bin/ (T-321(c)): `wc -l` never `grep -c` for a compared
# value; numeric guards defaulted ONLY where zero means FAIL; grep rc >= 2 is an
# error, never "no match"; never `-F` with `-i`. The CANONICAL copy of the block,
# with the reasoning and the I-031 asymmetry spelled out, lives in the header of
# tests/bin/no-ci-allow-dev. Read it before adding a counter here.
#
# Usage: tests/bin/gate-a-wave3.sh   (run from anywhere; it cd's to the repo root)

set -u
# No `set -e`: a failing check must be recorded, not abort the run.

# ---------------------------------------------------------------- repo root --
SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
REPO_ROOT=$(CDPATH= cd -- "$SCRIPT_DIR/../.." && pwd)
cd "$REPO_ROOT" || { echo "FATAL: cannot cd to repo root $REPO_ROOT"; exit 1; }

N=0
M=0

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

# check <label> <obtained> <expected>  -- exact string match
check() {
  _label=$1; _got=$2; _exp=$3
  N=$((N + 1))
  if [ "$_got" = "$_exp" ]; then
    _res="${C_OK}OK${C_OFF}"
  else
    _res="${C_BAD}FAIL${C_OFF}"
    M=$((M + 1))
  fi
  printf '  %-46s %-24s | %-24s | %s\n' \
    "$_label" "$(trunc "$_got" 24)" "$(trunc "$_exp" 24)" "$_res"
}

# check_positive <label> <obtained-number>  -- OK iff obtained is a positive
# integer. Used for every "scanned/scope count > 0" check (I-007): the
# obtained NUMBER is printed, not a bare yes/no, so a human can see it.
check_positive() {
  _label=$1; _got=$2
  N=$((N + 1))
  if [ -n "${_got:-}" ] && [ "$_got" -gt 0 ] 2>/dev/null; then
    _res="${C_OK}OK${C_OFF}"
  else
    _res="${C_BAD}FAIL${C_OFF}"
    M=$((M + 1))
  fi
  printf '  %-46s %-24s | %-24s | %s\n' \
    "$_label" "$(trunc "${_got:-<none>}" 24)" "$(trunc '>0' 24)" "$_res"
}

group() {
  printf '\n%s\n' "$1"
  printf '  %-46s %-24s | %-24s | %s\n' "check" "obtained" "expected" "verdict"
  printf '  %s\n' "----------------------------------------------------------------------------------------------------------"
}

note() { printf '  %s%s%s\n' "$C_DIM" "$1" "$C_OFF"; }

printf '=========================================================================================================\n'
printf 'Gate A - Agora unit 001 wave 3 - dependency and process invariants\n'
printf 'repo: %s\n' "$REPO_ROOT"
printf 'branch: %s\n' "$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '<no git>')"
printf 'date: %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')"
printf '=========================================================================================================\n'

# ------------------------------------------------------------- G0 preflight --
# I-026: EXERCISE every tool the eleven invariants actually call, do not merely
# locate it with `command -v`. Each check runs a real, minimal invocation and
# compares its actual output or exit code - a resolvable-but-broken stub
# (like the python3 case above) fails here, loudly, instead of surfacing as a
# confusing failure three groups later.
group 'G0 - Preflight (tools exercised, not merely located - I-026)'
PRE_BEFORE=$M

TMP1=$(mktemp -t gate-a-wave3.XXXXXX 2>&1)
if [ -f "$TMP1" ]; then check 'mktemp (creates a real file)' 'created' 'created'; rm -f "$TMP1"
else check 'mktemp (creates a real file)' "$TMP1" 'created'; fi

check 'jq (jq -n 1)'                "$(jq -n 1 2>&1)"                       '1'
check 'python3 (python3 -c pass, exit)' "$(python3 -c 'pass' >/dev/null 2>&1; echo $?)" '0'
check 'curl (curl --version, exit)'  "$(curl --version >/dev/null 2>&1; echo $?)" '0'
check 'git (rev-parse --is-inside-work-tree)' "$(git rev-parse --is-inside-work-tree 2>&1)" 'true'
check 'find (find . -maxdepth 0, exit)' "$(find . -maxdepth 0 >/dev/null 2>&1; echo $?)" '0'
# T-321(a) EXEMPTION: this `grep -c` is the SUBJECT of the probe, not a counter
# feeding a guard - the whole point is to find out whether this host's grep can
# still count. It is compared as a STRING against '1', so a blank fails loudly
# instead of being stepped over the way a numeric test would be.
check 'grep (printf a | grep -c a)' "$(printf 'a' | grep -c a 2>&1)" '1'
check 'sort (printf b\na | sort)'   "$(printf 'b\na' | sort | tr '\n' ',' 2>&1)" 'a,b,'
check 'wc (printf a\nb | wc -l)'    "$(printf 'a\nb\n' | wc -l | tr -d ' ' 2>&1)" '2'
check 'sed (printf a | sed s/a/b/)' "$(printf 'a' | sed 's/a/b/' 2>&1)"    'b'
check 'tr (printf a | tr a b)'      "$(printf 'a' | tr 'a' 'b' 2>&1)"      'b'
check 'cut (printf a:b | cut -d: -f2)' "$(printf 'a:b' | cut -d: -f2 2>&1)" 'b'
check 'tar (tar --version, exit)'   "$(tar --version >/dev/null 2>&1; echo $?)" '0'
check 'date (date -u +%Y, is 4 digits)' "$(date -u '+%Y' 2>&1 | grep -qE '^[0-9]{4}$' && echo 'yes' || echo 'no')" 'yes'

PREFLIGHT_FAILURES=$((M - PRE_BEFORE))
if [ "$PREFLIGHT_FAILURES" -gt 0 ]; then
  printf '\n%sPREFLIGHT FAILED:%s %d tool(s) present but not working correctly.\n' \
    "$C_BAD" "$C_OFF" "$PREFLIGHT_FAILURES"
  printf 'The gate CANNOT be evaluated. A tool that merely resolves (command -v) is not\n'
  printf 'enough - it must actually run. Fix the broken tool/PATH and re-run.\n'
  printf 'A gate that silently skips checks is worse than not having one.\n\n'
  printf '%d checks - %d failures\n' "$N" "$M"
  exit 1
fi

# --------------------------------------------------------- invariant runner --
# Runs one invariant, captures its combined output and exit code, and returns
# them via the OUT_/RC_ globals below (no subshell: an exit code from a
# command substitution's subshell would not survive to the caller).
run_invariant() { # <path>
  INV_RC=0
  INV_OUT=$("$1" 2>&1) || INV_RC=$?
}

# extract_count <output> <ERE for "label:">  -> last matching integer, or ''.
# The ten scripts do not share one wording for their scope/scanned line
# (see the per-group comments below), so each group passes the exact ERE that
# matches what that script actually prints, read from its own summary line.
#
# T-316 / I-027: `grep` has THREE exit states, not two - 0 = matched, 1 = no
# match, >= 2 = grep itself FAILED. Both greps were piped straight into `tail`,
# so the pipeline's status was `tail`'s and grep's was unreadable; an error and
# an unparseable summary produced the same empty answer. Each grep is now run
# and its status read, and an error returns a sentinel that check_positive()
# prints and FAILS on, instead of an empty string that only says "no number".
extract_count() {
  _m=$(printf '%s\n' "$1" | grep -oE "$2" 2>/dev/null)
  _rc=$?
  [ "$_rc" -ge 2 ] && { printf '<grep exit %s on the summary>' "$_rc"; return; }
  [ "$_rc" -eq 0 ] || return 0
  _n=$(printf '%s\n' "$_m" | tail -1 | grep -oE '[0-9]+' 2>/dev/null)
  _rc=$?
  [ "$_rc" -ge 2 ] && { printf '<grep exit %s on the summary>' "$_rc"; return; }
  [ "$_rc" -eq 0 ] || return 0
  printf '%s\n' "$_n" | tail -1
}

# ---------------------------------------------- G1 - no-unstable-deps (T-301) --
group 'G1 - no-unstable-deps'
INV=tests/bin/no-unstable-deps
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned:    N file(s)"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(scope|scanned|findings):' | tr '\n' ' ')"
  check 'no-unstable-deps (exit)'          "$INV_RC" '0'
  check_positive 'no-unstable-deps (scanned)' "$CNT"
else
  check 'no-unstable-deps present'         "$(trunc "$INV" 24)" 'present'
  check_positive 'no-unstable-deps (scanned)' ''
fi

# --------------------------------------------------------- G2 - no-patches (T-302) --
group 'G2 - no-patches'
INV=tests/bin/no-patches
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned:    N file(s)"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(scope|scanned|findings):' | tr '\n' ' ')"
  check 'no-patches (exit)'                "$INV_RC" '0'
  check_positive 'no-patches (scanned)'    "$CNT"
else
  check 'no-patches present'               "$(trunc "$INV" 24)" 'present'
  check_positive 'no-patches (scanned)'    ''
fi

# --------------------------------------------------------- G3 - no-secrets (T-303) --
group 'G3 - no-secrets'
INV=tests/bin/no-secrets
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned: N files - config/: ... - content/: ... - findings: N"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  # T-906: the binary-metadata sweep's own denominator. It is deliberately NOT
  # called "binaries scanned" in the invariant's summary line - the ERE above
  # takes the LAST `scanned:` match, so a second one would silently redirect
  # this group's denominator from the text scan to the binary sweep and nobody
  # would see it happen.
  BIN_CNT=$(extract_count "$INV_OUT" 'binaries opened:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^scanned:' | tail -1)"
  check 'no-secrets (exit)'                "$INV_RC" '0'
  check_positive 'no-secrets (scanned)'    "$CNT"
  check_positive 'no-secrets (binaries opened)' "$BIN_CNT"
else
  check 'no-secrets present'               "$(trunc "$INV" 24)" 'present'
  check_positive 'no-secrets (scanned)'    ''
  check_positive 'no-secrets (binaries opened)' ''
fi

# --------------------------------------------------------- G4 - sbom-check (T-304/306) --
# Needs the network (queries updates.drupal.org). If it is unavailable,
# sbom-check's OWN preflight fails and it exits non-zero - this gate does not
# skip it or paper over that: it is reported as a failing invariant, exactly
# like any other red result.
group 'G4 - sbom-check (needs network - not skipped if unavailable)'
INV=tests/bin/sbom-check
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned:    N file(s)" (composer.json + DECISIONS.md)
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(scope|scanned|queried|findings):' | tr '\n' ' ')"
  check 'sbom-check (exit)'                "$INV_RC" '0'
  check_positive 'sbom-check (scanned)'    "$CNT"
else
  check 'sbom-check present'               "$(trunc "$INV" 24)" 'present'
  check_positive 'sbom-check (scanned)'    ''
fi

# --------------------------------------------------- G5 - no-code-in-template (T-307) --
group 'G5 - no-code-in-template'
INV=tests/bin/no-code-in-template
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "packaged: N entries - working: N files - *.info.yml: N - ..."
  # (this script never prints the word "scanned"; its scope metric is the
  # packaged-tree entry count from `git archive`, its authoritative scope 1)
  CNT=$(extract_count "$INV_OUT" 'packaged:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^packaged:' | tail -1)"
  check 'no-code-in-template (exit)'       "$INV_RC" '0'
  check_positive 'no-code-in-template (packaged)' "$CNT"
else
  check 'no-code-in-template present'      "$(trunc "$INV" 24)" 'present'
  check_positive 'no-code-in-template (packaged)' ''
fi

# ------------------------------------------------------ G6 - no-ci-allow-dev (T-308ish) --
group 'G6 - no-ci-allow-dev'
INV=tests/bin/no-ci-allow-dev
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned: N files - mentions in scope: N - definitions: N - findings: N"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^scanned:' | tail -1)"
  check 'no-ci-allow-dev (exit)'           "$INV_RC" '0'
  check_positive 'no-ci-allow-dev (scanned)' "$CNT"
else
  check 'no-ci-allow-dev present'          "$(trunc "$INV" 24)" 'present'
  check_positive 'no-ci-allow-dev (scanned)' ''
fi

# ------------------------------------------------------------ G7 - no-boilerplate (T-309) --
group 'G7 - no-boilerplate'
INV=tests/bin/no-boilerplate
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scope: packaged tree (N entries) + their working copies -
  # scanned: N - deny-list terms: N - findings: N"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  # T-318 / I-028: exit code and scanned count were both asserted, but the
  # deny-term count itself never was - a degenerate TERM_COUNT=0 would print
  # "deny-list terms: 0 - findings: 0" and this gate would still pass at
  # 28 checks - 0 failures. Parsed the same way as every other metric here,
  # from the invariant's own summary line, and only asserted positive - never
  # pinned to a specific number, so a legitimate change to the deny-list size
  # elsewhere cannot break this check.
  TERMCNT=$(extract_count "$INV_OUT" 'deny-list terms:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^scope:' | tail -1)"
  check 'no-boilerplate (exit)'            "$INV_RC" '0'
  check_positive 'no-boilerplate (scanned)' "$CNT"
  check_positive 'no-boilerplate (deny terms)' "$TERMCNT"
else
  check 'no-boilerplate present'           "$(trunc "$INV" 24)" 'present'
  check_positive 'no-boilerplate (scanned)' ''
  check_positive 'no-boilerplate (deny terms)' ''
fi

# ------------------------------------------------------- G8 - no-blind-phpunit (T-214) --
group 'G8 - no-blind-phpunit'
INV=tests/bin/no-blind-phpunit
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scope: ... - files scanned: N - phpunit invocations: N -
  # guarded: N - unguarded: N - findings: N"
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E 'files scanned:' | tail -1)"
  check 'no-blind-phpunit (exit)'          "$INV_RC" '0'
  check_positive 'no-blind-phpunit (scanned)' "$CNT"
else
  check 'no-blind-phpunit present'         "$(trunc "$INV" 24)" 'present'
  check_positive 'no-blind-phpunit (scanned)' ''
fi

# ------------------------------------------------------ G9 - cited-tasks-exist (T-223) --
group 'G9 - cited-tasks-exist'
INV=tests/bin/cited-tasks-exist
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "citations: N - distinct cited: N - definitions: N -
  # distinct defined: N - findings: N". Its scope metric is the CITATION count,
  # not a file count: this invariant reads exactly one decision record plus the
  # task lists, so "files scanned" would be a constant that proves nothing,
  # while "citations extracted" is the number that actually goes to zero when
  # the extractor stops matching the record.
  #
  # The invariant's own anti-I-007 canary makes zero citations FATAL and prints
  # NO summary line in that case, so the parse below legitimately yields nothing
  # and check_positive FAILS - which is the intended outcome, not a gap.
  CNT=$(extract_count "$INV_OUT" 'citations:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^citations:' | tail -1)"
  check 'cited-tasks-exist (exit)'         "$INV_RC" '0'
  check_positive 'cited-tasks-exist (citations)' "$CNT"
else
  check 'cited-tasks-exist present'        "$(trunc "$INV" 24)" 'present'
  check_positive 'cited-tasks-exist (citations)' ''
fi

# ---------------------------------------------------- G10 - identity-strings (T-322) --
group 'G10 - identity-strings'
INV=tests/bin/identity-strings
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "identity files checked: N - prose-only declared: N -
  # packaged files naming the product: N - findings: N". Its scope metric is the
  # number of PACKAGED FILES NAMING THE PRODUCT, not the identity-file count:
  # the latter is a constant read straight back out of the script's own
  # declaration and would stay 5 even if the scan reached nothing, while the
  # former is derived by the scan and collapses to zero the moment the matcher
  # or the archive listing breaks.
  #
  # That collapse is FATAL inside the invariant, which prints NO summary line in
  # that case, so the parse below legitimately yields nothing and check_positive
  # FAILS - the intended outcome, not a gap (same shape as G9).
  CNT=$(extract_count "$INV_OUT" 'naming the product:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^identity files checked:' | tail -1)"
  check 'identity-strings (exit)'          "$INV_RC" '0'
  check_positive 'identity-strings (naming the product)' "$CNT"
else
  check 'identity-strings present'         "$(trunc "$INV" 24)" 'present'
  check_positive 'identity-strings (naming the product)' ''
fi

# ---------------------------------------------------- G11 - config-inventory (T-601) --
group 'G11 - config-inventory'
INV=tests/bin/config-inventory
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned: N file(s)", with "config objects: N" beside it.
  # Its scope metric is the FILE count under config/, and the note below puts
  # both denominators in the CI log verbatim - the whole reason this invariant
  # exists (I-045). The object count is not pinned to a number here: T-612
  # through T-615 grow config/ legitimately, and only "> 0" is an invariant.
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(scanned|config objects|nested files|zero-byte objects|findings):' | tr '\n' ' ')"
  check 'config-inventory (exit)'          "$INV_RC" '0'
  check_positive 'config-inventory (scanned)' "$CNT"
else
  check 'config-inventory present'         "$(trunc "$INV" 24)" 'present'
  check_positive 'config-inventory (scanned)' ''
fi

# ---------------------------------------------------- G12 - media-licence (T-904) --
group 'G12 - media-licence'
INV=tests/bin/media-licence
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "content entries: N (working) · M (packaged)" followed by
  # "N binaries (working) · M binaries (packaged) · K manifest rows · ...".
  #
  # Its scope metric is the CONTENT ENUMERATION, not the binary count, and that
  # choice is the whole reason this invariant could land before wave 10 authors
  # any media. content/ holds one file and zero binaries today, so pinning the
  # gate to "binaries > 0" would ship a check that is RED from birth - and a red
  # everybody steps over is worse than no red (I-020). The enumeration is
  # positive today, is asserted by the invariant itself as a FATAL, and cannot
  # be vacuous: a site template with an empty content/ is broken for other
  # reasons. The binary count is printed beside it as a census.
  #
  # The two `content entries (working):` / `(packaged):` lines carry a
  # parenthesis before the colon, so the ERE below matches only the summary
  # line - one match, not three.
  CNT=$(extract_count "$INV_OUT" 'content entries:[[:space:]]*[0-9]+')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(content entries|[0-9]+ binaries)' | tr '\n' ' ')"
  check 'media-licence (exit)'             "$INV_RC" '0'
  check_positive 'media-licence (content entries)' "$CNT"
else
  check 'media-licence present'            "$(trunc "$INV" 24)" 'present'
  check_positive 'media-licence (content entries)' ''
fi

# --------------------------------------------------- G13 - no-real-people (T-905) --
group 'G13 - no-real-people'
INV=tests/bin/no-real-people
if [ -x "$INV" ]; then
  run_invariant "$INV"
  # own summary line: "scanned: N files (A working + B packaged + roster) ·
  # R roster rows · D deny-list terms · S shape patterns · F findings".
  # `tail -1` inside extract_count picks that line rather than the two per-scope
  # `scanned:` lines printed above it.
  CNT=$(extract_count "$INV_OUT" 'scanned:[[:space:]]*[0-9]+')
  # The deny-list term count, asserted for the reason G7 gives for
  # no-boilerplate: a degenerate zero-term list prints "0 findings" and passes
  # by construction (I-028). Only "> 0" is checked, never a specific number -
  # T-1001 legitimately adds the office-holders of whatever real municipality
  # the demo is modelled on, and a pinned count would have to be relaxed then.
  DENY_CNT=$(extract_count "$INV_OUT" '[0-9]+ deny-list terms')
  note "$(printf '%s' "$INV_OUT" | grep -E '^(scanned:|person names found)' | tail -2 | tr '\n' ' ')"
  check 'no-real-people (exit)'            "$INV_RC" '0'
  check_positive 'no-real-people (scanned)' "$CNT"
  check_positive 'no-real-people (deny terms)' "$DENY_CNT"
else
  check 'no-real-people present'           "$(trunc "$INV" 24)" 'present'
  check_positive 'no-real-people (scanned)' ''
  check_positive 'no-real-people (deny terms)' ''
fi

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
