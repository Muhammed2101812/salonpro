<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çalışan Vardiyaları</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların günlük vardiya planlamasını ve mesai takibini yapın</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportShifts"
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
          Vardiya Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100">
            <CalendarIcon class="h-6 w-6 text-violet-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bu Hafta</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.thisWeek }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Planlandı</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.scheduled }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <CheckCircleIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Onaylandı</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.confirmed }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckBadgeIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Tamamlandı</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.completed }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <ClockIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Saat</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.totalHours }} sa</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler ve Hafta Seçici -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <!-- Hafta Navigasyonu -->
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
            <button
              @click="previousWeek"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
            </button>
            <span class="text-sm font-medium text-gray-700 min-w-[200px] text-center">
              {{ formatWeekRange(currentWeekStart) }}
            </span>
            <button
              @click="nextWeek"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-600" />
            </button>
            <button
              @click="goToToday"
              class="ml-2 px-2 py-1 text-xs font-medium text-violet-600 hover:bg-violet-50 rounded transition-colors"
            >
              Bugün
            </button>
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

        <!-- Görünüm Değiştirici -->
        <div class="flex items-center gap-2">
          <button
            @click="loadData"
            class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <ArrowPathIcon class="h-5 w-5" />
          </button>
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              @click="viewMode = 'calendar'"
              :class="[
                'px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'calendar' ? 'bg-violet-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <CalendarDaysIcon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'list' ? 'bg-violet-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Takvim Görünümü -->
    <div v-if="viewMode === 'calendar'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Takvim Başlıkları -->
      <div class="grid grid-cols-8 bg-gray-50 border-b border-gray-200">
        <div class="px-4 py-3 text-sm font-semibold text-gray-700">Çalışan</div>
        <div
          v-for="day in weekDays"
          :key="day.date"
          class="px-2 py-3 text-center border-l border-gray-200"
          :class="isToday(day.date) ? 'bg-violet-50' : ''"
        >
          <div class="text-xs font-medium text-gray-500">{{ day.label }}</div>
          <div
            :class="[
              'text-lg font-bold',
              isToday(day.date) ? 'text-violet-600' : 'text-gray-900'
            ]"
          >
            {{ formatDayNumber(day.date) }}
          </div>
        </div>
      </div>

      <!-- Yükleniyor -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <!-- Çalışan Satırları -->
      <div v-else>
        <div
          v-for="employee in filteredEmployees"
          :key="employee.id"
          class="grid grid-cols-8 border-b border-gray-100 hover:bg-gray-50 transition-colors"
        >
          <!-- Çalışan Bilgisi -->
          <div class="px-4 py-4 flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white font-medium">
              {{ getInitials(employee.first_name, employee.last_name) }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">
                {{ employee.first_name }} {{ employee.last_name }}
              </p>
              <p class="text-xs text-gray-500">{{ employee.position || 'Çalışan' }}</p>
            </div>
          </div>

          <!-- Gün Hücreleri -->
          <div
            v-for="day in weekDays"
            :key="`${employee.id}-${day.date}`"
            class="px-2 py-3 border-l border-gray-100 min-h-[100px]"
            :class="isToday(day.date) ? 'bg-violet-50/30' : ''"
          >
            <div class="space-y-1">
              <div
                v-for="shift in getShiftsForDay(employee.id, day.date)"
                :key="shift.id"
                @click="editShift(shift)"
                class="px-2 py-1.5 rounded-lg text-xs cursor-pointer transition-all hover:shadow-md"
                :class="getStatusColor(shift.status)"
              >
                <div class="font-semibold">{{ shift.start_time }} - {{ shift.end_time }}</div>
                <div v-if="shift.break_minutes" class="flex items-center mt-0.5 opacity-80">
                  <CoffeeIcon class="h-3 w-3 mr-1" />
                  {{ shift.break_minutes }} dk
                </div>
                <div v-if="shift.overtime_minutes" class="flex items-center mt-0.5 text-orange-700">
                  <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
                  +{{ Math.floor(shift.overtime_minutes / 60) }} sa mesai
                </div>
              </div>

              <!-- Vardiya Ekleme Butonu -->
              <button
                @click="openShiftModal(employee, day.date)"
                class="w-full px-2 py-1 border border-dashed border-gray-300 rounded-lg text-xs text-gray-500 hover:border-violet-500 hover:text-violet-600 transition-colors"
              >
                + Ekle
              </button>
            </div>
          </div>
        </div>

        <!-- Boş Durum -->
        <div v-if="filteredEmployees.length === 0" class="p-12 text-center">
          <UserGroupIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
          <p class="text-gray-500">Çalışan bulunamadı</p>
        </div>
      </div>
    </div>

    <!-- Liste Görünümü -->
    <div v-if="viewMode === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tarih
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Vardiya Saati
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Mola
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Mesai
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
          <tr v-for="shift in filteredShifts" :key="shift.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white font-medium">
                  {{ getInitials(shift.employee?.first_name || 'U', shift.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ shift.employee?.first_name }} {{ shift.employee?.last_name }}
                  </p>
                  <p class="text-xs text-gray-500">{{ shift.employee?.position || 'Çalışan' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(shift.shift_date) }}</div>
              <div class="text-xs text-gray-500">{{ getDayOfWeek(shift.shift_date) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ shift.start_time }} - {{ shift.end_time }}
              </div>
              <div class="text-xs text-gray-500">
                {{ calculateShiftDuration(shift.start_time, shift.end_time) }} saat
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ shift.break_minutes || 0 }} dk
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                v-if="shift.overtime_minutes"
                class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700 rounded-full"
              >
                +{{ Math.floor(shift.overtime_minutes / 60) }} sa
              </span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getStatusBadgeColor(shift.status)
                ]"
              >
                {{ getStatusLabel(shift.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="shift.status === 'scheduled'"
                  @click="confirmShift(shift)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Onayla"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="shift.status === 'confirmed'"
                  @click="completeShift(shift)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Tamamla"
                >
                  <CheckBadgeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editShift(shift)"
                  class="p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="deleteShift(shift)"
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
      <div v-if="filteredShifts.length === 0 && !loading" class="p-12 text-center">
        <CalendarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Bu dönemde vardiya bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-violet-600 hover:text-violet-700 font-medium"
        >
          Vardiya ekleyin
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="filteredShifts.length > 0" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            Toplam {{ filteredShifts.length }} vardiya
          </p>
        </div>
      </div>
    </div>

    <!-- Vardiya Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingShift ? 'Vardiya Düzenle' : 'Yeni Vardiya' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveShift" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              :disabled="editingShift"
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
              v-model="formData.shift_date"
              type="date"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            />
          </div>

          <!-- Saat Aralığı -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç *</label>
              <input
                v-model="formData.start_time"
                type="time"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş *</label>
              <input
                v-model="formData.end_time"
                type="time"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
            </div>
          </div>

          <!-- Mola ve Mesai -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mola Süresi (dk)</label>
              <input
                v-model.number="formData.break_minutes"
                type="number"
                min="0"
                step="5"
                placeholder="60"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mesai (dk)</label>
              <input
                v-model.number="formData.overtime_minutes"
                type="number"
                min="0"
                step="15"
                placeholder="0"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
              />
            </div>
          </div>

          <!-- Durum -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
            <div class="flex gap-2">
              <button
                v-for="status in shiftStatuses"
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

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="2"
              placeholder="Vardiya ile ilgili notlar..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500"
            ></textarea>
          </div>

          <!-- Özet Bilgi -->
          <div class="bg-violet-50 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-violet-700">Toplam Çalışma:</span>
                <span class="font-bold text-violet-900 ml-2">
                  {{ calculateFormDuration() }} saat
                </span>
              </div>
              <div>
                <span class="text-violet-700">Net Çalışma:</span>
                <span class="font-bold text-violet-900 ml-2">
                  {{ calculateNetDuration() }} saat
                </span>
              </div>
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
  CalendarIcon,
  CalendarDaysIcon,
  ClockIcon,
  CheckCircleIcon,
  CheckBadgeIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ListBulletIcon,
  UserGroupIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeShiftStore } from '@/stores/employeeshift'

// Coffee icon placeholder
const CoffeeIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513c0 1.135.845 2.098 1.976 2.192 1.327.11 2.669.166 4.024.166 1.355 0 2.697-.056 4.024-.166 1.131-.094 1.976-1.057 1.976-2.192v-2.513c0-1.135-.845-2.098-1.976-2.192A48.424 48.424 0 0012 8.25zm0 0V6.75m0 0c.621 0 1.125-.504 1.125-1.125S12.621 4.5 12 4.5s-1.125.504-1.125 1.125S11.379 6.75 12 6.75zm-3.75 9v1.875c0 .621.504 1.125 1.125 1.125h5.25c.621 0 1.125-.504 1.125-1.125V15.75" /></svg>`
}

interface Shift {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  shift_date: string
  start_time: string
  end_time: string
  break_minutes: number
  overtime_minutes: number
  status: 'scheduled' | 'confirmed' | 'completed' | 'cancelled'
  notes?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeShiftStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingShift = ref<Shift | null>(null)
const viewMode = ref<'calendar' | 'list'>('calendar')
const currentWeekStart = ref(getMonday(new Date()))

const shifts = ref<Shift[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: '',
  status: ''
})

const stats = ref({
  thisWeek: 0,
  scheduled: 0,
  confirmed: 0,
  completed: 0,
  totalHours: 0
})

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'scheduled', label: 'Planlandı' },
  { value: 'confirmed', label: 'Onaylandı' },
  { value: 'completed', label: 'Tamamlandı' },
  { value: 'cancelled', label: 'İptal' }
]

const shiftStatuses = [
  { value: 'scheduled', label: 'Planlandı', activeClass: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
  { value: 'confirmed', label: 'Onaylandı', activeClass: 'bg-blue-100 text-blue-800 border-blue-300' },
  { value: 'completed', label: 'Tamamlandı', activeClass: 'bg-green-100 text-green-800 border-green-300' },
  { value: 'cancelled', label: 'İptal', activeClass: 'bg-red-100 text-red-800 border-red-300' }
]

const formData = ref({
  employee_id: '',
  shift_date: '',
  start_time: '09:00',
  end_time: '18:00',
  break_minutes: 60,
  overtime_minutes: 0,
  status: 'scheduled' as const,
  notes: ''
})

// Computed
const weekDays = computed(() => {
  const days = []
  const labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
  for (let i = 0; i < 7; i++) {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() + i)
    days.push({
      date: date.toISOString().split('T')[0],
      label: labels[i]
    })
  }
  return days
})

const filteredEmployees = computed(() => {
  if (!filters.value.employeeId) return employees.value
  return employees.value.filter(emp => emp.id === filters.value.employeeId)
})

const filteredShifts = computed(() => {
  let result = shifts.value

  if (filters.value.employeeId) {
    result = result.filter(s => s.employee_id === filters.value.employeeId)
  }

  if (filters.value.status) {
    result = result.filter(s => s.status === filters.value.status)
  }

  return result.sort((a, b) => {
    const dateA = new Date(`${a.shift_date}T${a.start_time}`)
    const dateB = new Date(`${b.shift_date}T${b.start_time}`)
    return dateA.getTime() - dateB.getTime()
  })
})

// Helpers
function getMonday(date: Date): Date {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1)
  return new Date(d.setDate(diff))
}

const previousWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() - 7)
  currentWeekStart.value = newDate
  loadShifts()
}

const nextWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() + 7)
  currentWeekStart.value = newDate
  loadShifts()
}

const goToToday = () => {
  currentWeekStart.value = getMonday(new Date())
  loadShifts()
}

const isToday = (dateStr: string) => {
  return dateStr === new Date().toISOString().split('T')[0]
}

const formatWeekRange = (startDate: Date) => {
  const endDate = new Date(startDate)
  endDate.setDate(endDate.getDate() + 6)
  return `${startDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long' })} - ${endDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })}`
}

const formatDayNumber = (date: string) => {
  return new Date(date).getDate()
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

const getDayOfWeek = (date: string) => {
  const days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi']
  return days[new Date(date).getDay()]
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getShiftsForDay = (employeeId: string, date: string) => {
  return shifts.value.filter(s => s.employee_id === employeeId && s.shift_date === date)
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    scheduled: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 hover:bg-blue-200',
    completed: 'bg-green-100 text-green-800 hover:bg-green-200',
    cancelled: 'bg-red-100 text-red-800 hover:bg-red-200'
  }
  return colors[status] || 'bg-gray-100 text-gray-800 hover:bg-gray-200'
}

const getStatusBadgeColor = (status: string) => {
  const colors: Record<string, string> = {
    scheduled: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    scheduled: 'Planlandı',
    confirmed: 'Onaylandı',
    completed: 'Tamamlandı',
    cancelled: 'İptal'
  }
  return labels[status] || status
}

const calculateShiftDuration = (start: string, end: string) => {
  const startParts = start.split(':').map(Number)
  const endParts = end.split(':').map(Number)
  const startMinutes = startParts[0] * 60 + startParts[1]
  const endMinutes = endParts[0] * 60 + endParts[1]
  return ((endMinutes - startMinutes) / 60).toFixed(1)
}

const calculateFormDuration = () => {
  if (!formData.value.start_time || !formData.value.end_time) return '0'
  return calculateShiftDuration(formData.value.start_time, formData.value.end_time)
}

const calculateNetDuration = () => {
  const total = parseFloat(calculateFormDuration())
  const breakHours = (formData.value.break_minutes || 0) / 60
  return Math.max(0, total - breakHours).toFixed(1)
}

// Modal Functions
const openCreateModal = () => {
  editingShift.value = null
  formData.value = {
    employee_id: '',
    shift_date: new Date().toISOString().split('T')[0],
    start_time: '09:00',
    end_time: '18:00',
    break_minutes: 60,
    overtime_minutes: 0,
    status: 'scheduled',
    notes: ''
  }
  showModal.value = true
}

const openShiftModal = (employee: Employee, date: string) => {
  editingShift.value = null
  formData.value = {
    employee_id: employee.id,
    shift_date: date,
    start_time: '09:00',
    end_time: '18:00',
    break_minutes: 60,
    overtime_minutes: 0,
    status: 'scheduled',
    notes: ''
  }
  showModal.value = true
}

const editShift = (shift: Shift) => {
  editingShift.value = shift
  formData.value = {
    employee_id: shift.employee_id,
    shift_date: shift.shift_date,
    start_time: shift.start_time,
    end_time: shift.end_time,
    break_minutes: shift.break_minutes || 0,
    overtime_minutes: shift.overtime_minutes || 0,
    status: shift.status,
    notes: shift.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingShift.value = null
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

    await loadShifts()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const loadShifts = async () => {
  try {
    const endDate = new Date(currentWeekStart.value)
    endDate.setDate(endDate.getDate() + 6)

    const response = await store.fetchAll({
      start_date: currentWeekStart.value.toISOString().split('T')[0],
      end_date: endDate.toISOString().split('T')[0]
    })
    shifts.value = response?.data || []

    // İstatistikleri hesapla
    stats.value.thisWeek = shifts.value.length
    stats.value.scheduled = shifts.value.filter(s => s.status === 'scheduled').length
    stats.value.confirmed = shifts.value.filter(s => s.status === 'confirmed').length
    stats.value.completed = shifts.value.filter(s => s.status === 'completed').length
    stats.value.totalHours = shifts.value.reduce((sum, s) => {
      return sum + parseFloat(calculateShiftDuration(s.start_time, s.end_time))
    }, 0)
  } catch (error) {
    console.error('Vardiya yükleme hatası:', error)
  }
}

const saveShift = async () => {
  saving.value = true
  try {
    if (editingShift.value) {
      await store.update(editingShift.value.id, formData.value)
    } else {
      await store.create(formData.value)
    }
    closeModal()
    await loadShifts()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Vardiya kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const confirmShift = async (shift: Shift) => {
  try {
    await store.update(shift.id, { ...shift, status: 'confirmed' })
    await loadShifts()
  } catch (error) {
    console.error('Onaylama hatası:', error)
  }
}

const completeShift = async (shift: Shift) => {
  try {
    await store.update(shift.id, { ...shift, status: 'completed' })
    await loadShifts()
  } catch (error) {
    console.error('Tamamlama hatası:', error)
  }
}

const deleteShift = async (shift: Shift) => {
  if (!confirm('Bu vardiyayı silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(shift.id)
    await loadShifts()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Vardiya silinemedi')
  }
}

const exportShifts = () => {
  // CSV export işlevi
  const csvContent = [
    ['Çalışan', 'Tarih', 'Başlangıç', 'Bitiş', 'Mola (dk)', 'Mesai (dk)', 'Durum'].join(','),
    ...filteredShifts.value.map(s => [
      `${s.employee?.first_name} ${s.employee?.last_name}`,
      s.shift_date,
      s.start_time,
      s.end_time,
      s.break_minutes,
      s.overtime_minutes,
      getStatusLabel(s.status)
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `vardiyalar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>