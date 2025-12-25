<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Time column header -->
    <div class="grid grid-cols-8 bg-gray-50 border-b">
      <!-- Empty cell for time column -->
      <div class="px-2 py-3 text-center"></div>
      <!-- Day headers -->
      <div 
        v-for="day in weekDays" 
        :key="day.date"
        :class="[
          'px-2 py-3 text-center border-l',
          day.isToday ? 'bg-primary-50' : ''
        ]"
      >
        <div class="text-xs font-medium text-gray-500 uppercase">{{ day.dayName }}</div>
        <div :class="[
          'text-lg font-bold mt-1',
          day.isToday ? 'text-primary-600' : 'text-gray-900'
        ]">{{ day.dayNumber }}</div>
      </div>
    </div>

    <!-- Time grid -->
    <div class="relative overflow-auto" style="max-height: 600px;">
      <div class="grid grid-cols-8">
        <!-- Time labels -->
        <div class="divide-y divide-gray-100">
          <div 
            v-for="hour in hours" 
            :key="hour" 
            class="h-16 px-2 py-1 text-right text-xs text-gray-500 font-medium border-r bg-gray-50"
          >
            {{ formatHour(hour) }}
          </div>
        </div>

        <!-- Day columns -->
        <div 
          v-for="day in weekDays" 
          :key="day.date"
          class="relative border-l divide-y divide-gray-100"
          @drop="handleDrop($event, day.date)"
          @dragover="handleDragOver($event, day.date)"
          @dragleave="handleDragLeave"
        >
          <!-- Hour slots -->
          <div 
            v-for="hour in hours" 
            :key="hour" 
            :class="[
              'h-16 relative cursor-pointer hover:bg-gray-50 transition',
              dragOverSlot === `${day.date}-${hour}` ? 'bg-primary-100' : ''
            ]"
            @click="$emit('slot-click', day.date, hour)"
          >
          </div>

          <!-- Appointments overlay -->
          <div 
            v-for="appointment in getAppointmentsForDay(day.date)"
            :key="appointment.id"
            draggable="true"
            @dragstart="handleDragStart($event, appointment)"
            @dragend="handleDragEnd"
            @click.stop="$emit('edit-appointment', appointment)"
            :style="getAppointmentStyle(appointment)"
            :class="[
              'absolute left-1 right-1 rounded-lg px-2 py-1 cursor-move shadow-sm border text-xs overflow-hidden hover:z-20',
              getStatusClass(appointment.status),
              draggingId === appointment.id ? 'opacity-50' : ''
            ]"
          >
            <div class="font-semibold truncate">{{ formatTime(appointment.appointment_date) }}</div>
            <div class="truncate opacity-90">{{ appointment.customer_name }}</div>
            <div class="truncate text-[10px] opacity-75">{{ appointment.service_name }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Appointment {
  id: string
  appointment_date: string
  duration_minutes: number
  status: string
  customer_name?: string
  service_name?: string
  [key: string]: any
}

interface WeekDay {
  date: string
  dayName: string
  dayNumber: number
  isToday: boolean
}

const props = defineProps<{
  appointments: Appointment[]
  currentDate: Date
}>()

const emit = defineEmits<{
  'slot-click': [date: string, hour: number]
  'edit-appointment': [appointment: Appointment]
  'drop-appointment': [appointment: Appointment, newDate: string]
}>()

// State
const dragOverSlot = ref<string | null>(null)
const draggingId = ref<string | null>(null)
const draggingAppointment = ref<Appointment | null>(null)

// Hours (8:00 - 20:00)
const hours = Array.from({ length: 13 }, (_, i) => i + 8)

// Week days computed
const weekDays = computed<WeekDay[]>(() => {
  const days: WeekDay[] = []
  const today = new Date()
  const startOfWeek = getStartOfWeek(props.currentDate)

  for (let i = 0; i < 7; i++) {
    const date = new Date(startOfWeek)
    date.setDate(startOfWeek.getDate() + i)
    days.push({
      date: date.toISOString().split('T')[0],
      dayName: date.toLocaleDateString('tr-TR', { weekday: 'short' }),
      dayNumber: date.getDate(),
      isToday: date.toDateString() === today.toDateString()
    })
  }
  return days
})

// Helpers
const getStartOfWeek = (date: Date): Date => {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1) // Monday start
  d.setDate(diff)
  return d
}

const formatHour = (hour: number) => `${hour.toString().padStart(2, '0')}:00`

const formatTime = (dateString: string) => {
  return new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateString))
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-50 text-yellow-800 border-yellow-200',
    confirmed: 'bg-blue-50 text-blue-800 border-blue-200',
    completed: 'bg-green-50 text-green-800 border-green-200',
    cancelled: 'bg-red-50 text-red-800 border-red-200'
  }
  return classes[status] || 'bg-gray-50 text-gray-800 border-gray-200'
}

const getAppointmentsForDay = (dateStr: string) => {
  return props.appointments.filter(a => {
    const aDate = new Date(a.appointment_date).toISOString().split('T')[0]
    return aDate === dateStr
  }).sort((a, b) => new Date(a.appointment_date).getTime() - new Date(b.appointment_date).getTime())
}

const getAppointmentStyle = (appointment: Appointment) => {
  const startDate = new Date(appointment.appointment_date)
  const startHour = startDate.getHours()
  const startMinute = startDate.getMinutes()
  const duration = appointment.duration_minutes || 30

  // Calculate top position (relative to 8:00)
  const topPixels = ((startHour - 8) * 64) + (startMinute / 60 * 64) // 64px per hour (h-16 = 4rem = 64px)
  const heightPixels = Math.max((duration / 60) * 64, 24) // Minimum 24px

  return {
    top: `${topPixels}px`,
    height: `${heightPixels}px`
  }
}

// Drag & Drop
const handleDragStart = (e: DragEvent, a: Appointment) => {
  draggingId.value = a.id
  draggingAppointment.value = a
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move'
    e.dataTransfer.setData('appointmentId', a.id)
  }
}

const handleDragEnd = () => {
  draggingId.value = null
  draggingAppointment.value = null
  dragOverSlot.value = null
}

const handleDragOver = (e: DragEvent, _date: string) => {
  e.preventDefault()
  if (e.dataTransfer) e.dataTransfer.dropEffect = 'move'
}

const handleDragLeave = () => {
  dragOverSlot.value = null
}

const handleDrop = (e: DragEvent, date: string) => {
  e.preventDefault()
  dragOverSlot.value = null
  if (draggingAppointment.value) {
    emit('drop-appointment', draggingAppointment.value, date)
  }
}
</script>
