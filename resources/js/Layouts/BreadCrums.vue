<script setup>
import { usePage } from '@inertiajs/vue3';

// Get breadcrumbs from shared Inertia data
const breadcrumbs = usePage().props.breadcrumbs;
</script>

<template>
    <div class="custom-bg ">
        <div class="container-fluid container d-flex align-items-center justify-content-between">
            <p class="pt-2 pb-2">
                Home
                <template v-for="(crumb, index) in breadcrumbs" :key="index">
                    <span> / </span>
                    <span class="text-muted">{{ crumb }}</span>
                </template>
            </p>
        </div>
    </div>
</template>

<style scoped>
.custom-bg {
    background-color: #ececec;
}
p {
    padding: 0;
    margin: 0;
}
span {
    color: #000000 !important;
}
</style>
