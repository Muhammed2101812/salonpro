<template>
  <div v-if="loading" class="animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
  </div>
  <slot v-else></slot>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Props {
  delay?: number;
}

const props = withDefaults(defineProps<Props>(), {
  delay: 0
});

const loading = ref(true);

onMounted(() => {
  if (props.delay > 0) {
    setTimeout(() => {
      loading.value = false;
    }, props.delay);
  } else {
    loading.value = false;
  }
});
</script>
