<template>
  <div class="landing-page">
    
    <!-- HEADER / FLOATING NAVBAR -->
    <header :class="['navbar', { scrolled: isScrolled }]" id="navbar">
      <div class="nav-inner">
        <a href="#" class="nav-logo" @click.prevent="scrollToTop">
          <div class="logo-icon animate-pulse-subtle">
            <svg width="36" height="36" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="#FFD700"/>
              <path d="M8 10h4v12H8zM14 10h4l6 6-6 6h-4l6-6-6-6z" fill="#0A1E3C"/>
            </svg>
          </div>
          <div class="logo-text">
            <span class="logo-name">INFSP</span>
            <span class="logo-sub">Mohamed Tayeb Boucenna</span>
          </div>
        </a>

        <nav :class="['nav-links', { open: isMenuOpen }]" id="nav-links">
          <a href="#specialities" class="nav-link" @click="closeMenu">Programmes</a>
          <a href="#atouts" class="nav-link" @click="closeMenu">Atouts</a>
          <a href="#about" class="nav-link" @click="closeMenu">À Propos</a>
          <a href="#stats" class="nav-link" @click="closeMenu">Chiffres</a>
          <a href="#testimonials" class="nav-link" @click="closeMenu">Témoignages</a>
          <a href="#contact" class="nav-link" @click="closeMenu">Contact</a>
          
          <a href="#" class="nav-link highlight-ai" @click.prevent="openAssistant">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ai-sparkle-svg">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            Assistant IA
          </a>
          
          <!-- Mobile Links -->
          <router-link to="/register" class="nav-link mobile-only mobile-cta-outline" @click="closeMenu">S'inscrire</router-link>
          <router-link to="/login" class="nav-link mobile-only mobile-cta-filled" @click="closeMenu">Connexion</router-link>
        </nav>

        <div class="desktop-only cta-group">
          <router-link to="/register" class="btn btn-outline-white nav-cta">S'inscrire</router-link>
          <router-link to="/login" class="btn btn-yellow nav-cta">Connexion</router-link>
        </div>

        <button class="hamburger" :class="{ active: isMenuOpen }" :aria-expanded="isMenuOpen" @click="toggleMenu" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </header>

    <!-- HERO SECTION (DARK/NAVY PRESTIGE) -->
    <section class="hero" id="hero">
      <div class="hero-grid-pattern"></div>
      <div class="glow-orb hero-glow-1"></div>
      <div class="glow-orb hero-glow-2"></div>
      
      <div class="container hero-grid">
        <div class="hero-content text-left">
          <div class="hero-badge reveal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="badge-icon-svg">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
              <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
            <span class="badge-text">Institut National Spécialisé</span>
          </div>
          
          <h1 class="hero-title hero-anim-title">
            Bâtissez Votre Avenir dans la <span class="gradient-text">Technologie</span>
          </h1>
          
          <div class="arabic-hero-title hero-anim-title" dir="rtl">
            المعهد الوطني المتخصص في التكوين المهني <span class="accent-yellow">محمد الطيب بوسنة</span>
          </div>

          <p class="hero-subtitle hero-anim-subtitle">
            Formez-vous avec des professionnels de l'IT. Des diplômes d'État (Technicien Supérieur) hautement spécialisés, des ressources modernes et des compétences concrètes adaptées au marché de l'emploi technologique actuel.
          </p>

          <div class="hero-actions hero-anim-actions">
            <a href="#specialities" class="btn btn-yellow btn-large">Découvrir les Programmes</a>
            <a href="#about" class="btn btn-outline-white btn-large">Visite de l'Institut</a>
          </div>
        </div>

        <div class="hero-media hero-anim-media">
          <div class="media-frame-wrapper">
            <div class="media-glow"></div>
            <div class="media-dots"></div>
            
            <div class="media-image-container">
              <img src="/assets/images/hero_campus.png" alt="INFSP Campus" class="media-img" />
            </div>

            <!-- Floating Info Cards -->
            <div class="floating-card card-top-left animate-float">
              <div class="floating-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
              </div>
              <div class="floating-card-text">
                <strong>Diplôme d'État</strong>
                <span>Technicien Supérieur (TS)</span>
              </div>
            </div>

            <div class="floating-card card-bottom-right animate-float-delayed">
              <div class="floating-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <div class="floating-card-text">
                <strong>+300 Étudiants</strong>
                <span>Enseignement Pratique</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- PROGRAMMES / SPECIALITIES (DARK DEEP SCREEN SECTION) -->
    <section class="specialities-wrapper" id="specialities">
      <div class="glow-orb spec-glow-1"></div>
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag dark-section-tag">FORMATIONS</div>
          <h2 class="section-title text-white">Filières Spécialisées</h2>
          <div class="title-bar"></div>
          <p class="section-subtitle text-slate-350">Cinq spécialisations de haut niveau conçues pour répondre aux exigences réelles des entreprises et des recruteurs technologiques.</p>
        </div>

        <div class="specialities-card-grid reveal" ref="specialitiesCard">
          
          <!-- DEV WEB -->
          <div class="speciality-item card-hover-effect dark-glass-card">
            <div class="speciality-number">01</div>
            <div class="speciality-icon-wrapper brand-glow">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
                <line x1="14" y1="4" x2="10" y2="20"></line>
              </svg>
            </div>
            <span class="speciality-degree-tag tag-gold-dark">TS • 30 Mois</span>
            <h3 class="speciality-name text-white">Développement Web</h3>
            <p class="speciality-desc text-slate-300">Maîtrisez le développement frontend et backend avec des technologies modernes pour concevoir des applications web et mobiles innovantes.</p>
            <a href="#contact" class="speciality-link link-gold">S'inscrire <span class="arrow">→</span></a>
          </div>

          <!-- ASRI -->
          <div class="speciality-item card-hover-effect dark-glass-card">
            <div class="speciality-number">02</div>
            <div class="speciality-icon-wrapper brand-glow">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                <line x1="6" y1="6" x2="6.01" y2="6"></line>
                <line x1="6" y1="18" x2="6.01" y2="18"></line>
                <line x1="20" y1="6" x2="20.01" y2="6"></line>
                <line x1="20" y1="18" x2="20.01" y2="18"></line>
              </svg>
            </div>
            <span class="speciality-degree-tag tag-gold-dark">TS • 30 Mois</span>
            <h3 class="speciality-name text-white">ASRI (Réseaux)</h3>
            <p class="speciality-desc text-slate-300">Apprenez à administrer, sécuriser et superviser des réseaux d'entreprise et des infrastructures systèmes cloud et physiques.</p>
            <a href="#contact" class="speciality-link link-gold">S'inscrire <span class="arrow">→</span></a>
          </div>

          <!-- BD -->
          <div class="speciality-item card-hover-effect dark-glass-card">
            <div class="speciality-number">03</div>
            <div class="speciality-icon-wrapper brand-glow">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
              </svg>
            </div>
            <span class="speciality-degree-tag tag-gold-dark">TS • 30 Mois</span>
            <h3 class="speciality-name text-white">Base de Données</h3>
            <p class="speciality-desc text-slate-300">Spécialisez-vous dans la conception, l'optimisation et l'administration des systèmes de gestion de bases de données relationnels et NoSQL.</p>
            <a href="#contact" class="speciality-link link-gold">S'inscrire <span class="arrow">→</span></a>
          </div>

          <!-- CYBER -->
          <div class="speciality-item card-hover-effect dark-glass-card">
            <div class="speciality-number">04</div>
            <div class="speciality-icon-wrapper brand-glow">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <circle cx="12" cy="11" r="3"></circle>
                <path d="M12 14v4"></path>
              </svg>
            </div>
            <span class="speciality-degree-tag tag-gold-dark">TS • 30 Mois</span>
            <h3 class="speciality-name text-white">Cybersécurité</h3>
            <p class="speciality-desc text-slate-300">Formez-vous aux techniques de défense contre les cyberattaques, d'audit de sécurité et de sécurisation des architectures critiques.</p>
            <a href="#contact" class="speciality-link link-gold">S'inscrire <span class="arrow">→</span></a>
          </div>

          <!-- MAINTENANCE -->
          <div class="speciality-item card-hover-effect dark-glass-card">
            <div class="speciality-number">05</div>
            <div class="speciality-icon-wrapper brand-glow">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
              </svg>
            </div>
            <span class="speciality-degree-tag tag-gold-dark">TS • 30 Mois</span>
            <h3 class="speciality-name text-white">Maintenance</h3>
            <p class="speciality-desc text-slate-300">Devenez expert en diagnostic, réparation de pannes matérielles/logicielles et maintenance des architectures et parcs informatiques.</p>
            <a href="#contact" class="speciality-link link-gold">S'inscrire <span class="arrow">→</span></a>
          </div>

        </div>
      </div>
    </section>

    <!-- NOS ATOUTS / WHY US (LIGHT REFINED GRIDS) -->
    <section class="section features-section" id="atouts">
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag">EXCELLENCE</div>
          <h2 class="section-title text-navy">Pourquoi Choisir l'INFSP ?</h2>
          <div class="title-bar"></div>
          <p class="section-subtitle">Nous mettons tout en œuvre pour assurer la réussite académique et l'insertion professionnelle de nos étudiants.</p>
        </div>

        <div class="features-grid">
          
          <div class="feature-card reveal">
            <div class="feature-icon-wrapper light-brand-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <h3 class="feature-title">Diplômes d'État Reconnus</h3>
            <p class="feature-desc">Nos formations débouchent sur des diplômes de Technicien Supérieur (TS) reconnus par l'État, assurant une crédibilité immédiate auprès des entreprises.</p>
          </div>
          
          <div class="feature-card reveal" style="transition-delay: 0.1s;">
            <div class="feature-icon-wrapper light-brand-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3 class="feature-title">Laboratoires Équipés</h3>
            <p class="feature-desc">Accédez à des salles pratiques dotées d'ordinateurs performants, d'équipements de routage réels et de serveurs dédiés aux TP.</p>
          </div>

          <div class="feature-card reveal" style="transition-delay: 0.2s;">
            <div class="feature-icon-wrapper light-brand-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3 class="feature-title">Formateurs Professionnels</h3>
            <p class="feature-desc">Apprenez avec des enseignants experts et des intervenants du secteur IT qui apportent l'expérience vécue du marché du travail.</p>
          </div>

        </div>
      </div>
    </section>

    <!-- QUI SOMMES-NOUS ? / ABOUT (LIGHT BACKPLATE WITH SHADOWS) -->
    <section class="section about-section bg-light" id="about">
      <div class="container about-grid">

        <div class="about-images reveal">
          <div class="about-images-wrapper">
            <div class="about-img-bg-deco"></div>
            <div class="about-img-main card-hover-effect">
              <img src="/assets/images/about_students.png" alt="INFSP Students" />
            </div>
            <div class="about-img-badge animate-float">
              <span class="badge-number">20<sup>+</sup></span>
              <span class="badge-label">Ans d'Excellence</span>
            </div>
          </div>
        </div>

        <div class="about-content text-left reveal" style="transition-delay: 0.15s;">
          <div class="section-tag text-left">NOTRE INSTITUT</div>
          <h2 class="section-title about-title text-navy">Présentation de l'INFSP</h2>
          <div class="title-bar" style="margin-left: 0;"></div>
          
          <p class="about-text">
            L'INFSP — <strong>Institut National Spécialisé de Formation Professionnelle Mohamed Tayeb Boucenna</strong> — est une institution d'enseignement public leader dans le développement de compétences informatiques et logicielles.
          </p>
          <p class="about-text">
            Notre objectif principal est de combler l'écart entre la formation académique et les besoins du marché de l'emploi technologique en fournissant des programmes axés à 100% sur la pratique.
          </p>

          <!-- Key points -->
          <ul class="about-points-list">
            <li>
              <span class="point-check">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </span>
              <span>Formation orientée projet avec travaux pratiques rigoureux.</span>
            </li>
            <li>
              <span class="point-check">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </span>
              <span>Stages pratiques obligatoires intégrés au cursus en entreprise.</span>
            </li>
            <li>
              <span class="point-check">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0A1E3C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </span>
              <span>Environnement moderne et campus accueillant à taille humaine.</span>
            </li>
          </ul>

          <!-- Video thumbnail -->
          <div class="video-wrap" id="video-wrap" v-if="!showVideo">
            <div class="video-thumbnail" @click="showVideo = true">
              <img src="/assets/images/about_students.png" alt="Vidéo d'introduction INFSP" class="video-thumb-img" />
              <div class="video-overlay">
                <button class="play-btn" aria-label="Play video">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <polygon points="8,5 19,12 8,19" fill="#0A1E3C"/>
                  </svg>
                </button>
              </div>
              <span class="video-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; display: inline-block; vertical-align: middle;">
                  <polygon points="23 7 16 12 23 17 23 7"/>
                  <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                </svg>
                Présentation Vidéo
              </span>
            </div>
          </div>
          
          <div class="video-wrap" v-else>
            <div class="iframe-container">
              <iframe
                src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                style="width: 100%; height: 350px; border: 0; border-radius: 16px;"
              ></iframe>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- STATISTIQUES / STATS (DARK DASHBOARD CONTRAST SCREEN) -->
    <section class="section custom-stats-section dark-stats-section" id="stats">
      <div class="glow-orb stats-glow-1"></div>
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag dark-section-tag">CHIFFRES CLÉS</div>
          <h2 class="section-title text-white">L'Institut en Chiffres</h2>
          <div class="title-bar"></div>
          <p class="section-subtitle text-slate-350">Notre impact éducatif à travers des indicateurs clés de performance et de réussite.</p>
        </div>

        <div class="stats-grid-dashboard reveal" ref="statsWrapper">
          
          <!-- Stat item 1 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
              </svg>
            </div>
            <div class="stat-value text-gold">0{{ stats.facultes }}</div>
            <div class="stat-label-text text-slate-300">Filières / Départements</div>
          </div>

          <!-- Stat item 2 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <div class="stat-value text-gold">{{ stats.programmes }}</div>
            <div class="stat-label-text text-slate-300">Spécialités de Formation</div>
          </div>

          <!-- Stat item 3 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-value text-gold">+{{ stats.etudiants }}</div>
            <div class="stat-label-text text-slate-300">Étudiants Actifs</div>
          </div>

          <!-- Stat item 4 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
              </svg>
            </div>
            <div class="stat-value text-gold">+{{ stats.enseignants }}</div>
            <div class="stat-label-text text-slate-300">Enseignants & Intervenants</div>
          </div>

          <!-- Stat item 5 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="stat-value text-gold">+{{ stats.anciens }}</div>
            <div class="stat-label-text text-slate-300">Diplômés Insérés (Alumni)</div>
          </div>

          <!-- Stat item 6 -->
          <div class="stat-dashboard-card card-hover-effect dark-glass-card">
            <div class="stat-card-glow-line"></div>
            <div class="stat-icon-circle dark-stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <polyline points="16 11 18 13 22 9"></polyline>
              </svg>
            </div>
            <div class="stat-value text-gold">+{{ stats.partenariats }}</div>
            <div class="stat-label-text text-slate-300">Partenaires Entreprises</div>
          </div>

        </div>
      </div>
    </section>

    <!-- TESTIMONIALS SECTION (LIGHT REFINED SCREEN) -->
    <section class="section testimonials-section" id="testimonials">
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag">RÉUSSITE</div>
          <h2 class="section-title text-navy">Témoignages de nos Diplômés</h2>
          <div class="title-bar"></div>
          <p class="section-subtitle">Ceux qui ont étudié à l'INFSP partagent leur expérience et leur parcours vers l'emploi.</p>
        </div>

        <div class="testimonials-grid reveal">
          
          <!-- Testimonial 1 -->
          <div class="testimonial-card card-hover-effect">
            <div class="quote-icon">“</div>
            <p class="testimonial-text">
              L'INFSP m'a fourni les bases techniques solides indispensables. L'orientation fortement pratique des cours m'a permis d'être opérationnel et confiant dès mon premier jour de stage chez Yassir.
            </p>
            <div class="testimonial-user">
              <div class="user-avatar">AB</div>
              <div class="user-info">
                <h4 class="user-name">Amine Belkacem</h4>
                <p class="user-role">TS Dev Web • Software Engineer</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="testimonial-card card-hover-effect">
            <div class="quote-icon">“</div>
            <p class="testimonial-text">
              Les laboratoires réseaux modernes et le haut niveau des formateurs de la filière ASRI ont été les moteurs de ma réussite. Je gère aujourd'hui des parcs serveurs entiers en toute autonomie.
            </p>
            <div class="testimonial-user">
              <div class="user-avatar">SM</div>
              <div class="user-info">
                <h4 class="user-name">Sarah Meziani</h4>
                <p class="user-role">TS ASRI • Administrateur Systèmes</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="testimonial-card card-hover-effect">
            <div class="quote-icon">“</div>
            <p class="testimonial-text">
              Le programme de Cybersécurité est très riche. De plus, l'accompagnement de l'institut pour trouver des stages de fin d'études de qualité est un atout majeur pour débuter sa carrière.
            </p>
            <div class="testimonial-user">
              <div class="user-avatar">RO</div>
              <div class="user-info">
                <h4 class="user-name">Riad Othmani</h4>
                <p class="user-role">TS Cybersécurité • Auditeur SecOps</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- FOOTER / CONTACT (DARK BRIDGED FOOTER) -->
    <footer class="footer" id="contact">
      <div class="container footer-grid">
        <!-- Brand + Contact Info -->
        <div class="footer-col footer-brand reveal">
          <div class="footer-logo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="8" fill="#FFD700"/>
              <path d="M8 10h4v12H8zM14 10h4l6 6-6 6h-4l6-6-6-6z" fill="#0A1E3C"/>
            </svg>
            <span>INFSP</span>
          </div>
          <p class="footer-tagline">Institut National Spécialisé de Formation Professionnelle Mohamed Tayeb Boucenna</p>
          <p class="footer-place">Horrimet, Algérie</p>
          
          <ul class="footer-contact-list">
            <li>
              <span class="footer-contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
              </span>
              <span>+213 335 7720</span>
            </li>
            <li>
              <span class="footer-contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
              </span>
              <span>infsp@gmail.com</span>
            </li>
            <li>
              <span class="footer-contact-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
              </span>
              <span>Adresse Horrimet, Algérie</span>
            </li>
          </ul>

          <div class="footer-socials">
            <a href="#" class="social-icon" aria-label="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M9 8H7v3h2v9h3v-9h3l.5-3H12V6c0-.88.77-1 1-1h2V2h-3C9.17 2 9 3.5 9 5v3z"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="LinkedIn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="YouTube">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.107C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.003 3.003 0 0 0 .502 6.163C0 8.07 0 12 0 12s0 3.93.502 5.837a3.003 3.003 0 0 0 2.11 2.107c1.872.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.002 3.002 0 0 0 2.107-2.107C24 15.93 24 12 24 12s0-3.93-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col footer-links-col reveal" style="transition-delay: 0.1s;">
          <h4 class="footer-heading">Liens Rapides</h4>
          <ul class="footer-links">
            <li><a href="#specialities">Nos Programmes</a></li>
            <li><a href="#atouts">Nos Atouts</a></li>
            <li><a href="#about">Présentation</a></li>
            <li><a href="#stats">Statistiques</a></li>
            <li><a href="#testimonials">Témoignages</a></li>
            <li><router-link to="/login">Espace Étudiant / Enseignant</router-link></li>
          </ul>
        </div>

        <!-- Newsletter Subscription -->
        <div class="footer-col footer-newsletter-col reveal" style="transition-delay: 0.2s;">
          <h4 class="footer-heading">Newsletter</h4>
          <p class="footer-newsletter-text">Abonnez-vous pour recevoir les dernières actualités de l'institut, les dates d'inscriptions et les événements.</p>
          
          <form class="newsletter-form" @submit.prevent="handleSubscribe">
            <div class="newsletter-input-group">
              <input 
                type="email" 
                v-model="newsletterEmail" 
                placeholder="Votre adresse email" 
                class="newsletter-input" 
                :class="{ 'border-red': newsletterError }" 
              />
              <button type="submit" class="newsletter-btn" :class="{ success: newsletterSuccess }">
                {{ newsletterSuccess ? 'Inscrit ✓' : 'S\'abonner' }}
              </button>
            </div>
            <p v-if="newsletterError" class="newsletter-validation-msg error">Veuillez entrer un email valide.</p>
            <p v-if="newsletterSuccess" class="newsletter-validation-msg success">Inscription enregistrée avec succès !</p>
          </form>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 INFSP Mohamed Tayeb Boucenna. Tous droits réservés. Coded with ❤️</p>
      </div>
    </footer>

    <!-- AI ASSISTANT MODAL (PREMIUM GLASSMORPHIC) -->
    <div class="assistant-overlay" :class="{ 'is-open': isAssistantOpen }" @click.self="closeAssistant">
      <div class="assistant-modal">
        <div class="assistant-header">
          <div class="header-title">
            <div class="ai-header-orb"></div>
            <h3>Assistant Virtuel INFSP</h3>
          </div>
          <button @click="closeAssistant" class="close-assistant-btn" aria-label="Fermer l'assistant">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
        
        <div class="assistant-body" ref="assistantMessagesContainer">
          <!-- Idle / Welcome State -->
          <div v-if="!hasStartedChatting" class="assistant-idle">
            <div class="ai-orb-wrapper">
              <div class="ai-orb-glow"></div>
              <div class="ai-particles">
                <svg viewBox="0 0 200 200" width="100%" height="100%">
                  <circle cx="100" cy="20" r="4" class="ai-dot d1" />
                  <circle cx="150" cy="40" r="3" class="ai-dot d2" />
                  <circle cx="180" cy="80" r="5" class="ai-dot d1" style="fill: #0A1E3C;" />
                  <circle cx="170" cy="130" r="3" class="ai-dot d1" />
                  <circle cx="130" cy="170" r="4" class="ai-dot d2" />
                  <circle cx="80" cy="180" r="3" class="ai-dot d1" style="fill: #0A1E3C;" />
                  <circle cx="30" cy="140" r="5" class="ai-dot d1" />
                  <circle cx="20" cy="90" r="2.5" class="ai-dot d2" />
                  <circle cx="45" cy="45" r="4" class="ai-dot d1" style="fill: #0A1E3C;" />
                  <circle cx="100" cy="100" r="6" class="ai-dot d1" style="fill: #FFD700;" />
                </svg>
              </div>
              <div class="ai-orb-text">
                Comment puis-je<br/>vous aider ?
              </div>
            </div>
            <p class="ai-welcome-sub">Posez-moi des questions sur les filières, les inscriptions ou la vie à l'institut.</p>
          </div>

          <!-- Chat state -->
          <div v-if="hasStartedChatting" class="chat-messages">
            <div v-for="(msg, index) in chatMessages" :key="index" :class="['chat-bubble', msg.isBot ? 'bot' : 'user']">
              <div class="chat-bubble-inner">{{ msg.text }}</div>
            </div>
            <div v-if="isBotTyping" class="chat-bubble bot typing">
              <div class="typing-dots">
                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="assistant-footer">
          <input 
            type="text" 
            v-model="chatInput" 
            @keyup.enter="sendMessage" 
            placeholder="Écrivez votre message ici..." 
            class="chat-input"
          />
          <button @click="sendMessage" :disabled="!chatInput.trim()" class="chat-send-btn" aria-label="Envoyer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive } from 'vue'
import apiClient from '@/api/axios'

const isScrolled = ref(false)
const isMenuOpen = ref(false)
const showVideo = ref(false)

const newsletterEmail = ref('')
const newsletterError = ref(false)
const newsletterSuccess = ref(false)

const statsWrapper = ref(null)
const specialitiesCard = ref(null)
  
const isAssistantOpen = ref(false)
const hasStartedChatting = ref(false)
const chatInput = ref('')
const isBotTyping = ref(false)
const assistantMessagesContainer = ref(null)
const chatMessages = ref([])

const scrollToBottom = () => {
  setTimeout(() => {
    if (assistantMessagesContainer.value) {
      assistantMessagesContainer.value.scrollTop = assistantMessagesContainer.value.scrollHeight
    }
  }, 50)
}

const openAssistant = () => {
  isAssistantOpen.value = true
  closeMenu()
}

const closeAssistant = () => {
  isAssistantOpen.value = false
}

const sendMessage = async () => {
  if (!chatInput.value.trim()) return

  if (!hasStartedChatting.value) {
    hasStartedChatting.value = true
  }

  const userQuestion = chatInput.value.trim()
  chatMessages.value.push({ text: userQuestion, isBot: false })
  chatInput.value = ''
  scrollToBottom()

  isBotTyping.value = true
  scrollToBottom()

  try {
    const response = await apiClient.post('/api/chatbot', { message: userQuestion })
    isBotTyping.value = false
    chatMessages.value.push({ 
      text: response.data.reply || "Désolé, je n'ai pas pu vous répondre.", 
      isBot: true 
    })
    scrollToBottom()
  } catch (error) {
    isBotTyping.value = false
    console.error("Erreur Chatbot:", error)
    chatMessages.value.push({ 
      text: "Désolé, une erreur s'est produite lors de la connexion au serveur.", 
      isBot: true 
    })
    scrollToBottom()
  }
}

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
  
  const revealEls = document.querySelectorAll('.about-grid, .footer-col, .feature-card, .testimonials-grid')
  revealEls.forEach((el, i) => {
    el.classList.add('reveal')
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
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

/* ==========================================================================
   RESET & SYSTEM VARIABLE DEFS
   ========================================================================== */
.landing-page {
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #0F172A; /* Slate 900 */
  background: #FFFFFF;
  line-height: 1.6;
  overflow-x: hidden;
  position: relative;
}

* {
  box-sizing: border-box;
}

.container {
  width: 100%;
  max-width: 1140px; 
  margin-inline: auto;
  padding-inline: 40px; 
}

/* Repeating Dot Grid Tech Pattern */
.hero-grid-pattern {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
  background-size: 24px 24px;
  z-index: 1;
  pointer-events: none;
  opacity: 0.8;
}

/* ==========================================================================
   GLOWING DECORATIVE BACKGROUND ORBS
   ========================================================================== */
.glow-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(130px);
  z-index: 0;
  pointer-events: none;
  opacity: 0.35;
}

/* Hero Glows */
.hero-glow-1 {
  width: 500px;
  height: 500px;
  top: -150px;
  right: -50px;
  background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, rgba(10, 30, 60, 0.1) 100%);
}
.hero-glow-2 {
  width: 450px;
  height: 450px;
  bottom: 50px;
  left: -150px;
  background: radial-gradient(circle, rgba(16, 38, 74, 0.6) 0%, rgba(6, 18, 36, 0.2) 100%);
}

/* Specialties Section Glows */
.spec-glow-1 {
  width: 500px;
  height: 500px;
  top: 10%;
  right: -100px;
  background: radial-gradient(circle, rgba(255, 215, 0, 0.12) 0%, rgba(10, 30, 60, 0.2) 100%);
  z-index: 1;
}

/* Stats Section Glows */
.stats-glow-1 {
  width: 500px;
  height: 500px;
  bottom: -100px;
  left: -100px;
  background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, rgba(10, 30, 60, 0.25) 100%);
  z-index: 1;
}

/* ==========================================================================
   TYPOGRAPHY & UTILITIES
   ========================================================================== */
.gradient-text {
  background: linear-gradient(135deg, #FFFFFF 0%, #F5F7FA 40%, #FFD700 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.text-navy {
  color: #0A1E3C;
}

.text-gold {
  color: #FFD700 !important;
}

.section-tag {
  font-family: 'Outfit', sans-serif;
  font-size: 0.725rem;
  font-weight: 800;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: #0A1E3C;
  margin-bottom: 12px;
  display: inline-block;
  background: rgba(10, 30, 60, 0.05);
  padding: 6px 14px;
  border-radius: 100px;
  border: 1px solid rgba(10, 30, 60, 0.1);
}

/* Tag style inside dark navy sections */
.dark-section-tag {
  color: #FFD700;
  background: rgba(255, 215, 0, 0.1);
  border-color: rgba(255, 215, 0, 0.2);
}

.section-title {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(1.85rem, 3.2vw, 2.5rem);
  font-weight: 900;
  letter-spacing: -0.02em;
  line-height: 1.15;
  margin-bottom: 6px;
}

.title-bar {
  width: 50px;
  height: 4px;
  background: #FFD700;
  border-radius: 10px;
  margin: 16px auto 32px;
}

.section-subtitle {
  font-size: clamp(0.9rem, 1.1vw, 1rem);
  color: #64748B;
  max-width: 600px;
  margin-inline: auto;
  margin-bottom: 56px;
}

.text-slate-350 {
  color: #94A3B8;
}

/* ==========================================================================
   BUTTONS
   ========================================================================== */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  text-decoration: none;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  white-space: nowrap;
}

.btn-large {
  padding: 16px 32px;
  font-size: 0.925rem;
  border-radius: 10px;
}

.btn-yellow {
  background: #FFD700;
  color: #0A1E3C;
  border-color: #FFD700;
  box-shadow: 0 4px 14px rgba(255, 215, 0, 0.25);
}
.btn-yellow:hover {
  background: #E6C200;
  border-color: #E6C200;
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(255, 215, 0, 0.35);
}

.btn-outline-white {
  background: rgba(255, 255, 255, 0.08);
  color: #FFFFFF;
  border-color: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(8px);
}
.btn-outline-white:hover {
  background: #FFFFFF;
  color: #0A1E3C;
  border-color: #FFFFFF;
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(255, 255, 255, 0.2);
}

/* ==========================================================================
   INTERACTIVE CARDS (HOVER EFFECTS)
   ========================================================================== */
.card-hover-effect {
  position: relative;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(10, 30, 60, 0.06);
}

/* ==========================================================================
   FLOATING NAVBAR (MARGINS/PADDING FROM VIEWPORT)
   ========================================================================== */
.navbar {
  position: fixed;
  top: 24px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 48px);
  max-width: 1140px; 
  z-index: 1000;
  padding: 14px 28px;
  background: rgba(10, 30, 60, 0.75);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.navbar.scrolled {
  top: 12px;
  background: rgba(10, 30, 60, 0.92);
  border-color: rgba(255, 215, 0, 0.25);
  padding: 10px 24px;
  box-shadow: 0 12px 30px rgba(10, 30, 60, 0.25);
}

.nav-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.nav-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  flex-shrink: 0;
}
.logo-text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
  text-align: left;
}
.logo-name {
  font-family: 'Outfit', sans-serif;
  font-size: 1.3rem;
  font-weight: 900;
  color: #FFFFFF;
  letter-spacing: -0.01em;
}
.logo-sub {
  font-size: 0.58rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 0.04em;
  text-transform: uppercase;
  white-space: nowrap;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 4px;
}
.nav-link {
  padding: 8px 12px;
  font-size: 0.825rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s;
  white-space: nowrap;
}
.nav-link:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #FFD700;
}

/* AI Assistant special link */
.highlight-ai {
  color: #FFD700 !important;
  font-weight: 800 !important;
  background: rgba(255, 215, 0, 0.12);
  border: 1px solid rgba(255, 215, 0, 0.2);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.08);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.highlight-ai:hover {
  background: rgba(255, 215, 0, 0.22) !important;
  transform: scale(1.03);
}
.ai-sparkle-svg {
  display: inline-block;
  animation: rotateSparkle 3s linear infinite;
}
@keyframes rotateSparkle {
  0% { transform: scale(1) rotate(0deg); }
  50% { transform: scale(1.15) rotate(180deg); }
  100% { transform: scale(1) rotate(360deg); }
}

.cta-group {
  display: flex;
  align-items: center;
  gap: 8px;
}
.nav-cta {
  padding: 8px 16px;
  font-size: 0.775rem;
  border-radius: 6px;
}

.hamburger {
  display: none;
  flex-direction: column;
  gap: 6px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
}
.hamburger span {
  display: block;
  width: 22px;
  height: 2px;
  background: #FFFFFF;
  border-radius: 2px;
  transition: all 0.3s;
}

.hamburger.active span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}
.hamburger.active span:nth-child(2) {
  opacity: 0;
}
.hamburger.active span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

/* ==========================================================================
   HERO
   ========================================================================== */
.hero {
  position: relative;
  background: linear-gradient(135deg, #0A1E3C 0%, #10264A 50%, #061224 100%);
  padding: 190px 0 110px;
  color: #FFFFFF;
  overflow: hidden;
}
.hero::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 80px;
  background: linear-gradient(to top, #FFFFFF, transparent);
  pointer-events: none;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 40px;
  align-items: center;
  position: relative;
  z-index: 10;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 100px;
  padding: 6px 14px;
  margin-bottom: 24px;
}
.badge-icon-svg {
  color: #FFD700;
  flex-shrink: 0;
}
.badge-text {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  color: #FFD700;
}

.hero-title {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin-bottom: 12px;
}
.arabic-hero-title {
  font-family: 'Segoe UI', Arial, sans-serif;
  font-size: clamp(1.2rem, 1.8vw, 1.6rem);
  font-weight: 700;
  color: rgba(255, 255, 255, 0.85);
  margin-bottom: 24px;
  border-left: 3px solid #FFD700;
  padding-left: 14px;
}
.hero-subtitle {
  font-size: clamp(0.925rem, 1.2vw, 1.05rem);
  font-weight: 400;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.65;
  margin-bottom: 36px;
  max-width: 540px;
}
.hero-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

/* Hero Right: Interactive Image Frame */
.hero-media {
  display: flex;
  justify-content: center;
}
.media-frame-wrapper {
  position: relative;
  width: 100%;
  max-width: 380px;
  aspect-ratio: 1 / 1;
}
.media-glow {
  position: absolute;
  inset: -15px;
  background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 65%);
  filter: blur(15px);
  z-index: 1;
}
.media-dots {
  position: absolute;
  top: -15px;
  right: -20px;
  width: 100px;
  height: 100px;
  background-image: radial-gradient(rgba(255, 215, 0, 0.15) 2px, transparent 2px);
  background-size: 14px 14px;
  z-index: 1;
}
.media-image-container {
  width: 100%;
  height: 100%;
  border-radius: 30px 15px 30px 15px;
  border: 3px solid rgba(255, 255, 255, 0.12);
  overflow: hidden;
  position: relative;
  z-index: 2;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  background: #061224;
}
.media-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 6s cubic-bezier(0.16, 1, 0.3, 1);
}
.media-frame-wrapper:hover .media-img {
  transform: scale(1.06);
}

/* Floating widgets */
.floating-card {
  position: absolute;
  z-index: 5;
  background: rgba(10, 30, 60, 0.85);
  backdrop-filter: blur(12px);
  border: 1.5px solid rgba(255, 255, 255, 0.15);
  padding: 10px 16px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}
.card-top-left {
  top: 15%;
  left: -30px;
}
.card-bottom-right {
  bottom: 12%;
  right: -20px;
}
.floating-card-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFD700;
}
.floating-card-text {
  display: flex;
  flex-direction: column;
  text-align: left;
  line-height: 1.25;
}
.floating-card-text strong {
  font-size: 0.8rem;
  color: #FFFFFF;
}
.floating-card-text span {
  font-size: 0.65rem;
  color: rgba(255, 255, 255, 0.75);
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0px); }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}
.animate-float-delayed {
  animation: float 4s ease-in-out infinite;
  animation-delay: 2s;
}

.hero-anim-title { opacity: 0; animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards; }
.hero-anim-subtitle { opacity: 0; animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards; }
.hero-anim-actions { opacity: 0; animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.45s forwards; }
.hero-anim-media { opacity: 0; animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1) 0.35s forwards; }

/* ==========================================================================
   PROGRAMMES / SPECIALITIES (DARK DEEP SCREEN SECTION)
   ========================================================================== */
.specialities-wrapper {
  position: relative;
  background: #0A1E3C;
  color: #FFFFFF;
  padding: 100px 0;
  overflow: hidden;
  z-index: 10;
}
.specialities-wrapper::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 80px;
  background: linear-gradient(to top, #FFFFFF, transparent);
  pointer-events: none;
  opacity: 0.1;
}

.specialities-card-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  position: relative;
  z-index: 10;
}

/* Glass card layout for dark program section */
.dark-glass-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(10px);
}
.dark-glass-card:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: #FFD700;
  box-shadow: 0 12px 28px rgba(255, 215, 0, 0.12);
  transform: translateY(-6px);
}

.speciality-item {
  border-radius: 14px;
  padding: 28px 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  text-align: left;
}

.speciality-number {
  position: absolute;
  top: 15px;
  right: 20px;
  font-family: 'Outfit', sans-serif;
  font-size: 2rem;
  font-weight: 900;
  color: rgba(255, 215, 0, 0.05);
  line-height: 1;
  pointer-events: none;
  transition: all 0.3s;
}
.speciality-item:hover .speciality-number {
  color: rgba(255, 215, 0, 0.15);
  transform: scale(1.1);
}

.speciality-icon-wrapper {
  width: 54px;
  height: 54px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 18px;
  transition: transform 0.3s;
}
.speciality-item:hover .speciality-icon-wrapper {
  transform: scale(1.06) rotate(-3deg);
}

.brand-glow {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.speciality-degree-tag {
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 100px;
  margin-bottom: 12px;
}
.tag-gold-dark {
  background: rgba(255, 215, 0, 0.12);
  color: #FFD700;
}

.speciality-name {
  font-family: 'Outfit', sans-serif;
  font-size: 1.05rem;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 10px;
}
.speciality-desc {
  font-size: 0.8rem;
  line-height: 1.5;
  margin-bottom: 20px;
  flex: 1;
}

.speciality-link {
  font-size: 0.775rem;
  font-weight: 700;
  text-decoration: none;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: gap 0.2s;
}
.speciality-link:hover .arrow {
  transform: translateX(4px);
}
.speciality-link .arrow {
  transition: transform 0.2s;
  display: inline-block;
}
.link-gold {
  color: #FFD700;
}

/* ==========================================================================
   FEATURES (WHY US)
   ========================================================================== */
.features-section {
  background: #FFFFFF;
  padding: 100px 0;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.feature-card {
  background: #F8FAFC;
  border-radius: 14px;
  padding: 38px 28px;
  text-align: center;
  border: 1px solid rgba(10, 30, 60, 0.04);
  box-shadow: 0 8px 24px rgba(10, 30, 60, 0.02);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.feature-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(10, 30, 60, 0.05);
  border-color: #FFD700;
  background: #FFFFFF;
}

.feature-icon-wrapper {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  transition: transform 0.3s;
}
.feature-card:hover .feature-icon-wrapper {
  transform: scale(1.06) rotate(-4deg);
}
.light-brand-icon {
  background: rgba(10, 30, 60, 0.05);
  border: 1px solid rgba(10, 30, 60, 0.08);
}

.feature-title {
  font-family: 'Outfit', sans-serif;
  font-size: 1.15rem;
  font-weight: 800;
  color: #0A1E3C;
  margin-bottom: 12px;
}
.feature-desc {
  font-size: 0.85rem;
  color: #64748B;
  line-height: 1.6;
}

/* ==========================================================================
   ABOUT SECTION
   ========================================================================== */
.about-section {
  background: #F8FAFC;
}

.about-grid {
  display: grid;
  grid-template-columns: 0.95fr 1.05fr;
  gap: 60px;
  align-items: center;
}

.about-images {
  display: flex;
  justify-content: center;
}
.about-images-wrapper {
  position: relative;
  width: 100%;
  max-width: 380px;
}
.about-img-bg-deco {
  position: absolute;
  top: 24px;
  left: -16px;
  width: 100%;
  height: 100%;
  background: #FFD700;
  border-radius: 24px;
  z-index: 1;
  opacity: 0.95;
}
.about-img-main {
  position: relative;
  z-index: 2;
  border-radius: 24px;
  overflow: hidden;
  aspect-ratio: 4 / 5;
  box-shadow: 0 16px 32px rgba(0,0,0,0.1);
  background: #0A1E3C;
}
.about-img-main img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}
.about-img-main:hover img {
  transform: scale(1.04);
}

.about-img-badge {
  position: absolute;
  bottom: -12px;
  right: -20px;
  background: #0A1E3C;
  color: #FFFFFF;
  border-radius: 14px;
  padding: 16px 20px;
  text-align: center;
  box-shadow: 0 8px 24px rgba(10, 30, 60, 0.2);
  border: 1.5px solid rgba(255, 215, 0, 0.35);
  z-index: 3;
}
.badge-number {
  display: block;
  font-family: 'Outfit', sans-serif;
  font-size: 2rem;
  font-weight: 900;
  line-height: 1;
  color: #FFD700;
}
.badge-number sup {
  font-size: 1rem;
  vertical-align: super;
}
.badge-label {
  display: block;
  font-size: 0.6rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-top: 4px;
  opacity: 0.85;
}

.about-content {
  display: flex;
  flex-direction: column;
}
.about-title {
  margin-bottom: 12px;
}
.about-text {
  font-size: 0.9rem;
  color: #475569;
  line-height: 1.65;
  margin-bottom: 16px;
}

.about-points-list {
  list-style: none;
  padding-left: 0;
  margin: 16px 0 28px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.about-points-list li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #334155;
  text-align: left;
}
.point-check {
  color: #0A1E3C;
  background: rgba(255, 215, 0, 0.2);
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Introduction Video Container */
.video-wrap {
  margin-top: 8px;
}
.video-thumbnail {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(10, 30, 60, 0.06);
  cursor: pointer;
  aspect-ratio: 16 / 9;
  background: #000;
  border: 1px solid rgba(10, 30, 60, 0.06);
}
.video-thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.7;
  transition: all 0.4s;
}
.video-thumbnail:hover .video-thumb-img {
  opacity: 0.55;
  transform: scale(1.03);
}
.video-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.play-btn {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  background: #FFD700;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 6px 16px rgba(255, 215, 0, 0.35);
}
.play-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 10px 24px rgba(255, 215, 0, 0.45);
}
.play-btn svg {
  margin-left: 3px;
}
.video-label {
  position: absolute;
  bottom: 12px;
  left: 12px;
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #FFFFFF;
  background: rgba(10, 30, 60, 0.85);
  backdrop-filter: blur(8px);
  padding: 6px 12px;
  border-radius: 100px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
}

.iframe-container {
  position: relative;
  aspect-ratio: 16 / 9;
  box-shadow: 0 16px 32px rgba(10, 30, 60, 0.15);
  border-radius: 14px;
  overflow: hidden;
}

/* ==========================================================================
   STATS SECTION (DARK DASHBOARD CONTRAST SCREEN)
   ========================================================================== */
.dark-stats-section {
  background: #061224;
  color: #FFFFFF;
  padding: 100px 0;
  position: relative;
}
.dark-stats-section .section-subtitle {
  color: #94A3B8;
}

.stats-grid-dashboard {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  position: relative;
  z-index: 10;
}

.dark-stat-icon {
  background: rgba(255, 255, 255, 0.03) !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
}
.stat-dashboard-card:hover .dark-stat-icon {
  border-color: #FFD700 !important;
  background: rgba(255, 215, 0, 0.05) !important;
}

/* ==========================================================================
   TESTIMONIALS SECTION
   ========================================================================== */
.testimonials-section {
  background: #FFFFFF;
  padding: 100px 0;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.testimonial-card {
  background: #F8FAFC;
  border-radius: 16px;
  padding: 36px 28px;
  display: flex;
  flex-direction: column;
  position: relative;
}

.quote-icon {
  position: absolute;
  top: 20px;
  right: 28px;
  font-size: 3.5rem;
  font-family: Georgia, serif;
  color: rgba(10, 30, 60, 0.06);
  line-height: 1;
}

.testimonial-text {
  font-size: 0.875rem;
  color: #475569;
  line-height: 1.6;
  font-style: italic;
  margin-bottom: 28px;
  position: relative;
  z-index: 2;
  flex: 1;
  text-align: left;
}

.testimonial-user {
  display: flex;
  align-items: center;
  gap: 14px;
  position: relative;
  z-index: 2;
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.875rem;
  background: #0A1E3C;
  color: #FFD700;
  border: 1px solid rgba(255, 215, 0, 0.35);
}

.user-info {
  text-align: left;
  line-height: 1.25;
}
.user-name {
  font-family: 'Outfit', sans-serif;
  font-size: 0.925rem;
  font-weight: 800;
  color: #0A1E3C;
}
.user-role {
  font-size: 0.7rem;
  color: #64748B;
  font-weight: 600;
  margin-top: 2px;
}

/* ==========================================================================
   FOOTER
   ========================================================================== */
.footer {
  background: #0A1E3C;
  color: #FFFFFF;
  border-top: 1.5px solid rgba(255, 255, 255, 0.08);
  padding: 80px 0 0;
  position: relative;
  z-index: 10;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.25fr 0.75fr 1fr;
  gap: 50px;
  padding-bottom: 45px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}
.footer-logo span {
  font-family: 'Outfit', sans-serif;
  font-size: 1.45rem;
  font-weight: 900;
  color: #FFFFFF;
  letter-spacing: -0.01em;
}
.footer-tagline {
  font-size: 0.825rem;
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.6;
  margin-bottom: 20px;
  max-width: 300px;
  text-align: left;
}
.footer-place {
  font-size: 0.725rem;
  font-weight: 700;
  color: #FFD700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 24px;
  text-align: left;
}

.footer-contact-list {
  list-style: none;
  padding: 0;
  margin: 0 0 28px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.footer-contact-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.825rem;
  color: rgba(255, 255, 255, 0.75);
}
.footer-contact-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.footer-socials {
  display: flex;
  gap: 10px;
}
.social-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.75);
  text-decoration: none;
  transition: all 0.3s;
}
.social-icon:hover {
  background: #FFD700;
  border-color: #FFD700;
  color: #0A1E3C;
  transform: translateY(-3px);
}

.footer-heading {
  font-family: 'Outfit', sans-serif;
  font-size: 0.775rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #FFFFFF;
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 8px;
  text-align: left;
}
.footer-heading::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 24px;
  height: 2px;
  background: #FFD700;
}

.footer-links {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
  text-align: left;
}
.footer-links a {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.3s;
}
.footer-links a:hover {
  color: #FFD700;
  padding-left: 4px;
}

.footer-newsletter-text {
  font-size: 0.825rem;
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.55;
  margin-bottom: 18px;
  text-align: left;
}

.newsletter-form {
  position: relative;
}
.newsletter-input-group {
  display: flex;
  gap: 4px;
}
.newsletter-input {
  flex: 1;
  min-width: 0;
  padding: 12px 14px;
  border-radius: 8px;
  border: 1.5px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.05);
  color: #FFFFFF;
  font-size: 0.85rem;
  outline: none;
  transition: all 0.3s;
}
.newsletter-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}
.newsletter-input:focus {
  border-color: #FFD700;
  background: rgba(255, 255, 255, 0.08);
}
.newsletter-input.border-red {
  border-color: #ef4444 !important;
}

.newsletter-btn {
  background: #FFD700;
  color: #0A1E3C;
  border: none;
  border-radius: 8px;
  padding: 12px 18px;
  font-weight: 700;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}
.newsletter-btn:hover {
  background: #E6C200;
}
.newsletter-btn.success {
  background: #22c55e !important;
  color: #FFFFFF;
}

.newsletter-validation-msg {
  font-size: 0.725rem;
  margin-top: 8px;
  font-weight: 600;
  text-align: left;
}
.newsletter-validation-msg.error { color: #f87171; }
.newsletter-validation-msg.success { color: #4ade80; }

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding: 24px 0;
  text-align: center;
}
.footer-bottom p {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
  margin: 0;
}

/* ==========================================================================
   AI ASSISTANT MODAL (PREMIUM STYLING)
   ========================================================================== */
.assistant-overlay {
  position: fixed;
  inset: 0;
  background: rgba(6, 18, 36, 0.65);
  backdrop-filter: blur(6px);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.assistant-overlay.is-open {
  opacity: 1;
  pointer-events: all;
}

.assistant-modal {
  width: 90%;
  max-width: 520px;
  height: 75vh;
  max-height: 600px;
  background: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transform: scale(0.94) translateY(20px);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(10, 30, 60, 0.08);
}
.assistant-overlay.is-open .assistant-modal {
  transform: scale(1) translateY(0);
}

.assistant-header {
  background: #0A1E3C;
  padding: 16px 20px;
  color: #FFFFFF;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #FFD700;
}
.header-title {
  display: flex;
  align-items: center;
  gap: 10px;
}
.ai-header-orb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #FFD700;
  box-shadow: 0 0 8px #FFD700;
  position: relative;
}
.ai-header-orb::after {
  content: '';
  position: absolute;
  inset: -3px;
  border-radius: 50%;
  border: 1.5px solid #FFD700;
  opacity: 0.8;
  animation: pulseGlow 2s infinite ease-out;
}
.header-title h3 {
  margin: 0;
  font-family: 'Outfit', sans-serif;
  font-size: 1.05rem;
  font-weight: 800;
}

.close-assistant-btn {
  background: rgba(255, 255, 255, 0.08);
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.close-assistant-btn:hover {
  background: #FFD700;
  color: #0A1E3C;
}

.assistant-body {
  flex: 1;
  overflow-y: auto;
  background: #F8FAFC;
  position: relative;
  display: flex;
  flex-direction: column;
}

.assistant-idle {
  margin: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 24px;
  max-width: 360px;
}

.ai-orb-wrapper {
  position: relative;
  width: 180px;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}
.ai-orb-glow {
  position: absolute;
  width: 110px;
  height: 110px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 215, 0, 0.4) 0%, rgba(10, 30, 60, 0.3) 50%, transparent 100%);
  filter: blur(20px);
  animation: pulseGlow 4s ease-in-out infinite alternate;
}
.ai-particles {
  position: absolute;
  inset: 0;
  animation: spinOrb 24s linear infinite;
}
.ai-dot {
  fill: #0A1E3C;
  animation: scaleDot 3s ease-in-out infinite alternate;
  transform-origin: center;
  transform-box: fill-box;
}
.ai-dot.d2 { fill: #FFD700; animation-delay: 1.2s; }
.ai-dot.d1 { animation-delay: 0s; }

.ai-orb-text {
  position: relative;
  z-index: 2;
  font-family: 'Outfit', sans-serif;
  font-size: 1.2rem;
  font-weight: 800;
  color: #FFFFFF;
  line-height: 1.25;
  text-shadow: 0 3px 6px rgba(10, 30, 60, 0.7);
}

.ai-welcome-sub {
  font-size: 0.775rem;
  color: #64748B;
  line-height: 1.5;
}

/* Chat bubble styling */
.chat-messages {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.chat-bubble {
  max-width: 80%;
  display: flex;
}
.chat-bubble-inner {
  padding: 12px 16px;
  border-radius: 14px;
  font-size: 0.85rem;
  line-height: 1.5;
  text-align: left;
}
.chat-bubble.bot {
  align-self: flex-start;
}
.chat-bubble.bot .chat-bubble-inner {
  background: #FFFFFF;
  color: #1E293B;
  border-bottom-left-radius: 4px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.02);
  border: 1px solid rgba(10, 30, 60, 0.05);
}
.chat-bubble.user {
  align-self: flex-end;
}
.chat-bubble.user .chat-bubble-inner {
  background: #0A1E3C;
  color: #FFFFFF;
  border-bottom-right-radius: 4px;
  box-shadow: 0 3px 10px rgba(10, 30, 60, 0.1);
  border: 1px solid rgba(10, 30, 60, 0.08);
}

.assistant-footer {
  padding: 12px 16px;
  background: #FFFFFF;
  border-top: 1px solid rgba(10, 30, 60, 0.06);
  display: flex;
  gap: 8px;
}
.chat-input {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid rgba(10, 30, 60, 0.1);
  border-radius: 8px;
  outline: none;
  font-size: 0.85rem;
  background: #F8FAFC;
  color: #0F172A;
  transition: all 0.3s;
}
.chat-input:focus {
  border-color: #FFD700;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}
.chat-send-btn {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #FFD700;
  border: none;
  color: #0A1E3C;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 3px 8px rgba(255, 215, 0, 0.25);
  flex-shrink: 0;
}
.chat-send-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.35);
}
.chat-send-btn:disabled {
  background: #E2E8F0;
  color: #94A3B8;
  box-shadow: none;
  cursor: not-allowed;
}

/* Typing indicator */
.typing-dots {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 4px 2px;
}
.typing-dots .dot {
  width: 6px;
  height: 6px;
  background: #94A3B8;
  border-radius: 50%;
  animation: chatBounce 1.4s infinite ease-in-out both;
}
.typing-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dots .dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes chatBounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

/* Animation Keyframes */
@keyframes pulseGlow {
  0% { transform: scale(1); opacity: 0.6; }
  100% { transform: scale(1.1); opacity: 0.9; }
}
@keyframes spinOrb {
  100% { transform: rotate(360deg); }
}
@keyframes scaleDot {
  0% { transform: scale(0.8); opacity: 0.4; }
  100% { transform: scale(1.2); opacity: 0.85; }
}

/* ==========================================================================
   SCROLL REVEAL EFFECTS
   ========================================================================== */
.reveal {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.reveal.visible {
  opacity: 1;
  transform: translateY(0);
}

@keyframes slideUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* ==========================================================================
   RESPONSIVENESS / MEDIA QUERIES
   ========================================================================== */
@media (max-width: 1200px) {
  .specialities-card-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 992px) {
  .hero-grid {
    grid-template-columns: 1fr;
    gap: 40px;
    text-align: center;
  }
  .hero-badge, .arabic-hero-title {
    align-self: center;
  }
  .hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .hero-media {
    order: -1;
  }
  .about-grid {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  .stats-grid-dashboard {
    grid-template-columns: repeat(2, 1fr);
  }
  .testimonials-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 30px;
  }
}

@media (max-width: 768px) {
  .container {
    padding-inline: 24px;
  }
  .desktop-only {
    display: none !important;
  }
  .mobile-only {
    display: block !important;
  }
  .hamburger {
    display: flex;
    margin-left: auto;
  }
  .navbar {
    width: calc(100% - 32px);
    top: 16px;
    padding: 12px 20px;
    border-radius: 12px;
  }
  .navbar.scrolled {
    top: 8px;
    padding: 8px 16px;
  }
  .nav-links {
    display: none;
    position: fixed;
    inset: 64px 0 0;
    background: #0A1E3C;
    flex-direction: column;
    gap: 0;
    padding: 20px 0;
    overflow-y: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 0 0 12px 12px;
  }
  .nav-links.open {
    display: flex;
  }
  .nav-link {
    width: 100%;
    padding: 14px 24px;
    border-radius: 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    text-align: left;
  }
  .nav-link:hover {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #FFFFFF !important;
  }
  .mobile-cta-outline {
    border-color: rgba(255, 255, 255, 0.15) !important;
    font-weight: 700;
  }
  .mobile-cta-filled {
    background: #FFD700 !important;
    color: #0A1E3C !important;
    font-weight: 800;
  }

  .specialities-card-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .features-grid {
    grid-template-columns: 1fr;
  }
  .stats-grid-dashboard {
    grid-template-columns: 1fr;
  }
  .testimonials-grid {
    grid-template-columns: 1fr;
  }
  .footer-grid {
    grid-template-columns: 1fr;
  }
  .card-top-left {
    left: -10px;
  }
  .card-bottom-right {
    right: -10px;
  }
}

@media (max-width: 480px) {
  .container {
    padding-inline: 16px;
  }
  .hero {
    padding-top: 150px;
  }
  .hero-actions {
    flex-direction: column;
    width: 100%;
  }
  .hero-actions .btn {
    width: 100%;
  }
  .specialities-card-grid {
    grid-template-columns: 1fr;
  }
  .about-img-badge {
    right: -10px;
    bottom: -15px;
    padding: 10px 14px;
  }
  .badge-number {
    font-size: 1.6rem;
  }
}
</style>
