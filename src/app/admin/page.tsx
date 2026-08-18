"use client";

import React, { useState, useEffect } from "react";
import { useShop, Order } from "../../context/ShopContext";
import { Product } from "../../data/products";
import styles from "./page.module.css";

export default function AdminPage() {
  const {
    products,
    orders,
    addProduct,
    updateProduct,
    deleteProduct,
    updateOrderStatus,
    showToast
  } = useShop();

  // Authentication states
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [usernameInput, setUsernameInput] = useState("");
  const [passwordInput, setPasswordInput] = useState("");

  // Active Admin tab: 'dashboard' | 'products' | 'orders'
  const [activeTab, setActiveTab] = useState<"dashboard" | "products" | "orders">("dashboard");

  // Add Product Form States
  const [isAdding, setIsAdding] = useState(false);
  const [name, setName] = useState("");
  const [category, setCategory] = useState("Lippan Art");
  const [price, setPrice] = useState("");
  const [mrp, setMrp] = useState("");
  const [stock, setStock] = useState("");
  const [size, setSize] = useState("18 x 18 inches");
  const [material, setMaterial] = useState("Clay Relief, MDF Board");
  const [origin, setOrigin] = useState("Rohtak, Haryana, India");
  const [description, setDescription] = useState("");
  const [imageUrl, setImageUrl] = useState("/images/products/lippan_tree.jpg");
  const [rating, setRating] = useState("4.8");
  const [reviewsCount, setReviewsCount] = useState("15");
  const [artist, setArtist] = useState("Master Craftsman Murli Prasad");
  const [folklore, setFolklore] = useState("");

  // Edit Product Form States
  const [editingProduct, setEditingProduct] = useState<Product | null>(null);

  // Load auth state on mount
  useEffect(() => {
    const loggedIn = localStorage.getItem("mc_admin_logged_in");
    if (loggedIn === "true") {
      setIsLoggedIn(true);
    }
  }, []);

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (usernameInput === "admin" && passwordInput === "murli@2026") {
      setIsLoggedIn(true);
      localStorage.setItem("mc_admin_logged_in", "true");
      showToast("Access Granted. Welcome back, Administrator.", "success");
      setUsernameInput("");
      setPasswordInput("");
    } else {
      showToast("Incorrect Username or Password.", "error");
    }
  };

  const handleLogout = () => {
    setIsLoggedIn(false);
    localStorage.removeItem("mc_admin_logged_in");
    showToast("Logged out successfully.", "info");
  };

  // Analytics Metrics calculations
  const totalSales = orders.reduce((sum, o) => {
    if (o.orderStatus === "Cancelled") return sum;
    return o.paymentStatus === "Paid" || o.paymentMethod === "COD" ? sum + o.subtotal : sum;
  }, 0);
  const activeOrders = orders.filter((o) => o.orderStatus !== "Cancelled");
  const totalOrdersCount = activeOrders.length;
  const avgOrderValue = totalOrdersCount > 0 ? Math.round(totalSales / totalOrdersCount) : 0;
  const lowStockCount = products.filter((p) => p.stock <= 3).length;

  const handleAddProduct = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || !price || !stock || !description) {
      showToast("Please fill all required fields.", "error");
      return;
    }

    const calculatedMrp = mrp ? Number(mrp) : Math.round(Number(price) * 1.3);

    addProduct({
      name,
      category,
      price: Number(price),
      mrp: calculatedMrp,
      rating: Number(rating) || 4.8,
      reviewsCount: Number(reviewsCount) || 15,
      artist: artist || "Master Craftsman Murli Prasad",
      folklore: folklore || "",
      images: [imageUrl],
      description,
      size,
      material,
      origin,
      stock: Number(stock)
    });

    // Reset Form
    setName("");
    setPrice("");
    setMrp("");
    setStock("");
    setRating("4.8");
    setReviewsCount("15");
    setArtist("Master Craftsman Murli Prasad");
    setFolklore("");
    setDescription("");
    setIsAdding(false);
  };

  const handleUpdateProductSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingProduct) return;

    updateProduct(editingProduct);
    setEditingProduct(null);
  };

  const handleDeleteProduct = (id: string) => {
    if (confirm("Are you sure you want to delete this product from your database? This cannot be undone.")) {
      deleteProduct(id);
    }
  };

  // RENDER LOGIN CARD IF UNAUTHENTICATED
  if (!isLoggedIn) {
    return (
      <div className={styles.loginPage}>
        <div className={styles.loginCard}>
          <div className={styles.loginHeader}>
            <h2 className={styles.loginWordmark}>MURLI</h2>
            <span className={styles.loginTagline}>Creations Admin</span>
          </div>
          <p className={styles.loginSubtitle}>Enter store management portal key.</p>
          <form onSubmit={handleLogin} className={styles.loginForm}>
            <div className={styles.inputGroup}>
              <label className={styles.label}>Username</label>
              <input
                type="text"
                value={usernameInput}
                onChange={(e) => setUsernameInput(e.target.value)}
                className={styles.input}
                placeholder="e.g. admin"
                required
              />
            </div>
            <div className={styles.inputGroup}>
              <label className={styles.label}>Password</label>
              <input
                type="password"
                value={passwordInput}
                onChange={(e) => setPasswordInput(e.target.value)}
                className={styles.input}
                placeholder="••••••••"
                required
              />
            </div>
            <button type="submit" className={styles.loginSubmitBtn}>
              Verify Credentials
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className={styles.adminPage}>
      <div className="container">
        {/* Portal Header with Logout Option */}
        <div className={styles.portalHeader}>
          <h2 className={styles.title}>Murli Creations Portal</h2>
          <button onClick={handleLogout} className={styles.logoutBtn}>
            Log Out Portal
          </button>
        </div>

        {/* Tab Navigation */}
        <div className={styles.tabNav}>
          <button
            onClick={() => { setActiveTab("dashboard"); setEditingProduct(null); }}
            className={`${styles.tabBtn} ${activeTab === "dashboard" ? styles.activeTab : ""}`}
          >
            Metrics Dashboard
          </button>
          <button
            onClick={() => { setActiveTab("products"); setEditingProduct(null); }}
            className={`${styles.tabBtn} ${activeTab === "products" ? styles.activeTab : ""}`}
          >
            Product Catalog ({products.length})
          </button>
          <button
            onClick={() => { setActiveTab("orders"); setEditingProduct(null); }}
            className={`${styles.tabBtn} ${activeTab === "orders" ? styles.activeTab : ""}`}
          >
            Orders Manager ({orders.length})
          </button>
        </div>

        {/* TAB 1: ANALYTICS OVERVIEW */}
        {activeTab === "dashboard" && (
          <div>
            <div className={styles.dashboardGrid}>
              <div className={styles.metricCard}>
                <span className={styles.metricLabel}>Gross Sales</span>
                <div className={styles.metricValue}>₹{totalSales.toLocaleString("en-IN")}</div>
              </div>
              <div className={styles.metricCard}>
                <span className={styles.metricLabel}>Active Orders</span>
                <div className={styles.metricValue}>{totalOrdersCount}</div>
              </div>
              <div className={styles.metricCard}>
                <span className={styles.metricLabel}>Avg. Order Value</span>
                <div className={styles.metricValue}>₹{avgOrderValue.toLocaleString("en-IN")}</div>
              </div>
              <div className={styles.metricCard}>
                <span className={styles.metricLabel}>Low Stock Warns</span>
                <div className={`${styles.metricValue} ${lowStockCount > 0 ? styles.lowStockWarning : ""}`}>
                  {lowStockCount}
                </div>
              </div>
            </div>

            {/* Quick overview of latest orders */}
            <div className={styles.formSection}>
              <h3 className={styles.sectionTitle}>Recent Activity</h3>
              <p style={{ fontSize: "0.9rem", color: "var(--text-secondary)", lineHeight: "1.6" }}>
                Welcome to the administration panel of Murli Creations. Use this dashboard to manage product stocks, verify online payments, mark COD packages as shipped, and edit description sizes for custom murals. Cancelling customer orders will automatically reverse item stock counts.
              </p>
            </div>
          </div>
        )}

        {/* TAB 2: PRODUCT CATALOG MANAGER */}
        {activeTab === "products" && (
          <div>
            {/* ADD PRODUCT FORM */}
            {!isAdding && !editingProduct && (
              <button onClick={() => setIsAdding(true)} className={styles.submitBtn} style={{ marginBottom: "var(--spacing-md)" }}>
                + Add New Art Piece
              </button>
            )}

            {isAdding && (
              <div className={styles.formSection}>
                <div className={styles.sectionHeader}>
                  <h3 className={styles.sectionTitle}>Create New Product Entry</h3>
                  <button onClick={() => setIsAdding(false)} className={styles.cancelBtn}>Cancel</button>
                </div>
                <form onSubmit={handleAddProduct} className={styles.formGrid}>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Product Name *</label>
                    <input type="text" value={name} onChange={(e) => setName(e.target.value)} className={styles.input} required />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Category</label>
                    <select value={category} onChange={(e) => setCategory(e.target.value)} className={styles.select}>
                      <option value="Lippan Art">Lippan Art</option>
                      <option value="Jharokhas & Frames">Jharokhas & Frames</option>
                      <option value="Wall Plates & Mandalas">Wall Plates & Mandalas</option>
                    </select>
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Price (INR) *</label>
                    <input type="number" value={price} onChange={(e) => setPrice(e.target.value)} className={styles.input} required />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>MRP (Strikethrough Price)</label>
                    <input type="number" value={mrp} onChange={(e) => setMrp(e.target.value)} className={styles.input} placeholder="Leave blank to auto-calculate (approx +30%)" />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Initial Stock Count *</label>
                    <input type="number" value={stock} onChange={(e) => setStock(e.target.value)} className={styles.input} required />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Dimensions</label>
                    <input type="text" value={size} onChange={(e) => setSize(e.target.value)} className={styles.input} />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Materials Used</label>
                    <input type="text" value={material} onChange={(e) => setMaterial(e.target.value)} className={styles.input} />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Origin Location</label>
                    <input type="text" value={origin} onChange={(e) => setOrigin(e.target.value)} className={styles.input} />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Artisan / Artist *</label>
                    <input type="text" value={artist} onChange={(e) => setArtist(e.target.value)} className={styles.input} required />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Mock Star Rating (1.0 to 5.0)</label>
                    <input type="text" value={rating} onChange={(e) => setRating(e.target.value)} className={styles.input} />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Rating Reviews Count</label>
                    <input type="text" value={reviewsCount} onChange={(e) => setReviewsCount(e.target.value)} className={styles.input} />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Primary Image URL</label>
                    <input type="text" value={imageUrl} onChange={(e) => setImageUrl(e.target.value)} className={styles.input} />
                  </div>
                  <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                    <label className={styles.label}>History & Symbolism Folklore (Gaatha style)</label>
                    <textarea value={folklore} onChange={(e) => setFolklore(e.target.value)} className={styles.textarea} placeholder="Describe the mythological background or traditional meaning..." />
                  </div>
                  <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                    <label className={styles.label}>Description / Details *</label>
                    <textarea value={description} onChange={(e) => setDescription(e.target.value)} className={styles.textarea} required />
                  </div>
                  <div className={`${styles.actionsRow} ${styles.fullWidth}`}>
                    <button type="submit" className={styles.submitBtn}>Save Product</button>
                    <button type="button" onClick={() => setIsAdding(false)} className={styles.cancelBtn}>Cancel</button>
                  </div>
                </form>
              </div>
            )}

            {/* EDIT PRODUCT FORM */}
            {editingProduct && (
              <div className={styles.formSection}>
                <div className={styles.sectionHeader}>
                  <h3 className={styles.sectionTitle}>Edit Product: {editingProduct.name}</h3>
                  <button onClick={() => setEditingProduct(null)} className={styles.cancelBtn}>Cancel</button>
                </div>
                <form onSubmit={handleUpdateProductSubmit} className={styles.formGrid}>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Product Name *</label>
                    <input
                      type="text"
                      value={editingProduct.name}
                      onChange={(e) => setEditingProduct({ ...editingProduct, name: e.target.value })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Category</label>
                    <select
                      value={editingProduct.category}
                      onChange={(e) => setEditingProduct({ ...editingProduct, category: e.target.value })}
                      className={styles.select}
                    >
                      <option value="Lippan Art">Lippan Art</option>
                      <option value="Jharokhas & Frames">Jharokhas & Frames</option>
                      <option value="Wall Plates & Mandalas">Wall Plates & Mandalas</option>
                    </select>
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Price (INR) *</label>
                    <input
                      type="number"
                      value={editingProduct.price}
                      onChange={(e) => setEditingProduct({ ...editingProduct, price: Number(e.target.value) })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>MRP (Strikethrough Price)</label>
                    <input
                      type="number"
                      value={editingProduct.mrp}
                      onChange={(e) => setEditingProduct({ ...editingProduct, mrp: Number(e.target.value) })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Stock Count *</label>
                    <input
                      type="number"
                      value={editingProduct.stock}
                      onChange={(e) => setEditingProduct({ ...editingProduct, stock: Number(e.target.value) })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Dimensions</label>
                    <input
                      type="text"
                      value={editingProduct.size}
                      onChange={(e) => setEditingProduct({ ...editingProduct, size: e.target.value })}
                      className={styles.input}
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Materials</label>
                    <input
                      type="text"
                      value={editingProduct.material}
                      onChange={(e) => setEditingProduct({ ...editingProduct, material: e.target.value })}
                      className={styles.input}
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Origin Location</label>
                    <input
                      type="text"
                      value={editingProduct.origin}
                      onChange={(e) => setEditingProduct({ ...editingProduct, origin: e.target.value })}
                      className={styles.input}
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Artist / Artisan *</label>
                    <input
                      type="text"
                      value={editingProduct.artist}
                      onChange={(e) => setEditingProduct({ ...editingProduct, artist: e.target.value })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Star Rating</label>
                    <input
                      type="number"
                      step="0.1"
                      value={editingProduct.rating}
                      onChange={(e) => setEditingProduct({ ...editingProduct, rating: Number(e.target.value) })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Reviews Count</label>
                    <input
                      type="number"
                      value={editingProduct.reviewsCount}
                      onChange={(e) => setEditingProduct({ ...editingProduct, reviewsCount: Number(e.target.value) })}
                      className={styles.input}
                      required
                    />
                  </div>
                  <div className={styles.inputGroup}>
                    <label className={styles.label}>Image Path</label>
                    <input
                      type="text"
                      value={editingProduct.images[0] || ""}
                      onChange={(e) => setEditingProduct({ ...editingProduct, images: [e.target.value] })}
                      className={styles.input}
                    />
                  </div>
                  <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                    <label className={styles.label}>History & Symbolism Folklore (Gaatha style)</label>
                    <textarea
                      value={editingProduct.folklore || ""}
                      onChange={(e) => setEditingProduct({ ...editingProduct, folklore: e.target.value })}
                      className={styles.textarea}
                    />
                  </div>
                  <div className={`${styles.inputGroup} ${styles.fullWidth}`}>
                    <label className={styles.label}>Description *</label>
                    <textarea
                      value={editingProduct.description}
                      onChange={(e) => setEditingProduct({ ...editingProduct, description: e.target.value })}
                      className={styles.textarea}
                      required
                    />
                  </div>
                  <div className={`${styles.actionsRow} ${styles.fullWidth}`}>
                    <button type="submit" className={styles.submitBtn}>Update Product</button>
                    <button type="button" onClick={() => setEditingProduct(null)} className={styles.cancelBtn}>Cancel</button>
                  </div>
                </form>
              </div>
            )}

            {/* Products Inventory Table */}
            <div className={styles.tableContainer}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Item ID</th>
                    <th>Name / Category</th>
                    <th>Price / MRP</th>
                    <th>Stock status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {products.map((p) => (
                    <tr key={p.id} className={styles.tableRow}>
                      <td style={{ fontFamily: "monospace", color: "var(--text-muted)" }}>{p.id}</td>
                      <td>
                        <span className={styles.productName}>{p.name}</span>
                        <br />
                        <span className={styles.productCat}>{p.category}</span>
                      </td>
                      <td style={{ fontWeight: "700" }}>
                        ₹{p.price.toLocaleString("en-IN")} 
                        <span style={{ fontSize: "0.75rem", textDecoration: "line-through", color: "var(--text-muted)", marginLeft: "6px" }}>
                          ₹{p.mrp.toLocaleString("en-IN")}
                        </span>
                      </td>
                      <td className={styles.stockCol}>
                        {p.stock === 0 ? (
                          <span className={styles.lowStockText}>Out of stock</span>
                        ) : p.stock <= 3 ? (
                          <span className={styles.lowStockText}>Low Stock ({p.stock})</span>
                        ) : (
                          <span className={styles.inStockText}>In Stock ({p.stock})</span>
                        )}
                      </td>
                      <td className={styles.tableActions}>
                        <button onClick={() => setEditingProduct(p)} className={styles.editBtn}>Edit</button>
                        <button onClick={() => handleDeleteProduct(p.id)} className={styles.deleteBtn}>Delete</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* TAB 3: CUSTOMER ORDER LIST MANAGER */}
        {activeTab === "orders" && (
          <div className={styles.tableContainer}>
            {orders.length === 0 ? (
              <div className={styles.noData}>No customer orders placed yet. Placing test purchases at checkout will register records here.</div>
            ) : (
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Recipient</th>
                    <th>Items Purchased</th>
                    <th>Final Bill</th>
                    <th>Payment Details</th>
                    <th>Order Status</th>
                    <th>Quick Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map((order) => (
                    <tr key={order.id} className={styles.tableRow}>
                      <td className={styles.orderId}>{order.id}</td>
                      <td className={styles.custInfo}>
                        <span className={styles.custName}>{order.customer.name}</span>
                        <span className={styles.custContact}>{order.customer.phone}</span>
                        <span className={styles.custContact}>{order.customer.city}, {order.customer.state}</span>
                      </td>
                      <td>
                        <div className={styles.orderItems}>
                          {order.items.map((item, idx) => (
                            <div key={idx}>
                              {item.quantity}x {item.product.name}
                            </div>
                          ))}
                        </div>
                      </td>
                      <td className={styles.orderTotal}>₹{order.subtotal.toLocaleString("en-IN")}</td>
                      <td>
                        <span className={`${styles.paymentStatusBadge} ${
                          order.paymentStatus === "Paid" ? styles.statusPaid : order.paymentStatus === "Pending" ? styles.statusPending : styles.statusFailed
                        }`}>
                          {order.paymentMethod} - {order.paymentStatus}
                        </span>
                      </td>
                      <td>
                        <select
                          value={order.orderStatus}
                          onChange={(e) => updateOrderStatus(order.id, e.target.value as Order["orderStatus"])}
                          className={styles.statusSelect}
                          aria-label="Change order status"
                        >
                          <option value="Pending">Pending</option>
                          <option value="Processing">Processing</option>
                          <option value="Shipped">Shipped</option>
                          <option value="Delivered">Delivered</option>
                          <option value="Cancelled">Cancelled</option>
                        </select>
                      </td>
                      <td>
                        {order.orderStatus !== "Cancelled" ? (
                          <button
                            onClick={() => {
                              if (confirm(`Cancel Order ${order.id}? This will restore stock levels in inventory.`)) {
                                updateOrderStatus(order.id, "Cancelled");
                              }
                            }}
                            className={styles.cancelOrderBtn}
                          >
                            Cancel
                          </button>
                        ) : (
                          <span style={{ color: "var(--text-muted)", fontSize: "0.8rem", fontWeight: "600" }}>
                            Cancelled
                          </span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
