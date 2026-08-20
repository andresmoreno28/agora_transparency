# Ágora · Idioms y lecciones (append-only)

> Gotchas y lecciones del proyecto. Se promueven al cerrar cada unidad. No confundir con ADRs/decisiones.

- I-001 · Este proyecto construye sobre tecnología joven (Drupal CMS, Canvas, site templates): toda
  research caduca. Cualquier afirmación sobre el estado del arte lleva fecha y fuente, y se re-verifica
  antes de construir encima (prior-is-not-disk).
- I-002 · El requisito "sin releases inestables ni parches" del marketplace convierte cada dependencia
  en una decisión, no en un `composer require` casual.
- I-003 · El CI de instalación corre SIN claves de IA: cualquier feature de IA que rompa la instalación
  en ausencia de API key es un bug de diseño, no un detalle.
- I-004 · El repositorio de un site template **ES la receta**: `recipe.yml` en la raíz con `type: Site`
  (case-sensitive). No hay `recipes/` con sub-recetas locales, y el tema se **genera** vía
  `site_template_helper`, no se versiona. Verificado 2026-08-20 contra `drupal_cms_site_template_base`.
- I-005 · El starter kit **no tiene releases estables, solo ramas** — y eso **no** viola la política
  de dependencias: se copia como andamiaje, nunca se declara en `require`. No entra en el SBOM.
  El invariante `no-unstable-deps` debe excluirlo explícitamente para no dar un falso positivo.
- I-006 · `www.drupal.org` puede estar bloqueado, pero **`updates.drupal.org` y `git.drupalcode.org`
  suelen responder**. La release history oficial (`/release-history/<proyecto>/current`) da versión
  estable, `<core_compatibility>` y `<security covered="1">` — es la fuente correcta para `sbom-check`,
  y evita dar por "no verificable" algo que sí lo es.
- I-007 · Un `exit 0` sin counts no prueba nada: una suite sin tests, un grep sin ficheros y un axe que
  no cargó la página devuelven todos 0. Exigir siempre nº de elementos analizados.
- I-008 · En este harness, los **subagentes de `.claude/agents/` se congelan al iniciar la sesión**:
  no solo su registro, también **el contenido de su definición**. Editar un `agents/*.md` a mitad de
  sesión NO tiene efecto — el subagente sigue corriendo con el system prompt viejo. Verificado
  2026-08-20: tras reescribir `orquestador.md`, el agent seguía sin conocer los hechos nuevos y
  respondía según la versión anterior. Las **skills y los comandos SÍ se recargan en caliente**.
  → Tras crear o editar agents: **reiniciar la sesión** antes de fiarse de ellos.
- I-009 · Un subagente con contexto limpio no ve la conversación: su fichero de definición debe ser
  **autosuficiente**. Si un hecho verificado (estructura del repo, versiones del SBOM, falsos
  positivos conocidos) no está escrito en su `.md`, para él no existe — y rellenará el hueco con la
  regla genérica, que es justo como reaparecen los falsos positivos ya descartados.
