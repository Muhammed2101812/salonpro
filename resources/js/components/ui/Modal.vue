<template>
  <TransitionRoot :show="modelValue" as="template">
    <Dialog class="relative z-50" @close="close">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel :class="panelClasses">
              <!-- Header -->
              <div v-if="$slots.header || title" class="flex items-center justify-between mb-4">
                <DialogTitle v-if="title" class="text-lg font-semibold text-gray-900">
                  {{ title }}
                </DialogTitle>
                <slot name="header" />
                <button
                  v-if="closable"
                  type="button"
                  class="text-gray-400 hover:text-gray-500 transition-colors"
                  @click="close"
                >
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>

              <!-- Content -->
              <div>
                <slot />
              </div>

              <!-- Footer -->
              <div v-if="$slots.footer" class="mt-6 flex justify-end gap-3">
                <slot name="footer" />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionRoot,
  TransitionChild,
} from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

interface Props {
  modelValue: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg' | 'xl' | 'full'
  closable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  closable: true,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  close: []
}>()

const close = () => {
  emit('update:modelValue', false)
  emit('close')
}

const panelClasses = computed(() => {
  const base = 'w-full bg-white rounded-xl shadow-xl p-6'

  const sizes = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    full: 'max-w-full',
  }

  return [base, sizes[props.size]]
})
</script>
