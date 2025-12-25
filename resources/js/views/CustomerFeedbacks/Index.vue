<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Geri Bildirimleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri yorumlarını ve puanlamalarını takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportFeedbacks"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100">
            <ChatBubbleBottomCenterTextIcon class="h-6 w-6 text-amber-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <StarIcon class="h-6 w-6 text-yellow-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ortalama Puan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.avgRating.toFixed(1) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <FaceSmileIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Olumlu</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.positive }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-gray-100">
            <FaceMehIcon class="h-6 w-6 text-gray-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Nötr</p>
            <p class="text-2xl font-bold text-gray-600">{{ stats.neutral }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <FaceFrownIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Olumsuz</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.negative }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Puan Dağılımı -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
      <h3 class="text-sm font-medium text-gray-700 mb-4">Puan Dağılımı</h3>
      <div class="space-y-2">
        <div v-for="i in 5" :key="i" class="flex items-center gap-3">
          <span class="text-sm text-gray-500 w-8">{{ 6 - i }} ★</span>
          <div class="flex-1 bg-gray-200 rounded-full h-3">
            <div
              class="bg-yellow-400 h-3 rounded-full transition-all"
              :style="{ width: getRatingPercent(6 - i) + '%' }"
            ></div>
          </div>
          <span class="text-sm text-gray-600 w-8 text-right">{{ getRatingCount(6 - i) }}</span>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Yorum ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="rating in ratingFilters"
              :key="rating.value"
              @click="filters.rating = filters.rating === rating.value ? '' : rating.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.rating === rating.value ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ rating.label }}
            </button>
          </div>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Geri Bildirim Kartları -->
    <div v-else class="space-y-4">
      <div v-for="feedback in filteredFeedbacks" :key="feedback.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white font-bold">
              {{ getInitials(feedback.customer?.first_name, feedback.customer?.last_name) }}
            </div>
            <div>
              <p class="font-medium text-gray-900">
                {{ feedback.customer?.first_name }} {{ feedback.customer?.last_name }}
              </p>
              <p class="text-xs text-gray-500">{{ formatDate(feedback.created_at) }}</p>
            </div>
          </div>
          <div class="flex items-center gap-1">
            <template v-for="i in 5" :key="i">
              <StarIcon
                :class="['h-5 w-5', i <= feedback.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300']"
              />
            </template>
          </div>
        </div>

        <p v-if="feedback.comment" class="text-gray-700 mb-3">{{ feedback.comment }}</p>

        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
          <div class="flex items-center gap-4 text-sm text-gray-500">
            <span v-if="feedback.service">
              <span class="text-gray-400">Hizmet:</span> {{ feedback.service?.name }}
            </span>
            <span v-if="feedback.employee">
              <span class="text-gray-400">Çalışan:</span> {{ feedback.employee?.first_name }}
            </span>
          </div>
          <div class="flex gap-2">
            <button
              v-if="!feedback.is_responded"
              @click="replyFeedback(feedback)"
              class="text-sm text-amber-600 hover:text-amber-700 font-medium"
            >
              Yanıtla
            </button>
            <span v-else class="text-sm text-green-600 flex items-center gap-1">
              <CheckCircleIcon class="h-4 w-4" /> Yanıtlandı
            </span>
          </div>
        </div>
      </div>

      <div v-if="filteredFeedbacks.length === 0" class="text-center py-12">
        <ChatBubbleBottomCenterTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Geri bildirim bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, shallowRef } from 'vue'
import {
  ChatBubbleBottomCenterTextIcon,
  StarIcon,
  MagnifyingGlassIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid'
import { useCustomerFeedbackStore } from '@/stores/customerfeedback'

// Placeholder icons for face expressions
const FaceSmileIcon = { template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="15" cy="9" r="1" fill="currentColor"/></svg>` }
const FaceMehIcon = { template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="8" y1="14" x2="16" y2="14"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="15" cy="9" r="1" fill="currentColor"/></svg>` }
const FaceFrownIcon = { template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M8 16s1.5-2 4-2 4 2 4 2"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="15" cy="9" r="1" fill="currentColor"/></svg>` }

const store = useCustomerFeedbackStore()

const loading = ref(false)
const search = ref('')

const filters = ref({ rating: '' })

const stats = ref({ total: 0, avgRating: 0, positive: 0, neutral: 0, negative: 0 })

const ratingFilters = [
  { value: '', label: 'Tümü' },
  { value: '5', label: '5 ★' },
  { value: '4', label: '4 ★' },
  { value: '3', label: '3 ★' },
  { value: '2', label: '2 ★' },
  { value: '1', label: '1 ★' }
]

const feedbacks = ref<any[]>([])

const filteredFeedbacks = computed(() => {
  let result = feedbacks.value
  if (search.value) result = result.filter(f => f.comment?.toLowerCase().includes(search.value.toLowerCase()) || f.customer?.first_name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.rating) result = result.filter(f => f.rating === parseInt(filters.value.rating))
  return result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(d)) : '-'
const getInitials = (first: string, last: string) => `${first?.charAt(0) || ''}${last?.charAt(0) || ''}`.toUpperCase() || 'M'
const getRatingCount = (rating: number) => feedbacks.value.filter(f => f.rating === rating).length
const getRatingPercent = (rating: number) => feedbacks.value.length > 0 ? (getRatingCount(rating) / feedbacks.value.length) * 100 : 0

const updateStats = () => {
  stats.value.total = feedbacks.value.length
  stats.value.avgRating = feedbacks.value.length > 0 ? feedbacks.value.reduce((sum, f) => sum + (f.rating || 0), 0) / feedbacks.value.length : 0
  stats.value.positive = feedbacks.value.filter(f => f.rating >= 4).length
  stats.value.neutral = feedbacks.value.filter(f => f.rating === 3).length
  stats.value.negative = feedbacks.value.filter(f => f.rating <= 2).length
}

const replyFeedback = (feedback: any) => { alert('Yanıt özelliği geliştirme aşamasında') }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({ search: search.value })
    feedbacks.value = response?.data || []
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportFeedbacks = () => {
  const csvContent = [
    ['Müşteri', 'Puan', 'Yorum', 'Hizmet', 'Tarih'].join(','),
    ...filteredFeedbacks.value.map(f => [`${f.customer?.first_name || ''} ${f.customer?.last_name || ''}`, f.rating, `"${f.comment || ''}"`, f.service?.name || '', f.created_at].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `geri_bildirimler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>