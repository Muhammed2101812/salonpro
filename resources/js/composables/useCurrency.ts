import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

/**
 * Currency configuration
 */
export interface CurrencyConfig {
  code: string
  symbol: string
  name: string
  decimals: number
  thousandsSeparator: string
  decimalSeparator: string
  symbolPosition: 'before' | 'after'
}

/**
 * Supported currencies
 */
export const CURRENCIES: Record<string, CurrencyConfig> = {
  TRY: {
    code: 'TRY',
    symbol: '₺',
    name: 'Türk Lirası',
    decimals: 2,
    thousandsSeparator: '.',
    decimalSeparator: ',',
    symbolPosition: 'after',
  },
  USD: {
    code: 'USD',
    symbol: '$',
    name: 'US Dollar',
    decimals: 2,
    thousandsSeparator: ',',
    decimalSeparator: '.',
    symbolPosition: 'before',
  },
  EUR: {
    code: 'EUR',
    symbol: '€',
    name: 'Euro',
    decimals: 2,
    thousandsSeparator: '.',
    decimalSeparator: ',',
    symbolPosition: 'after',
  },
}

/**
 * Composable for currency formatting
 */
export function useCurrency() {
  const { locale } = useI18n()

  // Default currency based on locale
  const defaultCurrency = computed(() => {
    return locale.value === 'tr' ? 'TRY' : 'USD'
  })

  // Current currency (can be changed by user)
  const currentCurrency = ref<string>(
    localStorage.getItem('currency') || defaultCurrency.value
  )

  /**
   * Get currency configuration
   */
  const getCurrencyConfig = (currencyCode?: string): CurrencyConfig => {
    const code = currencyCode || currentCurrency.value
    return CURRENCIES[code] || CURRENCIES.TRY
  }

  /**
   * Format a number as currency
   */
  const format = (
    value: number | string | null | undefined,
    options?: {
      currency?: string
      showSymbol?: boolean
      decimals?: number
    }
  ): string => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (numValue === null || numValue === undefined || isNaN(numValue)) {
      return '-'
    }

    const currency = getCurrencyConfig(options?.currency)
    const showSymbol = options?.showSymbol !== false
    const decimals = options?.decimals ?? currency.decimals

    // Format the number
    const formattedNumber = formatNumber(
      numValue,
      decimals,
      currency.thousandsSeparator,
      currency.decimalSeparator
    )

    if (!showSymbol) {
      return formattedNumber
    }

    // Add currency symbol
    return currency.symbolPosition === 'before'
      ? `${currency.symbol}${formattedNumber}`
      : `${formattedNumber} ${currency.symbol}`
  }

  /**
   * Format using native Intl.NumberFormat
   */
  const formatIntl = (
    value: number | string | null | undefined,
    currencyCode?: string
  ): string => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (numValue === null || numValue === undefined || isNaN(numValue)) {
      return '-'
    }

    const code = currencyCode || currentCurrency.value

    return new Intl.NumberFormat(locale.value, {
      style: 'currency',
      currency: code,
    }).format(numValue)
  }

  /**
   * Parse currency string to number
   */
  const parse = (value: string, currencyCode?: string): number => {
    const currency = getCurrencyConfig(currencyCode)

    // Remove currency symbol
    let cleanValue = value.replace(currency.symbol, '').trim()

    // Remove thousands separators
    cleanValue = cleanValue.replace(new RegExp(`\\${currency.thousandsSeparator}`, 'g'), '')

    // Replace decimal separator with dot
    cleanValue = cleanValue.replace(currency.decimalSeparator, '.')

    return parseFloat(cleanValue) || 0
  }

  /**
   * Set current currency
   */
  const setCurrency = (currencyCode: string) => {
    if (CURRENCIES[currencyCode]) {
      currentCurrency.value = currencyCode
      localStorage.setItem('currency', currencyCode)
    }
  }

  /**
   * Get available currencies
   */
  const availableCurrencies = computed(() => {
    return Object.values(CURRENCIES)
  })

  /**
   * Format as compact currency (K, M, B)
   */
  const formatCompact = (
    value: number | string | null | undefined,
    currencyCode?: string
  ): string => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (numValue === null || numValue === undefined || isNaN(numValue)) {
      return '-'
    }

    const currency = getCurrencyConfig(currencyCode)
    let compactValue: number
    let suffix: string

    if (Math.abs(numValue) >= 1e9) {
      compactValue = numValue / 1e9
      suffix = 'B'
    } else if (Math.abs(numValue) >= 1e6) {
      compactValue = numValue / 1e6
      suffix = 'M'
    } else if (Math.abs(numValue) >= 1e3) {
      compactValue = numValue / 1e3
      suffix = 'K'
    } else {
      return format(numValue, { currency: currencyCode })
    }

    const formattedNumber = formatNumber(
      compactValue,
      1,
      currency.thousandsSeparator,
      currency.decimalSeparator
    )

    const result = `${formattedNumber}${suffix}`

    return currency.symbolPosition === 'before'
      ? `${currency.symbol}${result}`
      : `${result} ${currency.symbol}`
  }

  /**
   * Calculate percentage
   */
  const formatPercentage = (
    value: number | string | null | undefined,
    decimals = 0
  ): string => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (numValue === null || numValue === undefined || isNaN(numValue)) {
      return '-'
    }

    return `${numValue.toFixed(decimals)}%`
  }

  /**
   * Calculate change percentage
   */
  const calculateChangePercentage = (oldValue: number, newValue: number): number => {
    if (oldValue === 0) return 0
    return ((newValue - oldValue) / oldValue) * 100
  }

  /**
   * Format change with +/- sign
   */
  const formatChange = (
    value: number | string | null | undefined,
    options?: {
      currency?: string
      showSign?: boolean
      colored?: boolean
    }
  ): { text: string; color?: string } => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value

    if (numValue === null || numValue === undefined || isNaN(numValue)) {
      return { text: '-' }
    }

    const formatted = format(Math.abs(numValue), options)
    const showSign = options?.showSign !== false

    let text = formatted
    let color: string | undefined

    if (showSign && numValue !== 0) {
      text = numValue > 0 ? `+${formatted}` : `-${formatted}`
    }

    if (options?.colored) {
      color = numValue > 0 ? 'text-green-600' : numValue < 0 ? 'text-red-600' : 'text-gray-600'
    }

    return { text, color }
  }

  return {
    format,
    formatIntl,
    formatCompact,
    formatPercentage,
    formatChange,
    parse,
    setCurrency,
    calculateChangePercentage,
    currentCurrency: computed(() => currentCurrency.value),
    availableCurrencies,
    getCurrencyConfig,
  }
}

/**
 * Helper function to format a number with separators
 */
function formatNumber(
  value: number,
  decimals: number,
  thousandsSeparator: string,
  decimalSeparator: string
): string {
  const fixed = value.toFixed(decimals)
  const [integer, decimal] = fixed.split('.')

  // Add thousands separator
  const formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator)

  // Combine integer and decimal parts
  return decimal ? `${formattedInteger}${decimalSeparator}${decimal}` : formattedInteger
}

/**
 * Currency display component props helper
 */
export interface CurrencyDisplayProps {
  value: number | string | null | undefined
  currency?: string
  compact?: boolean
  showSymbol?: boolean
  decimals?: number
}
