<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
use HasApiTokens, HasFactory;
    // Connexion et recevoir le token 
    public function login(Request $request)
    {
        // Validation email et password  
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Vérification de l'user dans la bdd 
        $user = User::where('email', $request->email)->first();

        // Vérification si ce sont les bonnes entrées 
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiant incorrect'], 401);
        }

        // Nettoyage des old_tokens 
        $user->tokens()->delete();

        // Création du new_token
        $monToken = $user->createToken('auth_token')->plainTextToken;

        // Envoi au format JSON
        return response()->json([
            'message' => 'Connexion réussie !',
            'access_token' => $monToken,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}