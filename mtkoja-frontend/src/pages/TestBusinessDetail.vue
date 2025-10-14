<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">تست صفحه کسب‌وکار</h1>
      
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">اطلاعات Route</h2>
        <div class="space-y-2">
          <p><strong>Route Name:</strong> {{ route.name }}</p>
          <p><strong>Route Path:</strong> {{ route.path }}</p>
          <p><strong>Route Params:</strong> {{ JSON.stringify(route.params) }}</p>
          <p><strong>Route Query:</strong> {{ JSON.stringify(route.query) }}</p>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">تست API</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">شناسه کسب‌وکار (ID یا Slug):</label>
            <input 
              v-model="testId" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="1 یا slug-کسب‌وکار"
            >
          </div>
          <button 
            @click="testFetchBusiness"
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ loading ? 'در حال تست...' : 'تست دریافت کسب‌وکار' }}
          </button>
        </div>
      </div>

      <div v-if="testResult" class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">نتیجه تست</h2>
        <div class="space-y-4">
          <div v-if="testResult.success">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              ✅ موفق: کسب‌وکار یافت شد
            </div>
            <div class="bg-gray-100 p-4 rounded">
              <h3 class="font-semibold mb-2">اطلاعات کسب‌وکار:</h3>
              <pre class="text-sm overflow-auto">{{ JSON.stringify(testResult.data, null, 2) }}</pre>
            </div>
          </div>
          <div v-else class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            ❌ خطا: {{ testResult.error }}
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">لینک‌های تست</h2>
        <div class="space-y-2">
          <router-link to="/business/1" class="block text-blue-600 hover:text-blue-800">
            /business/1 (با ID)
          </router-link>
          <router-link to="/business/test-business" class="block text-blue-600 hover:text-blue-800">
            /business/test-business (با Slug)
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const testId = ref('1')
const loading = ref(false)
const testResult = ref(null)

const API_BASE_URL = 'https://mrkoja.com/api'

const testFetchBusiness = async () => {
  loading.value = true
  testResult.value = null
  
  try {
    const response = await axios.get(`${API_BASE_URL}/businesses/${testId.value}`)
    testResult.value = {
      success: true,
      data: response.data
    }
  } catch (error) {
    testResult.value = {
      success: false,
      error: error.response?.data?.message || error.message
    }
  } finally {
    loading.value = false
  }
}
</script>

