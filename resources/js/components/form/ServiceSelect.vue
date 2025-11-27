<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Hizmet'"
    :options="serviceOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="serviceOptions.loading.value"
    :error="serviceOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="serviceOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useServiceOptions, useActiveServiceOptions } from '@/composables/useRelationships'

interface Props {
  name: string
  label?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  hint?: string
  showRefresh?: boolean
  autoLoad?: boolean
  activeOnly?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  showRefresh: true,
  autoLoad: true,
  activeOnly: false,
})

const serviceOptions = props.activeOnly ? useActiveServiceOptions() : useServiceOptions()

onMounted(() => {
  if (props.autoLoad && serviceOptions.options.value.length === 0) {
    serviceOptions.fetch()
  }
})
</script>
