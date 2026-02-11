<template>
  <div>
    <BaseCrud v-if="isConfigReady" :config="pageConfig" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue';

const isConfigReady = ref(false);

const pageConfig = ref({
  title: 'Sahifalar Boshqaruvi',
  slug: 'pages',
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'title', label: 'Sarlavha', type: 'text' },
    
    // --- MANA BU YERGA SLUG QAYTARILDI ---
    { 
      key: 'slug', 
      label: 'URL (Slug)', 
      type: 'text', 
      placeholder: 'Bo\'sh qoldirsangiz, avtomatik yaratiladi' 
    },
    // -------------------------------------
    
    // --- GURUHLAR QISMI ---
    { 
      key: 'groups', 
      label: 'Guruhlarni biriktirish', 
      type: 'checkbox-group',
      options: [] 
    },
    // ---------------------

    { key: 'content', label: 'Matn', type: 'textarea' },
    { key: 'status', label: 'Holat', type: 'select', options: [
       { value: 'active', label: 'Faol' },
       { value: 'inactive', label: 'Nofaol' }
    ]}
  ]
});

onMounted(async () => {
  try {
    const response = await axios.get('/api/crud/groups');
    
    // API javobini to'g'ri olish
    const rawData = response.data.data ? response.data.data : response.data;

    if (Array.isArray(rawData)) {
      const groupOptions = rawData.map(group => ({
        value: group.id,
        label: group.name
      }));

      const groupField = pageConfig.value.fields.find(f => f.key === 'groups');
      
      if (groupField) {
        groupField.options = groupOptions;
      }
    } 

    isConfigReady.value = true;
  } catch (error) {
    console.error("Xato:", error);
    isConfigReady.value = true;
  }
});
</script>