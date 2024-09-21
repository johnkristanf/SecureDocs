<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $data = $request->validate([
            'fullname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|required|confirmed',
            'password_confirmation' => 'required',
        ]);
    
        User::create($data);
    
        return redirect()->route('login.page');   
        
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
    
            $isAuthenticated = Auth::attempt($credentials);
    
            if($isAuthenticated){
                $request->session()->regenerate();
                return redirect()->route('project_files.page');
            }
    
        } catch (\Exception $e) {
            Log::error(
                'Error registering logging in user: '. $e->getMessage(), 
                ['trace' => $e->getTraceAsString()]
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records'
            ])->onlyInput('email');
        }
        
        
    }


    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect()->route('login.page');
    }
}
