@extends('admin.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('admin.partials.rtl-style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Categories') }}</h4>
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
        <a href="#">{{ __('Listing Specifications') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Categories') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title d-inline-block">{{ __('Categories') }}</div>
            </div>

            <div class="col-lg-8 offset-lg-0 mt-2 mt-lg-0">
              <a href="#" data-toggle="modal" data-target="#createModal"
                class="btn btn-primary btn-sm float-lg-left float-left"><i class="fas fa-plus"></i>
                {{ __('Add') }}</a>

              <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                data-href="{{ route('admin.listing_specification.bulk_delete_category') }}">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($categories) == 0)
                <h3 class="text-center mt-2">{{ __('NO CATEGORY FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Parent') }}</th>
                        <th scope="col">{{ __('Slug') }}</th>
                        <th scope="col">{{ __('Icon') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($categories as $category)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $category->id }}">
                          </td>
                          <td>
                            {{ strlen($category->name) > 30 ? mb_substr($category->name, 0, 30, 'UTF-8') . '...' : $category->name }}
                          </td>
                          <td>
                            @if ($category->parent)
                              <span class="badge badge-info">{{ $category->parent->name }}</span>
                            @else
                              <span class="text-muted">—</span>
                            @endif
                          </td>
                          <td>
                            <code class="text-sm">{{ strlen($category->slug) > 20 ? mb_substr($category->slug, 0, 20, 'UTF-8') . '...' : $category->slug }}</code>
                          </td>
                          <td><i class="{{ $category->icon }}"></i></td>
                          <td>
                            @if ($category->status == 1)
                              <h2 class="d-inline-block"><span class="badge badge-success">{{ __('Active') }}</span>
                              </h2>
                            @else
                              <h2 class="d-inline-block"><span class="badge badge-danger">{{ __('Deactive') }}</span>
                              </h2>
                            @endif
                          </td>
                          <td>
                            <a class="btn btn-secondary btn-sm mr-1  mt-1 editBtn" href="#" data-toggle="modal"
                              data-target="#editModal" data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                              data-slug="{{ $category->slug }}" data-status="{{ $category->status }}" 
                              data-icon="{{ $category->icon }}" data-parent_id="{{ $category->parent_id }}"
                              data-description="{{ $category->description }}"
                              data-meta_title="{{ $category->meta_title }}"
                              data-meta_description="{{ $category->meta_description }}"
                              data-meta_keywords="{{ $category->meta_keywords }}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                            </a>
                            <form class="deleteForm d-inline-block"
                              action="{{ route('admin.listing_specification.delete_category', ['id' => $category->id]) }}"
                              method="post">
                              @csrf
                              <button type="submit" class="btn btn-danger  mt-1 btn-sm deleteBtn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>

  {{-- create modal --}}
  @include('admin.listing.category.create')

  {{-- edit modal --}}
  @include('admin.listing.category.edit')
@endsection

@section('script')
  <script>
    // Wait for jQuery and DOM to be ready
    (function() {
      function initTinyMCE() {
        if (typeof window.jQuery !== 'undefined' && typeof tinymce !== 'undefined') {
          window.jQuery(document).ready(function($) {
            $('.summernote').each(function() {
              const id = $(this).attr('id');
              if (id && !tinymce.get(id)) {
                tinymce.init({
                  selector: '#' + id,
                  height: 300,
                  menubar: false,
                  plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                  ],
                  toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                  directionality: 'rtl',
                  language: 'fa',
                  setup: function(editor) {
                    editor.on('init', function() {
                      // Editor initialized successfully
                    });
                  }
                });
              }
            });
          });
        } else {
          // Retry after a short delay
          setTimeout(initTinyMCE, 50);
        }
      }
      
      // Start initialization
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTinyMCE);
      } else {
        initTinyMCE();
      }
    })();
  </script>
@endsection
