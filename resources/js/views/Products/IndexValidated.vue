<template>
  <div class="p-8">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürünler</h1>
        <p class="mt-2 text-gray-600">Ürün stoğunuzu yönetin</p>
      </div>
      <button @click="openCreateModal" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
        Ürün Ekle
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="productStore.loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
      <p class="mt-4 text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="productStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg flex items-center gap-2">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ productStore.error }}
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alış Fiyatı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satış Fiyatı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in productStore.products" :key="product.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ product.sku }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="product.stock_quantity <= (product.min_stock_level || 0) ? 'text-red-600 font-semibold' : 'text-gray-600'" class="text-sm">
                {{ product.stock_quantity }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatPrice(product.purchase_price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatPrice(product.sale_price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs rounded-full font-semibold">
                {{ product.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button @click="openEditModal(product)" class="text-blue-600 hover:text-blue-900 font-medium">Düzenle</button>
              <button @click="handleDelete(product.id)" class="text-red-600 hover:text-red-900 font-medium">Sil</button>
            </td>
          </tr>
          <tr v-if="productStore.products.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <p class="mt-2">Henüz ürün eklenmemiş</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal with Validated Form -->
    <FormModal v-model="showModal" :title="isEdit ? 'Ürün Düzenle' : 'Ürün Ekle'">
      <ValidatedForm
        :validation-schema="productSchema"
        :initial-values="form"
        @submit="handleSubmit"
        @cancel="closeModal"
      >
        <template #default="{ errors, isSubmitting }">
          <div class="space-y-4">
            <!-- Product Name -->
            <TextInput
              name="name"
              label="Ürün Adı"
              placeholder="Örn: Saç Boyası"
              required
            />

            <!-- SKU -->
            <TextInput
              name="sku"
              label="Stok Kodu (SKU)"
              placeholder="Örn: PROD-001"
              required
              hint="Benzersiz ürün kodu"
            />

            <!-- Description -->
            <TextareaInput
              name="description"
              label="Açıklama"
              placeholder="Ürün açıklaması..."
              :rows="3"
            />

            <!-- Price Row -->
            <div class="grid grid-cols-2 gap-4">
              <TextInput
                name="purchase_price"
                label="Alış Fiyatı"
                type="number"
                placeholder="0.00"
                required
                hint="Ürünün alış maliyeti"
              />
              <TextInput
                name="sale_price"
                label="Satış Fiyatı"
                type="number"
                placeholder="0.00"
                required
                hint="Müşteriye satış fiyatı"
              />
            </div>

            <!-- Stock Row -->
            <div class="grid grid-cols-2 gap-4">
              <TextInput
                name="stock_quantity"
                label="Stok Miktarı"
                type="number"
                placeholder="0"
                required
              />
              <TextInput
                name="min_stock_level"
                label="Minimum Stok Seviyesi"
                type="number"
                placeholder="10"
                hint="Uyarı için minimum stok"
              />
            </div>

            <!-- Category & Status Row -->
            <div class="grid grid-cols-2 gap-4">
              <SelectInput
                name="category_id"
                label="Kategori"
                :options="categoryOptions"
                placeholder="Kategori seçiniz"
              />
              <div class="flex items-center pt-7">
                <label class="flex items-center cursor-pointer">
                  <Field name="is_active" type="checkbox" v-slot="{ field }">
                    <input
                      v-bind="field"
                      type="checkbox"
                      class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    />
                  </Field>
                  <span class="ml-2 text-sm text-gray-700">Ürün Aktif</span>
                </label>
              </div>
            </div>

            <!-- Unit & Barcode -->
            <div class="grid grid-cols-2 gap-4">
              <SelectInput
                name="unit"
                label="Birim"
                :options="unitOptions"
                placeholder="Birim seçiniz"
              />
              <TextInput
                name="barcode"
                label="Barkod"
                placeholder="Barkod numarası"
              />
            </div>

            <!-- Show validation summary if there are errors -->
            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
              <p class="text-sm font-medium text-red-800 mb-2">Lütfen aşağıdaki hataları düzeltin:</p>
              <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
              </ul>
            </div>
          </div>
        </template>
      </ValidatedForm>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Field } from 'vee-validate'
import { useProductStore } from '@/stores/product'
import { productSchema } from '@/composables/useValidation'
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'

const productStore = useProductStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)

const form = ref({
  name: '',
  sku: '',
  description: '',
  purchase_price: 0,
  sale_price: 0,
  stock_quantity: 0,
  min_stock_level: 10,
  category_id: '',
  unit: 'adet',
  barcode: '',
  is_active: true
})

const categoryOptions = computed(() => [
  { value: '1', label: 'Saç Ürünleri' },
  { value: '2', label: 'Cilt Bakım' },
  { value: '3', label: 'Makyaj' },
  { value: '4', label: 'Diğer' }
])

const unitOptions = [
  { value: 'adet', label: 'Adet' },
  { value: 'paket', label: 'Paket' },
  { value: 'kutu', label: 'Kutu' },
  { value: 'şişe', label: 'Şişe' },
  { value: 'litre', label: 'Litre' },
  { value: 'gram', label: 'Gram' },
  { value: 'kilogram', label: 'Kilogram' }
]

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price)
}

const resetForm = () => {
  form.value = {
    name: '',
    sku: '',
    description: '',
    purchase_price: 0,
    sale_price: 0,
    stock_quantity: 0,
    min_stock_level: 10,
    category_id: '',
    unit: 'adet',
    barcode: '',
    is_active: true
  }
}

const openCreateModal = () => {
  resetForm()
  isEdit.value = false
  editingId.value = null
  showModal.value = true
}

const openEditModal = (product: any) => {
  form.value = {
    name: product.name || '',
    sku: product.sku || '',
    description: product.description || '',
    purchase_price: product.purchase_price || 0,
    sale_price: product.sale_price || 0,
    stock_quantity: product.stock_quantity || 0,
    min_stock_level: product.min_stock_level || 10,
    category_id: product.category_id || '',
    unit: product.unit || 'adet',
    barcode: product.barcode || '',
    is_active: product.is_active ?? true
  }
  isEdit.value = true
  editingId.value = product.id
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const handleSubmit = async (values: Record<string, any>) => {
  try {
    if (isEdit.value && editingId.value) {
      await productStore.updateProduct(editingId.value, values)
    } else {
      await productStore.createProduct(values)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save product:', error)
  }
}

const handleDelete = async (id: string) => {
  if (confirm('Bu ürünü silmek istediğinize emin misiniz?')) {
    try {
      await productStore.deleteProduct(id)
    } catch (error) {
      console.error('Failed to delete product:', error)
    }
  }
}

onMounted(() => {
  productStore.fetchProducts()
})
</script>
