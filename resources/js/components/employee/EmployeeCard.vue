<template>
  <div :class="[
      'bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all group',
      employee.is_active ? 'border-gray-100' : 'border-gray-200 opacity-75'
    ]">
    <div class="p-5">
      <!-- Profil -->
      <div class="flex items-center gap-4 mb-4">
        <div
          :class="[
            'flex-shrink-0 h-16 w-16 rounded-full flex items-center justify-center text-white font-bold text-xl',
            getAvatarGradient(employee)
          ]"
        >
          {{ getInitials(employee.first_name, employee.last_name) }}
        </div>
        <div class="min-w-0 flex-1">
          <h4 class="font-bold text-gray-900 truncate group-hover:text-primary transition-colors">{{ employee.first_name }} {{ employee.last_name }}</h4>
          <p class="text-sm text-gray-500">{{ getPositionLabel(employee.position) }}</p>
          <span
            :class="[
              'inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full',
              employee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            ]"
          >
            {{ employee.is_active ? 'Aktif' : 'Pasif' }}
          </span>
        </div>
      </div>

      <!-- İletişim -->
      <div class="space-y-2 mb-4 text-sm">
        <div v-if="employee.phone" class="flex items-center gap-2 text-gray-600">
          <PhoneIcon class="h-4 w-4 text-gray-400" />
          <a :href="`tel:${employee.phone}`" class="hover:text-primary transition-colors">{{ employee.phone }}</a>
        </div>
        <div v-if="employee.email" class="flex items-center gap-2 text-gray-600">
          <EnvelopeIcon class="h-4 w-4 text-gray-400" />
          <a :href="`mailto:${employee.email}`" class="hover:text-primary transition-colors truncate">{{ employee.email }}</a>
        </div>
        <div v-if="employee.branch?.name" class="flex items-center gap-2 text-gray-600">
          <BuildingStorefrontIcon class="h-4 w-4 text-gray-400" />
          <span>{{ employee.branch.name }}</span>
        </div>
      </div>

      <!-- Uzmanlık Alanları -->
      <div v-if="employee.specialties?.length" class="mb-4 min-h-[24px]">
        <div class="flex flex-wrap gap-1">
          <span
            v-for="(specialty, idx) in employee.specialties.slice(0, 3)"
            :key="idx"
            class="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full"
          >
            {{ specialty }}
          </span>
          <span v-if="employee.specialties.length > 3" class="text-xs text-gray-500">
            +{{ employee.specialties.length - 3 }}
          </span>
        </div>
      </div>
      <div v-else class="min-h-[24px] mb-4"></div>

      <!-- İstatistikler -->
      <div class="grid grid-cols-2 gap-2 mb-4">
        <div class="bg-yellow-50 rounded-lg p-2 text-center">
          <p class="text-lg font-bold text-yellow-600">%{{ employee.commission_rate || 0 }}</p>
          <p class="text-xs text-gray-500">Komisyon</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-2 text-center">
          <p class="text-lg font-bold text-blue-600">{{ getExperience(employee.hire_date) }}</p>
          <p class="text-xs text-gray-500">Deneyim</p>
        </div>
      </div>

      <!-- Aksiyon Butonları -->
      <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
        <Button variant="ghost" size="sm" @click="$emit('edit', employee)">
          <PencilIcon class="h-4 w-4 text-primary" />
        </Button>
        <Button variant="ghost" size="sm" @click="$emit('delete', employee.id)">
          <TrashIcon class="h-4 w-4 text-danger" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  PhoneIcon,
  EnvelopeIcon,
  BuildingStorefrontIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'

defineProps<{
  employee: any
  positions?: any[]
}>()

defineEmits<{
  edit: [employee: any]
  delete: [id: string]
}>()

const avatarGradients = [
  'bg-gradient-to-br from-purple-500 to-pink-500',
  'bg-gradient-to-br from-blue-500 to-cyan-500',
  'bg-gradient-to-br from-green-500 to-teal-500',
  'bg-gradient-to-br from-orange-500 to-red-500',
  'bg-gradient-to-br from-indigo-500 to-purple-500'
]

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getAvatarGradient = (employee: any) => {
  const index = employee.first_name?.charCodeAt(0) || 0
  return avatarGradients[index % avatarGradients.length]
}

const getExperience = (hireDate?: string) => {
  if (!hireDate) return '-'
  const years = Math.floor((Date.now() - new Date(hireDate).getTime()) / (365.25 * 24 * 60 * 60 * 1000))
  if (years < 1) return '<1 yıl'
  return `${years} yıl`
}

// Logic duplicated here for display, ideally prop or shared util
const getPositionLabel = (position?: string) => {
    // This is passed as prop or imported, but for now fallback string returning logic is fine if prop not fully available
    // Better: accept label directly or list of positions
    return position || 'Çalışan' 
}
</script>
