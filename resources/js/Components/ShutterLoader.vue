<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const isLoading = ref(true);

// Show loader when navigation starts
router.on('start', () => {
    isLoading.value = true;
});

// Hide loader when navigation finishes
router.on('finish', () => {
    setTimeout(() => isLoading.value = false, 50000); // Delay for effect
});
</script>

<template>
    <transition name="shutter">
        <div v-if="isLoading" class="fixed inset-0 bg-black z-50 flex items-center justify-center">
            <div class="w-full h-full bg-gray-900 animate-pulse"></div>
        </div>
    </transition>
</template>

<style>
.shutter-enter-active, .shutter-leave-active {
    transition: transform 0.5s ease-in-out;
}
.shutter-enter-from {
    transform: translateY(-100%);
}
.shutter-leave-to {
    transform: translateY(100%);
}
</style>
