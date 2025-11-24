<div class="sidebar sidebar-style-2"
  data-background-color="{{ Session::get('vendor_theme_version') == 'light' ? 'white' : 'dark2' }}">
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <div class="user">
        <div class="avatar-sm float-left mr-2">
          @if (Auth::user()->photo != null)
            <img src="{{ asset('assets/admin/img/vendor-photo/' . Auth::user()->photo) }}"
              alt="Vendor Image" class="avatar-img rounded-circle">
          @else
            <img src="{{ asset('assets/img/blank-user.jpg') }}" alt="" class="avatar-img rounded-circle">
          @endif
        </div>

        <div class="info">
          <a data-toggle="collapse" href="#adminProfileMenu" aria-expanded="true">
            <span>
              {{ Auth::user()->username }}
              <span class="user-level">{{ __('Vendor') }}</span>
              <span class="caret"></span>
            </span>
          </a>

          <div class="clearfix"></div>

          <div class="collapse in" id="adminProfileMenu">
            <ul class="nav">
              <li>
                <a href="{{ route('vendor.edit.profile') }}">
                  <span class="link-collapse">{{ __('Edit Profile') }}</span>
                </a>
              </li>

              <li>
                <a href="{{ route('vendor.change_password') }}">
                  <span class="link-collapse">{{ __('Change Password') }}</span>
                </a>
              </li>

              <li>
                <a href="{{ route('vendor.logout') }}">
                  <span class="link-collapse">{{ __('Logout') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>


      <ul class="nav nav-primary">
        {{-- search --}}
        <div class="row mb-3">
          <div class="col-12">
            <form>
              <div class="form-group py-0">
                <input name="term" type="text" class="form-control sidebar-search ltr"
                  placeholder="{{ __('Search Menu Here...') }}">
              </div>
            </form>
          </div>
        </div>

        {{-- dashboard --}}
        <li class="nav-item @if (request()->routeIs('vendor.dashboard')) active @endif">
          <a href="{{ route('vendor.dashboard') }}">
            <i class="la flaticon-paint-palette"></i>
            <p>{{ __('Dashboard') }}</p>
          </a>
        </li>

        {{-- Listing management --}}

        <li
          class="nav-item @if (request()->routeIs('vendor.listing_management.listing')) active 
           @elseif (request()->routeIs('vendor.listing_management.create_listing')) active 
            @elseif (request()->routeIs('vendor.listing_management.listing.products')) active 
            @elseif (request()->routeIs('vendor.listing_management.listing.faq')) active 
            @elseif (request()->routeIs('vendor.listing_management.listing.business_hours')) active 
            @elseif (request()->routeIs('vendor.listing_management.listing.plugins')) active 
            @elseif (request()->routeIs('vendor.listing_management.manage_additional_specifications')) active 
            @elseif (request()->routeIs('vendor.listing_management.manage_social_link')) active 
            @elseif (request()->routeIs('vendor.listing_management.create_Product')) active 
            @elseif (request()->routeIs('vendor.listing_management.edit_product')) active 
            @elseif (request()->routeIs('vendor.listing_management.edit_listing')) active @endif">
          <a data-toggle="collapse" href="#listingManagement">
            <i class="fas fa-building"></i>
            <p>{{ __('Listing Managements') }}</p>
            <span class="caret"></span>
          </a>

          <div id="listingManagement"
            class="collapse 
              @if (request()->routeIs('vendor.listing_management.listing')) show 
              @elseif (request()->routeIs('vendor.listing_management.create_listing')) show 
              @elseif (request()->routeIs('vendor.listing_management.listing.products')) show 
              @elseif (request()->routeIs('vendor.listing_management.listing.faq')) show 
              @elseif (request()->routeIs('vendor.listing_management.listing.business_hours')) show 
              @elseif (request()->routeIs('vendor.listing_management.listing.plugins')) show 
              @elseif (request()->routeIs('vendor.listing_management.create_Product')) show 
              @elseif (request()->routeIs('vendor.listing_management.manage_additional_specifications')) show 
              @elseif (request()->routeIs('vendor.listing_management.manage_social_link')) show 
              @elseif (request()->routeIs('vendor.listing_management.edit_product')) show 
              @elseif (request()->routeIs('vendor.listing_management.edit_listing')) show @endif">
            <ul class="nav nav-collapse">

              <li class="{{ request()->routeIs('vendor.listing_management.create_listing') ? 'active' : '' }}">
                <a href="{{ route('vendor.listing_management.create_listing', ['language' => $defaultLang->code]) }}">
                  <span class="sub-item">{{ __('Add Listing') }}</span>
                </a>
              </li>

              <li
                class=" @if (request()->routeIs('vendor.listing_management.listing')) active
                   @elseif (request()->routeIs('vendor.listing_management.edit_listing')) active 
                   @elseif (request()->routeIs('vendor.listing_management.listing.products')) active 
                   @elseif (request()->routeIs('vendor.listing_management.listing.business_hours')) active 
                   @elseif (request()->routeIs('vendor.listing_management.listing.faq')) active 
                   @elseif (request()->routeIs('vendor.listing_management.listing.plugins')) active 
                   @elseif (request()->routeIs('vendor.listing_management.create_Product')) active 
                   @elseif (request()->routeIs('vendor.listing_management.manage_social_link')) active 
                   @elseif (request()->routeIs('vendor.listing_management.manage_additional_specifications')) active 
                   @elseif (request()->routeIs('vendor.listing_management.edit_product')) active @endif">
                <a href="{{ route('vendor.listing_management.listing', ['language' => $defaultLang->code]) }}">
                  <span class="sub-item">{{ __('Manage Listings') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- End Listing management --}}

        @php
          $support_status = DB::table('support_ticket_statuses')->first();
        @endphp
        @if ($support_status->support_ticket_status == 'active')
          {{-- Support Ticket --}}
          <li
            class="nav-item @if (request()->routeIs('vendor.support_tickets')) active
            @elseif (request()->routeIs('vendor.support_tickets.message')) active
            @elseif (request()->routeIs('vendor.support_ticket.create')) active @endif">
            <a data-toggle="collapse" href="#support_ticket">
              <i class="la flaticon-web-1"></i>
              <p>{{ __('Support Tickets') }}</p>
              <span class="caret"></span>
            </a>

            <div id="support_ticket"
              class="collapse
              @if (request()->routeIs('vendor.support_tickets')) show
              @elseif (request()->routeIs('vendor.support_tickets.message')) show
              @elseif (request()->routeIs('vendor.support_ticket.create')) show @endif">
              <ul class="nav nav-collapse">

                <li
                  class="{{ request()->routeIs('vendor.support_tickets') && empty(request()->input('status')) ? 'active' : '' }}">
                  <a href="{{ route('vendor.support_tickets') }}">
                    <span class="sub-item">{{ __('All Tickets') }}</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('vendor.support_ticket.create') ? 'active' : '' }}">
                  <a href="{{ route('vendor.support_ticket.create') }}">
                    <span class="sub-item">{{ __('Add a Ticket') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif
        <li class="nav-item @if (request()->routeIs('vendor.edit.profile')) active @endif">
          <a href="{{ route('vendor.edit.profile') }}">
            <i class="fal fa-user-edit"></i>
            <p>{{ __('Edit Profile') }}</p>
          </a>
        </li>
        <li class="nav-item  @if (request()->routeIs('vendor.email_setting.mail_to_admin')) active @endif">
          <a href="{{ route('vendor.email_setting.mail_to_admin') }}">
            <i class="far fa-envelope"></i>
            <p>{{ __('Recipient mail') }}</p>
          </a>
        </li>
        <li class="nav-item @if (request()->routeIs('vendor.change_password')) active @endif">
          <a href="{{ route('vendor.change_password') }}">
            <i class="fal fa-key"></i>
            <p>{{ __('Change Password') }}</p>
          </a>
        </li>

        <li class="nav-item @if (request()->routeIs('vendor.logout')) active @endif">
          <a href="{{ route('vendor.logout') }}">
            <i class="fal fa-sign-out"></i>
            <p>{{ __('Logout') }}</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
