import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import ExchangeRates from '../views/ExchangeRates.vue';
import RateManagement from '../views/RateManagement.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import store from '../store';
import Dashboard from '@/views/Dashboard.vue';
import api from '@/services/api';
import Audit from '@/components/Audit.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { public: true }
  },
  {
    path: '/rates',
    name: 'rates',
    component: ExchangeRates,
    meta: { public: true }
  },
  {
    path: '/manage',
    name: 'manage',
    component: RateManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { public: true, hideNavbar: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { public: true, hideNavbar: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/auditlog',
    name: 'auditlog',
    component: Audit,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to, from, next) => {
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    // Use the store's initializeAuth action
    const isAuthenticated = await store.dispatch('initializeAuth');
    
    if (!isAuthenticated) {
      return next('/login');
    }
    next();
  } 
  // Redirect authenticated users away from auth pages
  else if ((to.path === '/login' || to.path === '/register') && store.state.isAuthenticated) {
    next('/dashboard');
  } 
  // Public routes
  else {
    next();
  }
});

export default router;