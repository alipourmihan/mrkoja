<template>
  <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden">
    <div class="flex justify-around items-center py-2">
      <!-- خانه -->
      <router-link 
        to="/" 
        class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-primary-600 transition-colors"
        :class="{ 'text-primary-600': $route.path === '/' }"
      >
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="text-xs font-medium">خانه</span>
      </router-link>

      <!-- دسته بندی ها -->
      <router-link 
        to="/categories" 
        class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-primary-600 transition-colors"
        :class="{ 'text-primary-600': $route.path === '/categories' }"
      >
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <span class="text-xs font-medium">دسته‌بندی‌ها</span>
      </router-link>

      <!-- علاقه مندی ها -->
      <router-link 
        to="/favorites" 
        class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-primary-600 transition-colors"
        :class="{ 'text-primary-600': $route.path === '/favorites' }"
      >
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        <span class="text-xs font-medium">علاقه‌مندی‌ها</span>
      </router-link>

      <!-- حساب کاربری -->
      <router-link 
        to="/profile" 
        class="flex flex-col items-center py-2 px-3 text-gray-600 hover:text-primary-600 transition-colors"
        :class="{ 'text-primary-600': $route.path === '/profile' }"
      >
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-xs font-medium">حساب کاربری</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
// No additional logic needed for this component
</script>
