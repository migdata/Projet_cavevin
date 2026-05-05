<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: "/api/users",
        summary: "Liste des utilisateurs",
        tags: ["Utilisateurs"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des utilisateurs"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index()
    {
        $users = User::all(['id', 'name', 'email', 'created_at']);
        return response()->json([
            'success' => true,
            'total'   => $users->count(),
            'data'    => $users
        ]);
    }

    #[OA\Post(
        path: "/api/users",
        summary: "Créer un utilisateur (Admin)",
        tags: ["Utilisateurs"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Jean Dupont"),
                    new OA\Property(property: "email", type: "string", example: "jean@test.com"),
                    new OA\Property(property: "password", type: "string", example: "••••••••")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Utilisateur créé"),
            new OA\Response(response: 422, description: "Données invalides"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'data'    => $user
        ], 201);
    }

    #[OA\Put(
        path: "/api/users/{id}",
        summary: "Modifier un utilisateur (Admin)",
        tags: ["Utilisateurs"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Jean Modifié"),
                    new OA\Property(property: "email", type: "string", example: "jean@test.com"),
                    new OA\Property(property: "password", type: "string", example: "••••••••")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Utilisateur modifié"),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        if ($request->has('name'))     $user->name     = $request->name;
        if ($request->has('email'))    $user->email    = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès',
            'data'    => $user
        ]);
    }

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Supprimer un utilisateur (Admin)",
        tags: ["Utilisateurs"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Utilisateur supprimé"),
            new OA\Response(response: 404, description: "Utilisateur non trouvé"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
}