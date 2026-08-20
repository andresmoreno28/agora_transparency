# Ágora · Unidad 001 — Fundación · Plan

> Producido en el scaffolding turn del `DISPATCH-00.md` [ejecutor] 2026-08-20.
> Base factual: `research/2026-08-20-estado-del-arte.md`. **No implementar nada de aquí sin gate B.**

## 1 · Objetivo de la unidad

Que exista en disco un repositorio que **ya es un site template válido de Drupal CMS**: instalable
sobre un Drupal limpio, con el pipeline de drupalcode en verde y los invariantes de `tests/bin/`
operativos — **todavía sin identidad visual, sin modelo de contenido y sin contenido demo**.

Criterio de "hecho": un tercero clona el repo, levanta DDEV, instala Drupal CMS y **ve el template
de Ágora en el selector de plantillas del instalador**.

## 2 · Qué cambió respecto a lo que asumía el plan maestro

La research invalidó dos supuestos estructurales de `CLAUDE.md` §Estructura del repo:

1. **No hay `recipes/`.** El repositorio **es** una única receta: `recipe.yml` en la raíz con
   `type: Site`. Las funcionalidades se componen referenciando **paquetes composer externos**.
2. **No hay `themes/agora_theme/`** en el flujo por defecto: el tema lo **genera**
   `drupal/site_template_helper` desde `extra.drupal-site-template.generate-theme` en `composer.json`.

Ambas cosas disparan la regla de parada nº2 del dispatch → **D-011 y D-008 deben firmarse antes de
que esta unidad arranque**. El resto del plan maestro sobrevive intacto.

## 3 · Alcance

**SÍ** · Copiar y desnudar el starter kit (`1.x`) · identidad de paquete · `recipe.yml` propio ·
entorno DDEV reproducible · CI verde en vacío · los 4 scripts de invariantes · install smoke real.

**NO** · Modelo de contenido (002) · tema con estética (002) · contenido demo (003) · ECA (004) ·
IA y Config Guardian operativos (005) · creación del proyecto en Drupal.org (007) · tag/release.

## 4 · Waves

### Wave 1 — Esqueleto e identidad
Copiar `1.x`, renombrar el paquete, limpiar el andamiaje del kit, `.gitignore`/`.gitattributes`
definitivos, `recipe.yml` propio con las recetas base heredadas.
**Depende de:** D-007 (machine name), D-011 (arquitectura).

### Wave 2 — Entorno y CI
DDEV ≥ 1.25.0 reproducible y documentado; `.gitlab-ci.yml` con sus variables; primer pipeline verde;
decidir qué corre en GitHub Actions.
**Depende de:** wave 1. Abre D-009.

### Wave 3 — Invariantes
`tests/bin/no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check` (spec en §6).
Paralelizable con wave 2: ficheros disjuntos.

### Wave 4 — Install smoke y cierre
Instalación en limpio verificada, `screenshot.webp` provisional propio, README en inglés,
veredicto del `orquestador`.
**Depende de:** waves 1–3.

## 5 · Gates

| Wave | Gate A (automatizable, con counts) | Gate B (Andrés) |
|---|---|---|
| 1 | `composer validate --strict` exit 0 · `recipe.yml` con `type: Site` · sin `_comment` ni `extra.drupal-site-template` residuales | Confirma machine name e identidad |
| 2 | Pipeline de drupalcode **verde**, con nº de jobs ejecutados · `ddev start` limpio desde cero | Confirma dónde corre cada tipo de test |
| 3 | Los 4 scripts exit 0, cada uno reportando **nº de ficheros escaneados** y **nº de hallazgos** | — |
| 4 | `InstallTest`+`ValidationTest`+`RequirementsTest` verdes con nº de tests y assertions · template visible en el selector tras `sql:drop` + reinstalación | Firma el cierre de la unidad |

Regla que aplica a los cuatro: **counts reales, nunca exit codes sueltos** (skill `gate-a-verde`).

## 6 · Especificación de los invariantes (`tests/bin/`) — NO implementados aún

Los cuatro: exit 0 = limpio, exit 1 = hallazgos. Todos imprimen **ámbito escaneado + nº de ficheros +
nº de hallazgos**, y cada hallazgo con `fichero:línea`.

| Script | Qué busca | Ámbito | Notas |
|---|---|---|---|
| `no-unstable-deps` | `-dev`, `-alpha`, `-beta`, `-rc`, `dev-` en constraints; `minimum-stability` distinto de `stable` | `composer.json`, `composer.lock` | **Excluir** el starter kit: se copia, no se declara (research §3.1) |
| `no-patches` | Clave `patches`, `composer-patches`, `patches-file` | `composer.json` | Prohibición literal del kit |
| `no-secrets` | `api[_-]?key`, `secret`, `token`, `passwd`, `password`, `Bearer `, DSNs, claves privadas | todo el repo salvo `.git/` | Debe correr también sobre `config/` y `content/` |
| `sbom-check` | Por cada `drupal/*` de `require`: consulta `updates.drupal.org` y exige release **estable** + `<security covered="1">` + línea en `DECISIONES.md` | `composer.json` + `DECISIONES.md` | Método verificado en research §10.4 |

## 7 · Riesgos

| Riesgo | Sev | Mitigación |
|---|---|---|
| Copiar el `require` de la rama `2.x` arrastra `project_browser ^2.1-beta3` (**beta**) | 🔴 | Partir de `1.x`; si hace falta Project Browser, esperar su estable o dejarlo fuera |
| Requisitos del marketplace sin verificar (DCP-only, cuotas) | 🔴 | Verificar antes de 006; no bloquea 001 |
| Machine name ocupado en Drupal.org | 🟡 | Verificar antes de wave 1; alternativas ya listadas |
| Runners de drupalcode sin soporte para Playwright/axe | 🟡 | D-009; plan B = GitHub Actions del mirror |

## 8 · Open questions → decisiones a firmar

Redactadas en lenguaje llano, con recomendación ★, en `DECISIONES.md` §Pendientes.
**D-007** machine name · **D-008** enfoque del tema · **D-009** dónde corren los tests visuales ·
**D-011** arquitectura de recetas (bloqueante) · **D-012** vía de publicación · **D-013** provider de IA.
