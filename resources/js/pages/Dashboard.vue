<template>
  <div class="grid grid-cols-12 gap-6 p-6 min-h-screen bg-gray-50 dark:bg-[#0f1115] text-gray-800 dark:text-gray-200 transition-colors duration-300">
    
    <section 
      v-if="can('dashboard.view_working')" 
      class="col-span-12 md:col-span-6 xl:col-span-3 flex flex-col gap-4 animate-fade-in"
    >
      <div class="bg-white dark:bg-[#1a1d22] p-5 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 hover:border-green-500/30 transition-all duration-300">
        <StatusRing 
          title="Ish jarayonida" 
          :count="53" 
          color="green" 
        />
        
        <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
          <MachineGrid color="green" />
        </div>
      </div>
    </section>

    <section 
      v-if="can('dashboard.view_fuel')" 
      class="col-span-12 md:col-span-6 xl:col-span-3 flex flex-col gap-4 animate-fade-in"
    >
      <div class="bg-white dark:bg-[#1a1d22] p-5 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 hover:border-orange-500/30 transition-all duration-300">
        <StatusRing 
          title="Yoqilg‘i maydonida" 
          :count="17" 
          color="orange" 
        />
        
        <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
          <MachineGrid color="orange" />
        </div>
      </div>
    </section>

    <section 
      v-if="can('dashboard.view_shift')" 
      class="col-span-12 md:col-span-6 xl:col-span-3 flex flex-col gap-4 animate-fade-in"
    >
      <div class="bg-white dark:bg-[#1a1d22] p-5 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 hover:border-blue-500/30 transition-all duration-300">
        <StatusRing 
          title="Smena almashish" 
          :count="3" 
          color="blue" 
        />
        
        <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
          <MachineGrid color="blue" />
        </div>
      </div>
    </section>

    <section 
      v-if="can('dashboard.view_stats')" 
      class="col-span-12 md:col-span-6 xl:col-span-3 flex flex-col gap-4 animate-fade-in"
    >
      <div class="bg-white dark:bg-[#1a1d22] p-5 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 h-full">
        <RightStats />
      </div>
    </section>

    <div 
      v-if="!can('dashboard.view_working') && !can('dashboard.view_fuel') && !can('dashboard.view_shift') && !can('dashboard.view_stats')" 
      class="col-span-12 flex flex-col items-center justify-center py-32 text-gray-400 dark:text-gray-600"
    >
      <div class="bg-gray-100 dark:bg-[#1a1d22] p-8 rounded-full mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold mb-2">Ruxsat mavjud emas</h3>
      <p class="text-sm opacity-70">Sizda ushbu dashboard ma'lumotlarini ko'rish huquqi yo'q.</p>
    </div>

  </div>
</template>

<script setup>
// Kerakli komponentlarni import qilamiz
// (Yo'llarni loyihangiz tuzilishiga qarab to'g'rilang)
import StatusRing from '../components/StatusRing.vue';
import MachineGrid from '../components/MachineGrid.vue';
import RightStats from '../components/RightStats.vue';

// Biz yaratgan Auth composable-ni import qilamiz
import { useAuth } from '../composables/useAuth';

// "can" funksiyasini va "user" obyektini destructing orqali olamiz
const { can, user } = useAuth();

// Debug uchun konsolga chiqarib ko'rish (ixtiyoriy)
console.log('Joriy foydalanuvchi:', user.name);
console.log('Admin huquqi bormi?', can('admin_access')); // Test
</script>

<style scoped>
/* Sahifa yuklanganda elementlar silliq paydo bo'lishi uchun animatsiya */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}
</style>