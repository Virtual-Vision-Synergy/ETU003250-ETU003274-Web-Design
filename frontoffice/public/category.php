<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';


$slug     = $_GET['slug'] ?? '';
$category = $slug ? getCategoryBySlug($slug) : null;

if (!$category) {
    http_response_code(404);
    $pageTitle       = 'Catégorie introuvable';
    $pageDescription = 'Cette catégorie n\'existe pas.';
    $pageCanonical   = siteUrl();
    $categories      = getAllCategories();
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="container">
        <div class="error-page">
            <i class="fa-solid fa-circle-exclamation"></i>
            <h1>Catégorie introuvable</h1>
            <a href="<?= siteUrl() ?>" class="btn btn--primary">
                <i class="fa-solid fa-house"></i> Retour à l'accueil
            </a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$articles   = getArticlesByCategory($slug);
$categories = getAllCategories();

$pageTitle       = $category['name'] . ' — Analyses sur l\'Iran';
$pageDescription = $category['description'] ? truncate($category['description'], 160) : 'Retrouvez tous les articles de la rubrique ' . $category['name'] . '.';
$pageCanonical   = siteUrl('categorie/' . $slug);

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Fil d'Ariane">
    <div class="container">
        <ol class="breadcrumb__list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= siteUrl() ?>" itemprop="item">
                    <span itemprop="name"><i class="fa-solid fa-house"></i> Accueil</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                <span itemprop="name"><?= h($category['name']) ?></span>
                <meta itemprop="position" content="2">
            </li>
        </ol>
    </div>
</nav>

<!-- Category Header -->
<section class="category-hero" aria-labelledby="category-title">
    <div class="container">
        <h1 id="category-title" class="category-hero__title">
            <i class="fa-solid fa-folder-open"></i> <?= h($category['name']) ?>
        </h1>
        <?php if ($category['description']): ?>
        <p class="category-hero__desc"><?= h($category['description']) ?></p>
        <?php endif; ?>
        <p class="category-hero__count">
            <i class="fa-solid fa-newspaper"></i>
            <?= count($articles) ?> article<?= count($articles) > 1 ? 's' : '' ?> dans cette rubrique
        </p>
    </div>
</section>

<!-- Articles -->
<section class="section" aria-labelledby="articles-heading">
    <div class="container">
        <h2 id="articles-heading" class="section-title">
            <i class="fa-solid fa-list"></i> Articles de la rubrique
        </h2>
        <div class="section-divider"></div>

        <?php if (empty($articles)): ?>
        <p class="empty-state">
            <i class="fa-solid fa-circle-info"></i> Aucun article dans cette catégorie pour le moment.
        </p>
        <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
            <article class="article-card" itemscope itemtype="https://schema.org/NewsArticle">
                <div class="article-card__img-wrap">
                    <?php if ($article['image']): ?>
                    <img src="<?= h($article['image']) ?>"
                         alt="<?= h($article['image_alt'] ?? $article['title']) ?>"
                         class="article-card__img" loading="lazy" itemprop="image">
                    <?php else: ?>
                    <div class="article-card__img-placeholder" aria-hidden="true">
                        <i class="fa-solid fa-globe-europe"></i>
                    </div>
                    <?php endif; ?>
                    <span class="article-card__category"><?= h($category['name']) ?></span>
                </div>
                <div class="article-card__body">
                    <h2 class="article-card__title" itemprop="headline">
                        <a href="<?= siteUrl('article/' . h($article['slug'])) ?>" itemprop="url">
                            <?= h($article['title']) ?>
                        </a>
                    </h2>
                    <p class="article-card__summary" itemprop="description">
                        <?= h(truncate($article['summary'], 130)) ?>
                    </p>
                    <footer class="article-card__footer">
                        <span class="article-card__author" itemprop="author">
                            <i class="fa-regular fa-user"></i> <?= h($article['author_name'] ?? 'Rédaction') ?>
                        </span>
                        <time class="article-card__date" datetime="<?= h($article['published_at']) ?>" itemprop="datePublished">
                            <i class="fa-regular fa-calendar"></i> <?= formatDate($article['published_at']) ?>
                        </time>
                    </footer>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
