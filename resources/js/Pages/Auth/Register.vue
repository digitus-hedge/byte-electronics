<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { computed, ref, watch } from 'vue';
import Swal from 'sweetalert2';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const errors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: '',
});

const isNameFocused = ref(false);
const isEmailFocused = ref(false);
const isPasswordFocused = ref(false);
const isPasswordConfirmationFocused = ref(false);
const isTermsFocused = ref(false);

const isNameValid = computed(() => form.name.trim().length >= 3);
const isEmailValid = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email));
const isPasswordValid = computed(() => form.password.length >= 8);
const isPasswordMatch = computed(() => form.password === form.password_confirmation);
const isTermsChecked = computed(() => form.terms);

const validateForm = () => {
    errors.value.name = isNameValid.value ? '' : 'Name must be at least 3 characters.';
    errors.value.email = isEmailValid.value ? '' : 'Invalid email address.';
    errors.value.password = isPasswordValid.value ? '' : 'Password must be at least 8 characters.';
    errors.value.password_confirmation = isPasswordMatch.value ? '' : 'Passwords do not match.';
    errors.value.terms = isTermsChecked.value ? '' : 'You must agree to the terms.';
    return Object.values(errors.value).every(error => error === '');
};

watch([() => form.name, () => form.email, () => form.password, () => form.password_confirmation, () => form.terms], () => {
    validateForm();
});

const submit = () => {
    if (!validateForm()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fix the form errors.',
            confirmButtonColor: '#EF4137',
        });
        isNameFocused.value = true;
        isEmailFocused.value = true;
        isPasswordFocused.value = true;
        isPasswordConfirmationFocused.value = true;
        isTermsFocused.value = true;
        return;
    }

    form.post(route('register'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: 'Please check your email to verify your account.',
                confirmButtonColor: '#EF4137',
                timer: 2000,
            });
            form.reset('password', 'password_confirmation');
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: Object.values(errors).join('\n') || 'An error occurred.',
                confirmButtonColor: '#EF4137',
            });
        },
    });
};
</script>

<template>
    <AppLayout>

        <Head title="Register" />
        <BreadCrums />
        <div class="container d-flex flex-column justify-content-center align-items-center mt-4 mb-4">
            <div class="row w-80 justify-content-center">
                <div class="col-12 col-md-6 mb-4">
                    <h4 class="mb-3 fw-bold text-center text-md-start">REGISTER</h4>
                    <div class="card p-4 shadow-sm">
                        <p class="fw-bold">To create a Byte account, please fill in this form.</p>
                        <p class="m-2"><span class="text-danger">*</span>All fields are mandatory.</p>
                        <AuthenticationCard>
                            <form @submit.prevent="submit">
                                <div class="m-3">
                                    <label for="firstName" class="form-label">Name <span
                                            class="text-danger">*</span></label>
                                    <TextInput id="name" v-model="form.name" type="text" class="form-control" required
                                        autofocus autocomplete="name" aria-label="First Name"
                                        @focus="isNameFocused = true" />
                                    <InputError class="input-error mt-2"
                                        :message="isNameFocused ? errors.name || form.errors.name : ''" />
                                </div>

                                <div class="m-3">
                                    <label for="email" class="form-label">Email Address <span
                                            class="text-danger">*</span></label>
                                    <TextInput id="email" v-model="form.email" type="email" class="form-control"
                                        required autocomplete="username" @focus="isEmailFocused = true" />
                                    <InputError class="input-error mt-2"
                                        :message="isEmailFocused ? errors.email || form.errors.email : ''" />
                                </div>

                                <div class="m-3">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <TextInput id="password" v-model="form.password" type="password"
                                        class="form-control" required autocomplete="new-password"
                                        @focus="isPasswordFocused = true" />
                                    <InputError class="input-error mt-2"
                                        :message="isPasswordFocused ? errors.password || form.errors.password : ''" />
                                </div>

                                <div class="m-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <TextInput id="password_confirmation" v-model="form.password_confirmation"
                                        type="password" class="form-control" required autocomplete="new-password"
                                        @focus="isPasswordConfirmationFocused = true" />
                                    <InputError class="input-error mt-2"
                                        :message="isPasswordConfirmationFocused ? errors.password_confirmation || form.errors.password_confirmation : ''" />
                                </div>

                                <div class="mt-4">
                                    <div class="form-check mb-3" style="margin-left:2rem;">
                                        <Checkbox id="terms" v-model:checked="form.terms" class="form-check-input"
                                            name="terms" required @focus="isTermsFocused = true" />
                                        <label class="form-check-label" for="terms" style="font-size:15px;">
                                            Agree To Byte's <span class="text-danger">
                                                <Link href="/terms-conditions" class="text-danger text-decoration-none">
                                                Terms and Conditions</Link>
                                            </span> And <span class="text-danger">
                                                <Link href="/privacy-policy" class="text-danger text-decoration-none">
                                                Privacy Policy.</Link>
                                            </span>
                                        </label>
                                        <small class="text-muted fst-italic m-0 d-block mt-2"
                                            style="color:#111 !important;">
                                            If you would like to find out more about how byte handles your personal
                                            data, please refer to our privacy center for more information.
                                        </small>
                                    </div>
                                    <InputError class="input-error mt-2"
                                        :message="isTermsFocused ? errors.terms || form.errors.terms : ''" />
                                </div>

                                <div class="d-flex items-center justify-between" style="align-items: center;gap: 1rem;">
                                    <Link :href="route('login')"
                                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Already registered?
                                    </Link>
                                    <PrimaryButton class="loginBtn py-2 px-4 ml-4" style="font-size: 14px;"
                                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        CREATE ACCOUNT
                                    </PrimaryButton>
                                </div>
                            </form>
                        </AuthenticationCard>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <h4 class="mb-3 fw-bold text-center text-md-start">Create My Byte Account</h4>
                    <p>The information that you provide will be recalled the next time you log in to
                        byte-electronics.com, making
                        it quicker for you to complete orders.</p>
                    <div class="m-3">
                        <p class="fw-bold">Byte Account Features</p>
                        <ul class="byte-feature">
                            <!-- <li>Create and save multiple projects using Project Manager</li> -->
                            <!-- <li>Access to our Bill of Materials (BOM) tool</li> -->
                            <li>Use part numbers to order</li>
                            <li>Easy access to shipment tracking and past order information</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
h4 {
    font-size: 1.5rem;
}

p,
label,
li {
    font-size: 14px;
}

small {
    font-size: 12px;
    margin-left: 2rem;
}

.loginBtn {
    background-image: linear-gradient(to right, #892520, #EF4137);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    text-transform: uppercase;
    transition: background 0.3s ease;
    width: auto;
    padding: 0.75rem 1.5rem;
}

.loginBtn:hover {
    background-image: linear-gradient(to right, #EF4137, #892520);
    cursor: pointer;
}

.card {
    border-radius: 10px;
    padding: 20px;
}

.m-3 {
    margin-bottom: 1rem;
}

ul {
    padding-left: 20px;
}

.byte-feature li {
    margin: 0.5rem 0;
}

.form-check-label {
    font-size: 0.9rem;
}

.container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.row {
    width: 100%;
    max-width: 1200px;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.col-12.col-md-6 {
    flex: 1;
    max-width: 45%;
}

@media (max-width: 767px) {
    h4 {
        font-size: 1.2rem;
    }

    .loginBtn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    .col-12.col-md-6 {
        max-width: 100%;
    }

    .row {
        flex-direction: column;
    }

    .col-md-6 {
        max-width: 100%;
    }
}

input:focus,
textarea:focus,
select:focus {
    box-shadow: none !important;
    outline: none !important;
}

.input-error {
    color: red;
    font-size: 14px;
}
</style>
