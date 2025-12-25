<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Referans Programları</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri referans programlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Program
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-pink-100"><GiftIcon class="h-6 w-6 text-pink-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Program</p><p class="text-2xl font-bold">{{ programs.length }}</p></div>
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
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Referans</p><p class="text-2xl font-bold text-blue-600">{{ totalReferrals }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CurrencyDollarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ödül Değeri</p><p class="text-2xl font-bold text-purple-600">{{ formatCurrency(totalRewards) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Program Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="p in programs" :key="p.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', p.is_active ? 'bg-pink-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-gray-900">{{ p.name }}</h3>
              <span class="text-xs text-gray-500">{{ p.referrals_count || 0 }} referans</span>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', p.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ p.is_active ? 'Aktif' : 'Pasif' }}</span>
          </div>
          <div class="bg-pink-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-600">Davet Eden Ödülü</span><span class="font-bold text-pink-600">{{ formatCurrency(p.referrer_reward) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-600">Davet Edilen Ödülü</span><span class="font-bold text-pink-600">{{ formatCurrency(p.referred_reward) }}</span></div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="copyLink(p)" class="text-sm font-medium text-blue-600 hover:text-blue-700">Linki Kopyala</button>
            <div class="flex gap-2">
              <button @click="editProgram(p)" class="p-1.5 text-pink-600 hover:bg-pink-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(p.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="programs.length === 0" class="col-span-full text-center py-12">
        <GiftIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Program bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Programı Düzenle' : 'Yeni Program' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Program Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Davet Eden Ödülü</label><input v-model.number="form.referrer_reward" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Davet Edilen Ödülü</label><input v-model.number="form.referred_reward" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-pink-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, GiftIcon, CheckCircleIcon, UsersIcon, CurrencyDollarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useReferralProgramStore } from '@/stores/referralprogram'

const store = useReferralProgramStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', referrer_reward: 0, referred_reward: 0, is_active: true })
const programs = ref<any[]>([])

const activeCount = computed(() => programs.value.filter(p => p.is_active).length)
const totalReferrals = computed(() => programs.value.reduce((s, p) => s + (p.referrals_count || 0), 0))
const totalRewards = computed(() => programs.value.reduce((s, p) => s + (p.referrer_reward || 0) + (p.referred_reward || 0), 0))
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)

const openCreateModal = () => { form.value = { name: '', referrer_reward: 0, referred_reward: 0, is_active: true }; isEdit.value = false; showModal.value = true }
const editProgram = (p: any) => { form.value = { ...p }; isEdit.value = true; editingId.value = p.id; showModal.value = true }
const copyLink = (p: any) => { navigator.clipboard.writeText(`https://salon.com/ref/${p.code || p.id}`); alert('Link kopyalandı!') }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); programs.value = r?.data || [] }
onMounted(() => { loadData() })
</script>