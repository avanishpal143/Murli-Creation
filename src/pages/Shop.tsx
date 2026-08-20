import React, { useState, useEffect, Suspense } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import { useShop } from "../context/ShopContext";
import ProductCard from "../components/ProductCard";
import styles from "./Shop.module.css";

function ShopContent() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { products } = useShop();

  // State filters
  const [selectedCategory, setSelectedCategory] = useState<string>("All");
  const [maxPrice, setMaxPrice] = useState<number>(10000);
  const [sortBy, setSortBy] = useState<string>("default");
  const [searchQuery, setSearchQuery] = useState<string>("");

  // Sync state filters with URL parameters
  useEffect(() => {
    const categoryParam = searchParams.get("category");
    const searchParam = searchParams.get("search");

    if (categoryParam) {
      setSelectedCategory(categoryParam);
    } else {
      setSelectedCategory("All");
    }

    if (searchParam) {
      setSearchQuery(searchParam);
    } else {
      setSearchQuery("");
    }
  }, [searchParams]);

  // Extract unique categories dynamically from active products
  const categories = ["All", ...Array.from(new Set(products.map((p) => p.category)))];

  // Filtering Logic
  const filteredProducts = products.filter((product) => {
    const matchesCategory =
      selectedCategory === "All" || product.category === selectedCategory;
    const matchesPrice = product.price <= maxPrice;
    const matchesSearch =
      searchQuery === "" ||
      product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      product.category.toLowerCase().includes(searchQuery.toLowerCase()) ||
      product.description.toLowerCase().includes(searchQuery.toLowerCase());

    return matchesCategory && matchesPrice && matchesSearch;
  });

  // Sorting Logic
  const sortedProducts = [...filteredProducts].sort((a, b) => {
    if (sortBy === "price-low") {
      return a.price - b.price;
    }
    if (sortBy === "price-high") {
      return b.price - a.price;
    }
    if (sortBy === "name-asc") {
      return a.name.localeCompare(b.name);
    }
    // Default sorting / static ID ordering
    return 0;
  });

  const handleResetFilters = () => {
    setSelectedCategory("All");
    setMaxPrice(10000);
    setSortBy("default");
    setSearchQuery("");
    navigate("/shop");
  };

  const handleRemoveSearch = () => {
    setSearchQuery("");
    const params = new URLSearchParams(searchParams.toString());
    params.delete("search");
    navigate(`/shop?${params.toString()}`);
  };

  return (
    <div className={styles.shop}>
      <div className="container">
        {/* Banner Section */}
        <div className={styles.banner}>
          <h2 className={styles.title}>The Wall Art Catalog</h2>
          <p className={styles.subtitle}>
            Browse our collection of hand-kneaded Lippan murals, wood Jharokhas, and hand-painted plates.
          </p>
        </div>

        {/* Dynamic Shop Grid and Filters */}
        <div className={styles.layout}>
          {/* Sidebar Filters */}
          <aside className={styles.sidebar}>
            {/* Category Filter */}
            <div className={styles.filterGroup}>
              <h3 className={styles.filterTitle}>Categories</h3>
              <div className={styles.filterList}>
                {categories.map((cat) => (
                  <button
                    key={cat}
                    onClick={() => {
                      setSelectedCategory(cat);
                      const params = new URLSearchParams(searchParams.toString());
                      if (cat === "All") {
                        params.delete("category");
                      } else {
                        params.set("category", cat);
                      }
                      navigate(`/shop?${params.toString()}`);
                    }}
                    className={`${styles.categoryBtn} ${
                      selectedCategory === cat ? styles.activeCategory : ""
                    }`}
                  >
                    {cat}
                  </button>
                ))}
              </div>
            </div>

            {/* Price Filter */}
            <div className={styles.filterGroup}>
              <h3 className={styles.filterTitle}>Max Budget (INR)</h3>
              <div style={{ display: "flex", flexDirection: "column", gap: "6px" }}>
                <input
                  type="range"
                  min="500"
                  max="10000"
                  step="100"
                  value={maxPrice}
                  onChange={(e) => setMaxPrice(Number(e.target.value))}
                  style={{ accentColor: "var(--accent-gold)", cursor: "pointer" }}
                />
                <div
                  style={{
                    display: "flex",
                    justifyContent: "space-between",
                    fontSize: "0.8rem",
                    color: "var(--text-secondary)",
                    fontWeight: "600"
                  }}
                >
                  <span>₹500</span>
                  <span>₹{maxPrice.toLocaleString("en-IN")}</span>
                </div>
              </div>
            </div>

            {/* Reset Button */}
            <button onClick={handleResetFilters} className={styles.resetBtn}>
              Clear Filters
            </button>
          </aside>

          {/* Catalog content */}
          <main className={styles.catalog}>
            {/* Search Query Details if active */}
            {searchQuery && (
              <div className={styles.activeQuery}>
                <span className={styles.queryText}>
                  Showing results for search "<strong>{searchQuery}</strong>"
                </span>
                <button onClick={handleRemoveSearch} className={styles.clearQueryBtn}>
                  Clear search
                </button>
              </div>
            )}

            {/* Header controls (sort selection & product counting) */}
            <div className={styles.controls}>
              <div className={styles.resultsCount}>
                Showing <strong>{sortedProducts.length}</strong> of{" "}
                <strong>{products.length}</strong> creations
              </div>
              <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
                <span style={{ fontSize: "0.85rem", color: "var(--text-muted)", fontWeight: "600" }}>
                  Sort By:
                </span>
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className={styles.sortSelect}
                  aria-label="Sort products"
                >
                  <option value="default">Featured</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="name-asc">Alphabetical: A-Z</option>
                </select>
              </div>
            </div>

            {/* Grid display */}
            {sortedProducts.length === 0 ? (
              <div className={styles.emptyState}>
                <div className={styles.emptyIcon}>🔍</div>
                <h3 className={styles.emptyTitle}>No Masterpieces Found</h3>
                <p className={styles.emptyText}>
                  No products matched your combined filters. Try adjusting your search query, raising the budget range, or choosing another category.
                </p>
                <button onClick={handleResetFilters} className={styles.resetBtn}>
                  Reset All Filters
                </button>
              </div>
            ) : (
              <div className={styles.grid}>
                {sortedProducts.map((product) => (
                  <div key={product.id}>
                    <ProductCard product={product} />
                  </div>
                ))}
              </div>
            )}
          </main>
        </div>
      </div>
    </div>
  );
}

export default function Shop() {
  return (
    <Suspense
      fallback={
        <div className="container" style={{ padding: "100px 0", textAlign: "center" }}>
          <div className="spinner"></div>
          <p style={{ marginTop: "10px", color: "var(--text-muted)" }}>Loading wall art catalog...</p>
        </div>
      }
    >
      <ShopContent />
    </Suspense>
  );
}
