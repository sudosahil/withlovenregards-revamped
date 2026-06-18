"""
Download topical stock photos (loremflickr, real Flickr CC images) and write
them as WebP into the asset folders, replacing the flat placeholders.
Deterministic: each slug uses a fixed `lock` seed so re-runs are stable.
Falls back to picsum if a keyword fetch fails.

Run: python scripts/fetch_stock_images.py
"""
import os
import io
import time
import urllib.request
from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, "assets", "img")

UA = {"User-Agent": "Mozilla/5.0 (asset-fetch)"}


def fetch(url, timeout=30):
    req = urllib.request.Request(url, headers=UA)
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return r.read()


def save_webp(data, path, w, h):
    im = Image.open(io.BytesIO(data)).convert("RGB")
    # cover-crop to target aspect ratio
    tr = w / h
    iw, ih = im.size
    ir = iw / ih
    if ir > tr:
        nw = int(ih * tr)
        im = im.crop(((iw - nw) // 2, 0, (iw - nw) // 2 + nw, ih))
    else:
        nh = int(iw / tr)
        im = im.crop((0, (ih - nh) // 2, iw, (ih - nh) // 2 + nh))
    im = im.resize((w, h), Image.LANCZOS)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    im.save(path, "WEBP", quality=82, method=5)


def grab(tags, lock, w, h, path):
    # loremflickr pads extreme aspect ratios with a coloured background, so we
    # always request a SQUARE source (its native crop) and cover-crop ourselves.
    s = max(w, h, 800)
    sources = [
        f"https://loremflickr.com/{s}/{s}/{tags}?lock={lock}",
        f"https://loremflickr.com/{s}/{s}/{tags.split(',')[0]}?lock={lock}",
        f"https://picsum.photos/seed/{tags.split(',')[0]}{lock}/{s}/{s}",
    ]
    for attempt, src in enumerate(sources):
        for retry in range(2):
            try:
                data = fetch(src)
                if data and len(data) > 2000:
                    save_webp(data, path, w, h)
                    print("ok  ", os.path.relpath(path, ROOT), "<-", src.split("?")[0])
                    return True
            except Exception as e:  # noqa: BLE001
                last = e
                time.sleep(0.4)
    print("FAIL", os.path.relpath(path, ROOT), last if 'last' in dir() else '')
    return False


# slug -> flickr keyword tags
PRODUCTS = {
    "red-roses-bunch": "red,roses,bouquet",
    "pink-rose-elegance": "pink,roses,bouquet",
    "rainbow-rose-bouquet": "colorful,roses,bouquet",
    "red-roses-heart-box": "red,roses,box",
    "pink-carnation-posy": "pink,carnation,flowers",
    "mixed-carnation-basket": "carnation,flowers,basket",
    "white-lily-bouquet": "white,lily,flowers",
    "pink-lily-arrangement": "pink,lily,flowers",
    "seasonal-mixed-bouquet": "mixed,flower,bouquet",
    "orchid-grace": "purple,orchid,flower",
    "anniversary-special-bouquet": "roses,bouquet,romantic",
    "sunflower-sunshine": "sunflower,bouquet",
    "chocolate-truffle-cake": "chocolate,cake",
    "black-forest-cake": "blackforest,cake",
    "red-velvet-cake": "redvelvet,cake",
    "butterscotch-cake": "caramel,cake",
    "heart-shape-red-velvet": "heart,cake",
    "theme-cartoon-cake": "birthday,cake,colorful",
    "pineapple-cake": "pineapple,cake",
    "chocolate-bouquet-deluxe": "chocolate,bouquet",
    "ferrero-rocher-hamper": "chocolate,gift,box",
    "dairy-milk-gift-box": "chocolate,box",
    "premium-chocolate-hamper": "chocolate,hamper,gift",
    "roses-n-chocolate-cake": "roses,cake,gift",
    "flowers-n-chocolates-combo": "flowers,chocolate,gift",
    "teddy-roses-n-cake": "teddybear,roses,gift",
    "anniversary-combo-hamper": "roses,cake,celebration",
    "birthday-surprise-box": "birthday,gift,balloons",
    "valentine-romance-combo": "valentine,roses,gift",
    "mothers-day-bouquet": "pink,flowers,bouquet",
    "fathers-day-special": "flowers,gift",
    "womens-day-floral-treat": "flowers,bouquet,colorful",
}
CATEGORIES = {
    "flowers": "flowers,bouquet",
    "cakes": "cake,dessert",
    "chocolates": "chocolate,gift",
    "combos": "flowers,gift",
    "occasions": "celebration,gift",
    "roses": "roses,bouquet",
    "carnations": "carnation,flowers",
    "lilies": "lily,flowers",
}
BANNERS = {
    "banner-1": "flowers,bouquet,romantic",
    "banner-2": "cake,celebration,gift",
    "banner-3": "cake,dessert,bakery",
    "banner-4": "red,roses,romantic",
    "promo-anniversary": "roses,romantic,candles",
    "promo-birthday": "birthday,cake,balloons",
}


def main():
    lock = 100
    for slug, tags in PRODUCTS.items():
        grab(tags, lock, 600, 600, os.path.join(IMG, "products", slug + ".webp"))
        lock += 1
    for slug, tags in CATEGORIES.items():
        grab(tags, lock, 500, 500, os.path.join(IMG, "categories", slug + ".webp"))
        lock += 1
    for slug, tags in BANNERS.items():
        w, h = (1200, 480) if slug.startswith("banner") else (600, 300)
        grab(tags, lock, w, h, os.path.join(IMG, "banners", slug + ".webp"))
        lock += 1
    print("done")


if __name__ == "__main__":
    main()
