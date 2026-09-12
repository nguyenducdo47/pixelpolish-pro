<script setup>
import { reactive, ref } from 'vue';

const cardRef = ref(null);
const state = reactive({
    rotateX: 0,
    rotateY: 0,
    scale: 1,
    glowX: 50,
    glowY: 50,
    hovered: false,
});

function onMove(event) {
    const card = cardRef.value;
    if (!card) {
        return;
    }

    const rect = card.getBoundingClientRect();
    const mouseX = event.clientX - (rect.left + rect.width / 2);
    const mouseY = event.clientY - (rect.top + rect.height / 2);

    state.rotateX = (mouseY / (rect.height / 2)) * -6;
    state.rotateY = (mouseX / (rect.width / 2)) * 6;
    state.glowX = ((event.clientX - rect.left) / rect.width) * 100;
    state.glowY = ((event.clientY - rect.top) / rect.height) * 100;
}

function onEnter() {
    state.scale = 1.01;
    state.hovered = true;
}

function onLeave() {
    state.rotateX = 0;
    state.rotateY = 0;
    state.scale = 1;
    state.hovered = false;
    state.glowX = 50;
    state.glowY = 50;
}
</script>

<template>
    <div
        ref="cardRef"
        class="relative"
        style="perspective: 1000px; transform-style: preserve-3d"
        @mousemove="onMove"
        @mouseenter="onEnter"
        @mouseleave="onLeave"
    >
        <div
            class="pointer-events-none absolute -inset-px rounded-2xl transition-opacity duration-300"
            :class="state.hovered ? 'opacity-100' : 'opacity-0'"
            :style="{
                background: `radial-gradient(600px circle at ${state.glowX}% ${state.glowY}%, color-mix(in srgb, var(--pf-primary) 40%, transparent), transparent 40%)`,
            }"
        />
        <div
            class="relative z-10 transition-transform duration-200 ease-out"
            :style="{
                transform: `rotateX(${state.rotateX}deg) rotateY(${state.rotateY}deg) scale(${state.scale})`,
                transformStyle: 'preserve-3d',
            }"
        >
            <slot />
        </div>
    </div>
</template>
