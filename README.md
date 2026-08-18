# Murli Creations E-commerce Platform

A production-quality, visually stunning, multi-page e-commerce website for **Murli Creations**, a handcrafted wall art and home décor studio in Rohtak, Haryana, India.

This project is built using **Next.js 16 (App Router)**, **TypeScript**, and **Vanilla CSS Modules** for a custom, bespoke design system.

---

## 🎨 Visual Design System & Aesthetics
To reflect the authentic materials of Murli Creations (clay, wood, brass, and mirrors), we have implemented an **"Artisanal Gallery"** design system:
*   **Sandstone & Linen (`#FAF8F5`)**: Soft, organic backgrounds.
*   **Slate Charcoal (`#252C32`)**: High-contrast, elegant typography.
*   **Antique Gold & Brass (`#C39B62`)**: Highlights mirroring gold-leaf gilding.
*   **Indigo Clay (`#2A3D4E`)**: Deep blue details, representing canvas and pottery glaze backings.
*   **Jharokha Arches**: Traditional curved windows styling hero photos and process screens.
*   **Wall elevations**: Elevated drop-shadows that make product cards look like physical wall hangings.

---

## 🚀 How to Run the Project Locally

### 1. Prerequisites
Make sure you have [Node.js](https://nodejs.org) (v18 or higher) installed on your system.

### 2. Install Dependencies
Initialize npm packages:
```bash
npm install
```

### 3. Run Development Server
Start the Next.js dev server:
```bash
npm run dev
```
Open [http://localhost:3000](http://localhost:3000) in your browser to interact with the platform.

### 4. Build for Production
To compile and test static routing correctness:
```bash
npm run build
npm run start
```

---

## 📸 Swapping in Real Product & Instagram Media

### 1. Swapping Store Products
All products are driven by a central typescript catalog.
1. Place your high-resolution product photography in the `public/images/products/` directory.
2. Open [src/data/products.ts](file:///Users/avanishpal/Desktop/Murli%20Creation%20/src/data/products.ts).
3. Update the `INITIAL_PRODUCTS` list with your real names, prices, categories, dimensions, materials, and image file paths:
   ```typescript
   {
     id: "prod-unique-id",
     slug: "your-custom-slug",
     name: "Real Artwork Name",
     category: "Lippan Art", // or 'Jharokhas & Frames' | 'Wall Plates & Mandalas'
     price: 4500, // Price in INR
     images: ["/images/products/your_image.jpg"],
     description: "Write details about clay patterns and curing times...",
     size: "24 x 24 inches",
     material: "Polymer mud clay, glass mirrors, plywood backing",
     origin: "Rohtak, Haryana, India",
     stock: 6
   }
   ```

### 2. Swapping Instagram Feed Photos & Reels
1. Save your exported Instagram post images or video files inside the `public/images/instagram/` directory.
2. Open [src/app/instagram/page.tsx](file:///Users/avanishpal/Desktop/Murli%20Creation%20/src/app/instagram/page.tsx).
3. Find the `posts` array and update the `image` paths to match your assets.
4. Modify the captions, likes, and dates in the code. Your gallery feed will render them dynamically.

---

## 💳 Payments Integration: Razorpay Sandbox to Production

### 1. Sandbox Testing (Enabled by Default)
The checkout screen utilizes client-side Razorpay integrations with a sandbox key. You can complete mock orders using test credit cards or dummy UPI requests.

### 2. Going Live (Securing Server-side Verification)
**WARNING:** Never expose your Razorpay Key Secret on the client-side code. To process secure production payments, you must execute a server-side endpoint.

#### Step A: Server Endpoint Code (Node.js/Next.js API route)
Create a Vercel Serverless Function or Node.js endpoint (e.g. `/api/create-order` and `/api/verify-payment`) using the Razorpay Node SDK:

```javascript
const Razorpay = require('razorpay');
const crypto = require('crypto');

const razorpay = new Razorpay({
  key_id: process.env.RAZORPAY_KEY_ID,
  key_secret: process.env.RAZORPAY_KEY_SECRET,
});

// Endpoint 1: Create order server-side
async function createOrder(req, res) {
  const options = {
    amount: req.body.amount, // in paise
    currency: "INR",
    receipt: "receipt_order_" + Date.now(),
  };
  try {
    const order = await razorpay.orders.create(options);
    res.status(200).json(order);
  } catch (err) {
    res.status(500).send(err);
  }
}

// Endpoint 2: Verify payment signature
function verifyPayment(req, res) {
  const { razorpay_order_id, razorpay_payment_id, razorpay_signature } = req.body;
  const shasum = crypto.createHmac("sha256", process.env.RAZORPAY_KEY_SECRET);
  shasum.update(`${razorpay_order_id}|${razorpay_payment_id}`);
  const digest = shasum.digest("hex");

  if (digest === razorpay_signature) {
    res.status(200).json({ status: "success" });
  } else {
    res.status(400).json({ status: "failed" });
  }
}
```

#### Step B: Hosting Options
You can host these lightweight API routes on:
1.  **Vercel Serverless Functions**: Integrate them directly in Next.js (`src/app/api/...`).
2.  **Render / Railway**: Launch a simple Express.js Node app.
3.  **Supabase / Firebase Edge Functions**.

Update your client checkout keys by setting `process.env.NEXT_PUBLIC_RAZORPAY_KEY_ID` in your host variables.

---

## ⚡ SEO & Keyboard Accessibility
*   **SEO**: Global headers set dynamically using Next.js Metadata API, defining canonical anchors and beautiful Open Graph descriptions.
*   **Semantic HTML**: Proper `<header>`, `<main>`, `<section>`, `<aside>`, and `<footer>` layout tags.
*   **Accessibility**: Inline warnings for invalid forms, outline rings for keyboard focus focus-visible states, and alternate text on all products.
