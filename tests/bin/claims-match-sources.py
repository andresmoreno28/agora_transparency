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
                                       (left is not always CLAUDE.md - two of
                                        the offline ten compare a source against
                                        a fact; the note says which two things)
    ONLINE   <name>   OK|MISMATCH  <claimed>  <source>  <what was compared>
                                       the same, but read over the network from
                                       a CI job trace. Only with --online.
    NOT_READ  <name>   <why>              an ONLINE item that was not read. Never
                                         an agreement, never a disagreement.
    UNCHECKED  <name> <why>              nothing here can confirm it, ever
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

THE ONLINE HALF, 2026-09-20, and why it is opt-in.
--------------------------------------------------
Four of the nine quantities this file used to print as unreachable were
reachable all along, and two of the stated reasons were FALSE:

    /api/v4/projects/<id>/jobs/<id>/trace   -> 401, 30 bytes, anonymously
    /project/<name>/-/jobs/<id>/raw         -> 302 -> 200, the WHOLE log

The web route needs no credential at all, so every figure this project's CI
prints is readable by anybody - a marketplace reviewer included. `-L` is
mandatory: without it the 302 is 622 bytes and looks exactly like a failure.

That gap cost real staleness twice on 2026-09-20, both caught by a human
reading rather than by a gate: CLAUDE.md stated `OK (20 tests, 2549
assertions)` while the gate printed 21/2555, and the site template's observed
inventory stood NINE DAYS and eight pipelines behind - invisible to the
offline half, because the offline half compares job NAMES and the names had
not changed. A stale observation of an unchanged list is invisible to a check
that compares lists.

So the figures are read. But NOT by default, and the constraint is the reason:
this script is wired into gate-a-wave1.sh precisely because it is offline and
finishes in under a second. A gate that needs the network is a gate somebody
makes permissive within the week. --online is therefore opt-in, and its
absence is a THIRD STATE that is printed by name for every item it covers -
never an agreement. No network, no curl, a non-200, a pipeline still running,
a job id that no longer exists: each prints its own NOT READ line. An absence
of information reading as good news is the exact defect being closed here.

WHICH PIPELINE IS READ, decided rather than defaulted. The one the table
NAMES, never "the newest". CLAUDE.md's own convention is that each table
records a named, complete observation, and the file says in as many words that
"a job list read mid-pipeline is not the gate". Reading the newest would
compare today's prose against something nobody observed, and would go green or
red for reasons that have nothing to do with whether the prose is stale. The
tip's pipeline is tests/bin/watch-gate's subject and is named in NOT CHECKED.
"""

import glob
import io
import os
import re
import subprocess
import sys
import tempfile

# Forward slashes, not os.path.join: these strings are PRINTED, and a path that
# renders as tests\bin\watch-gate on Windows and tests/bin/watch-gate on the
# runner makes the same failure look like two different ones. Python opens either
# form on either platform.
CLAUDE = "CLAUDE.md"
WAVE1 = "tests/bin/gate-a-wave1.sh"
WAVE3 = "tests/bin/gate-a-wave3.sh"
WATCH = "tests/bin/watch-gate"

HOST = "https://git.drupalcode.org"

# THE FIVE NON-CANONICAL `jobs >= N` MENTIONS IN CLAUDE.md, frozen (T-0611).
#
# This looks like the hand-maintained second copy this whole script exists to
# refuse, and the difference is worth one paragraph rather than being left to
# look like an exception.
#
# A figure goes stale because the thing it describes MOVES. These five describe
# things that cannot: a verbatim blockquote of D-023(5) as first written (7),
# the struck-through amendment immediately before the canonical marker (9), and
# the three values quoted in the sentence after it out of D-020's amendment
# (7, 8, 9). Rule 8 makes all five immutable - a signed record is amended or
# superseded, never edited - so a change to any of them is a rule-8 violation
# and is exactly what this list is here to catch. The live floor, the one that
# does move, is NOT in here: it is derived from `len()` of a declared job list.
#
# Together they close the one hole the old `max()` reading left open: six
# numbers lowered TOGETHER used to pass, because the maximum of the lowered set
# is what the prose then said.
FROZEN_FLOOR_MENTIONS = [7, 7, 8, 9, 9]

# The two observed inventories, each with the anchors its four extractors use.
# `api` is the URL-encoded project path the API wants; `web` is the plain path
# the raw-log route wants. They differ, and passing one where the other belongs
# is the first mistake anybody makes here.
OBSERVED = (
    ("template", "the site template",
     "**Observed inventory — the site template.**",
     "**Trace figures — the site template.**",
     "project%2Fagora_transparency", "project/agora_transparency"),
    ("theme", "the theme",
     "**Observed inventory — the theme.**",
     "**Trace figures — the theme.**",
     "project%2Fagora_theme", "project/agora_theme"),
)

# A pipeline in any of these states has finished deciding. Anything else - and
# `canceling` especially, which watch-gate learned the hard way is not terminal -
# is NOT READ rather than read, because a job list read mid-pipeline is not the
# gate and a half-finished list would disagree with a true table.
TERMINAL = ("success", "failed", "canceled", "skipped", "manual")

# Quantities CLAUDE.md states that NOTHING can confirm from here, with or
# without the network. Printed on every run, by name. A guard that silently
# covers half its subject is the failure `preflight` was built for: the
# uncovered half has to be as visible as the covered one or the green reads as
# total coverage.
#
# FIVE ENTRIES LEFT THIS LIST ON 2026-09-20 and they did not leave because the
# list was tidied. Two of them carried reasons that were false - the `401` one
# and the "needs Chrome to reproduce" one, which confused REPRODUCING a figure
# with READING it - and had carried them for weeks. The lesson is in the list's
# own shape now: every reason below says what specifically is out of reach, not
# merely that something is.
UNCHECKED = [
    ("spellcheck's LOCAL denominators (files offered to cspell, files it opened)",
     "running cspell here needs the network for core's dictionaries and pnpm; "
     "the CI job's own `Files checked` line IS read, with --online, from its trace"),
    ("phpcs, phpstan, eslint and stylelint findings and denominators",
     "need a container rig with the project's PHP and JS toolchains; "
     "tests/bin/preflight runs them and needs Docker"),
    # THIS ENTRY REPLACES A REASON THAT STOPPED BEING TRUE, rather than being
    # deleted with the gap it named. The old one read "the floor is read as the
    # maximum of six mentions"; T-0611 made the floor derived, so that sentence
    # would now be a false reason sitting in a list whose whole value is that
    # its reasons are true. What is left uncovered is narrower and is stated as
    # what it is.
    ("whether the five frozen `jobs >= N` mentions are still in struck or "
     "quoted CONTEXTS",
     "their VALUES are asserted as a multiset, so none can be edited without "
     "failing the gate - but nothing here reads the markdown around them, so a "
     "frozen record moved out of its blockquote keeps its value and loses its "
     "framing. Rule 8 is what governs that, and rule 8 is read by people"),
    ("the GitHub mirror's conclusion, and how long it has been red",
     "network; tests/bin/watch-gate reads it live beside the gate and prints "
     "the streak, the last success and how many runs it examined"),
    ("whether an observation names the NEWEST pipeline, i.e. whether the row is "
     "stale rather than merely self-consistent",
     "deliberate, and it is the one entry here that names a gap nobody intends "
     "to close. A check demanding the newest pipeline would go red every time "
     "the OTHER repository is pushed to - a red no commit here can fix, which "
     "is the unfixable-red D-023(5) refuses. CLAUDE.md's own rule is that a "
     "table records a NAMED, COMPLETE observation, and 'newest' is not even "
     "defined while a pipeline is running. What IS bound as of T-0622: the "
     "prose id against the id in the API URL beside it, offline on every push, "
     "so a row now has to be refreshed in two places or fail. And "
     "tests/bin/watch-gate reads HEAD's pipeline live"),
    ("figures restated in the prose AROUND each trace-figures table",
     "only the table is compared. A value repeated in the narrative beside it - "
     "historical, struck through or simply said twice - is not, and the remedy "
     "is to stop making the second copy rather than to refresh both"),
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
                                                    ", ".join(str(f) for f in found))))
        return None
    return found[0]


def table_after(text, anchor, name, records, cells, window=None):
    """The rows of the first markdown table following *anchor*.

    Returns a list of tuples of the first *cells* columns, stripped of the
    backticks CLAUDE.md wraps identifiers in. The header row's first cell is
    `job`, with no backticks around it, so it is skipped without needing to be
    recognised; the separator row likewise. Collection stops at the first
    non-table line after at least one row, so a later table in the file can
    never be absorbed into this one once collection has begun.

    WINDOW IS NOT OPTIONAL FOR THE TRACE TABLES, and the reason was found by
    falsifying rather than by reasoning. Deleting every row of both trace-figures
    tables - the exact I-028 case the wave-1 gate check exists to catch - did NOT
    produce "0 rows". With nothing to collect under its own label, the scan ran
    on and collected the NEXT table in the file, so the figure count went UP,
    from 11 to 17, and the guard that was supposed to fire read `yes`. The run
    failed anyway, on two mismatches against nonsense values, but it failed for
    the wrong reason and the denominator check was useless.

    So a table that is placed directly under its own label declares how far it
    may be looked for. The job tables pass window=None because prose genuinely
    separates them from their anchor - 27 lines in one case and 67 in the other -
    and shortening that is a different change.
    """
    where = text.find(anchor)
    if where < 0:
        records.append(("FATAL",
                        "%s: the anchor %r is gone from CLAUDE.md - the table "
                        "cannot be located" % (name, anchor)))
        return None
    rows = []
    for offset, line in enumerate(text[where:].splitlines()[1:]):
        stripped = line.strip()
        if not stripped.startswith("|"):
            if rows:
                break
            if window is not None and offset >= window:
                break
            continue
        if not stripped.startswith("| `"):
            continue
        columns = [c.strip() for c in stripped.strip("|").split("|")]
        if len(columns) < cells:
            continue
        rows.append(tuple(c.strip("`") for c in columns[:cells]))
    if not rows:
        records.append(("FATAL",
                        "%s: the table after %r yielded 0 rows%s - an empty "
                        "table is a parse failure, not an empty gate (I-028)"
                        % (name, anchor,
                           "" if window is None
                           else " within %d lines of its label" % window)))
        return None
    return rows


def observation_header(text, anchor, name, records):
    """The pipeline id, ref and commit the observation bullet names.

    They are read from the 400 characters after the anchor rather than from the
    whole file, because every one of those three values appears many times in
    CLAUDE.md's narrative: the point is the one the TABLE is about.
    """
    where = text.find(anchor)
    if where < 0:
        records.append(("FATAL",
                        "%s: the anchor %r is gone from CLAUDE.md - the "
                        "pipeline the table is about cannot be located"
                        % (name, anchor)))
        return None
    window = text[where:where + 400]
    found = re.findall(
        r"Pipeline `(\d+)`, ref `([^`]+)`, commit `([0-9a-f]+)`", window)
    if len(found) != 1:
        records.append(("FATAL",
                        "%s: %d matches for the `Pipeline <id>, ref <r>, commit "
                        "<c>` opening in the 400 characters after %r, expected "
                        "exactly 1 - the observation no longer says which "
                        "pipeline it observed" % (name, len(found), anchor)))
        return None
    return found[0]


def observation_url(text, anchor, api, name, records):
    """The pipeline id inside the API URL the observation bullet prints.

    ⚠️ WHY THIS IS A COMPARISON AND NOT A SECOND READING OF ONE FACT. Each
    observation bullet states its pipeline TWICE - once in prose ("Pipeline
    `970030`, ref `1.x`, commit `a3037ae`") and once inside the re-runnable API
    URL printed beside it. Until T-0622 nothing compared them, so the two copies
    could disagree, and the failure mode is worse than a stale number: a reader
    who wants to check the table follows the URL, and a URL pointing at a
    DIFFERENT pipeline from the one the prose names answers a question nobody
    asked, confidently, with a job list that will usually look right.

    THE PROJECT IS PART OF THE PATTERN, not just the id. The two repositories in
    this project differ by a hyphen and a word, and a URL for the wrong project
    carrying the right id would otherwise compare equal. So the encoded project
    path is spliced into the pattern and a URL for the other repository simply
    does not match, which surfaces as "no API URL" rather than as a silent pass.

    WHAT THIS DELIBERATELY DOES NOT DO is assert that the named pipeline is the
    NEWEST one on the branch. See the note beside the comparison itself.
    """
    where = text.find(anchor)
    if where < 0:
        records.append(("FATAL",
                        "%s: the anchor %r is gone from CLAUDE.md - the API URL "
                        "beside the table cannot be located" % (name, anchor)))
        return None
    window = text[where:where + 400]
    found = re.findall(
        r"/api/v4/projects/%s/pipelines/(\d+)/jobs" % re.escape(api), window)
    if len(found) != 1:
        records.append(("FATAL",
                        "%s: %d API URLs of the shape "
                        "`/api/v4/projects/%s/pipelines/<id>/jobs` in the 400 "
                        "characters after %r, expected exactly 1. The table has "
                        "stopped carrying a re-runnable address for the "
                        "observation it records, so the pipeline id it names is "
                        "bound to nothing" % (name, len(found), api, anchor)))
        return None
    return found[0]


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


def mirror_declared(text, records):
    """The workflow filenames tests/bin/watch-gate says it reads."""
    match = re.search(r"^MIRROR_WORKFLOWS='([^']*)'", text, re.M)
    if not match:
        records.append(("FATAL",
                        "MIRROR_WORKFLOWS is not assigned in %s in the "
                        "single-quoted shape this extractor reads - the tool "
                        "that reads the mirror has stopped saying what it reads"
                        % WATCH))
        return None
    names = sorted(set(match.group(1).split()))
    if not names:
        records.append(("FATAL",
                        "MIRROR_WORKFLOWS is assigned an EMPTY list in %s - "
                        "every workflow on the mirror would then be undeclared "
                        "and none would be named (I-028)" % WATCH))
        return None
    return names


def mirror_on_disk(records):
    """The workflow files the mirror actually carries, from the working tree.

    THE ONLY OFFLINE EXTRACTOR HERE THAT READS A DIRECTORY, and the reason is
    dated. D-009(d) and T-804 put visual regression on the GitHub mirror, so a
    SECOND workflow is expected to appear. A workflow whose conclusion nothing
    reads is exactly the defect closed on 2026-09-19 - nine consecutive reds
    over three weeks - reproduced one file over. This comparison fires the day
    the file lands rather than the day somebody notices.
    """
    names = sorted(set(
        os.path.basename(path)
        for pattern in (".github/workflows/*.yml", ".github/workflows/*.yaml")
        for path in glob.glob(pattern)))
    if not names:
        records.append(("FATAL",
                        "mirror_workflows: no workflow file under "
                        ".github/workflows/, so this comparison has an empty "
                        "scope and would pass by construction (I-028). If the "
                        "workflow was deliberately deleted - D-020 rider (a) "
                        "sets its expiry at unit 007 - remove MIRROR_WORKFLOWS "
                        "from %s and this comparison with it, deliberately."
                        % WATCH))
        return None
    return names


# --------------------------------------------------------------- the network --
#
# curl is the transport rather than urllib, and that is a decision rather than
# an accident: tests/bin/watch-gate already reaches this same API with curl and
# parses the answer with python3, so following its shape means one proxy story,
# one TLS store and one thing to fix. It also makes "no curl" a state this
# script can NAME, which is one of the third states the design demands.

def have_curl():
    """The curl binary, or None. Exercised, not merely located (I-026)."""
    for candidate in ("curl",):
        try:
            probe = subprocess.Popen([candidate, "--version"],
                                     stdout=subprocess.PIPE,
                                     stderr=subprocess.PIPE)
            probe.communicate()
            if probe.returncode == 0:
                return candidate
        except OSError:
            continue
    return None


def fetch(curl, url, timeout=90):
    """(status, body, why). status is None when nothing was fetched at all.

    -L is not optional. Without it the raw-log route answers 302 with a 622-byte
    body, which parses as a perfectly well-formed nothing and reads exactly like
    a job that printed no figures.
    """
    if curl is None:
        return None, "", "curl is not on PATH, so nothing here can reach %s" % HOST
    handle, path = tempfile.mkstemp(prefix="claims-online.")
    os.close(handle)
    try:
        proc = subprocess.Popen(
            [curl, "-sS", "-L", "--max-time", str(timeout),
             "-o", path, "-w", "%{http_code}", url],
            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        out, err = proc.communicate()
        code = out.decode("ascii", "replace").strip()
        if proc.returncode != 0:
            detail = err.decode("utf-8", "replace").replace("\n", " ").strip()
            return None, "", ("curl exited %d for %s: %s"
                              % (proc.returncode, url, detail[:200] or "(silent)"))
        if not code.isdigit():
            return None, "", ("curl reported no HTTP status for %s (got %r)"
                              % (url, code[:60]))
        with io.open(path, encoding="utf-8", errors="replace") as handle2:
            return int(code), handle2.read(), ""
    finally:
        try:
            os.unlink(path)
        except OSError:
            pass


def fetch_json(curl, url):
    """(object, why). A non-200 or unparsable answer yields (None, why)."""
    import json
    status, body, why = fetch(curl, url)
    if status is None:
        return None, why
    if status != 200:
        return None, ("%s answered HTTP %d (%d bytes), not 200"
                      % (url, status, len(body)))
    try:
        return json.loads(body), ""
    except ValueError as exc:
        return None, "%s answered something that is not JSON: %s" % (url, exc)


def shape_of(figure):
    """The claimed literal, generalised over its digit runs.

    Comparing by shape rather than by presence is what lets a mismatch name BOTH
    values. Searching the trace for the claimed string would answer only "still
    there / not there", and "not there" is the same answer whether the figure
    moved, the job stopped printing it, or the log format changed - three very
    different things. Generalising the digits finds what the trace prints NOW,
    so the report can say `claimed 21/2555, measured 22/2600`.
    """
    escaped = re.escape(figure)
    return re.sub(r"[0-9]+", "[0-9]+", escaped)


def measure(trace, figure):
    """(value, why). The one string in *trace* shaped like *figure*."""
    found = sorted(set(re.findall(shape_of(figure), trace)))
    if not found:
        return None, ("the trace prints nothing shaped like it (searched for "
                      "%r over %d characters)" % (shape_of(figure), len(trace)))
    if len(found) > 1:
        return None, ("the trace prints %d DIFFERENT values of this shape (%s) "
                      "- an ambiguous measurement is not a measurement"
                      % (len(found), "; ".join(found[:4])))
    return found[0], ""


def online(curl, records, state):
    """Read each table's NAMED pipeline and compare the figures it printed.

    Every item this function is responsible for ends as exactly one ONLINE or
    exactly one NOT_READ, never as silence. state['items'] is incremented for
    each, so the wrapper can print `read X of N` and a reader can see at a
    glance how much of the online half actually happened.
    """
    for key, _label, obs_anchor, fig_anchor, api, web in OBSERVED:
        header = state["headers"].get(key)
        rows = state["rows"].get(key)
        figures = state["figures"].get(key) or []

        # Every online item this repository owns, named up front, so that a
        # failure at step one still accounts for all of them by name.
        items = ["%s_pipeline" % key, "%s_job_rows" % key]
        items += ["%s_trace[%s: %s]" % (key, job, fig) for job, fig in figures]
        state["items"] += len(items)

        def blocked(why):
            for item in items:
                records.append(("NOT_READ", item, why))
                state["not_read"] += 1

        if header is None or rows is None:
            blocked("the offline extractors did not yield a pipeline id or a "
                    "job table for %s, so there is nothing to read it against"
                    % key)
            continue

        pipeline_id, claimed_ref, claimed_commit = header
        url = "%s/api/v4/projects/%s/pipelines/%s" % (HOST, api, pipeline_id)
        data, why = fetch_json(curl, url)
        if data is None:
            # A 404 IS AN ANSWER, and it is the one case here that is a
            # disagreement rather than a third state. Everything else - a
            # timeout, a 5xx, a rate limit, no curl - means the measurement
            # could not be taken. A 404 means the server took it and said the
            # pipeline CLAUDE.md names does not exist, which is a claim about
            # this repository being wrong, and a wrong claim must not exit 0
            # merely because it was wrong in a way that also stopped the read.
            if " answered HTTP 404 " in why:
                records.append(("ONLINE", "%s_pipeline" % key, "MISMATCH",
                                "pipeline %s, ref %s, commit %s" % (
                                    pipeline_id, claimed_ref, claimed_commit),
                                "no such pipeline (HTTP 404)",
                                "the observation bullet names a pipeline that "
                                "%s does not have" % HOST))
                state["read"] += 1
                for item in items[1:]:
                    records.append(("NOT_READ", item,
                                    "pipeline %s does not exist, so nothing "
                                    "could be read from it" % pipeline_id))
                    state["not_read"] += 1
                continue
            blocked("pipeline %s of %s: %s" % (pipeline_id, api, why))
            continue

        status = str(data.get("status") or "?")
        if status not in TERMINAL:
            blocked("pipeline %s is `%s` and has not finished deciding. A job "
                    "list read mid-pipeline is not the gate (CLAUDE.md), so "
                    "this is NOT READ and never a pass" % (pipeline_id, status))
            continue

        # 1 - the pipeline is the one the bullet says it is.
        claimed = "pipeline %s, ref %s, commit %s" % (
            pipeline_id, claimed_ref, claimed_commit)
        measured = "pipeline %s, ref %s, commit %s" % (
            data.get("id"), data.get("ref"), str(data.get("sha") or "")[:len(claimed_commit)])
        records.append(("ONLINE", "%s_pipeline" % key,
                        "OK" if claimed == measured else "MISMATCH",
                        claimed, measured,
                        "the observation bullet's own pipeline/ref/commit vs "
                        "that pipeline at %s (status %s)" % (HOST, status)))
        state["read"] += 1

        # 2 - the WHOLE table, stage and status included, not only the names.
        # This is the comparison the nine-day staleness of 2026-09-20 walked
        # past: the names had not changed, so a name-only check saw nothing.
        jobs, why = fetch_json(
            curl, "%s/api/v4/projects/%s/pipelines/%s/jobs?per_page=100"
            % (HOST, api, pipeline_id))
        if jobs is None:
            for item in items[1:]:
                records.append(("NOT_READ", item,
                                "the job list of pipeline %s: %s"
                                % (pipeline_id, why)))
                state["not_read"] += 1
            continue
        if not jobs:
            for item in items[1:]:
                records.append(("NOT_READ", item,
                                "pipeline %s returned an EMPTY job list. "
                                "`jobs: 0` is a failure and not an empty "
                                "result (D-023(5)), and it is certainly not an "
                                "agreement" % pipeline_id))
                state["not_read"] += 1
            continue

        live = sorted((str(job.get("name")), str(job.get("stage")),
                       str(job.get("status")),
                       "true" if job.get("allow_failure") else "false")
                      for job in jobs)
        table = sorted(tuple(row) for row in rows)
        records.append(("ONLINE", "%s_job_rows" % key,
                        "OK" if table == live else "MISMATCH",
                        " / ".join(" ".join(r) for r in table),
                        " / ".join(" ".join(r) for r in live),
                        "CLAUDE.md's %d-row table vs the %d jobs pipeline %s "
                        "really ran - name, stage, status and allow_failure"
                        % (len(table), len(live), pipeline_id)))
        state["read"] += 1

        by_name = {}
        for job in jobs:
            by_name.setdefault(str(job.get("name")), job)

        # 3 - one comparison per claimed figure, read from that job's own log.
        traces = {}
        for job_name, figure in figures:
            item = "%s_trace[%s: %s]" % (key, job_name, figure)
            job = by_name.get(job_name)
            if job is None:
                # A disagreement, not a third state: the answer WAS available.
                records.append(("ONLINE", item, "MISMATCH",
                                "%s printed it" % job_name,
                                "pipeline %s ran no job named %s"
                                % (pipeline_id, job_name),
                                "the trace-figures table names a job that "
                                "pipeline %s did not run" % pipeline_id))
                state["read"] += 1
                continue
            job_id = job.get("id")
            if job_id not in traces:
                trace_url = "%s/%s/-/jobs/%s/raw" % (HOST, web, job_id)
                got, body, why = fetch(curl, trace_url)
                if got is None:
                    traces[job_id] = (None, why)
                elif got != 200:
                    traces[job_id] = (None,
                                      "%s answered HTTP %d (%d bytes). Without "
                                      "-L the 302 is 622 bytes and looks like a "
                                      "log with nothing in it"
                                      % (trace_url, got, len(body)))
                elif not body.strip():
                    traces[job_id] = (None, "%s answered 200 with an EMPTY body"
                                            % trace_url)
                else:
                    traces[job_id] = (body, "")
            trace, why = traces[job_id]
            if trace is None:
                records.append(("NOT_READ", item,
                                "job %s (%s): %s" % (job_id, job_name, why)))
                state["not_read"] += 1
                continue
            value, why = measure(trace, figure)
            if value is None:
                records.append(("ONLINE", item, "MISMATCH", figure,
                                "NOT PRINTED - " + why,
                                "job %s of pipeline %s, %d characters of log"
                                % (job_id, pipeline_id, len(trace))))
                state["read"] += 1
                continue
            records.append(("ONLINE", item, "OK" if value == figure else "MISMATCH",
                            figure, value,
                            "CLAUDE.md vs the trace of job %s (%s) in pipeline %s"
                            % (job_id, job_name, pipeline_id)))
            state["read"] += 1


def main():
    want_online = "--online" in sys.argv[1:]
    for argument in sys.argv[1:]:
        if argument != "--online":
            sys.stderr.write("unknown argument: %s\n" % argument)
            return 2

    records = []
    claims = {}
    sources = {}
    state = {"items": 0, "read": 0, "not_read": 0,
             "headers": {}, "rows": {}, "figures": {}}

    claude = read(CLAUDE, records)
    wave1 = read(WAVE1, records)
    wave3 = read(WAVE3, records)
    watch = read(WATCH, records)
    if claude is None or wave1 is None or wave3 is None or watch is None:
        return emit(records, 0, 0, 0, state, want_online)

    # ------------------------------------------------- claims, from CLAUDE.md --
    claims["wave1_checks"] = one(
        claude, r"`gate-a-wave1\.sh`\s*\(\*{0,2}(\d+)\*{0,2}\s*checks",
        "wave1_checks", records)
    claims["wave3_checks"] = one(
        claude, r"`gate-a-wave3\.sh`\s*\(\*{0,2}(\d+)\*{0,2}\s*checks",
        "wave3_checks", records)
    claims["invariants"] = one(
        claude, r"\*\*(\d+)\*\*\s*invariants in total", "invariants", records)

    # THE FLOOR IS DERIVED, NOT MAXIMISED (T-0611, 2026-09-21).
    #
    # It used to be read as `max()` of all six `jobs >= N` mentions in CLAUDE.md:
    # D-023(5) quoted verbatim at its original 7, two amendments struck through
    # at 8 and 9, and the operative value the largest. That is right for every
    # direction the gate has ever moved except one, and the exception is not
    # exotic - SIX NUMBERS LOWERED TOGETHER PASS, because the maximum of the
    # lowered set is exactly what the prose then says. The old code named that
    # gap in its own NOT CHECKED list, which is the honest thing to do with a
    # hole and is not the same as closing it.
    #
    # What replaces it has two halves, and neither works alone:
    #
    #   1. ONE canonical mention, marked in the prose so it can be found by
    #      shape rather than by size, compared against `len()` of the shorter
    #      declared job list. Lower it and it stops matching the lists; shorten
    #      a list and the derived side moves out from under it. There is no
    #      longer any value a human can type here that agrees with a job list it
    #      does not describe.
    #   2. The other five are FROZEN RECORDS under rule 8 - a verbatim
    #      blockquote, a struck-through amendment and three values quoted inside
    #      a sentence about what the amendment said. Their multiset is asserted,
    #      so "lower all six" now fails on the five as well as on the one, and
    #      an edit to a frozen record is caught as the rule-8 violation it is.
    #
    # The canonical mention is found by its marker and not by its position: a
    # pattern anchored on "the largest" or "the last" would start agreeing with
    # whatever somebody appends next.
    canonical_re = r"\*\*THE FLOOR, canonical and stated once: `jobs >= (\d+)`\*\*"
    claims["floor"] = one(claude, canonical_re, "floor", records)

    mentions = list(re.finditer(r"`jobs\s*>=\s*(\d+)`", claude))
    if not mentions:
        records.append(("FATAL",
                        "floor: no `jobs >= N` mention in CLAUDE.md at all - "
                        "the gate's own minimum is unstated"))
    else:
        canonical = re.search(canonical_re, claude)
        rest = []
        for mention in mentions:
            if canonical is not None and \
                    canonical.start() <= mention.start() < canonical.end():
                continue
            rest.append(int(mention.group(1)))
        claims["floor_history"] = ", ".join(str(n) for n in sorted(rest))
        sources["floor_history"] = ", ".join(
            str(n) for n in sorted(FROZEN_FLOOR_MENTIONS))

    # Each observation bullet yields three things: the pipeline it names, the
    # job table under it, and the trace-figures table under that.
    figure_total = 0
    for key, label, obs_anchor, fig_anchor, _api, _web in OBSERVED:
        header = observation_header(claude, obs_anchor,
                                    "%s_pipeline" % key, records)
        state["headers"][key] = header
        if header is not None:
            claims["%s_pipeline" % key] = "%s @ %s %s" % header
            claims["%s_pipeline_id" % key] = header[0]
        sources["%s_pipeline_id" % key] = observation_url(
            claude, obs_anchor, _api, "%s_pipeline_url" % key, records)

        rows = table_after(claude, obs_anchor, "%s_jobs" % key, records, 4)
        state["rows"][key] = rows
        if rows is not None:
            claims["%s_jobs" % key] = sorted(row[0] for row in rows)

        figures = table_after(claude, fig_anchor, "%s_figures" % key,
                              records, 2, window=20)
        if figures is not None:
            state["figures"][key] = figures
            figure_total += len(figures)
            claims["%s_figures" % key] = sorted(
                "%s: %s" % (job, fig) for job, fig in figures)
            # THE TRACE TABLE MAY NOT NAME A JOB THE OBSERVED TABLE DOES NOT.
            # Offline, prose against prose, and worth the row: a figure
            # attributed to a job that does not exist is unreadable online and
            # would otherwise surface only as a network-only failure, on the one
            # run in a hundred that passes --online. `<key>_job_rows` asks the
            # same question of the live pipeline, and only with --online.
            #
            # A subset test written as an equality, deliberately: the claimed
            # side is the jobs the figures name, the source side is those of
            # them the job table also knows. They are equal exactly when the
            # first is a subset of the second, and a mismatch then PRINTS both
            # lists, which `all(x in y)` could not.
            if rows is not None:
                named = set(job for job, _fig in figures)
                claims["%s_trace_jobs" % key] = sorted(named)
                sources["%s_trace_jobs" % key] = sorted(
                    named & set(row[0] for row in rows))

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

    # THE ONE STRUCTURAL SOURCE ON THIS PAGE, and the only offline comparison
    # here that reads a fact rather than a second piece of prose. Every invariant
    # in the wave-3 runner opens a `group 'GN - ...'`; G0 is the preflight and is
    # not an invariant, so the count starts at G1. If a group is added and the
    # marker is not updated, this disagrees with it - which is the direction
    # prose-only checking cannot see.
    groups = re.findall(r"^group\s+'G([1-9]\d*)\s*-", wave3, re.M)
    if not groups:
        records.append(("FATAL",
                        "structural_invariants: no `group 'GN - ...'` lines in "
                        "%s - the runner's shape changed and this extractor "
                        "read nothing" % WAVE3))
    else:
        sources["structural_invariants"] = str(len(set(groups)))

    sources["template_watch_jobs"] = expected_list(
        watch, "EXPECTED_agora_transparency", records)
    sources["theme_watch_jobs"] = expected_list(watch, "EXPECTED_agora_theme",
                                                records)

    sources["mirror_declared"] = mirror_declared(watch, records)
    sources["mirror_disk"] = mirror_on_disk(records)

    if sources.get("template_watch_jobs") and sources.get("theme_watch_jobs"):
        sources["floor"] = str(min(len(sources["template_watch_jobs"]),
                                   len(sources["theme_watch_jobs"])))

    # AN EMPTY FIGURES TABLE IS A FATAL, not "no figures to check". Deleting the
    # two tables would leave --online with nothing to compare and this script
    # printing `mismatches: 0` for a subject it had stopped having (I-028).
    if figure_total == 0:
        records.append(("FATAL",
                        "trace figures: 0 rows across both tables in CLAUDE.md. "
                        "The online half would then compare nothing and pass by "
                        "construction. Restore the tables, or remove --online "
                        "and this extractor together, deliberately."))

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
                        sources.get("template_watch_jobs"),
                        "CLAUDE.md's table vs EXPECTED_agora_transparency in "
                        + WATCH)
    compared += compare("theme_jobs", claims.get("theme_jobs"),
                        sources.get("theme_watch_jobs"),
                        "CLAUDE.md's table vs EXPECTED_agora_theme in " + WATCH)
    compared += compare("floor", claims.get("floor"), sources.get("floor"),
                        "the ONE canonical `jobs >= N` in CLAUDE.md vs len() of "
                        "the shorter of the two declared job lists - derived, "
                        "not maximised (T-0611)")
    compared += compare("floor_history", claims.get("floor_history"),
                        sources.get("floor_history"),
                        "the five NON-canonical `jobs >= N` mentions vs the "
                        "frozen multiset they are - all five are rule-8 records "
                        "of what the floor used to be, so any change to one is "
                        "an edit to a signed record and not a measurement")
    compared += compare("mirror_workflows", sources.get("mirror_disk"),
                        sources.get("mirror_declared"),
                        "the workflow files under .github/workflows/ vs "
                        "MIRROR_WORKFLOWS in " + WATCH + " - a directory "
                        "against a declaration, not prose against prose")
    # THE PIPELINE ID, BOUND TO THE URL THAT QUOTES IT. Offline, on every push.
    #
    # ⚠️ THE GAP THIS CLOSES, AND THE ONE IT DOES NOT. CLAUDE.md's theme row went
    # stale FOUR TIMES in twenty-four hours, and its own text named why nothing
    # caught it: `theme_jobs` compares job NAMES, which do not change when a
    # pipeline does, and `packaged-claims` cannot reach CLAUDE.md because that
    # file is `export-ignore`d. So the id was stated twice and checked against
    # nothing. This makes the second copy load-bearing instead of decorative: the
    # URL a reviewer would follow now has to address the pipeline the prose
    # claims to have read.
    #
    # ⚠️ IT IS DELIBERATELY NOT AN ASSERTION THAT THE NAMED PIPELINE IS THE
    # NEWEST ONE, and refusing that was the whole decision. CLAUDE.md's own rule
    # is that a table records a NAMED, COMPLETE observation and that "a job list
    # read mid-pipeline is not the gate" - so "newest" is not even well defined
    # while a pipeline is running. Worse, a check demanding it would go red every
    # time somebody pushes to the OTHER repository: a red no commit here can fix,
    # which is precisely the unfixable-red D-023(5) refuses and the reason
    # tests/bin/ported-drift reports deltas rather than failing on them.
    #
    # What remains uncovered is therefore STALENESS, and it stays named in the
    # NOT CHECKED list rather than being quietly implied away. Three things now
    # bear on it and none of them is this check: `--online` re-reads the named
    # pipeline's ref, commit and whole job table, so a row refreshed carelessly
    # fails; tests/bin/watch-gate reads HEAD's pipeline live; and a stale row now
    # has to be stale in TWO places at once to pass, because refreshing the prose
    # and forgetting the URL is exactly the half-edit this comparison catches.
    for key, label, _obs, _fig, _api, _web in OBSERVED:
        compared += compare(
            "%s_pipeline_id" % key,
            claims.get("%s_pipeline_id" % key),
            sources.get("%s_pipeline_id" % key),
            "the pipeline id %s's observation states in prose vs the id inside "
            "the re-runnable API URL printed beside it - one fact written "
            "twice, and now bound. It is NOT asserted to be the newest "
            "pipeline: see the NOT CHECKED entry" % label)
        compared += compare(
            "%s_trace_jobs" % key,
            claims.get("%s_trace_jobs" % key),
            sources.get("%s_trace_jobs" % key),
            "the jobs %s's trace-figures table names vs those of them its "
            "observed job table also knows - offline, and it keeps a figure "
            "from being attributed to a job that does not run" % label)

    if want_online:
        online(have_curl(), records, state)

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

    return emit(records, extracted, compared, figure_total, state, want_online)


def emit(records, extracted, compared, figures, state, want_online):
    # STDOUT IS FORCED TO UTF-8, and it is a correctness fix rather than a
    # cosmetic one. The theme's gate prints `35 checks — 0 failures` with an em
    # dash; on a Windows console Python defaults to cp1252, where that character
    # survives by luck. The next figure containing one that does NOT would raise
    # UnicodeEncodeError halfway through the records, and a reader that died
    # mid-print is a reader whose counts the wrapper cannot find - which the
    # wrapper reports as an unreadable summary, correctly, but only after the
    # failure has already been made to look like a tooling fault.
    try:
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")
    except (AttributeError, ValueError):
        pass

    fatal_count = sum(1 for r in records if r[0] == "FATAL")
    mismatches = sum(1 for r in records
                     if r[0] in ("CMP", "ONLINE") and r[2] == "MISMATCH")
    records.append(("COUNT", "claims_extracted", str(extracted)))
    records.append(("COUNT", "comparisons", str(compared)))
    records.append(("COUNT", "trace_figures", str(figures)))
    records.append(("COUNT", "online_mode", "1" if want_online else "0"))
    records.append(("COUNT", "online_items", str(state["items"])))
    records.append(("COUNT", "online_read", str(state["read"])))
    records.append(("COUNT", "online_not_read", str(state["not_read"])))
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
