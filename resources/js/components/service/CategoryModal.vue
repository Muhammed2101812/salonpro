<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Kategori Düzenle' : 'Yeni Kategori'"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <Input
        v-model="formData.name"
        label="Kategori Adı"
        required
        placeholder="Örn: Saç Kesimi"
      />

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
        <textarea
          v-model="formData.description"
          rows="3"
          placeholder="Kategori açıklaması..."
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm"
        ></textarea>
      </div>

      <div class="flex items-center">
        <input
          v-model="formData.is_active"
          type="checkbox"
          id="category_is_active"
          class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
        />
        <label for="category_is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
      </div>

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
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  name: '',
  description: '',
  is_active: true
})

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        name: '',
        description: '',
        is_active: true
    }
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = { ...val }
    } else {
        resetForm()
    }
}, { immediate: true })

watch(() => props.modelValue, (val) => {
    if (val && !props.isEdit && !props.initialData) {
        resetForm()
    }
})

const handleSubmit = () => {
    emit('submit', formData.value)
}
</script>
