<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Interface Routes
|--------------------------------------------------------------------------
*/




Route::get('/profile', 'FrontEnd\ProfileContoller@index')->name('profile');



Route::post('/push-notification/store-endpoint', 'FrontEnd\PushNotificationController@store');

// cron job for sending expiry mail
Route::get('/subcheck', 'CronJobController@expired')->name('cron.expired');

// Test route for language debugging
Route::get('/test-language', function() {
    $data = [
        'currentLangCode' => session('currentLangCode'),
        'currentLocaleCode' => session('currentLocaleCode'),
        'appLocale' => app()->getLocale(),
        'session_all' => session()->all(),
        'langtest' =>  __('Super Admin'),
    ];
  
    return response()->json($data);
});

Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    
    return "Cache cleared successfully!";
});

Route::get('/test-lang-page', function() {
    return view('test-lang');
});

Route::post('/store-subscriber', 'FrontEnd\MiscellaneousController@storeSubscriber')->name('store_subscriber');

Route::get('/offline', 'FrontEnd\HomeController@offline')->middleware('change.lang');

Route::middleware('change.lang')->group(function () {

  Route::get('/pricing', 'FrontEnd\HomeController@pricing')->name('frontend.pricing');
  Route::get('/faq', 'FrontEnd\FaqController@faq')->name('faq');

  Route::get('/', 'FrontEnd\HomeController@index')->name('index');

  Route::prefix('listings')->group(function () {
    Route::get('/', 'FrontEnd\ListingContoller@index')->name('frontend.listings');
    Route::get('/search-listing', 'FrontEnd\ListingContoller@search_listing')->name('frontend.search_listing');
    Route::post('/get-states', 'FrontEnd\ListingContoller@getState')->name('frontend.listings.get-state');
    Route::post('/get-cities', 'FrontEnd\ListingContoller@getCity')->name('frontend.listings.get-city');

    Route::get('/get-address', 'FrontEnd\ListingContoller@getAddress')->name('frontend.listings.get-address');


    Route::get('/{slug}/{id}', 'FrontEnd\ListingContoller@details')->name('frontend.listing.details');
    Route::post('/listing-review/{id}/store-review', 'FrontEnd\ListingContoller@storeReview')->name('listing.listing_details.store_review');
    Route::get('/store-visitor', 'FrontEnd\ListingContoller@store_visitor')->name('frontend.store_visitor');
    Route::get('addto/wishlist/{id}', 'FrontEnd\UserController@add_to_wishlist')->name('addto.wishlist');
    Route::get('remove/wishlist/{id}', 'FrontEnd\UserController@remove_wishlist')->name('remove.wishlist');
    Route::post('/contact-message', 'FrontEnd\ListingContoller@contact')->name('frontend.listings.contact_message');
    Route::post('/product-contact-message', 'FrontEnd\ListingContoller@productContact')->name('frontend.product.contact_message');
  });




  Route::prefix('vendors')->group(function () {
    Route::get('/', 'FrontEnd\VendorController@index')->name('frontend.vendors');
    Route::post('contact/message', 'FrontEnd\VendorController@contact')->name('vendor.contact.message');
  });
  Route::get('vendor/{username}', 'FrontEnd\VendorController@details')->name('frontend.vendor.details');

  Route::prefix('/blog')->group(function () {
    Route::get('', 'FrontEnd\BlogController@index')->name('blog');

    Route::get('/{slug}/{id}',  'FrontEnd\BlogController@details')->name('blog_details');
  });

  Route::get('/about-us', 'FrontEnd\HomeController@about')->name('about_us');

  Route::prefix('/contact')->group(function () {

    Route::get('', 'FrontEnd\ContactController@contact')->name('contact');
    Route::post('/send-mail', 'FrontEnd\ContactController@sendMail')->name('contact.send_mail')->withoutMiddleware('change.lang');
  });
});

Route::post('/advertisement/{id}/count-view', 'FrontEnd\MiscellaneousController@countAdView');

/*
|--------------------------------------------------------------------------
| Unified Authentication Routes
|--------------------------------------------------------------------------
*/

// Unified auth routes
Route::prefix('auth')->middleware(['guest', 'change.lang'])->group(function () {
  // Login routes
  Route::get('/login', 'Auth\AuthController@showLoginForm')->name('auth.login');
  Route::post('/login', 'Auth\AuthController@login')->name('auth.login.submit')->withoutMiddleware('change.lang');
  
  // Register routes
  Route::get('/register', 'Auth\AuthController@showRegisterForm')->name('auth.register');
  Route::post('/register', 'Auth\AuthController@register')->name('auth.register.submit')->withoutMiddleware('change.lang');
  
  // Email verification
  Route::get('/verify-email/{id}', 'Auth\AuthController@verifyEmail')->name('auth.verify.email')->withoutMiddleware('change.lang');
  
  // Upgrade to business
  Route::post('/upgrade-to-business', 'Auth\AuthController@upgradeToBusiness')->name('auth.upgrade.business')->withoutMiddleware(['guest', 'change.lang'])->middleware('auth');
});

// Social login routes (keep existing)
Route::prefix('login')->middleware(['guest', 'change.lang'])->group(function () {
  Route::prefix('/user/facebook')->group(function () {
    Route::get('', 'FrontEnd\UserController@redirectToFacebook')->name('user.login.facebook');
    Route::get('/callback', 'FrontEnd\UserController@handleFacebookCallback');
  });

  Route::prefix('/google')->group(function () {
    Route::get('', 'FrontEnd\UserController@redirectToGoogle')->name('user.login.google');
    Route::get('/callback', 'FrontEnd\UserController@handleGoogleCallback');
  });
});

// Redirect old routes to new unified routes
Route::get('/user/login', function () {
  return redirect()->route('auth.login');
})->name('user.login');

Route::get('/user/signup', function () {
  return redirect()->route('auth.register');
})->name('user.signup');

Route::post('/user/login-submit', function () {
  return redirect()->route('auth.login');
})->name('user.login_submit');

Route::post('/user/signup-submit', function () {
  return redirect()->route('auth.register');
})->name('user.signup_submit');

// User dashboard and profile routes (protected with isUser middleware)
Route::prefix('/user')->middleware(['auth', 'isUser', 'account.status', 'change.lang'])->group(function () {
  Route::get('/dashboard', 'FrontEnd\UserController@redirectToDashboard')->name('user.dashboard');
  Route::get('/wishlist', 'FrontEnd\UserController@wishlist')->name('user.wishlist');
  Route::get('/edit-profile', 'FrontEnd\UserController@editProfile')->name('user.edit_profile');
  Route::post('/update-profile', 'FrontEnd\UserController@updateProfile')->name('user.update_profile')->middleware('Demo')->withoutMiddleware('change.lang');
  Route::get('/change-password', 'FrontEnd\UserController@changePassword')->name('user.change_password');
  Route::post('/update-password', 'FrontEnd\UserController@updatePassword')->name('user.update_password')->middleware('Demo')->withoutMiddleware('change.lang');
  
  // Upgrade to business route
  Route::post('/upgrade-to-business', 'Auth\AuthController@upgradeToBusiness')->name('user.upgrade.business')->withoutMiddleware('change.lang');
});

// Logout route
Route::post('/auth/logout', 'Auth\AuthController@logout')->name('auth.logout')->middleware('auth');
Route::get('/user/logout', function () {
  return redirect()->route('auth.logout');
})->name('user.logout');

// Keep forget password routes (can be moved to AuthController later)
Route::prefix('/user')->middleware(['guest', 'change.lang'])->group(function () {
  Route::get('/forget-password', 'FrontEnd\UserController@forgetPassword')->name('user.forget_password');
  Route::post('/send-forget-password-mail', 'FrontEnd\UserController@forgetPasswordMail')->name('user.send_forget_password_mail')->withoutMiddleware('change.lang');
  Route::get('/reset-password', 'FrontEnd\UserController@resetPassword');
  Route::post('/reset-password-submit', 'FrontEnd\UserController@resetPasswordSubmit')->name('user.reset_password_submit')->withoutMiddleware('change.lang');
  Route::get('/signup-verify/{token}', 'FrontEnd\UserController@signupVerify')->withoutMiddleware('change.lang');
});

// service unavailable route
Route::get('/service-unavailable', 'FrontEnd\MiscellaneousController@serviceUnavailable')->name('service_unavailable')->middleware('exists.down');

/*
|--------------------------------------------------------------------------
| admin frontend route
|--------------------------------------------------------------------------
*/

Route::prefix('/admin')->middleware('guest:admin', 'change.lang')->group(function () {
  // admin redirect to login page route
  Route::get('/', 'Admin\AdminController@login')->name('admin.login');

  // admin login attempt route
  Route::post('/auth', 'Admin\AdminController@authentication')->name('admin.auth');

  // admin forget password route
  Route::get('/forget-password', 'Admin\AdminController@forgetPassword')->name('admin.forget_password');

  // send mail to admin for forget password route
  Route::post('/mail-for-forget-password', 'Admin\AdminController@forgetPasswordMail')->name('admin.mail_for_forget_password');
});


/*
|--------------------------------------------------------------------------
| Custom Page Route For UI
|--------------------------------------------------------------------------
*/
  // Category Location Pages - Structured URLs
  // Location-only routes (without category) - must be before category routes to avoid conflicts
  Route::get('/r/{city}', 'FrontEnd\CategoryLocationPageController@showByCity')->name('frontend.category_location_page.city')->middleware('change.lang');
  Route::get('/r/{city}/{neighborhood}', 'FrontEnd\CategoryLocationPageController@showByCityNeighborhood')->name('frontend.category_location_page.city_neighborhood')->middleware('change.lang');
  
  // Category + Location routes
  Route::get('/r/{category}', 'FrontEnd\CategoryLocationPageController@showByCategory')->name('frontend.category_location_page.category')->middleware('change.lang');
  Route::get('/r/{category}/{city}', 'FrontEnd\CategoryLocationPageController@showByCategoryCity')->name('frontend.category_location_page.category_city')->middleware('change.lang');
  Route::get('/r/{category}/{city}/{neighborhood}', 'FrontEnd\CategoryLocationPageController@showByCategoryCityNeighborhood')->name('frontend.category_location_page.category_city_neighborhood')->middleware('change.lang');

  Route::get('/{slug}', 'FrontEnd\PageController@page')->name('dynamic_page')->middleware('change.lang');

// fallback route
Route::fallback(function () {
  return view('errors.404');
})->middleware('change.lang');
