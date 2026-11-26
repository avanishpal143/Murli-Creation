# 🏛️ Murli Creations - Project Architecture & Directory Structure

Namaste! Murli Creations project ko 3 primary core layers me divide kiya gaya hai:
1. **`frontend/`** : Storefront UI, customer pages, components aur styles.
2. **`backend/`**  : Studio Admin Portal, REST API endpoints, business logic functions aur storage.
3. **`database/`** : Complete production MySQL schema, 1-click importable SQL dump aur guides.

Root directory me transparent delegator entry points hain, jisse **Hostinger shared hosting (`public_html`)** aur **Local development servers** dono par bina kisi configuration ke saari cheezein 100% smoothly chalti hain.

---

## 📂 Complete Directory Tree

```text
Murli Creation Project/
│
├── 🎨 frontend/                     # CUSTOMER STOREFRONT & USER INTERFACE
│   ├── pages/                       # Full page views (Storefront)
│   │   ├── home.php                 # Homepage (Hero, collections, artisan story, reviews)
│   │   ├── shop.php                 # Catalog with filter, sort & search
│   │   ├── product.php              # Single product detail, specs, image zoom & buy buttons
│   │   ├── cart.php                 # Shopping cart view
│   │   ├── checkout.php             # 3-step checkout with address & payment
│   │   ├── order-confirmation.php   # Order placed success screen with tracking link
│   │   ├── track-order.php          # Live shipment & AWB delivery tracking
│   │   ├── contact.php              # Studio contact form, phone, WhatsApp & visiting hours
│   │   ├── about.php                # Brand story, craftsmanship & authenticity
│   │   ├── founder-story.php        # Master craftsman Murli Prasad's journey
│   │   ├── blog.php                 # Lippan art culture & interior decor articles
│   │   ├── blog-post.php            # Single blog reader
│   │   └── policies/                # Legal policies & error pages
│   │       ├── privacy-policy.php
│   │       ├── terms.php
│   │       ├── return-policy.php
│   │       ├── shipping-policy.php
│   │       ├── cancellation-policy.php
│   │       └── 404.php
│   ├── components/                  # Reusable UI Components
│   │   ├── header.php               # Sticky header, promo banner, search & navigation
│   │   ├── footer.php               # Site footer with studio contact, links & copyright
│   │   ├── product-card.php         # Reusable artwork card with badge, rating & buy button
│   │   ├── cart-drawer.php          # Slide-out interactive cart drawer
│   │   ├── floating-widgets.php     # WhatsApp quick-chat & back-to-top buttons
│   │   ├── icons.php                # SVG icons helper library (`icon_svg()`)
│   │   └── toast.php                # Notification toast alerts
│   └── assets/                      # Static Assets
│       ├── css/                     # Page stylesheets (home.css, shop.css, etc.)
│       └── js/                      # Frontend client JavaScript
│
├── ⚙️ backend/                      # STUDIO ADMIN, APIS & CORE LOGIC
│   ├── admin/                       # Administration Portal
│   │   ├── index.php                # Main Admin Management Portal (14 Unified Tabs)
│   │   └── assets/                  # Admin UI styling & scripts (admin.css, admin.js)
│   ├── api/                         # REST API Endpoints & AJAX Controllers
│   │   ├── admin.php                # Admin live CRUD & data synchronization
│   │   ├── cart.php                 # Cart drawer, quantity adjustments & coupon calculation
│   │   ├── orders.php               # Order placement & checkout processing
│   │   ├── contact.php              # Contact inquiry submission handler
│   │   ├── reviews.php              # Product customer review submissions
│   │   └── webhook.php              # Third-party logistics & carrier tracking webhook
│   ├── config/                      # Configuration & Business Logic Functions
│   │   ├── database.php             # Database credentials & PDO connection
│   │   ├── admin_functions.php      # Admin database queries, auth, logs & analytics logic
│   │   ├── functions.php            # Core store functions, prices, discounts & catalog
│   │   ├── data.php                 # Initial fallback catalog & category tree
│   │   └── db_init.php              # Database schema auto-migration & initializer
│   └── storage/                     # JSON Fallback Data Files & Backups
│       ├── products.json            # 39 handcrafted artworks catalog
│       ├── orders.json              # Order data backup
│       ├── site_settings.json       # Studio phone, WhatsApp & visiting hours
│       ├── homepage_content.json    # Hero banner, artist story & announcements
│       ├── contact_inquiries.json   # Customer inquiries backup
│       ├── reviews.json             # Customer testimonials backup
│       └── coupons.json             # Promo coupons backup
│
├── 🗄️ database/                     # DATABASE SCHEMAS & 1-CLICK DUMPS
│   ├── murli_creations_database.sql # Complete 1-Click Importable SQL Dump (19 Tables + Data)
│   ├── schema.sql                   # Clean DDL Table Structures & Indexes
│   └── README.md                    # Step-by-step setup guide for Hostinger & Localhost
│
├── 🧪 tests/                        # AUTOMATED TEST SUITE (100% Coverage)
│   ├── test_admin_suite.php         # Tests all 14 core requirements & database operations
│   ├── test_new_admin_features.php  # Tests reviews toggle, CSV export, bulk discount, media
│   ├── test_faq_persistence.php     # Tests FAQ saving & cross-page synchronization
│   ├── test_login_management.php    # Tests user roles, bcrypt auth & audit trail
│   └── test_stock_and_cleanup.php   # Tests inventory toggle & product deletion
│
├── 🖼️ images/                       # Brand logos, banners & studio photos
├── 🖼️ img-prodcut/                  # High-resolution artwork catalog photos
│
└── 🌐 [Root Entry Points]           # Web-Root Delegates for Hostinger & Apache
    ├── index.php                    # Delegates to frontend/pages/home.php
    ├── shop.php                     # Delegates to frontend/pages/shop.php
    ├── product.php                  # Delegates to frontend/pages/product.php
    ├── cart.php                     # Delegates to frontend/pages/cart.php
    ├── checkout.php                 # Delegates to frontend/pages/checkout.php
    ├── order-confirmation.php       # Delegates to frontend/pages/order-confirmation.php
    ├── track-order.php              # Delegates to frontend/pages/track-order.php
    ├── contact.php                  # Delegates to frontend/pages/contact.php
    ├── about.php                    # Delegates to frontend/pages/about.php
    ├── founder-story.php            # Delegates to frontend/pages/founder-story.php
    ├── blog.php                     # Delegates to frontend/pages/blog.php
    ├── blog-post.php                # Delegates to frontend/pages/blog-post.php
    ├── admin.php                    # Delegates to backend/admin/index.php
    └── Murli_Creations_Hostinger_Ready.zip # Production deployment package
```

---

## 🎯 Important Features

1. **Clean Separation of Concerns**:
   - Kisi ko frontend UI badalna hai to sirf `frontend/` me kaam karega.
   - Kisi ko admin panel, APIs ya database functions edit karne hain to `backend/` me kaam karega.
   - Database import ya setup ke liye `database/` folder me full SQL dump aur simple guide maujood hai.
2. **Zero-Breakage Guarantee**:
   - Sabhi URLs (`/`, `/shop.php`, `/admin.php`, etc.), form actions (`checkout.php`), aur API endpoints (`/api/cart.php`) pehle ki tarah 100% functioning hain.
   - Koi bhi button, modal, function ya UI element break nahi hua hai.
3. **Hostinger Ready**:
   - Hostinger ke `public_html` me directly upload karne par yeh bina kisi complex rewrite ya subfolder configuration ke directly live ho jata hai.
