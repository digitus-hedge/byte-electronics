<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
  brand: {
    type: String,
    default: "AMPHENOL",
  },
  products: {
    type: Array,
    default: () => [],
  },
});

const fallbackImage = '/assets/images/dummy_product.webp';
</script>

<template>
  <div class="card mb-2 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" 
        style="background: #373737; padding: 10px 20px;">
      <h5 class="mb-0 text-white" style="font-size: 14px;">
        FEATURED PRODUCTS
      </h5>
      <span class="px-2 py-1 rounded" 
            style="background: #555; color: #fff; font-size: 14px;">
        {{ brand }}
      </span>
    </div>

    <Link v-for="(item, index) in products" :key="index" 
          :href="`/products/details/${item.slug}`"
          class="d-flex border-bottom p-3 align-items-start text-decoration-none"
          style="color:inherit; cursor:pointer;">
      <img 
        :src="`/uploads/products/${item.file_name}`" 
        @error="$event.target.src = fallbackImage"
        alt="product image"
        class="me-3" 
        style="width:80px; height:80px; object-fit:contain;" 
      />

      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1" style="color:#ef4137; font-size:14px;">
          {{ item.name }}
        </h6>
        <p class="mb-1" style="font-size:12px; color:#000;">
          {{ item.description }}
        </p>
      </div>
    </Link>
  </div>
</template>
