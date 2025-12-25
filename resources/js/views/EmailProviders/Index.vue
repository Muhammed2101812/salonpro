<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">E-posta Sağlayıcıları</h1>
        <p class="mt-2 text-sm text-gray-600">E-posta gönderim servislerini yapılandırın</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Sağlayıcı
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-rose-100"><EnvelopeIcon class="h-6 w-6 text-rose-600" /></div>
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
          <div class="p-3 rounded-full bg-blue-100"><StarIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Varsayılan</p><p class="text-lg font-bold text-blue-600">{{ defaultProvider }}</p></div>
        </div>
      </div>
    </div>

    <!-- Sağlayıcı Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="p in providers" :key="p.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', p.is_active ? 'bg-green-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['h-12 w-12 rounded-lg flex items-center justify-center', getProviderBg(p.driver)]">
                <EnvelopeIcon class="h-6 w-6 text-white" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ p.name }}</h3>
                <span class="text-xs text-gray-500">{{ getDriverLabel(p.driver) }}</span>
              </div>
            </div>
            <div class="flex flex-col items-end gap-1">
              <span v-if="p.is_default" class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Varsayılan</span>
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', p.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
                {{ p.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">SMTP Host</span>
              <span class="font-medium text-gray-900">{{ p.host || '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Port</span>
              <span class="font-medium text-gray-900">{{ p.port || '-' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Gönderen</span>
              <span class="font-medium text-gray-900">{{ p.from_email || '-' }}</span>
            </div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="testProvider(p)" class="text-sm font-medium text-blue-600 hover:text-blue-700">Test Et</button>
            <div class="flex gap-2">
              <button @click="setDefault(p)" v-if="!p.is_default" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg"><StarIcon class="h-4 w-4" /></button>
              <button @click="editProvider(p)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(p.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="providers.length === 0" class="col-span-full text-center py-12">
        <EnvelopeIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">E-posta sağlayıcı bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Sağlayıcıyı Düzenle' : 'Yeni Sağlayıcı' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Sağlayıcı Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Driver</label>
            <select v-model="form.driver" class="w-full rounded-lg border-gray-300">
              <option value="smtp">SMTP</option>
              <option value="ses">Amazon SES</option>
              <option value="mailgun">Mailgun</option>
              <option value="sendgrid">SendGrid</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Host</label><input v-model="form.host" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Port</label><input v-model.number="form.port" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı</label><input v-model="form.username" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label><input v-model="form.password" type="password" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Gönderen E-posta</label><input v-model="form.from_email" type="email" class="w-full rounded-lg border-gray-300" /></div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_default" class="rounded border-gray-300 text-rose-600" /><span class="text-sm">Varsayılan sağlayıcı</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, EnvelopeIcon, CheckCircleIcon, StarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useEmailProviderStore } from '@/stores/emailprovider'

const store = useEmailProviderStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', driver: 'smtp', host: '', port: 587, username: '', password: '', from_email: '', is_active: true, is_default: false })
const providers = ref<any[]>([])

const activeCount = computed(() => providers.value.filter(p => p.is_active).length)
const defaultProvider = computed(() => { const d = providers.value.find(p => p.is_default); return d?.name || '-' })
const getProviderBg = (d: string) => ({ smtp: 'bg-gray-600', ses: 'bg-orange-600', mailgun: 'bg-red-600', sendgrid: 'bg-blue-600' }[d] || 'bg-rose-600')
const getDriverLabel = (d: string) => ({ smtp: 'SMTP', ses: 'Amazon SES', mailgun: 'Mailgun', sendgrid: 'SendGrid' }[d] || d)

const openCreateModal = () => { form.value = { name: '', driver: 'smtp', host: '', port: 587, username: '', password: '', from_email: '', is_active: true, is_default: false }; isEdit.value = false; showModal.value = true }
const editProvider = (p: any) => { form.value = { ...p }; isEdit.value = true; editingId.value = p.id; showModal.value = true }
const testProvider = (p: any) => { alert(`${p.name} test ediliyor...`) }
const setDefault = async (p: any) => { await store.update(p.id, { is_default: true }); await loadData() }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); providers.value = r?.data || [] }
onMounted(() => { loadData() })
</script>