<template>
  <div :class="[
      'bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow group',
      getProductBorderColor(product)
    ]">
    <!-- Status Bar -->
    <div :class="['h-2', getStockBarColor(product)]"></div>

    <div class="p-5">
      <!-- Header -->
      <div class="flex items-start justify-between mb-3">
        <div>
          <h4 class="font-semibold text-gray-900 group-hover:text-primary transition-colors">{{ product.name }}</h4>
          <p class="text-xs text-gray-500">{{ product.category || 'Kategorisiz' }}</p>
        </div>
        <span :class="['px-2 py-1 text-xs font-medium rounded-full', getStockBadge(product)]">
          {{ getStockLabel(product) }}
        </span>
      </div>

      <p v-if="product.description" class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[40px]">
        {{ product.description }}
      </p>
      <div v-else class="min-h-[40px]"></div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <p class="text-lg font-bold text-primary">{{ formatCurrency(product.price) }}</p>
          <p class="text-xs text-gray-500">Satış Fiyatı</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <p :class="['text-lg font-bold', getStockTextColor(product)]">{{ product.stock_quantity }}</p>
          <p class="text-xs text-gray-500">{{ product.unit }}</p>
        </div>
      </div>

      <!-- IDs -->
      <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
        <span v-if="product.barcode" class="font-mono bg-gray-100 px-1 rounded">Barkod: {{ product.barcode }}</span>
        <span v-else></span>
        <span v-if="product.sku" class="font-mono bg-gray-100 px-1 rounded">SKU: {{ product.sku }}</span>
      </div>

      <!-- Progress Bar -->
      <div class="mb-4">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
          <span>Stok Durumu</span>
          <span>Min: {{ product.min_stock_quantity }}</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
          <div
            :class="['h-2 rounded-full transition-all', getStockProgressColor(product)]"
            :style="{ width: `${Math.min((product.stock_quantity / Math.max(product.min_stock_quantity * 3, 1)) * 100, 100)}%` }"
          ></div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
        <Button variant="ghost" size="sm" @click="$emit('edit', product)">
            <PencilIcon class="h-4 w-4 text-primary" />
        </Button>
        <Button variant="ghost" size="sm" @click="$emit('delete', product.id)">
            <TrashIcon class="h-4 w-4 text-danger" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'

defineProps<{
  product: any
}>()

defineEmits<{
  edit: [product: any]
  delete: [id: string]
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

// Helpers logic duplicated for display purposes, could be shared utility
const getStockLabel = (product: any) => {
  if (product.is_out_of_stock) return 'Tükendi'
  if (product.is_low_stock) return 'Az Stok'
  if (product.is_active) return 'Stokta'
  return 'Pasif'
}

const getStockBadge = (product: any) => {
  if (product.is_out_of_stock) return 'bg-red-100 text-red-800'
  if (product.is_low_stock) return 'bg-yellow-100 text-yellow-800'
  if (product.is_active) return 'bg-green-100 text-green-800'
  return 'bg-gray-100 text-gray-800'
}

const getStockBarColor = (product: any) => {
  if (product.is_out_of_stock) return 'bg-red-500'
  if (product.is_low_stock) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getStockTextColor = (product: any) => {
  if (product.is_out_of_stock) return 'text-red-600'
  if (product.is_low_stock) return 'text-yellow-600'
  return 'text-green-600'
}

const getStockProgressColor = (product: any) => {
  if (product.is_out_of_stock) return 'bg-red-500'
  if (product.is_low_stock) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getProductBorderColor = (product: any) => {
  if (product.is_out_of_stock) return 'border-red-200'
  if (product.is_low_stock) return 'border-yellow-200'
  return 'border-gray-200'
}
</script>
