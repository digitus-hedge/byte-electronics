<template>
    <div class="table-container">
        <table class="product-table">
            <thead>
                <tr>
                    <th v-for="filter in sortedProductPageFilter" :key="filter.head">{{ filter.head }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td v-for="filter in sortedProductPageFilter" :key="filter.head" class="tableTd">
                        <div class="custom-select">
                            <label v-for="option in getDistinctOptions(filter.data)" :key="option.name">
                                <input type="checkbox" :value="option.name" v-model="pendingFilters[filter.head]"
                                    @change="logCheckboxChange(filter.head, option.name, pendingFilters[filter.head])">
                                {{ option.name }}
                            </label>
                        </div>
                        <button class="td-reset-button" @click="resetColumn(filter.head)">Reset</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="filter-actions">
        <button class="btn-filter btn-reset" @click="resetAllFilters">Reset All</button>
        <button class="btn-filter btn-apply" @click="applyFilters">Apply Filters</button>
    </div>
    <div class="selected-filters-display">
        <div v-for="(values, key) in selectedFilters" :key="key">
            <p
                v-if="values && values.length > 0 && key !== 'active' && key !== 'rohsCompliant' && key !== 'newProducts'">
                {{ key }}: {{ Array.isArray(values) ? values.join(', ') : values }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
    productPageFilter: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});


const emit = defineEmits(['update:filters']);


const selectedFilters = ref(
    Object.fromEntries(
        props.productPageFilter
            .filter(f => !['Manufacturer', 'Product Type', 'Sub Product Type'].includes(f.head))
            .map(f => [f.head, props.filters.attributeFilters?.[f.head] || []])
    )
);

// Initialize flags
selectedFilters.value.active = props.filters.active ?? false;
selectedFilters.value.rohsCompliant = props.filters.rohsCompliant ?? false;
selectedFilters.value.newProducts = props.filters.newProducts ?? false;

// Initialize pendingFilters (temporary selections)
const pendingFilters = ref(
    Object.fromEntries(
        Object.entries(selectedFilters.value).map(([key, value]) => [
            key,
            Array.isArray(value) ? [...value] : value,
        ])
    )
);

console.log('AttributeFilter.vue initial state:', {
    selectedFilters: selectedFilters.value,
    pendingFilters: pendingFilters.value,
});

// Ensure distinct options by name (case-sensitive, first occurrence)
const getDistinctOptions = (options) => {
    const seenNames = new Set();
    const filteredOptions = (options || []).filter(option => {
        if (!option || !option.name) {
            console.warn('Invalid option:', option);
            return false;
        }
        if (seenNames.has(option.name)) {
            console.warn(`Duplicate option for name: ${option.name}`, option);
            return false;
        }
        seenNames.add(option.name);
        return true;
    });
    return filteredOptions;
};

// Debug Mounting Style, Package / Case, and Product options
const logAttributeOptions = () => {
    ['Mounting Style', 'Package / Case', 'Product'].forEach(head => {
        const filter = props.productPageFilter.find(f => f.head === head);
        if (filter) {
            console.log(`${head} options:`, filter.data);
            console.log(`Distinct ${head} options:`, getDistinctOptions(filter.data));
        }
    });
};

// Call logAttributeOptions after component is mounted
onMounted(() => {
    logAttributeOptions();
});

// Log checkbox changes for debugging
const logCheckboxChange = (head, name, currentState) => {
    console.log(`Checkbox change for ${head}:`, { name, currentState });
};

// Filter out accordion filters
const filteredProductPageFilter = computed(() => {
    const filtered = props.productPageFilter.filter(filter => {
        if (['Manufacturer', 'Product Type', 'Sub Product Type'].includes(filter.head)) {
            console.log(`Excluding accordion filter: ${filter.head}`);
            return false;
        }
        if (!filter.data || !Array.isArray(filter.data) || filter.data.length === 0) {
            console.log(`Skipping filter ${filter.head} due to empty or invalid data`);
            return false;
        }
        console.log(`Including attribute filter: ${filter.head}`);
        return true;
    });
    console.log('filteredProductPageFilter:', filtered);
    return filtered;
});

// Sort productPageFilter based on selectedFilters
const sortedProductPageFilter = computed(() => {
    const sorted = [...filteredProductPageFilter.value].sort((a, b) => {
        const aSelected = selectedFilters.value[a.head]?.length > 0;
        const bSelected = selectedFilters.value[b.head]?.length > 0;
        if (aSelected && !bSelected) return -1;
        if (!aSelected && bSelected) return 1;
        return a.head.localeCompare(b.head);
    });
    console.log('sortedProductPageFilter:', sorted);
    return sorted;
});

const resetColumn = (column) => {
    if (!['Manufacturer', 'Product Type', 'Sub Product Type'].includes(column)) {
        pendingFilters.value[column] = [];
        selectedFilters.value[column] = [];
        console.log(`Reset ${column}:`, selectedFilters.value);
        emitFilters();
    }
};

const resetAllFilters = () => {
    Object.keys(pendingFilters.value).forEach(key => {
        if (!['active', 'rohsCompliant', 'newProducts'].includes(key)) {
            pendingFilters.value[key] = [];
            selectedFilters.value[key] = [];
        }
    });
    console.log('Reset All Attribute Filters:', selectedFilters.value);
    emitFilters();
};

const applyFilters = () => {
    console.log('Applying Attribute Filters:', pendingFilters.value);
    Object.assign(selectedFilters.value, pendingFilters.value);
    emitFilters();
};

const emitFilters = () => {
    const filtersToEmit = {
        attributeFilters: {},
    };
    Object.entries(selectedFilters.value).forEach(([key, values]) => {
        if (['active', 'rohsCompliant', 'newProducts'].includes(key)) {
            filtersToEmit[key] = values ?? false;
        } else if (Array.isArray(values) && values.length > 0) {
            filtersToEmit.attributeFilters[key] = [...values]; // Ensure array
        }
    });
    console.log('Emitting Filters:', filtersToEmit);
    emit('update:filters', filtersToEmit);
};

// Sync with props.filters
watch(
    () => props.filters,
    (newFilters) => {
        console.log('AttributeFilter.vue watch newFilters:', newFilters);
        // Initialize all possible filter heads
        props.productPageFilter
            .filter(f => !['Manufacturer', 'Product Type', 'Sub Product Type'].includes(f.head))
            .forEach(f => {
                if (!(f.head in selectedFilters.value)) {
                    selectedFilters.value[f.head] = [];
                    pendingFilters.value[f.head] = [];
                }
            });
        // Update attribute filters
        Object.entries(newFilters.attributeFilters || {}).forEach(([key, values]) => {
            let normalizedValues = [];
            if (Array.isArray(values)) {
                normalizedValues = [...values];
            } else if (values && typeof values === 'object') {
                normalizedValues = Object.values(values).filter(val => typeof val === 'string');
            } else {
                normalizedValues = [values].filter(val => typeof val === 'string');
            }
            console.log(`Processing ${key} values:`, { raw: values, normalized: normalizedValues });
            const filter = props.productPageFilter.find(f => f.head === key);
            if (filter) {
                const validValues = normalizedValues.filter(val =>
                    filter.data.some(opt => opt.name === val)
                );
                selectedFilters.value[key] = validValues;
                pendingFilters.value[key] = validValues;
                console.log(`Synced ${key}:`, validValues);
            } else {
                console.warn(`Filter head ${key} not found in productPageFilter`);
                selectedFilters.value[key] = [];
                pendingFilters.value[key] = [];
            }
        });
        // Update flags
        selectedFilters.value.active = newFilters.active ?? selectedFilters.value.active;
        selectedFilters.value.rohsCompliant = newFilters.rohsCompliant ?? selectedFilters.value.rohsCompliant;
        selectedFilters.value.newProducts = newFilters.newProducts ?? newFilters.newProducts;
        pendingFilters.value.active = newFilters.active ?? pendingFilters.value.active;
        pendingFilters.value.rohsCompliant = newFilters.rohsCompliant ?? pendingFilters.value.rohsCompliant;
        pendingFilters.value.newProducts = newFilters.newProducts ?? pendingFilters.value.newProducts;
        console.log('AttributeFilter.vue synced state:', {
            selectedFilters: selectedFilters.value,
            pendingFilters: pendingFilters.value,
        });
    },
    { immediate: true, deep: true }
);
</script>

<style scoped>
.product-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-family: sans-serif;
    font-size: 14px;
    color: #000000;
}

.product-table th {
    background-color: #f2f2f2;
    text-align: left;
    padding: 12px;
    font-weight: bold;
    border: 1px solid #ddd;
    position: sticky;
    top: 0;
    height: 50px;
    box-sizing: border-box;
    white-space: nowrap;
    z-index: 10;
}

.product-table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    min-height: 50px;
    box-sizing: border-box;
    vertical-align: top;
}

.table-container {
    max-height: 600px;
    overflow: auto;
    border: 1px solid #ddd;
    width: 100%;
}

.td-reset-button {
    margin-top: 8px;
    padding: 4px 8px;
    font-size: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f2f2f2;
    cursor: pointer;
    display: block;
    width: 100%;
    text-align: center;
}

.td-reset-button:hover {
    background-color: #e0e0e0;
}

.table-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.custom-select {
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    max-height: 200px;
    min-width: 200px;
    overflow-y: auto;
    padding: 4px;
    margin-bottom: 21px;
}

.custom-select label {
    display: block;
    padding: 0px 1px;
    cursor: pointer;
}

.custom-select label:hover {
    background-color: #f0f0f0;
}

.custom-select input[type="checkbox"] {
    margin-right: 8px;
}

.custom-select input[type="checkbox"]:checked {
    accent-color: #EF4137;
}

.custom-select input[type="checkbox"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.filter-actions {
    display: flex;
    gap: 12px;
    margin: 16px 0;
    justify-content: flex-end;
}

.btn-filter {
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-reset {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    color: #333;
}

.btn-reset:hover {
    background-color: #e9ecef;
    border-color: #ccc;
}

.btn-apply {
    background-color: #EF4137;
    border: 1px solid #d83529;
    color: white;
}

.btn-apply:hover {
    background-color: #d83529;
    border-color: #c12e24;
}

@media (max-width: 768px) {

    .product-table th,
    .product-table td {
        padding: 8px;
        font-size: 13px;
    }

    .custom-select {
        min-width: 150px;
    }

    .td-reset-button {
        font-size: 11px;
        padding: 3px 6px;
    }

    .btn-filter {
        padding: 6px 12px;
        font-size: 13px;
    }
}

.tableTd {
    position: relative;
}

.td-reset-button {
    position: absolute;
    bottom: 0;
    right: 0;
    margin: 0;
}

.selected-filters-display {
    flex-grow: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.selected-filters-display p {
    background-color: #f0f0f0;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 0;
    font-size: 14px;
}
</style>