#!/usr/bin/env python3
"""
Generate WordPress.org plugin-directory artwork for a plugin:
  banner-772x250.png, banner-1544x500.png, icon-128x128.png, icon-256x256.png

Usage: wporg-assets.py <outDir> <slug> <plugin name> [tagline]

The look is deterministic per slug (colour pair picked from the slug's hash), so re-running gives the same
artwork. Plain, legible, no stock imagery: a two-colour gradient, the plugin name, an optional tagline, and
an icon with the plugin's initials.
"""
import hashlib
import os
import sys

from PIL import Image, ImageDraw, ImageFont

BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
REGULAR = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"

# Dark, saturated pairs that keep white text readable (contrast > 4.5:1 at the light end).
PALETTES = [
    ((20, 50, 110), (40, 110, 190)),
    ((70, 25, 110), (130, 60, 180)),
    ((10, 80, 80), (20, 140, 120)),
    ((120, 30, 40), (190, 70, 60)),
    ((30, 40, 60), (70, 90, 130)),
    ((15, 70, 40), (40, 130, 80)),
    ((110, 60, 10), (180, 100, 20)),
]


def palette(slug):
    h = int(hashlib.sha256(slug.encode()).hexdigest(), 16)
    return PALETTES[h % len(PALETTES)]


def gradient(w, h, a, b):
    img = Image.new("RGB", (w, h), a)
    px = img.load()
    for x in range(w):
        for y in range(h):
            t = (x / max(w - 1, 1)) * 0.75 + (y / max(h - 1, 1)) * 0.25
            px[x, y] = tuple(int(a[i] + (b[i] - a[i]) * t) for i in range(3))
    return img


def fit_font(draw, text, path, max_w, start):
    size = start
    while size > 10:
        font = ImageFont.truetype(path, size)
        if draw.textlength(text, font=font) <= max_w:
            return font
        size -= 2
    return ImageFont.truetype(path, 10)


def wrap(draw, text, font, max_w):
    words, lines, cur = text.split(), [], ""
    for w in words:
        trial = (cur + " " + w).strip()
        if draw.textlength(trial, font=font) <= max_w:
            cur = trial
        else:
            if cur:
                lines.append(cur)
            cur = w
    if cur:
        lines.append(cur)
    return lines[:2]


def banner(out, w, h, name, tagline, colors):
    img = gradient(w, h, *colors).convert("RGBA")
    s = w / 772.0
    # Faint decorative rings on the right, kept clear of the text column.
    rings = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    rd = ImageDraw.Draw(rings)
    cx, cy = int(w - 70 * s), int(h * 0.5)
    for r in (170, 115, 65):
        rr = int(r * s)
        rd.ellipse((cx - rr, cy - rr, cx + rr, cy + rr), outline=(255, 255, 255, 60), width=max(1, int(2 * s)))
    img = Image.alpha_composite(img, rings)
    d = ImageDraw.Draw(img)
    margin = int(48 * s)
    text_w = w * 0.58
    title = fit_font(d, name, BOLD, text_w, int(52 * s))
    lines = wrap(d, tagline, ImageFont.truetype(REGULAR, int(21 * s)), text_w) if tagline else []
    tf = ImageFont.truetype(REGULAR, int(21 * s))
    block = title.size + (int(16 * s) + len(lines) * (tf.size + int(8 * s)) if lines else 0)
    y = int((h - block) / 2)
    d.text((margin, y), name, font=title, fill=(255, 255, 255))
    y += title.size + int(16 * s)
    for line in lines:
        d.text((margin, y), line, font=tf, fill=(232, 238, 246))
        y += tf.size + int(8 * s)
    img.convert("RGB").save(out, optimize=True)


def initials(name):
    words = [w for w in name.replace("-", " ").split() if w[:1].isalnum()]
    return ("".join(w[0] for w in words[:2]) or name[:2]).upper()


def icon(out, size, name, colors):
    img = gradient(size, size, *colors)
    d = ImageDraw.Draw(img)
    text = initials(name)
    font = fit_font(d, text, BOLD, size * 0.7, int(size * 0.46))
    bbox = d.textbbox((0, 0), text, font=font)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    d.text(((size - tw) / 2 - bbox[0], (size - th) / 2 - bbox[1]), text, font=font, fill=(255, 255, 255))
    img.save(out, optimize=True)


def main():
    if len(sys.argv) < 4:
        print("Usage: wporg-assets.py <outDir> <slug> <plugin name> [tagline]", file=sys.stderr)
        sys.exit(2)
    out, slug, name = sys.argv[1], sys.argv[2], sys.argv[3]
    tagline = sys.argv[4] if len(sys.argv) > 4 else ""
    os.makedirs(out, exist_ok=True)
    colors = palette(slug)
    banner(os.path.join(out, "banner-772x250.png"), 772, 250, name, tagline, colors)
    banner(os.path.join(out, "banner-1544x500.png"), 1544, 500, name, tagline, colors)
    icon(os.path.join(out, "icon-128x128.png"), 128, name, colors)
    icon(os.path.join(out, "icon-256x256.png"), 256, name, colors)
    print("wrote banner-772x250.png banner-1544x500.png icon-128x128.png icon-256x256.png")


if __name__ == "__main__":
    main()
