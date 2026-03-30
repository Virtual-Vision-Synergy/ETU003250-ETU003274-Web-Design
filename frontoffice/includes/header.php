<?php
// $pageTitle, $pageDescription, $pageCanonical must be set before including this file
$siteTitle = 'Iran : Analyse du Conflit';
$siteUrl   = siteUrl();
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h(isset($pageTitle) ? $pageTitle . ' — ' . $siteTitle : $siteTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription ?? 'Analyses approfondies, reportages et décryptages sur le conflit en Iran. Actualité géopolitique, humanitaire et économique.') ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= h($pageCanonical ?? $siteUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="<?= isset($ogType)  ? h($ogType)  : 'website' ?>">
    <meta property="og:title"       content="<?= h(isset($pageTitle) ? $pageTitle . ' — ' . $siteTitle : $siteTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription ?? 'Analyses approfondies sur le conflit en Iran.') ?>">
    <meta property="og:url"         content="<?= h($pageCanonical ?? $siteUrl) ?>">
    <meta property="og:image"       content="<?= h($ogImage ?? siteUrl('assets/images/og-default.jpg')) ?>">
    <meta property="og:locale"      content="fr_FR">
    <meta property="og:site_name"   content="<?= h($siteTitle) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= h(isset($pageTitle) ? $pageTitle . ' — ' . $siteTitle : $siteTitle) ?>">
    <meta name="twitter:description" content="<?= h($pageDescription ?? 'Analyses approfondies sur le conflit en Iran.') ?>">
    <meta name="twitter:image"       content="<?= h($ogImage ?? siteUrl('assets/images/og-default.jpg')) ?>">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsMediaOrganization",
        "name": "<?= $siteTitle ?>",
        "url": "<?= $siteUrl ?>"
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= siteUrl('assets/css/style.css') ?>">

    <!-- Sitemap link -->
    <link rel="sitemap" type="application/xml" href="<?= siteUrl('sitemap.xml') ?>">
</head>
<body>

<!-- Top bar -->
<div class="topbar">
    <div class="container topbar__inner">
        <span class="topbar__date"><i class="fa-regular fa-calendar"></i> <?= date('d F Y') ?></span>
        <div class="topbar__social">
            <span>Suivre :</span>
            <a href="#" aria-label="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
            <a href="#" aria-label="Flux RSS"><i class="fa-solid fa-rss"></i></a>
        </div>
    </div>
</div>

<!-- Header -->
<header class="site-header" role="banner">
    <div class="container site-header__inner">
        <div class="site-header__brand">
            <a href="<?= siteUrl() ?>" class="brand-link" aria-label="Accueil — <?= h($siteTitle) ?>">
                <span class="brand-icon"><i class="fa-solid fa-globe"></i></span>
                <div class="brand-text">
                    <span class="brand-name">Iran Conflit</span>
                    <span class="brand-tagline">Analyse &amp; Information</span>
                </div>
            </a>
        </div>

        <!-- Search -->
        <form class="header-search" action="<?= siteUrl('recherche') ?>" method="get" role="search">
            <label for="header-search-input" class="sr-only">Rechercher un article</label>
            <input type="search" id="header-search-input" name="q" placeholder="Rechercher..." autocomplete="off" value="<?= h($_GET['q'] ?? '') ?>">
            <button type="submit" aria-label="Lancer la recherche">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <button class="nav-toggle" id="navToggle" aria-label="Ouvrir le menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="main-nav" id="mainNav" role="navigation" aria-label="Navigation principale">
        <div class="container">
            <ul class="main-nav__list">
                <li><a href="<?= siteUrl() ?>" <?= (basename($_SERVER['PHP_SELF']) === 'index.php' && !isset($_GET['slug'])) ? 'class="active" aria-current="page"' : '' ?>>
                    <i class="fa-solid fa-house"></i> Accueil
                </a></li>
                <?php foreach ($categories as $cat): ?>
                <li><a href="<?= siteUrl('categorie/' . h($cat['slug'])) ?>" <?= (($_GET['slug'] ?? '') === $cat['slug']) ? 'class="active" aria-current="page"' : '' ?>>
                    <?= h($cat['name']) ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<main class="main-content" id="main-content">
