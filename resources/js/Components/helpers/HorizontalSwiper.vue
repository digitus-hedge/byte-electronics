<template>
    <div class="swiper-container">
        <!-- Swiper Container -->
        <Swiper ref="mySwiper" :modules="[Autoplay, Navigation]"
            :autoplay="{ delay: 2500, disableOnInteraction: false }" :spaceBetween="20" :slidesPerView="2" :breakpoints="{
                320: { slidesPerView: 1, spaceBetween: 10 },
                576: { slidesPerView: 1, spaceBetween: 15 },
                768: { slidesPerView: 2, spaceBetween: 15 },
                1024: { slidesPerView: 2, spaceBetween: 20 }
            }" @swiper="setSwiperInstance">
            <SwiperSlide v-for="(banner, index) in products" :key="index">
                <div :class="['card', { 'd-flex': !left }]">
                    <div :class="['card-image-container', { 'd-flex align-items-center': !left }]"
                        :style="left ? 'justify-content: flex-start' : ''">
                        <img :src="banner.imgSrc" class="card-img-left img-fluid" :alt="banner.text"  />
                        <p class="card-title mb-1 p-3">{{ banner.text }}</p>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>

        <!-- Navigation Buttons -->
        <div class="swiper-button-prev-custom"></div>
        <div class="swiper-button-next-custom"></div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'bootstrap-icons/font/bootstrap-icons.css';

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    left: {
        type: Boolean,
        default: false
    }
});

const swiperInstance = ref(null);

const setSwiperInstance = (swiper) => {
    swiperInstance.value = swiper;
};

onMounted(() => {
    document.querySelector('.swiper-button-prev-custom')?.addEventListener('click', () => {
        if (swiperInstance.value) swiperInstance.value.slidePrev();
    });

    document.querySelector('.swiper-button-next-custom')?.addEventListener('click', () => {
        if (swiperInstance.value) swiperInstance.value.slideNext();
    });
});
</script>

<style scoped>
.swiper-container {
    position: relative;
    width: 100%;
    padding: 0 50px;
}

.card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    height: 100%;
    background: white;
}

.card-image-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.card-img-left {
    /* object-fit: cover; */
    width: 100%;
    height: 250px;
    border-radius: 20px 20px 0 0;
}

.card-title {
    font-size: 18px;
    font-weight: 400;
    padding: 15px;
    text-align: center;
    color: #333333;
    margin: 0;
}

/* Navigation Buttons */
.swiper-button-prev-custom,
.swiper-button-next-custom {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.swiper-button-prev-custom {
    left: 0;
}

.swiper-button-next-custom {
    right: 0;
}

.swiper-button-prev-custom::after,
.swiper-button-next-custom::after {
    font-family: 'bootstrap-icons';
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.swiper-button-prev-custom::after {
    content: '\F284';
}

.swiper-button-next-custom::after {
    content: '\F285';
}

/* Responsive Styles */
@media (max-width: 1199px) {
    .card-img-left {
        height: 200px;
    }

    .card-title {
        font-size: 16px;
        padding: 12px;
    }
}

@media (max-width: 1024px) {

    .swiper-button-prev-custom,
    .swiper-button-next-custom {
        width: 35px;
        height: 35px;
    }

    .swiper-button-prev-custom::after,
    .swiper-button-next-custom::after {
        font-size: 20px;
    }
}

@media (max-width: 768px) {
    .swiper-container {
        padding: 0 40px;
    }

    .card-img-left {
        height: 180px;
    }
}

@media (max-width: 582px) {

    .swiper-button-prev-custom,
    .swiper-button-next-custom {
        display: none;
    }

    .swiper-container {
        padding: 0;
    }

    .card-title {
        font-size: 15px;
        padding: 10px;
    }

    .card-img-left {
        height: 160px;
    }
}
</style>
