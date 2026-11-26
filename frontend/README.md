# 🎨 Murli Creations - Frontend Documentation & Architecture

Is directory me Murli Creations ka complete customer storefront, responsive UI pages, reusable components, CSS stylesheets aur JavaScript client logic sthit hai.

---

## 📁 Directory Structure

```text
frontend/
├── pages/                         # 📄 Storefront Full Page Views
│   ├── home.php                   # Homepage (Hero, collections, artisan story, customer reviews)
│   ├── shop.php                   # Catalog page with filter, sort, and search
│   ├── product.php                # Single product detail, specs, image zoom & buy buttons
│   ├── cart.php                   # Full cart view & quantity controls
│   ├── checkout.php               # 3-Step checkout (Address, Shipping, UPI/Partial COD)
│   ├── order-confirmation.php     # Order placed success screen with tracking link
│   ├── track-order.php            # Live shipment & AWB delivery tracking
│   ├── contact.php                # Studio contact form, phone, WhatsApp & visiting hours
│   ├── about.php                  # Brand story, craftsmanship process & authenticity
│   ├── founder-story.php          # Master craftsman Murli Prasad's journey
│   ├── blog.php                   # Lippan art culture & interior decor articles
│   ├── blog-post.php              # Single blog reader
│   └── policies/                  # Legal policies
│       ├── privacy-policy.php
│       ├── terms.php
│       ├── return-policy.php
│       ├── shipping-policy.php
│       ├── cancellation-policy.php
│       └── 404.php
│
├── components/                    # 🧩 Reusable UI Components & Widgets
│   ├── header.php                 # Sticky header, promo banner, search & navigation
│   ├── footer.php                 # Site footer with studio contact, links & copyright
│   ├── product-card.php           # Reusable artwork card with badge, rating & buy button
│   ├── cart-drawer.php            # Slide-out interactive cart drawer
│   ├── floating-widgets.php       # WhatsApp quick-chat & back-to-top floating buttons
│   ├── icons.php                  # SVG icon library helper (`icon_svg()`)
│   └── toast.php                  # Notification toast alerts
│
└── assets/                        # 🎨 Frontend Static Assets
    ├── css/                       # Stylesheets (home.css, shop.css, product.css, etc.)
    └── js/                        # Client-side JavaScript (main.js, cart.js, checkout.js, etc.)
```

---

## 💡 How to Customize & Edit Frontend

### 1. New Page Ya Banner Badalne Ke Liye:
- Homepage edit karne ke liye: `frontend/pages/home.php`
- Header navigation ya menu link badalne ke liye: `frontend/components/header.php`
- Footer text ya address badalne ke liye: `frontend/components/footer.php`
- Product cards ka layout badalne ke liye: `frontend/components/product-card.php`

### 2. Styling (CSS) Badalne Ke Liye:
- Sabhi page styles `frontend/assets/css/` me organized hain:
  - Global colors aur typography: `assets/css/globals.css`
  - Homepage design: `assets/css/home.css`
  - Shop catalog filters: `assets/css/shop.css`
  - Product page styling: `assets/css/product.css`

### 3. Backend Se Connection:
- Har page `ROOT_PATH . '/backend/config/functions.php'` ko require karta hai.
- Isse live MySQL database ka data, category tree aur cart functions automatically available rehte hain.
