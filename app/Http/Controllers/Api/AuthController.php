<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Login with email & password, return Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Revoke old tokens for this device
        $user->tokens()->where('name', 'mobile-app')->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data'    => [
                'token' => $token,
                'user'  => $this->formatUser($user),
            ],
            'message' => 'Login berhasil.',
        ]);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'nullable|in:student,lecturer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password, // Auto-hashed via cast
            'role'     => $request->role ?? 'student',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'data'    => [
                'token'         => $token,
                'user'          => $this->formatUser($user),
                'needs_onboarding' => true,
            ],
            'message' => 'Registrasi berhasil.',
        ], 201);
    }

    /**
     * Google OAuth login from mobile.
     * Mobile sends the Google ID token, backend verifies & creates/logs in user.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required_without:id_token|string',
            'id_token'     => 'required_without:access_token|string',
        ]);

        // Verify via Socialite (access_token) or Google tokeninfo API (id_token)
        try {
            if ($request->access_token) {
                $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);
                $googleId = $googleUser->getId();
                $email    = $googleUser->getEmail();
                $name     = $googleUser->getName();
                $avatar   = $googleUser->getAvatar();
            } else {
                // Verify ID token via Google's tokeninfo endpoint
                $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token,
                ]);

                if ($response->failed()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token Google tidak valid.',
                    ], 401);
                }

                $payload  = $response->json();
                $googleId = $payload['sub'];
                $email    = $payload['email'];
                $name     = $payload['name'] ?? ($payload['given_name'] ?? '') . ' ' . ($payload['family_name'] ?? '');
                $avatar   = $payload['picture'] ?? null;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi token Google: ' . $e->getMessage(),
            ], 401);
        }

        $isNewUser = false;

        // Find by google_id first, then by email
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'google_id'  => $googleId,
                    'avatar_url' => $user->avatar_url ?? $avatar,
                ]);
            } else {
                $isNewUser = true;
                $user = User::create([
                    'name'       => $name,
                    'email'      => $email,
                    'google_id'  => $googleId,
                    'avatar_url' => $avatar,
                    'role'       => 'student',
                    'password'   => null,
                ]);
            }
        }

        // Revoke old mobile tokens
        $user->tokens()->where('name', 'mobile-app')->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Check if profile exists
        $hasProfile = ($user->isStudent() && $user->student)
                   || ($user->isLecturer() && $user->lecturer);

        return response()->json([
            'success' => true,
            'data'    => [
                'token'            => $token,
                'user'             => $this->formatUser($user),
                'needs_onboarding' => $isNewUser || ! $hasProfile,
            ],
            'message' => 'Login Google berhasil.',
        ]);
    }

    /**
     * Complete onboarding — create student/lecturer profile.
     */
    public function onboarding(Request $request): JsonResponse
    {
        $user = $request->user();

        // Let the user set/update their role during onboarding
        if ($request->has('role')) {
            $request->validate([
                'role' => 'required|string|in:student,lecturer',
            ]);
            $user->update(['role' => $request->role]);
        }

        if ($user->role === 'student') {
            $request->validate([
                'nim'        => 'required|string|max:20',
                'semester'   => 'nullable|integer|min:1|max:14',
                'department' => 'required|string|max:255',
                'faculty'    => 'required|string|max:255',
            ]);

            Student::updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['nim', 'semester', 'department', 'faculty'])
            );
        } elseif ($user->role === 'lecturer') {
            $request->validate([
                'nidn'           => 'required|string|max:20',
                'department'     => 'required|string|max:255',
                'faculty'        => 'required|string|max:255',
                'specialization' => 'nullable|string|max:255',
            ]);

            Lecturer::updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['nidn', 'department', 'faculty', 'specialization'])
            );
        }

        // Send welcome email if not already sent
        try {
            $user->load(['student', 'lecturer']);
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\WelcomeNewUser($user));
        } catch (\Exception $e) {
            // Ignore mail service errors
        }

        return response()->json([
            'success' => true,
            'data'    => ['user' => $this->formatUser($user->fresh())],
            'message' => 'Onboarding berhasil.',
        ]);
    }

    /**
     * Logout — revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => ['user' => $this->formatUser($request->user())],
        ]);
    }

    /**
     * Format user data for API response.
     */
    private function formatUser(User $user): array
    {
        $data = [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'avatar_url' => $user->avatar_url,
            'initials'   => $user->initials,
        ];

        if ($user->isStudent() && $user->student) {
            $data['student'] = [
                'id'         => $user->student->id,
                'nim'        => $user->student->nim,
                'semester'   => $user->student->semester,
                'department' => $user->student->department,
                'faculty'    => $user->student->faculty,
            ];
        }

        if ($user->isLecturer() && $user->lecturer) {
            $data['lecturer'] = [
                'id'             => $user->lecturer->id,
                'nidn'           => $user->lecturer->nidn,
                'department'     => $user->lecturer->department,
                'faculty'        => $user->lecturer->faculty,
                'specialization' => $user->lecturer->specialization,
            ];
        }

        return $data;
    }
}
