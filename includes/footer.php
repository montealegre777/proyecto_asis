<?php
// includes/footer.php
// Desde admin/  →  include '../includes/footer.php';
// Desde raíz    →  include 'includes/footer.php';
?>

<style>
.sf-footer {
    width: 100%;
    margin-top: 0px;
    padding: 20px 32px 18px;
    background: #eff2f4ff;
    font-family: 'Segoe UI', system-ui, sans-serif;
    text-align: center;

    /* Sombra profesional hacia arriba (Eje Y negativo) */
    box-shadow: 0 -4px 20px -1px rgba(0, 0, 0, 0.01), 0 -2px 4px -1px rgba(0, 0, 0, 0.02);
}

.sf-footer__title {
    font-size: 0.88rem;
    font-weight: 600;
    color: #01234dff;
    margin-bottom: 15px;
}

.sf-footer__sub {
    font-size: 0.75rem;
    color: #8a96a8;
}

.sf-footer__sub span {
    color: #b0bac8;
    margin: 0 6px;
}
</style>

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