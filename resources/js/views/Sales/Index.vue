<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Satışlar</h1>
        <p class="mt-2 text-sm text-gray-600">Satış işlemlerinizi yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportSales"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Satış
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-rose-100">
            <ShoppingCartIcon class="h-6 w-6 text-rose-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Satış</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.count }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <BanknotesIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Gelir</p>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.totalRevenue) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ReceiptPercentIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam İndirim</p>
            <p class="text-2xl font-bold text-yellow-600">{{ formatCurrency(stats.totalDiscount) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <CalculatorIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ort. Satış</p>
            <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(stats.avgSale) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <CalendarDaysIcon class="h-6 w-6 text-purple-600" />
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
              placeholder="Müşteri ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
            />
          </div>

          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="employee in employeeStore.employees" :key="employee.id" :value="employee.id">
              {{ employee.first_name }} {{ employee.last_name }}
            </option>
          </select>

          <!-- Tarih Aralığı -->
          <div class="flex items-center gap-2">
            <input
              v-model="filters.startDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
            />
            <span class="text-gray-400">-</span>
            <input
              v-model="filters.endDate"
              type="date"
              class="rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
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
    <div v-if="saleStore.loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-rose-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Tablo -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çalışan</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ara Toplam</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İndirim</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">KDV</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="sale in filteredSales" :key="sale.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ formatDate(sale.sale_date) }}</p>
                <p class="text-xs text-gray-500">{{ formatTime(sale.created_at) }}</p>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-900">{{ getCustomerName(sale.customer_id) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-600">{{ getEmployeeName(sale.employee_id) }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
              {{ formatCurrency(sale.subtotal) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <span v-if="sale.discount > 0" class="text-sm text-yellow-600 font-medium">
                -{{ formatCurrency(sale.discount) }}
              </span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
              {{ formatCurrency(sale.tax) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <span class="text-lg font-bold text-rose-600">{{ formatCurrency(sale.total) }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewSale(sale)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Görüntüle"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="openEditModal(sale)"
                  class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="handleDelete(sale.id)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredSales.length === 0" class="p-12 text-center">
        <ShoppingCartIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Satış bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-rose-600 hover:text-rose-700 font-medium">
          Satış ekleyin
        </button>
      </div>
    </div>

    <!-- Satış Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ isEdit ? 'Satış Düzenle' : 'Yeni Satış' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <!-- Müşteri ve Çalışan -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
              <select
                v-model="form.customer_id"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
              >
                <option value="">Müşteri Seçin</option>
                <option v-for="customer in customerStore.customers" :key="customer.id" :value="customer.id">
                  {{ customer.first_name }} {{ customer.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
              <select
                v-model="form.employee_id"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
              >
                <option value="">Çalışan Seçin</option>
                <option v-for="employee in employeeStore.employees" :key="employee.id" :value="employee.id">
                  {{ employee.first_name }} {{ employee.last_name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Tarih -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Satış Tarihi *</label>
            <input
              v-model="form.sale_date"
              type="date"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
            />
          </div>

          <!-- Fiyatlandırma -->
          <div class="bg-gray-50 rounded-lg p-4 space-y-4">
            <h4 class="font-medium text-gray-900">Fiyatlandırma</h4>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ara Toplam *</label>
                <input
                  v-model.number="form.subtotal"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  @input="calculateTotal"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">İndirim</label>
                <input
                  v-model.number="form.discount"
                  type="number"
                  step="0.01"
                  min="0"
                  @input="calculateTotal"
                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">KDV (%{{ taxRate }})</label>
                <input
                  v-model.number="form.tax"
                  type="number"
                  step="0.01"
                  min="0"
                  readonly
                  class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Genel Toplam</label>
                <input
                  v-model.number="form.total"
                  type="number"
                  readonly
                  class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 text-lg font-bold text-rose-600"
                />
              </div>
            </div>
          </div>

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="Satış ile ilgili notlar..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
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
              :disabled="saleStore.loading"
              class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saleStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Satış Detay Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Satış Detayı</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>
        <div v-if="selectedSale" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Müşteri</p>
              <p class="font-medium">{{ getCustomerName(selectedSale.customer_id) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Çalışan</p>
              <p class="font-medium">{{ getEmployeeName(selectedSale.employee_id) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tarih</p>
              <p class="font-medium">{{ formatDate(selectedSale.sale_date) }}</p>
            </div>
          </div>
          <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">Ara Toplam</span>
              <span>{{ formatCurrency(selectedSale.subtotal) }}</span>
            </div>
            <div v-if="selectedSale.discount > 0" class="flex justify-between text-yellow-600">
              <span>İndirim</span>
              <span>-{{ formatCurrency(selectedSale.discount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">KDV</span>
              <span>{{ formatCurrency(selectedSale.tax) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span>Toplam</span>
              <span class="text-rose-600">{{ formatCurrency(selectedSale.total) }}</span>
            </div>
          </div>
          <div v-if="selectedSale.notes" class="border-t pt-4">
            <p class="text-sm text-gray-500">Not</p>
            <p class="text-gray-700">{{ selectedSale.notes }}</p>
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
  ShoppingCartIcon,
  BanknotesIcon,
  ReceiptPercentIcon,
  CalculatorIcon,
  CalendarDaysIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useSaleStore } from '@/stores/sale'
import { useCustomerStore } from '@/stores/customer'
import { useEmployeeStore } from '@/stores/employee'

interface Sale {
  id: string
  customer_id: string
  employee_id: string
  sale_date: string
  subtotal: number
  discount: number
  tax: number
  total: number
  notes?: string
  created_at?: string
}

const saleStore = useSaleStore()
const customerStore = useCustomerStore()
const employeeStore = useEmployeeStore()

// State
const showModal = ref(false)
const showDetailModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')
const selectedSale = ref<Sale | null>(null)
const taxRate = 20

const filters = ref({
  employeeId: '',
  startDate: '',
  endDate: ''
})

const stats = ref({
  count: 0,
  totalRevenue: 0,
  totalDiscount: 0,
  avgSale: 0,
  thisMonth: 0
})

const form = ref({
  customer_id: '',
  employee_id: '',
  sale_date: new Date().toISOString().split('T')[0],
  subtotal: 0,
  discount: 0,
  tax: 0,
  total: 0,
  notes: ''
})

// Computed
const filteredSales = computed(() => {
  let result = saleStore.sales as Sale[]

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(s => getCustomerName(s.customer_id).toLowerCase().includes(searchLower))
  }

  if (filters.value.employeeId) {
    result = result.filter(s => s.employee_id === filters.value.employeeId)
  }

  if (filters.value.startDate) {
    result = result.filter(s => s.sale_date >= filters.value.startDate)
  }

  if (filters.value.endDate) {
    result = result.filter(s => s.sale_date <= filters.value.endDate)
  }

  return result.sort((a, b) => new Date(b.sale_date).getTime() - new Date(a.sale_date).getTime())
})

// Helpers
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const formatDate = (dateString: string) => new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(dateString))
const formatTime = (dateString?: string) => dateString ? new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateString)) : ''

const getCustomerName = (id: string) => { const c = customerStore.customers.find((x: any) => x.id === id); return c ? `${c.first_name} ${c.last_name}` : '-' }
const getEmployeeName = (id: string) => { const e = employeeStore.employees.find((x: any) => x.id === id); return e ? `${e.first_name} ${e.last_name}` : '-' }

const calculateTotal = () => {
  const subtotal = form.value.subtotal || 0
  const discount = form.value.discount || 0
  const taxableAmount = subtotal - discount
  form.value.tax = taxableAmount * (taxRate / 100)
  form.value.total = taxableAmount + form.value.tax
}

const updateStats = () => {
  const sales = saleStore.sales as Sale[]
  const now = new Date()
  const thisMonth = sales.filter(s => {
    const d = new Date(s.sale_date)
    return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
  })

  stats.value.count = sales.length
  stats.value.totalRevenue = sales.reduce((acc, s) => acc + Number(s.total), 0)
  stats.value.totalDiscount = sales.reduce((acc, s) => acc + Number(s.discount), 0)
  stats.value.avgSale = sales.length > 0 ? stats.value.totalRevenue / sales.length : 0
  stats.value.thisMonth = thisMonth.reduce((acc, s) => acc + Number(s.total), 0)
}

// Modal Methods
const openCreateModal = () => {
  form.value = { customer_id: '', employee_id: '', sale_date: new Date().toISOString().split('T')[0], subtotal: 0, discount: 0, tax: 0, total: 0, notes: '' }
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (sale: Sale) => {
  form.value = { customer_id: sale.customer_id || '', employee_id: sale.employee_id || '', sale_date: sale.sale_date || '', subtotal: sale.subtotal || 0, discount: sale.discount || 0, tax: sale.tax || 0, total: sale.total || 0, notes: sale.notes || '' }
  isEdit.value = true
  editingId.value = sale.id
  showModal.value = true
}

const viewSale = (sale: Sale) => { selectedSale.value = sale; showDetailModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await saleStore.updateSale(editingId.value, form.value)
    } else {
      await saleStore.createSale(form.value)
    }
    closeModal()
    updateStats()
  } catch (error) {
    console.error('Satış kaydedilemedi:', error)
    alert('Satış kaydedilemedi')
  }
}

const handleDelete = async (id: string) => {
  if (!confirm('Bu satışı silmek istediğinizden emin misiniz?')) return
  try {
    await saleStore.deleteSale(id)
    updateStats()
  } catch (error) {
    console.error('Satış silinemedi:', error)
  }
}

const loadData = async () => {
  await saleStore.fetchSales()
  await customerStore.fetchCustomers()
  await employeeStore.fetchEmployees()
  updateStats()
}

const exportSales = () => {
  const csvContent = [
    ['Tarih', 'Müşteri', 'Çalışan', 'Ara Toplam', 'İndirim', 'KDV', 'Toplam', 'Not'].join(','),
    ...filteredSales.value.map(s => [
      s.sale_date,
      getCustomerName(s.customer_id),
      getEmployeeName(s.employee_id),
      s.subtotal,
      s.discount,
      s.tax,
      s.total,
      s.notes || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `satislar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>
