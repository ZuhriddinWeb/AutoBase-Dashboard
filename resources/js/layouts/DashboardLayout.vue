<template>
  <div class="min-h-screen bg-[#0f1115] text-gray-200">
    <header class="flex items-center justify-between px-4 h-12 bg-[#1a1d22] border-b border-gray-700 fixed w-full z-10">
      <div class="flex items-center gap-4">
        <span class="text-cyan-400 font-bold tracking-widest">AUTO MONITORING</span>
        
        <nav class="flex gap-4 text-xs ml-8">
          <router-link 
            v-for="page in dynamicPages" 
            :key="page.id" 
            :to="`/page/${page.slug}`"
            class="text-gray-400 hover:text-cyan-400 transition-colors uppercase tracking-tighter"
          >
            {{ page.title }}
          </router-link>
        </nav>
      </div>
      
      <div class="flex gap-4 text-sm items-center">
        <router-link to="/admin" class="text-cyan-400 cursor-pointer hover:bg-cyan-500/10 px-3 py-1 rounded transition">
          Admin Panelga o'tish
        </router-link>
      </div>
    </header>

    <main class="pt-16 p-4">
      <slot /> 
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const dynamicPages = ref([]);

const fetchPages = async () => {
  try {
    // Faqat 'active' holatdagi sahifalarni olib kelamiz
    const res = await axios.get('/api/crud/pages');
    // Agar status bo'yicha filtrlash backendda bo'lmasa, frontendda filtrlaymiz
    dynamicPages.value = res.data.filter(p => p.status === 'active');
  } catch (err) {
    console.error("Sahifalarni yuklashda xato:", err);
  }
};

onMounted(fetchPages);
</script>

<style scoped>
.router-link-active {
  @apply text-cyan-400 font-bold;
}
</style>