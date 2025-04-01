<template>
    <div>
      <Navbar />
      <div class="container mt-5">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card shadow">
              <div class="card-header bg-primary text-white heading">
                <h3 class="mb-0">Add Exchange Rate</h3>
              </div>
              <div class="card-body">
                <form @submit.prevent="submitRate">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Base Currency</label>
                      <select v-model="form.base_currency" class="form-select" required>
                        <option value="USD">USD</option>
                        <option value="AUD">AUD</option>
                        <option value="CAD">CAD</option>
                        <option value="GBP">GBP</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Target Currency</label>
                      <select v-model="form.target_currency" class="form-select" required>
                        <option value="LKR">LKR</option>
                        <!-- Add other target currencies if needed -->
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Rate</label>
                      <input 
                        v-model="form.rate" 
                        type="number" 
                        step="0.0001" 
                        class="form-control" 
                        required
                        placeholder="1.0000"
                      >
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Date</label>
                      <input
                        v-model="form.date"
                        type="date"
                        class="form-control"
                        :max="maxDate"
                        :min="minDate"
                        required
                      >
                      <small class="text-muted">Only dates within last 90 days allowed</small>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                      <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                        {{ loading ? 'Saving...' : 'Save Rate' }}
                      </button>
                    </div>
                  </div>
                </form>
  
                <div v-if="successMessage" class="alert alert-success mt-3">
                  {{ successMessage }}
                </div>
              </div>
            </div>
  
            <!-- Recent Rates Table -->
            <div class="card shadow mt-4">
              <div class="card-header bg-light heading">
                <h4 class="mb-0">Recent Exchange Rates</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Base Currency</th>
                        <th>Target Currency</th>
                        <th>Exchange Rate</th>
                      </tr>
                    </thead>
                    <tbody>
                    <tr v-for="entry in recentRates" :key="`${entry.date}-${entry.base_currency}`">
                      <td>{{ formatDate(entry.date) }}</td>
                      <td>{{ entry.base_currency }}</td>
                      <td>{{ entry.target_currency }}</td>
                      <td>{{ parseFloat(entry.rate).toFixed(4) }}</td>
                    </tr>
                    <tr v-if="recentRates.length === 0">
                  <td colspan="4" class="text-center">No exchange rates found</td>
                </tr>
                </tbody>
            </table>
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
import AppFooter from '@/components/AppFooter.vue';
import api from '@/services/api';

export default {
  components: { Navbar, AppFooter },
  data() {
    const today = new Date();
    const minDate = new Date();
    minDate.setDate(today.getDate() - 90);
    
    return {
      form: {
        base_currency: 'USD',
        target_currency: 'LKR',
        rate: '',
        date: today.toISOString().split('T')[0]
      },
      recentRates: [],
      loading: false,
      successMessage: '',
      maxDate: today.toISOString().split('T')[0],
      minDate: minDate.toISOString().split('T')[0]
    };
  },
  created() {
    this.fetchRecentRates();
  },
  methods: {
    async submitRate() {
      this.loading = true;
      this.successMessage = '';
      
      try {
        const response = await api.rates.submitManualRate(this.form);
        this.successMessage = response.data.message;
        this.form.rate = '';
        this.fetchRecentRates();
      } catch (error) {
        this.successMessage = '';
        alert(api.handleError(error));
      } finally {
        this.loading = false;
      }
    },
    async fetchRecentRates() {
      try {
        const response = await api.rates.getRecentRates();
        this.recentRates = response.data.data;
      } catch (error) {
        console.error('Error fetching rates:', api.handleError(error));
        this.recentRates = [];
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
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
  .table th {
    font-weight: 600;
  }
  .alert {
    border-radius: 0.5rem;
  }
  .heading {
  background-color: #007bff;
  background-image: linear-gradient(315deg, #007bff, #004785);
  color: #fff;
}
  </style>