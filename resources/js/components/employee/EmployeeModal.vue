<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Çalışan Düzenle' : 'Yeni Çalışan'"
    size="lg"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Ad Soyad -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.first_name"
          label="Ad"
          required
        />
        <Input
          v-model="formData.last_name"
          label="Soyad"
          required
        />
      </div>

      <!-- İletişim -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.phone"
          type="tel"
          label="Telefon"
          placeholder="05XX XXX XX XX"
        />
        <Input
          v-model="formData.email"
          type="email"
          label="E-posta"
        />
      </div>

      <!-- Şube ve Pozisyon -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
          <select
            v-model="formData.branch_id"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary h-[42px]"
          >
            <option value="">Şube Seçin</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Pozisyon</label>
          <select
            v-model="formData.position"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary h-[42px]"
          >
            <option value="">Pozisyon Seçin</option>
            <option v-for="pos in positions" :key="pos.value" :value="pos.value">
              {{ pos.label }}
            </option>
          </select>
        </div>
      </div>

      <!-- Komisyon ve İşe Başlama -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.commission_rate"
          type="number"
          label="Komisyon Oranı (%)"
          step="1"
          min="0"
          max="100"
        />
        <Input
          v-model="formData.hire_date"
          type="date"
          label="İşe Başlama Tarihi"
        />
      </div>

      <!-- Uzmanlık Alanları -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Uzmanlık Alanları</label>
        <div class="flex gap-2">
            <input
            v-model="specialtiesInput"
            @keydown.enter.prevent="addSpecialty"
            type="text"
            placeholder="Yazın ve Ekle'ye basın"
            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
            />
            <Button type="button" variant="secondary" @click="addSpecialty" label="Ekle" />
        </div>
        <div v-if="formData.specialties.length > 0" class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="(specialty, index) in formData.specialties"
            :key="index"
            class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"
          >
            {{ specialty }}
            <button type="button" @click="removeSpecialty(index)" class="ml-2 text-purple-600 hover:text-purple-900 font-bold">×</button>
          </span>
        </div>
      </div>

      <!-- Aktif -->
      <div class="flex items-center">
        <input
          v-model="formData.is_active"
          type="checkbox"
          id="is_active"
          class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
        />
        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
      </div>

      <!-- Form Butonları -->
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <Button variant="secondary" @click="$emit('update:modelValue', false)" label="İptal" />
        <Button
          type="submit"
          variant="primary"
          :loading="loading"
          :label="loading ? 'Kaydediliyor...' : 'Kaydet'"
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
  branches?: any[]
  positions?: any[]
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null,
  branches: () => [],
  positions: () => []
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  branch_id: '',
  position: '',
  specialties: [] as string[],
  commission_rate: 0,
  hire_date: '',
  is_active: true
})

const specialtiesInput = ref('')

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        branch_id: props.branches[0]?.id || '',
        position: '',
        specialties: [],
        commission_rate: 0,
        hire_date: '',
        is_active: true
    }
    specialtiesInput.value = ''
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = {
            ...val,
            specialties: [...(val.specialties || [])],
            commission_rate: Number(val.commission_rate || 0)
        }
    } else {
        resetForm()
    }
}, { immediate: true })

watch(() => props.modelValue, (val) => {
    if (val && !props.isEdit && !props.initialData) {
        resetForm()
        if (props.branches.length > 0) {
            formData.value.branch_id = props.branches[0].id
        }
    }
})

const addSpecialty = () => {
  if (specialtiesInput.value.trim()) {
    formData.value.specialties.push(specialtiesInput.value.trim())
    specialtiesInput.value = ''
  }
}

const removeSpecialty = (index: number) => {
  formData.value.specialties.splice(index, 1)
}

const handleSubmit = () => {
    emit('submit', formData.value)
}
</script>
