<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
      <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">همه کسب‌وکارها</h1>
            <p class="text-gray-600 mt-1">کشف بهترین کسب‌وکارها در اطراف شما</p>
          </div>
          <div class="text-sm text-gray-500">
            {{ totalBusinesses }} کسب‌وکار یافت شد
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b border-gray-200">
      <div class="container mx-auto px-4 py-4">
        <div class="flex flex-wrap gap-4 items-center">
          <!-- Search -->
          <div class="flex-1 min-w-64">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="جستجو در کسب‌وکارها..."
                class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                @input="debouncedSearch"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Category Filter -->
          <div class="min-w-48">
            <select
              v-model="selectedCategory"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              @change="filterBusinesses"
            >
              <option value="">همه دسته‌بندی‌ها</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- Sort -->
          <div class="min-w-32">
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              @change="filterBusinesses"
            >
              <option value="created_at">جدیدترین</option>
              <option value="rating">بهترین امتیاز</option>
              <option value="name">نام</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="container mx-auto px-4 py-8">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
        <p class="text-gray-600 mt-2">در حال بارگذاری کسب‌وکارها...</p>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="container mx-auto px-4 py-8">
      <div class="text-center">
        <div class="text-red-500 text-lg mb-2">خطا در بارگذاری</div>
        <p class="text-gray-600">{{ error }}</p>
        <button
          @click="loadBusinesses"
          class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        >
          تلاش مجدد
        </button>
      </div>
    </div>

    <!-- Businesses Grid -->
    <div v-else-if="businesses.length > 0" class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="business in businesses"
          :key="business.id"
          class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToBusiness(business.id)"
        >
          <!-- Business Image -->
          <div class="h-48 bg-gray-200 rounded-t-2xl overflow-hidden">
            <img
              v-if="getBusinessImage(business)"
              :src="getBusinessImage(business)"
              :alt="business.name"
              class="w-full h-full object-cover"
              @error="handleImageError"
            >
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>

          <!-- Business Info -->
          <div class="p-6">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ business.name }}</h3>
              <div v-if="business.is_featured" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                ویژه
              </div>
            </div>

            <p class="text-sm text-gray-500 mb-2">{{ business.category?.name || 'دسته‌بندی نامشخص' }}</p>
            <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ business.description || 'توضیحات موجود نیست' }}</p>

            <!-- Rating & Location -->
            <div class="flex items-center justify-between text-sm">
              <div class="flex items-center text-yellow-500">
                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-gray-700">{{ business.rating || '0' }}</span>
              </div>
              <div class="text-gray-500 truncate max-w-32">
                {{ business.address || 'آدرس موجود نیست' }}
              </div>
            </div>

            <!-- Contact Info -->
            <div v-if="business.phone" class="mt-3 pt-3 border-t border-gray-100">
              <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <span class="truncate">{{ business.phone }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2 space-x-reverse">
          <button
            v-if="pagination.current_page > 1"
            @click="goToPage(pagination.current_page - 1)"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            قبلی
          </button>
          
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-2 text-sm font-medium rounded-md',
              page === pagination.current_page
                ? 'bg-red-600 text-white'
                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          
          <button
            v-if="pagination.current_page < pagination.last_page"
            @click="goToPage(pagination.current_page + 1)"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            بعدی
          </button>
        </nav>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="container mx-auto px-4 py-16">
      <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">هیچ کسب‌وکاری یافت نشد</h3>
        <p class="mt-1 text-sm text-gray-500">لطفاً فیلترهای جستجو را تغییر دهید</p>
        <div class="mt-6">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
          >
            پاک کردن فیلترها
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCategoryStore } from '@/stores/category'

const router = useRouter()
const categoryStore = useCategoryStore()

// State
const businesses = ref([])
const categories = ref([])
const loading = ref(false)
const error = ref(null)
const searchQuery = ref('')
const selectedCategory = ref('')
const sortBy = ref('created_at')
const pagination = ref(null)
const totalBusinesses = ref(0)

const API_BASE_URL = 'https://mrkoja.com/api'

// Computed
const visiblePages = computed(() => {
  if (!pagination.value) return []
  
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  // Show 5 pages around current page
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const loadBusinesses = async (page = 1) => {
  loading.value = true
  error.value = null
  
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      sort_by: sortBy.value,
      sort_order: 'desc'
    })
    
    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }
    
    if (selectedCategory.value) {
      params.append('category_id', selectedCategory.value)
    }
    
    const response = await axios.get(`${API_BASE_URL}/businesses?${params}`)
    
    console.log('API Response:', response.data)
    console.log('Response structure:', {
      data: response.data.data,
      businesses: response.data.businesses,
      meta: response.data.meta,
      pagination: response.data.pagination
    })
    
    // Handle different response structures safely
    if (response.data.businesses && response.data.businesses.data) {
      // Laravel pagination structure
      businesses.value = response.data.businesses.data
      pagination.value = response.data.businesses
      totalBusinesses.value = response.data.businesses.total || response.data.businesses.data.length
    } else if (Array.isArray(response.data.businesses)) {
      // Direct businesses array
      businesses.value = response.data.businesses
      pagination.value = response.data.pagination || null
      totalBusinesses.value = response.data.businesses.length
    } else if (response.data.data && Array.isArray(response.data.data)) {
      // Laravel pagination structure (alternative)
      businesses.value = response.data.data
      pagination.value = response.data.meta
      totalBusinesses.value = response.data.meta?.total || response.data.data.length
    } else if (Array.isArray(response.data)) {
      // Direct array response
      businesses.value = response.data
      pagination.value = null
      totalBusinesses.value = response.data.length
    } else {
      // Fallback
      businesses.value = []
      pagination.value = null
      totalBusinesses.value = 0
    }
    
    console.log('Final businesses:', businesses.value)
    console.log('Final businesses length:', businesses.value?.length)
    console.log('Final pagination:', pagination.value)
    console.log('Total businesses:', totalBusinesses.value)
    
  } catch (err) {
    console.error('Error loading businesses:', err)
    console.error('Error response:', err.response?.data)
    console.error('Error status:', err.response?.status)
    
    // Try to load from Home page data as fallback
    try {
      console.log('Trying fallback: loading from Home page...')
      const homeResponse = await axios.get(`${API_BASE_URL}/businesses`)
      console.log('Home fallback response:', homeResponse.data)
      
      if (homeResponse.data && Array.isArray(homeResponse.data)) {
        businesses.value = homeResponse.data
        totalBusinesses.value = homeResponse.data.length
        console.log('Fallback successful, loaded:', homeResponse.data.length, 'businesses')
      } else {
        throw new Error('Fallback also failed')
      }
    } catch (fallbackErr) {
      console.error('Fallback also failed:', fallbackErr)
      error.value = 'خطا در بارگذاری کسب‌وکارها: ' + (err.response?.data?.message || err.message)
    }
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/categories`)
    categories.value = response.data.categories || response.data || []
  } catch (err) {
    console.error('Error loading categories:', err)
  }
}

const filterBusinesses = () => {
  loadBusinesses(1)
}

const goToPage = (page) => {
  loadBusinesses(page)
}

const goToBusiness = (businessId) => {
  router.push(`/business/${businessId}`)
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  sortBy.value = 'created_at'
  loadBusinesses(1)
}

const getBusinessImage = (business) => {
  if (business.image_urls && business.image_urls.length > 0) {
    return business.image_urls[0].url
  }
  if (business.images && business.images.length > 0) {
    const imageUrl = business.images[0]
    if (imageUrl.startsWith('http')) {
      return imageUrl
    } else if (imageUrl.startsWith('/storage/')) {
      return `https://mrkoja.com${imageUrl}`
    } else {
      return `https://mrkoja.com/storage/${imageUrl}`
    }
  }
  return null
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filterBusinesses()
  }, 500)
}

// Watch for changes
watch([searchQuery, selectedCategory, sortBy], () => {
  filterBusinesses()
})

// Lifecycle
onMounted(async () => {
  console.log('AllBusinesses component mounted')
  console.log('API_BASE_URL:', API_BASE_URL)
  
  await loadCategories()
  console.log('Categories loaded:', categories.value.length)
  
  await loadBusinesses()
  console.log('Businesses loaded:', businesses.value.length)
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
