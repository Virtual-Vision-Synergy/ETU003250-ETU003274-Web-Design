<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$db = getDB();
$pageTitle = 'Nouvel article';

$errors   = [];
$formData = ['title'=>'','slug'=>'','summary'=>'','content'=>'','category_id'=>'','author_id'=>'','image_alt'=>'','is_published'=>1];

$categories = $db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$authors    = $db->query("SELECT id, name FROM authors ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title'        => trim($_POST['title'] ?? ''),
        'slug'         => trim($_POST['slug'] ?? ''),
        'summary'      => trim($_POST['summary'] ?? ''),
        'content'      => $_POST['content'] ?? '',
        'category_id'  => (int)($_POST['category_id'] ?? 0),
        'author_id'    => (int)($_POST['author_id'] ?? 0),
        'image_alt'    => trim($_POST['image_alt'] ?? ''),
        'is_published' => isset($_POST['is_published']) ? 1 : 0,
    ];

    if (!$formData['slug'] && $formData['title']) {
        $formData['slug'] = slugify($formData['title']);
    } else {
        $formData['slug'] = slugify($formData['slug']);
    }

    if (!$formData['title'])   $errors[] = 'Le titre est requis.';
    if (!$formData['slug'])    $errors[] = 'Le slug est requis.';
    if (!$formData['summary']) $errors[] = 'Le résumé est requis.';
    if (!$formData['content']) $errors[] = 'Le contenu est requis.';

    // Check slug uniqueness
    if ($formData['slug']) {
        $check = $db->prepare("SELECT id FROM articles WHERE slug = ?");
        $check->execute([$formData['slug']]);
        if ($check->fetch()) $errors[] = 'Ce slug est déjà utilisé.';
    }

    // Image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadError = $_FILES['image']['error'] ?? UPLOAD_ERR_OK;
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        $uploadDir = __DIR__ . '/../uploads/';

        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors[] = 'Le fichier image n\'a pas pu etre televerse correctement.';
        } elseif (!in_array($ext, $allowed)) {
            $errors[] = 'Format d\'image non autorisé (jpg, jpeg, png, webp).';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'L\'image ne doit pas dépasser 5 Mo.';
        } else {
            $filename  = uniqid('img_') . '.' . $ext;

            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
                $errors[] = 'Impossible de creer le dossier uploads.';
            } elseif (!is_writable($uploadDir)) {
                $errors[] = 'Le dossier uploads n\'est pas inscriptible.';
            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/uploads/' . $filename;
            } else {
                $errors[] = 'Erreur lors du téléchargement de l\'image.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO articles (title, slug, summary, content, image, image_alt, author_id, category_id, is_published)
            VALUES (:title, :slug, :summary, :content, :image, :image_alt, :author_id, :category_id, :is_published)
        ");
        $stmt->execute([
            ':title'        => $formData['title'],
            ':slug'         => $formData['slug'],
            ':summary'      => $formData['summary'],
            ':content'      => $formData['content'],
            ':image'        => $imagePath,
            ':image_alt'    => $formData['image_alt'],
            ':author_id'    => $formData['author_id'] ?: null,
            ':category_id'  => $formData['category_id'] ?: null,
            ':is_published' => $formData['is_published'],
        ]);
        flash('success', 'Article créé avec succès.');
        header('Location: /articles/list.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <h1 class="admin-page-title"><i class="fa-solid fa-plus"></i> Nouvel article</h1>
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
            <!-- Main column -->
            <div class="form-main">
                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Contenu</h2></div>
                    <div class="admin-card__body">

                        <div class="form-group">
                            <label for="title">Titre <span class="required">*</span></label>
                            <input type="text" id="title" name="title"
                                   value="<?= h($formData['title']) ?>"
                                   required oninput="autoSlug(this.value)">
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug URL <span class="required">*</span></label>
                            <div class="input-prefix">
                                <span class="prefix">/article/</span>
                                <input type="text" id="slug" name="slug"
                                       value="<?= h($formData['slug']) ?>" required>
                            </div>
                            <small class="form-help">Généré automatiquement depuis le titre. Ne modifiez qu'en cas de besoin.</small>
                        </div>

                        <div class="form-group">
                            <label for="summary">Résumé <span class="required">*</span></label>
                            <textarea id="summary" name="summary" rows="3" required><?= h($formData['summary']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="content">Contenu HTML <span class="required">*</span></label>
                            <textarea id="content" name="content" rows="18" class="content-editor"><?= h($formData['content']) ?></textarea>
                            <small class="form-help">Vous pouvez utiliser les balises HTML : &lt;p&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;blockquote&gt;, etc.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="form-side">

                <div class="admin-card">
                    <div class="admin-card__header"><h2 class="admin-card__title">Publication</h2></div>
                    <div class="admin-card__body">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_published" value="1"
                                       <?= $formData['is_published'] ? 'checked' : '' ?>>
                                <span>Publier immédiatement</span>
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
                                <option value="<?= $cat['id'] ?>"
                                    <?= $formData['category_id'] == $cat['id'] ? 'selected' : '' ?>>
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
                                <option value="<?= $au['id'] ?>"
                                    <?= $formData['author_id'] == $au['id'] ? 'selected' : '' ?>>
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
                        <div class="form-group">
                            <label for="image">Fichier image</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                            <small class="form-help">Max 5 Mo — JPG, PNG, WEBP</small>
                        </div>
                        <div class="form-group">
                            <label for="image_alt">Texte alternatif (alt) <span class="required">*</span></label>
                            <input type="text" id="image_alt" name="image_alt"
                                   value="<?= h($formData['image_alt']) ?>"
                                   placeholder="Description de l'image pour l'accessibilité et le SEO">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
function autoSlug(title) {
    var map = {'à':'a','â':'a','ä':'a','é':'e','è':'e','ê':'e','ë':'e','î':'i','ï':'i','ô':'o','ö':'o','ù':'u','û':'u','ü':'u','ç':'c','ñ':'n',"'":" "};
    var slug = title.toLowerCase()
        .replace(/[àâäéèêëîïôöùûüçñ']/g, function(c){ return map[c]||c; })
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/[\s-]+/g, '-');
    document.getElementById('slug').value = slug;
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
