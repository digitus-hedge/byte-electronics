<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="footer mt-3">
        <div class="container-fluid container d-flex flex-column align-items-start ">
            <div class="footer_top  mb-4 w-100">
                <div class="row align-items-center">
                    <!-- Logos Section -->
                    <div
                        class="col-lg-4 col-md-12 d-flex align-items-center justify-content-center justify-content-lg-start mb-3 mb-lg-0">
                        <Link href="/" class="me-3">
                            <img src="/assets/images/BYTE-LOGO-01 1.png" alt="byte-logo" class="img-fluid"
                                style="max-height: 80px;">
                        </LInk>
                        <div style="width: 1px; height: 80px; background: #e7e7e7;"></div>
                        <Link href="/" class="ms-3">
                            <img src="/assets/images/powered-by-hedge.png" alt="Powered by Hedge" class="img-fluid"
                                style="max-height: 80px;">
                        </Link>
                    </div>

                    <!-- Quick Links Section -->
                    <div class="col-lg-8 col-md-12 text-center text-lg-start">
                        <h3 class="footer_title mb-3">Quick Links</h3>
                        <ul
                            class="footer_menu d-flex flex-wrap justify-content-center justify-content-lg-start list-unstyled p-0 m-0">
                            <li class="me-4 mb-2"><Link href="/about-us"
                                    class="text-white text-decoration-none">About Us</Link></li>


                            <li class="me-4 mb-2"><Link href="/contact-us"
                                    class="text-white text-decoration-none">Contact Us</Link></li>
                            <li class="me-4 mb-2"><Link href="/blogs"
                                    class="text-white text-decoration-none">Blog</Link></li>
                            <li class="me-4 mb-2"><Link href="/faq"
                                    class="text-white text-decoration-none">Faq</Link></li>
                            <li class="me-4 mb-2"><Link href="/privacy-policy"
                                    class="text-white text-decoration-none">Privacy Policy</Link></li>
                            <li class="mb-2"><Link href="/terms-conditions" class="text-white text-decoration-none">Terms &
                                    Conditions</Link></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100" style="border-bottom: 1px solid #e7e7e7; "></div>
        <div class="container-fluid container d-flex flex-column align-items-start mt-3">

            <div class="row w-100">
                <!-- First Column -->
                <div class="col-md-6 mb-4 mb-lg-0">
                    <p class="mb-4 text-center text-lg-start" style="font-size: 14px;">
                        Byte-electronics.com, a division of Epoch International LLC, stands as a premier source for automation spare parts. Over time, we have established ourselves as a trusted name, renowned for delivering top-notch products and exceptional service. Our vast inventory encompasses a diverse array of spare parts from well-known manufacturers, guaranteeing dependability, compatibility, and optimal performance. Committed to upholding high standards and offering an extensive product range, we proudly remain your reliable ally in the realm of industrial automation.
                    </p>
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                        <h4 class="mb-0 me-3">FOLLOW US</h4>
                        <div class="d-flex align-items-center gap-3">
                            <a href="https://www.linkedin.com/company/byte-electronics/?viewAsMember=true" class="social-icon" target="_blank">
                              <img src="/assets/images/LinkedIn.webp" alt="LinkedIn" class="img-fluid" style="max-height: 38px;">
                            </a>
                            <a href="https://www.instagram.com/byte_components?igsh=anJtaHFheTUxa2Zm" class="social-icon" target="_blank">
                                <img src="/assets/images/Instagram.webp" alt="Instagram" class="img-fluid" style="max-height: 38px;">
                            </a>
                            <a href="#" class="social-icon">
                                <img src="/assets/images/Facebook.webp" alt="Facebook" class="img-fluid" style="max-height: 38px;">
                            </a>
                            <a href="https://wa.me/971555316164" target="_blank" class="social-icon">
                                <img src="/assets/images/whatsapp-icon.webp" alt="WhatsApp" class="img-fluid" style="max-height: 38px;">
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Second Column -->
                <div class="col-md-6">
                    <div class="row">
                        <!-- Shipping -->
                        <div class="col-md-6 text-center mb-3">
                            <img src="/assets/images/export-icons.webp" alt="Shipping Worldwide" class="img-fluid mb-2">
                            <span style="font-weight: 700;">Shipping Worldwide</span>
                        </div>
                        <!-- Payments -->
                        <div class="col-md-6 text-center mb-3">
                            <p style="font-weight: 700; margin-top: 10px !important;">We are Accepting Payments</p>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Payment Logos -->
                        <div class="col-md-6 text-center">
                            <img src="/assets/images/shipping.webp" alt="Shipping Methods" class="img-fluid" style="height: 75px;">
                           
                        </div>
                        <div class="col-md-6 text-center">
                            <img src="/assets/images/payments.webp" alt="Payment Methods" class="img-fluid" style="height: 75px;">
                        </div>
                    </div>
                </div>
               

                   
                
            </div>
          </div>
        <div class="w-100" style="border-bottom: 1px solid #e7e7e7; "></div>
        <div class="container-fluid container d-flex flex-column align-items-start ">

            <div class="w-100  pt-3" >
                <p class="text-center mb-0" style="font-size: 14px;color:#C0C0C0;">
                    Byte Electronics does not act as an authorized distributor or representative for any products
                    featured on this site. The trademarks, product names, brands, and logos presented are the exclusive
                    property of their respective owners, with full legal rights preserved.
                </p>
            </div>
        </div>
        <div class="w-100" style="border-bottom: 1px solid #e7e7e7; "></div>

        <div class="w-100  pt-1">
            <p class="text-center mb-0" style="font-size: 14px;">
                Copyright © Byte Electronics 2025. All rights reserved.
            </p>
        </div>

    </footer>
</template>

<style scoped>
.custom{
    padding-bottom: 0 !important;
}

.footer {
    background-color: #000;
    color: #fff;
    padding: 1rem 0;
}

.footer_title {
    font-size: 1.25rem;
    font-weight: 600;
}

.footer_menu li a {
    transition: color 0.3s;
}

.footer_menu li a:hover {
    color: #EF4137 !important;
}

.social-icon {
    color: white;
    font-size: 2rem;
    transition: color 0.3s;
}

.social-icon:hover {
    color: #EF4137;
}

.footer-icon {
    color: #fff;
    font-size: 1rem;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .footer_menu {
        gap: 0.5rem;
    }

    .footer_menu li {
        margin-right: 0.5rem !important;
    }

    .footer_top {
        padding-top: 1rem !important;
    }
}

@media (max-width: 575.98px) {
    .footer_menu {
        justify-content: flex-start !important;
    }

    .footer_menu li {
        width: 45%;
        margin-right: 0 !important;
    }

    .footer_title {
        text-align: center;
    }
}
</style>
