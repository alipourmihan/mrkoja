{{-- fontawesome css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/all.min.css') }}">

{{-- fontawesome icon picker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}">

{{--  icon picker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/fonts.min.css') }}">




{{-- bootstrap css --}}

@if(session()->get('currentLangCode') == 'fa' || app()->getLocale() == 'fa')
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
@endif


{{-- bootstrap tags-input css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}">

{{-- jQuery-ui css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">


{{-- dropzone css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.min.css') }}">

{{-- atlantis css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/atlantis.css') }}">

{{-- select2 css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">

{{-- admin-main css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/admin-main.css') }}">

{{-- RTL css (loaded conditionally) --}}
@if(session()->get('currentLangCode') == 'fa' || app()->getLocale() == 'fa')
<link rel="stylesheet" href="{{ asset('vendor/pdatepicker/css/pdatepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bdatepicker4/src/jquery.md.bootstrap.datetimepicker.style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/vazir-matn.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/rtl.css') }}">

@endif
