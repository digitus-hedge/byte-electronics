<script setup>
import { reactive, ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import BackPage from '@/Components/helpers/BackPage.vue';
import BillingAddress from '@/Components/helpers/BillingAddress.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    user: Object,
    cartItems: Array,
    subtotal: Number,
    customerAddresses: Array,
    countries: Array,
    authUser: Object,
});

console.log('Auth User:', props.authUser);
console.log('Customer Addresses:', props.customerAddresses);
console.log('Cart Items:', props.cartItems);
console.log('Countries:', props.countries);

const trimCountryCode = (phone, countryCode, countryId = null) => {
    console.log('Trimming phone:', { phone, countryCode, countryId, phoneType: typeof phone, countryCodeType: typeof countryCode });
    
    if (!phone) {
        console.log('No trimming applied: phone is missing');
        return '';
    }

    const phoneStr = String(phone).trim();
    let codeStr = countryCode ? String(countryCode).trim() : '';

    // Infer country code from countryId if missing
    if (!codeStr && countryId) {
        const country = props.countries.find(c => c.id === Number(countryId));
        codeStr = country?.country_code || '';
        console.log('Inferred country code from countryId:', codeStr);
    }

    // Fallback: try to match phone against all country codes
    if (!codeStr) {
        const country = props.countries.find(c => phoneStr.startsWith(c.country_code));
        codeStr = country?.country_code || '';
        console.log('Inferred country code from phone match:', codeStr);
    }

    if (!codeStr) {
        console.log('No trimming applied: countryCode is missing');
        return phoneStr;
    }

    if (phoneStr.startsWith(codeStr)) {
        const trimmed = phoneStr.slice(codeStr.length);
        console.log('Trimmed phone:', trimmed);
        return trimmed;
    }

    console.log('No trimming applied: phone does not start with countryCode');
    return phoneStr;
};

const billingAddress = reactive({
    id: null,
    firstName: '',
    lastName: '',
    email: props.authUser?.email || '',
    confirmEmail: props.authUser?.email || '',
    phone: '',
    country_id: null,
    country_code: '',
    company: '',
    attention: '',
    address1: '',
    address2: '',
    city: '',
    state: '',
    postalCode: '',
});

const shippingAddress = reactive({
    id: null,
    firstName: '',
    lastName: '',
    email: '',
    confirmEmail: '',
    phone: '',
    country_id: null,
    country_code: '',
    company: '',
    attention: '',
    address1: '',
    address2: '',
    city: '',
    state: '',
    postalCode: '',
});

const copyToShipping = ref(false);
const shouldValidate = ref(false);
const billingFormRef = ref(null);
const shippingFormRef = ref(null);

const orderSummary = computed(() => ({
    itemCount: props.cartItems?.length || 0,
    subtotal: props.subtotal || 0,
    shipping: 0.00,
    incoterms: 'Incoterms: FCA (Duty, customs, and taxes collected at the time of delivery).',
}));
const orderTotal = computed(() => {
    return (orderSummary.value.subtotal + orderSummary.value.shipping).toFixed(2);
});
const selectBillingAddress = (addressId) => {
    const id = Number(addressId);
    console.log('Selecting billing address:', id);
    const address = props.customerAddresses.find((addr) => addr.id === id);
    if (address) {
        console.log('Found billing address:', address);
        Object.assign(billingAddress, {
            id: address.id,
            firstName: props.user?.name || '', // Fallback to auth user name
            lastName: '',
            email: props.authUser?.email || '',
            confirmEmail: props.authUser?.email || '',
            phone: trimCountryCode(address.phone, address.country_code, address.country_id),
            country_id: address.country_id || null,
            country_code: address.country_code || '',
            company: address.company_name || '',
            attention: address.attention || '',
            address1: address.address_line1 || '',
            address2: address.address_line2 || '',
            city: address.city || '',
            state: address.state || '',
            postalCode: address.postal_code || '',
        });
    } else {
        console.log('Resetting billing address');
        Object.assign(billingAddress, {
            id: null,
            firstName: props.user?.name || '',
            lastName: '',
            email: props.authUser?.email || '',
            confirmEmail: props.authUser?.email || '',
            phone: '',
            country_id: null,
            country_code: '',
            company: '',
            attention: '',
            address1: '',
            address2: '',
            city: '',
            state: '',
            postalCode: '',
        });
    }
};

const selectShippingAddress = (addressId) => {
    if (copyToShipping.value) return;
    const id = Number(addressId);
    console.log('Selecting shipping address:', id);
    const address = props.customerAddresses.find((addr) => addr.id === id);
    if (address) {
        console.log('Found shipping address:', address);
        Object.assign(shippingAddress, {
            id: address.id,
            firstName: props.user?.name || '', // Fallback to auth user name
            lastName: '',
            email: '',
            confirmEmail: '',
            phone: trimCountryCode(address.phone, address.country_code, address.country_id),
            country_id: address.country_id || null,
            country_code: address.country_code || '',
            company: address.company_name || '',
            attention: address.attention || '',
            address1: address.address_line1 || '',
            address2: address.address_line2 || '',
            city: address.city || '',
            state: address.state || '',
            postalCode: address.postal_code || '',
        });
    } else {
        console.log('Resetting shipping address');
        Object.assign(shippingAddress, {
            id: null,
            firstName: props.user?.name || '',
            lastName: '',
            email: '',
            confirmEmail: '',
            phone: '',
            country_id: null,
            country_code: '',
            company: '',
            attention: '',
            address1: '',
            address2: '',
            city: '',
            state: '',
            postalCode: '',
        });
    }
};
watch(copyToShipping, (shouldCopy) => {
    if (shouldCopy) {
        console.log('Copying billing to shipping');
        Object.assign(shippingAddress, {
            ...billingAddress,
            id: null,
            email: '',
            confirmEmail: '',
        });
    }
});

watch(
    billingAddress,
    (newBilling) => {
        if (copyToShipping.value) {
            console.log('Syncing billing to shipping');
            Object.assign(shippingAddress, {
                ...newBilling,
                id: null,
                email: '',
                confirmEmail: '',
            });
        }
    },
    { deep: true }
);

watch(
    () => billingAddress.country_id,
    (newCountryId) => {
        const country = props.countries.find(c => c.id === Number(newCountryId));
        billingAddress.country_code = country?.country_code || '';
        console.log('Updated billing country_code:', billingAddress.country_code);
    }
);

watch(
    () => shippingAddress.country_id,
    (newCountryId) => {
        const country = props.countries.find(c => c.id === Number(newCountryId));
        shippingAddress.country_code = country?.country_code || '';
        console.log('Updated shipping country_code:', shippingAddress.country_code);
    }
);

const submitAddresses = async () => {
    shouldValidate.value = true;

    const isBillingValid = await billingFormRef.value?.validate();
    const isShippingValid = await shippingFormRef.value?.validate();

    if (!isBillingValid || !isShippingValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            text: 'Please check your billing and shipping information.',
            confirmButtonText: 'OK',
            background: '#E6E7E8',
            color: '#2E2E2E',
            iconColor: '#F44336',
            confirmButtonColor: '#F44336',
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                htmlContainer: 'custom-swal-text',
                confirmButton: 'custom-swal-button',
            },
            didOpen: () => {
                const style = document.createElement('style');
                style.textContent = `
                    .custom-swal-title { font-size: 18px !important; }
                    .custom-swal-text { font-size: 14px !important; }
                    .custom-swal-button { font-size: 13px !important; letter-spacing: 0.5px; }
                `;
                document.head.appendChild(style);
            },
        });
        return;
    }

    const result = await Swal.fire({
        title: 'Confirm Checkout',
        text: 'Do you want to proceed with the checkout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B6007',
        cancelButtonColor: '#EF4137',
        confirmButtonText: 'Yes, checkout!',
        cancelButtonText: 'No, cancel',
        background: '#E6E7E8',
        color: '#2E2E2E',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            htmlContainer: 'custom-swal-text',
            confirmButton: 'custom-swal-button',
        },
        didOpen: () => {
            const style = document.createElement('style');
            style.textContent = `
                .custom-swal-title { font-size: 18px !important; }
                .custom-swal-text { font-size: 14px !important; }
                .custom-swal-button { font-size: 13px !important; }
            `;
            document.head.appendChild(style);
        },
    });

    if (result.isConfirmed) {
        try {
            const cartItemsForSubmission = props.cartItems.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price,
            }));

            const checkoutData = {
                billing_address: {
                    id: billingAddress.id || null,
                    address_name: billingAddress.firstName,
                    company_name: billingAddress.company,
                    address_line1: billingAddress.address1,
                    address_line2: billingAddress.address2,
                    city: billingAddress.city,
                    state: billingAddress.state,
                    postal_code: billingAddress.postalCode,
                    attention: billingAddress.attention,
                    phone: billingAddress.phone ? `${billingAddress.phone}` : '',
                    country_id: billingAddress.country_id,
                    country_code: billingAddress.country_code,
                    email: billingAddress.email,
                },
                shipping_address: {
                    id: shippingAddress.id || null,
                    address_name: shippingAddress.firstName,
                    company_name: shippingAddress.company,
                    address_line1: shippingAddress.address1,
                    address_line2: shippingAddress.address2,
                    city: shippingAddress.city,
                    state: shippingAddress.state,
                    postal_code: shippingAddress.postalCode,
                    attention: shippingAddress.attention,
                    phone: shippingAddress.phone ? `${shippingAddress.phone}` : '',
                    country_id: shippingAddress.country_id,
                    country_code: shippingAddress.country_code,
                    email: shippingAddress.email || billingAddress.email,
                },
                cart_items: cartItemsForSubmission,
                subtotal: orderSummary.value.subtotal,
                shipping: orderSummary.value.shipping,
                total: orderTotal.value,
            };

            console.log('Submitting checkout data:', checkoutData);

            await router.post('/checkout', checkoutData, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Submitted.',
                        text: 'Your order has been successfully submitted. We will soon send the order details to your email. Our team will review the order shortly and send a confirmation email with the payment link.',
                        confirmButtonText: 'View Orders',
                        background: '#E6E7E8',
                        color: '#2E2E2E',
                        iconColor: '#4CAF50',
                        confirmButtonColor: '#4CAF50',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'custom-swal-button',
                        },
                    }).then((result) => {
                        console.log('Checkout success result:', result);
                        if (result.isConfirmed) {
                            router.visit('/my-orders');
                        }
                    });
                },
                onError: (errors) => {
                    console.error('Checkout error:', errors);
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout Failed',
                        text: 'There was an error processing your order. Please try again.',
                        confirmButtonText: 'OK',
                        background: '#E6E7E8',
                        color: '#2E2E2E',
                        iconColor: '#F44336',
                        confirmButtonColor: '#F44336',
                    });
                    console.error('Checkout errors:', errors);
                },
            });
        } catch (error) {
            console.error('Checkout error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Checkout Failed',
                text: 'An unexpected error occurred. Please try again.',
                confirmButtonText: 'OK',
                background: '#E6E7E8',
                color: '#2E2E2E',
                iconColor: '#F44336',
                confirmButtonColor: '#F44336',
            });
        }
    }
};
</script>

<template>
    <AppLayout>
        <BreadCrums />
        
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <BackPage :label="'BACK TO BASKET'" :url="`/cart`" />
            <div class="row col-md-12">
                <div class="col-md-8">
                    <div class="card mt-3 w-100 p-2 address-card">
                        <h5 class="m-0" style="color: #fff;">ADDRESSES</h5>
                    </div>

                    <!-- Billing Address -->
                    <div class="mt-3 d-flex align-items-center gap-2 w-100">
                        <i class="bi bi-clipboard-data"></i>
                        <span class="fw-bold text-dark">Billing Information</span>
                    </div>

                    <!-- Billing Address Dropdown (only for authenticated users) -->
                    <div v-if="authUser" class="mt-3">
                        <label for="billingAddressSelect">Select Billing Address</label>
                        <select
                            id="billingAddressSelect"
                            class="form-control shadow-none"
                            @change="selectBillingAddress($event.target.value)"
                        >
                            <option value="">Select an address</option>
                            <option v-for="address in customerAddresses" :key="address.id" :value="address.id">
                                {{ address.address_name }} - {{ address.address_line1 }}, {{ address.city }}
                            </option>
                        </select>
                    </div>

                    <BillingAddress
                        ref="billingFormRef"
                        v-model="billingAddress"
                        :is-billing="true"
                        :should-validate="shouldValidate"
                        :countries="countries"
                        :user="user"
                        :is-authenticated="!!authUser"
                    />

                    <!-- Copy to Shipping Checkbox -->
                    <div class="row mt-3 align-items-center">
                        <div class="col">
                            <div class="form-check">
                                <input
                                    v-model="copyToShipping"
                                    class="form-check-input"
                                    style="box-shadow: none !important; outline: none !important;"
                                    type="checkbox"
                                    id="copyDetailsCheckbox"
                                />
                                <label class="form-check-label" for="copyDetailsCheckbox">
                                    Copy billing details to shipping information?
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mt-3 d-flex align-items-center gap-2 w-100">
                        <i class="bi bi-truck"></i>
                        <span class="fw-bold text-dark">Shipping Information</span>
                    </div>

                    <!-- Shipping Address Dropdown (only for authenticated users) -->
                    <div v-if="authUser" class="mt-3">
                        <label for="shippingAddressSelect">Select Shipping Address</label>
                        <select
                            id="shippingAddressSelect"
                            class="form-control shadow-none"
                            @change="selectShippingAddress($event.target.value)"
                            :disabled="copyToShipping"
                        >
                            <option value="">Select an address</option>
                            <option v-for="address in customerAddresses" :key="address.id" :value="address.id">
                                {{ address.address_name }} - {{ address.address_line1 }}, {{ address.city }}
                            </option>
                        </select>
                    </div>

                    <BillingAddress
                        ref="shippingFormRef"
                        v-model="shippingAddress"
                        :is-billing="false"
                        :disabled="copyToShipping"
                        :copied-from-billing="copyToShipping"
                        :should-validate="shouldValidate"
                        :countries="countries"
                        :user="user"
                        :is-authenticated="!!authUser"
                    />

                    <!-- Submit Button -->
                    <div class="mt-4 w-100 d-flex justify-content-end">
                        <button @click="submitAddresses" class="btn" style="background: #3B6007; color: #fff;">
                            Confirm Checkout
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-4 mt-3" style="z-index: 0;">
                    <div class="card summary-card p-3 p-md-4 shadow-sm">
                        <h5 class="card-title mb-3 mb-md-4 fw-bold" style="font-size: 14px; color: #000000;">
                            Order Summary
                            <span class="text-muted" style="font-size: 12px; " >
                                ({{ orderSummary.itemCount }} item<span v-if="orderSummary.itemCount > 1">s</span>)
                            </span>
                        </h5>

                        <div class="card order-total-card p-2 p-md-3 mb-2 mb-md-3 border-0 bg-light">
                            <table class="w-100">
                                <tr class="border-bottom">
                                    <td class="py-1 py-md-2" style="font-size: 12px; color: #000000;">Merchandise Total:</td>
                                    <td class="py-1 py-md-2 text-end" style="font-size: 12px; color: #000000;">
                                        {{ orderSummary.subtotal.toFixed(2) }} AED
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="py-1 py-md-2" style="font-size: 12px; color: #000000;">Delivery Charge:</td>
                                    <td class="py-1 py-md-2 text-end" style="font-size: 12px; color: #000000;">
                                        {{ orderSummary.shipping.toFixed(2) }} AED
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 py-md-2 fw-bold" style="font-size: 12px; color: #000000;">Order Total:</td>
                                    <td class="py-1 py-md-2 text-end fw-bold" style="font-size: 12px; color: #000000;">
                                        {{ orderTotal }} AED
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <p class="text-muted small mb-0" style="font-size: 10px;">
                            <em>{{ orderSummary.incoterms }}</em>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.address-card {
    background: #EF4137;
}
.address-card h5 {
    color: #000000;
    font-weight: 700;
    padding: 0 10px;
}
.disabled-address {
    opacity: 0.7;
    pointer-events: none;
}
.summary-card {
    background: #E6E7E8;
}
.order-total-card {
    background: #FFF1F1 !important;
    z-index: 1000;
}
select.form-control {
    font-size: 14px;
    color: #000000;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
}
select.form-control:focus {
    border-color: #3B6007;
    box-shadow: none;
}
</style>