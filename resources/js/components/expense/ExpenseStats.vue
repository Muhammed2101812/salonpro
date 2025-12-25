<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-red-100">
          <CurrencyDollarIcon class="h-6 w-6 text-red-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bu Ay</p>
          <p class="text-2xl font-bold text-red-600">{{ formatCurrency(stats.thisMonth) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-orange-100">
          <CalendarDaysIcon class="h-6 w-6 text-orange-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bu Hafta</p>
          <p class="text-2xl font-bold text-orange-600">{{ formatCurrency(stats.thisWeek) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <ClockIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Bugün</p>
          <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(stats.today) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100">
          <DocumentTextIcon class="h-6 w-6 text-purple-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Toplam Kayıt</p>
          <p class="text-2xl font-bold text-purple-600">{{ stats.count }}</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  CurrencyDollarIcon,
  CalendarDaysIcon,
  ClockIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    thisMonth: number
    thisWeek: number
    today: number
    count: number
  }
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}
</script>
