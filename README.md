# WithLoveNRegards

Production-ready e-commerce website for **WithLoveNRegards** — an Indian online florist and gifting business based in Pune. Built end-to-end in **vanilla PHP 8.2**, HTML5, CSS3 and vanilla JavaScript (jQuery 3.7.1). No frameworks.

## Features

- **Storefront** — homepage, category listings with filters, product pages, cart, checkout, wishlist, search, customer auth & account area, city landing pages, and policy pages.
- **Admin panel** — dashboard (Chart.js analytics), order management, product & category CRUD, CRM (customers + abandoned carts), homepage editor, and settings.
- **SEO engine** — dynamic meta tags, Open Graph/Twitter cards, JSON-LD (Product, Breadcrumb, Organization, FAQ, ItemList), sitemap & robots.
- **Commerce logic** — session cart, **server-side price recalculation** (client prices never trusted), **5 PM IST same-day cutoff** (server-evaluated), CSRF protection on all mutations, separate admin/customer sessions.
- **CC Avenue** payment integration scaffolding (AES request/response, order-id mapping) — keys left empty until live.
- **PHPMailer** invoice emails on successful payment.
- Responsive, mobile-first UI.

## Tech stack

| Layer | Choice |
|-------|--------|
| Backend | PHP 8.2 (PDO) |
| Database | MySQL — currently a placeholder data layer (`data/placeholder_data.php`) |
| Frontend | HTML5, CSS3, vanilla JS, jQuery 3.7.1 + jQuery UI 1.13.2 |
| Icons / fonts | Font Awesome 6, Google Fonts (Roboto + Playfair Display) |
| Payments | CC Avenue (scaffolded) |
| Email | PHPMailer |

## Running locally

This is a classic PHP app served by Apache (the `.htaccess` handles clean-URL routing). With a LAMP/XAMPP stack, point a vhost at the project root.

For quick local testing without Apache, the dev router emulates the rewrite rules:

```bash
php -S localhost:8000 scripts/router.php
```

Then open <http://localhost:8000/>. Admin panel: `/admin` (demo login `admin` / `admin123`).

## Going live

1. Fill the DB credentials in `config/db.php` and set `$use_placeholder = false`.
2. Run `composer install` for PHPMailer.
3. Add CC Avenue + SMTP credentials via **Admin → Settings** (saved to `config/settings.json`, gitignored).

## Project structure

```
config/      constants, DB, mail
data/        placeholder dataset + homepage config
includes/    header, footer, functions, SEO, schema, partials
pages/       storefront pages
admin/       admin panel (all PHP)
api/         AJAX + payment endpoints
assets/      css, js, images (WebP)
sendflowers/ city landing pages
scripts/     dev helpers (image generation, local router)
```
