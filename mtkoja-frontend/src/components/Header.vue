<template>
  <header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <router-link to="/" class="flex items-center">
            <img 
              src="/src/assets/img/logo.png" 
              alt="MRKOJA Logo" 
              class="h-14 w-auto"
            />
          </router-link>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:flex space-x-8">
          <router-link 
            to="/" 
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{ 'text-primary-600': $route.name === 'Home' }"
          >
            خانه
          </router-link>
          <router-link 
            to="/businesses" 
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
          >
            کسب‌وکارها
          </router-link>
          <router-link 
            to="/categories" 
            class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
          >
            دسته‌بندی‌ها
          </router-link>
        </nav>

        <!-- User Menu -->
        <div class="flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <!-- Admin Menu -->
            <router-link 
              v-if="authStore.isAdmin"
              to="/admin"
              class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              پنل مدیریت
            </router-link>
            
            <!-- Business Owner Menu -->
            <router-link 
              v-if="authStore.isBusinessOwner"
              to="/business"
              class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              پنل کسب‌وکار
            </router-link>

            <!-- User Dropdown -->
            <div class="relative">
              <button 
                @click="showUserMenu = !showUserMenu"
                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                  <span class="text-sm font-medium text-primary-700">
                    {{ authStore.user?.name?.charAt(0) }}
                  </span>
                </div>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                  {{ authStore.user?.name }}
                </div>
                <button 
                  @click="logout"
                  class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  خروج
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            <router-link 
              to="/login"
              class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              ورود
            </router-link>
            <router-link 
              to="/register"
              class="bg-primary-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-700 transition-colors"
            >
              ثبت‌نام
            </router-link>
          </template>

          <!-- Mobile menu button -->
          <button 
            @click="showMobileMenu = !showMobileMenu"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-primary-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="showMobileMenu" class="md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t">
          <router-link 
            to="/" 
            class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            خانه
          </router-link>
          <router-link 
            to="/businesses" 
            class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            کسب‌وکارها
          </router-link>
          <router-link 
            to="/categories" 
            class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
          >
            دسته‌بندی‌ها
          </router-link>
          
          <template v-if="authStore.isAuthenticated">
            <router-link 
              v-if="authStore.isAdmin"
              to="/admin"
              class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
            >
              پنل مدیریت
            </router-link>
            <router-link 
              v-if="authStore.isBusinessOwner"
              to="/business"
              class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
            >
              پنل کسب‌وکار
            </router-link>
            <button 
              @click="logout"
              class="text-gray-700 hover:text-primary-600 block w-full text-right px-3 py-2 rounded-md text-base font-medium"
            >
              خروج
            </button>
          </template>
          
          <template v-else>
            <router-link 
              to="/login"
              class="text-gray-700 hover:text-primary-600 block px-3 py-2 rounded-md text-base font-medium"
            >
              ورود
            </router-link>
            <router-link 
              to="/register"
              class="bg-primary-600 text-white block px-3 py-2 rounded-md text-base font-medium hover:bg-primary-700"
            >
              ثبت‌نام
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

const logout = async () => {
  await authStore.logout()
  showUserMenu.value = false
  showMobileMenu.value = false
  router.push('/')
}
</script>
