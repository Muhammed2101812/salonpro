<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Grup Randevuları</h1>
        <p class="mt-2 text-sm text-gray-600">Birden fazla kişilik randevuları yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Grup
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UserGroupIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Grup</p><p class="text-2xl font-bold">{{ groups.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CalendarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><UsersIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Katılımcı</p><p class="text-2xl font-bold">{{ totalParticipants }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ClockIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bugün</p><p class="text-2xl font-bold">{{ todayCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Grup Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="group in groups" :key="group.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', group.status === 'active' ? 'bg-green-500' : group.status === 'completed' ? 'bg-gray-400' : 'bg-yellow-500']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div>
              <h3 class="font-semibold text-gray-900">{{ group.name }}</h3>
              <p class="text-sm text-gray-500">{{ group.service?.name || 'Hizmet belirtilmemiş' }}</p>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusClass(group.status)]">{{ getStatusLabel(group.status) }}</span>
          </div>
          <div class="bg-blue-50 rounded-lg p-3 mb-4">
            <div class="flex items-center justify-between text-sm">
              <span class="text-blue-700 flex items-center gap-1"><CalendarDaysIcon class="h-4 w-4" />{{ formatDate(group.appointment_date) }}</span>
              <span class="text-blue-600 font-medium">{{ group.appointment_time }}</span>
            </div>
          </div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex -space-x-2">
              <div v-for="(p, i) in (group.participants || []).slice(0, 4)" :key="i" class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 border-2 border-white flex items-center justify-center text-white text-xs font-medium">
                {{ p.customer?.name?.charAt(0) || '?' }}
              </div>
              <div v-if="(group.participants?.length || 0) > 4" class="h-8 w-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-gray-600 text-xs font-medium">
                +{{ (group.participants?.length || 0) - 4 }}
              </div>
            </div>
            <span class="text-sm text-gray-500">{{ group.participants?.length || 0 }} / {{ group.max_participants || '∞' }}</span>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editGroup(group)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(group.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="groups.length === 0" class="col-span-full text-center py-12">
        <UserGroupIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Grup randevusu bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Grubu Düzenle' : 'Yeni Grup' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Grup Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label><input v-model="form.appointment_date" type="date" required class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Saat *</label><input v-model="form.appointment_time" type="time" required class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Max Katılımcı</label><input v-model.number="form.max_participants" type="number" min="2" class="w-full rounded-lg border-gray-300" /></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, UserGroupIcon, CalendarIcon, UsersIcon, ClockIcon, CalendarDaysIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useAppointmentGroupStore } from '@/stores/appointmentgroup'

const store = useAppointmentGroupStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', appointment_date: '', appointment_time: '', max_participants: 10, status: 'active' })
const groups = ref<any[]>([])

const activeCount = computed(() => groups.value.filter(g => g.status === 'active').length)
const totalParticipants = computed(() => groups.value.reduce((s, g) => s + (g.participants?.length || 0), 0))
const todayCount = computed(() => { const t = new Date().toISOString().split('T')[0]; return groups.value.filter(g => g.appointment_date === t).length })
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const getStatusLabel = (s: string) => ({ active: 'Aktif', completed: 'Tamamlandı', cancelled: 'İptal' }[s] || s)
const getStatusClass = (s: string) => ({ active: 'bg-green-100 text-green-800', completed: 'bg-gray-100 text-gray-800', cancelled: 'bg-red-100 text-red-800' }[s] || 'bg-gray-100')

const openCreateModal = () => { form.value = { name: '', appointment_date: '', appointment_time: '', max_participants: 10, status: 'active' }; isEdit.value = false; showModal.value = true }
const editGroup = (g: any) => { form.value = { ...g }; isEdit.value = true; editingId.value = g.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); groups.value = r?.data || [] }
onMounted(() => { loadData() })
</script>