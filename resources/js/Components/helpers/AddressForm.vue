<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    address: {
        type: Object,
        default: () => ({})
    },
    isEditing: {
        type: Boolean,
        default: false
    },
    countries: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['cancel', 'submit']);

const formData = ref({
    addressName: '',
    userName: '',
    email: '',
    company_name: '',
    address_line_1: '',
    address_line_2: '',
    country_id: '',
    postal_code: '',
    attention: '',
    countryCode: '',
    phoneNumber: '',
    ...props.address
});

// Pre-split phone if editing an existing address
if (props.isEditing && props.address.phone) {
    const phoneParts = props.address.phone.match(/^(\+\d{1,3})\s?(\d+)$/);
    if (phoneParts) {
        formData.value.countryCode = phoneParts[1]; // e.g., +91
        formData.value.phoneNumber = phoneParts[2]; // e.g., 9876543210
    } else {
        formData.value.phoneNumber = props.address.phone; // Fallback if no country code
    }
}

// Map countries for the select options
const countryOptions = props.countries.map(country => ({
    value: country.id || country.country_code,
    label: `+${country.country_code} - ${country.name}`
}));

// Function to get country details
const getCountryDetails = (countryId) => {
    const country = props.countries.find(c => c.id === countryId);
    return country ? { name: country.name, code: `+${country.country_code}` } : { name: '', code: '' };
};

// Set initial country and countryCode if editing
onMounted(() => {
    if (props.isEditing && props.address.country_id) { // Changed from 'country' to 'country_id'
        formData.value.country_id = props.address.country_id;
        const { code } = getCountryDetails(props.address.country_id);
        if (code) {
            formData.value.countryCode = code;
        }
    }
});

// Watch country_id changes to update countryCode
watch(() => formData.value.country_id, (newCountryId) => {
    // console.log('Country changed:', newCountryId);
    const { code } = getCountryDetails(newCountryId);
    formData.value.countryCode = code || '';
});

// Handle form submission
const handleSubmit = () => {
    const fullPhone = `${formData.value.countryCode} ${formData.value.phoneNumber}`.trim();
    const submitData = {
        ...formData.value,
        phone: fullPhone
    };
    // console.log('Submitting data:', submitData);
    emit('submit', submitData);
};
</script>

<template>
    <!-- <pre>{{ address }}</pre> -->
    <div class="card address-card">
        <div class="card-header">
            <h6>{{ isEditing ? 'Edit Address' : 'Add Address' }}</h6>
        </div>
        <div class="card-body">
            <form @submit.prevent="handleSubmit">
                <div class="form-row">
                    <label for="addressType">Address Type</label>
                    <input type="text" id="addressType" v-model="formData.addressName" class="form-control">
                </div>

                <div class="form-row">
                    <label for="userName"> Name</label>
                    <input type="text" id="userName" v-model="formData.userName" class="form-control">
                </div>

                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="formData.email" class="form-control">
                </div>

                <div class="form-row">
                    <label for="company_name">Company </label>
                    <input type="text" id="company_name" v-model="formData.company_name" class="form-control">
                </div>

                <div class="form-row">
                    <label for="address_line_1">Address Line 1</label>
                    <input type="text" id="address_line_1" v-model="formData.address_line_1" class="form-control">
                </div>

                <div class="form-row">
                    <label for="address_line_2">Address Line 2</label>
                    <input type="text" id="address_line_2" v-model="formData.address_line_2" class="form-control">
                </div>

                <div class="form-row">
                    <label for="City">City</label>
                    <input type="text" id="City" v-model="formData.city" class="form-control">
                </div>

                <div class="form-row">
                    <label for="State">State</label>
                    <input type="text" id="State" v-model="formData.state" class="form-control">
                </div>

                <!-- Native select with dynamic options -->
                <div class="form-row">
                    <label for="countrySelect">Country</label>
                    <select id="countrySelect" v-model="formData.country_id" class="form-control">
                        <option value="">Select a country</option>
                        <option v-for="option in countryOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" v-model="formData.postal_code" class="form-control">
                </div>

                <div class="form-row">
                    <label for="attention">Attention</label>
                    <input type="text" id="attention" v-model="formData.attention" class="form-control">
                </div>

                <div class="form-row">
                    <label for="phone">Phone</label>
                    <div class="phone-inputs d-flex">
                        <input type="text" id="countryCode" v-model="formData.countryCode" class="form-control me-2" style="width: 80px;" placeholder="+91" readonly>
                        <input type="tel" id="phoneNumber" v-model="formData.phoneNumber" class="form-control" placeholder="Phone Number">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" @click="$emit('cancel')">Cancel</button>
                    <button type="submit" class="btn btn-submit">
                        {{ isEditing ? 'Update Address' : 'Add Address' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.address-card {
    max-width: 800px;
    margin: 0 auto;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: #EF4137;
    color: white;
    padding: 0.5rem 1rem;
}

.card-header h6 {
    margin: 0;
    font-weight: 600;
}

.card-body {
    padding: 1.5rem;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 1rem;
}

.form-row label {
    flex: 0 0 150px;
    margin-right: 1rem;
    white-space: nowrap;
    font-size: 14px;
    color: #000000;
}

.form-row .form-control {
    flex: 1;
    min-width: 200px;
    height: 36px;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.phone-inputs {
    flex: 1;
    min-width: 200px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.btn {
    padding: 0.375rem 1rem;
    font-size: 0.875rem;
    min-width: 100px;
    border-radius: 0.25rem;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
    border: none;
}

.btn-submit {
    background-color: #EF4137;
    color: white;
    border: none;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-row label {
        flex: 0 0 auto;
        margin-bottom: 0.25rem;
        margin-right: 0;
    }

    .form-row .form-control,
    .phone-inputs {
        flex: 0 0 100%;
        width: 100%;
    }

    .phone-inputs {
        flex-direction: column;
        gap: 0.5rem;
    }

    .phone-inputs .form-control {
        width: 100%;
    }

    .form-actions {
        justify-content: center;
    }

    .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
    }

    .form-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
}

.form-control {
    box-shadow: none !important;
}
</style>
