<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Verification Link Sent',
                text: 'A new verification link has been sent to your email.',
                confirmButtonColor: '#EF4137',
                timer: 2000,
            });
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to send verification link. Please try again.',
                confirmButtonColor: '#EF4137',
            });
        },
    });
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AppLayout>
        <Head title="Email Verification" />
        <BreadCrums />
        <div class="container d-flex flex-column justify-content-center align-items-center mt-4 mb-4">
            <div class="row w-80 justify-content-center">
                <div class="col-12 col-md-6">
                    <h4 class="mb-3 fw-bold text-center text-md-start">Email Verification</h4>
                    <div class="card p-4 shadow-sm">
                        <AuthenticationCard>
                            <div class="mb-4 text-sm text-gray-600">
                                Before continuing, please verify your email address by clicking on the link we just emailed to you. If you didn't receive the email, we will gladly send you another.
                            </div>

                            <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
                                A new verification link has been sent to the email address you provided in your profile settings.
                            </div>

                            <form @submit.prevent="submit">
                                <div class="mt-4 flex items-center justify-between">
                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Resend Verification Email
                                    </PrimaryButton>

                                    <div>
                                        <Link
                                            :href="route('profile.show')"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Edit Profile
                                        </Link>

                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"
                                        >
                                            Log Out
                                        </Link>
                                    </div>
                                </div>
                            </form>
                        </AuthenticationCard>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
h4 { font-size: 1.5rem; }
.card { border-radius: 10px; padding: 20px; }
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
.text-sm { font-size: 0.875rem; }
@media (max-width: 767px) {
    h4 { font-size: 1.2rem; }
    .col-12.col-md-6 { max-width: 100%; }
    .row { flex-direction: column; }
}
</style>
