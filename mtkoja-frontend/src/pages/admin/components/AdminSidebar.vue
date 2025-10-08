<template>
  <div class="w-64 bg-white shadow-lg fixed right-0 top-0 h-full overflow-y-auto">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
      <h1 class="text-xl font-bold text-gray-900 text-center">پنل مدیریت</h1>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="mt-6">
      <!-- Dashboard -->
      <div class="px-3 mb-2">
        <button 
          @click="$emit('change-section', 'dashboard')"
          :class="[
            'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors',
            activeSection === 'dashboard' 
              ? 'bg-blue-100 text-blue-700' 
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
          </svg>
          داشبورد
        </button>
      </div>

      <!-- User Management -->
      <div class="px-3 mb-2">
        <button 
          @click="toggleUserMenu"
          :class="[
            'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-colors',
            userMenuOpen || activeSection.startsWith('users')
              ? 'bg-gray-100 text-gray-900' 
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            مدیریت کاربران
          </div>
          <svg 
            :class="['w-4 h-4 transition-transform', userMenuOpen ? 'rotate-180' : '']" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <!-- User Submenu -->
        <div v-show="userMenuOpen" class="mr-6 mt-2 space-y-1">
          <button 
            @click="$emit('change-section', 'users')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'users' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            کاربران
          </button>
          <button 
            @click="$emit('change-section', 'business-owners')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'business-owners' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            صاحبان کسب و کار
          </button>
          <button 
            @click="$emit('change-section', 'documents')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'documents' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            اسناد و مدارک
          </button>
          <button 
            @click="$emit('change-section', 'violations')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'violations' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            تخلفات
          </button>
          <button 
            @click="$emit('change-section', 'sales-experts')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'sales-experts' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            کارشناسان فروش
          </button>
        </div>
      </div>

      <!-- Business Management -->
      <div class="px-3 mb-2">
        <button 
          @click="toggleBusinessMenu"
          :class="[
            'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-colors',
            businessMenuOpen || activeSection.startsWith('business')
              ? 'bg-gray-100 text-gray-900' 
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <div class="flex items-center">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            مدیریت کسب و کارها
          </div>
          <svg 
            :class="['w-4 h-4 transition-transform', businessMenuOpen ? 'rotate-180' : '']" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <!-- Business Submenu -->
        <div v-show="businessMenuOpen" class="mr-6 mt-2 space-y-1">
          <button 
            @click="$emit('change-section', 'my-businesses')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'my-businesses' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            کسب و کارهای من
          </button>
          <button 
            @click="$emit('change-section', 'all-businesses')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'all-businesses' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            تمام کسب و کارها
          </button>
          <button 
            @click="$emit('change-section', 'add-business')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'add-business' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            اضافه کردن
          </button>
          <button 
            @click="$emit('change-section', 'categories')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'categories' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            دسته بندی ها
          </button>
          <button 
            @click="$emit('change-section', 'features')"
            :class="[
              'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
              activeSection === 'features' 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ویژگی ها
          </button>
          <button 
            @click="toggleLocationMenu"
            :class="[
              'w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-colors',
              locationMenuOpen || activeSection.startsWith('provinces') || activeSection.startsWith('cities') || activeSection.startsWith('neighborhoods')
                ? 'bg-gray-100 text-gray-900' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            ]"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              مدیریت مکان‌ها
            </div>
            <svg 
              :class="['w-4 h-4 transition-transform', locationMenuOpen ? 'rotate-180' : '']" 
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          
          <!-- Location Submenu -->
          <div v-if="locationMenuOpen" class="mr-4 mt-1 space-y-1">
            <button 
              @click="$emit('change-section', 'provinces')"
              :class="[
                'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
                activeSection === 'provinces' 
                  ? 'bg-purple-100 text-purple-700' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              استان‌ها
            </button>
            <button 
              @click="$emit('change-section', 'cities')"
              :class="[
                'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
                activeSection === 'cities' 
                  ? 'bg-blue-100 text-blue-700' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              شهرها
            </button>
            <button 
              @click="$emit('change-section', 'neighborhoods')"
              :class="[
                'w-full flex items-center px-3 py-2 text-sm rounded-md transition-colors',
                activeSection === 'neighborhoods' 
                  ? 'bg-green-100 text-green-700' 
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
              </svg>
              محله‌ها
            </button>
          </div>
        </div>
      </div>

      <!-- SEO Management -->
      <div class="px-3 mb-2">
        <button 
          @click="$emit('change-section', 'seo-management')"
          :class="[
            'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors',
            activeSection === 'seo-management' 
              ? 'bg-blue-100 text-blue-700' 
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          مدیریت SEO
        </button>
      </div>

      <!-- Tickets -->
      <div class="px-3 mb-2">
        <button 
          @click="$emit('change-section', 'tickets')"
          :class="[
            'w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors',
            activeSection === 'tickets' 
              ? 'bg-blue-100 text-blue-700' 
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
          </svg>
          تیکت ها
        </button>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  activeSection: {
    type: String,
    required: true
  }
})

defineEmits(['change-section'])

// Menu states
const userMenuOpen = ref(false)
const locationMenuOpen = ref(false)

// Methods
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const toggleLocationMenu = () => {
  locationMenuOpen.value = !locationMenuOpen.value
}

const businessMenuOpen = ref(false)

const toggleBusinessMenu = () => {
  businessMenuOpen.value = !businessMenuOpen.value
  if (businessMenuOpen.value) {
    userMenuOpen.value = false
  }
}
</script>
