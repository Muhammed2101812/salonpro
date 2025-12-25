<template>
  <div class="animate-pulse space-y-4">
    <!-- Stats skeleton -->
    <div v-if="type === 'stats'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
          <div class="ml-4 flex-1">
            <div class="h-3 bg-gray-200 rounded w-20 mb-2"></div>
            <div class="h-6 bg-gray-200 rounded w-16"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table skeleton -->
    <div v-else-if="type === 'table'" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <div class="h-10 bg-gray-200 rounded w-64"></div>
      </div>
      <div class="divide-y divide-gray-100">
        <div v-for="i in rows" :key="i" class="p-4 flex items-center gap-4">
          <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
          </div>
          <div class="h-6 bg-gray-200 rounded w-16"></div>
        </div>
      </div>
    </div>

    <!-- Cards skeleton -->
    <div v-else-if="type === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-start gap-4 mb-4">
          <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
        <div class="space-y-2">
          <div class="h-3 bg-gray-200 rounded w-full"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        </div>
      </div>
    </div>

    <!-- Chart skeleton -->
    <div v-else-if="type === 'chart'" class="bg-white rounded-xl p-6 border border-gray-100">
      <div class="h-5 bg-gray-200 rounded w-32 mb-6"></div>
      <div class="h-64 bg-gray-100 rounded-lg flex items-end justify-around gap-2 p-4">
        <div v-for="i in 7" :key="i" class="bg-gray-200 rounded-t-lg w-full" :style="{ height: `${Math.random() * 60 + 20}%` }"></div>
      </div>
    </div>

    <!-- Default skeleton -->
    <div v-else class="bg-white rounded-xl p-6 border border-gray-100">
      <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
      <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
      <div class="h-4 bg-gray-200 rounded w-5/6"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  type?: 'stats' | 'table' | 'cards' | 'chart' | 'default';
  rows?: number;
}

withDefaults(defineProps<Props>(), {
  type: 'default',
  rows: 5
});
</script>
