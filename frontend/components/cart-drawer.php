<div class="cart-drawer-backdrop" id="cart-drawer-backdrop"></div>

<aside class="cart-drawer-panel" id="cart-drawer-panel" aria-label="Shopping Cart Drawer">
  <!-- Drawer Header -->
  <div class="cart-drawer-header">
    <div style="display: flex; align-items: center; gap: 8px;">
      <?php echo icon_svg('shopping-bag', '', 20); ?>
      <h2 class="cart-drawer-title">Shopping Bag (<span id="drawer-cart-count">0</span>)</h2>
    </div>
    <button class="cart-drawer-close" id="cart-drawer-close-btn" aria-label="Close cart drawer">
      <?php echo icon_svg('x', '', 20); ?>
    </button>
  </div>

  <!-- Cart Items List Container -->
  <div class="cart-drawer-content" id="cart-drawer-content">
    <!-- Populated dynamically via JS -->
    <div class="cart-drawer-empty" id="drawer-empty-state">
      <div class="cart-drawer-empty-icon"><?php echo icon_svg('shopping-bag', '', 48); ?></div>
      <h3 class="cart-drawer-empty-text">Your bag is empty</h3>
      <p class="cart-drawer-empty-subtext">Discover handcrafted Lippan artwork & spiritual wall decor.</p>
      <a href="/shop.php" class="cart-drawer-shop-btn">
        Explore Collection <?php echo icon_svg('arrow-right', '', 14); ?>
      </a>
    </div>

    <div class="cart-drawer-list" id="drawer-items-list" style="display: none;"></div>
  </div>

  <!-- Drawer Footer Summary -->
  <div class="cart-drawer-footer" id="cart-drawer-footer" style="display: none;">
    <div class="cart-drawer-subtotal-row">
      <span>Subtotal:</span>
      <span class="cart-drawer-subtotal-val" id="drawer-subtotal">₹0</span>
    </div>
    <p class="cart-drawer-notice">Taxes and insured courier shipping calculated at checkout.</p>
    <div class="cart-drawer-actions">
      <a href="/checkout.php" class="cart-drawer-checkout-btn">
        Proceed to Checkout <?php echo icon_svg('arrow-right', '', 16); ?>
      </a>
      <a href="/cart.php" class="cart-drawer-view-btn">
        View Full Bag
      </a>
    </div>
  </div>
</aside>
