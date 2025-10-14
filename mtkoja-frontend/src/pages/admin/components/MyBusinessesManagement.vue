<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">کسب‌وکارهای من</h3>
      <button 
        @click="$router.push('/business/new')"
        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
      >
        اضافه کردن کسب‌وکار جدید
      </button>
    </div>
    <div class="p-6">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      <div v-else-if="businesses.length > 0" class="space-y-4">
        <div
          v-for="business in businesses"
          :key="business.id"
          class="border border-gray-200 rounded-lg p-4"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-4 space-x-reverse">
                <div v-if="business.images && business.images.length > 0" class="flex-shrink-0">
                  <img
                    :src="business.images[0]"
                    :alt="business.name"
                    class="h-16 w-16 rounded-lg object-cover"
                  >
                </div>
                <div v-else class="flex-shrink-0">
                  <div class="h-16 w-16 rounded-lg bg-gray-300 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <h4 class="text-lg font-semibold text-gray-900">{{ business.name }}</h4>
                  <p class="text-sm text-gray-600">{{ business.category.name }}</p>
                  <p class="text-sm text-gray-500 mt-1">{{ business.description.substring(0, 100) }}...</p>
                  <div class="flex items-center space-x-4 space-x-reverse mt-2">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      business.status === 'approved' ? 'bg-green-100 text-green-800' :
                      business.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-red-100 text-red-800'
                    ]">
                      {{ getStatusLabel(business.status) }}
                    </span>
                    <span class="text-xs text-gray-500">
                      {{ formatDate(business.created_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-2 space-x-reverse">
              <button
                @click="viewBusiness(business)"
                class="text-blue-600 hover:text-blue-900 text-sm"
              >
                مشاهده
              </button>
              <button
                @click="editBusiness(business)"
                class="text-yellow-600 hover:text-yellow-900 text-sm"
              >
                ویرایش
              </button>
              <button
                @click="deleteBusiness(business.id)"
                class="text-red-600 hover:text-red-900 text-sm"
              >
                حذف
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">هیچ کسب‌وکاری ندارید</h3>
        <p class="text-gray-500 mb-4">اولین کسب‌وکار خود را اضافه کنید</p>
        <button 
          @click="$router.push('/business/new')"
          class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
        >
          اضافه کردن کسب‌وکار جدید
        </button>
      </div>
    </div>

    <!-- Business Details Modal -->
    <div v-if="showBusinessDetails" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between pb-4 border-b">
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
              <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر</label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <img
                  v-for="(image, index) in selectedBusiness.images"
                  :key="index"
                  :src="image"
                  :alt="selectedBusiness.name"
                  class="h-24 w-full object-cover rounded-lg"
                >
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
                <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.phone }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedBusiness.email || '-' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">وب‌سایت</label>
                <p class="mt-1 text-sm text-gray-900">
                  <a v-if="selectedBusiness.website" :href="selectedBusiness.website" target="_blank" class="text-blue-600 hover:text-blue-800">
                    {{ selectedBusiness.website }}
                  </a>
                  <span v-else>-</span>
                </p>
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
          </div>
          
          <div class="flex justify-end mt-6 pt-4 border-t">
            <button
              @click="showBusinessDetails = false"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
            >
              بستن
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const API_BASE_URL = 'https://mrkoja.com/api'

// State
const businesses = ref([])
const loading = ref(false)
const showBusinessDetails = ref(false)
const selectedBusiness = ref(null)

// Methods
const fetchMyBusinesses = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/my-businesses`, {
      headers: {
        'Authorization': `Bearer ${token}`
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
  } catch (error) {
    console.error('Error fetching my businesses:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در دریافت کسب‌وکارهای شما: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    loading.value = false
  }
}

const getStatusLabel = (status) => {
  const labels = {
    'approved': 'تایید شده',
    'pending': 'در انتظار تایید',
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

const editBusiness = (business) => {
  router.push(`/business/${business.id}/edit`)
}

const deleteBusiness = async (id) => {
  if (!confirm('آیا از حذف این کسب‌وکار اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`${API_BASE_URL}/businesses/${id}`, {
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
  fetchMyBusinesses()
})
</script>
