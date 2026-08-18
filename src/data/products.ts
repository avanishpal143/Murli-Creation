export interface Product {
  id: string;
  slug: string;
  name: string;
  category: string;
  price: number;
  mrp: number; // Struck-through Market Price
  rating: number; // e.g. 4.8
  reviewsCount: number; // e.g. 24
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

export const INITIAL_PRODUCTS: Product[] = [
  {
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
    images: ["/images/products/lippan_tree.jpg", "/images/products/mandala_plate.jpg"],
    video: "https://www.w3schools.com/html/mov_bbb.mp4",
    description: "Bring the spiritual elegance of traditional Kutchi Lippan Kaam to your home. This handcrafted panel features the sacred 'Tree of Life' layout, meticulously sculpted with white clay relief paste and decorated with dozens of sparkling round, diamond, and triangular mirrors. Mounted on a heavy wood panel and finished in a rich, textured Indigo background.",
    size: "18 x 18 inches",
    material: "Clay Relief Paste, Mirror Glass pieces, Premium MDF Board base",
    origin: "Rohtak, Haryana, India",
    stock: 8,
    featured: true
  },
  {
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
    images: ["/images/products/jharokha_mirror.jpg", "/images/products/lippan_tree.jpg"],
    description: "Inspired by Rajasthani and Haryanvi royal architecture, this hand-carved solid wood Jharokha (window style) frame encloses a premium distortion-free mirror. The woodwork is individually hand-chiselled with floral arch motifs and given a beautiful distressed antique patina finish in teal-green and gold leaf overlay.",
    size: "20 x 28 inches",
    material: "Solid Mango Wood, Premium Mirror Glass, Antique Gold patinas",
    origin: "Handcrafted in Rohtak, Haryana",
    stock: 4,
    featured: true
  },
  {
    id: "prod-3",
    slug: "sunburst-hand-painted-mandala-plate",
    name: "Sunburst Hand-Painted Mandala Plate",
    category: "Wall Plates & Mandalas",
    price: 2299,
    mrp: 2999,
    rating: 4.7,
    reviewsCount: 16,
    artist: "Suman Murli (Mandala Specialist)",
    folklore: "The word Mandala means 'circle' in Sanskrit, representing wholeness, unity, and cosmic harmony. The 'Sunburst' mandala pattern acts as a visual anchor for meditation, symbolizing the solar energy of life, optimism, and warmth centered in Haryanvi homes.",
    images: ["/images/products/mandala_plate.jpg", "/images/products/jharokha_mirror.jpg"],
    description: "A gorgeous, circular mandala artwork hand-painted with high-precision fine brushes. Featuring a radiant 'Sunburst' geometric flow of dots, petals, and concentric circles in mustard yellow, terracotta red, and turquoise blue. Coated with a dust-resistant protective gloss varnish.",
    size: "14 inches diameter",
    material: "Pine Wood Plate, Premium Acrylic Paints, Gloss Varnish Sealant",
    origin: "Rohtak, Haryana, India",
    stock: 9,
    featured: true
  },
  {
    id: "prod-4",
    slug: "royal-peacock-lippan-art",
    name: "Royal Peacock Lippan Art Panel",
    category: "Lippan Art",
    price: 4299,
    mrp: 5499,
    rating: 4.9,
    reviewsCount: 29,
    artist: "Master Craftsman Murli Prasad",
    folklore: "The Peacock (Mayura) represents beauty, grace, and royal divinity in Indian folklore. Associated with rain and harvests, placing dual peacock relief motifs at home is believed to bring fortune, joy, and spiritual protection.",
    images: ["/images/products/lippan_tree.jpg", "/images/products/mandala_plate.jpg"],
    description: "A majestic dual peacock representation in traditional Lippan clay work. Circular geometry with intricate borders made of clay coils and triangular mirror inlays. Adds a warm, traditional festive charm to living rooms, entryways, or mandir rooms.",
    size: "24 x 24 inches",
    material: "Clay Relief, Round and Triangular Mirrors, Heavy MDF Board",
    origin: "Rohtak, Haryana, India",
    stock: 5,
    featured: false
  },
  {
    id: "prod-5",
    slug: "antique-floral-wooden-wall-bracket",
    name: "Antique Floral Wooden Wall Bracket",
    category: "Jharokhas & Frames",
    price: 1899,
    mrp: 2499,
    rating: 4.6,
    reviewsCount: 14,
    artist: "Suresh Jangra (Master Woodcarver)",
    folklore: "In ancient Indian temple architecture, brackets (Madalikas) were carved to support heavy beams, often depicting celestial dancers or floral creepers. These wall brackets bring that structural grandeur into your living rooms to display oil lamps or planters.",
    images: ["/images/products/jharokha_mirror.jpg"],
    description: "Add a touch of heritage to your walls. A pair of solid wooden brackets featuring classical Indian floral carvings and scrollwork. Perfect for placing small planters, brass oil lamps, or collectibles. Easy to mount with integrated keyhole hangers.",
    size: "10 x 8 inches",
    material: "Teak Wood, Matte Sheen Polish",
    origin: "Rohtak, Haryana, India",
    stock: 12,
    featured: false
  },
  {
    id: "prod-6",
    slug: "radha-krishna-folk-wall-plate-set",
    name: "Radha Krishna Folk Wall Plate Set",
    category: "Wall Plates & Mandalas",
    price: 3199,
    mrp: 3999,
    rating: 4.8,
    reviewsCount: 18,
    artist: "Suman Murli (Madhubani Artist)",
    folklore: "Radha and Krishna represent the cosmic union of the human soul with divine love in Hindu philosophy. Set under the Kadamba tree, the painting uses traditional folk borders representing the eternal spring of life.",
    images: ["/images/products/mandala_plate.jpg", "/images/products/lippan_tree.jpg"],
    description: "A set of three matching wall plates hand-painted with folk illustrations depicting Radha and Krishna under a tree, surrounded by floral borders. Captures the warmth of traditional Madhubani and Kalighat brush styles.",
    size: "10, 12, and 14 inches",
    material: "Engineered Wood, Acrylic Paints, Protective Varnish",
    origin: "Rohtak, Haryana, India",
    stock: 6,
    featured: false
  }
];
