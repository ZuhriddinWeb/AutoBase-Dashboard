<template>
  <BaseCrud :config="config" />
</template>

<script setup>
import BaseCrud from '../../components/BaseCrud.vue'; // BaseCrud yo'lini to'g'irlang

const config = {
  title: 'Transport Guruhlari (Wialon)',
  slug: 'transport_groups', // API: /api/crud/transport_groups
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    // Wialon ID ni o'zgartirib bo'lmaydi, chunki u sinxronizatsiyada kalit hisoblanadi
    { key: 'wialon_id', label: 'Wialon ID', type: 'text', readonly: true }, 
    { key: 'name', label: 'Guruh Nomi', type: 'text', placeholder: 'Guruh nomi' },
    // Qo'shimcha ustunlar (ixtiyoriy)
    { key: 'created_at', label: 'Yaratilgan vaqt', type: 'text', readonly: true } 
  ]
};
</script>