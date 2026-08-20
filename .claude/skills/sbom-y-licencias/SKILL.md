---
name: sbom-y-licencias
description: Use when adding, upgrading or evaluating any dependency for the site template — running composer require, editing the require section, listing a project in recommended.yml, or reviewing whether a module's release status, security coverage or license allows it into the SBOM.
---

# Política de dependencias, SBOM y licencias

## Principio nuclear

En este proyecto **cada dependencia es una decisión firmada, no un `composer require`**. El
marketplace exige SBOM con estado de cobertura de seguridad, y prohíbe releases inestables y parches.
Una dependencia sin justificación en `specs/000-proyecto/DECISIONES.md` **no existe**.

## Puerta de entrada — las cuatro preguntas

Antes de añadir NADA, en este orden:

1. **¿Lo resuelve algo que Drupal CMS ya trae?** Si sí → usa eso. Fin.
2. **¿Tiene release estable?** Ni dev, ni alpha, ni beta, ni RC. Si no → **fuera**.
3. **¿Tiene cobertura del equipo de seguridad de Drupal?** Si no → **fuera**, salvo escalación firmada.
4. **¿La licencia es compatible?** GPL-2.0-or-later para lo derivado de Drupal.

Si las cuatro pasan → añadir **y** escribir su línea en `DECISIONES.md` en el mismo cambio.

## Prohibiciones absolutas

| Prohibido | Ejemplo | Por qué |
|---|---|---|
| Releases inestables | `^2.1-beta3`, `1.0-alpha1`, `dev-main` | Requisito literal del marketplace |
| Parches | `composer-patches`, sección `patches` | Prohibido por el starter kit |
| Pins exactos | `"drupal/x": "1.2.3"` | Prohibido; usa `^1.2` |
| `minimum-stability` relajado | `"minimum-stability": "beta"` | Enmascara el problema anterior |
| Secretos en config o repo | claves de IA, tokens | No-negociable nº3 |

## La línea de `DECISIONES.md`

Cada módulo contrib necesita, como mínimo:

```
- **D-0NN** · SBOM: `drupal/<modulo>` ^X.Y — qué aporta (1 línea), por qué no lo cubre Drupal CMS,
  estado de cobertura de seguridad, licencia. Firmada por [andres] AAAA-MM-DD.
```

## Licencias — manifiesto

| Tipo de activo | Licencia esperada |
|---|---|
| Código derivado de Drupal (recetas, temas, módulos) | GPL-2.0-or-later |
| Tipografías | OFL u otra libre — **nunca** una fuente de licencia restringida |
| Imágenes y media demo | CC0, propias, o con derechos documentados |
| Contenido de texto demo | Propio |

Regla del kit: *"You must possess legal rights to all included content"*. Si no puedes nombrar la
licencia de un activo, **no entra**.

## Caso especial: el starter kit no cuenta

`drupal_cms_site_template_base` no tiene releases estables — solo ramas. **No es una violación**:
se **copia** como andamiaje, nunca se declara en `require`. No entra en el SBOM. No lo marques como
hallazgo del invariante `no-unstable-deps`.

## `recommended.yml` (Project Browser)

Aviso literal del propio fichero: lista **solo** proyectos con releases estables y soportadas.
Un proyecto en beta ahí no será instalable por la mayoría de usuarios (`minimum-stability` por
defecto de Composer).

## Racionalizaciones y realidad

| Excusa | Realidad |
|---|---|
| "El beta es estable en la práctica" | El requisito es formal, no una opinión sobre calidad |
| "Es solo una dependencia de desarrollo" | Si está en `require`, viaja al usuario |
| "Lo pineo para que sea reproducible" | Los pins están prohibidos explícitamente |
| "Un parche pequeño y lo quito luego" | Los parches están prohibidos; el "luego" no llega |
| "Lo documento después" | La línea de DECISIONES va en el MISMO cambio |
| "Es lo que trae el starter kit en 2.x" | Lo que traiga el kit no te exime; tu SBOM es tuyo |

## Red flags — PARA y escala

- Estás a punto de escribir `-beta`, `-alpha`, `-rc` o `dev-` en `composer.json`
- Estás buscando cómo relajar `minimum-stability`
- Añades un módulo "y ya lo justifico luego"
- No sabes decir bajo qué licencia está una fuente o una imagen que vas a incluir
