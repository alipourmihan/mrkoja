<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">مدیریت کاربران</h3>
      <div class="flex space-x-3 space-x-reverse">
        <button
          @click="goToAddUser"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
        >
          افزودن کاربر
        </button>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="جستجو در کاربران..."
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <select
          v-model="roleFilter"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">همه نقش‌ها</option>
          <option value="user">کاربر عادی</option>
          <option value="business_owner">صاحب کسب‌وکار</option>
          <option value="admin">مدیر</option>
        </select>
      </div>
    </div>
    <div class="p-6">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
      <div v-else-if="filteredUsers && filteredUsers.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کاربر</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ایمیل</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نقش</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ عضویت</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div v-if="user.avatar || user.avatar_url" class="h-10 w-10 rounded-full overflow-hidden">
                      <img 
                        :src="user.avatar_url || getAvatarUrl(user.avatar)" 
                        :alt="user.name"
                        class="h-full w-full object-cover"
                        @error="handleImageError"
                      >
                    </div>
                    <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">{{ user.name?.charAt(0) || '?' }}</span>
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name || 'نامشخص' }}</div>
                    <div class="text-sm text-gray-500">ID: {{ user.id || 'نامشخص' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email || 'نامشخص' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  (user.role || 'user') === 'admin' ? 'bg-red-100 text-red-800' :
                  (user.role || 'user') === 'business_owner' ? 'bg-blue-100 text-blue-800' :
                  'bg-gray-100 text-gray-800'
                ]">
                  {{ getRoleLabel(user.role || 'user') }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at || new Date()) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  (user.status || 'active') === 'active' ? 'bg-green-100 text-green-800' : 
                  (user.status || 'active') === 'inactive' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ getStatusLabel(user.status || 'active') }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                <button
                  @click="editUser(user)"
                  class="text-green-600 hover:text-green-900"
                >
                  ویرایش
                </button>
                <button
                  @click="viewUserDetails(user)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  جزئیات
                </button>
                <button
                  @click="toggleUserStatus(user)"
                  :class="[
                    (user.status || 'active') === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'
                  ]"
                >
                  {{ (user.status || 'active') === 'active' ? 'غیرفعال' : 'فعال' }}
                </button>
                <button
                  v-if="(user.role || 'user') !== 'admin'"
                  @click="deleteUser(user.id || 0)"
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
        هیچ کاربری یافت نشد
      </div>
    </div>

    <!-- User Details Modal -->
    <div v-if="showUserDetails" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">جزئیات کاربر</h3>
            <button @click="showUserDetails = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div v-if="selectedUser" class="mt-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">نام</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedUser.name || 'نامشخص' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedUser.email || 'نامشخص' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">نقش</label>
                <p class="mt-1 text-sm text-gray-900">{{ getRoleLabel(selectedUser.role || 'user') }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">تاریخ عضویت</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedUser.created_at || new Date()) }}</p>
              </div>
            </div>
            
            <div v-if="selectedUser.businesses && Array.isArray(selectedUser.businesses) && selectedUser.businesses.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-2">کسب‌وکارها</label>
              <div class="space-y-2">
                <div
                  v-for="business in (selectedUser.businesses || [])"
                  :key="business.id || Math.random()"
                  class="border border-gray-200 rounded-lg p-3"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">{{ business.name || 'نامشخص' }}</h4>
                      <p class="text-sm text-gray-500">{{ business.category?.name || 'نامشخص' }}</p>
                    </div>
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      (business.status || 'pending') === 'approved' ? 'bg-green-100 text-green-800' :
                      (business.status || 'pending') === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-red-100 text-red-800'
                    ]">
                      {{ getBusinessStatusLabel(business.status || 'pending') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end mt-6 pt-4 border-t">
            <button
              @click="showUserDetails = false"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
            >
              بستن
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const API_BASE_URL = 'https://api.mrkoja.com/api'

// State
const users = ref([])
const loading = ref(false)
const searchQuery = ref('')
const roleFilter = ref('')
const showUserDetails = ref(false)
const selectedUser = ref(null)

// Computed
const filteredUsers = computed(() => {
  // Ensure users.value is always an array
  let filtered = users.value || []

  if (searchQuery.value) {
    filtered = filtered.filter(user =>
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (roleFilter.value) {
    filtered = filtered.filter(user => user.role === roleFilter.value)
  }

  return filtered
})

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/users`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    // Handle different response structures safely
    if (response.data.users) {
      users.value = response.data.users
    } else if (Array.isArray(response.data)) {
      users.value = response.data
    } else {
      users.value = []
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    // Check if response is HTML (offline page)
    if (error.response?.data && typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
      alert('خطا: سرور در دسترس نیست. لطفاً اطمینان حاصل کنید که سرور Laravel راه‌اندازی شده است.')
    } else {
      alert('خطا در بارگذاری کاربران: ' + (error.response?.data?.message || error.message))
    }
  } finally {
    loading.value = false
  }
}

const getRoleLabel = (role) => {
  const labels = {
    'admin': 'مدیر',
    'business_owner': 'صاحب کسب‌وکار',
    'user': 'کاربر عادی'
  }
  return labels[role] || role
}

const getStatusLabel = (status) => {
  const labels = {
    'active': 'فعال',
    'inactive': 'غیرفعال',
    'suspended': 'مسدود'
  }
  return labels[status] || status
}

const getBusinessStatusLabel = (status) => {
  const labels = {
    'approved': 'تایید شده',
    'pending': 'در انتظار',
    'rejected': 'رد شده'
  }
  return labels[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return 'نامشخص'
  try {
    return new Date(dateString).toLocaleDateString('fa-IR')
  } catch (error) {
    return 'نامشخص'
  }
}

const viewUserDetails = async (user) => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/users/${user.id}/businesses`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    selectedUser.value = { 
      ...user, 
      businesses: response.data.businesses || [] 
    }
    showUserDetails.value = true
  } catch (error) {
    console.error('Error fetching user details:', error)
    selectedUser.value = { 
      ...user, 
      businesses: [] 
    }
    showUserDetails.value = true
  }
}

const toggleUserStatus = async (user) => {
  try {
    const token = localStorage.getItem('token')
    const currentStatus = user.status || 'active'
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active'
    
    await axios.put(`${API_BASE_URL}/admin/users/${user.id}`, {
      status: newStatus
    }, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    user.status = newStatus
    alert(`کاربر ${newStatus === 'active' ? 'فعال' : 'غیرفعال'} شد`)
  } catch (error) {
    console.error('Error toggling user status:', error)
    alert('خطا در تغییر وضعیت کاربر')
  }
}

const deleteUser = async (id) => {
  if (!id || id === 0) {
    alert('شناسه کاربر نامعتبر است')
    return
  }
  
  if (!confirm('آیا از حذف این کاربر اطمینان دارید؟')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`${API_BASE_URL}/admin/users/${id}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    fetchUsers()
    alert('کاربر با موفقیت حذف شد')
  } catch (error) {
    console.error('Error deleting user:', error)
    alert('خطا در حذف کاربر')
  }
}

const goToAddUser = () => {
  router.push('/admin/users/new')
}

const editUser = (user) => {
  if (!user || !user.id) {
    alert('اطلاعات کاربر نامعتبر است')
    return
  }
  router.push(`/admin/users/${user.id}/edit`)
}

const getAvatarUrl = (avatar) => {
  if (!avatar) return null
  try {
    // اگر avatar یک URL کامل است
    if (avatar.startsWith('http')) {
      return avatar
    }
    // اگر avatar فقط نام فایل است
    return `https://api.mrkoja.com/storage/avatars/${avatar}`
  } catch (error) {
    return null
  }
}

const handleImageError = (event) => {
  try {
    // اگر تصویر لود نشد، به fallback برو
    event.target.style.display = 'none'
    const parent = event.target.parentElement
    if (parent) {
      parent.innerHTML = '<div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"><span class="text-sm font-medium text-gray-700">?</span></div>'
    }
  } catch (error) {
    console.error('Error handling image error:', error)
  }
}

onMounted(async () => {
  try {
    await fetchUsers()
  } catch (error) {
    console.error('Error in onMounted:', error)
  }
})
</script>

