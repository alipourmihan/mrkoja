<template>
  <div class="min-h-screen bg-white pb-20">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
          <div class="flex items-center">
            <button @click="$router.go(-1)" class="mr-4">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <h1 class="text-xl font-bold text-gray-900">علاقه‌مندی‌ها</h1>
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="favorites.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">💔</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">هیچ علاقه‌مندی‌ای ندارید</h2>
        <p class="text-gray-600 mb-6">کسب‌وکارهای مورد علاقه خود را ذخیره کنید</p>
        <router-link 
          to="/" 
          class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors"
        >
          شروع جستجو
        </router-link>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="business in favorites"
          :key="business.id"
          class="flex items-center bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <!-- Business Image -->
          <div class="w-20 h-20 bg-gray-200 rounded-xl overflow-hidden flex-shrink-0 ml-4">
            <img 
              v-if="getBusinessImage(business)"
              :src="getBusinessImage(business)" 
              :alt="business.name"
              class="w-full h-full object-cover"
              @error="handleImageError"
            >
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          
          <!-- Business Info -->
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ business.name }}</h3>
            <p class="text-sm text-gray-500 truncate">{{ business.category?.name || 'دسته‌بندی نامشخص' }}</p>
            <p class="text-sm text-gray-500 truncate">{{ business.description || 'توضیحات موجود نیست' }}</p>
          </div>
          
          <!-- Actions -->
          <div class="flex items-center space-x-2 space-x-reverse">
            <button 
              @click="removeFromFavorites(business.id)"
              class="text-red-500 hover:text-red-700 p-2"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </button>
            <router-link 
              :to="`/business/${business.id}`"
              class="text-primary-600 hover:text-primary-700 p-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const favorites = ref([])
const API_BASE_URL = 'https://api.mrkoja.com/api'

const fetchFavorites = async () => {
  try {
    // This would be implemented based on your backend API
    // For now, we'll use mock data
    favorites.value = []
  } catch (error) {
    console.error('Error fetching favorites:', error)
  }
}

const getBusinessImage = (business) => {
  if (business.image_urls && business.image_urls.length > 0) {
    return business.image_urls[0].url
  }
  if (business.images && business.images.length > 0) {
    return business.images[0]
  }
  return null
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  event.target.style.display = 'none'
}

const removeFromFavorites = (businessId) => {
  favorites.value = favorites.value.filter(b => b.id !== businessId)
  // Here you would also call the API to remove from favorites
}

onMounted(() => {
  fetchFavorites()
})
</script>
