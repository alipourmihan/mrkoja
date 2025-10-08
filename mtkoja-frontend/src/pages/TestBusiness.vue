<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-8 font-iran">تست ثبت کسب‌وکار</h1>
      
      <!-- Auth Status -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">وضعیت احراز هویت</h2>
        <div v-if="authStore.isAuthenticated" class="text-green-600 font-iran">
          ✅ کاربر وارد شده: {{ authStore.user?.name }} ({{ authStore.user?.role }})
        </div>
        <div v-else class="text-red-600 font-iran">
          ❌ کاربر وارد نشده است
        </div>
        <button 
          @click="testLogin" 
          class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-iran"
        >
          تست ورود
        </button>
      </div>

      <!-- Test Form -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">تست ثبت کسب‌وکار</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">نام کسب‌وکار</label>
            <input 
              v-model="testData.name" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
              placeholder="نام کسب‌وکار"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">توضیحات</label>
            <textarea 
              v-model="testData.description" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
              placeholder="توضیحات کسب‌وکار"
              rows="3"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">آدرس</label>
            <input 
              v-model="testData.address" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
              placeholder="آدرس کسب‌وکار"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">شماره تماس</label>
            <input 
              v-model="testData.phone" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
              placeholder="09123456789"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">دسته‌بندی</label>
            <select 
              v-model="testData.category_id" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
            >
              <option value="">انتخاب کنید</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
        
        <button 
          @click="testCreateBusiness" 
          :disabled="loading"
          class="mt-6 w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran disabled:opacity-50"
        >
          {{ loading ? 'در حال ثبت...' : 'تست ثبت کسب‌وکار' }}
        </button>
      </div>

      <!-- Result -->
      <div v-if="result" class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">نتیجه</h2>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ JSON.stringify(result, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useBusinessStore } from '@/stores/business'
import { useCategoryStore } from '@/stores/category'
import { useAuthStore } from '@/stores/auth'

const businessStore = useBusinessStore()
const categoryStore = useCategoryStore()
const authStore = useAuthStore()

const loading = ref(false)
const result = ref(null)

const testData = ref({
  name: 'کافه تست',
  description: 'این یک کافه تست است برای بررسی عملکرد سیستم',
  address: 'تهران، خیابان ولیعصر، پلاک 123',
  phone: '09123456789',
  category_id: 1
})

const categories = computed(() => categoryStore.categories)

const testLogin = async () => {
  const loginResult = await authStore.login({
    email: 'owner@mtkoja.com',
    password: 'password'
  })
  
  if (loginResult.success) {
    alert('ورود موفق!')
  } else {
    alert('خطا در ورود: ' + loginResult.message)
  }
}

const testCreateBusiness = async () => {
  loading.value = true
  result.value = null
  
  try {
    const response = await businessStore.createBusiness(testData.value)
    result.value = response
  } catch (error) {
    result.value = { error: error.message }
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await categoryStore.fetchCategories()
  authStore.initializeAuth()
})
</script>
