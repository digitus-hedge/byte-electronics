<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import Banner from "@/Components/helpers/Banner.vue";

defineProps({
    aboutUsBanner: String,
});
</script>

<template>
    <AppLayout>
        <BreadCrums />
        <Banner :image="aboutUsBanner" :search="false" text="About Byte Electronics" />

        <div class="container-fluid container d-flex flex-column align-items-start mt-4 mb-4">
            <h4 class="section-title">Who We Are</h4>
            <p class="section-text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
                scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap
                into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the
                release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing
                software like Aldus PageMaker including versions of Lorem Ipsum.
            </p>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="section-text">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                        the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                        of type and scrambled it to make a type specimen book. It has survived not only five centuries,
                        but also the leap into electronic typesetting, remaining essentially unchanged.
                    </p>
                    <p class="section-text">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                        the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                        of type and scrambled it to make a type specimen book. It has survived not only five centuries,
                        but also the leap into electronic typesetting, remaining essentially unchanged.
                    </p>
                    <p class="section-text">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                        the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                        of type and scrambled it to make a type specimen book. It has survived not only five centuries,
                        but also the leap into electronic typesetting, remaining essentially unchanged.
                    </p>
                </div>

                <div class="col-md-6">
                    <div class="image-container">
                        <img src="/assets/images/aboutimg.png" alt="Byte Electronics" class="about-us-image" />
                    </div>
                </div>
            </div>

            <p class="section-text" style="margin-top: 1rem;">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
                scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap
                into electronic typesetting, remaining essentially unchanged.
            </p>
            <p class="section-text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
                scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap
                into electronic typesetting, remaining essentially unchanged.
            </p>
        </div>
    </AppLayout>
</template>

<style scoped>
.container {
    padding: 0 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.section-title {
    font-size: 2rem;
    font-weight: 800;
    color: #000000;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
}

.section-text {
    font-size: 14px;
    line-height: 1.6;
    color: #000000;
    margin-bottom: 1rem;
}

.image-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.about-us-image {
    width: 100%;
    height: auto;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.about-us-image:hover {
    transform: scale(1.02);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .container {
        padding: 0 1rem;
    }

    .section-title {
        font-size: 1.75rem;
    }

    .section-text {
        font-size: 0.95rem;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 0 0.75rem;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .section-text {
        font-size: 0.9rem;
    }

    .row {
        flex-direction: column-reverse;
    }

    .col-md-6 {
        margin-bottom: 1.5rem;
    }

    .image-container {
        margin-bottom: 1rem;
    }

    .about-us-image {
        max-height: 300px;
        object-fit: fill;
    }
}

@media (max-width: 576px) {
    .section-title {
        font-size: 1.25rem;
    }

    .section-text {
        font-size: 0.85rem;
    }
}
</style>
