<template>
  <BaseCrud :config="config" />
</template>

<script setup>
import BaseCrud from '../../components/BaseCrud.vue';

const config = {
  title: 'Texnikalar Parki (Wialon)',
  slug: 'machines', // API: /api/crud/machines
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    
    // Wialon ID va IMEI o'zgarmas bo'lishi kerak, shuning uchun readonly
    { key: 'wialon_id', label: 'Wialon ID', type: 'text', readonly: true },
    
    // Nomi (Sinxronizatsiya paytida yangilanadi)
    { key: 'name', label: 'Texnika Nomi', type: 'text', placeholder: 'Texnika nomi' },
    
    { key: 'imei', label: 'IMEI / UID', type: 'text', readonly: true },
    
    // Qachon yangilangani (Monitoring uchun muhim)
    { key: 'updated_at', label: 'Oxirgi yangilanish', type: 'text', readonly: true }
  ]
};
</script>