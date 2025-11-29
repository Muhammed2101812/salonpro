<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stok Hareketleri</h1>
        <p class="mt-2 text-sm text-gray-600">Stok giriş/çıkış hareketlerini yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-600 hover:bg-slate-700"
      >
        <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
        Yeni Hareket
      </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex gap-4">
        <div class="flex-1">
          <input
            v-model="search"
            type="text"
            placeholder="Ara..."
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
          />
        </div>
        <button
          @click="loadData"
          class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200"
        >
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Ürün
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tip
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Miktar
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Önceki Stok
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Sonraki Stok
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tarih
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in items" :key="item.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.product?.name || 'Bilinmiyor' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="getTypeClass(item.type)">
                {{ getTypeLabel(item.type) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.quantity }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ item.quantity_before }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ item.quantity_after }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(item.movement_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="deleteItem(item)"
                class="text-red-600 hover:text-red-900"
              >
                Sil
              </button>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              Henüz stok hareketi bulunmuyor.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Modal -->
    <FormModal
      v-model="showModal"
      title="Yeni Stok Hareketi"
      @save="saveItem"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Ürün *</label>
          <select
            v-model="formData.product_id"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
          >
            <option value="">Ürün seçin</option>
            <option v-for="product in products" :key="product.id" :value="product.id">
              {{ product.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Hareket Tipi *</label>
          <select
            v-model="formData.type"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
          >
            <option value="in">Giriş</option>
            <option value="out">Çıkış</option>
            <option value="adjustment">Düzeltme</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Miktar *</label>
          <input
            v-model.number="formData.quantity"
            type="number"
            min="1"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Hareket Tarihi *</label>
          <input
            v-model="formData.movement_date"
            type="date"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Açıklama</label>
          <textarea
            v-model="formData.reason"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
            placeholder="Hareket sebebini yazın..."
          ></textarea>
        </div>
      </div>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { PlusIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import { useInventoryMovementStore } from '@/stores/inventorymovement'
import { useProductStore } from '@/stores/product'
import FormModal from '@/components/FormModal.vue'

const store = useInventoryMovementStore()
const productStore = useProductStore()

const items = ref<any[]>([])
const products = ref<any[]>([])
const meta = ref<any>({})
const search = ref('')
const showModal = ref(false)
const loading = ref(false)

const formData = ref({
  product_id: '',
  type: 'in',
  quantity: 1,
  movement_date: new Date().toISOString().split('T')[0],
  reason: ''
})

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({ search: search.value })
    items.value = response?.data || []
    meta.value = response?.meta || {}
  } catch (error) {
    console.error('Stok hareketleri yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const loadProducts = async () => {
  try {
    await productStore.fetchProducts()
    products.value = productStore.products
  } catch (error) {
    console.error('Ürünler yüklenemedi:', error)
  }
}

const openCreateModal = () => {
  formData.value = {
    product_id: '',
    type: 'in',
    quantity: 1,
    movement_date: new Date().toISOString().split('T')[0],
    reason: ''
  }
  showModal.value = true
}

const saveItem = async () => {
  try {
    await store.create(formData.value)
    showModal.value = false
    loadData()
  } catch (error: any) {
    console.error('Stok hareketi oluşturulamadı:', error)
    alert(error.response?.data?.message || 'Bir hata oluştu')
  }
}

const deleteItem = async (item: any) => {
  if (confirm('Bu stok hareketini silmek istediğinizden emin misiniz?')) {
    try {
      await store.delete(item.id)
      loadData()
    } catch (error) {
      console.error('Stok hareketi silinemedi:', error)
    }
  }
}

const getTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    in: 'Giriş',
    out: 'Çıkış',
    adjustment: 'Düzeltme'
  }
  return labels[type] || type
}

const getTypeClass = (type: string) => {
  const classes: Record<string, string> = {
    in: 'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800',
    out: 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800',
    adjustment: 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800'
  }
  return classes[type] || ''
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

onMounted(() => {
  loadData()
  loadProducts()
})
</script>
