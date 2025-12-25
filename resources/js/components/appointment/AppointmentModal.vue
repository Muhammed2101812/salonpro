<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Randevu Düzenle' : 'Yeni Randevu'"
    size="lg"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Şube ve Müşteri -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <Select
            v-model="formData.branch_id"
            label="Şube"
            required
            :options="branchStore.branches"
            optionLabel="name"
            optionValue="id"
            placeholder="Şube Seçin"
        />
        <Select
            v-model="formData.customer_id"
            label="Müşteri"
            required
            :options="customerOptions"
            optionLabel="full_name"
            optionValue="id"
            placeholder="Müşteri Seçin"
        />
      </div>

      <!-- Çalışan ve Hizmet -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <Select
            v-model="formData.employee_id"
            label="Çalışan"
            required
            :options="employeeOptions"
            optionLabel="full_name"
            optionValue="id"
            placeholder="Çalışan Seçin"
        />
        <Select
            v-model="formData.service_id"
            label="Hizmet"
            required
            :options="serviceOptions"
            optionLabel="label"
            optionValue="id"
            placeholder="Hizmet Seçin"
            @change="onServiceChange"
        />
      </div>

      <!-- Tarih -->
      <Input
        v-model="formData.appointment_date"
        type="datetime-local"
        label="Randevu Tarihi"
        required
      />

      <!-- Süre ve Fiyat -->
      <div class="grid grid-cols-2 gap-4">
        <Input
            v-model="formData.duration_minutes"
            type="number"
            label="Süre (Dakika)"
            readonly
            class="bg-gray-50"
        />
        <Input
            v-model="formData.price"
            type="number"
            label="Fiyat (₺)"
            step="0.01"
            min="0"
            required
        />
      </div>

      <!-- Durum -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
          <Button
            v-for="status in statusOptions"
            :key="status.value"
            type="button"
            @click="formData.status = status.value"
            :variant="formData.status === status.value ? status.variant : 'outline'"
            size="sm"
            :label="status.label"
            :class="{ 'border-2': formData.status === status.value }"
          />
        </div>
      </div>

      <!-- Notlar -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
        <textarea
          v-model="formData.notes"
          rows="2"
          placeholder="Randevu hakkında notlar..."
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm"
        ></textarea>
      </div>

      <!-- Form Butonları -->
      <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <Button variant="secondary" @click="$emit('update:modelValue', false)" label="İptal" />
        <Button
            type="submit"
            variant="success"
            :loading="loading"
            :label="loading ? 'Kaydediliyor...' : 'Kaydet'"
        />
      </div>
    </form>
  </Modal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Modal from '@/components/ui/Modal.vue'

import { useCustomerStore } from '@/stores/customer'
import { useEmployeeStore } from '@/stores/employee'
import { useServiceStore } from '@/stores/service'
import { useBranchStore } from '@/stores/branch'

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

const customerStore = useCustomerStore()
const employeeStore = useEmployeeStore()
const serviceStore = useServiceStore()
const branchStore = useBranchStore()

const formData = ref({
  branch_id: '',
  customer_id: '',
  employee_id: '',
  service_id: '',
  appointment_date: '',
  duration_minutes: 0,
  price: 0,
  status: 'pending' as 'pending' | 'confirmed' | 'cancelled' | 'completed',
  notes: ''
})

const statusOptions = [
  { value: 'pending', label: 'Bekliyor', variant: 'secondary' },
  { value: 'confirmed', label: 'Onaylı', variant: 'primary' },
  { value: 'completed', label: 'Tamam', variant: 'success' },
  { value: 'cancelled', label: 'İptal', variant: 'danger' }
] as const

// Store Options Computed
const customerOptions = computed(() => customerStore.customers.map((c: any) => ({
    id: c.id,
    full_name: `${c.first_name} ${c.last_name}`
})))

const employeeOptions = computed(() => employeeStore.employees.map((e: any) => ({
    id: e.id,
    full_name: `${e.first_name} ${e.last_name}`
})))

const serviceOptions = computed(() => serviceStore.services.map((s: any) => ({
    id: s.id,
    label: `${s.name} - ${s.price}₺`
})))

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
     formData.value = {
      branch_id: '',
      customer_id: '',
      employee_id: '',
      service_id: '',
      appointment_date: '',
      duration_minutes: 0,
      price: 0,
      status: 'pending',
      notes: ''
    }
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = { ...val }
        // Date formatting if needed for input type="datetime-local"
        if(val.appointment_date && val.appointment_date.length > 16) {
             const d = new Date(val.appointment_date)
             // Format: YYYY-MM-DDThh:mm
             const pad = (n: number) => n.toString().padStart(2, '0')
             formData.value.appointment_date = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
        }
    } else {
        resetForm()
    }
}, { immediate: true })

// If opening clean
watch(() => props.modelValue, (val) => {
    if (val && !props.isEdit && !props.initialData) {
        resetForm()
    }
})

const onServiceChange = () => {
    const s = serviceStore.services.find((x: any) => x.id === formData.value.service_id)
    if (s) {
        formData.value.duration_minutes = s.duration_minutes
        formData.value.price = Number(s.price)
    }
}

const handleSubmit = () => {
    emit('submit', formData.value)
}
</script>
