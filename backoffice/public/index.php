<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$adminUser = $_ENV['ADMIN_USER'] ?? getenv('ADMIN_USER') ?: 'admin';
$adminPass = $_ENV['ADMIN_PASS'] ?? getenv('ADMIN_PASS') ?: 'admin123';

// Already logged in
if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === $adminUser && $pass === $adminPass) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $user;
        header('Location: /dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrects. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Administration Iran Conflit</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-body">
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <i class="fa-solid fa-globe"></i>
            <div>
                <span class="login-brand__name">Iran Conflit</span>
                <span class="login-brand__sub">Administration</span>
            </div>
        </div>

        <h1 class="login-title">Connexion</h1>
        <p class="login-desc">Accès réservé aux administrateurs autorisés.</p>

        <?php if ($error): ?>
        <div class="alert alert--danger" role="alert">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= h($error) ?>
        </div>
        <?php endif; ?>

        <form method="post" action="" class="login-form" novalidate>
            <div class="form-group">
                <label for="username">
                    <i class="fa-solid fa-user"></i> Identifiant
                </label>
                <input type="text" id="username" name="username"
                       value="admin"
                       required autocomplete="username"
                       placeholder="Votre identifiant">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fa-solid fa-lock"></i> Mot de passe
                </label>
                <input type="password" id="password" name="password"
                       required autocomplete="current-password"
                       placeholder="Votre mot de passe"
                       value="admin123">
            </div>

            <button type="submit" class="btn btn--primary btn--full">
                <i class="fa-solid fa-right-to-bracket"></i> Se connecter
            </button>
        </form>
    </div>
</div>
</body>
</html>
