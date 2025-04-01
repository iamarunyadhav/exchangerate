<template>
    <div>
      <Navbar />
      <div class="container mt-5">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="card shadow">
              <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Login</h2>
                
                <!-- Email Form (First Step) -->
                <form @submit.prevent="sendOTP" v-if="!showOTPForm">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input 
                      type="email" 
                      class="form-control" 
                      id="email" 
                      v-model="email"
                      required
                      placeholder="Enter your email"
                      :disabled="loading"
                    >
                  </div>
                  <div class="d-grid gap-2">
                    <button 
                      type="submit" 
                      class="btn btn-primary"
                      :disabled="loading || !email"
                    >
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Sending OTP...' : 'Send OTP' }}
                    </button>
                  </div>
                </form>
  
                <!-- OTP Form (Second Step) -->
                <form @submit.prevent="verifyOTP" v-else>
                  <div class="mb-3">
                    <label class="form-label">Enter OTP sent to {{ email }}</label>
                    <input 
                      v-model="otp"
                      type="text" 
                      class="form-control"
                      maxlength="6"
                      placeholder="6-digit code"
                      required
                      :disabled="loading"
                    >
                    <div class="d-flex justify-content-between mt-1">
                      <small class="text-muted">Expires in {{ formattedCountdown }}</small>
                      <a href="#" @click.prevent="resendOTP" class="small">Resend OTP</a>
                    </div>
                    <div v-if="otpExpired" class="text-danger small mt-1">
                      OTP has expired. Please request a new one.
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button 
                      type="submit" 
                      class="btn btn-primary"
                      :disabled="loading || otp.length !== 6 || otpExpired"
                    >
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Verifying...' : 'Verify OTP' }}
                    </button>
                  </div>
                  <div class="text-center mt-3">
                    <a href="#" @click.prevent="showOTPForm = false">Use different email</a>
                  </div>
                </form>
  
                <div v-if="successMessage" class="alert alert-success mt-3 mb-0">
                  <i class="bi bi-check-circle-fill me-2"></i>
                  {{ successMessage }}
                </div>
                <div v-if="errorMessage" class="alert alert-danger mt-3 mb-0">
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
  import Navbar from '@/components/Navbar.vue';
  import api from '@/services/api';
  
  export default {
    components: {
      Navbar
    },
    data() {
      return {
        email: '',
        otp: '',
        loading: false,
        showOTPForm: false,
        successMessage: '',
        errorMessage: '',
        countdown: 600, // 10 minutes in seconds
        timer: null,
        otpExpired: false
      };
    },
    computed: {
      formattedCountdown() {
        const mins = Math.floor(this.countdown / 60);
        const secs = this.countdown % 60;
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
      }
    },
    methods: {
      async sendOTP() {
        this.loading = true;
        this.successMessage = '';
        this.errorMessage = '';
        this.otpExpired = false;
        
        try {
          const response = await api.auth.login(this.email);
          
          if (response.data.message === 'OTP sent to your email') {
            this.successMessage = 'OTP sent to your email';
            this.showOTPForm = true;
            this.countdown = 600; // Reset countdown
            this.startCountdown();
          } else {
            throw new Error('Failed to send OTP');
          }
        } catch (error) {
          this.errorMessage = "Email not registered please signup ";
          console.error('OTP send error:', error);
        } finally {
          this.loading = false;
        }
      },
      
      async verifyOTP() {
        if (this.otpExpired) {
          this.errorMessage = 'OTP has expired. Please request a new one.';
          return;
        }
  
        this.loading = true;
        this.errorMessage = '';
        
        try {
          const response = await api.auth.verifyOTP({
            email: this.email,
            otp: this.otp
          });
          
          if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
            localStorage.setItem('auth_email', this.email);
            this.$router.push('/dashboard');
          } else {
            throw new Error('Invalid OTP verification response');
          }
        } catch (error) {
          this.errorMessage = api.handleError(error);
          console.error('OTP verification error:', error);
        } finally {
          this.loading = false;
        }
      },
  
      async resendOTP() {
        this.loading = true;
        this.errorMessage = '';
        this.otp = '';
        
        try {
          await api.auth.login(this.email);
          this.successMessage = 'New OTP sent to your email';
          this.countdown = 600; // Reset countdown
          this.otpExpired = false;
          this.startCountdown();
        } catch (error) {
          this.errorMessage = api.handleError(error);
        } finally {
          this.loading = false;
        }
      },
  
      startCountdown() {
        if (this.timer) {
          clearInterval(this.timer);
        }
        
        this.timer = setInterval(() => {
          if (this.countdown > 0) {
            this.countdown--;
          } else {
            clearInterval(this.timer);
            this.otpExpired = true;
            this.errorMessage = 'OTP has expired. Please request a new one.';
          }
        }, 1000);
      }
    },
    beforeUnmount() {
      if (this.timer) {
        clearInterval(this.timer);
      }
    }
  };
  </script>
  
  <style scoped>
  .card {
    border-radius: 0.5rem;
  }
  .card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
  }
  .alert {
    border-radius: 0.5rem;
  }
  .btn-primary {
    border-radius: 0.5rem;
  }
  .heading {
    background-color: #007bff;
    background-image: linear-gradient(315deg, #007bff, #004785);
    color: #fff;
  }
  </style>