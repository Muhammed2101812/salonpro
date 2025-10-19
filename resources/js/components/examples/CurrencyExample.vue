<template>
  <div class="space-y-6 p-6 max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-900">Currency Formatting Examples</h2>

    <!-- Basic Currency Display -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Basic Currency Display</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-600 mb-1">TRY (Turkish Lira)</p>
          <Currency :value="1234.56" currency="TRY" class="text-2xl font-bold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">USD (US Dollar)</p>
          <Currency :value="1234.56" currency="USD" class="text-2xl font-bold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">EUR (Euro)</p>
          <Currency :value="1234.56" currency="EUR" class="text-2xl font-bold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">Without Symbol</p>
          <Currency :value="1234.56" :show-symbol="false" class="text-2xl font-bold" />
        </div>
      </div>
    </section>

    <!-- Compact Format -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Compact Format (K, M, B)</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600 mb-1">5,500 (5.5K)</p>
          <Currency :value="5500" compact class="text-xl font-semibold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">1,250,000 (1.3M)</p>
          <Currency :value="1250000" compact class="text-xl font-semibold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">3,500,000,000 (3.5B)</p>
          <Currency :value="3500000000" compact class="text-xl font-semibold" />
        </div>
      </div>
    </section>

    <!-- Change Display with Color -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Change Display (Colored)</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600 mb-1">Positive Change</p>
          <Currency :value="250.75" change colored class="text-xl font-semibold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">Negative Change</p>
          <Currency :value="-150.50" change colored class="text-xl font-semibold" />
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">No Change</p>
          <Currency :value="0" change colored class="text-xl font-semibold" />
        </div>
      </div>
    </section>

    <!-- Using Composable Directly -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Using Composable Directly</h3>
      <div class="space-y-3">
        <div>
          <p class="text-sm text-gray-600">format(1234.56)</p>
          <p class="text-lg font-mono">{{ currency.format(1234.56) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">formatCompact(1500000)</p>
          <p class="text-lg font-mono">{{ currency.formatCompact(1500000) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">formatPercentage(15.75, 2)</p>
          <p class="text-lg font-mono">{{ currency.formatPercentage(15.75, 2) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">calculateChangePercentage(100, 125)</p>
          <p class="text-lg font-mono">{{ currency.formatPercentage(currency.calculateChangePercentage(100, 125), 1) }}</p>
        </div>
      </div>
    </section>

    <!-- Real-world Example: Dashboard Stats -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Real-world Example: Dashboard Stats</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Revenue -->
        <div class="bg-blue-50 p-4 rounded-lg">
          <p class="text-sm text-blue-600 mb-1">Toplam Gelir</p>
          <Currency :value="totalRevenue" class="text-2xl font-bold text-blue-900" />
          <div class="mt-2 flex items-center">
            <Currency
              :value="revenueChange"
              change
              colored
              class="text-sm font-medium"
            />
            <span class="ml-2 text-xs text-gray-600">bu ay</span>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="bg-red-50 p-4 rounded-lg">
          <p class="text-sm text-red-600 mb-1">Toplam Gider</p>
          <Currency :value="totalExpenses" class="text-2xl font-bold text-red-900" />
          <div class="mt-2 flex items-center">
            <Currency
              :value="expensesChange"
              change
              colored
              class="text-sm font-medium"
            />
            <span class="ml-2 text-xs text-gray-600">bu ay</span>
          </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-green-50 p-4 rounded-lg">
          <p class="text-sm text-green-600 mb-1">Net Kar</p>
          <Currency :value="netProfit" class="text-2xl font-bold text-green-900" />
          <div class="mt-2 flex items-center">
            <Currency
              :value="profitChange"
              change
              colored
              class="text-sm font-medium"
            />
            <span class="ml-2 text-xs text-gray-600">bu ay</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Currency Selector -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">Current Currency: {{ currentCurrencyInfo.name }}</h3>
      <div class="flex gap-2">
        <button
          v-for="curr in currency.availableCurrencies.value"
          :key="curr.code"
          :class="[
            'px-4 py-2 rounded-md font-medium transition-colors',
            currency.currentCurrency.value === curr.code
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
          ]"
          @click="currency.setCurrency(curr.code)"
        >
          {{ curr.symbol }} {{ curr.code }}
        </button>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useCurrency } from '@/composables/useCurrency'
import Currency from '@/components/ui/Currency.vue'

const currency = useCurrency()

// Example data
const totalRevenue = 125750.50
const revenueChange = 15250.75
const totalExpenses = 78300.25
const expensesChange = -3500.00
const netProfit = computed(() => totalRevenue - totalExpenses)
const profitChange = computed(() => revenueChange - expensesChange)

const currentCurrencyInfo = computed(() => {
  return currency.getCurrencyConfig()
})
</script>
