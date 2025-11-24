<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Listing Category') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxForm" class="modal-form create"
          action="{{ route('admin.listing_specification.store_category') }}" method="post"
          enctype="multipart/form-data">
          @csrf
          
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Name') . '*' }}</label>
                <input type="text" class="form-control" name="name" id="category_name" placeholder="{{ __('Enter Category Name') }}">
                <p id="err_name" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Slug (URL)') }}</label>
                <input type="text" class="form-control ltr" name="slug" id="category_slug" placeholder="{{ __('Enter Slug (leave empty to auto-generate)') }}">
                <p id="err_slug" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('If left empty, slug will be generated from name') }}</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Parent Category') }}</label>
                <select name="parent_id" class="form-control" id="parent_category">
                  <option value="">{{ __('None (Main Category)') }}</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">
                      @if ($cat->parent)
                        {{ $cat->parent->name }} > 
                      @endif
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
                <p id="err_parent_id" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Status') . '*' }}</label>
                <select name="status" class="form-control">
                  <option selected disabled>{{ __('Select Category Status') }}</option>
                  <option value="1">{{ __('Active') }}</option>
                  <option value="0">{{ __('Deactive') }}</option>
                </select>
                <p id="err_status" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="">{{ __('Icon') . '*' }}</label>
            <div class="btn-group d-block">
              <button type="button" class="btn btn-primary iconpicker-component">
                <i class="fas fa-shopping-cart"></i>
              </button>
              <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle" data-selected="fa-car"
                data-toggle="dropdown"></button>
              <div class="dropdown-menu"></div>
            </div>

            <input type="hidden" id="inputIcon" name="icon">
            <p id="err_icon" class="mt-2 mb-0 text-danger em"></p>

            <div class="text-warning mt-2">
              <small>{{ __('Click on the dropdown icon to select an icon.') }}</small>
            </div>
          </div>

          <div class="form-group">
            <label for="">{{ __('Description') }}</label>
            <textarea class="form-control summernote" name="description" id="category_description" rows="5" placeholder="{{ __('Enter Category Description') }}"></textarea>
            <p id="err_description" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="card-title">{{ __('SEO Settings') }}</h5>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="">{{ __('Meta Title') }}</label>
                <input type="text" class="form-control" name="meta_title" placeholder="{{ __('Enter Meta Title') }}">
                <p id="err_meta_title" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('Recommended: 50-60 characters') }}</small>
              </div>

              <div class="form-group">
                <label for="">{{ __('Meta Description') }}</label>
                <textarea class="form-control" name="meta_description" rows="3" placeholder="{{ __('Enter Meta Description') }}"></textarea>
                <p id="err_meta_description" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('Recommended: 150-160 characters') }}</small>
              </div>

              <div class="form-group">
                <label for="">{{ __('Meta Keywords') }}</label>
                <input type="text" class="form-control" name="meta_keywords" placeholder="{{ __('Enter Meta Keywords (comma separated)') }}">
                <p id="err_meta_keywords" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('Separate keywords with commas') }}</small>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          {{ __('Close') }}
        </button>
        <button id="submitBtn" type="button" class="btn btn-primary btn-sm">
          {{ __('Save') }}
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Auto-generate slug from name
  document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('category_name');
    const slugInput = document.getElementById('category_slug');
    
    if (nameInput && slugInput) {
      nameInput.addEventListener('blur', function() {
        if (!slugInput.value) {
          // Generate slug from name (you can use a slug function here)
          const slug = nameInput.value.toLowerCase()
            .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
          slugInput.value = slug;
        }
      });
    }
  });
</script>
