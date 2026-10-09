<template>
  <div class="auth">
    <!-- Brand panel (hidden on small screens) -->
    <aside class="auth-brand">
      <div class="auth-brand-pattern" aria-hidden="true"></div>

      <router-link to="/" class="auth-brand-logo">
        <img src="/logo.png" alt="Logo INSFP" width="48" height="48" />
        <span>
          <strong>INSFP</strong>
          <small>Mohamed Tayeb Boucenna</small>
        </span>
      </router-link>

      <div class="auth-brand-body">
        <p class="auth-brand-eyebrow">{{ t('auth.eyebrow') }}</p>
        <h2 class="auth-brand-title">{{ panelTitle || t('auth.panelLogin') }}</h2>
        <ul class="auth-brand-points">
          <li v-for="point in points" :key="point">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
            {{ point }}
          </li>
        </ul>
      </div>

      <p v-if="locale !== 'ar'" class="auth-brand-foot" lang="ar" dir="rtl">المعهد الوطني المتخصص في التكوين المهني</p>
      <p v-else class="auth-brand-foot">{{ t('landing.instituteName') }}</p>
    </aside>

    <!-- Form side -->
    <main class="auth-main">
      <div class="auth-top">
        <router-link to="/" class="auth-mobile-logo">
          <img src="/logo.png" alt="Logo INSFP" width="36" height="36" />
          <strong>INSFP</strong>
        </router-link>
        <div class="auth-top-actions">
          <LanguageSwitcher />
          <router-link to="/" class="auth-back">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
            {{ t('auth.home') }}
          </router-link>
        </div>
      </div>

      <div class="auth-card" :style="{ maxWidth: width }">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'

defineProps({
  panelTitle: { type: String, default: '' },
  width: { type: String, default: '400px' },
})

const { t, tm, rt, locale } = useI18n()
const points = computed(() => tm('auth.points').map((p) => rt(p)))
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&family=Noto+Naskh+Arabic:wght@500&display=swap');

/* Shared by the login and register pages (not scoped: the form markup lives in the pages). */
.auth {
  --navy: #0f3460;
  --navy-deep: #0a2443;
  --teal: #0e7c7b;
  --gold: #c9971c;
  --ink: #18212f;
  --muted: #5b6675;
  --line: #dde3ea;
  --soft: #f4f6f9;
  --danger: #c0392b;
  --success: #1e7d4f;
  --serif: 'Source Serif 4', Georgia, serif;
  --sans: 'IBM Plex Sans', 'Segoe UI', Arial, sans-serif;

  display: grid;
  grid-template-columns: minmax(340px, 0.9fr) 1.1fr;
  min-height: 100vh;
  font-family: var(--sans);
  color: var(--ink);
  background: #fff;
}
[dir="rtl"] .auth {
  --sans: 'IBM Plex Sans Arabic', 'IBM Plex Sans', Tahoma, sans-serif;
  --serif: 'Noto Naskh Arabic', 'Source Serif 4', serif;
}
[dir="rtl"] .auth-brand-eyebrow { letter-spacing: normal; }
[dir="rtl"] .auth-back svg, [dir="rtl"] .reg-arrow { transform: scaleX(-1); }
.auth-top-actions { display: flex; align-items: center; gap: 10px; }

/* Brand panel */
.auth-brand {
  position: relative; overflow: hidden;
  display: flex; flex-direction: column; justify-content: space-between; gap: 32px;
  padding: 40px 48px;
  background: linear-gradient(160deg, var(--navy-deep) 0%, var(--navy) 70%, #134a7a 100%);
  color: #fff;
}
.auth-brand-pattern {
  position: absolute; inset: 0; opacity: .07; pointer-events: none;
  background-image: linear-gradient(45deg, #fff 1px, transparent 1px), linear-gradient(-45deg, #fff 1px, transparent 1px);
  background-size: 28px 28px;
  mask-image: linear-gradient(to top, #000 0%, transparent 75%);
  animation: auth-drift 40s linear infinite;
}
.auth-brand > :not(.auth-brand-pattern) { position: relative; }
.auth-brand-logo { display: inline-flex; align-items: center; gap: 12px; color: #fff; text-decoration: none; }
.auth-brand-logo img { width: 48px; height: 48px; object-fit: contain; background: #fff; border-radius: 10px; padding: 4px; }
.auth-brand-logo span { display: flex; flex-direction: column; line-height: 1.2; }
.auth-brand-logo strong { font: 700 20px/1.1 var(--serif); letter-spacing: .03em; }
.auth-brand-logo small { font-size: 13px; color: #c3cfdf; }
.auth-brand-eyebrow { margin: 0 0 10px; font-size: 12.5px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gold); }
.auth-brand-title { margin: 0 0 24px; font: 700 clamp(26px, 2.6vw, 34px)/1.2 var(--serif); max-width: 16ch; color: #fff; }
.auth-brand-points { list-style: none; margin: 0; padding: 0; display: grid; gap: 12px; }
.auth-brand-points li { display: flex; align-items: flex-start; gap: 10px; color: #d3dceb; font-size: 15px; }
.auth-brand-points svg { flex-shrink: 0; width: 20px; height: 20px; padding: 3px; border-radius: 50%; background: rgba(95, 224, 214, .18); fill: none; stroke: #5fe0d6; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
.auth-brand-foot { margin: 0; font-family: 'Noto Naskh Arabic', serif; font-size: 15px; color: #9fb0c6; text-align: start; }

/* Form side */
.auth-main { display: flex; flex-direction: column; padding: 24px 40px; background: #fff; }
.auth-top { display: flex; justify-content: flex-end; align-items: center; }
.auth-mobile-logo { display: none; align-items: center; gap: 8px; text-decoration: none; color: var(--navy); }
.auth-mobile-logo img { width: 36px; height: 36px; object-fit: contain; }
.auth-mobile-logo strong { font: 700 18px var(--serif); }
.auth-back { display: inline-flex; align-items: center; gap: 6px; color: var(--muted); font-size: 14px; font-weight: 500; text-decoration: none; padding: 6px 10px; border-radius: 6px; }
.auth-back:hover { color: var(--navy); background: var(--soft); }
.auth-back svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.auth-card { width: 100%; margin: auto; padding: 16px 0; animation: auth-in .5s cubic-bezier(.2, .7, .2, 1); }

/* Headings */
.auth-title { margin: 0; font: 700 28px/1.2 var(--serif); color: var(--navy); }
.auth-subtitle { margin: 6px 0 24px; color: var(--muted); font-size: 15px; line-height: 1.5; }

/* Fields */
.auth-form { display: grid; gap: 16px; }
.auth-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.auth-field { display: grid; gap: 6px; min-width: 0; align-content: start; }
.auth label {
  /* override the global uppercase label style (main.css) */
  text-transform: none; letter-spacing: normal; font-size: 14px;
}
.auth .auth-label {
  text-transform: none; letter-spacing: normal;
  font: 500 14px/1.3 var(--sans); color: var(--ink);
}
.auth-label-hint { color: var(--muted); font-weight: 400; }
.auth-control { position: relative; }
.auth-control > .auth-icon { position: absolute; inset-inline-start: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #8a96a6; pointer-events: none; }
.auth-icon { fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.auth .auth-input {
  /* .auth prefix: beats the global input/select rule in main.css */
  width: 100%; height: 44px; padding-block: 0; padding-inline: 40px 12px;
  border: 1px solid var(--line); border-radius: 8px; background: #fff;
  font: 400 15px var(--sans); color: var(--ink);
  transition: border-color .15s, box-shadow .15s;
}
.auth .auth-input.no-icon { padding-inline-start: 12px; }
.auth .auth-control:has(.auth-suffix) .auth-input { padding-inline-end: 44px; }
.auth-input::placeholder { color: #9aa5b4; }
.auth .auth-input:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14, 124, 123, .15); }
.auth-input.is-invalid { border-color: var(--danger); }
.auth-input.is-valid { border-color: var(--success); }
.auth-input:disabled { background: var(--soft); color: #9aa5b4; cursor: not-allowed; }
.auth select.auth-input { appearance: none; padding-inline-end: 36px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238a96a6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 18px; }
[dir="rtl"] .auth select.auth-input { background-position: left 10px center; }
.auth-suffix { position: absolute; inset-inline-end: 6px; top: 50%; transform: translateY(-50%); display: grid; place-items: center; width: 32px; height: 32px; border: 0; border-radius: 6px; background: none; color: #8a96a6; cursor: pointer; }
.auth-suffix:hover { color: var(--navy); background: var(--soft); }
.auth-suffix svg { width: 18px; height: 18px; }
.auth-error { margin: 0; font-size: 12.5px; color: var(--danger); }
.auth-help { margin: 0; font-size: 12.5px; color: var(--muted); }

/* Misc */
.auth-inline { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; font-size: 13.5px; }
.auth-check { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: var(--ink); }
.auth-check input { width: 16px; height: 16px; accent-color: var(--navy); }
.auth-muted { color: var(--muted); }
.auth-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  height: 46px; padding: 0 20px; border: 0; border-radius: 8px;
  background: var(--navy); color: #fff; font: 600 15px var(--sans); cursor: pointer;
  transition: background-color .15s, transform .15s;
}
.auth-btn:hover:not(:disabled) { background: var(--navy-deep); }
.auth-btn:active:not(:disabled) { transform: translateY(1px); }
.auth-btn:disabled { opacity: .55; cursor: not-allowed; }
.auth-btn-ghost { background: #fff; color: var(--navy); border: 1px solid var(--line); }
.auth-btn-ghost:hover:not(:disabled) { background: var(--soft); }
.auth-spinner { width: 18px; height: 18px; border: 2px solid rgba(255, 255, 255, .35); border-top-color: #fff; border-radius: 50%; animation: auth-spin .7s linear infinite; }
.auth-alert { display: flex; gap: 10px; align-items: flex-start; padding: 11px 14px; border-radius: 8px; font-size: 14px; line-height: 1.45; }
.auth-alert svg { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; }
.auth-alert-error { background: #fdf0ee; color: #922b21; border: 1px solid #f5c6bf; }
.auth-alert-success { background: #ecf7f1; color: #1e5f3e; border: 1px solid #bfe3cf; }
.auth-switch { margin: 22px 0 0; text-align: center; font-size: 14px; color: var(--muted); }
.auth-switch a { color: var(--navy); font-weight: 600; text-decoration: none; }
.auth-switch a:hover { color: var(--teal); text-decoration: underline; }
.auth a:focus-visible, .auth button:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }

@keyframes auth-in { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
@keyframes auth-spin { to { transform: rotate(360deg); } }
@keyframes auth-drift { to { background-position: 280px 280px; } }

@media (max-width: 900px) {
  .auth { grid-template-columns: 1fr; }
  .auth-brand { display: none; }
  .auth-top { justify-content: space-between; }
  .auth-mobile-logo { display: inline-flex; }
}
@media (max-width: 520px) {
  .auth-main { padding: 16px; }
  .auth-row { grid-template-columns: 1fr; }
  .auth-title { font-size: 24px; }
}
@media (prefers-reduced-motion: reduce) {
  .auth-card, .auth-brand-pattern { animation: none; }
}
</style>
