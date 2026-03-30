<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$pageTitle = 'Gestion des catégories';

$success = flash('success');
$error   = flash('error');

$categories = $db->query("
    SELECT c.*, COUNT(a.id) AS article_count
    FROM categories c
    LEFT JOIN articles a ON a.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-folder-open"></i> Catégories</h1>
        <a href="/categories/create.php" class="btn btn--primary">
            <i class="fa-solid fa-folder-plus"></i> Nouvelle catégorie
        </a>
    </div>

    <?php if ($success): ?>
    <div class="alert alert--success"><i class="fa-solid fa-circle-check"></i> <?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert--danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= h($error) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr><td colspan="5" class="table-empty">Aucune catégorie.</td></tr>
                    <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td class="td-title"><?= h($cat['name']) ?></td>
                        <td><code><?= h($cat['slug']) ?></code></td>
                        <td>
                            <span class="badge badge--cat">
                                <i class="fa-solid fa-newspaper"></i> <?= $cat['article_count'] ?>
                            </span>
                        </td>
                        <td class="td-actions">
                            <a href="/categories/edit.php?id=<?= $cat['id'] ?>" class="action-btn action-btn--edit" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="/categories/delete.php?id=<?= $cat['id'] ?>"
                               class="action-btn action-btn--delete" title="Supprimer"
                               onclick="return confirm('Supprimer cette catégorie ? Les articles associés perdront leur catégorie.')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
