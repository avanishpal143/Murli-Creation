import React from "react";
import styles from "./OurCraft.module.css";

export default function OurCraftPage() {
  const steps = [
    {
      num: 1,
      title: "Symmetric Mapping & Design Sketching",
      desc: "Before mud touching wood, a mathematically symmetric template (mandalas, floral motifs, peacock panels) is sketched onto the wooden board. This establishes clear guide grids for clay alignment."
    },
    {
      num: 2,
      title: "Clay Sifting & Kneading",
      desc: "Traditional riverbed soil or clay is sifted to extract grit. We blend it with structural fibers and organic binders to build a polymer-like clay that is elastic, crack-free, and resistant to shrinkage."
    },
    {
      num: 3,
      title: "Relief Coiling",
      desc: "Small segments of kneaded clay are rolled by hand against flat boards into uniform cylinders (coils). These coils are placed along the sketched lines, forming raised relief borders."
    },
    {
      num: 4,
      title: "Clay Sculpting & Detailing",
      desc: "Using wood chisels and fingers, details are pinched into the clay coils (leaves, feathers, petals, dots) to produce a 3D structural outline."
    },
    {
      num: 5,
      title: "Geometric Mirror Embedding",
      desc: "While the clay relief borders are wet, glass mirrors (circular, diamond, triangular, and teardrop shapes) are carefully pressed into the mud. They are secured as the mud dries around them."
    },
    {
      num: 6,
      title: "Air Curing & Curing",
      desc: "The panel is rested in a dust-free humidity-controlled drying room for 48 hours. Slow, natural drying ensures that the clay board cures without cracking or wrapping."
    },
    {
      num: 7,
      title: "Earthy Background Painting",
      desc: "We paint the background board in rich traditional colors (Deep Indigo Blue, Ochre Yellow, Forest Green, or Terracotta Red) using matte acrylic paints."
    },
    {
      num: 8,
      title: "White Relief Detailing & Outlining",
      desc: "The dry clay relief lines are painted with pure white color, giving Lippan Kaam its signature high-contrast outline that causes the embedded mirrors to pop."
    },
    {
      num: 9,
      title: "Double Varnish Sealing",
      desc: "A final double coat of protective matte/gloss varnish is sprayed over the panel. This locks out moisture, protects mirrors from fogging, and allows safe dusting for years."
    }
  ];

  return (
    <div className={styles.craftPage}>
      <div className="container">
        {/* Header section */}
        <div className={styles.header}>
          <span className={styles.badge}>Our Studio Story</span>
          <h2 className={styles.title}>The Art of Lippan & Carved Wood</h2>
          <p className={styles.subtitle}>
            A deep-dive into how we convert raw clay, mirrors, and native wood into premium wall decor inside our Rohtak workshop.
          </p>
        </div>

        {/* Brand Story Section */}
        <section className={styles.storySection}>
          <div className={styles.storyContent}>
            <h3 className={styles.storyTitle}>From Rohtak to Your Walls</h3>
            <p className={styles.storyText}>
              Murli Creations was founded in Rohtak, Haryana, with a simple goal: preserving and refining traditional Indian mud-mirror relief art (Lippan Kaam) for modern homes. What started as small clay studies on wooden plates has grown into a collaborative design studio and artisan workshop.
            </p>
            <p className={styles.storyText}>
              By merging traditional Kutchi mud-mirror methods with premium Haryanvi woodworking, we produce decor pieces that are structurally durable, easy to hang, and rich in history. Every piece is made entirely by hand, carrying the minor organic variations of genuine artisan labor.
            </p>
          </div>
          <div className={styles.storyImageContainer} style={{ position: "relative" }}>
            <img
              src="/images/products/lippan_tree.jpg"
              alt="Sculpted Tree of Life detail"
              style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
            />
          </div>
        </section>

        {/* Materials Sourcing Section */}
        <section className={styles.materialsSection}>
          <h3 className={styles.sectionTitle}>Earthy, Locally Sourced Materials</h3>
          <div className={styles.materialsGrid}>
            <div className={styles.materialCard}>
              <div className={styles.materialIcon}>🏺</div>
              <h4 className={styles.materialName}>Reinforced Clay</h4>
              <p className={styles.materialDesc}>
                A blend of local clay, organic fibers, and binders, engineered to resist cracks and remain light on wood.
              </p>
            </div>
            <div className={styles.materialCard}>
              <div className={styles.materialIcon}>🪞</div>
              <h4 className={styles.materialName}>Premium Mirrors</h4>
              <p className={styles.materialDesc}>
                Precision-cut geometric glass mirrors (triangles, diamonds, circles) reflecting light dynamically.
              </p>
            </div>
            <div className={styles.materialCard}>
              <div className={styles.materialIcon}>🪵</div>
              <h4 className={styles.materialName}>Native Hardwoods</h4>
              <p className={styles.materialDesc}>
                Solid Mango, Teak, and Sheesham wood blocks hand-carved and seasoned to resist warping in humid climates.
              </p>
            </div>
            <div className={styles.materialCard}>
              <div className={styles.materialIcon}>🖌️</div>
              <h4 className={styles.materialName}>Fine Varnishes</h4>
              <p className={styles.materialDesc}>
                Vivid water-based acrylic paints sealed with two coats of protective dust-resistant matte varnishes.
              </p>
            </div>
          </div>
        </section>

        {/* Genuine Process Steps */}
        <section className={styles.processSection}>
          <h3 className={styles.sectionTitle} style={{ marginBottom: "var(--spacing-md)" }}>
            Our 9-Step Artisan Sequence
          </h3>
          <p className={styles.subtitle} style={{ textAlign: "center", marginBottom: "var(--spacing-xxl)", maxWidth: "600px", margin: "0 auto var(--spacing-xxl) auto" }}>
            Lippan and woodcraft are sequences of patience. Here is our detailed creation workflow:
          </p>
          <div className={styles.processList}>
            {steps.map((step) => (
              <div key={step.num} className={styles.stepCard}>
                <div className={styles.stepNum}>{step.num}</div>
                <div className={styles.stepContent}>
                  <h4 className={styles.stepTitle}>{step.title}</h4>
                  <p className={step.desc}>{step.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </section>

        {/* Artisan Spotlight */}
        <section className={`container ${styles.artisanSection}`}>
          <div className={styles.artisanGrid}>
            <div className={styles.artisanImageContainer} style={{ position: "relative" }}>
              <img
                src="/images/products/jharokha_mirror.jpg"
                alt="Workshop wood carving detail"
                style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
              />
            </div>
            <div className={styles.artisanContent}>
              <h3 className={styles.artisanTitle}>Meet the Woodcarvers & Clay Artists</h3>
              <p className={styles.artisanText}>
                Our studio is run by local clay artists and woodworkers based in Haryana. By creating a sustainable workspace, we support traditional rural crafting techniques and provide fair, stable livelihoods to native craftsmen.
              </p>
              <p className={styles.artisanText}>
                Every Jharokha window is carved by hand by master woodworkers who have inherited woodchiseling for generations. The clay detailing is done by women artists specializing in symmetric patterns, mud extrusion, and mirror setting. When you buy from Murli Creations, you are bringing their handcraft directly into your home.
              </p>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}
