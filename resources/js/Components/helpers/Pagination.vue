<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalItems: {
    type: Number,
    required: true,
  },
  itemsPerPage: {
    type: Number,
    required: true,
  },
});
const emit = defineEmits();

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    emit('update:currentPage', page);
  }
};

const totalPages = computed(() => {
  return Math.ceil(props.totalItems / props.itemsPerPage);
});

const itemRange = computed(() => {
  const start = (props.currentPage - 1) * props.itemsPerPage + 1;
  const end = Math.min(props.currentPage * props.itemsPerPage, props.totalItems);
  return `${start} - ${end} of ${props.totalItems} Products`;
});
</script>

<template>
  <div class="d-flex align-items-center">
    <span>{{ itemRange }}</span>
    <div class="d-flex ms-3">
      <button
        class="btn p-0 border"
        :disabled="props.currentPage <= 1"
        style="border-width: 1px; border-left: none; border-radius: 8px 0px 0px 8px;"
        @click="goToPage(props.currentPage - 1)"
      >
        <i class="bi bi-chevron-left p-2" style="color: #D2D2D2;"></i>
      </button>
      <span class="d-flex align-items-center p-2">{{ props.currentPage }}</span>
      <button
        class="btn p-0 border"
        :disabled="props.currentPage >= totalPages"
        style="border-width: 1px; border-right: none; border-radius: 0px 8px 8px 0px;"
        @click="goToPage(props.currentPage + 1)"
      >
        <i class="bi bi-chevron-right p-2" style="color: #D2D2D2;"></i>
      </button>
    </div>
  </div>
</template>
