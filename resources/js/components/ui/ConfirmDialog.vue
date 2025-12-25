<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-50" @close="handleClose">
      <TransitionChild
        as="template"
        enter="duration-200 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-150 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="duration-200 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-150 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all">
              <!-- Icon -->
              <div class="flex items-center justify-center mb-4">
                <div :class="iconContainerClass">
                  <ExclamationTriangleIcon v-if="type === 'danger'" class="h-6 w-6 text-red-600" />
                  <ExclamationCircleIcon v-else-if="type === 'warning'" class="h-6 w-6 text-yellow-600" />
                  <InformationCircleIcon v-else-if="type === 'info'" class="h-6 w-6 text-blue-600" />
                  <CheckCircleIcon v-else class="h-6 w-6 text-green-600" />
                </div>
              </div>

              <!-- Title -->
              <DialogTitle as="h3" class="text-lg font-semibold text-gray-900 text-center mb-2">
                {{ title }}
              </DialogTitle>

              <!-- Message -->
              <p class="text-sm text-gray-500 text-center mb-6">
                {{ message }}
              </p>

              <!-- Actions -->
              <div class="flex gap-3">
                <button
                  type="button"
                  class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                  @click="handleCancel"
                >
                  {{ cancelText }}
                </button>
                <button
                  type="button"
                  :class="confirmButtonClass"
                  @click="handleConfirm"
                >
                  {{ confirmText }}
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionRoot,
  TransitionChild,
} from '@headlessui/vue';
import {
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline';

interface Props {
  isOpen: boolean;
  title: string;
  message: string;
  type?: 'danger' | 'warning' | 'info' | 'success';
  confirmText?: string;
  cancelText?: string;
  closeOnOverlay?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'danger',
  confirmText: 'Onayla',
  cancelText: 'İptal',
  closeOnOverlay: true,
});

const emit = defineEmits<{
  confirm: [];
  cancel: [];
  close: [];
}>();

const iconContainerClass = computed(() => {
  const base = 'w-12 h-12 rounded-full flex items-center justify-center';
  switch (props.type) {
    case 'danger': return `${base} bg-red-100`;
    case 'warning': return `${base} bg-yellow-100`;
    case 'info': return `${base} bg-blue-100`;
    default: return `${base} bg-green-100`;
  }
});

const confirmButtonClass = computed(() => {
  const base = 'flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-lg transition-colors';
  switch (props.type) {
    case 'danger': return `${base} bg-red-600 hover:bg-red-700`;
    case 'warning': return `${base} bg-yellow-600 hover:bg-yellow-700`;
    case 'info': return `${base} bg-blue-600 hover:bg-blue-700`;
    default: return `${base} bg-green-600 hover:bg-green-700`;
  }
});

const handleConfirm = () => {
  emit('confirm');
  emit('close');
};

const handleCancel = () => {
  emit('cancel');
  emit('close');
};

const handleClose = () => {
  if (props.closeOnOverlay) {
    emit('cancel');
    emit('close');
  }
};
</script>
