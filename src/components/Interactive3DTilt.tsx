"use client";

import React, { useRef, ReactNode } from "react";

interface Interactive3DTiltProps {
  children: ReactNode;
  maxTilt?: number; // max tilt in degrees, default 12
  scale?: number; // scale on hover, default 1.02
  className?: string;
}

export default function Interactive3DTilt({
  children,
  maxTilt = 12,
  scale = 1.02,
  className = "",
}: Interactive3DTiltProps) {
  const containerRef = useRef<HTMLDivElement>(null);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const container = containerRef.current;
    if (!container) return;

    const rect = container.getBoundingClientRect();
    const width = rect.width;
    const height = rect.height;

    // Mouse coordinates relative to the element's top-left corner
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    // Normalise coordinates from -0.5 to 0.5
    const normalizedX = (mouseX / width) - 0.5;
    const normalizedY = (mouseY / height) - 0.5;

    // Calculate rotation angles
    const rotateX = -normalizedY * maxTilt;
    const rotateY = normalizedX * maxTilt;

    // Apply styles directly to the DOM for max performance (60fps lag-free)
    container.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${scale}, ${scale}, ${scale})`;
    
    // Calculate light reflection position for glass/glare shimmer effect
    const glareX = (mouseX / width) * 100;
    const glareY = (mouseY / height) * 100;
    container.style.setProperty("--glare-x", `${glareX}%`);
    container.style.setProperty("--glare-y", `${glareY}%`);
    container.style.setProperty("--glare-opacity", "0.2");
  };

  const handleMouseEnter = () => {
    const container = containerRef.current;
    if (!container) return;
    container.style.transition = "transform 0.05s ease-out, box-shadow 0.3s ease";
  };

  const handleMouseLeave = () => {
    const container = containerRef.current;
    if (!container) return;
    // Reset rotations smoothly
    container.style.transition = "transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.5s ease";
    container.style.transform = "perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)";
    container.style.setProperty("--glare-opacity", "0");
  };

  return (
    <div
      ref={containerRef}
      onMouseMove={handleMouseMove}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      className={className}
      style={{
        position: "relative",
        transformStyle: "preserve-3d",
        transition: "transform 0.3s ease, box-shadow 0.3s ease",
      }}
    >
      {/* Glare/Shimmer Reflection Overlay */}
      <div
        style={{
          position: "absolute",
          top: 0,
          left: 0,
          width: "100%",
          height: "100%",
          background: "radial-gradient(circle at var(--glare-x, 50%) var(--glare-y, 50%), rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 70%)",
          opacity: "var(--glare-opacity, 0)",
          pointerEvents: "none",
          zIndex: 5,
          transition: "opacity 0.3s ease",
          borderRadius: "inherit",
        }}
      />
      {children}
    </div>
  );
}
