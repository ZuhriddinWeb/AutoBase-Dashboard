<template>
  <BaseCrud :config="config" />
</template>

<script setup>
import BaseCrud from '../../components/BaseCrud.vue';

const config = {
  title: 'Wialon Geozonalari',
  
  // ENG MUHIMI: UniversalController buni "GeozoneGroup" modeliga aylantirib beradi
  slug: 'geozone_groups', 
  
  fields: [
    { key: 'id', label: 'ID', type: 'text', readonly: true },
    { key: 'wialon_id', label: 'Wialon ID', type: 'text', readonly: true },
    { key: 'name', label: 'Geozona Nomi', type: 'text', placeholder: 'Karyer yoki obyekt nomi' },
    { key: 'description', label: 'Tavsifi', type: 'text', placeholder: 'Qo\'shimcha ma\'lumot...' },
    
    // Wialon turlari: 1-Chiziq, 2-Ko'pburchak, 3-Aylana
    { key: 'type', label: 'Hudud Turi', type: 'select', options: [
       { value: 1, label: 'Chiziq (Yo\'l)' },
       { value: 2, label: 'Ko\'pburchak (Maydon)' },
       { value: 3, label: 'Aylana (Doira)' }
    ]},

    // Markaziy nuqtalarni ko'rsatish (Ixtiyoriy)
    { key: 'cen_x', label: 'Markaz (X)', type: 'text', readonly: true },
    { key: 'cen_y', label: 'Markaz (Y)', type: 'text', readonly: true }
  ]
};
</script>