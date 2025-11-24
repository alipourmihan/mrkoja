<?php

namespace App\Http\Controllers\Admin\FeaturedListing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FeaturedListingCharge;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class ChargeController extends Controller
{
    public function index(Request $request)
    {

        $information['charges'] = FeaturedListingCharge::all();

        return view('admin.featured-listing.charge.index', $information); 
    }
    public function store(Request $request)
    {
        $rules = [
            'price' => 'required',
            'days' => 'required',
        ];

        $message = [
            'price.required' => 'فیلد قیمت الزامی است.',
            'days.required' => 'فیلد روزها الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }
        FeaturedListingCharge::query()->create($request->except('language'));

        Session::flash('success', 'هزینه با موفقیت ذخیره شد!');

        return Response::json(['status' => 'success'], 200);
    }
    public function update(Request $request)
    {
        $rules = [
            'price' => 'required',
            'days' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $chargeInfo = FeaturedListingCharge::query()->find($request->id);

        $chargeInfo->update([
            'price' => $request->price,
            'days' => $request->days,
        ]);


        Session::flash('success', 'هزینه با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }
    public function destroy($id)
    {

        $charge = FeaturedListingCharge::query()->find($id);

        $charge->delete();

        return redirect()->back()->with('success', 'هزینه با موفقیت حذف شد!');
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        foreach ($ids as $id) {
            $charge = FeaturedListingCharge::query()->find($id);

            $charge->delete();
        }

        Session::flash('success', 'اطلاعات انتخاب شده با موفقیت حذف شدند!');

        return Response::json(['status' => 'success'], 200);
    }
}
