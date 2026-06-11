<?php
// ============================================================
// includes/auth_admin.php
// Responsabilidad: Funciones de apoyo para seguridad.
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
        SELECT u.documento, u.nombre_completo, u.password, u.pin, u.id_tip_user, t.nom_tip
        FROM usuario u
        INNER JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ? AND u.id_tip_user = 1
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