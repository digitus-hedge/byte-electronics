<!-- Updated template -->
<template>
    <AppLayout>
    <section class="container d-flex flex-column justify-content-center align-items-center">
        <section class="container mt-3 " style="padding-left: 0 !important;padding-right: 0 !important;">
            <div class="row">
                <!-- Large Banner -->
                <div class="col-lg-6 mainBanner">
                    <!-- <HomePageBanner :banners="imagesData.Banners[0].images" :large="true" /> -->
                    <HomePageBanner :banners="Banners[0].images.sort((a, b) => a.priority - b.priority)" :large="true" />

                </div>
                <!-- Small Banners -->
                <div class="col-lg-6 d-flex flex-column">
                    <div class="row g-2">
                        <div class="col-6 d-flex">
                            <!-- <HomePageBanner :banners="imagesData.Banners[1].images" :large="false" /> -->
                            <HomePageBanner :banners="Banners[1].images.sort((a, b) => a.priority - b.priority)" :large="true" />

                        </div>
                        <div class="col-6 d-flex">
                            <!-- <HomePageBanner :banners="imagesData.Banners[2].images" :large="false" /> -->
                            <HomePageBanner :banners="Banners[2].images.sort((a, b) => a.priority - b.priority)" :large="true" />

                        </div>
                        <div class="col-6 d-flex">
                            <!-- <HomePageBanner :banners="imagesData.Banners[3].images" :large="false" /> -->
                            <HomePageBanner :banners="Banners[3].images.sort((a, b) => a.priority - b.priority)" :large="true" />

                        </div>
                        <div class="col-6 d-flex">
                            <!-- <HomePageBanner :banners="imagesData.Banners[4].images" :large="false" /> -->
                            <HomePageBanner :banners="Banners[4].images.sort((a, b) => a.priority - b.priority)" :large="true" />

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <SwiperCard :products="newestProducts" title="NEWEST PRODUCTS" />
        <Featured :categoriesShow="categoriesShow" :imageUrl="imageUrl" :Brands="Brands" :imageBrandUrl="imageBrandUrl" />
        <!-- <pre>{{ bestSellers }}</pre> -->
        <SwiperCard :products="bestSellers" title="BEST SELLER" />
    </section>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SwiperCard from '@/Components/helpers/swiperCard.vue';
import Featured from '@/Components/helpers/Featured.vue';
import { ref } from 'vue';
import HomePageBanner from '@/Components/helpers/HomePageBanner.vue';
defineProps({
    Banners: {
        type: Array,
    },
    newestProducts: {
        type: Array,
    },
    bestSellers: {
        type: Array,
    },
    // categories: {
    //     type: Array,
    // },
    categoriesShow:{
        type: Array,
    },
    imageUrl: {
        type: String,
    },
    Brands: {
        type: Array,
    },
    imageBrandUrl: {
        type: String,
    },
    
});
</script>
<style scoped>
@media screen and (max-width:990px) {
    .mainBanner {
        margin-bottom: 1rem;
    }
}
</style>
