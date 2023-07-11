<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
<?php if (isset($page)):?><script src="<?php echo base_url("assets/js/" . $page . ".min.js?ver=" . getenv('VER')); ?>"></script><?php endif; ?>
<script src="<?php echo base_url("assets/js/script.min.js?ver=" . getenv('VER')); ?>"></script>
<?php if (isset($modal) && $modal === true):?><script src="<?php echo base_url("assets/js/croppieModal.min.js?ver=" . getenv('VER')); ?>"></script><?php endif; ?>
</body>
</html>