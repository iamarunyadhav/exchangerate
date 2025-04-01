<template>
    <div class="audit-trail-container">
      <!-- Header with filters -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Audit Trail</h3>
        <div class="filter-controls">
          <select v-model="filters.action" class="form-select form-select-sm me-2" style="width: 200px">
            <option value="">All Actions</option>
            <option v-for="action in uniqueActions" :value="action">{{ action }}</option>
          </select>
          <input 
            v-model="filters.search" 
            type="text" 
            class="form-control form-control-sm" 
            placeholder="Search descriptions..."
            style="width: 250px"
          >
        </div>
      </div>
  
      <!-- Timeline-style logs -->
      <div class="audit-timeline">
        <div v-for="log in filteredLogs" :key="log.id" class="timeline-item">
          <div class="timeline-badge" :class="logBadgeClass(log.action)"></div>
          <div class="timeline-content card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h6 class="card-title mb-1">
                  <span class="badge" :class="logBadgeClass(log.action)">
                    {{ humanReadableAction(log.action) }}
                  </span>
                </h6>
                <small class="text-muted">{{ formatDate(log.created_at) }}</small>
              </div>
              <p class="card-text mb-1">{{ log.description }}</p>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <span v-if="log.user" class="badge bg-light text-dark">
                    <i class="bi bi-person-fill me-1"></i>
                    {{ log.user.email }}
                  </span>
                  <span v-if="log.ip_address" class="badge bg-light text-dark ms-2">
                    <i class="bi bi-pc-display me-1"></i>
                    {{ log.ip_address }}
                  </span>
                </div>
                <button 
                  v-if="log.metadata" 
                  class="btn btn-sm btn-outline-secondary"
                  @click="toggleDetails(log)"
                >
                  <i class="bi" :class="showDetails[log.id] ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                  Details
                </button>
              </div>
              
              <!-- Metadata details -->
              <div v-if="showDetails[log.id] && log.metadata" class="mt-2 p-2 bg-light rounded">
                <pre class="mb-0">{{ JSON.stringify(log.metadata, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-4">
        <nav>
          <ul class="pagination">
            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
              <button class="page-link" @click="changePage(pagination.current_page - 1)">
                &laquo;
              </button>
            </li>
            <li 
              v-for="page in pagination.last_page" 
              :key="page"
              class="page-item"
              :class="{ active: pagination.current_page === page }"
            >
              <button class="page-link" @click="changePage(page)">{{ page }}</button>
            </li>
            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
              <button class="page-link" @click="changePage(pagination.current_page + 1)">
                &raquo;
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        logs: [],
        showDetails: {},
        filters: {
          action: '',
          search: ''
        },
        pagination: {
          current_page: 1,
          last_page: 1,
          per_page: 15
        }
      }
    },
    computed: {
  uniqueActions() {
    if (!Array.isArray(this.logs)) return []; // Handle non-array case
    
    return [...new Set(
      this.logs
        .filter(log => log?.action) // Only logs with actions
        .map(log => log.action)     // Extract action strings
    )].sort();                      // Optional: sort alphabetically
  }
},
    async created() {
      await this.fetchLogs();
    },
    methods: {
      async fetchLogs(page = 1) {
        const params = {
          page,
          per_page: this.pagination.per_page
        };
        
        if (this.filters.action) params.action = this.filters.action;
        if (this.filters.search) params.search = this.filters.search;
  
        const response = await this.$http.get('/audit-logs', { params });
        this.logs = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page
        };
      },
      toggleDetails(log) {
        this.$set(this.showDetails, log.id, !this.showDetails[log.id]);
      },
      changePage(page) {
        if (page > 0 && page <= this.pagination.last_page) {
          this.pagination.current_page = page;
          this.fetchLogs(page);
        }
      },
      formatDate(dateString) {
        return new Date(dateString).toLocaleString();
      },
      humanReadableAction(action) {
        return action
          .split('_')
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');
      },
      logBadgeClass(action) {
        const actionMap = {
          login: 'bg-success',
          logout: 'bg-secondary',
          create: 'bg-primary',
          update: 'bg-warning text-dark',
          delete: 'bg-danger',
          default: 'bg-info'
        };
        
        const baseAction = action.split('_')[0];
        return actionMap[baseAction] || actionMap.default;
      }
    },
    watch: {
      filters: {
        handler() {
          this.fetchLogs();
        },
        deep: true
      }
    }
  }
  </script>
  
  <style scoped>
  .audit-trail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .audit-timeline {
    position: relative;
    padding-left: 50px;
  }
  
  .timeline-item {
    position: relative;
    margin-bottom: 20px;
  }
  
  .timeline-badge {
    position: absolute;
    left: -25px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #6c757d;
    z-index: 1;
  }
  
  .timeline-content {
    position: relative;
    border-left: 2px solid #dee2e6;
    padding-left: 20px;
  }
  
  .timeline-content::before {
    content: '';
    position: absolute;
    top: 16px;
    left: -6px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #6c757d;
  }
  
  .card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  }
  
  .badge {
    font-weight: 500;
  }
  
  .pre-wrap {
    white-space: pre-wrap;
  }
  </style>