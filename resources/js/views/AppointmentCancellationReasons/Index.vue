<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">İptal Nedenleri</h1>
        <p class="mt-2 text-sm text-gray-600">Randevu iptal nedenlerini tanımlayın ve yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Neden
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><DocumentTextIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Neden</p><p class="text-2xl font-bold">{{ reasons.length }}</p></div>
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
          <div class="p-3 rounded-full bg-red-100"><XCircleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">En Çok Kullanılan</p><p class="text-2xl font-bold">{{ topReason }}</p></div>
        </div>
      </div>
    </div>

    <!-- Neden Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="reason in reasons" :key="reason.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
              <ExclamationTriangleIcon class="h-5 w-5 text-orange-600" />
            </div>
            <div><h3 class="font-semibold text-gray-900">{{ reason.name }}</h3><p class="text-xs text-gray-500">{{ reason.description || 'Açıklama yok' }}</p></div>
          </div>
          <span :class="['px-2 py-1 text-xs rounded-full font-medium', reason.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
            {{ reason.is_active ? 'Aktif' : 'Pasif' }}
          </span>
        </div>
        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg mb-4">
          <span class="text-sm text-orange-700">Kullanım</span>
          <span class="text-lg font-bold text-orange-600">{{ reason.usage_count || 0 }}</span>
        </div>
        <div class="flex justify-end gap-2">
          <button @click="editReason(reason)" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
          <button @click="handleDelete(reason.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
        </div>
      </div>
      <div v-if="reasons.length === 0" class="col-span-full text-center py-12">
        <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">İptal nedeni bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Nedeni Düzenle' : 'Yeni Neden' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Neden Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-orange-600" /><span class="text-sm">Aktif</span></label>
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
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, DocumentTextIcon, CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useAppointmentCancellationReasonStore } from '@/stores/appointmentcancellationreason'

const store = useAppointmentCancellationReasonStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', description: '', is_active: true })
const reasons = ref<any[]>([])

const activeCount = computed(() => reasons.value.filter(r => r.is_active).length)
const topReason = computed(() => { const top = [...reasons.value].sort((a, b) => (b.usage_count || 0) - (a.usage_count || 0))[0]; return top?.name || '-' })

const openCreateModal = () => { form.value = { name: '', description: '', is_active: true }; isEdit.value = false; showModal.value = true }
const editReason = (r: any) => { form.value = { ...r }; isEdit.value = true; editingId.value = r.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); reasons.value = r?.data || [] }
onMounted(() => { loadData() })
</script>