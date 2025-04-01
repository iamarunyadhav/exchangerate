<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">
        <i class="bi bi-currency-exchange me-2"></i>Currency Exchange
      </router-link>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link px-3" to="/rates" active-class="active">
              <i class="bi bi-graph-up me-1"></i> Exchange Rates
            </router-link>
          </li>
          <li v-if="isAuthenticated" class="nav-item">
            <router-link class="nav-link px-3" to="/dashboard" active-class="active">
              <i class="bi bi-pencil-square me-1"></i> Dashboard
            </router-link>
          </li>

          <!-- <li v-if="isAuthenticated" class="nav-item">
            <router-link class="nav-link px-3" to="/manage" active-class="active">
              <i class="bi bi-pencil-square me-1"></i> Manage
            </router-link>
          </li> -->
        </ul>

        <ul class="navbar-nav ms-auto">
          <template v-if="!isAuthenticated">
            <li class="nav-item">
              <router-link class="nav-link px-3" to="/login" active-class="active">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link px-3" to="/register" active-class="active">
                <i class="bi bi-person-plus me-1"></i> Register
              </router-link>
            </li>
          </template>
          
          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> {{ userEmail }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <button @click="handleLogout" class="dropdown-item" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="bi bi-box-arrow-left me-1"></i> Logout
                  </button>
                </li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
  name: 'AppNavbar',
  data() {
    return {
      loading: false
    };
  },
  computed: {
    ...mapState({
      isAuthenticated: state => state.isAuthenticated,
      user: state => state.user
    }),
    userEmail() {
      return this.user?.email || localStorage.getItem('auth_email') || 'Account';
    }
  },
  methods: {
    ...mapActions(['logout']),
    
    async handleLogout() {
      this.loading = true;
      try {
        await this.logout();
        this.$router.push('/login');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>




<style scoped>
.navbar {
  padding: 0.75rem 0;
}

.navbar-brand {
  font-size: 1.25rem;
  color: #1c67b3;
  transition: color 0.2s;
}

.navbar-brand:hover {
  color: #294dc1;
}

.nav-link {
  font-weight: 500;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.nav-link.active {
  color: #1c67b3;
  font-weight: 600;
}

.btn-outline-danger {
  border-color: transparent;
}

.btn-outline-danger:hover {
  background-color: rgba(220, 53, 69, 0.1);
}

.navbar-toggler {
  border: none;
  padding: 0.5rem;
}

.navbar-toggler:focus {
  box-shadow: none;
}

@media (max-width: 991.98px) {
  .navbar-collapse {
    padding-top: 1rem;
  }
  
  .nav-item {
    margin-bottom: 0.5rem;
  }
}
</style>