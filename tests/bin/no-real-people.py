#!/usr/bin/env python3
# no-real-people.py - Agora - unit 003, wave 9, task T-905.
#
# The matcher behind tests/bin/no-real-people. It is a helper, not an invariant:
# it owns no scope, prints no summary and decides no exit status. It reads a
# file list, emits tab-separated records, and exits non-zero only when it could
# not do its job - which the caller turns into a FATAL, because a matcher that
# did not run is not a matcher that found nothing (I-027).
#
# Records, one per line, tab separated:
#   COUNT   <name>  <n>
#   NOTE    <text>
#   FINDING <path>  <line>  <reason>
#
# NO NEW TOOL. python3 is certified on this host by tests/bin/doctor and
# exercised by gate-a-wave3.sh's preflight (I-026). Only the standard library is
# imported, so the toolchain floor set by T-803 does not move.
#
# WHY PYTHON AND NOT GREP. Three of the shapes below carry CHECK DIGITS - the
# Spanish DNI and NIE letter (mod 23) and the IBAN (mod 97). A grep for
# "eight digits and a letter" fires on an ISO basic date followed by a T; the
# same pattern with its check letter verified does not. False positives are not
# a cosmetic problem here: the only route from a red false positive back to
# green is deleting a pattern, which is the move the FORBIDDEN clause exists to
# stop. Precision buys the invariant the right to be trusted.

import argparse
import re
import sys
import unicodedata

# --------------------------------------------------------------------------
# (2) THE DENY-LIST
# --------------------------------------------------------------------------
# Real, currently or recently serving Spanish public office-holders, matched as
# FULL NAMES and never as surnames - see the caller's header for why. These are
# public figures acting in a public capacity; the list exists so that a demo org
# chart copied from a real institution fails loudly rather than shipping.
#
# It is a FLOOR, not a census, and it is deliberately incomplete in one named
# way: plan.md 6.2 defines the deny-list as "the real office-holders of the
# municipalities the demo is modelled on", and the demo municipality does not
# exist yet - T-1001 invents it. WHOEVER WRITES T-1001 ADDS THE REAL
# OFFICE-HOLDERS OF WHATEVER REAL MUNICIPALITY THEY MODEL IT ON. Until then this
# list covers the nationally prominent names, which is the copy-paste accident
# most likely to happen to somebody working quickly.
#
# cspell:disable
# The names are real people's names, not this project's vocabulary. They are
# scoped out in place rather than declared in .cspell-project-words.txt, exactly
# as that file's own header prescribes for the Spanish quotations in
# DECISIONS.md: declaring them would make them permanently correct everywhere.
DENY_LIST = [
    "Pedro Sánchez",
    "Alberto Núñez Feijóo",
    "María Jesús Montero",
    "Isabel Díaz Ayuso",
    "Juanma Moreno",
    "Salvador Illa",
    "José Luis Martínez-Almeida",
    "Jaume Collboni",
    "María José Catalá",
    "José Luis Sanz",
    "Natalia Chueca",
    "Óscar Puente",
]
# cspell:enable

# --------------------------------------------------------------------------
# ALLOW-LISTS. Both are DECLARATIONS, printed on every run by the caller.
# --------------------------------------------------------------------------
# RFC 2606 / RFC 6761 reserved names, which by definition belong to nobody and
# route nowhere. An email address in demo content must use one of these.
#
# The plan's wording was "a real municipal domain (.gob.es, or an .es host that
# resolves)". This is STRICTER and deliberately so, on two grounds. First, DNS
# resolution inside an invariant makes the verdict depend on the network, and
# this project already knows what that costs: sbom-check pays it, and it pays it
# for a fact obtainable no other way. Second, "not .es" is not safety - a real
# .com mailbox in demo content is the same leak. So the rule is inverted: an
# address is a finding unless its host is reserved.
ALLOWED_EMAIL_DOMAINS = [
    "example.com",
    "example.net",
    "example.org",
    "example.edu",
    "example.es",
    "example",
    "invalid",
    "localhost",
    "test",
]

# Street addresses of ORGANISATIONS, which are not personal data. Empty today
# and printed as `0` on every run. It exists NOW rather than later because wave
# 10 will ship a town hall with an address, that address will match the postal
# shape, and the person hitting that red needs a sanctioned route that is not
# "delete the pattern". Adding a REAL PERSON's address here is the forbidden
# move wearing a hat, and the caller's FORBIDDEN clause says so.
ALLOWED_ORG_ADDRESSES = []

# --------------------------------------------------------------------------
# (3) THE SHAPES
# --------------------------------------------------------------------------
# The DNI check-letter alphabet, in the order the mod-23 remainder indexes it.
# It is a specification constant, not a word.
DNI_LETTERS = "TRWAGMYFPDXBNJZSQVHLCKE"  # cspell:disable-line

RE_DNI = re.compile(r"(?<![0-9A-Za-z])(\d{8})[-\s]?([A-Za-z])(?![0-9A-Za-z])")
RE_NIE = re.compile(r"(?<![0-9A-Za-z])([XYZxyz])[-\s]?(\d{7})[-\s]?([A-Za-z])(?![0-9A-Za-z])")
RE_IBAN = re.compile(r"(?<![0-9A-Za-z])([A-Z]{2}\d{2}(?:[ ]?[A-Z0-9]{4}){2,7}[ ]?[A-Z0-9]{1,4})(?![0-9A-Za-z])")
RE_PHONE_INTL = re.compile(r"(?<![0-9])(?:\+34|0034)[\s.-]?[6-9]\d{2}[\s.-]?\d{3}[\s.-]?\d{3}(?![0-9])")
RE_PHONE_BARE = re.compile(r"(?<![0-9+])([6-9]\d{8})(?![0-9])")
RE_EMAIL = re.compile(r"[A-Za-z0-9._%+-]+@((?:[A-Za-z0-9-]+\.)*[A-Za-z0-9-]+)")
# cspell:disable
# Spanish and Catalan street-type words. They are data being matched, not
# prose being written: declaring them in .cspell-project-words.txt would make
# them correct everywhere in an English repository, which is what that file's
# own header tells you not to do.
RE_ADDRESS = re.compile(
    r"\b(?:Calle|C/|Avenida|Avda\.?|Av\.|Plaza|Pza\.?|Paseo|Carrer|Rúa|Rua|Travesía|Camino|Ronda)"
    r"\s+[^\n,|]{2,60}?[,\s]+(?:n[.ºo°]?\s*)?\d{1,4}\b",
    re.IGNORECASE,
)
# cspell:enable

# Key names whose value may legitimately be a nine-digit number that is NOT a
# telephone. The bare national form is the only shape here with no check digit,
# so it is the only one that needs this. A nine-digit value under a key that
# looks like a phone field is a FINDING; anywhere else it is SUPPRESSED and
# COUNTED - never silently dropped.
RE_PHONE_KEY = re.compile(r"phone|tel|movil|m[oó]vil|mobile|whatsapp|contacto|contact", re.IGNORECASE)  # cspell:disable-line

# The key path at which a person node's OWN title lives, in the default language
# and in each translation. `default.title.value` and `translations.es.title.value`
# and nothing else.
RE_TITLE_PATH = re.compile(r"^(?:default|translations\.[A-Za-z_-]+)\.title\.value$")

SHAPE_NAMES = [
    "DNI (mod-23 check letter verified)",
    "NIE (mod-23 check letter verified)",
    "IBAN (mod-97 verified)",
    "telephone, international +34 form",
    "telephone, bare national form (suppressed outside a phone-like key)",
    "email address at a non-reserved domain",
    "postal address carrying a street number",
]


def dni_ok(digits, letter):
    return letter.upper() == DNI_LETTERS[int(digits) % 23]


def nie_ok(prefix, digits, letter):
    lead = {"X": "0", "Y": "1", "Z": "2"}[prefix.upper()]
    return letter.upper() == DNI_LETTERS[int(lead + digits) % 23]


def iban_ok(raw):
    s = raw.replace(" ", "").upper()
    if len(s) < 15 or len(s) > 34:
        return False
    s = s[4:] + s[:4]
    total = 0
    for ch in s:
        if ch.isdigit():
            total = (total * 10 + int(ch)) % 97
        elif "A" <= ch <= "Z":
            total = (total * 100 + (ord(ch) - 55)) % 97
        else:
            return False
    return total == 1


def normalise(text):
    """Casefold, strip accents, collapse whitespace. Used for the deny-list only.

    Accents are stripped so that a name typed without them still matches: the
    accident this list catches is somebody pasting quickly, and somebody pasting
    quickly is exactly who drops an accent.
    """
    t = unicodedata.normalize("NFD", text)
    t = "".join(c for c in t if unicodedata.category(c) != "Mn")
    return re.sub(r"\s+", " ", t).strip().casefold()


def key_path_tracker():
    """Return a callable that maps a YAML line to the enclosing key path.

    Indentation-based, which is all the exporter's output needs: core's content
    exporter emits plain block YAML with no flow mappings and no tabs. It is
    used for reporting and for the one suppression rule; nothing's verdict turns
    on it alone.
    """
    stack = []

    def feed(line):
        m = re.match(r"^(\s*)([A-Za-z_][A-Za-z0-9_.-]*):", line)
        if m:
            indent = len(m.group(1))
            while stack and stack[-1][0] >= indent:
                stack.pop()
            stack.append((indent, m.group(2)))
        return ".".join(k for _, k in stack)

    return feed


def read_stream(stdin_buffer):
    """Yield (label, text) from the framed byte stream the caller writes.

    Framing is `FILE <bytes> <label>\\n` followed by exactly that many raw
    bytes. The caller streams bytes rather than passing paths because this
    repository's working copy is a Windows checkout driven from Git Bash, where
    `mktemp -d` returns an MSYS path the native python3 cannot open - and the
    failure mode is "unreadable", which is a hole in the scan wearing the mask
    of a finding. A byte count rather than a sentinel, because any sentinel
    string is a string some scanned file could contain.
    """
    while True:
        header = stdin_buffer.readline()
        if not header:
            return
        header = header.rstrip(b"\r\n")
        if not header:
            continue
        if not header.startswith(b"FILE "):
            raise ValueError("malformed frame header: %r" % header[:80])
        rest = header[5:]
        size_raw, _, label = rest.partition(b" ")
        size = int(size_raw)
        payload = stdin_buffer.read(size)
        if len(payload) != size:
            raise ValueError(
                "frame for %r was short: wanted %d bytes, got %d" % (label, size, len(payload))
            )
        yield label.decode("utf-8", "replace"), payload.decode("utf-8", "replace")


def main():
    # Windows text mode turns every \n into \r\n, and the caller compares the
    # COUNT values it parses back as integers: a trailing \r makes `12` fail
    # `[ -eq 0 ]` with "integer expression expected". Measured, not guessed.
    try:
        sys.stdout.reconfigure(encoding="utf-8", newline="\n")
    except AttributeError:  # pragma: no cover - python < 3.7
        pass

    ap = argparse.ArgumentParser()
    ap.add_argument("--roster", required=True)
    ap.add_argument("--roster-names", default="")
    ap.add_argument("--person-bearing", required=True)
    ap.add_argument("--non-person-bearing", required=True)
    args = ap.parse_args()

    out = []

    def emit(*parts):
        out.append("\t".join(str(p).replace("\t", " ").replace("\n", " ") for p in parts))

    def finding(path, line, reason):
        emit("FINDING", path, line, reason)

    try:
        scan = list(read_stream(sys.stdin.buffer))
    except ValueError as exc:
        sys.stderr.write("the input stream is malformed: %s\n" % exc)
        return 3
    if not scan:
        sys.stderr.write("the input stream held no files; the caller should have caught this\n")
        return 2

    roster_names = {normalise(l) for l in args.roster_names.splitlines() if l.strip()}

    person_bearing = {l.strip() for l in args.person_bearing.splitlines() if l.strip()}
    non_person = {l.strip() for l in args.non_person_bearing.splitlines() if l.strip()}

    deny_norm = [(t, normalise(t)) for t in DENY_LIST]
    allowed_domains = {d.casefold() for d in ALLOWED_EMAIL_DOMAINS}
    allowed_addresses = {normalise(a) for a in ALLOWED_ORG_ADDRESSES}

    emit("COUNT", "deny_terms", len(DENY_LIST))
    emit("COUNT", "shape_patterns", len(SHAPE_NAMES))
    emit("COUNT", "allow_domains", len(ALLOWED_EMAIL_DOMAINS))
    emit("COUNT", "allow_addresses", len(ALLOWED_ORG_ADDRESSES))
    emit("NOTE", "deny-list terms (%d, full names only): %s" % (len(DENY_LIST), ", ".join(DENY_LIST)))
    emit("NOTE", "shape patterns (%d): %s" % (len(SHAPE_NAMES), " · ".join(SHAPE_NAMES)))
    emit("NOTE", "reserved email domains allowed (%d): %s" % (len(allowed_domains), ", ".join(sorted(allowed_domains))))
    emit("NOTE", "organisation addresses allowed (%d): %s" % (len(ALLOWED_ORG_ADDRESSES), ", ".join(ALLOWED_ORG_ADDRESSES) or "none declared"))

    suppressed = 0
    names_found = 0
    bundles_seen = set()

    for shown, text in scan:
        lines = text.splitlines()

        # ---------------------------------------------- the closed world -----
        # Only entity exports carry _meta; the roster does not, and is skipped.
        m_type = re.search(r"^\s*entity_type:\s*['\"]?([A-Za-z0-9_]+)", text, re.M)
        m_bundle = re.search(r"^\s*bundle:\s*['\"]?([A-Za-z0-9_]+)", text, re.M)
        entity_key = None
        if m_type:
            ent_type = m_type.group(1)
            entity_key = "%s/%s" % (ent_type, m_bundle.group(1)) if m_bundle else ent_type
            bundles_seen.add(entity_key)
            declared = (
                entity_key in person_bearing
                or entity_key in non_person
                or ent_type in person_bearing
                or ent_type in non_person
            )
            if not declared:
                finding(
                    shown,
                    text[: m_type.start()].count("\n") + 1,
                    "entity type/bundle [%s] is declared neither person-bearing nor non-person-bearing in "
                    "tests/bin/no-real-people - a new place a human name can live must be declared, "
                    "or assertion (1) does not know to look there (I-024)" % entity_key,
                )

        # ---------------------------------------- (1) roster completeness ----
        is_person = bool(
            entity_key in person_bearing
            or (m_type is not None and m_type.group(1) in person_bearing)
        )
        if is_person:
            # The node's OWN title, in the default language and in each
            # translation, and nothing else. The key path is matched exactly
            # rather than by "ends with title": a paragraph or a referenced
            # entity may carry a title of its own, and demanding a roster row
            # for it would be a false positive whose only cure is deleting a
            # rule - the move the FORBIDDEN clause exists to stop.
            track = key_path_tracker()
            for i, line in enumerate(lines, 1):
                path_here = track(line)
                m_val = re.match(r"^\s*value:\s*(.+?)\s*$", line)
                if not m_val:
                    continue
                if not RE_TITLE_PATH.match(path_here):
                    continue
                raw = m_val.group(1).strip().strip("'\"")
                if not raw:
                    continue
                names_found += 1
                if normalise(raw) not in roster_names:
                    finding(
                        shown,
                        i,
                        "person name [%s] is not declared in %s - every name this package ships is on the "
                        "roster, with the word `fictional` and a provenance, or it is not shipped"
                        % (raw, args.roster),
                    )

        # ------------------------------------------------ (2) deny-list ------
        norm_text = normalise(text)
        for term, term_norm in deny_norm:
            if term_norm in norm_text:
                hit_line = 0
                for i, line in enumerate(lines, 1):
                    if term_norm in normalise(line):
                        hit_line = i
                        break
                finding(
                    shown,
                    hit_line,
                    "deny-list: [%s] is a real, named public office-holder. Demo content that carries a real "
                    "person's name is not demo content. Change the NAME; the list does not move" % term,
                )

        # --------------------------------------------------- (3) shapes ------
        track = key_path_tracker()
        for i, line in enumerate(lines, 1):
            path_here = track(line)

            for m in RE_DNI.finditer(line):
                if dni_ok(m.group(1), m.group(2)):
                    finding(shown, i, "shape: DNI [%s] at key [%s] - the mod-23 check letter is correct, so this "
                                      "is a validly formed Spanish national identity number" % (m.group(0), path_here or "<root>"))

            for m in RE_NIE.finditer(line):
                if nie_ok(m.group(1), m.group(2), m.group(3)):
                    finding(shown, i, "shape: NIE [%s] at key [%s] - the mod-23 check letter is correct"
                            % (m.group(0), path_here or "<root>"))

            for m in RE_IBAN.finditer(line):
                if iban_ok(m.group(1)):
                    finding(shown, i, "shape: IBAN [%s] at key [%s] - the mod-97 checksum is correct, so this is a "
                                      "real, valid bank account number" % (m.group(1), path_here or "<root>"))

            for m in RE_PHONE_INTL.finditer(line):
                finding(shown, i, "shape: telephone in international form [%s] at key [%s]"
                        % (m.group(0), path_here or "<root>"))

            for m in RE_PHONE_BARE.finditer(line):
                if RE_PHONE_INTL.search(line):
                    continue
                if RE_PHONE_KEY.search(path_here or ""):
                    finding(shown, i, "shape: telephone in bare national form [%s] under a contact key [%s]"
                            % (m.group(1), path_here))
                else:
                    suppressed += 1

            for m in RE_EMAIL.finditer(line):
                host = m.group(1).casefold()
                if host in allowed_domains:
                    continue
                if any(host.endswith("." + d) for d in allowed_domains):
                    continue
                finding(shown, i, "shape: email address [%s] at a non-reserved domain [%s], key [%s] - demo content "
                                  "uses RFC 2606 reserved names, which belong to nobody and route nowhere"
                        % (m.group(0), m.group(1), path_here or "<root>"))

            for m in RE_ADDRESS.finditer(line):
                if normalise(m.group(0)) in allowed_addresses:
                    continue
                finding(shown, i, "shape: postal address with a street number [%s] at key [%s] - if it is an "
                                  "ORGANISATION's address, declare it in ALLOWED_ORG_ADDRESSES; a person's address "
                                  "is not declared, it is removed" % (m.group(0).strip(), path_here or "<root>"))

    emit("COUNT", "suppressed", suppressed)
    emit("COUNT", "names_found", names_found)
    emit("COUNT", "bundles_seen", len(bundles_seen))
    emit("NOTE", "entity types/bundles seen (%d): %s" % (len(bundles_seen), ", ".join(sorted(bundles_seen)) or "none"))
    emit("NOTE", "person names extracted from person entities: %d; roster rows available to match them: %d"
         % (names_found, len(roster_names)))
    if suppressed:
        emit("NOTE", "%d nine-digit value(s) suppressed: they match the bare national telephone shape but sit under "
                     "a key that is not a contact field. Remuneration and severance figures land here by "
                     "arithmetic, not by exception" % suppressed)

    sys.stdout.write("\n".join(out) + "\n")
    return 0


if __name__ == "__main__":
    sys.exit(main())
