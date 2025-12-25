<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Randevular</h1>
        <p class="mt-2 text-sm text-gray-600">Randevularınızı yönetin ve takip edin</p>
      </div>
      <Button variant="primary" @click="openCreateModal" :icon="PlusIcon" label="Yeni Randevu" />
    </div>

    <!-- Stats -->
    <AppointmentStats :stats="stats" />

    <!-- Controls -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
          <!-- View Switcher -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden bg-white">
            <button
              @click="viewMode = 'calendar'"
              :class="[
                'flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'calendar' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <CalendarIcon class="h-4 w-4" />
              Ay
            </button>
            <button
              @click="viewMode = 'week'"
              :class="[
                'flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'week' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <CalendarIcon class="h-4 w-4" />
              Hafta
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors',
                viewMode === 'list' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              <ListBulletIcon class="h-4 w-4" />
              Liste
            </button>
          </div>

          <!-- Calendar Navigation -->
          <div v-if="viewMode === 'calendar' || viewMode === 'week'" class="flex items-center gap-2">
            <Button variant="ghost" size="sm" @click="previousPeriod" :icon="ChevronLeftIcon" />
            <span class="text-lg font-semibold text-gray-900 min-w-[180px] text-center">{{ currentPeriodLabel }}</span>
            <Button variant="ghost" size="sm" @click="nextPeriod" :icon="ChevronRightIcon" />
            <Button variant="secondary" size="sm" @click="goToToday" label="Bugün" class="ml-2" />
          </div>

          <!-- List Filters -->
          <div v-if="viewMode === 'list'" class="w-full lg:w-auto">
             <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                <button
                  v-for="status in statusOptions"
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
        </div>

        <!-- Search (List Only) -->
        <div v-if="viewMode === 'list'" class="w-full lg:w-64">
             <Input v-model="searchQuery" placeholder="Müşteri veya çalışan ara...">
                 <template #prefix>
                     <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                 </template>
             </Input>
        </div>
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="loading && appointments.length === 0" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Calendar View (Monthly) -->
    <AppointmentCalendar
        v-if="viewMode === 'calendar'"
        :appointments="enrichedAppointments"
        :current-date="currentDate"
        @date-click="createAppointmentOnDate"
        @edit-appointment="openEditModal"
        @drop-appointment="handleDrop"
    />

    <!-- Week View -->
    <AppointmentWeekView
        v-else-if="viewMode === 'week'"
        :appointments="enrichedAppointments"
        :current-date="currentDate"
        @slot-click="createAppointmentOnSlot"
        @edit-appointment="openEditModal"
        @drop-appointment="handleDrop"
    />

    <!-- List View -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredAppointments"
        :exportable="true"
        export-filename="randevular"
        export-title="Randevu Listesi"
    >
        <template #cell-date="{ row }">
            <div class="flex items-center gap-3">
                <div :class="['w-1.5 h-10 rounded-full', getStatusColor(row.status)]"></div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(row.appointment_date) }}</p>
                  <p class="text-xs text-gray-500">{{ row.duration_minutes }} dk</p>
                </div>
            </div>
        </template>
        <template #cell-customer="{ row }">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ row.customer_name }}</p>
              <p class="text-xs text-gray-500">{{ row.customer_phone }}</p>
            </div>
        </template>
        <template #cell-price="{ row }">
            {{ formatCurrency(row.price) }}
        </template>
        <template #cell-status="{ row }">
            <select
                :value="row.status"
                @change="updateStatus(row.id, ($event.target as HTMLSelectElement).value)"
                :class="['text-xs rounded-lg font-medium px-2 py-1 border-0 cursor-pointer focus:ring-2 ring-primary/20', getStatusBg(row.status)]"
                @click.stop
            >
                <option value="pending">Bekliyor</option>
                <option value="confirmed">Onaylandı</option>
                <option value="completed">Tamamlandı</option>
                <option value="cancelled">İptal Edildi</option>
            </select>
        </template>
        <template #actions="{ row }">
             <div class="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" @click="openEditModal(row)">
                   <PencilIcon class="h-4 w-4 text-primary" />
                </Button>
                <Button variant="ghost" size="sm" @click="handleDelete(row.id)">
                   <TrashIcon class="h-4 w-4 text-danger" />
                </Button>
             </div>
        </template>
    </DataTable>

    <!-- Modal -->
    <AppointmentModal
        v-model="showModal"
        :is-edit="isEdit"
        :initial-data="modalData"
        :loading="saving"
        @submit="handleModalSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import {
  PlusIcon,
  CalendarIcon,
  ListBulletIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import AppointmentStats from '@/components/appointment/AppointmentStats.vue'
import AppointmentCalendar from '@/components/appointment/AppointmentCalendar.vue'
import AppointmentWeekView from '@/components/appointment/AppointmentWeekView.vue'
import AppointmentModal from '@/components/appointment/AppointmentModal.vue'

import { useAppointmentStore } from '@/stores/appointment'
import { useCustomerStore } from '@/stores/customer'
import { useEmployeeStore } from '@/stores/employee'
import { useServiceStore } from '@/stores/service'
import { useBranchStore } from '@/stores/branch'

interface Appointment {
  id: string
  branch_id: string
  customer_id: string
  employee_id: string
  service_id: string
  appointment_date: string
  duration_minutes: number
  price: number
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed'
  notes?: string
  [key: string]: any
}

const appointmentStore = useAppointmentStore()
const customerStore = useCustomerStore()
const employeeStore = useEmployeeStore()
const serviceStore = useServiceStore()
const branchStore = useBranchStore()

// State
const loading = ref(true)
const saving = ref(false)
const viewMode = ref<'calendar' | 'week' | 'list'>('calendar')
const currentDate = ref(new Date())
const showModal = ref(false)
const isEdit = ref(false)
const modalData = ref<any>(null)
const editingId = ref<string | null>(null)
const searchQuery = ref('')
const filters = ref({ status: '' })

const stats = ref({
  today: 0,
  pending: 0,
  confirmed: 0,
  completed: 0,
  todayRevenue: 0
})

const tableColumns = [
    { key: 'date', label: 'Tarih/Saat' },
    { key: 'customer', label: 'Müşteri' },
    { key: 'employee_name', label: 'Çalışan' },
    { key: 'service_name', label: 'Hizmet' },
    { key: 'price', label: 'Fiyat' },
    { key: 'status', label: 'Durum' },
]

const statusOptions = [
  { value: 'pending', label: 'Bekliyor', activeClass: 'bg-yellow-600 text-white' },
  { value: 'confirmed', label: 'Onaylı', activeClass: 'bg-blue-600 text-white' },
  { value: 'completed', label: 'Tamam', activeClass: 'bg-green-600 text-white' },
  { value: 'cancelled', label: 'İptal', activeClass: 'bg-red-600 text-white' }
]

// Computed
const appointments = computed(() => appointmentStore.appointments as Appointment[])

// Enriched with helper names for Calendar/Table presentation
const enrichedAppointments = computed(() => {
    return appointments.value.map(a => {
        const c = customerStore.customers.find((x: any) => x.id === a.customer_id)
        const e = employeeStore.employees.find((x: any) => x.id === a.employee_id)
        const s = serviceStore.services.find((x: any) => x.id === a.service_id)
        return {
            ...a,
            customer_name: c ? `${c.first_name} ${c.last_name}` : '-',
            customer_phone: c?.phone || '',
            employee_name: e ? `${e.first_name} ${e.last_name}` : '-',
            service_name: s?.name || '-'
        }
    })
})

const filteredAppointments = computed(() => {
  let result = enrichedAppointments.value

  if (filters.value.status) {
    result = result.filter(a => a.status === filters.value.status)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(a => {
      return (
          a.customer_name?.toLowerCase().includes(query) ||
          a.employee_name?.toLowerCase().includes(query)
      )
    })
  }

  // List view sort: newest first
  return result.sort((a, b) => new Date(b.appointment_date).getTime() - new Date(a.appointment_date).getTime())
})

const currentMonthYear = computed(() => {
  return new Intl.DateTimeFormat('tr-TR', { month: 'long', year: 'numeric' }).format(currentDate.value)
})

// Methods
const formatDate = (dateString: string) => {
    const d = new Date(dateString)
    return d.toLocaleString('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
}
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)

const getStatusColor = (status: string) => ({ pending: 'bg-yellow-500', confirmed: 'bg-blue-500', completed: 'bg-green-500', cancelled: 'bg-red-500' }[status] || 'bg-gray-500')
const getStatusBg = (status: string) => ({ pending: 'bg-yellow-100 text-yellow-800', confirmed: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100')

const updateStats = () => {
    const today = new Date().toDateString()
    const todayAppts = appointments.value.filter(a => new Date(a.appointment_date).toDateString() === today)
    
    stats.value.today = todayAppts.length
    stats.value.pending = appointments.value.filter(a => a.status === 'pending').length
    stats.value.confirmed = appointments.value.filter(a => a.status === 'confirmed').length
    stats.value.completed = appointments.value.filter(a => a.status === 'completed').length
    stats.value.todayRevenue = todayAppts.filter(a => a.status === 'completed').reduce((acc, a) => acc + Number(a.price), 0)
}

const previousMonth = () => { currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1) }
const nextMonth = () => { currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1) }
const previousWeek = () => { currentDate.value = new Date(currentDate.value.getTime() - 7 * 24 * 60 * 60 * 1000) }
const nextWeek = () => { currentDate.value = new Date(currentDate.value.getTime() + 7 * 24 * 60 * 60 * 1000) }
const goToToday = () => { currentDate.value = new Date() }

// Dynamic period navigation
const previousPeriod = () => viewMode.value === 'week' ? previousWeek() : previousMonth()
const nextPeriod = () => viewMode.value === 'week' ? nextWeek() : nextMonth()

// Dynamic period label
const currentPeriodLabel = computed(() => {
  if (viewMode.value === 'week') {
    const d = currentDate.value
    const day = d.getDay()
    const diff = d.getDate() - day + (day === 0 ? -6 : 1) // Monday start
    const startOfWeek = new Date(d.getFullYear(), d.getMonth(), diff)
    const endOfWeek = new Date(startOfWeek.getTime() + 6 * 24 * 60 * 60 * 1000)
    const monthFormat = new Intl.DateTimeFormat('tr-TR', { month: 'short' })
    return `${startOfWeek.getDate()} ${monthFormat.format(startOfWeek)} - ${endOfWeek.getDate()} ${monthFormat.format(endOfWeek)}`
  }
  return new Intl.DateTimeFormat('tr-TR', { month: 'long', year: 'numeric' }).format(currentDate.value)
})

// Actions
const openCreateModal = () => {
    modalData.value = null
    isEdit.value = false
    editingId.value = null
    showModal.value = true
}

const createAppointmentOnDate = (dateIso: string) => {
     // Default to 10:00 on that day
     const d = new Date(dateIso)
     d.setHours(10, 0, 0, 0)
     modalData.value = { appointment_date: d.toISOString() }
     isEdit.value = false
     editingId.value = null
     showModal.value = true
}

const createAppointmentOnSlot = (dateStr: string, hour: number) => {
     const d = new Date(dateStr)
     d.setHours(hour, 0, 0, 0)
     modalData.value = { appointment_date: d.toISOString() }
     isEdit.value = false
     editingId.value = null
     showModal.value = true
}

const openEditModal = (appointment: any) => {
    modalData.value = { ...appointment }
    isEdit.value = true
    editingId.value = appointment.id
    showModal.value = true
}

const handleModalSubmit = async (data: any) => {
    saving.value = true
    try {
        if (isEdit.value && editingId.value) {
            await appointmentStore.updateAppointment(editingId.value, data)
        } else {
            await appointmentStore.createAppointment(data)
        }
        showModal.value = false
        updateStats()
    } catch (e) {
        console.error('Randevu hatası:', e)
        alert('İşlem başarısız.')
    } finally {
        saving.value = false
    }
}

const updateStatus = async (id: string, status: any) => {
    try {
        const a = appointments.value.find(x => x.id === id)
        if (a) {
             await appointmentStore.updateAppointment(id, { ...a, status })
             updateStats()
        }
    } catch(e) { console.error(e) }
}

const handleDelete = async (id: string) => {
    if (!confirm('Silmek istediğinizden emin misiniz?')) return
    try {
        await appointmentStore.deleteAppointment(id)
        updateStats()
    } catch(e) { console.error(e) }
}

const handleDrop = async (appointment: any, dateIso: string) => {
    // Only update date/time, keep duration/customer/etc
    const oldDate = new Date(appointment.appointment_date)
    const newDate = new Date(dateIso)
    // Preserve time
    newDate.setHours(oldDate.getHours(), oldDate.getMinutes())
    
    try {
        await appointmentStore.updateAppointment(appointment.id, { ...appointment, appointment_date: newDate.toISOString() })
    } catch (e) {
        console.error('Taşıma hatası:', e)
        alert('Randevu taşınamadı.')
    }
}

onMounted(async () => {
    loading.value = true
    await Promise.all([
        appointmentStore.fetchAppointments(),
        branchStore.fetchBranches(),
        customerStore.fetchCustomers(),
        employeeStore.fetchEmployees(),
        serviceStore.fetchServices()
    ])
    updateStats()
    loading.value = false
})
</script>


