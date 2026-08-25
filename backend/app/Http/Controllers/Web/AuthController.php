<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * The Next.js app opens login as a global modal on top of whatever page
     * you're on; /login there is just a redirect-and-open-modal shim. This
     * mirrors that: bounce to home with a flag the layout uses to auto-open
     * the modal client-side.
     */
    public function loginRedirect(): RedirectResponse
    {
        return redirect('/?login=1');
    }

    public function registerPage(): View
    {
        return view('pages.register');
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_blocked) {
            return response()->json(['message' => 'you had blocked by admin please contact admin'], 403);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Login successful']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function registerSendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|digits:10',
            'password' => 'required|string|min:6',
        ]);

        $otp = (string) mt_rand(100000, 999999);

        Cache::put('reg_otp_' . $validated['email'], [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'otp' => $otp,
        ], 900);

        try {
            Mail::to($validated['email'])->send(new OtpMail($otp, $validated['name']));
        } catch (\Exception $e) {
            Log::error("SABHA OTP email failed for {$validated['email']}: " . $e->getMessage());

            return response()->json(['message' => 'Could not send verification email. Please check your email address and try again.'], 500);
        }

        return response()->json([
            'message' => 'Email verification code has been sent to your email.',
            'email' => $validated['email'],
        ]);
    }

    public function registerConfirm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $cached = Cache::get('reg_otp_' . $validated['email']);

        if (! $cached || $cached['otp'] !== $validated['otp']) {
            return response()->json(['message' => 'Invalid or expired OTP verification code.'], 400);
        }

        $user = User::create([
            'name' => $cached['name'],
            'email' => $cached['email'],
            'phone' => $cached['phone'] ?? null,
            'password' => $cached['password'],
            'role' => 'user',
        ]);

        Cache::forget('reg_otp_' . $validated['email']);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Email verified and account registered successfully.']);
    }

    public function forgotPasswordPage(): View
    {
        return view('pages.forgot-password');
    }

    public function forgotPasswordSendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'We could not find an account with that email address.'], 404);
        }

        $otp = (string) mt_rand(100000, 999999);

        Cache::put('reset_otp_' . $validated['email'], [
            'email' => $validated['email'],
            'otp' => $otp,
        ], 900);

        try {
            Mail::to($validated['email'])->send(new OtpMail($otp, $user->name, 'reset_password'));
        } catch (\Exception $e) {
            Log::error("SABHA Password Reset OTP email failed for {$validated['email']}: " . $e->getMessage());

            return response()->json(['message' => 'Could not send verification email. Please check your email configuration and try again.'], 500);
        }

        return response()->json([
            'message' => 'Password reset verification code has been sent to your email.',
            'email' => $validated['email'],
        ]);
    }

    public function forgotPasswordReset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $cached = Cache::get('reset_otp_' . $validated['email']);

        if (! $cached || $cached['otp'] !== $validated['otp']) {
            return response()->json(['message' => 'Invalid or expired OTP verification code.'], 400);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'User account not found.'], 404);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        Cache::forget('reset_otp_' . $validated['email']);

        return response()->json(['message' => 'Your password has been reset successfully. Please log in with your new password.']);
    }
}
