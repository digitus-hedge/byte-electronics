<template>
    <div class="categories-grid">
        <div class="category-card" v-for="(category, categoryIndex) in categories" :key="categoryIndex">
            <!-- Category Title -->
            <div class="category-header d-flex align-items-center gap-3 mb-3" @click="toggle(categoryIndex)"
                style="cursor: pointer;">
                <h6>
                    <Link :href="category.url" @click.stop style="color: #ef4137;">{{ category.name }}</Link>
                </h6>
                <i :class="activeIndex === categoryIndex ? 'fa fa-angle-down' : 'fa fa-angle-right'" aria-hidden="true"></i>
            </div>

            <!-- Floating dropdown content -->
            <div v-if="activeIndex === categoryIndex" class="dropdown-content">
                <div v-for="(subCategory, subIndex) in category.items" :key="subIndex" class="w-100 mb-3">
                    <Link :href="subCategory.url">
                        <p class="category-title" style="font-size: 15px; font-weight: 600; color: #212529;">
                            {{ subCategory.name }}
                        </p>
                    </Link>

                    <div class="sub-cat-head" v-if="subCategory.items && subCategory.items.length">
                        <ul class="categories" style="margin-left: 2rem;">
                            <li v-for="(item, index) in subCategory.items" :key="index"
                                :style="getItemStyle(index, subCategory.items.length)">
                                <Link v-if="item.name" :href="item.url">{{ item.name }}</Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const activeIndex = ref(null); // ← changed from expanded object to single value

const toggle = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index; // ← close previous, open new
};

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
    padding: 20px;
    margin-top: 5px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-height: 500px;
    overflow-y: auto;
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
