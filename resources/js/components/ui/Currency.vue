<template>
  <span :class="computedClass">
    {{ formattedValue }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useCurrency } from '@/composables/useCurrency'

interface Props {
  value: number | string | null | undefined
  currency?: string
  compact?: boolean
  showSymbol?: boolean
  decimals?: number
  change?: boolean
  colored?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  compact: false,
  showSymbol: true,
  change: false,
  colored: false,
})

const { format, formatCompact, formatChange } = useCurrency()

const formattedValue = computed(() => {
  if (props.change) {
    const result = formatChange(props.value, {
      currency: props.currency,
      showSign: true,
      colored: false,
    })
    return result.text
  }

  if (props.compact) {
    return formatCompact(props.value, props.currency)
  }

  return format(props.value, {
    currency: props.currency,
    showSymbol: props.showSymbol,
    decimals: props.decimals,
  })
})

const computedClass = computed(() => {
  if (!props.colored) return ''

  const numValue = typeof props.value === 'string' ? parseFloat(props.value) : props.value

  if (numValue === null || numValue === undefined || isNaN(numValue)) {
    return 'text-gray-600'
  }

  if (numValue > 0) return 'text-green-600'
  if (numValue < 0) return 'text-red-600'
  return 'text-gray-600'
})
</script>
