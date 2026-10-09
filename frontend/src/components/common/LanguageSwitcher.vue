<script setup>
import { useI18n } from 'vue-i18n'
import { LOCALES, applyLocale } from '@/i18n'

const { locale, t } = useI18n()
</script>

<template>
  <div
    class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-0.5 dark:border-gray-700 dark:bg-gray-900"
    role="group"
    :aria-label="t('lang.choose')"
  >
    <button
      v-for="l in LOCALES"
      :key="l.code"
      type="button"
      class="min-w-[2.25rem] rounded-md px-2 py-1 text-xs font-semibold transition-colors"
      :class="locale === l.code
        ? 'bg-navy-600 text-white shadow-sm'
        : 'text-gray-500 hover:bg-gray-100 hover:text-navy-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white'"
      :lang="l.code"
      :title="l.label"
      :aria-pressed="locale === l.code"
      @click="applyLocale(l.code)"
    >
      {{ l.short }}
    </button>
  </div>
</template>
