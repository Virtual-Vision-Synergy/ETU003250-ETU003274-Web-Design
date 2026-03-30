<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$db = getDB();
$pageTitle = 'Tableau de bord';

$totalArticles   = (int)$db->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publishedCount  = (int)$db->query("SELECT COUNT(*) FROM articles WHERE is_published = 1")->fetchColumn();
$draftCount      = $totalArticles - $publishedCount;
$totalCategories = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalAuthors    = (int)$db->query("SELECT COUNT(*) FROM authors")->fetchColumn();

$recentArticles = $db->query("
    SELECT a.id, a.title, a.slug, a.is_published, a.published_at,
           c.name AS category_name, au.name AS author_name
    FROM articles a
    LEFT JOIN categories c  ON a.category_id = c.id
    LEFT JOIN authors    au ON a.author_id    = au.id
    ORDER BY a.published_at DESC LIMIT 8
")->fetchAll();

$successMessage = flash('success');

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title">
            <i class="fa-solid fa-gauge-high"></i> Tableau de bord
        </h1>
    </div>

    <?php if ($successMessage): ?>
    <div class="alert alert--success">
        <i class="fa-solid fa-circle-check"></i> <?= h($successMessage) ?>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card__icon stat-card__icon--blue">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $totalArticles ?></div>
                <div class="stat-card__label">Articles au total</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon stat-card__icon--green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $publishedCount ?></div>
                <div class="stat-card__label">Articles publiés</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon stat-card__icon--orange">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $draftCount ?></div>
                <div class="stat-card__label">Brouillons</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon stat-card__icon--red">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $totalCategories ?></div>
                <div class="stat-card__label">Catégories</div>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="quick-actions">
        <a href="/articles/create.php" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i> Nouvel article
        </a>
        <a href="/categories/create.php" class="btn btn--secondary">
            <i class="fa-solid fa-folder-plus"></i> Nouvelle catégorie
        </a>
        <a href="/articles/list.php" class="btn btn--outline">
            <i class="fa-solid fa-list"></i> Tous les articles
        </a>
    </div>

    <!-- Recent articles table -->
    <div class="admin-card">
        <div class="admin-card__header">
            <h2 class="admin-card__title">
                <i class="fa-solid fa-clock-rotate-left"></i> Articles récents
            </h2>
            <a href="/articles/list.php" class="btn btn--sm btn--outline">
                Voir tout <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentArticles)): ?>
                    <tr><td colspan="6" class="table-empty">Aucun article.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recentArticles as $art): ?>
                    <tr>
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
                            <a href="/articles/edit.php?id=<?= $art['id'] ?>" class="action-btn action-btn--edit" title="Modifier">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="/articles/delete.php?id=<?= $art['id'] ?>" class="action-btn action-btn--delete" title="Supprimer"
                               onclick="return confirm('Supprimer cet article ?')">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
