<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Http\Controllers\Controller;
use App\Http\Helpers\VendorPermissionHelper;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Listing\ListingImage;
use App\Models\Listing\Listing;
use App\Http\Requests\Listing\ListingStoreRequest;
use App\Http\Requests\Listing\ListingUpdateRequest;
use App\Models\BasicSettings\Basic;
use App\Models\Listing\ListingContent;
use App\Models\Listing\ListingFeature;
use App\Models\Listing\ListingSocialMedia;
use App\Models\BusinessHour;
use App\Models\FeaturedListingCharge;
use App\Models\FeatureOrder;
use App\Models\Listing\ListingFeatureContent;
use App\Models\Listing\ListingMessage;
use App\Models\Listing\ListingProduct;
use App\Models\Listing\ListingReview;
use App\Models\Listing\ProductMessage;
use App\Models\ListingCategory;
use Mews\Purifier\Facades\Purifier;
use App\Models\Location\State;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use App\Models\Aminite;
use App\Models\PaymentGateway\OfflineGateway;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Visitor;
use Carbon\Carbon;
use App\Models\Package;
use App\Models\Membership;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function settings()
    {
        $info = DB::table('basic_settings')->select('listing_view', 'admin_approve_status')->first();
        return view('admin.listing.settings', ['info' => $info]);
    }

    public function updateSettings(Request $request)
    {

        $rules = [
            'listing_view' => 'required|numeric',
            'admin_approve_status' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        // store the tax amount info into db
        DB::table('basic_settings')->updateOrInsert(
            ['uniqid' => 12345],
            [
                'listing_view' => $request->listing_view,
                'admin_approve_status' => $request->admin_approve_status
            ]
        );

        Session::flash('success', 'تنظیمات آگهی‌ها با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }

    public function index(Request $request)
    {
        $information['currencyInfo'] = $this->getCurrencyInfo();
        $information['langs'] = Language::all();

        if ($request->language) {
            $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
        } else {
            $language = Language::where('is_default', 1)->first();
        }
        $information['language'] = $language;

        $language_id = $language->id;
        $status = $vendor_id = $title = $category = $featured =  null;

        if (request()->filled('status') && request()->input('status') !== "All") {
            $status = request()->input('status');
        }


        $category_listingIds = [];
        if ($request->filled('category') && $request->input('category') !== "All") {
            $category = $request->input('category');
            $category_content = ListingCategory::where([['language_id', $language->id], ['slug', $category]])->first();

            if (!is_null($category_content)) {
                $category = $category_content->id;
                $contents = ListingContent::where('language_id', $language->id)
                    ->where('category_id', $category)
                    ->get()
                    ->pluck('listing_id');
                foreach ($contents as $content) {
                    if (!in_array($content, $category_listingIds)) {
                        array_push($category_listingIds, $content);
                    }
                }
            }
        }
        $featured_listingIds = [];
        if ($request->filled('featured') && $request->input('featured') !== "All") {
            $featured = $request->input('featured');

            if ($featured == 'active') {
                $contents = FeatureOrder::where('order_status', '=', 'completed')
                    ->where('payment_status', '=', 'completed')
                    ->whereDate('end_date', '>=', Carbon::now()->format('Y-m-d'))
                    ->get()
                    ->pluck('listing_id');
                foreach ($contents as $content) {
                    if (!in_array($content, $featured_listingIds)) {
                        array_push($featured_listingIds, $content);
                    }
                }
            }
            if ($featured == 'pending') {
                $contents = FeatureOrder::where('order_status', '=', 'pending')
                    ->get()
                    ->pluck('listing_id');
                foreach ($contents as $content) {
                    if (!in_array($content, $featured_listingIds)) {
                        array_push($featured_listingIds, $content);
                    }
                } 
            }
            if ($featured == 'rejected') {
                $contents = FeatureOrder::where('order_status', '=', 'pending')
                    ->get()
                    ->pluck('listing_id');
                foreach ($contents as $content) {
                    if (!in_array($content, $featured_listingIds)) {
                        array_push($featured_listingIds, $content);
                    }
                }
                $contentss = FeatureOrder::where('order_status', '=', 'completed')
                    ->where('payment_status', '=', 'completed')
                    ->whereDate('end_date', '>=', Carbon::now()->format('Y-m-d'))
                    ->get()
                    ->pluck('listing_id');
                foreach ($contentss as $conten) {
                    if (!in_array($conten, $featured_listingIds)) {
                        array_push($featured_listingIds, $conten);
                    }
                }
            }
        }

        if (request()->filled('vendor_id') && request()->input('vendor_id') !== "All") {
            $vendor_id = request()->input('vendor_id');
        }

        $listingIds = [];
        if ($request->filled('title')) {
            $title = $request->title;
            $listing_contents = ListingContent::where('language_id', $language->id)
                ->where('title', 'like', '%' . $title . '%')
                ->get()
                ->pluck('listing_id');
            foreach ($listing_contents as $listing_content) {
                if (!in_array($listing_content, $listingIds)) {
                    array_push($listingIds, $listing_content);
                }
            }
        }

        $information['listings'] = Listing::with(['listing_content' => function ($q) use ($language_id) {
            $q->where('language_id', $language_id);
        }])
            ->when($category, function ($query) use ($category_listingIds) {

                return $query->whereIn('listings.id', $category_listingIds);
            })
            ->when($featured, function ($query) use ($featured_listingIds, $featured) {
                if ($featured !== 'rejected') {
                    return $query->whereIn('listings.id', $featured_listingIds);
                } else {
                    return $query->whereNotIn('listings.id', $featured_listingIds);
                }
            })
            ->when($status, function ($query) use ($status) {

                if ($status === 'approved') {
                    return $query->where('status', 1);
                } elseif ($status === 'pending') {
                    return $query->where('status', 0);
                } else {
                    return $query->where('status', 2);
                }
            })
            ->when($vendor_id, function ($query) use ($vendor_id) {
                if ($vendor_id === 'admin') {
                    return $query->where('user_id', '0');
                } else {
                    return $query->where('user_id', $vendor_id);
                }
            })
            ->when($title, function ($query) use ($listingIds) {
                return $query->whereIn('listings.id', $listingIds);
            })
            ->orderBy('listings.id', 'desc')
            ->paginate(10);

        $information['categories'] = ListingCategory::Where('language_id', $language_id)->get();


        // hhhhhhhhhhhhhhhh
        $information['onlineGateways'] = OnlineGateway::where('status', 1)->get();

        $information['offline_gateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'asc')->get();

        $information['vendors'] = \App\Models\User::businesses()->where('id', '!=', 0)->get();
        $charges = FeaturedListingCharge::orderBy('days')->get();
        $information['charges'] = $charges;
        return view('admin.listing.index', $information);
    }
    public function create($id = 0)
    {
        // اگر vendor_id مشخص شده باشد، بررسی می‌کنیم
        $vendor = null;
        $vendorId = $id;
        $currentPackage = null;
        $numberOfImages = 99999999; // برای admin محدودیت نداریم
        $canListingAdd = 1; // admin همیشه می‌تواند اضافه کند
        $pendingPackage = null;
        $pendingMembership = null;

        if ($vendorId != 0) {
            $vendor = User::find($vendorId);
            if (!$vendor) {
                Session::flash('warning', 'فروشنده یافت نشد!');
                return redirect()->route('admin.listing_management.select_vendor');
            }

            $package = VendorPermissionHelper::packagePermission($vendorId);
            if ($package != '[]') {
                $currentPackage = $package;
                $numberOfImages = $currentPackage->number_of_images_per_listing ?? 99999999;
            } else {
                Session::flash('warning', 'این فروشنده عضویت ندارد!');
                return redirect()->route('admin.listing_management.select_vendor');
            }
        }

        $defaultLanguage = Language::where('code', 'fa')->first() ?? Language::where('is_default', 1)->first() ?? Language::first();
        $languages = Language::all();

        $parentCategories = ListingCategory::with(['children' => function ($query) {
            $query->where('status', 1)->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $approvalSettings = Basic::select('admin_approve_status')->first();
        $currencySettings = Basic::select('base_currency_text')->first();
        $states = State::orderBy('name')->get();
        $categoryTree = $parentCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ];
                })->values(),
            ];
        })->values();

        return view('vendors.listing.create', [
            'current_package' => $currentPackage,
            'number_of_images' => $numberOfImages,
            'can_listing_add' => $canListingAdd,
            'pending_package' => $pendingPackage,
            'pending_membership' => $pendingMembership,
            'default_language' => $defaultLanguage,
            'languages' => $languages,
            'parent_categories' => $parentCategories,
            'approval_settings' => $approvalSettings,
            'settings' => $currencySettings,
            'vendor' => $vendor,
            'vendor_id' => $vendorId,
            'states' => $states,
            'category_tree' => $categoryTree,
            'is_admin' => true, // برای تشخیص admin در view
        ]);
    }

    public function selectVendor()
    {
        $information = [];
        $languages = Language::get();
        $information['languages'] = $languages;
        $information['vendors'] = \App\Models\User::businesses()
            ->join('memberships', 'users.id', '=', 'memberships.user_id')
            ->where([
                ['memberships.status', '=', 1],
                ['memberships.start_date', '<=', Carbon::now()->format('Y-m-d')],
                ['memberships.expire_date', '>=', Carbon::now()->format('Y-m-d')]
            ])
            ->select('users.id', 'users.username')
            ->get();
        return view('admin.listing.select-vendor', $information);
    }
    public function findVendor(Request $request)
    {
        return redirect()->route('admin.listing_management.create_listing', ['vendor_id' => $request->vendor_id ?? 0]);
    }

    public function imagesstore(Request $request)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg', 'webp');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    $ext = $img->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg images are allowed");
                    }
                },
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $filename = uniqid() . '.jpg';

        $directory = public_path('assets/img/listing-gallery/');
        @mkdir($directory, 0775, true);
        $img->move($directory, $filename);

        $pi = new ListingImage();
        $pi->image = $filename;
        $pi->save();
        return response()->json(['status' => 'success', 'file_id' => $pi->id]);
    }
    public function imagermv(Request $request)
    {
        $pi = ListingImage::findOrFail($request->fileid);
        $image_count = ListingImage::where('listing_id', $pi->listing_id)->get()->count();
        if ($image_count > 1) {
            @unlink(public_path('assets/img/listing-gallery/') . $pi->image);
            $pi->delete();
            return $pi->id;
        } else {
            return 'false';
        }
    }
    public function imagedbrmv(Request $request)
    {
        $pi = ListingImage::findOrFail($request->fileid);
        $image_count = ListingImage::where('listing_id', $pi->listing_id)->get()->count();
        if ($image_count > 1) {
            @unlink(public_path('assets/img/listing-gallery/') . $pi->image);
            $pi->delete();
            Session::flash('success', 'تصویر اسلایدر با موفقیت حذف شد!');

            return Response::json(['status' => 'success'], 200);
        } else {
            Session::flash('warning', 'شما نمی‌توانید تمام تصاویر را حذف کنید!');

            return Response::json(['status' => 'success'], 200);
        }
    }

    public function getState(Request $request)
    {
        $data['states'] = State::where('country_id', $request->id)->get();
        $data['cities'] = City::where('country_id', $request->id)->get();
        return $data;
    }
    public function getVideo(Request $request)
    {
        return view('admin.listing.video')->render();
    }

    public function getCity(Request $request)
    {
        $stateId = $request->id ?? $request->state_id ?? $request->input('state_id');
        
        if (!$stateId) {
            return response()->json(['status' => 'error', 'message' => 'State ID is required'], 400);
        }
        
        $cities = City::where('state_id', $stateId)->get();
        
        // برای سازگاری با admin-listing.js که انتظار یک آرایه ساده دارد
        if ($request->has('lang') || $request->has('id')) {
            // فرمت قدیمی برای admin-listing.js
            return response()->json($cities->toArray(), 200);
        }
        
        // فرمت جدید برای admin_edit_form.blade.php
        return response()->json(['status' => 'success', 'cities' => $cities], 200);
    }

    public function getNeighborhood(Request $request)
    {
        $neighborhoods = Neighborhood::where('city_id', $request->city_id)->get();
        return response()->json(['status' => 'success', 'neighborhoods' => $neighborhoods], 200);
    }

    public function store(ListingStoreRequest $request)
    {
        if ($request->can_listing_add == 2) {

            Session::flash('warning', 'تعداد آگهی‌ها به محدودیت مجاز رسیده یا از آن فراتر رفته است');

            return Response::json(['status' => 'success'], 200);
        } elseif ($request->can_listing_add == 1) {

            DB::transaction(function () use ($request) {

                $featuredImgURL = $request->feature_image;
                $videoImgURL = $request->video_background_image;

                $approvalSettings = Basic::select('admin_approve_status')->first();
                $shouldApprove = $approvalSettings && $approvalSettings->admin_approve_status == 1;

                $listingData = [
                    'user_id' => $request->user_id ?? 0,
                    'mail' => $request->user_id && $request->user_id != 0 ? (User::find($request->user_id)->email ?? '') : '',
                    'phone' => $request->mobile_phone,
                    'mobile_phone' => $request->mobile_phone,
                    'landline_phone' => $request->landline_phone,
                    'website' => $request->website,
                    'instagram' => $request->instagram,
                    'telegram' => $request->telegram,
                    'whatsapp' => $request->whatsapp,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'status' => $shouldApprove ? 0 : 1,
                    'visibility' => 1,
                ];

                $videoUrl = $request->video_url;
                if ($videoUrl) {
                    if (strpos($videoUrl, '&') !== false) {
                        $videoUrl = substr($videoUrl, 0, strpos($videoUrl, '&'));
                    }
                    $listingData['video_url'] = $videoUrl;
                }

                $in = $listingData;
                if ($request->hasFile('feature_image')) {
                    $featuredImage = $request->file('feature_image');
                    $featuredImgName = uniqid('listing_') . '.' . $featuredImage->getClientOriginalExtension();
                    $featuredDir = public_path('assets/img/listing/');

                    if (!file_exists($featuredDir)) {
                        @mkdir($featuredDir, 0775, true);
                    }

                    $featuredImage->move($featuredDir, $featuredImgName);
                    $in['feature_image'] = $featuredImgName;
                }

                if ($request->hasFile('video_background_image')) {
                    $videoBg = $request->file('video_background_image');
                    $videoImgName = uniqid('listing_video_') . '.' . $videoBg->getClientOriginalExtension();
                    $videoDir = public_path('assets/img/listing/video/');

                    if (!file_exists($videoDir)) {
                        @mkdir($videoDir, 0775, true);
                    }

                    $videoBg->move($videoDir, $videoImgName);
                    $in['video_background_image'] = $videoImgName;
                }

                $listing = Listing::create($in);

                // مدیریت گالری تصاویر
                if ($request->hasFile('gallery')) {
                    foreach ($request->file('gallery') as $galleryFile) {
                        $galleryName = uniqid('listing_gallery_') . '.' . $galleryFile->getClientOriginalExtension();
                        $galleryDir = public_path('assets/img/listing-gallery/');

                        if (!file_exists($galleryDir)) {
                            @mkdir($galleryDir, 0775, true);
                        }

                        $galleryFile->move($galleryDir, $galleryName);

                        ListingImage::create([
                            'listing_id' => $listing->id,
                            'image' => $galleryName,
                        ]);
                    }
                }

                // استفاده از فرم جدید (business_name به جای fa_title)
                $language = Language::where('code', 'fa')->first() ?? Language::where('is_default', 1)->first() ?? Language::first();
                $languageId = $language ? $language->id : null;

                $address = trim($request->postal_address . '، ' . $request->address_details, '، ');
                $keywords = collect(explode(',', (string) ($request->keywords ?? '')))
                    ->map(function ($keyword) {
                        return trim($keyword);
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(',');

                try {
                    $convertedTitle = iconv('UTF-8', 'UTF-8//IGNORE', $request->business_name ?? '');
                    $convertedShortDesc = iconv('UTF-8', 'UTF-8//IGNORE', $request->short_description ?? '');
                    $convertedFullDesc = iconv('UTF-8', 'UTF-8//IGNORE', $request->full_description ?? '');
                    $convertedAddress = iconv('UTF-8', 'UTF-8//IGNORE', $address ?? '');
                    $convertedMetaDesc = iconv('UTF-8', 'UTF-8//IGNORE', $request->short_description ?? '');
                    $convertedKeywords = iconv('UTF-8', 'UTF-8//IGNORE', $keywords ?? '');
                } catch (\Exception $e) {
                    $convertedTitle = $request->business_name ?? '';
                    $convertedShortDesc = $request->short_description ?? '';
                    $convertedFullDesc = $request->full_description ?? '';
                    $convertedAddress = $address ?? '';
                    $convertedMetaDesc = $request->short_description ?? '';
                    $convertedKeywords = $keywords ?? '';
                }

                // ساخت slug از title
                $slug = createSlug($convertedTitle);
                // اگر slug خالی است، از listing id استفاده می‌کنیم
                if (empty($slug) || trim($slug) == '') {
                    $slug = 'listing-' . $listing->id;
                }
                
                // بررسی اینکه slug تکراری نباشد
                $existingSlug = ListingContent::where('slug', $slug)
                    ->where('listing_id', '!=', $listing->id)
                    ->first();
                
                if ($existingSlug) {
                    $slug = $slug . '-' . $listing->id;
                    // اگر باز هم تکراری بود، timestamp اضافه می‌کنیم
                    $counter = 1;
                    while (ListingContent::where('slug', $slug)->where('listing_id', '!=', $listing->id)->exists()) {
                        $slug = createSlug($convertedTitle) . '-' . $listing->id . '-' . $counter;
                        $counter++;
                    }
                }

                // بررسی اینکه language_id null نباشد
                if (empty($languageId)) {
                    \Log::error('Language ID is null', ['request' => $request->all()]);
                    throw new \Exception('Language ID is required. Please ensure at least one language exists in the database.');
                }

                // بررسی اینکه category_id, state_id, city_id null نباشند
                if (empty($request->category_id)) {
                    \Log::error('Category ID is missing', ['request' => $request->all()]);
                    throw new \Exception('Category ID is required.');
                }
                if (empty($request->state_id)) {
                    \Log::error('State ID is missing', ['request' => $request->all()]);
                    throw new \Exception('State ID is required.');
                }
                if (empty($request->city_id)) {
                    \Log::error('City ID is missing', ['request' => $request->all()]);
                    throw new \Exception('City ID is required.');
                }

                try {
                    // تبدیل مقادیر به integer برای foreign keys
                    $categoryId = (int) $request->category_id;
                    $stateId = (int) $request->state_id;
                    $cityId = (int) $request->city_id;
                    $neighborhoodId = $request->neighborhood_id ? (int) $request->neighborhood_id : null;
                    
                    // بررسی وجود foreign keys
                    if (!\App\Models\ListingCategory::find($categoryId)) {
                        throw new \Exception('Category ID ' . $categoryId . ' does not exist.');
                    }
                    if (!\App\Models\Location\State::find($stateId)) {
                        throw new \Exception('State ID ' . $stateId . ' does not exist.');
                    }
                    if (!\App\Models\Location\City::find($cityId)) {
                        throw new \Exception('City ID ' . $cityId . ' does not exist.');
                    }
                    if ($neighborhoodId && !\App\Models\Location\Neighborhood::find($neighborhoodId)) {
                        throw new \Exception('Neighborhood ID ' . $neighborhoodId . ' does not exist.');
                    }
                    
                    ListingContent::create([
                        'language_id' => (int) $languageId,
                        'listing_id' => (int) $listing->id,
                        'category_id' => $categoryId,
                        'state_id' => $stateId,
                        'city_id' => $cityId,
                        'title' => $convertedTitle ?: 'Untitled Listing',
                        'slug' => $slug,
                        'short_description' => $convertedShortDesc ?: null,
                        'description' => Purifier::clean($convertedFullDesc, 'youtube') ?: null,
                        'address' => $convertedAddress ?: null,
                        'meta_keyword' => $convertedKeywords ?: null,
                        'meta_description' => $convertedMetaDesc ?: null,
                        'neighborhood_id' => $neighborhoodId,
                        'aminities' => !empty($request->input('amenities', [])) ? json_encode($request->input('amenities', [])) : null,
                    ]);

                    // ذخیره چند دسته در جدول pivot
                    $categoryIds = json_decode($request->input('category_ids', '[]'), true);
                    if (empty($categoryIds) && $request->category_id) {
                        // برای backward compatibility
                        $categoryIds = [$request->category_id];
                    }
                    if (!empty($categoryIds)) {
                        $listing->categories()->sync($categoryIds);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    \Log::error('ListingContent creation failed', [
                        'error' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'sql' => $e->getSql(),
                        'bindings' => $e->getBindings(),
                        'request' => $request->all(),
                        'language_id' => $languageId,
                        'listing_id' => $listing->id,
                        'category_id' => $request->category_id,
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                    ]);
                    
                    // بررسی نوع خطا و نمایش پیام مناسب
                    $errorMessage = $e->getMessage();
                    $errorCode = $e->getCode();
                    
                    // بررسی خطاهای foreign key constraint
                    if (strpos(strtolower($errorMessage), 'foreign key constraint') !== false || 
                        strpos(strtolower($errorMessage), 'cannot add or update') !== false) {
                        if (strpos($errorMessage, 'category_id') !== false || strpos($errorMessage, 'listing_categories') !== false) {
                            throw new \Exception('دسته‌بندی انتخاب شده معتبر نیست. لطفاً یک دسته‌بندی معتبر انتخاب کنید.');
                        } elseif (strpos($errorMessage, 'state_id') !== false || strpos($errorMessage, 'states') !== false) {
                            throw new \Exception('استان انتخاب شده معتبر نیست. لطفاً یک استان معتبر انتخاب کنید.');
                        } elseif (strpos($errorMessage, 'city_id') !== false || strpos($errorMessage, 'cities') !== false) {
                            throw new \Exception('شهر انتخاب شده معتبر نیست. لطفاً یک شهر معتبر انتخاب کنید.');
                        } elseif (strpos($errorMessage, 'neighborhood_id') !== false || strpos($errorMessage, 'neighborhoods') !== false) {
                            throw new \Exception('محله انتخاب شده معتبر نیست. لطفاً یک محله معتبر انتخاب کنید.');
                        } elseif (strpos($errorMessage, 'language_id') !== false || strpos($errorMessage, 'languages') !== false) {
                            throw new \Exception('زبان انتخاب شده معتبر نیست. لطفاً یک زبان معتبر انتخاب کنید.');
                        } else {
                            throw new \Exception('خطا در ارتباط با پایگاه داده. لطفاً اطلاعات را بررسی کنید و دوباره تلاش کنید.');
                        }
                    }
                    
                    // بررسی خطاهای duplicate entry
                    if (strpos(strtolower($errorMessage), 'duplicate entry') !== false || 
                        strpos(strtolower($errorMessage), '1062') !== false) {
                        if (strpos($errorMessage, 'slug') !== false) {
                            throw new \Exception('این نام کسب‌وکار قبلاً استفاده شده است. لطفاً نام دیگری انتخاب کنید.');
                        } else {
                            throw new \Exception('این اطلاعات قبلاً در سیستم ثبت شده است. لطفاً اطلاعات را تغییر دهید.');
                        }
                    }
                    
                    // بررسی خطاهای null constraint
                    if (strpos(strtolower($errorMessage), 'cannot be null') !== false || 
                        strpos(strtolower($errorMessage), 'column cannot be null') !== false) {
                        throw new \Exception('برخی از فیلدهای اجباری خالی هستند. لطفاً تمام فیلدهای اجباری را پر کنید.');
                    }
                    
                    // خطای عمومی با اطلاعات بیشتر برای لاگ
                    \Log::error('Unhandled database error in ListingContent creation', [
                        'error_message' => $errorMessage,
                        'error_code' => $errorCode,
                        'sql' => method_exists($e, 'getSql') ? $e->getSql() : 'N/A',
                        'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : 'N/A',
                    ]);
                    
                    throw new \Exception('خطا در ذخیره اطلاعات کسب‌وکار. لطفاً اطلاعات را بررسی کنید و دوباره تلاش کنید. در صورت تکرار مشکل، با پشتیبانی تماس بگیرید.');
                } catch (\Exception $e) {
                    \Log::error('ListingContent creation failed', [
                        'error' => $e->getMessage(),
                        'request' => $request->all(),
                    ]);
                    throw $e;
                }

                // مدیریت ساعات کاری
                if ($workingHours = json_decode($request->input('working_hours', '[]'), true)) {
                    foreach ($workingHours as $day) {
                        $businessHours = new BusinessHour();
                        $businessHours->listing_id = $listing->id;
                        $businessHours->day = $day['day'];

                        if (isset($day['status']) && $day['status'] === 'closed') {
                            $businessHours->holiday = 1;
                            $businessHours->start_time = null;
                            $businessHours->end_time = null;
                        } else {
                            $businessHours->holiday = 0;
                            $startTime = $day['start_time'] ?? '09:00';
                            $endTime = $day['end_time'] ?? '18:00';

                            try {
                                $businessHours->start_time = Carbon::createFromFormat('H:i', $startTime)->format('h:i A');
                            } catch (\Exception $e) {
                                $businessHours->start_time = '09:00 AM';
                            }

                            try {
                                $businessHours->end_time = Carbon::createFromFormat('H:i', $endTime)->format('h:i A');
                            } catch (\Exception $e) {
                                $businessHours->end_time = '06:00 PM';
                            }
                        }

                        $businessHours->save();
                    }
                } else {
                    // اگر ساعات کاری ارسال نشده، مقادیر پیش‌فرض را تنظیم می‌کنیم
                    $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    foreach ($days as $day) {
                        $businessHours = new BusinessHour();
                        $businessHours->listing_id = $listing->id;
                        $businessHours->day = $day;
                        $businessHours->start_time = "10:00 AM";
                        $businessHours->end_time = "07:00 PM";
                        $businessHours->holiday = 1;
                        $businessHours->save();
                    }
                }
            });
            Session::flash('success', 'آگهی جدید با موفقیت اضافه شد!');

            return Response::json(['status' => 'success'], 200);
        } else {
            Session::flash('warning', 'این فروشنده هیچ طرحی خریداری نکرده است!');

            return Response::json(['status' => 'error'], 200);
        }
    }
    public function updateStatus(Request $request)
    {
        $listing = Listing::findOrFail($request->listingId);

        if ($request->status == 1) {
            $listing->update(['status' => 1]);

            Session::flash('success', 'آگهی با موفقیت تایید شد!');
        } elseif ($request->status == 2) {
            $listing->update(['status' => 2]);

            Session::flash('success', 'آگهی با موفقیت رد شد!');
        } else {
            $listing->update(['status' => 0]);

            Session::flash('success', 'آگهی با موفقیت در حالت انتظار قرار گرفت!');
        }

        return redirect()->back();
    }
    public function updateVisibility(Request $request)
    {
        $listing = Listing::findOrFail($request->listingId);

        if ($request->visibility == 1) {
            $listing->update(['visibility' => 1]);

            Session::flash('success', 'آگهی با موفقیت نمایش داده شد!');
        }
        if ($request->visibility == 0) {
            $listing->update(['visibility' => 0]);

            Session::flash('success', 'آگهی با موفقیت مخفی شد!');
        }

        return redirect()->back();
    }

    public function updateFeatured(Request $request)
    {
        $rules = [
            'charge' => 'required',
        ];

        $message = [
            'charge.required' => 'فیلد ارتقاء الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        if (!$request->exists('charge')) {

            $errorMessageKey = "select_days_" . $request->listing_id;
            Session::flash($errorMessageKey, 'لطفا لیست ارتقاء را انتخاب کنید.');
            return redirect()->back()->withInput();
        }
        $gatewayId = $request->gateway;
        $offlineGateway = OfflineGateway::query()->find($gatewayId);
        $chargeID = $request->charge;
        $charge = FeaturedListingCharge::findorfail($chargeID);
        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays($charge->days);

        $vendor_id = Listing::where('id', $request->listing_id)->pluck('user_id')->first();

        $be = Basic::select('to_mail')->firstOrFail();
        if ($vendor_id != 0) {
            $vendor = User::where('id', $vendor_id)->select('to_mail', 'username', 'email')->first();

            if (isset($vendor->to_mail)) {
                $to_mail = $vendor->to_mail;
            } else {
                $to_mail = $vendor->email;
            }
        } else {
            $to_mail = $be->to_mail;
        }

        $order =  FeatureOrder::where('listing_id', $request->listing_id)->first();
        if (empty($order)) {
            $order = new FeatureOrder();
        }

        $order->listing_id = $request->listing_id;
        $order->user_id = $vendor_id;
        $order->vendor_mail = $to_mail;
        $order->order_number = uniqid();
        $order->total = $charge->price;
        $order->payment_method = $offlineGateway ? $offlineGateway->name : $gatewayId;
        $order->gateway_type = "offline";
        $order->payment_status = "completed";
        $order->order_status = 'completed';
        $order->attachment = null;
        $order->days = $charge->days;
        $order->start_date = $startDate;
        $order->end_date = $endDate;
        $order->save();

        Session::flash('success', 'آگهی با موفقیت ویژه شد!');
        return  redirect()->back();
    }

    public function getChildCategories($parentId)
    {
        $children = ListingCategory::where('parent_id', $parentId)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json($children);
    }

    public function edit($id)
    {
        $listing = Listing::with('galleries')->findOrFail($id);
        $vendorId = $listing->user_id;
        
        $vendor = null;
        $currentPackage = null;
        $numberOfImages = 99999999; // برای admin محدودیت نداریم
        $canListingAdd = 1; // admin همیشه می‌تواند ویرایش کند
        $pendingPackage = null;
        $pendingMembership = null;

        if ($vendorId != 0) {
            $vendor = User::find($vendorId);
            if ($vendor) {
                $package = VendorPermissionHelper::packagePermission($vendorId);
                if ($package != '[]') {
                    $currentPackage = $package;
                    $numberOfImages = $currentPackage->number_of_images_per_listing ?? 99999999;
                }
            }
        }

        $defaultLanguage = Language::where('code', 'fa')->first() ?? Language::where('is_default', 1)->first() ?? Language::first();
        $languages = Language::all();

        $parentCategories = ListingCategory::with(['children' => function ($query) {
            $query->where('status', 1)->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $approvalSettings = Basic::select('admin_approve_status')->first();
        $currencySettings = Basic::select('base_currency_text')->first();
        $states = State::orderBy('name')->get();
        $categoryTree = $parentCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ];
                })->values(),
            ];
        })->values();

        // دریافت محتوای لیست برای زبان پیش‌فرض
        $listingContent = ListingContent::where('listing_id', $listing->id)
            ->where('language_id', $defaultLanguage->id)
            ->first();

        // دریافت امکانات برای admin
        $amenities = Aminite::where('language_id', $defaultLanguage->id)->get();
        
        // دریافت محلات برای admin (اگر شهر انتخاب شده باشد)
        $neighborhoods = collect([]);
        if ($listingContent && $listingContent->city_id) {
            $neighborhoods = Neighborhood::where('city_id', $listingContent->city_id)->orderBy('name')->get();
        }

        // دریافت ساعات کاری
        $businessHours = BusinessHour::where('listing_id', $listing->id)->get();
        $workingHoursData = [];
        if ($businessHours->count() > 0) {
            $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            foreach ($days as $day) {
                $hour = $businessHours->where('day', $day)->first();
                if ($hour) {
                    $workingHoursData[] = [
                        'day' => $day,
                        'status' => $hour->holiday ? 'closed' : 'open',
                        'start_time' => $hour->start_time ?? '',
                        'end_time' => $hour->end_time ?? '',
                    ];
                } else {
                    $workingHoursData[] = [
                        'day' => $day,
                        'status' => 'closed',
                        'start_time' => '',
                        'end_time' => '',
                    ];
                }
            }
        }

        // پیدا کردن دسته‌بندی اصلی و زیر دسته
        $selectedCategoryId = null;
        $selectedParentCategoryId = null;
        $selectedCategoryIds = [];
        
        // دریافت دسته‌های انتخاب شده از جدول pivot
        $listing->load('categories');
        if ($listing->categories && $listing->categories->count() > 0) {
            $selectedCategoryIds = $listing->categories->pluck('id')->toArray();
            $selectedCategoryId = $selectedCategoryIds[0]; // اولین دسته برای backward compatibility
        } elseif ($listingContent && $listingContent->category_id) {
            $selectedCategoryId = $listingContent->category_id;
            $selectedCategoryIds = [$selectedCategoryId];
        }
        
        if ($selectedCategoryId) {
            $category = ListingCategory::find($selectedCategoryId);
            if ($category && $category->parent_id) {
                $selectedParentCategoryId = $category->parent_id;
            } elseif ($category && !$category->parent_id) {
                $selectedParentCategoryId = $category->id;
            }
        }

        return view('vendors.listing.edit', [
            'listing' => $listing,
            'listing_content' => $listingContent,
            'current_package' => $currentPackage,
            'number_of_images' => $numberOfImages,
            'can_listing_add' => $canListingAdd,
            'pending_package' => $pendingPackage,
            'pending_membership' => $pendingMembership,
            'default_language' => $defaultLanguage,
            'languages' => $languages,
            'parent_categories' => $parentCategories,
            'approval_settings' => $approvalSettings,
            'settings' => $currencySettings,
            'vendor' => $vendor,
            'vendor_id' => $vendorId,
            'states' => $states,
            'category_tree' => $categoryTree,
            'is_admin' => true,
            'selected_category_id' => $selectedCategoryId,
            'selected_parent_category_id' => $selectedParentCategoryId,
            'selected_category_ids' => $selectedCategoryIds,
            'working_hours_data' => $workingHoursData,
            'amenities' => $amenities,
            'neighborhoods' => $neighborhoods,
        ]);
    }

    public function videoImageRemove($id)
    {

        $Listing = Listing::Where('id', $id)->first();
        $Listing->video_background_image = null;

        $Listing->save();

        Session::flash('success', 'تصویر ویدیو با موفقیت حذف شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function update(ListingUpdateRequest $request, $id)
    {
        $featuredImgURL = $request->thumbnail;
        $videoImgURL = $request->video_background_image;

        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        if ($request->hasFile('thumbnail')) {
            $rules['thumbnail'] = [
                'required',
                function ($attribute, $value, $fail) use ($featuredImgURL, $allowedExts) {
                    $ext = $featuredImgURL->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("فقط تصاویر png, jpg, jpeg مجاز هستند");
                    }
                },
            ];
        }

        if ($request->hasFile('video_background_image')) {
            $rules['video_background_image'] = [
                'required',
                function ($attribute, $value, $fail) use ($featuredImgURL, $allowedExts) {
                    $ext = $featuredImgURL->getClientOriginalExtension();
                    if (!in_array($ext, $allowedExts)) {
                        return $fail("فقط تصاویر png, jpg, jpeg مجاز هستند");
                    }
                },
            ];
        }

        $languages = Language::all();

        $in = $request->all();
        $listing = Listing::findOrFail($request->listing_id);
        if ($request->hasFile('thumbnail')) {
            $featuredImgExt = $featuredImgURL->getClientOriginalExtension();

            $featuredImgName = time() . '.' . $featuredImgExt;
            $featuredDir = public_path('assets/img/listing/');

            if (!file_exists($featuredDir)) {
                mkdir($featuredDir, 0777, true);
            }
            copy($featuredImgURL, $featuredDir . $featuredImgName);
            @unlink(public_path('assets/img/listing/') . $listing->feature_image);

            $in['feature_image'] = $featuredImgName;
        }

        if ($request->hasFile('video_background_image')) {
            $videoImgExt = $videoImgURL->getClientOriginalExtension();

            $videoImgName = time() . '.' . $videoImgExt;
            $videoDir = public_path('assets/img/listing/video/');

            if (!file_exists($videoDir)) {
                mkdir($videoDir, 0777, true);
            }
            copy($videoImgURL, $videoDir . $videoImgName);
            @unlink(public_path('assets/img/listing/video/') . $listing->video_background_image);

            $in['video_background_image'] = $videoImgName;
        }
        $videoLink = $request->video_url;
        if ($videoLink) {
            if (strpos($videoLink, "&") != false) {
                $videoLink = substr($videoLink, 0, strpos($videoLink, "&"));
            }
            $in['video_url'] = $videoLink;
        }

        $listing = $listing->update($in);

        $slders = $request->slider_images;
        if ($slders) {
            $pis = ListingImage::findOrFail($slders);
            foreach ($pis as $key => $pi) {
                $pi->listing_id = $request->listing_id;
                $pi->save();
            }
        }

        // بررسی اینکه آیا فرم جدید (business_name) ارسال شده یا فرم قدیمی
        $isNewForm = $request->has('business_name');
        
        if ($isNewForm) {
            // فرم جدید - استفاده از فیلدهای جدید
            $defaultLanguage = Language::where('code', 'fa')->first() ?? Language::where('is_default', 1)->first() ?? Language::first();
            $listingContent = ListingContent::where('listing_id', $request->listing_id)
                ->where('language_id', $defaultLanguage->id)
                ->first();
            
            if (empty($listingContent)) {
                $listingContent = new ListingContent();
                $listingContent->language_id = $defaultLanguage->id;
                $listingContent->listing_id = $request->listing_id;
            }
            
            $address = trim(($request->postal_address ?? '') . '، ' . ($request->address_details ?? ''), '، ');
            $keywords = collect(explode(',', (string) ($request->keywords ?? '')))
                ->map(function ($keyword) {
                    return trim($keyword);
                })
                ->filter()
                ->unique()
                ->values()
                ->implode(',');
            
            $convertedTitle = $request->business_name ?? '';
            $slug = createSlug($convertedTitle);
            if (empty($slug) || trim($slug) == '') {
                $slug = 'listing-' . $request->listing_id;
            }
            
            $listingContent->title = $convertedTitle;
            $listingContent->slug = $slug;
            $listingContent->short_description = $request->short_description ?? '';
            $listingContent->description = Purifier::clean($request->full_description ?? '', 'youtube');
            $listingContent->address = $address;
            $listingContent->meta_keyword = $keywords;
            $listingContent->meta_description = $request->short_description ?? '';
            $listingContent->category_id = $request->category_id ?? null;
            $listingContent->state_id = $request->state_id ?? null;
            $listingContent->city_id = $request->city_id ?? null;
            $listingContent->neighborhood_id = $request->neighborhood_id ?? null;
            
            // ذخیره amenities
            if ($request->has('amenities')) {
                $listingContent->aminities = json_encode($request->amenities);
            }
            
            $listingContent->save();
            
            // به‌روزرسانی چند دسته در جدول pivot
            $categoryIds = json_decode($request->input('category_ids', '[]'), true);
            if (empty($categoryIds) && $request->has('category_id')) {
                $categoryIds = [$request->category_id];
            }
            if (!empty($categoryIds)) {
                $listing = Listing::findOrFail($request->listing_id);
                $listing->categories()->sync($categoryIds);
            }
            
            // به‌روزرسانی ساعات کاری
            if ($workingHours = json_decode($request->input('working_hours', '[]'), true)) {
                BusinessHour::where('listing_id', $request->listing_id)->delete();
                foreach ($workingHours as $day) {
                    $businessHours = new BusinessHour();
                    $businessHours->listing_id = $request->listing_id;
                    $businessHours->day = $day['day'];
                    
                    if (isset($day['status']) && $day['status'] === 'closed') {
                        $businessHours->holiday = 1;
                        $businessHours->start_time = null;
                        $businessHours->end_time = null;
                    } else {
                        $businessHours->holiday = 0;
                        $startTime = $day['start_time'] ?? '09:00';
                        $endTime = $day['end_time'] ?? '18:00';
                        
                        try {
                            $businessHours->start_time = Carbon::createFromFormat('H:i', $startTime)->format('h:i A');
                        } catch (\Exception $e) {
                            $businessHours->start_time = '09:00 AM';
                        }
                        
                        try {
                            $businessHours->end_time = Carbon::createFromFormat('H:i', $endTime)->format('h:i A');
                        } catch (\Exception $e) {
                            $businessHours->end_time = '06:00 PM';
                        }
                    }
                    $businessHours->save();
                }
            }
            
            // به‌روزرسانی فیلدهای listing
            $listingUpdateData = [];
            if ($request->has('mobile_phone')) {
                $listingUpdateData['mobile_phone'] = $request->mobile_phone;
                $listingUpdateData['phone'] = $request->mobile_phone;
            }
            if ($request->has('landline_phone')) {
                $listingUpdateData['landline_phone'] = $request->landline_phone;
            }
            if ($request->has('website')) {
                $listingUpdateData['website'] = $request->website;
            }
            if ($request->has('instagram')) {
                $listingUpdateData['instagram'] = $request->instagram;
            }
            if ($request->has('telegram')) {
                $listingUpdateData['telegram'] = $request->telegram;
            }
            if ($request->has('whatsapp')) {
                $listingUpdateData['whatsapp'] = $request->whatsapp;
            }
            if ($request->has('video_url')) {
                $videoUrl = $request->video_url;
                if ($videoUrl && strpos($videoUrl, '&') !== false) {
                    $videoUrl = substr($videoUrl, 0, strpos($videoUrl, '&'));
                }
                $listingUpdateData['video_url'] = $videoUrl;
            }
            if ($request->has('latitude')) {
                $listingUpdateData['latitude'] = $request->latitude;
            }
            if ($request->has('longitude')) {
                $listingUpdateData['longitude'] = $request->longitude;
            }
            
            if (!empty($listingUpdateData)) {
                $listing = Listing::findOrFail($request->listing_id);
                $listing->update($listingUpdateData);
            }
            
            // مدیریت تصاویر
            if ($request->hasFile('feature_image')) {
                $featuredImage = $request->file('feature_image');
                $featuredImgName = uniqid('listing_') . '.' . $featuredImage->getClientOriginalExtension();
                $featuredDir = public_path('assets/img/listing/');
                if (!file_exists($featuredDir)) {
                    @mkdir($featuredDir, 0775, true);
                }
                $listing = Listing::findOrFail($request->listing_id);
                if ($listing->feature_image) {
                    @unlink($featuredDir . $listing->feature_image);
                }
                $featuredImage->move($featuredDir, $featuredImgName);
                $listing->update(['feature_image' => $featuredImgName]);
            }
            
            if ($request->hasFile('video_background_image')) {
                $videoBg = $request->file('video_background_image');
                $videoImgName = uniqid('listing_video_') . '.' . $videoBg->getClientOriginalExtension();
                $videoDir = public_path('assets/img/listing/video/');
                if (!file_exists($videoDir)) {
                    @mkdir($videoDir, 0775, true);
                }
                $listing = Listing::findOrFail($request->listing_id);
                if ($listing->video_background_image) {
                    @unlink($videoDir . $listing->video_background_image);
                }
                $videoBg->move($videoDir, $videoImgName);
                $listing->update(['video_background_image' => $videoImgName]);
            }
            
            // مدیریت گالری تصاویر جدید (اگر از طریق فرم ارسال شده باشد)
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $galleryFile) {
                    $galleryName = uniqid('listing_gallery_') . '.' . $galleryFile->getClientOriginalExtension();
                    $galleryDir = public_path('assets/img/listing-gallery/');

                    if (!file_exists($galleryDir)) {
                        @mkdir($galleryDir, 0775, true);
                    }

                    $galleryFile->move($galleryDir, $galleryName);

                    ListingImage::create([
                        'listing_id' => $request->listing_id,
                        'image' => $galleryName,
                    ]);
                }
            }
            
        } else {
            // فرم قدیمی - استفاده از فیلدهای چندزبانه
            foreach ($languages as $language) {
                $listingContent =  ListingContent::where('listing_id', $request->listing_id)->where('language_id', $language->id)->first();
                if (empty($listingContent)) {
                    $listingContent = new ListingContent();
                }
                $listingContent->language_id = $language->id;
                $listingContent->title = $request[$language->code . '_title'];
                $listingContent->slug = createSlug($request[$language->code . '_title']);
                $listingContent->category_id = $request[$language->code . '_category_id'];
                $listingContent->country_id = null;
                $listingContent->state_id = $request[$language->code . '_state_id'];
                $listingContent->city_id = $request[$language->code . '_city_id'];
                $listingContent->address = $request[$language->code . '_address'];
                $aminities = $request->input($language->code . '_aminities', []);
                $listingContent->aminities = json_encode($aminities);
                $listingContent->description = Purifier::clean($request[$language->code . '_description'], 'youtube');
                $listingContent->meta_keyword = $request[$language->code . '_meta_keyword'];
                $listingContent->meta_description = $request[$language->code . '_meta_description'];
                // به‌روزرسانی neighborhood_id اگر ارسال شده باشد
                if ($request->has('neighborhood_id')) {
                    $listingContent->neighborhood_id = $request->neighborhood_id;
                }
                $listingContent->save();
            }
        }

        // به‌روزرسانی چند دسته در جدول pivot
        $categoryIds = json_decode($request->input('category_ids', '[]'), true);
        if (empty($categoryIds) && $request->has('category_id')) {
            // برای backward compatibility
            $categoryIds = [$request->category_id];
        }
        if (!empty($categoryIds)) {
            $listing = Listing::findOrFail($request->listing_id);
            $listing->categories()->sync($categoryIds);
        }


        Session::flash('success', 'آگهی با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function manageSocialLink($id)
    {
        Listing::findOrFail($id);
        $permission = socialLinksPermission($id);
        if ($permission) {
            $information['listing_id'] = $id;
            $socialLink = ListingSocialMedia::Where('listing_id', $id)->get();
            $information['socialLinks'] = $socialLink;
            return view('admin.listing.social-link', $information);
        } else {
            Session::flash('warning', "دسترسی لینک‌های اجتماعی این فروشنده اعطا نشده است.");
            return redirect()->route('admin.listing_management.listing');
        }
    }

    public function updateSocialLink(Request $request, $id)
    {

        $vendorId = Listing::where('id', $id)
            ->pluck('user_id')
            ->first();

        if ($vendorId != 0) {
            $SocialLinkLimit = packageTotalSocialLink($vendorId);
        } else {
            $SocialLinkLimit = 99999999;
        }

        $request->validate([
            'socail_link' => [
                'sometimes',
                'array',
                'max:' . $SocialLinkLimit,
            ],
            'socail_link.*' => [
                'required',
            ],
            'icons' => 'required',
        ]);

        ListingSocialMedia::where('listing_id', $id)->delete();

        $iconsString = ($request->icons);
        $iconArray = explode(',', $iconsString);

        if (!empty($request->socail_link)) {

            foreach ($request->socail_link as $key => $link) {

                ListingSocialMedia::create([
                    'listing_id' => $id,
                    'link' => $link,
                    'icon' => $iconArray[$key]
                ]);
            }
        }
        Session::flash('success', 'لینک‌های اجتماعی با موفقیت به‌روزرسانی شد!');
        return Response::json(['status' => 'success'], 200);
    }
    public function manageAdditionalSpecification($id)
    {
        Listing::findOrFail($id);
        $permission = additionalSpecificationsPermission($id);
        if ($permission) {
            $information['listing_id'] = $id;
            $information['languages'] = Language::all();
            $information['features'] = ListingFeature::where('listing_id', $id)->get();
            $information['totalFeature'] = ListingFeature::where('listing_id', $id)->count();
            return view('admin.listing.feature', $information);
        } else {
            Session::flash('warning', "دسترسی مشخصات اضافی این فروشنده اعطا نشده است.");
            return redirect()->route('admin.listing_management.listing');
        }
    }

    public function updateAdditionalSpecification(Request $request, $id)
    {
        $vendorId = Listing::where('id', $id)
            ->pluck('user_id')
            ->first();

        if ($vendorId != 0) {
            $additionalFeatureLimit = packageTotalAdditionalSpecification($vendorId);
        } else {
            $additionalFeatureLimit = 99999999;
        }

        $rules = [];
        $messages = [];
        $languages = Language::all();
        foreach ($languages as $language) {

            $rules[$language->code . '_feature_heading'] = 'sometimes|array|max:' . $additionalFeatureLimit;
            $rules[$language->code . '_feature_heading.*'] = 'required';

            $messages[$language->code . '_feature_heading.*.required'] = 'The ' . $language->name . ' Feature Heading is required.';
            $messages[$language->code . '_feature_heading.array'] = 'The ' . $language->name . ' Feature Heading must be an array.';
            $messages[$language->code . '_feature_heading.max'] =  'Maximum ' . $additionalFeatureLimit . ' Additional Features can be added per listing for ' . $language->name . ' Language';
        }

        $listingFeatures = ListingFeature::where('listing_id', $id)->get();
        foreach ($listingFeatures as $listingFeature) {
            $listingFeaturesContents = ListingFeatureContent::where('listing_feature_id', $listingFeature->id)->get();
            foreach ($listingFeaturesContents as $listingFeaturesContent) {
                $listingFeaturesContent->delete();
            }
            $listingFeature->delete();
        }

        foreach ($languages as $language) {

            if (!empty(($request[$language->code . '_feature_heading']))) {

                foreach ($request[$language->code . '_feature_heading'] as $key => $v_helper) {
                    $feature_value = $request[$language->code . '_feature_value_' . $key];

                    $listing_feature = ListingFeature::where([['listing_id', $id], ['indx', $key]])->first();
                    if (is_null($listing_feature)) {

                        ListingFeature::create([
                            'listing_id' => $id,
                            'indx' =>  $key
                        ]);
                    }
                    $listing_feature = ListingFeature::where([['listing_id', $id], ['indx', $key]])->first();
                    $listing_specification_content = new ListingFeatureContent();
                    $listing_specification_content->language_id = $language->id;
                    $listing_specification_content->listing_feature_id = $listing_feature->id;
                    $listing_specification_content->feature_heading = $v_helper;
                    $listing_specification_content->feature_value = json_encode($feature_value);
                    $listing_specification_content->save();
                }
            }
        }

        Session::flash('success', 'ویژگی‌ها با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function delete($id)
    {
        $listing = Listing::findOrFail($id);

        //delete all the contents of this listing
        $contents = $listing->listing_content()->get();

        foreach ($contents as $content) {
            $content->delete();
        }

        // delete feature_image image and video image of this listing
        if (!is_null($listing->feature_image)) {
            @unlink(public_path('assets/img/listing/') . $listing->feature_image);
        }
        if (!is_null($listing->video_background_image)) {
            @unlink(public_path('assets/img/listing/video/') . $listing->video_background_image);
        }

        //delete all the images of this listing
        $galleries = $listing->galleries()->get();

        foreach ($galleries as $gallery) {
            @unlink(public_path('assets/img/listing-gallery/') . $gallery->image);
            $gallery->delete();
        }
        //delete all Features for this listing
        $listingFeatures =  $listing->specifications()->get();
        foreach ($listingFeatures as $listingFeature) {
            $listingFeaturesContents = ListingFeatureContent::where('listing_feature_id', $listingFeature->id)->get();
            foreach ($listingFeaturesContents as $listingFeaturesContent) {
                $listingFeaturesContent->delete();
            }
            $listingFeature->delete();
        }

        //delete feature order
        $featureOrders = FeatureOrder::where('listing_id', $id)->get();
        if (!is_null($featureOrders)) {

            foreach ($featureOrders as $order) {
                if (!is_null($order->attachment)) {
                    @unlink(public_path('assets/file/attachments/feature-activation/') . $order->attachment);
                }
                $order->delete();
            }
        }
        //delete all message for this listing
        $listingMessages = ListingMessage::where('listing_id', $id)->get();
        if (!is_null($listingMessages)) {

            foreach ($listingMessages as $message) {
                $message->delete();
            }
        }
        //delete all reviews for this listing
        $reviews = ListingReview::where('listing_id', $id)->get();
        if (!is_null($reviews)) {
            foreach ($reviews as $review) {
                $review->delete();
            }
        }
        //delete all visit for this listing
        $visitors  = Visitor::where('listing_id', $id)->get();
        if (!is_null($visitors)) {
            foreach ($visitors as $visitor) {
                $visitor->delete();
            }
        }
        //delete all faq for this listing
        $faqs = $listing->listingFaqs()->get();
        foreach ($faqs as $faq) {
            $faq->delete();
        }
        //delete all follow us  for this listing
        $sociallinks = $listing->sociallinks()->get();
        foreach ($sociallinks as $sociallink) {
            $sociallink->delete();
        }

        //delete all business hours for this listing
        BusinessHour::where('listing_id', $id)->delete();


        //delete all products
        $products = ListingProduct::where('listing_id', $id)->get();

        if (!is_null($products)) {

            foreach ($products as $product) {

                $productcontents = $product->listing_product_content()->get();
                //delete all product contents
                foreach ($productcontents as $productcontent) {
                    $productcontent->delete();
                }
                //delete product feature image
                if (!is_null($product->feature_image)) {
                    @unlink(public_path('assets/img/listing/product/') . $product->feature_image);
                }

                //delete all product slider images
                $galleries = $product->galleries()->get();

                foreach ($galleries as $gallery) {
                    @unlink(public_path('assets/img/listing/product-gallery/') . $gallery->image);
                    $gallery->delete();
                }
                //delete this product
                //delete all message for this listing
                $productMessages = ProductMessage::where('product_id', $product->id)->get();
                if (!is_null($productMessages)) {
                    foreach ($productMessages as $message) {
                        $message->delete();
                    }
                }
                $product->delete();
            }
        }
        // finally, delete this listing
        $listing->delete();

        Session::flash('success', 'آگهی با موفقیت حذف شد!');

        return redirect()->back();
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $listing = Listing::findOrFail($id);

            //delete all the contents of this listing
            $contents = $listing->listing_content()->get();

            foreach ($contents as $content) {
                $content->delete();
            }

            // delete feature_image image and video image of this listing
            if (!is_null($listing->feature_image)) {
                @unlink(public_path('assets/img/listing/') . $listing->feature_image);
            }
            if (!is_null($listing->video_background_image)) {
                @unlink(public_path('assets/img/listing/video/') . $listing->video_background_image);
            }

            //delete all the images of this listing
            $galleries = $listing->galleries()->get();

            foreach ($galleries as $gallery) {
                @unlink(public_path('assets/img/listing-gallery/') . $gallery->image);
                $gallery->delete();
            }
            //delete all Features for this listing
            $listingFeatures =  $listing->specifications()->get();
            foreach ($listingFeatures as $listingFeature) {
                $listingFeaturesContents = ListingFeatureContent::where('listing_feature_id', $listingFeature->id)->get();
                foreach ($listingFeaturesContents as $listingFeaturesContent) {
                    $listingFeaturesContent->delete();
                }
                $listingFeature->delete();
            }

            //delete feature order
            $featureOrders = FeatureOrder::where('listing_id', $id)->get();
            if (!is_null($featureOrders)) {

                foreach ($featureOrders as $order) {
                    if (!is_null($order->attachment)) {
                        @unlink(public_path('assets/file/attachments/feature-activation/') . $order->attachment);
                    }
                    $order->delete();
                }
            }
            //delete all message for this listing
            $listingMessages = ListingMessage::where('listing_id', $id)->get();
            if (!is_null($listingMessages)) {

                foreach ($listingMessages as $message) {
                    $message->delete();
                }
            }
            //delete all reviews for this listing
            $reviews = ListingReview::where('listing_id', $id)->get();
            if (!is_null($reviews)) {
                foreach ($reviews as $review) {
                    $review->delete();
                }
            }
            //delete all visit for this listing
            $visitors  = Visitor::where('listing_id', $id)->get();
            if (!is_null($visitors)) {
                foreach ($visitors as $visitor) {
                    $visitor->delete();
                }
            }
            //delete all faq for this listing
            $faqs = $listing->listingFaqs()->get();
            foreach ($faqs as $faq) {
                $faq->delete();
            }
            //delete all follow us  for this listing
            $sociallinks = $listing->sociallinks()->get();
            foreach ($sociallinks as $sociallink) {
                $sociallink->delete();
            }

            //delete all business hours for this listing
            BusinessHour::where('listing_id', $id)->delete();


            //delete all products
            $products = ListingProduct::where('listing_id', $id)->get();

            if (!is_null($products)) {

                foreach ($products as $product) {

                    $productcontents = $product->listing_product_content()->get();
                    //delete all product contents
                    foreach ($productcontents as $productcontent) {
                        $productcontent->delete();
                    }
                    //delete product feature image
                    if (!is_null($product->feature_image)) {
                        @unlink(public_path('assets/img/listing/product/') . $product->feature_image);
                    }

                    //delete all product slider images
                    $galleries = $product->galleries()->get();

                    foreach ($galleries as $gallery) {
                        @unlink(public_path('assets/img/listing/product-gallery/') . $gallery->image);
                        $gallery->delete();
                    }
                    //delete this product
                    //delete all message for this listing
                    $productMessages = ProductMessage::where('product_id', $product->id)->get();
                    if (!is_null($productMessages)) {
                        foreach ($productMessages as $message) {
                            $message->delete();
                        }
                    }
                    $product->delete();
                }
            }
            // finally, delete this listing
            $listing->delete();
        }

        Session::flash('success', 'آگهی با موفقیت حذف شد!');

        return response()->json(['status' => 'success'], 200);
    }

    public function featureDelete(Request $request)
    {
        $listing_feature = ListingFeature::find($request->spacificationId);
        $listing_feature_contents = ListingFeatureContent::where('listing_feature_id', $listing_feature->id)->get();
        foreach ($listing_feature_contents as $listing_feature_content) {
            $listing_feature_content->delete();
        }
        $listing_feature->delete();

        Session::flash('success', 'ویژگی با موفقیت حذف شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function socialDelete(Request $request)
    {
        $listing_feature = ListingSocialMedia::find($request->socialID);

        $listing_feature->delete();

        Session::flash('success', 'لینک اجتماعی با موفقیت حذف شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function aminitieUpdate(Request $request)
    {
        $Listing = ListingContent::Where([['listing_id', $request->listingId], ['language_id', $request->languageId]])->first();

        if (!$Listing) {
            return Response::json(['status' => 'error', 'message' => 'Listing content not found'], 404);
        }

        $aminities = $request->aminities;
        $aminitiesArray = explode(',', $aminities);
        $aminitiesArray = array_map('strval', $aminitiesArray);
        $Listing->aminities = json_encode($aminitiesArray);

        $Listing->save();

        Session::flash('success', 'امکانات با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function plugins($id)
    {
        Listing::findorFail($id);
        $vendorId = Listing::where('id', $id)->pluck('user_id')->first();
        $language = Language::where('is_default', 1)->first();

        $information['title'] = ListingContent::where([['language_id', $language->id], ['listing_id', $id]])
            ->select('title')
            ->first();

        if ($vendorId != 0) {
            $current_package = VendorPermissionHelper::packagePermission($vendorId);

            if ($current_package != '[]') {
                $information['data'] = DB::table('listings')
                    ->where('id', $id)
                    ->select('whatsapp_status', 'whatsapp_number', 'whatsapp_header_title', 'whatsapp_popup_status', 'whatsapp_popup_message',  'tawkto_status', 'tawkto_direct_chat_link', 'telegram_status', 'telegram_username', 'messenger_status', 'messenger_direct_chat_link')
                    ->first();
                $information['id'] = $id;

                return view('admin.listing.plugins', $information);
            } else {

                Session::flash('warning', 'این فروشنده هیچ طرحی ندارد');
                return redirect()->route('admin.listing_management.listing');
            }
        } else {
            $information['data'] = DB::table('listings')
                ->where('id', $id)
                ->select('whatsapp_status', 'whatsapp_number', 'whatsapp_header_title', 'whatsapp_popup_status', 'whatsapp_popup_message',  'tawkto_status', 'tawkto_direct_chat_link', 'telegram_status', 'telegram_username', 'messenger_status', 'messenger_direct_chat_link')
                ->first();
            $information['id'] = $id;

            return view('admin.listing.plugins', $information);
        }
    }
    public function updateTelegram(Request $request, $id)
    {
        $rules = [
            'telegram_status' => 'required',
            'telegram_username' => 'required'
        ];

        $messages = [
            'telegram_status.required' => 'فیلد وضعیت تلگرام الزامی است.',
            'telegram_username.required' => 'فیلد نام کاربری تلگرام الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::table('listings')->where('id', $id)->update(
            [
                'telegram_status' => $request->telegram_status,
                'telegram_username' => $request->telegram_username
            ]
        );

        Session::flash('success', 'اطلاعات تلگرام با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }
    public function updateTawkTo(Request $request, $id)
    {
        $rules = [
            'tawkto_status' => 'required',
            'tawkto_direct_chat_link' => 'required'
        ];

        $messages = [
            'tawkto_status.required' => 'فیلد وضعیت تاک.تو الزامی است.',
            'tawkto_direct_chat_link.required' => 'فیلد لینک تاک.تو الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::table('listings')->where('id', $id)->update(
            [
                'tawkto_status' => $request->tawkto_status,
                'tawkto_direct_chat_link' => $request->tawkto_direct_chat_link
            ]
        );

        Session::flash('success', 'اطلاعات تاک.تو با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }
    public function updateWhatsApp(Request $request, $id)
    {
        $rules = [
            'whatsapp_status' => 'required',
            'whatsapp_number' => 'required',
            'whatsapp_header_title' => 'required',
            'whatsapp_popup_status' => 'required',
            'whatsapp_popup_message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::table('listings')->where('id', $id)->update(
            [
                'whatsapp_status' => $request->whatsapp_status,
                'whatsapp_number' => $request->whatsapp_number,
                'whatsapp_header_title' => $request->whatsapp_header_title,
                'whatsapp_popup_status' => $request->whatsapp_popup_status,
                'whatsapp_popup_message' => $request->whatsapp_popup_message
            ]
        );

        Session::flash('success', 'اطلاعات واتس‌اپ با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }

    public function updateMessanger(Request $request, $id)
    {
        $rules = [
            'messenger_status' => 'required',
            'messenger_direct_chat_link' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::table('listings')->where('id', $id)->update(
            [
                'messenger_status' => $request->messenger_status,
                'messenger_direct_chat_link' => $request->messenger_direct_chat_link
            ]
        );

        Session::flash('success', 'اطلاعات مسنجر با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }

    public function businessHours($id)
    {
        Listing::findorFail($id);
        $permissions = businessHoursPermission($id);

        if ($permissions) {
            $information['id'] = $id;

            $information['days'] = DB::table('business_hours')
                ->Where('listing_id', $id)
                ->get();
            $language = Language::where('is_default', 1)->first();
            $information['title'] = ListingContent::where([['language_id', $language->id], ['listing_id', $id]])
                ->select('title')
                ->first();

            return view('admin.listing.business-hours', $information);
        } else {

            Session::flash('warning', "دسترسی ساعات کاری شما اعطا نشده است.");
            return redirect()->route('admin.listing_management.listing');
        }
    }
    public function updateHoliday(Request $request)
    {
        $listing = BusinessHour::findOrFail($request->holidayId);

        if ($request->holiday == 1) {
            $listing->update(['holiday' => 1]);

            Session::flash('success', 'تعطیلی با موفقیت به‌روزرسانی شد!');
        } else {
            $listing->update(['holiday' => 0]);

            Session::flash('success', 'تعطیلی با موفقیت به‌روزرسانی شد!');
        }

        return Response::json(['status' => 'success'], 200);
    }
    public function updateBusinessHours(Request $request, $id)
    {
        $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($days as $day) {

            $businessHours =  BusinessHour::where('id', $request[$day . '_id'])->first();

            if (empty($businessHours)) {
                $businessHours = new BusinessHour();
            }
            $businessHours->start_time = $request[$day . '_start_time'];
            $businessHours->end_time = $request[$day . '_end_time'];

            $businessHours->save();
        }
        Session::flash('success', 'ساعات کاری با موفقیت به‌روزرسانی شد!');
        return back();
    }
}
