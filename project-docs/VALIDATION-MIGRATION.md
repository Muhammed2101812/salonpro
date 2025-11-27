# Form Validation Migration Guide

**Status:** In Progress
**Created:** 2025-11-27
**Updated:** 2025-11-27

## Overview

This document tracks the migration of CRUD pages from basic HTML forms to validated forms using VeeValidate + Yup.

---

## Migration Status

### Completed Examples (3 pages)
✅ **Customers/IndexValidated.vue** - Complete with all form features
✅ **Products/IndexValidated.vue** - Complete with stock management
✅ **Form Components** - 4 reusable components created

### Remaining Pages (111 pages)

Pages can be migrated using the patterns established in the example files.

---

## Available Validation Schemas

Located in `resources/js/composables/useValidation.ts`:

| Schema | Fields Validated | Use Case |
|--------|------------------|----------|
| `customerSchema` | first_name, last_name, phone, email, address, city, notes | Customer management |
| `employeeSchema` | first_name, last_name, phone, email, hire_date, salary | Employee management |
| `serviceSchema` | name, description, price, duration, is_active | Service management |
| `productSchema` | name, sku, purchase_price, sale_price, stock_quantity, min_stock_level | Product management |
| `appointmentSchema` | customer_id, service_id, employee_id, date, time, notes | Appointment booking |
| `paymentSchema` | amount, payment_method, notes | Payment processing |
| `expenseSchema` | category_id, amount, date, description, vendor, receipt_number | Expense tracking |
| `userSchema` | name, email, password, password_confirmation, branch_id, role | User management |
| `loginSchema` | email, password, remember | Authentication |
| `branchSchema` | name, code, address, city, phone, email | Branch management |
| `invoiceSchema` | customer_id, invoice_number, invoice_date, due_date, amounts | Invoice creation |
| `supplierSchema` | name, contact_name, phone, email, address, tax_number | Supplier management |
| `stockTransferSchema` | from_branch_id, to_branch_id, product_id, quantity, notes | Stock transfers |
| `couponSchema` | code, discount_type, discount_value, start_date, end_date, usage_limit | Coupon management |
| `marketingCampaignSchema` | name, description, campaign_type, start_date, end_date, target_audience | Campaign management |

---

## Required Components

All validated pages need these imports:

```typescript
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'
import { Field } from 'vee-validate' // For checkboxes/radios
import { [schemaName] } from '@/composables/useValidation'
```

---

## Migration Checklist

For each CRUD page:

### 1. Preparation
- [ ] Identify which validation schema to use
- [ ] Check if schema exists (create if needed)
- [ ] Review current form fields

### 2. Import Components
- [ ] Import ValidatedForm, TextInput, SelectInput, TextareaInput
- [ ] Import appropriate validation schema
- [ ] Import Field for custom inputs (checkbox, radio)

### 3. Update Template
- [ ] Replace `<form>` with `<ValidatedForm>`
- [ ] Add `:validation-schema="schema"` prop
- [ ] Add `:initial-values="form"` prop
- [ ] Add `@submit="handleSubmit"` event
- [ ] Add `@cancel="closeModal"` event
- [ ] Wrap content in `<template #default="{ errors, isSubmitting }">`

### 4. Replace Form Fields
- [ ] Replace text inputs with `<TextInput>`
- [ ] Replace selects with `<SelectInput>`
- [ ] Replace textareas with `<TextareaInput>`
- [ ] Convert checkbox/radio with `<Field>` component
- [ ] Remove all `v-model` directives
- [ ] Remove `required` HTML attributes

### 5. Update JavaScript
- [ ] Update `handleSubmit` to accept `values` parameter
- [ ] Update submit logic to use `values` instead of `form.value`
- [ ] Create `computed` properties for select options
- [ ] Format options as `{ value, label }` objects

### 6. Add Features
- [ ] Add error summary display (optional)
- [ ] Add field hints where helpful
- [ ] Add loading states (automatic)
- [ ] Test all validation rules

### 7. Testing
- [ ] Test required field validation
- [ ] Test format validation (email, phone)
- [ ] Test min/max validation
- [ ] Test cross-field validation
- [ ] Test form submission
- [ ] Test edit mode (initial values)
- [ ] Test cancel action

---

## Migration Patterns

### Pattern 1: Simple Text Field

**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    İsim *
  </label>
  <input
    v-model="form.name"
    type="text"
    required
    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
  />
</div>
```

**After:**
```vue
<TextInput
  name="name"
  label="İsim"
  placeholder="İsim giriniz"
  required
/>
```

### Pattern 2: Select Dropdown

**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Kategori *
  </label>
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

<!-- In script: -->
<script setup>
const categoryOptions = computed(() =>
  categories.value.map(cat => ({
    value: cat.id,
    label: cat.name
  }))
)
</script>
```

### Pattern 3: Textarea

**Before:**
```vue
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Açıklama
  </label>
  <textarea
    v-model="form.description"
    rows="3"
    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
  ></textarea>
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

### Pattern 4: Checkbox

**Before:**
```vue
<div class="flex items-center">
  <input
    v-model="form.is_active"
    type="checkbox"
    class="h-4 w-4 text-blue-600"
  />
  <label class="ml-2 text-sm text-gray-700">Aktif</label>
</div>
```

**After:**
```vue
<div class="flex items-center">
  <Field name="is_active" type="checkbox" v-slot="{ field }">
    <input
      v-bind="field"
      type="checkbox"
      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
    />
  </Field>
  <label class="ml-2 text-sm text-gray-700">Aktif</label>
</div>
```

### Pattern 5: Form Submit Handler

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

---

## Complete Example Template

```vue
<template>
  <div class="p-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Page Title</h1>
        <p class="mt-2 text-gray-600">Page description</p>
      </div>
      <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
        Add New
      </button>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Table content -->
    </div>

    <!-- Validated Form Modal -->
    <FormModal v-model="showModal" :title="isEdit ? 'Edit' : 'Create'">
      <ValidatedForm
        :validation-schema="yourSchema"
        :initial-values="form"
        @submit="handleSubmit"
        @cancel="closeModal"
      >
        <template #default="{ errors, isSubmitting }">
          <div class="space-y-4">
            <!-- Your form fields here -->
            <TextInput name="name" label="Name" required />
            <SelectInput name="category_id" label="Category" :options="categoryOptions" />
            <TextareaInput name="description" label="Description" :rows="3" />

            <!-- Error Summary -->
            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
              <p class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</p>
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
import { yourSchema } from '@/composables/useValidation'
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'

const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)

const form = ref({
  name: '',
  category_id: '',
  description: ''
})

const handleSubmit = async (values: Record<string, any>) => {
  // Save logic
}

onMounted(() => {
  // Load data
})
</script>
```

---

## Common Issues & Solutions

### Issue 1: Options Not in Correct Format
**Problem:** SelectInput requires `{ value, label }` format
**Solution:** Use computed property to transform:
```typescript
const options = computed(() =>
  items.value.map(item => ({
    value: item.id,
    label: item.name
  }))
)
```

### Issue 2: Checkbox Not Working
**Problem:** Checkbox value not updating
**Solution:** Use Field component:
```vue
<Field name="is_active" type="checkbox" v-slot="{ field }">
  <input v-bind="field" type="checkbox" />
</Field>
```

### Issue 3: Initial Values Not Loading in Edit Mode
**Problem:** Form fields empty when editing
**Solution:** Ensure `:initial-values="form"` is set and form.value is populated before opening modal

### Issue 4: Validation Not Triggering
**Problem:** Form submits without validation
**Solution:** Ensure `:validation-schema="schema"` prop is set correctly

### Issue 5: Custom Validation Needed
**Problem:** Schema doesn't exist for your use case
**Solution:** Create new schema in `useValidation.ts`:
```typescript
export const mySchema = yup.object({
  field: yup.string().required().label('Field')
})
```

---

## Next Steps

1. **Prioritize Pages:** Start with most-used CRUD pages
2. **Batch Migration:** Migrate similar pages together (all product-related, all customer-related, etc.)
3. **Test Thoroughly:** Test each migrated page
4. **Create Missing Schemas:** Add schemas for resources that don't have one yet
5. **Gradual Rollout:** Migrate and deploy incrementally

---

## Resources

- **Form Components Documentation:** `resources/js/components/form/README.md`
- **Quick Migration Guide:** `scripts/add-validation-guide.md`
- **Example Pages:**
  - `resources/js/views/Customers/IndexValidated.vue`
  - `resources/js/views/Products/IndexValidated.vue`
- **Validation Schemas:** `resources/js/composables/useValidation.ts`
- **VeeValidate Docs:** https://vee-validate.logaretm.com/v4/
- **Yup Docs:** https://github.com/jquense/yup

---

## Notes

- All form state is managed automatically by VeeValidate
- No need for `v-model` on validated fields
- Error messages are automatically displayed below fields
- Submit button is automatically disabled during submission
- Loading spinner is shown automatically during submission

---

**Last Updated:** 2025-11-27
**Maintained By:** Development Team
