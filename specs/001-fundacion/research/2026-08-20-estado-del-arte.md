# Ágora · Estado del arte — 2026-08-20

> Research del reconciliation pass del DISPATCH-00 (unidad 001-fundación).
> I-001 aplica: **toda afirmación de aquí caduca**. Re-verificar antes de construir encima.
> Autor: [ejecutor]. Fecha de captura: **2026-08-20**.

## 0 · Nivel de confianza de cada bloque (leer antes que nada)

Durante esta sesión **`www.drupal.org` no fue alcanzable** (fallo de resolución DNS desde el entorno:
`getaddrinfo ENOTFOUND www.drupal.org`). Sí fue alcanzable `git.drupalcode.org` (API GitLab + raw) y
`packagist.org`. En consecuencia:

| Bloque | Fuente | Confianza |
|---|---|---|
| Estructura real del Starter Kit | `git.drupalcode.org` API/raw, leído directo | **ALTA — verificado en origen** |
| Versiones y ramas | API GitLab de tags/branches + Packagist | **ALTA — verificado en origen** |
| Requisitos del marketplace | Solo *snippets* de buscador; páginas no abiertas | **BAJA — SIN VERIFICAR** |
| Machine name `agora` disponible | No comprobable sin drupal.org | **NO VERIFICADO** |
| SBOM (cobertura de seguridad por módulo) | `updates.drupal.org` (API oficial), leída directa | **ALTA — verificado en origen** (ver §10) |

Lo marcado BAJA/NO VERIFICADO **no puede usarse para cerrar decisiones**. Requiere una segunda
pasada con acceso a drupal.org.

---

## 1 · Drupal CMS — versión estable actual

Tags del repo `project/drupal_cms` (API GitLab, 2026-08-20):

| Versión | Fecha |
|---|---|
| **2.1.3** | **2026-06-01** ← última estable |
| 2.1.2 | 2026-05-21 |
| 2.1.1 | 2026-04-09 |
| 2.1.0 | 2026-03-20 |
| 2.0.0 | 2026-01-27 |

→ **Línea estable vigente: Drupal CMS 2.1.3.** La rama `2.x` del starter kit exige `drupal/core ^11.4`.

Fuente: `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms/repository/tags`

## 2 · Drupal Canvas

Confirmado indirectamente pero de forma sólida: **Canvas es el page builder asumido por el starter kit**.

- La rama `2.x` del starter kit requiere `drupal/canvas ^1.2` → **Canvas tiene línea estable 1.x**.
  Esto es relevante para la no-negociable nº1: **no obliga a releases inestables**.
- `recipe.yml` (1.x) contiene un bloque extenso de acciones `canvas.component.block.*` / `canvas.component.sdc.*`
  con `disable: []`, es decir: el template controla qué componentes de Canvas aparecen en la UI.
- El contenido demo del kit vive como `content/canvas_page/<uuid>.yml` → **las páginas demo son entidades Canvas**.
- La portada por defecto se fija con `system.site: page.front: '/home'` apuntando a una landing Canvas en blanco.

⚠️ Nota terminológica: el material de marketing del marketplace habla de *"XB-compatible themes"*
(Experience Builder). XB es el nombre anterior de Canvas. Conviene confirmarlo al abrir drupal.org.

## 3 · El Starter Kit (`drupal_cms_site_template_base`) — ESTRUCTURA REAL

Proyecto: `https://git.drupalcode.org/project/drupal_cms_site_template_base`
Packagist: tipo de paquete **`drupal-recipe`**.

### 3.1 · Hallazgo mayor: NO tiene releases estables

`.../repository/tags` devuelve **lista vacía**. Solo existen ramas:

| Rama | Último commit |
|---|---|
| `1.x` | 2026-05-22 |
| `2.x` | 2026-08-18 (hace 2 días) |
| `3568170-theme-dev` | 2026-04-28 |

**Esto NO viola la no-negociable nº1.** El starter kit es un **andamiaje que se COPIA**, no una
dependencia que se declara: `GET-STARTED.md` describe el flujo como *"copying the starter project"* y
`composer.json` instruye literalmente *"Change the 'name' field to your own package name"*.
Nunca aparece en el `require` de Ágora → no entra en el SBOM. Registrar como idiom para no
levantar una falsa alarma en el invariante `no-unstable-deps`.

### 3.2 · Las dos ramas son cosas DISTINTAS

**`1.x` = el starter kit real para desarrolladores** (es el que trae la documentación):

```
.eslintrc.json
.gitattributes.example        → renombrar a .gitattributes
.gitignore.example            → renombrar a .gitignore
.gitlab-ci.yml                → include de gitlab_templates
.github/workflows/phpunit.yml
.tugboat/config.yml
.tugboat/tugboat-settings.txt
AGENTS.md
GET-STARTED.md                → export-ignore; borrar antes de publicar
LICENSE.txt
README.md
composer.json                 → type: drupal-recipe
recipe.yml                    → type: Site  (OBLIGATORIO en la RAÍZ)
recommended.yml               → lista curada para Project Browser
screenshot.webp
content/canvas_page/<uuid>.yml
tests/src/Functional/InstallTest.php
tests/src/Functional/ValidationTest.php
tests/src/Kernel/RequirementsTest.php
```

**`2.x` = un template ya exportado**, sin andamiaje de desarrollo: tiene `config/` (≈100 YAML de
config exportada) y `content/{file,menu_link_content,node}`, pero **no** tiene `.gitlab-ci.yml`,
ni `.github/`, ni `.tugboat/`, ni `GET-STARTED.md` (HTTP 404 confirmado).

→ **Para Ágora, la rama a copiar es `1.x`** (es la que trae CI, Tugboat y documentación).
`2.x` sirve como **referencia de cómo queda un template tras `drush site:export`**.

### 3.3 · Restricciones duras que impone el propio kit

De `GET-STARTED.md` y de los comentarios de `composer.json` / `recipe.yml`:

1. `recipe.yml` **debe llamarse así y estar en la raíz**.
2. `type: Site` **exacto y case-sensitive**. Sin eso no aparece en el instalador.
3. `type` de composer **debe ser `drupal-recipe`**.
4. Nombre de paquete: **debe empezar por `drupal/`** y contener solo letras, números y guiones bajos.
5. **Prohibido parchear dependencias** con plugins composer-patches.
6. **Prohibido pinear versiones**: usar operadores (`^1`), no versiones exactas.
7. Licencia: `GPL-2.0-or-later` salvo razón específica — *"which license you choose may affect your
   ability to publish the site template"*.
8. Hay que tener **derechos legales sobre todo el contenido incluido**.

→ Los puntos 5, 6, 7 y 8 **confirman literalmente** las no-negociables 1 y 2 de CLAUDE.md y el §4 de
`plan.md`. Esa parte del plan **NO diverge**.

### 3.4 · Regla de composición de recetas (CRÍTICA para plan.md §2)

Comentario textual de `recipe.yml`:

> *"Recipes to apply before this one. None of these should be site templates themselves; a site
> template can be built on any number of smaller recipes, but you shouldn't build a site template on
> top of another site template (or combination of site templates)."*

Doble lectura, ambas importantes:
- ✅ **Sí se permite** componer un site template a partir de N recetas más pequeñas → la idea modular
  de `plan.md` §2 (`agora_base`, `agora_foi`…) **es legítima conceptualmente**.
- ❌ **Pero** el starter kit **no tiene un directorio `recipes/`**. Las recetas que compone
  (`drupal_cms_admin_ui`, `drupal_cms_media`, `easy_email_express`…) son **paquetes composer externos**,
  declarados en `require` y referidos por nombre en la clave `recipes:`. Las de core van por ruta
  (`core/recipes/administrator_role`).

**No hay evidencia en el kit de sub-recetas locales dentro del mismo repositorio.** Es el punto que
dispara la regla de parada nº2 del DISPATCH (ver §7).

### 3.5 · El tema se GENERA, no se escribe a mano (de partida)

`composer.json` → `extra.drupal-site-template.generate-theme`:

```json
"generate-theme": {
  "info": { "name": "Blank", "regions": {"header","content","footer"},
            "libraries-override": {"core/normalize": false} },
  "from": false,
  "name": "blank"
}
```

`drupal/site_template_helper` (plugin composer, `allow-plugins: true`) genera el tema `blank` en el
sitio de trabajo. `from: false` = **no hereda de ningún tema base**. El bloque `extra.drupal-site-template`
**lo elimina automáticamente `drush site:export`** y debe borrarse antes de publicar.

→ Impacto en `plan.md`: `themes/agora_theme/` **no es una carpeta del repo del template** en el flujo
por defecto. Alimenta la decisión D-008.

### 3.6 · Project Browser: `recommended.yml`

Permite publicar una lista curada de add-ons recomendados, servida por **permalink de la API de GitLab**.
Aviso literal del fichero:

> *"It is STRONGLY recommended that this file ONLY list projects that have stable, supported releases."*

→ Alineado con la política SBOM (D-004). Ejemplos citados por el propio kit: `project/byte` y
`project/drupal_cms` (`recipes/drupal_cms_starter/recommended.yml`).

## 4 · CI: qué trae realmente

### 4.1 · GitLab CI (`drupalcode`)

`.gitlab-ci.yml` del kit **no define jobs propios**; es un include de los `gitlab_templates` de la DA:

```yaml
include:
  - project: $_GITLAB_TEMPLATES_REPO
    ref: $_GITLAB_TEMPLATES_REF
    file:
      - '/includes/include.drupalci.main.yml'
      - '/includes/include.drupalci.variables.yml'
      - '/includes/include.drupalci.workflows.yml'
```

Los jobs se controlan por **variables** (`SKIP_ESLINT: '1'`, `OPT_IN_TEST_NEXT_MAJOR: '1'`,
`_CURL_TEMPLATES_REF`…), documentadas en
`https://git.drupalcode.org/project/gitlab_templates/-/blob/main/includes/include.drupalci.variables.yml`.
Doc general: `https://project.pages.drupalcode.org/gitlab_templates/`

→ **Confirma D-006**: el pipeline de drupalcode ES el gate. La lista exacta de jobs (phpcs, phpstan,
cspell, eslint, stylelint, phpunit) **queda pendiente de leer el fichero de variables**: no se
inventa aquí.

### 4.2 · GitHub Actions

Un único workflow: `.github/workflows/phpunit.yml`. El mirror de GitHub **no es solo portfolio**:
el kit ya lo usa para PHPUnit. Alimenta D-009 (dónde corren los tests visuales).

### 4.3 · Tests que el kit ya trae

- `tests/src/Functional/InstallTest.php` — instalabilidad en limpio
- `tests/src/Functional/ValidationTest.php` — validación de la receta
- `tests/src/Kernel/RequirementsTest.php` — requisitos

→ **El "install smoke" de CLAUDE.md ya existe de serie.** No hay que inventarlo: se extiende.

### 4.4 · `.gitattributes` — patrón `export-ignore`

El kit excluye del paquete descargable: `/.cspell-project-words.txt`, `/GET-STARTED.md`, `/.github`,
`/.gitlab-ci.yml`, `/tests`, `/.tugboat`.

→ Dato colateral: la existencia de `/.cspell-project-words.txt` **confirma que hay job de cspell** en
el pipeline de la DA.

## 5 · `drush site:export` (Drupal CMS Helper)

Confirmado por `GET-STARTED.md`:
- Flujo: construir el sitio en la UI → `ddev drush site:export` → el sitio se convierte en receta.
- Elimina automáticamente el bloque `extra.drupal-site-template` de `composer.json`.
- Añade automáticamente al `require` los módulos/temas/recetas de los que depende el template.
- Test recomendado: exportar → `ddev drush sql:drop --yes` → reinstalar y comprobar que el template
  aparece en el paso de selección del instalador.
- Entorno recomendado: **DDEV ≥ 1.25.0** (confirma D-003).

Limitaciones conocidas: **no verificadas** (requieren la issue queue en drupal.org).

## 6 · Marketplace — ⚠️ SIN VERIFICAR, solo snippets de buscador

**Nada de este bloque debe tratarse como firme.** No se abrió ninguna página de drupal.org.

Señales recogidas que, de confirmarse, **divergen de `plan.md` §4**:

1. **Pilot limitado a Drupal Certified Partners (DCP).** El marketplace habría arrancado como piloto
   restringido a DCPs, con expansión posterior. `plan.md` §4 **no contempla** ninguna restricción de
   elegibilidad. → Si se confirma, es **bloqueante de la meta declarada en CLAUDE.md**.
2. **Cuotas: 395 $ por listing nuevo + 250 $ anuales de revisión.** `plan.md` §4 no menciona coste.
   Choca además con *"v1 es la plantilla gratuita insignia"*.
3. **Dos vías separadas**: *Marketplace* (revisado, de pago, DCP-only en piloto) vs *Community*
   (proyecto general en Drupal.org, **publicable directamente sin revisión**). D-002 ya eligió
   "proyecto general en drupalcode" = **vía Community**, que es compatible y no bloqueante.
4. Estándares citados para marketplace: seguridad, **accesibilidad WCAG 2.2 AA**, rendimiento,
   calidad de código, documentación estructurada, compromiso de mantenimiento y soporte.
   → Coherente con `plan.md` §4 y con la no-negociable nº4.
5. Requisito técnico citado: *"built for Drupal CMS, using the Recipes schema, demo content, and
   XB-compatible themes"*. → Coherente con la arquitectura elegida.

Fuentes (snippets, no abiertas):
`https://www.drupal.org/about/starshot/marketplace-initiative` ·
`https://www.drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates` ·
`https://events.drupal.org/chicago2026/session/launching-drupal-site-template-marketplace` ·
`https://www.thedroptimes.com/67494/drupal-clarifies-marketplace-and-community-pathways-site-templates`

## 7 · Divergencias contra `plan.md` / `CLAUDE.md`

| # | Qué asume el disco | Qué dice el estado real | Sev |
|---|---|---|---|
| A | `recipes/agora_base`, `agora_publishing`, `agora_foi`, `agora_ai`, `agora_governance` como subdirectorios | El repo **ES una sola receta**: `recipe.yml` en la raíz. El kit no tiene `recipes/`; compone paquetes composer externos | 🔴 |
| B | `themes/agora_theme/` como carpeta del repo | El tema se **genera** vía `site_template_helper` (`extra.generate-theme`), no se versiona en el template | 🟡 |
| C | Marketplace abierto; v1 gratuita insignia | Piloto **DCP-only** + **395 $/250 $** (sin verificar) | 🔴 |
| D | "Solo estables" aplicable a todo | El starter kit **no tiene releases estables** — pero se copia, no se declara → no aplica | 🟢 |
| E | Hay que construir el install smoke | Ya existe: `InstallTest` / `ValidationTest` / `RequirementsTest` | 🟢 |
| F | GitHub mirror "opcional, solo portfolio" | El kit ya trae `.github/workflows/phpunit.yml` de serie | 🟢 |
| G | Machine name `agora` a verificar | **No verificable** sin drupal.org | 🟡 |

## 8 · Pendiente para la segunda pasada (requiere acceso a drupal.org)

1. Abrir `new.drupal.org/site-template/apply` y `/share` → confirmar o desmentir §6 (DCP-only, cuotas).
2. Disponibilidad del machine name: `agora`, `agora_transparency`, `agora_gov`.
3. Leer `include.drupalci.variables.yml` → lista exacta de jobs y sus variables.
4. Viabilidad de Playwright + axe en runners de drupalcode (→ D-009).
5. SBOM: versión estable y **cobertura de seguridad** de cada candidato (facetas/búsqueda, webform,
   ECA, IA). Nota: `eca ^3.1.2` ya viene en el `require` de la rama `2.x` del kit.
6. Verificar que `project_browser ^2.1-beta3` (constraint **beta** presente en `2.x` del kit) no
   arrastra inestables al SBOM de Ágora.
7. Confirmar terminología XB vs Canvas en los requisitos oficiales.

## 9 · Fuentes consultadas (2026-08-20)

- `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms_site_template_base/repository/{tree,tags,branches}`
- `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/{GET-STARTED.md,composer.json,recipe.yml,recommended.yml,.gitlab-ci.yml,.gitignore.example,.gitattributes.example}`
- `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/2.x/composer.json`
- `https://git.drupalcode.org/api/v4/projects/project%2Fdrupal_cms/repository/tags`
- `https://packagist.org/packages/drupal/drupal_cms_site_template_base`
- Snippets de buscador (§6), páginas NO abiertas por bloqueo DNS de `www.drupal.org`.

---

## 10 · SBOM verificado — 2026-08-20 (ampliación)

`www.drupal.org` sigue bloqueado, pero **`updates.drupal.org` sí resuelve**. Es la API oficial de
release history y expone, por release, el elemento `<security covered="1">` y `<core_compatibility>`.
Esto **cierra el hueco** del bloque SBOM (§0) para los candidatos consultados.

Endpoint: `https://updates.drupal.org/release-history/<proyecto>/current`

| Proyecto | Última **estable** | Core compat | Cobertura de seguridad |
|---|---|---|---|
| **Config Guardian** | **1.0.3** | `^10.5 \|\| ^11 \|\| ^12` | ✅ **CUBIERTO** |
| AI (Artificial Intelligence) | **1.4.7** | `^10.5 \|\| ^11.2` | ✅ CUBIERTO |
| AI Agents | 1.3.4 | `^10.3 \|\| ^11` | ✅ CUBIERTO |
| OpenAI Provider | 1.2.5 | `^10.3 \|\| ^11` | ✅ CUBIERTO |
| ECA | **3.1.6** | `^11.3 \|\| ^12.0` | ✅ CUBIERTO |
| Search API | 8.x-1.41 | `^10.3 \|\| ^11` | ✅ CUBIERTO |
| Search API Autocomplete | 8.x-1.12 | `^10.2 \|\| ^11` | ✅ CUBIERTO |
| Facets | 3.0.4 | `^10.1 \|\| ^11` | ✅ CUBIERTO |
| Webform | 6.3.0 | `^10.3 \|\| ^11.0` | ✅ CUBIERTO |
| Charts | 5.2.3 | `^10.3 \|\| ^11 \|\| ^12` | ✅ CUBIERTO |

**Conclusión: ningún candidato del SBOM previsto queda fuera por política.** Los diez tienen release
estable y cobertura del equipo de seguridad. La regla de parada nº4 del DISPATCH **no se dispara**.

### 10.1 · Config Guardian encaja sin fricción

- Estable **1.0.3**, con cobertura de seguridad, y `core_compatibility: ^10.5 || ^11 || ^12`.
- La rama `2.x` del starter kit exige `drupal/core ^11.4` → **11.4 satisface `^11`**. Compatible.
- Cumple las cuatro puertas de la skill `sbom-y-licencias`. Confirma D-004 sin necesidad de enmienda.

### 10.2 · Aviso sobre el módulo AI: usar la línea 1.4, no la 1.5

La rama **1.5 solo tiene `alpha`/`rc`** (`1.5.0-rc1`, `1.5.0-alpha2`…). La última **estable** es
**1.4.7**. Declarar `^1.4` y **nunca** `^1.5` mientras la 1.5 no publique estable.
`ai` estable exige core `^11.2`, satisfecho por 11.4.

⚠️ Pendiente de verificar: qué **provider** de IA usar. `ai_provider_openai` está cubierto, pero
`plan.md` §2 exige **proveedor-agnóstico**; el provider concreto no debería ser dependencia dura del
template sino elección post-instalación. Decisión a preparar.

### 10.3 · Notas de compatibilidad a vigilar

- **ECA 3.1.6** declara `^11.3 || ^12.0` → **no es compatible con Drupal 10**. No es problema
  (Drupal CMS 2.1.3 va sobre core 11), pero fija un suelo de core más alto que el resto.
- **Charts 5.2.3** está disponible y cubierto, pero `plan.md` §3 pide *evitar módulos de charts
  pesados*. Si se quiere visualización, es una decisión a abrir, no un automatismo.
- `project_browser ^2.1-beta3` aparece en el `require` de la rama `2.x` del starter kit. **Beta.**
  Si Ágora copia ese `require`, arrastra un inestable → hay que resolverlo en la unidad 001.

### 10.4 · Método (reproducible)

```bash
curl -s "https://updates.drupal.org/release-history/<proyecto>/current"
# leer, por release: <version>, <security covered="1">, <core_compatibility>
# la primera release sin dev/alpha/beta/rc es la última estable
```

Este método es el que debe implementar el invariante `tests/bin/sbom-check`.
