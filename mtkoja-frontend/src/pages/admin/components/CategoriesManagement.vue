<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">مدیریت دسته‌بندی‌ها</h3>
          <p class="text-sm text-gray-500 mt-1">سازماندهی و مدیریت دسته‌بندی‌های کسب و کار</p>
        </div>
        <button 
          @click="showCategoryForm = true; editingCategory = null; resetCategoryForm()"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن دسته‌بندی
        </button>
      </div>
    </div>
    <div class="p-6">
      <div v-if="categoriesLoading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="text-gray-600 mt-4">در حال بارگذاری...</p>
      </div>
      <div v-else-if="categories.length > 0">
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
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">دسته مادر</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ترتیب</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in categories" :key="category.id" :class="{ 'bg-blue-50': !category.parent_id, 'bg-gray-50': category.parent_id }">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div :style="{ backgroundColor: category.color || (category.parent_id ? '#10b981' : '#3b82f6') }" class="h-10 w-10 rounded-lg flex items-center justify-center">
                      <span class="text-white text-sm font-medium">{{ category.name.charAt(0) }}</span>
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="flex items-center">
                      <span v-if="!category.parent_id" class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                      <span v-else class="w-1 h-1 bg-green-500 rounded-full ml-2"></span>
                      <div class="text-sm font-medium text-gray-900" :class="{ 'font-bold': !category.parent_id, 'mr-2': category.parent_id }">
                        {{ category.name }}
                        <span v-if="!category.parent_id" class="text-xs text-blue-600 mr-1">(اصلی)</span>
                        <span v-else class="text-xs text-green-600 mr-1">(زیردسته)</span>
                      </div>
                    </div>
                    <div class="text-sm text-gray-500">{{ category.slug }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span v-if="!category.parent_id" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  دسته اصلی
                </span>
                <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ category.parent ? category.parent.name : 'نامشخص' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ category.is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.sort_order }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                <button 
                  @click="editCategory(category)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  ویرایش
                </button>
                <button 
                  @click="deleteCategory(category.id)"
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
            v-for="category in categories" 
            :key="category.id"
            class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            :class="{ 'border-blue-300 bg-blue-50': !category.parent_id, 'border-gray-200': category.parent_id }"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center">
                <div 
                  class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-medium"
                  :class="category.parent_id ? 'bg-green-500' : 'bg-blue-500'"
                >
                  {{ category.name.charAt(0) }}
                </div>
                <div class="mr-3">
                  <div class="flex items-center">
                    <span v-if="!category.parent_id" class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                    <span v-else class="w-1 h-1 bg-green-500 rounded-full ml-2"></span>
                    <h3 class="text-sm font-medium text-gray-900" :class="{ 'font-bold': !category.parent_id }">
                      {{ category.name }}
                    </h3>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">{{ category.slug }}</p>
                </div>
              </div>
              <span 
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                :class="category.parent_id ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
              >
                {{ category.parent_id ? 'زیردسته' : 'اصلی' }}
              </span>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">دسته مادر:</span>
                <span v-if="!category.parent_id" class="text-blue-600 font-medium">دسته اصلی</span>
                <span v-else class="text-green-600 font-medium">{{ category.parent ? category.parent.name : 'نامشخص' }}</span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">وضعیت:</span>
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ category.is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </div>
              
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">ترتیب:</span>
                <span class="text-gray-900 font-medium">{{ category.sort_order || 0 }}</span>
              </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
              <button 
                @click="editCategory(category)"
                class="flex-1 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors"
              >
                ویرایش
              </button>
              <button 
                @click="deleteCategory(category.id)"
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
          <span class="text-gray-400 text-2xl">📂</span>
        </div>
        <h3 class="text-lg font-medium text-gray-600 mb-2">هیچ دسته‌بندی‌ای یافت نشد</h3>
        <p class="text-gray-500 mb-4">برای شروع، اولین دسته‌بندی خود را اضافه کنید</p>
        <button 
          @click="showCategoryForm = true; editingCategory = null; resetCategoryForm()"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
        >
          <span class="ml-1">➕</span>
          اضافه کردن دسته‌بندی
        </button>
      </div>
    </div>
  </div>

  <!-- Category Form Modal -->
  <div v-if="showCategoryForm" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex items-center justify-between pb-4 border-b">
          <h3 class="text-lg font-medium text-gray-900">
            {{ editingCategory ? 'ویرایش دسته‌بندی' : 'اضافه کردن دسته‌بندی جدید' }}
          </h3>
          <button @click="showCategoryForm = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="saveCategoryForm" class="mt-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نام دسته‌بندی *</label>
            <input 
              v-model="categoryForm.name" 
              type="text" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="نام دسته‌بندی را وارد کنید"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">دسته مادر</label>
            <select 
              v-model="categoryForm.parent_id" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">انتخاب دسته مادر (اختیاری)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات (برای سئو)</label>
            <textarea 
              v-model="categoryForm.description" 
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="توضیحات دسته‌بندی برای سئو"
            ></textarea>
          </div>
          
          <!-- CAT-ADMIN-FIELDS-003: Keywords input as tags -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">کلمات کلیدی</label>
            <input 
              v-model="keywordInput"
              @keydown.enter.prevent="addKeyword"
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="هر کلمه را تایپ و Enter بزنید"
            >
            <div class="flex flex-wrap gap-2 mt-3">
              <span 
                v-for="(kw, idx) in keywordTags" 
                :key="idx"
                class="inline-flex items-center px-2.5 py-1 rounded-full text-sm bg-blue-100 text-blue-800"
              >
                {{ kw }}
                <button 
                  type="button"
                  class="ml-2 text-blue-600 hover:text-blue-800"
                  @click="removeKeyword(idx)"
                >×</button>
              </span>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">متا تایتل</label>
            <input 
              v-model="categoryForm.meta_title" 
              type="text" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="عنوان متا برای سئو"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">متا دیسکریپشن</label>
            <textarea 
              v-model="categoryForm.meta_description" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="توضیحات متا برای سئو"
            ></textarea>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">رنگ</label>
              <input 
                v-model="categoryForm.color" 
                type="color" 
                class="w-full h-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">ترتیب نمایش</label>
              <input 
                v-model="categoryForm.sort_order" 
                type="number" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0"
              >
            </div>
          </div>

          <!-- Icon picker and Slug fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">آیکون</label>
              <input 
                v-model="categoryForm.icon"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="مثال: 🍔 یا کلاس آیکون"
              >
              <div v-if="categoryForm.icon" class="mt-2 text-2xl">پیشنمایش: <span>{{ categoryForm.icon }}</span></div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">نامک (slug)</label>
              <input 
                v-model="categoryForm.slug"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="مثل: fast-food"
              >
            </div>
          </div>
          
          <div class="flex items-center">
            <input 
              v-model="categoryForm.is_active" 
              type="checkbox" 
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label class="mr-2 block text-sm text-gray-900">فعال</label>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
            <button 
              type="button"
              @click="showCategoryForm = false"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
            >
              انصراف
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              {{ editingCategory ? 'ویرایش' : 'اضافه کردن' }}
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
const categories = ref([])
const categoriesLoading = ref(false)
const showCategoryForm = ref(false)
const editingCategory = ref(null)
const viewMode = ref('table')
const categoryForm = ref({
  name: '',
  parent_id: '',
  description: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  slug: '',
  icon: '',
  color: '#6366f1',
  sort_order: 0,
  is_active: true
})

// Methods
const fetchCategories = async () => {
  categoriesLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/categories`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    // Handle different response structures safely
    if (response.data.categories) {
      categories.value = response.data.categories
    } else if (Array.isArray(response.data)) {
      categories.value = response.data
    } else {
      categories.value = []
    }
    console.log('Categories loaded:', categories.value)
  } catch (error) {
    console.error('Error fetching categories:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در دریافت لیست دسته‌بندی‌ها: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    categoriesLoading.value = false
  }
}

const resetCategoryForm = () => {
  categoryForm.value = {
    name: '',
    parent_id: '',
    description: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    slug: '',
    icon: '',
    color: '#6366f1',
    sort_order: 0,
    is_active: true
  }
  keywordTags.value = []
  keywordInput.value = ''
}

const editCategory = (category) => {
  editingCategory.value = category
  categoryForm.value = { ...category }
  keywordTags.value = (category.meta_keywords || '')
    .split(',')
    .map(s => s.trim())
    .filter(Boolean)
  showCategoryForm.value = true
}

const keywordInput = ref('')
const keywordTags = ref([])

const addKeyword = () => {
  const k = (keywordInput.value || '').trim()
  if (!k) return
  if (!keywordTags.value.includes(k)) keywordTags.value.push(k)
  keywordInput.value = ''
}

const removeKeyword = (idx) => {
  keywordTags.value.splice(idx, 1)
}

const saveCategoryForm = async () => {
  try {
    const token = localStorage.getItem('token')
    const url = editingCategory.value 
      ? `${API_BASE_URL}/categories/${editingCategory.value.id}`
      : `${API_BASE_URL}/categories`
    
    const method = editingCategory.value ? 'PUT' : 'POST'
    
    categoryForm.value.meta_keywords = keywordTags.value.join(',')

    const response = await axios({
      method,
      url,
      data: categoryForm.value,
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    showCategoryForm.value = false
    resetCategoryForm()
    fetchCategories()
    alert(editingCategory.value ? 'دسته‌بندی با موفقیت ویرایش شد' : 'دسته‌بندی با موفقیت اضافه شد')
  } catch (error) {
    console.error('Error saving category:', error)
    alert('خطا در ذخیره دسته‌بندی')
  }
}

const deleteCategory = async (id) => {
  if (!confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`${API_BASE_URL}/categories/${id}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    fetchCategories()
    alert('دسته‌بندی با موفقیت حذف شد')
  } catch (error) {
    console.error('Error deleting category:', error)
    alert('خطا در حذف دسته‌بندی')
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

