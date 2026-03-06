<template>
  <div class="h-full bg-gray-50 dark:bg-[#0f1115] text-gray-800 dark:text-gray-200 p-6 transition-colors duration-300">
    
    <div v-if="loading" class="flex flex-col items-center justify-center h-[80vh]">
      <div class="w-12 h-12 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mb-4"></div>
      <p class="text-cyan-500 font-bold animate-pulse">Tizim yuklanmoqda...</p>
    </div>

    <div v-else-if="layout.length === 0" class="flex flex-col items-center justify-center h-[70vh] text-gray-400">
      <span class="text-6xl mb-4">📭</span>
      <h3 class="text-xl font-bold">Dashboard topilmadi</h3>
      <p class="text-sm">Admin tomonidan siz uchun dashboard shakllantirilmagan.</p>
    </div>

    <div v-else>
      <div class="flex justify-between items-end mb-6 border-b border-gray-200 dark:border-gray-800 pb-4">
        <div>
          <h1 class="text-2xl font-black uppercase tracking-tight">Xush kelibsiz, {{ user.name || 'Foydalanuvchi' }}!</h1>
          <p class="text-xs text-gray-500">Monitoring tizimi</p>
        </div>
        <div class="text-right">
          <div class="text-xl font-mono font-bold text-cyan-600 dark:text-cyan-400">{{ currentTime }}</div>
        </div>
      </div>

      <GridLayout
        v-if="layout.length > 0"
        v-model:layout="layout"
        :col-num="12"
        :row-height="30"
        :is-draggable="false" 
        :is-resizable="false"
        :vertical-compact="true"
        :use-css-transforms="true"
        :responsive="true" 
        :breakpoints="{ lg: 1200, md: 996, sm: 768, xs: 480, xxs: 0 }"
        :cols="{ lg: 12, md: 10, sm: 6, xs: 4, xxs: 2 }"
        class="w-full"
      >
        <GridItem
          v-for="item in layout"
          :key="item.i"
          :x="item.x"
          :y="item.y"
          :w="item.w"
          :h="item.h"
          :i="item.i"
          class="rounded-2xl shadow-lg border bg-white dark:bg-[#1a1d22] border-gray-200 dark:border-gray-800 flex flex-col overflow-hidden hover:shadow-xl transition-shadow duration-300"
        >
          <div class="flex-1 w-full h-full relative">

            <div v-if="getWidgetDef(item.type).category === 'gauge'" class="h-full flex flex-col justify-between p-4 items-center">
              <h4 class="text-xs font-bold uppercase text-gray-500 w-full text-left">{{ item.data?.title || getWidgetDef(item.type).title }}</h4>
              <div class="relative w-full flex-1 flex items-center justify-center">
                <svg viewBox="0 0 100 60" class="w-full h-full max-h-[150px]">
                  <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#374151" stroke-width="10" stroke-linecap="round" class="opacity-20 dark:opacity-100" />
                  <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" 
                        :stroke="item.data?.color || getWidgetDef(item.type).color || '#22c55e'" 
                        stroke-width="10" 
                        stroke-linecap="round" 
                        stroke-dasharray="125.6" 
                        :stroke-dashoffset="125.6 - (125.6 * (getWidgetValue(item.type) / getWidgetDef(item.type).max))" 
                        class="transition-all duration-1000 ease-out" />
                </svg>
                <div class="absolute bottom-0 flex flex-col items-center mb-2">
                  <span class="text-3xl font-black text-gray-900 dark:text-white leading-none">{{ getWidgetValue(item.type) }}</span>
                  <span class="text-[10px] text-gray-500 font-bold uppercase">{{ getWidgetDef(item.type).unit }}</span>
                </div>
              </div>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'area_chart'" class="h-full w-full flex flex-col p-4 relative overflow-hidden">
              <div class="flex justify-between items-center z-10 mb-2">
                <h4 class="text-xs font-bold uppercase text-gray-500">{{ item.data?.title || getWidgetDef(item.type).title }}</h4>
                <span class="text-xs font-bold text-white px-2 py-0.5 rounded-full" :style="{ backgroundColor: item.data?.color || '#22c55e' }">Live</span>
              </div>
              <div class="flex-1 w-full relative">
                <svg viewBox="0 0 300 100" class="w-full h-full absolute bottom-0" preserveAspectRatio="none">
                  <defs>
                    <linearGradient :id="'grad-' + item.i" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" :stop-color="item.data?.color || '#22c55e'" stop-opacity="0.5" />
                      <stop offset="100%" :stop-color="item.data?.color || '#22c55e'" stop-opacity="0" />
                    </linearGradient>
                  </defs>
                  <path d="M0,100 L0,50 C50,20 100,80 150,40 C200,10 250,60 300,30 L300,100 Z" 
                        :fill="`url(#grad-${item.i})`" 
                        :stroke="item.data?.color || '#22c55e'" 
                        stroke-width="2" 
                        class="drop-shadow-lg transition-all duration-500" />
                </svg>
              </div>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'stat'" class="h-full flex flex-col justify-center items-center p-4 relative overflow-hidden">
              <div class="absolute top-0 right-0 w-20 h-20 opacity-10 rounded-bl-full" :style="{ backgroundColor: item.data?.color || '#06b6d4' }"></div>
              <span class="text-3xl mb-2 filter drop-shadow-md">{{ getWidgetDef(item.type).icon }}</span>
              <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ getWidgetValue(item.type) }}</h3>
              <p class="text-[10px] uppercase font-bold text-gray-500 text-center tracking-wider">{{ item.data?.title || getWidgetDef(item.type).title }}</p>
              <p class="text-[10px] font-bold mt-1 opacity-80" :style="{ color: item.data?.color || '#06b6d4' }">{{ getWidgetDef(item.type).subtext }}</p>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'chart'" class="h-full w-full flex flex-col p-4">
              <h4 class="text-xs font-bold uppercase text-gray-500 mb-2 flex justify-between items-center">{{ item.data?.title || getWidgetDef(item.type).title }} <span :style="{ color: item.data?.color || '#06b6d4' }" class="text-[10px]">↑ 12%</span></h4>
              <div class="flex-1 flex items-end justify-between gap-1 pb-2">
                <div v-for="(val, idx) in (Array.isArray(getWidgetValue(item.type)) ? getWidgetValue(item.type) : [50,30,80])" :key="idx" 
                     class="w-full rounded-t opacity-70 hover:opacity-100 transition-all duration-300 relative group" 
                     :style="{ backgroundColor: item.data?.color || '#06b6d4', height: (val/2) + '%' }">
                </div>
              </div>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'grid'" class="h-full w-full flex flex-col">
              <div class="p-3 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]">
                <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: item.data?.color || '#22c55e' }"></span>
                  {{ item.data?.title || getWidgetDef(item.type).title }}
                </h4>
                <span class="text-[10px] font-mono text-gray-400">
                  {{ (getWidgetValue(item.type)?.length) || 0 }} ta
                </span>
              </div>
              <div class="flex-1 overflow-y-auto p-2 custom-scrollbar">
                <div class="grid grid-cols-4 gap-2">
                  <div v-for="(machine, idx) in (getWidgetValue(item.type) || [])" :key="idx" 
                       class="rounded-lg p-1.5 flex flex-col items-center justify-center transition-all hover:scale-105 cursor-pointer shadow-sm min-h-[50px] border"
                       :style="{ backgroundColor: item.data?.color || '#22c55e', borderColor: item.data?.color || '#22c55e', color: isLightColor(item.data?.color) ? '#000' : '#fff' }">
                    <span class="text-[9px] font-black opacity-70 uppercase tracking-tighter">{{ machine.label }}</span>
                    <span class="text-sm font-black leading-tight">{{ machine.value }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'system'" class="h-full flex flex-col justify-center p-5">
              <div class="flex justify-between mb-2"><span class="text-xs font-bold text-gray-500 uppercase">{{ item.data?.title || getWidgetDef(item.type).title }}</span><span class="text-xs font-bold" :style="{ color: item.data?.color || '#3b82f6' }">{{ getWidgetValue(item.type) }}%</span></div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 h-3 rounded-full overflow-hidden"><div class="h-full rounded-full transition-all duration-1000 shadow-[0_0_10px]" :style="{ backgroundColor: item.data?.color || '#3b82f6', width: getWidgetValue(item.type) + '%' }"></div></div>
            </div>

            <div v-else-if="getWidgetDef(item.type).category === 'list'" class="h-full w-full flex flex-col">
               <div class="p-3 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]"><h4 class="text-xs font-black uppercase text-gray-500">{{ item.data?.title || getWidgetDef(item.type).title }}</h4></div>
               <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                 <div v-for="(row, idx) in (getWidgetValue(item.type) || [])" :key="idx" class="flex justify-between items-center p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors text-xs">
                   <span class="font-bold dark:text-gray-300">{{ row.name }}</span>
                   <span :class="row.status.includes('%') || row.status === 'OK' ? 'text-green-500' : 'text-red-500'">{{ row.status }}</span>
                 </div>
               </div>
            </div>

            <div v-else-if="item.type === 'WeatherWidget'" class="h-full w-full flex flex-col justify-between p-5 bg-gradient-to-br from-blue-500 to-cyan-400 text-white relative overflow-hidden"><div class="absolute -right-4 -top-4 text-9xl opacity-20">☀️</div><div class="flex justify-between items-start z-10"><div><h4 class="font-bold text-lg leading-none">Toshkent</h4><p class="text-[10px] opacity-80 mt-1 uppercase tracking-wide">Bugun</p></div><span class="text-4xl drop-shadow-md">⛅</span></div><div class="z-10"><h2 class="text-5xl font-black tracking-tighter">24°</h2><p class="text-xs font-bold opacity-90">Qisman bulutli</p></div></div>
            
            <div v-else-if="item.type === 'ClockWidget'" class="h-full w-full flex flex-col items-center justify-center bg-gray-900 text-white"><div class="text-4xl font-mono font-bold text-cyan-400">{{ currentTime }}</div><div class="text-xs text-gray-500 font-bold uppercase mt-1 tracking-[4px]">Toshkent vaqti</div></div>
            
            <div v-else-if="item.type === 'NoteWidget'" class="h-full w-full flex flex-col p-4 bg-yellow-50 dark:bg-yellow-900/10"><h4 class="text-xs font-bold uppercase text-yellow-600 dark:text-yellow-500 mb-2">📌 Eslatma</h4><textarea class="w-full flex-1 bg-transparent resize-none outline-none text-sm" placeholder="Yozib qo'ying..." readonly>{{ item.data?.text || '' }}</textarea></div>

          </div>
        </GridItem>
      </GridLayout>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { GridLayout, GridItem } from 'grid-layout-plus';
import axios from 'axios';
import { useAuth } from '../composables/useAuth';

const { user } = useAuth();
const layout = ref([]);
const realData = ref({});
const loading = ref(true);
const currentTime = ref(new Date().toLocaleTimeString());

let timer;
let dataInterval;

onMounted(async () => {
  timer = setInterval(() => currentTime.value = new Date().toLocaleTimeString(), 1000);
  await loadDashboardConfig();
  await fetchRealData();
  dataInterval = setInterval(fetchRealData, 10000);
});

onUnmounted(() => {
  clearInterval(timer);
  clearInterval(dataInterval);
});

const loadDashboardConfig = async () => {
  try {
    const res = await axios.get('/api/dashboard/config');
    if (res.data && Array.isArray(res.data) && res.data.length > 0) {
      layout.value = res.data;
    } else {
      layout.value = [];
    }
  } catch (e) {
    console.error("Dashboard yuklashda xato:", e);
  } finally {
    loading.value = false;
  }
};

const fetchRealData = async () => {
  try {
    const res = await axios.get('/api/dashboard/data');
    realData.value = res.data;
  } catch (e) { console.error("Data error", e); }
};

const getWidgetValue = (type) => {
  if (['GreenGrid', 'OrangeGrid', 'BlueGrid', 'LastEvents', 'TopDrivers', 'MaintenanceList', 'WeeklyChart', 'FuelChart', 'DistanceChart', 'SplineChart'].includes(type)) {
      return realData.value[type] || [];
  }
  return realData.value[type] !== undefined ? realData.value[type] : 0;
};

// --- CONFIG ---
const widgetDefinitions = {
  'SpeedGauge': { category: 'gauge', title: 'Tezlik', unit: 'km/h', max: 200, color: '#22c55e' },
  'RpmGauge': { category: 'gauge', title: 'Motor', unit: 'x1000', max: 8, color: '#f97316' },
  'SplineChart': { category: 'area_chart', title: 'Signal' },
  'GreenGrid': { category: 'grid', title: 'Ishchilar' },
  'OrangeGrid': { category: 'grid', title: 'Yoqilg\'i' },
  'BlueGrid': { category: 'grid', title: 'Smena' },
  'TotalMachines': { category: 'stat', title: 'Jami', icon: '🚜', subtext: 'Parkdagi texnikalar' },
  'ActiveDrivers': { category: 'stat', title: 'Haydovchilar', icon: '👨‍✈️', subtext: 'Faol haydovchilar' },
  'FuelConsumption': { category: 'stat', title: 'Sarf', icon: '⛽', subtext: 'Kunlik sarf' },
  'TotalDistance': { category: 'stat', title: 'Masofa', icon: '🛣️', subtext: 'Jami masofa' },
  'AlertsCount': { category: 'stat', title: 'Xabarlar', icon: '🔔', subtext: 'Ogohlantirishlar' },
  'OnlineUsers': { category: 'stat', title: 'Adminlar', icon: '👥', subtext: 'Online adminlar' },
  'WeeklyChart': { category: 'chart', title: 'Haftalik' },
  'FuelChart': { category: 'chart', title: 'Yoqilg\'i' },
  'DistanceChart': { category: 'chart', title: 'Masofa' },
  'ServerStatus': { category: 'system', title: 'Server' },
  'DbStatus': { category: 'system', title: 'Baza' },
  'WialonStatus': { category: 'system', title: 'API' },
  'LastEvents': { category: 'list', title: 'Hodisalar' },
  'TopDrivers': { category: 'list', title: 'Reyting' },
  'MaintenanceList': { category: 'list', title: 'Remont' },
  'WeatherWidget': { category: 'other' },
  'ClockWidget': { category: 'other' },
  'NoteWidget': { category: 'other' },
};

const getWidgetDef = (type) => widgetDefinitions[type] || { category: 'other', title: 'Noma\'lum', color: '#888' };

const isLightColor = (hex) => {
  if(!hex) return false;
  const c = hex.substring(1);
  const rgb = parseInt(c, 16);
  return ((rgb >> 16) & 0xff) * 0.299 + ((rgb >> 8) & 0xff) * 0.587 + ((rgb >> 0) & 0xff) * 0.114 > 186;
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
</style>