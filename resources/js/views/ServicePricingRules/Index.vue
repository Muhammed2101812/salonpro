<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Fiyatlandırma Kuralları</h1>
        <p class="mt-2 text-sm text-gray-600">Dinamik fiyatlandırma kurallarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Kural
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><CalculatorIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kural</p><p class="text-2xl font-bold">{{ rules.length }}</p></div>
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
          <div class="p-3 rounded-full bg-orange-100"><ClockIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Zaman Bazlı</p><p class="text-2xl font-bold text-orange-600">{{ timeBasedCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kural Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="r in rules" :key="r.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', r.is_active ? 'bg-emerald-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-gray-900">{{ r.name }}</h3>
              <span class="text-xs text-gray-500">{{ getRuleTypeLabel(r.type) }}</span>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', r.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ r.is_active ? 'Aktif' : 'Pasif' }}</span>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Değişim</span><span :class="['font-bold', r.adjustment > 0 ? 'text-red-600' : 'text-green-600']">{{ r.adjustment > 0 ? '+' : '' }}{{ r.adjustment_type === 'percentage' ? '%' + r.adjustment : formatCurrency(r.adjustment) }}</span></div>
            <div v-if="r.start_time || r.end_time" class="flex justify-between text-sm"><span class="text-gray-500">Saat</span><span class="text-gray-700">{{ r.start_time || '00:00' }} - {{ r.end_time || '23:59' }}</span></div>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editRule(r)" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(r.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="rules.length === 0" class="col-span-full text-center py-12">
        <CalculatorIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kural bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Kuralı Düzenle' : 'Yeni Kural' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kural Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
            <select v-model="form.type" class="w-full rounded-lg border-gray-300">
              <option value="time">Zaman Bazlı</option>
              <option value="day">Gün Bazlı</option>
              <option value="demand">Talep Bazlı</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Değişim Türü</label>
              <select v-model="form.adjustment_type" class="w-full rounded-lg border-gray-300">
                <option value="percentage">Yüzde (%)</option>
                <option value="fixed">Sabit (₺)</option>
              </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Değer</label><input v-model.number="form.adjustment" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-emerald-600" /><span class="text-sm">Aktif</span></label>
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
import { PlusIcon, CalculatorIcon, CheckCircleIcon, ClockIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useServicePricingRuleStore } from '@/stores/servicepricingrule'

const store = useServicePricingRuleStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', type: 'time', adjustment_type: 'percentage', adjustment: 0, is_active: true })
const rules = ref<any[]>([])

const activeCount = computed(() => rules.value.filter(r => r.is_active).length)
const timeBasedCount = computed(() => rules.value.filter(r => r.type === 'time').length)
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const getRuleTypeLabel = (t: string) => ({ time: 'Zaman Bazlı', day: 'Gün Bazlı', demand: 'Talep Bazlı' }[t] || t)

const openCreateModal = () => { form.value = { name: '', type: 'time', adjustment_type: 'percentage', adjustment: 0, is_active: true }; isEdit.value = false; showModal.value = true }
const editRule = (r: any) => { form.value = { ...r }; isEdit.value = true; editingId.value = r.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); rules.value = r?.data || [] }
onMounted(() => { loadData() })
</script>