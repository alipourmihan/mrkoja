// Admin routes configuration
import AdminLayout from '../layouts/AdminLayout.vue'

// Views (keep all logic inside views untouched)
import Dashboard from '../views/admin/Dashboard.vue'

// Users
import UserList from '../views/admin/users/UserList.vue'
import UserForm from '../views/admin/users/UserForm.vue'

// Businesses
import MyBusinessList from '../views/admin/businesses/MyBusinessList.vue'
import BusinessList from '../views/admin/businesses/BusinessList.vue'
import BusinessForm from '../views/admin/businesses/BusinessForm.vue'
import CategoryList from '../views/admin/businesses/CategoryList.vue'
import FeatureList from '../views/admin/businesses/FeatureList.vue'

// Locations
import LocationManager from '../views/admin/locations/LocationManager.vue'

// SEO
import SeoManager from '../views/admin/seo/SeoManager.vue'

// Tickets
import TicketList from '../views/admin/tickets/TicketList.vue'

export const adminRoutes = [
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      // 1) Dashboard
      {
        path: '',
        name: 'AdminDashboard',
        component: Dashboard,
        meta: { requiresAuth: true, role: 'admin', title: 'داشبورد' }
      },

      // 2) Users
      {
        path: 'users',
        name: 'AdminUsers',
        component: UserList,
        meta: { requiresAuth: true, role: 'admin', title: 'مدیریت کاربران' }
      },
      {
        path: 'users/new',
        name: 'AdminUserNew',
        component: UserForm,
        meta: { requiresAuth: true, role: 'admin', title: 'افزودن کاربر' }
      },
      {
        path: 'users/:id/edit',
        name: 'AdminUserEdit',
        component: UserForm,
        meta: { requiresAuth: true, role: 'admin', title: 'ویرایش کاربر' }
      },

      // 3) Businesses
      {
        path: 'businesses/mine',
        name: 'AdminMyBusinesses',
        component: MyBusinessList,
        meta: { requiresAuth: true, role: 'admin', title: 'کسب‌وکارهای من' }
      },
      {
        path: 'businesses',
        name: 'AdminBusinesses',
        component: BusinessList,
        meta: { requiresAuth: true, role: 'admin', title: 'مدیریت کسب‌وکارها' }
      },
      {
        path: 'businesses/new',
        name: 'AdminBusinessNew',
        component: BusinessForm,
        meta: { requiresAuth: true, role: 'admin', title: 'افزودن کسب‌وکار' }
      },
      {
        path: 'businesses/:id/edit',
        name: 'AdminBusinessEdit',
        component: BusinessForm,
        meta: { requiresAuth: true, role: 'admin', title: 'ویرایش کسب‌وکار' }
      },
      {
        path: 'categories',
        name: 'AdminCategories',
        component: CategoryList,
        meta: { requiresAuth: true, role: 'admin', title: 'دسته‌بندی‌ها' }
      },
      {
        path: 'features',
        name: 'AdminFeatures',
        component: FeatureList,
        meta: { requiresAuth: true, role: 'admin', title: 'ویژگی‌ها' }
      },

      // 4) Locations
      {
        path: 'locations',
        name: 'AdminLocations',
        component: LocationManager,
        meta: { requiresAuth: true, role: 'admin', title: 'مدیریت مکان‌ها' }
      },

      // 5) SEO
      {
        path: 'seo',
        name: 'AdminSeo',
        component: SeoManager,
        meta: { requiresAuth: true, role: 'admin', title: 'مدیریت SEO' }
      },

      // 6) Tickets
      {
        path: 'tickets',
        name: 'AdminTickets',
        component: TicketList,
        meta: { requiresAuth: true, role: 'admin', title: 'تیکت‌ها' }
      }
    ]
  }
]
