<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\BasicMailer;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\User;
use App\Models\VendorInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_login', 'meta_description_login')->first();
        $information['pageHeading'] = $misc->getPageHeading($language);
        $information['bgImg'] = $misc->getBreadcrumb();
        $information['bs'] = Basic::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();

        if ($request->redirectPath == 'listingDetails' || $request->redirectPath == 'wishlist') {
            Session::put('redirectTo', URL::previous());
        }

        return view('auth.login', $information);
    }

    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Get redirect URL from session
        $redirectURL = Session::get('redirectTo');

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
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Login attempt
        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check account status
            if ($user->status == 0) {
                Auth::logout();
                Session::flash('error', 'حساب کاربری شما غیرفعال شده است.');
                return redirect()->back();
            }

            // Check email verification for business users
            if ($user->isBusiness()) {
                $setting = DB::table('basic_settings')->where('uniqid', 12345)
                    ->select('vendor_email_verification', 'vendor_admin_approval')
                    ->first();

                if ($setting && $setting->vendor_email_verification == 1 && $user->email_verified_at == null) {
                    Auth::logout();
                    Session::flash('error', 'لطفا ایمیل خود را تایید کنید.');
                    return redirect()->back();
                }
            } else {
                // Check email verification for regular users
                if ($user->email_verified_at == null) {
                    Auth::logout();
                    Session::flash('error', 'لطفا ایمیل خود را تایید کنید.');
                    return redirect()->back();
                }
            }

            // Clear redirect session
            Session::forget('redirectTo');

            // Redirect based on role
            return $this->redirectAfterLogin($redirectURL);
        }

        Session::flash('error', 'نام کاربری یا رمز عبور نادرست است.');
        return redirect()->back()->withInput($request->only('username'));
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        $information['seoInfo'] = $language->seoInfo()->select('meta_keyword_signup', 'meta_description_signup')->first();
        $information['pageHeading'] = $misc->getPageHeading($language);
        $information['bgImg'] = $misc->getBreadcrumb();
        $information['recaptchaInfo'] = Basic::select('google_recaptcha_status')->first();

        return view('auth.register', $information);
    }

    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $info = Basic::select('google_recaptcha_status', 'website_title')->first();
        $admin = \App\Models\Admin::select('username')->first();
        $admin_username = $admin ? $admin->username : 'admin';

        // Validation rules
        $rules = [
            'username' => "required|unique:users,username|max:255|not_in:$admin_username",
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ];

        if ($info->google_recaptcha_status == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [
            'password_confirmation.required' => 'تأیید رمز عبور الزامی است.',
            'username.not_in' => 'شما نمی‌توانید از ' . $admin_username . ' به عنوان نام کاربری استفاده کنید!'
        ];

        if ($info->google_recaptcha_status == 1) {
            $messages['g-recaptcha-response.required'] = 'لطفا تأیید کنید که ربات نیستید.';
            $messages['g-recaptcha-response.captcha'] = 'خطای کپچا! لطفا بعدا دوباره امتحان کنید یا با مدیر سایت تماس بگیرید.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Create user with role 'user' by default
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        $user->status = 1;
        $user->save();

        // Send verification email
        $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'verify_email')->first();
        if ($mailTemplate) {
            $mailData['subject'] = $mailTemplate->mail_subject;
            $mailBody = $mailTemplate->mail_body;

            $link = '<a href=' . url("auth/verify-email/" . $user->id) . '>Click Here</a>';

            $mailBody = str_replace('{username}', $user->username, $mailBody);
            $mailBody = str_replace('{verification_link}', $link, $mailBody);
            $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

            $mailData['body'] = $mailBody;
            $mailData['recipient'] = $user->email;
            $mailData['sessionMessage'] = 'ایمیل تایید برای شما ارسال شده است';

            BasicMailer::sendMail($mailData);
        }

        Session::flash('success', 'ثبت نام با موفقیت انجام شد. لطفا ایمیل خود را تایید کنید.');
        return redirect()->route('auth.login');
    }

    /**
     * Verify email address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->email_verified_at = Carbon::now();
        $user->save();

        Auth::login($user);
        return $this->redirectAfterLogin();
    }

    /**
     * Upgrade user to business role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upgradeToBusiness(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isUser()) {
            Session::flash('error', 'فقط کاربران عادی می‌توانند به کسب‌وکار تبدیل شوند.');
            return redirect()->back();
        }

        // Update role to business
        $user->role = 'business';
        $user->save();

        // Create vendor info if doesn't exist
        $misc = new MiscellaneousController();
        $language = $misc->getLanguage();

        if (!$user->vendorInfo) {
            VendorInfo::create([
                'user_id' => $user->id,
                'language_id' => $language->id,
            ]);
        }

        Session::flash('success', 'حساب کاربری شما به کسب‌وکار تبدیل شد.');
        return redirect()->route('vendor.dashboard');
    }

    /**
     * Handle logout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        Session::flash('success', 'با موفقیت خارج شدید.');
        return redirect()->route('auth.login');
    }

    /**
     * Redirect user after login based on their role.
     *
     * @param  string|null  $redirectURL
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectAfterLogin($redirectURL = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('auth.login');
        }

        // If custom redirect URL exists, use it
        if ($redirectURL) {
            return redirect($redirectURL);
        }

        // Redirect based on role
        if ($user->isBusiness()) {
            return redirect()->route('vendor.dashboard');
        } elseif ($user->isUser()) {
            return redirect()->route('user.dashboard');
        }

        // Default redirect
        return redirect()->route('index');
    }
}
