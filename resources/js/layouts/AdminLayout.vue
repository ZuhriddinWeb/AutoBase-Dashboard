<template>
  <div class="flex min-h-screen bg-[#0f1115] text-gray-200">
    <aside class="w-64 bg-[#1a1d22] border-r border-gray-800 flex flex-col fixed h-full shadow-2xl">
      <div class="p-6 border-b border-gray-800 flex items-center gap-3">
        <div class="w-8 h-8 bg-cyan-500 rounded flex items-center justify-center text-black font-black">A</div>
        <span class="font-bold tracking-tighter text-xl">ADMIN PANEL</span>
      </div>

      <nav class="flex-1 p-4 space-y-2 mt-4">
        <router-link to="/admin" class="admin-link">📊 Asosiy Panel</router-link>
        <router-link to="/admin/users" class="admin-link">👥 Foydalanuvchilar</router-link>
        <router-link to="/admin/roles" class="admin-link">
          <!-- <span class="text-xl">🛡️ Rollar va Ruxsatlar</span> -->
        🛡️ Rollar va Ruxsatlar

          <!-- <span class="font-bold">Rollar va Ruxsatlar</span> -->
        </router-link>
        <router-link to="/admin/pages" class="admin-link">
          <span class="mr-2">📄</span> Sahifalar
        </router-link>
        <router-link to="/admin/groups" class="admin-link">
          <span class="mr-2">📁</span> Guruhlar
        </router-link>
        <router-link to="/admin/transport-groups" class="admin-link">
          <span class="mr-2">🚛</span> Trans. Guruhlari
        </router-link>

        <router-link to="/admin/geozone-groups" class="admin-link">
          <span class="mr-2">🗺️</span> Geozona Guruhlari
        </router-link>
        <router-link to="/admin/machines" class="admin-link">🚜 Texnikalar</router-link>

        <router-link to="/admin/wialon" class="admin-link">
          <span class="mr-2">🛰️</span> Wialon API
        </router-link>
        <router-link to="/" class="admin-link mt-10 text-cyan-400 border-t border-gray-800 pt-6 italic">⬅ Monitoringa
          qaytish</router-link>
          <div class="mt-auto pt-4 border-t border-gray-800">
          <button @click="handleLogout" class="w-full text-left px-4 py-3 rounded-xl transition-all hover:bg-red-500/10 text-red-400 hover:text-red-500 font-bold flex items-center gap-3">
            <span>🚪</span>
            Chiqish
          </button>
        </div>
      </nav>
      
    </aside>

    <main class="flex-1 ml-64 p-10">
      <router-view />
    </main>
  </div>
</template>
<script setup>
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const handleLogout = async () => {
  if (!confirm("Tizimdan chiqmoqchimisiz?")) return;

  try {
    // 1. Backendga logout so'rovini yuborish (Tokenni o'chirish uchun)
    await axios.post('/api/logout');
  } catch (error) {
    console.error("Logout xatosi:", error);
    // Xato bersa ham baribir chiqaramiz (token eskirgan bo'lishi mumkin)
  } finally {
    // 2. LocalStorage ni tozalash
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('roles');
    localStorage.removeItem('permissions');

    // 3. Login sahifasiga yo'naltirish
    router.push('/login');
  }
};
</script>

<style scoped>
.admin-link {
  @apply block px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white text-gray-400;
}

.router-link-exact-active {
  @apply bg-cyan-500/10 text-cyan-400 border-l-4 border-cyan-500 rounded-l-none;
}

/* Scrollbar dizayni */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #1a1d22;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 4px;
}
.admin-link {
  @apply block px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white text-gray-400;
}

.router-link-exact-active {
  @apply bg-cyan-500/10 text-cyan-400 border-l-4 border-cyan-500 rounded-l-none;
}
</style>
