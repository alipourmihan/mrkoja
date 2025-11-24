<?php

namespace App\Http\Controllers\Admin\Journal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal\BlogCategory;
use App\Models\Language;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $language = Language::where('code', $request->language)->firstOrFail();
        $information['language'] = $language;

        $information['categories'] = $language->blogCategory()->orderByDesc('id')->get();

        $information['langs'] = Language::all();

        return view('admin.journal.category.index', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'language_id' => 'required',
            'name' => 'required|unique:blog_categories|max:255',
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $message = [
            'language_id.required' => 'فیلد زبان الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        BlogCategory::create($request->except('slug') + [
            'slug' => createSlug($request->name)
        ]);

        Session::flash('success', 'دسته‌بندی وبلاگ جدید با موفقیت اضافه شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'max:255',
                Rule::unique('blog_categories', 'name')->ignore($request->id, 'id')
            ],
            'status' => 'required|numeric',
            'serial_number' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $category = BlogCategory::find($request->id);

        $category->update($request->except('slug') + [
            'slug' => createSlug($request->name)
        ]);

        Session::flash('success', 'دسته‌بندی وبلاگ با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $category = BlogCategory::find($id);
        $blogInformations = $category->blogInfo()->get();

        if (count($blogInformations) > 0) {
            return redirect()->back()->with('warning', 'ابتدا تمام وبلاگ‌های این دسته‌بندی را حذف کنید!');
        } else {
            $category->delete();

            return redirect()->back()->with('success', 'دسته‌بندی وبلاگ با موفقیت حذف شد!');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        $errorOccurred = false;

        foreach ($ids as $id) {
            $category = BlogCategory::find($id);
            $blogInformations = $category->blogInfo()->get();

            if (count($blogInformations) > 0) {
                $errorOccurred = true;
                break;
            } else {
                $category->delete();
            }
        }

        if ($errorOccurred == true) {
            Session::flash('warning', 'ابتدا تمام وبلاگ‌های این دسته‌بندی‌ها را حذف کنید!');
        } else {
            Session::flash('success', 'دسته‌بندی‌های وبلاگ با موفقیت حذف شدند!');
        }

        return Response::json(['status' => 'success'], 200);
    }
}
