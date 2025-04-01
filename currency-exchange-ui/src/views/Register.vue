<template>
  <div>
    <Navbar/>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow">
            <div class="card-body p-4 ">
              <h2 class="card-title text-center mb-4">Create Account</h2>
              
              <form @submit.prevent="register">
                <div class="mb-3">
                  <label for="name" class="form-label">Full Name</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    v-model="form.name"
                    required
                    placeholder="Enter your full name"
                  >
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    v-model="form.email"
                    required
                    placeholder="Enter your email"
                  >
                </div>
                <div class="d-grid gap-2 mt-4">
                  <button 
                    type="submit" 
                    class="btn btn-primary"
                    :disabled="loading"
                  >
                    <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                    {{ loading ? 'Sending magic link...' : 'Register' }}
                  </button>
                  <router-link 
                    to="/login" 
                    class="btn btn-outline-secondary"
                  >
                    Already have an account? Login
                  </router-link>
                </div>
              </form>
              
              <!-- Success message -->
              <div v-if="successMessage" class="alert alert-success mt-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ successMessage }}
              </div>

              <!-- Error Message (red) -->
              <div v-if="errorMessage" class="alert alert-danger mt-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ errorMessage }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';
import Navbar from '@/components/Navbar.vue';
import AppFooter from '@/components/AppFooter.vue';
  
export default {
  components: { Navbar, AppFooter },
  data() {
    return {
      form: {
        name: '',
        email: ''
      },
      loading: false,
      successMessage: '',
      errorMessage:''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      try {
        const response = await api.auth.register(this.form);
        this.successMessage = response.data.message || 'Registration successful! Check your email for a OTP link.';
        
        setTimeout(() => {
          this.$router.push('/login');
        }, 3000);
      } catch (error) {
        this.errorMessage ="The Email is already Registered";
        // alert(api.handleError(error));
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.card {
  border-radius: 0.5rem;
}
.alert {
  margin-bottom: 0;
}
.heading {
  background-color: #007bff;
  background-image: linear-gradient(315deg, #007bff, #004785);
  color: #fff;
}
</style>