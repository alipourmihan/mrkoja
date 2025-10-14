<template>
  <div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- کل کسب‌وکارها -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
              </svg>
            </div>
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-500">کل کسب‌وکارها</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_businesses || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- تایید شده -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-500">تایید شده</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.approved_businesses || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- در انتظار تایید -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-500">در انتظار تایید</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.pending_businesses || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- دسته‌بندی‌ها -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
            </div>
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-500">دسته‌بندی‌ها</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_categories || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Businesses -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">کسب‌وکارهای در انتظار تایید</h3>
      </div>
      <div class="p-6">
        <div v-if="loading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        </div>
        <div v-else-if="pendingBusinesses.length > 0" class="space-y-4">
          <div
            v-for="business in pendingBusinesses"
            :key="business.id"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-lg font-semibold text-gray-900">{{ business.name }}</h4>
                <p class="text-sm text-gray-600">{{ business.category.name }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ business.description.substring(0, 100) }}...</p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="approveBusiness(business.id)"
                  class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700"
                >
                  تایید
                </button>
                <button
                  @click="rejectBusiness(business.id)"
                  class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700"
                >
                  رد
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          هیچ کسب‌وکاری در انتظار تایید نیست
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

// State
const stats = ref({})
const pendingBusinesses = ref([])
const loading = ref(false)

// Methods
const fetchStats = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/stats`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    // Handle different response structures safely
    if (response.data.stats) {
      stats.value = response.data.stats
    } else if (response.data) {
      stats.value = response.data
    } else {
      stats.value = {}
    }
  } catch (error) {
    console.error('Error fetching stats:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      console.error('خطا: سرور در دسترس نیست')
    }
  }
}

const fetchPendingBusinesses = async () => {
  loading.value = true
  const token = localStorage.getItem('token')
  try {
    const response = await axios.get(`${API_BASE_URL}/admin/businesses?status=pending`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    // Handle different response structures safely
    if (response.data.businesses && response.data.businesses.data) {
      pendingBusinesses.value = response.data.businesses.data
    } else if (Array.isArray(response.data.businesses)) {
      pendingBusinesses.value = response.data.businesses
    } else if (Array.isArray(response.data)) {
      pendingBusinesses.value = response.data
    } else {
      pendingBusinesses.value = []
    }
  } catch (error) {
    console.error('Error fetching pending businesses:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      console.error('خطا: سرور در دسترس نیست')
    }
    
    // Fallback
    try {
      const response = await axios.get(`${API_BASE_URL}/admin/businesses`, {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      const allBusinesses = (response.data?.businesses?.data) || (response.data?.businesses) || []
      pendingBusinesses.value = allBusinesses.filter(b => b.status === 'pending')
    } catch (fallbackError) {
      console.error('Fallback error:', fallbackError)
      pendingBusinesses.value = []
    }
  } finally {
    loading.value = false
  }
}

const approveBusiness = async (id) => {
  if (!confirm('آیا از تایید این کسب‌وکار اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.put(`${API_BASE_URL}/admin/businesses/${id}/approve`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    pendingBusinesses.value = pendingBusinesses.value.filter(b => b.id !== id)
    fetchStats()
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
    await axios.put(`${API_BASE_URL}/admin/businesses/${id}/reject`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    pendingBusinesses.value = pendingBusinesses.value.filter(b => b.id !== id)
    fetchStats()
    alert('کسب‌وکار رد شد')
  } catch (error) {
    console.error('Error rejecting business:', error)
    alert('خطا در رد کسب‌وکار: ' + (error.response?.data?.message || error.message))
  }
}

onMounted(() => {
  fetchStats()
  fetchPendingBusinesses()
})
</script>

