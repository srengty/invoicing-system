<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status'    => session('status'),
            'userRoles' => session('roles', []),
            'expires_in' => config('session.lifetime'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        $apiBase  = rtrim(config('services.external_auth.base_url'), '/');
        $loginUrl = "{$apiBase}/login";

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->post($loginUrl, $credentials);

            if ($response->failed()) {
                $errorMessage = $this->parseApiError($response);
                return back()->withInput()->withErrors(['email' => $errorMessage]);
            }

            $payload = $response->json();
            if (!isset($payload['access_token'], $payload['user']['roles'])) {
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Invalid response from auth server.']);
            }

            // Upsert local user record
            $extUser = $payload['user'];
            $user = User::updateOrCreate(
                ['email' => $extUser['email']],
                [
                    'name'     => $extUser['name'] ?? $extUser['email'],
                    'password' => Hash::make(Str::random(16)), // random local password
                ]
            );

            Auth::login($user);

            // Normalize roles
            $normalizedRoles = collect($extUser['roles'])
                ->pluck('name')
                ->map(fn($role) => strtolower($role))
                ->toArray();

            // Store token and roles in session
            $request->session()->put('api_token', $payload['access_token']);
            $request->session()->put('roles', $normalizedRoles);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Could not connect to authentication server.']);
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors(['email' => 'An unexpected error occurred during login.']);
        }
    }


    protected function parseApiError($response): string
    {
        if ($response->status() === 422 && $response->json('errors')) {
            return collect($response->json('errors'))->flatten()->join(' ');
        }

        return match ($response->status()) {
            401 => 'Invalid credentials.',
            403 => 'Account not authorized.',
            404 => 'Authentication endpoint not found.',
            500 => 'Authentication server error.',
            default => 'Authentication failed.',
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->forget(['api_token', 'roles']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
