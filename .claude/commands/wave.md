Run the next wave of the active unit according to tasks.md:
1. Short reconciliation: re-read tasks.md; verify that the previous wave has gate B signed [✓ date].
   If it does not → STOP and ask for the signature. Append-only: do not renumber anything signed.
2. Invoke the `orquestador` subagent for the wave plan: lanes (parallel/sequential), success
   criterion per task and what the tester must verify.
3. Execute that plan by invoking `desarrollador` and `tester` (in parallel where the files are
   disjoint). Runtime conflict → pause, sequentialize, report.
4. Complete gate A: linters/static + suites + tests/bin/ scripts + smoke if the unit closes.
   Everything exit 0 with real counts. Afterwards, invoke the `orquestador` in audit mode for the
   independent verdict. Without its ✓, the wave does NOT close; its 🔴 are resolved or escalated.
5. Prepare gate B: PRE-VALIDATE every command of the walk (restoring it if it is destructive) and
   deliver copy-paste commands + what must BE SEEN at each step + where the human signs in tasks.md.
6. HOLD: report in CLAUDE.md format. The merge to the canonical branch and the tag belong to the human.
