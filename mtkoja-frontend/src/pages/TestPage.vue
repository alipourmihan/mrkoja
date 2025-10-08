<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-800 mb-8 font-iran">تست اتصال به API</h1>
      
      <!-- Categories Test -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">دسته‌بندی‌ها</h2>
        <div v-if="categoryStore.loading" class="text-gray-500">در حال بارگذاری...</div>
        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div 
            v-for="category in categoryStore.categories" 
            :key="category.id"
            class="p-4 border border-gray-200 rounded-lg text-center"
          >
            <div class="text-2xl mb-2">{{ category.icon }}</div>
            <div class="font-medium font-iran">{{ category.name }}</div>
          </div>
        </div>
        <button 
          @click="loadCategories" 
          class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-iran"
        >
          بارگذاری مجدد دسته‌بندی‌ها
        </button>
      </div>

      <!-- Auth Test -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">احراز هویت</h2>
        <div v-if="authStore.isAuthenticated" class="text-green-600 font-iran">
          کاربر وارد شده: {{ authStore.user?.name }}
        </div>
        <div v-else class="text-gray-500 font-iran">
          کاربر وارد نشده است
        </div>
        <button 
          @click="testLogin" 
          class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 font-iran"
        >
          تست ورود
        </button>
      </div>

      <!-- Business Test -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4 font-iran">کسب‌وکارها</h2>
        <div v-if="businessStore.loading" class="text-gray-500">در حال بارگذاری...</div>
        <div v-else class="text-gray-600 font-iran">
          تعداد کسب‌وکارها: {{ businessStore.businesses.length }}
        </div>
        <button 
          @click="loadBusinesses" 
          class="mt-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 font-iran"
        >
          بارگذاری کسب‌وکارها
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useBusinessStore } from '@/stores/business'
import { useCategoryStore } from '@/stores/category'
import { useAuthStore } from '@/stores/auth'

const businessStore = useBusinessStore()
const categoryStore = useCategoryStore()
const authStore = useAuthStore()

const loadCategories = async () => {
  await categoryStore.fetchCategories()
}

const loadBusinesses = async () => {
  await businessStore.fetchBusinesses()
}

const testLogin = async () => {
  const result = await authStore.login({
    email: 'owner@mtkoja.com',
    password: 'password'
  })
  
  if (result.success) {
    alert('ورود موفق!')
  } else {
    alert('خطا در ورود: ' + result.message)
  }
}

onMounted(async () => {
  await loadCategories()
  await loadBusinesses()
})
</script>
