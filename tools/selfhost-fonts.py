#!/usr/bin/env python3
"""Download Google Fonts (woff2) and generate a self-hosted fonts.css.

- Downloads @font-face blocks for latin + latin-ext subsets only (smaller payload)
- Writes font files under assets/fonts/<slug>/
- Generates assets/fonts/fonts.css with relative urls

Run:
  python3 tools/selfhost-fonts.py

If you need more character support, update SUBSETS.
"""

from __future__ import annotations

import re
import urllib.request
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable

THEME_ROOT = Path(__file__).resolve().parents[1]
FONTS_ROOT = THEME_ROOT / "assets" / "fonts"
FONTS_CSS = FONTS_ROOT / "fonts.css"

UA = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
    "AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/122.0.0.0 Safari/537.36"
)

SUBSETS = {"latin", "latin-ext"}


@dataclass(frozen=True)
class FontRequest:
    slug: str
    google_family_query: str


FONT_REQUESTS: list[FontRequest] = [
    FontRequest("anton", "Anton:wght@400"),
    FontRequest("arvo", "Arvo:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest("bodoni-moda", "Bodoni+Moda:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest("cabin", "Cabin:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest("chivo", "Chivo:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest("inter", "Inter:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest("marcellus", "Marcellus:wght@400"),
    FontRequest("roboto", "Roboto:ital,wght@0,400;0,700;1,400;1,700"),
    FontRequest(
        "playfair-display",
        "Playfair+Display:ital,wght@0,400;0,600;1,400;1,600",
    ),
]


def fetch_text(url: str) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=30) as resp:
        return resp.read().decode("utf-8")


def fetch_bytes(url: str) -> bytes:
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=60) as resp:
        return resp.read()


FONT_FACE_RE = re.compile(r"@font-face\s*\{.*?\}", re.DOTALL)


def iter_font_faces(css: str) -> Iterable[tuple[str, str]]:
    pos = 0
    while True:
        m = FONT_FACE_RE.search(css, pos)
        if not m:
            break

        block_start = m.start()
        # Find nearest subset comment above the block.
        subset = ""
        before = css[:block_start]
        cm = re.findall(r"/\*\s*([^*]+?)\s*\*/\s*\n\s*$", before, flags=re.MULTILINE)
        if cm:
            subset = cm[-1].strip().lower()

        yield subset, m.group(0)
        pos = m.end()


def parse_prop(block: str, prop: str) -> str:
    m = re.search(rf"\b{re.escape(prop)}\s*:\s*([^;]+);", block)
    return m.group(1).strip() if m else ""


def parse_woff2_url(block: str) -> str:
    m = re.search(r"url\((https?://[^)]+?\.woff2)\)", block)
    return m.group(1) if m else ""


def sanitize_subset(subset: str) -> str:
    subset = subset.strip().lower().replace(" ", "-")
    subset = re.sub(r"[^a-z0-9\-]", "", subset)
    return subset or "subset"


def main() -> int:
    FONTS_ROOT.mkdir(parents=True, exist_ok=True)

    css_out: list[str] = []
    downloaded = 0

    for req in FONT_REQUESTS:
        css_url = f"https://fonts.googleapis.com/css2?family={req.google_family_query}&display=swap"
        print(f"Fetching CSS: {req.slug} -> {css_url}")
        css = fetch_text(css_url)

        slug_dir = FONTS_ROOT / req.slug
        slug_dir.mkdir(parents=True, exist_ok=True)

        for subset, block in iter_font_faces(css):
            if subset and subset not in SUBSETS:
                continue

            family = parse_prop(block, "font-family").strip("'\"")
            style = parse_prop(block, "font-style") or "normal"
            weight = parse_prop(block, "font-weight") or "400"
            unicode_range = parse_prop(block, "unicode-range")
            url = parse_woff2_url(block)
            if not (family and url):
                continue

            subset_name = sanitize_subset(subset) if subset else "latin"
            filename = f"{req.slug}-{style}-{weight}-{subset_name}.woff2"
            out_path = slug_dir / filename

            if not out_path.exists():
                out_path.write_bytes(fetch_bytes(url))
                downloaded += 1

            rel_url = f"./{req.slug}/{filename}"

            css_out.append(
                "@font-face {\n"
                f"  font-family: '{family}';\n"
                f"  font-style: {style};\n"
                f"  font-weight: {weight};\n"
                "  font-display: swap;\n"
                f"  src: url(\"{rel_url}\") format('woff2');\n"
                + (f"  unicode-range: {unicode_range};\n" if unicode_range else "")
                + "}\n"
            )

    FONTS_CSS.write_text("\n".join(css_out), encoding="utf-8")
    print(f"\nWrote {FONTS_CSS.relative_to(THEME_ROOT)}")
    print(f"Downloaded {downloaded} woff2 file(s) into {FONTS_ROOT.relative_to(THEME_ROOT)}/")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
