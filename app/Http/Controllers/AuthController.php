<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logout(){
        Auth::logout();
        session_destroy();
        return redirect(env('PORTAL_URL'));
    }
}
