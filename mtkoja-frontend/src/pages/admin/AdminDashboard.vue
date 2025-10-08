<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <AdminSidebar 
      :active-section="activeSection" 
      @change-section="changeSection" 
    />

    <!-- Main Content -->
    <div class="flex-1 mr-64">
      <div class="p-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">{{ getSectionTitle() }}</h1>
          <p class="mt-2 text-gray-600">{{ getSectionDescription() }}</p>
        </div>

        <!-- Dashboard Content -->
        <div v-if="activeSection === 'dashboard'">
          <DashboardStats />
        </div>

        <!-- Users Management -->
        <div v-else-if="activeSection === 'users'">
          <UsersManagement />
        </div>

        <!-- All Businesses Management -->
        <div v-else-if="activeSection === 'all-businesses'">
          <AllBusinessesManagement />
        </div>

        <!-- My Businesses Management -->
        <div v-else-if="activeSection === 'my-businesses'">
          <MyBusinessesManagement />
        </div>

        <!-- Categories Management -->
        <div v-else-if="activeSection === 'categories'">
          <CategoriesManagement />
        </div>

        <!-- Location Management -->
        <div v-else-if="activeSection === 'provinces'">
          <ProvincesManagement />
        </div>

        <div v-else-if="activeSection === 'cities'">
          <CitiesManagement />
        </div>

        <div v-else-if="activeSection === 'neighborhoods'">
          <NeighborhoodsManagement />
        </div>


        <!-- SEO Management -->
        <div v-else-if="activeSection === 'seo-management'">
          <SeoManagement />
        </div>

        <!-- Add Business -->
        <div v-else-if="activeSection === 'add-business'">
          <AdminAddBusiness />
        </div>

        <!-- Other Sections Placeholder -->
        <div v-else class="bg-white shadow rounded-lg p-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ getSectionTitle() }}</h3>
            <p class="text-gray-500">این بخش به زودی اضافه خواهد شد</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AdminSidebar from './components/AdminSidebar.vue'
import CategoriesManagement from './components/CategoriesManagement.vue'
import ProvincesManagement from './components/ProvincesManagement.vue'
import CitiesManagement from './components/CitiesManagement.vue'
import NeighborhoodsManagement from './components/NeighborhoodsManagement.vue'
import LocationManagement from './components/LocationManagement.vue'
import DashboardStats from './components/DashboardStats.vue'
import UsersManagement from './components/UsersManagement.vue'
import AllBusinessesManagement from './components/AllBusinessesManagement.vue'
import MyBusinessesManagement from './components/MyBusinessesManagement.vue'
import SeoManagement from './components/SeoManagement.vue'
import AdminAddBusiness from './components/AdminAddBusiness.vue'

// State
const activeSection = ref('dashboard')

// Methods
const changeSection = (section) => {
  activeSection.value = section
}

const getSectionTitle = () => {
  const titles = {
    'dashboard': 'داشبورد',
    'users': 'مدیریت کاربران',
    'categories': 'مدیریت دسته‌بندی‌ها',
    'provinces': 'مدیریت استان‌ها',
    'cities': 'مدیریت شهرها',
    'neighborhoods': 'مدیریت محله‌ها',
    'business-owners': 'صاحبان کسب و کار',
    'documents': 'اسناد و مدارک',
    'violations': 'تخلفات',
    'sales-experts': 'کارشناسان فروش',
    'my-businesses': 'کسب‌وکارهای من',
    'all-businesses': 'تمام کسب و کارها',
    'add-business': 'اضافه کردن کسب و کار',
    'features': 'ویژگی ها',
    'tickets': 'تیکت ها',
    'seo-pages': 'صفحات سئو',
    'seo-management': 'مدیریت SEO'
  }
  return titles[activeSection.value] || 'بخش نامشخص'
}

const getSectionDescription = () => {
  const descriptions = {
    'dashboard': 'نمای کلی از آمار و اطلاعات سیستم',
    'users': 'مدیریت کاربران سیستم',
    'categories': 'مدیریت دسته‌بندی‌ها با قابلیت‌های SEO',
    'provinces': 'مدیریت استان‌های کشور',
    'cities': 'مدیریت شهرهای استان‌ها',
    'neighborhoods': 'مدیریت محله‌های شهرها',
    'business-owners': 'مدیریت صاحبان کسب و کار',
    'documents': 'مدیریت اسناد و مدارک کاربران',
    'violations': 'مدیریت تخلفات و گزارشات',
    'sales-experts': 'مدیریت کارشناسان فروش',
    'my-businesses': 'مشاهده و مدیریت کسب‌وکارهای شما',
    'all-businesses': 'مشاهده و مدیریت تمام کسب و کارها',
    'add-business': 'ثبت کسب و کار جدید',
    'features': 'مدیریت ویژگی های کسب و کار',
    'tickets': 'مدیریت تیکت ها و پشتیبانی',
    'seo-pages': 'مدیریت صفحات سئو سفارشی',
    'seo-management': 'مدیریت کامل سئو کسب‌وکارها، دسته‌بندی‌ها و صفحات'
  }
  return descriptions[activeSection.value] || 'توضیحات بخش'
}
</script>
