(() => {
  function initBBaiAnimations() {
    // Avoid duplicate init
    if (window.__bbaiAnimObserverInstalled) return;
    window.__bbaiAnimObserverInstalled = true;

    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const nodes = Array.from(document.querySelectorAll('.bbai-anim, .bbai-slide-left, .bbai-slide-right, .bbai-pop'));
    if (!nodes.length) return;

    if (prefersReduced) {
      nodes.forEach(n => n.classList.add('is-visible'));
      return;
    }

    const io = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        }
      },
      { threshold: 0.12, rootMargin: '0px 0px -10% 0px' }
    );

    nodes.forEach(n => {
      // Ensure initial state
      if (!n.classList.contains('is-visible')) {
        n.style.willChange = 'opacity, transform';
      }
      io.observe(n);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBBaiAnimations);
  } else {
    initBBaiAnimations();
  }
})();

