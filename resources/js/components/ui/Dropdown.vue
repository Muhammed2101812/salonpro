<template>
  <Menu as="div" class="relative inline-block text-left">
    <MenuButton :class="buttonClasses">
      <slot name="trigger">
        <span>{{ label }}</span>
        <ChevronDownIcon class="ml-2 h-5 w-5" aria-hidden="true" />
      </slot>
    </MenuButton>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems :class="menuClasses">
        <slot />
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Menu, MenuButton, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

interface Props {
  label?: string
  align?: 'left' | 'right'
}

const props = withDefaults(defineProps<Props>(), {
  align: 'left',
})

const buttonClasses = computed(() => {
  return 'inline-flex justify-center items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
})

const menuClasses = computed(() => {
  const base = 'absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none'
  const alignment = props.align === 'right' ? 'right-0' : 'left-0'
  return [base, alignment]
})
</script>
