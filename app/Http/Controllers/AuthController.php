<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    function authenticate(Request $request)
    {
        $user_data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(Auth::attempt($user_data))
        {
            return $this->successlogin();
        }
        else
        {
            return back()->with('error', 'Wrong Login Details');
        }
    }

    public function registrationPage()
    {
        return view('register');
    }

    public function register(RegistrationRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        User::create($validatedData);

        return $this->successlogin();
    }

    function successlogin()
    {
        return redirect()->route('dashboard');
    }

    function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
