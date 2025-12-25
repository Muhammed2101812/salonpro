<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-emerald-100">
          <BanknotesIcon class="h-6 w-6 text-emerald-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Toplam Tahsilat</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total) }}</p>
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
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.completed) }}</p>
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
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.pending) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-blue-100">
          <CreditCardIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Kart Ödemeleri</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.cardPayments) }}</p>
        </div>
      </div>
    </Card>
    <Card>
      <div class="flex items-center">
        <div class="p-3 rounded-full bg-purple-100">
          <BanknotesIcon class="h-6 w-6 text-purple-600" />
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-500">Nakit</p>
          <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.cashPayments) }}</p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import {
  BanknotesIcon,
  CheckCircleIcon,
  ClockIcon,
  CreditCardIcon
} from '@heroicons/vue/24/outline'
import Card from '@/components/ui/Card.vue'

defineProps<{
  stats: {
    total: number
    completed: number
    pending: number
    cardPayments: number
    cashPayments: number
  }
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}
</script>
