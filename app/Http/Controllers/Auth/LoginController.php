<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Show the login page.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Redirect to Google OAuth.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleProviderCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Validar que el email sea del dominio corporativo
            $corporateDomain = 'gptservices.com';
            $emailDomain = explode('@', $googleUser->email)[1] ?? null;

            if ($emailDomain !== $corporateDomain) {
                return redirect()->route('login')->with('error', 'Solo se permiten correos del dominio @' . $corporateDomain);
            }

            // Find or create user
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]);

                // Assign default admin role to first user
                $adminRole = Role::where('name', 'admin')->first();
                if ($adminRole) {
                    $user->assignRole($adminRole);
                }
            } else {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con Google. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
