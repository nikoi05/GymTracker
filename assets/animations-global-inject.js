(() => {
  // Auto-apply bbai animation classes to common containers
  function apply() {
    if (window.__bbaiAppliedInject) return;
    window.__bbaiAppliedInject = true;

    const selectors = [
      'main',
      '.main-content',
      '.main-container',
      '.grid-main',
      '.grid-bot',
      '.grid-main *',
      '.card',
      '.stat-card',
      '.exercise-card',
      '.workout-card',
      'section',
      'header'
    ];

    // Add bbai-anim to elements in a safe way
    const toMaybeAnimate = new Set();
    selectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => toMaybeAnimate.add(el));
    });

    let i = 0;
    for (const el of toMaybeAnimate) {
      // Avoid animating script/style tags or already animated elements
      if (!(el instanceof HTMLElement)) continue;
      if (el.classList.contains('intro-loader')) continue;

      // Prefer base entry fade
      if (!el.classList.contains('bbai-anim')) {
        el.classList.add('bbai-anim');
      }
      // Staggering
      el.style.setProperty('--bbai-index', String(i % 20));
      i++;
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', apply);
  } else {
    apply();
  }
})();

