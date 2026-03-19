<script setup>
import Footer from '@/Components/LayoutComponent/Footer.vue';
import SiteHeader from '@/Components/LayoutComponent/SiteHeader.vue';
</script>

<template>
    <div>
        <SiteHeader />
        <div class="eid-mubarak" style="position: fixed; bottom: 1%; left: 1%; z-index: 99;">
            <img src="/assets/images/eid-mubarak.gif" style="    width: 260px;">
        </div>
        <!-- <main class="container d-flex flex-column justify-content-center align-items-center" > -->
        <main class="">
            <slot></slot>
        </main>
        <Footer />
    </div>
</template>

<style scoped>
@media (max-width: 575px) {
    .eid-mubarak img {
        width: 150px !important;
    }
}
</style>
