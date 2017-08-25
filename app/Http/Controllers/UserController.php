<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(Request $request)
    {
        return view('user', [
            'user' => User::findFullByNameOrFail($request->name),
        ]);
    }
}
