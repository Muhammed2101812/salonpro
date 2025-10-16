<template>
  <div id="app">
    <template v-if="isAuthenticated && $route.meta.requiresAuth">
      <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <div class="flex-shrink-0 flex items-center">
                <h1 class="text-xl font-bold">SalonPro</h1>
              </div>
              <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                <router-link to="/" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-blue-200">
                  Ana Sayfa
                </router-link>
                <router-link to="/branches" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-blue-200">
                  Şubeler
                </router-link>
                <router-link to="/customers" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-blue-200">
                  Müşteriler
                </router-link>
                <router-link to="/employees" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-blue-200">
                  Çalışanlar
                </router-link>
              </div>
            </div>
            <div class="flex items-center">
              <span v-if="user" class="mr-4 text-sm">{{ user.name }}</span>
              <button @click="handleLogout" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded text-sm">
                Çıkış Yap
              </button>
            </div>
          </div>
        </div>
      </nav>
    </template>
    <router-view />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

onMounted(() => {
  if (isAuthenticated.value && !user.value) {
    authStore.fetchProfile();
  }
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
