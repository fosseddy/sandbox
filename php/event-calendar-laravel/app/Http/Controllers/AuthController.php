<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController
{
    function getLogin(Request $req): View
    {
        return view("auth.login");
    }

    function postLogin(LoginRequest $req): RedirectResponse
    {
        $body = $req->validated();

        if (!Auth::attempt($body)) {
            return back()
               ->withErrors(["name" => "invalid name or password"])
               ->onlyInput("name");
        }

        $req->session()->regenerate();
        return redirect("/");
    }

    function getLogout(Request $req): RedirectResponse
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect()->route("login");
    }
}
