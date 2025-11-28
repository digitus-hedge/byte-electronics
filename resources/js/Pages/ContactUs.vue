<template>
    <AppLayout>
      <BreadCrums />
      <Banner :image="ContactUsBanner" :search="false" text="Contact Us" />
      <div class="container container-details">
        <div class="d-flex align-items-center mb-4"></div>
        <div class="line-with-label">
          <span class="label">Get in touch</span>
          <div class="line"></div>
        </div>

        <div class="row justify-content-center p-4">
          <!-- Left Contact Info Section -->
          <div class="col-lg-4 col-md-12 mb-4">
            <div class="contact-info p-4">
              <h3 class="contact-heading mb-4">We'd Love to Hear from You</h3>
              <p class="mb-4 contact-address">We understand that it is important for you access our services in a way time.</p>
              <div class="mb-4">
                <h5><img src="assets/images/contact/uae.webp" class="img-fluid"> United Arab Emirates</h5>
              </div>
              <div class="mb-4">
                <h5 style="font-weight: 700;"> Dubai</h5>
                <p class="contact-address">Byte Electronics, Office 201, 2nd Floor Al Mezan Tower (old FEWA Building), Muhaisnah 4, Dubai Landmarks: Madina Mall</p>
              </div>
              <div class="mb-4">
                <h5 style="font-weight: 700;"> Sharjah</h5>
                <p class="contact-address">Office Number 10, Sharjah, Media City</p>
              </div>
              <div class="mb-4">
                <h5 style="font-weight: 700;"> Abudhabi</h5>
                <p class="contact-address">Office :1.8 Musaffah 45, Abudhabi</p>
              </div>
              <div class="bg-color my-2 center">
                <p style="font-size: .8rem;">We are also present in <span style="font-weight: 700;">India, Taiwan, China, UK and USA</span></p>
              </div>
              <div class="content-with-vertical-line">
                <div class="social-icons">
                  <h5>Social Media</h5>
                  <a href="#" class="me-3"><img src="assets/images/contact/Facebook.webp"></a>
                  <a href="#" class="me-3"><img src="assets/images/contact/Instagram Circle.webp"></a>
                  <a href="#" class="me-3"><img src="assets/images/contact/twitterx.webp"></a>
                </div>
              </div>
            </div>
          </div>

          <!-- Center Logo Section -->
          <div class="col-lg-4 col-md-12 mb-4 d-flex align-items-center justify-content-center">
            <div class="logo-container text-center" style="height: 100%;">
              <img src="assets/images/contact/contact-main-img.webp" alt="Byte Electronics Logo" class="img-fluid" style="height: 100% !important;">
            </div>
          </div>

          <!-- Right Contact Details and Map Section -->
          <div class="col-lg-4 col-md-12 mb-4">
            <div class="contact-details p-4 mb-4">
              <div class="mb-3">
                <h5 style="font-weight: 700;"><img src="assets/images/contact/call-rounded.webp" class="img-fluid" style="margin-right: 10px;">+971 2 445 2300</h5>
              </div>
              <div class="line_1"></div>
              <div class="mb-3" style="padding-top: 1rem;">
                <h5 style="font-weight: 700;"><img src="assets/images/contact/mail-rounded.webp" class="img-fluid" style="margin-right: 10px;">info@byte-electronics.com</h5>
              </div>
            </div>
            <div class="map-container">
              <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d428.625491725297!2d55.400802!3d25.287399!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f5d033bbc8695%3A0xd3c1b5ccc8c9ba4f!2sEpoch%20International!5e1!3m2!1sen!2sin!4v1745930910901!5m2!1sen!2sin" 
                width="400" height="420" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Form Section -->
      <div class="container container-contact"></div>
      <div class="contact-form-section p-5 mt-4" :style="{
        backgroundImage: 'url(/assets/images/contact/contact-form-bg.webp)',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        position: 'relative'
      }">
        <div class="form-overlay">
          <h3 class="mb-3" style="text-align: center; font-weight: 700; font-size: 3rem;">We're Here to Help You</h3>
          <p class="mb-4" style="text-align: center; font-weight: 400; font-size: .8rem;">Reach out to us for inquiries, support, or feedback.</p>
          <div class="row">
            <div class="col-md-12 mb-4">
              <textarea v-model="form.message" class="form-control" rows="1" placeholder="What can we do for you ?" :class="{ 'is-invalid': errors.message }"></textarea>
              <div v-if="errors.message" class="invalid-feedback">{{ errors.message }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <input v-model="form.name" type="text" class="form-control" placeholder="First Name" :class="{ 'is-invalid': errors.name }">
              <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <input v-model="form.lastName" type="text" class="form-control" placeholder="Last Name">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <input v-model="form.company" type="text" class="form-control" placeholder="Company">
            </div>
            <div class="col-md-6 mb-3">
              <input v-model="form.email" type="email" class="form-control" placeholder="Email" :class="{ 'is-invalid': errors.email }">
              <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <select v-model="form.country" class="form-control" @change="updatePhoneCode">
                <option disabled value="">Select Country</option>
                <option v-for="country in countries" :key="country.code" :value="country.name" :data-code="country.code">{{ country.name }}</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <div class="input-group">
                <span class="input-group-text">{{ phoneCode }}</span>
                <input v-model="form.phone" type="tel" class="form-control" placeholder="Phone" :class="{ 'is-invalid': errors.phone }">
                <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mb-3">
              <div class="form-check">
                <div id="recaptcha-container"></div>
                <div v-if="recaptchaError" class="text-danger mt-2">Please complete the reCAPTCHA.</div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align: right;">
              <button @click="submitForm" class="btn btn-danger">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Swal from 'sweetalert2';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import BreadCrums from '@/Layouts/BreadCrums.vue';
import Banner from "@/Components/helpers/Banner.vue";

defineProps({
  ContactUsBanner: String,
});

const form = ref({
  name: '',
  lastName: '',
  company: '',
  country: '',
  email: '',
  phone: '',
  message: '',
});

const phoneCode = ref('');
const errors = ref({});
const recaptchaToken = ref('');
const recaptchaError = ref(false);

const countries = ref([
  { name: 'United States', code: '+1' },
  { name: 'Canada', code: '+1' },
  { name: 'United Kingdom', code: '+44' },
  { name: 'Australia', code: '+61' },
  { name: 'India', code: '+91' },
  { name: 'China', code: '+86' },
  { name: 'Germany', code: '+49' },
  { name: 'France', code: '+33' },
  { name: 'Japan', code: '+81' },
  { name: 'Brazil', code: '+55' },
  { name: 'South Africa', code: '+27' },
  { name: 'Mexico', code: '+52' },
  { name: 'Russia', code: '+7' },
  { name: 'Italy', code: '+39' },
  { name: 'Spain', code: '+34' },
  { name: 'Nigeria', code: '+234' },
  { name: 'Argentina', code: '+54' },
  { name: 'South Korea', code: '+82' },
  { name: 'Saudi Arabia', code: '+966' },
  { name: 'Turkey', code: '+90' },
  { name: 'Sweden', code: '+46' },
  { name: 'Switzerland', code: '+41' },
  { name: 'Netherlands', code: '+31' },
  { name: 'Norway', code: '+47' },
  { name: 'Denmark', code: '+45' },
  { name: 'Belgium', code: '+32' },
  { name: 'Austria', code: '+43' },
  { name: 'Singapore', code: '+65' },
  { name: 'New Zealand', code: '+64' },
  { name: 'Israel', code: '+972' },
  { name: 'Egypt', code: '+20' },
  { name: 'Philippines', code: '+63' },
  { name: 'Vietnam', code: '+84' },
  { name: 'Thailand', code: '+66' },
  { name: 'Malaysia', code: '+60' },
  { name: 'Pakistan', code: '+92' },
  { name: 'Bangladesh', code: '+880' },
  { name: 'Indonesia', code: '+62' },
  { name: 'Chile', code: '+56' },
  { name: 'Colombia', code: '+57' },
  { name: 'Venezuela', code: '+58' },
  { name: 'Peru', code: '+51' },
  { name: 'Poland', code: '+48' },
  { name: 'Czech Republic', code: '+420' },
  { name: 'Hungary', code: '+36' },
  { name: 'Portugal', code: '+351' },
  { name: 'Greece', code: '+30' },
  { name: 'Romania', code: '+40' },
  { name: 'Ukraine', code: '+380' },
  { name: 'United Arab Emirates', code: '+971' },
  { name: 'Oman', code: '+968' },
  { name: 'Bahrain', code: '+973' },
  { name: 'Iran', code: '+98' },
  { name: 'Iraq', code: '+964' },
  { name: 'Jordan', code: '+962' },
  { name: 'Kuwait', code: '+965' },
  { name: 'Lebanon', code: '+961' },
  { name: 'Palestine', code: '+970' },
  { name: 'Qatar', code: '+974' },
  { name: 'Afghanistan', code: '+93' },
  { name: 'Albania', code: '+355' },
  { name: 'Algeria', code: '+213' },
]);

const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const updatePhoneCode = () => {
  const selectedCountry = countries.value.find(country => country.name === form.value.country);
  phoneCode.value = selectedCountry ? selectedCountry.code : '';
  if (form.value.phone && !form.value.phone.startsWith(phoneCode.value)) {
    form.value.phone = phoneCode.value + form.value.phone.replace(/^\+\d+/, '');
  }
};

const handleSubmit = async () => {
  if (!recaptchaToken.value) {
    recaptchaError.value = true;
    toast.error('Please complete the reCAPTCHA', { autoClose: 3000 });
    return;
  }

  // Prepend country code to phone number if country is selected
  let phoneWithCode = form.value.phone;
  if (form.value.country && phoneCode.value && !phoneWithCode.startsWith(phoneCode.value)) {
    phoneWithCode = phoneCode.value + phoneWithCode;
  }

  const payload = {
    ...form.value,
    phone: phoneWithCode,
    recaptchaToken: recaptchaToken.value,
    _token: document.querySelector('meta[name="csrf-token"]').content
  };
  console.log('Submitting payload:', payload, 'Token length:', recaptchaToken.value.length);

  try {
    const response = await axios.post('/contact-us/submitMail', payload);

    if (response.data.success) {
      toast.success('Message sent successfully!', { autoClose: 3000 });
      form.value = {
        name: '',
        lastName: '',
        company: '',
        country: '',
        email: '',
        phone: '',
        message: ''
      };
      recaptchaToken.value = '';
      phoneCode.value = '';
      window.grecaptcha.reset();
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errorMessage = error.response.data.message || 'Validation failed. Please check your inputs.';
      toast.error(errorMessage, { autoClose: 3000 });
      if (errorMessage.includes('reCAPTCHA')) {
        recaptchaToken.value = '';
        window.grecaptcha.reset();
        toast.error('Please complete reCAPTCHA again.', { autoClose: 3000 });
      }
    } else {
      toast.error('Error sending message: ' + (error.message || 'Unknown error'), { autoClose: 3000 });
    }
  }
};

const submitForm = () => {
  Swal.fire({
    title: 'Confirm Submission',
    text: 'Are you sure you want to send this message?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ef4137',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Send',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      errors.value = {};
      let hasError = false;

      if (!form.value.name) {
        errors.value.name = 'First Name is required';
        hasError = true;
      }
      if (!form.value.email) {
        errors.value.email = 'Email is required';
        hasError = true;
      } else if (!isValidEmail(form.value.email)) {
        errors.value.email = 'Please enter a valid email address';
        hasError = true;
      }
      if (!form.value.phone) {
        errors.value.phone = 'Phone is required';
        hasError = true;
      }
      if (!form.value.message) {
        errors.value.message = 'Message is required';
        hasError = true;
      }

      if (hasError) {
        toast.error('Please fill out all required fields correctly', { autoClose: 3000 });
        return;
      }

      handleSubmit();
    }
  });
};

const loadRecaptcha = () => {
  if (window.grecaptcha) {
    window.grecaptcha.render('recaptcha-container', {
      sitekey: '6LcqcygrAAAAAB6AaD59hXzXnu-6_QZZKTmLXDkD',
      callback: (token) => {
        recaptchaToken.value = token;
        recaptchaError.value = false;
        console.log('reCAPTCHA token generated:', token, 'Length:', token.length);
      },
      'expired-callback': () => {
        recaptchaToken.value = '';
        recaptchaError.value = true;
        toast.error('reCAPTCHA expired. Please try again.', { autoClose: 3000 });
      },
      'error-callback': () => {
        recaptchaError.value = true;
        toast.error('reCAPTCHA failed to load. Please refresh the page.', { autoClose: 3000 });
      }
    });
  } else {
    console.error('reCAPTCHA not loaded');
    toast.error('reCAPTCHA failed to initialize. Please refresh the page.', { autoClose: 3000 });
  }
};

onMounted(() => {
  const script = document.createElement('script');
  script.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaApiLoad&render=explicit';
  script.async = true;
  script.defer = true;
  script.onerror = () => {
    console.error('Failed to load reCAPTCHA script');
    toast.error('Failed to load reCAPTCHA. Check your network and try again.', { autoClose: 3000 });
  };
  document.head.appendChild(script);

  window.onRecaptchaApiLoad = () => {
    loadRecaptcha();
  };
});
</script>

<style scoped>
.content-with-vertical-line {
  display: flex;
  align-items: flex-start;
  border-left: 13px solid #EF3F37;
  padding-left: 12px;
}

.bg-color {
  background-color: #F0F0F0;
}

.contact-heading {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
}

.contact-address {
  font-size: 0.8rem;
  color: #797979;
  font-weight: 500;
}

.line-with-label {
  display: flex;
  align-items: center;
}

.label {
  background-color: #EF3F37;
  padding-right: 4px;
  z-index: 1;
  color: #FFFF;
  font-weight: 700;
  font-size: .8rem;
  padding-left: 4px;
  border-radius: 4px;
}

.line {
  flex-grow: 1;
  height: 1px;
  background-color: #EF3F37;
  margin-left: -2px;
}

.line_1 {
  flex-grow: 1;
  height: 1px;
  background-color: #F5F5F5;
  margin-left: -24px;
  margin-right: -24px;
}

.container-details {
  display: block !important;
}

.contact-info,
.map-container,
.logo-container {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.contact-details {
  background: #fff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.contact-info h5,
.contact-details h5 {
  font-size: 1.1rem;
  font-weight: 600;
}

.social-icons a {
  font-size: 1.5rem;
  color: #333;
  text-decoration: none;
}

.social-icons a:hover {
  color: #ef4137;
}

.logo-container img {
  max-width: 100%;
  height: auto;
}

.contact-form-section {
  background: url('https://via.placeholder.com/1200x400?text=Cityscape+Background') no-repeat center center;
  background-size: cover;
  position: relative;
  border-radius: 10px;
  color: #333;
}

.contact-form-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}

.contact-form-section > * {
  position: relative;
  z-index: 1;
}

.form-control {
  border-radius: 5px;
}

.btn-danger {
  background-color: #ef4137;
  border: none;
  padding: 10px 20px;
  font-weight: 600;
}

.btn-danger:hover {
  background-color: #d32f2f;
}

.input-group-text {
  background-color: #f8f9fa;
  border-right: none;
}

.form-control {
  border-left: none;
}
</style>