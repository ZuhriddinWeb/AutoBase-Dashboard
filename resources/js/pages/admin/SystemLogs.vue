<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white tracking-tight">TIZIM TARIXI (LOGLAR)</h1>
      <router-link to="/admin/dashboard" class="text-sm text-cyan-500 hover:text-cyan-400">← Ortga qaytish</router-link>
    </div>

    <div class="bg-[#1a1d22] p-5 rounded-2xl border border-gray-800 shadow-xl flex flex-wrap gap-4 items-end">
      
      <div class="flex flex-col gap-1">
        <label class="text-[10px] text-gray-500 uppercase font-bold">Boshlanish Sanasi</label>
        <input type="date" v-model="filters.start_date" class="bg-[#0f1115] border border-gray-700 text-white text-sm rounded-lg p-2.5 focus:border-cyan-500 outline-none w-40">
      </div>

      <div class="flex flex-col gap-1">
        <label class="text-[10px] text-gray-500 uppercase font-bold">Tugash Sanasi</label>
        <input type="date" v-model="filters.end_date" class="bg-[#0f1115] border border-gray-700 text-white text-sm rounded-lg p-2.5 focus:border-cyan-500 outline-none w-40">
      </div>

      <button @click="fetchLogs(1)" class="bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-2.5 px-6 rounded-lg transition text-sm">
        Filtrlash
      </button>

      <button @click="resetFilters" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm">
        Tozalash
      </button>
    </div>

    <div class="bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[10px] text-gray-500 uppercase border-b border-gray-800">
              <th class="pb-3 font-black pl-2">ID</th>
              <th class="pb-3 font-black">Foydalanuvchi</th>
              <th class="pb-3 font-black">Amal</th>
              <th class="pb-3 font-black">Vaqt</th>
              <th class="pb-3 font-black text-right pr-2">Status</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-if="logs.length === 0">
              <td colspan="5" class="text-center py-8 text-gray-500 italic">Ma'lumot topilmadi</td>
            </tr>
            <tr v-for="log in logs" :key="log.id" class="border-b border-gray-800/50 last:border-0 hover:bg-white/5 transition-colors">
              <td class="py-4 pl-2 text-cyan-500/50 font-mono text-xs">#{{ log.id }}</td>
              <td class="py-4 text-white font-medium">{{ log.user }}</td>
              <td class="py-4 text-gray-400">{{ log.action }}</td>
              <td class="py-4 text-gray-500 text-xs font-mono">{{ log.time }}</td>
              <td class="py-4 text-right pr-2">
                <span :class="log.status === 'Success' ? 'text-green-400 bg-green-400/10' : (log.status === 'Warning' ? 'text-yellow-400 bg-yellow-400/10' : 'text-red-400 bg-red-400/10')" 
                  class="px-2 py-1 rounded text-[10px] font-bold uppercase">
                  {{ log.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-800">
        <div class="text-xs text-gray-500">
          Jami: <span class="text-white font-bold">{{ pagination.total }}</span> ta log
        </div>
        <div class="flex gap-2">
          <button 
            :disabled="!pagination.prev_page_url" 
            @click="fetchLogs(pagination.current_page - 1)"
            class="px-3 py-1 bg-gray-800 text-white rounded text-xs hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Oldingi
          </button>
          
          <span class="px-3 py-1 bg-cyan-500/10 text-cyan-500 rounded text-xs font-bold">
            {{ pagination.current_page }} / {{ pagination.last_page }}
          </span>

          <button 
            :disabled="!pagination.next_page_url" 
            @click="fetchLogs(pagination.current_page + 1)"
            class="px-3 py-1 bg-gray-800 text-white rounded text-xs hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
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
    
    // Pagination ma'lumotlarini saqlash
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
/* Date input icon rangini o'zgartirish (Dark mode uchun) */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>