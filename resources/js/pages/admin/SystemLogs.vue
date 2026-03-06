<template>
  <div class="space-y-6 transition-colors duration-300">
    
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">TIZIM TARIXI (LOGLAR)</h1>
      <router-link to="/admin" class="text-sm text-cyan-600 dark:text-cyan-500 hover:text-cyan-500 dark:hover:text-cyan-400 font-bold">← Ortga qaytish</router-link>
    </div>

    <div class="p-5 rounded-2xl border shadow-xl flex flex-wrap gap-4 items-end transition-colors duration-300
      bg-white border-gray-200 
      dark:bg-[#1a1d22] dark:border-gray-800">
      
      <div class="flex flex-col gap-1">
        <label class="text-[10px] text-gray-500 font-bold uppercase">Boshlanish Sanasi</label>
        <input type="date" v-model="filters.start_date" 
          class="border text-sm rounded-lg p-2.5 outline-none w-40 transition-all
          bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500
          dark:bg-[#0f1115] dark:border-gray-700 dark:text-white dark:focus:border-cyan-500">
      </div>

      <div class="flex flex-col gap-1">
        <label class="text-[10px] text-gray-500 font-bold uppercase">Tugash Sanasi</label>
        <input type="date" v-model="filters.end_date" 
          class="border text-sm rounded-lg p-2.5 outline-none w-40 transition-all
          bg-gray-50 border-gray-300 text-gray-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500
          dark:bg-[#0f1115] dark:border-gray-700 dark:text-white dark:focus:border-cyan-500">
      </div>

      <button @click="fetchLogs(1)" class="bg-cyan-500 hover:bg-cyan-400 text-white dark:text-black font-bold py-2.5 px-6 rounded-lg transition text-sm shadow-md">
        Filtrlash
      </button>

      <button @click="resetFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white font-bold py-2.5 px-4 rounded-lg transition text-sm">
        Tozalash
      </button>
    </div>

    <div class="p-6 rounded-2xl border shadow-xl transition-colors duration-300
      bg-white border-gray-200 
      dark:bg-[#1a1d22] dark:border-gray-800">
      
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[10px] uppercase border-b 
              text-gray-500 border-gray-200 dark:border-gray-800">
              <th class="pb-3 font-black pl-2">ID</th>
              <th class="pb-3 font-black">Foydalanuvchi</th>
              <th class="pb-3 font-black">Amal</th>
              <th class="pb-3 font-black">Vaqt</th>
              <th class="pb-3 font-black text-right pr-2">Status</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-if="logs.length === 0">
              <td colspan="5" class="text-center py-8 text-gray-400 italic">Ma'lumot topilmadi</td>
            </tr>
            <tr v-for="log in logs" :key="log.id" 
              class="border-b last:border-0 transition-colors 
              border-gray-100 hover:bg-gray-50 
              dark:border-gray-800/50 dark:hover:bg-white/5">
              
              <td class="py-4 pl-2 text-cyan-600 dark:text-cyan-500/50 font-mono text-xs">#{{ log.id }}</td>
              <td class="py-4 font-medium text-gray-800 dark:text-white">{{ log.user }}</td>
              <td class="py-4 text-gray-600 dark:text-gray-400">{{ log.action }}</td>
              <td class="py-4 text-gray-500 text-xs font-mono">{{ log.time }}</td>
              
              <td class="py-4 text-right pr-2">
                <span :class="getStatusClass(log.status)" 
                  class="px-2 py-1 rounded text-[10px] font-bold uppercase">
                  {{ log.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
        <div class="text-xs text-gray-500">
          Jami: <span class="font-bold text-gray-800 dark:text-white">{{ pagination.total }}</span> ta log
        </div>
        <div class="flex gap-2">
          <button 
            :disabled="!pagination.prev_page_url" 
            @click="fetchLogs(pagination.current_page - 1)"
            class="px-3 py-1 rounded text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed
            bg-gray-200 text-gray-700 hover:bg-gray-300
            dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
          >
            Oldingi
          </button>
          
          <span class="px-3 py-1 rounded text-xs font-bold
            bg-cyan-100 text-cyan-700
            dark:bg-cyan-500/10 dark:text-cyan-500">
            {{ pagination.current_page }} / {{ pagination.last_page }}
          </span>

          <button 
            :disabled="!pagination.next_page_url" 
            @click="fetchLogs(pagination.current_page + 1)"
            class="px-3 py-1 rounded text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed
            bg-gray-200 text-gray-700 hover:bg-gray-300
            dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
          >
            Keyingi
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);
const filters = ref({
  start_date: '',
  end_date: ''
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  prev_page_url: null,
  next_page_url: null,
  total: 0
});

// Statusga qarab rang tanlash (Universal)
const getStatusClass = (status) => {
  if (status === 'Success') return 'text-green-700 bg-green-100 dark:text-green-400 dark:bg-green-400/10';
  if (status === 'Warning') return 'text-yellow-700 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-400/10';
  return 'text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-400/10';
};

const fetchLogs = async (page = 1) => {
  try {
    const response = await axios.get('/api/system-logs', {
      params: {
        page: page,
        start_date: filters.value.start_date,
        end_date: filters.value.end_date
      }
    });

    logs.value = response.data.data;
    
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      prev_page_url: response.data.prev_page_url,
      next_page_url: response.data.next_page_url,
      total: response.data.total
    };
  } catch (error) {
    console.error("Loglarni yuklashda xato:", error);
  }
};

const resetFilters = () => {
  filters.value.start_date = '';
  filters.value.end_date = '';
  fetchLogs(1);
};

onMounted(() => {
  fetchLogs();
});
</script>

<style scoped>
/* Date input icon rangini Dark mode uchun oq qilish */
.dark input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>