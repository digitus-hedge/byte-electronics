<template>
    <div class="categories-grid">
        <div class="category-card" v-for="(category, categoryIndex) in categories" :key="categoryIndex">
            <!-- Category Title -->
            <div class="category-header d-flex align-items-center gap-3 mb-3" @click="toggle(categoryIndex)"
                style="cursor: pointer;">
                <h6>
                    <Link :href="category.url" @click.stop style="color: #ef4137;">{{ category.name }}</Link>
                </h6>
                <i :class="activeIndex === categoryIndex ? 'fa fa-angle-down' : 'fa fa-angle-right'"
                    aria-hidden="true"></i>
            </div>

            <!-- Floating dropdown content -->
            <div v-if="activeIndex === categoryIndex" class="dropdown-content">
                <div v-for="(subCategory, subIndex) in category.items" :key="subIndex" class="grid-item">
                    <div @click.stop="navigateTo(subCategory.url)" style="cursor: pointer;">
                        <span class="bullet">&#8250;</span>
                        {{ subCategory.name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const navigateTo = (url) => {
    activeIndex.value = null;
    router.visit(url);
};

const activeIndex = ref(null);

const toggle = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};

const closeDropdown = (e) => {
    if (!e.target.closest('.category-card') && !e.target.closest('.dropdown-content')) {
        activeIndex.value = null;
    }
};

onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));

const getBackgroundColor = (index) => ({
    backgroundColor: Math.floor(index / 3) % 2 === 0 ? '#FAFAFB' : '#fff'
});

const getItemStyle = (index, totalItems) => ({
    ...getBackgroundColor(index),
    width: '33.33%',
    display: 'inline-block'
});
</script>

<style scoped>
.categories {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    list-style-type: none;
    font-size: 14px;
    width: 100%;
}

.categories li {
    padding: 10px;
    box-sizing: border-box;
    padding-left: 2.5rem;
}

.categories li a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

.categories li a:hover {
    text-decoration: underline;
    color: #EF4137;
}

h6 {
    text-transform: uppercase;
    color: #EF4137;
    margin-bottom: 0;
    font-weight: bold;
}

.sub-cat-head {
    margin-top: 10px;
}

.category-title {
    font-size: 15px;
    font-weight: 600;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    width: 100%;
}

.category-card {
    border: 1px solid #CACACA;
    border-radius: 8px;
    padding: 20px;
    background-color: #fff;
    transition: box-shadow 0.3s ease;
    position: relative;
    /* Important for absolute positioning */
}

.category-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}


@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
}

.category-header {
    position: relative;
    z-index: 2;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #CACACA;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 5px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.grid-item a {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #3A3A3A;
    font-size: 14px;
    padding: 4px 6px;
    border-radius: 4px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.grid-item a:hover {
    color: #ef4137;
    background-color: #fdf1f0;
    padding-left: 10px;
}

.dropdown-content .grid-item div {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #3A3A3A;
    font-size: 14px;
    padding: 4px 6px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.dropdown-content .grid-item div:hover {
    color: #ef4137;
    background-color: #fdf1f0;
    padding-left: 10px;
}

.bullet {
    color: #ef4137;
    font-size: 18px;
    line-height: 1;
}

.dropdown-content .categories {
    display: block;
    width: 100%;
}

.dropdown-content .categories li {
    width: 100% !important;
    display: list-item !important;
}

@media (max-width: 1200px) {
    .categories li {
        width: 50% !important;
    }
}

@media (max-width: 768px) {
    .categories li {
        width: 100% !important;
    }

    .sub-cat-head {
        margin-top: 5px;
    }

    .categories {
        font-size: 12px;
    }
}

@media (max-width: 580px) {
    .categories {
        flex-direction: column !important;
    }

    .categories li {
        padding-left: 10px !important;
    }
}

li {
    list-style-type: disc !important;
    padding-left: 0px !important;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    width: 100%;
    margin-top: 20px;
}

.category-card {
    border: 1px solid #CACACA;
    border-radius: 8px;
    padding: 20px;
    background-color: #fff;
    transition: box-shadow 0.3s ease;
}

.category-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
}
</style>
