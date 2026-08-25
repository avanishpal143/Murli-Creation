# Murli Creations - Pure PHP E-Commerce Platform

A production-quality, visually stunning, multi-page e-commerce website for **Murli Creations**, a handcrafted wall art and home décor studio in Rohtak, Haryana, India.

This project is built using **Pure PHP**, **Vanilla CSS**, and **Vanilla JavaScript** with zero third-party framework dependencies.

---

## 🔑 Admin Portal Credentials

- **Portal URL**: `/admin.php`
- **Username**: `admin`
- **Password**: `murli@2026`

---

## 🎨 Visual Design System & Aesthetics

- **Devlofox Navy & Azure Palette**: `#00234b`, `#0099fa`, and `#F4F7FA`
- **Modern Typography**: Google Fonts `Open Sans` and `Playwrite BR`
- **3D Interactive Tilt**: 60fps vanilla JS engine with specular glare reflection on product cards and footer
- **Interactive Cart & Drawer**: Live AJAX-powered slide-in cart drawer synchronized with PHP sessions
- **Responsive Layout**: Glassmorphic pill navigation, mobile menu drawer, and adaptive layouts for all screen sizes

---

## 🚀 How to Run the Project Locally

### 1. Prerequisites
Make sure PHP (v7.4 or higher, recommended v8.x) is installed on your system.

### 2. Start the Local PHP Server
Run the built-in PHP development server in the project directory:

```bash
php -S 127.0.0.1:8000
```

### 3. Open in Browser
Visit [http://127.0.0.1:8000/](http://127.0.0.1:8000/) in your browser.

---

## 📁 Project Architecture & Directory Structure

```
├── .htaccess                     # Clean URL rewriting for Apache servers
├── 404.php                       # Branded 404 Not Found error page
├── admin.php                     # Admin login & full management dashboard
├── cart.php                      # Shopping cart page with coupon support
├── checkout.php                  # 3-step checkout wizard with Razorpay & COD
├── contact.php                   # Contact form & FAQ accordions
├── founder-story.php             # Founder's narrative & brand promise
├── index.php                     # Homepage with 3D tilt, stats, featured products
├── instagram.php / journal.php   # Studio Instagram social feed
├── our-craft.php                 # 4-step craft process & artisan spotlight
├── product.php                   # Product details, gallery switcher & reviews
├── shop.php                      # Catalog with live filters, slider, quick view
│
├── api/                          # Backend REST / AJAX Handlers
│   ├── admin.php                 # Admin authentication & catalog CRUD
│   ├── cart.php                  # Add, update, remove, subtotal sync
│   ├── contact.php               # Customer inquiry submission
│   ├── orders.php                # Order creation & status updates with stock restoration
│   └── reviews.php               # Patron review submission
│
├── assets/
│   ├── css/                      # 18 Modular Vanilla CSS Stylesheets
│   │   ├── globals.css           # Design tokens, fonts, and reset
│   │   ├── header.css, footer.css, product-card.css, cart-drawer.css
│   │   └── home.css, shop.css, product.css, cart.css, checkout.css, etc.
│   └── js/                       # Vanilla JavaScript Client Engines
│       ├── cart.js               # AJAX Cart & Drawer synchronized with PHP session
│       ├── checkout.js           # 3-step wizard validation & Razorpay modal
│       ├── main.js               # Scroll-reveal observer, mobile drawer, search popup
│       ├── product.js            # Media switcher, quantity picker, reviews submitter
│       ├── shop.js               # Price slider, category filtering & Quick View modal
│       ├── tilt.js               # 60fps 3D interactive tilt with glare overlay
│       └── toast.js              # Floating notification alert system
│
├── config/
│   ├── data.php                  # Catalog seed data with all 36 products & reviews
│   └── functions.php             # JSON file persistence, session cart, orders, stock
│
├── images/                       # Product gallery, Instagram feed & brand logos
└── storage/                      # Self-initializing JSON databases
    ├── products.json             # 36 products catalog
    ├── orders.json               # Customer orders
    └── reviews.json              # Verified patron reviews
```

---

## ⚡ Deployment to Production (Apache / Nginx / cPanel)

1. Upload all files to your web root (`public_html` or `/var/www/html/`).
2. Ensure the `storage/` directory has write permissions (`chmod 775 storage`).
3. For Apache servers, `.htaccess` automatically handles clean URLs.
4. For Nginx servers, add `try_files $uri $uri/ $uri.php?$query_string;` to your configuration.
