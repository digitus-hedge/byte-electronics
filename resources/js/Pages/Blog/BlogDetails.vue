<script setup>
import Banner from "@/Components/helpers/Banner.vue";
import HorizontalSwiper from "@/Components/helpers/HorizontalSwiper.vue";
import SwiperCard from "@/Components/helpers/SwiperCard.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from "@/Layouts/BreadCrums.vue";

defineProps({
    blogdetailsMainBanner: {
        type: String,
    },
    blogDetailsSubBanner: {
        type: String,
    },
    blogImage_1: {
        type: String,
    },
    blogImage_2: {
        type: String,
    },
    blogImage_3: {
        type: String,
    },
    blogImage_4: {
        type: String,
    },
    pageTitle: {
        type: String,
    },
    pageSubtitle: {
        type: String,
    },
    pageDescription: {
        type: String,
    },
    mainHeading: {
        type: String,
    },
    blogTitle: {
        type: String,
    },
    blogIntro: {
        type: String,
    },
    publishDate: {
        type: String,
    },
    blogContent: {
        type: Array,
    },
    blogContent_1: {
        type: Array,
    },
    bannerTitle: {
        type: String,
    },
    blogDescription: {
        type: String,
    },
    blogBanners: {
        type: Array,
    },
    otherContent: {
        type: Array,
    },
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <!-- <pre>{{  }}</pre> -->
        <Banner v-if="blogdetailsMainBanner" :image="blogdetailsMainBanner" :text="'blogs'" :search="false" />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <h3 class="mb-3 fw-bold text-wrap" style="max-width: 600px;color:#333333;">
                {{ bannerTitle }}
            </h3>
            <div class="position-relative w-100">
                <Banner v-if="blogDetailsSubBanner" :image="blogDetailsSubBanner" :search="false" class="sub-banner" />
                <p v-if="pageSubtitle" class="center-text text-white position-absolute start-50 translate-middle-x"
                    style="top: 20%;font-size: 1.3rem;text-decoration: underline;">
                    {{ pageSubtitle }}
                </p>
                <h2 v-if="mainHeading"
                    class="center-text text-white position-absolute top-50 start-50 translate-middle fw-bold"
                    style="font-size: 6rem;">
                    {{ mainHeading }}
                </h2>
            </div>
            <div class="mt-4 mb-4">
                <h3 v-if="blogTitle" class="blogDetailsHead fw-bold"
                    style="font-size: 20px; display: inline-block; border-bottom: 1px solid #000;color:#000000;">
                    {{ blogTitle }}
                </h3>
                <p v-if="blogIntro" class="blogDetailsPara fw-bold mt-3 mb-2" style="color: #000000;">
                    {{ blogIntro }}
                </p>
                <span v-if="publishDate" style="font-size: 14px;">Published {{ publishDate }}</span>
            </div>
            <div v-if="blogContent_1 && blogContent_1.length" class="mt-4">
                <p style="font-size: 13px;color:#333333;word-break: break-all;">
                    <div v-html="blogContent_1"></div>
                </p>
            </div>
            <div class="col-12">
                <div class="row">
                    <template v-if="blogImage_4">
                        <!-- Left Column -->
                        <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                            <Banner :image="blogImage_1" :search="false" :height="'100%'"
                                class="w-100 rounded-3 leftColumn" />
                        </div>
                        <!-- Middle Column -->
                        <div class="col-12 col-lg-4 d-flex flex-column gap-3 mb-3 mb-lg-0 middleSecBanner">
                            <div class="w-100" style="height: auto;">
                                <Banner :image="blogImage_2" :search="false" :height="'100px'"
                                    class="w-100 mb-0 rounded-3" />
                            </div>
                            <div class="w-100" style="height: auto;">
                                <Banner :image="blogImage_3" :search="false" :height="'200px'"
                                    class="w-100 rounded-3" />
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-12 col-lg-3 lastBanner">
                            <Banner :image="blogImage_4" :search="false" :height="'100%'" class="w-100 rounded-3" />
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-12">
                            <Banner :image="blogImage_1" :search="false" class="w-100 rounded-3" />
                        </div>
                    </template>
                </div>
            </div>
            <div v-if="blogContent && blogContent.length" class="mt-4" style="border-bottom: 0.5px solid #A6A0A0;">
                <p style="font-size: 13px;color:#333333;word-break: break-all;">
                    <div v-html="blogContent_1"></div>
                </p>
            </div>
            <!-- Horizontal Swiper -->
            <div v-if="blogBanners && blogBanners.length" class="container mt-4" id="SwiperContainer"
                style="padding: 0 3rem 0 3rem;">
                <HorizontalSwiper :products="blogBanners" :left="true" />
            </div>
            <div class="col-md-12 text-align-center mt-4" style="text-align: center;">
                <button @click="goBack"
                    style="border:1px solid #EE0B0B;color:#111;border-radius: 10px;padding: 10px 20px;"
                    class="inline-flex items-center px-4 focus-ring-offset-2 transition ease-in-out duration-150">
                    Return to Home
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
* {
    color: #000000;
}
.sub-banner {
    height: 50vh;
    max-height: 420px;
}
.sub-banner .banner-image {
    display: block; /* Ensure image displays */
    object-fit: cover; /* Maintain aspect ratio */
}
@media (max-width: 990px) {
    #SwiperContainer {
        padding: 0 1rem 0 1rem !important;
    }
}
@media (max-width: 992px) {
    .middleSecBanner,
    .lastBanner {
        display: none !important;
    }
    .leftColumn {
        height: 200px !important;
    }
}
@media (max-width: 768px) {
    .banner {
        height: 40vh;
    }
}
</style>
