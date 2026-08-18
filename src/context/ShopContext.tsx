"use client";

import React, { createContext, useContext, useState, useEffect } from "react";
import { Product, INITIAL_PRODUCTS } from "../data/products";

export interface CartItem {
  product: Product;
  quantity: number;
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
}

const ShopContext = createContext<ShopContextType | undefined>(undefined);

export function ShopProvider({ children }: { children: React.ReactNode }) {
  const [products, setProducts] = useState<Product[]>([]);
  const [cart, setCart] = useState<CartItem[]>([]);
  const [orders, setOrders] = useState<Order[]>([]);
  const [toasts, setToasts] = useState<ToastMessage[]>([]);
  const [isLoaded, setIsLoaded] = useState(false);

  // Load state on mount
  useEffect(() => {
    try {
      const storedProducts = localStorage.getItem("mc_products");
      const storedCart = localStorage.getItem("mc_cart");
      const storedOrders = localStorage.getItem("mc_orders");

      if (storedProducts) {
        const loaded: Product[] = JSON.parse(storedProducts);
        const validated = loaded.map((p) => {
          const match = INITIAL_PRODUCTS.find((init) => init.id === p.id);
          return {
            ...p,
            mrp: p.mrp || match?.mrp || Math.round(p.price * 1.3),
            rating: p.rating || match?.rating || 4.8,
            reviewsCount: p.reviewsCount || match?.reviewsCount || 15,
            artist: p.artist || match?.artist || "Master Craftsman Murli Prasad",
            folklore: p.folklore || match?.folklore || ""
          };
        });
        setProducts(validated);
        localStorage.setItem("mc_products", JSON.stringify(validated));
      } else {
        setProducts(INITIAL_PRODUCTS);
        localStorage.setItem("mc_products", JSON.stringify(INITIAL_PRODUCTS));
      }

      if (storedCart) {
        setCart(JSON.parse(storedCart));
      }

      if (storedOrders) {
        setOrders(JSON.parse(storedOrders));
      }
    } catch (e) {
      console.error("Error accessing localStorage", e);
      setProducts(INITIAL_PRODUCTS);
    }
    setIsLoaded(true);
  }, []);

  // Save states to localStorage when updated
  useEffect(() => {
    if (isLoaded) {
      localStorage.setItem("mc_products", JSON.stringify(products));
    }
  }, [products, isLoaded]);

  useEffect(() => {
    if (isLoaded) {
      localStorage.setItem("mc_cart", JSON.stringify(cart));
    }
  }, [cart, isLoaded]);

  useEffect(() => {
    if (isLoaded) {
      localStorage.setItem("mc_orders", JSON.stringify(orders));
    }
  }, [orders, isLoaded]);

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
        removeToast
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
