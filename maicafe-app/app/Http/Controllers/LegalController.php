<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    public function termsAndConditions()
    {
        return view('legal.terms-and-conditions');
    }
}
