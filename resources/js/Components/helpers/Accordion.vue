<template>
    <div class="card mb-1" style="border: none;">
        <!-- Accordion Header -->
        <div class="card-header" :id="'heading' + index" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index"
            aria-expanded="true" :aria-controls="'collapse' + index"
            style="background: #373737;">
            <h5 class="mb-0 toggle">
                <button class="btn btn-link text-decoration-none" type="button" @click="toggleCollapse"
                    style="color: #fff;">
                    {{ title }}
                </button>
                <i :id="'accordionIcon' + index" :class="iconClass" style="color: #fff;"></i>
            </h5>
        </div>
        <!-- Similar Products Count -->
        <div v-if="title === 'Specifications' && similarProductsCount !== null" class="p-3">
            <p class="mb-0" style="font-size: 0.9rem; color: #333;">
                Similar products with selected attributes:
                <Link :href="filterUrl" class="text-primary text-decoration-none">
                <strong>{{ similarProductsCount }}</strong>
                </Link>
            </p>
        </div>
        <!-- Accordion Content -->
        <div :id="'collapse' + index" class="collapse" :class="{ show: isOpen }" :aria-labelledby="'heading' + index"
           style="overflow-y: auto;">
            <div class="table-responsive">
                <!-- Filters Section -->
                <div v-if="filterSections && filterSections.length">
                    <!-- Search Bar -->
                    <div class="search-container p-3">
                        <input v-model="searchQuery" type="text" class="form-control" placeholder="Search filters..."
                            @input="filterItems" />
                    </div>

                    <!-- Filter List -->
                    <ul class="m-0 filter-list-container"
                        style="border-top: 1px solid #E2E2E2; padding: 15px; list-style: none;">
                        <li v-for="(item, i) in filteredItems" :key="i" style="margin-bottom: 10px;">
                            <label>
                                <input type="checkbox" :value="item.id" v-model="selectedFilters"
                                    @change="submitSelectedFilter" />
                                <!-- <Link :href="`/manufacturer/${item.name}`" style="margin-left: 8px;">

                                </Link> -->
                                {{ item.name }}
                            </label>
                        </li>
                    </ul>
                </div>

                <!-- More Information Section -->
                <div v-else-if="title === 'More Information'" class="more-info-container">
                    <div class="row">
                        <div v-for="(row, i) in attributeSets" :key="i" class="panel-body">
                        <div class="row" id="detail-feature">
                        <div class="col-sm-3 text-center" id="detail-feature-img">
                            <img
                            :src="row.image"
                            :alt="row.title"
                            class="supplier-logo more-info-image"
                            >
                        </div>
                        <div class="col-sm-9" id="detail-feature-desc">
                            <h5>{{ row.title }}</h5>
                            <p>{{ row.description }}</p>
                        </div>
                        <div class="clear"></div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Attributes Table -->
                <table v-else class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th v-if="title === 'Specifications'" scope="col" :class="title === 'Specifications' ? 'w-30' : 'w-50'">Product Attribute</th>
                            <th v-if="title === 'Specifications'" scope="col" :class="title === 'Specifications' ? 'w-50' : 'w-50'">Attribute Value</th>
                            <th v-if="title === 'Specifications'" scope="col" class="w-20 text-center">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(attribute, i) in attributes" :key="i">
                            <td class="fw-semibold">{{ attribute.name }}</td>
                            <td>
                                <template v-if="attribute.value_type === 'text'">
                                    <div class="text-wrap">{{ attribute.value }}</div>
                                </template>
                                <template
                                    v-else-if="attribute.value_type === 'document' || attribute.value_type === 'Document'">
                                    <a :href="attribute.value" target="_blank"
                                        class="text-decoration-none text-primary">
                                        {{ attribute.name }}_{{ attribute.part_no }}
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </template>
                                <template v-else-if="attribute.value_type === 'image'">
                                    <div class="d-flex justify-content-start">
                                        <img :src="attribute.value" :alt="`${attribute.name} image`"
                                            class="thumbnail img-thumbnail" @click="openImageModal(attribute.value)">
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="text-wrap">{{ attribute.value }}</div>
                                </template>
                            </td>
                            <td v-if="title === 'Specifications'" class="text-center">
                                <input type="checkbox" v-model="attribute.selected" class="form-check-input m-0"
                                    @change="title === 'Specifications' && productId && categoryId ? updateSimilarProductsCount() : null">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Similar Products Count -->
                <div v-if="title === 'Specifications' && similarProductsCount !== null" class="p-3">
                    <p class="mb-0" style="font-size: 0.9rem; color: #333;">
                        Similar products with selected attributes:
                        <Link :href="filterUrl" class="text-primary text-decoration-none">
                        <strong>{{ similarProductsCount }}</strong>
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  props: {
    attributes: {
      type: Array,
      required: true
    },
    title: String
  },
  computed: {
    attributeSets() {
      const sets = [];
      for (let i = 0; i < this.attributes.length; i += 3) {

        const group = this.attributes.slice(i, i + 3);
        const image = group.find(attr => attr.name === 'image')?.value || '';
        const title = group.find(attr => attr.name === 'title')?.value || '';
        const description = group.find(attr => attr.name === 'description')?.value || '';

        sets.push({ image, title, description });
      }
      return sets;
    }
  }
}
</script>

<script setup>
import axios from 'axios';
import { defineProps, defineEmits, ref, computed, watch } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { Link } from '@inertiajs/vue3';

// Define props
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    attributes: {
        type: Array,
        required: false,
        default: () => []
    },
    index: {
        type: Number,
        required: true
    },
    customStyle: {
        type: Object,
        required: false,
        default: () => ({
            background: '#fff',
            color: '#000000'
        })
    },
    headerStyle: {
        type: Object,
        required: false,
        default: () => ({ color: '#000000' })
    },
    filterSections: {
        type: Array,
        required: false,
        default: () => []
    },
    modelValue: {
        type: Array,
        required: true
    },
    openIndex: {
        type: Number,
        required: false
    },
    buttonColor: {
        type: String,
        default: '#000000'
    },
    buttonbgColor: {
        type: String,
        default: ''
    },
    productId: {
        type: [Number, String, null],
        default: null
    },
    categoryId: {
        type: [Number, String, null],
        default: null
    },
    subCategoryId: {
        type: [Number, String, null],
        default: null
    }
});

// Define emit function for v-model updates and openIndex control
const emit = defineEmits(['update:modelValue', 'update:openIndex', 'filter-change']);

// Search and filtering state
const searchQuery = ref('');
const filteredItems = ref(props.filterSections);

// State for similar products count
const similarProductsCount = ref(null);

// Sync selected filters with v-model
const selectedFilters = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

// Compute the filter URL for the clickable count
const filterUrl = computed(() => {
    if (!similarProductsCount.value || !props.categoryId) {
        return '#';
    }

    const selectedAttributes = props.attributes
        .filter(attr => attr.selected)
        .map(attr => ({
            name: attr.name,
            value: attr.value
        }));

    if (selectedAttributes.length === 0) {
        return '#';
    }

    // Initialize query parameters
    const params = new URLSearchParams();

    // Add productType[0] with categoryId
    params.append('productType[0]', props.categoryId);

    // Add attributeFilters
    selectedAttributes.forEach((attr, index) => {
        const key = `attributeFilters[${attr.name}][${index}]`;
        params.append(key, attr.value); // Do not encode name or value here
    });

    return `/products/filter?${params.toString()}`;
});

const filterItems = () => {
    if (!searchQuery.value) {
        filteredItems.value = props.filterSections;
        return;
    }

    const query = searchQuery.value.toLowerCase();
    filteredItems.value = props.filterSections.filter(item =>
        item.name.toLowerCase().includes(query)
    );
};

watch(() => props.filterSections, (newSections) => {
    filteredItems.value = newSections;
    filterItems();
});

const isOpen = computed(() => props.openIndex === props.index);
const toggleCollapse = () => {
    console.log(`Toggling accordion ${props.index}. Current openIndex: ${props.openIndex}, isOpen: ${isOpen.value}`);
    const newIndex = isOpen.value ? null : props.index;
    emit('update:openIndex', newIndex);
    console.log(`Emitted update:openIndex with newIndex: ${newIndex}`);
};

// Compute icon class dynamically
const iconClass = computed(() => ({
    'bi': true,
    'bi-chevron-up': isOpen.value,
    'bi-chevron-down': !isOpen.value
}));

// Emit event for filtering when selection changes
const submitSelectedFilter = () => {
    const data = {
        section: props.title,
        selectedItems: selectedFilters.value
    };
    console.log('Emitting filter-change with data:', data);
    emit('filter-change', data);
};

// Fetch similar products count based on selected attributes
const updateSimilarProductsCount = async () => {
    if (props.title !== 'Specifications' || !props.productId || !props.categoryId) {
        similarProductsCount.value = null;
        return;
    }

    // Get selected attributes
    const selectedAttributes = props.attributes
        .filter(attr => attr.selected)
        .map(attr => ({
            name: attr.name,
            value: attr.value
        }));

    console.log('Selected attributes:', selectedAttributes);

    if (selectedAttributes.length === 0) {
        similarProductsCount.value = null;
        return;
    }

    // Get CSRF token safely
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenElement) {
        console.error('CSRF token meta tag not found in the DOM');
        similarProductsCount.value = null;
        toast.error('Unable to fetch similar products count due to missing CSRF token.', {
            position: 'top-right',
            autoClose: 3000,
        });
        return;
    }

    try {
        const response = await axios.post('/products/similar-count', {
            attributes: selectedAttributes,
            product_id: props.productId,
            category_id: props.categoryId,
            sub_category_id: props.subCategoryId,
        }, {
            headers: {
                'X-CSRF-TOKEN': csrfTokenElement.content
            }
        });

        console.log('Similar products count response:', response.data);

        if (response.data.success) {
            similarProductsCount.value = response.data.count;
        } else {
            similarProductsCount.value = null;
            toast.error(response.data.message || 'Failed to fetch similar products count.', {
                position: 'top-right',
                autoClose: 3000,
            });
        }
    } catch (error) {
        console.error('Error fetching similar products count:', error);
        similarProductsCount.value = null;
        toast.error('An error occurred while fetching similar products count.', {
            position: 'top-right',
            autoClose: 3000,
        });
    }
};
</script>
<style scoped>
.more-info-container img {
  border: 0;
}
.more-info-container h5 {
    font-size: 15px;
    margin-top: 0;
    font-weight: bold;

}
.more-info-container p {
    margin: 0 0 9px;
    font-size: 13px;
}

</style>
<style scoped>
/* New styles for search and scroll */
.search-container {
    background: #fff;
    border-bottom: 1px solid #E2E2E2;
}

.search-container .form-control {
    border-radius: 6px;
    border: 1px solid #E2E2E2;
    padding: 8px 12px;
    font-size: 0.9rem;
}

.search-container .form-control:focus {
    border-color: #EF4137;
    box-shadow: 0 0 0 0.2rem rgba(239, 65, 55, 0.25);
}

.filter-list-container {
    min-height: 260px;
    /* Height for ~10 items (each ~26px) */
    max-height: 400px;
    /* Max height to trigger scroll for more items */
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #EF4137 #f1f1f1;
}

.filter-list-container::-webkit-scrollbar {
    width: 8px;
}

.filter-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.filter-list-container::-webkit-scrollbar-thumb {
    background: #EF4137;
    border-radius: 4px;
}

.filter-list-container::-webkit-scrollbar-thumb:hover {
    background: #d13830;
}

/* Existing styles preserved exactly as provided */
.card {
    border-radius: 10px !important;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 1rem !important;
}

.card-header {
    background-color: #fff;
    border-radius: 10px 10px 0 0 !important;
    padding: 12px 20px !important;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none !important;
    color: #000000;
}

.toggle {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.toggle button {
    font-weight: 500;
    font-size: 1rem;
    text-align: left;
    padding: 0;
    margin: 0;
    border: none;
    background: none;
    flex-grow: 1;
}

.toggle i {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.collapse {
    border-radius: 0 0 10px 10px;
}

.table-responsive {
    border-radius: 0 0 10px 10px;
    overflow: hidden;
}

.filter-section {
    padding: 15px;
    border: 1px solid #e2e2e2;
    border-radius: 10px;
    margin: 15px;
    background-color: #fff;
}

.filter-section ul {
    padding: 0;
    margin: 0;
    list-style: none;
}

.filter-section li {
    margin-bottom: 12px;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.filter-section li:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

.filter-section label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.95rem;
    color: #333;
}

.filter-section input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    cursor: pointer;
    accent-color: #EF4137;
}

.table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
}

.table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
    border-top: 1px solid #e9ecef;
}

.table-bordered {
    border: 1px solid #e9ecef;
}

.thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.more-info-container {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    background-color: #fff;
    border-radius: 0 0 10px 10px;
}

.more-info-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 20px;
    border: 1px solid #e0e0e0;
}

.more-info-content h4 {
    margin: 0 0 10px 0;
    font-size: 1.2rem;
    color: #333;
    font-weight: 600;
}

.more-info-content p {
    margin: 0;
    font-size: 0.95rem;
    color: #555;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .more-info-container {
        flex-direction: column;
    }

    .more-info-image {
        margin-right: 0;
        margin-bottom: 15px;
        width: 100%;
        height: auto;
        max-height: 200px;
    }

    .table thead th,
    .table tbody td {
        padding: 8px 10px;
        font-size: 0.9rem;
    }

    .card-header {
        padding: 10px 15px !important;
    }

    .toggle button {
        font-size: 0.95rem;
    }
}

.table {
    font-size: 0.875rem;
    margin-bottom: 0;
}

.w-30 {
    width: 30%;
}

.w-50 {
    width: 50%;
}

.w-20 {
    width: 20%;
}

td,
th {
    padding: 0.75rem 1rem;
    vertical-align: middle;
}

.text-wrap {
    word-break: break-word;
    white-space: normal;
}

.thumbnail {
    max-width: 100px;
    max-height: 100px;
    cursor: pointer;
    transition: transform 0.2s;
}

.thumbnail:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }

    .table {
        font-size: 0.8125rem;
    }

    td,
    th {
        padding: 0.5rem;
    }

    .thumbnail {
        max-width: 80px;
        max-height: 80px;
    }
}

tbody tr:nth-child(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.form-check-input {
    width: 1.1em;
    height: 1.1em;
    outline: none;
    box-shadow: none;
    border-color: inherit;
}
</style>
