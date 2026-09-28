<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        return view('home');
        return view('myPage.my_page_view');
    }

    public function home () {
        $user = Auth::user();
        if ($user) {
            if ($user->authority == 3) {
                Auth::logout();
            }
        }

        return view('home');
    }
}
