<?php
// includes/funciones.php

// ── ÁREAS ──
function obtenerAreas($pdo) {
    $stmt = $pdo->query("SELECT id_area, nom_area FROM area ORDER BY nom_area ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── EMPLEADOS ──
function obtenerEmpleados($pdo) {
    $stmt = $pdo->query("
        SELECT u.documento, u.nombre_completo, u.fecha_creacion,
               a.nom_area AS area, t.nom_tip AS tipo_usuario
        FROM usuario u
        LEFT JOIN area a ON u.id_area = a.id_area
        LEFT JOIN type_user t ON u.id_tip_user = t.id_tip_user
        ORDER BY u.nombre_completo ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerEmpleadoPorId($pdo, $documento) {
    $stmt = $pdo->prepare("
        SELECT u.*, a.nom_area AS area, t.nom_tip AS tipo_usuario
        FROM usuario u
        LEFT JOIN area a ON u.id_area = a.id_area
        LEFT JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ?
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearEmpleado($pdo, $datos) {
    $check = $pdo->prepare("SELECT documento FROM usuario WHERE documento = ? LIMIT 1");
    $check->execute([$datos['documento']]);
    if ($check->fetch()) return false;

    $stmt = $pdo->prepare("
        INSERT INTO usuario (documento, pin, password, nombre_completo, id_tip_user, id_area, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, CURDATE())
    ");
    return $stmt->execute([
        $datos['documento'],
        $datos['pin'],
        password_hash($datos['password'], PASSWORD_DEFAULT),
        $datos['nombre_completo'],
        $datos['id_tip_user'],
        $datos['id_area']
    ]);
}

function actualizarEmpleado($pdo, $documento, $datos) {
    $stmt = $pdo->prepare("
        UPDATE usuario SET nombre_completo = ?, id_area = ?, id_tip_user = ?
        WHERE documento = ?
    ");
    return $stmt->execute([
        $datos['nombre_completo'],
        $datos['id_area'],
        $datos['id_tip_user'],
        $documento
    ]);
}

function eliminarEmpleado($pdo, $documento) {
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE documento = ?");
    return $stmt->execute([$documento]);
}

// ── ASISTENCIAS ──
function registrarAsistencia($pdo, $documento, $pin) {
    $stmt = $pdo->prepare("
        SELECT u.* FROM usuario u
        INNER JOIN type_user t ON u.id_tip_user = t.id_tip_user
        WHERE u.documento = ? AND t.nom_tip = 'empleado'
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado || (int)$pin !== (int)$empleado['pin']) {
        return ['ok' => false, 'mensaje' => 'Documento o PIN incorrecto.'];
    }

    $hoy = date('Y-m-d');
    $check = $pdo->prepare("
        SELECT id_asistencia FROM asistencias
        WHERE id_empleado = ? AND DATE(fecha_entrada) = ? AND fecha_salida IS NULL
        LIMIT 1
    ");
    $check->execute([$empleado['documento'], $hoy]);
    $abierto = $check->fetch(PDO::FETCH_ASSOC);

    if (!$abierto) {
        $pdo->prepare("INSERT INTO asistencias (id_empleado, fecha_entrada) VALUES (?, NOW())")
            ->execute([$empleado['documento']]);
        return ['ok' => true, 'mensaje' => 'Entrada registrada correctamente.'];
    } else {
        $pdo->prepare("
            UPDATE asistencias
            SET fecha_salida = NOW(),
                horas_trabajadas = TIMESTAMPDIFF(MINUTE, fecha_entrada, NOW()) / 60
            WHERE id_asistencia = ?
        ")->execute([$abierto['id_asistencia']]);
        return ['ok' => true, 'mensaje' => 'Salida registrada correctamente.'];
    }
}

// ── REPORTES ──
function obtenerAsistenciasPorFecha($pdo, $fecha) {
    $stmt = $pdo->prepare("
        SELECT u.nombre_completo, u.documento, a.nom_area AS area,
               ast.fecha_entrada, ast.fecha_salida,
               ROUND(ast.horas_trabajadas, 2) AS horas_trabajadas
        FROM asistencias ast
        INNER JOIN usuario u ON ast.id_empleado = u.documento
        LEFT JOIN area a ON u.id_area = a.id_area
        WHERE DATE(ast.fecha_entrada) = ?
        ORDER BY ast.fecha_entrada ASC
    ");
    $stmt->execute([$fecha]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}