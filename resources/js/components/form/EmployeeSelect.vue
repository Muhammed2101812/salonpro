<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Çalışan'"
    :options="employeeOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="employeeOptions.loading.value"
    :error="employeeOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="employeeOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useEmployeeOptions, useActiveEmployeeOptions } from '@/composables/useRelationships'

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

const employeeOptions = props.activeOnly ? useActiveEmployeeOptions() : useEmployeeOptions()

onMounted(() => {
  if (props.autoLoad && employeeOptions.options.value.length === 0) {
    employeeOptions.fetch()
  }
})
</script>
