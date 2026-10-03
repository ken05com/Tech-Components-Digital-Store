# TECH COMPONENTS

A dark-themed Laravel dashboard and inventory catalog for tech components, built
around the 25 products and 5 subtopics defined in **`the subtopis and samples.docx`**
(Shoby Keneth R. Daamo, BSIT – 3A).

> **MySQL, no Eloquent.** The 25 products are defined as static PHP data in
> `app/Catalog/CatalogData.php` and seeded into a `products` table by
> `CatalogSeeder`. Add / Edit / Delete write straight to that table through the
> query builder, and `catalog:reset` truncates and re-seeds it back to the
> original 25 rows.

## Stack

| Layer      | Choice                                            |
| ---------- | ------------------------------------------------- |
| Framework  | Laravel 13                                        |
| Views      | Blade with reusable components (`resources/views/components`) |
| Styling    | Tailwind CSS v4 with a custom dark palette        |
| JS         | Alpine.js + Chart.js 4                            |
| Data       | MySQL `products` table via the query builder (no Eloquent) |

## Getting started

### Windows

Double-click **`setup.cmd`** — or right-click `setup.ps1` and choose *Run with
PowerShell* — from a copy of this folder. It:

1. installs anything missing from the toolchain (PHP 8.3+, Composer, Node.js
   LTS) using `winget`, enabling `pdo_mysql` and `zip` in `php.ini` as it goes;
2. runs `composer install` and `npm install && npm run build`;
3. installs XAMPP if no database server answers on port 3306, then starts
   MySQL/MariaDB — XAMPP is what provides **phpMyAdmin**;
4. asks for your MySQL host, database, user and password, preselecting the
   XAMPP defaults, and creates the database;
5. writes `.env` and `APP_KEY`, then runs `migrate` and `db:seed` for the 25
   products;
6. links `storage` and clears the caches;
7. offers to start `php artisan serve`.

The database password is proven against the server before `.env` is written,
so a rejected password never ends up in the file. XAMPP's `root` account has a
blank password by default; if the entered password is refused, the script
retries once with a blank one.

phpMyAdmin then lives at <http://localhost/phpmyadmin>. It is served by XAMPP's
Apache, so press **Start** next to **Apache** in the XAMPP Control Panel the
first time you want it.

Two XAMPP interactions are handled for you: its bundled PHP is usually older
than the 8.3 this project needs, so a standalone PHP 8.3+ is put ahead of it on
`PATH`, and its database is registered as a `MariaDB` service rather than
`MySQL`, so both names are looked for.

Installing anything needs Administrator rights, so if a step finds something
missing you will be asked to relaunch as Administrator. It is safe to run more
than once: finished steps are detected and skipped.

| Flag                    | Effect                                             |
| ----------------------- | -------------------------------------------------- |
| `-SkipPrompts`          | take every default, for unattended runs            |
| `-SkipToolchainInstall` | never install anything system-wide                |
| `-SkipServe`            | do not offer to start the server                  |
| `-DbUser` `-DbPassword` | supply database credentials instead of prompting   |

`setup.cmd` passes any flags straight through, so `setup.cmd -SkipServe` works.

### macOS / Linux

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force        # loads the 25 products
php artisan storage:link           # serves uploaded product images
npm run build                     # or: npm run dev
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
setup.cmd                 # double-click Windows installer
setup.ps1                 # what setup.cmd runs
app/
  Catalog/
    CatalogData.php        # the 25 products, verbatim from the DOCX
    Category.php           # the 5 subtopics as an enum
    Product.php            # row value object (price formatting, image URL)
    ProductRepository.php  # read/write, search, filter, sort, paginate
    ChartPalette.php       # shared chart colours
  Http/Controllers/        # Dashboard, Category, Product, Search, Page
database/
  migrations/              # the products table
  seeders/                 # CatalogSeeder, loads the 25 rows
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
