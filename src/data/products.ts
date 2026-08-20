export interface Product {
  id: string;
  slug: string;
  name: string;
  category: string;
  price: number;
  mrp: number; // Struck-through Market Price
  rating: number; // e.g. 4.9
  reviewsCount: number; // e.g. 38
  artist: string; // Artisan Name
  folklore?: string; // Traditional meaning/background
  images: string[];
  video?: string;
  description: string;
  size: string;
  material: string;
  origin: string;
  stock: number;
  featured?: boolean;
}

// Generate all 36 products matching the 36 cropped Instagram screenshots
export const INITIAL_PRODUCTS: Product[] = Array.from({ length: 36 }).map((_, idx) => {
  const index = idx + 1;
  const id = `prod-${index}`;
  
  // Set default values based on index modulo to distribute categories and metadata
  let category = "Lippan Art";
  let name = `Royal Kutchi Lippan Mural Panel (Design #${index})`;
  let price = 2499 + (idx % 6) * 300;
  let mrp = Math.round(price * 1.3);
  let artist = "Master Craftsman Murli Prasad";
  let size = "16 x 16 inches";
  let material = "Waterproof Mud-Clay Paste, Inlay Mirror Glass, MDF Board base";
  let folklore = "Lippan Kaam is a traditional clay-relief craft from Kutch. The circular mirrors are hand-embedded to reflect negative energies and bring light and prosperity into the home entryway.";
  let description = "Add traditional Kutchi elegance to your wall with this handcrafted clay-relief Lippan panel. Features intricate linear clay coils, floral motifs, and geometric borders decorated with sparkling mirrors. Varnish-coated for dust resistance.";
  let stock = 5 + (idx % 8);
  let featured = idx % 5 === 0;

  if (idx % 4 === 1) {
    category = "Jharokhas & Frames";
    name = `Haveli Arch Hand-Carved Wooden Jharokha (Design #${index})`;
    price = 4499 + (idx % 4) * 500;
    mrp = Math.round(price * 1.25);
    artist = "Suresh Jangra (Master Woodcarver)";
    size = "18 x 26 inches";
    material = "Seasoned Mango Wood, Antique Patina, Lead-Free Mirror Glass";
    folklore = "Jharokha window frames are inspired by Haryanvi and Rajasthani haveli balconies. Hanging a Jharokha mirror invites a royal heritage charm, opening a symbolic window of prosperity in your space.";
    description = "Inspired by heritage fort windows, this solid mango wood Jharokha features hand-chiselled floral carvings and a gorgeous distressed finish. Encloses a premium reflection mirror.";
  } else if (idx % 4 === 2) {
    category = "Wall Plates & Mandalas";
    name = `Sunburst Concentric Hand-Painted Mandala Plate (Design #${index})`;
    price = 1299 + (idx % 5) * 200;
    mrp = Math.round(price * 1.35);
    artist = "Suman Murli (Mandala Specialist)";
    size = "12 inches diameter";
    material = "Premium Pinewood Plate, Fine Brush Acrylics, Double Gloss Lacquer";
    folklore = "The Mandala circle represents wholeness, unity, and cosmic harmony in Indian philosophy. The dot patterns represent the centered solar energy of life, optimism, and warmth.";
    description = "A beautiful circular wall plate hand-painted using micro-fine brushes. Features Concentric geometric dots, petals, and rings. Protected with high-gloss double coat varnish.";
  } else if (idx % 4 === 3) {
    category = "Shyam Baba Decoration";
    name = `Shyam Baba Devotional Shringar Dress Set (Design #${index})`;
    price = 799 + (idx % 7) * 150;
    mrp = Math.round(price * 1.4);
    artist = "Suman Murli (Devotional Designer)";
    size = "Idol height 9-12 inches";
    material = "Embroidered Velvet, Silk thread borders, Genuine Peacock Feathers";
    folklore = "Devotional Poshak dresses are created as an offering of faith and love. Adorning the Baba with hand-sewn peacock garments brings peace, success, and protection to the home temple.";
    description = "A premium devotional poshak dress set created for Khatu Shyam Baba. Hand-embroidered with gold zari threads and detailed with authentic peacock feathers and pearls.";
  }

  // Create a 3-image gallery loop using subsequent posts to keep thumbnails rich
  const img1 = `/images/instagram/ig_post_${index}.png`;
  const img2 = `/images/instagram/ig_post_${(index % 36) + 1}.png`;
  const img3 = `/images/instagram/ig_post_${((index + 1) % 36) + 1}.png`;

  const slug = name
    .toLowerCase()
    .replace(/[^a-z0-9 ]/g, "")
    .replace(/\s+/g, "-");

  // Keep rich folklore and descriptions for the first 8 flagship products
  if (index === 1) {
    return {
      id: "prod-1",
      slug: "tree-of-life-lippan-art",
      name: "Tree of Life Lippan Art Panel",
      category: "Lippan Art",
      price: 3499,
      mrp: 4999,
      rating: 4.9,
      reviewsCount: 38,
      artist: "Master Craftsman Murli Prasad",
      folklore: "The 'Tree of Life' (Kalpavriksha) is a mythological, wish-fulfilling tree in Indian culture. It symbolizes the connection between all forms of creation, representing grounding, growth, and eternal prosperity. The circular mirrors are placed to reflect evil spirits and bring positive energy into the entryway.",
      images: [img1, img2, img3],
      video: "https://www.w3schools.com/html/mov_bbb.mp4",
      description: "Bring the spiritual elegance of traditional Kutchi Lippan Kaam to your home. This handcrafted panel features the sacred 'Tree of Life' layout, meticulously sculpted with white clay relief paste and decorated with dozens of sparkling round, diamond, and triangular mirrors. Mounted on a heavy wood panel and finished in a rich, textured Indigo background.",
      size: "18 x 18 inches",
      material: "Clay Relief Paste, Mirror Glass pieces, Premium MDF Board base",
      origin: "Rohtak, Haryana, India",
      stock: 8,
      featured: true
    };
  }

  if (index === 2) {
    return {
      id: "prod-2",
      slug: "vintage-carved-wooden-jharokha",
      name: "Vintage Carved Wooden Jharokha",
      category: "Jharokhas & Frames",
      price: 5499,
      mrp: 6999,
      rating: 4.8,
      reviewsCount: 22,
      artist: "Suresh Jangra (Master Woodcarver)",
      folklore: "Jharokhas are traditional overhanging enclosed balconies used in Rajasthani and Haryanvi havelis. Historically, they served as visual windows for the royal women to observe festivals safely. Today, hanging a Jharokha mirror represents opening a window of heritage and royalty in your home.",
      images: [img1, img2, img3],
      description: "Inspired by Rajasthani and Haryanvi royal architecture, this hand-carved solid wood Jharokha (window style) frame encloses a premium distortion-free mirror. The woodwork is individually hand-chiselled with floral arch motifs and given a beautiful distressed antique patina finish in teal-green and gold leaf overlay.",
      size: "20 x 28 inches",
      material: "Solid Mango Wood, Premium Mirror Glass, Antique Gold patinas",
      origin: "Handcrafted in Rohtak, Haryana",
      stock: 4,
      featured: true
    };
  }

  if (index === 3) {
    return {
      id: "prod-3",
      slug: "designer-shyam-baba-poshak-dress",
      name: "Designer Shyam Baba Poshak & Dress Set",
      category: "Shyam Baba Decoration",
      price: 1299,
      mrp: 1899,
      rating: 4.9,
      reviewsCount: 45,
      artist: "Suman Murli (Devotional Designer)",
      folklore: "Shyam Baba (Khatu Shyam Ji) POSHAK sets are designed as an offering of deep faith, devotion (Bhav), and blessings. Shyam Baba is believed to be the Kaliyug manifestation of Barbarika, who pledged his head to Lord Krishna. Adorning the Baba with handmade dresses brings prosperity and peace to the family.",
      images: [img1, img2, img3],
      description: "A gorgeous, designer poshak set created especially for Khatu Shyam Baba idols. Hand-embroidered with zari borders, sparkling pearl highlights, peacock feather prints, and vibrant velvet panels. Includes the dress, matching patka, and morpankhi tilak overlay details.",
      size: "Idol height 9-12 inches",
      material: "Velvet, Silk threads, Mor Pankh, Pearls",
      origin: "Rohtak, Haryana, India",
      stock: 10,
      featured: true
    };
  }

  if (index === 4) {
    return {
      id: "prod-4",
      slug: "shyam-baba-mor-pankh-jewellery-set",
      name: "Shyam Baba Mor Pankh Jewellery & Mukut",
      category: "Shyam Baba Decoration",
      price: 999,
      mrp: 1499,
      rating: 4.8,
      reviewsCount: 31,
      artist: "Suman Murli (Devotional Designer)",
      folklore: "Mor Pankh (peacock feather) mukuts represent the natural grace and beauty of Krishna and Shyam Baba. Handcrafted jewelry offered at the feet of Baba is believed to absorb spiritual blessings, which protect the home shrine when placed back in the mandir.",
      images: [img1, img2, img3],
      description: "A complete shringar jewellery set for Khatu Shyam Ji. Features a hand-assembled mukut (crown) with genuine peacock feathers, a pearl necklace (haar), earrings, and a tiny decorative flute (bansuri) with golden strings.",
      size: "Standard Mandir Idol Shringar",
      material: "Genuine Peacock Feathers, Gold-plated Alloy, Beads, Pearl chains",
      origin: "Rohtak, Haryana, India",
      stock: 15,
      featured: false
    };
  }

  if (index === 5) {
    return {
      id: "prod-5",
      slug: "festive-diwali-toran-door-decor",
      name: "Festive Diwali Toran & Subh-Labh Set",
      category: "Diwali & Festive Decor",
      price: 799,
      mrp: 1199,
      rating: 4.7,
      reviewsCount: 18,
      artist: "Master Craftsman Murli Prasad",
      folklore: "Torans (door hangings) are traditional entry decorations that welcome Lakshmi, the Hindu goddess of wealth. Featuring mango leaf motifs and customized Subh-Labh indicators (auspicious start and earnings), hanging it at the entrance brings fortune and positive vibes during Diwali.",
      images: [img1, img2, img3],
      description: "Decorate your main door with traditional Haryanvi style. This festive set includes a multi-colored floral Toran hanging with golden bells, and two customized Swastik / Subh-Labh wall plaques decorated with mirror work.",
      size: "Door Width 36 inches",
      material: "Cotton Fabrics, Clay relief plaques, Glass Mirrors, Bells",
      origin: "Rohtak, Haryana, India",
      stock: 20,
      featured: false
    };
  }

  if (index === 6) {
    return {
      id: "prod-6",
      slug: "customised-lippan-home-name-plate",
      name: "Customised Lippan Home Name Plate",
      category: "Customised Creations",
      price: 1999,
      mrp: 2799,
      rating: 4.9,
      reviewsCount: 26,
      artist: "Master Craftsman Murli Prasad",
      folklore: "A home name plate represents the identity and warmth of the family residing within. Customizing it with traditional Kutch Lippan work and mirror circles invites positivity, reflecting away negative energy before anyone steps inside the threshold.",
      images: [img1, img2, img3],
      description: "Personalized home name plate handcrafted with clay relief work and glass mirrors. Once ordered, our design team will contact you to customize the family names, colors, and border patterns. Coated with dust-resistant acrylic varnish.",
      size: "16 x 10 inches",
      material: "Premium MDF, Waterproof Clay Relief, Glass Mirrors",
      origin: "Rohtak, Haryana, India",
      stock: 12,
      featured: true
    };
  }

  if (index === 7) {
    return {
      id: "prod-7",
      slug: "sunburst-hand-painted-mandala-plate",
      name: "Sunburst Hand-Painted Mandala Plate",
      category: "Wall Plates & Mandalas",
      price: 2299,
      mrp: 2999,
      rating: 4.7,
      reviewsCount: 16,
      artist: "Suman Murli (Mandala Specialist)",
      folklore: "The word Mandala means 'circle' in Sanskrit, representing wholeness, unity, and cosmic harmony. The 'Sunburst' mandala pattern acts as a visual anchor for meditation, symbolizing the solar energy of life, optimism, and warmth centered in Haryanvi homes.",
      images: [img1, img2, img3],
      description: "A gorgeous, circular mandala wall plate hand-painted with high-precision fine brushes. Featuring a radiant 'Sunburst' geometric flow of dots, petals, and concentric circles in mustard yellow, terracotta red, and turquoise blue.",
      size: "14 inches diameter",
      material: "Pine Wood Plate, Premium Acrylic Paints, Gloss Varnish Sealant",
      origin: "Rohtak, Haryana, India",
      stock: 9,
      featured: false
    };
  }

  if (index === 8) {
    return {
      id: "prod-8",
      slug: "pooja-swastik-spiritual-lippan-panel",
      name: "Pooja Swastik Spiritual Lippan Panel",
      category: "Lippan Art",
      price: 1599,
      mrp: 2199,
      rating: 4.8,
      reviewsCount: 19,
      artist: "Master Craftsman Murli Prasad",
      folklore: "The Swastika is an ancient symbol of divinity, spirituality, and good luck in Indian culture. Sculpted in white clay with reflective mirror inlays, this panel is ideal for placement on the temple wall, entryway, or during religious ceremonies.",
      images: [img1, img2, img3],
      description: "A beautiful spiritual clay relief panel depicting the holy Swastika. Hand-molded coils, floral motifs, and geometric borders are set on a warm terracotta sandstone base, finished with circular mirrors.",
      size: "12 x 12 inches",
      material: "Clay Relief, MDF Board, Acrylic Paint, Inlay Mirrors",
      origin: "Rohtak, Haryana, India",
      stock: 7,
      featured: false
    };
  }

  return {
    id,
    slug,
    name,
    category,
    price,
    mrp,
    rating: parseFloat((4.5 + (idx % 6) * 0.1).toFixed(1)),
    reviewsCount: 10 + (idx % 20),
    artist,
    folklore,
    images: [img1, img2, img3],
    description,
    size,
    material,
    origin: "Rohtak, Haryana, India",
    stock,
    featured
  };
});
