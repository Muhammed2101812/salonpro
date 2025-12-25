<template>
  <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-40 md:hidden safe-area-bottom">
    <div class="flex items-center justify-around h-16">
      <router-link
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="flex flex-col items-center justify-center flex-1 h-full transition-colors"
        :class="[
          isActive(item.to)
            ? 'text-blue-600'
            : 'text-gray-500 hover:text-gray-700'
        ]"
      >
        <component :is="item.icon" class="h-6 w-6" />
        <span class="text-xs mt-1">{{ item.label }}</span>
      </router-link>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import {
  HomeIcon,
  UserGroupIcon,
  CalendarDaysIcon,
  ShoppingBagIcon,
  Cog6ToothIcon,
} from '@heroicons/vue/24/outline';

const route = useRoute();

const navItems = [
  { to: '/', icon: HomeIcon, label: 'Ana Sayfa' },
  { to: '/customers', icon: UserGroupIcon, label: 'Müşteriler' },
  { to: '/appointments', icon: CalendarDaysIcon, label: 'Randevular' },
  { to: '/sales', icon: ShoppingBagIcon, label: 'Satışlar' },
  { to: '/settings', icon: Cog6ToothIcon, label: 'Ayarlar' },
];

const isActive = (path: string) => {
  if (path === '/') {
    return route.path === '/';
  }
  return route.path.startsWith(path);
};
</script>

<style scoped>
.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
