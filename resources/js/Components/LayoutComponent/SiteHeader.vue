<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);

// Ensure categoriesForMenu is always an array
const categories = computed(() => {
    const cats = page.props.categoriesForMenu;
    console.log('categoriesForMenu:', cats);
    return Array.isArray(cats) ? cats : [];
});

const products = computed(() => {
    const categoryLinks = categories.value.map(category => {
        console.log('Category:', category.name, 'Subcategories:', category.subcategories);
        return {
            name: category.name,
            url: `/products/filter?productType%5B0%5D=${category.id}`,
            subcategories: Array.isArray(category.subcategories) ? category.subcategories.map(subcategory => ({
                id: subcategory.id,
                name: subcategory.name,
                url: `/products/filter?productType%5B0%5D=${category.id}&subCategory%5B0%5D=${subcategory.id}`
            })) : [],
        };
    });
    categoryLinks.push({
        name: 'All Categories',
        url: '/categories/list',
        subcategories: [],
    });
    return categoryLinks;
});

const isDropdownOpenProduct = ref(false);
const isShow = ref('');
const searchQuery = ref('');
const searchResults = ref({ products: [], categories: [], subcategories: [], brands: [] });
const isSearchDropdownOpen = ref(false);
const selectedCategory = ref(null);
const isCategoryDropdownOpen = ref(false);

const selectedCategoryName = computed(() => {
    if (!selectedCategory.value) return 'All Categories';
    const category = categories.value.find(c => c.id === selectedCategory.value);
    return category ? category.name : 'All Categories';
});

const isCategoryActive = (categoryId) => {
    return selectedCategory.value === categoryId;
};

const toggleDropdownProducts = () => {
    isDropdownOpenProduct.value = !isDropdownOpenProduct.value;
    isShow.value = isDropdownOpenProduct.value ? 'show' : '';
};

const toggleCategoryDropdown = () => {
    isCategoryDropdownOpen.value = !isCategoryDropdownOpen.value;
};

const props = defineProps({
    cartCount: Number,
    success: String,
    categoriesForMenu: Array,
});

watch(() => props.success, (newSuccess) => {
    if (newSuccess) {
        Swal.fire({
            title: 'Success!',
            text: newSuccess,
            icon: 'success',
            confirmButtonColor: '#EF4137',
            timer: 2000,
        });
    }
});

const cartCount = ref(page.props.cartCount || 0);
watch(() => page.props.cartCount, (newCount) => {
    cartCount.value = newCount;
});

const searchProducts = async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = { products: [], categories: [], subcategories: [], brands: [] };
        isSearchDropdownOpen.value = false;
        return;
    }
    try {
        const params = { search: searchQuery.value };
        if (selectedCategory.value) {
            params.category_id = selectedCategory.value;
        }
        console.log('Search params:', params);
        const response = await axios.get('/products/search', { params });
        searchResults.value = response.data;
        isSearchDropdownOpen.value = true;
        console.log('Search Results:', searchResults.value);
    } catch (error) {
        console.error('Error searching products:', error);
        searchResults.value = { products: [], categories: [], subcategories: [], brands: [] };
        isSearchDropdownOpen.value = false;
    }
};

const selectProduct = (product) => {
    searchQuery.value = product.name;
    isSearchDropdownOpen.value = false;
    router.visit(`/products/details/${encodeURIComponent(product.slug)}`, {
        preserveState: true,
        preserveScroll: true,
    });
};

const selectCategory = (categoryId) => {
    selectedCategory.value = categoryId;
    console.log('Selected Category:', categoryId);
    const data = categoryId ? { category_id: categoryId } : {};
    router.visit(window.location.pathname, {
        data,
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
    isCategoryDropdownOpen.value = false;
    searchProducts();
};

const selectSearchCategory = (category) => {
    searchQuery.value = category.name;
    isSearchDropdownOpen.value = false;
    router.visit(`/products/filter?productType%5B0%5D=${category.id}`, {
        preserveState: true,
        preserveScroll: true,
    });
};

const selectSubcategory = (subcategory) => {
    searchQuery.value = subcategory.name;
    isSearchDropdownOpen.value = false;
    router.visit(`/products/filter?productType%5B0%5D=${subcategory.category_id}&subCategory%5B0%5D=${subcategory.id}`, {
        preserveState: true,
        preserveScroll: true,
    });
};

const selectBrand = (brand) => {
    searchQuery.value = brand.name;
    isSearchDropdownOpen.value = false;
    router.visit(`/products/filter?manufacturer%5B0%5D=${brand.id}`, {
        preserveState: true,
        preserveScroll: true,
    });
};

watch(searchQuery, () => searchProducts(), { debounce: 300 });

const addToCart = async (productId, quantity = 1) => {
    router.post(`/cart/add/${productId}`, { quantity }, {
        preserveScroll: true,
        only: ['cartCount', 'success'],
        onSuccess: () => {
            toast.success("🛒 Item added to cart!", {
                position: "top-right",
                autoClose: 1500,
                theme: "colored",
            });
        },
        onError: () => {
            toast.error("❌ Failed to add item.", {
                position: "top-right",
                autoClose: 1500,
                theme: "colored",
            });
        },
    });
};

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');
    if (categoryId) {
        selectedCategory.value = parseInt(categoryId, 10);
        searchProducts();
    }
    console.log('Component mounted, selectedCategory:', selectedCategory.value);
});

const myFunction = (element) => {
    element.classList.toggle('change');
};
</script>

<template>
    <header>
        <nav class="navbar modern-navbar header">
            <div class="container d-flex align-items-center justify-content-between">
                <Link href="/">
                <img src="/assets/images/BYTE_LOGO.webp" alt="Logo" class="modern-logo" />
                </Link>
                <div class="mobile-screen" style="display: none;">
                    <Link href="tel:+97142962030" class="phone-link d-flex align-items-center">
                    <img src="/assets/images/Headset.png" alt="Headset" class="phone-icon" style="height: 40px;" />
                    <div class="phone-info d-flex flex-column">
                        <span class="contact-text">Need Help?</span>
                        <span class="contact-no" style="font-weight: 600;font-size: 13px;">+971 42 962 030</span>
                    </div>
                    </Link>
                </div>
                <form class="combined-search-category"
                    @submit.prevent="searchResults.products.length && selectProduct(searchResults.products[0])">
                    <div class="search-container">
                        <div class="input-group search-box-with-category">
                            <!-- Category dropdown -->
                            <div class="input-group-prepend category-dropdown-prepend" style="padding: 3px;">
                                <button class="btn dropdown-toggle category-dropdown-btn" type="button"
                                    @click="toggleCategoryDropdown">
                                    <span class="category-label">{{ selectedCategoryName }}</span>
                                </button>
                                <ul class="dropdown-menu category-dropdown-menu"
                                    :class="{ show: isCategoryDropdownOpen }">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)"
                                            @click.stop="selectCategory(null)"
                                            :class="{ 'active': isCategoryActive(null) }"
                                            style="font-size: 12px !important;">
                                            All Categories
                                        </a>
                                    </li>
                                    <li v-for="category in categories" :key="category.id">
                                        <a class="dropdown-item" href="javascript:void(0)"
                                            @click.stop="selectCategory(category.id)"
                                            :class="{ 'active': isCategoryActive(category.id) }">
                                            {{ category.name }}
                                        </a>
                                    </li>
                                    <li v-if="!categories.length">
                                        <a class="dropdown-item" href="javascript:void(0)">No Categories Available</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Search input -->
                            <input type="text" class="form-control search-input" placeholder="Search products..."
                                style="border: 0px;font-size: 13px;" v-model="searchQuery" aria-label="Search products">
                            <!-- Search button -->
                            <button class="btn search-btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <!-- Search results dropdown -->
                            <div class="search-results-dropdown"
                                v-if="isSearchDropdownOpen && searchResults.products.length">
                                <div class="search-results-container">
                                    <ul class="search-results-list">
                                        <!-- Products only when a category is selected -->
                                        <template v-if="selectedCategory">
                                            <li v-if="searchResults.products.length" class="search-result-header">
                                                Products
                                            </li>
                                            <li v-for="product in searchResults.products" :key="'product-' + product.id"
                                                @click="selectProduct(product)" class="search-result-item">
                                                <img :src="product.image" alt="Product" class="product-image" />
                                                <div class="product-info">
                                                    <span class="product-name">{{ product.name }}</span>
                                                    <span class="product-brand">{{ product.brand }}</span>
                                                    <span class="product-partno">{{ product.part_no }}</span>
                                                </div>
                                            </li>
                                        </template>
                                        <!-- All results when no category is selected -->
                                        <template v-else>
                                            <li v-if="searchResults.products.length" class="search-result-header">
                                                Products
                                            </li>
                                            <li v-for="product in searchResults.products" :key="'product-' + product.id"
                                                @click="selectProduct(product)" class="search-result-item">
                                                <img :src="product.image" alt="Product" class="product-image" />
                                                <div class="product-info">
                                                    <span class="product-name">{{ product.name }}</span>
                                                    <span class="product-brand">{{ product.brand }}</span>
                                                    <span class="product-partno">{{ product.part_no }}</span>
                                                </div>
                                            </li>
                                            <li v-if="searchResults.categories.length || searchResults.subcategories.length"
                                                class="search-result-header">
                                                Categories
                                            </li>
                                            <li v-for="category in searchResults.categories"
                                                :key="'category-' + category.id" @click="selectSearchCategory(category)"
                                                class="search-result-item search-result-item-no-image">
                                                <div class="product-info">
                                                    <span class="product-name">{{ category.name }}</span>
                                                </div>
                                            </li>
                                            <li v-for="subcategory in searchResults.subcategories"
                                                :key="'subcategory-' + subcategory.id"
                                                @click="selectSubcategory(subcategory)"
                                                class="search-result-item search-result-item-no-image">
                                                <div class="product-info">
                                                    <span class="product-name">{{ subcategory.name }}</span>
                                                </div>
                                            </li>
                                            <li v-if="searchResults.brands.length" class="search-result-header">
                                                Manufacturers
                                            </li>
                                            <li v-for="brand in searchResults.brands" :key="'brand-' + brand.id"
                                                @click="selectBrand(brand)"
                                                class="search-result-item search-result-item-no-image">
                                                <div class="product-info">
                                                    <span class="product-name">{{ brand.name }}</span>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="phone-contact d-flex flex-column align-items-end">
                    <a href="tel:+97142962030" class="phone-link d-flex align-items-center">
                        <img src="/assets/images/Headset.png" alt="Headset" class="phone-icon" style="height: 40px;" />
                        <div class="phone-info d-flex flex-column">
                            <span class="contact-text">Need Help?</span>
                            <span style="font-weight: 600;font-size: 13px;">+971 42 962 030</span>
                        </div>
                    </a>
                </div>
            </div>
        </nav>
        <nav class="navbar modern-navbar navbar-expand-lg navbar-light under-navbar" style="padding: 0px 2rem;">
            <div class="container-fluid container d-flex align-items-center justify-content-between">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-70 d-flex" style="gap: 20px;">
                        <li class="nav-item dropdown d-flex align-items-center" style="border-right:1px solid #E9E9E9;">
                            <div class="hamburger" @click="myFunction($event.target)">
                                <div class="bar bar1"></div>
                                <div class="bar2"></div>
                                <div class="bar3"></div>
                            </div>
                            <a class="nav-link dropdown-toggle" :class="{ show: isShow === 'show' }" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" :aria-expanded="isDropdownOpenProduct"
                                @click="toggleDropdownProducts">
                                PRODUCTS
                            </a>
                            <ul class="dropdown-menu dropdown-menu-1 enhanced-dropdown"
                                :class="{ show: isShow === 'show' }" aria-labelledby="navbarDropdown">
                                <li v-for="product in products" :key="product.name" class="dropdown-item-wrapper">
                                    <Link class="dropdown-item d-flex justify-content-between align-items-center"
                                        :href="product.url">
                                    {{ product.name }}
                                    <i class="bi bi-chevron-right ms-2" style="font-size: 12px;"></i>
                                    </Link>
                                    <ul v-if="product.subcategories.length" class="dropdown-menu sub-dropdown">
                                        <li v-for="subcategory in product.subcategories" :key="subcategory.id">
                                            <Link class="dropdown-item" :href="subcategory.url">
                                            {{ subcategory.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <Link class="nav-link product-link" href="/products/filter">PRODUCTS</Link>
                        </li>
                        <Link class="nav-link" href="/">HOME</Link>
                        <li>
                            <Link class="nav-link" href="/brands/list">MANUFACTURERS</Link>
                        </li>
                        <li>
                            <Link class="nav-link" href="/categories/list">CATEGORIES</Link>
                        </li>
                        <li>
                            <Link class="nav-link" href="/blogs/">BLOG</Link>
                        </li>
                        <li>
                            <Link class="nav-link" href="/contact-us">CONTACT US</Link>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item d-flex align-items-center d-lg-none">
                            <template v-if="!isAuthenticated">
                                <Link class="nav-link login-btn p-0" href="/login">
                                <i class="bi bi-person-circle me-1"></i>Login
                                </Link>
                            </template>
                            <template v-else>
                                <div class="dropdown user-profile-dropdown">
                                    <Link href="#"
                                        class="d-flex align-items-center text-decoration-none dropdown-toggle user-profile-toggle"
                                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar">
                                        <i class="bi bi-person-check-fill"></i>
                                    </div>
                                    <span class="user-greeting">My Profile</span>
                                    </Link>
                                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu"
                                        aria-labelledby="userDropdown">
                                        <li class="dropdown-header">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md me-2">
                                                    <i class="bi bi-person-circle"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ page.props.auth.user.name }}</h6>
                                                    <small class="text-muted">{{ page.props.auth.user.email }}</small>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <Link class="dropdown-item" href="/profile" style="color: #000000;">
                                            <i class="bi bi-person me-2" style="color: #000000;"></i>
                                            My Profile
                                            </Link>
                                        </li>
                                        <li>
                                            <Link class="dropdown-item" href="/my-orders" style="color: #000000;">
                                            <i class="bi bi-bag me-2" style="color: #000000;"></i>
                                            My Orders
                                            </Link>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <Link class="dropdown-item text-danger" href="/logout" method="post"
                                                as="button" style="font-weight: 500;">
                                            <i class="bi bi-box-arrow-right me-2" style="font-weight: 500;"></i>
                                            Log Out
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                        </li>
                        <li class="nav-item d-flex align-items-center ms-2 d-lg-none cartIconContainer">
                            <Link class="nav-link" href="/cart" style="display: flex; align-items: center;">
                            <i class="bi bi-cart-fill" style="font-size: 20px;"></i>
                            <span class="ms-1">{{ cartCount }}</span>
                            </Link>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item d-flex align-items-center ms-2 loginBtnContainer">
                            <template v-if="!isAuthenticated">
                                <Link class="nav-link login-btn " href="/login">
                                <i class="bi bi-person-circle me-1"></i>
                                <span>Login</span>
                                </Link>
                            </template>
                            <template v-else>
                                <div class="dropdown user-profile-dropdown">
                                    <Link href="#"
                                        class="d-flex align-items-center text-decoration-none dropdown-toggle user-profile-toggle"
                                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-avatar">
                                        <i class="bi bi-person-check-fill"></i>
                                    </div>
                                    <span class="user-greeting">My Profile</span>
                                    </Link>
                                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu"
                                        aria-labelledby="userDropdown">
                                        <li class="dropdown-header">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md me-2">
                                                    <i class="bi bi-person-circle"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ page.props.auth.user.name }}</h6>
                                                    <small class="text-muted">{{ page.props.auth.user.email }}</small>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <Link class="dropdown-item" href="/profile" style="color: #000000;">
                                            <i class="bi bi-person me-2" style="color: #000000;"></i>
                                            My Profile
                                            </Link>
                                        </li>
                                        <li>
                                            <Link class="dropdown-item" href="/my-orders" style="color: #000000;">
                                            <i class="bi bi-bag me-2" style="color: #000000;"></i>
                                            My Orders
                                            </Link>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <Link class="dropdown-item text-danger" href="/logout" method="post"
                                                as="button" style="font-weight: 500;">
                                            <i class="bi bi-box-arrow-right me-2" style="font-weight: 500;"></i>
                                            Log Out
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                        </li>
                        <li class="nav-item" style="display:flex;align-items: center;">
                            <Link class="nav-link" href="/cart" style="display: flex;align-items: center;">
                            <i class="bi bi-cart-fill" style="font-size: 20px;"></i>
                            <span class=""> My Cart ({{ cartCount }})</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="custom-bg">
            <div class="container-fluid container d-flex align-items-center justify-content-between">
                <!-- Breadcrumbs or other content -->
            </div>
        </div>
    </header>
</template>

<style scoped>
.search-result-header {
    padding: 0.5rem 1rem;
    font-weight: bold;
    font-size: 0.9rem;
    background: #f8f9fa;
    color: #333;
    border-bottom: 1px solid #e0e0e0;
    text-transform: uppercase;
}

.search-result-item-no-image {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid #f0f0f0;
}

.search-result-item-no-image:hover {
    background: #f8f9fa;
}

.enhanced-dropdown {
    top: calc(100% + 5px) !important;
    left: 0 !important;
    border: none !important;
    border-radius: 0 0 8px 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    padding: 8px 5px !important;
    min-width: 220px !important;
    background: white !important;
    border-top: 3px solid #4a6bff !important;
}

.sub-dropdown {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    min-width: 200px;
    padding-left: 10px;
    font-size: 0.9rem;
    background: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    max-height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.sub-dropdown::-webkit-scrollbar {
    width: 6px;
}

.sub-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sub-dropdown::-webkit-scrollbar-thumb {
    background: #4a6bff;
    border-radius: 3px;
}

.sub-dropdown::-webkit-scrollbar-thumb:hover {
    background: #3b5ad9;
}

.dropdown-item-wrapper:hover .sub-dropdown {
    display: block;
}

.dropdown-item:hover {
    background: #f8f9fa !important;
    color: #4a6bff !important;
}

.combined-search-category {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

.search-box-with-category {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: visible;
    border: 1px solid #DEDEDE;
}

.nav-link {
    font-size: 13px !important;
}

.category-dropdown-prepend {
    border-right: 1px solid #e0e0e0;
}

.category-dropdown-btn {
    color: #000000;
    border: none;
    padding: 0.5rem 1rem;
    font-weight: 500;
    white-space: nowrap;
    display: flex;
    align-items: center;
    font-size: 13px;
    border-right: 1px solid #DEDEDE;
    border-radius: 0px !important;
}

.category-dropdown-btn:hover {
    background: #f8f9fa;
}

.category-label {
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.category-dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
    word-break: break-all;
}

.search-input:focus {
    box-shadow: none;
    outline: none;
}

.custom-bg {
    background-color: #ECECEC;
}

.search-btn {
    background: #EF4137;
    color: white;
    border: none;
    border-radius: 0 10px 10px 0;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: #EF4137;
}

p {
    padding: 0;
    margin: 0;
}

.search-container {
    position: relative;
    width: 100%;
}

.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
}

.search-results-container {
    background: white;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-height: 400px;
    overflow-y: auto;
    margin-top: 5px;
}

.search-results-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.search-result-item {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid #f0f0f0;
}

.search-result-item:hover {
    background: #f8f9fa;
}

.product-image {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-right: 12px;
    border-radius: 4px;
}

.navbar-toggler:focus {
    outline: none;
    box-shadow: none;
}

.product-info {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.product-name {
    font-weight: 500;
    margin-bottom: 2px;
}

.product-partno {
    font-size: 14px;
    margin-bottom: 2px;
}

.product-brand {
    font-size: 14px;
    margin-bottom: 2px;
}

.product-price {
    font-size: 0.85rem;
    color: #3b82f6;
}

.product-link {
    display: none;
}

@media (max-width: 990px) {
    .product-link {
        display: block !important;
    }

    .mobile-screen {
        display: block !important;
    }

    .phone-contact {
        display: none !important;
    }

    .navbar-toggler {
        margin: 5px 0;
    }

    .navbar-nav {
        gap: 0px !important;
    }

    .navbar-toggler-icon {
        width: 1.2em;
        height: 1.2em;
    }

    .navbar-toggler {
        padding: 5px;
    }

    .nav-link {
        font-weight: 500;
    }

    .navbar-nav .nav-item.d-lg-none {
        display: flex !important;
        align-items: center;
    }
}

@media (max-width: 768px) {
    .search-box-with-category {
        border-radius: 10px;
    }

    .category-dropdown-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }

    .category-label {
        max-width: 80px;
    }

    .search-input {
        padding: 0.6rem 0.8rem;
        font-size: 0.5rem;
    }
}

@media (max-width: 576px) {
    .category-dropdown-btn {
        padding: 0.2rem 0.6rem;
    }

    .phone-contact {
        display: none !important;
    }

    .category-label {
        max-width: 60px;
    }

    .search-result-item {
        padding: 0.6rem;
    }

    .product-image {
        width: 35px;
        height: 35px;
        margin-right: 8px;
    }

    .modern-navbar {
        padding: 0px;
    }

    .header {
        padding-bottom: 10px;
    }

    .combined-search-category {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .under-navbar {
        padding: 0px !important;
    }

    .navbar-toggler {
        margin: 5px 0;
    }

    .modern-logo {
        width: 90px !important;
    }

    .phone-icon {
        height: 30px !important;
    }

    .contact-no,
    .contact-text {
        font-size: 10px !important;
    }
}

.category-wrapper {
    position: relative;
}

.category-dropdown {
    white-space: nowrap;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-right: none;
    border-radius: 0.25rem 0 0 0.25rem;
}

.combined-group {
    flex-grow: 1;
    margin-left: -1px;
}

.combined-search-input {
    border-radius: 0;
}

.combined-search-btn {
    border-radius: 0 0.25rem 0.25rem 0;
}

.dropdown-menu {
    min-width: 140px;
    font-size: 12px;
    z-index: 1000;
}

.dropdown-item.active,
.dropdown-item:active {
    background: #EF4137;
}

.search-dropdown {
    max-height: 300px;
    overflow-y: auto;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 1000;
    display: block;
}

.dropdown-item {
    cursor: pointer;
    padding: 3px 8px;
    display: flex;
    align-items: center;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.localization-btn {
    padding: 2px 6px;
    font-size: 12px;
    line-height: 1.2;
    display: flex;
    align-items: center;
}

.fi {
    width: 16px;
    height: 12px;
    display: inline-block;
    background-size: cover;
}

@media (max-width: 990px) {
    .hamburger {
        display: none !important;
    }

    .login-btn {
        padding: 0px !important;
    }

    .loginBtnContainer {
        margin-left: 0px !important;
    }
    .cartIconContainer{
        margin-left: 0px !important;
    }
}

.login-btn {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: #000000;
    transition: all 0.3s ease;
}

.login-btn:hover {
    color: #EF4137;
}

.login-btn i {
    font-size: 1.25rem;
}

.user-profile-dropdown {
    position: relative;
}

.user-profile-toggle {
    padding: 0.5rem;
    border-radius: 50px;
    transition: all 0.3s ease;
    color: #000000;
}

.user-avatar {
    color: #000000;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
}

.user-avatar i {
    font-size: 1.1rem;
}

.user-greeting {
    font-weight: 500;
    color: #000000;
    font-size: 14px;
}

.user-dropdown-menu {
    min-width: 280px;
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 0.5rem;
    margin-top: 10px;
}

.dropdown-header {
    padding: 0.75rem 1rem;
}

.avatar-md {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #EF4137;
}

.avatar-md i {
    font-size: 1.25rem;
}

.dropdown-item {
    padding: 5px 10px;
    border-radius: 8px;
    margin: 0.15rem 0;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}

.dropdown-item:hover {
    background-color: rgba(239, 65, 55, 0.1);
    color: #EF4137;
    transform: translateX(3px);
}

.dropdown-divider {
    border-color: rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
    .user-dropdown-menu {
        min-width: 240px;
    }

    .user-profile-toggle {
        padding: 0.25rem;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
    }
}

.hamburger.change .bar1 {
    transform: rotate(-45deg) translate(-5px, 6px);
}

.hamburger.change .bar2 {
    opacity: 0;
}

.hamburger.change .bar3 {
    transform: rotate(45deg) translate(-5px, -6px);
}

.bar {
    width: 24px;
    height: 2px;
    background-color: #333;
    margin: 4px 0;
    transition: 0.4s;
}
</style>
