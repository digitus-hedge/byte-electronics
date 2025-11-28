<template>
    <section>
        <div class="mt-4 mb-3 d-flex justify-content-between">
            <!-- Dynamic Title -->
            <h4 class="fw-bold">{{ sectionTitle }}</h4>
            <div>
                <!-- Previous Button -->
                <button class="btn p-0 border"
                    @click="swiperInstance && swiperInstance.slidePrev(); toggleButtonStyle('prev')"
                    :style="prevButtonStyle" :disabled="isAtBeginning"
                    style="border-width: 1px; border-left: none; border-radius: 8px 0px 0px 8px;">
                    <i class="bi bi-chevron-left p-2" :style="prevIconStyle"></i>
                </button>
                <!-- Next Button -->
                <button class="btn p-0 border"
                    @click="swiperInstance && swiperInstance.slideNext(); toggleButtonStyle('next')"
                    :style="nextButtonStyle" :disabled="isAtEnd"
                    style="border-width: 1px; border-right: none; border-radius: 0px 8px 8px 0px;">
                    <i class="bi bi-chevron-right p-2" :style="nextIconStyle"></i>
                </button>
            </div>
        </div>

        <!-- Swiper Component -->
        <Swiper ref="mySwiper" :modules="[Autoplay, Navigation]"
            :autoplay="{ delay: 3000, disableOnInteraction: false }" :spaceBetween="20" :slidesPerView="slidesPerView"
            :breakpoints="breakpoints" @swiper="setSwiperInstance" @slideChange="updateButtonState">
            <SwiperSlide v-for="(item, index) in items" :key="index">
                <ProductCard :product="item" />
            </SwiperSlide>
        </Swiper>
    </section>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation } from 'swiper';
import 'swiper/swiper-bundle.min.css';
import ProductCard from './ProductCardSub.vue';

// Props
const props = defineProps({
    sectionTitle: {
        type: String,
        default: 'Section Title',
    },
    items: {
        type: Array,
        required: true,
    },
    slidesPerView: {
        type: Number,
        default: 5,
    },
    breakpoints: {
        type: Object,
        default: () => ({
            320: { slidesPerView: 1, spaceBetween: 10 },
            768: { slidesPerView: 3, spaceBetween: 15 },
            1024: { slidesPerView: 5, spaceBetween: 20 },
        }),
    },
});

const swiperInstance = ref(null);
const prevButtonStyle = ref({});
const nextButtonStyle = ref({});
const prevIconStyle = ref({});
const nextIconStyle = ref({});
const isAtBeginning = ref(false);
const isAtEnd = ref(false);

const setSwiperInstance = (swiper) => {
    swiperInstance.value = swiper;
    updateButtonState();
};

const toggleButtonStyle = (buttonType) => {
    if (buttonType === 'prev') {
        prevButtonStyle.value = { backgroundColor: 'black' };
        prevIconStyle.value = { color: 'white' };
        nextButtonStyle.value = {};
        nextIconStyle.value = {};
    } else if (buttonType === 'next') {
        nextButtonStyle.value = { backgroundColor: 'black' };
        nextIconStyle.value = { color: 'white' };
        prevButtonStyle.value = {};
        prevIconStyle.value = {};
    }
};

const updateButtonState = () => {
    if (swiperInstance.value) {
        isAtBeginning.value = swiperInstance.value.isBeginning;
        isAtEnd.value = swiperInstance.value.isEnd;
    }
};
</script>
