<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Segment Üyeleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri segmentlerindeki üyeleri görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-violet-100"><UsersIcon class="h-6 w-6 text-violet-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Üye</p><p class="text-2xl font-bold">{{ members.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><RectangleGroupIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Segment</p><p class="text-2xl font-bold text-blue-600">{{ uniqueSegments }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Filtre ve Tablo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <input v-model="search" type="text" placeholder="Müşteri ara..." class="flex-1 rounded-lg border-gray-300" />
        <select v-model="segmentFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Segmentler</option>
          <option v-for="s in segments" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Segment</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Eklenme</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="m in filteredMembers" :key="m.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-violet-100 flex items-center justify-center">
                  <span class="text-violet-600 font-bold">{{ getInitials(m.customer?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ m.customer?.name || 'Müşteri' }}</div>
                  <div class="text-xs text-gray-500">{{ m.customer?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-violet-100 text-violet-800 font-medium">{{ m.segment?.name || 'Segment' }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', m.is_active !== false ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ m.is_active !== false ? 'Aktif' : 'Pasif' }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(m.added_at || m.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="handleDelete(m.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredMembers.length === 0" class="p-12 text-center">
        <UsersIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Üye bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { UsersIcon, RectangleGroupIcon, CheckCircleIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useSegmentMemberStore } from '@/stores/segmentmember'

const store = useSegmentMemberStore()
const search = ref('')
const segmentFilter = ref('')
const members = ref<any[]>([])
const segments = ref<any[]>([])

const uniqueSegments = computed(() => new Set(members.value.filter(m => m.segment_id).map(m => m.segment_id)).size)
const activeCount = computed(() => members.value.filter(m => m.is_active !== false).length)
const filteredMembers = computed(() => members.value.filter(m => (!segmentFilter.value || m.segment_id === segmentFilter.value) && (!search.value || m.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))))
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'

const handleDelete = async (id: string) => { if (confirm('Üyeyi segmentten çıkarmak istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); members.value = r?.data || []; segments.value = [...new Map(members.value.filter(m => m.segment).map(m => [m.segment.id, m.segment])).values()] }
onMounted(() => { loadData() })
</script>
