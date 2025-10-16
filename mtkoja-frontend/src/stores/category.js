import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE_URL = 'https://api.mrkoja.com/api'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    currentCategory: null,
    loading: false
  }),

  getters: {
    activeCategories: (state) => state.categories.filter(category => category.is_active),
    orderedCategories: (state) => state.categories.sort((a, b) => a.sort_order - b.sort_order),
  },

  actions: {
    async fetchCategories() {
      this.loading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/categories`)
        // Handle different response structures safely
        if (response.data.categories) {
          this.categories = response.data.categories
        } else if (Array.isArray(response.data)) {
          this.categories = response.data
        } else {
          this.categories = []
        }
        return { success: true }
      } catch (error) {
        console.error('Error fetching categories:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to fetch categories' 
        }
      } finally {
        this.loading = false
      }
    },

    async fetchCategory(id) {
      this.loading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/categories/${id}`)
        this.currentCategory = response.data.category
        return { success: true, category: response.data.category }
      } catch (error) {
        console.error('Error fetching category:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to fetch category' 
        }
      } finally {
        this.loading = false
      }
    },

    async createCategory(categoryData) {
      this.loading = true
      try {
        const response = await axios.post(`${API_BASE_URL}/categories`, categoryData)
        this.categories.push(response.data.category)
        return { success: true, category: response.data.category }
      } catch (error) {
        console.error('Error creating category:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to create category',
          errors: error.response?.data?.errors
        }
      } finally {
        this.loading = false
      }
    },

    async updateCategory(id, categoryData) {
      this.loading = true
      try {
        const response = await axios.put(`${API_BASE_URL}/categories/${id}`, categoryData)
        
        // Update in categories array
        const index = this.categories.findIndex(c => c.id === id)
        if (index !== -1) {
          this.categories[index] = response.data.category
        }
        
        // Update current category if it's the same
        if (this.currentCategory && this.currentCategory.id === id) {
          this.currentCategory = response.data.category
        }
        
        return { success: true, category: response.data.category }
      } catch (error) {
        console.error('Error updating category:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to update category',
          errors: error.response?.data?.errors
        }
      } finally {
        this.loading = false
      }
    },

    async deleteCategory(id) {
      this.loading = true
      try {
        await axios.delete(`${API_BASE_URL}/categories/${id}`)
        
        // Remove from categories array
        this.categories = this.categories.filter(c => c.id !== id)
        
        // Clear current category if it's the same
        if (this.currentCategory && this.currentCategory.id === id) {
          this.currentCategory = null
        }
        
        return { success: true }
      } catch (error) {
        console.error('Error deleting category:', error)
        return { 
          success: false, 
          message: error.response?.data?.message || 'Failed to delete category' 
        }
      } finally {
        this.loading = false
      }
    },

    setCurrentCategory(category) {
      this.currentCategory = category
    },

    clearCurrentCategory() {
      this.currentCategory = null
    }
  }
})
