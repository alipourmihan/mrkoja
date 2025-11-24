<?php

namespace App\Http\Controllers\Admin\Listing\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Listing\ListingContent;
use App\Models\Location\City;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Location\Country;
use App\Models\Location\State;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StateController extends Controller
{
    public function index(Request $request)
    {

        $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
        $information['countries'] = $language->countryInfo()->orderByDesc('id')->get();
        $states = $language->stateInfo()->orderByDesc('id')->get();
        
        // Generate frontend URLs for each state
        foreach ($states as $state) {
            $state->frontend_url = $this->generateFrontendUrl($state);
        }
        
        $information['states'] = $states;
        $information['langs'] = Language::all();
        $information['language'] = $language;

        return view('admin.listing.location.state.index', $information);
    }

    /**
     * Generate frontend URL for state
     * Uses first active category to create URL
     */
    private function generateFrontendUrl($state)
    {
        // Get slug from state or generate from name
        $stateSlug = $state->slug;
        if (!$stateSlug && $state->name) {
            $stateSlug = createSlug($state->name);
        }
        
        if (!$stateSlug) {
            return null;
        }

        // Get first active category
        $category = \App\Models\ListingCategory::where('status', 1)->first();
        if (!$category) {
            return null;
        }

        $categorySlug = $category->slug ?: createSlug($category->name);

        // For state, we'll use the listings page with state filter as query parameter
        return route('frontend.listings') . '?state=' . urlencode($stateSlug);
    }
    public function getCountry($language_id)
    {
        $countries = applyLanguageFilter(Country::query(), 'countries', $language_id)->get();
        $states = applyLanguageFilter(State::query(), 'states', $language_id)->get();

        return response()->json([
            'status' => 'success',
            'countries' => $countries,
            'states' => $states
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'm_language_id' => 'required',
            'name' => 'required',
        ];

        $messages = [
            'm_language_id.required' => 'فیلد زبان الزامی است.',
            'name.required' => 'فیلد نام الزامی است.'
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $state = new State();

        // Use the correct field names here
        if (\Illuminate\Support\Facades\Schema::hasColumn('states', 'language_id')) {
        $state->language_id = $request->m_language_id;
        }
        $state->name = $request->name;
        if (\Illuminate\Support\Facades\Schema::hasColumn('states', 'country_id')) {
            $state->country_id = null;
        }
        // Auto-generate slug from name if not provided
        if (\Illuminate\Support\Facades\Schema::hasColumn('states', 'slug')) {
            if (empty($request->slug)) {
                $state->slug = createSlug($request->name);
            } else {
                $state->slug = createSlug($request->slug);
            }
        }
        $state->save();

        Session::flash('success', 'استان با موفقیت ذخیره شد!');

        return response()->json(['status' => 'success'], 200);
    }


    public function update(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $state = State::query()->find($request->id);
        $state->name = $request->name;
        // Auto-generate slug from name if not provided
        if (\Illuminate\Support\Facades\Schema::hasColumn('states', 'slug')) {
            if (empty($request->slug)) {
                $state->slug = createSlug($request->name);
            } else {
                $state->slug = createSlug($request->slug);
            }
        }
        $state->save();

        Session::flash('success', 'استان با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $State = State::query()->find($id);

        $city = City::Where('state_id', $id)->get();
        $listing_content = ListingContent::Where('state_id', $id)->get();

        if (count($city) > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام شهرهای این استان را حذف کنید!');
        } else {

            if (count($listing_content) > 0) {
                return redirect()->back()->with('warning', 'ابتدا تمام آگهی‌های این استان را حذف کنید!');
            } else {

                $State->delete();
                return redirect()->back()->with('success', 'استان با موفقیت حذف شد!');
            }
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        $errorOccurred = false;
        $errorOccurred2 = false;
        foreach ($ids as $id) {
            $State = State::query()->find($id);
            $city = City::Where('state_id', $id)->get();
            $listing_content = ListingContent::Where('state_id', $id)->get();

            if (count($city) > 0) {

                $errorOccurred = true;
                break;
            } else {
                if (count($listing_content) > 0) {
                    $errorOccurred2 = true;
                    break;
                } else {
                    $State->delete();
                }
            }
        }

        if ($errorOccurred == true) {
            Session::flash('warning', 'ابتدا تمام شهرهای این استان‌ها را حذف کنید!');
        } elseif ($errorOccurred2 == true) {
            Session::flash('warning', 'ابتدا تمام آگهی‌های این استان‌ها را حذف کنید!');
        } else {
            Session::flash('success', 'اطلاعات انتخاب شده با موفقیت حذف شدند!');
        }
        return Response::json(['status' => 'success'], 200);
    }
}
