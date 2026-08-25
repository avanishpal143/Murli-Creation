<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'murli@2026') {
        $_SESSION['mc_admin_logged_in'] = true;
        echo json_encode(['success' => true, 'message' => 'Logged in successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect Username or Password.']);
    }
    exit;
}

if ($action === 'logout') {
    $_SESSION['mc_admin_logged_in'] = false;
    unset($_SESSION['mc_admin_logged_in']);
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit;
}

// Ensure admin authentication for mutation actions below
if (empty($_SESSION['mc_admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($action === 'add_product') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $category = $_POST['category'] ?? 'Lippan Art';
    $price = (float)($_POST['price'] ?? 0);
    $mrp = !empty($_POST['mrp']) ? (float)$_POST['mrp'] : round($price * 1.3);
    $stock = (int)($_POST['stock'] ?? 0);
    $size = $_POST['size'] ?? '18 x 18 inches';
    $material = $_POST['material'] ?? 'Clay Relief, MDF Board';
    $origin = $_POST['origin'] ?? 'Rohtak, Haryana, India';
    $artist = $_POST['artist'] ?? 'Master Craftsman Murli Prasad';
    $rating = (float)($_POST['rating'] ?? 4.8);
    $reviewsCount = (int)($_POST['reviewsCount'] ?? 15);
    $video = $_POST['video'] ?? '';
    $folklore = $_POST['folklore'] ?? '';
    $description = $_POST['description'] ?? '';
    
    $imagesRaw = $_POST['images'] ?? '';
    $images = array_values(array_filter(array_map('trim', explode(',', $imagesRaw))));
    if (empty($images)) {
        $images = ['/img-prodcut/IMG_4041.png'];
    }

    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
    $id = 'prod-' . round(microtime(true) * 1000);

    $newProduct = [
        'id' => $id,
        'code' => $code,
        'slug' => $slug,
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'mrp' => $mrp,
        'rating' => $rating,
        'reviewsCount' => $reviewsCount,
        'artist' => $artist,
        'folklore' => $folklore,
        'images' => $images,
        'video' => $video,
        'description' => $description,
        'size' => $size,
        'material' => $material,
        'origin' => $origin,
        'stock' => $stock,
        'featured' => false
    ];

    $products = get_all_products();
    array_unshift($products, $newProduct);
    save_all_products($products);

    echo json_encode(['success' => true, 'message' => "Successfully added product \"{$name}\"", 'product' => $newProduct]);
    exit;
}

if ($action === 'edit_product') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $category = $_POST['category'] ?? 'Lippan Art';
    $price = (float)($_POST['price'] ?? 0);
    $mrp = (float)($_POST['mrp'] ?? round($price * 1.3));
    $stock = (int)($_POST['stock'] ?? 0);
    $size = $_POST['size'] ?? '';
    $material = $_POST['material'] ?? '';
    $origin = $_POST['origin'] ?? '';
    $artist = $_POST['artist'] ?? '';
    $rating = (float)($_POST['rating'] ?? 4.8);
    $reviewsCount = (int)($_POST['reviewsCount'] ?? 15);
    $video = $_POST['video'] ?? '';
    $folklore = $_POST['folklore'] ?? '';
    $description = $_POST['description'] ?? '';

    $imagesRaw = $_POST['images'] ?? '';
    $images = array_values(array_filter(array_map('trim', explode(',', $imagesRaw))));
    if (empty($images)) {
        $images = ['/img-prodcut/IMG_4041.png'];
    }

    $products = get_all_products();
    $found = false;

    foreach ($products as &$p) {
        if ($p['id'] === $id) {
            $p['name'] = $name;
            if (!empty($code)) {
                $p['code'] = $code;
            }
            $p['category'] = $category;
            $p['price'] = $price;
            $p['mrp'] = $mrp;
            $p['stock'] = $stock;
            $p['size'] = $size;
            $p['material'] = $material;
            $p['origin'] = $origin;
            $p['artist'] = $artist;
            $p['rating'] = $rating;
            $p['reviewsCount'] = $reviewsCount;
            $p['video'] = $video;
            $p['folklore'] = $folklore;
            $p['description'] = $description;
            $p['images'] = $images;
            $found = true;
            break;
        }
    }

    if ($found) {
        save_all_products($products);
        echo json_encode(['success' => true, 'message' => "Successfully updated product \"{$name}\""]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
    exit;
}

if ($action === 'delete_product') {
    $productId = $_POST['productId'] ?? '';
    $products = get_all_products();
    $initialCount = count($products);

    $products = array_values(array_filter($products, function($p) use ($productId) {
        return $p['id'] !== $productId;
    }));

    if (count($products) < $initialCount) {
        save_all_products($products);
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
    exit;
}

// -------------------------------------------------------
// SAVE SITE SETTINGS (Announcement, Studio, Shipping, etc.)
// -------------------------------------------------------
if ($action === 'save_settings') {
    $settings = get_site_settings();

    // Announcement Banner
    $settings['announcement']['enabled'] = isset($_POST['announcement_enabled']) && ($_POST['announcement_enabled'] === '1' || $_POST['announcement_enabled'] === 'true' || $_POST['announcement_enabled'] === 'on');
    if (isset($_POST['announcement_text'])) $settings['announcement']['text'] = trim($_POST['announcement_text']);
    if (isset($_POST['announcement_coupon'])) $settings['announcement']['coupon_code'] = strtoupper(trim($_POST['announcement_coupon']));
    if (isset($_POST['announcement_badge'])) $settings['announcement']['badge'] = trim($_POST['announcement_badge']);
    if (isset($_POST['announcement_link'])) $settings['announcement']['link'] = trim($_POST['announcement_link']);

    // Studio Info
    if (isset($_POST['studio_name'])) $settings['studio']['name'] = trim($_POST['studio_name']);
    if (isset($_POST['studio_tagline'])) $settings['studio']['tagline'] = trim($_POST['studio_tagline']);
    if (isset($_POST['studio_founder'])) $settings['studio']['founder'] = trim($_POST['studio_founder']);
    if (isset($_POST['studio_address'])) $settings['studio']['address'] = trim($_POST['studio_address']);
    if (isset($_POST['studio_phone'])) $settings['studio']['phone'] = trim($_POST['studio_phone']);
    if (isset($_POST['studio_whatsapp'])) $settings['studio']['whatsapp'] = trim($_POST['studio_whatsapp']);
    if (isset($_POST['studio_email'])) $settings['studio']['email'] = trim($_POST['studio_email']);
    if (isset($_POST['studio_hours'])) $settings['studio']['hours'] = trim($_POST['studio_hours']);

    // Shipping & Delivery
    if (isset($_POST['shipping_free_threshold'])) $settings['shipping']['free_threshold'] = (float)$_POST['shipping_free_threshold'];
    if (isset($_POST['shipping_standard_charge'])) $settings['shipping']['standard_charge'] = (float)$_POST['shipping_standard_charge'];
    if (isset($_POST['shipping_dispatch_time'])) $settings['shipping']['dispatch_time'] = trim($_POST['shipping_dispatch_time']);

    // Social Links
    if (isset($_POST['social_instagram'])) $settings['social']['instagram'] = trim($_POST['social_instagram']);
    if (isset($_POST['social_whatsapp'])) $settings['social']['whatsapp'] = trim($_POST['social_whatsapp']);
    if (isset($_POST['social_youtube'])) $settings['social']['youtube'] = trim($_POST['social_youtube']);

    // Footer Info
    if (isset($_POST['footer_about'])) $settings['footer']['about_text'] = trim($_POST['footer_about']);
    if (isset($_POST['footer_copyright'])) $settings['footer']['copyright'] = trim($_POST['footer_copyright']);

    $saved = save_site_settings($settings);
    echo json_encode(['success' => $saved, 'message' => $saved ? 'Site settings saved successfully!' : 'Failed to save settings']);
    exit;
}

// -------------------------------------------------------
// SAVE HOMEPAGE CMS CONTENT
// -------------------------------------------------------
if ($action === 'save_homepage_content') {
    $content = get_homepage_content();

    // Section 1: Hero
    if (isset($_POST['hero_badge'])) $content['hero']['badge'] = trim($_POST['hero_badge']);
    if (isset($_POST['hero_title_main'])) $content['hero']['title_main'] = trim($_POST['hero_title_main']);
    if (isset($_POST['hero_title_highlight'])) $content['hero']['title_highlight'] = trim($_POST['hero_title_highlight']);
    if (isset($_POST['hero_description'])) $content['hero']['description'] = trim($_POST['hero_description']);
    if (isset($_POST['hero_cta_primary_text'])) $content['hero']['cta_primary_text'] = trim($_POST['hero_cta_primary_text']);
    if (isset($_POST['hero_cta_primary_link'])) $content['hero']['cta_primary_link'] = trim($_POST['hero_cta_primary_link']);
    if (isset($_POST['hero_image'])) $content['hero']['image'] = trim($_POST['hero_image']);

    // Section 1.5: Milestone Stats Numbers
    for ($i = 1; $i <= 3; $i++) {
        $idx = $i - 1;
        if (!isset($content['stats'][$idx])) {
            $content['stats'][$idx] = [];
        }
        if (isset($_POST["stat{$i}_count"])) $content['stats'][$idx]['count'] = trim($_POST["stat{$i}_count"]);
        if (isset($_POST["stat{$i}_suffix"])) $content['stats'][$idx]['suffix'] = trim($_POST["stat{$i}_suffix"]);
        if (isset($_POST["stat{$i}_decimals"])) $content['stats'][$idx]['decimals'] = trim($_POST["stat{$i}_decimals"]);
        if (isset($_POST["stat{$i}_label"])) $content['stats'][$idx]['label'] = trim($_POST["stat{$i}_label"]);
        if (isset($_POST["stat{$i}_desc"])) $content['stats'][$idx]['desc'] = trim($_POST["stat{$i}_desc"]);
    }

    // Section 2: Seasonal Vibes Curations Header & 3 Cards
    if (isset($_POST['seasonal_badge'])) $content['seasonal_vibes']['badge'] = trim($_POST['seasonal_badge']);
    if (isset($_POST['seasonal_title'])) $content['seasonal_vibes']['title'] = trim($_POST['seasonal_title']);
    if (isset($_POST['seasonal_subtitle'])) $content['seasonal_vibes']['subtitle'] = trim($_POST['seasonal_subtitle']);

    for ($i = 1; $i <= 3; $i++) {
        $idx = $i - 1;
        if (!isset($content['seasonal_vibes']['cards'][$idx])) {
            $content['seasonal_vibes']['cards'][$idx] = ['id' => "vibe-{$i}"];
        }
        if (isset($_POST["card{$i}_tag"])) $content['seasonal_vibes']['cards'][$idx]['tag'] = trim($_POST["card{$i}_tag"]);
        if (isset($_POST["card{$i}_title"])) $content['seasonal_vibes']['cards'][$idx]['title'] = trim($_POST["card{$i}_title"]);
        if (isset($_POST["card{$i}_desc"])) $content['seasonal_vibes']['cards'][$idx]['desc'] = trim($_POST["card{$i}_desc"]);
        if (isset($_POST["card{$i}_link"])) $content['seasonal_vibes']['cards'][$idx]['link'] = trim($_POST["card{$i}_link"]);
        if (isset($_POST["card{$i}_link_text"])) $content['seasonal_vibes']['cards'][$idx]['link_text'] = trim($_POST["card{$i}_link_text"]);
        if (isset($_POST["card{$i}_image"])) $content['seasonal_vibes']['cards'][$idx]['image'] = trim($_POST["card{$i}_image"]);
    }

    // Section 3: Festive Offer Card
    $content['festive_offer']['enabled'] = isset($_POST['festive_offer_enabled']) && ($_POST['festive_offer_enabled'] === '1' || $_POST['festive_offer_enabled'] === 'true' || $_POST['festive_offer_enabled'] === 'on');
    if (isset($_POST['festive_offer_badge'])) $content['festive_offer']['badge'] = trim($_POST['festive_offer_badge']);
    if (isset($_POST['festive_offer_title'])) $content['festive_offer']['title'] = trim($_POST['festive_offer_title']);
    if (isset($_POST['festive_offer_description'])) $content['festive_offer']['description'] = trim($_POST['festive_offer_description']);
    if (isset($_POST['festive_offer_coupon'])) $content['festive_offer']['coupon_code'] = strtoupper(trim($_POST['festive_offer_coupon']));
    if (isset($_POST['festive_offer_discount_label'])) $content['festive_offer']['discount_label'] = trim($_POST['festive_offer_discount_label']);

    // Section 4: Story Chapter Teaser
    if (isset($_POST['story_badge'])) $content['story']['badge'] = trim($_POST['story_badge']);
    if (isset($_POST['story_title'])) $content['story']['title'] = trim($_POST['story_title']);
    if (isset($_POST['story_lead'])) $content['story']['lead'] = trim($_POST['story_lead']);
    if (isset($_POST['story_quote'])) $content['story']['quote'] = trim($_POST['story_quote']);
    if (isset($_POST['story_author'])) $content['story']['author'] = trim($_POST['story_author']);
    if (isset($_POST['story_promise'])) $content['story']['promise'] = trim($_POST['story_promise']);
    if (isset($_POST['story_image'])) $content['story']['image'] = trim($_POST['story_image']);

    // Highlights
    if (isset($_POST['story_h1_title'])) $content['story']['highlights'][0]['title'] = trim($_POST['story_h1_title']);
    if (isset($_POST['story_h1_desc'])) $content['story']['highlights'][0]['desc'] = trim($_POST['story_h1_desc']);
    if (isset($_POST['story_h2_title'])) $content['story']['highlights'][1]['title'] = trim($_POST['story_h2_title']);
    if (isset($_POST['story_h2_desc'])) $content['story']['highlights'][1]['desc'] = trim($_POST['story_h2_desc']);
    if (isset($_POST['story_h3_title'])) $content['story']['highlights'][2]['title'] = trim($_POST['story_h3_title']);
    if (isset($_POST['story_h3_desc'])) $content['story']['highlights'][2]['desc'] = trim($_POST['story_h3_desc']);

    $saved = save_homepage_content($content);
    echo json_encode(['success' => $saved, 'message' => $saved ? 'Homepage content updated successfully!' : 'Failed to update homepage content']);
    exit;
}

// -------------------------------------------------------
// COUPON MANAGEMENT (Save / Delete)
// -------------------------------------------------------
if ($action === 'save_coupon') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discountPercent = (float)($_POST['discountPercent'] ?? 10);
    $minOrder = (float)($_POST['minOrder'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $active = isset($_POST['active']) && ($_POST['active'] === '1' || $_POST['active'] === 'true' || $_POST['active'] === 'on');

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
        exit;
    }

    $coupons = get_all_coupons();
    $found = false;

    foreach ($coupons as &$c) {
        if (strtoupper($c['code']) === $code) {
            $c['discountPercent'] = $discountPercent;
            $c['minOrder'] = $minOrder;
            $c['description'] = $description;
            $c['active'] = $active;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $coupons[] = [
            'code' => $code,
            'discountPercent' => $discountPercent,
            'minOrder' => $minOrder,
            'description' => $description,
            'active' => $active
        ];
    }

    save_all_coupons($coupons);
    echo json_encode(['success' => true, 'message' => "Coupon {$code} saved successfully!"]);
    exit;
}

if ($action === 'delete_coupon') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $coupons = get_all_coupons();
    $coupons = array_values(array_filter($coupons, fn($c) => strtoupper($c['code']) !== $code));
    save_all_coupons($coupons);
    echo json_encode(['success' => true, 'message' => "Coupon {$code} deleted successfully"]);
    exit;
}

// -------------------------------------------------------
// REVIEWS MANAGEMENT (Save / Delete)
// -------------------------------------------------------
if ($action === 'save_review') {
    $id = $_POST['id'] ?? ('rev-' . round(microtime(true) * 1000));
    $productId = $_POST['productId'] ?? 'prod-1';
    $name = trim($_POST['name'] ?? 'Verified Patron');
    $location = trim($_POST['location'] ?? 'India');
    $rating = (float)($_POST['rating'] ?? 5.0);
    $comment = trim($_POST['comment'] ?? '');
    $date = date('d M Y');

    $reviews = get_all_reviews();
    $found = false;

    foreach ($reviews as &$r) {
        if ($r['id'] === $id) {
            $r['productId'] = $productId;
            $r['name'] = $name;
            $r['location'] = $location;
            $r['rating'] = $rating;
            $r['comment'] = $comment;
            $found = true;
            break;
        }
    }

    if (!$found) {
        array_unshift($reviews, [
            'id' => $id,
            'productId' => $productId,
            'name' => $name,
            'location' => $location,
            'rating' => $rating,
            'comment' => $comment,
            'date' => $date
        ]);
    }

    save_all_reviews($reviews);
    echo json_encode(['success' => true, 'message' => 'Review saved successfully!']);
    exit;
}

if ($action === 'delete_review') {
    $id = $_POST['id'] ?? '';
    $reviews = get_all_reviews();
    $reviews = array_values(array_filter($reviews, fn($r) => $r['id'] !== $id));
    save_all_reviews($reviews);
    echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
