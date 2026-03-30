<?php $categories = $categories ?? getAllCategories(); ?>
</main><!-- /.main-content -->

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <!-- About -->
            <div class="footer-col footer-col--about">
                <h2 class="footer-heading">Iran Conflit</h2>
                <p>Analyses approfondies, reportages et décryptages sur le conflit en Iran. Une couverture rigoureuse de l'actualité géopolitique, humanitaire et économique.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                    <a href="#" aria-label="Flux RSS"><i class="fa-solid fa-rss"></i></a>
                </div>
            </div>

            <!-- Categories -->
            <div class="footer-col">
                <h3 class="footer-heading">Catégories</h3>
                <ul class="footer-links">
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?= siteUrl('categorie/' . h($cat['slug'])) ?>">
                            <i class="fa-solid fa-chevron-right"></i> <?= h($cat['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Navigation -->
            <div class="footer-col">
                <h3 class="footer-heading">Navigation</h3>
                <ul class="footer-links">
                    <li><a href="<?= siteUrl() ?>"><i class="fa-solid fa-chevron-right"></i> Accueil</a></li>
                    <li><a href="<?= siteUrl('sitemap.xml') ?>"><i class="fa-solid fa-chevron-right"></i> Plan du site</a></li>
                </ul>
                <div class="footer-info">
                    <p><i class="fa-solid fa-circle-info"></i> Site à vocation informationnelle et analytique.</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>
                <i class="fa-regular fa-copyright"></i> <?= date('Y') ?> Iran Conflit — Tous droits réservés.
                Contenu à vocation éducative et informationnelle.
            </p>
        </div>
    </div>
</footer>

<script>
(function() {
    var toggle = document.getElementById('navToggle');
    var nav    = document.getElementById('mainNav');
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            nav.classList.toggle('is-open');
        });
    }
})();
</script>
</body>
</html>
