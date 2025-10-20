<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">ServiceReviews</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your servicereviews</p>
      </div>
      <button
        @click="openCreateModal"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700"
      >
        <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
        New ServiceReview
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
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
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
                class="text-yellow-600 hover:text-yellow-900 mr-4"
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
      :title="editingItem ? 'Edit ServiceReview' : 'Create ServiceReview'"
      @save="saveItem"
    >
      <!-- Add your form fields here -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="formData.name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
          />
        </div>
      </div>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { PlusIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import { useServiceReviewStore } from '@/stores/servicereview'
import FormModal from '@/components/FormModal.vue'

const store = useServiceReviewStore()
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
</script>