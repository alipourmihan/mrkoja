<!DOCTYPE html>
<html dir="{{ session()->get('currentLangCode') == 'en' || app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}" lang="{{ session()->get('currentLangCode') ?? app()->getLocale() }}">

<head>
  {{-- required meta tags --}}
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  {{-- title --}}
  <title>{{ __('Admin Login') . ' | ' . $websiteInfo->website_title }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">

  {{-- bootstrap css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">

  {{-- login css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-login.css') }}">
  <style>
    :root {
      --matn: 'IRANYekan', sans-serif;
    }
  </style>
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
  {{-- login form start --}}
  <div class="login-page">
    @if (!empty($websiteInfo->logo))
      <div class="text-center mb-4">
        <img class="login-logo" src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="logo">
      </div>
    @endif

    <div class="form">
   
    
      @if (session()->has('alert'))
        <div class="alert alert-danger fade show" role="alert">
          <strong>{{ session('alert') }}</strong>
        </div>
      @endif

      <form class="login-form" action="{{ route('admin.auth') }}" method="POST">
        @csrf
        <input type="text" name="username" placeholder="{{ __('Enter Username') }}" />
        @if ($errors->has('username'))
          <p class="text-danger text-right">{{ $errors->first('username') }}</p>
        @endif

        <input type="password" name="password" placeholder="{{ __('Enter Password') }}" />
        @if ($errors->has('password'))
          <p class="text-danger text-right">{{ $errors->first('password') }}</p>
        @endif

        <button type="submit" class="w-100">{{ __('Login') }}</button>
      </form>

      <a class="forget-link" href="{{ route('admin.forget_password') }}">
        {{ __('Forget Password or Username?') }}
      </a>
    </div>
  </div>
  {{-- login form end --}}


  {{-- jQuery --}}
  <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>

  {{-- popper js --}}
  <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>

  {{-- bootstrap js --}}
  <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
</body>

</html>
