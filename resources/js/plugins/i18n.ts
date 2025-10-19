import { createI18n } from 'vue-i18n'
import tr from '@/locales/tr.json'
import en from '@/locales/en.json'

export type MessageSchema = typeof tr

const i18n = createI18n<[MessageSchema], 'tr' | 'en'>({
  legacy: false,
  locale: 'tr', // Default locale
  fallbackLocale: 'en',
  messages: {
    tr,
    en,
  },
  datetimeFormats: {
    tr: {
      short: {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long',
      },
      time: {
        hour: '2-digit',
        minute: '2-digit',
      },
      datetime: {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      },
    },
    en: {
      short: {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long',
      },
      time: {
        hour: '2-digit',
        minute: '2-digit',
      },
      datetime: {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      },
    },
  },
  numberFormats: {
    tr: {
      currency: {
        style: 'currency',
        currency: 'TRY',
        notation: 'standard',
      },
      decimal: {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      },
      percent: {
        style: 'percent',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
      },
    },
    en: {
      currency: {
        style: 'currency',
        currency: 'USD',
        notation: 'standard',
      },
      decimal: {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      },
      percent: {
        style: 'percent',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
      },
    },
  },
})

export default i18n
