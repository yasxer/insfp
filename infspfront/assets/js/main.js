/* ═══════════════════════════════════
   INFSP – main.js
   Navbar scroll, hamburger, counters,
   scroll reveal, play button
═══════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── Navbar: become solid on scroll ── */
  const navbar = document.getElementById('navbar');

  const handleScroll = () => {
    if (window.scrollY > 60) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });

  /* ── Hamburger menu ── */
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.getElementById('nav-links');

  hamburger.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    hamburger.setAttribute('aria-expanded', isOpen);
  });

  // Close mobile menu when a link is clicked
  navLinks.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
    });
  });

  /* ── Scroll-reveal (Intersection Observer) ── */
  const revealEls = document.querySelectorAll(
    '.faculty-card, .about-grid, .stat-item, .footer-col'
  );

  revealEls.forEach((el, i) => {
    el.classList.add('reveal');
    // stagger cards in the grid
    const delay = (i % 3) * 0.1;
    el.style.transitionDelay = `${delay}s`;
  });

  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

  /* ── Animated stat counters ── */
  const statNumbers = document.querySelectorAll('.stat-number');

  const animateCounter = (el) => {
    const target  = parseInt(el.dataset.target, 10);
    const suffix  = el.dataset.suffix || '';
    const duration = 1800;
    const step     = 16;
    const steps    = Math.round(duration / step);
    let   current  = 0;

    const timer = setInterval(() => {
      current++;
      const value = Math.round(easeOut(current / steps) * target);
      el.textContent = value + suffix;
      if (current >= steps) {
        el.textContent = target + suffix;
        clearInterval(timer);
      }
    }, step);
  };

  const easeOut = (t) => 1 - Math.pow(1 - t, 3);

  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          statsObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.5 }
  );

  statNumbers.forEach(el => statsObserver.observe(el));

  /* ── Play button (video placeholder) ── */
  const playBtn = document.getElementById('play-btn');

  if (playBtn) {
    playBtn.addEventListener('click', () => {
      const wrap = playBtn.closest('.video-thumbnail');

      // Replace with YouTube embed (update src with real video ID)
      const iframe = document.createElement('iframe');
      iframe.src    = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0';
      iframe.allow  = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
      iframe.allowFullscreen = true;
      iframe.style.cssText   = 'width:100%;height:100%;border:0;border-radius:var(--radius-md);';

      wrap.innerHTML = '';
      wrap.appendChild(iframe);
    });
  }

  /* ── Newsletter form ── */
  const newsletterForm = document.getElementById('newsletter-form');

  if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = document.getElementById('newsletter-email');
      if (!input.value || !input.value.includes('@')) {
        input.style.borderColor = '#ef4444';
        return;
      }
      const btn = document.getElementById('newsletter-submit');
      btn.textContent = '✓ Inscrit !';
      btn.style.background = '#22c55e';
      btn.style.borderColor = '#22c55e';
      input.value = '';
      setTimeout(() => {
        btn.textContent = 'S\'abonner';
        btn.style.background = '';
        btn.style.borderColor = '';
      }, 3000);
    });
  }

});
