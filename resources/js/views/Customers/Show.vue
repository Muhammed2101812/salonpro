<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
      <router-link
        to="/customers"
        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
      >
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Geri
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <!-- Customer Details -->
    <div v-else-if="customer" class="space-y-6">
      <!-- Header -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
              {{ getInitials(customer.first_name, customer.last_name) }}
            </div>

            <!-- Info -->
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ customer.first_name }} {{ customer.last_name }}
              </h1>
              <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                <span v-if="customer.email" class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  {{ customer.email }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ customer.phone }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <router-link
              :to="`/customers/${customer.id}/edit`"
              class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition"
            >
              Düzenle
            </router-link>
            <button
              @click="createAppointment"
              class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-medium transition"
            >
              Randevu Oluştur
            </button>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white shadow-sm rounded-lg">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              :class="[
                'px-6 py-4 text-sm font-medium border-b-2 transition',
                activeTab === tab.key
                  ? 'border-blue-500 text-blue-600'
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
              <div class="bg-blue-50 rounded-lg p-6">
                <div class="text-sm text-blue-600 font-medium">Toplam Randevu</div>
                <div class="mt-2 text-3xl font-bold text-blue-900">{{ stats.total_appointments }}</div>
              </div>

              <!-- Stats Card 2 -->
              <div class="bg-green-50 rounded-lg p-6">
                <div class="text-sm text-green-600 font-medium">Toplam Harcama</div>
                <div class="mt-2 text-3xl font-bold text-green-900">{{ formatCurrency(stats.total_spent) }}</div>
              </div>

              <!-- Stats Card 3 -->
              <div class="bg-purple-50 rounded-lg p-6">
                <div class="text-sm text-purple-600 font-medium">Son Randevu</div>
                <div class="mt-2 text-lg font-bold text-purple-900">{{ formatDate(stats.last_appointment) }}</div>
              </div>
            </div>

            <!-- Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kişisel Bilgiler</h3>
                <dl class="space-y-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Ad Soyad</dt>
                    <dd class="text-sm text-gray-900">{{ customer.first_name }} {{ customer.last_name }}</dd>
                  </div>
                  <div v-if="customer.email">
                    <dt class="text-sm font-medium text-gray-500">E-posta</dt>
                    <dd class="text-sm text-gray-900">{{ customer.email }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                    <dd class="text-sm text-gray-900">{{ customer.phone }}</dd>
                  </div>
                  <div v-if="customer.date_of_birth">
                    <dt class="text-sm font-medium text-gray-500">Doğum Tarihi</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(customer.date_of_birth) }}</dd>
                  </div>
                  <div v-if="customer.gender">
                    <dt class="text-sm font-medium text-gray-500">Cinsiyet</dt>
                    <dd class="text-sm text-gray-900">{{ customer.gender === 'male' ? 'Erkek' : 'Kadın' }}</dd>
                  </div>
                </dl>
              </div>

              <div v-if="customer.address">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Adres Bilgileri</h3>
                <p class="text-sm text-gray-900">{{ customer.address }}</p>
              </div>
            </div>
          </div>

          <!-- Timeline Tab -->
          <div v-else-if="activeTab === 'timeline'">
            <CustomerTimeline :customer-id="customer.id" />
          </div>

          <!-- Appointments Tab -->
          <div v-else-if="activeTab === 'appointments'">
            <div class="text-sm text-gray-500">Randevu listesi yükleniyor...</div>
          </div>

          <!-- Notes Tab -->
          <div v-else-if="activeTab === 'notes'">
            <div class="text-sm text-gray-500">Notlar yükleniyor...</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <p class="text-red-500">Müşteri bulunamadı</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import CustomerTimeline from '@/components/customer/CustomerTimeline.vue'

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
const getInitials = (firstName: string, lastName: string) => {
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
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
