# Ágora · Unidad 001 — Fundación · Tareas

> **Append-only.** Una tarea firmada `[✓ fecha]` no se renumera ni se reescribe.
> Ninguna tarea arranca sin gate B de la wave anterior. Producido [ejecutor] 2026-08-20.
>
> ✅ **DESBLOQUEADA 2026-08-21**: D-007, D-008, D-011, D-012, D-013 y D-014 firmadas por [andres].
> T-101…T-105 ejecutables. **T-106 diferida** a la unidad 002 (D-014=B). D-009 sigue abierta → T-206.
> Ver la tabla "Bloqueos activos" al final para el estado vigente.

Leyenda: `[ ]` pendiente · `[~]` en curso · `[✓ AAAA-MM-DD]` firmada · 👤 requiere al humano

---

## Wave 1 · Esqueleto e identidad

- [ ] **T-101** · Copiar la rama `1.x` de `drupal_cms_site_template_base` al repo, sin su historia git.
      *Éxito:* existen en raíz `recipe.yml`, `composer.json`, `.gitlab-ci.yml`, `.tugboat/`,
      `.github/`, `tests/`. **Bloqueada por D-011.**
- [ ] **T-102** · Renombrar el paquete a `drupal/<machine_name>` en `composer.json` y ajustar
      `description`. *Éxito:* `composer validate --strict` exit 0. **Bloqueada por D-007.**
- [ ] **T-103** · Desnudar el andamiaje: borrar el array `_comment` de `composer.json`, borrar
      `GET-STARTED.md`, sustituir `screenshot.webp` por uno propio provisional.
      *Éxito:* `grep -c '_comment' composer.json` = 0; `GET-STARTED.md` no existe.
      > **Nota (rider [andres] 2026-08-21):** *"three `_comment` occurrences, not one; the
      > `extra.drupal-site-template` block is NOT deleted in unit 001 — see the wave 1 rider on the
      > `blank` theme."*
- [ ] **T-104** · `.gitignore` y `.gitattributes` definitivos desde los `.example`; borrar los `.example`.
      *Éxito:* `.gitattributes` contiene los `export-ignore` de `/tests`, `/.github`,
      `/.gitlab-ci.yml`, `/.tugboat`.
      > **Nota (rider [andres] 2026-08-21):** *"`.gitattributes` must `export-ignore` `/CLAUDE.md`,
      > `/.claude`, `/specs` (D-015.2) and must NOT export-ignore `AGENTS.md` (D-015.1)."*
- [ ] **T-105** · `recipe.yml` propio: `name`, `description`, `type: Site`, recetas base heredadas.
      *Éxito:* `type: Site` exacto; el fichero parsea como YAML válido.
- [ ] **T-106** · Resolver el enfoque del tema según D-008 (generado vía `generate-theme` vs
      versionado). *Éxito:* la decisión está aplicada y `recipe.yml` instala el tema correcto.
      **Bloqueada por D-008.**
- [ ] **T-107** · Firmas D-007, D-008, D-011, D-012, D-013, D-014 + enmiendas de `plan.md` §2 y
      `CLAUDE.md` §Estructura, **en un solo commit** (rider D-011a + rider D-014b).
      *Éxito:* `git show --stat HEAD` lista exactamente 3 ficheros;
      `grep -c 'recipes/agora_base' specs/000-proyecto/plan.md` = 0;
      `grep -c 'D-014' specs/000-proyecto/DECISIONES.md` ≥ 1.
- [ ] **T-108** · Append I-011…I-017 a `IDIOMS.md`.
      *Éxito:* `grep -cE '^- I-01[1-7]' specs/000-proyecto/IDIOMS.md` = 7; ninguna línea previa eliminada.
- [ ] **T-109** · Research fechada `specs/001-fundacion/research/2026-08-21-flujo-tema-y-marketplace.md`.
      *Éxito:* ≥ 6 URLs de origen citadas y las 4 conclusiones registradas.
- [ ] **T-110** · 🔒 **T-106 se declara DIFERIDA** a la unidad 002: se redefine allí contra
      **D-014=B** (integrar el tema `drupal/agora_theme` como dependencia, no generarlo en este
      repo). *Éxito:* la tabla de bloqueos refleja el diferimiento y la redefinición pendiente.
- [ ] **T-111** · Fill in the "Template-specific notes" section of `AGENTS.md` in English, with the
      audience header required by D-015.1. *Success:* the section is no longer empty; it states that
      `CLAUDE.md` governs template development and `AGENTS.md` targets sites built with the template.
- [ ] **T-112** · `README.md` gains the "Development process" section required by D-015.4.
      *Success:* the section exists, in English, and links to `specs/`.
- [ ] **T-113** · Mechanical translation of the process layer to English (D-017): `CLAUDE.md`,
      `.claude/agents/*` (3), `.claude/commands/*` (3), `.claude/skills/*` (7), `specs/`. Own commit,
      no semantic changes; ambiguities escalated. *Success:* zero semantic diffs reported; a restart
      of the session after translating `.claude/agents/` (I-008).
- [ ] **T-114** · 🔒 The **definitive `screenshot.webp` is DEFERRED to unit 003**, where the demo
      content it must depict exists. T-103 shipped a **provisional placeholder** instead: a 632×363
      WebP (the starter kit's own dimensions, therefore known-compatible with the installer), neutral,
      hatched, and labelled *"PLACEHOLDER — NOT A SCREENSHOT OF A REAL SITE"*, rendered with DejaVu
      Sans (Bitstream Vera / DejaVu licence, redistributable). It deliberately does **not** imitate a
      site: fabricating a screenshot of a site that does not exist would misrepresent the template to
      anyone browsing the installer. This task owns the replacement.
      *Success:* `screenshot.webp` is a real capture of the installed demo site, WebP, at the
      repository root; `shasum -a 256 screenshot.webp` differs from
      `98363dd5a77e8374d33666d2bbf905f15229a7c1aca9e82fc7c37542b3e02f1c` (the placeholder);
      the blockers table records this debt as closed. Signed off visually by 👤 [andres].

**Gate A wave 1**
```bash
composer validate --strict
python3 -c "import yaml,sys; d=yaml.safe_load(open('recipe.yml')); assert d['type']=='Site', d.get('type'); print('type OK')"
grep -c '_comment' composer.json          # esperado: 0
test ! -f GET-STARTED.md && echo "kit docs limpias"
```
**Gate B wave 1** 👤 · Andrés confirma nombre de paquete, descripción visible e identidad.
Firma aquí: `[ ]`

---

## Wave 2 · Entorno y CI

- [ ] **T-201** · Configuración DDEV reproducible (≥ 1.25.0), documentada en el README.
      *Éxito:* `ddev start` desde cero en una máquina limpia, sin pasos manuales.
- [ ] **T-202** · Revisar `.gitlab-ci.yml`: mantener el include de `gitlab_templates`, fijar solo
      las variables necesarias. *Éxito:* no se define ningún job a mano.
- [ ] **T-203** · Leer `include.drupalci.variables.yml` y documentar en el README qué jobs quedan
      activos (phpcs, phpstan, cspell, eslint, stylelint, phpunit). *Éxito:* lista real, no supuesta.
- [ ] **T-204** · Crear `.cspell-project-words.txt` con el vocabulario del proyecto.
      *Éxito:* el job de cspell pasa sin desactivarlo.
- [ ] **T-205** · Primer pipeline verde en el repo de trabajo. *Éxito:* nº de jobs ejecutados > 0 y
      todos en verde. **Un pipeline sin jobs NO es verde.**
- [ ] **T-206** · Decidir y aplicar D-009: qué corre en drupalcode y qué en GitHub Actions.
      **Bloqueada por D-009.**
- [ ] **T-207** · Sustituir el supuesto "`ddev start` en el repo" por el flujo verificado: montar
      Drupal aparte y añadir el template como *path repository*, siguiendo `.github/workflows/phpunit.yml`
      del kit (`ddev config --project-type=drupal11 --docroot=web` → `ddev composer create-project
      --no-install drupal/recommended-project` → `ddev composer repository add source path source` →
      `ddev composer require "<paquete>:@dev"`, con `COMPOSER_MIRROR_PATH_REPOS=1`).
      *Éxito:* un comando reproduce el entorno desde cero; `ddev exec drush status` →
      `Drupal bootstrap : Successful`; existe `./recipes/agora_transparency`.
- [ ] **T-208** · Fijar DDEV ≥ 1.25.0 y **versionar `.ddev/config.yaml`** (hoy `.gitignore` ignora
      `/.ddev/`, lo que hace inalcanzable el criterio de T-201).
      *Éxito:* `git ls-files .ddev/config.yaml | wc -l` = 1; requisito documentado en el README.
- [ ] **T-209** · Invariante: `CI_ALLOW_DEV` no se define en ningún fichero versionado.
      *Éxito:* 0 coincidencias, imprimiendo nº de ficheros escaneados (> 0).
      > **Nota (rider [andres] 2026-08-21):** *"specified as 'not DEFINED', never 'not mentioned' —
      > see I-018."*

**Gate A wave 2**
```bash
ddev start && ddev drush status          # esperado: Drupal bootstrap = Successful
# En GitLab: el pipeline del último commit, en verde, con el nº de jobs a la vista
```
**Gate B wave 2** 👤 · Andrés confirma el reparto de tests entre drupalcode y GitHub.
Firma aquí: `[ ]`

---

## Wave 3 · Invariantes (paralelizable con wave 2 — ficheros disjuntos)

- [ ] **T-301** · `tests/bin/no-unstable-deps` según spec de `plan.md` §6.
      *Éxito:* detecta un `-beta` inyectado a propósito y **no** marca el starter kit.
- [ ] **T-302** · `tests/bin/no-patches`. *Éxito:* detecta una sección `patches` inyectada.
- [ ] **T-303** · `tests/bin/no-secrets` sobre todo el repo salvo `.git/`.
      *Éxito:* detecta una clave falsa inyectada en `config/` y otra en `content/`.
- [ ] **T-304** · `tests/bin/sbom-check` contra `updates.drupal.org` (método en research §10.4).
      *Éxito:* exige estable + `<security covered="1">` + línea en `DECISIONES.md`; falla si falta una.
- [ ] **T-305** · Los cuatro imprimen ámbito, nº de ficheros escaneados y nº de hallazgos.
      *Éxito:* ninguno reporta "0 ficheros escaneados".
- [ ] **T-306** · **Enmienda del método de T-304** (`sbom-check`): el endpoint devuelve **HTTP 200
      con cuerpo `<error>`** para proyectos inexistentes → un `curl -f` da falso verde. Debe:
      (a) comprobar red al arrancar y **fallar ruidosamente si no hay** — "skip" prohibido;
      (b) parsear el XML; (c) exigir `<title>` y ausencia de `<error>`; (d) tomar como estable la
      primera release sin `dev|alpha|beta|rc`; (e) exigir `<security covered="1">` en esa release;
      (f) comprobar `<core_compatibility>`; (g) exigir línea `D-NNN` en `DECISIONES.md` por cada
      `drupal/*` de `require`.
      *Éxito:* con el `require` real → exit 0 e imprime
      `N proyectos consultados · N con cobertura · 0 hallazgos`; con un proyecto inexistente
      inyectado → exit 1; con la red cortada (`https_proxy=http://127.0.0.1:1`) → **exit 1**,
      nunca exit 0.
- [ ] **T-307** · `tests/bin/no-code-in-template`: espeja localmente el assert de `RequirementsTest`.
      *Éxito:* imprime `N ficheros escaneados · 0 ficheros *.info.yml`, N > 0; detecta un
      `themes/x/x.info.yml` inyectado; árbol limpio tras revertir.

**Gate A wave 3**
```bash
for s in no-unstable-deps no-patches no-secrets sbom-check; do
  echo "── $s"; tests/bin/$s; echo "exit=$?"
done
# Cada uno: exit 0 + nº de ficheros escaneados > 0 + nº de hallazgos impreso
```
Cada script debe probarse además **con un caso sucio inyectado** (y revertido): si no falla con
basura dentro, no sirve. Silenciar un invariante para pasar = 🔴 automático.
**Gate B wave 3** 👤 · No requiere firma; entra en el veredicto del cierre de unidad.

---

## Wave 4 · Install smoke y cierre

- [ ] **T-401** · Install smoke en limpio: `sql:drop` + reinstalación, verificando que el template
      aparece en el selector. *Éxito:* captura o salida que lo demuestre.
- [ ] **T-402** · Extender `InstallTest`/`ValidationTest` con las rutas clave de Ágora.
      *Éxito:* nº de tests y assertions reportados, > 0.
- [ ] **T-403** · README del proyecto **en inglés** (docs públicas en inglés, D-005): qué es, cómo se
      instala, qué trae.
- [ ] **T-404** · Auditoría del `orquestador` (solo lectura): estándares, SBOM, licencias, requisitos
      del marketplace. *Éxito:* veredicto sin 🔴 abiertos.
- [ ] **T-405** · Promover a `IDIOMS.md` las lecciones de la unidad.
- [ ] **T-406** · Verificar que `InstallTest`, `ValidationTest` y `RequirementsTest` pasan **sin
      modificarse**. *Éxito:* 0 líneas eliminadas en esos 3 ficheros; salida de phpunit con nº de
      tests **y** assertions.

**Gate A wave 4**
```bash
ddev drush sql:drop --yes && ddev drush site:install --yes   # y comprobar el selector
ddev exec vendor/bin/phpunit --testdox tests/                 # nº de tests y assertions
```
**Gate B wave 4** 👤 · Andrés firma el cierre de la unidad 001.
Firma aquí: `[ ]`

---

## Bloqueos activos

> Tabla de **estado**, no de tareas firmadas: se reescribe en cada actualización.
> Última actualización: 2026-08-21, tras la tanda de firmas D-015…D-017.

| Bloqueo | Estado | Impide | Quién resuelve |
|---|---|---|---|
| D-011 arquitectura de recetas | ✅ FIRMADA 2026-08-21 · opción A (una sola receta en raíz) | — desbloquea T-101…T-105 | — |
| D-007 machine name | ✅ FIRMADA 2026-08-21 · `agora_transparency` | — desbloquea T-102 | — |
| D-008 enfoque del tema | ✅ FIRMADA 2026-08-21 · opción A; rider suspendido y **subsumida por D-014** | — | — |
| D-012 vía de publicación | ✅ FIRMADA 2026-08-21 · opción C (community primero; marketplace = 007-bis, no bloqueante) | — | — |
| D-013 provider de IA | ✅ FIRMADA 2026-08-21 · `ai` ^1.4 duro + `ai_provider_openai` recomendado | — | — |
| D-014 dónde vive el tema | ✅ FIRMADA 2026-08-21 · opción B (proyecto aparte `drupal/agora_theme`) | **T-106 DIFERIDA a la unidad 002**, donde se redefine contra D-014=B (ver T-110) | — |
| D-015 artefactos de IA en el repo público | ✅ FIRMADA 2026-08-21 · `AGENTS.md` es producto; `CLAUDE.md`/`.claude/`/`specs/` visibles y `export-ignore`d | — desbloquea T-111, T-112 y la nota de T-104 | — |
| D-016 flujo de repositorio | ✅ FIRMADA 2026-08-21 · D-002 CONFIRMADA (drupalcode canónico; mirror GitHub read-only, misma historia) | — el mirror se monta en la unidad 007 | — |
| D-017 idioma | ✅ FIRMADA 2026-08-21 · repo entero en inglés, capa de proceso incluida; enmienda D-005 y la regla 6 | — desbloquea T-113 | — |
| D-009 reparto de tests | 🔴 ABIERTA | T-206 | 👤 Andrés |
| D-010 alcance del contenido demo v1 | 🔴 ABIERTA | unidad 003 | 👤 Andrés |
| T-106 enfoque del tema | ⏸️ DIFERIDA a la unidad 002 (ver T-110) | — | — |
| `screenshot.webp` definitivo | ⏸️ DIFERIDA a la unidad 003 (ver T-114) · placeholder provisional en su lugar, deliberadamente no imita un sitio real | — | — |
