<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin SEO Dashboard - Redirect to Vue.js admin panel
Route::get('/admin/seo', function () {
    return redirect('/admin#seo-management');
});
