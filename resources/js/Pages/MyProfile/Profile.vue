<script setup>
import AddressAccordion from '@/Components/helpers/AddressAccordion.vue';
import AddressForm from '@/Components/helpers/AddressForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Swal from 'sweetalert2';
import OrderHistory from '@/Components/helpers/OrderHistory.vue';

const props = defineProps({
    user: Object,
    customerAddresses: Array,
    countries: Array,
    orders: Array, // Added orders prop
    tab: String,
    search: String,
    verified: String,
});
// 'user' => $user,
//             'customerAddresses' => $customerAddresses,
//             'countries' => $countries,
//             'orders' => $orders,
//             'tab' => $tab,
//             'search' => $search,
//             'verified' => $verified,

const getCountryDetails = (countryId) => {
    const country = props.countries.find(c => c.id === countryId);
    return country ? { name: country.name, code: `+${country.country_code}` } : { name: '', code: '' };
};

const addresses = ref(
    Array.isArray(props.customerAddresses)
        ? props.customerAddresses.map(address => {
            const countryId = address.country_id || '';
            const { code: countryCode, name: countryName } = getCountryDetails(countryId);
            return {
                user_id: props.user?.id || 'N/A',
                address_id: address.id || null,
                addressName: address.address_name || 'Work Address',
                userName: props.user?.name || 'Unknown',
                email: props.user?.email || 'N/A',
                company_name: address.company_name || '',
                address_line_1: address.address_line1 || '',
                address_line_2: address.address_line2 || '',
                city: address.city || '',
                state: address.state || '',
                postal_code: address.postal_code || '',
                attention: address.attention || '',
                country_id: countryId,
                countryCode: countryCode,
                countryName: countryName,
                phone: ` ${address.phone || ''}`.trim()
            };
        })
        : []
);

const showAddressForm = ref(false);
const isEditing = ref(false);
const currentAddress = ref(null);
const showOnlyOrderHistory = ref(false);

const viewOrderHistory = () => {
    showOnlyOrderHistory.value = true;
};

const handleShowForm = () => {
    isEditing.value = false;
    currentAddress.value = null;
    showAddressForm.value = true;
};

const handleEditAddress = (address) => {
    isEditing.value = true;
    currentAddress.value = { ...address };
    showAddressForm.value = true;
};

const handleCancel = () => {
    showAddressForm.value = false;
};

const handleSubmit = async (addressData) => {
    const url = isEditing.value ? `/profile/address/${currentAddress.value.address_id}` : '/profile/address';
    const method = isEditing.value ? 'put' : 'post';

    try {
        await router[method](url, addressData, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Success!',
                    text: isEditing.value ? 'Address updated successfully!' : 'Address added successfully!',
                    icon: 'success',
                    confirmButtonColor: '#EF4137'
                });
                showAddressForm.value = false;
                if (isEditing.value) {
                    const index = addresses.value.findIndex(a => a.address_id === currentAddress.value.address_id);
                    if (index !== -1) addresses.value[index] = { ...addressData, address_id: currentAddress.value.address_id };
                } else {
                    router.reload({
                        only: ['customerAddresses'], onSuccess: () => {
                            addresses.value = Array.isArray(props.customerAddresses)
                                ? props.customerAddresses.map(address => ({
                                    user_id: props.user?.id || 'N/A',
                                    address_id: address.id || null,
                                    addressName: address.address_name || 'Work Address',
                                    userName: props.user?.name || 'Unknown',
                                    email: props.user?.email || 'N/A',
                                    company_name: address.company_name || '',
                                    address_line_1: address.address_line1 || '',
                                    address_line_2: address.address_line2 || '',
                                    city: address.city || '',
                                    state: address.state || '',
                                    postal_code: address.postal_code || '',
                                    attention: address.attention || '',
                                    phone: ` ${address.phone || ''}`.trim()
                                }))
                                : [];
                        }
                    });
                }
            },
            onError: (errors) => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save address: ' + (Object.values(errors)[0] || 'Unknown error'),
                    icon: 'error',
                    confirmButtonColor: '#EF4137'
                });
            }
        });
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred.',
            icon: 'error',
            confirmButtonColor: '#EF4137'
        });
    }
};

const resendVerification = () => {
    router.post(route('verification.send'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'A new verification link has been sent to your email.',
                icon: 'success',
                confirmButtonColor: '#EF4137',
                timer: 2000,
            });
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: errors.message || 'Failed to send verification link. Please try again.',
                icon: 'error',
                confirmButtonColor: '#EF4137',
            });
        },
    });
};

// Handle success messages (e.g., from verification redirect if home is /profile)
watch(() => props.success, (newSuccess) => {
    if (newSuccess) {
        Swal.fire({
            title: 'Success!',
            text: newSuccess,
            icon: 'success',
            confirmButtonColor: '#EF4137',
            timer: 2000,
        });
    }
});
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <div class="row w-100">
                <h4 class="fw-bold mb-3">My Account</h4>

                <div class="col-md-3">
                    <div class="card sidebar-card">
                        <ul class="list-group list-group-flush sidebar-menu">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <Link href="#" class="menu-link">Personal Information</Link>
                                <i class="bi bi-chevron-right menu-icon"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center"  @click="viewOrderHistory">
                                <label  class="menu-link" style="font-size: 14px;">Order Status and History</label>
                                <i class="bi bi-chevron-right menu-icon"></i>
                            </li>
                            <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                <Link href="#" class="menu-link">BOMs, Carts & Projects</Link>
                                <i class="bi bi-chevron-right menu-icon"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <Link href="#" class="menu-link">Forms</Link>
                                <i class="bi bi-chevron-right menu-icon"></i>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <Link href="#" class="menu-link">Additional Information</Link>
                                <i class="bi bi-chevron-right menu-icon"></i>
                            </li> -->
                        </ul>
                    </div>
                </div>
                <div class="col-md-4" v-if="!showAddressForm && !showOnlyOrderHistory">
                    <div class="card contact-card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h6 class="m-0 fw-bold">Contact Information</h6>
                            <i class="bi bi-person-fill-gear contact-icon"></i>
                        </div>
                        <div class="card-body p-0">
                            <table class="table contact-table m-0">
                                <tbody>
                                    <tr>
                                        <td class="label-cell">Name</td>
                                        <td class="value-cell">{{ user.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">E-mail Address</td>
                                        <td class="value-cell">{{ user.email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Country</td>
                                        <td class="value-cell">India</td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Email verification status</td>
                                        <td class="value-cell" style="    display: flex
;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;">{{ verified }}
                                            <button v-if="verified === 'Unverified❌'" class="btn p-1 w-15"
                                                @click="resendVerification"
                                                style="background: rgb(239, 65, 55); font-size: 12px; color: rgb(255, 255, 255);">
                                                Resend Email
                                            </button>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-5" v-if="!showAddressForm && !showOnlyOrderHistory">
                    <AddressAccordion :addresses="addresses" @show-form="handleShowForm"
                        @edit-address="handleEditAddress" />
                </div>

                <div class="col-md-6" v-if="showAddressForm && !showOnlyOrderHistory">
                    <AddressForm :address="currentAddress" :countries="countries" :is-editing="isEditing"
                        @cancel="handleCancel" @submit="handleSubmit" />
                </div>
                <div class="col-md-12" v-if="!showOnlyOrderHistory">
                    <div class="row justify-content-end">
                        <div class="col-md-9">
                            <div class="card order-history-card text-center p-4 mt-4">
                                <h3 class="card-title mb-0" style="color: #000000; font-weight: 700;">Order History</h3>
                                <p class="card-subtitle text-muted mb-4 mt-4" style="color: #000000 !important;">
                                    View your recent orders, find tracking information, or initiate a return.
                                </p>
                                <button class="btn btn-primary order-history-btn mx-auto" @click="viewOrderHistory">
                                    Visit Order History
                                </button>
                            </div>
                            <div class="disclaimer-container">
                                <p class="disclaimer-text" style="font-size: 12px;">
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                                    when an unknown printer took a galley of type and scrambled it to make a type
                                    specimen book.
                                </p>
                                <Link class="privacy-link" :href="`/privacy-policy`">
                                    <i class="bi bi-shield-lock"></i>
                                    Privacy Policy
                                Visit the Privacy Centre to learn more
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 order_history" v-if="showOnlyOrderHistory">
                    <OrderHistory :orders="orders" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
a {
    color: #000000;
    font-size: 14px;
}

.list-group-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

table td,
th {
    font-size: 12px;
    padding: 10px;
}

table tr {
    border-bottom: 1px solid #E2E2E2;
}

.sidebar-card {
    border: 1px solid #E2E2E2;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.sidebar-menu {
    border-radius: 8px !important;
    overflow: hidden;
}

.sidebar-menu .list-group-item {
    padding: 12px 16px;
    border-color: #F5F5F5;
    transition: all 0.2s ease;
}

.sidebar-menu .list-group-item:hover {
    background-color: #F9F9F9;
}

.menu-link {
    color: #333;
    font-weight: 500;
    text-decoration: none;
    flex-grow: 1;
}

.menu-icon {
    color: #888;
    font-size: 0.9rem;
    transition: transform 0.2s ease;
}

.list-group-item:hover .menu-icon {
    color: #EF4137;
    transform: translateX(2px);
}

.sidebar-menu .list-group-item.active {
    background-color: #EF4137;
    border-color: #EF4137;
}

.sidebar-menu .list-group-item.active .menu-link,
.sidebar-menu .list-group-item.active .menu-icon {
    color: white;
}

.contact-card {
    border: 1px solid #E2E2E2;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-header {
    background-color: #F8F9FA;
    border-bottom: 1px solid #E2E2E2;
}

.contact-icon {
    font-size: 22px;
    color: #EF4137;
}

.contact-table {
    width: 100%;
    border-collapse: collapse;
}

.contact-table tr:not(:last-child) {
    border-bottom: 1px solid #F0F0F0;
}

.contact-table td {
    padding: 12px 16px;
    vertical-align: middle;
}

.label-cell {
    width: 40%;
    color: #666;
    font-weight: 500;
}

.value-cell {
    font-weight: 600;
    color: #333;
}

@media (max-width: 768px) {
    .contact-table td {
        padding: 10px 12px;
    }

    .label-cell {
        width: 45%;
    }

    .contact-card {
        margin-bottom: 1.5rem;
        margin-top: 1.5rem;
    }

    .order_history {
        margin-top: 10px;
    }
}

.order-history-card {
    border: 1px solid #e2e2e2;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.order-history-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}

.card-subtitle {
    font-size: 12px;
    margin-left: auto;
    margin-right: auto;
}

.order-history-btn {
    background-color: #EF4137;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    font-size: 12px;
}

.order-history-btn:hover {
    background-color: #d6372e;
    transform: translateY(-1px);
}

.order-history-btn:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .order-history-card {
        padding: 1.75rem;
    }

    .card-title {
        font-size: 1.3rem;
    }

    .card-subtitle {
        max-width: 100%;
    }
}

.disclaimer-container {
    margin: 0 auto;
    padding-top: 1rem;
}

.disclaimer-text {
    font-size: 0.75rem;
    line-height: 1.5;
    color: #000000;
    margin-bottom: 0.5rem;
}

.privacy-link {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    color: #EF4137;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.privacy-link:hover {
    color: #d6372e;
    text-decoration: underline;
}
</style>
