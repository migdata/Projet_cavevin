<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Liste des produits",
        tags: ["Produits"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Liste des produits"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function ListeProduits(Request $request)
    {
        $vretour = [];
        $maRecherche = $request->input('search');

        if ($maRecherche) {
            $products = Product::where('name', 'LIKE', "%$maRecherche%")
                                ->orWhere('type', 'LIKE', "%$maRecherche%")
                                ->get();
            $message = "Resultats de la recherche pour '$maRecherche'";
        } else {
            $products = Product::all();
            $message  = "Liste des produits";
        }

        if ($products->count() > 0) {
            $vretour['success'] = true;
            $vretour['message'] = $message;
            $vretour['total']   = $products->count();
            $vretour['data']    = $products;
        } else {
            $vretour['success'] = false;
            $vretour['message'] = "Aucun produit trouvé";
            $vretour['total']   = 0;
            $vretour['data']    = [];
        }

        return response()->json($vretour, 200);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Afficher un produit",
        tags: ["Produits"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Produit trouvé"),
            new OA\Response(response: 404, description: "Produit non trouvé"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function unProduit($id)
    {
        $vretour  = [];
        $unProduit = Product::find($id);

        if ($unProduit) {
            $vretour['success'] = true;
            $vretour['message'] = "Produit trouvé avec succès";
            $vretour['data']    = $unProduit;
        } else {
            $vretour['success'] = false;
            $vretour['message'] = "Produit non trouvé";
            $vretour['data']    = null;
        }

        return response()->json($vretour, $vretour['success'] ? 200 : 404);
    }

    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Mettre à jour le stock (Admin)",
        tags: ["Produits"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "stock", type: "integer", example: 50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Stock mis à jour"),
            new OA\Response(response: 404, description: "Produit non trouvé"),
            new OA\Response(response: 422, description: "Valeur invalide"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function updateStock(Request $request, $id)
    {
        $vretour   = [];
        $leProduct = Product::find($id);

        if (!$leProduct) {
            $vretour['success'] = false;
            $vretour['message'] = "Produit non trouvé";
            return response()->json($vretour, 404);
        }

        $valeurNegativeStock = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0'
        ]);

        if ($valeurNegativeStock->fails()) {
            $vretour['success'] = false;
            $vretour['message'] = "La valeur du stock est invalide";
            return response()->json($vretour, 422);
        }

        $leProduct->stock = $request->input('stock');
        $leProduct->save();

        $vretour['success'] = true;
        $vretour['message'] = "Stock mis à jour avec succès";
        $vretour['data']    = [
            'id'    => $leProduct->id,
            'stock' => $leProduct->stock
        ];

        return response()->json($vretour, 200);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Créer un produit (Admin)",
        tags: ["Produits"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type", "price", "stock", "supplier_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Château Margaux"),
                    new OA\Property(property: "type", type: "string", example: "Vin Rouge"),
                    new OA\Property(property: "description", type: "string", example: "Grand vin de Bordeaux"),
                    new OA\Property(property: "price", type: "number", example: 25.99),
                    new OA\Property(property: "stock", type: "integer", example: 100),
                    new OA\Property(property: "barcode", type: "string", example: "1234567890123"),
                    new OA\Property(property: "supplier_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Produit créé"),
            new OA\Response(response: 422, description: "Données invalides"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'barcode'     => 'sometimes|string|unique:products,barcode',
            'supplier_id' => 'sometimes|integer|exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data'    => $product
        ], 201);
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Supprimer un produit (Admin)",
        tags: ["Produits"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Produit supprimé"),
            new OA\Response(response: 404, description: "Produit non trouvé"),
            new OA\Response(response: 403, description: "Accès refusé")
        ]
    )]
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès'
        ]);
    }
}