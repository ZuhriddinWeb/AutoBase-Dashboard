<template>
  <div class="min-h-screen bg-[#0f1115] text-gray-200">
    <!-- TOP BAR -->
     <Sidebar />
    <header class="flex items-center justify-between px-4 h-12 bg-[#1a1d22] border-b border-gray-700">
      <TopTabs />
      <div class="flex gap-4 text-sm">
        <span>Minut <b>1</b></span>
        <span>Soat <b>1</b></span>
        <span>Kun <b>1</b></span>
        <span class="text-cyan-400 cursor-pointer">Kirish</span>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="grid grid-cols-12 gap-4 p-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
// import TopTabs from '../components/TopTabs.vue'
</script>
