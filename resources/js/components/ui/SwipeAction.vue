<template>
  <div
    ref="itemRef"
    class="relative overflow-hidden touch-pan-y"
    @touchstart="handleTouchStart"
    @touchmove="handleTouchMove"
    @touchend="handleTouchEnd"
  >
    <!-- Actions (revealed on swipe) -->
    <div
      class="absolute inset-y-0 right-0 flex items-center"
      :style="{ width: `${actionsWidth}px` }"
    >
      <slot name="actions">
        <button
          v-if="showDelete"
          @click="$emit('delete')"
          class="h-full px-6 bg-red-500 text-white flex items-center justify-center"
        >
          <TrashIcon class="h-5 w-5" />
        </button>
        <button
          v-if="showEdit"
          @click="$emit('edit')"
          class="h-full px-6 bg-blue-500 text-white flex items-center justify-center"
        >
          <PencilIcon class="h-5 w-5" />
        </button>
      </slot>
    </div>

    <!-- Content -->
    <div
      class="relative bg-white transition-transform"
      :style="{ transform: `translateX(${translateX}px)` }"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';

interface Props {
  showDelete?: boolean;
  showEdit?: boolean;
  actionsWidth?: number;
  threshold?: number;
}

const props = withDefaults(defineProps<Props>(), {
  showDelete: true,
  showEdit: false,
  actionsWidth: 80,
  threshold: 40,
});

defineEmits<{
  delete: [];
  edit: [];
}>();

const itemRef = ref<HTMLElement | null>(null);
const translateX = ref(0);
const startX = ref(0);
const isDragging = ref(false);

const handleTouchStart = (e: TouchEvent) => {
  startX.value = e.touches[0].clientX;
  isDragging.value = true;
};

const handleTouchMove = (e: TouchEvent) => {
  if (!isDragging.value) return;
  
  const currentX = e.touches[0].clientX;
  const diff = currentX - startX.value;
  
  // Only allow left swipe (negative diff)
  if (diff < 0) {
    translateX.value = Math.max(diff, -props.actionsWidth);
  } else if (translateX.value < 0) {
    // Allow returning to original position
    translateX.value = Math.min(0, translateX.value + diff);
  }
};

const handleTouchEnd = () => {
  isDragging.value = false;
  
  // Snap to open or closed
  if (translateX.value < -props.threshold) {
    translateX.value = -props.actionsWidth;
  } else {
    translateX.value = 0;
  }
};

// Reset method
const reset = () => {
  translateX.value = 0;
};

defineExpose({ reset });
</script>

<style scoped>
.touch-pan-y {
  touch-action: pan-y;
}
</style>
