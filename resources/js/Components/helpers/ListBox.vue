<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps, defineEmits, ref } from 'vue';

const props = defineProps({
    title: String,
    categories: Array,
    isBlog: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['select-category']);

// Reactive state for toggling list expansion and selected category
const isExpanded = ref(false);
const selectedCategory = ref('');

// Handle category selection
const selectCategory = (categoryName) => {
    selectedCategory.value = categoryName;
    emit('select-category', categoryName);
};

// Debug categories prop
console.log('ListBox categories:', props.categories);
</script>

<template>
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center gap-2" style="background: #373737; padding: 5px 20px;">
            <i class="bi bi-list" style="color: #fff; font-size: 24px; transform: scaleX(1.5);"></i>
            <h5 class="mb-0 text-white" style="font-size: 14px;">
                {{ title }}
            </h5>
        </div>

        <div class="card-body p-1" :style="{ maxHeight: isExpanded ? 'none' : '200px', overflowY: isExpanded ? 'auto' : 'hidden' }">
            <ul class="list-unstyled text-white">
                <!-- All Categories option -->
                <li class="border-bottom pb-2 mb-2 position-relative"
                    style="padding-left: 20px; color:#000000;font-size: 14px;padding-top: 5px !important;padding-bottom: 5px !important;"
                    :class="{ 'selected': selectedCategory === '' }">
                    <template v-if="isBlog">
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center"
                           @click.prevent="selectCategory('')">
                            All Categories
                        </a>
                    </template>
                    <template v-else>
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                            All Categories
                        </a>
                    </template>
                    <i class="bi bi-chevron-right"
                       style="font-size: 14px; position: absolute; right: 0; top: 50%; transform: translateY(-50%); color:#000000;"></i>
                </li>
                <!-- Category list -->
                <li v-for="(category, index) in categories" :key="index"
                    class="border-bottom p-2 position-relative"
                    style="padding-left: 20px; color:#000000;font-size: 14px;"
                    :class="{ 'selected': selectedCategory === category.name }">
                    <template v-if="isBlog">
                        <a href="#" class="text-decoration-none text-dark d-flex align-items-center"
                           @click.prevent="selectCategory(category.name)">
                            {{ category.name }}
                        </a>
                    </template>
                    <template v-else>
                    <!-- <pre>{{ categories }}</pre> -->
                        <a :href="`/categories/details/${category.slug}`" class="text-decoration-none text-dark d-flex align-items-center">
                            {{ category.name }}
                        </a>
                    </template>
                    <i class="bi bi-chevron-right"
                       style="font-size: 14px; position: absolute; right: 0; top: 50%; transform: translateY(-50%); color:#000000;"></i>
                </li>
            </ul>
        </div>
        <template v-if="isBlog">

        <div class="card-footer p-0 text-center" style="background: #373737;">
            <button :style="{ color: 'white', fontSize: '14px', padding: '5px', background: 'none', border: 'none', cursor: 'pointer' }"
                    @click="isExpanded = !isExpanded; if (!isExpanded) selectCategory('')">
                {{ isExpanded ? 'Collapse' : 'View All' }}
            </button>
        </div>
        </template>
        <template v-else>
            <div class="card-footer p-0 text-center" style="background: #373737;">
            <Link :href="`/categories/list`" :style="{ color: 'white', fontSize: '14px', padding: '5px', background: 'none', border: 'none', cursor: 'pointer' }">
              View All
            </Link>
        </div>
        </template>
    </div>
</template>

<style scoped>
.card-body {
    transition: max-height 0.3s ease;
}

.card-body::-webkit-scrollbar {
    width: 8px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.card-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.selected {
    background-color: #EF4137;
    color: white !important;
}

.selected a {
    color: white !important;
    font-weight: bold;
}

.selected .bi-chevron-right {
    color: white !important;
}
</style>
