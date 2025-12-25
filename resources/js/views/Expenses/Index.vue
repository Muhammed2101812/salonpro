<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Giderler</h1>
        <p class="mt-2 text-sm text-gray-600">İşletme giderlerini takip edin ve analiz edin</p>
      </div>
      <div class="flex gap-3">
        <Button variant="outline" @click="exportExpenses" :icon="ArrowDownTrayIcon" label="Dışa Aktar" />
        <Button variant="danger" @click="openCreateModal" :icon="PlusIcon" label="Gider Ekle" />
      </div>
    </div>

    <!-- Stats -->
    <ExpenseStats :stats="stats" />

    <!-- Category Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
      <div 
        v-for="cat in expenseCategories" 
        :key="cat.value" 
        @click="filters.category = filters.category === cat.value ? '' : cat.value"
        :class="[
          'bg-white rounded-lg shadow-sm p-4 cursor-pointer border-2 transition-all',
          filters.category === cat.value ? 'border-red-500' : 'border-transparent hover:border-gray-200'
        ]"
      >
        <div class="flex items-center gap-2 mb-1">
          <component :is="cat.icon" :class="['h-4 w-4', cat.color]" />
          <p class="text-xs text-gray-500 uppercase font-medium">{{ cat.label }}</p>
        </div>
        <p class="text-lg font-bold text-gray-900">{{ formatCurrency(getCategoryTotal(cat.value)) }}</p>
      </div>
    </div>

    <!-- Filters -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
           <Input v-model="search" placeholder="Gider ara..." class="w-full lg:w-64">
                <template #prefix>
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </template>
           </Input>

           <select
             v-model="filters.category"
             class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-2 px-3"
           >
             <option value="">Tüm Kategoriler</option>
             <option v-for="cat in expenseCategories" :key="cat.value" :value="cat.value">{{ cat.label }}</option>
           </select>

           <input
             v-model="filters.month"
             type="month"
             class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-2 px-3"
           />

           <select v-model="filters.branchId" class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-2 px-3">
             <option value="">Tüm Şubeler</option>
             <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
           </select>
        </div>

        <Button variant="ghost" @click="loadData" :icon="ArrowPathIcon" />
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
    </div>

    <!-- Table -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredExpenses"
    >
        <template #cell-date="{ row }">
            <span class="text-sm font-medium text-gray-900">{{ formatDate(row.expense_date) }}</span>
        </template>
        <template #cell-title="{ row }">
            <p class="text-sm font-medium text-gray-900">{{ row.title }}</p>
            <p v-if="row.description" class="text-xs text-gray-500 truncate max-w-xs">{{ row.description }}</p>
        </template>
        <template #cell-category="{ row }">
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getCategoryBadge(row.category)]">
              {{ row.category }}
            </span>
        </template>
        <template #cell-branch="{ row }">
            {{ getBranchName(row.branch_id) }}
        </template>
        <template #cell-amount="{ row }">
            <span class="text-lg font-bold text-red-600">{{ formatCurrency(row.amount) }}</span>
        </template>
        <template #actions="{ row }">
            <div class="flex items-center justify-end gap-2">
              <Button variant="ghost" size="sm" @click="openEditModal(row)">
                  <PencilIcon class="h-4 w-4 text-primary" />
              </Button>
              <Button variant="ghost" size="sm" @click="handleDelete(row.id)">
                  <TrashIcon class="h-4 w-4 text-danger" />
              </Button>
            </div>
        </template>
    </DataTable>

    <!-- Modal -->
    <ExpenseModal
        v-model="showModal"
        :is-edit="isEdit"
        :initial-data="modalData"
        :loading="loading"
        :branches="branches"
        @submit="handleSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  HomeIcon,
  BoltIcon,
  UsersIcon,
  CubeIcon,
  WrenchScrewdriverIcon,
  MegaphoneIcon,
  EllipsisHorizontalCircleIcon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import ExpenseStats from '@/components/expense/ExpenseStats.vue'
import ExpenseModal from '@/components/expense/ExpenseModal.vue'

import { useExpenseStore } from '@/stores/expense'
import { useBranchStore } from '@/stores/branch'

interface Expense {
    id: string
    title: string
    category: string
    amount: number
    expense_date: string
    branch_id: string
    description?: string
    [key: string]: any
}

const expenseStore = useExpenseStore()
const branchStore = useBranchStore()

// State
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const modalData = ref<any>(null)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ category: '', month: '', branchId: '' })

const stats = ref({ thisMonth: 0, thisWeek: 0, today: 0, count: 0 })

const expenseCategories = [
  { value: 'Kira', label: 'Kira', icon: markRaw(HomeIcon), color: 'text-purple-600' },
  { value: 'Fatura', label: 'Fatura', icon: markRaw(BoltIcon), color: 'text-yellow-600' },
  { value: 'Maaş', label: 'Maaş', icon: markRaw(UsersIcon), color: 'text-blue-600' },
  { value: 'Malzeme', label: 'Malzeme', icon: markRaw(CubeIcon), color: 'text-green-600' },
  { value: 'Bakım', label: 'Bakım', icon: markRaw(WrenchScrewdriverIcon), color: 'text-orange-600' },
  { value: 'Pazarlama', label: 'Pazarlama', icon: markRaw(MegaphoneIcon), color: 'text-pink-600' },
  { value: 'Diğer', label: 'Diğer', icon: markRaw(EllipsisHorizontalCircleIcon), color: 'text-gray-600' }
]

const tableColumns = [
    { key: 'date', label: 'Tarih' },
    { key: 'title', label: 'Başlık' },
    { key: 'category', label: 'Kategori' },
    { key: 'branch', label: 'Şube' },
    { key: 'amount', label: 'Tutar', align: 'right' }
]

// Computed
const expenses = computed(() => expenseStore.expenses || [])
const branches = computed(() => branchStore.branches || [])

const filteredExpenses = computed(() => {
  let result = expenses.value as Expense[]
  if (search.value) result = result.filter(e => e.title?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.category) result = result.filter(e => e.category === filters.value.category)
  if (filters.value.month) result = result.filter(e => e.expense_date?.startsWith(filters.value.month))
  if (filters.value.branchId) result = result.filter(e => e.branch_id === filters.value.branchId)
  return result.sort((a, b) => new Date(b.expense_date).getTime() - new Date(a.expense_date).getTime())
})

// Helpers
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d)) : '-'
const getBranchName = (id: string) => branches.value.find((b: any) => b.id === id)?.name || '-'
const getCategoryBadge = (cat: string) => ({ Kira: 'bg-purple-100 text-purple-800', Fatura: 'bg-yellow-100 text-yellow-800', Maaş: 'bg-blue-100 text-blue-800', Malzeme: 'bg-green-100 text-green-800', Bakım: 'bg-orange-100 text-orange-800', Pazarlama: 'bg-pink-100 text-pink-800', Diğer: 'bg-gray-100 text-gray-800' }[cat] || 'bg-gray-100')
const getCategoryTotal = (cat: string) => expenses.value.filter((e: any) => e.category === cat).reduce((sum: number, e: any) => sum + (parseFloat(e.amount) || 0), 0)

const updateStats = () => {
  const now = new Date()
  const today = now.toISOString().split('T')[0]
  const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)

  stats.value.count = expenses.value.length
  stats.value.today = expenses.value.filter((e: any) => e.expense_date?.startsWith(today)).reduce((sum: number, e: any) => sum + (parseFloat(e.amount) || 0), 0)
  stats.value.thisWeek = expenses.value.filter((e: any) => new Date(e.expense_date) >= weekAgo).reduce((sum: number, e: any) => sum + (parseFloat(e.amount) || 0), 0)
  stats.value.thisMonth = expenses.value.filter((e: any) => { const d = new Date(e.expense_date); return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear() }).reduce((sum: number, e: any) => sum + (parseFloat(e.amount) || 0), 0)
}

// Modal Actions
const openCreateModal = () => {
    modalData.value = null
    isEdit.value = false
    editingId.value = null
    showModal.value = true
}

const openEditModal = (expense: Expense) => {
    modalData.value = { ...expense }
    isEdit.value = true
    editingId.value = expense.id
    showModal.value = true
}

const handleSubmit = async (data: any) => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await expenseStore.updateExpense(editingId.value, data) }
    else { await expenseStore.createExpense(data) }
    showModal.value = false
    await loadData()
  } catch (e: any) {
      console.error(e)
      alert('Kaydedilemedi')
  }
  finally { loading.value = false }
}

const handleDelete = async (id: string) => {
    if (!confirm('Bu gideri silmek istediğinizden emin misiniz?')) return
    try {
        await expenseStore.deleteExpense(id)
        await loadData()
    } catch (e) { console.error(e) }
}

const loadData = async () => {
    loading.value = true
    try {
        await Promise.all([expenseStore.fetchExpenses(), branchStore.fetchBranches()])
        updateStats()
    } finally {
        loading.value = false
    }
}

const exportExpenses = () => {
  const csvContent = [
    ['Tarih', 'Başlık', 'Kategori', 'Şube', 'Tutar', 'Açıklama'].join(','),
    ...filteredExpenses.value.map(e => [e.expense_date, e.title, e.category, getBranchName(e.branch_id), e.amount, e.description || ''].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `giderler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>
