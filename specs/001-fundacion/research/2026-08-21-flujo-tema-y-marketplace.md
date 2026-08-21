# Research · Flujo del tema y vía de publicación

- **Investigación:** 2026-08-21 [ejecutor]
- **Re-verificada en origen:** 2026-08-21 [ejecutor], punto por punto, antes de escribir este
  documento. Todas las citas literales de abajo se reprodujeron en esta segunda pasada.
- **Dispara:** D-008 (rider suspendido), D-012, D-014 — firmadas por [andres] 2026-08-21.
- **Caduca:** por I-001, toda afirmación de aquí se re-verifica antes de construir encima.

---

## 1 · Propósito

Cerrar tres preguntas que bloqueaban la unidad 001 y que se habían estado respondiendo de memoria:

1. ¿El starter kit **regenera** el tema en cada instalación del usuario final? (condición de parada
   que [andres] adjuntó al rider de D-008).
2. ¿Puede un site template **contener** un tema propio versionado?
3. ¿Es el marketplace **DCP-only y de pago**, como se venía asumiendo?

## 2 · Método

Consulta directa al código y a las páginas de origen, nunca a documentación intermedia ni a memoria.

- **Comprobación de red primero** (I-011: la red de este entorno varía dentro de la misma sesión,
  no se asume ni que la hay ni que no):
  `curl -s -o /dev/null -w "%{http_code}" https://git.drupalcode.org/api/v4/projects/project%2Fagora`
  → `200`. Red disponible; se procede.
- Código fuente vía `raw` de git.drupalcode.org; disponibilidad de nombres vía **API de GitLab**;
  release history vía `updates.drupal.org`; páginas de marketplace vía `curl -L` + extracción de texto.
- Criterio: una afirmación solo entra aquí si se reprodujo la **cita literal** o el **valor exacto**.

## 3 · Hallazgos

### H-1 · El tema se genera UNA vez, en el sitio de trabajo del autor — no en cada instalación
Fuente: `https://git.drupalcode.org/project/site_template_helper/-/raw/1.x/src/Plugin.php`

El método `generateTheme()` construye la ruta del `.info.yml` bajo el **Drupal root del sitio de
trabajo** (`$drupal_root/themes/<name>/<name>.info.yml`, líneas 117-123) y sale sin hacer nada si ya
existe (líneas 124-126):

```php
// If the theme was already generated, leave it alone.
if (file_exists($info_file_path)) {
  return;
}
```

Es decir: **idempotente**, y escribe **en el sitio Drupal de trabajo, no dentro del paquete de la
receta**. Es andamiaje de desarrollo del autor.
→ **La condición de parada del rider de D-008 no se dispara.**

### H-2 · El bloque `extra.drupal-site-template` se borra antes de publicar — dicho por el propio kit
Fuente: `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/composer.json`

El bloque lleva su propio `_comment` autoexplicativo, citado literal:

> "This section contains configuration used by drupal/site_template_helper to assist developers in
> creating site templates. It will be automatically removed by `drush site:export`, but you should be
> sure to delete this section before publishing your site template."

En ese mismo fichero, el tema que genera se llama `blank`, con `"from": false` (tema vacío, sin
starterkit de origen). Concuerda con `GET-STARTED.md` línea 18: *"An empty theme, called `blank`,
which you can customize however you want (or uninstall completely…)"*.

### H-3 · Un site template NO puede contener código propio — lo verifica un test del kit
Fuente: `https://git.drupalcode.org/project/drupal_cms_site_template_base/-/raw/1.x/tests/src/Kernel/RequirementsTest.php`

Líneas 30-31, literal:

```php
$finder = Finder::create()->in($path)->files()->name('*.info.yml');
$this->assertCount(0, $finder, "Recipes cannot include any code (modules or themes) of their own; they must list them as dependencies in `composer.json`.");
```

**Cero ficheros `*.info.yml` en todo el paquete.** No es una convención: es un assert que corre en el gate.

### H-4 · El paquete se instala FUERA del docroot
Fuente: `https://git.drupalcode.org/project/drupal_cms/-/raw/2.x/project_template/composer.json`
(el repo `drupal_cms` 2.x es un monorepo: **no hay `composer.json` en la raíz**; el del proyecto
instalable — paquete **`drupal/cms`**, verificado en el campo `name` — vive en `project_template/`.)

`extra.installer-paths`, valores exactos y relevantes:

```
'web/themes/contrib/{$name}'  <- ['type:drupal-theme']
'./recipes/{$name}'           <- ['type:drupal-recipe']
```

Una receta (`type: drupal-recipe`, que es lo que es Ágora) aterriza en `./recipes/<name>`, **hermano
de `web/`, no dentro**. Drupal no escanea extensiones ahí: `RecursiveExtensionFilterCallback` solo
recurre a `profiles/`, `modules/` y `themes/` del root. Un tema colocado dentro del paquete sería
invisible aunque el test de H-3 no existiera.

### H-5 · El ADR oficial contempla depender de un tema, no incluirlo
Fuente: `https://git.drupalcode.org/project/drupal_cms/-/wikis/Architecture-Decision-Records/Site-Templates.md`

Literal:

> "They MAY depend on a theme (or multiple themes) as design systems – libraries of components, and
> their associated styles, for building out the look in Canvas."

El mismo ADR fija que MUST/SHOULD/MAY se leen según RFC 2119.

### H-6 · `CI_ALLOW_DEV` es un escape hatch — y explica por qué existe el patrón "tema acoplado"
Fuente: el mismo `RequirementsTest.php`, líneas 54-55:

```php
// latest commit of a bespoke theme to which it is strongly coupled.
$allow_dev = getenv('CI_ALLOW_DEV');
```

El comentario describe el caso de uso: probar el template contra el último commit de *"a bespoke
theme to which it is strongly coupled"*. Confirma que **tema acoplado como proyecto aparte es el
patrón previsto**, y a la vez que la variable **debilita el gate** de versiones (→ I-015, T-209).

### H-7 · Machine name: el único oráculo válido es la API de GitLab
Fuente: `https://git.drupalcode.org/api/v4/projects/project%2F<X>`

| Candidato | HTTP | Lectura |
|---|---|---|
| `agora` | **200** | ocupado por un proyecto ajeno |
| `agora_transparency` | **404** | libre |
| `agora_gov` | **404** | libre |
| `agora_theme` | **404** | libre (dato nuevo, relevante para el rider (c) de D-014) |

⚠️ `www.drupal.org/project/<X>` devuelve **302 hacia new.drupal.org para cualquier cadena**, también
inexistente: un 302 **no prueba disponibilidad** (→ I-012).

Nota concordante de `GET-STARTED.md` línea 64: *"don't prefix its name with `drupal_cms_` or
`drupal-cms-` … it is not part of Drupal CMS"*. `agora_transparency` cumple.

### H-8 · El marketplace está abierto a individuos para plantillas gratuitas
Fuente: `https://new.drupal.org/site-template/apply`, sección "Who can apply". Literal:

> "Individuals — Free templates: Any individual who wants to submit free templates, is welcome to."
> "For paid templates: Drupal Certified Partners, with a history of contribution."

Ser DCP/Ripplemaker es requisito **solo de las plantillas de pago**. La premisa "piloto DCP-only"
era **falsa**. Además: **no aparece ninguna cuota** (`395`/`250`) en la página. La cifra procede de la
*propuesta* de julio 2025 (`https://www.drupal.org/project/innovation_ideas/issues/3532934`), que dice
literalmente **"(none for pilot and MVP)"**.

Complemento:
`https://www.drupal.org/about/initiatives/cms/blog/differentiating-marketplace-site-templates-and-community-site-templates`
→ *"All free site templates, including marketplace templates, are general projects for packaging and
distribution purposes"*, con recomendación explícita de *"sharing a community template first"*.

### H-9 · `release-history` da falso verde por código de estado
Fuente: `https://updates.drupal.org/release-history/<X>/current`

Reproducido con un proyecto inexistente:

```
$ curl -s -w "%{http_code}" .../release-history/agora_no_existe_xyz/current
HTTP 200
<?xml version="1.0" encoding="utf-8"?>
<error>No release history was found for the requested project (agora_no_existe_xyz).</error>
```

**HTTP 200 con cuerpo `<error>`.** Un `curl -f` o cualquier chequeo por status da verde sobre un
proyecto que no existe. Control positivo (`config_guardian`) sí devuelve `<title>Config Guardian</title>`
y `<supported_branches>`. → I-013, y enmienda del método de `sbom-check` (T-306).

## 4 · Conclusiones

1. **El tema no se regenera en la instalación del usuario final.** Se genera una sola vez, en el sitio
   de trabajo del autor, de forma idempotente, y su bloque de configuración se borra antes de publicar
   (H-1, H-2).
2. **Un site template no puede contener código propio.** Cero `*.info.yml`, verificado por un test del
   propio kit, y además el paquete vive fuera del docroot donde Drupal ni escanea (H-3, H-4).
   Temas y módulos se **declaran en `require`** (H-5).
3. **El marketplace está abierto a individuos para plantillas gratuitas y no hay cuota confirmada para
   el MVP**; las plantillas gratuitas son, en todo caso, proyectos generales, y se recomienda publicar
   primero como community (H-8).
4. **El oráculo válido del machine name es la API de GitLab**, no `www.drupal.org/project/<X>` (H-7);
   y `updates.drupal.org` exige parsear el XML, no mirar el status (H-9).

## 5 · Qué decisiones dispara

| Decisión | Efecto |
|---|---|
| **D-008** | Rider "tema committeado en el repo" **SUSPENDIDO por imposibilidad técnica** (H-3, H-4). Sobrevive la regla negativa: este repositorio no contiene tema propio. Subsumida por D-014. |
| **D-012** | Community primero, marketplace después. Cierra el pendiente (c) de D-011 sobre la cuota (H-8). |
| **D-014** | El tema es **proyecto aparte** (`drupal/agora_theme`), declarado en `require` (H-3, H-4, H-5, H-6). H-7 aporta además que `agora_theme` está libre. |
| **D-007** | `agora_transparency` (H-7). |

Idioms derivados: I-011 (red), I-012 (oráculo de nombres), I-013 (falso verde de release-history),
I-014 (nada de código propio), I-015 (`CI_ALLOW_DEV`), I-016 (premisa alarmante no verificada),
I-017 (licencia y privacidad como restricciones estructurales).

## 6 · Caducidad

Por **I-001**, este documento describe el estado del arte a **2026-08-21**. Drupal CMS, Canvas y el
programa de site templates son tecnología joven: antes de construir sobre cualquier hallazgo de aquí
—muy especialmente H-8, que depende de una política en evolución— se re-verifica en origen.
