<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit Listing Category') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxEditForm" class="modal-form {{ $language->direction == 1 ? 'rtl text-left' : '' }}"
          action="{{ route('admin.listing_specification.update_category') }}" method="post"
          enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="in_id" name="id">

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Name') . '*' }}</label>
                <input type="text" id="in_name" class="form-control" name="name" placeholder="{{ __('Enter Category Name') }}">
                <p id="editErr_name" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Slug (URL)') }}</label>
                <input type="text" id="in_slug" class="form-control ltr" name="slug" placeholder="{{ __('Enter Slug') }}">
                <p id="editErr_slug" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('URL-friendly version of the name') }}</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Parent Category') }}</label>
                <select name="parent_id" id="in_parent_id" class="form-control">
                  <option value="">{{ __('None (Main Category)') }}</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" data-cat-id="{{ $cat->id }}">
                      @if ($cat->parent)
                        {{ $cat->parent->name }} > 
                      @endif
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
                <p id="editErr_parent_id" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="form-group">
                <label for="">{{ __('Status') . '*' }}</label>
                <select name="status" id="in_status" class="form-control">
                  <option disabled>{{ __('Select Category Status') }}</option>
                  <option value="1">{{ __('Active') }}</option>
                  <option value="0">{{ __('Deactive') }}</option>
                </select>
                <p id="editErr_status" class="mt-2 mb-0 text-danger em"></p>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="">{{ __('Icon') . '*' }}</label>
            <div class="btn-group d-block">
              <button type="button" class="btn btn-primary iconpicker-component edit-iconpicker-component">
                <i class="" id="in_icon"></i>
              </button>
              <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle" data-selected="fa-car"
                data-toggle="dropdown"></button>
              <div class="dropdown-menu"></div>
            </div>

            <input type="hidden" id="editInputIcon" name="icon">
            <p id="editErr_icon" class="mt-2 mb-0 text-danger em"></p>

            <div class="text-warning mt-2">
              <small>{{ __('Click on the dropdown icon to select an icon.') }}</small>
            </div>
          </div>

          <div class="form-group">
            <label for="">{{ __('Description') }}</label>
            <textarea class="form-control summernote" name="description" id="in_description" rows="5" placeholder="{{ __('Enter Category Description') }}"></textarea>
            <p id="editErr_description" class="mt-2 mb-0 text-danger em"></p>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="card-title">{{ __('SEO Settings') }}</h5>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="">{{ __('Meta Title') }}</label>
                <input type="text" id="in_meta_title" class="form-control" name="meta_title" placeholder="{{ __('Enter Meta Title') }}">
                <p id="editErr_meta_title" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('Recommended: 50-60 characters') }}</small>
              </div>

              <div class="form-group">
                <label for="">{{ __('Meta Description') }}</label>
                <textarea class="form-control" name="meta_description" id="in_meta_description" rows="3" placeholder="{{ __('Enter Meta Description') }}"></textarea>
                <p id="editErr_meta_description" class="mt-2 mb-0 text-danger em"></p>
                <small class="text-muted">{{ __('Recommended: 150-160 characters') }}</small>
              </div>

              <div class="form-group">
                <label for="">{{ __('Meta Keywords') }}</label>
                <input type="text" id="in_meta_keywords" class="form-control" name="meta_keywords" placeholder="{{ __('Enter Meta Keywords (comma separated)') }}">
                <p id="editErr_meta_keywords" class="mt-2 mb-0 text-danger em"></p>
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
        <button id="updateBtn" type="button" class="btn btn-primary btn-sm">
          {{ __('Update') }}
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Wait for jQuery to be available
  (function() {
    function initializeEditButton() {
      if (typeof window.jQuery !== 'undefined') {
        window.jQuery(document).ready(function($) {
          $('.editBtn').on('click', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      const slug = $(this).data('slug') || '';
      const status = $(this).data('status');
      const icon = $(this).data('icon') || '';
      const parentId = $(this).data('parent_id') || '';
      const description = $(this).data('description') || '';
      const metaTitle = $(this).data('meta_title') || '';
      const metaDescription = $(this).data('meta_description') || '';
      const metaKeywords = $(this).data('meta_keywords') || '';

      $('#in_id').val(id);
      $('#in_name').val(name);
      $('#in_slug').val(slug);
      $('#in_status').val(status);
      $('#editInputIcon').val(icon);
      $('#in_icon').attr('class', icon);
      $('#in_meta_title').val(metaTitle);
      $('#in_meta_description').val(metaDescription);
      $('#in_meta_keywords').val(metaKeywords);

      // Remove current category and its children from parent dropdown
      $('#in_parent_id option').show();
      $('#in_parent_id option[data-cat-id="' + id + '"]').hide();
      
      // Hide all children of current category (prevent circular reference)
      // This is a simple check - in production you might want a recursive check
      $('#in_parent_id').val(parentId);

      // Set summernote/tinymce content
      if (typeof tinymce !== 'undefined' && tinymce.get('in_description')) {
        tinymce.get('in_description').setContent(description);
      } else if (typeof tinyMCE !== 'undefined' && tinyMCE.get('in_description')) {
        tinyMCE.get('in_description').setContent(description);
      } else {
        $('#in_description').val(description);
      }

      // Update icon picker
      $('.edit-iconpicker-component').find('i').attr('class', icon);
    });
          });
        });
      } else {
        // Retry after a short delay if jQuery is not yet loaded
        setTimeout(initializeEditButton, 50);
      }
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initializeEditButton);
    } else {
      initializeEditButton();
    }
  })();
</script>
