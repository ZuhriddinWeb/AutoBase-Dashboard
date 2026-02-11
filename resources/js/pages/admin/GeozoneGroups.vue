<template>
  <BaseCrud :config="config" />
</template>

<script setup>
import BaseCrud from '../../components/BaseCrud.vue';

const config = {
  title: 'Geozona Guruhlari (Wialon)',
  slug: 'geozone_groups', // API: /api/crud/geozone_groups
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'wialon_id', label: 'Resource ID', type: 'text', readonly: true },
    { key: 'name', label: 'Geozona Guruhi Nomi', type: 'text', placeholder: 'Nomi' },
    { key: 'created_at', label: 'Sinxronizatsiya vaqti', type: 'text', readonly: true }
  ]
};
</script>