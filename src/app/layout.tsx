import type { Metadata } from "next";
import "./globals.css";
import ClientLayout from "../components/ClientLayout";

export const metadata: Metadata = {
  title: "Murli Creations | Handcrafted Wall Art & Home Décor Studio",
  description: "Exquisite handcrafted traditional Lippan clay-mirror murals, antique carved wooden Jharokha frames, and painted mandala plates. Handcrafted in Rohtak, Haryana.",
  metadataBase: new URL("https://murlicreations.com"),
  openGraph: {
    title: "Murli Creations | Handcrafted Wall Art & Home Décor",
    description: "Exquisite traditional Lippan art, wooden Jharokhas, and hand-painted plates from Rohtak, Haryana, India.",
    url: "https://murlicreations.com",
    siteName: "Murli Creations",
    locale: "en_IN",
    type: "website",
  },
  alternates: {
    canonical: "/",
  }
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body>
        <ClientLayout>{children}</ClientLayout>
      </body>
    </html>
  );
}
