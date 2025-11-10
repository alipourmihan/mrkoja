<template>
  <div class="w-full">
    <!-- Map Container -->
    <div 
      id="neshan-location-map" 
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
    
    <!-- Address Input -->
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        آدرس پستی
      </label>
      <textarea
        v-model="address"
        @input="handleAddressChange"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        rows="3"
        placeholder="آدرس را وارد کنید یا روی نقشه کلیک کنید"
        dir="rtl"
      ></textarea>
    </div>
    
    <!-- Selected Location Info -->
    <div v-if="lat && lng" class="mt-3 p-3 bg-blue-50 rounded-lg">
      <div class="flex items-center text-sm text-blue-700">
        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
        </svg>
        <span>موقعیت انتخاب شده: {{ lat.toFixed(6) }}, {{ lng.toFixed(6) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import '@neshan-maps-platform/mapbox-gl/dist/NeshanMapboxGl.css'
import neshanMapbox from '@neshan-maps-platform/mapbox-gl'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ lat: null, lng: null, address: '' })
  }
})

const emit = defineEmits(['update:modelValue', 'location-selected'])

// Reactive data
const mapLoaded = ref(false)
const address = ref('')
const lat = ref(null)
const lng = ref(null)
let map = null
let marker = null

// Neshan API Key
const NESHAN_API_KEY = 'web.90d955cf6f12443fa7b0654067a86f63'

// Initialize map
const initMap = async () => {
  try {
    // Wait for DOM to be ready
    await nextTick()
    
    // Wait a bit more for container to be visible
    await new Promise(resolve => setTimeout(resolve, 100))
    
    const mapElement = document.getElementById('neshan-location-map')
    if (!mapElement) {
      console.error('Map container not found')
      return
    }

    // Default position (Tehran)
    const defaultPosition = [51.3890, 35.6892] // [lng, lat]
    
    // Create map
    console.log('Creating Neshan location picker map...')
    map = new neshanMapbox.Map({
      mapType: neshanMapbox.Map.mapTypes.neshanVector,
      container: 'neshan-location-map',
      zoom: 13,
      center: defaultPosition,
      mapKey: NESHAN_API_KEY,
      poi: true,
      traffic: false,
      mapTypeControllerOptions: {
        show: true,
        position: 'bottom-left'
      }
    })
    
    // Wait for map to load
    map.on('load', () => {
      // Add click listener
      map.on('click', async (e) => {
        const lngLat = e.lngLat
        lat.value = lngLat.lat
        lng.value = lngLat.lng
        
        // Remove existing marker
        if (marker) {
          marker.remove()
        }
        
        // Add new marker
        marker = new neshanMapbox.Marker()
          .setLngLat([lng.value, lat.value])
          .addTo(map)
        
        // Reverse geocode using OpenStreetMap Nominatim (free service)
        try {
          const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat.value}&lon=${lng.value}&format=json&accept-language=fa`)
          
          if (response.ok) {
            const data = await response.json()
            if (data.display_name) {
              // Clean up the address for better readability
              const cleanAddress = data.display_name
                .replace(/،/g, '، ')
                .replace(/,/g, '، ')
                .trim()
              address.value = cleanAddress || `موقعیت: ${lat.value.toFixed(6)}, ${lng.value.toFixed(6)}`
            } else {
              address.value = `موقعیت: ${lat.value.toFixed(6)}, ${lng.value.toFixed(6)}`
            }
          } else {
            address.value = `موقعیت: ${lat.value.toFixed(6)}, ${lng.value.toFixed(6)}`
          }
        } catch (geErr) {
          console.error('Reverse geocoding error:', geErr)
          address.value = `موقعیت: ${lat.value.toFixed(6)}, ${lng.value.toFixed(6)}`
        }
        
        // Emit location selected
        emit('location-selected', { lat: lat.value, lng: lng.value, address: address.value })
        updateModelValue()
        
        console.log('Selected:', lat.value, lng.value)
      })
      
      // Set initial values
      if (props.modelValue.lat && props.modelValue.lng) {
        lat.value = props.modelValue.lat
        lng.value = props.modelValue.lng
        
        // Add marker at initial position
        marker = new neshanMapbox.Marker()
          .setLngLat([lng.value, lat.value])
          .addTo(map)
        map.setCenter([lng.value, lat.value])
        map.setZoom(15)
      }
      
      if (props.modelValue.address) {
        address.value = props.modelValue.address
      }
      
      mapLoaded.value = true
      console.log('Neshan location picker map initialized successfully')
    })
    
  } catch (error) {
    console.error('Error initializing Neshan location picker map:', error)
    mapLoaded.value = false
  }
}

// Handle address input change
const handleAddressChange = () => {
  updateModelValue()
}

// Update model value
const updateModelValue = () => {
  emit('update:modelValue', {
    lat: lat.value,
    lng: lng.value,
    address: address.value
  })
}

// Watch for prop changes
watch(() => props.modelValue, (newValue) => {
  if (newValue.lat && newValue.lng && map) {
    lat.value = newValue.lat
    lng.value = newValue.lng
    
    // Update marker position
    if (marker) {
      marker.remove()
    }
    marker = new neshanMapbox.Marker()
      .setLngLat([lng.value, lat.value])
      .addTo(map)
    map.setCenter([lng.value, lat.value])
    map.setZoom(15)
  }
  if (newValue.address) {
    address.value = newValue.address
  }
}, { deep: true })

// Initialize on mount
onMounted(() => {
  initMap().then(() => {
    // Fix hidden container sizing issues
    setTimeout(() => {
      if (map) {
        map.resize()
      }
    }, 200)

    // Resize on window resize
    window.addEventListener('resize', () => {
      if (map) {
        map.resize()
      }
    })
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
#neshan-location-map {
  height: 400px !important;
  width: 100% !important;
}
</style>
