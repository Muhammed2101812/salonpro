<template>
  <div
    ref="containerRef"
    class="overflow-y-auto"
    @touchstart="handleTouchStart"
    @touchmove="handleTouchMove"
    @touchend="handleTouchEnd"
  >
    <!-- Pull indicator -->
    <div
      v-if="isPulling"
      class="flex items-center justify-center py-4 transition-all"
      :style="{ height: `${pullDistance}px` }"
    >
      <div class="flex items-center gap-2 text-gray-500">
        <ArrowPathIcon
          class="h-5 w-5 transition-transform"
          :class="{ 'animate-spin': isRefreshing, 'rotate-180': pullDistance > threshold }"
        />
        <span class="text-sm">
          {{ isRefreshing ? 'Yenileniyor...' : pullDistance > threshold ? 'Bırakın' : 'Yenilemek için çekin' }}
        </span>
      </div>
    </div>

    <!-- Content -->
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ArrowPathIcon } from '@heroicons/vue/24/outline';

interface Props {
  threshold?: number;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  threshold: 60,
  disabled: false,
});

const emit = defineEmits<{
  refresh: [];
}>();

const containerRef = ref<HTMLElement | null>(null);
const isPulling = ref(false);
const isRefreshing = ref(false);
const pullDistance = ref(0);
const startY = ref(0);

const handleTouchStart = (e: TouchEvent) => {
  if (props.disabled || isRefreshing.value) return;
  
  const container = containerRef.value;
  if (container && container.scrollTop === 0) {
    startY.value = e.touches[0].clientY;
    isPulling.value = true;
  }
};

const handleTouchMove = (e: TouchEvent) => {
  if (!isPulling.value || props.disabled) return;
  
  const currentY = e.touches[0].clientY;
  const diff = currentY - startY.value;
  
  if (diff > 0) {
    // Resistance effect
    pullDistance.value = Math.min(diff * 0.5, 120);
    e.preventDefault();
  }
};

const handleTouchEnd = async () => {
  if (!isPulling.value || props.disabled) return;
  
  if (pullDistance.value > props.threshold) {
    isRefreshing.value = true;
    emit('refresh');
    
    // Wait for refresh to complete (parent should call done)
    await new Promise(resolve => setTimeout(resolve, 1000));
    isRefreshing.value = false;
  }
  
  isPulling.value = false;
  pullDistance.value = 0;
  startY.value = 0;
};

// Expose method to stop refreshing
defineExpose({
  stopRefresh: () => {
    isRefreshing.value = false;
  }
});
</script>
