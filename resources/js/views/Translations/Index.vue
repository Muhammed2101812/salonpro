<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çeviriler</h1>
        <p class="mt-2 text-sm text-gray-600">Sistem çevirilerini ve dil dosyalarını yönetin</p>
      </div>
      <div class="flex gap-3">
        <button @click="exportTranslations" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
          <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
        </button>
        <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
          <PlusIcon class="h-5 w-5 mr-2" />Yeni Çeviri
        </button>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><LanguageIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Çeviri</p><p class="text-2xl font-bold">{{ translations.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><GlobeAltIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Dil Sayısı</p><p class="text-2xl font-bold text-green-600">{{ languageCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ExclamationCircleIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Eksik</p><p class="text-2xl font-bold text-yellow-600">{{ missingCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CheckCircleIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Tamamlanma</p><p class="text-2xl font-bold text-purple-600">{{ completionRate }}%</p></div>
        </div>
      </div>
    </div>

    <!-- Dil Seçimi -->
    <div class="flex gap-2 flex-wrap">
      <button v-for="lang in languages" :key="lang" @click="selectedLang = lang" :class="['px-4 py-2 rounded-lg text-sm font-medium transition', selectedLang === lang ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50']">
        {{ getLanguageName(lang) }}
      </button>
    </div>

    <!-- Çeviri Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="search" type="text" placeholder="Çeviri ara..." class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anahtar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Değer</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Grup</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="t in filteredTranslations" :key="t.id" class="hover:bg-gray-50">
            <td class="px-6 py-4"><code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ t.key }}</code></td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ t.value || '-' }}</td>
            <td class="px-6 py-4 text-center"><span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ t.group || 'general' }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="editTranslation(t)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(t.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredTranslations.length === 0" class="p-12 text-center">
        <LanguageIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Çeviri bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Çeviriyi Düzenle' : 'Yeni Çeviri' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Anahtar *</label><input v-model="form.key" required class="w-full rounded-lg border-gray-300 font-mono text-sm" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Değer *</label><textarea v-model="form.value" rows="3" required class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Dil</label><input v-model="form.locale" class="w-full rounded-lg border-gray-300" placeholder="tr" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Grup</label><input v-model="form.group" class="w-full rounded-lg border-gray-300" placeholder="general" /></div>
          </div>
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
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, LanguageIcon, GlobeAltIcon, ExclamationCircleIcon, CheckCircleIcon, PencilIcon, TrashIcon, XMarkIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { useTranslationStore } from '@/stores/translation'

const store = useTranslationStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const selectedLang = ref('tr')
const search = ref('')
const languages = ['tr', 'en', 'de', 'ar']
const form = ref({ key: '', value: '', locale: 'tr', group: 'general' })
const translations = ref<any[]>([])

const languageCount = computed(() => new Set(translations.value.map(t => t.locale)).size)
const missingCount = computed(() => translations.value.filter(t => !t.value).length)
const completionRate = computed(() => translations.value.length ? Math.round(((translations.value.length - missingCount.value) / translations.value.length) * 100) : 100)
const filteredTranslations = computed(() => translations.value.filter(t => (t.locale === selectedLang.value || !t.locale) && (!search.value || t.key?.includes(search.value) || t.value?.includes(search.value))))
const getLanguageName = (l: string) => ({ tr: 'Türkçe', en: 'English', de: 'Deutsch', ar: 'العربية' }[l] || l)

const openCreateModal = () => { form.value = { key: '', value: '', locale: selectedLang.value, group: 'general' }; isEdit.value = false; showModal.value = true }
const editTranslation = (t: any) => { form.value = { ...t }; isEdit.value = true; editingId.value = t.id; showModal.value = true }
const exportTranslations = () => { alert('Çeviriler dışa aktarılıyor...') }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); translations.value = r?.data || [] }
onMounted(() => { loadData() })
</script>