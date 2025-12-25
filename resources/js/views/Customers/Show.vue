<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Back Button -->
    <div>
      <router-link
        to="/customers"
        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors"
      >
        <ArrowLeftIcon class="w-5 h-5 mr-1" />
        Geri
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Customer Details -->
    <div v-else-if="customer" class="space-y-6">
      <!-- Header -->
      <Card>
        <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div
                :class="[
                'flex-shrink-0 h-16 w-16 rounded-full flex items-center justify-center text-white font-bold text-2xl',
                customer.gender === 'female' ? 'bg-gradient-to-br from-pink-500 to-rose-500' : 'bg-gradient-to-br from-blue-500 to-cyan-500'
                ]"
            >
              {{ getInitials(customer.first_name, customer.last_name) }}
            </div>

            <!-- Info -->
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ customer.first_name }} {{ customer.last_name }}
              </h1>
              <div class="mt-1 flex flex-col sm:flex-row gap-2 sm:gap-4 text-sm text-gray-600">
                <span v-if="customer.email" class="flex items-center gap-1">
                  <EnvelopeIcon class="w-4 h-4 text-gray-400" />
                  {{ customer.email }}
                </span>
                <span class="flex items-center gap-1">
                  <PhoneIcon class="w-4 h-4 text-gray-400" />
                  {{ customer.phone }}
                </span>
                <span v-if="customer.address" class="flex items-center gap-1">
                  <MapPinIcon class="w-4 h-4 text-gray-400" />
                  {{ customer.address }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 w-full sm:w-auto mt-4 sm:mt-0">
            <Button
                :to="`/customers/${customer.id}/edit`"
                variant="primary"
                :icon="PencilIcon"
                label="Düzenle"
            />
            <Button
                @click="createAppointment"
                variant="success"
                :icon="PlusIcon"
                label="Randevu Oluştur"
            />
          </div>
        </div>
      </Card>

      <!-- Tabs -->
      <Card bodyClass="p-0">
        <div class="border-b border-gray-100">
          <nav class="flex -mb-px overflow-x-auto">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              :class="[
                'px-6 py-4 text-sm font-medium border-b-2 transition whitespace-nowrap',
                activeTab === tab.key
                  ? 'border-primary text-primary'
                  : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
              ]"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div class="p-6">
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Stats Card 1 -->
              <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                <div class="text-sm text-blue-700 font-medium">Toplam Randevu</div>
                <div class="mt-2 text-3xl font-bold text-blue-900">{{ stats.total_appointments }}</div>
              </div>

              <!-- Stats Card 2 -->
              <div class="bg-green-50 rounded-xl p-6 border border-green-100">
                <div class="text-sm text-green-700 font-medium">Toplam Harcama</div>
                <div class="mt-2 text-3xl font-bold text-green-900">{{ formatCurrency(stats.total_spent) }}</div>
              </div>

              <!-- Stats Card 3 -->
              <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                <div class="text-sm text-purple-700 font-medium">Son Randevu</div>
                <div class="mt-2 text-lg font-bold text-purple-900">{{ formatDate(stats.last_appointment) }}</div>
              </div>
            </div>

            <!-- Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kişisel Bilgiler</h3>
                <dl class="space-y-3">
                  <div class="flex justify-between py-2 border-b border-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Ad Soyad</dt>
                    <dd class="text-sm text-gray-900">{{ customer.first_name }} {{ customer.last_name }}</dd>
                  </div>
                  <div v-if="customer.email" class="flex justify-between py-2 border-b border-gray-50">
                    <dt class="text-sm font-medium text-gray-500">E-posta</dt>
                    <dd class="text-sm text-gray-900">{{ customer.email }}</dd>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                    <dd class="text-sm text-gray-900">{{ customer.phone }}</dd>
                  </div>
                  <div v-if="customer.date_of_birth" class="flex justify-between py-2 border-b border-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Doğum Tarihi</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(customer.date_of_birth) }}</dd>
                  </div>
                  <div v-if="customer.gender" class="flex justify-between py-2 border-b border-gray-50">
                    <dt class="text-sm font-medium text-gray-500">Cinsiyet</dt>
                    <dd class="text-sm text-gray-900">{{ customer.gender === 'male' ? 'Erkek' : 'Kadın' }}</dd>
                  </div>
                </dl>
              </div>

              <div v-if="customer.notes">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notlar</h3>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 text-sm text-yellow-800">
                    {{ customer.notes }}
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline Tab -->
          <div v-else-if="activeTab === 'timeline'">
            <CustomerTimeline :customer-id="customer.id" />
          </div>

          <!-- Appointments Tab -->
          <div v-else-if="activeTab === 'appointments'">
            <div class="text-sm text-gray-500 text-center py-8">Randevu listesi burada olacak (Refactor Step 17)</div>
          </div>

          <!-- Notes Tab -->
          <div v-else-if="activeTab === 'notes'">
            <div class="text-sm text-gray-500 text-center py-8">Notlar burada olacak</div>
          </div>
        </div>
      </Card>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <ExclamationCircleIcon class="h-12 w-12 text-danger mx-auto mb-2" />
      <p class="text-gray-500">Müşteri bulunamadı</p>
      <Button to="/customers" variant="ghost" class="mt-4" label="Listeye Dön" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import CustomerTimeline from '@/components/customer/CustomerTimeline.vue'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import {
  ArrowLeftIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon,
  PencilIcon,
  PlusIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const customer = ref<any>(null)
const activeTab = ref('overview')
const stats = ref({
  total_appointments: 0,
  total_spent: 0,
  last_appointment: null,
})

const tabs = [
  { key: 'overview', label: 'Genel Bakış' },
  { key: 'timeline', label: 'Geçmiş' },
  { key: 'appointments', label: 'Randevular' },
  { key: 'notes', label: 'Notlar' },
]

// Fetch customer data
const fetchCustomer = async () => {
  loading.value = true
  try {
    const response: any = await api.get(`/customers/${route.params.id}`)
    customer.value = response.data

    // Fetch stats
    const statsResponse: any = await api.get(`/customers/${route.params.id}/stats`)
    stats.value = statsResponse.data
  } catch (error) {
    console.error('Failed to fetch customer:', error)
  } finally {
    loading.value = false
  }
}

// Helper: Get initials
const getInitials = (firstName: string = '', lastName: string = '') => {
  return `${(firstName || '').charAt(0)}${(lastName || '').charAt(0)}`.toUpperCase()
}

// Helper: Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

// Helper: Format date
const formatDate = (date: string | null) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' })
}

// Create appointment
const createAppointment = () => {
  router.push({ path: '/appointments/create', query: { customer_id: customer.value.id } })
}

onMounted(() => {
  fetchCustomer()
})
</script>
