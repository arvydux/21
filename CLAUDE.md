# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel 12 POS (Point of Sale) system for a Lithuanian food establishment (Kibinai/Ceburek bakery). Features a cashier interface, kitchen display system, and menu management with thermal printer support.

## Commands

```bash
# Development (starts server, queue worker, logs, and Vite simultaneously)
composer dev

# Frontend
npm run dev      # Vite dev server
npm run build    # Production build

# Database
php artisan migrate:fresh --seed   # Reset DB with seed data

# Testing
php artisan test                   # Run all tests
php artisan test --filter=TestName # Run a single test
./vendor/bin/pest                  # Run tests via Pest directly
```

## Architecture

**Tech Stack:** Laravel 12, Livewire/Volt, Tailwind CSS 4, SQLite/MySQL, ESC/POS thermal printing

**Main views** (in `resources/views/`):
- `kasa.blade.php` — Cashier/POS interface
- `kitchen.blade.php` — Kitchen display system (KDS)
- `orders.blade.php` — Customer pickup display
- `menu.blade.php` — Menu management

**Livewire components** drive all interactivity (`app/Livewire/`):
- `KasaOrders` — POS cart and checkout; triggers printer
- `KasaMenu` — Category selector with drag-reorder
- `KasaCategorySection` — Product buttons for selected category
- `Buttons/` — Per-product-type add-to-cart buttons (Ceburekai, Kibinai, Drinks, Toppings, OtherProducts)
- `Orders/OrdersList` — Cart management, order finalization, receipt printing
- `Kitchen` — KDS: shows pending orders, marks them ready
- `Menu/` — CRUD forms and tables for each product type

**Key Livewire events:**
- `category-button-clicked` — User selects a category
- `change-order` — Cart updated
- `order-made` — Checkout completed
- `reset-orders` — Cart cleared
- `productAdded` — New menu item created

**Product models** (`app/Models/`): `Kibinai`, `Ceburek`, `Drink`, `Topping`, `OtherProduct` — each has its own table, seeder, and Livewire CRUD components. Common fields: `name`, `price`, `show`, `position` (for ordering).

**Order flow:**
1. Cashier selects category → product buttons appear
2. Products added to cart (OrdersList)
3. On checkout: `OrderNumbers` record created, receipt printed via ESC/POS
4. Kitchen sees new order → marks ready → customer pickup display updates

## Printer Configuration

Printers configured in `config/printers.php` via env vars:
```
KITCHEN_PRINTER_IP=192.168.1.101
KITCHEN_PRINTER_PORT=9100
CLIENT_PRINTER_NAME=pos-80
```

The printer code handles Lithuanian character encoding conversion for thermal printers.

## Database

Default connection is SQLite. MySQL is also supported — set `DB_CONNECTION=mysql` and configure credentials. The README references a database named `"prusakovas"` for MySQL setup.

Audio files (`public/12.mp3`, `public/14.mp3`) are played as order-ready notifications in the browser.