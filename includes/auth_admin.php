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

function limpiar($dato) {
    return htmlspecialchars(stripslashes(trim($dato)));
}

// ============================================================
// 2. VALIDACIONES
// ============================================================

function validarPin($pin) {
    return preg_match('/^\d{4}$/', $pin);
}

function validarPassword($pass) {
    return preg_match('/^[a-zA-Z0-9]{10}$/', $pass);
}

// ============================================================
// 3. AUTENTICACIÓN ADMIN
// ============================================================

function verificarCredencialesAdmin($pdo, $documento, $pin, $password) {
    $stmt = $pdo->prepare("
        SELECT u.*
        FROM usuario u
        INNER JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ? AND t.nom_tip = 'administrador'
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        return false;
    }

    // PIN guardado como INT: comparación directa
    if ((int)$pin !== (int)$admin['pin']) {
        return false;
    }

    // Contraseña cifrada con password_hash
    if (!password_verify($password, $admin['password'])) {
        return false;
    }

    return $admin;
}

// ============================================================
// 4. CONTROL DE ACCESO
// ============================================================

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// ============================================================
// 5. CIERRE DE SESIÓN
// ============================================================

function logout() {
    $_SESSION = [];
    session_regenerate_id(true);
    session_destroy();
    header('Location: login.php');
    exit;
}