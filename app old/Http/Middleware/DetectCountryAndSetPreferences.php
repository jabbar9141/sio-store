<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GeoIP;

class DetectCountryAndSetPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if country is already set in the session
        // if (!Session::has('country')) {
        //     // Detect the country based on the IP address
        //     $location = GeoIP::getLocation($request->ip());

        //     // Set country in the session
        //     $country = $location->country;
        //     Session::put('country', $country);

        //     // Set currency based on the country
        //     switch ($country) {
        //         case 'Nigeria':
        //             Session::put('currency', 'NGN');
        //             break;
        //         case in_array($country, ['France', 'Germany', 'Italy', 'Spain']): // Add other EU countries as needed
        //             Session::put('currency', 'EUR');
        //             break;
        //         case 'United Kingdom':
        //             Session::put('currency', 'GBP');
        //             break;
        //         default:
        //             Session::put('currency', 'USD');
        //             break;
        //     }

        //     // Set locale based on the country
        //     switch ($country) {
        //         case 'Italy':
        //             Session::put('locale', 'IT');
        //             break;
        //         default:
        //             Session::put('locale', 'EN');
        //             break;
        //     }
        // }

        return $next($request);
    }
}
