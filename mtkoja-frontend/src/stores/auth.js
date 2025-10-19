import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE_URL = 'https://api.mrkoja.com/api'

// AUTH-INIT-GUARD-001: Ensure axios has Authorization header on page reload if token exists
const persistedToken = typeof window !== 'undefined' ? localStorage.getItem('token') : null
if (persistedToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${persistedToken}`
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    lastActivity: localStorage.getItem('lastActivity') ? new Date(localStorage.getItem('lastActivity')) : null,
    timeoutId: null,
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isBusinessOwner: (state) => state.user?.role === 'business_owner',
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post(`${API_BASE_URL}/login`, credentials)
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        this.updateActivity()
        
        // Store token in localStorage and set axios header
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Login failed' 
        }
      }
    },

    async register(userData) {
      try {
        const response = await axios.post(`${API_BASE_URL}/register`, userData)
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        this.updateActivity()
        
        // Store token in localStorage and set axios header
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Registration failed',
          errors: error.response?.data?.errors
        }
      }
    },

    async logout() {
      try {
        await axios.post(`${API_BASE_URL}/logout`)
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        this.isAuthenticated = false
        this.lastActivity = null
        this.timeoutId = null
        localStorage.removeItem('token')
        localStorage.removeItem('lastActivity')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get(`${API_BASE_URL}/user`)
        this.user = response.data.user
        this.isAuthenticated = true
      } catch (error) {
        this.logout()
      }
    },

    // AUTH-INIT-GUARD-001: Ensure user info is loaded when token exists
    async ensureUserLoaded() {
      if (this.token && !this.user) {
        await this.fetchUser()
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.isAuthenticated = true
        this.fetchUser()
        this.startActivityTimer()
      }
    },

    // Activity tracking methods
    updateActivity() {
      this.lastActivity = new Date()
      localStorage.setItem('lastActivity', this.lastActivity.toISOString())
      this.resetTimeout()
    },

    startActivityTimer() {
      this.resetTimeout()
    },

    resetTimeout() {
      if (this.timeoutId) {
        clearTimeout(this.timeoutId)
      }
      
      // Set timeout for 20 minutes (20 * 60 * 1000 ms)
      this.timeoutId = setTimeout(() => {
        this.logout()
        // Redirect to login page
        if (window.location.pathname !== '/login') {
          window.location.href = '/login'
        }
      }, 20 * 60 * 1000)
    },

    checkSession() {
      if (this.lastActivity) {
        const now = new Date()
        const timeDiff = now - this.lastActivity
        const twentyMinutes = 20 * 60 * 1000
        
        if (timeDiff > twentyMinutes) {
          this.logout()
          return false
        }
      }
      return true
    },

    // Admin user creation
    async createUser(userData) {
      if (!this.isAdmin) {
        throw new Error('Access denied. Admin role required.')
      }

      try {
        const formData = new FormData()
        
        // Add all user data to FormData
        Object.keys(userData).forEach(key => {
          if (userData[key] !== null && userData[key] !== undefined) {
            formData.append(key, userData[key])
          }
        })

        const response = await axios.post(`${API_BASE_URL}/admin/users`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
          }
        })

        return { success: true, user: response.data.user, token: response.data.token }
      } catch (error) {
        throw new Error(error.response?.data?.message || 'Failed to create user')
      }
    }
  }
})

