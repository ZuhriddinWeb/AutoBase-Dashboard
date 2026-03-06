<template>
  <div
    class="flex min-h-screen transition-colors duration-300 bg-gray-100 dark:bg-[#0f1115] text-gray-800 dark:text-gray-200 overflow-hidden">

    <div v-if="isMobileOpen" @click="isMobileOpen = false"
      class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm transition-opacity"></div>

    <aside
      class="fixed h-full z-50 shadow-2xl transition-all duration-300 ease-in-out bg-white dark:bg-[#1a1d22] border-r border-gray-200 dark:border-gray-800 flex flex-col"
      :class="[
        // Desktop holati (Keng yoki Tor)
        !isMobile ? (isSidebarExpanded ? 'w-64' : 'w-20') : '',
        // Mobil holati (Yashirin yoki Ochiq)
        isMobile ? (isMobileOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64') : 'translate-x-0'
      ]">

      <div
        class="h-[72px] flex items-center border-b border-gray-200 dark:border-gray-800 transition-all duration-300 overflow-hidden"
        :class="isSidebarExpanded || isMobile ? 'px-6 justify-between' : 'justify-center px-0'">

        <div class="flex items-center gap-3">

          <div
            class="w-8 h-8 min-w-[32px] bg-cyan-500 rounded flex items-center justify-center text-white shadow-lg shadow-cyan-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>

          <span v-show="isSidebarExpanded || isMobile"
            class="font-bold tracking-tighter text-xl text-gray-900 dark:text-white whitespace-nowrap transition-opacity duration-300">
            ADMIN PANEL
          </span>

        </div>

        <button v-if="isMobile" @click="isMobileOpen = false" class="text-gray-500 hover:text-red-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 p-3 space-y-2 mt-2 flex flex-col overflow-y-auto custom-scrollbar overflow-x-hidden">

        <router-link v-for="link in menuItems" :key="link.path" :to="link.path"
          class="flex items-center rounded-xl transition-all duration-200 group relative" :class="[
            // Aktiv klass
            $route.path === link.path
              ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold'
              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-black dark:hover:text-white',
            // Ochiq/Yopiq holat paddinglari
            isSidebarExpanded || isMobile ? 'px-4 py-3' : 'justify-center py-3 px-0'
          ]" :title="!isSidebarExpanded ? link.name : ''">
          <span class="text-xl transition-transform duration-300 group-hover:scale-110">{{ link.icon }}</span>

          <span v-show="isSidebarExpanded || isMobile"
            class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300">
            {{ link.name }}
          </span>

          <div v-if="!isSidebarExpanded && !isMobile"
            class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-lg">
            {{ link.name }}
          </div>
        </router-link>

        <div class="h-px bg-gray-200 dark:bg-gray-800 my-2"></div>

        <router-link to="/" class="flex items-center rounded-xl transition-all duration-200 group"
          :class="isSidebarExpanded || isMobile ? 'px-4 py-3' : 'justify-center py-3'">
          <span class="text-xl">⬅</span>
          <span v-show="isSidebarExpanded || isMobile"
            class="ml-3 whitespace-nowrap text-cyan-600 dark:text-cyan-400 italic">Monitoring</span>
        </router-link>

        <div class="mt-auto pt-4 space-y-2">

          <button @click="toggleTheme"
            class="w-full flex items-center rounded-xl transition-all duration-200 group relative" :class="[
              'hover:bg-gray-100 dark:hover:bg-white/5 text-gray-600 dark:text-gray-400',
              isSidebarExpanded || isMobile ? 'px-4 py-3' : 'justify-center py-3'
            ]">
            <span class="text-xl">{{ isDark ? '☀️' : '🌙' }}</span>
            <span v-show="isSidebarExpanded || isMobile" class="ml-3 whitespace-nowrap font-bold">
              {{ isDark ? 'Kun rejimi' : 'Tun rejimi' }}
            </span>
          </button>

          <button @click="handleLogout"
            class="w-full flex items-center rounded-xl transition-all duration-200 group text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
            :class="isSidebarExpanded || isMobile ? 'px-4 py-3' : 'justify-center py-3'">
            <span class="text-xl group-hover:scale-110 transition-transform">🚪</span>
            <span v-show="isSidebarExpanded || isMobile" class="ml-3 whitespace-nowrap font-bold">Chiqish</span>
          </button>
        </div>

      </nav>
    </aside>

    <main class="flex-1 min-h-screen flex flex-col transition-all duration-300 ease-in-out" :class="[
      !isMobile ? (isSidebarExpanded ? 'ml-64' : 'ml-20') : 'ml-0'
    ]">

      <header
        class="h-[72px] bg-white dark:bg-[#1a1d22] border-b border-gray-200 dark:border-gray-800 flex items-center px-6 sticky top-0 z-30 shadow-sm justify-between">
        <div class="flex items-center">
          <button @click="toggleSidebar"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-white transition-colors">
            <svg v-if="!isSidebarExpanded && !isMobile" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else-if="isSidebarExpanded && !isMobile" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <h2 class="ml-4 font-bold text-gray-500 dark:text-gray-400 uppercase text-sm tracking-wider">Boshqaruv Paneli
          </h2>
        </div>

        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold">
            U</div>
        </div>
      </header>

      <div class="p-6 md:p-10 flex-1 overflow-x-hidden">
        <router-view />
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useTheme } from '../composables/useTheme';

const router = useRouter();
const { isDark, toggleTheme } = useTheme();

// --- STATE ---
const isSidebarExpanded = ref(true); // Desktopda ochiq/yopiq holati
const isMobileOpen = ref(false);     // Mobilda ochiq/yopiq holati
const windowWidth = ref(window.innerWidth);

// Mobil qurilma ekanligini aniqlash
const isMobile = computed(() => windowWidth.value < 768);

// --- MENU ITEMS (Array qilib oldim, kod toza turishi uchun) ---
const menuItems = [
  { name: 'Asosiy Panel', path: '/admin', icon: '📊' },
  { name: 'Sahifalar', path: '/admin/pages', icon: '📄' },
  { name: 'Guruhlar', path: '/admin/groups', icon: '📁' },
  { name: 'Wialon API', path: '/admin/wialon', icon: '🛰️' },
  { name: 'Texnikalar', path: '/admin/machines', icon: '🚜' },
  { name: 'Trans. Guruhlari', path: '/admin/transport-groups', icon: '🚛' },
  { name: 'Geozona', path: '/admin/geozone-groups', icon: '🗺️' },
  { name: 'Foydalanuvchilar', path: '/admin/users', icon: '👥' },
  { name: 'Rollar', path: '/admin/roles', icon: '🛡️' },
  { name: 'Dashboard yaratish', path: '/admin/custom', icon: '🎨' },
];

// --- ACTIONS ---
const toggleSidebar = () => {
  if (isMobile.value) {
    isMobileOpen.value = !isMobileOpen.value;
  } else {
    isSidebarExpanded.value = !isSidebarExpanded.value;
  }
};

// Oyna o'lchami o'zgarganda kuzatish
onMounted(() => {
  window.addEventListener('resize', () => {
    windowWidth.value = window.innerWidth;
    // Desktopga o'tganda sidebarni ochib qo'yish (ixtiyoriy)
    if (windowWidth.value >= 768) {
      isMobileOpen.value = false;
    }
  });
});

const handleLogout = async () => {
  if (!confirm("Tizimdan chiqmoqchimisiz?")) return;
  try { await axios.post('/api/logout'); }
  catch (error) { console.error("Logout xato:", error); }
  finally {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  }
};
</script>

<style scoped>
/* Scrollbar (faqat sidebar uchun) */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #374151;
}
</style>