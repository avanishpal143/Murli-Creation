<?php
/**
 * =============================================================================
 * MURLI CREATIONS - SHOP VIEW
 * =============================================================================
 * Is page ka design, layout aur storefront logic yahan sthit hai.
 * Backend data: backend/config/functions.php
 * =============================================================================
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}
require_once ROOT_PATH . '/backend/config/functions.php';

require_once __DIR__ . '/includes/product-card.php';

$all_products = get_all_products();
$category_tree = get_category_tree();

// Read URL parameters
$selectedCategory = $_GET['category'] ?? 'All';
$selectedSubcategory = $_GET['subcategory'] ?? 'All';
$selectedAvailability = $_GET['availability'] ?? 'all';
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 10000;
$sortBy = $_GET['sort'] ?? 'default';
$searchQuery = trim($_GET['search'] ?? '');

// Filter products
$filtered_products = array_filter($all_products, function($p) use ($selectedCategory, $selectedSubcategory, $selectedAvailability, $maxPrice, $searchQuery) {
    // Category match
    $matchesCategory = ($selectedCategory === 'All' || $p['category'] === $selectedCategory);
    
    // Subcategory match
    $matchesSubcategory = true;
    if ($selectedSubcategory !== 'All' && !empty($selectedSubcategory)) {
        $matchesSubcategory = (isset($p['subcategory']) && $p['subcategory'] === $selectedSubcategory);
    }

    // Availability match
    $matchesAvailability = true;
    if ($selectedAvailability === 'in_stock') {
        $matchesAvailability = (($p['stock'] ?? 0) > 0);
    } elseif ($selectedAvailability === 'out_of_stock') {
        $matchesAvailability = (($p['stock'] ?? 0) <= 0);
    }

    // Price match
    $matchesPrice = ($p['price'] <= $maxPrice);
    
    // Search query match (supports title, SKU product code, tags, category, material, size, description)
    $matchesSearch = true;
    if ($searchQuery !== '') {
        $q = strtolower($searchQuery);
        $qClean = preg_replace('/[^a-z0-9]/', '', $q);
        $code = strtolower($p['code'] ?? '');
        $codeClean = preg_replace('/[^a-z0-9]/', '', $code);
        $id = strtolower($p['id'] ?? '');

        // SKU Code / ID matches (exact substring, e.g. "MC/S-016" or normalized e.g. "mcs016", "s016")
        $matchesCode = ($code !== '' && strpos($code, $q) !== false) ||
                       ($qClean !== '' && $codeClean !== '' && strpos($codeClean, $qClean) !== false) ||
                       ($id !== '' && strpos($id, $q) !== false);

        // Tags match
        $matchesTags = false;
        if (!empty($p['tags']) && is_array($p['tags'])) {
            foreach ($p['tags'] as $tag) {
                if (strpos(strtolower($tag), $q) !== false) {
                    $matchesTags = true;
                    break;
                }
            }
        }

        $matchesSearch = (
            $matchesCode ||
            strpos(strtolower($p['name']), $q) !== false ||
            strpos(strtolower($p['category']), $q) !== false ||
            strpos(strtolower($p['subcategory'] ?? ''), $q) !== false ||
            strpos(strtolower($p['description']), $q) !== false ||
            strpos(strtolower($p['material'] ?? ''), $q) !== false ||
            strpos(strtolower($p['size'] ?? ''), $q) !== false ||
            $matchesTags
        );
    }
    
    return $matchesCategory && $matchesSubcategory && $matchesAvailability && $matchesPrice && $matchesSearch;
});

// Sort products
usort($filtered_products, function($a, $b) use ($sortBy) {
    if ($sortBy === 'price-low') {
        return $a['price'] <=> $b['price'];
    } elseif ($sortBy === 'price-high') {
        return $b['price'] <=> $a['price'];
    } elseif ($sortBy === 'rating') {
        return ($b['rating'] ?? 4.8) <=> ($a['rating'] ?? 4.8);
    } elseif ($sortBy === 'bestseller') {
        $aBest = !empty($a['bestseller']) ? 1 : 0;
        $bBest = !empty($b['bestseller']) ? 1 : 0;
        return $bBest <=> $aBest;
    } elseif ($sortBy === 'newest') {
        return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
    }
    // Default: featured first
    $aFeat = !empty($a['featured']) ? 1 : 0;
    $bFeat = !empty($b['featured']) ? 1 : 0;
    return $bFeat <=> $aFeat;
});

// Available Subcategories for the current category
$subcategories = [];
if ($selectedCategory !== 'All' && isset($category_tree[$selectedCategory])) {
    $subcategories = array_keys($category_tree[$selectedCategory]['subcategories']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if (function_exists('render_page_seo_head')): ?>
    <?php echo render_page_seo_head('shop'); ?>
  <?php else: ?>
    <title>The Art Catalog | Murli Creations Handcrafted Heritage</title>
    <meta name="description" content="Explore handcrafted Lippan art panels, Shyam Baba poshak, Vastu sacred creations, and traditional carved Jharokhas by master artisan Murli Prasad.">
  <?php endif; ?>

  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="/assets/css/globals.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/product-card.css">
  <link rel="stylesheet" href="/assets/css/cart-drawer.css">
  <link rel="stylesheet" href="/assets/css/floating-widgets.css">
  <link rel="stylesheet" href="/assets/css/toast.css">
  <link rel="stylesheet" href="/assets/css/shop.css">
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main style="padding-top: 80px;">
    <div class="shop-page">
      <div class="container">
        
        <!-- 1. Top Primary Category Pills (Horizontal Navigation) -->
        <div class="top-categories-bar">
          <?php
            // Build base URL helper
            function buildCatUrl($catName, $subName = 'All') {
                $params = $_GET;
                if ($catName === 'All') {
                    unset($params['category']);
                    unset($params['subcategory']);
                } else {
                    $params['category'] = $catName;
                    if ($subName === 'All') {
                        unset($params['subcategory']);
                    } else {
                        $params['subcategory'] = $subName;
                    }
                }
                return '/shop.php' . (!empty($params) ? '?' . http_build_query($params) : '');
            }
          ?>
          
          <a href="<?php echo buildCatUrl('All'); ?>" class="top-cat-pill <?php echo ($selectedCategory === 'All') ? 'active' : ''; ?>">
            <span>All Creations</span>
            <span class="top-cat-count">(<?php echo count($all_products); ?>)</span>
          </a>

          <?php foreach ($category_tree as $catKey => $catInfo): 
            $isActive = ($selectedCategory === $catKey);
            // Count products in this category
            $catCount = count(array_filter($all_products, fn($p) => $p['category'] === $catKey));
          ?>
            <a href="<?php echo buildCatUrl($catKey); ?>" class="top-cat-pill <?php echo $isActive ? 'active' : ''; ?>">
              <span class="top-cat-icon"><?php echo icon_svg($catInfo['icon'], '', 13); ?></span>
              <span><?php echo htmlspecialchars($catInfo['name']); ?></span>
              <span class="top-cat-count">(<?php echo $catCount; ?>)</span>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- 2. Second-Tier Subcategories Chips (When category is chosen) -->
        <?php if (!empty($subcategories)): ?>
          <div class="subcategories-bar">
            <span class="sub-filter-label">Filter by Type:</span>
            <a href="<?php echo buildCatUrl($selectedCategory, 'All'); ?>" class="sub-chip <?php echo ($selectedSubcategory === 'All') ? 'active' : ''; ?>">
              All in <?php echo htmlspecialchars($selectedCategory); ?>
            </a>
            <?php foreach ($subcategories as $sub): 
              $isSubActive = ($selectedSubcategory === $sub);
            ?>
              <a href="<?php echo buildCatUrl($selectedCategory, $sub); ?>" class="sub-chip <?php echo $isSubActive ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($sub); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- 3. Filter & Sort Toolbar Bar -->
        <div class="shop-toolbar-bar">
          <div class="toolbar-left">
            <span class="results-count">
              Showing <strong><?php echo count($filtered_products); ?></strong> of <strong><?php echo count($all_products); ?></strong> creations
            </span>

            <?php if ($selectedCategory !== 'All' || $selectedSubcategory !== 'All' || $searchQuery !== '' || $maxPrice < 10000): ?>
              <a href="/shop.php" class="clear-all-filters-btn">
                <?php echo icon_svg('x', '', 12); ?> Clear All Filters
              </a>
            <?php endif; ?>
          </div>

          <div class="toolbar-right">
            <!-- Price Budget Slider -->
            <div class="budget-slider-box">
              <label for="price-range-slider" class="budget-label">
                <span>Max:</span> <strong id="price-range-display">₹<?php echo format_inr($maxPrice); ?></strong>
              </label>
              <input
                type="range"
                id="price-range-slider"
                min="500"
                max="10000"
                step="200"
                value="<?php echo $maxPrice; ?>"
                class="budget-range-input"
              />
            </div>

            <!-- Availability Filter Dropdown -->
            <div class="sort-box">
              <label for="availability-select" class="sort-label">Stock:</label>
              <select id="availability-select" class="sort-dropdown" aria-label="Filter by stock">
                <option value="all" <?php echo ($selectedAvailability === 'all') ? 'selected' : ''; ?>>All Items</option>
                <option value="in_stock" <?php echo ($selectedAvailability === 'in_stock') ? 'selected' : ''; ?>>In Stock</option>
                <option value="out_of_stock" <?php echo ($selectedAvailability === 'out_of_stock') ? 'selected' : ''; ?>>Sold Out</option>
              </select>
            </div>

            <!-- Premium Sort By Dropdown (A-Z removed, added Bestsellers & Rating) -->
            <div class="sort-box">
              <label for="sort-select" class="sort-label">Sort By:</label>
              <select id="sort-select" class="sort-dropdown" aria-label="Sort products">
                <option value="default" <?php echo ($sortBy === 'default') ? 'selected' : ''; ?>>Featured & Curated</option>
                <option value="bestseller" <?php echo ($sortBy === 'bestseller') ? 'selected' : ''; ?>>Bestsellers & Popular</option>
                <option value="newest" <?php echo ($sortBy === 'newest') ? 'selected' : ''; ?>>New Arrivals</option>
                <option value="rating" <?php echo ($sortBy === 'rating') ? 'selected' : ''; ?>>Customer Rating (Highest)</option>
                <option value="price-low" <?php echo ($sortBy === 'price-low') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price-high" <?php echo ($sortBy === 'price-high') ? 'selected' : ''; ?>>Price: High to Low</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Search Query Tag if active -->
        <?php if ($searchQuery !== ''): 
          $clearSearchUrlParams = $_GET;
          unset($clearSearchUrlParams['search']);
          $clearSearchUrl = '/shop.php' . (!empty($clearSearchUrlParams) ? '?' . http_build_query($clearSearchUrlParams) : '');
        ?>
          <div class="active-query-banner">
            <span>Showing results for search: "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"</span>
            <a href="<?php echo $clearSearchUrl; ?>" class="clear-search-pill">
              <?php echo icon_svg('x', '', 12); ?> Clear search
            </a>
          </div>
        <?php endif; ?>

        <!-- 4. Myntra-Style 4-Column Responsive Catalog Grid -->
        <?php if (empty($filtered_products)): ?>
          <div class="shop-empty-state">
            <div class="shop-empty-icon"><?php echo icon_svg('search', '', 40); ?></div>
            <h3 class="shop-empty-title">No Creations Found</h3>
            <p class="shop-empty-text">
              We couldn't find any artworks matching your combined filters. Try clearing your search query or choosing another category above.
            </p>
            <a href="/shop.php" class="shop-reset-btn">
              Browse All <?php echo count($all_products); ?> Creations
            </a>
          </div>
        <?php else: ?>
          <div class="myntra-catalog-grid">
            <?php foreach ($filtered_products as $product): ?>
              <div>
                <?php render_product_card($product); ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <?php include __DIR__ . '/includes/cart-drawer.php'; ?>
  <?php include __DIR__ . '/includes/floating-widgets.php'; ?>
  <?php include __DIR__ . '/includes/toast.php'; ?>

  <!-- Scripts -->
  <script src="/assets/js/tilt.js"></script>
  <script src="/assets/js/toast.js"></script>
  <script src="/assets/js/cart.js"></script>
  <script src="/assets/js/shop.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>
