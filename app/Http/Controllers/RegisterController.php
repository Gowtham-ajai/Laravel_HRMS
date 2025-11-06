<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Only HR can view the registration form
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Please login first.');
        }

        // Admin can add HRs, HR can add Employees
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'HR') {
            return view('register');
        }

        return redirect()->route('login')->with('error', 'Access denied!');
    }

    // Store new employee (HR action)
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:registers,email',
            'phone'    => 'required|string|min:10|max:10|unique:registers,phone',
            'password' => 'required|string|min:6|max:12',
        ]);

        // Determine role to assign
        $roleToAssign = Auth::user()->role === 'Admin' ? 'HR' : 'Employee';

        Register::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $roleToAssign, // fixed
        ]);

        $redirectRoute = Auth::user()->role === 'Admin' ? 'admin.dashboard' : 'hr.dashboard';

        return redirect()->route($redirectRoute)->with('success', "{$roleToAssign} added successfully!");
    }
}
