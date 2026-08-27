#!/usr/bin/env python3
# no-varchar-aggregate.py - Agora - unit 003, D-040.
#
# The parser behind tests/bin/no-varchar-aggregate. It is a matcher, not an
# invariant: it owns no scope, prints no summary and decides no exit status. It
# reads a file list, emits tab-separated records, and exits non-zero only when
# it could not do its job - which the caller turns into a FATAL, because a
# parser that did not run is not a parser that found nothing (I-027).
#
# Records, one per line, tab separated:
#   COUNT   <name>  <n>
#   NOTE    <text>
#   FINDING <path>  <line>  <reason>
#
# NO NEW TOOL AND NO NEW LIBRARY. python3 is certified on this host by
# tests/bin/doctor and exercised by gate-a-wave3.sh's preflight (I-026). Only
# the standard library is imported, so the toolchain floor set by T-803 does not
# move. In particular PyYAML is NOT imported: it is present on the author's
# host and absent from the CI image, and an invariant that is green because its
# parser could not load is the worst shape a gate can take. The block-YAML
# subset that Drupal's config exporter emits is parsed here directly - see
# WHAT THIS PARSER ACCEPTS below - and it was cross-checked against PyYAML on a
# host that has it before being trusted.
#
# ---------------------------------------------------------------------------
# WHAT IT LOOKS FOR, AND WHY THAT EXACT SHAPE
# ---------------------------------------------------------------------------
# D-040, signed 2026-08-27. A Views display with `group_by: true` that carries a
# FIELD with `group_type: sum` (or `avg`, or `stddev_pop`) over a Field API
# field table emits SQL that sums a text column.
#
# The cause is core's `FieldPluginBase::addAdditionalFields()`: an aggregated
# field handler drags its table's companion columns - `langcode` and `bundle`,
# both `varchar` - into the SAME aggregate. Core issues [#2975149] and
# [#3018025] are open since 2018 and patching is closed by rule 1.
#
# Measured on all three databases rather than reasoned about:
#   PostgreSQL 16.15  ERROR: function sum(character varying) does not exist
#   MariaDB           0, with "Warning 1292 Truncated incorrect DOUBLE value"
#   SQLite            0.0, silently
#
# Read those three rows again: the query is wrong EVERYWHERE. PostgreSQL is the
# only one honest enough to refuse. That is why this check is worth a gate slot
# and not a note in a README - two of the three databases pass it while lying.
#
# THE TWO HALVES OF THE PREDICATE, and neither is optional:
#
#   1. `group_type` in {sum, avg, stddev_pop}. These three are the aggregate
#      functions that TAKE A NUMERIC ARGUMENT. `count`, `count_distinct`, `min`,
#      `max` and `group` are measured safe on all three databases: COUNT accepts
#      anything, and MIN/MAX are defined over text. So this is a deny-list of
#      three, not an allow-list of the rest, and it is named in full below so
#      that shortening it is a visible edit.
#
#   2. a `table` matching `^[a-z_]+__field_`. That is the shape of a Field API
#      field's dedicated table (`node__field_agora_base_amount`) as opposed to
#      an entity BASE table (`node_field_data`). Only the dedicated table has
#      the `langcode`/`bundle` companions that get dragged in; a base-table
#      column has none, so `SUM()` over one is safe and is not a finding.
#
# WHAT IS DELIBERATELY OUT OF SCOPE: `sorts`. A SORT with `group_type: sum` over
# the very same field is CLEAN, and that is measured, not assumed -
# `GroupByNumeric::query()` never calls `addAdditionalFields()`, so the SQL it
# emits sums the numeric column alone. D-040's signed remedy KEEPS that sort, so
# a check that flagged it would be red on the correct configuration. Sorts are
# counted and printed as a census so that nobody reads this file and concludes
# they were scanned.
#
# ---------------------------------------------------------------------------
# INHERITANCE, which is where a naive version of this check goes quietly blind
# ---------------------------------------------------------------------------
# A Views display does not have to own its `group_by` or its `fields`: an
# unlisted key in `defaults` means INHERIT, and `defaults` itself may be absent,
# which means inherit everything. A display that inherits aggregating fields
# from `default` is exactly as broken as one that declares them, and a check
# that only read each display's own keys would report zero on it. Both keys are
# therefore resolved through `defaults` against the `default` display before the
# predicate is applied, and an inherited finding says so in its reason.
#
# ---------------------------------------------------------------------------
# WHAT THIS PARSER ACCEPTS
# ---------------------------------------------------------------------------
# The block-YAML subset Drupal's exporter emits, and nothing more: 2-space
# block mappings and sequences, single/double-quoted scalars, the empty flow
# collections `{  }` and `[  ]`, and `#` comments. It rejects, loudly, anything
# it cannot place - a block scalar (`|`, `>`), an anchor or a tag - rather than
# skipping the line. A file it cannot parse is a FATAL for the caller, never a
# file with no findings: the failure mode this whole check exists to prevent is
# a green that was never a measurement.

import argparse
import re
import sys

# --------------------------------------------------------------------------
# THE DENY-LIST - three aggregate functions that require a numeric argument.
# Named here in one place so that shortening it is a diff, not an accident.
# tests/bin/no-varchar-aggregate asserts this list is non-empty before it
# reports anything: a degenerate list of zero terms prints "0 findings" and
# passes by construction (I-028).
# --------------------------------------------------------------------------
UNSAFE_GROUP_TYPES = ('sum', 'avg', 'stddev_pop')

# Measured safe on PostgreSQL, MariaDB and SQLite. Printed as a census, never
# used as an allow-list: an unknown group_type is not a finding here, because
# the deny-list above is the thing that was measured.
SAFE_GROUP_TYPES = ('count', 'count_distinct', 'group', 'max', 'min')

# A Field API field's dedicated table: `node__field_foo`, `paragraph__field_x`.
# An entity base table (`node_field_data`, `users_field_data`) does not match,
# and that is the point - it has no langcode/bundle companions to drag in.
FIELD_TABLE_RE = re.compile(r'^[a-z_]+__field_')


class YamlError(Exception):
    """The parser met something it will not guess about."""


def tokenize(text, path):
    """Line list -> [(indent, content, lineno)], comments and blanks dropped."""
    tokens = []
    for lineno, raw in enumerate(text.splitlines(), 1):
        stripped = raw.strip()
        if not stripped or stripped.startswith('#'):
            continue
        if '\t' in raw[:len(raw) - len(raw.lstrip())]:
            raise YamlError('%s:%d: tab in indentation' % (path, lineno))
        indent = len(raw) - len(raw.lstrip(' '))
        tokens.append((indent, stripped, lineno))
    return tokens


DQ_ESCAPES = {'n': '\n', 't': '\t', 'r': '\r', '0': '\0',
              '"': '"', '\\': '\\', '/': '/'}


def unescape_double(text):
    """The double-quoted escapes Drupal's exporter emits, and no others.

    `\\uXXXX` is not decoded: the exporter writes UTF-8 directly and has never
    been observed emitting one here. An unknown escape keeps its backslash
    rather than being dropped, so a value this function cannot fully decode
    still reads as wrong instead of reading as something else.
    """
    out = []
    i = 0
    while i < len(text):
        char = text[i]
        if char == '\\' and i + 1 < len(text):
            nxt = text[i + 1]
            if nxt in DQ_ESCAPES:
                out.append(DQ_ESCAPES[nxt])
                i += 2
                continue
        out.append(char)
        i += 1
    return ''.join(out)


def scalar(text):
    """A quoted or bare YAML scalar -> python value. Booleans are resolved.

    Numbers are deliberately NOT resolved: every non-boolean scalar stays text.
    This check compares `group_type` and `table` as strings and `group_by`
    against `True`, so number resolution would be surface with no reader.
    """
    if text.startswith("'") and text.endswith("'") and len(text) >= 2:
        return text[1:-1].replace("''", "'")
    if text.startswith('"') and text.endswith('"') and len(text) >= 2:
        return unescape_double(text[1:-1])
    if text in ('{}', '{  }', '{ }'):
        return {}
    if text in ('[]', '[  ]', '[ ]'):
        return []
    if text == 'true':
        return True
    if text == 'false':
        return False
    if text in ('null', '~'):
        return None
    return text


def split_kv(content, path, lineno):
    """`key: value` or `key:` -> (key, value-text). Quoted keys handled."""
    if content[0] in "'\"":
        quote = content[0]
        i = 1
        while i < len(content):
            if content[i] == quote:
                if quote == "'" and content[i:i + 2] == "''":
                    i += 2
                    continue
                break
            i += 1
        else:
            raise YamlError('%s:%d: unterminated quoted key' % (path, lineno))
        key = scalar(content[:i + 1])
        rest = content[i + 1:]
        if not rest.startswith(':'):
            raise YamlError('%s:%d: quoted key not followed by ":"'
                            % (path, lineno))
        return key, rest[1:].strip()

    # The first colon that ends a key is the first one followed by a space or
    # by end-of-line. A colon inside a value ("Total: 3") is not a key
    # separator, and neither is one inside a rewrite's HTML.
    for match in re.finditer(':', content):
        pos = match.start()
        if pos + 1 == len(content) or content[pos + 1] == ' ':
            return content[:pos], content[pos + 1:].strip()
    raise YamlError('%s:%d: not a mapping entry: %r' % (path, lineno, content))


def parse_block(tokens, i, indent, path):
    """Dispatch on the first token of a block: sequence or mapping."""
    if tokens[i][1] == '-' or tokens[i][1].startswith('- '):
        return parse_seq(tokens, i, indent, path)
    return parse_map(tokens, i, indent, path)


def parse_map(tokens, i, indent, path):
    """Block mapping -> {key: (value, lineno)}."""
    node = {}
    while i < len(tokens):
        token_indent, content, lineno = tokens[i]
        if token_indent < indent:
            break
        if token_indent > indent:
            raise YamlError('%s:%d: unexpected indent %d inside a mapping at %d'
                            % (path, lineno, token_indent, indent))
        if content == '-' or content.startswith('- '):
            break
        reject_unsupported(content, path, lineno)
        key, value_text = split_kv(content, path, lineno)
        i += 1
        if value_text == '':
            if i < len(tokens) and tokens[i][0] > indent:
                child, i = parse_block(tokens, i, tokens[i][0], path)
                node[key] = (child, lineno)
            elif (i < len(tokens) and tokens[i][0] == indent
                    and (tokens[i][1] == '-' or tokens[i][1].startswith('- '))):
                # A sequence written at its parent key's own indent. Drupal's
                # exporter indents them, but YAML permits this and guessing
                # wrong here would silently swallow the rest of the mapping.
                child, i = parse_seq(tokens, i, indent, path)
                node[key] = (child, lineno)
            else:
                node[key] = (None, lineno)
        else:
            node[key] = (scalar(value_text), lineno)
    return node, i


def parse_seq(tokens, i, indent, path):
    """Block sequence -> [(value, lineno)]."""
    items = []
    while i < len(tokens):
        token_indent, content, lineno = tokens[i]
        if token_indent != indent or not (content == '-'
                                        or content.startswith('- ')):
            break
        body = content[1:].strip()
        i += 1
        if body == '':
            if i < len(tokens) and tokens[i][0] > indent:
                child, i = parse_block(tokens, i, tokens[i][0], path)
                items.append((child, lineno))
            else:
                items.append((None, lineno))
            continue
        reject_unsupported(body, path, lineno)
        # `- key: value` opens a mapping whose first entry shares the dash's
        # line. Its continuation lines are indented past the dash.
        try:
            key, value_text = split_kv(body, path, lineno)
        except YamlError:
            items.append((scalar(body), lineno))
            continue
        child_indent = indent + 2
        head = {}
        if value_text == '':
            if i < len(tokens) and tokens[i][0] > child_indent:
                sub, i = parse_block(tokens, i, tokens[i][0], path)
                head[key] = (sub, lineno)
            else:
                head[key] = (None, lineno)
        else:
            head[key] = (scalar(value_text), lineno)
        if i < len(tokens) and tokens[i][0] == child_indent:
            rest, i = parse_map(tokens, i, child_indent, path)
            head.update(rest)
        items.append((head, lineno))
    return items, i


def reject_unsupported(content, path, lineno):
    """Refuse to guess about YAML this parser does not implement."""
    if content.startswith('&') or content.startswith('*'):
        raise YamlError('%s:%d: anchors and aliases are not supported'
                        % (path, lineno))
    if content.startswith('!'):
        raise YamlError('%s:%d: tags are not supported' % (path, lineno))
    if re.search(r':\s*[|>][-+0-9]*$', content):
        raise YamlError('%s:%d: block scalars are not supported'
                        % (path, lineno))


def parse_document(text, path):
    tokens = tokenize(text, path)
    if not tokens:
        return {}
    if tokens[0][0] != 0:
        raise YamlError('%s: document does not start at column 0' % path)
    node, i = parse_block(tokens, 0, 0, path)
    if i != len(tokens):
        raise YamlError('%s:%d: trailing content the parser could not place'
                        % (path, tokens[i][2]))
    return node


# --------------------------------------------------------------------------
# Accessors. Every mapping value is a (value, lineno) pair; these two hide it.
# --------------------------------------------------------------------------
def val(node, key, default=None):
    if isinstance(node, dict) and key in node:
        return node[key][0]
    return default


def line(node, key, default=0):
    if isinstance(node, dict) and key in node:
        return node[key][1]
    return default


def inherits(display_options, key, display_id):
    """Does this display take `key` from the `default` display?

    An unlisted key in `defaults` means inherit, and an absent `defaults`
    means inherit everything. Only an explicit `false` means "this display
    owns it". The `default` display inherits from nobody.
    """
    if display_id == 'default':
        return False
    defaults = val(display_options, 'defaults')
    if not isinstance(defaults, dict) or key not in defaults:
        return True
    return val(defaults, key) is True


def main():
    parser = argparse.ArgumentParser(add_help=True)
    parser.add_argument('files', nargs='+')
    args = parser.parse_args()

    out = []
    findings = 0
    displays_total = 0
    displays_aggregating = 0
    fields_inspected = 0
    unsafe_sorts = 0

    for path in args.files:
        try:
            with open(path, encoding='utf-8') as handle:
                text = handle.read()
            doc = parse_document(text, path)
        except (OSError, UnicodeDecodeError, YamlError) as error:
            sys.stderr.write('no-varchar-aggregate.py: %s\n' % error)
            return 1

        displays = val(doc, 'display')
        if not isinstance(displays, dict):
            out.append('NOTE\t%s carries no `display` mapping - not a view?'
                       % path)
            continue

        default_options = val(val(displays, 'default'), 'display_options') or {}

        for display_id in displays:
            displays_total += 1
            options = val(displays, display_id)
            if not isinstance(options, dict):
                continue
            options = val(options, 'display_options') or {}

            source = default_options if inherits(options, 'group_by',
                                                 display_id) else options
            if val(source, 'group_by') is not True:
                continue
            displays_aggregating += 1

            inherited = inherits(options, 'fields', display_id)
            fields = val(default_options if inherited else options, 'fields')
            if not isinstance(fields, dict):
                continue

            for field_id in fields:
                entry = val(fields, field_id)
                if not isinstance(entry, dict):
                    continue
                fields_inspected += 1
                group_type = val(entry, 'group_type')
                table = val(entry, 'table')
                if group_type not in UNSAFE_GROUP_TYPES:
                    continue
                if not isinstance(table, str) \
                        or not FIELD_TABLE_RE.match(table):
                    continue
                findings += 1
                out.append('FINDING\t%s\t%d\t%s' % (
                    path,
                    line(fields, field_id),
                    'display %s%s: field `%s` aggregates `%s` over the Field '
                    'API table `%s`. Views drags that table\'s varchar '
                    '`langcode` and `bundle` into the same aggregate - '
                    'PostgreSQL refuses the query, MariaDB and SQLite return 0. '
                    'Remove the aggregated field; a SORT with the same '
                    'group_type is safe (D-040).' % (
                        display_id,
                        ' (fields inherited from `default`)'
                        if inherited else '',
                        field_id, group_type, table),
                ))

            sorts = val(default_options if inherits(options, 'sorts',
                                                    display_id)
                        else options, 'sorts')
            if isinstance(sorts, dict):
                for sort_id in sorts:
                    entry = val(sorts, sort_id)
                    if isinstance(entry, dict) \
                            and val(entry, 'group_type') in UNSAFE_GROUP_TYPES:
                        unsafe_sorts += 1

    out.append('COUNT\tdisplays\t%d' % displays_total)
    out.append('COUNT\taggregating_displays\t%d' % displays_aggregating)
    out.append('COUNT\tfields_inspected\t%d' % fields_inspected)
    out.append('COUNT\tunsafe_terms\t%d' % len(UNSAFE_GROUP_TYPES))
    out.append('COUNT\taggregating_sorts\t%d' % unsafe_sorts)
    out.append('NOTE\tdeny-list: %s' % ', '.join(UNSAFE_GROUP_TYPES))
    out.append('NOTE\tmeasured safe, not flagged: %s'
               % ', '.join(SAFE_GROUP_TYPES))
    out.append('COUNT\tfindings\t%d' % findings)

    sys.stdout.write('\n'.join(out) + '\n')
    return 0


if __name__ == '__main__':
    sys.exit(main())
