<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stok Yönetimi</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün stok giriş, çıkış ve hareketlerini takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportMovements"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Stok Hareketi
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100">
            <CubeIcon class="h-6 w-6 text-teal-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Hareket</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalMovements }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <ArrowUpIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Giriş</p>
            <p class="text-2xl font-bold text-green-600">+{{ stats.totalIn }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <ArrowDownIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Çıkış</p>
            <p class="text-2xl font-bold text-red-600">-{{ stats.totalOut }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <AdjustmentsHorizontalIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Düzeltme</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.totalAdjustment }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <!-- Arama -->
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Ürün ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
            />
          </div>

          <!-- Ürün Filtresi -->
          <select
            v-model="filters.productId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
          >
            <option value="">Tüm Ürünler</option>
            <option v-for="product in products" :key="product.id" :value="product.id">
              {{ product.name }}
            </option>
          </select>

          <!-- Hareket Tipi Filtreleri -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="type in movementTypeFilters"
              :key="type.value"
              @click="filters.type = filters.type === type.value ? '' : type.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.type === type.value ? type.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ type.label }}
            </button>
          </div>

          <!-- Tarih Aralığı -->
          <div class="flex items-center gap-2">
            <input
              v-model="filters.startDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
            />
            <span class="text-gray-400">-</span>
            <input
              v-model="filters.endDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
            />
          </div>
        </div>

        <button
          @click="loadData"
          class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="inventoryStore.loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Tablo -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Değişimi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="movement in filteredMovements" :key="movement.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div :class="['w-2 h-10 rounded', getTypeBarColor(movement.type)]"></div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(movement.movement_date) }}</p>
                  <p class="text-xs text-gray-500">{{ formatTime(movement.created_at) }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm font-medium text-gray-900">{{ movement.product?.name || getProductName(movement.product_id) }}</p>
              <p class="text-xs text-gray-500">{{ movement.product?.sku || '-' }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="['px-2 py-1 text-xs rounded-full font-semibold inline-flex items-center gap-1', getTypeBadge(movement.type)]">
                <component :is="getTypeIcon(movement.type)" class="h-3 w-3" />
                {{ getTypeLabel(movement.type) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="['text-lg font-bold', getQuantityColor(movement.type)]">
                {{ movement.type === 'in' ? '+' : movement.type === 'out' ? '-' : '' }}{{ movement.quantity }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">{{ movement.quantity_before }}</span>
                <ArrowRightIcon class="h-4 w-4 text-gray-400" />
                <span :class="['font-medium', movement.quantity_after > movement.quantity_before ? 'text-green-600' : movement.quantity_after < movement.quantity_before ? 'text-red-600' : 'text-gray-600']">
                  {{ movement.quantity_after }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-500 truncate max-w-[200px]">{{ movement.reason || '-' }}</p>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredMovements.length === 0" class="p-12 text-center">
        <CubeIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Stok hareketi bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-teal-600 hover:text-teal-700 font-medium">
          Stok hareketi ekleyin
        </button>
      </div>
    </div>

    <!-- Stok Hareketi Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">Stok Hareketi Ekle</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Ürün -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ürün *</label>
            <select
              v-model="form.product_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            >
              <option value="">Ürün Seçin</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }} (Stok: {{ product.stock_quantity }})
              </option>
            </select>
          </div>

          <!-- Hareket Tipi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hareket Tipi *</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                type="button"
                @click="form.type = 'in'"
                :class="[
                  'flex flex-col items-center p-4 rounded-lg border transition-colors',
                  form.type === 'in'
                    ? 'bg-green-50 border-green-500 text-green-700'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
              >
                <ArrowUpIcon class="h-6 w-6 mb-1" />
                <span class="font-medium">Giriş</span>
              </button>
              <button
                type="button"
                @click="form.type = 'out'"
                :class="[
                  'flex flex-col items-center p-4 rounded-lg border transition-colors',
                  form.type === 'out'
                    ? 'bg-red-50 border-red-500 text-red-700'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
              >
                <ArrowDownIcon class="h-6 w-6 mb-1" />
                <span class="font-medium">Çıkış</span>
              </button>
              <button
                type="button"
                @click="form.type = 'adjustment'"
                :class="[
                  'flex flex-col items-center p-4 rounded-lg border transition-colors',
                  form.type === 'adjustment'
                    ? 'bg-blue-50 border-blue-500 text-blue-700'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
              >
                <AdjustmentsHorizontalIcon class="h-6 w-6 mb-1" />
                <span class="font-medium">Düzeltme</span>
              </button>
            </div>
          </div>

          <!-- Miktar ve Tarih -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Miktar *</label>
              <input
                v-model.number="form.quantity"
                type="number"
                min="1"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-lg font-bold"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
              <input
                v-model="form.movement_date"
                type="date"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
              />
            </div>
          </div>

          <!-- Seçili Ürün Stok Gösterimi -->
          <div v-if="selectedProduct" class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-sm text-gray-500">Mevcut Stok</p>
                <p class="text-xl font-bold text-gray-900">{{ selectedProduct.stock_quantity }}</p>
              </div>
              <ArrowRightIcon class="h-6 w-6 text-gray-400" />
              <div>
                <p class="text-sm text-gray-500">Yeni Stok</p>
                <p :class="['text-xl font-bold', calculatedNewStock >= 0 ? 'text-green-600' : 'text-red-600']">
                  {{ calculatedNewStock }}
                </p>
              </div>
            </div>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea
              v-model="form.reason"
              rows="2"
              placeholder="Stok hareketinin nedeni..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            ></textarea>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="inventoryStore.loading || calculatedNewStock < 0"
              class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ inventoryStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  PlusIcon,
  CubeIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  AdjustmentsHorizontalIcon,
  MagnifyingGlassIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline'
import { useInventoryStore } from '@/stores/inventory'
import { useProductStore } from '@/stores/product'

interface Movement {
  id: string
  product_id: string
  product?: { id: string; name: string; sku?: string }
  type: 'in' | 'out' | 'adjustment'
  quantity: number
  quantity_before: number
  quantity_after: number
  reason?: string
  movement_date: string
  created_at?: string
}

interface Product {
  id: string
  name: string
  stock_quantity: number
  sku?: string
}

const inventoryStore = useInventoryStore()
const productStore = useProductStore()

// State
const showModal = ref(false)
const search = ref('')
const products = ref<Product[]>([])

const filters = ref({
  productId: '',
  type: '',
  startDate: '',
  endDate: ''
})

const stats = ref({
  totalMovements: 0,
  totalIn: 0,
  totalOut: 0,
  totalAdjustment: 0
})

const movementTypeFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-teal-600 text-white' },
  { value: 'in', label: 'Giriş', activeClass: 'bg-green-600 text-white' },
  { value: 'out', label: 'Çıkış', activeClass: 'bg-red-600 text-white' },
  { value: 'adjustment', label: 'Düzeltme', activeClass: 'bg-blue-600 text-white' }
]

const form = ref({
  product_id: '',
  type: 'in' as 'in' | 'out' | 'adjustment',
  quantity: 1,
  reason: '',
  movement_date: new Date().toISOString().split('T')[0]
})

// Computed
const filteredMovements = computed(() => {
  let result = inventoryStore.movements as Movement[]

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(m => 
      m.product?.name?.toLowerCase().includes(searchLower) ||
      getProductName(m.product_id).toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.productId) {
    result = result.filter(m => m.product_id === filters.value.productId)
  }

  if (filters.value.type) {
    result = result.filter(m => m.type === filters.value.type)
  }

  if (filters.value.startDate) {
    result = result.filter(m => m.movement_date >= filters.value.startDate)
  }

  if (filters.value.endDate) {
    result = result.filter(m => m.movement_date <= filters.value.endDate)
  }

  return result.sort((a, b) => new Date(b.movement_date).getTime() - new Date(a.movement_date).getTime())
})

const selectedProduct = computed(() => {
  return products.value.find(p => p.id === form.value.product_id)
})

const calculatedNewStock = computed(() => {
  if (!selectedProduct.value) return 0
  const current = selectedProduct.value.stock_quantity || 0
  if (form.value.type === 'in') return current + form.value.quantity
  if (form.value.type === 'out') return current - form.value.quantity
  return form.value.quantity // adjustment = new value
})

// Helpers
const formatDate = (dateString: string) => new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(dateString))
const formatTime = (dateString?: string) => dateString ? new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateString)) : ''

const getProductName = (id: string) => products.value.find(p => p.id === id)?.name || '-'
const getTypeLabel = (type: string) => ({ in: 'Giriş', out: 'Çıkış', adjustment: 'Düzeltme' }[type] || type)
const getTypeBadge = (type: string) => ({ in: 'bg-green-100 text-green-800', out: 'bg-red-100 text-red-800', adjustment: 'bg-blue-100 text-blue-800' }[type] || 'bg-gray-100')
const getTypeBarColor = (type: string) => ({ in: 'bg-green-500', out: 'bg-red-500', adjustment: 'bg-blue-500' }[type] || 'bg-gray-500')
const getQuantityColor = (type: string) => ({ in: 'text-green-600', out: 'text-red-600', adjustment: 'text-blue-600' }[type] || 'text-gray-600')
const getTypeIcon = (type: string) => ({ in: markRaw(ArrowUpIcon), out: markRaw(ArrowDownIcon), adjustment: markRaw(AdjustmentsHorizontalIcon) }[type] || markRaw(CubeIcon))

const updateStats = () => {
  const movements = inventoryStore.movements as Movement[]
  stats.value.totalMovements = movements.length
  stats.value.totalIn = movements.filter(m => m.type === 'in').reduce((acc, m) => acc + m.quantity, 0)
  stats.value.totalOut = movements.filter(m => m.type === 'out').reduce((acc, m) => acc + m.quantity, 0)
  stats.value.totalAdjustment = movements.filter(m => m.type === 'adjustment').length
}

// Modal Methods
const openCreateModal = () => {
  form.value = { product_id: '', type: 'in', quantity: 1, reason: '', movement_date: new Date().toISOString().split('T')[0] }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  if (calculatedNewStock < 0) {
    alert('Stok yetersiz!')
    return
  }
  try {
    await inventoryStore.createMovement(form.value)
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Stok hareketi eklenemedi:', error)
    alert('Stok hareketi eklenemedi')
  }
}

const loadData = async () => {
  await inventoryStore.fetchMovements()
  await productStore.fetchProducts()
  products.value = productStore.products || []
  updateStats()
}

const exportMovements = () => {
  const csvContent = [
    ['Tarih', 'Ürün', 'Tip', 'Miktar', 'Önceki', 'Sonraki', 'Açıklama'].join(','),
    ...filteredMovements.value.map(m => [
      m.movement_date,
      m.product?.name || getProductName(m.product_id),
      getTypeLabel(m.type),
      m.quantity,
      m.quantity_before,
      m.quantity_after,
      m.reason || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `stok_hareketleri_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>
