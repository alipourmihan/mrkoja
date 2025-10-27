<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4 space-x-reverse">
            <button @click="goBack" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full p-2 transition-colors">
              ←
            </button>
            <h1 class="text-2xl font-bold font-iran">
              {{ isEditMode ? 'ویرایش کسب‌وکار' : 'ثبت کسب‌وکار جدید' }}
            </h1>
          </div>
          <div class="text-sm opacity-90">
            مرحله {{ currentStep }} از 10
          </div>
        </div>
      </div>
    </header>

    <!-- Progress Bar -->
    <div class="bg-white shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="progress-bar bg-gradient-to-r from-purple-500 to-blue-500 h-2 rounded-full transition-all duration-300" 
               :style="{ width: progressPercentage + '%' }"></div>
        </div>
      </div>
    </div>

    <!-- Main Form Container -->
    <form class="container mx-auto px-4 py-8" enctype="multipart/form-data" @submit.prevent="submitForm">
      <!-- Loading State for Edit Mode -->
      <div v-if="isEditMode && loading" class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mb-4"></div>
        <p class="text-gray-600 font-iran">در حال بارگذاری اطلاعات کسب‌وکار...</p>
      </div>
      
      <div v-else class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
        
        <!-- Step 1: Business Name -->
        <div v-if="currentStep === 1" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">نام کسب‌وکارتون رو بنویسید</h2>
          <p class="text-gray-600 mb-6">با توجه به راهنمای زیر، نام کسب‌وکارتون رو بنویسید تا در جست‌وجو‌ها بهتر دیده بشید.</p>
          
          <div class="mb-6">
            <input 
              v-model="formData.name" 
              type="text" 
              placeholder="نام کسب‌وکار..." 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-lg font-iran"
              maxlength="30"
            >
            <div class="text-sm text-gray-500 mt-2">حداکثر 30 حرف</div>
          </div>

          <!-- Guidelines -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between cursor-pointer" @click="toggleGuidelines">
              <span class="font-medium text-blue-800 font-iran">چه‌جوری نام کسب‌وکارتون رو در بهترینو به درستی ثبت کنید؟</span>
              <span class="text-blue-600">{{ showGuidelines ? '−' : '+' }}</span>
            </div>
            
            <div v-if="showGuidelines" class="mt-4 text-sm text-blue-700">
              <p class="mb-3">نام و تصویر کسب‌وکارتون اولین چیزیه که مشتریانتون می‌بینن.</p>
              <p class="mb-3 font-medium">نام کسب‌وکارتون رو این‌طوری ثبت کنید:</p>
              <ul class="space-y-2 mb-4">
                <li>• مرتبط با حوزهٔ کسب‌وکارتون باشه</li>
                <li>• حداکثر ۳۰ حرف باشه، بدون ایموجی و علائم نگارشی</li>
                <li>• شامل هیچ‌یک از این کاراکترها نباشد: !@#$%^&*?/|\</li>
                <li>• اسم برندتون در نام انتخابی باشه</li>
              </ul>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="font-medium text-green-700 mb-2">✓ مثال مناسب:</p>
                  <p class="text-sm">خدمات تخصصی ناخن پرستو</p>
                  <p class="text-sm">رستوران مهدی</p>
                </div>
                <div>
                  <p class="font-medium text-red-700 mb-2">✗ مثال نامناسب:</p>
                  <p class="text-sm">انجام کلیه خدمات ناخن و رنگ مو</p>
                  <p class="text-sm">🍕 بهترین پیتزای منطقه!!</p>
                </div>
              </div>
            </div>
          </div>

          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 2: Address & Location -->
        <div v-if="currentStep === 2" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">آدرس و ثبت در لوکیشن</h2>
          <p class="text-gray-600 mb-6">وارد کردن موقعیت دقیق و آدرس صحیح، به مشتریان کمک می‌کند راحت‌تر کسب‌وکار شما را پیدا کنند.</p>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">موقعیت مکانی روی نقشه</label>
            <p class="text-sm text-gray-600 mb-4">
              برای انتخاب دقیق موقعیت کسب‌وکار، در فیلد جستجو تایپ کنید یا روی نقشه کلیک کنید
            </p>
        <NeshanMapSearch
          v-model="locationData"
          @location-selected="setLocation"
        />
          </div>



          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 3: Business Category -->
        <div v-if="currentStep === 3" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">انتخاب صنف کسب‌وکار</h2>
          <p class="text-gray-600 mb-6">لطفاً ابتدا دسته‌بندی اصلی و سپس زیردسته مناسب کسب‌وکار خود را انتخاب کنید.</p>
          
          <!-- Parent Category Selection -->
          <div class="mb-8">
            <label class="block mb-4 text-gray-700 font-iran text-lg">انتخاب دسته‌بندی اصلی</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <label 
                v-for="cat in parentCategories" 
                :key="cat.id"
                class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 transition-colors"
                :class="{ 'bg-red-100 border-red-500': selectedParentCategoryId === cat.id }"
              >
                <input
                  type="radio"
                  :value="cat.id"
                  v-model="selectedParentCategoryId"
                  @change="selectedCategoryId = null"
                  class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 ml-3"
                />
                <span class="text-sm font-medium text-gray-900 font-iran">{{ cat.name }}</span>
              </label>
            </div>
          </div>

          <!-- Child Category Selection -->
          <div v-if="selectedParentCategoryId && childCategories.length > 0" class="mb-6">
            <label class="block mb-4 text-gray-700 font-iran text-lg">انتخاب زیردسته</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
              <label 
                v-for="cat in childCategories" 
                :key="cat.id"
                class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 transition-colors"
                :class="{ 'bg-red-100 border-red-500': selectedCategoryId === cat.id }"
              >
                <input
                  type="radio"
                  :value="cat.id"
                  v-model="selectedCategoryId"
                  class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 ml-3"
                />
                <span class="text-sm font-medium text-gray-900 font-iran">{{ cat.name }}</span>
              </label>
            </div>
          </div>

          <!-- Selected Category Display -->
          <div v-if="selectedParentCategoryId" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 mb-2 font-iran">
              دسته‌بندی اصلی: <span class="font-semibold text-red-600">{{ selectedParentCategoryName }}</span>
            </p>
            <p v-if="selectedCategoryId" class="text-sm text-gray-600 font-iran">
              زیردسته: <span class="font-semibold text-red-600">{{ selectedChildCategoryName }}</span>
            </p>
          </div>

          <button 
            @click="nextStep" 
            :disabled="!selectedParentCategoryId || (childCategories.length > 0 && !selectedCategoryId)"
            class="w-full bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition-colors font-iran disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            ادامه
          </button>
        </div>

        <!-- Step 4: Keywords -->
        <div v-if="currentStep === 4" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">کلمات کلیدی</h2>
          <p class="text-gray-600 mb-6">انتخاب کلیدواژه‌های مناسب باعث می‌شود مشتریان راحت‌تر کسب‌وکار شما را پیدا کنند.</p>
          
          <div class="mb-6">
            <input 
              v-model="keywordSearch" 
              type="text" 
              placeholder="جستجوی کلیدواژه..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
            >
          </div>

          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-3 font-iran">کلیدواژه‌های انتخاب شده:</h3>
            <div class="min-h-[50px] border-2 border-dashed border-gray-300 rounded-lg p-4">
              <div v-if="selectedKeywords.length === 0" class="text-gray-500 text-sm font-iran">
                هیچ کلیدواژه‌ای انتخاب نشده است.
              </div>
              <div v-else class="flex flex-wrap gap-2">
                <span 
                  v-for="keyword in selectedKeywords" 
                  :key="keyword"
                  class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"
                >
                  {{ keyword }}
                  <button @click="toggleKeyword(keyword)" class="mr-2 text-purple-600 hover:text-purple-800">×</button>
                </span>
              </div>
            </div>
          </div>

          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-3 font-iran">کلیدواژه‌های پیشنهادی</h3>
            <p class="text-sm text-gray-600 mb-3 font-iran">(در صنف رستوران و کافه رستوران)</p>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="keyword in filteredKeywords" 
                :key="keyword"
                class="keyword-tag px-3 py-2 rounded-full text-sm transition-colors"
                :class="selectedKeywords.includes(keyword) ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700 hover:bg-purple-100 hover:text-purple-700'"
                @click="toggleKeyword(keyword)"
              >
                {{ keyword }}
              </button>
            </div>
          </div>

          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 5: Short Description -->
        <div v-if="currentStep === 5" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">توضیحات کوتاه</h2>
          <p class="text-gray-600 mb-6">این توضیحات در نتایج جستجو و خلاصه کسب‌وکار شما نمایش داده می‌شود.</p>
          
          <div class="mb-6">
            <textarea 
              v-model="formData.description" 
              placeholder="توضیحات کوتاهی درباره کسب‌وکار خود بنویسید..." 
              rows="4" 
              maxlength="140"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none font-iran"
            ></textarea>
            <div class="flex justify-between text-sm text-gray-500 mt-2">
              <span class="font-iran">حداکثر ۱۴۰ حرف مجاز است</span>
              <span :class="{ 'text-red-500': formData.description.length > 126 }">{{ formData.description.length }}/۱۴۰</span>
            </div>
          </div>

          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 6: Full Description -->
        <div v-if="currentStep === 6" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">توضیحات کامل</h2>
          <p class="text-gray-600 mb-6">توضیح دقیق و کامل درباره خدمات و محصولات کسب‌وکار شما باعث می‌شود مشتریان اطلاعات بیشتری درباره کسب‌وکار شما داشته باشند.</p>
          
          <div class="mb-6">
            <textarea 
              v-model="formData.fullDescription" 
              placeholder="توضیحات کاملی درباره خدمات و محصولات کسب‌وکار خود بنویسید..." 
              rows="8" 
              maxlength="1000"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none font-iran"
            ></textarea>
            <div class="flex justify-between text-sm text-gray-500 mt-2">
              <span class="font-iran">حداقل ۳۰ و حداکثر ۱۰۰۰ حرف مجاز است</span>
              <span :class="{ 'text-red-500': formData.fullDescription.length > 900 }">{{ formData.fullDescription.length }}/۱۰۰۰</span>
            </div>
          </div>

          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 7: Phone Numbers -->
        <div v-if="currentStep === 7" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">شماره تلفن اصلی</h2>
          <p class="text-gray-600 mb-6">شماره‌های تماس فعال باعث می‌شود مشتریان راحت‌تر با شما ارتباط برقرار کنند.</p>
          
          <div class="space-y-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">شماره موبایل (باید با 09 شروع شود) *</label>
              <input 
                v-model="formData.phone" 
                type="tel" 
                placeholder="مثال: 09123456789"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
                pattern="09[0-9]{9}" 
                maxlength="11"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">شماره تلفن ثابت (اختیاری)</label>
              <input 
                v-model="formData.landlinePhone" 
                type="tel" 
                placeholder="مثال: 02112345678"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
              >
              <div class="text-sm text-gray-500 mt-1 font-iran">شماره تلفن ثابت باید با کد شهر (مثلا 021 برای تهران) وارد شود</div>
            </div>
          </div>

          <button @click="nextStep" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
            ادامه
          </button>
        </div>

        <!-- Step 8: Images and Videos -->
        <div v-if="currentStep === 8" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 font-iran">تصاویر و ویدیوهای کسب‌وکار</h2>
          <p class="text-gray-600 mb-6">تصاویر و ویدیوهای باکیفیت باعث جلب توجه بیشتر مشتریان می‌شود.</p>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">برای قرار دادن تصاویر سرویس و خدمات خود</label>
            <div 
              class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-400 transition-colors cursor-pointer"
              @click="$refs.fileInput.click()"
            >
              <div class="text-4xl text-gray-400 mb-4">📷</div>
              <p class="text-gray-600 mb-2 font-iran">فایل‌های خود را اینجا بکشید یا کلیک کنید</p>
              <p class="text-sm text-gray-500 font-iran">فرمت‌های مجاز تصویر: jpg, jpeg, png, webp</p>
            </div>
            <input 
              ref="fileInput"
              type="file" 
              multiple 
              accept="image/*" 
              class="hidden" 
              @change="handleFileSelect"
            >
          </div>

          <div v-if="uploadedFiles.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div 
              v-for="(file, index) in uploadedFiles" 
              :key="index"
              class="relative bg-gray-100 rounded-lg p-4 text-center"
            >
              <div v-if="file.type.startsWith('image/')" class="mb-2">
                <img :src="file.url" :alt="file.name" class="w-full h-24 object-cover rounded">
              </div>
              <div v-else class="text-4xl mb-2">🎥</div>
              <p class="text-sm text-gray-600 truncate font-iran">{{ file.name }}</p>
              <button 
                @click="removeFile(index)" 
                class="absolute top-2 left-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs hover:bg-red-600"
              >×</button>
            </div>
          </div>

          <div class="flex space-x-4 space-x-reverse">
            <button @click="nextStep" class="flex-1 bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
              ذخیره و ادامه
            </button>
            <button @click="nextStep" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-iran">
              رد کردن
            </button>
          </div>
        </div>

        <!-- Step 9: Social Media -->
        <div v-if="currentStep === 9" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-2 font-iran">شبکه‌های اجتماعی و وب‌سایت</h2>
          <p class="text-sm text-gray-500 mb-6 font-iran">مرحله ۹ از ۱۰</p>
          
          <div class="space-y-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">وب‌سایت</label>
              <div class="flex">
                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">www.</span>
                <input 
                  v-model="formData.website" 
                  type="text" 
                  placeholder="example.com"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
                >
              </div>
              <div class="text-sm text-gray-500 mt-1 font-iran">آدرس وبسایت را بدون http:// یا www وارد کنید (مثال: example.com)</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">اینستاگرام</label>
              <div class="flex">
                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">@</span>
                <input 
                  v-model="formData.instagram" 
                  type="text" 
                  placeholder="username"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
                >
              </div>
              <div class="text-sm text-gray-500 mt-1 font-iran">فقط نام کاربری را بدون @ یا https://www.instagram.com وارد کنید (مثال: username)</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">تلگرام</label>
              <div class="flex">
                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">@</span>
                <input 
                  v-model="formData.telegram" 
                  type="text" 
                  placeholder="username"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
                >
              </div>
              <div class="text-sm text-gray-500 mt-1 font-iran">فقط نام کاربری را بدون @ یا https://t.me وارد کنید (مثال: username)</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 font-iran">واتس‌اپ</label>
              <div class="flex">
                <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">+98</span>
                <input 
                  v-model="formData.whatsapp" 
                  type="tel" 
                  placeholder="9123456789"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500 font-iran"
                  maxlength="10"
                >
              </div>
              <div class="text-sm text-gray-500 mt-1 font-iran">شماره را دقیقاً ۱۰ رقم، بدون صفر اول و با اعداد انگلیسی وارد کنید</div>
            </div>
          </div>

          <div class="flex space-x-4 space-x-reverse">
            <button @click="nextStep" class="flex-1 bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors font-iran">
              ذخیره و ادامه
            </button>
            <button @click="nextStep" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-iran">
              رد کردن
            </button>
          </div>
        </div>

        <!-- Step 10: Working Hours -->
        <div v-if="currentStep === 10" class="step-content p-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-2 font-iran">ساعات کاری</h2>
          <p class="text-sm text-gray-500 mb-6 font-iran">مرحله ۱۰ از ۱۰</p>
          
          <div class="space-y-4 mb-6">
            <div class="working-hours-grid">
              <div class="font-medium text-gray-700 font-iran">روز</div>
              <div class="font-medium text-gray-700 font-iran">وضعیت</div>
              <div class="font-medium text-gray-700 font-iran">ساعات کاری</div>
            </div>
            
            <div 
              v-for="day in daysOfWeek" 
              :key="day.id"
              class="working-hours-grid py-3 border-b border-gray-200"
            >
              <div class="font-medium text-gray-700 font-iran">{{ day.name }}</div>
              <div>
                <label class="flex items-center">
                  <input 
                    v-model="workingHours[day.id].active" 
                    type="checkbox" 
                    class="ml-2"
                    @change="toggleDayHours(day.id)"
                  >
                  <span class="text-sm font-iran">روز کاری</span>
                </label>
              </div>
              <div 
                class="flex space-x-2 space-x-reverse"
                :class="{ 'opacity-50': !workingHours[day.id].active }"
              >
                <input 
                  v-model="workingHours[day.id].start" 
                  type="time" 
                  :disabled="!workingHours[day.id].active"
                  class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
                >
                <span class="text-gray-500 font-iran">تا</span>
                <input 
                  v-model="workingHours[day.id].end" 
                  type="time" 
                  :disabled="!workingHours[day.id].active"
                  class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
                >
              </div>
            </div>
          </div>

          <div class="flex space-x-4 space-x-reverse">
            <button @click="submitForm" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition-colors font-iran">
              {{ isEditMode ? 'ذخیره تغییرات' : 'ثبت کسب‌وکار' }}
            </button>
            <button @click="nextStep" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-iran">
              رد کردن
            </button>
          </div>
        </div>

      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useBusinessStore } from '@/stores/business'
import { useCategoryStore } from '@/stores/category'
import { useAuthStore } from '@/stores/auth'
import NeshanMapSearch from '@/components/NeshanMapSearch.vue'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const businessStore = useBusinessStore()
const categoryStore = useCategoryStore()
const authStore = useAuthStore()

// Loading state
const loading = ref(false)

// Edit mode detection
const isEditMode = computed(() => route.name === 'BusinessEdit' && route.params.id)
const businessId = computed(() => route.params.id)

// Location data
const locationData = ref({
  lat: null,
  lng: null,
  address: ''
})

// Form data
const formData = ref({
  name: '',
  address: '',
  description: '',
  fullDescription: '',
  phone: '',
  landlinePhone: '',
  email: '',
  website: '',
  instagram: '',
  telegram: '',
  whatsapp: '',
  latitude: '',
  longitude: '',
  images: [],
  features: [],
  working_hours: {}
})

// Form state
const currentStep = ref(1)
const showGuidelines = ref(false)
const categorySearch = ref('')
const selectedParentCategoryId = ref(null)
const keywordSearch = ref('')
const selectedCategoryId = ref(null)
const selectedKeywords = ref([])
const uploadedFiles = ref([])

// Computed properties
const categories = computed(() => categoryStore.activeCategories)
const parentCategories = computed(() => categories.value.filter(c => !c.parent_id))
const childCategories = computed(() => {
  if (!selectedParentCategoryId.value) return []
  return categories.value.filter(c => c.parent_id === selectedParentCategoryId.value)
})

// Keywords data
const keywords = ref({
  restaurant: ['بوفه', 'بوفه صبحانه', 'صبحانه سلف سرویس', 'غذای سنتی', 'غذای فرنگی', 'پیتزا', 'برگر', 'کباب'],
  cafe: ['قهوه تخصصی', 'کیک و شیرینی', 'نوشیدنی گرم', 'نوشیدنی سرد', 'فضای کار', 'WiFi'],
  beauty: ['کوتاهی مو', 'رنگ مو', 'فر مو', 'ماساژ', 'فیشیال', 'مانیکور', 'پدیکور'],
  health: ['دندانپزشکی', 'چشم‌پزشکی', 'فیزیوتراپی', 'آزمایشگاه', 'داروخانه', 'مشاوره'],
  shop: ['پوشاک', 'کفش', 'لوازم خانگی', 'الکترونیک', 'موبایل', 'لپ‌تاپ', 'کیف'],
  service: ['تعمیرات', 'نظافت', 'باربری', 'نقاشی ساختمان', 'تاسیسات', 'برق']
})

// Days of week
const daysOfWeek = ref([
  { id: 'saturday', name: 'شنبه' },
  { id: 'sunday', name: 'یکشنبه' },
  { id: 'monday', name: 'دوشنبه' },
  { id: 'tuesday', name: 'سه‌شنبه' },
  { id: 'wednesday', name: 'چهارشنبه' },
  { id: 'thursday', name: 'پنجشنبه' },
  { id: 'friday', name: 'جمعه' }
])

// Working hours
const workingHours = ref({})
daysOfWeek.value.forEach(day => {
  workingHours.value[day.id] = {
    active: true,
    start: '09:00',
    end: '18:00'
  }
})

// Computed properties
const progressPercentage = computed(() => (currentStep.value / 10) * 100)


const selectedParentCategoryName = computed(() => {
  if (selectedParentCategoryId.value) {
    const category = parentCategories.value.find(cat => cat.id === selectedParentCategoryId.value)
    return category ? category.name : ''
  }
  return ''
})

const selectedChildCategoryName = computed(() => {
  if (selectedCategoryId.value) {
    const category = childCategories.value.find(cat => cat.id === selectedCategoryId.value)
    return category ? category.name : ''
  }
  return ''
})

// preserved for potential future search usage (not used in hierarchy UI)
const filteredCategories = computed(() => {
  if (!categorySearch.value) return categories.value
  return categories.value.filter(cat => 
    cat.name.toLowerCase().includes(categorySearch.value.toLowerCase())
  )
})

const filteredKeywords = computed(() => {
  if (!keywordSearch.value) return selectedKeywords.value
  
  return selectedKeywords.value.filter(keyword => 
    keyword.toLowerCase().includes(keywordSearch.value.toLowerCase())
  )
})

// Methods
const nextStep = () => {
  if (validateCurrentStep()) {
    if (currentStep.value < 10) {
      currentStep.value++
    }
  }
}

const goBack = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  } else {
    router.back()
  }
}

const validateCurrentStep = () => {
  console.log('Validating step:', currentStep.value)
  
  switch(currentStep.value) {
    case 1:
      if (!formData.value.name.trim()) {
        alert('لطفاً نام کسب‌وکار را وارد کنید')
        return false
      }
      if (formData.value.name.length > 30) {
        alert('نام کسب‌وکار نباید بیش از 30 حرف باشد')
        return false
      }
      break
    case 2:
      if (!locationData.value.address || !locationData.value.address.trim()) {
        alert('لطفاً آدرس دقیق کسب‌وکار را وارد کنید')
        return false
      }
      if (!locationData.value.lat || !locationData.value.lng) {
        alert('لطفاً موقعیت کسب‌وکار را روی نقشه انتخاب کنید')
        return false
      }
      break
    case 3:
      if (!selectedParentCategoryId.value) {
        alert('لطفاً یک صنف انتخاب کنید')
        return false
      }
      break
    case 4:
      // No validation needed for features selection
      break
    case 5:
      if (!formData.value.description.trim()) {
        alert('لطفاً توضیحات کوتاه را وارد کنید')
        return false
      }
      break
    case 6:
      if (!formData.value.fullDescription.trim() || formData.value.fullDescription.length < 30) {
        alert('توضیحات کامل باید حداقل 30 حرف باشد')
        return false
      }
      break
    case 7:
      if (!formData.value.phone.trim() || !formData.value.phone.match(/^09[0-9]{9}$/)) {
        alert('لطفاً شماره موبایل معتبر وارد کنید (مثال: 09123456789)')
        return false
      }
      break
    case 8:
      // No validation needed for additional info
      break
    case 9:
      // No validation needed for working hours
      break
    case 10:
      // Final validation for all required fields
      if (!formData.value.name.trim()) {
        alert('لطفاً نام کسب‌وکار را وارد کنید')
        return false
      }
      if (!locationData.value.address || !locationData.value.address.trim()) {
        alert('لطفاً آدرس دقیق کسب‌وکار را وارد کنید')
        return false
      }
      if (!locationData.value.lat || !locationData.value.lng) {
        alert('لطفاً موقعیت کسب‌وکار را روی نقشه انتخاب کنید')
        return false
      }
      if (!selectedParentCategoryId.value) {
        alert('لطفاً یک صنف انتخاب کنید')
        return false
      }
      if (!formData.value.description.trim()) {
        alert('لطفاً توضیحات کوتاه را وارد کنید')
        return false
      }
      if (!formData.value.fullDescription.trim() || formData.value.fullDescription.length < 30) {
        alert('توضیحات کامل باید حداقل 30 حرف باشد')
        return false
      }
      if (!formData.value.phone.trim() || !formData.value.phone.match(/^09[0-9]{9}$/)) {
        alert('لطفاً شماره موبایل معتبر وارد کنید (مثال: 09123456789)')
        return false
      }
      break
  }
  
  console.log('Validation result: true')
  return true
}

const toggleGuidelines = () => {
  showGuidelines.value = !showGuidelines.value
}

const selectCategory = (categoryId, categoryName) => {
  selectedCategoryId.value = categoryId
}

const toggleKeyword = (keyword) => {
  const index = selectedKeywords.value.indexOf(keyword)
  if (index > -1) {
    selectedKeywords.value.splice(index, 1)
  } else {
    selectedKeywords.value.push(keyword)
  }
}

const handleFileSelect = async (event) => {
  const files = Array.from(event.target.files)
  for (const file of files) {
    if (uploadedFiles.value.length >= 10) break
    if (!file.type.startsWith('image/')) continue
    const compressed = await compressImage(file, 1600, 0.8)
    const fileObj = {
      file: compressed.blob,
      name: compressed.name,
      type: compressed.blob.type,
      size: compressed.blob.size,
      url: URL.createObjectURL(compressed.blob)
    }
    uploadedFiles.value.push(fileObj)
  }
}

const compressImage = (file, maxSize = 1600, quality = 0.8) => {
  return new Promise((resolve) => {
    const img = new Image()
    const url = URL.createObjectURL(file)
    img.onload = () => {
      let { width, height } = img
      const scale = Math.min(1, maxSize / Math.max(width, height))
      width = Math.round(width * scale)
      height = Math.round(height * scale)
      const canvas = document.createElement('canvas')
      canvas.width = width
      canvas.height = height
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, width, height)
      canvas.toBlob((blob) => {
        URL.revokeObjectURL(url)
        const name = file.name.replace(/\.(png|jpg|jpeg|webp|gif)$/i, '.jpg')
        resolve({ blob: blob || file, name })
      }, 'image/jpeg', quality)
    }
    img.onerror = () => resolve({ blob: file, name: file.name })
    img.src = url
  })
}

const removeFile = (index) => {
  // Clean up object URL to prevent memory leaks
  if (uploadedFiles.value[index] && uploadedFiles.value[index].url) {
    URL.revokeObjectURL(uploadedFiles.value[index].url)
  }
  uploadedFiles.value.splice(index, 1)
}

const toggleDayHours = (dayId) => {
  // Working hours are handled by v-model
}

const updateLocationData = (newLocationData) => {
  locationData.value = newLocationData
  formData.value.latitude = newLocationData.lat
  formData.value.longitude = newLocationData.lng
  
  // Auto-fill address if available
  if (newLocationData.address) {
    formData.value.address = newLocationData.address
  }
}

// Set location from LocationPicker
const setLocation = (coords) => {
  console.log('Location selected:', coords)
  locationData.value.lat = coords.lat
  locationData.value.lng = coords.lng
  formData.value.latitude = coords.lat
  formData.value.longitude = coords.lng
  if (coords.address) {
    formData.value.address = coords.address
  }
}

// Load business data for editing
const loadBusinessData = async () => {
  if (!isEditMode.value || !businessId.value) return
  
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    console.log('Loading business data for ID:', businessId.value)
    console.log('Using token:', token ? 'Present' : 'Missing')
    
    // Try different API endpoints
    let response
    try {
      // First try the regular endpoint
      response = await axios.get(`${API_BASE_URL}/businesses/${businessId.value}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
    } catch (firstError) {
      console.log('First endpoint failed, trying admin endpoint...')
      try {
        // Try admin endpoint
        response = await axios.get(`${API_BASE_URL}/admin/businesses/${businessId.value}`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
      } catch (secondError) {
        console.log('Admin endpoint also failed, trying my-businesses...')
        // Try my-businesses endpoint
        response = await axios.get(`${API_BASE_URL}/my-businesses`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        // Find the specific business
        const businesses = response.data.businesses?.data || response.data.businesses || []
        const business = businesses.find(b => b.id == businessId.value)
        if (business) {
          response.data = { business }
        } else {
          throw new Error('Business not found in my-businesses')
        }
      }
    }
    
    console.log('Business data response:', response.data)
    
    const business = response.data.business
    
    // Fill form data
    formData.value = {
      name: business.name || '',
      address: business.address || '',
      description: business.description || '',
      fullDescription: business.full_description || business.description || '',
      phone: business.phone || '',
      landlinePhone: business.landline_phone || '',
      email: business.email || '',
      website: business.website || '',
      instagram: business.instagram || '',
      telegram: business.telegram || '',
      whatsapp: business.whatsapp || '',
      latitude: business.latitude || '',
      longitude: business.longitude || '',
      images: business.images || [],
      features: business.features || [],
      working_hours: business.working_hours || {}
    }
    
    // Set category - check if it's a parent or child category
    if (business.category_id) {
      const category = categories.value.find(cat => cat.id === business.category_id)
      if (category) {
        if (category.parent_id) {
          // It's a child category
          selectedParentCategoryId.value = category.parent_id
          selectedCategoryId.value = business.category_id
        } else {
          // It's a parent category
          selectedParentCategoryId.value = business.category_id
        }
      }
    }
    
    // Set location data
    if (business.latitude && business.longitude) {
      locationData.value = {
        lat: business.latitude,
        lng: business.longitude,
        address: business.address || ''
      }
    }
    
    // Set working hours
    if (business.working_hours) {
      workingHours.value = business.working_hours
    }
    
    // Set features/keywords
    if (business.features) {
      selectedKeywords.value = business.features
    }
    
    // Load existing images
    if (business.images && business.images.length > 0) {
      uploadedFiles.value = business.images.map((imageUrl, index) => ({
        file: null,
        name: `image_${index + 1}.jpg`,
        type: 'image/jpeg',
        size: 0,
        url: imageUrl
      }))
    }
    
  } catch (error) {
    console.error('Error loading business data:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)
    
    let errorMessage = 'خطا در بارگذاری اطلاعات کسب‌وکار'
    
    if (error.response?.status === 404) {
      errorMessage = 'کسب‌وکار مورد نظر یافت نشد'
    } else if (error.response?.status === 403) {
      errorMessage = 'شما دسترسی لازم برای ویرایش این کسب‌وکار را ندارید'
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.message) {
      errorMessage = error.message
    }
    
    alert(errorMessage)
    router.push('/business')
  } finally {
    loading.value = false
  }
}

const submitForm = async () => {
  console.log('Submit form called, current step:', currentStep.value)
  console.log('Form data:', formData.value)
  console.log('Selected category ID:', selectedParentCategoryId.value)
  console.log('Selected keywords:', selectedKeywords.value)
  
  // Skip validation for testing
  console.log('Skipping validation for testing')
  loading.value = true
    
    try {
      // Check if user is authenticated
      console.log('Auth store state:', {
        isAuthenticated: authStore.isAuthenticated,
        user: authStore.user,
        token: localStorage.getItem('token')
      })
      
      if (!authStore.isAuthenticated) {
        alert('لطفاً ابتدا وارد سیستم شوید')
        router.push('/login')
        return
      }
      
  // Normalize social inputs
  const normalizeHandle = (val, providers) => {
    if (!val) return ''
    let v = String(val).trim()
    v = v.replace(/^@+/, '')
    providers.forEach(p => {
      v = v.replace(new RegExp(`^https?:\\/\\/(www\\.)?${p}\\.com\\/`, 'i'), '')
      v = v.replace(new RegExp(`^${p}\\.me\\/`, 'i'), '')
      v = v.replace(new RegExp(`^${p}\\.me:`, 'i'), '')
    })
    return v
  }
  const normalizedInstagram = normalizeHandle(formData.value.instagram, ['instagram'])
  const normalizedTelegram = normalizeHandle(formData.value.telegram, ['t', 'telegram'])
  const normalizedWhatsapp = (formData.value.whatsapp || '').toString().replace(/[^0-9]/g, '').slice(-10)

  // Collect all form data
  const formDataToSubmit = {
        name: formData.value.name,
        description: formData.value.fullDescription || formData.value.description,
        address: locationData.value.address || formData.value.address,
        phone: formData.value.phone,
    landline: formData.value.landlinePhone,
        email: formData.value.email,
        website: formData.value.website,
    instagram: normalizedInstagram,
    telegram: normalizedTelegram,
    whatsapp: normalizedWhatsapp,
        latitude: locationData.value.lat ? parseFloat(locationData.value.lat) : (formData.value.latitude ? parseFloat(formData.value.latitude) : null),
        longitude: locationData.value.lng ? parseFloat(locationData.value.lng) : (formData.value.longitude ? parseFloat(formData.value.longitude) : null),
        category_id: selectedCategoryId.value || selectedParentCategoryId.value,
        features: selectedKeywords.value,
        working_hours: workingHours.value
      }
      
      console.log('Submitting form data:', formDataToSubmit)
      
      // Test API call directly
      try {
        const token = localStorage.getItem('token')
        console.log('Using token:', token)
        
        let response
        if (isEditMode.value) {
          // Update existing business
          response = await axios.put(`https://api.mrkoja.com/api/businesses/${businessId.value}`, formDataToSubmit, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          })
        } else {
          // Create new business
          response = await axios.post('https://api.mrkoja.com/api/businesses', formDataToSubmit, {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          })
        }
        
        console.log('Direct API response:', response.data)
        
        // Upload images if any
        if (uploadedFiles.value.length > 0 && response.data.business) {
          const businessId = response.data.business.id
          console.log('Uploading images for business ID:', businessId)
          
          const formData = new FormData()
          uploadedFiles.value.forEach((fileObj, index) => {
            if (fileObj && fileObj.file) {
              formData.append(`images[${index}]`, fileObj.file)
            }
          })
          
          try {
            const imageResponse = await axios.post(`https://api.mrkoja.com/api/businesses/${businessId}/images`, formData, {
              headers: {
                'Authorization': `Bearer ${token}`
              }
            })
            console.log('Images uploaded successfully:', imageResponse.data)
          } catch (imageError) {
            console.error('Error uploading images:', imageError)
            alert('کسب‌وکار ثبت شد اما خطا در آپلود تصاویر: ' + (imageError.response?.data?.message || imageError.message))
          }
        }
        
        alert(isEditMode.value ? 'کسب‌وکار شما با موفقیت به‌روزرسانی شد!' : 'کسب‌وکار شما با موفقیت ثبت شد!')
        router.push('/business')
        return
      } catch (apiError) {
        console.error('Direct API error:', apiError)
        console.error('API error response:', apiError.response?.data)
        alert('خطا در API: ' + (apiError.response?.data?.message || apiError.message))
        return
      }
      
      const result = await businessStore.createBusiness(formDataToSubmit)
      
      console.log('Business creation result:', result)
      
      if (result.success) {
        alert('کسب‌وکار شما با موفقیت ثبت شد!')
        router.push('/business')
      } else {
        alert('خطا در ثبت کسب‌وکار: ' + result.message)
        console.error('Business creation error:', result)
        if (result.errors) {
          console.error('Validation errors:', result.errors)
        }
      }
    } catch (error) {
      console.error('Error submitting form:', error)
      alert('خطا در ثبت کسب‌وکار: ' + error.message)
    } finally {
      loading.value = false
    }
  }

  // مقداردهی اولیه
  onMounted(async () => {
    // دریافت دسته‌بندی‌ها از API
    await categoryStore.fetchCategories()
    
    // Initialize auth if needed
    if (!authStore.isAuthenticated) {
      authStore.initializeAuth()
    }
    
    // Load business data if in edit mode
    if (isEditMode.value) {
      await loadBusinessData()
    }
  })
</script>

<style scoped>
.gradient-bg {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.progress-bar {
  transition: width 0.3s ease;
}

.step-content {
  min-height: 400px;
}

.map-container {
  height: 300px;
  border-radius: 12px;
  overflow: hidden;
}

.keyword-tag {
  transition: all 0.2s ease;
}

.keyword-tag:hover {
  transform: translateY(-1px);
}

.working-hours-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 1rem;
  align-items: center;
}

@media (max-width: 768px) {
  .working-hours-grid {
    grid-template-columns: 1fr;
  }
}
</style>