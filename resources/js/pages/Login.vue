<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f1115] relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-96 h-96 bg-cyan-500/20 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full blur-[100px] translate-x-1/2 translate-y-1/2"></div>

    <div class="w-full max-w-md bg-[#1a1d22]/80 backdrop-blur-xl p-8 rounded-[2rem] border border-gray-800 shadow-[0_0_50px_rgba(0,0,0,0.5)] relative z-10">
      
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-tr from-cyan-500 to-blue-600 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-lg shadow-cyan-500/30">
          <span class="text-3xl">🚀</span>
        </div>
        <h1 class="text-2xl font-black text-white uppercase tracking-tighter">Tizimga Kirish</h1>
        <p class="text-gray-500 text-sm mt-2">AutoBase Boshqaruv Paneli</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        
        <div class="space-y-2">
          <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Login</label>
          <div class="relative group">
            <input 
              v-model="form.login" 
              type="text" 
              placeholder="admin"
              class="w-full bg-[#0f1115] border border-gray-800 text-white px-5 py-4 rounded-xl focus:border-cyan-500 focus:shadow-[0_0_15px_rgba(6,182,212,0.15)] outline-none transition-all pl-12"
              required
            >
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-cyan-500 transition-colors">
              👤
            </span>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1">Parol</label>
          <div class="relative group">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="••••••••"
              class="w-full bg-[#0f1115] border border-gray-800 text-white px-5 py-4 rounded-xl focus:border-cyan-500 focus:shadow-[0_0_15px_rgba(6,182,212,0.15)] outline-none transition-all pl-12"
              required
            >
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-cyan-500 transition-colors">
              🔒
            </span>
            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
              {{ showPassword ? '👁️' : '👁️‍🗨️' }}
            </button>
          </div>
        </div>

        <div v-if="errorMessage" class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs text-center font-bold">
          {{ errorMessage }}
        </div>

        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-cyan-500 hover:bg-cyan-400 text-black font-black py-4 rounded-xl shadow-[0_0_20px_rgba(6,182,212,0.3)] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider text-sm"
        >
          <span v-if="loading">Kirilmoqda...</span>
          <span v-else>Kirish -></span>
        </button>

      </form>

      <div class="mt-8 text-center text-xs text-gray-600">
        &copy; 2026 AutoBase System. Himoyalangan.
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({ login: '', password: '' });
const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';

  try {
    // 1. API so'rov yuborish
    // Eslatma: Agar Laravelingiz 8000 portda bo'lsa baseURL to'g'ri ekaniga ishonch hosil qiling
    const response = await axios.post('http://127.0.0.1:8000/api/login', form.value);

    // 2. Ma'lumotlarni saqlash (Browser Xotirasiga)
    localStorage.setItem('token', response.data.access_token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    // --- ENG MUHIM QISM (Rollar va Ruxsatlar) ---
    localStorage.setItem('roles', JSON.stringify(response.data.roles));
    localStorage.setItem('permissions', JSON.stringify(response.data.permissions));

    // 3. Axios sarlavhalarini yangilash
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;

    // 4. Panelga yo'naltirish
    router.push('/admin/dashboard');

  } catch (error) {
    console.error(error);
    if (error.response && error.response.status === 401) {
      errorMessage.value = "Login yoki parol noto'g'ri!";
    } else {
      errorMessage.value = "Serverda xatolik yuz berdi. Keyinroq urinib ko'ring.";
    }
  } finally {
    loading.value = false;
  }
};
</script>