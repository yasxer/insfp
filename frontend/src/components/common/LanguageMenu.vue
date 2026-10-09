<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import { LOCALES, applyLocale } from '@/i18n'

const { locale, t } = useI18n()
const open = ref(false)
const root = ref(null)
const current = computed(() => LOCALES.find((l) => l.code === locale.value) || LOCALES[0])

const choose = (code) => {
  applyLocale(code)
  open.value = false
}

const onOutside = (e) => {
  if (root.value && !root.value.contains(e.target)) open.value = false
}
const onKey = (e) => {
  if (e.key === 'Escape') open.value = false
}
onMounted(() => {
  document.addEventListener('click', onOutside)
  document.addEventListener('keydown', onKey)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', onOutside)
  document.removeEventListener('keydown', onKey)
})
</script>

<template>
  <div ref="root" class="lang-menu">
    <button
      type="button"
      class="lang-trigger"
      :aria-expanded="open"
      aria-haspopup="listbox"
      :aria-label="t('lang.choose')"
      @click="open = !open"
    >
      <svg class="lang-globe" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="9" />
        <path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z" />
      </svg>
      <span class="lang-current" :lang="current.code">{{ current.label }}</span>
      <span class="lang-short" :lang="current.code">{{ current.short }}</span>
      <svg class="lang-chevron" :class="{ up: open }" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
    </button>

    <Transition name="lang-pop">
      <ul v-if="open" class="lang-list" role="listbox" :aria-label="t('lang.choose')">
        <li v-for="l in LOCALES" :key="l.code">
          <button
            type="button"
            role="option"
            class="lang-option"
            :class="{ active: l.code === current.code }"
            :aria-selected="l.code === current.code"
            :lang="l.code"
            @click="choose(l.code)"
          >
            <span class="lang-code">{{ l.short }}</span>
            <span class="lang-name">{{ l.label }}</span>
            <svg v-if="l.code === current.code" class="lang-check" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 5 5 9-10" /></svg>
          </button>
        </li>
      </ul>
    </Transition>
  </div>
</template>

<style scoped>
.lang-menu { position: relative; }
.lang-trigger {
  display: inline-flex; align-items: center; gap: 8px;
  height: 40px; padding: 0 12px;
  border: 1px solid var(--line, #e3e8ef); border-radius: 999px;
  background: #fff; color: var(--navy, #0f3460);
  font: 600 13.5px/1 inherit; cursor: pointer;
  transition: border-color .2s, box-shadow .2s, background .2s;
}
.lang-trigger:hover { border-color: var(--teal, #0e7c7b); box-shadow: 0 4px 14px rgba(10, 36, 67, .08); }
.lang-trigger:focus-visible { outline: 3px solid var(--gold, #c9971c); outline-offset: 2px; }
.lang-globe, .lang-chevron, .lang-check {
  width: 17px; height: 17px; fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}
.lang-globe { color: var(--teal, #0e7c7b); }
.lang-short { display: none; }
.lang-chevron { width: 15px; height: 15px; opacity: .6; transition: transform .2s; }
.lang-chevron.up { transform: rotate(180deg); }

.lang-list {
  position: absolute; inset-inline-end: 0; top: calc(100% + 8px); z-index: 60;
  min-width: 190px; margin: 0; padding: 6px; list-style: none;
  background: #fff; border: 1px solid var(--line, #e3e8ef); border-radius: 14px;
  box-shadow: 0 18px 40px rgba(10, 36, 67, .14);
}
.lang-option {
  display: flex; align-items: center; gap: 10px; width: 100%;
  padding: 9px 10px; border: 0; border-radius: 10px; background: none;
  color: #263445; font: 500 14px/1.2 inherit; text-align: start; cursor: pointer;
}
.lang-option:hover { background: #f2f6fa; }
.lang-option.active { background: #e6f4f3; color: var(--teal, #0e7c7b); font-weight: 600; }
.lang-code {
  display: inline-grid; place-items: center; width: 30px; height: 24px;
  border-radius: 6px; background: #eef2f7; color: var(--navy, #0f3460);
  font-size: 11.5px; font-weight: 700;
}
.lang-option.active .lang-code { background: var(--teal, #0e7c7b); color: #fff; }
.lang-name { flex: 1; }
.lang-check { color: var(--teal, #0e7c7b); }

.lang-pop-enter-active, .lang-pop-leave-active { transition: opacity .16s ease, transform .16s ease; }
.lang-pop-enter-from, .lang-pop-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
