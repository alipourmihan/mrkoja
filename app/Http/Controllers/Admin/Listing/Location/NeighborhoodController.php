<?php

namespace App\Http\Controllers\Admin\Listing\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Listing\ListingContent;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Location\State;
use App\Models\Location\City;
use App\Models\Location\Neighborhood;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NeighborhoodController extends Controller
{
    public function index(Request $request)
    {
        $languageColumnExists = Schema::hasTable('languages') && Schema::hasColumn('neighborhoods', 'language_id');
        $stateHasLanguageColumn = Schema::hasColumn('states', 'language_id');
        $cityHasLanguageColumn = Schema::hasColumn('cities', 'language_id');

        if ($languageColumnExists) {
            $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
            $information['states'] = $language->stateInfo()->orderByDesc('id')->get();
            $information['stateCount'] = $language->stateInfo()->orderByDesc('id')->count();
            $information['cities'] = $language->cityInfo()->orderByDesc('id')->get();
            $information['cityCount'] = $language->cityInfo()->orderByDesc('id')->count();
            $neighborhoodsQuery = Neighborhood::query();
            if (Schema::hasColumn('neighborhoods', 'language_id')) {
                $neighborhoodsQuery->where('language_id', $language->id);
            }
            $neighborhoods = $neighborhoodsQuery->orderByDesc('id')->get();
            
            // Generate frontend URLs for each neighborhood
            foreach ($neighborhoods as $neighborhood) {
                $neighborhood->frontend_url = $this->generateFrontendUrl($neighborhood);
            }
            
            $information['neighborhoods'] = $neighborhoods;
            $information['langs'] = Language::all();
            $information['language'] = $language;
            } else {
                $information['states'] = State::orderByDesc('id')->get();
                $information['stateCount'] = $information['states']->count();
                $information['cities'] = City::orderByDesc('id')->get();
                $information['cityCount'] = $information['cities']->count();
                $neighborhoods = Neighborhood::orderByDesc('id')->get();
                
                // Generate frontend URLs for each neighborhood
                foreach ($neighborhoods as $neighborhood) {
                    $neighborhood->frontend_url = $this->generateFrontendUrl($neighborhood);
                }
                
                $information['neighborhoods'] = $neighborhoods;
                $information['langs'] = Schema::hasTable('languages') ? Language::all() : collect();
                $information['language'] = $information['langs']->first();
            }
            $information['languageColumnExists'] = $languageColumnExists;
            $information['stateHasLanguageColumn'] = $stateHasLanguageColumn;
            $information['cityHasLanguageColumn'] = $cityHasLanguageColumn;

            return view('admin.listing.location.neighborhood.index', $information);
        }

        /**
         * Generate frontend URL for neighborhood
         * Shows all businesses in the neighborhood (without category filter)
         */
        private function generateFrontendUrl($neighborhood)
        {
            if (!$neighborhood->city_id) {
                return null;
            }

            // Get city
            $city = City::find($neighborhood->city_id);
            if (!$city) {
                return null;
            }

            // Get slugs or generate from names
            $citySlug = $city->slug;
            if (!$citySlug && $city->name) {
                $citySlug = createSlug($city->name);
            }
            
            $neighborhoodSlug = $neighborhood->slug;
            if (!$neighborhoodSlug && $neighborhood->name) {
                $neighborhoodSlug = createSlug($neighborhood->name);
            }
            
            if (!$citySlug || !$neighborhoodSlug) {
                return null;
            }

            // Create URL: /r/{city}/{neighborhood} (without category - shows all businesses)
            return route('frontend.category_location_page.city_neighborhood', [
                'city' => $citySlug,
                'neighborhood' => $neighborhoodSlug
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

    public function getCity(Request $request)
    {
        $cities = City::where('state_id', $request->id)->get();
        return response()->json(['status' => 'success', 'cities' => $cities], 200);
    }

    public function getByCity(Request $request)
    {
        $neighborhoods = Neighborhood::where('city_id', $request->city_id)->get();
        return response()->json(['status' => 'success', 'neighborhoods' => $neighborhoods], 200);
    }

    public function store(Request $request)
    {
        $stateQuery = State::query();
        if (Schema::hasColumn('states', 'language_id') && $request->filled('m_language_id')) {
            $stateQuery->where('language_id', $request->m_language_id);
        }
        $totalState = $stateQuery->count();
        $state = $totalState > 0;

        $cityQuery = City::query();
        if (Schema::hasColumn('cities', 'language_id') && $request->filled('m_language_id')) {
            $cityQuery->where('language_id', $request->m_language_id);
        }
        $totalCity = $cityQuery->count();
        $city = $totalCity > 0;

        $rules = [
            'm_language_id' => Schema::hasColumn('neighborhoods', 'language_id') ? 'required' : 'nullable',
            'name' => 'required',
            'state_id' => $state ? 'required' : '',
            'city_id' => $city ? 'required' : '',
        ];

        $messages = [
            'm_language_id.required' => 'فیلد زبان الزامی است.',
            'state_id.required' => 'فیلد استان الزامی است.',
            'city_id.required' => 'فیلد شهر الزامی است.',
            'name.required' => 'فیلد نام الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $neighborhood = new Neighborhood();

        if (Schema::hasColumn('neighborhoods', 'language_id')) {
            $neighborhood->language_id = $request->m_language_id;
        }
        $neighborhood->state_id = $request->state_id;
        $neighborhood->city_id = $request->city_id;
        if (Schema::hasColumn('neighborhoods', 'slug')) {
            if (empty($request->slug)) {
                $neighborhood->slug = createSlug($request->name);
            } else {
                $neighborhood->slug = createSlug($request->slug);
            }
        }
        $neighborhood->name = $request->name;

        $neighborhood->save();

        Session::flash('success', 'محله با موفقیت ذخیره شد!');

        return response()->json(['status' => 'success'], 200);
    }

    public function update(Request $request)
    {
        $stateExists = State::query()->where('id', $request->state_id)->exists();
        $cityExists = City::query()->where('id', $request->city_id)->exists();

        $rules = [
            'name' => 'required',
            'state_id' => $stateExists ? 'required' : '',
            'city_id' => $cityExists ? 'required' : '',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $neighborhood = Neighborhood::find($request->id);

        $in = $request->all();
            if (Schema::hasColumn('neighborhoods', 'slug')) {
                if (empty($request->slug)) {
                    $in['slug'] = createSlug($request->name);
                } else {
                    $in['slug'] = createSlug($request->slug);
                }
            }

        if (!Schema::hasColumn('neighborhoods', 'language_id')) {
            unset($in['language_id']);
        }

        if (!State::query()->where('id', $request->state_id)->exists()) {
            $in['state_id'] = null;
        }
        if (!City::query()->where('id', $request->city_id)->exists()) {
            $in['city_id'] = null;
        }

        $neighborhood->update($in);

        Session::flash('success', 'محله با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $neighborhood = Neighborhood::query()->find($id);

        $listing_content = ListingContent::Where('neighborhood_id', $id)->get();

        if (count($listing_content) > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام آگهی‌های این محله را حذف کنید!');
        } else {
            $neighborhood->delete();
            return redirect()->back()->with('success', 'محله با موفقیت حذف شد!');
        }
        return redirect()->back()->with('success', 'محله با موفقیت حذف شد!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        $errorOccurred = false;
        foreach ($ids as $id) {
            $neighborhood = Neighborhood::query()->find($id);
            $listing_content = ListingContent::Where('neighborhood_id', $id)->get();

            if (count($listing_content) > 0) {
                $errorOccurred = true;
                break;
            } else {
                $neighborhood->delete();
            }
        }
        if ($errorOccurred == true) {
            Session::flash('warning', 'ابتدا تمام آگهی‌های این محله‌ها را حذف کنید!');
        } else {
            Session::flash('success', 'اطلاعات انتخاب شده با موفقیت حذف شدند!');
        }
        return Response::json(['status' => 'success'], 200);
    }
}
