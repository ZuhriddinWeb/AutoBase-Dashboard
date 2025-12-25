<template>
  <!-- ISH JARAYONIDA -->
  <section class="col-span-3">
    <StatusRing title="Ish jarayonida" :count="53" color="green" />
    <MachineGrid color="green" />
  </section>

  <!-- YOQILG‘I -->
  <section class="col-span-3">
    <StatusRing title="Yoqilg‘i maydonida" :count="17" color="orange" />
    <MachineGrid color="orange" />
  </section>

  <!-- SMENA -->
  <section class="col-span-3">
    <StatusRing title="Smena almashish" :count="3" color="blue" />
    <MachineGrid color="blue" />
  </section>

  <!-- O‘NG PANEL -->
  <section class="col-span-3">
    <RightStats />
  </section>
</template>

<script setup>
import StatusRing from '../components/StatusRing.vue'
import MachineGrid from '../components/MachineGrid.vue'
import RightStats from '../components/RightStats.vue'
</script>
