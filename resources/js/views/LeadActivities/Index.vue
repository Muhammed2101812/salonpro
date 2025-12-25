<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Lead Aktiviteleri</h1>
        <p class="mt-2 text-sm text-gray-600">Potansiyel müşteri etkileşimlerini takip edin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Aktivite
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><ClipboardDocumentListIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ activities.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><PhoneIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Arama</p><p class="text-2xl font-bold text-blue-600">{{ callCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><EnvelopeIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">E-posta</p><p class="text-2xl font-bold text-green-600">{{ emailCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CalendarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplantı</p><p class="text-2xl font-bold text-purple-600">{{ meetingCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Aktivite Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="divide-y divide-gray-100">
        <div v-for="a in activities" :key="a.id" class="p-4 hover:bg-gray-50 flex items-start gap-4">
          <div :class="['h-10 w-10 rounded-lg flex items-center justify-center flex-shrink-0', getTypeBg(a.type)]">
            <component :is="getTypeIcon(a.type)" class="h-5 w-5 text-white" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="font-medium text-gray-900">{{ a.subject || getTypeLabel(a.type) }}</h3>
                <p class="text-sm text-gray-500">{{ a.lead?.name || 'Lead' }} • {{ a.user?.name || 'Kullanıcı' }}</p>
              </div>
              <span class="text-xs text-gray-400">{{ formatDateTime(a.created_at) }}</span>
            </div>
            <p v-if="a.notes" class="text-sm text-gray-600 mt-2 line-clamp-2">{{ a.notes }}</p>
            <div class="flex items-center gap-4 mt-2">
              <span v-if="a.duration" class="text-xs text-gray-500"><ClockIcon class="h-3 w-3 inline mr-1" />{{ a.duration }} dk</span>
              <span :class="['text-xs px-2 py-0.5 rounded-full', getOutcomeBadge(a.outcome)]">{{ getOutcomeLabel(a.outcome) }}</span>
            </div>
          </div>
          <button @click="handleDelete(a.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg flex-shrink-0"><TrashIcon class="h-4 w-4" /></button>
        </div>
      </div>
      <div v-if="activities.length === 0" class="p-12 text-center">
        <ClipboardDocumentListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Aktivite bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Yeni Aktivite</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
            <select v-model="form.type" class="w-full rounded-lg border-gray-300">
              <option value="call">Arama</option>
              <option value="email">E-posta</option>
              <option value="meeting">Toplantı</option>
              <option value="note">Not</option>
            </select>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Konu</label><input v-model="form.subject" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label><textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Sonuç</label>
            <select v-model="form.outcome" class="w-full rounded-lg border-gray-300">
              <option value="positive">Olumlu</option>
              <option value="neutral">Nötr</option>
              <option value="negative">Olumsuz</option>
            </select>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { PlusIcon, ClipboardDocumentListIcon, PhoneIcon, EnvelopeIcon, CalendarIcon, ClockIcon, ChatBubbleLeftIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useLeadActivityStore } from '@/stores/leadactivity'

const store = useLeadActivityStore()
const showModal = ref(false)
const form = ref({ type: 'call', subject: '', notes: '', outcome: 'neutral' })
const activities = ref<any[]>([])

const callCount = computed(() => activities.value.filter(a => a.type === 'call').length)
const emailCount = computed(() => activities.value.filter(a => a.type === 'email').length)
const meetingCount = computed(() => activities.value.filter(a => a.type === 'meeting').length)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getTypeLabel = (t: string) => ({ call: 'Arama', email: 'E-posta', meeting: 'Toplantı', note: 'Not' }[t] || t)
const getTypeBg = (t: string) => ({ call: 'bg-blue-500', email: 'bg-green-500', meeting: 'bg-purple-500', note: 'bg-gray-500' }[t] || 'bg-orange-500')
const getTypeIcon = (t: string) => { const icons: Record<string, any> = { call: markRaw(PhoneIcon), email: markRaw(EnvelopeIcon), meeting: markRaw(CalendarIcon), note: markRaw(ChatBubbleLeftIcon) }; return icons[t] || markRaw(ClipboardDocumentListIcon) }
const getOutcomeLabel = (o: string) => ({ positive: 'Olumlu', neutral: 'Nötr', negative: 'Olumsuz' }[o] || o)
const getOutcomeBadge = (o: string) => ({ positive: 'bg-green-100 text-green-800', neutral: 'bg-gray-100 text-gray-800', negative: 'bg-red-100 text-red-800' }[o] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { type: 'call', subject: '', notes: '', outcome: 'neutral' }; showModal.value = true }
const handleSubmit = async () => { await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); activities.value = r?.data || [] }
onMounted(() => { loadData() })
</script>