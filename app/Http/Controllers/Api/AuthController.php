<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/login",
        summary: "Connexion et obtention du token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "admin@test.com"),
                    new OA\Property(property: "password", type: "string", example: "••••••••")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Connexion réussie"),
            new OA\Response(response: 401, description: "Identifiants incorrects"),
            new OA\Response(response: 429, description: "Trop de tentatives")
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        $user->tokens()->delete();
        $monToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion réussie !',
            'access_token' => $monToken,
            'user'         => $user
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnexion",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Déconnexion réussie"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}