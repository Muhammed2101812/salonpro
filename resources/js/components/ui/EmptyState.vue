<template>
  <div class="flex flex-col items-center justify-center py-12 px-4">
    <!-- Icon -->
    <div :class="iconContainerClass">
      <component :is="iconComponent" class="h-12 w-12" :class="iconClass" />
    </div>

    <!-- Title -->
    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ title }}</h3>

    <!-- Description -->
    <p class="mt-2 text-sm text-gray-500 text-center max-w-sm">{{ description }}</p>

    <!-- Action -->
    <div v-if="actionText" class="mt-6">
      <button
        @click="$emit('action')"
        :class="actionButtonClass"
      >
        <component v-if="actionIcon" :is="actionIcon" class="h-5 w-5 mr-2" />
        {{ actionText }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  InboxIcon,
  MagnifyingGlassIcon,
  DocumentIcon,
  UserGroupIcon,
  CalendarDaysIcon,
  ShoppingBagIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  WifiIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline';

interface Props {
  type?: 'empty' | 'search' | 'error' | 'offline' | 'no-results';
  title?: string;
  description?: string;
  icon?: string;
  actionText?: string;
  actionIcon?: any;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'empty',
  title: 'Veri bulunamadı',
  description: 'Henüz kayıt eklenmemiş.',
});

defineEmits<{
  action: [];
}>();

const iconMap: Record<string, any> = {
  inbox: InboxIcon,
  search: MagnifyingGlassIcon,
  document: DocumentIcon,
  users: UserGroupIcon,
  calendar: CalendarDaysIcon,
  shopping: ShoppingBagIcon,
  cube: CubeIcon,
  warning: ExclamationTriangleIcon,
  wifi: WifiIcon,
  plus: PlusIcon,
};

const iconComponent = computed(() => {
  if (props.icon && iconMap[props.icon]) {
    return iconMap[props.icon];
  }
  
  switch (props.type) {
    case 'search':
    case 'no-results':
      return MagnifyingGlassIcon;
    case 'error':
      return ExclamationTriangleIcon;
    case 'offline':
      return WifiIcon;
    default:
      return InboxIcon;
  }
});

const iconContainerClass = computed(() => {
  const base = 'w-20 h-20 rounded-full flex items-center justify-center';
  switch (props.type) {
    case 'error':
      return `${base} bg-red-100`;
    case 'offline':
      return `${base} bg-yellow-100`;
    default:
      return `${base} bg-gray-100`;
  }
});

const iconClass = computed(() => {
  switch (props.type) {
    case 'error':
      return 'text-red-500';
    case 'offline':
      return 'text-yellow-500';
    default:
      return 'text-gray-400';
  }
});

const actionButtonClass = computed(() => {
  const base = 'inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors';
  switch (props.type) {
    case 'error':
      return `${base} bg-red-600 hover:bg-red-700 text-white`;
    default:
      return `${base} bg-blue-600 hover:bg-blue-700 text-white`;
  }
});
</script>
