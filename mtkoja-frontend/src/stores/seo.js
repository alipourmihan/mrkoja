import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE_URL = 'https://mrkoja.com/api'

export const useSeoStore = defineStore('seo', {
  state: () => ({
    // Business listings
    businesses: [],
    businessesLoading: false,
    businessesError: null,
    
    // Location data
    provinces: [],
    cities: [],
    neighborhoods: [],
    locationsLoading: false,
    
    // Current page data
    currentCategory: null,
    currentCity: null,
    currentProvince: null,
    currentNeighborhood: null,
    
    // SEO data
    seoData: {
      title: '',
      description: '',
      keywords: '',
      canonical: '',
      ogTitle: '',
      ogDescription: '',
      ogType: 'website',
      ogImage: ''
    },
    
    // Breadcrumbs
    breadcrumbs: [],
    json_ld: null,
    
    // Pagination
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0
    }
  }),

  getters: {
    hasBusinesses: (state) => state.businesses.length > 0,
    hasMorePages: (state) => state.pagination.current_page < state.pagination.last_page,
    currentLocation: (state) => {
      if (state.currentNeighborhood) {
        return `${state.currentNeighborhood.name}، ${state.currentCity.name}`
      }
      if (state.currentCity) {
        return state.currentCity.name
      }
      return 'ایران'
    }
  },

  actions: {
    // Fetch businesses by category (all country)
    async fetchCategoryBusinesses(categorySlug) {
      this.businessesLoading = true
      this.businessesError = null
      
      try {
        const response = await axios.get(`${API_BASE_URL}/b/${categorySlug}`)
        const data = response.data
        
        // Handle different response structures safely
        if (data.businesses && data.businesses.data) {
          this.businesses = data.businesses.data
        } else if (Array.isArray(data.businesses)) {
          this.businesses = data.businesses
        } else {
          this.businesses = []
        }
        this.currentCategory = data.category
        this.currentCity = null
        this.currentProvince = null
        this.currentNeighborhood = null
        this.seoData = data.seo
        this.breadcrumbs = data.breadcrumbs
        this.pagination = data.businesses.meta || { current_page: 1, last_page: 1, per_page: 20, total: this.businesses.length }
        this.json_ld = data.json_ld || null
        
        return { success: true, data }
      } catch (error) {
        this.businessesError = error.response?.data?.message || 'خطا در بارگذاری کسب‌وکارها'
        console.error('Error fetching category businesses:', error)
        return { success: false, message: this.businessesError }
      } finally {
        this.businessesLoading = false
      }
    },

    // Fetch businesses by category and city
    async fetchCategoryCityBusinesses(categorySlug, citySlug) {
      this.businessesLoading = true
      this.businessesError = null
      
      try {
        const response = await axios.get(`${API_BASE_URL}/b/${categorySlug}/${citySlug}`)
        const data = response.data
        
        // Handle different response structures safely
        if (data.businesses && data.businesses.data) {
          this.businesses = data.businesses.data
        } else if (Array.isArray(data.businesses)) {
          this.businesses = data.businesses
        } else {
          this.businesses = []
        }
        this.currentCategory = data.category
        this.currentCity = data.city
        this.currentProvince = data.province
        this.currentNeighborhood = null
        this.seoData = data.seo
        this.breadcrumbs = data.breadcrumbs
        this.pagination = data.businesses.meta || { current_page: 1, last_page: 1, per_page: 20, total: this.businesses.length }
        this.json_ld = data.json_ld || null
        
        return { success: true, data }
      } catch (error) {
        this.businessesError = error.response?.data?.message || 'خطا در بارگذاری کسب‌وکارها'
        console.error('Error fetching category city businesses:', error)
        return { success: false, message: this.businessesError }
      } finally {
        this.businessesLoading = false
      }
    },

    // Fetch businesses by category, city and neighborhood
    async fetchCategoryCityNeighborhoodBusinesses(categorySlug, citySlug, neighborhoodSlug) {
      this.businessesLoading = true
      this.businessesError = null
      
      try {
        const response = await axios.get(`${API_BASE_URL}/b/${categorySlug}/${citySlug}/${neighborhoodSlug}`)
        const data = response.data
        
        // Handle different response structures safely
        if (data.businesses && data.businesses.data) {
          this.businesses = data.businesses.data
        } else if (Array.isArray(data.businesses)) {
          this.businesses = data.businesses
        } else {
          this.businesses = []
        }
        this.currentCategory = data.category
        this.currentCity = data.city
        this.currentProvince = data.province
        this.currentNeighborhood = data.neighborhood
        this.seoData = data.seo
        this.breadcrumbs = data.breadcrumbs
        this.pagination = data.businesses.meta || { current_page: 1, last_page: 1, per_page: 20, total: this.businesses.length }
        this.json_ld = data.json_ld || null
        
        return { success: true, data }
      } catch (error) {
        this.businessesError = error.response?.data?.message || 'خطا در بارگذاری کسب‌وکارها'
        console.error('Error fetching category city neighborhood businesses:', error)
        return { success: false, message: this.businessesError }
      } finally {
        this.businessesLoading = false
      }
    },

    // Fetch business profile
    async fetchBusinessProfile(businessSlug) {
      this.businessesLoading = true
      this.businessesError = null
      
      try {
        const response = await axios.get(`${API_BASE_URL}/business/${businessSlug}`)
        const data = response.data
        
        this.businesses = [data.business] // Single business in array for consistency
        this.seoData = data.seo
        this.breadcrumbs = data.breadcrumbs
        
        return { success: true, business: data.business }
      } catch (error) {
        this.businessesError = error.response?.data?.message || 'خطا در بارگذاری کسب‌وکار'
        console.error('Error fetching business profile:', error)
        return { success: false, message: this.businessesError }
      } finally {
        this.businessesLoading = false
      }
    },

    // Fetch locations data
    async fetchProvinces() {
      this.locationsLoading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/provinces`)
        this.provinces = response.data.provinces || response.data
        return { success: true }
      } catch (error) {
        console.error('Error fetching provinces:', error)
        return { success: false, message: 'خطا در بارگذاری استان‌ها' }
      } finally {
        this.locationsLoading = false
      }
    },

    async fetchCities(provinceId) {
      this.locationsLoading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/provinces/${provinceId}/cities`)
        this.cities = response.data.cities || response.data
        return { success: true }
      } catch (error) {
        console.error('Error fetching cities:', error)
        return { success: false, message: 'خطا در بارگذاری شهرها' }
      } finally {
        this.locationsLoading = false
      }
    },

    async fetchNeighborhoods(cityId) {
      this.locationsLoading = true
      try {
        const response = await axios.get(`${API_BASE_URL}/cities/${cityId}/neighborhoods`)
        this.neighborhoods = response.data.neighborhoods || response.data
        return { success: true }
      } catch (error) {
        console.error('Error fetching neighborhoods:', error)
        return { success: false, message: 'خطا در بارگذاری محله‌ها' }
      } finally {
        this.locationsLoading = false
      }
    },

    // Clear current data
    clearData() {
      this.businesses = []
      this.currentCategory = null
      this.currentCity = null
      this.currentProvince = null
      this.currentNeighborhood = null
      this.seoData = {
        title: '',
        description: '',
        keywords: '',
        canonical: '',
        ogTitle: '',
        ogDescription: '',
        ogType: 'website',
        ogImage: ''
      }
      this.breadcrumbs = []
      this.pagination = {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
      }
    }
  }
})

