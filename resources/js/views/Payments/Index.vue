<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ödemeler</h1>
        <p class="mt-2 text-sm text-gray-600">Tahsilatlarınızı yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <Button variant="outline" @click="exportPayments" :icon="ArrowDownTrayIcon" label="Dışa Aktar" />
        <Button variant="primary" @click="openCreateModal" :icon="PlusIcon" label="Yeni Ödeme" />
      </div>
    </div>

    <!-- Stats -->
    <PaymentStats :stats="stats" />

    <!-- Filters -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
           <Input v-model="search" placeholder="Müşteri ara..." class="w-full lg:w-64">
                <template #prefix>
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </template>
           </Input>

           <select
             v-model="filters.method"
             class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm py-2 px-3"
           >
             <option value="">Tüm Yöntemler</option>
             <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
               {{ method.label }}
             </option>
           </select>

           <div class="flex rounded-lg border border-gray-200 overflow-hidden">
             <button
               v-for="status in statusFilters"
               :key="status.value"
               @click="filters.status = filters.status === status.value ? '' : status.value"
               :class="[
                 'px-3 py-2 text-xs font-medium transition-colors',
                 filters.status === status.value ? status.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
               ]"
             >
               {{ status.label }}
             </button>
           </div>
           
           <!-- Date Range -->
           <div class="flex items-center gap-2 bg-gray-50 p-1 rounded-lg border border-gray-200">
             <input
               v-model="filters.startDate"
               type="date"
               class="rounded bg-transparent border-0 text-sm focus:ring-0 p-1"
             />
             <span class="text-gray-400">-</span>
             <input
               v-model="filters.endDate"
               type="date"
               class="rounded bg-transparent border-0 text-sm focus:ring-0 p-1"
             />
           </div>
        </div>

        <Button variant="ghost" @click="loadData" :icon="ArrowPathIcon" />
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="paymentStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Table -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredPayments"
    >
        <template #cell-date="{ row }">
            <div class="flex items-center gap-3">
              <div :class="['w-1.5 h-10 rounded-full', getStatusBarColor(row.status)]"></div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ formatDate(row.payment_date) }}</p>
                <p class="text-xs text-gray-500">{{ formatTime(row.created_at) }}</p>
              </div>
            </div>
        </template>
        <template #cell-customer="{ row }">
            {{ getCustomerName(row.customer_id) }}
        </template>
        <template #cell-amount="{ row }">
             <span class="text-sm font-bold text-primary">{{ formatCurrency(row.amount) }}</span>
        </template>
        <template #cell-method="{ row }">
            <div class="flex items-center gap-2">
              <component :is="getMethodIcon(row.payment_method)" class="h-4 w-4 text-gray-400" />
              <span class="text-sm text-gray-600">{{ getMethodLabel(row.payment_method) }}</span>
            </div>
        </template>
        <template #cell-status="{ row }">
             <span :class="['px-2 py-1 text-xs rounded-full font-semibold', getStatusBadge(row.status)]">
                {{ getStatusLabel(row.status) }}
             </span>
        </template>
        <template #actions="{ row }">
            <div class="flex items-center justify-end gap-2">
              <Button
                 v-if="row.status === 'pending'"
                 variant="ghost"
                 size="sm"
                 @click="completePayment(row)"
                 title="Tamamla"
              >
                  <CheckIcon class="h-4 w-4 text-success" />
              </Button>
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
    <PaymentModal
        v-model="showModal"
        :is-edit="isEdit"
        :initial-data="modalData"
        :loading="paymentStore.loading"
        :customers="customerStore.customers"
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
  CheckIcon,
  BanknotesIcon,
  CreditCardIcon,
  BuildingLibraryIcon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import PaymentStats from '@/components/payment/PaymentStats.vue'
import PaymentModal from '@/components/payment/PaymentModal.vue'

import { usePaymentStore } from '@/stores/payment'
import { useCustomerStore } from '@/stores/customer'

interface Payment {
  id: string
  customer_id: string
  amount: number
  payment_method: string
  payment_date: string
  status: string
  notes?: string
  created_at?: string
  [key: string]: any
}

const paymentStore = usePaymentStore()
const customerStore = useCustomerStore()

// State
const showModal = ref(false)
const isEdit = ref(false)
const modalData = ref<any>(null)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({
  method: '',
  status: '',
  startDate: '',
  endDate: ''
})

const stats = ref({
  total: 0,
  completed: 0,
  pending: 0,
  cardPayments: 0,
  cashPayments: 0
})

const paymentMethods = [
  { value: 'cash', label: 'Nakit', icon: markRaw(BanknotesIcon) },
  { value: 'credit_card', label: 'Kredi Kartı', icon: markRaw(CreditCardIcon) },
  { value: 'debit_card', label: 'Banka Kartı', icon: markRaw(CreditCardIcon) },
  { value: 'bank_transfer', label: 'Havale/EFT', icon: markRaw(BuildingLibraryIcon) }
]

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-primary text-white' },
  { value: 'completed', label: 'Tamamlandı', activeClass: 'bg-green-600 text-white' },
  { value: 'pending', label: 'Bekliyor', activeClass: 'bg-yellow-600 text-white' },
  { value: 'failed', label: 'Başarısız', activeClass: 'bg-red-600 text-white' },
  { value: 'refunded', label: 'İade', activeClass: 'bg-blue-600 text-white' }
]

const tableColumns = [
    { key: 'date', label: 'Tarih' },
    { key: 'customer', label: 'Müşteri' },
    { key: 'amount', label: 'Tutar' },
    { key: 'method', label: 'Yöntem' },
    { key: 'status', label: 'Durum' }
]

// Computed
const filteredPayments = computed(() => {
  let result = paymentStore.payments as Payment[]

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(p => getCustomerName(p.customer_id).toLowerCase().includes(searchLower))
  }

  if (filters.value.method) {
    result = result.filter(p => p.payment_method === filters.value.method)
  }

  if (filters.value.status) {
    result = result.filter(p => p.status === filters.value.status)
  }

  if (filters.value.startDate) {
    result = result.filter(p => p.payment_date >= filters.value.startDate)
  }

  if (filters.value.endDate) {
    result = result.filter(p => p.payment_date <= filters.value.endDate)
  }

  return result.sort((a, b) => new Date(b.payment_date).getTime() - new Date(a.payment_date).getTime())
})

// Helpers
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (dateString: string) => new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(dateString))
const formatTime = (dateString?: string) => dateString ? new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateString)) : ''

const getCustomerName = (id: string) => {
  const c = customerStore.customers.find((x: any) => x.id === id)
  return c ? `${c.first_name} ${c.last_name}` : '-'
}

const getMethodLabel = (method: string) => paymentMethods.find(m => m.value === method)?.label || method
const getMethodIcon = (method: string) => paymentMethods.find(m => m.value === method)?.icon || BanknotesIcon

const getStatusLabel = (status: string) => ({ completed: 'Tamamlandı', pending: 'Bekliyor', failed: 'Başarısız', refunded: 'İade' }[status] || status)
const getStatusBadge = (status: string) => ({ completed: 'bg-green-100 text-green-800', pending: 'bg-yellow-100 text-yellow-800', failed: 'bg-red-100 text-red-800', refunded: 'bg-blue-100 text-blue-800' }[status] || 'bg-gray-100 text-gray-800')
const getStatusBarColor = (status: string) => ({ completed: 'bg-green-500', pending: 'bg-yellow-500', failed: 'bg-red-500', refunded: 'bg-blue-500' }[status] || 'bg-gray-500')

const updateStats = () => {
  const payments = paymentStore.payments as Payment[]
  stats.value.total = payments.reduce((acc, p) => acc + Number(p.amount), 0)
  stats.value.completed = payments.filter(p => p.status === 'completed').reduce((acc, p) => acc + Number(p.amount), 0)
  stats.value.pending = payments.filter(p => p.status === 'pending').reduce((acc, p) => acc + Number(p.amount), 0)
  stats.value.cardPayments = payments.filter(p => ['credit_card', 'debit_card'].includes(p.payment_method)).reduce((acc, p) => acc + Number(p.amount), 0)
  stats.value.cashPayments = payments.filter(p => p.payment_method === 'cash').reduce((acc, p) => acc + Number(p.amount), 0)
}

const loadData = async () => {
  await paymentStore.fetchPayments()
  await customerStore.fetchCustomers()
  updateStats()
}

const openCreateModal = () => {
  modalData.value = null
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (payment: Payment) => {
  modalData.value = { ...payment }
  isEdit.value = true
  editingId.value = payment.id
  showModal.value = true
}

const handleSubmit = async (data: any) => {
  try {
    if (isEdit.value && editingId.value) {
      await paymentStore.updatePayment(editingId.value, data)
    } else {
      await paymentStore.createPayment(data)
    }
    showModal.value = false
    updateStats()
  } catch (error) {
    console.error('Ödeme kaydedilemedi:', error)
    alert('Ödeme kaydedilemedi')
  }
}

const handleDelete = async (id: string) => {
  if (!confirm('Bu ödemeyi silmek istediğinizden emin misiniz?')) return
  try {
    await paymentStore.deletePayment(id)
    updateStats()
  } catch (error) {
    console.error('Ödeme silinemedi:', error)
  }
}

const completePayment = async (payment: Payment) => {
  try {
    await paymentStore.updatePayment(payment.id, { ...payment, status: 'completed' })
    updateStats()
  } catch (error) {
    console.error('Ödeme tamamlanamadı:', error)
  }
}

const exportPayments = () => {
  const csvContent = [
    ['Tarih', 'Müşteri', 'Tutar', 'Yöntem', 'Durum', 'Not'].join(','),
    ...filteredPayments.value.map(p => [
      p.payment_date,
      getCustomerName(p.customer_id),
      p.amount,
      getMethodLabel(p.payment_method),
      getStatusLabel(p.status),
      p.notes || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `odemeler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>
