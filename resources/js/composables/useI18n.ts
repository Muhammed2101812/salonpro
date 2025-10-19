import { useI18n as useVueI18n } from 'vue-i18n'
import type { Composer } from 'vue-i18n'

/**
 * Composable for using i18n with TypeScript support
 */
export function useI18n() {
  const i18n = useVueI18n()

  /**
   * Translate a key with optional interpolation
   */
  const t = (key: string, values?: Record<string, any>) => {
    return i18n.t(key, values)
  }

  /**
   * Translate with pluralization
   */
  const tc = (key: string, choice: number, values?: Record<string, any>) => {
    return i18n.t(key, { count: choice, ...values })
  }

  /**
   * Check if a translation key exists
   */
  const te = (key: string) => {
    return i18n.te(key)
  }

  /**
   * Format date according to current locale
   */
  const d = (value: Date | number | string, format?: string) => {
    return i18n.d(value, format)
  }

  /**
   * Format number according to current locale
   */
  const n = (value: number, format?: string) => {
    return i18n.n(value, format)
  }

  /**
   * Get current locale
   */
  const locale = i18n.locale

  /**
   * Set locale
   */
  const setLocale = (newLocale: string) => {
    i18n.locale.value = newLocale
    localStorage.setItem('locale', newLocale)
  }

  /**
   * Get available locales
   */
  const availableLocales = i18n.availableLocales

  return {
    t,
    tc,
    te,
    d,
    n,
    locale,
    setLocale,
    availableLocales,
  }
}

/**
 * Helper function to format currency
 */
export function useCurrency() {
  const { n, locale } = useI18n()

  const formatCurrency = (value: number, currency?: string) => {
    const currencyCode = currency || (locale.value === 'tr' ? 'TRY' : 'USD')

    return new Intl.NumberFormat(locale.value, {
      style: 'currency',
      currency: currencyCode,
    }).format(value)
  }

  return {
    formatCurrency,
  }
}

/**
 * Helper function to format dates
 */
export function useDateFormat() {
  const { d, locale } = useI18n()

  const formatDate = (date: Date | string | number, format: 'short' | 'long' = 'short') => {
    return d(date, format)
  }

  const formatTime = (date: Date | string | number) => {
    return d(date, 'time')
  }

  const formatDateTime = (date: Date | string | number) => {
    return d(date, 'datetime')
  }

  const relativeTime = (date: Date | string) => {
    const now = new Date()
    const target = new Date(date)
    const diff = now.getTime() - target.getTime()

    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)
    const weeks = Math.floor(days / 7)
    const months = Math.floor(days / 30)
    const years = Math.floor(days / 365)

    const rtf = new Intl.RelativeTimeFormat(locale.value, { numeric: 'auto' })

    if (years > 0) return rtf.format(-years, 'year')
    if (months > 0) return rtf.format(-months, 'month')
    if (weeks > 0) return rtf.format(-weeks, 'week')
    if (days > 0) return rtf.format(-days, 'day')
    if (hours > 0) return rtf.format(-hours, 'hour')
    if (minutes > 0) return rtf.format(-minutes, 'minute')
    return rtf.format(-seconds, 'second')
  }

  return {
    formatDate,
    formatTime,
    formatDateTime,
    relativeTime,
  }
}
