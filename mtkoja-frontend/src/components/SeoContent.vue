<template>
  <section v-if="seo" class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h1 v-if="seo.h1" class="text-2xl font-bold text-gray-900 mb-4">{{ seo.h1 }}</h1>
    <img v-if="seo.og_image" :src="seo.og_image" alt="og" class="w-full max-h-64 object-cover rounded mb-4" />
    <div v-if="seo.content" class="prose prose-sm max-w-none" v-html="seo.content"></div>
    <div v-if="seo.custom_text" class="mt-4 text-gray-700">{{ seo.custom_text }}</div>
  </section>
  <section v-else class="hidden"></section>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'

const props = defineProps({
  seo: { type: Object, default: null },
  fallback: { type: Object, default: () => ({ title: '', description: '' }) },
  jsonLdData: { type: Object, default: null }
})

const pageTitle = computed(() => props.seo?.title || props.fallback.title)
const pageDescription = computed(() => props.seo?.description || props.fallback.description)
const canonicalUrl = computed(() => props.seo?.canonical || window.location.href)

// Update document head
const updateHead = () => {
  if (typeof document === 'undefined') return
  
  // Update title
  document.title = pageTitle.value
  
  // Update or create meta description
  let metaDesc = document.querySelector('meta[name="description"]')
  if (!metaDesc) {
    metaDesc = document.createElement('meta')
    metaDesc.name = 'description'
    document.head.appendChild(metaDesc)
  }
  metaDesc.content = pageDescription.value
  
  // Update or create canonical
  let canonical = document.querySelector('link[rel="canonical"]')
  if (!canonical) {
    canonical = document.createElement('link')
    canonical.rel = 'canonical'
    document.head.appendChild(canonical)
  }
  canonical.href = canonicalUrl.value
  
  // Update or create og:title
  let ogTitle = document.querySelector('meta[property="og:title"]')
  if (!ogTitle) {
    ogTitle = document.createElement('meta')
    ogTitle.setAttribute('property', 'og:title')
    document.head.appendChild(ogTitle)
  }
  ogTitle.content = pageTitle.value
  
  // Update or create og:description
  let ogDesc = document.querySelector('meta[property="og:description"]')
  if (!ogDesc) {
    ogDesc = document.createElement('meta')
    ogDesc.setAttribute('property', 'og:description')
    document.head.appendChild(ogDesc)
  }
  ogDesc.content = pageDescription.value
  
  // Update or create og:type
  let ogType = document.querySelector('meta[property="og:type"]')
  if (!ogType) {
    ogType = document.createElement('meta')
    ogType.setAttribute('property', 'og:type')
    document.head.appendChild(ogType)
  }
  ogType.content = props.seo?.og_type || 'website'
  
  // Update or create og:image
  if (props.seo?.og_image) {
    let ogImage = document.querySelector('meta[property="og:image"]')
    if (!ogImage) {
      ogImage = document.createElement('meta')
      ogImage.setAttribute('property', 'og:image')
      document.head.appendChild(ogImage)
    }
    ogImage.content = props.seo.og_image
  }
  
  // Update or create keywords
  if (props.seo?.keywords) {
    let keywords = document.querySelector('meta[name="keywords"]')
    if (!keywords) {
      keywords = document.createElement('meta')
      keywords.name = 'keywords'
      document.head.appendChild(keywords)
    }
    keywords.content = props.seo.keywords
  }
  
  // Update or create JSON-LD
  if (props.jsonLdData) {
    let jsonLdScript = document.querySelector('script[type="application/ld+json"]')
    if (!jsonLdScript) {
      jsonLdScript = document.createElement('script')
      jsonLdScript.type = 'application/ld+json'
      document.head.appendChild(jsonLdScript)
    }
    jsonLdScript.textContent = JSON.stringify(props.jsonLdData, null, 2)
  }
}

// Watch for changes and update head
watch([pageTitle, pageDescription, canonicalUrl, () => props.seo, () => props.jsonLdData, () => props.seo?.keywords], updateHead, { immediate: true })

onMounted(updateHead)
</script>

<style scoped>
.prose :where(img):not(:where([class~="not-prose"] *)){
  margin-top: 0;
  margin-bottom: 0;
}
</style>


