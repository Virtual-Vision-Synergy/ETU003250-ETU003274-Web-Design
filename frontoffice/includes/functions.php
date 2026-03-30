<?php
// Helper functions

function siteUrl(string $path = ''): string {
    $base = $_ENV['SITE_URL'] ?? getenv('SITE_URL') ?: 'http://localhost:8080';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function truncate(string $text, int $length = 160): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

function formatDate(string $date, string $format = 'd F Y'): string {
    $dt = new DateTime($date);
    $months = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];
    $day   = $dt->format('d');
    $month = $months[(int)$dt->format('n')];
    $year  = $dt->format('Y');
    return "$day $month $year";
}

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(
        ['à','â','ä','é','è','ê','ë','î','ï','ô','ö','ù','û','ü','ç','ñ'],
        ['a','a','a','e','e','e','e','i','i','o','o','u','u','u','c','n'],
        $text
    );
    $text = preg_replace('/[^a-z0-9\s\-]/', '', $text);
    $text = preg_replace('/[\s\-]+/', '-', trim($text));
    return $text;
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function getLatestArticles(int $limit = 6, int $offset = 0): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT a.id, a.title, a.slug, a.summary, a.image, a.image_alt, a.published_at,
               c.name AS category_name, c.slug AS category_slug,
               au.name AS author_name
        FROM articles a
        LEFT JOIN categories c  ON a.category_id = c.id
        LEFT JOIN authors    au ON a.author_id    = au.id
        WHERE a.is_published = 1
        ORDER BY a.published_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getArticleBySlug(string $slug): ?array {
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug,
               au.name AS author_name, au.bio AS author_bio
        FROM articles a
        LEFT JOIN categories c  ON a.category_id = c.id
        LEFT JOIN authors    au ON a.author_id    = au.id
        WHERE a.slug = :slug AND a.is_published = 1
    ");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getArticlesByCategory(string $slug, int $limit = 9): array {
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT a.id, a.title, a.slug, a.summary, a.image, a.image_alt, a.published_at,
               c.name AS category_name, c.slug AS category_slug,
               au.name AS author_name
        FROM articles a
        LEFT JOIN categories c  ON a.category_id = c.id
        LEFT JOIN authors    au ON a.author_id    = au.id
        WHERE c.slug = :slug AND a.is_published = 1
        ORDER BY a.published_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':slug', $slug);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategoryBySlug(string $slug): ?array {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getAllCategories(): array {
    $db = getDB();
    return $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}

function getRelatedArticles(int $articleId, int $categoryId, int $limit = 3): array {
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT a.id, a.title, a.slug, a.summary, a.image, a.image_alt, a.published_at,
               c.name AS category_name, c.slug AS category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.category_id = :cat AND a.id != :id AND a.is_published = 1
        ORDER BY a.published_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTotalArticleCount(): int {
    $db = getDB();
    return (int)$db->query("SELECT COUNT(*) FROM articles WHERE is_published = 1")->fetchColumn();
}

function searchArticles(string $query, int $limit = 9): array {
    $db   = getDB();
    $term = '%' . $query . '%';
    $stmt = $db->prepare("
        SELECT a.id, a.title, a.slug, a.summary, a.image, a.image_alt, a.published_at,
               c.name AS category_name, c.slug AS category_slug,
               au.name AS author_name
        FROM articles a
        LEFT JOIN categories c  ON a.category_id = c.id
        LEFT JOIN authors    au ON a.author_id    = au.id
        WHERE a.is_published = 1
          AND (a.title LIKE :term1 OR a.summary LIKE :term2 OR a.content LIKE :term3)
        ORDER BY a.published_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':term1', $term);
    $stmt->bindValue(':term2', $term);
    $stmt->bindValue(':term3', $term);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

