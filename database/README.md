# 🗄️ Murli Creations - Database Documentation & Setup Guide

Is directory me Murli Creations e-commerce aur Studio Admin portal ka complete database setup, schemas aur dumps maujood hain.

---

## 📁 Directory Structure

```text
database/
├── murli_creations_database.sql   # ⭐ Full 1-Click Production Database Dump (Schema + Data)
├── schema.sql                     # Clean DDL Table Structures & Indexes (19 Tables)
└── README.md                      # Yeh documentation aur step-by-step setup guide
```

---

## 📊 Database Tables Overview (Total: 19 Tables)

| # | Table Name | Description | Key Columns |
|---|------------|-------------|-------------|
| 1 | `mc_admins` | Admin & staff portal credentials | username, password_hash, role, status |
| 2 | `mc_admin_logs` | Security & audit logs for admin actions | username, action, ip_address, created_at |
| 3 | `mc_products` | Handcrafted artwork catalog & pricing | id, code, name, category, price, stock |
| 4 | `mc_product_specs` | Detailed art specifications | size, material, finish, care_instructions |
| 5 | `mc_orders` | Customer orders & payment status | order_number, total, payment_status, status |
| 6 | `mc_order_items` | Products within each order | order_id, product_name, quantity, price |
| 7 | `mc_order_shipments` | Logistics, courier & tracking (AWB) | tracking_number, courier, status |
| 8 | `mc_order_timeline` | Order lifecycle history events | order_id, event, note, timestamp |
| 9 | `mc_crm_leads` | Customer leads & inquiry pipeline | name, phone, email, status, budget |
| 10 | `mc_crm_notes` | Lead follow-up notes & comments | lead_id, note, author, created_at |
| 11 | `mc_customers` | Registered patrons & purchase history | phone, email, full_name, total_orders |
| 12 | `mc_customer_reminders` | Automated follow-up reminders | customer_id, channel, message, status |
| 13 | `mc_seo_meta` | Per-page SEO title, meta & OG tags | page_key, meta_title, meta_desc |
| 14 | `mc_email_templates` | Transactional email templates | slug, subject, body_html, variables |
| 15 | `mc_email_logs` | Outgoing email delivery logs | recipient, subject, status, sent_at |
| 16 | `mc_offers_cta` | Marketing banners & offer CTAs | banner_type, title, coupon, is_active |
| 17 | `mc_category_discounts` | Category-wide festival discounts | category_name, discount_percent, status |
| 18 | `mc_contact_inquiries` | Contact page messages & replies | name, email, phone, message, status |
| 19 | `mc_api_keys` | External logistics webhook API keys | service_name, api_key, is_active |

---

## 🚀 Hostinger / cPanel phpMyAdmin me Database Kaise Import Karein?

1. **Hostinger hPanel ya cPanel me login karein**:
   - `Databases` -> `MySQL Databases` me jaakar ek naya database banayein (e.g. `u123456789_murli_db`).
   - Ek naya database user banayein aur use full permissions grant karein.
2. **phpMyAdmin Open Karein**:
   - Apne naye database ko select karein.
   - Top menu me **`Import`** tab par click karein.
   - **`Choose File`** button par click karke is file ko select karein:
     ```text
     database/murli_creations_database.sql
     ```
   - Page ke bottom me **`Import` / `Go`** button dabayein.
   - 2-5 seconds me sabhi 19 tables aur 39 artworks ka complete data import ho jayega!
3. **Database Credentials Update Karein**:
   - File open karein: `backend/config/database.php` (ya `config/database.php`).
   - Apne Hostinger database ka name, username aur password set karein:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'u123456789_murli_db');
     define('DB_USER', 'u123456789_murli_user');
     define('DB_PASS', 'AapkaSecurePassword');
     ```

---

## 💻 Localhost (XAMPP / MAMP / Terminal) Setup

### Option A: phpMyAdmin (Browser)
1. Browser me open karein: `http://localhost/phpmyadmin`
2. Naya database banayein: `murli_creations_db`
3. `Import` tab me jaakar `database/murli_creations_database.sql` select karein aur `Go` dabayein.

### Option B: Terminal Command
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS murli_creations_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p murli_creations_db < database/murli_creations_database.sql
```

---

## 🔐 Default Admin Portal Login

- **Admin URL**: `http://your-domain.com/admin.php` ya `http://your-domain.com/backend/admin/`
- **Username**: `admin`
- **Password**: `murli@2026`
- **Role**: Super Admin

*(Aap admin panel ke "Logins Management" tab me jaakar kabhi bhi naya admin bana sakte hain ya password change kar sakte hain).*
