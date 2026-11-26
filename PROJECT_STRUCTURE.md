# 🏛️ Murli Creations - Project Architecture & Directory Structure

Namaste! Murli Creations project ko 3 primary dedicated core folders me divide kiya gaya hai:
1. **`frontend/`** : Storefront UI, customer-facing pages (`index.php`, `shop.php`, `product.php`, etc.), reusable components (`includes/`) aur styling (`assets/`).
2. **`backend/`**  : Studio Admin Portal (`admin.php`), REST API controllers (`api/`), business logic functions (`config/`) aur JSON storage (`storage/`).
3. **`database/`** : Complete production MySQL schema (`schema.sql`), 1-click importable SQL dump (`murli_creations_database.sql`) aur setup guides.

Root directory bilkul clean aur clutter-free hai, jisme sirf dynamic router (`router.php` / `index.php`), `.htaccess`, images aur test suite maujood hain.

---

## 📂 Complete Directory Structure

```text
Murli Creation Project/
│
├── 🎨 frontend/                     # SAARA FRONTEND CODE
│   ├── index.php                    # Storefront Homepage
│   ├── shop.php                     # Artwork Catalog & Category Filters
│   ├── product.php                  # Product Details, Image Zoom & Specs
│   ├── cart.php                     # Shopping Cart Page
│   ├── checkout.php                 # 3-Step Checkout with Partial COD & UPI
│   ├── order-confirmation.php       # Order Placed Success Screen
│   ├── track-order.php              # Live Shipment & Courier AWB Tracking
│   ├── contact.php                  # Studio Contact, Helpline & Visiting Hours
│   ├── about.php                    # Brand Heritage & Craftsmanship Process
│   ├── founder-story.php            # Master Artisan Murli Prasad Journey
│   ├── blog.php / blog-post.php     # Lippan Art Cultural Articles
│   ├── privacy-policy.php           # Privacy Policy
│   ├── terms.php                    # Terms & Conditions
│   ├── shipping-policy.php          # Shipping & Delivery Guidelines
│   ├── return-policy.php            # Return & Exchange Policies
│   ├── cancellation-policy.php      # Cancellation Guidelines
│   ├── 404.php                      # Not Found Error Page
│   ├── includes/                    # Reusable Storefront Components
│   │   ├── header.php               # Sticky Header, Promo Banner & Nav
│   │   ├── footer.php               # Studio Footer & Copyright
│   │   ├── product-card.php         # Handcrafted Artwork Card
│   │   ├── cart-drawer.php          # Interactive Slide-Out Cart Drawer
│   │   ├── floating-widgets.php     # WhatsApp Quick-Chat & Scroll-To-Top
│   │   ├── icons.php                # Clean SVG Icon Helper Library
│   │   └── toast.php                # Instant Notification Alerts
│   └── assets/                      # Frontend Static Styling & Client JS
│       ├── css/                     # Page-specific CSS files (home.css, shop.css, etc.)
│       └── js/                      # Frontend JavaScript (main.js, cart.js, etc.)
│
├── ⚙️ backend/                      # SAARA BACKEND CODE
│   ├── admin.php                    # Studio Administration Portal (14 Modules)
│   ├── api/                         # REST API Endpoints & AJAX Controllers
│   │   ├── admin.php                # Admin CRUD & Live Data Sync
│   │   ├── cart.php                 # Cart Drawer & Quantity Controls
│   │   ├── orders.php               # Order Creation & Checkout API
│   │   ├── contact.php              # Contact Form Handler
│   │   ├── reviews.php              # Customer Review Submissions
│   │   └── webhook.php              # Logistics Webhook for Couriers
│   ├── config/                      # Configuration & Core Store Functions
│   │   ├── database.php             # PDO Connection & Hostinger Credentials
│   │   ├── admin_functions.php      # Admin Database Queries, Auth & Analytics
│   │   ├── functions.php            # Core Pricing, Catalog & Discount Helpers
│   │   ├── data.php                 # Catalog Structures & Category Tree
│   │   └── db_init.php              # Auto-Migration & Database Setup
│   ├── storage/                     # JSON Fallback Data & Backups
│   │   ├── products.json            # 39 Handcrafted Artworks Backup
│   │   ├── orders.json              # Orders Backup
│   │   ├── site_settings.json       # Phone, WhatsApp & Address Backup
│   │   └── ...                      # Inquiries, Reviews, Coupons
│   └── assets/                      # Admin UI Styling & Scripts (admin.css, admin.js)
│
├── 🗄️ database/                     # SAARA DATABASE CODE
│   ├── murli_creations_database.sql # ⭐ Complete 1-Click Importable SQL Dump (19 Tables + Data)
│   ├── schema.sql                   # Clean Table Structures & Indexes DDL
│   └── README.md                    # Step-by-Step Setup Guide in Hindi & English
│
├── 🧪 tests/                        # AUTOMATED TEST SUITE (100% Passed)
│   ├── test_admin_suite.php         # 14 Core Requirements & Database Operations
│   ├── test_new_admin_features.php  # CSV Export, Bulk Discounts, Media Assets
│   ├── test_faq_persistence.php     # Cross-Page FAQ Persistence
│   ├── test_login_management.php    # User Roles, Bcrypt Auth & Audit Trail
│   └── test_stock_and_cleanup.php   # Inventory Toggle & Product Deletion
│
├── 🖼️ images/                       # Studio brand logos & artist photography
├── 🖼️ img-prodcut/                  # High-resolution artwork product photos
│
└── 🌐 [Root Clean Controllers]      # Web Gateway & Server Routers
    ├── router.php                   # Local development router (php -S 127.0.0.1:8000 router.php)
    ├── index.php                    # Front Controller Gateway
    ├── admin.php                    # Quick pointer to backend/admin.php
    ├── .htaccess                    # Hostinger / Apache mod_rewrite router
    └── Murli_Creations_Hostinger_Ready.zip # 1-Click Hostinger Production Zip (83MB)
```

---

## 🚀 How to Run Locally & Deploy to Hostinger

### 1. Local Development (Terminal)
```bash
php -S 127.0.0.1:8000 router.php
```
- Storefront: `http://127.0.0.1:8000/`
- Shop Catalog: `http://127.0.0.1:8000/shop.php`
- Admin Portal: `http://127.0.0.1:8000/admin.php`

### 2. Hostinger Live Deployment
- Hostinger hPanel ke File Manager me `public_html/` ke andar `Murli_Creations_Hostinger_Ready.zip` ko extract karein.
- phpMyAdmin me `database/murli_creations_database.sql` ko 1 click se import karein.
- `backend/config/database.php` me database name, user aur password update karein.
- Site turant live ho jayegi bina kisi configuration ke!
