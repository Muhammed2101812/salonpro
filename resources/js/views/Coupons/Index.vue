<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Kuponlar</h1>
        <p class="mt-2 text-sm text-gray-600">İndirim kuponları oluşturun ve yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportCoupons"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-fuchsia-600 hover:bg-fuchsia-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Kupon
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-fuchsia-100">
            <TicketIcon class="h-6 w-6 text-fuchsia-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Kupon</p>
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
            <ClipboardDocumentCheckIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Kullanım</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.usage }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <CurrencyDollarIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam İndirim</p>
            <p class="text-2xl font-bold text-orange-600">{{ formatCurrency(stats.discount) }}</p>
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
              placeholder="Kupon kodu ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 text-sm"
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

          <select v-model="filters.type" class="rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 text-sm">
            <option value="">Tüm Tipler</option>
            <option value="percentage">Yüzde</option>
            <option value="fixed">Sabit Tutar</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-fuchsia-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Kupon Kartları -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="coupon in filteredCoupons" :key="coupon.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-5">
          <!-- Üst Kısım -->
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-2">
              <span class="font-mono font-bold text-lg text-fuchsia-600 bg-fuchsia-50 px-3 py-1 rounded-lg">{{ coupon.code }}</span>
              <button @click="copyCode(coupon.code)" class="p-1 text-gray-400 hover:text-fuchsia-600 transition-colors">
                <ClipboardIcon class="h-4 w-4" />
              </button>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-semibold', getStatusBadge(coupon.status)]">
              {{ getStatusLabel(coupon.status) }}
            </span>
          </div>

          <!-- İndirim Bilgisi -->
          <div class="text-center my-6">
            <span :class="['text-4xl font-bold', coupon.discount_type === 'percentage' ? 'text-green-600' : 'text-blue-600']">
              {{ coupon.discount_type === 'percentage' ? `%${coupon.discount_value}` : formatCurrency(coupon.discount_value) }}
            </span>
            <p v-if="coupon.max_discount" class="text-sm text-gray-500 mt-1">
              Max: {{ formatCurrency(coupon.max_discount) }}
            </p>
          </div>

          <!-- Kullanım İlerlemesi -->
          <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Kullanım</span>
              <span class="font-medium">{{ coupon.usage_count || 0 }} / {{ coupon.usage_limit || '∞' }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-fuchsia-500 h-2 rounded-full transition-all" :style="{ width: getUsagePercent(coupon) + '%' }"></div>
            </div>
          </div>

          <!-- Detaylar -->
          <div class="space-y-2 text-sm">
            <div v-if="coupon.min_purchase" class="flex justify-between">
              <span class="text-gray-500">Min. Tutar</span>
              <span class="font-medium">{{ formatCurrency(coupon.min_purchase) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Geçerlilik</span>
              <span class="font-medium">{{ formatDate(coupon.start_date) }} - {{ formatDate(coupon.end_date) }}</span>
            </div>
          </div>

          <!-- Aksiyonlar -->
          <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
            <button
              @click="toggleStatus(coupon)"
              :class="['p-1.5 rounded-lg transition-colors', coupon.status === 'active' ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50']"
              :title="coupon.status === 'active' ? 'Durdur' : 'Aktifleştir'"
            >
              <component :is="coupon.status === 'active' ? PauseIcon : PlayIcon" class="h-4 w-4" />
            </button>
            <button @click="openEditModal(coupon)" class="p-1.5 text-fuchsia-600 hover:bg-fuchsia-50 rounded-lg transition-colors">
              <PencilIcon class="h-4 w-4" />
            </button>
            <button @click="handleDelete(coupon.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
              <TrashIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredCoupons.length === 0" class="col-span-full text-center py-12">
        <TicketIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Kupon bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-fuchsia-600 hover:text-fuchsia-700 font-medium">
          Kupon oluşturun
        </button>
      </div>
    </div>

    <!-- Kupon Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Kupon Düzenle' : 'Yeni Kupon' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Kupon Kodu -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kupon Kodu *</label>
            <div class="flex gap-2">
              <input v-model="form.code" type="text" required class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 font-mono uppercase text-lg" />
              <button type="button" @click="generateCode" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                <ArrowPathIcon class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- İndirim Tipi ve Değeri -->
          <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-900 mb-3">İndirim Ayarları</label>
            <div class="grid grid-cols-2 gap-4 mb-4">
              <button
                type="button"
                @click="form.discount_type = 'percentage'"
                :class="[
                  'flex flex-col items-center p-4 rounded-lg border transition-colors',
                  form.discount_type === 'percentage' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
              >
                <ReceiptPercentIcon class="h-8 w-8 mb-2" />
                <span class="font-medium">Yüzde (%)</span>
              </button>
              <button
                type="button"
                @click="form.discount_type = 'fixed'"
                :class="[
                  'flex flex-col items-center p-4 rounded-lg border transition-colors',
                  form.discount_type === 'fixed' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                ]"
              >
                <CurrencyDollarIcon class="h-8 w-8 mb-2" />
                <span class="font-medium">Sabit Tutar (₺)</span>
              </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">İndirim Değeri *</label>
                <input v-model.number="form.discount_value" type="number" min="0" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500 text-lg font-bold" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max İndirim (isteğe bağlı)</label>
                <input v-model.number="form.max_discount" type="number" min="0" placeholder="Sınırsız" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
              </div>
            </div>
          </div>

          <!-- Kullanım Koşulları -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Min. Sepet Tutarı</label>
              <input v-model.number="form.min_purchase" type="number" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kullanım Limiti</label>
              <input v-model.number="form.usage_limit" type="number" min="0" placeholder="Sınırsız" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
            </div>
          </div>

          <!-- Tarih Aralığı -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi *</label>
              <input v-model="form.start_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi *</label>
              <input v-model="form.end_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
            </div>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <input v-model="form.description" type="text" placeholder="Kupon açıklaması..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-fuchsia-500 focus:ring-fuchsia-500" />
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
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
  TicketIcon,
  CheckCircleIcon,
  ClipboardDocumentCheckIcon,
  CurrencyDollarIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ClipboardIcon,
  PlayIcon,
  PauseIcon,
  ReceiptPercentIcon
} from '@heroicons/vue/24/outline'
import { useCouponStore } from '@/stores/coupon'

const store = useCouponStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ status: '', type: '' })

const stats = ref({ total: 0, active: 0, usage: 0, discount: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-fuchsia-600 text-white' },
  { value: 'active', label: 'Aktif', activeClass: 'bg-green-600 text-white' },
  { value: 'inactive', label: 'Pasif', activeClass: 'bg-gray-600 text-white' },
  { value: 'expired', label: 'Süresi Dolmuş', activeClass: 'bg-red-600 text-white' }
]

const form = ref({
  code: '', description: '', discount_type: 'percentage', discount_value: 10,
  max_discount: null as number | null, min_purchase: null as number | null, usage_limit: null as number | null,
  start_date: new Date().toISOString().split('T')[0], end_date: ''
})

const coupons = computed(() => store.coupons || [])

const filteredCoupons = computed(() => {
  let result = coupons.value as any[]
  if (search.value) result = result.filter(c => c.code?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(c => c.status === filters.value.status)
  if (filters.value.type) result = result.filter(c => c.discount_type === filters.value.type)
  return result
})

const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit' }).format(new Date(d)) : '-'
const getUsagePercent = (coupon: any) => coupon.usage_limit ? Math.min(100, ((coupon.usage_count || 0) / coupon.usage_limit) * 100) : 0
const getStatusLabel = (status: string) => ({ active: 'Aktif', inactive: 'Pasif', expired: 'Süresi Dolmuş', used_up: 'Tükenmiş' }[status] || status)
const getStatusBadge = (status: string) => ({ active: 'bg-green-100 text-green-800', inactive: 'bg-gray-100 text-gray-600', expired: 'bg-red-100 text-red-800', used_up: 'bg-orange-100 text-orange-800' }[status] || 'bg-gray-100')

const generateCode = () => { const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; form.value.code = Array.from({ length: 8 }, () => chars.charAt(Math.floor(Math.random() * chars.length))).join('') }
const copyCode = (code: string) => { navigator.clipboard.writeText(code); alert('Kod kopyalandı: ' + code) }

const updateStats = () => {
  stats.value.total = coupons.value.length
  stats.value.active = coupons.value.filter((c: any) => c.status === 'active').length
  stats.value.usage = coupons.value.reduce((sum: number, c: any) => sum + (c.usage_count || 0), 0)
  stats.value.discount = coupons.value.reduce((sum: number, c: any) => sum + (parseFloat(c.total_discount_given) || 0), 0)
}

const openCreateModal = () => { form.value = { code: '', description: '', discount_type: 'percentage', discount_value: 10, max_discount: null, min_purchase: null, usage_limit: null, start_date: new Date().toISOString().split('T')[0], end_date: '' }; generateCode(); isEdit.value = false; editingId.value = null; showModal.value = true }
const openEditModal = (coupon: any) => { form.value = { ...coupon }; isEdit.value = true; editingId.value = coupon.id; showModal.value = true }
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

const toggleStatus = async (coupon: any) => { await store.update(coupon.id, { status: coupon.status === 'active' ? 'inactive' : 'active' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu kuponu silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await store.fetchCoupons(); updateStats() } finally { loading.value = false } }

const exportCoupons = () => {
  const csvContent = [
    ['Kod', 'Tip', 'Değer', 'Kullanım', 'Limit', 'Başlangıç', 'Bitiş', 'Durum'].join(','),
    ...filteredCoupons.value.map(c => [c.code, c.discount_type, c.discount_value, c.usage_count || 0, c.usage_limit || 'Sınırsız', c.start_date, c.end_date, getStatusLabel(c.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `kuponlar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>