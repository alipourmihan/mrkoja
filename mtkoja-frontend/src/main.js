import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import { adminRoutes } from './router/admin.js'
import './style.css'
import App from './App.vue'

// Import pages
import Home from './pages/Home.vue'
import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Favorites from './pages/Favorites.vue'
import Profile from './pages/Profile.vue'
import AdminDashboard from './pages/admin/AdminDashboard.vue'
import BusinessDashboard from './pages/BusinessDashboard.vue'
import BusinessForm from './pages/BusinessForm.vue'
import BusinessDetail from './pages/BusinessDetail.vue'
import CategoryPage from './pages/CategoryPage.vue'
import TestPage from './pages/TestPage.vue'
import TestBusiness from './pages/TestBusiness.vue'
import AllBusinesses from './pages/AllBusinesses.vue'

// Import stores
import { useAuthStore } from './stores/auth'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/favorites', name: 'Favorites', component: Favorites },
  { path: '/profile', name: 'Profile', component: Profile },
  { path: '/businesses', name: 'AllBusinesses', component: AllBusinesses }, // لیست کسب‌وکارها
  { path: '/categories', name: 'Categories', component: Home }, // لیست دسته‌بندی‌ها
  
  // SEO Routes - Business listings with location-based URLs
  { path: '/b/:category', name: 'CategoryList', component: CategoryPage }, // /b/cafe
  { path: '/b/:category/:city', name: 'CategoryCityList', component: CategoryPage }, // /b/cafe/tehran
  { path: '/b/:category/:city/:neighborhood', name: 'CategoryCityNeighborhoodList', component: CategoryPage }, // /b/cafe/tehran/vanak
  
  // Business profile routes
  { path: '/business/:business', name: 'BusinessProfile', component: BusinessDetail },
  
  // Admin and Business Owner routes
  // Admin routes are now defined in src/router/admin.js under /admin with AdminLayout
  ...adminRoutes,
  { path: '/business', name: 'BusinessDashboard', component: BusinessDashboard, meta: { requiresAuth: true, role: 'business_owner' } },
  { path: '/business/new', name: 'BusinessForm', component: BusinessForm, meta: { requiresAuth: true, role: 'business_owner' } },
  { path: '/business/:id', name: 'BusinessDetail', component: BusinessDetail },
  { path: '/business/:id/edit', name: 'BusinessEdit', component: BusinessForm, meta: { requiresAuth: true, role: 'business_owner' } },
  
  // Test routes
  { path: '/test', name: 'TestPage', component: TestPage },
  { path: '/test-business', name: 'TestBusiness', component: TestBusiness },
  { path: '/test-business-detail', name: 'TestBusinessDetail', component: () => import('./pages/TestBusinessDetail.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// AUTH-INIT-GUARD-001: Make guard async to allow user recovery on refresh
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // If token exists but user not loaded yet, try to load it before deciding
  if (authStore.token && !authStore.user) {
    await authStore.ensureUserLoaded?.()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login')
  }

  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    return next('/')
  }

  if (to.meta.roles && !to.meta.roles.includes(authStore.user?.role)) {
    return next('/')
  }

  return next()
})

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.mount('#app')
