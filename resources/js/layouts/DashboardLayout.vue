<template>
  <div class="h-screen flex flex-col transition-colors duration-300 bg-gray-50 dark:bg-[#0f1115] text-gray-800 dark:text-gray-200 overflow-hidden">
    
    <header class="flex-none h-14 flex items-center justify-between px-6 border-b transition-colors duration-300 z-20
      bg-white/80 backdrop-blur-md border-gray-200 
      dark:bg-[#1a1d22]/80 dark:border-gray-700">
      
      <div class="flex items-center gap-4">
        <span class="font-black tracking-widest text-lg text-cyan-600 dark:text-cyan-400">AUTO MONITORING</span>
        
        <nav class="hidden md:flex gap-6 text-xs font-bold ml-8">
          <router-link 
            v-for="page in dynamicPages" 
            :key="page.id" 
            :to="`/page/${page.slug}`"
            class="transition-colors uppercase tracking-widest py-1 border-b-2 border-transparent hover:border-cyan-500
            text-gray-500 hover:text-cyan-600 
            dark:text-gray-400 dark:hover:text-cyan-400"
          >
            {{ page.title }}
          </router-link>
        </nav>
      </div>
      
      <div class="flex gap-4 text-sm items-center">
        <button @click="toggleTheme" 
          class="p-2 rounded-full transition-all bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-orange-500 dark:text-gray-400 dark:hover:text-yellow-400">
          <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>

        <router-link to="/admin" 
          class="hidden sm:block px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all
          bg-cyan-50 text-cyan-600 hover:bg-cyan-100
          dark:bg-cyan-500/10 dark:text-cyan-400 dark:hover:bg-cyan-500/20">
          Admin Panel
        </router-link>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto custom-scrollbar p-6 relative">
      <slot /> 
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useTheme } from '../composables/useTheme';

const { isDark, toggleTheme } = useTheme();
const dynamicPages = ref([]);

const fetchPages = async () => {
  try {
    const res = await axios.get('/api/crud/pages');
    const data = Array.isArray(res.data) ? res.data : (res.data.data || []);
    dynamicPages.value = data.filter(p => p.status === 'active');
  } catch (err) {
    console.error("Sahifalarni yuklashda xato:", err);
  }
};

onMounted(fetchPages);
</script>

<style scoped>
.router-link-active {
  @apply text-cyan-600 dark:text-cyan-400 border-cyan-500;
}

/* Scrollbar faqat Main Content ichida chiqadi */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
</style>