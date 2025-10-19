<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-4">Giderler</h1>
    <button @click="showModal=true" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">Gider Ekle</button>
    <div v-if="expenseStore.loading">Yükleniyor...</div>
    <div v-else class="bg-white rounded shadow">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="exp in expenseStore.expenses" :key="exp.id">
            <td class="px-6 py-4 text-sm">{{exp.expense_date}}</td>
            <td class="px-6 py-4 text-sm">{{exp.title}}</td>
            <td class="px-6 py-4 text-sm">{{exp.category}}</td>
            <td class="px-6 py-4 text-sm">{{exp.amount}} TL</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded p-8 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">Gider Ekle</h2>
        <form @submit.prevent="handleSubmit" class="space-y-3">
          <div><input v-model="form.title" placeholder="Başlık" required class="w-full px-3 py-2 border rounded"/></div>
          <div><input v-model="form.category" placeholder="Kategori" required class="w-full px-3 py-2 border rounded"/></div>
          <div><input v-model="form.amount" type="number" step="0.01" placeholder="Tutar" required class="w-full px-3 py-2 border rounded"/></div>
          <div><input v-model="form.expense_date" type="date" required class="w-full px-3 py-2 border rounded"/></div>
          <div><textarea v-model="form.description" placeholder="Açıklama" class="w-full px-3 py-2 border rounded"></textarea></div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="showModal=false" class="px-4 py-2 border rounded">İptal</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {ref,onMounted} from 'vue';
import {useExpenseStore} from '@/stores/expense';
const expenseStore=useExpenseStore();
const showModal=ref(false);
const form=ref({title:'',category:'',amount:0,expense_date:new Date().toISOString().split('T')[0],description:''});
const handleSubmit=async()=>{await expenseStore.createExpense(form.value);showModal.value=false;form.value={title:'',category:'',amount:0,expense_date:new Date().toISOString().split('T')[0],description:''}};
onMounted(()=>expenseStore.fetchExpenses());
</script>
