<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">مدیریت شهر و استان</h3>
        <div class="flex space-x-3 space-x-reverse">
          <button 
            @click="locationActiveTab = 'provinces'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-md',
              locationActiveTab === 'provinces' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            ]"
          >
            استان‌ها
          </button>
          <button 
            @click="locationActiveTab = 'cities'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-md',
              locationActiveTab === 'cities' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            ]"
          >
            شهرها
          </button>
          <button 
            @click="locationActiveTab = 'neighborhoods'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-md',
              locationActiveTab === 'neighborhoods' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            ]"
          >
            محله‌ها
          </button>
        </div>
      </div>
      <div class="flex justify-end">
        <button 
          @click="showLocationForm = true; editingLocation = null; resetLocationForm()"
          class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
        >
          اضافه کردن {{ getLocationTypeLabel() }} جدید
        </button>
      </div>
    </div>
    <div class="p-6">
      <!-- Provinces Tab -->
      <div v-if="locationActiveTab === 'provinces'">
        <div v-if="provincesLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div v-else-if="provinces.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام انگلیسی</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کد</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="province in provinces" :key="province.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ province.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ province.name_en }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ province.code }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    province.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ province.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                  <button 
                    @click="editLocation(province, 'province')"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteLocation(province.id, 'province')"
                    class="text-red-600 hover:text-red-900"
                  >
                    حذف
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          هیچ استان‌ی یافت نشد
        </div>
      </div>

      <!-- Cities Tab -->
      <div v-if="locationActiveTab === 'cities'">
        <div v-if="citiesLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div v-else-if="cities.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">استان</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام انگلیسی</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="city in cities" :key="city.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ city.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.province?.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.name_en }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    city.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ city.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                  <button 
                    @click="editLocation(city, 'city')"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteLocation(city.id, 'city')"
                    class="text-red-600 hover:text-red-900"
                  >
                    حذف
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          هیچ شهری یافت نشد
        </div>
      </div>

      <!-- Neighborhoods Tab -->
      <div v-if="locationActiveTab === 'neighborhoods'">
        <div v-if="neighborhoodsLoading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div v-else-if="neighborhoods.length > 0" class="overflow-x-auto">
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
              <tr v-for="neighborhood in neighborhoods" :key="neighborhood.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ neighborhood.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ neighborhood.city?.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ neighborhood.city?.province?.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    neighborhood.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ neighborhood.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                  <button 
                    @click="editLocation(neighborhood, 'neighborhood')"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteLocation(neighborhood.id, 'neighborhood')"
                    class="text-red-600 hover:text-red-900"
                  >
                    حذف
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          هیچ محله‌ای یافت نشد
        </div>
      </div>
    </div>
  </div>

  <!-- Location Form Modal -->
  <div v-if="showLocationForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex items-center justify-between pb-4 border-b">
          <h3 class="text-lg font-medium text-gray-900">
            {{ editingLocation ? `ویرایش ${getLocationTypeLabel()}` : `اضافه کردن ${getLocationTypeLabel()} جدید` }}
          </h3>
          <button @click="showLocationForm = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="saveLocationForm" class="mt-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نام {{ getLocationTypeLabel() }} *</label>
            <input 
              v-model="locationForm.name" 
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :placeholder="`نام ${getLocationTypeLabel()} را وارد کنید`"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نام انگلیسی</label>
            <input 
              v-model="locationForm.name_en" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="English name"
            >
          </div>
          
          <!-- Province selection for cities -->
          <div v-if="locationActiveTab === 'cities'">
            <label class="block text-sm font-medium text-gray-700 mb-2">استان *</label>
            <select 
              v-model="locationForm.province_id" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">انتخاب استان</option>
              <option v-for="province in provinces" :key="province.id" :value="province.id">
                {{ province.name }}
              </option>
            </select>
          </div>
          
          <!-- City selection for neighborhoods -->
          <div v-if="locationActiveTab === 'neighborhoods'">
            <label class="block text-sm font-medium text-gray-700 mb-2">شهر *</label>
            <select 
              v-model="locationForm.city_id" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">انتخاب شهر</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.name }} ({{ city.province?.name }})
              </option>
            </select>
          </div>
          
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات (برای سئو)</label>
            <textarea 
              v-model="locationForm.description" 
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :placeholder="`توضیحات ${getLocationTypeLabel()} برای سئو`"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">کلمه کلیدی</label>
            <input 
              v-model="locationForm.meta_keywords" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="کلمات کلیدی با کاما جدا شده"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">متا تایتل</label>
            <input 
              v-model="locationForm.meta_title" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="عنوان متا برای سئو"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">متا دیسکریپشن</label>
            <textarea 
              v-model="locationForm.meta_description" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="توضیحات متا برای سئو"
            ></textarea>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">عرض جغرافیایی</label>
              <input 
                v-model="locationForm.latitude" 
                type="number" 
                step="0.000001"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="35.6892"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">طول جغرافیایی</label>
              <input 
                v-model="locationForm.longitude" 
                type="number" 
                step="0.000001"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="51.3890"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">ترتیب نمایش</label>
              <input 
                v-model="locationForm.sort_order" 
                type="number" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0"
              >
            </div>
          </div>
          
          <div class="flex items-center">
            <input 
              v-model="locationForm.is_active" 
              type="checkbox" 
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label class="mr-2 block text-sm text-gray-900">فعال</label>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
            <button 
              type="button"
              @click="showLocationForm = false"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
            >
              انصراف
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              {{ editingLocation ? 'ویرایش' : 'اضافه کردن' }}
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
const locationActiveTab = ref('provinces')
const provinces = ref([])
const cities = ref([])
const neighborhoods = ref([])
const provincesLoading = ref(false)
const citiesLoading = ref(false)
const neighborhoodsLoading = ref(false)
const showLocationForm = ref(false)
const editingLocation = ref(null)
const locationForm = ref({
  name: '',
  name_en: '',
  province_id: '',
  city_id: '',
  description: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  latitude: '',
  longitude: '',
  sort_order: 0,
  is_active: true
})

// Methods
const fetchProvinces = async () => {
  provincesLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces`)
    provinces.value = response.data.provinces
  } catch (error) {
    console.error('Error fetching provinces:', error)
  } finally {
    provincesLoading.value = false
  }
}

const fetchCities = async () => {
  citiesLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/cities`)
    cities.value = response.data.cities
  } catch (error) {
    console.error('Error fetching cities:', error)
  } finally {
    citiesLoading.value = false
  }
}

const fetchNeighborhoods = async () => {
  neighborhoodsLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/neighborhoods`)
    neighborhoods.value = response.data.neighborhoods
  } catch (error) {
    console.error('Error fetching neighborhoods:', error)
  } finally {
    neighborhoodsLoading.value = false
  }
}

const getLocationTypeLabel = () => {
  const labels = {
    'provinces': 'استان',
    'cities': 'شهر',
    'neighborhoods': 'محله'
  }
  return labels[locationActiveTab.value] || 'مکان'
}

const resetLocationForm = () => {
  locationForm.value = {
    name: '',
    name_en: '',
    province_id: '',
    city_id: '',
    description: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    latitude: '',
    longitude: '',
    sort_order: 0,
    is_active: true
  }
}

const editLocation = (location, type) => {
  editingLocation.value = location
  locationForm.value = { ...location }
  showLocationForm.value = true
  locationActiveTab.value = type + 's' // Convert 'province' to 'provinces'
}

const saveLocationForm = async () => {
  try {
    const token = localStorage.getItem('token')
    const endpoint = locationActiveTab.value.slice(0, -1) // Remove 's' from 'provinces'
    const url = editingLocation.value 
      ? `${API_BASE_URL}/${endpoint}s/${editingLocation.value.id}`
      : `${API_BASE_URL}/${endpoint}s`
    
    const method = editingLocation.value ? 'PUT' : 'POST'
    
    const response = await axios({
      method,
      url,
      data: locationForm.value,
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    showLocationForm.value = false
    resetLocationForm()
    
    // Refresh the appropriate data
    if (locationActiveTab.value === 'provinces') {
      fetchProvinces()
    } else if (locationActiveTab.value === 'cities') {
      fetchCities()
    } else if (locationActiveTab.value === 'neighborhoods') {
      fetchNeighborhoods()
    }
    
    alert(editingLocation.value ? `${getLocationTypeLabel()} با موفقیت ویرایش شد` : `${getLocationTypeLabel()} با موفقیت اضافه شد`)
  } catch (error) {
    console.error('Error saving location:', error)
    alert(`خطا در ذخیره ${getLocationTypeLabel()}`)
  }
}

const deleteLocation = async (id, type) => {
  if (!confirm(`آیا از حذف این ${getLocationTypeLabel()} اطمینان دارید؟`)) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`${API_BASE_URL}/${type}s/${id}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    // Refresh the appropriate data
    if (type === 'province') {
      fetchProvinces()
    } else if (type === 'city') {
      fetchCities()
    } else if (type === 'neighborhood') {
      fetchNeighborhoods()
    }
    
    alert(`${getLocationTypeLabel()} با موفقیت حذف شد`)
  } catch (error) {
    console.error('Error deleting location:', error)
    alert(`خطا در حذف ${getLocationTypeLabel()}`)
  }
}

onMounted(() => {
  fetchProvinces()
  fetchCities()
  fetchNeighborhoods()
})
</script>

