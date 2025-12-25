<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100">
          <UserGroupIcon class="h-6 w-6 text-purple-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Toplam Çalışan</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.totalEmployees }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-green-100">
          <CheckCircleIcon class="h-6 w-6 text-green-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Aktif</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.activeEmployees }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <StarIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Uzman</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.seniorEmployees }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-yellow-100">
          <CurrencyDollarIcon class="h-6 w-6 text-yellow-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Ort. Komisyon</p>
          <p class="text-2xl font-bold text-gray-900">%{{ stats.avgCommission }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-pink-100">
          <CalendarIcon class="h-6 w-6 text-pink-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Yeni (Bu Ay)</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.newThisMonth }}</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  UserGroupIcon,
  CheckCircleIcon,
  StarIcon,
  CurrencyDollarIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    totalEmployees: number
    activeEmployees: number
    seniorEmployees: number
    avgCommission: number
    newThisMonth: number
  }
}>()
</script>
