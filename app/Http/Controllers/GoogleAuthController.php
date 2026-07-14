<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $isNewUser = false;

        // Check if user exists with this google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Check if user exists with same email (link accounts)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id'  => $googleUser->getId(),
                    'avatar_url' => $user->avatar_url ?? $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user — flag as new so we send to onboarding
                $isNewUser = true;
                $user = User::create([
                    'name'       => $googleUser->getName(),
                    'email'      => $googleUser->getEmail(),
                    'google_id'  => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                    'role'       => 'student', // default, can be changed at onboarding
                    'password'   => null,
                ]);
            }
        } else {
            // Update avatar if not set
            if (!$user->avatar_url && $googleUser->getAvatar()) {
                $user->update(['avatar_url' => $googleUser->getAvatar()]);
            }
        }

        Auth::login($user, true);
        session()->regenerate();
        session(['role' => $user->role]);

        // Redirect new users OR existing users without a profile record to onboarding
        $hasProfile = ($user->isStudent() && $user->student)
                   || ($user->isLecturer() && $user->lecturer);

        if ($isNewUser || !$hasProfile) {
            return redirect('/onboarding');
        }

        return redirect($user->role === 'lecturer' ? '/lecturer' : '/student');
    }
}
