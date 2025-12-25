<template>
  <nav class="flex items-center text-sm" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
      <!-- Home -->
      <li>
        <router-link
          to="/"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <HomeIcon class="h-4 w-4" />
        </router-link>
      </li>

      <!-- Separator and Items -->
      <template v-for="(item, index) in items" :key="index">
        <li class="flex items-center">
          <ChevronRightIcon class="h-4 w-4 text-gray-300 mx-1" />
          <router-link
            v-if="item.to && index < items.length - 1"
            :to="item.to"
            class="text-gray-500 hover:text-gray-700 transition-colors"
          >
            {{ item.label }}
          </router-link>
          <span
            v-else
            class="text-gray-900 font-medium"
          >
            {{ item.label }}
          </span>
        </li>
      </template>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { HomeIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

interface BreadcrumbItem {
  label: string;
  to?: string;
}

interface Props {
  items: BreadcrumbItem[];
}

defineProps<Props>();
</script>
