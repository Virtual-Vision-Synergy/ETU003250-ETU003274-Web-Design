<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$pageTitle = 'Nouvelle catégorie';
$errors    = [];
$formData  = ['name' => '', 'slug' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name'        => trim($_POST['name'] ?? ''),
        'slug'        => trim($_POST['slug'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    if (!$formData['slug'] && $formData['name']) {
        $formData['slug'] = slugify($formData['name']);
    } else {
        $formData['slug'] = slugify($formData['slug']);
    }

    if (!$formData['name']) $errors[] = 'Le nom est requis.';
    if (!$formData['slug']) $errors[] = 'Le slug est requis.';

    if ($formData['slug']) {
        $check = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $check->execute([$formData['slug']]);
        if ($check->fetch()) $errors[] = 'Ce slug est déjà utilisé.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
        $stmt->execute([
            ':name'        => $formData['name'],
            ':slug'        => $formData['slug'],
            ':description' => $formData['description'],
        ]);
        flash('success', 'Catégorie "' . $formData['name'] . '" créée avec succès.');
        header('Location: /categories/list');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-folder-plus"></i> Nouvelle catégorie</h1>
        <a href="/categories" class="btn btn--outline">
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
                           value="<?= h($formData['name']) ?>"
                           required oninput="autoSlug(this.value)"
                           placeholder="Nom de la catégorie">
                </div>
                <div class="form-group">
                    <label for="slug">Slug URL <span class="required">*</span></label>
                    <div class="input-prefix">
                        <span class="prefix">/categorie/</span>
                        <input type="text" id="slug" name="slug"
                               value="<?= h($formData['slug']) ?>" required>
                    </div>
                    <small class="form-help">Généré automatiquement depuis le nom.</small>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Description courte de la catégorie..."><?= h($formData['description']) ?></textarea>
                </div>
                <div style="display:flex;gap:.75rem;margin-top:0.5rem">
                    <button type="submit" class="btn btn--primary">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                    </button>
                    <a href="/categories" class="btn btn--outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function autoSlug(name) {
    var map = {'à':'a','â':'a','ä':'a','é':'e','è':'e','ê':'e','ë':'e','î':'i','ï':'i','ô':'o','ö':'o','ù':'u','û':'u','ü':'u','ç':'c','ñ':'n',"'":" "};
    var slug = name.toLowerCase()
        .replace(/[àâäéèêëîïôöùûüçñ']/g, function(c){ return map[c]||c; })
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/[\s-]+/g, '-');
    document.getElementById('slug').value = slug;
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
