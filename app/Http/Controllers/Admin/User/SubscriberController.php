<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $searchKey = null;

        if ($request->filled('email')) {
            $searchKey = $request['email'];
        }

        $subscribers = Subscriber::query()->when($searchKey, function ($query, $searchKey) {
            return $query->where('email_id', 'like', '%' . $searchKey . '%');
        })
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.end-user.subscriber.index', compact('subscribers'));
    }

    public function destroy($id)
    {
        try {
            Subscriber::query()->findOrFail($id)->delete();

            return redirect()->back()->with('success', 'فروشنده با موفقیت حذف شد!');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('warning', 'متاسفانه، ایمیل یافت نشد!');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            Subscriber::query()->find($id)->delete();
        }

        Session::flash('success', 'فروشندگان با موفقیت حذف شدند!');

        return response()->json(['status' => 'success'], 200);
    }

    public function writeEmail()
    {
        return view('admin.end-user.subscriber.write-email');
    }

    public function prepareEmail(Request $request)
    {
        $subscribers = Subscriber::all();

        if (count($subscribers) == 0) {
            Session::flash('warning', 'No subscriber found!');

            return redirect()->back();
        }

        $rules = [
            'subject' => 'required',
            'message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $mailData['subject'] = $request['subject'];
        $mailData['body'] = $request['message'];

        foreach ($subscribers as $subscriber) {
            $mailData['recipient'] = $subscriber->email_id;

            BasicMailer::sendMail($mailData);
        }

        Session::flash('success', 'ایمیل با موفقیت به همه فروشندگان ارسال شد!');

        return redirect()->back();
    }
}
