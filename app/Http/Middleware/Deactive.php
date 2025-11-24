<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Deactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isBusiness() && Auth::user()->status == 0) {
            if ($request->isMethod('POST') || $request->isMethod('PUT')) {
                $message = DB::table('basic_settings')
                    ->select('admin_approval_notice')
                    ->first();

                session()->flash('warning', __($message->admin_approval_notice));
                return redirect()->back();
            }
        }
        return $next($request);
    }
}
