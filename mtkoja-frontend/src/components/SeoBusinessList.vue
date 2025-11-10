<template>
  <div class="seo-business-list">
    <!-- SEO Head -->
    <Head>
      <title>{{ seoData.title || 'لیست کسب‌وکارها' }}</title>
      <meta name="description" :content="seoData.description" />
      <meta name="keywords" :content="seoData.keywords" />
      <link rel="canonical" :href="seoData.canonical" />
      
      <!-- Open Graph -->
      <meta property="og:title" :content="seoData.ogTitle || seoData.title" />
      <meta property="og:description" :content="seoData.ogDescription || seoData.description" />
      <meta property="og:type" :content="seoData.ogType" />
      <meta property="og:image" :content="seoData.ogImage" />
      <meta property="og:url" :content="seoData.canonical" />
    </Head>

    <!-- Breadcrumbs -->
    <nav class="breadcrumbs bg-gray-50 py-3 px-4 border-b">
      <div class="max-w-7xl mx-auto">
        <ol class="flex items-center space-x-2 space-x-reverse text-sm">
          <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
            <router-link 
              v-if="index < breadcrumbs.length - 1"
              :to="crumb.url" 
              class="text-blue-600 hover:text-blue-800 transition-colors"
            >
              {{ crumb.name }}
            </router-link>
            <span v-else class="text-gray-500 font-medium">
              {{ crumb.name }}
            </span>
            <svg v-if="index < breadcrumbs.length - 1" class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </li>
        </ol>
      </div>
    </nav>

    <!-- Header -->
    <div class="bg-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            {{ seoData.title || 'لیست کسب‌وکارها' }}
          </h1>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            {{ seoData.description }}
          </p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center space-x-4 space-x-reverse">
            <span class="text-sm font-medium text-gray-700">
              {{ businesses.length }} کسب‌وکار در {{ currentLocation }}
            </span>
          </div>
          
          <div class="flex items-center space-x-4 space-x-reverse">
            <select v-model="sortBy" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
              <option value="rating">بر اساس امتیاز</option>
              <option value="newest">جدیدترین</option>
              <option value="name">نام</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="businessesLoading" class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="businessesError" class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="text-red-600 mb-4">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">خطا در بارگذاری</h3>
          <p class="text-gray-600">{{ businessesError }}</p>
        </div>
      </div>
    </div>

    <!-- Business List -->
    <div v-else-if="hasBusinesses" class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="business in businesses" 
            :key="business.id"
            class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden"
          >
            <!-- Business Image -->
            <div class="h-48 bg-gray-200 relative">
              <img 
                v-if="business.primary_image" 
                :src="business.primary_image" 
                :alt="business.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
              </div>
              
              <!-- Featured Badge -->
              <div v-if="business.is_featured" class="absolute top-2 right-2">
                <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                  ویژه
                </span>
              </div>
            </div>

            <!-- Business Info -->
            <div class="p-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ business.name }}
              </h3>
              
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                {{ business.description }}
              </p>

              <!-- Rating -->
              <div class="flex items-center mb-3">
                <div class="flex items-center">
                  <svg 
                    v-for="i in 5" 
                    :key="i"
                    class="w-4 h-4"
                    :class="i <= Math.round(business.average_rating) ? 'text-yellow-400' : 'text-gray-300'"
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="text-sm text-gray-600 mr-2">
                  {{ business.average_rating.toFixed(1) }} ({{ business.review_count }} نظر)
                </span>
              </div>

              <!-- Location -->
              <div class="flex items-center text-sm text-gray-500 mb-3">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <span>{{ business.city?.name }}, {{ business.province?.name }}</span>
              </div>

              <!-- Features -->
              <div v-if="business.features && business.features.length > 0" class="mb-4">
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="feature in business.features.slice(0, 3)" 
                    :key="feature.id"
                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                  >
                    {{ feature.name }}
                  </span>
                  <span v-if="business.features.length > 3" class="text-xs text-gray-500">
                    +{{ business.features.length - 3 }} بیشتر
                  </span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-between">
                <router-link 
                  :to="`/business/${business.slug}`"
                  class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-700 transition-colors"
                >
                  مشاهده جزئیات
                </router-link>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                  <button class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button class="text-gray-400 hover:text-blue-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center space-x-2 space-x-reverse">
            <button 
              v-if="pagination.current_page > 1"
              @click="loadPage(pagination.current_page - 1)"
              class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              قبلی
            </button>
            
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="loadPage(page)"
              class="px-3 py-2 text-sm font-medium rounded-md"
              :class="page === pagination.current_page 
                ? 'bg-primary-600 text-white' 
                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'"
            >
              {{ page }}
            </button>
            
            <button 
              v-if="pagination.current_page < pagination.last_page"
              @click="loadPage(pagination.current_page + 1)"
              class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              بعدی
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">هیچ کسب‌وکاری یافت نشد</h3>
          <p class="text-gray-600">در حال حاضر هیچ کسب‌وکاری در این دسته‌بندی وجود ندارد.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useSeoStore } from '@/stores/seo'

const route = useRoute()
const seoStore = useSeoStore()

// Props
const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['category', 'category-city', 'category-city-neighborhood'].includes(value)
  }
})

// Reactive data
const sortBy = ref('rating')

// Computed
const businesses = computed(() => seoStore.businesses)
const businessesLoading = computed(() => seoStore.businessesLoading)
const businessesError = computed(() => seoStore.businessesError)
const seoData = computed(() => seoStore.seoData)
const breadcrumbs = computed(() => seoStore.breadcrumbs)
const pagination = computed(() => seoStore.pagination)
const currentLocation = computed(() => seoStore.currentLocation)

const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const loadBusinesses = async () => {
  const { category, city, neighborhood } = route.params
  
  switch (props.type) {
    case 'category':
      await seoStore.fetchCategoryBusinesses(category)
      break
    case 'category-city':
      await seoStore.fetchCategoryCityBusinesses(category, city)
      break
    case 'category-city-neighborhood':
      await seoStore.fetchCategoryCityNeighborhoodBusinesses(category, city, neighborhood)
      break
  }
}

const loadPage = async (page) => {
  // Implementation for pagination
  console.log('Loading page:', page)
  // You can implement pagination logic here
}

// Watchers
watch(() => route.params, loadBusinesses, { immediate: true })
watch(sortBy, () => {
  // Implement sorting logic
  console.log('Sorting by:', sortBy.value)
})

// Lifecycle
onMounted(() => {
  loadBusinesses()
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

