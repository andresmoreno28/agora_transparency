# Ágora · Unidad 001 — Fundación · Tareas

> **Append-only.** Una tarea firmada `[✓ fecha]` no se renumera ni se reescribe.
> Ninguna tarea arranca sin gate B de la wave anterior. Producido [ejecutor] 2026-08-20.
>
> 🔒 **LA UNIDAD ESTÁ BLOQUEADA**: T-101 no puede empezar sin D-007 y D-011 firmadas.

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
- [ ] **T-104** · `.gitignore` y `.gitattributes` definitivos desde los `.example`; borrar los `.example`.
      *Éxito:* `.gitattributes` contiene los `export-ignore` de `/tests`, `/.github`,
      `/.gitlab-ci.yml`, `/.tugboat`.
- [ ] **T-105** · `recipe.yml` propio: `name`, `description`, `type: Site`, recetas base heredadas.
      *Éxito:* `type: Site` exacto; el fichero parsea como YAML válido.
- [ ] **T-106** · Resolver el enfoque del tema según D-008 (generado vía `generate-theme` vs
      versionado). *Éxito:* la decisión está aplicada y `recipe.yml` instala el tema correcto.
      **Bloqueada por D-008.**

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

**Gate A wave 4**
```bash
ddev drush sql:drop --yes && ddev drush site:install --yes   # y comprobar el selector
ddev exec vendor/bin/phpunit --testdox tests/                 # nº de tests y assertions
```
**Gate B wave 4** 👤 · Andrés firma el cierre de la unidad 001.
Firma aquí: `[ ]`

---

## Bloqueos activos

| Bloqueo | Impide | Quién resuelve |
|---|---|---|
| D-011 arquitectura de recetas | T-101 y toda la unidad | 👤 Andrés |
| D-007 machine name | T-102 | 👤 Andrés |
| D-008 enfoque del tema | T-106 | 👤 Andrés |
| D-009 reparto de tests | T-206 | 👤 Andrés |
