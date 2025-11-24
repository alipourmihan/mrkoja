<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MAilSetController extends Controller
{
    public function mailToAdmin()
    {
        $data = DB::table('users')->where('id', Auth::user()->id)->select('to_mail')->first();

        return view('vendors.email.mail-to-admin', ['data' => $data]);
    }

    public function updateMailToAdmin(Request $request)
    {

        $rule = [
            'to_mail' => 'required'
        ];

        $message = [
            'to_mail.required' => 'فیلد آدرس ایمیل الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rule, $message);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        DB::table('users')->where('id', Auth::user()->id)->update(
            ['to_mail' => $request->to_mail]
        );

        Session::flash('success', 'اطلاعات ایمیل با موفقیت به‌روزرسانی شد!');

        return redirect()->back();
    }
}
