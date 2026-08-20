---
name: accesibilidad-wcag-aa
description: Use when writing or reviewing Twig templates, CSS tokens, components, forms or demo pages for the site template, when choosing colours or focus styles, when an axe run reports violations, or when preparing the accessibility statement required for publication.
---

# Accesibilidad WCAG 2.2 AA como gate

## Principio nuclear

**La accesibilidad se escribe, no se parchea.** En este proyecto es un gate: axe sin violaciones y
navegación por teclado en los flujos clave. Un componente inaccesible no es "deuda", es una tarea
sin terminar.

## Lo que se comprueba antes de dar nada por hecho

| Capa | Comprobación | Herramienta |
|---|---|---|
| Semántica | Landmarks, jerarquía de encabezados sin saltos, listas reales | Revisión + axe |
| Teclado | Todo alcanzable en orden lógico; foco **visible** siempre; sin trampas | Manual, Tab/Shift+Tab |
| Contraste | Texto ≥ 4.5:1; texto grande y UI ≥ 3:1 | Tokens del tema |
| Formularios | `<label>` asociado; errores enlazados al campo; no solo color | axe + manual |
| Imágenes | `alt` con propósito; decorativas con `alt=""` | Revisión |
| Idioma | `lang` correcto y por fragmento en contenido bilingüe ES/EN | Revisión |
| Movimiento | Respeta `prefers-reduced-motion` | CSS |

## Criterios de 2.2 que se olvidan más

WCAG **2.2** añadió criterios que no estaban en 2.1 y que suelen fallar:

- **Focus Not Obscured** — el elemento enfocado no puede quedar tapado por cabeceras sticky ni
  banners de cookies. Es el fallo más habitual en portales institucionales.
- **Target Size (Minimum)** — objetivos táctiles ≥ 24×24 px, salvo excepciones.
- **Dragging Movements** — todo lo que se arrastre necesita alternativa con un solo puntero.
- **Consistent Help** — si hay ayuda/contacto, en el mismo sitio en todas las páginas.
- **Redundant Entry** — no volver a pedir datos ya introducidos en el mismo proceso.
- **Accessible Authentication** — sin pruebas cognitivas obligatorias (recordar, transcribir).

## Tablas de datos accesibles

Este template muestra retribuciones, contratos y presupuestos. Las tablas son contenido central:

- `<th scope="col|row">` real, nunca `<td>` en negrita
- `<caption>` que describa la tabla
- Nada de tablas para maquetar
- **Toda visualización necesita su tabla equivalente** como fallback accesible — y es la tabla, no
  el gráfico, la fuente de verdad

## Declaración de accesibilidad

Es un entregable, no un extra. Debe existir y contener: grado de conformidad declarado, fecha de la
evaluación, método, limitaciones conocidas y **canal de quejas operativo**.

## Errores comunes

- **Quitar el `outline` del foco** por estética → si lo quitas, sustitúyelo por algo con ≥ 3:1
- **Contraste medido sobre el color equivocado** → mide sobre el fondo real renderizado
- **`aria-*` para tapar HTML mal hecho** → primero el elemento nativo correcto
- **Color como único portador de significado** (estados, errores, categorías) → añade texto o forma
- **Probar solo la home** → los flujos (búsqueda, facetas, formulario de solicitud) son lo que falla
- **axe en verde = accesible** → axe detecta una fracción; el teclado hay que probarlo a mano

## Red flags — PARA

- "Lo arreglamos en la unidad de hardening" → la a11y no se aplaza, es gate por wave
- "axe pasa, ya está" → ¿lo has recorrido con teclado?
- "Es un caso raro" → los lectores de pantalla no son un caso raro
- Vas a silenciar/excluir una regla de axe para poner el gate en verde → eso se escala, no se silencia
