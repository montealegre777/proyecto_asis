<?php
// ============================================================
// admin/login.php — Login exclusivo para administradores
// ============================================================

session_start();

require_once '../config/db.php';
require_once '../includes/auth_admin.php';

header("cache-control: no-store, no-cache, must-revalidate, max-age=0");
header("cache-control: post-check=0, pre-check=0", false);
header("pragma: no-cache");
// Esta condición verifica que el usuario haya iniciado sesión y que además sea administrador.
if (isset($_SESSION['admin_id']) && intval($_SESSION['id_tip_user'] ?? 0) === 1) {
    header('Location: dashboard_admin.php');
    exit;
}

$error = '';
//verifica el envio del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['documento'] ?? '');
    $pin       = trim($_POST['pin'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($documento) || empty($pin) || empty($password)) {
        $error = 'Por favor, rellene todos los campos';

    } elseif (!validarPin($pin)) {
        $error = 'El PIN debe ser exactamente 4 dígitos numéricos';

    } elseif (!validarPassword($password)) {
        $error = 'La contraseña debe tener exactamente 10 caracteres alfanuméricos';

    } else {
        try {
            $db    = new Database();
            $pdo   = $db->conectar();

            $admin = verificarCredencialesAdmin($pdo, $documento, $pin, $password);

            if ($admin) {
                session_regenerate_id(true);//Genera un nuevo identificador de sesión.

                $_SESSION['admin_id']    = $admin['documento'];
                $_SESSION['documento']   = $admin['documento'];
                $_SESSION['nombres']     = $admin['nombre_completo'];
                $_SESSION['id_tip_user'] = 1;
                $_SESSION['login_time']  = time();

                header('Location: dashboard_admin.php');
                exit;
            } else {
                $error = 'Credenciales administrativas incorrectas';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'Error al conectar con la base de datos.';
        }
    }
}
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
            <form action="" method="POST">
                <h2>Iniciar sesión</h2>

                <?php if (!empty($error)): ?>
                <div
                    style="background: rgba(255,0,0,0.1); border: 1px solid red; color: #ff4a4a; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <div class="input-box">
                    <span class="icon">
                        <i class="fa-solid fa-id-card" style="color: #fff; font-size: 1.1em;"></i>
                    </span>
                    <input type="text" name="documento" required
                        value="<?= htmlspecialchars($_POST['documento'] ?? '') ?>">
                    <label>Documento</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <i class="fa-solid fa-key" style="color: #fff; font-size: 1.1em;"></i>
                    </span>
                    <input type="password" name="pin" maxlength="4" pattern="\d{4}" required inputmode="numeric">
                    <label>PIN</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <i class="fa-solid fa-lock" style="color: #fff; font-size: 1.1em;"></i>
                    </span>
                    <input type="password" name="password" required>
                    <label>Contraseña</label>
                </div>

                <button type="submit" class="btn-login">Login</button>
                <button type="button" class="btn-back" onclick="window.location.href='../index.php'">Volver al
                    inicio</button>
            </form>
        </div>

    </div>

    <?php include '../includes/footer.php'; ?>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>