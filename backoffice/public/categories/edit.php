<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$cat = $db->prepare("SELECT * FROM categories WHERE id = ?");
$cat->execute([$id]);
$cat = $cat->fetch();

if (!$cat) {
    flash('error', 'Catégorie introuvable.');
    header('Location: /categories/list.php');
    exit;
}

$pageTitle = 'Modifier la catégorie';
$errors    = [];
$formData  = $cat;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'id'          => $id,
        'name'        => trim($_POST['name'] ?? ''),
        'slug'        => trim($_POST['slug'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    if (!$formData['slug']) $formData['slug'] = slugify($formData['name']);
    else $formData['slug'] = slugify($formData['slug']);

    if (!$formData['name']) $errors[] = 'Le nom est requis.';
    if (!$formData['slug']) $errors[] = 'Le slug est requis.';

    if ($formData['slug']) {
        $check = $db->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $check->execute([$formData['slug'], $id]);
        if ($check->fetch()) $errors[] = 'Ce slug est déjà utilisé.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE categories SET name=:name, slug=:slug, description=:description WHERE id=:id");
        $stmt->execute([
            ':name'        => $formData['name'],
            ':slug'        => $formData['slug'],
            ':description' => $formData['description'],
            ':id'          => $id,
        ]);
        flash('success', 'Catégorie modifiée avec succès.');
        header('Location: /categories/list.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-pen-to-square"></i> Modifier la catégorie</h1>
        <a href="/categories/list.php" class="btn btn--outline">
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

    <div class="admin-card" style="max-width:640px">
        <div class="admin-card__header">
            <h2 class="admin-card__title"><i class="fa-solid fa-folder-open"></i> Informations</h2>
        </div>
        <div class="admin-card__body">
            <form method="post" class="admin-form">
                <div class="form-group">
                    <label for="name">Nom <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= h($formData['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="slug">Slug URL <span class="required">*</span></label>
                    <div class="input-prefix">
                        <span class="prefix">/categorie/</span>
                        <input type="text" id="slug" name="slug"
                               value="<?= h($formData['slug']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?= h($formData['description'] ?? '') ?></textarea>
                </div>
                <div style="display:flex;gap:.75rem;margin-top:0.5rem">
                    <button type="submit" class="btn btn--primary">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                    </button>
                    <a href="/categories/list.php" class="btn btn--outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
