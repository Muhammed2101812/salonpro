<template>
  <Dropdown align="right">
    <template #trigger>
      <button
        type="button"
        class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:text-gray-900 border border-gray-300 rounded-md hover:bg-gray-50"
      >
        <BuildingOfficeIcon class="h-5 w-5" />
        <span class="hidden sm:inline font-medium">{{ currentBranch?.name || 'Şube Seç' }}</span>
        <ChevronDownIcon class="h-4 w-4" />
      </button>
    </template>

    <!-- Loading State -->
    <div v-if="branchStore.loading" class="px-4 py-3">
      <p class="text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Branch List -->
    <template v-else>
      <MenuItem
        v-for="branch in branches"
        :key="branch.id"
        v-slot="{ active }"
      >
        <button
          type="button"
          :class="[
            active ? 'bg-gray-100' : '',
            currentBranch?.id === branch.id ? 'bg-blue-50 text-blue-700' : 'text-gray-700',
            'group flex w-full items-center px-4 py-2 text-sm',
          ]"
          @click="switchBranch(branch)"
        >
          <CheckIcon
            v-if="currentBranch?.id === branch.id"
            class="mr-3 h-5 w-5 text-blue-600"
            aria-hidden="true"
          />
          <BuildingOfficeIcon
            v-else
            class="mr-3 h-5 w-5 text-gray-400"
            aria-hidden="true"
          />
          <div class="flex-1 text-left">
            <div class="font-medium">{{ branch.name }}</div>
            <div v-if="branch.city" class="text-xs text-gray-500">
              {{ branch.city }}
            </div>
          </div>
          <div
            v-if="!branch.is_active"
            class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded"
          >
            Pasif
          </div>
        </button>
      </MenuItem>

      <!-- Divider -->
      <div class="border-t border-gray-100"></div>

      <!-- Manage Branches Link -->
      <MenuItem v-slot="{ active }">
        <router-link
          to="/branches"
          :class="[
            active ? 'bg-gray-100' : '',
            'group flex items-center px-4 py-2 text-sm text-gray-700',
          ]"
        >
          <CogIcon class="mr-3 h-5 w-5 text-gray-400" aria-hidden="true" />
          Şubeleri Yönet
        </router-link>
      </MenuItem>
    </template>
  </Dropdown>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useBranchStore } from '@/stores/branch'
import { MenuItem } from '@headlessui/vue'
import {
  BuildingOfficeIcon,
  ChevronDownIcon,
  CheckIcon,
  CogIcon,
} from '@heroicons/vue/24/outline'
import Dropdown from '@/components/ui/Dropdown.vue'

const branchStore = useBranchStore()

// Computed properties
const branches = computed(() => branchStore.branches)
const currentBranch = computed(() => branchStore.currentBranch)

// Switch branch
const switchBranch = async (branch: any) => {
  if (branch.id === currentBranch.value?.id) {
    return // Already selected
  }

  try {
    await branchStore.setCurrentBranch(branch.id)

    // Optionally reload the page to refresh all data
    // window.location.reload()

    // Or emit event to notify parent components
    // emit('branch-changed', branch)
  } catch (error) {
    console.error('Failed to switch branch:', error)
  }
}

// Load branches on mount
if (branches.value.length === 0) {
  branchStore.fetchBranches()
}
</script>
