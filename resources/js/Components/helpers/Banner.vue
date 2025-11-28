<script setup>
import { defineProps, defineEmits, ref } from "vue";

const emit = defineEmits(['search']);
const searchQuery = ref('');

defineProps({
    image: {
        type: String,
        required: false,
        default: '',
    },
    text: {
        type: String,
        required: false,
        default: '',
    },
    search: {
        type: Boolean,
        required: false,
        default: true,
    },
    height: {
        type: String,
        required: false,
        default: 'auto',
    },
    titleSize: {
        type: String,
        required: false,
        default: '2rem',
    },
    searchWidth: {
        type: String,
        required: false,
        default: '100%',
    },
    placeholderText: {
        type: String,
        required: false,
        default: 'Search...',
    },
    textColor: {
        type: String,
        required: false,
        default: 'white',
    },
    align: {
        type: String,
        required: false,
        default: 'center',
        validator: (value) => ['center', 'right'].includes(value)
    }
});

const handleSearch = () => {
    emit('search', searchQuery.value);
};
</script>

<template>
    <div class="image-container" :style="{ height: height }">
        <img v-if="image" :src="image" alt="Banner Image" class="banner-image" />
        <div class="content" :class="align">
            <h4 v-if="text" class="mb-2 fw-bold" :style="{ fontSize: titleSize, color: textColor }">{{ text }}</h4>
            <div v-if="search" class="search-container" :style="{ width: searchWidth }">
                <input type="text" name="search" id="search" :placeholder="placeholderText" v-model="searchQuery"
                    @keyup.enter="handleSearch" />
                <i class="fas fa-search search-icon" style="cursor: pointer;" @click="handleSearch"></i>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
.image-container {
    position: relative;
    width: 100%;
    text-align: center;
    color: white;
    overflow: hidden;
}

.banner-image {
    display: block; /* Ensure image displays by default */
    width: 100%;
    height: 100%;
    object-fit: cover; /* Maintain aspect ratio without distortion */
    object-position: center;
}

.content {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    padding: 10px;
    border-radius: 8px;
}

/* Center alignment (default) */
.content.center {
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

/* Right alignment */
.content.right {
    right: 0;
    text-align: right;
    padding-right: 20px;
}

/* Search container */
.search-container {
    position: relative;
    margin-top: 10px;
    width: 100%;
    max-width: 900px;
    min-width: 350px;
}

input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #111;
    font-size: 22px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .content h4 {
        font-size: 1.5rem;
    }

    .search-container {
        max-width: 90%;
    }

    input {
        font-size: 14px;
        padding: 8px 30px 8px 12px;
    }

    .search-icon {
        font-size: 20px;
    }

    .banner-image {
        max-height: 400px;
        height: 250px;
        object-fit: cover;
    }
}

@media (max-width: 480px) {
    .content h4 {
        font-size: 1.2rem;
    }

    .search-container {
        max-width: 100%;
        min-width: 200px !important;
    }

    input {
        font-size: 12px;
        padding: 6px 25px 6px 10px;
    }

    .search-icon {
        font-size: 18px;
    }

    .banner-image {
        max-height: 400px;
        height: 100px;
        object-fit: cover;
    }
}
</style>
