<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Bildirim Şablonları</h1>
        <p class="mt-1 text-sm text-gray-600">SMS, E-posta ve Push bildirim şablonlarını yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium transition"
      >
        Yeni Şablon
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Channel Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kanal</label>
          <select
            v-model="filters.channel"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Tümü</option>
            <option value="email">E-posta</option>
            <option value="sms">SMS</option>
            <option value="push">Push Bildirimi</option>
            <option value="whatsapp">WhatsApp</option>
          </select>
        </div>

        <!-- Event Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Olay Tipi</label>
          <select
            v-model="filters.event_type"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Tümü</option>
            <option value="appointment_created">Randevu Oluşturuldu</option>
            <option value="appointment_confirmed">Randevu Onaylandı</option>
            <option value="appointment_reminder">Randevu Hatırlatma</option>
            <option value="appointment_cancelled">Randevu İptal Edildi</option>
            <option value="payment_received">Ödeme Alındı</option>
            <option value="birthday_greeting">Doğum Günü Kutlaması</option>
            <option value="promotion">Promosyon</option>
          </select>
        </div>

        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ara</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Şablon adı..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      </div>
    </div>

    <!-- Templates List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <!-- Loading State -->
      <div v-if="loading" class="p-12 text-center text-gray-500">
        Yükleniyor...
      </div>

      <!-- Templates Table -->
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şablon Adı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kanal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Olay Tipi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="template in filteredTemplates" :key="template.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900">{{ template.name }}</div>
              <div class="text-xs text-gray-500">{{ template.subject }}</div>
            </td>
            <td class="px-6 py-4">
              <span :class="getChannelBadge(template.channel)" class="px-2 py-1 text-xs rounded-full font-medium">
                {{ getChannelLabel(template.channel) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
              {{ getEventLabel(template.event_type) }}
            </td>
            <td class="px-6 py-4">
              <span :class="template.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs rounded-full font-medium">
                {{ template.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button
                @click="editTemplate(template)"
                class="text-blue-600 hover:text-blue-900 text-sm font-medium"
              >
                Düzenle
              </button>
              <button
                @click="deleteTemplate(template.id)"
                class="text-red-600 hover:text-red-900 text-sm font-medium"
              >
                Sil
              </button>
            </td>
          </tr>
          <tr v-if="filteredTemplates.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
              Şablon bulunamadı
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Template Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">
            {{ isEditing ? 'Şablon Düzenle' : 'Yeni Şablon Oluştur' }}
          </h2>

          <form @submit.prevent="saveTemplate" class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Örn: Randevu Hatırlatma"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kanal *</label>
                <select
                  v-model="form.channel"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="email">E-posta</option>
                  <option value="sms">SMS</option>
                  <option value="push">Push Bildirimi</option>
                  <option value="whatsapp">WhatsApp</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Olay Tipi *</label>
                <select
                  v-model="form.event_type"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="appointment_created">Randevu Oluşturuldu</option>
                  <option value="appointment_confirmed">Randevu Onaylandı</option>
                  <option value="appointment_reminder">Randevu Hatırlatma</option>
                  <option value="appointment_cancelled">Randevu İptal Edildi</option>
                  <option value="payment_received">Ödeme Alındı</option>
                  <option value="birthday_greeting">Doğum Günü Kutlaması</option>
                  <option value="promotion">Promosyon</option>
                </select>
              </div>

              <div class="flex items-end">
                <label class="flex items-center space-x-2">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="text-sm font-medium text-gray-700">Aktif</span>
                </label>
              </div>
            </div>

            <!-- Subject (for email) -->
            <div v-if="form.channel === 'email'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Konu *</label>
              <input
                v-model="form.subject"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Örn: Randevunuz {{appointment_date}} tarihinde"
              />
            </div>

            <!-- Message Body -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-gray-700">Mesaj İçeriği *</label>
                <span class="text-xs text-gray-500">
                  {{ form.body.length }} / {{ form.channel === 'sms' ? '160' : '1000' }} karakter
                </span>
              </div>
              <textarea
                v-model="form.body"
                rows="6"
                required
                :maxlength="form.channel === 'sms' ? 160 : 1000"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                placeholder="Merhaba {{customer_name}},&#10;&#10;{{appointment_date}} tarihindeki randevunuzu hatırlatmak isteriz..."
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">
                Değişkenler için {{variable_name}} formatını kullanın
              </p>
            </div>

            <!-- Available Variables -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="text-sm font-medium text-gray-900 mb-2">Kullanılabilir Değişkenler</h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <button
                  v-for="variable in availableVariables"
                  :key="variable"
                  type="button"
                  @click="insertVariable(variable)"
                  class="px-2 py-1 bg-white border border-gray-300 rounded text-xs text-gray-700 hover:bg-blue-50 hover:border-blue-500 transition"
                >
                  {{ variable }}
                </button>
              </div>
            </div>

            <!-- Preview -->
            <div class="bg-blue-50 rounded-lg p-4">
              <h3 class="text-sm font-medium text-gray-900 mb-2">Önizleme</h3>
              <div class="bg-white rounded p-3 text-sm whitespace-pre-wrap">
                <div v-if="form.channel === 'email' && form.subject" class="font-bold mb-2">
                  {{ renderPreview(form.subject) }}
                </div>
                <div>{{ renderPreview(form.body) }}</div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4 border-t">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium transition"
              >
                İptal
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium transition"
              >
                Kaydet
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const templates = ref<any[]>([])
const showModal = ref(false)
const isEditing = ref(false)
const currentTemplate = ref<any>(null)

const filters = ref({
  channel: '',
  event_type: '',
  search: '',
})

const form = ref({
  name: '',
  slug: '',
  channel: 'email',
  event_type: 'appointment_reminder',
  subject: '',
  body: '',
  is_active: true,
})

const availableVariables = [
  '{{customer_name}}',
  '{{customer_email}}',
  '{{customer_phone}}',
  '{{appointment_date}}',
  '{{appointment_time}}',
  '{{service_name}}',
  '{{employee_name}}',
  '{{branch_name}}',
  '{{branch_address}}',
  '{{branch_phone}}',
  '{{price}}',
  '{{duration}}',
]

// Computed
const filteredTemplates = computed(() => {
  let result = templates.value

  if (filters.value.channel) {
    result = result.filter(t => t.channel === filters.value.channel)
  }

  if (filters.value.event_type) {
    result = result.filter(t => t.event_type === filters.value.event_type)
  }

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(t =>
      t.name.toLowerCase().includes(search) ||
      t.subject?.toLowerCase().includes(search) ||
      t.body.toLowerCase().includes(search)
    )
  }

  return result
})

// Methods
const fetchTemplates = async () => {
  loading.value = true
  try {
    const response: any = await api.get('/notification-templates')
    templates.value = response.data
  } catch (error) {
    console.error('Failed to fetch templates:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEditing.value = false
  currentTemplate.value = null
  form.value = {
    name: '',
    slug: '',
    channel: 'email',
    event_type: 'appointment_reminder',
    subject: '',
    body: '',
    is_active: true,
  }
  showModal.value = true
}

const editTemplate = (template: any) => {
  isEditing.value = true
  currentTemplate.value = template
  form.value = { ...template }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  currentTemplate.value = null
}

const saveTemplate = async () => {
  try {
    // Auto-generate slug from name
    form.value.slug = form.value.name.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '')

    if (isEditing.value) {
      const response: any = await api.put(`/notification-templates/${currentTemplate.value.id}`, form.value)
      const index = templates.value.findIndex(t => t.id === currentTemplate.value.id)
      if (index !== -1) templates.value[index] = response.data
    } else {
      const response: any = await api.post('/notification-templates', form.value)
      templates.value.push(response.data)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save template:', error)
    alert('Şablon kaydedilemedi')
  }
}

const deleteTemplate = async (id: string) => {
  if (!confirm('Bu şablonu silmek istediğinizden emin misiniz?')) return

  try {
    await api.delete(`/notification-templates/${id}`)
    templates.value = templates.value.filter(t => t.id !== id)
  } catch (error) {
    console.error('Failed to delete template:', error)
    alert('Şablon silinemedi')
  }
}

const insertVariable = (variable: string) => {
  const textarea = document.querySelector('textarea') as HTMLTextAreaElement
  if (!textarea) return

  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const text = form.value.body
  form.value.body = text.substring(0, start) + variable + text.substring(end)

  // Set cursor position after inserted variable
  setTimeout(() => {
    textarea.selectionStart = textarea.selectionEnd = start + variable.length
    textarea.focus()
  }, 0)
}

const renderPreview = (text: string) => {
  if (!text) return ''

  const sampleData: Record<string, string> = {
    '{{customer_name}}': 'Ahmet Yılmaz',
    '{{customer_email}}': 'ahmet@example.com',
    '{{customer_phone}}': '0555 123 4567',
    '{{appointment_date}}': '15 Kasım 2025',
    '{{appointment_time}}': '14:30',
    '{{service_name}}': 'Saç Kesimi',
    '{{employee_name}}': 'Mehmet Demir',
    '{{branch_name}}': 'Ana Şube',
    '{{branch_address}}': 'Atatürk Cad. No:123, İstanbul',
    '{{branch_phone}}': '0212 555 1234',
    '{{price}}': '150 TL',
    '{{duration}}': '30 dakika',
  }

  let preview = text
  Object.entries(sampleData).forEach(([key, value]) => {
    preview = preview.replaceAll(key, value)
  })

  return preview
}

// Helpers
const getChannelLabel = (channel: string) => {
  const labels: Record<string, string> = {
    email: 'E-posta',
    sms: 'SMS',
    push: 'Push',
    whatsapp: 'WhatsApp',
  }
  return labels[channel] || channel
}

const getChannelBadge = (channel: string) => {
  const badges: Record<string, string> = {
    email: 'bg-blue-100 text-blue-800',
    sms: 'bg-green-100 text-green-800',
    push: 'bg-purple-100 text-purple-800',
    whatsapp: 'bg-teal-100 text-teal-800',
  }
  return badges[channel] || 'bg-gray-100 text-gray-800'
}

const getEventLabel = (event: string) => {
  const labels: Record<string, string> = {
    appointment_created: 'Randevu Oluşturuldu',
    appointment_confirmed: 'Randevu Onaylandı',
    appointment_reminder: 'Randevu Hatırlatma',
    appointment_cancelled: 'Randevu İptal Edildi',
    payment_received: 'Ödeme Alındı',
    birthday_greeting: 'Doğum Günü',
    promotion: 'Promosyon',
  }
  return labels[event] || event
}

onMounted(() => {
  fetchTemplates()
})
</script>
