@extends(isset($is_admin) && $is_admin ? 'admin.layout' : 'vendors.layout')

@section('style')
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
@endsection

@section('content')
  @php
    // تعریف current_package_obj برای استفاده در view
    $current_package_obj = isset($current_package) && $current_package && $current_package != '[]' ? $current_package : null;
    if (!$current_package_obj) {
      // برای admin یا کاربران بدون پکیج، یک object ساختگی می‌سازیم
      $current_package_obj = (object)['number_of_images_per_listing' => 99999999];
    }
  @endphp
  <div class="page-header">
    <h4 class="page-title">{{ __('ویرایش کسب‌وکار') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ isset($is_admin) && $is_admin ? route('admin.dashboard') : route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ isset($is_admin) && $is_admin ? route('admin.listing_management.listing') : route('vendor.listing_management.listing') }}">{{ __('مدیریت کسب‌وکارها') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('ویرایش') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('ویرایش کسب‌وکار') }}</div>
          <a class="btn btn-info btn-sm float-left d-inline-block"
            href="{{ isset($is_admin) && $is_admin ? route('admin.listing_management.listing') : route('vendor.listing_management.listing') }}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{ __('بازگشت') }}
          </a>
          @if (!empty($listing_content) && !empty($listing_content->slug))
            <a class="btn btn-success btn-sm float-left mr-1 d-inline-block"
              href="{{ route('frontend.listing.details', ['slug' => $listing_content->slug, 'id' => $listing->id]) }}" target="_blank">
              <span class="btn-label">
                <i class="fas fa-eye"></i>
              </span>
              {{ __('مشاهده') }}
            </a>
          @endif
        </div>

        <div class="card-body">
          <div class="alert alert-danger pb-1 d-none" id="listingErrors">
            <strong>{{ __('خطاهای زیر را برطرف کنید:') }}</strong>
            <ul class="mb-0 mt-2"></ul>
          </div>

          @if(isset($is_admin) && $is_admin)
            {{-- فرم جدید برای admin --}}
            @include('vendors.listing.admin_edit_form', [
              'listing' => $listing,
              'listing_content' => $listing_content,
              'parent_categories' => $parent_categories,
              'category_tree' => $category_tree,
              'states' => $states,
              'selected_category_id' => $selected_category_id,
              'selected_parent_category_id' => $selected_parent_category_id,
              'selected_category_ids' => $selected_category_ids ?? [],
              'working_hours_data' => $working_hours_data ?? [],
              'amenities' => $amenities ?? [],
              'neighborhoods' => $neighborhoods ?? [],
            ])
                                        @else
            {{-- فرم vendor --}}
            <p class="text-info">{{ __('برای ویرایش کامل، لطفاً از پنل مدیریت استفاده کنید.') }}</p>
                            @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script type="text/javascript" src="{{ asset('assets/admin/js/feature.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-dropzone.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin-listing.js') }}"></script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing.imagesstore') : route('vendor.listing.imagesstore') }}";
    var removeUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing.imagermv') : route('vendor.listing.imagermv') }}";
    var rmvdbUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing.imgdbrmv') : route('vendor.listing.imgdbrmv') }}";
    var videoId = {{ $listing->id }};
    var videormvdbUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.video_image.delete', ['id' => ':videoId']) : route('vendor.listing_management.video_image.delete', ['id' => ':videoId']) }}";
    videormvdbUrl = videormvdbUrl.replace(':videoId', videoId);
    var getStateUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.get-state') : route('vendor.listing_management.get-state') }}";
    var getCityUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.get-city') : route('vendor.listing_management.get-city') }}";
    var featureRmvUrl = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.feature_delete') : route('vendor.listing_management.feature_delete') }}"
    var updateAminitie = "{{ isset($is_admin) && $is_admin ? route('admin.listing_management.update_aminitie') : route('vendor.listing_management.update_aminitie') }}"
    var galleryImages = {{ (isset($current_package_obj) && $current_package_obj ? $current_package_obj->number_of_images_per_listing : 99999999) - count($listing->galleries) }};
    var languages = {!! json_encode($languages) !!};
    const baseURL = "{{ url('/') }}";
  </script>
@endsection
