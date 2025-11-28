<script setup>
import Banner from "@/Components/helpers/Banner.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';

defineProps({
    privacyPolicyBanner: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <Banner :image="privacyPolicyBanner" :search="false" text="Privacy Policy" />
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <h4 class="section-title">Introduction</h4>
            <p class="section-text">
                Welcome to BYTE Electronics. Your privacy is important to us, and we are committed to protecting the
                personal information you share with us. This Privacy Policy explains how we collect, use, disclose, and
                safeguard your information when you visit our website, use our services, or interact with us.</p>
            <p><b>1. Information We Collect</b></p>
            <p class="section-text">We collect various types of information, including but not limited to:</p>
            <p class="section-text"><b>Personal Information:</b> Name, email address, phone number, billing and shipping
                address, payment information.</p>
            <p class="section-text"><b>Technical Information:</b> IP address, browser type, operating system, device
                information.</p>
            <p class="section-text"><b>Usage Data:</b>Pages visited, time spent on our website, referral sources,
                interactions with our content.</p>
            <p class="section-text"><b>Cookies and Tracking Technologies:</b> To enhance user experience and improve our
                services.</p>
            <p><b>2. How We Use Your Information</b></p>
            <p class="section-text">We use your information for the following purposes:</p>
            <ul>
                <li>To provide and improve our products and services.</li>
                <li>To process transactions and manage customer support.</li>
                <li>To send promotional materials, newsletters, and special offers (with consent).</li>
                <li>To analyze website performance and enhance user experience.</li>
                <li>To comply with legal obligations and enforce our policies.</li>
            </ul>
            <p><b>3. Sharing of Information</b></p>
            <p class="section-text">We do not sell or rent your personal information. However, we may share your data
                with:</p>
            <p class="section-text"><b>Service Providers:</b> Third-party vendors assisting in payment processing, order
                fulfillment, and marketing.</p>
            <p class="section-text"><b>Legal Authorities:</b> When required to comply with applicable laws, regulations,
                or legal processes.</p>
            <p class="section-text"><b>Business Transfers:</b> In case of a merger, sale, or transfer of assets.</p>
            <p><b>4. Data Security</b></p>
            <p class="section-text">We implement industry-standard security measures to protect your personal
                information. However, no method of transmission over the internet is 100% secure, and we cannot
                guarantee absolute security.</p>
            <p><b>5. Your Rights and Choices</b></p>
            <p class="section-text">Depending on your location, you may have the right to:</p>
            <ul>
                <li>Access, update, or delete your personal information.</li>
                <li>Opt out of marketing communications.</li>
                <li>Restrict or object to certain data processing activities.</li>
                <li>Withdraw consent where applicable</li>
            </ul>
            <p class="section-text">To exercise these rights, please contact us at [Insert Contact Information].</p>
            <p><b>6. Cookies and Tracking Technologies</b></p>
            <p class="section-text">We use cookies to enhance user experience. You can manage cookie preferences through
                your browser settings. Disabling cookies may impact website functionality.</p>
            <p><b>7. Third-Party Links</b></p>
            <p class="section-text">Our website may contain links to third-party websites. We are not responsible for
                their privacy practices and encourage you to review their policies.</p>
            <p><b>8. Changes to This Privacy Policy</b></p>
            <p class="section-text">We may update this Privacy Policy periodically. Any changes will be posted on this
                page with an updated effective date.</p>
            <p><b>9. Contact Us</b></p>
            <p class="section-text">If you have any questions or concerns about this Privacy Policy, please contact us
                at:
                BYTE Electronics</p>

        </div>
    </AppLayout>
</template>

<style scoped>
.section-title {
    font-size: 2rem;
    font-weight: 800;
    color: #000000;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
}

.section-text,
li {
    font-size: 14px;
    line-height: 1.6;
    color: #000000;
    margin-bottom: 1rem;
}
p{
    color: #000000;
}
</style>
