<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stok Transferleri</h1>
        <p class="mt-2 text-sm text-gray-600">Şubeler arası stok hareketlerini yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportTransfers"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-slate-600 hover:bg-slate-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Transfer
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100">
            <TruckIcon class="h-6 w-6 text-slate-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Transfer</p>
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
            <ArrowPathIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Yolda</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.inTransit }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Tamamlanan</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.completed }}</p>
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
              placeholder="Transfer ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm"
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

          <select v-model="filters.branchId" class="rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm">
            <option value="">Tüm Şubeler</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Tablo -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kaynak → Hedef</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Sayısı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="transfer in filteredTransfers" :key="transfer.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="font-mono font-medium text-slate-600 bg-slate-50 px-2 py-1 rounded">
                {{ transfer.transfer_number || transfer.id?.slice(0, 8) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="flex items-center gap-1">
                  <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                  <span class="text-sm text-gray-900">{{ getBranchName(transfer.source_branch_id) }}</span>
                </div>
                <ArrowRightIcon class="h-4 w-4 text-gray-400" />
                <div class="flex items-center gap-1">
                  <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                  <span class="text-sm text-gray-900">{{ getBranchName(transfer.destination_branch_id) }}</span>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="bg-gray-100 px-3 py-1 rounded-full text-sm font-medium">
                {{ transfer.item_count || 0 }} ürün
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDate(transfer.transfer_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-3 py-1 text-xs rounded-full font-semibold', getStatusBadge(transfer.status)]">
                {{ getStatusLabel(transfer.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewTransfer(transfer)"
                  class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Detay"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="transfer.status === 'pending'"
                  @click="approveTransfer(transfer)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Onayla"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="transfer.status === 'in_transit'"
                  @click="completeTransfer(transfer)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Tamamla"
                >
                  <TruckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="transfer.status === 'pending'"
                  @click="handleDelete(transfer.id)"
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

      <div v-if="filteredTransfers.length === 0" class="p-12 text-center">
        <TruckIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Transfer bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-slate-600 hover:text-slate-700 font-medium">
          Transfer oluşturun
        </button>
      </div>
    </div>

    <!-- Transfer Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Transfer Düzenle' : 'Yeni Transfer' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Şube Seçimleri -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kaynak Şube *</label>
              <select v-model="form.source_branch_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Şube Seçin</option>
                <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hedef Şube *</label>
              <select v-model="form.destination_branch_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Şube Seçin</option>
                <option v-for="branch in branches" :key="branch.id" :value="branch.id" :disabled="branch.id === form.source_branch_id">
                  {{ branch.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Transfer Tarihi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Tarihi *</label>
            <input v-model="form.transfer_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
          </div>

          <!-- Ürünler -->
          <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-900 mb-3">Transfer Edilecek Ürünler</label>
            <div class="space-y-2">
              <div v-for="(item, index) in form.items" :key="index" class="flex gap-2 items-center bg-white p-2 rounded-lg">
                <select v-model="item.product_id" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                  <option value="">Ürün Seçin</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                </select>
                <input v-model.number="item.quantity" type="number" min="1" placeholder="Adet" class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <button type="button" @click="removeItem(index)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
            <button type="button" @click="addItem" class="mt-3 text-slate-600 hover:text-slate-700 text-sm font-medium inline-flex items-center">
              <PlusIcon class="h-4 w-4 mr-1" /> Ürün Ekle
            </button>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Transfer Detay Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Transfer Detayı</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>
        <div v-if="selectedTransfer" class="p-6 space-y-4">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Transfer No</p>
              <p class="font-mono font-bold text-slate-600">{{ selectedTransfer.transfer_number }}</p>
            </div>
            <span :class="['px-3 py-1 text-xs rounded-full font-semibold', getStatusBadge(selectedTransfer.status)]">
              {{ getStatusLabel(selectedTransfer.status) }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Kaynak</p>
              <p class="font-medium">{{ getBranchName(selectedTransfer.source_branch_id) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Hedef</p>
              <p class="font-medium">{{ getBranchName(selectedTransfer.destination_branch_id) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tarih</p>
              <p class="font-medium">{{ formatDate(selectedTransfer.transfer_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Ürün Sayısı</p>
              <p class="font-medium">{{ selectedTransfer.item_count || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  TruckIcon,
  ClockIcon,
  CheckCircleIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  CheckIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline'
import { useStockTransferStore } from '@/stores/stocktransfer'
import { useBranchStore } from '@/stores/branch'
import { useProductStore } from '@/stores/product'

const transferStore = useStockTransferStore()
const branchStore = useBranchStore()
const productStore = useProductStore()

const loading = ref(false)
const showModal = ref(false)
const showDetailModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')
const selectedTransfer = ref<any>(null)

const filters = ref({ status: '', branchId: '' })

const stats = ref({ total: 0, pending: 0, inTransit: 0, completed: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-slate-600 text-white' },
  { value: 'pending', label: 'Bekleyen', activeClass: 'bg-yellow-600 text-white' },
  { value: 'in_transit', label: 'Yolda', activeClass: 'bg-blue-600 text-white' },
  { value: 'completed', label: 'Tamamlandı', activeClass: 'bg-green-600 text-white' }
]

const form = ref({
  source_branch_id: '', destination_branch_id: '', transfer_date: new Date().toISOString().split('T')[0], notes: '',
  items: [{ product_id: '', quantity: 1 }]
})

const transfers = computed(() => transferStore.transfers || [])
const branches = computed(() => branchStore.branches || [])
const products = computed(() => productStore.products || [])

const filteredTransfers = computed(() => {
  let result = transfers.value as any[]
  if (search.value) result = result.filter(t => t.transfer_number?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(t => t.status === filters.value.status)
  if (filters.value.branchId) result = result.filter(t => t.source_branch_id === filters.value.branchId || t.destination_branch_id === filters.value.branchId)
  return result.sort((a, b) => new Date(b.transfer_date).getTime() - new Date(a.transfer_date).getTime())
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d)) : '-'
const getBranchName = (id: string) => branches.value.find((b: any) => b.id === id)?.name || '-'
const getStatusLabel = (status: string) => ({ pending: 'Bekliyor', approved: 'Onaylandı', in_transit: 'Yolda', completed: 'Tamamlandı', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ pending: 'bg-yellow-100 text-yellow-800', approved: 'bg-blue-100 text-blue-800', in_transit: 'bg-purple-100 text-purple-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-gray-100 text-gray-600' }[status] || 'bg-gray-100')

const updateStats = () => {
  stats.value.total = transfers.value.length
  stats.value.pending = transfers.value.filter((t: any) => t.status === 'pending').length
  stats.value.inTransit = transfers.value.filter((t: any) => t.status === 'in_transit').length
  stats.value.completed = transfers.value.filter((t: any) => t.status === 'completed').length
}

const openCreateModal = () => { form.value = { source_branch_id: '', destination_branch_id: '', transfer_date: new Date().toISOString().split('T')[0], notes: '', items: [{ product_id: '', quantity: 1 }] }; isEdit.value = false; editingId.value = null; showModal.value = true }
const viewTransfer = (transfer: any) => { selectedTransfer.value = transfer; showDetailModal.value = true }
const closeModal = () => { showModal.value = false }

const addItem = () => { form.value.items.push({ product_id: '', quantity: 1 }) }
const removeItem = (index: number) => { if (form.value.items.length > 1) form.value.items.splice(index, 1) }

const handleSubmit = async () => {
  loading.value = true
  try {
    const data = { ...form.value, item_count: form.value.items.length, status: 'pending' }
    if (isEdit.value && editingId.value) { await transferStore.update(editingId.value, data) }
    else { await transferStore.create(data) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const approveTransfer = async (transfer: any) => { if (!confirm('Bu transferi onaylamak istiyor musunuz?')) return; await transferStore.update(transfer.id, { status: 'in_transit' }); await loadData() }
const completeTransfer = async (transfer: any) => { if (!confirm('Bu transferi tamamlamak istiyor musunuz?')) return; await transferStore.update(transfer.id, { status: 'completed' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu transferi iptal etmek istiyor musunuz?')) return; try { await transferStore.update(id, { status: 'cancelled' }); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await Promise.all([transferStore.fetchAll({}), branchStore.fetchBranches(), productStore.fetchProducts()]); updateStats() } finally { loading.value = false } }

const exportTransfers = () => {
  const csvContent = [
    ['Transfer No', 'Kaynak', 'Hedef', 'Ürün Sayısı', 'Tarih', 'Durum'].join(','),
    ...filteredTransfers.value.map(t => [t.transfer_number || t.id?.slice(0, 8), getBranchName(t.source_branch_id), getBranchName(t.destination_branch_id), t.item_count || 0, t.transfer_date, getStatusLabel(t.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `stok_transferleri_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>