<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import ImageCard from '@/Components/helpers/ImageCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import Accordion from '@/Components/helpers/Accordion.vue';
import SwiperCard from '@/Components/helpers/swiperCard.vue';
import ListBox from '@/Components/helpers/ListBox.vue';
import { Link, router } from '@inertiajs/vue3';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { toast } from "vue3-toastify";
import RequestQuoteForm from '@/Components/helpers/RequestQuoteForm.vue'
import FeaturedProducts from '@/Components/helpers/FeaturedProducts.vue';

// Define props
const props = defineProps({
    ProductBanner: String,
    productPageFilter: Array,
    product: Object,
    productCategories: Array,
    relatedProducts: Array,
    pricing: Object,
    hasPrices: Boolean,
    FeaturedProduct: Array,

});
///////
const unitKeys = computed(() => Object.keys(props.pricing.pricing || {}));

const firstUnitKey = computed(() => unitKeys.value[0] || null);

const firstUnitPrices = computed(() => {
    if (!firstUnitKey.value) return [];
    return props.pricing.pricing[firstUnitKey.value].slice(0, 100);
});

const remainingPrices = computed(() => {
    const result = [];

    if (firstUnitKey.value) {
        const allPrices = props.pricing.pricing[firstUnitKey.value];
        const remaining = allPrices.slice(5);
        if (remaining.length) {
            result.push({ unitKey: firstUnitKey.value, prices: remaining });
        }
    }

    // Add remaining unit keys (excluding first)
    unitKeys.value.slice(1).forEach((key) => {
        result.push({ unitKey: key, prices: props.pricing.pricing[key] });
    });

    return result;
});

// //////


// Reactive variables for selected quantity, unit price, unit key, and error
const selectedQuantity = ref('');
const selectedUnitPrice = ref(null);
const selectedUnitKey = ref('');
const quantityError = ref('');
const openIndex = ref(0);

const updateOpenIndex = (newIndex) => {
    openIndex.value = newIndex;
};
const filteredAccordionData = computed(() => {
    return props.product.accordionData.filter(section => section.title !== 'Images');

});


const getUnitPriceAndKey = (quantity, preferredUnitKey = null) => {
    const qty = parseInt(quantity) || 0;
    let unitPrice = props.product.price;
    let unitKey = Object.keys(props.pricing.pricing)[0] || '';

    // Log for debugging
    console.log('getUnitPriceAndKey:', {
        qty,
        preferredUnitKey,
        pricing: JSON.parse(JSON.stringify(props.pricing.pricing))
    });

    // Check if preferredUnitKey is provided and valid
    if (preferredUnitKey && props.pricing.pricing[preferredUnitKey]) {
        const priceList = [...props.pricing.pricing[preferredUnitKey]].sort((a, b) => b.qty - a.qty);
        const price = priceList.find(p => qty >= p.qty);
        if (price) {
            unitPrice = price.unit_price;
            unitKey = preferredUnitKey;
            return { unitPrice, unitKey };
        }
    }

    // Iterate through all pricing tiers to find the highest applicable price tier
    let selectedPrice = null;
    let selectedKey = unitKey;
    for (const [key, priceList] of Object.entries(props.pricing.pricing)) {
        const sortedPriceList = [...priceList].sort((a, b) => b.qty - a.qty);
        const price = sortedPriceList.find(p => qty >= p.qty);
        if (price && (!selectedPrice || price.qty > selectedPrice.qty)) {
            selectedPrice = price;
            selectedKey = key;
        }
    }

    if (selectedPrice) {
        unitPrice = selectedPrice.unit_price;
        unitKey = selectedKey;
    }

    return { unitPrice, unitKey };
};

// Update unit price and key on quantity input change
const updateUnitPrice = (event) => {
    const quantity = event.target.value;
    selectedQuantity.value = quantity; // Sync with v-model
    quantityError.value = ''; // Clear error on input

    console.log('updateUnitPrice:', { quantity, selectedUnitKey: selectedUnitKey.value });

    const qty = parseInt(quantity);
    if (!quantity || isNaN(qty) || qty < 1) {
        selectedUnitPrice.value = null;
        selectedUnitKey.value = '';
        return;
    }

    const { unitPrice, unitKey } = getUnitPriceAndKey(quantity, selectedUnitKey.value);
    selectedUnitPrice.value = unitPrice;
    selectedUnitKey.value = unitKey;
};

// Handle quantity click from pricing table
const selectQuantity = (qty, unitPrice, unitKey) => {
    // Validate unitKey
    const validUnitKey = Object.keys(props.pricing.pricing).includes(unitKey)
        ? unitKey
        : Object.keys(props.pricing.pricing)[0] || '';
    selectedQuantity.value = qty;
    selectedUnitPrice.value = unitPrice;
    selectedUnitKey.value = validUnitKey;
    quantityError.value = ''; // Clear error on valid selection
    const quantityInput = document.getElementById('quantityInput');
    if (quantityInput) {
        quantityInput.value = qty;
    }
    console.log('selectQuantity:', { qty, unitPrice, unitKey: validUnitKey });
};

// Validate quantity
const validateQuantity = (quantity) => {
    const qty = parseInt(quantity);
    if (!quantity || isNaN(qty) || qty < 1) {
        quantityError.value = 'Please enter a valid quantity';
        return false;
    }
    quantityError.value = '';
    return true;
};

// Add to cart function
const addToCart = async (productId) => {
    const quantity = selectedQuantity.value || document.getElementById('quantityInput').value;

    // Validate quantity
    if (!validateQuantity(quantity)) {
        return;
    }

    // Determine unit price and key
    let unitPrice = selectedUnitPrice.value;
    let unitKey = selectedUnitKey.value;
    if (!unitPrice) {
        // Manual input: calculate unit price based on quantity
        const priceInfo = getUnitPriceAndKey(quantity, selectedUnitKey.value);
        unitPrice = priceInfo.unitPrice;
        unitKey = priceInfo.unitKey;
    }

    console.log('addToCart:', { unitPrice, unitKey, quantity });
    router.post(`/cart/add/${productId}`, { quantity: parseInt(quantity), unit_price: unitPrice, unit_key: unitKey }, {
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
                }
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
                }
            });
        },
    });
};

// Handle buy button click
const handleBuyClick = (productId) => {
    const quantity = selectedQuantity.value || document.getElementById('quantityInput').value;

    // Validate quantity
    if (!validateQuantity(quantity)) {
        return;
    }

    // Determine unit price and key
    let unitPrice = selectedUnitPrice.value;
    let unitKey = selectedUnitKey.value;
    if (!unitPrice) {
        // Manual input: calculate unit price based on quantity
        const priceInfo = getUnitPriceAndKey(quantity, selectedUnitKey.value);
        unitPrice = priceInfo.unitPrice;
        unitKey = priceInfo.unitKey;
    }

    addToCart(productId);
};

onMounted(() => {
    console.log("Product Pricing Data:", JSON.parse(JSON.stringify(props.pricing.pricing)));
});
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container d-flex flex-column justify-content-center align-items-center">
            <section class="mt-3 mb-3">
                <div class="row d-flex align-items-stretch">
                    <div class="col-md-8">
                        <div class="row">
                            <!-- Main Image -->
                            <div class="col-md-5">
                                <ImageCard :image="product.images[0].src" />
                            </div>
                            <!-- Details Section -->
                            <div class="col-md-7">
                                <div class="card p-4 shadow-sm h-100">
                                    <h4 class="card-title mb-3 text-uppercase fs-4 fw-bold"
                                        style="font-size: 18px !important;">
                                        {{ product.name }}
                                    </h4>
                                    <p class="card-text border-bottom pb-3 mb-4" style="font-size: 12px;color:#000000;">
                                        <span v-html="product.description"></span>
                                    </p>
                                    <div class="row mb-3">
                                        <div class="col-4 fw-400" style="font-size: 14px;">Part No:</div>
                                        <div class="col-8 text-muted" style="font-size: 14px;color:#000000 !important;">
                                            {{ product.part_no }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4 fw-400" style="font-size: 14px;color:#000000 !important;">Mfr:
                                        </div>
                                        <div class="col-8 text-muted" style="font-size: 14px;color:#000000 !important;">
                                            {{ product.manufacturer }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4 fw-400" style="font-size: 14px;color:#000000 !important;">
                                            Description:</div>
                                        <div class="col-8 text-muted" v-html="product.description"
                                            style="font-size: 14px;color:#000000 !important;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <pre>{{ product }}</pre> -->
                        <!-- Accordion -->
                        <div class="pb-3 mt-3">
                            <div class="accordion" id="accordionExample">

                                <Accordion v-for="(section, index) in filteredAccordionData" :key="index"
                                    :title="section.title" :attributes="section.attributes" :index="index"
                                    :buttonColor="'#000000'" :buttonbgColor="'rgb(159 153 153)'" :productId="product.id"
                                    :categoryId="product.category_id" :subCategoryId="product.sub_category_id"
                                    :openIndex="openIndex" @update:openIndex="updateOpenIndex" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div v-if="hasPrices">
                            <div class="card p-3 mb-2 shadow-sm">
                                <div class="row " style="font-size: 12px;">
                                    <div class="col-5 fw-400" style="padding-right: 0px !important;color:#000000;">Enter
                                        Quantity:</div>
                                    <div class="col-7">
                                        <!-- <label class="form-label" style="color:#000000 !important;">
                                            Multiples: 1
                                        </label> -->
                                        <div class="input-group">
                                            <TextInput type="text" class="form-control"
                                                :class="{ 'input-error': quantityError }" id="quantityInput" required
                                                placeholder="Type here..." style="padding: 5px;font-size: 10px;"
                                                v-model="selectedQuantity" @input="updateUnitPrice" />
                                            <button class="btn"
                                                style="background:red;color:#fff;padding:5px;font-size: 10px;" type="button"
                                                @click="handleBuyClick(product.id)">Buy</button>
                                        </div>
                                        <!-- Error Message -->
                                        <div v-if="quantityError" class="text-danger mt-1" style="font-size: 10px;">
                                            {{ quantityError }}
                                        </div>
                                        <!-- Display selected unit price and unit key -->
                                        <div v-if="selectedUnitPrice" class="mt-2" style="font-size: 12px; color: #000000;">
                                            Unit Price: {{ product.currency }} {{ selectedUnitPrice }}
                                        </div>
                                        <div v-if="selectedUnitKey" class="mt-2" style="font-size: 12px; color: #000000;">
                                            Unit: {{ selectedUnitKey }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Pricing Table -->


                            <div class="card p-3 mb-2 shadow-sm">
                                <h5 class="border-bottom pb-3" style="color: #000000; font-weight: 700;">
                                    Pricing ({{ pricing.currency }})
                                </h5>

                                <div style="max-height: 250px; overflow-y: auto;">
                                    <table class="table mb-0">
                                        <thead class="bg-white" style="position: sticky; top: 0; z-index: 10;">
                                            <tr class="border-bottom text-center">
                                                <th class="p-1"
                                                    style="font-size: 14px; color: #000000; background: #ffffff;">Qty</th>
                                                <th class="p-1"
                                                    style="font-size: 14px; color: #000000; background: #ffffff;">Unit Price
                                                </th>
                                                <th class="p-1"
                                                    style="font-size: 14px; color: #000000; background: #ffffff;">Ext. Price
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(priceList, unitKey) in pricing.pricing" :key="unitKey">
                                                <tr>
                                                    <td colspan="3" class="p-1">
                                                        <h6 class="fw-bold mb-0" style="color: #000000; font-size: 0.9rem;">
                                                            {{ unitKey }}
                                                        </h6>
                                                    </td>
                                                </tr>
                                                <tr v-for="(price, index) in priceList" :key="index"
                                                    class="border-bottom text-center">
                                                    <td class="p-1" style="font-size: 14px; color: #000000;">
                                                        <a href="#"
                                                            @click.prevent="selectQuantity(price.qty, price.unit_price, unitKey)"
                                                            style="color: #EF4137; text-decoration: none;">
                                                            {{ price.qty }}
                                                        </a>
                                                    </td>
                                                    <td class="p-1" style="font-size: 14px; color: #000000;">
                                                        {{ product.currency }} {{ price.unit_price }}
                                                    </td>
                                                    <td class="p-1" style="font-size: 14px; color: #000000;">
                                                        {{ price.ext_price ? pricing.currency + ' ' + price.ext_price : ''
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr v-if="priceList.some(p => p.is_reel)" class="border-bottom">
                                                    <td colspan="3" class="p-1" style="color: #111;">
                                                        Full Reel (Order in multiples of 2500)
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div v-else class="request-quote-section">
                            <RequestQuoteForm :productId="product.id" />
                        </div>

                        <ListBox :title="productCategories.title" :categories="productCategories.categories" />
                        <div class="shadow-sm mt-3">
                            <!-- <ImageCard :image="product.images[0].src" customClass="my-custom-class" innerClass="p-0" /> -->
                        </div>
                        <FeaturedProducts v-if="FeaturedProduct.length > 0" :brand="product.manufacturer" :products="FeaturedProduct" />
                    </div>
                    <section class="mb-3 mt-3">
                        <div class="small-sub-sec d-flex justify-content-between align-items-center p-4">
                            <h3 class="m-0 fw-bold" style="font-size: 18px;">NEED HELP?</h3>
                            <div>
                                <i class="bi bi-chat-fill"></i>
                                <a href="https://wa.me/971555316164" target="_blank" class="chat ms-2 fw-bold"
                                    role="button" aria-label="Chat Now"
                                    style="font-size: 12px;border-radius: 10px;">CHAT NOW</a>
                            </div>
                        </div>
                    </section>
                    <SwiperCard :products="relatedProducts" title="RELATED PRODUCTS" />
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<style scoped>
.small-sub-sec {
    background-image: url('../../../../public/assets/images/img.png');
    height: 10px;
}

p {
    margin: 0px;
}

.input-error {
    border: 1px solid #EF4444 !important;
}
.request-quote-section {
    margin-bottom: 1rem; /* adjust as needed */
}


</style>
