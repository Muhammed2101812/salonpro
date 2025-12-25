<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Anketler</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri memnuniyet anketlerini yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Anket
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-cyan-100"><ClipboardDocumentListIcon class="h-6 w-6 text-cyan-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Anket</p><p class="text-2xl font-bold">{{ surveys.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UsersIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Yanıt</p><p class="text-2xl font-bold text-blue-600">{{ totalResponses }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ChartBarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Puan</p><p class="text-2xl font-bold text-purple-600">{{ avgScore.toFixed(1) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Anket Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="s in surveys" :key="s.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', s.is_active ? 'bg-cyan-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-gray-900">{{ s.title || s.name }}</h3>
              <span class="text-xs text-gray-500">{{ s.questions_count || 0 }} soru</span>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', s.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ s.is_active ? 'Aktif' : 'Pasif' }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ s.description || 'Açıklama yok' }}</p>
          <div class="bg-cyan-50 rounded-lg p-3 mb-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Yanıt Sayısı</span>
              <span class="text-lg font-bold text-cyan-600">{{ s.responses_count || 0 }}</span>
            </div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="viewResults(s)" class="text-sm font-medium text-blue-600 hover:text-blue-700">Sonuçlar</button>
            <div class="flex gap-2">
              <button @click="editSurvey(s)" class="p-1.5 text-cyan-600 hover:bg-cyan-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(s.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="surveys.length === 0" class="col-span-full text-center py-12">
        <ClipboardDocumentListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Anket bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Anketi Düzenle' : 'Yeni Anket' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Anket Başlığı *</label><input v-model="form.title" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300"></textarea></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-cyan-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ClipboardDocumentListIcon, CheckCircleIcon, UsersIcon, ChartBarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useSurveyStore } from '@/stores/survey'

const store = useSurveyStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ title: '', description: '', is_active: true })
const surveys = ref<any[]>([])

const activeCount = computed(() => surveys.value.filter(s => s.is_active).length)
const totalResponses = computed(() => surveys.value.reduce((sum, s) => sum + (s.responses_count || 0), 0))
const avgScore = computed(() => { const scores = surveys.value.filter(s => s.avg_score); return scores.length ? scores.reduce((sum, s) => sum + s.avg_score, 0) / scores.length : 0 })

const openCreateModal = () => { form.value = { title: '', description: '', is_active: true }; isEdit.value = false; showModal.value = true }
const editSurvey = (s: any) => { form.value = { ...s }; isEdit.value = true; editingId.value = s.id; showModal.value = true }
const viewResults = (s: any) => { alert(`${s.title} sonuçları görüntüleniyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); surveys.value = r?.data || [] }
onMounted(() => { loadData() })
</script>