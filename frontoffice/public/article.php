<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$slug    = $_GET['slug'] ?? '';
$article = $slug ? getArticleBySlug($slug) : null;

if (!$article) {
    http_response_code(404);
    $pageTitle       = 'Article introuvable';
    $pageDescription = 'Cet article n\'existe pas ou a été supprimé.';
    $pageCanonical   = siteUrl();
    $categories      = getAllCategories();
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="container">
        <div class="error-page">
            <i class="fa-solid fa-circle-exclamation"></i>
            <h1>Article introuvable</h1>
            <p>Cet article n'existe pas ou a été supprimé.</p>
            <a href="<?= siteUrl() ?>" class="btn btn--primary">
                <i class="fa-solid fa-house"></i> Retour à l'accueil
            </a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$related    = getRelatedArticles($article['id'], $article['category_id'] ?? 0);
$categories = getAllCategories();

$pageTitle       = $article['title'];
$pageDescription = truncate($article['summary'], 160);
$pageCanonical   = siteUrl('article/' . $article['slug']);
$ogType          = 'article';
$ogImage         = $article['image'] ? siteUrl($article['image']) : null;

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
            <?php if ($article['category_name']): ?>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                <a href="<?= siteUrl('categorie/' . h($article['category_slug'])) ?>" itemprop="item">
                    <span itemprop="name"><?= h($article['category_name']) ?></span>
                </a>
                <meta itemprop="position" content="2">
            </li>
            <?php endif; ?>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                <span itemprop="name"><?= h(truncate($article['title'], 50)) ?></span>
                <meta itemprop="position" content="3">
            </li>
        </ol>
    </div>
</nav>

<!-- Article -->
<div class="container article-layout">
    <article class="article-main" itemscope itemtype="https://schema.org/NewsArticle">
        <meta itemprop="url" content="<?= h($pageCanonical) ?>">

        <!-- Header -->
        <header class="article-header">
            <?php if ($article['category_name']): ?>
            <a href="<?= siteUrl('categorie/' . h($article['category_slug'])) ?>"
               class="category-badge" itemprop="articleSection">
                <?= h($article['category_name']) ?>
            </a>
            <?php endif; ?>

            <h1 class="article-title" itemprop="headline">
                <?= h($article['title']) ?>
            </h1>

            <p class="article-summary" itemprop="description">
                <?= h($article['summary']) ?>
            </p>

            <div class="article-meta">
                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <i class="fa-regular fa-user"></i>
                    <span itemprop="name"><?= h($article['author_name'] ?? 'Rédaction') ?></span>
                </span>
                <time datetime="<?= h($article['published_at']) ?>" itemprop="datePublished">
                    <i class="fa-regular fa-calendar"></i> <?= formatDate($article['published_at']) ?>
                </time>
                <?php if ($article['updated_at'] !== $article['published_at']): ?>
                <time datetime="<?= h($article['updated_at']) ?>" itemprop="dateModified">
                    <i class="fa-regular fa-pen-to-square"></i> Mis à jour le <?= formatDate($article['updated_at']) ?>
                </time>
                <?php endif; ?>
            </div>
        </header>

        <!-- Hero Image -->
        <?php if ($article['image']): ?>
        <figure class="article-figure">
            <img src="<?= h($article['image']) ?>"
                 alt="<?= h($article['image_alt'] ?? $article['title']) ?>"
                 class="article-figure__img"
                 itemprop="image">
            <?php if ($article['image_alt']): ?>
            <figcaption class="article-figure__caption">
                <i class="fa-solid fa-camera"></i> <?= h($article['image_alt']) ?>
            </figcaption>
            <?php endif; ?>
        </figure>
        <?php endif; ?>

        <!-- Content -->
        <div class="article-content" itemprop="articleBody">
            <?= $article['content'] ?>
        </div>

        <!-- Share -->
        <div class="article-share">
            <h3 class="article-share__title">Partager cet article</h3>
            <div class="article-share__links">
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageCanonical) ?>&text=<?= urlencode($article['title']) ?>"
                   target="_blank" rel="noopener noreferrer" class="share-btn share-btn--twitter" aria-label="Partager sur X">
                    <i class="fa-brands fa-x-twitter"></i> X (Twitter)
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageCanonical) ?>"
                   target="_blank" rel="noopener noreferrer" class="share-btn share-btn--facebook" aria-label="Partager sur Facebook">
                    <i class="fa-brands fa-facebook-f"></i> Facebook
                </a>
                <a href="https://t.me/share/url?url=<?= urlencode($pageCanonical) ?>&text=<?= urlencode($article['title']) ?>"
                   target="_blank" rel="noopener noreferrer" class="share-btn share-btn--telegram" aria-label="Partager sur Telegram">
                    <i class="fa-brands fa-telegram"></i> Telegram
                </a>
            </div>
        </div>

        <!-- Author Bio -->
        <?php if ($article['author_name'] && $article['author_bio']): ?>
        <div class="author-box">
            <div class="author-box__avatar" aria-hidden="true">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="author-box__info">
                <h4 class="author-box__name"><?= h($article['author_name']) ?></h4>
                <p class="author-box__bio"><?= h($article['author_bio']) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </article>

    <!-- Sidebar -->
    <aside class="article-sidebar" aria-label="Articles connexes">
        <?php if (!empty($related)): ?>
        <div class="sidebar-widget">
            <h3 class="sidebar-widget__title">
                <i class="fa-solid fa-link"></i> Articles connexes
            </h3>
            <ul class="sidebar-articles">
                <?php foreach ($related as $rel): ?>
                <li class="sidebar-article">
                    <a href="<?= siteUrl('article/' . h($rel['slug'])) ?>" class="sidebar-article__link">
                        <span class="sidebar-article__title"><?= h($rel['title']) ?></span>
                        <time class="sidebar-article__date" datetime="<?= h($rel['published_at']) ?>">
                            <i class="fa-regular fa-calendar"></i> <?= formatDate($rel['published_at']) ?>
                        </time>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Categories widget -->
        <div class="sidebar-widget">
            <h3 class="sidebar-widget__title">
                <i class="fa-solid fa-folder-open"></i> Rubriques
            </h3>
            <ul class="sidebar-categories">
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="<?= siteUrl('categorie/' . h($cat['slug'])) ?>">
                        <i class="fa-solid fa-chevron-right"></i> <?= h($cat['name']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
