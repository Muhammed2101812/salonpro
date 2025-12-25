<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-lg bg-green-100">
          <ClockIcon class="h-6 w-6 text-green-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Çalışma Saatleri</h3>
          <p class="text-sm text-gray-500">Salon açılış ve kapanış saatlerini belirleyin</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <Input
          :modelValue="settings.opening_time"
          @update:modelValue="updateSetting('opening_time', $event)"
          type="time"
          label="Açılış Saati *"
          required
        />
        <Input
          :modelValue="settings.closing_time"
          @update:modelValue="updateSetting('closing_time', $event)"
          type="time"
          label="Kapanış Saati *"
          required
        />
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Çalışma Günleri *</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="day in weekDays"
            :key="day.value"
            type="button"
            @click="toggleDay(day.value)"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium border transition-colors',
              settings.working_days.includes(day.value)
                ? 'bg-indigo-100 text-indigo-700 border-indigo-300'
                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
            ]"
          >
            {{ day.label }}
          </button>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Saat Dilimi *</label>
        <select
          :value="settings.timezone"
          @change="updateSetting('timezone', ($event.target as HTMLSelectElement).value)"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="Europe/Istanbul">İstanbul (UTC+3)</option>
          <option value="Europe/Athens">Atina (UTC+2)</option>
          <option value="Europe/Berlin">Berlin (UTC+1)</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ClockIcon } from '@heroicons/vue/24/outline'
import Input from '@/components/ui/Input.vue'

const props = defineProps<{
  settings: any
}>()

const emit = defineEmits<{
  'update:settings': [key: string, value: any]
}>()

const weekDays = [
  { value: 'monday', label: 'Pzt' },
  { value: 'tuesday', label: 'Sal' },
  { value: 'wednesday', label: 'Çar' },
  { value: 'thursday', label: 'Per' },
  { value: 'friday', label: 'Cum' },
  { value: 'saturday', label: 'Cmt' },
  { value: 'sunday', label: 'Paz' }
]

const updateSetting = (key: string, value: any) => {
  emit('update:settings', key, value)
}

const toggleDay = (day: string) => {
  const days = [...props.settings.working_days]
  const index = days.indexOf(day)
  if (index > -1) {
    days.splice(index, 1)
  } else {
    days.push(day)
  }
  updateSetting('working_days', days)
}
</script>
