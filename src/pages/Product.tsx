import React, { useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useShop } from "../context/ShopContext";
import ProductCard from "../components/ProductCard";
import styles from "./Product.module.css";

export default function ProductDetailPage() {
  const { slug } = useParams<{ slug: string }>();
  const { products, addToCart, reviews, addReview } = useShop();

  const [activeMediaIndex, setActiveMediaIndex] = useState(0);
  const [purchaseQuantity, setPurchaseQuantity] = useState(1);
  const [openAccordion, setOpenAccordion] = useState<string | null>("folklore");

  // Form states for reviews
  const [newReviewName, setNewReviewName] = useState("");
  const [newReviewRating, setNewReviewRating] = useState(5);
  const [newReviewComment, setNewReviewComment] = useState("");

  // Fetch the matching product
  const product = products.find((p) => p.slug === slug);

  if (!product) {
    return (
      <div className={styles.detailPage}>
        <div className={`container ${styles.missing}`}>
          <h2 className={styles.missingTitle}>Artwork Not Found</h2>
          <p className={styles.missingText}>The handcrafted piece you are looking for does not exist or has been archived.</p>
          <Link to="/shop" className={styles.backBtn}>
            Back to Catalog
          </Link>
        </div>
      </div>
    );
  }

  // Get related products (same category, excluding current product)
  const relatedProducts = products
    .filter((p) => p.category === product.category && p.id !== product.id)
    .slice(0, 3);

  // Fallback related products if none in the same category
  const fallbackProducts = relatedProducts.length > 0
    ? relatedProducts
    : products.filter((p) => p.id !== product.id).slice(0, 3);

  // Prepare media list (images + video if present)
  const mediaList = [...product.images];
  if (product.video) {
    mediaList.push(product.video);
  }

  const handleAdd = () => {
    addToCart(product, purchaseQuantity);
  };

  const toggleAccordion = (id: string) => {
    setOpenAccordion(openAccordion === id ? null : id);
  };

  const activeMedia = mediaList[activeMediaIndex];
  const isVideo = activeMedia && (activeMedia.endsWith(".mp4") || activeMedia.includes("youtube.com") || activeMedia.includes("youtu.be"));

  // Safe Fallback Calculations to prevent runtime crashes from stale localStorage
  const mrpVal = product.mrp || Math.round(product.price * 1.3);
  const ratingVal = product.rating || 4.8;
  const reviewsCountVal = product.reviewsCount || 15;
  const artistVal = product.artist || "Master Craftsman Murli Prasad";
  const discountPercent = Math.round(((mrpVal - product.price) / mrpVal) * 100);

  // Filter reviews for this product
  const productReviews = reviews.filter((r) => r.productId === product.id);

  const handleReviewSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newReviewName.trim() || !newReviewComment.trim()) return;
    addReview(product.id, newReviewName, newReviewRating, newReviewComment);
    setNewReviewName("");
    setNewReviewRating(5);
    setNewReviewComment("");
  };

  return (
    <div className={styles.detailPage}>
      <div className="container">
        {/* Breadcrumb Navigation */}
        <div className={styles.breadcrumbs}>
          <Link to="/">Home</Link> &nbsp;&gt;&nbsp;{" "}
          <Link to="/shop">Shop</Link> &nbsp;&gt;&nbsp;{" "}
          <Link to={`/shop?category=${encodeURIComponent(product.category)}`}>
            {product.category}
          </Link>{" "}
          &nbsp;&gt;&nbsp; <span>{product.name}</span>
        </div>

        {/* Product details grid layout */}
        <div className={styles.layout}>
          {/* Column 1: Image & Video Gallery */}
          <div className={styles.gallery}>
            <div className={styles.mainMediaContainer}>
              {isVideo ? (
                activeMedia.includes("youtube.com") || activeMedia.includes("youtu.be") ? (
                  <iframe
                    src={activeMedia.replace("watch?v=", "embed/")}
                    title={`${product.name} Video`}
                    frameBorder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowFullScreen
                    className={styles.mainMediaVideo}
                    style={{ width: "100%", height: "100%", border: "none" }}
                  />
                ) : (
                  <video
                    src={activeMedia}
                    controls
                    autoPlay
                    loop
                    muted
                    className={styles.mainMediaVideo}
                  />
                )
              ) : (
                <img
                  src={activeMedia}
                  alt={product.name}
                  className={styles.mainMediaImage}
                  style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
                />
              )}
            </div>

            {/* Thumbnail carousel */}
            {mediaList.length > 1 && (
              <div className={styles.thumbnails}>
                {mediaList.map((media, idx) => {
                  const isThumbVideo = media.endsWith(".mp4") || media.includes("youtube.com") || media.includes("youtu.be");
                  return (
                    <button
                      key={idx}
                      onClick={() => setActiveMediaIndex(idx)}
                      className={`${styles.thumbnailBtn} ${
                        activeMediaIndex === idx ? styles.activeThumbnail : ""
                      }`}
                      aria-label={`View media item ${idx + 1}`}
                    >
                      {isThumbVideo ? (
                        <div style={{ width: "100%", height: "100%", position: "relative" }}>
                          <div style={{ width: "100%", height: "100%", backgroundColor: "#1e293b", display: "flex", alignItems: "center", justifyContent: "center", color: "#fff", fontSize: "10px" }}>Video</div>
                          <span className={styles.videoBadge}>▶</span>
                        </div>
                      ) : (
                        <img
                          src={media}
                          alt={`${product.name} preview thumbnail ${idx + 1}`}
                          width={72}
                          height={72}
                          className={styles.thumbnailImage}
                          style={{ objectFit: "cover" }}
                        />
                      )}
                    </button>
                  );
                })}
              </div>
            )}
          </div>

          {/* Column 2: Details & Purchase actions */}
          <div className={styles.info}>
            <span className={styles.metaCategory}>{product.category}</span>
            
            {/* Stars Review and Artist Tagline */}
            <div className={styles.ratingRow}>
              <span className={styles.stars}>★ {ratingVal}</span>
              <span className={styles.ratingCount}>({productReviews.length} verified reviews)</span>
              <span className={styles.artistName}>• Handcrafted by {artistVal}</span>
            </div>

            <h2 className={styles.name}>{product.name}</h2>
            
            {/* Discounted pricing row */}
            <div className={styles.priceContainer}>
              <span className={styles.price}>₹{product.price.toLocaleString("en-IN")}</span>
              <span className={styles.mrp}>MRP: ₹{mrpVal.toLocaleString("en-IN")}</span>
              <span className={styles.discountBadge}>{discountPercent}% OFF</span>
            </div>
            
            <p className={styles.description}>{product.description}</p>

            {/* Product Specifications Card */}
            <div className={styles.specsTable}>
              <div className={styles.specRow}>
                <span className={styles.specLabel}>Dimensions</span>
                <span className={styles.specVal}>{product.size}</span>
              </div>
              <div className={styles.specRow}>
                <span className={styles.specLabel}>Materials</span>
                <span className={styles.specVal}>{product.material}</span>
              </div>
              <div className={styles.specRow}>
                <span className={styles.specLabel}>Origin</span>
                <span className={styles.specVal}>{product.origin}</span>
              </div>
              <div className={styles.specRow}>
                <span className={styles.specLabel}>Availability</span>
                <span className={styles.specVal}>
                  {product.stock > 0 ? (
                    <span style={{ color: "var(--accent-sage)", fontWeight: "600" }}>
                      In Stock ({product.stock} units left)
                    </span>
                  ) : (
                    <span style={{ color: "var(--accent-terracotta)", fontWeight: "600" }}>Sold Out</span>
                  )}
                </span>
              </div>
            </div>

            {/* Stepper & Cart add triggers */}
            <div className={styles.purchaseSection}>
              <div className={styles.quantityPicker}>
                <button
                  onClick={() => setPurchaseQuantity(Math.max(1, purchaseQuantity - 1))}
                  disabled={product.stock === 0}
                  className={styles.qtyBtn}
                  aria-label="Decrease quantity"
                >
                  -
                </button>
                <span className={styles.qtyVal}>{purchaseQuantity}</span>
                <button
                  onClick={() => setPurchaseQuantity(Math.min(product.stock, purchaseQuantity + 1))}
                  disabled={product.stock === 0}
                  className={styles.qtyBtn}
                  aria-label="Increase quantity"
                >
                  +
                </button>
              </div>

              <button
                onClick={handleAdd}
                disabled={product.stock === 0}
                className={styles.addBtn}
              >
                {product.stock === 0 ? "Out of Stock" : "Add to Gallery Cart"}
              </button>
            </div>

            {/* FAQ and Care Accordion panel */}
            <div className={styles.accordions}>
              {/* Folklore history section */}
              {product.folklore && (
                <div className={`${styles.accordion} ${openAccordion === "folklore" ? styles.accordionOpen : ""}`}>
                  <button onClick={() => toggleAccordion("folklore")} className={styles.accordionHeader}>
                    <span>History & Symbolism (Folklore)</span>
                    <span className={styles.accordionIcon}>+</span>
                  </button>
                  {openAccordion === "folklore" && (
                    <div className={styles.accordionContent}>
                      {product.folklore}
                    </div>
                  )}
                </div>
              )}

              {/* Shipping Accordion */}
              <div className={`${styles.accordion} ${openAccordion === "shipping" ? styles.accordionOpen : ""}`}>
                <button onClick={() => toggleAccordion("shipping")} className={styles.accordionHeader}>
                  <span>Shipping & Delivery</span>
                  <span className={styles.accordionIcon}>+</span>
                </button>
                {openAccordion === "shipping" && (
                  <div className={styles.accordionContent}>
                    All wall art panels are carefully crated in dual-layer bubble wrap and rigid 5-ply cardboard packaging to prevent mirror breakage or chipping. Orders are dispatched from our Rohtak workshop within 2-4 business days. Standard shipping in India takes 4-7 days. Express courier options are available on request.
                  </div>
                )}
              </div>

              {/* Care Instructions Accordion */}
              <div className={`${styles.accordion} ${openAccordion === "care" ? styles.accordionOpen : ""}`}>
                <button onClick={() => toggleAccordion("care")} className={styles.accordionHeader}>
                  <span>Care & Maintenance</span>
                  <span className={styles.accordionIcon}>+</span>
                </button>
                {openAccordion === "care" && (
                  <div className={styles.accordionContent}>
                    To keep your Lippan art or painted plates radiant:
                    <br />
                    1. Dust gently with a clean, soft micro-fiber cloth or soft-bristled brush.
                    <br />
                    2. Do not use wet cloths, chemical sprays, or clay cleaners directly on the clay paste.
                    <br />
                    3. For embedded mirrors, carefully wipe individual mirrors with a dry cotton swab if needed.
                    <br />
                    4. Hang in dry indoor areas; avoid direct exposure to moisture, rain, or harsh direct sunlight.
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Customer Reviews & Feedback Section */}
        <section className={styles.reviewsSection}>
          <h3 className={styles.reviewsTitle}>Customer Reviews & Art Journal</h3>
          
          <div className={styles.reviewsLayout}>
            {/* Reviews Summary Scorecard */}
            <div className={styles.reviewsSummary}>
              <div className={styles.summaryCard}>
                <h4 className={styles.summaryTitle}>Average Rating</h4>
                <div className={styles.summaryScore}>{ratingVal}</div>
                <div className={styles.summaryStars}>
                  {Array.from({ length: 5 }).map((_, idx) => (
                    <span 
                      key={idx} 
                      className={idx < Math.round(ratingVal) ? styles.starActive : styles.starInactive}
                    >
                      ★
                    </span>
                  ))}
                </div>
                <div className={styles.summaryCount}>{productReviews.length} Verified Reviews</div>
              </div>
            </div>

            {/* Reviews Feed & Submit Form */}
            <div className={styles.reviewsFeed}>
              <div className={styles.reviewsListHeader}>
                <h4>Feedback Thread ({productReviews.length})</h4>
              </div>
              
              <div className={styles.reviewsList}>
                {productReviews.length === 0 ? (
                  <p className={styles.noReviewsText}>No reviews yet. Be the first to share your thoughts on this artisan masterpiece!</p>
                ) : (
                  productReviews.map((rev) => (
                    <div key={rev.id} className={styles.reviewCard}>
                      <div className={styles.reviewHeader}>
                        <div className={styles.reviewInfo}>
                          <span className={styles.reviewUser}>{rev.userName}</span>
                          <span className={styles.reviewDate}>{rev.date}</span>
                        </div>
                        <div className={styles.reviewStars}>
                          {Array.from({ length: 5 }).map((_, idx) => (
                            <span 
                              key={idx} 
                              className={idx < rev.rating ? styles.starActive : styles.starInactive}
                            >
                              ★
                            </span>
                          ))}
                        </div>
                      </div>
                      <p className={styles.reviewComment}>{rev.comment}</p>
                    </div>
                  ))
                )}
              </div>

              {/* Review Write Form */}
              <div className={styles.writeReviewContainer}>
                <h4>Write an Art Review</h4>
                <p className={styles.writeReviewSub}>Have you purchased this artwork? Let us know your feedback on texture, borders, and clay craft.</p>
                
                <form onSubmit={handleReviewSubmit} className={styles.reviewForm}>
                  <div className={styles.formRow}>
                    <div className={styles.formGroup}>
                      <label htmlFor="reviewerName">Your Name</label>
                      <input
                        type="text"
                        id="reviewerName"
                        value={newReviewName}
                        onChange={(e) => setNewReviewName(e.target.value)}
                        placeholder="e.g. Snehil K."
                        required
                        className={styles.formInput}
                      />
                    </div>

                    <div className={styles.formGroup}>
                      <label>Your Rating</label>
                      <div className={styles.starSelector}>
                        {Array.from({ length: 5 }).map((_, idx) => {
                          const starValue = idx + 1;
                          return (
                            <button
                              type="button"
                              key={idx}
                              onClick={() => setNewReviewRating(starValue)}
                              className={starValue <= newReviewRating ? styles.starSelectActive : styles.starSelectInactive}
                              aria-label={`Rate ${starValue} stars`}
                            >
                              ★
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  </div>

                  <div className={styles.formGroup}>
                    <label htmlFor="reviewerComment">Your Comments</label>
                    <textarea
                      id="reviewerComment"
                      value={newReviewComment}
                      onChange={(e) => setNewReviewComment(e.target.value)}
                      placeholder="Comment on detailing, wooden carvings, packaging crate..."
                      rows={3}
                      required
                      className={styles.formTextarea}
                    />
                  </div>

                  <button type="submit" className={styles.submitReviewBtn}>
                    Submit Art Review
                  </button>
                </form>
              </div>
            </div>
          </div>
        </section>

        {/* Related Products Showcase */}
        <section className={styles.related}>
          <h3 className={styles.relatedTitle}>You May Also Appreciate</h3>
          <div className={styles.relatedGrid}>
            {fallbackProducts.map((relProduct) => (
              <div key={relProduct.id}>
                <ProductCard product={relProduct} />
              </div>
            ))}
          </div>
        </section>
      </div>
    </div>
  );
}
