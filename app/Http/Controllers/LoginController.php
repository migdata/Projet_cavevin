<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Svg\Tag\Rect;

class LoginController extends Controller
{
    // affiche le formulaire 

    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 

    public function login(Request $request)
    {
        $est_ok = $request->validate(
        ['email' => 'required|email',
         'password' => 'required'
        ]);

            if(Auth::attempt($est_ok)) {
                $request->session()->regenerate();
                return redirect()->route('products.index');
            }
            
            return back()->withErrors([
            'email' => 'identifiant incorrect',
            ]);
        }
        
        // deconnexion
    
    public function logout(Request $request) {
    
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
            }

}
