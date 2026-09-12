<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const progress = ref(0);

function update() {
    const height = document.documentElement.scrollHeight - window.innerHeight;
    progress.value = height > 0 ? Math.min(100, (window.scrollY / height) * 100) : 0;
}

onMounted(() => {
    update();
    window.addEventListener('scroll', update, { passive: true });
});

onBeforeUnmount(() => window.removeEventListener('scroll', update));
</script>

<template>
    <div class="pointer-events-none fixed inset-x-0 top-0 z-[60] h-0.5 print:hidden">
        <div class="h-full bg-primary transition-[width] duration-150" :style="{ width: `${progress}%` }" />
    </div>
</template>
