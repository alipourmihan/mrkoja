<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ChangeLanguage
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
    $locale = 'fa';
    $request->session()->put('currentLocaleCode', $locale);
    $request->session()->put('currentLangCode', $locale);

    App::setLocale($locale);
    config(['app.locale' => $locale]);
    Log::debug('Final locale set to: ' . App::getLocale());

    return $next($request);
  }
}
