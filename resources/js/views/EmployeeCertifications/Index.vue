<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Sertifika ve Belgeler</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların sertifika, eğitim ve yetkinlik belgelerini yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportCertifications"
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
          Sertifika Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100">
            <DocumentTextIcon class="h-6 w-6 text-violet-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Sertifika</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckBadgeIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Geçerli</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.valid }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Süresi Dolacak</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.expiringSoon }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Süresi Dolmuş</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.expired }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <UserGroupIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Sertifikalı Çalışan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.certifiedEmployees }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Süresi Dolacaklar Uyarısı -->
    <div v-if="expiringSoonList.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
      <div class="flex items-start gap-3">
        <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5" />
        <div>
          <h4 class="font-semibold text-yellow-800">Süresi Dolacak Sertifikalar</h4>
          <p class="text-sm text-yellow-700 mt-1">Aşağıdaki sertifikaların süresi 30 gün içinde dolacak:</p>
          <ul class="mt-2 space-y-1">
            <li v-for="cert in expiringSoonList" :key="cert.id" class="text-sm text-yellow-800">
              • <span class="font-medium">{{ cert.employee?.first_name }} {{ cert.employee?.last_name }}</span> - 
              {{ cert.name }} ({{ formatDate(cert.expiry_date) }})
            </li>
          </ul>
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
              placeholder="Sertifika ara..."
              class="pl-10 rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
            />
          </div>

          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
            </option>
          </select>

          <!-- Tür Filtresi -->
          <select
            v-model="filters.type"
            class="rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"
          >
            <option value="">Tüm Türler</option>
            <option v-for="type in certificationTypes" :key="type.value" :value="type.value">
              {{ type.label }}
            </option>
          </select>

          <!-- Durum Filtresi -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="status in statusFilters"
              :key="status.value"
              @click="filters.status = status.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.status === status.value
                  ? 'bg-violet-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ status.label }}
            </button>
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

    <!-- Sertifika Kartları Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="cert in filteredCertifications"
        :key="cert.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
      >
        <div
          :class="[
            'h-2',
            getCertStatusColor(cert.expiry_date)
          ]"
        ></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div
                :class="[
                  'p-2 rounded-lg',
                  getTypeBackground(cert.certification_type)
                ]"
              >
                <component :is="getTypeIcon(cert.certification_type)" class="h-6 w-6" :class="getTypeIconColor(cert.certification_type)" />
              </div>
              <div>
                <h4 class="font-semibold text-gray-900">{{ cert.name }}</h4>
                <p class="text-xs text-gray-500">{{ getTypeLabel(cert.certification_type) }}</p>
              </div>
            </div>
            <span
              :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                getStatusBadge(cert.expiry_date)
              ]"
            >
              {{ getStatusLabel(cert.expiry_date) }}
            </span>
          </div>

          <div class="flex items-center gap-3 mb-4">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white font-medium">
              {{ getInitials(cert.employee?.first_name || 'U', cert.employee?.last_name || 'N') }}
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">
                {{ cert.employee?.first_name }} {{ cert.employee?.last_name }}
              </p>
              <p class="text-xs text-gray-500">{{ cert.employee?.position || 'Çalışan' }}</p>
            </div>
          </div>

          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Verilme Tarihi:</span>
              <span class="font-medium text-gray-900">{{ formatDate(cert.issue_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Geçerlilik:</span>
              <span class="font-medium" :class="getExpiryTextColor(cert.expiry_date)">
                {{ cert.expiry_date ? formatDate(cert.expiry_date) : 'Süresiz' }}
              </span>
            </div>
            <div v-if="cert.issuing_organization" class="flex justify-between">
              <span class="text-gray-500">Veren Kurum:</span>
              <span class="font-medium text-gray-900 truncate max-w-[150px]">{{ cert.issuing_organization }}</span>
            </div>
            <div v-if="cert.credential_id" class="flex justify-between">
              <span class="text-gray-500">Sertifika No:</span>
              <span class="font-medium text-gray-900">{{ cert.credential_id }}</span>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex gap-2">
              <button
                v-if="cert.document_url"
                @click="viewDocument(cert)"
                class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                title="Belgeyi Görüntüle"
              >
                <EyeIcon class="h-4 w-4" />
              </button>
              <button
                @click="editCertification(cert)"
                class="p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                title="Düzenle"
              >
                <PencilIcon class="h-4 w-4" />
              </button>
              <button
                @click="deleteCertification(cert)"
                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                title="Sil"
              >
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
            <span v-if="getDaysUntilExpiry(cert.expiry_date) !== null" class="text-xs text-gray-500">
              {{ getDaysUntilExpiry(cert.expiry_date) > 0 
                ? `${getDaysUntilExpiry(cert.expiry_date)} gün kaldı` 
                : `${Math.abs(getDaysUntilExpiry(cert.expiry_date))} gün geçti` }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Boş Durum -->
    <div v-if="filteredCertifications.length === 0 && !loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
      <p class="text-gray-500">Sertifika bulunamadı</p>
      <button
        @click="openCreateModal"
        class="mt-4 text-violet-600 hover:text-violet-700 font-medium"
      >
        Sertifika ekleyin
      </button>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Sertifika Ekleme/Düzenleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingCertification ? 'Sertifika Düzenle' : 'Yeni Sertifika' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveCertification" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            >
              <option value="">Çalışan Seçin</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- Sertifika Adı -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sertifika Adı *</label>
            <input
              v-model="formData.name"
              type="text"
              required
              placeholder="Örn: Ustalık Belgesi"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            />
          </div>

          <!-- Sertifika Türü -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sertifika Türü *</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="type in certificationTypes"
                :key="type.value"
                type="button"
                @click="formData.certification_type = type.value"
                :class="[
                  'p-3 rounded-lg border text-left transition-colors',
                  formData.certification_type === type.value
                    ? 'border-violet-500 bg-violet-50'
                    : 'border-gray-200 hover:border-violet-300'
                ]"
              >
                <div class="flex items-center gap-2">
                  <component :is="type.icon" class="h-5 w-5 text-violet-600" />
                  <span class="text-sm font-medium text-gray-900">{{ type.label }}</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Tarihler -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Verilme Tarihi *</label>
              <input
                v-model="formData.issue_date"
                type="date"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Geçerlilik Tarihi</label>
              <input
                v-model="formData.expiry_date"
                type="date"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
              <p class="text-xs text-gray-500 mt-1">Boş bırakılırsa süresiz</p>
            </div>
          </div>

          <!-- Veren Kurum -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Veren Kurum</label>
            <input
              v-model="formData.issuing_organization"
              type="text"
              placeholder="Örn: Milli Eğitim Bakanlığı"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            />
          </div>

          <!-- Sertifika No -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sertifika/Belge No</label>
            <input
              v-model="formData.credential_id"
              type="text"
              placeholder="Örn: 2024-12345"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            />
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="2"
              placeholder="Ek bilgiler..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
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
              :disabled="saving"
              class="px-6 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
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
  DocumentTextIcon,
  CheckBadgeIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  UserGroupIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  EyeIcon,
  AcademicCapIcon,
  ShieldCheckIcon,
  BeakerIcon,
  WrenchScrewdriverIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeCertificationStore } from '@/stores/employeecertification'

interface Certification {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  name: string
  certification_type: string
  issue_date: string
  expiry_date?: string
  issuing_organization?: string
  credential_id?: string
  document_url?: string
  notes?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeCertificationStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingCertification = ref<Certification | null>(null)
const search = ref('')

const certifications = ref<Certification[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: '',
  type: '',
  status: ''
})

const stats = ref({
  total: 0,
  valid: 0,
  expiringSoon: 0,
  expired: 0,
  certifiedEmployees: 0
})

const certificationTypes = [
  { value: 'mastery', label: 'Ustalık Belgesi', icon: AcademicCapIcon },
  { value: 'hygiene', label: 'Hijyen Sertifikası', icon: ShieldCheckIcon },
  { value: 'training', label: 'Eğitim Sertifikası', icon: AcademicCapIcon },
  { value: 'health', label: 'Sağlık Raporu', icon: BeakerIcon },
  { value: 'technique', label: 'Teknik Yeterlilik', icon: WrenchScrewdriverIcon },
  { value: 'other', label: 'Diğer', icon: DocumentTextIcon }
]

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'valid', label: 'Geçerli' },
  { value: 'expiring', label: 'Dolacak' },
  { value: 'expired', label: 'Dolmuş' }
]

const formData = ref({
  employee_id: '',
  name: '',
  certification_type: 'mastery',
  issue_date: new Date().toISOString().split('T')[0],
  expiry_date: '',
  issuing_organization: '',
  credential_id: '',
  notes: ''
})

// Computed
const filteredCertifications = computed(() => {
  let result = certifications.value

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(c =>
      c.name.toLowerCase().includes(searchLower) ||
      c.employee?.first_name?.toLowerCase().includes(searchLower) ||
      c.employee?.last_name?.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.employeeId) {
    result = result.filter(c => c.employee_id === filters.value.employeeId)
  }

  if (filters.value.type) {
    result = result.filter(c => c.certification_type === filters.value.type)
  }

  if (filters.value.status) {
    result = result.filter(c => {
      const status = getCertStatus(c.expiry_date)
      return status === filters.value.status
    })
  }

  return result.sort((a, b) => {
    // Süresi dolacakları önce göster
    const daysA = getDaysUntilExpiry(a.expiry_date) ?? 999
    const daysB = getDaysUntilExpiry(b.expiry_date) ?? 999
    return daysA - daysB
  })
})

const expiringSoonList = computed(() => {
  return certifications.value.filter(c => {
    const days = getDaysUntilExpiry(c.expiry_date)
    return days !== null && days > 0 && days <= 30
  })
})

// Helpers
const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getDaysUntilExpiry = (expiryDate?: string): number | null => {
  if (!expiryDate) return null
  const today = new Date()
  const expiry = new Date(expiryDate)
  const diffTime = expiry.getTime() - today.getTime()
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

const getCertStatus = (expiryDate?: string): string => {
  if (!expiryDate) return 'valid'
  const days = getDaysUntilExpiry(expiryDate)
  if (days === null) return 'valid'
  if (days < 0) return 'expired'
  if (days <= 30) return 'expiring'
  return 'valid'
}

const getCertStatusColor = (expiryDate?: string) => {
  const status = getCertStatus(expiryDate)
  const colors: Record<string, string> = {
    valid: 'bg-green-500',
    expiring: 'bg-yellow-500',
    expired: 'bg-red-500'
  }
  return colors[status]
}

const getStatusLabel = (expiryDate?: string) => {
  if (!expiryDate) return 'Süresiz'
  const status = getCertStatus(expiryDate)
  const labels: Record<string, string> = {
    valid: 'Geçerli',
    expiring: 'Dolacak',
    expired: 'Dolmuş'
  }
  return labels[status]
}

const getStatusBadge = (expiryDate?: string) => {
  if (!expiryDate) return 'bg-blue-100 text-blue-800'
  const status = getCertStatus(expiryDate)
  const badges: Record<string, string> = {
    valid: 'bg-green-100 text-green-800',
    expiring: 'bg-yellow-100 text-yellow-800',
    expired: 'bg-red-100 text-red-800'
  }
  return badges[status]
}

const getExpiryTextColor = (expiryDate?: string) => {
  const status = getCertStatus(expiryDate)
  const colors: Record<string, string> = {
    valid: 'text-green-600',
    expiring: 'text-yellow-600',
    expired: 'text-red-600'
  }
  return colors[status] || 'text-gray-900'
}

const getTypeLabel = (type: string) => {
  return certificationTypes.find(t => t.value === type)?.label || type
}

const getTypeIcon = (type: string) => {
  return certificationTypes.find(t => t.value === type)?.icon || DocumentTextIcon
}

const getTypeBackground = (type: string) => {
  const backgrounds: Record<string, string> = {
    mastery: 'bg-purple-100',
    hygiene: 'bg-green-100',
    training: 'bg-blue-100',
    health: 'bg-red-100',
    technique: 'bg-orange-100',
    other: 'bg-gray-100'
  }
  return backgrounds[type] || 'bg-gray-100'
}

const getTypeIconColor = (type: string) => {
  const colors: Record<string, string> = {
    mastery: 'text-purple-600',
    hygiene: 'text-green-600',
    training: 'text-blue-600',
    health: 'text-red-600',
    technique: 'text-orange-600',
    other: 'text-gray-600'
  }
  return colors[type] || 'text-gray-600'
}

// Modal Functions
const openCreateModal = () => {
  editingCertification.value = null
  formData.value = {
    employee_id: '',
    name: '',
    certification_type: 'mastery',
    issue_date: new Date().toISOString().split('T')[0],
    expiry_date: '',
    issuing_organization: '',
    credential_id: '',
    notes: ''
  }
  showModal.value = true
}

const editCertification = (cert: Certification) => {
  editingCertification.value = cert
  formData.value = {
    employee_id: cert.employee_id,
    name: cert.name,
    certification_type: cert.certification_type,
    issue_date: cert.issue_date,
    expiry_date: cert.expiry_date || '',
    issuing_organization: cert.issuing_organization || '',
    credential_id: cert.credential_id || '',
    notes: cert.notes || ''
  }
  showModal.value = true
}

const viewDocument = (cert: Certification) => {
  if (cert.document_url) {
    window.open(cert.document_url, '_blank')
  }
}

const closeModal = () => {
  showModal.value = false
  editingCertification.value = null
}

// CRUD Operations
const loadData = async () => {
  loading.value = true
  try {
    // Çalışanları yükle
    const empResponse = await fetch('/api/employees')
    if (empResponse.ok) {
      const data = await empResponse.json()
      employees.value = data.data || []
    }

    // Sertifikaları yükle
    const response = await store.fetchAll()
    certifications.value = response?.data || []

    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateStats = () => {
  stats.value.total = certifications.value.length
  stats.value.valid = certifications.value.filter(c => getCertStatus(c.expiry_date) === 'valid').length
  stats.value.expiringSoon = certifications.value.filter(c => getCertStatus(c.expiry_date) === 'expiring').length
  stats.value.expired = certifications.value.filter(c => getCertStatus(c.expiry_date) === 'expired').length
  
  const employeeIds = new Set(certifications.value.map(c => c.employee_id))
  stats.value.certifiedEmployees = employeeIds.size
}

const saveCertification = async () => {
  saving.value = true
  try {
    if (editingCertification.value) {
      await store.update(editingCertification.value.id, formData.value)
    } else {
      await store.create(formData.value)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Sertifika kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const deleteCertification = async (cert: Certification) => {
  if (!confirm('Bu sertifikayı silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(cert.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Sertifika silinemedi')
  }
}

const exportCertifications = () => {
  const csvContent = [
    ['Çalışan', 'Sertifika', 'Tür', 'Verilme Tarihi', 'Geçerlilik', 'Veren Kurum', 'Durum'].join(','),
    ...filteredCertifications.value.map(c => [
      `${c.employee?.first_name} ${c.employee?.last_name}`,
      c.name,
      getTypeLabel(c.certification_type),
      c.issue_date,
      c.expiry_date || 'Süresiz',
      c.issuing_organization || '',
      getStatusLabel(c.expiry_date)
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `sertifikalar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>