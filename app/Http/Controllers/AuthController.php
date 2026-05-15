<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return Inertia::render('Auth/Login', [
            'prefillEmail' => $request->query('email', ''),
            'inviteToken'  => $request->query('invite', ''),
        ]);
    }

    public function showRegister(Request $request)
    {
        return Inertia::render('Auth/Register', [
            'prefillEmail' => $request->query('email', ''),
            'inviteToken'  => $request->query('invite', ''),
        ]);
    }

    public function showForgotPassword(Request $request)
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }
}
