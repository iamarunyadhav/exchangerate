import { createStore } from 'vuex'
import api from '@/services/api'

export default createStore({
  state: {
    user: null,
    isAuthenticated: !!localStorage.getItem('auth_token'),
    loading: false,
    error: null,
    exchangeRates: {
      current: {},
      historical: {},
      averages: {}
    }
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
      state.isAuthenticated = !!user
      if (user) {
        localStorage.setItem('auth_token', user.token || localStorage.getItem('auth_token'))
        localStorage.setItem('auth_email', user.email || localStorage.getItem('auth_email'))
      }
    },
    CLEAR_AUTH(state) {
      state.user = null
      state.isAuthenticated = false
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_email')
    },
    SET_LOADING(state, isLoading) {
      state.loading = isLoading
    },
    SET_ERROR(state, error) {
      state.error = error
    },
    SET_EXCHANGE_RATES(state, { current, historical, averages }) {
      state.exchangeRates.current = current
      state.exchangeRates.historical = historical
      state.exchangeRates.averages = averages
    }
  },
  actions: {
    // async login({ commit }, email) {
    //   commit('SET_LOADING', true)
    //   try {
    //     const response = await api.auth.login(email)
    //     localStorage.setItem('auth_token', response.data.token)
    //     localStorage.setItem('auth_email', email)
    //     commit('SET_USER', { email })
    //     return { success: true }
    //   } catch (error) {
    //     commit('SET_ERROR', api.handleError(error))
    //     commit('CLEAR_AUTH')
    //     return { success: false }
    //   } finally {
    //     commit('SET_LOADING', false)
    //   }
    // },

    async initializeAuth({ commit }) {
      const token = localStorage.getItem('auth_token')
      if (!token) return false
      
      try {
        // Try to get fresh user data from backend
        const response = await api.auth.getAuthenticatedUser()
        commit('SET_USER', response.data.user || { 
          email: localStorage.getItem('auth_email') 
        })
        return true
      } catch (error) {
        // Fallback to localStorage data if API fails but token exists
        const email = localStorage.getItem('auth_email')
        if (email) {
          commit('SET_USER', { email })
          return true
        }
        commit('CLEAR_AUTH')
        return false
      }
    },

    async logout({ commit }) {
      commit('SET_LOADING', true)
      try {
        await api.auth.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        commit('CLEAR_AUTH')
        commit('SET_LOADING', false)
      }
    },
  },
  getters: {
    currentUser: state => state.user,
    isAuthenticated: state => state.isAuthenticated,
    isLoading: state => state.loading,
    authError: state => state.error,
    userEmail: state => state.user?.email || localStorage.getItem('auth_email') || null
  }
})