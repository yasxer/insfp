<template>
  <div class="landing">

    <!-- HEADER -->
    <header :class="['site-header', { scrolled: isScrolled }]">
      <div class="container header-inner">
        <a href="#top" class="brand" @click.prevent="scrollToTop">
          <img src="/logo.png" alt="Logo INSFP" class="brand-logo" width="44" height="44" />
          <span class="brand-text">
            <span class="brand-name">INSFP</span>
            <span class="brand-sub">Mohamed Tayeb Boucenna</span>
          </span>
        </a>

        <nav id="main-nav" :class="['header-nav', { open: isMenuOpen }]" aria-label="Navigation principale">
          <a v-for="link in navLinks" :key="link.href" :href="link.href" class="nav-link" @click="isMenuOpen = false">{{ link.label }}</a>
          <button class="nav-link nav-ai" @click="openAssistant">
            <span class="ai-dot" aria-hidden="true"></span>
            Assistant IA
          </button>
          <div class="nav-mobile-actions">
            <router-link to="/register" class="btn btn-outline">Inscription en ligne</router-link>
            <router-link to="/login" class="btn btn-primary">Espace numérique</router-link>
          </div>
        </nav>

        <div class="header-actions">
          <router-link to="/register" class="btn btn-ghost">Inscription</router-link>
          <router-link to="/login" class="btn btn-primary">
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            Espace numérique
          </router-link>
        </div>

        <button class="menu-toggle" :aria-expanded="isMenuOpen" aria-controls="main-nav" @click="isMenuOpen = !isMenuOpen">
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true">
            <path v-if="!isMenuOpen" d="M3 6h18M3 12h18M3 18h18"/>
            <path v-else d="M18 6 6 18M6 6l12 12"/>
          </svg>
          <span class="sr-only">Menu</span>
        </button>
      </div>
    </header>

    <main id="top">
      <!-- HERO -->
      <section class="hero">
        <div class="hero-pattern" aria-hidden="true"></div>
        <div class="container hero-grid">
          <div class="hero-text">
            <p class="eyebrow eyebrow-light hero-in" style="--d: .05s">Plateforme numérique de l'établissement</p>
            <h1 class="hero-title hero-in" style="--d: .15s">Former les techniciens supérieurs de demain</h1>
            <p class="hero-lead hero-in" style="--d: .3s">
              L'INSFP Mohamed Tayeb Boucenna assure des formations diplômantes de niveau BTS dans les métiers du numérique.
              Stagiaires, formateurs et administration disposent d'un espace en ligne unique pour la scolarité,
              les emplois du temps, les évaluations et la communication.
            </p>
            <div class="hero-actions hero-in" style="--d: .45s">
              <a href="#formations" class="btn btn-gold btn-lg">Consulter les formations</a>
              <a href="#inscription" class="btn btn-outline-light btn-lg">Procédure d'inscription</a>
            </div>
          </div>

          <aside class="services-card hero-in-right" style="--d: .35s" aria-labelledby="services-title">
            <h2 id="services-title" class="services-title">Accès aux espaces</h2>
            <ul class="services-list">
              <li v-for="s in services" :key="s.title">
                <router-link :to="s.to" class="service-item">
                  <span class="service-icon"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path :d="s.icon"/></svg></span>
                  <span class="service-body">
                    <strong>{{ s.title }}</strong>
                    <span>{{ s.desc }}</span>
                  </span>
                  <svg class="ico chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </router-link>
              </li>
            </ul>
          </aside>
        </div>

        <div class="container">
          <dl class="key-facts">
            <div v-for="(f, i) in keyFacts" :key="f.label" class="fact hero-in" :style="{ '--d': `${0.6 + i * 0.1}s` }">
              <dt>{{ f.label }}</dt>
              <dd>{{ f.value }}</dd>
            </div>
          </dl>
        </div>
      </section>

      <!-- NOTICES -->
      <section class="section section-soft" id="avis">
        <div class="container">
          <header class="section-head" v-reveal>
            <p class="eyebrow">Avis et informations</p>
            <h2 class="section-title">Informations aux stagiaires</h2>
          </header>
          <div class="notices">
            <article v-for="(n, i) in notices" :key="n.title" class="notice" v-reveal="i * 110">
              <span class="notice-tag">{{ n.tag }}</span>
              <h3 class="notice-title">{{ n.title }}</h3>
              <p class="notice-text">{{ n.text }}</p>
              <router-link :to="n.to" class="notice-link">{{ n.cta }} <span aria-hidden="true">→</span></router-link>
            </article>
          </div>
        </div>
      </section>

      <!-- FORMATIONS -->
      <section class="section" id="formations">
        <div class="container">
          <header class="section-head" v-reveal>
            <p class="eyebrow">Offre de formation</p>
            <h2 class="section-title">Spécialités enseignées</h2>
            <p class="section-lead">
              Formations sanctionnées par un diplôme d'État de Brevet de Technicien Supérieur (BTS), niveau 5,
              organisées en cinq semestres.
            </p>
          </header>

          <div class="spec-grid">
            <article v-for="(sp, i) in specialties" :key="sp.code" class="spec-card" v-reveal="(i % 3) * 110">
              <div class="spec-top">
                <span class="spec-icon"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path :d="sp.icon"/></svg></span>
                <span class="spec-code">{{ sp.code }}</span>
              </div>
              <h3 class="spec-name">{{ sp.name }}</h3>
              <p class="spec-desc">{{ sp.desc }}</p>
              <ul class="spec-meta">
                <li>BTS · Niveau 5</li>
                <li>30 mois</li>
              </ul>
            </article>
          </div>
        </div>
      </section>

      <!-- MODES -->
      <section class="section section-soft" id="modes">
        <div class="container">
          <header class="section-head" v-reveal>
            <p class="eyebrow">Modes de formation</p>
            <h2 class="section-title">Trois modes d'accès à la formation</h2>
          </header>
          <div class="modes">
            <article v-for="(m, i) in modes" :key="m.title" class="mode" v-reveal="i * 110">
              <span class="mode-num">{{ String(i + 1).padStart(2, '0') }}</span>
              <h3 class="mode-title">{{ m.title }}</h3>
              <p class="mode-text">{{ m.text }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- AI ASSISTANT TEASER -->
      <section class="section ai-section" id="assistant">
        <div class="container ai-grid">
          <div class="ai-text" v-reveal>
            <p class="eyebrow">Assistant intelligent</p>
            <h2 class="section-title">Une question&nbsp;? L'assistant de l'INSFP vous répond</h2>
            <p class="section-lead">
              Spécialités, conditions d'inscription, sessions, fonctionnement de la plateforme :
              posez votre question en langage naturel et obtenez une réponse immédiate, à toute heure.
            </p>
            <ul class="ai-points">
              <li>Disponible 24 h / 24</li>
              <li>Réponses en français et en darja</li>
              <li>Basé sur l'intelligence artificielle</li>
            </ul>
            <button class="btn btn-primary btn-lg ai-cta" @click="openAssistant">
              <span class="ai-dot" aria-hidden="true"></span>
              Discuter avec l'assistant
            </button>
          </div>

          <button class="ai-stage" v-reveal="150" @click="openAssistant" aria-label="Ouvrir l'assistant">
            <span class="ai-ring ai-ring-1" aria-hidden="true"></span>
            <span class="ai-ring ai-ring-2" aria-hidden="true"></span>
            <span class="ai-bubble">Bonjour ! Posez-moi votre question 👋</span>
            <AssistantRobot class="ai-stage-robot" />
          </button>
        </div>
      </section>

      <!-- INSCRIPTION -->
      <section class="section" id="inscription">
        <div class="container">
          <header class="section-head" v-reveal>
            <p class="eyebrow">Inscription</p>
            <h2 class="section-title">Procédure d'inscription en ligne</h2>
            <p class="section-lead">Les sessions de formation sont ouvertes deux fois par an, en février et en septembre.</p>
          </header>

          <ol class="steps">
            <li v-for="(st, i) in steps" :key="st.title" class="step" v-reveal="i * 120">
              <span class="step-num">{{ i + 1 }}</span>
              <div>
                <h3 class="step-title">{{ st.title }}</h3>
                <p class="step-text">{{ st.text }}</p>
              </div>
            </li>
          </ol>

          <div class="steps-cta" v-reveal>
            <router-link to="/register" class="btn btn-primary btn-lg">Créer mon compte stagiaire</router-link>
            <span class="steps-note">Un numéro d'inscription délivré par l'administration est obligatoire.</span>
          </div>
        </div>
      </section>

      <!-- PLATFORM -->
      <section class="section section-navy" id="institut">
        <div class="container">
          <header class="section-head" v-reveal>
            <p class="eyebrow eyebrow-light">L'établissement</p>
            <h2 class="section-title section-title-light">Une administration numérique au service de la formation</h2>
            <p class="section-lead section-lead-light">
              La plateforme centralise la gestion pédagogique et administrative de l'institut et garantit
              la traçabilité des notes, des absences et des documents.
            </p>
          </header>
          <div class="roles">
            <article v-for="(r, i) in roles" :key="r.title" class="role" v-reveal="i * 110">
              <h3 class="role-title">{{ r.title }}</h3>
              <ul class="role-list">
                <li v-for="item in r.items" :key="item">{{ item }}</li>
              </ul>
            </article>
          </div>
        </div>
      </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer" id="contact">
      <div class="container footer-grid">
        <div class="footer-brand">
          <img src="/logo.png" alt="" class="footer-logo" width="56" height="56" />
          <p class="footer-name">INSFP Mohamed Tayeb Boucenna</p>
          <p class="footer-ar" lang="ar" dir="rtl">المعهد الوطني المتخصص في التكوين المهني محمد الطيب بوسنة</p>
          <p class="footer-tutelle">Sous la tutelle du Ministère de la Formation et de l'Enseignement Professionnels</p>
        </div>

        <div>
          <h2 class="footer-heading">Contact</h2>
          <ul class="footer-list">
            <li>
              <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
              Horrimet, Algérie
            </li>
            <li>
              <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>
              +213 335 7720
            </li>
            <li>
              <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM22 6l-10 7L2 6"/></svg>
              infsp@gmail.com
            </li>
          </ul>
        </div>

        <div>
          <h2 class="footer-heading">Liens utiles</h2>
          <ul class="footer-list footer-links">
            <li><a href="#formations">Spécialités</a></li>
            <li><a href="#inscription">Procédure d'inscription</a></li>
            <li><router-link to="/register">Inscription en ligne</router-link></li>
            <li><router-link to="/login">Espace stagiaire / formateur</router-link></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="container footer-bottom-inner">
          <span>© {{ year }} INSFP Mohamed Tayeb Boucenna. Tous droits réservés.</span>
          <span lang="ar" dir="rtl">جميع الحقوق محفوظة</span>
        </div>
      </div>
    </footer>

    <!-- ASSISTANT LAUNCHER -->
    <Transition name="launcher">
      <button v-if="!isAssistantOpen" class="assistant-launcher" @click="openAssistant">
        <span class="launcher-robot"><AssistantRobot head-only /></span>
        <span class="launcher-label">Assistant IA</span>
      </button>
    </Transition>

    <!-- ASSISTANT (centered) -->
    <Transition name="modal">
      <div v-if="isAssistantOpen" class="assistant-overlay" @click.self="closeAssistant">
        <div class="assistant" role="dialog" aria-modal="true" aria-labelledby="assistant-title">
          <div class="assistant-head">
            <span class="head-robot"><AssistantRobot head-only :mood="isBotTyping ? 'thinking' : 'idle'" /></span>
            <div class="head-text">
              <h2 id="assistant-title" class="assistant-title">Assistant IA de l'INSFP</h2>
              <p class="assistant-sub"><span class="online-dot" aria-hidden="true"></span>{{ isBotTyping ? 'En train de répondre…' : 'En ligne' }}</p>
            </div>
            <button class="assistant-close" @click="closeAssistant" aria-label="Fermer l'assistant">
              <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <div class="assistant-body" ref="assistantMessagesContainer">
            <!-- The robot waits for the first question, then flies away -->
            <Transition name="robot-leave">
              <div v-if="!chatStarted" class="assistant-welcome">
                <div class="welcome-bubble" :key="welcomeText">{{ welcomeText }}</div>
                <AssistantRobot class="welcome-robot" :mood="chatInput.trim() ? 'listening' : 'idle'" />
                <div class="suggestions">
                  <button v-for="q in suggestions" :key="q" class="suggestion" @click="ask(q)">{{ q }}</button>
                </div>
              </div>
            </Transition>

            <template v-if="chatStarted">
              <TransitionGroup name="msg" tag="div" class="messages">
                <div v-for="(msg, index) in chatMessages" :key="index" :class="['msg-row', msg.isBot ? 'bot' : 'user']">
                  <span v-if="msg.isBot" class="msg-avatar"><AssistantRobot head-only /></span>
                  <div class="bubble" v-html="formatMessage(msg.text)"></div>
                </div>
                <div v-if="isBotTyping" key="typing" class="msg-row bot">
                  <span class="msg-avatar"><AssistantRobot head-only mood="thinking" /></span>
                  <div class="bubble typing" aria-label="L'assistant écrit"><span></span><span></span><span></span></div>
                </div>
              </TransitionGroup>
            </template>
          </div>

          <form class="assistant-foot" @submit.prevent="sendMessage">
            <input ref="assistantInput" v-model="chatInput" type="text" class="assistant-input" placeholder="Écrivez votre question…" aria-label="Votre question" />
            <button type="submit" class="send-btn" :disabled="!chatInput.trim() || isBotTyping" aria-label="Envoyer">
              <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/></svg>
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import apiClient from '@/api/axios'
import AssistantRobot from '@/components/common/AssistantRobot.vue'

// Fade-up on scroll. Usage: v-reveal or v-reveal="delayInMs".
const vReveal = {
  mounted(el, binding) {
    el.classList.add('reveal')
    if (binding.value) el.style.transitionDelay = `${binding.value}ms`
    if (!('IntersectionObserver' in window)) {
      el.classList.add('is-visible')
      return
    }
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          el.classList.add('is-visible')
          observer.disconnect()
          // Drop the stagger delay once revealed so hover effects react instantly.
          setTimeout(() => { el.style.transitionDelay = '' }, 800 + (binding.value || 0))
        }
      })
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' })
    observer.observe(el)
    el._revealObserver = observer
  },
  unmounted(el) {
    el._revealObserver?.disconnect()
  },
}

const year = new Date().getFullYear()
const isScrolled = ref(false)
const isMenuOpen = ref(false)

const navLinks = [
  { href: '#formations', label: 'Formations' },
  { href: '#modes', label: 'Modes de formation' },
  { href: '#inscription', label: 'Inscription' },
  { href: '#contact', label: 'Contact' },
]

const services = [
  { title: 'Espace stagiaire', desc: 'Emploi du temps, notes, absences, cours', to: '/login', icon: 'M22 10 12 5 2 10l10 5 10-5zM6 12v5c3 3 9 3 12 0v-5' },
  { title: 'Espace formateur', desc: 'Appel, saisie des notes, supports de cours', to: '/login', icon: 'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z' },
  { title: 'Administration', desc: 'Sessions, délibérations, documents', to: '/login', icon: 'M3 21h18M5 21V10M19 21V10M9 21v-7h6v7M2 10l10-7 10 7' },
  { title: 'Inscription en ligne', desc: "Avec votre numéro d'inscription", to: '/register', icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM19 8v6M22 11h-6' },
]

const keyFacts = [
  { label: 'Diplôme', value: "BTS — diplôme d'État" },
  { label: 'Durée', value: '5 semestres' },
  { label: 'Sessions', value: 'Février et septembre' },
  { label: 'Modes', value: 'Présentiel, apprentissage, cours du soir' },
]

const notices = [
  {
    tag: 'Inscriptions',
    title: 'Session de février 2027',
    text: "Les candidats retenus créent leur compte en ligne à l'aide du numéro d'inscription remis par l'administration. Le compte est activé après validation du dossier.",
    cta: "S'inscrire",
    to: '/register',
  },
  {
    tag: 'Scolarité',
    title: 'Emplois du temps du semestre',
    text: 'Les emplois du temps publiés par spécialité et par groupe sont consultables dans l\'espace stagiaire et dans l\'espace formateur.',
    cta: 'Consulter',
    to: '/login',
  },
  {
    tag: 'Évaluations',
    title: 'Notes et délibérations',
    text: "Les notes de contrôle et d'examen sont visibles après validation par le formateur. Les résultats des délibérations sont publiés dans l'espace stagiaire.",
    cta: 'Accéder à mon espace',
    to: '/login',
  },
]

const specialties = [
  { code: 'DEV', name: 'Développement Web et Mobile', desc: "Conception et réalisation d'applications web et mobiles : interfaces, services, bases de données.", icon: 'm16 18 6-6-6-6M8 6l-6 6 6 6' },
  { code: 'ASRI', name: 'Administration des Systèmes et Réseaux', desc: "Installation, administration et supervision des réseaux et des serveurs d'entreprise.", icon: 'M4 4h16v6H4zM4 14h16v6H4zM8 7h.01M8 17h.01' },
  { code: 'BDD', name: 'Administration des Bases de Données', desc: "Conception, exploitation et optimisation des systèmes de gestion de bases de données.", icon: 'M12 8c5 0 9-1.3 9-3s-4-3-9-3-9 1.3-9 3 4 3 9 3zM3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5M3 12c0 1.7 4 3 9 3s9-1.3 9-3' },
  { code: 'SEC', name: 'Sécurité Informatique', desc: "Protection des systèmes d'information, audit de sécurité et gestion des incidents.", icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z' },
  { code: 'MNT', name: 'Maintenance Informatique', desc: 'Diagnostic, dépannage et maintenance du matériel et des parcs informatiques.', icon: 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z' },
]

const modes = [
  { title: 'Formation présentielle', text: "Formation à plein temps au sein de l'institut, alternant enseignements théoriques et travaux pratiques en laboratoire, complétée par un stage pratique." },
  { title: 'Formation par apprentissage', text: "Formation en alternance entre l'institut et un organisme employeur, sous contrat d'apprentissage, avec un suivi pédagogique assuré par l'établissement." },
  { title: 'Cours du soir', text: 'Formation organisée en horaires du soir, destinée aux travailleurs et aux personnes souhaitant se qualifier en parallèle de leur activité.' },
]

const steps = [
  { title: 'Retrait du numéro d\'inscription', text: "Le numéro est délivré par l'administration pour une session, une spécialité et un mode de formation." },
  { title: 'Création du compte', text: "Le candidat saisit son numéro d'inscription et ses informations personnelles sur la plateforme." },
  { title: 'Validation du dossier', text: "L'administration vérifie le dossier et active le compte du stagiaire." },
  { title: 'Accès à l\'espace stagiaire', text: 'Le stagiaire complète son profil et accède à son emploi du temps, à ses cours et à ses résultats.' },
]

const roles = [
  { title: 'Stagiaires', items: ['Emploi du temps de la semaine', 'Notes de contrôle et d\'examen', 'Suivi des absences', 'Cours, devoirs et documents', 'Résultats des délibérations'] },
  { title: 'Formateurs', items: ['Modules et groupes assignés', 'Appel par séance', 'Création des épreuves et saisie des notes', 'Dépôt des supports de cours', 'Correction des devoirs'] },
  { title: 'Administration', items: ['Sessions et spécialités', 'Numéros et validation des inscriptions', 'Emplois du temps et affectation des salles', 'Délibérations semestrielles', 'Diffusion des avis et documents'] },
]

// ── Assistant ───────────────────────────────────────────────
const isAssistantOpen = ref(false)
const chatInput = ref('')
const isBotTyping = ref(false)
const assistantMessagesContainer = ref(null)
const assistantInput = ref(null)
const chatMessages = ref([])
const chatStarted = ref(false)
const welcomeText = computed(() => chatInput.value.trim()
  ? 'Je vous écoute… ✍️'
  : "Bonjour ! Je suis l'assistant de l'INSFP. Posez-moi votre question.")
const suggestions = [
  'Quelles spécialités sont proposées ?',
  "Comment s'inscrire ?",
  'Quand ouvrent les sessions ?',
]

const scrollToBottom = async () => {
  await nextTick()
  const el = assistantMessagesContainer.value
  if (el) el.scrollTop = el.scrollHeight
}

const openAssistant = async () => {
  isAssistantOpen.value = true
  isMenuOpen.value = false
  await nextTick()
  assistantInput.value?.focus()
}

// Bot replies may contain **bold** markdown: escape everything, then allow only <strong>.
const formatMessage = (text) => String(text)
  .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
  .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')

// Lock page scroll behind the open assistant.
watch(isAssistantOpen, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
})

const closeAssistant = () => {
  isAssistantOpen.value = false
}

const ask = (question) => {
  chatInput.value = question
  sendMessage()
}

const sendMessage = async () => {
  const userQuestion = chatInput.value.trim()
  if (!userQuestion || isBotTyping.value) return

  chatStarted.value = true
  chatMessages.value.push({ text: userQuestion, isBot: false })
  chatInput.value = ''
  isBotTyping.value = true
  scrollToBottom()

  try {
    const response = await apiClient.post('/api/chatbot', { message: userQuestion })
    chatMessages.value.push({ text: response.data.reply || "Désolé, je n'ai pas pu vous répondre.", isBot: true })
  } catch (error) {
    console.error('Erreur Chatbot:', error)
    chatMessages.value.push({ text: "Le service est momentanément indisponible. Veuillez réessayer plus tard.", isBot: true })
  } finally {
    isBotTyping.value = false
    scrollToBottom()
  }
}

// ── Page ────────────────────────────────────────────────────
const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' })
const handleScroll = () => { isScrolled.value = window.scrollY > 180 }
const handleKeydown = (e) => { if (e.key === 'Escape') closeAssistant() }

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&family=Noto+Naskh+Arabic:wght@500;600&display=swap');

.landing {
  --navy: #0f3460;
  --navy-deep: #0a2443;
  --teal: #0e7c7b;
  --gold: #c9971c;
  --gold-soft: #f6ecd2;
  --green-dz: #006233;
  --red-dz: #d21034;
  --ink: #18212f;
  --muted: #566173;
  --line: #e1e6ed;
  --soft: #f4f6f9;
  --white: #ffffff;
  --radius: 6px;
  --serif: 'Source Serif 4', Georgia, 'Times New Roman', serif;
  --sans: 'IBM Plex Sans', 'Segoe UI', Arial, sans-serif;
  --arabic: 'Noto Naskh Arabic', 'Traditional Arabic', serif;

  font-family: var(--sans);
  color: var(--ink);
  background: var(--white);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

.container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
.ico { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
[lang="ar"] { font-family: var(--arabic); }

/* Buttons */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  padding: 10px 18px; border-radius: var(--radius); border: 1px solid transparent;
  font: 600 14px/1.2 var(--sans); text-decoration: none; cursor: pointer;
  transition: background-color .15s, border-color .15s, color .15s;
  white-space: nowrap;
}
.btn:focus-visible, .nav-link:focus-visible, .service-item:focus-visible, .suggestion:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
.btn-lg { padding: 13px 22px; font-size: 15px; }
.btn-primary { background: var(--navy); color: var(--white); }
.btn-primary:hover { background: var(--navy-deep); }
.btn-primary:disabled { opacity: .5; cursor: not-allowed; }
.btn-outline { border-color: var(--navy); color: var(--navy); background: var(--white); }
.btn-outline:hover { background: var(--soft); }
.btn-gold { background: var(--gold); color: var(--navy-deep); }
.btn-gold:hover { background: #b5861a; }
.btn-outline-light { border-color: rgba(255,255,255,.55); color: var(--white); background: transparent; }
.btn-outline-light:hover { background: rgba(255,255,255,.1); }

/* Header */
.site-header {
  position: sticky; top: 0; z-index: 40;
  background: rgba(255, 255, 255, .94); backdrop-filter: saturate(180%) blur(10px);
  border-bottom: 1px solid transparent; transition: border-color .25s, box-shadow .25s;
}
.site-header.scrolled { border-bottom-color: var(--line); box-shadow: 0 6px 20px rgba(10, 36, 67, .07); }
.header-inner { position: relative; display: flex; align-items: center; gap: 24px; height: 72px; }
.brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit; flex-shrink: 0; }
.brand-logo { width: 44px; height: 44px; object-fit: contain; transition: transform .4s ease; }
.brand:hover .brand-logo { transform: rotate(-6deg) scale(1.05); }
.brand-text { display: flex; flex-direction: column; line-height: 1.15; }
.brand-name { font: 700 19px/1.1 var(--serif); color: var(--navy); letter-spacing: .03em; }
.brand-sub { font-size: 12.5px; color: var(--muted); font-weight: 500; }
.header-nav { display: flex; align-items: center; gap: 2px; flex: 1; }
.nav-link {
  position: relative; display: inline-flex; align-items: center; gap: 8px;
  padding: 9px 12px; color: var(--ink); text-decoration: none; font: 500 14.5px/1 var(--sans); white-space: nowrap;
  background: none; border: 0; border-radius: var(--radius); cursor: pointer;
}
.nav-link::after {
  content: ''; position: absolute; left: 12px; right: 12px; bottom: 3px; height: 2px; background: var(--gold);
  transform: scaleX(0); transform-origin: left; transition: transform .25s ease;
}
.nav-link:hover { color: var(--navy); }
.nav-link:hover::after { transform: scaleX(1); }
.nav-ai { margin-left: auto; color: var(--teal); font-weight: 600; background: #e6f4f3; border-radius: 999px; padding: 9px 14px; }
.nav-ai::after { display: none; }
.nav-ai:hover { background: #d4ecea; color: var(--teal); }
.ai-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--teal); animation: ping 1.8s infinite; }
.header-actions { display: flex; gap: 8px; flex-shrink: 0; }
.btn-ghost { color: var(--navy); background: transparent; }
.btn-ghost:hover { background: var(--soft); }
.menu-toggle { display: none; margin-left: auto; background: none; border: 1px solid var(--line); border-radius: var(--radius); padding: 8px; color: var(--navy); cursor: pointer; }
.menu-toggle .ico { width: 22px; height: 22px; }
.nav-mobile-actions { display: none; }
@keyframes ping {
  0% { box-shadow: 0 0 0 0 rgba(14, 124, 123, .55); }
  70% { box-shadow: 0 0 0 8px rgba(14, 124, 123, 0); }
  100% { box-shadow: 0 0 0 0 rgba(14, 124, 123, 0); }
}

/* Hero */
.hero { position: relative; background: linear-gradient(160deg, var(--navy-deep) 0%, var(--navy) 70%, #134a7a 100%); color: var(--white); padding: 64px 0 0; overflow: hidden; }
.hero-pattern {
  position: absolute; inset: 0; opacity: .07; pointer-events: none;
  background-image:
    linear-gradient(45deg, #fff 1px, transparent 1px),
    linear-gradient(-45deg, #fff 1px, transparent 1px);
  background-size: 28px 28px;
  mask-image: linear-gradient(to left, #000 0%, transparent 70%);
}
.hero-grid { position: relative; display: grid; grid-template-columns: 1.25fr 1fr; gap: 56px; align-items: center; }
.eyebrow { margin: 0 0 10px; font-size: 13px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--teal); }
.eyebrow-light { color: var(--gold); }
.hero-title { margin: 0 0 18px; font: 700 clamp(30px, 4.2vw, 46px)/1.15 var(--serif); letter-spacing: -.01em; text-wrap: balance; }
.hero-lead { margin: 0 0 28px; font-size: 17px; color: #d3dceb; max-width: 60ch; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }

.services-card { background: var(--white); color: var(--ink); border-radius: 8px; border-top: 4px solid var(--gold); box-shadow: 0 18px 40px rgba(4, 18, 36, .35); padding: 22px 22px 10px; }
.services-title { margin: 0 0 8px; font: 700 18px/1.3 var(--serif); color: var(--navy); }
.services-list { list-style: none; margin: 0; padding: 0; }
.services-list li + li { border-top: 1px solid var(--line); }
.service-item { display: flex; align-items: center; gap: 14px; padding: 14px 4px; text-decoration: none; color: inherit; border-radius: var(--radius); }
.service-item:hover .service-body strong { color: var(--teal); }
.service-item:hover .chevron { transform: translateX(3px); color: var(--teal); }
.service-icon { display: grid; place-items: center; width: 40px; height: 40px; border-radius: var(--radius); background: #e8f1f8; color: var(--navy); flex-shrink: 0; }
.service-body { display: flex; flex-direction: column; flex: 1; min-width: 0; }
.service-body strong { font-size: 15px; color: var(--navy); transition: color .15s; }
.service-body span { font-size: 13.5px; color: var(--muted); }
.chevron { color: #9aa5b4; transition: transform .15s, color .15s; }

.key-facts { position: relative; display: grid; grid-template-columns: repeat(4, 1fr); margin: 56px 0 0; border-top: 1px solid rgba(255,255,255,.15); }
.fact { padding: 22px 20px 26px 0; }
.fact + .fact { padding-left: 20px; border-left: 1px solid rgba(255,255,255,.15); }
.fact dt { font-size: 12.5px; text-transform: uppercase; letter-spacing: .08em; color: #9fb0c6; margin-bottom: 4px; }
.fact dd { margin: 0; font-weight: 600; font-size: 15.5px; }

/* Sections */
.section { padding: 80px 0; }
.section-soft { background: var(--soft); }
.section-navy { background: var(--navy-deep); color: var(--white); }
.section-head { max-width: 720px; margin-bottom: 40px; }
.section-title { margin: 0; font: 700 clamp(26px, 3vw, 34px)/1.2 var(--serif); color: var(--navy); text-wrap: balance; }
.section-title-light { color: var(--white); }
.section-lead { margin: 14px 0 0; color: var(--muted); font-size: 16.5px; }
.section-lead-light { color: #c3cfdf; }

/* Notices */
.notices { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.notice { display: flex; flex-direction: column; background: var(--white); border: 1px solid var(--line); border-left: 4px solid var(--teal); border-radius: var(--radius); padding: 22px 22px 20px; }
.notice-tag { align-self: flex-start; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--teal); margin-bottom: 8px; }
.notice-title { margin: 0 0 8px; font: 700 18px/1.35 var(--serif); color: var(--navy); }
.notice-text { margin: 0 0 16px; color: var(--muted); font-size: 15px; flex: 1; }
.notice-link { font-weight: 600; font-size: 14.5px; color: var(--navy); text-decoration: none; }
.notice-link:hover { color: var(--teal); text-decoration: underline; }

/* Specialties */
/* Flex so an incomplete last row (5 specialties) stays centred */
.spec-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
.spec-grid > .spec-card { flex: 0 1 calc((100% - 40px) / 3); }
.spec-card { display: flex; flex-direction: column; border: 1px solid var(--line); border-radius: 8px; padding: 24px; background: var(--white); transition: border-color .15s, box-shadow .15s; }
.spec-card:hover { border-color: #bccad9; box-shadow: 0 8px 24px rgba(15, 52, 96, .08); }
.spec-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.spec-icon { display: grid; place-items: center; width: 44px; height: 44px; border-radius: var(--radius); background: var(--navy); color: var(--white); }
.spec-icon .ico { width: 20px; height: 20px; }
.spec-code { font: 600 12.5px/1 var(--sans); letter-spacing: .08em; background: var(--gold-soft); padding: 6px 9px; border-radius: 4px; color: #7a5a0c; }
.spec-name { margin: 0 0 8px; font: 700 19px/1.3 var(--serif); color: var(--navy); }
.spec-desc { margin: 0 0 18px; color: var(--muted); font-size: 15px; flex: 1; }
.spec-meta { display: flex; gap: 8px; list-style: none; margin: 0; padding: 14px 0 0; border-top: 1px solid var(--line); font-size: 13px; color: var(--ink); font-weight: 500; }
.spec-meta li + li::before { content: '·'; margin-right: 8px; color: #9aa5b4; }

/* Modes */
.modes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.mode { background: var(--white); border: 1px solid var(--line); border-radius: 8px; padding: 26px; }
.mode-num { display: block; font: 700 28px/1 var(--serif); color: var(--gold); margin-bottom: 14px; }
.mode-title { margin: 0 0 8px; font: 700 19px/1.3 var(--serif); color: var(--navy); }
.mode-text { margin: 0; color: var(--muted); font-size: 15px; }

/* Steps */
.steps { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; counter-reset: step; }
.step { position: relative; display: flex; flex-direction: column; gap: 14px; padding: 0 24px 0 0; }
.step::before { content: ''; position: absolute; top: 20px; left: 48px; right: 8px; height: 2px; background: var(--line); }
.step:last-child::before { display: none; }
.step-num { position: relative; display: grid; place-items: center; width: 40px; height: 40px; border-radius: 50%; background: var(--navy); color: var(--white); font-weight: 700; }
.step-title { margin: 0 0 6px; font: 700 17px/1.35 var(--serif); color: var(--navy); }
.step-text { margin: 0; color: var(--muted); font-size: 14.5px; }
.steps-cta { display: flex; align-items: center; flex-wrap: wrap; gap: 16px; margin-top: 40px; padding: 22px 24px; background: var(--soft); border-radius: 8px; }
.steps-note { color: var(--muted); font-size: 14.5px; }

/* Roles */
.roles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.role { border: 1px solid rgba(255,255,255,.14); border-top: 3px solid var(--gold); border-radius: 8px; padding: 24px; background: rgba(255,255,255,.03); }
.role-title { margin: 0 0 14px; font: 700 20px/1.3 var(--serif); }
.role-list { list-style: none; margin: 0; padding: 0; }
.role-list li { position: relative; padding: 7px 0 7px 22px; color: #d3dceb; font-size: 15px; border-top: 1px solid rgba(255,255,255,.08); }
.role-list li:first-child { border-top: 0; }
.role-list li::before { content: ''; position: absolute; left: 2px; top: 15px; width: 8px; height: 8px; border-radius: 2px; background: var(--teal); }

/* Footer */
.footer { background: #071a31; color: #c3cfdf; font-size: 14.5px; }
.footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 40px; padding-top: 56px; padding-bottom: 40px; }
.footer-logo { width: 56px; height: 56px; object-fit: contain; background: var(--white); border-radius: 8px; padding: 4px; }
.footer-name { margin: 14px 0 4px; font: 700 18px/1.3 var(--serif); color: var(--white); }
.footer-ar { margin: 0 0 10px; font-size: 15px; text-align: left; }
.footer-tutelle { margin: 0; color: #8fa1b8; font-size: 13.5px; }
.footer-heading { margin: 0 0 14px; font: 600 13px/1 var(--sans); text-transform: uppercase; letter-spacing: .1em; color: var(--gold); }
.footer-list { list-style: none; margin: 0; padding: 0; }
.footer-list li { display: flex; align-items: center; gap: 10px; padding: 5px 0; }
.footer-list .ico { color: #8fa1b8; width: 16px; height: 16px; }
.footer-links a { color: #c3cfdf; text-decoration: none; }
.footer-links a:hover { color: var(--white); text-decoration: underline; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,.1); font-size: 13px; color: #8fa1b8; }
.footer-bottom-inner { display: flex; justify-content: space-between; gap: 16px; padding-top: 18px; padding-bottom: 18px; }

/* AI section */
.ai-section { background: linear-gradient(180deg, var(--white) 0%, #eaf4f4 100%); overflow: hidden; }
.ai-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 48px; align-items: center; }
.ai-text .section-title { max-width: 22ch; }
.ai-points { list-style: none; padding: 0; margin: 22px 0 28px; display: flex; flex-wrap: wrap; gap: 10px; }
.ai-points li { padding: 7px 13px; border-radius: 999px; background: var(--white); border: 1px solid var(--line); font-size: 14px; font-weight: 500; color: var(--navy); }
.ai-cta .ai-dot { background: #5fe0d6; }
.ai-stage {
  position: relative; display: grid; place-items: center; width: 100%; max-width: 420px; aspect-ratio: 1; margin: 0 auto;
  border: 0; border-radius: 50%; cursor: pointer;
  background: radial-gradient(circle at 50% 55%, var(--white) 0%, #dff0ef 58%, transparent 59%);
}
.ai-stage:focus-visible { outline: 3px solid var(--gold); outline-offset: 4px; }
.ai-ring { position: absolute; border-radius: 50%; pointer-events: none; }
.ai-ring-1 { inset: 6%; border: 2px dashed rgba(14, 124, 123, .28); animation: spin 32s linear infinite; }
.ai-ring-2 { inset: -2%; border: 1px solid rgba(15, 52, 96, .12); animation: spin 48s linear infinite reverse; }
.ai-ring-2::before {
  content: ''; position: absolute; top: 12%; left: 12%; width: 12px; height: 12px; border-radius: 50%;
  background: var(--gold); box-shadow: 0 0 0 4px rgba(201, 151, 28, .2);
}
.ai-stage-robot { position: relative; z-index: 1; width: 56%; transition: transform .3s ease; }
.ai-stage:hover .ai-stage-robot { transform: scale(1.05); }
.ai-bubble {
  position: absolute; top: 8%; right: 0; z-index: 2;
  background: var(--white); color: var(--navy); font: 600 14.5px/1.3 var(--sans);
  padding: 10px 14px; border-radius: 14px 14px 14px 4px; box-shadow: 0 10px 24px rgba(10, 36, 67, .14);
  animation: bob 3.2s ease-in-out infinite;
}

/* Assistant launcher */
.assistant-launcher {
  position: fixed; right: 24px; bottom: 24px; z-index: 50;
  display: inline-flex; align-items: center; gap: 10px; padding: 6px 18px 6px 6px;
  background: var(--navy); color: var(--white); border: 0; border-radius: 999px;
  font: 600 14px/1 var(--sans); cursor: pointer; box-shadow: 0 10px 28px rgba(10, 36, 67, .35);
  transition: background-color .2s, transform .2s;
}
.assistant-launcher:hover { background: var(--navy-deep); transform: translateY(-2px); }
.launcher-robot { display: grid; place-items: center; width: 44px; height: 44px; padding: 5px; border-radius: 50%; background: var(--white); animation: nudge 5s ease-in-out infinite; }
.launcher-enter-active, .launcher-leave-active { transition: opacity .3s ease, transform .3s ease; }
.launcher-enter-from, .launcher-leave-to { opacity: 0; transform: translateY(20px) scale(.9); }

/* Assistant modal (centered) */
.assistant-overlay { position: fixed; inset: 0; z-index: 60; display: grid; place-items: center; padding: 24px; background: rgba(7, 26, 49, .55); backdrop-filter: blur(4px); }
.assistant { width: 100%; max-width: 560px; height: min(680px, calc(100vh - 48px)); display: flex; flex-direction: column; background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 30px 80px rgba(4, 18, 36, .45); }
.modal-enter-active, .modal-leave-active { transition: opacity .3s ease; }
.modal-enter-active .assistant, .modal-leave-active .assistant { transition: transform .4s cubic-bezier(.2, .8, .2, 1.1), opacity .3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .assistant, .modal-leave-to .assistant { opacity: 0; transform: translateY(28px) scale(.94); }

.assistant-head { display: flex; align-items: center; gap: 12px; padding: 14px 16px 14px 18px; background: var(--navy); color: var(--white); }
.head-robot { width: 42px; height: 42px; padding: 5px; border-radius: 50%; background: var(--white); flex-shrink: 0; }
.head-text { flex: 1; min-width: 0; }
.assistant-title { margin: 0; font: 700 16.5px/1.25 var(--serif); }
.assistant-sub { display: flex; align-items: center; gap: 6px; margin: 3px 0 0; font-size: 12.5px; color: #c3cfdf; }
.online-dot { width: 7px; height: 7px; border-radius: 50%; background: #3ddc97; }
.assistant-close { background: none; border: 0; color: var(--white); cursor: pointer; padding: 6px; border-radius: 50%; transition: background-color .15s, transform .2s; }
.assistant-close:hover { background: rgba(255, 255, 255, .12); transform: rotate(90deg); }
.assistant-body { position: relative; flex: 1; overflow-y: auto; padding: 20px; background: var(--soft); }

.assistant-welcome { display: flex; flex-direction: column; align-items: center; text-align: center; padding-top: 8px; }
.welcome-bubble {
  position: relative; max-width: 340px; padding: 12px 16px; border-radius: 14px;
  background: var(--white); border: 1px solid var(--line); color: var(--navy); font: 600 15px/1.4 var(--sans);
  box-shadow: 0 8px 20px rgba(10, 36, 67, .08); animation: pop .35s cubic-bezier(.2, .8, .2, 1.2);
}
.welcome-bubble::after {
  content: ''; position: absolute; left: 50%; bottom: -7px; width: 12px; height: 12px; background: var(--white);
  border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); transform: translateX(-50%) rotate(45deg);
}
.welcome-robot { width: 170px; margin: 16px 0 18px; }
.suggestions { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
.suggestion {
  padding: 9px 14px; border: 1px solid var(--line); border-radius: 999px; background: var(--white);
  color: var(--navy); font: 500 13.5px/1.3 var(--sans); cursor: pointer; transition: border-color .15s, transform .15s;
}
.suggestion:hover { border-color: var(--teal); transform: translateY(-2px); }

/* The robot flies away once the first question is sent */
.robot-leave-leave-active { position: absolute; top: 20px; left: 20px; right: 20px; transition: opacity .6s ease, transform .6s cubic-bezier(.5, 0, .75, 0); }
.robot-leave-leave-to { opacity: 0; transform: translateY(-140px) scale(.55) rotate(-12deg); }

.messages { display: flex; flex-direction: column; gap: 12px; }
.msg-row { display: flex; align-items: flex-end; gap: 8px; }
.msg-row.user { justify-content: flex-end; }
.msg-avatar { width: 30px; height: 30px; padding: 3px; border-radius: 50%; background: var(--white); border: 1px solid var(--line); flex-shrink: 0; }
.bubble { max-width: 80%; padding: 10px 14px; border-radius: 14px; font-size: 14.5px; line-height: 1.55; white-space: pre-wrap; word-wrap: break-word; }
.msg-row.user .bubble { background: var(--navy); color: var(--white); border-bottom-right-radius: 4px; }
.msg-row.bot .bubble { background: var(--white); border: 1px solid var(--line); border-bottom-left-radius: 4px; }
.bubble.typing { display: flex; gap: 4px; padding: 14px; }
.bubble.typing span { width: 7px; height: 7px; border-radius: 50%; background: #9aa5b4; animation: typing 1.2s infinite; }
.bubble.typing span:nth-child(2) { animation-delay: .2s; }
.bubble.typing span:nth-child(3) { animation-delay: .4s; }
.msg-enter-active { transition: opacity .35s ease, transform .35s cubic-bezier(.2, .8, .2, 1); }
.msg-enter-from { opacity: 0; transform: translateY(12px) scale(.97); }

.assistant-foot { display: flex; gap: 8px; padding: 12px; border-top: 1px solid var(--line); background: var(--white); }
.assistant-input { flex: 1; min-width: 0; padding: 12px 16px; border: 1px solid var(--line); border-radius: 999px; font: 400 14.5px var(--sans); color: var(--ink); transition: border-color .15s, box-shadow .15s; }
.assistant-input:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14, 124, 123, .15); }
.send-btn { display: grid; place-items: center; width: 46px; height: 46px; border: 0; border-radius: 50%; background: var(--navy); color: var(--white); cursor: pointer; transition: background-color .15s, transform .15s; flex-shrink: 0; }
.send-btn:hover:not(:disabled) { background: var(--teal); transform: scale(1.06); }
.send-btn:disabled { opacity: .4; cursor: not-allowed; }

/* Motion */
.hero-in { opacity: 0; animation: fadeUp .8s cubic-bezier(.2, .7, .2, 1) forwards; animation-delay: var(--d, 0s); }
.hero-in-right { opacity: 0; animation: fadeLeft .9s cubic-bezier(.2, .7, .2, 1) forwards; animation-delay: var(--d, 0s); }
.reveal { opacity: 0; transform: translateY(26px); transition: opacity .7s cubic-bezier(.2, .7, .2, 1), transform .7s cubic-bezier(.2, .7, .2, 1), border-color .15s, box-shadow .2s; }
.reveal.is-visible { opacity: 1; transform: none; }
.spec-card.is-visible:hover, .notice.is-visible:hover, .mode.is-visible:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(15, 52, 96, .1); }
.hero-pattern { animation: drift 40s linear infinite; }

@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
@keyframes fadeLeft { from { opacity: 0; transform: translateX(36px); } to { opacity: 1; transform: none; } }
@keyframes drift { to { background-position: 280px 280px; } }
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
@keyframes pop { from { opacity: 0; transform: scale(.85); } to { opacity: 1; transform: none; } }
@keyframes typing { 0%, 80%, 100% { opacity: .3; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-3px); } }
@keyframes nudge { 0%, 84%, 100% { transform: rotate(0); } 88% { transform: rotate(-14deg); } 92% { transform: rotate(10deg); } 96% { transform: rotate(-6deg); } }

/* Responsive */
@media (max-width: 1024px) {
  .hero-grid { grid-template-columns: 1fr; gap: 40px; }
  .services-card { max-width: 560px; }
  .key-facts { grid-template-columns: repeat(2, 1fr); }
  .fact:nth-child(3) { padding-left: 0; border-left: 0; }
  .fact:nth-child(n+3) { border-top: 1px solid rgba(255,255,255,.15); }
  .notices, .modes, .roles { grid-template-columns: repeat(2, 1fr); }
  .spec-grid > .spec-card { flex-basis: calc((100% - 20px) / 2); }
  .steps { grid-template-columns: repeat(2, 1fr); row-gap: 32px; }
  .step:nth-child(2)::before { display: none; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
  .footer-brand { grid-column: 1 / -1; }
}

@media (max-width: 1100px) {
  .header-nav {
    display: none; position: absolute; top: 72px; left: 0; right: 0;
    flex-direction: column; align-items: stretch; gap: 0; padding: 8px 24px 20px;
    background: var(--white); border-bottom: 1px solid var(--line); box-shadow: 0 16px 30px rgba(10, 36, 67, .12);
  }
  .header-nav.open { display: flex; animation: fadeUp .25s ease; }
  .nav-link { padding: 14px 4px; border-bottom: 1px solid var(--line); border-radius: 0; }
  .nav-link::after { display: none; }
  .nav-ai { margin: 12px 0 0; justify-content: center; border-bottom: 0; border-radius: 999px; }
  .nav-mobile-actions { display: flex; flex-direction: column; gap: 10px; margin-top: 12px; }
  .header-actions { display: none; }
  .menu-toggle { display: inline-flex; }
}

@media (max-width: 860px) {
  .ai-grid { grid-template-columns: 1fr; gap: 24px; }
  .ai-stage { max-width: 320px; }
}

@media (max-width: 640px) {
  .container { padding: 0 16px; }
  .brand-sub { font-size: 12.5px; }
  .hero { padding-top: 44px; }
  .hero-lead { font-size: 16px; }
  .hero-actions .btn { width: 100%; }
  .key-facts { grid-template-columns: 1fr; }
  .fact, .fact + .fact { padding: 16px 0; border-left: 0; }
  .fact + .fact { border-top: 1px solid rgba(255,255,255,.15); }
  .section { padding: 56px 0; }
  .notices, .modes, .roles, .steps { grid-template-columns: 1fr; }
  .spec-grid > .spec-card { flex-basis: 100%; }
  .step { flex-direction: row; padding: 0; }
  .step::before { top: 44px; bottom: -28px; left: 19px; right: auto; width: 2px; height: auto; }
  .step:nth-child(2)::before { display: block; }
  .step:last-child::before { display: none; }
  .steps-cta .btn { width: 100%; }
  .footer-grid { grid-template-columns: 1fr; gap: 28px; }
  .footer-bottom-inner { flex-direction: column; gap: 4px; }
  .header-nav { padding: 8px 16px 20px; }
  .assistant-overlay { padding: 0; }
  .assistant { max-width: none; height: 100%; border-radius: 0; }
  .assistant-launcher { right: 16px; bottom: 16px; }
  .ai-bubble { right: -4px; font-size: 13.5px; }
}

@media (prefers-reduced-motion: reduce) {
  * { transition: none !important; animation: none !important; }
  .reveal, .hero-in, .hero-in-right { opacity: 1 !important; transform: none !important; }
}
</style>
