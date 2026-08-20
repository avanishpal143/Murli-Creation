import React from "react";
import { Link } from "react-router-dom";
import ProductCard from "../components/ProductCard";
import { INITIAL_PRODUCTS } from "../data/products";
import Interactive3DTilt from "../components/Interactive3DTilt";
import styles from "./Home.module.css";

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
            <span className={styles.heroBadge}>Where Tradition Meets Creativity</span>
            <h2 className={styles.heroTitle}>
              Crafting Art, <br />
              <span className={styles.heroTitleHighlight}>Creating Emotions</span> <br />
              for Modern Homes
            </h2>
            <p className={styles.heroDescription}>
              Welcome to Murli Creations, a handcrafted home décor brand where faith meets craftsmanship. We thoughtfully design clay relief Lippan Art, vintage carved Jharokha mirrors, and beautiful Khatu Shyam Baba devotional shringar.
            </p>
            <div className={styles.heroActions}>
              <Link to="/shop" className={styles.primaryBtn}>
                Shop Handmade &nbsp;&rarr;
              </Link>
              <Link to="/founder-story" className={styles.secondaryBtn}>
                Founder's Story
              </Link>
            </div>
          </div>

          <Interactive3DTilt className={`${styles.heroImageTilt} reveal-on-scroll`} maxTilt={8} scale={1.01}>
            <div className={styles.heroImageContainer}>
              <img
                src="/images/products/lippan_tree.jpg"
                alt="Handcrafted Tree of Life Lippan Wall Panel"
                className={styles.image}
                style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
              />
            </div>
          </Interactive3DTilt>
        </div>
      </section>

      {/* 2. Brand Stats & Core Pillars */}
      <section className={styles.stats}>
        <div className={`container ${styles.statsGrid}`}>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>100%</div>
            <div className={styles.statLabel}>Handmade with Bhav</div>
            <div className={styles.statDesc}>No molds or machine stamps. Sculpted individually by hand with raw organic clay, mirrors, and deep devotion.</div>
          </div>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>5000+</div>
            <div className={styles.statLabel}>Spaces Blessed</div>
            <div className={styles.statDesc}>Bringing traditional Indian motifs, blessings, and character into modern homes across the country.</div>
          </div>
          <div className={`${styles.statCard} reveal-on-scroll`}>
            <div className={styles.statNum}>4.9★</div>
            <div className={styles.statLabel}>Artisan Sourced</div>
            <div className={styles.statDesc}>Loved by art lovers for our attention to detail, secure packing, and personalized custom creations.</div>
          </div>
        </div>
      </section>

      {/* 2.5 Artisan & Trust Metrics Banner */}
      <section className={styles.trustBanner}>
        <div className={`container ${styles.trustGrid}`}>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🦚</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Crafted with Pure Devotion</h4>
              <p className={styles.trustDesc}>Every devotional creation is hand-assembled with Khatu Shyam Baba blessings and love.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>📦</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Transit Damage Covered</h4>
              <p className={styles.trustDesc}>Dual bubble wrap + crated wooden cases. Full replacement if mirrors arrive broken.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🌱</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Handmade Stories</h4>
              <p className={styles.trustDesc}>Earthy materials and mirror inlays that tell a story, designed to be cherished for years.</p>
            </div>
          </div>
          <div className={`${styles.trustItem} reveal-on-scroll`}>
            <span className={styles.trustIcon}>🔒</span>
            <div className={styles.trustText}>
              <h4 className={styles.trustTitle}>Secured UPI & Cards</h4>
              <p className={styles.trustDesc}>Instant payments via Razorpay sandboxed gateway framework for easy order booking.</p>
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
            <Link to="/shop" className={styles.sectionLink}>
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
              <Link to="/our-craft" className={styles.primaryBtn}>
                Learn More About Our Craft
              </Link>
            </div>
          </div>

          <div className={`${styles.processImageContainer} reveal-on-scroll`}>
            <img
              src="/images/products/jharokha_mirror.jpg"
              alt="Artisan woodcarving detail on Jharokha"
              style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
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
              <Interactive3DTilt key={post.id} className={styles.instaCardTilt} maxTilt={10} scale={1.05}>
                <div className={styles.instaCard}>
                  <img
                    src={post.image}
                    alt={`Murli Creations Instagram post ${post.id}`}
                    style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
                  />
                  <div className={styles.instaOverlay}>
                    <span>❤️ {post.likes}</span>
                  </div>
                </div>
              </Interactive3DTilt>
            ))}
          </div>

          <div className="reveal-on-scroll">
            <Link to="/instagram" className={styles.instaLinkBtn}>
              Visit Gallery Feed &rarr;
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
