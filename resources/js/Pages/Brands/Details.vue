
<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <div class="w-100">
                <div class="row">
                    <!-- Sidebar Navigation -->
                    <div class="col-md-2 mb-3">
                        <ul class="nav card w-100 d-flex flex-column align-items-stretch">
                            <li v-for="tab in brand.tabs" :key="tab.key" class="nav-item w-100">
                                <button
                                    class="nav-link d-block w-100 text-start d-flex align-items-center justify-content-between"
                                    :class="{ 'active': activeTab === tab.key }" @click="activeTab = tab.key">
                                    {{ tab.label }}
                                    <span>
                                        <i class="bi bi-chevron-right mt-1 mb-1 fs-6" style="font-weight: 900;"></i>
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-10">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-3">
                            <h4 class="fw-bold text-center text-md-start mb-2 mb-md-0">{{ brand.name }}</h4>
                        </div>

                        <div class="d-flex align-items-start w-100">
                            <template v-if="activeTab === 'about'">
                                <div class="card me-3 flex-shrink-0" style="width: 100px; height: 100px;">
                                    <img :src="brand.image" alt="Brand Image" class="card-img-top"
                                        style="object-fit: cover; height: 100%; width: 100%;">
                                </div>
                            </template>

                            <div>
                                <div v-for="tab in brand.tabs" :key="tab.key" v-show="activeTab === tab.key">
                                    <BrandCategoryTree v-if="tab.key === 'product'" :categories_brand="tab.content" />
                                    <p v-else style="font-size: 14px;">{{ tab.content }}</p>
                                </div>

                                <p v-if="activeTab === 'about'"
                                   class="fw-bold text-danger d-flex align-items-center gap-1"
                                   style="font-size: 15px;">
                                    <Link :href="`/products/filter?manufacturer%5B0%5D=${brand.id}`">
                                        Browse all products by {{ brand.name }}
                                    </Link>
                                    <span>
                                        <i class="bi bi-arrow-right-short mt-1 mb-1 fs-6 text-white"
                                           style="background: #EF4137; border-radius: 50%;"></i>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { defineProps } from 'vue';
import BrandCategoryTree from  "@/Components/helpers/BrandTree.vue";

const props = defineProps({
    brand: {
        type: Object,
        required: true,
    },
});

const activeTab = ref(props.brand.tabs[0].key);
</script>

<style scoped>
.nav-link {
    color: black;
    border-radius: 5px;
    padding: 10px !important;
    font-weight: 500;
    min-width: 100px;
    text-align: left;
    border-bottom: 1px solid #E2E2E2;
}
a {
    color: rgba(var(--bs-danger-rgb),var(--bs-text-opacity))!important;
}
.nav-item:last-child .nav-link {
    border-bottom: none;
}
.nav-link.active {
    background-color: #EF4137;
    color: white;
}
</style>
