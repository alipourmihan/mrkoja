<?php

namespace App\Http\Controllers\Admin\Listing\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Listing\ListingContent;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $languageColumnExists = Schema::hasTable('languages') && Schema::hasColumn('cities', 'language_id');
        $stateHasLanguageColumn = Schema::hasColumn('states', 'language_id');

        if ($languageColumnExists) {
            $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
            $information['countries'] = $language->countryInfo()->orderByDesc('id')->get();
            $information['states'] = $language->stateInfo()->orderByDesc('id')->get();
            $information['stateCount'] = $language->stateInfo()->orderByDesc('id')->count();
            $cities = $language->cityInfo()->orderByDesc('id')->get();
            
            // Generate frontend URLs for each city
            foreach ($cities as $city) {
                $city->frontend_url = $this->generateFrontendUrl($city);
            }
            
            $information['cities'] = $cities;
            $information['langs'] = Language::all();
            $information['language'] = $language;
        } else {
            $information['countries'] = Country::orderByDesc('id')->get();
            $information['states'] = State::orderByDesc('id')->get();
            $information['stateCount'] = $information['states']->count();
            $cities = City::orderByDesc('id')->get();
            
            // Generate frontend URLs for each city
            foreach ($cities as $city) {
                $city->frontend_url = $this->generateFrontendUrl($city);
            }
            
            $information['cities'] = $cities;
            $information['langs'] = Schema::hasTable('languages') ? Language::all() : collect();
            $information['language'] = $information['langs']->first();
        }
        $information['languageColumnExists'] = $languageColumnExists;
        $information['stateHasLanguageColumn'] = $stateHasLanguageColumn;

        return view('admin.listing.location.city.index', $information);
    }

    /**
     * Generate frontend URL for city
     * Uses first active category to create URL
     */
    private function generateFrontendUrl($city)
    {
        // Get slug from city or generate from name
        $citySlug = $city->slug;
        if (!$citySlug && $city->name) {
            $citySlug = createSlug($city->name);
        }
        
        if (!$citySlug) {
            return null;
        }

        // Get first active category
        $category = \App\Models\ListingCategory::where('status', 1)->first();
        if (!$category) {
            return null;
        }

        // Create URL: /r/{city} (without category - shows all businesses)
        return route('frontend.category_location_page.city', [
            'city' => $citySlug
        ]);
    }
    public function getCountry($language_id)
    {
        $statesQuery = State::query();

        if (Schema::hasColumn('states', 'language_id')) {
            $statesQuery->where('language_id', $language_id);
        }

        $states = $statesQuery->get();

        return response()->json([
            'status' => 'success',
            'states' => $states
        ], 200);
    }

    public function getState($country)
    {
        $states = State::where('country_id', $country)->get();
        return response()->json(['status' => 'success', 'states' => $states], 200);
    }

    public function store(Request $request)
    {
        $stateQuery = State::query();
        if (Schema::hasColumn('states', 'language_id') && $request->filled('m_language_id')) {
            $stateQuery->where('language_id', $request->m_language_id);
        }
        $totalState = $stateQuery->count();
        $state = $totalState > 0;

        $rules = [
            'm_language_id' => Schema::hasColumn('cities', 'language_id') ? 'required' : 'nullable',
            'name' => 'required',
            'state_id' => $state ? 'required' : '',
        ];

        $messages = [
            'm_language_id.required' => 'فیلد زبان الزامی است.',
            'state_id.required' => 'فیلد استان الزامی است.',
            'name.required' => 'فیلد نام الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $state = new City();

        if (Schema::hasColumn('cities', 'language_id')) {
            $state->language_id = $request->m_language_id;
        }
        if (Schema::hasColumn('cities', 'country_id')) {
            $state->country_id = null;
        }
        $state->state_id = $request->state_id;
        if (Schema::hasColumn('cities', 'slug')) {
            if (empty($request->slug)) {
                $state->slug = createSlug($request->name);
            } else {
                $state->slug = createSlug($request->slug);
            }
        }
        $state->name = $request->name;

        $state->save();

        Session::flash('success', 'شهر با موفقیت ذخیره شد!');

        return response()->json(['status' => 'success'], 200);
    }

    public function update(Request $request)
    {
        $stateExists = false;
        if ($request->filled('state_id')) {
            $stateExists = State::query()->where('id', $request->state_id)->exists();
        }

        $rules = [
            'name' => 'required',
            'state_id' => $stateExists ? 'required' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $city = City::find($request->id);

        if (!$city) {
            return Response::json([
                'errors' => ['id' => ['شهر یافت نشد!']]
            ], 404);
        }

        // Prepare update data
        $updateData = [
            'name' => $request->name,
        ];
        
        // Handle slug
        if (Schema::hasColumn('cities', 'slug')) {
            if (empty($request->slug)) {
                $updateData['slug'] = createSlug($request->name);
            } else {
                $updateData['slug'] = createSlug($request->slug);
            }
        }

        // Handle state_id
        if ($request->filled('state_id')) {
            if (State::query()->where('id', $request->state_id)->exists()) {
                $updateData['state_id'] = $request->state_id;
            } else {
                $updateData['state_id'] = null;
            }
        }
        
        if (Schema::hasColumn('cities', 'country_id')) {
            $updateData['country_id'] = null;
        }

        $city->update($updateData);

        Session::flash('success', 'شهر با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $City = City::query()->find($id);

        $listing_content = ListingContent::Where('city_id', $id)->get();

        if (count($listing_content) > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام آگهی‌های این شهر را حذف کنید!');
        } else {
            $City->delete();
            return redirect()->back()->with('success', 'شهر با موفقیت حذف شد!');
        }
        return redirect()->back()->with('success', 'شهر با موفقیت حذف شد!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        $errorOccurred = false;
        foreach ($ids as $id) {
            $City = City::query()->find($id);
            $listing_content = ListingContent::Where('city_id', $id)->get();

            if (count($listing_content) > 0) {
                $errorOccurred = true;
                break;
            } else {
                $City->delete();
            }
        }
        if ($errorOccurred == true) {
            Session::flash('warning', 'ابتدا تمام آگهی‌های این شهرها را حذف کنید!');
        } else {
            Session::flash('success', 'اطلاعات انتخاب شده با موفقیت حذف شدند!');
        }
        return Response::json(['status' => 'success'], 200);
    }
}
