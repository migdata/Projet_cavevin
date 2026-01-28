<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { width: 100%; max-width: 400px; border: none; shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #722f37; border-color: #722f37; } /* Couleur Vin */
        .btn-primary:hover { background-color: #58242a; }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="text-center mb-4">
            <h3>CaveVin20 Manager</h3>
            <p class="text-muted">Entrer vos identifiants </p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>

</body>
</html>
        