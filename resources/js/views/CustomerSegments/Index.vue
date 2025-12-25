<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Segmentleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri gruplarını ve segmentlerini yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Segment
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100"><UsersIcon class="h-6 w-6 text-violet-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Segment</p><p class="text-2xl font-bold">{{ segments.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UserGroupIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Müşteri</p><p class="text-2xl font-bold text-blue-600">{{ totalMembers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ChartBarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Segment</p><p class="text-2xl font-bold text-green-600">{{ avgMembers }}</p></div>
        </div>
      </div>
    </div>

    <!-- Segment Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="s in segments" :key="s.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getSegmentColor(s.color)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['h-12 w-12 rounded-lg flex items-center justify-center', getSegmentBg(s.color)]">
                <UsersIcon class="h-6 w-6 text-white" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ s.name }}</h3>
                <span class="text-xs text-gray-500">{{ s.members_count || 0 }} müşteri</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', s.is_auto ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600']">{{ s.is_auto ? 'Otomatik' : 'Manuel' }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ s.description || 'Açıklama yok' }}</p>
          <div v-if="s.criteria" class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-xs text-gray-500 mb-1">Kriterler:</p>
            <p class="text-sm text-gray-700">{{ s.criteria }}</p>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="viewMembers(s)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><EyeIcon class="h-4 w-4" /></button>
            <button @click="editSegment(s)" class="p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(s.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="segments.length === 0" class="col-span-full text-center py-12">
        <UsersIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Segment bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Segmenti Düzenle' : 'Yeni Segment' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Segment Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
            <select v-model="form.color" class="w-full rounded-lg border-gray-300">
              <option value="violet">Mor</option>
              <option value="blue">Mavi</option>
              <option value="green">Yeşil</option>
              <option value="orange">Turuncu</option>
              <option value="red">Kırmızı</option>
            </select>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_auto" class="rounded border-gray-300 text-violet-600" /><span class="text-sm">Otomatik segment</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-violet-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, UsersIcon, UserGroupIcon, ChartBarIcon, EyeIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCustomerSegmentStore } from '@/stores/customersegment'

const store = useCustomerSegmentStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', description: '', color: 'violet', is_auto: false })
const segments = ref<any[]>([])

const totalMembers = computed(() => segments.value.reduce((s, seg) => s + (seg.members_count || 0), 0))
const avgMembers = computed(() => segments.value.length ? Math.round(totalMembers.value / segments.value.length) : 0)
const getSegmentColor = (c: string) => ({ violet: 'bg-violet-500', blue: 'bg-blue-500', green: 'bg-green-500', orange: 'bg-orange-500', red: 'bg-red-500' }[c] || 'bg-violet-500')
const getSegmentBg = (c: string) => ({ violet: 'bg-violet-500', blue: 'bg-blue-500', green: 'bg-green-500', orange: 'bg-orange-500', red: 'bg-red-500' }[c] || 'bg-violet-500')

const openCreateModal = () => { form.value = { name: '', description: '', color: 'violet', is_auto: false }; isEdit.value = false; showModal.value = true }
const editSegment = (s: any) => { form.value = { ...s }; isEdit.value = true; editingId.value = s.id; showModal.value = true }
const viewMembers = (s: any) => { alert(`${s.name} segmentindeki müşteriler görüntüleniyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); segments.value = r?.data || [] }
onMounted(() => { loadData() })
</script>