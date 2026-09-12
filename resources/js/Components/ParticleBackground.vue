<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const canvasRef = ref(null);
let frame = 0;
let onResize = null;

let cachedKey = '';
let cachedRgb = '31, 153, 168';

function primaryRgb() {
    const value = getComputedStyle(document.documentElement).getPropertyValue('--pf-primary').trim() || '#1f99a8';

    if (value === cachedKey) {
        return cachedRgb;
    }

    const probe = document.createElement('canvas').getContext('2d');
    probe.fillStyle = value;
    const hex = probe.fillStyle;

    if (!hex.startsWith('#')) {
        return cachedRgb;
    }

    const raw = hex.length === 4
        ? hex.slice(1).split('').map((part) => part + part).join('')
        : hex.slice(1);

    cachedKey = value;
    cachedRgb = `${parseInt(raw.slice(0, 2), 16)}, ${parseInt(raw.slice(2, 4), 16)}, ${parseInt(raw.slice(4, 6), 16)}`;

    return cachedRgb;
}

onMounted(() => {
    const canvas = canvasRef.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        return;
    }

    let particles = [];

    const resize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const count = Math.min(80, Math.floor((canvas.width * canvas.height) / 18000));
        particles = Array.from({ length: count }, () => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            size: Math.random() * 2 + 0.5,
            speedX: (Math.random() - 0.5) * 0.3,
            speedY: (Math.random() - 0.5) * 0.3,
            opacity: Math.random() * 0.5 + 0.2,
        }));
    };

    const draw = () => {
        const rgb = primaryRgb();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach((particle, index) => {
            particle.x += particle.speedX;
            particle.y += particle.speedY;
            if (particle.x < 0) particle.x = canvas.width;
            if (particle.x > canvas.width) particle.x = 0;
            if (particle.y < 0) particle.y = canvas.height;
            if (particle.y > canvas.height) particle.y = 0;

            ctx.beginPath();
            ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${rgb}, ${particle.opacity})`;
            ctx.fill();

            for (let i = index + 1; i < particles.length; i++) {
                const other = particles[i];
                const dx = particle.x - other.x;
                const dy = particle.y - other.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < 120) {
                    ctx.beginPath();
                    ctx.moveTo(particle.x, particle.y);
                    ctx.lineTo(other.x, other.y);
                    ctx.strokeStyle = `rgba(${rgb}, ${0.1 * (1 - distance / 120)})`;
                    ctx.lineWidth = 0.5;
                    ctx.stroke();
                }
            }
        });
        frame = requestAnimationFrame(draw);
    };

    resize();
    draw();
    onResize = resize;
    window.addEventListener('resize', onResize);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(frame);
    if (onResize) {
        window.removeEventListener('resize', onResize);
    }
});
</script>

<template>
    <canvas ref="canvasRef" class="pointer-events-none absolute inset-0 h-full w-full" />
</template>
