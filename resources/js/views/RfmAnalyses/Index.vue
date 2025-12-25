<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">RFM Analizleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri RFM (Recency, Frequency, Monetary) analizlerini görüntüleyin</p>
      </div>
      <button @click="runAnalysis" class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">
        <ArrowPathIcon class="h-5 w-5 mr-2" />Analizi Çalıştır
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-cyan-100"><ChartBarIcon class="h-6 w-6 text-cyan-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Analiz</p><p class="text-2xl font-bold">{{ analyses.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><StarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Şampiyonlar</p><p class="text-2xl font-bold text-green-600">{{ championsCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Risk Altında</p><p class="text-2xl font-bold text-yellow-600">{{ atRiskCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><UserMinusIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kayıp</p><p class="text-2xl font-bold text-red-600">{{ lostCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- RFM Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <input v-model="search" type="text" placeholder="Müşteri ara..." class="flex-1 rounded-lg border-gray-300" />
        <select v-model="segmentFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Segmentler</option>
          <option value="champions">Şampiyonlar</option>
          <option value="loyal">Sadık</option>
          <option value="potential">Potansiyel</option>
          <option value="at_risk">Risk Altında</option>
          <option value="lost">Kayıp</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">R</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">F</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">M</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Skor</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Segment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="a in filteredAnalyses" :key="a.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-cyan-100 flex items-center justify-center">
                  <span class="text-cyan-600 font-bold">{{ getInitials(a.customer?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ a.customer?.name || 'Müşteri' }}</div>
                  <div class="text-xs text-gray-500">{{ a.customer?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-8 w-8 items-center justify-center rounded-full font-bold text-sm', getScoreBg(a.recency_score)]">{{ a.recency_score || 0 }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-8 w-8 items-center justify-center rounded-full font-bold text-sm', getScoreBg(a.frequency_score)]">{{ a.frequency_score || 0 }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-8 w-8 items-center justify-center rounded-full font-bold text-sm', getScoreBg(a.monetary_score)]">{{ a.monetary_score || 0 }}</span></td>
            <td class="px-6 py-4 text-center"><span class="text-lg font-bold text-cyan-600">{{ a.total_score || (a.recency_score + a.frequency_score + a.monetary_score) || 0 }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getSegmentBadge(a.segment)]">{{ getSegmentLabel(a.segment) }}</span></td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredAnalyses.length === 0" class="p-12 text-center">
        <ChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Analiz bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ChartBarIcon, StarIcon, ExclamationTriangleIcon, UserMinusIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import { useRfmAnalysisStore } from '@/stores/rfmanalysis'

const store = useRfmAnalysisStore()
const search = ref('')
const segmentFilter = ref('')
const analyses = ref<any[]>([])

const championsCount = computed(() => analyses.value.filter(a => a.segment === 'champions').length)
const atRiskCount = computed(() => analyses.value.filter(a => a.segment === 'at_risk').length)
const lostCount = computed(() => analyses.value.filter(a => a.segment === 'lost').length)
const filteredAnalyses = computed(() => analyses.value.filter(a => (!segmentFilter.value || a.segment === segmentFilter.value) && (!search.value || a.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))))
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const getScoreBg = (s: number) => s >= 4 ? 'bg-green-100 text-green-800' : s >= 3 ? 'bg-blue-100 text-blue-800' : s >= 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
const getSegmentLabel = (s: string) => ({ champions: 'Şampiyonlar', loyal: 'Sadık', potential: 'Potansiyel', at_risk: 'Risk Altında', lost: 'Kayıp', new: 'Yeni' }[s] || s || 'Belirsiz')
const getSegmentBadge = (s: string) => ({ champions: 'bg-green-100 text-green-800', loyal: 'bg-blue-100 text-blue-800', potential: 'bg-purple-100 text-purple-800', at_risk: 'bg-yellow-100 text-yellow-800', lost: 'bg-red-100 text-red-800', new: 'bg-cyan-100 text-cyan-800' }[s] || 'bg-gray-100 text-gray-800')

const runAnalysis = () => { alert('RFM analizi çalıştırılıyor...') }
const loadData = async () => { const r = await store.fetchAll({}); analyses.value = r?.data || [] }
onMounted(() => { loadData() })
</script>
