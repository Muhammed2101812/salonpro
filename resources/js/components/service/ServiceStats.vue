<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-teal-100">
          <SparklesIcon class="h-6 w-6 text-teal-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Toplam Hizmet</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.totalServices }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100">
          <FolderIcon class="h-6 w-6 text-purple-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Kategori</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.totalCategories }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-green-100">
          <CheckCircleIcon class="h-6 w-6 text-green-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Aktif Hizmet</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.activeServices }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <CurrencyDollarIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Ort. Fiyat</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.avgPrice) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-orange-100">
          <ClockIcon class="h-6 w-6 text-orange-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Ort. Süre</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.avgDuration }} dk</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  SparklesIcon,
  FolderIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    totalServices: number
    totalCategories: number
    activeServices: number
    avgPrice: number
    avgDuration: number
  }
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}
</script>
