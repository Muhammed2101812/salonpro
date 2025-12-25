<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stok Denetimleri</h1>
        <p class="mt-2 text-sm text-gray-600">Stok sayım ve denetim kayıtlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-lime-600 hover:bg-lime-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Denetim
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-lime-100"><ClipboardDocumentCheckIcon class="h-6 w-6 text-lime-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Denetim</p><p class="text-2xl font-bold">{{ audits.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Tamamlanan</p><p class="text-2xl font-bold text-green-600">{{ completedCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Fark Var</p><p class="text-2xl font-bold text-yellow-600">{{ discrepancyCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CubeIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ürün Sayımı</p><p class="text-2xl font-bold text-blue-600">{{ totalProducts }}</p></div>
        </div>
      </div>
    </div>

    <!-- Denetim Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denetim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fark</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="a in audits" :key="a.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-lime-100 flex items-center justify-center"><ClipboardDocumentCheckIcon class="h-5 w-5 text-lime-600" /></div>
                <div>
                  <div class="font-medium text-gray-900">{{ a.name || 'Denetim #' + a.id?.slice(0, 6) }}</div>
                  <div class="text-xs text-gray-500">{{ a.auditor?.name || 'Denetçi' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(a.audit_date || a.created_at) }}</td>
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ a.products_count || 0 }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', a.discrepancy ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800']">
                {{ a.discrepancy || 0 }}
              </span>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(a.status)]">{{ getStatusLabel(a.status) }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="viewDetails(a)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><EyeIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(a.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="audits.length === 0" class="p-12 text-center">
        <ClipboardDocumentCheckIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Denetim bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Yeni Denetim</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Denetim Adı</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tarih</label><input v-model="form.audit_date" type="date" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Not</label><textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-lime-600 text-white rounded-lg">Başlat</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ClipboardDocumentCheckIcon, CheckCircleIcon, ExclamationTriangleIcon, CubeIcon, EyeIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useStockAuditStore } from '@/stores/stockaudit'

const store = useStockAuditStore()
const showModal = ref(false)
const form = ref({ name: '', audit_date: '', notes: '' })
const audits = ref<any[]>([])

const completedCount = computed(() => audits.value.filter(a => a.status === 'completed').length)
const discrepancyCount = computed(() => audits.value.filter(a => a.discrepancy > 0).length)
const totalProducts = computed(() => audits.value.reduce((s, a) => s + (a.products_count || 0), 0))
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getStatusLabel = (s: string) => ({ pending: 'Bekliyor', in_progress: 'Devam Ediyor', completed: 'Tamamlandı' }[s] || s || 'Bekliyor')
const getStatusBadge = (s: string) => ({ pending: 'bg-gray-100 text-gray-800', in_progress: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800' }[s] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { name: '', audit_date: new Date().toISOString().split('T')[0], notes: '' }; showModal.value = true }
const viewDetails = (a: any) => { alert(`Denetim detayları görüntüleniyor...`) }
const handleSubmit = async () => { await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); audits.value = r?.data || [] }
onMounted(() => { loadData() })
</script>