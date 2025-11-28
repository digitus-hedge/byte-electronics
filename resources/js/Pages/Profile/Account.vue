<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';

const page = usePage();
const user = page.props.user;
const addresses = ref(page.props.addresses);
</script>

<template>
  <AppLayout>
  <div class="profile-container">
    <div class="profile-content">
      <!-- Sidebar -->
      <aside class="sidebar">
        <h2>MY ACCOUNT</h2>
        <ul>
          <li class="active">Personal Information</li>
          <li>Order Status and History</li>
          <li>BOMs, Carts & Projects</li>
          <li>Forms</li>
          <li>Additional Information</li>
        </ul>
      </aside>

      <!-- Main Content -->
      <main class="profile-main">
        <!-- Contact Information -->
        <section class="profile-card">
          <h3 class="profile-title">
            Contact Information
            <span class="icon">👤</span>
          </h3>
          <div class="profile-details">
            <p><strong>First Name:</strong> {{ user.first_name }}</p>
            <p><strong>Last Name:</strong> {{ user.last_name }}</p>
            <p><strong>Email:</strong> {{ user.email }}</p>
            <p><strong>Country:</strong> {{ user.country }}</p>
          </div>
        </section>

        <!-- Addresses -->
        <section class="profile-card">
          <h3 class="profile-header">Addresses</h3>
          <div class="address-list">
            <div v-for="(address, index) in addresses" :key="index" class="address-card">
              <p class="address-title">{{ address.type }}</p>
            </div>
          </div>
          <button class="btn-add">Add More</button>
        </section>

        <!-- Order History -->
        <section class="profile-card order-history">
          <h3 class="order-title">Order History</h3>
          <p class="order-description">
            View your recent orders, find tracking information, or initiate a return.
          </p>
          <button class="btn-order">Visit order history</button>
        </section>

        <p class="privacy-info">
          Lorem Ipsum is simply dummy text of the printing and typesetting industry.
          <a href="#">Visit the privacy centre to learn more.</a>
        </p>
      </main>
    </div>
  </div>
</AppLayout>
</template>

<style scoped>
.profile-container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 20px;
}
.profile-content {
  display: flex;
  gap: 20px;
}
.sidebar {
  width: 25%;
  background: #f7f7f7;
  padding: 20px;
  border-radius: 8px;
}
.sidebar h2 {
  font-size: 18px;
  font-weight: bold;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin-top: 15px;
}
.sidebar li {
  padding: 10px;
  background: white;
  margin-bottom: 5px;
  border-radius: 5px;
  cursor: pointer;
}
.sidebar .active {
  background: #e60000;
  color: white;
}
.profile-main {
  flex: 1;
}
.profile-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.profile-title {
  display: flex;
  align-items: center;
  font-size: 18px;
  font-weight: bold;
}
.icon {
  margin-left: 8px;
  color: red;
}
.profile-details p {
  margin: 5px 0;
}
.profile-header {
  background: red;
  color: white;
  padding: 10px;
  border-radius: 5px;
}
.address-list {
  margin-top: 10px;
}
.address-card {
  padding: 10px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
}
.btn-add {
  background: red;
  color: white;
  padding: 10px;
  border: none;
  cursor: pointer;
  margin-top: 10px;
}
.order-history {
  text-align: center;
}
.order-title {
  font-size: 18px;
  font-weight: bold;
}
.order-description {
  color: gray;
}
.btn-order {
  background: red;
  color: white;
  padding: 10px;
  border: none;
  cursor: pointer;
  margin-top: 10px;
}
.privacy-info {
  color: gray;
  font-size: 14px;
}
.privacy-info a {
  color: red;
}
</style>
