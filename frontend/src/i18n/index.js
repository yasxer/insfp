import { createI18n } from 'vue-i18n'
import fr from './locales/fr'
import ar from './locales/ar'
import en from './locales/en'
// Page texts (admin / teacher / student spaces), generated from the templates
import frPages from './locales/pages/fr.json'
import arPages from './locales/pages/ar.json'
import enPages from './locales/pages/en.json'

export const LOCALES = [
  { code: 'fr', label: 'Français', short: 'FR', dir: 'ltr' },
  { code: 'ar', label: 'العربية', short: 'ع', dir: 'rtl' },
  { code: 'en', label: 'English', short: 'EN', dir: 'ltr' },
]

const STORAGE_KEY = 'locale'

const readLocale = () => {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (LOCALES.some((l) => l.code === saved)) return saved
  } catch {
    // storage unavailable: fall back to French
  }
  return 'fr'
}

export const i18n = createI18n({
  legacy: false,
  locale: readLocale(),
  fallbackLocale: 'fr',
  messages: {
    fr: { ...fr, ...frPages },
    ar: { ...ar, ...arPages },
    en: { ...en, ...enPages },
  },
})

// <html lang/dir> drive the Arabic font and the right-to-left layout
export const applyLocale = (code) => {
  const locale = LOCALES.find((l) => l.code === code) || LOCALES[0]
  i18n.global.locale.value = locale.code
  document.documentElement.lang = locale.code
  document.documentElement.dir = locale.dir
  try {
    localStorage.setItem(STORAGE_KEY, locale.code)
  } catch {
    // keep the choice for this visit only
  }
}

applyLocale(i18n.global.locale.value)

// BCP 47 tag for toLocaleDateString(); ar-DZ keeps Latin digits as used in Algeria
const DATE_LOCALES = { fr: 'fr-FR', ar: 'ar-DZ', en: 'en-GB' }
export const dateLocale = () => DATE_LOCALES[i18n.global.locale.value] || 'fr-FR'
