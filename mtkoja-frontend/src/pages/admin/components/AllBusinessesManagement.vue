<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
        <h3 class="text-lg font-medium text-gray-900">تمام کسب‌وکارها</h3>
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="جستجو در کسب‌وکارها..."
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          >
          <select
            v-model="statusFilter"
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          >
            <option value="">همه وضعیت‌ها</option>
            <option value="approved">تایید شده</option>
            <option value="pending">در انتظار</option>
            <option value="rejected">رد شده</option>
          </select>
          <select
            v-model="categoryFilter"
            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          >
            <option value="">همه دسته‌ها</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>
    </div>
    <div class="p-6">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      <!-- Desktop Table View -->
      <div v-else-if="filteredBusinesses.length > 0" class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کسب‌وکار</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">دسته‌بندی</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">صاحب</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ ثبت</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="business in filteredBusinesses" :key="business.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12">
                    <img
                      v-if="business.images && business.images.length > 0 && getBusinessImageUrl(business.images[0])"
                      :src="getBusinessImageUrl(business.images[0])"
                      :alt="business.name"
                      class="h-12 w-12 rounded-lg object-cover"
                      @error="handleBusinessImageError"
                    >
                    <div v-else class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="text-sm font-medium text-gray-900">{{ business.name }}</div>
                    <div class="text-sm text-gray-500">{{ business.address }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ business.category.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ business.user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  business.status === 'approved' ? 'bg-green-100 text-green-800' :
                  business.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ getStatusLabel(business.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(business.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <BusinessActions
                  :business="business"
                  @view="viewBusiness"
                  @edit="editBusiness"
                  @approve="approveBusiness"
                  @reject="rejectBusiness"
                  @delete="deleteBusiness"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div v-else-if="filteredBusinesses.length > 0" class="lg:hidden space-y-4">
        <div v-for="business in filteredBusinesses" :key="business.id" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
          <div class="flex items-start space-x-4 space-x-reverse">
            <div class="flex-shrink-0 h-16 w-16">
              <img
                v-if="business.images && business.images.length > 0 && getBusinessImageUrl(business.images[0])"
                :src="getBusinessImageUrl(business.images[0])"
                :alt="business.name"
                class="h-16 w-16 rounded-lg object-cover"
                @error="handleBusinessImageError"
              >
              <div v-else class="h-16 w-16 rounded-lg bg-gray-300 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-900 truncate">{{ business.name }}</h4>
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  business.status === 'approved' ? 'bg-green-100 text-green-800' :
                  business.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ getStatusLabel(business.status) }}
                </span>
              </div>
              <p class="text-sm text-gray-500 mb-1">{{ business.category.name }}</p>
              <p class="text-xs text-gray-400 mb-2">{{ business.address }}</p>
              <p class="text-xs text-gray-500 mb-3">{{ business.user.name }} • {{ formatDate(business.created_at) }}</p>
              <div class="flex justify-end">
                <BusinessActions
                  :business="business"
                  @view="viewBusiness"
                  @edit="editBusiness"
                  @approve="approveBusiness"
                  @reject="rejectBusiness"
                  @delete="deleteBusiness"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500">
        هیچ کسب‌وکاری یافت نشد
      </div>
    </div>

    <!-- Business Details Modal -->
    <div v-if="showBusinessDetails" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-4 mx-auto p-4 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
          <div class="flex items-center justify-between pb-4 border-b sticky top-0 bg-white">
            <h3 class="text-lg font-medium text-gray-900">جزئیات کسب‌وکار</h3>
            <button @click="showBusinessDetails = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div v-if="selectedBusiness" class="mt-4 space-y-6">
            <!-- Business Images -->
            <div v-if="selectedBusiness.images && selectedBusiness.images.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر ({{ selectedBusiness.images.length }} تصویر)</label>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="(image, index) in selectedBusiness.images" :key="index" class="relative group">
                  <img
                    :src="image"
                    :alt="selectedBusiness.name"
                    class="h-24 w-full object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                    @click="openImageModal(image)"
                  >
                  <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Map Section -->
            <div v-if="selectedBusiness.latitude && selectedBusiness.longitude">
              <label class="block text-sm font-medium text-gray-700 mb-2">موقعیت مکانی</label>
              <div class="bg-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-gray-600">مختصات: {{ selectedBusiness.latitude }}, {{ selectedBusiness.longitude }}</span>
                  <a 
                    :href="`https://www.google.com/maps?q=${selectedBusiness.latitude},${selectedBusiness.longitude}`"
                    target="_blank"
                    class="text-blue-600 hover:text-blue-800 text-sm flex items-center"
                  >
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    مشاهده در نقشه
                  </a>
                </div>
                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                  <div class="text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-sm">نقشه تعاملی</p>
                    <p class="text-xs text-gray-400">برای مشاهده کامل روی لینک بالا کلیک کنید</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">نام کسب‌وکار</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">دسته‌بندی</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.category.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">آدرس</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.address }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">تلفن</label>
                <p class="mt-1 text-sm text-gray-900">
                  <a v-if="selectedBusiness.phone" :href="`tel:${selectedBusiness.phone}`" class="text-blue-600 hover:text-blue-800">
                    {{ selectedBusiness.phone }}
                  </a>
                  <span v-else class="text-gray-500">-</span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                <p class="mt-1 text-sm text-gray-900">
                  <a v-if="selectedBusiness.email" :href="`mailto:${selectedBusiness.email}`" class="text-blue-600 hover:text-blue-800">
                    {{ selectedBusiness.email }}
                  </a>
                  <span v-else class="text-gray-500">-</span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">وب‌سایت</label>
                <p class="mt-1 text-sm text-gray-900">
                  <a v-if="selectedBusiness.website" :href="selectedBusiness.website" target="_blank" class="text-blue-600 hover:text-blue-800">
                    {{ selectedBusiness.website }}
                  </a>
                  <span v-else class="text-gray-500">-</span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">وضعیت</label>
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  selectedBusiness.status === 'approved' ? 'bg-green-100 text-green-800' :
                  selectedBusiness.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ getStatusLabel(selectedBusiness.status) }}
                </span>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">تاریخ ثبت</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedBusiness.created_at) }}</p>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700">توضیحات</label>
              <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.description }}</p>
            </div>

            <!-- Features -->
            <div v-if="selectedBusiness.features && selectedBusiness.features.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-2">ویژگی‌ها</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="feature in selectedBusiness.features"
                  :key="feature"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                >
                  {{ feature }}
                </span>
              </div>
            </div>

            <!-- Working Hours -->
            <div v-if="selectedBusiness.working_hours">
              <label class="block text-sm font-medium text-gray-700 mb-2">ساعات کاری</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div
                  v-for="(hours, day) in selectedBusiness.working_hours"
                  :key="day"
                  class="flex justify-between text-sm"
                >
                  <span class="font-medium">{{ getDayLabel(day) }}</span>
                  <span>{{ hours }}</span>
                </div>
              </div>
            </div>

            <!-- Statistics -->
            <div v-if="selectedBusiness.reviews_count || selectedBusiness.views_count" class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">آمار</h4>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-if="selectedBusiness.reviews_count" class="text-center p-3 bg-blue-50 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ selectedBusiness.reviews_count }}</div>
                  <div class="text-xs text-blue-600">نظر</div>
                </div>
                <div v-if="selectedBusiness.views_count" class="text-center p-3 bg-green-50 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ selectedBusiness.views_count }}</div>
                  <div class="text-xs text-green-600">بازدید</div>
                </div>
                <div v-if="selectedBusiness.rating" class="text-center p-3 bg-yellow-50 rounded-lg">
                  <div class="text-2xl font-bold text-yellow-600">{{ selectedBusiness.rating.toFixed(1) }}</div>
                  <div class="text-xs text-yellow-600">امتیاز</div>
                </div>
                <div v-if="selectedBusiness.features && selectedBusiness.features.length" class="text-center p-3 bg-purple-50 rounded-lg">
                  <div class="text-2xl font-bold text-purple-600">{{ selectedBusiness.features.length }}</div>
                  <div class="text-xs text-purple-600">ویژگی</div>
                </div>
              </div>
            </div>

            <!-- Owner Info -->
            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">اطلاعات صاحب کسب‌وکار</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">نام</label>
                  <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.user.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                  <p class="mt-1 text-sm text-gray-900">
                    <a :href="`mailto:${selectedBusiness.user.email}`" class="text-blue-600 hover:text-blue-800">
                      {{ selectedBusiness.user.email }}
                    </a>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end mt-6 pt-4 border-t sticky bottom-0 bg-white">
            <button
              @click="showBusinessDetails = false"
              class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
            >
              بستن
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60">
      <div class="relative max-w-4xl max-h-full p-4">
        <button @click="showImageModal = false" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <img :src="selectedImage" :alt="selectedBusiness?.name" class="max-w-full max-h-full object-contain rounded-lg">
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import BusinessActions from '@/components/BusinessActions.vue'

const router = useRouter()

const API_BASE_URL = 'http://localhost:8000/api'

// State
const businesses = ref([])
const categories = ref([])
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')
const showBusinessDetails = ref(false)
const selectedBusiness = ref(null)
const showImageModal = ref(false)
const selectedImage = ref('')

// Computed
const filteredBusinesses = computed(() => {
  let filtered = businesses.value

  if (searchQuery.value) {
    filtered = filtered.filter(business =>
      business.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      business.address.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      business.user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(business => business.status === statusFilter.value)
  }

  if (categoryFilter.value) {
    filtered = filtered.filter(business => business.category_id == categoryFilter.value)
  }

  return filtered
})

// Methods
const fetchBusinesses = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/businesses`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    // Handle different response structures safely
    if (response.data.businesses && response.data.businesses.data) {
      businesses.value = response.data.businesses.data
    } else if (Array.isArray(response.data.businesses)) {
      businesses.value = response.data.businesses
    } else if (Array.isArray(response.data)) {
      businesses.value = response.data
    } else {
      businesses.value = []
    }
    console.log('Businesses loaded:', businesses.value)
    if (businesses.value.length > 0) {
      console.log('First business images:', businesses.value[0].images)
    }
  } catch (error) {
    console.error('Error fetching businesses:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در دریافت لیست کسب‌وکارها: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/categories`)
    // Handle different response structures safely
    if (response.data.categories) {
      categories.value = response.data.categories
    } else if (Array.isArray(response.data)) {
      categories.value = response.data
    } else {
      categories.value = []
    }
  } catch (error) {
    console.error('Error fetching categories:', error)
    console.error('Error response:', error.response?.data)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      console.error('خطا: سرور در دسترس نیست')
    }
  }
}

const getStatusLabel = (status) => {
  const labels = {
    'approved': 'تایید شده',
    'pending': 'در انتظار',
    'rejected': 'رد شده'
  }
  return labels[status] || status
}

const getDayLabel = (day) => {
  const labels = {
    'monday': 'دوشنبه',
    'tuesday': 'سه‌شنبه',
    'wednesday': 'چهارشنبه',
    'thursday': 'پنج‌شنبه',
    'friday': 'جمعه',
    'saturday': 'شنبه',
    'sunday': 'یکشنبه'
  }
  return labels[day] || day
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fa-IR')
}

const viewBusiness = (business) => {
  selectedBusiness.value = business
  showBusinessDetails.value = true
}

const openImageModal = (imageUrl) => {
  selectedImage.value = imageUrl
  showImageModal.value = true
}

const getBusinessImageUrl = (imagePath) => {
  if (!imagePath) return null
  
  // اگر imagePath یک object است، path را استخراج کن
  if (typeof imagePath === 'object' && imagePath.path) {
    imagePath = imagePath.path
  }
  
  // اگر imagePath یک string نیست، null برگردان
  if (typeof imagePath !== 'string') {
    return null
  }
  
  // اگر imagePath یک URL کامل است
  if (imagePath.startsWith('http')) {
    return imagePath
  }
  // اگر imagePath فقط نام فایل است
  return `http://localhost:8000/storage/businesses/${imagePath}`
}

const handleBusinessImageError = (event) => {
  // اگر تصویر لود نشد، به fallback برو
  event.target.style.display = 'none'
  const parent = event.target.parentElement
  if (parent) {
    parent.innerHTML = '<div class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>'
  }
}

    const approveBusiness = async (id) => {
      if (!confirm('آیا از تایید این کسب‌وکار اطمینان دارید؟')) return
      
      try {
        const token = localStorage.getItem('token')
        const response = await axios.put(`${API_BASE_URL}/admin/businesses/${id}/approve`, {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        
        // Update the business in the local array
        const businessIndex = businesses.value.findIndex(b => b.id === id)
        if (businessIndex !== -1) {
          businesses.value[businessIndex].status = 'approved'
        }
        
        alert('کسب‌وکار با موفقیت تایید شد')
      } catch (error) {
        console.error('Error approving business:', error)
        alert('خطا در تایید کسب‌وکار: ' + (error.response?.data?.message || error.message))
      }
    }

    const rejectBusiness = async (id) => {
      if (!confirm('آیا از رد این کسب‌وکار اطمینان دارید؟')) return
      
      try {
        const token = localStorage.getItem('token')
        const response = await axios.put(`${API_BASE_URL}/admin/businesses/${id}/reject`, {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        
        // Update the business in the local array
        const businessIndex = businesses.value.findIndex(b => b.id === id)
        if (businessIndex !== -1) {
          businesses.value[businessIndex].status = 'rejected'
        }
        
        alert('کسب‌وکار رد شد')
      } catch (error) {
        console.error('Error rejecting business:', error)
        alert('خطا در رد کسب‌وکار: ' + (error.response?.data?.message || error.message))
      }
    }

const editBusiness = (business) => {
  // Navigate to admin edit page
  router.push(`/admin/business/${business.id}/edit`)
}

    const deleteBusiness = async (id) => {
      if (!confirm('آیا از حذف این کسب‌وکار اطمینان دارید؟')) return
      
      try {
        const token = localStorage.getItem('token')
        await axios.delete(`${API_BASE_URL}/admin/businesses/${id}`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        
        // Remove from local array
        businesses.value = businesses.value.filter(b => b.id !== id)
        
        alert('کسب‌وکار با موفقیت حذف شد')
      } catch (error) {
        console.error('Error deleting business:', error)
        alert('خطا در حذف کسب‌وکار: ' + (error.response?.data?.message || error.message))
      }
    }

onMounted(() => {
  fetchBusinesses()
  fetchCategories()
})
</script>
