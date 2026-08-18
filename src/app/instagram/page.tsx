import React from "react";
import Image from "next/image";
import styles from "./page.module.css";

interface InstagramPost {
  id: string;
  image: string;
  likes: string;
  comments: string;
  caption: string;
  date: string;
  link: string;
}

export default function InstagramPage() {
  const posts: InstagramPost[] = [
    {
      id: "post-1",
      image: "/images/products/lippan_tree.jpg",
      likes: "1,245",
      comments: "45",
      caption: "Crafting detail on our latest 'Tree of Life' Lippan panel! The Mud coils are rolled by hand and laid down while wet to seal in the mirrors. Dispatches to Mumbai tomorrow. ✨ DM for custom backings.",
      date: "August 12, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    },
    {
      id: "post-2",
      image: "/images/products/jharokha_mirror.jpg",
      likes: "982",
      comments: "28",
      caption: "Vintage arches and distressed patina. Our carved Jharokha mirrors are inspired by Rajasthani and old Haryanvi haveli windows. Individually chisel-finished by native woodworkers. 🪞",
      date: "August 10, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    },
    {
      id: "post-3",
      image: "/images/products/mandala_plate.jpg",
      likes: "1,421",
      comments: "62",
      caption: "Sunburst dot-mandala in the making. Precision paint brush work on seasoned pine plates. Sealed with high-gloss double coat varnish to withstand dust and moisture. Perfect accent for gallery walls! 🎨",
      date: "August 07, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    },
    {
      id: "post-4",
      image: "/images/products/lippan_tree.jpg",
      likes: "874",
      comments: "19",
      caption: "Symmetry is therapy. Pressing geometric diamond mirrors into organic polymer-reinforced mud relief coils. A labor of patience from our Rohtak studio. 🙏",
      date: "August 04, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    },
    {
      id: "post-5",
      image: "/images/products/jharokha_mirror.jpg",
      likes: "1,115",
      comments: "37",
      caption: "Studio Setup: Carved Jharokhas drying after their final gold patina coating. We use eco-friendly water-based patinas to preserve natural wood grains. 🪵",
      date: "August 01, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    },
    {
      id: "post-6",
      image: "/images/products/mandala_plate.jpg",
      likes: "794",
      comments: "14",
      caption: "Customer Corner: Our 'Sunburst' wall plate hanging in a modern apartment in Gurgaon. We love seeing how our traditional crafts fit modern layouts. Send us your wall photos! 🏡",
      date: "July 28, 2026",
      link: "https://www.instagram.com/murlicreationsofficial"
    }
  ];

  return (
    <div className={styles.instaPage}>
      <div className="container">
        {/* Header */}
        <div className={styles.header}>
          <span className={styles.badge}>Live Journal</span>
          <h2 className={styles.title}>The Studio Gallery</h2>
          <p className={styles.subtitle}>
            A live-styled feed documenting our creation steps, customer setups, and design drafts directly from our workshop.
          </p>
          <a
            href="https://www.instagram.com/murlicreationsofficial"
            target="_blank"
            rel="noopener noreferrer"
            className={styles.instaLink}
          >
            Follow @murlicreationsofficial
          </a>
        </div>

        {/* Photo Grid */}
        <div className={styles.grid}>
          {posts.map((post) => (
            <div key={post.id} className={styles.card}>
              <a href={post.link} target="_blank" rel="noopener noreferrer" className={styles.imageFrame}>
                <Image
                  src={post.image}
                  alt={post.caption}
                  fill
                  sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
                  className={styles.image}
                />
                <div className={styles.overlay}>
                  <span>❤️ {post.likes}</span>
                  <span>💬 {post.comments}</span>
                </div>
              </a>
              <div className={styles.meta}>
                <p className={styles.caption}>{post.caption}</p>
              </div>
              <div className={styles.cardFooter}>
                <span>{post.date}</span>
                <a href={post.link} target="_blank" rel="noopener noreferrer" className={styles.viewBtn}>
                  View on Instagram
                </a>
              </div>
            </div>
          ))}
        </div>

        {/* Admin media instructions */}
        <div className={styles.docPanel}>
          <h3 className={styles.docTitle}>💡 How to Swap in Your Real Instagram Media</h3>
          <p style={{ fontSize: "0.9rem", color: "var(--text-secondary)", marginBottom: "var(--spacing-md)", lineHeight: "1.6" }}>
            To show your live Instagram photography and Reels:
          </p>
          <ol className={styles.docList}>
            <li>Export your photos or reels from your phone or using Instagram Creator Studio.</li>
            <li>Place the files inside the `public/images/instagram/` folder of this project.</li>
            <li>Open the file `src/app/instagram/page.tsx` and find the `posts` array.</li>
            <li>Update the `image` paths to point to your new files (e.g., `"/images/instagram/my_post.jpg"`).</li>
            <li>Modify the `caption`, `likes`, `comments`, and `date` fields in the code to match your live Instagram post.</li>
          </ol>
        </div>
      </div>
    </div>
  );
}
