<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <header>
            <nav class="navbar modern-navbar">
                <div class="container d-flex align-items-center justify-content-between">
                    <a class="navbar-brand" href="#">
                        <img src="/assets/images/BYTE_LOGO-01 1.png" alt="Logo" class="modern-logo" width="100"
                            height="50">
                    </a>
                    <form class="combined-search-category">
                        <div class="input-group combined-group">
                            <div class="category-wrapper">
                                <button class="btn category-dropdown" type="button" id="categoryDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="pe-1">All Categories</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                    <li><a class="dropdown-item" href="#">Category 1</a></li>
                                    <li><a class="dropdown-item" href="#">Category 2</a></li>
                                    <li><a class="dropdown-item" href="#">Category 3</a></li>
                                </ul>
                            </div>
                            <input type="text" class="form-control combined-search-input" placeholder="Search here...">
                            <button class="btn combined-search-btn" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <div class="phone-contact">
                        <a href="tel:+1234567890" class="phone-link">
                            <img src="/assets/images/Headset.png" alt="Headset" class="phone-icon" width="24"
                                height="24">
                            <div class="phone-info d-flex flex-column">
                                <span class="contact-text">Need Help?</span>
                                <span>+971 42 962 030</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>
            <nav class="navbar modern-navbar navbar-expand-lg navbar-light">
                <div class="container-fluid container d-flex align-items-center justify-content-between">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-70 d-flex justify-content-between"
                            style="width: 70%;">
                            <li class="nav-item dropdown d-flex align-items-center"
                                style="border-right:1px solid #E9E9E9;">
                                <div class="hamburger" @click="toggleHamburger">
                                    <div class="bar1"></div>
                                    <div class="bar2"></div>
                                    <div class="bar3"></div>
                                </div>
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 15px;">
                                    PRODUCTS
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#">Product</a></li>
                                    <li><a class="dropdown-item" href="#">Manufacturer</a></li>
                                    <li><a class="dropdown-item" href="#">Service</a></li>
                                    <li><a class="dropdown-item" href="#">Technical</a></li>
                                </ul>
                            </li>
                            <li><a class="nav-link" href="#" role="button">MANUFACTURES</a></li>
                            <li><a class="nav-link" href="#" role="button">SERVICES & TOOLS</a></li>
                            <li><a class="nav-link" href="#" role="button">TECHNICAL RESOURCES</a></li>
                            <li><a class="nav-link" href="#" role="button">HELP</a></li>
                        </ul>

                        <!-- Right side: Login and Cart -->
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <!-- Login link -->
                            <li class="nav-item" style="display:flex;align-items: center;">
                                <i class="bi bi-person-fill" style="font-size: 20px;"></i>
                                <a class="nav-link" href="#">Login</a>
                            </li>
                            <!-- Cart link -->
                            <li class="nav-item" style="display:flex;align-items: center;">
                                <i class="bi bi-cart-fill" style="font-size: 20px;"></i>
                                <a class="nav-link" href="#">My Cart</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
            <section class="d-flex justify-content-center align-items-center w-100">
                <div class="row hero-banner mt-3">
                    <!-- Big Image -->
                    <div class="col-lg-6 d-flex align-items-stretch">
                        <a href="">
                            <img src="/assets/images/Group 69.webp" class="d-block w-100 img-fluid" alt="" width="600"
                                height="400" style="width: 600px; height: 400px;">
                        </a>
                    </div>

                    <!-- Smaller Images -->
                    <div class="col-lg-6 d-flex flex-column justify-content-between">
                        <!-- First Row -->
                        <div class="row">
                            <div class="col-6 mb-2">
                                <a href="">
                                    <img src="/assets/images/Group 71.png" class="d-block w-100 img-fluid" alt=""
                                        loading="lazy" width="300" height="200" style="width: 300px; height: 200px;">
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="">
                                    <img src="/assets/images/Group 72.png" class="d-block w-100 img-fluid" alt=""
                                        loading="lazy" width="300" height="200" style="width: 300px; height: 200px;">
                                </a>
                            </div>
                        </div>

                        <!-- Second Row -->
                        <div class="row">
                            <div class="col-6">
                                <a href="">
                                    <img src="/assets/images/Group 73.png" class="d-block w-100 img-fluid" alt=""
                                        loading="lazy" width="300" height="200" style="width: 300px; height: 200px;">
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#">
                                    <img src="/assets/images/Group 74.png" class="d-block w-100 img-fluid" alt=""
                                        loading="lazy" width="300" height="200" style="width: 300px; height: 200px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- NEWEST PRODUCTS -->
            <section>
                <div class="mt-4 mb-3 d-flex justify-content-between">
                    <h4 class="fw-bold">NEWEST PRODUCTS</h4>
                    <div>
                        <button class="btn p-0 border"
                            style="border-width: 1px; border-left: none; border-radius: 8px 0px 0px 8px;">
                            <i class="bi bi-chevron-left p-2" style="color: #D2D2D2;"></i>
                        </button>
                        <button class="btn p-0 border"
                            style="border-width: 1px; border-right: none; border-radius: 0px 8px 8px 0px;">
                            <i class="bi bi-chevron-right p-2" style="color: #D2D2D2;"></i>
                        </button>
                    </div>
                </div>
                <div class="swiper newest-products">
                    <div class="swiper-wrapper">
                        <!-- Card 1 -->
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">45555555555</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">22222222222</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba asdasdqwfwgeTLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NsasaEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLasdsadX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupledsasdalay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <!-- Add more swiper-slide elements as needed -->
                    </div>
                </div>
            </section>
            <section class="mt-4 mb-3">
                <div class="small-sub-sec d-flex justify-content-between align-items-center p-4">
                    <h3>NEED HELP?</h3>
                    <div>
                        <i class="bi bi-chat-fill"></i>
                        <a href="#" class="chat">CHAT NOW</a>
                    </div>
                </div>
            </section>
            <section class="mt-4 mb-3">
                <h3 class="text-center mb-4">Featured Manufacturers</h3>
                <div style="text-align: center;">
                    <img src="/assets/images/Group 1.png" style="height: 50px;" alt="" width="1301" height="72">
                </div>
                <div class="featCategory mt-4" style="display: flex;align-items:center;">
                    <div>
                        <img src="/assets/images/tools 1.png" alt="" width="50" height="50">
                        <span>Automation and Process Control</span>
                    </div>
                    <div>
                        <img src="/assets/images/quote 1.png" alt="" width="50" height="50">
                        <span>Passives</span>
                    </div>
                    <div>
                        <img src="/assets/images/best-price 1.png" alt="" width="50" height="50">
                        <span>Interconnect</span>
                    </div>
                    <div>
                        <img src="/assets/images/manager 1.png" alt="" width="50" height="50">
                        <span>Semiconductors</span>
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 1.png" alt="" width="50" height="50">
                        <span>Optoelectronics</span> <!-- cspell:ignore Optoelectronics -->
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 2.png" alt="" width="50" height="50">
                        <span>Electro-mechanical</span>
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 3.png" alt="" width="50" height="50">
                        <span>Embedded Systems</span>
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 4.png" alt="" width="50" height="50">
                        <span>Power</span>
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 5.png" alt="" width="50" height="50">
                        <span>Test</span>
                    </div>
                    <div>
                        <img src="/assets/images/shopping-cart 6.png" alt="" width="50" height="50">
                        <span>Tools</span>
                    </div>
                </div>
            </section>
            <section class="mt-4 mb-3">
                <div class="mt-4 mb-3 d-flex justify-content-between">
                    <h4 class="fw-bold">BEST SELLER</h4>
                    <div>
                        <button class="btn p-0 border"
                            style="border-width: 1px; border-left: none; border-radius: 8px 0px 0px 8px;">
                            <i class="bi bi-chevron-left p-2" style="color: #D2D2D2;"></i>
                        </button>
                        <button class="btn p-0 border"
                            style="border-width: 1px; border-right: none; border-radius: 0px 8px 8px 0px;">
                            <i class="bi bi-chevron-right p-2" style="color: #D2D2D2;"></i>
                        </button>
                    </div>
                </div>
                <div class="swiper newest-products">
                    <div class="swiper-wrapper">
                        <!-- Card 1 -->
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card text-center shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <img src="/assets/images/Group 74.png" class="card-img-top img-fluid" alt="Card image">
                                <!-- New Tag -->
                                <div class="new-tag">NEW</div>
                                <div class="card-body">
                                    <p class="card-title mb-1">Toshiba TLX9152M</p>
                                    <p class="card-text text-muted mb-3">Photo-coupler Photo-relay</p>
                                    <button class="btn-no-border">ADD TO CART</button>
                                </div>
                            </div>
                        </div>
                        <!-- Add more swiper-slide elements as needed -->
                    </div>
                </div>
            </section>
        </main>
        <footer style="background: linear-gradient(to right, #681919, #DC2E2E); color: white;padding-top: 24px;">
            <div style="border-bottom: 1px solid;">
                <div class="container">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                        <!-- Contact Section -->
                        <div>
                            <h4>Contact</h4>
                            <ul style="list-style: none; padding: 0; font-size: 14px;">
                                <li><i class="bi bi-geo-alt-fill" style="font-size: 25px;"></i> <span>Address line one,
                                        Address line two, Address line three</span></li>
                                <li><i class="bi bi-telephone-fill" style="font-size: 25px;"></i> <span>+971 88 999
                                        1000</span></li>
                                <li><i class="bi bi-envelope-fill" style="font-size: 25px;"></i>
                                    <span>info@bytelectronics.com</span>
                                </li>
                                <li><i class="bi bi-chat-fill" style="font-size: 25px;"></i> <span>Chat Now</span></li>
                            </ul>
                        </div>

                        <!-- Company Section -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; color: white;">
                            <!-- Company Links -->
                            <div>
                                <h4>Company</h4>
                                <ul style="list-style: none; padding: 0; font-size: 14px;">
                                    <li><a href="#services" style="color: white; text-decoration: none;">Services</a>
                                    </li>
                                    <li><a href="#careers" style="color: white; text-decoration: none;">Careers</a></li>
                                    <li><a href="#contact" style="color: white; text-decoration: none;">Contact Us</a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Additional Links -->
                            <div>
                                <ul style="list-style: none; padding: 0; font-size: 14px; margin-top: 20px;">
                                    <li><a href="#quality" style="color: white; text-decoration: none;">Quality</a></li>
                                    <li><a href="#environmental"
                                            style="color: white; text-decoration: none;">Environmental</a></li>
                                    <li><a href="#csr" style="color: white; text-decoration: none;">Corporate Social
                                            Responsibility</a></li>
                                </ul>
                            </div>

                            <!-- Subscription Section -->
                            <div style="grid-column: span 2;">
                                <p style="font-size: 16px; margin-bottom: 10px;">GET THE LATEST NEWS</p>
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="Enter your email"
                                        aria-label="Email">
                                    <button class="btn btn-danger" style="background: #EF4137;" type="button">Subscribe
                                        Now</button>
                                </div>
                            </div>
                        </div>

                        <!-- Resource Section -->
                        <div>
                            <h4>Resource</h4>
                            <ul style="list-style: none; padding: 0; font-size: 14px;">
                                <li><a href="#blog" style="color: white; text-decoration: none;">Blog</a></li>
                                <li><a href="#products" style="color: white; text-decoration: none;">Newest Products</a>
                                </li>
                                <li><a href="#manufacturers" style="color: white; text-decoration: none;">New
                                        Manufacturers</a></li>
                                <li><a href="#applications"
                                        style="color: white; text-decoration: none;">Applications</a></li>
                                <li><a href="#tools" style="color: white; text-decoration: none;">Services & Tools</a>
                                </li>
                                <img src="/assets/images/Group 76.png" alt="">
                            </ul>
                        </div>
                        <div style="margin-top: 2rem;">
                            <ul style="list-style: none; padding: 0; font-size: 14px;">
                                <li><a href="#applications"
                                        style="color: white; text-decoration: none;">Applications</a></li>
                                <li><a href="#tools" style="color: white; text-decoration: none;">Services & Tools</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container d-flex justify-content-between align-items-center flex-wrap py-2">
                <!-- Copyright -->
                <p class="mb-0 text-white">Copyright ©2024 Byte Electronics</p>

                <!-- Social Media Icons -->
                <div class="d-flex align-items-center justify-content-end">
                    <span class="me-2">FOLLOW US</span>
                    <a href="#" class="text-white text-decoration-none mx-2">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-white text-decoration-none mx-2">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-white text-decoration-none mx-2">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>
