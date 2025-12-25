<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürünler</h1>
        <p class="mt-2 text-sm text-gray-600">Salon ürünlerinizi ve stok durumlarını yönetin</p>
      </div>
      <div class="flex gap-3">
        <div class="flex rounded-lg border border-gray-200 overflow-hidden bg-white">
            <button
              @click="viewMode = 'grid'"
              :class="[ 'p-2 transition-colors', viewMode === 'grid' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Kart Görünümü"
            >
              <Squares2X2Icon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'table'"
              :class="[ 'p-2 transition-colors', viewMode === 'table' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Liste Görünümü"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
        </div>
        <Button variant="outline" @click="exportProducts" :icon="ArrowDownTrayIcon" label="Dışa Aktar" />
        <Button variant="primary" @click="openCreateModal" :icon="PlusIcon" label="Ürün Ekle" />
      </div>
    </div>

    <!-- Stats -->
    <ProductStats :stats="stats" />

    <!-- Alerts -->
    <div v-if="lowStockProducts.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
        <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5" />
        <div>
          <h4 class="font-semibold text-yellow-800">Stok Uyarısı</h4>
          <p class="text-sm text-yellow-700 mt-1">Aşağıdaki ürünlerin stoğu kritik seviyede:</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span
              v-for="product in lowStockProducts.slice(0, 5)"
              :key="product.id"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
            >
              {{ product.name }} ({{ product.stock_quantity }} {{ product.unit }})
            </span>
            <span v-if="lowStockProducts.length > 5" class="text-xs text-yellow-700">
              +{{ lowStockProducts.length - 5 }} ürün daha
            </span>
          </div>
        </div>
    </div>

    <!-- Controls -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
           <Input v-model="search" placeholder="Ürün, barkod veya SKU ara..." class="w-full lg:w-64">
                <template #prefix>
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </template>
           </Input>

           <select
             v-model="filters.category"
             class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm py-2 px-3"
           >
             <option value="">Tüm Kategoriler</option>
             <option v-for="cat in uniqueCategories" :key="cat" :value="cat">
               {{ cat }}
             </option>
           </select>

           <div class="flex rounded-lg border border-gray-200 overflow-hidden">
             <button
               v-for="status in stockStatusFilters"
               :key="status.value"
               @click="filters.stockStatus = filters.stockStatus === status.value ? '' : status.value"
               :class="[
                 'px-3 py-2 text-xs font-medium transition-colors',
                 filters.stockStatus === status.value
                   ? status.activeClass
                   : 'bg-white text-gray-700 hover:bg-gray-50'
               ]"
             >
               {{ status.label }}
             </button>
           </div>
        </div>

        <Button variant="ghost" @click="loadData" :icon="ArrowPathIcon" />
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="productStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
       <ProductCard
         v-for="product in filteredProducts"
         :key="product.id"
         :product="product"
         @edit="openEditModal"
         @delete="handleDelete"
       />
       <div v-if="filteredProducts.length === 0" class="col-span-full text-center py-12 text-gray-500">
          <CubeIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
          Ürün bulunamadı
       </div>
    </div>

    <!-- Table View -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredProducts"
        :exportable="true"
        export-filename="urunler"
        export-title="Ürün Listesi"
    >
        <template #cell-name="{ row }">
            <div class="flex items-center gap-3">
              <div :class="['w-1.5 h-10 rounded-full', getStockBarColor(row)]"></div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ row.name }}</p>
                <p class="text-xs text-gray-500">{{ row.unit }}</p>
              </div>
            </div>
        </template>
        <template #cell-codes="{ row }">
            <div class="text-sm text-gray-600">{{ row.barcode || '-' }}</div>
            <div class="text-xs text-gray-500">{{ row.sku || '-' }}</div>
        </template>
        <template #cell-price="{ row }">
            <div class="text-sm font-medium text-gray-900">{{ formatCurrency(row.price) }}</div>
            <div v-if="row.cost_price" class="text-xs text-gray-500">Maliyet: {{ formatCurrency(row.cost_price) }}</div>
        </template>
        <template #cell-stock="{ row }">
            <div :class="['text-sm font-medium', getStockTextColor(row)]">
              {{ row.stock_quantity }} {{ row.unit }}
            </div>
            <div class="text-xs text-gray-500">Min: {{ row.min_stock_quantity }}</div>
        </template>
        <template #cell-status="{ row }">
            <span :class="['px-2 py-1 text-xs rounded-full font-semibold', getStockBadge(row)]">
              {{ getStockLabel(row) }}
            </span>
        </template>
        <template #actions="{ row }">
            <div class="flex items-center justify-end gap-2">
              <Button variant="ghost" size="sm" @click="openEditModal(row)">
                  <PencilIcon class="h-4 w-4 text-primary" />
              </Button>
              <Button variant="ghost" size="sm" @click="handleDelete(row.id)">
                  <TrashIcon class="h-4 w-4 text-danger" />
              </Button>
            </div>
        </template>
    </DataTable>

    <!-- Modal -->
    <ProductModal
        v-model="showModal"
        :is-edit="isEdit"
        :initial-data="modalData"
        :loading="productStore.loading"
        :unique-categories="uniqueCategories"
        @submit="handleSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ListBulletIcon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import ProductStats from '@/components/product/ProductStats.vue'
import ProductModal from '@/components/product/ProductModal.vue'
import ProductCard from '@/components/product/ProductCard.vue'
import { useProductStore } from '@/stores/product'

interface Product {
  id: string
  name: string
  description?: string
  barcode?: string
  sku?: string
  price: number
  cost_price?: number
  stock_quantity: number
  min_stock_quantity: number
  unit: string
  category?: string
  is_active: boolean
  is_low_stock?: boolean
  is_out_of_stock?: boolean
  [key: string]: any
}

const productStore = useProductStore()

// State
const viewMode = ref<'grid' | 'table'>('grid')
const search = ref('')
const showModal = ref(false)
const isEdit = ref(false)
const modalData = ref<any>(null)
const editingId = ref<string | null>(null)

const filters = ref({
  category: '',
  stockStatus: ''
})

const stats = ref({
  totalProducts: 0,
  inStock: 0,
  lowStock: 0,
  outOfStock: 0,
  totalValue: 0
})

const stockStatusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-primary text-white' },
  { value: 'inStock', label: 'Stokta', activeClass: 'bg-green-600 text-white' },
  { value: 'lowStock', label: 'Az Stok', activeClass: 'bg-yellow-600 text-white' },
  { value: 'outOfStock', label: 'Tükendi', activeClass: 'bg-red-600 text-white' }
]

const tableColumns = [
    { key: 'name', label: 'Ürün' },
    { key: 'codes', label: 'Barkod / SKU' },
    { key: 'category', label: 'Kategori' },
    { key: 'price', label: 'Fiyat' },
    { key: 'stock', label: 'Stok' },
    { key: 'status', label: 'Durum' }
]

// Computed
const uniqueCategories = computed(() => {
  const categories = productStore.products.map((p: Product) => p.category).filter(c => c)
  return [...new Set(categories)]
})

const filteredProducts = computed(() => {
  let result = productStore.products as Product[]

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(p =>
      p.name?.toLowerCase().includes(searchLower) ||
      p.barcode?.toLowerCase().includes(searchLower) ||
      p.sku?.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.category) {
    result = result.filter(p => p.category === filters.value.category)
  }

  if (filters.value.stockStatus === 'inStock') {
    result = result.filter(p => !p.is_out_of_stock && !p.is_low_stock)
  } else if (filters.value.stockStatus === 'lowStock') {
    result = result.filter(p => p.is_low_stock && !p.is_out_of_stock)
  } else if (filters.value.stockStatus === 'outOfStock') {
    result = result.filter(p => p.is_out_of_stock)
  }

  return result
})

const lowStockProducts = computed(() => {
  return (productStore.products as Product[]).filter(p => p.is_low_stock || p.is_out_of_stock)
})

// Helpers
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

const getStockLabel = (product: Product) => {
  if (product.is_out_of_stock) return 'Tükendi'
  if (product.is_low_stock) return 'Az Stok'
  if (product.is_active) return 'Stokta'
  return 'Pasif'
}

const getStockBadge = (product: Product) => {
  if (product.is_out_of_stock) return 'bg-red-100 text-red-800'
  if (product.is_low_stock) return 'bg-yellow-100 text-yellow-800'
  if (product.is_active) return 'bg-green-100 text-green-800'
  return 'bg-gray-100 text-gray-800'
}

const getStockBarColor = (product: Product) => {
  if (product.is_out_of_stock) return 'bg-red-500'
  if (product.is_low_stock) return 'bg-yellow-500'
  return 'bg-green-500'
}

const getStockTextColor = (product: Product) => {
  if (product.is_out_of_stock) return 'text-red-600'
  if (product.is_low_stock) return 'text-yellow-600'
  return 'text-green-600'
}

const updateStats = () => {
  const products = productStore.products as Product[]
  stats.value.totalProducts = products.length
  stats.value.inStock = products.filter(p => !p.is_out_of_stock && !p.is_low_stock).length
  stats.value.lowStock = products.filter(p => p.is_low_stock && !p.is_out_of_stock).length
  stats.value.outOfStock = products.filter(p => p.is_out_of_stock).length
  stats.value.totalValue = products.reduce((acc, p) => acc + (Number(p.price) * p.stock_quantity), 0)
}

const loadData = async () => {
  await productStore.fetchProducts()
  updateStats()
}

// Modal Actions
const openCreateModal = () => {
  modalData.value = null
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (product: Product) => {
  modalData.value = { ...product }
  isEdit.value = true
  editingId.value = product.id
  showModal.value = true
}

const handleSubmit = async (data: any) => {
  try {
    if (isEdit.value && editingId.value) {
      await productStore.updateProduct(editingId.value, data)
    } else {
      await productStore.createProduct(data)
    }
    showModal.value = false
    updateStats()
  } catch (error) {
    console.error('Ürün kaydedilemedi:', error)
  }
}

const handleDelete = async (id: string) => {
  if (!confirm('Bu ürünü silmek istediğinizden emin misiniz?')) return
  try {
    await productStore.deleteProduct(id)
    updateStats()
  } catch (error) {
    console.error('Ürün silinemedi:', error)
  }
}

const exportProducts = () => {
  const csvContent = [
    ['Ürün Adı', 'Barkod', 'SKU', 'Kategori', 'Satış Fiyatı', 'Maliyet', 'Stok', 'Min Stok', 'Birim', 'Durum'].join(','),
    ...filteredProducts.value.map(p => [
      p.name,
      p.barcode || '',
      p.sku || '',
      p.category || '',
      p.price,
      p.cost_price || '',
      p.stock_quantity,
      p.min_stock_quantity,
      p.unit,
      getStockLabel(p)
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `urunler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>
