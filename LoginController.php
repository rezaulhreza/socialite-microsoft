<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Redirect the user to the MS authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToMS()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function handleMsCallback()
    {
        $m365User = Socialite::driver('microsoft')->user();

        $user = User::where('ms_id', $m365User->getId())->first();
        // When user is not found, redirect back to the login page with "Authorisation denied"
        if (!$user) {
            return redirect()->route('login')->with('message','Authorisation denied');
        }

        // Log the user in Dotty
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("https://login.microsoftonline.com/common/oauth2/v2.0/logout");
    }
}
