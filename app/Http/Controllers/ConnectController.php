<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Controllers\Controller;
use App\Libs\Reddit\OAuth as RedditOAuth;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class ConnectController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [ 'except' => 'logout' ]);
    }

    public function get(Request $request, UrlGenerator $generator)
    {
        if (!$request->has('code')) {
            return redirect(RedditOAuth::getAuthUrl());
        }

        $user = RedditOAuth::authWithCode($request->input('code'));
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /*
    public function logout(Request $request)
    {
        Auth::guard()->logout();

        $request->session()->flush();
        $request->session()->regenerate();

        return redirect(route('welcome'));
    }
    */
}
