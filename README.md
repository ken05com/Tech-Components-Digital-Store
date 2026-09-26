# TECH COMPONENTS

A dark-themed Laravel dashboard and inventory catalog for tech components, built
around the 25 products and 5 subtopics defined in **`the subtopis and samples.docx`**
(Shoby Keneth R. Daamo, BSIT – 3A).

> **No database.** The catalog is static PHP data (`app/Catalog/CatalogData.php`).
> Add / Edit / Delete are persisted as a small JSON overlay in
> `storage/app/private/catalog/products.json`, and *Settings → Restore document data*
> resets it back to the original 25 rows.

## Stack

| Layer      | Choice                                            |
| ---------- | ------------------------------------------------- |
| Framework  | Laravel 12                                        |
| Views      | Blade with reusable components (`resources/views/components`) |
| Styling    | Tailwind CSS v4 with a custom dark palette        |
| JS         | Alpine.js + Chart.js 4                            |
| Data       | Static catalog + JSON overlay (no SQL, no Eloquent) |

## Getting started

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link     # serves uploaded product images
npm run build               # or: npm run dev
php artisan serve
```

Open <http://127.0.0.1:8000>.

To undo every local Add / Edit / Delete from the terminal:

```bash
php artisan catalog:reset
```

## Routes

| Method        | URI                                | Name                             |
| ------------- | ---------------------------------- | -------------------------------- |
| GET           | `/`                                | `home` (redirects to dashboard)  |
| GET           | `/dashboard`                       | `dashboard`                      |
| GET           | `/products`                        | `products.index`                 |
| GET           | `/products/create`                 | `products.create`                |
| POST          | `/products`                        | `products.store`                 |
| GET           | `/products/{id}`                   | `products.show`                  |
| GET           | `/products/{id}/edit`              | `products.edit`                  |
| PATCH/PUT     | `/products/{id}`                   | `products.update`                |
| DELETE        | `/products/{id}`                   | `products.destroy`               |
| GET           | `/categories/computer-components`  | `categories.computer-components` |
| GET           | `/categories/storage-devices`      | `categories.storage-devices`     |
| GET           | `/categories/computer-accessories` | `categories.computer-accessories`|
| GET           | `/categories/networking-components`| `categories.networking-components`|
| GET           | `/categories/power-connection`     | `categories.power-connection`    |
| GET           | `/search?q=`                       | `search`                         |
| GET           | `/settings`                        | `settings`                       |
| GET           | `/profile`                         | `profile`                        |
| POST          | `/catalog/reset`                   | `catalog.reset`                  |

The five category routes are registered in a loop straight from the `Category`
enum, so a URL can never drift away from a subtopic in the document.

## Table columns

Every listing table uses exactly these columns, in this order, with **PICTURE
always last**:

```
ID | NAME | DESCRIPTION | PRICE | PICTURE
```

`ID` shows the row number used in the document (1–5 within each subtopic).

## Catalog structure

| # | Subtopic              | Products                                                        |
| - | --------------------- | --------------------------------------------------------------- |
| 1 | Computer Components   | CPU / Processor, Motherboard, RAM, GPU, PSU                      |
| 2 | Storage Devices       | HDD, SSD, NVMe SSD, USB Flash Drive, Memory Card                 |
| 3 | Computer Accessories  | Keyboard, Mouse, Webcam, Speakers, Headset                       |
| 4 | Networking Components | Wi-Fi Router, Network Switch, Ethernet Cable, Wi-Fi Adapter, LAN Card |
| 5 | Power & Connection    | HDMI Cable, USB Cable, Power Cable, USB Hub, Surge Protector     |

Product names, descriptions and prices are transcribed verbatim from the
document — including the en dash in ranges such as `₱5,000–10,000`.

## Project layout

```
app/
  Catalog/
    CatalogData.php        # the 25 products, verbatim from the DOCX
    Category.php           # the 5 subtopics as an enum
    Product.php            # row value object (price formatting, image URL)
    ProductRepository.php  # read/write, search, filter, sort, paginate
    ChartPalette.php       # shared chart colours
  Http/Controllers/        # Dashboard, Category, Product, Search, Page
resources/
  css/app.css              # Tailwind v4 theme (dark palette + orange accent)
  js/app.js                # Alpine shell + Chart.js dashboard charts
  views/
    layouts/app.blade.php
    components/
      app/                 # sidebar, header, flash, page header, icon
      catalog/             # product-table, product-form, product-card, …
      ui/                  # stat-card, badge
    dashboard.blade.php
    categories/show.blade.php
    products/{index,show,create,edit}.blade.php
    search.blade.php
    settings.blade.php
    profile.blade.php
public/images/products/    # the 25 product photos from the DOCX
```
