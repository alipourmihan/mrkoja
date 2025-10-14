<template>
  <div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">مدیریت SEO</h2>
        <p class="text-gray-600">مدیریت کامل سئو کسب‌وکارها، دسته‌بندی‌ها و صفحات</p>
      </div>
      <div class="flex space-x-3 space-x-reverse">
        <button @click="generateSitemap" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
          <i class="fas fa-sitemap ml-2"></i>
          تولید Sitemap
        </button>
        <button @click="generateRobots" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
          <i class="fas fa-robot ml-2"></i>
          تولید Robots.txt
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white shadow rounded-lg">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 space-x-reverse px-6">
          <button v-for="tab in tabs" 
                  :key="tab.id"
                  @click="activeTab = tab.id"
                  :class="[
                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                    activeTab === tab.id 
                      ? 'border-blue-500 text-blue-600' 
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]">
            <i :class="tab.icon" class="ml-2"></i>
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- Dashboard Tab -->
        <div v-if="activeTab === 'dashboard'" class="space-y-6">
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-building text-2xl"></i>
                </div>
                <div class="ml-4">
                  <p class="text-blue-100 text-sm font-medium">کل کسب‌وکارها</p>
                  <p class="text-2xl font-bold">{{ dashboardData.overview?.total_businesses || 0 }}</p>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-tags text-2xl"></i>
                </div>
                <div class="ml-4">
                  <p class="text-green-100 text-sm font-medium">کل دسته‌بندی‌ها</p>
                  <p class="text-2xl font-bold">{{ dashboardData.overview?.total_categories || 0 }}</p>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                  <p class="text-purple-100 text-sm font-medium">کسب‌وکارهای با SEO کامل</p>
                  <p class="text-2xl font-bold">{{ dashboardData.overview?.businesses_with_seo || 0 }}</p>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <i class="fas fa-percentage text-2xl"></i>
                </div>
                <div class="ml-4">
                  <p class="text-orange-100 text-sm font-medium">درصد تکمیل SEO</p>
                  <p class="text-2xl font-bold">{{ dashboardData.overview?.businesses_seo_percentage || 0 }}%</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- SEO Completion Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-4">وضعیت تکمیل SEO</h3>
              <canvas ref="seoChart" width="400" height="200"></canvas>
            </div>

            <!-- Meta Tags Distribution Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-4">توزیع Meta Tags</h3>
              <canvas ref="metaTagsChart" width="400" height="200"></canvas>
            </div>
          </div>
        </div>

        <!-- Pages Tab -->
        <div v-if="activeTab === 'pages'" class="space-y-6">
          <!-- Filters -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                <input v-model="filters.search" 
                       type="text" 
                       placeholder="جستجو در صفحات..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع</label>
                <select v-model="filters.type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">همه</option>
                  <option value="business">کسب‌وکار</option>
                  <option value="category">دسته‌بندی</option>
                  <option value="seo_page">صفحه SEO</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select v-model="filters.status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">همه</option>
                  <option value="complete">کامل</option>
                  <option value="incomplete">ناقص</option>
                  <option value="missing">بدون SEO</option>
                </select>
              </div>
              <div class="flex items-end">
                <button @click="loadPages" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md flex items-center justify-center">
                  <i class="fas fa-search ml-2"></i>
                  جستجو
                </button>
              </div>
            </div>
          </div>

          <!-- Bulk Actions -->
          <div v-if="selectedPages.length > 0" class="bg-blue-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <span class="text-sm text-blue-700">{{ selectedPages.length }} صفحه انتخاب شده</span>
              <div class="flex space-x-2 space-x-reverse">
                <button @click="generateBulkSeo" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                  <i class="fas fa-magic ml-1"></i>
                  تولید انبوه SEO
                </button>
                <button @click="validateAll" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm">
                  <i class="fas fa-check-circle ml-1"></i>
                  اعتبارسنجی همه
                </button>
                <button @click="clearSelection" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                  <i class="fas fa-times ml-1"></i>
                  لغو انتخاب
                </button>
              </div>
            </div>
          </div>

          <!-- Pages Table -->
          <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">صفحات SEO</h3>
                <div class="flex items-center space-x-2 space-x-reverse">
                  <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" class="rounded">
                  <span class="text-sm text-gray-600">انتخاب همه</span>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" v-model="selectAll" @change="toggleSelectAll">
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        نام
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        نوع
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        دسته‌بندی
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        مکان
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        وضعیت SEO
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        آخرین به‌روزرسانی
                      </th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        عملیات
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="page in pages" :key="page.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" v-model="selectedPages" :value="page.id">
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ page.name }}</div>
                        <div class="text-sm text-gray-500">{{ page.url }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                              :class="getTypeClass(page.type)">
                          {{ getTypeName(page.type) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ page.category || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ page.location || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                              :class="getStatusClass(page.seo_status)">
                          {{ getStatusName(page.seo_status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(page.last_updated) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2 space-x-reverse">
                          <button @click="editPage(page)" 
                                  class="text-blue-600 hover:text-blue-900" title="ویرایش">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button @click="validatePage(page)" 
                                  class="text-yellow-600 hover:text-yellow-900" title="اعتبارسنجی">
                            <i class="fas fa-check-circle"></i>
                          </button>
                          <button @click="generatePageSeo(page)" 
                                  class="text-green-600 hover:text-green-900" title="تولید SEO">
                            <i class="fas fa-magic"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div v-if="pagination" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                  نمایش {{ (pagination.current_page - 1) * pagination.per_page + 1 }} تا 
                  {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
                  از {{ pagination.total }} نتیجه
                </div>
                <div class="flex space-x-2 space-x-reverse">
                  <button v-if="pagination.current_page > 1" 
                          @click="loadPages(pagination.current_page - 1)"
                          class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    قبلی
                  </button>
                  <button v-if="pagination.current_page < pagination.last_page" 
                          @click="loadPages(pagination.current_page + 1)"
                          class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    بعدی
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reports Tab -->
        <div v-if="activeTab === 'reports'" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Missing SEO Report -->
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-4">صفحات بدون SEO</h3>
              <div v-if="reports.missing_seo" class="space-y-4">
                <div v-for="business in reports.missing_seo.businesses_without_seo" 
                     :key="business.id"
                     class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                  <div>
                    <div class="font-medium text-gray-900">{{ business.name }}</div>
                    <div class="text-sm text-gray-500">{{ business.category }} - {{ business.city }}</div>
                  </div>
                  <span class="text-xs text-red-600">
                    {{ business.missing_fields.length }} فیلد ناقص
                  </span>
                </div>
              </div>
            </div>

            <!-- Validation Errors Report -->
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-4">خطاهای اعتبارسنجی</h3>
              <div v-if="reports.validation_errors" class="space-y-4">
                <div v-for="error in reports.validation_errors.business_errors" 
                     :key="error.id"
                     class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                  <div>
                    <div class="font-medium text-gray-900">{{ error.name }}</div>
                    <div class="text-sm text-gray-500">{{ error.errors.length }} خطا</div>
                  </div>
                  <button @click="fixErrors(error)" 
                          class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-wrench"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const API_BASE_URL = 'https://mrkoja.com/api'

// State
const activeTab = ref('dashboard')
const tabs = [
  { id: 'dashboard', name: 'داشبورد', icon: 'fas fa-chart-pie' },
  { id: 'pages', name: 'صفحات', icon: 'fas fa-file-alt' },
  { id: 'reports', name: 'گزارش‌ها', icon: 'fas fa-chart-bar' },
]

const dashboardData = ref({})
const pages = ref([])
const pagination = ref(null)
const filters = ref({
  search: '',
  type: '',
  status: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})
const selectedPages = ref([])
const selectAll = ref(false)
const reports = ref({})

// Methods
const loadDashboard = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`${API_BASE_URL}/admin/seo/dashboard`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    dashboardData.value = response.data
    await nextTick()
    createCharts()
  } catch (error) {
    console.error('Error loading dashboard:', error)
  }
}

const loadPages = async (page = 1) => {
  try {
    const token = localStorage.getItem('token')
    const params = {
      page,
      ...filters.value
    }
    const response = await axios.get(`${API_BASE_URL}/admin/seo/pages`, { 
      params,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    pages.value = response.data.data
    pagination.value = response.data.pagination
  } catch (error) {
    console.error('Error loading pages:', error)
  }
}

const loadReports = async () => {
  try {
    const token = localStorage.getItem('token')
    const [missingSeoRes, validationRes] = await Promise.all([
      axios.get(`${API_BASE_URL}/admin/seo/reports?type=missing_seo`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      }),
      axios.get(`${API_BASE_URL}/admin/seo/reports?type=validation_errors`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
    ])
    reports.value.missing_seo = missingSeoRes.data
    reports.value.validation_errors = validationRes.data
  } catch (error) {
    console.error('Error loading reports:', error)
  }
}

const createCharts = () => {
  // SEO Completion Chart
  if (seoChart.value && dashboardData.value.charts?.seo_completion) {
    const ctx = seoChart.value.getContext('2d')
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['کامل', 'ناقص'],
        datasets: [{
          data: [
            dashboardData.value.charts.seo_completion.businesses.complete,
            dashboardData.value.charts.seo_completion.businesses.incomplete
          ],
          backgroundColor: ['#10B981', '#F59E0B']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    })
  }

  // Meta Tags Distribution Chart
  if (metaTagsChart.value && dashboardData.value.charts?.meta_tags_distribution) {
    const ctx = metaTagsChart.value.getContext('2d')
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Title', 'Description', 'Keywords'],
        datasets: [{
          label: 'کسب‌وکارها',
          data: [
            dashboardData.value.charts.meta_tags_distribution.title.businesses,
            dashboardData.value.charts.meta_tags_distribution.description.businesses,
            dashboardData.value.charts.meta_tags_distribution.keywords.businesses
          ],
          backgroundColor: '#3B82F6'
        }, {
          label: 'دسته‌بندی‌ها',
          data: [
            dashboardData.value.charts.meta_tags_distribution.title.categories,
            dashboardData.value.charts.meta_tags_distribution.description.categories,
            dashboardData.value.charts.meta_tags_distribution.keywords.categories
          ],
          backgroundColor: '#10B981'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }
}

const generateSitemap = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.post(`${API_BASE_URL}/admin/seo/generate-sitemap`, {}, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    alert('Sitemap با موفقیت تولید شد')
  } catch (error) {
    console.error('Error generating sitemap:', error)
    alert('خطا در تولید Sitemap')
  }
}

const generateRobots = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.post(`${API_BASE_URL}/admin/seo/generate-robots`, {}, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    alert('robots.txt با موفقیت تولید شد')
  } catch (error) {
    console.error('Error generating robots:', error)
    alert('خطا در تولید robots.txt')
  }
}

const generateBulkSeo = async () => {
  if (selectedPages.value.length === 0) {
    alert('لطفاً صفحاتی را انتخاب کنید')
    return
  }
  
  try {
    const token = localStorage.getItem('token')
    const businessIds = pages.value
      .filter(p => selectedPages.value.includes(p.id) && p.type === 'business')
      .map(p => p.id)
    
    if (businessIds.length > 0) {
      await axios.post(`${API_BASE_URL}/admin/seo/generate-bulk`, {
        type: 'businesses',
        ids: businessIds
      }, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
    }
    
    alert('SEO برای صفحات انتخاب شده تولید شد')
    loadPages()
  } catch (error) {
    console.error('Error generating bulk SEO:', error)
    alert('خطا در تولید SEO')
  }
}

const validateAll = async () => {
  alert('اعتبارسنجی همه صفحات شروع شد')
}

const editPage = (page) => {
  alert('ویرایش صفحه: ' + page.name)
}

const validatePage = async (page) => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.post(`${API_BASE_URL}/admin/seo/validate`, {
      type: page.type,
      id: page.id
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    alert(`امتیاز SEO: ${response.data.score}/100`)
  } catch (error) {
    console.error('Error validating page:', error)
    alert('خطا در اعتبارسنجی')
  }
}

const generatePageSeo = async (page) => {
  try {
    const token = localStorage.getItem('token')
    if (page.type === 'business') {
      await axios.post(`${API_BASE_URL}/admin/seo/generate-business/${page.id}`, {}, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
    } else if (page.type === 'category') {
      await axios.post(`${API_BASE_URL}/admin/seo/generate-category/${page.id}`, {}, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
    }
    alert('SEO برای صفحه تولید شد')
    loadPages()
  } catch (error) {
    console.error('Error generating page SEO:', error)
    alert('خطا در تولید SEO')
  }
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedPages.value = pages.value.map(p => p.id)
  } else {
    selectedPages.value = []
  }
}

const clearSelection = () => {
  selectedPages.value = []
  selectAll.value = false
}

const getTypeClass = (type) => {
  const classes = {
    business: 'bg-blue-100 text-blue-800',
    category: 'bg-green-100 text-green-800',
    seo_page: 'bg-purple-100 text-purple-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getTypeName = (type) => {
  const names = {
    business: 'کسب‌وکار',
    category: 'دسته‌بندی',
    seo_page: 'صفحه SEO'
  }
  return names[type] || type
}

const getStatusClass = (status) => {
  const classes = {
    complete: 'bg-green-100 text-green-800',
    incomplete: 'bg-yellow-100 text-yellow-800',
    missing: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusName = (status) => {
  const names = {
    complete: 'کامل',
    incomplete: 'ناقص',
    missing: 'بدون SEO'
  }
  return names[status] || status
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fa-IR')
}

const fixErrors = (error) => {
  console.log('Fixing errors for:', error)
}

// Refs for charts
const seoChart = ref(null)
const metaTagsChart = ref(null)

onMounted(() => {
  loadDashboard()
  loadPages()
  loadReports()
})
</script>

<style scoped>
/* Additional styles if needed */
</style>
