<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Entegrasyonlar</h1>
        <p class="mt-2 text-sm text-gray-600">Üçüncü parti entegrasyonları yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Entegrasyon
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-sky-100"><PuzzlePieceIcon class="h-6 w-6 text-sky-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ integrations.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bağlı</p><p class="text-2xl font-bold text-green-600">{{ connectedCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><ExclamationTriangleIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Hatalı</p><p class="text-2xl font-bold text-orange-600">{{ errorCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Entegrasyon Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in integrations" :key="i.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getStatusColor(i.status)]"></div>
        <div class="p-5">
          <div class="flex items-start gap-4 mb-4">
            <div :class="['h-14 w-14 rounded-xl flex items-center justify-center', getProviderBg(i.provider)]">
              <PuzzlePieceIcon class="h-7 w-7 text-white" />
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">{{ i.name || getProviderLabel(i.provider) }}</h3>
              <span :class="['text-xs font-medium', getStatusTextColor(i.status)]">{{ getStatusLabel(i.status) }}</span>
            </div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Sağlayıcı</span><span class="font-medium text-gray-900">{{ getProviderLabel(i.provider) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Son Sync</span><span class="text-gray-700">{{ formatDateTime(i.last_sync_at) }}</span></div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="syncIntegration(i)" :disabled="i.status !== 'connected'" class="text-sm font-medium text-blue-600 hover:text-blue-700 disabled:opacity-50">Senkronize Et</button>
            <div class="flex gap-2">
              <button @click="editIntegration(i)" class="p-1.5 text-sky-600 hover:bg-sky-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(i.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="integrations.length === 0" class="col-span-full text-center py-12">
        <PuzzlePieceIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Entegrasyon bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Entegrasyonu Düzenle' : 'Yeni Entegrasyon' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Sağlayıcı</label>
            <select v-model="form.provider" class="w-full rounded-lg border-gray-300">
              <option value="google">Google</option>
              <option value="stripe">Stripe</option>
              <option value="twilio">Twilio</option>
              <option value="zapier">Zapier</option>
              <option value="custom">Özel</option>
            </select>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İsim</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">API Key</label><input v-model="form.api_key" class="w-full rounded-lg border-gray-300 font-mono" type="password" /></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-sky-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, PuzzlePieceIcon, CheckCircleIcon, ExclamationTriangleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useIntegrationStore } from '@/stores/integration'

const store = useIntegrationStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ provider: 'google', name: '', api_key: '' })
const integrations = ref<any[]>([])

const connectedCount = computed(() => integrations.value.filter(i => i.status === 'connected').length)
const errorCount = computed(() => integrations.value.filter(i => i.status === 'error').length)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : 'Hiç'
const getProviderLabel = (p: string) => ({ google: 'Google', stripe: 'Stripe', twilio: 'Twilio', zapier: 'Zapier', custom: 'Özel' }[p] || p)
const getProviderBg = (p: string) => ({ google: 'bg-red-500', stripe: 'bg-purple-500', twilio: 'bg-red-600', zapier: 'bg-orange-500', custom: 'bg-gray-600' }[p] || 'bg-sky-500')
const getStatusLabel = (s: string) => ({ connected: 'Bağlı', disconnected: 'Bağlı Değil', error: 'Hata' }[s] || s || 'Bağlı Değil')
const getStatusColor = (s: string) => ({ connected: 'bg-green-500', disconnected: 'bg-gray-300', error: 'bg-red-500' }[s] || 'bg-gray-300')
const getStatusTextColor = (s: string) => ({ connected: 'text-green-600', disconnected: 'text-gray-500', error: 'text-red-600' }[s] || 'text-gray-500')

const openCreateModal = () => { form.value = { provider: 'google', name: '', api_key: '' }; isEdit.value = false; showModal.value = true }
const editIntegration = (i: any) => { form.value = { ...i }; isEdit.value = true; editingId.value = i.id; showModal.value = true }
const syncIntegration = (i: any) => { alert(`${i.name || getProviderLabel(i.provider)} senkronize ediliyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); integrations.value = r?.data || [] }
onMounted(() => { loadData() })
</script>