<template>
  <div class="landing-page">
    <!-- NAVBAR -->
    <header :class="['navbar', { scrolled: isScrolled }]" id="navbar">
      <div class="container nav-inner">
        <a href="#" class="nav-logo" @click.prevent="scrollToTop">
          <div class="logo-icon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="#0A1E3C"/>
              <path d="M8 10h4v12H8zM14 10h4l6 6-6 6h-4l6-6-6-6z" fill="#FFD700"/>
            </svg>
          </div>
          <div class="logo-text">
            <span class="logo-name">INFSP</span>
            <span class="logo-sub">Institute of Numerical &amp; Formal Sciences</span>
          </div>
        </a>

        <nav :class="['nav-links', { open: isMenuOpen }]" id="nav-links">
          <a href="#specialities" class="nav-link" @click="closeMenu">Formations</a>
          <a href="#admissions" class="nav-link" @click="closeMenu">Admissions</a>
          <a href="#research" class="nav-link" @click="closeMenu">Recherche</a>
          <a href="#student-life" class="nav-link" @click="closeMenu">Vie Étudiante</a>
          <a href="#about" class="nav-link" @click="closeMenu">À Propos</a>
          <a href="#contact" class="nav-link" @click="closeMenu">Contact</a>
          <!-- Mobile CTAs -->
          <router-link to="/register" class="nav-link mobile-only" @click="closeMenu">S'inscrire</router-link>
          <router-link to="/login" class="nav-link mobile-only" @click="closeMenu">Connexion</router-link>
        </nav>

        <div class="desktop-only" style="display: flex; gap: 8px;">
            <router-link to="/register" class="btn btn-outline-white nav-cta" :style="isScrolled ? 'border-color: #0A1E3C; color: #0A1E3C;' : ''">S'INSCRIRE</router-link>
            <router-link to="/login" class="btn btn-yellow nav-cta">CONNEXION</router-link>
        </div>

        <button class="hamburger" :aria-expanded="isMenuOpen" @click="toggleMenu" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </header>

    <!-- HERO -->
    <section class="hero" id="hero">
      <div class="hero-overlay"></div>
      <img src="/assets/images/hero_campus.png" alt="INFSP Campus" class="hero-bg" />
      <div class="container hero-content">
        <h1 class="hero-title hero-anim-title">
          INFSP <span class="accent-yellow" dir="rtl">محمد الطيب بوسنة</span>
        </h1>
        <p class="hero-subtitle hero-anim-subtitle">
          <strong style="color: #FFD700; letter-spacing: 1px;">INSTITUT LOCAL D'INFORMATIQUE</strong><br>
          Former la prochaine génération de professionnels de la technologie avec une formation professionnelle hautement spécialisée, des ressources de pointe et une expertise pratique adaptée au marché du travail moderne.
        </p>
        <div class="hero-actions hero-anim-actions">
          <a href="#specialities" class="btn btn-yellow">DÉCOUVRIR LES PROGRAMMES</a>
          <a href="#about" class="btn btn-outline-white">VISITE VIRTUELLE</a>
        </div>
      </div>
    </section>

    <!-- SPECIALITIES -->
    <section class="specialities-wrapper" id="specialities">
      <div class="container">
        <div class="specialities-card reveal" ref="specialitiesCard">
          
          <div class="speciality-item">
            <div class="speciality-icon">
              <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <rect x="15" y="15" width="10" height="10" fill="#29B6F6"/>
                <rect x="35" y="15" width="10" height="10" fill="#29B6F6"/>
                <rect x="15" y="35" width="10" height="10" fill="#29B6F6"/>
                <rect x="35" y="35" width="10" height="10" fill="#29B6F6"/>
                <rect x="25" y="5" width="10" height="10" fill="#29B6F6"/>
                <rect x="25" y="45" width="10" height="10" fill="#29B6F6"/>
                <rect x="5" y="25" width="10" height="10" fill="#29B6F6"/>
                <rect x="45" y="25" width="10" height="10" fill="#29B6F6"/>
                <rect x="25" y="25" width="10" height="10" fill="#fff"/>
                <path d="M25 25h10v10H25z" fill="#E1F5FE"/>
              </svg>
            </div>
            <h3 class="speciality-name">Développement Web</h3>
          </div>

          <div class="speciality-item">
            <div class="speciality-icon">
              <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <path d="M10 10l10 10-10 10 10 10-10 10M30 10l10 10-10 10 10 10-10 10M50 10L40 20l10 10-10 10 10 10" stroke="#D4AF37" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3 class="speciality-name">ASRI</h3>
          </div>

          <div class="speciality-item">
            <div class="speciality-icon">
              <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <circle cx="20" cy="20" r="12" stroke="#1565C0" stroke-width="4"/>
                <circle cx="40" cy="20" r="12" stroke="#1565C0" stroke-width="4"/>
                <circle cx="20" cy="40" r="12" stroke="#1565C0" stroke-width="4"/>
                <circle cx="40" cy="40" r="12" stroke="#1565C0" stroke-width="4"/>
                <path d="M28 28h4v4h-4z" fill="#1565C0"/>
              </svg>
            </div>
            <h3 class="speciality-name">Base de Données</h3>
          </div>

          <div class="speciality-item">
            <div class="speciality-icon">
              <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <circle cx="30" cy="30" r="18" stroke="#424242" stroke-width="5" stroke-dasharray="10 6"/>
                <circle cx="30" cy="30" r="8" fill="#424242"/>
              </svg>
            </div>
            <h3 class="speciality-name">Cybersécurité</h3>
          </div>

          <div class="speciality-item">
            <div class="speciality-icon">
              <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                <rect x="15" y="15" width="6" height="6" fill="#F57C00"/>
                <rect x="27" y="15" width="6" height="6" fill="#F57C00"/>
                <rect x="39" y="15" width="6" height="6" fill="#F57C00"/>
                <rect x="15" y="27" width="6" height="6" fill="#F57C00"/>
                <rect x="27" y="27" width="6" height="6" fill="#F57C00"/>
                <rect x="39" y="27" width="6" height="6" fill="#F57C00"/>
                <rect x="15" y="39" width="6" height="6" fill="#F57C00"/>
                <rect x="27" y="39" width="6" height="6" fill="#F57C00"/>
                <rect x="39" y="39" width="6" height="6" fill="#F57C00"/>
              </svg>
            </div>
            <h3 class="speciality-name">Maintenance</h3>
          </div>

        </div>
      </div>
    </section>

    <!-- NOS ATOUTS / MULTIPLE CARDS (STATIC SECTION) -->
    <section class="section features-section bg-light" id="atouts">
      <div class="container">
        <div class="section-header text-center" style="text-align:center; margin-bottom: 60px;">
          <h2 class="section-title">POURQUOI NOUS CHOISIR ?</h2>
          <div class="title-bar" style="margin: 0 auto;"></div>
          <p style="margin-top: 15px; color: #475569; max-width: 600px; margin-inline: auto;">Découvrez les avantages d'étudier à l'INFSP et comment nous préparons nos étudiants pour l'avenir.</p>
        </div>

        <div class="features-grid">
          
          <div class="feature-card reveal">
            <div class="feature-icon-wrapper blue-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <h3 class="feature-title">Diplômes Reconnus</h3>
            <p class="feature-desc">Nos formations sont sanctionnées par des diplômes d'État, garantissant une employabilité élevée.</p>
          </div>
          
          <div class="feature-card reveal" style="transition-delay: 0.1s;">
            <div class="feature-icon-wrapper orange-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3 class="feature-title">Équipement Moderne</h3>
            <p class="feature-desc">Accédez à des laboratoires informatiques de pointe équipés des dernières technologies du marché.</p>
          </div>

          <div class="feature-card reveal" style="transition-delay: 0.2s;">
            <div class="feature-icon-wrapper cyan-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3 class="feature-title">Expertise Pédagogique</h3>
            <p class="feature-desc">Apprenez avec des professeurs et des professionnels expérimentés du secteur IT.</p>
          </div>

        </div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section about-section" id="about">
      <div class="container about-grid">

        <div class="about-images">
          <div class="about-img-main">
            <img src="/assets/images/about_students.png" alt="INFSP Students" />
          </div>
          <div class="about-img-badge">
            <span class="badge-number">20<sup>+</sup></span>
            <span class="badge-label">Années d'Excellence</span>
          </div>
        </div>

        <div class="about-content" style="text-align: left;">
          <h2 class="section-title about-title">QUI SOMMES-NOUS?</h2>
          <div class="title-bar" style="margin-left: 0;"></div>
          <p class="about-text">
            L'INFSP — Institut National Spécialisé de Formation Professionnelle Mohamed Tayeb Boucenna — est une institution pionnière dédiée à l'enseignement supérieur et à la formation continue. Nous nous efforçons de former la prochaine génération d'innovateurs en informatique, en offrant des programmes adaptés aux exigences du marché du travail.
          </p>

          <div class="video-wrap" id="video-wrap" v-if="!showVideo">
            <div class="video-thumbnail" @click="showVideo = true">
              <img src="/assets/images/about_students.png" alt="Vidéo d'introduction INFSP" class="video-thumb-img" />
              <div class="video-overlay">
                <button class="play-btn" aria-label="Play video">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <polygon points="8,5 19,12 8,19" fill="white"/>
                  </svg>
                </button>
              </div>
              <span class="video-label">VIDÉO D'INTRODUCTION</span>
            </div>
          </div>
          <div class="video-wrap" v-else>
            <iframe
              src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              style="width: 100%; height: 350px; border: 0; border-radius: 14px;"
            ></iframe>
          </div>
        </div>

      </div>
    </section>

    <!-- STATS -->
    <section class="section custom-stats-section" id="stats">
      <div class="container">
        <div class="section-header text-center" style="text-align:center; margin-bottom: 60px;">
          <h2 class="section-title">STATISTIQUES</h2>
          <div class="title-bar" style="margin: 0 auto;"></div>
        </div>

        <div class="connected-stats-wrapper reveal" ref="statsWrapper">
          <div class="connecting-line desktop-only-line"></div>
          <div class="stats-timeline">
            
            <div class="timeline-stat stat-top">
              <div class="stat-text-box">
                <span class="stat-label">Facultés</span>
                <span class="stat-number" data-target="4">
                  0{{ stats.facultes }}
                </span>
              </div>
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 18.5l-8-4v-8l8 4 8-4v8l-8 4zm0-9.5l-8-4 8-4 8 4-8 4z"/>
                </svg>
              </div>
            </div>

            <div class="timeline-stat stat-bottom">
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 1L0 7l12 6 12-6L12 1zm0 15c-3.3 0-6-2.7-6-6H4c0 4.4 3.6 8 8 8s8-3.6 8-8h-2c0 3.3-2.7 6-6 6z"/>
                </svg>
              </div>
              <div class="stat-text-box">
                <span class="stat-number" data-target="8">
                  {{ stats.programmes }}
                </span>
                <span class="stat-label">Programmes</span>
              </div>
            </div>

            <div class="timeline-stat stat-top">
              <div class="stat-text-box">
                <span class="stat-label">Étudiants</span>
                <span class="stat-number" data-target="300">
                  +{{ stats.etudiants }}
                </span>
              </div>
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
              </div>
            </div>

            <div class="timeline-stat stat-bottom">
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M15 12h-6v2h6v-2zm-6-4h6v2h-6V8zm0-4h6v2h-6V4zm8 10v4c0 1.1-.9 2-2 2H9c-1.1 0-2-.9-2-2v-4H3v6h18v-6h-4z"/>
                </svg>
              </div>
              <div class="stat-text-box">
                <span class="stat-number" data-target="100">
                  +{{ stats.enseignants }}
                </span>
                <span class="stat-label">Enseignants et chercheurs</span>
              </div>
            </div>

            <div class="timeline-stat stat-top">
              <div class="stat-text-box">
                <span class="stat-label">Anciens étudiants</span>
                <span class="stat-number" data-target="200">
                  +{{ stats.anciens }}
                </span>
              </div>
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
              </div>
            </div>

            <div class="timeline-stat stat-bottom">
              <div class="stat-circle" style="color: #00BCD4;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.56-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.73 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .43-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.49-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                </svg>
              </div>
              <div class="stat-text-box">
                <span class="stat-number" data-target="50">
                  +{{ stats.partenariats }}
                </span>
                <span class="stat-label">Partenariats<br><small>Industrie-Gouvernement-Int</small></span>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer" id="contact" style="text-align: left;">
      <div class="container footer-grid">
        <!-- Brand + Contact -->
        <div class="footer-col footer-brand">
          <div class="footer-logo">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="#FFD700"/>
              <path d="M8 10h4v12H8zM14 10h4l6 6-6 6h-4l6-6-6-6z" fill="#0A1E3C"/>
            </svg>
            <span>INFSP</span>
          </div>
          <p class="footer-place">Algérie, Algérie</p>
          <ul class="footer-contact-list" style="padding-left: 0;">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 11a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
              +213 335 7720
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              infsp@gmail.com
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Adresse Horrimet, Algérie
            </li>
          </ul>
          <div class="footer-socials">
            <a href="#" class="social-icon" aria-label="Twitter">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="YouTube">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.4a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#0A1E3C"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col footer-links-col">
          <h4 class="footer-heading">Liens Utiles</h4>
          <ul class="footer-links" style="padding-left: 0;">
            <li><a href="#specialities">Formations</a></li>
            <li><a href="#research">Recherche</a></li>
            <li><a href="#student-life">Vie Étudiante</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-col footer-newsletter-col">
          <h4 class="footer-heading">Newsletter</h4>
          <p class="footer-newsletter-text">Restez informé des actualités et annonces de l'INFSP.</p>
          <form class="newsletter-form" @submit.prevent="handleSubscribe">
            <input type="email" v-model="newsletterEmail" placeholder="Entrez votre adresse email" class="newsletter-input" :style="newsletterError ? 'border-color: #ef4444;' : ''" />
            <button type="submit" class="btn btn-yellow newsletter-btn" :style="newsletterSuccess ? 'background: #22c55e; border-color: #22c55e; color: white;' : ''">
              {{ newsletterSuccess ? '✓ Inscrit !' : 'S\'abonner' }}
            </button>
          </form>
        </div>

      </div>

      <div class="footer-bottom">
        <p style="margin-bottom: 0;">Copyright INFSP 2026. Tous droits réservés.</p>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive } from 'vue'

const isScrolled = ref(false)
const isMenuOpen = ref(false)
const showVideo = ref(false)

const newsletterEmail = ref('')
const newsletterError = ref(false)
const newsletterSuccess = ref(false)

const statsWrapper = ref(null)
const specialitiesCard = ref(null)

const stats = reactive({
  facultes: 0,
  programmes: 0,
  etudiants: 0,
  enseignants: 0,
  anciens: 0,
  partenariats: 0
})

const handleScroll = () => {
  isScrolled.value = window.scrollY > 60
}

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleSubscribe = () => {
  if (!newsletterEmail.value || !newsletterEmail.value.includes('@')) {
    newsletterError.value = true
    return
  }
  newsletterError.value = false
  newsletterSuccess.value = true
  newsletterEmail.value = ''
  setTimeout(() => {
    newsletterSuccess.value = false
  }, 3000)
}

const animateCounter = (key, target) => {
  const duration = 1800
  const step = 16
  const steps = Math.round(duration / step)
  let current = 0

  const easeOut = (t) => 1 - Math.pow(1 - t, 3)

  const timer = setInterval(() => {
    current++
    stats[key] = Math.round(easeOut(current / steps) * target)
    if (current >= steps) {
      stats[key] = target
      clearInterval(timer)
    }
  }, step)
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  
  const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible')
            animateCounter('facultes', 4)
            animateCounter('programmes', 8)
            animateCounter('etudiants', 300)
            animateCounter('enseignants', 100)
            animateCounter('anciens', 200)
            animateCounter('partenariats', 50)
          statsObserver.disconnect()
        }
      })
    }, { threshold: 0.1 })

  if (statsWrapper.value) {
    statsObserver.observe(statsWrapper.value)
  }

  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible')
        revealObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.12 })

  if (specialitiesCard.value) revealObserver.observe(specialitiesCard.value)
  const revealEls = document.querySelectorAll('.about-grid, .footer-col, .feature-card')
  revealEls.forEach((el, i) => {
    el.classList.add('reveal')
    /* Feature cards have inline delays already defined, so let's only add delay if it's not a feature-card */
    if (!el.classList.contains('feature-card')) {
      el.style.transitionDelay = `${(i % 3) * 0.1}s`
    }
    revealObserver.observe(el)
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

.landing-page { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1E293B; background: #FFFFFF; line-height: 1.6; }
* { box-sizing: border-box; }
.container { width: 100%; max-width: 1200px; margin-inline: auto; padding-inline: 24px; }
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; border-radius: 8px; font-size: 0.875rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; text-decoration: none; cursor: pointer; border: 2px solid transparent; transition: all 0.3s cubic-bezier(.4,0,.2,1); white-space: nowrap; }
.btn-yellow { background: #FFD700; color: #0A1E3C; border-color: #FFD700; }
.btn-yellow:hover { background: #E6C200; border-color: #E6C200; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,215,0,.35); }
.btn-outline-white { background: transparent; color: #FFFFFF; border-color: rgba(255,255,255,.6); }
.btn-outline-white:hover { background: rgba(255,255,255,.12); border-color: #FFFFFF; transform: translateY(-2px); }
.section { padding: 96px 0; }
.section-header { text-align: center; margin-bottom: 56px; }
.section-title { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 800; color: #0A1E3C; letter-spacing: -.02em; }
.title-bar { width: 56px; height: 4px; background: #FFD700; border-radius: 4px; margin: 14px auto 0; }

.navbar { position: fixed; inset: 0 0 auto; z-index: 1000; padding: 14px 0; background: transparent; transition: all 0.3s ease; }
.navbar.scrolled { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px); padding: 10px 0; box-shadow: 0 8px 32px rgba(10,30,60,.12); }
.nav-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; max-width: max-content; }
.logo-icon { flex-shrink: 0; }
.logo-text { display: flex; flex-direction: column; line-height: 1.1; text-align: left; }
.logo-name { font-size: 1.25rem; font-weight: 900; color: #FFFFFF; letter-spacing: -.02em; transition: color 0.3s; }
.navbar.scrolled .logo-name { color: #0A1E3C; }
.logo-sub { font-size: 0.6rem; font-weight: 500; color: rgba(255,255,255,.8); letter-spacing: .04em; text-transform: uppercase; transition: color 0.3s; white-space: nowrap; }
.navbar.scrolled .logo-sub { color: #94A3B8; }
.nav-links { display: flex; align-items: center; gap: 2px; }
.nav-link { padding: 8px 10px; font-size: 0.875rem; font-weight: 500; color: #FFFFFF; text-decoration: none; border-radius: 8px; transition: all 0.3s; white-space: nowrap; }
.nav-link:hover { background: rgba(255,255,255,.1); color: #FFD700; }
.navbar.scrolled .nav-link { color: #1E293B; }
.navbar.scrolled .nav-link:hover { background: rgba(10,30,60,.06); color: #0A1E3C; }
.nav-cta { padding: 10px 18px; font-size: 0.8rem; }

.desktop-only { display: flex; }
.mobile-only { display: none; }
.hamburger { display: none; flex-direction: column; gap: 5px; background: none; border: none; cursor: pointer; padding: 4px; }
.hamburger span { display: block; width: 24px; height: 2px; background: #FFFFFF; border-radius: 2px; transition: all 0.3s; }
.navbar.scrolled .hamburger span { background: #0A1E3C; }

.hero { position: relative; height: 60vh; min-height: 500px; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; padding-bottom: 40px; }
.hero-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; transform: scale(1.04); transition: transform 8s ease-out; }
.hero:hover .hero-bg { transform: scale(1); }
.hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(10,30,60,.88) 0%, rgba(10,30,60,.72) 50%, rgba(10,30,60,.55) 100%); z-index: 1; }
.hero-content { position: relative; z-index: 2; max-width: 800px; padding-top: 60px; display: flex; flex-direction: column; align-items: center; }
.hero-title { font-size: clamp(2.4rem, 6vw, 4.5rem); font-weight: 900; line-height: 1.08; color: #FFFFFF; letter-spacing: -.03em; margin-bottom: 24px; }
.accent-yellow { color: #FFD700; }
.hero-subtitle { font-size: clamp(1rem, 1.5vw, 1.2rem); font-weight: 400; color: rgba(255,255,255,.8); line-height: 1.7; margin-bottom: 40px; max-width: 640px; }
.hero-actions { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 16px; }

@keyframes slideTextIn { 0% { opacity: 0; transform: translateY(40px) scale(0.95); filter: blur(8px); } 100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); } }
.hero-anim-title { opacity: 0; animation: slideTextIn 0.8s cubic-bezier(0.25, 1, 0.5, 1) 0.2s forwards; }
.hero-anim-subtitle { opacity: 0; animation: slideTextIn 0.8s cubic-bezier(0.25, 1, 0.5, 1) 0.5s forwards; }
.hero-anim-actions { opacity: 0; animation: slideTextIn 0.8s cubic-bezier(0.25, 1, 0.5, 1) 0.8s forwards; }

.specialities-wrapper { position: relative; z-index: 10; margin-top: -60px; padding-bottom: 80px; }
.specialities-card { background: #FFFFFF; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; box-shadow: 0 20px 40px rgba(10, 30, 60, 0.08); }
.speciality-item { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 20px 10px; border-radius: 14px; transition: all 0.3s; cursor: pointer; }
.speciality-item:hover { background: #F5F7FA; transform: translateY(-5px); }
.speciality-icon { margin-bottom: 16px; transition: transform 0.3s; }
.speciality-item:hover .speciality-icon { transform: scale(1.1); }
.speciality-name { font-size: 0.95rem; font-weight: 700; color: #0A1E3C; line-height: 1.3; }

.features-section { background: #F8FAFC; }
.features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; padding: 20px 0; }
.feature-card { background: #FFFFFF; border-radius: 16px; padding: 40px 32px; text-align: center; box-shadow: 0 10px 30px rgba(10,30,60,.04); transition: transform 0.3s, box-shadow 0.3s; border: 1px solid rgba(10,30,60,.05); }
.feature-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(10,30,60,.08); }
.feature-icon-wrapper { width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; transition: transform 0.3s; }
.feature-card:hover .feature-icon-wrapper { transform: scale(1.1) rotate(-5deg); }
.blue-icon { background: rgba(21,101,192,.1); color: #1565C0; }
.orange-icon { background: rgba(245,124,0,.1); color: #F57C00; }
.cyan-icon { background: rgba(41,182,246,.1); color: #0288D1; }
.feature-title { font-size: 1.25rem; font-weight: 700; color: #0A1E3C; margin-bottom: 16px; }
.feature-desc { font-size: 0.95rem; color: #475569; line-height: 1.6; }

.about-section { background: #FFFFFF; overflow: hidden; }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.about-images { position: relative; }
.about-img-main { border-radius: 28px; overflow: hidden; box-shadow: 0 20px 60px rgba(10,30,60,.18); aspect-ratio: 4/5; max-height: 520px; }
.about-img-main img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; }
.about-img-main:hover img { transform: scale(1.04); }
.about-img-badge { position: absolute; bottom: -20px; right: -20px; background: #FFD700; color: #0A1E3C; border-radius: 14px; padding: 20px 24px; text-align: center; box-shadow: 0 8px 32px rgba(10,30,60,.12); }
.badge-number { display: block; font-size: 2.5rem; font-weight: 900; line-height: 1; letter-spacing: -.04em; }
.badge-number sup { font-size: 1.2rem; vertical-align: super; }
.badge-label { display: block; font-size: 0.7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-top: 4px; opacity: .75; }
.about-content { display: flex; flex-direction: column; gap: 20px; }
.about-title { text-align: left; margin-bottom: 0;}
.about-text { font-size: 1rem; color: #475569; line-height: 1.8; margin-bottom: 1rem; }

.video-wrap { margin-top: 8px; }
.video-thumbnail { position: relative; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 32px rgba(10,30,60,.12); cursor: pointer; aspect-ratio: 16/9; background: #000; }
.video-thumb-img { width: 100%; height: 100%; object-fit: cover; opacity: .65; transition: opacity 0.3s, transform 0.3s; }
.video-thumbnail:hover .video-thumb-img { opacity: .5; transform: scale(1.03); }
.video-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
.play-btn { width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,.2); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.6); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; }
.play-btn:hover { background: #FFD700; border-color: #FFD700; transform: scale(1.1); }
.play-btn:hover svg polygon { fill: #0A1E3C; }
.play-btn svg { margin-left: 4px; }
.video-label { position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: rgba(255,255,255,.85); background: rgba(10,30,60,.55); backdrop-filter: blur(8px); padding: 5px 14px; border-radius: 100px; }

.custom-stats-section { background: #F8FAFC; padding: 100px 0; overflow: hidden; }
.connected-stats-wrapper { position: relative; max-width: 1200px; margin: 0 auto; padding: 120px 0; }
.desktop-only-line { position: absolute; top: 50%; left: 60px; right: 60px; height: 2px; background: #111; z-index: 1; transform: translateY(-50%); }
.stats-timeline { display: flex; justify-content: space-between; position: relative; z-index: 2; align-items: center; }
.timeline-stat { position: relative; display: flex; justify-content: center; align-items: center; width: 120px; height: 120px; }
.stat-circle { width: 120px; height: 120px; background: #FFFFFF; border-radius: 50%; border: 2px solid #111; display: flex; justify-content: center; align-items: center; position: relative; z-index: 3; transition: all 0.3s; }
.stat-circle::after { content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 3px dashed #00BCD4; border-right-style: solid; opacity: 1; animation: spinDash 15s linear infinite; }

@keyframes spinDash { 100% { transform: rotate(360deg); } }
.stat-text-box { position: absolute; width: 220px; text-align: center; display: flex; flex-direction: column; align-items: center; }
.stat-number { font-size: 1.8rem; font-weight: 900; color: #00BCD4; line-height: 1; margin: 8px 0; }
.stat-label { font-size: 0.85rem; font-weight: 700; color: #111; text-transform: uppercase; }
.stat-top .stat-text-box { bottom: 140px; flex-direction: column-reverse; }
.stat-top::before { content: ''; position: absolute; bottom: 120px; left: 50%; width: 2px; height: 20px; background: #111; z-index: 2; transform: translateX(-50%); }
.stat-top::after { content: ''; position: absolute; bottom: 140px; left: 50%; width: 8px; height: 8px; background: #111; border-radius: 50%; z-index: 2; transform: translateX(-50%); }
.stat-bottom .stat-text-box { top: 140px; }
.stat-bottom::before { content: ''; position: absolute; top: 120px; left: 50%; width: 2px; height: 20px; background: #111; z-index: 2; transform: translateX(-50%); }
.stat-bottom::after { content: ''; position: absolute; top: 140px; left: 50%; width: 8px; height: 8px; background: #111; border-radius: 50%; z-index: 2; transform: translateX(-50%); }

.footer { background: #0A1E3C; border-top: 1px solid rgba(255,255,255,.08); padding-top: 72px; }
.footer-grid { display: grid; grid-template-columns: 1.8fr 1fr 1.5fr; gap: 56px; padding-bottom: 56px; }
.footer-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.footer-logo span { font-size: 1.4rem; font-weight: 900; color: #FFFFFF; letter-spacing: -.02em; }
.footer-place { font-size: 0.875rem; font-weight: 600; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 20px; }
.footer-contact-list { list-style: none; display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px;}
.footer-contact-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.875rem; color: rgba(255,255,255,.7); line-height: 1.5; }
.footer-contact-list svg { flex-shrink: 0; margin-top: 2px; }
.footer-socials { display: flex; gap: 10px; }
.social-icon { width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.7); text-decoration: none; transition: all 0.3s; }
.social-icon:hover { background: #FFD700; border-color: #FFD700; color: #0A1E3C; transform: translateY(-3px); }
.footer-heading { font-size: 0.8rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #FFFFFF; margin-bottom: 20px; }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.footer-links a { font-size: 0.9rem; color: rgba(255,255,255,.65); text-decoration: none; transition: color 0.3s, padding-left 0.3s; }
.footer-links a:hover { color: #FFD700; padding-left: 4px; }
.footer-newsletter-text { font-size: 0.875rem; color: rgba(255,255,255,.6); line-height: 1.6; margin-bottom: 16px; }
.newsletter-form { display: flex; gap: 8px; }
.newsletter-input { flex: 1; min-width: 0; padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.06); color: #FFFFFF; font-size: 0.875rem; font-family: inherit; outline: none; transition: border-color 0.3s, background 0.3s; }
.newsletter-input::placeholder { color: rgba(255,255,255,.4); }
.newsletter-input:focus { border-color: #FFD700; background: rgba(255,255,255,.1); }
.footer-bottom { border-top: 1px solid rgba(255,255,255,.08); padding: 20px 0; text-align: center; }
.footer-bottom p { font-size: 0.8rem; color: rgba(255,255,255,.4); letter-spacing: .06em; margin-bottom: 0; }

.reveal { opacity: 0; transform: translateY(32px); transition: opacity .6s ease, transform .6s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }

@media (max-width: 1150px) {
  .logo-sub { display: none; }
}
@media (max-width: 1024px) {
  .specialities-card { grid-template-columns: repeat(3, 1fr); }
  .features-grid { grid-template-columns: repeat(2, 1fr); }
  .about-grid { gap: 48px; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
  .footer-newsletter-col { grid-column: 1 / -1; }
}
@media (max-width: 768px) {
  .desktop-only { display: none !important; }
  .mobile-only { display: block !important; }
  .hamburger { display: flex; margin-left: auto; }
  .nav-links { display: none; position: fixed; inset: 68px 0 0; background: #0A1E3C; flex-direction: column; gap: 0; padding: 24px 0; margin-left: 0; overflow-y: auto; text-align: left; }
  .nav-links.open { display: flex; }
  .nav-link { width: 100%; padding: 16px 28px; border-radius: 0; font-size: 1rem; color: rgba(255,255,255,.85) !important; border-bottom: 1px solid rgba(255,255,255,.06); }
  .nav-link:hover { background: rgba(255,255,255,.06) !important; color: #FFFFFF !important; }
  .hero-content { padding-inline: 0; padding-top: 70px; }
  .hero-actions { flex-direction: column; align-items: flex-start; }
  .hero-actions .btn { width: 100%; justify-content: center; }
  .specialities-card { grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 24px; }
  .features-grid { grid-template-columns: 1fr; gap: 24px; }
  .about-grid { grid-template-columns: 1fr; gap: 40px; }
  .about-img-badge { right: 12px; bottom: -16px; }
  .footer-grid { grid-template-columns: 1fr; gap: 40px; }
  .newsletter-form { flex-direction: column; }
  .newsletter-btn { width: 100%; justify-content: center; }
  
  .desktop-only-line { display: none; }
  .stat-top::before, .stat-top::after, .stat-bottom::before, .stat-bottom::after { display: none; }
  .stats-timeline { flex-direction: column; padding: 2rem 0; gap: 4rem; }
  .timeline-stat { margin-bottom: 2rem; width: auto; height: auto; }
  .stat-text-box { position: static; }
  .stat-circle { display: none; }
  .stat-top .stat-text-box { margin-bottom: 0; flex-direction: column; }
  .stat-bottom .stat-text-box { margin-top: 0;  flex-direction: column; }
}
@media (max-width: 480px) {
  .hero-title { font-size: 2.2rem; }
  .section-title { font-size: 1.6rem; }
  .specialities-card { grid-template-columns: 1fr; }
  .specialities-wrapper { margin-top: -30px; }
}
</style>


