<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Satın Alma Siparişleri</h1>
        <p class="mt-2 text-sm text-gray-600">Tedarikçilere verilen siparişleri yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportOrders"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Sipariş
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-lime-100">
            <ClipboardDocumentListIcon class="h-6 w-6 text-lime-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Sipariş</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bekleyen</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <TruckIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Yolda</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.ordered }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Teslim Edilen</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.delivered }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <CurrencyDollarIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Tutar</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalAmount) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Sipariş ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="status in statusFilters"
              :key="status.value"
              @click="filters.status = filters.status === status.value ? '' : status.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.status === status.value ? status.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ status.label }}
            </button>
          </div>

          <select v-model="filters.supplierId" class="rounded-lg border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 text-sm">
            <option value="">Tüm Tedarikçiler</option>
            <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-lime-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Tablo -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sipariş No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tedarikçi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="font-mono font-medium text-lime-600 bg-lime-50 px-2 py-1 rounded">
                {{ order.order_number || order.id?.slice(0, 8) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm font-medium text-gray-900">{{ getSupplierName(order.supplier_id) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDate(order.order_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-medium">
                {{ order.item_count || 0 }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <span class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total_amount) }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-3 py-1 text-xs rounded-full font-semibold', getStatusBadge(order.status)]">
                {{ getStatusLabel(order.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewOrder(order)"
                  class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Detay"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="order.status === 'pending'"
                  @click="approveOrder(order)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Onayla"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="order.status === 'ordered'"
                  @click="markDelivered(order)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Teslim Alındı"
                >
                  <TruckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="['draft', 'pending'].includes(order.status)"
                  @click="handleDelete(order.id)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="İptal"
                >
                  <XMarkIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredOrders.length === 0" class="p-12 text-center">
        <ClipboardDocumentListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Sipariş bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-lime-600 hover:text-lime-700 font-medium">
          Sipariş oluşturun
        </button>
      </div>
    </div>

    <!-- Sipariş Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Sipariş Düzenle' : 'Yeni Sipariş' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tedarikçi *</label>
              <select v-model="form.supplier_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                <option value="">Seçin</option>
                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Sipariş Tarihi *</label>
              <input v-model="form.order_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500" />
            </div>
          </div>

          <!-- Ürünler -->
          <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-900 mb-3">Sipariş Kalemleri</label>
            <div class="space-y-2">
              <div v-for="(item, index) in form.items" :key="index" class="flex gap-2 items-center bg-white p-2 rounded-lg">
                <select v-model="item.product_id" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                  <option value="">Ürün Seçin</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select>
                <input v-model.number="item.quantity" type="number" min="1" placeholder="Adet" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <input v-model.number="item.unit_price" type="number" min="0" step="0.01" placeholder="Birim Fiyat" class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <button type="button" @click="removeItem(index)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
            <button type="button" @click="addItem" class="mt-3 text-lime-600 hover:text-lime-700 text-sm font-medium inline-flex items-center">
              <PlusIcon class="h-4 w-4 mr-1" /> Ürün Ekle
            </button>
          </div>

          <!-- Toplam -->
          <div class="bg-lime-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-lime-900">Toplam Tutar</span>
              <span class="text-2xl font-bold text-lime-700">{{ formatCurrency(calculateTotal()) }}</span>
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-lime-600 hover:bg-lime-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  ClipboardDocumentListIcon,
  ClockIcon,
  TruckIcon,
  CheckCircleIcon,
  CurrencyDollarIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  CheckIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { usePurchaseOrderStore } from '@/stores/purchaseorder'
import { useSupplierStore } from '@/stores/supplier'
import { useProductStore } from '@/stores/product'

const orderStore = usePurchaseOrderStore()
const supplierStore = useSupplierStore()
const productStore = useProductStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ status: '', supplierId: '' })

const stats = ref({ total: 0, pending: 0, ordered: 0, delivered: 0, totalAmount: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-lime-600 text-white' },
  { value: 'pending', label: 'Bekleyen', activeClass: 'bg-yellow-600 text-white' },
  { value: 'ordered', label: 'Sipariş', activeClass: 'bg-blue-600 text-white' },
  { value: 'delivered', label: 'Teslim', activeClass: 'bg-green-600 text-white' }
]

const form = ref({
  supplier_id: '', order_date: new Date().toISOString().split('T')[0], notes: '',
  items: [{ product_id: '', quantity: 1, unit_price: 0 }]
})

const orders = computed(() => orderStore.orders || [])
const suppliers = computed(() => supplierStore.suppliers || [])
const products = computed(() => productStore.products || [])

const filteredOrders = computed(() => {
  let result = orders.value as any[]
  if (search.value) result = result.filter(o => o.order_number?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(o => o.status === filters.value.status)
  if (filters.value.supplierId) result = result.filter(o => o.supplier_id === filters.value.supplierId)
  return result.sort((a, b) => new Date(b.order_date).getTime() - new Date(a.order_date).getTime())
})

const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d)) : '-'
const getSupplierName = (id: string) => suppliers.value.find((s: any) => s.id === id)?.name || '-'
const getStatusLabel = (status: string) => ({ draft: 'Taslak', pending: 'Bekliyor', approved: 'Onaylandı', ordered: 'Sipariş Verildi', delivered: 'Teslim Edildi', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ draft: 'bg-gray-100 text-gray-700', pending: 'bg-yellow-100 text-yellow-800', approved: 'bg-blue-100 text-blue-800', ordered: 'bg-purple-100 text-purple-800', delivered: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100')
const calculateTotal = () => form.value.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0)

const updateStats = () => {
  stats.value.total = orders.value.length
  stats.value.pending = orders.value.filter((o: any) => o.status === 'pending').length
  stats.value.ordered = orders.value.filter((o: any) => o.status === 'ordered').length
  stats.value.delivered = orders.value.filter((o: any) => o.status === 'delivered').length
  stats.value.totalAmount = orders.value.reduce((sum: number, o: any) => sum + (parseFloat(o.total_amount) || 0), 0)
}

const openCreateModal = () => { form.value = { supplier_id: '', order_date: new Date().toISOString().split('T')[0], notes: '', items: [{ product_id: '', quantity: 1, unit_price: 0 }] }; isEdit.value = false; editingId.value = null; showModal.value = true }
const viewOrder = (order: any) => { alert('Detay modalı geliştirme aşamasında') }
const closeModal = () => { showModal.value = false }

const addItem = () => { form.value.items.push({ product_id: '', quantity: 1, unit_price: 0 }) }
const removeItem = (index: number) => { if (form.value.items.length > 1) form.value.items.splice(index, 1) }

const handleSubmit = async () => {
  loading.value = true
  try {
    const data = { ...form.value, item_count: form.value.items.length, total_amount: calculateTotal(), status: 'pending' }
    if (isEdit.value && editingId.value) { await orderStore.update(editingId.value, data) }
    else { await orderStore.create(data) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const approveOrder = async (order: any) => { if (!confirm('Bu siparişi onaylamak istiyor musunuz?')) return; await orderStore.update(order.id, { status: 'ordered' }); await loadData() }
const markDelivered = async (order: any) => { if (!confirm('Bu sipariş teslim alındı mı?')) return; await orderStore.update(order.id, { status: 'delivered' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu siparişi iptal etmek istiyor musunuz?')) return; try { await orderStore.update(id, { status: 'cancelled' }); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await Promise.all([orderStore.fetchAll({}), supplierStore.fetchSuppliers(), productStore.fetchProducts()]); updateStats() } finally { loading.value = false } }

const exportOrders = () => {
  const csvContent = [
    ['Sipariş No', 'Tedarikçi', 'Tarih', 'Ürün Sayısı', 'Tutar', 'Durum'].join(','),
    ...filteredOrders.value.map(o => [o.order_number || o.id?.slice(0, 8), getSupplierName(o.supplier_id), o.order_date, o.item_count || 0, o.total_amount || 0, getStatusLabel(o.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `satin_alma_siparisleri_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>