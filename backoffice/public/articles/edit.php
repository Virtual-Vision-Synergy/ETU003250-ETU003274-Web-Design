<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$article = $db->prepare("SELECT * FROM articles WHERE id = ?");
$article->execute([$id]);
$article = $article->fetch();

if (!$article) {
    flash('error', 'Article introuvable.');
    header('Location: /articles/list.php');
    exit;
}

$pageTitle  = 'Modifier l\'article';
$categories = $db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$authors    = $db->query("SELECT id, name FROM authors ORDER BY name")->fetchAll();
$errors     = [];
$formData   = $article;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'id'           => $id,
        'title'        => trim($_POST['title'] ?? ''),
        'slug'         => trim($_POST['slug'] ?? ''),
        'summary'      => trim($_POST['summary'] ?? ''),
        'content'      => $_POST['content'] ?? '',
        'category_id'  => (int)($_POST['category_id'] ?? 0),
        'author_id'    => (int)($_POST['author_id'] ?? 0),
        'image_alt'    => trim($_POST['image_alt'] ?? ''),
        'is_published' => isset($_POST['is_published']) ? 1 : 0,
        'image'        => $article['image'],
    ];

    if (!$formData['slug']) $formData['slug'] = slugify($formData['title']);
    else $formData['slug'] = slugify($formData['slug']);

    if (!$formData['title'])   $errors[] = 'Le titre est requis.';
    if (!$formData['slug'])    $errors[] = 'Le slug est requis.';
    if (!$formData['summary']) $errors[] = 'Le résumé est requis.';
    if (!$formData['content']) $errors[] = 'Le contenu est requis.';

    // Check slug uniqueness (excluding current article)
    if ($formData['slug']) {
        $check = $db->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $check->execute([$formData['slug'], $id]);
        if ($check->fetch()) $errors[] = 'Ce slug est déjà utilisé par un autre article.';
    }

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format d\'image non autorisé.';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'L\'image ne doit pas dépasser 5 Mo.';
        } else {
            $filename  = uniqid('img_') . '.' . $ext;
            $uploadDir = '/var/www/html/public/uploads/';
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $formData['image'] = '/uploads/' . $filename;
            } else {
                $errors[] = 'Erreur lors du téléchargement de l\'image.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE articles
            SET title=:title, slug=:slug, summary=:summary, content=:content,
                image=:image, image_alt=:image_alt, author_id=:author_id,
                category_id=:category_id, is_published=:is_published
            WHERE id=:id
        ");
        $stmt->execute([
            ':title'        => $formData['title'],
            ':slug'         => $formData['slug'],
            ':summary'      => $formData['summary'],
            ':content'      => $formData['content'],
            ':image'        => $formData['image'],
            ':image_alt'    => $formData['image_alt'],
            ':author_id'    => $formData['author_id'] ?: null,
            ':category_id'  => $formData['category_id'] ?: null,
            ':is_published' => $formData['is_published'],
            ':id'           => $id,
        ]);
        flash('success', 'Article modifié avec succès.');
        header('Location: /articles/list.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-pen-to-square"></i> Modifier l'article</h1>
        <a href="/articles/list.php" class="btn btn--outline">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert--danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul class="alert-list">
            <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="admin-form">
        <div class="form-grid">
            <div class="form-main">
                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Contenu</h2></div>
                    <div class="admin-card__body">
                        <div class="form-group">
                            <label for="title">Titre <span class="required">*</span></label>
                            <input type="text" id="title" name="title" value="<?= h($formData['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug URL <span class="required">*</span></label>
                            <div class="input-prefix">
                                <span class="prefix">/article/</span>
                                <input type="text" id="slug" name="slug" value="<?= h($formData['slug']) ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="summary">Résumé <span class="required">*</span></label>
                            <textarea id="summary" name="summary" rows="3" required><?= h($formData['summary']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="content">Contenu HTML <span class="required">*</span></label>
                            <textarea id="content" name="content" rows="18" class="content-editor"><?= h($formData['content']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-side">
                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Publication</h2></div>
                    <div class="admin-card__body">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_published" value="1"
                                       <?= $formData['is_published'] ? 'checked' : '' ?>>
                                <span>Publié</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn--primary btn--full">
                            <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                        </button>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Classification</h2></div>
                    <div class="admin-card__body">
                        <div class="form-group">
                            <label for="category_id">Catégorie</label>
                            <select id="category_id" name="category_id">
                                <option value="">-- Choisir --</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $formData['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= h($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="author_id">Auteur</label>
                            <select id="author_id" name="author_id">
                                <option value="">-- Choisir --</option>
                                <?php foreach ($authors as $au): ?>
                                <option value="<?= $au['id'] ?>" <?= $formData['author_id'] == $au['id'] ? 'selected' : '' ?>>
                                    <?= h($au['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Image</h2></div>
                    <div class="admin-card__body">
                        <?php if ($formData['image']): ?>
                        <div class="current-image">
                            <p class="form-help"><i class="fa-solid fa-image"></i> Image actuelle : <code><?= h(basename($formData['image'])) ?></code></p>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="image">Remplacer l'image</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="form-group">
                            <label for="image_alt">Texte alternatif (alt)</label>
                            <input type="text" id="image_alt" name="image_alt" value="<?= h($formData['image_alt'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
