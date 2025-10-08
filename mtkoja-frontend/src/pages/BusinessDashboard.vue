<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">پنل کسب‌وکار</h1>
            <p class="mt-2 text-gray-600">مدیریت کسب‌وکارهای خود</p>
          </div>
          <router-link
            to="/business/new"
            class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-700"
          >
            افزودن کسب‌وکار جدید
          </router-link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
            </div>
            <div class="mr-4">
              <p class="text-sm font-medium text-gray-500">کل کسب‌وکارها</p>
              <p class="text-2xl font-semibold text-gray-900">{{ myBusinesses.length }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="mr-4">
              <p class="text-sm font-medium text-gray-500">تایید شده</p>
              <p class="text-2xl font-semibold text-gray-900">{{ approvedCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="mr-4">
              <p class="text-sm font-medium text-gray-500">در انتظار تایید</p>
              <p class="text-2xl font-semibold text-gray-900">{{ pendingCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Businesses List -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">کسب‌وکارهای من</h3>
        </div>
        <div class="p-6">
          <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
          <div v-else-if="myBusinesses.length > 0" class="space-y-4">
            <div
              v-for="business in myBusinesses"
              :key="business.id"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">{{ business.name }}</h4>
                    <span
                      :class="{
                        'bg-green-100 text-green-800': business.status === 'approved',
                        'bg-yellow-100 text-yellow-800': business.status === 'pending',
                        'bg-red-100 text-red-800': business.status === 'rejected'
                      }"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ getStatusText(business.status) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600">{{ business.category.name }}</p>
                  <p class="text-sm text-gray-500 mt-1">{{ business.description.substring(0, 100) }}...</p>
                  <div class="flex items-center mt-2">
                    <div class="flex text-yellow-400">
                      <span v-for="i in 5" :key="i" class="text-sm">★</span>
                    </div>
                    <span class="text-sm text-gray-500 mr-2">{{ business.rating }}</span>
                    <span class="text-sm text-gray-400">•</span>
                    <span class="text-sm text-gray-500 mr-2">{{ business.review_count }} نظر</span>
                  </div>
                </div>
                <div class="flex space-x-2 mr-4">
                  <router-link
                    :to="`/business/${business.id}`"
                    class="text-primary-600 hover:text-primary-700 text-sm font-medium"
                  >
                    مشاهده
                  </router-link>
                  <router-link
                    :to="`/business/${business.id}/edit`"
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                  >
                    ویرایش
                  </router-link>
                  <button
                    @click="deleteBusiness(business.id)"
                    class="text-red-600 hover:text-red-700 text-sm font-medium"
                  >
                    حذف
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">هیچ کسب‌وکاری ندارید</h3>
            <p class="mt-1 text-sm text-gray-500">شروع کنید با افزودن اولین کسب‌وکار خود.</p>
            <div class="mt-6">
              <router-link
                to="/business/new"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
              >
                <svg class="-mr-1 ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                افزودن کسب‌وکار جدید
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const myBusinesses = ref([])
const loading = ref(false)

const API_BASE_URL = 'http://localhost:8000/api'

const approvedCount = computed(() => {
  return myBusinesses.value.filter(b => b.status === 'approved').length
})

const pendingCount = computed(() => {
  return myBusinesses.value.filter(b => b.status === 'pending').length
})

const getStatusText = (status) => {
  const statusMap = {
    'approved': 'تایید شده',
    'pending': 'در انتظار تایید',
    'rejected': 'رد شده'
  }
  return statusMap[status] || status
}

const fetchMyBusinesses = async () => {
  loading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/my-businesses`)
    myBusinesses.value = response.data.businesses.data
  } catch (error) {
    console.error('Error fetching my businesses:', error)
  } finally {
    loading.value = false
  }
}

const deleteBusiness = async (businessId) => {
  if (confirm('آیا مطمئن هستید که می‌خواهید این کسب‌وکار را حذف کنید؟')) {
    try {
      await axios.delete(`${API_BASE_URL}/businesses/${businessId}`)
      await fetchMyBusinesses()
    } catch (error) {
      console.error('Error deleting business:', error)
    }
  }
}

onMounted(() => {
  fetchMyBusinesses()
})
</script>


