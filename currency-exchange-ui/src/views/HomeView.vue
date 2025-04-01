<template>
  <div>
    <Navbar />
    <div class="container mt-5">
      <header class="py-5 mb-4 border-bottom">
        <div class="container">
          <div class="text-center my-5">
            <h1 class="fw-light">CURRENCY EXCHANGE</h1>
            <p class="lead text-muted">
              View and manage currency exchange rates with secure and efficient operations
            </p>
          </div>
        </div>
      </header>

      <div class="row text-center">
        <!-- View Rates Card -->
        <div class="col-lg-6 mb-5">
          <div class="card h-100 shadow border-0">
            <div class="card-body">
              <h2 class="card-title">View Rates</h2>
              <p class="card-text">
                See current exchange rates and historical trends for USD, AUD, CAD, and GBP.
              </p>
              <router-link to="/rates" class="btn btn-primary">View Rates</router-link>
            </div>
          </div>
        </div>

        <!-- Manage Rates Card -->
        <div class="col-lg-6 mb-5">
          <div class="card h-100 shadow border-0">
            <div class="card-body">
              <h2 class="card-title">Manage Rates</h2>
              <p class="card-text">
                Registered users can add and manage exchange rate data with precision.
              </p>
              <router-link 
                v-if="isAuthenticated" 
                to="/manage" 
                class="btn btn-primary"
              >
                Manage Rates
              </router-link>
              <router-link 
                v-else 
                to="/login" 
                class="btn btn-outline-primary"
              >
                Login to Manage
              </router-link>
            </div>
          </div>
        </div>

        <!-- Historical Data Card -->
        <!-- <div class="col-lg-4 mb-5">
          <div class="card h-100 shadow border-0">
            <div class="card-body">
              <h2 class="card-title">Historical Data</h2>
              <p class="card-text">
                Analyze trends with comprehensive historical exchange rate data and charts.
              </p>
              <router-link to="/historical" class="btn btn-primary">View History</router-link>
            </div>
          </div>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '@/components/Navbar.vue';
import AppFooter from '@/components/AppFooter.vue';

export default {
  components: {
    Navbar,
    AppFooter
  }
};
</script>

<style scoped>
header {
  background-color: #007bff;
  background-image: linear-gradient(315deg, #007bff, #004785);
  color: #fff;
  padding-top: 48px;
  padding-bottom: 48px;
}

.card {
  transition: transform 0.2s;
  border-radius: 0.5rem;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.card-body {
  padding: 2rem;
}

.btn-primary {
  background-color: #1a73e8;
  border-color: #1a73e8;
  padding: 0.5rem 1.5rem;
  border-radius: 0.375rem;
}

.btn-outline-primary {
  color: #1a73e8;
  border-color: #1a73e8;
  padding: 0.5rem 1.5rem;
  border-radius: 0.375rem;
}

.btn-outline-primary:hover {
  background-color: #1a73e8;
  color: white;
}

h1 {
  color: #ffffff;
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.card-title {
  color: #2d3748;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.card-text {
  color: #4a5568;
  margin-bottom: 1.5rem;
  min-height: 60px;
}
.text-muted {
    --bs-text-opacity: 1;
    color: #ffffff !important;
}
</style>