<script setup>
import { ref, onMounted, computed } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import SwiperCore, { Navigation, Pagination, Autoplay } from "swiper";

SwiperCore.use([Navigation, Pagination, Autoplay]);

const props = defineProps({
  banners: Array,
  large: Boolean
});
const sortedBanners = computed(() => {
  return [...props.banners].sort((a, b) => (a.priority || 999) - (b.priority || 999));
});
</script>

<template>
  <Swiper
    :slides-per-view="1"
    :loop="true"
    :autoplay="{ delay: 3000, disableOnInteraction: false }"
    class="banner-swiper"
  >
    <swiper-slide v-for="(image, index) in sortedBanners" :key="index">
      <a :href="image.link">
        <img :src="image.src" :alt="image.alt" class="d-block w-100 img-fluid" />
      </a>
    </swiper-slide>
  </Swiper>
</template>

<style scoped>
.banner-swiper {
  width: 100%;
  height: 100%;
}
img {
  border-radius: 10px;
}
</style>
