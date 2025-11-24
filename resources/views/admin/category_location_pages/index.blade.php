@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">صفحات دسته‌بندی و مکان</h4>
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
        <a href="#">صفحات داینامیک</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title">صفحات دسته‌بندی و مکان</div>
            </div>

            <div class="col-sm-6 col-lg-4 offset-lg-4 mt-2 mt-lg-0">
              <div class="text-left">
                <a href="{{ route('admin.category_location_pages.create') }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> افزودن
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col">
              @if (count($pages) == 0)
                <h3 class="text-center mt-2">صفحه‌ای یافت نشد!</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">عنوان</th>
                        <th scope="col">دسته‌بندی</th>
                        <th scope="col">استان</th>
                        <th scope="col">شهر</th>
                        <th scope="col">محله</th>
                        <th scope="col">وضعیت</th>
                        <th scope="col">عملیات</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($pages as $page)
                        <tr>
                          <td>{{ $page->title }}</td>
                          <td>{{ $page->category ? $page->category->name : '--' }}</td>
                          <td>{{ $page->state ? $page->state->name : '--' }}</td>
                          <td>{{ $page->city ? $page->city->name : '--' }}</td>
                          <td>{{ $page->neighborhood ? $page->neighborhood->name : '--' }}</td>
                          <td>
                            @if ($page->status == 1)
                              <span class="badge badge-success">فعال</span>
                            @else
                              <span class="badge badge-danger">غیرفعال</span>
                            @endif
                          </td>
                          <td>
                            @if($page->frontend_url)
                              <a class="btn btn-info btn-sm mr-1 mt-1" href="{{ $page->frontend_url }}" target="_blank" title="مشاهده صفحه">
                                <span class="btn-label">
                                  <i class="fas fa-eye"></i>
                                </span>
                              </a>
                            @endif

                            <a class="btn btn-secondary btn-sm mr-1 mt-1" href="{{ route('admin.category_location_pages.edit', $page->id) }}" title="ویرایش">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                            </a>

                            <form class="deleteForm d-inline-block" action="{{ route('admin.category_location_pages.destroy', $page->id) }}" method="post">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-sm mt-1 deleteBtn" title="حذف">
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
        <div class="card-footer">
          {{ $pages->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection

