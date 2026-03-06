<template>
  <div class="bg-white dark:bg-[#1a1d22] rounded-3xl border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden transition-colors duration-300">
    
    <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-[#1e2228]/50 backdrop-blur-md">
      <div class="flex items-center gap-3">
        <div class="w-2 h-8 bg-cyan-500 rounded-full shadow-[0_0_15px_rgba(6,182,212,0.5)]"></div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ config.title }}</h2>
      </div>

      <button v-if="$can(config.slug + '_create')" @click="openModal()"
        class="bg-cyan-500 hover:bg-cyan-400 text-black font-black px-6 py-2.5 rounded-2xl transition-all shadow-[0_0_20px_rgba(6,182,212,0.3)] active:scale-95">
        + Yangi qo'shish
      </button>
    </div>

    <div class="overflow-x-auto bg-gray-50 dark:bg-[#0f1115]">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="text-[10px] uppercase tracking-[2px] text-gray-500 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-[#16191d]">
            <th v-for="col in config.fields" :key="col.key" class="p-5 font-black">{{ col.label }}</th>
            <th class="p-5 text-right font-black">Amallar</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800/50">
          <tr v-for="item in items" :key="item.id" class="hover:bg-cyan-500/5 transition-all group">
            <td v-for="col in config.fields" :key="col.key" class="p-5 text-sm font-medium text-gray-700 dark:text-gray-300">

              <span v-if="col.key === 'status'"
                :class="item[col.key] === 'active' ? 'text-green-600 bg-green-100 border-green-200 dark:text-green-400 dark:bg-green-400/10 dark:border-green-400/20' : 'text-red-600 bg-red-100 border-red-200 dark:text-red-400 dark:bg-red-400/10 dark:border-red-400/20'"
                class="px-3 py-1 rounded-lg text-[10px] font-black border uppercase tracking-wider">
                {{ item[col.key] }}
              </span>

              <span v-else-if="col.key === 'id'" class="text-cyan-600 dark:text-cyan-500/50 font-mono">#{{ item[col.key] }}</span>

              <span v-else-if="col.type === 'textarea'" class="line-clamp-1 max-w-[200px] text-gray-500 italic">
                {{ item[col.key] }}
              </span>

              <span v-else-if="col.type === 'checkbox-group'">
                <div class="flex flex-wrap gap-1">
                  <template v-if="item[col.key] && item[col.key].length">
                    <span v-for="group in item[col.key]" :key="group.id"
                      class="bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-500/30 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider whitespace-nowrap">
                      {{ group.name || group }}
                    </span>
                  </template>
                  <span v-else class="text-gray-400 dark:text-gray-600 text-xs italic">-</span>
                </div>
              </span>

              <div v-else-if="col.type === 'color-picker'">
                <span v-if="item[col.key]" 
                  class="px-3 py-1 rounded-lg border font-bold text-[10px] uppercase tracking-wider inline-flex items-center gap-2"
                  :style="{
                    borderColor: item[col.key], 
                    color: item[col.key], 
                    backgroundColor: item[col.key] + '15' 
                  }"
                >
                  <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: item[col.key] }"></span>
                  {{ col.options?.find(o => o.value === item[col.key])?.label || item[col.key] }}
                </span>
                <span v-else class="text-gray-400 dark:text-gray-600 text-xs">-</span>
              </div>

              <span v-else>{{ item[col.key] }}</span>

            </td>
            
            <td class="p-5 text-right space-x-3 whitespace-nowrap">
              <button v-if="$can(config.slug + '_edit')" @click="openModal(item)"
                class="text-gray-400 hover:text-cyan-600 dark:text-gray-500 dark:hover:text-cyan-400 transition-all transform hover:scale-125">✏️</button>
              <button v-if="$can(config.slug + '_delete')" @click="confirmDelete(item)"
                class="text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-500 transition-all transform hover:scale-125">🗑</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 dark:bg-black/80 backdrop-blur-md">
      <div
        class="bg-white dark:bg-[#1a1d22] w-full max-w-2xl rounded-[2.5rem] shadow-2xl dark:shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-gray-200 dark:border-gray-800 overflow-hidden scale-in-center">
        
        <div class="p-8 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#1e2228] flex justify-between items-center">
          <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ form.id ? 'Tahrirlash' : 'Yangi Ma\'lumot' }}</h3>
          <button @click="showModal = false" class="text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors text-2xl">✕</button>
        </div>

        <form @submit.prevent="save" class="p-8 space-y-5 bg-white dark:bg-[#0f1115] max-h-[70vh] overflow-y-auto custom-scrollbar">
          <div v-for="field in config.fields" :key="field.key">
            <template v-if="!field.readonly">
              <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-[2px]">{{ field.label }}</label>

              <div v-if="field.type === 'select'" class="space-y-2">
                <div v-if="!field.options || field.options.length === 0" class="text-gray-500 italic text-sm p-2">
                  Ma'lumot yo'q
                </div>

                <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto custom-scrollbar p-1">
                  <div v-for="opt in field.options" :key="opt.value" @click="form[field.key] = opt.value" :class="[
                    'relative flex items-center justify-between p-4 rounded-xl border cursor-pointer transition-all duration-300 group select-none',
                    form[field.key] === opt.value
                      ? getActiveColor(opt.value) + ' scale-[1.02]' 
                      : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-[#1a1d22] dark:border-gray-800 dark:text-gray-500 dark:hover:border-gray-600 dark:hover:bg-[#252930]'
                  ]">
                    <span class="font-bold text-xs uppercase tracking-wide" :class="form[field.key] !== opt.value ? 'text-gray-600 dark:text-gray-500' : ''">{{ opt.label }}</span>

                    <div :class="[
                      'w-5 h-5 rounded-full flex items-center justify-center border transition-all duration-300',
                      form[field.key] === opt.value 
                        ? getCircleColor(opt.value) 
                        : 'border-gray-300 dark:border-gray-600 group-hover:border-gray-400'
                    ]">
                      <svg v-if="form[field.key] === opt.value" xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3 text-white dark:text-black font-bold" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                          clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else-if="field.type === 'checkbox-group'" class="space-y-3">
                
                <div class="relative">
                   <input 
                    type="text" 
                    v-model="searchQueries[field.key]" 
                    placeholder="Qidirish..." 
                    class="w-full bg-gray-100 dark:bg-[#1a1d22] border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-xs focus:border-cyan-500 outline-none transition-all pl-8"
                  >
                  <span class="absolute left-2.5 top-2 text-gray-400">🔍</span>
                </div>

                <div v-if="getFilteredOptions(field).length === 0" class="text-gray-500 italic text-sm p-2 text-center border border-dashed rounded-lg">
                  Hech narsa topilmadi
                </div>

                <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto custom-scrollbar p-1">
                  <div v-for="opt in getFilteredOptions(field)" :key="opt.value" @click="toggleCheckbox(field.key, opt.value)"
                    :class="[
                      'relative flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all duration-200 group select-none',
                      form[field.key] && form[field.key].includes(opt.value)
                        ? 'bg-cyan-50 border-cyan-500 text-cyan-700 shadow-sm dark:bg-cyan-500/10 dark:text-cyan-400 dark:shadow-[0_0_15px_rgba(6,182,212,0.15)] scale-[1.01]'
                        : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-[#1a1d22] dark:border-gray-800 dark:text-gray-500 dark:hover:border-gray-600 dark:hover:bg-[#252930]'
                    ]">
                    <span class="font-bold text-xs uppercase tracking-wide truncate pr-2" :class="!(form[field.key] && form[field.key].includes(opt.value)) ? 'text-gray-600 dark:text-gray-500' : ''">{{ opt.label }}</span>
                    
                    <div :class="[
                      'w-4 h-4 rounded flex items-center justify-center border transition-all duration-300 flex-shrink-0',
                      form[field.key] && form[field.key].includes(opt.value) 
                        ? 'bg-cyan-500 border-cyan-500' 
                        : 'border-gray-300 dark:border-gray-600 group-hover:border-gray-400'
                    ]">
                      <svg v-if="form[field.key] && form[field.key].includes(opt.value)"
                        xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white dark:text-black font-bold" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else-if="field.type === 'color-picker'" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div v-for="opt in field.options" :key="opt.value"
                  @click="form[field.key] = opt.value"
                  class="relative p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 flex items-center justify-between group overflow-hidden select-none"
                  :class="form[field.key] === opt.value ? 'scale-[1.02]' : 'hover:border-gray-400 dark:hover:border-gray-600 bg-gray-50 border-gray-200 dark:bg-[#1a1d22] dark:border-gray-800'"
                  :style="form[field.key] === opt.value ? `border-color: ${opt.value}; background-color: ${opt.value}15;` : ''"
                >
                  <span class="font-bold text-sm uppercase tracking-wider"
                    :style="form[field.key] === opt.value ? `color: ${opt.value}` : 'color: #9ca3af'">
                    {{ opt.label }}
                  </span>
                  
                  <div class="w-7 h-7 rounded-full flex items-center justify-center transition-all shadow-lg"
                    :style="{ backgroundColor: form[field.key] === opt.value ? opt.value : '#e5e7eb' /* gray-200 */, border: '1px solid transparent' }">
                    <svg v-if="form[field.key] === opt.value" class="w-4 h-4 text-white dark:text-black font-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
              </div>

              <textarea v-else-if="field.type === 'textarea'" v-model="form[field.key]" :placeholder="field.placeholder"
                rows="2" class="w-full bg-gray-50 border border-gray-300 text-gray-900 dark:bg-[#1a1d22] dark:border-gray-800 dark:text-white p-4 rounded-2xl focus:border-cyan-500 dark:focus:border-cyan-500 outline-none transition-all resize-none custom-scrollbar">
              </textarea>

              <input v-else v-model="form[field.key]" :type="field.type" :placeholder="field.placeholder"
                class="w-full bg-gray-50 border border-gray-300 text-gray-900 dark:bg-[#1a1d22] dark:border-gray-800 dark:text-white p-4 rounded-2xl focus:border-cyan-500 dark:focus:border-cyan-500 outline-none transition-all">
            </template>
          </div>

          <div class="flex justify-end gap-4 mt-8 sticky bottom-0 bg-white dark:bg-[#0f1115] py-2 border-t border-gray-200 dark:border-gray-800/50 pt-4">
            <button type="button" @click="showModal = false" class="px-6 py-3 text-gray-500 font-bold uppercase text-xs hover:text-gray-900 dark:hover:text-white transition-colors">Bekor qilish</button>
            <button type="submit" class="px-10 py-3 bg-cyan-500 text-black font-black rounded-2xl hover:bg-cyan-400 shadow-lg shadow-cyan-500/20 uppercase text-xs transition-transform active:scale-95">Saqlash</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showConfirm" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/50 dark:bg-black/90 backdrop-blur-sm">
      <div class="bg-white dark:bg-[#1a1d22] w-full max-w-sm rounded-[2rem] border border-red-200 dark:border-red-500/30 p-8 text-center shadow-xl dark:shadow-[0_0_50px_rgba(239,68,68,0.2)]">
        <div class="w-20 h-20 bg-red-100 dark:bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-red-200 dark:border-red-500/20">
          <span class="text-4xl text-red-500">⚠️</span>
        </div>
        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tighter">O'chirishni tasdiqlaysizmi?</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">Siz ma'lumotlarni butunlay o'chirib tashlamoqchisiz.</p>
        <div class="flex gap-4">
          <button @click="showConfirm = false" class="flex-1 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-800 dark:text-white font-bold text-xs uppercase transition-all dark:hover:bg-gray-700">Yo'q, qolsin</button>
          <button @click="remove" class="flex-1 py-3 rounded-xl bg-red-500 text-white font-bold text-xs uppercase transition-all hover:bg-red-600 shadow-lg shadow-red-500/20">Ha, o'chirilsin</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps(['config']);
const items = ref([]);
const showModal = ref(false);
const showConfirm = ref(false);
const itemToDelete = ref(null);
const form = ref({});
const searchQueries = ref({});

// Ranglar funksiyasi (Dark/Light modega moslandi)
const getActiveColor = (value) => {
  if (value === 'active') {
    // YASHIL (Faol)
    return 'bg-green-100 border-green-500 text-green-700 dark:bg-green-500/10 dark:text-green-400 shadow-sm dark:shadow-[0_0_15px_rgba(34,197,94,0.15)]';
  } 
  if (value === 'inactive') {
    // QIZIL (Nofaol)
    return 'bg-red-100 border-red-500 text-red-700 dark:bg-red-500/10 dark:text-red-400 shadow-sm dark:shadow-[0_0_15px_rgba(239,68,68,0.15)]';
  }
  // Odatiy (MOVIY)
  return 'bg-cyan-100 border-cyan-500 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400 shadow-sm dark:shadow-[0_0_15px_rgba(6,182,212,0.15)]';
};

const getCircleColor = (value) => {
  if (value === 'active') return 'bg-green-500 border-green-500';
  if (value === 'inactive') return 'bg-red-500 border-red-500';
  return 'bg-cyan-500 border-cyan-500';
};
const getFilteredOptions = (field) => {
  const query = searchQueries.value[field.key]?.toLowerCase() || '';
  
  if (!query) return field.options;

  return field.options.filter(opt => 
    opt.label.toLowerCase().includes(query)
  );
};
const fetchItems = async () => {
  try {
    const res = await axios.get(`/api/crud/${props.config.slug}`);
    items.value = res.data.data ? res.data.data : res.data;
  } catch (err) { console.error("Xato:", err); }
};

const openModal = (item = null) => {
  if (item) {
    let data = JSON.parse(JSON.stringify(item));
    props.config.fields.forEach(field => {
      if (field.type === 'checkbox-group' && Array.isArray(data[field.key])) {
        if (data[field.key].length > 0 && typeof data[field.key][0] === 'object') {
          data[field.key] = data[field.key].map(obj => obj.id);
        }
      }
    });
    form.value = data;
  } else {
    form.value = {};
    props.config.fields.forEach(field => {
      if (field.type === 'checkbox-group') {
        form.value[field.key] = [];
      } else {
        form.value[field.key] = '';
      }
    });
  }
  showModal.value = true;
};

const toggleCheckbox = (key, value) => {
  if (!Array.isArray(form.value[key])) {
    form.value[key] = [];
  }
  const index = form.value[key].indexOf(value);
  if (index === -1) {
    form.value[key].push(value);
  } else {
    form.value[key].splice(index, 1);
  }
};

const save = async () => {
  try {
    const url = `/api/crud/${props.config.slug}`;
    if (form.value.id) {
      await axios.put(`${url}/${form.value.id}`, form.value);
    } else {
      await axios.post(url, form.value);
    }
    showModal.value = false;
    fetchItems();
  } catch (err) {
    alert("Xatolik yuz berdi: " + (err.response?.data?.message || "Server xatosi"));
  }
};

const confirmDelete = (item) => {
  itemToDelete.value = item;
  showConfirm.value = true;
};

const remove = async () => {
  try {
    await axios.delete(`/api/crud/${props.config.slug}/${itemToDelete.value.id}`);
    showConfirm.value = false;
    fetchItems();
  } catch (err) { alert("O'chirishda xatolik!"); }
};

onMounted(() => {
  fetchItems();
});
</script>

<style scoped>
.scale-in-center { animation: scale-in-center 0.3s cubic-bezier(0.250, 0.460, 0.450, 0.940) both; }
@keyframes scale-in-center { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

/* Light/Dark mode scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #06b6d4; }

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>