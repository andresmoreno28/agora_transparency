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
- I-010 · Una allowlist de permisos con **glob final** sobre un comando que acepta flags de salida
  equivale a escritura arbitraria de ficheros: `Bash(curl -s https://host/path/*)` autoriza
  `curl -s https://host/path/x -o ~/.zshrc`, y `Bash(git diff:*)` autoriza `git diff --output=<fichero>`.
  Regla: comandos exactos, nunca `*` como token final, y **jamás allowlistar un directorio cuyo
  contenido todavía no existe** (`tests/bin/*` con la carpeta vacía autoriza scripts que aún no se
  han escrito ni revisado). Detectado por revisión de seguridad automática, 2026-08-20.
- I-011 · Enmienda a I-006: en este entorno **Bash SÍ tiene red** (verificado 2026-08-21: `curl` a
  `updates.drupal.org`, `git.drupalcode.org` y `www.drupal.org` → exit 0). Pero **varía dentro de la
  misma sesión**: el mismo `curl` falló con exit 6 (DNS) veinte minutos antes de funcionar. Se
  comprueba con un comando al empezar, nunca se asume — ni que la hay, ni que no. Declarar "no hay
  red" sin probarlo produce planes tan malos como darla por hecha.
- I-012 · `www.drupal.org/project/<X>` devuelve **302 hacia new.drupal.org para cualquier cadena**,
  incluida una inexistente: un 302 NO prueba que un machine name esté libre. El oráculo válido es
  `git.drupalcode.org/api/v4/projects/project%2F<X>` (200 = ocupado, 404 = libre).
- I-013 · `updates.drupal.org/release-history/<X>/current` devuelve **HTTP 200 con cuerpo `<error>`**
  para proyectos inexistentes. `curl -f` o cualquier chequeo por código de estado da falso verde:
  hay que parsear el XML y exigir `<title>` + al menos una `<release>`.
- I-014 · Un site template **no puede contener código propio**: `RequirementsTest` del starter kit
  exige **0 ficheros `*.info.yml`** en el paquete, que además se instala en `./recipes/<name>` —
  fuera del docroot, donde `RecursiveExtensionFilterCallback` ni recurre (solo `profiles/`,
  `modules/`, `themes/` del root). Temas y módulos se **declaran en `require`**. Corrige I-004 en su
  parte de "el tema se genera": se genera en el **sitio de trabajo**, no en el repo, es andamiaje de
  desarrollo, y el bloque `extra.drupal-site-template` se borra antes de publicar.
- I-015 · `RequirementsTest` respeta la variable `CI_ALLOW_DEV`: si está definida en CI, las
  dependencias listadas **se saltan** la comprobación de versiones pineadas/dev. Es un debilitamiento
  de gate por diseño. En Ágora **no se define nunca**, y hay invariante que lo verifica (T-209).
- I-016 · Una premisa alarmante no verificada envenena la planificación tanto como una falsa
  tranquilizadora: "el marketplace es DCP-only y cuesta 395 $" circuló dos unidades como restricción
  dura y resultó **falso** (gratis está abierto a cualquier individuo; la cuota decía "none for pilot
  and MVP"). Verificar en origen antes de dejar que una restricción externa moldee el alcance.
- I-017 · La licencia y la privacidad son restricciones **estructurales**, no acabados: la tipografía
  OFL auto-alojada son ficheros que la configuración no transporta, y una CDN de fuentes es pasivo
  RGPD en sector público de la UE. Eso, y no la estética, es lo que obligó a que el tema sea un
  proyecto aparte (D-014).
