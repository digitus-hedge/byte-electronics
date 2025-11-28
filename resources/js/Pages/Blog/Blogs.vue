
<template>
    <AppLayout>
        <BreadCrums />

        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <Banner :image="blogBanner" @search="handleSearch" />

            <div class="row full-height mt-4 w-100">
                <div class="col-12 col-md-8 blog-content pb-0">
                    <div class="row ">
                        <div v-for="(blog, index) in filteredBlogs" :key="index" class="col-12 col-sm-6 col-md-6 mb-4 d-flex flex-column">
                            <div class="blog-card">
                                <img :src="`/storage/${blog.featured_image}`" class="img-fluid rounded" alt="Blog Image">
                                <p class="mt-3 text-start">{{ blog.title }}</p>
                                <Link :href="`/blogs/details/${blog.slug}`" class="btn btn-link w-100 text-start p-0">View Blog</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 blog-category">
                    <ListBox :categories="blogCategory" :isBlog="true" title="Categories" @select-category="handleCategorySelect" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { defineProps, ref, computed } from "vue";
import Banner from "@/Components/helpers/Banner.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ListBox from "@/Components/helpers/ListBox.vue";
import BreadCrums from "@/Layouts/BreadCrums.vue";
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    blogs: Array,
    blogBanner: String,
    blogCategory: Array,
});

// Debug blogCategory prop
console.log('blogCategory:', props.blogCategory);

// Reactive state for search query and selected category
const searchQuery = ref('');
const selectedCategory = ref('');

// Computed property to filter blogs based on search query and category
const filteredBlogs = computed(() => {
    let filtered = props.blogs;

    // Filter by search query
    if (searchQuery.value.trim()) {
        filtered = filtered.filter(blog =>
            blog.title.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // Filter by category
    if (selectedCategory.value && typeof selectedCategory.value === 'string') {
        filtered = filtered.filter(blog =>
            blog.categories && typeof blog.categories === 'string' &&
            blog.categories.toLowerCase() === selectedCategory.value.toLowerCase()
        );
    }

    return filtered;
});

// Handle search event from Banner
const handleSearch = (query) => {
    console.log('Search query:', query);
    searchQuery.value = typeof query === 'string' ? query : '';
};

// Handle category selection from ListBox
const handleCategorySelect = (category) => {
    console.log('Selected category:', category);
    selectedCategory.value = typeof category === 'string' ? category : '';
};
</script>

<style scoped>
.row.full-height {
    display: flex;
    min-height: 100vh;
    flex-wrap: wrap;
}

.blog-content, .blog-category {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.blog-card {
    text-align: center;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.blog-card a {
    color: #EF4137;
    font-size: 14px;
}

img {
    max-width: 100%;
    height: auto;
    object-fit: cover;
}

p {
    font-size: 14px;
    color: #000000;
}

.btn-link {
    font-size: 1.2rem;
    color: #0682a3;
    text-decoration: none;
}

.btn-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .row.full-height {
        flex-direction: column;
    }

    .blog-card {
        max-width: 100%;
    }

    .blog-category {
        margin-top: 2rem;
    }
}
</style>
