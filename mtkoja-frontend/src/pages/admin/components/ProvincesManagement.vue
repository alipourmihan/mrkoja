<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">مدیریت استان‌ها</h3>
          <p class="text-sm text-gray-500 mt-1">سازماندهی و مدیریت استان‌های کشور</p>
        </div>
        <button 
          @click="showProvinceForm = true; editingProvince = null; resetProvinceForm()"
          class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن استان
        </button>
      </div>
    </div>
    <div class="p-6">
      <div v-if="provincesLoading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
        <p class="text-gray-600 mt-4">در حال بارگذاری...</p>
      </div>
      <div v-else-if="provinces.length > 0">
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
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ترتیب</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="province in provinces" :key="province.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                      {{ province.name.charAt(0) }}
                    </div>
                    <div class="mr-4">
                      <div class="text-sm font-medium text-gray-900">{{ province.name }}</div>
                      <div class="text-sm text-gray-500">{{ province.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ province.slug || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    province.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ province.is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ province.sort_order || 0 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button 
                    @click="editProvince(province)"
                    class="text-blue-600 hover:text-blue-900 ml-4"
                  >
                    ویرایش
                  </button>
                  <button 
                    @click="deleteProvince(province.id)"
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
            v-for="province in provinces" 
            :key="province.id"
            class="bg-white border border-purple-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white text-sm font-medium">
                  {{ province.name.charAt(0) }}
                </div>
                <div class="mr-3">
                  <h3 class="text-sm font-medium text-gray-900">{{ province.name }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ province.slug }}</p>
                </div>
              </div>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                استان
              </span>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">Slug:</span>
                <span class="text-gray-900 font-medium">{{ province.slug || '-' }}</span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">وضعیت:</span>
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  province.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ province.is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">ترتیب:</span>
                <span class="text-gray-900 font-medium">{{ province.sort_order || 0 }}</span>
              </div>

              <div v-if="province.name_en" class="flex items-center justify-between text-xs">
                <span class="text-gray-500">انگلیسی:</span>
                <span class="text-gray-900 font-medium">{{ province.name_en }}</span>
              </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
              <button 
                @click="editProvince(province)"
                class="flex-1 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors"
              >
                ویرایش
              </button>
              <button 
                @click="deleteProvince(province.id)"
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
          <span class="text-gray-400 text-2xl">🏛️</span>
        </div>
        <h3 class="text-lg font-medium text-gray-600 mb-2">هیچ استانی یافت نشد</h3>
        <p class="text-gray-500 mb-4">برای شروع، اولین استان خود را اضافه کنید</p>
        <button 
          @click="showProvinceForm = true; editingProvince = null; resetProvinceForm()"
          class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن استان
        </button>
      </div>
    </div>
  </div>

  <!-- Province Form Modal -->
  <div v-if="showProvinceForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingProvince ? 'ویرایش استان' : 'اضافه کردن استان جدید' }}
        </h3>
        
        <form @submit.prevent="saveProvince" class="space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام استان *</label>
              <input
                v-model="provinceForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                placeholder="نام استان"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نام انگلیسی</label>
              <input
                v-model="provinceForm.name_en"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                placeholder="نام انگلیسی استان"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input
              v-model="provinceForm.slug"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="slug-استان"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات</label>
            <textarea
              v-model="provinceForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="توضیحات استان"
            ></textarea>
          </div>

          <!-- SEO Information -->
          <div class="border-t pt-4">
            <h4 class="text-md font-medium text-gray-900 mb-3">اطلاعات SEO</h4>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input
                  v-model="provinceForm.meta_title"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="عنوان متا"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea
                  v-model="provinceForm.meta_description"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="توضیحات متا"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                <input
                  v-model="provinceForm.meta_keywords"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
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
                  v-model="provinceForm.latitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="35.6892"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">طول جغرافیایی</label>
                <input
                  v-model="provinceForm.longitude"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
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
                  v-model="provinceForm.is_active"
                  type="checkbox"
                  id="is_active"
                  class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                />
                <label for="is_active" class="mr-2 block text-sm text-gray-900">
                  فعال
                </label>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ترتیب نمایش</label>
                <input
                  v-model="provinceForm.sort_order"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="0"
                />
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button
              type="button"
              @click="showProvinceForm = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            >
              انصراف
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 disabled:opacity-50"
            >
              {{ saving ? 'در حال ذخیره...' : (editingProvince ? 'بروزرسانی' : 'ذخیره') }}
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

const API_BASE_URL = 'http://localhost:8000/api'

// State
const provinces = ref([])
const provincesLoading = ref(false)
const showProvinceForm = ref(false)
const editingProvince = ref(null)
const viewMode = ref('table')
const saving = ref(false)

const provinceForm = ref({
  name: '',
  slug: '',
  name_en: '',
  description: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  latitude: '',
  longitude: '',
  is_active: true,
  sort_order: 0
})

// Methods
const fetchProvinces = async () => {
  provincesLoading.value = true
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces`)
    provinces.value = response.data.provinces || response.data
    console.log('Provinces loaded:', provinces.value)
  } catch (error) {
    console.error('Error fetching provinces:', error)
    alert('خطا در بارگذاری استان‌ها: ' + error.message)
  } finally {
    provincesLoading.value = false
  }
}

const editProvince = (province) => {
  editingProvince.value = province
  provinceForm.value = {
    name: province.name || '',
    slug: province.slug || '',
    name_en: province.name_en || '',
    description: province.description || '',
    meta_title: province.meta_title || '',
    meta_description: province.meta_description || '',
    meta_keywords: province.meta_keywords || '',
    latitude: province.latitude || '',
    longitude: province.longitude || '',
    is_active: province.is_active,
    sort_order: province.sort_order || 0
  }
  showProvinceForm.value = true
}

const deleteProvince = async (id) => {
  if (confirm('آیا مطمئن هستید که می‌خواهید این استان را حذف کنید؟')) {
    try {
      await axios.delete(`${API_BASE_URL}/provinces/${id}`)
      await fetchProvinces()
      alert('استان با موفقیت حذف شد')
    } catch (error) {
      console.error('Error deleting province:', error)
      alert('خطا در حذف استان: ' + error.message)
    }
  }
}

const saveProvince = async () => {
  saving.value = true
  try {
    const data = {
      name: provinceForm.value.name,
      slug: provinceForm.value.slug,
      name_en: provinceForm.value.name_en,
      description: provinceForm.value.description,
      meta_title: provinceForm.value.meta_title,
      meta_description: provinceForm.value.meta_description,
      meta_keywords: provinceForm.value.meta_keywords,
      latitude: provinceForm.value.latitude ? parseFloat(provinceForm.value.latitude) : null,
      longitude: provinceForm.value.longitude ? parseFloat(provinceForm.value.longitude) : null,
      is_active: provinceForm.value.is_active,
      sort_order: parseInt(provinceForm.value.sort_order) || 0
    }

    if (editingProvince.value) {
      await axios.put(`${API_BASE_URL}/provinces/${editingProvince.value.id}`, data)
      alert('استان با موفقیت ویرایش شد')
    } else {
      await axios.post(`${API_BASE_URL}/provinces`, data)
      alert('استان با موفقیت اضافه شد')
    }

    showProvinceForm.value = false
    await fetchProvinces()
  } catch (error) {
    console.error('Error saving province:', error)
    alert('خطا در ذخیره استان: ' + error.message)
  } finally {
    saving.value = false
  }
}

const resetProvinceForm = () => {
  provinceForm.value = {
    name: '',
    slug: '',
    name_en: '',
    description: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    latitude: '',
    longitude: '',
    is_active: true,
    sort_order: 0
  }
  editingProvince.value = null
}

// Lifecycle
onMounted(() => {
  fetchProvinces()
})
</script>
