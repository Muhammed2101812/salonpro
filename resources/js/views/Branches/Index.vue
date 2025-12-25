<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Şubeler</h1>
        <p class="mt-2 text-sm text-gray-600">Salon şubelerinizi yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="toggleView"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <component :is="viewMode === 'grid' ? ListBulletIcon : Squares2X2Icon" class="h-5 w-5 mr-2 text-gray-500" />
          {{ viewMode === 'grid' ? 'Liste' : 'Kart' }}
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-cyan-600 hover:bg-cyan-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Şube Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-cyan-100">
            <BuildingStorefrontIcon class="h-6 w-6 text-cyan-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Şube</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalBranches }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aktif Şube</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.activeBranches }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <UserGroupIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Çalışan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalEmployees }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <MapPinIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Şehir Sayısı</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.uniqueCities }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="branchStore.loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Grid Görünümü -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="branch in branchStore.branches"
        :key="branch.id"
        :class="[
          'bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all',
          branch.is_active ? 'border-gray-100' : 'border-gray-200 opacity-75'
        ]"
      >
        <!-- Üst Renk Bar -->
        <div :class="['h-2', branch.is_active ? 'bg-gradient-to-r from-cyan-500 to-teal-500' : 'bg-gray-300']"></div>
        
        <div class="p-6">
          <!-- Başlık -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="p-3 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-500 text-white">
                <BuildingStorefrontIcon class="h-6 w-6" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">{{ branch.name }}</h3>
                <span class="text-sm text-gray-500">{{ branch.code }}</span>
              </div>
            </div>
            <span
              :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                branch.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]"
            >
              {{ branch.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>

          <!-- İletişim Bilgileri -->
          <div class="space-y-3 mb-4">
            <div v-if="branch.address" class="flex items-start gap-2 text-sm text-gray-600">
              <MapPinIcon class="h-4 w-4 text-gray-400 mt-0.5 flex-shrink-0" />
              <span class="line-clamp-2">{{ branch.address }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
              <MapPinIcon class="h-4 w-4 text-gray-400" />
              <span>{{ branch.city || 'Şehir belirtilmemiş' }}, {{ branch.country || 'Türkiye' }}</span>
            </div>
            <div v-if="branch.phone" class="flex items-center gap-2 text-sm text-gray-600">
              <PhoneIcon class="h-4 w-4 text-gray-400" />
              <a :href="`tel:${branch.phone}`" class="hover:text-cyan-600">{{ branch.phone }}</a>
            </div>
            <div v-if="branch.email" class="flex items-center gap-2 text-sm text-gray-600">
              <EnvelopeIcon class="h-4 w-4 text-gray-400" />
              <a :href="`mailto:${branch.email}`" class="hover:text-cyan-600 truncate">{{ branch.email }}</a>
            </div>
          </div>

          <!-- Çalışma Saatleri -->
          <div v-if="branch.working_hours" class="mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
              <ClockIcon class="h-4 w-4 text-gray-400" />
              <span class="text-sm font-medium text-gray-700">Çalışma Saatleri</span>
            </div>
            <div class="text-sm text-gray-600">
              {{ branch.working_hours }}
            </div>
          </div>

          <!-- İstatistikler -->
          <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-purple-50 rounded-lg p-3 text-center">
              <p class="text-lg font-bold text-purple-600">{{ branch.employee_count || 0 }}</p>
              <p class="text-xs text-gray-500">Çalışan</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
              <p class="text-lg font-bold text-blue-600">{{ branch.customer_count || 0 }}</p>
              <p class="text-xs text-gray-500">Müşteri</p>
            </div>
          </div>

          <!-- Aksiyon Butonları -->
          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex gap-2">
              <button
                @click="viewBranchDetails(branch)"
                class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                title="Detay"
              >
                <EyeIcon class="h-4 w-4" />
              </button>
              <button
                @click="openEditModal(branch)"
                class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-lg transition-colors"
                title="Düzenle"
              >
                <PencilIcon class="h-4 w-4" />
              </button>
              <button
                @click="handleDelete(branch.id)"
                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="Sil"
              >
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
            <button
              v-if="branch.is_active"
              class="text-xs text-cyan-600 hover:text-cyan-700 font-medium"
              @click="selectBranch(branch)"
            >
              Bu Şubeyi Seç →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste Görünümü -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şube</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İletişim</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konum</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çalışan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="branch in branchStore.branches" :key="branch.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 text-white">
                  <BuildingStorefrontIcon class="h-5 w-5" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ branch.name }}</p>
                  <p class="text-xs text-gray-500">{{ branch.code }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div v-if="branch.phone" class="text-sm text-gray-600">{{ branch.phone }}</div>
              <div v-if="branch.email" class="text-xs text-gray-500 truncate max-w-[150px]">{{ branch.email }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600">{{ branch.city || '-' }}</div>
              <div class="text-xs text-gray-500">{{ branch.country || 'Türkiye' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <UserGroupIcon class="h-4 w-4 text-gray-400" />
                <span class="text-sm text-gray-600">{{ branch.employee_count || 0 }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs rounded-full font-semibold',
                  branch.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ branch.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="openEditModal(branch)"
                  class="p-1.5 text-cyan-600 hover:bg-cyan-50 rounded-lg transition-colors"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="handleDelete(branch.id)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="branchStore.branches.length === 0" class="p-12 text-center">
        <BuildingStorefrontIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Şube bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-cyan-600 hover:text-cyan-700 font-medium"
        >
          İlk şubeyi ekleyin
        </button>
      </div>
    </div>

    <!-- Şube Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEdit ? 'Şube Düzenle' : 'Yeni Şube' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Şube Adı ve Kodu -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube Adı *</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Örn: Kadıköy Şubesi"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube Kodu *</label>
              <input
                v-model="form.code"
                type="text"
                required
                placeholder="Örn: KDK-01"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
            </div>
          </div>

          <!-- Telefon ve E-posta -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="0216 XXX XX XX"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="kadikoy@salon.com"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
            </div>
          </div>

          <!-- Adres -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
            <textarea
              v-model="form.address"
              rows="2"
              placeholder="Tam adres..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            ></textarea>
          </div>

          <!-- Şehir ve Ülke -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
              <input
                v-model="form.city"
                type="text"
                list="cityList"
                placeholder="İstanbul"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
              <datalist id="cityList">
                <option value="İstanbul" />
                <option value="Ankara" />
                <option value="İzmir" />
                <option value="Antalya" />
                <option value="Bursa" />
              </datalist>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ülke</label>
              <input
                v-model="form.country"
                type="text"
                placeholder="Türkiye"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
              />
            </div>
          </div>

          <!-- Çalışma Saatleri -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışma Saatleri</label>
            <input
              v-model="form.working_hours"
              type="text"
              placeholder="Örn: Hafta içi 09:00-21:00, Hafta sonu 10:00-19:00"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
            />
          </div>

          <!-- Aktif -->
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500"
            />
            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
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
              :disabled="branchStore.loading"
              class="px-6 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ branchStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
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
  UserGroupIcon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  ClockIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  EyeIcon,
  ListBulletIcon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'
import { useBranchStore } from '@/stores/branch'

interface Branch {
  id: string
  name: string
  code: string
  phone?: string
  email?: string
  address?: string
  city?: string
  country?: string
  working_hours?: string
  is_active: boolean
  employee_count?: number
  customer_count?: number
}

const branchStore = useBranchStore()

// State
const viewMode = ref<'grid' | 'table'>('grid')
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)

const stats = ref({
  totalBranches: 0,
  activeBranches: 0,
  totalEmployees: 0,
  uniqueCities: 0
})

const form = ref({
  name: '',
  code: '',
  phone: '',
  email: '',
  address: '',
  city: '',
  country: 'Türkiye',
  working_hours: '',
  is_active: true
})

// Helpers
const toggleView = () => {
  viewMode.value = viewMode.value === 'grid' ? 'table' : 'grid'
}

const updateStats = () => {
  const branches = branchStore.branches as Branch[]
  stats.value.totalBranches = branches.length
  stats.value.activeBranches = branches.filter(b => b.is_active).length
  stats.value.totalEmployees = branches.reduce((acc, b) => acc + (b.employee_count || 0), 0)
  
  const cities = new Set(branches.map(b => b.city).filter(c => c))
  stats.value.uniqueCities = cities.size
}

// Modal Methods
const openCreateModal = () => {
  form.value = {
    name: '',
    code: '',
    phone: '',
    email: '',
    address: '',
    city: '',
    country: 'Türkiye',
    working_hours: '',
    is_active: true
  }
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (branch: Branch) => {
  form.value = {
    name: branch.name || '',
    code: branch.code || '',
    phone: branch.phone || '',
    email: branch.email || '',
    address: branch.address || '',
    city: branch.city || '',
    country: branch.country || 'Türkiye',
    working_hours: branch.working_hours || '',
    is_active: branch.is_active ?? true
  }
  isEdit.value = true
  editingId.value = branch.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const viewBranchDetails = (branch: Branch) => {
  // Detay sayfasına yönlendirme veya modal
  openEditModal(branch)
}

const selectBranch = (branch: Branch) => {
  // Şube seçim işlemi - localStorage veya store'a kaydet
  localStorage.setItem('selectedBranchId', branch.id)
  alert(`${branch.name} şubesi seçildi!`)
}

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await branchStore.updateBranch(editingId.value, form.value)
    } else {
      await branchStore.createBranch(form.value)
    }
    closeModal()
    updateStats()
  } catch (error) {
    console.error('Şube kaydedilemedi:', error)
    alert('Şube kaydedilemedi')
  }
}

const handleDelete = async (id: string) => {
  if (!confirm('Bu şubeyi silmek istediğinizden emin misiniz? Şubeye bağlı tüm veriler etkilenebilir.')) return
  try {
    await branchStore.deleteBranch(id)
    updateStats()
  } catch (error) {
    console.error('Şube silinemedi:', error)
    alert('Şube silinemedi')
  }
}

onMounted(async () => {
  await branchStore.fetchBranches()
  updateStats()
})
</script>
