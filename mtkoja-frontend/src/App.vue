<script setup>
import { onMounted, onUnmounted } from 'vue'
import { useAuthStore } from './stores/auth'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import MobileBottomNav from './components/MobileBottomNav.vue'

const authStore = useAuthStore()

// Activity tracking
const trackActivity = () => {
  if (authStore.isAuthenticated) {
    authStore.updateActivity()
  }
}

onMounted(() => {
  authStore.initializeAuth()
  
  // Add event listeners for activity tracking
  document.addEventListener('click', trackActivity)
  document.addEventListener('keypress', trackActivity)
  document.addEventListener('scroll', trackActivity)
  document.addEventListener('mousemove', trackActivity)
})

onUnmounted(() => {
  // Remove event listeners
  document.removeEventListener('click', trackActivity)
  document.removeEventListener('keypress', trackActivity)
  document.removeEventListener('scroll', trackActivity)
  document.removeEventListener('mousemove', trackActivity)
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <Header />
    <main>
      <router-view />
    </main>
    <Footer />
    <MobileBottomNav />
  </div>
</template>
