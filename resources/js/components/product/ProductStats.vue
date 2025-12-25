<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-amber-100">
          <CubeIcon class="h-6 w-6 text-amber-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Toplam Ürün</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.totalProducts }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-green-100">
          <CheckCircleIcon class="h-6 w-6 text-green-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Stokta</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.inStock }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-yellow-100">
          <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Az Stok</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.lowStock }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-red-100">
          <XCircleIcon class="h-6 w-6 text-red-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Tükendi</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.outOfStock }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <BanknotesIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Stok Değeri</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalValue) }}</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  CubeIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    totalProducts: number
    inStock: number
    lowStock: number
    outOfStock: number
    totalValue: number
  }
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}
</script>
