# Relationship Select Components

## Overview

Relationship select components provide reusable dropdowns for selecting foreign key relationships (branches, customers, employees, products, services) with automatic data fetching, loading states, error handling, and refresh functionality.

## Architecture

### Components
- `RelationshipSelect.vue` - Base component with common functionality
- `BranchSelect.vue` - Branch selection
- `CustomerSelect.vue` - Customer selection
- `EmployeeSelect.vue` - Employee selection with active-only filter
- `ProductSelect.vue` - Product selection with in-stock filter
- `ServiceSelect.vue` - Service selection with active-only filter

### Composable
- `useRelationships.ts` - Data fetching logic for all relationship types

## Features

- **Auto-loading**: Data loads automatically on component mount
- **Loading States**: Spinner shown while fetching data
- **Error Handling**: User-friendly error messages
- **Refresh Button**: Manual refresh with icon button
- **Filtering**: Support for activeOnly, inStockOnly filters
- **VeeValidate Integration**: Works seamlessly with ValidatedForm

## Basic Usage

### BranchSelect

```vue
<template>
  <ValidatedForm :validation-schema="schema" @submit="handleSubmit">
    <BranchSelect
      name="branch_id"
      label="Şube"
      placeholder="Şube seçiniz"
      required
      hint="İşlemin yapılacağı şube"
    />
  </ValidatedForm>
</template>

<script setup lang="ts">
import BranchSelect from '@/components/form/BranchSelect.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
</script>
```

### CustomerSelect

```vue
<CustomerSelect
  name="customer_id"
  label="Müşteri"
  placeholder="Müşteri seçiniz"
  required
  hint="Randevu alacak müşteri"
/>
```

### EmployeeSelect

```vue
<!-- All employees -->
<EmployeeSelect
  name="employee_id"
  label="Çalışan"
  required
/>

<!-- Active employees only -->
<EmployeeSelect
  name="employee_id"
  label="Çalışan"
  required
  active-only
  hint="Sadece aktif çalışanlar"
/>
```

### ServiceSelect

```vue
<!-- All services -->
<ServiceSelect
  name="service_id"
  label="Hizmet"
  required
/>

<!-- Active services only -->
<ServiceSelect
  name="service_id"
  label="Hizmet"
  required
  active-only
  hint="Sadece aktif hizmetler"
/>
```

### ProductSelect

```vue
<!-- All products -->
<ProductSelect
  name="product_id"
  label="Ürün"
  required
/>

<!-- In-stock products only -->
<ProductSelect
  name="product_id"
  label="Ürün"
  required
  in-stock-only
  hint="Sadece stokta olan ürünler"
/>
```

## Props

All relationship select components accept the following props:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string` | **required** | Field name for form validation |
| `label` | `string` | Component-specific | Label text shown above select |
| `placeholder` | `string` | `''` | Placeholder text when no option selected |
| `required` | `boolean` | `false` | Whether field is required |
| `disabled` | `boolean` | `false` | Whether select is disabled |
| `hint` | `string` | `''` | Helper text shown below select |
| `showRefresh` | `boolean` | `true` | Whether to show refresh button |
| `autoLoad` | `boolean` | `true` | Whether to auto-load data on mount |

### Component-Specific Props

**EmployeeSelect & ServiceSelect:**
- `activeOnly` - `boolean` - Filter to show only active records

**ProductSelect:**
- `inStockOnly` - `boolean` - Filter to show only in-stock products

## Complete Example

See `resources/js/views/Appointments/CreateExample.vue` for a comprehensive example:

```vue
<template>
  <ValidatedForm
    :validation-schema="appointmentSchema"
    :initial-values="form"
    @submit="handleSubmit"
  >
    <template #default="{ errors, isSubmitting }">
      <div class="space-y-6">
        <!-- Customer & Branch -->
        <div class="grid grid-cols-2 gap-4">
          <CustomerSelect
            name="customer_id"
            label="Müşteri"
            placeholder="Müşteri seçiniz"
            required
            hint="Randevu alacak müşteri"
          />

          <BranchSelect
            name="branch_id"
            label="Şube"
            placeholder="Şube seçiniz"
            required
            hint="Randevu şubesi"
          />
        </div>

        <!-- Service & Employee -->
        <div class="grid grid-cols-2 gap-4">
          <ServiceSelect
            name="service_id"
            label="Hizmet"
            placeholder="Hizmet seçiniz"
            required
            active-only
            hint="Sadece aktif hizmetler"
          />

          <EmployeeSelect
            name="employee_id"
            label="Çalışan"
            placeholder="Çalışan seçiniz"
            required
            active-only
            hint="Hizmeti verecek çalışan"
          />
        </div>

        <!-- Optional Product -->
        <ProductSelect
          name="product_id"
          label="Ürün"
          placeholder="Ürün seçiniz"
          in-stock-only
          hint="Randevuda kullanılacak ürün"
        />
      </div>
    </template>
  </ValidatedForm>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import BranchSelect from '@/components/form/BranchSelect.vue'
import CustomerSelect from '@/components/form/CustomerSelect.vue'
import EmployeeSelect from '@/components/form/EmployeeSelect.vue'
import ServiceSelect from '@/components/form/ServiceSelect.vue'
import ProductSelect from '@/components/form/ProductSelect.vue'
import { appointmentSchema } from '@/composables/useValidation'

const form = ref({
  customer_id: '',
  branch_id: '',
  service_id: '',
  employee_id: '',
  product_id: ''
})

const handleSubmit = async (values: Record<string, any>) => {
  console.log('Form submitted:', values)
  // API call here
}
</script>
```

## Updating Existing Pages

### Before (Manual SelectInput)

```vue
<template>
  <FormModal>
    <ValidatedForm>
      <SelectInput
        name="branch_id"
        label="Şube"
        :options="branchOptions"
        required
      />
    </ValidatedForm>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useBranchStore } from '@/stores/branch'
import SelectInput from '@/components/form/SelectInput.vue'

const branchStore = useBranchStore()

const branchOptions = computed(() =>
  (branchStore.branches || []).map(branch => ({
    value: branch.id,
    label: branch.name
  }))
)

onMounted(() => {
  branchStore.fetchBranches()
})
</script>
```

### After (BranchSelect)

```vue
<template>
  <FormModal>
    <ValidatedForm>
      <BranchSelect
        name="branch_id"
        label="Şube"
        required
      />
    </ValidatedForm>
  </FormModal>
</template>

<script setup lang="ts">
import BranchSelect from '@/components/form/BranchSelect.vue'

// No need for store imports, computed properties, or onMounted calls!
</script>
```

**Benefits:**
- 20+ lines of code reduced to 1 component
- No manual store management
- Automatic loading states
- Built-in error handling
- Refresh functionality included

## Advanced Usage

### Disabling Auto-Load

```vue
<BranchSelect
  name="branch_id"
  :auto-load="false"
/>

<script setup>
// Manually trigger load when needed
const branchSelect = ref()
branchSelect.value.fetch()
</script>
```

### Hiding Refresh Button

```vue
<CustomerSelect
  name="customer_id"
  :show-refresh="false"
/>
```

### Manual Refresh

The refresh button automatically triggers data reload. You can also programmatically refresh:

```vue
<template>
  <BranchSelect ref="branchSelectRef" name="branch_id" />
  <button @click="refreshBranches">Refresh</button>
</template>

<script setup>
import { ref } from 'vue'

const branchSelectRef = ref()

const refreshBranches = () => {
  // RelationshipSelect emits refresh event
  branchSelectRef.value.$emit('refresh')
}
</script>
```

## Creating Custom Relationship Selectors

To create a new relationship selector for a different entity:

### 1. Add Composable Function

Edit `resources/js/composables/useRelationships.ts`:

```typescript
export function useSupplierOptions() {
  const store = useSupplierStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.suppliers || []).map(supplier => ({
      value: supplier.id,
      label: supplier.name
    }))
  )

  const fetchSuppliers = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchSuppliers()
    } catch (e: any) {
      error.value = e.message || 'Tedarikçiler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchSuppliers
  }
}
```

### 2. Create Component

Create `resources/js/components/form/SupplierSelect.vue`:

```vue
<template>
  <RelationshipSelect
    :name="name"
    :label="label || 'Tedarikçi'"
    :options="supplierOptions.options.value"
    :placeholder="placeholder"
    :required="required"
    :disabled="disabled"
    :loading="supplierOptions.loading.value"
    :error="supplierOptions.error.value"
    :hint="hint"
    :show-refresh="showRefresh"
    @refresh="supplierOptions.fetch"
  />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import RelationshipSelect from './RelationshipSelect.vue'
import { useSupplierOptions } from '@/composables/useRelationships'

interface Props {
  name: string
  label?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  hint?: string
  showRefresh?: boolean
  autoLoad?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  showRefresh: true,
  autoLoad: true,
})

const supplierOptions = useSupplierOptions()

onMounted(() => {
  if (props.autoLoad && supplierOptions.options.value.length === 0) {
    supplierOptions.fetch()
  }
})
</script>
```

### 3. Use in Forms

```vue
<SupplierSelect
  name="supplier_id"
  label="Tedarikçi"
  required
/>
```

## Testing

When testing forms with relationship selects, ensure:

1. **Data Loading**: Selects show loading spinner initially
2. **Options Populated**: Options appear after data loads
3. **Error Handling**: Error message displays if fetch fails
4. **Refresh**: Refresh button reloads data
5. **Validation**: Required validation works correctly
6. **Filtering**: activeOnly/inStockOnly filters work as expected

## Migration Checklist

When converting existing forms to use relationship selects:

- [ ] Replace manual `SelectInput` with appropriate relationship select
- [ ] Remove store imports (`useBranchStore`, etc.)
- [ ] Remove computed options mapping
- [ ] Remove `onMounted` fetch calls
- [ ] Remove manual loading state management
- [ ] Test that data loads correctly
- [ ] Verify validation still works
- [ ] Check that form submission includes correct IDs

## Troubleshooting

### Options Not Loading

**Problem**: Dropdown shows "Yükleniyor..." indefinitely

**Solutions**:
1. Check that the store's fetch method is working
2. Verify API endpoint returns data
3. Check browser console for errors
4. Ensure store is properly initialized

### Validation Not Working

**Problem**: Required validation doesn't trigger

**Solutions**:
1. Ensure `required` prop is set on component
2. Check that validation schema includes the field
3. Verify field `name` matches schema key

### Wrong Data Displayed

**Problem**: Showing inactive/out-of-stock items when filtered

**Solutions**:
1. Verify `active-only` or `in-stock-only` prop is set
2. Check that composable uses correct variant (e.g., `useActiveEmployeeOptions`)
3. Ensure backend API supports filtering

## Performance Considerations

- Data is fetched only once per component lifecycle (unless manually refreshed)
- Multiple instances of same selector type share the same store data
- Auto-load can be disabled for conditional rendering scenarios
- Consider pagination for large datasets (future enhancement)

## Future Enhancements

Potential improvements for relationship selects:

- [ ] Search/filter functionality within dropdown
- [ ] Pagination for large datasets
- [ ] Multi-select support
- [ ] Custom option templates (with avatars, icons, etc.)
- [ ] Keyboard navigation improvements
- [ ] Virtual scrolling for performance
- [ ] Grouped options (e.g., branches by city)

## Related Documentation

- [Form Validation Guide](./README.md)
- [ValidatedForm Component](./README.md#validatedform-component)
- [Creating Custom Validators](../../composables/useValidation.ts)
- [Pinia Store Setup](../../stores/)
