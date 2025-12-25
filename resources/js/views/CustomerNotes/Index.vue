<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Notları</h1>
        <p class="mt-2 text-sm text-gray-600">Müşterileriniz hakkındaki notları yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportNotes"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Not
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100">
            <DocumentTextIcon class="h-6 w-6 text-teal-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Not</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <ExclamationCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Önemli</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.important }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <BellIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Hatırlatma</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.reminders }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <UserGroupIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.customers }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Not veya müşteri ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="type in typeFilters"
              :key="type.value"
              @click="filters.type = filters.type === type.value ? '' : type.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.type === type.value ? type.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ type.label }}
            </button>
          </div>

          <select v-model="filters.customer_id" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
            <option value="">Tüm Müşteriler</option>
            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Not Kartları -->
    <div v-else class="space-y-3">
      <div v-for="note in filteredNotes" :key="note.id" :class="['bg-white rounded-xl shadow-sm border border-gray-100 p-4 border-l-4', getTypeBorderColor(note.note_type)]">
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-3">
            <div :class="['p-2 rounded-lg', getTypeBg(note.note_type)]">
              <component :is="getTypeIcon(note.note_type)" :class="['h-5 w-5', getTypeIconColor(note.note_type)]" />
            </div>
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="font-medium text-gray-900">{{ note.customer?.name || 'Bilinmiyor' }}</span>
                <span :class="['px-2 py-0.5 text-xs rounded-full font-medium', getTypeBadge(note.note_type)]">
                  {{ getTypeLabel(note.note_type) }}
                </span>
              </div>
              <p class="text-gray-700">{{ note.content }}</p>
              <p class="text-xs text-gray-400 mt-2">{{ formatDateTime(note.created_at) }} • {{ note.created_by?.name || 'Sistem' }}</p>
            </div>
          </div>
          <div class="flex gap-1">
            <button
              v-if="note.is_pinned"
              class="p-1.5 text-yellow-500"
              title="Sabitlenmiş"
            >
              <StarIcon class="h-4 w-4 fill-current" />
            </button>
            <button
              @click="editNote(note)"
              class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
              title="Düzenle"
            >
              <PencilIcon class="h-4 w-4" />
            </button>
            <button
              @click="handleDelete(note.id)"
              class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              title="Sil"
            >
              <TrashIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredNotes.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Not bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-teal-600 hover:text-teal-700 font-medium">
          Not ekleyin
        </button>
      </div>
    </div>

    <!-- Not Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Notu Düzenle' : 'Yeni Not' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
            <select v-model="form.customer_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
              <option value="">Müşteri Seçin</option>
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Not Tipi *</label>
            <div class="grid grid-cols-4 gap-2">
              <button
                v-for="type in noteTypes"
                :key="type.value"
                type="button"
                @click="form.note_type = type.value"
                :class="['p-3 rounded-lg border text-center transition-colors', form.note_type === type.value ? type.activeClass : 'border-gray-200 hover:border-teal-300']"
              >
                <component :is="type.icon" class="h-5 w-5 mx-auto mb-1" />
                <span class="text-xs font-medium">{{ type.label }}</span>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Not İçeriği *</label>
            <textarea v-model="form.content" rows="4" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Not içeriğini yazın..."></textarea>
          </div>

          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.is_pinned" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" />
              <span class="text-sm text-gray-700">Sabitle</span>
            </label>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  PlusIcon,
  DocumentTextIcon,
  ExclamationCircleIcon,
  BellIcon,
  UserGroupIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  StarIcon,
  ChatBubbleLeftIcon,
  HeartIcon,
  ShieldExclamationIcon
} from '@heroicons/vue/24/outline'
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid'
import { useCustomerNoteStore } from '@/stores/customernote'
import { useCustomerStore } from '@/stores/customer'

const store = useCustomerNoteStore()
const customerStore = useCustomerStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ type: '', customer_id: '' })

const stats = ref({ total: 0, important: 0, reminders: 0, customers: 0 })

const typeFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-teal-600 text-white' },
  { value: 'general', label: 'Genel', activeClass: 'bg-blue-600 text-white' },
  { value: 'important', label: 'Önemli', activeClass: 'bg-red-600 text-white' },
  { value: 'reminder', label: 'Hatırlatma', activeClass: 'bg-yellow-600 text-white' },
  { value: 'preference', label: 'Tercih', activeClass: 'bg-purple-600 text-white' }
]

const noteTypes = [
  { value: 'general', label: 'Genel', icon: markRaw(ChatBubbleLeftIcon), activeClass: 'bg-blue-50 text-blue-700 border-blue-500' },
  { value: 'important', label: 'Önemli', icon: markRaw(ExclamationCircleIcon), activeClass: 'bg-red-50 text-red-700 border-red-500' },
  { value: 'reminder', label: 'Hatırlatma', icon: markRaw(BellIcon), activeClass: 'bg-yellow-50 text-yellow-700 border-yellow-500' },
  { value: 'preference', label: 'Tercih', icon: markRaw(HeartIcon), activeClass: 'bg-purple-50 text-purple-700 border-purple-500' }
]

const form = ref({ customer_id: '', note_type: 'general', content: '', is_pinned: false })

const notes = ref<any[]>([])
const customers = computed(() => customerStore.customers || [])

const filteredNotes = computed(() => {
  let result = notes.value
  if (search.value) {
    const s = search.value.toLowerCase()
    result = result.filter(n => n.content?.toLowerCase().includes(s) || n.customer?.name?.toLowerCase().includes(s))
  }
  if (filters.value.type) result = result.filter(n => n.note_type === filters.value.type)
  if (filters.value.customer_id) result = result.filter(n => n.customer_id === filters.value.customer_id)
  return result.sort((a, b) => (b.is_pinned ? 1 : 0) - (a.is_pinned ? 1 : 0) || new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
})

const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getTypeLabel = (type: string) => ({ general: 'Genel', important: 'Önemli', reminder: 'Hatırlatma', preference: 'Tercih' }[type] || type)
const getTypeBadge = (type: string) => ({ general: 'bg-blue-100 text-blue-800', important: 'bg-red-100 text-red-800', reminder: 'bg-yellow-100 text-yellow-800', preference: 'bg-purple-100 text-purple-800' }[type] || 'bg-gray-100')
const getTypeBg = (type: string) => ({ general: 'bg-blue-100', important: 'bg-red-100', reminder: 'bg-yellow-100', preference: 'bg-purple-100' }[type] || 'bg-gray-100')
const getTypeIconColor = (type: string) => ({ general: 'text-blue-600', important: 'text-red-600', reminder: 'text-yellow-600', preference: 'text-purple-600' }[type] || 'text-gray-600')
const getTypeBorderColor = (type: string) => ({ general: 'border-l-blue-500', important: 'border-l-red-500', reminder: 'border-l-yellow-500', preference: 'border-l-purple-500' }[type] || 'border-l-gray-300')
const getTypeIcon = (type: string) => {
  const icons: Record<string, any> = { general: markRaw(ChatBubbleLeftIcon), important: markRaw(ExclamationCircleIcon), reminder: markRaw(BellIcon), preference: markRaw(HeartIcon) }
  return icons[type] || markRaw(DocumentTextIcon)
}

const updateStats = () => {
  stats.value.total = notes.value.length
  stats.value.important = notes.value.filter(n => n.note_type === 'important').length
  stats.value.reminders = notes.value.filter(n => n.note_type === 'reminder').length
  const uniqueCustomers = new Set(notes.value.map(n => n.customer_id))
  stats.value.customers = uniqueCustomers.size
}

const openCreateModal = () => { form.value = { customer_id: '', note_type: 'general', content: '', is_pinned: false }; isEdit.value = false; editingId.value = null; showModal.value = true }
const editNote = (note: any) => { form.value = { ...note }; isEdit.value = true; editingId.value = note.id; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await store.update(editingId.value, form.value) }
    else { await store.create(form.value) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const handleDelete = async (id: string) => { if (!confirm('Bu notu silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    notes.value = response?.data || []
    await customerStore.fetchCustomers()
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportNotes = () => {
  const csvContent = [
    ['Müşteri', 'Tip', 'İçerik', 'Tarih'].join(','),
    ...filteredNotes.value.map(n => [n.customer?.name || '', getTypeLabel(n.note_type), `"${n.content?.replace(/"/g, '""') || ''}"`, n.created_at].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `musteri_notlari_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>