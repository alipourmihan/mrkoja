// API Configuration
export const API_BASE_URL = 'http://localhost:8000/api'

export const API_ENDPOINTS = {
  // Users
  USERS: {
    LIST: `${API_BASE_URL}/admin/users`,
    CREATE: `${API_BASE_URL}/admin/users`,
    SHOW: (id) => `${API_BASE_URL}/admin/users/${id}`,
    UPDATE: (id) => `${API_BASE_URL}/admin/users/${id}`,
    DELETE: (id) => `${API_BASE_URL}/admin/users/${id}`,
  },
  
  // Businesses
  BUSINESSES: {
    LIST: `${API_BASE_URL}/admin/businesses`,
    CREATE: `${API_BASE_URL}/admin/businesses`,
    SHOW: (id) => `${API_BASE_URL}/admin/businesses/${id}`,
    UPDATE: (id) => `${API_BASE_URL}/admin/businesses/${id}`,
    DELETE: (id) => `${API_BASE_URL}/admin/businesses/${id}`,
  },
  
  // Categories
  CATEGORIES: {
    LIST: `${API_BASE_URL}/admin/categories`,
    CREATE: `${API_BASE_URL}/admin/categories`,
    SHOW: (id) => `${API_BASE_URL}/admin/categories/${id}`,
    UPDATE: (id) => `${API_BASE_URL}/admin/categories/${id}`,
    DELETE: (id) => `${API_BASE_URL}/admin/categories/${id}`,
  },
  
  // Auth
  AUTH: {
    LOGIN: `${API_BASE_URL}/auth/login`,
    LOGOUT: `${API_BASE_URL}/auth/logout`,
    ME: `${API_BASE_URL}/auth/me`,
  }
}

// Helper function to get auth headers
export const getAuthHeaders = () => {
  const token = localStorage.getItem('auth_token')
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(token && { 'Authorization': `Bearer ${token}` })
  }
}

// Helper function to get auth headers for file uploads
export const getAuthHeadersForUpload = () => {
  const token = localStorage.getItem('auth_token')
  return {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    ...(token && { 'Authorization': `Bearer ${token}` })
  }
}
