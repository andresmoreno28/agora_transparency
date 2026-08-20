---
name: gate-a-verde
description: Use when about to declare a wave, task or gate complete, when a test suite or invariant script fails and the fix is tempting to skip, when reporting test results, or when considering marking something done based on an exit code alone.
---

# Cerrar un gate en verde de verdad

## Principio nuclear

**Verde significa counts reales, no exit codes.** Un `exit 0` sin números no prueba nada: una suite
que no encontró tests devuelve 0, un axe que no cargó la página devuelve 0, un grep sin ficheros
devuelve 0. Si no puedes decir *cuántos*, no lo has verificado.

## Qué se reporta

| Capa | Count exigido |
|---|---|
| PHPUnit | nº de tests **y** de assertions |
| Install smoke | rutas comprobadas y qué se vio en cada una |
| Playwright funcional | nº de specs pasados |
| Playwright visual | nº de screenshots **comparados** (no solo generados) |
| axe | nº de páginas analizadas y nº de violaciones (debe ser 0) |
| Invariantes `tests/bin/` | nº de ficheros escaneados y nº de hallazgos |
| Linters | nº de ficheros analizados |

Un count de **0 tests ejecutados** es un fallo, no un éxito.

## Antes de decir "hecho"

1. Ejecuta el comando. No lo deduzcas.
2. Lee la salida entera, no la última línea.
3. Comprueba que el nº de tests ejecutados es **mayor que cero** y coherente con lo que escribiste.
4. Si algo falla, arréglalo o **escálalo**. No lo silencies.
5. Reporta el comando exacto y su salida real.

## Prohibido para poner un gate en verde

- Debilitar una aserción o marcar un test como `skip`/`incomplete`
- Silenciar, excluir o "temporalmente" desactivar un invariante de `tests/bin/`
- Excluir una regla de axe o una ruta del escaneo
- Añadir el fichero problemático al ignore del linter
- Bajar el umbral de comparación visual hasta que pase

**Todo esto se escala al humano. Ninguna de estas acciones cierra un gate.**

## Racionalizaciones y realidad

| Excusa | Realidad |
|---|---|
| "El test es frágil, no el código" | Puede ser — pero eso lo decide el humano, no tú al cerrar |
| "Es un falso positivo del invariante" | Si el reporte contradice al script, **manda el script**: re-córrelo |
| "Pasa en local, el CI va raro" | El pipeline de drupalcode ES el gate, no una aproximación |
| "Lo dejo skip y abro una tarea" | Un skip sin firma del humano es un gate falso |
| "Exit 0, luego está bien" | ¿Cuántos tests corrieron? Si no lo sabes, no está bien |
| "Solo falta un detalle menor" | Nada roto avanza (no-negociable nº9) |
| "Ya lo verifiqué antes" | Verifícalo ahora, en el estado actual del disco |

## Red flags — PARA y escala

- Estás editando un test, un `.eslintignore`, un `phpcs.xml` o un umbral **mientras cierras un gate**
- Vas a reportar "todo en verde" sin tener números delante
- Un invariante te molesta y estás pensando en su flag de exclusión
- Estás a punto de escribir "debería funcionar" o "en principio pasa"

## Después del gate A

Gate A verde **no cierra la wave**. Falta el veredicto independiente del `orquestador` (auditoría
de solo lectura) y, después, la firma del humano en el gate B. Sin esas dos cosas, la wave sigue abierta.
