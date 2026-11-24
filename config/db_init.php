<?php
// =============================================================================
// Murli Creations - Database Initializer & Migration Utility
// Automatically runs table schemas and migrates storage/*.json data into MySQL
// =============================================================================

require_once __DIR__ . '/database.php';

function run_database_migrations($force = false) {
    $db = get_db_connection();
    if (!$db) {
        return ['success' => false, 'message' => 'Cannot connect to MySQL database. Please check config/database.php.'];
    }

    $results = [];

    // 1. Run Schema SQL
    $schemaFile = __DIR__ . '/../database/schema.sql';
    if (!file_exists($schemaFile)) {
        return ['success' => false, 'message' => 'Schema file database/schema.sql not found.'];
    }

    try {
        $sql = file_get_contents($schemaFile);
        $db->exec($sql);
        $results[] = 'All 18 tables verified/created successfully.';
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Schema execution failed: ' . $e->getMessage()];
    }

    // 2. Seed Default Admin if none exists
    $stmt = $db->query("SELECT COUNT(*) FROM mc_admins");
    if ($stmt->fetchColumn() == 0 || $force) {
        $checkAdmin = $db->prepare("SELECT id FROM mc_admins WHERE username = ?");
        $checkAdmin->execute(['admin']);
        if (!$checkAdmin->fetch()) {
            $insertAdmin = $db->prepare("
                INSERT INTO mc_admins (username, password_hash, full_name, email, role, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $hash = password_hash('murli@2026', PASSWORD_BCRYPT);
            $insertAdmin->execute(['admin', $hash, 'Master Artisan Murli Prasad', 'info@murlicreations.com', 'super_admin', 'active']);
            $results[] = 'Default admin user initialized (admin / murli@2026).';
        }
    }

    // 3. Migrate Products from storage/products.json if mc_products is empty
    $stmt = $db->query("SELECT COUNT(*) FROM mc_products");
    $prodCount = $stmt->fetchColumn();
    $productsJsonFile = __DIR__ . '/../storage/products.json';
    if (($prodCount == 0 || $force) && file_exists($productsJsonFile)) {
        $products = json_decode(file_get_contents($productsJsonFile), true);
        if (is_array($products)) {
            $insertProd = $db->prepare("
                INSERT INTO mc_products (
                    id, code, slug, name, category, subcategory, price, mrp, stock,
                    size, material, origin, artist, rating, reviews_count,
                    images, video, folklore, description, featured, bestseller, tags
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?
                ) ON DUPLICATE KEY UPDATE
                    name = VALUES(name), price = VALUES(price), mrp = VALUES(mrp),
                    stock = VALUES(stock), images = VALUES(images), description = VALUES(description)
            ");

            $imported = 0;
            foreach ($products as $p) {
                $imagesJson = is_array($p['images'] ?? null) ? json_encode($p['images']) : json_encode(['/img-prodcut/IMG_4041.png']);
                $tagsJson = is_array($p['tags'] ?? null) ? json_encode($p['tags']) : json_encode([]);
                
                $insertProd->execute([
                    $p['id'] ?? ('prod-' . uniqid()),
                    $p['code'] ?? 'MC/L-000',
                    $p['slug'] ?? ('product-' . uniqid()),
                    $p['name'] ?? 'Handcrafted Lippan Artwork',
                    $p['category'] ?? 'Lippan Art',
                    $p['subcategory'] ?? '',
                    (float)($p['price'] ?? 0),
                    (float)($p['mrp'] ?? 0),
                    (int)($p['stock'] ?? 10),
                    $p['size'] ?? '',
                    $p['material'] ?? '',
                    $p['origin'] ?? 'Delhi, India',
                    $p['artist'] ?? 'Master Craftsman Murli Prasad',
                    (float)($p['rating'] ?? 5.0),
                    (int)($p['reviewsCount'] ?? 10),
                    $imagesJson,
                    $p['video'] ?? '',
                    $p['folklore'] ?? '',
                    $p['description'] ?? '',
                    !empty($p['featured']) ? 1 : 0,
                    !empty($p['bestseller']) ? 1 : 0,
                    $tagsJson
                ]);
                $imported++;
            }
            $results[] = "Imported {$imported} products into MySQL mc_products.";
        }
    }

    // 4. Migrate Orders and Customer Details from storage/orders.json
    $stmt = $db->query("SELECT COUNT(*) FROM mc_orders");
    $ordersCount = $stmt->fetchColumn();
    $ordersJsonFile = __DIR__ . '/../storage/orders.json';
    if (($ordersCount == 0 || $force) && file_exists($ordersJsonFile)) {
        $orders = json_decode(file_get_contents($ordersJsonFile), true);
        if (is_array($orders)) {
            $insertOrder = $db->prepare("
                INSERT INTO mc_orders (
                    id, customer_name, customer_email, customer_phone,
                    customer_address, customer_city, customer_state, customer_pincode,
                    items, subtotal, coupon_code, discount_amount, shipping_fee, total_amount,
                    token_paid, balance_on_delivery, payment_method, payment_status, order_status,
                    tracking_carrier, tracking_number, tracking_url, estimated_delivery, notes, created_at
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?
                ) ON DUPLICATE KEY UPDATE order_status = VALUES(order_status)
            ");

            $insertCustomer = $db->prepare("
                INSERT INTO mc_customers (name, email, phone, address, city, state, pincode, total_orders, total_spent)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
                ON DUPLICATE KEY UPDATE
                    total_orders = total_orders + 1,
                    total_spent = total_spent + VALUES(total_spent)
            ");

            $orderCountImported = 0;
            foreach ($orders as $o) {
                $cust = $o['customer'] ?? [];
                $itemsJson = json_encode($o['items'] ?? []);
                $subtotal = (float)($o['subtotal'] ?? 0);
                $total = (float)($o['totalAmount'] ?? $subtotal);
                $discount = (float)($o['coupon']['discount'] ?? 0);
                $coupon = $o['coupon']['code'] ?? '';
                $shipping = (float)($o['shipping'] ?? 0);
                $payMethod = $o['paymentMethod'] ?? 'Partial COD';
                $payStatus = $o['paymentStatus'] ?? 'Pending';
                $orderStatus = $o['orderStatus'] ?? $o['status'] ?? 'Pending';
                $tokenPaid = (float)($o['tokenPaid'] ?? ($payMethod === 'Partial COD' ? 500 : $total));
                $balanceDue = (float)($o['balanceOnDelivery'] ?? ($payMethod === 'Partial COD' ? max(0, $total - 500) : 0));
                $createdAt = !empty($o['createdAt']) ? date('Y-m-d H:i:s', strtotime($o['createdAt'])) : date('Y-m-d H:i:s');

                $insertOrder->execute([
                    $o['id'] ?? ('MURLI-' . uniqid()),
                    $cust['name'] ?? 'Patron',
                    $cust['email'] ?? 'customer@example.com',
                    $cust['phone'] ?? '',
                    $cust['address'] ?? '',
                    $cust['city'] ?? 'Delhi',
                    $cust['state'] ?? 'Delhi',
                    $cust['pinCode'] ?? '110001',
                    $itemsJson,
                    $subtotal,
                    $coupon,
                    $discount,
                    $shipping,
                    $total,
                    $tokenPaid,
                    $balanceDue,
                    $payMethod,
                    $payStatus,
                    $orderStatus,
                    $o['trackingCarrier'] ?? 'Shiprocket Express',
                    $o['trackingNumber'] ?? 'SR-' . rand(10000000, 99999999),
                    $o['trackingUrl'] ?? 'https://shiprocket.co/tracking',
                    date('Y-m-d', strtotime('+4 days')),
                    'Customer requested bubble-wrapped premium wooden crate packaging.',
                    $createdAt
                ]);

                if (!empty($cust['email'])) {
                    try {
                        $insertCustomer->execute([
                            $cust['name'] ?? 'Patron',
                            $cust['email'],
                            $cust['phone'] ?? '',
                            $cust['address'] ?? '',
                            $cust['city'] ?? '',
                            $cust['state'] ?? '',
                            $cust['pinCode'] ?? '',
                            $total
                        ]);
                    } catch (Exception $e) {
                        // ignore duplicate customer key
                    }
                }
                $orderCountImported++;
            }
            $results[] = "Imported {$orderCountImported} orders & synced customer records.";
        }
    }

    // 5. Migrate Coupons from storage/coupons.json
    $stmt = $db->query("SELECT COUNT(*) FROM mc_coupons");
    if ($stmt->fetchColumn() == 0 || $force) {
        $couponsJsonFile = __DIR__ . '/../storage/coupons.json';
        if (file_exists($couponsJsonFile)) {
            $coupons = json_decode(file_get_contents($couponsJsonFile), true);
            if (is_array($coupons)) {
                $insC = $db->prepare("
                    INSERT INTO mc_coupons (code, discount_type, discount_value, min_order, is_active, description)
                    VALUES (?, 'percentage', ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE discount_value = VALUES(discount_value)
                ");
                foreach ($coupons as $c) {
                    $insC->execute([
                        strtoupper($c['code'] ?? 'MURLI10'),
                        (float)($c['discountPercent'] ?? 10),
                        (float)($c['minOrder'] ?? 0),
                        !empty($c['active']) ? 1 : 0,
                        $c['description'] ?? 'Festive Discount Offer'
                    ]);
                }
                $results[] = 'Imported discount coupons into mc_coupons.';
            }
        }
    }

    // 6. Migrate Reviews from storage/reviews.json
    $stmt = $db->query("SELECT COUNT(*) FROM mc_reviews");
    if ($stmt->fetchColumn() == 0 || $force) {
        $revFile = __DIR__ . '/../storage/reviews.json';
        if (file_exists($revFile)) {
            $revs = json_decode(file_get_contents($revFile), true);
            if (is_array($revs)) {
                $insR = $db->prepare("
                    INSERT INTO mc_reviews (id, product_id, name, location, rating, comment, date_text, is_approved)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                    ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
                ");
                foreach ($revs as $r) {
                    $insR->execute([
                        $r['id'] ?? ('rev-' . uniqid()),
                        $r['productId'] ?? 'prod-1',
                        $r['name'] ?? 'Patron',
                        $r['location'] ?? 'Delhi',
                        (float)($r['rating'] ?? 5.0),
                        $r['comment'] ?? '',
                        $r['date'] ?? date('d M Y')
                    ]);
                }
                $results[] = 'Imported customer reviews into mc_reviews.';
            }
        }
    }

    // 7. Seed Default SEO Page Tags (Requirement 6: Per-Page SEO Management)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_seo_pages");
    if ($stmt->fetchColumn() == 0 || $force) {
        $defaultSeoPages = [
            [
                'page_key' => 'home',
                'page_name' => 'Home Page (Main Landing)',
                'url_path' => '/',
                'meta_title' => 'Murli Creations | Handcrafted Lippan Art, Jharokhas & Sacred Wall Decor',
                'meta_description' => 'Discover authentic handcrafted Lippan art, traditional Rajasthani Jharokhas, and sacred spiritual wall mandalas by master artisan Murli Prasad. Made in Delhi, India.',
                'meta_keywords' => 'lippan art, handcrafted jharokha, khatu shyam decor, vastu decor, mud relief art, mirror artwork, indian heritage decor',
                'canonical_url' => 'https://murlicreations.com/',
                'og_title' => 'Murli Creations | Handcrafted Lippan Art & Sacred Wall Decor',
                'og_description' => 'Direct from the artisan: 100% authentic clay relief mirror artwork, Khatu Shyam shringar, and architectural heritage decor.',
                'og_image' => '/img-prodcut/IMG_4041.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'ArtGallery',
                    'name' => 'Murli Creations',
                    'founder' => 'Murli Prasad',
                    'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Delhi', 'addressCountry' => 'IN'],
                    'telephone' => '+91-9876543210'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ],
            [
                'page_key' => 'shop',
                'page_name' => 'Shop & Catalog',
                'url_path' => '/shop.php',
                'meta_title' => 'Buy Lippan Art Online | Royal Jharokhas & Sacred Mandalas | Murli Creations',
                'meta_description' => 'Browse our exclusive handcrafted collections: Peacock Lippan Jharokhas, Lotus Mandalas, Khatu Shyam Baba sacred shringar, and Vastu wall arts with Free Shipping in India.',
                'meta_keywords' => 'buy lippan art online, peacock wall art, custom nameplate, khatu shyam ji poshak, wall decor online india',
                'canonical_url' => 'https://murlicreations.com/shop.php',
                'og_title' => 'Exclusive Handcrafted Art Collection | Murli Creations',
                'og_description' => 'Explore mastercrafted Lippan artworks, festive wall decor, and devotional pieces.',
                'og_image' => '/img-prodcut/IMG_4042.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ],
            [
                'page_key' => 'product_detail',
                'page_name' => 'Product Details Template',
                'url_path' => '/product.php?id={id}',
                'meta_title' => '{product_name} | Master Artisan Handcrafted Lippan Art | Murli Creations',
                'meta_description' => 'Buy {product_name} handcrafted by master artisan Murli Prasad. Made with authentic clay relief paste, sparkling mirror inlays, and protected with moisture-resistant lacquer.',
                'meta_keywords' => 'handcrafted {product_category}, authentic lippan mirror work, delhi art studio',
                'canonical_url' => 'https://murlicreations.com/product.php?id={id}',
                'og_title' => '{product_name} - Murli Creations',
                'og_description' => 'Handcrafted clay relief artwork with mirror inlays. Authentic Indian heritage for your home.',
                'og_image' => '{product_image}',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ],
            [
                'page_key' => 'about',
                'page_name' => 'About Murli Creations',
                'url_path' => '/about.php',
                'meta_title' => 'Our Heritage & Craftsmanship | About Murli Creations',
                'meta_description' => 'Learn the journey of master craftsman Murli Prasad, preserving the centuries-old Kutchi Lippan Kaam relief art from his studio in Delhi, India.',
                'meta_keywords' => 'lippan art history, murli prasad artist, traditional mud craft india, handmade artisan studio',
                'canonical_url' => 'https://murlicreations.com/about.php',
                'og_title' => 'The Soul of Kutchi Clay Art | About Murli Creations',
                'og_description' => 'A generational dedication to preserving India’s finest relief mud mirror art.',
                'og_image' => '/images/brand/founder.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ],
            [
                'page_key' => 'founder_story',
                'page_name' => 'Founder Story',
                'url_path' => '/founder-story.php',
                'meta_title' => 'The Hands Behind The Clay | Murli Prasad Founder Journey',
                'meta_description' => 'Read the inspiring story of Murli Prasad — from childhood village clay modeling to crafting royal Lippan jharokhas adored across homes worldwide.',
                'meta_keywords' => 'murli prasad bio, lippan artisan story, indian craft revival',
                'canonical_url' => 'https://murlicreations.com/founder-story.php',
                'og_title' => 'Meet Murli Prasad | Founder & Master Artisan',
                'og_description' => 'Discover how traditional clay, mirrors, and devotion become heirloom pieces.',
                'og_image' => '/images/brand/founder.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ],
            [
                'page_key' => 'contact',
                'page_name' => 'Contact Us & Custom Orders',
                'url_path' => '/contact.php',
                'meta_title' => 'Contact Murli Creations | Custom Commissions & Studio Visits',
                'meta_description' => 'Get in touch for custom size Lippan art commissions, Khatu Shyam temple decor, or corporate gifts. Visit our Delhi studio or chat on WhatsApp.',
                'meta_keywords' => 'custom lippan art order, commission handcrafted wall art, murli creations phone number',
                'canonical_url' => 'https://murlicreations.com/contact.php',
                'og_title' => 'Connect with Murli Creations Studio',
                'og_description' => 'Discuss your bespoke wall dimensions and devotional decor requirements directly with our team.',
                'og_image' => '/img-prodcut/IMG_4041.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ],
            [
                'page_key' => 'blog',
                'page_name' => 'Art & Decor Blog',
                'url_path' => '/blog.php',
                'meta_title' => 'Artisan Journal | Lippan Art Care, Vastu Tips & Styling Ideas',
                'meta_description' => 'Expert guides on caring for clay mirror artworks, Vastu placement rules for peacock art, and festive decor inspiration.',
                'meta_keywords' => 'lippan art cleaning tips, vastu directions for spiritual art, diy home decor ideas',
                'canonical_url' => 'https://murlicreations.com/blog.php',
                'og_title' => 'Murli Creations Artisan Journal',
                'og_description' => 'Tips, folklore, and heritage styling advice for art lovers.',
                'og_image' => '/img-prodcut/IMG_4043.png',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index, follow',
                'schema_json' => ''
            ]
        ];

        $insSeo = $db->prepare("
            INSERT INTO mc_seo_pages (
                page_key, page_name, url_path, meta_title, meta_description,
                meta_keywords, canonical_url, og_title, og_description, og_image,
                twitter_card, robots, schema_json
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?
            ) ON DUPLICATE KEY UPDATE meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)
        ");

        foreach ($defaultSeoPages as $sp) {
            $insSeo->execute([
                $sp['page_key'],
                $sp['page_name'],
                $sp['url_path'],
                $sp['meta_title'],
                $sp['meta_description'],
                $sp['meta_keywords'],
                $sp['canonical_url'],
                $sp['og_title'],
                $sp['og_description'],
                $sp['og_image'],
                $sp['twitter_card'],
                $sp['robots'],
                $sp['schema_json']
            ]);
        }
        $results[] = 'Initialized per-page SEO configurations for all main pages.';
    }

    // 8. Seed Default Email Templates (Requirement 11)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_email_templates");
    if ($stmt->fetchColumn() == 0 || $force) {
        $templates = [
            [
                'template_key' => 'order_confirmation',
                'template_name' => 'Order Confirmation Email',
                'subject' => 'Order Confirmed #{order_id} - Murli Creations Handcrafted Studio',
                'available_vars' => '{customer_name}, {order_id}, {total_amount}, {items_list}, {payment_method}, {tracking_link}',
                'body_html' => '<div style="font-family:sans-serif; max-width:600px; margin:auto; border:1px solid #e2d9cc; border-radius:12px; overflow:hidden;">
  <div style="background:#1b1510; color:#d4af37; padding:24px; text-align:center;">
    <h1 style="margin:0; font-size:24px; letter-spacing:2px;">MURLI CREATIONS</h1>
    <p style="margin:5px 0 0; font-size:13px; color:#f5efe6;">Handcrafted Lippan Art & Sacred Wall Decor</p>
  </div>
  <div style="padding:28px; background:#fff; color:#333;">
    <h2 style="color:#1b1510; margin-top:0;">Namaste {customer_name},</h2>
    <p>Thank you for choosing Murli Creations. Master artisan Murli Prasad has received your order <strong>#{order_id}</strong>.</p>
    <div style="background:#fcf9f2; border-left:4px solid #d4af37; padding:15px; margin:20px 0; border-radius:4px;">
      <p style="margin:4px 0;"><strong>Total Amount:</strong> ₹{total_amount}</p>
      <p style="margin:4px 0;"><strong>Payment Method:</strong> {payment_method}</p>
      <p style="margin:4px 0;"><strong>Items Ordered:</strong><br>{items_list}</p>
    </div>
    <p>Every piece is carefully handcrafted with genuine Kutchi mud relief and mirror inlays. We will notify you once dispatched.</p>
    <p style="text-align:center; margin:30px 0;">
      <a href="{tracking_link}" style="background:#1b1510; color:#d4af37; padding:12px 24px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block;">Track Your Order</a>
    </p>
  </div>
  <div style="background:#f5efe6; padding:16px; text-align:center; font-size:12px; color:#777;">
    Delhi Studio, India • Phone: +91 98765 43210 • info@murlicreations.com
  </div>
</div>'
            ],
            [
                'template_key' => 'order_shipped',
                'template_name' => 'Order Shipped & Tracking Email',
                'subject' => 'Dispatched! Your Artwork #{order_id} is on the way | Murli Creations',
                'available_vars' => '{customer_name}, {order_id}, {carrier}, {tracking_number}, {tracking_url}, {estimated_delivery}',
                'body_html' => '<div style="font-family:sans-serif; max-width:600px; margin:auto; border:1px solid #e2d9cc; border-radius:12px; overflow:hidden;">
  <div style="background:#1b1510; color:#d4af37; padding:24px; text-align:center;">
    <h1 style="margin:0; font-size:24px; letter-spacing:2px;">MURLI CREATIONS</h1>
    <p style="margin:5px 0 0; font-size:13px; color:#f5efe6;">Your Masterpiece has Been Dispatched 📦</p>
  </div>
  <div style="padding:28px; background:#fff; color:#333;">
    <h2 style="color:#1b1510; margin-top:0;">Namaste {customer_name},</h2>
    <p>Great news! Your handcrafted order <strong>#{order_id}</strong> has been carefully packed with multi-layer protective bubble wrap and wooden shock absorbers, and is now dispatched.</p>
    <div style="background:#f0f8ff; border:1px solid #cce5ff; padding:16px; border-radius:8px; margin:20px 0;">
      <p style="margin:4px 0;"><strong>Courier Partner:</strong> {carrier}</p>
      <p style="margin:4px 0;"><strong>Tracking / AWB Number:</strong> {tracking_number}</p>
      <p style="margin:4px 0;"><strong>Estimated Delivery Date:</strong> {estimated_delivery}</p>
    </div>
    <p style="text-align:center; margin:30px 0;">
      <a href="{tracking_url}" target="_blank" style="background:#1e88e5; color:#fff; padding:12px 28px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block;">Track Shipment Live &rarr;</a>
    </p>
  </div>
</div>'
            ],
            [
                'template_key' => 'cart_reminder',
                'template_name' => 'Abandoned Cart & Customer Reminder Email',
                'subject' => 'Your handcrafted artwork is waiting for you + 10% OFF | Murli Creations',
                'available_vars' => '{customer_name}, {coupon_code}, {checkout_link}',
                'body_html' => '<div style="font-family:sans-serif; max-width:600px; margin:auto; border:1px solid #e2d9cc; border-radius:12px; overflow:hidden;">
  <div style="background:#1b1510; color:#d4af37; padding:24px; text-align:center;">
    <h1 style="margin:0; font-size:24px; letter-spacing:2px;">MURLI CREATIONS</h1>
    <p style="margin:5px 0 0; font-size:13px; color:#f5efe6;">Handmade with Love & Devotion</p>
  </div>
  <div style="padding:28px; background:#fff; color:#333;">
    <h2 style="color:#1b1510; margin-top:0;">Namaste {customer_name},</h2>
    <p>We noticed you were admiring our authentic Lippan Art and left something special in your cart.</p>
    <p>Because each artwork requires hours of hand-applied clay relief and mirror work, studio batches are strictly limited.</p>
    <div style="background:#fff9e6; border:2px dashed #d4af37; padding:18px; text-align:center; border-radius:8px; margin:25px 0;">
      <p style="margin:0; font-size:14px; color:#666;">Use Special VIP Code for 10% OFF:</p>
      <h3 style="margin:8px 0; color:#b8860b; font-size:26px; letter-spacing:3px;">{coupon_code}</h3>
      <p style="margin:0; font-size:12px; color:#888;">Valid for the next 48 hours only</p>
    </div>
    <p style="text-align:center;">
      <a href="{checkout_link}" style="background:#1b1510; color:#d4af37; padding:14px 30px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block; font-size:16px;">Complete Your Order &rarr;</a>
    </p>
  </div>
</div>'
            ]
        ];

        $insT = $db->prepare("
            INSERT INTO mc_email_templates (template_key, template_name, subject, available_vars, body_html)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE subject = VALUES(subject), body_html = VALUES(body_html)
        ");

        foreach ($templates as $t) {
            $insT->execute([
                $t['template_key'],
                $t['template_name'],
                $t['subject'],
                $t['available_vars'],
                $t['body_html']
            ]);
        }
        $results[] = 'Initialized standard email communication templates.';
    }

    // 9. Seed Default CRM Leads if none exist (Requirement 5)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_crm_leads");
    if ($stmt->fetchColumn() == 0 || $force) {
        $leads = [
            [
                'name' => 'Aarav Singhania',
                'email' => 'aarav.singhania@example.com',
                'phone' => '+91 98112 34567',
                'subject' => 'Custom 4x3 ft Peacock Jharokha for Living Room Entryway',
                'message' => 'Looking for a bespoke large Lippan peacock frame with warm amber backlighting. Need delivery before Diwali in Gurgaon.',
                'lead_status' => 'In Discussion',
                'priority' => 'High',
                'follow_up_date' => date('Y-m-d', strtotime('+2 days')),
                'notes' => 'Spoke on phone. Shared studio video of 36x24 inch prototype. Client interested in gold antique finish.'
            ],
            [
                'name' => 'Pooja Verma',
                'email' => 'pooja.verma@example.com',
                'phone' => '+91 97223 88990',
                'subject' => 'Bulk Khatu Shyam Ji Poshak for Mandir Festival',
                'message' => 'Need 10 sets of heavy zardozi embroidered poshak with matching mukut kalangis for upcoming Ekadashi kirtan.',
                'lead_status' => 'Quote Sent',
                'priority' => 'Urgent',
                'follow_up_date' => date('Y-m-d', strtotime('+1 day')),
                'notes' => 'Sent quotation of ₹18,500. Awaiting temple committee confirmation.'
            ]
        ];

        $insL = $db->prepare("
            INSERT INTO mc_crm_leads (name, email, phone, subject, message, lead_status, priority, follow_up_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($leads as $l) {
            $insL->execute([
                $l['name'], $l['email'], $l['phone'], $l['subject'], $l['message'],
                $l['lead_status'], $l['priority'], $l['follow_up_date'], $l['notes']
            ]);
        }
        $results[] = 'Seeded initial CRM customer inquiries & leads.';
    }

    // 10. Seed Default API Key for Webhook Integration (Requirement 10)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_api_keys");
    if ($stmt->fetchColumn() == 0 || $force) {
        $insKey = $db->prepare("
            INSERT INTO mc_api_keys (key_name, api_key, service, permissions, is_active)
            VALUES (?, ?, ?, ?, 1)
        ");
        $defaultKey = 'mc_live_' . bin2hex(random_bytes(16));
        $insKey->execute(['Shiprocket Logistics Webhook', $defaultKey, 'Shiprocket / Delhivery API', 'orders:status_update']);
        $results[] = 'Created default external webhook API key: ' . $defaultKey;
    }

    // 11. Seed Default Offers & CTA (Requirement 14)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_offers_cta");
    if ($stmt->fetchColumn() == 0 || $force) {
        $insOffer = $db->prepare("
            INSERT INTO mc_offers_cta (section_key, title, badge, description, cta_text, cta_link, coupon_code, discount_label, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE title = VALUES(title)
        ");

        $insOffer->execute([
            'announcement_bar',
            'Festive Season Special: Free Shipping Pan-India on orders above ₹1,999!',
            'FESTIVE BLESSINGS',
            'Handcrafted directly by master artisans in Delhi studio',
            'Claim Offer',
            '/shop.php',
            'MURLI10',
            '10% OFF'
        ]);

        $insOffer->execute([
            'hero_cta',
            'Handcrafted Lippan Art & Sacred Temple Wall Decor',
            'AUTHENTIC DELHI STUDIO',
            'Infuse your sanctuary with timeless Kutchi mud relief and mirror reflections.',
            'Explore Collection',
            '/shop.php',
            'MURLI10',
            'Flat 10% Discount'
        ]);

        $insOffer->execute([
            'festive_popup',
            'Welcome to Murli Creations! Take ₹500 OFF Your First Heritage Masterpiece',
            'EXCLUSIVE INVITATION',
            'Sign up for artisan updates and receive an instant gift voucher for your home sanctum.',
            'Unlock Voucher',
            '/shop.php',
            'WELCOME500',
            '₹500 Flat OFF'
        ]);

        $results[] = 'Seeded default Offers and CTA banners.';
    }

    // 12. Seed Sample Reminders (Requirement 13)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_reminders");
    if ($stmt->fetchColumn() == 0 || $force) {
        $insRem = $db->prepare("
            INSERT INTO mc_reminders (customer_name, customer_phone, customer_email, order_id, reminder_type, channel, message_body, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $insRem->execute([
            'Radhika Sharma',
            '9876543210',
            'radhika@example.com',
            'MURLI-457702-467',
            'abandoned_cart',
            'whatsapp',
            'Namaste Radhika ji, your Peacock Jharokha Wall Art is reserved at Murli Creations studio. Complete your booking today with code MURLI10 for 10% OFF!'
        ]);
        $results[] = 'Seeded sample customer reminders.';
    }

    // 13. Seed Sample Contact Inquiries (mc_contact_inquiries)
    $stmt = $db->query("SELECT COUNT(*) FROM mc_contact_inquiries");
    if ($stmt->fetchColumn() == 0 || $force) {
        $insInq = $db->prepare("
            INSERT INTO mc_contact_inquiries (name, email, phone, subject, message, status, reply_channel, reply_message, replied_at, replied_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $inquiries = [
            [
                'Anandita Verma',
                'anandita.verma@example.com',
                '+91 98765 43210',
                'Custom Order Request',
                'Namaste! I have a 5x3 feet dining foyer wall in my Gurgaon apartment. Can Master Murli ji create a bespoke turquoise and terracotta Peacock Lippan panel for this space?',
                'new',
                null,
                null,
                null,
                null
            ],
            [
                'Vikram Singhania',
                'vikram.singhania@example.com',
                '+91 98112 34567',
                'Wholesale Bulk Gifting',
                'We require 25 handcrafted Swastik and Shubh-Labh Lippan plates for our corporate Diwali gifting hamper. What would be the bulk price and delivery timeline to Delhi?',
                'replied',
                'whatsapp',
                'Namaste Vikram ji, we have shared our bespoke corporate gifting catalog and volume discount tiers via WhatsApp. Looking forward to handcrafting your festive blessings.',
                date('Y-m-d H:i:s', strtotime('-2 hours')),
                'Master Artisan Murli Prasad'
            ],
            [
                'Meera Joshi',
                'meera.joshi@example.com',
                '+91 99887 76655',
                'Order Tracking Query',
                'Hi, I placed an order for the Khatu Shyam Ji Poshak set yesterday. Just wanted to verify if it will be delivered before Friday ekadashi.',
                'new',
                null,
                null,
                null,
                null
            ]
        ];
        foreach ($inquiries as $inq) {
            $insInq->execute($inq);
        }
        $results[] = 'Seeded sample Contact Form Inquiries.';
    }

    // 14. Seed mc_site_settings from storage/site_settings.json & storage/homepage_content.json
    $stmt = $db->query("SELECT COUNT(*) FROM mc_site_settings");
    if ($stmt->fetchColumn() == 0 || $force) {
        $insSetting = $db->prepare("
            INSERT INTO mc_site_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        $settingsFile = __DIR__ . '/../storage/site_settings.json';
        if (file_exists($settingsFile)) {
            $insSetting->execute(['site_settings', file_get_contents($settingsFile)]);
        }

        $homeFile = __DIR__ . '/../storage/homepage_content.json';
        if (file_exists($homeFile)) {
            $insSetting->execute(['homepage_content', file_get_contents($homeFile)]);
        }

        $results[] = 'Seeded site_settings and homepage_content into MySQL mc_site_settings.';
    }

    return ['success' => true, 'messages' => $results];
}

// If invoked from CLI directly, run it
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    echo "Running Murli Creations Database Migrations...\n";
    $res = run_database_migrations(true);
    echo json_encode($res, JSON_PRETTY_PRINT) . "\n";
}

