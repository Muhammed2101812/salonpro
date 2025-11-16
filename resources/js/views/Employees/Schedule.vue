<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Çalışan Vardiya Yönetimi</h1>
        <p class="mt-1 text-sm text-gray-600">Çalışanların haftalık vardiya planlamasını yapın</p>
      </div>
      <button
        @click="showShiftModal = true"
        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition"
      >
        Vardiya Ekle
      </button>
    </div>

    <!-- Week Navigator -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4 flex items-center justify-between">
      <button
        @click="previousWeek"
        class="p-2 hover:bg-gray-100 rounded-md transition"
      >
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="text-center">
        <div class="text-lg font-semibold text-gray-900">
          {{ formatWeekRange(currentWeekStart) }}
        </div>
        <button
          @click="goToToday"
          class="mt-1 text-sm text-blue-600 hover:text-blue-700"
        >
          Bugüne Git
        </button>
      </div>

      <button
        @click="nextWeek"
        class="p-2 hover:bg-gray-100 rounded-md transition"
      >
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Schedule Grid -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <!-- Days Header -->
      <div class="grid grid-cols-8 border-b border-gray-200">
        <div class="px-4 py-3 text-sm font-medium text-gray-500 bg-gray-50">Çalışan</div>
        <div
          v-for="day in weekDays"
          :key="day.date"
          class="px-4 py-3 text-center border-l border-gray-200"
          :class="isToday(day.date) ? 'bg-blue-50' : 'bg-gray-50'"
        >
          <div class="text-xs font-medium text-gray-500">{{ day.label }}</div>
          <div class="text-sm font-semibold" :class="isToday(day.date) ? 'text-blue-600' : 'text-gray-900'">
            {{ formatDayNumber(day.date) }}
          </div>
        </div>
      </div>

      <!-- Employee Rows -->
      <div v-if="loading" class="p-12 text-center text-gray-500">
        Yükleniyor...
      </div>

      <div v-else>
        <div
          v-for="employee in employees"
          :key="employee.id"
          class="grid grid-cols-8 border-b border-gray-200 hover:bg-gray-50 transition"
        >
          <!-- Employee Name -->
          <div class="px-4 py-4 flex items-center">
            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
              {{ getInitials(employee.first_name, employee.last_name) }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">
                {{ employee.first_name }} {{ employee.last_name }}
              </p>
            </div>
          </div>

          <!-- Day Cells -->
          <div
            v-for="day in weekDays"
            :key="`${employee.id}-${day.date}`"
            class="px-2 py-4 border-l border-gray-200 min-h-[80px]"
            :class="isToday(day.date) ? 'bg-blue-50/30' : ''"
          >
            <div class="space-y-1">
              <div
                v-for="shift in getShiftsForDay(employee.id, day.date)"
                :key="shift.id"
                @click="editShift(shift)"
                class="px-2 py-1 rounded text-xs cursor-pointer transition"
                :class="getShiftColor(shift.status)"
              >
                <div class="font-medium">{{ shift.start_time }} - {{ shift.end_time }}</div>
                <div v-if="shift.break_minutes" class="text-xs opacity-75">
                  Mola: {{ shift.break_minutes }}dk
                </div>
              </div>

              <!-- Add Shift Button -->
              <button
                @click="openShiftModal(employee, day.date)"
                class="w-full px-2 py-1 border border-dashed border-gray-300 rounded text-xs text-gray-500 hover:border-blue-500 hover:text-blue-500 transition"
              >
                + Ekle
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="employees.length === 0" class="p-12 text-center text-gray-500">
          Çalışan bulunamadı
        </div>
      </div>
    </div>

    <!-- Shift Modal -->
    <div v-if="showShiftModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
          {{ isEditingShift ? 'Vardiya Düzenle' : 'Yeni Vardiya' }}
        </h2>

        <form @submit.prevent="saveShift" class="space-y-4">
          <!-- Employee Select -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan</label>
            <select
              v-model="shiftForm.employee_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              :disabled="isEditingShift"
            >
              <option value="">Seçiniz</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tarih</label>
            <input
              v-model="shiftForm.shift_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Time Range -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label>
              <input
                v-model="shiftForm.start_time"
                type="time"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label>
              <input
                v-model="shiftForm.end_time"
                type="time"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Break Minutes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mola (dakika)</label>
            <input
              v-model.number="shiftForm.break_minutes"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
            <select
              v-model="shiftForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="scheduled">Planlandı</option>
              <option value="confirmed">Onaylandı</option>
              <option value="completed">Tamamlandı</option>
              <option value="cancelled">İptal Edildi</option>
            </select>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="shiftForm.notes"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 pt-4">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition"
            >
              Kaydet
            </button>
            <button
              v-if="isEditingShift"
              type="button"
              @click="deleteShift"
              class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md font-medium transition"
            >
              Sil
            </button>
            <button
              type="button"
              @click="closeShiftModal"
              class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium transition"
            >
              İptal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const employees = ref<any[]>([])
const shifts = ref<any[]>([])
const currentWeekStart = ref(getMonday(new Date()))
const showShiftModal = ref(false)
const isEditingShift = ref(false)
const currentShift = ref<any>(null)

const shiftForm = ref({
  employee_id: '',
  shift_date: '',
  start_time: '09:00',
  end_time: '17:00',
  break_minutes: 60,
  status: 'scheduled',
  notes: '',
})

// Get Monday of current week
function getMonday(date: Date): Date {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1)
  return new Date(d.setDate(diff))
}

// Week days (Monday to Sunday)
const weekDays = computed(() => {
  const days = []
  const labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
  for (let i = 0; i < 7; i++) {
    const date = new Date(currentWeekStart.value)
    date.setDate(date.getDate() + i)
    days.push({
      date: date.toISOString().split('T')[0],
      label: labels[i],
    })
  }
  return days
})

// Navigation
const previousWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() - 7)
  currentWeekStart.value = newDate
  fetchShifts()
}

const nextWeek = () => {
  const newDate = new Date(currentWeekStart.value)
  newDate.setDate(newDate.getDate() + 7)
  currentWeekStart.value = newDate
  fetchShifts()
}

const goToToday = () => {
  currentWeekStart.value = getMonday(new Date())
  fetchShifts()
}

// Helpers
const isToday = (date: string) => {
  return date === new Date().toISOString().split('T')[0]
}

const formatWeekRange = (startDate: Date) => {
  const endDate = new Date(startDate)
  endDate.setDate(endDate.getDate() + 6)
  return `${startDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long' })} - ${endDate.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })}`
}

const formatDayNumber = (date: string) => {
  return new Date(date).getDate()
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
}

const getShiftsForDay = (employeeId: string, date: string) => {
  return shifts.value.filter(s => s.employee_id === employeeId && s.shift_date === date)
}

const getShiftColor = (status: string) => {
  const colors: Record<string, string> = {
    scheduled: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 hover:bg-blue-200',
    completed: 'bg-green-100 text-green-800 hover:bg-green-200',
    cancelled: 'bg-red-100 text-red-800 hover:bg-red-200',
  }
  return colors[status] || 'bg-gray-100 text-gray-800 hover:bg-gray-200'
}

// Modal actions
const openShiftModal = (employee: any, date: string) => {
  isEditingShift.value = false
  shiftForm.value = {
    employee_id: employee.id,
    shift_date: date,
    start_time: '09:00',
    end_time: '17:00',
    break_minutes: 60,
    status: 'scheduled',
    notes: '',
  }
  showShiftModal.value = true
}

const editShift = (shift: any) => {
  isEditingShift.value = true
  currentShift.value = shift
  shiftForm.value = { ...shift }
  showShiftModal.value = true
}

const closeShiftModal = () => {
  showShiftModal.value = false
  isEditingShift.value = false
  currentShift.value = null
}

// CRUD operations
const fetchEmployees = async () => {
  try {
    const response: any = await api.get('/employees')
    employees.value = response.data
  } catch (error) {
    console.error('Failed to fetch employees:', error)
  }
}

const fetchShifts = async () => {
  loading.value = true
  try {
    const endDate = new Date(currentWeekStart.value)
    endDate.setDate(endDate.getDate() + 6)

    const response: any = await api.get('/employee-shifts', {
      params: {
        start_date: currentWeekStart.value.toISOString().split('T')[0],
        end_date: endDate.toISOString().split('T')[0],
      }
    })
    shifts.value = response.data
  } catch (error) {
    console.error('Failed to fetch shifts:', error)
  } finally {
    loading.value = false
  }
}

const saveShift = async () => {
  try {
    if (isEditingShift.value) {
      const response: any = await api.put(`/employee-shifts/${currentShift.value.id}`, shiftForm.value)
      const index = shifts.value.findIndex(s => s.id === currentShift.value.id)
      if (index !== -1) shifts.value[index] = response.data
    } else {
      const response: any = await api.post('/employee-shifts', shiftForm.value)
      shifts.value.push(response.data)
    }
    closeShiftModal()
  } catch (error) {
    console.error('Failed to save shift:', error)
    alert('Vardiya kaydedilemedi')
  }
}

const deleteShift = async () => {
  if (!confirm('Bu vardiyayı silmek istediğinizden emin misiniz?')) return

  try {
    await api.delete(`/employee-shifts/${currentShift.value.id}`)
    shifts.value = shifts.value.filter(s => s.id !== currentShift.value.id)
    closeShiftModal()
  } catch (error) {
    console.error('Failed to delete shift:', error)
    alert('Vardiya silinemedi')
  }
}

onMounted(async () => {
  await fetchEmployees()
  await fetchShifts()
})
</script>
