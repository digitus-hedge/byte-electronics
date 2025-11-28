<script setup>
import Accordion from "@/Components/helpers/Accordion.vue";
import Banner from "@/Components/helpers/Banner.vue";
import Pagination from "@/Components/helpers/Pagination.vue";
import ProductCard from "@/Components/helpers/ProductCard.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from "@/Layouts/BreadCrums.vue";
import { ref, watch, computed } from "vue";
import { Link, useForm, router } from '@inertiajs/vue3';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import AttributeFilter from "@/Components/helpers/AttributeFilter.vue";
import AttributeFilterNew from "@/Components/helpers/AttributeFilterNew.vue";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import axios from 'axios'

const props = defineProps({
    ProductBanner: String,
    productPageFilter: { type: Array, required: true },
    products: { type: Object },
    brands: { type: Array, required: true },
    categories: { type: Array, required: true },
    subCategories: { type: Array, required: true },
    selectedFilters: {
        type: Object,
        default: () => ({
            manufacturer: [],
            productType: [],
            subCategory: [],
            page: 1,
            search: '',
            active: false,
            rohsCompliant: false,
            newProducts: false,
            attributeFilters: {},
        })
    },
});
// Initialize filters with only explicitly provided values
const filters = ref({
    manufacturer: props.selectedFilters.manufacturer?.length > 0 ? props.selectedFilters.manufacturer : [],
    productType: props.selectedFilters.productType?.length > 0 ? props.selectedFilters.productType : [],
    subCategory: props.selectedFilters.subCategory?.length > 0 ? props.selectedFilters.subCategory : [],
    page: props.selectedFilters.page || 1,
    search: props.selectedFilters.search || '',
    active: props.selectedFilters.active ?? false,
    rohsCompliant: props.selectedFilters.rohsCompliant ?? false,
    newProducts: props.selectedFilters.newProducts ?? false,
    attributeFilters: Object.keys(props.selectedFilters.attributeFilters || {}).length > 0 ? props.selectedFilters.attributeFilters : {},
});
const form = useForm({ quantity: 1 });
const searchQuery = ref(filters.value.search);
const currentPage = ref(filters.value.page);
const openIndex = ref(null);

const filteredManufacturers = computed(() => props.brands);
const filteredProductTypes = computed(() => props.categories);
const filteredSubCategory = computed(() => props.subCategories);

const applyFilters = () => {
    filters.value.page = 1;
    currentPage.value = 1;
    const queryParams = {};
    if (filters.value.search) queryParams.search = filters.value.search;
    if (filters.value.manufacturer.length > 0) queryParams.manufacturer = filters.value.manufacturer;
    if (filters.value.productType.length > 0) queryParams.productType = filters.value.productType;
    if (filters.value.subCategory.length > 0) queryParams.subCategory = filters.value.subCategory;
    if (filters.value.active === true) queryParams.active = true;
    if (filters.value.rohsCompliant === true) queryParams.rohsCompliant = true;
    if (filters.value.newProducts === true) queryParams.newProducts = true;
    if (Object.keys(filters.value.attributeFilters).length > 0) {
        queryParams.attributeFilters = {};
        Object.entries(filters.value.attributeFilters).forEach(([key, values]) => {
            if (Array.isArray(values) && values.length > 0) {
                queryParams.attributeFilters[key] = [...values];
            }
        });
    }
    router.get('/products/filter/', queryParams, {
        preserveState: false,
        replace: true,
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('Filter request success:', page.props);
        },
        onError: (errors) => {
            console.error('Filter request error:', errors);
            toast.error("Failed to apply filters.", {
                position: "top-right",
                autoClose: 1500,
            });
        },
    });
};

const updatePage = (newPage) => {
    currentPage.value = newPage;
    filters.value.page = newPage;
    const queryParams = { page: newPage };
    if (filters.value.search) queryParams.search = filters.value.search;
    if (filters.value.manufacturer.length > 0) queryParams.manufacturer = filters.value.manufacturer;
    if (filters.value.productType.length > 0) queryParams.productType = filters.value.productType;
    if (filters.value.subCategory.length > 0) queryParams.subCategory = filters.value.subCategory;
    if (filters.value.active === true) queryParams.active = true;
    if (filters.value.rohsCompliant === true) queryParams.rohsCompliant = true;
    if (filters.value.newProducts === true) queryParams.newProducts = true;
    if (Object.keys(filters.value.attributeFilters).length > 0) {
        queryParams.attributeFilters = {};
        Object.entries(filters.value.attributeFilters).forEach(([key, values]) => {
            if (Array.isArray(values) && values.length > 0) {
                queryParams.attributeFilters[key] = [...values];
            }
        });
    }
    router.get('/products/filter/', queryParams, {
        preserveState: false,
        replace: true,
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('Pagination request success:', page.props);
        },
        onError: (errors) => {
            console.error('Pagination request error:', errors);
        },
    });
};

const updateAttributeFilters = (newFilters) => {
    filters.value.attributeFilters = newFilters.attributeFilters || {};
    // Merge newFilters with existing filters, preserving boolean states unless explicitly provided
    filters.value.active = newFilters.active !== undefined ? newFilters.active : filters.value.active;
    filters.value.rohsCompliant = newFilters.rohsCompliant !== undefined ? newFilters.rohsCompliant : filters.value.rohsCompliant;
    filters.value.newProducts = newFilters.newProducts !== undefined ? newFilters.newProducts : filters.value.newProducts;
    applyFilters();
};

// Sync filters with props.selectedFilters
watch(() => props.selectedFilters, (newSelectedFilters) => {
    console.log('List.vue watch props.selectedFilters:', newSelectedFilters);
    filters.value.manufacturer = newSelectedFilters.manufacturer?.length > 0 ? newSelectedFilters.manufacturer : [];
    filters.value.productType = newSelectedFilters.productType?.length > 0 ? newSelectedFilters.productType : [];
    filters.value.subCategory = newSelectedFilters.subCategory?.length > 0 ? newSelectedFilters.subCategory : [];
    filters.value.page = newSelectedFilters.page || 1;
    filters.value.search = newSelectedFilters.search || '';
    filters.value.attributeFilters = Object.keys(newSelectedFilters.attributeFilters || {}).length > 0 ? newSelectedFilters.attributeFilters : {};
    filters.value.active = newSelectedFilters.active ?? filters.value.active;
    filters.value.rohsCompliant = newSelectedFilters.rohsCompliant ?? filters.value.rohsCompliant;
    filters.value.newProducts = newSelectedFilters.newProducts ?? filters.value.newProducts;

    searchQuery.value = filters.value.search;
    currentPage.value = filters.value.page;
}, { deep: true });

watch(searchQuery, (newQuery) => {
    filters.value.search = newQuery;
    filters.value.page = 1;
    applyFilters();
});

// const addToCart = async (product) => {

//     const response = await axios.post(`/cart/add/${product.id}`);
//     console.log('Add to Cart Response:', response.data);

//     if (response.data.requires_confirmation) {
//             const minQty = response.data.minimum_qty;

//             const result = await Swal.fire({
//                 html: `<p>Your minimum quantity is ${minQty}. Do you want to proceed?</p>`,
//                 icon: "question",
//                 showCancelButton: true,
//                 confirmButtonText: "Yes, Add to Cart",
//                 cancelButtonText: "Cancel",
//             });

//     if (!result.isConfirmed) return;

//     try {
//         await axios.post(`/cart/add/${product.id}`, {
//             confirmed: true,
//         });

//         toast.success("🛒 Item added to cart with minimum quantity!", {
//             position: "top-right",
//             autoClose: 1500,
//         });
//     } catch (error) {
//         toast.error("❌ Failed to add item", {
//             position: "top-right",
//             autoClose: 1500,
//         });
//     }
// }
// };


const addToCart = async (productId, quantity = 1) => {

    router.post(`/cart/add/${productId}`, { quantity }, {
        preserveScroll: true,
        only: ['cartCount'],
        onSuccess: () => {
            toast.success("🛒 Item added to cart!", {
                position: "top-right",
                autoClose: 1500,
                hideProgressBar: false,
                closeButton: true,
                className: 'custom-toast-success',
                style: {
                    fontSize: '14px',
                    fontWeight: '500',
                    padding: '12px 20px',
                },
                iconTheme: {
                    primary: '#10B981',
                    secondary: '#FFFFFF',
                },
            });
        },
        onError: () => {
            toast.error("❌ Failed to add item", {
                position: "top-right",
                autoClose: 1500,
                hideProgressBar: false,
                closeButton: true,
                className: 'custom-toast-error',
                style: {
                    background: '#FFFFFF',
                    color: '#1F2937',
                    border: '1px solid #E5E7EB',
                    borderRadius: '8px',
                    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.1)',
                    fontSize: '14px',
                    fontWeight: '500',
                    padding: '12px 20px',
                },
                iconTheme: {
                    primary: '#EF4444',
                    secondary: '#FFFFFF',
                },
            });
        },
    });
};

const removeItemFromCart = (itemId) => {
    form.delete(`/cart/remove/${itemId}`, {
        preserveScroll: true,
        only: ['cartCount'],
        onSuccess: () => toast.success("Item removed from cart!"),
        onError: () => toast.error("Failed to remove item."),
    });
};
</script>

<template>
    <AppLayout>
        <BreadCrums />

        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <Banner :image="ProductBanner" :search="false" :style="{ borderRadius: '10px' }" />
            <div class="py-3 w-100">
                <div class="container" style="padding: 0; font-size: 14px;">
                    <AttributeFilterNew :productPageFilter="productPageFilter" :filters="filters"
                        @update:filters="updateAttributeFilters" />
                    <!-- <pre>{{ 'rohs', selectedFilters }}</pre> -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="filter-bar d-flex flex-wrap align-items-center gap-3 p-1 rounded">
                            <span class="filter-label fw-medium text-secondary small align-self-center"
                                style="color: #000000 !important;">FILTERS:</span>
                            <div class="position-relative bg-white rounded" style="width: 220px;">
                                <input v-model="searchQuery" type="text" class="form-control ps-3 pe-4 py-2 small"
                                    placeholder="Search products..." id="searchProduct" @input="applyFilters"
                                    style="height: 36px;">
                                <i
                                    class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted small"></i>
                            </div>
                            <div class="filter-options d-flex flex-wrap align-items-center gap-3">
                                <div class="form-check d-flex align-items-center">
                                    <input v-model="filters.active" class="form-check-input mt-0 me-2" type="checkbox"
                                        id="active" @change="filters.page = 1; applyFilters()"
                                        style="width: 16px; height: 16px;">
                                    <label class="form-check-label small" for="active">Active</label>
                                </div>
                                <div class="form-check d-flex align-items-center">
                                    <input v-model="filters.rohsCompliant" class="form-check-input mt-0 me-2"
                                        type="checkbox" id="rohs" @change="filters.page = 1; applyFilters()"
                                        style="width: 16px; height: 16px;">
                                    <label class="form-check-label small" for="rohs">RoHS</label>
                                </div>
                                <div class="form-check d-flex align-items-center">
                                    <input v-model="filters.newProducts" class="form-check-input mt-0 me-2"
                                        type="checkbox" id="newProducts" @change="filters.page = 1; applyFilters()"
                                        style="width: 16px; height: 16px;">
                                    <label class="form-check-label small" for="newProducts">New</label>
                                </div>
                            </div>
                        </div>
                        <div class="pagination">
                            <Pagination :currentPage="currentPage" :totalItems="products.total"
                                :itemsPerPage="products.per_page" @update:currentPage="updatePage" />
                        </div>
                    </div>
                </div>
                <div class="container mt-3" style="padding-left: 0;">
                    <div class="row">
                        <div class="col-md-3">
                            <Accordion title="Manufacturer" :filterSections="filteredManufacturers"
                                v-model="filters.manufacturer" :index="0" :openIndex="openIndex"
                                @update:openIndex="openIndex = $event" @change="applyFilters" />
                            <Accordion title="Categories" :filterSections="filteredProductTypes"
                                v-model="filters.productType" :index="1" :openIndex="openIndex"
                                @update:openIndex="openIndex = $event" @change="applyFilters" />
                            <Accordion title="Sub Categories" :filterSections="filteredSubCategory"
                                v-model="filters.subCategory" :index="2" :openIndex="openIndex"
                                @update:openIndex="openIndex = $event" @change="applyFilters" />
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 p-1" v-for="product in products.data"
                                    :key="product.id">
                                    <div class="product-card-wrapper position-relative h-100">
                                        <div class="card d-flex flex-column h-100">
                                            <!-- <div class="image-container position-relative">
                                                <img :src="getProductImage(product.image)" class="card-img-top" :alt="product.name">
                                                <div
                                                    class="hover-overlay d-flex align-items-center justify-content-center">
                                                    <Link :href="`/products/details/${product.slug}`"
                                                        class="view-product-btn">
                                                        <i class="fas fa-eye me-2"></i> View Product
                                                    </Link>
                                                </div>
                                            </div> -->
                                            <div v-if="product" class="image-container position-relative">
                                                <img :src="product.image"
                                                    @error="event => event.target.src = '/assets/images/dummy_product.webp'"
                                                    class="card-img-top" :alt="product.name" />
                                                <div
                                                    class="hover-overlay d-flex align-items-center justify-content-center">
                                                    <Link :href="`/products/details/${product.slug}`"
                                                        class="view-product-btn">
                                                    <i class="fas fa-eye me-2"></i> View Product
                                                    </Link>
                                                </div>
                                            </div>
                                            <!--  -->

                                            <div class="card-body d-flex flex-column">
                                                <Link :href="`/products/details/${product.slug}`" class="product-title">
                                                {{ product.name }}
                                                </Link>
                                                <button @click="addToCart(product.id)" class="btn w-100 mt-auto"
                                                    style="color:#EF4137; font-weight: 600;">
                                                    ADD TO CART
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- <script>
export default {
    methods: {
        getProductImage(image) {
            if (image && image.trim() !== '') {
                return `${image}`;
            }
            return '/assets/images/dummy_product.webp';
        },
    },
};
</script> -->
<script>
export default {
    props: {
        product: {
            type: Object,
            required: true,
        },
    },
};

</script>



<style scoped>
.card-img-top {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: contain;
    padding: 10px !important;
}

@media (max-width: 1400px) {
    .pagination {
        display: flex;
        justify-content: end;
        width: 100%;
    }
}

* {
    font-size: 14px;
    color: #000000;
}

.filter-bar {
    /* background-color: #f8f9fa !important; */
    /* border: 1px solid #e9ece SCALEf; */
}

.search-input-container {
    background-color: white;
    border-radius: 6px;
    overflow: hidden;
}

.form-control {
    background-color: transparent;
    box-shadow: none !important;
}

.form-control:focus {
    outline: none;
    box-shadow: none;
}

.form-check-input {
    border: 1px solid #adb5bd;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: #EF4137;
    border-color: #EF4137;
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(239, 65, 55, 0.25);
}

.form-check-label {
    color: #000000;
    cursor: pointer;
    user-select: none;
    font-size: 0.85rem;
    line-height: 1.2;
}

@media (max-width: 768px) {
    .filter-bar {
        gap: 1rem;
        padding: 1rem;
    }

    .search-input-container {
        width: 100% !important;
    }

    .filter-options {
        width: 100%;
        gap: 1rem !important;
        justify-content: flex-start;
    }
}

.product-card-wrapper {
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
}

.image-container {
    height: 200px;
    background: #fffdfd;
    position: relative;
    overflow: hidden;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.view-product-btn {
    color: #000000;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

.view-product-btn:hover {
    transform: scale(1.05);
}

.product-card-wrapper:hover .hover-overlay {
    opacity: 1;
}

.product-card-wrapper:hover .card-img-top {
    transform: scale(1.05);
}

.product-title {
    color: #000000;
    text-decoration: none;
    font-size: 14px;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

@media (max-width: 992px) {
    .image-container {
        height: 160px;
    }
}

@media (max-width: 768px) {
    .image-container {
        height: 140px;
    }
}

@media (max-width: 576px) {
    .image-container {
        height: 120px;
    }
}
</style>