<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../proyecto_asis/css/styles_footer.css">
</head>

<body>
    <?php
// includes/footer.php
// Desde admin/  →  include '../includes/footer.php';
// Desde raíz    →  include 'includes/footer.php';
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

</body>

</html>