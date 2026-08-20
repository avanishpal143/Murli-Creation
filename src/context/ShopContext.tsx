import React, { createContext, useContext, useState, useEffect } from "react";
import { Product, INITIAL_PRODUCTS } from "../data/products";

export interface CartItem {
  product: Product;
  quantity: number;
}

export interface Review {
  id: string;
  productId: string;
  userName: string;
  rating: number; // 1-5
  comment: string;
  date: string;
}

export interface Order {
  id: string;
  date: string;
  customer: {
    name: string;
    phone: string;
    email: string;
    address: string;
    city: string;
    state: string;
    pinCode: string;
  };
  items: CartItem[];
  subtotal: number;
  paymentMethod: "Razorpay" | "COD";
  paymentStatus: "Pending" | "Paid" | "Failed";
  orderStatus: "Pending" | "Processing" | "Shipped" | "Delivered" | "Cancelled";
}

interface ToastMessage {
  id: string;
  text: string;
  type: "success" | "error" | "info";
}

interface ShopContextType {
  products: Product[];
  cart: CartItem[];
  orders: Order[];
  reviews: Review[];
  toasts: ToastMessage[];
  addToCart: (product: Product, quantity?: number) => void;
  removeFromCart: (productId: string) => void;
  updateCartQuantity: (productId: string, quantity: number) => void;
  clearCart: () => void;
  addProduct: (product: Omit<Product, "id" | "slug">) => void;
  updateProduct: (product: Product) => void;
  deleteProduct: (productId: string) => void;
  placeOrder: (customer: Order["customer"], paymentMethod: Order["paymentMethod"], paymentStatus: Order["paymentStatus"]) => Order;
  updateOrderStatus: (orderId: string, status: Order["orderStatus"]) => void;
  showToast: (text: string, type?: ToastMessage["type"]) => void;
  removeToast: (id: string) => void;
  addReview: (productId: string, userName: string, rating: number, comment: string) => void;
}

const ShopContext = createContext<ShopContextType | undefined>(undefined);

const INITIAL_REVIEWS: Review[] = [
  {
    id: "rev-1",
    productId: "prod-1",
    userName: "Rahul Sharma",
    rating: 5,
    comment: "Absolutely stunning piece! The mirror layout is mathematically perfect, and the indigo background fits my entryway beautifully. Packing was double-crate wood, very secure.",
    date: "14 Aug 2026"
  },
  {
    id: "rev-2",
    productId: "prod-1",
    userName: "Priya Patel",
    rating: 5,
    comment: "Gorgeous traditional Kutchi art. You can tell it's completely handcrafted from the organic texture of the clay paste. Highly recommend Suman's work!",
    date: "10 Aug 2026"
  },
  {
    id: "rev-3",
    productId: "prod-2",
    userName: "Amit Verma",
    rating: 4,
    comment: "Very heavy solid mango wood window. The distressed teal green and gold finish is gorgeous, feels like a piece from a royal haveli. Took 10 days to deliver, but worth the wait.",
    date: "12 Aug 2026"
  },
  {
    id: "rev-4",
    productId: "prod-2",
    userName: "Ritu Jangra",
    rating: 5,
    comment: "Beautiful mirror frame! Fits my guest room gallery wall perfectly. Suresh Jangra's carving details are masterclass.",
    date: "08 Aug 2026"
  },
  {
    id: "rev-5",
    productId: "prod-3",
    userName: "Meera Bai",
    rating: 5,
    comment: "Bhav and devotion captured beautifully. The velvet fabric is very rich and fitting for Khatu Shyam Baba. Highly satisfied.",
    date: "15 Aug 2026"
  },
  {
    id: "rev-6",
    productId: "prod-3",
    userName: "Krishna Das",
    rating: 5,
    comment: "The zari work is incredibly detailed, and the peacock feather print mukut is highly beautiful. Will buy again for festive occasions.",
    date: "11 Aug 2026"
  },
  {
    id: "rev-7",
    productId: "prod-4",
    userName: "Deepak Kumar",
    rating: 4,
    comment: "Nice shringar set. The peacock feathers are fresh and high quality. Looks gorgeous in my home temple.",
    date: "06 Aug 2026"
  },
  {
    id: "rev-8",
    productId: "prod-5",
    userName: "Sarla Devi",
    rating: 5,
    comment: "Very auspicious Swastik plaques. Decorated my main door for early Diwali setup. Bells have a sweet sound.",
    date: "16 Aug 2026"
  },
  {
    id: "rev-9",
    productId: "prod-6",
    userName: "Sanjay Singhal",
    rating: 5,
    comment: "Perfect custom name plate. Suman contacted us for customization colors and text, and delivered within 12 days. Absolute masterpiece on our entryway!",
    date: "13 Aug 2026"
  },
  {
    id: "rev-10",
    productId: "prod-7",
    userName: "Ananya Roy",
    rating: 5,
    comment: "Concentric dot painting is flawless. Sealed with gloss varnish so it is very easy to dust clean. Adds a warm pop of mustard to my gallery wall.",
    date: "09 Aug 2026"
  },
  {
    id: "rev-11",
    productId: "prod-8",
    userName: "Gaurav Joshi",
    rating: 5,
    comment: "Spiritual Swastik panel sculpted beautifully in clay. Highly auspicious vibes in my pooja room.",
    date: "07 Aug 2026"
  }
];

export function ShopProvider({ children }: { children: React.ReactNode }) {
  // Synchronous initializers to avoid React render race conditions
  const [products, setProducts] = useState<Product[]>(() => {
    try {
      const stored = localStorage.getItem("mc_products");
      if (stored) {
        const loaded: Product[] = JSON.parse(stored);
        if (loaded.length < INITIAL_PRODUCTS.length) {
          // If we expanded the store code database, force reset the local storage to load all 36 items
          localStorage.setItem("mc_products", JSON.stringify(INITIAL_PRODUCTS));
          return INITIAL_PRODUCTS;
        }
        return loaded;
      }
    } catch (e) {
      console.error("Error reading products cache", e);
    }
    return INITIAL_PRODUCTS;
  });

  const [cart, setCart] = useState<CartItem[]>(() => {
    try {
      const stored = localStorage.getItem("mc_cart");
      if (stored) return JSON.parse(stored);
    } catch (e) {
      console.error("Error reading cart cache", e);
    }
    return [];
  });

  const [orders, setOrders] = useState<Order[]>(() => {
    try {
      const stored = localStorage.getItem("mc_orders");
      if (stored) return JSON.parse(stored);
    } catch (e) {
      console.error("Error reading orders cache", e);
    }
    return [];
  });

  const [reviews, setReviews] = useState<Review[]>(() => {
    try {
      const stored = localStorage.getItem("mc_reviews");
      if (stored) return JSON.parse(stored);
    } catch (e) {
      print: console.error("Error reading reviews cache", e);
    }
    return INITIAL_REVIEWS;
  });

  const [toasts, setToasts] = useState<ToastMessage[]>([]);
  const [isLoaded, setIsLoaded] = useState(true);

  // Sync state changes back to localStorage
  useEffect(() => {
    localStorage.setItem("mc_products", JSON.stringify(products));
  }, [products]);

  useEffect(() => {
    localStorage.setItem("mc_cart", JSON.stringify(cart));
  }, [cart]);

  useEffect(() => {
    localStorage.setItem("mc_orders", JSON.stringify(orders));
  }, [orders]);

  useEffect(() => {
    localStorage.setItem("mc_reviews", JSON.stringify(reviews));
  }, [reviews]);

  // Toast notifications manager
  const showToast = (text: string, type: ToastMessage["type"] = "success") => {
    const id = Date.now().toString() + Math.random().toString(36).substr(2, 5);
    setToasts((prev) => [...prev, { id, text, type }]);
    
    // Auto remove after 3.5 seconds
    setTimeout(() => {
      removeToast(id);
    }, 3500);
  };

  const removeToast = (id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id));
  };

  // Cart Functions
  const addToCart = (product: Product, quantity = 1) => {
    setCart((prev) => {
      const existing = prev.find((item) => item.product.id === product.id);
      if (existing) {
        showToast(`Updated quantity of ${product.name} in cart!`, "success");
        return prev.map((item) =>
          item.product.id === product.id
            ? { ...item, quantity: item.quantity + quantity }
            : item
        );
      }
      showToast(`Added ${product.name} to cart!`, "success");
      return [...prev, { product, quantity }];
    });
  };

  const removeFromCart = (productId: string) => {
    const item = cart.find((item) => item.product.id === productId);
    setCart((prev) => prev.filter((item) => item.product.id !== productId));
    if (item) {
      showToast(`Removed ${item.product.name} from cart`, "info");
    }
  };

  const updateCartQuantity = (productId: string, quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    setCart((prev) =>
      prev.map((item) =>
        item.product.id === productId ? { ...item, quantity } : item
      )
    );
  };

  const clearCart = () => {
    setCart([]);
  };

  // Admin Product Operations
  const addProduct = (newProductDetails: Omit<Product, "id" | "slug">) => {
    const slug = newProductDetails.name
      .toLowerCase()
      .replace(/[^a-z0-9 ]/g, "")
      .replace(/\s+/g, "-");
    const id = "prod-" + Date.now();
    const newProduct: Product = {
      ...newProductDetails,
      id,
      slug
    };

    setProducts((prev) => [newProduct, ...prev]);
    showToast(`Successfully added product "${newProduct.name}"`, "success");
  };

  const updateProduct = (updatedProduct: Product) => {
    setProducts((prev) =>
      prev.map((p) => (p.id === updatedProduct.id ? updatedProduct : p))
    );
    showToast(`Successfully updated product "${updatedProduct.name}"`, "success");
  };

  const deleteProduct = (productId: string) => {
    const target = products.find((p) => p.id === productId);
    setProducts((prev) => prev.filter((p) => p.id !== productId));
    if (target) {
      showToast(`Deleted product "${target.name}"`, "info");
    }
  };

  // Add Comment/Review
  const addReview = (productId: string, userName: string, rating: number, comment: string) => {
    const newReview: Review = {
      id: "rev-" + Date.now(),
      productId,
      userName,
      rating,
      comment,
      date: new Date().toLocaleDateString("en-IN", {
        day: "2-digit",
        month: "short",
        year: "numeric"
      })
    };

    setReviews((prev) => [newReview, ...prev]);

    // Recalculate average stars and count
    setProducts((prevProducts) =>
      prevProducts.map((p) => {
        if (p.id === productId) {
          const currentReviewsCount = p.reviewsCount || 0;
          const currentRating = p.rating || 4.8;
          const newCount = currentReviewsCount + 1;
          const newRating = parseFloat((((currentRating * currentReviewsCount) + rating) / newCount).toFixed(1));
          return {
            ...p,
            reviewsCount: newCount,
            rating: newRating
          };
        }
        return p;
      })
    );

    showToast("Review submitted successfully!", "success");
  };

  // Checkout and Order Placement
  const placeOrder = (
    customer: Order["customer"],
    paymentMethod: Order["paymentMethod"],
    paymentStatus: Order["paymentStatus"]
  ): Order => {
    const subtotal = cart.reduce(
      (sum, item) => sum + item.product.price * item.quantity,
      0
    );
    const newOrder: Order = {
      id: "MURLI-" + Date.now().toString().slice(-6) + "-" + Math.floor(100 + Math.random() * 900),
      date: new Date().toLocaleDateString("en-IN", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }),
      customer,
      items: [...cart],
      subtotal,
      paymentMethod,
      paymentStatus,
      orderStatus: "Pending"
    };

    // Decrement stock levels of ordered items
    setProducts((prevProducts) =>
      prevProducts.map((p) => {
        const cartItem = cart.find((item) => item.product.id === p.id);
        if (cartItem) {
          return { ...p, stock: Math.max(0, p.stock - cartItem.quantity) };
        }
        return p;
      })
    );

    setOrders((prev) => [newOrder, ...prev]);
    clearCart();
    return newOrder;
  };

  const updateOrderStatus = (orderId: string, status: Order["orderStatus"]) => {
    setOrders((prevOrders) => {
      return prevOrders.map((o) => {
        if (o.id === orderId) {
          // Transition to Cancelled: Restore stock
          if (status === "Cancelled" && o.orderStatus !== "Cancelled") {
            setProducts((prevProducts) =>
              prevProducts.map((p) => {
                const orderItem = o.items.find((item) => item.product.id === p.id);
                if (orderItem) {
                  return { ...p, stock: p.stock + orderItem.quantity };
                }
                return p;
              })
            );
            return { ...o, orderStatus: status, paymentStatus: "Failed" };
          }
          // Transition away from Cancelled: Decrement stock
          if (o.orderStatus === "Cancelled" && status !== "Cancelled") {
            setProducts((prevProducts) =>
              prevProducts.map((p) => {
                const orderItem = o.items.find((item) => item.product.id === p.id);
                if (orderItem) {
                  return { ...p, stock: Math.max(0, p.stock - orderItem.quantity) };
                }
                return p;
              })
            );
            return { ...o, orderStatus: status, paymentStatus: o.paymentMethod === "Razorpay" ? "Paid" : "Pending" };
          }
          return { ...o, orderStatus: status };
        }
        return o;
      });
    });
    showToast(`Order status updated to ${status}`, "success");
  };

  return (
    <ShopContext.Provider
      value={{
        products,
        cart,
        orders,
        reviews,
        toasts,
        addToCart,
        removeFromCart,
        updateCartQuantity,
        clearCart,
        addProduct,
        updateProduct,
        deleteProduct,
        placeOrder,
        updateOrderStatus,
        showToast,
        removeToast,
        addReview
      }}
    >
      {children}
    </ShopContext.Provider>
  );
}

export function useShop() {
  const context = useContext(ShopContext);
  if (context === undefined) {
    throw new Error("useShop must be used within a ShopProvider");
  }
  return context;
}
