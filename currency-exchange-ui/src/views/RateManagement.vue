<template>
    <div>
      <Navbar/>
      <div class="container mt-4">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Manage Exchange Rates</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitRate" class="mb-4">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    v-model="form.date"
                    required
                  >
                </div>
                <div class="col-md-3">
                  <label class="form-label">Currency</label>
                  <select 
                    class="form-select" 
                    v-model="form.base_currency"
                    required
                  >
                    <option value="USD">USD</option>
                    <option value="AUD">AUD</option>
                    <option value="CAD">CAD</option>
                    <option value="GBP">GBP</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Rate (LKR)</label>
                  <input 
                    type="number" 
                    step="0.0001" 
                    class="form-control" 
                    v-model="form.rate"
                    placeholder="0.0000"
                    required
                  >
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <button 
                    class="btn btn-primary w-100" 
                    type="submit"
                    :disabled="loading"
                  >
                    <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                    {{ loading ? 'Adding...' : 'Add Rate' }}
                  </button>
                </div>
              </div>
            </form>
  
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Currency</th>
                    <th>Rate (LKR)</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="entry in recentEntries" :key="entry.id">
                    <td>{{ formatDate(entry.date) }}</td>
                    <td>{{ entry.base_currency }}</td>
                    <td>{{ entry.rate }}</td>
                    <td>
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deleteEntry(entry.id)"
                        :disabled="loading"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
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
        date: new Date().toISOString().split('T')[0],
        currency: 'USD',
        rate: ''
      },
      recentEntries: [],
      loading: false
    };
  },
  created() {
    this.fetchRecentEntries();
  },
  methods: {
    async fetchRecentEntries() {
      this.loading = true;
      try {
        const response = await api.rates.getRecentRates();
        this.recentEntries = response.data.data;
      } catch (error) {
        console.error('Error fetching rates:', api.handleError(error));
        this.recentEntries = [];
      } finally {
        this.loading = false;
      }
    },
    async submitRate() {
      this.loading = true;
      try {
        await api.rates.submitManualRate({
          ...this.form,
          rate: parseFloat(this.form.rate)
        });
        this.form.rate = '';
        this.fetchRecentEntries();
      } catch (error) {
        alert(api.handleError(error));
      } finally {
        this.loading = false;
      }
    },
    async deleteEntry(id) {
      if (!confirm('Are you sure you want to delete this rate entry?')) return;
      
      this.loading = true;
      try {
        await api.rates.deleteRate(id);
        this.fetchRecentEntries();
      } catch (error) {
        alert(api.handleError(error));
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
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
  </style>