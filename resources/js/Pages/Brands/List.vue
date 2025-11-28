<script setup>
import Banner from '@/Components/helpers/Banner.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    brandBanner: String,
    manufacturers: Object,
});

const alphabets = Array.from({ length: 26 }, (_, i) => String.fromCharCode(65 + i));

const scrollToSection = (letter) => {
    const section = document.getElementById(`section-${letter}`);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <p class="fw-bold" style="color: #000000;">ALL MANUFACTURERS (A-Z)</p>
            <Banner :image="brandBanner" :search="false"  />

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center w-100 py-3">
                <div class="d-flex flex-wrap gap-2 pt-2 pb-2 text-center text-md-start">
                    <p class="fw-bold mb-0">Manufacturers by Product Category</p>
                    <span class="d-none d-md-inline">|</span>
                    <p class="fw-bold mb-0">New Manufacturers</p>
                </div>
                <!-- <button class="btn mt-2 mt-md-0" style="border: 1.5px solid #EF4137; font-size: 14px;">
                    Export to Excel
                </button> -->
            </div>


            <div style="background: #ECECEC;" class="d-flex gap-2 p-3 mb-1 flex-wrap justify-content-between w-100">
                <a @click.prevent="scrollToSection('#')" href="#" class="text-dark text-decoration-none">#</a>
                <a v-for="letter in alphabets" :key="letter" @click.prevent="scrollToSection(letter)" href="#"
                    class="text-dark text-decoration-none">
                    {{ letter }}
                </a>
            </div>

            <div class="w-100" v-for="(manufacturersList, letter) in manufacturers" :key="letter"
                :id="`section-${letter}`">
                <div class="w-100 p-2" style="background: #FFEEED; padding-left: 0.75rem !important;">{{ letter }}</div>
                <ul class="list p-3 d-grid gap-3" style="grid-template-columns: repeat(3, 1fr);">
                    <!-- <li v-for="manufacturer in manufacturersList" :key="manufacturer" >
                        <Link :href="manufacturer.url" style="color: #333333;">
                        {{ manufacturer.name }}
                        </Link>
                    </li> -->
                    <li v-for="manufacturer in manufacturersList.filter(m => m.name && m.name.toString().trim() !== '')"
                        :key="manufacturer">
                        <Link :href="manufacturer.url" style="color: #333333;">
                            {{ manufacturer.name }}
                        </Link>
                    </li>

          </ul>
            </div>

        </div>
    </AppLayout>
</template>
<style scoped>
@media (max-width: 767.98px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    [style*="background: #ECECEC"] {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    [style*="grid-template-columns"] {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
        gap: 1rem !important;
    }

    .p-3 {
        padding: 1rem !important;
    }
}

@media (min-width: 768px) {
    [style*="grid-template-columns"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (min-width: 1200px) {
    [style*="grid-template-columns"] {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

</style>
