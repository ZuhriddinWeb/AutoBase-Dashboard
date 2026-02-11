<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-white tracking-tight">TIZIM TASVIRLANISHI</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

      <div
        class="bg-[#1a1d22] p-5 rounded-2xl border border-gray-800 shadow-xl group hover:border-cyan-500/50 transition-all">
        <div class="flex justify-between items-start">
          <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">JAMI TEXNIKALAR</span>
          <span class="text-cyan-500 text-xs font-bold">🚜</span>
        </div>
        <div class="text-2xl font-black mt-2 text-white">{{ stats.machines_count }}</div>
        <div class="w-full bg-gray-800 h-1 mt-4 rounded-full overflow-hidden">
          <div class="bg-cyan-500 h-full w-full shadow-[0_0_10px_#06b6d4]"></div>
        </div>
      </div>

      <div
        class="bg-[#1a1d22] p-5 rounded-2xl border border-gray-800 shadow-xl group hover:border-purple-500/50 transition-all">
        <div class="flex justify-between items-start">
          <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">TRANS. GURUHLAR</span>
          <span class="text-purple-500 text-xs font-bold">🚛</span>
        </div>
        <div class="text-2xl font-black mt-2 text-white">{{ stats.transport_groups_count }}</div>
        <div class="w-full bg-gray-800 h-1 mt-4 rounded-full overflow-hidden">
          <div class="bg-purple-500 h-full w-3/4 shadow-[0_0_10px_#a855f7]"></div>
        </div>
      </div>

      <div
        class="bg-[#1a1d22] p-5 rounded-2xl border border-gray-800 shadow-xl group hover:border-green-500/50 transition-all">
        <div class="flex justify-between items-start">
          <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">GEOZONA GURUHLAR</span>
          <span class="text-green-500 text-xs font-bold">🗺️</span>
        </div>
        <div class="text-2xl font-black mt-2 text-white">{{ stats.geozone_groups_count }}</div>
        <div class="w-full bg-gray-800 h-1 mt-4 rounded-full overflow-hidden">
          <div class="bg-green-500 h-full w-1/2 shadow-[0_0_10px_#22c55e]"></div>
        </div>
      </div>

      <div
        class="bg-[#1a1d22] p-5 rounded-2xl border border-gray-800 shadow-xl group hover:border-yellow-500/50 transition-all">
        <div class="flex justify-between items-start">
          <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">SAHIFALAR</span>
          <span class="text-yellow-500 text-xs font-bold">📄</span>
        </div>
        <div class="text-2xl font-black mt-2 text-white">{{ stats.pages_count }}</div>
        <div class="w-full bg-gray-800 h-1 mt-4 rounded-full overflow-hidden">
          <div class="bg-yellow-500 h-full w-2/3 shadow-[0_0_10px_#eab308]"></div>
        </div>
      </div>

    </div>

    <div class="grid grid-cols-12 gap-6">
      <div
        class="col-span-8 bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 h-80 flex flex-col relative overflow-hidden group hover:border-cyan-500/30 transition-all">

        <div class="flex justify-between items-center mb-4 z-10">
          <h3 class="text-sm font-bold text-cyan-400 uppercase tracking-wider">Texnikalar Taqsimoti (Guruhlar)</h3>
          <div class="flex gap-2">
            <span class="w-3 h-3 rounded-full bg-cyan-500"></span>
            <span class="text-[10px] text-gray-400">Real vaqt rejimida</span>
          </div>
        </div>

        <div class="w-full h-full" v-if="chartOptions.xaxis.categories.length > 0">
          <apexchart type="bar" height="100%" :options="chartOptions" :series="series"></apexchart>
        </div>

        <div v-else class="flex items-center justify-center h-full text-gray-500 italic">
          Grafik yuklanmoqda...
        </div>

        <div
          class="absolute top-0 right-0 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
        </div>
      </div>
      <div class="col-span-4 bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 h-80 overflow-y-auto custom-scrollbar">
        <h3 class="text-sm font-bold mb-4 text-cyan-400 uppercase tracking-wider">So'nggi Texnikalar</h3>

        <ul v-if="stats.latest_machines && stats.latest_machines.length > 0" class="space-y-4">
          <li v-for="machine in stats.latest_machines" :key="machine.id"
            class="flex items-center gap-3 border-b border-gray-800 pb-3 last:border-0 hover:bg-white/5 p-2 rounded transition-colors">
            <div
              class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold text-xs">
              T
            </div>
            <div>
              <p class="text-white font-bold text-sm">{{ machine.name }}</p>
              <p class="text-gray-500 text-[10px]">{{ formatDate(machine.created_at) }}</p>
            </div>
          </li>
        </ul>

        <div v-else class="text-gray-500 text-xs italic text-center mt-10">
          Hozircha ma'lumot yo'q
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <div class="lg:col-span-2 bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <span class="text-cyan-500">📜</span> Tizim Loglari (Oxirgi harakatlar)
          </h3>
          <router-link to="/admin/logs"
            class="text-[10px] bg-gray-800 hover:bg-gray-700 text-gray-300 px-3 py-1 rounded transition">
            Barchasini ko'rish
          </router-link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="text-[10px] text-gray-500 uppercase border-b border-gray-800">
                <th class="pb-3 font-black">Foydalanuvchi</th>
                <th class="pb-3 font-black">Amal</th>
                <th class="pb-3 font-black">Vaqt</th>
                <th class="pb-3 font-black text-right">Status</th>
              </tr>
            </thead>
            <tbody class="text-sm">
              <tr v-for="(log, i) in systemLogs" :key="i"
                class="border-b border-gray-800/50 last:border-0 hover:bg-white/5 transition-colors">
                <td class="py-2 text-white font-medium">{{ log.user }}</td>
                <td class="py-2 text-gray-400">{{ log.action }}</td>
                <td class="py-2 text-gray-500 text-xs font-mono">{{ log.time }}</td>
                <td class="py-2 text-right">
                  <span
                    :class="log.status === 'Success' ? 'text-green-400 bg-green-400/10' : 'text-red-400 bg-red-400/10'"
                    class="px-2 py-1 rounded text-[10px] font-bold uppercase">
                    {{ log.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="space-y-6">

        <div class="bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 shadow-xl relative overflow-hidden group">
          <div :class="stats.wialon_status?.online ? 'bg-green-500/10' : 'bg-red-500/10'"
            class="absolute top-0 right-0 w-20 h-20 rounded-full blur-xl -mr-5 -mt-5 transition-colors duration-500">
          </div>

          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-400 uppercase">Wialon API</h3>

            <span class="flex h-3 w-3 relative">
              <span v-if="stats.wialon_status?.online"
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span :class="stats.wialon_status?.online ? 'bg-green-500' : 'bg-red-500'"
                class="relative inline-flex rounded-full h-3 w-3"></span>
            </span>
          </div>

          <div :class="stats.wialon_status?.color" class="text-3xl font-black mb-1 transition-colors">
            {{ stats.wialon_status?.text || 'TEKSHIRILMOQDA...' }}
          </div>

          <p class="text-xs text-gray-500">Ping:
            <span :class="stats.wialon_status?.ping < 100 ? 'text-green-400' : 'text-yellow-400'" class="font-mono">
              {{ stats.wialon_status?.ping || 0 }}ms
            </span>
          </p>

          <div class="mt-4 pt-4 border-t border-gray-800 flex justify-between text-xs text-gray-400">
            <span>Oxirgi yangilanish:</span>
            <span class="text-white">Hozirgina</span>
          </div>
        </div>

        <div class="bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 shadow-xl relative overflow-hidden">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-gray-400 uppercase">Ma'lumotlar Bazasi</h3>
            <span class="text-cyan-500 text-xs font-bold">{{ stats.db_status?.text || 'SQL SERVER' }}</span>
          </div>

          <div class="w-full bg-gray-800 h-2 rounded-full overflow-hidden mb-2">
            <div class="bg-cyan-500 h-full shadow-[0_0_10px_#06b6d4] transition-all duration-1000"
              :style="{ width: (stats.db_status?.percent || 0) + '%' }">
            </div>
          </div>

          <div class="flex justify-between text-[10px] text-gray-500">
            <span>Hajmi: <b class="text-white">{{ stats.db_status?.size_mb || 0 }} MB</b></span>
            <span>{{ stats.db_status?.percent || 0 }}% (10GB dan)</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
// VueApexCharts is likely registered globally in main.js, so explicit import might not be needed, 

// --- CHART SETTINGS ---
const series = ref([{
  name: 'Texnikalar soni',
  data: []
}]);

const systemLogs = ref([]);

const chartOptions = ref({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    background: 'transparent',
    fontFamily: 'inherit'
  },
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '50%',
      distributed: true,
    }
  },
  colors: ['#06b6d4', '#8b5cf6', '#22c55e', '#eab308'],
  dataLabels: { enabled: false },
  stroke: { show: true, width: 2, colors: ['transparent'] },
  xaxis: {
    categories: [],
    labels: { style: { colors: '#9ca3af', fontSize: '10px' } },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: { labels: { style: { colors: '#9ca3af' } } },
  grid: {
    borderColor: '#374151',
    strokeDashArray: 4,
    yaxis: { lines: { show: true } }
  },
  tooltip: { theme: 'dark' },
  legend: { show: false }
});

// --- STATISTICS & LOGS ---
const stats = ref({
  machines_count: 0,
  transport_groups_count: 0,
  geozone_groups_count: 0,
  pages_count: 0,
  latest_machines: []
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('uz-UZ', {
    hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short'
  }).format(date);
};

// --- ON MOUNTED ---
onMounted(async () => {
  try {
    const response = await axios.get('/api/dashboard/stats');
    stats.value = response.data;

    // 1. GET LOGS
    if (response.data.system_logs) {
      systemLogs.value = response.data.system_logs;
    }

    // 2. UPDATE CHART DATA (This part was missing/incomplete in your code)
    if (response.data.chart_data) {
      // Update X-axis categories (Group Names)
      chartOptions.value = {
        ...chartOptions.value,
        xaxis: {
          ...chartOptions.value.xaxis,
          categories: response.data.chart_data.labels
        }
      };

      // Update Y-axis data (Counts)
      series.value = [{
        name: 'Texnikalar',
        data: response.data.chart_data.series
      }];
    }

  } catch (error) {
    console.error("Xato:", error);
  }
});
</script>

<style scoped>
/* Scrollbar dizayni */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #1a1d22;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 10px;
}
</style>