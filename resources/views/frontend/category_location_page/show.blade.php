@extends('frontend.layout')

@section('pageHeading')
  {{ $page->title }}
@endsection

@section('metaKeywords')
  {{ $page->meta_keywords }}
@endsection

@section('metaDescription')
  {{ $page->meta_description }}
@endsection

@section('content')
  <div class="breadcrumb-area" style="background-image: url('{{ $bgImg }}');">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="breadcrumb-inner">
            <h2 class="page-title">{{ $page->title }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="listing-area pt-100 pb-100">
    <div class="container">
      @if($page->description)
        <div class="row mb-40">
          <div class="col-lg-12">
            <div class="page-description">
              {!! $page->description !!}
            </div>
          </div>
        </div>
      @endif

      <div class="row">
        @forelse($listings as $listing)
          <div class="col-md-6 col-lg-4 mb-30" data-aos="fade-up">
            <div class="product-default border radius-md">
              <figure class="product-img">
                <a href="{{ route('frontend.listing.details', ['slug' => $listing->slug, 'id' => $listing->id]) }}"
                  class="lazy-container ratio ratio-2-3">
                  <img class="lazyload"
                    data-src="{{ asset('assets/img/listing/' . $listing->feature_image) }}"
                    alt="{{ $listing->title }}">
                </a>

                @if (Auth::check())
                  @php
                    $user_id = Auth::user()->id;
                    $checkWishList = checkWishList($listing->id, $user_id);
                  @endphp
                @else
                  @php
                    $checkWishList = false;
                  @endphp
                @endif
                <a href="{{ $checkWishList == false ? route('addto.wishlist', $listing->id) : route('remove.wishlist', $listing->id) }}"
                  class="btn-icon {{ $checkWishList == false ? '' : 'wishlist-active' }}" data-tooltip="tooltip"
                  data-bs-placement="top"
                  title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                  <i class="fal fa-heart"></i>
                </a>
              </figure>

              <div class="product-details">
                <div class="product-top mb-10">
                  @php
                    if ($listing->user_id == 0) {
                        $vendorInfo = App\Models\Admin::first();
                        $userName = 'admin';
                    } else {
                        $vendorInfo = App\Models\User::findorfail($listing->user_id);
                        $userName = $vendorInfo->username;
                    }
                  @endphp

                  <div class="author">
                    <a class="color-medium"
                      href="{{ route('frontend.vendor.details', ['username' => $userName]) }}" target="_self"
                      title={{ $vendorInfo->username }}>

                      @if ($listing->user_id == 0)
                        <img class="lazyload" src="assets/images/placeholder.png"
                          data-src="{{ asset('assets/img/admins/' . $vendorInfo->image) }}" alt="Vendor">
                      @else
                        @if ($vendorInfo->photo != null)
                          <img class="blur-up lazyload"
                            data-src="{{ asset('assets/admin/img/vendor-photo/' . $vendorInfo->photo) }}"
                            alt="Image">
                        @else
                          <img class="blur-up lazyload" data-src="{{ asset('assets/img/blank-user.jpg') }}"
                            alt="Image">
                        @endif
                      @endif
                      <span>{{ __('By') }}
                        {{ $vendorInfo->username }}
                      </span>
                    </a>
                  </div>
                  @php
                    $categorySlug = App\Models\ListingCategory::findorfail($listing->category_id);
                  @endphp
                  <a href="{{ route('frontend.listings', ['category_id' => $categorySlug->slug]) }}"
                    title="Link" class="product-category font-sm icon-start">
                    <i class="{{ $listing->icon }}"></i>{{ $listing->category_name }}
                  </a>
                </div>
                <h6 class="product-title mb-10">
                  <a href="{{ route('frontend.listing.details', ['slug' => $listing->slug, 'id' => $listing->id]) }}">
                    {{ $listing->title }}
                  </a>
                </h6>
                <div class="product-ratings mb-10">
                  <div class="ratings">
                    @php
                      $rateStar = asset('assets/front/img/star.png');
                      $averageRating = $listing->average_rating ?? 0;
                    @endphp
                    <div class="rate" style="background-image: url('{{ $rateStar }}')">
                      <div class="rating-icon"
                        style="background-image: url('{{ $rateStar }}'); width: {{ $averageRating * 20 }}%;">
                      </div>
                    </div>
                    <span class="ratings-total font-sm">({{ $averageRating }})</span>
                    <span class="ratings-total color-medium ms-1 font-sm">
                      {{ totalListingReview($listing->id) }} {{ __('Reviews') }}
                    </span>
                  </div>
                </div>
                @php
                  $city = null;
                  $State = null;
                  $country = null;
                  $neighborhood = null;

                  if ($listing->city_id) {
                      $city = App\Models\Location\City::Where('id', $listing->city_id)->first()->name ?? null;
                  }
                  if ($listing->state_id) {
                      $State = App\Models\Location\State::Where('id', $listing->state_id)->first()->name ?? null;
                  }
                  if ($listing->neighborhood_id) {
                      $neighborhood = App\Models\Location\Neighborhood::Where('id', $listing->neighborhood_id)->first()->name ?? null;
                  }
                @endphp
                <span class="product-location icon-start font-sm">
                  <i class="fal fa-map-marker-alt"></i>
                  @if($neighborhood){{ $neighborhood }}, @endif
                  @if($city){{ $city }}@endif
                  @if($State), {{ $State }}@endif
                </span>
              </div>
            </div>
          </div>
        @empty
          <div class="col-lg-12">
            <div class="p-4 text-center bg-light radius-md">
              <h3 class="mb-0">هیچ آگهی‌ای یافت نشد!</h3>
            </div>
          </div>
        @endforelse
      </div>

      <div class="row">
        <div class="col-lg-12">
          {{ $listings->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection

