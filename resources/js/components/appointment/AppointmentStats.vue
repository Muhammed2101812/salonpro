<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-indigo-100">
          <CalendarDaysIcon class="h-6 w-6 text-indigo-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bugün</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.today }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-yellow-100">
          <ClockIcon class="h-6 w-6 text-yellow-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bekleyen</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <CheckBadgeIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Onaylı</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.confirmed }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-green-100">
          <CheckCircleIcon class="h-6 w-6 text-green-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Tamamlanan</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.completed }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-green-100">
          <BanknotesIcon class="h-6 w-6 text-green-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bugün Gelir</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.todayRevenue) }}</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  CalendarDaysIcon,
  ClockIcon,
  CheckBadgeIcon,
  CheckCircleIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    today: number
    pending: number
    confirmed: number
    completed: number
    todayRevenue: number
  }
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}
</script>
