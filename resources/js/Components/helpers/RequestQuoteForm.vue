

  <template>
    <form @submit.prevent="submitForm"  @keydown.enter.prevent class="quote-form">
      <h5 class="form-title">Request a Quote</h5>
  
      <div class="form-group">
        <label>Name</label>
        <input v-model="form.name" type="text" class="form-control" />
        <div v-if="form.errors.name" class="error">{{ form.errors.name }}</div>
      </div>
  
      <div class="form-group">
        <label>Email</label>
        <input v-model="form.email" type="email" class="form-control" />
        <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
      </div>
  
      <div class="form-group">
        <label>Contact Number</label>
        <input v-model="form.phone" type="number" class="form-control"/>
        <div v-if="form.errors.phone" class="error">{{ form.errors.phone }}</div>
      </div>
  
      <div class="form-group">
        <label>Quantity</label>
        <input v-model="form.quantity" type="number" min="1" />
        <div v-if="form.errors.quantity" class="error">{{ form.errors.quantity }}</div>
      </div>
  
      <div class="form-group">
        <label>Attach File</label>
        <input type="file" @change="handleFileUpload" />
      </div>
  
      <button type="submit" class="btn-submit">Submit</button>
    </form>
  </template>
  

<script setup>
import { useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import axios from 'axios'

const props = defineProps({
  productId: Number
})

const form = useForm({
  name: '',
  email: '',
  phone: '',
  quantity: 1,
  file: null
})

const handleFileUpload = (event) => {
  form.file = event.target.files[0]
}

const submitForm = () => {
  // Clear old errors
  form.clearErrors()

  axios.post(`/products/request-quote/${props.productId}`, form.data(), {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  .then(() => {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Quote request sent successfully!',
      showConfirmButton: false,
      timer: 3000
    })
    form.reset()
  })

  .catch((error) => {
  if (error.response?.status === 422) {
    const errors = error.response.data.errors
    const formattedErrors = {}

    // Convert array of messages to a single string
    for (const field in errors) {
      formattedErrors[field] = errors[field].join(' ')
    }

    form.setError(formattedErrors)
  } else {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: error.response?.data?.message || 'Something went wrong',
      showConfirmButton: false,
      timer: 3000
    })
  }
})

}
</script>
<style>
form-control {
  display: block;
  width: 50%;
  padding: .375rem .75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 0.5;
  color: #212529;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: .375rem;
  transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

.form-control:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .25);
}
/* Form Container */
.quote-form {
  background: #fff;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  max-width: 500px;
  margin: auto;
  font-family: 'Inter', sans-serif;
}
/* Remove card look when inside modal */
.in-modal .quote-form {
  background: transparent;
  padding: 0;
  border-radius: 0;
  box-shadow: none;
  max-width: 100%;
}

/* Title */
.quote-form .form-title {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  font-weight: bold;
  color: #333;
  text-align: center;
  
}

/* Form Groups */
.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-weight: 400;
  margin-bottom: 0.2rem;
  color:black;
  font-family:  'Inter', sans-serif;
  font-size:14px ;
  text-decoration:none!important;
}

/* Inputs */
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="number"],
.form-group input[type="file"] {
  width: 100%;
  padding: 0.55rem 0.75rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 0.85rem;
  transition: all 0.2s ease;
  height:20%;
}

.form-group input:focus {
  border-color: #4a90e2;
  box-shadow: 0 0 0 3px rgba(74,144,226,0.15);
  outline: none;
}

/* Error Messages */
.error {
  color: #d9534f;
  font-size: 0.85rem;
  margin-top: 0.3rem;
}

/* Submit Button */
.btn-submit {
  background:#EF4137;
  color: white;
  padding: 0.6rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: .9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
  display: block;
  width: 100%;
  height: 1.1 rem;
}

.btn-submit:hover {
  background: #EF4137;
}

.btn-submit:active {
  transform: scale(0.98);
}

</style>
