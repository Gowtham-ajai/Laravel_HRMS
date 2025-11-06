<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // public function login()
    // {
    //     return view('login');
    // }

     public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6|max:12',
        ]);

        $user = Register::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $this->getRedirectRoute($user),
                    'message' => 'Login successful! Welcome ' . $user->name
                ]);
            }

            return redirect()->intended($this->getRedirectRoute($user))
                           ->with('success', 'Welcome ' . $user->name . '!');
        } else {
            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 422);
            }
            
            return redirect()->route('home')
                           ->with('error', 'Invalid email or password')
                           ->withInput($request->except('password'));
        }
    }

    private function getRedirectRoute($user)
    {
        switch ($user->role) {
            case 'Admin':
                return route('admin.dashboard');
            case 'HR':
                return route('hr.dashboard');
            default:
                return route('employee.dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    public function showForgotPassword()
    {
        // This will be handled via modal, no separate view needed
        return response()->json(['success' => true]);
    }

     public function resetPasswordDirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:registers,email',
            'password' => 'required|string|min:6|max:12|confirmed',
        ], [
            'email.exists' => 'No account found with this email address.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->with('error', $validator->errors()->first());
        }

        try {
            // Find user by email
            $user = Register::where('email', $request->email)->first();

            if (!$user) {
                throw new \Exception('User not found.');
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password has been updated successfully! You can now login with your new password.'
                ]);
            }

            return redirect()->route('home')->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update password: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update password.');
        }
    }
}
