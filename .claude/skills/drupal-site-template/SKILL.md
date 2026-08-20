---
name: drupal-site-template
description: Use when working on the structure, layout, packaging or publication of a Drupal CMS site template — creating or moving recipe.yml, composer.json, config/, content/ or screenshot.webp; deciding where a recipe, theme or demo page belongs; renaming the package; or preparing the template for release on Drupal.org.
---

# Estructura de un Site Template de Drupal CMS

## Principio nuclear

**El repositorio ES la receta.** No es un contenedor con recetas dentro: `recipe.yml` vive en la
raíz y describe todo el template. Colocar un `recipes/` con sub-recetas locales NO es el patrón del
starter kit y no hay evidencia de que el instalador lo resuelva.

Verificado contra `drupal_cms_site_template_base` el 2026-08-20. Ver
`specs/001-fundacion/research/2026-08-20-estado-del-arte.md`.

## Layout canónico

```
recipe.yml                 ← OBLIGATORIO, en la raíz, `type: Site`
composer.json              ← `type: drupal-recipe`, nombre `drupal/<machine_name>`
config/                    ← config exportada (la crea `drush site:export`)
content/<entity_type>/<uuid>.yml   ← contenido demo como entidades
recommended.yml            ← lista curada para Project Browser (opcional)
screenshot.webp            ← captura del template
README.md · LICENSE.txt
.gitlab-ci.yml             ← include de gitlab_templates (NO definas jobs a mano)
.github/workflows/         ← PHPUnit
.tugboat/                  ← previews en vivo
tests/src/{Functional,Kernel}/
.gitattributes             ← export-ignore de lo que no viaja al usuario final
```

## Reglas duras (romper una = el template no se publica)

| # | Regla | Por qué |
|---|---|---|
| 1 | `recipe.yml`, ese nombre exacto, en la raíz | El instalador no lo encuentra si no |
| 2 | `type: Site` **case-sensitive** | Sin esto no aparece en el selector de plantillas |
| 3 | composer `type: drupal-recipe` | Determina cómo se instala |
| 4 | Paquete `drupal/nombre_maquina` — solo letras, números, guion bajo | Requisito de publicación en Drupal.org |
| 5 | **Sin `composer-patches`** | Prohibido explícitamente por el kit |
| 6 | **Sin pins**: usa `^1`, nunca `1.2.3` | Prohibido explícitamente por el kit |
| 7 | `GPL-2.0-or-later` salvo razón fuerte | Otra licencia puede impedir publicar |
| 8 | Derechos legales sobre TODO el contenido incluido | Fuentes, imágenes, texto demo |

## Composición: cómo se añaden funcionalidades

Un site template **sí** puede componerse de varias recetas más pequeñas, pero éstas son
**paquetes composer externos**, no carpetas locales:

```yaml
recipes:
  - core/recipes/administrator_role   # las de core, por ruta
  - drupal_cms_media                  # las contrib, por nombre de receta
install:
  - modulo_o_tema                     # módulos/temas a instalar
```

**Prohibido** construir un site template encima de otro site template. Regla textual del kit:
*"you shouldn't build a site template on top of another site template"*.

## El tema NO se versiona (por defecto)

El tema lo genera el plugin `drupal/site_template_helper` desde `composer.json`:

```json
"extra": { "drupal-site-template": { "generate-theme": {
    "name": "mi_tema", "from": false,
    "info": { "name": "Mi tema", "regions": {"header":"Header","content":"Content","footer":"Footer"} }
}}}
```

`from: false` = no hereda de tema base. **`drush site:export` borra este bloque** — y debes borrarlo
tú antes de publicar si sigue ahí. Un tema propio versionado es una desviación deliberada del flujo,
no el camino por defecto: decídelo explícitamente.

## Qué rama del starter kit copiar

| Rama | Qué es | Uso |
|---|---|---|
| `1.x` | El starter kit real: CI, Tugboat, `GET-STARTED.md`, `AGENTS.md`, tests | **Copiar ésta** |
| `2.x` | Un template ya exportado (config/ + content/, sin CI ni docs) | Referencia de "cómo queda tras exportar" |

El starter kit **no tiene releases estables, solo ramas**. No es un problema de política de
dependencias: **se copia, no se declara en `require`**. Nunca entra en el SBOM.

## Errores comunes

- **Crear `recipes/agora_x/` dentro del repo** → no es el patrón; el repo ya es la receta.
- **Versionar el tema generado sin decidirlo** → diverge del flujo de `site:export`.
- **Dejar `extra.drupal-site-template` en el `composer.json` publicado** → hay que borrarlo.
- **Dejar `GET-STARTED.md`** → es del kit, no de tu template (va en `export-ignore`).
- **Escribir jobs de CI a mano** → `.gitlab-ci.yml` solo incluye `gitlab_templates`; se configura
  con variables (`SKIP_ESLINT`, `OPT_IN_TEST_NEXT_MAJOR`…).
- **Pinear una versión** para "asegurar" reproducibilidad → prohibido.

## Antes de publicar

- [ ] `name`, `description` y `screenshot.webp` propios (no los del kit)
- [ ] `_comment` y `extra.drupal-site-template` eliminados de `composer.json`
- [ ] `GET-STARTED.md` borrado; `.gitattributes` con los `export-ignore`
- [ ] `recipe.yml` con `type: Site` y descripción visible en el instalador
- [ ] Prueba real: exportar → `drush sql:drop --yes` → reinstalar → el template aparece en el selector
- [ ] `recommended.yml` solo con proyectos que tengan release **estable**
