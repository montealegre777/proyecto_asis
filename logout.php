<?php
// ============================================================
// logout.php — Cierre de sesión del administrador
// Responsabilidad: Destruir completamente la sesión activa
// y redirigir al usuario a la página principal.
// ============================================================

session_start();

// Paso 1: Vaciar todas las variables de sesión guardadas en memoria
$_SESSION = array();

// Paso 2: Eliminar la cookie de sesión del navegador del usuario
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // Fecha en el pasado: fuerza al navegador a borrar la cookie
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Paso 3: Destruir el archivo de sesión en el servidor
session_destroy();

// Evitar que el navegador guarde en caché el panel (el botón "atrás" no debe funcionar)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirigir a la página principal pública
header("Location: index.php");
exit;
?>