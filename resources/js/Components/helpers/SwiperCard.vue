<!-- SwiperCard.vue -->
<template>
    <section>
        <div class="mt-4 mb-3 d-flex justify-content-between">
            <h4 class="fw-bold" style="font-size: 1.3rem;background:#0000;color:#000000;">{{ title }}</h4>
            <div>
                <button class="btn p-0 border swiper-button" @click="swiperInstance && swiperInstance.slidePrev()"
                    style="border-width: 1px; border-left: none; border-radius: 8px 0px 0px 8px;">
                    <i class="bi bi-chevron-left p-2" style="color: #D2D2D2;"></i>
                </button>
                <button class="btn p-0 border swiper-button" @click="swiperInstance && swiperInstance.slideNext()"
                    style="border-width: 1px; border-right: none; border-radius: 0px 8px 8px 0px;">
                    <i class="bi bi-chevron-right p-2" style="color: #D2D2D2;"></i>
                </button>
            </div>
        </div>

        <!-- Swiper Component -->
        <!-- <pre>{{ products }}</pre> -->
        <Swiper ref="mySwiper" :modules="[Autoplay, Navigation]"
            :autoplay="{ delay: 3000, disableOnInteraction: false }" :spaceBetween="20" :slidesPerView="5" :breakpoints="{
                320: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 3, spaceBetween: 15 },
                1024: { slidesPerView: 5, spaceBetween: 20 }
            }" @swiper="setSwiperInstance">
            <SwiperSlide v-for="(product, index) in products" :key="index"
                :class="{ 'last-card': index === products.length - 1 }">
                <!-- <pre>{{product  }}</pre> -->
                <div class="card text-center shadow-sm h-100"
                    style="border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">

                    <div class="swiper-img-container" style="position: relative;">
                        <Link :href="`${product.slug}`" class="view-icon-wrapper">
                        <i class="fas fa-eye"></i>
                        <span>View Product</span>
                        </Link>

                        <img :src="getValidImage(product.image)" class="card-img-top img-fluid" :alt="product.name"
                            @error="handleImageError">

                        <div class="new-tag" style="background: #EF4137;" v-if="product.isNew">NEW</div>
                    </div>



                    <div class="card-body d-flex flex-column" style="flex: 1;">

                        <Link :href="`${product.slug}`">
                        <!-- <pre>{{ product }}</pre> -->
                        <p class="card-title mb-1" style="font-size: 14px; color: #000000;">
                            {{ product.name }}
                            <span>{{ product.part_no ? '(' + product.part_no + ')' : '' }}</span>
                        </p>
                        </Link>

                        <p v-if="isBlog" class="card-text text-muted  text-truncate"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;font-size: 14px;color:#000000;">
                            {{ product.description }}
                        </p>
                        <Link v-if="isBlog" class="btn-no-border fw-bold mt-auto" :style="{ fontSize: '12px' }">
                        READ ARTICLE
                        </Link>
                        <!-- <button v-else class="btn-no-border fw-bold mt-auto" @click="addToCart(product.id)"
                            :style="{ fontSize: '12px' }">
                            ADD TO CART
                        </button> -->
                        <button v-if="product.has_price" class="btn-no-border fw-bold mt-auto"
                            @click="handleAddToCart(product, product.min_qty, product.name, product.total_price)"
                            :style="{ fontSize: '12px' }">
                            ADD TO CART
                        </button>



                        <button v-else class="btn-no-border fw-bold mt-auto" @click="requestQuote(product.id)"
                            :style="{ fontSize: '12px' }">
                            REQUEST FOR QUOTATION
                        </button>



                    </div>
                </div>
            </SwiperSlide>
            <!-- modal -->

            <!-- <div v-if="showConfirm" class="modal-overlay">
                <div class="modal-content in-modal">
                    <button class="modal-close" @click="cancelAdd">✕</button>
                    <p>
                        You are about to add

                        <strong>{{ selectedProduct.qty }}</strong> units of
                        <strong>{{ selectedProduct.name }}</strong> <br>
                        for <strong>{{ selectedProduct.total_price }} {{ selectedProduct.currency_symbol }}</strong>.
                    </p>
                    <div class="flex justify-end gap-2 mt-4">
                        <button class="btn btn-secondary" @click="cancelAdd">Cancel</button>
                        <button class="btn btn-primary" @click="confirmAddToCart">Proceed</button>
                    </div>
                </div>
            </div> -->

            <div v-if="showConfirm" class="modal-overlay">
    <div class="modal-content in-modal">
      <button class="modal-close" @click="cancelAdd">✕</button>
      <p class="modal-text">
        You are about to add
        <strong>{{ selectedProduct.qty }}</strong> units of
        <strong>{{ selectedProduct.name }}</strong> <br>
        for <strong>{{ selectedProduct.total_price }} {{ selectedProduct.currency_symbol }}</strong>.
      </p>
      <div class="modal-actions">
        <button class="btn btn-secondary" @click="cancelAdd">Cancel</button>
        <button class="btn btn-primary" @click="confirmAddToCart">Proceed</button>
      </div>
    </div>
  </div>


            <div v-if="showQuoteModal" class="modal-overlay">
                <div class="modal-content in-modal">
                    <button class="modal-close" @click="closeQuoteModal">✕</button>
                    <!-- <h2 class="modal-title">Request a Quote</h2> -->
                    <RequestQuoteForm :product-id="selectedProductId" @submitted="closeQuoteModal" />
                </div>
            </div>

            <!--  -->
        </Swiper>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Navigation } from "swiper";
import "swiper/swiper-bundle.min.css";
import { Link, router } from '@inertiajs/vue3';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import toastr from "toastr";
import RequestQuoteForm from '@/Components/helpers/RequestQuoteForm.vue'
import axios from "axios";

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    title: {
        type: String,
        default: 'NEWEST PRODUCTS'
    },
    isBlog: {
        type: Boolean,
        default: false
    },
});

const swiperInstance = ref(null);

const setSwiperInstance = (swiper) => {
    swiperInstance.value = swiper;
};
const getValidImage = (image) => {
    if (!image || image.includes('default.png')) {
        return '/assets/images/dummy_product.webp';
    }
    const validExtensions = ['.jpg', '.jpeg', '.png', '.webp', '.gif', '.svg'];
    const hasValidExtension = validExtensions.some(ext =>
        image.toLowerCase().endsWith(ext)
    );
    return hasValidExtension ? image : '/assets/images/dummy_product.webp';
};
const handleImageError = (e) => {
    e.target.src = '/assets/images/dummy_product.webp';
};
const addToCart = async (productId, quantity = 1) => {
    console.log(productId);
    router.post(`/cart/add/${productId}`, { quantity }, {
        preserveScroll: true,
        only: ['cartCount'],
        onSuccess: () => {
            toast.success("🛒 Item added to cart!", {
                position: "top-right",
                autoClose: 1500,
                hideProgressBar: false,
                closeButton: true,
                className: 'custom-toast-success',
                style: {
                    fontSize: '14px',
                    fontWeight: '500',
                    padding: '12px 20px',
                },
                iconTheme: {
                    primary: '#10B981',
                    secondary: '#FFFFFF',
                }
            });
        },
        onError: () => {
            toast.error("❌ Failed to add item", {
                position: "top-right",
                autoClose: 1500,
                hideProgressBar: false,
                closeButton: true, className: 'custom-toast-error',
                style: {
                    background: '#FFFFFF',
                    color: '#1F2937',
                    border: '1px solid #E5E7EB',
                    borderRadius: '8px', boxShadow: '0 2px 8px rgba(0, 0, 0, 0.1)',
                    fontSize: '14px',
                    fontWeight: '500', padding: '12px 20px',
                },
                iconTheme: {
                    primary: '#EF4444', // Red error color
                    secondary: '#FFFFFF',
                }
            });
        },
    });
};

// <!-- request quote model -->


const showQuoteModal = ref(false)
const selectedProductId = ref(null)

const requestQuote = (id) => {
    selectedProductId.value = id
    showQuoteModal.value = true
}

const closeQuoteModal = () => {
    showQuoteModal.value = false
    selectedProductId.value = null
}
//show confirm cart
const showConfirm = ref(false)
const selectedProduct = ref(null)

function handleAddToCart(product, minQty, name, total_price) {

    if (!product) {
        console.log("No product found");
        return;
    }

    // If quantity is 1 → directly add to cart
    if (minQty === 1) {
        addToCart(product.id, 1);
        return;
    }

    // Otherwise → show confirmation popup
    selectedProduct.value = {
        id: product.id,
        name: name,
        qty: minQty,
        total_price: total_price,
        currency_symbol: product.currency_symbol ?? '' // fallback
    };

    showConfirm.value = true;
}

function confirmAddToCart() {
    if (selectedProduct?.value) {
        console.log('confirmAddToCart', selectedProduct.value);

        // If id is missing, fallback to name or some other key
        const productId = selectedProduct.value.id ?? selectedProduct.value.product_id;

        addToCart(productId, selectedProduct.value.qty)

        showConfirm.value = false
        selectedProduct.value = null
    }
}

function cancelAdd() {
    showConfirm.value = false
    selectedProduct.value = null
}

</script>



<style scoped>
.last-card {
    /* margin-bottom: 2rem; */
}

.swiper-button {
    transition: all 0.3s ease-in-out;
}

.active-btn {
    background-color: black !important;
}

.active-btn i {
    color: white !important;
}

.swiper-slide {
    height: auto;
}

.card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.card-body {
    flex: 1;
}

/* For the ellipsis on description */
.text-truncate-multiline {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Ensure images maintain aspect ratio */
.card-img-container {
    position: relative;
    overflow: hidden;
}

.swiper-img-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.swiper-img-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.view-icon-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: inline-flex;
    /* Keep in single line */
    align-items: center;
    gap: 4px;
    color: black;
    font-size: 12px;
    /* Small font size */
    /* background: rgba(255, 255, 255, 0.85); */
    padding: 5px 8px;
    border-radius: 4px;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
    /* Prevent breaking into two lines */
}

.swiper-img-container:hover .view-icon-wrapper {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.05);
}

.view-icon-wrapper i {
    font-size: 12px;
    /* Make icon match text size */
}



</style>

<style>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.modal-content {
  background: #fff;
  padding: 28px 24px;
  border-radius: 14px;
  max-width: 440px;
  width: 90%;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
  position: relative;
  animation: fadeIn 0.25s ease-out;
  font-family: 'Helvetica Neue', Arial, sans-serif;
}

.modal-close {
  position: absolute;
  top: 12px;
  right: 12px;
  background: transparent;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #555;
  transition: color 0.2s ease;
}

.modal-close:hover {
  color: #000;
}

.modal-text {
  font-size: 14px;
  line-height: 1.7;
  color: #222;
  margin-bottom: 22px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 20px; /* Increased space between buttons */
}

.btn {
  padding: 10px 18px;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
  font-weight: 500;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: #ef4137;
  color: white;
}

.btn-primary:hover {
  background: #ef4137;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
