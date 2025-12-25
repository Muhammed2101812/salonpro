<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Pazarlama Kampanyaları</h1>
        <p class="mt-2 text-sm text-gray-600">Kampanya yönetimi ve performans takibi</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportCampaigns"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Kampanya
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <MegaphoneIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <PlayIcon class="h-6 w-6 text-green-600" />
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
            <CurrencyDollarIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Bütçe</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.budget) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <ChartBarIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Harcanan</p>
            <p class="text-2xl font-bold text-orange-600">{{ formatCurrency(stats.spent) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100">
            <UserGroupIcon class="h-6 w-6 text-teal-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ulaşılan</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.reached) }}</p>
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
              placeholder="Kampanya ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
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

          <select v-model="filters.channel" class="rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
            <option value="">Tüm Kanallar</option>
            <option v-for="ch in channels" :key="ch.value" :value="ch.value">{{ ch.label }}</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Kampanya Kartları -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="campaign in filteredCampaigns" :key="campaign.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getChannelBg(campaign.channel)]"></div>
        <div class="p-5">
          <!-- Üst Kısım -->
          <div class="flex justify-between items-start mb-3">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ campaign.name }}</h3>
              <div class="flex items-center gap-2 mt-1">
                <component :is="getChannelIcon(campaign.channel)" class="h-4 w-4 text-gray-400" />
                <span class="text-sm text-gray-500">{{ getChannelLabel(campaign.channel) }}</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-semibold', getStatusBadge(campaign.status)]">
              {{ getStatusLabel(campaign.status) }}
            </span>
          </div>

          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ campaign.description || 'Açıklama yok' }}</p>

          <!-- Bütçe Progress -->
          <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Bütçe Kullanımı</span>
              <span class="font-medium">{{ getBudgetPercent(campaign) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                :class="['h-2 rounded-full transition-all', getBudgetPercent(campaign) > 80 ? 'bg-red-500' : 'bg-purple-500']"
                :style="{ width: Math.min(100, getBudgetPercent(campaign)) + '%' }"
              ></div>
            </div>
          </div>

          <!-- İstatistikler -->
          <div class="grid grid-cols-3 gap-2 mb-4 text-center">
            <div class="bg-gray-50 rounded-lg p-2">
              <p class="text-lg font-bold text-gray-900">{{ campaign.sent_count || 0 }}</p>
              <p class="text-xs text-gray-500">Gönderilen</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-2">
              <p class="text-lg font-bold text-green-600">{{ campaign.delivered_count || 0 }}</p>
              <p class="text-xs text-gray-500">Teslim</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-2">
              <p class="text-lg font-bold text-blue-600">{{ campaign.click_count || 0 }}</p>
              <p class="text-xs text-gray-500">Tıklama</p>
            </div>
          </div>

          <!-- Tarihler -->
          <div class="flex justify-between text-xs text-gray-500 mb-4">
            <span>{{ formatDate(campaign.start_date) }}</span>
            <span>→</span>
            <span>{{ formatDate(campaign.end_date) }}</span>
          </div>

          <!-- Aksiyonlar -->
          <div class="flex justify-between items-center pt-4 border-t">
            <button
              v-if="campaign.status === 'draft' || campaign.status === 'paused'"
              @click="startCampaign(campaign)"
              class="text-sm font-medium text-green-600 hover:text-green-700"
            >
              <PlayIcon class="h-4 w-4 inline mr-1" /> Başlat
            </button>
            <button
              v-else-if="campaign.status === 'active'"
              @click="pauseCampaign(campaign)"
              class="text-sm font-medium text-orange-600 hover:text-orange-700"
            >
              <PauseIcon class="h-4 w-4 inline mr-1" /> Duraklat
            </button>
            <span v-else></span>

            <div class="flex gap-2">
              <button @click="openEditModal(campaign)" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                <PencilIcon class="h-4 w-4" />
              </button>
              <button @click="handleDelete(campaign.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredCampaigns.length === 0" class="col-span-full text-center py-12">
        <MegaphoneIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Kampanya bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-purple-600 hover:text-purple-700 font-medium">
          Kampanya oluşturun
        </button>
      </div>
    </div>

    <!-- Kampanya Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Kampanya Düzenle' : 'Yeni Kampanya' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Adı *</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
          </div>

          <!-- Kanal Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kanal *</label>
            <div class="grid grid-cols-4 gap-2">
              <button
                v-for="ch in channels"
                :key="ch.value"
                type="button"
                @click="form.channel = ch.value"
                :class="[
                  'p-3 rounded-lg border text-center transition-colors',
                  form.channel === ch.value ? ch.activeClass : 'border-gray-200 hover:border-purple-300'
                ]"
              >
                <component :is="ch.icon" class="h-5 w-5 mx-auto mb-1" />
                <span class="text-xs font-medium">{{ ch.label }}</span>
              </button>
            </div>
          </div>

          <!-- Bütçe ve Tarihler -->
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bütçe (₺)</label>
              <input v-model.number="form.budget" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç *</label>
              <input v-model="form.start_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş *</label>
              <input v-model="form.end_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
            </div>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
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
  MegaphoneIcon,
  PlayIcon,
  PauseIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  UserGroupIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  EnvelopeIcon,
  ChatBubbleLeftIcon,
  DevicePhoneMobileIcon,
  BellIcon
} from '@heroicons/vue/24/outline'
import { useMarketingCampaignStore } from '@/stores/marketingcampaign'

const store = useMarketingCampaignStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ status: '', channel: '' })

const stats = ref({ total: 0, active: 0, budget: 0, spent: 0, reached: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-purple-600 text-white' },
  { value: 'active', label: 'Aktif', activeClass: 'bg-green-600 text-white' },
  { value: 'draft', label: 'Taslak', activeClass: 'bg-gray-600 text-white' },
  { value: 'completed', label: 'Bitti', activeClass: 'bg-blue-600 text-white' }
]

const channels = [
  { value: 'sms', label: 'SMS', icon: markRaw(DevicePhoneMobileIcon), activeClass: 'bg-blue-50 text-blue-700 border-blue-500' },
  { value: 'email', label: 'E-posta', icon: markRaw(EnvelopeIcon), activeClass: 'bg-green-50 text-green-700 border-green-500' },
  { value: 'push', label: 'Push', icon: markRaw(BellIcon), activeClass: 'bg-orange-50 text-orange-700 border-orange-500' },
  { value: 'whatsapp', label: 'WhatsApp', icon: markRaw(ChatBubbleLeftIcon), activeClass: 'bg-emerald-50 text-emerald-700 border-emerald-500' }
]

const form = ref({
  name: '', channel: 'sms', budget: 0, start_date: new Date().toISOString().split('T')[0], end_date: '', description: ''
})

const campaigns = computed(() => store.campaigns || [])

const filteredCampaigns = computed(() => {
  let result = campaigns.value as any[]
  if (search.value) result = result.filter(c => c.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(c => c.status === filters.value.status)
  if (filters.value.channel) result = result.filter(c => c.channel === filters.value.channel)
  return result
})

const formatNumber = (n: number) => new Intl.NumberFormat('tr-TR').format(n || 0)
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit' }).format(new Date(d)) : '-'
const getChannelLabel = (ch: string) => channels.find(c => c.value === ch)?.label || ch
const getChannelIcon = (ch: string) => channels.find(c => c.value === ch)?.icon || MegaphoneIcon
const getChannelBg = (ch: string) => ({ sms: 'bg-blue-500', email: 'bg-green-500', push: 'bg-orange-500', whatsapp: 'bg-emerald-500' }[ch] || 'bg-gray-300')
const getStatusLabel = (status: string) => ({ draft: 'Taslak', scheduled: 'Planlandı', active: 'Aktif', paused: 'Duraklatıldı', completed: 'Tamamlandı', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ draft: 'bg-gray-100 text-gray-700', scheduled: 'bg-blue-100 text-blue-700', active: 'bg-green-100 text-green-800', paused: 'bg-yellow-100 text-yellow-800', completed: 'bg-purple-100 text-purple-800', cancelled: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100')
const getBudgetPercent = (campaign: any) => campaign.budget > 0 ? Math.round(((campaign.spent || 0) / campaign.budget) * 100) : 0

const updateStats = () => {
  stats.value.total = campaigns.value.length
  stats.value.active = campaigns.value.filter((c: any) => c.status === 'active').length
  stats.value.budget = campaigns.value.reduce((sum: number, c: any) => sum + (parseFloat(c.budget) || 0), 0)
  stats.value.spent = campaigns.value.reduce((sum: number, c: any) => sum + (parseFloat(c.spent) || 0), 0)
  stats.value.reached = campaigns.value.reduce((sum: number, c: any) => sum + (c.delivered_count || 0), 0)
}

const openCreateModal = () => { form.value = { name: '', channel: 'sms', budget: 0, start_date: new Date().toISOString().split('T')[0], end_date: '', description: '' }; isEdit.value = false; editingId.value = null; showModal.value = true }
const openEditModal = (campaign: any) => { form.value = { ...campaign }; isEdit.value = true; editingId.value = campaign.id; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await store.update(editingId.value, form.value) }
    else { await store.create({ ...form.value, status: 'draft' }) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const startCampaign = async (campaign: any) => { await store.update(campaign.id, { status: 'active' }); await loadData() }
const pauseCampaign = async (campaign: any) => { await store.update(campaign.id, { status: 'paused' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu kampanyayı silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await store.fetchAll({}); updateStats() } finally { loading.value = false } }

const exportCampaigns = () => {
  const csvContent = [
    ['Ad', 'Kanal', 'Bütçe', 'Harcanan', 'Gönderilen', 'Teslim', 'Durum'].join(','),
    ...filteredCampaigns.value.map(c => [c.name, getChannelLabel(c.channel), c.budget || 0, c.spent || 0, c.sent_count || 0, c.delivered_count || 0, getStatusLabel(c.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `kampanyalar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>