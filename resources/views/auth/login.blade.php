<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CaveVin20 – Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c0a0e, #722f37);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .logo { font-size: 48px; text-align: center; margin-bottom: 8px; }
        .login-card h3 { color: #722f37; font-weight: 700; text-align: center; margin-bottom: 4px; }
        .login-card p { text-align: center; color: #888; margin-bottom: 28px; font-size: 14px; }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .form-control { border-radius: 8px; padding: 10px 14px; border: 1.5px solid #ddd; }
        .form-control:focus { border-color: #722f37; box-shadow: 0 0 0 3px rgba(114,47,55,0.15); }
        .input-group-text { background: #f8f9fa; border: 1.5px solid #ddd; color: #722f37; }
        .btn-primary { background-color: #722f37; border-color: #722f37; border-radius: 8px; padding: 12px; font-weight: 600; }
        .btn-primary:hover { background-color: #58242a; border-color: #58242a; }
        .divider { border-top: 1px solid #eee; margin: 24px 0; }
        .footer-text { text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">🍷</div>
        <h3>CaveVin20 Manager</h3>
        <p>Entrez vos identifiants pour accéder à votre espace</p>

        @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="exemple@email.com"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>
        </form>

        <div class="divider"></div>
        <p class="footer-text">© 2026 CaveVin20 Manager — Tous droits réservés</p>
    </div>
</body>
</html>