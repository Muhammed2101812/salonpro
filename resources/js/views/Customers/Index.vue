<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteriler</h1>
        <p class="mt-2 text-sm text-gray-600">Salon müşterilerinizi yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <Button variant="secondary" @click="exportCustomers" :icon="ArrowDownTrayIcon" label="Dışa Aktar" />
        <Button variant="success" @click="openCreateModal" :icon="PlusIcon" label="Müşteri Ekle" />
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <UserGroupIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </Card>
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <SparklesIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Yeni (Bu Ay)</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.newThisMonth }}</p>
          </div>
        </div>
      </Card>
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <HeartIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Sadık Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.loyal }}</p>
          </div>
        </div>
      </Card>
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Riskli (90+ gün)</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.atRisk }}</p>
          </div>
        </div>
      </Card>
      <Card>
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-pink-100">
            <CakeIcon class="h-6 w-6 text-pink-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bu Ay Doğum Günü</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.birthdayThisMonth }}</p>
          </div>
        </div>
      </Card>
    </div>

    <!-- Filtreler -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
          <!-- Arama -->
          <div class="w-full lg:w-64">
             <Input v-model="search" placeholder="Ad, telefon veya e-posta ara...">
                 <template #prefix>
                     <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                 </template>
             </Input>
          </div>

          <!-- Şube Filtresi -->
          <div class="w-full lg:w-48">
            <Select
                v-model="filters.branchId"
                :options="[{ id: '', name: 'Tüm Şubeler' }, ...branches]"
                optionLabel="name"
                optionValue="id"
            />
          </div>

          <!-- Cinsiyet Filtresi -->
          <div class="w-full lg:w-40">
             <Select
                v-model="filters.gender"
                :options="[{ value: '', label: 'Tüm Cinsiyetler' }, { value: 'male', label: 'Erkek' }, { value: 'female', label: 'Kadın' }]"
             />
          </div>

          <!-- Segment Filtresi -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="segment in customerSegments"
              :key="segment.value"
              @click="filters.segment = filters.segment === segment.value ? '' : segment.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.segment === segment.value
                  ? segment.activeClass
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ segment.label }}
            </button>
          </div>
        </div>

        <div class="flex gap-2">
           <Button variant="ghost" size="sm" @click="viewMode = viewMode === 'grid' ? 'table' : 'grid'">
             <component :is="viewMode === 'grid' ? ListBulletIcon : Squares2X2Icon" class="h-5 w-5" />
           </Button>
           <Button variant="ghost" size="sm" @click="loadData">
             <ArrowPathIcon class="h-5 w-5" />
           </Button>
        </div>
      </div>
    </Card>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Grid Görünümü -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div
        v-for="customer in filteredCustomers"
        :key="customer.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
        @click="viewCustomer(customer)"
      >
        <div class="p-5">
          <div class="flex items-center gap-4 mb-4">
            <div
              :class="[
                'flex-shrink-0 h-14 w-14 rounded-full flex items-center justify-center text-white font-bold text-lg',
                customer.gender === 'female' ? 'bg-gradient-to-br from-pink-500 to-rose-500' : 'bg-gradient-to-br from-blue-500 to-cyan-500'
              ]"
            >
              {{ getInitials(customer.first_name, customer.last_name) }}
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="font-semibold text-gray-900 truncate">{{ customer.first_name }} {{ customer.last_name }}</h4>
              <p class="text-sm text-gray-500">{{ customer.phone }}</p>
            </div>
            <div v-if="getCustomerSegment(customer)" class="flex-shrink-0">
              <span :class="['px-2 py-1 text-xs font-medium rounded-full', getSegmentBadge(getCustomerSegment(customer))]">
                {{ getSegmentLabel(getCustomerSegment(customer)) }}
              </span>
            </div>
          </div>

          <div class="space-y-2 text-sm">
            <div v-if="customer.email" class="flex items-center gap-2 text-gray-600">
              <EnvelopeIcon class="h-4 w-4 text-gray-400" />
              <span class="truncate">{{ customer.email }}</span>
            </div>
            <div v-if="customer.branch?.name" class="flex items-center gap-2 text-gray-600">
              <BuildingStorefrontIcon class="h-4 w-4 text-gray-400" />
              <span>{{ customer.branch.name }}</span>
            </div>
            <div v-if="customer.last_visit" class="flex items-center gap-2 text-gray-600">
              <CalendarIcon class="h-4 w-4 text-gray-400" />
              <span>Son ziyaret: {{ formatDate(customer.last_visit) }}</span>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <span>{{ customer.total_visits || 0 }} ziyaret</span>
              <span>{{ formatCurrency(customer.total_spent || 0) }}</span>
            </div>
            <div class="flex gap-1">
              <Button variant="ghost" size="sm" @click.stop="editCustomer(customer)">
                  <PencilIcon class="h-4 w-4 text-success" />
              </Button>
              <Button variant="ghost" size="sm" @click.stop="deleteCustomer(customer)">
                  <TrashIcon class="h-4 w-4 text-danger" />
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tablo Görünümü -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredCustomers"
        :exportable="true"
        export-filename="musteriler"
        export-title="Müşteri Listesi"
    >
        <template #cell-customer="{ row }">
              <div class="flex items-center">
                <div
                  :class="[
                    'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-medium',
                    row.gender === 'female' ? 'bg-gradient-to-br from-pink-500 to-rose-500' : 'bg-gradient-to-br from-blue-500 to-cyan-500'
                  ]"
                >
                  {{ getInitials(row.first_name, row.last_name) }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">{{ row.first_name }} {{ row.last_name }}</p>
                  <p class="text-xs text-gray-500">{{ getGenderLabel(row.gender) }}</p>
                </div>
              </div>
        </template>
        <template #cell-contact="{ row }">
              <div class="text-sm text-gray-900">{{ row.phone }}</div>
              <div v-if="row.email" class="text-xs text-gray-500">{{ row.email }}</div>
        </template>
        <template #cell-branch="{ row }">
             {{ row.branch?.name || '-' }}
        </template>
        <template #cell-visits="{ row }">
              <div class="text-sm text-gray-900">{{ row.total_visits || 0 }} kez</div>
              <div v-if="row.last_visit" class="text-xs text-gray-500">
                Son: {{ formatDateShort(row.last_visit) }}
              </div>
        </template>
        <template #cell-spent="{ row }">
              {{ formatCurrency(row.total_spent || 0) }}
        </template>
        <template #cell-segment="{ row }">
              <span
                v-if="getCustomerSegment(row)"
                :class="['px-2 py-1 text-xs font-medium rounded-full', getSegmentBadge(getCustomerSegment(row))]"
              >
                {{ getSegmentLabel(getCustomerSegment(row)) }}
              </span>
              <span v-else class="text-gray-400">-</span>
        </template>
        <template #actions="{ row }">
            <div class="flex items-center justify-end gap-2">
                <Button variant="ghost" size="sm" @click="viewCustomer(row)">
                   <EyeIcon class="h-4 w-4 text-gray-600" />
                </Button>
                <Button variant="ghost" size="sm" @click="editCustomer(row)">
                   <PencilIcon class="h-4 w-4 text-success" />
                </Button>
                <Button variant="ghost" size="sm" @click="deleteCustomer(row)">
                   <TrashIcon class="h-4 w-4 text-danger" />
                </Button>
            </div>
        </template>
    </DataTable>

    <!-- Müşteri Ekleme/Düzenleme Modal -->
    <Modal
        v-model="showModal"
        :title="editingCustomer ? 'Müşteri Düzenle' : 'Yeni Müşteri'"
    >
        <form @submit.prevent="saveCustomer" class="space-y-5">
          <!-- Ad Soyad -->
          <div class="grid grid-cols-2 gap-4">
            <Input v-model="formData.first_name" label="Ad" required />
            <Input v-model="formData.last_name" label="Soyad" required />
          </div>

          <!-- Telefon ve E-posta -->
          <div class="grid grid-cols-2 gap-4">
            <Input v-model="formData.phone" label="Telefon" type="tel" placeholder="05XX XXX XX XX" required />
            <Input v-model="formData.email" label="E-posta" type="email" />
          </div>

          <!-- Şube ve Cinsiyet -->
          <div class="grid grid-cols-2 gap-4">
            <Select
                v-model="formData.branch_id"
                label="Şube"
                required
                :options="branches"
                optionLabel="name"
                optionValue="id"
                placeholder="Şube Seçin"
            />
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Cinsiyet</label>
              <div class="flex gap-2">
                <Button
                    type="button"
                    :variant="formData.gender === 'male' ? 'primary' : 'outline'"
                    class="flex-1"
                    @click="formData.gender = 'male'"
                    label="Erkek"
                />
                 <Button
                    type="button"
                    :variant="formData.gender === 'female' ? 'primary' : 'outline'"
                    class="flex-1"
                    @click="formData.gender = 'female'"
                    label="Kadın"
                />
              </div>
            </div>
          </div>

          <!-- Doğum Tarihi -->
          <Input v-model="formData.date_of_birth" label="Doğum Tarihi" type="date" />

          <!-- Notlar -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea
              v-model="formData.notes"
              rows="3"
              placeholder="Müşteri hakkında özel notlar (alerji, tercihler vb.)"
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
            ></textarea>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <Button variant="secondary" @click="closeModal" label="İptal" />
            <Button
                type="submit"
                variant="success"
                :loading="saving"
                :label="saving ? 'Kaydediliyor...' : 'Kaydet'"
            />
          </div>
        </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  PlusIcon,
  UserGroupIcon,
  SparklesIcon,
  HeartIcon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  EyeIcon,
  EnvelopeIcon,
  BuildingStorefrontIcon,
  CalendarIcon,
  ListBulletIcon,
  Squares2X2Icon,
  CakeIcon
} from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import DataTable from '@/components/ui/DataTable.vue'
import Modal from '@/components/ui/Modal.vue'
import Card from '@/components/ui/Card.vue'
import { useCustomerStore } from '@/stores/customer'
import { useBranchStore } from '@/stores/branch'

interface Customer {
  id: string
  first_name: string
  last_name: string
  phone: string
  email?: string
  gender?: 'male' | 'female'
  date_of_birth?: string
  branch_id: string
  branch?: { id: string; name: string }
  notes?: string
  total_visits?: number
  total_spent?: number
  last_visit?: string
  created_at: string
}

interface Branch {
  id: string
  name: string
}

// Table Definition
const tableColumns = [
    { key: 'customer', label: 'Müşteri' },
    { key: 'contact', label: 'İletişim' },
    { key: 'branch', label: 'Şube' },
    { key: 'visits', label: 'Ziyaret' },
    { key: 'spent', label: 'Harcama' },
    { key: 'segment', label: 'Segment' },
]

const router = useRouter()
const customerStore = useCustomerStore()
const branchStore = useBranchStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingCustomer = ref<Customer | null>(null)
const viewMode = ref<'grid' | 'table'>('grid')
const search = ref('')

const customers = ref<Customer[]>([])
const branches = ref<Branch[]>([])

const filters = ref({
  branchId: '',
  gender: '',
  segment: ''
})

const stats = ref({
  total: 0,
  newThisMonth: 0,
  loyal: 0,
  atRisk: 0,
  birthdayThisMonth: 0
})

const customerSegments = [
  { value: 'new', label: 'Yeni', activeClass: 'bg-blue-600 text-white' },
  { value: 'loyal', label: 'Sadık', activeClass: 'bg-purple-600 text-white' },
  { value: 'vip', label: 'VIP', activeClass: 'bg-yellow-600 text-white' },
  { value: 'atRisk', label: 'Riskli', activeClass: 'bg-red-600 text-white' }
]

const formData = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  gender: '' as '' | 'male' | 'female',
  date_of_birth: '',
  branch_id: '',
  notes: ''
})

// Computed
const filteredCustomers = computed(() => {
  let result = customers.value

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(c =>
      c.first_name?.toLowerCase().includes(searchLower) ||
      c.last_name?.toLowerCase().includes(searchLower) ||
      c.phone?.includes(search.value) ||
      c.email?.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.branchId) {
    result = result.filter(c => c.branch_id === filters.value.branchId)
  }

  if (filters.value.gender) {
    result = result.filter(c => c.gender === filters.value.gender)
  }

  if (filters.value.segment) {
    result = result.filter(c => getCustomerSegment(c) === filters.value.segment)
  }

  return result.sort((a, b) => (b.total_visits || 0) - (a.total_visits || 0))
})

// Helpers
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

const formatDateShort = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount)
}

const getInitials = (firstName: string = '', lastName: string = '') => {
  return `${(firstName || '').charAt(0)}${(lastName || '').charAt(0)}`.toUpperCase()
}

const getGenderLabel = (gender?: string) => {
  if (gender === 'male') return 'Erkek'
  if (gender === 'female') return 'Kadın'
  return '-'
}

const getCustomerSegment = (customer: Customer): string | null => {
  const now = new Date()
  const created = new Date(customer.created_at)
  const daysSinceCreated = Math.floor((now.getTime() - created.getTime()) / (1000 * 60 * 60 * 24))
  
  if (daysSinceCreated <= 30) return 'new'
  
  if ((customer.total_visits || 0) >= 10 && (customer.total_spent || 0) >= 5000) return 'vip'
  
  if ((customer.total_visits || 0) >= 5) return 'loyal'
  
  if (customer.last_visit) {
    const lastVisit = new Date(customer.last_visit)
    const daysSinceVisit = Math.floor((now.getTime() - lastVisit.getTime()) / (1000 * 60 * 60 * 24))
    if (daysSinceVisit >= 90) return 'atRisk'
  }
  
  return null
}

const getSegmentLabel = (segment: string) => {
  const labels: Record<string, string> = {
    new: 'Yeni',
    loyal: 'Sadık',
    vip: 'VIP',
    atRisk: 'Riskli'
  }
  return labels[segment] || segment
}

const getSegmentBadge = (segment: string) => {
  const badges: Record<string, string> = {
    new: 'bg-blue-100 text-blue-800',
    loyal: 'bg-purple-100 text-purple-800',
    vip: 'bg-yellow-100 text-yellow-800',
    atRisk: 'bg-red-100 text-red-800'
  }
  return badges[segment] || 'bg-gray-100 text-gray-800'
}

// Modal Functions
const openCreateModal = () => {
  editingCustomer.value = null
  formData.value = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    gender: '',
    date_of_birth: '',
    branch_id: branches.value[0]?.id || '',
    notes: ''
  }
  showModal.value = true
}

const editCustomer = (customer: Customer) => {
  editingCustomer.value = customer
  formData.value = {
    first_name: customer.first_name,
    last_name: customer.last_name,
    phone: customer.phone,
    email: customer.email || '',
    gender: customer.gender || '',
    date_of_birth: customer.date_of_birth || '',
    branch_id: customer.branch_id,
    notes: customer.notes || ''
  }
  showModal.value = true
}

const viewCustomer = (customer: Customer) => {
  router.push(`/customers/${customer.id}`)
}

const closeModal = () => {
  showModal.value = false
  editingCustomer.value = null
}

// CRUD Operations
const loadData = async () => {
  loading.value = true
  try {
    // Şubeleri yükle
    await branchStore.fetchBranches()
    branches.value = branchStore.branches || []

    // Müşterileri yükle
    await customerStore.fetchCustomers()
    customers.value = customerStore.customers || []

    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateStats = () => {
  const now = new Date()
  const thisMonth = now.getMonth()
  
  stats.value.total = customers.value.length
  
  stats.value.newThisMonth = customers.value.filter(c => {
    const created = new Date(c.created_at)
    return created.getMonth() === thisMonth && created.getFullYear() === now.getFullYear()
  }).length
  
  stats.value.loyal = customers.value.filter(c => 
    getCustomerSegment(c) === 'loyal' || getCustomerSegment(c) === 'vip'
  ).length
  
  stats.value.atRisk = customers.value.filter(c => getCustomerSegment(c) === 'atRisk').length
  
  stats.value.birthdayThisMonth = customers.value.filter(c => {
    if (!c.date_of_birth) return false
    const birthday = new Date(c.date_of_birth)
    return birthday.getMonth() === thisMonth
  }).length
}

const saveCustomer = async () => {
  saving.value = true
  try {
    if (editingCustomer.value) {
      await customerStore.updateCustomer(editingCustomer.value.id, formData.value)
    } else {
      await customerStore.createCustomer(formData.value)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Müşteri kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const deleteCustomer = async (customer: Customer) => {
  if (!confirm(`${customer.first_name} ${customer.last_name} müşterisini silmek istediğinizden emin misiniz?`)) return

  try {
    await customerStore.deleteCustomer(customer.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Müşteri silinemedi')
  }
}

const exportCustomers = () => {
  const csvContent = [
    ['Ad', 'Soyad', 'Telefon', 'E-posta', 'Cinsiyet', 'Şube', 'Toplam Ziyaret', 'Toplam Harcama'].join(','),
    ...filteredCustomers.value.map(c => [
      c.first_name,
      c.last_name,
      c.phone,
      c.email || '',
      getGenderLabel(c.gender),
      c.branch?.name || '',
      c.total_visits || 0,
      c.total_spent || 0
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `musteriler_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>
