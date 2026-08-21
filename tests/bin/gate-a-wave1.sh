#!/usr/bin/env bash
#
# gate-a-wave1.sh - Agora - Unit 001, wave 1 gate A verification.
#
# Verifies the skeleton and identity of the site template package (T-101..T-105,
# T-107, T-111, T-112) against the hard structural rules recorded in CLAUDE.md and
# IDIOMS.md (I-004, I-007, I-014, I-015, I-020).
#
# Contract:
#   - every check prints:  obtained | expected | OK/FALLO
#   - closing line:        "N comprobaciones - M fallos"
#   - exit 0 ONLY if M = 0
#   - a check that CANNOT be evaluated (missing file, missing tool, invalid
#     JSON) is a FALLO, never a skip. I-007: an exit 0 without counts proves
#     nothing.
#
# Usage: tests/bin/gate-a-wave1.sh   (run from anywhere; it cd's to the repo root)

set -u
# No `set -e`: a failing check must be recorded, not abort the run.

# ---------------------------------------------------------------- repo root --
SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
REPO_ROOT=$(CDPATH= cd -- "$SCRIPT_DIR/../.." && pwd)
cd "$REPO_ROOT" || { echo "FATAL: cannot cd to repo root $REPO_ROOT"; exit 1; }

N=0
M=0
GRP=""

C_OK=""; C_BAD=""; C_OFF=""; C_DIM=""
if [ -t 1 ]; then
  C_OK=$'\033[32m'; C_BAD=$'\033[31m'; C_OFF=$'\033[0m'; C_DIM=$'\033[2m'
fi

# Truncate a value so the table stays aligned; never hide the fact it was cut.
trunc() {
  _v=$1; _max=$2
  if [ "${#_v}" -gt "$_max" ]; then
    printf '%s...' "$(printf '%s' "$_v" | cut -c1-$((_max - 3)))"
  else
    printf '%s' "$_v"
  fi
}

# check <label> <obtained> <expected>
check() {
  _label=$1; _got=$2; _exp=$3
  N=$((N + 1))
  if [ "$_got" = "$_exp" ]; then
    _res="${C_OK}OK${C_OFF}"
  else
    _res="${C_BAD}FALLO${C_OFF}"
    M=$((M + 1))
  fi
  printf '  %-38s %-28s | %-28s | %s\n' \
    "$_label" "$(trunc "$_got" 28)" "$(trunc "$_exp" 28)" "$_res"
}

group() {
  GRP=$1
  printf '\n%s\n' "$GRP"
  printf '  %-38s %-28s | %-28s | %s\n' "comprobacion" "obtenido" "esperado" "veredicto"
  printf '  %s\n' "----------------------------------------------------------------------------------------------------------"
}

note() { printf '  %s%s%s\n' "$C_DIM" "$1" "$C_OFF"; }

# ------------------------------------------------------------ safe readers ----
# Every reader returns a sentinel string on failure so the check reports FALLO
# with a legible reason instead of crashing or silently passing.

jq_raw() { # <filter> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s ausente>' "$_f"; return; }
  _out=$(jq -r "$1" "$_f" 2>/dev/null) || { printf '<%s json invalido>' "$_f"; return; }
  printf '%s' "$_out"
}

grep_count() { # <ERE> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s ausente>' "$_f"; return; }
  printf '%s' "$(grep -cE "$1" "$_f" 2>/dev/null || true)"
}

grep_count_fixed() { # <fixed string> <file>
  _f=$2
  [ -f "$_f" ] || { printf '<%s ausente>' "$_f"; return; }
  printf '%s' "$(grep -cF "$1" "$_f" 2>/dev/null || true)"
}

exists_file() { [ -f "$1" ] && printf 'presente' || printf 'ausente'; }
exists_dir()  { [ -d "$1" ] && printf 'presente' || printf 'ausente'; }

# Files in scope for filesystem scans: the packaged tree, minus VCS/build dirs.
scan_files() {
  find . \
    -path ./.git -prune -o \
    -path ./vendor -prune -o \
    -path ./node_modules -prune -o \
    -type f -print 2>/dev/null
}

printf '=========================================================================================================\n'
printf 'Gate A - Agora unidad 001 wave 1 - esqueleto e identidad\n'
printf 'repo: %s\n' "$REPO_ROOT"
printf 'rama: %s\n' "$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '<sin git>')"
printf 'fecha: %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')"
printf '=========================================================================================================\n'

# ------------------------------------------------------------- G0 preflight --
# Missing tooling is a FAILURE of the gate, not a reason to skip it.
group 'G0 - Preflight (herramientas requeridas)'
MISSING=0
# `tar` is required by G8 (packaging): it reads the tarball produced by
# `git archive`. If it is missing, G8 cannot be evaluated -> that is a FALLO.
for tool in jq composer git find grep tar; do
  if command -v "$tool" >/dev/null 2>&1; then
    got='disponible'
  else
    got="NO ENCONTRADO: $tool"
    MISSING=$((MISSING + 1))
  fi
  check "herramienta '$tool'" "$got" 'disponible'
done

if [ "$MISSING" -gt 0 ]; then
  printf '\n%sPREFLIGHT FALLIDO:%s faltan %d herramienta(s).\n' "$C_BAD" "$C_OFF" "$MISSING"
  printf 'El gate NO puede evaluarse. Instala lo que falta (brew install jq composer) y re-ejecuta.\n'
  printf 'Un gate que se salta comprobaciones en silencio es peor que no tenerlo.\n\n'
  printf '%d comprobaciones - %d fallos\n' "$N" "$M"
  exit 1
fi

# ------------------------------------------------ G1 identidad del paquete ---
group 'G1 - Identidad del paquete (composer.json)'
check 'composer.json presente'     "$(exists_file composer.json)" 'presente'
check '.name'                      "$(jq_raw '.name'    composer.json)" 'drupal/agora_transparency'
check '.type'                      "$(jq_raw '.type'    composer.json)" 'drupal-recipe'
check '.license'                   "$(jq_raw '.license' composer.json)" 'GPL-2.0-or-later'

if [ -f composer.json ]; then
  CV_OUT=$(composer validate --strict --no-interaction 2>&1)
  CV_RC=$?
  check 'composer validate --strict (exit)' "$CV_RC" '0'
  if [ "$CV_RC" -ne 0 ]; then
    printf '%s' "$CV_OUT" | sed 's/^/      | /'
  fi
else
  check 'composer validate --strict (exit)' '<composer.json ausente>' '0'
fi

# ---------------------------------------------------------- G2 recipe.yml ----
group 'G2 - recipe.yml (raiz, type: Site case-sensitive)'
check 'recipe.yml presente'        "$(exists_file recipe.yml)" 'presente'
check 'lineas "^type: Site" (exacto)'  "$(grep_count '^type:[[:space:]]*Site$' recipe.yml)" '1'
check 'lineas "^type:" (sin duplicar)' "$(grep_count '^type:' recipe.yml)"                  '1'
check 'lineas "^name:"'                "$(grep_count '^name:' recipe.yml)"                  '1'
check 'lineas "^description:"'         "$(grep_count '^description:' recipe.yml)"           '1'

# -------------------------------------------- G3 andamiaje del kit borrado ---
group 'G3 - Andamiaje del starter kit eliminado (T-103, T-104)'
check 'ocurrencias de _comment'    "$(grep_count_fixed '_comment' composer.json)" '0'
# ---------------------------------------------------------------------------
# DEUDA CON DUENO Y GATE DE SALIDA - NO ES UNA COMPROBACION RELAJADA.
#
# Esperado = PRESENTE, a proposito. Rider del tema `blank`, firmado por [andres]
# el 2026-08-21 (specs/000-proyecto/DECISIONES.md, "Riders on wave 1"):
#
#   "`blank` and the `extra.drupal-site-template` block are kept until unit 002.
#    T-103 deletes the three `_comment` arrays and `GET-STARTED.md`, but NOT the
#    `extra` block. [...] the affected check is adjusted to the specification in
#    force for unit 001 (`blank` and the `extra` block are expected to be
#    PRESENT, with a reference to this rider)."
#
# Por que es la especificacion vigente y no una excusa: `recipe.yml` instala hoy
# el tema `blank` (`install: - blank`) y lo fija como tema por defecto
# (`system.theme.default: 'blank'`). Ese tema NO se versiona: lo FABRICA
# drupal/site_template_helper leyendo exactamente este bloque `extra`. Borrarlo
# hoy dejaria `recipe.yml` apuntando a un tema inexistente y la plantilla NO
# instalaria. El bloque es, en la unidad 001, un requisito duro; esperar
# 'ausente' era describir el estado final de la unidad 002, no el contrato
# vigente.
#
# QUIEN LA REVIERTE: la tarea de la UNIDAD 002 que hace el cambio atomico
# `drupal/agora_theme` (D-014, opcion B) en UN SOLO COMMIT:
#     borrar .extra["drupal-site-template"] de composer.json
#   + anadir "drupal/agora_theme" a .require
#   + cambiar `- blank` por `- agora_theme` en `install:` de recipe.yml
#   + cambiar system.theme.default a 'agora_theme'
# Esa misma tarea DEBE volver a poner 'ausente' aqui. Esta linea es el tripwire:
# en cuanto alguien borre el bloque `extra` sin tocar el gate, esta comprobacion
# se pone en ROJO y el cambio no pasa. Es deliberado (I-020: "la deuda conocida
# es una tarea con dueno y un gate de salida, nunca un rojo tolerado").
#
# PROHIBIDO: cambiar este esperado a 'ausente' -- o borrar la comprobacion --
# sin ejecutar en el mismo commit los cuatro pasos de arriba.
# ---------------------------------------------------------------------------
check '.extra["drupal-site-template"]' "$(jq_raw '.extra["drupal-site-template"] // "ausente" | if . == "ausente" then . else "presente" end' composer.json)" 'presente'
note 'esperado PRESENTE por el rider de `blank` [andres] 2026-08-21; lo revierte la tarea del tema de la unidad 002 (cambio atomico)'
check 'GET-STARTED.md'             "$(exists_file GET-STARTED.md)" 'ausente'
# NOTA: se usa find, no el glob `*.example` del dispatch: el glob del shell NO
# casa con dotfiles y el kit trae `.gitignore.example` / `.gitattributes.example`.
EXAMPLES=$(find . -maxdepth 1 -name '*.example' -type f 2>/dev/null | wc -l | tr -d ' ')
check 'ficheros *.example en raiz'  "$EXAMPLES" '0'
if [ "$EXAMPLES" != "0" ]; then
  find . -maxdepth 1 -name '*.example' -type f | sed 's/^/      | /'
fi

# ------------------------------- G4 invariantes estructurales (I-014) --------
group 'G4 - Invariantes estructurales (espeja RequirementsTest del kit)'
SCANNED=$(scan_files | wc -l | tr -d ' ')
note "ambito: arbol del paquete sin .git/ vendor/ node_modules/ - $SCANNED ficheros escaneados"
check 'ficheros escaneados > 0'    "$([ "$SCANNED" -gt 0 ] && echo 'si' || echo 'no')" 'si'
INFOYML=$(scan_files | grep -c '\.info\.yml$' || true)
check 'ficheros *.info.yml'        "$INFOYML" '0'
if [ "$INFOYML" != "0" ]; then
  scan_files | grep '\.info\.yml$' | sed 's/^/      | /'
fi
check 'directorio recipes/'        "$(exists_dir recipes)" 'ausente'
check 'directorio themes/'         "$(exists_dir themes)"  'ausente'
check 'directorio modules/'        "$(exists_dir modules)" 'ausente'

# ------------------------- G5 sin pins, parches ni escape hatches (I-015) ----
group 'G5 - Sin pins, sin parches, sin escape hatches'
REQ_COUNT=$(jq_raw '.require | to_entries | length' composer.json)
check 'entradas en .require > 0'   "$([ "$REQ_COUNT" -gt 0 ] 2>/dev/null && echo 'si' || echo 'no')" 'si'
note "require: $REQ_COUNT entradas"
PINNED=$(jq_raw '[.require | to_entries[] | select(.value|test("^v?[0-9]+\\.")) | .key] | length' composer.json)
check 'versiones pineadas'         "$PINNED" '0'
[ "$PINNED" != "0" ] && jq -r '.require | to_entries[] | select(.value|test("^v?[0-9]+\\.")) | "      | \(.key): \(.value)"' composer.json 2>/dev/null
UNSTABLE=$(jq_raw '[.require | to_entries[] | select(.value|test("dev|alpha|beta|rc";"i")) | .key] | length' composer.json)
check 'constraints dev/alpha/beta/rc' "$UNSTABLE" '0'
[ "$UNSTABLE" != "0" ] && jq -r '.require | to_entries[] | select(.value|test("dev|alpha|beta|rc";"i")) | "      | \(.key): \(.value)"' composer.json 2>/dev/null
check '.extra.patches'             "$(jq_raw 'if (.extra.patches // null) == null then "ausente" else "presente" end' composer.json)" 'ausente'
# NOTA: no se busca la simple mencion de CI_ALLOW_DEV (RequirementsTest.php del
# kit la LEE con getenv() y no puede tocarse - T-406). Se busca su DEFINICION.
CIALLOW=$(scan_files | xargs grep -lIE 'CI_ALLOW_DEV[[:space:]]*[:=]' 2>/dev/null | wc -l | tr -d ' ')
check 'ficheros que DEFINEN CI_ALLOW_DEV' "$CIALLOW" '0'
[ "$CIALLOW" != "0" ] && scan_files | xargs grep -lIE 'CI_ALLOW_DEV[[:space:]]*[:=]' 2>/dev/null | sed 's/^/      | /'

# ------------------------------------- G6 ficheros del kit presentes ---------
# `ValidationTest.php` entra aqui por el rider de correcciones de especificacion
# [andres] 2026-08-21: "ValidationTest.php is added to the set of kit files
# watched by the gate." Son los tres tests que trae el kit: T-406 prohibe
# modificarlos, asi que el gate vigila que sigan existiendo.
group 'G6 - Ficheros del starter kit presentes (T-101) - 13/13'
for f in \
  recipe.yml \
  composer.json \
  recommended.yml \
  screenshot.webp \
  LICENSE.txt \
  README.md \
  .gitlab-ci.yml \
  .github/workflows/phpunit.yml \
  .tugboat/config.yml \
  .gitattributes \
  tests/src/Functional/InstallTest.php \
  tests/src/Functional/ValidationTest.php \
  tests/src/Kernel/RequirementsTest.php
do
  check "$f" "$(exists_file "$f")" 'presente'
done

# ------------------------------------- G7 coherencia con lo firmado ----------
group 'G7 - Coherencia de la capa de proceso con lo firmado'
D011=$(grep_count_fixed 'D-011' specs/000-proyecto/DECISIONES.md)
check 'D-011 en DECISIONES.md (>=1)' "$([ "${D011:-0}" -ge 1 ] 2>/dev/null && echo 'si' || echo 'no')" 'si'
NAME_REF=$(grep_count_fixed 'agora_transparency' composer.json)
check 'agora_transparency en composer.json (>=1)' "$([ "${NAME_REF:-0}" -ge 1 ] 2>/dev/null && echo 'si' || echo 'no')" 'si'

# --------------------------------------------------- G8 packaging (D-015.2) --
# El unico muro que impide que la capa de proceso (specs/, .claude/, CLAUDE.md)
# viaje dentro del release publicado en Drupal.org es el `export-ignore` de
# `.gitattributes` (D-015.2). Si alguien se lleva una linea por delante, nadie se
# entera hasta la revision del marketplace.
#
# Por eso este grupo NO hace grep sobre `.gitattributes`: EJECUTA `git archive` y
# mira el tarball resultante, que es literalmente lo que empaqueta Drupal.org.
# Un grep verificaria el texto de la regla; esto verifica su efecto.
#
# AMBITO: el arbol de HEAD. `git archive <commit>` lee los atributos del propio
# commit, no del working tree -- igual que el empaquetador de Drupal.org, que
# archiva un tag. Consecuencia conocida: una edicion de `.gitattributes` aun sin
# commitear no la ve este grupo; se caza en el commit siguiente (y en CI).
group 'G8 - Packaging: contenido real de `git archive` (D-015.2)'

ARCHIVE_LIST=""
ARCHIVE_RC=1
if git rev-parse --verify HEAD >/dev/null 2>&1; then
  ARCHIVE_LIST=$(git archive --format=tar HEAD 2>/dev/null | tar -tf - 2>/dev/null)
  ARCHIVE_RC=$?
fi
if [ "$ARCHIVE_RC" -eq 0 ] && [ -n "$ARCHIVE_LIST" ]; then
  ENTRIES=$(printf '%s\n' "$ARCHIVE_LIST" | wc -l | tr -d ' ')
else
  # Fallo al archivar = FALLO del gate, jamas un skip. I-007: un archivo vacio
  # pasaria todas las comprobaciones de "no contiene" por la puerta de atras.
  ENTRIES=0
fi
note "HEAD: $(git rev-parse --short HEAD 2>/dev/null || echo '<sin git>') - entradas en el tarball: $ENTRIES"
check 'git archive ejecutable (exit)'  "$ARCHIVE_RC" '0'
check 'entradas en el tarball > 0'     "$([ "$ENTRIES" -gt 0 ] && echo 'si' || echo 'no')" 'si'

# --- NO debe viajar: capa de proceso, CI, tests y tooling de desarrollo -------
for excluded in \
  'specs/' \
  '.claude/' \
  'CLAUDE.md' \
  'tests/' \
  '.github/' \
  '.gitlab-ci.yml' \
  '.tugboat/' \
  '.eslintrc.json'
do
  # Prefijo anclado al inicio: 'tests/' casa 'tests/...' y la entrada de
  # directorio 'tests/'; 'CLAUDE.md' casa la entrada exacta del fichero.
  HITS=$(printf '%s\n' "$ARCHIVE_LIST" | grep -c "^$(printf '%s' "$excluded" | sed 's/[.[\*^$\/]/\\&/g')" 2>/dev/null || true)
  check "NO empaquetado: $excluded" "${HITS:-0}" '0'
  if [ "${HITS:-0}" != "0" ]; then
    printf '%s\n' "$ARCHIVE_LIST" | grep "^$(printf '%s' "$excluded" | sed 's/[.[\*^$\/]/\\&/g')" | sed 's/^/      | SE CUELA EN EL RELEASE: /'
  fi
done

# --- SI debe viajar: el producto que instala el usuario final -----------------
# AGENTS.md esta aqui a proposito (D-015.1): es producto, no proceso.
for included in \
  AGENTS.md \
  recipe.yml \
  composer.json \
  recommended.yml \
  screenshot.webp \
  LICENSE.txt
do
  FOUND=$(printf '%s\n' "$ARCHIVE_LIST" | grep -cx -F "$included" 2>/dev/null || true)
  check "empaquetado: $included" "$([ "${FOUND:-0}" -ge 1 ] && echo 'presente' || echo 'ausente')" 'presente'
done

# ----------------------------------------------------------------- resumen ---
printf '\n=========================================================================================================\n'
if [ "$M" -eq 0 ]; then
  printf '%s%d comprobaciones - %d fallos%s\n' "$C_OK" "$N" "$M" "$C_OFF"
else
  printf '%s%d comprobaciones - %d fallos%s\n' "$C_BAD" "$N" "$M" "$C_OFF"
fi
printf '=========================================================================================================\n'

[ "$M" -eq 0 ] && exit 0
exit 1
