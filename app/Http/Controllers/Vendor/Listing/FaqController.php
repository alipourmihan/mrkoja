<?php

namespace App\Http\Controllers\Vendor\Listing;

use App\Http\Controllers\Controller;
use App\Http\Helpers\VendorPermissionHelper;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingContent;
use App\Models\Listing\ListingFaq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class FaqController extends Controller
{
    public function index(Request $request, $id)
    {
        $vendor_id = Listing::where('id', $id)->pluck('user_id')->first();
        if ($vendor_id == Auth::user()->id) {
        $vendorId = Auth::user()->id;
        $current_package = VendorPermissionHelper::packagePermission($vendorId);

        if ($current_package != '[]') {

            $permissions = faqPermission($id);

            if ($permissions) {
                if (is_null($request->language)) {
                    $language = Language::where('is_default', 1)->first();
                } else {
                    $language = Language::query()->where('code', '=', $request->language)->firstOrFail();
                }
                $information['language'] = $language;

                $title = ListingContent::Where('listing_id', $id)
                ->where('language_id', $language->id)
                    ->first();
                $information['title'] = $title;


                $information['faqs'] = ListingFaq::Where('listing_id', $id)
                ->where('language_id', $language->id)
                    ->orderByDesc('serial_number')
                    ->get();

                $information['langs'] = Language::all();
                $information['listing_id'] = $id;
                $information['slug'] = ListingContent::where([['listing_id', $id], ['language_id', $language->id]])->pluck('slug')->first();

                return view('vendors.listing.faq.index', $information);
            } else {

                Session::flash('warning', "شما هیچ دسترسی به سوالات متداول ندارید.");
                return redirect()->route('vendor.listing_management.listing');
            }
        } else {

            Session::flash('warning', 'لطفا یک پکیج خریداری کنید تا بتوانید سوالات متداول را مدیریت کنید!');
            return redirect()->route('vendor.listing_management.listing');
            }
        } else {

            Session::flash('warning', 'شما هیچ دسترسی به سوالات متداول ندارید!');
            return redirect()->route('vendor.listing_management.listing');
        }
    }

    public function store(Request $request)
    {

        $totalFaq = ListingFaq::where('listing_id', $request->listing_id)
            ->where('language_id', $request->language_id)
            ->count();
            
        if ($totalFaq < packageTotalFaqs($request->listing_id)) {
            $rules = [
                'language_id' => 'required',
                'question' => 'required',
                'answer' => 'required',
                'serial_number' => 'required'
            ];

            $message = [
                'language_id.required' => 'The language field is required.'
            ];

            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return Response::json([
                    'errors' => $validator->getMessageBag()->toArray()
                ], 400);
            }

            ListingFaq::query()->create($request->all());

            Session::flash('success', 'سوال متداول جدید با موفقیت اضافه شد!');

            return Response::json(['status' => 'success'], 200);
        } else {
            $V = "شما می‌توانید فقط " . packageTotalFaqs($request->listing_id) . " سوال متداول اضافه کنید.";

            Session::flash('warning', $V);
            return Response::json(['status' => 'success'], 200);
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required',
            'serial_number' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $faq = ListingFaq::query()->find($request->id);

        $faq->update($request->all());

        Session::flash('success', 'سوال متداول با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function destroy($id)
    {
        $faq = ListingFaq::query()->find($id);

        $faq->delete();

        return redirect()->back()->with('success', 'سوال متداول با موفقیت حذف شد!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $faq = ListingFaq::query()->find($id);

            $faq->delete();
        }

        Session::flash('success', 'سوالات متداول با موفقیت حذف شد!');

        return Response::json(['status' => 'success'], 200);
    }
}
