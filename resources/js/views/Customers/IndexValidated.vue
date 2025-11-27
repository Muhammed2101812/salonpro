<template>
  <div class="p-8">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteriler</h1>
        <p class="mt-2 text-gray-600">Salon müşterilerinizi yönetin</p>
      </div>
      <button @click="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition">
        Müşteri Ekle
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="customerStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      <p class="mt-4 text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="customerStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg flex items-center gap-2">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ customerStore.error }}
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Soyad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-posta</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cinsiyet</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şube</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="customer in customerStore.customers" :key="customer.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap">
              <router-link :to="`/customers/${customer.id}`" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                {{ customer.first_name }} {{ customer.last_name }}
              </router-link>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.email || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ customer.gender || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.branch?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button @click="openEditModal(customer)" class="text-blue-600 hover:text-blue-900 font-medium">Düzenle</button>
              <button @click="handleDelete(customer.id)" class="text-red-600 hover:text-red-900 font-medium">Sil</button>
            </td>
          </tr>
          <tr v-if="customerStore.customers.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p class="mt-2">Henüz müşteri eklenmemiş</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal with Validated Form -->
    <FormModal v-model="showModal" :title="isEdit ? 'Müşteri Düzenle' : 'Müşteri Ekle'">
      <ValidatedForm
        :validation-schema="customerSchema"
        :initial-values="form"
        @submit="handleSubmit"
        @cancel="closeModal"
      >
        <template #default="{ errors, isSubmitting }">
          <div class="space-y-4">
            <!-- First Name & Last Name Row -->
            <div class="grid grid-cols-2 gap-4">
              <TextInput
                name="first_name"
                label="Ad"
                placeholder="Örn: Ahmet"
                required
              />
              <TextInput
                name="last_name"
                label="Soyad"
                placeholder="Örn: Yılmaz"
                required
              />
            </div>

            <!-- Phone & Email Row -->
            <div class="grid grid-cols-2 gap-4">
              <TextInput
                name="phone"
                label="Telefon"
                type="tel"
                placeholder="+90 555 123 4567"
                required
              />
              <TextInput
                name="email"
                label="E-posta"
                type="email"
                placeholder="ornek@email.com"
              />
            </div>

            <!-- Branch & Gender Row -->
            <div class="grid grid-cols-2 gap-4">
              <SelectInput
                name="branch_id"
                label="Şube"
                :options="branchOptions"
                placeholder="Şube seçiniz"
                required
              />
              <SelectInput
                name="gender"
                label="Cinsiyet"
                :options="genderOptions"
                placeholder="Cinsiyet seçiniz"
              />
            </div>

            <!-- Date of Birth -->
            <TextInput
              name="date_of_birth"
              label="Doğum Tarihi"
              type="date"
            />

            <!-- Address -->
            <TextInput
              name="address"
              label="Adres"
              placeholder="Cadde/Sokak, No: X, İlçe/İl"
            />

            <!-- City -->
            <TextInput
              name="city"
              label="Şehir"
              placeholder="İstanbul"
            />

            <!-- Notes -->
            <TextareaInput
              name="notes"
              label="Notlar"
              placeholder="Müşteri hakkında notlar..."
              :rows="3"
            />

            <!-- Show validation summary if there are errors -->
            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
              <p class="text-sm font-medium text-red-800 mb-2">Lütfen aşağıdaki hataları düzeltin:</p>
              <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
              </ul>
            </div>
          </div>
        </template>
      </ValidatedForm>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useCustomerStore } from '@/stores/customer'
import { useBranchStore } from '@/stores/branch'
import { customerSchema } from '@/composables/useValidation'
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'

const customerStore = useCustomerStore()
const branchStore = useBranchStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)

const form = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  branch_id: '',
  gender: '',
  date_of_birth: '',
  address: '',
  city: '',
  notes: ''
})

const branchOptions = computed(() =>
  branchStore.branches.map(branch => ({
    value: branch.id,
    label: branch.name
  }))
)

const genderOptions = [
  { value: 'male', label: 'Erkek' },
  { value: 'female', label: 'Kadın' },
  { value: 'other', label: 'Diğer' }
]

const resetForm = () => {
  form.value = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    branch_id: '',
    gender: '',
    date_of_birth: '',
    address: '',
    city: '',
    notes: ''
  }
}

const openCreateModal = () => {
  resetForm()
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (customer: any) => {
  form.value = {
    first_name: customer.first_name || '',
    last_name: customer.last_name || '',
    phone: customer.phone || '',
    email: customer.email || '',
    branch_id: customer.branch_id || '',
    gender: customer.gender || '',
    date_of_birth: customer.date_of_birth || '',
    address: customer.address || '',
    city: customer.city || '',
    notes: customer.notes || ''
  }
  isEdit.value = true
  editingId.value = customer.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const handleSubmit = async (values: Record<string, any>) => {
  try {
    if (isEdit.value && editingId.value) {
      await customerStore.updateCustomer(editingId.value, values)
    } else {
      await customerStore.createCustomer(values)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save customer:', error)
  }
}

const handleDelete = async (id: string) => {
  if (confirm('Bu müşteriyi silmek istediğinize emin misiniz?')) {
    try {
      await customerStore.deleteCustomer(id)
    } catch (error) {
      console.error('Failed to delete customer:', error)
    }
  }
}

onMounted(() => {
  customerStore.fetchCustomers()
  branchStore.fetchBranches()
})
</script>
