<?php
// ============================================================
// includes/funciones.php
// Lógica de negocio: empleados, asistencias, reportes, áreas
// Requiere: config/db.php incluido antes de este archivo
// ============================================================

// ============================================================
// 1. ÁREAS
// ============================================================

/**
 * Retorna todas las áreas para llenar selects en formularios.
 */
function obtenerAreas($pdo) {
    $stmt = $pdo->query("SELECT id, nombre FROM areas ORDER BY nombre ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// 2. EMPLEADOS
// ============================================================

/**
 * Retorna todos los empleados con su área y tipo de usuario.
 */
function obtenerEmpleados($pdo) {
    $stmt = $pdo->query("
        SELECT u.documento, u.nombre_completo, u.fecha_creacion,
               a.nombre AS area, t.nombre AS tipo_usuario
        FROM usuarios u
        LEFT JOIN areas a ON u.area_id = a.id
        LEFT JOIN tipo_usuario t ON u.tipo_usuario_id = t.id
        ORDER BY u.nombre_completo ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retorna un empleado por su documento.
 * Retorna false si no existe.
 */
function obtenerEmpleadoPorId($pdo, $documento) {
    $stmt = $pdo->prepare("
        SELECT u.*, a.nombre AS area, t.nombre AS tipo_usuario
        FROM usuarios u
        LEFT JOIN areas a ON u.area_id = a.id
        LEFT JOIN tipo_usuario t ON u.tipo_usuario_id = t.id
        WHERE u.documento = ?
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Crea un nuevo empleado.
 * El PIN y la contraseña se almacenan con password_hash().
 * Retorna true si se insertó correctamente, false si no.
 */
function crearEmpleado($pdo, $datos) {
    // Verificar que el documento no exista
    $check = $pdo->prepare("SELECT documento FROM usuarios WHERE documento = ? LIMIT 1");
    $check->execute([$datos['documento']]);
    if ($check->fetch()) {
        return false; // Documento ya registrado
    }

    $stmt = $pdo->prepare("
        INSERT INTO usuarios (documento, pin, password, nombre_completo, tipo_usuario_id, area_id, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    return $stmt->execute([
        $datos['documento'],
        password_hash($datos['pin'], PASSWORD_DEFAULT),
        password_hash($datos['password'], PASSWORD_DEFAULT),
        $datos['nombre_completo'],
        $datos['tipo_usuario_id'],
        $datos['area_id']
    ]);
}

/**
 * Actualiza los datos de un empleado (sin modificar PIN ni contraseña).
 * Retorna true si se actualizó correctamente, false si no.
 */
function actualizarEmpleado($pdo, $documento, $datos) {
    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET nombre_completo = ?, area_id = ?, tipo_usuario_id = ?
        WHERE documento = ?
    ");

    return $stmt->execute([
        $datos['nombre_completo'],
        $datos['area_id'],
        $datos['tipo_usuario_id'],
        $documento
    ]);
}

/**
 * Elimina un empleado por su documento.
 * Las asistencias se manejan según ON DELETE de la BD.
 * Retorna true si se eliminó correctamente, false si no.
 */
function eliminarEmpleado($pdo, $documento) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE documento = ?");
    return $stmt->execute([$documento]);
}

// ============================================================
// 3. ASISTENCIAS (flujo desde index.php)
// ============================================================

/**
 * Busca un empleado activo por documento y verifica su PIN.
 * Retorna el array del empleado si es válido, false si no.
 */
function registrarAsistencia($pdo, $documento, $pin) {
    // Buscar empleado
    $stmt = $pdo->prepare("
        SELECT u.*
        FROM usuarios u
        INNER JOIN tipo_usuario t ON u.tipo_usuario_id = t.id
        WHERE u.documento = ? AND t.nombre = 'empleado'
        LIMIT 1
    ");
    $stmt->execute([$documento]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        return ['ok' => false, 'mensaje' => 'Documento o PIN incorrecto.'];
    }

    if (!password_verify($pin, $empleado['pin'])) {
        return ['ok' => false, 'mensaje' => 'Documento o PIN incorrecto.'];
    }

    // Buscar si tiene registro abierto hoy (sin salida)
    $hoy = date('Y-m-d');
    $check = $pdo->prepare("
        SELECT id FROM asistencias
        WHERE usuario_documento = ?
          AND DATE(fecha_hora_entrada) = ?
          AND fecha_hora_salida IS NULL
        LIMIT 1
    ");
    $check->execute([$empleado['documento'], $hoy]);
    $registroAbierto = $check->fetch(PDO::FETCH_ASSOC);

    if (!$registroAbierto) {
        // Registrar entrada
        $insert = $pdo->prepare("
            INSERT INTO asistencias (usuario_documento, fecha_hora_entrada)
            VALUES (?, NOW())
        ");
        $insert->execute([$empleado['documento']]);
        return ['ok' => true, 'mensaje' => 'Entrada registrada correctamente.'];
    } else {
        // Registrar salida y calcular horas trabajadas
        $update = $pdo->prepare("
            UPDATE asistencias
            SET fecha_hora_salida = NOW(),
                horas_trabajadas = TIMESTAMPDIFF(MINUTE, fecha_hora_entrada, NOW()) / 60
            WHERE id = ?
        ");
        $update->execute([$registroAbierto['id']]);
        return ['ok' => true, 'mensaje' => 'Salida registrada correctamente.'];
    }
}

// ============================================================
// 4. REPORTES
// ============================================================

/**
 * Retorna todas las asistencias de una fecha específica (YYYY-MM-DD).
 * Incluye nombre del empleado y área.
 */
function obtenerAsistenciasPorFecha($pdo, $fecha) {
    $stmt = $pdo->prepare("
        SELECT u.nombre_completo, u.documento, a.nombre AS area,
               as2.fecha_hora_entrada, as2.fecha_hora_salida,
               ROUND(as2.horas_trabajadas, 2) AS horas_trabajadas
        FROM asistencias as2
        INNER JOIN usuarios u ON as2.usuario_documento = u.documento
        LEFT JOIN areas a ON u.area_id = a.id
        WHERE DATE(as2.fecha_hora_entrada) = ?
        ORDER BY as2.fecha_hora_entrada ASC
    ");
    $stmt->execute([$fecha]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}