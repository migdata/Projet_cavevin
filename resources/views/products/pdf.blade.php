<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaire</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #d9534f; } /* Rouge Bootstrap */
        table {
        width: 100%;
        /* border-collapse: collapse;  <-- ON SUPPRIME ÇA */
        border-spacing: 0; /* Alternative plus légère */
    }
    
    th, td {
        border-bottom: 1px solid #ddd; /* Juste une ligne en bas, plus léger qu'un cadre complet */
        padding: 8px;
        text-align: left;
    }
    </style>
</head>
<body>

    <h1>Inventaire CaveVin20</h1>
    <p style="text-align: center;">Date : {{ date('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Type</th>
                <th>Fournisseur</th>
                <th>Prix</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->type }}</td>
                    <td>{{ $product->supplier->name ?? 'Aucun' }}</td>
                    <td>{{ $product->price }} €</td>
                    <td class="text-right">
                        <span class="{{ $product->stock < 5 ? 'danger' : '' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>