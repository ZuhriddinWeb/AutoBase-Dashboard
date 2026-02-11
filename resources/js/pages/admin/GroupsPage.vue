<template>
  <div>
    <BaseCrud :config="groupConfig" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue';

// 1. Config endi "ref" ichida (chunki ichidagi options o'zgaradi)
const groupConfig = ref({
  title: 'Guruhlar Boshqaruvi',
  slug: 'groups',
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'name', label: 'Guruh nomi', type: 'text', placeholder: 'Guruh nomini kiriting' },
    { key: 'description', label: 'Tavsif', type: 'textarea', placeholder: 'Guruh haqida qisqacha matn...' },
    
    // RANG TANLASH (Color Picker)
    { 
      key: 'color', 
      label: 'Guruh Rangi', 
      type: 'color-picker', 
      options: [
        { value: '#ef4444', label: 'Qizil' },
        { value: '#f97316', label: 'Apelsin' },
        { value: '#eab308', label: 'Sariq' },
        { value: '#22c55e', label: 'Yashil' },
        { value: '#06b6d4', label: 'Moviy' },
        { value: '#3b82f6', label: 'Ko\'k' },
        { value: '#a855f7', label: 'Binafsha' },
        { value: '#ec4899', label: 'Pushti' },
      ]
    },

    // YANGI: TRANSPORT GURUHLARINI BIRIKTIRISH
    {
      key: 'transport_groups', // Backenddagi relation nomi (belongsToMany)
      label: 'Transport Guruhlari',
      type: 'checkbox-group', // Ko'p tanlash mumkin bo'ladi
      options: [] // <--- Boshida bo'sh, API dan kelgach to'ladi
    },

    { key: 'status', label: 'Holati', type: 'select', options: [
      { value: 'active', label: 'Faol' },
      { value: 'inactive', label: 'Nofaol' }
    ]}
  ]
});

// 2. Transport guruhlarini API dan yuklash funksiyasi
const fetchTransportGroups = async () => {
  try {
    // API manzili (Transport guruhlari ro'yxati)
    const response = await axios.get('/api/crud/transport_groups');
    const data = response.data.data || response.data;

    // Kelgan ma'lumotni { value, label } formatiga o'tkazamiz
    const options = data.map(item => ({
      value: item.id,
      label: item.name
    }));

    // Config ichidagi 'transport_groups' maydonini topib, options ni yangilaymiz
    const field = groupConfig.value.fields.find(f => f.key === 'transport_groups');
    if (field) {
      field.options = options;
    }

  } catch (error) {
    console.error("Transport guruhlarini yuklashda xatolik:", error);
  }
};

// 3. Sahifa yuklanganda ishga tushirish
onMounted(() => {
  fetchTransportGroups();
});
</script>