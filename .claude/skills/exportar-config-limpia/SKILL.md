---
name: exportar-config-limpia
description: Use when exporting a Drupal site into a recipe with drush site:export, or when reviewing exported YAML under config/ — checking for site UUIDs, _core hashes, unstable ordering, leaked secrets or environment-specific values before committing the export.
---

# Exportar el sitio a receta, limpio

## Principio nuclear

`drush site:export` convierte el sitio de trabajo en receta. **Lo que exporta es lo que hay** — si el
entorno estaba sucio, la receta sale sucia. La limpieza se hace **antes** de exportar y se **verifica**
después, en el diff.

## Flujo

```bash
# 1. Deja el sitio exactamente como quieres que lo reciba el usuario
# 2. Exporta
ddev drush site:export
# 3. Prueba en LIMPIO (no en el entorno de trabajo)
ddev drush sql:drop --yes
#    reinstala y comprueba que el template aparece en el selector
```

`site:export` hace por su cuenta dos cosas que conviene conocer:
- **Añade al `require`** de `composer.json` los módulos/temas/recetas que el sitio usa.
- **Elimina el bloque `extra.drupal-site-template`** (el de `generate-theme`).

## Qué revisar SIEMPRE en el diff

| Buscar | Por qué | Acción |
|---|---|---|
| `uuid:` a nivel raíz de un config | Es el UUID **del sitio origen** | Quitar |
| `_core:` / `default_config_hash` | Atado a la instalación origen | Quitar salvo que toque |
| Rutas absolutas (`/var/www`, `/home/...`) | Específicas del entorno | Quitar |
| Claves de API, tokens, contraseñas, DSNs | **Nunca en el repo** | Quitar y mover a entorno |
| `mail`, dominios reales, IDs de analítica | Datos del entorno de desarrollo | Neutralizar |
| Módulos de desarrollo (`devel`, `webprofiler`) | No van al usuario final | Desinstalar y re-exportar |
| Orden inestable entre exports | Genera diffs de ruido | Re-exportar y comparar |

## Comprobación rápida antes de commitear

```bash
grep -rn "_core\|default_config_hash" config/ | head
grep -rniE "api[_-]?key|secret|token|passwd|password" config/ content/ | head
grep -rn "/var/www\|/home/" config/ | head
```

Cualquier acierto → **parar y limpiar**, no commitear "y ya lo arreglo".

## Contenido demo

Sale a `content/<entity_type>/<uuid>.yml`. Reglas:
- Solo contenido sobre el que se tienen **derechos legales** (imágenes, fuentes, textos).
- Sin datos personales reales: nombres, emails y teléfonos inventados y evidentes.
- Los ficheros binarios referenciados deben viajar con el template.

## Errores comunes

- **Exportar desde el entorno sucio** con módulos de dev instalados → viajan al usuario.
- **Probar la receta sobre el mismo sitio del que se exportó** → siempre pasa; no prueba nada.
- **Commitear el export sin leer el diff** → así entran UUIDs y secretos.
- **Dejar `extra.drupal-site-template`** si se editó el composer.json después de exportar.

## Red flags — PARA

- "El secreto es de un entorno de pruebas, no pasa nada" → **no**, sale del repo.
- "El diff es enorme, lo reviso por encima" → es justo cuando entran las fugas.
- "Ya lo probé en mi sitio y funciona" → prueba en limpio o no has probado.
