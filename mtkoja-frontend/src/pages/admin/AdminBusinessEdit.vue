<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
          <div class="flex items-center space-x-4 space-x-reverse">
            <button @click="goBack" class="text-gray-600 hover:text-gray-900">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">ویرایش کسب‌وکار</h1>
          </div>
          <div class="flex items-center space-x-3 space-x-reverse">
            <span class="text-sm text-gray-500">ID: {{ businessId }}</span>
            <span :class="getStatusBadgeClass(business?.status)" class="px-2 py-1 text-xs font-medium rounded-full">
              {{ getStatusText(business?.status) }}
            </span>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
        <p class="text-gray-600">در حال بارگذاری اطلاعات کسب‌وکار...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex">
          <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="mr-3">
            <h3 class="text-sm font-medium text-red-800">خطا در بارگذاری</h3>
            <p class="mt-1 text-sm text-red-700">{{ error }}</p>
          </div>
        </div>
        <div class="mt-4">
          <button @click="loadBusinessData" class="text-sm bg-red-100 text-red-800 px-3 py-2 rounded-md hover:bg-red-200">
            تلاش مجدد
          </button>
        </div>
      </div>
    </div>

    <!-- Main Form -->
    <div v-else-if="business" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900">اطلاعات اصلی</h2>
            </div>
            <div class="p-6 space-y-6">
              <!-- Business Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نام کسب‌وکار</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                <textarea
                  v-model="formData.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>

              <!-- Address -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">آدرس</label>
                <input
                  v-model="formData.address"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تلفن</label>
                <input
                  v-model="formData.phone"
                  type="tel"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ایمیل</label>
                <input
                  v-model="formData.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Website -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وب‌سایت</label>
                <input
                  v-model="formData.website"
                  type="url"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Category -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی</label>
                <select
                  v-model="formData.category_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">انتخاب دسته‌بندی</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select
                  v-model="formData.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="pending">در انتظار تایید</option>
                  <option value="approved">تایید شده</option>
                  <option value="rejected">رد شده</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Business Info -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">اطلاعات کسب‌وکار</h3>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label class="text-sm font-medium text-gray-500">تاریخ ثبت</label>
                <p class="text-sm text-gray-900">{{ formatDate(business.created_at) }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">آخرین به‌روزرسانی</label>
                <p class="text-sm text-gray-900">{{ formatDate(business.updated_at) }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">امتیاز</label>
                <p class="text-sm text-gray-900">{{ business.rating || 'بدون امتیاز' }}</p>
              </div>
            </div>
          </div>

          <!-- Owner Info -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">صاحب کسب‌وکار</h3>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label class="text-sm font-medium text-gray-500">نام</label>
                <p class="text-sm text-gray-900">{{ business.user?.name || 'نامشخص' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">ایمیل</label>
                <p class="text-sm text-gray-900">{{ business.user?.email || 'نامشخص' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">نقش</label>
                <p class="text-sm text-gray-900">{{ getRoleText(business.user?.role) }}</p>
              </div>
            </div>
          </div>

          <!-- Images -->
          <div v-if="getBusinessImages().length > 0" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">تصاویر</h3>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-2 gap-4">
                <img
                  v-for="(image, index) in getBusinessImages().slice(0, 4)"
                  :key="index"
                  :src="image"
                  :alt="business.name"
                  class="w-full h-24 object-cover rounded-lg"
                  @error="handleImageError"
                />
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">عملیات</h3>
            </div>
            <div class="p-6 space-y-3">
              <button
                @click="saveChanges"
                :disabled="saving"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ saving ? 'در حال ذخیره...' : 'ذخیره تغییرات' }}
              </button>
              
              <button
                @click="approveBusiness"
                v-if="business.status !== 'approved'"
                class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
              >
                تایید کسب‌وکار
              </button>
              
              <button
                @click="rejectBusiness"
                v-if="business.status !== 'rejected'"
                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
              >
                رد کسب‌وکار
              </button>
              
              <button
                @click="deleteBusiness"
                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
              >
                حذف کسب‌وکار
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

const API_BASE_URL = 'http://localhost:8000/api'

// State
const loading = ref(false)
const saving = ref(false)
const error = ref(null)
const business = ref(null)
const categories = ref([])

// Form data
const formData = ref({
  name: '',
  description: '',
  address: '',
  phone: '',
  email: '',
  website: '',
  category_id: '',
  status: 'pending'
})

// Computed
const businessId = computed(() => route.params.id)

// Methods
const loadBusinessData = async () => {
  loading.value = true
  error.value = null
  
  try {
    const token = localStorage.getItem('token')
    
    // First try to get from admin businesses list
    let response
    try {
      console.log('Trying to get from admin businesses list...')
      const adminResponse = await axios.get(`${API_BASE_URL}/admin/businesses`, {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      const businesses = adminResponse.data.businesses?.data || adminResponse.data.businesses || []
      const foundBusiness = businesses.find(b => b.id == businessId.value)
      if (foundBusiness) {
        response = { data: { business: foundBusiness } }
        console.log('Found business in admin list:', foundBusiness.name)
      } else {
        throw new Error('Business not found in admin list')
      }
    } catch (adminListError) {
      console.log('Admin list failed, trying individual endpoints...')
      
      // Try different individual endpoints
      try {
        // Try regular endpoint first
        response = await axios.get(`${API_BASE_URL}/businesses/${businessId.value}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        console.log('Found business via regular endpoint')
      } catch (firstError) {
        console.log('Regular endpoint failed, trying admin endpoint...')
        try {
          response = await axios.get(`${API_BASE_URL}/admin/businesses/${businessId.value}`, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          console.log('Found business via admin endpoint')
        } catch (secondError) {
          console.log('Admin endpoint also failed, trying my-businesses...')
          // Try my-businesses endpoint
          const myBusinessesResponse = await axios.get(`${API_BASE_URL}/my-businesses`, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          // Find the specific business
          const businesses = myBusinessesResponse.data.businesses?.data || myBusinessesResponse.data.businesses || []
          const business = businesses.find(b => b.id == businessId.value)
          if (business) {
            response = { data: { business } }
            console.log('Found business via my-businesses')
          } else {
            throw new Error('Business not found in any endpoint')
          }
        }
      }
    }
    
    business.value = response.data.business || response.data
    
    // Fill form data
    formData.value = {
      name: business.value.name || '',
      description: business.value.description || '',
      address: business.value.address || '',
      phone: business.value.phone || '',
      email: business.value.email || '',
      website: business.value.website || '',
      category_id: business.value.category_id || '',
      status: business.value.status || 'pending'
    }
    
    // Load categories
    await loadCategories()
    
  } catch (err) {
    console.error('Error loading business:', err)
    console.error('Error response:', err.response?.data)
    console.error('Error status:', err.response?.status)
    
    let errorMessage = 'خطا در بارگذاری اطلاعات کسب‌وکار'
    
    if (err.response?.status === 404) {
      errorMessage = 'کسب‌وکار مورد نظر یافت نشد. لطفاً از لیست کسب‌وکارها یک مورد موجود انتخاب کنید.'
    } else if (err.response?.status === 403) {
      errorMessage = 'شما دسترسی لازم برای مشاهده این کسب‌وکار را ندارید'
    } else if (err.response?.status === 405) {
      errorMessage = 'API endpoint پشتیبانی نمی‌شود. لطفاً با پشتیبانی تماس بگیرید'
    } else if (err.response?.data?.message) {
      errorMessage = err.response.data.message
    } else if (err.message) {
      errorMessage = err.message
    }
    
    error.value = errorMessage
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/categories`)
    categories.value = response.data.categories || response.data
  } catch (err) {
    console.error('Error loading categories:', err)
  }
}

const saveChanges = async () => {
  saving.value = true
  
  try {
    const token = localStorage.getItem('token')
    
    // Only try admin endpoint since we're in admin panel
    const response = await axios.put(`${API_BASE_URL}/admin/businesses/${businessId.value}`, formData.value, {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    
    alert('تغییرات با موفقیت ذخیره شد')
    await loadBusinessData() // Reload data
    
  } catch (err) {
    console.error('Error saving changes:', err)
    console.error('Error response:', err.response?.data)
    console.error('Error status:', err.response?.status)
    
    let errorMessage = 'خطا در ذخیره تغییرات'
    
    if (err.response?.status === 500) {
      errorMessage = 'خطای داخلی سرور. لطفاً با پشتیبانی تماس بگیرید'
    } else if (err.response?.status === 404) {
      errorMessage = 'کسب‌وکار مورد نظر یافت نشد'
    } else if (err.response?.status === 403) {
      errorMessage = 'شما دسترسی لازم برای ویرایش این کسب‌وکار را ندارید'
    } else if (err.response?.status === 422) {
      errorMessage = 'داده‌های ارسالی نامعتبر است: ' + (err.response?.data?.message || '')
    } else if (err.response?.data?.message) {
      errorMessage = err.response.data.message
    } else if (err.message) {
      errorMessage = err.message
    }
    
    alert(errorMessage)
  } finally {
    saving.value = false
  }
}

const approveBusiness = async () => {
  if (!confirm('آیا از تایید این کسب‌وکار اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.put(`${API_BASE_URL}/admin/businesses/${businessId.value}/approve`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    alert('کسب‌وکار با موفقیت تایید شد')
    await loadBusinessData()
    
  } catch (err) {
    console.error('Error approving business:', err)
    alert('خطا در تایید کسب‌وکار: ' + (err.response?.data?.message || err.message))
  }
}

const rejectBusiness = async () => {
  if (!confirm('آیا از رد این کسب‌وکار اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.put(`${API_BASE_URL}/admin/businesses/${businessId.value}/reject`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    alert('کسب‌وکار رد شد')
    await loadBusinessData()
    
  } catch (err) {
    console.error('Error rejecting business:', err)
    alert('خطا در رد کسب‌وکار: ' + (err.response?.data?.message || err.message))
  }
}

const deleteBusiness = async () => {
  if (!confirm('آیا از حذف این کسب‌وکار اطمینان دارید؟ این عمل قابل بازگشت نیست.')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`${API_BASE_URL}/admin/businesses/${businessId.value}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    alert('کسب‌وکار با موفقیت حذف شد')
    router.push('/admin')
    
  } catch (err) {
    console.error('Error deleting business:', err)
    alert('خطا در حذف کسب‌وکار: ' + (err.response?.data?.message || err.message))
  }
}

const goBack = () => {
  router.push('/admin')
}

const getStatusText = (status) => {
  const statusMap = {
    'approved': 'تایید شده',
    'pending': 'در انتظار تایید',
    'rejected': 'رد شده'
  }
  return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
  const classMap = {
    'approved': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'rejected': 'bg-red-100 text-red-800'
  }
  return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getRoleText = (role) => {
  const roleMap = {
    'admin': 'مدیر',
    'business_owner': 'صاحب کسب‌وکار',
    'user': 'کاربر عادی'
  }
  return roleMap[role] || role
}

const formatDate = (dateString) => {
  if (!dateString) return 'نامشخص'
  return new Date(dateString).toLocaleDateString('fa-IR')
}

const getBusinessImages = () => {
  if (!business.value) return []
  
  // Check for image_urls first (new format)
  if (business.value.image_urls && Array.isArray(business.value.image_urls)) {
    return business.value.image_urls.map(img => img.url || img).filter(Boolean)
  }
  
  // Check for images (old format)
  if (business.value.images && Array.isArray(business.value.images)) {
    return business.value.images.map(img => getImageUrl(img)).filter(Boolean)
  }
  
  return []
}

const getImageUrl = (imageUrl) => {
  if (!imageUrl || typeof imageUrl !== 'string') return ''
  if (imageUrl.startsWith('http')) return imageUrl
  if (imageUrl.startsWith('/storage/')) return `http://localhost:8000${imageUrl}`
  return `http://localhost:8000/storage/${imageUrl}`
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Lifecycle
onMounted(() => {
  loadBusinessData()
})
</script>
