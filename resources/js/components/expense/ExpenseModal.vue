<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Gider Düzenle' : 'Yeni Gider'"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Başlık -->
      <Input
        v-model="formData.title"
        label="Başlık"
        required
        placeholder="Gider başlığı..."
      />

      <!-- Kategori Seçimi -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
        <div class="grid grid-cols-4 gap-2">
          <button
            v-for="cat in expenseCategories"
            :key="cat.value"
            type="button"
            @click="formData.category = cat.value"
            :class="[
              'flex flex-col items-center p-3 rounded-lg border transition-colors',
              formData.category === cat.value ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
            ]"
          >
            <component :is="cat.icon" class="h-5 w-5 mb-1" />
            <span class="text-xs font-medium">{{ cat.label }}</span>
          </button>
        </div>
      </div>

      <!-- Tutar ve Tarih -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.amount"
          type="number"
          label="Tutar (₺)"
          step="0.01"
          min="0"
          required
          class="text-lg font-bold"
        />
        <Input
          v-model="formData.expense_date"
          type="date"
          label="Tarih"
          required
        />
      </div>

      <!-- Şube -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
        <select
          v-model="formData.branch_id"
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 h-[42px]"
        >
          <option value="">Şube Seçin</option>
          <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
        </select>
      </div>

      <!-- Açıklama -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
        <textarea
          v-model="formData.description"
          rows="2"
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
        ></textarea>
      </div>

      <!-- Form Butonları -->
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <Button variant="secondary" @click="$emit('update:modelValue', false)" label="İptal" />
        <Button
          type="submit"
          variant="danger"
          :loading="loading"
          :label="loading ? 'Kaydediliyor...' : 'Kaydet'"
        />
      </div>
    </form>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch, markRaw } from 'vue'
import {
  HomeIcon,
  BoltIcon,
  UsersIcon,
  CubeIcon,
  WrenchScrewdriverIcon,
  MegaphoneIcon,
  EllipsisHorizontalCircleIcon
} from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Modal from '@/components/ui/Modal.vue'

interface Props {
  modelValue: boolean
  isEdit?: boolean
  initialData?: any
  loading?: boolean
  branches?: any[]
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null,
  branches: () => []
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  title: '',
  category: '',
  amount: 0,
  expense_date: new Date().toISOString().split('T')[0],
  branch_id: '',
  description: ''
})

const expenseCategories = [
  { value: 'Kira', label: 'Kira', icon: markRaw(HomeIcon), color: 'text-purple-600' },
  { value: 'Fatura', label: 'Fatura', icon: markRaw(BoltIcon), color: 'text-yellow-600' },
  { value: 'Maaş', label: 'Maaş', icon: markRaw(UsersIcon), color: 'text-blue-600' },
  { value: 'Malzeme', label: 'Malzeme', icon: markRaw(CubeIcon), color: 'text-green-600' },
  { value: 'Bakım', label: 'Bakım', icon: markRaw(WrenchScrewdriverIcon), color: 'text-orange-600' },
  { value: 'Pazarlama', label: 'Pazarlama', icon: markRaw(MegaphoneIcon), color: 'text-pink-600' },
  { value: 'Diğer', label: 'Diğer', icon: markRaw(EllipsisHorizontalCircleIcon), color: 'text-gray-600' }
]

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        title: '',
        category: '',
         amount: 0,
        expense_date: new Date().toISOString().split('T')[0],
        branch_id: '',
        description: ''
    }
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = {
            ...val,
            amount: Number(val.amount)
        }
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
