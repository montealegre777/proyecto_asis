<?php
session_start();
require_once __DIR__ . '/connection/connect.php';

header("cache-control: no-store, no-cache, must-revalidate, max-age=0");
header("cache-control: post-check=0, pre-check=0", false);
header("pragma: no-cache");

// Si ya hay sesión activa, redirigir
if (isset($_SESSION['tip_user'])) {
    $rutas = [
        'admin'    => 'admin/index_admin.php',
        'cliente'  => 'cliente/index.php',
        'vendedor' => 'vendedor/index.php'
    ];
    header('Location: ' . ($rutas[$_SESSION['tip_user']] ?? 'login.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Formato de email inválido';
    } else {
        try {
            $db  = new Database();
            $pdo = $db->conectar();

            // JOIN correcto: u.id_tip_user = t.id_tip_user
            $sql  = "SELECT u.documento, u.nombres, u.email, u.password,
                            t.tip_user, t.id_tip_user
                     FROM user u
                     INNER JOIN tip_user t ON u.id_tip_user = t.id_tip_user
                     WHERE u.email = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['documento']    = $user['documento'];
                $_SESSION['nombres']      = $user['nombres'];
                $_SESSION['email']        = $user['email'];
                $_SESSION['id_tipo_user'] = $user['id_tip_user'];
                $_SESSION['tip_user']     = strtolower(trim($user['tip_user']));
                $_SESSION['login_time']   = time();

                $rutas = [
                    'admin'    => 'admin/index_admin.php',
                    'cliente'  => 'cliente/index.php',
                    'vendedor' => 'vendedor/index.php'
                ];
                header('Location: ' . ($rutas[$_SESSION['tip_user']] ?? 'login.php'));
                exit;
            } else {
                $error = 'Email o contraseña incorrectos';
            }

        } catch (Exception $e) {
            $error = 'Error SQL: ' . $e->getMessage(); // visible para depuración
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="../style/loginstyle.css">

</head>
<body>

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
        <label>PIN </label>
      </div>

       <div class="input-box">
        <span class="icon">
          <ion-icon name="lock-closed"></ion-icon>
        </span>
        <input type="password" required>
        <label>Contraseña</label>
      </div>

      <button type="submit" class="btn-login">Login</button>
      
      <form action="pagina-destino.html">
        <button type="submit">Ir a la página</button>
      </form> 
      

    </form>
  </div>

</div>

</body>
</html>