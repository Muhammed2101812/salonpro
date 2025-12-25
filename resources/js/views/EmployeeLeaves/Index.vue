<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çalışan İzinleri</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların izin taleplerini yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportLeaves"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          İzin Talebi
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Talep</p>
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
            <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Onaylanan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Reddedilen</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.rejected }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <CalendarIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Gün</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalDays }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- İzin Bakiyesi Özeti -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">İzin Türleri ve Bakiyeler</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div
          v-for="type in leaveTypes"
          :key="type.value"
          class="p-4 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
        >
          <div class="flex items-center gap-2 mb-2">
            <div :class="['w-3 h-3 rounded-full', type.color]"></div>
            <span class="text-sm font-medium text-gray-700">{{ type.label }}</span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-gray-900">{{ getLeaveCountByType(type.value) }}</span>
            <span class="text-xs text-gray-500">gün kullanıldı</span>
          </div>
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
              placeholder="Çalışan ara..."
              class="pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>

          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
            </option>
          </select>

          <!-- İzin Türü Filtresi -->
          <select
            v-model="filters.leaveType"
            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
          >
            <option value="">Tüm Türler</option>
            <option v-for="type in leaveTypes" :key="type.value" :value="type.value">
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
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ status.label }}
            </button>
          </div>

          <!-- Tarih Aralığı -->
          <div class="flex items-center gap-2">
            <input
              v-model="filters.startDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
            <span class="text-gray-500">-</span>
            <input
              v-model="filters.endDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
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

    <!-- İzin Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Yükleniyor -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              İzin Türü
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tarih Aralığı
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Süre
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Durum
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="leave in filteredLeaves" :key="leave.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white font-medium">
                  {{ getInitials(leave.employee?.first_name || 'U', leave.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ leave.employee?.first_name }} {{ leave.employee?.last_name }}
                  </p>
                  <p class="text-xs text-gray-500">{{ leave.employee?.position || 'Çalışan' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div :class="['w-3 h-3 rounded-full', getLeaveTypeColor(leave.leave_type)]"></div>
                <span class="text-sm text-gray-900">{{ getLeaveTypeLabel(leave.leave_type) }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">
                {{ formatDate(leave.start_date) }}
              </div>
              <div class="text-xs text-gray-500">
                {{ formatDate(leave.end_date) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-sm font-medium bg-gray-100 text-gray-700 rounded-full">
                {{ calculateDays(leave.start_date, leave.end_date) }} gün
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getStatusBadgeColor(leave.status)
                ]"
              >
                {{ getStatusLabel(leave.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="leave.status === 'pending'"
                  @click="approveLeave(leave)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Onayla"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="leave.status === 'pending'"
                  @click="rejectLeave(leave)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Reddet"
                >
                  <XMarkIcon class="h-4 w-4" />
                </button>
                <button
                  @click="viewLeave(leave)"
                  class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Detay"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editLeave(leave)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="deleteLeave(leave)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Sil"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Boş Durum -->
      <div v-if="filteredLeaves.length === 0 && !loading" class="p-12 text-center">
        <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">İzin talebi bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-blue-600 hover:text-blue-700 font-medium"
        >
          İlk izin talebini oluşturun
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="filteredLeaves.length > 0" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            Toplam {{ filteredLeaves.length }} izin talebi
          </p>
        </div>
      </div>
    </div>

    <!-- İzin Talebi Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingLeave ? 'İzin Düzenle' : 'Yeni İzin Talebi' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveLeave" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Çalışan Seçin</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- İzin Türü -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">İzin Türü *</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="type in leaveTypes"
                :key="type.value"
                type="button"
                @click="formData.leave_type = type.value"
                :class="[
                  'p-3 rounded-lg border text-left transition-colors',
                  formData.leave_type === type.value
                    ? 'border-blue-500 bg-blue-50'
                    : 'border-gray-200 hover:border-blue-300'
                ]"
              >
                <div class="flex items-center gap-2">
                  <div :class="['w-3 h-3 rounded-full', type.color]"></div>
                  <span class="text-sm font-medium text-gray-900">{{ type.label }}</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Tarih Aralığı -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi *</label>
              <input
                v-model="formData.start_date"
                type="date"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi *</label>
              <input
                v-model="formData.end_date"
                type="date"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
          </div>

          <!-- Yarım Gün Seçeneği -->
          <div class="flex items-center gap-4">
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="formData.is_half_day"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">Yarım gün izin</span>
            </label>
            <select
              v-if="formData.is_half_day"
              v-model="formData.half_day_period"
              class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            >
              <option value="morning">Sabah</option>
              <option value="afternoon">Öğleden Sonra</option>
            </select>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea
              v-model="formData.reason"
              rows="3"
              placeholder="İzin sebebini belirtin..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>
          </div>

          <!-- Durum (Sadece düzenleme modunda) -->
          <div v-if="editingLeave">
            <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
            <div class="flex gap-2">
              <button
                v-for="status in leaveStatuses"
                :key="status.value"
                type="button"
                @click="formData.status = status.value"
                :class="[
                  'flex-1 px-3 py-2 rounded-lg text-sm font-medium border transition-colors',
                  formData.status === status.value
                    ? status.activeClass
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ status.label }}
              </button>
            </div>
          </div>

          <!-- Özet Bilgi -->
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-blue-900">Toplam İzin Süresi</span>
              <span class="text-lg font-bold text-blue-700">
                {{ calculateFormDays() }} gün
              </span>
            </div>
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
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Detay Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">İzin Detayı</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <div v-if="selectedLeave" class="p-6 space-y-4">
          <div class="flex items-center gap-4">
            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white font-bold text-lg">
              {{ getInitials(selectedLeave.employee?.first_name || 'U', selectedLeave.employee?.last_name || 'N') }}
            </div>
            <div>
              <p class="text-lg font-semibold text-gray-900">
                {{ selectedLeave.employee?.first_name }} {{ selectedLeave.employee?.last_name }}
              </p>
              <p class="text-sm text-gray-500">{{ selectedLeave.employee?.position || 'Çalışan' }}</p>
            </div>
          </div>

          <div class="border-t border-gray-200 pt-4 space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">İzin Türü</span>
              <div class="flex items-center gap-2">
                <div :class="['w-3 h-3 rounded-full', getLeaveTypeColor(selectedLeave.leave_type)]"></div>
                <span class="text-sm font-medium text-gray-900">{{ getLeaveTypeLabel(selectedLeave.leave_type) }}</span>
              </div>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Başlangıç</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(selectedLeave.start_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Bitiş</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(selectedLeave.end_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Süre</span>
              <span class="text-sm font-medium text-gray-900">{{ calculateDays(selectedLeave.start_date, selectedLeave.end_date) }} gün</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Durum</span>
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getStatusBadgeColor(selectedLeave.status)
                ]"
              >
                {{ getStatusLabel(selectedLeave.status) }}
              </span>
            </div>
            <div v-if="selectedLeave.reason" class="pt-2">
              <span class="text-sm text-gray-500 block mb-1">Açıklama</span>
              <p class="text-sm text-gray-900 bg-gray-50 rounded-lg p-3">{{ selectedLeave.reason }}</p>
            </div>
          </div>

          <div v-if="selectedLeave.status === 'pending'" class="flex gap-3 pt-4 border-t border-gray-200">
            <button
              @click="approveLeave(selectedLeave); showDetailModal = false"
              class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
            >
              Onayla
            </button>
            <button
              @click="rejectLeave(selectedLeave); showDetailModal = false"
              class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors"
            >
              Reddet
            </button>
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
  CalendarDaysIcon,
  CalendarIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeLeaveStore } from '@/stores/employeeleave'

interface Leave {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  leave_type: string
  start_date: string
  end_date: string
  is_half_day: boolean
  half_day_period?: 'morning' | 'afternoon'
  reason?: string
  status: 'pending' | 'approved' | 'rejected' | 'cancelled'
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeLeaveStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const showDetailModal = ref(false)
const editingLeave = ref<Leave | null>(null)
const selectedLeave = ref<Leave | null>(null)
const search = ref('')

const leaves = ref<Leave[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: '',
  leaveType: '',
  status: '',
  startDate: '',
  endDate: ''
})

const stats = ref({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0,
  totalDays: 0
})

const leaveTypes = [
  { value: 'annual', label: 'Yıllık İzin', color: 'bg-blue-500' },
  { value: 'sick', label: 'Hastalık', color: 'bg-red-500' },
  { value: 'personal', label: 'Mazeret', color: 'bg-orange-500' },
  { value: 'maternity', label: 'Doğum', color: 'bg-pink-500' },
  { value: 'marriage', label: 'Evlilik', color: 'bg-purple-500' },
  { value: 'bereavement', label: 'Ölüm', color: 'bg-gray-500' },
  { value: 'unpaid', label: 'Ücretsiz', color: 'bg-yellow-500' },
  { value: 'other', label: 'Diğer', color: 'bg-teal-500' }
]

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'pending', label: 'Bekleyen' },
  { value: 'approved', label: 'Onaylanan' },
  { value: 'rejected', label: 'Reddedilen' }
]

const leaveStatuses = [
  { value: 'pending', label: 'Bekliyor', activeClass: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
  { value: 'approved', label: 'Onaylandı', activeClass: 'bg-green-100 text-green-800 border-green-300' },
  { value: 'rejected', label: 'Reddedildi', activeClass: 'bg-red-100 text-red-800 border-red-300' }
]

const formData = ref({
  employee_id: '',
  leave_type: 'annual',
  start_date: '',
  end_date: '',
  is_half_day: false,
  half_day_period: 'morning' as const,
  reason: '',
  status: 'pending' as const
})

// Computed
const filteredLeaves = computed(() => {
  let result = leaves.value

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(l =>
      l.employee?.first_name?.toLowerCase().includes(searchLower) ||
      l.employee?.last_name?.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.employeeId) {
    result = result.filter(l => l.employee_id === filters.value.employeeId)
  }

  if (filters.value.leaveType) {
    result = result.filter(l => l.leave_type === filters.value.leaveType)
  }

  if (filters.value.status) {
    result = result.filter(l => l.status === filters.value.status)
  }

  if (filters.value.startDate) {
    result = result.filter(l => l.start_date >= filters.value.startDate)
  }

  if (filters.value.endDate) {
    result = result.filter(l => l.end_date <= filters.value.endDate)
  }

  return result.sort((a, b) => new Date(b.start_date).getTime() - new Date(a.start_date).getTime())
})

// Helpers
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const calculateDays = (startDate: string, endDate: string) => {
  const start = new Date(startDate)
  const end = new Date(endDate)
  const diffTime = Math.abs(end.getTime() - start.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  return diffDays
}

const calculateFormDays = () => {
  if (!formData.value.start_date || !formData.value.end_date) return 0
  let days = calculateDays(formData.value.start_date, formData.value.end_date)
  if (formData.value.is_half_day) days = days - 0.5
  return days
}

const getLeaveTypeLabel = (type: string) => {
  return leaveTypes.find(t => t.value === type)?.label || type
}

const getLeaveTypeColor = (type: string) => {
  return leaveTypes.find(t => t.value === type)?.color || 'bg-gray-500'
}

const getLeaveCountByType = (type: string) => {
  return leaves.value
    .filter(l => l.leave_type === type && l.status === 'approved')
    .reduce((sum, l) => sum + calculateDays(l.start_date, l.end_date), 0)
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Bekliyor',
    approved: 'Onaylandı',
    rejected: 'Reddedildi',
    cancelled: 'İptal'
  }
  return labels[status] || status
}

const getStatusBadgeColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-600'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

// Modal Functions
const openCreateModal = () => {
  editingLeave.value = null
  formData.value = {
    employee_id: '',
    leave_type: 'annual',
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
    is_half_day: false,
    half_day_period: 'morning',
    reason: '',
    status: 'pending'
  }
  showModal.value = true
}

const editLeave = (leave: Leave) => {
  editingLeave.value = leave
  formData.value = {
    employee_id: leave.employee_id,
    leave_type: leave.leave_type,
    start_date: leave.start_date,
    end_date: leave.end_date,
    is_half_day: leave.is_half_day || false,
    half_day_period: leave.half_day_period || 'morning',
    reason: leave.reason || '',
    status: leave.status
  }
  showModal.value = true
}

const viewLeave = (leave: Leave) => {
  selectedLeave.value = leave
  showDetailModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingLeave.value = null
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

    // İzinleri yükle
    const response = await store.fetchAll()
    leaves.value = response?.data || []

    // İstatistikleri hesapla
    stats.value.total = leaves.value.length
    stats.value.pending = leaves.value.filter(l => l.status === 'pending').length
    stats.value.approved = leaves.value.filter(l => l.status === 'approved').length
    stats.value.rejected = leaves.value.filter(l => l.status === 'rejected').length
    stats.value.totalDays = leaves.value
      .filter(l => l.status === 'approved')
      .reduce((sum, l) => sum + calculateDays(l.start_date, l.end_date), 0)
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const saveLeave = async () => {
  saving.value = true
  try {
    if (editingLeave.value) {
      await store.update(editingLeave.value.id, formData.value)
    } else {
      await store.create(formData.value)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('İzin kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const approveLeave = async (leave: Leave) => {
  try {
    await store.update(leave.id, { ...leave, status: 'approved' })
    await loadData()
  } catch (error) {
    console.error('Onaylama hatası:', error)
  }
}

const rejectLeave = async (leave: Leave) => {
  try {
    await store.update(leave.id, { ...leave, status: 'rejected' })
    await loadData()
  } catch (error) {
    console.error('Reddetme hatası:', error)
  }
}

const deleteLeave = async (leave: Leave) => {
  if (!confirm('Bu izin talebini silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(leave.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('İzin silinemedi')
  }
}

const exportLeaves = () => {
  const csvContent = [
    ['Çalışan', 'İzin Türü', 'Başlangıç', 'Bitiş', 'Gün', 'Durum', 'Açıklama'].join(','),
    ...filteredLeaves.value.map(l => [
      `${l.employee?.first_name} ${l.employee?.last_name}`,
      getLeaveTypeLabel(l.leave_type),
      l.start_date,
      l.end_date,
      calculateDays(l.start_date, l.end_date),
      getStatusLabel(l.status),
      l.reason || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `izinler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>