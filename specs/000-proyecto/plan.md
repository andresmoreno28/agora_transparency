# Ágora · Plan maestro (unidad 000)

> Leer SIEMPRE al retomar. Este documento encodea lo decidido en la fase de concepto [andres, fase de concepto].
> Nada de aquí se contradice sin decisión nueva en DECISIONES.md.

## 1 · Posicionamiento

"Rendición de cuentas por defecto: portal de transparencia WCAG-AA, asistido por IA, que mantiene
registro de auditoría de sí mismo." Público: ayuntamientos pequeños, organismos públicos, fundaciones
y entidades que rinden cuentas. Doble propósito estratégico: (a) entrada al Site Template Marketplace
de Drupal.org como plantilla gratuita insignia; (b) escaparate de la tesis profesional del autor
(seguridad + accesibilidad + gobernanza de IA en sector público) y de Config Guardian.

## 2 · Arquitectura de recipes (modular, reutilizable en la futura plantilla de pago)

- **agora_base** — modelo de contenido y taxonomías: Documento (con facetas: tipo, año, área),
  Cargo/Persona, Contrato, Partida presupuestaria, Convocatoria. Roles y permisos.
- **agora_publishing** — flujos editoriales de publicación con ECA (borrador → revisión → publicado,
  con trazabilidad).
- **agora_foi** — solicitudes de información ciudadana: Webform + ciclo de vida ECA (acuse, plazos,
  estados, recordatorios).
- **agora_ai** — asistente con citas sobre el corpus documental (RAG), montado sobre el recipe de IA
  de Drupal CMS, proveedor-agnóstico, **opcional y con degradación elegante** si no hay API key
  (crítico para el CI de instalación). Responde SOLO desde documentos publicados; dice "no lo sé"
  fuera de fuentes.
- **agora_governance** — Config Guardian preconfigurado: snapshots programados, panel en admin,
  narrativa "el portal se audita a sí mismo".
- **agora_theme** — tema compatible con Drupal Canvas: estética institucional sobria, tokens de
  contraste AA, tipografía con licencia libre (OFL), imágenes propias/CC0. Todo en el manifiesto
  de licencias.

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
