<style scoped>
h4 {
    font-size: 1.3rem !important;
}

p,
label,
li {
    font-size: 0.9rem !important;
}

.loginBtn {
    background-image: linear-gradient(to right, #892520, #EF4137);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    text-transform: uppercase;
    transition: background 0.3s ease;
    width: auto;
    padding: 0.75rem 1.5rem;
    font-weight: 400;
}

#remember {
    height: 1.2rem;
    width: 1.5rem;
}

.loginBtn:hover {
    background-image: linear-gradient(to right, #EF4137, #892520);
    cursor: pointer;
}

.card {
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

form {
    width: 100%;
}

.m-3 {
    margin-bottom: 1.5rem;
}

small {
    font-size: 0.875rem;
}

a {
    text-decoration: none;
    color: #0682a3;
}

a:hover {
    text-decoration: underline;
}

ul {
    list-style-type: disc;
    padding-left: 20px;
}

.byte-feature li {
    margin-bottom: 1rem;
    margin-top: 1rem;
}

@media (max-width: 767px) {
    h4 {
        font-size: 1.1rem !important;
    }

    p,
    label,
    li {
        font-size: 0.85rem !important;
    }

    .loginBtn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .byte-feature li {
        margin-bottom: 0.75rem;
        margin-top: 0.75rem;
    }

    .m-3 {
        margin-bottom: 1rem;
    }
}
</style>
<template>
    <AppLayout>
        <BreadCrums />
        <div class="container d-flex flex-column justify-content-center align-items-center mt-4 mb-4">
            <div class="row w-80 justify-content-center">
                <!-- LOGIN Section (Left) -->
                <div class="col-12 col-md-6 mb-4">
                    <h4 class="mb-3 fw-bold text-center text-md-start">LOGIN</h4>
                    <div class="card p-4 shadow-sm">
                        <p class="fw-bold">Returning Byte-Electronics Account holders, please Log In</p>
                        <p class="m-3"> <span style="color:red;">*</span> Indicates a required field.</p>
                        <div v-if="status" class="alert alert-success">
                            {{ status }}
                        </div>
                        <form @submit.prevent="submit">
                            <div class="m-3">
                                <label for="username" class="form-label">email <span style="color:red;">*</span></label>
                                <!-- <input type="text" class="form-control" id="username" required> -->
                                <TextInput id="email" v-model="form.email" type="email" class="form-control" required
                                    autofocus autocomplete="username" />
                                <InputError v-if="errors.email" class="text-danger small mt-1"
                                    :message="errors.email" />
                            </div>
                            <div class="m-3 position-relative">
                                <label for="password" class="form-label">Password <span
                                        style="color:red;">*</span></label>
                                <!-- <input type="password" class="form-control" id="password" required> -->
                                <!-- <TextInput id="password" v-model="form.password" type="password" class="form-control"
                                    required autocomplete="current-password" /> -->
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control pe-5"
                                        required
                                        autocomplete="current-password"
                                    />
                                    <!-- Eye Icon Button -->
                                    <span
                                    class="position-absolute  end-0 translate-middle-y me-3"
                                    @click="togglePassword"
                                    style="cursor: pointer;top: 50px;" >
                                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </span>
                                    
                                <InputError v-if="errors.password" class="text-danger small mt-1"
                                    :message="errors.password" />
                            </div>
                            <div class="mb-3 form-check d-flex align-items-center gap-2" style="padding-left:1rem;">
                                <!-- <input type="checkbox" class="" id="remember" style="font-size:20px;"> -->
                                <Checkbox v-model:checked="form.remember" name="remember" class=""
                                    style="font-size:20px;" />
                                <label class="form-check-label" for="remember">Remember my email on this
                                    computer</label>
                            </div>
                            <div class="d-flex flex-column align-items-start">
                                <!-- <button type="submit" class="btn loginBtn w-auto py-2">Login</button> -->
                                <PrimaryButton class="btn loginBtn w-auto py-2"
                                    :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                                    Log in
                                </PrimaryButton>
                                <!-- <a href="#" class="small mt-2 text-center" style="color:#111;">Request your <span style="color:red;">Username</span> or <span style="color:red;">Password</span></a> -->
                                <Link v-if="canResetPassword" :href="route('password.request')"
                                    class="small mt-2 text-center" style="color:#111;">
                                Request your
                                <span style="color:red;">email</span> or <span style="color:red;">Password</span>
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Create Account Section (Right) -->
                <div class="col-12 col-md-6">
                    <h4 class="mb-3 fw-bold text-center text-md-start">Create My Byte Account</h4>
                    <p class="mt-3">The information you provide will be recalled the next time you log in, making it
                        quicker to complete orders.</p>
                    <div class="m-3">
                        <p class="fw-bold">Byte Account Features</p>
                        <ul class="byte-feature">
                            <!-- <li>Build & manage multiple projects</li> -->
                            <li>Use part numbers to order</li>
                            <li>Manage Delivery Addresses</li>
                            <!-- <li>Import a bill of materials</li> -->
                        </ul>
                        <Link href="/register" class="loginBtn py-2 w-auto d-inline-block text-white text-decoration-none"
                            role="button">
                            Create My Byte Account
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


<script setup>


import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { reactive, ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const errors = ref({});
const validate = () => {
    errors.value = {};

    let isValid = true;

    if (!form.email) {
        errors.value.email = 'Email is required';
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.value.email = 'Invalid email';
        isValid = false;
    }

    if (!form.password) {
        errors.value.password = 'Password is required';
        isValid = false;
    } else if (form.password.length < 8) {
        errors.value.password = 'Password must be at least 8 characters long';
        isValid = false;
    }

    return isValid;
};
const submit = () => {
    if (!validate()) return;
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: (errorResponse) => {
            if (errorResponse.email || errorResponse.password) {
                errors.value.email = 'The email or password is incorrect.';
                errors.value.password = '';
            }
        }
    });
};
const showPassword = ref(false)
const togglePassword = () => {
    showPassword.value = !showPassword.value
}

</script>
