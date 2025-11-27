# Guide: Adding Validation to Existing CRUD Pages

This guide explains how to quickly add validation to existing CRUD pages.

## Quick Steps

### 1. Import Required Components & Schema

```typescript
// Add these imports at the top of <script setup>
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'
import { serviceSchema } from '@/composables/useValidation' // Change schema as needed
```

### 2. Replace Form Tag

**Before:**
```vue
<form @submit.prevent="handleSubmit" class="space-y-4">
  <!-- fields -->
  <div class="flex justify-end space-x-3">
    <button type="button" @click="closeModal">İptal</button>
    <button type="submit">Kaydet</button>
  </div>
</form>
```

**After:**
```vue
<ValidatedForm
  :validation-schema="serviceSchema"
  :initial-values="form"
  @submit="handleSubmit"
  @cancel="closeModal"
>
  <template #default="{ errors, isSubmitting }">
    <!-- fields -->
  </template>
</ValidatedForm>
```

### 3. Replace Input Fields

#### Text Input
**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
  <input v-model="form.name" type="text" required class="w-full ...">
</div>
```

**After:**
```vue
<TextInput
  name="name"
  label="Ad"
  placeholder="Hizmet adı"
  required
/>
```

#### Select Input
**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
  <select v-model="form.category_id" required class="w-full ...">
    <option value="">Seçiniz</option>
    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
      {{ cat.name }}
    </option>
  </select>
</div>
```

**After:**
```vue
<SelectInput
  name="category_id"
  label="Kategori"
  :options="categoryOptions"
  placeholder="Kategori seçiniz"
  required
/>

<!-- Add computed property: -->
<script setup>
const categoryOptions = computed(() =>
  categories.value.map(cat => ({
    value: cat.id,
    label: cat.name
  }))
)
</script>
```

#### Textarea
**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
  <textarea v-model="form.description" rows="3" class="w-full ..."></textarea>
</div>
```

**After:**
```vue
<TextareaInput
  name="description"
  label="Açıklama"
  placeholder="Açıklama giriniz..."
  :rows="3"
/>
```

### 4. Update Submit Handler

The `handleSubmit` function now receives validated values:

**Before:**
```typescript
const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await store.update(editingId.value, form.value)
    } else {
      await store.create(form.value)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save:', error)
  }
}
```

**After:**
```typescript
const handleSubmit = async (values: Record<string, any>) => {
  try {
    if (isEdit.value && editingId.value) {
      await store.update(editingId.value, values)
    } else {
      await store.create(values)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save:', error)
  }
}
```

### 5. Add Error Summary (Optional)

Add this inside the ValidatedForm template:

```vue
<div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
  <p class="text-sm font-medium text-red-800 mb-2">Lütfen aşağıdaki hataları düzeltin:</p>
  <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
    <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
  </ul>
</div>
```

## Complete Example

```vue
<template>
  <FormModal v-model="showModal" :title="isEdit ? 'Düzenle' : 'Ekle'">
    <ValidatedForm
      :validation-schema="serviceSchema"
      :initial-values="form"
      @submit="handleSubmit"
      @cancel="closeModal"
    >
      <template #default="{ errors, isSubmitting }">
        <div class="space-y-4">
          <TextInput
            name="name"
            label="Hizmet Adı"
            placeholder="Örn: Saç Kesimi"
            required
          />

          <SelectInput
            name="category_id"
            label="Kategori"
            :options="categoryOptions"
            placeholder="Kategori seçiniz"
            required
          />

          <div class="grid grid-cols-2 gap-4">
            <TextInput
              name="price"
              label="Fiyat"
              type="number"
              placeholder="0.00"
              required
            />
            <TextInput
              name="duration"
              label="Süre (dakika)"
              type="number"
              placeholder="30"
              required
            />
          </div>

          <TextareaInput
            name="description"
            label="Açıklama"
            placeholder="Hizmet açıklaması..."
            :rows="3"
          />

          <!-- Error Summary -->
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
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { serviceSchema } from '@/composables/useValidation'
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'

const form = ref({
  name: '',
  category_id: '',
  price: 0,
  duration: 30,
  description: ''
})

const categoryOptions = computed(() =>
  categories.value.map(cat => ({
    value: cat.id,
    label: cat.name
  }))
)

const handleSubmit = async (values: Record<string, any>) => {
  // Save logic
}
</script>
```

## Available Validation Schemas

Located in `@/composables/useValidation.ts`:

- `customerSchema` - First name, last name, phone, email, address
- `employeeSchema` - First name, last name, phone, email, hire_date, salary
- `serviceSchema` - Name, description, price, duration, is_active
- `productSchema` - Name, SKU, purchase_price, sale_price, stock_quantity
- `appointmentSchema` - Customer, service, employee, date, time
- `paymentSchema` - Amount, payment_method, notes
- `expenseSchema` - Category, amount, date, description, vendor
- `userSchema` - Name, email, password, branch_id, role
- `loginSchema` - Email, password, remember
- `branchSchema` - Name, code, address, city, phone
- `invoiceSchema` - Customer, invoice_number, dates, amounts
- `supplierSchema` - Name, contact_name, phone, email, address
- `stockTransferSchema` - From/to branches, product, quantity
- `couponSchema` - Code, discount_type, discount_value, dates
- `marketingCampaignSchema` - Name, campaign_type, dates, target_audience

## Schema Not Available?

Create a new schema in `useValidation.ts`:

```typescript
export const mySchema = yup.object({
  field1: yup.string().required().max(255).label('Field 1'),
  field2: yup.number().required().positive().label('Field 2'),
  field3: yup.string().email().nullable().label('Field 3'),
})
```

## Tips

1. **Remove v-model** - ValidatedForm manages form state automatically
2. **Remove required attribute** - Validation schema handles this
3. **Remove manual error display** - Components show errors automatically
4. **Use computed for options** - Convert arrays to `{value, label}` format
5. **Test validation** - Try submitting empty or invalid data

## Common Field Types

| Field Type | Component | Props |
|------------|-----------|-------|
| Text, Email, Tel, Date, Number | TextInput | `type="..."` |
| Dropdown Select | SelectInput | `:options="[...]"` |
| Multi-line Text | TextareaInput | `:rows="3"` |
| Checkbox | TextInput | `type="checkbox"` |

## Field Props

All components support:
- `name` (required) - Field name
- `label` - Display label
- `placeholder` - Placeholder text
- `required` - Show asterisk
- `disabled` - Disable input
- `hint` - Help text below field

## Questions?

See full documentation: `resources/js/components/form/README.md`
