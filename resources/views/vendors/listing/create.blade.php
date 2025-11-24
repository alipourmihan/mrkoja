@extends(isset($is_admin) && $is_admin ? 'admin.layout' : 'vendors.layout')

@section('style')
  {{-- Leaflet CSS will be loaded lazily when map is needed --}}
  <style>
    .step-panel {
      display: none;
    }

    .step-panel.active {
      display: block;
    }

    .progress {
      height: 6px;
    }

    .progress-bar {
      transition: width .3s ease;
    }

    .category-pill,
    .child-pill {
      border: 1px solid #e0e6ed;
      border-radius: 999px;
      padding: .45rem 1.25rem;
      margin: .35rem .35rem 0 0;
      cursor: pointer;
      transition: all .2s ease;
      background: #fff;
      position: relative;
      z-index: 1;
      user-select: none;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
    }

    .category-pill.active,
    .child-pill.active {
      background: #1363df;
      color: #fff;
      border-color: #1363df;
      box-shadow: 0 8px 20px rgba(19, 99, 223, .25);
    }

    #map {
      height: 320px;
      border-radius: .75rem;
    }

    .working-table thead th {
      background: #f5f7fb;
      font-weight: 600;
    }

    .working-table input[type="time"] {
      max-width: 130px;
    }

    #galleryPreview img {
      width: 120px;
      height: 90px;
      object-fit: cover;
      border-radius: .5rem;
      margin: .4rem;
      border: 1px solid #e0e6ed;
    }
    
    /* Force RTL for form */
    #listingForm {
      direction: rtl !important;
      text-align: right !important;
    }
    
    #listingForm .form-group {
      direction: rtl !important;
      text-align: right !important;
    }
    
    #listingForm label {
      text-align: right !important;
      direction: rtl !important;
    }
    
    #listingForm .row {
      direction: rtl !important;
    }
    
    #listingForm .col-lg-6,
    #listingForm .col-lg-12,
    #listingForm .col-md-6,
    #listingForm .col-md-12 {
      direction: rtl !important;
      text-align: right !important;
    }
    
    #listingForm .btn {
      direction: rtl !important;
    }
    
    #listingForm .d-flex {
      direction: rtl !important;
    }
    
    #listingForm .justify-content-between {
      direction: rtl !important;
    }
    
    #listingForm .justify-content-end {
      direction: rtl !important;
    }
    
    /* Category pills RTL */
    .category-pill,
    .child-pill {
      margin: .35rem 0 0 .35rem !important;
    }
    
    /* اطمینان از کلیک‌پذیری دکمه‌های دسته‌بندی */
    .parent-category-btn,
    .child-pill {
      pointer-events: auto !important;
      position: relative !important;
      z-index: 10 !important;
      display: inline-block !important;
      border: none !important;
      outline: none !important;
    }
    
    .parent-category-btn:hover,
    .child-pill:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(19, 99, 223, .3);
    }
    
    #parentCategories,
    #childCategories {
      position: relative;
      z-index: 1;
      pointer-events: auto !important;
    }
    
    /* جلوگیری از تداخل با سایر عناصر */
    .step-panel {
      position: relative;
      z-index: 1;
    }
    
    /* Working hours table RTL */
    .working-table {
      direction: rtl !important;
    }
    
    .working-table thead th {
      text-align: right !important;
    }
    
    .working-table tbody td {
      text-align: right !important;
    }
    
    /* Gallery preview RTL */
    #galleryPreview {
      direction: rtl !important;
    }
    
    /* Step navigation buttons RTL */
    .step-navigation {
      direction: rtl !important;
    }
    
    /* Breadcrumbs RTL */
    .breadcrumbs {
      direction: rtl !important;
    }
    
    .breadcrumbs li {
      direction: rtl !important;
    }
  </style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Listing') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('vendor.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-left-arrow"></i></li>
      <li class="nav-item"><span>{{ __('Listings Management') }}</span></li>
      <li class="separator"><i class="flaticon-left-arrow"></i></li>
      <li class="nav-item"><span>{{ __('Add Listing') }}</span></li>
    </ul>
  </div>

  @if ($pending_package)
    <div class="alert alert-warning">
      {{ __('You have requested a package which needs an action by Admin. Please wait for the decision email.') }}
    </div>
  @endif

  @if ($can_listing_add === 0)
    <div class="alert alert-warning">
      {{ __('You need to purchase a package before adding a new business.') }}
    </div>
  @elseif ($can_listing_add === 2)
    <div class="alert alert-warning">
      {{ __('You have reached the limit of your current package. Please upgrade to add more businesses.') }}
    </div>
  @endif

  @if ($can_listing_add === 1)
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-1">{{ isset($is_edit) && $is_edit ? __('ویرایش کسب‌وکار') : __('فرم ثبت کسب‌وکار') }}</h5>
            <p class="text-muted mb-0">{{ isset($is_edit) && $is_edit ? __('اطلاعات را مرحله‌به‌مرحله ویرایش کنید. تمام فیلدهای ستاره‌دار الزامی است.') : __('اطلاعات را مرحله‌به‌مرحله تکمیل کنید. تمام فیلدهای ستاره‌دار الزامی است.') }}</p>
          </div>
        </div>

        <div class="progress mb-4">
          <div id="progressBar" class="progress-bar bg-primary" role="progressbar" style="width: 20%"></div>
        </div>
        <div class="text-muted mb-4" id="stepLabel">{{ __('مرحله 1 از 5') }}</div>

        <div id="listingErrors" class="alert alert-danger d-none">
          <ul class="mb-0"></ul>
        </div>
        @if ($errors->any())
          <script>
            window.serverListingErrors = @json($errors->all());
          </script>
        @endif

        <form id="listingForm" method="POST" action="{{ isset($is_edit) && $is_edit ? (isset($is_admin) && $is_admin ? route('admin.listing_management.update_listing', $listing->id) : route('vendor.listing_management.update_listing', $listing->id)) : (isset($is_admin) && $is_admin ? route('admin.listing_management.store_listing') : route('vendor.listing_management.store_listing')) }}"
          enctype="multipart/form-data">
          @csrf
          @if(isset($is_edit) && $is_edit)
            @method('PUT')
            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
          @endif
          <input type="hidden" name="can_listing_add" value="1">
          <input type="hidden" name="user_id" value="{{ $vendor ? $vendor->id : (isset($vendor_id) ? $vendor_id : 0) }}">
          <input type="hidden" name="category_parent_id" id="category_parent_id" value="{{ isset($selected_parent_category_id) ? $selected_parent_category_id : '' }}">
          <input type="hidden" name="category_id" id="category_id" value="{{ isset($selected_category_id) ? $selected_category_id : '' }}">
          <input type="hidden" name="category_ids" id="category_ids" value="{{ isset($selected_category_ids) ? json_encode($selected_category_ids) : '[]' }}">
          <input type="hidden" name="latitude" id="latitude" value="{{ isset($listing) && $listing ? $listing->latitude : '' }}">
          <input type="hidden" name="longitude" id="longitude" value="{{ isset($listing) && $listing ? $listing->longitude : '' }}">
          <input type="hidden" name="working_hours" id="working_hours" value="{{ isset($working_hours_data) ? json_encode($working_hours_data) : '' }}">

          {{-- Step 1 --}}
          <div class="step-panel active" data-step="0">
            <h5 class="mb-3">{{ __('اطلاعات اصلی کسب‌وکار') }}</h5>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('نام کسب‌وکار') }} *</label>
                  <input type="text" name="business_name" class="form-control" maxlength="30"
                    placeholder="{{ __('مثال: کلینیک دندانپزشکی آرمان') }}"
                    value="{{ isset($listing_content) && $listing_content ? $listing_content->title : '' }}">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('توضیح کوتاه (حداکثر ۱۴0 کاراکتر)') }} *</label>
                  <textarea name="short_description" class="form-control" rows="2">{{ isset($listing_content) && $listing_content ? $listing_content->short_description : '' }}</textarea>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>{{ __('دسته‌بندی اصلی') }} *</label>
              <div id="parentCategories" class="d-flex flex-wrap">
                @foreach ($parent_categories as $parent)
                  <button type="button" class="category-pill parent-category-btn" data-id="{{ $parent->id }}">
                    {{ $parent->name }}
                  </button>
                @endforeach
              </div>
            </div>
            <div class="form-group">
              <label>{{ __('زیر دسته‌ها') }} *</label>
              <small class="text-muted d-block mb-2">{{ __('می‌توانید چند زیر دسته را انتخاب کنید') }}</small>
              <div id="childCategories" class="d-flex flex-wrap">
                <span class="text-muted">{{ __('ابتدا دسته‌بندی اصلی را انتخاب کنید.') }}</span>
              </div>
            </div>
          </div>

          {{-- Step 2 --}}
          <div class="step-panel" data-step="1">
            <h5 class="mb-3">{{ __('موقعیت و آدرس') }}</h5>
            
            {{-- انتخاب استان و شهر (بالا) --}}
            <div class="row mb-4">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('استان') }} *</label>
                  <select class="form-control" name="state_id" id="stateSelect">
                    <option value="">{{ __('انتخاب استان') }}</option>
                    @foreach ($states as $state)
                      <option value="{{ $state->id }}" {{ isset($listing_content) && $listing_content && $listing_content->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('شهر') }} *</label>
                  <select class="form-control" name="city_id" id="citySelect">
                    <option value="">{{ __('ابتدا استان را انتخاب کنید') }}</option>
                  </select>
                </div>
              </div>
            </div>

            {{-- جستجوی آدرس --}}
            <div class="row mb-3">
              <div class="col-lg-12">
                <div class="form-group">
                  <label>{{ __('جستجوی آدرس (اختیاری)') }}</label>
                  <div class="input-group">
                    <input type="text" id="addressSearch" class="form-control"
                      placeholder="{{ __('مثال: تهران، خیابان ولیعصر') }}">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary" id="searchAddressBtn">
                        <i class="fas fa-search"></i> {{ __('جستجو') }}
                      </button>
                    </div>
                  </div>
                  <small class="text-muted">{{ __('آدرس را وارد کنید تا نقشه به آن موقعیت برود.') }}</small>
                </div>
              </div>
            </div>

            {{-- نقشه (پایین) --}}
            <div class="mb-3">
              <label>{{ __('انتخاب موقعیت روی نقشه') }} *</label>
              <div id="map" style="position: relative; background: #f5f5f5; border-radius: 0.75rem; min-height: 320px; display: flex; align-items: center; justify-content: center;">
                <div id="mapLoading" style="text-align: center; color: #666;">
                  <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                  <p>در حال بارگذاری نقشه...</p>
                </div>
              </div>
              <small class="text-muted">{{ __('برای تعیین موقعیت دقیق، روی نقشه کلیک کنید یا از جستجوی آدرس استفاده کنید.') }}</small>
            </div>

            {{-- فیلدهای آدرس --}}
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('آدرس دقیق (خیابان، پلاک و ...)') }} *</label>
                  <input type="text" name="address_details" id="address_details" class="form-control"
                    placeholder="{{ __('مثال: تهران، خیابان ولیعصر، کوچه ۱۲، پلاک ۷') }}"
                    value="{{ isset($listing_content) && $listing_content ? $listing_content->address : '' }}">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('آدرس پستی / توضیح تکمیلی (اختیاری)') }}</label>
                  <input type="text" name="postal_address" class="form-control"
                    placeholder="{{ __('مثال: برج A، طبقه سوم، واحد 9') }}">
                </div>
              </div>
            </div>
          </div>

          {{-- Step 3 --}}
          <div class="step-panel" data-step="2">
            <h5 class="mb-3">{{ __('راه‌های ارتباطی و شبکه‌های اجتماعی') }}</h5>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('شماره موبایل') }} *</label>
                  <input type="text" name="mobile_phone" class="form-control" placeholder="09123456789" value="{{ isset($listing) && $listing ? $listing->mobile_phone : '' }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('تلفن ثابت') }}</label>
                  <input type="text" name="landline_phone" class="form-control" placeholder="02187654321" value="{{ isset($listing) && $listing ? $listing->landline_phone : '' }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('وب‌سایت') }}</label>
                  <input type="text" name="website" class="form-control" placeholder="https://example.com" value="{{ isset($listing) && $listing ? $listing->website : '' }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('اینستاگرام') }}</label>
                  <input type="text" name="instagram" class="form-control" placeholder="@username" value="{{ isset($listing) && $listing ? $listing->instagram : '' }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('تلگرام') }}</label>
                  <input type="text" name="telegram" class="form-control" placeholder="t.me/username" value="{{ isset($listing) && $listing ? $listing->telegram : '' }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>{{ __('واتس‌اپ') }}</label>
                  <input type="text" name="whatsapp" class="form-control" placeholder="09123456789" value="{{ isset($listing) && $listing ? $listing->whatsapp : '' }}">
                </div>
              </div>
            </div>
          </div>

          {{-- Step 4 --}}
          <div class="step-panel" data-step="3">
            <h5 class="mb-3">{{ __('تصاویر و توضیحات کامل') }}</h5>
            <div class="form-group">
              <label>{{ __('توضیحات کامل کسب‌وکار') }} *</label>
              <textarea name="full_description" class="form-control" rows="6">{{ isset($listing_content) && $listing_content ? $listing_content->description : '' }}</textarea>
              <small class="text-muted">{{ __('حداقل ۳۰ و حداکثر ۱۰۰۰ کاراکتر.') }}</small>
            </div>
            <div class="form-group">
              <label>{{ __('کلمات کلیدی (با کاما جدا کنید – اختیاری)') }}</label>
              <input type="text" name="keywords" class="form-control" placeholder="مثال: رستوران، غذای ایرانی" value="{{ isset($listing_content) && $listing_content ? $listing_content->meta_keyword : '' }}">
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('تصویر شاخص') }} *</label>
                  <input type="file" name="feature_image" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('گالری تصاویر (حداقل ۱ تصویر)') }} *</label>
                  <input type="file" name="gallery[]" class="form-control" multiple id="galleryInput">
                  <div id="galleryPreview" class="d-flex flex-wrap mt-2"></div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('لینک ویدئو (اختیاری)') }}</label>
                  <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/..." value="{{ isset($listing) && $listing ? $listing->video_url : '' }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>{{ __('تصویر پس‌زمینه ویدئو (اختیاری)') }}</label>
                  <input type="file" name="video_background_image" class="form-control">
                </div>
              </div>
            </div>
          </div>

          {{-- Step 5 --}}
          <div class="step-panel" data-step="4">
            <h5 class="mb-3">{{ __('ساعات کاری') }}</h5>
            <div class="table-responsive">
              <table class="table table-sm working-table">
                <thead>
                  <tr>
                    <th>{{ __('روز') }}</th>
                    <th>{{ __('وضعیت') }}</th>
                    <th>{{ __('شروع') }}</th>
                    <th>{{ __('پایان') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $days = [
                        ['key' => 'Saturday', 'label' => 'شنبه'],
                        ['key' => 'Sunday', 'label' => 'یکشنبه'],
                        ['key' => 'Monday', 'label' => 'دوشنبه'],
                        ['key' => 'Tuesday', 'label' => 'سه‌شنبه'],
                        ['key' => 'Wednesday', 'label' => 'چهارشنبه'],
                        ['key' => 'Thursday', 'label' => 'پنجشنبه'],
                        ['key' => 'Friday', 'label' => 'جمعه'],
                    ];
                  @endphp
                  @foreach ($days as $day)
                    @php
                      $dayData = null;
                      if (isset($working_hours_data) && count($working_hours_data) > 0) {
                        $dayData = collect($working_hours_data)->firstWhere('day', $day['key']);
                      }
                      $status = $dayData ? $dayData['status'] : 'closed';
                      $startTime = $dayData && $dayData['start_time'] ? $dayData['start_time'] : '09:00';
                      $endTime = $dayData && $dayData['end_time'] ? $dayData['end_time'] : '18:00';
                    @endphp
                    <tr class="working-row" data-day="{{ $day['key'] }}">
                      <td>{{ $day['label'] }}</td>
                      <td>
                        <select class="form-control form-control-sm working-status">
                          <option value="open" {{ $status === 'open' ? 'selected' : '' }}>{{ __('باز') }}</option>
                          <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>{{ __('تعطیل') }}</option>
                        </select>
                      </td>
                      <td>
                        <input type="time" class="form-control form-control-sm working-start" value="{{ $startTime }}">
                      </td>
                      <td>
                        <input type="time" class="form-control form-control-sm working-end" value="{{ $endTime }}">
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="alert alert-info">
              {{ __('پس از کلیک روی دکمه ثبت، اطلاعات ساعات کاری و سایر فیلدها به‌صورت خودکار ذخیره می‌شوند.') }}
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="button" class="btn btn-outline-secondary d-none" data-prev>{{ __('مرحله قبل') }}</button>
            <div>
              <button type="button" class="btn btn-outline-secondary mr-2 d-none" data-prev-mobile>{{ __('مرحله قبل') }}</button>
              <button type="button" class="btn btn-primary" data-next>{{ __('مرحله بعد') }}</button>
              <button type="submit" class="btn btn-success d-none" id="listingSubmitBtn">{{ __('ثبت نهایی') }}</button>
            </div>
          </div>
        </form>
      </div>
      </div>
    </div>
  @endif
@endsection

@section('script')
  {{-- Leaflet JS will be loaded lazily when map is needed --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // اگر در حالت edit هستیم، داده‌های موجود را پر می‌کنیم
      @if(isset($is_edit) && $is_edit && isset($listing_content))
        // انتخاب دسته‌بندی
        @if(isset($selected_parent_category_id) && $selected_parent_category_id)
          const parentCategoryBtn = document.querySelector(`.parent-category-btn[data-id="{{ $selected_parent_category_id }}"]`);
          if (parentCategoryBtn) {
            parentCategoryBtn.click();
            // بعد از لود شدن زیر دسته‌ها، دسته انتخاب شده را انتخاب می‌کنیم
            setTimeout(() => {
              @if(isset($selected_category_ids) && is_array($selected_category_ids) && count($selected_category_ids) > 0)
                selectedCategoryIds = @json($selected_category_ids);
                if (categoryIdsInput) {
                  categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
                }
                selectedCategoryIds.forEach(catId => {
                  const childCategoryBtn = document.querySelector(`.child-pill[data-id="${catId}"]`);
                  if (childCategoryBtn) {
                    childCategoryBtn.click();
                  }
                });
              @elseif(isset($selected_category_id) && $selected_category_id)
                selectedCategoryIds = [{{ $selected_category_id }}];
                if (categoryIdsInput) {
                  categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
                }
                const childCategoryBtn = document.querySelector(`.child-pill[data-id="{{ $selected_category_id }}"]`);
                if (childCategoryBtn) {
                  childCategoryBtn.click();
                }
              @endif
            }, 500);
          }
        @endif

        // انتخاب شهر
        @if(isset($listing_content) && $listing_content->state_id)
          const stateSelect = document.getElementById('stateSelect');
          if (stateSelect) {
            stateSelect.value = {{ $listing_content->state_id }};
            stateSelect.dispatchEvent(new Event('change'));
            
            // بعد از لود شدن شهرها، شهر انتخاب شده را انتخاب می‌کنیم
            setTimeout(() => {
              @if(isset($listing_content) && $listing_content->city_id)
                const citySelect = document.getElementById('citySelect');
                if (citySelect) {
                  citySelect.value = {{ $listing_content->city_id }};
                  citySelect.dispatchEvent(new Event('change'));
                }
              @endif
            }, 500);
          }
        @endif

        // تنظیم نقشه و marker
        @if(isset($listing) && $listing->latitude && $listing->longitude)
          window.editModeMapData = {
            lat: {{ $listing->latitude }},
            lng: {{ $listing->longitude }},
            address: "{{ isset($listing_content) && $listing_content->address ? addslashes($listing_content->address) : '' }}"
          };
        @endif

        // پر کردن ساعات کاری
        @if(isset($working_hours_data) && count($working_hours_data) > 0)
          window.editModeWorkingHours = @json($working_hours_data);
        @endif
      @endif
      const form = document.getElementById('listingForm');
      if (!form) {
        return;
      }

      const steps = Array.from(document.querySelectorAll('.step-panel'));
      const nextBtn = document.querySelector('[data-next]');
      const prevBtn = document.querySelector('[data-prev]');
      const prevBtnMobile = document.querySelector('[data-prev-mobile]');
      const submitBtn = document.getElementById('listingSubmitBtn');
      const progressBar = document.getElementById('progressBar');
      const stepLabel = document.getElementById('stepLabel');
      const errorBox = document.getElementById('listingErrors');
      const errorList = errorBox.querySelector('ul');
      const serverErrors = Array.isArray(window.serverListingErrors) ? window.serverListingErrors : [];
      const categoryTree = @json($category_tree);
      const childContainer = document.getElementById('childCategories');
      const parentButtons = document.querySelectorAll('.parent-category-btn');
      const parentInput = document.getElementById('category_parent_id');
      const childInput = document.getElementById('category_id');
      const categoryIdsInput = document.getElementById('category_ids');
      let selectedCategoryIds = [];
      const stateSelect = document.getElementById('stateSelect');
      const citySelect = document.getElementById('citySelect');
      const latitudeInput = document.getElementById('latitude');
      const longitudeInput = document.getElementById('longitude');
      const workingInput = document.getElementById('working_hours');
      const workingRows = document.querySelectorAll('.working-row');
      const galleryInput = document.getElementById('galleryInput');
      const galleryPreview = document.getElementById('galleryPreview');
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const getCityUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.get-city') : route('vendor.listing_management.get-city') }}";
      
      // ساخت Map از دسته‌بندی‌ها
      const parentMap = new Map();
      if (categoryTree && Array.isArray(categoryTree)) {
        categoryTree.forEach(cat => {
          parentMap.set(cat.id, cat.children || []);
        });
      }

      // Debug: بررسی وجود دکمه‌ها
      console.log('تعداد دکمه‌های دسته‌بندی اصلی:', parentButtons.length);
      console.log('تعداد دسته‌بندی‌ها در categoryTree:', categoryTree ? categoryTree.length : 0);
      console.log('parentMap size:', parentMap.size);
      
      if (parentButtons.length === 0) {
        console.error('هیچ دکمه دسته‌بندی اصلی یافت نشد!');
      }
      
      if (!childContainer) {
        console.error('عنصر childCategories یافت نشد!');
      }
      
      if (!parentInput) {
        console.error('عنصر category_parent_id یافت نشد!');
      }
      
      if (!childInput) {
        console.error('عنصر category_id یافت نشد!');
      }

      let currentStep = 0;

      let mapInitialized = false;
      let mapInstance = null;

      function showStep(index) {
        steps.forEach((step, idx) => step.classList.toggle('active', idx === index));
        currentStep = index;
        updateNavigation();
        
        // Lazy load map when step 1 (location step) is shown
        if (index === 1 && !mapInitialized) {
          initializeMap();
        }
      }

      function initializeMap() {
        if (mapInitialized) return;
        
        const mapContainer = document.getElementById('map');
        const mapLoading = document.getElementById('mapLoading');
        
        // Load Leaflet CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
        link.crossOrigin = '';
        document.head.appendChild(link);

        // Load Leaflet JS
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
        script.crossOrigin = '';
        script.onload = function() {
          // Hide loading indicator
          if (mapLoading) {
            mapLoading.style.display = 'none';
          }
          
          // Initialize map
          setupMap();
          mapInitialized = true;

          // اگر در حالت edit هستیم و داده‌های نقشه موجود است، نقشه را تنظیم می‌کنیم
          if (window.editModeMapData) {
            setTimeout(() => {
              if (mapInstance) {
                mapInstance.setView([window.editModeMapData.lat, window.editModeMapData.lng], 15);
                const marker = L.marker([window.editModeMapData.lat, window.editModeMapData.lng]).addTo(mapInstance);
                marker.bindPopup(window.editModeMapData.address || 'موقعیت کسب‌وکار').openPopup();
                latitudeInput.value = window.editModeMapData.lat.toFixed(6);
                longitudeInput.value = window.editModeMapData.lng.toFixed(6);
              }
            }, 1000);
          }
        };
        script.onerror = function() {
          if (mapLoading) {
            mapLoading.innerHTML = '<p style="color: red;">خطا در بارگذاری نقشه. لطفاً صفحه را رفرش کنید.</p>';
          }
        };
        document.head.appendChild(script);
      }

      function setupMap() {
        // محدود کردن نقشه به ایران
        const iranBounds = L.latLngBounds(
          [25.0, 44.0], // جنوب غربی
          [39.8, 63.3]  // شمال شرقی
        );

        // استفاده از tile layer مناسب‌تر برای ایران با بهینه‌سازی
        mapInstance = L.map('map', {
          maxBounds: iranBounds,
          maxBoundsViscosity: 1.0, // جلوگیری از خروج از مرزها
          zoomControl: true,
          scrollWheelZoom: true,
          preferCanvas: true, // استفاده از Canvas برای performance بهتر
          fadeAnimation: false, // غیرفعال کردن انیمیشن fade برای سرعت بیشتر
          zoomAnimation: false, // غیرفعال کردن انیمیشن zoom برای سرعت بیشتر
          markerZoomAnimation: false
        }).setView([32.5, 53.5], 6); // مرکز ایران

        // استفاده از OpenStreetMap با تنظیمات بهینه
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 18, // کاهش maxZoom برای سرعت بیشتر
          minZoom: 5,
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
          tileSize: 256,
          zoomOffset: 0,
          updateWhenZooming: false, // بهینه‌سازی performance
          updateWhenIdle: true,
          keepBuffer: 2 // کاهش buffer برای سرعت بیشتر
        }).addTo(mapInstance);

        // محدود کردن view به ایران
        mapInstance.setMaxBounds(iranBounds);

        let marker = null;

        // جابجایی نقشه با انتخاب شهر
        citySelect.addEventListener('change', function () {
          if (!mapInstance) return;
          
          const selectedOption = this.options[this.selectedIndex];
          const cityName = selectedOption.dataset.cityName;
          
          if (cityName && cityCoordinates[cityName]) {
            const coords = cityCoordinates[cityName];
            mapInstance.setView(coords, 13);
            
            // اضافه کردن marker در مرکز شهر
            if (marker) {
              mapInstance.removeLayer(marker);
            }
            marker = L.marker(coords).addTo(mapInstance);
            marker.bindPopup(cityName).openPopup();
            
            // به‌روزرسانی مختصات
            latitudeInput.value = coords[0].toFixed(6);
            longitudeInput.value = coords[1].toFixed(6);
          }
        });

        // کلیک روی نقشه
        mapInstance.on('click', function (e) {
          const {
            lat,
            lng
          } = e.latlng;
          latitudeInput.value = lat.toFixed(6);
          longitudeInput.value = lng.toFixed(6);
          if (marker) {
            mapInstance.removeLayer(marker);
          }
          marker = L.marker([lat, lng]).addTo(mapInstance);
          
          // Reverse Geocoding - دریافت آدرس از مختصات
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=fa`, {
            headers: {
              'User-Agent': 'YourAppName/1.0'
            }
          })
          .then(res => res.json())
          .then(data => {
            if (data && data.display_name) {
              const addressInput = document.getElementById('address_details');
              if (addressInput && !addressInput.value) {
                addressInput.value = data.display_name;
              }
            }
          })
          .catch(() => {
            // در صورت خطا، هیچ کاری نکن
          });
        });

        // جستجوی آدرس
        const addressSearchInput = document.getElementById('addressSearch');
        const searchAddressBtn = document.getElementById('searchAddressBtn');

        function searchAddress() {
          if (!mapInstance) {
            alert('لطفاً صبر کنید تا نقشه بارگذاری شود.');
            return;
          }
          
          const query = addressSearchInput.value.trim();
          if (!query) {
            alert('لطفاً آدرسی برای جستجو وارد کنید.');
            return;
          }

          // اضافه کردن "ایران" به انتهای جستجو برای دقت بیشتر
          const searchQuery = query.includes('ایران') ? query : query + '، ایران';

          searchAddressBtn.disabled = true;
          searchAddressBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال جستجو...';

          fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=1&accept-language=fa`, {
            headers: {
              'User-Agent': 'YourAppName/1.0'
            }
          })
          .then(res => res.json())
          .then(data => {
            searchAddressBtn.disabled = false;
            searchAddressBtn.innerHTML = '<i class="fas fa-search"></i> جستجو';

            if (data && data.length > 0) {
              const result = data[0];
              const lat = parseFloat(result.lat);
              const lon = parseFloat(result.lon);

              // بررسی اینکه نتیجه در ایران است
              if (lat >= 25.0 && lat <= 39.8 && lon >= 44.0 && lon <= 63.3) {
                mapInstance.setView([lat, lon], 15);
                
                if (marker) {
                  mapInstance.removeLayer(marker);
                }
                marker = L.marker([lat, lon]).addTo(mapInstance);
                marker.bindPopup(result.display_name).openPopup();
                
                latitudeInput.value = lat.toFixed(6);
                longitudeInput.value = lon.toFixed(6);
                
                // پر کردن فیلد آدرس
                const addressInput = document.getElementById('address_details');
                if (addressInput && !addressInput.value) {
                  addressInput.value = result.display_name;
                }
              } else {
                alert('نتیجه جستجو خارج از مرزهای ایران است. لطفاً آدرس دیگری جستجو کنید.');
              }
            } else {
              alert('آدرس یافت نشد. لطفاً آدرس دقیق‌تری وارد کنید.');
            }
          })
          .catch(() => {
            searchAddressBtn.disabled = false;
            searchAddressBtn.innerHTML = '<i class="fas fa-search"></i> جستجو';
            alert('خطا در جستجوی آدرس. لطفاً دوباره تلاش کنید.');
          });
        }

        if (searchAddressBtn) {
          searchAddressBtn.addEventListener('click', searchAddress);
        }
        if (addressSearchInput) {
          addressSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              searchAddress();
            }
          });
        }
      }

      function updateNavigation() {
        const percent = ((currentStep + 1) / steps.length) * 100;
        progressBar.style.width = `${percent}%`;
        stepLabel.textContent = `مرحله ${currentStep + 1} از ${steps.length}`;

        const atStart = currentStep === 0;
        const atEnd = currentStep === steps.length - 1;

        prevBtn.classList.toggle('d-none', atStart);
        prevBtnMobile.classList.toggle('d-none', atStart);
        nextBtn.classList.toggle('d-none', atEnd);
        submitBtn.classList.toggle('d-none', !atEnd);
      }

      function clearErrors() {
        errorList.innerHTML = '';
        errorBox.classList.add('d-none');
      }

      function showErrors(messages) {
        errorList.innerHTML = '';
        messages.forEach(msg => {
          const li = document.createElement('li');
          li.textContent = msg;
          errorList.appendChild(li);
        });
        errorBox.classList.remove('d-none');
        window.scrollTo({
          top: errorBox.offsetTop - 80,
          behavior: 'smooth'
        });
      }

      if (serverErrors.length) {
        showErrors(serverErrors);
      }

      function validateStep(stepIndex) {
        const messages = [];
        if (stepIndex === 0) {
          if (!form.business_name.value.trim()) {
            messages.push('نام کسب‌وکار را وارد کنید.');
          }
          if (!form.short_description.value.trim()) {
            messages.push('توضیح کوتاه را وارد کنید.');
          }
          if (selectedCategoryIds.length === 0) {
            messages.push('دسته‌بندی اصلی و حداقل یک زیر دسته را انتخاب کنید.');
          }
        } else if (stepIndex === 1) {
          if (!latitudeInput.value || !longitudeInput.value) {
            messages.push('موقعیت جغرافیایی را روی نقشه مشخص کنید.');
          }
          if (!stateSelect.value) {
            messages.push('استان را انتخاب کنید.');
          }
          if (!citySelect.value) {
            messages.push('شهر را انتخاب کنید.');
          }
          if (!form.address_details.value.trim()) {
            messages.push('آدرس دقیق را وارد کنید.');
          }
        } else if (stepIndex === 2) {
          if (!form.mobile_phone.value.trim()) {
            messages.push('شماره موبایل الزامی است.');
          }
        } else if (stepIndex === 3) {
          if (!form.full_description.value.trim()) {
            messages.push('توضیحات کامل را وارد کنید.');
          }
          if (!form.feature_image.value) {
            messages.push('تصویر شاخص را انتخاب کنید.');
          }
          if (!galleryInput.files.length) {
            messages.push('حداقل یک تصویر برای گالری آپلود کنید.');
          }
        }

        if (messages.length) {
          showErrors(messages);
          return false;
        }
        clearErrors();
        return true;
      }

      nextBtn.addEventListener('click', function () {
        if (validateStep(currentStep) && currentStep < steps.length - 1) {
          showStep(currentStep + 1);
        }
      });

      function goPrev() {
        if (currentStep > 0) {
          showStep(currentStep - 1);
        }
      }

      prevBtn.addEventListener('click', goPrev);
      prevBtnMobile.addEventListener('click', goPrev);

      // اضافه کردن event listener به دکمه‌های دسته‌بندی اصلی
      if (parentButtons && parentButtons.length > 0) {
        parentButtons.forEach((btn, index) => {
          // بررسی وجود data-id
          if (!btn.dataset.id) {
            console.error('دکمه دسته‌بندی data-id ندارد:', btn, index);
            return;
          }
          
          // اضافه کردن event listener
          btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('دکمه دسته‌بندی کلیک شد:', this.dataset.id, this.textContent);
            
            // حذف active از همه دکمه‌ها
            parentButtons.forEach(b => b.classList.remove('active'));
            
            // اضافه کردن active به دکمه کلیک شده
            this.classList.add('active');
            
            // ذخیره ID دسته‌بندی اصلی
            const parentId = parseInt(this.dataset.id, 10);
            if (isNaN(parentId)) {
              console.error('خطا: parentId معتبر نیست:', this.dataset.id);
              return;
            }
            
            if (parentInput) {
              parentInput.value = parentId;
            }
            if (childInput) {
              childInput.value = '';
            }
            // پاک کردن دسته‌های انتخاب شده قبلی
            selectedCategoryIds = [];
            if (categoryIdsInput) {
              categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
            }
            
            console.log('دسته‌بندی اصلی انتخاب شد:', parentId);
            
            // نمایش زیر دسته‌ها
            const children = parentMap.get(parentId) || [];
            console.log('تعداد زیر دسته‌ها:', children.length);
            
            if (childContainer) {
              renderChildren(children);
            }
          });
          
          // تست: اضافه کردن hover effect
          btn.addEventListener('mouseenter', function() {
            console.log('Hover روی دکمه:', this.textContent);
          });
        });
      } else {
        console.error('هیچ دکمه دسته‌بندی اصلی یافت نشد!');
      }

      function renderChildren(children) {
        childContainer.innerHTML = '';
        if (!children.length) {
          childContainer.innerHTML = `<span class="text-muted">برای این دسته، زیر دسته‌ای ثبت نشده است.</span>`;
          return;
        }
        children.forEach(child => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'child-pill';
          btn.textContent = child.name;
          btn.dataset.id = child.id;
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          
          console.log('زیر دسته کلیک شد:', child.id, child.name);
          
          const categoryId = parseInt(child.id, 10);
          const index = selectedCategoryIds.indexOf(categoryId);
          
          if (index > -1) {
            // حذف از انتخاب‌ها
            selectedCategoryIds.splice(index, 1);
            this.classList.remove('active');
          } else {
            // اضافه به انتخاب‌ها
            selectedCategoryIds.push(categoryId);
            this.classList.add('active');
          }
          
          // به‌روزرسانی input hidden
          if (categoryIdsInput) {
            categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
          }
          
          // برای backward compatibility، اولین دسته را در category_id قرار می‌دهیم
          if (selectedCategoryIds.length > 0) {
            childInput.value = selectedCategoryIds[0];
          } else {
            childInput.value = '';
          }
          
          console.log('زیر دسته‌های انتخاب شده:', selectedCategoryIds);
        });
          childContainer.appendChild(btn);
        });
      }

      // مختصات مرکزی شهرهای اصلی ایران
      const cityCoordinates = {
        'تهران': [35.6892, 51.3890],
        'مشهد': [36.2605, 59.6168],
        'اصفهان': [32.6546, 51.6680],
        'شیراز': [29.5918, 52.5837],
        'تبریز': [38.0809, 46.2900],
        'قم': [34.6401, 50.8766],
        'اهواز': [31.3183, 48.6706],
        'کرمانشاه': [34.3142, 47.0650],
        'رشت': [37.2808, 49.5832],
        'ارومیه': [37.5527, 45.0680],
        'زاهدان': [29.4960, 60.8629],
        'کرمان': [30.2839, 57.0834],
        'همدان': [34.7992, 48.5146],
        'یزد': [31.8974, 54.3569],
        'اردبیل': [38.2498, 48.2933],
        'بندرعباس': [27.1865, 56.2808],
        'سنندج': [35.3097, 46.9987],
        'گرگان': [36.8428, 54.4319],
        'ساری': [36.5633, 53.0601],
        'بابول': [36.5512, 52.6784],
        'خرم‌آباد': [33.4878, 48.3558],
        'سمنان': [35.5729, 53.3971],
        'زنجان': [36.6769, 48.4963],
        'قزوین': [36.2794, 50.0049],
        'بجنورد': [37.4750, 57.3294],
        'بیرجند': [32.8649, 59.2262],
        'ایلام': [33.6374, 46.4226],
        'یاسوج': [30.6680, 51.5880],
        'شهرکرد': [32.3256, 50.8644],
        'بوشهر': [28.9234, 50.8203],
        'آبادان': [30.3473, 48.2934],
        'دزفول': [32.3942, 48.4078],
        'اندیمشک': [32.4600, 48.3592],
        'ملایر': [34.3028, 48.8217],
        'کاشان': [33.9850, 51.4100],
        'نجف‌آباد': [32.6333, 51.3667],
        'شاهین‌شهر': [32.8667, 51.5500],
        'خمینی‌شهر': [32.7000, 51.5167],
        'نیشابور': [36.2140, 58.7967],
        'سبزوار': [36.2126, 57.6819],
        'قائم‌شهر': [36.4611, 52.8611],
        'آمل': [36.4694, 52.3508],
        'کرج': [35.8400, 50.9391],
        'اسلام‌شهر': [35.5444, 51.2300],
        'ورامین': [35.3253, 51.6458],
        'شهریار': [35.6589, 51.0597],
        'پاکدشت': [35.4817, 51.6783],
        'قدس': [35.7214, 51.1089],
        'ملارد': [35.6667, 50.9833],
        'فولادشهر': [32.4833, 51.3167],
        'بهبهان': [30.5958, 50.2417],
        'گنبد کاووس': [37.2500, 55.1672],
        'ساوه': [35.0214, 50.3569],
        'قوچان': [37.1061, 58.5094],
        'مراغه': [37.3908, 46.2381],
        'مرند': [38.4250, 45.7750],
        'میاندوآب': [36.9667, 46.1167],
        'مریوان': [35.5269, 46.1761],
        'بانه': [35.9975, 45.8853],
        'سقز': [36.2464, 46.2664],
        'دیواندره': [35.9139, 47.0239],
        'بیجار': [35.8667, 47.6000],
        'قروه': [35.1667, 47.8000],
        'کامیاران': [34.7956, 46.9356],
        'دهگلان': [35.2778, 47.4167],
        'سنقر': [34.7833, 47.6000],
        'هرسین': [34.2719, 47.5819],
        'کنگاور': [34.5042, 47.9653],
        'جوانرود': [34.8069, 46.4889],
        'پاوه': [35.0433, 46.3564],
        'جوانرود': [34.8069, 46.4889],
        'نودشه': [35.1681, 46.7981],
        'روانسر': [34.7125, 46.6531],
        'ثلاث باباجانی': [34.7375, 46.1494],
        'قصر شیرین': [34.5156, 45.5792],
        'سرپل ذهاب': [34.4611, 45.8625],
        'گیلانغرب': [34.1422, 45.9203],
        'اسلام‌آباد غرب': [34.1094, 46.5281],
        'پاوه': [35.0433, 46.3564],
        'نوسود': [35.1681, 46.7981],
        'نودشه': [35.1681, 46.7981],
        'روانسر': [34.7125, 46.6531],
        'ثلاث باباجانی': [34.7375, 46.1494],
        'قصر شیرین': [34.5156, 45.5792],
        'سرپل ذهاب': [34.4611, 45.8625],
        'گیلانغرب': [34.1422, 45.9203],
        'اسلام‌آباد غرب': [34.1094, 46.5281]
      };

      stateSelect.addEventListener('change', function () {
        const stateId = this.value;
        citySelect.innerHTML = `<option value="">در حال بارگذاری...</option>`;
        if (!stateId) {
          citySelect.innerHTML = `<option value="">ابتدا استان را انتخاب کنید</option>`;
          return;
        }
        fetch(getCityUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
              id: stateId
            })
          })
          .then(res => res.json())
          .then(data => {
            citySelect.innerHTML = `<option value="">انتخاب شهر</option>`;
            data.forEach(city => {
              const opt = document.createElement('option');
              opt.value = city.id;
              opt.textContent = city.name;
              opt.dataset.cityName = city.name; // ذخیره نام شهر برای استفاده در نقشه
              citySelect.appendChild(opt);
            });
          })
          .catch(() => {
            citySelect.innerHTML = `<option value="">دریافت شهرها ناموفق بود</option>`;
          });
      });

      function updateWorkingHours() {
        const result = [];
        workingRows.forEach(row => {
          const day = row.dataset.day;
          const status = row.querySelector('.working-status').value;
          const start = row.querySelector('.working-start').value;
          const end = row.querySelector('.working-end').value;
          result.push({
            day,
            status,
            start_time: status === 'open' ? start : null,
            end_time: status === 'open' ? end : null,
          });
        });
        workingInput.value = JSON.stringify(result);
      }

      workingRows.forEach(row => {
        row.querySelectorAll('select, input').forEach(el => el.addEventListener('change', updateWorkingHours));
      });
      updateWorkingHours();

      if (galleryInput) {
        galleryInput.addEventListener('change', function () {
          galleryPreview.innerHTML = '';
          Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
              const img = document.createElement('img');
              img.src = e.target.result;
              galleryPreview.appendChild(img);
            };
            reader.readAsDataURL(file);
          });
        });
      }

      form.addEventListener('submit', function () {
        updateWorkingHours();
      });

      showStep(0);
    });
  </script>
@endsection
