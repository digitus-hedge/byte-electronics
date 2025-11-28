<script setup>
import { defineProps, ref, watch, onMounted, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import Swal from 'sweetalert2';
// import { debounce } from 'lodash-es';
import axios from 'axios';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { debounce } from 'lodash-es';



const props = defineProps({
    cartItems: {
        type: Array,
        default: () => []
    },
    subtotal: {
        type: Number,
        default: 0
    },
    auth: {
        type: Object,
        default: () => ({ user: null })
    }
});

// Reactive state
const quantity = ref(1);
const searchQuery = ref('');
const searchResults = ref([]);
const isSearchDropdownOpen = ref(false);
const selectedProduct = ref(null);
const isLoading = ref(false);

// Cart functions
// const updateCartItem = async (itemId, newQuantity) => {
//     if (!Number.isInteger(newQuantity) || newQuantity < 1) {
//         Swal.fire({
//             title: 'Invalid Quantity',
//             text: 'Quantity must be at least 1.',
//             icon: 'warning',
//             confirmButtonColor: '#EF4137',
//         });
//         return;
//     }
//     try {
//         console.log('Updating item:', { itemId, newQuantity });
//         await router.put(`/cart/update/${itemId}`, {
//             quantity: newQuantity,
//         }, {
//             preserveScroll: true,
//             headers: {
//                 'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
//             },
            
//             onError: (errors) => {
//                 console.error('Update errors:', errors);
//                 Swal.fire({
//                     title: 'Error!',
//                     text: errors.quantity || 'Failed to update cart item.',
//                     icon: 'error',
//                     confirmButtonColor: '#EF4137',
//                 });
//             },
//         });
        
//     } catch (error) {
//         console.error('Error updating cart item:', error);updateCartItem
//         Swal.fire({
//             title: 'Error!',
//             text: 'Failed to update cart item.',
//             icon: 'error',
//             confirmButtonColor: '#EF4137',
//         });
//     }
// };
const updateCartItem = async (itemId, newQuantity) => {
console.log('Updating item:', { itemId, newQuantity });
    if (!Number.isInteger(newQuantity) || newQuantity < 1) return;

    try {
        const response = await axios.put(`/cart/update/${itemId}`, {
            quantity: newQuantity,
            _method: 'PUT'
        }, {
            headers: {
                'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
        });

        if (response.status === 200) {
            const updatedItem = props.cartItems.find(item => item.id === itemId);
            if (updatedItem && response.data.unit_price) {
                updatedItem.unitPrice = parseFloat(response.data.unit_price);
                updatedItem.quantity = newQuantity;
            }
        }
    } catch (error) {
        console.error('Error updating cart item:', error);
    }
};


const removeItem = async (itemId) => {
    try {
        console.log('Removing item:', { itemId });
        await router.delete(`/cart/remove/${itemId}`, {
            preserveScroll: true,
            headers: {
                'X-XSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            onError: (errors) => {
                console.error('Remove errors:', errors);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to remove item from cart.',
                    icon: 'error',
                    confirmButtonColor: '#EF4137',
                });
            },
        });
    } catch (error) {
        console.error('Error removing item from cart:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to remove item from cart.',
            icon: 'error',
            confirmButtonColor: '#EF4137',
        });
    }
};

// Product search and add to cart
const searchProducts = async () => {
    
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        isSearchDropdownOpen.value = false;
        return;
    }

    try {
        const response = await axios.get('/products/search', {
            params: { search: searchQuery.value }
        });
  
        searchResults.value = response.data.products || [];
     console.log('Search results:', searchResults.value);
        isSearchDropdownOpen.value = true;
    } catch (error) {
        console.error('Error searching products:', error);
        searchResults.value = [];
        isSearchDropdownOpen.value = false;
    }
};


const selectProduct = (event, product) => {

    event.preventDefault();
    searchQuery.value = product.name;
    
    selectedProduct.value = product;
    isSearchDropdownOpen.value = false;
};

const addToCart = async () => {
    if (!selectedProduct.value) {
        toast.error("Please select a product first");
        return;
    }

    if (quantity.value < 1) {
        toast.error("Quantity must be at least 1");
        quantity.value = 1;
        return;
    }

    isLoading.value = true;

    try {
        await router.post(`/cart/add/${selectedProduct.value.id}`, {
            quantity: quantity.value,
            unit_key: 'Normal' // Default unit_key, can be updated based on product selection
        }, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("🛒 Item added to cart!");
                quantity.value = 1;
                searchQuery.value = '';
                selectedProduct.value = null;
                router.reload({ only: ['cartItems', 'subtotal'], preserveScroll: true });
            },
            onError: (errors) => {
                toast.error(errors.message || "Failed to add item");
            },
        });
    } catch (error) {
        toast.error("An error occurred");
        console.error('Error adding to cart:', error);
    } finally {
        isLoading.value = false;
    }
};

// Checkout and utilities
const checkout = () => {
    if (!props.auth.user) {
        toast.error("Please log in to proceed with checkout");
        router.visit('/login', { preserveScroll: true });
        return;
    }

    if (props.cartItems.length === 0) {
        toast.error("Your cart is empty");
        return;
    }
    const invalidItem = props.cartItems.find(item => !item.quantity || item.quantity < 1);
    if (invalidItem) {
        toast.error("Quantity must be at least 1 ");
        return;
    }
 
    router.visit('/checkout-page', { preserveScroll: true });
};

const printPage = () => window.print();

const sharePage = () => {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href,
        }).catch(error => console.log('Sharing error:', error));
    } else {
        Swal.fire({
            title: 'Not Supported',
            text: 'Sharing is not supported on this browser.',
            icon: 'info',
            confirmButtonColor: '#EF4137',
        });
    }
};

const exportData = () => {
    Swal.fire({
        title: 'Coming Soon',
        text: 'Export functionality will be implemented soon.',
        icon: 'info',
        confirmButtonColor: '#3B6007',
    });
};

const viewOrders = () => {
    router.visit('/my-orders', { preserveScroll: true });
};

const addAddress = () => {
    Swal.fire({
        title: 'Coming Soon',
        text: 'More delivery options will be implemented soon.',
        icon: 'info',
        confirmButtonColor: '#3B6007',
    });
};

// Watchers
watch(searchQuery, debounce(searchProducts, 300));

watch(() => props.cartItems, (newItems) => {
    console.log('Cart updated:', newItems);
}, { immediate: true });

// Computed
// const total = computed(() => props.subtotal.toFixed(2));
const subtotalReactive = computed(() => {
    return props.cartItems.reduce((sum, item) => {
        const unitPrice = parseFloat(item.unitPrice) || 0;
        const quantity = parseInt(item.quantity) || 0;
        return sum + unitPrice * quantity;
    }, 0).toFixed(2);
});

const cartIsEmpty = computed(() => props.cartItems.length === 0);
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <Link :href="'/products/filter'" class="backBtn d-flex align-items-center">
            <i class="bi bi-chevron-left mt-1 mb-1 fs-6" style="font-weight: 900;"></i>
            <span>Continue Shopping</span>
            </Link>
            <h4 class="mt-3 mb-3 fw-bold" style="font-size: 18px;">Shopping Basket</h4>

            <!-- Button Wrapper -->
            <div class="btnWrap d-flex flex-wrap w-100 justify-content-end align-items-center gap-3 mb-2 p-2">
                <button @click="printPage">
                    <i class="bi bi-printer mt-1 mb-1 fs-6"></i>
                    Print this page
                </button>
                <span>|</span>
                <button @click="sharePage">
                    <i class="bi bi-share mt-1 mb-1 fs-6"></i>
                    Share
                </button>
                <span>|</span>
                <button @click="exportData">
                    <i class="bi bi-file-arrow-up mt-1 mb-1 fs-6"></i>
                    Export
                </button>
                <span>|</span>
                <button @click="viewOrders">
                    <i class="bi bi-clipboard mt-1 mb-1 fs-6"></i>
                    My Orders
                </button>
            </div>

            <!-- Search and Add Section -->
            <div class="checkOut mt-4 mb-3 p-3">
                <div class="row g-2 align-items-end flex-wrap">
                    <div class="col-md-5 col-sm-6 position-relative">
                        <label>Product</label>
                        <input type="text" v-model="searchQuery" class="form-control shadow-none"
                            @keydown.enter="addToCart" placeholder="Search products..." @input="searchProducts" />
                            <div class="search-results-dropdown" v-if="isSearchDropdownOpen && searchResults.length">
                                <ul class="search-results-list">
                                    <li v-for="product in searchResults" :key="product.id"
                                        @click="selectProduct($event, product)" class="search-result-item">
                                        <div class="product-info">
                                            <span class="product-name">{{ product.name }}</span>
                                            <!-- Add price if available -->
                                            <span class="product-price" v-if="product.price">
                                                USD {{ product.price }}
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label>Quantity</label>
                        <input type="number" v-model.number="quantity" class="form-control shadow-none" min="1"
                            @keydown.enter="addToCart">
                    </div>

                    <div class="col-md-4 col-sm-6 d-flex gap-3">
                        <label> </label>
                        <button class="btn w-100" :style="{
                            backgroundColor: '#EF4137',
                            color: '#fff',
                            fontSize: '14px',
                            opacity: selectedProduct ? 1 : 0.6,
                            cursor: selectedProduct ? 'pointer' : 'not-allowed'
                        }" @click="addToCart" :disabled="!selectedProduct || isLoading">
                            <span v-if="!isLoading" style="color:#fff;">Add</span>
                            <span v-else class="spinner-border spinner-border-sm" role="status"></span>
                        </button>
                    </div>


                </div>
            </div>

            <!-- Cart Items Table -->
            <div class="w-100 card p-3 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="m-0">Listed Items : {{ cartItems.length }}</p>
                    </div>
                    <div class="col-md-1">
                        <button class="btn w-100" :style="{
                            backgroundColor: '#3B6007',
                            color: '#fff',
                            fontSize: '14px',
                            opacity: cartIsEmpty ? 0.6 : 1,
                            cursor: cartIsEmpty ? 'not-allowed' : 'pointer'
                        }" @click="checkout" :disabled="cartIsEmpty">
                            Checkout
                        </button>
                    </div>
                </div>
                <div class="table-responsive mb-3">
                    <table class="custom-table" v-if="!cartIsEmpty">
                        <thead>
                            <tr>
                                <th class="rounded-left">#</th>
                                <th>Product Detail</th>
                                <th></th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Unit Price (USD)</th>
                                <th>Ext. Price (USD)</th>
                                <th class="rounded-right">
                                    <i class="bi bi-trash delete-icon"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="(item, index) in cartItems" :key="item.id" class="border-bottom">
                                <td>{{ index + 1 }}</td>
                                <td class="d-flex align-items-center gap-2">
                                    <div class="product-img">
                                        <img :src="item.image && /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(item.image) ? item.image : '/assets/images/dummy_product.webp'"
                                            class="card-img-top p-0" :alt="item.name">
                                    </div>
                                    <div class="product-details">
                                        <span>Mfr. No: {{ item.partno }}</span><br>
                                        <span>Mfr: {{ item.brand }}</span><br>
                                    </div>
                                </td>
                                <td class="product-details"></td>
                                <td>
                                    <div v-html="item.description"></div><br>
                                    <Link :href="`products/details/${item.slug}`" class="quick-view">
                                    Product Details
                                    <i class="fa-regular fa-share-from-square share-icon" style="color: #EF4137;"></i>
                                    </Link>
                                </td>
                                <td>{{ item.unit_key ?? 'Normal' }}</td>
                                <td>
                                    <input type="number" class="form-control quantity-input shadow-none"
                                        v-model.number="item.quantity" min="1"
                                        @change="updateCartItem(item.id, item.quantity)">
                                </td>
                                <td>{{ item.unitPrice }}</td>
                                <td>{{ (item.unitPrice * item.quantity).toFixed(2) }}</td>
                                <td>
                                    <i class="bi bi-trash delete-icon" @click="removeItem(item.id)"
                                        style="cursor: pointer;"></i>
                                </td>
                            </tr>
                        </tbody> 
         

                    </table>

                    <div v-if="cartIsEmpty" class="text-center py-5">
                        <h5>Your cart is empty</h5>

                        <Link :href="'/products/filter'" class="btn btn-primary mt-3">
                        Continue Shopping

                        </Link>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="p-3 mt-3" style="background:#E6E7E8; border-radius: 10px;" v-if="!cartIsEmpty">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <p class="mb-0">
                                By submitting your order you agree to these terms and conditions. For additional
                                information on availability, click on the Byte Part No.
                                Incoterms: FCA (Duty, customs, and taxes collected at the time of delivery).
                            </p>
                        </div>
                        <div class="col-md-7">
                            <div class="bg-white p-3 rounded shadow-sm">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Merchandise</strong></span>
                                    <span><strong>{{ subtotalReactive }} USD</strong></span>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <span class="ms-4">Shipping:</span>
                                    </div>
                                    <!-- <div class="col-md-8">
                                        <div class="p-3 rounded" style="background:#FFF1F1;">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <span class="d-block"><strong>DHL International
                                                            Express</strong></span>
                                                    <span class="d-block text-muted">Online orders ship today</span>
                                                    <span class="d-block text-muted">Expected Arrival: Jan 08 - Jan
                                                        13</span>
                                                </div>
                                                <span><strong>$1102.50</strong></span>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-end">
                                            <button class="btn d-flex align-items-center" @click="addAddress">
                                                <i class="bi bi-plus-square me-2"></i> More Delivery Options
                                            </button>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <span><strong>Subtotal:</strong></span>
                                    <span><strong>{{ subtotalReactive }}   USD</strong></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <div class="col-md-2">
                                    <button class="btn w-100" :style="{
                                        backgroundColor: '#3B6007',
                                        color: '#fff',
                                        fontSize: '14px',
                                        opacity: cartIsEmpty ? 0.6 : 1,
                                        cursor: cartIsEmpty ? 'not-allowed' : 'pointer'
                                    }" @click="checkout" :disabled="cartIsEmpty">
                                        Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.backBtn span {
    color: #EF4137;
    font-size: 14px;
}

.backBtn i {
    color: #000000;
}

* {
    color: #000000;
    font-size: 14px;
}

.btnWrap {
    background: #373737;
    border-radius: 10px;
    display: flex;
    flex-wrap: wrap;
}

.btnWrap span {
    color: #fff;
}

.btnWrap button {
    background: none;
    border: none;
    color: #fff;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}

.btnWrap button:hover {
    text-decoration: underline;
}

.btnWrap i {
    color: #fff;
}

.checkOut {
    background: #E6E7E8;
    border-radius: 10px;
}

.custom-table thead {
    background: #E6E7E8;
}

.rounded-left {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
}

.rounded-right {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

.custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.custom-table th,
.custom-table td {
    padding: 12px;
    text-align: left;
}

.custom-table tbody tr {
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.quantity-input {
    width: 80px;
    text-align: center;
}

.quick-view {
    color: #F06F68;
    cursor: pointer;
    text-decoration: none;
}

.quick-view:hover {
    text-decoration: underline;
}

.product-img img {
    height: 50px;
    width: 50px;
    object-fit: contain;
}

.product-details span {
    font-size: 12px;
}

.delete-icon {
    color: #EF4137;
    font-size: 16px;
}

.delete-icon:hover {
    opacity: 0.8;
}

.search-results-dropdown {
    position: absolute;
    width: 100%;
    max-width: 500px;
    z-index: 1000;
    margin-top: 0.5rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-height: 300px;
    overflow-y: auto;
    display: block !important;
}

.search-results-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.search-result-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.search-result-item:hover {
    background-color: #f5f5f5;
}

.product-name {
    font-weight: 500;
    display: block;
}

.product-price {
    color: #EF4137;
    font-size: 0.9em;
    display: block;
}

@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .custom-table th,
    .custom-table td {
        white-space: nowrap;
    }

    .btnWrap {
        justify-content: center !important;
    }

    .checkOut .row>div {
        margin-bottom: 10px;
    }
}

@media (max-width: 576px) {
    .btnWrap button {
        font-size: 12px;
    }

    .product-img img {
        height: 40px;
        width: 40px;
    }
}
</style>
