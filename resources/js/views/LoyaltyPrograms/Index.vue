<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Sadakat Programları</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri sadakat programlarını ve puan sistemini yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportPrograms"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Program
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100">
            <SparklesIcon class="h-6 w-6 text-violet-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aktif Programlar</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <UserGroupIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Üye</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.members) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <StarIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Dağıtılan Puan</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.pointsGiven) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <GiftIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Kullanılan Puan</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.pointsUsed) }}</p>
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
              placeholder="Program ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
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
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Program Kartları -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="program in filteredPrograms" :key="program.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getTierBgClass(program.tier)]"></div>
        <div class="p-5">
          <!-- Üst Kısım -->
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ program.name }}</h3>
              <span :class="['px-2 py-0.5 text-xs rounded-full font-medium', getTierBadge(program.tier)]">
                {{ getTierLabel(program.tier) }}
              </span>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-semibold', getStatusBadge(program.status)]">
              {{ getStatusLabel(program.status) }}
            </span>
          </div>

          <!-- Puan Oranı -->
          <div class="bg-violet-50 rounded-lg p-4 mb-4 text-center">
            <p class="text-sm text-violet-700 mb-1">Puan Oranı</p>
            <p class="text-3xl font-bold text-violet-600">{{ program.points_per_amount || 1 }} <span class="text-lg">puan</span></p>
            <p class="text-xs text-violet-500">her {{ formatCurrency(program.amount_for_points || 1) }} harcamada</p>
          </div>

          <!-- Detaylar -->
          <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between">
              <span class="text-gray-500">Üye Sayısı</span>
              <span class="font-medium">{{ program.member_count || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Min. Puan</span>
              <span class="font-medium">{{ program.min_points || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Puan Geçerliliği</span>
              <span class="font-medium">{{ program.expiry_days || '∞' }} gün</span>
            </div>
          </div>

          <!-- Aksiyonlar -->
          <div class="flex justify-between items-center pt-4 border-t">
            <button
              @click="toggleStatus(program)"
              :class="['text-sm font-medium', program.status === 'active' ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700']"
            >
              {{ program.status === 'active' ? 'Durdur' : 'Aktifleştir' }}
            </button>
            <div class="flex gap-2">
              <button @click="openEditModal(program)" class="p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors">
                <PencilIcon class="h-4 w-4" />
              </button>
              <button @click="handleDelete(program.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredPrograms.length === 0" class="col-span-full text-center py-12">
        <SparklesIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Sadakat programı bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-violet-600 hover:text-violet-700 font-medium">
          Program oluşturun
        </button>
      </div>
    </div>

    <!-- Program Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Program Düzenle' : 'Yeni Program' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Program Adı *</label>
            <input v-model="form.name" type="text" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500" />
          </div>

          <!-- Tier Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Seviye *</label>
            <div class="grid grid-cols-4 gap-2">
              <button
                v-for="tier in tiers"
                :key="tier.value"
                type="button"
                @click="form.tier = tier.value"
                :class="[
                  'p-3 rounded-lg border text-center transition-colors',
                  form.tier === tier.value ? tier.activeClass : 'border-gray-200 hover:border-violet-300'
                ]"
              >
                <span class="text-sm font-medium">{{ tier.label }}</span>
              </button>
            </div>
          </div>

          <!-- Puan Ayarları -->
          <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-900 mb-3">Puan Ayarları</label>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Harcama Tutarı (₺)</label>
                <input v-model.number="form.amount_for_points" type="number" min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Kazanılan Puan</label>
                <input v-model.number="form.points_per_amount" type="number" min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Min. Kullanım Puanı</label>
                <input v-model.number="form.min_points" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Geçerlilik (gün)</label>
                <input v-model.number="form.expiry_days" type="number" min="0" placeholder="Sınırsız" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500" />
              </div>
            </div>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
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
  SparklesIcon,
  UserGroupIcon,
  StarIcon,
  GiftIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useLoyaltyProgramStore } from '@/stores/loyaltyprogram'

const store = useLoyaltyProgramStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ status: '' })

const stats = ref({ active: 0, members: 0, pointsGiven: 0, pointsUsed: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-violet-600 text-white' },
  { value: 'active', label: 'Aktif', activeClass: 'bg-green-600 text-white' },
  { value: 'inactive', label: 'Pasif', activeClass: 'bg-gray-600 text-white' }
]

const tiers = [
  { value: 'bronze', label: 'Bronz', activeClass: 'bg-amber-100 text-amber-800 border-amber-500' },
  { value: 'silver', label: 'Gümüş', activeClass: 'bg-gray-200 text-gray-800 border-gray-500' },
  { value: 'gold', label: 'Altın', activeClass: 'bg-yellow-100 text-yellow-800 border-yellow-500' },
  { value: 'platinum', label: 'Platin', activeClass: 'bg-violet-100 text-violet-800 border-violet-500' }
]

const form = ref({
  name: '', tier: 'bronze', points_per_amount: 1, amount_for_points: 10, min_points: 100, expiry_days: 365, description: ''
})

const programs = computed(() => store.programs || [])

const filteredPrograms = computed(() => {
  let result = programs.value as any[]
  if (search.value) result = result.filter(p => p.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(p => p.status === filters.value.status)
  return result
})

const formatNumber = (n: number) => new Intl.NumberFormat('tr-TR').format(n || 0)
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const getTierLabel = (tier: string) => ({ bronze: 'Bronz', silver: 'Gümüş', gold: 'Altın', platinum: 'Platin' }[tier] || tier)
const getTierBgClass = (tier: string) => ({ bronze: 'bg-amber-500', silver: 'bg-gray-400', gold: 'bg-yellow-500', platinum: 'bg-violet-500' }[tier] || 'bg-gray-300')
const getTierBadge = (tier: string) => ({ bronze: 'bg-amber-100 text-amber-800', silver: 'bg-gray-200 text-gray-700', gold: 'bg-yellow-100 text-yellow-800', platinum: 'bg-violet-100 text-violet-800' }[tier] || 'bg-gray-100')
const getStatusLabel = (status: string) => ({ active: 'Aktif', inactive: 'Pasif', expired: 'Süresi Dolmuş' }[status] || status)
const getStatusBadge = (status: string) => ({ active: 'bg-green-100 text-green-800', inactive: 'bg-gray-100 text-gray-600', expired: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100')

const updateStats = () => {
  stats.value.active = programs.value.filter((p: any) => p.status === 'active').length
  stats.value.members = programs.value.reduce((sum: number, p: any) => sum + (p.member_count || 0), 0)
  stats.value.pointsGiven = programs.value.reduce((sum: number, p: any) => sum + (p.total_points_given || 0), 0)
  stats.value.pointsUsed = programs.value.reduce((sum: number, p: any) => sum + (p.total_points_used || 0), 0)
}

const openCreateModal = () => { form.value = { name: '', tier: 'bronze', points_per_amount: 1, amount_for_points: 10, min_points: 100, expiry_days: 365, description: '' }; isEdit.value = false; editingId.value = null; showModal.value = true }
const openEditModal = (program: any) => { form.value = { ...program }; isEdit.value = true; editingId.value = program.id; showModal.value = true }
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

const toggleStatus = async (program: any) => { await store.update(program.id, { status: program.status === 'active' ? 'inactive' : 'active' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu programı silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await store.fetchAll({}); updateStats() } finally { loading.value = false } }

const exportPrograms = () => {
  const csvContent = [
    ['Ad', 'Seviye', 'Puan Oranı', 'Üye Sayısı', 'Durum'].join(','),
    ...filteredPrograms.value.map(p => [p.name, getTierLabel(p.tier), `${p.points_per_amount}/${p.amount_for_points}`, p.member_count || 0, getStatusLabel(p.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `sadakat_programlari_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>