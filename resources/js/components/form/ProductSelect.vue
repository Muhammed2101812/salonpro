<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Ürün'"
    :options="productOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="productOptions.loading.value"
    :error="productOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="productOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useProductOptions, useInStockProductOptions } from '@/composables/useRelationships'

interface Props {
  name: string
  label?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  hint?: string
  showRefresh?: boolean
  autoLoad?: boolean
  inStockOnly?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  showRefresh: true,
  autoLoad: true,
  inStockOnly: false,
})

const productOptions = props.inStockOnly ? useInStockProductOptions() : useProductOptions()

onMounted(() => {
  if (props.autoLoad && productOptions.options.value.length === 0) {
    productOptions.fetch()
  }
})
</script>
