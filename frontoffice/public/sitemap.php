<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$db    = getDB();
$base  = siteUrl();
$articles = $db->query("SELECT slug, updated_at FROM articles WHERE is_published = 1 ORDER BY published_at DESC")->fetchAll();
$cats     = $db->query("SELECT slug FROM categories")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc><?= $base ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Categories -->
    <?php foreach ($cats as $cat): ?>
    <url>
        <loc><?= h(siteUrl('categorie/' . $cat['slug'])) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>

    <!-- Articles -->
    <?php foreach ($articles as $article): ?>
    <url>
        <loc><?= h(siteUrl('article/' . $article['slug'])) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($article['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php endforeach; ?>
</urlset>
