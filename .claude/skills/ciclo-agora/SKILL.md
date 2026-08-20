---
name: ciclo-agora
description: Use when starting or resuming work on the Ágora project, when a dispatch or prompt asks to plan, implement or close a wave, when deciding whether something needs the human's signature, or when writing the end-of-turn report.
---

# La dinámica de trabajo de Ágora

## Principio nuclear

**El disco manda sobre cualquier prompt.** Antes de implementar nada se hace un reconciliation pass:
si el prompt asume algo que el disco contradice, **para y reporta** — no lo fuerces para que encaje.

## Roles — no mezclar

| Quién | Hace | No hace |
|---|---|---|
| **Andrés (humano)** | Decide lo load-bearing, firma gates B, merges a rama canónica, tags, releases, publicación en Drupal.org | Escribir prompts largos |
| **Sesión principal** | Coordina, mantiene contexto, invoca subagentes, ejecuta sus planes, escala | Implementar a mano lo que toca a un subagente; planificar o cerrar sin pasar por `orquestador` |
| **`orquestador`** | Scaffolding, plan de lanes, auditorías de solo lectura, veredicto de gate | Implementar; invocar a otros subagentes |
| **`desarrollador`** | Implementa contra el plan firmado | Cerrar su propia tarea |
| **`tester`** | Tests, smokes, invariantes, con counts reales | Debilitar un test para pasar |

**Limitación de plataforma:** los subagentes **no pueden invocar subagentes**. El `orquestador`
devuelve órdenes; la sesión principal las ejecuta con `desarrollador` y `tester`.

## Reconciliation pass — siempre, al frente

1. Lee `CLAUDE.md`, `specs/000-proyecto/plan.md`, `DECISIONES.md`, `IDIOMS.md`.
2. Lee `plan.md` + `tasks.md` de la unidad activa (la de número más alto con tareas sin firmar).
3. Verifica **en disco**: rama, árbol limpio/sucio, último tag, último task firmado `[✓]`,
   **siguiente D-NNN libre**.
4. Reporta divergencias entre lo que asume el prompt y lo que hay. Las divergencias son sanas.

## Ciclo de una wave

```
reconciliation → orquestador planifica lanes → desarrollador + tester en paralelo
   → gate A (counts reales) → orquestador audita y da veredicto → gate B (firma humana)
```

- Lanes en paralelo **solo** si tocan ficheros disjuntos. Lo que comparte área, secuencial.
- El `tester` arranca en paralelo al código: sus fallos iniciales son esperados — dilo.
- Sin ✓ explícito del `orquestador`, **la wave no cierra**.

## Qué necesita firma del humano

- Cualquier decisión arquitectural load-bearing → **opciones + recomendación ★**, decide él
- Gate B de cada wave
- Merge a rama canónica, tags, releases
- Creación del proyecto en Drupal.org

Nunca cierres una de éstas por tu cuenta. Prepara la decisión, no la tomes.

## Append-only

- Tareas firmadas en `tasks.md` **no se renumeran**
- Decisiones firmadas **no se editan**: se enmiendan o se abre una nueva
- Los `D-NNN` se verifican **en disco** antes de proponer el siguiente

## Convenciones que se rompen sin querer

| Regla | Detalle |
|---|---|
| Idiomas | Docs de proceso en **español**; identificadores, código, commits y docs públicas en **inglés** |
| Commits | Convencionales, en inglés, **sin trailers de co-autoría de IA** |
| Etiquetas | `[ejecutor]`, `[andres]` — nunca nombres de herramientas de IA |
| Tooling | Composer para PHP, **pnpm exclusivo** para JS (ni npm ni yarn, tampoco en docs ni CI) |
| Research | Toda afirmación sobre el estado del arte lleva **fecha y fuente** y se re-verifica (I-001) |

## Formato de reporte al cerrar un turno

1. **Reconciliación** — qué asumía el prompt vs qué hay en disco
2. **Hecho / no hecho** — con counts reales, no exit codes
3. **Escalaciones** 🔴/🟡/🟢 — cada una con opciones + recomendación
4. **HOLD** — qué firma se necesita antes de seguir

## Red flags — PARA

- Vas a implementar sin haber leído el disco
- Vas a cerrar una decisión arquitectural tú mismo
- Vas a renumerar o editar algo ya firmado
- Vas a reportar verde sin números
- El prompt asume algo y el disco dice otra cosa, y estás a punto de seguir igualmente
