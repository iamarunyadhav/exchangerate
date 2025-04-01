<template>
  <div>
    <Navbar/>
    <div class="container mt-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white heading">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Currency Exchange Rates</h3>
            <div class="d-flex gap-2">
              <input 
                type="date" 
                class="form-control form-control-sm" 
                v-model="selectedDate"
                @change="fetchRates"
                :max="maxDate"
              >
              <select 
                class="form-select form-select-sm" 
                v-model="baseCurrency"
                @change="fetchRates"
              >
                <option value="USD">USD</option>
                <option value="AUD">AUD</option>
                <option value="CAD">CAD</option>
                <option value="GBP">GBP</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <template v-else>
            <div class="table-responsive mb-4">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Currency</th>
                    <th>Current Rate (LKR)</th>
                    <th>Weekly Average</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="fw-bold">{{ baseCurrency }}</td>
                    <td>{{ currentRate.toFixed(4) }}</td>
                    <td>{{ weeklyAverage.toFixed(4) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-4">
              <h5 class="mb-3">Historical Rates (Last 7 Days)</h5>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Exchange Rate (LKR)</th>
                      <th>Change</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(rate, index) in historicalRates" :key="index">
                      <td>{{ rate.date }}</td>
                      <td>{{ rate.value.toFixed(4) }}</td>
                      <td :class="getChangeClass(rate.change)">
                        {{ rate.change >= 0 ? '+' : '' }}{{ rate.change.toFixed(4) }}
                        <i :class="getChangeIcon(rate.change)"></i>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </template>
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
      selectedDate: new Date().toISOString().split('T')[0],
      maxDate: new Date().toISOString().split('T')[0],
      baseCurrency: 'USD',
      currentRate: 0,
      weeklyAverage: 0,
      historicalRates: [],
      loading: false
    };
  },
  created() {
    this.fetchRates();
  },
  methods: {
    async fetchRates() {
      this.loading = true;
      try {
        const [currentResponse, historicalResponse] = await Promise.all([
          api.rates.getCurrentRate(this.baseCurrency, this.selectedDate),
          api.rates.getHistoricalRates(this.baseCurrency, this.selectedDate)
        ]);
        
        this.currentRate = parseFloat(currentResponse.data.data.rate);
        
        if (historicalResponse.data?.data?.rates) {
          this.weeklyAverage = parseFloat(historicalResponse.data.data.weekly_average);
          this.processHistoricalData(historicalResponse.data.data.rates);
        }
      } catch (error) {
        console.error('Error fetching rates:', api.handleError(error));
        this.historicalRates = [];
      } finally {
        this.loading = false;
      }
    },
    processHistoricalData(ratesData) {
      const ratesArray = ratesData
        .map(rate => ({
          date: new Date(rate.date).toLocaleDateString('en-US', { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric' 
          }),
          value: parseFloat(rate.rate),
          rawDate: rate.date
        }))
        .sort((a, b) => new Date(a.rawDate) - new Date(b.rawDate));

      this.historicalRates = ratesArray.map((rate, index) => {
        const change = index > 0 
          ? rate.value - ratesArray[index - 1].value 
          : 0;
        return { 
          ...rate,
          change: parseFloat(change.toFixed(4))
        };
      });
    },
    getChangeClass(change) {
      return {
        'text-success': change > 0,
        'text-danger': change < 0,
        'text-muted': change === 0
      };
    },
    getChangeIcon(change) {
      return {
        'bi bi-arrow-up': change > 0,
        'bi bi-arrow-down': change < 0,
        'bi bi-dash': change === 0
      };
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
.text-success {
  color: #28a745;
}
.text-danger {
  color: #dc3545;
}
.text-muted {
  color: #6c757d;
}

.heading {
  background-color: #007bff;
  background-image: linear-gradient(315deg, #007bff, #004785);
  color: #fff;
}
</style>