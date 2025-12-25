<template>
  <Modal
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :title="isEdit ? 'Ürün Düzenle' : 'Yeni Ürün'"
    size="lg"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Ürün Adı -->
      <Input
        v-model="formData.name"
        label="Ürün Adı"
        required
        placeholder="Örn: L'Oreal Şampuan 500ml"
      />

      <!-- Açıklama -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
        <textarea
          v-model="formData.description"
          rows="2"
          placeholder="Ürün açıklaması..."
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm"
        ></textarea>
      </div>

      <!-- Barkod ve SKU -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.barcode"
          label="Barkod"
          placeholder="8690000000000"
        />
        <Input
          v-model="formData.sku"
          label="Stok Kodu (SKU)"
          placeholder="SMP-001"
        />
      </div>

      <!-- Fiyatlar -->
      <div class="grid grid-cols-2 gap-4">
        <Input
          v-model="formData.price"
          type="number"
          label="Satış Fiyatı (₺)"
          step="0.01"
          min="0"
          required
        />
        <Input
          v-model="formData.cost_price"
          type="number"
          label="Maliyet Fiyatı (₺)"
          step="0.01"
          min="0"
        />
      </div>

      <!-- Kar Marjı -->
      <div v-if="formData.price && formData.cost_price" class="bg-green-50 rounded-lg p-3 text-center border border-green-100">
        <p class="text-sm text-green-700">
          Kar Marjı: <span class="font-bold">{{ calculateProfitMargin() }}%</span>
          ({{ formatCurrency(formData.price - formData.cost_price) }} kar)
        </p>
      </div>

      <!-- Stok ve Birim -->
      <div class="grid grid-cols-3 gap-4">
        <Input
          v-model="formData.stock_quantity"
          type="number"
          label="Stok Miktarı"
          min="0"
          required
        />
        <Input
          v-model="formData.min_stock_quantity"
          type="number"
          label="Min. Stok"
          min="0"
          required
        />
        <Select
          v-model="formData.unit"
          label="Birim"
          required
          :options="unitOptions"
          optionLabel="label"
          optionValue="value"
        />
      </div>

      <!-- Kategori -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <input
          v-model="formData.category"
          type="text"
          placeholder="Örn: Şampuan, Boya, Maske"
          list="categoryList"
          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm"
        />
        <datalist id="categoryList">
          <option v-for="cat in uniqueCategories" :key="cat" :value="cat" />
        </datalist>
      </div>

      <!-- Aktiflik -->
      <div class="flex items-center">
        <input
          v-model="formData.is_active"
          type="checkbox"
          id="is_active"
          class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
        />
        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif (Satışa açık)</label>
      </div>

      <!-- Buttons -->
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
import { ref, watch } from 'vue'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Modal from '@/components/ui/Modal.vue'

interface Props {
  modelValue: boolean
  isEdit?: boolean
  initialData?: any
  loading?: boolean
  uniqueCategories?: string[]
}

const props = withDefaults(defineProps<Props>(), {
  isEdit: false,
  loading: false,
  initialData: null,
  uniqueCategories: () => []
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  submit: [data: any]
}>()

const formData = ref({
  name: '',
  description: '',
  barcode: '',
  sku: '',
  price: 0,
  cost_price: 0,
  stock_quantity: 0,
  min_stock_quantity: 5,
  unit: 'adet',
  category: '',
  is_active: true
})

const unitOptions = [
  { value: 'adet', label: 'Adet' },
  { value: 'kg', label: 'Kilogram' },
  { value: 'gram', label: 'Gram' },
  { value: 'litre', label: 'Litre' },
  { value: 'ml', label: 'Mililitre' },
  { value: 'paket', label: 'Paket' },
  { value: 'kutu', label: 'Kutu' }
]

// IMPORTANT: resetForm must be defined BEFORE the watch callbacks that use it
const resetForm = () => {
    formData.value = {
        name: '',
        description: '',
        barcode: '',
        sku: '',
        price: 0,
        cost_price: 0,
        stock_quantity: 0,
        min_stock_quantity: 5,
        unit: 'adet',
        category: '',
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

const calculateProfitMargin = () => {
  if (!formData.value.price || !formData.value.cost_price) return 0
  return Math.round(((formData.value.price - formData.value.cost_price) / formData.value.price) * 100)
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

const handleSubmit = () => {
    emit('submit', formData.value)
}
</script>
