<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Komisyon Yönetimi</h1>
        <p class="mt-2 text-sm text-gray-600">Çalışanların hizmet ve ürün komisyonlarını takip edin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="openSettingsModal"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <Cog6ToothIcon class="h-5 w-5 mr-2 text-gray-500" />
          Komisyon Oranları
        </button>
        <button
          @click="exportCommissions"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Komisyon Ekle
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100">
            <CurrencyDollarIcon class="h-6 w-6 text-emerald-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bu Ay Toplam</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.monthTotal) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <ScissorsIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Hizmet Komisyonu</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.serviceCommission) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <ShoppingBagIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ürün Komisyonu</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.productCommission) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bekleyen Ödeme</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.pending) }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ödenen</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.paid) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Çalışan Komisyon Özeti -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Çalışan Bazlı Komisyon Özeti ({{ currentMonthName }})</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div
          v-for="summary in employeeSummaries"
          :key="summary.employee_id"
          class="p-4 rounded-lg border border-gray-200 hover:border-emerald-300 transition-colors"
        >
          <div class="flex items-center gap-3 mb-3">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-bold">
              {{ getInitials(summary.first_name, summary.last_name) }}
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900">
                {{ summary.first_name }} {{ summary.last_name }}
              </p>
              <p class="text-xs text-gray-500">{{ summary.position || 'Çalışan' }}</p>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Hizmet:</span>
              <span class="font-medium text-gray-900">{{ formatCurrency(summary.serviceTotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Ürün:</span>
              <span class="font-medium text-gray-900">{{ formatCurrency(summary.productTotal) }}</span>
            </div>
            <div class="pt-2 border-t border-gray-200">
              <div class="flex justify-between">
                <span class="text-sm font-medium text-emerald-600">Toplam:</span>
                <span class="text-lg font-bold text-emerald-700">{{ formatCurrency(summary.total) }}</span>
              </div>
            </div>
          </div>
          <div class="mt-3 flex gap-2">
            <span
              :class="[
                'px-2 py-0.5 text-xs font-medium rounded-full',
                summary.pendingAmount > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'
              ]"
            >
              {{ summary.pendingAmount > 0 ? `${formatCurrency(summary.pendingAmount)} bekliyor` : 'Ödendi' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <!-- Dönem Seçici -->
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
            <button
              @click="previousMonth"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-600" />
            </button>
            <span class="text-sm font-medium text-gray-700 min-w-[120px] text-center">
              {{ currentMonthName }}
            </span>
            <button
              @click="nextMonth"
              class="p-1 hover:bg-gray-200 rounded transition-colors"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-600" />
            </button>
          </div>

          <!-- Çalışan Filtresi -->
          <select
            v-model="filters.employeeId"
            class="rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
          >
            <option value="">Tüm Çalışanlar</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.first_name }} {{ emp.last_name }}
            </option>
          </select>

          <!-- Tür Filtresi -->
          <select
            v-model="filters.type"
            class="rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
          >
            <option value="">Tüm Türler</option>
            <option value="service">Hizmet</option>
            <option value="product">Ürün</option>
            <option value="bonus">Bonus</option>
          </select>

          <!-- Durum Filtresi -->
          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="status in statusFilters"
              :key="status.value"
              @click="filters.status = status.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.status === status.value
                  ? 'bg-emerald-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ status.label }}
            </button>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="markAllAsPaid"
            class="px-3 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
          >
            Tümünü Öde
          </button>
          <button
            @click="loadData"
            class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <ArrowPathIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Komisyon Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Yükleniyor -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Çalışan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tarih
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tür
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Açıklama
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Satış Tutarı
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Oran
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Komisyon
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Durum
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="commission in filteredCommissions" :key="commission.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center text-white font-medium">
                  {{ getInitials(commission.employee?.first_name || 'U', commission.employee?.last_name || 'N') }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ commission.employee?.first_name }} {{ commission.employee?.last_name }}
                  </p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(commission.date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getTypeBadgeColor(commission.commission_type)
                ]"
              >
                {{ getTypeLabel(commission.commission_type) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 max-w-[200px] truncate">
              {{ commission.description || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatCurrency(commission.sale_amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              %{{ commission.commission_rate }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm font-bold text-emerald-600">
                {{ formatCurrency(commission.commission_amount) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getStatusBadgeColor(commission.payment_status)
                ]"
              >
                {{ getStatusLabel(commission.payment_status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="commission.payment_status === 'pending'"
                  @click="markAsPaid(commission)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Ödendi İşaretle"
                >
                  <CheckIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editCommission(commission)"
                  class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="deleteCommission(commission)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Sil"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Boş Durum -->
      <div v-if="filteredCommissions.length === 0 && !loading" class="p-12 text-center">
        <CurrencyDollarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Bu dönemde komisyon kaydı bulunamadı</p>
        <button
          @click="openCreateModal"
          class="mt-4 text-emerald-600 hover:text-emerald-700 font-medium"
        >
          Komisyon ekleyin
        </button>
      </div>

      <!-- Pagination & Toplam -->
      <div v-if="filteredCommissions.length > 0" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            Toplam {{ filteredCommissions.length }} kayıt
          </p>
          <p class="text-sm font-medium text-emerald-600">
            Toplam Komisyon: {{ formatCurrency(totalCommission) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Komisyon Ekleme/Düzenleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingCommission ? 'Komisyon Düzenle' : 'Yeni Komisyon' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="saveCommission" class="p-6 space-y-5">
          <!-- Çalışan Seçimi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select
              v-model="formData.employee_id"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
              <option value="">Çalışan Seçin</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}
              </option>
            </select>
          </div>

          <!-- Tarih -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
            <input
              v-model="formData.date"
              type="date"
              required
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            />
          </div>

          <!-- Komisyon Türü -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Komisyon Türü *</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="type in commissionTypes"
                :key="type.value"
                type="button"
                @click="formData.commission_type = type.value"
                :class="[
                  'p-3 rounded-lg border text-center transition-colors',
                  formData.commission_type === type.value
                    ? type.activeClass
                    : 'border-gray-200 hover:border-emerald-300'
                ]"
              >
                <component :is="type.icon" class="h-5 w-5 mx-auto mb-1" />
                <span class="text-sm font-medium">{{ type.label }}</span>
              </button>
            </div>
          </div>

          <!-- Açıklama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <input
              v-model="formData.description"
              type="text"
              placeholder="Hizmet veya ürün adı..."
              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            />
          </div>

          <!-- Satış Tutarı ve Oran -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Satış Tutarı (₺) *</label>
              <input
                v-model.number="formData.sale_amount"
                type="number"
                min="0"
                step="0.01"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Komisyon Oranı (%) *</label>
              <input
                v-model.number="formData.commission_rate"
                type="number"
                min="0"
                max="100"
                step="0.5"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
              />
            </div>
          </div>

          <!-- Ödeme Durumu -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Durumu</label>
            <div class="flex gap-2">
              <button
                type="button"
                @click="formData.payment_status = 'pending'"
                :class="[
                  'flex-1 px-3 py-2 rounded-lg text-sm font-medium border transition-colors',
                  formData.payment_status === 'pending'
                    ? 'bg-yellow-100 text-yellow-800 border-yellow-300'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                Bekliyor
              </button>
              <button
                type="button"
                @click="formData.payment_status = 'paid'"
                :class="[
                  'flex-1 px-3 py-2 rounded-lg text-sm font-medium border transition-colors',
                  formData.payment_status === 'paid'
                    ? 'bg-green-100 text-green-800 border-green-300'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                Ödendi
              </button>
            </div>
          </div>

          <!-- Hesaplanan Komisyon -->
          <div class="bg-emerald-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-emerald-900">Hesaplanan Komisyon</span>
              <span class="text-2xl font-bold text-emerald-700">
                {{ formatCurrency(calculateCommission()) }}
              </span>
            </div>
          </div>

          <!-- Form Butonları -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Komisyon Oranları Ayarları Modal -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Varsayılan Komisyon Oranları</h2>
          <button @click="showSettingsModal = false" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Komisyonu (%)</label>
              <input
                v-model.number="defaultRates.service"
                type="number"
                min="0"
                max="100"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Komisyonu (%)</label>
              <input
                v-model.number="defaultRates.product"
                type="number"
                min="0"
                max="100"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
              />
            </div>
          </div>

          <div class="pt-4 flex justify-end gap-3">
            <button
              @click="showSettingsModal = false"
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors"
            >
              Kapat
            </button>
            <button
              @click="saveSettings"
              class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors"
            >
              Kaydet
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  CurrencyDollarIcon,
  ClockIcon,
  CheckCircleIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  CheckIcon,
  Cog6ToothIcon,
  ShoppingBagIcon
} from '@heroicons/vue/24/outline'
import { useEmployeeCommissionStore } from '@/stores/employeecommission'

// Scissors icon placeholder
const ScissorsIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.848 8.25l1.536.887M7.848 8.25a3 3 0 11-5.196-3 3 3 0 015.196 3zm1.536.887a2.165 2.165 0 011.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 11-5.196 3 3 3 0 015.196-3zm1.536-.887a2.165 2.165 0 001.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863l2.077-1.199m0-3.328a4.323 4.323 0 012.068-1.379l5.325-1.628a4.5 4.5 0 012.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.331 4.331 0 0010.607 12m3.736 0l7.794 4.5-.802.215a4.5 4.5 0 01-2.48-.043l-5.326-1.629a4.324 4.324 0 01-2.068-1.379M14.343 12l-2.882 1.664" /></svg>`
}

interface Commission {
  id: string
  employee_id: string
  employee?: {
    id: string
    first_name: string
    last_name: string
    position?: string
  }
  date: string
  commission_type: 'service' | 'product' | 'bonus'
  description?: string
  sale_amount: number
  commission_rate: number
  commission_amount: number
  payment_status: 'pending' | 'paid'
}

interface Employee {
  id: string
  first_name: string
  last_name: string
  position?: string
}

const store = useEmployeeCommissionStore()

// State
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const showSettingsModal = ref(false)
const editingCommission = ref<Commission | null>(null)
const currentMonth = ref(new Date())

const commissions = ref<Commission[]>([])
const employees = ref<Employee[]>([])

const filters = ref({
  employeeId: '',
  type: '',
  status: ''
})

const stats = ref({
  monthTotal: 0,
  serviceCommission: 0,
  productCommission: 0,
  pending: 0,
  paid: 0
})

const defaultRates = ref({
  service: 30,
  product: 10
})

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'pending', label: 'Bekleyen' },
  { value: 'paid', label: 'Ödenen' }
]

const commissionTypes = [
  { value: 'service', label: 'Hizmet', icon: ScissorsIcon, activeClass: 'border-blue-500 bg-blue-50 text-blue-700' },
  { value: 'product', label: 'Ürün', icon: ShoppingBagIcon, activeClass: 'border-purple-500 bg-purple-50 text-purple-700' },
  { value: 'bonus', label: 'Bonus', icon: CurrencyDollarIcon, activeClass: 'border-emerald-500 bg-emerald-50 text-emerald-700' }
]

const formData = ref({
  employee_id: '',
  date: new Date().toISOString().split('T')[0],
  commission_type: 'service' as const,
  description: '',
  sale_amount: 0,
  commission_rate: 30,
  payment_status: 'pending' as const
})

// Computed
const currentMonthName = computed(() => {
  return currentMonth.value.toLocaleDateString('tr-TR', { month: 'long', year: 'numeric' })
})

const filteredCommissions = computed(() => {
  let result = commissions.value

  // Ay filtresi
  const monthStart = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 1)
  const monthEnd = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 0)
  
  result = result.filter(c => {
    const date = new Date(c.date)
    return date >= monthStart && date <= monthEnd
  })

  if (filters.value.employeeId) {
    result = result.filter(c => c.employee_id === filters.value.employeeId)
  }

  if (filters.value.type) {
    result = result.filter(c => c.commission_type === filters.value.type)
  }

  if (filters.value.status) {
    result = result.filter(c => c.payment_status === filters.value.status)
  }

  return result.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
})

const totalCommission = computed(() => {
  return filteredCommissions.value.reduce((sum, c) => sum + c.commission_amount, 0)
})

const employeeSummaries = computed(() => {
  const summaries: Record<string, any> = {}
  
  filteredCommissions.value.forEach(c => {
    if (!summaries[c.employee_id]) {
      summaries[c.employee_id] = {
        employee_id: c.employee_id,
        first_name: c.employee?.first_name || '',
        last_name: c.employee?.last_name || '',
        position: c.employee?.position,
        serviceTotal: 0,
        productTotal: 0,
        total: 0,
        pendingAmount: 0
      }
    }
    
    if (c.commission_type === 'service') {
      summaries[c.employee_id].serviceTotal += c.commission_amount
    } else if (c.commission_type === 'product') {
      summaries[c.employee_id].productTotal += c.commission_amount
    }
    
    summaries[c.employee_id].total += c.commission_amount
    
    if (c.payment_status === 'pending') {
      summaries[c.employee_id].pendingAmount += c.commission_amount
    }
  })
  
  return Object.values(summaries)
})

// Helpers
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' })
}

const getInitials = (firstName: string, lastName: string) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const getTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    service: 'Hizmet',
    product: 'Ürün',
    bonus: 'Bonus'
  }
  return labels[type] || type
}

const getTypeBadgeColor = (type: string) => {
  const colors: Record<string, string> = {
    service: 'bg-blue-100 text-blue-800',
    product: 'bg-purple-100 text-purple-800',
    bonus: 'bg-emerald-100 text-emerald-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string) => {
  return status === 'paid' ? 'Ödendi' : 'Bekliyor'
}

const getStatusBadgeColor = (status: string) => {
  return status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
}

const calculateCommission = () => {
  return (formData.value.sale_amount * formData.value.commission_rate) / 100
}

// Navigation
const previousMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1)
}

// Modal Functions
const openCreateModal = () => {
  editingCommission.value = null
  formData.value = {
    employee_id: '',
    date: new Date().toISOString().split('T')[0],
    commission_type: 'service',
    description: '',
    sale_amount: 0,
    commission_rate: defaultRates.value.service,
    payment_status: 'pending'
  }
  showModal.value = true
}

const openSettingsModal = () => {
  showSettingsModal.value = true
}

const editCommission = (commission: Commission) => {
  editingCommission.value = commission
  formData.value = {
    employee_id: commission.employee_id,
    date: commission.date,
    commission_type: commission.commission_type,
    description: commission.description || '',
    sale_amount: commission.sale_amount,
    commission_rate: commission.commission_rate,
    payment_status: commission.payment_status
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingCommission.value = null
}

// CRUD Operations
const loadData = async () => {
  loading.value = true
  try {
    // Çalışanları yükle
    const empResponse = await fetch('/api/employees')
    if (empResponse.ok) {
      const data = await empResponse.json()
      employees.value = data.data || []
    }

    // Komisyonları yükle
    const response = await store.fetchAll()
    commissions.value = response?.data || []

    updateStats()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  } finally {
    loading.value = false
  }
}

const updateStats = () => {
  const monthCommissions = filteredCommissions.value
  
  stats.value.monthTotal = monthCommissions.reduce((sum, c) => sum + c.commission_amount, 0)
  stats.value.serviceCommission = monthCommissions
    .filter(c => c.commission_type === 'service')
    .reduce((sum, c) => sum + c.commission_amount, 0)
  stats.value.productCommission = monthCommissions
    .filter(c => c.commission_type === 'product')
    .reduce((sum, c) => sum + c.commission_amount, 0)
  stats.value.pending = monthCommissions
    .filter(c => c.payment_status === 'pending')
    .reduce((sum, c) => sum + c.commission_amount, 0)
  stats.value.paid = monthCommissions
    .filter(c => c.payment_status === 'paid')
    .reduce((sum, c) => sum + c.commission_amount, 0)
}

const saveCommission = async () => {
  saving.value = true
  try {
    const payload = {
      ...formData.value,
      commission_amount: calculateCommission()
    }

    if (editingCommission.value) {
      await store.update(editingCommission.value.id, payload)
    } else {
      await store.create(payload)
    }
    closeModal()
    await loadData()
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Komisyon kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const markAsPaid = async (commission: Commission) => {
  try {
    await store.update(commission.id, { ...commission, payment_status: 'paid' })
    await loadData()
  } catch (error) {
    console.error('Güncelleme hatası:', error)
  }
}

const markAllAsPaid = async () => {
  if (!confirm('Tüm bekleyen komisyonları ödendi olarak işaretlemek istiyor musunuz?')) return
  
  try {
    const pending = filteredCommissions.value.filter(c => c.payment_status === 'pending')
    for (const commission of pending) {
      await store.update(commission.id, { ...commission, payment_status: 'paid' })
    }
    await loadData()
  } catch (error) {
    console.error('Toplu güncelleme hatası:', error)
  }
}

const deleteCommission = async (commission: Commission) => {
  if (!confirm('Bu komisyonu silmek istediğinizden emin misiniz?')) return

  try {
    await store.delete(commission.id)
    await loadData()
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('Komisyon silinemedi')
  }
}

const saveSettings = () => {
  localStorage.setItem('commissionRates', JSON.stringify(defaultRates.value))
  showSettingsModal.value = false
}

const exportCommissions = () => {
  const csvContent = [
    ['Çalışan', 'Tarih', 'Tür', 'Açıklama', 'Satış Tutarı', 'Oran', 'Komisyon', 'Durum'].join(','),
    ...filteredCommissions.value.map(c => [
      `${c.employee?.first_name} ${c.employee?.last_name}`,
      c.date,
      getTypeLabel(c.commission_type),
      c.description || '',
      c.sale_amount,
      c.commission_rate,
      c.commission_amount,
      getStatusLabel(c.payment_status)
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `komisyonlar_${currentMonthName.value}.csv`
  link.click()
}

onMounted(() => {
  // Kayıtlı oranları yükle
  const savedRates = localStorage.getItem('commissionRates')
  if (savedRates) {
    defaultRates.value = JSON.parse(savedRates)
  }
  loadData()
})
</script>