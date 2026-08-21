---
name: gate-a-verde
description: Use when about to declare a wave, task or gate complete, when a test suite or invariant script fails and the fix is tempting to skip, when reporting test results, or when considering marking something done based on an exit code alone.
---

# Closing a gate genuinely green

## Core principle

**Green means real counts, not exit codes.** An `exit 0` with no numbers proves nothing: a suite
that found no tests returns 0, an axe run that did not load the page returns 0, a grep with no files
returns 0. If you cannot say *how many*, you have not verified it.

## What gets reported

| Layer | Required count |
|---|---|
| PHPUnit | number of tests **and** of assertions |
| Install smoke | routes checked and what was seen on each one |
| Playwright functional | number of specs passed |
| Playwright visual | number of screenshots **compared** (not just generated) |
| axe | number of pages analysed and number of violations (must be 0) |
| `tests/bin/` invariants | number of files scanned and number of findings |
| Linters | number of files analysed |

A count of **0 tests executed** is a failure, not a success.

## Before saying "done"

1. Run the command. Do not infer it.
2. Read the whole output, not the last line.
3. Check that the number of tests executed is **greater than zero** and consistent with what you wrote.
4. If something fails, fix it or **escalate it**. Do not silence it.
5. Report the exact command and its real output.

## Forbidden in order to turn a gate green

- Weakening an assertion or marking a test as `skip`/`incomplete`
- Silencing, excluding or "temporarily" disabling a `tests/bin/` invariant
- Excluding an axe rule or a route from the scan
- Adding the problematic file to the linter's ignore list
- Lowering the visual comparison threshold until it passes

**All of this is escalated to the human. None of these actions closes a gate.**

## Rationalisations and reality

| Excuse | Reality |
|---|---|
| "The test is fragile, not the code" | It may be — but the human decides that, not you while closing |
| "It's a false positive from the invariant" | If the report contradicts the script, **the script wins**: re-run it |
| "It passes locally, CI is being odd" | The drupalcode pipeline IS the gate, not an approximation |
| "I'll leave it skipped and open a task" | A skip without the human's signature is a fake gate |
| "Exit 0, therefore it's fine" | How many tests ran? If you do not know, it is not fine |
| "Only a minor detail is missing" | Nothing broken moves forward (non-negotiable #9) |
| "I already verified it" | Verify it now, in the current state of the disk |

## Red flags — STOP and escalate

- You are editing a test, an `.eslintignore`, a `phpcs.xml` or a threshold **while closing a gate**
- You are about to report "all green" without having numbers in front of you
- An invariant is bothering you and you are thinking about its exclusion flag
- You are about to write "it should work" or "in principle it passes"

## After gate A

A green gate A **does not close the wave**. The independent verdict of the `orquestador` (read-only
audit) is still missing and, after that, the human's signature on gate B. Without those two things,
the wave stays open.
