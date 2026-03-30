<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle       = 'Actualité et Analyses sur la Guerre en Iran';
$pageDescription = 'Suivez en temps réel l\'actualité du conflit en Iran : analyses géopolitiques, reportages de terrain, impact humanitaire et décryptages économiques.';
$pageCanonical   = siteUrl();

$latestArticles = getLatestArticles(7);
$heroArticle    = isset($latestArticles[0]) ? $latestArticles[0] : null; // Keep hero in latest list as well
$categories     = getAllCategories();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero -->
<?php if ($heroArticle): ?>
<section class="hero" aria-label="Article à la une">
    <div class="container hero__inner">
        <div class="hero__meta">
            <?php if ($heroArticle['category_name']): ?>
            <a href="<?= siteUrl('categorie/' . h($heroArticle['category_slug'])) ?>" class="category-badge">
                <?= h($heroArticle['category_name']) ?>
            </a>
            <?php endif; ?>
        </div>
        <h1 class="hero__title">
            <a href="<?= siteUrl('article/' . h($heroArticle['slug'])) ?>">
                <?= h($heroArticle['title']) ?>
            </a>
        </h1>
        <p class="hero__summary"><?= h(truncate($heroArticle['summary'], 220)) ?></p>
        <div class="hero__byline">
            <span><i class="fa-regular fa-user"></i> <?= h($heroArticle['author_name'] ?? 'Rédaction') ?></span>
            <span><i class="fa-regular fa-clock"></i> <?= formatDate($heroArticle['published_at']) ?></span>
        </div>
        <a href="<?= siteUrl('article/' . h($heroArticle['slug'])) ?>" class="btn btn--primary">
            Lire l'article <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Latest Articles Grid -->
<section class="section" aria-labelledby="latest-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="latest-heading" class="section-title">
                <i class="fa-solid fa-newspaper"></i> Derniers Articles
            </h2>
            <div class="section-divider"></div>
        </div>

        <?php if (empty($latestArticles)): ?>
        <p class="empty-state"><i class="fa-solid fa-circle-info"></i> Aucun article disponible pour le moment.</p>
        <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($latestArticles as $article): ?>
            <article class="article-card" itemscope itemtype="https://schema.org/NewsArticle">
                <div class="article-card__img-wrap">
                    <?php if ($article['image']): ?>
                    <img src="<?= h($article['image']) ?>"
                         alt="<?= h($article['image_alt'] ?? $article['title']) ?>"
                         class="article-card__img" loading="lazy"
                         itemprop="image">
                    <?php else: ?>
                    <div class="article-card__img-placeholder" aria-hidden="true">
                        <i class="fa-solid fa-globe-europe"></i>
                    </div>
                    <?php endif; ?>
                    <?php if ($article['category_name']): ?>
                    <a href="<?= siteUrl('categorie/' . h($article['category_slug'])) ?>"
                       class="article-card__category" itemprop="articleSection">
                        <?= h($article['category_name']) ?>
                    </a>
                    <?php endif; ?>
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

<!-- Categories section -->
<section class="section section--alt" aria-labelledby="categories-heading">
    <div class="container">
        <div class="section-header">
            <h2 id="categories-heading" class="section-title">
                <i class="fa-solid fa-folder-open"></i> Rubriques
            </h2>
            <div class="section-divider"></div>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= siteUrl('categorie/' . h($cat['slug'])) ?>" class="category-card">
                <span class="category-card__icon">
                    <?php
                    $icons = [
                        'politique-diplomatie'  => 'fa-landmark',
                        'operations-militaires' => 'fa-shield-halved',
                        'impact-humanitaire'    => 'fa-hand-holding-heart',
                        'economie-sanctions'    => 'fa-chart-line',
                        'geopolitique-regionale'=> 'fa-earth-europe',
                    ];
                    $icon = $icons[$cat['slug']] ?? 'fa-folder';
                    ?>
                    <i class="fa-solid <?= $icon ?>"></i>
                </span>
                <h3 class="category-card__name"><?= h($cat['name']) ?></h3>
                <?php if ($cat['description']): ?>
                <p class="category-card__desc"><?= h(truncate($cat['description'], 80)) ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
