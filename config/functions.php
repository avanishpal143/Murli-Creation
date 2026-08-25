<?php
// Core System Functions & Data Persistence for Murli Creations

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/../includes/icons.php';

define('STORAGE_DIR', __DIR__ . '/../storage');

function init_storage() {
    if (!file_exists(STORAGE_DIR)) {
        mkdir(STORAGE_DIR, 0777, true);
    }

    $products_file = STORAGE_DIR . '/products.json';
    if (!file_exists($products_file)) {
        $initial_products = get_initial_products();
        file_put_contents($products_file, json_encode($initial_products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $reviews_file = STORAGE_DIR . '/reviews.json';
    if (!file_exists($reviews_file)) {
        $initial_reviews = get_initial_reviews();
        file_put_contents($reviews_file, json_encode($initial_reviews, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $orders_file = STORAGE_DIR . '/orders.json';
    if (!file_exists($orders_file)) {
        file_put_contents($orders_file, json_encode([], JSON_PRETTY_PRINT));
    }
}

// Ensure storage files exist
init_storage();

function get_all_products() {
    $file = STORAGE_DIR . '/products.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data) && count($data) > 0) {
            return $data;
        }
    }
    return get_initial_products();
}

function get_category_tree() {
    return [
        'Lippan Art' => [
            'name' => 'Lippan Art Collections',
            'slug' => 'Lippan Art',
            'icon' => 'sparkles',
            'badge' => 'Signature Heritage',
            'description' => 'Traditional mud clay relief & mirror artwork handcrafted by Murli Prasad',
            'subcategories' => [
                'Lippan Wall Art' => 'Large statement pieces & traditional patterns',
                'Lippan Mirrors' => 'Mirror-work panels & decorative round mirrors',
                'Lippan Name Plates' => 'Customised home & office name plates',
                'Lippan Spiritual Art' => 'Om, Shree, Swastik, Radha-Krishna & Khatu Shyam',
                'Lippan Mandala Art' => 'Geometric & sacred symmetrical designs',
                'Lippan Peacock Art' => 'Auspicious peacock-themed decorative pieces 🦚',
                'Lippan Floral Art' => 'Lotus, blossoms & botanical motifs 🌸',
                'Lippan Traditional Kutch' => 'Authentic Kutchi clay motifs & relief patterns',
                'Lippan Gift Collection' => 'Handmade gift pieces for weddings & housewarmings'
            ]
        ],
        'Shyam Ji Decor' => [
            'name' => 'Shyam Ji (Khatu Baba Collection)',
            'slug' => 'Shyam Ji Decor',
            'icon' => 'award',
            'badge' => 'Devotional Bhav',
            'description' => 'Complete sacred shringar, dresses, and adornments for Khatu Shyam Baba',
            'subcategories' => [
                'Shyam Baba Dresses & Poshak' => 'Designer heavy embroidered festive poshak 👑',
                'Shyam Baba Shringar & Flowers' => 'Real & artificial flower shringar garlands ✨',
                'Shyam Baba Singhasan & Chowki' => 'Decorative wooden & metal temple chowkis',
                'Shyam Baba Jewellery & Mukut' => 'Mukut, mor pankh, morchadi, haar & kundal 💎',
                'Shyam Baba Bansuri & Flutes' => 'Embellished decorative flutes & accessories 🦚'
            ]
        ],
        'Vastu Species' => [
            'name' => 'Vastu Species & Auspicious Art',
            'slug' => 'Vastu Species',
            'icon' => 'sun',
            'badge' => 'Positive Energy',
            'description' => 'Artworks designed according to Vedic Vastu principles for prosperity',
            'subcategories' => [
                'Vastu Peacock (North-East)' => 'North/East direction vitality & evil eye protection',
                'Tree of Life (Kalpavriksha)' => 'Growth, grounding & continuous prosperity',
                'Sacred Surya Yantra' => 'Radiant solar energy, health & leadership',
                'Kamdhenu & Gau Mata' => 'Motherly blessings, peace & abundance',
                'Sacred Geometric Mandala' => 'Centering focus, balance & harmony'
            ]
        ],
        'Diwali & Festive Decor' => [
            'name' => 'Diwali & Festive Decor',
            'slug' => 'Diwali & Festive Decor',
            'icon' => 'tag',
            'badge' => 'Festive Glow',
            'description' => 'Handmade festive embellishments, diyas, torans and door blessings',
            'subcategories' => [
                'Decorative Diyas & Sets' => 'Hand-painted terracotta & mirror diya sets',
                'Diwali Toran & Door Décor' => 'Auspicious bandarwal & door entrance hangings',
                'Rangoli & Acrylic Cutouts' => 'Reusable portable handcrafted rangoli sets',
                'Diwali Thali & Mandir Decor' => 'Aarti thalis & pooja room adornments',
                'Customised Shubh-Labh' => 'Handmade mirror & acrylic door blessings ✨'
            ]
        ],
        'Customised Creations' => [
            'name' => 'Customised & Bespoke Art',
            'slug' => 'Customised Creations',
            'icon' => 'send',
            'badge' => 'Personalised',
            'description' => 'Personalised names, dimensions, and interior colour palettes',
            'subcategories' => [
                'Personalised Name & Door Plates' => 'Bespoke family nameplates with mirror inlays',
                'Custom Sized Wall Panels' => 'Tailored dimensions from 12" up to 6 feet',
                'Bespoke Color Palette Art' => 'Interior designer & palette matched pieces'
            ]
        ],
        'Jharokhas & Frames' => [
            'name' => 'Royal Jharokhas & Frames',
            'slug' => 'Jharokhas & Frames',
            'icon' => 'shield-check',
            'badge' => 'Heritage Architecture',
            'description' => 'Solid wood Rajasthani & Haryanvi hand-chiselled window frames',
            'subcategories' => [
                'Carved Wooden Jharokha' => 'Antiqued solid wood architectural mirrors',
                'Distressed Gold Jharokha' => 'Royal distressed gold leaf finish frames'
            ]
        ]
    ];
}

function get_featured_products($limit = 8) {
    $products = get_all_products();
    $featured = array_filter($products, function($p) {
        return !empty($p['featured']);
    });
    if (empty($featured)) {
        $featured = $products;
    }
    return array_slice(array_values($featured), 0, $limit);
}

function render_product_card($product) {
    include __DIR__ . '/../includes/product-card.php';
}

function save_all_products($products) {
    $file = STORAGE_DIR . '/products.json';
    return file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_product_by_slug($slug) {
    $products = get_all_products();
    foreach ($products as $p) {
        if ($p['slug'] === $slug) {
            return $p;
        }
    }
    return null;
}

function get_product_by_id($id) {
    $products = get_all_products();
    foreach ($products as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

function get_all_reviews($productId = null) {
    $file = STORAGE_DIR . '/reviews.json';
    if (file_exists($file)) {
        $reviews = json_decode(file_get_contents($file), true) ?: [];
    } else {
        $reviews = get_initial_reviews();
    }

    if ($productId !== null) {
        return array_values(array_filter($reviews, function($r) use ($productId) {
            return $r['productId'] === $productId;
        }));
    }
    return $reviews;
}

function save_all_reviews($reviews) {
    $file = STORAGE_DIR . '/reviews.json';
    return file_put_contents($file, json_encode($reviews, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_product_reviews($productId) {
    return get_all_reviews($productId);
}

function get_all_orders() {
    $file = STORAGE_DIR . '/orders.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

function get_order_by_id($orderId) {
    $orders = get_all_orders();
    foreach ($orders as $order) {
        if ($order['id'] === $orderId) {
            return $order;
        }
    }
    return null;
}

function save_all_orders($orders) {
    $file = STORAGE_DIR . '/orders.json';
    return file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function format_inr($number) {
    return number_format((float)$number, 0, '.', ',');
}

// Cart Management via PHP Session
function &get_session_cart() {
    if (!isset($_SESSION['mc_cart']) || !is_array($_SESSION['mc_cart'])) {
        $_SESSION['mc_cart'] = [];
    }
    return $_SESSION['mc_cart'];
}

function add_to_session_cart($product, $quantity = 1) {
    $cart = &get_session_cart();
    $found = false;

    foreach ($cart as &$item) {
        if ($item['product']['id'] === $product['id']) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $cart[] = [
            'product' => $product,
            'quantity' => (int)$quantity
        ];
    }
    return $cart;
}

function update_session_cart_quantity($productId, $quantity) {
    $cart = &get_session_cart();
    if ($quantity <= 0) {
        remove_from_session_cart($productId);
        return get_session_cart();
    }

    foreach ($cart as &$item) {
        if ($item['product']['id'] === $productId) {
            $item['quantity'] = (int)$quantity;
            break;
        }
    }
    return $cart;
}

function remove_from_session_cart($productId) {
    $cart = &get_session_cart();
    $cart = array_values(array_filter($cart, function($item) use ($productId) {
        return $item['product']['id'] !== $productId;
    }));
    $_SESSION['mc_cart'] = $cart;
    return $cart;
}

function clear_session_cart() {
    $_SESSION['mc_cart'] = [];
    return [];
}

function get_cart_count() {
    $cart = get_session_cart();
    $count = 0;
    foreach ($cart as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}

function get_cart_subtotal() {
    $cart = get_session_cart();
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += (float)$item['product']['price'] * (int)$item['quantity'];
    }
    return $subtotal;
}

// Add Review & Update Rating
function submit_product_review($productId, $userName, $rating, $comment) {
    $reviews = get_all_reviews();
    $newReview = [
        'id' => 'rev-' . round(microtime(true) * 1000),
        'productId' => $productId,
        'userName' => htmlspecialchars(trim($userName)),
        'rating' => (int)$rating,
        'comment' => htmlspecialchars(trim($comment)),
        'date' => date('d M Y')
    ];
    array_unshift($reviews, $newReview);
    save_all_reviews($reviews);

    // Update Product average rating
    $products = get_all_products();
    foreach ($products as &$p) {
        if ($p['id'] === $productId) {
            $currentReviewsCount = isset($p['reviewsCount']) ? (int)$p['reviewsCount'] : 0;
            $currentRating = isset($p['rating']) ? (float)$p['rating'] : 4.8;
            $newCount = $currentReviewsCount + 1;
            $newRating = round((($currentRating * $currentReviewsCount) + $rating) / $newCount, 1);
            $p['reviewsCount'] = $newCount;
            $p['rating'] = $newRating;
            break;
        }
    }
    save_all_products($products);
    return $newReview;
}

// Place Order
function place_new_order($customer, $paymentMethod, $paymentStatus, $cartItems = null) {
    if ($cartItems === null) {
        $cartItems = get_session_cart();
    }

    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += (float)$item['product']['price'] * (int)$item['quantity'];
    }

    $orderId = 'MURLI-' . substr((string)time(), -6) . '-' . rand(100, 999);
    $order = [
        'id' => $orderId,
        'date' => date('d M Y, h:i A'),
        'customer' => $customer,
        'items' => $cartItems,
        'subtotal' => $subtotal,
        'paymentMethod' => $paymentMethod,
        'paymentStatus' => $paymentStatus,
        'orderStatus' => 'Pending'
    ];

    // Decrement stock levels
    $products = get_all_products();
    foreach ($products as &$p) {
        foreach ($cartItems as $item) {
            if ($item['product']['id'] === $p['id']) {
                $p['stock'] = max(0, $p['stock'] - (int)$item['quantity']);
            }
        }
    }
    save_all_products($products);

    $orders = get_all_orders();
    array_unshift($orders, $order);
    save_all_orders($orders);

    clear_session_cart();
    return $order;
}

// Update Order Status
function update_order_status_by_id($orderId, $status) {
    $orders = get_all_orders();
    $products = get_all_products();
    $updated = false;

    foreach ($orders as &$o) {
        if ($o['id'] === $orderId) {
            $prevStatus = $o['orderStatus'];
            
            // Stock restoration on cancellation
            if ($status === 'Cancelled' && $prevStatus !== 'Cancelled') {
                foreach ($products as &$p) {
                    foreach ($o['items'] as $item) {
                        if ($item['product']['id'] === $p['id']) {
                            $p['stock'] += (int)$item['quantity'];
                        }
                    }
                }
                $o['paymentStatus'] = 'Failed';
            }
            
            // Deduct stock if restored from cancellation
            if ($prevStatus === 'Cancelled' && $status !== 'Cancelled') {
                foreach ($products as &$p) {
                    foreach ($o['items'] as $item) {
                        if ($item['product']['id'] === $p['id']) {
                            $p['stock'] = max(0, $p['stock'] - (int)$item['quantity']);
                        }
                    }
                }
                $o['paymentStatus'] = ($o['paymentMethod'] === 'Razorpay') ? 'Paid' : 'Pending';
            }

            $o['orderStatus'] = $status;
            $updated = true;
            break;
        }
    }

    if ($updated) {
        save_all_orders($orders);
        save_all_products($products);
    }
    return $updated;
}

// =======================================================
// DYNAMIC SITE SETTINGS & CMS PERSISTENCE
// =======================================================

function get_site_settings() {
    $file = STORAGE_DIR . '/site_settings.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [
        'announcement' => [
            'enabled' => true,
            'text' => 'Festive Offer: Use code {coupon} for 10% OFF + Free Insured Shipping!',
            'coupon_code' => 'MURLI10',
            'badge' => '✨ Special Celebration ✨',
            'link' => '/shop.php'
        ],
        'studio' => [
            'name' => 'Murli Creations',
            'tagline' => 'Handcrafted Lippan Art & Sacred Devotional Heritage',
            'founder' => 'Master Craftsman Murli Prasad',
            'location' => 'Rohtak, Haryana, India',
            'address' => 'Opp. Civil Lines, Rohtak, Haryana 124001, India',
            'phone' => '+91 99999 99999',
            'whatsapp' => '+91 99999 99999',
            'email' => 'contact@murlicreations.com',
            'hours' => 'Mon - Sat: 10:00 AM - 7:30 PM (IST)'
        ],
        'shipping' => [
            'free_threshold' => 3000,
            'standard_charge' => 149,
            'transit_safe_note' => '5-layer wooden crate & shock-absorbent cushioning',
            'dispatch_time' => 'Dispatched in 2-3 business days'
        ],
        'social' => [
            'instagram' => 'https://instagram.com/murlicreations',
            'whatsapp' => 'https://wa.me/919999999999',
            'youtube' => 'https://youtube.com'
        ],
        'footer' => [
            'about_text' => 'Sacred mud & mirror art handcrafted by Murli Prasad. Bringing positive energy, devotion, and traditional Indian grandeur to homes across the globe.',
            'copyright' => '© 2026 Murli Creations. Handcrafted in India with Devotion. All Rights Reserved.'
        ]
    ];
}

function save_site_settings($settings) {
    $file = STORAGE_DIR . '/site_settings.json';
    return file_put_contents($file, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

function get_homepage_content() {
    $file = STORAGE_DIR . '/homepage_content.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [
        'hero' => [
            'badge' => 'Handcrafted in Rohtak, Haryana',
            'title_main' => 'Authentic Handcrafted Lippan Art for',
            'title_highlight' => 'Soulful Living Spaces',
            'description' => 'Bring auspicious energy and timeless Indian grandeur into your home. Master artisan Murli Prasad blends sacred mud clay, precision mirrors, and vibrant devotions into heirloom-grade wall masterpieces.',
            'cta_primary_text' => 'Explore Catalog',
            'cta_primary_link' => '/shop.php',
            'cta_secondary_text' => 'About Our Studio',
            'cta_secondary_link' => '/about.php',
            'image' => '/img-prodcut/IMG_4041.png'
        ],
        'seasonal_vibes' => [
            'badge' => 'Curated Seasonal Collections',
            'title' => 'Seasonal Vibes & Festive Auspicious Curations',
            'subtitle' => 'Curated themes matching Indian celebrations, festive light reflections, and sanctuary harmony.',
            'cards' => [
                [
                    'id' => 'vibe-1',
                    'tag' => 'Festive Radiance',
                    'title' => 'Festive Door & Mandir Decor',
                    'desc' => 'Auspicious handcrafted Shubh-Labh and Swastik wall frames designed to welcome festive blessings.',
                    'link' => '/shop.php?category=Diwali+%26+Festive+Decor',
                    'link_text' => 'Explore Festive Decor',
                    'image' => '/img-prodcut/IMG_3426.png'
                ],
                [
                    'id' => 'vibe-2',
                    'tag' => 'Pooja Mandir',
                    'title' => 'Divine Sanctum & Spiritual Peace',
                    'desc' => 'Handcrafted Khatu Shyam Baba and sacred deity reliefs sanctified for devotional prayer sanctuaries.',
                    'link' => '/shop.php?category=Shyam+Ji+Decor',
                    'link_text' => 'Explore Spiritual Collection',
                    'image' => '/img-prodcut/IMG_4158.png'
                ],
                [
                    'id' => 'vibe-3',
                    'tag' => 'Contemporary Living',
                    'title' => 'Geometric Sacred Mandalas',
                    'desc' => 'Symmetrical sacred geometry and clay reliefs for living rooms, dining focal points, and office suites.',
                    'link' => '/shop.php?category=Lippan+Art',
                    'link_text' => 'Explore Mandalas',
                    'image' => '/img-prodcut/IMG_3519.png'
                ]
            ]
        ],
        'festive_offer' => [
            'enabled' => true,
            'badge' => 'Special Festive Celebration',
            'title' => 'Claim 10% Off On Your First Handcrafted Artwork',
            'description' => 'Every creation comes with certified authenticity, transit-insured bespoke packaging, and authentic brass hooks ready to grace your walls.',
            'coupon_code' => 'MURLI10',
            'discount_label' => '10% FLAT OFF',
            'button_text' => 'Shop Auspicious Masterpieces',
            'button_link' => '/shop.php'
        ],
        'story' => [
            'badge' => 'Our Story • A Daughter\'s Tribute',
            'title' => 'Clay, Mirrors & Devotion: The Legacy of Murli Prasad',
            'quote' => '“Art is not just decoration. When crafted with pure clay, sacred mantras, and patient hands, it turns a house into an auspicious sanctuary.”',
            'author' => 'Master Craftsman Murli Prasad',
            'highlights' => [
                ['title' => 'Rooted in Tradition', 'desc' => 'Reviving centuries-old Kutchi mud-and-mirror craftsmanship adapted for modern luxury architecture.'],
                ['title' => 'Zero Mass Production', 'desc' => 'Each piece requires 30-70 hours of painstaking clay rolling, mirror positioning, and mineral painting.'],
                ['title' => 'Devotional Sanctity', 'desc' => 'Crafted with reverence to bring peace, vastu harmony, and spiritual light into every home.']
            ],
            'image' => '/img-prodcut/IMG_3495.png'
        ],
        'custom_commission' => [
            'badge' => 'Bespoke Artistry',
            'title' => 'Commission a Custom Masterpiece',
            'description' => 'Looking for specific dimensions, auspicious color palettes, or deity themes for your pooja mandir or luxury interior? Our master artisan collaborates directly with homeowners and interior designers.',
            'cta_text' => 'Chat on WhatsApp for Custom Orders',
            'cta_link' => 'https://wa.me/919999999999?text=Hello%20Murli%20Creations,%20I\'d%20like%20to%20inquire%20about%20a%20custom%20artwork.'
        ],
        'faqs' => [
            ['question' => 'How are the delicate mirrors and clay reliefs protected during shipping?', 'answer' => 'Every artwork is encased in shock-absorbent custom foam, wrapped in moisture-resistant film, and secured within a 5-layer heavy-duty wooden reinforcement crate to ensure 100% transit safety across India.'],
            ['question' => 'Are these authentic handmade Lippan artworks?', 'answer' => 'Yes, each artwork is 100% hand-sculpted by Master Craftsman Murli Prasad in Rohtak, Haryana, using traditional clay relief paste, genuine glass mirrors, and premium wooden backings.'],
            ['question' => 'Can I request custom sizes or specific deity designs?', 'answer' => 'Absolutely! We create customized pieces tailored to your exact wall dimensions, interior color schemes, and pooja mandir requirements. Simply reach out via WhatsApp or our Contact page.'],
            ['question' => 'What is the expected delivery timeframe?', 'answer' => 'In-stock pieces are dispatched within 2 business days and delivered within 4-7 business days across India. Custom commissions typically take 10-15 business days depending on design complexity.']
        ]
    ];
}

function save_homepage_content($content) {
    $file = STORAGE_DIR . '/homepage_content.json';
    return file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
}

// =======================================================
// DYNAMIC COUPONS PERSISTENCE & VALIDATION
// =======================================================

function get_all_coupons() {
    $file = STORAGE_DIR . '/coupons.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [
        ['code' => 'MURLI10', 'discountPercent' => 10, 'minOrder' => 1500, 'description' => '10% Festive Inaugural Discount on orders above ₹1,500', 'active' => true],
        ['code' => 'FESTIVE15', 'discountPercent' => 15, 'minOrder' => 3000, 'description' => '15% Auspicious Festival Discount on orders above ₹3,000', 'active' => true],
        ['code' => 'DEVOTION20', 'discountPercent' => 20, 'minOrder' => 5000, 'description' => '20% Grand Heritage Discount on orders above ₹5,000', 'active' => true]
    ];
}

function save_all_coupons($coupons) {
    $file = STORAGE_DIR . '/coupons.json';
    return file_put_contents($file, json_encode($coupons, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function validate_coupon($code, $subtotal) {
    $code = strtoupper(trim($code));
    $coupons = get_all_coupons();
    
    foreach ($coupons as $c) {
        if (strtoupper($c['code']) === $code && !empty($c['active'])) {
            if ($subtotal < ($c['minOrder'] ?? 0)) {
                return [
                    'valid' => false,
                    'message' => 'Coupon ' . $c['code'] . ' requires minimum cart value of ₹' . number_format($c['minOrder'])
                ];
            }
            $discount = round(($subtotal * (float)$c['discountPercent']) / 100);
            return [
                'valid' => true,
                'code' => $c['code'],
                'discountPercent' => $c['discountPercent'],
                'discount' => $discount,
                'message' => $c['discountPercent'] . '% discount applied!'
            ];
        }
    }
    
    return [
        'valid' => false,
        'message' => 'Invalid or expired coupon code.'
    ];
}

// Available Product Images in directory
function get_available_product_images() {
    $imgDir = __DIR__ . '/../img-prodcut';
    $images = [];
    if (is_dir($imgDir)) {
        $files = scandir($imgDir);
        foreach ($files as $f) {
            if (in_array(pathinfo($f, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'webp'])) {
                $images[] = '/img-prodcut/' . $f;
            }
        }
    }
    return $images;
}
