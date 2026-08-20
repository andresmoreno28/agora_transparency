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
