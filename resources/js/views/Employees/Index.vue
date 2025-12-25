<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çalışanlar</h1>
        <p class="mt-2 text-sm text-gray-600">Salon çalışanlarınızı yönetin ve takip edin</p>
      </div>
      <div class="flex gap-3">
        <div class="flex rounded-lg border border-gray-200 overflow-hidden bg-white">
            <button
              @click="viewMode = 'grid'"
              :class="[ 'p-2 transition-colors', viewMode === 'grid' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Kart Görünümü"
            >
              <Squares2X2Icon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'table'"
              :class="[ 'p-2 transition-colors', viewMode === 'table' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Liste Görünümü"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
        </div>
        <Button variant="outline" @click="exportEmployees" :icon="ArrowDownTrayIcon" label="Dışa Aktar" />
        <Button variant="primary" @click="openCreateModal" :icon="PlusIcon" label="Çalışan Ekle" />
      </div>
    </div>

    <!-- Stats -->
    <EmployeeStats :stats="stats" />

    <!-- Filters -->
    <Card class="p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
           <Input v-model="search" placeholder="Çalışan ara..." class="w-full lg:w-64">
                <template #prefix>
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </template>
           </Input>

           <select
             v-model="filters.branchId"
             class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm py-2 px-3"
           >
             <option value="">Tüm Şubeler</option>
             <option v-for="branch in branches" :key="branch.id" :value="branch.id">
               {{ branch.name }}
             </option>
           </select>

           <select
             v-model="filters.position"
             class="rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm py-2 px-3"
           >
             <option value="">Tüm Pozisyonlar</option>
             <option v-for="pos in positions" :key="pos.value" :value="pos.value">
               {{ pos.label }}
             </option>
           </select>

           <div class="flex rounded-lg border border-gray-200 overflow-hidden">
             <button
               @click="filters.status = ''"
               :class="[
                 'px-3 py-2 text-xs font-medium transition-colors',
                 filters.status === '' ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
               ]"
             >
               Tümü
             </button>
             <button
               @click="filters.status = 'active'"
               :class="[
                 'px-3 py-2 text-xs font-medium transition-colors',
                 filters.status === 'active' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
               ]"
             >
               Aktif
             </button>
             <button
               @click="filters.status = 'inactive'"
               :class="[
                 'px-3 py-2 text-xs font-medium transition-colors',
                 filters.status === 'inactive' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
               ]"
             >
               Pasif
             </button>
           </div>
        </div>

        <Button variant="ghost" @click="loadData" :icon="ArrowPathIcon" />
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="employeeStore.loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <EmployeeCard
        v-for="employee in filteredEmployees"
        :key="employee.id"
        :employee="employee"
        :positions="positions"
        @edit="openEditModal"
        @delete="handleDelete"
      />
      <div v-if="filteredEmployees.length === 0" class="col-span-full text-center py-12 text-gray-500">
         <UserGroupIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
         Çalışan bulunamadı
      </div>
    </div>

    <!-- Table View -->
    <DataTable
        v-else
        :columns="tableColumns"
        :data="filteredEmployees"
    >
        <template #cell-name="{ row }">
            <div class="flex items-center gap-3">
                <div
                  :class="[
                    'flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-medium text-sm',
                    getAvatarGradient(row)
                  ]"
                >
                  {{ getInitials(row.first_name, row.last_name) }}
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ row.first_name }} {{ row.last_name }}</p>
                  <p class="text-xs text-gray-500">{{ getPositionLabel(row.position) }}</p>
                </div>
            </div>
        </template>
        <template #cell-contact="{ row }">
            <div class="text-sm text-gray-600">{{ row.phone || '-' }}</div>
            <div class="text-xs text-gray-500 truncate max-w-[150px]">{{ row.email || '' }}</div>
        </template>
        <template #cell-branch="{ row }">
            {{ row.branch?.name || '-' }}
        </template>
        <template #cell-specialties="{ row }">
            <div v-if="row.specialties?.length" class="flex flex-wrap gap-1">
                <span
                  v-for="(specialty, idx) in row.specialties.slice(0, 2)"
                  :key="idx"
                  class="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full"
                >
                  {{ specialty }}
                </span>
                <span v-if="row.specialties.length > 2" class="text-xs text-gray-500">
                  +{{ row.specialties.length - 2 }}
                </span>
            </div>
            <span v-else class="text-gray-400">-</span>
        </template>
        <template #cell-commission="{ row }">
            <span class="text-sm font-medium text-yellow-600">%{{ row.commission_rate || 0 }}</span>
        </template>
        <template #cell-status="{ row }">
            <span
                :class="[
                  'px-2 py-1 text-xs rounded-full font-semibold',
                  row.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
            >
                {{ row.is_active ? 'Aktif' : 'Pasif' }}
            </span>
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
    <EmployeeModal
        v-model="showModal"
        :is-edit="isEdit"
        :initial-data="modalData"
        :loading="employeeStore.loading"
        :branches="branches"
        :positions="positions"
        @submit="handleSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  UserGroupIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ListBulletIcon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import EmployeeStats from '@/components/employee/EmployeeStats.vue'
import EmployeeModal from '@/components/employee/EmployeeModal.vue'
import EmployeeCard from '@/components/employee/EmployeeCard.vue'

import { useEmployeeStore } from '@/stores/employee'
import { useBranchStore } from '@/stores/branch'

interface Employee {
  id: string
  first_name: string
  last_name: string
  phone?: string
  email?: string
  branch_id: string
  branch?: { id: string; name: string }
  position?: string
  specialties?: string[]
  commission_rate?: number
  hire_date?: string
  is_active: boolean
  [key: string]: any
}

interface Branch {
  id: string
  name: string
}

const employeeStore = useEmployeeStore()
const branchStore = useBranchStore()

// State
const viewMode = ref<'grid' | 'table'>('grid')
const search = ref('')
const showModal = ref(false)
const isEdit = ref(false)
const modalData = ref<any>(null)
const editingId = ref<string | null>(null)

const branches = ref<Branch[]>([])

const filters = ref({
  branchId: '',
  position: '',
  status: ''
})

const stats = ref({
  totalEmployees: 0,
  activeEmployees: 0,
  seniorEmployees: 0,
  avgCommission: 0,
  newThisMonth: 0
})

const positions = [
  { value: 'stylist', label: 'Kuaför' },
  { value: 'colorist', label: 'Boyacı' },
  { value: 'barber', label: 'Berber' },
  { value: 'manicurist', label: 'Manikür Uzmanı' },
  { value: 'esthetician', label: 'Güzellik Uzmanı' },
  { value: 'masseur', label: 'Masör' },
  { value: 'receptionist', label: 'Resepsiyon' },
  { value: 'manager', label: 'Yönetici' },
  { value: 'assistant', label: 'Asistan' }
]

const avatarGradients = [
  'bg-gradient-to-br from-purple-500 to-pink-500',
  'bg-gradient-to-br from-blue-500 to-cyan-500',
  'bg-gradient-to-br from-green-500 to-teal-500',
  'bg-gradient-to-br from-orange-500 to-red-500',
  'bg-gradient-to-br from-indigo-500 to-purple-500'
]

const tableColumns = [
    { key: 'name', label: 'Çalışan' },
    { key: 'contact', label: 'İletişim' },
    { key: 'branch', label: 'Şube' },
    { key: 'specialties', label: 'Uzmanlık' },
    { key: 'commission', label: 'Komisyon' },
    { key: 'status', label: 'Durum' }
]

// Computed
const filteredEmployees = computed(() => {
  let result = employeeStore.employees as Employee[]

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(e =>
      e.first_name?.toLowerCase().includes(searchLower) ||
      e.last_name?.toLowerCase().includes(searchLower) ||
      e.phone?.includes(search.value) ||
      e.email?.toLowerCase().includes(searchLower)
    )
  }

  if (filters.value.branchId) {
    result = result.filter(e => e.branch_id === filters.value.branchId)
  }

  if (filters.value.position) {
    result = result.filter(e => e.position === filters.value.position)
  }

  if (filters.value.status === 'active') {
    result = result.filter(e => e.is_active)
  } else if (filters.value.status === 'inactive') {
    result = result.filter(e => !e.is_active)
  }

  return result
})

// Helpers
const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getAvatarGradient = (employee: Employee) => {
  const index = employee.first_name?.charCodeAt(0) || 0
  return avatarGradients[index % avatarGradients.length]
}

const getPositionLabel = (position?: string) => {
  return positions.find(p => p.value === position)?.label || position || 'Çalışan'
}

const updateStats = () => {
  const employees = employeeStore.employees as Employee[]
  stats.value.totalEmployees = employees.length
  stats.value.activeEmployees = employees.filter(e => e.is_active).length
  
  // 2+ yıl deneyimli çalışanlar
  stats.value.seniorEmployees = employees.filter(e => {
    if (!e.hire_date) return false
    const years = (Date.now() - new Date(e.hire_date).getTime()) / (365.25 * 24 * 60 * 60 * 1000)
    return years >= 2
  }).length
  
  const commissions = employees.map(e => e.commission_rate || 0)
  stats.value.avgCommission = commissions.length 
    ? Math.round(commissions.reduce((a, b) => a + b, 0) / commissions.length) 
    : 0
  
  const now = new Date()
  stats.value.newThisMonth = employees.filter(e => {
    if (!e.hire_date) return false
    const hireDate = new Date(e.hire_date)
    return hireDate.getMonth() === now.getMonth() && hireDate.getFullYear() === now.getFullYear()
  }).length
}

// Modal Actions
const openCreateModal = () => {
  modalData.value = null
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (employee: Employee) => {
  modalData.value = { ...employee }
  isEdit.value = true
  editingId.value = employee.id
  showModal.value = true
}

const handleSubmit = async (data: any) => {
  try {
    if (isEdit.value && editingId.value) {
      await employeeStore.updateEmployee(editingId.value, data)
    } else {
      await employeeStore.createEmployee(data)
    }
    showModal.value = false
    updateStats()
  } catch (error) {
    console.error('Çalışan kaydedilemedi:', error)
    alert('Çalışan kaydedilemedi')
  }
}

const handleDelete = async (id: string) => {
  if (!confirm('Bu çalışanı silmek istediğinizden emin misiniz?')) return
  try {
    await employeeStore.deleteEmployee(id)
    updateStats()
  } catch (error) {
    console.error('Çalışan silinemedi:', error)
  }
}

const loadData = async () => {
  await Promise.all([
    employeeStore.fetchEmployees(),
    branchStore.fetchBranches()
  ])
  branches.value = branchStore.branches || []
  updateStats()
}

const exportEmployees = () => {
  // Simple csv export logic
  const csvContent = [
    ['Ad', 'Soyad', 'Telefon', 'E-posta', 'Şube', 'Pozisyon', 'Durum'].join(','),
    ...filteredEmployees.value.map(e => [
      e.first_name,
      e.last_name,
      e.phone || '',
      e.email || '',
      e.branch?.name || '',
      getPositionLabel(e.position),
      e.is_active ? 'Aktif' : 'Pasif'
    ].join(','))
  ].join('\n')
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `calisanlar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => {
  loadData()
})
</script>
