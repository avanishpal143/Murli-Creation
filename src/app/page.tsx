import React from "react";
import Link from "next/link";
import Image from "next/image";
import ProductCard from "../components/ProductCard";
import { INITIAL_PRODUCTS } from "../data/products";
import styles from "./page.module.css";

export default function Home() {
  // Grab the featured items for the homepage showcase
  const featuredProducts = INITIAL_PRODUCTS.filter((p) => p.featured).slice(0, 3);

  // Mock Instagram images from our product catalog
  const mockInstaPosts = [
    { id: "insta-1", image: "/images/products/lippan_tree.jpg", likes: "1.2k" },
    { id: "insta-2", image: "/images/products/jharokha_mirror.jpg", likes: "982" },
    { id: "insta-3", image: "/images/products/mandala_plate.jpg", likes: "1.4k" },
    { id: "insta-4", image: "/images/products/lippan_tree.jpg", likes: "842" }
  ];

  return (
    <div>
      {/* 1. Hero Showcase */}
      <section className={styles.hero}>
        <div className={`container ${styles.heroGrid}`}>
          <div className={`${styles.heroContent} reveal-on-scroll`}>
            <span className={styles.heroBadge}>Rohtak, Haryana Estd. 2021</span>
            <h2 className={styles.heroTitle}>
              Handcrafted <br />
              <span className={styles.heroTitleHighlight}>Heritage Wall Art</span> <br />
              for Modern Spaces
            </h2>
            <p className={styles.heroDescription}>
              Step into the world of Murli Creations. We design premium, custom mud-clay relief Lippan art, vintage hand-carved Jharokha mirrors, and spiritual mandala plates that invite warmth and soul into your home.
            </p>
            <div className={styles.heroActions}>
              <Link href="/shop" className={styles.primaryBtn}>
                Explore Shop &nbsp;&rarr;
              </Link>
              <Link href="/our-craft" className={styles.secondaryBtn}>
                Our Process
              </Link>
            </div>
          </div>

          <div className={`${styles.heroImageContainer} reveal-on-scroll`}>
            <Image
              src="/images/products/lippan_tree.jpg"
              alt="Handcrafted Tree of Life Lippan Wall Panel"
              fill
              sizes="(max-width: 768px) 100vw, 50vw"
              priority
              style={{ objectFit: "cover" }}
            />
          </div>
        </div>
      </section>

      {/* 2. Brand Stats & Core Pillars */}
      <section className={styles.stats}>
        <div className={`container ${styles.statsGrid}`}>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>100%</div>
            <div className={styles.statLabel}>Handmade & Bespoke</div>
            <div className={styles.statDesc}>No molds or machine stamping. Each line is sculpted by hand with authentic raw clay and mirror work.</div>
          </div>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>5000+</div>
            <div className={styles.statLabel}>Walls Adorned</div>
            <div className={styles.statDesc}>Crafting high-quality statement decor shipped across India from our studio workshop in Rohtak.</div>
          </div>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>4.9★</div>
            <div className={styles.statLabel}>Instagram Rating</div>
            <div className={styles.statDesc}>Loved by art enthusiasts and home decorators for our attention to detail and customization.</div>
          </div>
        </div>
      </section>

      {/* 2.5 Artisan & Trust Metrics Banner */}
      <section className={styles.trustBanner}>
        <div className={`container ${styles.trustGrid}`}>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🇮🇳</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Rohtak Workshop Direct</h4>
              <p className={styles.trustDesc}>Genuine Haryanvi clay-art and woodcraft made directly by regional folk artisans.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>📦</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Transit Damage Covered</h4>
              <p className={styles.trustDesc}>Crated wooden cases. Free placement replacement if any mirrors arrive broken.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🌱</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Earthy Organic Materials</h4>
              <p className={styles.trustDesc}>Lightweight polymer-reinforced organic mud paste for long-lasting structural strength.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🔒</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Secured UPI & Cards</h4>
              <p className={styles.trustDesc}>Safe checkout payments via Razorpay sandboxed gateway framework.</p>
            </div>
          </div>
        </div>
      </section>

      {/* 3. Featured Showcase */}
      <section className={styles.featured}>
        <div className="container">
          <div className={`${styles.sectionHeader} reveal-on-scroll`}>
            <div>
              <span className={styles.heroBadge}>Exquisite Works</span>
              <h2 className={styles.sectionTitle}>Featured Masterpieces</h2>
            </div>
            <Link href="/shop" className={styles.sectionLink}>
              View All Products &rarr;
            </Link>
          </div>
          
          <div className={`${styles.productsGrid} reveal-on-scroll`}>
            {featuredProducts.map((product) => (
              <div key={product.id}>
                <ProductCard product={product} />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. Craft Process Teaser */}
      <section className={styles.process}>
        <div className={`container ${styles.processGrid}`}>
          <div className={`${styles.processContent} reveal-on-scroll`}>
            <span className={styles.heroBadge}>Behind the Art</span>
            <h2 className={styles.sectionTitle}>The Alchemy of Mud and Mirror</h2>
            <p className={styles.heroDescription}>
              Traditional Kutchi Lippan Kaam and Haryanvi wood carving require patience, precision, and passion. Here is a brief look at how we sculpt your murals:
            </p>
            <div className={styles.processSteps}>
              <div className={styles.step}>
                <div className={styles.stepNum}>1</div>
                <div className={styles.stepText}>
                  <h4 className={styles.stepTitle}>Base Preparation & Clay Kneading</h4>
                  <p className={styles.stepDesc}>High-density wooden panels are prepared and lightweight polymer-reinforced clay is hand-kneaded for durability.</p>
                </div>
              </div>
              <div className={styles.step}>
                <div className={styles.stepNum}>2</div>
                <div className={styles.stepText}>
                  <h4 className={styles.stepTitle}>Relief Line Sculpting</h4>
                  <p className={styles.stepDesc}>Intricate lines, coils, and floral motifs are rolled by hand and attached to the base to build structured 3D relief patterns.</p>
                </div>
              </div>
              <div className={styles.step}>
                <div className={styles.stepNum}>3</div>
                <div className={styles.stepText}>
                  <h4 className={styles.stepTitle}>Mirror Inlays & Acrylic Coating</h4>
                  <p className={styles.stepDesc}>Shimmering geometric glass mirrors are embedded into wet clay, followed by hand-painting with long-lasting acrylics.</p>
                </div>
              </div>
            </div>
            <div style={{ marginTop: "var(--spacing-md)" }}>
              <Link href="/our-craft" className={styles.primaryBtn}>
                Learn More About Our Craft
              </Link>
            </div>
          </div>

          <div className={`${styles.processImageContainer} reveal-on-scroll`}>
            <Image
              src="/images/products/jharokha_mirror.jpg"
              alt="Artisan woodcarving detail on Jharokha"
              fill
              sizes="(max-width: 768px) 100vw, 50vw"
              style={{ objectFit: "cover" }}
            />
          </div>
        </div>
      </section>

      {/* 5. Instagram Journal Section */}
      <section className={styles.insta}>
        <div className="container">
          <div className={`${styles.instaHeader} reveal-on-scroll`}>
            <span className={styles.badge}>Our Journal</span>
            <h2 className={styles.instaTitle}>On The Studio Feed</h2>
            <a
              href="https://www.instagram.com/murlicreationsofficial"
              target="_blank"
              rel="noopener noreferrer"
              className={styles.instaHandle}
            >
              @murlicreationsofficial
            </a>
            <p className={styles.instaText}>
              Follow our daily design experiments, workshop processes, custom order shipping diaries, and styling configurations.
            </p>
          </div>

          <div className={`${styles.instaGrid} reveal-on-scroll`}>
            {mockInstaPosts.map((post) => (
              <div key={post.id} className={styles.instaCard}>
                <Image
                  src={post.image}
                  alt={`Murli Creations Instagram post ${post.id}`}
                  fill
                  sizes="(max-width: 768px) 50vw, 25vw"
                  style={{ objectFit: "cover" }}
                />
                <div className={styles.instaOverlay}>
                  <span>❤️ {post.likes}</span>
                </div>
              </div>
            ))}
          </div>

          <div className="reveal-on-scroll">
            <Link href="/instagram" className={styles.instaLinkBtn}>
              Visit Gallery Feed &rarr;
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
