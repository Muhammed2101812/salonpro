#!/usr/bin/env node

/**
 * Vue CRUD Generator
 * Generates complete CRUD pages for Vue 3 + TypeScript + Tailwind
 */

const fs = require('fs');
const path = require('path');

const resources = [
  // Core Resources
  { name: 'Appointment', plural: 'Appointments', icon: 'CalendarIcon', color: 'blue' },
  { name: 'Customer', plural: 'Customers', icon: 'UserGroupIcon', color: 'green' },
  { name: 'Employee', plural: 'Employees', icon: 'UsersIcon', color: 'purple' },
  { name: 'Service', plural: 'Services', icon: 'SparklesIcon', color: 'pink' },
  { name: 'Product', plural: 'Products', icon: 'CubeIcon', color: 'yellow' },
  { name: 'Branch', plural: 'Branches', icon: 'BuildingStorefrontIcon', color: 'indigo' },

  // Financial
  { name: 'Sale', plural: 'Sales', icon: 'ShoppingBagIcon', color: 'emerald' },
  { name: 'Payment', plural: 'Payments', icon: 'BanknotesIcon', color: 'teal' },
  { name: 'Expense', plural: 'Expenses', icon: 'ArrowTrendingDownIcon', color: 'red' },
  { name: 'Invoice', plural: 'Invoices', icon: 'DocumentTextIcon', color: 'orange' },

  // Inventory
  { name: 'StockAudit', plural: 'StockAudits', icon: 'ClipboardDocumentCheckIcon', color: 'cyan' },
  { name: 'StockTransfer', plural: 'StockTransfers', icon: 'ArrowsRightLeftIcon', color: 'slate' },
  { name: 'Supplier', plural: 'Suppliers', icon: 'TruckIcon', color: 'zinc' },
  { name: 'PurchaseOrder', plural: 'PurchaseOrders', icon: 'ShoppingCartIcon', color: 'lime' },

  // Marketing
  { name: 'MarketingCampaign', plural: 'MarketingCampaigns', icon: 'MegaphoneIcon', color: 'rose' },
  { name: 'Coupon', plural: 'Coupons', icon: 'TicketIcon', color: 'fuchsia' },
  { name: 'LoyaltyProgram', plural: 'LoyaltyPrograms', icon: 'GiftIcon', color: 'violet' },

  // Customer Management
  { name: 'CustomerCategory', plural: 'CustomerCategories', icon: 'TagIcon', color: 'green' },
  { name: 'CustomerTag', plural: 'CustomerTags', icon: 'HashtagIcon', color: 'emerald' },
  { name: 'CustomerNote', plural: 'CustomerNotes', icon: 'DocumentIcon', color: 'teal' },
  { name: 'CustomerSegment', plural: 'CustomerSegments', icon: 'UserGroupIcon', color: 'cyan' },

  // Employee Management
  { name: 'EmployeeSchedule', plural: 'EmployeeSchedules', icon: 'CalendarDaysIcon', color: 'purple' },
  { name: 'EmployeeShift', plural: 'EmployeeShifts', icon: 'ClockIcon', color: 'violet' },
  { name: 'EmployeeSkill', plural: 'EmployeeSkills', icon: 'AcademicCapIcon', color: 'indigo' },
  { name: 'EmployeeLeave', plural: 'EmployeeLeaves', icon: 'CalendarIcon', color: 'blue' },

  // Service Management
  { name: 'ServiceCategory', plural: 'ServiceCategories', icon: 'FolderIcon', color: 'pink' },
  { name: 'ServiceAddon', plural: 'ServiceAddons', icon: 'PlusCircleIcon', color: 'rose' },
  { name: 'ServicePackage', plural: 'ServicePackages', icon: 'CubeIcon', color: 'fuchsia' },

  // Product Management
  { name: 'ProductBundle', plural: 'ProductBundles', icon: 'CubeTransparentIcon', color: 'amber' },
  { name: 'ProductVariant', plural: 'ProductVariants', icon: 'SwatchIcon', color: 'yellow' },

  // System & Settings
  { name: 'NotificationTemplate', plural: 'NotificationTemplates', icon: 'BellIcon', color: 'blue' },
  { name: 'ReportTemplate', plural: 'ReportTemplates', icon: 'ChartBarIcon', color: 'purple' },
  { name: 'Webhook', plural: 'Webhooks', icon: 'LinkIcon', color: 'gray' },

  // Appointments Extended
  { name: 'AppointmentCancellation', plural: 'AppointmentCancellations', icon: 'XCircleIcon', color: 'red' },
  { name: 'AppointmentReminder', plural: 'AppointmentReminders', icon: 'BellAlertIcon', color: 'blue' },
  { name: 'AppointmentWaitlist', plural: 'AppointmentWaitlists', icon: 'QueueListIcon', color: 'yellow' },
  { name: 'AppointmentRecurrence', plural: 'AppointmentRecurrences', icon: 'ArrowPathIcon', color: 'indigo' },

  // Financial Extended
  { name: 'BankAccount', plural: 'BankAccounts', icon: 'BuildingLibraryIcon', color: 'green' },
  { name: 'BudgetPlan', plural: 'BudgetPlans', icon: 'ChartPieIcon', color: 'purple' },
  { name: 'CashRegister', plural: 'CashRegisters', icon: 'CalculatorIcon', color: 'teal' },
  { name: 'TaxRate', plural: 'TaxRates', icon: 'ReceiptPercentIcon', color: 'orange' },

  // Employee Extended
  { name: 'EmployeeAttendance', plural: 'EmployeeAttendances', icon: 'ClipboardDocumentListIcon', color: 'blue' },
  { name: 'EmployeeCertification', plural: 'EmployeeCertifications', icon: 'AcademicCapIcon', color: 'indigo' },
  { name: 'EmployeeCommission', plural: 'EmployeeCommissions', icon: 'CurrencyDollarIcon', color: 'green' },
  { name: 'EmployeePerformance', plural: 'EmployeePerformances', icon: 'ChartBarSquareIcon', color: 'purple' },

  // Customer Extended
  { name: 'CustomerFeedback', plural: 'CustomerFeedbacks', icon: 'ChatBubbleLeftRightIcon', color: 'blue' },
  { name: 'Lead', plural: 'Leads', icon: 'UserPlusIcon', color: 'cyan' },
  { name: 'Referral', plural: 'Referrals', icon: 'ShareIcon', color: 'pink' },

  // Product Extended
  { name: 'ProductAttribute', plural: 'ProductAttributes', icon: 'AdjustmentsHorizontalIcon', color: 'purple' },
  { name: 'ProductDiscount', plural: 'ProductDiscounts', icon: 'ReceiptPercentIcon', color: 'red' },
  { name: 'ProductImage', plural: 'ProductImages', icon: 'PhotoIcon', color: 'blue' },

  // Service Extended
  { name: 'ServicePricingRule', plural: 'ServicePricingRules', icon: 'CurrencyDollarIcon', color: 'green' },
  { name: 'ServiceReview', plural: 'ServiceReviews', icon: 'StarIcon', color: 'yellow' },

  // Inventory Extended
  { name: 'InventoryMovement', plural: 'InventoryMovements', icon: 'ArrowsUpDownIcon', color: 'slate' },
  { name: 'StockAlert', plural: 'StockAlerts', icon: 'ExclamationTriangleIcon', color: 'amber' },

  // Product Extended (More)
  { name: 'ProductBarcode', plural: 'ProductBarcodes', icon: 'QrCodeIcon', color: 'gray' },
  { name: 'ProductStockHistory', plural: 'ProductStockHistories', icon: 'ClockIcon', color: 'blue' },
  { name: 'ProductPriceHistory', plural: 'ProductPriceHistories', icon: 'ChartLineIcon', color: 'green' },
  { name: 'ProductSupplierPrice', plural: 'ProductSupplierPrices', icon: 'CurrencyDollarIcon', color: 'teal' },

  // Financial Extended (More)
  { name: 'InvoiceItem', plural: 'InvoiceItems', icon: 'ListBulletIcon', color: 'orange' },
  { name: 'BankTransaction', plural: 'BankTransactions', icon: 'ArrowsRightLeftIcon', color: 'green' },
  { name: 'BudgetItem', plural: 'BudgetItems', icon: 'RectangleGroupIcon', color: 'purple' },
  { name: 'CashRegisterSession', plural: 'CashRegisterSessions', icon: 'ClockIcon', color: 'cyan' },
  { name: 'Currency', plural: 'Currencies', icon: 'BanknotesIcon', color: 'emerald' },
  { name: 'ExchangeRate', plural: 'ExchangeRates', icon: 'ArrowPathIcon', color: 'blue' },

  // Marketing Extended (More)
  { name: 'CampaignStatistic', plural: 'CampaignStatistics', icon: 'ChartBarIcon', color: 'purple' },
  { name: 'CouponUsage', plural: 'CouponUsages', icon: 'ReceiptRefundIcon', color: 'pink' },
  { name: 'LoyaltyPoint', plural: 'LoyaltyPoints', icon: 'StarIcon', color: 'yellow' },
  { name: 'ReferralProgram', plural: 'ReferralPrograms', icon: 'UserGroupIcon', color: 'indigo' },

  // Service Extended (More)
  { name: 'ServiceTemplate', plural: 'ServiceTemplates', icon: 'DocumentDuplicateIcon', color: 'blue' },
  { name: 'ServiceRequirement', plural: 'ServiceRequirements', icon: 'CheckCircleIcon', color: 'green' },
  { name: 'ServicePriceHistory', plural: 'ServicePriceHistories', icon: 'ChartLineIcon', color: 'teal' },

  // Appointments Extended (More)
  { name: 'AppointmentConflict', plural: 'AppointmentConflicts', icon: 'ExclamationCircleIcon', color: 'red' },
  { name: 'AppointmentGroup', plural: 'AppointmentGroups', icon: 'UserGroupIcon', color: 'blue' },
  { name: 'AppointmentHistory', plural: 'AppointmentHistories', icon: 'ClockIcon', color: 'gray' },

  // System & Settings Extended (More)
  { name: 'NotificationQueue', plural: 'NotificationQueues', icon: 'InboxStackIcon', color: 'blue' },
  { name: 'NotificationLog', plural: 'NotificationLogs', icon: 'DocumentTextIcon', color: 'gray' },
  { name: 'ActivityLog', plural: 'ActivityLogs', icon: 'ClipboardDocumentListIcon', color: 'slate' },
  { name: 'AuditLog', plural: 'AuditLogs', icon: 'ShieldCheckIcon', color: 'indigo' },
  { name: 'SystemBackup', plural: 'SystemBackups', icon: 'CloudArrowUpIcon', color: 'green' },
  { name: 'Integration', plural: 'Integrations', icon: 'PuzzlePieceIcon', color: 'purple' },
];

function generateIndexPage(resource) {
  const { name, plural, icon, color } = resource;
  const apiPath = plural.toLowerCase().replace(/([A-Z])/g, '-$1').toLowerCase().slice(1);

  return `<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">${plural}</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your ${plural.toLowerCase()}</p>
      </div>
      <button
        @click="openCreateModal"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${color}-600 hover:bg-${color}-700"
      >
        <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
        New ${name}
      </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg shadow">
      <div class="flex gap-4">
        <div class="flex-1">
          <input
            v-model="search"
            type="text"
            placeholder="Search..."
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-${color}-500 focus:ring-${color}-500"
          />
        </div>
        <button
          @click="loadData"
          class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200"
        >
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Created
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in items" :key="item.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.id.slice(0, 8) }}...
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.name || item.title || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(item.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editItem(item)"
                class="text-${color}-600 hover:text-${color}-900 mr-4"
              >
                Edit
              </button>
              <button
                @click="deleteItem(item)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="previousPage"
            :disabled="!meta.prev_page_url"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            @click="nextPage"
            :disabled="!meta.next_page_url"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <FormModal
      v-model="showModal"
      :title="editingItem ? 'Edit ${name}' : 'Create ${name}'"
      @save="saveItem"
    >
      <!-- Add your form fields here -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="formData.name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-${color}-500 focus:ring-${color}-500"
          />
        </div>
      </div>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { PlusIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import { use${name}Store } from '@/stores/${name.toLowerCase()}'
import FormModal from '@/components/FormModal.vue'

const store = use${name}Store()
const items = ref([])
const meta = ref({})
const search = ref('')
const showModal = ref(false)
const editingItem = ref(null)
const formData = ref({})

const loadData = async () => {
  const response = await store.fetchAll({ search: search.value })
  items.value = response.data
  meta.value = response.meta
}

const openCreateModal = () => {
  editingItem.value = null
  formData.value = {}
  showModal.value = true
}

const editItem = (item: any) => {
  editingItem.value = item
  formData.value = { ...item }
  showModal.value = true
}

const saveItem = async () => {
  if (editingItem.value) {
    await store.update(editingItem.value.id, formData.value)
  } else {
    await store.create(formData.value)
  }
  showModal.value = false
  loadData()
}

const deleteItem = async (item: any) => {
  if (confirm('Are you sure?')) {
    await store.delete(item.id)
    loadData()
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

const previousPage = () => {
  // Implement pagination
}

const nextPage = () => {
  // Implement pagination
}

onMounted(() => {
  loadData()
})
</script>`;
}

function generateStore(resource) {
  const { name, plural } = resource;
  const apiPath = plural.toLowerCase().replace(/([A-Z])/g, '-$1').toLowerCase().slice(1);

  return `import { defineStore } from 'pinia'
import api from '@/services/api'

export const use${name}Store = defineStore('${name.toLowerCase()}', {
  state: () => ({
    items: [] as any[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchAll(params = {}) {
      this.loading = true
      try {
        const response = await api.get('/${apiPath}', { params })
        this.items = response.data.data
        return response.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchOne(id: string) {
      this.loading = true
      try {
        const response = await api.get(\`/${apiPath}/\${id}\`)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(data: any) {
      this.loading = true
      try {
        const response = await api.post('/${apiPath}', data)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(id: string, data: any) {
      this.loading = true
      try {
        const response = await api.put(\`/${apiPath}/\${id}\`, data)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async delete(id: string) {
      this.loading = true
      try {
        await api.delete(\`/${apiPath}/\${id}\`)
        this.items = this.items.filter(item => item.id !== id)
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})`;
}

// Generate files
console.log('🚀 Generating Vue CRUD pages...\n');

resources.forEach(resource => {
  const viewsDir = path.join(__dirname, '..', 'resources', 'js', 'views', resource.plural);
  const storesDir = path.join(__dirname, '..', 'resources', 'js', 'stores');

  // Create directories
  if (!fs.existsSync(viewsDir)) {
    fs.mkdirSync(viewsDir, { recursive: true });
  }

  if (!fs.existsSync(storesDir)) {
    fs.mkdirSync(storesDir, { recursive: true });
  }

  // Generate Index page
  const indexPath = path.join(viewsDir, 'Index.vue');
  if (!fs.existsSync(indexPath)) {
    fs.writeFileSync(indexPath, generateIndexPage(resource));
    console.log(`✅ Created ${resource.plural}/Index.vue`);
  } else {
    console.log(`⏭️  Skipped ${resource.plural}/Index.vue (already exists)`);
  }

  // Generate Store
  const storePath = path.join(storesDir, `${resource.name.toLowerCase()}.ts`);
  if (!fs.existsSync(storePath)) {
    fs.writeFileSync(storePath, generateStore(resource));
    console.log(`✅ Created store/${resource.name.toLowerCase()}.ts`);
  } else {
    console.log(`⏭️  Skipped store/${resource.name.toLowerCase()}.ts (already exists)`);
  }
});

console.log('\n✅ Generation complete!');
console.log('\n📝 Next steps:');
console.log('1. Add routes to router/index.ts');
console.log('2. Customize form fields in each Index.vue');
console.log('3. Add FormModal component if not exists');
console.log('4. Run: npm run dev');
