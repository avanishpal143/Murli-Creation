"use client";

import React, { useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { useShop } from "../context/ShopContext";
import { Product } from "../data/products";
import styles from "./ProductCard.module.css";

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useShop();
  const [isQuickViewOpen, setIsQuickViewOpen] = useState(false);

  const handleQuickAdd = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    addToCart(product, 1);
  };

  // Safe Fallback Calculations to prevent runtime crashes from stale localStorage
  const mrpVal = product.mrp || Math.round(product.price * 1.3);
  const ratingVal = product.rating || 4.8;
  const reviewsCountVal = product.reviewsCount || 15;
  const artistVal = product.artist || "Master Craftsman Murli Prasad";
  
  const discountPercent = Math.round(((mrpVal - product.price) / mrpVal) * 100);
  const shortArtistName = artistVal.split(" (")[0];

  return (
    <>
      <div className={styles.card}>
        <Link href={`/product/${product.slug}`} className={styles.linkWrapper}>
          {/* Product Image Frame */}
          <div className={styles.imageFrame}>
            <Image
              src={product.images[0]}
              alt={product.name}
              fill
              sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
              className={styles.image}
              priority={false}
            />
            {/* Quick View Button overlay */}
            <div className={styles.overlay}>
              <button
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  setIsQuickViewOpen(true);
                }}
                className={styles.quickViewBtn}
              >
                Quick View
              </button>
            </div>
            
            {/* Stock indicator badge */}
            {product.stock <= 3 && product.stock > 0 && (
              <span className={styles.stockBadge}>Only {product.stock} left</span>
            )}
            {product.stock === 0 && (
              <span className={`${styles.stockBadge} ${styles.outOfStock}`}>Sold Out</span>
            )}
          </div>

          {/* Product Details */}
          <div className={styles.details}>
            <div className={styles.ratingRow}>
              <span className={styles.stars}>★ {ratingVal}</span>
              <span className={styles.ratingCount}>({reviewsCountVal})</span>
              <span className={styles.artistName}>• {shortArtistName}</span>
            </div>

            <span className={styles.category}>{product.category}</span>
            <h3 className={styles.name}>{product.name}</h3>
            
            <div className={styles.footer}>
              <div className={styles.priceContainer}>
                <span className={styles.price}>₹{product.price.toLocaleString("en-IN")}</span>
                <span className={styles.mrp}>₹{mrpVal.toLocaleString("en-IN")}</span>
                <span className={styles.discountBadge}>{discountPercent}% OFF</span>
              </div>
              <button
                onClick={handleQuickAdd}
                disabled={product.stock === 0}
                className={styles.quickAddBtn}
                aria-label={`Add ${product.name} to cart`}
              >
                {product.stock === 0 ? "Sold Out" : "+ Add"}
              </button>
            </div>
          </div>
        </Link>
      </div>

      {/* Quick View Modal Overlay */}
      {isQuickViewOpen && (
        <div className={styles.modalBackdrop} onClick={() => setIsQuickViewOpen(false)}>
          <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
            <button
              onClick={() => setIsQuickViewOpen(false)}
              className={styles.modalClose}
              aria-label="Close modal"
            >
              &times;
            </button>
            
            <div className={styles.modalGrid}>
              <div className={styles.modalImageContainer}>
                <Image
                  src={product.images[0]}
                  alt={product.name}
                  width={360}
                  height={360}
                  className={styles.modalImage}
                />
              </div>
              <div className={styles.modalDetails}>
                <span className={styles.modalCategory}>{product.category}</span>
                <h2 className={styles.modalName}>{product.name}</h2>

                {/* Stars and Artist Info inside modal */}
                <div className={styles.modalRatingRow}>
                  <span className={styles.stars}>★ {ratingVal}</span>
                  <span className={styles.ratingCount}>({reviewsCountVal} reviews)</span>
                  <span className={styles.modalArtist}>• Handcrafted by {artistVal}</span>
                </div>
                
                {/* MRP and discount inside modal */}
                <div className={styles.modalPriceContainer}>
                  <span className={styles.modalPrice}>₹{product.price.toLocaleString("en-IN")}</span>
                  <span className={styles.modalMrp}>₹{mrpVal.toLocaleString("en-IN")}</span>
                  <span className={styles.modalDiscount}>{discountPercent}% OFF</span>
                </div>
                
                <p className={styles.modalDescription}>{product.description}</p>
                
                <div className={styles.specs}>
                  <p><strong>Dimensions:</strong> {product.size}</p>
                  <p><strong>Material:</strong> {product.material}</p>
                  <p><strong>Origin:</strong> {product.origin}</p>
                  <p>
                    <strong>Availability:</strong>{" "}
                    {product.stock > 0 ? (
                      <span className={styles.inStockText}>In Stock ({product.stock} units)</span>
                    ) : (
                      <span className={styles.outStockText}>Out of Stock</span>
                    )}
                  </p>
                </div>
                
                <div className={styles.modalActions}>
                  <button
                    onClick={() => {
                      addToCart(product, 1);
                      setIsQuickViewOpen(false);
                    }}
                    disabled={product.stock === 0}
                    className={styles.modalAddBtn}
                  >
                    {product.stock === 0 ? "Out of Stock" : "Add to Cart"}
                  </button>
                  <Link
                    href={`/product/${product.slug}`}
                    onClick={() => setIsQuickViewOpen(false)}
                    className={styles.modalDetailsLink}
                  >
                    View Full Details
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
