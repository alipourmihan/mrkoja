<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">مدیریت محله‌ها</h3>
          <p class="text-sm text-gray-500 mt-1">سازماندهی و مدیریت محله‌های شهرها</p>
        </div>
        <button 
          @click="showNeighborhoodForm = true; editingNeighborhood = null; resetNeighborhoodForm()"
          class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن محله
        </button>
      </div>
    </div>
    <div class="p-6">
      <div v-if="neighborhoodsLoading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        <p class="text-gray-600 mt-4">در حال بارگذاری...</p>
      </div>
      <div v-else-if="neighborhoods.length > 0">
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
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شهر</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">استان</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="neighborhood in neighborhoods" :key="neighborhood.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                      {{ neighborhood.name.charAt(0) }}
                    </div>
                    <div class="mr-4">
                      <div class="text-sm font-medium text-gray-900">{{ neighborhood.name }}</div>
                      <div class="text-sm text-gray-500">{{ neighborhood.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ neighborhood.city ? neighborhood.city.name : 'نامشخص' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ neighborhood.city && neighborhood.city.province ? neighborhood.city.province.name : 'نامشخص' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    neighborhood.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ neighborhood.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button 
                    @click="editNeighborhood(neighborhood)"
                    class="text-blue-600 hover:text-blue-900 ml-4"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteNeighborhood(neighborhood.id)"
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
            v-for="neighborhood in neighborhoods" 
            :key="neighborhood.id"
            class="bg-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                  {{ neighborhood.name.charAt(0) }}
                </div>
                <div class="mr-3">
                  <h3 class="text-sm font-medium text-gray-900">{{ neighborhood.name }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ neighborhood.slug }}</p>
                </div>
              </div>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                محله
              </span>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">شهر:</span>
                <span class="text-gray-900 font-medium">{{ neighborhood.city ? neighborhood.city.name : 'نامشخص' }}</span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">استان:</span>
                <span class="text-gray-900 font-medium">{{ neighborhood.city && neighborhood.city.province ? neighborhood.city.province.name : 'نامشخص' }}</span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">وضعیت:</span>
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  neighborhood.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ neighborhood.is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
              <button 
                @click="editNeighborhood(neighborhood)"
                class="flex-1 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors"
              >
                ویرایش
              </button>
              <button 
                @click="deleteNeighborhood(neighborhood.id)"
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
          <span class="text-gray-400 text-2xl">🏘️</span>
        </div>
        <h3 class="text-lg font-medium text-gray-600 mb-2">هیچ محله‌ای یافت نشد</h3>
        <p class="text-gray-500 mb-4">برای شروع، اولین محله خود را اضافه کنید</p>
        <button 
          @click="showNeighborhoodForm = true; editingNeighborhood = null; resetNeighborhoodForm()"
          class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن محله
        </button>
      </div>
    </div>
  </div>

  <!-- Neighborhood Form Modal -->
  <div v-if="showNeighborhoodForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingNeighborhood ? 'ویرایش محله' : 'اضافه کردن محله جدید' }}
        </h3>
        
        <form @submit.prevent="saveNeighborhood" class="space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام محله *</label>
              <input
                v-model="neighborhoodForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="نام محله"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام انگلیسی</label>
              <input
                v-model="neighborhoodForm.name_en"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="نام انگلیسی محله"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input
              v-model="neighborhoodForm.slug"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="slug-محله"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات</label>
            <textarea
              v-model="neighborhoodForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="توضیحات محله"
            ></textarea>
          </div>

          <!-- City Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">شهر *</label>
            <select
              v-model="neighborhoodForm.city_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <option value="">انتخاب شهر</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.name }} - {{ city.province ? city.province.name : '' }}
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
                  v-model="neighborhoodForm.meta_title"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="عنوان متا"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea
                  v-model="neighborhoodForm.meta_description"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="توضیحات متا"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                <input
                  v-model="neighborhoodForm.meta_keywords"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
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
                  v-model="neighborhoodForm.latitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="35.6892"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">طول جغرافیایی</label>
                <input
                  v-model="neighborhoodForm.longitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
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
                  v-model="neighborhoodForm.is_active"
                  type="checkbox"
                  id="is_active"
                  class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                />
                <label for="is_active" class="mr-2 block text-sm text-gray-900">
                  فعال
                </label>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ترتیب نمایش</label>
                <input
                  v-model="neighborhoodForm.sort_order"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="0"
                />
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button
              type="button"
              @click="showNeighborhoodForm = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              انصراف
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50"
            >
              {{ saving ? 'در حال ذخیره...' : (editingNeighborhood ? 'بروزرسانی' : 'ذخیره') }}
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

const API_BASE_URL = 'https://mrkoja.com/api'

// State
const neighborhoods = ref([])
const cities = ref([])
const neighborhoodsLoading = ref(false)
const showNeighborhoodForm = ref(false)
const editingNeighborhood = ref(null)
const viewMode = ref('table')
const saving = ref(false)

const neighborhoodForm = ref({
  name: '',
  slug: '',
  name_en: '',
  description: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  latitude: '',
  longitude: '',
  city_id: '',
  is_active: true,
  sort_order: 0
})

// Methods
const fetchNeighborhoods = async () => {
  neighborhoodsLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/neighborhoods`)
    // Handle different response structures safely
    if (response.data.neighborhoods) {
      neighborhoods.value = response.data.neighborhoods
    } else if (Array.isArray(response.data)) {
      neighborhoods.value = response.data
    } else {
      neighborhoods.value = []
    }
    console.log('Neighborhoods loaded:', neighborhoods.value)
  } catch (error) {
    console.error('Error fetching neighborhoods:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در بارگذاری محله‌ها: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    neighborhoodsLoading.value = false
  }
}

const fetchCities = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/cities`)
    cities.value = response.data.cities || response.data
  } catch (error) {
    console.error('Error fetching cities:', error)
  }
}

const editNeighborhood = (neighborhood) => {
  editingNeighborhood.value = neighborhood
  neighborhoodForm.value = {
    name: neighborhood.name || '',
    slug: neighborhood.slug || '',
    name_en: neighborhood.name_en || '',
    description: neighborhood.description || '',
    meta_title: neighborhood.meta_title || '',
    meta_description: neighborhood.meta_description || '',
    meta_keywords: neighborhood.meta_keywords || '',
    latitude: neighborhood.latitude || '',
    longitude: neighborhood.longitude || '',
    city_id: neighborhood.city_id || '',
    is_active: neighborhood.is_active,
    sort_order: neighborhood.sort_order || 0
  }
  showNeighborhoodForm.value = true
}

const deleteNeighborhood = async (id) => {
  if (confirm('آیا مطمئن هستید که می‌خواهید این محله را حذف کنید؟')) {
    try {
      await axios.delete(`${API_BASE_URL}/neighborhoods/${id}`)
      await fetchNeighborhoods()
      alert('محله با موفقیت حذف شد')
    } catch (error) {
      console.error('Error deleting neighborhood:', error)
      alert('خطا در حذف محله: ' + error.message)
    }
  }
}

const saveNeighborhood = async () => {
  saving.value = true
  try {
    const data = {
      name: neighborhoodForm.value.name,
      slug: neighborhoodForm.value.slug,
      name_en: neighborhoodForm.value.name_en,
      description: neighborhoodForm.value.description,
      meta_title: neighborhoodForm.value.meta_title,
      meta_description: neighborhoodForm.value.meta_description,
      meta_keywords: neighborhoodForm.value.meta_keywords,
      latitude: neighborhoodForm.value.latitude ? parseFloat(neighborhoodForm.value.latitude) : null,
      longitude: neighborhoodForm.value.longitude ? parseFloat(neighborhoodForm.value.longitude) : null,
      city_id: neighborhoodForm.value.city_id,
      is_active: neighborhoodForm.value.is_active,
      sort_order: parseInt(neighborhoodForm.value.sort_order) || 0
    }

    if (editingNeighborhood.value) {
      await axios.put(`${API_BASE_URL}/neighborhoods/${editingNeighborhood.value.id}`, data)
      alert('محله با موفقیت ویرایش شد')
    } else {
      await axios.post(`${API_BASE_URL}/neighborhoods`, data)
      alert('محله با موفقیت اضافه شد')
    }

    showNeighborhoodForm.value = false
    await fetchNeighborhoods()
  } catch (error) {
    console.error('Error saving neighborhood:', error)
    alert('خطا در ذخیره محله: ' + error.message)
  } finally {
    saving.value = false
  }
}

const resetNeighborhoodForm = () => {
  neighborhoodForm.value = {
    name: '',
    slug: '',
    name_en: '',
    description: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    latitude: '',
    longitude: '',
    city_id: '',
    is_active: true,
    sort_order: 0
  }
  editingNeighborhood.value = null
}

// Lifecycle
onMounted(() => {
  fetchNeighborhoods()
  fetchCities()
})
</script>
