<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Performans Değerlendirme</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların performans metriklerini takip edin ve değerlendirin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportPerformances"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Rapor İndir
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Değerlendirme Ekle
        </button>
      </div>
    </div>

    <!-- Dönem Seçici ve Özet İstatistikler -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
      <!-- Dönem Seçici -->
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-3 mb-4">
          <CalendarIcon class="h-5 w-5 text-indigo-600" />
          <span class="font-medium text-gray-900">Değerlendirme Dönemi</span>
        </div>
        <select
          v-model="selectedPeriod"
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="monthly">Aylık</option>
          <option value="quarterly">Çeyreklik</option>
          <option value="yearly">Yıllık</option>
        </select>
        <div class="mt-3 flex items-center gap-2">
          <button @click="previousPeriod" class="p-1 hover:bg-gray-100 rounded">
            <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
          </button>
          <span class="flex-1 text-center text-sm font-medium text-gray-700">{{ currentPeriodLabel }}</span>
          <button @click="nextPeriod" class="p-1 hover:bg-gray-100 rounded">
            <ChevronRightIcon class="h-5 w-5 text-gray-600" />
          </button>
        </div>
      </div>

      <!-- Ortalama Performans -->
      <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-sm p-5 text-white">
        <div class="flex items-center justify-between mb-2">
          <span class="text-indigo-100">Ortalama Performans</span>
          <ChartBarIcon class="h-5 w-5 text-indigo-200" />
        </div>
        <div class="flex items-end gap-2">
          <span class="text-4xl font-bold">{{ stats.averageScore }}</span>
          <span class="text-indigo-200 mb-1">/ 100</span>
        </div>
        <div class="mt-2 flex items-center gap-1">
          <div v-for="star in 5" :key="star" class="text-yellow-300">
            <StarIcon v-if="star <= Math.round(stats.averageScore / 20)" class="h-4 w-4 fill-current" />
            <StarIcon v-else class="h-4 w-4" />
          </div>
        </div>
      </div>

      <!-- En Yüksek Performans -->
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-500">En Yüksek</span>
          <TrophyIcon class="h-5 w-5 text-yellow-500" />
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.highestScore }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ stats.topPerformer }}</div>
      </div>

      <!-- Değerlendirilen Sayısı -->
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-500">Değerlendirilen</span>
          <UserGroupIcon class="h-5 w-5 text-indigo-500" />
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.evaluated }}</div>
        <div class="text-sm text-gray-500 mt-1">/ {{ stats.totalEmployees }} çalışan</div>
      </div>
    </div>

    <!-- Çalışan Performans Kartları -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Çalışan Performans Özeti</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="emp in employeePerformances"
          :key="emp.id"
          class="p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all cursor-pointer"
          @click="viewEmployeeDetail(emp)"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                {{ getInitials(emp.first_name, emp.last_name) }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ emp.first_name }} {{ emp.last_name }}</p>
                <p class="text-xs text-gray-500">{{ emp.position || 'Çalışan' }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold" :class="getScoreColor(emp.overallScore)">
                {{ emp.overallScore }}
              </div>
              <div class="flex items-center gap-0.5">
                <StarIcon
                  v-for="star in 5"
                  :key="star"
                  class="h-3 w-3"
                  :class="star <= Math.round(emp.overallScore / 20) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                />
              </div>
            </div>
          </div>

          <!-- KPI Bars -->
          <div class="space-y-2">
            <div v-for="kpi in emp.kpis" :key="kpi.name">
              <div class="flex justify-between text-xs mb-1">
                <span class="text-gray-600">{{ kpi.label }}</span>
                <span class="font-medium" :class="getScoreColor(kpi.score)">{{ kpi.score }}%</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div
                  class="h-1.5 rounded-full transition-all"
                  :class="getBarColor(kpi.score)"
                  :style="{ width: `${kpi.score}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Trend -->
          <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">Önceki döneme göre</span>
            <div class="flex items-center gap-1" :class="emp.trend >= 0 ? 'text-green-600' : 'text-red-600'">
              <ArrowTrendingUpIcon v-if="emp.trend >= 0" class="h-4 w-4" />
              <ArrowTrendingDownIcon v-else class="h-4 w-4" />
              <span class="text-sm font-medium">{{ emp.trend >= 0 ? '+' : '' }}{{ emp.trend }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Performans Değerlendirmeleri Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Değerlendirme Geçmişi</h3>
        <div class="flex gap-2">
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Yükleniyor -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Dönem
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Genel Puan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Randevu
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Memnuniyet
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Satış
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Değerlendiren
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="perf in filteredPerformances" :key="perf.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium">
                  {{ getInitials(perf.employee?.first_name || 'U', perf.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ perf.employee?.first_name }} {{ perf.employee?.last_name }}
                  </p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ perf.period_label }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <span class="text-lg font-bold" :class="getScoreColor(perf.overall_score)">
                  {{ perf.overall_score }}
                </span>
                <div class="flex">
                  <StarIcon
                    v-for="star in 5"
                    :key="star"
                    class="h-4 w-4"
                    :class="star <= Math.round(perf.overall_score / 20) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
                  />
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="w-16 bg-gray-100 rounded-full h-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getBarColor(perf.appointment_score)"
                    :style="{ width: `${perf.appointment_score}%` }"
                  ></div>
                </div>
                <span class="text-sm text-gray-600">{{ perf.appointment_score }}%</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="w-16 bg-gray-100 rounded-full h-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getBarColor(perf.satisfaction_score)"
                    :style="{ width: `${perf.satisfaction_score}%` }"
                  ></div>
                </div>
                <span class="text-sm text-gray-600">{{ perf.satisfaction_score }}%</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="w-16 bg-gray-100 rounded-full h-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getBarColor(perf.sales_score)"
                    :style="{ width: `${perf.sales_score}%` }"
                  ></div>
                </div>
                <span class="text-sm text-gray-600">{{ perf.sales_score }}%</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ perf.evaluated_by || 'Sistem' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewDetail(perf)"
                  class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Detay"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editPerformance(perf)"
                  class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="deletePerformance(perf)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Sil"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Boş Durum -->
      <div v-if="filteredPerformances.length === 0 && !loading" class="p-12 text-center">
        <ChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Bu dönemde değerlendirme bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-indigo-600 hover:text-indigo-700 font-medium"
        >
          Değerlendirme ekleyin
        </button>
      </div>
    </div>

    <!-- Değerlendirme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingPerformance ? 'Değerlendirme Düzenle' : 'Yeni Performans Değerlendirme' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="savePerformance" class="p-6 space-y-6">
          <!-- Çalışan ve Dönem -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
              <select
                v-model="formData.employee_id"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">Çalışan Seçin</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Dönem *</label>
              <input
                v-model="formData.period_label"
                type="text"
                required
                placeholder="Örn: Ocak 2024"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
          </div>

          <!-- KPI Puanları -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Performans Metrikleri</label>
            <div class="space-y-4">
              <!-- Randevu Performansı -->
              <div class="p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium text-gray-900">Randevu Performansı</span>
                  <span class="text-lg font-bold" :class="getScoreColor(formData.appointment_score)">
                    {{ formData.appointment_score }}%
                  </span>
                </div>
                <input
                  v-model.number="formData.appointment_score"
                  type="range"
                  min="0"
                  max="100"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                />
                <p class="text-xs text-gray-500 mt-1">Randevu tamamlama oranı ve zamanında başlama</p>
              </div>

              <!-- Müşteri Memnuniyeti -->
              <div class="p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium text-gray-900">Müşteri Memnuniyeti</span>
                  <span class="text-lg font-bold" :class="getScoreColor(formData.satisfaction_score)">
                    {{ formData.satisfaction_score }}%
                  </span>
                </div>
                <input
                  v-model.number="formData.satisfaction_score"
                  type="range"
                  min="0"
                  max="100"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                />
                <p class="text-xs text-gray-500 mt-1">Müşteri geri bildirimleri ve tekrar randevu oranı</p>
              </div>

              <!-- Satış Performansı -->
              <div class="p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium text-gray-900">Satış Performansı</span>
                  <span class="text-lg font-bold" :class="getScoreColor(formData.sales_score)">
                    {{ formData.sales_score }}%
                  </span>
                </div>
                <input
                  v-model.number="formData.sales_score"
                  type="range"
                  min="0"
                  max="100"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                />
                <p class="text-xs text-gray-500 mt-1">Ürün satışı ve ek hizmet önerileri</p>
              </div>

              <!-- Devam/Dakiklik -->
              <div class="p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium text-gray-900">Devam ve Dakiklik</span>
                  <span class="text-lg font-bold" :class="getScoreColor(formData.attendance_score)">
                    {{ formData.attendance_score }}%
                  </span>
                </div>
                <input
                  v-model.number="formData.attendance_score"
                  type="range"
                  min="0"
                  max="100"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                />
                <p class="text-xs text-gray-500 mt-1">İşe geliş ve zamanına uyum</p>
              </div>
            </div>
          </div>

          <!-- Genel Değerlendirme -->
          <div class="bg-indigo-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="font-medium text-indigo-900">Genel Performans Puanı</span>
              <span class="text-3xl font-bold text-indigo-700">{{ calculateOverallScore() }}</span>
            </div>
            <div class="flex items-center gap-1">
              <StarIcon
                v-for="star in 5"
                :key="star"
                class="h-6 w-6"
                :class="star <= Math.round(calculateOverallScore() / 20) ? 'text-yellow-400 fill-current' : 'text-indigo-200'"
              />
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Değerlendirme Notları</label>
            <textarea
              v-model="formData.notes"
              rows="3"
              placeholder="Çalışan hakkındaki gözlemler ve öneriler..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            ></textarea>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  ChartBarIcon,
  CalendarIcon,
  UserGroupIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  EyeIcon,
  StarIcon,
  TrophyIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon
} from '@heroicons/vue/24/outline'
import { useEmployeePerformanceStore } from '@/stores/employeeperformance'

interface Performance {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  period_label: string
  overall_score: number
  appointment_score: number
  satisfaction_score: number
  sales_score: number
  attendance_score: number
  notes?: string
  evaluated_by?: string
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeePerformanceStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingPerformance = ref<Performance | null>(null)
const selectedPeriod = ref('monthly')
const currentPeriodIndex = ref(0)

const performances = ref<Performance[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: ''
})

const stats = ref({
  averageScore: 0,
  highestScore: 0,
  topPerformer: '',
  evaluated: 0,
  totalEmployees: 0
})

const formData = ref({
  employee_id: '',
  period_label: '',
  appointment_score: 75,
  satisfaction_score: 80,
  sales_score: 70,
  attendance_score: 90,
  notes: ''
})

// Computed
const currentPeriodLabel = computed(() => {
  const date = new Date()
  date.setMonth(date.getMonth() - currentPeriodIndex.value)
  
  if (selectedPeriod.value === 'monthly') {
    return date.toLocaleDateString('tr-TR', { month: 'long', year: 'numeric' })
  } else if (selectedPeriod.value === 'quarterly') {
    const quarter = Math.floor(date.getMonth() / 3) + 1
    return `${date.getFullYear()} Q${quarter}`
  } else {
    return date.getFullYear().toString()
  }
})

const filteredPerformances = computed(() => {
  let result = performances.value

  if (filters.value.employeeId) {
    result = result.filter(p => p.employee_id === filters.value.employeeId)
  }

  return result.sort((a, b) => b.overall_score - a.overall_score)
})

const employeePerformances = computed(() => {
  const empMap: Record<string, any> = {}
  
  employees.value.forEach(emp => {
    const empPerfs = performances.value.filter(p => p.employee_id === emp.id)
    const latestPerf = empPerfs[0]
    const previousPerf = empPerfs[1]
    
    if (latestPerf) {
      empMap[emp.id] = {
        ...emp,
        overallScore: latestPerf.overall_score,
        kpis: [
          { name: 'appointment', label: 'Randevu', score: latestPerf.appointment_score },
          { name: 'satisfaction', label: 'Memnuniyet', score: latestPerf.satisfaction_score },
          { name: 'sales', label: 'Satış', score: latestPerf.sales_score },
          { name: 'attendance', label: 'Devam', score: latestPerf.attendance_score }
        ],
        trend: previousPerf ? latestPerf.overall_score - previousPerf.overall_score : 0
      }
    }
  })
  
  return Object.values(empMap).sort((a: any, b: any) => b.overallScore - a.overallScore)
})

// Helpers
const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getScoreColor = (score: number) => {
  if (score >= 80) return 'text-green-600'
  if (score >= 60) return 'text-yellow-600'
  return 'text-red-600'
}

const getBarColor = (score: number) => {
  if (score >= 80) return 'bg-green-500'
  if (score >= 60) return 'bg-yellow-500'
  return 'bg-red-500'
}

const calculateOverallScore = () => {
  const scores = [
    formData.value.appointment_score,
    formData.value.satisfaction_score,
    formData.value.sales_score,
    formData.value.attendance_score
  ]
  return Math.round(scores.reduce((a, b) => a + b, 0) / scores.length)
}

// Navigation
const previousPeriod = () => {
  currentPeriodIndex.value++
}

const nextPeriod = () => {
  if (currentPeriodIndex.value > 0) {
    currentPeriodIndex.value--
  }
}

// Modal Functions
const openCreateModal = () => {
  editingPerformance.value = null
  formData.value = {
    employee_id: '',
    period_label: currentPeriodLabel.value,
    appointment_score: 75,
    satisfaction_score: 80,
    sales_score: 70,
    attendance_score: 90,
    notes: ''
  }
  showModal.value = true
}

const editPerformance = (perf: Performance) => {
  editingPerformance.value = perf
  formData.value = {
    employee_id: perf.employee_id,
    period_label: perf.period_label,
    appointment_score: perf.appointment_score,
    satisfaction_score: perf.satisfaction_score,
    sales_score: perf.sales_score,
    attendance_score: perf.attendance_score,
    notes: perf.notes || ''
  }
  showModal.value = true
}

const viewDetail = (perf: Performance) => {
  editPerformance(perf)
}

const viewEmployeeDetail = (emp: any) => {
  filters.value.employeeId = emp.id
}

const closeModal = () => {
  showModal.value = false
  editingPerformance.value = null
}

// CRUD Operations
const loadData = async () => {
  loading.value = true
  try {
    // Çalışanları yükle
    const empResponse = await fetch('/api/employees')
    if (empResponse.ok) {
      const data = await empResponse.json()
      employees.value = data.data || []
    }

    // Performansları yükle
    const response = await store.fetchAll()
    performances.value = response?.data || []

    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateStats = () => {
  if (performances.value.length === 0) {
    stats.value = {
      averageScore: 0,
      highestScore: 0,
      topPerformer: '-',
      evaluated: 0,
      totalEmployees: employees.value.length
    }
    return
  }

  const scores = performances.value.map(p => p.overall_score)
  stats.value.averageScore = Math.round(scores.reduce((a, b) => a + b, 0) / scores.length)
  stats.value.highestScore = Math.max(...scores)
  
  const topPerf = performances.value.find(p => p.overall_score === stats.value.highestScore)
  stats.value.topPerformer = topPerf ? `${topPerf.employee?.first_name} ${topPerf.employee?.last_name}` : '-'
  
  const evaluatedIds = new Set(performances.value.map(p => p.employee_id))
  stats.value.evaluated = evaluatedIds.size
  stats.value.totalEmployees = employees.value.length
}

const savePerformance = async () => {
  saving.value = true
  try {
    const payload = {
      ...formData.value,
      overall_score: calculateOverallScore()
    }

    if (editingPerformance.value) {
      await store.update(editingPerformance.value.id, payload)
    } else {
      await store.create(payload)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Değerlendirme kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const deletePerformance = async (perf: Performance) => {
  if (!confirm('Bu değerlendirmeyi silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(perf.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Değerlendirme silinemedi')
  }
}

const exportPerformances = () => {
  const csvContent = [
    ['Çalışan', 'Dönem', 'Genel Puan', 'Randevu', 'Memnuniyet', 'Satış', 'Devam'].join(','),
    ...filteredPerformances.value.map(p => [
      `${p.employee?.first_name} ${p.employee?.last_name}`,
      p.period_label,
      p.overall_score,
      p.appointment_score,
      p.satisfaction_score,
      p.sales_score,
      p.attendance_score
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `performans_raporu_${currentPeriodLabel.value}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>