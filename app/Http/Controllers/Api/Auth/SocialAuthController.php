<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;

class SocialAuthController extends Controller
{
 

public function redirectToGoogle()
{
    /** @var Provider $provider */
    $provider = Socialite::driver('google');
    return $provider->stateless()->redirect();
}

public function handleGoogleCallback()
{
    try {
        /** @var Provider $provider */
        $provider = Socialite::driver('google');
        $googleUser = $provider->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(24)),
            ]
        );

        $token = $user->createToken('google-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to authenticate user.'], 500);
    }
}

}
