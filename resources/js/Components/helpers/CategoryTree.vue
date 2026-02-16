<template>
    <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4"
        v-for="(category, categoryIndex) in categories" :key="categoryIndex">
        <!-- Category Title -->
        <div class="d-flex align-items-center gap-3 mb-3"
             @click="toggle(categoryIndex)"
             style="cursor: pointer;">
            <h6>
                <Link :href="category.url" @click.stop style="color: #ef4137;">{{ category.name }}</Link>
            </h6>
            <i :class="expanded[categoryIndex] ? 'fa fa-angle-down' : 'fa fa-angle-right'" aria-hidden="true"></i>
        </div>

        <!-- Only render subcategories when expanded -->
        <template v-if="expanded[categoryIndex]">
            <div v-for="(subCategory, subIndex) in category.items" :key="subIndex" class="ps-4 w-100 mb-3">
                <Link :href="subCategory.url">
                    <p class="category-title" style="font-size: 15px; font-weight: 600; color: #212529;">
                        {{ subCategory.name }}
                    </p>
                </Link>

                <div class="sub-cat-head" v-if="subCategory.items && subCategory.items.length">
                    <ul class="categories" style="margin-left: 2rem;">
                        <li v-for="(item, index) in subCategory.items"
                            :key="index"
                            :style="getItemStyle(index, subCategory.items.length)">
                            <Link v-if="item.name" :href="item.url">{{ item.name }}</Link>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
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

const expanded = ref({});

const toggle = (index) => {
    expanded.value[index] = !expanded.value[index];
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
</style>
