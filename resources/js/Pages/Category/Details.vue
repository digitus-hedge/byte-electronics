<script setup>
import { ref } from "vue";
import axios from "axios";
import Buttons from "@/Components/helpers/Buttons.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import BreadCrums from "@/Layouts/BreadCrums.vue";

const props = defineProps({
    image: String,
    title: String,
    description: String,
    productId: Number,
    current_categories: Array,
    subCategories: Array,
    // productCategory: Object,
    filterUrl: [String, Object],

});

const activeButton = ref(null);

const handleClick = async (index) => {
    activeButton.value = index;
    console.log("Button clicked:", activeButton.value);
    const filterUrl = props.filterUrl;
    console.log("Filter URL:", filterUrl.category);

    // Check if button has a predefined route
    // if (buttons[index].route) {
    //     router.visit(route(buttons[index].route));
    //     return;
    // }
    if (buttons[index].route) {
        let url = (buttons[index].route);

        // Check if it's the "Newest Products" button
        if (buttons[index].name === "Newest Products") {
            url += '/filter?newProducts=true';
        }

        router.visit(url);
        return;
    }

    // Handle filter URL
    console.log("Filter URL:", filterUrl);

    if (filterUrl) {
        // If filterUrl is a string, use it directly
        // If filterUrl is an object, use the 'category' key
        const url = typeof filterUrl === 'string' ? filterUrl : filterUrl.category;
        router.visit(url);
    } else {
        try {
            const response = await axios.post("/categories/category-action", {
                product_id: props.productId,
                action: buttons[index].name,
            });
            console.log("Response:", response.data);
        } catch (error) {
            console.error("Error:", error);
        }
    }
};

// Define buttons with different names & icons
const buttons = [
    { name: "Products", icon: "bi-box-seam", },
    // { name: "Datasheets", icon: "bi-file-earmark-text" },
    // { name: "Images", icon: "bi-image" },
    { name: "Newest Products", icon: "bi-star-fill", url: 'product/list' }
];
const searchQuery = ref("");
const selectedFilter = ref("");

const filterOptions = ref([
    { value: "in-stock", label: "In Stock" },
    { value: "normally-stocked", label: "Normally Stocked" },
    { value: "new-products", label: "New Products" },
    { value: "active", label: "Active" },
    { value: "rohs-compliant", label: "RoHS Compliant" }
]);

const checkboxes = ref([
    { id: "in-stock", label: "In Stock", checked: false },
    { id: "normally-stocked", label: "Normally Stocked", checked: false },
    { id: "new-products", label: "New Products", checked: false },
    { id: "active", label: "Active", checked: false },
    { id: "rohs-compliant", label: "RoHS Compliant", checked: false }
]);

const applyFilters = () => {
    const activeFilters = checkboxes.value.filter(c => c.checked).map(c => c.id);
    console.log("Filters Applied:", {
        searchQuery: searchQuery.value,
        selectedFilter: selectedFilter.value,
        activeFilters
    });
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <div class="d-flex align-items-center gap-4 ">
                <div>
                    <img :src="image && image.includes('/uploads/') ? image : '/assets/images/dummy_product.webp'"
                        :alt="title" class="img-fluid custom-img"
                        style="background: radial-gradient(#040404, #111);border-radius: 10px;">
                </div>
                <div>
                    <h5 class="mb-2 text-danger fw-bold">{{ title }}</h5>
                    <p class="description mb-0">{{ description }}</p>
                </div>
            </div>
            <!-- <pre>{{ subCategories }}</pre> -->

            <div class="d-flex pt-4 pb-4 align-items-center gap-4 flex-wrap actionBtn">
                <Buttons v-for="(btn, index) in buttons" :key="index" :name="btn.name" :icon="btn.icon"
                    :isActive="activeButton === index" @click="handleClick(index)" />
            </div>
            <div class="pt-3 w-100">
                <h5 class="categoryfilterHead fw-bold w-100 d-block border-bottom pb-2">Types of {{ title }}</h5>

                <div v-for="(category, index) in current_categories" :key="index" class="mt-2 pb-3 ms-3 border-bottom">
                    <!-- <code>{{ category }}</code> -->
                    <Link :href="category.url" class="fw-bold pt-2 pb-2" style="color:#585D62;">
                    {{ category.name }}
                    </Link>
                    <div class="row ms-4">
                        <div v-for="(item, i) in category.items" :key="i" class="col-md-4">
                            <Link>
                            {{ item.name }}
                            </LInk>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="border w-100 custom-border-color rounded-3 p-4 mt-5">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="col-12 col-md-5 mb-3 mb-md-0">
                        <label class="mb-2" for="search-input">Search within results</label>
                        <div class="input-group">
                            <input type="text" class="form-control combined-search-input border" id="search-input"
                                v-model="searchQuery" placeholder="Search here..." />
                            <button class="btn custom-bg-color" @click="applyFilters">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="mb-2" for="select-filter">Filter by</label>
                        <div class="d-flex">
                            <select v-model="selectedFilter" id="select-filter" class="form-control box-shadow-0">
                                <option value="">Select Filter</option>
                                <option v-for="option in filterOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <button class="btn ml-3 custom-filter-btn-color fw-light" @click="applyFilters">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 pt-3">
                    <div class="row">
                        <div v-for="(checkbox, index) in checkboxes" :key="index"
                            class="col-12 col-md-4 d-flex align-items-center mb-2 mb-md-0">
                            <div class="form-check text-center">
                                <input type="checkbox"
                                    class="form-check-input checkbox-lg border border-secondary box-shadow-0"
                                    :id="checkbox.id" v-model="checkbox.checked">
                                <label class="form-check-label custom-span-style small-text" :for="checkbox.id">{{
                                    checkbox.label }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-img {
    width: 130px;
    height: auto;
    border-radius: 10px;
}

h5 {
    font-size: 18px;
}

p {
    font-size: 14px;
    color: #000000;
}

a {
    color: #3A3A3A;
    font-size: 14px;
}

.categoryfilterHead {
    color: #585D62;
    font-size: 16px;
}

@media (max-width: 368px) {
    .actionBtn {
        justify-content: center;
    }
}

.custom-bg-color {
    background-color: #ef4137;
    color: white;
}

.custom-filter-btn-color {
    background: linear-gradient(#EF4137, #892520);
    color: white;
    margin-left: 10px;
    width: 100%;
    max-width: 120px;
}

.custom-border-color {
    border-color: #CACACA;
}

label {
    font-size: 14px;
    font-weight: 500;
}

.custom-span-style {
    font-size: 14px;
    font-weight: 400;
    color: #333333;
}

.form-check {
    display: flex;
    align-items: center;
    text-align: center;
    gap: 15px;
}

.form-check-input {
    margin-bottom: 5px;
    transform: scale(1.2);
}

@media (max-width: 767px) {
    .custom-filter-btn-color {
        width: 100%;
    }

    .form-check {
        align-items: flex-start;
    }

    .col-12 {
        padding-right: 0;
        padding-left: 0;
    }

    .form-check-label {
        margin-top: 5px;
    }
}

.form-control:focus,
.form-check-input:focus {
    box-shadow: none !important;
    border-color: #CACACA !important;
}
</style>
