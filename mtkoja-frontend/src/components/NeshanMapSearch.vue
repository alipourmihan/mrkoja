<template>
  <div class="w-full">
    <!-- Search Box -->
    <div class="relative mb-4">
      <div class="relative">
        <input
          v-model="searchQuery"
          @input="searchLocation"
          @focus="showSuggestions = true"
          @blur="hideSuggestions"
          type="text"
          placeholder="جستجو در نقشه (مثال: میدان آزادی، تهران)"
          class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-iran"
          dir="rtl"
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </div>
      
      <!-- Search Suggestions -->
      <div 
        v-if="showSuggestions && suggestions.length > 0" 
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
      >
        <div 
          v-for="(item, index) in suggestions" 
          :key="index"
          @click="selectSuggestion(item)"
          class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
        >
          <div class="font-medium text-gray-900 font-iran">{{ item.title }}</div>
          <div class="text-sm text-gray-600 font-iran">{{ item.address }}</div>
        </div>
      </div>
      
      <!-- Loading State -->
      <div v-if="searching" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
      </div>
    </div>

    <!-- Map Container -->
    <div 
      id="neshan-search-map" 
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
    
    <!-- Address Input Field -->
    <div class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">
        آدرس دقیق کسب‌وکار <span class="text-red-500">*</span>
      </label>
      <textarea
        v-model="addressInput"
        @input="handleAddressInput"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        rows="3"
        placeholder="آدرس کامل و دقیق کسب‌وکار خود را وارد کنید"
        dir="rtl"
        required
      ></textarea>
      <div class="text-sm text-gray-500 mt-1 font-iran">
        مثال: تهران، خیابان ولیعصر، پلاک 123، طبقه دوم
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
  },
  initialLocation: {
    type: Object,
    default: () => ({ lat: 35.6892, lng: 51.3890 })
  }
})

const emit = defineEmits(['update:modelValue', 'location-selected'])

// Reactive data
const mapLoaded = ref(false)
const error = ref('')
const searchQuery = ref('')
const suggestions = ref([])
const showSuggestions = ref(false)
const searching = ref(false)
const selectedLocation = ref({
  lat: null,
  lng: null,
  address: ''
})
const addressInput = ref('')

let map = null
let marker = null
let debounceTimer = null

// Neshan API Key for map display
const NESHAN_MAP_KEY = 'web.90d955cf6f12443fa7b0654067a86f63'
// Note: Search uses OpenStreetMap Nominatim (free service) instead of Neshan due to API key limitations

// Initialize map
const initMap = async () => {
  try {
    // Wait for DOM to be ready
    await nextTick()
    
    // Wait a bit more for container to be visible
    await new Promise(resolve => setTimeout(resolve, 100))
    
    const mapElement = document.getElementById('neshan-search-map')
    if (!mapElement) {
      console.error('Map container not found')
      return
    }

    // Default position (Tehran)
    const defaultPosition = [props.initialLocation?.lng || 51.3890, props.initialLocation?.lat || 35.6892]
    
    // Create map
    console.log('Creating Neshan search map...')
    map = new neshanMapbox.Map({
      mapType: neshanMapbox.Map.mapTypes.neshanVector,
      container: 'neshan-search-map',
      zoom: 13,
      center: defaultPosition,
      mapKey: NESHAN_MAP_KEY,
      poi: true,
      traffic: false,
      mapTypeControllerOptions: {
        show: true,
        position: 'bottom-left'
      }
    })
    
    // Wait for map to load
    map.on('load', () => {
      // Add click listener for manual selection
      map.on('click', async (e) => {
        const lngLat = e.lngLat
        selectedLocation.value = {
          lat: lngLat.lat,
          lng: lngLat.lng,
          address: addressInput.value // Keep user's manual address input
        }
        
        // Update marker
        updateMarker(lngLat.lat, lngLat.lng)
        
        // Emit location selected
        emit('location-selected', selectedLocation.value)
        updateModelValue()
        
        console.log('Manual selection:', lngLat.lat, lngLat.lng)
      })
      
      // Set initial values
      if (props.modelValue && props.modelValue.lat && props.modelValue.lng) {
        selectedLocation.value = {
          lat: props.modelValue.lat,
          lng: props.modelValue.lng,
          address: props.modelValue.address || ''
        }
        
        // Add marker at initial position
        updateMarker(props.modelValue.lat, props.modelValue.lng)
        map.setCenter([props.modelValue.lng, props.modelValue.lat])
        map.setZoom(15)
      }
      
      mapLoaded.value = true
      console.log('Neshan search map initialized successfully')
    })
    
  } catch (error) {
    console.error('Error initializing Neshan search map:', error)
    error.value = error.message
    mapLoaded.value = true // Set to true to hide loading state even on error
  }
}

// Search location with debounce
const searchLocation = () => {
  clearTimeout(debounceTimer)
  
  if (searchQuery.value.length < 3) {
    suggestions.value = []
    return
  }
  
  debounceTimer = setTimeout(async () => {
    await performSearch()
  }, 300)
}

// Perform search using OpenStreetMap Nominatim (free service)
const performSearch = async () => {
  try {
    searching.value = true
    
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery.value)}&format=json&limit=5&addressdetails=1&accept-language=fa&countrycodes=ir`
    )
    
    if (response.ok) {
      const data = await response.json()
      // Convert OSM format to our expected format
      suggestions.value = data.map(item => ({
        title: item.display_name.split(',')[0] || item.name || 'موقعیت نامشخص',
        address: item.display_name,
        location: {
          x: parseFloat(item.lon),
          y: parseFloat(item.lat)
        }
      }))
    } else {
      console.error('Search API error:', response.status)
      suggestions.value = []
    }
  } catch (err) {
    console.error('Search error:', err)
    suggestions.value = []
  } finally {
    searching.value = false
  }
}

// Select suggestion
const selectSuggestion = (item) => {
  searchQuery.value = item.title
  suggestions.value = []
  showSuggestions.value = false
  
  const lat = item.location.y
  const lng = item.location.x
  
  selectedLocation.value = {
    lat,
    lng,
    address: addressInput.value // Keep user's manual address input
  }
  
  // Update marker
  updateMarker(lat, lng)
  
  // Fly to location
  if (map) {
    map.flyTo({ 
      center: [lng, lat], 
      zoom: 16,
      duration: 1000
    })
  }
  
  // Emit location selected
  emit('location-selected', selectedLocation.value)
  updateModelValue()
  
  console.log('Selected suggestion:', item)
}

// Update marker
const updateMarker = (lat, lng) => {
  if (!map) return
  
  // Remove existing marker
  if (marker) {
    marker.remove()
  }
  
  // Add new marker
  marker = new neshanMapbox.Marker()
    .setLngLat([lng, lat])
    .addTo(map)
}

// Hide suggestions
const hideSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

// Handle address input change
const handleAddressInput = () => {
  selectedLocation.value.address = addressInput.value
  updateModelValue()
}

// Update model value
const updateModelValue = () => {
  emit('update:modelValue', {
    lat: selectedLocation.value.lat,
    lng: selectedLocation.value.lng,
    address: addressInput.value
  })
}

// Watch for prop changes
watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue.lat && newValue.lng && map) {
    selectedLocation.value = {
      lat: newValue.lat,
      lng: newValue.lng,
      address: newValue.address || ''
    }
    
    // Update address input
    addressInput.value = newValue.address || ''
    
    // Update marker position
    updateMarker(newValue.lat, newValue.lng)
    map.setCenter([newValue.lng, newValue.lat])
    map.setZoom(15)
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
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
})
</script>

<style scoped>
#neshan-search-map {
  height: 400px !important;
  width: 100% !important;
}

/* Custom scrollbar for suggestions */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
