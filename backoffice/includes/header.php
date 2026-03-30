<?php
// Backoffice Admin Header
$adminSiteTitle = 'Iran Conflit — Administration';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Administration du site Iran Conflit">
    <title><?= h(isset($pageTitle) ? $pageTitle . ' — Admin' : $adminSiteTitle) ?></title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">

<?php if (isLoggedIn()): ?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Navigation administration">
    <div class="sidebar__header">
        <span class="sidebar__icon"><i class="fa-solid fa-globe"></i></span>
        <div>
            <span class="sidebar__title">Iran Conflit</span>
            <span class="sidebar__sub">Administration</span>
        </div>
    </div>

    <nav class="sidebar__nav">
        <div class="sidebar__section-label">Navigation</div>
        <ul>
            <li>
                <a href="/dashboard.php" <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'class="active"' : '' ?>>
                    <i class="fa-solid fa-gauge-high"></i> Tableau de bord
                </a>
            </li>
        </ul>

        <div class="sidebar__section-label">Contenu</div>
        <ul>
            <li>
                <a href="/articles/list.php" <?= (strpos($_SERVER['PHP_SELF'] ?? '', 'articles') !== false) ? 'class="active"' : '' ?>>
                    <i class="fa-solid fa-newspaper"></i> Articles
                </a>
            </li>
            <li>
                <a href="/categories/list.php" <?= (strpos($_SERVER['PHP_SELF'] ?? '', 'categories') !== false) ? 'class="active"' : '' ?>>
                    <i class="fa-solid fa-folder-open"></i> Catégories
                </a>
            </li>
        </ul>

        <div class="sidebar__section-label">Liens</div>
        <ul>
            <li>
                <a href="<?= $_ENV['FRONT_URL'] ?? getenv('FRONT_URL') ?: 'http://localhost:8080' ?>" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Voir le site
                </a>
            </li>
            <li>
                <a href="/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Top bar -->
<div class="admin-topbar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Ouvrir/fermer le menu">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="admin-topbar__info">
        <span><i class="fa-solid fa-user-tie"></i> Administrateur</span>
    </div>
</div>
<?php endif; ?>

<main class="admin-main <?= isLoggedIn() ? 'admin-main--with-sidebar' : '' ?>">
