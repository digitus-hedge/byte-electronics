<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    isBilling: {
        type: Boolean,
        default: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    copiedFromBilling: {
        type: Boolean,
        default: false,
    },
    shouldValidate: {
        type: Boolean,
        default: false,
    },
    countries: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

const address = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const errors = ref({
    firstName: '',
    lastName: '',
    email: '',
    confirmEmail: '',
    phone: '',
    country_id: '',
    address1: '',
    city: '',
    state: '',
    postalCode: '',
});

// Update country code when country_id changes
watch(() => address.value.country_id, (newCountryId) => {
    const country = props.countries.find((c) => c.id === newCountryId);
    address.value.country_code = country ? country.country_code : '';
    validateField('country_id', newCountryId);
});

const validateField = (field, value) => {

  const validationRules = {
    firstName: (val) => (!val ? 'First name is required' : ''),
    email: (val) => {
      if (!props.isBilling) return ''; // Skip email validation for shipping
      console.log('Validating email:', val);
      if (!val) return 'Email is required';
      if (!/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val)) {
        return 'Invalid email format';
      }
      return '';
    },
    confirmEmail: (val) => {
      if (!props.isBilling) return ''; // Skip confirmEmail validation for shipping
      console.log('Validating confirmEmail:', val);
      if (!val) return 'Confirm email is required';
      if (val !== address.value.email) {
        return 'Emails must match';
      }
      return '';
    },
    phone: (val) => {
      const digitsOnly = val?.replace(/\s+/g, '');
      if (!digitsOnly) return 'Phone is required';
      if (!/^[0-9]{8,15}$/.test(digitsOnly)) return 'Invalid phone number (8–15 digits)';
      return '';
    },
    country_id: (val) => (!val ? 'Country is required' : ''),
    address1: (val) => (!val ? 'Address is required' : ''),
    city: (val) => (!val ? 'City is required' : ''),
    state: (val) => (!val ? 'State is required' : ''),
    postalCode: (val) => {
      if (!val) return 'Postal code is required';
      if (!/^[0-9]{6}$/.test(val)) return 'Invalid postal code (6 digits)';
      return '';
    },
  };



    errors.value[field] = validationRules[field] ? validationRules[field](value) : '';
};

const validate = () => {

  let isValid = true;
  const fieldsToValidate = props.isBilling
    ? Object.keys(errors.value)
    : Object.keys(errors.value).filter((field) => field !== 'email' && field !== 'confirmEmail');

  fieldsToValidate.forEach((field) => {
    validateField(field, address.value[field]);
    if (errors.value[field]) {
      isValid = false;
    }
  });
  console.log('Validation errors:', errors.value);
  return isValid;

};

defineExpose({ validate });
</script>

<template>
    <div class="card mt-3 w-100 p-3 billing-card" :class="{ 'disabled-address': disabled }">
        <div class="required-field"><span class="red-text">*</span> Required Field</div>


    <div class="row mt-3">
      <div class="col-md-6">
        <label>First Name <span class="red-text">*</span></label>
        <input
          v-model="address.firstName"
          :disabled="disabled"
          type="text"
          class="form-control no-focus"
          :class="{
            'copied-field': copiedFromBilling && !isBilling,
            'is-invalid': shouldValidate && errors.firstName,
          }"
          @blur="validateField('firstName', address.firstName)"
        />
        <div v-if="shouldValidate && errors.firstName" class="invalid-feedback">{{ errors.firstName }}</div>
      </div>
      <div class="col-md-6">
        <label>Last Name</label>
        <input
          v-model="address.lastName"
          :disabled="disabled"
          type="text"
          class="form-control no-focus"
          :class="{
            'copied-field': copiedFromBilling && !isBilling,
            'is-invalid': shouldValidate && errors.lastName,
          }"
          @blur="validateField('lastName', address.lastName)"
        />
        <div v-if="shouldValidate && errors.lastName" class="invalid-feedback">{{ errors.lastName }}</div>
      </div>
    </div>


        <div v-if="isBilling" class="row mt-3">
            <div class="col-md-6">
                <label>Email Address <span class="red-text">*</span></label>
                <input v-model="address.email" :disabled="disabled" type="email" class="form-control no-focus"
                    :class="{ 'is-invalid': shouldValidate && errors.email }"
                    @blur="validateField('email', address.email)" />
                <div v-if="shouldValidate && errors.email" class="invalid-feedback">{{ errors.email }}</div>
            </div>
            <div class="col-md-6">
                <label>Confirm Email Address <span class="red-text">*</span></label>
                <input v-model="address.confirmEmail" :disabled="disabled" type="email" class="form-control no-focus"
                    :class="{
                        'is-invalid': shouldValidate && (errors.confirmEmail || (address.email && address.email !== address.confirmEmail)),
                    }" @blur="validateField('confirmEmail', address.confirmEmail)" />
                <div v-if="shouldValidate && (errors.confirmEmail || (address.email && address.email !== address.confirmEmail))"
                    class="invalid-feedback">
                    {{ errors.confirmEmail || 'Emails must match' }}
                </div>
            </div>
        </div>


    <div class="row mt-3">
      <div class="col-md-6">
        <label>Country <span class="red-text">*</span></label>
        <select
          v-model="address.country_id"
          :disabled="disabled"
          class="form-control no-focus"
          :class="{ 'is-invalid': shouldValidate && errors.country_id }"
          @change="validateField('country_id', address.country_id)"
        >
          <option value="">Select a country</option>
          <option v-for="country in countries" :key="country.id" :value="country.id">
            {{ country.name }}
          </option>
        </select>
        <div v-if="shouldValidate && errors.country_id" class="invalid-feedback">{{ errors.country_id }}</div>
      </div>
      <div class="col-md-6">
        <label>Phone <span class="red-text">*</span></label>
        <div class="input-group">
          <span class="input-group-text" style="background: aliceblue;">{{ address.country_code || '+' }}</span>
          <input
            v-model="address.phone"
            :disabled="disabled"
            type="tel"
            class="form-control no-focus"
            :class="{ 'is-invalid': shouldValidate && errors.phone }"
            placeholder="Enter phone number"
            @blur="validateField('phone', address.phone)"
          />
        </div>
        <div v-if="shouldValidate && errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
      </div>
    </div>


        <div class="row mt-3">
            <div class="col-md-6">
                <label>Company Name</label>
                <input v-model="address.company" :disabled="disabled" type="text" class="form-control no-focus" />
            </div>
            <div class="col-md-6">
                <label>Attention</label>
                <input v-model="address.attention" :disabled="disabled" type="text" class="form-control no-focus" />
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Address Line 1 <span class="red-text">*</span></label>
                <input v-model="address.address1" :disabled="disabled" type="text"
                    class="form-control no-focus expandable-height"
                    :class="{ 'is-invalid': shouldValidate && errors.address1 }"
                    @blur="validateField('address1', address.address1)" />
                <div v-if="shouldValidate && errors.address1" class="invalid-feedback">{{ errors.address1 }}</div>
            </div>
            <div class="col-md-6">
                <label>Address Line 2</label>
                <input v-model="address.address2" :disabled="disabled" type="text"
                    class="form-control no-focus expandable-height" />
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>City <span class="red-text">*</span></label>
                <input v-model="address.city" :disabled="disabled" type="text" class="form-control no-focus"
                    :class="{ 'is-invalid': shouldValidate && errors.city }"
                    @blur="validateField('city', address.city)" />
                <div v-if="shouldValidate && errors.city" class="invalid-feedback">{{ errors.city }}</div>
            </div>
            <div class="col-md-3">
                <label>State <span class="red-text">*</span></label>
                <input v-model="address.state" :disabled="disabled" type="text" class="form-control no-focus"
                    :class="{ 'is-invalid': shouldValidate && errors.state }"
                    @blur="validateField('state', address.state)" />
                <div v-if="shouldValidate && errors.state" class="invalid-feedback">{{ errors.state }}</div>
            </div>
            <div class="col-md-3">
                <label>Postal Code <span class="red-text">*</span></label>
                <input v-model="address.postalCode" :disabled="disabled" type="text" class="form-control no-focus"
                    :class="{ 'is-invalid': shouldValidate && errors.postalCode }"
                    @blur="validateField('postalCode', address.postalCode)" />
                <div v-if="shouldValidate && errors.postalCode" class="invalid-feedback">{{ errors.postalCode }}</div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.required-field {
    color: #6c757d;
    font-size: 0.875rem;
}

.red-text {
    color: #dc3545;
}

.invalid-feedback {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.copied-field {
    background-color: #f8f9fa;
}

.disabled-address {
    opacity: 0.7;
    pointer-events: none;
}

.expandable-height {
    transition: height 0.3s ease;
    height: 38px;
    /* default Bootstrap input height */
}

.expandable-height:focus {
    height: 60px;
    /* expand to taller height on focus */
}

.no-focus:focus {
    box-shadow: none !important;
    outline: none !important;
}

.billing-card {
    background: #E6E7E8;
}

label {
    font-size: 14px;
    color: #000000;
}

.input-group-text {
    background: #E6E7E8;
    color: #000000;
    font-size: 14px;
}
</style>
