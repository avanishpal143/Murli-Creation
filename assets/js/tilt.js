// 60fps Interactive 3D Tilt Effect with Specular Glare Shimmer

document.addEventListener('DOMContentLoaded', () => {
  init3DTilt();
});

function init3DTilt() {
  const tiltElements = document.querySelectorAll('[data-tilt]');
  
  tiltElements.forEach((element) => {
    const maxTilt = parseFloat(element.getAttribute('data-tilt-max') || '10');
    const scale = parseFloat(element.getAttribute('data-tilt-scale') || '1.02');
    
    // Ensure relative positioning and 3D transform style
    element.style.position = 'relative';
    element.style.transformStyle = 'preserve-3d';
    element.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';

    // Add Glare/Shimmer Reflection Overlay if not present
    let glare = element.querySelector('.tilt-glare-overlay');
    if (!glare) {
      glare = document.createElement('div');
      glare.className = 'tilt-glare-overlay';
      glare.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        opacity: 0;
        pointer-events: none;
        z-index: 5;
        transition: opacity 0.3s ease;
        border-radius: inherit;
      `;
      element.appendChild(glare);
    }

    element.addEventListener('mousemove', (e) => {
      const rect = element.getBoundingClientRect();
      const width = rect.width;
      const height = rect.height;

      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;

      const normalizedX = (mouseX / width) - 0.5;
      const normalizedY = (mouseY / height) - 0.5;

      const rotateX = -normalizedY * maxTilt;
      const rotateY = normalizedX * maxTilt;

      element.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${scale}, ${scale}, ${scale})`;

      const glareX = (mouseX / width) * 100;
      const glareY = (mouseY / height) * 100;
      glare.style.background = `radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 70%)`;
      glare.style.opacity = '0.2';
    });

    element.addEventListener('mouseenter', () => {
      element.style.transition = 'transform 0.05s ease-out, box-shadow 0.3s ease';
    });

    element.addEventListener('mouseleave', () => {
      element.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.5s ease';
      element.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
      glare.style.opacity = '0';
    });
  });
}
