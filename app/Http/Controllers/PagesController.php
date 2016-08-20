<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PagesController extends Controller
{
    public function __construct()
    {
        // If the user is logged in, redirect to checklist page
        $this->middleware('guest', [
            'only' => [
                'getLandingMain', 'getLandingStart'
            ]
        ]);
    }


    /**
     * Primary landing page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLandingMain()
    {
        return view('landing.main');
    }

    /**
     * Secondary landing page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLandingStart()
    {
        return view('landing.start');
    }
}
