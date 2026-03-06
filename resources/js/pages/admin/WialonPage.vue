<template>
  <div class="space-y-6">
    
    <div class="p-4 rounded-2xl flex items-start gap-3 border text-sm
      bg-yellow-50 border-yellow-200 text-yellow-800 
      dark:bg-yellow-500/10 dark:border-yellow-500/30 dark:text-yellow-200">
      <span class="text-xl">⚠️</span>
      <div>
        <h4 class="font-bold uppercase tracking-wider mb-1">Muhim Eslatma</h4>
        <p>Wialon integratsiyasi ishlashi uchun <b>Token</b> yoki <b>Login/Parol</b> to'ldirilishi shart.</p>
      </div>
    </div>

    <div class="p-6 rounded-2xl border shadow-xl flex flex-wrap gap-4 items-center justify-between transition-colors duration-300
      bg-white border-gray-200 
      dark:bg-[#1a1d22] dark:border-gray-800">
      
      <div>
        <h3 class="font-bold uppercase tracking-widest text-sm mb-1 text-gray-900 dark:text-white">Ma'lumotlarni Yangilash</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400">Wialondan ma'lumotlarni tortib olish va sinxronizatsiya qilish</p>
      </div>
      
      <div class="flex flex-wrap gap-3">
        <button 
          @click="sync('machines')" 
          :disabled="loading"
          class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-xl transition-all disabled:opacity-50 active:scale-95 font-bold text-xs uppercase shadow-lg shadow-blue-500/30"
        >
          <span v-if="loading && currentSync === 'machines'">Yuklanmoqda...</span>
          <span v-else>🚜 Texnikalar</span>
        </button>

        <button 
          @click="sync('groups')" 
          :disabled="loading"
          class="flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white px-5 py-3 rounded-xl transition-all disabled:opacity-50 active:scale-95 font-bold text-xs uppercase shadow-lg shadow-purple-500/30"
        >
          <span v-if="loading && currentSync === 'groups'">Yuklanmoqda...</span>
          <span v-else>📂 Guruhlar</span>
        </button>

        <button 
          @click="sync('geozones')" 
          :disabled="loading"
          class="flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white px-5 py-3 rounded-xl transition-all disabled:opacity-50 active:scale-95 font-bold text-xs uppercase shadow-lg shadow-teal-500/30"
        >
          <span v-if="loading && currentSync === 'geozones'">Yuklanmoqda...</span>
          <span v-else>📍 Geozonalar</span>
        </button>
      </div>
    </div>

    <div v-if="message" 
      :class="isError 
        ? 'text-red-700 bg-red-50 border-red-200 dark:text-red-400 dark:border-red-500/20 dark:bg-red-500/10' 
        : 'text-green-700 bg-green-50 border-green-200 dark:text-green-400 dark:border-green-500/20 dark:bg-green-500/10'" 
      class="p-4 border rounded-xl text-sm font-mono whitespace-pre-wrap transition-colors duration-300 shadow-inner">
      {{ message }}
    </div>

    <BaseCrud :config="wialonConfig" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue'; 

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
    // Qaysi tugma bosilganiga qarab URL ni tanlaymiz
    let url = '';
    if (type === 'machines') url = '/api/wialon/sync-machines';
    else if (type === 'groups') url = '/api/wialon/sync-groups';
    else if (type === 'geozones') url = '/api/wialon/sync-geozones'; // <--- YANGI URL
      
    const res = await axios.post(url);
    message.value = "✅ " + res.data.message + "\n" + (res.data.details || '');
  } catch (error) {
    isError.value = true;
    message.value = "❌ Xatolik yuz berdi: " + (error.response?.data?.message || error.message);
  } finally {
    loading.value = false;
    currentSync.value = '';
  }
};

const wialonConfig = {
  title: 'Wialon Integratsiyasi',
  slug: 'wialon_settings',
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'base_url', label: 'API URL', type: 'text', placeholder: 'https://hst-api.wialon.com' },
    { key: 'token', label: 'Wialon Token (Hash)', type: 'textarea', placeholder: 'Tokenni bu yerga joylang...' },
    { key: 'username', label: 'Login (Ixtiyoriy)', type: 'text', placeholder: 'Wialon user' },
    { key: 'password', label: 'Parol (Ixtiyoriy)', type: 'password', placeholder: '******' },
    { key: 'resource_id', label: 'Resource ID', type: 'text', placeholder: 'Masalan: 10453' },
    { key: 'status', label: 'Holat', type: 'select', options: [
      { value: 'active', label: 'Faol' },
      { value: 'inactive', label: 'Nofaol' }
    ]}
  ]
};
</script>