<template>
  <div class="space-y-4">
    <!-- Export Buttons -->
    <div v-if="exportable" class="flex items-center justify-between">
      <slot name="toolbar"></slot>
      <div class="flex gap-2">
        <button
          @click="handleExcelExport"
          class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
        >
          <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Excel
        </button>
        <button
          @click="handlePdfExport"
          class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
        >
          <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
          PDF
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
      <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="col.headerClass"
            >
              {{ col.label }}
            </th>
            <th v-if="$slots.actions" scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          <tr v-for="(row, rowIndex) in data" :key="row.id || rowIndex" class="hover:bg-gray-50 transition-colors">
            <td
              v-for="col in columns"
              :key="col.key"
              class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
              :class="col.cellClass"
            >
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ row[col.key] }}
              </slot>
            </td>
            <td v-if="$slots.actions" class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
              <slot name="actions" :row="row"></slot>
            </td>
          </tr>
          <tr v-if="data.length === 0">
            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-12 text-center text-gray-500">
              <slot name="empty">
                Veri bulunamadı.
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { exportToExcel, exportToPDF, type ExportColumn } from '@/utils/export'

export interface Column {
  key: string
  label: string
  headerClass?: string
  cellClass?: string
}

const props = defineProps<{
  columns: Column[]
  data: any[]
  exportable?: boolean
  exportFilename?: string
  exportTitle?: string
}>()

const handleExcelExport = () => {
  const exportColumns: ExportColumn[] = props.columns.map(col => ({
    key: col.key,
    label: col.label,
    width: 20
  }))

  exportToExcel({
    filename: props.exportFilename || 'export',
    columns: exportColumns,
    data: props.data
  })
}

const handlePdfExport = () => {
  const exportColumns: ExportColumn[] = props.columns.map(col => ({
    key: col.key,
    label: col.label
  }))

  exportToPDF({
    filename: props.exportFilename || 'export',
    title: props.exportTitle || 'Rapor',
    columns: exportColumns,
    data: props.data
  })
}
</script>

