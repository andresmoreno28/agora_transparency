#!/usr/bin/env python3
# no-secrets-binary.py - Agora - unit 003, wave 9, task T-906.
#
# The binary-metadata sweep behind level 3 of tests/bin/no-secrets.
#
# WHY IT EXISTS. no-secrets' is_text() guard uses `grep -I`, so every image in
# the package is skipped BY DESIGN. That was correct while content/ held one
# YAML file. It stops being correct the moment the package ships photographs,
# because EXIF and XMP metadata carry GPS coordinates, camera serial numbers and
# author names - personal data in a file the secrets invariant is structurally
# blind to. This closes that blindness rather than working around it.
#
# NO NEW TOOL. Markers are located at BYTE level with the standard library
# alone. An external EXIF reader would move the toolchain floor set by T-803 for
# one check, and the floor is a certified surface - tests/bin/doctor says what
# this host has, and python3 is on that list while exiftool is not.
#
# WHAT IT DETECTS, and the honest limit of each:
#   Exif\x00\x00                   the APP1/EXIF marker. Present in any JPEG or
#                                  TIFF carrying EXIF, wherever it is embedded.
#   http://ns.adobe.com/xap/1.0/   the XMP packet namespace URI.
#   <x:xmpmeta  /  <?xpacket       the XMP packet's own wrappers, which survive
#                                  some strippers that remove only the URI.
#   RIFF/WebP chunks EXIF and XMP  found by WALKING the chunk list, not by
#                                  searching for the four bytes: `EXIF` as a raw
#                                  string occurs in ordinary compressed data
#                                  often enough to be useless as a substring.
#   PNG chunk eXIf                 same, walked rather than searched.
#
# THE LIMIT, STATED: absence of these markers is not proof that a file carries
# no metadata. A private maker-note in an unrecognised container, or a format
# this sweep does not walk, would pass. It proves the two standard carriers named
# in the plan are absent, and it names each hit with its byte offset so the claim
# can be checked by hand. A check that claimed more would be worse than none.
#
# Records on stdout, tab separated, exactly as the caller expects:
#   COUNT   <name>  <n>
#   NOTE    <text>
#   FINDING <path>  <offset>  <reason>
# Exit non-zero only when the sweep could not run.

import sys

# (label, needle) - located as a raw byte substring anywhere in the file.
SUBSTRING_MARKERS = [
    ("EXIF", b"Exif\x00\x00", "APP1/EXIF marker"),
    ("XMP", b"http://ns.adobe.com/xap/1.0/", "XMP packet namespace URI"),
    ("XMP", b"<x:xmpmeta", "XMP packet wrapper element"),
    ("XMP", b"<?xpacket begin", "XMP packet processing instruction"),
]

MARKER_NAMES = [
    "Exif\\x00\\x00 (APP1/EXIF)",
    "http://ns.adobe.com/xap/1.0/ (XMP URI)",
    "<x:xmpmeta (XMP wrapper)",
    "<?xpacket begin (XMP packet)",
    "RIFF/WebP chunk EXIF (chunk list walked)",
    "RIFF/WebP chunk 'XMP ' (chunk list walked)",
    "PNG chunk eXIf (chunk list walked)",
]


def walk_riff(data):
    """Yield (fourcc, offset) for a RIFF/WebP container, or nothing.

    Walked rather than searched: `EXIF` as four raw bytes turns up inside
    ordinary compressed image data, so a substring search for it would produce
    false positives - and the only route from a false positive back to green is
    deleting a rule, which is the move this project's FORBIDDEN clauses exist to
    stop.
    """
    if len(data) < 12 or data[0:4] != b"RIFF" or data[8:12] != b"WEBP":
        return
    pos = 12
    while pos + 8 <= len(data):
        fourcc = data[pos:pos + 4]
        size = int.from_bytes(data[pos + 4:pos + 8], "little")
        yield fourcc, pos
        pos += 8 + size + (size & 1)


def walk_png(data):
    """Yield (chunk type, offset) for a PNG container, or nothing."""
    if len(data) < 8 or data[0:8] != b"\x89PNG\r\n\x1a\n":
        return
    pos = 8
    while pos + 8 <= len(data):
        size = int.from_bytes(data[pos:pos + 4], "big")
        ctype = data[pos + 4:pos + 8]
        yield ctype, pos
        pos += 12 + size
        if ctype == b"IEND":
            return


def read_stream(buf):
    """Yield (label, payload bytes). Framing: `FILE <bytes> <label>\\n` + bytes.

    Bytes on stdin rather than paths, for the reason recorded in
    no-real-people.py: this repository's working copy is a Windows checkout
    driven from Git Bash, and the MSYS temp path `mktemp -d` returns is one the
    native python3 cannot open. A path that cannot be opened reports as
    "unreadable", which is a hole in the scan wearing the mask of a finding.
    """
    while True:
        header = buf.readline()
        if not header:
            return
        header = header.rstrip(b"\r\n")
        if not header:
            continue
        if not header.startswith(b"FILE "):
            raise ValueError("malformed frame header: %r" % header[:80])
        size_raw, _, label = header[5:].partition(b" ")
        size = int(size_raw)
        payload = buf.read(size)
        if len(payload) != size:
            raise ValueError("frame for %r was short: wanted %d, got %d" % (label, size, len(payload)))
        yield label.decode("utf-8", "replace"), payload


def main():
    try:
        sys.stdout.reconfigure(encoding="utf-8", newline="\n")
    except AttributeError:  # pragma: no cover - python < 3.7
        pass

    out = []

    def emit(*parts):
        out.append("\t".join(str(p).replace("\t", " ").replace("\n", " ") for p in parts))

    try:
        files = list(read_stream(sys.stdin.buffer))
    except ValueError as exc:
        sys.stderr.write("the input stream is malformed: %s\n" % exc)
        return 3

    emit("COUNT", "markers", len(MARKER_NAMES))
    emit("NOTE", "markers searched (%d): %s" % (len(MARKER_NAMES), " · ".join(MARKER_NAMES)))

    opened = 0
    with_exif = 0
    with_xmp = 0

    for label, data in files:
        opened += 1
        hits = []

        for kind, needle, human in SUBSTRING_MARKERS:
            off = data.find(needle)
            while off != -1:
                hits.append((kind, human, off))
                off = data.find(needle, off + 1)

        for fourcc, off in walk_riff(data):
            if fourcc == b"EXIF":
                hits.append(("EXIF", "RIFF/WebP chunk EXIF", off))
            elif fourcc == b"XMP ":
                hits.append(("XMP", "RIFF/WebP chunk 'XMP '", off))

        for ctype, off in walk_png(data):
            if ctype == b"eXIf":
                hits.append(("EXIF", "PNG chunk eXIf", off))

        if any(k == "EXIF" for k, _, _ in hits):
            with_exif += 1
        if any(k == "XMP" for k, _, _ in hits):
            with_xmp += 1

        for kind, human, off in hits:
            emit(
                "FINDING",
                label,
                off,
                "binary metadata: %s marker [%s] at byte offset %d (0x%x) - EXIF and XMP carry GPS "
                "coordinates, camera serial numbers and author names, and no-secrets' grep -I guard "
                "cannot see any of it. Strip the metadata; the file stays, the marker goes"
                % (kind, human, off, off),
            )

    emit("COUNT", "opened", opened)
    emit("COUNT", "with_exif", with_exif)
    emit("COUNT", "with_xmp", with_xmp)

    sys.stdout.write("\n".join(out) + "\n")
    return 0


if __name__ == "__main__":
    sys.exit(main())
