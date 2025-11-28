<script setup>
import Footer from '@/Components/LayoutComponent/Footer.vue';
import SiteHeader from '@/Components/LayoutComponent/SiteHeader.vue';
</script>

<template>
  <div>
    <SiteHeader />
    <!-- <main class="container d-flex flex-column justify-content-center align-items-center" > -->
        <main class="" >
      <slot></slot>
    </main>
    <Footer />
  </div>
</template>

<style scoped>
</style>
