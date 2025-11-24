<!DOCTYPE html>
<html dir="{{ session()->get('currentLangCode') == 'en' || app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}" lang="{{ session()->get('currentLangCode') ?? app()->getLocale() }}">

<head>
  {{-- required meta tags --}}
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  {{-- title --}}
  <title>{{ __('Forget Password') . ' | ' . $websiteInfo->website_title }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">

  {{-- bootstrap css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">

  {{-- atlantis css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/atlantis.css') }}">

  {{-- admin-login css also using for forget password --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-login.css') }}">

  {{-- forget password css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-forget-password.css') }}">
  
  {{-- RTL css (loaded conditionally) --}}
  @if(session()->get('currentLangCode') == 'fa' || app()->getLocale() == 'fa')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/vazir-matn.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/rtl.css') }}">
  @endif
  
  <style>
    .language-selector {
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
    }
    .language-selector select {
      padding: 8px 10px;
      border-radius: 5px;
      border: 1px solid #ddd;
      background-color: #f8f9fa;
      cursor: pointer;
    }
  </style>
</head>

<body>
  {{-- forget password form start --}}
  <div class="forget-page">
    @if (!empty($websiteInfo->logo))
      <div class="text-center">
        <img class="forget-logo" src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="logo">
      </div>
    @endif

    <div class="form">
      {{-- Language Selector --}}
      <div class="language-selector" style="display: flex; justify-content: center; gap: 20px;">
        <a href="{{ url('changelanguage/en') }}" class="{{ (session()->get('currentLangCode') == 'en' || app()->getLocale() == 'en') ? 'active' : '' }}" style="padding: 8px 15px; text-decoration: none; border: 1px solid #ddd; border-radius: 5px; color: #fff;">{{ __('English') }}</a>
        <a href="{{ url('changelanguage/fa') }}" class="{{ (session()->get('currentLangCode') == 'fa' || app()->getLocale() == 'fa') ? 'active' : '' }}" style="padding: 8px 15px; text-decoration: none; border: 1px solid #ddd; border-radius: 5px; color: #fff;">{{ __('فارسی') }}</a>
      </div>
      
      @if (session()->has('alert'))
        <div class="alert alert-danger fade show" role="alert">
          <strong>{{ session('alert') }}</strong>
        </div>
      @endif

      <form class="forget-password-form" action="{{ route('admin.mail_for_forget_password') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="{{ __('Enter Your Email') }}" value="{{ old('email') }}" />
        @if ($errors->has('email'))
          <p class="text-danger text-left">{{ $errors->first('email') }}</p>
        @endif

        <button type="submit" class="mt-2">{{ __('proceed') }}</button>
      </form>

      <a class="back-to-login" href="{{ route('admin.login') }}">
        &lt;&lt; {{ __('Back') }}
      </a>
    </div>
  </div>
  {{-- forget password form end --}}


  {{-- jQuery --}}
  <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>

  {{-- popper js --}}
  <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>

  {{-- bootstrap js --}}
  <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>

  {{-- bootstrap notify --}}
  <script src="{{ asset('assets/admin/js/bootstrap-notify.min.js') }}"></script>

  {{-- fonts and icons script --}}
  <script src="{{ asset('assets/admin/js/webfont.min.js') }}"></script>

  <script>
    "use strict";
    const baseUrl = "{{ url('/') }}";
  </script>
  {{-- fonts and icons script --}}
  <script src="{{ asset('assets/admin/js/admin-login.js') }}"></script>

  @if (session()->has('success'))
    <script>
      'use strict';
      var content = {};

      content.message = '{{ session('success') }}';
      content.title = 'موفق';
      content.icon = 'fa fa-bell';

      $.notify(content, {
        type: 'success',
        placement: {
          from: 'top',
          align: 'right'
        },
        showProgressbar: true,
        time: 1000,
        delay: 4000
      });
    </script>
  @endif

  @if (session()->has('warning'))
    <script>
      'use strict';
      var content = {};

      content.message = '{{ session('warning') }}';
      content.title = 'هشدار!';
      content.icon = 'fa fa-bell';

      $.notify(content, {
        type: 'warning',
        placement: {
          from: 'top',
          align: 'right'
        },
        showProgressbar: true,
        time: 1000,
        delay: 4000
      });
    </script>
  @endif
</body>
</html>
