<!-- <template>
  <DashboardLayout>
    <Dashboard />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from './layouts/DashboardLayout.vue'
import Dashboard from './pages/Dashboard.vue'
</script> -->
<template>
  <router-view />
</template>

<script setup>
import axios from 'axios';
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
// Bu yerda ortiqcha importlar shart emas
</script>