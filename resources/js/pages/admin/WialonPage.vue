<template>
  <div class="space-y-6">
    
    <div class="p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/30 text-yellow-200 text-sm flex items-start gap-3">
      <span class="text-xl">⚠️</span>
      <div>
        <h4 class="font-bold uppercase tracking-wider mb-1">Muhim Eslatma</h4>
        <p>Wialon integratsiyasi ishlashi uchun <b>Token</b> yoki <b>Login/Parol</b> to'ldirilishi shart.</p>
      </div>
    </div>

    <div class="bg-[#1a1d22] p-6 rounded-2xl border border-gray-800 shadow-xl flex flex-wrap gap-4 items-center justify-between">
      <div>
        <h3 class="text-white font-bold uppercase tracking-widest text-sm mb-1">Ma'lumotlarni Yangilash</h3>
        <p class="text-gray-500 text-xs">Wialondan texnika va guruhlarni tortib olish</p>
      </div>
      
      <div class="flex gap-3">
        <button 
          @click="sync('machines')" 
          :disabled="loading"
          class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-xl transition-all disabled:opacity-50 active:scale-95 font-bold text-xs uppercase"
        >
          <span v-if="loading && currentSync === 'machines'">Yuklanmoqda...</span>
          <span v-else>🚜 Texnikalar</span>
        </button>

        <button 
          @click="sync('groups')" 
          :disabled="loading"
          class="flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white px-5 py-3 rounded-xl transition-all disabled:opacity-50 active:scale-95 font-bold text-xs uppercase"
        >
          <span v-if="loading && currentSync === 'groups'">Yuklanmoqda...</span>
          <span v-else>📂 Guruhlar</span>
        </button>
      </div>
    </div>

    <div v-if="message" :class="isError ? 'text-red-400 border-red-500/20 bg-red-500/10' : 'text-green-400 border-green-500/20 bg-green-500/10'" class="p-4 border rounded-xl text-sm font-mono whitespace-pre-wrap">
      {{ message }}
    </div>

    <BaseCrud :config="wialonConfig" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue'; 

// --- SINXRONIZATSIYA LOGIKASI ---
const loading = ref(false);
const currentSync = ref('');
const message = ref('');
const isError = ref(false);

const sync = async (type) => {
  loading.value = true;
  currentSync.value = type;
  message.value = '';
  isError.value = false;
  
  try {
    const url = type === 'machines' 
      ? '/api/wialon/sync-machines' 
      : '/api/wialon/sync-groups';
      
    const res = await axios.post(url);
    message.value = "✅ " + res.data.message + "\n" + (res.data.details || '');
  } catch (error) {
    isError.value = true;
    message.value = "❌ Xatolik: " + (error.response?.data?.message || error.message);
  } finally {
    loading.value = false;
    currentSync.value = '';
  }
};

// --- CRUD CONFIG ---
const wialonConfig = {
  title: 'Wialon Integratsiyasi',
  slug: 'wialon_settings', // Backenddagi route bilan bir xil bo'lishi shart!
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'base_url', label: 'API URL', type: 'text', placeholder: 'https://hst-api.wialon.com' },
    { key: 'token', label: 'Wialon Token (Hash)', type: 'textarea', placeholder: 'Tokenni bu yerga joylang...' },
    { key: 'username', label: 'Login (Ixtiyoriy)', type: 'text', placeholder: 'Wialon user' },
    { key: 'password', label: 'Parol (Ixtiyoriy)', type: 'password', placeholder: '******' },
    { key: 'resource_id', label: 'Resource ID', type: 'text', placeholder: 'Masalan: 10453' },
    
    // Status Select (Default qiymat Backendda qo'yiladi, lekin bu yerda ham bo'lgani yaxshi)
    { key: 'status', label: 'Holat', type: 'select', options: [
      { value: 'active', label: 'Faol' },
      { value: 'inactive', label: 'Nofaol' }
    ]}
  ]
};
</script>