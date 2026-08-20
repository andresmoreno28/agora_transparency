---
name: drupal-recipe-authoring
description: Use when writing or editing a Drupal recipe.yml — adding config actions, choosing between the recipes/install/config keys, setting strict mode, making config optional, disabling Canvas components, or debugging a recipe that fails to apply or conflicts with existing configuration.
---

# Escribir `recipe.yml`

## Principio nuclear

Una receta es **declarativa e idempotente-por-diseño**: describe el estado deseado, no pasos.
Si falla al aplicarse, casi siempre es por **config que ya existe** o **config que no existe todavía**.

## Anatomía

```yaml
name: Nombre visible
description: 'Texto que ve el usuario en el instalador.'
type: Site          # 'Site' solo para site templates; otras recetas lo omiten

recipes:            # recetas que se aplican ANTES que ésta
  - core/recipes/administrator_role
  - drupal_cms_media

install:            # módulos y temas a instalar
  - pathauto
  - mi_tema

config:
  strict: false     # ver abajo — casi siempre false en site templates
  actions:
    nombre.del.config:
      accionQueSea: valor

extra:
  recipe_installer_kit:
    finish_url: '/admin/dashboard/welcome'
```

## `recipes:` vs `install:` — la confusión más frecuente

| Clave | Qué acepta | Cuándo |
|---|---|---|
| `recipes:` | Otras **recetas** (por ruta de core o por nombre) | Quieres su config y su modelo completo |
| `install:` | **Módulos y temas** sueltos | Solo necesitas que el módulo esté activo |

Las recetas listadas en `recipes:` **no deben ser site templates**. Componer sobre recetas pequeñas
es correcto; sobre otro site template, no.

## `strict: false` — qué significa realmente

- `strict: false` → si el sitio **ya tiene** un config que la receta aporta, **gana el existente** y
  la receta no falla.
- `strict: true` → conflicto = error al aplicar.

En site templates se usa **`false`**. Comentario textual del starter kit: *"most site templates
provide configuration that will break at install time if you change this"*.

## El prefijo `?` — config opcional

```yaml
config:
  actions:
    ?canvas.component.block.navigation_user:   # con ? → si no existe, se ignora
      disable: []
    canvas.component.block.system_messages_block:   # sin ? → si no existe, ERROR
      disable: []
```

**Regla:** si el config lo aporta un módulo que puede no estar instalado, ponle `?`.
Un `?` de más es inocuo; uno de menos rompe la instalación en limpio.

## Acciones habituales

| Acción | Uso |
|---|---|
| `simpleConfigUpdate` | Cambiar claves de config simple (`system.site`, `system.theme`) |
| `disable: []` | Ocultar un componente de Canvas de la UI (no lo borra) |
| `setComponentList` / `grantPermissions` | Según entidad; depende del tipo de config |

```yaml
system.site:
  simpleConfigUpdate:
    page.front: '/home'
system.theme:
  simpleConfigUpdate:
    default: 'mi_tema'
```

## Errores comunes

- **Olvidar `?`** en config de un módulo opcional → rompe el install smoke en limpio.
- **Poner un módulo en `recipes:`** (o una receta en `install:`) → no resuelve.
- **Cambiar `strict` a `true`** para "ser más riguroso" → rompe la instalación.
- **`type: site`** en minúscula → el template no aparece en el instalador (es case-sensitive).
- **Asumir orden**: `recipes:` se aplica antes; si tu acción toca config que aporta otra receta,
  esa receta debe estar listada antes.

## Verificación

Aplicar sobre un Drupal **limpio**, nunca sobre el entorno sucio de desarrollo:

```bash
ddev drush sql:drop --yes
# reinstalar y comprobar que el template aparece en el selector
```

Un `?` que falta solo se manifiesta en limpio. Probar sobre el entorno de trabajo da falsos verdes.
