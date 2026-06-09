<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/styles_login.css">
    <link rel="stylesheet" href="../css/styles_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- Encabezado principal -->
    <header class="site-header">
        <div class="site-header__inner">
            <div class="site-header__brand">
                <div class="site-header__logo">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <span class="site-header__name">Sistema de Asistencias</span>
                    <span class="site-header__sub">Gestión de Personal</span>
                </div>
            </div>
            <div class="site-header__date">
                <i class="fa-regular fa-calendar" style="margin-right:6px;color:#00d4ff"></i>
                <?php echo date('d/m/Y'); ?>
            </div>
        </div>
    </header>

    <div class="wrapper">

        <div class="login-box">
            <form action="">
                <h2>Iniciar sesión</h2>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="mail"></ion-icon>
                    </span>
                    <input type="text" required>
                    <label>Documento</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="lock-closed"></ion-icon>
                    </span>
                    <input type="password" required>
                    <label>PIN</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="lock-closed"></ion-icon>
                    </span>
                    <input type="password" required>
                    <label>Contraseña</label>
                </div>

                <button type="submit" class="btn-login">Login</button>
                <button type="button" class="btn-back" onclick="window.location.href='../index.php'">Volver al
                    inicio</button>
            </form>
        </div>

    </div>

    <?php include '../includes/footer.php'; ?>


</body>

</html>