<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Müşteri'"
    :options="customerOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="customerOptions.loading.value"
    :error="customerOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="customerOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useCustomerOptions } from '@/composables/useRelationships'

interface Props {
  name: string
  label?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  hint?: string
  showRefresh?: boolean
  autoLoad?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  showRefresh: true,
  autoLoad: true,
})

const customerOptions = useCustomerOptions()

onMounted(() => {
  if (props.autoLoad && customerOptions.options.value.length === 0) {
    customerOptions.fetch()
  }
})
</script>
