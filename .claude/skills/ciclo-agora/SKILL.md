---
name: ciclo-agora
description: Use when starting or resuming work on the Ágora project, when a dispatch or prompt asks to plan, implement or close a wave, when deciding whether something needs the human's signature, or when writing the end-of-turn report.
---

# Ágora's working dynamic

## Core principle

**Disk wins over any prompt.** Before implementing anything, a reconciliation pass is run: if the
prompt assumes something the disk contradicts, **stop and report** — do not force it to fit.

## Roles — do not mix

| Who | Does | Does not do |
|---|---|---|
| **Andrés (human)** | Decides the load-bearing calls, signs gates B, merges to the canonical branch, tags, releases, publication on Drupal.org | Write long prompts |
| **Main session** | Coordinates, keeps context, invokes subagents, executes their plans, escalates | Implement by hand what belongs to a subagent; plan or close without going through `orquestador` |
| **`orquestador`** | Scaffolding, lane plan, read-only audits, gate verdict | Implement; invoke other subagents |
| **`desarrollador`** | Implements against the signed plan | Close its own task |
| **`tester`** | Tests, smokes, invariants, with real counts | Weaken a test to make it pass |

**Platform limitation:** subagents **cannot invoke subagents**. The `orquestador` returns orders;
the main session executes them with `desarrollador` and `tester`.

## Reconciliation pass — always, up front

1. Read `CLAUDE.md`, `specs/000-project/plan.md`, `DECISIONS.md`, `IDIOMS.md`.
2. Read `plan.md` + `tasks.md` of the active unit (the highest-numbered one with unsigned tasks).
3. Verify **on disk**: branch, clean/dirty tree, last tag, last signed task `[✓]`,
   **next free D-NNN**.
4. Report divergences between what the prompt assumes and what is there. Divergences are healthy.

## The cycle of a wave

```
reconciliation → orquestador plans lanes → desarrollador + tester in parallel
   → gate A (real counts) → orquestador audits and gives a verdict → gate B (human signature)
```

- Parallel lanes **only** if they touch disjoint files. Anything sharing an area goes sequential.
- The `tester` starts in parallel with the code: its initial failures are expected — say so.
- Without an explicit ✓ from the `orquestador`, **the wave does not close**.

## What needs the human's signature

- Any load-bearing architectural decision → **options + recommendation ★**, he decides
- Gate B of every wave
- Merge to the canonical branch, tags, releases
- Creation of the project on Drupal.org

Never close one of these on your own. Prepare the decision, do not take it.

## Append-only

- Signed tasks in `tasks.md` **are not renumbered**
- Signed decisions **are not edited**: they are amended or a new one is opened
- `D-NNN` numbers are verified **on disk** before proposing the next one

## Conventions that get broken by accident

| Rule | Detail |
|---|---|
| Languages | The **entire repository is in English** (D-017), process layer included. Spanish is the language of orchestration outside the repo. Demo content bilingual ES/EN |
| Commits | Conventional, in English, **no AI co-authorship trailers** |
| Labels | `[ejecutor]`, `[andres]` — never AI tool names |
| Tooling | Composer for PHP, **pnpm exclusively** for JS (no npm, no yarn, not in docs or CI either) |
| Research | Every claim about the state of the art carries a **date and source** and is re-verified (I-001) |

## End-of-turn report format

1. **Reconciliation** — what the prompt assumed vs what is on disk
2. **Done / not done** — with real counts, not exit codes
3. **Escalations** 🔴/🟡/🟢 — each one with options + recommendation
4. **HOLD** — which signature is needed before moving on

## Red flags — STOP

- You are about to implement without having read the disk
- You are about to close an architectural decision yourself
- You are about to renumber or edit something already signed
- You are about to report green without numbers
- The prompt assumes something and the disk says otherwise, and you are about to carry on anyway
