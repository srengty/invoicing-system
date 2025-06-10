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
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // 1) Validate the request data
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);
        // 2) Build the external API login URL
        $apiBase  = config('services.external_auth.base_url');
        $loginUrl = rtrim($apiBase, '/') . '/login';

        try {
            // 3) Actually POST to the external authentication server
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->post($loginUrl, [
                    'email'    => $validated['email'],
                    'password' => $validated['password'],
                ]);

            // 4) If the HTTP status is 400–599, treat it as a “failed” response
            if ($response->failed()) {
                // Note: parseApiError() expects $response to be defined
                $errorMessage = $this->parseApiError($response);
                return back()
                    ->withInput()
                    ->withErrors(['email' => $errorMessage]);
            }

            // 5) Decode JSON payload
            $payload = $response->json();

            // 6) Make sure the payload actually has the pieces we expect
            if (! isset($payload['access_token'], $payload['user'], $payload['user']['roles'])) {
                return back()
                    ->withInput()
                    ->withErrors(['email' => 'Invalid response from authentication server.']);
            }

            // 7) Upsert (create or update) the local user record by email
            $extUserData = $payload['user'];
            $user = User::updateOrCreate(
                ['email' => $extUserData['email']],
                [
                    'name'     => $extUserData['name'] ?? $extUserData['email'],
                    // We store a random password locally, since actual auth is handled externally
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            // 8) Log in the Laravel “web” guard
            Auth::login($user);

            // 9) Grab just the “name” field from each role in the external payload
            //    and normalize to lowercase. E.g. ["revenue manager", "director", …]
            $rawRoles = $extUserData['roles'];
            $normalizedRoles = collect($rawRoles)
                ->pluck('name')             // ["Revenue Manager", …]
                ->map(fn($r) => strtolower($r))
                ->toArray();          // ["revenue manager", …]

            // 10) Store the external API token & the normalized roles array in session
            $request->session()->put('api_token', $payload['access_token']);
            $request->session()->put('roles', $normalizedRoles);
            $request->session()->get("roles");
            // 11) Regenerate session ID for security
            $request->session()->regenerate();

            $request->session()->put('user', [
                'name' => $extUserData['name'] ?? $extUserData['email'],
                'email' => $extUserData['email'],
            ]);

            // 12) Redirect to “dashboard” (or whatever route you’ve named)
            return redirect()->route('dashboard');
        }
        catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Could not connect to authentication server.']);
        }
        catch (\Exception $e) {
            // If you want to inspect $e->getMessage() for debugging, you can log it here:
            // \Log::error('Login exception: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['email' => 'An unexpected error occurred during login.']);
        }
    }


    protected function parseApiError($response): string
    {
        if ($response->status() === 422 && $response->json('errors')) {
            return collect($response->json('errors'))
                ->flatten()
                ->join(' ');
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
        // 1) Log out of Laravel’s “web” guard
        Auth::guard('web')->logout();

        // 2) Forget any external‐API token and roles stored in session
        $request->session()->forget(['api_token', 'roles']);

        // 3) Invalidate & regenerate CSRF token for safety
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4) Redirect back to login
        return redirect()->route('login');
    }
}
