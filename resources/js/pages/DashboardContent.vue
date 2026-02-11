<template>
  <div class="grid grid-cols-12 gap-4">
    <section class="col-span-3">
      <StatusRing title="Ish jarayonida" :count="53" color="green" />
      <MachineGrid color="green" />
    </section>

    <section class="col-span-3">
      <StatusRing title="Yoqilg‘i maydonida" :count="17" color="orange" />
      <MachineGrid color="orange" />
    </section>

    <section class="col-span-3">
      <StatusRing title="Smena almashish" :count="3" color="blue" />
      <MachineGrid color="blue" />
    </section>

    <section class="col-span-3 text-white">
      <RightStats />
    </section>
  </div>
</template>

<script setup>
import StatusRing from '../components/StatusRing.vue'
import MachineGrid from '../components/MachineGrid.vue'
import RightStats from '../components/RightStats.vue'
</script>