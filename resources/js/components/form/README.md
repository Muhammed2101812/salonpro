# Form Validation Components

This directory contains reusable form components integrated with VeeValidate and Yup for robust form validation.

## Components

### ValidatedForm
A wrapper component that handles form submission and validation using VeeValidate.

**Props:**
- `validationSchema` (required): Yup validation schema
- `initialValues` (optional): Initial form values
- `submitText` (optional): Submit button text (default: "Kaydet")
- `showActions` (optional): Show submit/cancel buttons (default: true)

**Events:**
- `submit`: Emitted when form is successfully validated and submitted
- `cancel`: Emitted when cancel button is clicked

**Usage:**
```vue
<ValidatedForm
  :validation-schema="customerSchema"
  :initial-values="formData"
  @submit="handleSubmit"
  @cancel="closeModal"
>
  <template #default="{ errors, isSubmitting }">
    <!-- Your form fields here -->
  </template>
</ValidatedForm>
```

### TextInput
A validated text input field.

**Props:**
- `name` (required): Field name for validation
- `label` (optional): Input label
- `type` (optional): Input type (default: "text")
- `placeholder` (optional): Placeholder text
- `required` (optional): Show required indicator (default: false)
- `disabled` (optional): Disable input (default: false)
- `hint` (optional): Help text below input

**Usage:**
```vue
<TextInput
  name="first_name"
  label="Ad"
  placeholder="Örn: Ahmet"
  required
/>
```

### SelectInput
A validated select dropdown field.

**Props:**
- `name` (required): Field name for validation
- `label` (optional): Input label
- `options` (required): Array of `{value, label}` objects
- `placeholder` (optional): Placeholder text
- `required` (optional): Show required indicator (default: false)
- `disabled` (optional): Disable input (default: false)
- `hint` (optional): Help text below input

**Usage:**
```vue
<SelectInput
  name="branch_id"
  label="Şube"
  :options="branchOptions"
  placeholder="Şube seçiniz"
  required
/>
```

### TextareaInput
A validated textarea field.

**Props:**
- `name` (required): Field name for validation
- `label` (optional): Input label
- `placeholder` (optional): Placeholder text
- `required` (optional): Show required indicator (default: false)
- `disabled` (optional): Disable input (default: false)
- `hint` (optional): Help text below input
- `rows` (optional): Number of rows (default: 3)

**Usage:**
```vue
<TextareaInput
  name="notes"
  label="Notlar"
  placeholder="Müşteri hakkında notlar..."
  :rows="3"
/>
```

## Validation Schemas

Validation schemas are defined in `@/composables/useValidation.ts` using Yup.

### Available Schemas
- `customerSchema` - Customer form validation
- `employeeSchema` - Employee form validation
- `serviceSchema` - Service form validation
- `productSchema` - Product form validation
- `appointmentSchema` - Appointment form validation
- `paymentSchema` - Payment form validation
- `expenseSchema` - Expense form validation
- `userSchema` - User form validation
- `loginSchema` - Login form validation
- `branchSchema` - Branch form validation
- `invoiceSchema` - Invoice form validation
- `supplierSchema` - Supplier form validation
- `stockTransferSchema` - Stock transfer validation
- `couponSchema` - Coupon validation
- `marketingCampaignSchema` - Marketing campaign validation

### Creating Custom Schemas

```typescript
import * as yup from 'yup'

export const myCustomSchema = yup.object({
  name: yup
    .string()
    .required()
    .max(255)
    .label('İsim'),
  email: yup
    .string()
    .email()
    .required()
    .label('E-posta'),
  age: yup
    .number()
    .required()
    .positive()
    .integer()
    .min(18, 'En az 18 yaşında olmalısınız')
    .label('Yaş'),
})
```

## Complete Example

Here's a complete example of a validated form:

```vue
<template>
  <FormModal v-model="showModal" title="Müşteri Ekle">
    <ValidatedForm
      :validation-schema="customerSchema"
      :initial-values="formData"
      @submit="handleSubmit"
      @cancel="closeModal"
    >
      <template #default="{ errors, isSubmitting }">
        <div class="space-y-4">
          <!-- Text Input -->
          <TextInput
            name="first_name"
            label="Ad"
            placeholder="Örn: Ahmet"
            required
          />

          <!-- Email Input -->
          <TextInput
            name="email"
            label="E-posta"
            type="email"
            placeholder="ornek@email.com"
          />

          <!-- Select Input -->
          <SelectInput
            name="branch_id"
            label="Şube"
            :options="branchOptions"
            placeholder="Şube seçiniz"
            required
          />

          <!-- Textarea -->
          <TextareaInput
            name="notes"
            label="Notlar"
            placeholder="Notlar..."
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
import { customerSchema } from '@/composables/useValidation'
import FormModal from '@/components/FormModal.vue'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import SelectInput from '@/components/form/SelectInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'

const showModal = ref(false)
const formData = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  branch_id: '',
  notes: ''
})

const branchOptions = computed(() => [
  { value: '1', label: 'Merkez Şube' },
  { value: '2', label: 'Kadıköy Şubesi' }
])

const handleSubmit = async (values: Record<string, any>) => {
  console.log('Form submitted:', values)
  // Save to API
}

const closeModal = () => {
  showModal.value = false
}
</script>
```

## Validation Rules

### Common Validation Methods

```typescript
// String validations
yup.string()
  .required()           // Field is required
  .email()             // Must be valid email
  .url()               // Must be valid URL
  .uuid()              // Must be valid UUID
  .min(5)              // Minimum 5 characters
  .max(100)            // Maximum 100 characters
  .matches(/regex/)    // Must match regex pattern
  .label('Field Name') // Custom field name for error messages

// Number validations
yup.number()
  .required()
  .positive()          // Must be positive
  .negative()          // Must be negative
  .integer()           // Must be integer
  .min(0)              // Minimum value
  .max(100)            // Maximum value
  .label('Field Name')

// Date validations
yup.date()
  .required()
  .min(new Date())     // Must be after date
  .max(new Date())     // Must be before date
  .label('Field Name')

// Boolean validations
yup.boolean()
  .required()
  .default(true)       // Default value
  .label('Field Name')

// Array validations
yup.array()
  .required()
  .min(1)              // At least 1 item
  .max(10)             // At most 10 items
  .of(yup.string())    // Array of strings
  .label('Field Name')

// Conditional validations
yup.string()
  .when('other_field', {
    is: 'value',
    then: (schema) => schema.required(),
    otherwise: (schema) => schema.nullable()
  })

// Custom validation
yup.string()
  .test('custom', 'Custom error message', (value) => {
    // Your validation logic
    return value === 'expected'
  })

// Cross-field validation
yup.string()
  .oneOf([yup.ref('password')], 'Passwords must match')
```

## Turkish Error Messages

Error messages are automatically localized to Turkish through the validation configuration in `resources/js/plugins/validation.ts`.

You can customize messages by updating the Yup locale configuration or VeeValidate messages.

## Tips

1. **Always use `.label()`** to provide user-friendly field names
2. **Use `.nullable()`** for optional fields
3. **Group related fields** in the same schema object
4. **Reuse schemas** for similar forms (create, update)
5. **Show error summary** at the top of complex forms
6. **Disable submit button** while submitting (`isSubmitting`)

## Resources

- [VeeValidate Documentation](https://vee-validate.logaretm.com/v4/)
- [Yup Documentation](https://github.com/jquense/yup)
