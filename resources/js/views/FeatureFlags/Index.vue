<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Özellik Bayrakları</h1>
        <p class="mt-2 text-sm text-gray-600">Uygulama özelliklerini kontrol edin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Bayrak
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100"><FlagIcon class="h-6 w-6 text-violet-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ flags.length }}</p></div>
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
          <div class="p-3 rounded-full bg-orange-100"><BeakerIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Beta</p><p class="text-2xl font-bold text-orange-600">{{ betaCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Bayrak Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="f in flags" :key="f.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', f.enabled ? 'bg-green-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', f.enabled ? 'bg-green-100' : 'bg-gray-100']">
                <FlagIcon :class="['h-5 w-5', f.enabled ? 'text-green-600' : 'text-gray-400']" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ f.name }}</h3>
                <code class="text-xs text-gray-500">{{ f.key }}</code>
              </div>
            </div>
            <button @click="toggleFlag(f)" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', f.enabled ? 'bg-green-500' : 'bg-gray-300']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', f.enabled ? 'translate-x-6' : 'translate-x-1']" />
            </button>
          </div>
          <p class="text-sm text-gray-600 mb-4">{{ f.description || 'Açıklama yok' }}</p>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <span v-if="f.is_beta" class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800 font-medium">Beta</span>
            <span v-else></span>
            <div class="flex gap-2">
              <button @click="editFlag(f)" class="p-1.5 text-violet-600 hover:bg-violet-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(f.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="flags.length === 0" class="col-span-full text-center py-12">
        <FlagIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Bayrak bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Bayrağı Düzenle' : 'Yeni Bayrak' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Anahtar *</label><input v-model="form.key" required class="w-full rounded-lg border-gray-300 font-mono" placeholder="feature_name" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="flex gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.enabled" class="rounded border-gray-300 text-green-600" /><span class="text-sm">Aktif</span></label>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_beta" class="rounded border-gray-300 text-orange-600" /><span class="text-sm">Beta</span></label>
          </div>
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
import { PlusIcon, FlagIcon, CheckCircleIcon, BeakerIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useFeatureFlagStore } from '@/stores/featureflag'

const store = useFeatureFlagStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', key: '', description: '', enabled: true, is_beta: false })
const flags = ref<any[]>([])

const activeCount = computed(() => flags.value.filter(f => f.enabled).length)
const betaCount = computed(() => flags.value.filter(f => f.is_beta).length)

const openCreateModal = () => { form.value = { name: '', key: '', description: '', enabled: true, is_beta: false }; isEdit.value = false; showModal.value = true }
const editFlag = (f: any) => { form.value = { ...f }; isEdit.value = true; editingId.value = f.id; showModal.value = true }
const toggleFlag = async (f: any) => { await store.update(f.id, { enabled: !f.enabled }); await loadData() }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); flags.value = r?.data || [] }
onMounted(() => { loadData() })
</script>