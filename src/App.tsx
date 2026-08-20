import React from "react";
import { Routes, Route } from "react-router-dom";
import ClientLayout from "./components/ClientLayout";
import Home from "./pages/Home";
import Shop from "./pages/Shop";
import Cart from "./pages/Cart";
import Checkout from "./pages/Checkout";
import Founder from "./pages/Founder";
import OurCraft from "./pages/OurCraft";
import Journal from "./pages/Journal";
import Contact from "./pages/Contact";
import Admin from "./pages/Admin";
import OrderConfirmation from "./pages/OrderConfirmation";
import Product from "./pages/Product";
import NotFound from "./pages/NotFound";

export default function App() {
  return (
    <ClientLayout>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/shop" element={<Shop />} />
        <Route path="/cart" element={<Cart />} />
        <Route path="/checkout" element={<Checkout />} />
        <Route path="/founder-story" element={<Founder />} />
        <Route path="/our-craft" element={<OurCraft />} />
        <Route path="/instagram" element={<Journal />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/admin" element={<Admin />} />
        <Route path="/order-confirmation" element={<OrderConfirmation />} />
        <Route path="/product/:slug" element={<Product />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </ClientLayout>
  );
}
