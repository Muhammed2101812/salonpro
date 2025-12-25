<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Webhooks</h1>
        <p class="mt-2 text-sm text-gray-600">API webhook entegrasyonlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Webhook
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100"><LinkIcon class="h-6 w-6 text-slate-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ webhooks.length }}</p></div>
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
          <div class="p-3 rounded-full bg-blue-100"><ArrowPathIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Son 24s</p><p class="text-2xl font-bold text-blue-600">{{ last24hCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ExclamationCircleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Başarısız</p><p class="text-2xl font-bold text-red-600">{{ failedCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Webhook Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Webhook</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Olaylar</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="w in webhooks" :key="w.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', w.is_active ? 'bg-green-100' : 'bg-gray-100']">
                  <LinkIcon :class="['h-5 w-5', w.is_active ? 'text-green-600' : 'text-gray-400']" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ w.name || 'Webhook' }}</div>
                  <div class="text-xs text-gray-500">{{ w.secret ? '••••••' + w.secret.slice(-4) : 'Secret yok' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4"><code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ truncateUrl(w.url) }}</code></td>
            <td class="px-6 py-4 text-center">
              <div class="flex flex-wrap justify-center gap-1">
                <span v-for="e in (w.events || []).slice(0, 2)" :key="e" class="px-2 py-0.5 text-xs bg-slate-100 rounded">{{ e }}</span>
                <span v-if="(w.events?.length || 0) > 2" class="text-xs text-gray-500">+{{ w.events.length - 2 }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', w.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ w.is_active ? 'Aktif' : 'Pasif' }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="testWebhook(w)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><ArrowPathIcon class="h-4 w-4" /></button>
              <button @click="editWebhook(w)" class="p-1.5 text-slate-600 hover:bg-slate-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(w.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="webhooks.length === 0" class="p-12 text-center">
        <LinkIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Webhook bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Webhook Düzenle' : 'Yeni Webhook' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İsim</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">URL *</label><input v-model="form.url" required type="url" class="w-full rounded-lg border-gray-300" placeholder="https://" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Secret</label><input v-model="form.secret" class="w-full rounded-lg border-gray-300 font-mono" /></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-slate-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-slate-700 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, LinkIcon, CheckCircleIcon, ArrowPathIcon, ExclamationCircleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useWebhookStore } from '@/stores/webhook'

const store = useWebhookStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', url: '', secret: '', is_active: true })
const webhooks = ref<any[]>([])

const activeCount = computed(() => webhooks.value.filter(w => w.is_active).length)
const last24hCount = computed(() => webhooks.value.filter(w => { const d = new Date(w.last_triggered_at); return Date.now() - d.getTime() < 24 * 60 * 60 * 1000 }).length)
const failedCount = computed(() => webhooks.value.filter(w => w.last_status === 'failed').length)
const truncateUrl = (url: string) => url?.length > 40 ? url.slice(0, 40) + '...' : url

const openCreateModal = () => { form.value = { name: '', url: '', secret: '', is_active: true }; isEdit.value = false; showModal.value = true }
const editWebhook = (w: any) => { form.value = { ...w }; isEdit.value = true; editingId.value = w.id; showModal.value = true }
const testWebhook = (w: any) => { alert(`${w.name || 'Webhook'} test ediliyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); webhooks.value = r?.data || [] }
onMounted(() => { loadData() })
</script>