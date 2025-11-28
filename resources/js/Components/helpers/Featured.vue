<script setup>
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

defineProps({
    categoriesShow: {
        type: Array,
        required: true
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
})
</script>

<template>
    <section class="mt-4 mb-3">
        <div class="small-sub-sec d-flex justify-content-between align-items-center p-4">
            <h3 class="m-0 needHelp">NEED HELP?</h3>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-chat-fill"></i>
                <a href="https://wa.me/971555316164"  target="_blank" rel="noopener noreferrer"
                    class="d-flex align-items-center text-decoration-none" style="color: #000000;">CHAT NOW</a>
            </div>
        </div>
    </section>

    <section class="mt-4 mb-3">
        <h3 class="text-center mb-4" style="color: #000000; font-weight: 700;">Featured Manufacturers</h3>
        <!-- <div class="text-center">
            <img src="/assets/images/Group 1.png" class="img-fluid" style="height: 50px;" alt="Manufacturers Logo">
        </div> -->

        <div class="manufacturers-grid text-center mb-4">
            <Link
                v-for="(brand, index) in Brands"
                :key="index"
                :href="`brands/details/${brand.slug}`"
                class="manufacturer-link"
            >
        <!-- <pre>{{ brand.file_name }}</pre> -->

                <img
                    :src="`${imageBrandUrl}/${brand.file_name} `"
                    class="img-fluid manufacturer-logo"
                >
            </Link>
        </div>
        <div class="featCategory mt-4 d-flex flex-wrap justify-content-center text-center">
            <div v-for="(category, index) in categoriesShow" :key="index" class="" style="width: 150px;">
                <Link :href="`/categories/details/${category.slug}`"
                    class="text-decoration-none text-dark d-flex flex-column align-items-center">
                <img :src="`${imageUrl}/${category.file_name || 'default.png'}`" class="img-fluid"
                    :alt="category.name" />
                <span class="d-block mt-2">{{ category.name }}</span>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.needHelp {
    color: #000000;
    font-weight: 700;
}

.chat {
    text-decoration: none;
}

.featCategory {
    gap: 1rem;
}
.manufacturers-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    align-items: center;
    justify-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.manufacturer-link {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    transition: transform 0.3s ease;
}

.manufacturer-link:hover {
    transform: scale(1.05);
}

.manufacturer-logo {
    max-height: 50px;
    width: auto;
    object-fit: contain;
}

@media (max-width: 992px) {
    .manufacturers-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 576px) {
    .manufacturers-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
