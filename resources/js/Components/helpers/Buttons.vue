<script setup>
import { defineProps } from "vue";

defineProps({
    name: String,
    icon: String,
    isActive: Boolean,
    buttonWidth: String // Receives dynamic width
});
</script>

<template>
    <button
        class="custom-button"
        :class="{ active: isActive }"
        :style="{ width: buttonWidth }"
    >
        <i :class="`bi ${icon}`"></i>
        <span>{{ name }}</span>
    </button>
</template>

<style scoped>
.custom-button {
    width: 10rem;
    background: #D9D9D9;
    color: black;
    border-radius: 10px;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    cursor: pointer;
    transition: background 0.3s, color 0.3s;
    font-size: 14px;
    font-weight: 500;
}

.custom-button.active {
    background: #EF4137;
    color: white;
}
span{
    font-size: 12px;
}
</style>
