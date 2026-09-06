#!/usr/bin/env python3
"""Extractor and comparator behind tests/bin/claims-match-sources.

Standard library only, for the reason every other .py in tests/bin/ gives:
this runs in the `agora-invariants` job, whose image is built by composer and
carries no pip packages. A dependency here would be a gate that fails on the
runner and passes on the author's machine.

It prints TAB-separated records on stdout and nothing else, so that the bash
wrapper does the reporting and this file does the reading:

    CLAIM    <name>   <value>            a number or list read out of CLAUDE.md
    SOURCE   <name>   <value>            the same quantity read from the repo
    CMP      <name>   OK|MISMATCH  <claimed>  <source>  <what was compared>
    UNCHECKED  <name> <why>
    COUNT    <name>   <n>
    FATAL    <why>                       extraction failed; nothing was compared

A FATAL is not a finding and is not a pass. A regex that silently matches
nothing is this project's most-catalogued defect shape (I-028), so every
extractor below either yields a value or names itself in a FATAL record.

AMBIGUITY IS ALSO A FATAL, and that is deliberate rather than defensive.
CLAUDE.md warns that "a number written down twice is a number that goes stale
in one place first". Every scalar claim here is therefore required to match
EXACTLY ONCE: a second copy of the wave-1 check count is refused at the gate
instead of being silently preferred over the first. The one exception is the
gate floor, which the file mentions six times on purpose - a verbatim D-023(5)
quote, two struck amendments and the operative value - and whose rule is stated
where it is applied.
"""

import io
import re
import sys

# Forward slashes, not os.path.join: these strings are PRINTED, and a path that
# renders as tests\bin\watch-gate on Windows and tests/bin/watch-gate on the
# runner makes the same failure look like two different ones. Python opens either
# form on either platform.
CLAUDE = "CLAUDE.md"
WAVE1 = "tests/bin/gate-a-wave1.sh"
WAVE3 = "tests/bin/gate-a-wave3.sh"
WATCH = "tests/bin/watch-gate"

# Quantities CLAUDE.md states that NOTHING in this repository can confirm.
# Printed on every run, by name. A guard that silently covers half its subject
# is the failure `preflight` was built for: the uncovered half has to be as
# visible as the covered one or the green reads as total coverage.
UNCHECKED = [
    ("phpunit test and assertion counts (both repositories)",
     "only in a CI job trace; /trace answers 401 anonymously"),
    ("axe pages, rules per page and total assertions (theme nightwatch)",
     "only in a CI job trace; needs Chrome and chromedriver to reproduce"),
    ("job status and allow_failure in both job tables",
     "network; tests/bin/watch-gate reads them live and is the tool for it"),
    ("spellcheck denominators (files offered, files checked)",
     "cspell needs the network for core's dictionaries and pnpm to run"),
    ("Drupal CMS install-smoke figures and the resolved agora_theme version",
     "only in a CI job trace"),
    ("phpcs, phpstan, eslint and stylelint findings and denominators",
     "need a container rig with the project's PHP and JS toolchains"),
    ("gate-a-theme.sh check count",
     "lives in the agora_theme repository; not readable from this tree"),
    ("whether a LOWERED gate floor is still consistent",
     "the floor is read as the maximum of six mentions - see the header"),
]


def read(path, records):
    """Return the text of *path*, or None having logged a FATAL."""
    try:
        with io.open(path, encoding="utf-8") as handle:
            return handle.read()
    except OSError as exc:
        records.append(("FATAL", "cannot read %s: %s" % (path, exc)))
        return None


def one(text, pattern, name, records):
    """Exactly one match, or a FATAL naming which extractor failed and why."""
    found = re.findall(pattern, text)
    if not found:
        records.append(("FATAL",
                        "%s: no match for %r - CLAUDE.md no longer states this "
                        "number in the shape this extractor reads, which is a "
                        "parse failure and not a pass" % (name, pattern)))
        return None
    if len(found) > 1:
        records.append(("FATAL",
                        "%s: %d matches for %r (values %s) - the same number is "
                        "written in more than one place, which is the drift this "
                        "guard exists to refuse" % (name, len(found), pattern,
                                                    ", ".join(found))))
        return None
    return found[0]


def job_table(text, anchor, name, records):
    """The first column, wrapped in backticks, of the table following *anchor*.

    The header row's first cell is `job`, with no backticks around it, so it is
    skipped without needing to be recognised; the separator row likewise. Collection
    stops at the first non-table line after at least one row, so a later table
    in the file can never be absorbed into this one.
    """
    where = text.find(anchor)
    if where < 0:
        records.append(("FATAL",
                        "%s: the anchor %r is gone from CLAUDE.md - the job "
                        "table cannot be located" % (name, anchor)))
        return None
    rows = []
    for line in text[where:].splitlines()[1:]:
        stripped = line.strip()
        if not stripped.startswith("|"):
            if rows:
                break
            continue
        match = re.match(r"^\|\s*`([^`]+)`\s*\|", stripped)
        if match:
            rows.append(match.group(1))
    if not rows:
        records.append(("FATAL",
                        "%s: the table after %r yielded 0 rows - an empty job "
                        "list is a parse failure, not an empty gate"
                        % (name, anchor)))
        return None
    return sorted(rows)


def marker(text, path, name, records):
    """The `# GATE-CLAIM:` line a runner carries, as a dict of key -> int."""
    found = re.findall(r"^#\s*GATE-CLAIM:\s*(.+)$", text, re.M)
    if len(found) != 1:
        records.append(("FATAL",
                        "%s: %s carries %d GATE-CLAIM lines, expected exactly 1"
                        % (name, path, len(found))))
        return None
    fields = dict(re.findall(r"(\w+)=(\d+)", found[0]))
    if not fields:
        records.append(("FATAL",
                        "%s: the GATE-CLAIM line in %s parsed to no key=value "
                        "pairs: %r" % (name, path, found[0])))
        return None
    return {key: int(value) for key, value in fields.items()}


def expected_list(text, variable, records):
    """A watch-gate EXPECTED_* assignment, as a sorted list of job names."""
    match = re.search(r"^%s='([^']*)'" % re.escape(variable), text, re.M)
    if not match:
        records.append(("FATAL",
                        "%s is not assigned in %s in the single-quoted shape "
                        "this extractor reads" % (variable, WATCH)))
        return None
    names = [n.strip() for n in match.group(1).splitlines() if n.strip()]
    if not names:
        records.append(("FATAL",
                        "%s is assigned an EMPTY list in %s - every job-list "
                        "comparison would pass by construction (I-028)"
                        % (variable, WATCH)))
        return None
    return sorted(names)


def main():
    records = []
    claims = {}
    sources = {}

    claude = read(CLAUDE, records)
    wave1 = read(WAVE1, records)
    wave3 = read(WAVE3, records)
    watch = read(WATCH, records)
    if claude is None or wave1 is None or wave3 is None or watch is None:
        return emit(records, 0, 0)

    # ------------------------------------------------- claims, from CLAUDE.md --
    claims["wave1_checks"] = one(
        claude, r"`gate-a-wave1\.sh`\s*\(\*{0,2}(\d+)\*{0,2}\s*checks",
        "wave1_checks", records)
    claims["wave3_checks"] = one(
        claude, r"`gate-a-wave3\.sh`\s*\(\*{0,2}(\d+)\*{0,2}\s*checks",
        "wave3_checks", records)
    claims["invariants"] = one(
        claude, r"\*\*(\d+)\*\*\s*invariants in total", "invariants", records)

    # THE FLOOR IS THE ONE PLURAL CLAIM, and its rule is stated where it is
    # applied rather than left to be inferred. CLAUDE.md mentions `jobs >= N`
    # six times on purpose: D-023(5) is quoted verbatim at its original 7, two
    # amendments are struck through at 8 and 9, and the operative value is the
    # largest. Reading the maximum is therefore right for every direction the
    # gate has ever moved - EXCEPT a deliberate lowering, which a struck-through
    # higher number would mask. That gap is named in UNCHECKED rather than
    # papered over.
    floors = re.findall(r"`jobs\s*>=\s*(\d+)`", claude)
    if not floors:
        records.append(("FATAL",
                        "floor: no `jobs >= N` mention in CLAUDE.md at all - "
                        "the gate's own minimum is unstated"))
        claims["floor"] = None
    else:
        claims["floor"] = str(max(int(f) for f in floors))

    claims["template_jobs"] = job_table(
        claude, "**Observed inventory — the site template.**",
        "template_jobs", records)
    claims["theme_jobs"] = job_table(
        claude, "**Observed inventory — the theme.**", "theme_jobs", records)

    # ------------------------------------------------ sources, from the repo --
    wave1_marker = marker(wave1, WAVE1, "wave1_marker", records)
    wave3_marker = marker(wave3, WAVE3, "wave3_marker", records)

    for who, mark, path in (("wave1", wave1_marker, WAVE1),
                            ("wave3", wave3_marker, WAVE3)):
        if mark is None:
            continue
        for field in ("checks", "invariants"):
            if field not in mark:
                records.append(("FATAL",
                                "%s_marker: no %s= field in the GATE-CLAIM line "
                                "of %s" % (who, field, path)))
        if "checks" in mark:
            sources["%s_checks" % who] = str(mark["checks"])

    # "N invariants in total" in CLAUDE.md is a total ACROSS BOTH RUNNERS, so it
    # is summed here rather than read off whichever runner happens to hold most
    # of them. Until 2026-09-06 that was 0 + 15 and reading wave 3 alone gave the
    # right answer by accident; G9 in wave 1 made the accident visible.
    if (wave1_marker is not None and wave3_marker is not None
            and "invariants" in wave1_marker and "invariants" in wave3_marker):
        sources["wave3_invariants"] = str(wave3_marker["invariants"])
        sources["invariants"] = str(wave1_marker["invariants"]
                                    + wave3_marker["invariants"])

    # THE ONE STRUCTURAL SOURCE ON THIS PAGE, and the only comparison here that
    # reads a fact rather than a second piece of prose. Every invariant in the
    # wave-3 runner opens a `group 'GN - ...'`; G0 is the preflight and is not an
    # invariant, so the count starts at G1. If a group is added and the marker is
    # not updated, this disagrees with it - which is the direction prose-only
    # checking cannot see.
    groups = re.findall(r"^group\s+'G([1-9]\d*)\s*-", wave3, re.M)
    if not groups:
        records.append(("FATAL",
                        "structural_invariants: no `group 'GN - ...'` lines in "
                        "%s - the runner's shape changed and this extractor "
                        "read nothing" % WAVE3))
    else:
        sources["structural_invariants"] = str(len(set(groups)))

    sources["template_jobs"] = expected_list(
        watch, "EXPECTED_agora_transparency", records)
    sources["theme_jobs"] = expected_list(watch, "EXPECTED_agora_theme", records)

    if sources.get("template_jobs") and sources.get("theme_jobs"):
        sources["floor"] = str(min(len(sources["template_jobs"]),
                                   len(sources["theme_jobs"])))

    # ------------------------------------------------------------- compare --
    extracted = sum(1 for value in claims.values() if value is not None)
    compared = 0

    def compare(name, claimed, source, note):
        if claimed is None or source is None:
            return 0
        shown_c = ", ".join(claimed) if isinstance(claimed, list) else claimed
        shown_s = ", ".join(source) if isinstance(source, list) else source
        verdict = "OK" if claimed == source else "MISMATCH"
        records.append(("CMP", name, verdict, shown_c, shown_s, note))
        return 1

    compared += compare("wave1_checks", claims.get("wave1_checks"),
                        sources.get("wave1_checks"),
                        "CLAUDE.md vs the GATE-CLAIM line in " + WAVE1)
    compared += compare("wave3_checks", claims.get("wave3_checks"),
                        sources.get("wave3_checks"),
                        "CLAUDE.md vs the GATE-CLAIM line in " + WAVE3)
    compared += compare("invariants", claims.get("invariants"),
                        sources.get("invariants"),
                        "CLAUDE.md vs the GATE-CLAIM lines of BOTH runners, summed")
    compared += compare("invariants_structural", sources.get("wave3_invariants"),
                        sources.get("structural_invariants"),
                        "the wave-3 GATE-CLAIM line vs that runner's own "
                        "`group 'GN'` lines - source against fact, not prose "
                        "against prose")
    compared += compare("template_jobs", claims.get("template_jobs"),
                        sources.get("template_jobs"),
                        "CLAUDE.md's table vs EXPECTED_agora_transparency in "
                        + WATCH)
    compared += compare("theme_jobs", claims.get("theme_jobs"),
                        sources.get("theme_jobs"),
                        "CLAUDE.md's table vs EXPECTED_agora_theme in " + WATCH)
    compared += compare("floor", claims.get("floor"), sources.get("floor"),
                        "the largest `jobs >= N` in CLAUDE.md vs the shorter of "
                        "the two declared job lists")

    for name, value in sorted(claims.items()):
        if value is not None:
            records.append(("CLAIM", name,
                            ", ".join(value) if isinstance(value, list) else value))
    for name, value in sorted(sources.items()):
        if value is not None:
            records.append(("SOURCE", name,
                            ", ".join(value) if isinstance(value, list) else value))
    for name, why in UNCHECKED:
        records.append(("UNCHECKED", name, why))

    return emit(records, extracted, compared)


def emit(records, extracted, compared):
    fatal_count = sum(1 for r in records if r[0] == "FATAL")
    mismatches = sum(1 for r in records if r[0] == "CMP" and r[2] == "MISMATCH")
    records.append(("COUNT", "claims_extracted", str(extracted)))
    records.append(("COUNT", "comparisons", str(compared)))
    records.append(("COUNT", "unchecked", str(len(UNCHECKED))))
    records.append(("COUNT", "mismatches", str(mismatches)))
    records.append(("COUNT", "fatal_count", str(fatal_count)))
    out = sys.stdout
    for record in records:
        out.write("\t".join(record) + "\n")
    if fatal_count:
        return 2
    return 1 if mismatches else 0


if __name__ == "__main__":
    sys.exit(main())
