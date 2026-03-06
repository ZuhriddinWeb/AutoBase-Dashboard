<template>
  <div class="grid grid-cols-4 gap-2">
    <MachineCard
      v-for="n in 52"
      :key="n"
      :label="`C${480 + n}`"
      :value="Math.floor(Math.random() * 40)"
      :color="color"
    />
  </div>
</template>

<script setup>
import MachineCard from './MachineCard.vue'
defineProps({ color: String })
</script>