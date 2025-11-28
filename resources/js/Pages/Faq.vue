<template>
    <AppLayout>
        <BreadCrums/>

        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <div class="w-100">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="font-size: 22px;">FAQ</h3>
                    <p class="text-muted" style="font-size: 18px;">
                        Welcome to our Frequently Asked Questions (FAQs) page.
                    </p>
                </div>

                <div class="mx-auto" style="max-width: 800px;">
                    <div v-for="(faq, index) in faqs" :key="index" class="mb-3">
                        <h4 class="fw-bold" style="font-size: 16px;">{{ faq.question }}</h4>
                        <p class="text-muted" style="font-size: 14px;">{{ faq.answer }}</p>
                    </div>
                    <div>
                        <Link href="/" class=" text-danger text-decoration-none" style="font-size: 15px;">
                        <i class="bi bi-chevron-left mt-1 mb-1 fs-6" style="font-weight: 900; color:#111;"></i>

                        Back to Home
                        </Link>
                    </div>
                    <div class="pt-4">
                        <h3 style="font-size: 22px;" class="pb-4 fw-bold">Leave Feedback</h3>

                        <p class="text-muted" style="font-size: 14px;">Can't find what you're looking for? Contact us by phone, email, chat, or visit the Contact
                            Centre for additional information.</p>
                    </div>
                    <div>
                        <Link href="/contact-us" class=" text-danger text-decoration-none" style="font-size: 15px;">
                            <i class="bi bi-telephone mt-1 mb-1 fs-6" style="font-weight: 900; color:#111;"></i>
                            CONTACT US
                        </Link>
                        <!-- <Link href="/" class="text-danger text">
                        Go Back</Link> -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import { Link } from '@inertiajs/vue3';

const faqs = [
    { question: "Does Byte ship to an APO or FPO address?", answer: "Yes." },
    { question: "Does Byte accept personal cheques?", answer: "Yes. Cheques exceeding a certain dollar value may require bank verification prior to shipment of an order. Dishonoured cheques will be charged a $25.00 (USD) handling fee." },
    { question: "What is Byte's corporate address?", answer: "Yes. Cheques exceeding a certain dollar value may require bank verification prior to shipment of an order. Dishonoured cheques will be charged a $25.00 (USD) handling fee." },
    { question: "What is Byte's 'remit-to' address?", answer: "Remit-to addresses vary based on your currency, payment method, and bill-to country. Please refer to your invoice for the appropriate address." },
    { question: "Do you have a catalogue?", answer: "No, we no longer produce a catalogue in print or online." },
    { question: "Can I enter a scheduled order online?", answer: "Yes, scheduled orders can be placed and edited online." },
];
</script>
