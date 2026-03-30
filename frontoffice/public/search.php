<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$query    = trim($_GET['q'] ?? '');
$articles = $query ? searchArticles($query) : [];
$categories = getAllCategories();

$pageTitle       = $query ? 'Résultats pour "' . $query . '"' : 'Recherche';
$pageDescription = 'Résultats de recherche pour : ' . $query;
$pageCanonical   = siteUrl('recherche?q=' . urlencode($query));

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" aria-labelledby="search-heading">
    <div class="container">
        <h1 id="search-heading" class="page-title">
            <i class="fa-solid fa-magnifying-glass"></i>
            <?php if ($query): ?>
                Résultats pour <em>"<?= h($query) ?>"</em>
            <?php else: ?>
                Recherche
            <?php endif; ?>
        </h1>

        <form class="search-form" action="<?= siteUrl('recherche') ?>" method="get" role="search">
            <label for="search-input">Rechercher un article :</label>
            <div class="search-form__row">
                <input type="search" id="search-input" name="q"
                       value="<?= h($query) ?>"
                       placeholder="Ex : sanctions, nucléaire, humanitaire..."
                       autocomplete="off">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                </button>
            </div>
        </form>

        <?php if ($query): ?>
        <p class="search-count">
            <i class="fa-solid fa-circle-info"></i>
            <?= count($articles) ?> résultat<?= count($articles) > 1 ? 's' : '' ?> trouvé<?= count($articles) > 1 ? 's' : '' ?>.
        </p>

        <?php if (empty($articles)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-file-circle-question"></i>
            <p>Aucun article ne correspond à votre recherche. Essayez avec d'autres mots-clés.</p>
        </div>
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
                    <?php if ($article['category_name']): ?>
                    <a href="<?= siteUrl('categorie/' . h($article['category_slug'])) ?>"
                       class="article-card__category">
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
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
