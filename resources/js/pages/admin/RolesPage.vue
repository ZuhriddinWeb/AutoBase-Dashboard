<template>
  <div class="space-y-6 h-full flex flex-col transition-colors duration-300">
    
    <div class="flex justify-between items-center p-4 rounded-2xl border shadow-lg transition-colors duration-300
      bg-white border-gray-200 
      dark:bg-[#1a1d22] dark:border-gray-800">
      
      <div class="flex items-center gap-3">
        <div class="w-2 h-8 bg-purple-500 rounded-full shadow-[0_0_15px_rgba(168,85,247,0.5)]"></div>
        <h1 class="text-xl font-black uppercase tracking-tighter text-gray-900 dark:text-white">ROLLAR VA RUXSATLAR</h1>
      </div>
      
      <button @click="createRole" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-6 py-2.5 rounded-xl transition-all shadow-md active:scale-95 text-xs uppercase">
        + Yangi Rol
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 flex-1 min-h-0">
      
      <div class="lg:col-span-1 p-5 rounded-2xl border shadow-xl overflow-y-auto custom-scrollbar flex flex-col transition-colors duration-300
        bg-white border-gray-200 
        dark:bg-[#1a1d22] dark:border-gray-800">
        
        <h3 class="text-[10px] font-black uppercase mb-4 tracking-[2px] text-gray-500 dark:text-gray-500">Mavjud Rollar</h3>
        
        <div v-if="loadingRoles" class="text-center py-4 text-xs animate-pulse text-gray-600 dark:text-gray-400">Yuklanmoqda...</div>
        
        <ul v-else class="space-y-2">
          <li v-for="role in roles" :key="role.id" 
            @click="selectRole(role)"
            :class="[
              'p-4 rounded-xl border cursor-pointer transition-all flex justify-between items-center group relative overflow-hidden',
              selectedRole?.id === role.id 
                ? 'bg-purple-100 border-purple-500 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 shadow-md' 
                : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-gray-400 hover:text-black dark:bg-[#0f1115] dark:border-gray-800 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-white'
            ]"
          >
            <div v-if="selectedRole?.id === role.id" class="absolute left-0 top-0 bottom-0 w-1 bg-purple-500"></div>

            <span class="font-bold text-sm">{{ role.name }}</span>
            
            <div class="flex items-center gap-2">
               <span class="text-[10px] font-mono px-2 py-1 rounded transition
                 bg-gray-200 text-gray-500 group-hover:text-gray-700
                 dark:bg-black/30 dark:text-gray-500 dark:group-hover:text-gray-300">
                 ID: {{ role.id }}
               </span>
               <button v-if="role.name !== 'Super Admin'" @click.stop="deleteRole(role)" class="text-gray-400 hover:text-red-500 transition px-1">
                 🗑
               </button>
            </div>
          </li>
        </ul>
      </div>

      <div class="lg:col-span-3 rounded-2xl border shadow-xl flex flex-col overflow-hidden relative transition-colors duration-300
        bg-white border-gray-200 
        dark:bg-[#1a1d22] dark:border-gray-800">
        
        <div v-if="selectedRole" class="flex flex-col h-full">
          <div class="p-6 border-b flex justify-between items-center shrink-0 transition-colors duration-300
            bg-gray-50 border-gray-200 
            dark:bg-[#1e2228] dark:border-gray-800">
            <div>
              <span class="text-[10px] uppercase tracking-widest font-bold text-gray-500">Tanlangan Rol:</span>
              <h2 class="text-2xl font-black mt-1 text-gray-900 dark:text-white">{{ selectedRole.name }}</h2>
            </div>
            
            <button 
              @click="savePermissions" 
              :disabled="saving"
              class="bg-green-500 hover:bg-green-400 text-white dark:text-black font-black px-8 py-3 rounded-xl transition-all shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed text-xs uppercase"
            >
              {{ saving ? 'Saqlanmoqda...' : 'O\'zgarishlarni Saqlash' }}
            </button>
          </div>

          <div class="overflow-auto custom-scrollbar p-6 flex-1 transition-colors duration-300
            bg-white dark:bg-[#0f1115]">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="text-[10px] uppercase tracking-[2px] border-b
                  text-gray-500 border-gray-200 dark:border-gray-800">
                  <th class="pb-4 pl-4 font-black">Bo'lim (Resurs)</th>
                  <th class="pb-4 text-center font-black text-blue-600 dark:text-blue-400">Ko'rish (View)</th>
                  <th class="pb-4 text-center font-black text-green-600 dark:text-green-400">Yaratish (Create)</th>
                  <th class="pb-4 text-center font-black text-yellow-600 dark:text-yellow-400">Tahrirlash (Edit)</th>
                  <th class="pb-4 text-center font-black text-red-600 dark:text-red-400">O'chirish (Delete)</th>
                </tr>
              </thead>
              <tbody class="text-sm divide-y divide-gray-200 dark:divide-gray-800/50">
                <tr v-for="(actions, resource) in permissionsMatrix" :key="resource" class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition group">
                  
                  <td class="p-4 font-bold capitalize border-r
                    text-gray-700 bg-gray-50 border-gray-200 
                    dark:text-gray-300 dark:bg-[#16191d] dark:border-gray-800/50">
                    {{ formatResourceName(resource) }}
                  </td>

                  <td v-for="action in ['view', 'create', 'edit', 'delete']" :key="action" class="p-4 text-center relative">
                    <label v-if="permissionExists(resource, action)" class="inline-flex items-center cursor-pointer justify-center w-full h-full">
                      <input type="checkbox" 
                        :value="resource + '_' + action" 
                        v-model="selectedPermissions"
                        class="peer sr-only">
                      
                      <div class="w-5 h-5 border-2 rounded transition-all duration-200 flex items-center justify-center
                        border-gray-300 bg-white peer-checked:bg-purple-500 peer-checked:border-purple-500
                        dark:border-gray-700 dark:bg-transparent dark:peer-checked:bg-purple-500 dark:peer-checked:border-purple-500">
                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                    </label>

                    <span v-else class="text-xs select-none text-gray-300 dark:text-gray-800">-</span>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="col-span-2 flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50 dark:bg-[#0f1115]/50 dark:text-gray-600">
          <div class="text-6xl mb-4 opacity-20">🛡️</div>
          <p class="font-bold uppercase tracking-widest text-xs">Ruxsatlarni boshqarish uchun chapdan birorta rolni tanlang</p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// State
const roles = ref([]);
const allPermissions = ref([]); 
const selectedRole = ref(null);
const selectedPermissions = ref([]); 
const loadingRoles = ref(false);
const saving = ref(false);

// 1. API dan ma'lumot olish
const fetchData = async () => {
  loadingRoles.value = true;
  try {
    const token = localStorage.getItem('token'); 
    const config = { headers: { Authorization: `Bearer ${token}` } };

    const [rolesRes, permsRes] = await Promise.all([
      axios.get('/api/roles', config),
      axios.get('/api/permissions', config)
    ]);
    
    roles.value = Array.isArray(rolesRes.data) ? rolesRes.data : rolesRes.data.data;
    allPermissions.value = Array.isArray(permsRes.data) ? permsRes.data : permsRes.data.data;

  } catch (e) {
    console.error("Ma'lumot olishda xato:", e);
    if (e.response && e.response.status === 401) {
       alert("Sessiya vaqti tugadi. Qaytadan kiring.");
    } else {
       alert("Serverga ulanishda xatolik!");
    }
  } finally {
    loadingRoles.value = false;
  }
};

// 2. Ruxsatlarni Matritsa ko'rinishiga keltirish
const permissionsMatrix = computed(() => {
  const groups = {};
  if (!allPermissions.value) return {};

  allPermissions.value.forEach(p => {
    const parts = p.name.split('_');
    const action = parts.pop(); 
    const resource = parts.join('_');

    if (!groups[resource]) groups[resource] = [];
    if (!groups[resource].includes(action)) groups[resource].push(action);
  });

  return groups;
});

// 3. Yordamchi funksiyalar
const formatResourceName = (name) => {
  return name.replace(/_/g, ' ').toUpperCase(); 
};

const permissionExists = (resource, action) => {
  const permName = `${resource}_${action}`;
  return allPermissions.value.some(p => p.name === permName);
};

// 4. Rol tanlash
const selectRole = async (role) => {
  selectedRole.value = role;
  try {
    if (role.permissions) {
       selectedPermissions.value = role.permissions.map(p => p.name);
    } else {
       const res = await axios.get(`/api/roles/${role.id}/permissions`);
       const data = Array.isArray(res.data) ? res.data : res.data.data; 
       selectedPermissions.value = data.map(p => p.name);
    }
  } catch (e) {
    console.error(e);
    selectedPermissions.value = [];
  }
};

// 5. Saqlash
const savePermissions = async () => {
  if (!selectedRole.value) return;
  saving.value = true;
  try {
    await axios.post(`/api/roles/${selectedRole.value.id}/sync-permissions`, {
      permissions: selectedPermissions.value
    });
    selectedRole.value.permissions_count = selectedPermissions.value.length;
    alert(`"${selectedRole.value.name}" roli uchun ruxsatlar saqlandi!`);
    fetchData(); 
  } catch (e) {
    console.error(e);
    alert("Saqlashda xatolik: " + (e.response?.data?.message || e.message));
  } finally {
    saving.value = false;
  }
};

// 6. Rol Yaratish
const createRole = async () => {
  const name = prompt("Yangi rol nomini kiriting (masalan: Manager):");
  if (!name) return;
  
  try {
    await axios.post('/api/roles', { name });
    fetchData(); 
  } catch (e) {
    alert("Xatolik: " + (e.response?.data?.message || "Yaratib bo'lmadi"));
  }
};

// 7. Rol O'chirish
const deleteRole = async (role) => {
  if (!confirm(`"${role.name}" rolini o'chirib tashlamoqchimisiz?`)) return;
  
  try {
    await axios.delete(`/api/roles/${role.id}`);
    if (selectedRole.value?.id === role.id) selectedRole.value = null;
    fetchData();
  } catch (e) {
    alert("O'chirishda xatolik!");
  }
};

onMounted(fetchData);
</script>

<style scoped>
/* Scrollbar Light/Dark */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #374151;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #a855f7; /* Purple hover */
}
</style>