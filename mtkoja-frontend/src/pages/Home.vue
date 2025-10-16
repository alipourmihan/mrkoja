<template>
  <div class="min-h-screen bg-white">
    <!-- SEO Business List for category routes -->
    <SeoBusinessList 
      v-if="isSeoRoute" 
      :type="seoRouteType" 
    />
    
    <!-- Regular Home Page -->
    <div v-else class="pb-20 md:pb-0">
      <!-- Header Section -->
      <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="flex items-center">
              <h1 class="text-2xl font-bold">
                <span class="text-red-500">مت</span><span class="text-gray-900">کجا</span>
              </h1>
            </div>
            
            <!-- Location & Notifications -->
            <div class="flex items-center space-x-4 space-x-reverse">
              <!-- Location -->
              <div class="flex items-center text-gray-700">
                <span class="text-sm font-medium">تهران</span>
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
              
              <!-- Notifications -->
              <div class="relative">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">1</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Hero Section -->
      <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Tagline -->
          <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              پیدا کردن بعدی شما فقط یک جستجو فاصله دارد
            </h2>
          </div>
          
          <!-- Search Bar -->
          <div class="max-w-2xl mx-auto mb-8">
            <div class="relative">
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="جستجو برای کسب‌وکارها..."
                class="w-full pr-10 pl-4 py-4 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right"
                @keyup.enter="searchBusinesses"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                <button class="text-gray-400 hover:text-gray-600">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Categories Section -->
      <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-3 gap-4 mb-6">
            <!-- Real Estate -->
            <div 
              @click="filterByCategory('real-estate')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">🏠</div>
              <h3 class="font-semibold text-gray-900 text-sm">املاک</h3>
            </div>
            
            <!-- Motors -->
            <div 
              @click="filterByCategory('motors')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">🚗</div>
              <h3 class="font-semibold text-gray-900 text-sm">خودرو</h3>
            </div>
            
            <!-- Jobs -->
            <div 
              @click="filterByCategory('jobs')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">💼</div>
              <h3 class="font-semibold text-gray-900 text-sm">شغل</h3>
            </div>
          </div>
          
          <div class="grid grid-cols-3 gap-4">
            <!-- Classifieds -->
            <div 
              @click="filterByCategory('classifieds')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">📢</div>
              <h3 class="font-semibold text-gray-900 text-sm">آگهی</h3>
            </div>
            
            <!-- Furniture -->
            <div 
              @click="filterByCategory('furniture')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">🛋️</div>
              <h3 class="font-semibold text-gray-900 text-sm">مبلمان</h3>
            </div>
            
            <!-- Community -->
            <div 
              @click="filterByCategory('community')"
              class="bg-gray-100 rounded-2xl p-6 text-center hover:bg-gray-200 transition-colors cursor-pointer"
            >
              <div class="text-4xl mb-3">👥</div>
              <h3 class="font-semibold text-gray-900 text-sm">اجتماعی</h3>
            </div>
          </div>
        </div>
      </section>

      <!-- What's New Section -->
      <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">چه چیز جدیدی در متکجا</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Featured Card 1 -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
              <div class="relative h-48 bg-gradient-to-br from-blue-400 to-blue-600">
                <div class="absolute top-4 right-4">
                  <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                    کسب‌وکار
                  </span>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="text-white text-center">
                    <div class="text-6xl mb-2">🏢</div>
                    <p class="text-lg font-semibold">کسب‌وکارهای جدید</p>
                  </div>
                </div>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">کشف پروژه‌های جدید</h3>
                <p class="text-gray-600">بهترین کسب‌وکارها و خدمات جدید را کشف کنید</p>
              </div>
            </div>
            
            <!-- Featured Card 2 -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
              <div class="relative h-48 bg-gradient-to-br from-green-400 to-green-600">
                <div class="absolute top-4 right-4">
                  <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                    خدمات
                  </span>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="text-white text-center">
                    <div class="text-6xl mb-2">🤝</div>
                    <p class="text-lg font-semibold">ارتباط با ارائه‌دهندگان</p>
                  </div>
                </div>
              </div>
              <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">ارتباط با ارائه‌دهندگان</h3>
                <p class="text-gray-600">مستقیماً با بهترین ارائه‌دهندگان خدمات ارتباط برقرار کنید</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular Businesses Section -->
      <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">محبوب در کسب‌وکار</h2>
            <router-link to="/businesses" class="text-red-500 font-semibold text-sm">
              مشاهده همه
            </router-link>
          </div>

          <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>

          <div v-else-if="featuredBusinesses.length > 0" class="space-y-4">
            <router-link
              v-for="business in featuredBusinesses.slice(0, 3)"
              :key="business.id"
              :to="`/business/${business.id}`"
              class="flex items-center bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
            >
              <!-- Business Image -->
              <div class="w-20 h-20 bg-gray-200 rounded-xl overflow-hidden flex-shrink-0 ml-4">
                <img 
                  v-if="getBusinessImage(business)"
                  :src="getBusinessImage(business)" 
                  :alt="business.name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                >
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>
              
              <!-- Business Info -->
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ business.name }}</h3>
                <p class="text-sm text-gray-500 truncate">{{ business.category?.name || 'دسته‌بندی نامشخص' }}</p>
                <p class="text-sm text-gray-500 truncate">{{ business.description || 'توضیحات موجود نیست' }}</p>
              </div>
              
              <!-- Time & Price -->
              <div class="text-left flex-shrink-0">
                <div class="text-sm text-gray-500 mb-1">الان</div>
                <div class="text-lg font-bold text-gray-900">امتیاز: {{ business.rating || '0' }}</div>
              </div>
            </router-link>
          </div>

          <div v-else class="text-center py-8">
            <p class="text-gray-500">هیچ کسب‌وکاری یافت نشد</p>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import SeoBusinessList from '@/components/SeoBusinessList.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ref([])
const featuredBusinesses = ref([])
const stats = ref({})
const loading = ref(false)

const API_BASE_URL = 'https://api.mrkoja.com/api'

// SEO Route Detection
const isSeoRoute = computed(() => {
  return route.name === 'CategoryList' || 
         route.name === 'CategoryCityList' || 
         route.name === 'CategoryCityNeighborhoodList'
})

const seoRouteType = computed(() => {
  if (route.name === 'CategoryList') return 'category'
  if (route.name === 'CategoryCityList') return 'category-city'
  if (route.name === 'CategoryCityNeighborhoodList') return 'category-city-neighborhood'
  return 'category'
})

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/categories`)
    categories.value = response.data.categories
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchFeaturedBusinesses = async () => {
  loading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/businesses`, {
      params: {
        sort_by: 'rating',
        sort_order: 'desc',
        per_page: 6
      }
    })
    
    // Handle different response structures safely
    if (response.data.businesses && response.data.businesses.data) {
      featuredBusinesses.value = response.data.businesses.data
    } else if (Array.isArray(response.data.businesses)) {
      featuredBusinesses.value = response.data.businesses
    } else {
      featuredBusinesses.value = []
    }
  } catch (error) {
    console.error('Error fetching businesses:', error)
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    // Only fetch stats if user is admin
    if (authStore.isAdmin) {
      const token = localStorage.getItem('token')
      const response = await axios.get(`${API_BASE_URL}/admin/stats`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      stats.value = response.data.stats
    }
  } catch (error) {
    console.error('Error fetching stats:', error)
    // Don't show error for non-admin users
  }
}

const searchBusinesses = () => {
  const params = new URLSearchParams()
  if (searchQuery.value) params.append('search', searchQuery.value)
  if (selectedCategory.value) params.append('category_id', selectedCategory.value)
  
  router.push(`/businesses?${params.toString()}`)
}

const filterByCategory = (categoryId) => {
  // Find category by ID to get slug
  const category = categories.value.find(c => c.id === categoryId)
  if (category) {
    router.push(`/b/${category.slug}`)
  } else {
    router.push(`/businesses?category_id=${categoryId}`)
  }
}

const getBusinessImage = (business) => {
  // Check for image_urls first (new format)
  if (business.image_urls && business.image_urls.length > 0) {
    const imageUrl = business.image_urls[0].url
    if (imageUrl.startsWith('http')) {
      return imageUrl
    } else if (imageUrl.startsWith('/storage/')) {
      return `https://mrkoja.com${imageUrl}`
    } else {
      return `https://api.mrkoja.com/storage/${imageUrl}`
    }
  }
  
  // Check for images (old format)
  if (business.images && business.images.length > 0) {
    // Handle both relative and absolute URLs
    const imageUrl = business.images[0]
    if (imageUrl.startsWith('http')) {
      return imageUrl
    } else if (imageUrl.startsWith('/storage/')) {
      return `https://mrkoja.com${imageUrl}`
    } else {
      return `https://api.mrkoja.com/storage/${imageUrl}`
    }
  }
  
  // Return null if no images
  return null
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  event.target.style.display = 'none'
}

onMounted(async () => {
  // Initialize auth first
  authStore.initializeAuth()
  
  // Then fetch data
  await fetchCategories()
  await fetchFeaturedBusinesses()
  await fetchStats()
})
</script>
