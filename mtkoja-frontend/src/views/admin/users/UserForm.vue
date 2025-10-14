<template>
  <div class="max-w-4xl mx-auto" dir="rtl">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        {{ isEdit ? 'ویرایش کاربر' : 'افزودن کاربر جدید' }}
      </h1>
      <p class="text-gray-600">
        {{ isEdit ? 'اطلاعات کاربر را ویرایش کنید' : 'اطلاعات کاربر جدید را وارد کنید' }}
      </p>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-8">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Basic Information -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">
            اطلاعات پایه
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                نام و نام خانوادگی <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                :class="{ 'border-red-500': errors.name }"
                placeholder="نام و نام خانوادگی را وارد کنید"
              >
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                ایمیل <span class="text-red-500">*</span>
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                :class="{ 'border-red-500': errors.email }"
                placeholder="example@domain.com"
              >
              <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
            </div>

            <!-- Phone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                شماره تلفن
              </label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                placeholder="09123456789"
              >
            </div>

            <!-- Gender -->
            <div>
              <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                جنسیت
              </label>
              <select
                id="gender"
                v-model="form.gender"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
              >
                <option value="">انتخاب کنید</option>
                <option value="male">مرد</option>
                <option value="female">زن</option>
                <option value="other">سایر</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Account Settings -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">
            تنظیمات حساب کاربری
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Role -->
            <div>
              <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                نقش <span class="text-red-500">*</span>
              </label>
              <select
                id="role"
                v-model="form.role"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                :class="{ 'border-red-500': errors.role }"
              >
                <option value="">انتخاب نقش</option>
                <option value="user">کاربر عادی</option>
                <option value="business_owner">صاحب کسب‌وکار</option>
                <option value="admin">مدیر</option>
              </select>
              <p v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role }}</p>
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                وضعیت <span class="text-red-500">*</span>
              </label>
              <select
                id="status"
                v-model="form.status"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="active">فعال</option>
                <option value="inactive">غیرفعال</option>
                <option value="suspended">مسدود</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
            </div>

            <!-- Department -->
            <div>
              <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                گروه/دپارتمان
              </label>
              <select
                id="department"
                v-model="form.department"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
              >
                <option value="">انتخاب کنید</option>
                <option value="management">مدیریت</option>
                <option value="development">توسعه</option>
                <option value="marketing">بازاریابی</option>
                <option value="sales">فروش</option>
                <option value="support">پشتیبانی</option>
                <option value="finance">مالی</option>
                <option value="hr">منابع انسانی</option>
                <option value="other">سایر</option>
              </select>
            </div>

            <!-- Password (only for new users or when editing) -->
            <div v-if="!isEdit || showPasswordField">
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                رمز عبور {{ isEdit ? '(اختیاری)' : '' }} <span v-if="!isEdit" class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  :required="!isEdit"
                  class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                  :class="{ 'border-red-500': errors.password }"
                  placeholder="رمز عبور را وارد کنید"
                >
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                  <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
              </div>
              <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
              <button
                v-if="isEdit"
                type="button"
                @click="showPasswordField = !showPasswordField"
                class="mt-2 text-sm text-red-600 hover:text-red-800"
              >
                {{ showPasswordField ? 'لغو تغییر رمز عبور' : 'تغییر رمز عبور' }}
              </button>
            </div>
            
            <!-- Password Confirmation (only for new users) -->
            <div v-if="!isEdit">
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                تکرار رمز عبور <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                  :class="{ 'border-red-500': errors.password_confirmation }"
                  placeholder="رمز عبور را مجدداً وارد کنید"
                >
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                  <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
              </div>
              <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">
            اطلاعات تکمیلی
          </h2>
          
          <div class="space-y-6">
            <!-- Address -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                آدرس
              </label>
              <textarea
                id="address"
                v-model="form.address"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                placeholder="آدرس کامل را وارد کنید"
              ></textarea>
            </div>

            <!-- Notes -->
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                یادداشت داخلی
              </label>
              <textarea
                id="notes"
                v-model="form.notes"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                placeholder="یادداشت‌های داخلی در مورد این کاربر..."
              ></textarea>
            </div>

            <!-- Avatar Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                تصویر پروفایل
              </label>
              <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Current Avatar -->
                <div class="flex-shrink-0">
                  <div v-if="avatarPreview" class="h-20 w-20 rounded-full overflow-hidden border-2 border-gray-300">
                    <img :src="avatarPreview" alt="Avatar Preview" class="h-full w-full object-cover">
                  </div>
                  <div v-else class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                </div>
                
                <!-- Upload Button -->
                <div class="flex-1">
                  <input
                    ref="avatarInput"
                    type="file"
                    accept="image/*"
                    @change="handleAvatarChange"
                    class="hidden"
                  >
                  <button
                    type="button"
                    @click="$refs.avatarInput.click()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                  >
                    انتخاب تصویر
                  </button>
                  <p class="mt-1 text-xs text-gray-500">
                    فرمت‌های مجاز: JPG, PNG, GIF (حداکثر 2MB)
                  </p>
                </div>
              </div>
            </div>

            <!-- Last Login (Display Only) -->
            <div v-if="isEdit && form.last_login_at">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                آخرین ورود
              </label>
              <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-600">
                  {{ formatDate(form.last_login_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4 space-x-reverse pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="handleCancel"
            class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors"
          >
            لغو
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-3 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              در حال پردازش...
            </span>
            <span v-else>
              {{ isEdit ? 'ذخیره تغییرات' : 'افزودن کاربر' }}
            </span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { API_ENDPOINTS, getAuthHeadersForUpload } from '../../../config/api'

const route = useRoute()
const router = useRouter()

// Form data
const form = reactive({
  name: '',
  email: '',
  phone: '',
  gender: '',
  role: '',
  status: 'active',
  department: '',
  password: '',
  password_confirmation: '',
  address: '',
  notes: '',
  avatar: null,
  last_login_at: null
})

// Form state
const loading = ref(false)
const showPassword = ref(false)
const showPasswordField = ref(false)
const avatarPreview = ref(null)
const errors = reactive({})

// Computed
const isEdit = computed(() => route.name === 'AdminUserEdit' && route.params.id)

// Methods
const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      alert('حجم فایل نباید از 2 مگابایت بیشتر باشد')
      return
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('لطفاً یک فایل تصویری انتخاب کنید')
      return
    }
    
    form.avatar = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      avatarPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const validateForm = () => {
  const newErrors = {}
  
  // Name validation
  if (!form.name || !form.name.trim()) {
    newErrors.name = 'نام و نام خانوادگی الزامی است'
  } else if (form.name.trim().length < 2) {
    newErrors.name = 'نام باید حداقل 2 کاراکتر باشد'
  }
  
  // Email validation
  if (!form.email || !form.email.trim()) {
    newErrors.email = 'ایمیل الزامی است'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    newErrors.email = 'فرمت ایمیل صحیح نیست'
  }
  
  // Role validation
  if (!form.role) {
    newErrors.role = 'انتخاب نقش الزامی است'
  }
  
  // Status validation
  if (!form.status) {
    newErrors.status = 'انتخاب وضعیت الزامی است'
  }
  
  // Password validation (for new users)
  if (!isEdit.value && !form.password) {
    newErrors.password = 'رمز عبور الزامی است'
  } else if (form.password && form.password.length < 6) {
    newErrors.password = 'رمز عبور باید حداقل 6 کاراکتر باشد'
  }
  
  // Password confirmation validation (for new users)
  if (!isEdit.value && form.password && form.password !== form.password_confirmation) {
    newErrors.password_confirmation = 'رمز عبور و تکرار آن مطابقت ندارند'
  }
  
  // Phone validation (if provided)
  if (form.phone && !/^09\d{9}$/.test(form.phone)) {
    newErrors.phone = 'شماره تلفن باید با 09 شروع شده و 11 رقم باشد'
  }
  
  Object.assign(errors, newErrors)
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }
  
  loading.value = true
  
  try {
    const formData = new FormData()
    
    // Add form fields
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '') {
        formData.append(key, form[key])
      }
    })
    
    // Add avatar if selected
    if (form.avatar) {
      formData.append('avatar', form.avatar)
    }
    
    const url = isEdit.value 
      ? API_ENDPOINTS.USERS.UPDATE(route.params.id)
      : API_ENDPOINTS.USERS.CREATE
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method,
      body: formData,
      headers: getAuthHeadersForUpload()
    })
    
    if (!response.ok) {
      let errorMessage = 'خطا در ذخیره اطلاعات'
      try {
        const errorData = await response.json()
        errorMessage = errorData.message || errorData.error || errorMessage
      } catch (parseError) {
        console.error('Error parsing error response:', parseError)
        errorMessage = `خطا در سرور (کد: ${response.status})`
      }
      throw new Error(errorMessage)
    }
    
    const result = await response.json()
    
    // Success message
    alert(isEdit.value ? 'کاربر با موفقیت ویرایش شد' : 'کاربر با موفقیت افزوده شد')
    
    // Redirect to users list
    router.push('/admin/users')
    
  } catch (error) {
    console.error('Error saving user:', error)
    console.error('Error message:', error.message)
    console.error('Error stack:', error.stack)
    
    // Check if it's a network error
    if (error.name === 'TypeError' && error.message.includes('fetch')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در ذخیره اطلاعات: ' + error.message)
    }
  } finally {
    loading.value = false
  }
}

const handleCancel = () => {
  router.push('/admin/users')
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('fa-IR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadUserData = async () => {
  if (!isEdit.value) return
  
  try {
    loading.value = true
    const response = await fetch(API_ENDPOINTS.USERS.SHOW(route.params.id), {
      headers: getAuthHeadersForUpload()
    })
    
    if (!response.ok) {
      throw new Error('خطا در بارگذاری اطلاعات کاربر')
    }
    
    const userData = await response.json()
    
    // Populate form with user data
    Object.keys(form).forEach(key => {
      if (userData[key] !== undefined) {
        form[key] = userData[key]
      }
    })
    
    // Set avatar preview if exists
    if (userData.avatar_url) {
      avatarPreview.value = userData.avatar_url
    }
    
  } catch (error) {
    console.error('Error loading user:', error)
    console.error('Error message:', error.message)
    console.error('Error stack:', error.stack)
    
    // Check if it's a network error
    if (error.name === 'TypeError' && error.message.includes('fetch')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در بارگذاری اطلاعات کاربر: ' + error.message)
    }
    router.push('/admin/users')
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  if (isEdit.value) {
    loadUserData()
  }
})
</script>