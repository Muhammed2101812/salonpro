<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Devam Takibi</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların giriş/çıkış kayıtlarını ve devam durumlarını takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportAttendances"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Kayıt Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100">
            <UserGroupIcon class="h-6 w-6 text-teal-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bugün Gelen</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.todayPresent }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bugün Gelmeyen</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.todayAbsent }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Geç Gelen</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.todayLate }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <ArrowRightStartOnRectangleIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Erken Çıkan</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.todayEarlyLeave }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bu Ay</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.monthTotal }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <ChartBarIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Devam Oranı</p>
            <p class="text-2xl font-bold text-gray-900">%{{ stats.attendanceRate }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Bugünün Durumu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Bugünün Durumu</h3>
        <span class="text-sm text-gray-500">{{ formatDateFull(today) }}</span>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div
          v-for="employee in todayStatus"
          :key="employee.id"
          class="p-4 rounded-lg border border-gray-200 hover:border-teal-300 transition-colors"
        >
          <div class="flex items-center gap-3 mb-3">
            <div
              :class="[
                'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-medium',
                employee.status === 'present' ? 'bg-gradient-to-br from-teal-500 to-green-500' :
                employee.status === 'absent' ? 'bg-gradient-to-br from-red-500 to-pink-500' :
                employee.status === 'late' ? 'bg-gradient-to-br from-yellow-500 to-orange-500' :
                'bg-gradient-to-br from-gray-400 to-gray-500'
              ]"
            >
              {{ getInitials(employee.first_name, employee.last_name) }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ employee.first_name }}
              </p>
              <p class="text-xs text-gray-500 truncate">{{ employee.last_name }}</p>
            </div>
          </div>
          <div class="space-y-1">
            <div v-if="employee.check_in" class="flex items-center text-xs text-gray-600">
              <ArrowRightEndOnRectangleIcon class="h-3 w-3 mr-1 text-green-500" />
              Giriş: {{ employee.check_in }}
            </div>
            <div v-if="employee.check_out" class="flex items-center text-xs text-gray-600">
              <ArrowRightStartOnRectangleIcon class="h-3 w-3 mr-1 text-red-500" />
              Çıkış: {{ employee.check_out }}
            </div>
            <div v-if="!employee.check_in && employee.status !== 'absent'" class="text-xs text-gray-400 italic">
              Henüz giriş yok
            </div>
          </div>
          <span
            :class="[
              'mt-2 inline-block px-2 py-0.5 text-xs font-medium rounded-full',
              getStatusBadgeColor(employee.status)
            ]"
          >
            {{ getStatusLabel(employee.status) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <!-- Tarih Seçici -->
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
            <button
              @click="previousDay"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
            </button>
            <input
              v-model="selectedDate"
              type="date"
              class="bg-transparent border-none text-sm font-medium text-gray-700 focus:ring-0"
            />
            <button
              @click="nextDay"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-600" />
            </button>
            <button
              @click="goToToday"
              class="ml-2 px-2 py-1 text-xs font-medium text-teal-600 hover:bg-teal-50 rounded transition-colors"
            >
              Bugün
            </button>
          </div>

          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
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
                  ? 'bg-teal-600 text-white'
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

    <!-- Kayıt Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Yükleniyor -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tarih
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Giriş
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çıkış
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışma Süresi
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
          <tr v-for="attendance in filteredAttendances" :key="attendance.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  :class="[
                    'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-medium',
                    attendance.status === 'present' ? 'bg-gradient-to-br from-teal-500 to-green-500' :
                    attendance.status === 'absent' ? 'bg-gradient-to-br from-red-500 to-pink-500' :
                    attendance.status === 'late' ? 'bg-gradient-to-br from-yellow-500 to-orange-500' :
                    'bg-gradient-to-br from-gray-400 to-gray-500'
                  ]"
                >
                  {{ getInitials(attendance.employee?.first_name || 'U', attendance.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ attendance.employee?.first_name }} {{ attendance.employee?.last_name }}
                  </p>
                  <p class="text-xs text-gray-500">{{ attendance.employee?.position || 'Çalışan' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(attendance.date) }}</div>
              <div class="text-xs text-gray-500">{{ getDayOfWeek(attendance.date) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-if="attendance.check_in_time" class="flex items-center">
                <ArrowRightEndOnRectangleIcon class="h-4 w-4 mr-1 text-green-500" />
                <span class="text-sm font-medium text-gray-900">{{ attendance.check_in_time }}</span>
                <span
                  v-if="attendance.is_late"
                  class="ml-2 px-1.5 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded"
                >
                  +{{ attendance.late_minutes }} dk
                </span>
              </div>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-if="attendance.check_out_time" class="flex items-center">
                <ArrowRightStartOnRectangleIcon class="h-4 w-4 mr-1 text-red-500" />
                <span class="text-sm font-medium text-gray-900">{{ attendance.check_out_time }}</span>
                <span
                  v-if="attendance.is_early_leave"
                  class="ml-2 px-1.5 py-0.5 text-xs bg-orange-100 text-orange-700 rounded"
                >
                  -{{ attendance.early_leave_minutes }} dk
                </span>
              </div>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="attendance.work_hours" class="text-sm font-medium text-gray-900">
                {{ attendance.work_hours }} saat
              </span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getStatusBadgeColor(attendance.status)
                ]"
              >
                {{ getStatusLabel(attendance.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="!attendance.check_in_time"
                  @click="checkIn(attendance)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Giriş Yap"
                >
                  <ArrowRightEndOnRectangleIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="attendance.check_in_time && !attendance.check_out_time"
                  @click="checkOut(attendance)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Çıkış Yap"
                >
                  <ArrowRightStartOnRectangleIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editAttendance(attendance)"
                  class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="deleteAttendance(attendance)"
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
      <div v-if="filteredAttendances.length === 0 && !loading" class="p-12 text-center">
        <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Bu tarihte kayıt bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-teal-600 hover:text-teal-700 font-medium"
        >
          Kayıt ekleyin
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="filteredAttendances.length > 0" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            Toplam {{ filteredAttendances.length }} kayıt
          </p>
        </div>
      </div>
    </div>

    <!-- Devam Kaydı Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingAttendance ? 'Kaydı Düzenle' : 'Yeni Devam Kaydı' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveAttendance" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            >
              <option value="">Çalışan Seçin</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- Tarih -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
            <input
              v-model="formData.date"
              type="date"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            />
          </div>

          <!-- Giriş/Çıkış Saatleri -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Giriş Saati</label>
              <input
                v-model="formData.check_in_time"
                type="time"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Çıkış Saati</label>
              <input
                v-model="formData.check_out_time"
                type="time"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
              />
            </div>
          </div>

          <!-- Durum -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="status in attendanceStatuses"
                :key="status.value"
                type="button"
                @click="formData.status = status.value"
                :class="[
                  'p-3 rounded-lg border text-left transition-colors',
                  formData.status === status.value
                    ? status.activeClass
                    : 'border-gray-200 hover:border-teal-300'
                ]"
              >
                <div class="flex items-center gap-2">
                  <component :is="status.icon" class="h-5 w-5" />
                  <span class="text-sm font-medium">{{ status.label }}</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="2"
              placeholder="Ek notlar..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
            ></textarea>
          </div>

          <!-- Özet Bilgi -->
          <div v-if="formData.check_in_time && formData.check_out_time" class="bg-teal-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-teal-900">Toplam Çalışma Süresi</span>
              <span class="text-lg font-bold text-teal-700">
                {{ calculateWorkHours(formData.check_in_time, formData.check_out_time) }} saat
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
              class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
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
  CalendarDaysIcon,
  ClockIcon,
  UserGroupIcon,
  XCircleIcon,
  CheckCircleIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowRightEndOnRectangleIcon,
  ArrowRightStartOnRectangleIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeAttendanceStore } from '@/stores/employeeattendance'

interface Attendance {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  date: string
  check_in_time?: string
  check_out_time?: string
  work_hours?: number
  status: 'present' | 'absent' | 'late' | 'early_leave' | 'half_day'
  is_late?: boolean
  late_minutes?: number
  is_early_leave?: boolean
  early_leave_minutes?: number
  notes?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeAttendanceStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingAttendance = ref<Attendance | null>(null)
const today = new Date().toISOString().split('T')[0]
const selectedDate = ref(today)

const attendances = ref<Attendance[]>([])
const employees = ref<Employee[]>([])
const todayStatus = ref<any[]>([])

const filters = ref({
  employeeId: '',
  status: ''
})

const stats = ref({
  todayPresent: 0,
  todayAbsent: 0,
  todayLate: 0,
  todayEarlyLeave: 0,
  monthTotal: 0,
  attendanceRate: 0
})

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'present', label: 'Geldi' },
  { value: 'absent', label: 'Gelmedi' },
  { value: 'late', label: 'Geç' },
  { value: 'early_leave', label: 'Erken Çıktı' }
]

const attendanceStatuses = [
  { value: 'present', label: 'Geldi', icon: CheckCircleIcon, activeClass: 'border-green-500 bg-green-50 text-green-700' },
  { value: 'absent', label: 'Gelmedi', icon: XCircleIcon, activeClass: 'border-red-500 bg-red-50 text-red-700' },
  { value: 'late', label: 'Geç Geldi', icon: ClockIcon, activeClass: 'border-yellow-500 bg-yellow-50 text-yellow-700' },
  { value: 'half_day', label: 'Yarım Gün', icon: CalendarDaysIcon, activeClass: 'border-blue-500 bg-blue-50 text-blue-700' }
]

const formData = ref({
  employee_id: '',
  date: today,
  check_in_time: '',
  check_out_time: '',
  status: 'present' as const,
  notes: ''
})

// Computed
const filteredAttendances = computed(() => {
  let result = attendances.value.filter(a => a.date === selectedDate.value)

  if (filters.value.employeeId) {
    result = result.filter(a => a.employee_id === filters.value.employeeId)
  }

  if (filters.value.status) {
    result = result.filter(a => a.status === filters.value.status)
  }

  return result
})

// Helpers
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

const formatDateFull = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
}

const getDayOfWeek = (date: string) => {
  const days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi']
  return days[new Date(date).getDay()]
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    present: 'Geldi',
    absent: 'Gelmedi',
    late: 'Geç Geldi',
    early_leave: 'Erken Çıktı',
    half_day: 'Yarım Gün'
  }
  return labels[status] || status
}

const getStatusBadgeColor = (status: string) => {
  const colors: Record<string, string> = {
    present: 'bg-green-100 text-green-800',
    absent: 'bg-red-100 text-red-800',
    late: 'bg-yellow-100 text-yellow-800',
    early_leave: 'bg-orange-100 text-orange-800',
    half_day: 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const calculateWorkHours = (checkIn: string, checkOut: string) => {
  if (!checkIn || !checkOut) return 0
  const start = checkIn.split(':').map(Number)
  const end = checkOut.split(':').map(Number)
  const startMinutes = start[0] * 60 + start[1]
  const endMinutes = end[0] * 60 + end[1]
  return ((endMinutes - startMinutes) / 60).toFixed(1)
}

// Navigation
const previousDay = () => {
  const date = new Date(selectedDate.value)
  date.setDate(date.getDate() - 1)
  selectedDate.value = date.toISOString().split('T')[0]
}

const nextDay = () => {
  const date = new Date(selectedDate.value)
  date.setDate(date.getDate() + 1)
  selectedDate.value = date.toISOString().split('T')[0]
}

const goToToday = () => {
  selectedDate.value = today
}

// Modal Functions
const openCreateModal = () => {
  editingAttendance.value = null
  formData.value = {
    employee_id: '',
    date: selectedDate.value,
    check_in_time: '',
    check_out_time: '',
    status: 'present',
    notes: ''
  }
  showModal.value = true
}

const editAttendance = (attendance: Attendance) => {
  editingAttendance.value = attendance
  formData.value = {
    employee_id: attendance.employee_id,
    date: attendance.date,
    check_in_time: attendance.check_in_time || '',
    check_out_time: attendance.check_out_time || '',
    status: attendance.status,
    notes: attendance.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingAttendance.value = null
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

    // Devam kayıtlarını yükle
    const response = await store.fetchAll({ date: selectedDate.value })
    attendances.value = response?.data || []

    // Bugünün durumunu hesapla
    updateTodayStatus()
    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateTodayStatus = () => {
  const todayRecords = attendances.value.filter(a => a.date === today)
  
  todayStatus.value = employees.value.map(emp => {
    const record = todayRecords.find(r => r.employee_id === emp.id)
    return {
      ...emp,
      status: record?.status || 'pending',
      check_in: record?.check_in_time,
      check_out: record?.check_out_time
    }
  })
}

const updateStats = () => {
  const todayRecords = attendances.value.filter(a => a.date === today)
  
  stats.value.todayPresent = todayRecords.filter(a => a.status === 'present').length
  stats.value.todayAbsent = todayRecords.filter(a => a.status === 'absent').length
  stats.value.todayLate = todayRecords.filter(a => a.status === 'late' || a.is_late).length
  stats.value.todayEarlyLeave = todayRecords.filter(a => a.is_early_leave).length
  stats.value.monthTotal = attendances.value.length
  
  const totalEmployees = employees.value.length
  const presentToday = stats.value.todayPresent + stats.value.todayLate
  stats.value.attendanceRate = totalEmployees > 0 ? Math.round((presentToday / totalEmployees) * 100) : 0
}

const saveAttendance = async () => {
  saving.value = true
  try {
    const payload = {
      ...formData.value,
      work_hours: formData.value.check_in_time && formData.value.check_out_time
        ? parseFloat(calculateWorkHours(formData.value.check_in_time, formData.value.check_out_time))
        : null
    }

    if (editingAttendance.value) {
      await store.update(editingAttendance.value.id, payload)
    } else {
      await store.create(payload)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kayıt kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const checkIn = async (attendance: Attendance) => {
  const now = new Date()
  const time = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
  
  try {
    await store.update(attendance.id, {
      ...attendance,
      check_in_time: time,
      status: 'present'
    })
    await loadData()
  } catch (error) {
    console.error('Giriş hatası:', error)
  }
}

const checkOut = async (attendance: Attendance) => {
  const now = new Date()
  const time = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
  
  try {
    await store.update(attendance.id, {
      ...attendance,
      check_out_time: time,
      work_hours: parseFloat(calculateWorkHours(attendance.check_in_time || '', time))
    })
    await loadData()
  } catch (error) {
    console.error('Çıkış hatası:', error)
  }
}

const deleteAttendance = async (attendance: Attendance) => {
  if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(attendance.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Kayıt silinemedi')
  }
}

const exportAttendances = () => {
  const csvContent = [
    ['Çalışan', 'Tarih', 'Giriş', 'Çıkış', 'Çalışma Süresi', 'Durum'].join(','),
    ...filteredAttendances.value.map(a => [
      `${a.employee?.first_name} ${a.employee?.last_name}`,
      a.date,
      a.check_in_time || '',
      a.check_out_time || '',
      a.work_hours || '',
      getStatusLabel(a.status)
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `devam_takibi_${selectedDate.value}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>