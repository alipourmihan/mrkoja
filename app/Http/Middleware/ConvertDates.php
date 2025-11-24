<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertDates
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
        // Convert Jalali dates to Gregorian before processing
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $input = $request->all();
            
            // List of input fields that contain dates
            $dateFields = ['date', 'start_date', 'end_date', 'created_at', 'updated_at', 'published_at'];
            
            foreach ($dateFields as $field) {
                if (isset($input[$field]) && !empty($input[$field])) {
                    $input[$field] = to_gregorian($input[$field]);
                }
            }
            
            $request->replace($input);
        }

        $response = $next($request);

        return $response;
    }
} 