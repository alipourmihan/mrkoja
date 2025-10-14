<template>
  <div class="min-h-screen bg-gray-50" @click="handlePageClick">
    <!-- Header Section -->
         <div class="bg-white border-b border-gray-200" @click.stop>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2 space-x-reverse">
            <li>
              <router-link to="/" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-500 text-sm font-medium mr-2">دسته‌بندی‌ها</span>
              </div>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-900 text-sm font-medium mr-2">{{ category?.name || 'دسته‌بندی' }}</span>
              </div>
            </li>
            <li v-if="currentCity">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-900 text-sm font-medium mr-2">{{ currentCity.name }}</span>
              </div>
            </li>
          </ol>
        </nav>

        <!-- Page Title -->
        <div class="flex items-center justify-between">
          <div>
            <SeoContent
              :seo="seoStore.seoData"
              :fallback="{ title: getPageTitle(), description: getPageDescription() }"
              :json-ld-data="seoStore.json_ld || {}"
            />
          </div>
          
          <!-- Location Selector -->
          <div class="flex items-center space-x-4 space-x-reverse" @click.stop>
            <div class="relative">
              <select 
                v-model="selectedProvince" 
                @change="onProvinceChange"
                @click.stop
                class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">انتخاب استان</option>
                <option v-for="province in provinces" :key="province.id" :value="province.id">
                  {{ province.name }}
                </option>
              </select>
              <div class="absolute left-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
            
            <div class="relative">
              <select 
                v-model="selectedCity" 
                @change="onCityChange"
                @click.stop
                :disabled="!selectedProvince"
                class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
              >
                <option value="">انتخاب شهر</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <div class="absolute left-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1" @click.stop>
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">فیلترها</h3>
            
            <!-- Sort By -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">مرتب‌سازی بر اساس</label>
              <select 
                v-model="sortBy" 
                @change="applyFilters"
                @click.stop
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="rating">امتیاز</option>
                <option value="created_at">جدیدترین</option>
                <option value="name">نام</option>
                <option value="review_count">تعداد نظرات</option>
              </select>
            </div>

            <!-- Features Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">ویژگی‌ها</label>
              <div class="space-y-2">
                <label v-for="feature in availableFeatures" :key="feature" class="flex items-center" @click.stop>
                  <input 
                    type="checkbox" 
                    :value="feature" 
                    v-model="selectedFeatures"
                    @change="applyFilters"
                    @click.stop
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="mr-2 text-sm text-gray-700">{{ feature }}</span>
                </label>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">حداقل امتیاز</label>
              <div class="space-y-2">
                <label v-for="rating in [4, 3, 2, 1]" :key="rating" class="flex items-center" @click.stop>
                  <input 
                    type="radio" 
                    :value="rating" 
                    v-model="minRating"
                    @change="applyFilters"
                    @click.stop
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                  >
                  <span class="mr-2 text-sm text-gray-700">{{ rating }}+ ستاره</span>
                </label>
              </div>
            </div>

            <!-- Clear Filters -->
            <button 
              @click="clearFilters"
              @click.stop
              class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors"
            >
              پاک کردن فیلترها
            </button>
          </div>
        </div>

        <!-- Business List -->
        <div class="lg:col-span-3">
          <!-- Results Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <p class="text-gray-600">
                {{ businesses.length }} کسب‌وکار یافت شد
                <span v-if="currentCity"> در {{ currentCity.name }}</span>
              </p>
            </div>
            
            <!-- View Toggle -->
            <div class="flex items-center space-x-2 space-x-reverse">
              <button 
                @click="viewMode = 'grid'"
                @click.stop
                :class="[
                  'p-2 rounded-lg',
                  viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
              </button>
              <button 
                @click="viewMode = 'list'"
                @click.stop
                :class="[
                  'p-2 rounded-lg',
                  viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
              <div class="flex items-center space-x-4 space-x-reverse mb-4">
                <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                <div class="flex-1">
                  <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
              </div>
              <div class="space-y-2">
                <div class="h-3 bg-gray-200 rounded"></div>
                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
              </div>
            </div>
          </div>

          <!-- Business Grid -->
          <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="business in businesses" 
              :key="business.id"
              class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow group cursor-pointer"
              @click="goToBusiness(business)"
            >
              <!-- Business Image -->
              <div class="aspect-w-16 aspect-h-9 rounded-t-lg overflow-hidden">
                <img 
                  v-if="business.images && business.images.length > 0"
                  :src="business.images[0]" 
                  :alt="business.name"
                  class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                  @error="handleImageError"
                >
                <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
                  <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
              </div>

              <!-- Business Info -->
              <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                    {{ business.name }}
                  </h3>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-900 mr-1">{{ business.rating || 0 }}</span>
                    <span class="text-sm text-gray-500">({{ business.review_count || 0 }})</span>
                  </div>
                </div>

                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                  {{ business.description }}
                </p>

                <div class="flex items-center text-sm text-gray-500 mb-3">
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <span>{{ business.address }}</span>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2 space-x-reverse">
                    <span v-if="business.features && business.features.length > 0" 
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ business.features[0] }}
                    </span>
                    <span v-if="business.features && business.features.length > 1" 
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      +{{ business.features.length - 1 }}
                    </span>
                  </div>
                  
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ formatDate(business.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Business List View -->
          <div v-else class="space-y-4">
            <div 
              v-for="business in businesses" 
              :key="business.id"
              class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow group cursor-pointer"
              @click="goToBusiness(business)"
            >
              <div class="flex p-6">
                <!-- Business Image -->
                <div class="flex-shrink-0">
                  <div class="w-20 h-20 rounded-lg overflow-hidden">
                    <img 
                      v-if="business.images && business.images.length > 0"
                      :src="business.images[0]" 
                      :alt="business.name"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      @error="handleImageError"
                    >
                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- Business Info -->
                <div class="flex-1 mr-4">
                  <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                      {{ business.name }}
                    </h3>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                      <span class="text-sm font-medium text-gray-900 mr-1">{{ business.rating || 0 }}</span>
                      <span class="text-sm text-gray-500">({{ business.review_count || 0 }})</span>
                    </div>
                  </div>

                  <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    {{ business.description }}
                  </p>

                  <div class="flex items-center text-sm text-gray-500 mb-3">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ business.address }}</span>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 space-x-reverse">
                      <span v-for="feature in (business.features || []).slice(0, 3)" 
                            :key="feature"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ feature }}
                      </span>
                      <span v-if="business.features && business.features.length > 3" 
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        +{{ business.features.length - 3 }}
                      </span>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-500">
                      <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>{{ formatDate(business.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!loading && businesses.length === 0" class="text-center py-12">
            <div class="text-gray-400 mb-4">
              <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">هیچ کسب‌وکاری یافت نشد</h3>
            <p class="text-gray-600">در حال حاضر هیچ کسب‌وکاری در این دسته‌بندی وجود ندارد.</p>
          </div>

          <!-- Pagination -->
          <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex items-center justify-center">
            <nav class="flex items-center space-x-2 space-x-reverse">
              <button 
                @click="goToPage(pagination.current_page - 1)"
                @click.stop
                :disabled="pagination.current_page <= 1"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                قبلی
              </button>
              
              <button 
                v-for="page in visiblePages" 
                :key="page"
                @click="goToPage(page)"
                @click.stop
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-lg',
                  page === pagination.current_page 
                    ? 'bg-blue-600 text-white' 
                    : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              
              <button 
                @click="goToPage(pagination.current_page + 1)"
                @click.stop
                :disabled="pagination.current_page >= pagination.last_page"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                بعدی
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSeoStore } from '@/stores/seo'
import axios from 'axios'
import SeoContent from '@/components/SeoContent.vue'

const route = useRoute()
const router = useRouter()
const seoStore = useSeoStore()

const API_BASE_URL = 'https://mrkoja.com/api'

// Reactive data
const loading = ref(true)
const viewMode = ref('grid')
const sortBy = ref('rating')
const selectedFeatures = ref([])
const minRating = ref(null)
const selectedProvince = ref('')
const selectedCity = ref('')
const provinces = ref([])
const cities = ref([])

// Computed
const businesses = computed(() => seoStore.businesses)
const pagination = computed(() => seoStore.pagination)
const category = computed(() => seoStore.currentCategory)
const currentCity = computed(() => seoStore.currentCity)

const availableFeatures = computed(() => {
  const features = new Set()
  businesses.value.forEach(business => {
    if (business.features) {
      business.features.forEach(feature => features.add(feature))
    }
  })
  return Array.from(features)
})

const visiblePages = computed(() => {
  if (!pagination.value) return []
  
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
const getPageTitle = () => {
  if (currentCity.value) {
    return `بهترین ${category.value?.name || 'کسب‌وکار'}‌های ${currentCity.value.name}`
  }
  return `بهترین ${category.value?.name || 'کسب‌وکار'}‌های ایران`
}

const getPageDescription = () => {
  if (currentCity.value) {
    return `لیست کامل ${category.value?.name || 'کسب‌وکار'}‌های ${currentCity.value.name} با امتیاز، نظرات و اطلاعات تماس`
  }
  return `لیست کامل ${category.value?.name || 'کسب‌وکار'}‌های ایران با امتیاز، نظرات و اطلاعات تماس`
}

const fetchProvinces = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces`)
    provinces.value = response.data.provinces
  } catch (error) {
    console.error('Error fetching provinces:', error)
  }
}

const fetchCities = async (provinceId) => {
  if (!provinceId) {
    cities.value = []
    return
  }
  
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces/${provinceId}/cities`)
    cities.value = response.data.cities
  } catch (error) {
    console.error('Error fetching cities:', error)
  }
}

const loadBusinesses = async () => {
  loading.value = true
  
  try {
    const { category, city, neighborhood } = route.params
    
    if (neighborhood) {
      await seoStore.fetchCategoryCityNeighborhoodBusinesses(category, city, neighborhood)
    } else if (city) {
      await seoStore.fetchCategoryCityBusinesses(category, city)
    } else {
      await seoStore.fetchCategoryBusinesses(category)
    }
    
    // Add sample images to businesses for testing
    if (businesses.value && businesses.value.length > 0) {
      const sampleImages = [
        'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
      ]
      
      businesses.value.forEach((business, index) => {
        if (!business.images || business.images.length === 0) {
          // Add 1-3 random images to each business
          const numImages = Math.floor(Math.random() * 3) + 1
          business.images = sampleImages.slice(0, numImages)
        }
      })
    }
  } catch (error) {
    console.error('Error loading businesses:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  // Implement filtering logic here
  console.log('Applying filters:', { sortBy: sortBy.value, features: selectedFeatures.value, minRating: minRating.value })
  loadBusinesses()
}

const clearFilters = () => {
  sortBy.value = 'rating'
  selectedFeatures.value = []
  minRating.value = null
  applyFilters()
}

const onProvinceChange = () => {
  selectedCity.value = ''
  fetchCities(selectedProvince.value)
  // Update URL and reload businesses
  updateLocationFilter()
}

const onCityChange = () => {
  // Update URL and reload businesses
  updateLocationFilter()
}

const updateLocationFilter = () => {
  const { category } = route.params
  let newPath = `/b/${category}`
  
  if (selectedCity.value) {
    const city = cities.value.find(c => c.id == selectedCity.value)
    if (city) {
      newPath += `/${city.slug}`
    }
  }
  
  router.push(newPath)
}

const goToBusiness = (business) => {
  router.push(`/business/${business.slug || business.id}`)
}

const goToPage = (page) => {
  // Implement pagination logic
  console.log('Going to page:', page)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fa-IR')
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  event.target.style.display = 'none'
}

const handlePageClick = (event) => {
  // This method is called when clicking on the page background
  // It can be used for debugging or additional functionality
  console.log('Page clicked:', event.target)
}

// Watchers
watch(() => route.params, loadBusinesses, { immediate: true })

// Lifecycle
onMounted(async () => {
  await fetchProvinces()
  loadBusinesses()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.aspect-h-9 {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
</style>
