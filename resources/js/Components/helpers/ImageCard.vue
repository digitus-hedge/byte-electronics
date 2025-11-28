<!-- <template>
    <div :class="customClass || ['card', 'shadow-sm', 'flex-grow-1']" @click="handleClick" style="cursor: pointer;">
        <div :class="['text-center', innerClass || 'p-4']">
        <img :src="isValidImage(image) ? image : '/assets/images/dummy_product.webp'"
        alt="Product Image" class="img-fluid rounded">
           
        </div>
    </div>
</template>

<script>
export default {
    props: {
        image: {
            type: String,
            required: true,
        },
        customClass: {
            type: String,
            default: '',
        },
        innerClass: {
            type: String,
            default: '',
        },
    },
    emits: ['select'],
    methods: {
        handleClick() {
            this.$emit('select', this.image); // Emit the selected image to the parent
        },
        isValidImage(image) {
        if (!image) return false;
        const validExtensions = ['.png', '.jpg', '.jpeg', '.webp'];
        return validExtensions.some(ext => image.toLowerCase().endsWith(ext));
    }
    },
};
</script> -->

<template>
    <div :class="customClass || ['card', 'shadow-sm', 'flex-grow-1']" @click="handleClick" style="cursor: pointer;">
      <div :class="['text-center', innerClass || 'p-4']">
        <img
          :src="currentImage"
          @error="handleImageError"
          alt="Product Image"
          class="img-fluid rounded"
        >
      </div>
    </div>
  </template>
<script>
export default {
  props: {
    image: {
      type: String,
      required: true,
    },
    customClass: {
      type: String,
      default: '',
    },
    innerClass: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      currentImage: this.image,
      fallbackImage: '/assets/images/dummy_product.webp',
    };
  },
  emits: ['select'],
  methods: {
    handleClick() {
      this.$emit('select', this.image);
    },
    isValidImage(image) {
      if (!image) return false;
      const validExtensions = ['.png', '.jpg', '.jpeg', '.webp'];
      return validExtensions.some(ext => image.toLowerCase().endsWith(ext));
    },
    handleImageError() {
      this.currentImage = this.fallbackImage;
    },
  },
};
</script>

  