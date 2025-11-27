<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Şube'"
    :options="branchOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="branchOptions.loading.value"
    :error="branchOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="branchOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useBranchOptions } from '@/composables/useRelationships'

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

const branchOptions = useBranchOptions()

onMounted(() => {
  if (props.autoLoad && branchOptions.options.value.length === 0) {
    branchOptions.fetch()
  }
})
</script>
