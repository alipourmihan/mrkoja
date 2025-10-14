import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

export const useBusinessStore = defineStore('business', {
  state: () => ({
    businesses: [],
    currentBusiness: null,
    loading: false,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    },
    filters: {
      category_id: null,
      search: '',
      sort_by: 'created_at',
      sort_order: 'desc'
    }
  }),

  getters: {
    featuredBusinesses: (state) => state.businesses.filter(business => business.is_featured),
    approvedBusinesses: (state) => state.businesses.filter(business => business.status === 'approved'),
  },

  actions: {
    async fetchBusinesses(params = {}) {
      this.loading = true
      try {
        const queryParams = { ...this.filters, ...params }
        const response = await axios.get(`${API_BASE_URL}/businesses`, { params: queryParams })
        
        // Handle different response structures safely
        if (response.data.businesses && response.data.businesses.data) {
          // Laravel pagination structure
          this.businesses = response.data.businesses.data
          this.pagination = {
            current_page: response.data.businesses.current_page,
            last_page: response.data.businesses.last_page,
            per_page: response.data.businesses.per_page,
            total: response.data.businesses.total
          }
        } else if (Array.isArray(response.data.businesses)) {
          // Direct array structure
          this.businesses = response.data.businesses
          this.pagination = {
            current_page: 1,
            last_page: 1,
            per_page: response.data.businesses.length,
            total: response.data.businesses.length
          }
        } else {
          // Fallback
          this.businesses = []
          this.pagination = {
            current_page: 1,
            last_page: 1,
            per_page: 12,
            total: 0
          }
        }
        
        return { success: true }
      } catch (error) {
        console.error('Error fetching businesses:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to fetch businesses' 
        }
      } finally {
        this.loading = false
      }
    },

    async fetchBusiness(id) {
      this.loading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/businesses/${id}`)
        this.currentBusiness = response.data.business
        return { success: true, business: response.data.business }
      } catch (error) {
        console.error('Error fetching business:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to fetch business' 
        }
      } finally {
        this.loading = false
      }
    },

    async createBusiness(businessData) {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        const response = await axios.post(`${API_BASE_URL}/businesses`, businessData, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        })
        this.businesses.unshift(response.data.business)
        return { success: true, business: response.data.business }
      } catch (error) {
        console.error('Error creating business:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to create business',
          errors: error.response?.data?.errors
        }
      } finally {
        this.loading = false
      }
    },

    async updateBusiness(id, businessData) {
      this.loading = true
      try {
        const response = await axios.put(`${API_BASE_URL}/businesses/${id}`, businessData)
        
        // Update in businesses array
        const index = this.businesses.findIndex(b => b.id === id)
        if (index !== -1) {
          this.businesses[index] = response.data.business
        }
        
        // Update current business if it's the same
        if (this.currentBusiness && this.currentBusiness.id === id) {
          this.currentBusiness = response.data.business
        }
        
        return { success: true, business: response.data.business }
      } catch (error) {
        console.error('Error updating business:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to update business',
          errors: error.response?.data?.errors
        }
      } finally {
        this.loading = false
      }
    },

    async deleteBusiness(id) {
      this.loading = true
      try {
        await axios.delete(`${API_BASE_URL}/businesses/${id}`)
        
        // Remove from businesses array
        this.businesses = this.businesses.filter(b => b.id !== id)
        
        // Clear current business if it's the same
        if (this.currentBusiness && this.currentBusiness.id === id) {
          this.currentBusiness = null
        }
        
        return { success: true }
      } catch (error) {
        console.error('Error deleting business:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to delete business' 
        }
      } finally {
        this.loading = false
      }
    },

    async fetchMyBusinesses() {
      this.loading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/my-businesses`)
        this.businesses = response.data.businesses.data
        this.pagination = {
          current_page: response.data.businesses.current_page,
          last_page: response.data.businesses.last_page,
          per_page: response.data.businesses.per_page,
          total: response.data.businesses.total
        }
        return { success: true }
      } catch (error) {
        console.error('Error fetching my businesses:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to fetch my businesses' 
        }
      } finally {
        this.loading = false
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        category_id: null,
        search: '',
        sort_by: 'created_at',
        sort_order: 'desc'
      }
    },

    setCurrentBusiness(business) {
      this.currentBusiness = business
    },

    clearCurrentBusiness() {
      this.currentBusiness = null
    }
  }
})
