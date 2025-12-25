<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Faturalar</h1>
        <p class="mt-2 text-sm text-gray-600">Fatura yönetimi ve takibi</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportInvoices"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Fatura
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <DocumentTextIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Fatura</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ödenen</p>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.paidAmount) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bekleyen</p>
            <p class="text-2xl font-bold text-yellow-600">{{ formatCurrency(stats.pendingAmount) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <ExclamationCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Gecikmiş</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.overdueCount }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <BanknotesIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bu Ay</p>
            <p class="text-2xl font-bold text-purple-600">{{ formatCurrency(stats.thisMonth) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <!-- Arama -->
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Fatura no veya müşteri..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>

          <!-- Durum Filtreleri -->
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

          <!-- Tarih Aralığı -->
          <div class="flex items-center gap-2">
            <input
              v-model="filters.startDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
            <span class="text-gray-400">-</span>
            <input
              v-model="filters.endDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>
        </div>

        <button
          @click="loadData"
          class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Tablo -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fatura No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vade</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm font-medium text-blue-600">{{ invoice.invoice_number }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-900">{{ getCustomerName(invoice.customer_id) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDate(invoice.issue_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p :class="['text-sm', isOverdue(invoice) ? 'text-red-600 font-medium' : 'text-gray-600']">
                {{ formatDate(invoice.due_date) }}
              </p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <p class="text-lg font-bold text-gray-900">{{ formatCurrency(invoice.total_amount) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-3 py-1 text-xs rounded-full font-semibold', getStatusBadge(invoice.status)]">
                {{ getStatusLabel(invoice.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewInvoice(invoice)"
                  class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  title="Görüntüle"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  v-if="invoice.status === 'pending'"
                  @click="markAsPaid(invoice)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Ödendi"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  @click="openEditModal(invoice)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="handleDelete(invoice.id)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredInvoices.length === 0" class="p-12 text-center">
        <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Fatura bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
          Fatura ekleyin
        </button>
      </div>
    </div>

    <!-- Fatura Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEdit ? 'Fatura Düzenle' : 'Yeni Fatura' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Müşteri ve Şube -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
              <select v-model="form.customer_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Müşteri Seçin</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                  {{ customer.first_name }} {{ customer.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
              <select v-model="form.branch_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Şube Seçin</option>
                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                  {{ branch.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Tarihler -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fatura Tarihi *</label>
              <input v-model="form.issue_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Vade Tarihi *</label>
              <input v-model="form.due_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
          </div>

          <!-- Fatura Kalemleri -->
          <div class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-900 mb-3">Fatura Kalemleri</label>
            <div class="space-y-2">
              <div v-for="(item, index) in form.items" :key="index" class="flex gap-2 items-center bg-white p-2 rounded-lg">
                <input v-model="item.description" placeholder="Açıklama" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <input v-model.number="item.quantity" type="number" min="1" placeholder="Adet" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <input v-model.number="item.unit_price" type="number" step="0.01" placeholder="Fiyat" class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                <span class="w-24 text-right font-medium text-sm">{{ formatCurrency(item.quantity * item.unit_price) }}</span>
                <button type="button" @click="removeItem(index)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
            <button type="button" @click="addItem" class="mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center">
              <PlusIcon class="h-4 w-4 mr-1" /> Kalem Ekle
            </button>
          </div>

          <!-- Toplamlar -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Ara Toplam</span>
                <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                  <span class="text-gray-600">KDV (%</span>
                  <input v-model.number="form.tax_rate" type="number" min="0" max="100" class="w-16 px-2 py-1 border border-gray-300 rounded text-sm" />
                  <span class="text-gray-600">)</span>
                </div>
                <span class="font-medium">{{ formatCurrency(taxAmount) }}</span>
              </div>
              <div class="flex justify-between pt-2 border-t border-gray-200">
                <span class="text-lg font-bold">Genel Toplam</span>
                <span class="text-xl font-bold text-blue-600">{{ formatCurrency(totalAmount) }}</span>
              </div>
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="form.notes" rows="2" placeholder="Fatura ile ilgili notlar..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
              İptal
            </button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Fatura Detay Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Fatura Detayı</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>
        <div v-if="selectedInvoice" class="p-6 space-y-4">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-500">Fatura No</p>
              <p class="text-lg font-bold text-blue-600">{{ selectedInvoice.invoice_number }}</p>
            </div>
            <span :class="['px-3 py-1 text-xs rounded-full font-semibold', getStatusBadge(selectedInvoice.status)]">
              {{ getStatusLabel(selectedInvoice.status) }}
            </span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Müşteri</p>
              <p class="font-medium">{{ getCustomerName(selectedInvoice.customer_id) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Fatura Tarihi</p>
              <p class="font-medium">{{ formatDate(selectedInvoice.issue_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Vade Tarihi</p>
              <p :class="['font-medium', isOverdue(selectedInvoice) ? 'text-red-600' : '']">{{ formatDate(selectedInvoice.due_date) }}</p>
            </div>
          </div>
          <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">Ara Toplam</span>
              <span>{{ formatCurrency(selectedInvoice.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">KDV</span>
              <span>{{ formatCurrency(selectedInvoice.tax_amount) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span>Toplam</span>
              <span class="text-blue-600">{{ formatCurrency(selectedInvoice.total_amount) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  ExclamationCircleIcon,
  BanknotesIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  CheckIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useInvoiceStore } from '@/stores/invoice'
import { useCustomerStore } from '@/stores/customer'
import { useBranchStore } from '@/stores/branch'

interface Invoice {
  id: string
  invoice_number: string
  customer_id: string
  branch_id: string
  issue_date: string
  due_date: string
  subtotal: number
  tax_rate: number
  tax_amount: number
  total_amount: number
  status: string
  notes?: string
  items?: { description: string; quantity: number; unit_price: number }[]
}

const invoiceStore = useInvoiceStore()
const customerStore = useCustomerStore()
const branchStore = useBranchStore()

// State
const loading = ref(false)
const showModal = ref(false)
const showDetailModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')
const selectedInvoice = ref<Invoice | null>(null)

const filters = ref({ status: '', startDate: '', endDate: '' })

const stats = ref({
  total: 0,
  paidAmount: 0,
  pendingAmount: 0,
  overdueCount: 0,
  thisMonth: 0
})

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-blue-600 text-white' },
  { value: 'paid', label: 'Ödendi', activeClass: 'bg-green-600 text-white' },
  { value: 'pending', label: 'Bekliyor', activeClass: 'bg-yellow-600 text-white' },
  { value: 'overdue', label: 'Gecikmiş', activeClass: 'bg-red-600 text-white' },
  { value: 'draft', label: 'Taslak', activeClass: 'bg-gray-600 text-white' }
]

const form = ref({
  customer_id: '',
  branch_id: '',
  issue_date: new Date().toISOString().split('T')[0],
  due_date: '',
  tax_rate: 20,
  notes: '',
  items: [{ description: '', quantity: 1, unit_price: 0 }]
})

// Computed
const invoices = computed(() => invoiceStore.invoices as Invoice[] || [])
const customers = computed(() => customerStore.customers || [])
const branches = computed(() => branchStore.branches || [])

const filteredInvoices = computed(() => {
  let result = invoices.value
  if (search.value) {
    const s = search.value.toLowerCase()
    result = result.filter(i => i.invoice_number?.toLowerCase().includes(s) || getCustomerName(i.customer_id).toLowerCase().includes(s))
  }
  if (filters.value.status) result = result.filter(i => i.status === filters.value.status)
  if (filters.value.startDate) result = result.filter(i => i.issue_date >= filters.value.startDate)
  if (filters.value.endDate) result = result.filter(i => i.issue_date <= filters.value.endDate)
  return result.sort((a, b) => new Date(b.issue_date).getTime() - new Date(a.issue_date).getTime())
})

const subtotal = computed(() => form.value.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0))
const taxAmount = computed(() => subtotal.value * (form.value.tax_rate / 100))
const totalAmount = computed(() => subtotal.value + taxAmount.value)

// Helpers
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (dateString: string) => dateString ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(dateString)) : '-'
const getCustomerName = (id: string) => { const c = customers.value.find((x: any) => x.id === id); return c ? `${c.first_name} ${c.last_name}` : '-' }
const isOverdue = (invoice: Invoice) => invoice.status === 'pending' && new Date(invoice.due_date) < new Date()
const getStatusLabel = (status: string) => ({ draft: 'Taslak', pending: 'Bekliyor', paid: 'Ödendi', overdue: 'Gecikmiş', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ draft: 'bg-gray-100 text-gray-800', pending: 'bg-yellow-100 text-yellow-800', paid: 'bg-green-100 text-green-800', overdue: 'bg-red-100 text-red-800', cancelled: 'bg-gray-100 text-gray-500' }[status] || 'bg-gray-100')

const updateStats = () => {
  const now = new Date()
  const thisMonth = invoices.value.filter(i => { const d = new Date(i.issue_date); return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear() })
  stats.value.total = invoices.value.length
  stats.value.paidAmount = invoices.value.filter(i => i.status === 'paid').reduce((acc, i) => acc + Number(i.total_amount), 0)
  stats.value.pendingAmount = invoices.value.filter(i => i.status === 'pending').reduce((acc, i) => acc + Number(i.total_amount), 0)
  stats.value.overdueCount = invoices.value.filter(i => isOverdue(i)).length
  stats.value.thisMonth = thisMonth.reduce((acc, i) => acc + Number(i.total_amount), 0)
}

// Modal Methods
const openCreateModal = () => { form.value = { customer_id: '', branch_id: '', issue_date: new Date().toISOString().split('T')[0], due_date: '', tax_rate: 20, notes: '', items: [{ description: '', quantity: 1, unit_price: 0 }] }; isEdit.value = false; editingId.value = null; showModal.value = true }
const openEditModal = (invoice: Invoice) => { form.value = { customer_id: invoice.customer_id || '', branch_id: invoice.branch_id || '', issue_date: invoice.issue_date || '', due_date: invoice.due_date || '', tax_rate: invoice.tax_rate || 20, notes: invoice.notes || '', items: invoice.items?.length ? [...invoice.items] : [{ description: '', quantity: 1, unit_price: 0 }] }; isEdit.value = true; editingId.value = invoice.id; showModal.value = true }
const viewInvoice = (invoice: Invoice) => { selectedInvoice.value = invoice; showDetailModal.value = true }
const closeModal = () => { showModal.value = false }

const addItem = () => { form.value.items.push({ description: '', quantity: 1, unit_price: 0 }) }
const removeItem = (index: number) => { if (form.value.items.length > 1) form.value.items.splice(index, 1) }

const handleSubmit = async () => {
  loading.value = true
  try {
    const data = { ...form.value, subtotal: subtotal.value, tax_amount: taxAmount.value, total_amount: totalAmount.value, status: 'pending' }
    if (isEdit.value && editingId.value) { await invoiceStore.updateInvoice(editingId.value, data) } else { await invoiceStore.createInvoice(data) }
    closeModal()
    await loadData()
  } catch (error) { console.error('Fatura kaydedilemedi:', error); alert('Fatura kaydedilemedi') }
  finally { loading.value = false }
}

const markAsPaid = async (invoice: Invoice) => { if (!confirm('Bu faturayı ödendi olarak işaretlemek istiyor musunuz?')) return; try { await invoiceStore.updateInvoice(invoice.id, { status: 'paid', paid_at: new Date().toISOString() }); await loadData() } catch (e) { console.error(e) } }
const handleDelete = async (id: string) => { if (!confirm('Bu faturayı silmek istediğinizden emin misiniz?')) return; try { await invoiceStore.deleteInvoice(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => { loading.value = true; try { await Promise.all([invoiceStore.fetchInvoices(), customerStore.fetchCustomers(), branchStore.fetchBranches()]); updateStats() } finally { loading.value = false } }

const exportInvoices = () => {
  const csvContent = [
    ['Fatura No', 'Müşteri', 'Tarih', 'Vade', 'Tutar', 'Durum'].join(','),
    ...filteredInvoices.value.map(i => [i.invoice_number, getCustomerName(i.customer_id), i.issue_date, i.due_date, i.total_amount, getStatusLabel(i.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `faturalar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>