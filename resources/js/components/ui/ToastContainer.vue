<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] space-y-3 max-w-sm w-full pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'pointer-events-auto rounded-lg shadow-lg p-4 flex items-start gap-3 border',
            getToastClasses(toast.type)
          ]"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <CheckCircleIcon v-if="toast.type === 'success'" class="h-5 w-5 text-green-500" />
            <ExclamationCircleIcon v-else-if="toast.type === 'error'" class="h-5 w-5 text-red-500" />
            <ExclamationTriangleIcon v-else-if="toast.type === 'warning'" class="h-5 w-5 text-yellow-500" />
            <InformationCircleIcon v-else class="h-5 w-5 text-blue-500" />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p v-if="toast.title" class="text-sm font-medium text-gray-900">{{ toast.title }}</p>
            <p :class="['text-sm', toast.title ? 'text-gray-600' : 'text-gray-900']">{{ toast.message }}</p>
          </div>

          <!-- Dismiss button -->
          <button
            v-if="toast.dismissible"
            @click="dismiss(toast.id)"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';
import { useToast } from '@/composables/useToast';

const { toasts, dismiss } = useToast();

const getToastClasses = (type?: string) => {
  switch (type) {
    case 'success':
      return 'bg-green-50 border-green-200';
    case 'error':
      return 'bg-red-50 border-red-200';
    case 'warning':
      return 'bg-yellow-50 border-yellow-200';
    default:
      return 'bg-blue-50 border-blue-200';
  }
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
