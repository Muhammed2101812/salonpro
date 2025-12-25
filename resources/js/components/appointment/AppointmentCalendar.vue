<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header / Days -->
    <div class="grid grid-cols-7 bg-gray-50 border-b">
      <div v-for="day in weekDays" :key="day" class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
        {{ day }}
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 divide-x divide-y">
      <div
        v-for="day in calendarDays"
        :key="day.date"
        :class="[
          'min-h-32 p-2 relative transition-colors cursor-pointer',
          !day.isCurrentMonth ? 'bg-gray-50' : 'bg-white hover:bg-gray-50',
          day.isToday ? 'bg-primary-50' : '',
          dragOverDay === day.date ? 'bg-primary-100 ring-2 ring-primary-500 ring-inset' : ''
        ]"
        @click="$emit('date-click', day.date)"
        @drop="handleDrop($event, day.date)"
        @dragover="handleDragOver($event, day.date)"
        @dragleave="handleDragLeave"
      >
        <div class="flex justify-between items-start mb-2">
          <span :class="[
            'text-sm font-semibold',
            day.isToday ? 'bg-primary text-white w-7 h-7 flex items-center justify-center rounded-full' : '',
            !day.isCurrentMonth ? 'text-gray-400' : 'text-gray-700'
          ]">
            {{ day.dayNumber }}
          </span>
          <span v-if="day.appointments.length > 0" class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full">
            {{ day.appointments.length }}
          </span>
        </div>

        <div class="space-y-1">
          <div
            v-for="appointment in day.appointments.slice(0, 3)"
            :key="appointment.id"
            draggable="true"
            @dragstart="handleDragStart($event, appointment)"
            @dragend="handleDragEnd"
            @click.stop="$emit('edit-appointment', appointment)"
            :class="[
              'text-xs p-1.5 rounded cursor-move hover:opacity-80 transition shadow-sm border',
              getStatusClass(appointment.status),
              draggingId === appointment.id ? 'opacity-50' : ''
            ]"
          >
            <div class="font-medium truncate">{{ formatTime(appointment.appointment_date) }}</div>
            <div class="truncate opacity-90">{{ appointment.customer_name }}</div>
          </div>
          <div v-if="day.appointments.length > 3" class="text-xs text-primary-600 text-center py-1 font-medium hover:underline">
            +{{ day.appointments.length - 3 }} daha
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
  status: string
  customer_name?: string // Pre-computed or passed
  customer_id: string
  [key: string]: any
}

interface CalendarDay {
  date: string
  dayNumber: number
  isCurrentMonth: boolean
  isToday: boolean
  appointments: Appointment[]
}

const props = defineProps<{
  appointments: Appointment[] // Should be ALL appointments for the month
  currentDate: Date
}>()

const emit = defineEmits<{
  'date-click': [date: string]
  'edit-appointment': [appointment: Appointment]
  'drop-appointment': [appointment: Appointment, newDate: string]
}>()

const weekDays = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
const dragOverDay = ref<string | null>(null)
const draggingId = ref<string | null>(null)
const draggingAppointment = ref<Appointment | null>(null)

// Helpers
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

// Calendar Logic
const calendarDays = computed<CalendarDay[]>(() => {
  const year = props.currentDate.getFullYear()
  const month = props.currentDate.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)

  let startDay = firstDay.getDay() - 1 // 0=Sun, 1=Mon... we want Mon=0
  if (startDay === -1) startDay = 6

  const days: CalendarDay[] = []
  const prevMonthLastDay = new Date(year, month, 0).getDate()

  // Prev Month Days
  for (let i = startDay - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonthLastDay - i)
    days.push({ 
        date: date.toISOString(), 
        dayNumber: prevMonthLastDay - i, 
        isCurrentMonth: false, 
        isToday: false, 
        appointments: getAppointmentsForDate(date) 
    })
  }

  // Current Month Days
  const today = new Date()
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i)
    days.push({ 
        date: date.toISOString(), 
        dayNumber: i, 
        isCurrentMonth: true, 
        isToday: date.toDateString() === today.toDateString(), 
        appointments: getAppointmentsForDate(date) 
    })
  }

  // Next Month Days (fill to 42 usually)
  const remainingDays = 42 - days.length
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year, month + 1, i)
    days.push({ 
        date: date.toISOString(), 
        dayNumber: i, 
        isCurrentMonth: false, 
        isToday: false, 
        appointments: getAppointmentsForDate(date) 
    })
  }

  return days
})

const getAppointmentsForDate = (date: Date) => {
  return props.appointments.filter(a => new Date(a.appointment_date).toDateString() === date.toDateString())
    .sort((a, b) => new Date(a.appointment_date).getTime() - new Date(b.appointment_date).getTime())
}

// Drag & Drop Handlers
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
    dragOverDay.value = null
}
const handleDragOver = (e: DragEvent, day: string) => {
    e.preventDefault()
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'move'
    dragOverDay.value = day
}
const handleDragLeave = () => {
    dragOverDay.value = null
}
const handleDrop = (e: DragEvent, day: string) => {
    e.preventDefault()
    dragOverDay.value = null
    if (draggingAppointment.value) {
        emit('drop-appointment', draggingAppointment.value, day)
    }
}
</script>
