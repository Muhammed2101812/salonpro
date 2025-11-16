<template>
  <div class="space-y-6">
    <!-- Timeline Header -->
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Müşteri Geçmişi</h3>
      <div class="flex gap-2">
        <button
          v-for="filter in filters"
          :key="filter.value"
          @click="activeFilter = filter.value"
          :class="[
            'px-3 py-1 text-sm rounded-md transition',
            activeFilter === filter.value
              ? 'bg-blue-500 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredItems.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="mt-2 text-sm text-gray-500">Henüz kayıt yok</p>
    </div>

    <!-- Timeline -->
    <div v-else class="flow-root">
      <ul role="list" class="-mb-8">
        <li v-for="(item, index) in filteredItems" :key="item.id" class="relative pb-8">
          <!-- Connecting Line -->
          <span
            v-if="index !== filteredItems.length - 1"
            class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
            aria-hidden="true"
          ></span>

          <div class="relative flex items-start space-x-3">
            <!-- Icon -->
            <div :class="[
              'relative flex h-10 w-10 items-center justify-center rounded-full ring-8 ring-white',
              getTypeColor(item.type)
            ]">
              <component :is="getTypeIcon(item.type)" class="h-5 w-5 text-white" />
            </div>

            <!-- Content -->
            <div class="min-w-0 flex-1">
              <div>
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium text-gray-900">{{ item.title }}</p>
                  <time class="text-sm text-gray-500">{{ formatDate(item.date) }}</time>
                </div>
                <p class="mt-0.5 text-sm text-gray-600">{{ item.description }}</p>

                <!-- Item Details -->
                <div v-if="item.details" class="mt-2 text-sm">
                  <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                    <div v-for="(value, key) in item.details" :key="key">
                      <dt class="font-medium text-gray-500">{{ key }}:</dt>
                      <dd class="text-gray-900">{{ value }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Amount Badge (for payments) -->
                <div v-if="item.amount" class="mt-2">
                  <span :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    item.amount > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ formatCurrency(item.amount) }}
                  </span>
                </div>

                <!-- Status Badge -->
                <div v-if="item.status" class="mt-2">
                  <span :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    getStatusColor(item.status)
                  ]">
                    {{ getStatusLabel(item.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Load More Button -->
    <div v-if="hasMore && !loading" class="text-center pt-4">
      <button
        @click="loadMore"
        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition"
      >
        Daha Fazla Yükle
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { h } from 'vue'
import api from '@/services/api'

const props = defineProps<{
  customerId: string
}>()

const loading = ref(false)
const timelineItems = ref<any[]>([])
const activeFilter = ref('all')
const page = ref(1)
const hasMore = ref(true)

const filters = [
  { value: 'all', label: 'Tümü' },
  { value: 'appointment', label: 'Randevular' },
  { value: 'payment', label: 'Ödemeler' },
  { value: 'note', label: 'Notlar' },
  { value: 'sale', label: 'Satışlar' },
]

const filteredItems = computed(() => {
  if (activeFilter.value === 'all') {
    return timelineItems.value
  }
  return timelineItems.value.filter(item => item.type === activeFilter.value)
})

// Fetch timeline data
const fetchTimeline = async (append = false) => {
  loading.value = true
  try {
    const response: any = await api.get(`/customers/${props.customerId}/timeline`, {
      params: { page: page.value, per_page: 20 }
    })

    if (append) {
      timelineItems.value = [...timelineItems.value, ...response.data]
    } else {
      timelineItems.value = response.data
    }

    hasMore.value = response.data.length === 20
  } catch (error) {
    console.error('Failed to fetch timeline:', error)
  } finally {
    loading.value = false
  }
}

const loadMore = async () => {
  page.value++
  await fetchTimeline(true)
}

// Helper: Get icon for timeline item type
const getTypeIcon = (type: string) => {
  const icons: Record<string, any> = {
    appointment: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
    ]),
    payment: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })
    ]),
    note: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z' })
    ]),
    sale: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })
    ]),
  }
  return icons[type] || icons.note
}

// Helper: Get background color for timeline item type
const getTypeColor = (type: string) => {
  const colors: Record<string, string> = {
    appointment: 'bg-blue-500',
    payment: 'bg-green-500',
    note: 'bg-yellow-500',
    sale: 'bg-purple-500',
  }
  return colors[type] || 'bg-gray-500'
}

// Helper: Get status badge color
const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    paid: 'bg-green-100 text-green-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

// Helper: Get status label in Turkish
const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Beklemede',
    confirmed: 'Onaylandı',
    completed: 'Tamamlandı',
    cancelled: 'İptal Edildi',
    paid: 'Ödendi',
    unpaid: 'Ödenmedi',
  }
  return labels[status] || status
}

// Helper: Format date
const formatDate = (date: string) => {
  const d = new Date(date)
  const now = new Date()
  const diff = now.getTime() - d.getTime()
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (days === 0) return 'Bugün'
  if (days === 1) return 'Dün'
  if (days < 7) return `${days} gün önce`
  if (days < 30) return `${Math.floor(days / 7)} hafta önce`
  if (days < 365) return `${Math.floor(days / 30)} ay önce`

  return d.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

// Helper: Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount)
}

onMounted(() => {
  fetchTimeline()
})
</script>
