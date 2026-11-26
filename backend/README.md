# ⚙️ Murli Creations - Backend Documentation & Architecture

Is directory me Murli Creations ka complete backend logic, admin panel, REST APIs, business logic functions aur secure database configurations sthit hain.

---

## 📁 Directory Structure

```text
backend/
├── admin/                         # 👑 Studio Administration Portal
│   ├── index.php                  # Main Admin Panel (14 Operational Modules)
│   └── assets/                    # Admin specific CSS & JS
│       ├── admin.css              # Modern admin UI styling & responsive layout
│       └── admin.js               # Multi-step artwork modal & AJAX operations
│
├── api/                           # 🔌 REST API Endpoints & AJAX Controllers
│   ├── admin.php                  # Admin panel live CRUD & data synchronization
│   ├── cart.php                   # Cart drawer, quantity updates & coupons
│   ├── orders.php                 # Order creation & checkout processing
│   ├── contact.php                # Contact inquiry submission handler
│   ├── reviews.php                # Verified customer review submissions
│   └── webhook.php                # Shipping & logistics carrier status webhooks
│
├── config/                        # 🛠️ Configuration & Core Business Logic
│   ├── database.php               # PDO database connection & hostinger credentials
│   ├── admin_functions.php        # Admin database queries, auth & audit logging
│   ├── functions.php              # Core store functions, prices, categories & discounts
│   ├── data.php                   # Fallback catalog & default store structures
│   └── db_init.php                # Automatic database migration & table installer
│
└── storage/                       # 💾 JSON Fallback Data Files & Backups
    ├── products.json              # Artwork catalog backup
    ├── orders.json                # Customer orders backup
    ├── site_settings.json         # Studio phone, WhatsApp & visiting hours
    ├── homepage_content.json      # Hero banner, artist story & announcements
    ├── contact_inquiries.json     # Contact form messages backup
    ├── reviews.json               # Customer testimonials backup
    ├── blog_posts.json            # Blog articles backup
    └── coupons.json               # Promo discount coupons
```

---

## 🚀 Key Modules & How to Customize

### 1. Database Connection (`backend/config/database.php`)
- Hostinger ya local MySQL server ke credentials yahan change karein:
  - `DB_HOST`: Hostinger par `localhost` hota hai.
  - `DB_NAME`: Aapka MySQL database name.
  - `DB_USER`: Database username.
  - `DB_PASS`: Database password.

### 2. Admin Panel (`backend/admin/index.php`)
- Isme 14 core modules unified hain:
  - **Product Management**: 5-Step visual creation wizard, instant stock counter & catalog table.
  - **Order Tracking & Approval**: Live status change (Pending -> Processing -> Shipped -> Delivered) aur AWB number assignment.
  - **Category Discounts**: Lippan Art, Shyam Ji Decor, etc. par 1-click se sabhi products par discount lagana.
  - **Page CMS**: Studio phone, WhatsApp helpline, workshop address aur visiting hours update karna.
  - **CRM & Customer Reminders**: Inquiry leads pipeline aur customer follow-up.
  - **Logins Management**: Team accounts banana, password change aur security audit log.

### 3. REST APIs (`backend/api/`)
- Frontend JavaScript `fetch()` calls inhi endpoints se communicate karti hain.
- Response format hamesha standard JSON hota hai: `{"success": true, "message": "..."}`.
