<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Leadler</h1>
        <p class="mt-2 text-sm text-gray-600">Potansiyel müşterileri takip edin ve yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Lead
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><UserPlusIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Lead</p><p class="text-2xl font-bold">{{ leads.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><FunnelIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Yeni</p><p class="text-2xl font-bold text-blue-600">{{ newCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ChatBubbleLeftRightIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">İletişimde</p><p class="text-2xl font-bold text-yellow-600">{{ contactedCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Dönüştürülen</p><p class="text-2xl font-bold text-green-600">{{ convertedCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Lead Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="l in leads" :key="l.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getStatusColor(l.status)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center">
                <span class="text-orange-600 font-bold">{{ getInitials(l.name) }}</span>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ l.name }}</h3>
                <span class="text-xs text-gray-500">{{ l.company || l.email || '' }}</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(l.status)]">{{ getStatusLabel(l.status) }}</span>
          </div>
          <div class="space-y-2 mb-4">
            <div v-if="l.phone" class="flex items-center gap-2 text-sm text-gray-600"><PhoneIcon class="h-4 w-4" />{{ l.phone }}</div>
            <div v-if="l.email" class="flex items-center gap-2 text-sm text-gray-600"><EnvelopeIcon class="h-4 w-4" />{{ l.email }}</div>
            <div v-if="l.source" class="flex items-center gap-2 text-sm text-gray-600"><GlobeAltIcon class="h-4 w-4" />{{ l.source }}</div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-400">{{ formatDate(l.created_at) }}</span>
            <div class="flex gap-2">
              <button @click="convertLead(l)" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg"><UserIcon class="h-4 w-4" /></button>
              <button @click="editLead(l)" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(l.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="leads.length === 0" class="col-span-full text-center py-12">
        <UserPlusIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Lead bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Lead Düzenle' : 'Yeni Lead' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label><input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label><input v-model="form.phone" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kaynak</label>
            <select v-model="form.source" class="w-full rounded-lg border-gray-300">
              <option value="website">Web Sitesi</option>
              <option value="referral">Referans</option>
              <option value="social">Sosyal Medya</option>
              <option value="walk_in">Yürüyerek Gelen</option>
              <option value="other">Diğer</option>
            </select>
          </div>
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
import { PlusIcon, UserPlusIcon, FunnelIcon, ChatBubbleLeftRightIcon, CheckCircleIcon, PhoneIcon, EnvelopeIcon, GlobeAltIcon, UserIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useLeadStore } from '@/stores/lead'

const store = useLeadStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', email: '', phone: '', source: 'website' })
const leads = ref<any[]>([])

const newCount = computed(() => leads.value.filter(l => l.status === 'new').length)
const contactedCount = computed(() => leads.value.filter(l => l.status === 'contacted').length)
const convertedCount = computed(() => leads.value.filter(l => l.status === 'converted').length)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const getStatusLabel = (s: string) => ({ new: 'Yeni', contacted: 'İletişimde', qualified: 'Nitelikli', converted: 'Dönüştürüldü', lost: 'Kayıp' }[s] || s || 'Yeni')
const getStatusBadge = (s: string) => ({ new: 'bg-blue-100 text-blue-800', contacted: 'bg-yellow-100 text-yellow-800', qualified: 'bg-purple-100 text-purple-800', converted: 'bg-green-100 text-green-800', lost: 'bg-red-100 text-red-800' }[s] || 'bg-gray-100 text-gray-800')
const getStatusColor = (s: string) => ({ new: 'bg-blue-500', contacted: 'bg-yellow-500', qualified: 'bg-purple-500', converted: 'bg-green-500', lost: 'bg-red-500' }[s] || 'bg-gray-500')

const openCreateModal = () => { form.value = { name: '', email: '', phone: '', source: 'website' }; isEdit.value = false; showModal.value = true }
const editLead = (l: any) => { form.value = { ...l }; isEdit.value = true; editingId.value = l.id; showModal.value = true }
const convertLead = (l: any) => { alert(`${l.name} müşteriye dönüştürülüyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); leads.value = r?.data || [] }
onMounted(() => { loadData() })
</script>