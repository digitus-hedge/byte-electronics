<script setup>
import { computed } from 'vue';

const props = defineProps({
    order: Object,
    currentStatus: {
        type: String,
        default: 'Pending',
        validator: (value) => ['Pending', 'Confirmed', 'Completed', 'Cancelled'].includes(value)
    },
    trackingNumber: {
        type: String,
        default: ''
    },
    statusDates: {
        type: Object,
        default: () => ({
            pending: '',
            confirmed: '',
            completed: '',
            cancelled: ''
        })
    }
});

const emit = defineEmits(['close']);

const statuses = [
    { id: 'Pending', label: 'Order Pending', icon: 'bi-cart-check' },
    { id: 'Confirmed', label: 'Order Confirmed', icon: 'bi-check-circle' },
    { id: 'Completed', label: 'Order Completed', icon: 'bi-house-check' },
    { id: 'Cancelled', label: 'Order Cancelled', icon: 'bi-x-circle' }
];

// Normalize currentStatus to match statuses array
const normalizedStatus = computed(() => {
    const status = props.currentStatus ? props.currentStatus.toLowerCase() : 'pending';
    const validStatus = statuses.find(s => s.id.toLowerCase() === status);
    return validStatus ? validStatus.id : 'Pending';
});

const progressPercentage = computed(() => {
    if (normalizedStatus.value === 'Cancelled') return 100;
    const currentIndex = statuses.findIndex(s => s.id === normalizedStatus.value);
    return (currentIndex / (statuses.length - 1)) * 100;
});

const getStatusClass = (statusId) => {
    if (normalizedStatus.value === 'Cancelled') {
        return statusId === 'Cancelled' ? 'cancelled' : '';
    }

    const currentIndex = statuses.findIndex(s => s.id === normalizedStatus.value);
    const statusIndex = statuses.findIndex(s => s.id === statusId);

    if (statusIndex < currentIndex) {
        return 'completed';
    }
    if (statusIndex === currentIndex) {
        return 'current';
    }
    return '';
};
</script>

<template>
    <div class="">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button @click="emit('close')" class="btn btn-sm btn-light">
                <i class="bi bi-x-lg"></i> Close
            </button>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center p-4">
                        <img :src="order.image" alt="Product" class="img-fluid rounded">
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <table class="w-100 single-order-table">
                    <tbody>
                        <tr>
                            <td>Byte No:</td>
                            <td class="text-end">{{ order.id }}</td>
                            <td>Quantity:</td>
                            <td class="text-end">{{ order.quantity }}</td>
                        </tr>
                        <tr>
                            <td>Mfr. No</td>
                            <td class="text-end">{{ order.mfr_no }}</td>
                            <!-- <td>Availability:</td>
                            <td class="text-end">{{ order.availability }}</td> -->
                        </tr>
                        <tr>
                            <td>Mfr:</td>
                            <td class="text-end">{{ order.manufacturer }}</td>
                            <td style="white-space: nowrap;">Unit Price (AED):</td>
                            <td class="text-end">{{ order.unit_price }}</td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td class="text-end">
                                {{ order.description.replace(/<[^>]*>/g, '') }}</td>
                            <td style="white-space: nowrap;">Ext. Price (AED):</td>
                            <td class="text-end">{{ order.ext_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>
                            <i :class="`bi ${statuses.find(s => s.id === normalizedStatus).icon}`"
                               :style="{color: normalizedStatus === 'Cancelled' ? '#DC3545' : '#FF9800'}"></i>
                            {{ normalizedStatus }}
                        </h4>
                        <p>It is a long established fact that a reader will be distracted by the
                            readable content of a page when looking at its layout. The point of
                            using Lorem Ipsum is that it has</p>
                    </div>
                    <div class="shipping-tracker">
                        <div class="progress-line">
                            <div class="progress" :style="{
                                width: `${progressPercentage}%`,
                                background: normalizedStatus === 'Cancelled' ? '#DC3545' : '#4CAF50'
                            }"></div>

                            <div
                                class="status"
                                v-for="status in statuses"
                                :key="status.id"
                            >
                                <div class="dot" :class="getStatusClass(status.id)">
                                    <i :class="`bi ${status.icon}`"></i>
                                    <div class="hover-content">
                                        <span class="status-label">{{ status.label }}</span>
                                        <span class="status-date">{{ statusDates[status.id.toLowerCase()] }}</span>
                                        <span
                                            class="status-info"
                                            v-if="['Confirmed', 'Completed'].includes(status.id) && trackingNumber"
                                        >
                                            Tracking #{{ trackingNumber }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");
.p-4 {
    background-color: #fff;
}

.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    background-color: #fff;
}

.card-body {
    padding: 1.5rem;
}

.card-body h4 {
    font-weight: 600;
    color: #000000;
}

.img-fluid {
    max-height: 300px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.img-fluid:hover {
    transform: scale(1.05);
}

table {
    border-collapse: separate;
    border-spacing: 0 0.75rem;
}

td {
    padding: 0.5rem 0.5rem;
    vertical-align: middle;
}

.btn-light {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    transition: all 0.2s ease;
}

.btn-light:hover {
    background-color: #f5f5f5;
    transform: translateY(-1px);
}

h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

p {
    color: #000000;
    line-height: 1.6;
    font-size: 12px;
}

@media (max-width: 768px) {
    .row {
        flex-direction: column;
    }

    .col-md-3,
    .col-md-5,
    .col-md-4 {
        width: 100%;
        margin-bottom: 1.5rem;
    }

    table {
        display: block;
    }

    tr {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    td {
        flex: 1 1 50%;
        padding: 0.5rem;
    }

    .status-label {
        display: none;
    }
}

.single-order-table td {
    font-size: 12px;
    color: #000000;
}

.shipping-tracker {
    font-family: sans-serif;
    max-width: 100%;
    padding: 0 1px;
}

.progress-line {
    position: relative;
    width: 100%;
    height: 3px;
    background: #e0e0e0;
    margin: 50px 0;
    display: flex;
    justify-content: space-between;
}

.progress {
    position: absolute;
    height: 100%;
    transition: width 0.4s ease;
}

.status {
    position: relative;
    flex-direction: column;
    align-items: center;
    width: 25px;
}

.dot {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: white;
    border: 3px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: -13px;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 2;
}

.dot i {
    font-size: 12px;
    color: #757575;
}

/* Hover Content */
.hover-content {
    position: absolute;
    top: -70px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 8px 12px;
    border-radius: 6px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    min-width: 120px;
    text-align: center;
    z-index: 10;
}

.hover-content::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid white;
}

.dot:hover .hover-content {
    opacity: 1;
    visibility: visible;
    top: -80px;
}

/* Status states */
.dot.current {
    border-color: #FF9800;
    transform: scale(1.2);
}

.dot.current i {
    color: #FF9800;
}

.dot.completed {
    border-color: #4CAF50;
    background: #4CAF50;
}

.dot.completed i {
    color: white;
}

.dot.cancelled {
    border-color: #DC3545;
    background: #DC3545;
}

.dot.cancelled i {
    color: white;
}

/* Content styling */
.status-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #333;
}

.status-date {
    display: block;
    font-size: 11px;
    color: #666;
    margin: 3px 0;
}

.status-info {
    display: block;
    font-size: 10px;
    color: #FF9800;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hover-content {
        top: -60px;
        min-width: 100px;
        padding: 6px 8px;
    }

    .dot:hover .hover-content {
        top: -65px;
    }

    .status-label {
        font-size: 11px;
    }

    .status-date {
        font-size: 10px;
    }
}


</style>
