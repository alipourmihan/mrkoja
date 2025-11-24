@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">محلات</h4>
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
        <a href="#">{{ __('Listing Specification') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Location') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-left-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">محلات</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title">محلات</div>
            </div>

            <div class="col-sm-6 col-lg-3">
              @includeIf('admin.partials.languages')
            </div>

            <div class="col-sm-6 col-lg-4 offset-lg-1 mt-2 mt-lg-0">
              <div class="text-left">
                <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm"><i
                    class="fas fa-plus"></i>
                  افزودن</a>

                <button class="btn btn-danger btn-sm ml-2 d-none bulk-delete"
                  data-href="{{ route('admin.listing_specification.location.bulk_delete_neighborhood') }}">
                  <i class="flaticon-interface-5"></i> حذف
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col">
              @if (count($neighborhoods) == 0)
                <h3 class="text-center mt-2">محله‌ای یافت نشد!</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">نام</th>
                        <th scope="col">استان</th>
                        <th scope="col">شهر</th>
                        <th scope="col">عملیات</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($neighborhoods as $neighborhood)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $neighborhood->id }}">
                          </td>
                          <td>
                            {{ strlen($neighborhood->name) > 20 ? mb_substr($neighborhood->name, 0, 20, 'UTF-8') . '...' : $neighborhood->name }}
                          </td>
                          <td>
                            @if ($neighborhood->state_id)
                              {{ $neighborhood->state->name }}
                            @else
                              --
                            @endif
                          </td>
                          <td>
                            @if ($neighborhood->city_id)
                              {{ $neighborhood->city->name }}
                            @else
                              --
                            @endif
                          </td>

                          <td>
                            @php
                              $stateQuery = App\Models\Location\State::query()->where('id', $neighborhood->state_id);
                              if (!empty($stateHasLanguageColumn) && $stateHasLanguageColumn && isset($neighborhood->language_id)) {
                                  $stateQuery->where('language_id', $neighborhood->language_id);
                              }
                              $x = $stateQuery->count();
                              $okValue = $x != 0 ? 'OK' : null;
                            @endphp
                            @if($neighborhood->frontend_url)
                              <a class="btn btn-info btn-sm mr-1 mt-1" href="{{ $neighborhood->frontend_url }}" target="_blank" title="مشاهده صفحه">
                                <span class="btn-label">
                                  <i class="fas fa-eye"></i>
                                </span>
                              </a>
                            @endif

                            <a class="btn btn-secondary btn-sm mr-1  mt-1 editBtn" href="#" data-toggle="modal"
                              data-target="#editModal" data-id="{{ $neighborhood->id }}"
                              data-state_id="{{ $neighborhood->state_id }}"
                              data-city_id="{{ $neighborhood->city_id }}"
                              data-ok="{{ $okValue }}"
                              data-name="{{ $neighborhood->name }}"
                              data-slug="{{ $neighborhood->slug ?? '' }}">
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                            </a>

                            <form class="deleteForm d-inline-block"
                              action="{{ route('admin.listing_specification.location.delete_neighborhood', ['id' => $neighborhood->id]) }}"
                              method="post">
                              @csrf
                              <button type="submit" class="btn btn-danger btn-sm  mt-1 deleteBtn">
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
  @include('admin.listing.location.neighborhood.create')

  {{-- edit modal --}}
  @include('admin.listing.location.neighborhood.edit')
@endsection
@section('script')
  <script src="{{ asset('assets/admin/js/neighborhood.js') }}"></script>
@endsection

