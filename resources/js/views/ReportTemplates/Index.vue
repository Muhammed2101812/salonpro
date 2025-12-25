<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Rapor Şablonları</h1>
      <p class="mt-2 text-gray-600">Özelleştirilebilir rapor şablonları oluşturun ve yönetin</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
        <p class="text-indigo-100 text-sm font-medium">Toplam Şablon</p>
        <p class="text-3xl font-bold mt-2">{{ templates.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <p class="text-green-100 text-sm font-medium">Aktif</p>
        <p class="text-3xl font-bold mt-2">{{ activeTemplates }}</p>
      </div>
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <p class="text-blue-100 text-sm font-medium">Bu Ay Oluşturulan</p>
        <p class="text-3xl font-bold mt-2">{{ reportsThisMonth }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <p class="text-purple-100 text-sm font-medium">Zamanlanmış</p>
        <p class="text-3xl font-bold mt-2">{{ scheduledTemplates }}</p>
      </div>
    </div>

    <!-- Controls -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4 items-center">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Şablon ara..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 w-64"
        >
        <select v-model="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          <option value="">Tüm Kategoriler</option>
          <option value="financial">Finansal</option>
          <option value="appointment">Randevu</option>
          <option value="customer">Müşteri</option>
          <option value="employee">Çalışan</option>
          <option value="inventory">Stok</option>
          <option value="marketing">Pazarlama</option>
        </select>
      </div>
      <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Yeni Şablon
      </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="template in filteredTemplates" :key="template.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition group">
        <div :class="getCategoryBgClass(template.category)" class="h-2"></div>
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ template.name }}</h3>
              <span :class="getCategoryClass(template.category)" class="text-xs px-2 py-1 rounded-full font-medium mt-1 inline-block">
                {{ getCategoryLabel(template.category) }}
              </span>
            </div>
            <span :class="getStatusClass(template.status)" class="px-3 py-1 text-xs rounded-full font-semibold">
              {{ getStatusLabel(template.status) }}
            </span>
          </div>

          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ template.description || 'Açıklama yok' }}</p>

          <!-- Format & Schedule Info -->
          <div class="flex items-center gap-4 mb-4">
            <div class="flex items-center gap-1 text-sm text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span>{{ getFormatLabel(template.format) }}</span>
            </div>
            <div v-if="template.schedule" class="flex items-center gap-1 text-sm text-gray-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ getScheduleLabel(template.schedule) }}</span>
            </div>
          </div>

          <!-- Preview Fields -->
          <div v-if="template.fields?.length" class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-xs text-gray-500 mb-2">İçerik Alanları:</p>
            <div class="flex flex-wrap gap-1">
              <span v-for="(field, idx) in template.fields.slice(0, 5)" :key="idx" class="text-xs bg-white px-2 py-1 rounded border">
                {{ field }}
              </span>
              <span v-if="template.fields.length > 5" class="text-xs text-gray-500 px-2 py-1">
                +{{ template.fields.length - 5 }} daha
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-between items-center pt-4 border-t">
            <button @click="generateReport(template)" class="flex items-center gap-1 px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Rapor Oluştur
            </button>
            <div class="flex gap-2">
              <button @click="openEditModal(template)" class="px-3 py-1 text-sm text-gray-600 hover:text-indigo-600">Düzenle</button>
              <button @click="deleteTemplate(template.id)" class="px-3 py-1 text-sm text-red-600 hover:text-red-800">Sil</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredTemplates.length === 0" class="col-span-full text-center py-12 text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p>Rapor şablonu bulunamadı</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Şablon Düzenle' : 'Yeni Şablon' }}</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label>
            <input v-model="form.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="form.description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
              <select v-model="form.category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">Seçin</option>
                <option value="financial">Finansal</option>
                <option value="appointment">Randevu</option>
                <option value="customer">Müşteri</option>
                <option value="employee">Çalışan</option>
                <option value="inventory">Stok</option>
                <option value="marketing">Pazarlama</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Format *</label>
              <select v-model="form.format" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="html">HTML</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Zamanlama</label>
              <select v-model="form.schedule" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="">Manuel</option>
                <option value="daily">Günlük</option>
                <option value="weekly">Haftalık</option>
                <option value="monthly">Aylık</option>
                <option value="quarterly">Çeyreklik</option>
                <option value="yearly">Yıllık</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
              <select v-model="form.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="active">Aktif</option>
                <option value="inactive">Pasif</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rapor Alanları</label>
            <div class="flex flex-wrap gap-2 mb-2">
              <span v-for="(field, idx) in form.fields" :key="idx" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                {{ field }}
                <button type="button" @click="removeField(idx)" class="hover:text-red-600">&times;</button>
              </span>
            </div>
            <div class="flex gap-2">
              <input v-model="newField" type="text" @keyup.enter.prevent="addField" placeholder="Alan adı ekle..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
              <button type="button" @click="addField" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">Ekle</button>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useReportTemplateStore } from '@/stores/reporttemplate';

const store = useReportTemplateStore();

const loading = ref(false);
const searchQuery = ref('');
const categoryFilter = ref('');
const showModal = ref(false);
const isEdit = ref(false);
const editingId = ref<string | null>(null);
const newField = ref('');

const form = ref({
  name: '',
  description: '',
  category: '',
  format: 'pdf',
  schedule: '',
  status: 'active',
  fields: [] as string[]
});

const templates = computed(() => store.templates || []);

const activeTemplates = computed(() => templates.value.filter((t: any) => t.status === 'active').length);
const scheduledTemplates = computed(() => templates.value.filter((t: any) => t.schedule).length);
const reportsThisMonth = computed(() => templates.value.filter((t: any) => {
  if (!t.last_generated) return false;
  const date = new Date(t.last_generated);
  const now = new Date();
  return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
}).length);

const filteredTemplates = computed(() => {
  return templates.value.filter((template: any) => {
    const matchesSearch = !searchQuery.value || template.name?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = !categoryFilter.value || template.category === categoryFilter.value;
    return matchesSearch && matchesCategory;
  });
});

const addField = () => {
  if (newField.value.trim()) {
    form.value.fields.push(newField.value.trim());
    newField.value = '';
  }
};

const removeField = (index: number) => {
  form.value.fields.splice(index, 1);
};

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    category: '',
    format: 'pdf',
    schedule: '',
    status: 'active',
    fields: []
  };
  newField.value = '';
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (template: any) => {
  form.value = { ...template, fields: template.fields || [] };
  isEdit.value = true;
  editingId.value = template.id;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const handleSubmit = async () => {
  loading.value = true;
  try {
    if (isEdit.value && editingId.value) {
      await store.update(editingId.value, form.value);
    } else {
      await store.create(form.value);
    }
    closeModal();
    await loadData();
  } catch (err) {
    console.error('Şablon kaydedilemedi:', err);
  } finally {
    loading.value = false;
  }
};

const deleteTemplate = async (id: string) => {
  if (confirm('Bu şablonu silmek istediğinizden emin misiniz?')) {
    await store.delete(id);
    await loadData();
  }
};

const generateReport = (template: any) => {
  console.log('Generate report:', template);
  alert(`"${template.name}" raporu oluşturuluyor...`);
};

const getStatusClass = (status: string) => {
  return status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
};

const getStatusLabel = (status: string) => {
  return status === 'active' ? 'Aktif' : 'Pasif';
};

const getCategoryClass = (category: string) => {
  const classes: Record<string, string> = {
    financial: 'bg-green-100 text-green-800',
    appointment: 'bg-blue-100 text-blue-800',
    customer: 'bg-purple-100 text-purple-800',
    employee: 'bg-orange-100 text-orange-800',
    inventory: 'bg-yellow-100 text-yellow-800',
    marketing: 'bg-pink-100 text-pink-800'
  };
  return classes[category] || 'bg-gray-100 text-gray-800';
};

const getCategoryBgClass = (category: string) => {
  const classes: Record<string, string> = {
    financial: 'bg-green-500',
    appointment: 'bg-blue-500',
    customer: 'bg-purple-500',
    employee: 'bg-orange-500',
    inventory: 'bg-yellow-500',
    marketing: 'bg-pink-500'
  };
  return classes[category] || 'bg-gray-500';
};

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    financial: 'Finansal',
    appointment: 'Randevu',
    customer: 'Müşteri',
    employee: 'Çalışan',
    inventory: 'Stok',
    marketing: 'Pazarlama'
  };
  return labels[category] || category;
};

const getFormatLabel = (format: string) => {
  const labels: Record<string, string> = {
    pdf: 'PDF',
    excel: 'Excel',
    csv: 'CSV',
    html: 'HTML'
  };
  return labels[format] || format?.toUpperCase();
};

const getScheduleLabel = (schedule: string) => {
  const labels: Record<string, string> = {
    daily: 'Günlük',
    weekly: 'Haftalık',
    monthly: 'Aylık',
    quarterly: 'Çeyreklik',
    yearly: 'Yıllık'
  };
  return labels[schedule] || 'Manuel';
};

const loadData = async () => {
  loading.value = true;
  try {
    await store.fetchAll({});
  } catch (err) {
    console.error('Şablonlar yüklenemedi:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadData();
});
</script>