<template>
  <div v-if="loading" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="text-center">
      <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600 font-medium">در حال بارگذاری...</p>
    </div>
  </div>

  <div v-else-if="!business" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-100">
    <div class="text-center max-w-md mx-auto px-6">
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      <h3 class="text-2xl font-bold text-gray-900 mb-3">کسب‌وکار یافت نشد</h3>
      <p class="text-gray-600 mb-6">کسب‌وکار مورد نظر وجود ندارد یا حذف شده است</p>
      <router-link to="/" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        بازگشت به صفحه اصلی
      </router-link>
    </div>
  </div>

  <div v-else-if="business" class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-black opacity-10"></div>
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'2\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
      </div>
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
            <li class="inline-flex items-center">
              <router-link to="/" class="text-blue-100 hover:text-white text-sm font-medium transition-colors">
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                خانه
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-blue-200 text-sm font-medium mr-2">{{ business.category?.name || 'دسته‌بندی' }}</span>
              </div>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-white text-sm font-medium mr-2">{{ business.name }}</span>
              </div>
            </li>
          </ol>
        </nav>

        <!-- Business Info -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
          <div class="flex-1">
            <div class="flex items-start space-x-6 space-x-reverse">
              <!-- Business Logo/Image -->
              <div class="flex-shrink-0">
                <div v-if="business.image_urls && business.image_urls.length > 0" class="w-24 h-24 rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white ring-opacity-20">
                  <img 
                    :src="business.image_urls[0].url" 
                    :alt="business.name"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                  >
                </div>
                <div v-else class="w-24 h-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center shadow-2xl ring-4 ring-white ring-opacity-20">
                  <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
              </div>

              <!-- Business Details -->
              <div class="flex-1 min-w-0">
                <h1 class="text-4xl font-bold mb-3">{{ business.name }}</h1>
                <p class="text-blue-100 text-lg mb-4">{{ business.description }}</p>
                
                <!-- Rating and Location -->
                <div class="flex items-center space-x-6 space-x-reverse mb-6">
                  <div class="flex items-center">
                    <div class="flex items-center">
                      <svg v-for="i in 5" :key="i" class="w-6 h-6 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                    </div>
                    <span class="text-white font-semibold mr-2">4.5</span>
                    <span class="text-blue-200">(12 نظر)</span>
                  </div>
                  <div class="flex items-center text-blue-100">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>تهران، ایران</span>
                  </div>
                </div>

                <!-- Status Badge -->
                <div class="flex items-center space-x-3 space-x-reverse">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">
                    <svg class="w-2 h-2 ml-1" fill="currentColor" viewBox="0 0 8 8">
                      <circle cx="4" cy="4" r="3"></circle>
                    </svg>
                    فعال
                  </span>
                  <span class="text-blue-200 text-sm">آخرین بروزرسانی: 2 روز پیش</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Action Buttons -->
          <div class="mt-8 lg:mt-0 lg:mr-8">
            <div class="flex flex-col space-y-3">
              <button class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                📞 تماس بگیرید
              </button>
              <button class="bg-white bg-opacity-20 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-opacity-30 transition-all duration-200 backdrop-blur-sm">
                💾 ذخیره کنید
              </button>
              <button class="bg-white bg-opacity-20 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-opacity-30 transition-all duration-200 backdrop-blur-sm">
                📤 اشتراک‌گذاری
              </button>
            </div>
          </div>
        </div>

        <!-- Social Media Links -->
        <div class="mt-8 flex flex-wrap items-center gap-4">
          <span class="text-blue-100 font-medium">ارتباط با ما:</span>
          
          <!-- Phone -->
          <a v-if="business.phone" :href="`tel:${business.phone}`" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 transition-all duration-200 backdrop-blur-sm">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            تماس
          </a>
          
          <!-- WhatsApp -->
          <a v-if="business.whatsapp" :href="`https://wa.me/${business.whatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            واتساپ
          </a>
          
          <!-- Telegram -->
          <a v-if="business.telegram" :href="`https://t.me/${business.telegram}`" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
            </svg>
            تلگرام
          </a>
          
          <!-- Instagram -->
          <a v-if="business.instagram" :href="`https://instagram.com/${business.instagram}`" target="_blank" class="inline-flex items-center px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            اینستاگرام
          </a>
          
          <!-- Website -->
          <a v-if="business.website" :href="business.website" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-all duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            وب‌سایت
          </a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Business Images Gallery -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900">گالری تصاویر</h2>
            </div>
            
            <div v-if="business.image_urls && business.image_urls.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-6">
              <div 
                v-for="(image, index) in business.image_urls" 
                :key="index" 
                class="group aspect-square bg-gray-100 rounded-2xl overflow-hidden cursor-pointer hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                @click="openImageModal(image.url, index)"
              >
                <img 
                  :src="image.url" 
                  :alt="`تصویر ${index + 1} از ${business.name}`"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  @error="handleImageError"
                >
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                  <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <p class="text-gray-500 text-lg">هیچ تصویری موجود نیست</p>
            </div>
          </div>

          <!-- Business Description -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900">درباره کسب و کار</h2>
            </div>
            <div class="text-gray-700 leading-relaxed text-lg whitespace-pre-line" v-html="formatDescription(business.fullDescription || business.description)"></div>
          </div>

          <!-- Services -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900">خدمات ما</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="feature in business.features" :key="feature.id" class="flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <span class="text-gray-800 font-medium">{{ feature.name }}</span>
              </div>
            </div>
          </div>

          <!-- Reviews Section -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center justify-between mb-8">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center ml-3">
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">نظرات مشتریان</h2>
              </div>
              <button class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                همه نظرات
              </button>
            </div>
            
            <!-- Review Stats -->
            <div class="flex items-center space-x-8 space-x-reverse mb-8">
              <div class="text-center p-6 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl">
                <div class="text-5xl font-bold text-gray-900 mb-2">4.5</div>
                <div class="flex items-center justify-center mb-2">
                  <svg v-for="i in 5" :key="i" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                  </svg>
                </div>
                <div class="text-sm text-gray-600 font-medium">بر اساس 12 نظر</div>
              </div>
              <div class="flex-1">
                <div v-for="i in 5" :key="i" class="flex items-center space-x-4 space-x-reverse mb-3">
                  <span class="text-sm text-gray-700 font-medium w-12">{{ 6-i }} ستاره</span>
                  <div class="flex-1 bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-3 rounded-full" :style="{ width: `${Math.random() * 100}%` }"></div>
                  </div>
                  <span class="text-sm text-gray-600 font-bold w-8">{{ Math.floor(Math.random() * 10) }}</span>
                </div>
              </div>
            </div>

            <!-- Individual Reviews -->
            <div class="space-y-6">
              <div v-for="i in 3" :key="i" class="group p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl hover:shadow-lg transition-all duration-200">
                <div class="flex items-start space-x-4 space-x-reverse">
                  <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <span class="text-lg font-bold text-white">م</span>
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center space-x-3 space-x-reverse mb-3">
                      <span class="font-bold text-gray-900 text-lg">مشتری {{ i }}</span>
                      <div class="flex items-center">
                        <svg v-for="j in 5" :key="j" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                      </div>
                      <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full">2 روز پیش</span>
                    </div>
                    <p class="text-gray-700 text-lg leading-relaxed">خدمات عالی و کیفیت خوب. حتماً دوباره مراجعه می‌کنم.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
          <!-- Contact Info Card -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900">اطلاعات تماس</h3>
            </div>
            
            <div class="space-y-6">
              <!-- Address -->
              <div v-if="business.address" class="group p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4 space-x-reverse">
                  <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mt-1 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-600 font-medium">📍 آدرس</p>
                    <p class="text-gray-900 font-medium leading-relaxed">{{ business.address }}</p>
                    <button class="text-orange-600 hover:text-orange-700 text-sm font-medium mt-2 transition-colors">🗺️ نمایش روی نقشه</button>
                  </div>
                </div>
              </div>
              
              <!-- Phone -->
              <div v-if="business.phone" class="group p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4 space-x-reverse">
                  <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-600 font-medium">📱 تلفن همراه</p>
                    <a :href="`tel:${business.phone}`" class="text-gray-900 font-bold text-lg hover:text-green-600 transition-colors">{{ business.phone }}</a>
                  </div>
                </div>
              </div>
              
              <!-- Landline -->
              <div v-if="business.landline" class="group p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4 space-x-reverse">
                  <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-600 font-medium">☎️ تلفن ثابت</p>
                    <a :href="`tel:${business.landline}`" class="text-gray-900 font-bold text-lg hover:text-blue-600 transition-colors">{{ business.landline }}</a>
                  </div>
                </div>
              </div>
              
              <!-- Email -->
              <div v-if="business.email" class="group p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4 space-x-reverse">
                  <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-600 font-medium">📧 ایمیل</p>
                    <a :href="`mailto:${business.email}`" class="text-gray-900 font-bold text-lg hover:text-purple-600 transition-colors break-all">{{ business.email }}</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Working Hours -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900">ساعات کاری</h3>
            </div>
            <div class="space-y-3">
              <div v-for="(hours, day) in workingHours" :key="day" class="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all duration-200">
                <span class="text-gray-700 font-medium">{{ getDayName(day) }}</span>
                <span class="text-gray-900 font-bold">{{ hours }}</span>
              </div>
            </div>
          </div>

          <!-- Map -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900">موقعیت</h3>
            </div>
            <NeshanMapStatic
              v-if="business.latitude && business.longitude"
              :latitude="business.latitude"
              :longitude="business.longitude"
              :business-name="business.name"
              :address="business.address"
            />
            <div v-else class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
              <div class="text-center">
                <div class="w-16 h-16 bg-gray-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <p class="text-gray-600 font-medium">موقعیت جغرافیایی موجود نیست</p>
              </div>
            </div>
          </div>

          <!-- Similar Businesses -->
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center ml-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900">کسب‌وکارهای مشابه</h3>
            </div>
            <div class="space-y-4">
              <div v-for="i in 3" :key="i" class="group flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl hover:shadow-md transition-all duration-200 cursor-pointer">
                <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">کسب‌وکار مشابه {{ i }}</h4>
                  <p class="text-sm text-gray-600">📍 تهران، ایران</p>
                  <div class="flex items-center mt-2">
                    <div class="flex items-center">
                      <svg v-for="j in 5" :key="j" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                    </div>
                    <span class="text-sm text-gray-500 mr-2">4.2</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      <h2 class="text-xl font-semibold text-gray-900 mb-2">کسب‌وکار یافت نشد</h2>
      <p class="text-gray-600 mb-4">کسب‌وکار مورد نظر شما وجود ندارد یا حذف شده است.</p>
      <router-link to="/" class="text-purple-600 hover:text-purple-700 font-medium">
        بازگشت به صفحه اصلی
      </router-link>
    </div>
  </div>

  <!-- Image Modal -->
  <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click="closeImageModal">
    <div class="relative max-w-4xl max-h-full p-4" @click.stop>
      <button 
        @click="closeImageModal"
        class="absolute top-4 right-4 text-white hover:text-gray-300 z-10"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      
      <div class="relative">
        <img 
          :src="selectedImage" 
          :alt="`تصویر ${selectedImageIndex + 1}`"
          class="max-w-full max-h-[80vh] object-contain rounded-lg"
        >
        
        <!-- Navigation arrows -->
        <button 
          v-if="business.image_urls && business.image_urls.length > 1"
          @click="previousImage"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        
        <button 
          v-if="business.image_urls && business.image_urls.length > 1"
          @click="nextImage"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
      
      <!-- Image counter -->
      <div v-if="business.image_urls && business.image_urls.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
        {{ selectedImageIndex + 1 }} از {{ business.image_urls.length }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import NeshanMapStatic from '@/components/NeshanMapStatic.vue'
import axios from 'axios'

const route = useRoute()
const business = ref(null)
const loading = ref(true)

// Image modal state
const showImageModal = ref(false)
const selectedImage = ref('')
const selectedImageIndex = ref(0)

const API_BASE_URL = 'http://localhost:8000/api'

// Working hours data
const workingHours = ref({
  monday: '9:00 - 18:00',
  tuesday: '9:00 - 18:00',
  wednesday: '9:00 - 18:00',
  thursday: '9:00 - 18:00',
  friday: '9:00 - 18:00',
  saturday: '9:00 - 18:00',
  sunday: 'تعطیل'
})

// Get day name in Persian
const getDayName = (day) => {
  const days = {
    monday: 'دوشنبه',
    tuesday: 'سه‌شنبه',
    wednesday: 'چهارشنبه',
    thursday: 'پنج‌شنبه',
    friday: 'جمعه',
    saturday: 'شنبه',
    sunday: 'یکشنبه'
  }
  return days[day] || day
}

// Format description to handle line breaks properly
const formatDescription = (text) => {
  if (!text) return ''
  return text
    .replace(/\n/g, '<br>')
    .replace(/\r\n/g, '<br>')
    .replace(/\r/g, '<br>')
}

const fetchBusiness = async () => {
  try {
    // Check if we have id or business (slug) parameter
    const businessParam = route.params.id || route.params.business
    console.log('Fetching business with param:', businessParam)
    console.log('Route params:', route.params)
    
    const response = await axios.get(`${API_BASE_URL}/businesses/${businessParam}`)
    console.log('Business response:', response.data)
    business.value = response.data.business
  } catch (error) {
    console.error('Error fetching business:', error)
    console.error('Error response:', error.response?.data)
    // If error, try to show error message
    business.value = null
  } finally {
    loading.value = false
  }
}

// Image modal methods
const openImageModal = (image, index) => {
  selectedImage.value = image
  selectedImageIndex.value = index
  showImageModal.value = true
}

const closeImageModal = () => {
  showImageModal.value = false
  selectedImage.value = ''
  selectedImageIndex.value = 0
}

const nextImage = () => {
  if (business.value.image_urls && business.value.image_urls.length > 0) {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % business.value.image_urls.length
    selectedImage.value = business.value.image_urls[selectedImageIndex.value].url
  }
}

const previousImage = () => {
  if (business.value.image_urls && business.value.image_urls.length > 0) {
    selectedImageIndex.value = selectedImageIndex.value === 0 
      ? business.value.image_urls.length - 1 
      : selectedImageIndex.value - 1
    selectedImage.value = business.value.image_urls[selectedImageIndex.value].url
  }
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  // You can add a fallback image here if needed
  event.target.style.display = 'none'
}

// Keyboard navigation for image modal
const handleKeydown = (event) => {
  if (showImageModal.value) {
    if (event.key === 'Escape') {
      closeImageModal()
    } else if (event.key === 'ArrowLeft') {
      previousImage()
    } else if (event.key === 'ArrowRight') {
      nextImage()
    }
  }
}

onMounted(() => {
  fetchBusiness()
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
/* Custom styles for RTL layout */
.space-x-reverse > :not([hidden]) ~ :not([hidden]) {
  --tw-space-x-reverse: 1;
  margin-right: calc(0.5rem * var(--tw-space-x-reverse));
  margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
}
</style>