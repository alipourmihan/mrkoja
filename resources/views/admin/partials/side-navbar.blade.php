<div class="sidebar sidebar-style-2"
  data-background-color="{{ $settings->admin_theme_version == 'light' ? 'white' : 'dark2' }}">
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <div class="user">
        <div class="avatar-sm float-left mr-2">
          @if (Auth::guard('admin')->user()->image != null)
            <img src="{{ asset('assets/img/admins/' . Auth::guard('admin')->user()->image) }}" alt="Admin Image"
              class="avatar-img rounded-circle">
          @else
            <img src="{{ asset('assets/img/blank_user.jpg') }}" alt="" class="avatar-img rounded-circle">
          @endif
        </div>

        <div class="info">
          <a data-toggle="collapse" href="#adminProfileMenu" aria-expanded="true">
            <span>
              {{ Auth::guard('admin')->user()->first_name }}

              @if (is_null($roleInfo))
                <span class="user-level">{{ __('Super Admin') }}</span>
              @else
                <span class="user-level">{{ $roleInfo->name }}</span>
              @endif

              <span class="caret"></span>
            </span>
          </a>

          <div class="clearfix"></div>

          <div class="collapse in" id="adminProfileMenu">
            <ul class="nav">
              <li>
                <a href="{{ route('admin.edit_profile') }}">
                  <span class="link-collapse">{{ __('Edit Profile') }}</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.change_password') }}">
                  <span class="link-collapse">{{ __('Change Password') }}</span>
                </a>
              </li>

              <li>
                <a href="{{ route('admin.logout') }}">
                  <span class="link-collapse">{{ __('Logout') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      @php
        if (!is_null($roleInfo)) {
            $rolePermissions = json_decode($roleInfo->permissions);
        }
      @endphp

      <ul class="nav nav-primary">
        {{-- search --}}
        <div class="row mb-3">
          <div class="col-12">
            <form action="">
              <div class="form-group py-0">
                <input name="term" type="text" class="form-control sidebar-search ltr"
                  placeholder="{{ __('Search Menu Here...') }}">
              </div>
            </form>
          </div>
        </div>

        {{-- dashboard --}}
        <li class="nav-item @if (request()->routeIs('admin.dashboard')) active @endif">
          <a href="{{ route('admin.dashboard') }}">
            <i class="la flaticon-paint-palette"></i>
            <p>{{ __('Dashboard') }}</p>
          </a>
        </li>
        {{-- Listing specifications --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Listing Specifications', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.listing_specification.categories')) active 
            @elseif (request()->routeIs('admin.listing_specification.location.city')) active 
            @elseif (request()->routeIs('admin.listing_specification.aminites')) active 
            @elseif (request()->routeIs('admin.listing_specification.location.states')) active 
            @elseif (request()->routeIs('admin.listing_specification.location.countries')) active
            @elseif (request()->routeIs('admin.listing_specification.location.neighborhood')) active @endif">
            <a data-toggle="collapse" href="#listingSpecification">
              <i class="far fa-file-alt"></i>
              <p>{{ __('Listing Specifications') }}</p>
              <span class="caret"></span>
            </a>

            <div id="listingSpecification"
              class="collapse 
              @if (request()->routeIs('admin.listing_specification.categories')) show 
              @elseif (request()->routeIs('admin.listing_specification.aminites')) show 
              @elseif (request()->routeIs('admin.listing_specification.location.states')) show 
              @elseif (request()->routeIs('admin.listing_specification.location.city')) show 
              @elseif (request()->routeIs('admin.listing_specification.location.countries')) show
              @elseif (request()->routeIs('admin.listing_specification.location.neighborhood')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.listing_specification.categories') ? 'active' : '' }}">
                  <a href="{{ route('admin.listing_specification.categories', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Categories') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.listing_specification.aminites') ? 'active' : '' }}">
                  <a href="{{ route('admin.listing_specification.aminites', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Aminites') }}</span>
                  </a>
                </li>
                <li class="submenu">
                  <a data-toggle="collapse"
                    href="#set-location"aria-expanded="{{ request()->routeIs('admin.listing_specification.location.countries') || request()->routeIs('admin.listing_specification.location.city') || request()->routeIs('admin.listing_specification.location.states') || request()->routeIs('admin.listing_specification.location.neighborhood') ? 'true' : 'false' }}">
                    <span class="sub-item">{{ __('Location') }}</span>
                    <span class="caret"></span>
                  </a>

                  <div id="set-location"
                    class="collapse 
                    @if (request()->routeIs('admin.listing_specification.location.city')) show
                    @elseif (request()->routeIs('admin.listing_specification.location.states')) show
                    @elseif (request()->routeIs('admin.listing_specification.location.neighborhood')) show @endif">
                    <ul class="nav nav-collapse subnav">
                      <li
                        class="{{ request()->routeIs('admin.listing_specification.location.states') ? 'active' : '' }}">
                        <a
                          href="{{ route('admin.listing_specification.location.states', ['language' => $defaultLang->code]) }}">
                          <span class="sub-item">{{ __('States') }}</span>
                        </a>
                      </li>

                      <li
                        class="{{ request()->routeIs('admin.listing_specification.location.city') ? 'active' : '' }}">
                        <a
                          href="{{ route('admin.listing_specification.location.city', ['language' => $defaultLang->code]) }}">
                          <span class="sub-item">{{ __('Cities') }}</span>
                        </a>
                      </li>

                      <li
                        class="{{ request()->routeIs('admin.listing_specification.location.neighborhood') ? 'active' : '' }}">
                        <a
                          href="{{ route('admin.listing_specification.location.neighborhood', ['language' => $defaultLang->code]) }}">
                          <span class="sub-item">محلات</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- Category Location Pages --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Listing Specifications', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.category_location_pages.*')) active @endif">
            <a href="{{ route('admin.category_location_pages.index') }}">
              <i class="fas fa-file-alt"></i>
              <p>صفحات دسته‌بندی و مکان</p>
            </a>
          </li>
        @endif

        {{-- Listing management --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Listings Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.listing_management.listing')) active 
            @elseif (request()->routeIs('admin.listing_management.select_vendor')) active 
            @elseif (request()->routeIs('admin.listing_management.listing.products')) active 
            @elseif (request()->routeIs('admin.listing_management.listing.faq')) active 
            @elseif (request()->routeIs('admin.listing_management.listing.business_hours')) active 
            @elseif (request()->routeIs('admin.listing_management.listing.plugins')) active 
            @elseif (request()->routeIs('admin.listing_management.listing.select_vendor')) active 
            @elseif (request()->routeIs('admin.listing_management.create_Product')) active 
            @elseif (request()->routeIs('admin.listing_management.settings')) active 
            @elseif (request()->routeIs('admin.listing_management.manage_social_link')) active 
            @elseif (request()->routeIs('admin.listing_management.manage_additional_specification_link')) active 
            @elseif (request()->routeIs('admin.listing_management.create_listing')) active 
            @elseif (request()->routeIs('admin.listing_management.edit_product')) active 
            @elseif (request()->routeIs('admin.listing_management.edit_listing')) active @endif">
            <a data-toggle="collapse" href="#listingManagement">
              <i class="fas fa-building"></i>
              <p>{{ __('Listings Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="listingManagement"
              class="collapse 
              @if (request()->routeIs('admin.listing_management.listing')) show 
              @elseif (request()->routeIs('admin.listing_management.select_vendor')) show 
              @elseif (request()->routeIs('admin.listing_management.listing.products')) show 
              @elseif (request()->routeIs('admin.listing_management.listing.faq')) show 
              @elseif (request()->routeIs('admin.listing_management.create_listing')) show 
              @elseif (request()->routeIs('admin.listing_management.listing.business_hours')) show 
              @elseif (request()->routeIs('admin.listing_management.listing.plugins')) show 
              @elseif (request()->routeIs('admin.listing_management.create_Product')) show 
              @elseif (request()->routeIs('admin.listing_management.select_vendor')) show 
              @elseif (request()->routeIs('admin.listing_management.manage_social_link')) show 
              @elseif (request()->routeIs('admin.listing_management.manage_additional_specification_link')) show 
              @elseif (request()->routeIs('admin.listing_management.edit_product')) show 
              @elseif (request()->routeIs('admin.listing_management.settings')) show 
              @elseif (request()->routeIs('admin.listing_management.edit_listing')) show @endif">
              <ul class="nav nav-collapse">

                <li class=" @if (request()->routeIs('admin.listing_management.settings')) active @endif">
                  <a href="{{ route('admin.listing_management.settings') }}">
                    <span class="sub-item">{{ __('Settings') }}</span>
                  </a>
                </li>
                <li
                  class=" @if (request()->routeIs('admin.listing_management.select_vendor')) active
                   @elseif (request()->routeIs('admin.listing_management.create_listing')) active @endif">
                  <a href="{{ route('admin.listing_management.select_vendor', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Add Listing') }}</span>
                  </a>
                </li>
                <li
                  class=" @if (request()->routeIs('admin.listing_management.listing')) active
                   @elseif (request()->routeIs('admin.listing_management.edit_listing')) active 
                   @elseif (request()->routeIs('admin.listing_management.listing.products')) active 
                   @elseif (request()->routeIs('admin.listing_management.manage_social_link')) active 
                   @elseif (request()->routeIs('admin.listing_management.manage_additional_specification_link')) active 
                   @elseif (request()->routeIs('admin.listing_management.listing.business_hours')) active 
                   @elseif (request()->routeIs('admin.listing_management.listing.faq')) active 
                   @elseif (request()->routeIs('admin.listing_management.listing.plugins')) active 
                   @elseif (request()->routeIs('admin.listing_management.create_Product')) active 
                   @elseif (request()->routeIs('admin.listing_management.edit_product')) active @endif">
                  <a href="{{ route('admin.listing_management.listing', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Manage Listings') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- Featured Listing  --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Featured Listings', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.featured_listing.all_request')) active 
            @elseif (request()->routeIs('admin.featured_listing.pending_request')) active 
            @elseif (request()->routeIs('admin.featured_listing.approved_request')) active  
            @elseif (request()->routeIs('admin.featured_listing.charge')) active  
            @elseif (request()->routeIs('admin.featured_listing.rejected_request')) active @endif">
            <a data-toggle="collapse" href="#featured-listing">
              <i class="fas fa-money-bill"></i>
              <p>{{ __('Featured Listings') }}</p>
              <span class="caret"></span>
            </a>

            <div id="featured-listing"
              class="collapse 
              @if (request()->routeIs('admin.featured_listing.all_request')) show 
              @elseif (request()->routeIs('admin.featured_listing.pending_request')) show 
              @elseif (request()->routeIs('admin.featured_listing.approved_request')) show 
              @elseif (request()->routeIs('admin.featured_listing.charge')) show 
              @elseif (request()->routeIs('admin.featured_listing.rejected_request')) show @endif">
              <ul class="nav nav-collapse">

                <li class="{{ request()->routeIs('admin.featured_listing.charge') ? 'active' : '' }}">
                  <a href="{{ route('admin.featured_listing.charge') }}">
                    <span class="sub-item">{{ __('Charges') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.featured_listing.all_request') ? 'active' : '' }}">
                  <a href="{{ route('admin.featured_listing.all_request', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('All Requests') }}</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('admin.featured_listing.pending_request') ? 'active' : '' }}">
                  <a href="{{ route('admin.featured_listing.pending_request', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Pending Requests') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.featured_listing.approved_request') ? 'active' : '' }}">
                  <a
                    href="{{ route('admin.featured_listing.approved_request', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Aproved Requests') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.featured_listing.rejected_request') ? 'active' : '' }}">
                  <a
                    href="{{ route('admin.featured_listing.rejected_request', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Rejected Requests') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- package management --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Package Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.package.settings')) active 
            @elseif (request()->routeIs('admin.package.index')) active 
            @elseif (request()->routeIs('admin.package.edit')) active @endif">
            <a data-toggle="collapse" href="#packageManagement">
              <i class="fal fa-receipt"></i>
              <p>{{ __('Package Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="packageManagement"
              class="collapse 
              @if (request()->routeIs('admin.package.settings')) show 
              @elseif (request()->routeIs('admin.package.index')) show 
              @elseif (request()->routeIs('admin.package.edit')) show @endif">
              <ul class="nav nav-collapse">

                <li class="{{ request()->routeIs('admin.package.settings') ? 'active' : '' }}">
                  <a href="{{ route('admin.package.settings', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Settings') }}</span>
                  </a>
                </li>
                <li
                  class=" @if (request()->routeIs('admin.package.index')) active 
            @elseif (request()->routeIs('admin.package.edit')) active @endif">
                  <a href="{{ route('admin.package.index', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Packages') }}</span>
                  </a>
                </li>

              </ul>
            </div>
          </li>
        @endif

        {{-- menu builder --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Menu Builder', $rolePermissions)))
          <li class="nav-item @if (request()->routeIs('admin.menu_builder')) active @endif">
            <a href="{{ route('admin.menu_builder', ['language' => $defaultLang->code]) }}">
              <i class="fal fa-bars"></i>
              <p>{{ __('Menu Builder') }}</p>
            </a>
          </li>
        @endif

        {{-- Subscription Log --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Subscription Log', $rolePermissions)))
          <li class="nav-item @if (request()->routeIs('admin.payment-log.index')) active @endif">
            <a href="{{ route('admin.payment-log.index') }}">
              <i class="fas fa-list-ol"></i>
              <p>{{ __('Subscription Log') }}</p>
            </a>
          </li>
        @endif




        {{-- user --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('User Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.user_management.registered_users')) active 
            @elseif (request()->routeIs('admin.user_management.registered_user.create')) active 
            @elseif (request()->routeIs('admin.user_management.registered_user.edit')) active 
            @elseif (request()->routeIs('admin.user_management.user.change_password')) active 
            @elseif (request()->routeIs('admin.user_management.subscribers')) active 
            @elseif (request()->routeIs('admin.user_management.mail_for_subscribers')) active 
            @elseif (request()->routeIs('admin.user_management.push_notification.settings')) active 
            @elseif (request()->routeIs('admin.user_management.push_notification.notification_for_visitors')) active @endif">
            <a data-toggle="collapse" href="#user">
              <i class="la flaticon-users"></i>
              <p>{{ __('Users Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="user"
              class="collapse 
              @if (request()->routeIs('admin.user_management.registered_users')) show 
              @elseif (request()->routeIs('admin.user_management.registered_user.create')) show 
              @elseif (request()->routeIs('admin.user_management.registered_user.edit')) show 
              @elseif (request()->routeIs('admin.user_management.user.change_password')) show 
              @elseif (request()->routeIs('admin.user_management.subscribers')) show 
              @elseif (request()->routeIs('admin.user_management.mail_for_subscribers')) show 
              @elseif (request()->routeIs('admin.user_management.push_notification.settings')) show 
              @elseif (request()->routeIs('admin.user_management.push_notification.notification_for_visitors')) show @endif">
              <ul class="nav nav-collapse">
                <li
                  class="@if (request()->routeIs('admin.user_management.registered_users')) active 
                  @elseif (request()->routeIs('admin.user_management.user.change_password')) active
@elseif (request()->routeIs('admin.user_management.registered_user.edit'))
active @endif
                  ">
                  <a href="{{ route('admin.user_management.registered_users') }}">
                    <span class="sub-item">{{ __('Registered Users') }}</span>
                  </a>
                </li>

                <li class="@if (request()->routeIs('admin.user_management.registered_user.create')) active @endif
                  ">
                  <a href="{{ route('admin.user_management.registered_user.create') }}">
                    <span class="sub-item">{{ __('Add User') }}</span>
                  </a>
                </li>

                <li
                  class="@if (request()->routeIs('admin.user_management.subscribers')) active 
                  @elseif (request()->routeIs('admin.user_management.mail_for_subscribers')) active @endif">
                  <a href="{{ route('admin.user_management.subscribers') }}">
                    <span class="sub-item">{{ __('Subscribers') }}</span>
                  </a>
                </li>

                <li class="submenu">
                  <a data-toggle="collapse" href="#push_notification">
                    <span class="sub-item">{{ __('Push Notification') }}</span>
                    <span class="caret"></span>
                  </a>

                  <div id="push_notification"
                    class="collapse 
                    @if (request()->routeIs('admin.user_management.push_notification.settings')) show 
                    @elseif (request()->routeIs('admin.user_management.push_notification.notification_for_visitors')) show @endif">
                    <ul class="nav nav-collapse subnav">
                      <li
                        class="{{ request()->routeIs('admin.user_management.push_notification.settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.user_management.push_notification.settings') }}">
                          <span class="sub-item">{{ __('Settings') }}</span>
                        </a>
                      </li>

                      <li
                        class="{{ request()->routeIs('admin.user_management.push_notification.notification_for_visitors') ? 'active' : '' }}">
                        <a href="{{ route('admin.user_management.push_notification.notification_for_visitors') }}">
                          <span class="sub-item">{{ __('Send Notification') }}</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- vendor --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Vendors Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.vendor_management.registered_vendor')) active
            @elseif (request()->routeIs('admin.vendor_management.add_vendor')) active
            @elseif (request()->routeIs('admin.vendor_management.vendor_details')) active
            @elseif (request()->routeIs('admin.edit_management.vendor_edit')) active
            @elseif (request()->routeIs('admin.vendor_management.settings')) active
            @elseif (request()->routeIs('admin.vendor_management.vendor.change_password')) active @endif">
            <a data-toggle="collapse" href="#vendor">
              <i class="la flaticon-users"></i>
              <p>{{ __('Vendors Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="vendor"
              class="collapse
              @if (request()->routeIs('admin.vendor_management.registered_vendor')) show
              @elseif (request()->routeIs('admin.vendor_management.vendor_details')) show
              @elseif (request()->routeIs('admin.edit_management.vendor_edit')) show
              @elseif (request()->routeIs('admin.vendor_management.add_vendor')) show
              @elseif (request()->routeIs('admin.vendor_management.settings')) show
              @elseif (request()->routeIs('admin.vendor_management.vendor.change_password')) show @endif">
              <ul class="nav nav-collapse">
                <li class="@if (request()->routeIs('admin.vendor_management.settings')) active @endif">
                  <a href="{{ route('admin.vendor_management.settings') }}">
                    <span class="sub-item">{{ __('Settings') }}</span>
                  </a>
                </li>
                <li
                  class="@if (request()->routeIs('admin.vendor_management.registered_vendor')) active
                  @elseif (request()->routeIs('admin.vendor_management.vendor_details')) active
                  @elseif (request()->routeIs('admin.edit_management.vendor_edit')) active
                  @elseif (request()->routeIs('admin.vendor_management.vendor.change_password')) active @endif">
                  <a href="{{ route('admin.vendor_management.registered_vendor') }}">
                    <span class="sub-item">{{ __('Registered vendors') }}</span>
                  </a>
                </li>
                <li class="@if (request()->routeIs('admin.vendor_management.add_vendor')) active @endif">
                  <a href="{{ route('admin.vendor_management.add_vendor') }}">
                    <span class="sub-item">{{ __('Add vendor') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- Support Tickets --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Support Tickets', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.support_ticket.setting')) active
            @elseif (request()->routeIs('admin.support_tickets')) active
            @elseif (request()->routeIs('admin.support_tickets.message')) active active
            @elseif (request()->routeIs('admin.user_management.push_notification.notification_for_visitors')) active @endif">
            <a data-toggle="collapse" href="#support_ticket">
              <i class="la flaticon-web-1"></i>
              <p>{{ __('Support Tickets') }}</p>
              <span class="caret"></span>
            </a>

            <div id="support_ticket"
              class="collapse
              @if (request()->routeIs('admin.support_ticket.setting')) show
              @elseif (request()->routeIs('admin.support_tickets')) show
              @elseif (request()->routeIs('admin.support_tickets.message')) show @endif">
              <ul class="nav nav-collapse">
                <li class="@if (request()->routeIs('admin.support_ticket.setting')) active @endif">
                  <a href="{{ route('admin.support_ticket.setting') }}">
                    <span class="sub-item">{{ __('Settings') }}</span>
                  </a>
                </li>
                <li
                  class="{{ request()->routeIs('admin.support_tickets') && empty(request()->input('status')) ? 'active' : '' }}">
                  <a href="{{ route('admin.support_tickets') }}">
                    <span class="sub-item">{{ __('All Tickets') }}</span>
                  </a>
                </li>
                <li
                  class="{{ request()->routeIs('admin.support_tickets') && request()->input('status') == 1 ? 'active' : '' }}">
                  <a href="{{ route('admin.support_tickets', ['status' => 1]) }}">
                    <span class="sub-item">{{ __('Pending Tickets') }}</span>
                  </a>
                </li>
                <li
                  class="{{ request()->routeIs('admin.support_tickets') && request()->input('status') == 2 ? 'active' : '' }}">
                  <a href="{{ route('admin.support_tickets', ['status' => 2]) }}">
                    <span class="sub-item">{{ __('Open Tickets') }}</span>
                  </a>
                </li>
                <li
                  class="{{ request()->routeIs('admin.support_tickets') && request()->input('status') == 3 ? 'active' : '' }}">
                  <a href="{{ route('admin.support_tickets', ['status' => 3]) }}">
                    <span class="sub-item">{{ __('Closed Tickets') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- home page --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Home Page', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.home_page.hero_section')) active 
            @elseif (request()->routeIs('admin.home_page.location_section')) active 
            @elseif (request()->routeIs('admin.home_page.about_section')) active 
            @elseif (request()->routeIs('admin.home_page.category_section')) active 
            @elseif (request()->routeIs('admin.home_page.listing_section')) active 
            @elseif (request()->routeIs('admin.home_page.video_section')) active 
            @elseif (request()->routeIs('admin.home_page.package_section')) active 
            @elseif (request()->routeIs('admin.home_page.banners')) active 
            @elseif (request()->routeIs('admin.home_page.work_process_section')) active 
            @elseif (request()->routeIs('admin.home_page.feature_section')) active 
            @elseif (request()->routeIs('admin.home_page.counter_section')) active 
            @elseif (request()->routeIs('admin.home_page.testimonial_section')) active 
            @elseif (request()->routeIs('admin.home_page.product_section')) active 
            @elseif (request()->routeIs('admin.home_page.call_to_action_section')) active 
            @elseif (request()->routeIs('admin.home_page.blog_section')) active 
            @elseif (request()->routeIs('admin.home_page.section_customization')) active 
            @elseif (request()->routeIs('admin.home_page.partners')) active @endif">
            <a data-toggle="collapse" href="#home_page">
              <i class="la flaticon-home"></i>
              <p>{{ __('Home Page') }}</p>
              <span class="caret"></span>
            </a>

            <div id="home_page"
              class="collapse 
              @if (request()->routeIs('admin.home_page.hero_section')) show 
              @elseif (request()->routeIs('admin.home_page.location_section')) show 
              @elseif (request()->routeIs('admin.home_page.about_section')) show 
              @elseif (request()->routeIs('admin.home_page.banners')) show 
              @elseif (request()->routeIs('admin.home_page.category_section')) show 
              @elseif (request()->routeIs('admin.home_page.listing_section')) show 
              @elseif (request()->routeIs('admin.home_page.video_section')) show 
              @elseif (request()->routeIs('admin.home_page.package_section')) show 
              @elseif (request()->routeIs('admin.home_page.work_process_section')) show 
              @elseif (request()->routeIs('admin.home_page.feature_section')) show 
              @elseif (request()->routeIs('admin.home_page.counter_section')) show 
              @elseif (request()->routeIs('admin.home_page.testimonial_section')) show 
              @elseif (request()->routeIs('admin.home_page.product_section')) show 
              @elseif (request()->routeIs('admin.home_page.call_to_action_section')) show 
              @elseif (request()->routeIs('admin.home_page.blog_section')) show 
              @elseif (request()->routeIs('admin.home_page.section_customization')) show 
              @elseif (request()->routeIs('admin.home_page.partners')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.home_page.hero_section') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.hero_section', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{__('Hero Section')}}</span>
                  </a>
                </li>
                @if ($settings->theme_version == 3)
                  <li class="{{ request()->routeIs('admin.home_page.location_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.location_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Location Section')}}</span>
                    </a>
                  </li>
                @endif

                <li class="{{ request()->routeIs('admin.home_page.category_section') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.category_section', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{__('Category Section')}}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.home_page.listing_section') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.listing_section', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{__('Featured Listing Section')}}</span>
                  </a>
                </li>

                @if ($settings->theme_version == 2 || $settings->theme_version == 4)
                  <li class="{{ request()->routeIs('admin.home_page.video_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.video_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Video Section')}}</span>
                  </a>
                </li>
                @endif
                @if ($settings->theme_version == 2 || $settings->theme_version == 3)
                  <li class="{{ request()->routeIs('admin.home_page.testimonial_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.testimonial_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Testimonial Section')}}</span>
                    </a>
                  </li>
                @endif

                @if ($settings->theme_version == 1 || $settings->theme_version == 4)
                  <li class="{{ request()->routeIs('admin.home_page.work_process_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.work_process_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Work Process Section')}}</span>
                  </a>
                </li>
                @endif
                @if ($settings->theme_version == 1 || $settings->theme_version == 3)
                  <li class="{{ request()->routeIs('admin.home_page.counter_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.counter_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Counter Section')}}</span>
                    </a>
                  </li>
                @endif

                <li class="{{ request()->routeIs('admin.home_page.package_section') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.package_section', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{__('Package Section')}}</span>
                  </a>
                </li>

                @if ($settings->theme_version == 1 || $settings->theme_version == 4)
                  <li class="{{ request()->routeIs('admin.home_page.call_to_action_section') ? 'active' : '' }}">
                    <a
                      href="{{ route('admin.home_page.call_to_action_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Call To Action Section')}}</span>
                  </a>
                </li>
                @endif
                @if ($settings->theme_version == 2)
                  <li class="{{ request()->routeIs('admin.home_page.feature_section') ? 'active' : '' }}">
                    <a href="{{ route('admin.home_page.feature_section', ['language' => $defaultLang->code]) }}">
                      <span class="sub-item">{{__('Latest Listing Section')}}</span>
                    </a>
                  </li>
                @endif


                <li class="{{ request()->routeIs('admin.home_page.blog_section') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.blog_section', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{__('Blog Section')}}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.home_page.section_customization') ? 'active' : '' }}">
                  <a href="{{ route('admin.home_page.section_customization') }}">
                    <span class="sub-item">{{__('Section Show/Hide')}}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif



        {{-- footer --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Footer', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.footer.logo_and_image')) active 
            @elseif (request()->routeIs('admin.footer.content')) active 
            @elseif (request()->routeIs('admin.footer.quick_links')) active @endif">
            <a data-toggle="collapse" href="#footer">
              <i class="la flaticon-layers"></i>
              <p>{{ __('Footer') }}</p>
              <span class="caret"></span>
            </a>

            <div id="footer"
              class="collapse @if (request()->routeIs('admin.footer.logo_and_image')) show 
              @elseif (request()->routeIs('admin.footer.content')) show 
              @elseif (request()->routeIs('admin.footer.quick_links')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.footer.logo_and_image') ? 'active' : '' }}">
                  <a href="{{ route('admin.footer.logo_and_image') }}">
                    <span class="sub-item">{{ __('Logo & Image') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.footer.content') ? 'active' : '' }}">
                  <a href="{{ route('admin.footer.content', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Content') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.footer.quick_links') ? 'active' : '' }}">
                  <a href="{{ route('admin.footer.quick_links', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Quick Links') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- custom page --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Custom Pages', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.custom_pages')) active 
            @elseif (request()->routeIs('admin.custom_pages.create_page')) active 
            @elseif (request()->routeIs('admin.custom_pages.edit_page')) active @endif">
            <a href="{{ route('admin.custom_pages', ['language' => $defaultLang->code]) }}">
              <i class="la flaticon-file"></i>
              <p>{{ __('Custom Pages') }}</p>
            </a>
          </li>
        @endif

        {{-- blog --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Blog Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.blog_management.categories')) active 
            @elseif (request()->routeIs('admin.blog_management.blogs')) active 
            @elseif (request()->routeIs('admin.blog_management.create_blog')) active 
            @elseif (request()->routeIs('admin.blog_management.edit_blog')) active @endif">
            <a data-toggle="collapse" href="#blog">
              <i class="fal fa-blog"></i>
              <p>{{ __('Blog Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="blog"
              class="collapse 
              @if (request()->routeIs('admin.blog_management.categories')) show 
              @elseif (request()->routeIs('admin.blog_management.blogs')) show 
              @elseif (request()->routeIs('admin.blog_management.create_blog')) show 
              @elseif (request()->routeIs('admin.blog_management.edit_blog')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.blog_management.categories') ? 'active' : '' }}">
                  <a href="{{ route('admin.blog_management.categories', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Categories') }}</span>
                  </a>
                </li>

                <li
                  class="@if (request()->routeIs('admin.blog_management.blogs')) active 
                  @elseif (request()->routeIs('admin.blog_management.create_blog')) active 
                  @elseif (request()->routeIs('admin.blog_management.edit_blog')) active @endif">
                  <a href="{{ route('admin.blog_management.blogs', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Posts') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- faq --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('FAQ Management', $rolePermissions)))
          <li class="nav-item {{ request()->routeIs('admin.faq_management') ? 'active' : '' }}">
            <a href="{{ route('admin.faq_management', ['language' => $defaultLang->code]) }}">
              <i class="la flaticon-round"></i>
              <p>{{ __('FAQ Management') }}</p>
            </a>
          </li>
        @endif

        {{-- advertise --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Advertisements', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.advertise.settings')) active 
            @elseif (request()->routeIs('admin.advertise.all_advertisement')) active @endif">
            <a data-toggle="collapse" href="#customid">
              <i class="fab fa-buysellads"></i>
              <p>{{ __('Advertisements') }}</p>
              <span class="caret"></span>
            </a>

            <div id="customid"
              class="collapse @if (request()->routeIs('admin.advertise.settings')) show 
              @elseif (request()->routeIs('admin.advertise.all_advertisement')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.advertise.settings') ? 'active' : '' }}">
                  <a href="{{ route('admin.advertise.settings') }}">
                    <span class="sub-item">{{ __('Settings') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.advertise.all_advertisement') ? 'active' : '' }}">
                  <a href="{{ route('admin.advertise.all_advertisement') }}">
                    <span class="sub-item">{{ __('All Advertisements') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- announcement popup --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Announcement Popups', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.announcement_popups')) active 
            @elseif (request()->routeIs('admin.announcement_popups.select_popup_type')) active 
            @elseif (request()->routeIs('admin.announcement_popups.create_popup')) active 
            @elseif (request()->routeIs('admin.announcement_popups.edit_popup')) active @endif">
            <a href="{{ route('admin.announcement_popups', ['language' => $defaultLang->code]) }}">
              <i class="fal fa-bullhorn"></i>
              <p>{{ __('Announcement Popups') }}</p>
            </a>
          </li>
        @endif


        {{-- basic settings --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Basic Settings', $rolePermissions)))
          <li
            class="nav-item 
            @if (request()->routeIs('admin.basic_settings.contact_page')) active
            @elseif (request()->routeIs('admin.basic_settings.mail_from_admin')) active 
            @elseif (request()->routeIs('admin.basic_settings.mail_to_admin')) active 
            @elseif (request()->routeIs('admin.basic_settings.mail_templates')) active
            @elseif (request()->routeIs('admin.basic_settings.edit_mail_template')) active
            @elseif (request()->routeIs('admin.basic_settings.breadcrumb')) active 
            @elseif (request()->routeIs('admin.basic_settings.page_headings')) active
            @elseif (request()->routeIs('admin.basic_settings.plugins')) active
            @elseif (request()->routeIs('admin.basic_settings.seo')) active
            @elseif (request()->routeIs('admin.basic_settings.theme_and_home')) active
            @elseif (request()->routeIs('admin.basic_settings.maintenance_mode')) active
            @elseif (request()->routeIs('admin.basic_settings.general_settings')) active
            @elseif (request()->routeIs('admin.basic_settings.cookie_alert')) active 
            @elseif (request()->routeIs('admin.basic_settings.social_medias')) active @endif">
            <a data-toggle="collapse" href="#basic_settings">
              <i class="la flaticon-settings"></i>
              <p>{{ __('Basic Settings') }}</p>
              <span class="caret"></span>
            </a>

            <div id="basic_settings"
              class="collapse 
              @if (request()->routeIs('admin.basic_settings.contact_page')) show
              @elseif (request()->routeIs('admin.basic_settings.mail_from_admin')) show 
              @elseif (request()->routeIs('admin.basic_settings.mail_to_admin')) show 
              @elseif (request()->routeIs('admin.basic_settings.mail_templates')) show
              @elseif (request()->routeIs('admin.basic_settings.edit_mail_template')) show
              @elseif (request()->routeIs('admin.basic_settings.breadcrumb')) show 
              @elseif (request()->routeIs('admin.basic_settings.page_headings')) show
              @elseif (request()->routeIs('admin.basic_settings.plugins')) show
              @elseif (request()->routeIs('admin.basic_settings.seo')) show
              @elseif (request()->routeIs('admin.basic_settings.theme_and_home')) show
              @elseif (request()->routeIs('admin.basic_settings.maintenance_mode')) show
              @elseif (request()->routeIs('admin.basic_settings.cookie_alert')) show 
              @elseif (request()->routeIs('admin.basic_settings.general_settings')) show
              @elseif (request()->routeIs('admin.basic_settings.social_medias')) show @endif">
              <ul class="nav nav-collapse">
                <li class="{{ request()->routeIs('admin.basic_settings.general_settings') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.general_settings') }}">
                    <span class="sub-item">{{ __('General Settings') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.contact_page') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.contact_page') }}">
                    <span class="sub-item">{{ __('Contact Page') }}</span>
                  </a>
                </li>


                <li class="submenu">
                  <a data-toggle="collapse" href="#mail-settings"
                    aria-expanded="{{ request()->routeIs('admin.basic_settings.mail_from_admin') || request()->routeIs('admin.basic_settings.mail_to_admin') || request()->routeIs('admin.basic_settings.mail_templates') || request()->routeIs('admin.basic_settings.edit_mail_template') ? 'true' : 'false' }}">
                    <span class="sub-item">{{ __('Email Settings') }}</span>
                    <span class="caret"></span>
                  </a>

                  <div id="mail-settings"
                    class="collapse 
                    @if (request()->routeIs('admin.basic_settings.mail_from_admin')) show 
                    @elseif (request()->routeIs('admin.basic_settings.mail_to_admin')) show
                    @elseif (request()->routeIs('admin.basic_settings.mail_templates')) show
                    @elseif (request()->routeIs('admin.basic_settings.edit_mail_template')) show @endif">
                    <ul class="nav nav-collapse subnav">
                <li class="{{ request()->routeIs('admin.basic_settings.mail_from_admin') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.mail_from_admin') }}">
                    <span class="sub-item">{{ __('Mail From Admin') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.mail_to_admin') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.mail_to_admin') }}">
                    <span class="sub-item">{{ __('Mail To Admin') }}</span>
                  </a>
                </li>

                <li
                        class="@if (request()->routeIs('admin.basic_settings.mail_templates')) active 
                        @elseif (request()->routeIs('admin.basic_settings.edit_mail_template')) active @endif">
                        <a href="{{ route('admin.basic_settings.mail_templates') }}">
                          <span class="sub-item">{{ __('Mail Templates') }}</span>
                  </a>
                </li>
                    </ul>
                  </div>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.breadcrumb') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.breadcrumb') }}">
                    <span class="sub-item">{{ __('Breadcrumb') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.theme_and_home') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.theme_and_home') }}">
                    <span class="sub-item">{{ __('Theme & Home') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.page_headings') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.page_headings', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Page Headings') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.plugins') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.plugins') }}">
                    <span class="sub-item">{{ __('Plugins') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.seo') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.seo', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('SEO Informations') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.maintenance_mode') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.maintenance_mode') }}">
                    <span class="sub-item">{{ __('Maintenance Mode') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.cookie_alert') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.cookie_alert', ['language' => $defaultLang->code]) }}">
                    <span class="sub-item">{{ __('Cookie Alert') }}</span>
                  </a>
                </li>

                <li class="{{ request()->routeIs('admin.basic_settings.social_medias') ? 'active' : '' }}">
                  <a href="{{ route('admin.basic_settings.social_medias') }}">
                    <span class="sub-item">{{ __('Social Medias') }}  </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        {{-- admin --}}
        @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Admin Management', $rolePermissions)))
          <li
            class="nav-item @if (request()->routeIs('admin.admin_management.role_permissions')) active 
            @elseif (request()->routeIs('admin.admin_management.role.permissions')) active 
            @elseif (request()->routeIs('admin.admin_management.registered_admins')) active @endif">
            <a data-toggle="collapse" href="#admin">
              <i class="fal fa-users-cog"></i>
              <p>{{ __('Admin Management') }}</p>
              <span class="caret"></span>
            </a>

            <div id="admin"
              class="collapse 
              @if (request()->routeIs('admin.admin_management.role_permissions')) show 
              @elseif (request()->routeIs('admin.admin_management.role.permissions')) show 
              @elseif (request()->routeIs('admin.admin_management.registered_admins')) show @endif">
              <ul class="nav nav-collapse">
                <li
                  class="@if (request()->routeIs('admin.admin_management.role_permissions')) active 
                  @elseif (request()->routeIs('admin.admin_management.role.permissions')) active @endif">
                  <a href="{{ route('admin.admin_management.role_permissions') }}">
                    <span class="sub-item">{{ __('Roles & Permissions') }}</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('admin.admin_management.registered_admins') ? 'active' : '' }}">
                  <a href="{{ route('admin.admin_management.registered_admins') }}">
                    <span class="sub-item">{{ __('Admins') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

      </ul>
    </div>
  </div>
</div>
