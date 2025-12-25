<template>
  <nav class="p-4 space-y-1">
    <template v-for="item in menuConfig" :key="item.path || item.label">
      <!-- Alt menüsü olmayan öğeler -->
      <router-link
        v-if="!item.children"
        :to="item.path!"
        @click="$emit('itemClick')"
        class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition group"
        active-class="bg-blue-500 text-white hover:bg-blue-600 hover:text-white"
      >
        <component :is="getIcon(item.icon)" class="w-5 h-5 flex-shrink-0" />
        <span class="font-medium">{{ item.label }}</span>
      </router-link>

      <!-- Alt menülü öğeler (Collapsible) -->
      <div v-else class="space-y-1">
        <button
          @click="toggleCategory(item.label)"
          :class="[
            'w-full flex items-center justify-between px-4 py-3 rounded-lg transition group',
            isActiveCategory(item) 
              ? 'bg-blue-50 text-blue-600' 
              : 'text-gray-700 hover:bg-gray-100'
          ]"
        >
          <div class="flex items-center gap-3">
            <component :is="getIcon(item.icon)" class="w-5 h-5 flex-shrink-0" />
            <span class="font-medium">{{ item.label }}</span>
          </div>
          <svg
            :class="[
              'w-4 h-4 transition-transform duration-200',
              isOpen(item.label) ? 'rotate-180' : ''
            ]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Alt menü öğeleri -->
        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-[1000px]"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 max-h-[1000px]"
          leave-to-class="opacity-0 max-h-0"
        >
          <div v-show="isOpen(item.label)" class="overflow-hidden">
            <div class="ml-4 pl-4 border-l-2 border-gray-200 space-y-1 py-1">
              <router-link
                v-for="child in item.children"
                :key="child.path"
                :to="child.path!"
                @click="$emit('itemClick')"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition"
                active-class="bg-blue-500 text-white hover:bg-blue-600 hover:text-white"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                <span>{{ child.label }}</span>
              </router-link>
            </div>
          </div>
        </Transition>
      </div>
    </template>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, h } from 'vue';
import { useRoute } from 'vue-router';
import { menuConfig, type MenuItem } from '@/config/menuConfig';

defineEmits<{
  (e: 'itemClick'): void;
}>();

const route = useRoute();

// Açık kategorileri takip et
const openCategories = ref<Set<string>>(new Set());

// LocalStorage'dan açık kategorileri yükle
onMounted(() => {
  const saved = localStorage.getItem('salonpro_open_menus');
  if (saved) {
    try {
      const parsed = JSON.parse(saved);
      openCategories.value = new Set(parsed);
    } catch (e) {
      console.error('Menü durumu yüklenemedi:', e);
    }
  }
  
  // Aktif sayfanın kategorisini aç
  const activeCategory = findCategoryForPath(route.path);
  if (activeCategory) {
    openCategories.value.add(activeCategory);
    saveOpenCategories();
  }
});

// Kategoriyi aç/kapat
const toggleCategory = (label: string) => {
  if (openCategories.value.has(label)) {
    openCategories.value.delete(label);
  } else {
    openCategories.value.add(label);
  }
  saveOpenCategories();
};

// Kategori açık mı kontrol et
const isOpen = (label: string) => openCategories.value.has(label);

// Açık kategorileri LocalStorage'a kaydet
const saveOpenCategories = () => {
  localStorage.setItem('salonpro_open_menus', JSON.stringify([...openCategories.value]));
};

// Aktif path'e göre kategoriyi bul
const findCategoryForPath = (path: string): string | null => {
  for (const item of menuConfig) {
    if (item.children) {
      for (const child of item.children) {
        if (child.path === path) {
          return item.label;
        }
      }
    }
  }
  return null;
};

// Kategoride aktif sayfa var mı kontrol et
const isActiveCategory = (item: MenuItem): boolean => {
  if (!item.children) return false;
  return item.children.some(child => route.path === child.path);
};

// ─────────────────────────────────────────────────────────────
// İkon Component'leri (Heroicons)
// ─────────────────────────────────────────────────────────────

const HomeIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
]);

const BuildingOfficeIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
]);

const UsersIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
]);

const BriefcaseIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
]);

const SparklesIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' })
]);

const CalendarDaysIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
]);

const ShoppingCartIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' })
]);

const CubeIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })
]);

const TruckIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h1m4-4h3M8 11l4-7 4 7m-4-7v18' })
]);

const BanknotesIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' })
]);

const MegaphoneIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z' })
]);

const ChartBarIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })
]);

const BellIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' })
]);

const ClipboardDocumentListIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' })
]);

const Cog6ToothIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
]);

// İkon eşleştirme - Fonksiyon olarak tip tanımla
const iconComponents: Record<string, () => ReturnType<typeof h>> = {
  HomeIcon,
  BuildingOfficeIcon,
  UsersIcon,
  BriefcaseIcon,
  SparklesIcon,
  CalendarDaysIcon,
  ShoppingCartIcon,
  CubeIcon,
  TruckIcon,
  BanknotesIcon,
  MegaphoneIcon,
  ChartBarIcon,
  BellIcon,
  ClipboardDocumentListIcon,
  Cog6ToothIcon
};

const getIcon = (iconName?: string) => {
  if (!iconName || !iconComponents[iconName]) {
    return HomeIcon;
  }
  return iconComponents[iconName];
};
</script>
