<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bekleme Listesi</h1>
        <p class="mt-2 text-sm text-gray-600">Randevu bekleyen müşterileri yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportWaitlist"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Listeye Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100">
            <QueueListIcon class="h-6 w-6 text-amber-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Bekleyen</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <ExclamationCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Yüksek Öncelik</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.highPriority }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Randevuya Dönüşen</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.converted }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <ClockIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ort. Bekleme</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.avgWaitDays }} gün</p>
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
              placeholder="Müşteri ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="priority in priorityFilters"
              :key="priority.value"
              @click="filters.priority = filters.priority === priority.value ? '' : priority.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.priority === priority.value ? priority.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ priority.label }}
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
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Bekleme Listesi Kartları -->
    <div v-else class="space-y-3">
      <div v-for="(item, index) in filteredWaitlist" :key="item.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-4">
            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-amber-100 text-amber-700 font-bold">
              {{ index + 1 }}
            </div>
            <div>
              <div class="flex items-center gap-2">
                <p class="font-medium text-gray-900">{{ item.customer?.name || item.customer_name || 'Bilinmiyor' }}</p>
                <span :class="['px-2 py-0.5 text-xs rounded-full font-medium', getPriorityBadge(item.priority)]">
                  {{ getPriorityLabel(item.priority) }}
                </span>
              </div>
              <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                  <SparklesIcon class="h-4 w-4" />
                  {{ item.service?.name || item.requested_service || 'Herhangi' }}
                </span>
                <span class="flex items-center gap-1">
                  <UserIcon class="h-4 w-4" />
                  {{ item.employee?.first_name || item.preferred_employee || 'Herhangi' }}
                </span>
              </div>
            </div>
          </div>
          <div class="text-right">
            <p class="text-xs text-gray-500">Eklendi</p>
            <p class="text-sm font-medium text-gray-900">{{ formatDate(item.created_at) }}</p>
            <p class="text-xs text-gray-400">{{ getDaysWaiting(item.created_at) }} gün bekliyor</p>
          </div>
        </div>

        <div v-if="item.notes" class="mt-3 p-2 bg-gray-50 rounded-lg text-sm text-gray-600">
          {{ item.notes }}
        </div>

        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
          <div class="flex items-center gap-2 text-sm">
            <PhoneIcon class="h-4 w-4 text-gray-400" />
            <span class="text-gray-600">{{ item.customer?.phone || item.contact_phone || '-' }}</span>
          </div>
          <div class="flex gap-2">
            <button
              @click="createAppointment(item)"
              class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors"
            >
              <CalendarDaysIcon class="h-4 w-4 mr-1" />
              Randevu Oluştur
            </button>
            <button
              @click="contactCustomer(item)"
              class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              title="İletişime Geç"
            >
              <PhoneIcon class="h-4 w-4" />
            </button>
            <button
              @click="handleDelete(item.id)"
              class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              title="Sil"
            >
              <TrashIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredWaitlist.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <QueueListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Bekleme listesinde kimse yok</p>
        <button @click="openCreateModal" class="mt-4 text-amber-600 hover:text-amber-700 font-medium">
          Listeye ekleyin
        </button>
      </div>
    </div>

    <!-- Ekleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">Bekleme Listesine Ekle</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
            <select v-model="form.customer_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
              <option value="">Müşteri Seçin</option>
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">İstenen Hizmet</label>
            <select v-model="form.service_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
              <option value="">Herhangi</option>
              <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik *</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="p in priorities"
                :key="p.value"
                type="button"
                @click="form.priority = p.value"
                :class="['p-3 rounded-lg border text-center transition-colors', form.priority === p.value ? p.activeClass : 'border-gray-200 hover:border-amber-300']"
              >
                <span class="text-sm font-medium">{{ p.label }}</span>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Ekleniyor...' : 'Ekle' }}
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
  QueueListIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
  ClockIcon,
  MagnifyingGlassIcon,
  SparklesIcon,
  UserIcon,
  PhoneIcon,
  CalendarDaysIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentWaitlistStore } from '@/stores/appointmentwaitlist'
import { useCustomerStore } from '@/stores/customer'
import { useServiceStore } from '@/stores/service'

const store = useAppointmentWaitlistStore()
const customerStore = useCustomerStore()
const serviceStore = useServiceStore()

const loading = ref(false)
const showModal = ref(false)
const search = ref('')

const filters = ref({ priority: '' })

const stats = ref({ total: 0, highPriority: 0, converted: 0, avgWaitDays: 0 })

const priorityFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-amber-600 text-white' },
  { value: 'high', label: 'Yüksek', activeClass: 'bg-red-600 text-white' },
  { value: 'normal', label: 'Normal', activeClass: 'bg-blue-600 text-white' },
  { value: 'low', label: 'Düşük', activeClass: 'bg-gray-600 text-white' }
]

const priorities = [
  { value: 'high', label: 'Yüksek', activeClass: 'bg-red-50 text-red-700 border-red-500' },
  { value: 'normal', label: 'Normal', activeClass: 'bg-blue-50 text-blue-700 border-blue-500' },
  { value: 'low', label: 'Düşük', activeClass: 'bg-gray-100 text-gray-700 border-gray-500' }
]

const form = ref({ customer_id: '', service_id: '', priority: 'normal', notes: '' })

const waitlist = ref<any[]>([])
const customers = computed(() => customerStore.customers || [])
const services = computed(() => serviceStore.services || [])

const filteredWaitlist = computed(() => {
  let result = waitlist.value.filter(w => w.status !== 'converted')
  if (search.value) result = result.filter(w => w.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.priority) result = result.filter(w => w.priority === filters.value.priority)
  return result.sort((a, b) => {
    const priorityOrder = { high: 0, normal: 1, low: 2 }
    return (priorityOrder[a.priority as keyof typeof priorityOrder] || 1) - (priorityOrder[b.priority as keyof typeof priorityOrder] || 1)
  })
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const getDaysWaiting = (d: string) => d ? Math.floor((Date.now() - new Date(d).getTime()) / (1000 * 60 * 60 * 24)) : 0
const getPriorityLabel = (p: string) => ({ high: 'Yüksek', normal: 'Normal', low: 'Düşük' }[p] || p)
const getPriorityBadge = (p: string) => ({ high: 'bg-red-100 text-red-800', normal: 'bg-blue-100 text-blue-800', low: 'bg-gray-100 text-gray-600' }[p] || 'bg-gray-100')

const updateStats = () => {
  stats.value.total = waitlist.value.filter(w => w.status !== 'converted').length
  stats.value.highPriority = waitlist.value.filter(w => w.priority === 'high' && w.status !== 'converted').length
  stats.value.converted = waitlist.value.filter(w => w.status === 'converted').length
  const totalDays = waitlist.value.reduce((sum, w) => sum + getDaysWaiting(w.created_at), 0)
  stats.value.avgWaitDays = waitlist.value.length > 0 ? Math.round(totalDays / waitlist.value.length) : 0
}

const openCreateModal = () => { form.value = { customer_id: '', service_id: '', priority: 'normal', notes: '' }; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    await store.create({ ...form.value, status: 'waiting' })
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Eklenemedi') }
  finally { loading.value = false }
}

const createAppointment = (item: any) => { alert('Randevu oluşturma sayfasına yönlendiriliyor...'); window.location.href = `/appointments?customer_id=${item.customer_id}` }
const contactCustomer = (item: any) => { alert(`${item.customer?.phone || item.contact_phone} numaralı müşteri aranacak`) }
const handleDelete = async (id: string) => { if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    waitlist.value = response?.data || []
    await Promise.all([customerStore.fetchCustomers(), serviceStore.fetchServices()])
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportWaitlist = () => {
  const csvContent = [
    ['Müşteri', 'Hizmet', 'Öncelik', 'Eklendi', 'Bekleme Gün'].join(','),
    ...filteredWaitlist.value.map(w => [w.customer?.name || '', w.service?.name || 'Herhangi', getPriorityLabel(w.priority), w.created_at, getDaysWaiting(w.created_at)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `bekleme_listesi_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>