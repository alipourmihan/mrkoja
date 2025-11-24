<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\ListingCategory;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $languageCode = $request->get('language');

        if (!$languageCode) {
            $language = Language::where('is_default', 1)->first() ?? Language::first();

            if (is_null($language)) {
                abort(404, 'هیچ زبانی تنظیم نشده است.');
            }

            return redirect()->route('admin.listing_specification.categories', ['language' => $language->code]);
        }

        $language = Language::where('code', $languageCode)->first();

        if (is_null($language)) {
            $language = Language::where('is_default', 1)->first() ?? Language::first();

            if (is_null($language)) {
                abort(404, 'هیچ زبانی تنظیم نشده است.');
            }

            return redirect()->route('admin.listing_specification.categories', ['language' => $language->code]);
        }

        $information['language'] = $language;

        // Get Persian language (default)
        $persianLanguage = Language::where('code', 'fa')->first();
        if (!$persianLanguage) {
            $persianLanguage = Language::where('is_default', 1)->first() ?? Language::first();
        }

        // Get all categories (only Persian, no language filter needed)
        $information['categories'] = ListingCategory::where('language_id', $persianLanguage->id)
            ->orWhereNull('language_id')
            ->orderBy('parent_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.listing.category.index', $information);
    }

    public function store(Request $request)
    {
        // Get Persian language ID
        $persianLanguage = Language::where('code', 'fa')->first();
        if (!$persianLanguage) {
            $persianLanguage = Language::where('is_default', 1)->first() ?? Language::first();
        }

        $rules = [
            'icon' => 'required',
            'name' => [
                'required',
                Rule::unique('listing_categories')->where(function ($query) use ($persianLanguage) {
                    return $query->where('language_id', $persianLanguage->id);
                }),
                'max:255',
            ],
            'slug' => [
                'nullable',
                'max:255',
                Rule::unique('listing_categories')->where(function ($query) use ($persianLanguage) {
                    return $query->where('language_id', $persianLanguage->id);
                }),
            ],
            'parent_id' => 'nullable|exists:listing_categories,id',
            'status' => 'required|numeric',
            'description' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $in = $request->all();
        
        // Set language to Persian
        $in['language_id'] = $persianLanguage->id;
        
        // Generate slug if not provided
        if (empty($in['slug'])) {
            $in['slug'] = createSlug($request->name);
        } else {
            $in['slug'] = createSlug($request->slug);
        }
        
        // Prevent self-parent
        if (isset($in['parent_id']) && $in['parent_id'] == $request->id) {
            $in['parent_id'] = null;
        }

        ListingCategory::create($in);

        Session::flash('success', 'دسته‌بندی جدید با موفقیت اضافه شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function update(Request $request)
    {
        // Get Persian language ID
        $persianLanguage = Language::where('code', 'fa')->first();
        if (!$persianLanguage) {
            $persianLanguage = Language::where('is_default', 1)->first() ?? Language::first();
        }

        $rules = [
            'icon' => 'required',
            'name' => [
                'required',
                Rule::unique('listing_categories')->where(function ($query) use ($persianLanguage) {
                    return $query->where('language_id', $persianLanguage->id);
                })->ignore($request->id, 'id'),
                'max:255',
            ],
            'slug' => [
                'nullable',
                'max:255',
                Rule::unique('listing_categories')->where(function ($query) use ($persianLanguage) {
                    return $query->where('language_id', $persianLanguage->id);
                })->ignore($request->id, 'id'),
            ],
            'parent_id' => 'nullable|exists:listing_categories,id',
            'status' => 'required|numeric',
            'description' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $category = ListingCategory::find($request->id);

        $in = $request->all();
        
        // Set language to Persian
        $in['language_id'] = $persianLanguage->id;

        // Generate slug if not provided
        if (empty($in['slug'])) {
            $in['slug'] = createSlug($request->name);
        } else {
            $in['slug'] = createSlug($request->slug);
        }
        
        // Prevent self-parent and circular reference
        if (isset($in['parent_id'])) {
            // Check if trying to set parent to itself
            if ($in['parent_id'] == $request->id) {
                $in['parent_id'] = null;
            } else {
                // Check for circular reference (prevent child from becoming parent of its own parent)
                $parent = ListingCategory::find($in['parent_id']);
                if ($parent && $parent->parent_id == $request->id) {
                    $in['parent_id'] = $category->parent_id; // Keep original parent
                }
            }
        }

        $category->update($in);

        Session::flash('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $category = ListingCategory::find($id);
        
        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام دسته‌بندی‌های زیرمجموعه را حذف کنید!');
        }
        
        $listingContents = $category->listing_contents()->get();

        if (count($listingContents) > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام کسب‌وکارهای این دسته‌بندی را حذف کنید!');
        } else {
            $category->delete();

            return redirect()->back()->with('success', 'دسته‌بندی با موفقیت حذف شد!');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        $errorOccurred = false;
        $hasChildren = false;

        foreach ($ids as $id) {
            $category = ListingCategory::find($id);
            
            // Check if category has children
            if ($category->children()->count() > 0) {
                $hasChildren = true;
                break;
            }
            
            $listingContents = $category->listing_contents()->get();

            if (count($listingContents) > 0) {
                $errorOccurred = true;
                break;
            } else {
                $category->delete();
            }
        }

        if ($hasChildren) {
            Session::flash('warning', 'ابتدا تمام دسته‌بندی‌های زیرمجموعه را حذف کنید!');
        } elseif ($errorOccurred == true) {
            Session::flash('warning', 'ابتدا تمام کسب‌وکارهای این دسته‌بندی‌ها را حذف کنید!');
        } else {
            Session::flash('success', 'دسته‌بندی‌ها با موفقیت حذف شدند!');
        }

        return Response::json(['status' => 'success'], 200);
    }
}
