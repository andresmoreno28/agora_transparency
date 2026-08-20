DISPATCH · Ágora · 001-fundación / Turno 00 — Scaffolding: research + plan, SIN CÓDIGO
Turno de scaffolding puro. Base: repo recién inicializado con este kit. NO se escribe código, NO se
crea el proyecto en Drupal.org, NO se instala nada aún salvo lo necesario para verificar. NO tag.

═══════════════════════════════════════════
CONTEXTO
═══════════════════════════════════════════
Ágora es un Site Template para Drupal CMS (portal de transparencia) destinado al Site Template
Marketplace de Drupal.org. Identidad, arquitectura de recipes y restricciones del marketplace están
encodeadas en `specs/000-proyecto/plan.md` y `CLAUDE.md` — léelos primero; mandan sobre este prompt.
Decisiones ya firmadas por el humano (no reabrir): partir del **Drupal CMS Site Template Starter Kit**
oficial; desarrollo en **git.drupalcode.org** como proyecto general (mirror GitHub opcional después);
solo dependencias estables con cobertura de seguridad; Config Guardian dentro, Midgard fuera;
pnpm/composer/DDEV. Este turno existe porque construimos sobre tecnología joven (Drupal CMS, Canvas,
site templates) y el estado del arte DEBE verificarse fresco antes de fijar plan y tareas.

═══════════════════════════════════════════
RECONCILIATION PASS — OBLIGATORIO, AL FRENTE, SIN CÓDIGO
═══════════════════════════════════════════
1. Lee `CLAUDE.md`, `specs/000-proyecto/plan.md`, `specs/000-proyecto/DECISIONES.md`. Vincula todo
   lo que produzcas a este marco. Verifica EN DISCO el siguiente número D-NNN libre antes de proponer
   decisiones nuevas.
2. Ground-truth FRESCO en web (todo fechado, con URL, guardado en `specs/001-fundacion/research/`):
   a. Versión estable actual de **Drupal CMS** y estado de **Drupal Canvas** (¿qué exige hoy un tema
      "Canvas-compatible"? ¿hay tema de referencia/starter?).
   b. El **Drupal CMS Site Template Starter Kit** (proyecto drupal_cms_site_template_base en
      Drupal.org): estructura real, qué CI trae (GitLab CI y GitHub Actions), qué recipes base
      incluye, cómo se integra Tugboat y Project Browser.
   c. El flujo real de **compartir plantilla** (new.drupal.org/site-template/share) y los requisitos
      vigentes del marketplace (new.drupal.org/site-template/apply): confirma que siguen como los
      encodea plan.md §4; si divergen → repórtalo (disco/plan se reconcilia, no se fuerza).
   d. `drush site:export` del **Drupal CMS Helper**: estado, limitaciones conocidas.
   e. **gitlab_templates de Drupal.org**: jobs exactos disponibles hoy (phpcs, phpstan, cspell,
      eslint, stylelint, phpunit…), cómo se referencian, y si existe job/patrón para probar
      instalabilidad de recipes. Viabilidad de Playwright+axe en el CI de drupalcode (¿runners lo
      soportan? ¿o los tests visuales van a GitHub Actions del mirror?).
   f. Disponibilidad del machine name en Drupal.org: "agora" probablemente ocupado; comprueba
      alternativas (p.ej. agora_transparency, agora_gov) SIN crear nada.
   g. Módulos candidatos del SBOM (facetas/búsqueda, webform, ECA, IA): para cada uno, versión
      estable actual, compatibilidad con el core que usa Drupal CMS hoy y estado de cobertura de
      seguridad. Lo que no cumpla → fuera, con alternativa dentro de Drupal CMS si existe.
3. Entregables del pass: `research/2026-XX-XX-estado-del-arte.md` (fechado, con fuentes) +
   reporte de divergencias respecto a plan.md/este prompt.

REGLAS DE PARADA:
  ▸ Los requisitos del marketplace han cambiado respecto a plan.md §4 → ESCALAR con el diff exacto.
  ▸ El Starter Kit impone una estructura incompatible con la arquitectura de recipes de plan.md §2
    → ESCALAR con opciones (adaptar arquitectura vs no usar starter kit), NO decidir solo.
  ▸ Canvas exige releases inestables para algo esencial → ESCALAR (choca con la no-negociable nº1).
  ▸ Un candidato del SBOM no tiene cobertura de seguridad y no hay alternativa razonable → ESCALAR.
  ▸ Cualquier cosa que este prompt asuma y el estado del arte contradiga → el estado real manda;
    repórtalo, no lo fuerces.

═══════════════════════════════════════════
ALCANCE (SÍ / NO)
═══════════════════════════════════════════
SÍ: research fechada · `specs/001-fundacion/plan.md` (objetivo de la unidad: esqueleto desde starter
kit + CI verde en vacío + estructura de recipes creada pero mínima) · `specs/001-fundacion/tasks.md`
(tareas numeradas, waves, gates A/B por wave) · open questions con opciones + recomendación ·
propuesta de los primeros scripts de invariantes (`tests/bin/`: no-unstable-deps, no-patches,
no-secrets, sbom-check) COMO ESPECIFICACIÓN, no implementados.
NO: código · instalación del sitio · creación del proyecto en Drupal.org (unidad 007, humano) ·
diseño visual del tema (unidad 002) · decisiones load-bearing cerradas por tu cuenta · tag/release.

═══════════════════════════════════════════
REGLAS NO-NEGOCIABLES
═══════════════════════════════════════════
Las 10 de CLAUDE.md aplican íntegras. Recordatorio de las que este turno puede rozar: solo estables
sin parches; secretos jamás en repo; docs de proceso en español, identificadores en inglés; pnpm
exclusivo si algún tooling JS aparece en research; commits sin co-autoría de IA; append-only.

═══════════════════════════════════════════
GATE A · GATE B
═══════════════════════════════════════════
Gate A de este turno (docs-only): los tres entregables existen en disco, la research tiene fecha y
fuentes, tasks.md tiene gates A/B definidos por wave con comandos concretos.
Gate B (humano): Andrés lee plan.md + open questions y firma las decisiones D-NNN nuevas en
DECISIONES.md. Prepara tú la lista de decisiones EXACTA: una por punto, opciones, recomendación
marcada, en lenguaje llano.

═══════════════════════════════════════════
ORDEN DE EJECUCIÓN
═══════════════════════════════════════════
1) Reconciliation (lecturas disco + research web fechada). 2) Delegar en el subagente `orquestador`
la redacción de research.md, plan.md y tasks.md de la unidad y las open questions + propuestas D-NNN
(este turno NO invoca a `desarrollador` ni `tester`: es scaffolding, sin código). 3) La sesión
principal integra y persiste los entregables en disco. 4) HOLD: reportar al humano con el formato de
CLAUDE.md (reconciliación → entregables → escalaciones 🔴🟡🟢 → decisiones pendientes). NO avanzar a
implementación hasta firma.
