<script setup>
import SingleOrder from '@/Components/SingleOrder/SingleOrder.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array,
    search: String,
    email: String,
    type: String,
});

const identificationType = ref(props.type || 'Order Number');
const searchQuery = ref(props.search || '');
const email = ref(props.email || '');

const currentPage = ref(1);
const itemsPerPage = ref(10);

const paginatedOrders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return props.orders.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(props.orders.length / itemsPerPage.value);
});

const deleteOrder = (orderId) => {
    if (confirm(`Are you sure you want to delete order ${orderId}?`)) {
        router.delete(`/orders/${orderId}`, {
            preserveScroll: true,
            onSuccess: () => {
                console.log(`Order ${orderId} deleted successfully`);
            },
            onError: (errors) => {
                console.error('Failed to delete order:', errors);
                alert('Failed to delete order. Please try again.');
            },
        });
    }
};

const searchOrders = () => {
    router.get('/my-orders', {
        search: searchQuery.value,
        email: email.value,
        type: identificationType.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['orders'],
    });
};

const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const selectedOrderId = ref(null);
const selectedOrder = ref(null);

const showOrderDetails = (orderId) => {
    selectedOrderId.value = orderId;
    selectedOrder.value = props.orders.find(order => order.id === orderId);
};

const hideOrderDetails = () => {
    selectedOrderId.value = null;
    selectedOrder.value = null;
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <h4 class="mt-3 mb-4 fw-bold page-title">My Orders</h4>
            <div class="card col-md-12 filter-card" v-if="!selectedOrder">
                <div class="card-body p-4">
                    <div class="row align-items-end g-3">
                        <!-- <div class="col-md-3">
                            <label for="identificationType" class="form-label">Choose type of identification <span class="required">*</span></label>
                            <select class="form-select select-field" id="identificationType" v-model="identificationType">
                                <option>Order Number</option>
                                <option>Byte No.</option>
                                <option>Mfr. No.</option>
                                <option>Product Name</option>
                            </select>
                        </div> -->

                        <div class="col-md-3">
                            <label for="searchQuery" class="form-label">Search Query <span class="required">*</span></label>
                            <input type="text" class="form-control input-field" id="searchQuery" v-model="searchQuery" :placeholder="'Enter ' + identificationType">
                        </div>

                        <div class="col-md-3">
                            <label for="email" class="form-label">Email used for this order <span class="required">*</span></label>
                            <input type="email" class="form-control input-field" id="email" v-model="email" placeholder="your@email.com">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn search-btn" @click="searchOrders">
                                Search Orders
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <pre>{{ order }}</pre> -->
            <div v-if="selectedOrder" class="col-12 mt-4">
                <SingleOrder
                    :order="selectedOrder"
                    :currentStatus="selectedOrder.status.toLowerCase()"
                    :trackingNumber="selectedOrder.tracking_number || 'N/A'"
                    :statusDates="{
                        placed: selectedOrder.created_at || 'N/A',
                        processing: selectedOrder.processing_at || 'N/A',
                        shipped: selectedOrder.shipped_at || 'N/A',
                        transit: selectedOrder.transit_at || 'N/A',
                        delivered: selectedOrder.delivered_at || 'N/A'
                    }"
                    @close="hideOrderDetails" />
            </div>

            <div v-else class="col-12 mt-4">
                <div class="table-responsive rounded-3 shadow-sm">
                    <table class="table table-borderless align-middle mb-0 order-tble">
                        <thead class="bg-light-primary">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold text-uppercase small text-secondary">Serial No.</th>
                                <!-- <th class="py-3 fw-semibold text-uppercase small text-secondary text-center">Order No.</th> -->
                                <th class="py-3 fw-semibold text-uppercase small text-secondary">Product Details</th>
                                <th class="py-3 fw-semibold text-uppercase small text-secondary">Description</th>
                                <th class="py-3 fw-semibold text-uppercase small text-secondary text-center">Quantity</th>

                                <!-- <th class="py-3 fw-semibold text-uppercase small text-secondary">Availability</th> -->
                                <th class="py-3 fw-semibold text-uppercase small text-secondary text-end">Unit Price (AED)</th>
                                <th class="py-3 fw-semibold text-uppercase small text-secondary text-end">Ext. Price (AED)</th>
                                <th class="pe-4 py-3 fw-semibold text-uppercase small text-secondary text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="border-top">
                            <tr v-for="(order, index) in paginatedOrders" :key="`${order.id}-${index}`" @click="showOrderDetails(order.id)" class="hover-shadow bg-white cursor-pointer">
                                        <!-- <code>{{ order.image }}</code> -->
                                        <!-- <pre>{{ order }}</pre> -->
                                <td class="ps-4 py-3 fw-medium" data-label="Serial No.">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                                <!-- <td class="ps-4 py-3 fw-medium" data-label="Order No.">{{ order.id }}</td> -->
                                <td class="py-3" data-label="Product Details">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-2 me-3" style="width: 64px; height: 64px;">
                                            <img :src="order.image" alt="Product" class="img-fluid" style="object-fit: contain; width: 100%; height: 100%;">
                                        </div>
                                        <div>
                                            <ul class="list-unstyled small text-muted mb-0">
                                                <!-- <li class="d-flex justify-content-between">
                                                    <span class="me-2">Byte No:</span>
                                                    <span class="fw-medium text-dark">{{ order.byte_no }}</span>
                                                </li> -->
                                                <li class="d-flex justify-content-between">
                                                    <span class="me-2">Mfr. No:</span>
                                                    <span class="fw-medium text-dark">{{ order.mfr_no }}</span>
                                                </li>
                                                <li class="d-flex justify-content-between">
                                                    <span class="me-2">Manufacturer:</span>
                                                    <span class="fw-medium text-dark">{{ order.manufacturer }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3" data-label="Description">
                                    <p class="mb-1">{{ order.product_name }}</p>
                                    <p>{{ order.description.replace(/<[^>]*>/g, '') }}</p>
                                    <a href="#" class="d-inline-flex align-items-center text-decoration-none" style="color:#EF4137;">
                                        Quick view
                                    </a>
                                </td>
                                <td class="py-3 text-center" data-label="Quantity">{{ order.quantity }}</td>
                                <!-- <td class="py-3" data-label="Availability">{{ order.availability }}</td> -->
                                <td class="py-3 text-end fw-medium" data-label="Unit Price">{{ order.unit_price }}</td>
                                <td class="py-3 text-end fw-bold text-primary" data-label="Ext. Price">{{ order.ext_price }}</td>
                                <td class="py-3 text-end fw-bold text-primary" data-label="Ext. Price">{{ order.status }}</td>
                                <!-- <td class="pe-4 py-3 text-center" data-label="">
                                    <button @click.stop="deleteOrder(order.id)" class="btn btn-sm btn-icon btn-hover-danger bg-danger bg-opacity-10 rounded-2">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-3">
                    <div class="d-flex align-items-center">
                        <span class="me-3 text-muted small">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                            {{ Math.min(currentPage * itemsPerPage, props.orders.length) }} of
                            {{ props.orders.length }} entries
                        </span>

                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="changePage(currentPage - 1)" aria-label="Previous">
                                        <span aria-hidden="true">«</span>
                                    </button>
                                </li>

                                <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                                    <button class="page-link" @click="changePage(page)">
                                        {{ page }}
                                    </button>
                                </li>

                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="changePage(currentPage + 1)" aria-label="Next">
                                        <span aria-hidden="true">»</span>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.page-title {
    font-size: 1.3rem;
    color: #000000;
    position: relative;
    padding-bottom: 0.5rem;
}

.filter-card {
    background: #F9F9F9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    border: none;
}

.form-label {
    font-size: 0.875rem;
    color: #000000;
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.required {
    color: #EF4137;
}

.select-field,
.input-field {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.select-field:focus,
.input-field:focus {
    border-color: #EF4137;
    box-shadow: 0 0 0 0.25rem rgba(239, 65, 55, 0.15);
}

.search-btn {
    background: #EF4137;
    color: #FFFFFF;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.625rem 1.25rem;
    border-radius: 6px;
    border: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-btn:hover {
    background: #d6372e;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 65, 55, 0.2);
}

.search-btn:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .filter-card .col-md-3 {
        width: 100%;
    }
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.bg-light-primary {
    background-color: rgba(239, 65, 55, 0.05);
}

.btn-hover-danger:hover {
    background-color: #EF4137 !important;
}

.btn-hover-danger:hover i {
    color: white !important;
}

.order-tble th {
    color: #000000 !important;
    font-weight: 600;
    font-size: 14px !important;
    white-space: nowrap;
}

.order-tble td {
    font-size: 14px !important;
    color: #000000 !important;
    font-weight: 400 !important;
    vertical-align: middle;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.page-item.active .page-link {
    background-color: #fff;
    border-color: #fff;
}

.page-link {
    color: #EF4137;
}

.page-link:hover {
    color: #d6372e;
}
</style>
