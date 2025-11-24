<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryLocationPage;
use App\Models\ListingCategory;
use App\Models\Location\State;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryLocationPageController extends Controller
{
    public function index()
    {
        $pages = CategoryLocationPage::with(['category', 'state', 'city', 'neighborhood'])
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Generate frontend URLs for each page
        foreach ($pages as $page) {
            $page->frontend_url = $this->generateFrontendUrl($page);
        }

        return view('admin.category_location_pages.index', compact('pages'));
    }

    /**
     * Generate frontend URL based on category, city, and neighborhood
     */
    private function generateFrontendUrl($page)
    {
        if (!$page->category) {
            return null;
        }

        $categorySlug = $page->category->slug ?: createSlug($page->category->name);
        
        // If only category
        if (!$page->city_id) {
            return route('frontend.category_location_page.category', ['category' => $categorySlug]);
        }

        $citySlug = $page->city->slug ?: createSlug($page->city->name);
        
        // If category + city
        if (!$page->neighborhood_id) {
            return route('frontend.category_location_page.category_city', [
                'category' => $categorySlug,
                'city' => $citySlug
            ]);
        }

        // If category + city + neighborhood
        $neighborhoodSlug = $page->neighborhood->slug ?: createSlug($page->neighborhood->name);
        return route('frontend.category_location_page.category_city_neighborhood', [
            'category' => $categorySlug,
            'city' => $citySlug,
            'neighborhood' => $neighborhoodSlug
        ]);
    }

    public function create()
    {
        $categories = ListingCategory::where('status', 1)->get();
        $states = State::orderBy('name')->get();
        $cities = collect(); // Empty initially, will be loaded via AJAX
        $neighborhoods = collect(); // Empty initially, will be loaded via AJAX

        return view('admin.category_location_pages.create', compact('categories', 'states', 'cities', 'neighborhoods'));
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:category_location_pages,slug',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $page = new CategoryLocationPage();
        $page->category_id = $request->category_id;
        $page->state_id = $request->state_id;
        $page->city_id = $request->city_id;
        $page->neighborhood_id = $request->neighborhood_id;
        $page->title = $request->title;
        // Auto-generate slug from title if not provided
        if (empty($request->slug)) {
            $page->slug = createSlug($request->title);
        } else {
            $page->slug = createSlug($request->slug);
        }
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->description = $request->description;
        $page->status = $request->status ?? 1;
        $page->sort_order = $request->sort_order ?? 0;

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('assets/img/category-location-pages/');
            if (!file_exists($directory)) {
                @mkdir($directory, 0777, true);
            }
            $image->move($directory, $filename);
            $page->featured_image = $filename;
        }

        $page->save();

        Session::flash('success', 'صفحه با موفقیت ایجاد شد!');
        return redirect()->route('admin.category_location_pages.index');
    }

    public function edit($id)
    {
        $page = CategoryLocationPage::findOrFail($id);
        $categories = ListingCategory::where('status', 1)->get();
        $states = State::orderBy('name')->get();
        
        // Load cities for selected state
        $cities = $page->state_id ? City::where('state_id', $page->state_id)->orderBy('name')->get() : collect();
        
        // Load neighborhoods for selected city
        $neighborhoods = $page->city_id ? Neighborhood::where('city_id', $page->city_id)->orderBy('name')->get() : collect();

        return view('admin.category_location_pages.edit', compact('page', 'categories', 'states', 'cities', 'neighborhoods'));
    }

    public function update(Request $request, $id)
    {
        $page = CategoryLocationPage::findOrFail($id);

        $rules = [
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:category_location_pages,slug,' . $id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $page->category_id = $request->category_id;
        $page->state_id = $request->state_id;
        $page->city_id = $request->city_id;
        $page->neighborhood_id = $request->neighborhood_id;
        $page->title = $request->title;
        // Auto-generate slug from title if not provided
        if (empty($request->slug)) {
            $page->slug = createSlug($request->title);
        } else {
            $page->slug = createSlug($request->slug);
        }
        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->description = $request->description;
        $page->status = $request->status ?? 1;
        $page->sort_order = $request->sort_order ?? 0;

        if ($request->hasFile('featured_image')) {
            if ($page->featured_image) {
                @unlink(public_path('assets/img/category-location-pages/') . $page->featured_image);
            }
            $image = $request->file('featured_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('assets/img/category-location-pages/');
            @mkdir($directory, 0775, true);
            $image->move($directory, $filename);
            $page->featured_image = $filename;
        }

        $page->save();

        Session::flash('success', 'صفحه با موفقیت به‌روزرسانی شد!');
        return redirect()->route('admin.category_location_pages.index');
    }

    public function destroy($id)
    {
        $page = CategoryLocationPage::findOrFail($id);
        if ($page->featured_image) {
            @unlink(public_path('assets/img/category-location-pages/') . $page->featured_image);
        }
        $page->delete();

        Session::flash('success', 'صفحه با موفقیت حذف شد!');
        return redirect()->route('admin.category_location_pages.index');
    }
}
