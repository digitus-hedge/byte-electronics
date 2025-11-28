<script setup>
import { ref } from 'vue';
const props = defineProps({
      addresses: {
        type: Object,
        default: () => ({})
      },
      isEditing: {
        type: Boolean,
        default: false
      }
    });
const isOpen = ref(false);
const showMore = ref(false);

const toggleIcon = () => {
    isOpen.value = !isOpen.value;
};

const toggleAddresses = () => {
    showMore.value = !showMore.value;
};

// const props = defineProps({
//     address: {
//         type: Array,
//         default: () => []
//     }
// });
// const props = defineProps({
//     user: { default: () => ({ id: 1, name: 'Test User', email: 'test@example.com' }) },
//     customerAddresses: { default: () => [{ id: 1, address_line1: '123 St', city: 'Test City' }] },
//     countries: { default: () => [] }
// });

const emit = defineEmits(['show-form', 'edit-address']);

const handleEdit = (address) => {
    emit('edit-address', address);
};

const handleAdd = () => {
    emit('show-form');
};
</script>

<template>
    <!-- <pre>{{ addresses }}</pre> -->
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="btn w-100 d-flex justify-content-between collapsed accordionMain" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                    aria-controls="collapseTwo" @click="toggleIcon" style="background: #EF4137; color: #FFFFFF;">
                    Addresses
                    <i :class="isOpen ? 'bi bi-dash-circ q qle' : 'bi bi-plus-circle'" style="color: #FFFFFF;"></i>
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                data-bs-parent="#accordionExample">
                <div class="accordion-body p-0 border-0">
                    <div class="accordion" id="nestedAccordion">
                        <div v-for="(address, index) in addresses" :key="index" class="accordion-items"
                            :style="{ 'border-bottom': index === addresses.length - 1 ? '0px' : '1px solid #E2E2E2' }">
                            <h2 class="accordion-header" :id="`nestedHeading${index}`">
                                <button class="accordion-button AccBtn collapsed p-2" type="button"
                                    data-bs-toggle="collapse" :data-bs-target="`#nestedCollapse${index}`"
                                    :aria-expanded="index === 0" :aria-controls="`nestedCollapse${index}`"
                                    style="color: #000;">
                                    {{ address.addressName }}
                                </button>
                            </h2>
                            <div :id="`nestedCollapse${index}`" class="accordion-collapse collapse"
                                :class="{ 'show': index === 0 }" :aria-labelledby="`nestedHeading${index}`"
                                :data-bs-parent="`#nestedAccordion`">
                                <div class="accordion-body" style="position: relative;">
                                    <button class="btn" style="position: absolute; top: 0; right: 0;"
                                        @click="handleEdit(address)">
                                        <i class="bi bi-pencil-square" style="color: #EF4137;"></i>
                                    </button>
                                    <table class="accordion-table w-100">
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">User Name</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td style="padding-left: 10px;">{{ address.userName }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Email</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td style="padding-left: 10px;">{{ address.email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Company Name</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td style="padding-left: 10px;">{{ address.company_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Address Line 1</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td style="padding-left: 10px;">{{ address.address_line_1 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Address Line 2</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.address_line_2 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">City</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.city }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">State</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.state }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Country</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.countryName }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Postal Code</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.postal_code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Attention</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.attention }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2" style="white-space: nowrap; padding-right: 20px;">Phone</td>
                                            <td style="padding-left: 10px;">:</td>
                                            <td class="text-wrap" style="padding-left: 10px;">{{ address.phone }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="p-2" style="display: flex; justify-content: end; gap: 5px;">
                            <!-- <button @click="toggleAddresses" class="btn p-1 w-15"
                                style="background: #EF4137; font-size: 12px; color: #fff;">
                                {{ showMore ? 'Show Less' : 'Show More' }}
                            </button> -->
                            <button class="btn p-1 w-15" @click="handleAdd"
                                style="background: #EF4137; font-size: 12px; color: #fff;">Add More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- <style scoped>
/* Existing styles remain unchanged */
</style> -->

<style scoped>
.accordion-button:focus {
    box-shadow: none !important;
    outline: none !important;
}

.accordionMain {
    padding: 10px;
}

.accordion-items {
    border-left: 0px;
    border-bottom: 1px solid #E2E2E2;
}

.AccBtn {
    padding: 10px;
    font-weight: 600;
    font-size: 14px;
    background: #ffffff;
}

.AccBtn:not(.collapsed) {
    border-bottom: 1px solid #E2E2E2;

}

.accordion-table td {
    font-size: 12px;
    color: #000000;
}
</style>
