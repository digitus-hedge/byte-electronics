<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    orders: Array,
    tab: String,
    search: String,
});

const statusClasses = {
    'Pending': 'bg-processing',
    'Confirmed': 'bg-shipped',
    'Completed': 'bg-delivered',
    'Cancelled': 'bg-cancelled'
};

const paymentClasses = {
    'Paid': 'bg-paid',
    'Pending': 'bg-pending',
    'Failed': 'bg-failed'
};

const activeTab = ref(props.tab || 'ALL ORDERS');
const searchQuery = ref(props.search || '');

const filterOrders = (tab) => {
    activeTab.value = tab;
    router.get('/profile', { tab, search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'tab', 'search'],
    });
};

const searchOrders = () => {
    router.get('/profile', { search: searchQuery.value, tab: activeTab.value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'tab', 'search'],
    });
};
// const viewOrder = (orderId) => {

//     router.get(route('getOrders', { id: orderId }));
// };
const viewOrder = (orderId) => {
  console.log('Raw ID:', orderId);
  console.log('Char codes:', Array.from(orderId).map(c => c.charCodeAt(0)));

  const cleanId = orderId.replace(/#/g, '');
  router.get(`/get-orders/${cleanId}`);
};
</script>

<template>
    <div class="order-history-container">
        <!-- Search Form -->
        <div class="search-container m-2">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by order ID (e.g., ORD123)"
                class="form-control input-field"
                @keyup.enter="searchOrders"
            />
            <button class="btn search-btn" @click="searchOrders">
                Search
            </button>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs-container">
            <div class="tabs-scroll">
                <button
                    class="tab"
                    :class="{ active: activeTab === 'ALL ORDERS' }"
                    @click="filterOrders('ALL ORDERS')"
                >
                    ALL ORDERS
                </button>
                <button
                    class="tab"
                    :class="{ active: activeTab === 'Pending' }"
                    @click="filterOrders('Pending')"
                >
                    PENDING
                </button>
                <button
                    class="tab"
                    :class="{ active: activeTab === 'Confirmed' }"
                    @click="filterOrders('Confirmed')"
                >
                    CONFIRMED
                </button>
                <button
                    class="tab"
                    :class="{ active: activeTab === 'Completed' }"
                    @click="filterOrders('Completed')"
                >
                    COMPLETED
                </button>
                <button
                    class="tab"
                    :class="{ active: activeTab === 'Cancelled' }"
                    @click="filterOrders('Cancelled')"
                >
                    CANCELLED
                </button>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="desktop-table">
            <table>
                <thead>
                    <tr>
                        <th>Serial No.</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Payment</th>
                        <!-- <th>Time Remaining</th> -->
                        <!-- <th>Type</th> -->
                        <th>Status</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(order, index) in orders" :key="index"   @click="viewOrder(order.id.replace('#', ''))">
                    <!-- <pre>{{ order.id }}</pre> -->
                        <td>{{ index + 1 }}</td>
                        <td>{{ order.id }}</td>
                        <td>{{ order.name }}</td>

                        <td>

                        <span :class="['badge', paymentClasses[order.payment]]" v-if="order.status=='Confirmed'"> Payment Collected</span>
                        <span :class="['badge', paymentClasses[order.payment]]" v-else> {{ order.payment }}</span>

                        </td>
                        <!-- <td>{{ order.timeLeft }}</td> -->
                        <!-- <td>{{ order.type }}</td> -->
                        <td><span :class="['badge', statusClasses[order.status]]">{{ order.status }}</span></td>
                        <td class="text-end">{{ order.total }}</td>
                    </tr>
                    <tr v-if="orders.length === 0">
                        <td colspan="6" class="text-center py-4">No orders found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="mobile-cards">
            <div v-for="(order, index) in orders" :key="index" class="order-card">
                <div class="card-header">
                    <div class="order-id">{{ order.id }}</div>
                    <div class="order-status">
                        <span :class="['badge', statusClasses[order.status]]">{{ order.status }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <span class="label">Name:</span>
                        <span class="value">{{ order.name }}</span>
                    </div>
                    <!-- <div class="row">
                        <span class="label">Payment:</span>
                        <span :class="['value', 'badge', paymentClasses[order.payment]]">{{ order.payment }}</span>
                    </div> -->
                    <!-- <div class="row">
                        <span class="label">Type:</span>
                        <span class="value">{{ order.type }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Time Left:</span>
                        <span class="value">{{ order.timeLeft }}</span>
                    </div> -->
                </div>
                <div class="card-footer">
                    <span class="total-label">Total:</span>
                    <span class="total-value">{{ order.total }}</span>
                </div>
            </div>
            <div v-if="orders.length === 0" class="no-orders-message">
                No orders found
            </div>
        </div>
    </div>
</template>

<style scoped>
.order-history-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.search-container {
    display: flex;
    gap: 10px;
}

.input-field {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.search-btn {
    background: #EF4137;
    color: #FFFFFF;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
}

.search-btn:hover {
    background: #d6372e;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.text-end {
    text-align: right;
}

.bg-processing { color: #FFC107; }
.bg-shipped { color: #17A2B8; }
.bg-delivered { color: #28A745; }
.bg-cancelled { color: #DC3545; }
.bg-paid { color: #28A745; }
.bg-pending { color: #FFC107; }
.bg-failed { color: #DC3545; }

.tabs-container {
    border-bottom: 1px solid #e0e0e0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.tabs-container::-webkit-scrollbar {
    display: none;
}

.tabs-scroll {
    display: flex;
    min-width: max-content;
}

.tab {
    padding: 12px 16px;
    font-weight: 600;
    font-size: 16px;
    color: #CFCFCF;
    background: none;
    border: none;
    position: relative;
    cursor: pointer;
    white-space: nowrap;
}

.tab.active {
    color: #000000;
}

.tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #EF4137;
}

.desktop-table {
    display: block;
    width: 100%;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.desktop-table::-webkit-scrollbar {
    display: none;
}

.desktop-table table {
    width: 100%;
    border-collapse: collapse;
}

.desktop-table th {
    padding: 12px 16px;
    font-weight: 600;
    color: #000000;
    font-size: 14px;
    text-align: left;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.desktop-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    color: #000000;
    font-size: 14px;
}

.desktop-table tr:last-child td {
    border-bottom: none;
}

.mobile-cards {
    display: none;
}

.order-card {
    border-bottom: 1px solid #f0f0f0;
    padding: 16px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
}

.order-id {
    font-weight: 600;
}

.card-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
}

.row {
    display: flex;
    flex-direction: column;
}

.label {
    font-size: 12px;
    color: #666;
}

.value {
    font-size: 14px;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    font-weight: 600;
}

.total-value {
    font-weight: 600;
    font-size: 16px;
}

.no-orders-message {
    text-align: center;
    padding: 16px;
    color: #666;
}

@media (max-width: 768px) {
    .desktop-table {
        display: none;
    }

    .mobile-cards {
        display: block;
    }

    .tabs-scroll {
        padding: 0 16px;
    }

    .search-container {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .card-body {
        grid-template-columns: 1fr;
    }

    .tab {
        padding: 12px 12px;
        font-size: 14px;
    }
}
</style>
