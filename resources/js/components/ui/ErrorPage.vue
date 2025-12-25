<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="text-center">
      <!-- Error Icon -->
      <div class="mb-8">
        <div :class="iconContainerClass">
          <component :is="iconComponent" :class="iconClass" />
        </div>
      </div>

      <!-- Error Code -->
      <h1 class="text-7xl font-bold text-gray-200 mb-4">{{ code }}</h1>

      <!-- Title -->
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">{{ title }}</h2>

      <!-- Description -->
      <p class="text-gray-500 max-w-md mx-auto mb-8">{{ description }}</p>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <router-link
          to="/"
          class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
        >
          <HomeIcon class="h-5 w-5 mr-2" />
          Ana Sayfa
        </router-link>
        <button
          @click="goBack"
          class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5 mr-2" />
          Geri Dön
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import {
  HomeIcon,
  ArrowLeftIcon,
  ExclamationTriangleIcon,
  LockClosedIcon,
  MagnifyingGlassIcon,
  ServerIcon,
} from '@heroicons/vue/24/outline';

interface Props {
  code?: number | string;
  title?: string;
  description?: string;
}

const props = withDefaults(defineProps<Props>(), {
  code: 404,
  title: 'Sayfa bulunamadı',
  description: 'Aradığınız sayfa mevcut değil veya taşınmış olabilir.',
});

const router = useRouter();

const goBack = () => {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push('/');
  }
};

const iconComponent = computed(() => {
  switch (props.code) {
    case 401:
    case 403:
      return LockClosedIcon;
    case 404:
      return MagnifyingGlassIcon;
    case 500:
    case 502:
    case 503:
      return ServerIcon;
    default:
      return ExclamationTriangleIcon;
  }
});

const iconContainerClass = computed(() => {
  const base = 'w-24 h-24 rounded-full flex items-center justify-center mx-auto';
  switch (props.code) {
    case 401:
    case 403:
      return `${base} bg-yellow-100`;
    case 500:
    case 502:
    case 503:
      return `${base} bg-red-100`;
    default:
      return `${base} bg-blue-100`;
  }
});

const iconClass = computed(() => {
  const base = 'h-12 w-12';
  switch (props.code) {
    case 401:
    case 403:
      return `${base} text-yellow-600`;
    case 500:
    case 502:
    case 503:
      return `${base} text-red-600`;
    default:
      return `${base} text-blue-600`;
  }
});
</script>
