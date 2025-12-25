<template>
    <div class="rounded-lg w-16 h-8 flex flex-col items-center justify-center text-xs font-bold" :class="bgClass">
        <span>{{ label }}</span>
        <span class="text-[10px] opacity-70">{{ value }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: String,
    value: Number,
    color: String,
})

const bgClass = computed(() => {
    return {
        green: 'bg-green-500 text-black',
        orange: 'bg-orange-500 text-black',
        blue: 'bg-blue-500 text-black',
        red: 'bg-red-500 text-black',
        gray: 'bg-gray-600 text-white',
    }[props.color] || 'bg-gray-700 text-white'
})
</script>
