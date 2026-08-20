import React from "react";
import { Link } from "react-router-dom";
import styles from "./Founder.module.css";

export default function FounderStoryPage() {
  return (
    <div className={styles.storyPage}>
      {/* 1. Header Banner */}
      <section className={styles.hero}>
        <div className={`container ${styles.heroGrid}`}>
          <div className={`${styles.heroContent} reveal-on-scroll`}>
            <span className={styles.playfulBadge}>From a Mother’s Hands to Murli Creations</span>
            <h2 className={styles.mainTitle}>OUR STORY</h2>
            <p className={styles.leadParagraph}>
              Every creation has a story. Ours began with a mother, a daughter, and a deep love for handmade art.
            </p>
          </div>
          <div className={`${styles.heroImageFrame} reveal-on-scroll`} style={{ position: "relative" }}>
            <img
              src="/images/brand/SM DP.png"
              alt="Murli Creations Brand Profile"
              style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
            />
          </div>
        </div>
      </section>

      {/* 2. Quote Moment */}
      <section className={styles.quoteSection}>
        <div className="container">
          <div className={`${styles.quoteBox} reveal-on-scroll`}>
            <p className={styles.playfulQuote}>
              &ldquo;What started with watching my mother create with her hands became my own journey of creating with love.&rdquo;
            </p>
            <span className={styles.quoteAuthor}>— Suman Murli, Founder</span>
          </div>
        </div>
      </section>

      {/* 3. Story Breakdown Blocks */}
      <section className={styles.narrative}>
        <div className={`container ${styles.narrativeGrid}`}>
          {/* Block 1 */}
          <div className={`${styles.narrativeText} reveal-on-scroll`}>
            <h3 className={styles.blockTitle}>Where It All Began</h3>
            <p className={styles.paragraph}>
              As a little girl growing up in Haryana, I would often watch my mother beautifully decorate the earthen pots around our home with vibrant colors and transform simple skeins of wool into warm, handmade creations.
            </p>
            <p className={styles.paragraph}>
              Back then, I often wondered: why do things made by hand feel so much more beautiful and special than things manufactured in a factory?
            </p>
            <p className={styles.paragraph}>
              As I grew older, I slowly understood the answer. A handmade creation is never just an object. It carries time, patience, creativity, effort, and a little piece of the soul of the person who made it. No two handmade pieces are ever exactly alike—and that is what makes them special.
            </p>
          </div>
          
          {/* Block 2 */}
          <div className={`${styles.narrativeText} reveal-on-scroll`}>
            <h3 className={styles.blockTitle}>The Thought That Became a Beginning</h3>
            <p className={styles.paragraph}>
              After my mother passed away, those childhood memories stayed close to my heart. I often found myself thinking about the joy she brought into our home through her creativity and simple materials.
            </p>
            <p className={styles.paragraph}>
              One thought kept coming back to me: <em>&ldquo;Why don't I create things with my own hands, just like Maa used to?&rdquo;</em>
            </p>
            <p className={styles.paragraph}>
              That thought was the beginning of my own creative journey. In 2021, I decided to share this art with the world, and that is how <strong>Murli Creations</strong> was born.
            </p>
          </div>

          {/* Block 3: Devotion focus */}
          <div className={`${styles.narrativeBlockFull} reveal-on-scroll`}>
            <div className={styles.devotionFlex}>
              <div className={styles.devotionContent}>
                <span className={styles.playfulBadge}>Mor Pankh & Devotion</span>
                <h3 className={styles.blockTitle}>Inspired by Shyam Baba 🦚</h3>
                <p className={styles.paragraph}>
                  The name <strong>Murli Creations</strong> carries a very special meaning. Its inspiration comes from Khatu Shyam Baba, whose presence has been a constant source of faith and strength in my life.
                </p>
                <p className={styles.paragraph}>
                  I began our studio's work with handmade creations for Khatu Shyam Baba. Some of our very first pieces included custom designer poshak dresses, handmade pearl necklaces, mor pankh chadi, and morpankhi tilaks.
                </p>
                <p className={styles.paragraph}>
                  Soon, people began not only purchasing these pieces for their homes but also offering them with love at the feet of Shyam Baba. Knowing that something created by my hands had become a part of someone's devotion gave my work a meaning far beyond business.
                </p>
              </div>
              <div className={styles.devotionImage}>
                <img
                  src="/images/products/mandala_plate.jpg"
                  alt="Devotional Mandala & Wall Plates"
                  width={400}
                  height={400}
                  style={{ objectFit: "cover", borderRadius: "var(--radius-md)" }}
                />
              </div>
            </div>
          </div>

          {/* Block 4 */}
          <div className={`${styles.narrativeText} reveal-on-scroll`}>
            <h3 className={styles.blockTitle}>From Devotion to Home Décor</h3>
            <p className={styles.paragraph}>
              As more people discovered my work, we started receiving custom requests for temple décor, festive door hangings, and home wall art. This encouraged me to experiment with new designs and materials.
            </p>
            <p className={styles.paragraph}>
              That's when I discovered and fell in love with Kutchi <strong>Lippan Art</strong>. The combination of local mud work, organic clay lines, and reflective mirrors fit our vision perfectly.
            </p>
          </div>

          {/* Block 5 */}
          <div className={`${styles.narrativeText} reveal-on-scroll`}>
            <h3 className={styles.blockTitle}>Our Promise & Future</h3>
            <p className={styles.paragraph}>
              Today, Murli Creations continues to grow from our studio workshop in Rohtak, Haryana. Whether we are preparing designer poshak dresses, painting concentric mandalas, or sculpting large Lippan murals, we want every piece to feel personal.
            </p>
            <p className={styles.playfulHighlight}>
              This is more than a brand. This is my journey. This is my mother's inspiration. This is my devotion. 🦚
            </p>
            <div className={styles.actions}>
              <Link to="/shop" className={styles.shopBtn}>
                Explore Our Collection
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* 4. Pillars of Trust */}
      <section className={styles.brandPromise}>
        <div className="container">
          <h3 className={styles.promiseTitle}>Our Creative Promise</h3>
          <div className={styles.promiseGrid}>
            <div className={styles.promiseCard}>
              <span className={styles.promiseIcon}>✨</span>
              <h4>Handcrafted with Love</h4>
              <p>Every single line of clay, mirror inlay, and thread stitch is placed by hand with care.</p>
            </div>
            <div className={styles.promiseCard}>
              <span className={styles.promiseIcon}>🏡</span>
              <h4>Inspired by Tradition</h4>
              <p>We blend ancient Indian folklore, Khatu Shyam devotion, and rustic Kutchi heritage.</p>
            </div>
            <div className={styles.promiseCard}>
              <span className={styles.promiseIcon}>🦚</span>
              <h4>Created to Be Cherished</h4>
              <p>Designed to invite peace, positive reflections, and warmth into your home mandirs.</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
