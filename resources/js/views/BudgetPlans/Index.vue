<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bütçe Planları</h1>
        <p class="mt-2 text-sm text-gray-600">Gelir ve gider bütçelerinizi planlayın</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Plan
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><DocumentChartBarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Plan</p><p class="text-2xl font-bold">{{ plans.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowTrendingUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Planlanan Gelir</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalIncome) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ArrowTrendingDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Planlanan Gider</p><p class="text-2xl font-bold text-red-600">{{ formatCurrency(totalExpense) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CalculatorIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Net Bütçe</p><p :class="['text-2xl font-bold', netBudget >= 0 ? 'text-green-600' : 'text-red-600']">{{ formatCurrency(netBudget) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Plan Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', plan.is_active ? 'bg-purple-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div>
              <h3 class="font-semibold text-gray-900">{{ plan.name }}</h3>
              <p class="text-sm text-gray-500">{{ plan.period || 'Aylık' }}</p>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', plan.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ plan.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
              <span class="text-sm text-green-700">Gelir</span>
              <span class="font-bold text-green-600">{{ formatCurrency(plan.planned_income || 0) }}</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
              <span class="text-sm text-red-700">Gider</span>
              <span class="font-bold text-red-600">{{ formatCurrency(plan.planned_expense || 0) }}</span>
            </div>
          </div>

          <!-- İlerleme -->
          <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Gerçekleşen</span><span>{{ getProgress(plan) }}%</span></div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-purple-500 rounded-full" :style="{ width: getProgress(plan) + '%' }"></div>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
            <span>{{ formatDate(plan.start_date) }}</span>
            <span>→</span>
            <span>{{ formatDate(plan.end_date) }}</span>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editPlan(plan)" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(plan.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="plans.length === 0" class="col-span-full text-center py-12">
        <DocumentChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Bütçe planı bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Planı Düzenle' : 'Yeni Plan' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Plan Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label><input v-model="form.start_date" type="date" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label><input v-model="form.end_date" type="date" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Planlanan Gelir</label><input v-model.number="form.planned_income" type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Planlanan Gider</label><input v-model.number="form.planned_expense" type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, DocumentChartBarIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, CalculatorIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useBudgetPlanStore } from '@/stores/budgetplan'

const store = useBudgetPlanStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', start_date: '', end_date: '', planned_income: 0, planned_expense: 0, is_active: true })
const plans = ref<any[]>([])

const totalIncome = computed(() => plans.value.reduce((s, p) => s + (parseFloat(p.planned_income) || 0), 0))
const totalExpense = computed(() => plans.value.reduce((s, p) => s + (parseFloat(p.planned_expense) || 0), 0))
const netBudget = computed(() => totalIncome.value - totalExpense.value)
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'short' }).format(new Date(d)) : '-'
const getProgress = (p: any) => { const actual = (parseFloat(p.actual_income) || 0) - (parseFloat(p.actual_expense) || 0); const planned = (parseFloat(p.planned_income) || 0) - (parseFloat(p.planned_expense) || 1); return Math.min(100, Math.max(0, Math.round((actual / planned) * 100))) }

const openCreateModal = () => { form.value = { name: '', start_date: '', end_date: '', planned_income: 0, planned_expense: 0, is_active: true }; isEdit.value = false; showModal.value = true }
const editPlan = (p: any) => { form.value = { ...p }; isEdit.value = true; editingId.value = p.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); plans.value = r?.data || [] }
onMounted(() => { loadData() })
</script>