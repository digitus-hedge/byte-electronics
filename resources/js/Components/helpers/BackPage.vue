<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';
const props = defineProps({
  url: {
    type: String,
    required: true
  },
  text:{
    type: String,
    default: 'BACK TO BASKET'
  }
});
</script>
<template>
    <Link :href="url"><i class="bi bi-chevron-left"></i><span class="text">{{ text }}</span></Link>
</template>
<style>
.bi-chevron-left{
    color: #000000;
}
.text{
   color: #EF4137;
   font-weight: 600;
}
</style>
