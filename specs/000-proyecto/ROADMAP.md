# Ágora · Roadmap de desarrollo completo (unidades 001 → 007)

> **Estado: DIRECCIÓN, no scope firmado.** Este documento desarrolla el §6 de `plan.md` para que
> exista visión de extremo a extremo. El scope real de cada unidad se fija en **su** scaffolding turn
> (research fechada → `plan.md` → `tasks.md`), y ahí puede diverger de aquí: manda el disco y el
> estado del arte del momento (I-001).
>
> Redactado [ejecutor] 2026-08-20, tras la research `specs/001-fundacion/research/2026-08-20-estado-del-arte.md`.

## Mapa de dependencias

```
001 fundación ──► 002 base + tema ──┬─► 003 contenido demo ──┐
                                    │                        ├─► 006 hardening ──► 007 publicación
                                    ├─► 004 publishing + foi ─┤
                                    └─► 005 ia + governance ──┘
```

`003`, `004` y `005` dependen de `002` pero **no entre sí**: pueden solaparse si los ficheros son
disjuntos. `006` exige las tres cerradas. `007` es del humano.

## Convenciones del roadmap

- **Gate A** = verde automatizable, con counts reales (ver skill `gate-a-verde`).
- **Gate B** = firma de Andrés.
- Toda unidad arranca con reconciliation pass y cierra con reporte + HOLD.
- Cada módulo contrib que aparezca necesita su línea en `DECISIONES.md` **en el mismo cambio**.

---

## 001 · Fundación — esqueleto y CI verde en vacío

**Objetivo:** que exista un repositorio que ES un site template válido, instalable en limpio y con el
pipeline de drupalcode en verde, todavía sin identidad ni contenido propio.

**Bloqueada por:** D-007 (machine name), D-008 (enfoque del tema), **D-011 (arquitectura de recetas)**
y la re-verificación de los requisitos del marketplace. Ver `specs/001-fundacion/plan.md`.

**Puntos de desarrollo**
1. Copiar la rama `1.x` del starter kit y renombrar el paquete a `drupal/<machine_name>`.
2. Limpiar el andamiaje: `_comment` de `composer.json`, `GET-STARTED.md`, `screenshot.webp` del kit.
3. `.gitignore` / `.gitattributes` definitivos desde los `.example`.
4. `recipe.yml` propio: `name`, `description`, `type: Site`, recetas base heredadas.
5. Entorno DDEV (≥1.25.0) reproducible y documentado.
6. `.gitlab-ci.yml` con las variables correctas; primer pipeline verde en vacío.
7. Scripts de invariantes en `tests/bin/`: `no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check`.
8. Install smoke real: aplicar sobre Drupal CMS limpio y comprobar que aparece en el selector.

**Gate A** · `composer validate` · pipeline drupalcode verde · `InstallTest`/`ValidationTest`/
`RequirementsTest` en verde con counts · los 4 invariantes exit 0 con nº de ficheros escaneados.

**Gate B** · Andrés confirma que el esqueleto instala en limpio y que el machine name es el definitivo.

---

## 002 · Base + tema — modelo de contenido y Canvas

**Objetivo:** el modelo de datos de transparencia y un tema Canvas-compatible sobrio y accesible.

**Puntos de desarrollo**
1. **Tipos de contenido**: Documento, Cargo/Persona, Contrato, Partida presupuestaria, Convocatoria.
2. **Taxonomías y facetas**: tipo de documento, año, área/departamento, estado.
3. **Roles y permisos**: editor, revisor, publicador, administrador. Permisos mínimos por rol.
4. **Vistas base**: biblioteca documental con facetas, listados por tipo, buscador.
5. **Tema Canvas**: tokens de color con contraste AA verificado, tipografía OFL, escala tipográfica,
   espaciado, foco visible, `prefers-reduced-motion`.
6. **Componentes Canvas**: qué componentes se habilitan y cuáles se ocultan (`disable: []`).
7. **Plantillas Twig** de los tipos de contenido, con semántica correcta y tablas accesibles.
8. Decidir y aplicar el enfoque del tema (D-008: generado vs versionado).

**Gate A** · phpcs/phpstan/eslint/stylelint verdes · axe sin violaciones sobre las plantillas ·
contraste de todos los tokens verificado · PHPUnit kernel del modelo de contenido.

**Gate B** · Andrés valida la estética institucional y el modelo de contenido.

**Riesgo principal** 🟡 · Canvas es tecnología joven: re-verificar qué exige hoy un tema
"Canvas-compatible" antes de escribir el tema.

---

## 003 · Contenido demo — bilingüe ES/EN

**Objetivo:** un portal que al instalarse ya cuenta una historia coherente y completa.

**Puntos de desarrollo**
1. **Portada**: buscador "¿qué quieres saber?" + indicadores clave.
2. **Institución**: organigrama, cargos, retribuciones en tablas accesibles.
3. **Biblioteca documental** con facetas pobladas.
4. **Presupuestos y contratos**: visualización ligera **+ tabla accesible como fuente de verdad**
   (evitar módulos de charts pesados).
5. **Datos abiertos** descargables (CSV/JSON) con su ficha.
6. **Declaración de accesibilidad** pre-armada, con canal de quejas.
7. Traducciones ES/EN de todo lo anterior; `lang` correcto por fragmento.
8. Media demo con licencias documentadas (CC0/propias) en el manifiesto.

**Gate A** · axe sin violaciones en **todas** las páginas demo · Playwright funcional + visual ·
invariante `no-secrets` sobre `content/` · verificación de que no hay datos personales reales.

**Gate B** · Andrés revisa el tono institucional y la veracidad plausible del contenido ficticio.

**Riesgo** 🟡 · Derechos de imágenes y fuentes: nada entra sin licencia nombrable.

---

## 004 · Publishing + FOI — flujos con ECA

**Objetivo:** el portal deja de ser estático: tiene flujo editorial y ciclo de solicitudes ciudadanas.

**Puntos de desarrollo**
1. **Flujo editorial** (borrador → revisión → publicado) con moderación de contenido y trazabilidad.
2. **ECA de publicación**: notificaciones, transiciones, registro de quién y cuándo.
3. **Webform de solicitud de información** ciudadana, accesible y con validación clara.
4. **Ciclo FOI con ECA**: acuse de recibo automático, cómputo de plazos, estados, recordatorios,
   respuesta y cierre.
5. **Panel de seguimiento** de solicitudes para el gestor.
6. Correos transaccionales (sobre `easy_email_express`) accesibles y sin datos sensibles.
7. Permisos y visibilidad: qué ve el ciudadano, qué ve el gestor.

**Gate A** · PHPUnit funcional de los ciclos ECA (estados y plazos) · Playwright del flujo completo
de solicitud · axe sobre el formulario y sus errores · `no-secrets`.

**Gate B** · Andrés recorre el ciclo FOI de punta a punta.

**Riesgo** 🟡 · Los plazos legales varían por jurisdicción: configurables, nunca hardcodeados.

---

## 005 · IA + governance — asistente con citas y auditoría

**Objetivo:** las dos features diferenciales. Ambas deben degradar con elegancia.

**Puntos de desarrollo**
1. **Recipe `agora_ai`** sobre el recipe de IA de Drupal CMS, **proveedor-agnóstico**.
2. **RAG sobre el corpus documental**: indexa **solo documentos publicados**.
3. **Citas obligatorias**: toda respuesta enlaza a sus fuentes; fuera de fuentes responde "no lo sé".
4. **Degradación elegante sin API key** — el CI de instalación corre sin claves (I-003). Si falta la
   clave: la feature se oculta o informa, y **la instalación no se rompe**.
5. **Descargo visible** de que la respuesta la genera una IA.
6. **Config Guardian preconfigurado**: snapshots programados + panel en admin.
7. Narrativa "el portal se audita a sí mismo" documentada para el usuario final.
8. Configuración de la clave **por variable de entorno/UI post-instalación**, jamás en config.

**Gate A** · install smoke **sin API key** verde (bloqueante) · PHPUnit del recipe de IA ·
invariante `no-secrets` reforzado · axe sobre la UI del asistente.

**Gate B** · Andrés valida que sin clave el sitio instala y se comporta bien, y que con clave las
citas son correctas.

**Riesgo** 🔴 · Es la unidad con más superficie de fuga de secretos y la que más fácilmente rompe
la instalabilidad. Tratar el smoke sin clave como el test principal, no como un extra.

---

## 006 · Hardening — auditoría completa antes de publicar

**Objetivo:** pasar la revisión del marketplace **a la primera**.

**Puntos de desarrollo**
1. **Auditoría a11y completa**: axe + recorrido de teclado en todos los flujos + criterios de WCAG 2.2
   (foco no obstruido, tamaño de objetivo, ayuda consistente, entrada redundante).
2. **Atestación WCAG** redactada y firmada.
3. **SBOM final**: cada componente con versión estable, estado de cobertura de seguridad y su línea
   en `DECISIONES.md`.
4. **Manifiesto de licencias** completo: código GPL, fuentes OFL, media CC0/propia.
5. **Binding smoke**: instalación en limpio de extremo a extremo, sin claves, verificando rutas y render.
6. **Regresión visual** estabilizada de todas las páginas demo.
7. **Rendimiento**: revisión de peso de página y consultas; sin módulos pesados innecesarios.
8. **Documentación pública en inglés**: README del proyecto, instalación, configuración post-install,
   qué hace cada recipe.
9. **Compromiso de respuesta de seguridad**: SLA documentado.
10. Barrido final de invariantes: `no-unstable-deps`, `no-patches`, `no-secrets`, `sbom-check`.

**Gate A** · Todo lo anterior en verde con counts + veredicto del `orquestador` sin 🔴 abiertos.

**Gate B** · Andrés firma que el template está listo para someterse a revisión.

---

## 007 · Publicación — manos del humano

**Objetivo:** que Ágora exista públicamente. **Esta unidad no la ejecuta la IA.**

**Puntos de desarrollo**
1. Crear el proyecto en Drupal.org con el machine name firmado (D-007).
2. Empujar el repositorio a `git.drupalcode.org`; configurar el mirror en GitHub si aplica.
3. Verificar el pipeline en el proyecto real.
4. Publicar `screenshot.webp`, descripción y documentación del proyecto.
5. Configurar `recommended.yml` con su permalink de la API de GitLab.
6. Tugboat para la demo en vivo; enlazarla desde `recipe.yml` (`drupal_cms_installer.links`).
7. Primera **release estable**.
8. Decidir vía de publicación (ver ⚠️ abajo) y, si procede, solicitud al marketplace.

⚠️ **Bloqueante sin resolver:** la research del 2026-08-20 recogió señales (sin verificar) de que el
marketplace arrancó como **piloto limitado a Drupal Certified Partners**, con **cuota de 395 $ por
listing + 250 $ anuales**. Si se confirma, la vía marketplace puede no estar abierta y la ruta real
sería **Community** (proyecto general, publicable sin revisión). Debe verificarse antes de 006.

---

## Riesgos transversales

| Riesgo | Sev | Mitigación |
|---|---|---|
| Elegibilidad y coste del marketplace | 🔴 | Verificar en drupal.org antes de 006; vía Community como plan B |
| Arquitectura de recetas sin decidir (D-011) | 🔴 | Bloquea 001; decisión de Andrés |
| Canvas / site templates son tecnología joven | 🟡 | Research fechada al inicio de cada unidad (I-001) |
| IA rompiendo la instalación sin clave | 🟡 | Smoke sin clave como test principal de 005 |
| Derechos de media y fuentes | 🟡 | Nada entra sin licencia nombrable |
| Módulos sin cobertura de seguridad | 🟡 | Puerta de entrada de la skill `sbom-y-licencias` |
