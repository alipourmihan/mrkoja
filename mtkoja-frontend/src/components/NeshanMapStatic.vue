<template>
  <div class="w-full">
    <!-- Map Container -->
    <div 
      id="neshan-static-map" 
      class="w-full h-64 rounded-lg border border-gray-300"
      style="height: 400px; width: 100%;"
    ></div>
    
    <!-- Loading State -->
    <div v-if="!mapLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-lg">
      <div class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
        <p class="text-sm text-gray-600">در حال بارگذاری نقشه...</p>
      </div>
    </div>
    
    <!-- Error State -->
    <div v-if="error" class="flex items-center justify-center h-64 bg-red-50 rounded-lg border border-red-200">
      <div class="text-center">
        <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <h3 class="text-lg font-medium text-red-800 mb-2">خطا در بارگذاری نقشه</h3>
        <p class="text-sm text-red-600">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import '@neshan-maps-platform/mapbox-gl/dist/NeshanMapboxGl.css'
import neshanMapbox from '@neshan-maps-platform/mapbox-gl'

const props = defineProps({
  latitude: {
    type: [String, Number],
    required: true
  },
  longitude: {
    type: [String, Number],
    required: true
  },
  businessName: {
    type: String,
    default: 'کسب‌وکار'
  },
  address: {
    type: String,
    default: ''
  }
})

// Reactive data
const mapLoaded = ref(false)
const error = ref('')
let map = null
let marker = null

// Neshan API Key
const NESHAN_MAP_KEY = 'web.90d955cf6f12443fa7b0654067a86f63'

// Initialize map
const initMap = async () => {
  try {
    // Wait for DOM to be ready
    await nextTick()
    
    // Wait a bit more for container to be visible
    await new Promise(resolve => setTimeout(resolve, 100))
    
    const mapElement = document.getElementById('neshan-static-map')
    if (!mapElement) {
      throw new Error('Map container not found')
    }

    // Validate coordinates
    const lat = parseFloat(props.latitude)
    const lng = parseFloat(props.longitude)
    
    if (isNaN(lat) || isNaN(lng)) {
      throw new Error('Invalid coordinates')
    }

    const position = [lng, lat] // Neshan uses [lng, lat] format
    
    // Create map
    console.log('Creating Neshan static map...')
    map = new neshanMapbox.Map({
      mapType: neshanMapbox.Map.mapTypes.neshanVector,
      container: 'neshan-static-map',
      zoom: 15,
      center: position,
      mapKey: NESHAN_MAP_KEY,
      poi: true,
      traffic: false,
      // Disable all interactions for static view
      interactive: false,
      mapTypeControllerOptions: {
        show: false // Hide map type controller
      }
    })
    
    // Wait for map to load
    map.on('load', () => {
      // Create marker
      marker = new neshanMapbox.Marker()
        .setLngLat(position)
        .addTo(map)
      
      // Create popup
      const popup = new neshanMapbox.Popup({ offset: 25 })
        .setLngLat(position)
        .setHTML(`
          <div class="p-2 text-right">
            <h3 class="font-bold text-gray-800 mb-1">${props.businessName}</h3>
            ${props.address ? `<p class="text-sm text-gray-600">${props.address}</p>` : ''}
            <p class="text-xs text-gray-500 mt-1">${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
          </div>
        `)
        .addTo(map)
      
      marker.setPopup(popup)
      
      mapLoaded.value = true
      console.log('Neshan static map initialized successfully')
    })
    
  } catch (err) {
    console.error('Error initializing Neshan static map:', err)
    error.value = err.message
    mapLoaded.value = false
  }
}

// Initialize on mount
onMounted(() => {
  initMap().then(() => {
    // Fix hidden container sizing issues
    setTimeout(() => {
      if (map) {
        map.resize()
      }
    }, 200)
  })
})

// Cleanup on unmount
onUnmounted(() => {
  if (map) {
    map.remove()
  }
})
</script>

<style scoped>
#neshan-static-map {
  height: 400px !important;
  width: 100% !important;
}
</style>
