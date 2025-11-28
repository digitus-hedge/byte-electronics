<script setup>
import { ref, computed } from "vue";
import { Link } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryTree from "@/Components/helpers/CategoryTree.vue";

const searchQuery = ref("");
const selectedFilter = ref("");
const checkboxes = ref([
    { id: "new-products", label: "New Products", checked: false },
    { id: "active", label: "Active", checked: false }
]);

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    brands: {
        type: Array,
        required: true
    },
});

const hasMatchingDescendants = (category) => {
    if (matchesFilters(category)) return true;
    if (category.items && category.items.length > 0) {
        return category.items.some(item => hasMatchingDescendants(item));
    }
    return false;
};

const filterSubcategories = (categories) => {
    return categories
        .filter(category => hasMatchingDescendants(category))
        .map(category => {
            const filteredCategory = { ...category };
            if (category.items && category.items.length > 0) {
                filteredCategory.items = filterSubcategories(category.items);
            }
            return filteredCategory;
        });
};

const filteredCategories = computed(() => {
    if (!searchQuery.value && !selectedFilter.value && !checkboxes.value.some(c => c.checked)) {
        return props.categories;
    }
    return filterSubcategories(props.categories);
});

const matchesFilters = (item) => {
    const matchesSearch = searchQuery.value
        ? item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        : true;

    const matchesCheckboxes = checkboxes.value.every(checkbox => {
        if (!checkbox.checked) return true;
        switch (checkbox.id) {
            case 'new-products':
                return item.newProducts;
            case 'active':
                return item.active;
            default:
                return true;
        }
    });

    const matchesSelectedFilter = !selectedFilter.value ? true :
        selectedFilter.value === 'new-products' ? item.newProducts :
        selectedFilter.value === 'active' ? item.active :
        selectedFilter.value.startsWith('brand-') ?
            Array.isArray(item.brand_ids) && item.brand_ids.includes(parseInt(selectedFilter.value.replace('brand-', ''))) :
        true;

    return matchesSearch && matchesCheckboxes && matchesSelectedFilter;
};

const applyFilters = () => {
    console.log("Filters Applied:", {
        searchQuery: searchQuery.value,
        selectedFilter: selectedFilter.value,
        activeFilters: checkboxes.value.filter(c => c.checked).map(c => c.id)
    });
};
</script>

<template>
    <AppLayout>
        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <div class="border w-100 custom-border-color rounded-3 p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="col-12 col-md-5 mb-3 mb-md-0">
                        <label class="mb-2" for="search-input">Search within results</label>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control combined-search-input border"
                                id="search-input"
                                v-model="searchQuery"
                                placeholder="Search here..."
                                @keyup.enter=""
                            />
                            <button class="btn custom-bg-color" @click="applyFilters">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="mb-2" for="select-filter">Filter by Manufacturers</label>
                        <div class="d-flex">
                            <select
                                v-model="selectedFilter"
                                id="select-filter"
                                class="form-control box-shadow-0"
                                @change=""
                                style="font-size: 14px;color:#000000;"
                            >
                                <option value="" style="font-size: 14px;">Select Filter</option>
                                <option value="new-products">New Products</option>
                                <option value="active">Active</option>
                                <option v-for="brand in props.brands" :key="brand.id" :value="'brand-' + brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                            <button class="btn ml-3 custom-filter-btn-color fw-light" @click="applyFilters">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 pt-3">
                    <div class="row">
                        <div
                            v-for="(checkbox, index) in checkboxes"
                            :key="index"
                            class="col-12 col-md-4 d-flex align-items-center mb-2 mb-md-0"
                        >
                            <div class="form-check text-center">
                                <input
                                    type="checkbox"
                                    class="form-check-input checkbox-lg border border-secondary box-shadow-0"
                                    :id="checkbox.id"
                                    v-model="checkbox.checked"
                                    @change="applyFilters"
                                >
                                <label
                                    class="form-check-label custom-span-style small-text"
                                    :for="checkbox.id"
                                >
                                    {{ checkbox.label }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <CategoryTree :categories="filteredCategories" />
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-bg-color {
    background-color: #ef4137;
    color: white;
}
select#select-filter:focus {
    outline: none;
    box-shadow: none;
    border-color: #ccc;
}
.custom-filter-btn-color {
    background: linear-gradient(#EF4137, #892520);
    color: white;
    margin-left: 10px;
    width: 100%;
    max-width: 120px;
}
.custom-border-color {
    border-color: #CACACA;
}
label {
    font-size: 14px;
    font-weight: 500;
}
.custom-span-style {
    font-size: 14px;
    font-weight: 400;
    color: #000000;
}
.form-check {
    display: flex;
    align-items: center;
    text-align: center;
    gap: 15px;
}
.form-check-input {
    margin-bottom: 5px;
    transform: scale(1.2);
}
@media (max-width: 767px) {
    .custom-filter-btn-color {
        width: 100%;
    }
    .form-check {
        align-items: flex-start;
    }
    .col-12 {
        padding-right: 0;
        padding-left: 0;
    }
    .form-check-label {
        margin-top: 5px;
    }
}
.form-control:focus, .form-check-input:focus {
    box-shadow: none !important;
    border-color: #CACACA !important;
}
</style>