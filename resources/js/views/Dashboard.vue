<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Gösterge Paneli</h1>
        <p class="mt-2 text-sm text-gray-600">SalonPro Yönetim Sistemine Hoş Geldiniz</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">{{ formatDate(new Date().toISOString()) }}</span>
        <Button variant="secondary" size="sm" @click="refreshAll" :icon="ArrowPathIcon">
        </Button>
      </div>
    </div>

    <!-- Dönem Seçici -->
    <Card class="p-1">
      <div class="flex flex-wrap gap-1">
        <Button
          v-for="period in periods"
          :key="period.value"
          @click="selectedPeriod = period.value"
          :variant="selectedPeriod === period.value ? 'primary' : 'ghost'"
          size="sm"
          :label="period.label"
        />
      </div>
    </Card>

    <!-- Finansal Özet Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card class="border-l-4 border-l-success">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Toplam Gelir</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(totalIncome) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ selectedPeriodLabel }}</p>
          </div>
          <div class="p-3 rounded-full bg-success-light">
            <CurrencyDollarIcon class="h-6 w-6 text-success" />
          </div>
        </div>
      </Card>

      <Card class="border-l-4 border-l-danger">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Toplam Gider</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(totalExpense) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ selectedPeriodLabel }}</p>
          </div>
          <div class="p-3 rounded-full bg-danger-light">
            <BanknotesIcon class="h-6 w-6 text-danger" />
          </div>
        </div>
      </Card>

      <Card class="border-l-4 border-l-primary">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Net Kar / Zarar</p>
            <p :class="['text-2xl font-bold mt-1', netProfit >= 0 ? 'text-success' : 'text-danger']">{{ formatCurrency(netProfit) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ profitMargin }}% Kar Marjı</p>
          </div>
          <div class="p-3 rounded-full bg-primary-light">
            <ChartBarIcon class="h-6 w-6 text-primary" />
          </div>
        </div>
      </Card>

      <Card class="border-l-4 border-l-warning">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Ortalama Satış</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(averageSale) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ filteredSales.length }} Satış</p>
          </div>
          <div class="p-3 rounded-full bg-warning-light">
            <ShoppingBagIcon class="h-6 w-6 text-warning" />
          </div>
        </div>
      </Card>
    </div>

    <!-- İkinci İstatistik Satırı -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-50">
            <UserGroupIcon class="h-6 w-6 text-teal-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ customerStore.customers.length }}</p>
          </div>
        </div>
      </Card>

      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-primary-light">
            <CalendarDaysIcon class="h-6 w-6 text-primary" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Randevu</p>
            <p class="text-2xl font-bold text-gray-900">{{ filteredAppointments.length }}</p>
            <p class="text-xs text-warning">{{ pendingAppointments }} Bekliyor</p>
          </div>
        </div>
      </Card>

      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-50">
            <CubeIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Ürün</p>
            <p class="text-2xl font-bold text-gray-900">{{ productStore.products.length }}</p>
            <p v-if="lowStockProducts.length > 0" class="text-xs text-danger">{{ lowStockProducts.length }} Düşük Stok</p>
          </div>
        </div>
      </Card>

      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-50">
            <SparklesIcon class="h-6 w-6 text-amber-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Hizmet</p>
            <p class="text-2xl font-bold text-gray-900">{{ serviceStore.services.length }}</p>
            <p class="text-xs text-gray-500">{{ activeServices }} Aktif</p>
          </div>
        </div>
      </Card>
    </div>

    <!-- Grafikler ve Listeler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Gelir vs Gider Grafiği -->
      <Card title="Haftalık Gelir/Gider">
        <div class="h-64">
          <BarChart
            :labels="weeklyLabels"
            :datasets="[
              { label: 'Gelir', data: weeklyIncome, backgroundColor: 'rgba(16, 185, 129, 0.8)' },
              { label: 'Gider', data: weeklyExpense, backgroundColor: 'rgba(239, 68, 68, 0.8)' }
            ]"
            :show-legend="true"
          />
        </div>
      </Card>

      <!-- Hizmet Dağılımı -->
      <Card title="Hizmet Dağılımı">
        <div class="h-64">
          <DoughnutChart
            :labels="serviceLabels"
            :data="serviceData"
            :show-legend="true"
          />
        </div>
      </Card>
    </div>

    <!-- Trend Grafiği -->
    <Card title="Aylık Satış Trendi">
      <div class="h-64">
        <LineChart
          :labels="monthlyLabels"
          :datasets="[
            { label: 'Satış', data: monthlySales, borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)' }
          ]"
          :show-legend="false"
        />
      </div>
    </Card>

    <!-- Randevular ve Diğer Kartlar -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <Card>
        <template #header>
            <h3 class="text-lg font-semibold text-gray-900">Bugünkü Randevular</h3>
            <router-link to="/appointments" class="text-sm text-primary hover:text-primary-hover">Tümünü Gör</router-link>
        </template>
        
        <div class="max-h-48 overflow-y-auto space-y-2">
          <div v-if="todayAppointments.length > 0">
            <div v-for="appointment in todayAppointments.slice(0, 5)" :key="appointment.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                  {{ getInitials(getCustomerName(appointment)) }}
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ getCustomerName(appointment) }}</p>
                  <p class="text-xs text-gray-500">{{ appointment.service?.name }} • {{ getAppointmentTime(appointment) }}</p>
                </div>
              </div>
              <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusBadge(appointment.status)]">
                {{ getStatusLabel(appointment.status) }}
              </span>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <CalendarDaysIcon class="h-12 w-12 text-gray-300 mx-auto mb-2" />
            <p class="text-gray-500 text-sm">Bugün randevu yok</p>
          </div>
        </div>
      </Card>
    </div>

    <!-- Alt Kısım -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Düşük Stoklu Ürünler -->
      <Card>
        <template #header>
            <h3 class="text-lg font-semibold text-gray-900">Düşük Stoklu Ürünler</h3>
            <ExclamationTriangleIcon v-if="lowStockProducts.length > 0" class="h-5 w-5 text-danger" />
        </template>
        <div class="max-h-48 overflow-y-auto space-y-2">
          <div v-if="lowStockProducts.length > 0">
            <div v-for="product in lowStockProducts" :key="product.id"
                 class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-xs text-gray-500">Min: {{ product.min_stock_quantity }}</p>
              </div>
              <span class="px-3 py-1 bg-danger text-white rounded-lg text-sm font-bold">{{ product.stock_quantity }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <CheckCircleIcon class="h-12 w-12 text-success mx-auto mb-2" />
            <p class="text-gray-500 text-sm">Tüm ürünler yeterli stokta</p>
          </div>
        </div>
      </Card>

      <!-- Son Ödemeler -->
      <Card>
        <template #header>
            <h3 class="text-lg font-semibold text-gray-900">Son Ödemeler</h3>
            <router-link to="/payments" class="text-sm text-primary hover:text-primary-hover">Tümünü Gör</router-link>
        </template>
        
        <div class="max-h-48 overflow-y-auto space-y-2">
          <div v-if="recentPayments.length > 0">
            <div v-for="payment in recentPayments" :key="payment.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ payment.customer?.name || 'Bilinmiyor' }}</p>
                <p class="text-xs text-gray-500">{{ getPaymentMethodLabel(payment.payment_method) }}</p>
              </div>
              <span class="text-sm font-bold text-success">{{ formatCurrency(payment.amount) }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <BanknotesIcon class="h-12 w-12 text-gray-300 mx-auto mb-2" />
            <p class="text-gray-500 text-sm">Ödeme kaydı yok</p>
          </div>
        </div>
      </Card>

      <!-- Hızlı İşlemler -->
      <Card title="Hızlı İşlemler">
        <div class="space-y-2">
          <router-link to="/appointments" class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-primary font-medium transition group">
            <PlusCircleIcon class="h-5 w-5 group-hover:scale-110 transition-transform" />
            Yeni Randevu
          </router-link>
          <router-link to="/sales" class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg text-success font-medium transition group">
            <ShoppingBagIcon class="h-5 w-5 group-hover:scale-110 transition-transform" />
            Yeni Satış
          </router-link>
          <router-link to="/customers" class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-purple-700 font-medium transition group">
            <UserPlusIcon class="h-5 w-5 group-hover:scale-110 transition-transform" />
            Yeni Müşteri
          </router-link>
          <router-link to="/products" class="flex items-center gap-3 px-4 py-3 bg-orange-50 hover:bg-orange-100 rounded-lg text-orange-700 font-medium transition group">
            <CubeIcon class="h-5 w-5 group-hover:scale-110 transition-transform" />
            Ürün Yönetimi
          </router-link>
        </div>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed, ref } from 'vue'
import {
  CurrencyDollarIcon,
  BanknotesIcon,
  ChartBarIcon,
  ShoppingBagIcon,
  UserGroupIcon,
  CalendarDaysIcon,
  CubeIcon,
  SparklesIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  PlusCircleIcon,
  UserPlusIcon
} from '@heroicons/vue/24/outline'
import { useCustomerStore } from '@/stores/customer'
import { useAppointmentStore } from '@/stores/appointment'
import { useProductStore } from '@/stores/product'
import { useServiceStore } from '@/stores/service'
import { usePaymentStore } from '@/stores/payment'
import { useExpenseStore } from '@/stores/expense'
import { useSaleStore } from '@/stores/sale'
import BarChart from '@/components/charts/BarChart.vue'
import LineChart from '@/components/charts/LineChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'

const customerStore = useCustomerStore()
const appointmentStore = useAppointmentStore()
const productStore = useProductStore()
const serviceStore = useServiceStore()
const paymentStore = usePaymentStore()
const expenseStore = useExpenseStore()
const saleStore = useSaleStore()

const selectedPeriod = ref('today')

const periods = [
  { value: 'today', label: 'Bugün' },
  { value: 'week', label: 'Bu Hafta' },
  { value: 'month', label: 'Bu Ay' },
  { value: 'year', label: 'Bu Yıl' }
]

const selectedPeriodLabel = computed(() => periods.find(p => p.value === selectedPeriod.value)?.label || '')

const getDateRange = () => {
  const now = new Date()
  let startDate = new Date()
  switch (selectedPeriod.value) {
    case 'today': startDate.setHours(0, 0, 0, 0); break
    case 'week': startDate.setDate(now.getDate() - 7); break
    case 'month': startDate.setMonth(now.getMonth() - 1); break
    case 'year': startDate.setFullYear(now.getFullYear() - 1); break
  }
  return { startDate, endDate: now }
}

const isInDateRange = (dateString: string) => {
  const { startDate, endDate } = getDateRange()
  const date = new Date(dateString)
  return date >= startDate && date <= endDate
}

const filteredPayments = computed(() => paymentStore.payments.filter((p: any) => p.status === 'completed' && p.payment_date && isInDateRange(p.payment_date)))
const filteredExpenses = computed(() => expenseStore.expenses.filter((e: any) => e.expense_date && isInDateRange(e.expense_date)))
const filteredSales = computed(() => saleStore.sales.filter((s: any) => s.sale_date && isInDateRange(s.sale_date)))
const filteredAppointments = computed(() => appointmentStore.appointments.filter((a: any) => a.appointment_date && isInDateRange(a.appointment_date)))

const totalIncome = computed(() => {
  const payments = filteredPayments.value.reduce((sum: number, p: any) => sum + parseFloat(p.amount || 0), 0)
  const sales = filteredSales.value.reduce((sum: number, s: any) => sum + parseFloat(s.total || 0), 0)
  return payments + sales
})
const totalExpense = computed(() => filteredExpenses.value.reduce((sum: number, e: any) => sum + parseFloat(e.amount || 0), 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)
const profitMargin = computed(() => totalIncome.value === 0 ? 0 : ((netProfit.value / totalIncome.value) * 100).toFixed(1))
const averageSale = computed(() => filteredSales.value.length === 0 ? 0 : totalIncome.value / filteredSales.value.length)
const pendingAppointments = computed(() => filteredAppointments.value.filter((a: any) => a.status === 'pending').length)
const activeServices = computed(() => serviceStore.services.filter((s: any) => s.is_active).length)
const todayAppointments = computed(() => { const today = new Date().toISOString().split('T')[0]; return appointmentStore.appointments.filter((apt: any) => apt.appointment_date?.startsWith(today)) })
const lowStockProducts = computed(() => productStore.products.filter((p: any) => p.stock_quantity <= p.min_stock_quantity).slice(0, 5))
const recentPayments = computed(() => [...paymentStore.payments].filter((p: any) => p.created_at).sort((a: any, b: any) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()).slice(0, 5))

// Haftalık grafik verileri
const weeklyLabels = computed(() => {
  const days = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
  const today = new Date().getDay()
  return [...days.slice(today), ...days.slice(0, today)]
})

const weeklyIncome = computed(() => {
  const data = Array(7).fill(0)
  filteredPayments.value.forEach((p: any) => {
    const day = new Date(p.payment_date).getDay()
    data[day] += parseFloat(p.amount || 0)
  })
  return data
})

const weeklyExpense = computed(() => {
  const data = Array(7).fill(0)
  filteredExpenses.value.forEach((e: any) => {
    const day = new Date(e.expense_date).getDay()
    data[day] += parseFloat(e.amount || 0)
  })
  return data
})

// Hizmet dağılımı
const serviceLabels = computed(() => serviceStore.services.slice(0, 5).map((s: any) => s.name || 'Hizmet'))
const serviceData = computed(() => {
  const counts: Record<string, number> = {}
  filteredAppointments.value.forEach((a: any) => {
    const name = a.service?.name || 'Diğer'
    counts[name] = (counts[name] || 0) + 1
  })
  return serviceStore.services.slice(0, 5).map((s: any) => counts[s.name] || 0)
})

// Aylık satış trendi
const monthlyLabels = computed(() => {
  const months = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
  return months
})

const monthlySales = computed(() => {
  const data = Array(12).fill(0)
  saleStore.sales.forEach((s: any) => {
    if (s.sale_date) {
      const month = new Date(s.sale_date).getMonth()
      data[month] += parseFloat(s.total || 0)
    }
  })
  return data
})

const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount)
const formatDate = (d: string) => new Intl.DateTimeFormat('tr-TR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(d))
const getInitials = (name: string) => name?.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2) || 'M'
const getCustomerName = (apt: any) => apt.customer?.first_name ? `${apt.customer.first_name} ${apt.customer.last_name || ''}` : apt.customer?.name || 'Bilinmiyor'
const getStatusLabel = (status: string) => ({ pending: 'Bekliyor', confirmed: 'Onaylandı', completed: 'Tamamlandı', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ pending: 'bg-yellow-100 text-yellow-800', confirmed: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' }[status] || 'bg-gray-100 text-gray-800')
const getPaymentMethodLabel = (method: string) => ({ cash: 'Nakit', credit_card: 'Kredi Kartı', debit_card: 'Banka Kartı', bank_transfer: 'Havale' }[method] || method)
const getAppointmentTime = (apt: any) => apt.appointment_time || apt.appointment_date?.split('T')[1]?.slice(0, 5) || ''

const refreshAll = async () => {
  await Promise.all([
    customerStore.fetchCustomers(),
    appointmentStore.fetchAppointments(),
    productStore.fetchProducts(),
    serviceStore.fetchServices(),
    paymentStore.fetchPayments(),
    expenseStore.fetchExpenses(),
    saleStore.fetchSales()
  ])
}

onMounted(() => { refreshAll() })
</script>

