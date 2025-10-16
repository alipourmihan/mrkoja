<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">مدیریت شهرها</h3>
          <p class="text-sm text-gray-500 mt-1">سازماندهی و مدیریت شهرهای کشور</p>
        </div>
        <button 
          @click="showCityForm = true; editingCity = null; resetCityForm()"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن شهر
        </button>
      </div>
    </div>
    <div class="p-6">
      <div v-if="citiesLoading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="text-gray-600 mt-4">در حال بارگذاری...</p>
      </div>
      <div v-else-if="cities.length > 0">
        <!-- View Toggle -->
        <div class="mb-4 flex justify-end">
          <div class="flex bg-gray-100 rounded-lg p-1">
            <button 
              @click="viewMode = 'table'"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center',
                viewMode === 'table' 
                  ? 'bg-white text-gray-900 shadow-sm' 
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <span class="ml-1">📊</span>
              جدول
            </button>
            <button 
              @click="viewMode = 'cards'"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center',
                viewMode === 'cards' 
                  ? 'bg-white text-gray-900 shadow-sm' 
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <span class="ml-1">📋</span>
              کارت
            </button>
          </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">استان</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">محله‌ها</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="city in cities" :key="city.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                      {{ city.name.charAt(0) }}
                    </div>
                    <div class="mr-4">
                      <div class="text-sm font-medium text-gray-900">{{ city.name }}</div>
                      <div class="text-sm text-gray-500">{{ city.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ city.province ? city.province.name : 'نامشخص' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    city.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ city.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ city.neighborhoods_count || 0 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button 
                    @click="editCity(city)"
                    class="text-blue-600 hover:text-blue-900 ml-4"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteCity(city.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    حذف
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Card View -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="city in cities" 
            :key="city.id"
            class="bg-white border border-blue-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                  {{ city.name.charAt(0) }}
                </div>
                <div class="mr-3">
                  <h3 class="text-sm font-medium text-gray-900">{{ city.name }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ city.slug }}</p>
                </div>
              </div>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                شهر
              </span>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">استان:</span>
                <span class="text-gray-900 font-medium">{{ city.province ? city.province.name : 'نامشخص' }}</span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">وضعیت:</span>
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  city.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ city.is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">محله‌ها:</span>
                <span class="text-gray-900 font-medium">{{ city.neighborhoods_count || 0 }}</span>
              </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
              <button 
                @click="editCity(city)"
                class="flex-1 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors"
              >
                ویرایش
              </button>
              <button 
                @click="deleteCity(city.id)"
                class="flex-1 bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition-colors"
              >
                حذف
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-8 text-gray-500">
        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-gray-400 text-2xl">🏙️</span>
        </div>
        <h3 class="text-lg font-medium text-gray-600 mb-2">هیچ شهری یافت نشد</h3>
        <p class="text-gray-500 mb-4">برای شروع، اولین شهر خود را اضافه کنید</p>
        <button 
          @click="showCityForm = true; editingCity = null; resetCityForm()"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن شهر
        </button>
      </div>
    </div>
  </div>

  <!-- City Form Modal -->
  <div v-if="showCityForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingCity ? 'ویرایش شهر' : 'اضافه کردن شهر جدید' }}
        </h3>
        
        <form @submit.prevent="saveCity" class="space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام شهر *</label>
              <input
                v-model="cityForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="نام شهر"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام انگلیسی</label>
              <input
                v-model="cityForm.name_en"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="نام انگلیسی شهر"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input
              v-model="cityForm.slug"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="slug-شهر"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات</label>
            <textarea
              v-model="cityForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="توضیحات شهر"
            ></textarea>
          </div>

          <!-- Province Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">استان *</label>
            <select
              v-model="cityForm.province_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">انتخاب استان</option>
              <option v-for="province in provinces" :key="province.id" :value="province.id">
                {{ province.name }}
              </option>
            </select>
          </div>

          <!-- SEO Information -->
          <div class="border-t pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-3">اطلاعات SEO</h4>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input
                  v-model="cityForm.meta_title"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="عنوان متا"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea
                  v-model="cityForm.meta_description"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="توضیحات متا"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                <input
                  v-model="cityForm.meta_keywords"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="کلمات کلیدی متا"
                />
              </div>
            </div>
          </div>

          <!-- Location Information -->
          <div class="border-t pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-3">اطلاعات مکانی</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عرض جغرافیایی</label>
                <input
                  v-model="cityForm.latitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="35.6892"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">طول جغرافیایی</label>
                <input
                  v-model="cityForm.longitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="51.3890"
                />
              </div>
            </div>
          </div>

          <!-- Settings -->
          <div class="border-t pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-3">تنظیمات</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center">
                <input
                  v-model="cityForm.is_active"
                  type="checkbox"
                  id="is_active"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label for="is_active" class="mr-2 block text-sm text-gray-900">
                  فعال
                </label>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ترتیب نمایش</label>
                <input
                  v-model="cityForm.sort_order"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="0"
                />
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button
              type="button"
              @click="showCityForm = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              انصراف
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ saving ? 'در حال ذخیره...' : (editingCity ? 'بروزرسانی' : 'ذخیره') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API_BASE_URL = 'https://api.mrkoja.com/api'

// State
const cities = ref([])
const provinces = ref([])
const citiesLoading = ref(false)
const showCityForm = ref(false)
const editingCity = ref(null)
const viewMode = ref('table')
const saving = ref(false)

const cityForm = ref({
  name: '',
  slug: '',
  name_en: '',
  description: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  latitude: '',
  longitude: '',
  province_id: '',
  is_active: true,
  sort_order: 0
})

// Methods
const fetchCities = async () => {
  citiesLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/cities`)
    // Handle different response structures safely
    if (response.data.cities) {
      cities.value = response.data.cities
    } else if (Array.isArray(response.data)) {
      cities.value = response.data
    } else {
      cities.value = []
    }
    console.log('Cities loaded:', cities.value)
  } catch (error) {
    console.error('Error fetching cities:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در بارگذاری شهرها: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    citiesLoading.value = false
  }
}

const fetchProvinces = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces`)
    provinces.value = response.data.provinces || response.data
  } catch (error) {
    console.error('Error fetching provinces:', error)
  }
}

const editCity = (city) => {
  editingCity.value = city
  cityForm.value = {
    name: city.name,
    province_id: city.province_id,
    is_active: city.is_active,
  }
  showCityForm.value = true
}

const deleteCity = async (id) => {
  if (confirm('آیا مطمئن هستید که می‌خواهید این شهر را حذف کنید؟')) {
    try {
      await axios.delete(`${API_BASE_URL}/cities/${id}`)
      await fetchCities()
      alert('شهر با موفقیت حذف شد')
    } catch (error) {
      console.error('Error deleting city:', error)
      alert('خطا در حذف شهر: ' + error.message)
    }
  }
}

const saveCity = async () => {
  saving.value = true
  try {
    const data = {
      name: cityForm.value.name,
      province_id: cityForm.value.province_id,
      is_active: cityForm.value.is_active,
    }

    if (editingCity.value) {
      await axios.put(`${API_BASE_URL}/cities/${editingCity.value.id}`, data)
      alert('شهر با موفقیت ویرایش شد')
    } else {
      await axios.post(`${API_BASE_URL}/cities`, data)
      alert('شهر با موفقیت اضافه شد')
    }

    showCityForm.value = false
    await fetchCities()
  } catch (error) {
    console.error('Error saving city:', error)
    alert('خطا در ذخیره شهر: ' + error.message)
  } finally {
    saving.value = false
  }
}

const resetCityForm = () => {
  cityForm.value = {
    name: '',
    province_id: '',
    is_active: true,
  }
  editingCity.value = null
}

// Lifecycle
onMounted(() => {
  fetchCities()
  fetchProvinces()
})
</script>
