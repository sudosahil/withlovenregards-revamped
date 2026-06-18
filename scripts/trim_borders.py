"""
Trim solid-colour padding that loremflickr adds around some photos, then
re-crop each image back to its original target size. Operates in-place on the
already-downloaded WebP assets (no network).

Run: python scripts/trim_borders.py
"""
import os
from PIL import Image, ImageChops

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, "assets", "img")

DIRS = ["products", "categories", "banners"]


def trim_uniform_border(im, tol=22):
    """Remove a uniform border (sampled from the top-left pixel) if present."""
    rgb = im.convert("RGB")
    bg = Image.new("RGB", rgb.size, rgb.getpixel((0, 0)))
    diff = ImageChops.difference(rgb, bg).convert("L")
    mask = diff.point(lambda p: 255 if p > tol else 0)
    bbox = mask.getbbox()
    if not bbox:
        return im
    w, h = im.size
    l, t, r, b = bbox
    # Only trim when a real border exists (>2% on at least one side).
    if l > w * 0.02 or t > h * 0.02 or r < w * 0.98 or b < h * 0.98:
        cropped = im.crop(bbox)
        # Guard against pathological over-trim (keep if it left almost nothing).
        if cropped.width > w * 0.3 and cropped.height > h * 0.3:
            return cropped
    return im


def cover(im, w, h):
    tr = w / h
    iw, ih = im.size
    ir = iw / ih
    if ir > tr:
        nw = int(ih * tr)
        im = im.crop(((iw - nw) // 2, 0, (iw - nw) // 2 + nw, ih))
    else:
        nh = int(iw / tr)
        im = im.crop((0, (ih - nh) // 2, iw, (ih - nh) // 2 + nh))
    return im.resize((w, h), Image.LANCZOS)


def main():
    fixed = 0
    for d in DIRS:
        folder = os.path.join(IMG, d)
        for name in os.listdir(folder):
            if not name.endswith(".webp"):
                continue
            path = os.path.join(folder, name)
            im = Image.open(path).convert("RGB")
            w, h = im.size
            trimmed = trim_uniform_border(im)
            if trimmed.size != im.size:
                out = cover(trimmed, w, h)
                out.save(path, "WEBP", quality=82, method=5)
                print(f"trimmed {d}/{name}  {im.size} -> content {trimmed.size}")
                fixed += 1
    print(f"done — {fixed} image(s) had padding removed")


if __name__ == "__main__":
    main()
