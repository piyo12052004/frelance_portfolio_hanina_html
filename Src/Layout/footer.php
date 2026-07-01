</main>

</div>

</div>
<?php if (isset($_SESSION['flash'])) : ?>
    <script>
        Swal.fire({
            icon: "<?= $_SESSION['flash']['type']; ?>",
            title: "<?= $_SESSION['flash']['title']; ?>",
            text: "<?= $_SESSION['flash']['message']; ?>",
            timer: 3000,
            showConfirmButton: true
        });
    </script>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<script src="<?= BASE_URL ?>/Asset/js/app.js"></script>
</body>

</html>