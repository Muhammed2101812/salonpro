<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Ödeme Düzenle' : 'Yeni Ödeme'"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Müşteri -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
        <select
          v-model="formData.customer_id"
          required
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary h-[42px]"
        >
          <option value="">Müşteri Seçin</option>
          <option v-for="customer in customers" :key="customer.id" :value="customer.id">
            {{ customer.first_name }} {{ customer.last_name }}
          </option>
        </select>
      </div>

      <!-- Tutar -->
      <Input
        v-model="formData.amount"
        type="number"
        label="Tutar (₺)"
        step="0.01"
        min="0"
        required
        placeholder="0.00"
        class="text-lg font-bold"
      />

      <!-- Ödeme Yöntemi -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Yöntemi *</label>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="method in paymentMethods"
            :key="method.value"
            type="button"
            @click="formData.payment_method = method.value"
            :class="[
              'flex items-center gap-2 p-3 rounded-lg border transition-colors',
              formData.payment_method === method.value
                ? 'bg-primary/10 border-primary text-primary'
                : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
            ]"
          >
            <component :is="method.icon" class="h-5 w-5" />
            <span class="font-medium">{{ method.label }}</span>
          </button>
        </div>
      </div>

      <!-- Tarih -->
      <Input
        v-model="formData.payment_date"
        type="date"
        label="Ödeme Tarihi"
        required
      />

      <!-- Durum -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
        <div class="grid grid-cols-4 gap-2">
          <button
            v-for="status in statusOptions"
            :key="status.value"
            type="button"
            @click="formData.status = status.value"
            :class="[
              'px-3 py-2 rounded-lg text-xs font-medium border transition-colors',
              formData.status === status.value ? status.activeClass : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
            ]"
          >
            {{ status.label }}
          </button>
        </div>
      </div>

      <!-- Notlar -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
        <textarea
          v-model="formData.notes"
          rows="2"
          placeholder="Ödeme ile ilgili notlar..."
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
import { ref, watch, markRaw } from 'vue'
import {
    BanknotesIcon,
    CreditCardIcon,
    BuildingLibraryIcon
} from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Modal from '@/components/ui/Modal.vue'

interface Props {
  modelValue: boolean
  isEdit?: boolean
  initialData?: any
  loading?: boolean
  customers?: any[]
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null,
  customers: () => []
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  customer_id: '',
  amount: 0,
  payment_method: 'cash',
  payment_date: new Date().toISOString().split('T')[0],
  status: 'completed',
  notes: ''
})

const paymentMethods = [
  { value: 'cash', label: 'Nakit', icon: markRaw(BanknotesIcon) },
  { value: 'credit_card', label: 'Kredi Kartı', icon: markRaw(CreditCardIcon) },
  { value: 'debit_card', label: 'Banka Kartı', icon: markRaw(CreditCardIcon) },
  { value: 'bank_transfer', label: 'Havale/EFT', icon: markRaw(BuildingLibraryIcon) }
]

const statusOptions = [
  { value: 'completed', label: 'Tamamlandı', activeClass: 'bg-green-100 text-green-800 border-green-300' },
  { value: 'pending', label: 'Bekliyor', activeClass: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
  { value: 'failed', label: 'Başarısız', activeClass: 'bg-red-100 text-red-800 border-red-300' },
  { value: 'refunded', label: 'İade', activeClass: 'bg-blue-100 text-blue-800 border-blue-300' }
]

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        customer_id: '',
        amount: 0,
        payment_method: 'cash',
        payment_date: new Date().toISOString().split('T')[0],
        status: 'completed',
        notes: ''
    }
}

watch(() => props.initialData, (val) => {
    if (val) {
        formData.value = {
            ...val,
            amount: Number(val.amount) // Ensure number
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
