<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$pageTitle = 'Gestion des articles';

$success = flash('success');
$error   = flash('error');

$articles = $db->query("
    SELECT a.id, a.title, a.slug, a.is_published, a.published_at,
           c.name AS category_name, au.name AS author_name
    FROM articles a
    LEFT JOIN categories c  ON a.category_id = c.id
    LEFT JOIN authors    au ON a.author_id    = au.id
    ORDER BY a.published_at DESC
")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-newspaper"></i> Articles</h1>
        <a href="/articles/create" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i> Nouvel article
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
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                    <tr><td colspan="7" class="table-empty">Aucun article.</td></tr>
                    <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                    <tr>
                        <td><?= $art['id'] ?></td>
                        <td class="td-title"><?= h($art['title']) ?></td>
                        <td><span class="badge badge--cat"><?= h($art['category_name'] ?? '—') ?></span></td>
                        <td><?= h($art['author_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($art['is_published']): ?>
                            <span class="badge badge--success"><i class="fa-solid fa-circle-dot"></i> Publié</span>
                            <?php else: ?>
                            <span class="badge badge--warning"><i class="fa-regular fa-clock"></i> Brouillon</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($art['published_at']) ?></td>
                        <td class="td-actions">
                            <a href="/articles/edit/<?= $art['id'] ?>" class="action-btn action-btn--edit" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="/articles/delete/<?= $art['id'] ?>"
                               class="action-btn action-btn--delete" title="Supprimer"
                               onclick="return confirm('Supprimer définitivement cet article ?')">
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
