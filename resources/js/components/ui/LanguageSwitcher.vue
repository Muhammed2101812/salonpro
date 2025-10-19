<template>
  <Dropdown align="right">
    <template #trigger>
      <button
        type="button"
        class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:text-gray-900"
      >
        <LanguageIcon class="h-5 w-5" />
        <span class="hidden sm:inline">{{ currentLocale.name }}</span>
      </button>
    </template>

    <MenuItem
      v-for="locale in availableLocales"
      :key="locale.code"
      v-slot="{ active }"
    >
      <button
        type="button"
        :class="[
          active ? 'bg-gray-100' : '',
          currentLocale.code === locale.code ? 'bg-blue-50 text-blue-700' : 'text-gray-700',
          'group flex w-full items-center px-4 py-2 text-sm',
        ]"
        @click="switchLocale(locale.code)"
      >
        <CheckIcon
          v-if="currentLocale.code === locale.code"
          class="mr-3 h-5 w-5 text-blue-600"
          aria-hidden="true"
        />
        <span :class="currentLocale.code === locale.code ? '' : 'ml-8'">
          {{ locale.name }}
        </span>
      </button>
    </MenuItem>
  </Dropdown>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { MenuItem } from '@headlessui/vue'
import { LanguageIcon, CheckIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/components/ui/Dropdown.vue'

const { locale } = useI18n()

const availableLocales = [
  { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
  { code: 'en', name: 'English', flag: '🇬🇧' },
]

const currentLocale = computed(() => {
  return availableLocales.find((l) => l.code === locale.value) || availableLocales[0]
})

const switchLocale = (newLocale: string) => {
  locale.value = newLocale
  // Optionally save to localStorage
  localStorage.setItem('locale', newLocale)
  // Optionally save to backend user preferences
}
</script>
