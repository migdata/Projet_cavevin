<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>CaveVin20</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
</head>
<body>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-danger fw-bold"> Gestion des Stocks</h1>
            <button class="btn btn-dark">Exporter PDF</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th>Type</th>
                            <th>Fournisseur</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end">Action Rapide</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong><br>
                                    <small class="text-muted">Réf: {{ $product->id }}</small>
                                </td>
                                
                                <td>
                                    @if(mb_strtolower($product->type) == 'vin')
                                        <span class="badge badge-vin">Vin</span>
                                    @else
                                        @if(mb_strtolower($product->type) == 'bières' || mb_strtolower($product->type) == 'biere')
                                            <span class="badge badge-biere">Bière</span>
                                        @else
                                            <span class="badge badge-spi">Spiritueux</span>
                                        @endif
                                    @endif
                                </td>
                                
                                <td>{{ $product->supplier->name }}</td>

                                <td class="text-center fw-bold fs-5" id="stock-display-{{ $product->id }}">
                                    {{ $product->stock }}
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm btn-action" 
                                            data-id="{{ $product->id }}" 
                                            data-action="-">
                                        <i class="fas fa-minus"></i>
                                    </button>

                                    <button class="btn btn-outline-success btn-sm btn-action" 
                                            data-id="{{ $product->id }}" 
                                            data-action="+">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>

    <script src="{{ asset('js/stock.js') }}"></script>
</body>
</html>