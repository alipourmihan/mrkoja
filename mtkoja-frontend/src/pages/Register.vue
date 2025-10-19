<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="mx-auto h-12 w-12 flex items-center justify-center">
          <h1 class="text-3xl font-bold text-primary-600">مسترکجا</h1>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          ایجاد حساب کاربری جدید
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          یا
          <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
            وارد حساب کاربری موجود شوید
          </router-link>
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
          {{ errorMessage }}
        </div>
        
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">نام و نام خانوادگی</label>
            <input
              id="name"
              v-model="form.name"
              name="name"
              type="text"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="نام و نام خانوادگی خود را وارد کنید"
            />
            <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">ایمیل</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="آدرس ایمیل خود را وارد کنید"
            />
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</div>
          </div>

          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">نوع حساب کاربری</label>
            <select
              id="role"
              v-model="form.role"
              name="role"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            >
              <option value="">انتخاب کنید</option>
              <option value="user">کاربر عادی</option>
              <option value="business_owner">صاحب کسب‌وکار</option>
            </select>
            <div v-if="errors.role" class="mt-1 text-sm text-red-600">{{ errors.role[0] }}</div>
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">شماره تلفن (اختیاری)</label>
            <input
              id="phone"
              v-model="form.phone"
              name="phone"
              type="tel"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="شماره تلفن خود را وارد کنید"
            />
            <div v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</div>
          </div>

          <div>
            <label for="address" class="block text-sm font-medium text-gray-700">آدرس (اختیاری)</label>
            <textarea
              id="address"
              v-model="form.address"
              name="address"
              rows="3"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="آدرس خود را وارد کنید"
            ></textarea>
            <div v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">رمز عبور</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="رمز عبور خود را وارد کنید"
            />
            <div v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">تکرار رمز عبور</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="رمز عبور خود را مجدداً وارد کنید"
            />
            <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation[0] }}</div>
          </div>
        </div>

        <div class="flex items-center">
          <input
            id="terms"
            v-model="form.terms"
            name="terms"
            type="checkbox"
            required
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
          />
          <label for="terms" class="mr-2 block text-sm text-gray-900">
            با
            <a href="#" class="text-primary-600 hover:text-primary-500">شرایط استفاده</a>
            و
            <a href="#" class="text-primary-600 hover:text-primary-500">حریم خصوصی</a>
            موافقم
          </label>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading || !form.terms"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
            </span>
            {{ loading ? 'در حال ایجاد حساب...' : 'ایجاد حساب کاربری' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  role: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
  terms: false
})

const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})

const handleRegister = async () => {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  const result = await authStore.register(form.value)
  
  if (result.success) {
    // Redirect based on user role
    if (authStore.isAdmin) {
      router.push('/admin')
    } else if (authStore.isBusinessOwner) {
      router.push('/business')
    } else {
      router.push('/')
    }
  } else {
    errorMessage.value = result.message
    if (result.errors) {
      errors.value = result.errors
    }
  }
  
  loading.value = false
}
</script>









