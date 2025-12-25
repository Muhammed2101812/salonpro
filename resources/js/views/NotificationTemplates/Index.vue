<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bildirim Şablonları</h1>
        <p class="mt-2 text-sm text-gray-600">SMS, E-posta ve push bildirim şablonlarınızı yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Şablon
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><BellIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ templates.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><DevicePhoneMobileIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">SMS</p><p class="text-2xl font-bold text-green-600">{{ smsCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><EnvelopeIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">E-posta</p><p class="text-2xl font-bold text-purple-600">{{ emailCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><BellAlertIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Push</p><p class="text-2xl font-bold text-orange-600">{{ pushCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Şablon Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="t in templates" :key="t.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getChannelColor(t.channel)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getChannelBg(t.channel)]">
                <component :is="getChannelIcon(t.channel)" :class="['h-5 w-5', getChannelText(t.channel)]" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ t.name }}</h3>
                <span class="text-xs text-gray-500">{{ getChannelLabel(t.channel) }}</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', t.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ t.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-600 line-clamp-2">{{ t.subject || t.content?.substring(0, 100) || 'İçerik yok' }}</p>
          </div>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <span>Tetikleyici: {{ t.trigger || 'Belirlenmedi' }}</span>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="previewTemplate(t)" class="text-sm font-medium text-blue-600 hover:text-blue-700">Önizle</button>
            <div class="flex gap-2">
              <button @click="editTemplate(t)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(t.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="templates.length === 0" class="col-span-full text-center py-12">
        <BellIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Şablon bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Şablonu Düzenle' : 'Yeni Şablon' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-2">Kanal *</label>
            <div class="grid grid-cols-3 gap-2">
              <button v-for="c in channels" :key="c.value" type="button" @click="form.channel = c.value" :class="['p-3 rounded-lg border text-center text-sm', form.channel === c.value ? c.activeClass : 'border-gray-200']">{{ c.label }}</button>
            </div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Konu</label><input v-model="form.subject" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İçerik</label><textarea v-model="form.content" rows="4" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { PlusIcon, BellIcon, DevicePhoneMobileIcon, EnvelopeIcon, BellAlertIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useNotificationTemplateStore } from '@/stores/notificationtemplate'

const store = useNotificationTemplateStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const channels = [
  { value: 'sms', label: 'SMS', activeClass: 'bg-green-50 border-green-500 text-green-700' },
  { value: 'email', label: 'E-posta', activeClass: 'bg-purple-50 border-purple-500 text-purple-700' },
  { value: 'push', label: 'Push', activeClass: 'bg-orange-50 border-orange-500 text-orange-700' }
]
const form = ref({ name: '', channel: 'sms', subject: '', content: '', is_active: true })
const templates = ref<any[]>([])

const smsCount = computed(() => templates.value.filter(t => t.channel === 'sms').length)
const emailCount = computed(() => templates.value.filter(t => t.channel === 'email').length)
const pushCount = computed(() => templates.value.filter(t => t.channel === 'push').length)
const getChannelLabel = (c: string) => ({ sms: 'SMS', email: 'E-posta', push: 'Push' }[c] || c)
const getChannelColor = (c: string) => ({ sms: 'bg-green-500', email: 'bg-purple-500', push: 'bg-orange-500' }[c] || 'bg-gray-500')
const getChannelBg = (c: string) => ({ sms: 'bg-green-100', email: 'bg-purple-100', push: 'bg-orange-100' }[c] || 'bg-gray-100')
const getChannelText = (c: string) => ({ sms: 'text-green-600', email: 'text-purple-600', push: 'text-orange-600' }[c] || 'text-gray-600')
const getChannelIcon = (c: string) => { const icons: Record<string, any> = { sms: markRaw(DevicePhoneMobileIcon), email: markRaw(EnvelopeIcon), push: markRaw(BellAlertIcon) }; return icons[c] || markRaw(BellIcon) }

const openCreateModal = () => { form.value = { name: '', channel: 'sms', subject: '', content: '', is_active: true }; isEdit.value = false; showModal.value = true }
const editTemplate = (t: any) => { form.value = { ...t }; isEdit.value = true; editingId.value = t.id; showModal.value = true }
const previewTemplate = (t: any) => { alert(`${t.name}\n\n${t.content || 'İçerik yok'}`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); templates.value = r?.data || [] }
onMounted(() => { loadData() })
</script>