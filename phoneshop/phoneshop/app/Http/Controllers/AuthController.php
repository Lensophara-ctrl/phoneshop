<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    /**
     * Show modern login page with multiple options
     */
    public function showModernLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.modern-login');
    }

    /**
     * Show email login page
     */
    public function showEmailLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.email-login');
    }

    /**
     * Show OTP login page
     */
    public function showOtpLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.otp-login');
    }

    /**
     * Show biometric login page
     */
    public function showBiometricLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.biometric-login');
    }

    /**
     * Request OTP from web form
     */
    public function requestOtpWeb(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found with this email.']);
        }

        // Send OTP
        $this->otpService->createOtp($user->email, 'login', $request->ip());

        // Store email in session
        $request->session()->put('otp_email', $user->email);
        $request->session()->put('otp_type', 'login');
        $request->session()->put('otp_role', $user->role);

        return redirect()->route('otp.verify')
            ->with('success', 'OTP code sent to your email and Telegram');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        // Check if Google OAuth is configured
        $clientId = env('GOOGLE_CLIENT_ID');
        
        if (!$clientId || $clientId === 'your_google_client_id') {
            return redirect()->route('login')->with('error', 'Google login is not configured yet. Please use Email or OTP login instead.');
        }

        return redirect()->away('https://accounts.google.com/o/oauth2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => env('GOOGLE_REDIRECT_URI', url('/login/google/callback')),
            'response_type' => 'code',
            'scope' => 'email profile',
        ]));
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        // In production, implement proper OAuth flow
        return redirect()->route('login')->with('error', 'Google login is not configured yet. Please contact administrator.');
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook()
    {
        // Check if Facebook OAuth is configured
        $clientId = env('FACEBOOK_CLIENT_ID');
        
        if (!$clientId || $clientId === 'your_facebook_client_id') {
            return redirect()->route('login')->with('error', 'Facebook login is not configured yet. Please use Email or OTP login instead.');
        }

        return redirect()->away('https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => url('/login/facebook/callback'),
            'scope' => 'email',
        ]));
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback(Request $request)
    {
        // In production, implement proper OAuth flow
        return redirect()->route('login')->with('error', 'Facebook login is not configured yet. Please contact administrator.');
    }

    /**
     * Redirect to Apple OAuth
     */
    public function redirectToApple()
    {
        // Check if Apple OAuth is configured
        $clientId = env('APPLE_CLIENT_ID');
        
        if (!$clientId || $clientId === 'your_apple_client_id') {
            return redirect()->route('login')->with('error', 'Apple login is not configured yet. Please use Email or OTP login instead.');
        }

        return redirect()->away('https://appleid.apple.com/auth/authorize?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => url('/login/apple/callback'),
            'response_type' => 'code',
            'scope' => 'email name',
            'response_mode' => 'form_post',
        ]));
    }

    /**
     * Handle Apple OAuth callback
     */
    public function handleAppleCallback(Request $request)
    {
        // In production, implement proper OAuth flow
        return redirect()->route('login')->with('error', 'Apple login is not configured yet. Please contact administrator.');
    }

    public function showAdminLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.admin-login');
    }

    public function showCustomerLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.customer-login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        abort(403, 'Customer registration is disabled. Admin accounts can only be created by administrators.');
    }

    public function customerRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'min:6', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Account created successfully!');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Allow both admin and staff roles
            if (!in_array($user->role, ['admin', 'staff'])) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Access denied. Admin or Staff credentials required.',
                ])->onlyInput('email');
            }

            // Check if OTP is enabled
            if (env('OTP_ENABLED', true)) {
                Auth::logout(); // Logout temporarily until OTP is verified
                
                // Send OTP
                $this->otpService->createOtp($user->email, 'login', $request->ip());
                
                // Store email in session for OTP verification
                $request->session()->put('otp_email', $user->email);
                $request->session()->put('otp_type', 'login');
                $request->session()->put('otp_role', $user->role); // Store actual role (admin or staff)
                
                return redirect()->route('otp.verify')
                    ->with('success', 'OTP code sent to your email and Telegram');
            }

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'customer') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Invalid customer credentials.',
                ])->onlyInput('email');
            }

            // Check if OTP is enabled
            if (env('OTP_ENABLED', true)) {
                Auth::logout(); // Logout temporarily until OTP is verified
                
                // Send OTP
                $this->otpService->createOtp($user->email, 'login', $request->ip());
                
                // Store email in session for OTP verification
                $request->session()->put('otp_email', $user->email);
                $request->session()->put('otp_type', 'login');
                $request->session()->put('otp_role', 'customer');
                
                return redirect()->route('otp.verify')
                    ->with('success', 'OTP code sent to your email and Telegram');
            }

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerify(Request $request)
    {
        if (!$request->session()->has('otp_email')) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        return view('auth.otp-verify');
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'login');
        $role = $request->session()->get('otp_role', 'customer');

        if (!$email) {
            return back()->withErrors(['error' => 'Session expired. Please login again.']);
        }

        // Verify OTP
        $result = $this->otpService->verifyOtp($email, $request->code, $type);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        // OTP verified, now login the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['error' => 'User not found.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Clear OTP session data
        $request->session()->forget(['otp_email', 'otp_type', 'otp_role']);

        // Redirect based on role
        if (in_array($role, ['admin', 'staff'])) {
            return redirect()->intended('/dashboard')->with('success', 'Login successful!');
        }

        return redirect()->intended('/')->with('success', 'Login successful!');
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'login');

        if (!$email) {
            return back()->withErrors(['error' => 'Session expired. Please login again.']);
        }

        // Send new OTP
        $result = $this->otpService->createOtp($email, $type, $request->ip());

        return back()->with('success', 'New OTP code sent successfully!');
    }
}
