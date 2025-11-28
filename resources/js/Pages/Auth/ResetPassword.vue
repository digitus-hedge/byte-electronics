<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="forgot-password-container">
            <div class="illustration-container">
                <img src="/assets/images/Forgot_Password_Page copy-01 1.png"
                     alt="Forgot Password Illustration"
                     class="illustration-image" />
            </div>

            <div class="form-container">
                <div class="card">
                    <form @submit.prevent="submit" class="password-form">
                        <div class="form-header">
                            <h1 class="form-title">Reset Password</h1>
                            <p class="form-subtitle">
                                Enter your new password below to reset your account.
                            </p>
                        </div>

                        <div class="form-group">
                            <InputLabel for="email" value="Email" class="input-label" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-input"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <InputError class="error-message" :message="form.errors.email" />
                        </div>

                        <div class="form-group">
                            <InputLabel for="password" value="New Password" class="input-label" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="form-input"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="error-message" :message="form.errors.password" />
                        </div>

                        <div class="form-group">
                            <InputLabel for="password_confirmation" value="Confirm Password" class="input-label" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="form-input"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="error-message" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="button-container">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="submit-button"
                            >
                                Reset Password
                            </PrimaryButton>
                        </div>

                        <div class="back-to-login">
                            Remember your password?
                            <a :href="route('login')" class="login-link">Sign in here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
.forgot-password-container {
    display: flex;
    min-height: 80vh;
    background-color: #f8fafc;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.illustration-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: #f1f5f9;
}

.illustration-image {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
}

.form-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.card {
    width: 100%;
    max-width: 450px;
    background: white;
    border-radius: 12px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.form-header {
    margin-bottom: 2rem;
    text-align: center;
}

.form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 0.9375rem;
    line-height: 1.5;
}

.form-group {
    margin-bottom: 1.5rem;
}

.input-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #475569;
    font-weight: 500;
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9375rem;
    transition: all 0.2s;
    background-color: #f8fafc;
}

.form-input:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background-color: white;
}

.error-message {
    margin-top: 0.5rem;
    color: #dc2626;
    font-size: 0.8125rem;
}

.button-container {
    margin-top: 2rem;
}

.submit-button {
    width: 100%;
    padding: 0.875rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    background-color: #dc2626;
    color: white;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.submit-button:hover {
    background: linear-gradient(to right, #EF4137, #892929);
}

.submit-button:disabled {
    opacity: 0.25;
    cursor: not-allowed;
}

.back-to-login {
    margin-top: 1.5rem;
    text-align: center;
    color: #64748b;
    font-size: 0.875rem;
}

.login-link {
    color: #6366f1;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
}

.login-link:hover {
    color: #4f46e5;
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .forgot-password-container {
        flex-direction: column;
    }

    .illustration-container {
        padding: 1rem;
        max-height: 200px;
    }

    .illustration-image {
        max-height: 150px;
    }

    .form-container {
        padding: 1.5rem;
    }

    .card {
        padding: 1.5rem;
        box-shadow: none;
    }
}
</style>
