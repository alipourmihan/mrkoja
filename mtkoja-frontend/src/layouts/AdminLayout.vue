<template>
  <div class="min-h-screen flex" style="background-color: #F9FAFB; font-family: 'Vazirmatn', 'IRANSansX', system-ui, sans-serif; direction: rtl;" dir="rtl">
    <!-- Sidebar (RTL - right aligned) -->
    <aside class="w-64 bg-white border-r border-gray-200 min-h-screen sticky top-0 shadow-lg order-1">
      <div class="px-4 py-6 border-b border-gray-200">
        <h2 class="text-xl font-bold" style="color: #C8102E;">پنل مدیریت</h2>
      </div>

      <nav class="px-3 py-4 space-y-1" dir="rtl">
        <!-- Dashboard -->
        <RouterLink
          to="/admin"
          class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
          :class="isActive('/admin') 
            ? 'text-white shadow-sm' 
            : 'text-gray-700 hover:text-gray-900'"
          :style="isActive('/admin') 
            ? 'background-color: #C8102E;' 
            : 'background-color: transparent;'"
          @mouseenter="handleMouseEnter($event, '/admin')"
          @mouseleave="handleMouseLeave($event, '/admin')"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
          </svg>
          داشبورد
        </RouterLink>

        <!-- Users group -->
        <div>
          <button
            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors duration-200"
            @click="toggle('users')"
            @mouseenter="handleMouseEnter($event)"
            @mouseleave="handleMouseLeave($event)"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              مدیریت کاربران
            </div>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.users }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="open.users" class="mt-1 pr-6 space-y-1">
            <RouterLink 
              to="/admin/users" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/users') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/users') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/users')"
              @mouseleave="handleMouseLeave($event, '/admin/users')"
            >
              لیست کاربران
            </RouterLink>
            <RouterLink 
              to="/admin/users/new" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/users/new') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/users/new') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/users/new')"
              @mouseleave="handleMouseLeave($event, '/admin/users/new')"
            >
              افزودن کاربر
            </RouterLink>
          </div>
        </div>

        <!-- Businesses group -->
        <div>
          <button
            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors duration-200"
            @click="toggle('businesses')"
            @mouseenter="handleMouseEnter($event)"
            @mouseleave="handleMouseLeave($event)"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              مدیریت کسب‌وکارها
            </div>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open.businesses }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="open.businesses" class="mt-1 pr-6 space-y-1">
            <RouterLink 
              to="/admin/businesses/mine" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/businesses/mine') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/businesses/mine') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/businesses/mine')"
              @mouseleave="handleMouseLeave($event, '/admin/businesses/mine')"
            >
              کسب‌وکارهای من
            </RouterLink>
            <RouterLink 
              to="/admin/businesses" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="activeStartsWith('/admin/businesses') && !isActive('/admin/businesses/mine') && !isActive('/admin/businesses/new')
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="activeStartsWith('/admin/businesses') && !isActive('/admin/businesses/mine') && !isActive('/admin/businesses/new')
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/businesses')"
              @mouseleave="handleMouseLeave($event, '/admin/businesses')"
            >
              تمام کسب‌وکارها
            </RouterLink>
            <RouterLink 
              to="/admin/businesses/new" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/businesses/new') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/businesses/new') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/businesses/new')"
              @mouseleave="handleMouseLeave($event, '/admin/businesses/new')"
            >
              افزودن کسب‌وکار
            </RouterLink>
            <RouterLink 
              to="/admin/categories" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/categories') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/categories') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/categories')"
              @mouseleave="handleMouseLeave($event, '/admin/categories')"
            >
              دسته‌بندی‌ها
            </RouterLink>
            <RouterLink 
              to="/admin/features" 
              class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors duration-200"
              :class="isActive('/admin/features') 
                ? 'text-white shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'"
              :style="isActive('/admin/features') 
                ? 'background-color: #C8102E;' 
                : 'background-color: transparent;'"
              @mouseenter="handleMouseEnter($event, '/admin/features')"
              @mouseleave="handleMouseLeave($event, '/admin/features')"
            >
              ویژگی‌ها
            </RouterLink>
          </div>
        </div>

        <!-- Locations -->
        <RouterLink
          to="/admin/locations"
          class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
          :class="isActive('/admin/locations') 
            ? 'text-white shadow-sm' 
            : 'text-gray-700 hover:text-gray-900'"
          :style="isActive('/admin/locations') 
            ? 'background-color: #C8102E;' 
            : 'background-color: transparent;'"
          @mouseenter="handleMouseEnter($event, '/admin/locations')"
          @mouseleave="handleMouseLeave($event, '/admin/locations')"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          مدیریت مکان‌ها
        </RouterLink>

        <!-- SEO -->
        <RouterLink
          to="/admin/seo"
          class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
          :class="isActive('/admin/seo') 
            ? 'text-white shadow-sm' 
            : 'text-gray-700 hover:text-gray-900'"
          :style="isActive('/admin/seo') 
            ? 'background-color: #C8102E;' 
            : 'background-color: transparent;'"
          @mouseenter="handleMouseEnter($event, '/admin/seo')"
          @mouseleave="handleMouseLeave($event, '/admin/seo')"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          مدیریت SEO
        </RouterLink>

        <!-- Tickets -->
        <RouterLink
          to="/admin/tickets"
          class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
          :class="isActive('/admin/tickets') 
            ? 'text-white shadow-sm' 
            : 'text-gray-700 hover:text-gray-900'"
          :style="isActive('/admin/tickets') 
            ? 'background-color: #C8102E;' 
            : 'background-color: transparent;'"
          @mouseenter="handleMouseEnter($event, '/admin/tickets')"
          @mouseleave="handleMouseLeave($event, '/admin/tickets')"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          تیکت‌ها
        </RouterLink>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 order-2">
      <header class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="px-6 py-4">
          <h1 class="text-2xl font-bold text-gray-900" dir="rtl">{{ currentTitle }}</h1>
        </div>
      </header>

      <section class="p-6" style="background-color: #F9FAFB;">
        <router-view />
      </section>
    </main>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'AdminLayout',
  setup() {
    const route = useRoute()

    const open = ref({
      users: true,
      businesses: true
    })

    const toggle = (key) => {
      open.value[key] = !open.value[key]
    }

    const currentTitle = computed(() => route.meta?.title || 'مدیریت')

    const isActive = (path) => route.path === path
    const activeStartsWith = (prefix) => route.path.startsWith(prefix)

    const handleMouseEnter = (event, path = null) => {
      if (path && isActive(path)) return
      event.target.style.backgroundColor = 'rgba(200,16,46,0.1)'
    }

    const handleMouseLeave = (event, path = null) => {
      if (path && isActive(path)) return
      event.target.style.backgroundColor = 'transparent'
    }

    return {
      open,
      toggle,
      currentTitle,
      isActive,
      activeStartsWith,
      handleMouseEnter,
      handleMouseLeave
    }
  }
}
</script>
