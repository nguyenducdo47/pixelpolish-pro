<script setup>
defineProps({
    html: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div v-if="html" class="rich-content" v-html="html" />
</template>
