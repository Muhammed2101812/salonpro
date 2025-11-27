<template>
  <div class="max-w-2xl mx-auto p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Randevu Oluştur</h1>
      <p class="mt-2 text-gray-600">Yeni randevu ekleyin</p>
    </div>

    <!-- Validated Form with Relationship Selects -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <ValidatedForm
        :validation-schema="appointmentSchema"
        :initial-values="form"
        @submit="handleSubmit"
        submit-text="Randevu Oluştur"
      >
        <template #default="{ errors, isSubmitting }">
          <div class="space-y-6">
            <!-- Customer & Branch Selection -->
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

            <!-- Service & Employee Selection -->
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

            <!-- Date & Time -->
            <div class="grid grid-cols-2 gap-4">
              <TextInput
                name="date"
                label="Tarih"
                type="date"
                required
                hint="Randevu tarihi"
              />

              <TextInput
                name="time"
                label="Saat"
                type="time"
                required
                hint="Randevu saati (ÖR: 14:30)"
              />
            </div>

            <!-- Duration -->
            <TextInput
              name="duration_minutes"
              label="Süre (dakika)"
              type="number"
              placeholder="60"
              hint="Tahmini süre"
            />

            <!-- Notes -->
            <TextareaInput
              name="notes"
              label="Notlar"
              placeholder="Randevu ile ilgili notlar..."
              :rows="3"
              hint="İsteğe bağlı notlar"
            />

            <!-- Product Selection (Optional - for product usage) -->
            <div class="border-t pt-4">
              <h3 class="text-sm font-medium text-gray-700 mb-3">Kullanılacak Ürünler (Opsiyonel)</h3>
              <ProductSelect
                name="product_id"
                label="Ürün"
                placeholder="Ürün seçiniz"
                in-stock-only
                hint="Randevuda kullanılacak ürün"
              />
            </div>

            <!-- Error Summary -->
            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
              <p class="text-sm font-medium text-red-800 mb-2">
                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lütfen aşağıdaki hataları düzeltin:
              </p>
              <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                <li v-for="(error, field) in errors" :key="field">{{ error }}</li>
              </ul>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4">
              <p class="text-sm text-green-800 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ successMessage }}
              </p>
            </div>
          </div>
        </template>
      </ValidatedForm>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
      <h3 class="text-sm font-medium text-blue-900 mb-2">
        💡 Relationship Select Özellikleri
      </h3>
      <ul class="text-xs text-blue-800 space-y-1">
        <li>• <strong>Otomatik Yükleme:</strong> Veriler otomatik olarak yüklenir</li>
        <li>• <strong>Yükleme Göstergesi:</strong> Veriler yüklenirken spinner gösterilir</li>
        <li>• <strong>Yenileme Butonu:</strong> İkonla verileri yeniden yükleyebilirsiniz</li>
        <li>• <strong>Hata Yönetimi:</strong> Yükleme hatası durumunda mesaj gösterilir</li>
        <li>• <strong>Filtreleme:</strong> activeOnly, inStockOnly gibi filtreler kullanabilirsiniz</li>
        <li>• <strong>Validasyon:</strong> Form validation ile entegre çalışır</li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { appointmentSchema } from '@/composables/useValidation'
import ValidatedForm from '@/components/ValidatedForm.vue'
import TextInput from '@/components/form/TextInput.vue'
import TextareaInput from '@/components/form/TextareaInput.vue'
import BranchSelect from '@/components/form/BranchSelect.vue'
import CustomerSelect from '@/components/form/CustomerSelect.vue'
import EmployeeSelect from '@/components/form/EmployeeSelect.vue'
import ServiceSelect from '@/components/form/ServiceSelect.vue'
import ProductSelect from '@/components/form/ProductSelect.vue'

const form = ref({
  customer_id: '',
  branch_id: '',
  service_id: '',
  employee_id: '',
  date: '',
  time: '',
  duration_minutes: 60,
  notes: '',
  product_id: ''
})

const successMessage = ref('')

const handleSubmit = async (values: Record<string, any>) => {
  try {
    console.log('Creating appointment with values:', values)

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))

    successMessage.value = 'Randevu başarıyla oluşturuldu!'

    // Reset form after 2 seconds
    setTimeout(() => {
      successMessage.value = ''
      form.value = {
        customer_id: '',
        branch_id: '',
        service_id: '',
        employee_id: '',
        date: '',
        time: '',
        duration_minutes: 60,
        notes: '',
        product_id: ''
      }
    }, 2000)
  } catch (error) {
    console.error('Failed to create appointment:', error)
  }
}
</script>
