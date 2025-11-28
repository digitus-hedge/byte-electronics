<script setup>
import Banner from '@/Components/helpers/Banner.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const productLineBanner = '/assets/banners/BOSCH-1 1.png';

const productLines = ref([
    {
        title: 'Power Tools',
        items: [
            'Drills & Hammer DrillsDrills & Hammer DrillsDrills & Hammer DrillsDrills & Hammer Drills',
            'Impact Drivers & Wrenches',
            'Sanders & Polishers',
            'Circular Saws & Jigsaws',
            'Angle Grinders'
        ]
    },
    {
        title: 'Garden Tools',
        items: [
            'Lawn Mowers',
            'Hedge Trimmers',
            'Chainsaws',
            'Leaf Blowers',
            'Pressure Washers'
        ]
    },
    {
        title: 'Measuring Tools',
        items: [
            'Laser Measures',
            'Distance Meters',
            'Levels & Protractors',
            'Thermal Imaging Cameras',
            'Moisture Detectors'
        ]
    }
]);
const searchTerm = ref('');
const filteredProductLines = computed(() => {
    if (!searchTerm.value) return productLines.value;

    return productLines.value.map(category => ({
        ...category,
        items: category.items.filter(item =>
            item.toLowerCase().includes(searchTerm.value.toLowerCase())
        )
    })).filter(category => category.items.length > 0);
});

const handleSearch = (term) => {
    searchTerm.value = term;
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <Banner :image="productLineBanner" :search="true" text="" align="right" @search="handleSearch" />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <template v-for="(productLine, index) in filteredProductLines" :key="index">
                <div class="d-flex align-items-center gap-3 mb-3 w-100 p-3 headContainer">
                    <h6><a style="color: #ef4137;">{{ productLine.title }}</a></h6>
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </div>
                <div class="sub-cat-head w-100">
                    <ul class="productLine">
                        <li v-for="(item, itemIndex) in productLine.items" :key="itemIndex">
                            <Link href="#">{{ item }}</Link>
                        </li>
                    </ul>
                </div>
            </template>
            <div v-if="searchTerm && filteredProductLines.length === 0" class="w-100 text-center py-5">
                <p>No results found for "{{ searchTerm }}"</p>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
h6 {
    text-transform: uppercase;
    color: #EF4137;
    margin-bottom: 0;
    font-weight: bold;
}

.headContainer {
    background-color: #F8F5F6;
}

.sub-cat-head {
    padding: 15px 0;
}

.productLine {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding-left: 20px;
    margin: 0;
    list-style-position: inside;
}

.productLine li {
    list-style-type: disc !important;
    width: 30.33%;
    white-space: normal;
    /* Changed from nowrap */
    word-wrap: break-word;
    /* Add this */
    font-size: 14px;
    margin-left: 1em;
    text-indent: -1em;
    box-sizing: border-box;
}

.productLine li a {
    color: #333;
    text-decoration: none;
}

.productLine li a:hover {
    color: #EF4137;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .productLine li {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .productLine li {
        flex: 0 0 100%;
        /* 1 item per row on very small screens */
        max-width: 100%;
    }
}
</style>
