@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">افزودن صفحه جدید</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.category_location_pages.index') }}">صفحات دسته‌بندی و مکان</a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">افزودن</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title">افزودن صفحه جدید</div>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.category_location_pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>دسته‌بندی <span class="text-danger">*</span></label>
                  <select name="category_id" class="form-control" required>
                    <option value="">انتخاب دسته‌بندی</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>استان</label>
                  <select name="state_id" class="form-control" id="state_id">
                    <option value="">انتخاب استان</option>
                    @foreach ($states as $state)
                      <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>شهر</label>
                  <select name="city_id" class="form-control" id="city_id">
                    <option value="">ابتدا استان را انتخاب کنید</option>
                  </select>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>محله</label>
                  <select name="neighborhood_id" class="form-control" id="neighborhood_id">
                    <option value="">ابتدا شهر را انتخاب کنید</option>
                  </select>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>عنوان صفحه <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="title" value="{{ old('title') }}" required placeholder="مثال: بهترین باشگاه‌های تهران">
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>Slug (URL)</label>
                  <input type="text" class="form-control ltr" name="slug" value="{{ old('slug') }}" placeholder="بهترین-باشگاه-های-تهران">
                  <small class="text-muted">اگر خالی بگذارید، از عنوان ساخته می‌شود</small>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <label>توضیحات</label>
                  <textarea class="form-control summernote" name="description" rows="5">{{ old('description') }}</textarea>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>Meta Title</label>
                  <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}">
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>Meta Keywords</label>
                  <input type="text" class="form-control" name="meta_keywords" value="{{ old('meta_keywords') }}">
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <label>Meta Description</label>
                  <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label>تصویر شاخص</label>
                  <br>
                  <div class="thumb-preview">
                    <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..." class="uploaded-img" style="max-width: 200px;">
                  </div>
                  <div class="mt-3">
                    <div role="button" class="btn btn-primary btn-sm upload-btn">
                      انتخاب تصویر
                      <input type="file" class="img-input" name="featured_image">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-3">
                <div class="form-group">
                  <label>وضعیت</label>
                  <select name="status" class="form-control">
                    <option value="1">فعال</option>
                    <option value="0">غیرفعال</option>
                  </select>
                </div>
              </div>

              <div class="col-lg-3">
                <div class="form-group">
                  <label>ترتیب نمایش</label>
                  <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 0) }}">
                </div>
              </div>
            </div>

            <div class="card-action">
              <button type="submit" class="btn btn-success">ذخیره</button>
              <a href="{{ route('admin.category_location_pages.index') }}" class="btn btn-danger">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function() {
      // State change handler
      $('#state_id').on('change', function() {
        var stateId = $(this).val();
        var citySelect = $('#city_id');
        var neighborhoodSelect = $('#neighborhood_id');
        
        citySelect.empty().append('<option value="">انتخاب شهر</option>');
        neighborhoodSelect.empty().append('<option value="">ابتدا شهر را انتخاب کنید</option>');
        
        if (stateId) {
          $.ajax({
            url: baseUrl + 'admin/listing-specification/location/get-city-neighborhood',
            type: 'POST',
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              id: stateId
            },
            success: function(response) {
              if (response.status === 'success' && response.cities) {
                $.each(response.cities, function(index, city) {
                  citySelect.append($('<option>', {
                    value: city.id,
                    text: city.name
                  }));
                });
              }
            }
          });
        }
      });

      // City change handler
      $('#city_id').on('change', function() {
        var cityId = $(this).val();
        var neighborhoodSelect = $('#neighborhood_id');
        
        neighborhoodSelect.empty().append('<option value="">انتخاب محله</option>');
        
        if (cityId) {
          $.ajax({
            url: baseUrl + 'admin/listing-specification/location/neighborhood/get-by-city',
            type: 'POST',
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              city_id: cityId
            },
            success: function(response) {
              if (response.status === 'success' && response.neighborhoods) {
                $.each(response.neighborhoods, function(index, neighborhood) {
                  neighborhoodSelect.append($('<option>', {
                    value: neighborhood.id,
                    text: neighborhood.name
                  }));
                });
              }
            }
          });
        }
      });

      // Auto-generate slug from title
      function createSlug(text) {
        return text
          .toString()
          .toLowerCase()
          .replace(/\s+/g, '-')
          .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-z0-9-]+/g, '')
          .replace(/--+/g, '-')
          .replace(/^-+/, '')
          .replace(/-+$/, '');
      }

      var titleInput = $('input[name="title"]');
      var slugInput = $('input[name="slug"]');
      var slugManuallyChanged = false;

      // Generate slug when title changes (only if slug is empty or was auto-generated)
      titleInput.on('blur', function() {
        if (!slugManuallyChanged && (!slugInput.val() || slugInput.data('auto-generated'))) {
          var slug = createSlug($(this).val());
          slugInput.val(slug);
          slugInput.data('auto-generated', true);
        }
      });

      // Track manual slug changes
      slugInput.on('input', function() {
        slugManuallyChanged = true;
        slugInput.data('auto-generated', false);
      });
    });
  </script>
@endsection

