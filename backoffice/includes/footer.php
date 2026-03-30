</main>

<script>
(function() {
    var toggle  = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar--collapsed');
            document.querySelector('.admin-topbar').classList.toggle('topbar--shifted');
            document.querySelector('.admin-main').classList.toggle('main--shifted');
        });
    }
})();
</script>
</body>
</html>
