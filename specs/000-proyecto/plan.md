# Ágora · Plan maestro (unidad 000)

> Leer SIEMPRE al retomar. Este documento encodea lo decidido en la fase de concepto [andres, fase de concepto].
> Nada de aquí se contradice sin decisión nueva en DECISIONES.md.

## 1 · Posicionamiento

"Rendición de cuentas por defecto: portal de transparencia WCAG-AA, asistido por IA, que mantiene
registro de auditoría de sí mismo." Público: ayuntamientos pequeños, organismos públicos, fundaciones
y entidades que rinden cuentas. Doble propósito estratégico: (a) entrada al Site Template Marketplace
de Drupal.org como plantilla gratuita insignia; (b) escaparate de la tesis profesional del autor
(seguridad + accesibilidad + gobernanza de IA en sector público) y de Config Guardian.

## 2 · Arquitectura: una sola receta, con costura para extraer

> **Enmendado por D-011 y D-014, [andres] 2026-08-21.** La versión original describía `agora_base`,
> `agora_publishing`, `agora_foi`, `agora_ai`, `agora_governance` y `agora_theme` como sub-recetas en
> subdirectorios. **El repositorio ES la receta**: un único `recipe.yml` en la raíz con `type: Site`.
> Los cinco primeros nombres dejan de ser artefactos instalables y pasan a ser **áreas funcionales**:
> la unidad de organización interna de `recipe.yml` y de `config/`. `agora_theme` sale de este
> repositorio y pasa a ser **un proyecto propio en Drupal.org** (D-014).

Ágora v1 es **un `recipe.yml` en la raíz** que compone recetas de Drupal CMS y módulos contrib
declarados en `require`. Las áreas funcionales son la costura por la que, cuando exista la plantilla
de pago, se extraerán **recetas contrib independientes** (patrón B de D-011).
**En v1 se deja la costura; no se implementa la extracción.**

Reglas de costura (obligatorias desde el día 1, coste cero):
- Cada área ocupa un bloque contiguo y rotulado con comentario en `recipe.yml`.
- Los identificadores propios llevan prefijo de área: `agora_base_*`, `agora_publishing_*`,
  `agora_foi_*`, `agora_ai_*`, `agora_governance_*`.
- Si un área referencia un identificador de otra, la dependencia se anota en el bloque.

| Área | Qué aporta |
|---|---|
| **base** | Modelo de contenido y taxonomías: Documento (facetas: tipo, año, área), Cargo/Persona, Contrato, Partida presupuestaria, Convocatoria. Roles y permisos. |
| **publishing** | Flujos editoriales con ECA (borrador → revisión → publicado, con trazabilidad). |
| **foi** | Solicitudes de información ciudadana: Webform + ciclo de vida ECA (acuse, plazos, estados, recordatorios). |
| **ai** | Asistente con citas sobre el corpus documental (RAG), sobre el recipe de IA de Drupal CMS, proveedor-agnóstico, **opcional y con degradación elegante** sin API key. Responde SOLO desde documentos publicados; dice "no lo sé" fuera de fuentes. Dependencia dura: `ai ^1.4` y ningún provider (D-013). |
| **governance** | Config Guardian preconfigurado: snapshots programados, panel en admin. |

**Fuera de este repositorio — `drupal/agora_theme` (D-014):** estética institucional sobria, tokens de
contraste AA, tipografía con licencia libre (OFL) **auto-alojada**, imágenes propias/CC0, todo en el
manifiesto de licencias. Es un **proyecto separado en Drupal.org**, declarado en el `require` de Ágora:
un site template **no puede contener código propio** (`RequirementsTest` exige 0 ficheros `*.info.yml`).
Alcance: mínimo con dientes, compatible con Canvas, sin frameworks CSS genéricos.

## 3 · Páginas del contenido demo (bilingüe ES/EN)

Portada (buscador "¿qué quieres saber?" + indicadores clave) · Institución (organigrama, cargos,
retribuciones en tablas accesibles) · Biblioteca documental con facetas · Presupuestos y contratos
(visualización ligera + tabla accesible como fallback; evitar módulos de charts pesados) ·
Participación (solicitud de información con ciclo ECA) · Datos abiertos descargables · Asistente IA
(con descargo y citas) · **Declaración de accesibilidad** pre-armada con canal de quejas.

## 4 · Requisitos del marketplace que actúan como restricciones duras

Revisión de instalabilidad por CI · SBOM con estado de cobertura de seguridad de cada componente ·
manifiesto de licencias (GPL para lo derivado de Drupal; propietario/CC0 posible en contenido/imágenes) ·
atestación WCAG · compromiso de respuesta de seguridad (SLA definido; el autor pertenece al proceso
de Security Team con Config Guardian) · solo Drupal CMS + Canvas actuales · **sin releases inestables
ni parches**.

## 5 · Out-of-scope v1 (explícito)

- La plantilla de pago vertical (usa estos mismos recipes; unidad futura separada).
- Integraciones reales con sedes electrónicas / plataformas de contratación (costuras, no features).
- Multiidioma más allá de ES/EN. — Midgard (alpha; solo narrativa en docs). — Comercio del
  marketplace (la DA lo está construyendo). — Cualquier módulo sin cobertura de seguridad.

## 6 · Unidades previstas (la 001 se planifica en su scaffolding turn; el resto es dirección, no scope)

000 proyecto (este doc) → 001 fundación (research + esqueleto starter kit + CI verde vacío) →
002 base+theme (modelo de contenido + Canvas theme) → 003 demo content → 004 publishing+foi (ECA) →
005 ai+governance → 006 hardening (a11y audit completo, binding smoke, SBOM/licencias) →
007 publicación (proyecto Drupal.org, release, solicitud al marketplace) [manos del humano].
