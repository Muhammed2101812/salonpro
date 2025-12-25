<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Para Birimleri</h1>
        <p class="mt-2 text-sm text-gray-600">Sistemde kullanılan para birimlerini yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Para Birimi
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><CurrencyDollarIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ currencies.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CheckCircleIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-blue-600">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><StarIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Varsayılan</p><p class="text-2xl font-bold">{{ defaultCurrency }}</p></div>
        </div>
      </div>
    </div>

    <!-- Para Birimi Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="c in currencies" :key="c.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
              <span class="text-xl font-bold text-white">{{ c.symbol || c.code?.charAt(0) || '₺' }}</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">{{ c.name }}</h3>
              <code class="text-xs text-gray-500 bg-gray-100 px-1 rounded">{{ c.code }}</code>
            </div>
          </div>
          <span v-if="c.is_default" class="px-2 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-800">Varsayılan</span>
        </div>
        <div class="space-y-2 mb-4">
          <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
            <span class="text-sm text-gray-600">Simge</span>
            <span class="font-bold text-gray-900">{{ c.symbol || '-' }}</span>
          </div>
          <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
            <span class="text-sm text-gray-600">Ondalık</span>
            <span class="font-bold text-gray-900">{{ c.decimal_places || 2 }}</span>
          </div>
        </div>
        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
          <span :class="['text-xs font-medium', c.is_active !== false ? 'text-green-600' : 'text-gray-400']">{{ c.is_active !== false ? 'Aktif' : 'Pasif' }}</span>
          <div class="flex gap-2">
            <button @click="editCurrency(c)" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(c.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="currencies.length === 0" class="col-span-full text-center py-12">
        <CurrencyDollarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Para birimi bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Para Birimini Düzenle' : 'Yeni Para Birimi' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Para Birimi Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" placeholder="Türk Lirası" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kod *</label><input v-model="form.code" required class="w-full rounded-lg border-gray-300 uppercase" placeholder="TRY" maxlength="3" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Simge</label><input v-model="form.symbol" class="w-full rounded-lg border-gray-300" placeholder="₺" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Ondalık Basamak</label><input v-model.number="form.decimal_places" type="number" min="0" max="4" class="w-full rounded-lg border-gray-300" /></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_default" class="rounded border-gray-300 text-emerald-600" /><span class="text-sm">Varsayılan para birimi</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, CurrencyDollarIcon, CheckCircleIcon, StarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCurrencyStore } from '@/stores/currency'

const store = useCurrencyStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', code: '', symbol: '', decimal_places: 2, is_default: false, is_active: true })
const currencies = ref<any[]>([])

const activeCount = computed(() => currencies.value.filter(c => c.is_active !== false).length)
const defaultCurrency = computed(() => { const d = currencies.value.find(c => c.is_default); return d?.code || 'TRY' })

const openCreateModal = () => { form.value = { name: '', code: '', symbol: '', decimal_places: 2, is_default: false, is_active: true }; isEdit.value = false; showModal.value = true }
const editCurrency = (c: any) => { form.value = { ...c }; isEdit.value = true; editingId.value = c.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); currencies.value = r?.data || [] }
onMounted(() => { loadData() })
</script>