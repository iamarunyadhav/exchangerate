// src/services/api.js
import axios from 'axios';
import store from '@/store';
import router from '@/router';

// Configuration constants
const API_CONFIG = {
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
};

// Create axios instance
const api = axios.create(API_CONFIG);

/**
 * Request interceptor to add auth token if available
 */
const requestInterceptor = (config) => {
  const token = localStorage.getItem('auth_token') || localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
};

/**
 * Response interceptor to handle errors globally
 */
const responseInterceptor = (error) => {
  if (error.response?.status === 401) {
    store.dispatch('auth/logout');
  }
  
  return Promise.reject(error);
};

// Add interceptors
api.interceptors.request.use(requestInterceptor);
api.interceptors.response.use(
  response => response, 
  responseInterceptor
);

/**
 * API Service Methods
 */
const apiService = {
  /**
   * Auth endpoints
   */
  auth: {

    async login(email) {
        try {
          const response = await api.post('/auth/login', { email });
          return response;
        } catch (error) {
          // Clear any existing auth data on failure
          localStorage.removeItem('auth_token');
          localStorage.removeItem('auth_email');
          store.commit('auth/CLEAR_AUTH');
          throw error;
        }
      },
    async authenticate(token) {
    try {
        const response = await api.post('/auth/authenticate', { token });
        
        // Store the Sanctum token
        localStorage.setItem('auth_token', response.data.token);
        
        // Get user data
        const userResponse = await this.getUser();
        
        // Commit user data to store
        store.commit('auth/SET_USER', userResponse.data);
        
        // Redirect to dashboard
        router.push('/dashboard');
        
        return response;
      } catch (error) {
          this.handleAuthError(error);
          throw error;
      }
    }, 
    
    async verifyOTP({ email, otp }) {
      return api.post('/auth/verify-otp', { email, otp });
    },
    async getAuthenticatedUser() {
        return api.get('/auth/user');
    },
    
    async logout() {
        try {
          await api.post('/auth/logout');
        } finally {
          // Clear auth data regardless of API success
          localStorage.removeItem('auth_token');
          localStorage.removeItem('auth_email');
          store.commit('auth/CLEAR_AUTH');
          router.push('/login');
        }
      },
    async getUser() {
      return api.get('/user');
    },
    
    async checkUser(email) {
      return api.post('/check-user', { email });
    },
    
    async register(userData) {
      return api.post('/auth/register', userData);
    }
  },

  /**
   * Exchange Rates endpoints
   */
  rates: {
    async getCurrentRate(currency, date) {
      return api.get(`/exchange-rate/current/${currency}`, { 
        params: { date } 
      });
    },
    
    async getHistoricalRates(currency, date) {
      return api.get(`/exchange-rate/${currency}/last-seven-days`, { 
        params: { date } 
      });
    },
    
    async getRecentRates() {
      return api.get('/exchange-rate/recent');
    },
    
    async submitManualRate(rateData) {
      return api.post('/exchange-rate/manual', rateData);
    }
  },

  /**
   * Enhanced error handler
   */
  handleError(error) {
    const defaultMessage = 'An unexpected error occurred';
    
    if (axios.isCancel(error)) {
      return 'Request was cancelled';
    }
    
    if (!error.response) {
      return error.message || 'Network error - please check your connection';
    }
    
    const { status, data } = error.response;
    
    switch (status) {
      case 400:
        return data.message || 'Bad request';
      case 401:
        return data.message || 'Unauthorized access';
      case 403:
        return data.message || 'Forbidden';
      case 404:
        return data.message || 'Resource not found';
      case 500:
        return data.message || 'Server error';
      default:
        return data?.message || defaultMessage;
    }
  },

  /**
   * Cancel token generator for cancelable requests
   */
  createCancelToken() {
    return axios.CancelToken.source();
  }
};

export default apiService;