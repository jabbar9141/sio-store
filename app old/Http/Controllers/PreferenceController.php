<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PreferenceController extends Controller
{
    public function setCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|string'
        ]);

        Session::put('currency', $request->currency);
        return back()->with('success', 'Currency has been updated.');
    }

    public function setLocale(Request $request)
    {
        $request->validate([
            'locale' => 'required|string'
        ]);

        Session::put('locale', $request->locale);
        return back()->with('success', 'Locale has been updated.');
    }
}
