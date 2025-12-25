<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Tedarikçiler</h1>
        <p class="mt-2 text-sm text-gray-600">Tedarikçi bilgilerini ve sipariş geçmişini yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportSuppliers"
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
          Yeni Tedarikçi
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100">
            <BuildingStorefrontIcon class="h-6 w-6 text-slate-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Tedarikçi</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aktif</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <ShoppingCartIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Sipariş</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.orders }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <BanknotesIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Harcama</p>
            <p class="text-2xl font-bold text-purple-600">{{ formatCurrency(stats.spent) }}</p>
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
              placeholder="Tedarikçi ara..."
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

          <!-- Görünüm -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              @click="viewMode = 'grid'"
              :class="['px-3 py-2 transition-colors', viewMode === 'grid' ? 'bg-slate-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']"
            >
              <Squares2X2Icon class="h-4 w-4" />
            </button>
            <button
              @click="viewMode = 'table'"
              :class="['px-3 py-2 transition-colors', viewMode === 'table' ? 'bg-slate-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']"
            >
              <ListBulletIcon class="h-4 w-4" />
            </button>
          </div>
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

    <!-- Grid Görünümü -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="supplier in filteredSuppliers" :key="supplier.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center">
                <span class="text-lg font-bold text-white">{{ getInitials(supplier.name) }}</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">{{ supplier.name }}</h3>
                <p class="text-sm text-gray-500">{{ supplier.contact_person || '-' }}</p>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', supplier.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ supplier.status === 'active' ? 'Aktif' : 'Pasif' }}
            </span>
          </div>

          <div class="space-y-2 text-sm mb-4">
            <div class="flex items-center gap-2 text-gray-600">
              <PhoneIcon class="w-4 h-4" />
              <span>{{ supplier.phone || '-' }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
              <EnvelopeIcon class="w-4 h-4" />
              <span>{{ supplier.email || '-' }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600">
              <MapPinIcon class="w-4 h-4" />
              <span class="truncate">{{ supplier.address || '-' }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-4 bg-gray-50 rounded-lg p-3">
            <div class="text-center">
              <p class="text-lg font-bold text-slate-700">{{ supplier.order_count || 0 }}</p>
              <p class="text-xs text-gray-500">Sipariş</p>
            </div>
            <div class="text-center">
              <p class="text-lg font-bold text-green-600">{{ formatCurrency(supplier.total_amount || 0) }}</p>
              <p class="text-xs text-gray-500">Toplam</p>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t">
            <button @click="openEditModal(supplier)" class="p-1.5 text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
              <PencilIcon class="h-4 w-4" />
            </button>
            <button @click="toggleStatus(supplier)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
              <ArrowPathIcon class="h-4 w-4" />
            </button>
            <button @click="handleDelete(supplier.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
              <TrashIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredSuppliers.length === 0" class="col-span-full text-center py-12">
        <BuildingStorefrontIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Tedarikçi bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-slate-600 hover:text-slate-700 font-medium">
          Tedarikçi ekleyin
        </button>
      </div>
    </div>

    <!-- Tablo Görünümü -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tedarikçi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İletişim</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sipariş</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="supplier in filteredSuppliers" :key="supplier.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center">
                  <span class="text-sm font-bold text-white">{{ getInitials(supplier.name) }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ supplier.name }}</p>
                  <p class="text-xs text-gray-500">{{ supplier.contact_person || '-' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-600">{{ supplier.phone || '-' }}</p>
              <p class="text-xs text-gray-500">{{ supplier.email || '-' }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ supplier.order_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
              {{ formatCurrency(supplier.total_amount || 0) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', supplier.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
                {{ supplier.status === 'active' ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openEditModal(supplier)" class="p-1.5 text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button @click="handleDelete(supplier.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Tedarikçi Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Tedarikçi Düzenle' : 'Yeni Tedarikçi' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Firma Adı *</label>
              <input v-model="form.name" type="text" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Yetkili Kişi</label>
              <input v-model="form.contact_person" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
              <input v-model="form.phone" type="tel" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
            <textarea v-model="form.address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Vergi No</label>
              <input v-model="form.tax_number" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Vadesi (Gün)</label>
              <input v-model.number="form.payment_terms" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" />
            </div>
          </div>

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
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  BuildingStorefrontIcon,
  CheckCircleIcon,
  ShoppingCartIcon,
  BanknotesIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  PhoneIcon,
  EnvelopeIcon,
  MapPinIcon,
  Squares2X2Icon,
  ListBulletIcon
} from '@heroicons/vue/24/outline'
import { useSupplierStore } from '@/stores/supplier'

const store = useSupplierStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')
const viewMode = ref<'grid' | 'table'>('grid')

const filters = ref({ status: '' })

const stats = ref({ total: 0, active: 0, orders: 0, spent: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-slate-600 text-white' },
  { value: 'active', label: 'Aktif', activeClass: 'bg-green-600 text-white' },
  { value: 'inactive', label: 'Pasif', activeClass: 'bg-gray-600 text-white' }
]

const form = ref({
  name: '', contact_person: '', phone: '', email: '', address: '', tax_number: '', payment_terms: 30, notes: ''
})

const suppliers = computed(() => store.suppliers || [])

const filteredSuppliers = computed(() => {
  let result = suppliers.value as any[]
  if (search.value) result = result.filter(s => s.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(s => s.status === filters.value.status)
  return result
})

const getInitials = (name: string) => name?.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() || '??'
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)

const updateStats = () => {
  stats.value.total = suppliers.value.length
  stats.value.active = suppliers.value.filter((s: any) => s.status === 'active').length
  stats.value.orders = suppliers.value.reduce((sum: number, s: any) => sum + (s.order_count || 0), 0)
  stats.value.spent = suppliers.value.reduce((sum: number, s: any) => sum + (parseFloat(s.total_amount) || 0), 0)
}

const openCreateModal = () => { form.value = { name: '', contact_person: '', phone: '', email: '', address: '', tax_number: '', payment_terms: 30, notes: '' }; isEdit.value = false; editingId.value = null; showModal.value = true }
const openEditModal = (supplier: any) => { form.value = { ...supplier }; isEdit.value = true; editingId.value = supplier.id; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await store.update(editingId.value, form.value) }
    else { await store.create({ ...form.value, status: 'active' }) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const toggleStatus = async (supplier: any) => { await store.update(supplier.id, { status: supplier.status === 'active' ? 'inactive' : 'active' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu tedarikçiyi silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await store.fetchAll({}); updateStats() } finally { loading.value = false } }

const exportSuppliers = () => {
  const csvContent = [
    ['Ad', 'Yetkili', 'Telefon', 'E-posta', 'Adres', 'Sipariş', 'Toplam', 'Durum'].join(','),
    ...filteredSuppliers.value.map(s => [s.name, s.contact_person || '', s.phone || '', s.email || '', s.address || '', s.order_count || 0, s.total_amount || 0, s.status].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `tedarikciler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>