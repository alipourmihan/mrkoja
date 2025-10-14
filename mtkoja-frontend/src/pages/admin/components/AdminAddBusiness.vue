<template>
  <div class="p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">اضافه کردن کسب و کار جدید</h2>
        <p class="text-gray-600">اطلاعات کامل کسب و کار را وارد کنید</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات پایه</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Business Name -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">نام کسب و کار *</label>
              <input
                v-model="formData.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="نام کسب و کار"
              />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات کوتاه *</label>
              <textarea
                v-model="formData.description"
                rows="3"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="توضیحات کوتاه کسب و کار"
              ></textarea>
            </div>

            <!-- Full Description -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات کامل</label>
              <div class="border rounded-md">
                <div class="border-b p-2 bg-gray-50 flex flex-wrap gap-2">
                  <button type="button" @click="execCommand('bold')" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="پررنگ">
                    <strong>B</strong>
                  </button>
                  <button type="button" @click="execCommand('italic')" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="کج">
                    <em>I</em>
                  </button>
                  <button type="button" @click="execCommand('underline')" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="زیرخط">
                    <u>U</u>
                  </button>
                  <div class="w-px h-6 bg-gray-300 mx-1"></div>
                  <button type="button" @click="execCommand('insertUnorderedList')" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="لیست">
                    • لیست
                  </button>
                  <button type="button" @click="execCommand('insertOrderedList')" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="لیست شماره‌دار">
                    1. لیست
                  </button>
                  <div class="w-px h-6 bg-gray-300 mx-1"></div>
                  <button type="button" @click="insertImage" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="تصویر">
                    🖼️ تصویر
                  </button>
                  <button type="button" @click="insertLink" class="px-3 py-1 text-sm border rounded hover:bg-gray-200" title="لینک">
                    🔗 لینک
                  </button>
                </div>
                <div 
                  ref="descriptionEditor" 
                  contenteditable="true" 
                  @input="updateDescription"
                  class="min-h-[200px] p-4 focus:outline-none"
                  v-html="formData.fullDescription"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Location Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات مکانی</h3>
          
          <div class="space-y-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">انتخاب مکان *</label>
            
            <!-- Province Selection -->
            <div>
              <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <span class="w-2 h-2 bg-purple-500 rounded-full ml-2"></span>
                استان
              </h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <label 
                  v-for="province in provinces" 
                  :key="province.id"
                  class="flex items-center p-4 border-2 border-purple-200 rounded-lg cursor-pointer hover:bg-purple-50 hover:border-purple-300 transition-all duration-200"
                  :class="{ 'bg-purple-100 border-purple-500 shadow-md': formData.province_id === province.id }"
                >
                  <input
                    type="radio"
                    :value="province.id"
                    v-model="formData.province_id"
                    @change="onProvinceChange"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 ml-2"
                  />
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900">{{ province.name }}</span>
                    <span class="text-xs text-gray-500 mt-1">استان</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- City Selection -->
            <div v-if="formData.province_id && cities.length > 0">
              <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <span class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                شهر
              </h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <label 
                  v-for="city in cities" 
                  :key="city.id"
                  class="flex items-center p-4 border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all duration-200"
                  :class="{ 'bg-blue-100 border-blue-500 shadow-md': formData.city_id === city.id }"
                >
                  <input
                    type="radio"
                    :value="city.id"
                    v-model="formData.city_id"
                    @change="onCityChange"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 ml-2"
                  />
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900">{{ city.name }}</span>
                    <span class="text-xs text-gray-500 mt-1">شهر</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Neighborhood Selection -->
            <div v-if="formData.city_id && neighborhoods.length > 0">
              <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full ml-2"></span>
                محله
              </h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <label 
                  v-for="neighborhood in neighborhoods" 
                  :key="neighborhood.id"
                  class="flex items-center p-4 border-2 border-green-200 rounded-lg cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all duration-200"
                  :class="{ 'bg-green-100 border-green-500 shadow-md': formData.neighborhood_id === neighborhood.id }"
                >
                  <input
                    type="radio"
                    :value="neighborhood.id"
                    v-model="formData.neighborhood_id"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 ml-2"
                  />
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900">{{ neighborhood.name }}</span>
                    <span class="text-xs text-gray-500 mt-1">محله</span>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Map Selection -->
          <div class="mt-6">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">انتخاب موقعیت از روی نقشه</label>
              <NeshanMapSearch
                :model-value="selectedLocation"
                @location-selected="setLocation"
              />
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات تماس</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Phone -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">تلفن *</label>
              <input
                v-model="formData.phone"
                type="tel"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="09123456789"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل</label>
              <input
                v-model="formData.email"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="example@email.com"
              />
            </div>

            <!-- Website -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">وب‌سایت</label>
              <input
                v-model="formData.website"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="https://example.com"
              />
            </div>

            <!-- Instagram -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">آدرس اینستاگرام</label>
              <input
                v-model="formData.instagram"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="@username یا https://instagram.com/username"
              />
            </div>

            <!-- WhatsApp -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">شماره واتس‌اپ</label>
              <input
                v-model="formData.whatsapp"
                type="tel"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="09123456789"
              />
            </div>

            <!-- Category -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-3">دسته‌بندی *</label>
              
              <!-- Parent Categories -->
              <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                  <span class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                  دسته‌بندی‌های اصلی
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                  <label 
                    v-for="parentCategory in parentCategories" 
                    :key="parentCategory.id"
                    class="flex items-center p-4 border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-all duration-200"
                    :class="{ 'bg-blue-100 border-blue-500 shadow-md': formData.category_id === parentCategory.id }"
                  >
                    <input
                      type="radio"
                      :value="parentCategory.id"
                      v-model="formData.category_id"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 ml-2"
                    />
                    <div class="flex flex-col">
                      <span class="text-sm font-bold text-gray-900">{{ parentCategory.name }}</span>
                      <span class="text-xs text-gray-500 mt-1">اصلی</span>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Child Categories -->
              <div v-if="parentCategories.length > 0">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                  <span class="w-2 h-2 bg-green-500 rounded-full ml-2"></span>
                  زیردسته‌ها
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                  <label 
                    v-for="parentCategory in parentCategories" 
                    :key="'parent-' + parentCategory.id"
                    class="col-span-full"
                  >
                    <div class="mb-2">
                      <h5 class="text-xs font-medium text-gray-600 mb-2 flex items-center">
                        <span class="w-1 h-1 bg-gray-400 rounded-full ml-2"></span>
                        {{ parentCategory.name }}
                      </h5>
                      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mr-4">
                        <label 
                          v-for="childCategory in getChildCategories(parentCategory.id)" 
                          :key="childCategory.id"
                          class="flex items-center p-3 border border-green-200 rounded-md cursor-pointer hover:bg-green-50 hover:border-green-300 transition-all duration-200"
                          :class="{ 'bg-green-100 border-green-500 shadow-sm': formData.category_id === childCategory.id }"
                        >
                          <input
                            type="radio"
                            :value="childCategory.id"
                            v-model="formData.category_id"
                            class="h-3 w-3 text-green-600 focus:ring-green-500 border-gray-300 ml-2"
                          />
                          <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-800">{{ childCategory.name }}</span>
                            <span class="text-xs text-gray-400">زیردسته</span>
                          </div>
                        </label>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SEO Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات SEO</h3>
          
          <div class="space-y-4">
            <!-- Meta Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">متاتایتل</label>
              <input
                v-model="formData.meta_title"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="عنوان صفحه برای موتورهای جستجو"
              />
            </div>

            <!-- Meta Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">متادیسکریپشن</label>
              <textarea
                v-model="formData.meta_description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="توضیحات صفحه برای موتورهای جستجو"
              ></textarea>
            </div>

            <!-- Keywords -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">کلمات کلیدی (با اینتر جدا کنید)</label>
              <div class="flex flex-wrap gap-2 p-3 border rounded min-h-[40px] bg-gray-50">
                <span v-for="(keyword, index) in keywords" :key="index" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                  {{ keyword }}
                  <button type="button" @click="removeKeyword(index)" class="mr-2 text-blue-600 hover:text-blue-800">×</button>
                </span>
                <input v-model="keywordInput" @keydown.enter.prevent="addKeyword" 
                       placeholder="کلمه کلیدی جدید..." 
                       class="flex-1 min-w-[120px] bg-transparent border-none outline-none" />
              </div>
            </div>

            <!-- Features -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ویژگی‌ها (با اینتر جدا کنید)</label>
              <div class="flex flex-wrap gap-2 p-3 border rounded min-h-[40px] bg-gray-50">
                <span v-for="(feature, index) in features" :key="index" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                  {{ feature }}
                  <button type="button" @click="removeFeature(index)" class="mr-2 text-green-600 hover:text-green-800">×</button>
                </span>
                <input v-model="featureInput" @keydown.enter.prevent="addFeature" 
                       placeholder="ویژگی جدید..." 
                       class="flex-1 min-w-[120px] bg-transparent border-none outline-none" />
              </div>
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">تصاویر</h3>
          
          <div class="space-y-4">
            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">آپلود تصاویر</label>
              <input
                ref="imageInput"
                type="file"
                multiple
                accept="image/*"
                @change="handleImageUpload"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Image Preview -->
            <div v-if="uploadedImages.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-for="(image, index) in uploadedImages" :key="index" class="relative group">
                <img
                  :src="image.url"
                  :alt="image.name"
                  class="w-full h-24 object-cover rounded-lg"
                />
                <button
                  type="button"
                  @click="removeImage(index)"
                  class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                >
                  ×
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Working Hours -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">ساعات کاری</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="day in days" :key="day.id" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">{{ day.name }}</label>
              <div class="flex items-center space-x-2 space-x-reverse">
                <input
                  v-model="workingHours[day.id].active"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <input
                  v-model="workingHours[day.id].start"
                  type="time"
                  :disabled="!workingHours[day.id].active"
                  class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                />
                <span class="text-gray-500">تا</span>
                <input
                  v-model="workingHours[day.id].end"
                  type="time"
                  :disabled="!workingHours[day.id].active"
                  class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Status -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">وضعیت</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت کسب و کار</label>
              <select
                v-model="formData.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="pending">در انتظار تایید</option>
                <option value="approved">تایید شده</option>
                <option value="rejected">رد شده</option>
              </select>
            </div>

            <div class="flex items-center">
              <input
                v-model="formData.is_featured"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label class="mr-2 block text-sm text-gray-900">ویژه</label>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="resetForm"
            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
          >
            پاک کردن فرم
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ loading ? 'در حال ذخیره...' : 'ذخیره کسب و کار' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import NeshanMapSearch from '@/components/NeshanMapSearch.vue'

const API_BASE_URL = 'https://mrkoja.com/api'

// Form data
const formData = ref({
  name: '',
  description: '',
  fullDescription: '',
  address: '',
  phone: '',
  email: '',
  website: '',
  instagram: '',
  whatsapp: '',
  category_id: '',
  province_id: '',
  city_id: '',
  neighborhood_id: '',
  meta_title: '',
  meta_description: '',
  status: 'pending',
  is_featured: false
})

// Selected location from map
const selectedLocation = ref({
  lat: null,
  lng: null,
  address: ''
})

// Location data
const provinces = ref([])
const cities = ref([])
const neighborhoods = ref([])

// Categories
const categories = ref([])

// Keywords and features
const keywords = ref([])
const keywordInput = ref('')
const features = ref([])
const featureInput = ref('')

// Images
const uploadedImages = ref([])
const imageInput = ref(null)

// Working hours
const days = ref([
  { id: 'saturday', name: 'شنبه' },
  { id: 'sunday', name: 'یکشنبه' },
  { id: 'monday', name: 'دوشنبه' },
  { id: 'tuesday', name: 'سه‌شنبه' },
  { id: 'wednesday', name: 'چهارشنبه' },
  { id: 'thursday', name: 'پنج‌شنبه' },
  { id: 'friday', name: 'جمعه' }
])

const workingHours = ref({
  saturday: { active: false, start: '09:00', end: '18:00' },
  sunday: { active: false, start: '09:00', end: '18:00' },
  monday: { active: true, start: '09:00', end: '18:00' },
  tuesday: { active: true, start: '09:00', end: '18:00' },
  wednesday: { active: true, start: '09:00', end: '18:00' },
  thursday: { active: true, start: '09:00', end: '18:00' },
  friday: { active: true, start: '09:00', end: '18:00' }
})

// Editor ref
const descriptionEditor = ref(null)

// Loading state
const loading = ref(false)

// Computed properties
const parentCategories = computed(() => {
  return categories.value.filter(cat => !cat.parent_id)
})

const getChildCategories = (parentId) => {
  return categories.value.filter(cat => cat.parent_id === parentId)
}

// Methods
const fetchProvinces = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/provinces`)
    provinces.value = response.data.provinces || response.data
    console.log('Provinces loaded:', provinces.value)
  } catch (error) {
    console.error('Error fetching provinces:', error)
    alert('خطا در بارگذاری استان‌ها: ' + error.message)
  }
}

const fetchCategories = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/categories`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    categories.value = response.data.categories || response.data || []
    console.log('Categories loaded:', categories.value)
  } catch (error) {
    console.error('Error fetching categories:', error)
    alert('خطا در بارگذاری دسته‌بندی‌ها: ' + (error.response?.data?.message || error.message))
  }
}

const onProvinceChange = async () => {
  cities.value = []
  neighborhoods.value = []
  formData.value.city_id = ''
  formData.value.neighborhood_id = ''
  
  if (formData.value.province_id) {
    try {
      const response = await axios.get(`${API_BASE_URL}/provinces/${formData.value.province_id}/cities`)
      cities.value = response.data.cities || response.data
      console.log('Cities loaded:', cities.value)
    } catch (error) {
      console.error('Error fetching cities:', error)
      alert('خطا در بارگذاری شهرها: ' + error.message)
    }
  }
}

const onCityChange = async () => {
  neighborhoods.value = []
  formData.value.neighborhood_id = ''
  
  if (formData.value.city_id) {
    try {
      const response = await axios.get(`${API_BASE_URL}/cities/${formData.value.city_id}/neighborhoods`)
      neighborhoods.value = response.data.neighborhoods || response.data
      console.log('Neighborhoods loaded:', neighborhoods.value)
    } catch (error) {
      console.error('Error fetching neighborhoods:', error)
      alert('خطا در بارگذاری محله‌ها: ' + error.message)
    }
  }
}

// Keywords management
const addKeyword = () => {
  const keyword = keywordInput.value.trim()
  if (keyword && !keywords.value.includes(keyword)) {
    keywords.value.push(keyword)
    keywordInput.value = ''
  }
}

const removeKeyword = (index) => {
  keywords.value.splice(index, 1)
}

// Features management
const addFeature = () => {
  const feature = featureInput.value.trim()
  if (feature && !features.value.includes(feature)) {
    features.value.push(feature)
    featureInput.value = ''
  }
}

const removeFeature = (index) => {
  features.value.splice(index, 1)
}

// Image management
const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        uploadedImages.value.push({
          file: file,
          name: file.name,
          url: e.target.result
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  uploadedImages.value.splice(index, 1)
}

// Editor functions
const execCommand = (command, value = null) => {
  document.execCommand(command, false, value)
  descriptionEditor.value.focus()
}

const insertImage = () => {
  const url = prompt('آدرس تصویر را وارد کنید:')
  if (url) {
    execCommand('insertImage', url)
  }
}

const insertLink = () => {
  const url = prompt('آدرس لینک را وارد کنید:')
  if (url) {
    execCommand('createLink', url)
  }
}

const updateDescription = () => {
  formData.value.fullDescription = descriptionEditor.value.innerHTML
}

// Map location selection
const setLocation = (location) => {
  selectedLocation.value = {
    lat: location.lat,
    lng: location.lng,
    address: location.address || ''
  }
  // آدرس از نقشه به formData اضافه می‌شود
  if (location.address) {
    formData.value.address = location.address
  }
}

// Form submission
const submitForm = async () => {
  loading.value = true
  
  try {
    const token = localStorage.getItem('token')
    
    // Prepare form data
    const submitData = {
      ...formData.value,
      features: features.value,
      working_hours: workingHours.value,
      keywords: keywords.value.join(', '),
      latitude: selectedLocation.value?.lat || null,
      longitude: selectedLocation.value?.lng || null,
      address: selectedLocation.value?.address || formData.value.address || ''
    }
    
    console.log('Submitting business data:', submitData)
    
    // Create business
    const response = await axios.post(`${API_BASE_URL}/admin/businesses`, submitData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    
    console.log('Business created successfully:', response.data)
    
    const businessId = response.data.business.id
    
    // Upload images if any
    if (uploadedImages.value.length > 0) {
      const formData = new FormData()
      uploadedImages.value.forEach((imageObj, index) => {
        formData.append(`images[${index}]`, imageObj.file)
      })
      
      await axios.post(`${API_BASE_URL}/businesses/${businessId}/images`, formData, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
    }
    
    alert('کسب و کار با موفقیت ایجاد شد!')
    resetForm()
    
  } catch (error) {
    console.error('Error creating business:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در ایجاد کسب و کار: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    description: '',
    fullDescription: '',
    address: '',
    phone: '',
    email: '',
    website: '',
    instagram: '',
    whatsapp: '',
    category_id: '',
    province_id: '',
    city_id: '',
    neighborhood_id: '',
    meta_title: '',
    meta_description: '',
    status: 'pending',
    is_featured: false
  }
  
  keywords.value = []
  features.value = []
  uploadedImages.value = []
  cities.value = []
  neighborhoods.value = []
  selectedLocation.value = {
    lat: null,
    lng: null,
    address: ''
  }
  
  if (descriptionEditor.value) {
    descriptionEditor.value.innerHTML = ''
  }
}

onMounted(() => {
  fetchProvinces()
  fetchCategories()
})
</script>
