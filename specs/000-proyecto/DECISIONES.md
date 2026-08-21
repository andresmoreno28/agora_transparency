# Ágora · Registro de decisiones (D-NNN, append-only)

> Verificar EN DISCO el siguiente número libre antes de añadir. Firmadas no se editan: se enmiendan
> (mismo commit que el cambio que lo motiva, solo si es consecuencia directa) o se abre una nueva.

- **D-001** · Nombre y concepto: "Ágora", portal de transparencia como Site Template de Drupal CMS,
  plantilla gratuita insignia para el marketplace. Machine name PENDIENTE de verificar disponibilidad
  (candidatos: agora_transparency, agora_gov) — se cierra tras el DISPATCH-00. Firmada (concepto) por [andres] 2026-08-20.
- **D-002** · Repositorio y base: desarrollo en git.drupalcode.org como proyecto general de Drupal.org,
  partiendo del Drupal CMS Site Template Starter Kit oficial (trae GitLab CI, GitHub Actions, Tugboat
  y recipes base). Mirror en GitHub opcional, solo para portfolio y, si hiciera falta, tests visuales.
  Firmada por [andres] 2026-08-20.
- **D-003** · Stack y tooling: Drupal CMS estable actual + Recipes + tema compatible con Drupal Canvas.
  Composer (PHP), pnpm exclusivo (JS), DDEV (local). Firmada por [andres] 2026-08-20.
- **D-004** · Política SBOM: solo releases estables con cobertura del equipo de seguridad; sin parches
  ni pins. Config Guardian incluido y preconfigurado (recipe agora_governance). Midgard EXCLUIDO
  mientras esté en alpha (solo narrativa en docs). Firmada por [andres] 2026-08-20.
- **D-005** · Idiomas: docs de proceso ES; código, identificadores, commits y docs públicas EN;
  contenido demo bilingüe ES/EN. Sin trailers de co-autoría de IA en commits. Firmada por [andres] 2026-08-20.
- **D-006** · Calidad como gate: pipeline de drupalcode (gitlab_templates) + install smoke + PHPUnit +
  Playwright (funcional/visual) + axe + scripts de invariantes en tests/bin/. Nada avanza con el
  pipeline en rojo. Firmada por [andres] 2026-08-20.

## Pendientes (se plantean tras el DISPATCH-00, con opciones + recomendación)
- D-007 · Machine name definitivo del proyecto.
- D-008 · Enfoque del tema Canvas (partir del tema del starter kit vs tema propio desde cero).
- D-009 · Dónde corren los tests visuales (drupalcode CI vs GitHub Actions del mirror), según lo que
  soporten los runners hoy.
- D-010 · Alcance exacto del contenido demo v1.

---

## Framing de decisiones pendientes — preparado [ejecutor] 2026-08-20

> Tras la research `specs/001-fundacion/research/2026-08-20-estado-del-arte.md`.
> Ninguna se cierra sin firma de [andres]. ★ = recomendación del ejecutor.
> Siguiente D-NNN libre verificado en disco: **D-011**.

### D-007 · Machine name definitivo
Contexto: el nombre del paquete será `drupal/<machine_name>` y ya no se puede cambiar tras publicar.
No se pudo comprobar disponibilidad (drupal.org bloqueado en la sesión).
- **A** · `agora` — limpio, pero es una palabra común: probablemente ocupado.
- **B ★** · `agora_transparency` — descriptivo, en inglés, casi seguro libre.
- **C** · `agora_gov` — más corto, pero "gov" sugiere administración estatal y el público es más amplio.
★ **B**: describe qué hace, sobrevive a la búsqueda en Project Browser y no depende de que `agora` esté libre.
*Requisito previo:* comprobar disponibilidad en drupal.org antes de fijarlo.

### D-008 · Enfoque del tema
Contexto: el starter kit **genera** el tema con `site_template_helper` (`generate-theme`, `from: false`).
`CLAUDE.md` asumía una carpeta `themes/agora_theme/` versionada, que no es el flujo por defecto.
- **A ★** · Tema **generado** por el plugin y personalizado después vía config y CSS del propio tema.
  Sigue el camino oficial; menos fricción en la revisión.
- **B** · Tema **propio versionado** en el repo. Más control y mejor para la tesis profesional, pero
  se sale del flujo estándar y hay que justificarlo ante el marketplace.
★ **A** para v1: el objetivo declarado es pasar la revisión a la primera. La estética sobria se
consigue igual con tokens y CSS; B es una desviación que hay que defender sin necesidad.

### D-009 · Dónde corren los tests visuales
Contexto: el kit trae CI de GitLab (jobs de la DA) **y** `.github/workflows/phpunit.yml`. No se pudo
verificar si los runners de drupalcode soportan Playwright + axe.
- **A ★** · Linters, static analysis y PHPUnit en **drupalcode**; Playwright + axe en **GitHub Actions**
  del mirror. El kit ya usa GitHub para PHPUnit, así que el mirror no es solo portfolio.
- **B** · Todo en drupalcode, si los runners lo permiten.
★ **A**, pero **verificar primero**: si drupalcode soporta Playwright, B es más limpio (un solo gate).
Decisión revisable en la wave 2 de la unidad 001.

### D-010 · Alcance del contenido demo v1
Se pospone a la unidad 003, cuando exista el modelo de contenido. Mantener abierta.

### D-011 · Arquitectura de recetas 🔴 **BLOQUEANTE de la unidad 001**
Contexto: `plan.md` §2 y `CLAUDE.md` describen `recipes/agora_base`, `agora_publishing`, `agora_foi`,
`agora_ai`, `agora_governance` como subdirectorios. **El starter kit no funciona así**: el repositorio
ES una sola receta (`recipe.yml` en la raíz, `type: Site`) y compone **paquetes composer externos**.
No hay evidencia de que el instalador resuelva sub-recetas locales.
- **A ★** · **Una sola receta** en la raíz. Ágora = un `recipe.yml` que compone recetas de Drupal CMS
  y módulos contrib. Es el camino verificado del kit y el de menor riesgo en la revisión.
  Coste: se pierde la modularidad interna que `plan.md` §2 quería para la futura plantilla de pago.
- **B** · **Varios proyectos en Drupal.org**: `agora_base`, `agora_foi`… como recetas contrib
  independientes, y Ágora como site template que las lista en `recipes:`. Máxima reutilización, es el
  patrón que usa el propio Drupal CMS. Coste: mantener N proyectos, N releases, N revisiones.
- **C** · Monorepo con sub-recetas locales. Conserva el plan original tal cual, pero **no está
  verificado que funcione** y es el mayor riesgo de rechazo.
★ **A para v1, con B como evolución**: publicar primero un template que pase la revisión, y extraer
recetas reutilizables cuando exista la plantilla de pago. C se descarta salvo que se verifique.
*Si se firma A o B, hay que enmendar `plan.md` §2 y la sección "Estructura del repo" de `CLAUDE.md`.*

### D-012 · Vía de publicación 🔴
Contexto (⚠️ **sin verificar**, drupal.org bloqueado): señales de que el marketplace arrancó como
**piloto limitado a Drupal Certified Partners**, con **395 $ por listing + 250 $ anuales**.
`CLAUDE.md` declara como meta "pasar la revisión del marketplace a la primera" y `plan.md` describe v1
como "plantilla gratuita insignia" — ambas cosas podrían ser incompatibles con lo anterior.
- **A ★** · **Vía Community** (proyecto general en Drupal.org, publicable sin revisión y sin coste).
  Es lo que ya eligió D-002. Cumple igual todos los estándares de calidad; el marketplace queda como
  objetivo posterior si abre a no-DCP.
- **B** · Marketplace, asumiendo cuota y requisito de DCP.
★ **A**, pero **la decisión no debe cerrarse hasta verificar** los requisitos reales en drupal.org.
Construir cumpliendo el estándar del marketplace mantiene ambas puertas abiertas.

### D-013 · Provider de IA
Contexto: `ai` estable es **1.4.7** (la rama 1.5 solo tiene alpha/rc). `ai_provider_openai` 1.2.5 está
estable y cubierto. `plan.md` §2 exige **proveedor-agnóstico** y degradación elegante sin clave.
- **A ★** · Depender solo de `ai` (^1.4) y **ningún provider concreto**. El usuario elige e instala su
  provider tras la instalación. Máxima neutralidad; el CI corre sin claves de forma natural.
- **B** · Incluir `ai_provider_openai` como recomendado en `recommended.yml`, sin que sea dependencia dura.
- **C** · Depender de un provider concreto. Contradice el proveedor-agnóstico. Descartar.
★ **A**, con **B** como complemento: recomendar sin imponer.

### Nota sobre D-004 (Config Guardian) — CONFIRMADA, sin cambios
Verificado el 2026-08-20 en `updates.drupal.org`: **Config Guardian 1.0.3**, estable, **con cobertura
de seguridad**, `core_compatibility: ^10.5 || ^11 || ^12` → compatible con el core 11.4 que exige el
starter kit. Cumple las cuatro puertas de la política SBOM. **No requiere enmienda.**
Igualmente verificados y aptos: ECA 3.1.6, AI 1.4.7, AI Agents 1.3.4, Search API 8.x-1.41,
Facets 3.0.4, Webform 6.3.0, Charts 5.2.3 — todos estables y cubiertos (research §10).

---

## Firmas — 2026-08-21 [andres]

> Append-only. Esta sección no edita nada anterior. Cuando una firma cambia el marco de una
> decisión ya redactada arriba, se dice explícitamente cuál queda superada y por cuál.
> Siguiente D-NNN libre tras esta tanda: **D-015**.

- **D-007** · Machine name definitivo: **`agora_transparency`**. El paquete composer será
  `drupal/agora_transparency`; el título visible del proyecto sigue siendo "Ágora".
  *Evidencia (2026-08-21):* `git.drupalcode.org/api/v4/projects/project%2Fagora` → **200** (ocupado
  por un proyecto ajeno); `…%2Fagora_transparency` y `…%2Fagora_gov` → **404** (libres).
  ⚠️ La API de GitLab es el único oráculo válido: `www.drupal.org/project/<X>` devuelve **302 hacia
  new.drupal.org para cualquier cadena**, incluida una inexistente, y por tanto **no prueba
  disponibilidad** (ver I-012). Firmada por [andres] 2026-08-21.

- **D-011** · Arquitectura de recetas: **opción A — una sola receta en la raíz**. Ágora es un único
  `recipe.yml` (`type: Site`) que compone recetas de Drupal CMS y módulos contrib declarados en
  `require`. **No hay directorio `recipes/`** con sub-recetas locales. Firmada por [andres] 2026-08-21.
  *Riders de [andres]:*
  (a) `plan.md` §2 y `CLAUDE.md` §"Estructura del repo" se enmiendan **en este mismo commit**.
  (b) La reutilización para la futura plantilla de pago se hará **extrayendo piezas a recetas
      contrib independientes (patrón B)** cuando exista esa unidad. En v1 se deja **costura, no
      implementación**: cada área funcional ocupa un bloque contiguo y rotulado de `recipe.yml`, y
      los identificadores propios llevan prefijo de área (`agora_base_*`, `agora_foi_*`…), de modo
      que la extracción futura sea mover ficheros y no reescribir.
  (c) El dato "cuota de 395 $ por listing" queda pendiente de verificación con fuente y fecha en la
      unidad 007. → **Pendiente CERRADO el mismo día por D-012**, ver allí.

- **D-008** · Firmada por [andres] 2026-08-21 como **opción A** (el tema **no** se escribe a mano
  dentro de este repositorio). Con dos salvedades registradas el mismo día:
  1. **El rider adjunto — "el tema generado queda committeado en el repo y la personalización va
     encima, versionada" — queda SUSPENDIDO por imposibilidad técnica**, verificada en origen:
     · `tests/src/Kernel/RequirementsTest.php` del kit exige **0 ficheros `*.info.yml`** en todo el
       paquete: *"Recipes cannot include any code (modules or themes) of their own; they must list
       them as dependencies in `composer.json`."*
     · El paquete se instala en `./recipes/<name>` (`drupal/cms` 2.x, `extra.installer-paths`),
       **fuera del docroot**, donde `RecursiveExtensionFilterCallback` ni siquiera recurre (solo
       `profiles/`, `modules/`, `themes/` del root).
     · El ADR oficial de site templates dice que un template **MAY depend on a theme**, no incluirlo.
     La condición de parada que [andres] adjuntó al rider ("si el kit regenera el tema en cada
     instalación del usuario final, PARAR") **no se dispara**: `site_template_helper` genera una sola
     vez, en el sitio de trabajo del autor, es idempotente, y el bloque `extra.drupal-site-template`
     se elimina antes de publicar.
  2. **D-008 queda SUBSUMIDA por D-014**: la pregunta "dónde vive el tema" se responde allí. Lo que
     sobrevive de D-008 es la regla negativa: *este repositorio no contiene tema propio*.

- **D-012** · Vía de publicación: **opción C — community primero, marketplace después**. No son
  excluyentes: el marketplace **exige** que el template sea un proyecto general.
  Firmada por [andres] 2026-08-21.
  *Hallazgo que cierra el pendiente (c) de D-011, verificado 2026-08-21:*
  · `new.drupal.org/site-template/apply`, §Individuals: *"Free templates: Any individual who wants
    to submit free templates, is welcome to."* Ser Drupal Certified Partner o Ripplemaker es
    requisito **solo para plantillas de pago**. La premisa "piloto DCP-only" era **falsa**.
  · La cuota de **395 $ + 250 $/año** procede de la *propuesta* de julio 2025
    (`drupal.org/project/innovation_ideas/issues/3532934`), que dice literalmente
    **"(none for pilot and MVP)"**. No hay cuota confirmada para el MVP.
  · `drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates`:
    *"All free site templates, including marketplace templates, are general projects for packaging
    and distribution purposes"*, y recomienda explícitamente *"sharing a community template first"*.
  *Riders de [andres]:* la unidad **007 pasa a ser "publicación community"**; la solicitud al
  marketplace se trata como **007-bis, no bloqueante**.
  *Nota:* los cinco criterios de revisión del marketplace (instalabilidad por CI · SBOM con cobertura
  de seguridad · manifiesto de licencias · atestación WCAG · respuesta de seguridad, sin pins ni
  parches) coinciden literalmente con `plan.md` §4: ese apartado queda **confirmado en origen**.

- **D-013** · Provider de IA: **opción A + B**. Dependencia dura únicamente de `ai` **^1.4**
  (nunca `^1.5`: esa rama solo tiene alpha/rc, violaría la no-negociable nº 1). Ningún provider como
  dependencia dura. `ai_provider_openai` (1.2.5, estable y con cobertura) se **recomienda** en
  `recommended.yml`, sin imponerse. Mantiene el proveedor-agnóstico de `plan.md` §2 y el CI corre sin
  claves (I-003). Firmada por [andres] 2026-08-21.
  *Riders de [andres]:*
  (a) La recomendación es **por criterio, no por marca**: cualquier provider que cumpla el mismo
      listón (release estable + cobertura de seguridad) se lista también en `recommended.yml`.
  (b) **Verificación fresca del estado de `ai` ^1.x al llegar a la unidad 005**, antes de implementar.
      Si `^1.5` alcanzó estable, se **propone enmienda**; no se asume (I-001).

- **D-014** · Dónde vive la estética de Ágora: **opción B — tema como proyecto separado en
  Drupal.org** (`drupal/agora_theme`), versionado con normalidad (Twig, tokens, tipografía OFL),
  declarado en el `require` de Ágora e instalado desde `recipe.yml`. Es la vía que contempla el ADR
  oficial y la que prevé el propio `RequirementsTest` del kit ("a bespoke theme to which it is
  strongly coupled"). Firmada por [andres] 2026-08-21.
  *Riders de [andres]:*
  (a) **Motivo adicional que hace a B la única opción compatible con las no-negociables:** la
      tipografía OFL auto-alojada son **ficheros**, y la configuración no los transporta; usar una
      CDN de fuentes es **pasivo RGPD** en sector público de la UE. La opción "todo en config de
      Canvas" no podía cumplir ni la licencia ni la privacidad.
  (b) `agora_theme` se **scaffolda con el generador oficial** (`site_template_helper`) y se
      **promueve a proyecto propio** desde ahí.
  (c) **Machine name del tema pendiente de verificar** (propuesta: `agora_theme`). Se cierra en la
      unidad 002, con el oráculo de I-012.
  (d) **La release estable de `agora_theme` es gate de la release de Ágora** en la unidad 007: debe
      existir antes.
  (e) Al crear el proyecto del tema, **opt-in a cobertura del equipo de seguridad**.
  (f) **Alcance del tema: mínimo con dientes** — tokens AA, tipografía, tablas y formularios
      accesibles, compatible con Canvas. **Sin frameworks CSS genéricos.**

- **D-015** · **Policy for AI artifacts in the public repository.** Signed by [andres] 2026-08-21.
  1. **`AGENTS.md` is product.** It stays in the repository, ships to the end user, and is written
     in English. Its "Template-specific notes" section — empty in the starter kit — is filled with
     what is specific to Ágora (do not hand-edit exported config, where the theme lives, AI features
     degrade without an API key in CI). It carries an **audience header**: *a guide for AI assistants
     working on a site built WITH this template*. During development of the template itself,
     `CLAUDE.md` governs — no agent of ours may mistake `AGENTS.md` for process instructions.
  2. **`CLAUDE.md`, `.claude/` and `specs/` stay VISIBLE** in the public repository, as disclosure of
     methodology. They are `export-ignore`d so they do not travel inside the packaged release.
     ⚠️ Verified 2026-08-21: `export-ignore` affects **only the packaged tarball**, never the git
     repository — *"All files will still be available for users that clone your project via Git."*
     Visibility in the repo is therefore a deliberate choice, not a side effect. Source:
     `drupal.org/docs/develop/git/git-for-drupal-project-maintainers/creating-a-project-release`
  3. **Amends D-005 on language** → superseded in full by **D-017**, see below.
  4. The `README.md` gains a **"Development process"** section: human-in-the-loop methodology,
     decisions signed under `specs/`, and disclosure of AI use in line with the current governance
     debate. It frames the artifacts before anyone discovers them.
  5. **Rule 7 reaffirmed:** no AI co-authorship trailers in commit messages, ever.

- **D-016** · **Repository workflow: D-002 is CONFIRMED, not amended.** Development stays canonical on
  `git.drupalcode.org`; the GitHub mirror is **read-only and carries the same history**, a trivial
  sync, and is set up in unit 007 (today it is only recorded). Signed by [andres] 2026-08-21.
  *Rationale, recorded because it is load-bearing:*
  · A **synthetic history** on drupalcode would reproduce the appearance of the "code dump" pattern
    that governance guidelines penalise. The **real history** — waves, signed gates, granular
    commits — **is the anti-slop evidence**.
  · Commits on drupalcode are the author's **contribution currency**; a filtered republish would
    throw them away.
  · The collaboration surface must match the truth of the code.
  · The aesthetic discomfort of exposing process artifacts is handled by `README` §"Development
    process" (D-015.4), which frames them before anyone stumbles on them.
  *Considered and rejected:* developing on a public GitHub as canonical and publishing only the
  template layer to drupalcode through a filtering script. Rejected for the reasons above.

- **D-017** · **Language: the ENTIRE repository is in English, process layer included.** Amends **D-005**
  and **rule 6 of `CLAUDE.md`**. Spanish remains the language of orchestration **outside** the
  repository (conversation with the human). Signed by [andres] 2026-08-21.
  *Riders of [andres]:*
  (a) The mechanical translation is executed **now**, in its own commit
      (`docs: translate process layer to English`), **with no semantic changes**. Any ambiguity that
      could alter meaning is **escalated, never resolved silently**.
  (b) For `DECISIONES.md` and `IDIOMS.md` the translation **does not violate append-only**: the whole
      file is translated with a header note (date, *"semantic content unchanged"*), and the Spanish
      original is preserved in git history as the signed record.
  (c) **New entries are written directly in English** from this decision onward.
  ⚠️ Operational consequence (I-008): the three subagent definitions under `.claude/agents/` freeze
  at session start. After translating them, the session **must be restarted** before relying on them.

---

## Riders on wave 1, signed by [andres] 2026-08-21

- **On the `blank` theme (T-103 / T-105).** `blank` and the `extra.drupal-site-template` block are
  **kept until unit 002**. T-103 deletes the three `_comment` arrays and `GET-STARTED.md`, but **not**
  the `extra` block. **A gate A with a permanent red is NOT accepted** — a tolerated red degrades the
  gate and violates non-negotiable rule 9. Therefore the affected check is **adjusted to the
  specification in force for unit 001** (`blank` and the `extra` block are expected to be PRESENT,
  with a reference to this rider); if adjusting it required touching a protected file, it is recorded
  as an **explicit, documented skip** in the gate runner. In both cases the unit-002 task that
  performs the coordinated change (delete `extra` + `require` the theme + `install:` + `system.theme`,
  in **one atomic commit**) **owns that debt** and is the one that reverts the adjustment or skip.
  **Debt = a task with an owner and an exit gate, never a known red light.**

- **On the specification corrections found by the `tester` in wave 1, 2026-08-21.** All three adopted:
  1. **T-209** is specified as *"`CI_ALLOW_DEV` is not **defined** in any versioned file"*, never
     *"not mentioned"* — the string legitimately lives in `tests/src/Kernel/RequirementsTest.php:55`,
     which T-406 forbids modifying.
  2. **T-103** must account for the **three** `_comment` occurrences in `composer.json`.
  3. **`ValidationTest.php`** is added to the set of kit files watched by the gate.
