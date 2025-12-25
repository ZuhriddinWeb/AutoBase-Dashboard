<template>
  <div class="flex flex-col items-center mb-4">
    <div
      class="w-16 h-16 rounded-full flex items-center justify-center border-4"
      :class="borderColor"
    >
      <span class="font-bold">{{ count }}</span>
    </div>
    <span class="mt-2 text-xs uppercase tracking-wide">
      {{ title }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  count: Number,
  color: String,
})

const borderColor = computed(() => {
  return {
    green: 'border-green-500 text-green-400',
    orange: 'border-orange-500 text-orange-400',
    blue: 'border-blue-500 text-blue-400',
    red: 'border-red-500 text-red-400',
    gray: 'border-gray-500 text-gray-400',
  }[props.color] || 'border-gray-600 text-gray-400'
})
</script>
