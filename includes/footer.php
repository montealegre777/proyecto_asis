<?php
// includes/footer.php — snippet parcial, sin estructura HTML completa
?>

<footer class="sf-footer">
    <div class="sf-footer__title">Sistema de Control de Asistencias</div>
    <div class="sf-footer__sub">
        &copy; <?php echo date('Y'); ?> Todos los derechos reservados
        <?php if (isset($_SESSION['admin_usuario'])): ?>
        <span>&middot;</span>
        <?php echo htmlspecialchars($_SESSION['admin_usuario']); ?>
        <?php endif; ?>
    </div>
</footer>
