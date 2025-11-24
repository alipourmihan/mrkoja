<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Language;
use App\Models\Listing\Listing;
use App\Models\Membership;
use App\Models\Package;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\VendorInfo;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use Carbon\Carbon;
use Config;
use stdClass;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    //signup
    public function signup()
    {
        if (Auth::check() && Auth::user()->isBusiness()) {
            return redirect()->route('vendor.dashboard');
        } else {
            $misc = new MiscellaneousController();

            $language = $misc->getLanguage();

            $information['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_signup', 'meta_description_vendor_signup')->first();

            $information['pageHeading'] = $misc->getPageHeading($language);

            $information['recaptchaInfo'] = Basic::select('google_recaptcha_status')->first();

            $information['bgImg'] = $misc->getBreadcrumb();

            return view('frontend.vendor.auth.register', $information);
        }
    }
    //create
    public function create(Request $request)
    {
        $admin = Admin::select('username')->first();
        $admin_username = $admin->username;
        $rules = [
            'username' => "required|unique:vendors|not_in:$admin_username",
            'email' => 'required|email|unique:vendors',
            'password' => 'required|confirmed|min:6',
        ];

        $info = Basic::select('google_recaptcha_status')->first();
        if ($info->google_recaptcha_status == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [];

        if ($info->google_recaptcha_status == 1) {
            $messages['g-recaptcha-response.required'] = 'لطفا تأیید کنید که ربات نیستید.';
            $messages['g-recaptcha-response.captcha'] = 'خطای کپچا! لطفا بعدا دوباره امتحان کنید یا با مدیر سایت تماس بگیرید.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->username == 'admin') {
            Session::flash('username_error', 'شما نمی‌توانید از admin به عنوان نام کاربری استفاده کنید!');
            return redirect()->back();
        }


        $in = $request->all();
        $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval')->first();

        if ($setting->vendor_email_verification == 1) {
            // first, get the mail template information from db
            $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->first();

            $mailSubject = $mailTemplate->mail_subject;
            $mailBody = $mailTemplate->mail_body;

            // second, send a password reset link to user via email
            $info = DB::table('basic_settings')
                ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
                ->first();

            $token =  $request->email;

            $link = '<a href=' . url("vendor/email/verify?token=" . $token) . '>Click Here</a>';

            $mailBody = str_replace('{username}', $request->username, $mailBody);
            $mailBody = str_replace('{verification_link}', $link, $mailBody);
            $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

            $data = [
                'subject' => $mailSubject,
                'to' => $request->email,
                'body' => $mailBody,
            ];

            // if smtp status == 1, then set some value for PHPMailer
            if ($info->smtp_status == 1) {
                try {
                    $smtp = [
                        'transport' => 'smtp',
                        'host' => $info->smtp_host,
                        'port' => $info->smtp_port,
                        'encryption' => $info->encryption,
                        'username' => $info->smtp_username,
                        'password' => $info->smtp_password,
                        'timeout' => null,
                        'auth_mode' => null,
                    ];
                    Config::set('mail.mailers.smtp', $smtp);
                } catch (\Exception $e) {
                    Session::flash('error', $e->getMessage());
                    return back();
                }

                // finally add other informations and send the mail
                try {
                    if ($info->smtp_status == 1) {
                        Mail::send([], [], function (Message $message) use ($data, $info) {
                            $fromMail = $info->from_mail;
                            $fromName = $info->from_name;
                            $message->to($data['to'])
                                ->subject($data['subject'])
                                ->from($fromMail, $fromName)
                                ->html($data['body'], 'text/html');
                        });
                    }

                    Session::flash('success', 'ایمیل تایید برای شما ارسال شده است');
                } catch (\Exception $e) {
                    Session::flash('message', 'ایمیل ارسال نشد!');
                    Session::flash('alert-type', 'error');
                    return redirect()->back();
                }
            } else {
                $in['email_verified_at'] = now();
            }

            $in['status'] = 0;
        } else {
            $in['email_verified_at'] = now();
            Session::flash('success', 'ثبت نام با موفقیت انجام شد. لطفا وارد شوید');
        }
        if ($setting->vendor_admin_approval == 1) {
            $in['status'] = 0;
        }

        if ($setting->vendor_admin_approval == 0 && $setting->vendor_email_verification == 0) {
            $in['status'] = 1;
        }

        $in['password'] = Hash::make($request->password);
        $in['role'] = 'business';
        $vendor = User::create($in);


        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $in['language_id'] = $language->id;
        $in['user_id'] = $vendor->id;
        VendorInfo::create($in);

        return redirect()->route('vendor.login');
    }

    //login
    public function login(Request $request)
    {

        if (Auth::check() && Auth::user()->isBusiness()) {
            return redirect()->route('vendor.dashboard');
        } else {

            $misc = new MiscellaneousController();

            $language = $misc->getLanguage();

            $information['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_login', 'meta_description_vendor_login')->first();

            $information['pageHeading'] = $misc->getPageHeading($language);

            $information['bgImg'] = $misc->getBreadcrumb();

            $information['bs'] = Basic::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();

            if ($request->redirectPath == 'buy_plan') {
                Session::put('redirectTo', 'buy_plan');
            }
            if ($request->package) {
                Session::put('package', $request->package);
            }

            return view('frontend.vendor.auth.login', $information);
        }
    }

    //authenticate
    public function authentication(Request $request)
    {
        if ($request->session()->has('redirectTo')) {
            $redirectURL = $request->session()->get('redirectTo');
        } else {
            $redirectURL = null;
        }
        if ($request->session()->has('package')) {
            $packageIds = $request->session()->get('package');
        } else {
            $packageIds = null;
        }

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $info = Basic::select('google_recaptcha_status')->first();
        if ($info->google_recaptcha_status == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [];

        if ($info->google_recaptcha_status == 1) {
            $messages['g-recaptcha-response.required'] = 'لطفا تأیید کنید که ربات نیستید.';
            $messages['g-recaptcha-response.captcha'] = 'خطای کپچا! لطفا بعدا دوباره امتحان کنید یا با مدیر سایت تماس بگیرید.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            $authAdmin = Auth::user();


            $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval')->first();

            // check whether the vendor's account is active or not
            if ($setting->vendor_email_verification == 1 && $authAdmin->email_verified_at == NULL) {
                Session::flash('error', 'لطفا ایمیل خود را تایید کنید');

                // logout auth vendor as condition not satisfied
                Auth::logout();

                return redirect()->back();
            } elseif ($setting->vendor_email_verification == 0 && $setting->vendor_admin_approval == 1) {
                Session::put('secret_login', 0);
                $request->session()->forget('redirectTo');
                $request->session()->forget('package');
                return redirect()->route('vendor.dashboard');
            } else {
                Session::put('secret_login', 0);
                $request->session()->forget('redirectTo');
                $request->session()->forget('package');
                return redirect()->route('vendor.dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'نام کاربری یا رمز عبور نادرست است');
        }
    }
    //confirm_email'
    public function confirm_email()
    {
        $email = request()->input('token');
        $user = User::where('email', $email)->where('role', 'business')->first();
        if ($user) {
            $user->email_verified_at = now();
            $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_admin_approval')->first();
            if ($setting->vendor_admin_approval != 1) {
                $user->status = 1;
            }
            $user->save();
            Auth::login($user);
            return redirect()->route('vendor.dashboard');
        }
        return redirect()->route('auth.login');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        Session::forget('secret_login');
        return redirect()->route('vendor.login');
    }

    public function dashboard()
    {
        $vendor_id = Auth::user()->id;
        $information['totalListings'] = Listing::query()->where('user_id', $vendor_id)->count();

        $information['admin_setting'] = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_admin_approval', 'admin_approval_notice')->first();

        $support_status = DB::table('support_ticket_statuses')->first();
        if ($support_status->support_ticket_status == 'active') {
            $total_support_tickets = SupportTicket::where([['user_id', Auth::user()->id], ['user_type', 'vendor']])->get()->count();
            $information['total_support_tickets'] = $total_support_tickets;
        }
        $information['support_status'] = $support_status;
        $information['admin_setting'] = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_admin_approval', 'admin_approval_notice')->first();

        //total car posts
        $totalCars = DB::table('listings')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('user_id', $vendor_id)
            ->whereYear('created_at', '=', date('Y'))
            ->get();
        //total car visitors
        $totalVisitors = DB::table('visitors')
            ->select(DB::raw('month(date) as month'), DB::raw('count(id) as total'))
            ->groupBy('month')
            ->where('user_id', $vendor_id)
            ->whereYear('date', '=', date('Y'))
            ->get();

        $months = [];
        $totalCarArr = [];
        $totalVisitorArr = [];

        //event icome calculation
        for ($i = 1; $i <= 12; $i++) {
            // get all 12 months name
            $monthNum = $i;
            $monthName = jdate_en('F', jmktime(0, 0, 0, $monthNum, 1));
            $date = date('n', jmktime(0, 0, 0, $monthNum, 1));
            array_push($months, $monthName);
         
          
            // get all 12 months's car posts
            $carFound = false;
            foreach ($totalCars as $totalCar) {
                if ($totalCar->month == $date) {
                    $carFound = true;
                    array_push($totalCarArr, $totalCar->total);
                    break;
                }
            }
            if ($carFound == false) {
                array_push($totalCarArr, 0);
            }

            // get all 12 months's visitors
            $visitorFound = false;
            foreach ($totalVisitors as $totalVisitor) {
                if ($totalCar->month == $date) {
                    $visitorFound = true;
                    array_push($totalVisitorArr, $totalVisitor->total);
                    break;
                }
            }
            if ($visitorFound == false) {
                array_push($totalVisitorArr, 0);
            }
        }
        // die();
        $payment_logs = Membership::where('user_id', $vendor_id)->get()->count();

        //package start
        $nextPackageCount = Membership::query()->where([
            ['user_id', Auth::user()->id],
            ['expire_date', '>=', Carbon::now()->toDateString()]
        ])->whereYear('start_date', '<>', '9999')->where('status', '<>', 2)->count();
        //current package
        $information['current_membership'] = Membership::query()->where([
            ['user_id', Auth::user()->id],
            ['start_date', '<=', Carbon::now()->toDateString()],
            ['expire_date', '>=', Carbon::now()->toDateString()]
        ])->where('status', 1)->whereYear('start_date', '<>', '9999')->first();
        if ($information['current_membership'] != null) {
            $countCurrMem = Membership::query()->where([
                ['user_id', Auth::user()->id],
                ['start_date', '<=', Carbon::now()->toDateString()],
                ['expire_date', '>=', Carbon::now()->toDateString()]
            ])->where('status', 1)->whereYear('start_date', '<>', '9999')->count();
            if ($countCurrMem > 1) {
                $information['next_membership'] = Membership::query()->where([
                    ['user_id', Auth::user()->id],
                    ['start_date', '<=', Carbon::now()->toDateString()],
                    ['expire_date', '>=', Carbon::now()->toDateString()]
                ])->where('status', '<>', 2)->whereYear('start_date', '<>', '9999')->orderBy('id', 'DESC')->first();
            } else {
                $information['next_membership'] = Membership::query()->where([
                    ['user_id', Auth::user()->id],
                    ['start_date', '>', $information['current_membership']->expire_date]
                ])->whereYear('start_date', '<>', '9999')->where('status', '<>', 2)->first();
            }
            $information['next_package'] = $information['next_membership'] ? Package::query()->where('id', $information['next_membership']->package_id)->first() : null;
        } else {
            $information['next_package'] = null;
        }
        $information['current_package'] = $information['current_membership'] ? Package::query()->where('id', $information['current_membership']->package_id)->first() : null;
        $information['package_count'] = $nextPackageCount;
        //package start end

        $information['monthArr'] = $months;
        $information['totalCarsArr'] = $totalCarArr;
        $information['visitorArr'] = $totalVisitorArr;
        $information['payment_logs'] = $payment_logs;

        return view('vendors.index', $information);
    }

    //change_password
    public function change_password()
    {
        return view('frontend.vendor.auth.change-password');
    }

    //update_password
    public function updated_password(Request $request)
    {
        $rules = [
            'current_password' => [
                'required',
                new MatchOldPasswordRule('vendor')

            ],
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'تأیید رمز عبور مطابقت ندارد.',
            'new_password_confirmation.required' => 'فیلد تأیید رمز عبور جدید الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $vendor = Auth::user();

        $vendor->update([
            'password' => Hash::make($request->new_password)
        ]);

        Session::flash('success', 'رمز عبور با موفقیت به‌روزرسانی شد!');

        return response()->json(['status' => 'success'], 200);
    }

    //edit_profile
    public function edit_profile()
    {
        $information = [];
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $information['language'] = $language;
        $information['languages'] = Language::get();

        $vendor_id = Auth::user()->id;
        $information['vendor'] = User::with('vendorInfo')->where('id', $vendor_id)->first();
        return view('frontend.vendor.auth.edit-profile', $information);
    }
    //update_profile
    public function update_profile(Request $request, Vendor $vendor)
    {
        $id = Auth::user()->id;
        $admin = Admin::select('username')->first();
        $admin_username = $admin->username;
        $rules = [
            'username' => [
                'required',
                Rule::unique('vendors', 'username')->ignore($id),
                Rule::notIn([$admin_username]),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('vendors', 'email')->ignore($id)
            ]
        ];

        if ($request->hasFile('photo')) {
            $rules['photo'] = 'mimes:png,jpeg,jpg|dimensions:min_width=80,max_width=80,min_width=80,min_height=80';
        }

        $languages = Language::get();
        foreach ($languages as $language) {
            $rules[$language->code . '_name'] = 'required';
        }

        $messages = [];

        foreach ($languages as $language) {
            $messages[$language->code . '_name.required'] = "فیلد نام برای زبان {$language->name} ضروری است";
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }


        $in = $request->all();
        $vendor  = User::where('id', $id)->where('role', 'business')->first();
        $file = $request->file('photo');
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $directory = public_path('assets/admin/img/vendor-photo/');
            $fileName = uniqid() . '.' . $extension;
            @mkdir($directory, 0775, true);
            $file->move($directory, $fileName);

            @unlink(public_path('assets/admin/img/vendor-photo/') . $vendor->photo);
            $in['photo'] = $fileName;
        }


        if ($request->show_email_addresss) {
            $in['show_email_addresss'] = 1;
        } else {
            $in['show_email_addresss'] = 0;
        }
        if ($request->show_phone_number) {
            $in['show_phone_number'] = 1;
        } else {
            $in['show_phone_number'] = 0;
        }
        if ($request->show_contact_form) {
            $in['show_contact_form'] = 1;
        } else {
            $in['show_contact_form'] = 0;
        }

        $vendor->update($in);

        $languages = Language::get();
        $vendor_id = $vendor->id;
        foreach ($languages as $language) {
            $vendorInfo = VendorInfo::where('user_id', $vendor_id)->where('language_id', $language->id)->first();
            if ($vendorInfo == NULL) {
                $vendorInfo = new VendorInfo();
            }
            $vendorInfo->language_id = $language->id;
            $vendorInfo->user_id = $vendor_id;
            $vendorInfo->name = $request[$language->code . '_name'];
            $vendorInfo->country = $request[$language->code . '_country'];
            $vendorInfo->city = $request[$language->code . '_city'];
            $vendorInfo->state = $request[$language->code . '_state'];
            $vendorInfo->zip_code = $request[$language->code . '_zip_code'];
            $vendorInfo->address = $request[$language->code . '_address'];
            $vendorInfo->details = $request[$language->code . '_details'];
            $vendorInfo->save();
        }

        Session::flash('success', 'پروفایل با موفقیت به‌روزرسانی شد!');

        return Response::json(['status' => 'success'], 200);
    }

    public function changeTheme(Request $request)
    {
        $request->validate([
            'vendor_theme_version' => 'required|in:light,dark',
        ]);

        Session::put('vendor_theme_version', $request->vendor_theme_version);

        return redirect()->back();
    }
    //forget_passord
    public function forget_passord()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $information['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_forget_password', 'meta_descriptions_vendor_forget_password')->first();

        $information['pageHeading'] = $misc->getPageHeading($language);

        $information['bgImg'] = $misc->getBreadcrumb();
        $information['bs'] = Basic::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();
        return view('frontend.vendor.auth.forget-password', $information);
    }
    //forget_mail
    public function forget_mail(Request $request)
    {
        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                new MatchEmailRule('vendor')
            ]
        ];

        $messages = [];



        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Vendor::where('email', $request->email)->first();

        // first, get the mail template information from db
        $mailTemplate = MailTemplate::where('mail_type', 'reset_password')->first();
        $mailSubject = $mailTemplate->mail_subject;
        $mailBody = $mailTemplate->mail_body;

        // second, send a password reset link to user via email
        $info = DB::table('basic_settings')
            ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();

        $name = $user->username;
        $token =  Str::random(32);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
        ]);

        $link = '<a href=' . url("vendor/reset-password?token=" . $token) . '>Click Here</a>';

        $mailBody = str_replace('{customer_name}', $name, $mailBody);
        $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
        $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

        $data = [
            'to' => $request->email,
            'subject' => $mailSubject,
            'body' => $mailBody,
        ];

        // if smtp status == 1, then set some value for PHPMailer
        if ($info->smtp_status == 1) {
            try {
                $smtp = [
                    'transport' => 'smtp',
                    'host' => $info->smtp_host,
                    'port' => $info->smtp_port,
                    'encryption' => $info->encryption,
                    'username' => $info->smtp_username,
                    'password' => $info->smtp_password,
                    'timeout' => null,
                    'auth_mode' => null,
                ];
                Config::set('mail.mailers.smtp', $smtp);
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                return back();
            }
        }

        // finally add other informations and send the mail
        try {
            if ($info->smtp_status == 1) {
                Mail::send([], [], function (Message $message) use ($data, $info) {
                    $fromMail = $info->from_mail;
                    $fromName = $info->from_name;
                    $message->to($data['to'])
                        ->subject($data['subject'])
                        ->from($fromMail, $fromName)
                        ->html($data['body'], 'text/html');
                });
            }

            Session::flash('success', 'ایمیل بازیابی رمز عبور برای شما ارسال شد');
        } catch (\Exception $e) {
            Session::flash('error', 'ایمیل ارسال نشد!');
        }

        // store user email in session to use it later
        $request->session()->put('userEmail', $user->email);
        return redirect()->back();
    }
    //reset_password
    public function reset_password()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $information['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_forget_password', 'meta_descriptions_vendor_forget_password')->first();

        $information['bgImg'] = $misc->getBreadcrumb();
        return view('frontend.vendor.auth.reset-password', $information);
    }
    //update_password
    public function update_password(Request $request)
    {
        $rules = [
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'تأیید رمز عبور با شکست مواجه شد.',
            'new_password_confirmation.required' => 'فیلد تأیید رمز عبور جدید الزامی است.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $reset = DB::table('password_resets')->where('token', $request->token)->first();
        $email = $reset->email;

        $vendor = Vendor::where('email',  $email)->first();

        $vendor->update([
            'password' => Hash::make($request->new_password)
        ]);
        DB::table('password_resets')->where('token', $request->token)->delete();
        Session::flash('success', 'بازیابی رمز عبور با موفقیت انجام شد. لطفا وارد شوید');

        return redirect()->route('vendor.login');
    }
    public function payment_log(Request $request)
    {
        $search = $request->search;
        $data['memberships'] = Membership::query()->when($search, function ($query, $search) {
            return $query->where('transaction_id', 'like', '%' . $search . '%');
        })
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return view('vendors.payment_log', $data);
    }
}
