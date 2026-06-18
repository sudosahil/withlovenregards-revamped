"""
Generate branded placeholder images for the WithLoveNRegards build.
Creates WebP product/category/banner images, a PNG logo and a favicon.
Run once: python scripts/gen_placeholders.py
"""
import os
from PIL import Image, ImageDraw, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, "assets", "img")

PRIMARY = (232, 51, 90)
DARK = (44, 44, 44)
BLUSH = (248, 240, 242)
WHITE = (255, 255, 255)


def font(size):
    for name in ("seguibl.ttf", "segoeui.ttf", "arialbd.ttf", "arial.ttf", "DejaVuSans-Bold.ttf"):
        try:
            return ImageFont.truetype(name, size)
        except OSError:
            continue
    return ImageFont.load_default()


def gradient(w, h, top, bottom):
    base = Image.new("RGB", (w, h), top)
    draw = ImageDraw.Draw(base)
    for y in range(h):
        t = y / max(1, h - 1)
        col = tuple(int(top[i] + (bottom[i] - top[i]) * t) for i in range(3))
        draw.line([(0, y), (w, y)], fill=col)
    return base


def centered(draw, box, text, fnt, fill):
    x0, y0, x1, y1 = box
    bbox = draw.textbbox((0, 0), text, font=fnt)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    draw.text((x0 + (x1 - x0 - tw) / 2 - bbox[0],
               y0 + (y1 - y0 - th) / 2 - bbox[1]), text, font=fnt, fill=fill)


def label_image(path, w, h, title, top=PRIMARY, bottom=(201, 31, 69), tag="WithLoveNRegards"):
    img = gradient(w, h, top, bottom)
    draw = ImageDraw.Draw(img)
    # subtle floral mark
    draw.ellipse([w - 90, -40, w + 40, 90], fill=tuple(min(255, c + 25) for c in top))
    centered(draw, (20, h * 0.30, w - 20, h * 0.62), title, font(int(min(w, h) * 0.11)), WHITE)
    centered(draw, (0, h * 0.74, w, h * 0.88), tag, font(int(min(w, h) * 0.05)), (255, 255, 255))
    os.makedirs(os.path.dirname(path), exist_ok=True)
    img.save(path, "WEBP", quality=82, method=4)
    print("wrote", os.path.relpath(path, ROOT))


# --- Product slugs (match data/placeholder_data.php) ----------------------
PRODUCTS = [
    "red-roses-bunch", "pink-rose-elegance", "rainbow-rose-bouquet", "red-roses-heart-box",
    "pink-carnation-posy", "mixed-carnation-basket", "white-lily-bouquet", "pink-lily-arrangement",
    "seasonal-mixed-bouquet", "orchid-grace", "anniversary-special-bouquet", "sunflower-sunshine",
    "chocolate-truffle-cake", "black-forest-cake", "red-velvet-cake", "butterscotch-cake",
    "heart-shape-red-velvet", "theme-cartoon-cake", "pineapple-cake", "chocolate-bouquet-deluxe",
    "ferrero-rocher-hamper", "dairy-milk-gift-box", "premium-chocolate-hamper", "roses-n-chocolate-cake",
    "flowers-n-chocolates-combo", "teddy-roses-n-cake", "anniversary-combo-hamper", "birthday-surprise-box",
    "valentine-romance-combo", "mothers-day-bouquet", "fathers-day-special", "womens-day-floral-treat",
]
CATEGORIES = ["flowers", "cakes", "chocolates", "combos", "occasions", "roses", "carnations", "lilies"]
BANNERS = ["banner-1", "banner-2", "banner-3", "banner-4"]
PROMOS = ["promo-anniversary", "promo-birthday"]

PALETTES = [
    (PRIMARY, (201, 31, 69)),
    ((44, 44, 44), (90, 44, 90)),
    ((217, 84, 84), (160, 40, 70)),
    ((140, 92, 217), (90, 44, 160)),
]


def title_from_slug(slug):
    return slug.replace("-", " ").title()


def main():
    # Products
    for i, slug in enumerate(PRODUCTS):
        pal = PALETTES[i % len(PALETTES)]
        label_image(os.path.join(IMG, "products", slug + ".webp"), 600, 600,
                    title_from_slug(slug), pal[0], pal[1])
    # Categories
    for i, slug in enumerate(CATEGORIES):
        pal = PALETTES[i % len(PALETTES)]
        label_image(os.path.join(IMG, "categories", slug + ".webp"), 400, 400,
                    title_from_slug(slug), pal[0], pal[1])
    # Banners (wide)
    for i, slug in enumerate(BANNERS):
        label_image(os.path.join(IMG, "banners", slug + ".webp"), 1200, 480,
                    "Send Flowers & Gifts", PRIMARY, (201, 31, 69))
    for slug in PROMOS:
        label_image(os.path.join(IMG, "banners", slug + ".webp"), 600, 300,
                    title_from_slug(slug.replace("promo-", "")), DARK, (90, 44, 90))

    # Logo (transparent PNG)
    logo = Image.new("RGBA", (360, 96), (0, 0, 0, 0))
    d = ImageDraw.Draw(logo)
    d.text((4, 24), "WithLove", font=font(44), fill=DARK)
    w1 = d.textbbox((0, 0), "WithLove", font=font(44))[2]
    d.text((4 + w1, 24), "NRegards", font=font(44), fill=PRIMARY)
    logo.save(os.path.join(IMG, "logo", "logo.png"), "PNG")
    print("wrote assets/img/logo/logo.png")

    # Favicon (multi-size ICO)
    fav = Image.new("RGB", (64, 64), PRIMARY)
    fd = ImageDraw.Draw(fav)
    centered(fd, (0, 0, 64, 64), "W", font(40), WHITE)
    fav.save(os.path.join(IMG, "favicon.ico"), sizes=[(16, 16), (32, 32), (48, 48)])
    print("wrote assets/img/favicon.ico")


if __name__ == "__main__":
    main()
