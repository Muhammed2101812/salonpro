<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-lg bg-yellow-100">
          <CurrencyDollarIcon class="h-6 w-6 text-yellow-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Para ve Vergi</h3>
          <p class="text-sm text-gray-500">Para birimi, KDV ve fatura ayarları</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Para Birimi *</label>
          <select
            :value="settings.currency"
            @change="updateSetting('currency', ($event.target as HTMLSelectElement).value)"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option value="TRY">Türk Lirası (₺)</option>
            <option value="USD">Amerikan Doları ($)</option>
            <option value="EUR">Euro (€)</option>
            <option value="GBP">İngiliz Sterlini (£)</option>
          </select>
        </div>
        <Input
          :modelValue="settings.default_tax_rate"
          @update:modelValue="updateSetting('default_tax_rate', Number($event))"
          type="number"
          label="Varsayılan KDV Oranı (%) *"
          min="0"
          max="100"
          required
        />
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Fiyat Formatı *</label>
          <select
            :value="settings.price_format"
            @change="updateSetting('price_format', ($event.target as HTMLSelectElement).value)"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option value="before">Sembol Önce (₺100,00)</option>
            <option value="after">Sembol Sonra (100,00₺)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ondalık Basamak *</label>
          <select
            :value="settings.decimal_places"
            @change="updateSetting('decimal_places', Number(($event.target as HTMLSelectElement).value))"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option :value="0">0 (100)</option>
            <option :value="2">2 (100,00)</option>
          </select>
        </div>
      </div>

      <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors mb-4">
        <input
          type="checkbox"
          :checked="settings.enable_invoicing"
          @change="updateSetting('enable_invoicing', ($event.target as HTMLInputElement).checked)"
          class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
        />
        <div>
          <span class="font-medium text-gray-900">Fatura Sistemi</span>
          <p class="text-sm text-gray-500">Fatura/fiş oluşturma sistemini etkinleştir</p>
        </div>
      </label>

      <div v-if="settings.enable_invoicing">
        <Input
          :modelValue="settings.invoice_prefix"
          @update:modelValue="updateSetting('invoice_prefix', $event)"
          label="Fatura Numarası Öneki"
          placeholder="Örn: INV-"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import Input from '@/components/ui/Input.vue'

const props = defineProps<{
  settings: any
}>()

const emit = defineEmits<{
  'update:settings': [key: string, value: any]
}>()

const updateSetting = (key: string, value: any) => {
  emit('update:settings', key, value)
}
</script>
