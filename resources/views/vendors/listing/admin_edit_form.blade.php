{{-- فرم جدید برای admin با تمام فیلدهای create --}}
@php
  // تعریف current_package_obj برای استفاده در view
  $current_package_obj = isset($current_package) && $current_package && $current_package != '[]' ? $current_package : null;
  if (!$current_package_obj) {
    // برای admin یا کاربران بدون پکیج، یک object ساختگی می‌سازیم
    $current_package_obj = (object)['number_of_images_per_listing' => 99999999];
  }
@endphp
@push('styles')
<style>
  .category-pill, .child-pill {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    border: 2px solid #ddd;
    border-radius: 25px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative !important;
  }
  .category-pill:hover, .child-pill:hover {
    border-color: #007bff;
    background: #f0f8ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.2);
  }
  .category-pill.active, .child-pill.active {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
  }
  .category-pill.active::after {
    content: ' ✓';
    font-weight: bold;
  }
  .child-pill.active::after {
    content: ' ✓';
    font-weight: bold;
  }
  .working-table {
    font-size: 14px;
  }
  .working-table th {
    background: #f8f9fa;
    font-weight: 600;
  }
  #map {
    height: 400px;
    border-radius: 0.75rem;
    border: 1px solid #ddd;
  }
  .selected-categories {
    background: #e7f3ff;
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
  }
  .selected-categories span {
    display: inline-block;
    background: #007bff;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    margin: 3px;
    font-size: 13px;
  }
  .amenity-checkbox {
    margin: 8px;
  }
  .amenity-checkbox input[type="checkbox"] {
    margin-left: 5px;
  }
  .form-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .form-section h6 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
  }
</style>
@endpush

{{-- گالری تصاویر --}}
<div class="form-section mb-4">
  <h6>{{ __('گالری تصاویر') }}</h6>
  <div class="row">
    <div class="col-12">
      <table class="table table-striped" id="imgtable">
        @foreach ($listing->galleries as $item)
          <tr class="trdb table-row" id="trdb{{ $item->id }}">
            <td>
              <div class="">
                <img class="thumb-preview wf-150"
                  src="{{ asset('assets/img/listing-gallery/' . $item->image) }}" alt="Ad Image">
              </div>
            </td>
            <td>
              <i class="fa fa-times rmvbtndb" data-indb="{{ $item->id }}" style="cursor: pointer; color: red;"></i>
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
  <form action="{{ route('admin.listing.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata"
    class="dropzone create">
    @csrf
    <div class="fallback">
      <input name="file" type="file" multiple />
    </div>
    <input type="hidden" value="{{ $listing->id }}" name="listing_id">
  </form>
  <p class="text-warning mt-2">
    {{ __('حداکثر ' . (isset($current_package_obj) && $current_package_obj ? $current_package_obj->number_of_images_per_listing : 99999999) . ' تصویر می‌توانید آپلود کنید') }}
  </p>
</div>

<form id="adminListingForm" method="POST" action="{{ route('admin.listing_management.update_listing', $listing->id) }}" enctype="multipart/form-data">
  @csrf
  <input type="hidden" name="listing_id" value="{{ $listing->id }}">
  <input type="hidden" name="can_listing_add" value="1">
  <input type="hidden" name="user_id" value="{{ $listing->user_id ?? 0 }}">
  <input type="hidden" name="category_parent_id" id="category_parent_id" value="{{ $selected_parent_category_id ?? '' }}">
  <input type="hidden" name="category_id" id="category_id" value="{{ $selected_category_id ?? '' }}">
  <input type="hidden" name="category_ids" id="category_ids" value="{{ isset($selected_category_ids) ? json_encode($selected_category_ids) : '[]' }}">
  <input type="hidden" name="latitude" id="latitude" value="{{ $listing->latitude ?? '' }}">
  <input type="hidden" name="longitude" id="longitude" value="{{ $listing->longitude ?? '' }}">
  <input type="hidden" name="working_hours" id="working_hours" value="{{ isset($working_hours_data) ? json_encode($working_hours_data) : '[]' }}">
  <input type="hidden" name="neighborhood_id" id="neighborhood_id" value="{{ isset($listing_content) && $listing_content ? $listing_content->neighborhood_id : '' }}">

  {{-- Error Box --}}
  <div class="alert alert-danger d-none" id="listingErrors">
    <strong>{{ __('خطاهای زیر را برطرف کنید:') }}</strong>
    <ul class="mb-0 mt-2"></ul>
  </div>

  {{-- اطلاعات اصلی --}}
  <div class="form-section">
    <h6>{{ __('اطلاعات اصلی کسب‌وکار') }}</h6>
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
          <button type="button" class="category-pill parent-category-btn {{ (isset($selected_parent_category_id) && $selected_parent_category_id == $parent->id) ? 'active' : '' }}" data-id="{{ $parent->id }}">
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
      <div id="selectedCategoriesDisplay" class="selected-categories d-none">
        <strong>{{ __('دسته‌های انتخاب شده:') }}</strong>
        <div id="selectedCategoriesList"></div>
      </div>
    </div>
  </div>

  {{-- موقعیت و آدرس --}}
  <div class="form-section">
    <h6>{{ __('موقعیت و آدرس') }}</h6>
    <div class="row mb-3">
      <div class="col-lg-4">
        <div class="form-group">
          <label>{{ __('استان') }} *</label>
          <select class="form-control" name="state_id" id="stateSelect">
            <option value="">{{ __('انتخاب استان') }}</option>
            @foreach ($states as $state)
              <option value="{{ $state->id }}" {{ (isset($listing_content) && $listing_content && $listing_content->state_id == $state->id) ? 'selected' : '' }}>{{ $state->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label>{{ __('شهر') }} *</label>
          <select class="form-control" name="city_id" id="citySelect">
            <option value="">{{ __('ابتدا استان را انتخاب کنید') }}</option>
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label>{{ __('محله') }}</label>
          <select class="form-control" name="neighborhood_id" id="neighborhoodSelect">
            <option value="">{{ __('ابتدا شهر را انتخاب کنید') }}</option>
          </select>
        </div>
      </div>
    </div>
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
    <div class="mb-3">
      <label>{{ __('انتخاب موقعیت روی نقشه') }} *</label>
      <div id="map" style="position: relative; background: #f5f5f5; border-radius: 0.75rem; min-height: 400px; display: flex; align-items: center; justify-content: center;">
        <div id="mapLoading" style="text-align: center; color: #666;">
          <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
          <p>در حال بارگذاری نقشه...</p>
        </div>
      </div>
      <small class="text-muted">{{ __('برای تعیین موقعیت دقیق، روی نقشه کلیک کنید یا از جستجوی آدرس استفاده کنید.') }}</small>
    </div>
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

  {{-- راه‌های ارتباطی --}}
  <div class="form-section">
    <h6>{{ __('راه‌های ارتباطی و شبکه‌های اجتماعی') }}</h6>
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

  {{-- امکانات --}}
  <div class="form-section">
    <h6>{{ __('امکانات') }}</h6>
    <div class="form-group">
      <label>{{ __('انتخاب امکانات') }}</label>
      <div class="d-flex flex-wrap">
        @php
          $selectedAmenities = isset($listing_content) && $listing_content ? json_decode($listing_content->aminities, true) : [];
          $selectedAmenities = is_array($selectedAmenities) ? $selectedAmenities : [];
        @endphp
        @foreach ($amenities as $amenity)
          <div class="form-check amenity-checkbox">
            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
              {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
            <label class="form-check-label" for="amenity_{{ $amenity->id }}">
              {{ $amenity->title }}
            </label>
          </div>
        @endforeach
        @if(count($amenities) == 0)
          <p class="text-muted">{{ __('هیچ امکاناتی ثبت نشده است.') }}</p>
        @endif
      </div>
    </div>
  </div>

  {{-- تصاویر و توضیحات --}}
  <div class="form-section">
    <h6>{{ __('تصاویر و توضیحات کامل') }}</h6>
    <div class="form-group">
      <label>{{ __('توضیحات کامل کسب‌وکار') }} *</label>
      <textarea name="full_description" class="form-control summernote" rows="6">{{ isset($listing_content) && $listing_content ? $listing_content->description : '' }}</textarea>
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
          @if ($listing->feature_image)
            <div class="mt-2">
              <img src="{{ asset('assets/img/listing/' . $listing->feature_image) }}" alt="Feature Image" class="img-thumbnail" width="150">
            </div>
          @endif
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>{{ __('لینک ویدئو (اختیاری)') }}</label>
          <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/..." value="{{ isset($listing) && $listing ? $listing->video_url : '' }}">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label>{{ __('تصویر پس‌زمینه ویدئو (اختیاری)') }}</label>
      <input type="file" name="video_background_image" class="form-control">
      @if ($listing->video_background_image)
        <div class="mt-2 position-relative">
          <img src="{{ asset('assets/img/listing/video/' . $listing->video_background_image) }}" alt="Video Background Image" class="img-thumbnail" width="150">
          <button type="button" class="btn btn-danger btn-sm position-absolute" style="top:0; right:0;" onclick="removeVideoImage({{ $listing->id }})">
            <i class="fas fa-times"></i>
          </button>
        </div>
      @endif
    </div>
  </div>

  {{-- ساعات کاری --}}
  <div class="form-section">
    <h6>{{ __('ساعات کاری') }}</h6>
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
  </div>

  <div class="form-group">
    <button type="submit" class="btn btn-success btn-lg">{{ __('ذخیره تغییرات') }}</button>
  </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('adminListingForm');
  const parentCategoriesContainer = document.getElementById('parentCategories');
  const childCategoriesContainer = document.getElementById('childCategories');
  const selectedCategoriesDisplay = document.getElementById('selectedCategoriesDisplay');
  const selectedCategoriesList = document.getElementById('selectedCategoriesList');
  const categoryParentIdInput = document.getElementById('category_parent_id');
  const categoryIdInput = document.getElementById('category_id');
  const categoryIdsInput = document.getElementById('category_ids');
  let selectedCategoryIds = JSON.parse(categoryIdsInput.value || '[]');

  const stateSelect = document.getElementById('stateSelect');
  const citySelect = document.getElementById('citySelect');
  const neighborhoodSelect = document.getElementById('neighborhoodSelect');
  const latitudeInput = document.getElementById('latitude');
  const longitudeInput = document.getElementById('longitude');
  const workingInput = document.getElementById('working_hours');
  const neighborhoodIdInput = document.getElementById('neighborhood_id');

  const categoryTree = @json($category_tree);
  const parentMap = new Map();
  if (categoryTree && Array.isArray(categoryTree)) {
    categoryTree.forEach(cat => {
      parentMap.set(cat.id, cat.children || []);
    });
  }

  // نمایش دسته‌های انتخاب شده
  function updateSelectedCategoriesDisplay() {
    if (selectedCategoryIds.length === 0) {
      selectedCategoriesDisplay.classList.add('d-none');
      return;
    }
    selectedCategoriesDisplay.classList.remove('d-none');
    selectedCategoriesList.innerHTML = '';
    selectedCategoryIds.forEach(catId => {
      const childBtn = document.querySelector(`.child-pill[data-id="${catId}"]`);
      if (childBtn) {
        const span = document.createElement('span');
        span.textContent = childBtn.textContent.replace(' ✓', '');
        selectedCategoriesList.appendChild(span);
      }
    });
  }

  // Render child categories
  function renderChildren(children) {
    childCategoriesContainer.innerHTML = '';
    if (!children.length) {
      childCategoriesContainer.innerHTML = `<span class="text-muted">برای این دسته، زیر دسته‌ای ثبت نشده است.</span>`;
      return;
    }
    children.forEach(child => {
      const div = document.createElement('div');
      div.className = 'form-check mr-3 mb-2';
      const input = document.createElement('input');
      input.type = 'checkbox';
      input.className = 'form-check-input child-pill';
      input.value = child.id;
      input.id = `child_category_${child.id}`;
      input.dataset.id = child.id;
      if (selectedCategoryIds.includes(child.id)) {
        input.checked = true;
      }

      const label = document.createElement('label');
      label.className = 'form-check-label';
      label.htmlFor = `child_category_${child.id}`;
      label.textContent = child.name;

      div.appendChild(input);
      div.appendChild(label);

      input.addEventListener('change', function(e) {
        e.stopPropagation();
        const categoryId = parseInt(this.dataset.id, 10);
        if (this.checked) {
          if (!selectedCategoryIds.includes(categoryId)) {
            selectedCategoryIds.push(categoryId);
          }
        } else {
          selectedCategoryIds = selectedCategoryIds.filter(id => id !== categoryId);
        }
        if (categoryIdsInput) categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
        if (selectedCategoryIds.length > 0) {
          categoryIdInput.value = selectedCategoryIds[0];
        } else {
          categoryIdInput.value = '';
        }
        updateSelectedCategoriesDisplay();
      });
      childCategoriesContainer.appendChild(div);
    });
    updateSelectedCategoriesDisplay();
  }

  // Parent category button click handler
  parentCategoriesContainer.querySelectorAll('.parent-category-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      parentCategoriesContainer.querySelectorAll('.parent-category-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      const parentId = parseInt(this.dataset.id, 10);
      categoryParentIdInput.value = parentId;
      
      // فقط دسته‌های مربوط به این parent را نگه داریم
      const children = parentMap.get(parentId) || [];
      const childrenIds = children.map(c => c.id);
      selectedCategoryIds = selectedCategoryIds.filter(id => childrenIds.includes(id));
      
      categoryIdInput.value = selectedCategoryIds.length > 0 ? selectedCategoryIds[0] : '';
      categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
      updateSelectedCategoriesDisplay();

      renderChildren(children);
    });
  });

  // Pre-select parent and child categories
  @if(isset($selected_parent_category_id) && $selected_parent_category_id)
    const parentBtn = document.querySelector(`.parent-category-btn[data-id="{{ $selected_parent_category_id }}"]`);
    if (parentBtn) {
      // First, get the children for this parent
      const parentId = parseInt("{{ $selected_parent_category_id }}", 10);
      const children = parentMap.get(parentId) || [];
      
      // Filter selected categories to only include children of this parent
      @if(isset($selected_category_ids) && is_array($selected_category_ids) && count($selected_category_ids) > 0)
        const allSelectedIds = @json($selected_category_ids);
        const childrenIds = children.map(c => c.id);
        selectedCategoryIds = allSelectedIds.filter(id => childrenIds.includes(id));
      @else
        selectedCategoryIds = [];
      @endif
      
      // Click the parent button to show children
      parentBtn.click();
      
      // After children are rendered, check the selected ones
      setTimeout(() => {
        selectedCategoryIds.forEach(catId => {
          const childCheckbox = document.querySelector(`.child-pill[data-id="${catId}"]`);
          if (childCheckbox) {
            childCheckbox.checked = true;
            // Also trigger the change event to update the display
            childCheckbox.dispatchEvent(new Event('change'));
          }
        });
        categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
        if (selectedCategoryIds.length > 0) {
          categoryIdInput.value = selectedCategoryIds[0];
        }
        updateSelectedCategoriesDisplay();
      }, 500);
    }
  @endif

  // Function to load cities
  function loadCities(stateId, selectedCityId = null) {
    if (!stateId) {
      citySelect.innerHTML = '<option value="">{{ __('ابتدا استان را انتخاب کنید') }}</option>';
      neighborhoodSelect.innerHTML = '<option value="">{{ __('ابتدا شهر را انتخاب کنید') }}</option>';
      return;
    }
    citySelect.innerHTML = '<option value="">{{ __('در حال بارگذاری...') }}</option>';
    fetch(`{{ route('admin.listing_management.get-city') }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ state_id: stateId })
    })
      .then(res => res.json())
      .then(data => {
        citySelect.innerHTML = '<option value="">{{ __('انتخاب شهر') }}</option>';
        if (data.cities && data.cities.length > 0) {
          data.cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.name;
            if (selectedCityId && city.id == selectedCityId) {
              option.selected = true;
            }
            citySelect.appendChild(option);
          });
        }
        // Load neighborhoods if city is already selected
        if (selectedCityId) {
          setTimeout(() => {
            loadNeighborhoods(selectedCityId);
          }, 100);
        }
      })
      .catch(error => {
        console.error('Error loading cities:', error);
        citySelect.innerHTML = '<option value="">{{ __('خطا در بارگذاری شهرها') }}</option>';
      });
  }

  // Load cities based on selected state
  if (stateSelect) {
    stateSelect.addEventListener('change', function() {
      loadCities(this.value);
    });
    
    // Load cities on page load if state is already selected (edit mode)
    @if(isset($listing_content) && $listing_content->state_id)
      const selectedStateId = {{ $listing_content->state_id }};
      const selectedCityId = {{ $listing_content->city_id ?? 'null' }};
      if (stateSelect.value == selectedStateId) {
        // State is already selected, load cities
        loadCities(selectedStateId, selectedCityId);
      }
    @endif
  }

  // Function to load neighborhoods
  function loadNeighborhoods(cityId, selectedNeighborhoodId = null) {
    if (!cityId) {
      neighborhoodSelect.innerHTML = '<option value="">{{ __('ابتدا شهر را انتخاب کنید') }}</option>';
      if (neighborhoodIdInput) {
        neighborhoodIdInput.value = '';
      }
      return;
    }
    neighborhoodSelect.innerHTML = '<option value="">{{ __('در حال بارگذاری...') }}</option>';
    fetch(`{{ route('admin.listing_management.get-neighborhood') }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ city_id: cityId })
    })
      .then(res => res.json())
      .then(data => {
        neighborhoodSelect.innerHTML = '<option value="">{{ __('انتخاب محله') }}</option>';
        if (data.neighborhoods && data.neighborhoods.length > 0) {
          data.neighborhoods.forEach(neighborhood => {
            const option = document.createElement('option');
            option.value = neighborhood.id;
            option.textContent = neighborhood.name;
            if (selectedNeighborhoodId && neighborhood.id == selectedNeighborhoodId) {
              option.selected = true;
            }
            neighborhoodSelect.appendChild(option);
          });
        }
        if (neighborhoodIdInput) {
          neighborhoodIdInput.value = neighborhoodSelect.value;
        }
      })
      .catch(error => {
        console.error('Error loading neighborhoods:', error);
        neighborhoodSelect.innerHTML = '<option value="">{{ __('خطا در بارگذاری محلات') }}</option>';
      });
  }

  // Load neighborhoods based on selected city
  if (citySelect) {
    citySelect.addEventListener('change', function() {
      loadNeighborhoods(this.value);
    });
  }

  // Update neighborhood_id when neighborhood select changes
  if (neighborhoodSelect) {
    neighborhoodSelect.addEventListener('change', function() {
      if (neighborhoodIdInput) {
        neighborhoodIdInput.value = this.value;
      }
    });
  }

  // Map initialization
  let mapInstance = null;
  let marker = null;
  let iranBounds = null;

  function initializeMap() {
    const mapContainer = document.getElementById('map');
    const mapLoading = document.getElementById('mapLoading');
    
    if (!mapContainer) {
      console.error('Map container not found');
      return;
    }
    
    // Check if Leaflet is already loaded
    if (typeof L !== 'undefined') {
      setupMap();
      return;
    }

    // Check if CSS is already loaded
    if (!document.querySelector('link[href*="leaflet.css"]')) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
      link.crossOrigin = '';
      document.head.appendChild(link);
    }

    // Check if script is already loaded
    if (document.querySelector('script[src*="leaflet.js"]')) {
      // Script is already in DOM, wait for it to load
      const checkLeaflet = setInterval(() => {
        if (typeof L !== 'undefined') {
          clearInterval(checkLeaflet);
          setupMap();
        }
      }, 100);
      setTimeout(() => clearInterval(checkLeaflet), 5000); // Timeout after 5 seconds
      return;
    }

    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
    script.crossOrigin = '';
    script.onload = function() {
      console.log('Leaflet loaded successfully');
      if (mapLoading) mapLoading.style.display = 'none';
      setupMap();
      // Set map view to existing coordinates if available
      @if(isset($listing) && $listing->latitude && $listing->longitude)
        const lat = {{ $listing->latitude }};
        const lng = {{ $listing->longitude }};
        setTimeout(() => {
          if (mapInstance && typeof L !== 'undefined') {
            mapInstance.setView([lat, lng], 15);
            if (marker) mapInstance.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(mapInstance);
            const addressText = "{{ isset($listing_content) && $listing_content->address ? addslashes($listing_content->address) : 'موقعیت کسب‌وکار' }}";
            marker.bindPopup(addressText).openPopup();
            if (latitudeInput) latitudeInput.value = lat.toFixed(6);
            if (longitudeInput) longitudeInput.value = lng.toFixed(6);
          }
        }, 500);
      @else
        if (mapInstance && iranBounds) {
          mapInstance.fitBounds(iranBounds); // Fit to Iran if no existing coordinates
        }
      @endif
    };
    script.onerror = function() {
      console.error('Error loading Leaflet script');
      if (mapLoading) {
        mapLoading.innerHTML = '<p style="color: red;">خطا در بارگذاری نقشه. لطفاً صفحه را رفرش کنید.</p>';
      }
    };
    document.head.appendChild(script);
  }

  function setupMap() {
    if (mapInstance) {
      console.log('Map already initialized');
      return; // Prevent re-initialization
    }
    
    if (typeof L === 'undefined') {
      console.error('Leaflet is not loaded');
      return;
    }
    
    // Define bounds after L is available
    iranBounds = L.latLngBounds([
      [25.0, 44.0], // Southwest
      [40.0, 64.0] // Northeast
    ]);
    
    try {
      mapInstance = L.map('map', {
        center: [32.4279, 53.6880], // Center of Iran
        zoom: 6,
        minZoom: 5,
        maxZoom: 18,
        maxBounds: iranBounds,
        preferCanvas: true,
        fadeAnimation: false,
        zoomAnimation: false,
      });

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 18,
      tileSize: 256,
      zoomOffset: 0,
      updateWhenZooming: false,
      updateWhenIdle: true,
      keepBuffer: 2,
    }).addTo(mapInstance);

    const cityCoordinates = {
      'تهران': [35.6892, 51.3890],
      'مشهد': [36.2605, 59.6168],
      'اصفهان': [32.6546, 51.6680],
      'شیراز': [29.5918, 52.5837],
      'تبریز': [38.0806, 46.2956],
      'کرج': [35.8243, 50.9910],
      'اهواز': [31.3200, 48.6700],
      'قم': [34.6414, 50.8746],
      'کرمانشاه': [34.3145, 47.0650],
      'ارومیه': [37.5500, 45.0700],
      'رشت': [37.2700, 49.5800],
      'زاهدان': [29.5000, 60.8000],
      'همدان': [34.7983, 48.5147],
      'کرمان': [30.2800, 57.0600],
      'یزد': [31.8970, 54.3670],
    };

    citySelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const cityName = selectedOption.textContent;
      if (cityName && cityCoordinates[cityName]) {
        const coords = cityCoordinates[cityName];
        mapInstance.setView(coords, 13);
        if (marker) mapInstance.removeLayer(marker);
        marker = L.marker(coords).addTo(mapInstance);
        marker.bindPopup(cityName).openPopup();
        latitudeInput.value = coords[0].toFixed(6);
        longitudeInput.value = coords[1].toFixed(6);
      }
    });

    mapInstance.on('click', function(e) {
      const { lat, lng } = e.latlng;
      latitudeInput.value = lat.toFixed(6);
      longitudeInput.value = lng.toFixed(6);
      if (marker) mapInstance.removeLayer(marker);
      marker = L.marker([lat, lng]).addTo(mapInstance);

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
        });
    });

    const addressSearchInput = document.getElementById('addressSearch');
    const searchAddressBtn = document.getElementById('searchAddressBtn');

    function searchAddress() {
      const address = addressSearchInput.value;
      if (!address) return;

      searchAddressBtn.disabled = true;
      searchAddressBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جستجو...';

      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${address}&limit=1&countrycodes=ir&accept-language=fa`, {
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

            if (iranBounds.contains([lat, lon])) {
              mapInstance.setView([lat, lon], 15);
              if (marker) mapInstance.removeLayer(marker);
              marker = L.marker([lat, lon]).addTo(mapInstance);
              marker.bindPopup(result.display_name).openPopup();

              latitudeInput.value = lat.toFixed(6);
              longitudeInput.value = lon.toFixed(6);

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
    } catch (error) {
      console.error('Error setting up map:', error);
      const mapLoading = document.getElementById('mapLoading');
      if (mapLoading) {
        mapLoading.innerHTML = '<p style="color: red;">خطا در راه‌اندازی نقشه: ' + error.message + '</p>';
      }
    }
  }

  // Initialize TinyMCE for full_description
  if (typeof tinymce !== 'undefined') {
    tinymce.init({
      selector: 'textarea.summernote',
      plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality emoticons template paste textpattern imagetools codesample toc help',
      toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
      height: 300,
      directionality: 'rtl',
      language: 'fa',
      setup: function(editor) {
        editor.on('change', function() {
          editor.save();
        });
      }
    });
  }

  // Video image removal
  window.removeVideoImage = function(listingId) {
    if (confirm('آیا از حذف این تصویر مطمئن هستید؟')) {
      fetch(`{{ route('admin.listing_management.video_image.delete', $listing->id) }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          document.querySelector('img[alt="Video Background Image"]').closest('div').remove();
          alert('تصویر پس‌زمینه ویدئو با موفقیت حذف شد.');
        } else {
          alert('خطا در حذف تصویر پس‌زمینه ویدئو.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('خطا در حذف تصویر پس‌زمینه ویدئو.');
      });
    }
  };

  // Working hours update
  function updateWorkingHours() {
    const rows = document.querySelectorAll('.working-row');
    const hours = [];
    rows.forEach(row => {
      const day = row.dataset.day;
      const status = row.querySelector('.working-status').value;
      const startTime = row.querySelector('.working-start').value;
      const endTime = row.querySelector('.working-end').value;
      hours.push({
        day: day,
        status: status,
        start_time: startTime,
        end_time: endTime
      });
    });
    if (workingInput) workingInput.value = JSON.stringify(hours);
  }

  document.querySelectorAll('.working-row select, .working-row input').forEach(el => {
    el.addEventListener('change', updateWorkingHours);
  });
  updateWorkingHours(); // Initial update
  initializeMap(); // Initialize map on page load for edit form
  
  // Prevent default behavior for amenities checkboxes to avoid page reload
  document.querySelectorAll('input[name="amenities[]"]').forEach(checkbox => {
    checkbox.addEventListener('click', function(e) {
      // Allow normal checkbox behavior - no AJAX call needed in edit form
      // The amenities will be saved when the form is submitted
    });
  });

  // Form submit handler
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      console.log('Form submit started');
      
      // Update working hours before submit
      updateWorkingHours();
      
      // Update neighborhood_id
      if (neighborhoodSelect && neighborhoodIdInput) {
        neighborhoodIdInput.value = neighborhoodSelect.value;
      }
      
      // Update category fields
      if (categoryIdsInput && selectedCategoryIds.length > 0) {
        categoryIdsInput.value = JSON.stringify(selectedCategoryIds);
        if (categoryIdInput) {
          categoryIdInput.value = selectedCategoryIds[0];
        }
      }
      
      // Ensure category_id is set
      if (categoryIdInput && !categoryIdInput.value && selectedCategoryIds.length > 0) {
        categoryIdInput.value = selectedCategoryIds[0];
      }
      
      // Show loading
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> در حال ذخیره...';
      }
      
      // Create FormData
      const formData = new FormData(form);
      
      // Debug: Log form data
      console.log('Form data:');
      for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
      }
      
      // Handle TinyMCE if exists
      if (typeof tinymce !== 'undefined') {
        const fullDescEditor = tinymce.get('full_description');
        if (fullDescEditor) {
          formData.set('full_description', fullDescEditor.getContent());
        }
      }
      
      // Submit form
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => {
          if (response.ok) {
            return data;
          } else {
            return Promise.reject(data);
          }
        });
      })
      .then(data => {
        console.log('Success data:', data);
        if (data.status === 'success') {
          // Show success message
          alert('تغییرات با موفقیت ذخیره شد!');
          // Reload page
          window.location.reload();
        } else {
          alert('خطا در ذخیره تغییرات: ' + (data.message || 'خطای نامشخص'));
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '{{ __('ذخیره تغییرات') }}';
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        let errorList = [];
        
        // Handle different error formats
        if (error.errors) {
          // Laravel validation errors
          errorList = Object.values(error.errors).flat();
        } else if (error.message) {
          errorList = [error.message];
        } else if (typeof error === 'string') {
          errorList = [error];
        } else {
          errorList = ['خطا در ارسال فرم. لطفاً دوباره تلاش کنید.'];
        }
        
        displayErrors(errorList);
        
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '{{ __('ذخیره تغییرات') }}';
        }
      });
      
      function displayErrors(errors) {
        const errorBox = document.getElementById('listingErrors');
        if (!errorBox) {
          console.error('Error box not found!');
          alert('خطا: ' + (errors.length > 0 ? errors.join('\n') : 'خطای نامشخص'));
          return;
        }
        const errorList = errorBox.querySelector('ul');
        if (!errorList) {
          console.error('Error list not found!');
          alert('خطا: ' + (errors.length > 0 ? errors.join('\n') : 'خطای نامشخص'));
          return;
        }
        if (errors.length > 0) {
          errorList.innerHTML = '';
          errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
          });
          errorBox.classList.remove('d-none');
          errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  }
});
</script>
@endpush

@push('scripts')
<script>
  // Variables for dropzone (needed by admin-dropzone.js)
  if (typeof storeUrl === 'undefined') {
    var storeUrl = "{{ route('admin.listing.imagesstore') }}";
  }
  if (typeof removeUrl === 'undefined') {
    var removeUrl = "{{ route('admin.listing.imagermv') }}";
  }
  if (typeof rmvdbUrl === 'undefined') {
    var rmvdbUrl = "{{ route('admin.listing.imgdbrmv') }}";
  }
  if (typeof galleryImages === 'undefined') {
    var galleryImages = {{ (isset($current_package_obj) && $current_package_obj ? $current_package_obj->number_of_images_per_listing : 99999999) - count($listing->galleries) }};
  }
  if (typeof baseURL === 'undefined') {
    const baseURL = "{{ url('/') }}";
  }
</script>
<script type="text/javascript" src="{{ asset('assets/admin/js/admin-dropzone.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
@endpush
