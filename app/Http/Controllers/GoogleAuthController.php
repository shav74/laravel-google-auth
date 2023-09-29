<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        try {
            // Get user data from Google
            $google_user = Socialite::driver('google')->user();

            // Check if a user with the Google ID already exists
            $user = User::where('email', $google_user->getEmail())
                ->orWhere('google_id', $google_user->getId())
                ->first();

            if (!$user) {
                // Create a new user if they don't exist
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                ]);

                // Log in the new user
                Auth::login($new_user);
            } else {
                // Log in the existing user
                Auth::login($user);
            }

            return redirect('/dashboard');
        } catch (\Throwable $th) {
            dd('Something went wrong: ' . $th->getMessage());
        }
    }

}
