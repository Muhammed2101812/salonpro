<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <template v-if="isAuthenticated && $route.meta.requiresAuth">
      <!-- Top Bar -->
      <div class="fixed top-0 left-0 right-0 bg-white shadow-md z-30 h-16">
        <div class="h-full px-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 transition">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h1 class="text-xl font-bold text-blue-600">SalonPro</h1>
          </div>
          <div class="flex items-center gap-4">
            <BranchSwitcher />
            <span v-if="user" class="text-sm text-gray-700 font-medium">{{ user.name }}</span>
            <button @click="handleLogout" class="flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Çıkış
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <aside :class="[
        'fixed top-16 left-0 bottom-0 bg-white shadow-lg z-20 transition-transform duration-300 ease-in-out overflow-y-auto',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]" class="w-72">
        <SidebarMenu @itemClick="closeSidebarOnMobile" />
      </aside>

      <!-- Overlay for mobile -->
      <div
        v-if="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden"
      ></div>

      <!-- Main Content -->
      <main :class="[
        'pt-16 transition-all duration-300 min-h-screen',
        sidebarOpen ? 'md:ml-72' : 'md:ml-0'
      ]">
        <!-- Breadcrumb -->
        <div v-if="breadcrumbItems.length > 0" class="px-6 py-4 bg-white border-b border-gray-100">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
        <div class="p-6">
          <router-view />
        </div>
      </main>
    </template>
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useBranchStore } from '@/stores/branch';
import BranchSwitcher from '@/components/ui/BranchSwitcher.vue';
import SidebarMenu from '@/components/layout/SidebarMenu.vue';
import Breadcrumb from '@/components/ui/Breadcrumb.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const branchStore = useBranchStore();
const sidebarOpen = ref(true);

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

// Breadcrumb items from route meta
const breadcrumbItems = computed(() => {
  const meta = route.meta;
  if (meta.breadcrumb && Array.isArray(meta.breadcrumb)) {
    return (meta.breadcrumb as string[]).map((label, index, arr) => ({
      label,
      to: index < arr.length - 1 ? undefined : undefined // Son öğe için link yok
    }));
  }
  return [];
});

const closeSidebarOnMobile = () => {
  if (window.innerWidth < 768) {
    sidebarOpen.value = false;
  }
};

onMounted(async () => {
  if (isAuthenticated.value && !user.value) {
    authStore.fetchProfile();
  }

  // Load current branch
  if (isAuthenticated.value) {
    await branchStore.loadCurrentBranch();
  }

  // Desktop'ta sidebar açık başlasın
  if (window.innerWidth >= 768) {
    sidebarOpen.value = true;
  } else {
    sidebarOpen.value = false;
  }
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
