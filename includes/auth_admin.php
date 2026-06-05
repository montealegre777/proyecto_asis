<?php
// ============================================================
// includes/auth_admin.php
// Seguridad, validaciones y control de sesión administrador
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// 1. SANITIZACIÓN
// ============================================================

/**
 * Limpia un dato de entrada del usuario.
 * Usar en TODO lo que venga de $_POST o $_GET antes de procesarlo.
 */
function limpiar($dato) {
    return htmlspecialchars(stripslashes(trim($dato)));
}

// ============================================================
// 2. VALIDACIONES
// ============================================================

/**
 * Valida que el PIN sea exactamente 4 dígitos numéricos.
 */
function validarPin($pin) {
    return preg_match('/^\d{4}$/', $pin);
}

/**
 * Valida que la contraseña tenga exactamente 10 caracteres alfanuméricos.
 */
function validarPassword($pass) {
    return preg_match('/^[a-zA-Z0-9]{10}$/', $pass);
}

// ============================================================
// 3. AUTENTICACIÓN ADMIN
// ============================================================

/**
 * Busca al administrador por documento, verifica PIN y contraseña.
 * Retorna el array del admin si las credenciales son correctas, false si no.
 */
function verificarCredencialesAdmin($pdo, $documento, $pin, $password) {
    $stmt = $pdo->prepare("
        SELECT u.* 
        FROM usuarios u
        INNER JOIN tipo_usuario t ON u.tipo_usuario_id = t.id
        WHERE u.documento = ? AND t.nombre = 'administrador'
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        return false;
    }

    // Verificar PIN (almacenado con password_hash)
    if (!password_verify($pin, $admin['pin'])) {
        return false;
    }

    // Verificar contraseña
    if (!password_verify($password, $admin['password'])) {
        return false;
    }

    return $admin;
}

// ============================================================
// 4. CONTROL DE ACCESO
// ============================================================

/**
 * Protege cualquier página del panel admin.
 * Colocar al inicio de cada archivo dentro de admin/.
 * Si no hay sesión activa, redirige al login.
 */
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// ============================================================
// 5. CIERRE DE SESIÓN
// ============================================================

/**
 * Destruye la sesión del administrador y redirige al login.
 */
function logout() {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}