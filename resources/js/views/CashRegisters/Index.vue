<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Kasa Yönetimi</h1>
        <p class="mt-2 text-sm text-gray-600">Kasalarınızı ve nakit hareketlerinizi takip edin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Kasa
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100"><CalculatorIcon class="h-6 w-6 text-teal-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kasa Sayısı</p><p class="text-2xl font-bold">{{ registers.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><BanknotesIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Nakit</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalCash) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><LockOpenIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Açık Kasa</p><p class="text-2xl font-bold text-blue-600">{{ openCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-gray-100"><LockClosedIcon class="h-6 w-6 text-gray-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kapalı Kasa</p><p class="text-2xl font-bold">{{ closedCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kasa Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="register in registers" :key="register.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', register.is_open ? 'bg-green-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['h-12 w-12 rounded-lg flex items-center justify-center', register.is_open ? 'bg-gradient-to-br from-teal-500 to-emerald-500' : 'bg-gray-200']">
                <CalculatorIcon :class="['h-6 w-6', register.is_open ? 'text-white' : 'text-gray-500']" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ register.name }}</h3>
                <p class="text-sm text-gray-500">{{ register.branch?.name || 'Merkez' }}</p>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', register.is_open ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ register.is_open ? 'Açık' : 'Kapalı' }}
            </span>
          </div>

          <div class="bg-teal-50 rounded-lg p-4 mb-4">
            <p class="text-xs text-teal-600 mb-1">Mevcut Bakiye</p>
            <p class="text-2xl font-bold text-teal-700">{{ formatCurrency(register.current_balance || 0) }}</p>
            <div class="flex justify-between text-xs text-teal-600 mt-2">
              <span>Açılış: {{ formatCurrency(register.opening_balance || 0) }}</span>
            </div>
          </div>

          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="toggleOpen(register)" :class="['text-sm font-medium', register.is_open ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700']">
              {{ register.is_open ? 'Kasa Kapat' : 'Kasa Aç' }}
            </button>
            <div class="flex gap-2">
              <button @click="viewDetails(register)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><EyeIcon class="h-4 w-4" /></button>
              <button @click="editRegister(register)" class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(register.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="registers.length === 0" class="col-span-full text-center py-12">
        <CalculatorIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Kasa bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Kasayı Düzenle' : 'Yeni Kasa' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kasa Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Açılış Bakiyesi</label><input v-model.number="form.opening_balance" type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Bakiye</label><input v-model.number="form.current_balance" type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, CalculatorIcon, BanknotesIcon, LockOpenIcon, LockClosedIcon, EyeIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCashRegisterStore } from '@/stores/cashregister'

const store = useCashRegisterStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', opening_balance: 0, current_balance: 0, description: '', is_open: true })
const registers = ref<any[]>([])

const totalCash = computed(() => registers.value.reduce((s, r) => s + (parseFloat(r.current_balance) || 0), 0))
const openCount = computed(() => registers.value.filter(r => r.is_open).length)
const closedCount = computed(() => registers.value.filter(r => !r.is_open).length)
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)

const openCreateModal = () => { form.value = { name: '', opening_balance: 0, current_balance: 0, description: '', is_open: true }; isEdit.value = false; showModal.value = true }
const editRegister = (r: any) => { form.value = { ...r }; isEdit.value = true; editingId.value = r.id; showModal.value = true }
const viewDetails = (r: any) => { alert(`Kasa: ${r.name}\nBakiye: ${formatCurrency(r.current_balance || 0)}`) }
const toggleOpen = async (r: any) => { await store.update(r.id, { is_open: !r.is_open }); await loadData() }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); registers.value = r?.data || [] }
onMounted(() => { loadData() })
</script>