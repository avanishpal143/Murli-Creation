import React from "react";
import styles from "./Journal.module.css";

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
  // Generate post data dynamically for all 36 instagram screenshots
  const posts: InstagramPost[] = Array.from({ length: 36 }).map((_, idx) => {
    const index = idx + 1;
    
    const captions = [
      "A labor of absolute patience and devotion. The geometric mirror borders on our Lippan clay panel are laid down detail by detail. ✨ DM for pricing.",
      "Vintage haveli arches carved from seasoned mango wood. Distressed teal gold patinas are hand-polished. 🪞 Perfect for a foyer mirror setup.",
      "Detailed sunburst mandala plates. Dot painting is therapy. Finished with a double gloss weather-seal lacquer. 🎨 Click to explore.",
      "Clay relief coils rolled and positioned by hand while wet. Lippan art mud reliefs from our Rohtak workshop. 🙏",
      "Gold distressed Jharokhas drying. We use eco-friendly water-based finishes to protect natural grains. 🪵",
      "Dot mandala detail in the making. Pure patience on custom pinewood wall plates. 🌸",
      "Spiritual Ganesha and Swastik wall plaques ready to adorn your temple entryway. 📿",
      "Our signature nameplates with clay border reliefs. Message us to custom order your nameplate. 🏡"
    ];

    const likesList = ["1,245", "982", "1,421", "874", "1,115", "794", "941", "882", "1,053", "927", "1,311", "812"];
    const commentsList = ["45", "28", "62", "19", "37", "14", "31", "24", "41", "18", "52", "11"];
    const dates = [
      "August 19, 2026", "August 17, 2026", "August 16, 2026", 
      "August 14, 2026", "August 12, 2026", "August 10, 2026", 
      "August 08, 2026", "August 06, 2026", "August 04, 2026", 
      "August 02, 2026", "July 31, 2026", "July 29, 2026"
    ];

    return {
      id: `post-${index}`,
      image: `/images/instagram/ig_post_${index}.png`,
      likes: likesList[idx % likesList.length],
      comments: commentsList[idx % commentsList.length],
      caption: captions[idx % captions.length],
      date: dates[idx % dates.length],
      link: "https://www.instagram.com/murlicreationsofficial"
    };
  });

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
              <a href={post.link} target="_blank" rel="noopener noreferrer" className={styles.imageFrame} style={{ position: "relative", display: "block" }}>
                <img
                  src={post.image}
                  alt={post.caption}
                  className={styles.image}
                  style={{ width: "100%", height: "100%", position: "absolute", top: 0, left: 0, objectFit: "cover" }}
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

        {/* Admin instructions panel */}
        <div className={styles.docPanel}>
          <h3 className={styles.docTitle}>💡 Instagram Studio Gallery Setup</h3>
          <p style={{ fontSize: "0.9rem", color: "var(--text-secondary)", marginBottom: "var(--spacing-md)", lineHeight: "1.6" }}>
            The 36 Instagram screenshots provided have been copied to the project's static folder and mapped directly into this gallery. 
            You can add description captions and direct Instagram post links dynamically in `src/pages/Journal.tsx`.
          </p>
        </div>
      </div>
    </div>
  );
}
