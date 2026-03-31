<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$article = $db->prepare("SELECT id, title FROM articles WHERE id = ?");
$article->execute([$id]);
$article = $article->fetch();

if (!$article) {
    flash('error', 'Article introuvable.');
    header('Location: /articles/list');
    exit;
}

$stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
$stmt->execute([$id]);

flash('success', 'Article "' . $article['title'] . '" supprimé.');
header('Location: /articles/list');
exit;
