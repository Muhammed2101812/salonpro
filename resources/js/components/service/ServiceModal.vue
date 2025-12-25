<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Hizmet Düzenle' : 'Yeni Hizmet'"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Kategori -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
        <select
          v-model="formData.service_category_id"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 h-[42px]"
        >
          <option value="">Kategori Seçin</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <!-- Hizmet Adı -->
      <Input
        v-model="formData.name"
        label="Hizmet Adı"
        required
        placeholder="Örn: Erkek Saç Kesimi"
      />

      <!-- Açıklama -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
        <textarea
          v-model="formData.description"
          rows="2"
          placeholder="Hizmet açıklaması..."
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
        ></textarea>
      </div>

      <!-- Fiyat ve Süre -->
      <div class="grid grid-cols-2 gap-4">
        <Input
            v-model="formData.price"
            type="number"
            label="Fiyat (₺)"
            step="0.01"
            min="0"
            required
        />
        <Input
            v-model="formData.duration_minutes"
            type="number"
            label="Süre (Dakika)"
            min="5"
            step="5"
            required
        />
      </div>

      <!-- Süre Hızlı Seç -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Hızlı Süre Seç</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="dur in [15, 30, 45, 60, 90, 120]"
            :key="dur"
            type="button"
            @click="formData.duration_minutes = dur"
            :class="[
              'px-3 py-1.5 text-sm rounded-lg border transition-colors',
              formData.duration_minutes === dur
                ? 'bg-teal-100 text-teal-700 border-teal-300'
                : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
            ]"
          >
            {{ dur }} dk
          </button>
        </div>
      </div>

      <!-- Aktif -->
      <div class="flex items-center">
        <input
          v-model="formData.is_active"
          type="checkbox"
          id="service_is_active"
          class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
        />
        <label for="service_is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
      </div>

      <!-- Form Butonları -->
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <Button variant="secondary" @click="$emit('update:modelValue', false)" label="İptal" />
        <Button
          type="submit"
          variant="primary"
          :loading="loading"
          :label="loading ? 'Kaydediliyor...' : 'Kaydet'"
          class="bg-teal-600 hover:bg-teal-700 text-white"
        />
      </div>
    </form>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Modal from '@/components/ui/Modal.vue'

interface Props {
  modelValue: boolean
  isEdit?: boolean
  initialData?: any
  loading?: boolean
  categories?: any[]
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null,
  categories: () => []
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  service_category_id: '',
  name: '',
  description: '',
  price: 0,
  duration_minutes: 30,
  is_active: true
})

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        service_category_id: props.categories[0]?.id || '',
        name: '',
        description: '',
        price: 0,
        duration_minutes: 30,
        is_active: true
    }
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = {
            ...val,
            price: Number(val.price || 0),
            duration_minutes: Number(val.duration_minutes || 30)
        }
    } else {
        resetForm()
    }
}, { immediate: true })

watch(() => props.modelValue, (val) => {
    if (val && !props.isEdit && !props.initialData) {
        resetForm()
        if (props.categories.length > 0) {
            formData.value.service_category_id = props.categories[0].id
        }
    }
})

const handleSubmit = () => {
    emit('submit', formData.value)
}
</script>
