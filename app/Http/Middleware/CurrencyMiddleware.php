<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;

class CurrencyMiddleware
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
        if ($request->has('currency_id')) {
            session(['currency_id' => $request->currency_id]);
        }

        if ($request->has('delivery_country')) {
            session(['country_id' => $request->delivery_country]);
        }

        if ($request->has('delivery_city')) {
            session(['city_id' => $request->delivery_city]);
        }
        return $next($request);
    }
}
