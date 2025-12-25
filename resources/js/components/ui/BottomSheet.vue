<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-50" @close="handleClose">
      <!-- Backdrop -->
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50" />
      </TransitionChild>

      <!-- Sheet -->
      <div class="fixed inset-x-0 bottom-0">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="translate-y-full"
          enter-to="translate-y-0"
          leave="duration-200 ease-in"
          leave-from="translate-y-0"
          leave-to="translate-y-full"
        >
          <DialogPanel
            class="w-full bg-white rounded-t-2xl shadow-xl max-h-[90vh] overflow-hidden flex flex-col"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
          >
            <!-- Handle bar -->
            <div class="flex justify-center py-3">
              <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Header -->
            <div v-if="title" class="px-4 pb-3 border-b border-gray-100">
              <DialogTitle class="text-lg font-semibold text-gray-900">
                {{ title }}
              </DialogTitle>
              <p v-if="description" class="text-sm text-gray-500 mt-1">
                {{ description }}
              </p>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-4">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="p-4 border-t border-gray-100 bg-gray-50">
              <slot name="footer"></slot>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionRoot,
  TransitionChild,
} from '@headlessui/vue';

interface Props {
  isOpen: boolean;
  title?: string;
  description?: string;
  closeOnSwipe?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  closeOnSwipe: true,
});

const emit = defineEmits<{
  close: [];
}>();

// Swipe to close
const touchStartY = ref(0);
const touchCurrentY = ref(0);

const handleTouchStart = (e: TouchEvent) => {
  touchStartY.value = e.touches[0].clientY;
};

const handleTouchMove = (e: TouchEvent) => {
  touchCurrentY.value = e.touches[0].clientY;
};

const handleTouchEnd = () => {
  if (props.closeOnSwipe) {
    const diff = touchCurrentY.value - touchStartY.value;
    if (diff > 100) {
      emit('close');
    }
  }
  touchStartY.value = 0;
  touchCurrentY.value = 0;
};

const handleClose = () => {
  emit('close');
};
</script>
