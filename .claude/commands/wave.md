Ejecuta la siguiente wave de la unidad activa según tasks.md:
1. Reconciliation corto: relee tasks.md; verifica que la wave anterior tiene gate B firmado [✓ fecha].
   Si no lo tiene → PARA y pide la firma. Append-only: no renumeres nada firmado.
2. Invoca al subagente `orquestador` para el plan de la wave: lanes (paralelo/secuencial), criterio
   de éxito por tarea y qué debe verificar el tester.
3. Ejecuta ese plan invocando `desarrollador` y `tester` (en paralelo donde los ficheros sean
   disjuntos). Conflicto runtime → pausa, secuencializa, reporta.
4. Gate A completo: linters/static + suites + scripts de tests/bin/ + smoke si la unidad cierra.
   Todo exit 0 con counts reales. Después, invoca al `orquestador` en modo auditoría para el
   veredicto independiente. Sin su ✓, la wave NO cierra; sus 🔴 se resuelven o se escalan.
5. Prepara gate B: PRE-VALIDA cada comando del walk (restaurando si es destructivo) y entrega
   comandos copy-paste + qué debe VERSE en cada paso + dónde firma el humano en tasks.md.
6. HOLD: reporte formato CLAUDE.md. El merge a rama canónica y el tag son del humano.
