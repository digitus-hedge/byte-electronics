<template>
    <div class="card-footer p-1">
      <Link
        :href="'/categories/list'"
        class="btn"
        :style="{ ...defaultButtonStyle, ...buttonStyle }"
      >
        {{ buttonText }}
      </Link>
    </div>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';

  export default {
    components: { Link },
    props: {
      buttonText: {
        type: String,
        default: "View All Products",
      },
      buttonStyle: {
        type: Object,
        default: () => ({}),
      },
    },
    computed: {
      defaultButtonStyle() {
        return {
          color: "#fff",
          border: "none",
          cursor: "pointer",
        };
      },
    },
  };
  </script>

  <style scoped>
  a.btn:hover {
    opacity: 0.8;
  }
  </style>
