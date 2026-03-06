<template>
  <BaseCrud v-if="isReady" :config="config" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BaseCrud from '../../components/BaseCrud.vue';

const isReady = ref(false);

const config = ref({
  title: 'Transport Guruhlari',
  slug: 'transport_groups', 
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'wialon_id', label: 'Wialon ID', type: 'text', readonly: true, placeholder: 'Avtomatik' },
    { key: 'name', label: 'Guruh Nomi', type: 'text', placeholder: 'Yangi guruh nomi...' },
    
    // --- YANGI: Mashinalarni tanlash qismi ---
   { key: 'machines', label: 'Biriktirilgan Texnikalar', type: 'checkbox-group', options: [] }
    // ----------------------------------------
  ]
});

onMounted(async () => {
  try {
    // 1. Barcha mashinalar ro'yxatini olamiz
    // Eslatma: '/api/crud/machines' o'rniga o'zingizdagi mashinalar API manzilini qo'ying
    const response = await axios.get('/api/crud/machines'); 
    
    const machinesData = response.data.data ? response.data.data : response.data;

    // 2. Ularni checkbox uchun kerakli formatga o'tkazamiz
    const machineOptions = machinesData.map(m => ({
      value: m.id,       // Bazadagi ID
      label: m.name || m.nm // Mashina nomi (bazada qanday nomlangan bo'lsa)
    }));

    // 3. Config ichidagi 'machines' maydoniga optionlarni joylaymiz
    const machinesField = config.value.fields.find(f => f.key === 'machines');
    if (machinesField) {
      machinesField.options = machineOptions;
    }

    isReady.value = true;
  } catch (error) {
    console.error("Mashinalarni yuklashda xatolik:", error);
    isReady.value = true; // Xato bo'lsa ham formani ochamiz (mashinasiz)
  }
});
</script>