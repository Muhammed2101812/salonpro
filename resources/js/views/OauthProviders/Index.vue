<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">OAuth Sağlayıcıları</h1>
        <p class="mt-2 text-sm text-gray-600">Sosyal giriş ve API entegrasyonlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Sağlayıcı
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><FingerPrintIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ providers.length }}</p></div>
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
          <div class="ml-4"><p class="text-sm text-gray-500">Giriş Sayısı</p><p class="text-2xl font-bold text-blue-600">{{ totalLogins }}</p></div>
        </div>
      </div>
    </div>

    <!-- Sağlayıcı Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="p in providers" :key="p.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getProviderColor(p.driver)]"></div>
        <div class="p-5">
          <div class="flex items-center gap-4 mb-4">
            <div :class="['h-14 w-14 rounded-xl flex items-center justify-center', getProviderBg(p.driver)]">
              <component :is="getProviderIcon(p.driver)" class="h-7 w-7 text-white" />
            </div>
            <div>
              <h3 class="font-bold text-gray-900">{{ getProviderLabel(p.driver) }}</h3>
              <span :class="['text-xs font-medium', p.is_active ? 'text-green-600' : 'text-gray-400']">{{ p.is_active ? 'Aktif' : 'Pasif' }}</span>
            </div>
          </div>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-4">
            <span class="text-sm text-gray-500">Giriş</span>
            <span class="text-lg font-bold text-gray-900">{{ p.login_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="toggleActive(p)" :class="[p.is_active ? 'text-red-600' : 'text-green-600', 'text-sm font-medium']">{{ p.is_active ? 'Deaktif' : 'Aktif Et' }}</button>
            <div class="flex gap-2">
              <button @click="editProvider(p)" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(p.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="providers.length === 0" class="col-span-full text-center py-12">
        <FingerPrintIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">OAuth sağlayıcı bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Sağlayıcıyı Düzenle' : 'Yeni Sağlayıcı' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Sağlayıcı</label>
            <select v-model="form.driver" class="w-full rounded-lg border-gray-300">
              <option value="google">Google</option>
              <option value="facebook">Facebook</option>
              <option value="twitter">Twitter/X</option>
              <option value="github">GitHub</option>
              <option value="apple">Apple</option>
            </select>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Client ID *</label><input v-model="form.client_id" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Client Secret *</label><input v-model="form.client_secret" type="password" required class="w-full rounded-lg border-gray-300" /></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-purple-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { PlusIcon, FingerPrintIcon, CheckCircleIcon, UsersIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useOauthProviderStore } from '@/stores/oauthprovider'

const store = useOauthProviderStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ driver: 'google', client_id: '', client_secret: '', is_active: true })
const providers = ref<any[]>([])

const activeCount = computed(() => providers.value.filter(p => p.is_active).length)
const totalLogins = computed(() => providers.value.reduce((s, p) => s + (p.login_count || 0), 0))
const getProviderLabel = (d: string) => ({ google: 'Google', facebook: 'Facebook', twitter: 'Twitter/X', github: 'GitHub', apple: 'Apple' }[d] || d)
const getProviderColor = (d: string) => ({ google: 'bg-red-500', facebook: 'bg-blue-600', twitter: 'bg-sky-500', github: 'bg-gray-900', apple: 'bg-black' }[d] || 'bg-purple-500')
const getProviderBg = (d: string) => ({ google: 'bg-red-500', facebook: 'bg-blue-600', twitter: 'bg-sky-500', github: 'bg-gray-900', apple: 'bg-black' }[d] || 'bg-purple-500')
const getProviderIcon = () => markRaw(FingerPrintIcon)

const openCreateModal = () => { form.value = { driver: 'google', client_id: '', client_secret: '', is_active: true }; isEdit.value = false; showModal.value = true }
const editProvider = (p: any) => { form.value = { ...p }; isEdit.value = true; editingId.value = p.id; showModal.value = true }
const toggleActive = async (p: any) => { await store.update(p.id, { is_active: !p.is_active }); await loadData() }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); providers.value = r?.data || [] }
onMounted(() => { loadData() })
</script>