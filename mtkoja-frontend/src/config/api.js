// تشخیص لوکال یا سرور اصلی
const isLocal = window.location.hostname.includes('localhost') || window.location.hostname.includes('127.0.0.1');

// آدرس‌های API
const API_LOCAL_URL = 'http://127.0.0.1:8000/api';    // لوکال برای توسعه
const API_MAIN_URL  = 'https://api.mrkoja.com/api';   // سرور اصلی

// تابع کمکی برای انتخاب مسیر مناسب
const getApiBase = (endpoint) => {
  const serverOnlyEndpoints = [
    '/login',
    '/register',
    '/admin/users',
    '/admin/businesses',
    '/admin/categories',
    '/logout',
    '/user'
    // می‌توانید بقیه endpointهای حساس را اضافه کنید
  ];

  for (let path of serverOnlyEndpoints) {
    if (endpoint.startsWith(path)) return API_MAIN_URL;
  }

  return isLocal ? API_LOCAL_URL : API_MAIN_URL;
};

// خروجی API_BASE_URL برای بخش‌های عمومی
export const API_BASE_URL = isLocal ? API_LOCAL_URL : API_MAIN_URL;

console.log('API BASE URL IS:', API_BASE_URL);

export const API_ENDPOINTS = {
  USERS: {
    LIST: `${getApiBase('/admin/users')}/admin/users`,
    CREATE: `${getApiBase('/admin/users')}/admin/users`,
    SHOW: (id) => `${getApiBase('/admin/users')}/admin/users/${id}`,
    UPDATE: (id) => `${getApiBase('/admin/users')}/admin/users/${id}`,
    DELETE: (id) => `${getApiBase('/admin/users')}/admin/users/${id}`,
  },

  BUSINESSES: {
    LIST: `${getApiBase('/admin/businesses')}/admin/businesses`,
    CREATE: `${getApiBase('/admin/businesses')}/admin/businesses`,
    SHOW: (id) => `${getApiBase('/admin/businesses')}/admin/businesses/${id}`,
    UPDATE: (id) => `${getApiBase('/admin/businesses')}/admin/businesses/${id}`,
    DELETE: (id) => `${getApiBase('/admin/businesses')}/admin/businesses/${id}`,
  },

  CATEGORIES: {
    LIST: `${getApiBase('/admin/categories')}/admin/categories`,
    CREATE: `${getApiBase('/admin/categories')}/admin/categories`,
    SHOW: (id) => `${getApiBase('/admin/categories')}/admin/categories/${id}`,
    UPDATE: (id) => `${getApiBase('/admin/categories')}/admin/categories/${id}`,
    DELETE: (id) => `${getApiBase('/admin/categories')}/admin/categories/${id}`,
  },

  AUTH: {
    LOGIN: `${getApiBase('/login')}/login`,
    REGISTER: `${getApiBase('/register')}/register`,
    LOGOUT: `${getApiBase('/logout')}/logout`,
    ME: `${getApiBase('/user')}/user`,
  },

  BUSINESS_OWNER: {
    CREATE: `${API_BASE_URL}/businesses`,
    UPDATE: (id) => `${API_BASE_URL}/businesses/${id}`,
    DELETE: (id) => `${API_BASE_URL}/businesses/${id}`,
    MY_BUSINESSES: `${API_BASE_URL}/my-businesses`,
  },

  IMAGES: {
    UPLOAD: (businessId) => `${API_BASE_URL}/businesses/${businessId}/images`,
    LIST: (businessId) => `${API_BASE_URL}/businesses/${businessId}/images`,
    DELETE: (imageId) => `${API_BASE_URL}/images/${imageId}`,
    SET_PRIMARY: (imageId) => `${API_BASE_URL}/images/${imageId}/set-primary`,
    REORDER: (businessId) => `${API_BASE_URL}/businesses/${businessId}/images/reorder`,
  }
};

// Helper function to get auth headers
export const getAuthHeaders = () => {
  const token = localStorage.getItem('token');
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(token && { 'Authorization': `Bearer ${token}` })
  };
};

// Helper function to get auth headers for file uploads
export const getAuthHeadersForUpload = () => {
  const token = localStorage.getItem('token');
  return {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(token && { 'Authorization': `Bearer ${token}` })
  };
};
