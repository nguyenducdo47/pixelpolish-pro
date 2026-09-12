<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const visible = ref(false);

function update() {
    visible.value = window.scrollY > 400;
}

function toTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    update();
    window.addEventListener('scroll', update, { passive: true });
});

onBeforeUnmount(() => window.removeEventListener('scroll', update));
</script>

<template>
    <button
        v-show="visible"
        type="button"
        class="fixed right-4 bottom-4 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-glow print:hidden"
        aria-label="Scroll to top"
        @click="toTop"
    >
        ↑
    </button>
</template>
