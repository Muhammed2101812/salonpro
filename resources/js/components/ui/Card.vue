<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div v-if="$slots.header || title" class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 v-if="title" class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <slot name="header" />
    </div>

    <!-- Body -->
    <div :class="['p-6', bodyClass]">
      <slot />
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="px-6 py-4 bg-gray-50 border-t border-gray-100">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title?: string
  bodyClass?: string
}>()
</script>
