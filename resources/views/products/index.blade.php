<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>CaveVin20 - Gestion Stock</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
</head>
<body>

    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-danger fw-bold"><i class="fas fa-wine-bottle me-2"></i>Gestion des Stocks</h1>
            
            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-1"></i> Importer DGSYS
                </button>
                
                <a href="{{ route('exportPdf') }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Exporter PDF
                </a>

                <a href="{{ route('products.calendar') }}" class="btn btn-info text-white ms-2">
                         <i class="fas fa-calendar-alt"></i> Voir l'Historique
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong>Oups !</strong> Une erreur est survenue lors de l'importation.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4 p-3 bg-light">
        <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-end">
            
            <div class="col-md-3">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Nom ou Code barre" value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">-- Tous les types --</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Fournisseur</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- Tous les fournisseurs --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary w-100">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produit</th>
                            <th>Type</th>
                            <th>Fournisseur</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end pe-4">Action Rapide</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 me-3 text-secondary">
                                
                                        </div>
                                        <div>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">Code Barre: {{ $product->barcode ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    @php $type = mb_strtolower($product->type ?? ''); @endphp
                                    @if(str_contains($type, 'vin'))
                                        <span class="badge bg-danger rounded-pill">Vin</span>
                                    @elseif(str_contains($type, 'biere') || str_contains($type, 'bière'))
                                        <span class="badge bg-warning text-dark rounded-pill">Bière</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Autre</span>
                                    @endif
                                </td>
                                
                                <td>{{ $product->supplier->name ?? 'Non défini' }}</td>

                                <td class="text-center fw-bold fs-5" id="stock-display-{{ $product->id }}">
                                    <span class="{{ $product->stock < 5 ? 'text-danger' : 'text-success' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
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
                                    </div>
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

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-csv me-2"></i>Mise à jour Stock DGSYS</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Sélectionnez le fichier exporté de la caisse (.csv)</label>
                            <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                            <div class="form-text">Le fichier doit contenir les colonnes : Code barre, Nom, Stock, Prix.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Lancer l'importation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/stock.js') }}"></script>
</body>
</html>