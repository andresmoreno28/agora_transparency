#!/usr/bin/env python3
# content-timestamps.py - Agora - demo corpus tool.
#
# ===========================================================================
# WHY THIS FILE EXISTS AT ALL, SAID FIRST, BECAUSE IT IS THE WHOLE ARGUMENT
# ===========================================================================
# CLAUDE.md's rule is that `content/` is produced by `drush content:export`,
# never hand-written. This file is the ONE documented exception, and it is not
# a preference - it is forced by core, in code that can be read:
#
#   web/core/lib/Drupal/Core/DefaultContent/Exporter.php
#     // Ignore fields that don't make sense in default content:
#     // - `changed` fields aren't needed because default content has no history
#     // - `created` fields aren't needed because default content should be
#     //   "created" upon import.
#     foreach ($field_definitions as $name => $definition) {
#       if (in_array($definition->getType(), ['changed', 'created'], TRUE)) {
#         $event->setExportable($name, FALSE);
#       }
#     }
#
# THE EXPORTER DELETES THESE TWO FIELDS ON PURPOSE. So every node this package
# ships is imported with `created` and `changed` set to the moment of import -
# one instant, identical across all 56 records. On a TRANSPARENCY portal that
# is the single field a visitor uses to judge whether the data is alive, and a
# corpus whose every record was last touched in the same minute reads as fake.
#
# The IMPORTER, by contrast, honours both. `Importer::toEntity()` walks
# `$data['default']` with no allow-list and calls `setFieldValues()` on every
# key it finds, and `ChangedItem::preSave()` overwrites a supplied value only
# when `!$entity->isNew()` - which is never true on import. Read, not assumed.
#
# So the pipeline is: export (which strips) -> THIS SCRIPT (which restores).
# Run it after every `drush content:export` that touches `content/node/`, or
# the corpus silently goes back to one instant.
#
# ===========================================================================
# DETERMINISTIC, AND WHY THAT IS THE HARD REQUIREMENT
# ===========================================================================
# `time()` is banned here. The corpus uses v5 UUIDs precisely so that a re-run
# produces a zero-line diff, and a timestamp drawn from the clock would make
# every re-export a diff nobody can review. Every value below is derived from
# `sha256(uuid)` plus dates the record ALREADY CARRIES - a financial year, a
# contract period, the year and month encoded in the file it publishes.
#
# Running this script twice produces byte-identical output. `--check` proves
# it without writing: it recomputes every value and reports mismatches.
#
# ===========================================================================
# PLAUSIBILITY IS A CONSTRAINT, NOT A DECORATION
# ===========================================================================
# The dates are not scattered at random across 2022-2025. Each record's
# `created` is anchored to a date the record itself states, so that:
#
#   - minutes of the meeting of 25 April 2024 are not published in January;
#   - the annual accounts for 2022 are not created before 2022 ended;
#   - a contract record does not predate the contract;
#   - `changed >= created` for every record, always;
#   - nothing is dated after CEILING, so the corpus has a coherent "now".
#
# NO NEW TOOL: python3 only, standard library only, so the toolchain floor set
# by T-803 does not move.

import argparse
import hashlib
import os
import re
import sys
from datetime import datetime, timedelta, timezone

# The corpus's "now". Every record is created and last changed on or before
# this date, so the demo has one coherent present instead of a scatter that
# runs past the content it describes.
CEILING = datetime(2025, 12, 15, 12, 0, 0, tzinfo=timezone.utc)

# The council term the org chart belongs to. Person records carry no date of
# their own - a post is not a financial year - so this is the one anchor in
# the file that is chosen rather than read out of the content. It is fiction,
# like every person on it (see content/PEOPLE.md).
COUNCIL_TERM_START = datetime(2023, 6, 15, tzinfo=timezone.utc)

# Document types whose record can only exist AFTER the period it covers.
RETROSPECTIVE_TYPES = {
    'Annual accounts',
    'Report',
    'Council meeting minutes',
}

REPO = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
CONTENT = os.path.join(REPO, 'content')

# `-YYYY-MM.` and `-YYYY-qN.` in a published file's name. The corpus encodes
# the meeting month and the reporting quarter there, and using it is what keeps
# minutes from being "published" before the meeting they minute.
MONTH_IN_NAME = re.compile(r'-(\d{4})-(0[1-9]|1[0-2])\.')
QUARTER_IN_NAME = re.compile(r'-(\d{4})-q([1-4])\.')


def digest(uuid: str) -> bytes:
    """The single source of every derived number in this file."""
    return hashlib.sha256(uuid.encode('utf-8')).digest()


def draw(seed: bytes, index: int, span: int) -> int:
    """A stable integer in [0, span) from two bytes of the digest."""
    if span <= 0:
        return 0
    return ((seed[index] << 8) | seed[index + 1]) % span


def end_of_month(year: int, month: int) -> datetime:
    first_next = datetime(year + (month // 12), (month % 12) + 1, 1, tzinfo=timezone.utc)
    return first_next - timedelta(days=1)


def read_yaml_scalars(path: str) -> dict:
    """A deliberately tiny reader for the exporter's own YAML dialect.

    This package ships no YAML library and T-803's toolchain floor is not
    moved for a content tool. The exporter's output is two-space indented,
    block style throughout, with no anchors, no flow collections and no
    multi-line scalars - so the handful of shapes this script needs can be
    read by matching lines. Anything it cannot read, it does not claim to:
    the callers below ask only for keys they know are simple.
    """
    values = {}
    field = None
    with open(path, encoding='utf-8') as handle:
        for line in handle:
            stripped = line.rstrip('\n')
            match = re.match(r'^  ([A-Za-z0-9_]+):$', stripped)
            if match:
                field = match.group(1)
                continue
            if field is None:
                continue
            match = re.match(r"^      ([A-Za-z0-9_]+): '?(.*?)'?$", stripped)
            if match:
                values.setdefault(field, {}).setdefault(match.group(1), match.group(2))
    return values


def load_index(kind: str) -> dict:
    index = {}
    directory = os.path.join(CONTENT, kind)
    for name in sorted(os.listdir(directory)):
        if not name.endswith('.yml'):
            continue
        index[name[:-4]] = read_yaml_scalars(os.path.join(directory, name))
    return index


def bundle_of(path: str) -> str:
    with open(path, encoding='utf-8') as handle:
        for line in handle:
            match = re.match(r'^  bundle: (\S+)$', line.rstrip('\n'))
            if match:
                return match.group(1)
    return ''


def anchor_for(bundle: str, node: dict, terms: dict, media: dict, files: dict) -> tuple:
    """The date the record's own content says it belongs to, and its lag band.

    Returns (anchor, lag_low, lag_high): `created` lands lag_low..lag_high days
    after the anchor. The bands are editorial reality, not padding - minutes
    are approved at the next meeting, accounts months after the year closes,
    an award notice within days of the contract starting.
    """
    year_term = node.get('field_agora_base_financial_year', {}).get('entity')
    year = None
    if year_term and year_term in terms:
        label = terms[year_term].get('name', {}).get('value', '')
        if label.isdigit():
            year = int(label)

    if bundle == 'agora_base_person':
        return COUNCIL_TERM_START, 0, 45

    if bundle in ('agora_base_contract', 'agora_base_grant', 'agora_base_agreement'):
        start = node.get('field_agora_base_period', {}).get('value')
        return datetime.strptime(start, '%Y-%m-%d').replace(tzinfo=timezone.utc), 0, 21

    if bundle == 'agora_base_dataset':
        frequency = node.get('field_agora_base_frequency', {}).get('value', '')
        if frequency == 'irregular':
            return datetime(year, 6, 30, tzinfo=timezone.utc), 0, 180
        return datetime(year, 12, 31, tzinfo=timezone.utc), 20, 110

    # -- Documents: the most dated of the six, so the most anchored ----------
    filename = ''
    media_uuid = node.get('field_agora_base_document_file', {}).get('entity')
    if media_uuid and media_uuid in media:
        file_uuid = media[media_uuid].get('field_media_document', {}).get('entity')
        if file_uuid and file_uuid in files:
            filename = files[file_uuid].get('filename', {}).get('value', '')

    match = MONTH_IN_NAME.search(filename)
    if match:
        # A meeting, minuted. Approved at the following ordinary session.
        return end_of_month(int(match.group(1)), int(match.group(2))), 14, 45

    match = QUARTER_IN_NAME.search(filename)
    if match:
        # A quarterly execution report: published after the quarter closes.
        quarter_end_month = int(match.group(2)) * 3
        return end_of_month(int(match.group(1)), quarter_end_month), 20, 60

    document_type = node.get('field_agora_base_document_type', {}).get('entity')
    label = terms.get(document_type, {}).get('name', {}).get('value', '')
    if label in RETROSPECTIVE_TYPES:
        # Covers a year that must have finished first.
        return datetime(year, 12, 31, tzinfo=timezone.utc), 60, 180
    # A budget, a plan, a by-law, a notice: adopted within the year it names.
    return datetime(year, 1, 1, tzinfo=timezone.utc), 30, 300


def timestamps_for(uuid: str, bundle: str, node: dict, terms: dict, media: dict, files: dict) -> tuple:
    seed = digest(uuid)
    anchor, lag_low, lag_high = anchor_for(bundle, node, terms, media, files)

    created = anchor + timedelta(days=lag_low + draw(seed, 0, lag_high - lag_low + 1))
    created = created.replace(
        hour=8 + seed[2] % 10,
        minute=seed[3] % 60,
        second=0,
        microsecond=0,
    )
    if created > CEILING:
        created = CEILING

    # A little under half the corpus has never been revised, which is what a
    # real register looks like. The rest moved at some point after publication.
    if seed[4] % 100 < 45:
        changed = created
    else:
        room = (CEILING - created).days - 7
        changed = created + timedelta(days=7 + draw(seed, 5, room + 1)) if room > 0 else created
        changed = changed.replace(hour=8 + seed[7] % 10, minute=seed[8] % 60, second=0, microsecond=0)

    if changed < created:
        changed = created
    if changed > CEILING:
        changed = CEILING

    return int(created.timestamp()), int(changed.timestamp())


def strip_existing(text: str) -> str:
    """Remove any `created:`/`changed:` block, so the script is idempotent."""
    out, drop = [], False
    for line in text.split('\n'):
        if re.match(r'^  (created|changed):$', line):
            drop = True
            continue
        if drop:
            if line.startswith('    ') or line == '':
                if line.startswith('    '):
                    continue
            drop = False
        out.append(line)
    return '\n'.join(out)


def render(created: int, changed: int) -> str:
    return (
        '  created:\n'
        '    -\n'
        f'      value: {created}\n'
        '  changed:\n'
        '    -\n'
        f'      value: {changed}\n'
    )


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--check', action='store_true', help='verify without writing')
    args = parser.parse_args()

    terms = load_index('taxonomy_term')
    media = load_index('media')
    files = load_index('file')

    directory = os.path.join(CONTENT, 'node')
    names = sorted(name for name in os.listdir(directory) if name.endswith('.yml'))
    if not names:
        print('FAILURE: 0 node files - an empty corpus proves nothing.')
        return 1

    written, mismatched, spread = 0, 0, []
    for name in names:
        path = os.path.join(directory, name)
        uuid = name[:-4]
        bundle = bundle_of(path)
        node = read_yaml_scalars(path)
        created, changed = timestamps_for(uuid, bundle, node, terms, media, files)
        spread.append((created, changed, bundle))

        text = open(path, encoding='utf-8').read()
        body = strip_existing(text).rstrip('\n') + '\n'
        wanted = body + render(created, changed)

        if args.check:
            if text != wanted:
                mismatched += 1
                print(f'MISMATCH {name}')
            continue
        if text != wanted:
            with open(path, 'w', encoding='utf-8', newline='\n') as handle:
                handle.write(wanted)
            written += 1

    created_dates = sorted(datetime.fromtimestamp(c, timezone.utc) for c, _, _ in spread)
    changed_dates = sorted(datetime.fromtimestamp(m, timezone.utc) for _, m, _ in spread)
    distinct_changed = len({m for _, m, _ in spread})
    never_revised = sum(1 for c, m, _ in spread if c == m)

    print(f'nodes read: {len(names)}')
    print(f'created span: {created_dates[0].date()} .. {created_dates[-1].date()}')
    print(f'changed span: {changed_dates[0].date()} .. {changed_dates[-1].date()}')
    print(f'distinct changed timestamps: {distinct_changed} of {len(names)}')
    print(f'never revised (changed == created): {never_revised}')
    print(f'changed < created: {sum(1 for c, m, _ in spread if m < c)}')
    if args.check:
        print(f'mismatched files: {mismatched}')
        return 1 if mismatched else 0
    print(f'files written: {written}')
    return 0


if __name__ == '__main__':
    sys.exit(main())
