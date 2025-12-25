<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Değerlendirmeleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri hizmet değerlendirmelerini görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><StarIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ reviews.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><FaceSmileIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Puan</p><p class="text-2xl font-bold text-green-600">{{ avgRating.toFixed(1) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><HandThumbUpIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">5 Yıldız</p><p class="text-2xl font-bold text-blue-600">{{ fiveStarCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><HandThumbDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">1-2 Yıldız</p><p class="text-2xl font-bold text-red-600">{{ lowRatingCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Değerlendirmeler -->
    <div class="space-y-4">
      <div v-for="r in reviews" :key="r.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start gap-4">
          <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <span class="text-yellow-600 font-bold">{{ getInitials(r.customer?.name) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between mb-2">
              <div>
                <h3 class="font-semibold text-gray-900">{{ r.customer?.name || 'Müşteri' }}</h3>
                <p class="text-sm text-gray-500">{{ r.service?.name || 'Hizmet' }} - {{ r.employee?.name || '' }}</p>
              </div>
              <div class="flex items-center gap-1">
                <StarIcon v-for="i in 5" :key="i" :class="['h-5 w-5', i <= r.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300']" />
              </div>
            </div>
            <p class="text-gray-700 mb-3">{{ r.comment || 'Yorum yok' }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400">{{ formatDate(r.created_at) }}</span>
              <div class="flex gap-2">
                <button v-if="!r.response" @click="respondReview(r)" class="text-sm text-blue-600 hover:text-blue-700">Yanıtla</button>
                <button @click="handleDelete(r.id)" class="text-sm text-red-600 hover:text-red-700">Sil</button>
              </div>
            </div>
            <div v-if="r.response" class="mt-3 p-3 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-800"><strong>Yanıt:</strong> {{ r.response }}</p>
            </div>
          </div>
        </div>
      </div>
      <div v-if="reviews.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <StarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Değerlendirme bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { StarIcon, FaceSmileIcon, HandThumbUpIcon, HandThumbDownIcon } from '@heroicons/vue/24/outline'
import { useServiceReviewStore } from '@/stores/servicereview'

const store = useServiceReviewStore()
const reviews = ref<any[]>([])

const avgRating = computed(() => reviews.value.length ? reviews.value.reduce((s, r) => s + (r.rating || 0), 0) / reviews.value.length : 0)
const fiveStarCount = computed(() => reviews.value.filter(r => r.rating === 5).length)
const lowRatingCount = computed(() => reviews.value.filter(r => r.rating <= 2).length)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'

const respondReview = (r: any) => { alert(`${r.customer?.name || 'Müşteri'} değerlendirmesine yanıt veriliyor...`) }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); reviews.value = r?.data || [] }
onMounted(() => { loadData() })
</script>