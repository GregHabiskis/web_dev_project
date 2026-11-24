# SalesPro - PHP & MySQL E-Commerce Platform

A high-performance e-commerce website built with **PHP 8** and **MySQL / MariaDB** (database: `salespro`), converted from static HTML/CSS/JS.

## 🚀 Key Features

1. **Database-Driven Catalog**:
   - Products and categories are stored and retrieved from the `salespro` MySQL database.
   - Dynamic category filtering, search keyword query, and price sorting (low to high, high to low, name, featured).
   - Dedicated product detail view (`product-detail.php`) with stock checks and related products.
2. **Shopping Cart & Checkout**:
   - Session-backed cart with dynamic quantity increment, decrement, and item removal.
   - Real-time cart badge indicator in the header.
   - Complete checkout form: Customer Name, Email, Phone, Shipping Address, Payment Method, and Order Notes.
   - Automatically calculates shipping ($25 or **Free** for orders > $500).
   - Saves order and line items into `orders` and `order_items` tables.
   - Generates unique order number (e.g. `SP-20261117-XXXXX`) and printable confirmation receipt (`order-success.php`).
3. **Contact & Newsletter Inquiries**:
   - Contact form submissions are validated and stored in `contact_messages`.
   - Newsletter subscription form saves to `subscribers` table via AJAX.
4. **Administration Portal (`/admin`)**:
   - Secure login (`admin/login.php`) with password hashing.
   - **Dashboard**: Revenue KPIs, Total Orders, Product Count, Inquiries, recent orders and messages.
   - **Product Management**: Add new products, edit price/stock/details, toggle featured status, delete products.
   - **Order Management**: View all orders, update status (`Pending`, `Processing`, `Shipped`, `Delivered`, `Cancelled`), view itemized invoice.
   - **Customer Inquiries**: Read, toggle read/unread status, and delete messages.
   - **Subscribers**: Manage newsletter subscriber list.

---

## 🗄️ Database Setup (`salespro`)

The database is already configured and initialized with seed data in `salespro.sql`.

- **Database Name**: `salespro`
- **Host**: `127.0.0.1` (or `localhost`)
- **Port**: `3306`
- **User**: `root`
- **Password**: *(empty by default on XAMPP)*

To re-import or reset the database at any time:
```bash
mysql -u root -p salespro < salespro.sql
```

### Tables Overview
- `categories`: Product categories (Laptops, Desktops, Printers, Monitors, Accessories).
- `products`: Product catalog with name, slug, price, description, image, stock, featured flag.
- `orders`: Customer orders, delivery addresses, payment methods, subtotal, shipping fee, total amount, status.
- `order_items`: Line items linked to orders.
- `contact_messages`: Contact form submissions with read status.
- `subscribers`: Email newsletter subscribers.
- `admins`: Administrative users.

---

## 🔐 Default Admin Credentials

- **URL**: `http://localhost/admin/` (or `http://localhost:8000/admin/`)
- **Username**: `admin`
- **Password**: `admin123`

---

## 💻 Running the Application

### Option A: Using Built-in PHP Server (Fastest)
Run the following in the project directory:
```bash
php -S 127.0.0.1:8000
```
Then open:
- Store: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`

### Option B: Using XAMPP Apache
1. Copy or link this folder into `C:\xampp\htdocs\salespro`
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/salespro/` in your browser.

