<template>
  <div>
    <CrudEngine :config="currentConfig" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import CrudEngine from '../../components/CrudEngine.vue';

const route = useRoute();

// Dinamik konfiguratsiya
const configs = {
  users: {
    title: 'Foydalanuvchilar',
    slug: 'users',
    fields: [
      { key: 'name', label: 'F.I.SH' },
      { key: 'email', label: 'Email', type: 'email' },
      { key: 'role', label: 'Lavozimi' }
    ]
  },
  machines: {
    title: 'Texnikalar Ro‘yxati',
    slug: 'machines',
    fields: [
      { key: 'code', label: 'Texnika kodi' },
      { key: 'type', label: 'Turi' },
      { key: 'status', label: 'Holati' }
    ]
  }
};

const currentConfig = computed(() => {
  return configs[route.params.resource] || configs.users;
});
</script>