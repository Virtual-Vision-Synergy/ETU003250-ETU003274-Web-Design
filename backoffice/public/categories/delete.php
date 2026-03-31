<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$cat = $db->prepare("SELECT id, name FROM categories WHERE id = ?");
$cat->execute([$id]);
$cat = $cat->fetch();

if (!$cat) {
    flash('error', 'Catégorie introuvable.');
    header('Location: /categories/list');
    exit;
}

$stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

flash('success', 'Catégorie "' . $cat['name'] . '" supprimée.');
header('Location: /categories/list');
exit;
