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
<style>
/* --- GLOBAL SCROLLBAR STYLES --- */

/* 1. Webkit (Chrome, Edge, Safari, Opera) uchun sozlamalar */
::-webkit-scrollbar {
    width: 8px;  /* Vertikal scroll qalinligi */
    height: 8px; /* Gorizontal scroll qalinligi */
}

/* Scroll yo'lagi (orqa fon) */
::-webkit-scrollbar-track {
    background-color: transparent; /* Orqa fonni shaffof qilamiz */
    /* Yoki aniq rang: background-color: #0f1115; */
}

/* Scroll tayoqchasi (Thumb) */
::-webkit-scrollbar-thumb {
    background-color: #374151; /* Gray-700 (Tinch holatda) */
    border-radius: 10px;       /* Yumaloq burchaklar */
    border: 2px solid transparent; /* Chekkasidan ozgina joy tashlash */
    background-clip: content-box;  /* Border hisobiga kichraytirish */
}

/* Sichqoncha borganda (Hover) - Sizning Cyan rangingizda */
::-webkit-scrollbar-thumb:hover {
    background-color: #06b6d4; /* Cyan-500 */
}

/* 2. Firefox uchun sozlamalar */
* {
    scrollbar-width: thin; /* Ingichka scroll */
    scrollbar-color: #374151 transparent; /* Tayoqcha rangi va Fon rangi */
}

/* Dark mode uchun maxsus (ixtiyoriy, agar tizim to'liq dark bo'lsa yuqoridagisi yetadi) */
.dark ::-webkit-scrollbar-thumb {
    background-color: #4b5563; /* Gray-600 */
}
.dark ::-webkit-scrollbar-thumb:hover {
    background-color: #06b6d4; /* Cyan hover */
}
</style>